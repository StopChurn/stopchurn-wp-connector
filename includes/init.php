<?php

if (!defined('ABSPATH')) {
  exit;
}

function stopchurn_send_user_update($data, $table = 'user', $type = 'insert', $async = false) {
  $options = stopchurn_settings();

  $brand_id = isset($options['brand_id']) ? intval($options['brand_id']) : '';

  if (empty($brand_id)) {
    return;
  }

  $data = [
    'data' => [
      [
        'type' => $type,
        'data' => [
          'tableName' => $table,
          'brandId' => $brand_id,
          'data' => $data,
          'createdAt' => time(),
          'updatedAt' => time()
        ],
      ]
    ]
  ];

  if ($async) {
    wp_schedule_single_event(time() + 20, 'stopchurn_send_request', [$data, 'client-data/update']);
    return;
  }

  return stopchurn_send_request($data, 'client-data/update');
}

function stopchurn_send_user_event($user_id, $event_name, $value = null) {
  $options = stopchurn_settings();

  $brand_id = isset($options['brand_id']) ? intval($options['brand_id']) : '';

  if (empty($brand_id)) {
    return;
  }

  $data = [
    'data' => [
      [
        "brandId" => $brand_id,
        "userId" => $user_id,
        "name" => $event_name,
        "value" => $value,
      ]
    ]
  ];

  wp_schedule_single_event(time() + 30, 'stopchurn_send_request', [$data, 'client-data/event']);

  /* return stopchurn_send_request($data, 'client-data/event'); */
}

function stopchurn_send_request($data, $path) {
  $options = stopchurn_settings();

  $api_key = isset($options['api_key']) ? $options['api_key'] : '';
  $endpoint = isset($options['api_endpoint']) ? $options['api_endpoint'] : '';

  if (empty($api_key) || empty($endpoint)) {
    return;
  }

  $endpoint = rtrim($endpoint, '/') . '/' . ltrim($path, '/');

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
  // $body        = wp_remote_retrieve_body($response); var_dump($args, $body); exit;

  return [
    'status' => $status_code,
  ];
}
add_action('stopchurn_send_request', 'stopchurn_send_request', 10, 2);

