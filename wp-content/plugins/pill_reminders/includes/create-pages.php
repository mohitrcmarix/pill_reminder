<?php
// ========================= Activation pages=======================================
function pill_reminders_activate()
{
    pill_reminders_install_table();
    pill_reminders_create_page('Pill Reminder', 'pill_reminder', '[pill_reminder_shortcode]');
    pill_reminders_create_page('Add Pill Reminder', 'add_pill_reminder', '[add_pill_reminder_shortcode]');
    pill_reminders_create_page('Pill Reminder Details', 'pill_reminder_details', '[pill_reminder_details_shortcode]');
    pill_reminders_create_page('View Pill Reminder', 'view_pill_reminder', '[view_pill_reminder_shortcode]');
    pill_reminders_create_page('Sign-In', 'sign-in', '[sign-in_shortcode]');
    pill_reminders_create_page('Sign-Up', 'sign-Up', '[sign-up_shortcode]');

    flush_rewrite_rules();
}

function pill_reminders_create_page($title, $slug, $shortcode)
{
    if (!get_page_by_path($slug)) {
        wp_insert_post([
            'post_title' => $title,
            'post_name' => $slug,
            'post_content' => $shortcode,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
        ]);
    }
}

// ====================== SHORTCODES ======================

add_shortcode('pill_reminder_shortcode', 'pill_reminders_page_content');
function pill_reminders_page_content()
{
    ob_start();
    require_once plugin_dir_path(__FILE__) . '../templates/home.php';
    return home();
}

add_shortcode('add_pill_reminder_shortcode', 'render_add_pill_page');


function render_add_pill_page()
{
    ob_start();
    require_once plugin_dir_path(__FILE__) . '../templates/add_pill_reminder.php';
    return add_pill_reminder();
}
add_shortcode('add_pill_page', 'render_add_pill_page');



add_shortcode('pill_reminder_details_shortcode', 'render_pill_reminder_details_page');
function render_pill_reminder_details_page()
{
    ob_start();
    require plugin_dir_path(__FILE__) . '../templates/pill_reminder_details.php';
    return pill_reminder_detail();

}

add_shortcode('view_pill_reminder_shortcode', 'render_view_pill_reminder_page');
function render_view_pill_reminder_page()
{
    ob_start();
    require_once plugin_dir_path(__FILE__) . '../templates/view_pill_reminder.php';
    return view_pill_reminder();

}

add_shortcode('sign-in_shortcode', 'customsignin');

function customsignin()
{
    ob_start();
    require_once plugin_dir_path(__FILE__) . '../auth/signin.php';
    return singin();
}

add_shortcode('sign-up_shortcode', 'customsigup');

function customsigup()
{
    ob_start();
    require_once plugin_dir_path(__FILE__) . '../auth/signup.php';
    return signup();
}



// ================================deactive the pages======================================

function pill_reminders_deactivate()
{
    $slugs = ['pill_reminder', 'add_pill_reminder', 'pill_reminder_details', 'view_pill_reminder', 'sign-in', 'sign-up'];
    foreach ($slugs as $slug) {
        $page = get_page_by_path($slug);
        if ($page)
            wp_delete_post($page->ID, true);
    }
    // pill_reminders_remove_table();
    flush_rewrite_rules();
}

add_filter('get_pages', 'my_filter_wp_list_pages');
function my_filter_wp_list_pages($pages)
{
    $filtered = array();

    // echo "<pre>";
    // print_r($pages);
    // echo "</pre>";
     
    foreach ($pages as $page) {
        if (in_array($page->post_name, array('sign-up'))) {
            continue;
        }
        if (is_user_logged_in()) {
            if (in_array($page->post_name, array('sign-in', 'sign-up'))) {
                continue;
            }
        } else {
            if ($page->post_name === 'logout') {
                continue;
            }
        }
        $filtered[] = $page;
    }

    return $filtered;
}

add_filter('wp_list_pages', 'pill_reminders_add_logout_to_list_pages', 10, 2);

function pill_reminders_add_logout_to_list_pages($output, $args)
{
    if (!is_user_logged_in()) {
        return $output;
    }

    $logout_link = ' <a href="' . wp_logout_url(home_url('/pill_reminder')) . '" class="logout-link">Logout</a>';
    return $output . $logout_link;
}