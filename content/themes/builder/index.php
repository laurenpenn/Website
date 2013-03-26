<?php get_header(); ?>
<?php 
	global $more;
	$more = 0; 
?>
<?php
$title = get_the_title();
if ( $title == "Big Image + Left Sidebar")  $data['blog_sidebar_position'] = "Left Sidebar";
if ( $title == "Mini Image + Right Sidebar")  $data['sl_blog_style'] = "Medium Images";
if ( $title == "Mini Image + Left Sidebar")  $data['sl_blog_style'] = "Medium Images";
if ( $title == "Mini Image + Left Sidebar")  $data['blog_sidebar_position'] = "Left Sidebar";

?>				
        	<?php if (!is_front_page()){ ?>
				<?php if($data['revolution_index'] == true ) { ?>
                    <?php putRevSlider("main_slider") ?>
                <?php } ?>
            <?php } ?>
            <div class="main_content_area">
            <div class="container">
                <div class="row">
                	<?php if ($data['blog_sidebar_position'] == "Left Sidebar") { ?>
                    
                     <?php if (($data['blog_sidebar_position'] == "Left Sidebar") & ($data['sl_blog_style'] == "Medium Images")) { ?>
                    <!--Sidebar-->
                    <div class="span3 blog_sidebar">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Sidebar") ) : ?>                
                    	<?php endif; ?> 
                    </div>
                    <!--/Sidebar-->
                    <?php } ?>
                    
                    
                    <?php if (($data['blog_sidebar_position'] == "Left Sidebar") & ($data['sl_blog_style'] == "Large Images")) { ?>
                    <!--Sidebar-->
                    <div class="span4 blog_sidebar">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Sidebar") ) : ?>                
                    	<?php endif; ?> 
                    </div>
                    <!--/Sidebar-->
                    <?php } ?>
                    
                    
                    
                    
                    
                    <?php } ?>
                    <!--Page contetn-->
                    <div class="<?php if ($data['sl_blog_style'] == "Medium Images") { ?>span9<?php } ?> <?php if ($data['sl_blog_style'] == "Large Images") { ?>span8<?php } ?>">
                        <?php if ( !is_archive() ) { ?>
						<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts('paged='.$paged.'&cat='.$cat); ?>		
                        <?php } ?> 
                        <?php if (!(have_posts())) { ?><div class="span12"><h2 class="colored">There is no posts</h2></div><?php }  ?>   
                        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
                        ?>
                        <?php $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large'); ?>
                        
                            <?php if ($data['sl_blog_style'] == "Large Images") { ?>
                            <div class="row <?php post_class(); ?>" id="post-<?php the_ID(); ?>" style="margin-bottom:50px;">
                                <div class="span8">
                                    <div class="blog_item">
                                        
                                        <div class="blog_head">
                                            <?php if($data['blog_show_posts_date'] == true ) { ?><div class="date"><h6><?php if($data['blog_show_date_icon'] == true ) { ?><i class="icon-calendar icon-white"></i> <?php } ?><?php the_time('d') ?> <?php if ($data['blog_date_format'] == "American Style") { ?><?php the_time('M') ?><?php } ?> / <?php if ($data['blog_date_format'] == "European Style") { ?><?php the_time('m') ?> / <?php } ?> <?php the_time('Y') ?></h6></div><?php } ?>
                                            <h3><a href="<?php echo the_permalink(); ?>"><?php the_title(); ?> </a></h3>
                                            <div class="meta" <?php if(($data['blog_show_posts_meta_author'] == false) & ($data['blog_show_posts_meta_category'] == false ) & ($data['blog_show_posts_meta_comments'] == false )) { ?>style="margin-bottom:24px;"<?php } ?>>
                                                <?php if($data['blog_show_posts_meta_author'] == true ) { ?><span <?php if(($data['blog_show_posts_meta_category'] == false ) & ($data['blog_show_posts_meta_comments'] == false )) { ?>  class="last_item"<?php } ?> ><strong><?php __("By","builder"); ?></strong> <?php the_author_posts_link() ?></span><?php } ?>
                                                <?php if($data['blog_show_posts_meta_category'] == true ) { ?><span <?php if(($data['blog_show_posts_meta_comments'] == false )) { ?>  class="last_item"<?php } ?>><?php $tag = get_the_tags(); if (! $tag) { ?> <?php __("There are no tags","builder"); ?><?php } else { ?><?php the_tags(''); ?><?php } ?></span><?php } ?>
                                                <?php if($data['blog_show_posts_meta_comments'] == true ) { ?><span class="last_item"><a href="<?php the_permalink() ?>#comments"><?php comments_number('0','1','%')?>  <?php __("comments","builder"); ?></a></span><?php } ?>
                                            </div>
                                        </div>
										
										<?php if (get_post_meta($post->ID, video, true));{ ?>
                                        	<div style=" margin-bottom:-5px !important;">
												<?php echo get_post_meta($post->ID, video, true); ?>
                                            </div>
                                        <?php }?>
                                        <?php if (( has_post_thumbnail())) { ?>
                                        
                                        <div class="row">
                                            <div class="span8 slider_area">
                                                <div class="view view-first <?php if ($data['blog_image_hover_icons'] == "Zoom icon only") { ?>nolink <?php } ?> <?php if ($data['blog_image_hover_icons'] == "Link icon only") { ?>noinfo <?php } ?>">
                                                    <img src="<?php echo $large_image_url[0]; ?>" alt="" />
                                                    <div class="mask">
                                                    	<?php if ($data['blog_image_hover_icons'] == "Zoom icon + Link icon") { ?>
                                                        <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                    	<a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                        <?php } ?>
                                                        <?php if ($data['blog_image_hover_icons'] == "Zoom icon only") { ?>
                                                        <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                        <?php } ?>
                                                        <?php if ($data['blog_image_hover_icons'] == "Link icon only") { ?>
                                                        <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>

                                        <?php } ?>
                                        <div class="blog_item_description" <?php if($data['blog_item_description_padding'] == 0 ) { ?>style="margin-top:20px;"<?php } ?>>
                                        	<?php the_content('<h6 class="read_more" style="margin-bottom:-20px !important"><a style="margin-top:15px;" href="'. get_permalink($post->ID) . '">'. __("Read More","commander") .'</a></h6>'); ?>
                                    	</div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            
                            
                            
                            <?php if ($data['sl_blog_style'] == "Medium Images") { ?>
                            <div class="row <?php post_class(); ?>" id="post-<?php the_ID(); ?>" style="margin-bottom:50px;">
                                <div class="span9">
                                    <div class="blog_item">
										
                                        <?php if (( has_post_thumbnail()) || (get_post_meta($post->ID, video, true))) { ?>
                                        
                                        <div class="row">
                                        	<?php if (( has_post_thumbnail())) { ?>
                                            <div class="span4 slider_area"> 
                                                <div class="view view-first <?php if ($data['blog_image_hover_icons'] == "Zoom icon only") { ?>nolink <?php } ?> <?php if ($data['blog_image_hover_icons'] == "Link icon only") { ?>noinfo <?php } ?>">
                                                    <img src="<?php echo $large_image_url[0]; ?>" alt="" />
                                                    <div class="mask">
                                                    	<?php if ($data['blog_image_hover_icons'] == "Zoom icon + Link icon") { ?>
                                                        <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto"  title="<?php the_title(); ?>" class="info"></a>
                                                    	<a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                        <?php } ?>
                                                        <?php if ($data['blog_image_hover_icons'] == "Zoom icon only") { ?>
                                                        <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto"  title="<?php the_title(); ?>" class="info"></a>
                                                        <?php } ?>
                                                        <?php if ($data['blog_image_hover_icons'] == "Link icon only") { ?>
                                                        <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
												<div class="clearfix"></div>
                                            </div>
                                                <?php }?>
                                                <?php if (get_post_meta($post->ID, video, true));{ ?>
                                                	<div class="span4" style="margin-bottom:0px !important;">
													<?php echo get_post_meta($post->ID, video, true); ?>
                                                    </div>
                                                <?php }?>
                                            
                                            
                                            <div class="span5">
                                            	<div class="blog_head">
													<?php if($data['blog_show_posts_date'] == true ) { ?><div class="date"><h6><?php if($data['blog_show_date_icon'] == true ) { ?><i class="icon-calendar icon-white"></i> <?php } ?><?php the_time('d') ?> <?php the_time('M') ?> <?php the_time('Y') ?></h6></div><?php } ?>
                                                    <h3><a href="<?php echo the_permalink(); ?>"><?php the_title(); ?> </a></h3>
                                                    <div class="meta" <?php if(($data['blog_show_posts_meta_author'] == false) & ($data['blog_show_posts_meta_category'] == false ) & ($data['blog_show_posts_meta_comments'] == false )) { ?>style="margin-bottom:24px;"<?php } ?>>
                                                        <?php if($data['blog_show_posts_meta_category'] == true ) { ?><span <?php if(($data['blog_show_posts_meta_comments'] == false )) { ?>  class="last_item"<?php } ?>><?php $tag = get_the_tags(); if (! $tag) { ?> <?php __("There are no tags","builder"); ?><?php } else { ?><?php the_tags(''); ?><?php } ?></span><?php } ?>
                                                        <?php if($data['blog_show_posts_meta_comments'] == true ) { ?><span class="last_item"><a href="<?php the_permalink() ?>#comments"><?php comments_number('0','1','%')?>  <?php __("comments","builder"); ?></a></span><?php } ?>
                                                    </div>
                                                </div>
                                            	<div class="blog_item_description" <?php if($data['blog_item_description_padding'] == 0 ) { ?>style="margin-top:20px;"<?php } ?>>
													<?php the_content('<h6 class="read_more"><a style="margin-top:15px;" href="'. get_permalink($post->ID) . '">'. __("Read More","commander") .'</a></h6>'); ?>
                                                </div>
                                            </div>
                                            
                                        </div>
										<?php } ?>
                                        
                                        
                                        <?php if (!( has_post_thumbnail()) & !(get_post_meta($post->ID, video, true))) { ?>
                                        
                                        <div class="row">
                                            <div class="span9">
                                            	<div class="blog_head">
													<?php if($data['blog_show_posts_date'] == true ) { ?><div class="date"><h6><?php if($data['blog_show_date_icon'] == true ) { ?><i class="icon-calendar icon-white"></i> <?php } ?><?php the_time('d') ?> <?php the_time('M') ?> <?php the_time('Y') ?></h6></div><?php } ?>
                                                    <h3><a href="<?php echo the_permalink(); ?>"><?php the_title(); ?> </a></h3>
                                                    <div class="meta" <?php if(($data['blog_show_posts_meta_author'] == false) & ($data['blog_show_posts_meta_category'] == false ) & ($data['blog_show_posts_meta_comments'] == false )) { ?>style="margin-bottom:24px;"<?php } ?>>
                                                        <?php if($data['blog_show_posts_meta_category'] == true ) { ?><span <?php if(($data['blog_show_posts_meta_comments'] == false )) { ?>  class="last_item"<?php } ?>><?php $tag = get_the_tags(); if (! $tag) { ?> There are no tags<?php } else { ?><?php the_tags(''); ?><?php } ?></span><?php } ?>
                                                        <?php if($data['blog_show_posts_meta_comments'] == true ) { ?><span class="last_item"><a href="<?php the_permalink() ?>#comments"><?php comments_number('0','1','%')?>  comments</a></span><?php } ?>
                                                    </div>
                                                </div>
                                            	<div class="blog_item_description" <?php if($data['blog_item_description_padding'] == 0 ) { ?>style="margin-top:20px;"<?php } ?>>
													<?php the_content('<h6 class="read_more"><a style="margin-top:15px;" href="'. get_permalink($post->ID) . '">'. __("Read More","commander") .'</a></h6>'); ?>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        
										<?php } ?>
                                        
                                        
                                        

                                        
                                        
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            
                            
                            
                            
						<?php endwhile;  ?> 
						<?php endif; ?>
                        <section style="padding:0px !important;">
	                        <hr style="margin-top:0px;">
							<?php if (function_exists('wp_corenavi')) { ?><div class="pride_pg"><?php wp_corenavi(); ?></div><?php }?>
                        </section>
                    </div>
                    <!--/Page contetn-->
                    <?php if (($data['blog_sidebar_position'] == "Right Sidebar") & ($data['sl_blog_style'] == "Medium Images")) { ?>
                    <!--Sidebar-->
                    <div class="span3 blog_sidebar">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Sidebar") ) : ?>                
                    	<?php endif; ?> 
                    </div>
                    <!--/Sidebar-->
                    <?php } ?>
                    
                    
                    <?php if (($data['blog_sidebar_position'] == "Right Sidebar") & ($data['sl_blog_style'] == "Large Images")) { ?>
                    <!--Sidebar-->
                    <div class="span4 blog_sidebar">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Sidebar") ) : ?>                
                    	<?php endif; ?> 
                    </div>
                    <!--/Sidebar-->
                    <?php } ?>
                    
                </div>
            </div>
            </div>
        

<?php get_footer(); ?>