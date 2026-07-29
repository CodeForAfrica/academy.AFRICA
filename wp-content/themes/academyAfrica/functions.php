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
        wp_enqueue_style('chld_thm_cfg_separate', trailingslashit(get_stylesheet_directory_uri()) . 'ctc-style.css', array('hello-elementor', 'hello-elementor-theme-style'));
    }
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 10);

// END ENQUEUE PARENT ACTION

define('ACADEMY_AFRICA_VERSION', '1.7.16');
const MINIMUM_ELEMENTOR_VERSION = '3.16.6';


function my_theme_enqueue_styles()
{
    $base = get_stylesheet_directory_uri() . '/assets/css/dist/';

    // Base styles — needed on every front-end page.
    wp_enqueue_style('child-style', $base . 'main.css', array('hello-elementor', 'hello-elementor-theme-style'), ACADEMY_AFRICA_VERSION);
    wp_enqueue_style('default-page-content', $base . 'pages/page-content.css', array(), ACADEMY_AFRICA_VERSION);
    // sfwd-common styles the shared course-card component (.course-card .card),
    // which renders in the course grids on many pages (courses, my courses,
    // learning paths, home, etc.), not just LearnDash single views — so it is a
    // site-wide base style, not a page-specific bundle.
    wp_enqueue_style('sfwd-common', $base . 'pages/sfwd-common.css', array(), ACADEMY_AFRICA_VERSION);

    // Page-specific bundles — loaded only where the page actually needs them so
    // unrelated pages don't ship course/event/profile/auth CSS.
    if (is_singular('event')) {
        wp_enqueue_style('single-event', $base . 'pages/single_event.css', array(), ACADEMY_AFRICA_VERSION);
    }

    // Follow the Profile template (edit-profile.php) rather than a single slug,
    // so renamed or Polylang-translated profile pages still get the styles; keep
    // the slug as a harmless fallback.
    if (is_page_template('edit-profile.php') || is_page('profile')) {
        wp_enqueue_style('profile', $base . 'pages/profile.css', array(), ACADEMY_AFRICA_VERSION);
    }

    if (is_page_template('contact-us.php')) {
        wp_enqueue_style('contact-us', $base . 'pages/contact-us.css', array(), ACADEMY_AFRICA_VERSION);
    }

    // LearnDash single content (course, lesson, quiz, topic).
    if (is_singular('sfwd-courses')) {
        wp_enqueue_style('single-courses', $base . 'pages/single-sfwd-courses.css', array(), ACADEMY_AFRICA_VERSION);
        wp_enqueue_style('course-completed', $base . 'pages/course-completed.css', array(), ACADEMY_AFRICA_VERSION);
    }
    if (is_singular('sfwd-lessons')) {
        wp_enqueue_style('single-lesson', $base . 'pages/single-sfwd-lessons.css', array(), ACADEMY_AFRICA_VERSION);
    }
    if (is_singular('sfwd-quiz')) {
        wp_enqueue_style('single-quiz', $base . 'pages/single-sfwd-quiz.css', array(), ACADEMY_AFRICA_VERSION);
    }
    if (is_singular('sfwd-topic')) {
        wp_enqueue_style('single-topic', $base . 'pages/single-sfwd-topic.css', array(), ACADEMY_AFRICA_VERSION);
    }

    // Learning path single (+ its print stylesheet).
    if (is_singular('ac-learning-path')) {
        wp_enqueue_style('single-ac-learning-path', $base . 'pages/single-ac-learning-path.css', array(), ACADEMY_AFRICA_VERSION);
        wp_enqueue_style('learning_path_print', $base . 'print/print.css', array(), ACADEMY_AFRICA_VERSION, 'print');
    }

    if (is_search() || is_page_template('search.php')) {
        wp_enqueue_style('search', $base . 'pages/search.css', array(), ACADEMY_AFRICA_VERSION);
        // search.php renders template-parts/filter_bar.php directly (not via a
        // widget), so it needs the filter-bar stylesheet enqueued here.
        wp_enqueue_style('academy-africa-filter-bar', $base . 'pages/filter_bar.css', array(), ACADEMY_AFRICA_VERSION);
    }

    if (is_page_template('login.php')) {
        wp_enqueue_style('cfa-login', $base . 'pages/login.css', array(), ACADEMY_AFRICA_VERSION);
    }

    // The course-grid filter bar (academy-africa-filter-bar, registered in
    // includes/widgets/widgets.php) is otherwise pulled in via the All Courses,
    // My Courses, and Learning Pathways widgets' get_style_depends().
}

