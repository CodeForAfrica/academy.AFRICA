<?php

namespace AcademyAfrica\Theme\Courses;


class CoursesFunctions
{

    public static function getOrganizations()
    {
        $cached = wp_cache_get('academy_organizations', 'academy_africa');
        if (false !== $cached) {
            return $cached;
        }
        $args = array(
            'post_type' => 'ac-organization',
            'post_status' => 'publish',
            'numberposts' => -1,
            'update_post_meta_cache' => false,
            'update_post_thumbnail_cache' => false,
        );
        $organization_posts = get_posts($args);
        $organizations = array();
        foreach ($organization_posts as $organization_post) {
            $organizations[] = array(
                'id' => $organization_post->ID,
                'title' => $organization_post->post_title,
                'excerpt' => $organization_post->post_excerpt
            );
        }
        wp_cache_set('academy_organizations', $organizations, 'academy_africa', HOUR_IN_SECONDS);
        return $organizations;
    }

    public static function getAllLearningPaths()
    {
        $cached = wp_cache_get('academy_all_learning_paths', 'academy_africa');
        if (false !== $cached) {
            return $cached;
        }
        $args = array(
            'post_type' => 'ac-learning-path',
            'post_status' => 'publish',
            'numberposts' => -1,
            'update_post_meta_cache' => false,
            'update_post_thumbnail_cache' => false,
        );
        $learning_path_posts = get_posts($args);
        $learning_paths = array();
        foreach ($learning_path_posts as $learning_path_post) {
            $learning_paths[] = array(
                'id' => $learning_path_post->ID,
                'title' => $learning_path_post->post_title,
                'excerpt' => $learning_path_post->post_excerpt
            );
        }
        wp_cache_set('academy_all_learning_paths', $learning_paths, 'academy_africa', HOUR_IN_SECONDS);
        return $learning_paths;
    }

    public static function getLearningPaths($attr = [])
    {
        if (empty($attr['per_page'])) {
            $attr['per_page'] = 3;
        }
        $paged   = isset($attr['paged'])   ? intval($attr['paged'])                          : 1;
        $orderby = isset($attr['orderby']) ? sanitize_text_field($attr['orderby'])           : 'date';
        $order   = isset($attr['order'])   ? sanitize_text_field($attr['order'])             : 'DESC';

        $cache_key = 'academy_learning_paths_' . md5(serialize([
            'per_page' => $attr['per_page'],
            'paged'    => $paged,
            'orderby'  => $orderby,
            'order'    => $order,
        ]));

        $cached = wp_cache_get($cache_key, 'academy_africa');
        if (false !== $cached) {
            do_action('qm/debug', 'getLearningPaths: cache hit (per_page={per_page})', [
                'per_page' => $attr['per_page'],
            ]);
            return $cached;
        }

        do_action('qm/start', 'getLearningPaths');

        $learning_path_posts = get_posts(array(
            'post_type'      => 'ac-learning-path',
            'post_status'    => 'publish',
            'posts_per_page' => intval($attr['per_page']),
            'paged'          => $paged,
            'orderby'        => $orderby,
            'order'          => $order,
            'update_post_meta_cache'      => false,
            'update_post_thumbnail_cache' => true,
        ));
        do_action('qm/debug', 'getLearningPaths: DB fetch {count} paths (per_page={per_page})', [
            'count'    => count($learning_path_posts),
            'per_page' => $attr['per_page'],
        ]);

        $learning_paths = array();
        foreach ($learning_path_posts as $learning_path_post) {
            $acf_courses = \get_field('courses', $learning_path_post->ID);
            $course_ids  = !empty($acf_courses) ? array_map(function ($c) {
                return is_object($c) ? $c->ID : (int) $c;
            }, $acf_courses) : [];

            $courses = array();
            if (!empty($course_ids)) {
                $course_posts = get_posts(array(
                    'post__in'                    => $course_ids,
                    'post_type'                   => 'sfwd-courses',
                    'posts_per_page'              => -1,
                    'orderby'                     => 'post__in',
                    'update_post_meta_cache'      => false,
                    'update_post_thumbnail_cache' => true,
                ));
                foreach ($course_posts as $course) {
                    $courses[] = array(
                        'id'        => $course->ID,
                        'title'     => $course->post_title,
                        'thumbnail' => get_the_post_thumbnail_url($course),
                        'excerpt'   => $course->post_excerpt,
                    );
                }
            }

            $learning_paths[] = array(
                'id'        => $learning_path_post->ID,
                'title'     => $learning_path_post->post_title,
                'excerpt'   => $learning_path_post->post_excerpt,
                'thumbnail' => get_the_post_thumbnail_url($learning_path_post),
                'courses'   => $courses,
            );
        }

        do_action('qm/stop', 'getLearningPaths');

        $result = array(
            'learning_paths' => $learning_paths,
            'count'          => wp_count_posts('ac-learning-path')->publish,
            'per_page'       => $attr['per_page'],
        );

        wp_cache_set($cache_key, $result, 'academy_africa', WEEK_IN_SECONDS);

        return $result;
    }

