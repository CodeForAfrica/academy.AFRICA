<?php
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
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/assets/css/dist/main.css', array('hello-elementor', 'hello-elementor', 'hello-elementor-theme-style'));
}

add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function my_theme_enqueue_scripts()
{
    $js_files = glob(get_stylesheet_directory() . '/assets/js/*.js');
    foreach ($js_files as $js_file) {
        $js_file_name = basename($js_file, '.js');
        wp_enqueue_script($js_file_name, get_stylesheet_directory_uri() . '/assets/js/' . $js_file_name . '.js', [], true);
    }
}

add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');

function academy_africa_register_widgets($widgets_manager)
{
    require_once(__DIR__ . '/includes/widgets/test_widget.php');

    $widgets_manager->register_widget_type(new \Academy_Africa_Test_Widget());
}

add_action('elementor/widgets/register', 'academy_africa_register_widgets');

