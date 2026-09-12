<?php

declare(strict_types=1);

namespace Drupal\webship_installer_theme\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Form alter hook implementations for the steps of the Webship installer.
 *
 * @internal
 *   Everything in the Webship installer is internal and may be changed or
 *   removed at any time without warning. External code should not interact
 *   with this class.
 */
final class FormHooks {

  use StringTranslationTrait;

  /**
   * Implements hook_form_FORM_ID_alter() for installer_site_name_form.
   */
  #[Hook('form_installer_site_name_form_alter')]
  public function siteNameFormAlter(array &$form, FormStateInterface $form_state): void {
    $form['help'] = [
      '#prefix' => '<p class="cms-installer__subhead">',
      '#markup' => $this->t('You can change this later.'),
      '#suffix' => '</p>',
      '#weight' => -100,
    ];
    $form['site_name'] += [
      '#prefix' => '<div class="cms-installer__form-group">',
      '#suffix' => '</div>',
    ];
    $form['svg'] = [
      '#theme' => 'step_svg',
      '#id' => 'name',
      '#weight' => 1000,
    ];
    $form['actions']['submit']['#attributes']['class'] = ['button--next'];
  }

  /**
   * Implements hook_form_FORM_ID_alter() for installer_site_template_form.
   */
  #[Hook('form_installer_site_template_form_alter')]
  public function siteTemplateFormAlter(array &$form, FormStateInterface $form_state): void {
    $form['help'] = [
      '#prefix' => '<p class="cms-installer__subhead">',
      '#markup' => $this->t("Site templates provide a starting point with styling and features already included. You can customize the look and feel and add new features using Drupal's powerful extensibility."),
      '#suffix' => '</p>',
      '#weight' => -100,
    ];
    $form['add_ons'] += [
      '#prefix' => '<div class="cms-installer__form-group">',
      '#suffix' => '</div>',
    ];
    $form['svg'] = [
      '#theme' => 'step_svg',
      '#id' => 'template',
      '#weight' => 1000,
    ];
    $form['actions']['submit']['#attributes']['class'] = ['button--next'];
  }

  /**
   * Implements hook_form_FORM_ID_alter() for install_settings_form.
   */
  #[Hook('form_install_settings_form_alter')]
  public function installSettingsFormAlter(array &$form, FormStateInterface $form_state): void {
    $form['help'] = [
      '#prefix' => '<p class="cms-installer__subhead">',
      '#markup' => $this->t("You don't need to change anything here unless you want to use a different database type."),
      '#suffix' => '</p>',
      '#weight' => -50,
    ];
    $form['svg'] = [
      '#theme' => 'step_svg',
      '#id' => 'settings',
      '#weight' => 1000,
    ];
    $form['driver']['#type'] = 'select';
  }

  /**
   * Implements hook_form_FORM_ID_alter() for install_configure_form.
   */
  #[Hook('form_install_configure_form_alter')]
  public function installConfigureFormAlter(array &$form, FormStateInterface $form_state): void {
    $form['#title'] = $this->t('Create your account');

    $form['help'] = [
      '#prefix' => '<p class="cms-installer__subhead">',
      '#markup' => $this->t('Creating an account allows you to log in to your site.'),
      '#suffix' => '</p>',
      '#weight' => -40,
    ];

    // Use isset() to guard against the possibility that core will change the
    // structure of this form in a minor release.
    if (isset($form['admin_account']['account']['name'])) {
      $form['admin_account']['account']['mail'] += [
        '#prefix' => '<div class="cms-installer__form-group">',
        '#suffix' => '</div>',
      ];
    }
    if (isset($form['admin_account']['account']['pass'])) {
      $form['admin_account']['account']['pass'] += [
        '#prefix' => '<div class="cms-installer__form-group">',
        '#suffix' => '</div>',
      ];
    }
    $form['svg'] = [
      '#theme' => 'step_svg',
      '#id' => 'account',
      '#weight' => 1000,
    ];
    $form['actions']['submit']['#value'] = $this->t('Finish');
  }

}
