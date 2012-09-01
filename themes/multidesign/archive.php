<?php /*** The template for displaying Archive pages. */ get_header(); ?>

<!-- Container Start -->
<div class="container_16">

  <!-- Top Back Theme -->
  <div id="top-back-two"></div>
  
  <?php if ( have_posts() ) the_post(); ?>
  
  <!-- Big Message -->
  <div class="grid_11 top-message-single">
    <h1 style="margin:17px 0px 0px 10px;"><?php if ( is_day() ) : ?>
				<?php printf( __( 'Daily Archives: <span>%s</span>', 'twentyten' ), get_the_date() ); ?>
<?php elseif ( is_month() ) : ?>
				<?php printf( __( 'Monthly Archives: <span>%s</span>', 'twentyten' ), get_the_date('F Y') ); ?>
<?php elseif ( is_year() ) : ?>
				<?php printf( __( 'Yearly Archives: <span>%s</span>', 'twentyten' ), get_the_date('Y') ); ?>
<?php else : ?>
				<?php _e( 'Blog Archives', 'twentyten' ); ?>
<?php endif; ?></h1>
  </div>
  
  <!-- Emty Grid -->
  <div class="grid_5">
  </div>
  

  
  <div style="margin-top:16px; margin-left:0px;" class="grid_16 post-blog-read">
      <p><?php rewind_posts();  get_template_part( 'loop', 'archive' ); ?></p>
    </div>
  
</div>

<?php get_footer(); ?>
