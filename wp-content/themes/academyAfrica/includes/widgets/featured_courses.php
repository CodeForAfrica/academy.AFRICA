<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Academy_Africa_Featured_Courses extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'Featured Courses';
    }

    public function get_style_depends()
    {
        return ['academy-africa-featured-courses', 'academy-africa-learndash-course-grid'];
    }

    public function get_script_depends()
    {
        return ['academy-africa_learndash_course_grid'];
    }

    public function get_title()
    {
        return esc_html__('Featured Courses', 'elementor-featured-courses-widget');
    }

    public function get_icon()
    {
        return 'eicon-code';
    }

    public function get_categories()
    {
        return ['academy-africa'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_title_and_description',
            [
                'label' => __('Title and Description', 'academy-africa'),
            ]
        );
        $this->add_control(
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Develop your skills & get a certificate', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'description',
            [
                'label' => __('Description', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('Take courses taught by top professionals in the field to advance your knowledge of data science, technology, and media development.', 'academy-africa'),
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'certificate',
            [
                'label' => __('Certificate', 'academy-africa'),
            ]
        );
        $this->add_control(
            'certificate_title',
            [
                'label' => __('Certificate Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('CERTIFICATE OF', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'certificate_type',
            [
                'label' => __('Certificate Type', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('CCOMPLETION', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'presented_to',
            [
                'label' => __('Certificate Presented to', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('PRESENTED TO', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'certificate_description',
            [
                'label' => __('Certificate Presented to', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('For completing the academy.AFRICA course', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'certificate_course',
            [
                'label' => __('Course', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Data Visualisation', 'academy-africa'),
                'label_block' => true,
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'banner',
            [
                'label' => __('Banner', 'academy-africa'),
            ]
        );
        $this->add_control(
            'banner_description',
            [
                'label' => __('Certificate Presented to', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Gain a certificate of accomplishment when you complete a course!', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'want_to_learn',
            [
                'label' => __('Certificate Presented to', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('What would you like to learn today?', 'academy-africa'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'link_to_courses_label',
            [
                'label' => __('Courses Label', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Explore all courses', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'link_to_courses',
            [
                'label' => esc_html__('Courses Link', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::URL,
                'options' => ['url', 'custom_attributes'],
                'default' => [
                    'url' => '/courses',
                ],
                'label_block' => true,
            ]
        );

        $this->end_controls_section();
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $title = $settings['title'];
        $description = $settings['description'];
?>
        <div class="featured-courses">
            <div class="title-description">
                <p class="cfa-title">
                    <?php echo $title; ?>
                </p>
                <div class="description">
                    <?php echo $description; ?>
                </div>
            </div>
            <div class="featured-course-list">
                <!-- course list -->
                <?php echo do_shortcode('[learndash_course_grid taxonomies="ld_course_tag:featured"  id="featured" columns="3" skin="grid" card="grid-1" per_page="3" filter="false" progress_bar="" pagination="" button=""  ]'); ?>

            </div>
        </div>
<?
    }
}
