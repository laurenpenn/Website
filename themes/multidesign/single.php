<?php get_header(); ?>
<!-- Container Start -->
<div class="container_16">

  <!-- Top Back Theme -->
  <div id="top-back-two"></div>  
  <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
  
  <!-- Big Message -->
  <div class="grid_11 top-message-single">
	<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
    <h1><?php printf( esc_attr__( 'Posted by %s', 'twentyten' ), get_the_author() ); ?></h1>
    <p><?php the_author_meta( 'description' ); ?>    
    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
      <?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyten' ), get_the_author() ); ?>
    </a></p>
  </div>
  
  <!-- Emty Grid -->
  <div class="grid_5">
  </div>
  
  
  <div class="grid_12 blog-page" style="padding-top:20px;">
    <h1><?php the_title(); ?></h1>
    <h2 class="blog-page-space"></h2>
    
    <!-- Elements-->
      <div id="post-elements-two">
        <ul>          
		  <?php if ( get_post_meta($post->ID, 'mp3', true) ) { ?> 
          <li>
          <a class="code" href="#single-mp3" title="Open MP3 Listen!"><img src="<?php bloginfo('template_directory'); ?>/image/theme/post-music.png" alt=""></a>
          </li>
          <div style="display: none;">
            <div id="single-mp3" style="overflow:auto;">
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
    
    <!-- # Post-->
    <div id="post-blog-list-read" class="post-blog-read">
            
      <!-- Image -->
      <?php if ( get_post_meta($post->ID, 'thumb', true) ) { ?>
      <img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=300&amp;w=650&amp;zc=1&amp;a=t&amp;s=5" style="margin-bottom:2px;" alt="">
      <?php } ?> 
      
      <!-- Text -->
      <p><strong>Posted in:</strong> <?php the_category(', ') ?> | <?php comments_number(__('No Comments'), __('1 Comment'), __('% Comments')); ?> | <?php twentyten_posted_on(); ?></p>
      <p><?php the_content(); ?></p>             
    </div>
    
    <!-- Comment-->
    <div id="blog-comment" style="width:672px;">
      <?php comments_template( '', true ); ?>
    </div>
    <?php endwhile; // end of the loop. ?>
  </div>
  
  <!-- Sidebar -->
  <div class="grid_4 alpha" style="margin-top:20px;">
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