add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function load_admin_styles()
{
    // Only events.css exists in assets/css/dist/admin/; the previously enqueued
    // admin/main.css does not exist and produced a 404 on every admin screen.
    wp_enqueue_style('event-style', get_stylesheet_directory_uri() . '/assets/css/dist/admin/events.css', array(), ACADEMY_AFRICA_VERSION);
}
add_action('admin_enqueue_scripts', 'load_admin_styles');

function load_fa()
{
    wp_enqueue_style('load-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
}

add_action('wp_enqueue_scripts', 'load_fa');

function my_theme_enqueue_scripts()
{
    // Site-wide scripts. filters.js stays global: besides the course-grid filter
    // sidebar it provides addAccordion(), used by accordions across the site
    // (FAQ, Events, Learning Pathways, the filter bar, single course curriculum).
    $js_files = ['header', 'modal', 'search', 'filters'];
    foreach ($js_files as $js_file_name) {
        wp_enqueue_script($js_file_name, get_stylesheet_directory_uri() . '/assets/js/' . $js_file_name . '.js', [], ACADEMY_AFRICA_VERSION);
    }

    // courses.js is LearnDash single-view behaviour (enroll-button styling +
    // quiz "View Answers" relabel), not course-grid behaviour, so load it only
    // on single LearnDash content.
    if (is_singular(array('sfwd-courses', 'sfwd-lessons', 'sfwd-quiz', 'sfwd-topic'))) {
        wp_enqueue_script('courses', get_stylesheet_directory_uri() . '/assets/js/courses.js', [], ACADEMY_AFRICA_VERSION);
    }
}


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
            'hierarchical' => true,
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
            'hierarchical' => true,
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

/**
 * One-time post-type maintenance, run after the post types are registered.
 *
 * Guarded by an option so it runs once per version bump:
 *  - Migrates legacy footer posts stored under the old capitalized `Footer`
 *    key to the lowercase `footer` key (post-type keys are case-sensitive, so
 *    otherwise the footer query would never find them).
 *  - Flushes rewrite rules so the corrected `hierarchical` config and the
 *    footer slug take effect without a manual Permalinks re-save.
 */
function academyafrica_posttype_maintenance()
{
    $version = '2024-07-footer-hierarchy';
    if (get_option('academyafrica_posttype_maint_version') === $version) {
        return;
    }

    global $wpdb;
    $legacy_ids = $wpdb->get_col(
        $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", 'Footer')
    );
    // Bail without recording completion on a DB error, so a later request retries
    // instead of skipping the migration forever and leaving the footer broken.
    if ('' !== $wpdb->last_error) {
        return;
    }

    if (!empty($legacy_ids)) {
        $updated = $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->posts} SET post_type = %s WHERE post_type = %s",
                'footer',
                'Footer'
            )
        );
        if (false === $updated || '' !== $wpdb->last_error) {
            return; // leave the flag unset so the migration is retried
        }
        foreach ($legacy_ids as $legacy_id) {
            clean_post_cache((int) $legacy_id);
        }
    }

    flush_rewrite_rules(false);

    // Only now that the migration + flush have succeeded do we record completion.
    update_option('academyafrica_posttype_maint_version', $version);
}
add_action('init', 'academyafrica_posttype_maintenance', 20);

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
    static $cache = [];

    // Resolve $id_or_email to a user ID — it can be an int, email string,
    // WP_User object, or WP_Comment object (which holds the commenter's email).
    if ($id_or_email instanceof WP_User) {
        $user_id = $id_or_email->ID;
    } elseif ($id_or_email instanceof WP_Comment) {
        $user_id = (int) $id_or_email->user_id;
        if (!$user_id && !empty($id_or_email->comment_author_email)) {
            $user = get_user_by('email', $id_or_email->comment_author_email);
            $user_id = $user ? $user->ID : 0;
        }
    } elseif (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
    } else {
        $user = get_user_by('email', $id_or_email);
        $user_id = $user ? $user->ID : 0;
    }

    if (!$user_id) {
        return $args;
    }

    if (!array_key_exists($user_id, $cache)) {
        $cache[$user_id] = get_user_meta($user_id, 'avatar', true) ?: null;
    }

    if ($cache[$user_id]) {
        $args['url'] = $cache[$user_id];
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

    // Constant-time comparison to avoid leaking the key via timing.
    return hash_equals((string) $stored_key, (string) $key);
}

