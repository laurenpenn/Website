<?php
/**
 * Template Name: Home
 *
 * This template is for the Home page
 * tes
 *
 * @package Hybrid
 * @subpackage Template
 */
global $blog_ID;

get_header(); ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>
		
		<!--
		<div class="columns columns-3">
			
			<div class="column col-1">
				
				<a href="<?php bloginfo( 'siteurl' ); ?>/denton/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/serve-denton.png" height="200" width="297" alt="Serve Denton" /></a>
				
			</div>

			<div class="column col-2">
				
				<a href="<?php bloginfo( 'siteurl' ); ?>/international/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/serve-international.png" height="200" width="297" alt="Serve International" /></a>
				
			</div>

			<div class="column column-last col-3">
				
				
				
			</div>
									
		</div>
		
		-->
	

			<div id="paths">
				<a class="big-button" href="<?php bloginfo( 'siteurl' ); ?>/denton/"><span class="league-gothic">serve Denton</span><br />strengthening our community</a><br />
				<a class="big-button" href="<?php bloginfo( 'siteurl' ); ?>/international/"><span class="league-gothic">serve International</span><br />mobilizing missionaries</a>
			</div><!-- #paths -->
			
			<?php get_template_part( 'slider-home' ); // loads slider-home.php ?>
			
			<div class="clear"></div>
						
		<!--
		<div id="diamond-recent-posts" class="alignright">
			<h2>Updates from the field</h2>
			<?php echo do_shortcode('[diamond-post format="{avatar} <strong>{blog}</strong><br />{title}<br /><small> {date}</small>" date_format="M d, Y" exclude="1" avatar_size="50" count="3" /]'); ?>
		</div>
		-->
		 
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

				<div class="entry-content">
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<p class="page-links pages">' . __( 'Pages:', 'hybrid' ), 'after' => '</p>' ) ); ?>
				</div><!-- .entry-content -->

			</div><!-- .hentry -->

			<?php do_atomic( 'after_singular' ); // dbc_after_singular ?>

			<?php endwhile; ?>

		<?php else: ?>

			<p class="no-data">
				<?php _e( 'Apologies, but no results were found.', 'hybrid' ); ?>
			</p><!-- .no-data -->

		<?php endif; ?>
			
		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->
		
	<?php get_sidebar( 'home' ); ?>

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>