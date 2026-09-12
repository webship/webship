<?php

declare(strict_types=1);

namespace Drupal\webship\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Installer\Form\SiteSettingsForm as CoreSiteSettingsForm;

/**
 * Overrides the database settings form.
 *
 * @internal
 *   Everything in the Webship installer is internal and may be changed or
 *   removed at any time without warning. External code should not interact
 *   with this class.
 */
final class SiteSettingsForm extends CoreSiteSettingsForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    $sqlite_key = 'Drupal\sqlite\Driver\Database\sqlite';
    // Default to SQLite, if available, because it doesn't require any
    // additional configuration.
    if (array_key_exists($sqlite_key, $form['driver']['#options'])) {
      $form['driver']['#default_value'] = $sqlite_key;

      // The database file path has a sensible default value, so move it into
      // the advanced options.
      $form['settings'][$sqlite_key]['advanced_options']['database'] = $form['settings'][$sqlite_key]['database'];
      unset($form['settings'][$sqlite_key]['database']);
    }
    return $form;
  }

}
