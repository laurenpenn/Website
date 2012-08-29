<?php
/*
Plugin Name: LeagueManager
Plugin URI: http://wordpress.org/extend/plugins/leaguemanager/
Description: Manage and present sports league results.
Version: 3.8
Author: Kolja Schleich

Copyright 2008-2009  Kolja Schleich  (email : kolja.schleich@googlemail.com)

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
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Loading class for the WordPress plugin LeagueManager
* 
* @author 	Kolja Schleich
* @package	LeagueManager
* @copyright 	Copyright 2008-2009
*/
class LeagueManagerLoader
{
	/**
	 * plugin version
	 *
	 * @var string
	 */
	var $version = '3.7';
	
	
	/**
	 * database version
	 *
	 * @var string
	 */
	var $dbversion = '3.7';
	
		
	/**
	 * check if bridge is active
	 *
	 * @var boolean
	 */
	var $bridge = false;
		
		
	/**
	 * admin Panel object
	 *
	 * @var object
	 */
	var $adminPanel;


	/**
	 * constructor
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		global $leaguemanager, $lmStats, $wpdb, $championship;
		$wpdb->show_errors();
		$this->loadOptions();
		$this->defineConstants();
		$this->defineTables();
		$this->loadTextdomain();
		$this->loadLibraries();

		register_activation_hook(__FILE__, array(&$this, 'activate') );
			
		if (function_exists('register_uninstall_hook'))
			register_uninstall_hook(__FILE__, array(&$this, 'uninstall'));

		add_action( 'widgets_init', array(&$this, 'registerWidget') );
		// Start this plugin once all other plugins are fully loaded
		add_action( 'plugins_loaded', array(&$this, 'initialize') );
		
		$leaguemanager = new LeagueManager( $this->bridge );
		$championship = new LeagueManagerchampionship();
		$lmStats = new LeagueManagerStats();
		
		if ( is_admin() )
			$this->adminPanel = new LeagueManagerAdminPanel();
	}
	function LeagueManagerLoader()
	{
		$this->__construct();
	}
	
		
	/**
	 * initialize plugin
	 *
	 * @param none
	 * @return void
	 */
	function initialize()
	{
		// Add the script and style files
		add_action('wp_head', array(&$this, 'loadScripts') );
		add_action('wp_print_styles', array(&$this, 'loadStyles') );
		// Add TinyMCE Button
		add_action( 'init', array(&$this, 'addTinyMCEButton') );
		add_filter( 'tiny_mce_version', array(&$this, 'changeTinyMCEVersion') );
	}
		
	
	/**
	 * register Widget
	 */
	function registerWidget()
	{
		register_widget('LeagueManagerWidget');
	}


	/**
	 * define constants
	 *
	 * @param none
	 * @return void
	 */
	function defineConstants()
	{
		if ( !defined( 'WP_CONTENT_URL' ) )
			define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
		if ( !defined( 'WP_PLUGIN_URL' ) )
			define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
		if ( !defined( 'WP_CONTENT_DIR' ) )
			define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ( !defined( 'WP_PLUGIN_DIR' ) )
			define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
			
		define( 'LEAGUEMANAGER_VERSION', $this->version );
		define( 'LEAGUEMANAGER_DBVERSION', $this->dbversion );
		define( 'LEAGUEMANAGER_URL', WP_PLUGIN_URL.'/leaguemanager' );
		define( 'LEAGUEMANAGER_PATH', WP_PLUGIN_DIR.'/leaguemanager' );
	}
	
		
	/**
	 * define database tables
	 *
	 * @param none
	 * @return void
	 */
	function defineTables()
	{
		global $wpdb;
		$wpdb->leaguemanager = $wpdb->prefix . 'leaguemanager_leagues';
		$wpdb->leaguemanager_teams = $wpdb->prefix . 'leaguemanager_teams';
		$wpdb->leaguemanager_matches = $wpdb->prefix . 'leaguemanager_matches';
		$wpdb->leaguemanager_stats = $wpdb->prefix . 'leaguemanager_stats';
	}
	
		
	/**
	 * load libraries
	 *
	 * @param none
	 * @return void
	 */
	function loadLibraries()
	{
		global $lmShortcodes, $lmAJAX;
		
		// Global libraries
		require_once (dirname (__FILE__) . '/lib/core.php');
		require_once (dirname (__FILE__) . '/lib/ajax.php');
		require_once (dirname (__FILE__) . '/lib/stats.php');
		require_once (dirname (__FILE__) . '/lib/shortcodes.php');
		require_once (dirname (__FILE__) . '/lib/widget.php');
		require_once (dirname (__FILE__) . '/functions.php');
		require_once (dirname (__FILE__) . '/lib/championship.php');
		$this->loadSports();
		$lmAJAX = new LeagueManagerAJAX();

		if ( is_admin() ) {
			require_once (dirname (__FILE__) . '/lib/image.php');
			require_once (dirname (__FILE__) . '/admin/admin.php');	
		}
			
		if ( file_exists(WP_PLUGIN_DIR . '/projectmanager/projectmanager.php') ) {
			$p = get_option('projectmanager');
			if (version_compare($p['version'], '2.4.7', '>=')) {
				global $lmBridge;
				require_once(dirname (__FILE__) . '/lib/bridge.php');
				$lmBridge = new LeagueManagerBridge();
				$this->bridge = true;
			}
		}
		$lmShortcodes = new LeagueManagerShortcodes($this->bridge);
	}
		

