<?php

namespace AcademyAfrica\Theme\Widget;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


final class Widget
{

    public function init()
    {
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
        add_action('elementor/widgets/register', [$this,  'register_widgets']);
        add_action('wp_enqueue_scripts', [$this, 'register_styles']);
        // add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'register_scripts']);
    }

    function register_scripts()
    {
        $scripts = [
            "academy-africa-filters" => "filters.js",
        ];
        foreach ($scripts as $handle => $file) {
            wp_register_script($handle, get_stylesheet_directory_uri() . '/assets/js/' . $file, ['academy-africa']);
        }
    }

    function add_elementor_widget_categories($elements_manager)
    {
        $elements_manager->add_category(
            'academy-africa',
            [
                'title' => __('Academy Africa', 'academy-africa'),
                'icon' => 'fa fa-plug',
            ]
        );
    }

    function register_widgets($widgets_manager)
    {
        require_once(__DIR__ . '/hero.php');
        require_once(__DIR__ . '/all_courses.php');
        require_once(__DIR__ . '/faq.php');
        require_once(__DIR__ . '/slider.php');

        $widgets_manager->register_widget_type(new \Academy_Africa_Hero());
        $widgets_manager->register_widget_type(new \Academy_Africa_All_Courses());
        $widgets_manager->register_widget_type(new \Academy_Africa_FAQ());
        $widgets_manager->register_widget_type(new \Academy_Africa_Slider());
    }

    function register_styles()
    {
        $styles = [
            'academy-africa-hero' => 'hero.css',
            'academy-africa-error' => 'error.css',
            'academy-africa-all-courses' => 'all_courses.css',
            'academy-africa-learning-pathways' => 'learning_pathways.css',
            'academy-africa-faq' => 'faq.css',
            'academy-africa-slider' => 'slider.css',
        ];

        foreach ($styles as $handle => $file) {
            wp_enqueue_style(
                $handle,
                get_stylesheet_directory_uri() . '/assets/css/dist/widgets/' . $file,
                [],
                filemtime(get_stylesheet_directory() . '/assets/css/dist/widgets/' . $file)
            );
        }
    }
}
