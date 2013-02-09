<?php
/**
 * Note Template (Tom's Notes)
 *
 * The archive template is the default template used for 'note' posts.
 *
 * @package DBC
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // prototype_before_content ?>

	<div id="content" role="main">

		<?php do_atomic( 'open_content' ); // prototype_open_content ?>

		<div class="hfeed">

			<?php //get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>
			
			<h1 class="loop-title">Notes from the Desk of Tom Nelson</h1>

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_atomic( 'before_entry' ); // prototype_before_entry ?>

					<article id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // prototype_open_entry ?>

						<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'thumbnail' ) ); ?>

						<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

						<div class="left-col">
						
							<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __( '[entry-published]', 'dbc' ) . '</div>' ); ?>
	
							<?php 
							$pdf = dbc_get_post_pdf();
							
							if ( !empty( $pdf ) )
								echo '<div class="tom-pdf"><a href="'. $pdf .'" class="button nice blue medium radius right">View PDF</a></div>';
							?>

							<div class="facebook-like">
								<fb:like href="<?php echo urlencode(get_permalink($post->ID)); ?>" layout="button_count" show_faces="true" width="150"></fb:like>
							</div>
							
						</div>
						
						<div class="entry-summary">
							<?php the_content(); ?>
							<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'dbc' ), 'after' => '</p>' ) ); ?>
						</div><!-- .entry-summary -->

						<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">' . __( '[entry-terms taxonomy="category" before="Posted in "] [entry-terms before="| Tagged "] [entry-comments-link before=" | "]', 'dbc' ) . '</div>' ); ?>

						<?php do_atomic( 'close_entry' ); // prototype_close_entry ?>

					</article><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // prototype_after_entry ?>

				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // prototype_close_content ?>

		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // prototype_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>