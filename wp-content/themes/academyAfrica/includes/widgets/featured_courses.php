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
        return ['academy-africa-featured-courses'];
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

    public function concatenate_with_count($array)
    {
        $count = count($array);
        return $count === 0 ? '' : ($count === 1 ? $array[0] : $array[0] . ' + ' . ($count - 1) . ' more');
    }


    public function get_courses()
    {
        return [
            [
                'title' => 'Introduction to Data Visualisation with Flourish',
                'providers' => ['code for africa'],
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/user.svg',
            ],
            [
                'title' => 'Fact-checking for Social Media',
                'providers' => ['pesacheck'],
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/user.svg',
            ],
            [
                'title' => 'Drone Journalism',
                'providers' => ['Sensors Africa', 'pesacheck', 'code for africa'],
                'students_count' => 75,
                'price' => 'Free',
                'image' => '/wp-content/plugins/academy-africa/includes/assets/images/user.svg',
            ],
        ];;
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

    public function get_current_user()
    {
        return [
            'first_name' => 'First Name Middle Name',
            'last_name' => 'Last Name'
        ];
    }

    public function get_academy_head()
    {
        return [
            'name' => 'First Name Middle Name',
            'role' => 'Academy Head',
            'signature' => '/wp-content/plugins/academy-africa/includes/assets/images/signature.png',
            'date' => 'MM/DD/YYYY',
        ];
    }


    protected function render()
    {
        // $course_list = do_shortcode('[ld_course_list array=true]');
        // print_r($course_list);

        $settings = $this->get_settings_for_display();
        $courses = $this->get_courses();
        $title = $settings['title'];
        $description = $settings['description'];
        $courses_link_label = $settings['link_to_courses_label'];
        $courses_link_url = $settings['link_to_courses'];
        $want_to_learn = $settings['want_to_learn'];
        $banner_description = $settings['banner_description'];
        $certificate_course = $settings['certificate_course'];
        $certificate_description = $settings['certificate_description'];
        $presented_to = $settings['presented_to'];
        $certificate_type = $settings['certificate_type'];
        $certificate_title = $settings['certificate_title'];
        $user = $this->get_current_user();
        $art_board = "/wp-content/plugins/academy-africa/includes/assets/images/artboard.png";
        $company_image = "/wp-content/plugins/academy-africa/includes/assets/images/cfa_bw.png";
        $company_name = "academy.Africa";
        $academy_head = $this->get_academy_head();
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
            <div>
                <!-- course list -->
                <?php echo do_shortcode('[learndash_course_grid taxonomies="ld_course_tag:featured"  id="featured" columns="3" skin="grid" card="grid-1" per_page="3" filter="false" progress_bar="" pagination="" button=""  ]'); ?>

            </div>
            <!-- Fetch from Learndash -->
            <!-- <div class="neutral-bg">
                <div class="content">
                    <?
                    if (!empty($courses)) {
                        foreach ($courses as $course) {
                            $title = $course['title'];
                            $provider = $this->concatenate_with_count($course['providers']);
                            $student_count = $course['students_count'];
                            $price = $course['price'];
                    ?>
                            <div class="card">
                                <img alt="course-logo" class="logo" src="/wp-content/plugins/academy-africa/includes/assets/images/course-image.png" />
                                <div class="card-content">
                                    <div class="card-title">
                                        <p>
                                            <?php echo $title; ?>
                                        </p>
                                    </div>
                                    <p class="provider">
                                        by <?php echo $provider; ?>
                                    </p>
                                    <div class="card-footer">
                                        <div class="student-count">
                                            <img alt="user" src="/wp-content/plugins/academy-africa/includes/assets/images/user.svg" />
                                            <span><?php echo $student_count; ?></span>
                                        </div>
                                        <div class="tag free">
                                            <?php echo $price; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?
                        }
                    }
                    ?>
                </div>
            </div> -->
            <!-- Fetch from Learndash -->
        </div>
        <!-- <div class="neutral-bg">
            <div class="certificate-showcase">
                <div class="certificate">
                    <div class="certificate-content">
                        <hr>
                        </hr>
                        <div class="flex-items">
                            <div style="flex: 1">
                                <p class="certificate-of">
                                    <? echo $certificate_title ?>
                                </p>
                                <p class="certificate-type">
                                    <? echo $certificate_type ?>
                                </p>
                                <hr>
                                </hr>
                            </div>
                            <div class="cert-logo">
                                <svg width="90" height="91" viewBox="0 0 90 91" fill="#000" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <rect x="0.363281" y="0.257812" width="89.4864" height="89.8571" fill="url(#pattern0)" style="mix-blend-mode:difference" />
                                    <defs>
                                        <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                                            <use xlink:href="#image0_11038_43255" transform="matrix(0.00850969 0 0 0.00847458 -0.0063263 0)" />
                                        </pattern>
                                        <image id="image0_11038_43255" width="119" height="118" xlink:href="data:image/png;base64,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" />
                                    </defs>
                                </svg>
                            </div>

                        </div>
                        <div class="course-details">
                            <div class="student">
                                <p class="title"><? echo $presented_to ?></p>
                                <p class="first-name">
                                    <? echo $user['first_name'] ?>
                                </p>
                                <p class="last-name"><? echo $user['last_name'] ?></p>
                            </div>
                            <div class="course">
                                <p class="course-description">
                                    <? echo $certificate_description ?>
                                </p>
                                <p class="course-name">
                                    <? echo $certificate_course ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="certificate-footer">
                        <img height="40px" width="40px" alt="logo" src="<? echo $art_board ?>" />
                        <p class="company-name">
                            <? echo $company_name ?>
                        </p>
                        <img alt="artwork" src="<? echo $company_image ?>" />
                        <div class="signature">
                            <img alt="signature" alt="<? echo $academy_head['name'] ?>" src="<? echo $academy_head['signature'] ?>" />
                            <p class="signee-name"><? echo $academy_head['name'] ?></p>
                            <p class="signee-role"><? echo $academy_head['role'] ?></p>
                            <p class="sign-date"><? echo $academy_head['date'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="certificate-showcase-content">
                    <p class="showcase-text">
                        <? echo $banner_description ?>
                    </p>
                    <p class="course-text">
                        <? echo $want_to_learn ?>
                    </p>
                    <a class="all-courses" href="<?php echo $courses_link_url; ?>">
                        <? echo $courses_link_label ?>
                    </a>
                </div>
            </div>
        </div> -->
<?
    }
}
