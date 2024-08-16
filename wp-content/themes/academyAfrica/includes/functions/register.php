<?

function check_register_action()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) && $_POST['action'] === 'register')) {
        $user = array(
            'first_name' => $_POST['firstName'],
            'last_name' => $_POST['lastName'],
            'user_email' => $_POST['email'],
            'user_pass' => $_POST['password'],
            'user_nicename' => $_POST['firstName'] . $_POST['lastName'],
            'user_login' => $_POST['email'],
            'user_status' => 1,
        );
        $new_user = wp_insert_user($user);
        if (is_wp_error($new_user)) {
            wp_redirect(home_url('/login?action=register&error_message=' . urlencode($new_user->get_error_message())));
        } else {
            $success_message = "You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.";
            wp_redirect(home_url('/login?action=register&success=' . urlencode($success_message)));
        }
        exit;
    }
}

function activate_new_user_action()
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && ((isset($_GET['action'])) && $_GET['action'] === 'account_activation') && (isset($_GET['key'])) && (isset($_GET['user_id']))) {
        $user_id = $_GET['user_id'];
        $code = $_GET['key'];
        global $wpdb;
        $user = get_user_by('ID', $user_id);
        update_user_meta($user_id, 'account_status', "active");
        $wpdb->update(
            'wp_users',
            array('user_status' => 0),
            array(
                'ID' => $user_id,
                'user_activation_key' => $code
            ),
        );
    }
}
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
