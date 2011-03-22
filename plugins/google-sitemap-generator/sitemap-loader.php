<?php

/**
 * Loader class for the Google Sitemap Generator
 *
 * This class takes care of the sitemap plugin and tries to load the different parts as late as possible.
 * On normal requests, only this small class is loaded. When the sitemap needs to be rebuild, the generator itself is loaded.
 * The last stage is the user interface which is loaded when the administration page is requested.
 *
 * @author Arne Brachhold
 * @package sitemap
 */
class GoogleSitemapGeneratorLoader {
	
	/**
	 * @var Version of the generator in SVN
	*/
	private static $svnVersion = '$Id: sitemap-loader.php 296024 2010-10-02 20:51:07Z arnee $';
	
	/**
	 * Enabled the sitemap plugin with registering all required hooks
	 *
	 * @uses add_action Adds actions for admin menu, executing pings and handling robots.txt
	 * @uses add_filter Adds filtes for admin menu icon and contexual help
	 * @uses GoogleSitemapGeneratorLoader::SetupRewriteHooks() Registeres the rewrite hooks
	 * @uses GoogleSitemapGeneratorLoader::CallShowPingResult() Shows the ping result on request
	 * @uses GoogleSitemapGeneratorLoader::ActivateRewrite() Writes rewrite rules the first time
	 */
	public static function Enable() {
		

		//Register the sitemap creator to wordpress...
		add_action('admin_menu', array('GoogleSitemapGeneratorLoader', 'RegisterAdminPage'));
		
		//Nice icon for Admin Menu (requires Ozh Admin Drop Down Plugin)
		add_filter('ozh_adminmenu_icon', array('GoogleSitemapGeneratorLoader', 'RegisterAdminIcon'));
				
		//Additional links on the plugin page
		add_filter('plugin_row_meta', array('GoogleSitemapGeneratorLoader', 'RegisterPluginLinks'),10,2);

		//Existing page was published
		add_action('do_pings', array('GoogleSitemapGeneratorLoader', 'CallSendPing'),9999,1);
		
		//Robots.txt request
		add_action('do_robots', array('GoogleSitemapGeneratorLoader', 'CallDoRobots'),100,0);
		
		//Help topics for context sensitive help
		add_filter('contextual_help_list', array('GoogleSitemapGeneratorLoader', 'CallHtmlShowHelpList'),9999,2);
	
		//Set up hooks for adding permalinks, query vars
		GoogleSitemapGeneratorLoader::SetupRewriteHooks();
		
		//Check if the result of a ping request should be shown
		if(!empty($_GET["sm_ping_service"])) {
			GoogleSitemapGeneratorLoader::CallShowPingResult();
		}
		
		//Fix rewrite rules if not already done on activation hook. This happens on network activation for example.
		if(get_option("sm_rewrite_done", null) != "v1") {
			GoogleSitemapGeneratorLoader::ActivateRewrite();
		}
	}
	
	/**
	 * Adds the filters for wp rewrite and query vars handling
	 *
	 * @since 4.0
	 * @uses add_filter()
	 */
	public static function SetupRewriteHooks() {
		add_filter('query_vars', array('GoogleSitemapGeneratorLoader', 'RegisterQueryVars'),1,1);
		
		add_filter('rewrite_rules_array', array('GoogleSitemapGeneratorLoader', 'AddRewriteRules'),1,1);
		
		add_filter('template_redirect', array('GoogleSitemapGeneratorLoader', 'DoTemplateRedirect'),1,0);
	}
	
	/**
	 * Register the plugin specific "xml_sitemap" query var
	 *
	 * @since 4.0
	 * @param $vars Array Array of existing query_vars
	 * @return Array An aarray containing the new query vars
	 */
	public static function RegisterQueryVars($vars) {
	    array_push($vars, 'xml_sitemap');
	    return $vars;
	}
	
