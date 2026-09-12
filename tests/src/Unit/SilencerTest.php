<?php

declare(strict_types=1);

namespace Drupal\Tests\webship\Unit;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\webship\Silencer;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the messenger decorator of the Webship installer.
 */
#[CoversClass(Silencer::class)]
#[Group('webship')]
final class SilencerTest extends UnitTestCase {

  /**
   * Provides the messenger methods to test.
   *
   * @return array
   *   The test cases, each with the name of a messenger method.
   */
  public static function provider(): array {
    return [
      ['addMessage'],
      ['addStatus'],
      ['addWarning'],
      ['addError'],
    ];
  }

  /**
   * Tests that the translation status message is not passed on.
   */
  #[DataProvider('provider')]
  public function testRejectedMessage(string $method): void {
    $decorated = $this->createMock(MessengerInterface::class);
    $decorated->expects($this->never())
      ->method('addMessage')
      ->withAnyParameters();

    $silencer = new Silencer($decorated);
    $silencer->$method('Check <a href=":translate_status">available translations</a> for your language(s).');
    $silencer->$method(new TranslatableMarkup('Check <a href=":translate_status">available translations</a> for your language(s).'));
  }

  /**
   * Tests that other messages are passed on.
   */
  #[DataProvider('provider')]
  public function testAllowedMessage(string $method): void {
    $decorated = $this->createMock(MessengerInterface::class);
    $decorated->expects($this->exactly(2))
      ->method('addMessage')
      ->withAnyParameters();

    $silencer = new Silencer($decorated);
    $silencer->$method('I can say this.');
    $silencer->$method(new TranslatableMarkup('I can say this.'));
  }

}
