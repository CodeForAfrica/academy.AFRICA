<?php

?>
<div class="modal-anchor" id="register-modal">
    <label for="modal" class="modal-bg" id="register-modal-bg"></label>
    <div class="modal-content" id="register-modal-content">
        <label for="modal" onclick="closeModal('register')" class="close">
            <i class="fa fa-times" aria-hidden="true"></i>
        </label>
        <header>
            <h4 class="cfa-title">Welcome to Academy.AFRICA</h4>
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
        <form action="#">
            <label for="firstName">First Name</label>
            <input placeholder="First Name" id="firstName" type="text">
            <label for="lastName">Last Name</label>
            <input placeholder="Last Name" id="lastName" type="text">
            <label for="email">Email</label>
            <input placeholder="Email" id="email" type="email">
            <label for="password">Password</label>
            <input placeholder="Password" id="password" type="password">
            <label class="mui-checkbox">
                <input type="checkbox">
                <span class="checkmark"></span>
                Remember me
            </label>
        </form>
        <footer class="modal-footer">
            <div>
                <span>Already a member?</span><span onclick="closeModal('register'); openModal('login');"> Login now</span>
            </div>
            <button class="button primary" type="submit">Register</button>
        </footer>
    </div>
</div>