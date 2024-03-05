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
            'sliders_section',
            [
                'label' => __('Sliders', 'academy-africa'),
            ]
        );
        $sliders = new \Elementor\Repeater();
        $sliders->add_control(
            'title',
            [
                'label' => __('Slider Title', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => 'Slider Title'
            ]
        );
        $sliders->add_control(
            'description',
            [
                'label' => __('Slider Description', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'label_block' => true,
            ]
        );
        $sliders->add_control(
            'image',
            [
                'label' => __('Slider Image', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'label_block' => true,
            ]
        );
        $sliders->add_control(
            'url',
            [
                'label' => __('Slider URL', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::URL,
                'label_block' => true,
            ]
        );
        $this->add_control(
            'sliders',
            [
                'label' => esc_html__('Slider\'s', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $sliders->get_controls(),
                'title_field' => '{{{ title }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $sliders = $settings['sliders'];
?>
        <div class="slider-root">
            <div class="slider-wrapper">
                <div class="swipper mySwiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($sliders as $slide) : ?>
                            <a href="<?php echo $slide['url']['url'] ?>" class="swiper-slide">
                                <div class="swiper-slide">
                                    <div class="slide-content">
                                        <div class="slide-content__title">
                                            <h1 class="cfa-title"><?php echo $slide['title'] ?></h1>
                                        </div>
                                        <div class="slide-content__body">
                                            <p><?php echo $slide['description'] ?></p>
                                        </div>
                                    </div>
                                    <div class="slide-image">
                                        <img src="<?php echo $slide['image']['url'] ?>" alt="">
                                    </div>
                                </div>
                            </a>
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
                spaceBetween: 100,
                centeredSlides: true,
                grabCursor: true,
                loop: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: true,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                breakpoints: {
                    1920: {
                        spaceBetween: 200,
                    }
                }
            });
        </script>
<?
    }
}
