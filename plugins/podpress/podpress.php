<?php
define('PODPRESS_VERSION', '8.8.9.2');
/*
Info for WordPress:
==============================================================================
Plugin Name: podPress
Version: 8.8.9.2
Plugin URI: http://www.mightyseek.com/podpress/
Description: The podPress plugin gives you everything you need in one easy plugin to use WordPress for Podcasting. Set it up in <a href="admin.php?page=podpress/podpress_feed.php">'podPress'->Feed/iTunes Settings</a>. If this plugin works for you, send us a comment.
Author: Dan Kuykendall (Seek3r)
Author URI: http://www.mightyseek.com/
Min WP Version: 2.1
Max WP Version: 3.1 RC 3

podPress - Podcasting made easy for WordPress
==============================================================================

This plugin makes it much easier and organized to use WordPress for Podcasting.

The plugin was created as a way for me to merge Garrick Van Buren's  WP-iPodCatter
and Martin Laine's Audio Player with some hacks I made to WordPress 2.0.
I had tweaked the player to have the [audio:filename.mp3] entry to drive the
whole podcasting need. In the rss2.php I had tweaked it to generate the
enclosure tag from it. So thats how the plugin took birth, and I have been adding
features to make the process cleaner over time.

Feel free to visit my website under www.mightyseek.com or contact me at
dan [at] kuykendall [dot] org

Have fun!

Installation:
==============================================================================
1. Upload the full directory into your wp-content/plugins directory
2. Activate it in the Plugin options
3. Edit or publish a post or click on Rebuild Sitemap on the Sitemap Administration Interface

Contributors:
==============================================================================
Developer					Dan Kuykendall (seek3r)	http://www.mightyseek.com/
Developer					David Maciejewski (macx)	http://www.macx.de/
Forum Support/BugBoy			Jeff Norris (iscfi)		http://www.iscifi.tv/
Maintenance / Development 2010		Tim Berger (ntm)		http://undeuxoutrois.de/

WP Audio Player				Martin Laine			http://www.1pixelout.net/
WP-iPodCatter				Garrick Van Buren		http://garrickvanburen.com/

Thanks to all contributors and bug reporters!
 
If you discover a problem with this plugin then report it in the WP.org "Plugins and Hacks" forum (http://wordpress.org/tags/podpress?forum_id=10) and tag your post with the tag "podpress".
 
Release History:
==============================================================================
Instead of maintaining the history in here, I'm just going to maintain it at
http://wordpress.org/extend/plugins/podpress/changelog/
or
http://www.mightyseek.com/podpress/changelog/

License:
==============================================================================

    Copyright 2006  Dan Kuykendall  (email : dan@kuykendall.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-107  USA
*/

// Pre-2.6 compatibility 
if ( ! defined( 'WP_CONTENT_URL' ) ) { define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' ); }
if ( ! defined( 'WP_CONTENT_DIR' ) ) { define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' ); }
if ( ! defined( 'WP_PLUGIN_URL' ) ) { define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' ); }
if ( ! defined( 'WP_PLUGIN_DIR' ) ) { define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' ); }
if ( ! defined( 'PODPRESS_URL' ) ) { define( 'PODPRESS_URL', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)) ); }
if ( ! defined( 'PODPRESS_DIR' ) ) { define( 'PODPRESS_DIR', WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__)) ); }

// the folder for config file for podPress like podpress_xspf_config.php
if ( ! defined( 'PODPRESS_OPTIONS_URL' ) ) { define( 'PODPRESS_OPTIONS_URL', WP_PLUGIN_URL.'/podpress_options' ); }
if ( ! defined( 'PODPRESS_OPTIONS_DIR' ) ) { define( 'PODPRESS_OPTIONS_DIR', WP_PLUGIN_DIR.'/podpress_options' ); }

// These two lines are old and could be replaced by the definitions above if the the code will be changed to the new constants above which should provide more indepence of the plugin folder name.
if (!defined('PLUGINDIR')) { define('PLUGINDIR', 'wp-content/plugins'); }
if (!defined('PODPRESSPLUGINDIR')) { define('PODPRESSPLUGINDIR', ABSPATH.PLUGINDIR); }

// to (de-)activate the Podango Integration (default: FALSE) - Leave this feature deactivated because the Podango platform and all coresponding feature are offline for a long time (since end of 2008) now.
if ( ! defined( 'PODPRESS_ACTIVATE_PODANGO_INTEGRATION' ) ) { define( 'PODPRESS_ACTIVATE_PODANGO_INTEGRATION', FALSE ); }
// to (de-)activate the 3rd party stats "feature" (default: FALSE)
if ( ! defined( 'PODPRESS_ACTIVATE_3RD_PARTY_STATS' ) ) { define( 'PODPRESS_ACTIVATE_3RD_PARTY_STATS', FALSE ); }

// You can log some of the procedures of podPress if you define this constant as true. The log file is podpress_log.dat.
if ( ! defined( 'PODPRESS_DEBUG_LOG' ) ) { define( 'PODPRESS_DEBUG_LOG', FALSE ); }

// maximum number of the additional podPress Feeds
if ( ! defined( 'PODPRESS_FEEDS_MAX_NUMBER' ) ) { define( 'PODPRESS_FEEDS_MAX_NUMBER', 5 ); }


// ####  podPress INIT ####
// some things like the widgets need be initiated before init see http://codex.wordpress.org/Plugin_API/Action_Reference#Actions_Run_During_an_Admin_Page_Request
add_action('plugins_loaded', 'podPress_init');
add_action('init', 'podPress_class_init');
// ####  podPress INIT ####

/**
* podPress_init - all intializing actions which needs to be executed before the WP init Action Hook (Widget init)
*
* @package podPress
* @since 8.8.8.6
*/
function podPress_init() {
	GLOBAL $podPress, $wp_version, $podPress_memoryUsage, $podPress_memoryIncrease, $podPress_feedHooksAdded;
	$podPress_memoryUsage = array();
	$podPress_memoryIncrease = 0;
	$podPress_feedHooksAdded = false;
	$GLOBALS['podPressPlayer'] = 0;  // Global counter of Players
	
	// Begin - import the XSPF Jukebox player configuration:
	// If you want to use custom skins for the XSPF players then edit the podpress_xspf_config-sample.php file and rename it to podpress_xspf_config.php, create a folder called podpress_options as a sub folder of the plugins folder e.g. /wp-content/plugins/podpress_options and copy this file to this folder.
	if (is_file(PODPRESS_OPTIONS_DIR.'/podpress_xspf_config.php')) {
		require_once(PODPRESS_OPTIONS_DIR.'/podpress_xspf_config.php');
	}
	// End - import the XSPF Jukebox player configuration

	if ( function_exists('load_plugin_textdomain') ) {
		if ( version_compare( $wp_version, '2.7', '>=' ) ) {
			load_plugin_textdomain( 'podpress', false, str_replace(WP_PLUGIN_DIR, '', PODPRESS_DIR.'/languages/') );
		} else {
			load_plugin_textdomain( 'podpress', PODPRESS_DIR.'/languages/' );
		}
	}

	if (!class_exists('podPress_class')) {
		require_once(PODPRESS_DIR.'/podpress_class.php');
		podPress_checkmem('podPress base class included');
		require_once(PODPRESS_DIR.'/podpress_functions.php');
		podPress_checkmem('podPress functions loaded');
		if($podPress_x = @parse_url($_SERVER['REQUEST_URI'])) {
			$podPress_x = $podPress_x['path'];
			if (strpos($podPress_x, 'crossdomain.xml')) {
				podPress_crossdomain();
			} elseif ($pos = strpos($podPress_x, 'podpress_trac')) {
				/* short circut the loading process for a simple redirect */
				podPress_checkmem('standard podPress class loaded', true);
				$podPress = new podPress_class;
				podPress_checkmem('standard podPress class loaded');
				podPress_statsDownloadRedirect($podPress_x);
				exit;
			}
			unset($podPress_x);
		}

		$customThemeFile = get_template_directory().'/podpress_theme.php';
		if(file_exists($customThemeFile)) {
			require_once($customThemeFile);
			podPress_checkmem('podPress custom theme file loaded');
		}
		require_once(PODPRESS_DIR.'/podpress_theme.php');
		podPress_checkmem('podPress core theme file loaded');
		if ( TRUE == isset($_GET['podpress_showmem']) AND 1 === $_GET['podpress_showmem'] ) {
			echo __('PHP has a memory_limit set to:', 'podpress').' '.ini_get('memory_limit').'<br/>';
		}

		podPress_checkmem('podPress start');

		if(file_exists(ABSPATH.PLUGINDIR.'/podpress.php')) {
			echo __('It appears you are upgrading podPress, but left the pre-4.x version of podpress.php file in the plugins directory. Please delete this file to continue.', 'podpress');
			exit;
		}
	}
	
	/* Widgets */
	if ( TRUE == version_compare($wp_version, '2.8', '>=') ) {
		// ntm: using the "new" Widget API 
		add_action('widgets_init', create_function('', 'return register_widget("podpress_feedbuttons");'));
		add_action('widgets_init', create_function('', 'return register_widget("podpress_xspfplayer");'));
	} else {
		add_action('widgets_init', 'podPress_loadWidgets');
	}
}

