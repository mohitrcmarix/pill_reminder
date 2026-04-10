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

// function my_custom_post_type()
// {
//     $labels=array(
//             'name' => 'Pill Reminder',
//             'singular_name' => 'Pill Reminder',
//             'add_new_item' => 'Add New Pillreminder',
//         );

//     $args = array(
//         'labels' => $labels,
//         'public' => true,
//         'has_archive' => true,
//         'show_in_rest' => true,
//         'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
//         'menu_icon' => 'dashicons-controls-play',
//     );
//     register_post_type('pillreminder', $args);

// }
// add_action('init', 'my_custom_post_type');
