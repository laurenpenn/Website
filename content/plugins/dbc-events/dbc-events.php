<?php 
/*
 *  Plugin Name: DBC Events
 *  Description: Event/calendar functionality
 *  Version: 1.1
 *  Author: Patrick Daly
 *  Author URI: http://developdaly.com/
 * 
 *  Plugin derived from code and tutorial at http://www.noeltock.com/web-design/wordpress/custom-post-types-events-pt1/
 *  
 */

add_action( 'init', 'dbc_event_post_types' );
add_action( 'init', 'dbc_remove_actions' );
//add_action( 'admin_print_styles' . $page, 'dbc_events_admin_styles' );
//add_action( 'admin_print_scripts' . $page, 'dbc_events_admin_scripts' );
//add_action( 'admin_init', 'event_create' );
add_action( 'admin_head', 'dbc_events_icons' );
add_action( 'manage_posts_custom_column', 'event_custom_columns' );
add_action( 'save_post', 'save_event' );

add_filter( 'post_updated_messages', 'events_updated_messages' );
add_filter( 'manage_edit-event_columns', 'event_edit_columns' );

add_shortcode( 'events-full', 'event_full' );
add_shortcode( 'events-sidebar', 'event_sidebar' );

/**
 * Remove defaults set by WordPress or other plugins
 *
 * @since 1.0
 */
function dbc_remove_actions() {
	
	// Removes the post expirator plugin meta box
	remove_action( 'edit_form_advanced','expirationdate_meta_custom' );
}

/**
 * Queues stylesheets into the admin
 *
 * @since 1.0
 */
function dbc_events_admin_styles() {
	wp_enqueue_style( 'events', plugins_url( basename( dirname( __FILE__ ) ) . '/css/events.css' ), false );
	wp_enqueue_style( 'datepicker', plugins_url( basename( dirname( __FILE__ ) ) . '/css/redmond/jquery-ui-1.8.16.custom.css' ), false );
}

/**
 * Queues script files into the admin
 *
 * @since 1.0
 */
function dbc_events_admin_scripts() {
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-datepicker', plugins_url( basename( dirname( __FILE__ ) ) ) . '/js/jquery.ui.datepicker.js', array( 'jquery' ) );
	wp_enqueue_script( 'dbc-events', plugins_url( basename( dirname( __FILE__ ) ) ) . '/js/scripts.js', array( 'jquery' ) );
}

/**
 * Registers the "event" post type
 *
 * @since 1.0
 */
function dbc_event_post_types() {

	$labels = array(
		'name' => _x('Events', 'post type general name'),
		'singular_name' => _x('Event', 'post type singular name'),
		'add_new' => _x('Add New', 'events'),
		'add_new_item' => __('Add New Event'),
		'edit_item' => __('Edit Event'),
		'new_item' => __('New Event'),
		'view_item' => __('View Event'),
		'search_items' => __('Search Events'),
		'not_found' =>  __('No events found'),
		'not_found_in_trash' => __('No events found in Trash'),
		'parent_item_colon' => ''
	);
	
	$args = array(
		'label' => __('Events'),
		'labels' => $labels,
		'public' => true,
		'can_export' => true,
		'show_ui' => true,
		'_builtin' => false,
		'_edit_link' => 'post.php?post=%d', // ?
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => array( "slug" => "events" ),
		'supports'=> array('title', 'thumbnail', 'excerpt', 'editor'),
		'show_in_nav_menus' => true,
		'menu_position' => 5,
		'has_archive' => true
	);
	
	register_post_type( 'event', $args );

}

/**
 * Adds CSS to the admin header that places an icon on the "event"
 * post type menu item
 *
 * @since 1.0
 */
function dbc_events_icons() {
	?>
	<style type="text/css" media="screen">
		#menu-posts-event .wp-menu-image {
			background: url('<?php echo plugins_url( '/images/calendar-day.png', __FILE__ ); ?>') no-repeat 6px -17px !important;
		}
		#menu-posts-event:hover .wp-menu-image, #menu-posts-event.wp-has-current-submenu .wp-menu-image {
			background-position:6px 7px!important;
		}

	</style>
