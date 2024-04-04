<div style="min-height: calc(100vh - 620px);" class="login">
<div class="content" id="login-modal-content">
        <header>
            <h6 style="font-size: 20px;" class="cfa-title">Change your Password</h6>
        </header>
        <p class="subtitle">
            Enter your new password below or generate one
        </p>
        <form name="resetpassform" id="resetpassform" action="/wp-login.php?action=resetpass" method="post" autocomplete="off">
			<input type="hidden" id="user_login" value="" autocomplete="off">
            <label for="password">Password</label>
            <input placeholder="Password" name="pass1" type="password">
            <button class="button primary" style="width: 100%; margin: 24px 0;" type="submit">SAVE PASSWORD</button>
		</form>
        <footer style="display: flex; justify-content: flex-end;" class="modal-footers">
            
            <!-- <button class="button primary" type="submit">Login</button> -->
            <a style="font-size: 14px; color: var(--Black, #000);" href="javascript:history.back()">Back</a>
        </footer>
    </div>
</div>
