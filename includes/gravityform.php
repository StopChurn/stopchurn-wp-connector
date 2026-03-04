<?php

if (!defined('ABSPATH')) {
  exit;
}

function stopchurn_gform_after_submission($entry, $form) {
  $user = [
    "email" => '',
    "phone" => '',
    "firstName" => '',
    "lastName" => '',
  ];
  $utms = [];
  $country_code = '';

  foreach ( $form['fields'] as $field ) {
    switch ($field->type) {
      case 'name':
        $user['firstName'] = $field->id . '.3';
        $user['lastName'] = $field->id . '.6';
        break;
      case 'email':
      case 'phone':
        $user[$field->type] = (string) $field->id;
        break;
    }

    if ($field->label === 'Country Code' || $field->adminLabel === 'Country Code' || $field->autocompleteAttribute === 'tel-country-code') {
      $country_code = $field->id;
    }

    if (
      !$user['firstName'] &&
      (strpos($field->label, 'First Name') !== false || strpos($field->adminLabel, 'First Name') !== false || $field->autocompleteAttribute === 'given-name')
    ) {
      $user['firstName'] = $field->id;
    }

    if (
      !$user['lastName'] &&
      (strpos($field->label, 'Last Name') !== false || strpos($field->adminLabel, 'Last Name') !== false || $field->autocompleteAttribute === 'family-name')
    ) {
      $user['lastName'] = $field->id;
    }

    if (strpos($field->label, 'utm_') === 0) {
      $utms[$field->label] = $field->id;
    }
  }


  foreach($user as $k => $id) {
    $user[$k] = $id ? rgar( $entry, $id ) : '';
  }

  foreach($utms as $k => $id) {
    $utms[$k] = $id ? rgar( $entry, $id ) : '';
  }

  if ($country_code && $user['phone']) {
    $user['phone'] = rgar( $entry, $country_code ) . $user['phone'];
  }

  if (!$user['email']) {
    return;
  }

  $user['id'] = $user['email'];

  stopchurn_send_user_update($user);

  stopchurn_send_user_update(
    array_merge(
      [
        "id" => $form['id'] . '_' . $user['email'] . '_' . uniqid(),
        "userId" => $user['email'],
        "formId" => strval($form['id']),
        "formTitle" => $form['title'],
        "submittedAt" => time() * 1000,
      ],
      $utms
    ),
    'formSubmission',
    'insert',
    true,
  );

  stopchurn_send_user_event(
    $user['email'],
    'SUBMISSION',
    $form['title'],
  );
}
add_action('gform_after_submission', 'stopchurn_gform_after_submission', 10, 2);
