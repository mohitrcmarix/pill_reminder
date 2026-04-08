<?php
function pill_reminders_install_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pill_reminders';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        reminder_title varchar(255) DEFAULT '' NOT NULL,
        user_number varchar(255),
        medicine_name varchar(255) NOT NULL,
        dose_value varchar(50) DEFAULT '' NOT NULL, -- e.g., '25'
        dose_type varchar(50) DEFAULT '' NOT NULL,  -- e.g., 'ml', 'spoon'
        frequency varchar(100) DEFAULT '' NOT NULL, -- e.g., 'daily'
        duration_type varchar(100) DEFAULT '' NOT NULL, -- e.g., '6-Month'
        duration_value varchar(50) DEFAULT '' NOT NULL,
        instruction varchar(100) DEFAULT '' NOT NULL, -- e.g., 'beforefood'
        from_date date NOT NULL,
        to_date date NOT NULL,
        reminder_times text NOT NULL, -- We will store the times[] array as a serialized string or JSON
        email varchar(100) DEFAULT '' NOT NULL,
        twilio_from_number varchar(50) DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        status varchar(5) default 1,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function pill_reminders_remove_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pill_reminders';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
