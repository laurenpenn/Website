<?php 
/*
 *  Plugin Name: amMap
 *  Description: Loads amMap for use.
 *  Version: 0.1
 *  Author: Patrick Daly
 *  Author URI: http://developdaly.com/
 *  
 */

add_action( 'wp_enqueue_scripts', 'ammap_scripts' );

function ammap_scripts() {
	wp_enqueue_script( 'swfobject-ammap', get_bloginfo( 'siteurl') .'/wp-content/plugins/ammap/swfobject.js' );
}
