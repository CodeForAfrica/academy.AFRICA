
<div class="login">
<?
if (isset($_GET['email_sent'])) {
        echo 'Password reset instructions have been sent to your email. Follow the link to reset.';
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
            <button onclick="theChampInitiateLogin(this, 'facebook')" class="facebook">
                <img src="/wp-content/themes/academyAfrica/assets/images/icons/facebook.svg" alt="Facebook">
                Sign In with Facebook
            </button>
            <button onclick="theChampInitiateLogin(this, 'x')" class="twitter">
                <img src="/wp-content/themes/academyAfrica/assets/images/icons/twitter.svg" alt="Twitter">
                Sign In with X (formerly Twitter)
            </button>
        </div>
        <div class="content-divider">
            <div></div><span>or</span>
            <div></div>
        </div>
        <div class="error-message">
            <p id="login_error">
            </p>
        </div>
        <script>
            console.log(<? echo json_encode($_GET['redirect_url'])?>)
        </script>
        <?php wp_login_form(array('value_redirect_to' => $_GET['redirect_url'],'redirect'=> $_GET['redirect_url'])); ?>
        <footer style="display: flex; justify-content: space-between;" class="modal-footers">
            <div>
                <span>New to academy.AFRICA? </span><a class="remember-me" href="/login?action=register" style="margin-left: 4px; font-size: 16px;">Register
                    now</a>
            </div>
            <a style="font-size: 14px; color: var(--Black, #000);" href="javascript:history.back()">Back</a>
        </footer>
    </div>
    <script>
        const btn = document.getElementById("wp-submit");
        if(btn) {
            btn.value = "SIGN IN"
        }
    </script>
</div>
  <?  }
