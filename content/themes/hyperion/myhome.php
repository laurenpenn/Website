<?php
/*
Template Name: Home
*/
?>

<?php get_header(); ?>
    
<?php $showfullslider = get_option('hp_show_fullslider'); ?>
<?php if ($showfullslider== "true") : ?>
	<?php include('inc/sliders/wide-slider.php'); ?>
<?php endif; ?>

<?php $showcompactslider = get_option('hp_show_compactslider'); ?>
<?php if ($showcompactslider== "true") : ?>
	<?php include('inc/sliders/compact-slider.php'); ?>
<?php endif; ?>

<?php $showfadingslider = get_option('hp_show_fadingslider'); ?>
<?php if ($showfadingslider== "true") : ?>
	<?php include('inc/sliders/fading-slider.php'); ?>
<?php endif; ?>

<!--  DISPLAYS THE GET IN TOUCH BUTTON AND TEASER TEXT  -->
<?php $intouch = get_option('hp_show_intouch'); ?>
<?php if ($intouch== "true") : ?>
<div id="intouch">
	<div class="intouch-text">
    <p><?php echo stripslashes(get_option('hp_intouch_teaser')); ?></p>
    </div>
   		<a class="intouch-button" href="<?php echo stripslashes(get_option('hp_intouch_formurl')); ?>">Get in touch.</a>
</div>
<?php endif; ?>

<div class="content-topper"></div> <!--TRANSPARENT DROP SHADOW-->
<div id="content-wrapper"> <!--BEGIN CONTENT WRAPPER-->

<!--DISPLAYS 3 INFORMATION BOXES WITH IMAGES AND TEXT AND Optional Lightbox-->
<?php 
$infoboxes = get_option('hp_show_infoboxes');
$homevideo = get_option('hp_home_lightbox_video');
$homevideo2 = get_option('hp_home_lightbox_video2');
$homevideo3 = get_option('hp_home_lightbox_video3');
$infoboxeslight = get_option('hp_show_homelightbox');
?>
<?php if ($infoboxes== "true") : ?>
	<div id="info-boxes">
    	<div class="infobox">
        	<h3><?php echo stripslashes(get_option('hp_box1_title')); ?></h3>
            <h4><?php echo stripslashes(get_option('hp_box1_subtext')); ?></h4>
			<?php if ($infoboxeslight== "true") : ?>
            <div class="hoverbox">
            <a <?php if ($homevideo== "true") : ?>class="hover-zoom-video-home"<?php  else : ?>class="hover-zoom"<?php endif; ?> href="<?php echo get_option('hp_box1_content'); ?>" rel="prettyPhoto[pp_gal]"><img src="<?php echo get_option('hp_box1_image'); ?>" alt="<?php echo get_option('hp_box1_title'); ?>" width="272px" height="159px"/></a>
            </div>
            <?php  else : ?>
            <img src="<?php echo get_option('hp_box1_image'); ?>" alt="<?php echo get_option('hp_box1_title'); ?>" width="272px" height="159px"/>            
			<?php endif; ?>
            <p><?php echo stripslashes(get_option('hp_box1_maintext')); ?></p>
            <p><a href="<?php echo get_option('hp_box1_morelink'); ?>"><?php echo stripslashes(get_option('hp_box_moretext')); ?></a></p>
        </div>
    	<div class="infobox">
        	<h3><?php echo stripslashes(get_option('hp_box2_title')); ?></h3>
            <h4><?php echo stripslashes(get_option('hp_box2_subtext')); ?></h4>
            <?php $infoboxes = get_option('hp_show_homelightbox'); ?>
			<?php if ($infoboxes== "true") : ?>
            <div class="hoverbox">
            <a <?php if ($homevideo2== "true") : ?>class="hover-zoom-video-home"<?php  else : ?>class="hover-zoom"<?php endif; ?> href="<?php echo get_option('hp_box2_content',true); ?>" rel="prettyPhoto[pp_gal]"><img src="<?php echo get_option('hp_box2_image',true); ?>" alt="<?php echo get_option('hp_box2_title',true); ?>" width="272px" height="159px"/></a>
            </div>
            <?php  else : ?>
            <img src="<?php echo get_option('hp_box2_image',true); ?>" alt="<?php echo get_option('hp_box2_title',true); ?>" width="272px" height="155px"/>            <?php endif; ?>
            <p><?php echo stripslashes(get_option('hp_box2_maintext',true)); ?></p>
            <p><a href="<?php echo get_option('hp_box2_morelink',true); ?>"><?php echo stripslashes(get_option('hp_box_moretext',true)); ?></a></p>
        </div>
    	<div class="infobox-last">
        	<h3><?php echo stripslashes(get_option('hp_box3_title',true)); ?></h3>
            <h4><?php echo stripslashes(get_option('hp_box3_subtext',true)); ?></h4>
            <?php $infoboxes = get_option('hp_show_homelightbox'); ?>
			<?php if ($infoboxes== "true") : ?>
            <div class="hoverbox">
            <a <?php if ($homevideo3== "true") : ?>class="hover-zoom-video-home"<?php  else : ?>class="hover-zoom"<?php endif; ?> href="<?php echo get_option('hp_box3_content',true); ?>" rel="prettyPhoto[pp_gal]"><img src="<?php echo get_option('hp_box3_image',true); ?>" alt="<?php echo get_option('hp_box3_title',true); ?>" width="272px" height="155px"/> </a>
            </div>
            <?php  else : ?>
            <img src="<?php echo get_option('hp_box3_image',true); ?>" alt="<?php echo get_option('hp_box3_title',true); ?>" width="272px" height="155px"/>            <?php endif; ?>
            <p><?php echo stripslashes(get_option('hp_box3_maintext',true)); ?></p>
            <p><a href="<?php echo get_option('hp_box3_morelink',true); ?>"><?php echo stripslashes(get_option('hp_box_moretext',true)); ?></a></p>
        </div>
    </div>
