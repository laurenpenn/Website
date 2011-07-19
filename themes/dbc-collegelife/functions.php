<?php

// Remove the subtitle
remove_action( 'dbc_header', 'dbc_subsite_title', 12 );

// Fix child theme custom backgrounds
add_action( 'wp_head', 'dbc_custom_background', 11 );
// Remove the parent theme home page CSS and use this theme's
add_action( 'template_redirect', 'dbc_collegelife_home_style', 100 );
// Add the site title
add_action( 'dbc_header', 'hybrid_site_title', 12 );
	
// Remove established sidebar filters
remove_filter( 'sidebars_widgets', 'dbc_disable_sidebars' );

// Change the site title to work for a child theme
add_filter( 'hybrid_site_title', 'dbc_collegelife_site_title', 12 );

function dbc_collegelife_home_style() {
	wp_deregister_style( 'front-page' );
	if ( is_page_template( 'page-template-front-page.php' ) )
		wp_enqueue_style( 'home', trailingslashit( CHILD_THEME_URI ) .'home.css' );
}

/**
* Disable sidebars on the home page
*
* @since 0.1
*/
function dbc_collegelife_disable_sidebars( $sidebars_widgets ) {

	if ( hybrid_get_setting( 'info' ) == 'true' ) $sidebars_widgets['home'] = true;
	
	return $sidebars_widgets;
}

/**
* If an image path exists for the logo, use it instead of plain text
*
* @since 0.1
*/
function dbc_collegelife_site_title() {
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

?>