<?php

if (!defined('ABSPATH')) {
  exit;
}

function stopchurn_cf7_sent($data) {
  $submission = WPCF7_Submission::get_instance();

  if ( $submission ) {
    $cf7_data = $submission->get_posted_data();

    stopchurn_send_user_update([
      "id" => $cf7_data['your-email'],
      "email" => $cf7_data['your-email'],
      "name" => $cf7_data['your-name'],
    ]);
  }
}
add_action('wpcf7_before_send_mail', 'stopchurn_cf7_sent');
