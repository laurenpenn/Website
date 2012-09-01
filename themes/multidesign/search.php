<?php /*** The template for displaying Archive pages. */ get_header(); ?>

<!-- Container Start -->
<div class="container_16">

  <!-- Top Back Theme -->
  <div id="top-back-two"></div>
  
  <?php if ( have_posts() ) : ?>
  
  <!-- Big Message -->
  <div class="grid_11 top-message-single">
    <h1 style="margin:17px 0px 0px 10px;"><?php printf( __( 'Search Results for: %s', 'twentyten' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
  </div>
  
  <!-- Emty Grid -->
  <div class="grid_5">
  </div>
  

  
  <div style="margin-top:16px; margin-left:0px;" class="grid_16 post-blog-read">
      <p><?php get_template_part( 'loop', 'search' ); ?></p>
      <?php else : ?>
      <h2><?php _e( 'Nothing Found', 'twentyten' ); ?></h2>
      <p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'twentyten' ); ?></p>
	  <?php get_search_form(); ?>
      <?php endif; ?>
    </div>
  
</div>

<?php get_footer(); ?>
