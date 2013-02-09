<?php get_header(); ?>

    <?php if ((is_home() ) && (get_option_tree( 'homepage_caption' ) || (get_option_tree( 'homepage_button' ) ) ) ) { ?>    
	<div id="topMessage">
		<div id="messageWrap">
			<div class="columns columns-2" style="margin: 0;">
				<div class="column">
					<p><?php echo get_option_tree( 'homepage_caption' ); ?> </p>
				</div>
	 			<div class="column column-last alignright">
					<a href="http://www.facebook.com/pages/CollegeLife/104722509624498"><img src="<?php bloginfo( 'siteurl' ); ?>/wp-content/themes/CircloSquero/images/facebook.png" alt="CollegeLife's Facebook page" /></a>
					<a href="http://twitter.com/collegelife_dbc"><img src="<?php bloginfo( 'siteurl' ); ?>/wp-content/themes/CircloSquero/images/twit.png" alt="CollegeLife's Tiwtter page" /></a>
					<a href="http://vimeo.com/user7791912"><img src="<?php bloginfo( 'siteurl' ); ?>/wp-content/themes/CircloSquero/images/vimeo.png" alt="CollegeLife's Viemo page" /></a>
					<a href="http://feeds.feedburner.com/college_life"><img src="<?php bloginfo( 'siteurl' ); ?>/wp-content/themes/CircloSquero/images/rss.png" alt="Subscribe to updates via email or RSS" /></a>
				</div>
			</div>
		</div>
	</div>
	<div class="clear"> </div>
	</div>
	<?php ;}  ?>
    <div class="separatorFull"></div>
    <div class="shadowBg">
    <?php $readmore =  get_option_tree( 't_read_more' ); ?>
    <?php $no_comments_yet =  get_option_tree( 't_no_comments' ); ?>
    <?php $one_comment =  get_option_tree( 't_comment' ); ?>
    <?php $x_comments =  get_option_tree( 't_x_comments' ); ?>    
    
    <?php  if ( get_option_tree( 'homepage_layout' ) == 'Blog' ) {  /*     start blog homepage layout        */ ?> 
        <div class="contentWrap blogG">
            <div class="postlist">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="postWrap">
                    
                    <?php $postimageurl280x225 = get_post_meta($post->ID, 'post-thumb-280x225', true); 
                          $postimageurl600x225 = get_post_meta($post->ID, 'post-thumb-600x225', true);
                        if ($postimageurl280x225) {  /* IF to decide which post layout to use, depending on the thumbnail included ||| 280x225 */ ?>
                            <div class="postThumbWrap"><a href="<?php the_permalink(); ?>"><img src="<?php echo $postimageurl280x225; ?>" alt="<?php the_title(); ?>" width="280" height="225" /></a></div>
                            <div class="eachpostWrap">
                                <h1 class="titleCS"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                                <div class="metaBox">
                                    <div class="dateMeta roundbox"><?php the_time('M j, Y') ?> </div>
                                    <div class="authorMeta roundbox">By <?php the_author_posts_link(); ?></div>
                                    <div class="categoryMeta roundbox"><?php the_category(', '); ?></div>
                                    <div class="commentMeta roundbox"><?php comments_number($no_comments_yet , $one_comment, '% '.$x_comments); ?></div>
                                </div><div class="clear"></div>
                             <?php   $content =  strip_shortcodes( get_the_content($readmore) );
                             $content = strip_tags($content, '<a>');   ?>
                            <?php echo $content; ?>
                            </div>
                        <?php } elseif ($postimageurl600x225) { /* IF to decide which post layout to use, depending on the thumbnail included ||| 600x225 */ ?>
                            <div class="eachpostWrapFW">
                                <h1 class="titleCS"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                                <div class="metaBox">
                                    <div class="dateMeta roundbox"><?php the_time('M j, Y') ?> </div>
                                    <div class="authorMeta roundbox">By <?php the_author_posts_link(); ?></div>
                                    <div class="categoryMeta roundbox"><?php the_category(', '); ?></div>
                                    <div class="commentMeta roundbox"><?php comments_number($no_comments_yet, $one_comment, '% '.$x_comments); ?></div>
                                </div><div class="clear"></div>
                                <div class="postThumbWrapFW"><a href="<?php the_permalink(); ?>"><img src="<?php echo $postimageurl600x225; ?>" alt="<?php the_title(); ?>" width="600" height="225" /></a></div>
                                <div class="clear"> </div>                                                                                                                                                                                        
                             <?php   $content =  strip_shortcodes( get_the_content($readmore) );
                             $content = strip_tags($content, '<a>');   ?>
                            <?php echo $content; ?>
                            </div>    
                        <?php } else { /* IF to decide which post layout to use, depending on the thumbnail included ||| no thumbnail */ ?>
                        <div class="eachpostWrapFW">
                            <h1 class="titleCS"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                                <div class="metaBox">
                                    <div class="dateMeta roundbox"><?php the_time('M j, Y') ?></div>
                                    <div class="authorMeta roundbox">By <?php the_author_posts_link(); ?></div>
                                    <div class="categoryMeta roundbox"><?php the_category(', '); ?></div>
                                    <div class="commentMeta roundbox"><?php comments_number($no_comments_yet, $one_comment, '% '.$x_comments); ?></div>
                                </div><div class="clear"></div>
                             <?php   $content =  strip_shortcodes( get_the_content($readmore) );
                             $content = strip_tags($content, '<a>');   ?>
                            <?php echo $content; ?>
                        </div>  
                        <?php }; ?>
                </div>
                <div class="clear"></div>
                <?php endwhile; else: ?>
                <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
                <?php endif; ?>
                <div id="paginationCS">
<?php $p_next =  get_option_tree( 'p_next' );
$p_prev =  get_option_tree( 'p_prev' ); ?>
                        <?php if (  $wp_query->max_num_pages > 1 ) { 
                            circlosquero_pagination($p_next, $p_prev);
                        }; ?>

                </div>
            </div>           
            <?php $d = get_sidebar(); ?>                           
        </div>                                             <?php /* end blog homepage layout */ ?>
    <?php  } elseif ( get_option_tree( 'homepage_layout' ) == 'Corporate') {  /*       start corporate homepage layout        */ ?>
    <div class="spacernew"></div>
        <?php
        $page_id1 = get_option_tree( 'homepagepage' );
        $page_data = get_page( $page_id1 );
        ?>
        <div class="contentWrap corporate">
            <?php show_post($page_id1);  ?>
        </div>  
   
    <?php  } else {  /*       start mix homepage layout        */ ?>
        <?php
        $page_id2 = get_option_tree( 'homepagepage' );
        $page_data = get_page( $page_id2 );
        ?>
        <div class="spacernew"></div>
        <div class="contentWrap mix">
            <?php show_post($page_id2);  ?>
            <div class="clear"> </div>
 <div class="postlist">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="postWrap">
                    
                    <?php $postimageurl280x225 = get_post_meta($post->ID, 'post-thumb-280x225', true); 
                          $postimageurl600x225 = get_post_meta($post->ID, 'post-thumb-600x225', true);
                        if ($postimageurl280x225) {  /* IF to decide which post layout to use, depending on the thumbnail included ||| 280x225 */ ?>
                            <div class="postThumbWrap"><a href="<?php the_permalink(); ?>"><img src="<?php echo $postimageurl280x225; ?>" alt="<?php the_title(); ?>" width="280" height="225" /></a></div>
                            <div class="eachpostWrap">
                                <h1 class="titleCS"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                                <div class="metaBox">
                                    <div class="dateMeta roundbox"><?php the_time('M j, Y') ?> </div>
                                    <div class="authorMeta roundbox">By <?php the_author_posts_link(); ?></div>
                                    <div class="categoryMeta roundbox"><?php the_category(', '); ?></div>
                                    <div class="commentMeta roundbox"><?php comments_number($no_comments_yet,$one_comment,'% '.$x_comments); ?></div>
                                </div><div class="clear"></div>
                             <?php   $content =  strip_shortcodes( get_the_content($readmore) );
                             $content = strip_tags($content, '<a>');   ?>
                            <?php echo $content; ?>
                            </div>
                        <?php } elseif ($postimageurl600x225) { /* IF to decide which post layout to use, depending on the thumbnail included ||| 600x225 */ ?>
                            <div class="eachpostWrapFW">
                                <h1 class="titleCS"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                                <div class="metaBox">
                                    <div class="dateMeta roundbox"><?php the_time('M j, Y') ?> </div>
                                    <div class="authorMeta roundbox">By <?php the_author_posts_link(); ?></div>
                                    <div class="categoryMeta roundbox"><?php the_category(', '); ?></div>
                                    <div class="commentMeta roundbox"><?php comments_number($no_comments_yet,$one_comment,'% '.$x_comments); ?></div>
                                </div><div class="clear"></div>
                                <div class="postThumbWrapFW"><a href="<?php the_permalink(); ?>"><img src="<?php echo $postimageurl600x225; ?>" alt="<?php the_title(); ?>" width="600" height="225" /></a></div>
                                <div class="clear"> </div>                                                                                                                                                                                        
                             <?php   $content =  strip_shortcodes( get_the_content($readmore) );
                             $content = strip_tags($content, '<a>');   ?>
                            <?php echo $content; ?>
                            </div>    
                        <?php } else { /* IF to decide which post layout to use, depending on the thumbnail included ||| no thumbnail */ ?>
                        <div class="eachpostWrapFW">
                            <h1 class="titleCS"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                                <div class="metaBox">
                                    <div class="dateMeta roundbox"><?php the_time('M j, Y') ?></div>
                                    <div class="authorMeta roundbox">By <?php the_author_posts_link(); ?></div>
                                    <div class="categoryMeta roundbox"><?php the_category(', '); ?></div>
                                    <div class="commentMeta roundbox"><?php comments_number($no_comments_yet,$one_comment,'% '.$x_comments); ?></div>
                                </div><div class="clear"></div>
                             <?php   $content =  strip_shortcodes( get_the_content($readmore) );
                             $content = strip_tags($content, '<a>');   ?>
                            <?php echo $content; ?>
                        </div>  
                        <?php }; ?>
                </div>
                <div class="clear"></div>
                <?php endwhile; else: ?>
                <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
                <?php endif; ?>
                <div id="paginationCS">
<?php $p_next =  get_option_tree( 'p_next' );
$p_prev =  get_option_tree( 'p_prev' ); ?>
                        <?php if (  $wp_query->max_num_pages > 1 ) { 
                            circlosquero_pagination($p_next, $p_prev);
                        }; ?>

                </div>
            </div>           
                        <div class="sidebarRight">
                <ul>
                    <?php if ( !function_exists('dynamic_sidebar')  
                    || !dynamic_sidebar( 'Sidebar' ) ) : ?>  
                    <h2>About</h2>  
                    <p>This is the deafult sidebar, add some widgets to change it.</p>  
                    <?php endif; ?>
                </ul>
            </div>                      
        </div>  
    <?php  };  ?>
        
<div class="clear"> </div>
<?php get_footer(); ?>

