<?php

// namespace AcademyAfrica\Theme;

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if (!function_exists('chld_thm_cfg_locale_css')) :
    function chld_thm_cfg_locale_css($uri)
    {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('child_theme_configurator_css')) :
    function child_theme_configurator_css()
    {
        wp_enqueue_style('chld_thm_cfg_separate', trailingslashit(get_stylesheet_directory_uri()) . 'ctc-style.css', array('hello-elementor', 'hello-elementor', 'hello-elementor-theme-style'));
    }
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 10);

// END ENQUEUE PARENT ACTION


function my_theme_enqueue_styles()
{
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/assets/css/dist/main.css', array('hello-elementor', 'hello-elementor', 'hello-elementor-theme-style'), '6.3.9');
}

add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function load_admin_styles()
{
    wp_enqueue_style('event-style', get_stylesheet_directory_uri() . '/assets/css/dist/admin/events.css', false, '6.3.9');
}
add_action('admin_enqueue_scripts', 'load_admin_styles');

function load_fa()
{
    wp_enqueue_style('load-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
}

add_action('wp_enqueue_scripts', 'load_fa');

function my_theme_enqueue_scripts()
{
    $js_files = glob(get_stylesheet_directory() . '/assets/js/*.js');
    foreach ($js_files as $js_file) {
        $js_file_name = basename($js_file, '.js');
        wp_enqueue_script($js_file_name, get_stylesheet_directory_uri() . '/assets/js/' . $js_file_name . '.js', [], '6.3.8');
    }
}

function home_page()
{
    return '/';
}

add_filter('login_redirect', 'home_page');
add_filter('logout_redirect', 'home_page');

add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');

require_once __DIR__ . '/includes/widgets/widgets.php';
$widget = new \AcademyAfrica\Theme\Widget\Widget();
$widget->init();

require_once __DIR__  . '/events.php';
add_action('init', 'event_post_type');
