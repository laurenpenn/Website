<?php
/* Stop the theme from killing WordPress if BuddyPress is not enabled. */
if ( !class_exists( 'BP_Core_User' ) )
	return false;

	
/* Add before and after BuddyPress page hooks */
function hybrid_before_buddypress() {
	do_atomic( 'before_buddypress' );
}

function hybrid_after_buddypress() {
	do_atomic( 'after_buddypress' );
}



/* Load the default BuddyPress AJAX functions */
/* We are going to load the ajax from the BuddyPress plugin directory so we always use the latest version */
require_once( BP_PLUGIN_DIR . '/bp-themes/bp-default/_inc/ajax.php' );

/* We are going to load add the BuddyPress Log In/Out box as a widget */
require_once( trailingslashit( TEMPLATEPATH ) . 'buddypress/bp_log_in_out_widget.php' );


/* Load the BuddyPress javascript and css */
/* We use !bp_is_blog_page() here to only load the JS and CSS on BuddyPress pages to save on load time */
/* We want to load the adminbar css on all pages so it has been taken out of the if statement */
/* We are going to load the JS from the BuddyPress plugin directory so we always use the latest version */
function theme_loaded_init() {
if (!bp_is_blog_page() || ( 'activity' == bp_dtheme_page_on_front() )) {
	wp_enqueue_script( 'bp-js', BP_PLUGIN_URL . '/bp-themes/bp-default/_inc/global.js', array( 'jquery' ) );
	wp_enqueue_style( 'buddypress', trailingslashit( get_bloginfo( 'template_url' ) ) . 'buddypress/style-buddypress.css', false, '0.1', 'screen' );	
	}
}

add_action('wp_head', 'theme_loaded_init', 1);

/* Added to BuddyPress take care of page titles on BuddyPress pages */
if (!bp_is_blog_page() ) {
	add_action( 'wp_title', 'bp_get_page_title');
	}
	
/* Filter the dropdown for selecting the page to show on front to include "Activity Stream" */
function bp_dtheme_wp_pages_filter( $page_html ) {
	if ( !bp_is_active( 'activity' ) )
		return $page_html;

	if ( 'page_on_front' != substr( $page_html, 14, 13 ) )
		return $page_html;

	$selected = false;
	$page_html = str_replace( '</select>', '', $page_html );

	if ( bp_dtheme_page_on_front() == 'activity' )
		$selected = ' selected="selected"';

	$page_html .= '<option class="level-0" value="activity"' . $selected . '>' . __( 'Activity Stream', 'buddypress' ) . '</option></select>';
	return $page_html;
}
add_filter( 'wp_dropdown_pages', 'bp_dtheme_wp_pages_filter' );

/* Hijack the saving of page on front setting to save the activity stream setting */
function bp_dtheme_page_on_front_update( $oldvalue, $newvalue ) {
	if ( !is_admin() || !is_site_admin() )
		return false;

	if ( 'activity' == $_POST['page_on_front'] )
		return 'activity';
	else
		return $oldvalue;
}
add_action( 'pre_update_option_page_on_front', 'bp_dtheme_page_on_front_update', 10, 2 );

/* Load the activity stream template if settings allow */
function bp_dtheme_page_on_front_template( $template ) {
	global $wp_query;

	if ( empty( $wp_query->post->ID ) )
		return locate_template( array( 'activity/index.php' ), false );
	else
		return $template;
}
add_filter( 'page_template', 'bp_dtheme_page_on_front_template' );

/* Return the ID of a page set as the home page. */
function bp_dtheme_page_on_front() {
	if ( 'page' != get_option( 'show_on_front' ) )
		return false;

	return apply_filters( 'bp_dtheme_page_on_front', get_option( 'page_on_front' ) );
}

/* Force the page ID as a string to stop the get_posts query from kicking up a fuss. */
function bp_dtheme_fix_get_posts_on_activity_front() {
	global $wp_query;

	if ( !empty($wp_query->query_vars['page_id']) && 'activity' == $wp_query->query_vars['page_id'] )
		$wp_query->query_vars['page_id'] = '"activity"';
}
add_action( 'pre_get_posts', 'bp_dtheme_fix_get_posts_on_activity_front' );

/* WP 3.0 requires there to be a non-null post in the posts array */
function bp_dtheme_fix_the_posts_on_activity_front( $posts ) {
	global $wp_query;

	// NOTE: the double quotes around '"activity"' are thanks to our previous function bp_dtheme_fix_get_posts_on_activity_front()
	if ( empty( $posts ) && !empty( $wp_query->query_vars['page_id'] ) && '"activity"' == $wp_query->query_vars['page_id'] )
		$posts = array( (object) array( 'ID' => 'activity' ) );

	return $posts;
}
add_filter( 'the_posts', 'bp_dtheme_fix_the_posts_on_activity_front' );


/* Add words that we need to use in JS to the end of the page so they can be translated and still used. */
function bp_dtheme_js_terms() { ?>
<script type="text/javascript">
	var bp_terms_my_favs = '<?php _e( "My Favorites", "buddypress" ) ?>';
	var bp_terms_accepted = '<?php _e( "Accepted", "buddypress" ) ?>';
	var bp_terms_rejected = '<?php _e( "Rejected", "buddypress" ) ?>';
	var bp_terms_show_all_comments = '<?php _e( "Show all comments for this thread", "buddypress" ) ?>';
	var bp_terms_show_all = '<?php _e( "Show all", "buddypress" ) ?>';
	var bp_terms_comments = '<?php _e( "comments", "buddypress" ) ?>';
	var bp_terms_close = '<?php _e( "Close", "buddypress" ) ?>';
	var bp_terms_mention_explain = '<?php printf( __( "%s is a unique identifier for %s that you can type into any message on this site. %s will be sent a notification and a link to your message any time you use it.", "buddypress" ), '@' . bp_get_displayed_user_username(), bp_get_user_firstname(bp_get_displayed_user_fullname()), bp_get_user_firstname(bp_get_displayed_user_fullname()) ); ?>';
	</script>
<?php
}
add_action( 'wp_footer', 'bp_dtheme_js_terms' );
?>