	/**
	 * load sport types
	 *
	 * @param none
	 * @return void
	 */
	function loadSports()
	{
		$dir = LEAGUEMANAGER_PATH."/sports";
		if ( $handle = opendir($dir) ) {
			while ( false !== ($file = readdir($handle)) ) {
				$file_info = pathinfo($dir.'/'.$file);
				$file_type = $file_info['extension'];
				if ( $file != "." && $file != ".." && !is_dir($file) && substr($file, 0,1) != "."  && $file_type == 'php' )  {
					require_once($dir.'/'.$file);
				}
			}
		}
	}


	/**
	 * load options
	 *
	 * @param none
	 * @return void
	 */
	function loadOptions()
	{
		$this->options = get_option('leaguemanager');
	}
		
		
	/**
	 * get options
	 *
	 * @param boolean $index (optional)
	 * @return void
	 */
	function getOptions($index = false)
	{
		if ( $index )
			return $this->options[$index];
			return $this->options;
	}
	
		
	/**
	 * load textdomain
	 *
	 * @param none
	 * @return void
	 */
	function loadTextdomain()
	{
		global $leaguemanager;
		
		$textdomain = $this->getOptions('textdomain');
		if ( !empty($textdomain) ) {
			$locale = get_locale();
			$path = dirname(__FILE__) . '/languages';
			$domain = 'leaguemanager';
			$mofile = $path . '/'. $domain . '-' . $textdomain . '-' . $locale . '.mo';
			
			if ( file_exists($mofile) ) {
				load_textdomain($domain, $mofile);
				return true;
			}
		}
		
		load_plugin_textdomain( 'leaguemanager', false, 'leaguemanager/languages' );
	}
	

