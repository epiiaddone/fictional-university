<?php

function university_post_types(){
    register_post_type('event', array(
        'capability_type'=>'event',//for the members plugin
        'public'=>true,
        'show_in_rest' => true,//for use in new block editor
        'has_archive'=> true,//page for all events
        'rewrite'=> array('slug'=>'events'),
        'labels'=>array(
            'name'=>'Events',//name in admin dashboard
            'singular_name'=> 'Event',
            'add_new_item'=> 'Add New Event',//why no work?
            'edit_item'=> 'Edit Event',
            'all_items'=> 'All Events',
            'search_items'=> 'Search Events'
        ),
        'menu_icon'=>'dashicons-calendar-alt',
        'supports'=>array('title', 'editor', 'excerpt')
    ));

    register_post_type('program', array(
        'public'=>true,
        'show_in_rest' => true,//for use in new block editor
        'has_archive'=> true,//page for all events
        'rewrite'=> array('slug'=>'programs'),
        'labels'=>array(
            'name'=>'Programs',//name in admin dashboard
            'singular_name'=> 'Program',
            'add_new_item'=> 'Add New Program',//why no work?
            'edit_item'=> 'Edit Program',
            'all_items'=> 'All Programs',
            'search_items'=> 'Search Programs'
        ),
        'menu_icon'=>'dashicons-awards',
        'supports'=>array('title', 'excerpt')
    ));

    register_post_type('professor', array(
        'public'=>true,
        'show_in_rest' => true,//for use in new block editor
        'has_archive'=> true,//page for all events
        'rewrite'=> array('slug'=>'professors'),
        'labels'=>array(
            'name'=>'Professors',//name in admin dashboard
            'singular_name'=> 'Professor',
            'add_new_item'=> 'Add New professor',//why no work?
            'edit_item'=> 'Edit Professor',
            'all_items'=> 'All Professors',
            'search_items'=> 'Search Professors'
        ),
        'menu_icon'=>'dashicons-welcome-learn-more',
        'supports'=>array('title', 'editor', 'excerpt', 'thumbnail')
    ));

    register_post_type('campus', array(
        'capability_type'=>'campus',//for member plugin
        'map_meta_cap'=>true,//more options for the role
        'public'=>true,
        'show_in_rest' => true,//for use in new block editor
        'has_archive'=> true,//page for all events
        'rewrite'=> array('slug'=>'campuses'),
        'labels'=>array(
            'name'=>'Campuses',//name in admin dashboard
            'singular_name'=> 'Campus',
            'add_new_item'=> 'Add New Campus',//why no work?
            'edit_item'=> 'Edit Campus',
            'all_items'=> 'All Campuses',
            'search_items'=> 'Search Campuses'
        ),
        'menu_icon'=>'dashicons-location-alt',
        'supports'=>array('title', 'editor', 'excerpt', 'thumbnail')
    ));

    register_post_type('note', array(
        'capability_type'=>'note',//for custom permissions
        'map_meta_cap'=>true,
        'public'=>false,//private to each user
        'show_ui'=>true, //so visible from admin page
        'show_in_rest' => true,//for use in new block editor
        'has_archive'=> true,//page for all notes
        'rewrite'=> array('slug'=>'notes'),
        'labels'=>array(
            'name'=>'Notes',//name in admin dashboard
            'singular_name'=> 'Note',
            'add_new_item'=> 'Add New Note',//why no work?
            'edit_item'=> 'Edit Note',
            'all_items'=> 'All Notes',
            'search_items'=> 'Search Notes'
        ),
        'menu_icon'=>'dashicons-welcome-write-blog',
        'supports'=>array('title', 'editor')
    ));

    register_post_type('like', array(
        'public'=>false,//private to each user
        'show_ui'=>true, //so visible from admin page
        'labels'=>array(
            'name'=>'Likes',//name in admin dashboard
            'singular_name'=> 'Like',
            'add_new_item'=> 'Add New Like',//why no work?
            'edit_item'=> 'Edit Like',
            'all_items'=> 'All Likes',
            'search_items'=> 'Search Likes'
        ),
        'menu_icon'=>'dashicons-heart',
        'supports'=>array('title')
    ));
}
add_action('init', 'university_post_types');
