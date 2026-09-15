<?php

/**
 * @file
 * Contains the install tasks hook of the Webship installer.
 *
 * Core only discovers hook_install_tasks_alter() as a procedural function.
 *
 * @see \Drupal\webship\Installer\InstallTasks
 *
 * @internal
 *   Everything in the Webship installer is internal and may be changed or
 *   removed at any time without warning.
 */

declare(strict_types=1);

use Drupal\Core\StringTranslation\Translator\FileTranslation as CoreFileTranslation;
use Drupal\webship\FileTranslation;
use Drupal\webship\Form\SiteNameForm;
use Drupal\webship\Form\SiteSettingsForm;
use Drupal\webship\Form\SiteTemplateForm;
use Drupal\webship\Installer\InstallTasks;

/**
 * Implements hook_install_tasks_alter().
 */
function webship_install_tasks_alter(array &$tasks, array $install_state): void {
  $container = Drupal::getContainer();
  // Add a translator that will look at translation files for projects other
  // than Drupal core.
  $decorated = $container->get('string_translator.file_translation', $container::NULL_ON_INVALID_REFERENCE);
  if ($decorated instanceof CoreFileTranslation) {
    Drupal::translation()->addTranslator(new FileTranslation($decorated));
  }

  $insert_before = function (string $key, array $additions) use (&$tasks): void {
    $key = array_search($key, array_keys($tasks), TRUE);
    if ($key === FALSE) {
      return;
    }
    // This isn't very clean, but it's the only way to positionally splice
    // into an associative (and therefore by definition unordered) array.
    $tasks_before = array_slice($tasks, 0, $key, TRUE);
    $tasks_after = array_slice($tasks, $key, NULL, TRUE);
    $tasks = $tasks_before + $additions + $tasks_after;
  };

  // We need to override the database settings form because form alter hooks are
  // not invoked in the early installer.
  $tasks['install_settings_form']['function'] = SiteSettingsForm::class;

  // When we install the profile itself, we'll also need User to configure the
  // site and administrator account.
  $install_profile_task = [
    'function' => InstallTasks::class . '::installProfile',
  ] + $tasks['install_install_profile'];

  $configure_form_task = $tasks['install_configure_form'];
  unset($tasks['install_install_profile'], $tasks['install_configure_form']);

  // Before applying any recipes:
  // - Install the profile itself
  // - Choose a name for the site
  // - Choose a site template
  // - Set up the administrator account.
  $insert_before('install_profile_modules', [
    'install_install_profile' => $install_profile_task,
    'webship_set_site_name' => [
      'display_name' => t('Name your site'),
      'type' => 'form',
      'run' => $install_state['parameters'][SiteNameForm::TASK_ID] ?? INSTALL_TASK_RUN_IF_REACHED,
      'function' => SiteNameForm::class,
    ],
    'webship_choose_template' => [
      'display_name' => t('Choose site template'),
      'run' => $install_state['parameters'][SiteTemplateForm::TASK_ID] ?? INSTALL_TASK_RUN_IF_REACHED,
      'function' => InstallTasks::class . '::chooseTemplate',
    ],
    'install_configure_form' => $configure_form_task,
  ]);

  // Set English as the default language; we support changing it mid-stream.
  // We can't use the passed-in $install_state here because it's not passed
  // by reference.
  $GLOBALS['install_state']['parameters'] += ['langcode' => 'en'];

  // If translations will be downloaded, ensure that we also download the
  // translations for this profile.
  $tasks['install_download_translation']['function'] = InstallTasks::class . '::downloadTranslations';

  // Wrap the install_profile_modules() function, which returns a batch job, and
  // add all the necessary operations to apply the chosen template recipe.
  $tasks['install_profile_modules']['function'] = InstallTasks::class . '::applyRecipes';

  // When the installation is finished, perform additional clean-up tasks (i.e.,
  // uninstall this profile).
  $tasks['install_finished']['function'] = InstallTasks::class . '::finished';
}
