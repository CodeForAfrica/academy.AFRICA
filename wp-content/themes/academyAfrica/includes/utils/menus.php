<?php

namespace AcademyAfrica\Theme\Utils;


class MenuFunctions
{
    public static function get_menu_items($menu_location)
    {
        $locations = get_nav_menu_locations();
        $header = wp_get_nav_menu_object($locations[$menu_location]);

        $menu_items = wp_get_nav_menu_items($header->name);

        return self::build_menu_structure($menu_items);
    }

    private static function build_menu_structure($menu_items, $parent = 0)
    {
        $formatted_menu_items = array();

        foreach ($menu_items as $item) {
            if ($item->menu_item_parent == $parent) {
                $formatted_menu_item = array(
                    'id' => $item->ID,
                    'title' => $item->title,
                    'url' => $item->url,
                    'children' => self::build_menu_structure($menu_items, $item->ID),
                    'class' => $item->classes
                );

                $formatted_menu_items[] = $formatted_menu_item;
            }
        }

        return $formatted_menu_items;
    }
}
