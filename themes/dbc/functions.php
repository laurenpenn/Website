<?php
	
/* Load the core theme framework. */
require_once( trailingslashit( TEMPLATEPATH ) . 'library/hybrid.php' );
$theme = new Hybrid();

/* Execute all functions after the theme is setup. */
add_action( 'after_setup_theme', 'dbc_theme_setup', 10 );

function dbc_theme_setup() {

	/* Get action/filter hook prefix. */
	$prefix = hybrid_get_prefix();
	
	/* Load the theme settings. */
	require_once( trailingslashit( TEMPLATEPATH ) . 'admin/theme-settings.php' );
     
     /* Load the custom admin settings. */
	require_once( trailingslashit( TEMPLATEPATH ) . 'admin/admin-theme.php' );

	/* Add theme support for core framework features. */
	add_theme_support( 'hybrid-core-menus' );
	add_theme_support( 'hybrid-core-sidebars', array( 'primary' ) );
	add_theme_support( 'hybrid-core-widgets' );
	add_theme_support( 'hybrid-core-shortcodes' );
	add_theme_support( 'hybrid-core-seo' );
	add_theme_support( 'hybrid-core-template-hierarchy' );
	add_theme_support( 'hybrid-core-theme-settings', array( 'about', 'footer' ) );
	add_theme_support( 'hybrid-core-meta-box-footer' );

	/* Add theme support for framework extensions. */
	add_theme_support( 'breadcrumb-trail' );
	add_theme_support( 'cleaner-caption' );
	add_theme_support( 'cleaner-gallery' );
	add_theme_support( 'get-the-image' );
	add_theme_support( 'loop-pagination' );
	add_theme_support( 'theme-layouts', array( 'layout-default', '2c-l', '2c-r' ) );

	/* Add theme support for WordPress features. */
	add_theme_support( 'automatic-feed-links' );
				
	/* Add actions */ 
	add_action( 'init', 'dbc_remove_header_info' );
	add_action( 'init', 'dbc_register_shortcodes' );
	add_action( 'init', 'dbc_register_taxonomies' );
	add_action( 'init', 'dbc_register_post_types' );	
	add_action( 'template_redirect', 'archive_redirect' );
	add_action( 'template_redirect', 'dbc_load_scripts' );
	add_action( 'template_redirect', 'dbc_one_column' );
	add_action( 'widgets_init', 'dbc_sidebars' );	
	add_action( 'widgets_init', 'dbc_register_widgets' );
	add_action( "{$prefix}_before_html", 'dbc_ie6_detection', 11 );
	add_action( "{$prefix}_header", 'dbc_get_sidebar_header', 11 );
	add_action( "{$prefix}_footer", 'dbc_footer', 11 );
	
	/* Add filters */
	add_filter( 'body_class', 'dbc_body_class' );
	add_filter( 'stylesheet_uri', 'dbc_debug_stylesheet', 10, 2 );
	add_filter( 'sidebars_widgets', 'dbc_disable_sidebars' );
	add_filter( 'breadcrumb_trail', 'dbc_breadcrumb_trail' );

	/* Add shortcodes */
	add_shortcode( 'primary_menu', 'dbc_shortcode_primary_menu' );
	
	if ( function_exists( 'add_image_size' ) ) { 
		add_image_size( 'small-thumb', 80, 80, true ); //300 pixels wide (and unlimited height)
	}

}

/**
 * Removes some of the default header meta that WordPress adds in.
 * Removes some of the default header meta that Hyrbid adds in.
 * We're removing this to make the page HTML5 compatible.
 *
 * @since 0.3
 */
function dbc_remove_header_info() {
	remove_action( 'wp_head', 'rsd_link', 1 );
	remove_action( 'wp_head', 'wlwmanifest_link', 1 );
	remove_action( 'wp_head', 'wp_generator', 1 );
	remove_action( 'wp_head', 'start_post_rel_link', 1 );
	remove_action( 'wp_head', 'index_rel_link', 1 );
	remove_action( 'wp_head', 'adjacent_posts_rel_link', 1 );
	remove_action( 'wp_head', 'hybrid_meta_robots', 1 );
	remove_action( 'wp_head', 'hybrid_meta_author', 1 );
	remove_action( 'wp_head', 'hybrid_meta_copyright', 1 );
	remove_action( 'wp_head', 'hybrid_meta_revised', 1 );
	remove_action( 'wp_head', 'hybrid_meta_description', 1 );
	remove_action( 'wp_head', 'hybrid_meta_keywords', 1 );
	remove_action( 'wp_head', 'hybrid_meta_template', 1 );
}