    public static function getAllInstructors()
    {
        $cached = wp_cache_get('academy_instructors', 'academy_africa');
        if (false !== $cached) {
            return $cached;
        }
        do_action('qm/start', 'getAllInstructors');
        global $wpdb;
        $author_ids = $wpdb->get_col(
            "SELECT DISTINCT post_author FROM {$wpdb->posts}
             WHERE post_type = 'sfwd-courses' AND post_status = 'publish'"
        );
        $instructors = array();
        if (!empty($author_ids)) {
            $users = get_users(array('include' => $author_ids));
            foreach ($users as $user) {
                $instructor_name = trim($user->first_name . ' ' . $user->last_name);
                $instructors[] = array(
                    'id' => $user->ID,
                    'name' => $instructor_name ?: $user->user_nicename,
                    'avatar' => get_avatar_url($user->ID)
                );
            }
        }
        wp_cache_set('academy_instructors', $instructors, 'academy_africa', HOUR_IN_SECONDS);
        do_action('qm/stop', 'getAllInstructors');
        do_action('qm/debug', 'getAllInstructors: loaded {count} instructors', ['count' => count($instructors)]);
        return $instructors;
    }

    public static function get_default_atts()
    {
        return apply_filters('academy-africa_course_grid_default_shortcode_attributes', [
            'post_type' => 'sfwd-courses',
            'per_page'  => 9,
            'paged'     => 1,
            'orderby'   => 'ID',
            'order'     => 'DESC',
            'taxonomies' => '',
            'enrollment_status' => '',
            'progress_status' => '',
            'search' => '',
            // Elements
            'thumbnail' => true,
            'thumbnail_size' => 'thumbnail',
            'ribbon' => true,
            'video' => false,
            /**
             * Content includes title, description and button
             */
            'content' => true,
            'title' => true,
            'title_clickable' => true,
            'description' => true,
            'description_char_max' => 120,
            'button' => true,
            'filter' => true,
            "organization" => [],
            "instructor" => [],
            "language" => [],
            'learning_path' => [],
        ]);
    }

    public static function get_author_by_name($name)
    {
        $user = get_user_by('slug', $name);
        if (!$user) {
            $user = get_user_by('login', $name);
        }
        if (!$user) {
            $users = get_users(array('search' => $name));
            foreach ($users as $u) {
                if ($u->display_name == $name) {
                    $user = $u;
                    break;
                }
            }
        }
        return $user;
    }

    public static function build_query($atts = [])
    {
        if (empty($atts['per_page'])) {
            $atts['per_page'] = -1;
        }

        if (empty($atts['paged'])) {
            $atts['paged'] = 1;
        }

        $tax_query = [];

        $taxonomies = !empty($atts['taxonomies']) ? array_filter(explode(';', sanitize_text_field(str_replace('"', '', wp_unslash($atts['taxonomies']))))) : [];

        foreach ($taxonomies as $taxonomy_entry) {
            $taxonomy_parts = explode(':', $taxonomy_entry);

            if (empty($taxonomy_parts[0]) || empty($taxonomy_parts[1])) {
                continue;
            }

            $taxonomy = trim($taxonomy_parts[0]);
            $terms = array_map('trim', explode(',', $taxonomy_parts[1]));

            if (!empty($taxonomy) && !empty($terms)) {
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $terms,
                ];
            }
        }

        $meta_query = array();
        $organization = !empty($atts['organization']) ? $atts['organization'] : [];
        if (!empty($organization)) {
            $orgs = self::getOrganizations();
            $org_ids = array();
            foreach ($orgs as $org) {
                if (in_array($org['title'], $organization)) {
                    array_push($org_ids, $org['id']);
                }
            }
            foreach ($org_ids as $org_id) {
                $org_q = array(
                    'key' => 'organization',
                    'value' => '"' . $org_id . '"',
                    'compare' => 'LIKE'
                );
                array_push($meta_query, $org_q);
            }
        }
        // Match any of the selected organizations (OR within the group). Only
        // add the relation when there are multiple clauses to combine.
        if (count($meta_query) > 1) {
            $meta_query['relation'] = 'OR';
        }

