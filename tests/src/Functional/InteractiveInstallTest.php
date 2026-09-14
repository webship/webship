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
    // and no site template should be selected by default.
    $template = static::$fixtureSiteTemplate;
    $assert_session->elementExists('css', 'input[name="add_ons"][value="' . $template . '"]');
    $assert_session->elementNotExists('css', 'input[name="add_ons"][checked]');

    $choice = $assert_session->elementExists('css', 'input[value="' . $template . '"]')
      ->getParent();
    $assert_session->elementExists('css', 'h3:contains("Webship Test Site")', $choice);
    $assert_session->elementExists('css', '.description:contains("A site template for the tests")', $choice);
    $assert_session->elementExists('css', 'img[src^="data:image/webp;base64,"]', $choice);

    // The installer uses server-side forms and the HTMX of core, with no custom
    // JavaScript of its own.
    $assert_session->responseNotContains('webship_installer_theme/js/');
    $assert_session->elementExists('css', 'script[src*="core/misc/htmx/htmx-behaviors.js"]');
    // The language switcher is a GET form, which keeps the installer query.
    $language_form = $assert_session->elementExists('css', 'form.webship-installer__language-form[method="get"][data-hx-get][data-hx-trigger="change"]');
    $assert_session->elementExists('css', 'select[name="langcode"] option[value="en"][selected]', $language_form);
    $assert_session->elementExists('css', 'input[type="hidden"][name="profile"][value="webship"]', $language_form);
    $assert_session->buttonExists('Change language', $language_form);
    // Choosing a site template updates the license key panel through HTMX.
    $assert_session->elementExists('css', '#site-template-license');
    // The messages of the page can be replaced out of band.
    $assert_session->elementExists('css', '#webship-installer-messages[data-hx-swap-oob="true"]');
    $radio = $assert_session->elementExists('css', 'input[name="add_ons"][value="webship_test_premium"]');
    $this->assertStringContainsString('install.php', (string) $radio->getAttribute('data-hx-post'));
    $this->assertSame('change', $radio->getAttribute('data-hx-trigger'));
    $this->assertSame('#site-template-license', $radio->getAttribute('data-hx-select'));
    $this->assertSame('#site-template-license', $radio->getAttribute('data-hx-target'));
    $this->assertStringContainsString('outerHTML', (string) $radio->getAttribute('data-hx-swap'));
    $this->assertStringContainsString('_triggering_element_name', (string) $radio->getAttribute('data-hx-vals'));
    // No license key field is shown before a premium site template is chosen.
    $assert_session->fieldNotExists('access_key[webship_test_premium]');

    // Going on without a choice should ask for a site template, with no PHP
    // warnings. The post-install test also checks that no messages are left
    // over for the dashboard.
    $this->submitForm([], 'Next');
    $assert_session->statusCodeEquals(200);
    $assert_session->pageTextContains('Choose a site template');
    $assert_session->statusMessageContains('Choose a site template.', 'error');
    $assert_session->pageTextNotContains('Undefined array key');
    $assert_session->pageTextNotContains('Trying to access array offset');
    $assert_session->elementNotExists('css', 'input[name="add_ons"][checked]');

    // Choosing the premium site template, as HTMX does, shows its license key
    // field in the panel, and stays on this step. The errors already shown are
    // removed, so they do not show again on the next pages.
    $this->sendHtmxChoice('webship_test_premium');
    $assert_session->statusCodeEquals(200);
    $assert_session->pageTextContains('Choose a site template');
    $assert_session->statusMessageNotExists();
    $assert_session->pageTextNotContains('Undefined array key');
    // The response carries the messages out of band, now with no messages, so
    // HTMX removes the error of the previous step from the page.
    $messages = $assert_session->elementExists('css', '#webship-installer-messages[data-hx-swap-oob="true"]');
    $this->assertNull($messages->find('css', '[data-drupal-messages] .messages, [role="alert"]'));
    $this->assertStringNotContainsString('Choose a site template.', $messages->getText());
    $panel = $assert_session->elementExists('css', '#site-template-license.site-template-license--required');
    $assert_session->fieldExists('access_key[webship_test_premium]', $panel);
    $assert_session->elementTextContains('css', '#site-template-license', 'License key for Webship Test Premium');
    // The form build ID of the page is updated out of band.
    $assert_session->elementExists('css', 'input[name="form_build_id"][data-hx-swap-oob]');

    // Choosing the free site template empties the panel.
    $this->sendHtmxChoice($template);
    $assert_session->elementExists('css', '#site-template-license');
    $assert_session->elementNotExists('css', '#site-template-license.site-template-license--required');
    $assert_session->fieldNotExists('access_key[webship_test_premium]');

    // Going on with the premium site template and no license key asks for it.
    $this->submitForm(['add_ons' => 'webship_test_premium'], 'Next');
    $assert_session->pageTextContains('Choose a site template');
    $assert_session->statusMessageContains('Enter the license key for Webship Test Premium.', 'error');
    $assert_session->fieldExists('access_key[webship_test_premium]');

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
    // List the fixture site templates instead of the curated site templates.
    $this->writeSiteTemplatesFile(DRUPAL_ROOT . '/' . $this->siteDirectory, premium: TRUE);
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
    // The errors of the site template step, already shown, should not show
    // again on the account step.
    $this->assertSession()->pageTextContains('Create your account');
    $this->assertSession()->statusMessageNotExists();

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
   * Posts a site template choice the way HTMX does when a radio changes.
   *
   * @param string $choice
   *   The machine name of the site template to choose.
   */
  private function sendHtmxChoice(string $choice): void {
    $page = $this->getSession()->getPage();
    $form = $this->assertSession()->elementExists('css', 'form[data-drupal-selector="installer-site-template-form"]');
    $radio = $this->assertSession()->elementExists('css', 'input[name="add_ons"][value="' . $choice . '"]', $form);
    $values = [
      'add_ons' => $choice,
      'form_build_id' => $form->find('css', 'input[name="form_build_id"]')->getValue(),
      'form_id' => $form->find('css', 'input[name="form_id"]')->getValue(),
      '_triggering_element_name' => 'add_ons',
    ];
    $url = $this->getAbsoluteUrl((string) $radio->getAttribute('data-hx-post'));
    $this->getSession()->getDriver()->getClient()->request('POST', $url, $values, [], [
      'HTTP_HX_REQUEST' => 'true',
      'HTTP_HX_TARGET' => 'site-template-license',
      'HTTP_HX_TRIGGER' => (string) $radio->getAttribute('id'),
      'HTTP_HX_TRIGGER_NAME' => 'add_ons',
    ]);
    $this->assertNotNull($page->find('css', 'body'));
  }

  /**
   * {@inheritdoc}
   */
  protected function installDefaultThemeFromClassProperty(ContainerInterface $container): void {
    // Nothing to do here; the default theme is set during the install process.
  }

}
