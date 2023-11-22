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
    $date_time = $custom["date_time"][0];
    $options = get_post_options("post");
    $users = get_user_options();
    $is_virtual = $custom["is_virtual"][0];
?>
    <script>
        console.log(<?php echo json_encode($custom) ?>)
    </script>
    <div class="form-container">
        <style>
            .form-container {
                max-width: 400px;
                margin: 0 auto;
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            label {
                display: block;
                margin-bottom: 8px;
                font-weight: bold;
                color: #333;
            }

            input,
            select {
                width: 100%;
                padding: 10px;
                margin-bottom: 16px;
                box-sizing: border-box;
                border: 1px solid #ccc;
                border-radius: 4px;
            }

            select {
                appearance: none;
            }

            select::after {
                content: '\25BC';
                position: absolute;
                top: 50%;
                right: 10px;
                transform: translateY(-50%);
            }

            input[type="datetime-local"] {
                width: calc(100% - 22px);
                /* Adjust for the datetime-local arrow */
            }

            input[type="submit"] {
                background-color: #3498db;
                color: #fff;
                padding: 10px 15px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }

            label.checkbox-label {
                display: flex;
                align-items: center;
                margin-bottom: 16px;
            }

            input[type="checkbox"] {
                margin-right: 8px;
            }

            input[type="submit"]:hover {
                background-color: #007bb5;
            }
        </style>
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
        <!-- <input name="speaker" class="large-text" value="<?php echo $speaker; ?>" /> -->

        <label for="organisation">Organisation</label>
        <input name="organisation" class="large-text" value="<?php echo $organisation; ?>" />

        <label for="country">Country</label>
        <select value="<?php echo $country; ?>" name="country" id="country">
            <?php
            require_once __DIR__ . '/includes/utils/african_countries.php';
            foreach ($african_countries as $name => $flag) {
                $label = $flag . ' ' . $name;
                $selected = $label === $country ? 'selected="selected"' : null;
            ?>
                <option <? echo $selected ?> value="<?php echo $label ?>">
                    <?php echo $label ?>
                </option>
            <?php
            }
            ?>
        </select>

        <label for="date_time">Date and Time</label>
        <input value="<?php echo $date_time; ?>" type="datetime-local" class="large-text" id="date_time" name="date_time">
        <label class="checkbox-label">
            <?
            $checked = $is_virtual ? 'checked' : "";
            ?><input <? echo $checked ?> type="checkbox" name="is_virtual">
            Is Virtual
        </label>
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
    update_post_meta($post->ID, "date_time", $_POST["date_time"]);
    update_post_meta($post->ID, "country", $_POST["country"]);
    update_post_meta($post->ID, "is_virtual", $_POST["is_virtual"]);
}

add_action("admin_init", "admin_init");
add_action('save_post', 'save_details');
