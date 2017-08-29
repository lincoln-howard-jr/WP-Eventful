<?php 
function setup () {
    global $wpdb;

    $table_name = $wpdb->prefix . "eventfulevent"; 

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "
      CREATE TABLE $table_name (
        id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
        day SMALLINT NOT NULL,
        month SMALLINT NOT NULL,
        year SMALLINT NOT NULL,
        hour SMALLINT,
        ampm VARCHAR(2),
        title VARCHAR(32) NOT NULL,
        description VARCHAR(140),
        PRIMARY KEY (id)
      ) $charset_collate;
    ";
    var_dump($sql);
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
setup ();