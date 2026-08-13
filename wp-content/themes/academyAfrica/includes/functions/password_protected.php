<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WordPress' default password form ships with zero theme styling (bare
 * browser-default input/button, no wrapper), so single-course/lesson/quiz
 * pages that are password-protected render completely off-brand. This swaps
 * in a card that matches the site's design system instead.
 */
add_filter('the_password_form', 'academyafrica_password_form', 10, 3);
function academyafrica_password_form($output, $post, $invalid_password)
{
    $field_id       = 'pwbox-' . (empty($post->ID) ? wp_rand() : $post->ID);
    $action_url     = esc_url(site_url('wp-login.php?action=postpass', 'login_post'));
    $redirect_field = !empty($post->ID)
        ? sprintf('<input type="hidden" name="redirect_to" value="%s">', esc_attr(get_permalink($post->ID)))
        : '';

    $heading      = function_exists('pll__') ? pll__('This content is password protected') : 'This content is password protected';
    $description  = function_exists('pll__') ? pll__('Enter the password below to view it.') : 'Enter the password below to view it.';
    $label        = function_exists('pll__') ? pll__('Password') : 'Password';
    $button_label = function_exists('pll__') ? pll__('Unlock') : 'Unlock';

    $error_html  = '';
    $input_class = '';
    $aria        = '';
    if (!empty($invalid_password)) {
        $error_html  = '<p class="password-protected__error" id="error-' . esc_attr($field_id) . '" role="alert">' . esc_html($invalid_password) . '</p>';
        $input_class = ' has-error';
        $aria        = ' aria-describedby="error-' . esc_attr($field_id) . '"';
    }

    ob_start();
    ?>
    <div class="password-protected">
        <div class="password-protected__card">
            <span class="password-protected__icon" aria-hidden="true">
                <?php academyafrica_render_lock_icon(); ?>
            </span>
            <p class="password-protected__title"><?php echo esc_html($heading); ?></p>
            <p class="password-protected__description"><?php echo esc_html($description); ?></p>
            <?php echo $error_html; ?>
            <form action="<?php echo $action_url; ?>" class="password-protected__form post-password-form" method="post">
                <?php echo $redirect_field; ?>
                <label class="password-protected__label" for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($label); ?></label>
                <input
                    class="password-protected__input<?php echo esc_attr($input_class); ?>"
                    name="post_password"
                    id="<?php echo esc_attr($field_id); ?>"
                    type="password"
                    spellcheck="false"
                    required
                    <?php echo $aria; ?>
                >
                <button type="submit" name="Submit" class="button primary medium password-protected__submit">
                    <?php echo esc_html($button_label); ?>
                </button>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function academyafrica_render_lock_icon()
{
    ?>
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
    <?php
}

/**
 * WordPress prepends "Protected: " to the post title (visible in the <title>
 * tag and admin lists). The single-view templates already hide .entry-title,
 * so the prefix serves no purpose here and just looks unfinished.
 */
add_filter('protected_title_format', function () {
    return '%s';
});