/**
 * Whether the current request is non-interactive (API/CLI) and therefore must
 * receive a WP_Error rather than an HTML redirect when verification fails.
 */
function academyafrica_is_non_interactive_request()
{
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return true;
    }
    if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
        return true;
    }
    if (defined('WP_CLI') && WP_CLI) {
        return true;
    }
    if (wp_doing_ajax()) {
        return true;
    }
    // Application passwords / HTTP Basic auth (e.g. over REST or XML-RPC).
    if (!empty($_SERVER['PHP_AUTH_USER']) || !empty($_SERVER['HTTP_AUTHORIZATION'])) {
        return true;
    }
    return false;
}

// `is_verified` is the single authoritative account-state flag; the legacy
// account_status/user_status path has been removed (see #56).

/**
 * Whether is_verified enforcement is active. Enforcement only turns on once the
 * one-time grandfathering migration has completed, so a deploy can never lock
 * out legitimate users (including admins) before their accounts are migrated
 * — which would otherwise deadlock, since running the migration itself requires
 * an admin to be able to log in (#56 review).
 */
function academyafrica_verification_enforced()
{
    return (bool) get_option('aa_verification_migrated_v1');
}

/**
 * Roles that are always allowed through the pre-enforcement grandfather fallback
 * so staff/admins can bootstrap and run the migration. Filterable.
 *
 * @return string[]
 */
function academyafrica_privileged_roles()
{
    return (array) apply_filters('academyafrica_privileged_roles', array('administrator', 'editor', 'author'));
}

/**
 * Narrow legacy-state fallback, consulted ONLY before enforcement is active: an
 * account the migration would grandfather (explicit legacy account_status of
 * 'active', or a privileged role) is allowed through so it isn't locked out in
 * the window before the migration runs.
 */
function academyafrica_user_is_grandfathered($user_id)
{
    if ('active' === get_user_meta($user_id, 'account_status', true)) {
        return true;
    }
    $user = get_userdata($user_id);
    return $user instanceof WP_User && (bool) array_intersect((array) $user->roles, academyafrica_privileged_roles());
}

/**
 * The single verification predicate used by every enforcement point (login,
 * session guard, application passwords) so they can never disagree.
 */
function academyafrica_user_passes_verification($user_id)
{
    if (get_user_meta($user_id, 'is_verified', true)) {
        return true;
    }
    if (!academyafrica_verification_enforced() && academyafrica_user_is_grandfathered($user_id)) {
        return true;
    }
    return false;
}

/**
 * Send the activation email at most once per cooldown window per user, so a
 * caller with valid credentials for an unverified account can't trigger mail on
 * every attempt (#56 review).
 */
function academyafrica_maybe_send_activation_link($user_id)
{
    $key = 'aa_activation_sent_' . (int) $user_id;
    if (get_transient($key)) {
        return;
    }
    set_transient($key, 1, 10 * MINUTE_IN_SECONDS);
    send_activation_link($user_id);
}

function verify_user_on_login($user, $username = '')
{
    if (!$user || is_wp_error($user) || !($user instanceof WP_User)) {
        return $user;
    }

    if (academyafrica_user_passes_verification($user->ID)) {
        return $user;
    }

    academyafrica_maybe_send_activation_link($user->ID);

    // Non-interactive clients get a proper authentication error, not a redirect.
    if (academyafrica_is_non_interactive_request()) {
        return new WP_Error(
            'account_unverified',
            __('Your account is not verified. Please use the verification link sent to your email.', 'academyafrica')
        );
    }

    set_transient('login_error_message', 'Please verify your account. Check your email for the verification link.', 30);
    wp_safe_redirect(add_query_arg('verification', 'required', home_url('/login')));
    exit;
}

