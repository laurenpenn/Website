<?php
/**
 * Template Name: Missionaries
 *
 * This template will list all of the missionaries.
 *
 * @package DBC
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>

		<ul id="sort-bar">
		    <li class="sort last">
		      <dl class="clearfix">
		        <dt>
		          Sort by:
		        </dt>
		        <dd>
		          <div class="drop-down-menu">
		            <div>Country</div>
		            <ul>
		                <li>
		                  <a id="sort-by-price" class="" href="?meta_key=price&orderby=meta_value_num&order=ASC">Price: Low to High</a>
		                </li>
		                <li>
		                  <a id="sort-by-price" class="" href="?meta_key=price&orderby=meta_value_num&order=DESC">Price: High to Low</a>
		                </li>
		                <li>
		                  <a id="sort-by-name" class="" href="?orderby=title&order=ASC">Name</a>
		                </li>
		            </ul>
		          </div>
		        </dd>
		      </dl>
		    </li>
		</ul>

		<div class="hfeed">

			<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?>
			<?php $missionaries = new WP_Query( array( 'orderby' => 'title', 'order' => 'DSC', 'paged' => $paged, 'posts_per_page'=> 18, 'post_type'=> 'missionary' )); ?>
		
			<?php if ( $missionaries->have_posts() ) : ?>

				<?php while ( $missionaries->have_posts() ) : $missionaries->the_post(); ?>

					<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // dbc_open_entry ?>

						<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

						<p><?php get_the_image( array( 'default_image' => 'http://serve-intl.com/wp-content/themes/dbc-serve/images/noavatar.png', 'image_class' => 'avatar')); ?><br />
						<small><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" ><?php the_title(); ?></a> | <a href="<?php echo get_post_meta($post->ID, 'blog-address', true); ?>">View blog</a></small></p>

						<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">[entry-edit-link]</div>' ); ?>

						<?php do_atomic( 'close_entry' ); // dbc_close_entry ?>

					</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>

					<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>

					<?php do_atomic( 'after_singular' ); // dbc_after_singular ?>

				<?php endwhile; ?>

			<?php endif; wp_reset_query(); ?>
			
		</div><!-- .hfeed -->
		
		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>