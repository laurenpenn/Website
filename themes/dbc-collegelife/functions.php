<?php

/* Theme PHP code will go here. */
add_action( 'after_setup_theme', 'dbc_child_theme_setup', 11 );

function dbc_child_theme_setup() {

	add_action( 'template_redirect', 'dbc_collegelife_remove', 100 );
	
	remove_action( 'dbc_header', 'dbc_subsite_title', 12 );	
	add_action( 'dbc_header', 'hybrid_site_title', 12 );
	
	remove_filter( 'sidebars_widgets', 'dbc_disable_sidebars' );

}

function dbc_collegelife_remove() {
	wp_deregister_style( 'front-page' );
	if ( is_page_template( 'page-template-front-page.php' ) )
		wp_enqueue_style( 'home', trailingslashit( CHILD_THEME_URI ) .'home.css' );
}

?>