/**
* podPress_class_init - all intializing actions which should be executed after the WP init Action Hook / initiates the $podPress object
*
* @package podPress
* @since 8.8.8.6
*/
function podPress_class_init() {
	GLOBAL $podPress, $wp_version, $wp_importers;
	
	/*************************************************************/
	/* Create the podPress object */
	/*************************************************************/
	if ( FALSE == isset($podPress) OR FALSE == is_object($podPress) ) {
		$podpress_version_from_db = get_option('podPress_version');
		if ( FALSE === $podpress_version_from_db ) {
			// if the podPress version is not in the db then start the upgrade mechanism
			$podPress_inUpgrade = true;
		} else {
			if ( TRUE === version_compare($podpress_version_from_db, PODPRESS_VERSION, '<') ) {
				$podPress_inUpgrade = true;
			} else {
				$podPress_inUpgrade = false;
			}
		}
		if ($podPress_inUpgrade) {
			require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_upgrade_class.php');
			podPress_checkmem('podpress upgrade class loaded');
			$podPress = new podPress_class();
			$podPress = $podPress->update_podpress_class($podpress_version_from_db);
		}
		
		if ( TRUE == is_admin() ) {
			podPress_checkmem('podpress admin functions loaded', true);
			require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_admin_functions.php');
			podPress_checkmem('podpress admin functions loaded');
			if(isset($_GET['page'])) {
				$podPress_adminPage = $_GET['page'];
			} elseif(isset($_POST['podPress_submitted'])) {
				$podPress_adminPage = 'podpress/podpress_'.$_POST['podPress_submitted'].'.php';
			} else {
				$podPress_adminPage = 'usedefault';
			}
			switch($podPress_adminPage) {
				case 'podpress/podpress_general.php':
					require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_admin_general_class.php');
					podPress_checkmem('admin general code loaded');
				break;
				case 'podpress/podpress_feed.php':
					require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_admin_feed_class.php');
					podPress_checkmem('admin feed code loaded');
				break;
				case 'podpress/podpress_players.php':
					require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_admin_player_class.php');
					podPress_checkmem('admin player code loaded');
				break;
				case 'podpress/podpress_stats.php':
					require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_admin_stats_class.php');
					podPress_checkmem('admin stats code loaded');
				break;
				case 'podpress/podpress_podango.php':
					require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_admin_podango_class.php');
					podPress_checkmem('admin podango code loaded');
				break;
				default:
					require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_admin_class.php');
					podPress_checkmem('admin code loaded');
				break;
			}
			$podPress = new podPressAdmin_class();
			if($podPress->settings['enablePodangoIntegration']) {
				podPress_checkmem('PodangoAPI code loaded', true);
				require_once(ABSPATH.PLUGINDIR.'/podpress/podango-api.php');
				$podPress->podangoAPI = new PodangoAPI ($podPress->settings['podangoUserKey'], $podPress->settings['podangoPassKey']);
				if(!empty($podPress->settings['podangoDefaultPodcast'])) {
					$podPress->podangoAPI->defaultPodcast = $podPress->settings['podangoDefaultPodcast'];
				}
				if(!empty($podPress->settings['podangoDefaultTranscribe'])) {
					$podPress->podangoAPI->defaultTranscribe = (int)$podPress->settings['podangoDefaultTranscribe'];
				}
				podPress_checkmem('PodangoAPI code loaded');
			}
		} else {
			podPress_checkmem('standard podPress class loaded', true);
			$podPress = new podPress_class;
			podPress_checkmem('standard podPress class loaded');
		}
	}

	/*************************************************************/
	/* Register all the actions and filters */
	/*************************************************************/
	/* Add podpress data to each post */
	if(!podPress_WPVersionCheck()) {
		// WP 1.5 legacy vars support
		if(isset($table_prefix) && !isset($wpdb->prefix)) {
			$wpdb->prefix = $table_prefix;
		}
		if(isset($tablecomments) && !isset($wpdb->comments)) {
			$wpdb->comments = $tablecomments;
		}
	}

	add_action( 'pre_get_posts', 'podPress_feed_content_filtering' );

	/* Add podpress data to each post */
	if ( TRUE == version_compare($wp_version, '2.0.0', '<') ) {
	//~ if (podPress_WPVersionCheck()) {
		add_action('the_posts', array(&$podPress, 'the_posts'));
	} else {
		add_filter('the_posts', array(&$podPress, 'the_posts'));
	}
	
	// ntm: where is do_action('xmlrpc-mw_ ? 
	add_action('xmlrpc-mw_newPost', array(&$podPress, 'xmlrpc_post_addMedia'));
	add_action('xmlrpc-mw_editPost', array(&$podPress, 'xmlrpc_post_addMedia'));
	
	add_filter('posts_join', array(&$podPress, 'posts_join'));
	add_filter('posts_where', array(&$podPress, 'posts_where'));
	
	/* stuff that goes in the display of the Post */
	add_filter('the_content', array(&$podPress, 'insert_content'));
	add_filter('get_the_excerpt', array(&$podPress, 'insert_the_excerpt'), 1);
	add_filter('the_excerpt', array(&$podPress, 'insert_the_excerptplayer'));
	
	// ntm: This is deactivated because of the unknown purpose and reported problems (http://wordpress.org/support/topic/podpress_get_attached_file-overwriting-attempts-to-include-thumbnail-in-feed)
	// The only purpose I can imagine is to prevent WP from adding non-podPress enclosure to Feed items. Since 8.8.5 podPress has a different filter to achieve that (see podPress_dont_print_nonpodpress_enclosures).
	//~ add_filter('get_attached_file', 'podPress_get_attached_file');
	//~ add_filter('wp_get_attachment_metadata', 'podPress_wp_get_attachment_metadata');
		
	/* stuff that goes in the HTML header */
	if ( TRUE == version_compare($wp_version, '2.7', '>=') ) {
		if (FALSE === is_admin()) {
			add_action('wp_print_scripts', 'podpress_print_frontend_js');
			add_action('wp_print_styles', 'podpress_print_frontend_css');
		}
		add_action('wp_head', 'podPress_print_feed_links_to_header');
	} else {
		add_action('wp_head', 'podPress_wp_head');
	}
	add_action('wp_footer', 'podPress_wp_footer');
	add_action('switch_theme', 'podPress_switch_theme');

	/* misc stuff */
	// the dashboard widget:
	if ( TRUE === $podPress->settings['enableStats'] ) {
		if ( (TRUE == isset($podPress->settings['disabledashboardwidget']) AND FALSE === $podPress->settings['disabledashboardwidget']) OR FALSE == isset($podPress->settings['disabledashboardwidget']) ) {
			if ( TRUE == version_compare($wp_version, '2.7', '>=') ) { // for WP >= 2.7 add the stats overview as a dashboard widget
				add_action('wp_dashboard_setup', 'podpress_wp_dashboard_setup');
			} else { // for older versions via the activity_box_end hook
				add_action('activity_box_end', 'podPress_activity_box');
			}
		}
		add_action('template_redirect', 'podPress_statsDownloadRedirect');
	}
		
	add_filter('get_the_guid', 'podPress_get_the_guid');

	/* stuff that goes into all feeds */
	// ntm: that seems to be unnecessary because that function is called in every do_feed function (see above)
	//~ if(is_feed()) {
		//~ podPress_addFeedHooks();
	//~ }

	/* stuff for premium podcasts */
	if ( isset($podPress->settings['enablePremiumContent']) AND TRUE === $podPress->settings['enablePremiumContent'] ) {
		require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_premium_functions.php');
		podPress_checkmem('premium functions included');
		add_action('wp_login', 'podpress_adddigestauth');
	}

	/* stuff that goes into setting up the site for podpress */
	if ( TRUE === is_admin() ) {
		add_action('activate_podpress/podpress.php', array(&$podPress, 'activate'));
		add_action('deactivate_podpress/podpress.php', array(&$podPress, 'deactivate'));

		/* if this is an admin page, run the function to add podpress tab to options menu */
		if ( TRUE == version_compare($wp_version, '2.7', '>=') ) {
			add_action('admin_print_scripts', 'podpress_print_admin_js');
			add_action('admin_print_scripts-podpress/podpress_stats.php', 'podpress_print_admin_statistics_js');
			add_action('admin_print_styles', 'podpress_print_admin_css');
			add_action('admin_print_styles-podpress/podpress_stats.php', 'podpress_print_admin_statistics_css');
			add_action('admin_print_styles-index.php', 'podpress_print_admin_statistics_css');
		} else {
			add_action('admin_head', 'podPress_print_admin_js_and_css_old_wp');
		}		
		add_action('admin_head', 'podPress_admin_head');
		add_action('admin_menu', 'podPress_add_menu_page');
		add_action('admin_footer', 'podPress_admin_footer');
			
		/* Adds a custom section to the "advanced" Post and Page edit screens */
		if ( TRUE == version_compare($wp_version, '2.5', '>=') ) {
			add_action('admin_menu', 'add_podpress_form_box_for_modern_wp');
		} else {
			add_action('simple_edit_form', array(&$podPress, 'post_form'));
			add_action('edit_form_advanced', array(&$podPress, 'post_form'));
			add_action('edit_page_form', array(&$podPress, 'page_form'));
		}
		add_action('save_post', array(&$podPress, 'post_edit'));
			
		/* stuff that goes in the category */
		add_action('create_category', array(&$podPress, 'edit_category'));
		add_action('edit_category_form', array(&$podPress, 'edit_category_form'));
		add_action('edit_category', array(&$podPress, 'edit_category'));
		//add_action('delete_category', array(&$podPress, 'delete_category'));

		/* stuff for editing settings */
		// ntm: saving the settings of the settings pages of podPress
		if(isset($_POST['podPress_submitted']) && method_exists($podPress, 'settings_'.$_POST['podPress_submitted'].'_save')) {
			$funcnametouse = 'settings_'.$_POST['podPress_submitted'].'_save';
			$podPress->$funcnametouse();
		}
		
		// ntm: there is no importer function ('import_dispatch') in the podPress files
		// $wp_importers['podcast'] = array (__('Podcast RSS2', 'podpress'), __('podPress import of posts from a Podcast RSS2 feed.', 'podpress'), array(&$podPress, 'import_dispatch'));
		// if(function_exists('register_importer')) {
			// register_importer('podcast', __('Podcast RSS2'), __('Import posts from an RSS2 Podcast feed'), array (&$podPress, 'import_dispatch'));
		// }
	}
	
	if ( function_exists('add_feed') ) {
		if ( is_array($podPress->settings['podpress_feeds']) AND FALSE == empty($podPress->settings['podpress_feeds']) ) {
			foreach ($podPress->settings['podpress_feeds'] as $feed) {
				if ( TRUE === $feed['use'] AND FALSE == empty($feed['slug']) ) {
					add_feed($feed['slug'], 'podPress_do_dyn_podcast_feed');
				}
			}
		}
		add_feed('playlist.xspf', 'podPress_do_feed_xspf');
	}
	
	remove_action('do_feed_rss', 'do_feed_rss', 10, 1);
	add_action('do_feed_rss', 'podPress_do_dyn_podcast_feed', 1, 1);
	add_action('do_feed_rss2', 'podPress_do_dyn_podcast_feed', 1, 1);
	remove_action('do_feed_atom', 'do_feed_atom', 10, 1);
	add_action('do_feed_atom', 'podPress_do_dyn_podcast_feed', 1, 1);
		
	// ntm: that seems to be unnecessary because that function is called in every do_feed function (see above)
	// and $podPress->feed_getCategory(); seems not to exist
	//if ( is_feed() ) {
		//podPress_addFeedHooks();
		//$podPress->feed_getCategory();
	//}
}
	
