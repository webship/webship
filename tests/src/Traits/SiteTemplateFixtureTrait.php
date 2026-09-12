<?php

declare(strict_types=1);

namespace Drupal\Tests\webship\Traits;

/**
 * Lists the fixture site template for the installer in a test site.
 */
trait SiteTemplateFixtureTrait {

  /**
   * The machine name of the fixture site template.
   */
  protected static string $fixtureSiteTemplate = 'webship_test_site';

  /**
   * Returns the directory of the fixture site template.
   *
   * @return string
   *   The absolute path of the fixture recipe.
   */
  protected static function getFixtureSiteTemplatePath(): string {
    return dirname(__DIR__, 2) . '/fixtures/recipes/' . static::$fixtureSiteTemplate;
  }

  /**
   * Writes the list of site templates of a test site.
   *
   * The Webship installer reads `site-templates.php` in the site directory
   * instead of the curated list, so the tests install a local recipe without
   * Composer or network access.
   *
   * @param string $site_path
   *   The absolute path of the site directory.
   *
   * @see \Drupal\webship\Form\SiteTemplateForm::getCuratedList()
   */
  protected function writeSiteTemplatesFile(string $site_path): void {
    $path = static::getFixtureSiteTemplatePath();
    $list = [
      static::$fixtureSiteTemplate => [
        'name' => 'Webship Test Site',
        'description' => 'A site template for the tests of the Webship installer.',
        'path' => $path,
        'screenshot' => $path . '/screenshot.webp',
      ],
    ];
    if (!is_dir($site_path)) {
      mkdir($site_path, 0777, TRUE);
    }
    file_put_contents($site_path . '/site-templates.php', '<?php return ' . var_export($list, TRUE) . ';');
  }

}
