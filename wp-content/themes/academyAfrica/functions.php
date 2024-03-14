<?php

// namespace AcademyAfrica\Theme;

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

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

define('ACADEMY_AFRICA_VERSION', '1.1.36');
const MINIMUM_ELEMENTOR_VERSION = '3.16.6';


function my_theme_enqueue_styles()
{
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/assets/css/dist/main.css', array('hello-elementor', 'hello-elementor', 'hello-elementor-theme-style'), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-event', get_stylesheet_directory_uri() . '/assets/css/dist/pages/single_event.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('profile', get_stylesheet_directory_uri() . '/assets/css/dist/pages/profile.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-courses', get_stylesheet_directory_uri() . '/assets/css/dist/pages/single-sfwd-courses.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-lesson', get_stylesheet_directory_uri() . '/assets/css/dist/pages/single-sfwd-lessons.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-quiz', get_stylesheet_directory_uri() . '/assets/css/dist/pages/single-sfwd-quiz.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-topic', get_stylesheet_directory_uri() . '/assets/css/dist/pages/single-sfwd-topic.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style(('sfwd-common'), get_stylesheet_directory_uri() . '/assets/css/dist/pages/sfwd-common.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style(('course-completed'), get_stylesheet_directory_uri() . '/assets/css/dist/pages/course-completed.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style(('single-ac-learning-path'), get_stylesheet_directory_uri() . '/assets/css/dist/pages/single-ac-learning-path.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style(('search'), get_stylesheet_directory_uri() . '/assets/css/dist/pages/search.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style(('filter_bar'), get_stylesheet_directory_uri() . '/assets/css/dist/pages/filter_bar.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style(('learning_path_print'), get_stylesheet_directory_uri() . '/assets/css/dist/print/print.css', array(), ACADEMY_AFRICA_VERSION, 'print');
}

add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function load_admin_styles()
{
    wp_enqueue_style('event-style', get_stylesheet_directory_uri() . '/assets/css/dist/admin/events.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('main-style', get_stylesheet_directory_uri() . '/assets/css/dist/admin/main.css', array(), ACADEMY_AFRICA_VERSION);
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
        wp_enqueue_script($js_file_name, get_stylesheet_directory_uri() . '/assets/js/' . $js_file_name . '.js', [], ACADEMY_AFRICA_VERSION);
    }
    wp_enqueue_script("canvas", "https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js", array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_script("jsPDF", "https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js", array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_script('html2pdf', 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js', array(), ACADEMY_AFRICA_VERSION);
}

function load_swipper()
{
    wp_enqueue_script('swipperjs', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('swippercss', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), ACADEMY_AFRICA_VERSION);
}

add_action('wp_enqueue_scripts', 'load_swipper');

function home_page()
{
    return '/';
}

add_filter('login_redirect', 'home_page');
add_filter('logout_redirect', 'home_page');

add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');

// Organizations Post Type
function create_organization_post_type()
{
    register_post_type(
        'ac-organization',
        array(
            'labels' => array(
                'name' => __('Organizations'),
                'singular_name' => __('Organization')
            ),
            'public' => true,
            'has_archive' => false,
            "heirarchical" => true,
            'rewrite' => array('slug' => 'academy-africa-organizations'),
            'show_in_rest' => true,
            'supports' => array('title', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes')
        )
    );
}
add_action('init', 'create_organization_post_type');

function create_learning_path_post_type()
{
    register_post_type(
        'ac-learning-path',
        array(
            'labels' => array(
                'name' => __('Learning Paths'),
                'singular_name' => __('Learning Path')
            ),
            'public' => true,
            'has_archive' => false,
            "heirarchical" => true,
            'rewrite' => array('slug' => 'learning-pathways'),
            'show_in_rest' => true,
            'supports' => array('title', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes')
        )
    );
}
add_action('init', 'create_learning_path_post_type');

require_once __DIR__ . '/includes/widgets/widgets.php';
$widget = new \AcademyAfrica\Theme\Widget\Widget();
$widget->init();

require_once __DIR__ . '/posts/events.php';
require_once __DIR__ . '/posts/networks.php';
require_once __DIR__ . '/posts/footer.php';
add_action('init', 'event_post_type');
add_action('init', 'create_networks_post_type');
add_action('init', 'create_footer_post_type');

if (basename($_SERVER['PHP_SELF']) == 'profile.php') {
    $custom_profile_edit_url = home_url('/profile');
    wp_redirect($custom_profile_edit_url);
    exit();
}
function redirect_to_custom_profile_edit()
{
    if (basename($_SERVER['PHP_SELF']) == 'profile.php') {
        $custom_profile_edit_url = home_url('/profile');
        wp_redirect($custom_profile_edit_url);
        exit();
    }
}

add_action('template_redirect', 'redirect_to_custom_profile_edit');

add_filter('get_avatar_data', 'change_avatar', 100, 2);

function change_avatar($args, $id_or_email)
{
    $avatar_url = get_user_meta($id_or_email, 'avatar', true);

    if (!empty($avatar_url)) {
        $args['url'] = $avatar_url;
    }
    return $args;
}

add_filter('acf/settings/show_admin', 'my_acf_show_admin');

function my_acf_show_admin($show)
{

    return current_user_can('manage_options');
}

add_action('user_register', 'send_activation_link', 10, 1);

function send_activation_link($user_id)
{
    if ($user_id) {
        $user = get_user_by('ID', $user_id);
        $sign_in_url = home_url() . '#sign-in';
        $code = $user->data->user_activation_key;
        $valid_code = isset($code) ? $code : sha1($user_id . time());
        global $wpdb;
        $wpdb->update(
            'wp_users',
            array('user_activation_key' => $valid_code, 'user_status' => 1,),
            array('ID' => $user_id),
        );
        $email = $user->data->user_email;
        $activation_link = add_query_arg(array('action' => 'account_activation', 'key' => $valid_code, 'user_id' => $user_id), $sign_in_url);
?>
        <script>
            console.log(<? echo json_encode($user) ?>, <? echo json_encode($activation_link) ?>);
        </script>
        <?
        wp_mail($email, '[academy.AFRICA] Login Details', 'Activation link : ' . $activation_link);
    }
}

function whitelist_address()
{
    return array(
        '127.0.0.1',
        '::1',
        'localhost',
        'academyafridev.wpenginepowered.com'
    );
}

function restrict_user_status($user, $username, $password)
{
    if (!in_array($_SERVER['REMOTE_ADDR'], whitelist_address())) {
        return $user;
    } else {
        if ($user instanceof WP_User) {
            $account_status = get_user_meta($user->data->ID, 'account_status', true);
            if ($user->data->user_status == 1 || $account_status !== "active") {
                if (!isset($user->data->user_activation_key)) {
                    send_activation_link($user->data->ID);
                }
                return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Your account is not active.'));
            } else {
                return $user;
            }
        } else {
            return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Your account is not active.'));
        }
    }
}

function authenticate_user()
{
    $user_id = get_current_user_id();
    $user = get_user_by('ID', $user_id);
    if (!in_array($_SERVER['REMOTE_ADDR'], whitelist_address())) {
        return $user;
    } else {
        if ($user instanceof WP_User) {
            $account_status = get_user_meta($user->data->ID, 'account_status', true);
            global $whitelist;
            if (!in_array($_SERVER['REMOTE_ADDR'], whitelist_address())) {
                if ($user->data->user_status == 1 || $account_status !== "active") {
                    send_activation_link($user->data->ID);
                    wp_logout();
        ?>
                    <script>
                        window.location.reload();
                    </script>
<?
                }
            }
        } else {
            wp_logout();
        }
    }
}

add_action('init', 'authenticate_user');
add_filter('authenticate', 'restrict_user_status', 20, 3);

const required_plugins = array(
    'LearnDash' => [
        'name' => 'LearnDash',
        'min_version' => '4.9.1',
        'path' => 'sfwd-lms/sfwd_lms.php',
        'check' => 'class_exists',
        'url' => 'https://www.learndash.com/'
    ],
    'LearnDash Certificate Builder' => [
        'name' => 'LearnDash Certificate Builder',
        'min_version' => '1.0.4',
        'path' => 'learndash-certificate-builder/learndash-certificate-builder.php',
        'check' => 'class_exists',
        'url' => 'https://www.learndash.com/support/docs/add-ons/certificate-builder-add-on/'
    ],
    'LearnDash Course Grid' => [
        'name' => 'LearnDash Course Grid',
        'min_version' => '2.0.8',
        'path' => 'learndash-course-grid/learndash_course_grid.php',
        'check' => 'class_exists',
        'url' => 'https://www.learndash.com/support/docs/add-ons/course-grid/'
    ],
    'LearnDash Elementor' => [
        'name' => 'LearnDash Elementor',
        'min_version' => '1.0.4',
        'path' => 'learndash-elementor/learndash-elementor.php',
        'check' => 'class_exists',
        'url' => 'https://www.learndash.com/support/docs/add-ons/learndash-elementor-addon/'
    ],
    'Learndash Multilingual' => [
        'name' => 'Learndash Multilingual',
        'min_version' => '1.0.0',
        'path' => 'ld-multilingual/ld-multilingual.php',
        'check' => 'class_exists',
        'url' => 'https://www.learndash.com/support/docs/add-ons/compatibility/'
    ],
    'Polylang' => [
        'name' => 'Polylang',
        'min_version' => '3.5.2',
        'path' => 'polylang/polylang.php',
        'check' => 'function_exists',
        'url' => 'https://wordpress.org/plugins/polylang/'
    ],
    'WP Bakery' => [
        'name' => 'WP Bakery',
        'min_version' => '7.3',
        'path' => 'js_composer/js_composer.php',
        'check' => 'function_exists',
        'url' => 'https://wpbakery.com/'
    ],
    'Advanced Custom Fields' => [
        'name' => 'Advanced Custom Fields',
        'min_version' => '6.2.3',
        'path' => 'advanced-custom-fields/acf.php',
        'check' => 'function_exists',
        'url' => 'https://www.advancedcustomfields.com/'
    ],
    'Elementor' => [
        'name' => 'Elementor',
        'min_version' => '3.16.6',
        'path' => 'elementor/elementor.php',
        'check' => 'elementor/loaded',
        'url' => 'https://wordpress.org/plugins/elementor/'
    ],
    'Elementor Pro' => [
        'name' => 'Elementor Pro',
        'min_version' => '3.14.2',
        'path' => 'elementor-pro/elementor-pro.php',
        'check' => 'elementor_pro_load_plugin',
        'url' => 'https://elementor.com/pro/'
    ],
    'User Menus' => [
        'name' => 'User Menus',
        'min_version' => '1.3.2',
        'path' => 'user-menus/user-menus.php',
        'check' => 'class_exists',
        'url' => 'https://wordpress.org/plugins/user-menus/'
    ],
);

function check_compatibility()
{
    $is_admin = current_user_can('administrator');
    if ($is_admin) {
        foreach (required_plugins as $plugin_name => $plugin) {
            if (!is_plugin_active($plugin['path'])) {
                add_action('admin_notices', function () use ($plugin_name) {
                    admin_notice_missing_plugin($plugin_name);
                });
            } else {
                if (isset($plugin['min_version'])) {
                    $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin['path']);
                    if (version_compare($plugin_data['Version'], $plugin['min_version'], '<')) {
                        add_action('admin_notices', function () use ($plugin_name, $plugin) {
                            admin_notice_minimum_plugin_version($plugin_name, $plugin['min_version']);
                        });
                    }
                }
            }
        }
    }
}

add_action('init', 'check_compatibility');

function admin_notice_missing_plugin($plugin_name)
{
    if (isset($_GET['activate'])) unset($_GET['activate']);
    $url = required_plugins[$plugin_name]['url'];

    $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor */
        esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'academy-africa'),
        '<strong>' . esc_html__('academyAfrica Theme', 'academy-africa') . '</strong>',
        '<strong><a href="' . $url . '" target="_blank">' . esc_html__($plugin_name, 'academy-africa') . '</a></strong>'
    );

    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
}

function admin_notice_minimum_plugin_version($plugin_name, $min_version)
{
    if (isset($_GET['activate'])) unset($_GET['activate']);

    $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
        esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'academy-africa'),
        '<strong>' . esc_html__('academyAfrica Theme', 'academy-africa') . '</strong>',
        '<strong>' . esc_html__($plugin_name, 'academy-africa') . '</strong>',
        $min_version
    );

    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
}

function restrict_admin_access()
{
    if (!current_user_can('administrator') && !current_user_can('editor') && !current_user_can('author')) {
        wp_redirect(home_url());
        exit;
    }
}

add_action('admin_init', 'restrict_admin_access');

function hide_admin_bar()
{
    if (!current_user_can('administrator') && !current_user_can('editor') && !current_user_can('author')) {
        show_admin_bar(false);
    }
}
