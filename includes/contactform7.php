<?php

if (!defined('ABSPATH')) {
  exit;
}

function stopchurn_cf7_sent($form) {
  $submission = WPCF7_Submission::get_instance();

  if ( $submission ) {
    $cf7_data = $submission->get_posted_data();

    $email_key = '';
    $name_key = '';
    $phone_key = '';

    foreach($cf7_data as $k => $d) {
      if (!is_string($d)) {
        continue;
      }

      if (strpos(strtolower($k), 'email') !== false) {
        $email_key = $k;
      }

      if (strpos(strtolower($k), 'name') !== false) {
        $name_key = $k;
      }

      if (strpos(strtolower($k), 'phone') !== false) {
        /* $phone_key = $k; */
      }
    }

    $email_key = $email_key ? $email_key : 'your-email';
    $name_key = $name_key ? $name_key : 'your-name';
    $phone_key = $phone_key ? $phone_key : 'your-phone';

    $full_name = isset($cf7_data[$name_key]) ? trim($cf7_data[$name_key]) : '';
    $name_parts = explode(' ', $full_name, 2);

    $phone = isset($cf7_data[$phone_key]) ? trim($cf7_data[$phone_key]) : '';
    $email = isset($cf7_data[$email_key]) ? trim($cf7_data[$email_key]) : '';

    if (empty($email)) {
      // no data
      return;
    }

    stopchurn_send_user_update([
      "id" => $email,
      "email" => $email,
      "phone" => $phone,
      "firstName" => $name_parts[0],
      "lastName" => isset($name_parts[1]) ? $name_parts[1] : '',
    ]);

    stopchurn_send_user_event(
      $email,
      'SUBMISSION',
      $form->title(),
    );
  }
}
add_action('wpcf7_before_send_mail', 'stopchurn_cf7_sent');

