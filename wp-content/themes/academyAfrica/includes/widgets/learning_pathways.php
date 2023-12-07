<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/../utils/courses.php';


use AcademyAfrica\Theme\Courses\CoursesFunctions;

class Academy_Africa_Learning_Pathways  extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'Learning Pathways';
    }

    public function get_style_depends()
    {
        return ['academy-africa-pathways', 'academy-africa'];
    }

    public function get_script_depends()
    {
        return [];
    }

    public function get_title()
    {
        return esc_html__('Learning Pathways');
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
        $this->end_controls_section();
    }



    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $pathway_title = $settings['pathway_title'];
        $pathway_description = $settings['pathway_description'];
        $courses_count = $settings['pathway_courses_count_text'];
        $filter_by = "Filter by:";
        $current_page = get_query_var('paged') ? get_query_var('paged') : 1;
        $sort = $this->get_query_param('sort');


        $leaning_attr = [
            'per_page' => '9',
            'paged' => $current_page,
            'sort' => $sort
        ];
        $pathways = CoursesFunctions::getLearningPaths($leaning_attr);
        $learning_pathways = $pathways["learning_paths"];
        $count = $pathways["count"];
        $per_page = $pathways["per_page"];
        $total_pages = ceil($count / $per_page);
        if ($total_pages > 1) {
            $has_pagination = true;
        } else {
            $has_pagination = false;
        }

?>
        <main class="all-courses" id="all-courses">
            <aside class="filter-sidebar">
                <div class="sidebar" id="sidebar">
                    <p class="filter-by">
                        <? echo $filter_by ?>
                    </p>
                </div>
            </aside>
            <div class="courses-main">
                <section class="learning-pathways">
                    <div class="title">
                        <h4 class="cfa-title">
                            <? echo $pathway_title ?>
                        </h4>
                    </div>
                    <p class="description">
                        <? echo $pathway_description ?>
                    </p>
                    <div class="content">
                        <?
                        if (!empty($learning_pathways)) {
                            foreach ($learning_pathways as $pathway) {
                                $pathway_name = $pathway["title"];
                                $pathway_icon = $pathway["thumbnail"];
                                $pathway_courses = $pathway["courses"];
                                $pathway_link = get_permalink($pathway["id"]);
                        ?>
                                <a href="<? echo $pathway_link ?>" class="pathway-link">
                                    <div class="card">
                                        <div class="course-card-pattern">
                                            <div class="icon">
                                                <img src="<? echo $pathway_icon ?>" alt="sample-icon">
                                            </div>
                                        </div>
                                        <div class="pathway-card-content">
                                            <p class="pathway-name">
                                                <? echo $pathway_name ?>
                                            </p>
                                            <p class="course-count">
                                                <? echo count($pathway_courses) . ' ' . $courses_count ?>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                        <?
                            }
                        }
                        ?>
                    </div>
                    <?
                    if ($has_pagination) {
                    ?>
                        <hr class="divider">
                        <div class="pagination-container">
                            <a href="/" class="see-all">
                                View All
                            </a>
                            <ul class="pagination">
                                <?
                                if ($current_page > 1) {
                                ?>
                                    <li class="page-item"><a class="page-link" href="<? echo get_pagenum_link($current_page - 1) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M10 12L6 8L10 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a></li>
                                    <?
                                }
                                for ($i = 1; $i <= $total_pages; $i++) {
                                    if ($i == $current_page) {
                                    ?>
                                        <li class="page-item active">
                                            <a class="page-link" href="<? echo get_pagenum_link($i) ?>"><? echo $i ?></a>
                                        </li>
                                    <?
                                    } else {
                                    ?>
                                        <li class="page-item"><a class="page-link" href="<? echo get_pagenum_link($i) ?>"><? echo $i ?></a></li>
                                    <?
                                    }
                                }
                                if ($current_page < $total_pages) {
                                    ?>
                                    <li class="page-item"><a class="page-link" href="<? echo get_pagenum_link($current_page + 1) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M6 12L10 8L6 4" stroke="#616582" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a></li>
                                <?
                                }
                                ?>
                            </ul>
                        </div>
                    <?
                    }
                    ?>
                </section>
            </div>
        </main>
<?
    }
}
