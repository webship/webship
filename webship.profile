<?php

/**
 * @file
 * Site configuration for Webship site installation.
 */

/**
 * Implements hook_preprocess_install_page().
 */
function webship_preprocess_install_page(&$variables) {
  // Webship has custom styling for the install page.
  $variables['#attached']['library'][] = 'webship/install-page';
}