function podPress_add_menu_page() {
	GLOBAL $podPress, $wp_version;
	if(podPress_WPVersionCheck('2.0.0')) {
		$permission_needed = $podPress->requiredAdminRights;
	} else {
		$permission_needed = 1;
	}
	if (function_exists('add_menu_page')) {
		if($podPress->settings['enableStats'] == true) {
			$starting_point = 'podpress_stats';
		} else {
			$starting_point = 'podpress_feed';
		}
		if ( version_compare( $wp_version, '2.7', '>=' ) ) {
			$menutitle = __('podPress', 'podpress');
			add_menu_page('podPress',  $menutitle, $permission_needed, 'podpress/'.$starting_point.'.php', '', PODPRESS_URL.'/images/podpress_icon_r2_v2_16.png');
		} else {
			add_menu_page('podPress', 'podPress', $permission_needed, 'podpress/'.$starting_point.'.php');
		}
	}
	if (function_exists('add_submenu_page')) {
		if($podPress->settings['enableStats'] == true) {
			$starting_point = 'podpress_stats';
		} else {
			$starting_point = 'podpress_feed';
		}

		if($podPress->settings['enableStats'] == true) {
			add_submenu_page('podpress/'.$starting_point.'.php', __('podPress - Statistics', 'podpress'), __('Statistics', 'podpress'), $permission_needed, 'podpress/podpress_stats.php');
		}
		add_submenu_page('podpress/'.$starting_point.'.php', __('podPress - Feed/iTunes Settings', 'podpress'), __('Feed/iTunes Settings', 'podpress'), $permission_needed, 'podpress/podpress_feed.php');
		add_submenu_page('podpress/'.$starting_point.'.php', __('podPress - General Settings', 'podpress'), __('General Settings', 'podpress'), $permission_needed, 'podpress/podpress_general.php');

		if($podPress->settings['contentPlayer'] != 'disabled') {
			add_submenu_page('podpress/'.$starting_point.'.php', __('podPress - Player Settings', 'podpress'), __('Player Settings', 'podpress'), $permission_needed, 'podpress/podpress_players.php');
		}

		if($podPress->settings['enablePodangoIntegration'] == true) {
			add_submenu_page('podpress/'.$starting_point.'.php', __('podPress - Podango Settings', 'podpress'), __('Podango Settings', 'podpress'), $permission_needed, 'podpress/podpress_podango.php');
		}
	}
}
	
function podPress_switch_theme() {
	GLOBAL $podPress;
	$podPress->settings['compatibilityChecks']['themeTested'] = false;
	$podPress->settings['compatibilityChecks']['wp_head'] = false;
	$podPress->settings['compatibilityChecks']['wp_footer'] = false;
	podPress_update_option('podPress_config', $podPress->settings);
}

