<?php /** Template Name: Blog Style One */ get_header(); ?>
<!-- Container Start -->
<div class="container_16">

  <!-- Top Back Theme -->
  <div id="top-back-two"></div>

  <!-- Big Message -->
  <div class="grid_11 top-message">
    <h1>
	<?php if ( get_post_meta($post->ID, 'bigtitle', true) ) { ?>
	<?php echo get_post_meta($post->ID, "bigtitle", $single = true); ?>
    <?php } ?>
    </h1>
  </div>
  
  <!-- Emty Grid -->
  <div class="grid_5">
  </div>
  
  <div class="grid_12 blog-page">
    <h1>
    <?php if ( get_post_meta($post->ID, 'title', true) ) { ?>
	<?php echo get_post_meta($post->ID, "title", $single = true); ?>
    <?php } ?>
    </h1>
    <h2 class="blog-page-space"></h2>
    
    <?php 
	$category = get_option_tree('blog_category');
	$number = get_option_tree('blog_show');
	?>

	<?php if (have_posts()) : ?>
    <?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=$category&showposts=$number&somecat&paged=$paged"); ?>
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
    
    <?php else : ?>
	<?php endif; ?>
    
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
