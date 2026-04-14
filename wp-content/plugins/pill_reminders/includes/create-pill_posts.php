<?php 
function register_pill_reminder_cpt() {
    $labels = [
        'name'               => 'Pill Reminders',
        'singular_name'      => 'Pill Reminder',
        'menu_name'          => 'Pill Reminders',
        'name_admin_bar'     => 'Pill Reminder',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Reminder',
        'new_item'           => 'New Reminder',
        'edit_item'          => 'Edit Reminder',
        'view_item'          => 'View Reminder',
        'all_items'          => 'All Reminders',
        'search_items'       => 'Search Reminders',
        'not_found'          => 'No reminders found',
        'not_found_in_trash' => 'No reminders found in Trash',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-clock',
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'pill_reminder'],
        'show_in_rest'       => true,
    ];

    register_post_type('pill_reminder', $args);
}
add_action('init', 'register_pill_reminder_cpt');

// function create_custom_post_type() {
//     $labels = array(
//         'name'               => _x( 'reminder', 'Post Type General Name', 'textdomain' ),
//         'singular_name'      => _x( 'reminder', 'Post Type Singular Name', 'textdomain' ),
//         'menu_name'          => __( 'reminder', 'textdomain' ),
//         'add_new_item'       => __( 'Add New reminder', 'textdomain' ),
//         'all_items'          => __( 'All reminder', 'textdomain' ),
//     );
//     $args = array(
//         'labels'             => $labels,    
//         'public'             => true,
//         'has_archive'        => true,
//         'rewrite'            => array('slug' => 'reminder'),
//         'show_in_rest'       => true, // Enables support for the Gutenberg editor
//         'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
//     );
//     register_post_type( 'reminder', $args );
// }
// add_action( 'init', 'create_custom_post_type' );