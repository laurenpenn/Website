<?php
/*
Plugin Name: BackWPup
Plugin URI: http://backwpup.com
Description: WordPress Backup and more...
Author: Daniel H&uuml;sken
Version: 2.1.17
Author URI: http://danielhuesken.de
Text Domain: backwpup
Domain Path: /lang/
*/

/*
	Copyright (C) 2012 Inpsyde GmbH  (email: info@inpsyde.com)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if (!defined('ABSPATH')) 
  die();
  
//Set plugin dirname
define('BACKWPUP_PLUGIN_BASEDIR', dirname(plugin_basename(__FILE__)));
define('BACKWPUP_PLUGIN_BASEURL', plugins_url(BACKWPUP_PLUGIN_BASEDIR));
//Set Plugin Version
define('BACKWPUP_VERSION', '2.1.17');
//Set Min Wordpress Version
define('BACKWPUP_MIN_WORDPRESS_VERSION', '3.1');
//Set User Capability
define('BACKWPUP_USER_CAPABILITY', 'export');
//Set useable destinations
if (!defined('BACKWPUP_DESTS')) {
	if (!function_exists('curl_init'))
		define('BACKWPUP_DESTS', 'FTP,MSAZURE');
	else
		define('BACKWPUP_DESTS', 'FTP,DROPBOX,SUGARSYNC,S3,GSTORAGE,RSC,MSAZURE');
}
//load Text Domain
load_plugin_textdomain('backwpup', false, BACKWPUP_PLUGIN_BASEDIR.'/lang');
//Load functions file
require_once(dirname(__FILE__).'/backwpup-functions.php');
//Plugin deactivate
register_deactivation_hook(__FILE__, 'backwpup_plugin_deactivate');
//Admin message
if (is_multisite())
	add_action('network_admin_notices', 'backwpup_admin_notice');
else
	add_action('admin_notices', 'backwpup_admin_notice');
//add cron intervals
add_filter('cron_schedules', 'backwpup_intervals',20);
//call activation settings
backwpup_plugin_activate();
//Check if plugin can activated
if (backwpup_env_checks()) {
	if (is_multisite()) {  //For multisite
		//add Menu
		add_action('network_admin_menu','backwpup_admin_menu');
		//add Dashboard widget
		add_action('wp_network_dashboard_setup', 'backwpup_add_dashboard');
		if (is_main_site())
			add_action('plugins_loaded','backwpup_plugin_activate');
		//Additional links on the plugin page
		add_filter('plugin_row_meta', 'backwpup_plugin_links',10,2);
	} else {
		//add Menu
		add_action('admin_menu', 'backwpup_admin_menu');
		//add Dashboard widget
		add_action('wp_dashboard_setup', 'backwpup_add_dashboard');
		//Additional links on the plugin page
		add_filter('plugin_action_links_'.BACKWPUP_PLUGIN_BASEDIR.'/backwpup.php', 'backwpup_plugin_options_link');
		add_filter('plugin_row_meta', 'backwpup_plugin_links',10,2);
	}
	//Actions for Cron job
	add_action('backwpup_cron', 'backwpup_cron',1);
	//add Admin Bar menu
	add_action('admin_bar_menu', 'backwpup_add_adminbar',100);
	//load ajax functions
	backwpup_load_ajax();
}