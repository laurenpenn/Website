<?php
/**
 * Archive Template
 *
 * The archive template is the default template used for archives pages without a more specific template. 
 *
 * @package Prototype
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // prototype_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // prototype_open_content ?>
		
		<div class="hfeed">

			<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>

			<?php query_posts( array(
				'post_type' => 'location',
				'posts_per_page' => -1,
				'order' => 'ASC',
				'orderby' => 'name'
			));
			?>
									
			<?php if ( have_posts() ) : ?>

				<div class="grid">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_atomic( 'before_entry' ); // prototype_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // prototype_open_entry ?>

						<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
						
						<p><?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'default_image' => get_template_directory_uri(). '/images/noavatar.png', 'meta_key' => 'Thumbnail', 'size' => 'medium' ) ); ?><br />

						<?php do_atomic( 'close_entry' ); // prototype_close_entry ?>

					</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // prototype_after_entry ?>

				<?php endwhile; ?>
				
				</div><!-- .grid -->
				
				<script type="text/javascript">
					jQuery(document).ready(function($) {
					
						var $container = $('.grid');
						$container.imagesLoaded(function() {
							$container.masonry({
								itemSelector : '.hentry',
								columnWidth : 275
							});
						});
					
					});
				</script>

			<?php else : ?>

				<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // prototype_close_content ?>

		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // prototype_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>
