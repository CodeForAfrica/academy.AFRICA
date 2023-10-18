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
        return ['basic'];
    }

    public function get_keywords()
    {
        return ['header', 'navigation'];
    }

    protected function render()
    {
?>
        <div class="academy-africa-header">
            <h1 class="title">Header</h1>
        </div>

<?php
    }
}
