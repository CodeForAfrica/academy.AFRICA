<?php

?>
<div class="modal-anchor" id="login-modal">
    <label for="modal" class="modal-bg" id="login-modal-bg"></label>
    <div class="modal-content" id="login-modal-content">
        <label for="modal" onclick="closeModal('login')" class="close">
            <i class="fa fa-times" aria-hidden="true"></i>
        </label>
        <header>
            <h4 class="cfa-title">Welcome Back</h4>
        </header>
        <p class="subtitle">
            Sign up to access all the features on academy.AFRICA
        </p>
        <div class="google">
            <img src="/wp-content/themes/academyAfrica/assets/images/icons/google.svg" alt="">
            <button class="sign-in-with-google">
                Sign in with Google
            </button>
        </div>
        <div class="content-divider">
            <div></div><span>or</span>
            <div></div>
        </div>
        <?php wp_login_form(); ?>
        <footer class="modal-footer">
            <div>
                <span>New to academy.Africa?</span><span onclick="closeModal('login'); openModal('register');">Register now</span>
            </div>
            <!-- <button class="button primary" type="submit">Login</button> -->
        </footer>
    </div>
</div>