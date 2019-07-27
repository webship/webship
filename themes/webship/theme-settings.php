<?php

/**
 * @file
 * Provides theme settings for Webship Theme.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function webship_form_system_theme_settings_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {

  if (isset($form_id)) {
    return;
  }
}
