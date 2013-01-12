<?php /*** The template for displaying Tag Archive pages. */ get_header(); ?>

<!-- Container Start -->
<div class="container_16">

  <!-- Top Back Theme -->
  <div id="top-back-two"></div>
  
  
  <!-- Big Message -->
  <div class="grid_11 top-message-single">
    <h1 style="margin:17px 0px 0px 10px;"><?php
					printf( __( 'Tag Archives: %s', 'twentyten' ), '<span>' . single_tag_title( '', false ) . '</span>' );
				?></h1>
  </div>
  
  <!-- Emty Grid -->
  <div class="grid_5">
  </div>
  

  
  <div style="margin-top:16px; margin-left:0px;" class="grid_16 post-blog-read">
      <p><?php get_template_part( 'loop', 'tag' ); ?></p>
    </div>
  
</div>

<?php get_footer(); ?>
