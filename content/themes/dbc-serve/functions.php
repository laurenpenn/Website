<?php

add_theme_support( 'hybrid-core-menus' );

add_action( 'init', 'dbv_serve_remove_actions' );

add_action( 'init', 'dbcm_email_missionary' );

add_action( 'init', 'register_cpt_prayer' );
add_action( 'init', 'register_cpt_missionary' );
add_action( 'init', 'register_cpt_location' );

add_action( 'wp_loaded', 'dbc_serve_connection_types', 100 );

add_action( 'save_post', 'dbcm_update_map', 1 ); 

add_action( 'wp_head', 'dbc_serve_custom_background', 11 );

add_action( 'template_redirect', 'dbc_serve_load_scripts' );

add_action( 'dbc_close_body', 'dbcm_email_missionary_modal' );

add_action( 'template_redirect', 'dbc_serve_one_column' );
add_filter( 'sidebars_widgets', 'dbc_serve_disable_sidebars' );
add_filter( 'hybrid_site_title', 'dbc_serve_site_title', 12 );

add_action( 'wp_enqueue_scripts', 'dbc_serve_deregister_styles', 100 );

add_filter( 'gform_pre_render_7', 'dbcm_email_missionary' );

add_filter( 'manage_edit-missionary_columns', 'dbc_serve_edit_missionary_columns' ) ;
add_action( 'manage_missionary_posts_custom_column', 'dbc_serve_manage_missionary_columns', 10, 2 );
/* Only run our customization on the 'edit.php' page in the admin. */
//add_action( 'load-edit.php', 'dbc_serve_edit_missionary_load' );

add_action( 'dbc_footer', 'dbc_serve_footer', 11 );

/**
 * Function for deciding which pages should have a one-column layout.
 *
 * @since 0.2.0
 */
function dbc_serve_one_column() {

	if ( is_archive( 'location' ) || is_archive( 'missionary' ) )
		add_filter( 'get_post_layout', 'dbc_post_layout_one_column' );
	
}

function dbv_serve_remove_actions() {
	remove_action( 'dbc_footer', 'dbc_footer', 11 );
}


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
	
	if ( is_page_template( 'page-template-international.php' ) )
		$sidebars_widgets['primary'] = false;
	
	return $sidebars_widgets;
}

	
function dbc_serve_load_scripts() {

	wp_enqueue_script( 'ammap', trailingslashit( CHILD_THEME_URI ) .'ammap/ammap.js', false, false, true );
	wp_enqueue_script( 'ammap-world_low', trailingslashit( CHILD_THEME_URI ) .'ammap/maps/js/world_low.js', false, false, true );

	if( is_archive( 'location') || is_archive( 'missionary' ) ) {
		wp_enqueue_script( 'imagesloaded', trailingslashit( CHILD_THEME_URI ) .'js/jquery.imagesloaded.min.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'masonry', trailingslashit( CHILD_THEME_URI ) .'js/jquery.masonry.min.js', array( 'jquery' ), false, true );
	}

	wp_enqueue_script( 'chosen', trailingslashit( CHILD_THEME_URI ) .'js/jquery.chosen.min.js', array( 'jquery' ), false, true );	
	wp_enqueue_script( 'app', trailingslashit( CHILD_THEME_URI ) .'js/app.js', array( 'jquery' ), false, true );
	
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
	$idackground = get_background_image();
	if ( $idackground ) {
		?>
		<style type="text/css">
			#container {
				background: none;
			}
		</style>
		<?php
	}
}


function dbc_serve_edit_missionary_columns( $columns ) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Missionary' ),
		'location' => __( 'Location' ),
		'field_director' => __( 'Field Director' ),
		'type' => __( 'Type' ),
		'date' => __( 'Date added' )
	);

	return $columns;
}

function dbc_serve_manage_missionary_columns( $column, $post_id ) {

	switch( $column ) {

		/* If displaying the 'location' column. */
		case 'location' :

			$connected = p2p_type( 'missionary_to_location' )->get_connected( $post_id);

			p2p_list_posts( $connected );

			break;

		/* If displaying the 'field_director' column. */
		case 'field_director' :

			$field_director = get_post_meta( $post_id, 'field-director', true );

			echo $field_director;

			break;

		/* If displaying the 'field_director' column. */
		case 'type' :

			$terms = wp_get_post_terms($post_id, 'type', array("fields" => "all"));
			$count = count($terms);
			if ( $count > 0 ){
			     echo "<ul>";
			     foreach ( $terms as $term ) {
			       echo "<li>" . $term->name . "</li>";
			        
			     }
			     echo "</ul>";
			} else {
				echo 'SERVE Missionary';
			}

			break;
		/* Just break out of the switch statement for everything else. */
		default :
			break;
			
		
	}

}

function dbc_serve_connection_types() {
	if ( !function_exists( 'p2p_register_connection_type' ) )
		return;

	p2p_register_connection_type( array( 
		'name' => 'missionary_to_location',
		'from' => 'missionary',
		'to' => 'location',
		'reciprocal' => true
	) );
}

