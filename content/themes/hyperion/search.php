<?php get_header(); ?>
    
<div id="page-entry-top">
    <h1>Search Results</h1>
</div>    
            
<div class="content-topper"></div> <!--  TRANSPARENT DROP SHADOW  -->
<div id="content-wrapper"> <!--  BEGIN CONTENT WRAPPER  -->
	<div id="content"> <!--  BEGIN MAIN CONTENT  -->

<div id="post-entry-wrap">    
<?php if (have_posts()) : ?>        
		<?php while (have_posts()) : the_post(); ?>

   	<div class="post-entry" id="post-<?php the_ID(); ?>">
		<?php 
		$image = get_post_meta($post->ID, 'post_image', true);
		$videoimage = get_post_meta( $post->ID, "video_image", true);
		?>        
		<?php if ( $videoimage ) : ?>
        <a href="<?php the_permalink() ?>" rel="bookmark"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "video_image", true ); ?>&amp;w=200&amp;h=150&amp;zc=1&amp;q=100" alt="<?php the_title(); ?>" class="post-thumb" /></a>
        <?php endif; ?>
                
        <?php if ( $image ) : ?>
        <a href="<?php the_permalink() ?>" rel="bookmark"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "post_image", true ); ?>&amp;w=200&amp;h=150&amp;zc=1&amp;q=100" alt="<?php the_title(); ?>" class="post-thumb" /></a>
        <?php endif; ?>
        
        <h2><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
        <p class="post-meta"><?php the_time('m/d/y') ?> by <?php the_author_posts_link() ?> | <?php the_category(', ') ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?> <span class="edit"><?php edit_post_link('Edit', '| ', ''); ?></span></p>
		<?php the_excerpt(''); ?>
      
      </div><!--end post-->

		<?php endwhile; ?>        

<?php include(TEMPLATEPATH . '/inc/wp-pagenavi.php'); if(function_exists('wp_pagenavi')) { wp_pagenavi();} ?>
		
	<?php else : ?>
    
    <h2>No posts found. Try a different search?</h2>
		<?php get_search_form(); ?>

	<?php endif; ?>
</div>    


<?php get_sidebar(); ?>
<?php get_footer(); ?>
