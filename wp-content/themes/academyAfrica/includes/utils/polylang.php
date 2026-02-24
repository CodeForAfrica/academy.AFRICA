<?php

namespace AcademyAfrica\Theme\Utils;

if (!defined('ABSPATH')) {
    exit;
}

class PolylangHelper
{
    /**
     * Get current language code
     * @return string
     */
    public static function get_current_language()
    {
        return function_exists('pll_current_language') ? pll_current_language() : 'en';
    }

    /**
     * Get default language code
     * @return string
     */
    public static function get_default_language()
    {
        return function_exists('pll_default_language') ? pll_default_language() : 'en';
    }

    /**
     * Translate a string using Polylang
     * @param string $string
     * @return string
     */
    public static function translate($string)
    {
        return function_exists('pll__') ? pll__($string) : $string;
    }

    /**
     * Register a string for translation
     * @param string $name
     * @param string $string
     * @param string $group
     */
    public static function register_string($name, $string, $group = 'AcademyAfrica')
    {
        if (function_exists('pll_register_string')) {
            pll_register_string($name, $string, $group);
        }
    }

    /**
     * Get translated post ID
     * @param int $post_id
     * @param string|null $language
     * @return int
     */
    public static function get_translated_post($post_id, $language = null)
    {
        if (!$language) {
            $language = self::get_current_language();
        }

        if (function_exists('pll_get_post')) {
            $translated_id = pll_get_post($post_id, $language);
            return $translated_id ? $translated_id : $post_id;
        }

        return $post_id;
    }

    /**
     * Query posts with language support and fallback
     * @param array $args
     * @param string|null $language
     * @return array
     */
    public static function get_posts_with_fallback($args, $language = null)
    {
        if (!$language) {
            $language = self::get_current_language();
        }

        $args['lang'] = $language;
        $posts = get_posts($args);

        // Fallback to default language if no posts found
        if (empty($posts) && $language !== self::get_default_language()) {
            $args['lang'] = self::get_default_language();
            $posts = get_posts($args);
        }

        return $posts;
    }

    /**
     * Get all registered Polylang languages as slug => name pairs
     * @return array
     */
    public static function get_languages()
    {
        if (!function_exists('pll_languages_list')) {
            return [];
        }

        $slugs = pll_languages_list(['fields' => 'slug']);
        $names = pll_languages_list(['fields' => 'name']);

        if (empty($slugs) || empty($names) || count($slugs) !== count($names)) {
            return [];
        }

        return array_combine($slugs, $names);
    }

    /**
     * Get translated term ID
     * @param int $term_id
     * @param string|null $language
     * @return int
     */
    public static function get_translated_term($term_id, $language = null)
    {
        if (!$language) {
            $language = self::get_current_language();
        }

        if (function_exists('pll_get_term')) {
            $translated_id = pll_get_term($term_id, $language);
            return $translated_id ? $translated_id : $term_id;
        }

        return $term_id;
    }
}