/**
 * Loads the Javascript.
 *
 * @since 0.1
 */
function dbc_load_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-functions', trailingslashit( THEME_URI ) . 'js/scripts.dev.js', array( 'jquery' ), '0.2.1', true );
	
	wp_enqueue_style( 'skeleton', trailingslashit( THEME_URI ) . 'css/skeleton.css', false, '0.3', 'screen' );
	wp_enqueue_style( 'layout', trailingslashit( THEME_URI ) . 'css/layout.css', false, '0.3', 'screen' );
	
	if ( is_page_template( 'page-template-home.php' ) || is_page_template( 'page-media-home.php' ) ) {
		wp_enqueue_style( 'front-page', trailingslashit( THEME_URI ) . 'css/home.css', false, '0.2.1', 'screen' );
		wp_enqueue_style( 'orbit-css', trailingslashit( THEME_URI ) . 'css/orbit.css', false, '0.2.1', 'screen' );
	}

	if ( is_tax( 'note' ) || is_singular( 'note' ) || is_post_type_archive( 'note' ) )
		wp_enqueue_style( 'note', trailingslashit( THEME_URI ) . 'css/note.css', false, '0.2.1', 'screen' );
}
	
/**
 * Checks the URL for parameters used in the previous DBC website. If they exist
 * then we redirect to the archive site and preserve the parameters.
 *
 * @since 0.1
 */
function archive_redirect() {
	if ( isset( $_GET["pfile"] ) || isset( $_GET["mfile"] ) || isset( $_GET["dir"] ) ) {
		$url = 'Location: http://archive.dentonbible.org/?' . $_SERVER['QUERY_STRING'] . '';
		header( $url );
	}
}

/**
 * Detects IE6 and displays a message
 *
 * @since 0.1
 */
function dbc_ie6_detection(){
	echo '<!--[if IE 6]>';
	echo '<script type="text/javascript" src="'. trailingslashit( TEMPLATEPATH ) .'js/ie6/warning.js' . '"></script><script>window.onload=function(){e("'. trailingslashit( TEMPLATEPATH ) .'/js/ie6/' .'")}</script>';
	echo '<![endif]-->';
}

/**
 * Subsite Title
 *
 * @since 0.1
 */
function dbc_subsite_title() {
	global $blog_id, $dbc_settings;
	if ( $blog_id != 1 ){
		$title = get_bloginfo('name');
		$url = get_bloginfo('url');
		if ( $dbc_settings['logo_src'] != '' ){
			echo '<div id="subsite-title"><a href="'. $url .'" title="'. $title .'"><img src="'. $dbc_settings['logo_src'] .'" alt="'. $title .'" /></div></a>';
		} else {
			echo '<div id="subsite-title"><a href="'. $url .'" title="'. $title .'">'. $title . '</div></a>';		
		}
	}
}

/**
* Override the BuddyPress redirection to previous page
* Instead, it takes the user to the backend
*
* @since 0.1
*/
function change_login_redirect($redirect_to, $request_redirect_to, $user) {
    return get_bloginfo('url').'/wp-admin/';    
}

/**
 * Register DBC's extra widgets.
 *
 * @since 0.1
 * @uses register_widget() Registers individual widgets.
 * @link http://codex.wordpress.org/WordPress_Widgets_Api
 */
function dbc_register_widgets() {

	/* Load each widget file. */
	require_once( trailingslashit( TEMPLATEPATH ) . 'widgets/widget-pages.php' );

	/* Register each widget. */
	register_widget( 'DBC_Widget_Pages' );
}

