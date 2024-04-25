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
if ( isset($_POST['pass_reset'])) {
    ?>
    <div>Password reset instructions have been sent to your email.</div>
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
</div>
<?php get_footer(); ?>
