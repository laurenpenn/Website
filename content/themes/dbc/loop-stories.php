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
		'post_type' => 'story',
		'post_status' => 'publish'
	);
	
	query_posts( $args );	

	if ( have_posts() ) : ?>
	
		<div class="loop loop-stories">
	
			<h3>New Stories <span class="all"><a href="<?php bloginfo( 'siteurl' ); ?>/stories/">view all</a></span></h3>
		
			<ul>
	
			<?php while ( have_posts() ) : the_post(); ?>
							
				<li><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a></li>					
				
			<?php endwhile; ?>
			
			</ul>
		
		</div>
	
	<?php endif; wp_reset_query(); ?>