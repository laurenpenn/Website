<?php

add_action( 'init', 'dbc_serve_register' );

add_action( 'wp_head', 'dbc_serve_custom_background', 11 );

add_action( 'template_redirect', 'dbc_serve_load_scripts' );

add_filter( 'sidebars_widgets', 'dbc_serve_disable_sidebars' );
add_filter( 'hybrid_site_title', 'dbc_serve_site_title', 12 );

add_action( 'wp_print_styles', 'dbc_serve_deregister_styles', 100 );

add_filter( 'manage_edit-missionary_columns', 'dbc_serve_edit_missionary_columns' ) ;
add_action( 'manage_missionary_posts_custom_column', 'dbc_serve_manage_missionary_columns', 10, 2 );
add_filter( 'manage_edit-missionary_sortable_columns', 'dbc_serve_missionary_sortable_columns' );
/* Only run our customization on the 'edit.php' page in the admin. */
add_action( 'load-edit.php', 'dbc_serve_edit_missionary_load' );

function dbc_serve_deregister_styles() {
	wp_deregister_style( 'front-page' );
}

/**
* Disable sidebars on the home page
*
* @since 0.1
*/
function dbc_serve_disable_sidebars( $sidebars_widgets ) {

	if ( hybrid_get_setting( 'info' ) == 'true' ) $sidebars_widgets['home'] = true;
	
	return $sidebars_widgets;
}

	
function dbc_serve_load_scripts() {
	
	wp_enqueue_style( 'zoommap', trailingslashit( CHILD_THEME_URI ) .'maps/zoommap.css' );
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'zoommap', trailingslashit( CHILD_THEME_URI ) .'maps/zoommap.js' );
	wp_enqueue_script( 'setup', trailingslashit( CHILD_THEME_URI ) .'maps/setup.js' );
	wp_enqueue_script( 'scripts', trailingslashit( CHILD_THEME_URI ) .'js/scripts.js' );
}

/**
* If an image path exists for the logo, use it instead of plain text
*
* @since 0.1
*/
function dbc_serve_site_title() {
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
function dbc_serve_custom_background() {
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

function dbc_serve_register() {

	$missionary_labels = array(
		'name' => _x('Missionaries', 'missionaries'),
		'singular_name' => _x('Missionary', 'missionary'),
		'add_new' => _x('Add Missionary', 'missionary'),
		'add_new_item' => __('Add New Missionary'),
		'edit_item' => __('Edit Missionary'),
		'new_item' => __('New Missionary'),
		'view_item' => __('View Missionary'),
		'search_items' => __('Search Missionaries'),
		'not_found' =>  __('No missionaries found'),
		'not_found_in_trash' => __('No missionaries found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => 'Missionaries'
	);
	
	register_post_type( 'missionary', 
		array(
			'labels' => $missionary_labels,
			'menu_position' => 4,
			'public' => true,
			'query_var' => 'missionary',
			'has_archive' => true,
			'rewrite' => array( 'slug' => 'missionaries/missionary', 'with_front' => false ),
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes' ),
			'register_meta_box_cb' => null,
			'has_archive' => true
		) 
	);	
}


function dbc_serve_edit_missionary_columns( $columns ) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Missionary' ),
		'location' => __( 'Location' ),
		'date' => __( 'Date added' )
	);

	return $columns;
}



function dbc_serve_manage_missionary_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		/* If displaying the 'location' column. */
		case 'location' :

			/* Get the post meta. */
			$location = get_post_meta( $post_id, 'location', true );

			/* If no location is found, output a default message. */
			if ( empty( $location ) )
				echo __( 'Unknown' );

			/* If there is a location, print it. */
			else
				printf( __( '%s' ), $location );

			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}



function dbc_serve_missionary_sortable_columns( $columns ) {

	$columns['location'] = 'location';

	return $columns;
}



function dbc_serve_edit_missionary_load() {
	add_filter( 'request', 'bc_serve_sort_missionaries' );
}
/* Sorts the missionaries. */
function bc_serve_sort_missionaries( $vars ) {

	/* Check if we're viewing the 'missionary' post type. */
	if ( isset( $vars['post_type'] ) && 'missionary' == $vars['post_type'] ) {

		/* Check if 'orderby' is set to 'location'. */
		if ( isset( $vars['orderby'] ) && 'location' == $vars['orderby'] ) {

			/* Merge the query vars with our custom variables. */
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'location',
					'orderby' => 'meta_value'
				)
			);
		}
	}

	return $vars;
}

?>