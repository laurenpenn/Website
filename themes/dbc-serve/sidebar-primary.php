<?php
/**
 * Primary Sidebar Template
 *
 * Displays widgets for the Primary dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package DBC
 * @subpackage Template
 */

$missionary_blog_url = get_post_meta($post->ID, 'blog-address', true);
 
$location = get_post_meta($post->ID, 'location', true);

if ( is_active_sidebar( 'primary' ) ) : ?>
	
	<?php do_atomic( 'before_sidebar_primary' ); // dbc_before_sidebar_primary ?>
 
	<div id="sidebar-primary" class="sidebar">

		<?php do_atomic( 'open_sidebar_primary' ); // dbc_open_sidebar_primary ?>

		<?php if ( is_singular('missionary') ): ?>
			
			<p><?php get_the_image( array( 'default_size' => 'medium', 'link_to_post' => false )); ?></p>
			
			<?php // Get RSS Feed(s)
			include_once(ABSPATH . WPINC . '/feed.php');
			
			// Get a SimplePie feed object from the specified feed source.
			$rss = fetch_feed( $missionary_blog_url .'/feed/' );
			if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly 
			    // Figure out how many total items there are, but limit it to 5. 
			    $maxitems = $rss->get_item_quantity(5); 
			
			    // Build an array of all the items, starting with element 0 (first element).
			    $rss_items = $rss->get_items(0, $maxitems); 
			endif;
			?>
			
			<ul id="blog-preview">
			    <?php if ($maxitems == 0) echo '';
			    else
			    // Loop through each feed item and display each item as a hyperlink.
			    foreach ( $rss_items as $item ) : ?>
			    <li>
			        <a href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>"><?php echo esc_html( $item->get_title() ); ?></a>
			    </li>
			    <?php endforeach; ?>
			</ul>

			<?php if ( !empty( $missionary_blog_url ) ) { ?><p><a href="<?php echo $missionary_blog_url; ?>" class="button" rel="external">View blog</a></p><?php } ?>
			
			<?php

			p2p_each_connected( $wp_query, array(
				'post_type' => 'location',
			) );
			
			?>
			
			<div id="locations">
			
			<?php			
			while ( have_posts() ) : the_post();
			
				foreach ( $post->connected as $post ) {
					setup_postdata( $post );
					$population = get_post_meta($post->ID, 'population', true);
					$religions = get_post_meta($post->ID, 'religions', true);
					?>
					
					<div class="location">
			
						<h2><?php the_title_attribute(); ?></h2>
						
						<p><?php get_the_image( array( 'default_size' => 'medium' )); ?></p>
						
						<p><a href="<?php the_permalink(); ?>" class="button">View the <?php the_title_attribute(); ?> team</a></p>
					
					</div>
							
					<?php
				}
			
				wp_reset_postdata();
				
			endwhile; ?>
			
			</div><!-- #locations -->
		
			
		<?php elseif ( is_singular('location') ): ?>
			
			<?php
			$population = get_post_meta($post->ID, 'population', true);
			$religions = get_post_meta($post->ID, 'religions', true);
			?>
			
			<p><?php get_the_image( array( 'default_size' => 'medium', 'link_to_post' => false )); ?></p>
			
			<p><?php echo do_shortcode('[mappress height="185" width="185"]'); ?></p>
						
			<dl>
				<?php if ( !empty( $population ) ) { ?><dt>Population</dt><dd><?php echo $population; ?></dd><?php } ?>
				<?php if ( !empty( $religions ) ) { ?><dt>Religions</dt><dd><?php echo $religions; ?></dd><?php } ?>						
			</dl>
			
			<div id="team">
			<h2><?php the_title_attribute(); ?> Team</h2>
			
			<?php

			p2p_each_connected( $wp_query, array(
				'post_type' => 'missionary',
			) );
			
			while ( have_posts() ) : the_post();
			
				foreach ( $post->connected as $post ) {
					setup_postdata( $post );
					$population = get_post_meta($post->ID, 'population', true);
					$religions = get_post_meta($post->ID, 'religions', true);
					?>
					
					<div class="missionary">
					
						<h3><?php the_title_attribute(); ?></h3>
					
						<p><?php get_the_image( array( 'default_image' => 'http://serve-intl.com/wp-content/themes/dbc-serve/images/noavatar.png', 'image_class' => 'avatar', 'meta_key' => 'Thumbnail', 'size' => 'thumbnail' ) ); ?></p>
					
					</div>
					<?php
				}
			
				wp_reset_postdata();
				
			endwhile; ?>
			
			</div><!-- #team -->
			
		<?php else: ?>
				
			<?php dynamic_sidebar( 'primary' ); ?>
		
		<?php endif; ?>

		<?php do_atomic( 'after_sidebar_primary' ); // dbc_after_sidebar_primary ?>

	</div><!-- #sidebar-primary -->

<?php endif; ?>