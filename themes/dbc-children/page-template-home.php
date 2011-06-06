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
	
	<div id="slider-container-container">
	<?php get_template_part( 'slider-home' ); // loads slider-home.php ?>
	</div>
	
	<?php get_sidebar( 'home' ); ?>
	
	<div class="clear"></div>

	<div id="welcome-bar">
		<dl id="welcome-preschool">
			<a href="http://children.dentonbible.org/preschool/">
				<dt><strong>Preschool</strong></dt>
				<dd>4 months - 4 years</dd>
			</a>
		</dl>
		<dl id="welcome-elementary">
			<a href="http://children.dentonbible.org/elementary/">
				<dt><strong>Elementary</strong></dt>
				<dd>Kindergarten - 4th grade</dd>
			</a>
		</dl>
		<dl id="welcome-volunteer">
			<a href="http://children.dentonbible.org/volunteer/">
				<dt><strong>Volunteer</strong></dt>
				<dd>Learn how to get involved</dd>
			</a>
		</dl>
		<dl id="welcome-connect">
			<a href="http://children.dentonbible.org/calendar/">
				<dt><strong>Calendar</strong></dt>
				<dd>Sign up for events</dd>
			</a>
		</dl>
	</div>
	
	<div class="clear"></div>
	
	<div id="content">

		<?php do_atomic( 'open_content' ); // prototype_open_content ?>

		<?php if ( hybrid_get_setting( 'latest-message' ) == 'true' ) get_template_part( 'latest-message' ); // loads latest-message.php ?>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

				<div class="entry-content">
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<p class="page-links pages">' . __( 'Pages:', 'hybrid' ), 'after' => '</p>' ) ); ?>
				</div><!-- .entry-content -->

			</div><!-- .hentry -->

			<?php do_atomic( 'after_singular' ); // prototype_after_singular ?>

			<?php endwhile; ?>

		<?php else: ?>

			<p class="no-data">
				<?php _e( 'Apologies, but no results were found.', 'hybrid' ); ?>
			</p><!-- .no-data -->

		<?php endif; ?>
			
		<?php do_atomic( 'close_content' ); // prototype_close_content ?>

	</div><!-- #content -->
	
	<div class="clear"></div>
	
	<?php do_atomic( 'after_content' ); // prototype_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>