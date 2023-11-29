<?php
/*
Template Name: Profile
*/
get_header();
if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    $page_title = "Profile Settings";
    $avatar_label = "Change your profile image";
    $user_id = get_current_user_id();
    $avatar_url = get_avatar_url($user_id, array('size' => 100));
    $upload_text = "upload";
    $avatar_label = "Change your profile image";
    $view_my_courses = "View your courses";
    $form_description = "Please enter your information and indicate whether you would like it to be visible to the public.";
    $first_name_label = "First Name";
    $last_name_label = "Last Name";
    $facebook_label = "Facebook";
    $linked_in_label = "Linked In";
    $slack_label = " Slack";
    $city_label = "City";
    $company_label = "Company";
    $country_label = "Country";
    $phone_label = "Phone";
    $email_label = "Email";
    $twitter_label = "Twitter";
    $position_label = "Position";
    $bio_label = "Tell us a little about yourself";
    $mandatory_label = "Mandatory Fields";
    $receive_updates_label = "Would you like to receive email updates about new content and events by academy.AFRICA?";
    $new_courses_label = "New Courses";
    $new_events_label = "New Events";
    $save_changes_label = "Save Changes";
?>
    <main class="profile">
        <h4 class="cfa-title">
            <? echo $page_title ?>
        </h4>
        <form method="post" action="">
            <label for="avatar" class="avatar-label">
                <? echo $avatar_label ?>
            </label>
            <div class="avatar">
                <img src="<? echo $avatar_url ?>" alt="<?php echo esc_attr($current_user->user_firstname); ?>">
                <button class="button primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M14 10V12.6667C14 13.0203 13.8595 13.3594 13.6095 13.6095C13.3594 13.8595 13.0203 14 12.6667 14H3.33333C2.97971 14 2.64057 13.8595 2.39052 13.6095C2.14048 13.3594 2 13.0203 2 12.6667V10" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M11.3346 5.33333L8.0013 2L4.66797 5.33333" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8 2V10" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <? echo $upload_text ?>
                </button>
            </div>

            <p class="my-courses">
                <a href="/my_courses">
                    <? echo $view_my_courses ?>
                </a>
            </p>
            <p class="form-description">
                <? echo $form_description ?>
            </p>
            <div class="user-form">
                <div class="input">
                    <label for="user_firstname"><? echo $first_name_label ?></label>
                    <input type="text" name="user_firstname" id="user_firstname">
                </div>
                <div class="input">
                    <label for="user_lastname"><? echo $last_name_label ?>*</label>
                    <input type="text" name="user_lastname" id="user_lastname">
                </div>
                <div class="input">
                    <label for="user_email"><? echo $email_label ?>*</label>
                    <input type="email" name="user_email" id="user_email">
                </div>
                <div class="input">
                    <label for="phone"><? echo $phone_label ?>*</label>
                    <select style="width:96px" class="prefix" name="prefix" id="prefix">
                        <option value=""></option>
                        <?php
                        include_once __DIR__ . '/includes/utils/countries.php';
                        foreach ($all_countries as $country) {
                            $label = $country['flag'] . ' ' . $country['dial_code'];
                            $selected = $label === $country ? 'selected="selected"' : null;
                        ?>
                            <option <? echo $selected ?> value="<?php echo $country['dial_code'] ?>">
                                <?php echo $label ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                    <input type="tel" name="phone" id="phone">
                </div>
                <div class="input">
                    <label for="city"><? echo $city_label ?>*</label>
                    <input type="text" name="city" id="city">
                </div>

                <div class="input">
                    <label for="country"><? echo $country_label ?>*</label>
                    <input type="text" name="country" id="country">
                </div>
                <div class="input">
                    <label for="position"><? echo $position_label ?>*</label>
                    <input type="text" name="position" id="position">
                </div>
                <div class="input">
                    <label for="company"><? echo $company_label ?>*</label>
                    <input type="text" name="company" id="company">
                </div>

                <div class="input">
                    <label for="slack"><? echo $slack_label ?>*</label>
                    <input type="text" name="slack" id="slack">
                </div>
                <div class="input">
                    <label for="twitter"><? echo $twitter_label ?>*</label>
                    <input type="text" name="twitter" id="twitter">
                </div>
                <div class="input">
                    <label for="facebook"><? echo $facebook_label ?>*</label>
                    <input type="text" name="facebook" id="facebook">
                </div>
                <div class="input">
                    <label for="linked_in"><? echo $linked_in_label ?>*</label>
                    <input type="text" name="linked_in" id="linked_in">
                </div>
                <div class="bio">
                    <label for="bio"><? echo $bio_label ?></label>
                    <textarea name="bio" id="bio" rows="10"></textarea>
                </div>
            </div>

            <p class="mandatory">
                *<? echo $mandatory_label ?>
            </p>
            <div class="updates">
                <p class="receive-updates">
                    <? echo $receive_updates_label ?>
                </p>
                <div class="checkbox-group">
                    <ul>
                        <li>
                            <label class="mui-checkbox">
                                <input type="checkbox" name="updates[]" value="courses">
                                <span class="checkmark"></span>
                                <? echo $new_courses_label ?>
                            </label>
                        </li>
                        <li>
                            <label class="mui-checkbox">
                                <input type="checkbox" name="updates[]" value="events">
                                <span class="checkmark"></span>
                                <? echo $new_events_label ?>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="submit-area">
                <button type="submit" class="button primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                        <path d="M12.6667 14.5H3.33333C2.97971 14.5 2.64057 14.3595 2.39052 14.1095C2.14048 13.8594 2 13.5203 2 13.1667V3.83333C2 3.47971 2.14048 3.14057 2.39052 2.89052C2.64057 2.64048 2.97971 2.5 3.33333 2.5H10.6667L14 5.83333V13.1667C14 13.5203 13.8595 13.8594 13.6095 14.1095C13.3594 14.3595 13.0203 14.5 12.6667 14.5Z" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M11.3346 14.4993V9.16602H4.66797V14.4993" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4.66797 2.5V5.83333H10.0013" stroke="#EFF0FD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <? echo $save_changes_label ?>
                </button>
            </div>
        </form>
    </main>
<?php
} else {
    echo 'Please log in to edit your profile.';
}
?>

<?php get_footer(); ?>