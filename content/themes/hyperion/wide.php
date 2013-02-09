<?php
/*
Template Name: Wide
*/
?>

<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
<div id="page-entry-top">
    <h1><?php the_title(); ?></h1>
</div>    
            
<div class="content-topper"></div> <!--  TRANSPARENT DROP SHADOW  -->
<div id="content-wrapper"> <!--  BEGIN CONTENT WRAPPER  -->
	<div id="content"> <!--  BEGIN MAIN CONTENT  -->
    
    	<div class="wide-page" id="post-<?php the_ID(); ?>">  <!--  BEGIN POST ENTRY  -->
		<?php 
		$image = get_post_meta($post->ID, 'post_image', true);
		?>        
        <?php if ( $image ) : ?>
        <img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "post_image", true ); ?>&amp;w=225&amp;h=190&amp;zc=1&amp;q=100" alt="<?php the_title(); ?>" class="post-image" />
        <?php endif; ?>

            <?php the_content('<p>Read the rest of this page &raquo;</p>'); ?>
            <?php wp_link_pages(array('before' => '<p>Pages: ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
            <?php endwhile; endif; ?>
            
            <?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
        </div> <!-- END POST ENTRY -->

<?php get_footer(); ?>
