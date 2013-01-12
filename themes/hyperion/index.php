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
<div id="content"> <!--BEGIN MAIN CONTENT-->

<div id="post-entry-wrap"> 
<?php 
	$btitle =  stripslashes(get_option('hp_homeblog_title'));
	$bsubtitle =  stripslashes(get_option('hp_homeblog_sub_title'));
?>
<?php if ( $btitle ) : ?><h3><?php echo $btitle ?></h3><?php endif; ?>
<?php if ( $bsubtitle ) : ?><h4><?php echo $bsubtitle ?></h4><?php endif; ?>

<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>

<div class="post-entry" id="post-<?php the_ID(); ?>"> <!--  BEGIN POST ENTRY  -->
        
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
        
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
<p class="post-meta"><?php the_time('m/d/y') ?> by <?php the_author_posts_link() ?> | <?php the_category(', ') ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?> <span class="edit"><?php edit_post_link('Edit', '| ', ''); ?></span></p>
<?php the_content('Read more &rarr;'); ?>

</div>

<?php endwhile; ?>

<?php include(TEMPLATEPATH . '/inc/wp-pagenavi.php'); if(function_exists('wp_pagenavi')) { wp_pagenavi();} ?>
	
<?php else : ?>

<h2>Not Found</h2>
<p>Sorry, but you are looking for something that isn't here.</p>
<?php get_search_form(); ?>

<?php endif; ?>
</div>

<?php get_sidebar('home'); ?>


<?php get_footer(); ?>
