<?php
/**
 * Template Name: International Locations
 *
 * This page will list all the international locations
 *
 * @package DBC
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>

		<div class="hfeed">
			
			<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
			
			<?php $args = array(
				'post_type' => 'location',
				'posts_per_page' => -1,
				'order' => 'ASC',
				'orderby' => 'title'
			);
			?>
			
			<?php $intl_locations = new WP_Query( $args ); ?>
			
			<?php if ( $intl_locations->have_posts() ) : ?>

				<?php while ( $intl_locations->have_posts() ) : $intl_locations->the_post(); ?>

					<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // dbc_open_entry ?>

						<div class="entry-content">
							<h2><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a></h2>
							<p><?php get_the_image( array( 'default_size' => 'medium' )); ?></p>
							
						</div><!-- .entry-content -->

						<?php do_atomic( 'close_entry' ); // dbc_close_entry ?>

					</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>

					<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>

					<?php do_atomic( 'after_singular' ); // dbc_after_singular ?>

				<?php endwhile; ?>

			<?php endif; wp_reset_postdata(); ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>
