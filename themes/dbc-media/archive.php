<?php get_header(); ?>
		       
        <div id="bd">
						   
			<div id="yui-main">
				
				<div class="yui-b">
				
					<div id="page" class="yui-g">
	
				       <?php if (have_posts()) : ?>
				        	
		        		<?php if (is_category()) { ?>
						
						<div class="box-container">    
						
							<h1><?php echo single_cat_title(); ?> Category</h1>
		
						<?php } elseif (is_tag()) { ?>
		        	
		            	<h2 class="archive_name"><?php single_tag_title(); ?></h2>        
		            	
		            	<div class="archive_meta">
		            	
		            		<div class="archive_number">
		            			This tag is associated with <?php $tag = get_tags(); if ($tag) { $tag = $tag[0]; echo $tag->count; } ?> posts
		            		</div>           		
		            	
		            	</div>
		            	
						<?php } elseif (is_day()) { ?>
						<h2 class="archive_name">Archive for <?php the_time('F jS, Y'); ?></h2>
		
						<?php } elseif (is_month()) { ?>
						<h2 class="archive_name">Archive for <?php the_time('F, Y'); ?></h2>
		
						<?php } elseif (is_year()) { ?>
						<h2 class="archive_name">Archive for <?php the_time('Y'); ?></h2>
						
						<?php } ?>				        
			            
			            <?php while (have_posts()) : the_post(); ?>
			            
			            	<div class="archive-post-block">
			            		<h3 class="archive_title" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>

			            		<?php if(function_exists('the_excerpt_reloaded')) { the_excerpt_reloaded(140,'','','TRUE',''); } ?> 
			            	</div>
			            	
			            	<?php endwhile; ?>
			
							<div class="navigation">
								<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
								<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
							</div>
			
							<?php else : ?>
			
								<p>Lost? Go back to the <a href="<?php echo get_option('home'); ?>/">home page</a>.</p>
			
						<?php endif; ?>
						
						</div>
							
					</div><!-- end #page -->
					
				</div><!-- end .yui-b -->
				
			</div><!-- end #yui-main -->
			
			<ul class="yui-b">
			
				<?php get_sidebar(); ?>

			</ul><!-- end .yui-b -->  	

   	 	</div><!-- end #bd -->        
        
<?php get_footer(); ?>