// for WP 2.7+
function podpress_print_frontend_js() {
	wp_register_script( 'podpress_frontend_script',  PODPRESS_URL.'/js/podpress.js' );
	wp_enqueue_script( 'podpress_frontend_script' );
	
	// ntm: this way of loading a localized JS scripts is probably not very elegant but it works in WP version older than 2.3
	// I know that since WP 2.3 the function wp_localize_script() exists and when it is decided to raise the minimum WP requirement of this plugin then this method will be used.
	require_once(PODPRESS_DIR.'/podpress_js_i18.php');
	podpress_print_localized_frontend_js_vars();
	
	podpress_print_js_vars();
}
// for WP 2.7+
function podpress_print_frontend_css() {
	if (file_exists(get_template_directory().'/podpress.css')) {
		wp_register_style( 'podpress_frontend_styles',  get_template_directory_uri().'/podpress.css' );
	} else {
		wp_register_style( 'podpress_frontend_styles',  PODPRESS_URL.'/podpress.css' );
	}
	wp_enqueue_style( 'podpress_frontend_styles' );
}
// for WP version < 2.7
function podPress_wp_head() {
	// frontend header
	echo '<script type="text/javascript" src="'.PODPRESS_URL.'/js/podpress.js"></script>'."\n";
	
	// ntm: this way of loading a localized Js scripts is probably not very elegant but it works in WP version older than 2.3
	// I know that since WP 2.3 the function wp_localize_script() exists and when it is decided to raise the minimum WP requirement of this plugin then this method will be used.
	require_once(PODPRESS_DIR.'/podpress_js_i18.php');
	podpress_print_localized_frontend_js_vars();

	podpress_print_js_vars();
	if (file_exists(get_template_directory().'/podpress.css')) {
		echo '<link rel="stylesheet" href="'.get_template_directory_uri().'/podpress.css" type="text/css" />'."\n";
	} else {
		echo '<link rel="stylesheet" href="'.PODPRESS_URL.'/podpress.css" type="text/css" />'."\n";
	}
	
	podPress_print_feed_links_to_header();
}

// the dashboard widget for all WP versions
function podPress_activity_box() {
	GLOBAL $podPress, $wpdb, $wp_version;
	if ( TRUE === $podPress->settings['enableStats'] ) {
		if ( TRUE == version_compare($wp_version, '2.8', '>=') ) {
			// get the plugins version information via the WP plugins version check
			if ( TRUE == version_compare($wp_version, '2.9', '>=') ) {
				$versioninfo = get_site_transient( 'update_plugins' );
			} else {
				$versioninfo = get_transient( 'update_plugins' );
			}
			// If there is a new version then there is a 'response'. This is the method from the plugins page. 
			if ( FALSE !== isset($versioninfo->response[plugin_basename(__FILE__)]->new_version) ) {
				echo '<p class="podpress_notice"><a href="http://wordpress.org/extend/plugins/podpress/" target="_blank">'.__('a new podPress version is available', 'podpress').'</a></p>';
			}
		} else {
			// in older versions use the old version check
			if ( TRUE == version_compare($wp_version, '2.7', '>=') ) {
				echo '<a href="http://www.mightyseek.com/podpress/#download" target="_new"><img src="http://www.mightyseek.com/podpress_downloads/versioncheck.php?current='.PODPRESS_VERSION.'" alt="'.__('Checking for updates... Failed', 'podpress').'" border="0" /></a>'."\n";
			} else {
				echo '<h3>podPress&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.mightyseek.com/podpress/#download" target="_new"><img src="http://www.mightyseek.com/podpress_downloads/versioncheck.php?current='.PODPRESS_VERSION.'" alt="'.__('Checking for updates... Failed', 'podpress').'" border="0" /></a></h3>'."\n";
			}
		}
		if($podPress->settings['statLogging'] == 'Full' || $podPress->settings['statLogging'] == 'FullPlus') {
			$where = $podPress->wherestr_to_exclude_bots();
			$query_string="SELECT method, COUNT(DISTINCT id) as downloads FROM ".$wpdb->prefix."podpress_stats ".$where."GROUP BY method ORDER BY method ASC";
			$stats = $wpdb->get_results($query_string);
			echo '			<fieldset><legend>'.sprintf(__('Statistics Summary (%1$s/%2$s)', 'podpress'), __('Full', 'podpress'),__('Full+', 'podpress')).'</legend>'."\n";
			if (0 < count($stats)) {
				$feed = intval($stats[0]->downloads);
				$play = intval($stats[1]->downloads);
				$web = intval($stats[2]->downloads);
				$total = $feed + $web + $play;
				echo '			<table class="the-list-x podpress_statistics_summary_table">'."\n";
				echo '				<thead><tr><th>'.__('Feed', 'podpress').'</th><th>'.__('Web', 'podpress').'</th><th>'.__('Play', 'podpress').'</th><th>'.__('Total', 'podpress').'</th></tr></thead>'."\n";
				echo '				<tbody><tr><td>'.$feed.'</td><td>'.$web.'</td><td>'.$play.'</td><td>'.$total.'</td></tr></tbody>'."\n";
				echo '			</table>'."\n";
			} else {
				echo '<p>'.__('No downloads yet.','podpress').'</p>';
			}
			echo '			</fieldset>'."\n";
		} else {
			$sql = "SELECT SUM(total) as cnt_total, SUM(feed) as cnt_feed, SUM(web) as cnt_web, SUM(play) as cnt_play FROM ".$wpdb->prefix."podpress_statcounts";
			$stats = $wpdb->get_results($sql);
			if($stats) {
				echo '			<fieldset><legend>'.__('Statistics Summary', 'podpress').'</legend>'."\n";
				echo '			<table class="the-list-x podpress_statistics_summary_table">'."\n";
				echo '				<thead><tr><th>'.__('Feed', 'podpress').'</th><th>'.__('Web', 'podpress').'</th><th>'.__('Play', 'podpress').'</th><th>'.__('Total', 'podpress').'</th></tr></thead>'."\n";
				echo '				<tbody><tr><td>'.intval($stats[0]->cnt_feed).'</td><td>'.intval($stats[0]->cnt_web).'</td><td>'.intval($stats[0]->cnt_play).'</td><td>'.intval($stats[0]->cnt_total).'</td></tr></tbody>'."\n";
				echo '			</table></fieldset>'."\n";
			}
		}
	}
}

// adds the dasboard widget for WP >= 2.7
function podpress_wp_dashboard_setup() { 
	wp_add_dashboard_widget( 'podpress_wp_dashboard_widget', __('podPress Stats', 'podpress'), 'podPress_activity_box' );
}

