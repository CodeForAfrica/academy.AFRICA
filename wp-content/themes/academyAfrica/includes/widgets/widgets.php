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
        add_action('elementor/frontend/after_register_scripts', [$this, 'register_widget_scripts']);
    }

    function register_scripts()
    {
        $scripts = [
            "academy-africa-filters" => "filters.js",
            "academy-africa-modal" => "modal.js"
        ];
        foreach ($scripts as $handle => $file) {
            wp_register_script($handle, get_stylesheet_directory_uri() . '/assets/js/' . $file, ['academy-africa'], ACADEMY_AFRICA_VERSION);
        }
    }

    function register_widget_scripts()
    {
        $js_files = glob(get_stylesheet_directory() . '/assets/js/widgets/*.js');
        foreach ($js_files as $js_file) {
            $js_file_name = basename($js_file, '.js');
            $script_name = 'academy-africa_' . $js_file_name;
            wp_register_script($script_name, get_stylesheet_directory_uri() . '/assets/js/widgets/' . $js_file_name . '.js', array(), ACADEMY_AFRICA_VERSION);
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
        require_once(__DIR__ . '/feedback.php');
        require_once(__DIR__ . '/slider.php');
        require_once(__DIR__ . '/connect_and_collaborate.php');
        require_once(__DIR__ . '/featured_courses.php');
        require_once(__DIR__ . '/partners.php');
        require_once(__DIR__ . '/my_courses.php');
        require_once(__DIR__ . '/join_our_slack.php');
        require_once(__DIR__ . '/about.php');
        require_once(__DIR__ . '/header.php');
        require_once(__DIR__ . '/events.php');
        require_once(__DIR__ . '/learning_pathways.php');

        $widgets_manager->register_widget_type(new \Academy_Africa_Hero());
        $widgets_manager->register_widget_type(new \Academy_Africa_All_Courses());
        $widgets_manager->register_widget_type(new \Academy_Africa_FAQ());
        $widgets_manager->register_widget_type(new \Academy_Africa_Slider());
        $widgets_manager->register_widget_type(new \Academy_Africa_Connect_and_Collaborate());
        $widgets_manager->register(new \Academy_Africa_User_Feedback());
        $widgets_manager->register(new \Academy_Africa_Featured_Courses());
        $widgets_manager->register(new \Academy_Africa_Partners());
        $widgets_manager->register(new \Academy_Africa_My_Courses());
        $widgets_manager->register(new \Academy_Africa_Join_Our_Slack());
        $widgets_manager->register(new \Academy_Africa_About_Section());
        $widgets_manager->register(new \Academy_Africa_Header_Section());
        $widgets_manager->register(new \Academy_Africa_Events());
        $widgets_manager->register(new \Academy_Africa_Learning_Pathways());
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
            'academy-africa-connect' => 'connect.css',
            'academy-africa-featured-courses' => 'featured_courses.css',
            'academy-africa-partners' => 'partners.css',
            'academy-africa-feedback' => 'feedback.css',
            'academy-africa-my-courses' => 'my_courses.css',
            'academy-africa-join-our-slack' => 'join_our_slack.css',
            'academy-africa-about-section' => 'about_section.css',
            'academy-africa-header-section' => 'header_section.css',
            'academy-africa-learndash-course-grid' => 'learndash_course_grid.css',
            'academy-africa-events' => 'events.css',
        ];

        foreach ($styles as $handle => $file) {
            wp_enqueue_style(
                $handle,
                get_stylesheet_directory_uri() . '/assets/css/dist/widgets/' . $file,
                [],
                "ACADEMY_AFRICA_VERSION"
            );
        }
    }
}
