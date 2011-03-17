<?php get_header(); ?>
		       
        <div id="bd" class="single">
   
			<div id="yui-main">
				
				<div class="yui-b">
				
					<div id="post" class="yui-g">

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
														
						<div class="box-container" id="post-<?php the_ID(); ?>">
						
							<h1 class="floatleft"><?php the_title(); ?></h1>
							
							<div class="clear"></div>
												
							<div class="entry">
																				
								<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
							
								<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
							
							</div><!-- end .entry -->

							<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
							
							<?php if (function_exists('sharethis_button')) { ?> <div class="share-this"><?php sharethis_button(); ?></div> <?php } ?>
																	
						</div><!-- end .box-container -->
						
					</div><!-- end #post -->
	
					<?php comments_template(); ?>
										
				</div><!-- end .yui-b -->
				
			</div><!-- end #yui-main -->
			
			<div id="sidebar" class="yui-b">
							
				<div id="sidebar-container">

					<h2>Shopping Cart</h2>
					
					<?php shopp('cart','sidecart'); ?>
					
				</div>
				
			</div><!-- end .yui-b --> 
			
			<?php endwhile; endif; ?>	

   	 	</div><!-- end #bd -->        
        
<?php get_footer(); ?>     
