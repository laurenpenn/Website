<?php
/**
 * Slider Home Template
 *
 * Displays a slider meant for the home page template.
 * Only displays if the settings are turned on.
 *
 * @package Prototype
 * @subpackage Template
 */

if ( hybrid_get_setting( 'slider' ) == 'true' ) { ?>

<div class="flex-container">

	<div class="flexslider">
		
	    <ul class="slides">

		<?php
			if ( hybrid_get_setting( 'feature_category' ) )
				$feature_query = new WP_Query( array( 'posts_per_page' => hybrid_get_setting( 'feature_num_posts' ), 'meta_key' => 'expiration-date', 'orderby' => 'meta_value_num', 'order' => 'ASC' ) );
	
			while ( $feature_query->have_posts() ) : $feature_query->the_post(); ?>
	
			<li><a href="<?php the_permalink(); ?>" class="orbit-slide"><?php get_the_image( array( 'link_to_post' => false, 'default_size' => 'full', 'image_scan' => true ) ); ?></a></li>
			
			<?php endwhile;  ?>
			
		</ul>

	</div>
	
</div>

<?php } ?>