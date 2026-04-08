<?php
/**
 * Plugin Name:       Pill Reminders with Twilio SMS
 * Description:       Manage pill reminders and send SMS using each user's own Twilio account.
 * Version:           2.0.0
 * Author:            Mohit
 * Text Domain:       pill-reminder
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// ====================== LOAD TWILIO SDK ======================
$autoload_path = plugin_dir_path(__FILE__) . 'vendor/autoload.php';
if (file_exists($autoload_path)) {
    require_once $autoload_path;
} else {
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p><strong>Pill Reminders Error:</strong> Twilio SDK not found. Please run <code>composer require twilio/sdk</code> inside the plugin folder.</p></div>';
    });
}

// ====================== ENQUEUE ASSETS ======================
function pill_reminders_enqueue_assets()
{
    if (is_page(['pill_reminder', 'add_pill_reminder', 'pill_reminder_details', 'view_pill_reminder'])) {
        $cssurl = plugin_dir_url(__FILE__) . 'assets/css/';
        $jsurl = plugin_dir_url(__FILE__) . 'assets/js/';

        wp_enqueue_style('pill-reminders-style', $cssurl . 'style.css');
        wp_enqueue_style('pill-reminders-custom', $cssurl . 'custom.css');
        wp_enqueue_style('pill-google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400..700;1,400..700&display=swap', [], null);

        wp_enqueue_script('jquery');
        wp_enqueue_script('popper-js', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js', [], null, true);
        wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js', ['jquery'], null, true);

        wp_enqueue_script('pill-custom', $jsurl . 'custom.js', ['jquery'], '2.0', true);
        wp_enqueue_script('pill-new', $jsurl . 'new.js', ['jquery'], '2.0', true);

        wp_localize_script('pill-new', 'ajax_object', [
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'pill_reminders_enqueue_assets');

// Favicon
add_action('wp_head', function () {
    echo '<link rel="icon" type="image/x-icon" href="' . plugin_dir_url(__FILE__) . 'img/favicon.ico">';
});

// ====================== REQUIRED FILES ======================
require_once plugin_dir_path(__FILE__) . 'includes/create_table.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';     // Twilio settings per user
require_once plugin_dir_path(__FILE__) . 'includes/twilio-sms.php';  // SMS sending logic

// ====================== ACTIVATION / DEACTIVATION ======================
register_activation_hook(__FILE__, 'pill_reminders_activate');
function pill_reminders_activate()
{
    pill_reminders_install_table();
    pill_reminders_create_page('Pill Reminder', 'pill_reminder', '[pill_reminder_shortcode]');
    pill_reminders_create_page('Add Pill Reminder', 'add_pill_reminder', '[add_pill_reminder_shortcode]');
    pill_reminders_create_page('Pill Reminder Details', 'pill_reminder_details', '[pill_reminder_details_shortcode]');
    pill_reminders_create_page('View Pill Reminder', 'view_pill_reminder', '[view_pill_reminder_shortcode]');
    pill_reminders_create_page('test_sms','test_sms','[test_sms]');
    flush_rewrite_rules();  
}

register_deactivation_hook(__FILE__, 'pill_reminders_deactivate');
function pill_reminders_deactivate()
{
    $slugs = ['pill_reminder', 'add_pill_reminder', 'pill_reminder_details', 'view_pill_reminder','test_sms'];
    foreach ($slugs as $slug) {
        $page = get_page_by_path($slug);
        if ($page) {
            wp_delete_post($page->ID, true);
        }
    }
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
    require plugin_dir_path(__FILE__) . 'templates/pill_reminder.php';
    return pill_reminder();
}

add_shortcode('add_pill_reminder_shortcode', 'render_add_pill_page');
function render_add_pill_page()
{
    ob_start();
    require plugin_dir_path(__FILE__) . 'templates/add_pill_reminder.php';
    return add_pill_reminder();
}

add_shortcode('pill_reminder_details_shortcode', 'render_pill_reminder_details_page');
function render_pill_reminder_details_page()
{
    ob_start();
    require plugin_dir_path(__FILE__) . 'templates/pill_reminder_details.php';
    return pill_reminder_details();
}

add_shortcode('view_pill_reminder_shortcode', 'render_view_pill_reminder_page');
function render_view_pill_reminder_page()
{
    ob_start();
    require plugin_dir_path(__FILE__) . 'templates/view_pill_reminder.php';
    return view_pill_reminder();
}

add_shortcode('test_sms', function() {
    ob_start();
    require plugin_dir_path(__FILE__) . 'templates/test-sms.php';
    return test_sms_form();
});

// ====================== HANDLE ADD / EDIT REMINDER ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_reminder'])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pill_reminders';

    $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;
    $twilio_from = get_user_meta($user_id, 'twilio_phone_number', true);
    $reminder_title = sanitize_text_field($_POST['title']);
    $medicine_name = sanitize_text_field($_POST['medicine_name']);
    $dose_value = sanitize_text_field($_POST['dose_value']);
    $dose_type = sanitize_text_field($_POST['dose_type']);
    $frequency = sanitize_text_field($_POST['frequency']);
    $duration_type = sanitize_text_field($_POST['duration_type']);
    $duration_value = intval($_POST['duration_value']);
    $instruction = sanitize_text_field($_POST['instruction']);
    $from_date = sanitize_text_field($_POST['from_date']);
    $to_date = sanitize_text_field($_POST['to_date']);
    $times = isset($_POST['times']) ? array_map('sanitize_text_field', $_POST['times']) : [];
    $email = sanitize_email($_POST['email']);
    $phone = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';

    $errors = [];

    if (empty($reminder_title))
        $errors[] = "Reminder title is required.";
    if (empty($medicine_name))
        $errors[] = "Medicine name is required.";
    if (empty($dose_value))
        $errors[] = "Dose value is required.";
    if (empty($dose_type))
        $errors[] = "Dose type is required.";
    if (empty($frequency))
        $errors[] = "Frequency is required.";
    if (empty($duration_type))
        $errors[] = "Duration type is required.";
    if ($duration_value <= 0)
        $errors[] = "Duration value must be > 0.";
    if (empty($instruction))
        $errors[] = "Instruction is required.";
    if (empty($from_date) || empty($to_date) || strtotime($to_date) < strtotime($from_date)) {
        $errors[] = "Valid start and end dates are required.";
    }
    if (empty($times))
        $errors[] = "At least one reminder time is required.";
    if (!is_email($email))
        $errors[] = "Valid email is required.";
    if (empty($phone) || !preg_match("/^\+?[1-9]\d{1,14}$/", $phone)) {
        $errors[] = "Valid international phone number is required.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<div class="alert alert-danger">' . esc_html($error) . '</div>';
        }
    } else {
        $data = [
            'user_id' => get_current_user_id(),
            'reminder_title' => $reminder_title,
            'user_number' => $phone,
            'medicine_name' => $medicine_name,
            'dose_value' => $dose_value,
            'dose_type' => $dose_type,
            'frequency' => $frequency,
            'duration_type' => $duration_type,
            'duration_value' => $duration_value,
            'instruction' => $instruction,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'reminder_times' => wp_json_encode($times),
            'email' => $email,
            'twilio_from_number'=> $twilio_from,
        ];

        $format = ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s'];

        if ($edit_id > 0) {
            $result = $wpdb->update($table_name, $data, ['id' => $edit_id], $format, ['%d']);
            $msg = ($result !== false) ? 'Reminder updated successfully!' : 'Update failed.';
        } else {
            $result = $wpdb->insert($table_name, $data, $format);
            $msg = $result ? 'New reminder added successfully!' : 'Failed to add reminder.';
        }

        echo '<div class="alert alert-success">' . esc_html($msg) . '</div>';
        echo '<script>setTimeout(function(){ window.location.href = "' . esc_url(site_url('/pill_reminder_details/')) . '"; }, 1500);</script>';
    }
}

// Active / Deactive
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['medicine_id'])) {
    $medicine_id = intval($_POST['medicine_id']);
    $status = isset($_POST['status']) ? 1 : 0;
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'pill_reminders', ['status' => $status], ['id' => $medicine_id], ['%d'], ['%d']);
}

// Delete Reminder (AJAX)
add_action('wp_ajax_delete_pill_reminder', 'pill_reminders_delete_reminder');
function pill_reminders_delete_reminder()
{
    if (!is_user_logged_in()) {
        wp_send_json_error('Permission denied');
    }
    global $wpdb;
    $medicine_id = intval($_POST['medicine_id']);
    $deleted = $wpdb->delete($wpdb->prefix . 'pill_reminders', ['id' => $medicine_id], ['%d']);

    $deleted ? wp_send_json_success('Deleted successfully') : wp_send_json_error('Failed to delete');
}

// ====================== CRON - SEND SMS ======================
add_action('pill_reminder_cron', 'send_pill_reminders');

if (!wp_next_scheduled('pill_reminder_cron')) {
    wp_schedule_event(time(), 'minutely', 'pill_reminder_cron');
}

add_filter('cron_schedules', function($s) {
    $s['minutely'] = ['interval' => 60, 'display' => 'Every Minute'];
    return $s;
});

function send_pill_reminders() {
    global $wpdb;
    $reminders = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}pill_reminders WHERE status = 1");

    $now = current_time('H:i');

    foreach ($reminders as $rem) {
        $times = json_decode($rem->reminder_times, true) ?: [];
        foreach ($times as $time) {
            if (abs(strtotime($time) - strtotime($now)) <= 60) {
                send_twilio_sms_reminder($rem);
                break;
            }
        }
    }
}

// TEMP: Force cron
add_action('admin_init', function() {
    if (isset($_GET['force_cron'])) {
        send_pill_reminders();
        wp_die('Cron executed. Check debug.log and Twilio dashboard.');
    }
});