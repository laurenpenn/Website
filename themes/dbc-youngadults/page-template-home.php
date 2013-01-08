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
			
			<div class="four columns">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/YoungAdultsLogo_web.png" alt="young adults" />
			</div>
			
			<div class="eight columns">
				
				<?php get_template_part( 'slider-home' ); // loads slider-home.php ?>
				
			</div>
			
						
		</div>
		
		<div id="features" class="row">
			
			<div class="six columns">
				<a href="<?php echo site_url(); ?>/married-home/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/MBF-button.png" alt="Married Bible Fellowship" /></a>
			</div>
			
			<div class="six columns">
				<a href="<?php echo site_url(); ?>/singles-home/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/YSBF-button.png" alt="Young Singles Bible Fellowship" /></a>
			</div>
						
		</div>
		
		<div class="row">
		
			<div class="eight columns">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/videoplayer.jpg" alt="video player" />
			</div>
			
			<div class="four columns">
				<h2>Welcome to Young Adults of Denton Bible Church!</h2>
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