        $language = !empty($atts['language']) ? $atts['language'] : [];
        $lang_slug = !empty($language) ? implode(',', $language) : '';

        $post__in = null;
        $learning_path = !empty($atts['learning_path']) ? $atts['learning_path'] : [];
        if (!empty($learning_path)) {
            $lps = self::getLearningPaths(['per_page' => -1]);
            $courses = array();
            foreach ($lps['learning_paths'] as $lp) {
                if (in_array($lp['title'], $learning_path)) {
                    foreach ($lp['courses'] as $course) {
                        if (isset($course['id'])) {
                            array_push($courses, $course['id']);
                        }
                    }
                }
            }

            // A learning-path filter is active. If it resolves to no courses
            // (empty or unknown path), return zero results. WP_Query ignores an
            // empty post__in array — which would otherwise show ALL courses —
            // so use a non-existent ID to force an empty result set.
            $post__in = !empty($courses) ? $courses : [0];
        }

        // Combine multiple taxonomy clauses with OR (a course matches if it has
        // any of the selected terms). Different filter groups (taxonomy, meta,
        // author, learning path) are still AND-ed together by WP_Query.
        if (count($tax_query) > 1) {
            $tax_query['relation'] = 'OR';
        }

        $author_query = [];
        $instructors = !empty($atts['instructor']) ? $atts['instructor'] : [];
        foreach ($instructors as $instructor) {
            $author = self::get_author_by_name($instructor);
            if ($author) {
                $author_query[] = $author->ID;
            }
        }

        // Allowlist orderby/order so an unexpected value can never reach the
        // SQL ORDER BY clause; unknown values fall back to a safe default.
        $allowed_orderby = ['ID', 'title', 'date', 'name', 'menu_order', 'author', 'modified', 'rand'];
        $orderby = in_array($atts['orderby'], $allowed_orderby, true) ? $atts['orderby'] : 'date';
        $order   = strtoupper((string) $atts['order']) === 'ASC' ? 'ASC' : 'DESC';

        $query_args = apply_filters('academy-africa_course_grid_query_args', [
            'post_type' => sanitize_text_field($atts['post_type']),
            'posts_per_page' => intval($atts['per_page']),
            'paged' => intval($atts['paged']),
            's' => sanitize_text_field($atts['search']),
            'post_status' => 'publish',
            'orderby' => $orderby,
            'order' => $order,
            'tax_query' => $tax_query,
            'post__in' => $post__in,
            'author__in' => $author_query,
            'meta_query' => $meta_query,
            'lang' => $lang_slug,
            'update_post_meta_cache' => true,
            'update_post_thumbnail_cache' => true,
        ], $atts, $filter = null);

        do_action('qm/debug', 'build_query: posts_per_page={per_page} post__in_count={in_count}', [
            'per_page' => $query_args['posts_per_page'],
            'in_count' => is_array($query_args['post__in']) ? count($query_args['post__in']) : 'null',
        ]);

