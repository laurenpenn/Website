<?php get_header(); ?>

<div id="page-entry-top">
	<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	<?php /* If this is a category archive */ if (is_category()) { ?>
	<h1 class="archive-title">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h1>
	<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
	<h1 class="archive-title">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h1>
	<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
	<h1 class="archive-title">Archive for <?php the_time('F jS, Y'); ?></h1>
	<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
	<h1 class="archive-title">Archive for <?php the_time('F, Y'); ?></h1>
	<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
	<h1 class="archive-title">Archive for <?php the_time('Y'); ?></h1>
	<?php /* If this is an author archive */ } elseif (is_author()) { ?>
	<h1 class="archive-title">Author Archive</h1>
	<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
	<h1 class="archive-title">Blog Archives</h1>
	<?php } ?>
</div>

<div class="content-topper"></div> <!--  TRANSPARENT DROP SHADOW  -->
<div id="content-wrapper"> <!--  BEGIN CONTENT WRAPPER  -->
	<div id="content"> <!--  BEGIN MAIN CONTENT  -->    

<div id="post-entry-wrap"> 
<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			
		<div class="post-entry">
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
		<?php
		if ( is_category() ) { // If this is a category archive
			printf("<h3>Sorry, but there aren't any posts in the %s category yet.</h3>", single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			echo("<h3>Sorry, but there aren't any posts with this date.</h3>");
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf("<h3>Sorry, but there aren't any posts by %s yet.</h3>", $userdata->display_name);
		} else {
			echo("<h3>No posts found.</h3>");
		}
		?>	
    

    <p>You can go back or try searching.</p>
    <?php get_search_form(); ?>
    
	<?php endif; ?>
    
</div> <!--END POST ENTRY WRAP-->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
