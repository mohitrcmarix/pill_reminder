<?php
// ====================== SAVE REMINDER (Fixed Edit + Post Update) ======================

add_action('init', function () {
    if (isset($_POST['set_reminder'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pill_reminders';

        $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;

        $times = isset($_POST['times']) && is_array($_POST['times']) 
        ? array_filter(array_map('trim', $_POST['times'])) 
        : [];

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
            'instruction_time' => sanitize_text_field($_POST['instruction_time'] ?? ''),
            'from_date' => sanitize_text_field($_POST['from_date'] ?? ''),
            'to_date' => sanitize_text_field($_POST['to_date'] ?? ''),
            'reminder_times' =>wp_json_encode($times),
            'email' => sanitize_email($_POST['email'] ?? ''),
        ];

        $errors = [];

        // Reminder title
        if (empty($data['reminder_title'])) {
            $errors[] = "Reminder title is required.";
        }

        // Medicine name
        if (empty($data['medicine_name']) || !preg_match('/^[a-zA-Z0-9\s]+$/', $data['medicine_name'])) {
            $errors[] = "Medicine name must contain only letters, numbers, and spaces.";
        }

        // Dose value
        if (!is_numeric($data['dose_value']) || $data['dose_value'] <= 0) {
            $errors[] = "Dose value must be a positive number.";
        }

        // Dose type
        $allowed_dose_types = ['spoon', 'ml', 'mm', 'number'];
        if (!in_array($data['dose_type'], $allowed_dose_types)) {
            $errors[] = "Invalid dose type.";
        }

        // Frequency
        $allowed_frequencies = ['daily', 'onceaday', 'atnight','every4hrs', 'every6hrs', 'every8hrs', 'every12hrs',];
        if (!in_array($data['frequency'], $allowed_frequencies)) {
            $errors[] = "Invalid frequency.";
        }

        // Duration
        $allowed_duration_types = ['6-Month', '1-Year', 'Life-time'];
        if (!in_array($data['duration_type'], $allowed_duration_types) || $data['duration_value'] <= 0) {
            $errors[] = "Invalid duration.";
        }

        // Dates
        if (!strtotime($data['from_date'])) {
            $errors[] = "Invalid from_date.";
        }
        if (!strtotime($data['to_date']) || strtotime($data['to_date']) < strtotime($data['from_date'])) {
            $errors[] = "Invalid to_date.";
        }

        // Reminder times
        $times = json_decode($data['reminder_times'], true);
        if (!is_array($times)) {
            $errors[] = "Reminder times must be a valid array.";
        } else {
            foreach ($times as $time) {
                if (!preg_match('/^(2[0-3]|[01]?[0-9]):([0-5][0-9])$/', $time)) {
                    $errors[] = "Invalid time format: $time";
                }
            }
        }

        // Email
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address.";
        }

        // Final check
        if (!empty($errors)) {
            
            echo '<div class="alert alert-danger"><pre>';
            print_r($errors);
            echo '</pre></div>';
            return;
        } else {
            if ($edit_id > 0) {

                $result = $wpdb->update($table_name, $data, ['id' => $edit_id]);
                if ($result !== false) {

                    $post_id = $wpdb->get_var($wpdb->prepare(
                        "SELECT post_id FROM $table_name WHERE id = %d",
                        $edit_id
                    ));
                    if ($post_id) {

                        wp_update_post([
                            'ID' => $post_id,
                            'post_title' => $data['reminder_title'],
                            'post_content' => 'Medicine: ' . $data['medicine_name'] .
                                "\nDose: " . $data['dose_value'] . ' ' . $data['dose_type'] .
                                "\nFrequency: " . $data['frequency'] .
                                "\nDuration: " . $data['duration_value'] . ' ' . $data['duration_type'] .
                                "\nInstruction: " . $data['instruction'] . ' ' . $data['instruction_time'] .
                                "\nFrom date: " . $data['from_date'] .
                                "\nTo Date: " . $data['to_date'] .
                                "\nReminder Times: " . $data['reminder_times'] .
                                "\nEmail: " . $data['email'],
                        ]);

                        // Update post meta
                        update_post_meta($post_id, 'medicine_name', $data['medicine_name']);
                        update_post_meta($post_id, 'dose_value', $data['dose_value']);
                        update_post_meta($post_id, 'dose_type', $data['dose_type']);
                        update_post_meta($post_id, 'frequency', $data['frequency']);
                        update_post_meta($post_id, 'duration_type', $data['duration_type']);
                        update_post_meta($post_id, 'duration_value', $data['duration_value']);
                        update_post_meta($post_id, 'instruction', $data['instruction']);
                        update_post_meta($post_id, 'instruction_time', $data['instruction_time']);
                        update_post_meta($post_id, 'from_date', $data['from_date']);
                        update_post_meta($post_id, 'to_date', $data['to_date']);
                        update_post_meta($post_id, 'reminder_times', $data['reminder_times']);
                        update_post_meta($post_id, 'email', $data['email']);

                        echo '<div class="alert alert-success">Reminder updated successfully in both table and post!</div>';
                    } else {
                        echo '<div class="alert alert-warning">Reminder updated in table, but linked post not found.</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger">Failed to update reminder.</div>';
                }

            } else {
                // ==================== INSERT NEW REMINDER ====================
                $wpdb->insert($table_name, $data);
                $insert_id = $wpdb->insert_id;

                $post_id = wp_insert_post([
                    'post_title' => $data['reminder_title'],
                    'post_content' => 'Medicine: ' . $data['medicine_name'] .
                        "\nDose: " . $data['dose_value'] . ' ' . $data['dose_type'] .
                        "\nFrequency: " . $data['frequency'] .
                        "\nDuration: " . $data['duration_value'] . ' ' . $data['duration_type'] .
                        "\nInstruction: " . $data['instruction']  .' ' . $data['instruction_time'].
                        "\nFrom date: " . $data['from_date'] .
                        "\nTo Date: " . $data['to_date'] .
                        "\nReminder Times: " . $data['reminder_times'] .
                        "\nEmail: " . $data['email'],
                    'post_status' => 'publish',
                    'post_author' => get_current_user_id(),
                    'post_type' => 'pill_reminder',
                    'meta_input' => [
                        'medicine_name' => $data['medicine_name'],
                        'dose_value' => $data['dose_value'],
                        'dose_type' => $data['dose_type'],
                        'frequency' => $data['frequency'],
                        'duration_type' => $data['duration_type'],
                        'duration_value' => $data['duration_value'],
                        'instruction' => $data['instruction'],
                        'instruction_time' => $data['instruction_time'],
                        'from_date' => $data['from_date'],
                        'to_date' => $data['to_date'],
                        'reminder_times' => $data['reminder_times'],
                        'email' => $data['email'],
                    ]
                ]);

                if ($post_id && !is_wp_error($post_id)) {
                    $wpdb->update($table_name, ['post_id' => $post_id], ['id' => $insert_id]);
                    echo '<div class="alert alert-success">New reminder added successfully!</div>';
                } else {
                    echo '<div class="alert alert-danger">Failed to create post.</div>';
                }
            }

            // Redirect after save
            echo '<script>
            setTimeout(function(){
                window.location.href = "' . esc_url(site_url('/pill_reminder_details/')) . '";
            }, 1200);
        </script>';
        }
    }

}, 5);