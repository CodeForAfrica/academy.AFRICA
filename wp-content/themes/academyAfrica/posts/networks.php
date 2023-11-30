<?php

function create_networks_post_type()
{
    $labels = array(
        'name'               => 'Networks',
        'singular_name'      => 'Network',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Network',
        'edit_item'          => 'Edit Network',
        'new_item'           => 'New Network',
        'all_items'          => 'All Networks',
        'view_item'          => 'View Network',
        'search_items'       => 'Search Networks',
        'not_found'          => 'No Networks found',
        'not_found_in_trash' => 'No Networks found in Trash',
        'parent_item_colon'  => '',
        'menu_name'          => 'Networks'
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'menu_position' => 5,
        'supports'      => array('title', 'editor', 'thumbnail',),
        'rewrite'       => array('slug' => 'networks'),
    );

    register_post_type('network', $args);
}