// for WP 2.7+
function podpress_print_admin_statistics_js() {
	wp_register_script( 'podpress_admin_statistics_script',  PODPRESS_URL.'/js/podpress_admin_statistics.js' );
	wp_enqueue_script( 'podpress_admin_statistics_script' );
}
// for WP 2.7+
function podpress_print_admin_statistics_css() {
	wp_register_style( 'podpress_admin_statistics_styles',  PODPRESS_URL.'/podpress_admin_statistics.css' );
	wp_enqueue_style( 'podpress_admin_statistics_styles' );
}
// for WP 2.7+
function podpress_print_admin_js() { // ntm: some of these scripts are not necessary on all admin pages
	GLOBAL $pagenow, $wp_version;
	$page_with_podPress = Array('post.php', 'page.php', 'post-new.php', 'page-new.php', 'categories.php', 'admin.php', 'edit-tags.php');
	if ( in_array($pagenow, $page_with_podPress) ) {
		wp_register_script( 'podpress_js',  PODPRESS_URL.'/js/podpress.js' );
		wp_register_script( 'podpress_admin_js',  PODPRESS_URL.'/js/podpress_admin.js' );
		wp_enqueue_script( 'podpress_js' );
		wp_enqueue_script( 'podpress_admin_js' );
		
		// ntm: this way of loading a localized Js scripts is probably not very elegant but it works in WP version older than 2.3
		// I know that since WP 2.3 the function wp_localize_script() exists and when it is decided to raise the minimum WP requirement of this plugin then this method will be used.
		require_once(PODPRESS_DIR.'/podpress_admin_js_i18.php');
		podpress_print_localized_admin_js_vars();
		
		podpress_print_js_vars();
	}
		
	if ( 'admin.php' == $pagenow AND 'podpress/podpress_feed.php' == $_GET['page'] ) {
		wp_register_script( 'podpress_jquery_ui',  PODPRESS_URL.'/js/jquery/podpress_jquery142_ui_feedssettings.js' );
	}
	if ( 'widgets.php' == $pagenow ) {
		if ( TRUE == version_compare($wp_version, '2.9', '>=') ) {
			wp_register_script( 'podpress_jquery_ui',  PODPRESS_URL.'/js/jquery/podpress_jquery142_ui_widgetssettings.js' );
		} elseif ( TRUE == version_compare($wp_version, '2.8', '>=') AND TRUE == version_compare($wp_version, '2.9', '<') ) {
			wp_register_script( 'podpress_jquery_ui',  PODPRESS_URL.'/js/jquery/podpress_jquery142_ui_widgetssettings_wp28.js' );
		} elseif (TRUE == version_compare($wp_version, '2.8', '<')) {
			wp_register_script( 'podpress_jquery_ui',  PODPRESS_URL.'/js/jquery/podpress_jquery142_ui_widgetssettings_pre_wp28.js' );
		}
	}
	if ( ('admin.php' == $pagenow AND 'podpress/podpress_feed.php' == $_GET['page']) OR 'widgets.php' == $pagenow ) {
		wp_register_script( 'podpress-jquery-ui-core',  PODPRESS_URL.'/js/jquery/jquery-1.4.2.min.js' );
		wp_register_script( 'podpress_jquery_init',  PODPRESS_URL.'/js/jquery/podpress_jquery142_init.js' );
		wp_register_script( 'jquery-ui-accordion-dialog',  PODPRESS_URL.'/js/jquery/jquery-ui-1.8.5.accordion_dialog.min.js' );
		wp_enqueue_script( 'podpress-jquery-ui-core' );
		wp_enqueue_script( 'podpress_jquery_init' );
		wp_enqueue_script( 'jquery-ui-accordion-dialog' );
		wp_enqueue_script( 'podpress_jquery_ui' );
	}
}
// for WP 2.7+
function podpress_print_admin_css() {
	wp_register_style( 'podpress_admin_styles',  PODPRESS_URL.'/podpress_admin_wp27plus.css' );
	wp_enqueue_style( 'podpress_admin_styles' );
	GLOBAL $pagenow;
	if ( 'admin.php' == $pagenow AND $_GET['page'] == 'podpress/podpress_players.php'  ) {
		// since 8.8.5.3: styles for the 1PixelOut player with listen wrapper
		podpress_print_frontend_css();
	}
	if ( ('admin.php' == $pagenow AND 'podpress/podpress_feed.php' == $_GET['page']) OR 'widgets.php' == $pagenow ) {
		wp_register_style( 'podpress_jquery_ui',  PODPRESS_URL.'/js/jquery/css/custom-theme/jquery-ui-1.8.5.custom.css' );
		wp_enqueue_style( 'podpress_jquery_ui' );
	}
}

// for WP version < 2.7
function podPress_print_admin_js_and_css_old_wp() {
	Global $pagenow, $wp_version;
	$page_with_podPress = Array('post.php', 'page.php', 'post-new.php', 'page-new.php', 'categories.php', 'admin.php');
	if ( in_array($pagenow, $page_with_podPress) ) {
	
		// ntm: this way of loading a localized Js scripts is probably not very elegant but it works in WP version older than 2.3
		// I know that since WP 2.3 the function wp_localize_script() exists and when it is decided to raise the minimum WP requirement of this plugin then this method will be used.
		require_once(PODPRESS_DIR.'/podpress_admin_js_i18.php');
		podpress_print_localized_admin_js_vars();
		
		podpress_print_js_vars();
		
		echo '<script type="text/javascript" src="'.PODPRESS_URL.'/js/podpress.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.PODPRESS_URL.'/js/podpress_admin.js"></script>'."\n";
	}
	$page_with_podPress = Array('post.php', 'page.php', 'post-new.php', 'page-new.php', 'categories.php', 'admin.php', 'widgets.php');
	if ( in_array($pagenow, $page_with_podPress) ) {
		//~ if ( function_exists('wp_admin_tiger_css') ) {
		if ( TRUE == version_compare($wp_version, '2.5', '>=') AND TRUE == version_compare($wp_version, '2.7', '<') ) {
			$admincss = 'podpress_admin_tigercheck.css';
		} else {
			$admincss = 'podpress_admin.css';
		}
		echo '<link rel="stylesheet" href="'.PODPRESS_URL.'/'.$admincss.'" type="text/css" />'."\n";
	}
	
	if ( ('admin.php' == $pagenow AND 'podpress/podpress_feed.php' == $_GET['page']) OR 'widgets.php' == $pagenow ) {
		echo '<script type="text/javascript" src="'.PODPRESS_URL.'/js/jquery/jquery-1.4.2.min.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.PODPRESS_URL.'/js/jquery/podpress_jquery142_init.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.PODPRESS_URL.'/js/jquery/jquery-ui-1.8.5.accordion_dialog.min.js"></script>'."\n";
		if ( 'widgets.php' == $pagenow ) {
			echo '<script type="text/javascript" src="'.PODPRESS_URL.'/js/jquery/podpress_jquery142_ui_widgetssettings_pre_wp28.js"></script>'."\n";
		} else {
			echo '<script type="text/javascript" src="'.PODPRESS_URL.'/js/jquery/podpress_jquery142_ui_feedssettings.js"></script>'."\n";
		}
		echo '<link rel="stylesheet" href="'.PODPRESS_URL.'/js/jquery/css/custom-theme/jquery-ui-1.8.5.custom.css" type="text/css" />'."\n";
	}
	if ( ('admin.php' == $pagenow AND 'podpress/podpress_players.php' == $_GET['page']) OR 'widgets.php' == $pagenow ) {
		echo '<link rel="stylesheet" href="'.PODPRESS_URL.'/podpress.css'.'" type="text/css" />'."\n";
	}
	if ( 'admin.php' == $pagenow AND 'podpress/podpress_stats.php' == $_GET['page'] ) {
		echo '<script type="text/javascript" src="'.PODPRESS_URL.'/js/podpress_admin_statistics.js"></script>'."\n";
		echo '<link rel="stylesheet" href="'.PODPRESS_URL.'/podpress_admin_statistics.css'.'" type="text/css" />'."\n";
	}
}

