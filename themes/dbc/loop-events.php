<?php
/**
 * Loop Events Template
 *
 * Displays an abbreviated list of upcoming events
 *
 * @package DBC
 * @subpackage Template
 * 
 */

 $user_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->users;" ) );
echo "<p>User count is {$user_count}</p>";
$fivesdrafts = $wpdb->get_results( 
	"
	SELECT ID, post_title 
	FROM $wpdb->posts
	WHERE post_status = 'publish' 
		AND post_author = 5
	"
);

foreach ( $fivesdrafts as $fivesdraft ) 
{
	echo $fivesdraft->post_title;
}
 
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
	    ORDER BY metastart.meta_value ASC LIMIT $limit
	 ";
	
	$events = $wpdb->get_results($querystr, OBJECT);
	print_r($fivesdrafts);
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
	$longdate = date("F j, Y g:iA", $sd);
	$longdate .= date(" - g:iA", $ed);
	
	// multiple day event
	if ( date("F j, Y", $sd) != date("F j, Y", $ed) ) {
		$longdate = date("F j, Y g:iA", $sd) .' - ' . date("F j @ g:iA", $ed);
	}
	
	?>
	<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

		<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>

		<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
		
		<?php echo '<div class="byline">' . $longdate .'</div>'; ?>

		<?php get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'small-thumb' ) ); ?>
		
		<div class="entry-summary">
			<?php the_excerpt(); ?>
			<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'dbc' ), 'after' => '</p>' ) ); ?>
		</div><!-- .entry-summary -->

		<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">' . __( '[entry-terms taxonomy="category" before="Posted in "] [entry-terms before="| Tagged "] [entry-comments-link before=" | "]', 'dbc' ) . '</div>' ); ?>

		<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>

	</div><!-- .hentry -->
	<?php
	
	// - fill daycheck with the current day -
	$daycheck = $longdate;
	
	endforeach;
	else :
	endif;
	
	?>
