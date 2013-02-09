<?php

/* Register post types. */
add_action('init', 'dbc_admin_register_post_types');

/* Disables sidebars. */
add_filter( 'sidebars_widgets', 'dbc_child_disable_sidebars' );

/* Modifies hybrid_site_title. */
add_filter( 'hybrid_site_title', 'dbc_child_site_title', 12 );

/* Modifies hybrid_entry_title. */
add_filter( 'hybrid_entry_title_shortcode', 'dbc_admin_entry_title' );

/* populate the field with "user_firstname" as the population parameter with the "first_name" of the current user. */
add_filter('gform_field_value_user_firstname', create_function("", '$value = populate_usermeta(\'first_name\'); return $value;' ));

/* populate the field with "user_lastname" as the population parameter with the "last_name" of the current user. */
add_filter('gform_field_value_user_lastname', create_function("", '$value = populate_usermeta(\'last_name\'); return $value;' ));

/* populate the field with "user_email" as the population parameter with the "user_email" of the current user. */
add_filter('gform_field_value_user_email', create_function("", '$value = populate_usermeta(\'user_email\'); return $value;' ));

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
* This function is called by both filters and returns the requested user meta of the current user
*
* @since 0.1
*/
function populate_usermeta($meta_key){
    global $current_user;
    get_currentuserinfo();

    foreach($current_user as $key => $value){
        if($key == $meta_key)
            return $value;
    }

    return '';
}

/**
* Register post types
*
* @since 0.1
*/
function dbc_admin_register_post_types() {
	
	$labels = array(
		'name' => _x('Documentation', 'Documentation'),
		'singular_name' => _x('Documentation', 'Documentation'),
		'add_new' => _x('Add New', 'documentation'),
		'add_new_item' => __('Add New Documentation'),
		'edit_item' => __('Edit Documentation'),
		'new_item' => __('New Documentation'),
		'view_item' => __('View Documentation'),
		'search_items' => __('Search Documentation'),
		'not_found' =>  __('No documentation found'),
		'not_found_in_trash' => __('No documentation found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => 'Docs'
	);
	
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => 4,
		'supports' => array('title','editor','author','thumbnail','excerpt','comments')
	); 
	
	register_post_type('documentation',$args);
}

/**
* Modifies hybrid_entry_title
*
* @since 0.1
*/
function dbc_admin_entry_title() {
	global $post;

	//if ( is_page_template('page-template-home.php') && ( 'documentation' == get_post_type() )  )
		$title = the_title( 'test<h1 class="' . esc_attr( $post->post_type ) . '-title entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h1>', false );


	return $title;

}

?>