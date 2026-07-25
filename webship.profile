<?php

/**
 * @file
 * Site configuration for webship.co portal site installation.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\path_alias\Entity\PathAlias;
use Drupal\Core\Recipe\Recipe;
use Drupal\Core\Recipe\RecipeRunner;

 /**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function webship_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  $form['site_information']['site_name']['#default_value'] = t('Webship.co');
  $form['site_information']['site_mail']['#default_value'] = 'info@webship.co';
  $form['admin_account']['account']['name']['#default_value'] = 'webmaster';
  $form['admin_account']['account']['mail']['#default_value'] = 'info@webship.co';
}

/**
 * Implements hook_install_tasks_alter().
 */
function webship_install_tasks_alter(&$tasks, $install_state) {
  unset($tasks['install_select_language']);
  unset($tasks['install_download_translation']);

  $tasks['install_finished']['function'] = 'webship_after_install_finished';

  // Force the core content-entity-providing modules (node, taxonomy, block,
  // etc.) to be installed before Drupal's regular profile module batch runs.
  //
  // install_profile_modules() in core/includes/install.core.inc does not
  // honour the order of webship.info.yml's "install:" list. It schedules
  // modules purely by each module's dependency "sort" weight (arsort() on
  // the weight ExtensionDiscovery computes from *explicit* `dependencies:`
  // edges declared in .info.yml files). A module such as Web Development
  // (webdev) that has no explicit `dependencies: - drupal:node` edge can
  // therefore be scheduled, and have its hook_install() recipe run, before
  // "node" itself — even though "node" appears much earlier in
  // webship.info.yml's own list.
  //
  // Web Development's default recipe imports Pathauto's
  // "canonical_entities:node" pattern, and Drupal 11.4's stricter
  // EntityDisplayModeBase::calculateDependencies() throws a hard
  // PluginNotFoundException ("The 'node' entity type does not exist.") if
  // the node entity type does not exist yet when that config is imported.
  //
  // Pre-installing these modules in a dedicated task, before
  // install_profile_modules runs, guarantees they exist first; the profile
  // module batch simply skips modules that are already installed.
  //
  // The same task also creates webship.info.yml's own default user roles
  // (webship/config/install/user.role.*.yml) early. Drupal only installs an
  // install profile's own config/install directory during the
  // 'install_install_profile' task, which runs *after* install_profile_modules
  // has already run every module's hook_install()/recipe. Several sibling
  // web* modules' default recipes (e.g. Web Doc's "content_editor" grant)
  // call config actions such as `grantPermissions` against
  // "user.role.content_editor" while installing, which throws a hard
  // ConfigActionException ("Entity user.role.content_editor does not
  // exist") unless the role already exists by then.
  $key = array_search('install_profile_modules', array_keys($tasks), TRUE);
  $tasks = array_slice($tasks, 0, $key, TRUE) + [
    'webship_install_core_content_modules' => [
      'display' => FALSE,
      'run' => INSTALL_TASK_RUN_IF_REACHED,
    ],
  ] + array_slice($tasks, $key, NULL, TRUE);
}

/**
 * Installs core content-entity-providing modules ahead of the profile batch.
 *
 * @param array $install_state
 *   The current install state.
 *
 * @see webship_install_tasks_alter()
 */
function webship_install_core_content_modules(array &$install_state) {
  \Drupal::service('module_installer')->install([
    'node',
    'taxonomy',
    'block',
    'block_content',
    'field',
    'field_ui',
    'text',
    'filter',
    'views',
    'views_ui',
    'menu_ui',
    'menu_link_content',
    'datetime',
    'datetime_range',
    'options',
  ]);

  webship_install_default_roles();
}

/**
 * Creates webship's own default user roles ahead of the profile batch.
 *
 * @see webship_install_tasks_alter()
 */
function webship_install_default_roles() {
  $role_storage = \Drupal::entityTypeManager()->getStorage('user_role');
  $config_dir = \Drupal::service('extension.list.profile')->getPath('webship') . '/config/install';

  foreach (glob($config_dir . '/user.role.*.yml') as $file) {
    $id = basename($file, '.yml');
    $id = substr($id, strlen('user.role.'));

    // The anonymous/authenticated roles always already exist; everything
    // else (e.g. "content_editor") needs creating here.
    if ($role_storage->load($id)) {
      continue;
    }

    try {
      $values = \Drupal\Core\Serialization\Yaml::decode(file_get_contents($file));
      unset($values['dependencies']);
      $role_storage->create($values)->save();
    }
    catch (\Exception $e) {
      \Drupal::logger('webship')->error('Unable to pre-create the %id role: %message', [
        '%id' => $id,
        '%message' => $e->getMessage(),
      ]);
    }
  }
}

/**
 * Implements hook_preprocess_install_page().
 */
function webship_preprocess_install_page(&$variables) {
  // Webship has custom styling for the install page.
  $variables['#attached']['library'][] = 'webship/install-page';
}

/**
 * Webship after install finished.
 *
 * Set front page to "/home" after install.
 *
 * @param array $install_state
 *   The current install state.
 *
 */
function webship_after_install_finished(array &$install_state) {

  $default_recipe = Recipe::createFromDirectory(__DIR__ . '/recipes/default');
  RecipeRunner::processRecipe($default_recipe);

  // Set front page to "/home".
  try {
    $alias_ids = \Drupal::entityQuery('path_alias')
      ->accessCheck(FALSE)
      ->condition('alias', '/home', '=')
      ->execute();

    if (count($alias_ids) > 0) {
      foreach ($alias_ids as $alias_id) {

        if (!(end($alias_ids))) {
          $path_alias = PathAlias::load($alias_id);
          $path_alias->delete();
        }
        else {
          $page_front_path = PathAlias::load($alias_id)->getPath();

          \Drupal::configFactory()->getEditable('system.site')
            ->set('page.front', $page_front_path)
            ->save();
        }
      }
    }
  }
  catch (\Exception $e) {
    \Drupal::messenger()->addError($e->getMessage());
  }

}
