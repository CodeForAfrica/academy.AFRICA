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

define('ACADEMY_AFRICA_VERSION', '1.3.8');
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

function set_html_content_type() {
    return 'text/html';
}
function send_activation_link($user_id)
{
    if ($user_id) {
        $user = get_user_by('ID', $user_id);
        $sign_in_url = home_url() . '/login';
        $code = $user->data->user_activation_key;
        $valid_code = (!!$code && isset($code)) ? $code : sha1($user_id . time());
        global $wpdb;
        $wpdb->update(
            'wp_users',
            array('user_activation_key' => $valid_code, 'user_status' => 1,),
            array('ID' => $user_id),
        );
        $email = $user->data->user_email;
        $activation_link = add_query_arg(array('action' => 'account_activation', 'key' => $valid_code, 'user_id' => $user_id), $sign_in_url);
?>
        <?
        add_filter('wp_mail_content_type', 'set_html_content_type');
        $name = get_user_meta($user->data->ID, 'first_name', true).' '.get_user_meta($user_id, 'last_name', true);
        $body = "Hi <strong>".$name."</strong>,
        <p>
        Thank you for creating an account with academy.AFRICA! To get started, please verify your account by clicking the link below:</p>
            <a href=".  $activation_link .">
            <button style=\"background: #004085;
            border: 1px solid #004085;
            margin-top: 16px;
            margin-bottom: 16px;
            padding: 8px 16px; 
            font-size: 14px; 
            line-height: 16px;
            color: #ffffff; 
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1.6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            font-family: 'Open Sans', sans-serif;
            border-radius: 0;\"
            onmouseover=\"this.style.background='#cce5ff'; this.style.color='#004085';\"
            onmouseout=\"this.style.background='#004085'; this.style.color='#ffffff';\">
            Activate Your Account
            </button>
            </a>
        <p>Once your account is activated, you'll have full access to academy.AFRICA and can begin your learning journey with us.</p>
        <p>If you did not sign up for this account, please disregard this email.</p>
        <p style=\"margin-bottom: 0; margin-top: 10px\">
        Thank you,
        </p>
        <p>The academy.AFRICA Team</p>
        ";
        wp_mail($email, 'Please Verify Your academy.AFRICA Account', $body);
        remove_filter('wp_mail_content_type', 'set_html_content_type');
    }
}

function whitelist_address()
{
    return array(
        '127.0.0.1',
        '::1',
        'localhost',
        'academyafridev.wpenginepowered.com',
        'academyafristg.wpenginepowered.com',
        'academy.africa'
    );
}

function restrict_user_status($user, $username, $password)
{
    if (in_array($_SERVER['REMOTE_ADDR'], whitelist_address())) {
        return $user;
    } else {
        if ($user instanceof WP_User) {
            $account_status = get_user_meta($user->data->ID, 'account_status', true);
            if ($user->data->user_status == 1 || $account_status !== "active") {
                if (!isset($user->data->user_activation_key)) {
                    send_activation_link($user->data->ID);
                }
                wp_logout();
                return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Your account is not active.'));
            } else {
                return $user;
            }
        } else {
            wp_logout();
            return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Your account is not active.'));
        }
    }
}

function render_inactive($render){
    wp_logout();
    if($render){
        ?>
        <script>
            window.error = "Your account is not active. Please check your email inbox or spam folder for an activation link."
        </script>
    <?
    }
}

function authenticate_user()
{
    $user_id = get_current_user_id();
    $user = get_user_by('ID', $user_id);
    if (in_array($_SERVER['REMOTE_ADDR'], whitelist_address())) {
        return $user;
    } else {
        if ($user instanceof WP_User) {
            $account_status = get_user_meta($user->data->ID, 'account_status', true);
            global $whitelist;
            if (!in_array($_SERVER['REMOTE_ADDR'], whitelist_address())) {
                if ($user->data->user_status == 1 || $account_status !== "active") {
                    send_activation_link($user->data->ID);
                    render_inactive(true);
                }
            }
        } else {
            render_inactive(false);
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

add_action('after_setup_theme', 'hide_admin_bar');

function check_password_reset_action()
{
    if (isset($_POST['pass_reset'])) {
        ?>
        <div>Password reset instructions have been sent to your email address.</div>
<?
    }
}

function check_register_action()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) && $_POST['action'] === 'register')) {
        $user = array(
            'first_name' => $_POST['firstName'],
            'last_name' => $_POST['lastName'],
            'user_email' => $_POST['email'],
            'user_pass' => $_POST['password'],
            'user_nicename' => $_POST['firstName'] . $_POST['lastName'],
            'user_login' => $_POST['email'],
            'user_status' => 1,
        );
        $new_user = wp_insert_user($user);
        if (is_wp_error($new_user)) {
            wp_redirect(home_url('/login?action=register&error_message=' . urlencode($new_user->get_error_message())));
        } else {
            $success_message = "You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.";
            wp_redirect(home_url('/login?action=register&success=' . urlencode($success_message)));
        }
        exit;
    }
}

function activate_new_user_action()
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && ((isset($_GET['action'])) && $_GET['action'] === 'account_activation') && (isset($_GET['key'])) && (isset($_GET['user_id']))) {
        $user_id = $_GET['user_id'];
        $code = $_GET['key'];
        global $wpdb;
        $user = get_user_by('ID', $user_id);
        update_user_meta($user_id, 'account_status', "active");
        $wpdb->update(
            'wp_users',
            array('user_status' => 0),
            array(
                'ID' => $user_id,
                'user_activation_key' => $code
            ),
        );
    }
}

