<?php
/**
 * Archive Template
 *
 * The archive template is the default template used for archives pages without a more specific template. 
 *
 * @package Prototype
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // prototype_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // prototype_open_content ?>
		
		<div class="hfeed">

			<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>

			<ul id="sort-bar">

				<li class="sort last">
					<dl class="clearfix">
						<dt>
							Sort by:
						</dt>
						<dd>
							<div id="sort-missionaries" class="drop-down-menu">
								<div>Name A-Z</div>
								<ul>
									<li>
										<a id="sort-by-name" class="" href="?orderby=title&order=ASC">Name A-Z</a>
									</li>
									<li>
										<a id="sort-by-name" class="" href="?orderby=title&order=DSC">Name Z-A</a>
									</li>
									<li>
										<a id="sort-by-price" class="" href="?meta_key=location&orderby=meta_value&order=ASC">Region A-Z</a>
									</li>
									<li>
										<a id="sort-by-price" class="" href="?meta_key=location&orderby=meta_value&order=DESC">Region Z-A</a>
									</li>		
								</ul>
			          		</div>
						</dd>
					</dl>
				</li>

				<li class="sort last">
					<dl class="clearfix">
						<dt>
							Region:
						</dt>
						<dd>
							<div id="select-country" class="drop-down-menu">
								<div>All</div>
								<ul>
									<li>
										<a id="sort-by-name" class="" href="?meta_key=location&meta_value=Argentina">Argentina</a>
									</li>
									<li>
										<a id="sort-by-name" class="" href="?meta_key=location&meta_value=Kenya">Kenya</a>
									</li>		
								</ul>
			          		</div>
						</dd>
					</dl>
				</li>
			    
				<li class="sort last">
					<dl class="clearfix">
						<dt>
							Show affiliates:
						</dt>
						<dd>
							<input type="checkbox" />
						</dd>
					</dl>
				</li>
			</ul>
						
			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_atomic( 'before_entry' ); // prototype_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // prototype_open_entry ?>

						<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
						
						<p><?php echo get_post_meta($post->ID, 'location', true); ?></p>

						<p><?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'default_image' => 'http://serve-intl.com/wp-content/themes/dbc-serve/images/noavatar.png', 'image_class' => 'avatar', 'meta_key' => 'Thumbnail', 'size' => 'thumbnail' ) ); ?><br />
						<small><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" ><?php the_title(); ?></a> | <a href="<?php echo get_post_meta($post->ID, 'blog-address', true); ?>">View blog</a></small></p>

						<?php do_atomic( 'close_entry' ); // prototype_close_entry ?>

					</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // prototype_after_entry ?>

				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // prototype_close_content ?>

		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // prototype_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>