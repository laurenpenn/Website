<?php /** Template Name: Photo Playlist */ get_header(); ?>
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
	$category = get_option_tree('picture_category');
	$number = get_option_tree('picture_show');
	?>

	<?php if (have_posts()) : ?>
    <?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=$category&showposts=$number&somecat&paged=$paged"); ?> 
  
  <!-- Filter-->
    <ul class="splitter">
      <li>
        <ul>
          <li><a href="#" id="allcat" class="middle-button" ><span class="middle-right"></span>All </a></li>
          
		  <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'picture_genre') ) : ?>	      
          <?php get_option_tree( 'picture_genre', '', 'true' ); ?>
		  <?php else : ?><?php endif; endif; ?>

        </ul>
      </li>
    </ul>   
    <div style="clear:both"></div>

    <div id="listing">
    
    <?php while (have_posts()) : the_post(); ?>

    <!-- #1 -->
    <div class="discounted-item <?php echo get_post_meta($post->ID, "picture-genre", $single = true); ?> photo-playlist">		
      <a rel="picture-album" href="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=600&amp;w=900&amp;zc=1&amp;a=t&amp;s=5" title="<?php echo get_post_meta($post->ID, "artist", $single = true); ?> Picture">
      <img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=300&amp;w=190&amp;zc=1&amp;a=t&amp;s=5" alt="" class="photo-playlist-left"  />
      </a>
      <h1><?php echo get_post_meta($post->ID, "artist", $single = true); ?></h1>
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