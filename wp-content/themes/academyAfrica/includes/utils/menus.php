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

        $menu_id = $locations[$menu_location];
        
        // Polylang automatically handles menu switching if configured in:
        // Languages > Settings > "The menus are translated"
        $menu_items = wp_get_nav_menu_items($menu_id);

        // Fallback: if empty, try getting English menu by name
        if (empty($menu_items)) {
            $en_menu = wp_get_nav_menu_object($menu_location . 'en');
            if ($en_menu) {
                $menu_items = wp_get_nav_menu_items($en_menu->term_id);
            }
        }

        if (!$menu_items) {
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
