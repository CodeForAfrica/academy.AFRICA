<?php

class Academy_Africa_Header extends \Elementor\Widget_Base
{

    public function get_style_depends()
    {
        return ['academy-africa-header'];
    }

    public function get_name()
    {
        return 'academy_africa_header';
    }

    public function get_title()
    {
        return esc_html__('Header Section', 'elementor-addon');
    }

    public function get_icon()
    {
        return 'eicon-code';
    }

    public function get_categories()
    {
        return ['academy-africa'];
    }

    public function get_keywords()
    {
        return ['header', 'navigation'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_header',
            [
                'label' => __('Header', 'academy-africa'),
            ]
        );

        // Sign in button text
        $this->add_control(
            'sign_in_button_text',
            [
                'label' => __('Sign In Button Text', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Sign In', 'academy-africa'),
                'placeholder' => __('Sign In', 'academy-africa'),
            ]
        );

        // Search bar placeholder text
        $this->add_control(
            'search_bar_placeholder_text',
            [
                'label' => __('Search Bar Placeholder Text', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Search Courses', 'academy-africa'),
                'placeholder' => __('Search Courses', 'academy-africa'),
            ]
        );




        $this->end_controls_section();
    }

    function getSiteName()
    {
        return get_bloginfo('name');
    }

    function get_custom_logo_url()
    {
        $custom_logo_id = get_theme_mod('custom_logo');
        $image = wp_get_attachment_image_src($custom_logo_id, 'full');
        return $image[0];
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // inline editing
        $this->add_inline_editing_attributes('sign_in_button_text', 'none');
?>
        <nav class="nav">
            <div class="logo-section">
                <img src="<?php echo $this->get_custom_logo_url(); ?>" alt=<?php echo $this->getSiteName(); ?> class="logo">
                <p class="page-title">
                    <?php echo $this->getSiteName(); ?>
                </p>
            </div>
            <div class="navigation-section">
                <div class="search-bar">
                    <input type="text" placeholder="<?php echo $settings['search_bar_placeholder_text']; ?>" class="search-input">
                    <button class="search-button">
                        <i class="eicon-search search-icon"></i>
                    </button>
                </div>
                <div class="navigation-links">
                    <a href="#" class="nav-link">
                        <p class="nav-link-text">Courses</p>
                    </a>
                    <a href="#" class="nav-link">
                        <p class="nav-link-text">Events</p>
                    </a>
                    <a href="#" class="nav-link">
                        <p class="nav-link-text">Blog</p>
                    </a>
                    <a href="#" class="nav-link">
                        <p class="nav-link-text">About Us</p>
                        <i class="eicon-caret-down arrow-down"></i>
                    </a>
                    <a href="#" class="nav-link login-button">
                        <p class="nav-link-text" <?php echo $this->get_render_attribute_string('sign_in_button_text'); ?>>
                            <?php echo $settings['sign_in_button_text']; ?>
                        </p>
                    </a>
                    <a href="#" class="nav-link nav-button lang-button ">
                        <i class="eicon-globe arrow-down icon language-icon"></i>
                        <i class="eicon-caret-down arrow-down icon"></i>
                    </a>
                </div>
            </div>
        </nav>

<?php
    }
}
