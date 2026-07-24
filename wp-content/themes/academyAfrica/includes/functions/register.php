<?php

function academyafrica_register_error_redirect($message)
{
    wp_safe_redirect(home_url('/login?action=register&error_message=' . urlencode($message)));
    exit;
}

function check_register_action()
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || ($_POST['action'] ?? '') !== 'register') {
        return;
    }

    // CSRF protection.
    if (
        empty($_POST['academyafrica_register_nonce']) ||
        !wp_verify_nonce($_POST['academyafrica_register_nonce'], 'academyafrica_register')
    ) {
        academyafrica_register_error_redirect(__('Your session has expired. Please try again.', 'academyafrica'));
    }

    // Honeypot: only bots populate this field. Pretend success so we don't
    // reveal the trap.
    if (!empty($_POST['academyafrica_hp'])) {
        wp_safe_redirect(home_url('/login?action=register&success=' . urlencode(__('Please check your email to activate your account.', 'academyafrica'))));
        exit;
    }

    // Per-IP abuse throttle.
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    $throttle_key = 'aa_register_throttle_' . md5($ip);
    $attempts = (int) get_transient($throttle_key);
    if ($attempts >= 5) {
        academyafrica_register_error_redirect(__('Too many attempts. Please try again later.', 'academyafrica'));
    }
    set_transient($throttle_key, $attempts + 1, 10 * MINUTE_IN_SECONDS);

    // Sanitize and validate input.
    $first_name = isset($_POST['firstName']) ? sanitize_text_field(wp_unslash($_POST['firstName'])) : '';
    $last_name  = isset($_POST['lastName']) ? sanitize_text_field(wp_unslash($_POST['lastName'])) : '';
    $email      = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $password   = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $confirm    = isset($_POST['confirm-password']) ? (string) $_POST['confirm-password'] : '';

    if ($first_name === '' || $last_name === '' || $email === '' || $password === '') {
        academyafrica_register_error_redirect(__('Please fill in all required fields.', 'academyafrica'));
    }
    if (!is_email($email)) {
        academyafrica_register_error_redirect(__('Please enter a valid email address.', 'academyafrica'));
    }
    if (email_exists($email)) {
        academyafrica_register_error_redirect(__('An account with that email already exists.', 'academyafrica'));
    }
    if (strlen($password) < 8) {
        academyafrica_register_error_redirect(__('Password must be at least 8 characters long.', 'academyafrica'));
    }
    if ($confirm !== '' && $password !== $confirm) {
        academyafrica_register_error_redirect(__('Passwords do not match.', 'academyafrica'));
    }

    // Account starts unverified; verification is enforced via `is_verified` (#56).
    $new_user = wp_insert_user(array(
        'first_name'    => $first_name,
        'last_name'     => $last_name,
        'user_email'    => $email,
        'user_pass'     => $password,
        'user_nicename' => sanitize_title($first_name . $last_name),
        'user_login'    => $email,
    ));

    if (is_wp_error($new_user)) {
        academyafrica_register_error_redirect($new_user->get_error_message());
    }

    // Activation email is sent by the user_register hook (send_activation_link).
    delete_transient($throttle_key);
    $success_message = __('You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'academyafrica');
    wp_safe_redirect(home_url('/login?action=register&success=' . urlencode($success_message)));
    exit;
}

// Legacy activate_new_user_action() removed (#56): account activation is now
// handled by handle_account_activation() on `init`, keyed on the signed token +
// `is_verified` meta, instead of an unvalidated ?key=&user_id= wp_users write.

function academyafrica_customize_register($wp_customize)
{
    // Section for Profile Settings
    $wp_customize->add_section('profile_settings_section', array(
        'title'    => __('Profile Settings', 'academyafrica'),
        'priority' => 30,
    ));

    // Add settings and controls for each field
    $fields = array(
        'page_title' => array(
            'label' => __('Page Title', 'mytheme'),
            'default' => 'Profile Settings'
        ),
        'avatar_label' => array(
            'label' => __('Avatar Label', 'mytheme'),
            'default' => 'Change your profile image'
        ),
        'upload_text' => array(
            'label' => __('Upload Text', 'mytheme'),
            'default' => 'upload'
        ),
        'view_my_courses' => array(
            'label' => __('View My Courses', 'mytheme'),
            'default' => 'View your courses'
        ),
        'form_description' => array(
            'label' => __('Form Description', 'mytheme'),
            'default' => 'Please enter your information and indicate whether you would like it to be visible to the public.'
        ),
        'first_name_label' => array(
            'label' => __('First Name Label', 'mytheme'),
            'default' => 'First Name'
        ),
        'last_name_label' => array(
            'label' => __('Last Name Label', 'mytheme'),
            'default' => 'Last Name'
        ),
        'facebook_label' => array(
            'label' => __('Facebook Label', 'mytheme'),
            'default' => 'Facebook'
        ),
        'linked_in_label' => array(
            'label' => __('LinkedIn Label', 'mytheme'),
            'default' => 'Linked In'
        ),
        'slack_label' => array(
            'label' => __('Slack Label', 'mytheme'),
            'default' => 'Slack'
        ),
        'city_label' => array(
            'label' => __('City Label', 'mytheme'),
            'default' => 'City'
        ),
        'company_label' => array(
            'label' => __('Company Label', 'mytheme'),
            'default' => 'Company'
        ),
        'country_label' => array(
            'label' => __('Country Label', 'mytheme'),
            'default' => 'Country'
        ),
        'phone_label' => array(
            'label' => __('Phone Label', 'mytheme'),
            'default' => 'Phone'
        ),
        'email_label' => array(
            'label' => __('Email Label', 'mytheme'),
            'default' => 'Email'
        ),
        'twitter_label' => array(
            'label' => __('Twitter Label', 'mytheme'),
            'default' => 'Twitter'
        ),
        'position_label' => array(
            'label' => __('Position Label', 'mytheme'),
            'default' => 'Position'
        ),
        'bio_label' => array(
            'label' => __('Bio Label', 'mytheme'),
            'default' => 'Tell us a little about yourself'
        ),
        'mandatory_label' => array(
            'label' => __('Mandatory Label', 'mytheme'),
            'default' => 'Mandatory Fields'
        ),
        'receive_updates_label' => array(
            'label' => __('Receive Updates Label', 'mytheme'),
            'default' => 'Would you like to receive email updates about new content and events by academy.AFRICA?'
        ),
        'new_courses_label' => array(
            'label' => __('New Courses Label', 'mytheme'),
            'default' => 'New Courses'
        ),
        'new_events_label' => array(
            'label' => __('New Events Label', 'mytheme'),
            'default' => 'New Events'
        ),
        'save_changes_label' => array(
            'label' => __('Save Changes Label', 'mytheme'),
            'default' => 'Save Changes'
        ),
        'settings_title' => array(
            'label' => __('Settings Title', 'mytheme'),
            'default' => 'Network Settings'
        ),
        'settings_description' => array(
            'label' => __('Settings Description', 'mytheme'),
            'default' => 'Please indicate the networks you belong to or use the ‘Join’ button to join a network.'
        ),
        'membership_label' => array(
            'label' => __('Membership Label', 'mytheme'),
            'default' => 'I’m already a member'
        ),
    );

    foreach ($fields as $field => $data) {
        $wp_customize->add_setting($field, array(
            'default'   => $data['default'],
            'transport' => 'refresh',
        ));

        $wp_customize->add_control($field, array(
            'label'    => $data['label'],
            'section'  => 'profile_settings_section',
            'type'     => 'text',
        ));
    }
}
add_action('customize_register', 'academyafrica_customize_register');
?>
