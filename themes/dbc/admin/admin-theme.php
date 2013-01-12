<?php

add_action( 'admin_init', 'custom_login' );

function custom_login() {
	if ( !is_admin() ) return false;
	
	wp_enqueue_style( 'custom-login', trailingslashit( THEME_URI ) .'library/css/custom-login.css' ); 
}

?>
