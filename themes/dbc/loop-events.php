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
	$args = array (
		'posts_per_page' => 5,
		'post_type' => 'event'
	);
	
	query_posts( $args );	

	if ( have_posts() ) : ?>
	
		<ul>

		<?php while ( have_posts() ) : the_post(); ?>
			
			<?php
			
				// get custom fields
				$custom = get_post_custom(get_the_ID());
				$sd = $custom["event_startdate"][0];
				$ed = $custom["event_enddate"][0];
			
				// single day event
				$longdate = date("M j, Y", $sd);
				
				// multiple day event
				if ( $sd != $ed ) {
					$longdate = date("M j, Y", $sd) .' - ' . date("M j, Y", $ed);
				}
				
				// local time format
				$time_format = get_option('time_format');
				$stime = date($time_format, $sd);
				$etime = date($time_format, $ed);
			?>
						
			<li><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a><br /><?php echo $longdate ?></li>					
			
		<?php endwhile; ?>
		
		</ul>
	
	<?php endif; wp_reset_query(); ?>