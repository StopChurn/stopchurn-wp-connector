<?php

if (!defined('ABSPATH')) {
  exit;
}

function stopchurn_send_user_update($data) {
  $options = stopchurn_settings();

  $brand_id = isset($options['brand_id']) ? intval($options['brand_id']) : '';

  if (empty($brand_id)) {
    return;
  }

  $data = [
    'data' => [
      [
        'type' => 'update',
        'data' => [
          'tableName' => 'user',
          'brandId' => $brand_id,
          'data' => $data,
          'createdAt' => time(),
          'updatedAt' => time()
        ],
      ]
    ]
  ];

  return stopchurn_send_update($data);
}

function stopchurn_send_update($data) {
  $options = stopchurn_settings();

  $api_key = isset($options['api_key']) ? $options['api_key'] : '';
  $endpoint = isset($options['api_endpoint']) ? $options['api_endpoint'] : '';

  if (empty($api_key) || empty($endpoint)) {
    return;
  }

  $args = [
    'headers' => [
      'Authorization' => 'Bearer ' . $api_key,
      'Content-Type'  => 'application/json',
    ],
    'body'    => wp_json_encode($data),
    'timeout' => 20,
  ];

  $response = wp_remote_post($endpoint, $args);

  if (is_wp_error($response)) {
    return $response;
  }

  $status_code = wp_remote_retrieve_response_code($response);
  // $body        = wp_remote_retrieve_body($response);

  return [
    'status' => $status_code,
  ];
}
