<?php
/*
	Template Name: Portfolio Style 3
*/
?>
<?php get_header(); ?>


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
						
								<!-- begin portfolio container 1 -->
								<div class="portfolio_box_container clearfix">
									
									
									<?php if(have_posts()) : ?>
									<?php 
										
										$category_id =  get_post_meta($post->ID, "page_id", true);
										$limit = get_option('portfolio3_limit', 10);
										$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
										query_posts("cat=$category_id&showposts=$limit&paged=$paged");
										
										$i = 1;
										$culomns = get_option('portfolio3_columns', '2');
										$padtop = get_option('portfolio3_thumb_height', '150')/2 - 28;
										$padleft = get_option('portfolio3_thumb_width', '270')/2 - 37;
									?>
									<?php while(have_posts()) : the_post(); 
									
										
									?>
									
									<!-- begin box 1 -->
									<div class="portfolio_box_skin_3" <?php if($i == $culomns){ echo 'style="padding-right:0;"'; } ?>>
										
										<?php if(get_post_meta($post->ID, "thumb_one", true) != "" or get_post_meta($post->ID, "video_one", true) != ""){ ?>
										<!-- begin box image -->
										<div class="portfolio_image_skin2">
											<div class="inside_border">
												<!-- begin image -->
												<div class="portfolio_box_anime">
													<?php if(get_post_meta($post->ID, "video_one", true) != ""){ ?>
													
													<?php echo get_post_meta($post->ID, "video_one", true); ?>
													
													<?php } else { ?>
													
													<?php if(get_post_meta($post->ID, "big_one", true) != ""){ ?>
													
													<a href="<?php echo get_post_meta($post->ID, "big_one", true); ?>" rel="prettyPhoto[canyon_gallery]">
													
													<?php } ?>
													
													<img src="<?php echo get_post_meta($post->ID, "thumb_one", true); ?>" width="<?php echo get_option('portfolio3_thumb_width', '270'); ?>" height="<?php echo get_option('portfolio3_thumb_height', '150'); ?>" alt=""/>
													
													<?php if(get_post_meta($post->ID, "big_one", true) != ""){ ?>	
														<span class="portfolio_zoom" style="padding-top:<?php echo $padtop; ?>px; padding-left:<?php echo $padleft; ?>px;">
															<?php 
																$type = get_post_meta($post->ID, "big_one", true);
																$type =  substr($type, -4);
																if($type == ".jpg" or $type == ".gif" or $type == ".png" or $type == ".jpeg" or $type == ".JPG" or $type == ".GIF" or $type == ".PNG" or $type == ".JPEG"){ ?>
																<img src="<?php bloginfo('template_url'); ?>/images/zoom.png" alt="" />
															<?php } else { ?>
																<img src="<?php bloginfo('template_url'); ?>/images/play.png" alt="" />
															<?php }	?>
														</span>													
													</a>
													<?php } } ?>
													
												</div>
												<!-- end image -->
											</div>
										</div>
										<!-- begin box image -->
										<?php } ?>
										
										<div class="cleardiv"></div>
										
										<!-- begin box details -->
										<div class="portfolio_details_with_categories_skin_2">
											
											<!-- begin box title -->
											<div class="portfolio_title">
												<div class="custom_title"><?php the_title(); ?></div>
											</div>
											<!-- end box title -->
											
											<!-- begin detail text -->
											<div class="portfolio_text"><?php echo get_post_meta($post->ID, "short_desc", true); ?>
											
												<?php if (get_option('disable_portfolio_details', 1) == '1'): ?>
												<div class="portfolio_details_button"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><span class="details_button"><?php echo get_option('portfolio_details_title', 'DETAILS'); ?></span></a></div>
												<?php endif; ?>
											
											</div>
											<!-- begin detail text -->
										</div>
										<!-- end box details -->

									</div>
									<!-- end box 1 -->
									<?php if($i == $culomns){ echo '</div><div class="portfolio_box_container clearfix">'; $i =0; } $i++; ?>
									<?php endwhile; endif; ?>
									
								</div>
								<!-- end portfolio container 1 -->
							

								<div class="cleardiv"></div>
								<!-- begin pages numbers -->
								<div class="pages_numbers">
									<?php include (TEMPLATEPATH . '/includes/get_pagination.php'); ?>
								</div>
								<!-- end pages numbers -->

									
						
						
						
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
								wp_reset_query();
								
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
<?php get_footer(); ?>