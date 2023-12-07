<?php

// namespace AcademyAfrica\Theme;

// Exit if accessed directly
if(!defined('ABSPATH'))
    exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if(!function_exists('chld_thm_cfg_locale_css')):
    function chld_thm_cfg_locale_css($uri) {
        if(empty($uri) && is_rtl() && file_exists(get_template_directory().'/rtl.css'))
            $uri = get_template_directory_uri().'/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if(!function_exists('child_theme_configurator_css')):
    function child_theme_configurator_css() {
        wp_enqueue_style('chld_thm_cfg_separate', trailingslashit(get_stylesheet_directory_uri()).'ctc-style.css', array('hello-elementor', 'hello-elementor', 'hello-elementor-theme-style'));
    }
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 10);

// END ENQUEUE PARENT ACTION

define('ACADEMY_AFRICA_VERSION', '1.0.8');

function my_theme_enqueue_styles() {
    wp_enqueue_style('child-style', get_stylesheet_directory_uri().'/assets/css/dist/main.css', array('hello-elementor', 'hello-elementor', 'hello-elementor-theme-style'), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-event', get_stylesheet_directory_uri().'/assets/css/dist/pages/single_event.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('profile', get_stylesheet_directory_uri().'/assets/css/dist/pages/profile.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-courses', get_stylesheet_directory_uri().'/assets/css/dist/pages/single-sfwd-courses.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-lesson', get_stylesheet_directory_uri().'/assets/css/dist/pages/single-sfwd-lessons.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-quiz', get_stylesheet_directory_uri().'/assets/css/dist/pages/single-sfwd-quiz.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('single-topic', get_stylesheet_directory_uri().'/assets/css/dist/pages/single-sfwd-topic.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style(('sfwd-common'), get_stylesheet_directory_uri().'/assets/css/dist/pages/sfwd-common.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style(('course-completed'), get_stylesheet_directory_uri().'/assets/css/dist/pages/course-completed.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style(('single-ac-learning-path'), get_stylesheet_directory_uri().'/assets/css/dist/pages/single-ac-learning-path.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style(('search'), get_stylesheet_directory_uri() . '/assets/css/dist/pages/search.css', array(), ACADEMY_AFRICA_VERSION);
}

add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function load_admin_styles() {
    wp_enqueue_style('event-style', get_stylesheet_directory_uri().'/assets/css/dist/admin/events.css', array(), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('main-style', get_stylesheet_directory_uri().'/assets/css/dist/admin/main.css', array(), ACADEMY_AFRICA_VERSION);
}
add_action('admin_enqueue_scripts', 'load_admin_styles');

function load_fa() {
    wp_enqueue_style('load-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
}

add_action('wp_enqueue_scripts', 'load_fa');

function my_theme_enqueue_scripts() {
    $js_files = glob(get_stylesheet_directory().'/assets/js/*.js');
    foreach($js_files as $js_file) {
        $js_file_name = basename($js_file, '.js');
        wp_enqueue_script($js_file_name, get_stylesheet_directory_uri().'/assets/js/'.$js_file_name.'.js', [], ACADEMY_AFRICA_VERSION);
    }
}

function home_page() {
    return '/';
}

add_filter('login_redirect', 'home_page');
add_filter('logout_redirect', 'home_page');

add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');

// Organizations Post Type
function create_organization_post_type() {
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

function create_organization_taxonomy() {
    $labels = array(
        'name' => _x('Organizations', 'taxonomy general name'),
        'singular_name' => _x('Organization', 'taxonomy singular name'),
        // other labels
    );

    register_taxonomy(
        'organization',
        array('post', 'sfwd-courses'),
        array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'organization'),
        )
    );
}

add_action('init', 'create_organization_taxonomy', 0);

function create_learning_path_post_type() {
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
            'rewrite' => array('slug' => 'academy-africa-learning-paths'),
            'show_in_rest' => true,
            'supports' => array('title', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes')
        )
    );
}
add_action('init', 'create_learning_path_post_type');

require_once __DIR__.'/includes/widgets/widgets.php';
$widget = new \AcademyAfrica\Theme\Widget\Widget();
$widget->init();

require_once __DIR__.'/posts/events.php';
require_once __DIR__.'/posts/networks.php';
add_action('init', 'event_post_type');
add_action('init', 'create_networks_post_type');

function redirect_to_custom_profile_edit() {
    if(is_user_logged_in() && is_admin() && basename($_SERVER['PHP_SELF']) == 'profile.php') {
        $custom_profile_edit_url = home_url('/profile');
        wp_redirect($custom_profile_edit_url);
        exit();
    }
}

add_action('template_redirect', 'redirect_to_custom_profile_edit');

add_filter('get_avatar_data', 'change_avatar', 100, 2);

function change_avatar($args, $id_or_email) {
    $avatar_url = get_user_meta($id_or_email, 'avatar', true);

    $args['url'] = $avatar_url;

    return $args;
}

add_filter('acf/settings/show_admin', 'my_acf_show_admin');

function my_acf_show_admin($show) {

    return current_user_can('manage_options');
}

add_filter('authenticate', 'restrict_user_status', 20, 3);

function restrict_user_status($user, $username, $password) {
    if($user instanceof WP_User) {
        if($user->data->user_status == 0) {
            return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Your account is not active.'));
        }
    }
    return $user;
}