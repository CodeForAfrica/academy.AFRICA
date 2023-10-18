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
                    <input type="text" placeholder="Search Courses" class="search-input">
                    <button class="search-button">
                        <img src="./search-icon.png" alt="search" class="search-icon">
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
                        <img src="./arrow-down.png" alt="arrow-down" class="arrow-down">
                    </a>
                    <a href="#" class="nav-link login-button">
                        <p class="nav-link-text">Sign In</p>
                    </a>
                    <a href="#" class="nav-link nav-button lang-button ">
                        <img src="./language-icon.png" alt="language" class="icon language-icon">
                        <img src="./arrow-down.png" alt="arrow-down" class="arrow-down icon">
                    </a>
                </div>
            </div>
        </nav>

<?php
    }
}