/**
* Adds a menu for child pages if they exist
* Excludes the home page
*
* @since 0.1
*/
function dbc_child_pages() {
	if ( !is_front_page() ){
		include ( trailingslashit( TEMPLATEPATH ) . 'menu-secondary.php' );
	}
}

/**
* Disable sidebars on the home page
*
* @since 0.1
*/
function dbc_disable_sidebars( $sidebars_widgets ) {

	if ( is_front_page() || is_page_template('page-template-home.php') || is_page_template( 'page-template-full-width.php' ) || is_singular( 'story' ) || is_singular( 'event' ) ) {
		$sidebars_widgets['primary'] = false;
		$sidebars_widgets['secondary'] = false;
	}
	
	if (  ( is_page_template( 'page-template-private.php' ) && !is_user_logged_in() ) || is_search() ) $sidebars_widgets['primary'] = false;
		
	return $sidebars_widgets;
}

/**
 * Function for deciding which pages should have a one-column layout.
 *
 * @since 0.2.0
 */
function dbc_one_column() {

	if ( is_attachment() )
		add_filter( 'get_post_layout', 'dbc_post_layout_one_column' );

	elseif ( is_search() ) 
		add_filter( 'get_post_layout', 'dbc_post_layout_one_column' );
	
}

/**
 * Filters 'get_post_layout' by returning 'layout-1c'.
 *
 * @since 0.2.0
 */
function dbc_post_layout_one_column( $layout ) {
	return 'layout-1c';
}

