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

//  DELETE PILL REMINDER 
add_action('wp_ajax_delete_pill_reminder', 'pill_reminders_delete_reminder');

function pill_reminders_delete_reminder()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'pill_reminders';
    $medicine_id = intval($_POST['medicine_id']);

    if ($medicine_id <= 0) {
        wp_send_json_error('Invalid reminder ID');
    }

    $reminder = $wpdb->get_row($wpdb->prepare(
        "SELECT id, post_id FROM $table_name WHERE id = %d",
        $medicine_id
    ));

    if (!$reminder) {
        wp_send_json_error('Reminder not found');
    }

    $post_id = $reminder->post_id;

    $deleted_table = $wpdb->delete($table_name, ['id' => $medicine_id]);

    if ($post_id > 0) {
        wp_delete_post($post_id, true);
    }

    if ($deleted_table) {
        wp_send_json_success('Deleted');
    } else {
        wp_send_json_error('Failed to delete from table');
    }
}

//actve deactive
if (isset($_POST['medicine_id'])) {
    $medicine_id = intval($_POST['medicine_id']);
    $status = isset($_POST['status']) ? 1 : 0;

    $wpdb->update($wpdb->prefix . 'pill_reminders', ['status' => $status], ['id' => $medicine_id], ['%d'], ['%d']);

}

// -------------------- email sending code for remider timming --------------------------
/**
 * Mailtrap SMTP Configuration
 */

function mailtrap($phpmailer)
{
    $phpmailer->isSMTP();
    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 2525;
    $phpmailer->Username = '642b4be144e59e';
    $phpmailer->Password = '2d787642b43b3f';
    $phpmailer->SMTPSecure = '';
    $phpmailer->setFrom('noreply@yourdomain.com', 'Pill Reminders');
}
add_action('phpmailer_init', 'mailtrap');

add_filter('wp_mail_from', function () {
    return 'noreply@yourdomain.com';
});
add_filter('wp_mail_from_name', function () {
    return 'Pill Reminders';
});

/**
 * Schedule Cron - Every 30 seconds for better accuracy
 */
function schedule_reminder_email()
{
    if (!wp_next_scheduled('check_reminder_email')) {
        wp_schedule_event(time(), 'every_30_seconds', 'check_reminder_email');
    }
}
add_action('wp', 'schedule_reminder_email');

add_filter('cron_schedules', function ($schedules) {
    $schedules['every_30_seconds'] = [
        'interval' => 30,
        'display'  => __('Every 30 seconds')
    ];
    return $schedules;
});

/**
 * MAIN REMINDER EMAIL FUNCTION - FIXED FOR CORRECT TIMING
 */
add_action('check_reminder_email', function() {
    global $wpdb, $phpmailer;
    $table_name = $wpdb->prefix . 'pill_reminders';
    
    $wp_timezone = wp_timezone();
    $current_dt  = new DateTime('now', $wp_timezone);
    
    $current_date   = $current_dt->format('Y-m-d');
    $current_timestamp = $current_dt->getTimestamp();

    error_log("🔍 Cron ran at: " . $current_dt->format('Y-m-d H:i:s'));

    $rows = $wpdb->get_results($wpdb->prepare("
        SELECT * FROM $table_name 
        WHERE status = 1 
          AND %s BETWEEN from_date AND to_date
    ", $current_date));

    if (empty($rows)) return;

    $sent_count = 0;

    foreach ($rows as $row) {
        $times_raw = trim($row->reminder_times ?? '');
        $times_raw = preg_replace('/afterfood|withfood|beforefood.*/i', '', $times_raw);
        $times = json_decode($times_raw, true) ?: [];

        if (!is_array($times) || empty($times)) continue;

        foreach ($times as $time) {
            $time = trim($time);

            $reminder_dt = date_create_from_format('Y-m-d H:i', $current_date . ' ' . $time, $wp_timezone);
            if (!$reminder_dt) continue;
            
            $reminder_timestamp = $reminder_dt->getTimestamp();

            // Resilient timestamp check prevents WP-Cron dropping pills when it sleeps
            if ($reminder_timestamp > $current_timestamp) {
                continue; // Time has not arrived yet
            }
            if (($current_timestamp - $reminder_timestamp) > 3600) {
                continue; // Over 1 hour late, expiration limit reached
            }

            $sent_key = "pill_email_sent_{$row->id}_{$current_date}_{$time}";

            if (get_transient($sent_key)) {
                continue; // Already successfully processed today
            }

            $subject = "🔔 Pill Reminder - {$row->medicine_name} at {$time}";

            $body = '
            <div style="font-family:Arial,sans-serif; max-width:600px; margin:0 auto; padding:20px; border:1px solid #ddd;">
                <h2 style="color:#d32f2f;">Pill Reminder</h2>
                <p>Hello,</p>
                <p><strong>Time to take your medicine:</strong></p>
                
                <table style="width:100%; border-collapse:collapse; margin:15px 0;">
                    <tr>
                        <td style="padding:12px; border:1px solid #ddd; background:#f9f9f9; width:40%;"><strong>Medicine</strong></td>
                        <td style="padding:12px; border:1px solid #ddd;">' . esc_html($row->medicine_name) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:12px; border:1px solid #ddd; background:#f9f9f9;"><strong>Time</strong></td>
                        <td style="padding:12px; border:1px solid #ddd;">' . esc_html($time) . '</td>
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
            </div>';

            $headers = [
                'Content-Type: text/html; charset=UTF-8',
                'From: Pill Reminders <noreply@yourdomain.com>',
                'Cc: rathodmohit149@gmail.com'
            ];

            if (wp_mail($row->email, $subject, $body, $headers)) {
                set_transient($sent_key, true, 86400);   // Complete locking prevents duplicate sends
                error_log("✅ SUCCESS: Sent to {$row->email} → {$row->medicine_name} at {$time}");
                $sent_count++;
                
                // IMPORTANT BUGFIX: Mailtrap throws "nested MAIL command" if we don't close the global connection between rapid loops!
                if (isset($phpmailer) && is_object($phpmailer) && method_exists($phpmailer, 'smtpClose')) {
                    $phpmailer->smtpClose();
                }
                
                sleep(10);
            } else {
                error_log("❌ FAILED: {$row->medicine_name} at {$time}");
            }
        }
    }

    if ($sent_count > 0) {
        error_log("📊 Total sequentially queued emails fully delivered this run: {$sent_count}");
    }
});

// Catch failures
add_action('wp_mail_failed', function($wp_error) {
    error_log('Mail error: ' . print_r($wp_error, true));
});