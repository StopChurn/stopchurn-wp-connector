<?php

if (!defined('ABSPATH')) {
  exit;
}

function stopchurn_llms_insert_user($user_id, $async = false) {
  $student = llms_get_student($user_id);

  $user = [
    "id" => $student->user_email,
    "email" => $student->user_email,
    "phone" => $student->phone,
    "firstName" => $student->first_name,
    "lastName" => $student->last_name,
  ];

  if (empty($user['id'])) {
    return;
  }

  stopchurn_send_user_update($user, 'user', 'insert', $async);

  return $user;
}

function stopchurn_lifterlms_user_registered($user_id) {
  if ($user = stopchurn_llms_insert_user($user_id)) {
    stopchurn_send_user_event(
      $user['id'],
      'REGISTER'
    );
  }
}
add_action('lifterlms_user_registered', 'stopchurn_lifterlms_user_registered');

function stopchurn_llms_user_enrolled_in_course($user_id, $product_id) {
  if ($user = stopchurn_llms_insert_user($user_id)) {
    $course = new LLMS_Course($product_id);

    stopchurn_send_user_update(
      [
        "id" => $product_id . '_' . $user['email'] . '_' . uniqid(),
        "userId" => $user['email'],
        "courseId" => $product_id,
        "courseTitle" => $course->post_title,
      ],
      'course',
      'insert',
      true,
    );

    stopchurn_send_user_event(
      $user['id'],
      'COURSE_ENROLL',
      $course->post_title,
    );
  }
}
add_action('llms_user_enrolled_in_course', 'stopchurn_llms_user_enrolled_in_course', 10, 2);

function stopchurn_llms_user_added_to_membership_level($user_id, $product_id) {
  if ($user = stopchurn_llms_insert_user($user_id)) {
    $membership = new LLMS_Membership($product_id);

    stopchurn_send_user_update(
      [
        "id" => $product_id . '_' . $user['email'] . '_' . uniqid(),
        "userId" => $user['email'],
        "courseId" => $product_id,
        "courseTitle" => $membership->post_title,
      ],
      'membership',
      'insert',
      true,
    );

    stopchurn_send_user_event(
      $user['id'],
      'MEMBERSHIP_ADD',
      $membership->post_title,
    );
  }

}
add_action('llms_user_added_to_membership_level', 'stopchurn_llms_user_added_to_membership_level', 10, 2);
