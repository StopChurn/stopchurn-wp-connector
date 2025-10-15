<?php

if (!defined('ABSPATH')) {
    exit;
}

function stopchurn_add_menu() {
    add_options_page(
        'StopChurn Settings',
        'StopChurn',
        'manage_options',
        'stopchurn',
        'stopchurn_page_html'
    );
}
add_action('admin_menu', 'stopchurn_add_menu');

function stopchurn_register() {
    register_setting(
        'stopchurn_group',
        'stopchurn_options',
        [
            'sanitize_callback' => 'stopchurn_sanitize',
        ]
    );

    add_settings_section(
        'stopchurn_section',
        'API Configuration',
        function() {
            echo '<p>Enter your API credentials below.</p>';
        },
        'stopchurn'
    );

    add_settings_field(
        'api_key',
        'API Key',
        'stopchurn_api_key_field',
        'stopchurn',
        'stopchurn_section'
    );

    add_settings_field(
        'api_endpoint',
        'API Endpoint',
        'stopchurn_api_endpoint_field',
        'stopchurn',
        'stopchurn_section'
    );

    add_settings_field(
        'brand_id',
        'Brand ID',
        'stopchurn_brand_id_field',
        'stopchurn',
        'stopchurn_section'
    );
}
add_action('admin_init', 'stopchurn_register');

function stopchurn_sanitize($input) {
    $new_input = [];
    if (isset($input['api_key'])) {
        $new_input['api_key'] = sanitize_text_field($input['api_key']);
    }
    if (isset($input['api_endpoint'])) {
        $new_input['api_endpoint'] = esc_url_raw($input['api_endpoint']);
    }
    if (isset($input['brand_id'])) {
        $new_input['brand_id'] = sanitize_text_field($input['brand_id']);
    }
    return $new_input;
}

function stopchurn_api_key_field() {
    $options = stopchurn_settings();
    $value = isset($options['api_key']) ? esc_attr($options['api_key']) : '';
    echo "<input type='text' name='stopchurn_options[api_key]' value='{$value}' class='regular-text' />";
}

function stopchurn_api_endpoint_field() {
    $options = stopchurn_settings();
    $value = isset($options['api_endpoint']) ? esc_attr($options['api_endpoint']) : '';
    echo "<input type='url' name='stopchurn_options[api_endpoint]' value='{$value}' class='regular-text' />";
}

function stopchurn_brand_id_field() {
    $options = stopchurn_settings();
    $value = isset($options['brand_id']) ? esc_attr($options['brand_id']) : '';
    echo "<input type='number' name='stopchurn_options[brand_id]' value='{$value}' class='regular-text' />";
}

function stopchurn_page_html() {
    ?>
    <div class="wrap">
        <h1>StopChurn Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('stopchurn_group');
            do_settings_sections('stopchurn');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}


function stopchurn_settings() {
  return get_option('stopchurn_options');
}
