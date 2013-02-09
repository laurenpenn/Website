<?php
/*
	Template Name: With Navigation Page
*/
?>
<?php get_header(); ?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				
			<div class="simple_page_title">
				<?php the_title(); ?> 
			</div>
			<?php include (TEMPLATEPATH . '/includes/get_shortcut.php'); ?>
			
			<div class="white_line_alternative"></div>

			<!-- begin recent container -->
			<div class="recent_container clearfix">

				<!-- begin recent posts -->
				<div class="recent_posts clearfix">
					<div class="category_page_content">
						<?php the_content(); ?> 
					</div>
						
				</div>
				<!-- end recent posts -->
				
				<!-- begin recent navigation -->
				<div class="recent_nav">
	
					<!-- begin page navigation -->
					<div class="page_navigation_container">
						
							<!-- begin navigation title -->
							<div class="page_navigation_title">
								<h3><?php echo get_option('navigation_title', 'Navigation'); ?></h3>
							</div>
							<!-- end navigation title -->
							
							<!-- begin navigation categories -->
							<?php
								if($post->post_parent) {
									$children = wp_list_pages("title_li=&sort_column=menu_order&child_of=".$post->post_parent."&echo=0");
								} else {
									$children = wp_list_pages("title_li=&sort_column=menu_order&child_of=".$post->ID."&echo=0");
								}
								if($children){
									echo "<ul>";
									echo $children;
									echo "</ul>";
								} 
							?>
							<!-- begin navigation categories -->
						
							<!-- begin custom navigation -->
								<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Right Widget 1 (All of Pages)')) ?>
								<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Right Widget 2 (All of Pages)')) ?>
								<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Right Widget 3 (All of Pages)')) ?>
								<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Right Widget 4 (All of Pages)')) ?>
								<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Right Widget 5 (All of Pages)')) ?>
							<!-- begin custom navigation -->
						
					</div>
					<!-- end page navigation -->
					
				</div>
				<!-- end recent navigation -->
				
			</div>
			<!-- end recent container -->
			
			<div class="cleardiv"></div>
			
				
<?php endwhile; endif;  ?>

<?php get_footer(); ?>
