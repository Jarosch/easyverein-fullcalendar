<?php
if (! defined('ABSPATH') ) {
    exit;
}

/**
 * Create database table
 * 
 * @package easyVerein
 * @return  void
 */
function easyVerein_createDatabase()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'easyVerein';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `option` varchar(100) NOT NULL,
            `value` varchar(500) NOT NULL,
            PRIMARY KEY  (`option`)
        ) $charset_collate;";
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

    // Create database
    easyVerein_createDatabase();
?>