function podpress_print_js_vars() {
	GLOBAL $podPress;
	// Set the player settings which are not part of $podPress->settings['player']. This for instance important after an podPress resp. 1PixelOut player update (if there are new settings)
	foreach ($podPress->PlayerDefaultSettings() as $key => $value) {
		if ( FALSE === isset($podPress->settings['player'][$key]) ) {
			$podPress->settings['player'][$key] = $value;
		}
	}
	$playerOptions = '';
	if($podPress->settings['enablePodangoIntegration'] || (TRUE == isset($podPress->settings['mp3Player']) AND 'podango' == $podPress->settings['mp3Player']) ) {
		$mp3playerswffile = 'var podPressPlayerFile = "podango_player.swf";'."\n";
		// create the parameter string for the mp3 player
		foreach($podPress->settings['player'] as $key => $val) {
			if ( 'listenWrapper' !== $key AND 'overwriteTitleandArtist' !== $key ) {
				$val = str_replace('#', '0x', $val);
				$playerOptions .= '&amp;' . $key . '=' . rawurlencode($val);
			}
		}
		$mp3playerOptionsStr = 'var podPressMP3PlayerOptions = "'.$playerOptions.'&amp;";'."\n";
	} else {
		$mp3playerswffile = '';
		$mp3playerOptionsStr = '';
		// create the parameter string for the mp3 player
		foreach($podPress->settings['player'] as $key => $val) {
			if ( 'listenWrapper' !== $key AND 'overwriteTitleandArtist' !== $key ) {
				$val = str_replace('#', '', $val);
				$playerOptions .= $key . ':"' . rawurlencode($val).'", ';
				$podpupplayerOptions .= '	podPressPopupPlayerOpt["' . $key . '"] = "' . rawurlencode($val).'";'."\n";
			}
		}
		echo '<script type="text/javascript" src="'.PODPRESS_URL.'/players/1pixelout/1pixelout_audio-player.js"></script>'."\n";
		echo '<script type="text/javascript">//<![CDATA['."\n";
		echo '	var podPressPlayerFile = "1pixelout_player.swf";'."\n"; // this is for the Play in Popup function, too!
		echo '	var podPressPopupPlayerOpt = new Object();'."\n";
		echo $podpupplayerOptions;
		echo '	podpressAudioPlayer.setup("'.PODPRESS_URL.'/players/1pixelout/" + podPressPlayerFile, {'.$playerOptions.' pagebg:"FFFFFF", transparentpagebg:"yes", encode: "no"} );'."\n";
		echo '//]]></script>'."\n";
	}
	echo '<script type="text/javascript">//<![CDATA['."\n";
	echo 'var podPressBlogURL = "'.get_option('siteurl').'/";'."\n";
	echo 'var podPressBackendURL = "'.PODPRESS_URL.'/";'."\n";
	if ( FALSE == isset($podPress->settings['videoPreviewImage']) OR empty($podPress->settings['videoPreviewImage']) ) {
		echo 'var podPressDefaultPreviewImage = podPressBackendURL+"images/vpreview_center.png";'."\n";
	} else {
		echo 'var podPressDefaultPreviewImage = "'.$podPress->settings['videoPreviewImage'].'";'."\n";
	}
	echo $mp3playerswffile;
	echo $mp3playerOptionsStr;
	if (TRUE == isset($podPress->settings['player']['listenWrapper']) AND TRUE == $podPress->settings['player']['listenWrapper']) {
		echo 'var podPressMP3PlayerWrapper = true;'."\n";
	} else {
		echo 'var podPressMP3PlayerWrapper = false;'."\n";
	}
	if (TRUE == isset($podPress->settings['cortado_version']) AND 'cortado_signed' == $podPress->settings['cortado_version']) {
		echo 'var podPress_cortado_signed = true;'."\n";
	} else {
		echo 'var podPress_cortado_signed = false;'."\n";
	}
	if ('yes' == $podPress->settings['player']['overwriteTitleandArtist']) { // should the 1Pixelout player try to show the ID3 data or the custom values
		echo 'var podPressOverwriteTitleandArtist = true;'."\n";
	} else {
		echo 'var podPressOverwriteTitleandArtist = false;'."\n";
	}
	echo 'var podPressText_PlayNow = "'.__('Play Now', 'podpress').'";'."\n";
	echo 'var podPressText_HidePlayer = "'.__('Hide Player', 'podpress').'";'."\n";
	echo '//]]></script>'."\n";
}

/**
* podPress_print_feed_links_to_header - prints the links of the podPress Feeds to the header of each blog page
*
* @package podPress
* @since 8.8.9.2
*/
function podPress_print_feed_links_to_header() {
	GLOBAL $podPress;
	if ( is_array($podPress->settings['podpress_feeds']) AND FALSE == empty($podPress->settings['podpress_feeds']) ) {
		echo "\n";
		$blogname = get_bloginfo('name');
		foreach ($podPress->settings['podpress_feeds'] as $feed) {
			if ( isset($feed['use_headerlink']) AND TRUE === $feed['use_headerlink'] AND TRUE === $feed['use'] AND FALSE == empty($feed['slug']) ) {//TRUE === $feed['use_headerlink'] 
				switch ( $feed['feedtitle'] ) {
					default:
					case 'append' :
						$feedtitle = $blogname.' &#187; '.$feed['name'];
					break;
					case 'blognameastitle' :
						$feedtitle = $blogname;
					break;
					case 'feednameastitle' :
						$feedtitle = $feed['name'];
					break;
				}
				switch ($feed['feedtype']) {
					default :
					case 'rss' :
						echo '<link rel="alternate" type="application/rss+xml" title="'.stripslashes($feedtitle).'" href="'.get_feed_link($feed['slug']).'" />'."\n";
					break;
					case 'atom' :
						echo '<link rel="alternate" type="application/atom+xml" title="'.stripslashes($feedtitle).'" href="'.get_feed_link($feed['slug']).'" />'."\n";
					break;
				}
			}
		}
	}	
}

function podPress_admin_head() {
	GLOBAL $podPress, $action;
	if(!$podPress->settings['compatibilityChecks']['themeTested']) {
		$podPress->settings['compatibilityChecks']['themeTested'] = true;
		podPress_update_option('podPress_config', $podPress->settings);
	}
	if(!$podPress->settings['compatibilityChecks']['wp_head']) {
		$podPress->settings['compatibilityChecks']['wp_head'] = true;
		podPress_update_option('podPress_config', $podPress->settings);
	} else {
		$podPress->settings['compatibilityChecks']['wp_head'] = true;
	}

	// ntm: old podPress version check. It checks only at myghtyseek.com and not at wordpress.org for new versions !!! and only on the plugins.php page
	if ((strpos($_SERVER['REQUEST_URI'], 'plugins.php') !== false) && (podPress_remote_version_check() == 1)) {
		echo "<script type='text/javascript' src='" . PODPRESS_URL . "/js/prototype/prototype-1.4.0.js'></script>\n";
		$alert = "\n";
		$alert .= "\n<script type='text/javascript'>";
		$alert .= "\n//<![CDATA[";
		$alert .= "\nfunction alertNewPodPressVersion() {";
		$alert .= "\n	pluginname = 'podPress';";
		$alert .= "\n	allNodes = document.getElementsByClassName('name');";
		$alert .= "\n	for(i = 0; i < allNodes.length; i++) {";
		$alert .= "\n			var regExp=/<\S[^>]*>/g;";
		$alert .= "\n	    temp = allNodes[i].innerHTML;";
		$alert .= "\n	    if (temp.replace(regExp,'') == pluginname) {";
		$alert .= "\n		    Element.setStyle(allNodes[i].getElementsByTagName('a')[0], {color: '#f00'});";
		$alert .= "\n		    new Insertion.After(allNodes[i].getElementsByTagName('strong')[0],'<br/><small>" .  __('new version available', 'podpress') . "</small>');";
		$alert .= "\n	  	}";
		$alert .= "\n	}";
		$alert .= "\n}";
		$alert .= "\naddLoadEvent(alertNewPodPressVersion);";
		$alert .= "\n//]]>";
		$alert .= "\n</script>";
		$alert .= "\n";
		echo $alert;
	}
}

function podPress_admin_footer() {
	GLOBAL $podPress;
	if(!$podPress->settings['compatibilityChecks']['themeTested']) {
		$podPress->settings['compatibilityChecks']['themeTested'] = true;
		podPress_update_option('podPress_config', $podPress->settings);
	}
	if(!$podPress->settings['compatibilityChecks']['wp_footer']) {
		$podPress->settings['compatibilityChecks']['wp_footer'] = true;
		podPress_update_option('podPress_config', $podPress->settings);
	} else {
		$podPress->settings['compatibilityChecks']['wp_footer'] = true;
	}
}

function podPress_wp_footer() {
	GLOBAL $podPress;
	if ( $podPress->settings['enableFooter'] ) {
		echo '<div id="podPress_footer">'.__('Podcast powered by', 'podpress').' <a href="http://wordpress.org/extend/plugins/podpress/" title="'.__('podPress, a plugin for podcasting with WordPress', 'podpress').'"><strong>podPress v'.PODPRESS_VERSION.'</strong></a></div>';
	}
}

function podPress_get_the_guid($guid) {
	GLOBAL $post, $wpdb;
	if ( empty($guid) ) {
		$guid = get_option('siteurl') . '/?p=' . $post->ID;
		if ( is_object($post) && !empty($post->ID) ) {
			$wpdb->query("UPDATE ".$wpdb->posts." SET guid = '". $guid ."' WHERE ID=".$post->ID);
		}
	}
	return $guid;
}

