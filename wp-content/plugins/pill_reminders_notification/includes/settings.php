<?php
/**
 * Twilio Settings Page - Each user can enter their own Twilio credentials
 */

// Add menu page in admin
function pill_reminders_add_settings_page() {
    add_menu_page(
        'Pill Reminders Settings',
        'Pill Reminders',
        'read',                          // Allow any logged-in user
        'pill-reminders-settings',
        'pill_reminders_settings_page_callback',
        'dashicons-bell',
        25
    );
}
add_action('admin_menu', 'pill_reminders_add_settings_page');

function pill_reminders_settings_page_callback() {
    $user_id = get_current_user_id();

    // Save settings
    if (isset($_POST['pill_reminders_save_twilio']) && check_admin_referer('pill_reminders_twilio_nonce')) {
        update_user_meta($user_id, 'twilio_account_sid',   sanitize_text_field($_POST['account_sid']));
        update_user_meta($user_id, 'twilio_auth_token',    sanitize_text_field($_POST['auth_token']));
        update_user_meta($user_id, 'twilio_phone_number',  sanitize_text_field($_POST['phone_number']));

        echo '<div class="notice notice-success"><p>Twilio credentials saved successfully!</p></div>';
    }

    $sid   = get_user_meta($user_id, 'twilio_account_sid', true);
    $token = get_user_meta($user_id, 'twilio_auth_token', true);
    $phone = get_user_meta($user_id, 'twilio_phone_number', true);
    ?>
    <div class="wrap">
        <h1>Pill Reminders - Twilio Settings</h1>
        <p>Enter your personal Twilio credentials below. These will be used to send SMS reminders for your pill schedule.</p>

        <form method="post">
            <?php wp_nonce_field('pill_reminders_twilio_nonce'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="account_sid">Twilio Account SID</label></th>
                    <td>
                        <input type="text" name="account_sid" id="account_sid" 
                               value="<?php echo esc_attr($sid); ?>" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="auth_token">Twilio Auth Token</label></th>
                    <td>
                        <input type="password" name="auth_token" id="auth_token" 
                               value="<?php echo esc_attr($token); ?>" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="phone_number">Twilio Phone Number</label></th>
                    <td>
                        <input type="text" name="phone_number" id="phone_number" 
                               value="<?php echo esc_attr($phone); ?>" 
                               placeholder="+1234567890" class="regular-text" required>
                        <p class="description">Your Twilio phone number (e.g., +14155551234)</p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" name="pill_reminders_save_twilio" class="button button-primary">
                    Save Twilio Credentials
                </button>
            </p>
        </form>
    </div>
    <?php
}