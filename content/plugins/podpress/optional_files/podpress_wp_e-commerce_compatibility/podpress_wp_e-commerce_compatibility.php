<?php
/*
Plugin Name: WP e-Commerce compatibility with podPress
Plugin URI: http://wordpress.org/extend/plugins/podpress/
Description: This is a plugin which will make it possible to use podPress and WP e-Commerce at the same time (without this plugin the RSS and ATOM feeds will not show podcast elements and settings while WP e-Commerce is active.)
Author: Tim Berger (ntm)
Version: 1.0
Author URI: http://undeuxoutrois.de/
Min WP Version: 2.2
Max WP Version: 3.4.1

Usage: Copy this file into the plugins folder of your blog (e.g. /wp-content/plugins/podpress_wp_e-commerce_compatibility/podpress_wp_e-commerce_compatibility.php). Open the plugins page of your blog and activate it.
*/

add_action('init', 'podpress_wp_e_commerce_compatibility', 9);
function podpress_wp_e_commerce_compatibility() {
	GLOBAL $wp_version;
	$wp_ecommerce_is_active = FALSE;
	if ( version_compare($wp_version, '3.0', '<') ) {
		$current_plugins = get_option('active_plugins');
	} else {
		$current_plugins = wp_get_active_and_valid_plugins();
	}
	if ( TRUE === is_array($current_plugins) ) {
		foreach ($current_plugins as $current_plugin) {
			if (FALSE !== stripos($current_plugin, 'wp-shopping-cart.php')) {
				$wp_ecommerce_is_active = TRUE;
				break;
			}
		}
	}
	if ( TRUE === $wp_ecommerce_is_active ) { 
		if ( ! defined( 'PODPRESS_WP_ECOMMERCE_IS_ACTIVE' ) ) { define( 'PODPRESS_WP_ECOMMERCE_IS_ACTIVE', TRUE ); }
	}
}
?>