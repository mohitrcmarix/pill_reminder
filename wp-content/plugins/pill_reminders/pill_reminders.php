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


// Create Table
require_once plugin_dir_path(__FILE__) . 'enqueue_file.php';

// Create Table
require_once plugin_dir_path(__FILE__) . 'includes/create_table.php';

// create pages
require_once plugin_dir_path(__FILE__) . 'includes/create-pages.php';

//user signin/signup
require_once plugin_dir_path(__FILE__) . 'auth/useradd.php';

//pill_remider add
require_once plugin_dir_path(__FILE__) . 'templates/save_pill_reminder.php';

//create pill_reminder CPT
require_once plugin_dir_path(__FILE__) . 'includes/create-pill_posts.php';

// active deactive plugin(pages add/remove) 
register_activation_hook(__FILE__, 'pill_reminders_activate');
register_deactivation_hook(__FILE__, 'pill_reminders_deactivate');

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
add_filter('cron_schedules', function ($schedules) {
    $schedules['minute'] = [
        'interval' => 60,
        'display' => __('Every Minute')
    ];
    return $schedules;
});

add_action('wp', function () {
    if (!wp_next_scheduled('check_reminder_email')) {
        wp_schedule_event(time(), 'minute', 'check_reminder_email');
    }
});

// 2. Reminder Email HTML structure
add_action('check_reminder_email', function () {
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
                // error_log("ℹ️ User {$row->user_id} reminder scheduled at {$reminder_dt->format('Y-m-d H:i:s')} — current time {$current_dt->format('Y-m-d H:i:s')}");

                // 4. Compare and send
                if (abs($current_timestamp - $reminder_timestamp) < 60) {

                    // Structured fields
                    $title = "Pill Reminder";
                    $subject = "🔔 Pill Reminder - {$row->medicine_name} at {$current_dt->format('H:i')}";
                    $to = $row->email;
                    $from = "noreply@yourdomain.com";
                    $cc = "rathodmohit149@gmail.com";

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
add_action('wp_mail_failed', function ($wp_error) {
    error_log('Mail error: ' . print_r($wp_error, true));
});