	/**
	 * load scripts
	 *
	 * @param none
	 * @return void
	 */
	function loadScripts()
	{
		wp_register_script( 'leaguemanager', LEAGUEMANAGER_URL.'/leaguemanager.js', array('jquery', 'sack', 'thickbox'), LEAGUEMANAGER_VERSION );
		wp_print_scripts('leaguemanager');
		?>
		<script type="text/javascript">
		//<![CDATA[
		LeagueManagerAjaxL10n = {
			blogUrl: "<?php bloginfo( 'wpurl' ); ?>", pluginPath: "<?php echo LEAGUEMANAGER_PATH; ?>", pluginUrl: "<?php echo LEAGUEMANAGER_URL; ?>", requestUrl: "<?php echo LEAGUEMANAGER_URL ?>/ajax.php", Edit: "<?php _e("Edit"); ?>", Post: "<?php _e("Post"); ?>", Save: "<?php _e("Save"); ?>", Cancel: "<?php _e("Cancel"); ?>", pleaseWait: "<?php _e("Please wait..."); ?>", Revisions: "<?php _e("Page Revisions"); ?>", Time: "<?php _e("Insert time"); ?>", Options: "<?php _e("Options") ?>", Delete: "<?php _e('Delete') ?>"
	 	}
		//]]>
		</script>
		<?php
	}
		
		
	/**
	 * load styles
	 *
	 * @param none
	 * @return void
	 */
	function loadStyles()
	{
		wp_enqueue_style('thickbox');
		wp_enqueue_style('leaguemanager', LEAGUEMANAGER_URL . "/style.css", false, '1.0', 'screen');
		
		echo "\n<style type='text/css'>";
		if ( !empty($this->options['colors']['headers']) )
		echo "\n\ttable.leaguemanager th { background-color: ".$this->options['colors']['headers']." }";

		if ( !empty($this->options['colors']['rows']['main']) )
		echo "\n\ttable.leaguemanager tr { background-color: ".$this->options['colors']['rows']['main']." }";

		if ( !empty($this->options['colors']['rows']['alternate']) )
		echo "\n\ttable.leaguemanager tr.alternate { background-color: ".$this->options['colors']['rows']['alternate']." }";

		if ( !empty($this->options['colors']['rows']['ascend']) )
		echo "\n\ttable.standingstable tr.ascend, table.standingstable tr.ascend.alternate { background-color: ".$this->options['colors']['rows']['ascend']." }";

		if ( !empty($this->options['colors']['rows']['descend']) )
		echo "\n\ttable.standingstable tr.descend, table.standingstable tr.descend.alternate { background-color: ".$this->options['colors']['rows']['descend']." }";

		if ( !empty($this->options['colors']['rows']['relegation']) )
		echo "\n\ttable.standingstable tr.relegation, table.standingstable tr.relegation.alternate { background-color: ".$this->options['colors']['rows']['relegation']." }";

		if ( !empty($this->options['colors']['rows']['alternate']) )
		echo "\n\ttable.crosstable th, table.crosstable td { border: 1px solid ".$this->options['colors']['rows']['alternate']."; }";
		echo "\n</style>";
	}
		
		
	/**
	 * add TinyMCE Button
	 *
	 * @param none
	 * @return void
	 */
	function addTinyMCEButton()
	{
		// Don't bother doing this stuff if the current user lacks permissions
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) return;
		
		// Check for LeagueManager capability
		if ( !current_user_can('manage_leagues') ) return;
		
		// Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {
			add_filter("mce_external_plugins", array(&$this, 'addTinyMCEPlugin'));
			add_filter('mce_buttons', array(&$this, 'registerTinyMCEButton'));
		}
	}
	function addTinyMCEPlugin( $plugin_array )
	{
		$plugin_array['LeagueManager'] = LEAGUEMANAGER_URL.'/admin/tinymce/editor_plugin.js';
		return $plugin_array;
	}
	function registerTinyMCEButton( $buttons )
	{
		array_push($buttons, "separator", "LeagueManager");
		return $buttons;
	}
	function changeTinyMCEVersion( $version )
	{
		return ++$version;
	}
		
		
	/**
	 * Activate plugin
	 *
	 * @param none
	 */
	function activate()
	{
		$options = array();
		$options['version'] = $this->version;
		$options['dbversion'] = $this->dbversion;
		$options['textdomain'] = 'default';
		$options['colors']['headers'] = '#dddddd';
		$options['colors']['rows'] = array( '#ffffff', '#efefef' );
		add_option( 'leaguemanager', $options, 'Leaguemanager Options', 'yes' );
		add_option( 'leaguemanager_widget', array(), 'Leaguemanager Widget Options', 'yes' );
		/*
		* Set Capabilities
		*/
		$role = get_role('administrator');
		$role->add_cap('manage_leagues');
		$role->add_cap('leagues');
	
		$role = get_role('editor');
		$role->add_cap('leagues');
	
		$this->install();
	}
		
		
		
	function install()
	{
		global $wpdb;
		include_once( ABSPATH.'/wp-admin/includes/upgrade.php' );
		
		$charset_collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty($wpdb->charset) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( ! empty($wpdb->collate) )
				$charset_collate .= " COLLATE $wpdb->collate";
		}
		
		$create_leagues_sql = "CREATE TABLE {$wpdb->leaguemanager} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT,
						`title` varchar( 100 ) NOT NULL default '',
						`settings` longtext NOT NULL,
						`seasons` longtext NOT NULL,
						PRIMARY KEY ( `id` )) $charset_collate;";
		maybe_create_table( $wpdb->leaguemanager, $create_leagues_sql );
			
		$create_teams_sql = "CREATE TABLE {$wpdb->leaguemanager_teams} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`status` varchar( 50 ) NOT NULL default '&#8226;',
						`title` varchar( 100 ) NOT NULL default '',
						`logo` varchar( 150 ) NOT NULL default '',
						`website` varchar( 255 ) NOT NULL default '',
						`coach` varchar( 100 ) NOT NULL default '',
						`stadium` varchar( 150 ) NOT NULL default '',
						`home` tinyint( 1 ) NOT NULL default '0',
						`points_plus` float NOT NULL default '0',
						`points_minus` float NOT NULL default '0',
						`points2_plus` int( 11 ) NOT NULL default '0',
						`points2_minus` int( 11 ) NOT NULL default '0',
						`add_points` int( 11 ) NOT NULL default '0',
						`done_matches` int( 11 ) NOT NULL default '0',
						`won_matches` int( 11 ) NOT NULL default '0',
						`draw_matches` int( 11 ) NOT NULL default '0',
						`lost_matches` int( 11 ) NOT NULL default '0',
						`diff` int( 11 ) NOT NULL default '0',
						`group` varchar( 30 ) NOT NULL default '',
						`league_id` int( 11 ) NOT NULL,
						`season` varchar( 255 ) NOT NULL default '',
						`rank` int( 11 ) NOT NULL default '0',
						`roster` longtext NOT NULL,
						`custom` longtext NOT NULL,
						PRIMARY KEY ( `id` )) $charset_collate;";
		maybe_create_table( $wpdb->leaguemanager_teams, $create_teams_sql );
			
		$create_matches_sql = "CREATE TABLE {$wpdb->leaguemanager_matches} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`group` varchar( 30 ) NOT NULL default '',
						`date` datetime NOT NULL default '0000-00-00',
						`home_team` varchar( 255 ) NOT NULL default '0',
						`away_team` varchar( 255 ) NOT NULL default '0',
						`match_day` tinyint( 4 ) NOT NULL default '0',
						`location` varchar( 100 ) NOT NULL default '',
						`league_id` int( 11 ) NOT NULL default '0',
						`season` varchar( 255 ) NOT NULL default '',
						`home_points` varchar( 30 ) NULL default NULL,
						`away_points` varchar( 30 ) NULL default NULL,
						`winner_id` int( 11 ) NOT NULL default '0',
						`loser_id` int( 11 ) NOT NULL default '0',
						`post_id` int( 11 ) NOT NULL default '0',
						`final` varchar( 150 ) NOT NULL default '',
						`custom` longtext NOT NULL,
						PRIMARY KEY ( `id` )) $charset_collate;";
		maybe_create_table( $wpdb->leaguemanager_matches, $create_matches_sql );
		$create_stats_sql = "CREATE TABLE {$wpdb->leaguemanager_stats} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT,
						`name` varchar( 30 ) NOT NULL default '',
						`fields` longtext NOT NULL,
						`league_id` int( 11 ) NOT NULL,
						PRIMARY KEY ( `id` )) $charset_collate;";
		maybe_create_table( $wpdb->leaguemanager_stats, $create_stats_sql );
	}
		
		
	/**
	 * Uninstall Plugin
	 *
	 * @param none
	 */
	function uninstall()
	{
		global $wpdb, $leaguemanager;
		
		$wpdb->query( "DROP TABLE {$wpdb->leaguemanager_matches}" );
		$wpdb->query( "DROP TABLE {$wpdb->leaguemanager_teams}" );
		$wpdb->query( "DROP TABLE {$wpdb->leaguemanager}" );
		
		delete_option( 'leaguemanager_widget' );
		delete_option( 'leaguemanager' );
		
		// Delete Logos
		$dir = $leaguemanager->getImagePath();
		if ( $handle = opendir($dir) ) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..")
					@unlink($file);
			}
			closedir($handle);
		}
		@rmdir($dir);
	}


	/**
	 * get admin object
	 *
	 * @param none
	 * @return object
	 */
	function getAdminPanel()
	{
		return $this->adminPanel;
	}
}

// Run the Plugin
global $lmLoader;
$lmLoader = new LeagueManagerLoader();
// export
if ( isset($_POST['leaguemanager_export']) )
	$lmLoader->adminPanel->export($_POST['league_id'], $_POST['mode']);

?>
