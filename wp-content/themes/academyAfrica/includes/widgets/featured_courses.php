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
            'certificate',
            [
                'label' => __('Certificate', 'academy-africa'),
            ]
        );
        $this->add_control(
            'certificate_header',
            [
                'label' => __('Certificate Header', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Receive a certificate when you complete a course to show off your new skills.', 'academy-africa'),
                'label_block' => true,
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
                'default' => __('COMPLETION', 'academy-africa'),
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
            'user',
            [
                'label' => __('Certificate User Name', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('First Name Middle Name', 'academy-africa'),
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
        $this->add_control(
            'academy_head_name',
            [
                'label' => __('Course', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Data Visualisation', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'certificate_cta_description',
            [
                'label' => __('CTA Description', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('What would you like to learn today', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'link_to_courses',
            [
                'label' => __('Link to Courses Label', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => [
                    'url' => 'https://academy.africa/courses/',
                    'is_external' => true,
                    'nofollow' => true,
                    'custom_attributes' => 'target="_blank",title="Explore All Courses"',
                ],
            ]
        );
        $this->end_controls_section();
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $title = $settings['title'];
        $description = $settings['description'];

        // Certificate
        $certificate_header = $settings['certificate_header'];
        $courses_link = $settings['link_to_courses'];
        $courses_link_custom_attributes = $courses_link['custom_attributes'];
        preg_match('/title="([^"]+)"/', $courses_link_custom_attributes, $matches);
        $courses_link_label = $matches[1];
        $certificate_course = $settings['certificate_course'];
        $certificate_description = $settings['certificate_description'];
        $presented_to = $settings['presented_to'];
        $certificate_type = $settings['certificate_type'];
        $certificate_title = $settings['certificate_title'];
        $company_name = "academy.Africa";
        $certificate_cta_description = $settings['certificate_cta_description'];
        $academy_head = array(
            'name' => $settings['academy_head_name'],
            'role' => 'Head of Academy',
            'signature' => get_stylesheet_directory_uri() . '/assets/images/signature.png',
            'date' => date("d/m/Y")
        );
?>
        <div class="featured-courses">
            <div class="featured-content">
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
            <div class="featured-certificate">
                <div class="certificate-root">
                    <p class="certificate-header"> <?php echo $certificate_header; ?> </p>
                    <div class="certificate-showcase">
                        <div class="certificate">
                            <div class="certificate-content">
                                <div class="certificate-content-header">
                                    <hr />
                                    <div class="certificate-header-details">
                                        <p class="certificate-of">
                                            <? echo $certificate_title ?>
                                        </p>
                                        <p class="certificate-type">
                                            <? echo $certificate_type ?>
                                        </p>
                                    </div>
                                    <div class="certificate-header-logo">
                                        <hr />
                                        <img class="logo" alt="logo" src="<? echo get_stylesheet_directory_uri() . '/assets/images/mooc-logo-black.svg' ?>" />
                                    </div>

                                </div>
                                <div class="course-details">
                                    <div class="student">
                                        <p class="title"><? echo $presented_to ?></p>
                                        <p class="name first-name">
                                            First Name
                                        </p>
                                        <p class="name bold-text">
                                            Last Name
                                        </p>
                                    </div>
                                    <div class="course">
                                        <p class="course-description title">
                                            <? echo $certificate_description ?>
                                        </p>
                                        <p class="name bold-text">
                                            <? echo $certificate_course ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="certificate-footer">
                                <div class="company-details">
                                    <div class="brand-details">
                                        <img class="logo" alt="logo" src="<? echo get_stylesheet_directory_uri() . '/assets/images/mooc-logo-white.svg' ?>" />
                                        <p class="company-name">
                                            <? echo $company_name ?>
                                        </p>
                                    </div>
                                    <img class="artwork" alt="artwork" src="<? echo get_stylesheet_directory_uri() . '/assets/images/cfa_logo.svg' ?>" />
                                </div>
                                <div class="signature">
                                    <img class="signature-img" alt="signature" alt="<? echo $academy_head['name'] ?>" src="<? echo $academy_head['signature'] ?>" />
                                    <p class="signee-name"><? echo $academy_head['name'] ?></p>
                                    <p class="signee-role"><? echo $academy_head['role'] ?></p>
                                    <p class="sign-date"><? echo $academy_head['date'] ?></p>
                                </div>
                            </div>
                            <div class="certificate-site-name">
                                <p>
                                    www.academy.africa
                                </p>
                            </div>
                        </div>
                        <div class="certificate-showcase-content">
                            <div class="showcase-header">
                                <p class="certificate-header"> <?php echo $certificate_header; ?> </p>
                            </div>
                            <p class="showcase-text">
                                <? echo $certificate_cta_description ?>
                            </p>
                            <a class="button primary large all-courses" href="<?php echo $courses_link['url']; ?>">
                                <? echo $courses_link_label; ?>
                                <i class="fa-solid fa-chevron-right icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?
    }
}
