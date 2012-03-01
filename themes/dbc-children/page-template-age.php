<?php
/**
 * Template Name: Age Group Front Page
 *
 * Comprised of 3 main columns for content and a header.
 *
 * @package DBC Children
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>
		
		<div class="age-top">
		
			<div class="overview">
				
			<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
	
			<?php if ( have_posts() ) : ?>
	
				<?php while ( have_posts() ) : the_post(); ?>
	
					<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>
	
					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
	
						<?php do_atomic( 'open_entry' ); // dbc_open_entry ?>							
	
						<div class="entry-content">
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', hybrid_get_textdomain() ) ); ?>
							<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', hybrid_get_textdomain() ), 'after' => '</p>' ) ); ?>
						</div><!-- .entry-content -->
	
						<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">[entry-edit-link]</div>' ); ?>
	
						<?php do_atomic( 'close_entry' ); // dbc_close_entry ?>
	
					</div><!-- .hentry -->
	
					<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>
	
					<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>
	
					<?php do_atomic( 'after_singular' ); // dbc_after_singular ?>
	
				<?php endwhile; ?>
	
			<?php endif; ?>
			
			</div>
			
			<?php echo do_shortcode('[events-sidebar]'); ?>
		
		</div>

		<div class="columns columns-3">
			
			<div class="column"><div class="entry-content"><?php the_secondary_content( 'Column 2' ); ?></div></div>
			<div class="column"><div class="entry-content"><?php the_secondary_content( 'Column 3' ); ?></div></div>
			<div class="column column-last"><div class="entry-content"><?php the_secondary_content( 'Column 4' ); ?></div></div>
			
		</div><!-- .columns-3 -->

		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>