<?php endif; ?>

<div id="content"> <!--BEGIN MAIN CONTENT-->

<!--DISPLAYS BLOG AND NEWS ENTRIES-->
<?php $blognews = get_option('hp_show_blognews'); ?>
<?php if ($blognews== "true") : ?>
	<div id="blog-news"> <!--  IF ENABLED START BLOG AND NEWS ENTRIES  -->
    
    	<div class="blog-home"> <!--  BEGIN BLOG ENTRIES  -->
        	<h3><?php echo stripslashes(get_option('hp_homeblog_title')); ?></h3>
            <h4><?php echo stripslashes(get_option('hp_homeblog_sub_title')); ?></h4>
			<?php
				$homeblogcat = get_option('hp_home_blogcat',true);
				$blogpostnumber = get_option('hp_blog_postnumber',true);
				query_posts('category_name='.$homeblogcat.'&showposts='.$blogpostnumber.''); while (have_posts()) : the_post();
            ?>

            <div class="home-post" id="post-<?php the_ID(); ?>">
			<?php 
                $image = get_post_meta($post->ID, 'post_image', true); 
                $videoimage = get_post_meta( $post->ID, "video_image", true);
            ?>
				<?php if ( $videoimage ) : ?>
                <a href="<?php the_permalink() ?>" rel="bookmark"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "video_image", true ); ?>&amp;w=200&amp;h=150&amp;zc=1&amp;q=100" alt="<?php the_title(); ?>" class="homepost-thumb" /></a>
                <?php endif; ?>
                
                <?php if ( $image ) : ?>
                <a href="<?php the_permalink() ?>" rel="bookmark"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "post_image", true ); ?>&amp;w=200&amp;h=150&amp;zc=1&amp;q=100" alt="<?php the_title(); ?>" class="homepost-thumb" /></a>
                <?php endif; ?>

                <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                <p class="post-meta"><?php the_time('m/d/y') ?> by <?php the_author_posts_link() ?> | <?php the_category(', ') ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?> <span class="edit"><?php edit_post_link('Edit', '| ', ''); ?></span></p>
                <?php global $more; $more = FALSE; ?><?php the_content('Read more &rarr;'); ?>
            </div>
            <?php endwhile; ?>
        </div> <!--  END BLOG ENTRIES  -->
        
        <div class="news-home"><!--  BEGIN NEWS ENTRIES  -->
        	<h3><?php echo stripslashes(get_option('hp_homenews_title')); ?></h3>
            <h4><?php echo stripslashes(get_option('hp_homenews_sub_title')); ?></h4>
			<?php
            $homenewscat = get_option('hp_home_newscat',true);
            $newspostnumber = get_option('hp_news_postnumber',true);
            query_posts('category_name='.$homenewscat.'&showposts='.$newspostnumber.''); while (have_posts()) : the_post();
            ?>
            <div class="home-news-post" id="post-<?php the_ID(); ?>">
            <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <?php the_content(''); ?>
            </div>
            <?php endwhile; ?>        
        </div> <!--  END NEWS ENTRIES  -->       

    </div> <!--  END OF BLOG AND NEWS ENTRIES  -->
<?php endif; ?>

<?php get_footer(); ?>
