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

define('ACADEMY_AFRICA_VERSION', '1.7.10');
const MINIMUM_ELEMENTOR_VERSION = '3.16.6';


function my_theme_enqueue_styles()
{
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/assets/css/dist/main.css', array('hello-elementor', 'hello-elementor', 'hello-elementor-theme-style'), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-event', get_stylesheet_directory_uri() . '/assets/css/dist/pages/single_event.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('profile', get_stylesheet_directory_uri() . '/assets/css/dist/pages/profile.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('contact-us', get_stylesheet_directory_uri() . '/assets/css/dist/pages/contact-us.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('default-page-content', get_stylesheet_directory_uri() . '/assets/css/dist/pages/page-content.css', array(), ACADEMY_AFRICA_VERSION);
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
    wp_enqueue_style(('cfa-login'), get_stylesheet_directory_uri() . '/assets/css/dist/pages/login.css', array(), ACADEMY_AFRICA_VERSION);
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

// Prevent logged-in users from accessing login page
function redirect_logged_in_users()
{
    if (!is_admin() && is_page('login') && is_user_logged_in()) {
        $redirect_url = isset($_GET['redirect_url']) ? $_GET['redirect_url'] : home_url('/');
        wp_redirect($redirect_url);
        exit;
    }
}
add_action('template_redirect', 'redirect_logged_in_users');

function academyafrica_track_404()
{
    if (!is_404()) {
        return;
    }

    $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
    $referrer = isset($_SERVER['HTTP_REFERER']) ? wp_unslash($_SERVER['HTTP_REFERER']) : '';

    $payload = array(
        'not_found_path' => $request_uri,
        'not_found_url' => home_url($request_uri),
        'not_found_referrer' => $referrer,
        'page_title' => '404',
    );

    $payload_json = wp_json_encode($payload);

    echo "<script>
        (function() {
            var payload = {$payload_json};
            if (typeof window.gtag === 'function') {
                window.gtag('event', 'page_not_found', payload);
                return;
            }
            if (window.dataLayer && typeof window.dataLayer.push === 'function') {
                window.dataLayer.push(Object.assign({ event: 'page_not_found' }, payload));
            }
        })();
    </script>";
}
add_action('wp_footer', 'academyafrica_track_404', 20);

function generate_user_activation_key($user_id)
{
    $activation_key = wp_generate_password(32, false);
    $expiry = time() + (24 * 60 * 60); // 24 hours from now

    update_user_meta($user_id, 'account_activation_key', $activation_key);
    update_user_meta($user_id, 'activation_key_expiry', $expiry);

    return $activation_key;
}

function is_activation_key_valid($user_id, $key)
{
    $stored_key = get_user_meta($user_id, 'account_activation_key', true);
    $expiry = get_user_meta($user_id, 'activation_key_expiry', true);

    if (empty($stored_key) || empty($expiry)) {
        return false;
    }

    if (time() > $expiry) {
        delete_user_meta($user_id, 'account_activation_key');
        delete_user_meta($user_id, 'activation_key_expiry');
        return false;
    }

    return $stored_key === $key;
}

// Add verification check to authenticate filter
function verify_user_on_login($user, $username = '')
{
    if (!$user || is_wp_error($user)) {
        return $user;
    }

    $is_verified = get_user_meta($user->ID, 'is_verified', true);

    if (!$is_verified) {
        $activation_key = get_user_meta($user->ID, 'account_activation_key', true);
        if (empty($activation_key)) {
            $activation_key = generate_user_activation_key($user->ID);
        }

        send_activation_link($user->ID);

        // Store the error message in a transient
        set_transient('login_error_message', 'Please verify your account. Check your email for the verification link.', 30);

        // Redirect to custom login page
        wp_redirect(add_query_arg('verification', 'required', home_url('/login')));
        exit;
    }

    return $user;
}

// Change priority to run earlier
remove_filter('authenticate', 'verify_user_on_login', 99);
add_filter('authenticate', 'verify_user_on_login', 20);

// Additional security to prevent unauthorized access
function check_verified_user_status()
{
    $user = wp_get_current_user();
    if ($user->ID && !get_user_meta($user->ID, 'is_verified', true)) {
        wp_logout();
        wp_redirect(add_query_arg('verification', 'required', home_url('/login')));
        exit;
    }
}
add_action('init', 'check_verified_user_status');

function set_html_content_type()
{
    return 'text/html';
}

function generate_verification_token($user_id, $activation_key)
{
    $data = json_encode([
        'user_id' => $user_id,
        'activation_key' => $activation_key,
        'timestamp' => time()
    ]);
    return base64_encode($data);
}

function decode_verification_token($token)
{

    $decoded = base64_decode($token);
    if ($decoded === false) {
        return false;
    }
    $data = json_decode($decoded, true);
    $user_id = $data['user_id'];
    $activation_key = $data['activation_key'];
    $timestamp = $data['timestamp'];
    if (!$data || !isset($data['user_id']) || !isset($data['activation_key']) || !isset($data['timestamp'])) {
        return false;
    }
    return $data;
}

function send_activation_link($user_id)
{
    if ($user_id) {
        $user = get_user_by('ID', $user_id);
        $sign_in_url = home_url() . '/login';

        $activation_key = get_user_meta($user_id, 'account_activation_key', true);
        $expiry = get_user_meta($user_id, 'activation_key_expiry', true);
        if (empty($activation_key) || empty($expiry) || time() > $expiry) {
            $activation_key = generate_user_activation_key($user_id);
        }

        $token = generate_verification_token($user_id, $activation_key);

        $email = $user->data->user_email;
        $activation_link = add_query_arg(
            array(
                'action' => 'account_activation',
                'token' => $token
            ),
            $sign_in_url
        );
        add_filter('wp_mail_content_type', 'set_html_content_type');
        $body = get_registration_email_template($user_id, $activation_link);
        wp_mail($email, 'Please Verify Your academy.AFRICA Account', $body);
        remove_filter('wp_mail_content_type', 'set_html_content_type');
    }
}

function resend_verification_email()
{
    // xdebug_break();

    if (isset($_POST['action']) && $_POST['action'] === 'resend_verification' && isset($_POST['email'])) {
        $user = get_user_by('email', sanitize_email($_POST['email']));

        if ($user && !get_user_meta($user->ID, 'is_verified', true)) {
            send_activation_link($user->ID);
            set_transient('login_message_activation_email_sent', 'Verification email resent. Please check your email.', 30);
            wp_redirect(add_query_arg('email_verification_sent', 'true', home_url('/login')));
            exit;
        }

        wp_redirect(add_query_arg('email_not_found', 'true', home_url('/login')));
        set_transient('login_error_message', 'Email not found. Please check your email address.', 30);
        exit;
    }
}

add_action('init', 'resend_verification_email');

function get_registration_email_template($user_id, $activation_link)
{
    $user = get_user_by('ID', $user_id);
    $name = get_user_meta($user->data->ID, 'first_name', true) . ' ' . get_user_meta($user_id, 'last_name', true);

    $body = <<<EOD
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registration Confirmation</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #ffffff;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            h1 {
                color: #333333;
            }
            p {
                color: #666666;
            }
            .button {
                background: #004085;
                text-decoration: none;
                border: 1px solid #004085;
                margin-top: 16px;
                margin-bottom: 16px;
                padding: 8px 16px; 
                font-size: 14px; 
                line-height: 16px;
                color: #ffffff !important; 
                text-transform: uppercase;
                font-weight: 800;
                letter-spacing: 1.6px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                cursor: pointer;
                font-family: 'Open Sans', sans-serif;
                border-radius: 0;
            }
            .button:hover {
                background-color: #cce5ff;
                color:#004085 !important
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Registration Confirmation</h1>
            <p>Dear <strong>$name</strong>,</p>
            <p>Thank you for registering with <strong>academy.AFRICA!</strong> We're excited to have you on board.</p>
            <p><strong>This link is valid for 24 hours.</strong></p>
            <p>To get started, please verify your account by clicking the button below:</p>
            <a class="button" href="$activation_link" target="_blank">Verify Your Account</a>
            <p>If you did not sign up for this account, please disregard this email.</p>
            <p>Thank you,<br>The academy.AFRICA Team</p>
        </div>
    </body>
    </html>
    EOD;

    return $body;
}

function whitelist_address()
{
    return array(
        '127.0.0.1',
        '::1',
        'localhost',
        'academyafridev.wpenginepowered.com',
        'academyafristg.wpenginepowered.com'
    );
}

function set_global_error($message = "An error occured")
{
?>
    <script>
        window.error = <? echo json_encode($message) ?>
    </script>
<?
}
function render_inactive($render)
{
    wp_logout();
    if ($render) {
        set_global_error("Your account is not active. Please check your email inbox or spam folder for an activation link.");
    }
}

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
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
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
    if (!current_user_can('administrator') && !current_user_can('editor') && !current_user_can('author') && !(defined('DOING_AJAX') && DOING_AJAX)) {
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

add_action('after_setup_theme', 'hide_admin_bar');


function enqueue_my_scripts()
{
    if (is_page('profile')) {
        wp_enqueue_script('tiny_mce');
        wp_enqueue_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_my_scripts');

// Get the path to the 'inc' directory
$inc_dir = __DIR__ . '/includes/functions/';

// Check if the directory exists
$files = glob($inc_dir . '*.php');
// Include each file
foreach ($files as $file) {
    require_once $file;
}
// add_action('init', 'custom_login_page');

add_filter(
    'wpcf7_recaptcha_threshold',

    function ($threshold) {
        $threshold = 0.8;
        return $threshold;
    },
    10,
    1
);

/**
 * Register strings for Polylang translation
 */
function academyafrica_register_polylang_strings() {
    if (function_exists('pll_register_string')) {
        // Register footer strings
        pll_register_string('footer-imprint', 'Imprint', 'AcademyAfrica Footer');
        pll_register_string('footer-privacy', 'Privacy', 'AcademyAfrica Footer');

        // Register course page strings
        pll_register_string('course-download-certificate', 'Download Certificate', 'AcademyAfrica Course');
        pll_register_string('course-continue', 'Continue the Course', 'AcademyAfrica Course');
        pll_register_string('course-enroll-now', 'Enroll Now', 'AcademyAfrica Course');
        pll_register_string('course-introduction', 'Introduction', 'AcademyAfrica Course');
        pll_register_string('course-pathways-text', 'Completing this course can bring you closer to completing the following pathways', 'AcademyAfrica Course');
        pll_register_string('course-curriculum', 'Course Curriculum', 'AcademyAfrica Course');
        pll_register_string('course-instructor', 'The Instructor', 'AcademyAfrica Course');
        pll_register_string('course-organization', 'The Organization', 'AcademyAfrica Course');
        pll_register_string('course-related', 'Related', 'AcademyAfrica Course');
        pll_register_string('course-related-courses', 'Related Courses', 'AcademyAfrica Course');
    }
}
add_action('init', 'academyafrica_register_polylang_strings');
