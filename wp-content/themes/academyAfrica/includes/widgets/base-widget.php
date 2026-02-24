<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/../utils/polylang.php';

use AcademyAfrica\Theme\Utils\PolylangHelper;

abstract class Academy_Africa_Base_Widget extends \Elementor\Widget_Base
{
    /**
     * Translatable fields that should be registered with Polylang
     * Override in child classes
     * @var array
     */
    protected $translatable_fields = [];

    /**
     * Get current language
     * @return string
     */
    protected function get_current_language()
    {
        return PolylangHelper::get_current_language();
    }

    /**
     * Translate a string
     * @param string $string
     * @return string
     */
    protected function translate($string)
    {
        return PolylangHelper::translate($string);
    }

    /**
     * Get translated setting value
     * @param string $key
     * @return mixed
     */
    protected function get_translated_setting($key)
    {
        $settings = $this->get_settings_for_display();
        $value = isset($settings[$key]) ? $settings[$key] : '';
        
        if (is_string($value) && !empty($value)) {
            return $this->translate($value);
        }
        
        return $value;
    }

    /**
     * Get posts with language fallback
     * @param array $args
     * @return array
     */
    protected function get_posts_with_fallback($args)
    {
        return PolylangHelper::get_posts_with_fallback($args, $this->get_current_language());
    }

    /**
     * Get translated post
     * @param int $post_id
     * @return int
     */
    protected function get_translated_post($post_id)
    {
        return PolylangHelper::get_translated_post($post_id, $this->get_current_language());
    }

    /**
     * Register translatable strings from settings
     * Called on widget save/update
     */
    public function register_translatable_strings()
    {
        $settings = $this->get_settings_for_display();
        $widget_name = $this->get_name();

        foreach ($this->translatable_fields as $field) {
            if (isset($settings[$field]) && is_string($settings[$field]) && !empty($settings[$field])) {
                $string_name = $widget_name . '_' . $field;
                PolylangHelper::register_string($string_name, $settings[$field], 'AcademyAfrica Widgets');
            }
        }
    }

    /**
     * Get translated label with fallback
     * @param string $label
     * @param string $default
     * @return string
     */
    protected function get_label($label, $default = '')
    {
        $translated = $this->translate($label);
        return !empty($translated) ? $translated : $default;
    }
}