<?php }

/**
 * Creates new admin columns
 *
 * @since 1.0
 */
function event_edit_columns($columns) {

	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		//"tf_col_ev_date" => "Dates",
		//"tf_col_ev_times" => "Times",
		"tf_col_ev_thumb" => "Thumbnail",
		"title" => "Event",
		"tf_col_ev_desc" => "Description",
		);

	return $columns;

}

/**
 * Creates admin column content
 *
 * @since 1.0
 */
function event_custom_columns($column) {

	global $post;
	$custom = get_post_custom();
	switch ($column)

		{
			/*
			case "tf_col_ev_date":
				// - show dates -
				$startd = $custom["event_startdate"][0];
				$endd = $custom["event_enddate"][0];
				if ( !empty( $startd ) ) $startdate = date("F j, Y", $startd);
				if ( !empty( $endd ) ) $enddate = date("F j, Y", $endd);
				echo $startdate . '<br /><em>' . $enddate . '</em>';
			break;
			case "tf_col_ev_times":
				// - show times -
				$startt = $custom["event_startdate"][0];
				$endt = $custom["event_enddate"][0];
				$time_format = get_option('time_format');
				if ( !empty( $startt ) ) $starttime = date($time_format, $startt);
				if ( !empty( $endt ) ) $endtime = date($time_format, $endt);
				echo $starttime . ' - ' .$endtime;
			break;
			 * */
			case "tf_col_ev_thumb":
				// - show thumb -
				$post_image_id = get_post_thumbnail_id(get_the_ID());
				if ($post_image_id) {
					$thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
					if ($thumbnail) (string)$thumbnail = $thumbnail[0];
					echo '<img src="';
					echo bloginfo('template_url');
					echo '/timthumb/timthumb.php?src=';
					echo $thumbnail;
					echo '&h=60&w=60&zc=1" alt="" />';
				}
			break;
			case "tf_col_ev_desc";
				the_excerpt();
			break;

		}
}

/**
 * Registers an events meta box for the "event" post type
 *
 * @since 1.0
 */
function event_create() {
	add_meta_box('event_meta', 'Events', 'event_meta', 'event');
}

/**
 * Creates the content of the aforementioned events meta box
 *
 * @since 1.0
 */
function event_meta () {

	// - grab data -

	global $post;
	
	$cf_event_startdate = get_post_meta($post->ID, 'event_startdate', true);
	$cf_event_enddate = get_post_meta($post->ID, 'event_enddate', true);
	
	$custom = get_post_custom($post->ID);
	$meta_sd = $custom["event_startdate"][0];
	$meta_ed = $custom["event_enddate"][0];
	$meta_st = $meta_sd;
	$meta_et = $meta_ed;

	// - grab wp time format -

	$date_format = get_option('date_format'); // Not required in my code
	$time_format = get_option('time_format');

	// - populate today if empty, 00:00 for time -

	if ($meta_sd == null) { $meta_sd = time(); $meta_ed = $meta_sd; $meta_st = 0; $meta_et = 0;}

	// - convert to pretty formats -

	$clean_sd = date("D, M d, Y", $meta_sd);
	$clean_ed = date("D, M d, Y", $meta_ed);
	$clean_st = date($time_format, $meta_st);
	$clean_et = date($time_format, $meta_et);

	// - security -

	echo '<input type="hidden" name="tf-events-nonce" id="tf-events-nonce" value="' .
	wp_create_nonce( 'tf-events-nonce' ) . '" />';

	// - output -

	?>
	<div class="tf-meta">
		<ul>
			<li><label>Start Date</label><input name="event_startdate" class="dbc-event-date" value="<?php if ( !empty( $cf_event_startdate ) ) echo $clean_sd; ?>" /></li>
			<li><label>Start Time</label><input name="event_starttime" value="<?php if ( !empty( $meta_st ) ) echo $clean_st; ?>" /><em>Use 24h format (7pm = 19:00)</em></li>
			<li><label>End Date</label><input name="event_enddate" class="dbc-event-date" value="<?php if ( !empty( $cf_event_enddate ) ) echo $clean_ed; ?>" /></li>
			<li><label>End Time</label><input name="event_endtime" value="<?php if ( !empty( $meta_et ) ) echo $clean_et; ?>" /><em>Use 24h format (7pm = 19:00)</em></li>
		</ul>
	</div>
	<?php
}

