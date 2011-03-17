<?php

add_filter( 'sidebars_widgets', 'dbc_child_disable_sidebars' );
add_filter( 'hybrid_site_title', 'dbc_child_site_title', 12 );

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
	return false;
}

?>