<?php /*** The template for displaying Author Archive pages. */ get_header(); ?>

<!-- Container Start -->
<div class="container_16">

  <!-- Top Back Theme -->
  <div id="top-back-two"></div>
  
  <?php if ( have_posts() ) the_post(); ?>
  
  <!-- Big Message -->
  <div class="grid_11 top-message-single">
	<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
    <h1><?php printf( esc_attr__( 'Posted by %s', 'twentyten' ), get_the_author() ); ?></h1>
    <p><?php printf( __( 'Author Archives: %s', 'twentyten' ), "<span class='vcard'><a class='url fn n' href='" . get_author_posts_url( get_the_author_meta( 'ID' ) ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . get_the_author() . "</a></span>" ); ?></p>
  </div>
  
  <!-- Emty Grid -->
  <div class="grid_5">
  </div>
  

  
  <div style="margin-top:16px; margin-left:0px;" class="grid_16 post-blog-read">
      <p><?php rewind_posts(); get_template_part( 'loop', 'author' ); ?></p>
    </div>
  
</div>

<?php get_footer(); ?>
