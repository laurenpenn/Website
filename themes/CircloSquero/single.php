<?php get_header(); ?>
<div id="topinfobar">
    <div id="<?php if (get_option_tree( 'cs_search_d' ) == 'No' ) {echo 'pageinfoFW';} else {echo 'pageinfo'; };?>">
    <?php $posttitle = get_post_meta($post->ID, 'post_title', true);  ?>
    <?php if ($posttitle) {echo '<h2>'.$posttitle.'</h2>';} else { echo '<h2>'; single_post_title(); echo '</h2>'; }; ?>
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
    </div>
    
    <?php $readmore =  get_option_tree( 't_read_more' ); ?>
    <?php $no_comments_yet =  get_option_tree( 't_no_comments' ); ?>
    <?php $one_comment =  get_option_tree( 't_comment' ); ?>
    <?php $x_comments =  get_option_tree( 't_x_comments' ); ?>   

<div class="contentWrap">
            <div class="singlePostCS">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                  
                                <h1 class="titleCS"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                                <div class="metaBox">
                                    <div class="dateMeta roundbox"><?php the_time('M j, Y') ?> </div>
                                    <div class="authorMeta roundbox">By <?php the_author_posts_link(); ?></div>
                                    <div class="categoryMeta roundbox"><?php the_category(', '); ?></div>
                                    <div class="commentMeta roundbox"><?php comments_number($no_comments_yet , $one_comment, '% '.$x_comments); ?></div>
                                </div><div class="clear"></div>                                                                                                                                                                                      
                                <?php the_content(); ?>                        
                    <div class="clear"></div>
                    <?php endwhile; else: ?>
                    <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
                <?php endif; ?>
                
                <br/>
                <div id="tags">
                <?php the_tags(); ?>   
                </div>
                <div class="separatorDots separatorDots222"></div>

                <div id="aboutAuthor">
                    <h2>About the Author</h2>
                    <div id="authorBoxx">
                        <div id="authorAvatar">
                            <?php echo get_avatar($post->post_author, 60); ?>
                        </div>
                        <div id="authorInfo">
                            <h3><?php the_author(); ?></h3>
                            <?php the_author_meta('user_description'); ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>

                <div class="separatorDots separatorDots222"></div>
                <?php comments_template(); ?> 
            </div>           
            <?php $d = get_sidebar(); ?>
            <div class="clear"></div>
            

</div>                                         

<?php get_footer(); ?>
