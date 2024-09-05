<?
if (isset($_GET['email_sent'])) {
    echo 'Password reset instructions have been sent to your email. Follow the link to reset.';
} else {
    if(isset($_GET['action']) && $_GET["action"] == "account_activation"){
      ?>
      <div class="success_message text-center">Account activated successfully. You can now proceed to login</div>
      <?
    }
?>
    <div class="login">
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
            wp_login_form(array('value_redirect_to' => $_GET['redirect_url'], 'label_username' => 'Email Address', 'redirect' => $_GET['redirect_url']));
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
        <script>
            const btn = document.getElementById("wp-submit");
            if (btn) {
                btn.value = "SIGN IN"
            }
        </script>
    </div>
<?  }
