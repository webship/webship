<?php

declare(strict_types=1);

namespace Drupal\Tests\webship\Functional;

use Drupal\Core\Test\TestSetupTrait;
use Drupal\Tests\webship\Traits\SiteTemplateFixtureTrait;
use Drush\TestTraits\DrushTestTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Tests installing Webship from the command line.
 */
#[Group('webship')]
#[RequiresPhpExtension('pdo_sqlite')]
#[RunTestsInSeparateProcesses]
class CommandLineInstallTest extends TestCase {

  use DrushTestTrait;
  use SiteTemplateFixtureTrait;
  use TestSetupTrait;

  /**
   * The full path to the test site directory.
   *
   * @var string
   */
  private string $sitePath;

  /**
   * The Drupal root.
   *
   * @var string
   *
   * @see https://www.drupal.org/node/3574112
   */
  protected $root;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->root = dirname((new \ReflectionClass('Drupal'))->getFileName(), 3);
    $this->prepareDatabasePrefix();
    $this->sitePath = $this->root . '/' . $this->siteDirectory;

    mkdir($this->sitePath, recursive: TRUE);
    // Install the fixture site template instead of the curated ones.
    $this->writeSiteTemplatesFile($this->sitePath);

    // This is needed for Drush to work properly.
    if (!defined('DRUPAL_TEST_IN_CHILD_SITE')) {
      define('DRUPAL_TEST_IN_CHILD_SITE', FALSE);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    $file_system = new Filesystem();
    $file_system->chmod($this->sitePath, 0755);
    $file_system->remove($this->sitePath);

    parent::tearDown();
  }

  /**
   * Asserts the state of the site after the installation.
   */
  private function assertPostInstallState(): void {
    // The administrator role should exist.
    $this->drush('config:get', ['user.role.administrator'], cd: $this->root);

    // Confirm that there's no install profile.
    $this->drush('core:status', options: ['field' => 'install-profile'], cd: $this->root);
    $this->assertEmpty($this->getOutput());

    // Confirm that non-core extensions are installed.
    $options = [
      'format' => 'json',
      'no-core' => TRUE,
      'status' => 'enabled',
    ];
    $this->drush('pm:list', options: $options, cd: $this->root);
    $this->assertNotEmpty($this->getOutputFromJSON());

    // Confirm that Claro is the admin theme and Olivero is the default theme,
    // proving that the fixture site template was used.
    $this->drush('config:get', ['system.theme'], ['format' => 'json'], cd: $this->root);
    $this->assertSame('claro', $this->getOutputFromJSON('admin'));
    $this->assertSame('olivero', $this->getOutputFromJSON('default'));
  }

  /**
   * Tests installing with the site:install command of Drush.
   */
  public function testDrushSiteInstall(): void {
    $options = [
      'yes' => TRUE,
      'sites-subdir' => substr($this->siteDirectory, 6),
      'db-url' => "sqlite://$this->siteDirectory/files/.sqlite",
    ];
    $this->drush('site:install', options: $options, cd: $this->root);

    $this->assertPostInstallState();
  }

  /**
   * Tests installing with the install command of core.
   */
  public function testCoreInstallCommand(): void {
    $command = [
      PHP_BINARY,
      '../vendor/bin/dr',
      'install',
    ];
    $process = new Process($command, $this->root, [
      'DRUPAL_DEV_SITE_PATH' => $this->siteDirectory,
    ]);
    // Process uses a default timeout of 60 seconds. $this->drush() disables
    // it entirely, so do that here too.
    $process->setTimeout(0)->mustRun();
    $this->assertStringContainsString('Congratulations, you installed Webship!', $process->getErrorOutput());

    // The core install command write-protects the site directory, which
    // interferes with $this->drush().
    chmod($this->sitePath, 0755);

    $this->assertPostInstallState();
  }

}
