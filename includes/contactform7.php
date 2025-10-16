<?php

if (!defined('ABSPATH')) {
  exit;
}

function stopchurn_cf7_sent($data) {
  $submission = WPCF7_Submission::get_instance();

  if ( $submission ) {
    $cf7_data = $submission->get_posted_data();

    $full_name = trim($cf7_data['your-name']);
    $name_parts = explode(' ', $full_name, 2);

    stopchurn_send_user_update([
      "id" => $cf7_data['your-email'],
      "email" => $cf7_data['your-email'],
      "firstName" => $name_parts[0],
      "lastName" => isset($name_parts[1]) ? $name_parts[1] : '',
    ]);
  }
}
add_action('wpcf7_before_send_mail', 'stopchurn_cf7_sent');
