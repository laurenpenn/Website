<?php

add_filter( 'hybrid_site_title', 'dbc_child_site_title', 12 );

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

?>
