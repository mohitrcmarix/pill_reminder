<?php
/**
 * Plugin Name:       Pill Reminders
 * Description:       Automatically creates Pill Reminder pages and sends email reminders.
 * Version:           1.2.0
 * Author:            Mohit
 */

if (!defined('ABSPATH')) {
    exit;
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

        wp_enqueue_script('pill-custom', $jsurl . 'custom.js', ['jquery'], '1.2', true);
        wp_enqueue_script('pill-new', $jsurl . 'new.js', ['jquery'], '1.2', true);

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

// Create Table
require_once plugin_dir_path(__FILE__) . 'includes/create_table.php';

// create pages
require_once plugin_dir_path(__FILE__) . 'includes/create-pages.php';

// active deactive plugin(pages add/remove) 
register_activation_hook(__FILE__, 'pill_reminders_activate');
register_deactivation_hook(__FILE__, 'pill_reminders_deactivate');


// ====================== SAVE REMINDER ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_reminder'])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pill_reminders';

    $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;

    $data = [
        'user_id' => get_current_user_id(),
        'reminder_title' => sanitize_text_field($_POST['title'] ?? ''),
        'medicine_name' => sanitize_text_field($_POST['medicine_name'] ?? ''),
        'dose_value' => sanitize_text_field($_POST['dose_value'] ?? ''),
        'dose_type' => sanitize_text_field($_POST['dose_type'] ?? ''),
        'frequency' => sanitize_text_field($_POST['frequency'] ?? ''),
        'duration_type' => sanitize_text_field($_POST['duration_type'] ?? ''),
        'duration_value' => intval($_POST['duration_value'] ?? 0),
        'instruction' => sanitize_text_field($_POST['instruction'] ?? ''),
        'from_date' => sanitize_text_field($_POST['from_date'] ?? ''),
        'to_date' => sanitize_text_field($_POST['to_date'] ?? ''),
        'reminder_times' => wp_json_encode(array_map('sanitize_text_field', $_POST['times'] ?? [])),
        'email' => sanitize_text_field($_POST['email'] ?? ''),
    ];

    if (empty($data['medicine_name']) || empty($data['email'])) {
        echo '<div class="alert alert-danger">Medicine name and email are required.</div>';
    } else {
        if ($edit_id > 0) {
            $wpdb->update($table_name, $data, ['id' => $edit_id]);
            echo '<div class="alert alert-success">Reminder updated successfully!</div>';
        } else {
            $wpdb->insert($table_name, $data);
            echo '<div class="alert alert-success">New reminder added successfully!</div>';
        }
        echo '<script>setTimeout(function(){ window.location.href = "' . esc_url(site_url('/pill_reminder_details/')) . '"; }, 1500);</script>';
    }
}

// Delete Reminder
add_action('wp_ajax_delete_pill_reminder', 'pill_reminders_delete_reminder');
function pill_reminders_delete_reminder()
{
    global $wpdb;
    $id = intval($_POST['medicine_id']);
    $wpdb->delete($wpdb->prefix . 'pill_reminders', ['id' => $id]);
    wp_send_json_success('Deleted');
}

//actve deactive
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['medicine_id'])) {
    $medicine_id = intval($_POST['medicine_id']);
    $status = isset($_POST['status']) ? 1 : 0;

    $wpdb->update($wpdb->prefix . 'pill_reminders', ['status' => $status], ['id' => $medicine_id], ['%d'], ['%d']);
}


// -------------------- email sending code for remider timming --------------------------

function mailtrap($phpmailer)
{
    $phpmailer->isSMTP();
    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 2525;
    $phpmailer->Username = 'b8a04176602916';
    $phpmailer->Password = '9a5de39ed7dc73';
    $phpmailer->SMTPSecure = '';

    $phpmailer->setFrom('noreply@yourdomain.com', 'WordPress Test');
}
add_action('phpmailer_init', 'mailtrap');

add_filter('wp_mail_from', function () {
    return 'noreply@yourdomain.com';
});
add_filter('wp_mail_from_name', function () {
    return 'WordPress Test';
});

function schedule_reminder_email()
{
    if (!wp_next_scheduled('check_reminder_email')) {
        wp_schedule_event(time(), 'minute', 'check_reminder_email');
    }
}
add_action('wp', 'schedule_reminder_email');

