<?php

/**
 * @file
 * Site configuration for webship.co portal site installation.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\path_alias\Entity\PathAlias;

 /**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function webship_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  $form['site_information']['site_name']['#default_value'] = t('Webship.co');
  $form['site_information']['site_mail']['#default_value'] = 'admin@webship.co';
  $form['admin_account']['account']['name']['#default_value'] = 'webmaster';
  $form['admin_account']['account']['mail']['#default_value'] = 'admin@webship.co';
}

/**
 * Implements hook_install_tasks_alter().
 */
function webship_install_tasks_alter(&$tasks, $install_state) {
  unset($tasks['install_select_language']);
  unset($tasks['install_download_translation']);

  $tasks['install_finished']['function'] = 'webship_after_install_finished';
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
 * @return array
 *   A renderable array with a redirect header.
 */
function webship_after_install_finished(array &$install_state) {

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
