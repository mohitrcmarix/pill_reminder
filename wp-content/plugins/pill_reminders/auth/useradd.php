<?php
/**
 * Fixed Sign-in & Sign-up Handler
 */

add_action('init', 'pill_reminders_handle_auth', 5);

function pill_reminders_handle_auth() {

    // ====================== SIGNUP ======================
    if (isset($_POST['signup'])) {
        $fname    = sanitize_text_field($_POST['first_name'] ?? '');
        $lname    = sanitize_text_field($_POST['last_name'] ?? '');
        $username = sanitize_user($_POST['username'] ?? '');
        $email    = sanitize_email($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $gender   = sanitize_text_field($_POST['radiogroup1'] ?? '');
        $terms    = !empty($_POST['terms']);

        $errors = [];
        if (empty($fname)) $errors[] = "First Name is required.";
        if (empty($lname)) $errors[] = "Last Name is required.";
        if (empty($username) || username_exists($username)) $errors[] = "Username already taken or empty.";
        if (!is_email($email) || email_exists($email)) $errors[] = "Invalid or already registered email.";
        if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
        if ($password !== $confirm) $errors[] = "Passwords do not match.";
        if (!$terms) $errors[] = "You must agree to the terms.";

        if (!empty($errors)) {
            $error_msg = urlencode(implode(' | ', $errors));
            wp_safe_redirect(add_query_arg('reg_error', $error_msg, wp_get_referer() ?: home_url('/sign-up/')));
            exit;
        }

        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            $error_msg = urlencode($user_id->get_error_message());
            wp_safe_redirect(add_query_arg('reg_error', $error_msg, wp_get_referer() ?: home_url('/sign-up/')));
            exit;
        }

        wp_update_user([
            'ID'         => $user_id,
            'first_name' => $fname,
            'last_name'  => $lname,
        ]);
        update_user_meta($user_id, 'gender', $gender);

        wp_safe_redirect(home_url('/sign-in/?reg_success=1'));
        exit;
    }

    // ====================== SIGNIN ======================
    if (isset($_POST['signin'])) {
        $login_input = sanitize_text_field($_POST['login'] ?? '');
        $password    = $_POST['password'] ?? '';
        $remember    = !empty($_POST['remember']);

        if (empty($login_input) || empty($password)) {
            wp_safe_redirect(add_query_arg('login_error', 'invalid_creds', wp_get_referer() ?: home_url('/sign-in/')));
            exit;
        }

        $user = get_user_by(is_email($login_input) ? 'email' : 'login', $login_input);

        if (!$user || !wp_check_password($password, $user->user_pass, $user->ID)) {
            wp_safe_redirect(add_query_arg('login_error', 'invalid_creds', wp_get_referer() ?: home_url('/sign-in/')));
            exit;
        }

        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, $remember);
        do_action('wp_login', $user->user_login, $user);

        wp_safe_redirect(home_url('/pill_reminder/'));
        exit;
    }
}