// 1. Cron schedule
add_filter('cron_schedules', function($schedules) {
    $schedules['minute'] = [
        'interval' => 60,
        'display'  => __('Every Minute')
    ];
    return $schedules;
});

add_action('wp', function() {
    if (!wp_next_scheduled('check_reminder_email')) {
        wp_schedule_event(time(), 'minute', 'check_reminder_email');
    }
});

// 2. Reminder Email HTML structure
add_action('check_reminder_email', function() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pill_reminders';

    $rows = $wpdb->get_results("
        SELECT * FROM $table_name 
        WHERE status = 1 
          AND CURDATE() BETWEEN from_date AND to_date
    ");

    $wp_timezone = wp_timezone();
    $current_dt = new DateTime('now', $wp_timezone);
    $current_timestamp = $current_dt->getTimestamp();

    foreach ($rows as $row) {
        $times = json_decode($row->reminder_times, true);

        if (is_array($times)) {
            foreach ($times as $time) {
                $reminder_dt = date_create_from_format('Y-m-d H:i', date('Y-m-d') . ' ' . $time, $wp_timezone);
                $reminder_timestamp = $reminder_dt->getTimestamp();

                // 3. Log every check
                error_log("ℹ️ User {$row->user_id} reminder scheduled at {$reminder_dt->format('Y-m-d H:i:s')} — current time {$current_dt->format('Y-m-d H:i:s')}");

                // 4. Compare and send
                if (abs($current_timestamp - $reminder_timestamp) < 60) {
                    
                    // Structured fields
                    $title   = "Pill Reminder";
                    $subject = "🔔 Pill Reminder - {$row->medicine_name} at {$current_dt->format('H:i')}";
                    $to      = $row->email;
                    $from    = "noreply@yourdomain.com";
                    $cc      = "rathodmohit149@gmail.com";

                    // HTML body
                    $body = '
                    <div style="font-family:Arial,sans-serif; max-width:600px; margin:0 auto; padding:20px; border:1px solid #ddd;">
                        <h2 style="color:#d32f2f;">' . esc_html($title) . '</h2>
                        <p>Hello,</p>
                        <p><strong>Time to take your medicine:</strong></p>
                        
                        <table style="width:100%; border-collapse:collapse; margin:15px 0;">
                            <tr>
                                <td style="padding:12px; border:1px solid #ddd; background:#f9f9f9; width:40%;"><strong>Medicine</strong></td>
                                <td style="padding:12px; border:1px solid #ddd;">' . esc_html($row->medicine_name) . '</td>
                            </tr>
                            <tr>
                                <td style="padding:12px; border:1px solid #ddd; background:#f9f9f9;"><strong>Time</strong></td>
                                <td style="padding:12px; border:1px solid #ddd;">' . esc_html($current_dt->format('Y-m-d H:i:s')) . '</td>
                            </tr>
                            <tr>
                                <td style="padding:12px; border:1px solid #ddd; background:#f9f9f9;"><strong>Dose</strong></td>
                                <td style="padding:12px; border:1px solid #ddd;">' . esc_html($row->dose_value . " " . $row->dose_type) . '</td>
                            </tr>
                            <tr>
                                <td style="padding:12px; border:1px solid #ddd; background:#f9f9f9;"><strong>Instruction</strong></td>
                                <td style="padding:12px; border:1px solid #ddd;">' . esc_html($row->instruction) . '</td>
                            </tr>
                        </table>

                        <p><strong>Please take it on time.</strong></p>
                        <hr>
                        <p style="font-size:12px;color:#666;">This is an automated reminder from Pill Reminders plugin.</p>
                    </div>
                    ';

                    // Headers with CC
                    $headers = [
                        'Content-Type: text/html; charset=UTF-8',
                        'From: WordPress Test <' . $from . '>',
                        'Cc: ' . $cc
                    ];

                    if (wp_mail($to, $subject, $body, $headers)) {
                        error_log("✅ Reminder email sent to {$to} (copy to {$cc}) at {$current_dt->format('Y-m-d H:i:s')}");
                    } else {
                        error_log("❌ Failed to send reminder email to {$to} at {$current_dt->format('Y-m-d H:i:s')}");
                    }
                }
            }
        }
    }
});

// Catch failures
add_action('wp_mail_failed', function($wp_error) {
    error_log('Mail error: ' . print_r($wp_error, true));
});
