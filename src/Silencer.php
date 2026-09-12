<?php

declare(strict_types=1);

namespace Drupal\webship;

use Drupal\Component\Gettext\PoItem;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;

/**
 * Decorates the messenger to suppress certain install-time messages.
 *
 * @internal
 *   Everything in the Webship installer is internal and may be changed or
 *   removed at any time without warning. External code should not interact
 *   with this class.
 */
final class Silencer implements MessengerInterface {

  /**
   * The untranslated messages that should be silenced.
   *
   * @var list<string>
   */
  private static array $reject = [];

  public function __construct(
    #[AutowireDecorated] private readonly MessengerInterface $decorated,
  ) {
    // When running in DDEV, this message serves no purpose.
    if (getenv('IS_DDEV_PROJECT')) {
      self::$reject[] = 'All necessary changes to %dir and %file have been made, so you should remove write permissions to them now in order to avoid security risks. If you are unsure how to do so, consult the <a href=":handbook_url">online handbook</a>.';
    }

    // If installed in a language other than English, don't show these confusing
    // technical messages about how many translations were imported.
    // @see locale_translate_batch_finished()
    self::$reject[] = implode(PoItem::DELIMITER, [
      'One translation file imported. %number translations were added, %update translations were updated and %delete translations were removed.',
      '@count translation files imported. %number translations were added, %update translations were updated and %delete translations were removed.',
    ]);
    self::$reject[] = 'The configuration was successfully updated. There are %number configuration objects updated.';

    // Appears mid-install if you install in a non-English language, which is
    // confusing.
    self::$reject[] = 'Check <a href=":translate_status">available translations</a> for your language(s).';

    // Appears post-install, or mid-install (when it's not actionable) if you
    // install in a non-English language, but it's pointless since Webship
    // does the necessary set-up.
    self::$reject[] = 'Honeypot installed successfully. Please <a href=":url">configure Honeypot</a> to protect your forms from spam bots.';
  }

  /**
   * {@inheritdoc}
   */
  public function addMessage($message, $type = self::TYPE_STATUS, $repeat = FALSE): static {
    $raw = $message instanceof TranslatableMarkup
      ? $message->getUntranslatedString()
      : strval($message);

    if (!in_array($raw, self::$reject, TRUE)) {
      $this->decorated->addMessage($message, $type, $repeat);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addStatus($message, $repeat = FALSE): static {
    return $this->addMessage($message, self::TYPE_STATUS, $repeat);
  }

  /**
   * {@inheritdoc}
   */
  public function addError($message, $repeat = FALSE): static {
    return $this->addMessage($message, self::TYPE_ERROR, $repeat);
  }

  /**
   * {@inheritdoc}
   */
  public function addWarning($message, $repeat = FALSE): static {
    return $this->addMessage($message, self::TYPE_WARNING, $repeat);
  }

  /**
   * {@inheritdoc}
   */
  public function all(): array {
    return $this->decorated->all();
  }

  /**
   * {@inheritdoc}
   */
  public function messagesByType($type): array {
    return $this->decorated->messagesByType($type);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll(): array {
    return $this->decorated->deleteAll();
  }

  /**
   * {@inheritdoc}
   */
  public function deleteByType($type): array {
    return $this->decorated->deleteByType($type);
  }

}
