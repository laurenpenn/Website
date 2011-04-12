<?php
	
/* Load the core theme framework. */
require_once( trailingslashit( TEMPLATEPATH ) . 'hybrid-core/hybrid.php' );
$theme = new Hybrid();

/* Execute all functions after the theme is setup. */
add_action( 'after_setup_theme', 'dbc_theme_setup', 10 );

function dbc_theme_setup() {

	/* Get action/filter hook prefix. */
	$prefix = hybrid_get_prefix();
	
	/* Add theme support for core framework features. */
	add_theme_support( 'hybrid-core-menus' );
	add_theme_support( 'hybrid-core-sidebars' );
	add_theme_support( 'hybrid-core-widgets' );
	add_theme_support( 'hybrid-core-shortcodes' );
	add_theme_support( 'hybrid-core-post-meta-box' );
	add_theme_support( 'hybrid-core-seo' );
	add_theme_support( 'hybrid-core-template-hierarchy' );
	add_theme_support( 'hybrid-core-theme-settings' );
	add_theme_support( 'hybrid-core-meta-box-footer' );

	/* Add theme support for framework extensions. */
	add_theme_support( 'post-layouts' );
	add_theme_support( 'post-stylesheets' );
	add_theme_support( 'loop-pagination' );
	add_theme_support( 'get-the-image' );
	add_theme_support( 'breadcrumb-trail' );

	/* Add theme support for WordPress features. */
	add_theme_support( 'automatic-feed-links' );
				
	/* Add actions */ 
	add_action( 'template_redirect', 'archive_redirect' );
	add_action( 'template_redirect', 'dbc_load_scripts' );
	add_action( 'template_redirect', 'dbc_one_column' );
	add_action( 'widgets_init', 'dbc_sidebars' );
	add_action( "{$prefix}_before_html", 'dbc_ie6_detection', 11 );
	add_action( "{$prefix}_header", 'dbc_get_sidebar_header', 11 );
	add_action( "{$prefix}_open_body", 'dbc_facebook_sdk', 12 );
	add_action( "{$prefix}_footer", 'dbc_footer', 11 );
	
	/* Add filters */
	add_filter( 'body_class', 'dbc_body_class' );
	add_filter( 'stylesheet_uri', 'dbc_debug_stylesheet', 10, 2 );
	add_filter( 'sidebars_widgets', 'dbc_disable_sidebars' );

	/* Add shortcodes */
	add_shortcode( 'primary_menu', 'dbc_shortcode_primary_menu' );

}

/**
 * Loads the Javascript.
 *
 * @since 0.1
 */
function dbc_load_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-functions', trailingslashit( THEME_URI ) . 'library/js/scripts.js', array( 'jquery' ), '0.2.1', true );

	if ( is_tax( 'note' ) || get_post_type() == 'note' )
		wp_enqueue_style( 'note', trailingslashit( THEME_URI ) . 'library/css/note.css', false, '0.2.1', 'screen' );
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
	echo '<script type="text/javascript" src="'. trailingslashit( TEMPLATEPATH ) .'/library/js/ie6/warning.js' . '"></script><script>window.onload=function(){e("'. trailingslashit( TEMPLATEPATH ) .'/library/js/ie6/' .'")}</script>';
	echo '<![endif]-->';
}

/**
 * Loads Facebook SDK
 *
 * @since 0.2.1
 */
function dbc_facebook_sdk() {
	?>
	<div id="fb-root"></div>
	<script>
	  window.fbAsyncInit = function() {
	    FB.init({appId: '163053213744962', status: true, cookie: true,
	             xfbml: true});
	  };
	  (function() {
	    var e = document.createElement('script'); e.async = true;
	    e.src = document.location.protocol +
	      '//connect.facebook.net/en_US/all.js';
	    document.getElementById('fb-root').appendChild(e);
	  }());
	</script>
	<?php
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
	require_once( trailingslashit( TEMPLATEPATH ) . 'library/classes/widget-pages.php' );

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

	if ( is_front_page() ) {
		$sidebars_widgets['primary'] = false;
		$sidebars_widgets['secondary'] = false;
	}
		
	return $sidebars_widgets;
}

/**
 * Function for deciding which pages should have a one-column layout.
 *
 * @since 0.2.0
 */
function dbc_one_column() {

	if ( !is_active_sidebar( 'primary' ) && !is_active_sidebar( 'secondary' ) )
		add_filter( 'get_post_layout', 'dbc_post_layout_one_column' );

	elseif ( is_attachment() )
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
	register_sidebar( array( 'name' => __( 'Header', 'dbc' ), 'id' => 'header', 'description' => __( 'Appears at the top of every page, to the right of the logo. This should probably contain the Search widget.', 'dbc' ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
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
	
			<p class="copyright">Copyright &#169; <?php echo date('Y'); ?> <a href="http://dentonbible.org">Denton Bible Church</a>, all rights reserved.</p>
			<p class="credit">Designed by <a href="http://eddierenz.com/" class="highlight">Eddie Renz</a> &amp; built by <a href="http://developdaly.com/" class="highlight">Develop Daly</a>. <a href="http://dentonbible.org/staff-registration/" rel="nofollow">Staff Registration</a> | <?php wp_loginout(); ?></p>
			
		</div>
	
		<div class="footer-right">
	
			<h6>Denton Bible Church</h6>
			
			<p>2300 E. University Dr.<br />
			Denton, TX 76209<br />
			(940) 297-6700</p>
		
		</div>
	</div>
<?php 
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
 * Get the first PDF attached to the current post
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

?>