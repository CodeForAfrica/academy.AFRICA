<?php
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
        'message' => academyafrica_translate('Activation email sent successfully. Please check your email.'),
        'type' => 'success'
    ));
    delete_transient('login_message_activation_email_sent');
}

if (isset($_GET['email_sent'])) {
    get_template_part('template-parts/message-bar', 'template', array(
        'message' => academyafrica_translate('Password reset instructions have been sent to your email. Follow the link to reset.'),
        'type' => 'info'
    ));
} else {
    // Account activation itself is processed before output in
    // handle_account_activation() (#56); here we only render its outcome.
    if (isset($_GET['activation'])) {
        $activation = sanitize_key(wp_unslash($_GET['activation']));
        if ($activation === 'success') {
            get_template_part('template-parts/message-bar', 'template', array(
                'message' => academyafrica_translate('Account activated successfully. You can now proceed to login.'),
                'type' => 'success'
            ));
        } elseif ($activation === 'already') {
            get_template_part('template-parts/message-bar', 'template', array(
                'message' => academyafrica_translate('Your account is already verified. Please proceed to login.'),
                'type' => 'info'
            ));
        } else {
            get_template_part('template-parts/message-bar', 'template', array(
                'message' => academyafrica_translate('Invalid or expired activation link. Please request a new one.'),
                'type' => 'error'
            ));
        }
    }

?>

    <div class="login">
        <?php
        if (isset($_GET['verification']) && $_GET['verification'] === 'required') {
        ?>
            <div class="verification-resend">
                <p class="description"><?php echo esc_html(academyafrica_translate("Didn't receive the email? Check your spam folder or request a new verification email.")); ?></p>
                <form method="post" action="">
                    <input type="hidden" name="action" value="resend_verification">
                    <input type="email" name="email" placeholder="<?php echo esc_attr(academyafrica_translate('Enter your email address')); ?>" required>
                    <button type="submit" id="resend-email-verification"><?php echo esc_html(academyafrica_translate('Resend Verification Email')); ?></button>
                </form>
            </div>
        <?php
        } else {
        ?>
            <div class="content" id="login-modal-content">
                <header>
                    <h6 style="font-size: 20px;" class="cfa-title"><?php echo esc_html(academyafrica_translate('Welcome Back')); ?></h6>
                </header>
                <p class="subtitle">
                    <?php echo esc_html(academyafrica_translate('Sign up to access all the features on academy.AFRICA')); ?>
                </p>
                <div class="social-login">
                    <button class="google" onclick="theChampInitiateLogin(this, 'google')" id="google-login">
                        <img src="/wp-content/themes/academyAfrica/assets/images/icons/google.svg" alt="Google">
                        <?php echo esc_html(academyafrica_translate('Sign in with Google')); ?>
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
                    <div></div><span><?php echo esc_html(academyafrica_translate('or')); ?></span>
                    <div></div>
                </div>
                <div class="error-message">
                    <p id="login_error">
                    </p>
                </div>
                <?php
                // Capture the login form output
                ob_start();

                $login_args = array('label_username' => academyafrica_translate('Email Address'));

                // Determine where to send the user after login.
                // Priority: explicit redirect_url param → HTTP referer (if not home/login) → /learning-pathways
                if (isset($_GET['redirect_url']) && $_GET['redirect_url'] !== '') {
                    $post_login_redirect = $_GET['redirect_url'];
                } elseif (!empty($_SERVER['HTTP_REFERER'])) {
                    $referer_path = parse_url(wp_unslash($_SERVER['HTTP_REFERER']), PHP_URL_PATH) ?? '';
                    $login_path   = parse_url(home_url('/login'), PHP_URL_PATH) ?? '/login';
                    if ($referer_path !== '/' && $referer_path !== $login_path) {
                        $post_login_redirect = esc_url_raw(wp_unslash($_SERVER['HTTP_REFERER']));
                    } else {
                        $post_login_redirect = '/learning-pathways';
                    }
                } else {
                    $post_login_redirect = '/learning-pathways';
                }

                $login_args['redirect']          = $post_login_redirect;
                $login_args['value_redirect_to'] = $post_login_redirect;

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
                        <span><?php echo esc_html(academyafrica_translate('New to academy.AFRICA?')); ?> </span><a class="remember-me" href="/login?action=register" style="margin-left: 4px; font-size: 16px;"><?php echo esc_html(academyafrica_translate('Register now')); ?></a>
                    </div>
                    <a style="font-size: 14px; color: var(--primary-700, #0c1a81);" href="javascript:history.back()"><?php echo esc_html(academyafrica_translate('Back')); ?></a>
                </footer>
            </div>
        <?php
        }
        ?>
        <script>
            const btn = document.getElementById("wp-submit");
            if (btn) {
                btn.value = <?php echo wp_json_encode(academyafrica_translate('SIGN IN')); ?>;
            }

            document.getElementById("wp-submit")?.addEventListener("click", function() {
                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    'event': 'login',
                    'method': 'standard'
                });
                typeof window.gtag === 'function' && gtag('event', 'login', {
                    'event_category': 'engagement',
                    'event_label': 'standard'
                });
            });
            const loginError = document.getElementById("login_error");
            if (loginError) {
                const errorMessage = loginError.innerText;
                if (errorMessage) {
                    window.dataLayer = window.dataLayer || [];
                    dataLayer.push({
                        'event': 'login_error',
                        'error_message': errorMessage
                    });
                    typeof window.gtag === 'function' && gtag('event', 'login_error', {
                        'event_category': 'engagement',
                        'event_label': errorMessage
                    });
                }
            }
            const googleLoginBtn = document.getElementById("google-login");
            if (googleLoginBtn) {
                googleLoginBtn.addEventListener("click", function() {
                    window.dataLayer = window.dataLayer || [];
                    dataLayer.push({
                        'event': 'login',
                        'method': 'google'
                    });
                    typeof window.gtag === 'function' && gtag('event', 'login', {
                        'event_category': 'engagement',
                        'event_label': 'google'
                    });
                });
            }
            

        </script>
    </div>
<?php  }