        return $query_args;
    }

    public static function format_price($price, $format = 'plain')
    {
        if ($format == 'output') {
            preg_match('/(((\d+)[,\.]?)*(\d+)([\.,]?\d+)?)/', $price, $matches);

            $price = $matches[1];

            if (!empty($price)) {
                $match_comma_decimal = preg_match('/(?:\d+\.?)*\d+(,\d{1,2})$/', $price, $comma_matches);

                $match_dot_decimal = preg_match('/(?:\d+,?)*\d+(\.\d{1,2})$/', $price, $dot_matches);

                if ($match_comma_decimal) {
                    $has_decimal = !empty($comma_matches[1]) ? true : false;
                    $thousands_separator = '.';
                    $decimal_separator = ',';
                    $price = str_replace('.', '', $price);
                    $price = str_replace(',', '.', $price);
                } else {
                    $has_decimal = !empty($dot_matches[1]) ? true : false;
                    $thousands_separator = ',';
                    $decimal_separator = '.';
                    $price = str_replace(',', '', $price);
                }

                $price = floatval($price);

                if ($has_decimal) {
                    $price = number_format($price, 2, $decimal_separator, $thousands_separator);
                } else {
                    $price = number_format($price, 0, $decimal_separator, $thousands_separator);
                }
            }

            return $price;
        }

        return $price;
    }

    public static function get_post_attr($post, $atts = [], $args = [])
    {
        if (is_numeric($post)) {
            $post = get_post($post);
        }
        do_action('qm/start', 'get_post_attr:' . $post->ID);
        $user_id = get_current_user_id();

        // $course_options = null;
        $price = '';
        $price_type = '';
        $price_text = '';
        $students_count = 0;
        $price_args = [];
        if ($post->post_type == 'sfwd-courses') {
            // $course_options = get_post_meta($post->ID, '_sfwd-courses', true);
            $students_count = academyafrica_count_students($post->ID);
            $price_args = learndash_get_course_price($post->ID);
        }
        $currency = learndash_get_currency_symbol();


        if (!empty($price_args)) {
            $price = $price_args['price'];
            $price_type = $price_args['type'];
            $price_format = apply_filters('academy-africa_course_grid_price_format', '{currency}{price}');

            if (is_numeric($price) && !empty($price)) {
                $price = self::format_price($price, 'output');
                $price_text = str_replace(['{currency}', '{price}'], [$currency, $price], $price_format);
            } elseif (is_string($price) && !empty($price)) {
                if (preg_match('/(((\d+),?)*(\d+)(\.?\d+)?)/', $price)) {
                    $price = self::format_price($price, 'output');
                    $price_text = str_replace(['{currency}', '{price}'], [$currency, $price], $price_format);
                } else {
                    $price_text = $price;
                }
            } elseif (empty($price)) {
                if ('closed' === $price_type || 'open' === $price_type) {
                    $price_text = '';
                } else {
                    $price_text = __('Free', 'learndash-course-grid');
                }
            }
        }

        if (empty($price)) {
            $price = __('Free', 'academy-africa-course-grid');
        }

        $user_object = get_user_by('ID', $post->post_author);
        $author = apply_filters('academy-africa_course_grid_author', [
            'name' => $user_object ? $user_object->display_name : '',
            'avatar' => get_avatar_url($post->post_author),
        ], $post->ID, $post->post_author);
        $course_link = get_permalink($post->ID);


        $post_attr = [
            'user_id' => $user_id,
            'post_type' => $post->post_type,
            'title' => $post->post_title,
            'price' => $price,
            'students' => $students_count,
            'author' => $author,
            'link' => $course_link,
        ];

        $result = apply_filters('academy-africa_course_grid_post_attr', $post_attr, $post->ID, $atts, $args);
        do_action('qm/stop', 'get_post_attr:' . $post->ID);
        return $result;
    }

    public static function get_filter_by()
    {
        $allOrganizations = CoursesFunctions::getOrganizations();
        $allInstructors = CoursesFunctions::getAllInstructors();
        $allLearningPaths = CoursesFunctions::getAllLearningPaths();
        $filter_by = [
            [
                'title' => 'Learning Paths',
                'name' => 'learning_path',
                'options' => []
            ],

        ];


        foreach ($allLearningPaths as $learningPath) {
            $formatedLearningPath = (object)[
                'id' => $learningPath['id'],
                'name' => $learningPath['title'],
            ];
            array_push($filter_by[0]['options'], $formatedLearningPath);
        }

        return $filter_by;
    }

    /**
     * Flush all learning-path cache entries.
     * Called whenever a learning path or course is saved/deleted so stale
     * data never shows. wp_cache_flush_group() is used when available
     * (Redis Object Cache Pro); otherwise falls back to a version bump
     * that effectively invalidates all keys in the group.
     */
    public static function flush_learning_path_cache(): void {
        if (function_exists('wp_cache_flush_group')) {
            wp_cache_flush_group('academy_africa');
        } else {
            // Bump a version key — all getLearningPaths keys become stale
            $version = (int) wp_cache_get('academy_lp_cache_version', 'academy_africa');
            wp_cache_set('academy_lp_cache_version', $version + 1, 'academy_africa', WEEK_IN_SECONDS);
        }
        do_action('qm/debug', 'CoursesFunctions: learning path cache flushed');
    }
}

// Invalidate learning path cache whenever a learning path or course is saved/deleted
add_action('save_post_ac-learning-path', ['AcademyAfrica\Theme\Courses\CoursesFunctions', 'flush_learning_path_cache']);
add_action('delete_post',                function (int $post_id): void {
    if (get_post_type($post_id) === 'ac-learning-path') {
        AcademyAfrica\Theme\Courses\CoursesFunctions::flush_learning_path_cache();
    }
});
// Also flush when a course that belongs to a learning path is updated
add_action('save_post_sfwd-courses',     ['AcademyAfrica\Theme\Courses\CoursesFunctions', 'flush_learning_path_cache']);
