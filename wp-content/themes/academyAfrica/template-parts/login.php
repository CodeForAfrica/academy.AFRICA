<?
$error_message = get_transient('login_error_message');
$activation_email_sent = get_transient('login_message_activation_email_sent');
if ($error_message) {
    get_template_part('template-parts/message-bar', 'template', array(
        'message' => $error_message,
        'type' => 'error'
    ));
    delete_transient('login_error_message');
}
if ($activation_email_sent) {
    get_template_part('template-parts/message-bar', 'template', array(
        'message' => 'Activation email sent successfully. Please check your email.',
        'type' => 'success'
    ));
    delete_transient('login_message_activation_email_sent');
}

if (isset($_GET['email_sent'])) {
    get_template_part('template-parts/message-bar', 'template', array(
        'message' => 'Password reset instructions have been sent to your email. Follow the link to reset.',
        'type' => 'info'
    ));
} else {
    if (isset($_GET['action']) && $_GET['action'] === 'account_activation' && isset($_GET['token'])) {
        $token_data = decode_verification_token($_GET['token']);
        // xdebug_break();

        if ($token_data) {
            $is_verified = get_user_meta($token_data['user_id'], 'is_verified', true);

            if ($is_verified) {
                get_template_part('template-parts/message-bar', 'template', array(
                    'message' => 'Your account is already verified. Please proceed to login.',
                    'type' => 'info'
                ));
                wp_redirect(home_url('/'));
            } else if (is_activation_key_valid($token_data['user_id'], $token_data['activation_key'])) {
                update_user_meta($token_data['user_id'], 'is_verified', true);
                delete_user_meta($token_data['user_id'], 'account_activation_key');
                delete_user_meta($token_data['user_id'], 'activation_key_expiry');

                get_template_part('template-parts/message-bar', 'template', array(
                    'message' => 'Account activated successfully. You can now proceed to login.',
                    'type' => 'success'
                ));
            } else {
                get_template_part('template-parts/message-bar', 'template', array(
                    'message' => 'Invalid or expired activation link. Please request a new one.',
                    'type' => 'error'
                ));
            }
        } else {
            get_template_part('template-parts/message-bar', 'template', array(
                'message' => 'Invalid or expired activation link. Please request a new one.',
                'type' => 'error'
            ));
        }
    }

?>

    <div class="login">
        <?
        if (isset($_GET['verification']) && $_GET['verification'] === 'required') {
        ?>
            <div class="verification-resend">
                <p class="description">Didn't receive the email? Check your spam folder or request a new verification email.</p>
                <form method="post" action="">
                    <input type="hidden" name="action" value="resend_verification">
                    <input type="email" name="email" placeholder="Enter your email address" required>
                    <button type="submit">Resend Verification Email</button>
                </form>
            </div>
        <?php
        } else {
        ?>
            <div class="content" id="login-modal-content">
                <header>
                    <h6 style="font-size: 20px;" class="cfa-title">Welcome Back</h6>
                </header>
                <p class="subtitle">
                    Sign up to access all the features on academy.AFRICA
                </p>
                <div class="social-login">
                    <button class="google" onclick="theChampInitiateLogin(this, 'google')">
                        <img src="/wp-content/themes/academyAfrica/assets/images/icons/google.svg" alt="Google">
                        Sign in with Google
                    </button>
                    <!-- <button onclick="theChampInitiateLogin(this, 'facebook')" class="facebook">
                <img src="/wp-content/themes/academyAfrica/assets/images/icons/facebook.svg" alt="Facebook">
                Sign In with Facebook
            </button>
            <button onclick="theChampInitiateLogin(this, 'x')" class="twitter">
                <img src="/wp-content/themes/academyAfrica/assets/images/icons/twitter.svg" alt="Twitter">
                Sign In with X (formerly Twitter)
            </button> -->
                </div>
                <div class="content-divider">
                    <div></div><span>or</span>
                    <div></div>
                </div>
                <div class="error-message">
                    <p id="login_error">
                    </p>
                </div>
                <?php
                // Capture the login form output
                ob_start();

                $login_args = array('label_username' => 'Email Address');
                if (isset($_GET['redirect_url'])) {
                    $login_args['redirect'] = $_GET['redirect_url'];
                    $login_args['value_redirect_to'] = $_GET['redirect_url'];
                }
                wp_login_form($login_args);
                $form_output = ob_get_clean();

                // Get the Google Captcha shortcode output
                $captcha_output = do_shortcode('[bws_google_captcha]');

                // Find the position of the submit button
                $submit_button_pos = strpos($form_output, '<input type="submit"');

                // Insert the captcha before the submit button
                if ($submit_button_pos !== false) {
                    $form_output = substr_replace($form_output, $captcha_output, $submit_button_pos, 0);
                }

                // Echo the combined output
                echo $form_output;
                ?>
                <footer style="display: flex; justify-content: space-between;" class="modal-footers">
                    <div>
                        <span>New to academy.AFRICA? </span><a class="remember-me" href="/login?action=register" style="margin-left: 4px; font-size: 16px;">Register
                            now</a>
                    </div>
                    <a style="font-size: 14px; color: var(--primary-700, #0c1a81);" href="javascript:history.back()">Back</a>
                </footer>
            </div>
        <?
        }
        ?>
        <script>
            const btn = document.getElementById("wp-submit");
            if (btn) {
                btn.value = "SIGN IN"
            }
        </script>
    </div>
<?  }
