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
<!--
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
		</ul>-->

		<div class="hfeed">

			<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?>
			<?php //$missionaries = new WP_Query( array( 'orderby' => 'title', 'order' => 'DSC', 'paged' => $paged, 'posts_per_page'=> 18, 'post_type'=> 'missionary' )); ?>
			<?php global $query_string; query_posts( $querystring. '&posts_per_page=18&post_type=missionary&paged='. $paged ); ?>
		
			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // dbc_open_entry ?>

						<h2><a href="<?php the_permalink(); ?>"><?php the_title_attribute(); ?></a></h2>

						<p><?php get_the_image( array( 'default_image' => 'http://serve-intl.com/wp-content/themes/dbc-serve/images/noavatar.png', 'image_class' => 'avatar')); ?></p>

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