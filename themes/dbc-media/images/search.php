<?php get_header(); ?>
<?php $catid = $wpdb->get_var("SELECT term_ID FROM $wpdb->terms WHERE name='Featured'"); ?>

        <div id="bd">
   
			<div id="yui-main">
				
				<div class="yui-b">
				
					<div id="page" class="yui-g">
						
						<div class="box-container">

							<h1>Results for '<?php the_search_query(); ?>'</h1>
							
							<p>Not what you were looking for?</p>
							
							<p>Try searching the store for '<a href="<?php bloginfo('url'); ?>/?s=<?php the_search_query(); ?>&st=shopp"><?php the_search_query(); ?></a>'.</p>
			
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							
							<div id="post-<?php the_ID(); ?>" class="search-result">
							
								<h3 class="search-title"><a href="<?php the_permalink(); ?>" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
							
								<p class="search-desc"><?php the_excerpt(); ?></p>
								
								<p class="search-url"><?php the_permalink(); ?></p>
							
								<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

							</div><!-- end #post -->
						
						<?php endwhile; endif; ?>
						
						</div><!-- end .box-container -->
						
					</div><!-- end #page -->
					
				</div><!-- end .yui-b -->
				
			</div><!-- end #yui-main -->
			
			<ul class="yui-b">
			
				<?php get_sidebar(); ?>

			</ul><!-- end .yui-b -->  	

   	 	</div><!-- end #bd -->        
        
<?php get_footer(); ?>     
