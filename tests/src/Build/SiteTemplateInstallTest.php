<?php

declare(strict_types=1);

namespace Drupal\Tests\webship\Build;

use Composer\InstalledVersions;
use Composer\Json\JsonFile;
use Composer\Util\Platform;
use Drupal\BuildTests\QuickStart\QuickStartTestBase;
use Drupal\Component\Utility\Html;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * Contains end-to-end tests of installing Webship with site templates.
 */
#[Group('webship')]
#[RequiresPhpExtension('pdo_sqlite')]
final class SiteTemplateInstallTest extends QuickStartTestBase {

  /**
   * The protected repository's server process.
   */
  private ?Process $server = NULL;

  /**
   * Tests selecting a site template provided by a protected repository.
   */
  public function testInstallPackageFromProtectedRepository(): void {
    // This test builds a project from the Webship project template, which is
    // not part of this repository.
    $origin = (getenv('CI_PROJECT_DIR') ?: dirname(__DIR__, 4)) . '/project_template';
    if (!is_dir($origin)) {
      $this->markTestSkipped('This test needs the Webship project template in ' . $origin . '.');
    }
    $this->requiresComposer();

    // Give Composer a place to sync and cache data.
    Platform::putEnv('COMPOSER_HOME', $this->getWorkingPath('.composer'));
    // If the environment is set up to mirror path repositories, we need to
    // undo that for the purposes of this test.
    Platform::clearEnv('COMPOSER_MIRROR_PATH_REPOS');

    $dir = $this->getWorkspaceDirectory();
    $file_system = new Filesystem();
    // Copy the project template's `composer.json` into the workspace. The
    // location of the project template varies depending on whether we're in a
    // CI job or not.
    $finder = Finder::create()
      ->files()
      ->in($origin)
      ->path(['assets/', 'composer.json']);
    $file_system->mirror($origin, $dir, $finder);

    // Disable Packagist and the Drupal repositories, and disable all network
    // operations, so we don't inadvertently test the internet.
    $this->executeCommand('composer repository disable packagist.org');
    $this->assertCommandSuccessful();
    $this->executeCommand('composer repository remove drupal');
    $this->assertCommandSuccessful();
    $this->executeCommand('composer config scripts.pre-update-cmd "@putenv COMPOSER_DISABLE_NETWORK=1"');
    $this->assertCommandSuccessful();
    $this->executeCommand('composer config scripts.pre-install-cmd "@putenv COMPOSER_DISABLE_NETWORK=1"');
    $this->assertCommandSuccessful();

    // Allow insecure connections to our fake protected repository.
    $this->executeCommand('composer config secure-http false');
    $this->assertCommandSuccessful();

    // Expose all installed dependencies in a local Composer repository, derived
    // from the lock file we're operating with.
    $root_package = InstalledVersions::getRootPackage();
    $lock = $root_package['install_path'] . '/composer.lock';
    $lock = new JsonFile($lock);
    $this->assertTrue($lock->exists());
    $lock = $lock->read();

    $installed_packages = array_merge($lock['packages'], $lock['packages-dev']);
    $vendor_packages = [];
    foreach ($installed_packages as $package) {
      ['name' => $name, 'version' => $version] = $package;

      $path = InstalledVersions::getInstallPath($name);
      if ($path) {
        $path = realpath($path);
        assert($path && is_dir($path));
        $package['dist'] = ['type' => 'path', 'url' => $path];
      }
      $vendor_packages['packages'][$name][$version] = $package;
    }
    (new JsonFile("$dir/vendor.json"))->write($vendor_packages);
    $this->executeCommand('composer repository add vendor composer vendor.json');
    $this->assertCommandSuccessful();

    // The blank site template requires a theme that gets generated on-the-fly,
    // which will not work in this test setup, and is irrelevant anyway.
    $this->executeCommand('composer remove --no-update drupal/webship');
    $this->assertCommandSuccessful();

    // Without this, the test fails when running against a tag of Webship.
    if (getenv('CI') && getenv('CI_COMMIT_TAG')) {
      $this->executeCommand('composer config minimum-stability dev');
      $this->assertCommandSuccessful();
    }

    // And now, install dependencies. Almost all can be symlinked...
    $this->executeCommand('composer install');
    $this->assertCommandSuccessful();

    // Serve our fake, protected Composer repository.
    $repository = 'localhost:' . $this->findAvailablePort();
    $this->server = new Process([
      PHP_BINARY,
      '-S',
      $repository,
      dirname(__DIR__, 2) . '/fixtures/protected-server.php',
    ]);
    $this->server->start();

    // ...except core, which doesn't work properly if symlinked. Leave tests out
    // of it, since we don't need them for our purposes.
    $origin = InstalledVersions::getInstallPath('drupal/core');
    $destination = "$dir/web/core";
    unlink($destination);
    $finder = Finder::create()->in($origin)->notPath('tests');
    $file_system->mirror($origin, $destination, $finder);

    // Set up an alternate list of site templates for the installer to read.
    $list = [
      // This package is provided by the fake protected repository. It's a tiny
      // recipe that wraps around webship.
      'test_auth' => [
        'name' => 'Testing Authorization',
        'description' => 'Simulates a paid package.',
        'screenshot' => '../recipes/webship/screenshot.webp',
        'package' => [
          'name' => 'drupal/test_auth',
          'repository' => "http://$repository",
          'authorization' => 'bearer',
        ],
        'purchase' => [
          'price' => 99,
          'url' => 'https://www.example.com/buy',
        ],
      ],
    ];
    file_put_contents(
      $this->getWorkingPath('web/sites/default') . '/site-templates.php',
      '<?php return ' . var_export($list, TRUE) . ';',
    );

    $mink = $this->visit(working_dir: 'web');
    // Go through the first couple steps of the installer, to the site template
    // selection form.
    $page = $mink->getSession()->getPage();
    $page->selectFieldOption('driver', 'SQLite');
    $page->pressButton('Save and continue');
    $assert_session = $mink->assertSession();
    $assert_session->pageTextContains('Give your site a name');
    $page->pressButton('Next');
    $assert_session->pageTextContains('Choose a site template');
    $assert_session->elementAttributeContains('named', ['link', 'Buy for $99'], 'href', 'https://www.example.com/buy');

    $auth_file = new JsonFile("$dir/auth.json");
    // Choosing the premium site template and going on without a license key
    // asks for it, and shows its license key field.
    $page->selectFieldOption('add_ons', 'test_auth');
    $page->pressButton('Next');
    $assert_session->pageTextContains('Enter the license key for Testing Authorization.');
    // An invalid access key should produce an error, and auth.json should exist
    // but not have the invalid key. The repository should have been added (the
    // name is the xxh3 hash of the host and port).
    $page->fillField('access_key[test_auth]', 'not a valid access key');
    $page->pressButton('Next');
    $assert_session->pageTextContains('The access key you entered did not grant access to the package. Contact the seller for support.');
    $this->assertTrue($auth_file->exists());
    ['bearer' => $credentials] = $auth_file->read();
    $this->assertEmpty($credentials);
    // This will fail if the repository is undefined.
    $this->executeCommand('composer repository get-url ' . hash('xxh3', $repository));
    $this->assertCommandSuccessful();

    // With a valid access key, we should be able to proceed.
    $valid_key = 'A8C8F935-107E-F883-55BB-CE43E398CF53';
    $page->fillField('access_key[test_auth]', $valid_key);
    $page->pressButton('Next');
    $assert_session->pageTextContains('Create your account');

    // The license key was accepted, which means that `auth.json` should have
    // been updated.
    ['bearer' => $credentials] = $auth_file->read();
    $this->assertSame($valid_key, $credentials[$repository]);

    // Finish the installation. When done, we should have been sent to the
    // finish URL of the installer, the dashboards of the Web Dashboard module.
    $assert_session->pageTextContains('Create your account');
    $page->fillField('Email', 'test@example.com');
    $page->fillField('Password', 'test');
    $page->pressButton('Finish');

    $refreshed = 0;
    do {
      if ($refreshed === 100) {
        $this->fail('The installation is taking too long and is probably stuck in an infinite loop.');
      }
      $refresh = $page->find('css', 'meta[http-equiv="refresh"], meta[http-equiv="Refresh"]')
        ?->getAttribute('content');

      if ($refresh && preg_match('/\d+;\s*URL=\'?(?<url>[^\']*)/i', $refresh, $match)) {
        $url = Html::decodeEntities($match['url']);
        $this->visit($url, 'web');
        $refreshed++;
      }
    } while (isset($refresh));

    // Don't use `getWorkingPath()` here because it will create the directory
    // if it doesn't exist.
    $this->assertFileExists("$dir/recipes/test_auth");
    $assert_session->addressEquals('/admin/webdashboard');
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    if ($this->destroyBuild) {
      // Don't bother destroying the build on CI, because it doesn't seem to
      // work consistently or properly, and it's not necessary anyway -- the
      // CI environment is ephemeral.
      $this->destroyBuild = empty(getenv('CI'));
    }
    $this->server?->stop();
    parent::tearDown();
  }

}
