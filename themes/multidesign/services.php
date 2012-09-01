<?php /** Template Name: Services */ get_header(); ?>
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
    <?php the_title_limit( 30, '...'); ?>
    </h1>
    <h2 class="blog-page-space"></h2>
  </div>
  
  <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
  <!-- Detail -->
  <div style=" margin:-4px 0px 0px 0px; width:970px;" class="grid_16">
    <?php the_content(); ?>
  </div>
  
  
  <?php endwhile; ?>
  
</div>

<?php get_footer(); ?>