/**
 * Saves the content of our meta box
 *
 * @since 1.0
 */
function save_event(){

	global $post;

	// - still require nonce

	if ( !wp_verify_nonce( $_POST['tf-events-nonce'], 'tf-events-nonce' )) {
		return $post->ID;
	}

	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	// - convert back to unix & update post

	if(!isset($_POST["event_startdate"])):
		return $post;
		endif;
		$updatestartd = strtotime ( $_POST["event_startdate"] . $_POST["event_starttime"] );
		update_post_meta($post->ID, "event_startdate", $updatestartd );

	if(!isset($_POST["event_enddate"])):
		return $post;
		endif;
		$updateendd = strtotime ( $_POST["event_enddate"] . $_POST["event_endtime"]);
		update_post_meta($post->ID, "event_enddate", $updateendd );

}

/**
 * Customizes the update messages
 *
 * @since 1.0
 */
function events_updated_messages( $messages ) {

  global $post, $post_ID;

  $messages['event'] = array(
	0 => '', // Unused. Messages start at index 1.
	1 => sprintf( __('Event updated. <a href="%s">View item</a>'), esc_url( get_permalink($post_ID) ) ),
	2 => __('Custom field updated.'),
	3 => __('Custom field deleted.'),
	4 => __('Event updated.'),
	/* translators: %s: date and time of the revision */
	5 => isset($_GET['revision']) ? sprintf( __('Event restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	6 => sprintf( __('Event published. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
	7 => __('Event saved.'),
	8 => sprintf( __('Event submitted. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	9 => sprintf( __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>'),
	  // translators: Publish box date format, see http://php.net/date
	  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
	10 => sprintf( __('Event draft updated. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

/**
 * Creates the content for a shortcode to list events
 * 
 * Example: [events-full limit='20']
 *
 * @since 1.0
 */
function event_full ( $atts ) {

	// - define arguments -
	extract(shortcode_atts(array(
	    'limit' => '99', // # of events to show
	 ), $atts));
	
	// ===== OUTPUT FUNCTION =====
	
	ob_start();
	
	// ===== LOOP: FULL EVENTS SECTION =====
	
	// - hide events that are older than 6am today (because some parties go past your bedtime) -
	
	$today6am = strtotime('today 6:00') + ( get_option( 'gmt_offset' ) * 3600 );
	
	// - query -
	global $wpdb;
	$querystr = "
		SELECT *
		FROM $wpdb->posts wposts, $wpdb->postmeta metastart, $wpdb->postmeta metaend
		WHERE (wposts.ID = metastart.post_id AND wposts.ID = metaend.post_id)
		AND (metaend.meta_key = 'event_enddate' AND metaend.meta_value > $today6am OR metaend.meta_value = '' )
		AND metastart.meta_key = 'event_enddate'
		AND wposts.post_type = 'event'
		AND wposts.post_status = 'publish'
		GROUP BY wposts.ID
		ORDER BY wposts.post_title ASC LIMIT $limit
	 ";
	
	$events = $wpdb->get_results($querystr, OBJECT);

	// - declare fresh day -
	$daycheck = null;
	
	// - loop -
	if ($events):
	global $post;
	foreach ($events as $post):
	setup_postdata($post);
	
	// - custom variables -
	$custom = get_post_custom(get_the_ID());
	$sd = $custom["event_startdate"][0];
	$ed = $custom["event_enddate"][0];
	
	// single day event
	if ( !empty( $sd ) ) $longdate = date("F j, Y g:iA", $sd);
	if ( !empty( $ed ) ) $longdate .= date(" - g:iA", $ed);
	
	// multiple day event
	if ( !empty( $sd ) || !empty( $ed ) ) {
		if ( date("F j, Y", $sd) != date("F j, Y", $ed) )
			$longdate = date("F j, Y g:iA", $sd) .' - ' . date("F j @ g:iA", $ed);
	}
	
	?>
	<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

		<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>

		<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
		
		<?php //echo '<div class="byline">' . $longdate .'</div>'; ?>

		<?php get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'small-thumb' ) ); ?>
		
		<div class="entry-summary">
			<?php the_excerpt(); ?>
			<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', hybrid_get_textdomain() ), 'after' => '</p>' ) ); ?>
		</div><!-- .entry-summary -->

		<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">' . __( '[entry-terms taxonomy="category" before="Posted in "] [entry-terms before="| Tagged "] [entry-comments-link before=" | "]', hybrid_get_textdomain() ) . '</div>' ); ?>

		<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>

	</div><!-- .hentry -->
	<?php
	
	// - fill daycheck with the current day -
	$daycheck = $longdate;
	
	endforeach;
	else :
	endif;
	
	// ===== RETURN: FULL EVENTS SECTION =====
	
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

/**
 * Creates the content for a shortcode to list events
 * 
 * Example: [events-sidebar limit='20']
 *
 * @since 1.0
 */
function event_sidebar ( $atts ) {

	// - define arguments -
	extract(shortcode_atts(array(
	    'limit' => '5', // # of events to show
	 ), $atts));
	
	// ===== OUTPUT FUNCTION =====
	
	ob_start();
	
	// ===== LOOP: FULL EVENTS SECTION =====
	
	// - hide events that are older than 6am today (because some parties go past your bedtime) -
	
	$today6am = strtotime('today 6:00') + ( get_option( 'gmt_offset' ) * 3600 );
	
	// - query -
	global $wpdb;
	$querystr = "
	    SELECT *
	    FROM $wpdb->posts wposts, $wpdb->postmeta metastart, $wpdb->postmeta metaend
	    WHERE (wposts.ID = metastart.post_id AND wposts.ID = metaend.post_id)
	    AND (metaend.meta_key = 'event_enddate' AND metaend.meta_value > $today6am )
	    AND metastart.meta_key = 'event_enddate'
	    AND wposts.post_type = 'event'
	    AND wposts.post_status = 'publish'
	    ORDER BY wposts.post_title ASC LIMIT $limit
	 ";
	
	$events = $wpdb->get_results($querystr, OBJECT);
	
	// - declare fresh day -
	$daycheck = null;
	
	// - loop -
	if ($events):
	global $post;
		
		echo '<div class="loop loop-events">';
		echo '<h3>Upcoming Events <span class="all"><a href="'. get_bloginfo( 'siteurl' ) .'/events/">view all</a></span></h3>';
		echo '<ul>';
			foreach ($events as $post):
			setup_postdata($post);
			
			// - custom variables -
			$custom = get_post_custom(get_the_ID());
			$sd = $custom["event_startdate"][0];
			$ed = $custom["event_enddate"][0];
			
			// single day event
			$longdate = date("M j, Y", $sd);
			
			// multiple day event
			if ( date("M j, Y", $sd) != date("M j, Y", $ed) ) {
				if ( date("M", $sd) != date("M", $ed) )
					$longdate = date("M j, Y", $sd) .' - ' . date("M j, Y", $ed);
				else
					$longdate = date("M j", $sd) .' - ' . date("j, Y", $ed);
			}
			
			?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a><br /><span class="date"><?php echo $longdate ?></span></li>		
			<?php
			
			// - fill daycheck with the current day -
			$daycheck = $longdate;
			
			endforeach;
		echo '</ul>';
		echo '</div>';
	endif;
	
	// ===== RETURN: FULL EVENTS SECTION =====
	
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
?>