<?php

declare(strict_types=1);

namespace Drupal\Tests\webship\Functional;

use Drupal\FunctionalTests\Installer\InstallerTestBase;
use Drupal\Tests\webship\Traits\SiteTemplateFixtureTrait;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tests the interactive Webship installer.
 */
#[Group('webship')]
#[IgnoreDeprecations]
#[RunTestsInSeparateProcesses]
class InteractiveInstallTest extends InstallerTestBase {

  use SiteTemplateFixtureTrait;

  /**
   * {@inheritdoc}
   */
  protected $profile = NULL;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUpSettings(): void {
    // Set up the database as normal. It's the first thing our installer does.
    parent::setUpSettings();

    $assert_session = $this->assertSession();
    // We should be asked for the site name, with a default value in place for
    // the truly lazy.
    $assert_session->pageTextContains('Give your site a name');
    $site_name_field = $assert_session->fieldExists('Site name');
    $this->assertTrue($site_name_field->hasAttribute('required'));
    $this->assertNotEmpty($site_name_field->getValue());
    // We have to use submitForm() to ensure that batch operations, redirects,
    // and so forth in the remaining install tasks get done.
    $this->submitForm(['Site name' => 'Installer Test'], 'Next');

    // The next step asks you to choose a site template.
    $assert_session->pageTextContains('Choose a site template');

    // The site template of the site-templates.php file should be available,
    // and selected by default.
    $template = static::$fixtureSiteTemplate;
    $assert_session->fieldValueEquals('add_ons', $template);
    $assert_session->elementAttributeContains('named', ['field', 'add_ons'], 'value', $template);

    $choice = $assert_session->elementExists('css', 'input[value="' . $template . '"]')
      ->getParent();
    $assert_session->elementExists('css', 'h3:contains("Webship Test Site")', $choice);
    $assert_session->elementExists('css', '.description:contains("A site template for the tests")', $choice);
    $assert_session->elementExists('css', 'img[src^="data:image/webp;base64,"]', $choice);
    $this->submitForm(['add_ons' => $template], 'Next');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUpProfile(): void {
    // Nothing to do here; Webship marks itself as a distribution so that the
    // installer will automatically select it.
  }

  /**
   * {@inheritdoc}
   */
  protected function visitInstaller(): void {
    // List the fixture site template instead of the curated site templates.
    $this->writeSiteTemplatesFile(DRUPAL_ROOT . '/' . $this->siteDirectory);
    parent::visitInstaller();
    // The task list should be hidden.
    $this->assertSession()->elementNotExists('css', '.task-list');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUpLanguage() {
    // The Webship installer suppresses the language selection step, so
    // there's nothing to do here.
  }

  /**
   * {@inheritdoc}
   */
  protected function setUpSite(): void {
    $page = $this->getSession()->getPage();
    $page->fillField('Email', 'hello@good.bye');
    $page->fillField('Password', "kitty");
    $page->pressButton('Finish');

    $this->checkForMetaRefresh();
    $this->isInstalled = TRUE;
  }

  /**
   * Tests basic expectations of a successful Webship install.
   */
  public function testPostInstallState(): void {
    // The administrator role should exist.
    $this->assertInstanceOf(Role::class, Role::load('administrator'));

    // We should have been sent to the dashboards of the Web Dashboard module.
    $assert_session = $this->assertSession();
    $assert_session->addressEquals('/admin/webdashboard');
    $assert_session->statusMessageNotExists();

    // The site name and e-mail should have been set.
    $this->drupalGet('/admin/config/system/site-information');
    $assert_session->fieldValueEquals('Site name', 'Installer Test');
    $assert_session->fieldValueEquals('Email address', 'hello@good.bye');

    // Update Status should be installed, and user 1 should be getting its
    // notifications.
    $this->drupalGet('/admin/reports/updates/settings');
    $account = User::load(1);
    $assert_session->fieldValueEquals('Email addresses to notify when updates are available', $account->getEmail());
    $this->assertTrue($account->hasRole('administrator'));

    // The installer should have uninstalled itself. We can confirm this by
    // ensuring that its theme is not visible, because the theme is embedded in
    // the profile and therefore should not be discoverable if the profile is
    // not itself installed.
    $this->drupalGet('/admin/appearance');
    $assert_session->responseNotContains('webship_installer_theme');
    // The themes of the site template should have been set, proving that its
    // recipe was applied during installation.
    // The theme list shows the version of core between the name and the notes.
    $assert_session->responseMatches('/Olivero[^(]*\(default theme\)/');
    $assert_session->responseMatches('/Claro[^(]*\(administration theme\)/');

    // Log out so we can test that user 1's credentials were properly saved.
    $this->drupalLogout();

    // It should be possible to log in with the username, which is set to
    // `webmaster` by the installer, and the password we chose in the
    // installer. Logging in with the email address needs a module of the site
    // template, which the fixture site template does not install.
    // @see ::setUpSite()
    $page = $this->getSession()->getPage();
    $page->fillField('name', 'webmaster');
    $page->fillField('pass', 'kitty');
    $page->pressButton('Log in');
    $assert_session->addressEquals('/user/1');
    $this->drupalLogout();
  }

  /**
   * {@inheritdoc}
   */
  protected function installDefaultThemeFromClassProperty(ContainerInterface $container): void {
    // Nothing to do here; the default theme is set during the install process.
  }

}
