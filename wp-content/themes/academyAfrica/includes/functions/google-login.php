<?php
/**
 * Google sign-in via the "Log in with Google" plugin (rtCamp, slug
 * `login-with-google`), which replaces Super Socializer.
 *
 * The plugin does the OAuth work: authorisation URL, CSRF state, token
 * exchange, matching existing users by verified email, and binding each account
 * to its Google ID (`google_oauth_sub`). This file adapts it to the theme:
 *
 * - renders the theme's own Google button with the plugin's authorisation URL;
 * - sets the plugin's CSRF state cookie in the browser on click (assets/js/google-login.js);
 * - lets the OAuth callback through to wp-login.php (see custom_login_page());
 * - keeps the account-verification rules for Google sign-ups;
 * - sends failed Google sign-ins back to the theme's friendly error.
 *
 * If the plugin is inactive, every helper here is a no-op and the Google button
 * is simply not rendered.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Whether the Log in with Google plugin is active and configured.
 */
function academyafrica_google_login_available()
{
    if (!function_exists('RtCamp\GoogleLogin\plugin') || !class_exists('RtCamp\GoogleLogin\Utils\Helper')) {
        return false;
    }

    try {
        $settings = \RtCamp\GoogleLogin\plugin()->container()->get('settings');
        // Settings exposes values via __get() only (no __isset()), so empty()
        // on the property itself would always be true; read them first.
        $client_id = $settings->client_id;
        $client_secret = $settings->client_secret;
        return !empty($client_id) && !empty($client_secret);
    } catch (\Throwable $e) {
        return false;
    }
}

/**
 * Where to send the user after a successful Google sign-in.
 *
 * Mirrors custom_login_page(): on the /login page honour ?redirect_url= (or
 * WordPress's redirect_to), falling back to /learning-pathways; anywhere else
 * (e.g. the header login modal) return to the current page.
 */
function academyafrica_google_login_redirect_target()
{
    $requested = '';
    if (!empty($_GET['redirect_url'])) {
        $requested = wp_unslash($_GET['redirect_url']);
    } elseif (!empty($_GET['redirect_to'])) {
        $requested = wp_unslash($_GET['redirect_to']);
    }

    $request_path = isset($_SERVER['REQUEST_URI']) ? (string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '/';
    $on_login_page = untrailingslashit($request_path) === '/login';

    if ($requested !== '') {
        $path = (string) wp_parse_url($requested, PHP_URL_PATH);
        if ($path !== '' && $path !== '/' && untrailingslashit($path) !== '/login') {
            // Accept a path or a full URL; wp_validate_redirect() rejects other hosts.
            $absolute = strpos($requested, '/') === 0 && strpos($requested, '//') !== 0 ? home_url($requested) : $requested;
            return wp_validate_redirect($absolute, home_url('/learning-pathways'));
        }
    }

    if ($on_login_page) {
        return home_url('/learning-pathways');
    }

    $current = isset($_SERVER['REQUEST_URI']) ? home_url(wp_unslash($_SERVER['REQUEST_URI'])) : home_url('/');
    return wp_validate_redirect($current, home_url('/'));
}

/**
 * Google authorisation URL carrying our post-login redirect in its state.
 *
 * On front-end pages the plugin deliberately doesn't set its state cookie
 * server-side (to keep pages cacheable); google-login.js sets it on click and
 * writes the matching token into the state.
 */
function academyafrica_google_login_url()
{
    if (!academyafrica_google_login_available()) {
        return '';
    }

    $helper = '\RtCamp\GoogleLogin\Utils\Helper';

    try {
        $helper::set_redirect_state_filter(academyafrica_google_login_redirect_target());
        $url = \RtCamp\GoogleLogin\plugin()->container()->get('gh_client')->authorization_url();
    } catch (\Throwable $e) {
        error_log('[academyafrica] Google login URL failed: ' . $e->getMessage());
        $url = '';
    } finally {
        $helper::remove_redirect_state_filter();
    }

    return $url;
}

/**
 * Render the theme's "Sign in with Google" button, or nothing if the plugin is
 * unavailable.
 *
 * @param string $label  Already-translated button label.
 * @param string $id     Optional element id.
 */
function academyafrica_google_login_button($label, $id = '')
{
    $url = academyafrica_google_login_url();
    if ($url === '') {
        return;
    }

    $cookie_name = \RtCamp\GoogleLogin\Utils\Helper::get_state_cookie_name();
    ?>
    <a class="google aa-google-login" href="<?php echo esc_url($url); ?>"
        data-cookie-name="<?php echo esc_attr($cookie_name); ?>"
        <?php echo $id !== '' ? 'id="' . esc_attr($id) . '"' : ''; ?>>
        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/icons/google.svg'); ?>" alt="Google">
        <?php echo esc_html($label); ?>
    </a>
    <?php
}

/**
 * Load the click handler that sets the plugin's CSRF state cookie, only for
 * logged-out visitors (the only ones who see the button).
 */
function academyafrica_google_login_scripts()
{
    if (is_user_logged_in() || !academyafrica_google_login_available()) {
        return;
    }

    wp_enqueue_script(
        'academyafrica-google-login',
        get_stylesheet_directory_uri() . '/assets/js/google-login.js',
        [],
        ACADEMY_AFRICA_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'academyafrica_google_login_scripts');

/**
 * Google sign-ups authenticate an already-verified email, so skip the
 * activation email for them.
 *
 * The plugin creates the user inside the `rtcamp.google_register_user` filter
 * (priority 10), and wp_insert_user() fires `user_register` (send_activation_link)
 * before `rtcamp.google_user_created`, so this must run earlier, at priority 5.
 * The filter value is passed through untouched.
 */
function academyafrica_google_suppress_activation($user)
{
    remove_action('user_register', 'send_activation_link', 10);
    return $user;
}
add_filter('rtcamp.google_register_user', 'academyafrica_google_suppress_activation', 5);

/**
 * Mark Google-authenticated accounts verified: the plugin only accepts Google
 * accounts whose email Google has verified, which proves the same thing the
 * activation link does. Covers both new sign-ups and existing (e.g. unverified
 * password) accounts signing in with Google for the first time. Runs inside
 * the plugin's `authenticate` callback, i.e. before verify_user_on_login().
 */
function academyafrica_google_mark_verified($user)
{
    $user_id = $user instanceof WP_User ? $user->ID : (int) $user;
    if ($user_id) {
        update_user_meta($user_id, 'is_verified', true);
    }
}
add_action('rtcamp.google_user_created', 'academyafrica_google_mark_verified', 10, 1);
add_action('rtcamp.google_user_logged_in', 'academyafrica_google_mark_verified', 10, 1);

/**
 * A failed Google sign-in would otherwise land on WordPress's unstyled default
 * login screen. Send it to the theme's existing social-login error instead
 * (authenticate_user() in includes/functions/login.php shows the message), and
 * log the reason for debugging.
 */
function academyafrica_google_login_failed($username, $error = null)
{
    if (!is_wp_error($error)) {
        return;
    }

    $codes = ['google_login_failed', 'expired_session'];
    if (!array_intersect($codes, $error->get_error_codes())) {
        return;
    }

    error_log('[academyafrica] Google sign-in failed: ' . $error->get_error_message());
    wp_safe_redirect(home_url('/?login_type=social'));
    exit;
}
add_action('wp_login_failed', 'academyafrica_google_login_failed', 10, 2);
