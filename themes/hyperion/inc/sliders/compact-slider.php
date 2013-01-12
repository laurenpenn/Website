<!--  CONTROLS FOR WIDE SLIDER WITH BOUNCING  -->   
<?php 
	// get slider control options
	$displaytime = get_option('hp_display_time');
	$transitionspeed = get_option('hp_transition_speed');
	$use_easing = get_option('hp_use_easing');
	$easing = get_option('hp_easing');
	// get options for slider content choice
	$sliderposts = get_option('hp_show_sliderposts');
	$sliderchoice = get_option('hp_show_slider_choice');
?>
 
<script type="text/javascript">
$(function(){
	$('#carousel').infiniteCarousel({
	displayTime: <?php if ( $displaytime ) : ?><?php echo $displaytime ?><?php else : ?>5000<?php endif; ?>,
	transitionSpeed: <?php if ( $transitionspeed ) : ?><?php echo $transitionspeed ?><?php else : ?>1500<?php endif; ?>,
	textholderHeight : .25,	
	<?php if ($use_easing== "true") : ?>
	easeLeft: '<?php if ( $easing ) : ?><?php echo $easing ?><?php else : ?>easeOutBounce<?php endif; ?>',
	<?php endif; ?>
	displayThumbnailBackground: 0,
	displayProgressBar : 0
	});
});
</script>

<!--This is the overlay shadow and white border for the carousel -->
<div id="carousel-wrapper-compact"></div>
    
    <!--Center section of carousel displaying main item-->
    <div id="carousel-holder-compact">
        <div id="carousel">
        
        <ul>
        
        <!-- If user wants to pull posts from a category for the slider do this stuff -->
        <?php if ($sliderposts== "true") : ?>
        <?php
			$featurecat = get_option('hp_featurecat',true);
			$postnumber = get_option('hp_postnumber',true);
			$my_query = new WP_Query('category_name='.$featurecat.'&showposts='.$postnumber.'');
			while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
        ?>
 
        <li><a href="<?php the_permalink() ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "feature_image", true ); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" alt="<?php the_title(); ?>"/></a>
        <span><a href="<?php the_permalink() ?>" title="View Project"><?php the_title(); ?></a></span></li>
        
        <?php endwhile; ?>
        <?php endif; ?>
        
        <!-- If user wants to define specific content in the slider do this stuff -->
        <?php if ($sliderchoice== "true") : ?>
        
        <!-- Slider item 1 -->
        <?php if (get_option('hp_slider_image1')) : ?>
        <li><a href="<?php echo get_option('hp_slider_link1'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image1'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" alt="<?php echo get_option('hp_slider_title1'); ?>"/></a>
        <span><a href="<?php echo get_option('hp_slider_link1'); ?>" title="View Project"><?php echo get_option('hp_slider_title1'); ?></a></span></li>
        <?php endif; ?>
        
        <!-- Slider item 2 -->
        <?php if (get_option('hp_slider_image2')) : ?>
        <li><a href="<?php echo get_option('hp_slider_link2'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image2'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" alt="<?php echo get_option('hp_slider_title2'); ?>"/></a>
        <span><a href="<?php echo get_option('hp_slider_link2'); ?>" title="View Project"><?php echo get_option('hp_slider_title2'); ?></a></span></li>
        <?php endif; ?>
        
        <!-- Slider item 3 -->
        <?php if (get_option('hp_slider_image3')) : ?>
        <li><a href="<?php echo get_option('hp_slider_link3'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image3'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" alt="<?php echo get_option('hp_slider_title3'); ?>"/></a>
        <span><a href="<?php echo get_option('hp_slider_link3'); ?>" title="View Project"><?php echo get_option('hp_slider_title3'); ?></a></span></li>
        <?php endif; ?>
        
        <!-- Slider item 4 -->
        <?php if (get_option('hp_slider_image4')) : ?>
        <li><a href="<?php echo get_option('hp_slider_link4'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image4'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" alt="<?php echo get_option('hp_slider_title4'); ?>"/></a>
        <span><a href="<?php echo get_option('hp_slider_link4'); ?>" title="View Project"><?php echo get_option('hp_slider_title4'); ?></a></span></li>
        <?php endif; ?>
        
        <!-- Slider item 5 -->
        <?php if (get_option('hp_slider_image5')) : ?>
        <li><a href="<?php echo get_option('hp_slider_link5'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image5'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" alt="<?php echo get_option('hp_slider_title5'); ?>"/></a>
        <span><a href="<?php echo get_option('hp_slider_link5'); ?>" title="View Project"><?php echo get_option('hp_slider_title5'); ?></a></span></li>
        <?php endif; ?>
        
        <!-- Slider item 6 -->
        <?php if (get_option('hp_slider_image6')) : ?>
        <li><a href="<?php echo get_option('hp_slider_link6'); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_option('hp_slider_image6'); ?>&amp;w=960&amp;h=317&amp;zc=1&amp;q=100" width="960" height="317" alt="<?php echo get_option('hp_slider_title6'); ?>"/></a>
        <span><a href="<?php echo get_option('hp_slider_link6'); ?>" title="View Project"><?php echo get_option('hp_slider_title6'); ?></a></span></li>
        <?php endif; ?>
        
        
        <?php endif; ?>
        
        </ul>
    </div>

</div>
