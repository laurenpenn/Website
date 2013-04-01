<?php
//if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	die();

global $wpdb;

//only uninstall if no BackWPup Version active
if ( ! class_exists( 'BackWPup' ) ) {
	//delete log folder and logs
	$log_folder = get_site_option( 'backwpup_cfg_logfolder' );
	if ( $dir = opendir( $log_folder ) ) {
		while ( FALSE !== ( $file = readdir( $dir ) ) ) {
			if ( is_file( $log_folder . $file ) && ( substr( $file, -8 ) == '.html.gz' || substr( $file, -5 ) == '.html' ) )
				unlink( $log_folder . $file );
		}
		closedir( $dir );
	}
	rmdir( $log_folder );
	//delete plugin options
	if ( is_multisite() )
		$wpdb->query( "DELETE FROM " . $wpdb->sitemeta . " WHERE meta_key LIKE '%backwpup_%' " );
	else
		$wpdb->query( "DELETE FROM " . $wpdb->options . " WHERE option_name LIKE '%backwpup_%' " );
}