add_filter('authenticate', 'verify_user_on_login', 20);

// Application-password authentication resolves the user via determine_current_user
// → wp_authenticate_application_password(), which does NOT run the `authenticate`
// chain that verify_user_on_login() is on. Enforce the same policy on that path
// so an unverified account with an app password can't authenticate (#56 review).
function academyafrica_enforce_verification_app_password($error, $user)
{
    if ($user instanceof WP_User && !academyafrica_user_passes_verification($user->ID)) {
        if (!is_wp_error($error)) {
            $error = new WP_Error();
        }
        $error->add(
            'account_unverified',
            __('Your account is not verified. Please use the verification link sent to your email.', 'academyafrica')
        );
    }
    return $error;
}
add_filter('wp_authenticate_application_password_errors', 'academyafrica_enforce_verification_app_password', 10, 2);

// Additional guard to prevent an unverified session from surviving on the front
// end (e.g. after a programmatic login). Interactive requests only, and mirrors
// the login predicate exactly.
function check_verified_user_status()
{
    if (is_admin() || academyafrica_is_non_interactive_request()) {
        return;
    }

    $user = wp_get_current_user();
    if ($user->ID && !academyafrica_user_passes_verification($user->ID)) {
        wp_logout();
        wp_safe_redirect(add_query_arg('verification', 'required', home_url('/login')));
        exit;
    }
}
add_action('init', 'check_verified_user_status');

/**
 * Handle the account-activation link before any output is sent, then redirect
 * with a status flag the login template renders. Replaces the previous logic
 * that mutated state and redirected from inside the template (after output).
 */
function handle_account_activation()
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return;
    }
    if (!isset($_GET['action'], $_GET['token']) || $_GET['action'] !== 'account_activation') {
        return;
    }

    $login_url = home_url('/login');
    $token_data = decode_verification_token(sanitize_text_field(wp_unslash($_GET['token'])));

    if (!$token_data) {
        wp_safe_redirect(add_query_arg('activation', 'invalid', $login_url));
        exit;
    }

    $user_id = absint($token_data['user_id']);

    if (get_user_meta($user_id, 'is_verified', true)) {
        wp_safe_redirect(add_query_arg('activation', 'already', $login_url));
        exit;
    }

    if (is_activation_key_valid($user_id, $token_data['activation_key'])) {
        update_user_meta($user_id, 'is_verified', true);
        delete_user_meta($user_id, 'account_activation_key');
        delete_user_meta($user_id, 'activation_key_expiry');
        wp_safe_redirect(add_query_arg('activation', 'success', $login_url));
        exit;
    }

    wp_safe_redirect(add_query_arg('activation', 'invalid', $login_url));
    exit;
}
add_action('init', 'handle_account_activation');

/**
 * One-time migration: grandfather existing users as verified so that turning on
 * `is_verified` enforcement doesn't lock anyone out.
 *
 * An account is grandfathered only on signals that carry real meaning in this
 * install: an explicit legacy account_status of 'active', or a privileged role.
 * wp_users.user_status is deliberately NOT used — wp_insert_user() never
 * persisted the `user_status => 1` that registration passed, so it is 0 for
 * every user (including never-activated ones), which would verify pending
 * accounts (#56 review).
 *
 * These signals select a small set (a few hundred), so the backfill is fast and
 * done per user (proper cache invalidation, retryable). Prefer running it via
 * WP-CLI (`wp academyafrica migrate-verification`) before enabling enforcement;
 * an admin_init fallback runs it for the first capable admin otherwise.
 *
 * @param bool $force Bypass the completed flag and stale lock (WP-CLI).
 * @return bool True on success (completion recorded); false if it did not run
 *              or failed (left retryable).
 */
