<?php
/**
 * Plugin Name: academyAfrica Elementor Addon
 * Description: Academy Africa Website Elementor Addon
 * Version:     1.0.0
 * Author:      Code for Africa
 * Author URI:  github.com/CodeForAfrica
 * Text Domain: academy-africa
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main Elementor Test Extension Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */


// function academy_africa_scripts() {
//     wp_enqueue_style( 'academy-africa-style', plugins_url( '/assets/css/academy-africa.css', __FILE__ ) );
//     wp_enqueue_script( 'academy-africa-script', plugins_url( '/assets/js/academy-africa.js', __FILE__ ), array('jquery'), '1.0.0', true );
// }

// add_action( 'wp_enqueue_scripts', 'academy_africa_scripts' );

function register_academy_africa_widget( $widgets_manager ) {

    require_once( __DIR__ . '/widgets/academy-africa-hero.php' );

    $widgets_manager->register( new \Academy_Africa_Hero() );

}
add_action( 'elementor/widgets/register', 'register_academy_africa_widget' );