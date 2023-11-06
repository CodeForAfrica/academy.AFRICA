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
        require_once(__DIR__ . '/test_widget.php');

        $widgets_manager->register_widget_type(new \Academy_Africa_Test_Widget());
    }

    function register_styles()
    {
    }
}
