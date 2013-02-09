<?php /** Template Name: Music Playlist */ get_header(); ?>
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
	$category = get_option_tree('music_category');
	$number = get_option_tree('music_show');
	?>

	<?php if (have_posts()) : ?>
    <?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=$category&showposts=$number&somecat&paged=$paged"); ?> 
  <!-- Filter-->
    <ul class="splitter">
      <li>
        <ul>
          <li><a href="#" id="allcat" class="middle-button" ><span class="middle-right"></span>All </a></li>
          
		  <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'music_genre') ) : ?>	      
          <?php get_option_tree( 'music_genre', '', 'true' ); ?>
		  <?php else : ?><?php endif; endif; ?>

        </ul>
      </li>
    </ul>   
    <div style="clear:both"></div>

    <div id="listing">
    
    <?php while (have_posts()) : the_post(); ?>

    <!-- #1 -->
    <div class="discounted-item <?php echo get_post_meta($post->ID, "music-genre", $single = true); ?> music-playlist">		
      <img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "cover", $single = true); ?>&amp;h=38&amp;w=38&amp;zc=1&amp;a=t&amp;s=5" alt="" class="music-playlist-left"  />
      <h1><?php echo get_post_meta($post->ID, "artist", $single = true); ?></h1>
      <p><?php echo get_post_meta($post->ID, "song", $single = true); ?></p>
      <a class="code" href="#<?php the_ID(); ?>" title="<?php echo get_post_meta($post->ID, "artist", $single = true); ?>"><img src="<?php bloginfo('template_directory'); ?>/image/theme/play.png" alt="" class="music-playlist-right"  /></a>
      

      <div style="display: none;">
        <div id="<?php the_ID(); ?>" style="overflow:auto;">
          <?php echo get_post_meta($post->ID, "mp3", $single = true); ?>
         </div>
       </div>

        	
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
