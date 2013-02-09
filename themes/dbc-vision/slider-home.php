<?php
/**
 * Slider Home Template
 *
 * Displays a slider meant for the home page template.
 * Only displays if the settings are turned on.
 *
 * @package DBC Child
 * @subpackage Template
 */
?>

<script type="text/javascript">

jQuery(document).ready(function($) {
	$("#showcase").awShowcase({
		content_width:			755,
		content_height:			270,
		fit_to_parent:			true,
		auto:					true,
		interval:				3000,
		continuous:				true,
		loading:				true,
		tooltip_width:			200,
		tooltip_icon_width:		32,
		tooltip_icon_height:	32,
		tooltip_offsetx:		18,
		tooltip_offsety:		0,
		arrows:					true,
		buttons:				true,
		btn_numbers:			false,
		keybord_keys:			true,
		mousetrace:				false, /* Trace x and y coordinates for the mouse */
		pauseonover:			false,
		stoponclick:			true,
		transition:				'hslide', /* hslide/vslide/fade */
		transition_delay:		300,
		transition_speed:		500,
		show_caption:			'onhover', /* onload/onhover/show */
		thumbnails:				true,
		thumbnails_position:	'outside-last', /* outside-last/outside-first/inside-last/inside-first */
		thumbnails_direction:	'horizontal', /* vertical/horizontal */
		thumbnails_slidex:		0, /* 0 = auto / 1 = slide one thumbnail / 2 = slide two thumbnails / etc. */
		dynamic_height:			false, /* For dynamic height to work in webkit you need to set the width and height of images in the source. Usually works to only set the dimension of the first slide in the showcase. */
		speed_change:			false, /* Set to true to prevent users from swithing more then one slide at once. */
		viewline:				false /* If set to true content_width, thumbnails, transition and dynamic_height will be disabled. As for dynamic height you need to set the width and height of images in the source. */
	});
});

</script>

<div id="showcase" class="showcase<?php if ( hybrid_get_setting( 'slider_16x9' ) == 'true' ) { echo ' widescreen'; } ?>">
	
	<?php
	if ( hybrid_get_setting( 'feature_category' ) )
		$feature_query = new WP_Query( array( 'posts_per_page' => hybrid_get_setting( 'feature_num_posts' ), 'order' => 'ASC' ) );
	while ( $feature_query->have_posts() ) : $feature_query->the_post(); ?>
	
	<!-- Each child div in #showcase with the class .showcase-slide represents a slide. -->
	<div class="showcase-slide">
		<!-- Put the slide content in a div with the class .showcase-content. -->
		<div class="showcase-content">
			<?php the_post_thumbnail( 'slider' ); ?>
		</div>
		<!-- Put the thumbnail content in a div with the class .showcase-thumbnail -->
		<div class="showcase-thumbnail">
			<?php the_post_thumbnail( 'thumbnail' ); ?>
			<!-- The div below with the class .showcase-thumbnail-cover is used for the thumbnails active state. -->
			<div class="showcase-thumbnail-cover"></div>
		</div>
	</div>

	<?php endwhile;  ?>
	
</div><!-- #showcase -->