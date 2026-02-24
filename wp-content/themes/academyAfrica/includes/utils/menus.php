<?php

namespace AcademyAfrica\Theme\Utils;

class MenuFunctions
{
    /**
     * Get menu items for a given menu location
     *
     * @param string $menu_location The menu location slug
     * @param string $language Optional language code for Polylang
     * @return array
     */
    public static function get_menu_items($menu_location, $language = null)
    {
        $locations = get_nav_menu_locations();

        if (!isset($locations[$menu_location])) {
            return [];
        }

        $default_menu_id = $locations[$menu_location];
        $menu_id = $default_menu_id;

        // Resolve the translated menu for the current language via Polylang.
        // Nav menus are taxonomy terms, so pll_get_term() can look up their translation.
        if (function_exists('pll_get_term')) {
            $lang = $language ?: (function_exists('pll_current_language') ? pll_current_language() : null);
            if ($lang) {
                $translated_id = pll_get_term($default_menu_id, $lang);
                if ($translated_id) {
                    $menu_id = $translated_id;
                }
            }
        }

        $menu_items = wp_get_nav_menu_items($menu_id);

        // Fallback: if the translated menu is empty, use the original English menu
        if (empty($menu_items) && $menu_id !== $default_menu_id) {
            $menu_items = wp_get_nav_menu_items($default_menu_id);
        }

        if (empty($menu_items)) {
            return [];
        }

        return self::build_menu_tree($menu_items);
    }

    private static function build_menu_tree($menu_items, $parent = 0)
    {
        $formatted_menu_items = array();

        if (is_array($menu_items)) {

            foreach ($menu_items as $item) {
                if ($item->menu_item_parent == $parent) {
                    $formatted_menu_item = array(
                        'id' => $item->ID,
                        'title' => $item->title,
                        'url' => $item->url,
                        'children' => self::build_menu_tree($menu_items, $item->ID),
                        'class' => implode(' ', $item->classes)
                    );

                    $formatted_menu_items[] = $formatted_menu_item;
                }
            }
        }
        return $formatted_menu_items;
    }
}
