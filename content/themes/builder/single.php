<?php get_header(); ?>
<?php
$title = get_the_title();
if ( $title == "Post + Left Sidebar")  $data['blog_sidebar_position'] = "Left Sidebar";

?>
			<?php if (!is_front_page()){ ?>
				<?php if($data['revolution_index'] == true ) { ?>
                    <?php putRevSlider("main_slider") ?>
                <?php } ?>
            <?php } ?>
            
        	<div class="main_content_area blog_item_page">
            <div class="container">
                <div class="row">
                	<?php if ($data['blog_sidebar_position'] == "Left Sidebar") { ?>
                    <!--Sidebar-->
                    <div class="span4 blog_sidebar">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Sidebar") ) : ?>                
                    	<?php endif; ?> 
                    </div>
                    <!--/Sidebar-->
                    <?php } ?>
                    <!--Page contetn-->
                    <div class="span8">
                        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                        <?php $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large'); ?>
                            <div class="row <?php post_class(); ?>" id="post-<?php the_ID(); ?>">
                                <div class="span8">
                                	<?php if (get_post_meta($post->ID, video, true));{ ?>
										<?php echo get_post_meta($post->ID, video, true); ?>
                                    <?php }?>
                                    <?php if($data['blog_post_show_featured_image'] == true ) { ?>
                                	<?php if (( has_post_thumbnail())) { ?>
                                        <div class="row">
                                            <div class="span8 blog_item nolink" style="margin-bottom:0px;">
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
                                    <?php }?>
                                </div>
                                <div class="span8">
                                    <div>
                                    	<div class="blog_post_item_description"  style=" <?php if($data['blog_post_item_description_padding'] == 0 ) { ?> margin-top:20px;<?php } ?> <?php if (get_post_meta($post->ID, video, true));{ ?> margin-top:-5px; <?php } ?>" >
                                        <div class="blog_head blog_inner">
                                            <?php if($data['blog_post_show_posts_meta_title'] == true ) { ?><h4 style="font-weight:600"><?php the_title(); ?></h4><?php }?>
                                            <div class="meta">
												<?php if($data['blog_post_show_posts_date'] == true ) { ?><span><?php the_time('d') ?> <?php if ($data['blog_date_format'] == "American Style") { ?><?php the_time('M') ?><?php } ?>  <?php if ($data['blog_date_format'] == "European Style") { ?><?php the_time('m') ?><?php } ?> <?php the_time('Y') ?></span><?php } ?>
                                                <?php if($data['blog_post_show_posts_meta_author'] == true ) { ?><span <?php if(($data['blog_post_show_posts_meta_category'] == false ) & ($data['blog_post_show_posts_meta_comments'] == false )) { ?>  class="last_item"<?php } ?> ><strong><?php __("By","builder"); ?></strong> <?php the_author_posts_link() ?></span><?php } ?>
                                                <?php if($data['blog_post_show_posts_meta_category'] == true ) { ?><span <?php if(($data['blog_post_show_posts_meta_comments'] == false )) { ?>  class="last_item"<?php } ?>><?php $tag = get_the_tags(); if (! $tag) { ?> <?php __("There are no tags","builder"); ?><?php } else { ?><?php the_tags(''); ?><?php } ?></span><?php } ?>
                                                <?php if($data['blog_post_show_posts_meta_comments'] == true ) { ?><span class="last_item"><a href="<?php the_permalink() ?>#comments"><?php comments_number('0','1','%')?>  <?php __("comments","builder"); ?></a></span><?php } ?>
                                            </div>
                                        </div>
                                        <?php the_content(''); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if($data['blog_post_show_share_button'] == true ) { ?>
                                <div class="span8">                                
                                    <div class="share">
                                        <span style="float:left; margin-right:10px;"><?php echo $data['blog_post_show_share_button_text']; ?></span>
                                        <div style="float:left">
                                        <!-- AddThis Button BEGIN -->
                                        <div class="addthis_toolbox addthis_default_style ">
                                        <a class="addthis_counter addthis_pill_style"></a>
                                        </div>
                                        <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
                                        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4f88195d6026781e"></script>
                                        <!-- AddThis Button END -->
                                        </div>
                                    </div>
                                </div>
								<?php } ?>
                                <?php if($data['blog_post_show_author'] == true ) { ?>
                                <div class="span8">
                                	<div class="blog_item_description" style="margin-top:30px;">
                                    	<img class="img-polaroid" src="<?php echo stripslashes($data['blog_post_show_author_avatar']) ?>" style="float:left; margin-right:20px; width:80px; height:80px" alt="<?php bloginfo('name'); ?>" />
                                        <h5 style="margin-bottom:5px;font-weight:600 !important;"><?php echo $data['blog_post_show_author_header']; ?></h5>
										<?php the_author_meta('description'); ?> 
                                        <div class="clearfix"></div>                       
                                    </div><!--end author-bio-->
                                </div>
                                <?php } ?>
                                <?php if($data['blog_post_show_comments'] == true ) { ?>
                                <div class="span8">
                                	<div class="comments_div">
                                    <h3 style="font-weight:600 !important; text-transform:uppercase !important;"><?php comments_number('There are no comments yet, but you can be the first','1 Comment:','% Comments:')?></h3>
									<?php comments_template(); ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
						<?php endwhile;  ?> 
						<?php endif; ?>

                    </div>
                    <!--/Page contetn-->
                    <?php if ($data['blog_sidebar_position'] == "Right Sidebar") { ?>
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