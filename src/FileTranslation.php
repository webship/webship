<?php

declare(strict_types=1);

namespace Drupal\webship;

use Drupal\Core\StringTranslation\Translator\FileTranslation as CoreFileTranslation;

/**
 * Decorates the file-based string translation service to use dynamic prefixing.
 *
 * @internal
 *   Everything in the Webship installer is internal and may be changed or
 *   removed at any time without warning. External code should not interact
 *   with this class.
 */
final class FileTranslation extends CoreFileTranslation {

  public function __construct(parent $instance) {
    parent::__construct($instance->directory, $instance->fileSystem);
  }

  /**
   * {@inheritdoc}
   */
  protected function getTranslationFilesPattern($langcode = NULL): string {
    $pattern = parent::getTranslationFilesPattern($langcode);

    if (str_starts_with($pattern, '!drupal-')) {
      $pattern = '![a-zA-Z0-9_]+-' . substr($pattern, 8);
    }
    return $pattern;
  }

}
