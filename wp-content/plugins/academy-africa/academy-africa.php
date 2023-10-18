<?php

/**
 * Plugin Name: Academy Africa
 * Description: This plugin provides custom widgets for the Academy Africa website.
 * Version:     1.0.0
 * Author:      Code for Africa
 * Author URI:  github.com/CodeForAfrica
 * Text Domain: academy-africa
 */


if (!defined('ABSPATH')) exit; // Exit if accessed directly


// Define Academy Africa Plugin Constants

if (!defined('ACADEMY_AFRICA_PLUGIN_VERSION')) {
    define('ACADEMY_AFRICA_PLUGIN_VERSION', '1.0.0');
}

if (!defined('ACADEMY_AFRICA_PLUGIN_DIR')) {
    define('ACADEMY_AFRICA_PLUGIN_DIR', dirname(__FILE__));
}

/**
 * Main Elementor Test Extension Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */



function academy_africa_plugin($widgets_manager)
{

    require_once(__DIR__ . '/includes/plugin.php');

    // Run the plugin
    \Academy_Africa\Plugin::instance();
}
add_action('plugins_loaded', 'academy_africa_plugin');
