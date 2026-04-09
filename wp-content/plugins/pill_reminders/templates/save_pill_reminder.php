<?php 

// ====================== SAVE REMINDER ======================
add_action('init', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_reminder'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pill_reminders';

        $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;

        $data = [
            'user_id'        => get_current_user_id(),
            'reminder_title' => sanitize_text_field($_POST['title'] ?? ''),
            'medicine_name'  => sanitize_text_field($_POST['medicine_name'] ?? ''),
            'dose_value'     => sanitize_text_field($_POST['dose_value'] ?? ''),
            'dose_type'      => sanitize_text_field($_POST['dose_type'] ?? ''),
            'frequency'      => sanitize_text_field($_POST['frequency'] ?? ''),
            'duration_type'  => sanitize_text_field($_POST['duration_type'] ?? ''),
            'duration_value' => intval($_POST['duration_value'] ?? 0),
            'instruction'    => sanitize_text_field($_POST['instruction'] ?? ''),
            'from_date'      => sanitize_text_field($_POST['from_date'] ?? ''),
            'to_date'        => sanitize_text_field($_POST['to_date'] ?? ''),
            'reminder_times' => wp_json_encode(array_map('sanitize_text_field', $_POST['times'] ?? [])),
            'email'          => sanitize_text_field($_POST['email'] ?? ''),
        ];

        if (empty($data['medicine_name']) || empty($data['email'])) {
            echo '<div class="alert alert-danger">Medicine name and email are required.</div>';
        } else {
            if ($edit_id > 0) {
                $wpdb->update($table_name, $data, ['id' => $edit_id]);
                echo '<div class="alert alert-success">Reminder updated successfully!</div>';

                // Update linked post if exists
                $post_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $table_name WHERE id = %d", $edit_id));
                // if ($post_id) {
                    // wp_update_post([
                    //     'ID'           => $post_id,
                    //     'post_title'   => $data['reminder_title'],
                    //     'post_content' => 'Medicine: ' . $data['medicine_name'] . 
                    //                       "\nDose: " . $data['dose_value'] . ' ' . $data['dose_type'] . 
                    //                       "\nFrequency: " . $data['frequency'] . 
                    //                       "\nDuration: " . $data['duration_value'] . ' ' . $data['duration_type'] . 
                    //                       "\nInstruction: " . $data['instruction'],
                    // ]);
                // }
            } else {
                $wpdb->insert($table_name, $data);
                $insert_id = $wpdb->insert_id;
                echo '<div class="alert alert-success">New reminder added successfully!</div>';

                // $post_id = wp_insert_post([
                //     'post_title'   => $data['reminder_title'],
                //     'post_content' => 'Medicine: ' . $data['medicine_name'] . 
                //                       "\nDose: " . $data['dose_value'] . ' ' . $data['dose_type'] . 
                //                       "\nFrequency: " . $data['frequency'] . 
                //                       "\nDuration: " . $data['duration_value'] . ' ' . $data['duration_type'] . 
                //                       "\nInstruction: " . $data['instruction'],
                //     'post_status'  => 'publish',
                //     'post_author'  => get_current_user_id(),
                //     'post_type'    => 'pill_reminder',
                // ]);

                // // Save post_id back to table
                // $wpdb->update($table_name, ['post_id' => $post_id], ['id' => $insert_id]);
            }

           echo '<script>setTimeout(function(){ window.location.href = "' . esc_url(site_url('/pill_reminder_details/')) . '"; }, 500);</script>';
        }
    }
});
