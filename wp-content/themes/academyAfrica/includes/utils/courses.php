<?php

namespace AcademyAfrica\Theme\Courses;


class CoursesFunctions
{

    public static function getOrganizations()
    {
        $args = array(
            'post_type' => 'ac-organization',
            'post_status' => 'publish',
            'numberposts' => -1
        );
        $organization_posts = get_posts($args);
        $organizations = array();
        foreach ($organization_posts as $organization_post) {
            $organization = array(
                'id' => $organization_post->ID,
                'title' => $organization_post->post_title,
                'excerpt' => $organization_post->post_excerpt
            );
            array_push($organizations, $organization);
        }
        return $organizations;
    }

    public static function getLearningPaths($attr = [])
    {
        if (empty($attr['per_page'])) {
            $attr['per_page'] = 3;
        }

        $args = array(
            'post_type' => 'ac-learning-path',
            'post_status' => 'publish',
            'numberposts' => $attr['per_page']
        );
        $learning_path_posts = get_posts($args);
        $learning_paths = array();
        foreach ($learning_path_posts as $learning_path_post) {
            $course_ids = get_field('courses', $learning_path_post->ID);
            $courses = array();
            foreach ($course_ids as $course_id) {
                $course = get_post($course_id);
                $course_title = $course->post_title;
                $course_thumbnail = get_the_post_thumbnail_url($course);
                $course_excerpt = $course->post_excerpt;
                $course = array(
                    'id' => $course_id,
                    'title' => $course_title,
                    'thumbnail' => $course_thumbnail,
                    'excerpt' => $course_excerpt
                );
                array_push($courses, $course);
            }
            $learning_path = array(
                'id' => $learning_path_post->ID,
                'title' => $learning_path_post->post_title,
                'excerpt' => $learning_path_post->post_excerpt,
                'thumbnail' => get_the_post_thumbnail_url($learning_path_post),
                'courses' => $courses
            );
            array_push($learning_paths, $learning_path);
        }
        return $learning_paths;
    }

    public static function getAllInstructors()
    {
        $all_courses = get_posts(array(
            'post_type' => 'sfwd-courses',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids'
        ));
        $instructors = array();
        foreach ($all_courses as $course_id) {
            $course = get_post($course_id);
            $instructor_id = $course->post_author;
            $instructor = get_user_by('id', $instructor_id);
            // trim instructor name $instructor->first_name . ' ' . $instructor->last_name;
            $instructor_name = trim($instructor->first_name . ' ' . $instructor->last_name);
            $instructor_avatar = get_avatar_url($instructor_id);
            $instructor_username = $instructor->user_nicename;
            $instructor = array(
                'id' => $instructor_id,
                // if username is empty, use name
                'name' => $instructor_name ? $instructor_name : $instructor_username,
                'avatar' => $instructor_avatar
            );
            if (!in_array($instructor, $instructors)) {
                array_push($instructors, $instructor);
            }
        }
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

        $meta_query = array(
            'relation' => 'OR',
        );
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

        $tax_query['relation'] = 'OR';

        $author_query = [];
        $instructors = !empty($atts['instructor']) ? $atts['instructor'] : [];
        foreach ($instructors as $instructor) {
            $author = self::get_author_by_name($instructor);
            if ($author) {
                $author_query[] = $author->ID;
            }
        }

        $post__in = null;
        $query_args = apply_filters('academy-africa_course_grid_query_args', [
            'post_type' => sanitize_text_field($atts['post_type']),
            'posts_per_page' => intval($atts['per_page']),
            'paged' => intval($atts['paged']),
            'post_status' => 'publish',
            'orderby' => sanitize_text_field($atts['orderby']),
            'order' => sanitize_text_field($atts['order']),
            'tax_query' => $tax_query,
            'post__in' => $post__in,
            'author__in' => $author_query,
            'meta_query' => $meta_query,
        ], $atts, $filter = null);

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
        $user_id = get_current_user_id();

        // $course_options = null;
        $price = '';
        $price_type = '';
        $price_text = '';
        if ($post->post_type == 'sfwd-courses') {
            // $course_options = get_post_meta($post->ID, '_sfwd-courses', true);
            $students_count = learndash_course_grid_count_students($post->ID);
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
            'name' => $user_object->display_name,
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

        return apply_filters('academy-africa_course_grid_post_attr', $post_attr, $post->ID, $atts, $args);
    }
}
