<?php
function pill_reminders_install_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pill_reminders';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) unsigned NOT NULL,
    post_id bigint(20) DEFAULT NULL,
    reminder_title varchar(255) DEFAULT '' NOT NULL,
    medicine_name varchar(255) NOT NULL,
    dose_value varchar(50) DEFAULT '' NOT NULL,
    dose_type varchar(50) DEFAULT '' NOT NULL,
    frequency varchar(100) DEFAULT '' NOT NULL,
    duration_type varchar(100) DEFAULT '' NOT NULL,
    duration_value varchar(50) DEFAULT '' NOT NULL,
    instruction varchar(100) DEFAULT '' NOT NULL,
    from_date date NOT NULL,
    to_date date NOT NULL,
    reminder_times longtext NOT NULL, -- JSON or serialized array
    email varchar(100) DEFAULT '' NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    status tinyint(1) DEFAULT 1,
    PRIMARY KEY  (id),
    KEY user_id (user_id),
    KEY email (email)
) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function pill_reminders_remove_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pill_reminders';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
