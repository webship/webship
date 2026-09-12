<?php

/**
 * @file
 * Simulates a Composer repository protected by an access token.
 *
 * It only exposes packages when presented with a valid access token.
 */

declare(strict_types=1);

$data = [
  'packages' => [],
  'notify-batch' => '/packages',
];

if (array_key_exists('HTTP_AUTHORIZATION', $_SERVER) && $_SERVER['HTTP_AUTHORIZATION'] === 'Bearer A8C8F935-107E-F883-55BB-CE43E398CF53') {
  $data['packages']['drupal/test_auth'][] = [
    'name' => 'drupal/test_auth',
    'version' => '1.0.0',
    'type' => 'drupal-recipe',
    'dist' => [
      'type' => 'zip',
      'url' => 'http://' . $_SERVER['HTTP_HOST'] . '/download-me.zip',
    ],
  ];
  // The actual ZIP file also requires that you have the proper token.
  if ($_SERVER['REQUEST_URI'] === '/download-me.zip') {
    header('Content-Type: application/zip');
    echo file_get_contents(__DIR__ . '/payload.zip');
    exit;
  }
}
header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_SLASHES);
