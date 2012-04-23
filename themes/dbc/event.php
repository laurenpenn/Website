<?php
/**
 * Event Template
 *
 * This is the default event template.  It is used when a more specific template can't be found to display
 * singular views of the 'event' post type.
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

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>
					
					<?php
					
						// get custom fields
						$custom = get_post_custom(get_the_ID());
						$sd = $custom["event_startdate"][0];
						$ed = $custom["event_enddate"][0];
					
						if ( !empty( $sd ) || !empty( $ed ) ) {
							// single day event
							$longdate = date("F j, Y g:iA", $sd);
							
							// multiple day event
							if ( $sd != $ed ) {
								$longdate = date("F j, Y g:iA", $sd) .' - ' . date("F j @ g:iA", $ed);
							}
						}

					?>

					<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>
			
					<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
					
					<?php //echo '<h2>' . $longdate .'</h2>'; ?>
			
					<?php get_the_image( array( 'size' => 'full' ) ); ?>
					
					<article class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'dbc' ), 'after' => '</p>' ) ); ?>
					</article><!-- .entry-summary -->
								
					<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">' . __( '[entry-terms taxonomy="category" before="Posted in "] [entry-terms before="| Tagged "] [entry-comments-link before=" | "]', 'dbc' ) . '</div>' ); ?>
					
					<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>

					<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>

					<?php do_atomic( 'after_singular' ); // dbc_after_singular ?>
					
				<?php endwhile; ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // dbc_close_content ?>
		
		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<div id="sidebar-sticky" class="addthis_toolbox addthis_default_style addthis_32x32_style">
		<div class="buttons">
			<div class="fb-like" data-href="<?php echo urlencode(get_permalink($post->ID)); ?>" data-send="false" data-layout="box_count" data-width="42" data-show-faces="false"></div>
			<p>
				<a class="addthis_button_twitter"></a>
				<a class="addthis_button_email"></a>
				<a class="addthis_button_print"></a>
			</p>
		</div>
	</div><!-- #sidebar-sticky -->
	
	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>