	/**
	 * Registers the plugin specific rewrite rules
	 *
	 * @since 4.0
	 * @param $vars  Array Array of existing rewrite rules
	 * @return Array An aarray containing the new rewrite rules
	 */
	public static function AddRewriteRules($rules){
		$newrules = array();
		$newrules['sitemap-?([a-zA-Z0-9\-_]+)?\.xml$'] = 'index.php?xml_sitemap=params=$matches[1]';
		$newrules['sitemap-?([a-zA-Z0-9\-_]+)?\.xml\.gz$'] = 'index.php?xml_sitemap=params=$matches[1];zip=true';
		
		return $newrules + $rules;
	}
	
	/**
	 * Handles the plugin output on template redirection if the xml_sitemap query var is present.
	 *
	 * @since 4.0
	 * @global $wp_query  The WordPress query object
	 */
	public static function DoTemplateRedirect(){
		global $wp_query;
		if(!empty($wp_query->query_vars["xml_sitemap"])) {
			GoogleSitemapGeneratorLoader::CallShowSitemap($wp_query->query_vars["xml_sitemap"]);
		}
	}
	
	/**
	 * Handled the plugin activation on installation
	 *
	 * @uses GoogleSitemapGeneratorLoader::ActivateRewrite
	 * @since 4.0
	 */
	public static function ActivatePlugin() {
		GoogleSitemapGeneratorLoader::ActivateRewrite();
	}
	
	/**
	 * Sets up the rewrite rules and flushes them
	 *
	 * @since 4.0
	 * @global $wp_rewrite WP_Rewrite
	 * @uses WP_Rewrite::flush_rules()
	 * @uses GoogleSitemapGeneratorLoader::SetupRewriteHooks()
	 */
	public static function ActivateRewrite() {
		global $wp_rewrite;
		GoogleSitemapGeneratorLoader::SetupRewriteHooks();
		$wp_rewrite->flush_rules(false);
		update_option("sm_rewrite_done","v1");
	}
	
	/**
	 * Registers the plugin in the admin menu system
	 *
	 * @uses add_options_page()
	 */
	public static function RegisterAdminPage() {
		if (function_exists('add_options_page')) {
			add_options_page(__('XML-Sitemap Generator','sitemap'), __('XML-Sitemap','sitemap'), 'level_10', GoogleSitemapGeneratorLoader::GetBaseName(), array('GoogleSitemapGeneratorLoader','CallHtmlShowOptionsPage'));
		}
	}
	
	/**
	 * Returns a nice icon for the Ozh Admin Menu if the {@param $hook} equals to the sitemap plugin
	 *
	 * @param string $hook The hook to compare
	 * @return string The path to the icon
	 */
	public static function RegisterAdminIcon($hook) {
		if ( $hook == GoogleSitemapGeneratorLoader::GetBaseName() && function_exists('plugins_url')) {
			return plugins_url('img/icon-arne.gif',GoogleSitemapGeneratorLoader::GetBaseName());
		}
		return $hook;
	}
	
	/**
	 * Registers additional links for the sitemap plugin on the WP plugin configuration page
	 *
	 * Registers the links if the $file param equals to the sitemap plugin
	 * @param $links Array An array with the existing links
	 * @param $file string The file to compare to
	 */
	public static function RegisterPluginLinks($links, $file) {
		$base = GoogleSitemapGeneratorLoader::GetBaseName();
		if ($file == $base) {
			$links[] = '<a href="options-general.php?page=' . GoogleSitemapGeneratorLoader::GetBaseName() .'">' . __('Settings','sitemap') . '</a>';
			$links[] = '<a href="http://www.arnebrachhold.de/redir/sitemap-plist-faq/">' . __('FAQ','sitemap') . '</a>';
			$links[] = '<a href="http://www.arnebrachhold.de/redir/sitemap-plist-support/">' . __('Support','sitemap') . '</a>';
			$links[] = '<a href="http://www.arnebrachhold.de/redir/sitemap-plist-donate/">' . __('Donate','sitemap') . '</a>';
		}
		return $links;
	}
	
