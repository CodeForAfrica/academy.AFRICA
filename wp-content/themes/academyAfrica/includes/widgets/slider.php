<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


class Academy_Africa_Slider  extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'slider';
    }

    public function get_style_depends()
    {
        return ['academy-africa-slider', 'academy-africa'];
    }

    public function get_title()
    {
        return esc_html__('Slider', 'elementor-faq-widget');
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
            'section_header',
            [
                'label' => __('Header', 'academy-africa'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('FAQ\'s', 'academy-africa'),
                'label_block' => true,
            ]
        );
        $faqs = new \Elementor\Repeater();
        $faqs->add_control(
            'title',
            [
                'label' => __('Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'FAQ Title'
            ]
        );
        $faqs->add_control(
            'content',
            [
                'label' => __('content', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'label_block' => true,
            ]
        );
        $this->add_control(
            'faqs',
            [
                'label' => esc_html__('FAQ\'s', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $faqs->get_controls(),
                'title_field' => '{{{ title }}}',
            ]
        );
        $this->end_controls_section();
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_inline_editing_attributes('title', 'none');
        $slides = [
            [
                'title' => 'Start Learning',
                'content' => 'Want to expand your knowledge level in different topics?
                academy.AFRICA has the resources to develop your skillset.',
                'image' => '/wp-content/themes/academyAfrica/assets/images/mask.svg',
                'image_position' => 'left'
            ],
            [
                'title' => 'Turn learning into action',
                'content' => 'So many courses you don’t know where to start from?
                We have curated a learning map for you on different topics based on your current skill level.',
                'image' => '/wp-content/themes/academyAfrica/assets/images/mask.svg',
                'image_position' => 'left'
            ],
            [
                'title' => 'Use Tailored Resources',
                'content' => 'We also have open source tools you can explore on your learning journey
                ',
                'image' => '/wp-content/themes/academyAfrica/assets/images/mask.svg',
                'image_position' => 'left'
            ],
            [
                'title' => 'Join the community',
                'content' => 'What\'s better than one learner?A community of learners collaborating on projects! Join our community of over 800+ members',
                'image' => '/wp-content/themes/academyAfrica/assets/images/mask.svg',
                'image_position' => 'left'
            ],
        ];
?>
        <div class="slider-root">
            <div class="slider-wrapper">
                <div class="swipper mySwiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($slides as $slide) : ?>
                            <div class="swiper-slide">
                                <div class="slide-content">
                                    <div class="slide-content__title">
                                        <h1 class="cfa-title"><?php echo $slide['title'] ?></h1>
                                    </div>
                                    <div class="slide-content__body">
                                        <p><?php echo $slide['content'] ?></p>
                                    </div>
                                </div>
                                <div class="slide-image">
                                    <img src="<?php echo $slide['image'] ?>" alt="">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="pagination">
                <div class="swiper-pagination">
                    <span class="swiper-pagination-bullet">
                        <i class="fas fa-circle"></i>
                    </span>
                </div>
            </div>
        </div>
        <script>
            var swiper = new Swiper(".mySwiper", {
                spaceBetween: 30,
                centeredSlides: true,
                autoplay: {
                    delay: 500000,
                    disableOnInteraction: true,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
            });
        </script>
<?
    }
}
