			<?php get_header(); ?>
            
            <?php if (!is_front_page()){ ?>
				<?php if($data['revolution_index'] == true ) { ?>
                    <?php putRevSlider("main_slider") ?>
                <?php } ?>
            <?php } ?>
            
			<?php
			$title = get_the_title();
			if ( $title == "Item–Left Sidebar")  $data['sl_portfolio_details_style'] = "With Sidebar";
			if ( $title == "Item–Right Sidebar")  $data['sl_portfolio_details_style'] = "With Sidebar";
			if ( $title == "Item–Left Sidebar") $data['portfolio_sidebar_position'] = "Left Sidebar";
			if ( $title == "Item–Right Sidebar") $data['portfolio_sidebar_position'] = "Right Sidebar";
			
			if ( $title == "Item-Landscape") $data['sl_portfolio_details_style'] = "Landscape Style";
			?>
			
			
			<?php
				$custom = get_post_custom($post->ID);
				$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large'); 
				$small_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'blog'); 
				$small_p_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'portfolio-three'); 
				$cat = get_the_category($post->ID); 
				$cat = $cat[0]; 
			?>
            <?php $img1 = get_post_meta($post->ID, 'image', true); ?>
            <?php $img2 = get_post_meta($post->ID, 'image2', true); ?>
            <?php $img3 = get_post_meta($post->ID, 'image3', true); ?>            
            
			<?php if ($data['sl_portfolio_details_style'] == "Portrait Style") { ?>
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                
                <?php if (!is_front_page()){ ?>
					<?php if($data['revolution_index'] == true ) { ?>
                        <?php putRevSlider("main_slider") ?>
                    <?php } ?>
                <?php } ?>
                
                <div class="main_content_area">
                <div class="container inner_content">
                    <section style="padding:0px !important">
                        <div class="row">
                            <div class="span8">
                            	<div class="slider_area">
									<?php if (!((get_post_meta($post->ID, image, true)) || (get_post_meta($post->ID, image2, true)) || (get_post_meta($post->ID, image3, true)) || (get_post_meta($post->ID, video, true)))) { ?>
                                        <div class="row">
                                            <div class="span8 portfolio_item nolink" style="margin-bottom:0px;">
                                                <div class="view view-first">
                                                    <img src="<?php echo $large_image_url[0]; ?>" alt="" />
                                                    <div class="mask">
                                                        <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (get_post_meta($post->ID, video, true));{ ?><?php echo get_post_meta($post->ID, video, true); ?><?php }?>	
                                    <?php if ((get_post_meta($post->ID, 'image', true)) || (get_post_meta($post->ID, 'image2', true)) || (get_post_meta($post->ID, 'image3', true))){ ?>
                                    <div class="theme-default">
                                        <div id="slider" class="nivoSlider">
                                            <?php if (get_post_meta($post->ID, 'image', true)) { ?>
                                                <img src="<?php echo get_post_meta($post->ID, image, true); ?>" alt="" />
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'image2', true)) { ?>
                                                <img src="<?php echo get_post_meta($post->ID, image2, true); ?>" alt="" />
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'image3', true)) { ?>
                                                <img src="<?php echo get_post_meta($post->ID, image3, true); ?>" alt="" />
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                            	</div>
                            </div>
                            <div class="span4 portfolio-description">
                            	<div class="portfolio_post_item_description">
                                    <div><h4 <?php if($data['portfolio_details_pagination'] == false ) { ?>style="margin-bottom:10px;"<?php } ?>><?php the_title(); ?></h4><?php if($data['portfolio_details_pagination'] == true ) { ?><div class="meta"><span> <?php previous_post_link('<strong>< %link</strong>'); ?> </span> <span class="last_item"><?php  next_post_link('<strong>%link ></strong>'); ?></span></div><?php } ?></div>
                                    <?php the_content(''); ?>
                            	</div>
                            </div>
                        </div>
                    </section>
                </div>
                </div>
           
            <?php endwhile;  ?>
	 		<?php endif; ?>
            <?php } ?>
            
            
            
            <?php if ($data['sl_portfolio_details_style'] == "Landscape Style") { ?>
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="main_content_area">
                <div class="container inner_content">
                    <section style=" padding:0px !important">
                        <div class="row">
                            <div class="span12">
                            	<div class="slider_area">
									<?php if (!((get_post_meta($post->ID, image, true)) || (get_post_meta($post->ID, image2, true)) || (get_post_meta($post->ID, image3, true)) || (get_post_meta($post->ID, video, true)))) { ?>
                                        <div class="row">
                                            <div class="span12 portfolio_item nolink" style="margin-bottom:0px;">
                                                <div class="view view-first">
                                                    <img src="<?php echo $large_image_url[0]; ?>" alt="" />
                                                    <div class="mask">
                                                        <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (get_post_meta($post->ID, video, true));{ ?><?php echo get_post_meta($post->ID, video, true); ?><?php }?>	
                                    <?php if ((get_post_meta($post->ID, 'image', true)) || (get_post_meta($post->ID, 'image2', true)) || (get_post_meta($post->ID, 'image3', true))){ ?>
                                    <div class="theme-default">
                                        <div id="slider" class="nivoSlider">
                                            <?php if (get_post_meta($post->ID, 'image', true)) { ?>
                                                <img src="<?php echo get_post_meta($post->ID, image, true); ?>" alt="" />
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'image2', true)) { ?>
                                                <img src="<?php echo get_post_meta($post->ID, image2, true); ?>" alt="" />
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'image3', true)) { ?>
                                                <img src="<?php echo get_post_meta($post->ID, image3, true); ?>" alt="" />
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                            	</div>
                            </div>
                            <div class="clearfix"></div>
                            <div>
                                <div class="span12 portfolio-description">
                                    <div class="portfolio_post_item_description">
                                    	<div><h4 <?php if($data['portfolio_details_pagination'] == false ) { ?>style="margin-bottom:10px;"<?php } ?>><?php the_title(); ?></h4><?php if($data['portfolio_details_pagination'] == true ) { ?><div class="meta"><span> <?php previous_post_link('<strong>< %link</strong>'); ?> </span> <span class="last_item"><?php  next_post_link('<strong>%link ></strong>'); ?></span></div><?php } ?></div>
                                        <?php the_content(''); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                </div>
           
            <?php endwhile;  ?>
	 		<?php endif; ?>
            <?php } ?>
            
            
            <?php if ($data['sl_portfolio_details_style'] == "With Sidebar") { ?>
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="main_content_area">
                    <div class="container inner_content">
                        <section style=" padding:0px !important">
                            <div class="row">
                            	<?php if ($data['portfolio_sidebar_position'] == "Left Sidebar") { ?>
                                <div class="span4 portfolio_sidebar">
                                    <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Portfolio Sidebar") ) : ?>                
                                    <?php endif; ?> 
                                </div>
                                <?php } ?>
                            	<div class="span8">
                                <div class="row">
                                <div class="span8">
                                    <div class="slider_area">
                                        <?php if (!((get_post_meta($post->ID, image, true)) || (get_post_meta($post->ID, image2, true)) || (get_post_meta($post->ID, image3, true)) || (get_post_meta($post->ID, video, true)))) { ?>
                                            <div class="row">
                                            <div class="span8 portfolio_item nolink" style="margin-bottom:0px;">
                                                <div class="view view-first">
                                                    <img src="<?php echo $large_image_url[0]; ?>" alt="" />
                                                    <div class="mask">
                                                        <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if (get_post_meta($post->ID, video, true));{ ?><?php echo get_post_meta($post->ID, video, true); ?><?php }?>	
                                        <?php if ((get_post_meta($post->ID, 'image', true)) || (get_post_meta($post->ID, 'image2', true)) || (get_post_meta($post->ID, 'image3', true))){ ?>
                                        <div class="theme-default">
                                            <div id="slider" class="nivoSlider">
                                                <?php if (get_post_meta($post->ID, 'image', true)) { ?>
                                                    <img src="<?php echo get_post_meta($post->ID, image, true); ?>" alt="" />
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'image2', true)) { ?>
                                                    <img src="<?php echo get_post_meta($post->ID, image2, true); ?>" alt="" />
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'image3', true)) { ?>
                                                    <img src="<?php echo get_post_meta($post->ID, image3, true); ?>" alt="" />
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div>
                                    <div class="span8 portfolio-description">
                                        <div class="portfolio_post_item_description">
		                                    <div><h4 <?php if($data['portfolio_details_pagination'] == false ) { ?>style="margin-bottom:10px;"<?php } ?>><?php the_title(); ?></h4><?php if($data['portfolio_details_pagination'] == true ) { ?><div class="meta"><span> <?php previous_post_link('<strong>< %link</strong>'); ?> </span> <span class="last_item"><?php  next_post_link('<strong>%link ></strong>'); ?></span></div><?php } ?></div>
                                            <?php the_content(''); ?>
                                        </div>
                                    </div>
                                </div>
                            	</div>
                                </div>
                            	<?php if ($data['portfolio_sidebar_position'] == "Right Sidebar") { ?>
                                <div class="span4 portfolio_sidebar">
                                    <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Portfolio Sidebar") ) : ?>                
                                    <?php endif; ?> 
                                </div>
                                <?php } ?>
                            </div>
                        </section>
                    </div>
                </div>
           
            <?php endwhile;  ?>
	 		<?php endif; ?>
            <?php } ?>
            
            
        <!--End main container-->
        <!--Footer-->
        <?php get_footer(); ?>