<?php get_header(); ?>
		       
    <div id="bd">
			  
		<div id="yui-main">
								
			<div class="yui-b">
									
				<div id="page" class="yui-g">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<div class="post" id="post-<?php the_ID(); ?>">
						
						<h1><?php the_title(); ?></h1>
						
						<div class="entry">
						
							<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
						
							<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
						
						</div><!-- end .entry -->
						
						<?php if (function_exists('sharethis_button')) { ?> <div class="share-this"><?php sharethis_button(); ?></div> <?php } ?>
					
					</div><!-- end .box-container -->
					
					<?php endwhile; ?>

					<div class="navigation">
						<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
						<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
					</div>
			
				<?php else : ?>
			
					<h2 class="center">Not Found</h2>
					<p class="center">Sorry, but you are looking for something that isn't here.</p>
					<?php include (TEMPLATEPATH . "/searchform.php"); ?>
			
				<?php endif; ?>
						
				</div><!-- end #page -->
				
			</div><!-- end .yui-b -->
			
		</div><!-- end #yui-main -->
		
		<?php get_sidebar(); ?>

	 </div><!-- end #bd -->        
        
<?php get_footer(); ?>     
