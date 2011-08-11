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

		<?php do_atomic( 'open_content' ); // prototype_open_content ?>
		
		<div class="columns columns-2">

		<div class="column">
			
			<ul class="links-list">		
				<?php wp_list_bookmarks( array( 'category' => 2, 'show_description' => true )) ?>
			</ul><!-- links-list -->
		</div><!-- .col-1 -->

		<div class="column column-last">
			
			<h2>How To...</h2>
			
			<ul class="links-list">				
				
			<?php $args = array( 'post_type' => 'documentation' ); ?>
				
			<?php $docs = new WP_Query( $args ); ?>		
				
			<?php if ( $docs->have_posts() ) : ?>

				<?php while ( $docs->have_posts() ) : $docs->the_post(); ?>

					<?php do_atomic( 'before_entry' ); // prototype_before_entry ?>

					<li id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // prototype_open_entry ?>

						<a href="<?php the_permalink() ?>" title="<?php the_title_attribute() ?>" rel="bookmark"><?php the_title_attribute() ?></a>


						<div class="entry-summary">
							<?php the_excerpt(); ?>
							<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', hybrid_get_textdomain() ), 'after' => '</p>' ) ); ?>
						</div><!-- .entry-summary -->

						<?php do_atomic( 'close_entry' ); // prototype_close_entry ?>

					</li><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // prototype_after_entry ?>

				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

			<?php endif; ?>
			
			</ul><!-- links-list -->
			
		</div><!-- .col-2 -->
		
		</div><!-- .columns -->
		
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

	<?php do_atomic( 'after_content' ); // prototype_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>