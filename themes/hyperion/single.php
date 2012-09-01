<?php get_header(); ?>
    
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
<div id="entry-top">
    <h1><?php the_title(); ?></h1>
    <div class="post-meta"><p>By <?php  the_author_posts_link(''); ?> in <?php the_category(', ') ?> | <?php comments_number('0','1','%'); ?> <a href="#comments" class="anchorLink">Comments</a> <span class="edit"><?php edit_post_link('Edit', '| ', ''); ?></span></p></div>
    <div class="date">
        <span class="day"><?php the_time('j'); ?></span>
        <span class="month"><?php the_time('F'); ?></span>
        <span class="year"><?php the_time('Y'); ?></span>
    </div>
</div>
        
<div class="content-topper"></div> <!--  TRANSPARENT DROP SHADOW  -->
<div id="content-wrapper"> <!--  BEGIN CONTENT WRAPPER  -->
	<div id="content"> <!--  BEGIN MAIN CONTENT  -->
    
    <div <?php post_class() ?> id="post-<?php the_ID(); ?>">    
		<?php 
		$image = get_post_meta($post->ID, 'post_image', true);
		$videoimage = get_post_meta( $post->ID, "video_image", true);
		$portfoliocat = get_option('hp_portfoliocat',true);
		?>
         
         <!--If this post is in the portfolio category do these things -->
        <?php if( in_category($portfoliocat)) : ?>
        <?php if ( $image ) : ?>
        <img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "post_image", true ); ?>&amp;w=611&amp;h=310&amp;zc=1&amp;q=100" alt="<?php the_title(); ?>" class="portfolio-post-image" />
        <?php endif; ?>
        
        <?php if ( $videoimage ) : ?>
        <div id="video">
        <a class="hover-zoom-video-single" href="<?php echo get_post_meta( $post->ID, "video", true ); ?>" rel="prettyPhoto[pp_gal]"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "video_image", true ); ?>&amp;w=611&amp;h=310&amp;zc=1&amp;q=100" /></a>
        </div>
        <?php endif; ?>
                
        <?php else : ?>
        <!--If this post is in the blog category do these things -->
        <?php if ( $image ) : ?>
        <img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "post_image", true ); ?>&amp;w=225&amp;h=190&amp;zc=1&amp;q=100" alt="<?php the_title(); ?>" class="post-image" />
        <?php endif; ?>
        
        <?php endif; ?>    
                   
        <?php the_content('<p>Read the rest of this entry &raquo;</p>'); ?>
        <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
        
        <?php the_tags( '<p class="tags">Tags: ', ', ', '</p>'); ?> <!--GET THE TAGS-->      
 
        
        <?php // show author bio on blog article if checked in admin options
		$blogcat = get_option('hp_blogcat',true);
		if (in_category(''.$blogcat.'')) {
		include(TEMPLATEPATH . '/inc/bio.php');
		} ?>
        
        <?php // looking for blog category posts and displaying related if checked in admin options
		$blogcat = get_option('hp_blogcat',true);
		if (in_category(''.$blogcat.'')) {
		include(TEMPLATEPATH . '/inc/related.php');
		} ?>
		
        <?php comments_template('', true); ?> <!--DISPLAYS COMMENTS TEMPLATE-->
        
        <?php endwhile; else: ?>
        
        <p>Sorry, no posts matched your criteria.</p>
        
        <?php endif; ?>
    </div> <!--END OF POST -->  

<?php get_sidebar(); ?>

<?php get_footer(); ?>