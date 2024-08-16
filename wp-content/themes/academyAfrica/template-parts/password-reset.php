<?php
namespace AcademyAfrica\Theme;
get_header();
function get_full_url($path = '', $search = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'];
    $base_url = $protocol . "://" . $hostname;
    $full_url = $base_url . $path;
    if (!empty($search)) {
        $full_url .= '?' . $search;
    }
    return $full_url;
}
?>
<div style="min-height: calc(100vh - 620px);" class="login">
<?php
if ( isset($_GET['email_sent'])) {
    ?>
        <div class="login" style="min-height: calc(100vh - 550px)">
        <strong>🎉 Password Reset Request Received!</strong>
<p style="margin-top: 16px;" class="description">We've sent you an email with instructions to reset your password. Please check your inbox for the email. If you don't see it in a few minutes, be sure to check your spam or junk folder, just in case it got filtered there.
</p>
<p class="description">
Didn't get the email?
</p>
<div style="display:flex; gap: 8px">
<span class="description">You can </span> 
<form action="<? echo wp_lostpassword_url()?>" method="post">
<input type="email" placeholder="Email"  id="user_login" name="user_login" value="<? echo $_GET['user_login']?>" required hidden>
<input type="text" hidden name="pass_reset" value="pass-reset">
<button class="description" style="background: none; border: none; padding: 0; margin: 0; font: inherit; color: #0C1A81; text-decoration: none; cursor: pointer; display: inline;" 
        onmouseover="this.style.textDecoration='underline';" 
        onmouseout="this.style.textDecoration='none';">
        resend the reset link 
</button>
</div>
</form>
<span class="description">or contact our support team for further assistance.</span>

        </div>
        <?
} else {
    ?>
    <div class="content" id="login-modal-content">
        <header>
            <h6 style="font-size: 20px;" class="cfa-title">Change your Password</h6>
        </header>
        <p class="subtitle">
            We will send instructions to reset your password
        </p>
        <div class="error-message">
            <p id="login_error">
            </p>
        </div>
        <form action="<? echo wp_lostpassword_url()?>" method="post">
        <label for="email">Email</label>
            <input type="email" placeholder="Email"  id="user_login" name="user_login" required>
            <input type="text" hidden name="pass_reset" value="pass-reset">
            <button class="button primary" style="width: 100%; margin: 24px 0;" type="submit">SUBMIT</button>
        </form>
        <footer style="display: flex; justify-content: flex-end;" class="modal-footers">
            
            <!-- <button class="button primary" type="submit">Login</button> -->
            <a style="font-size: 14px; color: var(--Black, #000);" href="javascript:history.back()">Back</a>
        </footer>
    </div>
    <?
}
?>
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const error= urlParams.get('error_message');
    if(error){
        window.error = error;
    }
</script>
</div>
<?php get_footer(); ?>