/**
 * Adds footer information
 *
 * @since 0.1
 */
function dbc_serve_footer() {
?>
	<div class="footer-container">
		<div class="footer-left">
	
			<?php do_shortcode('[primary_menu]'); ?>
			<p class="copyright">Copyright &#169; <?php echo date('Y'); ?> <a href="http://dentonbible.org">Denton Bible Church</a>, all rights reserved.</p>
			<p class="credit"><a href="http://www.serve-intl.com/coco/">Staff/Missionary Login</a> | <a href="http://mail.dbcm.org/">Serve Mail</a> | <a href="http://dentonbible.org/staff-registration/">Staff Registration</a> | <?php wp_loginout(); ?></p>
	
			<?php //hybrid_footer(); // Hybrid footer hook ?>
		
		</div>
	
		<div class="footer-right">

			<div class="vcard">
				<h6 class="org">
					Denton Bible Church
				</h6>
				<div class="adr">
					<div class="street-address">2300 E. University Dr.</div>
					<span class="locality">Denton</span>, <span class="region">TX</span>, <span class="postal-code">76209</span>
				</div>
				<div class="tel">(940) 297-6700</div>
			</div>
		
		</div>
	</div>
<?php 
}

function dbcm_update_map($post_id) {

	// verify if this is an auto save routine. 
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;

	// Check permissions
	if ( 'location' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
	  } else {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	}

	// Get the existing XML file
	$file = get_stylesheet_directory() .'/serve-map-data.xml';
	$dom = simplexml_load_file( $file );
	
	// Loop through 'map'
	foreach ($dom->children() as $map) {
	
		// Loop through 'area'
	    foreach ($map->children() as $area) {
	    	
			$mc_name = get_post_meta( $post_id, 'mc_name', true );
	    	
			// If area already exists, update its attributes
			if ( $area['mc_name'] == $mc_name ) {
				
				$color = get_post_meta( $post_id, 'color', true );
				
				if ( $color == 'None')					$color = '';
				if ( $color == 'SERVE Missionary')		$color = '#FF9900';
				if ( $color == 'BTCP')					$color = '#3366CC';
				if ( $color == 'Affiliate Missionary')	$color = '#DC3912';
				if ( $color == 'Other')					$color = '#109618';

				$area['id'] = 				$post_id;
				$area['mc_name'] =			$mc_name;
				$area['title'] =			get_the_title( $post_id );
				$area['color'] =			$color;
				$area['text_box_x'] =		'30';
				$area['text_box_y'] =		'30%';
				$area['text_box_width'] =	'250';
				$area['text_box_height'] =	'30%';
				
				// Build and update description
				$my_post = get_post($post_id); 
				$content = $my_post->post_content;				

				// Find connected posts
				$connected = new WP_Query( array(
				  'connected_type' => 'missionary_to_location',
				  'connected_items' => $post_id,
				  'nopaging' => true,
				) );
				
				// Display connected posts
				if ( $connected->have_posts() ) :
				$description = '<h3>Missionaries</h3>';
				$description .= '<ul>';
				while ( $connected->have_posts() ) : $connected->the_post();
					$description .= '<li><a href="'. get_permalink() .'">'. get_the_title() .'</a></li>';
				endwhile;
				$description .= '</ul><br />';

				// Prevent weirdness
				wp_reset_postdata();
				
				endif;
				
				$description .= $content;
				
				$area->description = $description;
				
				break;

	    	}
		}
	}

	$dom->asXml( get_stylesheet_directory() ."/serve-map-data.xml" );

}

function dbcm_get_map_data() {
	
	$locations = get_posts( array( 'post_type' => 'location', 'posts_per_page' => -1) );
	
	foreach( $locations as $location ) {
		
		$mc_name = get_post_meta( $location->ID, 'mc_name', true );		
		$color = get_post_meta( $location->ID, 'color', true );
		if ( $color == 'None')					$color = '';
		if ( $color == 'SERVE Missionary')		$color = '#FF9900';
		if ( $color == 'BTCP')					$color = '#3366CC';
		if ( $color == 'Affiliate Missionary')	$color = '#DC3912';
		if ( $color == 'Other')					$color = '#109618';
		
		// Find connected missionaries
		$connected = new WP_Query( array(
		  'connected_type' => 'missionary_to_location',
		  'connected_items' => $location->ID,
		  'nopaging' => true,
		) );
		
		// Display connected posts
		if ( $connected->have_posts() ) :
			$description = '<p>Missionaries</p>';
			$description .= '<ul>';
			while ( $connected->have_posts() ) : $connected->the_post();
				$description .= '<li>'. get_the_title() .'</li>';
			endwhile;		
			$description .= '</ul>';		

			// Prevent weirdness
			wp_reset_postdata();
		else:
			$description = ' '; // Space is intentional
		endif;
		
		echo '{id:"'. $mc_name .'", color:"'. $color .'", description:"'. $description .'", url: "'. get_permalink( $location->ID ) .'"},';
	}

}