// ntm: This is deactivated because of the unknown purpose and reported problems (http://wordpress.org/support/topic/podpress_get_attached_file-overwriting-attempts-to-include-thumbnail-in-feed)
function podPress_get_attached_file($file, $id = '') {
	if ( is_feed() ) { return ''; }
	return $file;
}
// ntm: This is deactivated because of the unknown purpose and reported problems (http://wordpress.org/support/topic/podpress_get_attached_file-overwriting-attempts-to-include-thumbnail-in-feed)
function podPress_wp_get_attachment_metadata($data, $id = '') {
	if ( is_feed() ) { return ''; }
	return $data;
}

function podPress_crossdomain() {
	// ntm: Which purpose has this function? Shouldn't the crossdomain file on the server/domain with the mp3 files? And isn't it to sloppy and dangerous to allow acces from all domains?
	// http://www.adobe.com/devnet/articles/crossdomain_policy_file_spec.html
	header("HTTP/1.0 200 OK");
	header('Content-type: text/xml; charset=' . get_bloginfo('charset'), true);
	echo '<?xml version="1.0"?>'."\n";
	echo '<!DOCTYPE cross-domain-policy SYSTEM "http://www.macromedia.com/xml/dtds/cross-domain-policy.dtd">'."\n";
	echo '<cross-domain-policy>'."\n";
	echo '	<allow-access-from domain="*" />'."\n";
	echo '</cross-domain-policy>'."\n";
	exit;
}

// only for compatibility with WP versions older than WP 2.3
function podPress_get_cat_ID_by_nicename($cat_nicename='general') {
	global $wpdb;
	$cid = $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories WHERE category_nicename='$cat_nicename'");
	return $cid?$cid:1;	// default to cat 1
}

/**
* podpress_get_exts_from_filetypes - extracts the file name extensions from the strings of the allowed file types
*
* @package podPress
* @since 8.8.7 beta 3
*
* @param Array $filetypes
*
* @return mixed $allowed_extensions
*/
function podpress_get_exts_from_filetypes($filetypes=array()) {
	if ( TRUE == is_array($filetypes) AND FALSE === empty($filetypes) ) {
		$allowed_extensions = Array();
		foreach ($filetypes as $filetype) {
			$allowed_extensions[] = end(explode('_', $filetype));
		}
		return $allowed_extensions;
	} else {
		return false;
	}
}

/**
* podpress_get_IDs_of_posts_with_allowed_exts - retrieves the IDs of posts with podPress attachments of certain file tapes from the db
*
* @package podPress
* @since 8.8.8.1
*
* @param Array $allowed_ext - file name extensions of the allowed file types e.g array('mp3', 'm4a')
*
* @return mixed $IDs as an array OR FALSE if it was not possible to retrieve IDs from the db
*/
function podpress_get_IDs_of_posts_with_allowed_exts($allowed_ext = array()) {
	global $wpdb;
	$IDs = Array();
	if ( FALSE == empty($allowed_ext) AND TRUE == is_array($allowed_ext) ) {
		$querystring = "SELECT post_id, meta_value FROM ".$wpdb->postmeta." WHERE meta_key = 'podPressMedia'";
		$meta_values = $wpdb->get_results($querystring, ARRAY_A);
		$nr_meta_values = count($meta_values);
		foreach ($meta_values as $meta_value) {
			$unserialized_data_sets = @unserialize($meta_value['meta_value']);
			if ( is_array($unserialized_data_sets) AND FALSE == empty($unserialized_data_sets) ) {
				foreach ($unserialized_data_sets AS $unserialized_data) {
					if ( TRUE == isset($unserialized_data['URI']) AND 4 < strlen($unserialized_data['URI']) AND TRUE == in_array(substr(strtolower($unserialized_data['URI']), -3), $allowed_ext) ) {
						$IDs[] = $meta_value['post_id'];
					}
				}
			}
		}
		$IDs = array_unique($IDs);
	}
	if ( FALSE == empty($IDs) ) {
		return $IDs;
	} else {
		return FALSE;
	}
}

/**
* podPress_feed_content_filtering - is called via the Action Hook pre_get_posts and influences the WP Query if the query is for a feed
*
* @package podPress
* @since 8.8.8.1
*
* @param Array $query - WP Query object - see http://codex.wordpress.org/Function_Reference/WP_Query and http://codex.wordpress.org/Plugin_API/Action_Reference#Advanced_Actions
*/	
function podPress_feed_content_filtering( $query ) {
	global $podPress, $wp_version, $podpress_allowed_ext;
	if ( $query->is_feed ) {

		$is_podpress_feed = FALSE;
		$feedslug = $query->query_vars['feed'];
		foreach ( $podPress->settings['podpress_feeds'] as $feed ) {
			if ( $feedslug === $feed['slug'] ) {
				$is_podpress_feed = TRUE;
				break;
			}
		}
		if ( FALSE === $is_podpress_feed ) {
			if ( empty($query->query_vars['cat']) AND empty($query->query_vars['tag']) AND FALSE == empty($query->query_vars['category_name']) ) {
				if ( TRUE == version_compare($wp_version, '2.3', '>=') ) {
					$idObj = get_category_by_slug( $query->query_vars['category_name'] ); 
					$cat_id = $idObj->term_id;
				} else {
					$cat_id = podPress_get_cat_ID_by_nicename( $query->query_vars['category_name'] );
				}
			}
			$categorysettings = get_option('podPress_category_'.$cat_id);
		}
		if ( TRUE === $is_podpress_feed ) {
			// podPress Custom Feeds
			// get the list of allowed file extensions
			$podpress_allowed_ext = podpress_get_exts_from_filetypes($feed['FileTypes']);
			if ( (is_array($podpress_allowed_ext) AND FALSE === empty($podpress_allowed_ext)) OR (is_array($feed['inclCategories']) AND FALSE === empty($feed['inclCategories'])) ) {
				if ( is_array($podpress_allowed_ext) AND FALSE === empty($podpress_allowed_ext) ) {
					// get a list of IDs of posts which have podPress attachment of an allowed type
					$post_ids = podpress_get_IDs_of_posts_with_allowed_exts($podpress_allowed_ext);
					if ( is_array($post_ids) AND FALSE == empty($post_ids) ) {
						// insert the post IDs into the WP Query
						$query->set('post__in', $post_ids);
					} else {
						$query->set('post__in', Array(0));
					}
				}
				
				if ( is_array($feed['inclCategories']) AND FALSE === empty($feed['inclCategories']) ) {
					$query->query_vars['category__in'] = $feed['inclCategories'];
				}
			} else {
				if ( isset($feed['show_only_podPress_podcasts']) AND FALSE === $feed['show_only_podPress_podcasts'] ) {
					// the feed should not only posts with podPress attachments
					define('PODPRESS_PODCASTSONLY', FALSE);
				} else {
					// get only posts with podPress attachments (that is how was in older versions)
					define('PODPRESS_PODCASTSONLY', TRUE);
				}
			}
			if ( 'torrent' === $feed['slug'] ) {
				define('PODPRESS_TORRENTCAST', true);
			}
			if ( TRUE === $feed['premium'] ) {
				GLOBAL $cache_lastpostmodified;
				unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
				$cache_lastpostmodified = date('Y-m-d h:i:s', time()+36000);
				podPress_addFeedHooks();
				define('PREMIUMCAST', true);
				require_once( PODPRESS_DIR.'/podpress_premium_functions.php' );
				podPress_validateLogin();
			} else {
				podPress_addFeedHooks();
			}
		} elseif ( isset($categorysettings) AND FALSE !== $categorysettings AND isset($categorysettings['categoryCasting']) AND 'true' == $categorysettings['categoryCasting'] ) {
			// CategoryCasting Feeds
			// get the list of allowed file extensions
			$podpress_allowed_ext = podpress_get_exts_from_filetypes($categorysettings['FileTypes']);
			if (is_array($podpress_allowed_ext) AND FALSE === empty($podpress_allowed_ext)) {
				// get a list of IDs of posts which have podPress attachment of an allowed type
				$post_ids = podpress_get_IDs_of_posts_with_allowed_exts($podpress_allowed_ext);
				if ( is_array($post_ids) AND FALSE == empty($post_ids) ) {
					// insert the post IDs into the WP Query
					$query->set('post__in', $post_ids);
				} else {
					$query->set('post__in', Array(0));
				}
			} else {
				// get only posts with podPress attachments
				if ( isset($categorysettings['show_only_podPress_podcasts']) AND FALSE === $categorysettings['show_only_podPress_podcasts'] ) {
					// the feed should not only posts with podPress attachments
					define('PODPRESS_PODCASTSONLY', FALSE);
				} else {
					// get only posts with podPress attachments (that is how was in older versions)
					define('PODPRESS_PODCASTSONLY', TRUE);
				}				
			}
			podPress_addFeedHooks();
		} else {
			// RSS, RSS2, ATOM
			podPress_addFeedHooks();
		}
	}
}

