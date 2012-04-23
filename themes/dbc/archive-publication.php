<?php
/**
 * Publication Archive
 *
 * This is the archive template for Publications.
 *
 * @package DBC
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>
	
	<?php breadcrumb_trail(); ?>

	<div id="content" role="main">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>

		<div class="hfeed">

			<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>
			
			<?php if ( have_posts() ) : ?>
				
				<ul>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php
					
					$args = array(
						'post_type' => 'attachment',
						'numberposts' => 51,
						'post_status' => null,
						'post_parent' => $post->ID
						); 
					$attachments = get_posts($args);
					if ($attachments) {
						foreach ($attachments as $attachment) {
							if ( $attachment->post_mime_type == 'application/pdf') {
								$link = $attachment->guid;
								$type = 'pdf';	
							}
						}
					} else {
						$link = get_permalink();
						$type = 'page';
					}
					
					?>
						
					<?php do_atomic( 'before_entry' ); // prototype_before_entry ?>

					<li id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // prototype_open_entry ?>

						<a href="<?php echo $link; ?>" class="date"><?php dbc_publication_title(); ?></a>
							
						<?php do_atomic( 'close_entry' ); // prototype_close_entry ?>

					</li><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // prototype_after_entry ?>

				<?php endwhile; ?>
				
				</ul>

			<?php else : ?>

				<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // dbc_close_content ?>
		
		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>