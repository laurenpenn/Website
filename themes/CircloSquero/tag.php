<?php get_header(); ?>
<div id="topinfobar">
    <div id="<?php if (get_option_tree( 'cs_search_d' ) == 'No' ) {echo 'pageinfoFW';} else {echo 'pageinfo'; };?>">
    <?php echo '<h2>'; single_tag_title(); echo '</h2>'; ?>
    </div>
    <?php if (get_option_tree( 'cs_search_d' ) == 'Yes' ) { ?> 
    <div id="searchTB">
        <form role="search" method="get" id="searchform" action="<?php echo home_url(); ?>" >
            <div><input type="text" value="<?php echo get_option_tree( 't_search' );?>" name="s" id="s" class="CS_searchform" onfocus="if (this.value == '<?php echo get_option_tree( 't_search' );?>') {this.value = '';}";" /><input type="submit" id="searchsubmit" value="" class="CS_searchform_button" />
            </div>
        </form>
    </div>
    <?php }; ?>
    <div class="clear"></div>
</div>
</div>
<div id="separator22"></div>
<div class="shadowBg">
    <div class="breadcrumbs">
        <?php
        if(function_exists('bcn_display'))
        {
            bcn_display();
        }
        ?>
    <div class="separatorDots hcsdt separatorDotsPage2"></div>
    </div><div class="clear"></div>
    
    <?php $readmore =  get_option_tree( 't_read_more' ); ?>
    <?php $no_comments_yet =  get_option_tree( 't_no_comments' ); ?>
    <?php $one_comment =  get_option_tree( 't_comment' ); ?>
    <?php $x_comments =  get_option_tree( 't_x_comments' ); ?>    

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
                                <?php the_content($readmore); ?>
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
                                <?php the_content($readmore); ?>
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
                            <?php the_content($readmore); ?>
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
        </div>   <div class="clear"></div>                 
<?php get_footer(); ?>