<?php

/*
 $Id: sitemap.php 296046 2010-10-02 22:37:20Z arnee $

 Google XML Sitemaps Generator for WordPress
 ==============================================================================
 
 This generator will create a sitemaps.org compliant sitemap of your WordPress blog.

 The priority of a post depends on its comments. You can choose the way the priority
 is calculated in the options screen.
 
 Feel free to visit my website under www.arnebrachhold.de!

 For aditional details like installation instructions, please check the readme.txt and documentation.txt files.
 
 Have fun!
   Arne


 Info for WordPress:
 ==============================================================================
 Plugin Name: Google XML Sitemaps
 Plugin URI: http://www.arnebrachhold.de/redir/sitemap-home/
 Description: This plugin will generate a special XML sitemap which will help search engines like Google, Yahoo, Bing and Ask.com to better index your blog.
 Version: 4.0alpha5
 Author: Arne Brachhold
 Author URI: http://www.arnebrachhold.de/
 Text Domain: sitemap
 Domain Path: /lang/
 
*/

function sm_Setup() {
	
	$fail = false;
	
	if(version_compare(PHP_VERSION, "5.1", "<")) {
		add_action('admin_notices',  'sm_AddPhpVersionError');
		$fail = true;
	}
	
	//Check minimum WP requirements, which is 2.8 at the moment.
	if(version_compare($GLOBALS["wp_version"], "2.8", "<")) {
		add_action('admin_notices',  'sm_AddWpVersionError');
		$fail = true;
	}
	
	if(!$fail) require_once(trailingslashit(dirname(__FILE__)) . "sitemap-loader.php");
}

/**
 * Adds a notice to the admin interface that the WordPress version is too old for the plugin
 * @since 4.0
 */
function sm_AddWpVersionError() {
	echo "<div id='sm-version-error' class='error fade'><p><strong>".__('Your WordPress version is too old for XML Sitemaps.','sitemap')."</strong><br /> ".sprintf(__('Unfortunately this release of Google XML Sitemaps requires at least WordPress 2.8. You are using Wordpress %2$s, which is out-dated and unsecure. Please upgrade or go to <a href="%1$s">active plugins</a> and deactivate the Google XML Sitemaps plugin to hide this message. You can download an older version of this plugin from the <a href="%3$s">plugin website</a>.','sitemap'), "plugins.php?plugin_status=active",$GLOBALS["wp_version"],"http://www.arnebrachhold.de/redir/sitemap-home/")."</p></div>";
}

/**
 * Adds a notice to the admin interface that the WordPress version is too old for the plugin
 * @since 4.0
 */
function sm_AddPhpVersionError() {
	echo "<div id='sm-version-error' class='error fade'><p><strong>".__('Your PHP version is too old for XML Sitemaps.','sitemap')."</strong><br /> ".sprintf(__('Unfortunately this release of Google XML Sitemaps requires at least PHP 5.1. You are using PHP %2$s, which is out-dated and unsecure. Please ask your web host to update your PHP installation or go to <a href="%1$s">active plugins</a> and deactivate the Google XML Sitemaps plugin to hide this message. You can download an older version of this plugin from the <a href="%3$s">plugin website</a>.','sitemap'), "plugins.php?plugin_status=active",PHP_VERSION,"http://www.arnebrachhold.de/redir/sitemap-home/")."</p></div>";
}

function sm_GetInitFile() {
	return __FILE__;
}

if(defined('ABSPATH') && defined('WPINC')) {
	sm_Setup();
}

