<?php /** Template Name: Blog Full Style Four */ get_header(); ?>
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
  
  <div class="grid_16 blog-page">
     <h1>
    <?php if ( get_post_meta($post->ID, 'title', true) ) { ?>
	<?php echo get_post_meta($post->ID, "title", $single = true); ?>
    <?php } ?>
    </h1>
    <h2 class="blog-page-space"></h2>
  </div>
  
  <div class="grid_16 list-page">
   <?php 
	$category = get_option_tree('blog_category');
	$number = get_option_tree('blog_show');
	?>

	<?php if (have_posts()) : ?>
    <?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=$category&showposts=$number&somecat&paged=$paged"); ?>
  <!-- Filter-->
    <ul class="splitter">
      <li>
        <ul>
          <li><a href="?page_id=<?php get_option_tree( 'rightpage_sidebar', '', 'true' ); ?>"  id="allpage-login-top"  title="Open Sidebar Box" class="middle-button" ><span class="middle-right"></span>Categories </a></li>
          <li><a href="?page_id=<?php get_option_tree( 'leftpage_sidebar', '', 'true' ); ?>"  id="allpage-signup-top"  title="Open Sidebar Box" class="middle-button" ><span class="middle-right"></span>Tags </a></li>
        </ul>
      </li>
    </ul>   
    <div style="clear:both"></div>

    <div id="listing">
    
    <?php while (have_posts()) : the_post(); ?>

    <!-- #1 -->
    <div class="discounted-item portfolio-four">		
       <a class="picture" href="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=600&amp;w=900&amp;zc=1&amp;a=t&amp;s=5" title="Look Picture"><img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=190&amp;w=190&amp;zc=1&amp;a=t&amp;s=5" alt="<?php the_title_limit( 30, '...'); ?>" class="portfolio-four-left"  /></a>
      <h1><?php the_title_limit( 30, '...'); ?></h1>
      <p><?php the_content_rss('', TRUE, '', 30); ?></p>
      <a href="<?php the_permalink() ?>" class="middle-button" style="float:left; margin:-10px 0px 0px 4px;"> <span class="middle-right"></span>More</a>      	
	</div>    
    <?php endwhile;?>
   
    </div>
    
    <div style="clear:both"></div>
    <!-- Page Navi -->
    <div class="page-navi" style="margin:0px 0px 0px -28px;">
      <?php wp_pagenavi(); ?>
    </div>
    <?php else : ?>
    <?php endif; ?>   
  </div>
</div>

<?php get_footer(); ?>