function custom_login_page()
{
    $path_name = isset($_GET['redirect_url']) ? $_GET['redirect_url'] : "/";
    $login_page = home_url("/login" . "?redirect_url=" . $path_name);
    $to_redirect = array("lostpassword");
    $reset_password_page = home_url('/login?action=lostpassword');
    $check_path = parse_url($_SERVER['REQUEST_URI'])['path'];
    check_password_reset_action();
    check_register_action();
    activate_new_user_action();
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'lostpassword') {
        wp_redirect($reset_password_page);
        exit;
    }
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && (!isset($_GET['action']) || isset($to_redirect[$_GET['action']]))) {
        wp_redirect($login_page);
        exit;
    }
}

add_action('init', 'custom_login_page');
add_action('login_form_middle', 'add_lost_password_link');
function add_lost_password_link()
{
    return '<a class="remember-me" href="/login?action=lostpassword">Lost Password?</a>';
}

function enqueue_my_scripts()
{
    if (is_page('profile')) { // Replace 'my-page' with the slug of your page
        wp_enqueue_script('tiny_mce');
        wp_enqueue_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_my_scripts');
function fix_wpelogin($url)
{
    $url = add_query_arg('wpe-login', true, $url);
    $url = add_query_arg('email_sent', true, $url);
    return $url;
}
add_filter('lostpassword_url', 'fix_wpelogin');

function academyafrica_customize_register($wp_customize)
{
    // Section for Profile Settings
    $wp_customize->add_section('profile_settings_section', array(
        'title'    => __('Profile Settings', 'academyafrica'),
        'priority' => 30,
    ));

    // Add settings and controls for each field
    $fields = array(
        'page_title' => array(
            'label' => __('Page Title', 'mytheme'),
            'default' => 'Profile Settings'
        ),
        'avatar_label' => array(
            'label' => __('Avatar Label', 'mytheme'),
            'default' => 'Change your profile image'
        ),
        'upload_text' => array(
            'label' => __('Upload Text', 'mytheme'),
            'default' => 'upload'
        ),
        'view_my_courses' => array(
            'label' => __('View My Courses', 'mytheme'),
            'default' => 'View your courses'
        ),
        'form_description' => array(
            'label' => __('Form Description', 'mytheme'),
            'default' => 'Please enter your information and indicate whether you would like it to be visible to the public.'
        ),
        'first_name_label' => array(
            'label' => __('First Name Label', 'mytheme'),
            'default' => 'First Name'
        ),
        'last_name_label' => array(
            'label' => __('Last Name Label', 'mytheme'),
            'default' => 'Last Name'
        ),
        'facebook_label' => array(
            'label' => __('Facebook Label', 'mytheme'),
            'default' => 'Facebook'
        ),
        'linked_in_label' => array(
            'label' => __('LinkedIn Label', 'mytheme'),
            'default' => 'Linked In'
        ),
        'slack_label' => array(
            'label' => __('Slack Label', 'mytheme'),
            'default' => 'Slack'
        ),
        'city_label' => array(
            'label' => __('City Label', 'mytheme'),
            'default' => 'City'
        ),
        'company_label' => array(
            'label' => __('Company Label', 'mytheme'),
            'default' => 'Company'
        ),
        'country_label' => array(
            'label' => __('Country Label', 'mytheme'),
            'default' => 'Country'
        ),
        'phone_label' => array(
            'label' => __('Phone Label', 'mytheme'),
            'default' => 'Phone'
        ),
        'email_label' => array(
            'label' => __('Email Label', 'mytheme'),
            'default' => 'Email'
        ),
        'twitter_label' => array(
            'label' => __('Twitter Label', 'mytheme'),
            'default' => 'Twitter'
        ),
        'position_label' => array(
            'label' => __('Position Label', 'mytheme'),
            'default' => 'Position'
        ),
        'bio_label' => array(
            'label' => __('Bio Label', 'mytheme'),
            'default' => 'Tell us a little about yourself'
        ),
        'mandatory_label' => array(
            'label' => __('Mandatory Label', 'mytheme'),
            'default' => 'Mandatory Fields'
        ),
        'receive_updates_label' => array(
            'label' => __('Receive Updates Label', 'mytheme'),
            'default' => 'Would you like to receive email updates about new content and events by academy.AFRICA?'
        ),
        'new_courses_label' => array(
            'label' => __('New Courses Label', 'mytheme'),
            'default' => 'New Courses'
        ),
        'new_events_label' => array(
            'label' => __('New Events Label', 'mytheme'),
            'default' => 'New Events'
        ),
        'save_changes_label' => array(
            'label' => __('Save Changes Label', 'mytheme'),
            'default' => 'Save Changes'
        ),
        'settings_title' => array(
            'label' => __('Settings Title', 'mytheme'),
            'default' => 'Network Settings'
        ),
        'settings_description' => array(
            'label' => __('Settings Description', 'mytheme'),
            'default' => 'Please indicate the networks you belong to or use the ‘Join’ button to join a network.'
        ),
        'membership_label' => array(
            'label' => __('Membership Label', 'mytheme'),
            'default' => 'I’m already a member'
        ),
    );

    foreach ($fields as $field => $data) {
        $wp_customize->add_setting($field, array(
            'default'   => $data['default'],
            'transport' => 'refresh',
        ));

        $wp_customize->add_control($field, array(
            'label'    => $data['label'],
            'section'  => 'profile_settings_section',
            'type'     => 'text',
        ));
    }
}
add_action('customize_register', 'academyafrica_customize_register');

function auto_confirm_admin_email($new_email) {
    // Confirm the email change programmatically
    if (get_option('admin_email') !== $new_email) {
        update_option('admin_email', $new_email);
    }
    return $new_email;
}
add_filter('pre_update_option_admin_email', 'auto_confirm_admin_email');
