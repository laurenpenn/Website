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

<div class="slider">

	<?php
	if ( hybrid_get_setting( 'feature_category' ) )
		$feature_query = new WP_Query( array( 'posts_per_page' => hybrid_get_setting( 'feature_num_posts' ), 'meta_key' => 'expiration-date', 'orderby' => 'meta_value_num', 'order' => 'ASC' ) );

	while ( $feature_query->have_posts() ) : $feature_query->the_post(); ?>

	<div class="myslide"><a href="<?php the_permalink(); ?>"><?php get_the_image( array( 'link_to_post' => false, 'default_size' => 'full', 'image_scan' => true ) ); ?></a></div>
	
	<?php endwhile;  ?>

</div>

<?php } ?>
