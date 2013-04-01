<?php
/*
Plugin Name: Bitlove Scripts for podPress
Plugin URI: http://wordpress.org/extend/plugins/podpress/
Description: This plugins adds a Bitlove script to this blog which helps to use Bitlove and podPress. For more information about Bitlove and this script see <a href="http://bitlove.org/help/podcaster/widget" tagret="_blank" title="URL to the bitlove.org page">http://bitlove.org/</a>.
Author: Tim Berger (ntm)
Version: 1.0
Author URI: http://undeuxoutrois.de/
Min WP Version: 2.6
Max WP Version: 3.4

Usage: Copy this file into the plugins folder of your blog (e.g. /wp-content/plugins/bitlove_scripts_for_podpress/bitlove_scripts_for_podpress.php). Open the plugins page of your blog and activate it.
*/

add_action('wp_enqueue_scripts', 'include_bitlove_scripts_for_podpress');
function include_bitlove_scripts_for_podpress() {
	if ( TRUE === is_ssl() ) {
		wp_register_script( 'bitlove-scripts-for-podpress', 'https://bitlove.org/widget/podpress.js');
	} else {
		wp_register_script( 'bitlove-scripts-for-podpress', 'http://bitlove.org/widget/podpress.js');
	}
	wp_enqueue_script( 'bitlove-scripts-for-podpress' );
}
?>