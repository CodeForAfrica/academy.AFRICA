<?php

// namespace AcademyAfrica\Theme;

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Parse an event's stored date (optionally with time) into a timestamp, or null
 * when the stored value is missing or unparseable. Shared by the event list and
 * single-event views so one malformed record can't fatal on
 * date_format(false, ...) or new DateTime() (#57, findings 17 & 39).
 *
 * @param mixed  $raw_date
 * @param string $raw_time
 * @return int|null
 */
function academyafrica_event_timestamp($raw_date, $raw_time = '')
{
    if (empty($raw_date) || !is_string($raw_date)) {
        return null;
    }
    $raw_date = trim($raw_date);
    // Treat MySQL/ACF zero-dates as missing rather than parsing them to year -1.
    if ($raw_date === '' || strncmp($raw_date, '0000-00-00', 10) === 0) {
        return null;
    }
    $value = trim($raw_date . ' ' . (is_string($raw_time) ? $raw_time : ''));
    $ts = strtotime($value);

    return false === $ts ? null : $ts;
}

/**
 * Format an event date for display, returning $fallback for missing/invalid data.
 *
 * @param mixed  $raw_date
 * @param string $format
 * @param string $fallback
 * @return string
 */
function academyafrica_format_event_date($raw_date, $format = 'd/m/Y', $fallback = '')
{
    $ts = academyafrica_event_timestamp($raw_date);

    return null === $ts ? $fallback : date($format, $ts);
}

/**
 * Cache-buster for the event filter options, bumped whenever an event is saved
 * so cached filter dropdowns don't go stale (#57, finding 32).
 *
 * @return int
 */
function academyafrica_event_filters_version()
{
    return (int) get_option('aa_event_filters_version', 1);
}

/**
 * Increment the filter-cache version so cached dropdowns are recomputed.
 * Incremented (not time()) so multiple mutations within the same second still
 * produce distinct versions (#57 review).
 */
function academyafrica_bump_event_filters_version()
{
    update_option('aa_event_filters_version', academyafrica_event_filters_version() + 1);
}

/**
 * Invalidate the event filter cache on any event mutation — not just editor
 * saves. Covers create/update (editor, REST, importer), status transitions,
 * trash/untrash, permanent deletion, and ACF field updates, so stale
 * country/language options can't linger (#57 review).
 *
 * @param int          $post_id
 * @param WP_Post|null $post
 */
function academyafrica_invalidate_event_filters($post_id, $post = null)
{
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }
    $post_type = ($post instanceof WP_Post) ? $post->post_type : get_post_type($post_id);
    if ('event' === $post_type) {
        academyafrica_bump_event_filters_version();
    }
}
add_action('save_post_event', 'academyafrica_invalidate_event_filters', 10, 2);
add_action('before_delete_post', 'academyafrica_invalidate_event_filters', 10, 2);
add_action('trashed_post', 'academyafrica_invalidate_event_filters', 10, 1);
add_action('untrashed_post', 'academyafrica_invalidate_event_filters', 10, 1);
// ACF-managed fields (countries, language, etc.) save outside save_post_event.
add_action('acf/save_post', function ($post_id) {
    if (is_numeric($post_id) && 'event' === get_post_type($post_id)) {
        academyafrica_bump_event_filters_version();
    }
}, 20);

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
        'has_archive'   => false,
        'menu_position' => 5,
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', "speaker"),
        'rewrite'       => array('slug' => 'events', 'feeds' => true),
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
            'title' => $post->post_title,
            'content' => $post->post_content,
            'date' => $post->post_date,
            // Add more fields as needed
        );

        $posts_array[] = $post_array;
    }
    return $posts_array;
}

function get_user_options()
{
    $users_args = array(
        'number' => 3000,
        'order' => 'ASC',
        'orderby' => 'display_name',
        'meta_query' => array(
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
    $speaker = $custom["speaker"][0] ?? '';
    $country = $custom["country"][0] ?? '';
    $date = $custom["date"][0] ?? '';
    $time = $custom["time"][0] ?? '';
    $users = get_user_options();
    $is_virtual = $custom["is_virtual"][0] ?? false;
?>
    <?php wp_nonce_field('event_custom_fields_nonce', 'event_nonce'); ?>
    <div class="form-container">
        <div class="form-group">
            <label for="date">Date</label>
            <input value="<?php echo esc_attr($date); ?>" type="date" class="large-text" id="date" name="date">
        </div>

        <div class="form-group">
            <label for="time">Time</label>
            <input value="<?php echo esc_attr($time); ?>" type="time" class="large-text" id="time" name="time">
        </div>
        <div class="form-group checkbox-label">
            <label>
                <?php
                $checked = $is_virtual ? 'checked' : "";
                ?>
                <input <?php echo $checked ?> type="checkbox" name="is_virtual" value="1">
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

function save_details($post_id)
{
    // Security checks
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'event') return;
    
    // Verify nonce for security
    if (!isset($_POST['event_nonce']) || !wp_verify_nonce($_POST['event_nonce'], 'event_custom_fields_nonce')) {
        return;
    }
    
    // Only update if the post data exists to prevent undefined key errors
    if (isset($_POST["is_virtual"])) {
        update_post_meta($post_id, "is_virtual", sanitize_text_field($_POST["is_virtual"]));
    } else {
        // If checkbox is not checked, delete the meta or set to empty
        update_post_meta($post_id, "is_virtual", '');
    }
    
    if (isset($_POST["date"])) {
        update_post_meta($post_id, "date", sanitize_text_field($_POST["date"]));
    }
    
    if (isset($_POST["time"])) {
        update_post_meta($post_id, "time", sanitize_text_field($_POST["time"]));
    }

    // Filter-cache invalidation is handled independently of this metabox save by
    // academyafrica_invalidate_event_filters() (hooked to save/delete/ACF).
}

add_action("admin_init", "admin_init");
add_action('save_post', 'save_details');
