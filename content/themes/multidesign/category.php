<?php /*** The template for displaying Category Archive pages.*/get_header(); ?>
<!-- Container Start -->
<div class="container_16">

  <!-- Top Back Theme -->
  <div id="top-back-two"></div>

  <!-- Big Message -->
  <div class="grid_11 top-message">
    <h1>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'blogcategory_title') ) : ?>
	<?php get_option_tree( 'blogcategory_title', '', 'true' ); ?>
	<?php else : ?>
    Contrary to popular belief, Lorem Ipsum is the standard simply random text.
    <?php endif; endif; ?>
    </h1>
  </div>
  
  
  <!-- Emty Grid -->
  <div class="grid_5">
  </div>
  
  <div class="grid_12 blog-page">
    <h1>
    <?php if (have_posts()) : ?>
	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<?php single_cat_title(); ?>&#8217; Category
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<?php single_tag_title(); ?>&#8217;
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<?php the_time('F jS, Y'); ?>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<?php the_time('F, Y'); ?>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<?php the_time('Y'); ?>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		Author Archive
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		Blog Archives
 	  <?php } ?>
    </h1>
    <h2 class="blog-page-space"></h2>
    
   <?php while (have_posts()) : the_post(); ?>
    
    <!-- # Post 1 -->
    <div id="post-blog-list" class="post-blog grid_6">
      <div class="post-elements">
        <ul>
          
          <?php if ( get_post_meta($post->ID, 'mp3', true) ) { ?> 
          <li>
          <a class="code" href="#<?php the_ID(); ?>" title="Open MP3 Listen!"><img src="<?php bloginfo('template_directory'); ?>/image/theme/post-music.png" alt=""></a>
          </li>
          <div style="display: none;">
            <div id="<?php the_ID(); ?>" style="overflow:auto;">
            <?php echo get_post_meta($post->ID, "mp3", $single = true); ?>
            </div>
          </div>
          <?php } ?>            
          
          <?php if ( get_post_meta($post->ID, 'video', true) ) { ?>   
          <li>
          <a class="iframe" href="<?php echo get_post_meta($post->ID, "video", $single = true); ?>" title="Watch Video!"><img src="<?php bloginfo('template_directory'); ?>/image/theme/post-video.png" alt=""></a>
          </li> 
		  <?php } ?>          
          
		  <?php if ( get_post_meta($post->ID, 'thumb', true) ) { ?>
          <li>
          <a class="picture" href="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=600&amp;w=900&amp;zc=1&amp;a=t&amp;s=5" title="Look Picture"><img src="<?php bloginfo('template_directory'); ?>/image/theme/post-camera.png" alt=""></a>
          </li>
          <?php } ?>        
        
        </ul>
      </div>
      
      <?php if ( get_post_meta($post->ID, 'thumb', true) ) { ?>
      <a href="<?php the_permalink() ?>" title="<?php the_time('j F Y') ?>"><img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=175&amp;w=300&amp;zc=1&amp;a=t&amp;s=5" alt="<?php the_title_limit( 30, '...'); ?>">
      </a>
      <?php } ?>
      
      <h1><?php the_title_limit( 30, '...'); ?></h1>
      <h2>Posted in: <?php the_category(', ') ?> | <?php comments_number(__('No Comments'), __('1 Comment'), __('% Comments')); ?></h2>
      <p><?php the_content_rss('', TRUE, '', 30); ?></p>
      <a href="<?php the_permalink() ?>" class="middle-button" style="float:left; margin:-10px 0px 0px 4px;"><span class="middle-right"></span>More</a>
      
      <span class="post-blog-dot"></span>
    </div>
    
    <?php endwhile; ?>
  
    <!-- Page Navi -->
    <div class="page-navi grid_12">
        <?php wp_pagenavi(); ?>
    </div>
    
    <?php else :

		if ( is_category() ) { // If this is a category archive
			printf("<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			echo("<h2>Sorry, but there aren't any posts with this date.</h2>");
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf("<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name);
		} else {
			echo("<h2 class='center'>No posts found.</h2>");
		}
		get_search_form();

	endif;
?>
    
  </div>
  
  <!-- Sidebar -->
  <div class="grid_4 alpha">
    <!-- Categories -->
    <div class="sidebar-categories">
      <?php dynamic_sidebar(4); ?>
    </div>
    <!-- Tags -->
    <div class="sidebar-tags">
      <h1>Tags</h1>
      <?php wp_tag_cloud('smallest=10&largest=10&number=25&orderby=name'); ?>
    </div>
    <div style="clear:both; margin-bottom:-8px;"></div>
    <!-- Archives -->
    <div class="sidebar-archives">
      <?php dynamic_sidebar(5); ?>
    </div>
  </div> 
</div>

<?php get_footer(); ?>
