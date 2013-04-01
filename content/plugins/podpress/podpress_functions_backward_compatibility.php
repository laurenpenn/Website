<?php
/* A file for functions of new WP versions which are not available in older WP versions. This gets imported as the first thing. */

if ( ! function_exists( 'is_ssl' ) ) {
	/**
	* is_ssl - is the is_ssl() function from WP. It provides backwards compatibility (is_ssl() was new in WP 2.6).
	*
	* @package podPress
	* @since 8.8.10.14
	*
	* @return bool - returns TRUE if it is a SSL request and FALSE if not
	*/
	function is_ssl() {
		if ( isset($_SERVER['HTTPS']) ) {
			if ( 'on' == strtolower($_SERVER['HTTPS']) ) {
				return true;
			}
			if ( '1' == $_SERVER['HTTPS'] ) {
				return true;
			}
		} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
			return true;
		}
		return false;
	}
}

if ( FALSE === function_exists( 'site_url' ) ) {
	/**
	* site_url - is a function which should provide backwards compatibility if podPress is used with WP older than 2.6. (site_url() was new in WP 2.6.
	*
	* @package podPress
	* @since 8.8.10.14
	*
	* @return str $siteurl - the site URL or an empty string
	*/
	function site_url() {
		$siteurl = get_option('siteurl');
		if (FALSE !== $siteurl) {
			return podpress_siteurl_is_ssl($siteurl);
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'plugins_url' ) ) {
	/**
	* Retrieve the url to the plugins directory or to a specific file within that directory.
	* You can hardcode the plugin slug in $path or pass __FILE__ as a second argument to get the correct folder name.
	*
	* @package WordPress
	* @since 2.6.0
	*
	* @param string $path Optional. Path relative to the plugins url.
	* @param string $plugin Optional. The plugin file that you want to be relative to - i.e. pass in __FILE__
	* @return string Plugins url link with optional path appended.
	*/
	function plugins_url($path = '', $plugin = '') {
		$mu_plugin_dir = WPMU_PLUGIN_DIR;
		foreach ( array('path', 'plugin', 'mu_plugin_dir') as $var ) {
			$$var = str_replace('\\' ,'/', $$var); // sanitize for Win32 installs
			$$var = preg_replace('|/+|', '/', $$var);
		}
		if ( !empty($plugin) && 0 === strpos($plugin, $mu_plugin_dir) ) {
			$url = WPMU_PLUGIN_URL;
		} else {
			$url = WP_PLUGIN_URL;
		}
		if ( 0 === strpos($url, 'http') && is_ssl() ) {
			$url = str_replace( 'http://', 'https://', $url );
		}
		if ( !empty($plugin) && is_string($plugin) ) {
			$folder = dirname(plugin_basename($plugin));
			if ( '.' != $folder ) {
				$url .= '/' . ltrim($folder, '/');
			}
		}
	  
		if ( !empty($path) && is_string($path) && strpos($path, '..') === false ) {
			$url .= '/' . ltrim($path, '/');
		}
		return apply_filters('plugins_url', $url, $path, $plugin);
	}
}
?>