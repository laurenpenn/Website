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
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/YoungAdultsLogo-web.png" alt="young adults" />
			</div>
			
			<div class="four columns">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/MBF-logo-web.png" alt="Married Bible Fellowship" />
			</div>
			
			<div class="four columns">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/YSBF-logo-web.png" alt="Young Singles Bible Fellowship" />
			</div>
			
						
		</div>
		
		<div class="row">
			<hr>
		</div>
		
		
		<div class="row">
		
			<div class="eight columns">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/videoplayer.jpg" alt="video player" />
			</div>
			
			<div class="four columns">
				<h2>Welcome to Young Adults of Denton Bible Church!</h2>
				<h4>Our prayer and purpose for this ministry is simple...</h4>
				<h4>- Authentic Community: Acts 2:42</h4>
				<h4>- Love of Christ: 1 Timothy 1:5</h4>
				<h4>- Changed Lives: Romans 8:29</h4>
				
				<p>We believe that we live this out through the grid of Psalm 1:1-3...that the blessed man is like a tree planted by a stream...bearing fruit in season and having leaves that do not wither.</p>

				<p>That is our framework for this ministry...to be discerning fruit bearers and shade casters to those around us so that the gospel may be proclaimed through your life in all contexts and as a result lives transformed to the glory of God! </p> 

				<p>Gospel transformation begins and continues with one heart and one soul set upon the purposes of God!</p>

				Join us!

				Let's grow in Christ together,	
				Drew Anderson
					
				
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
