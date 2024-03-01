<?php

function create_footer_post_type()
{
    $labels = array(
        'name'               => 'Footers',
        'singular_name'      => 'Footer',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Footer',
        'edit_item'          => 'Edit Footer',
        'new_item'           => 'New Footer',
        'all_items'          => 'All Footers',
        'view_item'          => 'View Footer',
        'search_items'       => 'Search Footers',
        'not_found'          => 'No Footers found',
        'not_found_in_trash' => 'No Footers found in Trash',
        'parent_item_colon'  => '',
        'menu_name'          => 'Footers'
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'menu_position' => 5,
        'supports'      => array('title', 'editor', 'thumbnail',),
        'rewrite'       => array('slug' => 'Footers'),
    );

    register_post_type('Footer', $args);
}
