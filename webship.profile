<?php

/**
 * @file
 * Site configuration for Webship site installation.
 */

/**
 * Implements hook_install_tasks_alter().
 */
function webship_install_tasks_alter(&$tasks, $install_state) {
  unset($tasks['install_select_language']);
  unset($tasks['install_download_translation']);
}

/**
 * Implements hook_preprocess_install_page().
 */
function webship_preprocess_install_page(&$variables) {
  // Webship has custom styling for the install page.
  $variables['#attached']['library'][] = 'webship/install-page';
}

/**
 * Implements hook_toolbar_alter().
 */
function webship_toolbar_alter(&$items) {
  if (\Drupal::currentUser()->hasPermission('access toolbar')
    && !empty($items['admin_toolbar_tools'])) {
    $items['admin_toolbar_tools']['#attached']['library'][] = 'webadmin/admin-toolbar-tools';
  }
}

