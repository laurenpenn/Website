<?php
/**
Plugin Name: WPBakery Visual Composer
Plugin URI: http://wpbakery.com/vc
Description: Create content without headaches with simple drag-n-drop interface.
Version: 3.5.1
Author: Michael M - WPBakery.com
Author URI: http://wpbakery.com
License:
 */

// don't load directly

if (!defined('ABSPATH')) die('-1');

/**
 * Current plugin file directory.
 * var string
 */

$dir = dirname(__FILE__);

if (!defined('VC_THEME_DIR')) define('VC_THEME_DIR', get_theme_root());


global $vc_as_theme;
$vc_as_theme = preg_match('/'.preg_quote(str_replace('\\','/',VC_THEME_DIR), '/').'/', str_replace('\\','/',$dir));


if( !$vc_as_theme && is_file(get_template_directory() . '/wpbakery/js_composer/js_composer.php') ) {
 // Load from theme
} else {

    // {{{ constants

    /**
     * Current visual composer version
     */

    if (!defined('WPB_VC_VERSION')) define('WPB_VC_VERSION', '3.5.1');

    /**
     * jQuery UI version
     */

    if (!defined('WPB_JQUERY_UI_VERSION')) define('WPB_JQUERY_UI_VERSION', 'less');

    // }}}

    /**
     * Define default settings for visual composer.
     *
     * APP_ROOT - plugin directory.
     * WP_ROOT - Wordpress application root directory.
     * APP_DIR - plugin directory name.
     * CONFIG - configuration directory.
     * ASSETS_DIR  - directory name for assets. Used from urls creating.
     * COMPOSER      => main visual composer directory.
     * COMPOSER_LIB  => libraries for composer.
     * SHORTCODES_LIB  => Shortcodes directory.
     *
     * @var array
     */
    global $composer_settings;
    if(!isset($composer_settings)) {

        $composer_settings = Array(
            'APP_ROOT'      => $dir . '/',
            'WP_ROOT'       => dirname( dirname( dirname( dirname($dir ) ) ) ). '/',
            'APP_DIR'       => basename( $dir ) . '/',
            'CONFIG'        => $dir . '/config/',
            'ASSETS_DIR'    => 'assets/',
            'COMPOSER'      => $dir . '/composer/',
            'COMPOSER_LIB'  => $dir . '/composer/lib/',
            'SHORTCODES_LIB'=> $dir . '/composer/lib/shortcodes/',
            /* Default post type where to activate visual composer meta box settings */
            'default_post_types' => Array('page')
        );
    }

    /*
     * Here we include all useful files
     */

    require_once( $composer_settings['COMPOSER'] . 'wp_bakery_visual_composer.php' );

    /*
     * Include visual composer builders.
     * class WPBakeryVisualComposerSetupAdmin - for admin panel
     * class WPBakeryVisualComposerSetup - for frontend
     */
    require_once( $composer_settings['COMPOSER'] . 'build.php' );

    /**
     * Setting file for layouts and shortcodes
     */

    require_once( $composer_settings['CONFIG'] . 'map.php' );


    /**
     * Initialize plugin depending on admin panel or public layout
     * @var object
     */
    global $wpVC_setup;
	$wpVC_setup = is_admin() ? new WPBakeryVisualComposerSetupAdmin() : new WPBakeryVisualComposerSetup();
	if(!$vc_as_theme) {
	    $wpVC_setup->init($composer_settings);
	}
	
	add_action('wpb_check_for_update', 'wpb_js_composer_check_version_schedule');
	
	register_activation_hook(__FILE__, 'wpb_js_composer_check_version_schedule_activation');
	register_deactivation_hook(__FILE__, 'wpb_js_composer_check_version_schedule_deactivation');
	
}
?>