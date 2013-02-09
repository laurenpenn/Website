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
			
			if ( !empty( $maxitems ) ) { ?>

			<h3>Recent blog posts</h3>
			<ul id="blog-preview">
			    <?php 
			    // Loop through each feed item and display each item as a hyperlink.
			    foreach ( $rss_items as $item ) : ?>
			    <li>
			        <a href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>"><?php echo esc_html( $item->get_title() ); ?></a>
			    </li>
			    <?php endforeach; ?>
			</ul>
			
			<?php } ?>

			<p>
			<?php
			$email = get_post_meta( $post->ID, 'missionary-email', true );
			if( $email ) { ?><a href="#" class="button nice radius green small" data-reveal-id="modal-email-missionary">Email</a><?php } ?>
			<?php if ( !empty( $missionary_blog_url ) ) { ?><a href="<?php echo $missionary_blog_url; ?>" class="button nice radius green small" rel="external">View blog</a><?php } ?>
			</p>
			
			<?php

			$connected = new WP_Query( array(
				'connected_type' => 'missionary_to_location',
				'connected_items' => get_queried_object_id(),
			) );
			
			?>
			
			<div id="locations">
			
			<?php			
				while ( $connected->have_posts() ) : $connected->the_post();
				setup_postdata( $post );
				$population = get_post_meta($post->ID, 'population', true);
				$religions = get_post_meta($post->ID, 'religions', true);
				?>
				
				<div class="location">
		
					<h2><?php the_title_attribute(); ?></h2>
					
					<p><?php get_the_image( array( 'default_size' => 'medium' )); ?></p>
					
					<p><a href="<?php the_permalink(); ?>" class="button nice radius small green"><?php the_title_attribute(); ?> team</a></p>
				
				</div>
						
				<?php
				
			
				wp_reset_postdata();
				
			endwhile; ?>
			
			</div><!-- #locations -->
		
			
		<?php elseif ( is_singular('location') ): ?>
			
			<?php
			$population = get_post_meta($post->ID, 'population', true);
			$religions = get_post_meta($post->ID, 'religions', true);
			$map_data = get_post_meta($post->ID, 'map-data', true);
			?>
			
			<p class="text-center"><a href="<?php echo home_url(); ?>/locations/">All locations</a> | <a href="<?php echo home_url(); ?>/missionaries/">All missionaries</a></p>
			
			<p><?php get_the_image( array( 'default_size' => 'medium', 'link_to_post' => false )); ?></p>

			<?php
			$connected = new WP_Query( array(
				'connected_type' => 'missionary_to_location',
				'connected_items' => get_queried_object_id(),
				'tax_query' => array(
					'relation' => 'OR',
					array(
						'taxonomy' => 'type',
						'field' => 'slug',
						'terms' => 'affiliate'
					),
					array(
						'taxonomy' => 'type',
						'field' => 'slug',
						'terms' => 'endorsed'						
					)
				)
			) );

			if ( $connected->have_posts() ) : ?>
			
				<div class="team">
					
					<h3 >Affiliate &amp; Endorsed Missionaries</h3>
					
				<?php
				
				while ( $connected->have_posts() ) : $connected->the_post();
					setup_postdata( $post );
					$population = get_post_meta($post->ID, 'population', true);
					$religions = get_post_meta($post->ID, 'religions', true);
					?>
					
					<div class="missionary">
					
						<p><?php get_the_image( array( 'default_image' => get_stylesheet_directory_uri(). '/images/noavatar.png', 'image_class' => 'avatar', 'meta_key' => 'Thumbnail', 'size' => 'thumbnail' ) ); ?></p>
						
						<p><?php the_title_attribute(); ?></p>
						
					</div>
					<?php
			
				wp_reset_postdata();
				
				endwhile; ?>
				
				</div><!-- .team -->
				
			<?php endif; ?>
									
		<?php else: ?>
				
			<?php dynamic_sidebar( 'primary' ); ?>
		
		<?php endif; ?>

		<?php do_atomic( 'after_sidebar_primary' ); // dbc_after_sidebar_primary ?>

	</div><!-- #sidebar-primary -->

<?php endif; ?>
