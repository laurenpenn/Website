<?php
/*
Plugin Name: Multisite Plugin Stats
Plugin URI: http://wordpress.org/extend/plugins/multisite-plugin-stats/
Description: A multisite plugin to show plugin activations across all your sites.
Version: 1.1
Author: Lew Goettner
Author URI: http://www.goettner.net
License: GPL2
Network: true

Copyright 2012	Lewis J. Goettner, III	(email : lew@goettner.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA	 02110-1301	 USA

*/

class MultisitePluginStats {
	function __construct() {
		//declare hooks
		add_action( 'network_admin_menu', array( &$this, 'add_menu' ) );
		add_action( 'admin_head', array( &$this, 'custom_css') );
		add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts' ) );
		add_action( 'plugins_loaded', array( &$this, 'localization' ) );
		
	}

	function MultisitePluginStats() {
		$this->__construct();
	}

	function localization() {
		load_plugin_textdomain('multisite_plugin_stats', false, '/multisite-plugin-stats/languages/');
	}

	function add_menu() {
		add_submenu_page( 'plugins.php', __('Plugin Statistics', 'multisite_plugin_stats'), __('Plugin Statistics', 'multisite_plugin_stats'), 'manage_network_options', 'multisite_plugin_stats', array( &$this, 'stats_page' ) );
	}
	
	function stats_page() {
		global $wpdb;
		
		// Check Permissions
		if (!is_site_admin())
			die('Not on my watch!');
			
		// Get a list of all the plugins
		$plugin_info = get_plugins();
		
		$active_plugins = array();
		
		// Get the network activated plugins
		$network_plugins = get_site_option( 'active_sitewide_plugins');

		// Initialize the name array
		$site_names = array();

		// Scan the sites for activation
		$blogs = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = {$wpdb->siteid} AND spam = 0 AND deleted = 0");

		if ($blogs) {
			foreach($blogs as $blog_id) {
				switch_to_blog($blog_id);

				// Get the name and add it to the list
				$site_names[$blog_id] = get_option('blogname');
				
				// Get active plugins
				$site_plugins = (array) get_option( 'active_plugins', array() );
				
				// Keep a Count
				foreach ($site_plugins as $plugin) {
					if (isset($active_plugins[$plugin])) {
						$active_plugins[$plugin][] = $blog_id;
					} else {
						$active_plugins[$plugin] = array($blog_id);
					}
				}
				
				restore_current_blog();
			}
		}
		
		?>
		
		<div class='wrap'>
		<div class="icon32" id="icon-plugins"><br></div>
		<h2><?php _e('Plugin Statistics', 'multisite_plugin_stats'); ?></h2>
		<h3><?php _e('Network Activated Plugins', 'multisite_plugin_stats'); ?> (<?php echo count($network_plugins); ?>)</h3>
		<ul class="plugin_list">
		<?php
			foreach ($network_plugins as $plugin => $etc) {
				echo '<li>' . $plugin_info[$plugin]["Name"] . '</li>';
				// Remove it from the list
				unset($plugin_info[$plugin]);
			}
		?>
		</ul>
		
		<h3><?php _e('Active Plugins', 'multisite_plugin_stats'); ?> (<?php echo count($active_plugins); ?>)</h3>
		<p><em><?php _e('Click the count to display the blogs using a given plugin.'); ?></em></p>
		<ul class="plugin_list">
		<?php
			$counter = 0;
			foreach ($active_plugins as $plugin => $blog_array) {

				echo '<li>' . $plugin_info[$plugin]["Name"] . ' ';
				echo '<a href="#" class="plugin_count" id="plugin_count_'.$counter.'">';
				echo '('.count($blog_array).' activations)</a>';
				echo '</li>';

				// List the sites
				echo '<ul class="plugin_site_list" style="display:none;" id="site_list_'.$counter.'">';
				foreach ($blog_array as $blog_id) {
					echo '<li>' . htmlspecialchars($site_names[$blog_id]) . '</li>';
				}
				echo '</ul>';
				
				// Remove it from the list
				unset($plugin_info[$plugin]);
				$counter++;
			}
		?>
		</ul>
		
		<h3><?php _e('Inactive Plugins', 'multisite_plugin_stats'); ?> (<?php echo count($plugin_info); ?>)</h3>
		<ul class="plugin_list">
		<?php
			foreach ($plugin_info as $plugin => $info) {
				echo '<li>' . $info["Name"] . '</li>';
			}
		?>
		</ul>
		
		</div> <!-- .wrap -->
	<?php
	}
	
	// Add a little style
	function custom_css() {
		echo '<style type="text/css">
						 .plugin_list li { margin-left: 2em; }
						 .plugin_site_list li { margin-left: 4em; }
					 </style>';
	}

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */	
	public function register_admin_scripts() {
	
		wp_register_script( 'multisite-plugin-stats-admin-script', plugins_url( 'multisite-plugin-stats/js/admin.js' ) );
		wp_enqueue_script( 'multisite-plugin-stats-admin-script' );
	
	} // end register_admin_scripts

}

$multisite_plugin_stats = new MultisitePluginStats();

?>