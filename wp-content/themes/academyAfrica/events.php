<?php

// namespace AcademyAfrica\Theme;

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function event_post_type()
{
    $labels = array(
        'name'               => 'Events',
        'singular_name'      => 'Event',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Event',
        'edit_item'          => 'Edit Event',
        'new_item'           => 'New Event',
        'all_items'          => 'All Events',
        'view_item'          => 'View Event',
        'search_items'       => 'Search Events',
        'not_found'          => 'No Events found',
        'not_found_in_trash' => 'No Events found in Trash',
        'parent_item_colon'  => '',
        'menu_name'          => 'Events'
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'menu_position' => 5,
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', "speaker"),
        'rewrite'       => array('slug' => 'events'),
    );

    register_post_type('event', $args);
}

function get_timezones()
{
    $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC);

    $timezonesArray = array();

    foreach ($timezones as $timezone) {
        $dateTimeZone = new DateTimeZone($timezone);
        $offset = $dateTimeZone->getOffset(new DateTime('now', $dateTimeZone));
        $offsetHours = floor(abs($offset) / 3600);
        $offsetMinutes = floor((abs($offset) % 3600) / 60);
        $offsetSign = ($offset < 0) ? '-' : '+';
        $gmtOffset = $offsetSign . sprintf('%02d:%02d', $offsetHours, $offsetMinutes);

        $timezonesArray[$timezone] = $gmtOffset;
    }
    return $timezonesArray;
}

function get_post_options($post_type)
{
    $args = array(
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $posts = get_posts($args);

    $posts_array = array();

    foreach ($posts as $post) {
        $post_array = array(
            'ID' => $post->ID,
            'post_title' => $post->post_title,
            'post_content' => $post->post_content,
            'post_date' => $post->post_date,
            // Add more fields as needed
        );

        $posts_array[] = $post_array;
    }
    return $posts_array;
}

function get_user_options()
{
    $users_args = array(
        'number' => 3000, 'order' => 'ASC', 'orderby' => 'display_name', 'meta_query' => array(
            array(
                'key' => 'wp_capabilities',
                'value' => 'subscriber',
                'compare' => 'NOT LIKE'
            )
        ),
        'has_published_posts' => true,
    );

    $users = get_users($users_args);

    $users_array = array();

    foreach ($users as $user) {
        $user_array = array(
            'ID' => $user->ID,
            'user_login' => $user->user_login,
            'user_email' => $user->user_email,
            'display_name' => $user->display_name,
            'data' => $user->data,
            // Add more fields as needed
        );

        $users_array[] = $user_array;
    }
    return $users_array;
}
function custom_fields()
{
    global $post;
    $custom = get_post_custom($post->ID);
    $speaker = $custom["speaker"][0];
    $organisation = $custom["organisation"][0];
    $country = $custom["country"][0];
    $date = $custom["date"][0];
    $time = $custom["time"][0];
    $timezone = $custom["timezone"][0];
    $options = get_post_options("post");
    $users = get_user_options();
    $is_virtual = $custom["is_virtual"][0];
    $timezones = get_timezones();
?>
    <div class="form-container">

        <div class="form-group">
            <label for="speaker">Speaker</label>
            <select name="speaker" id="speaker" value="<?php echo $speaker; ?>">
                <?php
                foreach ($users as $user) {
                    $label = $user["display_name"];
                    $selected = $user["ID"] === $speaker ? 'selected="selected"' : null;
                ?>
                    <option <? echo $selected ?> value="<?php echo $user["ID"] ?>">
                        <?php echo $label ?>
                    </option>
                <?php
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="organisation">Organisation</label>
            <input name="organisation" class="large-text" value="<?php echo $organisation; ?>" />
        </div>

        <div class="form-group">
            <label for="country">Country</label>
            <select value="<?php echo $country; ?>" name="country" id="country">
                <?php
                require_once __DIR__ . '/includes/utils/african_countries.php';
                foreach ($african_countries as $name => $flag) {
                    $label = $name;
                    $selected = $label === $country ? 'selected="selected"' : null;
                ?>
                    <option <? echo $selected ?> value="<?php echo $label ?>">
                        <?php echo $label ?>
                    </option>
                <?php
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="language">Language</label>
            <select name="language" id="language">
                <option value="ENGLISH">ENGLISH</option>
                <option value="FRENCH">FRENCH</option>
                <option value="PORTUGUESE">PORTUGUESE</option>
            </select>
        </div>

        <div class="form-group">
            <label for="date">Date</label>
            <input value="<?php echo $date; ?>" type="date" class="large-text" id="date" name="date">
        </div>

        <div class="form-group">
            <label for="date">Time</label>
            <input value="<?php echo $time; ?>" type="time" class="large-text" id="time" name="time">
        </div>
        <div class="form-group">
            <label for="timezone">
                Timezone
            </label>
            <select value="<?php echo $timezone; ?>" name="timezone" id="timezone">
                <?php
                foreach ($timezones as $name => $offset) {
                    $label = $name . ' ' . $offset;
                    $selected = $label === $timezone ? 'selected="selected"' : null;
                ?>
                    <option <? echo $selected ?> value="<?php echo $label ?>">
                        <?php echo $label ?>
                    </option>
                <?php
                }
                ?>
            </select>
        </div>
        <div class="form-group checkbox-label">
            <label>
                <?php
                $checked = $is_virtual ? 'checked' : "";
                ?>
                <input <?php echo $checked ?> type="checkbox" name="is_virtual">
                Is Virtual
            </label>
        </div>
    </div>

<?php
}


function admin_init()
{
    add_meta_box("custom_fields", "Event Fields", "custom_fields", "event", "normal", "low");
}

function save_details()
{
    global $post;
    update_post_meta($post->ID, "speaker", $_POST["speaker"]);
    update_post_meta($post->ID, "organisation", $_POST["organisation"]);
    update_post_meta($post->ID, "date", $_POST["date"]);
    update_post_meta($post->ID, "time", $_POST["time"]);
    update_post_meta($post->ID, "country", $_POST["country"]);
    update_post_meta($post->ID, "is_virtual", $_POST["is_virtual"]);
    update_post_meta($post->ID, "language", $_POST["language"]);
    update_post_meta($post->ID, "timezone", $_POST["timezone"]);
}

add_action("admin_init", "admin_init");
add_action('save_post', 'save_details');
