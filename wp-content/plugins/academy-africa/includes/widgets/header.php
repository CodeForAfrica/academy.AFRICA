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


    function get_menus()
    {
        $menus = wp_get_nav_menus();
        $menu_names = [];
        foreach ($menus as $menu) {
            $menu_names[$menu->name] = $menu->name;
        }
        return $menu_names;
    }


    protected function register_controls()
    {
        $this->start_controls_section(
            'section_header',
            [
                'label' => __('Header', 'academy-africa'),
            ]
        );
        // Select menu
        $this->add_control(
            'select_menu',
            [
                'label' => __('Select Menu', 'academy-africa'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'primary',
                'options' => $this->get_menus(),
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

    function get_menu_items($name)
    {
        $list = wp_get_nav_menu_items($name);
        $menu_items = [];
        foreach ($list as $item) {
            $menu_items[] = [
                'title' => $item->title,
                'url' => $item->url,
                'id' => $item->ID,
                'parent_id' => $item->menu_item_parent,
            ];
        }
        $menu = [];
        foreach ($menu_items as $item) {
            if ($item['parent_id'] == 0) {
                $menu[$item['id']] = $item;
            } else {
                $menu[$item['parent_id']]['children'][] = $item;
            }
        }
        return $menu;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $selected_menu = $settings['select_menu'];
        $menus = $selected_menu ? $this->get_menu_items($selected_menu) : [];

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
                    <?php foreach ($menus as $menu) : ?>
                        <a href="<?php echo $menu['url']; ?>" class="nav-link">
                            <p class="nav-link-text"><?php echo $menu['title']; ?></p>
                            <?php if (isset($menu['children'])) : ?>
                                <i class="eicon-caret-down arrow-down"></i>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
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
