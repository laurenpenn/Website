<?php
/**
 * Plugin Name: My Snippetss
 * Plugin URI: http://justintadlock.com/archives/2009/12/03/my-snippets-wordpress-plugin
 * Description: Add custom snippets to your widget areas (sidebars) on a post-by-post basis.
 * Version: 0.1
 * Author: Justin Tadlock
 * Author URI: http://justintadlock.com
 *
 * The My Snippets plugin creates a meta box on the edit post screen in the WordPress admin
 * that allows users to add custom metadata to the post. This metadata is then displayed for
 * the singular view of the post on the frontend of the site by using the My Snippets widget.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package MySnippets
 */

/* Set constant path to the my_snippets plugin directory. */
define( MY_SNIPPETS_DIR, plugin_dir_path( __FILE__ ) );

/* Launch the plugin. */
add_action( 'plugins_loaded', 'my_snippets_plugin_init' );

/**
 * Initialize the plugin.  This function loads the required files needed for the plugin
 * to run in the proper order and adds needed functions to the required hooks.
 *
 * @since 0.1
 */
function my_snippets_plugin_init() {

	/* Load the translation of the plugin. */
	load_plugin_textdomain( 'my-snippets', false, 'my-snippets/languages' );

	/* Load global functions for the WordPress admin. */
	if ( is_admin() )
		require_once( MY_SNIPPETS_DIR . '/meta-box.php' );

	add_action( 'widgets_init', 'my_snippets_load_widgets' );
}

/**
 * Loads the widgets packaged with the My Snippets plugin.
 *
 * @since 0.1
 */
function my_snippets_load_widgets() {
	require_once( MY_SNIPPETS_DIR . '/widget-snippet.php' );
	register_widget( 'My_Snippets_Widget_Snippet' );
}

?>