<?php
/**
 * Slider Home Template
 *
 * Displays a slider meant for the home page template.
 * Only displays if the settings are turned on.
 *
 * @package DBC Christian Service Brigade
 * @subpackage Template
 */

if ( hybrid_get_setting( 'slider' ) == 'true' ) { ?>

<div id="slider-container"<?php if ( hybrid_get_setting( 'slider_16x9' ) == 'true' ) { echo ' class="widescreen"'; } ?>>

	<div id="slider">

	<?php
		if ( hybrid_get_setting( 'feature_category' ) )
			$feature_query = new WP_Query( array( 'posts_per_page' => hybrid_get_setting( 'feature_num_posts' ), 'order' => 'ASC' ) );

		while ( $feature_query->have_posts() ) : $feature_query->the_post(); ?>

			<div class="<?php hybrid_entry_class( 'feature' ); ?>">

				<?php get_the_image( array( 'custom_key' => array( 'Medium', 'Feature Image' ), 'default_size' => 'full', 'image_scan' => true ) ); ?>

			</div>

		<?php endwhile;  ?>

	</div>
	
</div>

<?php } ?>