/**
* podPress_do_dyn_podcast_feed - loads the right feed template
*
* @package podPress
* @since 8.8.8
*
* @param bool $withcomments - add a link to the comments of a post below the content or not
*/
function podPress_do_dyn_podcast_feed($withcomments) {
	GLOBAL $wp_query, $wp_version, $podPress;
	$is_podpress_feed = FALSE;
	$feedslug = get_query_var('feed');
	foreach ($podPress->settings['podpress_feeds'] as $feed) {
		if ( $feedslug === $feed['slug'] ) {
			$is_podpress_feed = TRUE;
			break;
		}
	}
	if ( FALSE === $is_podpress_feed ) {
		// RSS, RSS2, ATOM or CategoryCasting Feeds
		$feed['feedtype'] = $feedslug;
	}
	
	switch ($feed['feedtype']) {
		default :
		case 'rss' :
		case 'rss2' :
		case 'feed' :
			if (!function_exists('do_feed_rss2')) {
				load_template(ABSPATH.'wp-rss2.php');
			} else {
				do_feed_rss2($withcomments);
			}
		break;
		case 'atom' :
			if (!function_exists('do_feed_atom') OR TRUE == version_compare('2.3', $wp_version,'>')) { 
				load_template(ABSPATH.PLUGINDIR.'/podpress/wp-atom1.php');
			} else {
				do_feed_atom($withcomments);
			}
		break;
	}
}

function podPress_do_feed_xspf() {
	GLOBAL $wp_query;
	podPress_addFeedHooks();
	define('PODPRESS_PODCASTSONLY', true);
	$wp_query->get_posts();
	podPress_xspf_playlist();
}

function podPress_addFeedHooks() {
	GLOBAL $podPress, $podPress_feedHooksAdded;
	if(!$podPress_feedHooksAdded) {
		require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_feed_functions.php');
		podPress_checkmem('podPress feed functions loaded');
		add_filter('option_blogname', 'podPress_feedblogname');
		add_filter('option_blogdescription', 'podPress_feedblogdescription');
		add_filter('option_rss_language', 'podPress_feedblogrsslanguage');
		add_filter('option_rss_image', 'podPress_feedblogrssimage');

		/* stuff that goes in the rss feed */
		add_action('the_content_rss', array(&$podPress, 'insert_content'));
		add_action('rss2_ns', 'podPress_rss2_ns');
		add_action('rss2_head', 'podPress_rss2_head');
		// Remove all enclosures which were not added with podPress. They will be added again at the end of the action rss2_item.
		add_filter('rss_enclosure', 'podPress_dont_print_nonpodpress_enclosures');
		add_action('rss2_item', 'podPress_rss2_item');

		/* stuff that goes in the atom feed */
		add_action('atom_head', 'podPress_atom_head');
		// Remove all enclosures which were not added with podPress. They will be added again at the end of the action atom_entry.
		add_filter('atom_enclosure', 'podPress_dont_print_nonpodpress_enclosures');
		add_action('atom_entry', 'podPress_atom_entry');
		$podPress_feedHooksAdded = true;
	}
}

if (!function_exists('podPress_shutdown')) {
	function podPress_shutdown() {
		GLOBAL $podPress_memoryUsage, $podPress_memoryIncrease;
		if ( TRUE == isset($_GET['podpress_showmem']) AND 1 === $_GET['podpress_showmem'] ) {
			echo sprintf(__('Total podPress mem: %1$s out of a total %2$s', 'podpress'), podPress_bytes($podPress_memoryIncrease), podPress_bytes(memory_get_usage()))."<br/>\n";
		}

		if ( TRUE == isset($_GET['podpress_showmem']) AND 2 === $_GET['podpress_showmem'] ) {
			html_print_r($podPress_memoryUsage);
		} elseif ( TRUE == isset($_GET['podpress_showmem']) AND 3 === $_GET['podpress_showmem'] ) {
			comment_print_r($podPress_memoryUsage);
		}
	}
	add_action( 'shutdown', 'podPress_shutdown', 1);
}

// adding the podPress box to the post / page editor pages
function add_podpress_form_box_for_modern_wp() {
	add_meta_box( 'podPressstuff', __('podPress - podcasting settings of this post', 'podpress'), 'podpress_box_content_post', 'post', 'advanced' );
	add_meta_box( 'podPressstuff', __('podPress - podcasting settings of this page', 'podpress'), 'podpress_box_content_page', 'page', 'advanced' );
}
function podpress_box_content_post() {
	global $podPress;
	echo "\n<!-- podPress dbx for modern WP versions - post -->\n";
	$podPress->post_form_wp25plus('post');
	echo "\n<!-- podPress dbx for modern WP versions - post -->\n";
}
function podpress_box_content_page() {
	global $podPress;
	echo "\n<!-- podPress dbx for modern WP versions - page -->\n";
	$podPress->post_form_wp25plus('page');
	echo "\n<!-- podPress dbx for modern WP versions - page -->\n";
}

// some helper functions
if (!function_exists('memory_get_usage')) {
	unset($_GET['podpress_showmem']);
	function memory_get_usage() { return 0; }
	if(!function_exists('podPress_bytes')) {
		function podPress_bytes($i) { return $i; }
		function podPress_checkmem() { return; }
	}
} elseif (!function_exists('podPress_bytes')) {
	function podPress_bytes($input, $dec=0) {
		$unim = array('B','KB','MB','GB','TB','PB');
		$value = round($input, $dec);
		$i=0;
		while ($value>1024) { $value /= 1024; $i++; }
		return round($value, $dec).$unim[$i]; 
	}

	function podPress_checkmem($txt, $start = false) {
		GLOBAL $podPress_memoryUsage, $podPress_memoryIncrease;
		if (isset($_GET['podpress_showmem'])) {
			$mem = memory_get_usage();
			if($start) {
				$podPress_memoryUsage[$txt] = array('start'=>$mem);
			} else {
				if(!is_array($podPress_memoryUsage[$txt])) {
					if(count($podPress_memoryUsage) > 0) {
						$prevval = end($podPress_memoryUsage);
						$prevval = $prevval['finish'];
					} else {
						$prevval = $mem;
					}
					$podPress_memoryUsage[$txt] = array('start'=>$prevval, 'fromprev'=>'X');
					unset($prevval);
				}
				$podPress_memoryUsage[$txt]['finish'] = $mem;
				$increase = $mem - $podPress_memoryUsage[$txt]['start'];
				$podPress_memoryUsage[$txt]['increase'] = $increase;
				$podPress_memoryIncrease = $podPress_memoryIncrease+$increase;
				if ($_GET['podpress_showmem'] == 1) {
					echo sprintf(__('%1$s: Increased memory %2$s for a total of %3$s', 'podpress'), $txt, podPress_bytes($increase), podPress_bytes($mem))."<br/>\n";
				}
			}
		}
	}
}
?>