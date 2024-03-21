<?php
$user = get_user_by('ID', get_current_user_id());
$account_status = get_user_meta(get_current_user_id(), 'account_status', true);
?>
<script>
    // console.log("==============>",<? echo json_encode($account_status) ?>)
</script>
<div class="modal-anchor" id="register-modal">
    <label for="modal" class="modal-bg" id="register-modal-bg"></label>
    <div class="modal-content" id="register-modal-content">
        <label for="modal" onclick="closeModal('register')" class="close">
            <i class="fa fa-times" aria-hidden="true"></i>
        </label>
        <header>
            <h4 class="cfa-title">Welcome to Academy.AFRICA</h4>
        </header>
        <p class="subtitle">
            Sign up to access all the features on academy.AFRICA
        </p>
        <div class="social-login">
            <button class="google" onclick="theChampInitiateLogin(this, 'google')">
                <img src="/wp-content/themes/academyAfrica/assets/images/icons/google.svg" alt="Google">
                Sign in with Google
            </button>
            <button onclick="theChampInitiateLogin(this, 'facebook')" class="facebook">
                <img src="/wp-content/themes/academyAfrica/assets/images/icons/facebook.svg" alt="Google">
                Sign In with Facebook
            </button>
            <button onclick="theChampInitiateLogin(this, 'x')" class="twitter">
                <img src="/wp-content/themes/academyAfrica/assets/images/icons/twitter.svg" alt="Google">
                Sign In with X (formerly Twitter)
            </button>
        </div>
        <div class="content-divider">
            <div></div><span>or</span>
            <div></div>
        </div>
        <form action="#" method="post">
            <label for="firstName">First Name</label>
            <input placeholder="First Name" name="firstName" type="text">
            <label for="lastName">Last Name</label>
            <input placeholder="Last Name" name="lastName" type="text">
            <label for="email">Email</label>
            <input placeholder="Email" name="email" type="email">
            <label for="password">Password</label>
            <input placeholder="Password" name="password" type="password">
            <input type="hidden" name="action" value="register">
            <label class="mui-checkbox">
                <input type="checkbox">
                <span class="checkmark"></span>
                Remember me
            </label>
            <button class="button primary" type="submit">Register</button>
        </form>
        <footer class="modal-footer">
            <div>
                <span>Already a member?</span><span onclick="closeModal('register'); openModal('login');"> Login
                    now</span>
            </div>
        </footer>
    </div>
</div>

<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) && $_POST['action'] === 'register')) {
    $user = array(
        'first_name' => $_POST['firstName'],
        'last_name' => $_POST['lastName'],
        'user_email' => $_POST['email'],
        'user_pass' => $_POST['password'],
        'user_nicename' => $_POST['firstName'].$_POST['lastName'],
        'user_login' => $_POST['email'],
        'user_status' => 1,
    );
    $new_user = wp_insert_user($user);
    if(is_wp_error($new_user)) {
        echo $new_user->get_error_message();
    } else {
        echo "<br>You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.";
    }
}

if($_SERVER['REQUEST_METHOD'] === 'GET' && ((isset($_GET['action'])) && $_GET['action'] === 'account_activation') && (isset($_GET['key'])) && (isset($_GET['user_id']))) {
    $user_id = $_GET['user_id'];
    $code = $_GET['key'];
    global $wpdb;
    $user = get_user_by('ID', $user_id);
    update_user_meta($user_id, 'account_status', "active");
    $wpdb->update(
        'wp_users',
        array('user_status' => 0),
        array('ID' => $user_id,
            'user_activation_key' => $code
        ),
    );
}
?>