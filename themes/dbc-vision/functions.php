<?php

add_action( 'wp_head', 'dbc_custom_background', 11 );
add_action( 'wp_enqueue_scripts', 'dbc_vision_load_files' );

add_filter( 'sidebars_widgets', 'dbc_child_disable_sidebars' );
add_filter( 'hybrid_site_title', 'dbc_child_site_title', 12 );

function dbc_vision_load_files(){
	if ( hybrid_get_setting( 'slider' ) == 'true' )
		wp_enqueue_script( 'showcase', get_stylesheet_directory_uri() .'/js/jquery.aw-showcase.min.js', array( 'jquery' ), '1.0', true );
}
/**
* Disable sidebars on the home page
*
* @since 0.1
*/
function dbc_child_disable_sidebars( $sidebars_widgets ) {

	if ( hybrid_get_setting( 'info' ) == 'true' ) $sidebars_widgets['home'] = true;
	
	return $sidebars_widgets;
}

/**
* If an image path exists for the logo, use it instead of plain text
*
* @since 0.1
*/
function dbc_child_site_title() {
	$title = get_bloginfo('name');
	$url = get_bloginfo('url');
	$img_src = hybrid_get_setting( 'logo_src' );
	
	if ( !empty( $img_src ) )
		echo '<div id="site-title"><a href="'. $url .'" title="'. $title .'"><img src="'. hybrid_get_setting( 'logo_src' ) .'" alt="'. $title .'" /></div></a>';
	else
		echo '<div id="site-title"><a href="'. $url .'" title="'. $title .'" class="test">'. $title . '</div></a>';		
}

/**
* If a custom background image exists use this CSS to hide
* images that shouldn't be displayed over the background.
*
* @since 0.1
*/
function dbc_custom_background() {
	$background = get_background_image();
	if ( $background ) {
		?>
		<style type="text/css">
			#container {
				background: none;
			}
		</style>
		<?php
	}
}

if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'slider', 960, 350, true ); //300 pixels wide (and unlimited height)
	add_image_size( 'slider-thumb', 250, 9999 ); //300 pixels wide (and unlimited height)
}

function dbc_vision_slider() {
	ob_start();
	include( get_stylesheet_directory(). '/slider-home.php' );
	return ob_get_clean();
}
add_shortcode( 'slider', 'dbc_vision_slider' );



