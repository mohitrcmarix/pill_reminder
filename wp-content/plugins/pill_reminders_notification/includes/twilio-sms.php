<?php
use Twilio\Rest\Client;

/**
 * Send SMS reminder using the logged-in user's own Twilio credentials
 */
function send_twilio_sms_reminder($reminder) {
    $user_id = $reminder->user_id;

    $sid   = get_user_meta($user_id, 'twilio_account_sid', true);
    $token = get_user_meta($user_id, 'twilio_auth_token', true);
    $from  = get_user_meta($user_id, 'twilio_phone_number', true);

    // Safety check
    if (empty($sid) || empty($token) || empty($from)) {
        error_log("Twilio SMS failed: Missing credentials for user ID {$user_id}");
        return false;
    }

    try {
        $client = new Client($sid, $token);

        $message_body = "🔔 Pill Reminder Alert!\n\n" .
                        "Medicine: " . $reminder->medicine_name . "\n" .
                        "Dose: " . $reminder->dose_value . " " . $reminder->dose_type . "\n" .
                        "Time: " . current_time('H:i') . "\n" .
                        "Instructions: " . $reminder->instruction . "\n\n" .
                        "Please take your medicine now.";

        $result = $client->messages->create(
            $reminder->user_number,   // To: patient's phone number
            [
                'from' => $from,      // From: user's Twilio number
                'body' => $message_body
            ]
        );

        error_log("Twilio SMS sent successfully to {$reminder->user_number} for medicine: {$reminder->medicine_name}");
        return true;

    } catch (Exception $e) {
        error_log("Twilio Error for user {$user_id}: " . $e->getMessage());
        return false;
    }
}