<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Central list of Polylang strings used in template-parts and LearnDash templates.
 *
 * @return array<string, array<string, string>>
 */
function academyafrica_get_polylang_strings()
{
    return [
        'AcademyAfrica Footer' => [
            'footer-imprint' => 'Imprint',
            'footer-privacy' => 'Privacy',
        ],
        'AcademyAfrica Course' => [
            'course-completed-congratulations' => 'Congratulations',
            'course-completed-share-title' => 'Share the good news!',
            'course-completed-share-message' => "🎉 Just completed the %s on academy.Africa!\n🚀 Ready to take on new challenges and apply what I've learned.\nCheck out the course 👉🏽.",
            'course-completed-download' => 'Download',
            'course-completed-view-course' => 'View Course',
            'course-completed-head-role' => 'Head of Academy',
            'course-download-certificate' => 'Download Certificate',
            'course-continue' => 'Continue the Course',
            'course-enroll-now' => 'Enroll Now',
            'course-introduction' => 'Introduction',
            'course-pathways-text' => 'Completing this course can bring you closer to completing the following pathways',
            'course-curriculum' => 'Course Curriculum',
            'course-instructor' => 'The Instructor',
            'course-organization' => 'The Organization',
            'course-related' => 'Related',
            'course-related-courses' => 'Related Courses',
            'certificate-title' => 'CERTIFICATE OF',
            'certificate-type' => 'COMPLETION',
            'certificate-presented-to' => 'PRESENTED TO',
            'certificate-description' => 'For completing a course on',
            'course-card-by' => 'By',
            'course-price-free' => 'Free',
            'learning-print-title' => 'Take the courses',
            'share-default-message' => 'Check out this link: %s',
            'share-icon-label' => 'Share',
        ],
        'AcademyAfrica 404' => [
            '404-title' => 'PAGE NOT FOUND',
            '404-description' => 'There seems to be an error on this page. Please contact us for more details',
            '404-refresh' => 'Refresh',
            '404-home' => 'Home',
        ],
        'AcademyAfrica Filters' => [
            'filter-search-by' => 'Search By',
            'filter-filter-by' => 'Filter By',
            'filter-show-more' => 'Show More',
            'filter-close' => 'Close',
        ],
        'AcademyAfrica Auth' => [
            'auth-activation-email-sent' => 'Activation email sent successfully. Please check your email.',
            'auth-password-reset-email-sent' => 'Password reset instructions have been sent to your email. Follow the link to reset.',
            'auth-account-already-verified' => 'Your account is already verified. Please proceed to login.',
            'auth-account-activated' => 'Account activated successfully. You can now proceed to login.',
            'auth-invalid-activation-link' => 'Invalid or expired activation link. Please request a new one.',
            'auth-verification-required-description' => "Didn't receive the email? Check your spam folder or request a new verification email.",
            'auth-enter-email-address' => 'Enter your email address',
            'auth-resend-verification-email' => 'Resend Verification Email',
            'auth-welcome-back' => 'Welcome Back',
            'auth-sign-up-description' => 'Sign up to access all the features on academy.AFRICA',
            'auth-sign-in-with-google' => 'Sign in with Google',
            'auth-divider-or' => 'or',
            'auth-email-address' => 'Email Address',
            'auth-new-to' => 'New to academy.AFRICA?',
            'auth-register-now' => 'Register now',
            'auth-back' => 'Back',
            'auth-sign-in' => 'SIGN IN',
            'auth-welcome-register' => 'Welcome to Academy.AFRICA',
            'auth-sign-up-with-google' => 'Sign up with Google',
            'auth-register-success-message' => 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.  Please check your email inbox or spam folder for an activation link.',
            'auth-first-name' => 'First Name',
            'auth-last-name' => 'Last Name',
            'auth-email' => 'Email',
            'auth-password' => 'Password',
            'auth-confirm-password' => 'Confirm Password',
            'auth-sign-up' => 'SIGN UP',
            'auth-remember-me' => 'Remember me',
            'auth-already-member' => 'Already a member?',
            'auth-login-now' => 'Login now',
            'auth-passwords-do-not-match' => 'Passwords do not match',
            'auth-invalid-key' => 'Invalid Key',
            'auth-change-password' => 'Change your Password',
            'auth-enter-new-password' => 'Enter your new password below or generate one',
            'auth-save-password' => 'SAVE PASSWORD',
            'auth-password-reset-success-1' => 'Your password has been successfully reset. You can now',
            'auth-log-in' => 'log in',
            'auth-password-reset-success-2' => 'with your new password.',
            'auth-password-reset-title' => '🎉 Password Reset Request Received!',
            'auth-password-reset-description' => "We've sent you an email with instructions to reset your password. Please check your inbox for the email. If you don't see it in a few minutes, be sure to check your spam or junk folder, just in case it got filtered there.",
            'auth-didnt-get-email' => "Didn't get the email?",
            'auth-you-can' => 'You can',
            'auth-resend-reset-link' => 'resend the reset link',
            'auth-contact-support' => 'or contact our support team for further assistance.',
            'auth-reset-instructions' => 'We will send instructions to reset your password',
            'auth-submit' => 'SUBMIT',
        ],
    ];
}

/**
 * Register Polylang strings from a centralized map.
 */
function academyafrica_register_polylang_strings()
{
    if (!function_exists('pll_register_string')) {
        return;
    }

    foreach (academyafrica_get_polylang_strings() as $group => $strings) {
        foreach ($strings as $name => $string) {
            pll_register_string($name, $string, $group);
        }
    }
}
add_action('init', 'academyafrica_register_polylang_strings');

/**
 * Translate a registered Polylang string with a graceful fallback.
 *
 * @param string $string
 * @return string
 */
function academyafrica_translate($string)
{
    return function_exists('pll__') ? pll__($string) : $string;
}
