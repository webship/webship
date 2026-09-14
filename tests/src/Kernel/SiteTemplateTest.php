<?php

declare(strict_types=1);

namespace Drupal\Tests\webship\Kernel;

use Drupal\Component\Assertion\Inspector;
use Drupal\Core\Link;
use Drupal\Core\Recipe\Recipe;
use Drupal\webship\SiteTemplate;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests the site template value object of the Webship installer.
 */
#[Group('webship')]
#[CoversClass(SiteTemplate::class)]
#[RunTestsInSeparateProcesses]
final class SiteTemplateTest extends KernelTestBase {

  /**
   * Tests creating a site template from a list of values.
   */
  public function testCreateFromValues(): void {
    $values = [
      'name' => 'Meridian Charter School',
      'screenshot' => 'https://www.example.com/screenshot',
      'package' => [
        'name' => 'dripyard/meridian-charter-school',
        'repository' => 'https://packages.dripyard.com',
        'authorization' => 'bearer',
      ],
      'description' => $this->getRandomGenerator()->sentences(3),
      'links' => [
        'https://www.example.com/demo',
        ['text' => 'Documentation', 'url' => 'https://www.example.com/docs'],
      ],
      'purchase' => [
        'price' => 899,
        'url' => 'https://www.example.com/buy',
        'validation_url' => 'https://www.example.com/validate',
      ],
    ];

    $template = new SiteTemplate(...$values);
    $this->assertSame($values['name'], $template->name);
    $this->assertSame($values['package']['name'], $template->locator);
    $this->assertSame($values['description'], $template->description);
    $this->assertSame($values['screenshot'], $template->getScreenshot());
    $this->assertTrue(
      Inspector::assertAllObjects($template->links, Link::class),
    );
    $this->assertCount(2, $template->links);
    $this->assertSame('More Info', (string) $template->links[0]->getText());
    $this->assertSame($values['links'][1]['text'], (string) $template->links[1]->getText());
    $this->assertSame((float) $values['purchase']['price'], $template->price);
    $this->assertSame($values['purchase']['url'], $template->purchaseUrl?->toString());
    $this->assertSame($values['purchase']['validation_url'], $template->keyValidationUrl?->toString());
    $this->assertSame($values['package']['repository'], $template->repository);
    $this->assertSame($values['package']['authorization'], $template->authorization);
  }

  /**
   * Tests creating a site template from a recipe.
   */
  public function testCreateFromRecipe(): void {
    $recipe = Recipe::createFromDirectory(dirname(__DIR__, 2) . '/fixtures/recipes/webship_test_site');
    $template = SiteTemplate::createFromRecipe($recipe);
    $this->assertSame($recipe->name, $template->name);
    $this->assertSame($recipe->path, $template->locator);
    $this->assertSame($recipe->description, $template->description);
    $this->assertStringStartsWith('data:image/webp;base64,', $template->getScreenshot());

    // The metadata comes from extra.webship_installer in recipe.yml.
    $extra = $recipe->getExtra('webship_installer');
    $this->assertSame('Webship', $extra['creator']);
    $this->assertSame($extra['creator'], $template->creator);
    $this->assertCount(2, $template->links);
    $this->assertSame('More Info', (string) $template->links[0]->getText());
    $this->assertSame($extra['links'][0], $template->links[0]->getUrl()->toString());
    $this->assertSame('Documentation', (string) $template->links[1]->getText());
    $this->assertSame($extra['links'][1]['url'], $template->links[1]->getUrl()->toString());

    $this->assertSame(0.0, $template->price);
    $this->assertNull($template->purchaseUrl);
    $this->assertNull($template->keyValidationUrl);
    $this->assertNull($template->repository);
    $this->assertNull($template->authorization);
  }

  /**
   * Tests creating a site template from a recipe without a screenshot.
   */
  public function testCreateFromRecipeWithoutScreenshot(): void {
    // The fallback must hold with assertions enabled, like in DDEV.
    $this->assertSame('1', ini_get('zend.assertions'));

    $path = dirname(__DIR__, 2) . '/fixtures/recipes/webship_test_site_no_screenshot';
    $this->assertFileDoesNotExist($path . '/screenshot.webp');

    $template = SiteTemplate::createFromRecipe(Recipe::createFromDirectory($path));
    $default = dirname(__DIR__, 3) . '/default-screenshot.webp';
    $this->assertFileExists($default);
    $this->assertSame(
      'data:image/webp;base64,' . base64_encode(file_get_contents($default)),
      $template->getScreenshot(),
    );

    // The Webship installer metadata is optional.
    $this->assertNull($template->creator);
    $this->assertSame([], $template->links);
  }

}
