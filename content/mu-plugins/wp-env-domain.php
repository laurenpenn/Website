<?php
/*
Plugin Name: WP Environment Domain
Version: 0.1
Author: Patrick Daly
Author URI: http://developdaly.com/
Credit to @markjaquith for most of this code.
*/

// Convenience methods
if(!class_exists('WP_Stack_Plugin')){class WP_Stack_Plugin{function hook($h){$p=10;$m=$this->sanitize_method($h);$b=func_get_args();unset($b[0]);foreach((array)$b as $a){if(is_int($a))$p=$a;else $m=$a;}return add_action($h,array($this,$m),$p,999);}private function sanitize_method($m){return str_replace(array('.','-'),array('_DOT_','_DASH_'),$m);}}}

// The plugin
class WP_Environment_Domain_Plugin extends WP_Stack_Plugin {
	public static $instance;

	public function __construct() {
		self::$instance = $this;
		if ( !defined( 'ENV_DOMAIN' ) )
			return;
		$this->hook( 'option_home', 'replace_domain' );
		$this->hook( 'option_siteurl', 'replace_domain' );
	}
	
	public function replace_domain ( $url ) {
		$current_domain = parse_url( $url, PHP_URL_HOST );
		if( function_exists('is_subdomain_install') && is_subdomain_install() ) {
			$replacement_domain = getSubDomain( $_SERVER['HTTP_HOST'] ) . ENV_DOMAIN;
		} else {
			$replacement_domain = ENV_DOMAIN;
		}
		$url = str_replace( '//' . $current_domain, '//' . $replacement_domain, $url );
		return $url;
	}
}

new WP_Environment_Domain_Plugin;