function academyafrica_migrate_account_verification($force = false)
{
    if (!$force && get_option('aa_verification_migrated_v1')) {
        return true;
    }

    // Atomic lock: add_option() fails if the row already exists, so concurrent
    // runs can't both proceed (no check-then-set race). A crashed run leaves the
    // lock; clear it with the WP-CLI --force flag.
    if (!$force && false === add_option('aa_verification_migration_lock', time(), '', 'no')) {
        return false;
    }

    global $wpdb;

    // Set A — explicit legacy "active" accounts (small: a few hundred).
    $active_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
            'account_status',
            'active'
        )
    );
    if ('' !== $wpdb->last_error) {
        error_log('academyafrica verification migration: query failed: ' . $wpdb->last_error);
        delete_option('aa_verification_migration_lock');
        return false; // leave retryable; do NOT record completion
    }

    // Set B — privileged roles that must retain access.
    $priv_ids = get_users(array(
        'role__in' => academyafrica_privileged_roles(),
        'fields'   => 'ID',
        'number'   => -1,
    ));

    $ids = array_values(array_unique(array_map('intval', array_merge((array) $active_ids, (array) $priv_ids))));

    $verified = 0;
    foreach ($ids as $id) {
        if (get_user_meta($id, 'is_verified', true)) {
            continue;
        }
        // update_user_meta returns false on failure (and true/meta_id on success);
        // it also invalidates the user's meta cache for us.
        if (false !== update_user_meta($id, 'is_verified', true)) {
            $verified++;
        }
    }

    // Only record completion after the work is done.
    update_option('aa_verification_migrated_v1', time());
    update_option('aa_verification_migrated_v1_count', $verified);
    delete_option('aa_verification_migration_lock');

    return true;
}

// Fallback trigger: run once for the first capable admin if it wasn't already
// run via WP-CLI. The grandfather set is small, so this is safe on admin_init.
add_action('admin_init', function () {
    if (get_option('aa_verification_migrated_v1')) {
        return;
    }
    if (wp_doing_ajax() || wp_doing_cron() || !current_user_can('manage_options')) {
        return;
    }
    academyafrica_migrate_account_verification();
});

// Preferred, deployment-controlled path: run before enabling enforcement.
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('academyafrica migrate-verification', function ($args, $assoc_args) {
        $force = isset($assoc_args['force']);
        $ok = academyafrica_migrate_account_verification($force);
        if ($ok) {
            WP_CLI::success(sprintf(
                'Verification migration complete. Grandfathered %d user(s).',
                (int) get_option('aa_verification_migrated_v1_count')
            ));
        } else {
            WP_CLI::error('Migration did not run (locked or a query failed). Re-run with --force to clear a stale lock.');
        }
    });
}

/**
 * Social sign-ups (Google, etc.) authenticate an already-verified email, so mark
 * the account verified and suppress the standard activation email for them.
 */
function academyafrica_suppress_activation_for_social($profile_data = null)
{
    remove_action('user_register', 'send_activation_link', 10);
}
add_action('the_champ_before_registration', 'academyafrica_suppress_activation_for_social', 10, 1);

function academyafrica_mark_social_user_verified($user_id)
{
    if ($user_id) {
        update_user_meta($user_id, 'is_verified', true);
    }
}
add_action('the_champ_user_successfully_created', 'academyafrica_mark_social_user_verified', 10, 1);

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
    if (!$data || !isset($data['user_id'], $data['activation_key'], $data['timestamp'])) {
        return false;
    }

    return $data;
}

