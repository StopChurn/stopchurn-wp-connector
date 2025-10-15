<?php

/**
 * Plugin Name: StopChurn Connector
 * Version: 1.0.0
 * Description: StopChurn Plugin
 * Author: StopChurn
 * Author URI: https://stopchurn.com/
 */

if (!defined('ABSPATH')) {
    exit;
}

define('STOPCHURN_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once STOPCHURN_PLUGIN_DIR . 'includes/settings.php';
require_once STOPCHURN_PLUGIN_DIR . 'includes/init.php';
require_once STOPCHURN_PLUGIN_DIR . 'includes/contactform7.php';
