<?php
/**
 * Loop Stories Template
 *
 * Displays an abbreviated list of recent stories
 *
 * @package DBC
 * @subpackage Template
 * 
 */
	$args = array (
		'posts_per_page' => 5,
		'post_type' => 'story'
	);
	
	query_posts( $args );	

	if ( have_posts() ) : ?>
	
		<ul>

		<?php while ( have_posts() ) : the_post(); ?>
						
			<li><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a></li>					
			
		<?php endwhile; ?>
		
		</ul>
	
	<?php endif; wp_reset_query(); ?>