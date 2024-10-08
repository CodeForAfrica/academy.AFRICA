<?php

namespace Academy_Africa;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


final class Plugin
{

    /**
     * Plugin Version
     *
     * @since 1.0.0
     *
     * @var string The plugin version.
     */
    const VERSION = '1.0.0';

    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     *
     * @var string Minimum Elementor version required to run the plugin.
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.15.3';

    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     *
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '8.0';

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var Plugin The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     *
     * @access public
     * @static
     *
     * @return Plugin An instance of the class.
     */
    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Constructor
     *
     * Perform some compatibility checks to make sure basic requirements are meet.
     * If all compatibility checks pass, initiali
     * @since 1.0.0
     *
     * @access public
     */
    public function __construct()
    {
        if ($this->is_compatible()) {
            add_action('elementor/init', [$this, 'init']);
        }
    }

    /**
     * Compatibility Check
     * 
     * Checks if the installed version of Elementor meets the plugin's minimum requirement.
     * 
     * @since 1.0.0
     * @access public
     */

    public function is_compatible()
    {
        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return false;
        }

        // Check if the installed version of Elementor meets the plugin's minimum requirement.
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return false;
        }

        // Check if the installed version of PHP meets the plugin's minimum requirement.
        if (!version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return false;
        }

        return true;
    }

    /**
     * Admin notice
     * 
     * Warning when the site doesn't have Elementor installed or activated.
     * 
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_missing_main_plugin()
    {
        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'academy-africa'),
            '<strong>' . esc_html__('academyAfrica Elementor Addon', 'academy-africa') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'academy-africa') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     * 
     * Warning when the site doesn't have a minimum required Elementor version.
     * 
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_elementor_version()
    {
        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'academy-africa'),
            '<strong>' . esc_html__('academyAfrica Elementor Addon', 'academy-africa') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'academy-africa') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     * 
     * Warning when the site doesn't have a minimum required PHP version.
     * 
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_php_version()
    {
        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'academy-africa'),
            '<strong>' . esc_html__('academyAfrica Elementor Addon', 'academy-africa') . '</strong>',
            '<strong>' . esc_html__('PHP', 'academy-africa') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Initialize the plugin
     * 
     * Load the plugin only after Elementor (and other plugins) are loaded.
     * 
     * @since 1.0.0
     * @access public
     */

    public function init()
    {

        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'register_widget_styles']);
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
    }

    /**
     * Include Widgets files
     * 
     * Load widgets files
     * 
     * @since 1.0.0
     * @access private
     */
    public function register_widgets($widgets_manager)
    {
        require_once(__DIR__ . '/widgets/footer.php');
        require_once(__DIR__ . '/widgets/hero.php');
        require_once(__DIR__ . '/widgets/featured_courses.php');
        require_once(__DIR__ . '/widgets/connect_and_collaborate.php');
        require_once(__DIR__ . '/widgets/feedback.php');
        require_once(__DIR__ . '/widgets/partners.php');
        require_once(__DIR__ . '/widgets/all_courses.php');
        require_once(__DIR__ . '/widgets/my_courses.php');
        require_once(__DIR__ . '/widgets/error.php');
        $widgets_manager->register(new \Academy_Africa_Footer());
        $widgets_manager->register(new \Academy_Africa_Hero());
        $widgets_manager->register(new \Academy_Africa_Featured_Courses());
        $widgets_manager->register(new \Academy_Africa_Connect_and_Collaborate());
        $widgets_manager->register(new \Academy_Africa_User_Feedback());
        $widgets_manager->register(new \Academy_Africa_Partners());
        $widgets_manager->register(new \Academy_Africa_All_Courses());
        $widgets_manager->register(new \Academy_Africa_Error());
        $widgets_manager->register(new \Academy_Africa_My_Courses());
    }

    public function register_widget_styles()
    {
        // Define an array of styles with their dependencies and file paths
        $styles = [
            'academy-africa' => 'academy-africa.css',
            'academy-africa-connect' => 'connect.css',
            'academy-africa-feedback' => 'feedback.css',
            'academy-africa-header' => 'header.css',
            'academy-africa-hero' => 'hero.css',
            'academy-africa-footer' => 'footer.css',
            'academy-africa-featured-courses' => 'featured-courses.css',
            'academy-africa-other-partners' => 'other-partners.css',
            'academy-africa-all-courses' => 'all-courses.css',
            'academy-africa-my-courses' => 'my-courses.css',
            'academy-africa-learning-pathways' => 'learning-pathways.css',
            'academy-africa-partners' => 'partners.css',
            'academy-africa-error' => 'error.css',
        ];

        // Define the base URL for the styles
        $base_url = plugins_url('assets/css/', __FILE__);

        // Enqueue the styles with their respective dependencies and file modification times
        foreach ($styles as $handle => $file) {
            $dependencies = ($handle === 'academy-africa') ? [] : ['academy-africa'];
            $file_path = plugin_dir_path(__FILE__) . 'assets/css/' . $file;
            $version = filemtime($file_path);

            wp_enqueue_style($handle, $base_url . $file, $dependencies, $version);
        }
    }

    /**
     * Add custom category
     * 
     * @since 1.0.0
     * @access public
     */
    public function add_elementor_widget_categories($elements_manager)
    {
        $elements_manager->add_category(
            'academy-africa',
            [
                'title' => __('Academy Africa', 'academy-africa'),
                'icon' => 'fa fa-plug',
            ]
        );
    }
}
