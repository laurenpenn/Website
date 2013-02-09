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
    <div class="separatorDots hcsdt"></div>
    </div>
<div class="contentWrap">
            <div class="singlePostCS_FW">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                  
                                                                                                                                                                                                                     
                                <?php the_content(); ?>                        
                    <div class="clear"></div>
                    <?php endwhile; else: ?>
                    <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
                <?php endif; ?>
            </div>           
            <div class="clear"></div>          

</div>                                         

                <div id="paginationCS">


                </div>

<?php get_footer(); ?>
