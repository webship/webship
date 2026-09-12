<?php

declare(strict_types=1);

namespace Drupal\Tests\webship\Kernel;

use Composer\InstalledVersions;
use Drupal\Core\Recipe\Recipe;
use Drupal\KernelTests\KernelTestBase;
use Drupal\webship\RecipeHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests the recipe handler of the Webship installer.
 */
#[CoversClass(RecipeHandler::class)]
#[Group('webship')]
#[RunTestsInSeparateProcesses]
final class RecipeHandlerTest extends KernelTestBase {

  /**
   * Tests finding and queueing recipes.
   */
  public function test(): void {
    $fixtures = dirname(__DIR__, 2) . '/fixtures/recipes';
    $handler = new RecipeHandler(
      \Drupal::state(),
      \Drupal::messenger(),
      $fixtures . '/{$name}',
    );

    $recipes = array_map(
      fn (Recipe $recipe): string => $recipe->path,
      iterator_to_array($handler->scan()),
    );
    $this->assertSame($fixtures . '/webship_test_site', $recipes['webship_test_site']);
    $this->assertSame($fixtures . '/webship_test_feature', $recipes['webship_test_feature']);
    // Only the site templates are listed by type.
    $this->assertSame(['webship_test_site'], array_keys(iterator_to_array($handler->scan('Site'))));

    // A Composer-managed package path should be returned as-is.
    $this->assertSame(InstalledVersions::getInstallPath('drupal/core'), $handler->getPath('drupal/core'));
    // An unpacked recipe (i.e., not Composer-managed) should still have its
    // path extrapolated.
    $this->assertSame($fixtures . '/foo', $handler->getPath('drupal/foo'));

    $this->assertSame([], $handler->list());
    $this->assertSame(['drupal/foo', 'drupal/bar'], $handler->enqueue('drupal/foo', 'drupal/bar')->list());
    $this->assertSame(['drupal/bar'], $handler->markAsApplied('drupal/foo')->list());
    $this->assertSame([], $handler->markAsApplied('drupal/bar')->list());
  }

  /**
   * Tests a project without a recipe directory.
   */
  public function testMissingRecipeDirectory(): void {
    $handler = new RecipeHandler(
      \Drupal::state(),
      \Drupal::messenger(),
      $this->siteDirectory . '/no-recipes/{$name}',
    );
    $this->assertSame([], iterator_to_array($handler->scan('Site')));
  }

}