/**
* Registers new, custom sidebars
*
* @since 0.1
*/
function dbc_sidebars() {
	register_sidebar( array( 'name' => __( 'Home Sidebar', 'dbc' ), 'id' => 'home', 'description' => __( 'The left hand sidebar on the home page (under the Welcome box).', 'dbc' ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
	register_sidebar( array( 'name' => __( 'Header', 'dbc' ), 'id' => 'header', 'description' => __( 'Appears at the top of every page, to the right of the logo. This should probably contain the Search widget.', 'dbc' ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
	register_sidebar( array( 'name' => __( 'What\'s Happening', 'dbc' ), 'id' => 'whats-happening', 'description' => __( 'Underneath "This Week\'s Message box.', 'dbc' ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
}

/**
 * Loads the Header Sidebar
 *
 * @since 0.1
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function dbc_get_sidebar_header() {
	get_sidebar( 'header' );
}

/**
 * Loads the What's Happening Sidebar
 *
 * @since 0.1
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function dbc_get_sidebar_whats_happening() {
	get_sidebar( 'whats-happening' );
}

/**
 * Creates a shortcode to output the primary menu
 *
 * @since 0.1
 */
function dbc_shortcode_primary_menu($atts) {
	return wp_nav_menu('primary');
}

/**
 * Displays the breadcrumb trail.  Calls the get_the_breadcrumb() function.
 * Use the get_the_breadcrumb_args filter hook.  The hybrid_breadcrumb_args 
 * filter is deprecated.
 *
 * @deprecated 0.5 Theme still needs this function.
 * @todo Find an elegant way to transition to breadcrumb_trail() 
 * in child themes and filter breadcrumb_trail_args instead.
 *
 * @since 0.1
 */
function dbc_breadcrumb() {
	dbc_breadcrumb_trail( array( 'front_page' => false, 'singular_post_taxonomy' => 'category' ) );
}

/**
 * Turns BuddyPress into a private community
 *
 * @since 0.1
 */
function dbc_walled_garden(){
	global $bp;
	
	if( bp_is_register_page() || bp_is_activation_page() )
	return;
	
	if( ! is_user_logged_in() )
	bp_core_redirect( $bp->root_domain .'/'. BP_REGISTER_SLUG );
}

/**
 * Registers shortcodes
 *
 * @since 0.1
 */
function dbc_register_shortcodes() {
	add_shortcode( 'slideshow', 'dbc_slideshow_shortcode' );
}

/**
 * Slideshow that displays in all available media
 *
 * @since 0.1
 */
function dbc_slideshow_shortcode( $attr ) {
	global $post;

	$defaults = array(
		'order' => 'ASC',
		'orderby' => 'menu_order ID',
		'id' => $post->ID,
		'size' => 'thumbnail',
		'include' => '',
		'exclude' => '',
		'numberposts' => -1,
	);
	extract( shortcode_atts( $defaults, $attr ) );

	/* Arguments for get_children(). */
	$children = array(
		'post_parent' => intval( $id ),
		'post_status' => 'inherit',
		'post_type' => 'attachment',
		//'post_mime_type' => 'image',
		'order' => $order,
		'orderby' => $orderby,
		'exclude' => absint( $exclude ),
		'include' => absint( $include ),
		'numberposts' => intval( $numberposts ),
	);

	/* Get image attachments. If none, return. */
	$attachments = get_children( $children );

	if ( empty( $attachments ) )
		return '';

	/* If is feed, leave the default WP settings. We're only worried about on-site presentation. */
	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link( $id, $size, true ) . "\n";
		return $output;
	}

	$slideshow = '<div class="slideshow"><div class="slideshow-items">';

	$i = 0;

	foreach ( $attachments as $attachment ) {

		/* Open item. */
		$slideshow .= '<div class="slideshow-item item item-' . ++$i . '">';

		if ( $attachment->post_mime_type == 'video/x-flv' ) {
			
			/* Get video. */
			$preview_image = get_the_post_thumbnail( $post->id );
				
			$slideshow .= 	'<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="655" height="358">';
			$slideshow .= 	'<param name="movie" value="http://dentonbible.org/wp-content/themes/dbc/player/player.swf" />';
			$slideshow .= 	'<param name="allowfullscreen" value="true" />';
			$slideshow .= 	'<param name="allowscriptaccess" value="always" />';
			$slideshow .=   '<param name="flashvars="file='. $attachment->guid .'&image='. $preview_image .'&skin=http://dentonbible.org/wp-content/themes/dbc/player/modieus.zip">';
	
			$slideshow .= 	'<param name="flashvars" value="'. $attachment->guid .'" />';
			$slideshow .= 	'<embed ';
				$slideshow .= 	'type="application/x-shockwave-flash"';
				$slideshow .= 	'id="player2"';
				$slideshow .= 	'name="player2"';
				$slideshow .= 	'src="http://dentonbible.org/wp-content/themes/dbc/player/player.swf"'; 
				$slideshow .= 	'width="655" ';
				$slideshow .= 	'height="358"';
				$slideshow .= 	'allowscriptaccess="always" ';
				$slideshow .= 	'allowfullscreen="true"';
				$slideshow .= 	'flashvars="file='. $attachment->guid .'&image='. $preview_image .'&skin=http://dentonbible.org/wp-content/themes/dbc/player/modieus.zip" ';
			$slideshow .= 	'/>';
			$slideshow .= 	'</object>';
		
		} else {
		
			/* Get image. */
			$slideshow .= wp_get_attachment_link( $attachment->ID, 'large', true, false );
		
		}

		/* Check for caption. */
		if ( !empty( $attachment->post_excerpt ) )
			$caption = $attachment->post_excerpt;
		elseif ( !empty( $attachment->post_content ) )
			$caption = $attachment->post_content;
		else
			$caption = '';

		if ( !empty( $caption ) ) {
			$slideshow .= '<div class="slideshow-caption">';
			$slideshow .= '<a class="slideshow-caption-control">' . __( 'Caption', 'dbc' ) . '</a>';
			$slideshow .= '<div class="slideshow-caption-text">' . $caption . '</div>';
			$slideshow .= '</div>';
		}

		$slideshow .= '</div>';
	}

	$slideshow .= '</div><div class="slideshow-controls">';

		$slideshow .= '<span class="slideshow-pager"></span>';
		$slideshow .= '<a class="slider-prev">' . __( 'Previous', 'dbc' ) . '</a>';
		$slideshow .= '<a class="slider-next">' . __( 'Next', 'dbc' ) . '</a>';

	$slideshow .= '</div>';

	$slideshow .= '</div><!-- End slideshow. -->';

	return $slideshow;
}


/**
 * Pagination
 *
 * @since 0.1
 */
function dbc_pagination( $args = array() ) {
	global $wp_rewrite, $wp_query, $post;

	if ( 1 >= $wp_query->max_num_pages )
		return;
	
	$current = ( get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );

	$max_page = intval( $wp_query->max_num_pages );

	/* Set up some default arguments for the paginate_links() function. */
	$defaults = array(
		'base' => add_query_arg( 'page', '%#%' ),
		'format' => '',
		'total' => $max_page,
		'current' => $current,
		'echo' => true,
		'prev_next' => true,
		'prev_text' => __( '&laquo; Previous' ),
		'next_text' => __( 'Next &raquo;' ),
		'end_size' => 1,
		'mid_size' => 1,
		'add_fragment' => ''
	);

	/* Add the $base argument to the array if the user is using permalinks. */
	if ( $wp_rewrite->using_permalinks() )
		$defaults['base'] = user_trailingslashit( trailingslashit( get_pagenum_link() ) . 'page/%#%' );

	/* Merge the arguments input with the defaults. */
	$args = wp_parse_args( $args, $defaults );

	/* Get the paginated links. */
	$page_links = paginate_links( $args );

	/* Remove 'page/1' from the entire output since it's not needed. */
	$page_links = str_replace( 'page/1\'', '\'', $page_links );

	/* Wrap the paginated links in a wrapper element. */
	$page_links = "<div class='pagination wp-pagenavi'>" . $page_links . "</div>";

	/* Return the paginated links for use in themes. */
	echo $page_links;
}

/**
 * Adds footer information
 *
 * @since 0.1
 */
function dbc_footer() {
?>
	<div class="footer-container">
		<div class="footer-left">
	
			<?php do_shortcode('[primary_menu]'); ?>
			<p class="copyright">Copyright &#169; <?php echo date('Y'); ?> <a href="http://dentonbible.org">Denton Bible Church</a>, all rights reserved.</p>
			<p class="credit">Designed by <a href="http://pixelightcreative.com/" class="highlight">Pixelight Creative</a> &amp; built by <a href="http://developdaly.com/" class="highlight">Develop Daly</a>. <a href="http://dentonbible.org/staff-registration/">Staff Registration</a> | <?php wp_loginout(); ?></p>
	
			<?php //hybrid_footer(); // Hybrid footer hook ?>
		
		</div>
	
		<div class="footer-right vcard">
	
			<h6 class="org">Denton Bible Church</h6>
			<div class="adr">
				<div class="street-address">2300 E. University Dr.</div>
				<span class="locality">Denton</span>, <span class="region">TX</span> <span class="postal-code">76209</span>
			</div>
			<div class="tel">(940) 297-6700</div>
		
		</div>
	</div>
<?php 
}


/**
 * Registers custom post types for the theme. We're registering the Publication and Note post types.
 *
 * @since 0.2.0
 */
function dbc_register_post_types() {

	global $blog_id;

	$domain = 'dbc';
	$prefix = hybrid_get_prefix();

	/* Labels for the publication post type. */
	$publication_labels = array(
		'name' => __( 'Publications', $domain ),
		'singular_name' => __( 'Publication', $domain ),
		'add_new' => __( 'Add New', $domain ),
		'add_new_item' => __( 'Add New Publication', $domain ),
		'edit' => __( 'Edit', $domain ),
		'edit_item' => __( 'Edit Publication', $domain ),
		'new_item' => __( 'New Publication', $domain ),
		'view' => __( 'View Publication', $domain ),
		'view_item' => __( 'View Publication', $domain ),
		'search_items' => __( 'Search Publications', $domain ),
		'not_found' => __( 'No publications found', $domain ),
		'not_found_in_trash' => __( 'No publications found in Trash', $domain ),
		'parent_item_colon' => __( 'Publication Note:', $domain ),
		'menu_name' => __( 'Publications', $domain )
	);

	/* Arguments for the publication post type. */
	$publication_args = array(
		'labels' => $publication_labels,
		'hierarchical' => false,
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		'taxonomies' => array( 'publication-type' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 9,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => array( 'slug' => 'publications', 'with_front' => false ),
		'capability_type' => 'post'
	);
	
	/* Labels for the note post type. */
	$note_labels = array(
		'name' => __( 'Tom\'s Notes', $domain ),
		'singular_name' => __( 'Tom\'s Note', $domain ),
		'add_new' => __( 'Add New', $domain ),
		'add_new_item' => __( 'Add New Note', $domain ),
		'edit' => __( 'Edit', $domain ),
		'edit_item' => __( 'Edit Note', $domain ),
		'new_item' => __( 'New Note', $domain ),
		'view' => __( 'View Note', $domain ),
		'view_item' => __( 'View Note', $domain ),
		'search_items' => __( 'Search Note', $domain ),
		'not_found' => __( 'No notes found', $domain ),
		'not_found_in_trash' => __( 'No notes found in Trash', $domain ),
		'parent_item_colon' => __( 'Parent Note:', $domain ),
		'menu_name' => __( 'Notes', $domain )
	);

	/* Arguments for the note post type. */
	$note_args = array(
		'labels' => $note_labels,
		'hierarchical' => false,
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 7,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => array( 'slug' => 'note', 'with_front' => false ),
		'capability_type' => 'post'
	);						

	/* Labels for the story type. */
	$story_labels = array(
		'name' => __( 'Stories', $domain ),
		'singular_name' => __( 'Story', $domain ),
		'add_new' => __( 'Add New', $domain ),
		'add_new_item' => __( 'Add New Story', $domain ),
		'edit' => __( 'Edit', $domain ),
		'edit_item' => __( 'Edit Story', $domain ),
		'new_item' => __( 'New Story', $domain ),
		'view' => __( 'View Story', $domain ),
		'view_item' => __( 'View Story', $domain ),
		'search_items' => __( 'Search Story', $domain ),
		'not_found' => __( 'No stories found', $domain ),
		'not_found_in_trash' => __( 'No stories found in Trash', $domain ),
		'parent_item_colon' => __( 'Parent Story:', $domain ),
		'menu_name' => __( 'Stories', $domain )
	);

	/* Arguments for the story post type. */
	$story_args = array(
		'labels' => $story_labels,
		'hierarchical' => false,
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		'taxonomies' => array( 'publication-type' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => array( 'slug' => 'stories', 'with_front' => false ),
		'capability_type' => 'post'
	);		

	/* Register the story post type. */
	if ( $blog_id == 1 ) register_post_type( apply_filters( 'dbc_story_post_type', 'story' ), apply_filters( 'dbc_story_post_type_args', $story_args ) );

	/* Register the note post type. */
	if ( $blog_id == 1 ) register_post_type( apply_filters( 'dbc_note_post_type', 'note' ), apply_filters( 'dbc_note_post_type_args', $note_args ) );
			
	/* Register the publication post type. */
	if ( $blog_id == 1 ) register_post_type( apply_filters( 'dbc_publication_post_type', 'publication' ), apply_filters( 'dbc_publication_post_type_args', $publication_args ) );
	
	add_action( 'admin_head', 'cpt_icons' );
	function cpt_icons() {
	    ?>
	    <style type="text/css" media="screen">
			#menu-posts-story .wp-menu-image {
				background: url('<?php bloginfo('template_url') ?>/images/book-open-list.png') no-repeat 6px -17px !important;
			}
			#menu-posts-note .wp-menu-image {
				background: url('<?php bloginfo('template_url') ?>/images/document-sticky-note.png') no-repeat 6px -17px !important;
			}
			#menu-posts-publication .wp-menu-image {
				background: url('<?php bloginfo('template_url') ?>/images/document-pdf-text.png') no-repeat 6px -17px !important;
			}
			#menu-posts-story:hover .wp-menu-image, #menu-posts-story.wp-has-current-submenu .wp-menu-image,
			#menu-posts-note:hover .wp-menu-image, #menu-posts-note.wp-has-current-submenu .wp-menu-image,
			#menu-posts-publication:hover .wp-menu-image, #menu-posts-publication.wp-has-current-submenu .wp-menu-image {
				background-position:6px 7px!important;
			}

	    </style>
	<?php }
	
}
/**
 * Register taxonomies
 *
 * @since 0.2.0
 */
function dbc_register_taxonomies() {

	$publication_type_labels = array(
    	'name' => __( 'Publication Type' ),
    	'singular_name' => __( 'Publication Type' ),
    	'search_items' =>  __( 'Search Publication Types' ),
    	'all_items' => __( 'All Publication Types' ),
    	'parent_item' => __( 'Parent Publication Type' ),
    	'parent_item_colon' => __( 'Parent Publication Type:' ),
    	'edit_item' => __( 'Edit Publication Type' ), 
    	'update_item' => __( 'Update Publication Type' ),
    	'add_new_item' => __( 'Add New Publication Type' ),
    	'new_item_name' => __( 'New Genre Publication Type' ),
	); 	

	register_taxonomy( 'publication-type', array( 'publication' ), array(
	    'hierarchical' => true,
	    'labels' => $publication_type_labels,
	    'show_ui' => true,
	    'query_var' => true,
	    'rewrite' => array( 'slug' => 'publication-type' ),
	));

}

/**
 * Adds browser detection to the body class
 *
 * @since 0.2.0
 */
function dbc_body_class( $classes ) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome;

	$browsers = array( 'gecko' => $is_gecko, 'opera' => $is_opera, 'lynx' => $is_lynx, 'ns4' => $is_NS4, 'safari' => $is_safari, 'chrome' => $is_chrome, 'msie' => $is_IE );
	foreach ( $browsers as $key => $value ) {
		if ( $value ) {
			$classes[] = $key;
			break;
		}
	}

	return $classes;
}

/**
 * Uses the development stylesheet (style.dev.css) when SCRIPT_DEBUG equals true in wp-config.php
 *
 * @since 0.2.0
 */
function dbc_debug_stylesheet( $stylesheet_uri, $stylesheet_dir_uri ) {

	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );
		$stylesheet = str_replace( '.css', '.dev.css', $stylesheet );

		if ( file_exists( trailingslashit( STYLESHEETPATH ) . $stylesheet ) )
			$stylesheet_uri = trailingslashit( $stylesheet_dir_uri ) . $stylesheet;
	}

	return $stylesheet_uri;
}

/**
 * Get the first PDF's URL attached to the current post.
 *
 * @since 0.2.0
 */
function dbc_get_post_pdf() {
	global $post;

	$attachments = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'application/pdf', 'order' => 'ASC', 'orderby' => 'menu_order ID') );

	if ($attachments) {
		$attachment = array_shift($attachments);
		return wp_get_attachment_url($attachment->ID);
	}

	return false;
}

/**
 * Uses a custom field to populate the title of a publication if one exists.
 * The function falls back to using the post title.
 *
 * @since 0.2.0
 */
function dbc_publication_title() {
	global $post;

	$key = get_post_meta($post->ID, 'public-title', true);
	
	if ( !empty( $key ) ) {
		echo $key;
	} else {
		echo get_the_title();
	}

}

/**
 * Uses a custom field to populate the title of a publication if one exists.
 * The function falls back to using the post title.
 *
 * @since 0.2.0
 */
function dbc_publication_link() {
	global $post;

	$key = get_post_meta($post->ID, 'publication-link', true);
	
	$args = array(
		'post_type' => 'attachment',
		'numberposts' => 51,
		'post_status' => null,
		'post_parent' => $post->ID
		); 
	
	$attachments = get_posts($args);
	
	if ($attachments & empty( $key ) ) {
		foreach ($attachments as $attachment) {
			if ( $attachment->post_mime_type == 'application/pdf')
				$link = $attachment->guid;
		}
	} elseif ( !empty( $key ) ) {
		$link = $key;
	} else {
		$link = get_permalink();
	}
	
	return $link;

}

function dbc_breadcrumb_trail( $breadcrumb ) {
	$sr_post_type = get_post_type();
	$post_type = get_post_type_object( $sr_post_type );
	$search = 'Home</a> <span class="sep">/</span>';
	$replace = 'Home</a> <span class="sep">/</span> <a href="/' . $post_type->rewrite[slug] . '">' . $post_type->labels->name . '</a> <span class="sep">/</span>';
	$breadcrumb = str_replace( $search, $replace, $breadcrumb );
	return $breadcrumb;
}

?>