function send_activation_link($user_id)
{
    if ($user_id) {
        // Already-verified accounts never need an activation email.
        if (get_user_meta($user_id, 'is_verified', true)) {
            return;
        }
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
        window.error = <?php echo json_encode($message) ?>
    </script>
<?php
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

// ------------------------------------------------------------------
// Heartbeat optimisation
// Heartbeat defaults to every 15s. With 60+ queries per tick and
// multiple editors open simultaneously this saturates the DB.
// We slow the tick down site-wide, and slow it down further on admin
// screens that don't need frequent updates (list tables, settings,
// plugin pages, dashboard). We deliberately do NOT deregister the
// heartbeat script: wp-auth-check (the "session expired" login modal)
// and autosave declare it as a dependency, so removing the handle
// breaks those scripts on every screen where they load. Tuning the
// interval cuts the DB load while keeping the handle — and its
// dependents — intact.
// ------------------------------------------------------------------

/**
 * Post types whose edit screens genuinely need a responsive heartbeat
 * (autosave + post locking). They keep the shorter interval.
 */
function academyafrica_heartbeat_edit_post_types(): array
{
    return ['post', 'page', 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'ac-learning-path', 'ac-organization'];
}

/**
 * Whether the current admin request is a post-editor screen for one of the
 * post types that need a responsive heartbeat.
 *
 * Deliberately uses $GLOBALS['pagenow'] + the request rather than
 * get_current_screen(): the heartbeat_settings filter is applied while scripts
 * are registered, which can run before set_current_screen(), so
 * get_current_screen() may return null on the very edit screens we want to
 * keep fast. $pagenow is set early in wp-settings.php and is always available.
 */
function academyafrica_is_heartbeat_edit_screen(): bool
{
    if (!is_admin()) {
        return false;
    }

    $pagenow = $GLOBALS['pagenow'] ?? '';

    if ($pagenow === 'post-new.php') {
        $post_type = isset($_GET['post_type']) ? sanitize_key(wp_unslash($_GET['post_type'])) : 'post';
    } elseif ($pagenow === 'post.php') {
        $post_id   = isset($_GET['post']) ? absint($_GET['post']) : 0;
        $post_type = $post_id ? get_post_type($post_id) : '';
    } else {
        return false;
    }

    return in_array($post_type, academyafrica_heartbeat_edit_post_types(), true);
}

add_filter('heartbeat_settings', function (array $settings): array {
    // Editor screens keep a responsive 60s tick (autosave + post locking).
    // Every other admin screen is slowed to the core-clamped maximum (120s)
    // instead of killing heartbeat outright, so wp-auth-check and autosave
    // keep resolving. The frontend keeps the 60s default.
    if (is_admin() && !academyafrica_is_heartbeat_edit_screen()) {
        $settings['interval'] = 120; // seconds — WordPress clamps to 15–120
    } else {
        $settings['interval'] = 60;
    }

    return $settings;
});

// Fallback stub for when Co-Authors Plus plugin is disabled.
if (!function_exists('get_coauthors')) {
    function get_coauthors($post_id = 0)
    {
        $post_id = $post_id ? (int) $post_id : get_the_ID();
        $post = get_post($post_id);
        if (!$post) {
            return [];
        }
        $author = get_userdata($post->post_author);
        return $author ? [$author] : [];
    }
}

// Central object-cache helper + invalidation hooks (save/delete/enrollment/completion).
require_once __DIR__ . '/includes/utils/cache.php';

$inc_dir = __DIR__ . '/includes/functions/';
foreach (['learndash', 'login', 'password_reset', 'polylang-strings', 'register'] as $_inc) {
    require_once $inc_dir . $_inc . '.php';
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
 * Fix question marks in downloaded LearnDash certificates for non-Latin languages.
 *
 * LearnDash uses TCPDF to generate PDFs. TCPDF cannot load Google Web Fonts
 * (Open Sans, Lato, etc.) so characters from non-Latin scripts (Arabic, Amharic,
 * CJK, etc.) render as "?". This filter detects non-Latin characters in the
 * certificate content and switches to "freeserif" — a Unicode font bundled with
 * TCPDF that supports Arabic, Ethiopic, Hebrew, Cyrillic, CJK, and many others.
 */
// Per-user progress cache invalidation on lesson/topic/quiz completion is
// registered centrally in includes/utils/cache.php (loaded above).

add_filter('learndash_certificate_content', function ($cert_content, $_cert_id) {
    // Unicode ranges for scripts that standard TCPDF fonts cannot render:
    // Arabic, Hebrew, Ethiopic (Amharic), Devanagari, CJK Unified Ideographs
    $non_latin_pattern = '/[\x{0600}-\x{06FF}\x{0590}-\x{05FF}\x{1200}-\x{137F}\x{0900}-\x{097F}\x{4E00}-\x{9FFF}]/u';

    if (preg_match($non_latin_pattern, wp_strip_all_tags($cert_content))) {
        // freeserif is bundled with TCPDF and has broad Unicode glyph coverage
        $cert_content = '<style>* { font-family: freeserif !important; }</style>' . $cert_content;
    }

    return $cert_content;
}, 10, 2);
