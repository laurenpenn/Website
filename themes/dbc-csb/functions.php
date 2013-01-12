<?php

add_filter( 'hybrid_site_title', 'dbc_child_site_title', 12 );

/**
* If an image path exists for the logo, use it instead of plain text
*
* @since 0.1
*/
function dbc_child_site_title() {
	return false;
}

?>
