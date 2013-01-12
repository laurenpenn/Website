<?php 
    // get slider control options
	$transitionspeed = get_option('hp_transition_speed');
	// get options for slider content choice
	$sliderposts = get_option('hp_show_sliderposts');
	$sliderchoice = get_option('hp_show_slider_choice');
?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#slider').s3Slider({
            timeOut: <?php if ( $displaytime ) : ?><?php echo $displaytime ?><?php else : ?>5000<?php endif; ?>
        });
    });
</script>
<div id="sliderWrap">
    <div id="slider">
        <ul id="sliderContent">
        
        <!-- If user wants to pull posts from a category for the slider do this stuff -->
        <?php if ($sliderposts== "true") : ?>
        <?php
			$featurecat = get_option('hp_featurecat',true);
			$postnumber = get_option('hp_postnumber',true);
			$my_query = new WP_Query('category_name='.$featurecat.'&showposts='.$postnumber.'');
			while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
        ?>
                
            <li class="sliderImage">
                <a href="<?php the_permalink() ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "feature_image", true ); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" /></a>
                <span class="sliderbottom"><strong><?php the_title(); ?></strong></span>
            </li>
            
         <?php endwhile; ?>
         <?php endif; ?>
         
         <!-- If user wants to define specific content in the slider do this stuff -->
         <?php if ($sliderchoice== "true") : ?>  
         
         <!-- Slider item 1 -->
         <?php if (get_option('hp_slider_image1')) : ?>
         <li class="sliderImage">
                <a href="<?php echo get_option('hp_slider_link1'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image1'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" /></a>
                <span class="sliderbottom"><strong><?php echo get_option('hp_slider_title1'); ?></strong></span>
         </li>
         <?php endif; ?>
         
         <!-- Slider item 2 -->
         <?php if (get_option('hp_slider_image2')) : ?>
         <li class="sliderImage">
                <a href="<?php echo get_option('hp_slider_link2'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image2'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" /></a>
                <span class="sliderbottom"><strong><?php echo get_option('hp_slider_title2'); ?></strong></span>
         </li>
         <?php endif; ?>
         
         <!-- Slider item 3 -->
         <?php if (get_option('hp_slider_image3')) : ?>
         <li class="sliderImage">
                <a href="<?php echo get_option('hp_slider_link3'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image3'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" /></a>
                <span class="sliderbottom"><strong><?php echo get_option('hp_slider_title3'); ?></strong></span>
         </li>
         <?php endif; ?>
         
         <!-- Slider item 4 -->
         <?php if (get_option('hp_slider_image4')) : ?>
         <li class="sliderImage">
                <a href="<?php echo get_option('hp_slider_link4'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image4'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" /></a>
                <span class="sliderbottom"><strong><?php echo get_option('hp_slider_title4'); ?></strong></span>
         </li>
         <?php endif; ?>
         
         <!-- Slider item 5 -->
         <?php if (get_option('hp_slider_image5')) : ?>
         <li class="sliderImage">
                <a href="<?php echo get_option('hp_slider_link5'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image5'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" /></a>
                <span class="sliderbottom"><strong><?php echo get_option('hp_slider_title5'); ?></strong></span>
         </li>
         <?php endif; ?>
         
         <!-- Slider item 6 -->
         <?php if (get_option('hp_slider_image6')) : ?>
         <li class="sliderImage">
                <a href="<?php echo get_option('hp_slider_link6'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image6'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" /></a>
                <span class="sliderbottom"><strong><?php echo get_option('hp_slider_title6'); ?></strong></span>
         </li>
         <?php endif; ?>
         
         <?php endif; ?> 

            <div class="clear sliderImage"></div>
        </ul>
    </div>
</div>