	/**
	 * Invokes the HtmlShowOptionsPage method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::HtmlShowOptionsPage()
	 */
	public static function CallHtmlShowOptionsPage() {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->HtmlShowOptionsPage();
		}
	}
	
	/**
	 * Invokes the ShowPingResult method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::ShowPingResult()
	 */
	public static function CallShowPingResult() {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->ShowPingResult();
		}
	}
	
	/**
	 * Invokes the ShowPingResult method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::SendPing()
	 */
	public static function CallSendPing() {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->SendPing();
		}
	}
	
	/**
	 * Invokes the ShowSitemap method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::ShowSitemap()
	 */
	public static function CallShowSitemap($options) {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->ShowSitemap($options);
		}
	}
	
	/**
	 * Invokes the DoRobots method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::DoRobots()
	 */
	public static function CallDoRobots() {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {
			GoogleSitemapGenerator::GetInstance()->DoRobots();
		}
	}
	
	/**
	 * Displays the help links in the upper Help Section of WordPress
	 *
	 * @param $filterVal Array The existing links
	 * @param $screen Object The current screen object
	 * @return Array The new links
	 */
	public static function CallHtmlShowHelpList($filterVal,$screen) {

		$id = get_plugin_page_hookname(GoogleSitemapGeneratorLoader::GetBaseName(),'options-general.php');
		
		//WP 3.0 passes a screen object instead of a string
		if(is_object($screen)) $screen = $screen->id;
		
		if($screen == $id) {
			$links = array(
				__('Plugin Homepage','sitemap')=>'http://www.arnebrachhold.de/redir/sitemap-help-home/',
				__('My Sitemaps FAQ','sitemap')=>'http://www.arnebrachhold.de/redir/sitemap-help-faq/'
			);
			
			$filterVal[$id] = '';
			
			$i=0;
			foreach($links AS $text=>$url) {
				$filterVal[$id].='<a href="' . $url . '">' . $text . '</a>' . ($i < (count($links)-1)?' | ':'') ;
				$i++;
			}
		}
		return $filterVal;
	}
	

	/**
	 * Loads the actual generator class and tries to raise the memory and time limits if not already done by WP
	 *
	 * @uses GoogleSitemapGenerator::Enable()
	 * @return boolean true if run successfully
	 */
	public static function LoadPlugin() {
			
		if(!class_exists("GoogleSitemapGenerator")) {
			
			$path = trailingslashit(dirname(__FILE__));
			
			if(!file_exists( $path . 'sitemap-core.php')) return false;
			require_once($path. 'sitemap-core.php');
		}

		GoogleSitemapGenerator::Enable();
		return true;
	}
	
	/**
	 * Returns the plugin basename of the plugin (using __FILE__)
	 *
	 * @return string The plugin basename, "sitemap" for example
	 */
	public static function GetBaseName() {
		return plugin_basename(sm_GetInitFile());
	}
	
	/**
	 * Returns the name of this loader script, using sm_GetInitFile
	 *
	 * @return string The sm_GetInitFile value
	 */
	public static function GetPluginFile() {
		return sm_GetInitFile();
	}
	
	/**
	 * Returns the plugin version
	 *
	 * Uses the WP API to get the meta data from the top of this file (comment)
	 *
	 * @return string The version like 3.1.1
	 */
	public static function GetVersion() {
		if(!isset($GLOBALS["sm_version"])) {
			if(!function_exists('get_plugin_data')) {
				if(file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) require_once(ABSPATH . 'wp-admin/includes/plugin.php');
				else return "0.ERROR";
			}
			$data = get_plugin_data(GoogleSitemapGeneratorLoader::GetPluginFile(), false, false);
			$GLOBALS["sm_version"] = $data['Version'];
		}
		return $GLOBALS["sm_version"];
	}
	
	public static function GetSvnVersion() {
		return self::$svnVersion;
	}
}

//Enable the plugin for the init hook, but only if WP is loaded. Calling this php file directly will do nothing.
if(defined('ABSPATH') && defined('WPINC')) {
	add_action("init",array("GoogleSitemapGeneratorLoader","Enable"),1000,0);
	register_activation_hook(__FILE__, array('GoogleSitemapGeneratorLoader', 'ActivatePlugin'));
}

