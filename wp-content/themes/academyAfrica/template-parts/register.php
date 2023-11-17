<?php
?>
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
            <?php echo do_shortcode('[TheChamp-Login show_username="ON" style="height:40px; width: 100%"]') ?>
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
                <span>Already a member?</span><span onclick="closeModal('register'); openModal('login');"> Login now</span>
            </div>
        </footer>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) && $_POST['action'] === 'register')) {
    $user = array(
        'first_name' => $_POST['firstName'],
        'last_name' => $_POST['lastName'],
        'email' => $_POST['email'],
        'password'  => $_POST['password'],
        'nick_name' => $_POST['firstName'] . $_POST['lastName'],
        'user_login' => $_POST['email'],
    );
    $new_user = wp_insert_user($user);

    if (is_wp_error($new_user)) {
        echo $new_user->get_error_message();
    } else {
        echo "You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.";
    }
    exit;
}
?>