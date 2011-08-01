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
 
if ( is_active_sidebar( 'primary' ) ) : ?>
	
	<?php do_atomic( 'before_sidebar_primary' ); // dbc_before_sidebar_primary ?>
 
	<div id="sidebar-primary" class="sidebar">

		<?php do_atomic( 'open_sidebar_primary' ); // dbc_open_sidebar_primary ?>

		<?php if ( is_singular('missionary') ): ?>
			
			<p><?php get_the_image( array( 'default_size' => 'medium', 'link_to_post' => false )); ?></p>
			
			<p><a href="<?php echo $missionary_blog_url; ?>" class="button" rel="external">View blog</a></p>
			
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