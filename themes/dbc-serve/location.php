<?php
/**
 * Post Template
 *
 * This is the default post template.  It is used when a more specific template can't be found to display
 * singular views of the 'post' post type.
 *
 * @package DBC
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>

		<div class="hfeed">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // dbc_open_entry ?>
						
						<?php
						$connected = new WP_Query( array(
							'connected_type' => 'missionary_to_location',
							'connected_items' => get_queried_object_id(),
							'orderby' => 'meta_value',
							'meta_key' => 'field-director',
							'order' => 'DESC'
						) );
			
						if ( $connected->have_posts() ) : ?>
						
							<div id="team">
								
							<?php
							
							while ( $connected->have_posts() ) : $connected->the_post();
								setup_postdata( $post );
								$population = get_post_meta($post->ID, 'population', true);
								$religions = get_post_meta($post->ID, 'religions', true);
								?>
								
								<div class="missionary">
								
									<p><?php get_the_image( array( 'default_image' => get_stylesheet_directory_uri(). '/images/noavatar.png', 'image_class' => 'avatar', 'meta_key' => 'Thumbnail', 'size' => 'thumbnail' ) ); ?></p>
									
									<h3><?php the_title_attribute(); ?></h3>
									
								</div>
								<?php
						
							wp_reset_postdata();
							
							endwhile; ?>
							
							</div><!-- #team -->
							
						<?php endif; ?>
			
						<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

						<div class="entry-content">
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'dbc' ) ); ?>
							<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'dbc' ), 'after' => '</p>' ) ); ?>
						</div><!-- .entry-content -->

						<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">' . __( '[entry-terms taxonomy="category" before="Posted in "] [entry-terms taxonomy="post_tag" before="| Tagged "]', 'dbc' ) . '</div>' ); ?>

						<?php do_atomic( 'close_entry' ); // dbc_close_entry ?>

					</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>

					<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>

					<?php do_atomic( 'after_singular' ); // dbc_after_singular ?>
					
					<?php comments_template( '/comments.php', true ); // Loads the comments.php template. ?>

				<?php endwhile; ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>