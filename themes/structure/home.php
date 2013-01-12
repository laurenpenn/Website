<?php
/**
 * Home Template
 *
 * This template is loaded when on the home/blog page.
 *
 * @package Structure
 * @subpackage Template
 */

get_header(); ?>

	<div id="content" class="hfeed content">

		<?php hybrid_before_content(); // Before content hook ?>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<div class="<?php hybrid_entry_class(); ?>">

				<?php get_the_image( array( 'custom_key' => array( 'Thumbnail' ), 'size' => 'thumbnail' ) ); ?>

				<?php hybrid_before_entry(); ?>

				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div>

				<?php hybrid_after_entry(); ?>

			</div>

			<?php endwhile; endif; ?>

		<?php hybrid_after_content(); // After content hook ?>

	</div><!-- .content .hfeed -->

<?php get_footer(); ?>
