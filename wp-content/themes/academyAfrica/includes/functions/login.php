<?php
// Load custom functions
function custom_login_page()
{
    // Prefer our custom param, then WordPress's native redirect_to (used by
    // LearnDash, course pages, etc.), then fall back to the referer.
    $redirect_url = '';
    if (isset($_GET['redirect_url']) && $_GET['redirect_url'] !== '') {
        $redirect_url = wp_unslash($_GET['redirect_url']);
    } elseif (isset($_GET['redirect_to']) && $_GET['redirect_to'] !== '') {
        $redirect_url = wp_unslash($_GET['redirect_to']);
    }

    // Keep only the path (+ query) of the requested target so a caller cannot
    // smuggle in an external host and turn login into an open redirect.
    $parsed_url = parse_url($redirect_url);
    $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    if (!empty($parsed_url['query'])) {
        $path .= '?' . $parsed_url['query'];
    }

    $path_name = (!empty($path) && strpos($path, '/') === 0 && $path !== '/' && $path !== '/login') ? $path : "/learning-pathways";
    $login_page = add_query_arg('redirect_url', $path_name, home_url('/login'));
    $to_redirect = array("lostpassword");
    $reset_password_page = home_url('/login?action=lostpassword');
    $check_path = parse_url($_SERVER['REQUEST_URI'])['path'];
    check_register_action();
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'lostpassword') {
        wp_safe_redirect($reset_password_page);
        exit;
    }
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'rp') {
        if (isset($_GET["key"]) && isset($_GET["login"])) {
            // add_query_arg() encodes the values, producing a well-formed URL.
            $target = add_query_arg(array(
                'action' => 'rp',
                'key'    => sanitize_text_field(wp_unslash($_GET["key"])),
                'login'  => sanitize_text_field(wp_unslash($_GET["login"])),
            ), home_url('/login'));
            wp_safe_redirect($target);
        } else {
            $url = add_query_arg(array(
                'action' => 'lostpassword',
                'error_message' => 'Password Reset link is invalid.'
            ), home_url('/login'));
            wp_safe_redirect($url);
        }
        exit;
    }
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['email_sent']) && $_GET['email_sent'] == '1') {
        password_reset($_POST["user_login"]);
        exit;
    }
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && (!isset($_GET['action']) || isset($to_redirect[$_GET['action']]))) {
        wp_safe_redirect($login_page);
        exit;
    }
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET["wpe-login"])) {
        $login = add_query_arg(array(
            'redirect_url' => $path_name,
            'login'        => 'failed',
        ), home_url('/login'));
        wp_safe_redirect($login);
        exit;
    }
}

function add_lost_password_link()
{
    return '<a class="remember-me" href="/login?action=lostpassword">Lost Password?</a>';
}
// Surface a friendly error when a social (Google) sign-in fails. Account-state
// enforcement now lives solely in verify_user_on_login()/check_verified_user_status()
// keyed on the `is_verified` meta (see #56); the legacy account_status/user_status
// path has been removed.
function authenticate_user()
{
    if (!is_user_logged_in() && isset($_GET['login_type']) && $_GET['login_type'] === 'social') {
        set_global_error("An error occurred while signing up with Google. Please try again with your username and password.");
    }
}

add_action('init', 'authenticate_user');
add_action('init', 'custom_login_page');
add_action('login_form_middle', 'add_lost_password_link');
