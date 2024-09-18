<?php
// Load custom functions
function custom_login_page()
{
    $path_name = isset($_GET['redirect_url']) ? $_GET['redirect_url'] : "/";
    $login_page = home_url("/login" . "?redirect_url=" . $path_name);
    $to_redirect = array("lostpassword");
    $reset_password_page = home_url('/login?action=lostpassword');
    $check_path = parse_url($_SERVER['REQUEST_URI'])['path'];
    check_register_action();
    activate_new_user_action();
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'lostpassword') {
        wp_redirect($reset_password_page);
        exit;
    }
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'rp') {
        if(isset($_GET["key"]) && isset($_GET["login"])){
            $reset_key = $_GET["key"];
            wp_redirect(home_url('/login?action=rp&key='.$reset_key).'&login='.$_GET["login"]);
        } else {
            $url = add_query_arg(array(
                'action' => 'lostpassword',
                'error_message' => 'Password Reset link is invalid.'
            ), home_url('/login'));
            wp_redirect(home_url($url));
        }
        exit;
    }
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['email_sent']) && $_GET['email_sent'] == '1') {
        password_reset($_POST["user_login"]);
        exit;
    }
    if ($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && (!isset($_GET['action']) || isset($to_redirect[$_GET['action']]))) {
        wp_redirect($login_page);
        exit;
    }
    if($check_path == "/wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET["wpe-login"])){
        $login = home_url("/login" . "?redirect_url=" . $path_name."&login=failed");
        wp_redirect($login);
    }
}

function add_lost_password_link()
{
    return '<a class="remember-me" href="/login?action=lostpassword">Lost Password?</a>';
}
function authenticate_user()
{
  if ( !is_user_logged_in() ) {
    if ( isset($_GET['login_type']) && $_GET['login_type'] === 'social' ) {
      set_global_error("An error occurred while signing up with Google. Please try again with your username and password.");
    }
}
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
                    render_inactive(true);
                }
            }
        } else {
            render_inactive(false);
        }
    }
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

add_action('init', 'authenticate_user');
add_filter('authenticate', 'restrict_user_status', 20, 3);
add_action('init', 'custom_login_page');
add_action('login_form_middle', 'add_lost_password_link');
?>
