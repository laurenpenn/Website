<?php
/**
 * The functions file is used to initialize everything in the theme.  It controls how the theme is loaded and 
 * sets up the supported features, default actions, and default filters.  If making customizations, users 
 * should create a child theme and make changes to its functions.php file (not this one).  Friends don't let 
 * friends modify parent theme files. ;)
 *
 * Child themes should do their setup on the 'after_setup_theme' hook with a priority of 11 if they want to
 * override parent theme features.  Use a priority of 9 if wanting to run before the parent theme.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package DBCU
 * @subpackage Functions
 * @version 0.3.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2011, Justin Tadlock
 * @link http://themehybrid.com/themes/dbcu
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Load the core theme framework. */
require_once( trailingslashit( TEMPLATEPATH ) . 'library/hybrid.php' );
$theme = new Hybrid();

/* Do theme setup on the 'after_setup_theme' hook. */
add_action( 'after_setup_theme', 'dbcu_theme_setup' );

/**
 * Theme setup function.  This function adds support for theme features and defines the default theme
 * actions and filters.
 *
 * @since 0.1.0
 */
function dbcu_theme_setup() {

	/* Get action/filter hook prefix. */
	$prefix = hybrid_get_prefix();

	/* Add theme support for core framework features. */
	add_theme_support( 'hybrid-core-menus', array( 'primary' ) );
	add_theme_support( 'hybrid-core-sidebars', array( 'header', 'primary' ) );
	add_theme_support( 'hybrid-core-widgets' );
	add_theme_support( 'hybrid-core-shortcodes' );
	add_theme_support( 'hybrid-core-theme-settings', array( 'about', 'footer' ) );
	add_theme_support( 'hybrid-core-drop-downs' );
	add_theme_support( 'hybrid-core-seo' );
	add_theme_support( 'hybrid-core-template-hierarchy' );

	/* Add theme support for framework extensions. */
	add_theme_support( 'post-stylesheets' );
	add_theme_support( 'dev-stylesheet' );
	add_theme_support( 'loop-pagination' );
	add_theme_support( 'get-the-image' );
	add_theme_support( 'cleaner-gallery' );
	add_theme_support( 'theme-layouts', array( '1c' ) );

	add_action( 'widgets_init', 'dbc_register_widgets' );

	/* Add theme support for WordPress features. */
	add_theme_support( 'automatic-feed-links' );
	add_custom_background();
	
	/* Add custom editor stylesheet. */
	add_editor_style('style-editor.css');

	/* Add the search form to the secondary menu. */
	add_action( "{$prefix}_close_menu_secondary", 'get_search_form' );

	/* Embed width/height defaults. */
	add_filter( 'embed_defaults', 'dbcu_embed_defaults' );

	/* Filter the sidebar widgets. */
	add_filter( 'sidebars_widgets', 'dbcu_disable_sidebars' );
	add_action( 'template_redirect', 'dbcu_one_column' );
	
	add_action( 'wp_enqueue_scripts', 'dbcu_enqueue_scripts' );
	
	add_action( 'init', 'dbcu_remove_header_info' );

	/* Set the content width. */
	hybrid_set_content_width( 600 );
}

function dbcu_enqueue_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'modernizr', trailingslashit( THEME_URI ). 'js/modernizr.foundation.js');
	wp_enqueue_script( 'respond', trailingslashit( THEME_URI ). 'js/respond.min.js' );
	wp_enqueue_script( 'foundation', trailingslashit( THEME_URI ). 'js/foundation.js'); 
	wp_enqueue_script( 'app', trailingslashit( THEME_URI ). 'js/app.js'); 
}

/**
 * Removes some of the default header meta that WordPress adds in.
 * Removes some of the default header meta that Hyrbid adds in.
 * We're removing this to make the page HTML5 compatible.
 *
 * @since 0.3
 */
function dbcu_remove_header_info() {
	remove_action( 'wp_head', 'rsd_link', 1 );
	remove_action( 'wp_head', 'wlwmanifest_link', 1 );
	remove_action( 'wp_head', 'wp_generator', 1 );
	remove_action( 'wp_head', 'start_post_rel_link', 1 );
	remove_action( 'wp_head', 'index_rel_link', 1 );
	remove_action( 'wp_head', 'adjacent_posts_rel_link', 1 );
	remove_action( 'wp_head', 'hybrid_meta_robots', 1 );
	remove_action( 'wp_head', 'hybrid_meta_author', 1 );
	remove_action( 'wp_head', 'hybrid_meta_copyright', 1 );
	remove_action( 'wp_head', 'hybrid_meta_revised', 1 );
	remove_action( 'wp_head', 'hybrid_meta_description', 1 );
	remove_action( 'wp_head', 'hybrid_meta_keywords', 1 );
	remove_action( 'wp_head', 'hybrid_meta_template', 1 );
}

/**
 * Function for deciding which pages should have a one-column layout.
 *
 * @since 0.1.0
 */
