<?php
/*
Template Name: Profile
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) && $_POST['action'] === 'profile')) {
    $user_id = get_current_user_id();
    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] == 0) {
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['path'];
        $uploads_url = $upload_dir['url'];
        $file_name = $_FILES["avatar"]["name"];
        $target_file = $target_dir . '/' . $file_name;
        $moved = move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file);
        if ($moved) {
            $new_avatar_url = $uploads_url . '/' . $file_name;
            update_user_meta($user_id, 'show_avatars', 1);
            add_user_meta($user_id, 'wp_user_avatar', $new_avatar_url);
            update_user_meta($user_id, 'avatar', $new_avatar_url);
        }
    }
    $keys = array("first_name", "last_name", "user_email", "city", "country", "position", "company", "facebook", "linked_in", "twitter", "description", "slack", "prefix", "phone");
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            update_user_meta($user_id, $key, $_POST[$key]);
            if ($key === "user_email") {
                $user_data = get_userdata($user_id);
                $user_data->user_email = $_POST[$key];
                wp_update_user($user_data);
            }
        }
    }
    if (isset($_POST["prefix"]) && isset($_POST["phone"])) {
        $new_phone = $_POST["prefix"] . $_POST["phone"];
        update_user_meta($user_id, "phone_number", $new_phone);
    }
    if (isset($_POST["networks"])) {
        $networks = join(",", $_POST["networks"]);
        update_user_meta($user_id, "networks", $networks);
    }
    if (isset($_POST["updates"])) {
        $updates = join(",", $_POST["updates"]);
        update_user_meta($user_id, "updates", $updates);
    }
}
get_header();
if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    $page_title = "Profile Settings";
    $avatar_label = "Change your profile image";
    $user_id = get_current_user_id();
    $avatar_url = get_avatar_url($user_id, array('size' => 100)) . '?nocache=' . time();
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
    $settings_title = "Network Settings";
    $settings_description = "Please indicate the networks you belong to or use the ‘Join’ button to join a network.";
    $membership_label = "I’m already a member";

    $first_name = get_user_meta($user_id, 'first_name', true);
    $last_name = get_user_meta($user_id, 'last_name', true);
    $position = get_user_meta($user_id, 'position', true);
    $city = get_user_meta($user_id, 'city', true);
    $_country = get_user_meta($user_id, 'country', true);
    $user_email = $current_user->user_email;
    $linked_in = get_user_meta($user_id, 'linked_in', true);
    $facebook = get_user_meta($user_id, 'facebook', true);
    $slack = get_user_meta($user_id, 'slack', true);
    $twitter = get_user_meta($user_id, 'twitter', true);
    $company = get_user_meta($user_id, 'company', true);
    $description = $current_user->description;
    $prefix = get_user_meta($user_id, 'prefix', true);
    $phone = get_user_meta($user_id, 'phone', true);
    $user_networks = explode(",", get_user_meta($user_id, 'networks', true));
    $user_updates = explode(",", get_user_meta($user_id, 'updates', true));
?>
    <main class="profile">
        <h4 class="cfa-title">
            <? echo $page_title ?>
        </h4>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="avatar" class="avatar-label">
                <? echo $avatar_label ?>
            </label>
            <div class="avatar">
                <img height="100px" width="100px" style="height: 100px;" src="<? echo $avatar_url ?>" alt="<?php echo esc_attr($current_user->user_firstname); ?>" id="avatar_preview">
                <button type="button" onclick="document.getElementById('avatar').click()" class="button primary">
                    <input onchange="displayImage(this)" style="display: none;" type="file" name="avatar" id="avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M14 10V12.6667C14 13.0203 13.8595 13.3594 13.6095 13.6095C13.3594 13.8595 13.0203 14 12.6667 14H3.33333C2.97971 14 2.64057 13.8595 2.39052 13.6095C2.14048 13.3594 2 13.0203 2 12.6667V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M11.3346 5.33333L8.0013 2L4.66797 5.33333" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8 2V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <? echo $upload_text ?>
                </button>
            </div>

            <p class="my-courses">
                <a href="/my-courses">
                    <? echo $view_my_courses ?>
                </a>
            </p>
            <p class="form-description">
                <? echo $form_description ?>
            </p>
            <div class="user-form">
                <div class="input">
                    <label for="user_firstname">
                        <? echo $first_name_label ?>*
                    </label>
                    <input type="text" name="first_name" required value="<? echo $first_name ?>" id="user_firstname">
                </div>
                <div class="input">
                    <label for="user_lastname">
                        <? echo $last_name_label ?>*
                    </label>
                    <input type="text" name="last_name" required value="<? echo $last_name ?>" id="user_lastname">
                </div>
                <div class="input">
                    <label for="user_email">
                        <? echo $email_label ?>
                    </label>
                    <input type="email" name="user_email" value="<? echo $user_email ?>" id="user_email" disabled>
                </div>
                <div class="input">
                    <label for="phone">
                        <? echo $phone_label ?>
                    </label>
                    <select style="width:96px" class="prefix" value="<? echo $prefix ?>" name="prefix" id="prefix">
                        <?php
                        include_once __DIR__ . '/includes/utils/countries.php';
                        function sort_by_dial_code($a, $b)
                        {
                            return strcmp($a["dial_code"], $b["dial_code"]);
                        }
                        usort($all_countries, 'sort_by_dial_code');
                        foreach ($all_countries as $country) {
                            $label = $country['flag'] . ' ' . $country['dial_code'];
                            $selected = $prefix === $country['dial_code'] ? 'selected="selected"' : null;
                        ?>
                            <option <? echo $selected ?> value="<?php echo $country['dial_code'] ?>">
                                <?php echo $label ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                    <input type="tel" name="phone" value="<? echo $phone ?>" id="phone">
                </div>
                <div class="input">
                    <label for="city">
                        <? echo $city_label ?>*
                    </label>
                    <input type="text" name="city" required value="<? echo $city ?>" id="city">
                </div>

                <div class="input">
                    <label for="country">
                        <? echo $country_label ?>*
                    </label>
                    <input type="text" name="country" id="country" required value="<? echo $_country ?>">
                </div>
                <div class="input">
                    <label for="position">
                        <? echo $position_label ?>
                    </label>
                    <input type="text" name="position" value="<? echo $position ?>" id="position">
                </div>
                <div class="input">
                    <label for="company">
                        <? echo $company_label ?>
                    </label>
                    <input type="text" name="company" value="<? echo $company ?>" id="company">
                </div>

                <div class="input">
                    <label for="slack">
                        <? echo $slack_label ?>
                    </label>
                    <input type="text" name="slack" value="<? echo $slack ?>" id="slack">
                </div>
                <div class="input">
                    <label for="twitter">
                        <? echo $twitter_label ?>
                    </label>
                    <input type="text" name="twitter" value="<? echo $twitter ?>" id="twitter">
                </div>
                <div class="input">
                    <label for="facebook">
                        <? echo $facebook_label ?>
                    </label>
                    <input type="text" name="facebook" value="<? echo $facebook ?>" id="facebook">
                </div>
                <div class="input">
                    <label for="linked_in">
                        <? echo $linked_in_label ?>
                    </label>
                    <input type="text" name="linked_in" value="<? echo $linked_in ?>" id="linked_in">
                </div>
                <input type="hidden" name="action" value="profile">
                <div class="bio">
                    <label for="description">
                        <? echo $bio_label ?>
                    </label>
                    <?
                    $content = $description;
                    $editor_id = 'description';
                    wp_editor($content, $editor_id);
                    ?>
                    <!-- <textarea name="description" id="description" rows="10" value="<? echo $description ?>"><? echo $description ?></textarea> -->
                </div>
            </div>

            <p class="mandatory">
                *
                <? echo $mandatory_label ?>
            </p>
            <div class="updates">
                <p class="receive-updates">
                    <? echo $receive_updates_label ?>
                </p>
                <div class="checkbox-group">
                    <ul>
                        <li>
                            <label class="mui-checkbox">
                                <?
                                $checked = in_array("courses", $user_updates) ? 'checked="true"' : "";
                                ?>
                                <input type="checkbox" <? echo $checked ?> name="updates[]" value="courses">
                                <span class="checkmark"></span>
                                <? echo $new_courses_label ?>
                            </label>
                        </li>
                        <li>
                            <label class="mui-checkbox">
                                <?
                                $checked = in_array("events", $user_updates) ? 'checked="true"' : "";
                                ?>
                                <input type="checkbox" <? echo $checked ?> name="updates[]" value="events">
                                <span class="checkmark"></span>
                                <? echo $new_events_label ?>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
            <h1 class="setting-title">
                <? echo $settings_title ?>
            </h1>
            <p class="setting-description">
                <? echo $settings_description ?>
            </p>
            <?
            $args = array(
                'post_type' => 'network',
                'posts_per_page' => -1,
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $network_id = get_the_ID();
                    $network_title = get_the_title();
                    $image_url = get_the_post_thumbnail_url($network_id, array(100, 100));
                    $description = get_the_excerpt($network_id);
                    $join = get_post_meta($network_id, 'join', true)["url"];
            ?>
            <script>
                console.log(<? echo json_encode($join)?>)
            </script>
                    <div class="network">
                        <img class="img" src="<? echo $image_url ?>" alt="<? echo $network_title ?>">
                        <div class="content">
                            <p class="title">
                                <? echo $network_title ?>
                            </p>
                            <div class="description">
                                <? echo $description ?>
                            </div>
                            <label class="mui-checkbox">
                                <?
                                $checked = in_array($network_id, $user_networks) ? 'checked="true"' : "";
                                ?>
                                <input <? echo $checked ?> type="checkbox" name="networks[]" value="<? echo $network_id ?>">
                                <span class="checkmark"></span>
                                <? echo $membership_label ?>
                            </label>
                            <a target="_blank" href="<? echo $join ?>">
                                <button type="button" class="primary button">
                                    Join
                                </button>
                            </a>
                        </div>
                    </div>
            <?
                }
                wp_reset_query();
            }
            ?>
            <div class="submit-area">
                <button type="submit" class="button primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                        <path d="M12.6667 14.5H3.33333C2.97971 14.5 2.64057 14.3595 2.39052 14.1095C2.14048 13.8594 2 13.5203 2 13.1667V3.83333C2 3.47971 2.14048 3.14057 2.39052 2.89052C2.64057 2.64048 2.97971 2.5 3.33333 2.5H10.6667L14 5.83333V13.1667C14 13.5203 13.8595 13.8594 13.6095 14.1095C13.3594 14.3595 13.0203 14.5 12.6667 14.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M11.3346 14.4993V9.16602H4.66797V14.4993" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4.66797 2.5V5.83333H10.0013" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <? echo $save_changes_label ?>
                </button>
            </div>
        </form>
        <script>
            // console.log(<? echo json_encode($_POST) ?>);

            function displayImage(input) {
                const preview = document.getElementById('avatar_preview');

                const file = input.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    };

                    reader.readAsDataURL(file);
                }
            }
        </script>
    </main>
<?php
} else {
    echo 'Please log in to edit your profile.';
}
?>

<?php get_footer(); ?>
