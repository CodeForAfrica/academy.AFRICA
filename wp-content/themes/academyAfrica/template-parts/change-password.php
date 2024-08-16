
<?
$is_success = false;
if(isset($_POST["rp_key"])){
        $key = $_POST["rp_key"];
        $reset_key = sanitize_text_field($_POST['rp_key']);
        
        $user_login = $_POST["user_login"];
        $new_password = $_POST['pass1'];
        $confirm_password = $_POST['pass2'];
        if ( $new_password !== $confirm_password ) {
            set_global_error("Passwords do not match");
        }else {
            $user = check_password_reset_key($reset_key, $user_login);
            if ( is_wp_error($user) ) {
                set_global_error("Invalid Key");
            } else {
                reset_password($user, $new_password);
                $is_success = true;
            }
        }
}
?>
<div style="min-height: calc(100vh - 620px);" class="login">
<div class="content" id="login-modal-content">
        <?
        $url = home_url('/login?action=rp&key='.$_GET["key"]).'&login='.$_GET["login"];
        if(!$is_success) {
        ?>
        <header>
            <h6 style="font-size: 20px;" class="cfa-title">Change your Password</h6>
        </header>
        <p class="subtitle">
            Enter your new password below or generate one
        </p>
        <form name="resetpassform" id="resetpassform" action="<? echo $url ?>" method="post" autocomplete="off">
			<input type="hidden" id="user_login" name="user_login" value="<? echo $_GET["login"]?>" autocomplete="off">
            <label for="password">Password</label>
            <input placeholder="Password" name="pass1" type="password">
            <label for="pass2">Confirm Password</label>
            <input placeholder="Password" name="pass2" type="password">
            <input type="text" hidden name="rp_key" value="<? echo $_GET["key"]?>">
            <button class="button primary" style="width: 100%; margin: 24px 0;" type="submit">SAVE PASSWORD</button>
		</form>
        <footer style="display: flex; justify-content: flex-end;" class="modal-footers">
            
            <!-- <button class="button primary" type="submit">Login</button> -->
            <a style="font-size: 14px; color: var(--Black, #000);" href="javascript:history.back()">Back</a>
        </footer>
        <?
        }else {
            ?>
            <p>Your password has been successfully reset. You can now <a style="color: #0C1A81; text-decoration: none;" href="/login/?redirect_url=/">
        <strong>log in</strong>
        </a> with your new password.</p>
            <?
        }?>
    </div>
</div>
<?