function dbcu_one_column() {

	if ( !is_active_sidebar( 'primary' ) && !is_active_sidebar( 'secondary' ) )
		add_filter( 'get_theme_layout', 'dbcu_theme_layout_one_column' );

	elseif ( is_attachment() && 'layout-default' == theme_layouts_get_layout() )
		add_filter( 'get_theme_layout', 'dbcu_theme_layout_one_column' );
	
	if ( is_page_template( 'page-template-home.php' ) )
		add_filter( 'get_theme_layout', 'dbcu_theme_layout_one_column' );
		
}

/**
 * Filters 'get_theme_layout' by returning 'layout-1c'.
 *
 * @since 0.2.0
 */
function dbcu_theme_layout_one_column( $layout ) {
	return 'layout-1c';
}

/**
 * Disables sidebars if viewing a one-column page.
 *
 * @since 0.1.0
 */
function dbcu_disable_sidebars( $sidebars_widgets ) {
	global $wp_query;

	if ( current_theme_supports( 'theme-layouts' ) ) {

		if ( 'layout-1c' == theme_layouts_get_layout() ) {
			$sidebars_widgets['primary'] = false;
			$sidebars_widgets['secondary'] = false;
		}
	}

	return $sidebars_widgets;
}

/**
 * Overwrites the default widths for embeds.  This is especially useful for making sure videos properly
 * expand the full width on video pages.  This function overwrites what the $content_width variable handles
 * with context-based widths.
 *
 * @since 0.1.0
 */
function dbcu_embed_defaults( $args ) {

	if ( current_theme_supports( 'theme-layouts' ) ) {

		$layout = theme_layouts_get_layout();

		if ( 'layout-3c-l' == $layout || 'layout-3c-r' == $layout || 'layout-3c-c' == $layout )
			$args['width'] = 500;
		elseif ( 'layout-1c' == $layout )
			$args['width'] = 928;
		else
			$args['width'] = 600;
	}
	else
		$args['width'] = 600;

	return $args;
}

//[foobar]
function dbcu_blog(){
	
	switch_to_blog(2);

	$output = '<div class="row">';
    $args = array(
        'posts_per_page' => 3
    );
    $blog = new  WP_Query( $args );
	
    while ( $blog->have_posts() ) : $blog->the_post();
		if ( get_the_post_thumbnail() ):
	        $output .= '<div class="post hentry twelve columns">'.
	                   get_the_post_thumbnail( $blog->ID, 'medium', array( 'class' => 'left' ) ).
	                   '<h2 class="entry-title"><a href="'. get_permalink() .'">'.  get_the_title(). '</a></h2>'.
	                   '<p>'. get_the_excerpt(). '</p><p><a class="button small" href="'.  get_permalink(). '">Read more</a></div><!--  ends here -->';
		else :
	        $output .= '<div class="post hentry six columns">'.
						get_the_post_thumbnail().
						'<h2 class="entry-title"><a href="'. get_permalink() .'">'.  get_the_title(). '</a></h2>'.
						'<p>'. get_the_excerpt(). '</p><p><a class="button small" href="'.  get_permalink(). '">Read more</a></div><!--  ends here -->';
				   
		endif;
		
    endwhile;
	restore_current_blog();
    wp_reset_query();
    $output .= '</div>';
    return $output;

    
			
}
add_shortcode( 'blog', 'dbcu_blog' );

/**
 * Register DBC's extra widgets.
 *
 * @since 0.1
 * @uses register_widget() Registers individual widgets.
 * @link http://codex.wordpress.org/WordPress_Widgets_Api
 */
function dbc_register_widgets() {

	/* Load each widget file. */
	require_once( trailingslashit( TEMPLATEPATH ) . 'widgets/widget-pages.php' );

	/* Register each widget. */
	register_widget( 'DBC_Widget_Pages' );
}

add_action( 'init', 'register_cpt_home_page_tab' );

function register_cpt_home_page_tab() {

    $labels = array( 
        'name' => _x( 'Home Page Tabs', 'home_page_tab' ),
        'singular_name' => _x( 'Home Page Tab', 'home_page_tab' ),
        'add_new' => _x( 'Add New', 'home_page_tab' ),
        'add_new_item' => _x( 'Add New Home Page Tab', 'home_page_tab' ),
        'edit_item' => _x( 'Edit Home Page Tab', 'home_page_tab' ),
        'new_item' => _x( 'New Home Page Tab', 'home_page_tab' ),
        'view_item' => _x( 'View Home Page Tab', 'home_page_tab' ),
        'search_items' => _x( 'Search Home Page Tabs', 'home_page_tab' ),
        'not_found' => _x( 'No home page tabs found', 'home_page_tab' ),
        'not_found_in_trash' => _x( 'No home page tabs found in Trash', 'home_page_tab' ),
        'parent_item_colon' => _x( 'Parent Home Page Tab:', 'home_page_tab' ),
        'menu_name' => _x( 'Home Page Tabs', 'home_page_tab' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        
        'supports' => array( 'title', 'editor', 'revisions', 'page-attributes' ),
        
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'home_page_tab', $args );
}
