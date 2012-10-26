<?php
/**
 * Template Name: Home
 *
 * This is the default page template.  It is used when a more specific template can't be found to display 
 * singular views of pages.
 *
 * @package Prototype
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // prototype_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // prototype_open_content ?>
		
		<div class="row">
			
			<div class="eight columns">
				
				<?php get_template_part( 'slider-home' ); // loads slider-home.php ?>
				
			</div>
			
			<div id="social-icons" class="four columns">
				
				<h2>Find what you're looking for...</h2>
				
				<?php get_template_part( 'searchform' ); ?>
				
				<h2>Connect with us here...</h2>
				
				<div class="three columns">				
				<a href="http://www.facebook.com/mensministryofdbc"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-facebook.png" alt="Facebook" /></a>
				</div>
				<div class="three columns">		
				<a href="https://twitter.com/REALMENofDBC"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-twitter.png" alt="Twitter" /></a>
				</div>
				<div class="three columns">		
				<a href="https://vimeo.com/user9865105"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-vimeo.png" alt="Vimeo" /></a>
				</div>
				<div class="three columns">		
				<a href="http://www.goodreads.com/user/show/7333648-the-men-s-ministry-of-denton-bible-church"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-goodreads.png" alt="Goodreads" /></a>
				</div>
				
			</div>
			
		</div>
		
		<div id="features" class="row">
			
			<div class="three columns">
				<a href="<?php echo site_url(); ?>/connect/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/connect-button.jpg" alt="Connect" /></a>
			</div>
			
			<div class="three columns">
				<a href="<?php echo site_url(); ?>/grow/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/grow-button.jpg" alt="Grow" /></a>
			</div>
			
			<div class="three columns">
				<a href="<?php echo site_url(); ?>/gather/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/gather-button.jpg" alt="Gather" /></a>
			</div>
			
			<div class="three columns">
				<a href="<?php echo site_url(); ?>/serve/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/serve-button.jpg" alt="Serve" /></a>
			</div>
						
		</div>

		<div class="row">
			
			<div class="eight columns">
												
				<?php
				$args = array(
					'post_type' => 'home_page_tab',
				);
				
				$home_page_tabs = new WP_Query( $args );
				?>
	
				<?php if ( $home_page_tabs->have_posts() ) : $i = 0;  ?>

					<ul class="tabs-content contained">

					<?php while ( $home_page_tabs->have_posts() ) : $home_page_tabs->the_post(); $i++; ?>
				
						<li id="simple<?php echo $i; ?>Tab"<?php if ( $i == 1 ) echo ' class="active"'; ?>>

							<div class="entry-content">
								<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', hybrid_get_textdomain() ) ); ?>
								<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', hybrid_get_textdomain() ), 'after' => '</p>' ) ); ?>
							</div><!-- .entry-content -->
				
						</li><!-- .hentry -->
	
					<?php endwhile; ?>

					</ul>
						
				<?php endif; ?>
							
			</div>
			
			<div class="four columns">
				
				<div class="fb-like-box" data-href="https://www.facebook.com/mensministryofdbc" data-width="390" data-height="545" data-show-faces="false" data-stream="true" data-header="false"></div>
			
			</div>
			
		</div>	

		<?php do_atomic( 'close_content' ); // prototype_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // prototype_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>
