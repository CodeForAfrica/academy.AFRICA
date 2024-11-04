<?
function get_url_param($param_name)
{
    // Check if the parameter exists in the URL
    if (isset($_GET[$param_name])) {
        // Retrieve the parameter and URL decode it
        $decoded_param = urldecode($_GET[$param_name]);
        return $decoded_param;
    } else {
        // Return null if the parameter is not set
        return null;
    }
}
$success = get_url_param("success");
if ($success) {
?>
    <div class="error" style="background-image: none;">
        <div class="content">
            <p class="code">

            </p>
            <p class="text">
            </p>
            <p style="max-width: 400px; margin: 0;" class="description">
                <? echo $success ?>
            </p>
            <div class="actions">
                <a class="button" href="/login">
                    SIGN IN
                </a>
                <a class="button" href="/">
                    Home
                </a>
            </div>
        </div>
    </div>
<?
} else {
?>
    <div class="login" id="register-modal">
        <div class="content" id="register-modal-content">
            <h6 style="font-size: 20px;" class="cfa-title">Welcome to Academy.AFRICA</h6>
            <p class="subtitle">
                Sign up to access all the features on academy.AFRICA
            </p>
            <div class="social-login">
                <button class="google" onclick="theChampInitiateLogin(this, 'google')">
                    <img src="/wp-content/themes/academyAfrica/assets/images/icons/google.svg" alt="Google">
                    Sign up with Google
                </button>
                <!-- <button onclick="theChampInitiateLogin(this, 'facebook')" class="facebook">
                <img src="/wp-content/themes/academyAfrica/assets/images/icons/facebook.svg" alt="Google">
                Sign In with Facebook
            </button>
            <button onclick="theChampInitiateLogin(this, 'x')" class="twitter">
                <img src="/wp-content/themes/academyAfrica/assets/images/icons/twitter.svg" alt="Google">
                Sign In with X (formerly Twitter)
            </button> -->
            </div>
            <div class="content-divider">
                <div></div><span>or</span>
                <div></div>
            </div>
            <?
            $error = get_url_param("error_message");
            if ($error) {
            ?><div class="error_message"><?
                                            echo $error;
                                            ?></div><?
                                                }
                                                    ?>
            <?
            $success_message = "You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.  Please check your email inbox or spam folder for an activation link.";
            $url = home_url('/login?action=register&success=' . urlencode($success_message));
            ?>
            <form action="<? echo $url ?>" method="post" onsubmit="return validateForm()">
                <label for="firstName">First Name</label>
                <input placeholder="First Name" name="firstName" type="text">
                <label for="lastName">Last Name</label>
                <input placeholder="Last Name" name="lastName" type="text">
                <label for="email">Email</label>
                <input placeholder="Email" name="email" type="email">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input placeholder="Password" name="password" type="password" id="password">
                    <span id="toggle-password" class="toggle-password material-icons" onclick="togglePasswordVisibility('password')">visibility_off</span>
                </div>
                <label for="confirm-password">Confirm Password</label>
                <div class="password-wrapper">
                    <input required placeholder="Password" name="confirm-password" type="password" id="confirm-password">
                    <span id="toggle-confirm-password" class="toggle-password material-icons" onclick="togglePasswordVisibility('confirm-password')">visibility_off</span>
                </div>
                <div id="error-alert" style="color: red;"></div>
                <input type="hidden" name="action" value="register">
                <? echo do_shortcode('[bws_google_captcha]') ?>
                <button class="button primary" style="width: 100%; margin: 24px 0;" type="submit">SIGN UP</button>
                <label class="mui-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Remember me
                </label>
            </form>
            <footer class="modal-footer">
                <div style="margin: 16px 0;">
                    <span>Already a member?</span><a href="/wp-login.php" class="remember-me" style="margin-left: 4px;"> Login
                        now</a>
                </div>
            </footer>
            <script>
                function validateForm() {
                    const password = document.getElementById("password").value;
                    const confirmPassword = document.getElementById("confirm-password").value;
                    if (password !== confirmPassword) {
                        const errorAlert = document.getElementById("error-alert");
                        errorAlert.innerText = "Passwords do not match";
                        return false;
                    }
                    return true;
                }
                function togglePasswordVisibility(id) {
                    const password = document.getElementById(id);
                    const togglePassword = document.getElementById(`toggle-${id}`);
                    if (password.type === "password") {
                        password.type = "text";
                        togglePassword.innerText = "visibility";
                    } else {
                        password.type = "password";
                        togglePassword.innerText = "visibility_off";
                    }
                }
            </script>
        </div>
    </div>
<?
}
?>
