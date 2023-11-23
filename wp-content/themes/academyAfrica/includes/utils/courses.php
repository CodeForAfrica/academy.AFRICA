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


    public static function getAllInstructors(){
        // get all learndash course ids
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
}
