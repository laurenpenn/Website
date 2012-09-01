<?php
/**
 * Loop Nav Template
 *
 * This template is used to show your your next/previous post links on singular pages and
 * the next/previous posts links on the home/posts page and archive pages.
 *
 * @package Prototype
 * @subpackage Template
 */
?>

	<?php if ( is_attachment() ) : ?>

		<div class="loop-nav">
			<?php previous_post_link( '%link', '<span class="previous">' . __( '&larr; Return to entry', 'dbc' ) . '</span>' ); ?>
		</div><!-- .loop-nav -->

	<?php elseif ( is_singular( 'post' ) || is_singular( 'story' ) ) : ?>

		<div class="loop-nav">
			<?php previous_post_link( '<div class="previous">' . __( 'Previous Entry: %link', 'dbc' ) . '</div>', '%title' ); ?>
			<?php next_post_link( '<div class="next">' . __( 'Next Entry: %link', 'dbc' ) . '</div>', '%title' ); ?>
		</div><!-- .loop-nav -->

	<?php elseif ( !is_singular() && current_theme_supports( 'loop-pagination' ) ) : loop_pagination(); ?>

	<?php elseif ( !is_singular() && $nav = get_posts_nav_link( array( 'sep' => '', 'prelabel' => '<span class="previous">' . __( '&larr; Previous', 'dbc' ) . '</span>', 'nxtlabel' => '<span class="next">' . __( 'Next &rarr;', 'dbc' ) . '</span>' ) ) ) : ?>

		<div class="loop-nav">
			<?php echo $nav; ?>
		</div><!-- .loop-nav -->

	<?php endif; ?>