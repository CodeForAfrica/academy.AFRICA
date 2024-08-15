<?
function get_url_param($param_name) {
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
$success=get_url_param("success");
if($success){
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
                    <a class="button" href="/login" >
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
            if($error){
                ?><div class="error_message"><?
                echo $error;
                ?></div><?
            }
            ?>
        <?
        $success_message = "You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.  Please check your email inbox or spam folder for an activation link.";
        $url = home_url('/login?action=register&success=' . urlencode($success_message));
        ?>
        <form action="<? echo $url?>" method="post">
            <label for="firstName">First Name</label>
            <input placeholder="First Name" name="firstName" type="text">
            <label for="lastName">Last Name</label>
            <input placeholder="Last Name" name="lastName" type="text">
            <label for="email">Email</label>
            <input placeholder="Email" name="email" type="email">
            <label for="password">Password</label>
            <input placeholder="Password" name="password" type="password">
            <input type="hidden" name="action" value="register">
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
    </div>
</div>
    <?
}
?>

