<?php
/**
 * Template Name: International
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

						<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

						<div class="entry-content">
							
							<p><a href="http://dbcm.org/international/missionaries/" class="button">View all missionaries</a> <a href="http://dbcm.org/international/locations/" class="button">View all locations</a></p>
							
							<!-- ammap script-->
							<script type="text/javascript" src="http://dbcm.org/wp-content/plugins/ammap/swfobject.js"></script>
							<div id="flashcontent">
								<strong>You need to upgrade your Flash Player</strong>
							</div>
							<script type="text/javascript">
								// <![CDATA[
								var so = new SWFObject("http://dbcm.org/wp-content/plugins/ammap/ammap.swf", "ammap", "940", "600", "8", "#ffffff");
								//so.addVariable("path", "ammap/");
								so.addVariable("settings_file", escape("<?php bloginfo( 'stylesheet_directory' ); ?>/ammap_settings.xml"));
								so.addVariable("data_file", escape("<?php bloginfo( 'stylesheet_directory' ); ?>/ammap_data.xml"));
								so.write("flashcontent");
								// ]]>
							</script>
							<!-- end of ammap script -->

							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'dbc' ) ); ?>

							<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'dbc' ), 'after' => '</p>' ) ); ?>
						</div><!-- .entry-content -->

						<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">[entry-edit-link]</div>' ); ?>

						<?php do_atomic( 'close_entry' ); // dbc_close_entry ?>

					</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>

					<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>

					<?php do_atomic( 'after_singular' ); // dbc_after_singular ?>

				<?php endwhile; ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>