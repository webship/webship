<?php

declare(strict_types=1);

namespace Drupal\webship_installer_theme\Hook;

use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Theme hook implementations for the pages of the Webship installer.
 *
 * @internal
 *   Everything in the Webship installer is internal and may be changed or
 *   removed at any time without warning. External code should not interact
 *   with this class.
 */
final readonly class InstallPageHooks {

  public function __construct(
    private ThemeManagerInterface $themeManager,
    private FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'step_svg' => [
        'variables' => [
          'id' => NULL,
        ],
      ],
    ];
  }

  /**
   * Implements hook_preprocess_HOOK() for install_page.
   */
  #[Hook('preprocess_install_page')]
  public function preprocessInstallPage(array &$variables): void {
    $theme_path = $this->themeManager->getActiveTheme()->getPath();
    $variables['theme_path'] = $this->fileUrlGenerator
      ->generateString($theme_path);

    // Only show the language switcher if there is no batch job in progress.
    $batch = &batch_get();
    if (empty($batch)) {
      $variables['languages'] = [];
      foreach (LanguageManager::getStandardLanguageList() as $langcode => $language) {
        // Pass the language's native name to the template, along with its
        // direction (if defined).
        $variables['languages'][$langcode] = [
          'name' => $language[1],
          'direction' => $language[2] ?? LanguageInterface::DIRECTION_LTR,
        ];
      }
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for form_element.
   *
   * Adds the site template information to the site template choices.
   */
  #[Hook('preprocess_form_element')]
  public function preprocessFormElement(array &$variables, string $hook): void {
    if ($hook !== 'form_element__site_template') {
      return;
    }

    /** @var \Drupal\webship\SiteTemplate $recipe */
    $recipe = $variables['element']['item'];
    $variables['screenshot'] = $recipe->getScreenshot();
    // Open all links in a new tab.
    foreach ($recipe->links as $link) {
      $link->getUrl()->setOption('attributes', ['target' => '_blank']);
    }
    $variables['links'] = $recipe->links;
    $variables['price'] = $recipe->price;
    $variables['purchase_url'] = $recipe->purchaseUrl;
    $variables['creator'] = $recipe->creator;

    $variables['name'] = $variables['element']['#title'];
    $variables['id'] = $variables['element']['#id'];
  }

}