function dbcm_email_missionary_modal() {
	?>
	<div id="modal-email-missionary" class="reveal-modal large">
		<?php echo do_shortcode('[gravityform id=7 title=false description=true ajax=true]'); ?>
		<a class="close-reveal-modal">&#215;</a>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
								
				$(".chzn-select").chosen();
				$(".email-missionary-dropdown select").chosen();
			
			});
		</script>
	</div><!-- #modal-email-missionary -->
	<?php
}

function dbcm_email_missionary( $form ){
    
    foreach($form['fields'] as &$field){
        
        if($field['type'] != 'select' || strpos($field['cssClass'], 'email-missionary-dropdown') === false)
            continue;
        
        $args = array(
			'post_type'			=> 'missionary',
			'posts_per_page'	=> -1,
			'meta_query'		=> array(
		       array(
		           'key'		=> 'missionary-email',
		           'value'		=> '',
		           'compare'	=> '!='
		       )
		   )
		);
		
        $posts = get_posts( $args );
        $choices = array(array('text' => 'Select a Missionary', 'value' => ' '));
        
        foreach($posts as $post){
        	$email = antispambot( get_post_meta( $post->ID, 'missionary-email', true ) );
            $choices[] = array( 'text' => $post->post_title, 'value' => $email );
        }
        
        $field['choices'] = $choices;
        
    }
    
    return $form;
}

/**
 * Registers the "prayer" post type
 *
 * @since 1.0
 */
function register_cpt_prayer() {

    $labels = array( 
        'name' => _x( 'Prayers', 'prayer' ),
        'singular_name' => _x( 'Prayer', 'prayer' ),
        'add_new' => _x( 'Add New', 'prayer' ),
        'add_new_item' => _x( 'Add New Prayer', 'prayer' ),
        'edit_item' => _x( 'Edit Prayer', 'prayer' ),
        'new_item' => _x( 'New Prayer', 'prayer' ),
        'view_item' => _x( 'View Prayer', 'prayer' ),
        'search_items' => _x( 'Search Prayers', 'prayer' ),
        'not_found' => _x( 'No prayers found', 'prayer' ),
        'not_found_in_trash' => _x( 'No prayers found in Trash', 'prayer' ),
        'parent_item_colon' => _x( 'Parent Prayer:', 'prayer' ),
        'menu_name' => _x( 'Prayers', 'prayer' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes' ),
        'taxonomies' => array( 'category', 'post_tag' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'prayer', $args );
}

function register_cpt_missionary() {

    $labels = array( 
        'name' => _x( 'Missionaries', 'missionary' ),
        'singular_name' => _x( 'Missionary', 'missionary' ),
        'add_new' => _x( 'Add New', 'missionary' ),
        'add_new_item' => _x( 'Add New Missionary', 'missionary' ),
        'edit_item' => _x( 'Edit Missionary', 'missionary' ),
        'new_item' => _x( 'New Missionary', 'missionary' ),
        'view_item' => _x( 'View Missionary', 'missionary' ),
        'search_items' => _x( 'Search Missionaries', 'missionary' ),
        'not_found' => _x( 'No missionaries found', 'missionary' ),
        'not_found_in_trash' => _x( 'No missionaries found in Trash', 'missionary' ),
        'parent_item_colon' => _x( 'Parent Missionary:', 'missionary' ),
        'menu_name' => _x( 'Missionaries', 'missionary' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,        
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', 'revisions' ),        
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
		'rewrite' => array( 
            'slug' => 'missionaries', 
            'with_front' => true,
            'feeds' => true,
            'pages' => true
        ),
        'capability_type' => 'post'
    );

    register_post_type( 'missionary', $args );
}

function register_cpt_location() {

    $labels = array( 
        'name' => _x( 'Locations', 'location' ),
        'singular_name' => _x( 'Location', 'location' ),
        'add_new' => _x( 'Add New', 'location' ),
        'add_new_item' => _x( 'Add New Location', 'location' ),
        'edit_item' => _x( 'Edit Location', 'location' ),
        'new_item' => _x( 'New Location', 'location' ),
        'view_item' => _x( 'View Location', 'location' ),
        'search_items' => _x( 'Search Locations', 'location' ),
        'not_found' => _x( 'No locations found', 'location' ),
        'not_found_in_trash' => _x( 'No locations found in Trash', 'location' ),
        'parent_item_colon' => _x( 'Parent Location:', 'location' ),
        'menu_name' => _x( 'Locations', 'location' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', 'revisions', 'page-attributes' ),
        
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array( 
            'slug' => 'locations', 
            'with_front' => true,
            'feeds' => true,
            'pages' => true
        ),
        'capability_type' => 'post'
    );

    register_post_type( 'location', $args );
}