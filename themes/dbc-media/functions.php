<?php
/**
 * Functions File
 *
 * Loads theme functions before templates kick in.
 * Defines constants to be used throughout the theme.
 *
 * @package DBC Media
 * @subpackage Functions
 */

/* Load theme textdomain. */
load_theme_textdomain( 'dbcmedia' );

/* Define constant paths (PHP files). */
define( DBCMEDIA_DIR, TEMPLATEPATH );
define( DBCMEDIA_PLUGINS, DBCMEDIA_DIR   . '/plugins' );

/* Define constant paths (other file types). */
$DBCMEDIA_dir = get_bloginfo( 'template_directory' );
define( DBCMEDIA_IMAGES, $DBCMEDIA_dir . '/images' );
define( DBCMEDIA_CSS, $DBCMEDIA_dir . '/css' );
define( DBCMEDIA_JS, $DBCMEDIA_dir . '/js' );

/* Include all files. */
require_once ( DBCMEDIA_PLUGINS   . '/get-the-image.php');
require_once ( DBCMEDIA_PLUGINS   . '/custom-feed.php');

/* Add actions. */
add_action( 'parse_request', 'shopp_search' );
add_action( 'wp',            'disable_shopp_resources',100 ); 

/* Remove actions. */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );

/* Load styles and scripts in the front end. */
if(!is_admin()) { 

	wp_enqueue_style(  "yui",    DBCMEDIA_CSS  . "/reset-fonts-grids.css");
	wp_enqueue_style(  "style",  $DBCMEDIA_dir  . "/style.css");
	
	wp_enqueue_style(  "ie",     DBCMEDIA_CSS  . "/ie.css", false, $version_identifier, "all");
	$wp_styles->add_data( "ie",  "conditional", "IE" );
	
	wp_enqueue_style(  "ie7",    DBCMEDIA_CSS  . "/ie7.css", false, $version_identifier, "all");
	$wp_styles->add_data( "ie7", "conditional", "IE 7" );
	
	wp_enqueue_script( "custom", DBCMEDIA_JS   . "/custom.js");
}

/* Reigster Sidebars. */
register_sidebar(array( 'name' => 'Home Sidebar', 'before_widget' => '<div class="widget box-container">', 'after_widget' => '</div>', 'before_title' => '<h2 class="section-header">', 'after_title' => '</h2>', ));
register_sidebar(array( 'name' => 'Page Sidebar', 'before_widget' => '<div class="widget box-container">', 'after_widget' => '</div>', 'before_title' => '<h2 class="section-header">', 'after_title' => '</h2>', ));
register_sidebar(array( 'name' => 'Post Sidebar', 'before_widget' => '<div class="widget box-container">', 'after_widget' => '</div>', 'before_title' => '<h2 class="section-header">', 'after_title' => '</h2>', ));
 
function disable_shopp_resources() {
	global $Shopp;
	wp_deregister_script('shopp-thickbox');
	wp_deregister_style('shopp-thickbox');
}

function shopp_search($wp){
    global $Shopp;
    if(!empty($wp->query_vars['s'])) $_REQUEST['_wp_http_referer'] = $Shopp->link('catalog');
}

?>