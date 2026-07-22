<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/../utils/courses.php';


use AcademyAfrica\Theme\Courses\CoursesFunctions;

class Academy_Africa_Home_Learning_Pathways  extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'Home Page Learning Pathways';
    }

    public function get_style_depends()
    {
        return ['academy-africa-home-pathways', 'academy-africa'];
    }

    public function get_script_depends()
    {
        return [];
    }

    public function get_title()
    {
        return esc_html__('Home Learning Pathways');
    }

    public function get_icon()
    {
        return 'eicon-code';
    }

    public function get_categories()
    {
        return ['academy-africa'];
    }

    public function get_query_param($param)
    {
        if (isset($_GET[$param])) {
            if ($_GET[$param]) {
                return explode(",", $_GET[$param]);
            }
        }
    }


    protected function register_controls()
    {
        $this->start_controls_section(
            'learning_pathways',
            [
                'label' => __('Learning Pathways', 'academy-africa'),
            ]
        );

        $this->add_control(
            'pathway_title',
            [
                'label' => __('Learning Pathways Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Learning Pathways', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'pathway_description',
            [
                'label' => __('Learning Pathways Description', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Find out how you can enhance your skills and achieve mastery in specific disciplines within data science and technology.', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'pathway_courses_count_text',
            [
                'label' => __('Learning Pathways Courses Count', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Courses', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'pathway_cta_description',
            [
                'label' => __('CTA Description', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Explore them all', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'pathway_cta_link_text',
            [
                'label' => __('CTA Link Text', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Learning Pathways', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'pathway_cta_link',
            [
                'label' => __('CTA Link', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => [
                    'url' => '/learning-pathways',
                    'is_external' => false,
                    'nofollow' => true,
                ],
                'label_block' => true,
            ]
        );

        $this->end_controls_section();
    }



    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $pathway_title = $settings['pathway_title'];
        $pathway_description = $settings['pathway_description'];
        $courses_count = $settings['pathway_courses_count_text'];
        $pathway_cta_description = $settings['pathway_cta_description'];
        $pathway_cta_link = $settings['pathway_cta_link'];
        $pathway_cta_link_text = $settings['pathway_cta_link_text'];
        $pathway_cta_link = $settings['pathway_cta_link'];
        $leaning_attr = [
            'per_page' => 5,
        ];
        $pathways = CoursesFunctions::getLearningPaths($leaning_attr);
        $learning_pathways = $pathways["learning_paths"];


?>
        <main class="all-courses" id="all-courses">
            <section class="learning-pathways home-learning-pathways">
                <div class="title">
                    <h4 class="cfa-title">
                        <?php echo $pathway_title ?>
                    </h4>
                </div>
                <p class="description">
                    <?php echo $pathway_description ?>
                </p>
                <div class="content">
                    <?php
                    if (!empty($learning_pathways)) {
                        foreach ($learning_pathways as $pathway) {
                            $pathway_name = $pathway["title"];
                            $pathway_desc = get_field('pathway_description', $pathway["id"]);
                            $pathway_icon = $pathway["thumbnail"];
                            $pathway_courses = $pathway["courses"];
                            $pathway_link = get_permalink($pathway["id"]);
                    ?>
                            <a href="<?php echo $pathway_link ?>" class="pathway-link">
                                <div class="card">
                                    <div class="course-card-pattern">
                                        <div class="icon">
                                            <img src="<?php echo $pathway_icon ?>" alt="sample-icon">
                                        </div>
                                    </div>
                                    <div class="pathway-card-content">
                                        <div>
                                            <p class="pathway-name">
                                                <?php echo $pathway_name ?>
                                            </p>
                                            <p class="pathway-description">
                                                <?php echo wp_trim_words($pathway_desc, 20, '...') ?>
                                            </p>
                                        </div>
                                        <p class="course-count">
                                            <?php echo count($pathway_courses) . ' ' . $courses_count ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                    <?php
                        }
                    }
                    ?>
                    <div class="pathway-card card">
                        <div class="pathway-cta">
                            <p class="pathway-cta-description">
                                <?php echo $pathway_cta_description ?>
                            </p>
                            <a href="<?php echo $pathway_cta_link['url'] ?>" class="button primary large all-courses">
                                <?php echo $pathway_cta_link_text ?>
                                <i class="fa-solid fa-chevron-right icon"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </section>
        </main>
<?php
    }
}
