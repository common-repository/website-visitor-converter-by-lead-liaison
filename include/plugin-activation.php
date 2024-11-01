<?php

if ( ! function_exists( 'wvc_create_plugin_database_table' ) ) {
	function wvc_create_plugin_database_table() {
		global $wpdb;

		$tblname = 'wvc_codes';
		$tbename = 'wvc_emails';
		$wp_table = $wpdb->prefix . "$tblname ";
		$wpe_table = $wpdb->prefix . "$tbename ";
		$charset_collate = $wpdb->get_charset_collate();

		#Check to see if the table exists already, if not, then create it
		if( $wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table ) {

			$sql = "CREATE TABLE " . $wp_table . " (
			  id int(10) NOT NULL AUTO_INCREMENT,
			  code int(5) NOT NULL,
			  status varchar(3) NOT NULL,
			  limitation varchar(10),
			  used_times varchar(10),
			  valid_to int(10),
			  form_id int(10),
			  PRIMARY KEY (`id`)
			) $charset_collate;";
			
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}   

		#Check to see if the table exists already, if not, then create it
		if( $wpdb->get_var( "show tables like '$wpe_table'" ) != $wpe_table ) {

			$sql = "CREATE TABLE " . $wpe_table . " (
			  id int(10) NOT NULL AUTO_INCREMENT,
			  date int(10) NOT NULL,
			  form_id int(10) NOT NULL,
			  name varchar(100),
			  email varchar(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) $charset_collate;";
			
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}
	}
}
register_activation_hook( WVC_PLUGIN, 'wvc_create_plugin_database_table' );

