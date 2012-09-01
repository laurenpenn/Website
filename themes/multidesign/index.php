<?php get_header(); ?>

<!-- Container Start -->
<div class="container_16">

<?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'disable_slider') ) : ?>
<!-- Top Back Theme -->
  <div id="top-back-two"></div>
<?php else : ?>

<!-- Top Back Theme -->
<div id="top-back"></div>
<?php endif; endif; ?>

  <!-- Big Message -->
<div class="grid_11 top-message">

  </div>
  
  
  <!-- Emty Grid -->
  <div class="grid_5">
  </div>

  <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'disable_slider') ) : ?>
  <?php else : ?>
  <!-- Slide Show-->
  <div class="grid_16">
  <div id="slider-ribbon"></div>
  <div id="slider">
    <div id="slide-backs"></div>
    <div id="slides">
      <div class="slides_container">
        <?php
		if ( function_exists( 'get_option_tree' ) ) {
		$slides = get_option_tree( 'home_slider', $option_tree, false, true, -1 );
		foreach( $slides as $slide ) { ?>
          
		   <a href="<?php echo $slide['link']; ?>" title="<?php echo $slide['description'];?>"><img src="<?php echo $slide['image']; ?>" width="919" height="326" alt="<?php echo $slide['Title']; ?>" /></a>
		<?php  }
		}
		?>       
      </div>
    </div>
    </div>
  </div>
    <?php endif; endif; ?>

  
  <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'disable_hotnews') ) : ?>
  <div id="hotnews-style" class="grid_12 hotnews-homepage">
  </div>
  <?php else : ?>
  <!-- Hot News-->
<div id="hotnews-style" class="grid_12 hotnews-homepage">
    <h1><img src="<?php bloginfo('template_directory'); ?>/image/theme/hotnews.png" alt=""  /> 
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_hotnews_title') ) : ?>
	<?php get_option_tree( 'home_hotnews_title', '', 'true' ); ?>
	<?php else : ?>
    Hot News
	<?php endif; endif; ?>
    </h1>
    <ul id="news">
	  <?php 
		$category = get_option_tree('home_hotnews');
		$number = get_option_tree('home_hotnews_number');
      ?>
      <?php $showPostsInCategory = new WP_Query(); $showPostsInCategory->query('cat='. $category .'&showposts='. $number .'');  ?>
      <?php if ($showPostsInCategory->have_posts()) :?>
      <?php while ($showPostsInCategory->have_posts()) : $showPostsInCategory->the_post(); ?>
      <?php $data = get_post_meta( $post->ID, 'key', true ); ?>
      <li><?php the_title_limit( 20, '...'); ?>: <?php the_content_rss('', TRUE, '', 13); ?> <a href="<?php the_permalink() ?>" title="Read More">» more</a></li>
      <?php endwhile; endif; ?> 
    </ul>
  </div>
  <?php endif; endif; ?>
  
  <!-- Login and Signup-->
  <div class="grid_4" id="login-signup">
  <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_loginbutton_display') ) : ?>
  <?php else : ?>
  <a href="http://dbcstudents.org/contact-us/"><span class="purple-right"></span><img src="<?php bloginfo('template_directory'); ?>/image/theme/login-icon.png" alt="" class="button-icon"  /> 
  <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_loginbutton_title') ) : ?>
  <?php get_option_tree( 'home_loginbutton_title', '', 'true' ); ?>
  <?php else : ?>
  Login
  <?php endif; endif; ?>
  </a>
  <?php endif; endif; ?>
  
  <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_signupbutton_display') ) : ?>
  <?php else : ?>
  <a href="http://dbcstudents.org/contact-us/" class="signup purple-button"><span class="red-right"></span><img src="<?php bloginfo('template_directory'); ?>/image/theme/icon-signup.png" alt="" class="button-icon"  />
  <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_signupbutton_title') ) : ?>
  <?php get_option_tree( 'home_signupbutton_title', '', 'true' ); ?>
  <?php else : ?>
  Signup
  <?php endif; endif; ?>
  </a>
  <?php endif; endif; ?> 
  </div>
  

<!-- Container End -->
</div>

<!-- Dot-->
<div class="dot"></div>

<?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'disable_goarea') ) : ?>
<?php else : ?>

<!-- Top4 Start-->
<div class="container_16">
  <!-- Go!Music-->
  <div style="margin-left:2px;" class="grid_4 mini-advert">    
	<?php if ( function_exists( 'get_option_tree') ) { ?>	
    <div id="image-hover"><a href="<?php bloginfo('url'); ?>/?page_id=<?php echo get_option_tree( 'home_gomusic_page' ); ?>"><img src="<?php bloginfo('template_directory'); ?>/image/theme/spacer.png" alt="" width="220" height="110" /></a></div>
    <?php } ?>
    
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gomusic_picture') ) : ?>
	<img src="<?php get_option_tree( 'home_gomusic_picture', '', 'true' ); ?>" alt="" width="220" height="110" />
	<?php else : ?>
    <img src="<?php bloginfo('template_directory'); ?>/image/post/home-1.png" alt="" />
    <?php endif; endif; ?>
    
    <h1>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gomusic_title') ) : ?>
	<?php get_option_tree( 'home_gomusic_title', '', 'true' ); ?>
	<?php else : ?>
    <span style="color:#e73b12;">Go!</span>Music
    <?php endif; endif; ?>
    </h1>
    
    <p>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gomusic_detail') ) : ?>
	<?php get_option_tree( 'home_gomusic_detail', '', 'true' ); ?>
	<?php else : ?>
    Lorem Ipsum is simply dummy text of the printing and typesetting industry of setting dummy.
    <?php endif; endif; ?>
    </p>
    
    <a href="<?php bloginfo('url'); ?>/?page_id=<?php echo get_option_tree( 'home_gomusic_page' ); ?>" class="grey-button" style=" margin-left:5px;"><span class="grey-right"></span><img src="<?php bloginfo('template_directory'); ?>/image/theme/icon-music.png" alt="" class="button-icon"  /> 
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gomusic_button') ) : ?>
	<?php get_option_tree( 'home_gomusic_button', '', 'true' ); ?>
	<?php else : ?>
    Listen
    <?php endif; endif; ?>
    </a>
  </div>
  
  <!-- Go!Video-->
  <div class="grid_4 mini-advert">
	<?php if ( function_exists( 'get_option_tree') ) { ?>	
    <div id="image-hover-two"><a href="<?php bloginfo('url'); ?>/?page_id=<?php echo get_option_tree( 'home_govideo_page' ); ?>"><img src="<?php bloginfo('template_directory'); ?>/image/theme/spacer.png" alt="" width="220" height="110" /></a></div>
    <?php } ?>
    
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_govideo_picture') ) : ?>
	<img src="<?php get_option_tree( 'home_govideo_picture', '', 'true' ); ?>" alt="" width="220" height="110" />
	<?php else : ?>
    <img src="<?php bloginfo('template_directory'); ?>/image/post/home-2.png" alt="" />
    <?php endif; endif; ?>
    
    <h1>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_govideo_title') ) : ?>
	<?php get_option_tree( 'home_govideo_title', '', 'true' ); ?>
	<?php else : ?>
    <span style="color:#809421;">Go!</span>Video
    <?php endif; endif; ?>
    </h1>
    
    <p>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_govideo_detail') ) : ?>
	<?php get_option_tree( 'home_govideo_detail', '', 'true' ); ?>
	<?php else : ?>
    Lorem Ipsum is simply dummy text of the printing and typesetting industry of setting dummy.
    <?php endif; endif; ?>
    </p>
    
    <a href="<?php bloginfo('url'); ?>/?page_id=<?php echo get_option_tree( 'home_govideo_page' ); ?>" class="grey-button" style=" margin-left:5px;"><span class="grey-right"></span><img src="<?php bloginfo('template_directory'); ?>/image/theme/icon-video.png" alt="" class="button-icon"  /> 
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_govideo_button') ) : ?>
	<?php get_option_tree( 'home_govideo_button', '', 'true' ); ?>
	<?php else : ?>
    Watch
    <?php endif; endif; ?>
    </a>
  </div>
  
  <!-- Go!Picture-->
  <div class="grid_4 mini-advert">
	<?php if ( function_exists( 'get_option_tree') ) { ?>	
    <div id="image-hover-three"><a href="<?php bloginfo('url'); ?>/?page_id=<?php echo get_option_tree( 'home_gopicture_page' ); ?>"><img src="<?php bloginfo('template_directory'); ?>/image/theme/spacer.png" alt="" width="220" height="110" /></a></div>
    <?php } ?>
    
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gopicture_picture') ) : ?>
	<img src="<?php get_option_tree( 'home_gopicture_picture', '', 'true' ); ?>" alt="" width="220" height="110" />
	<?php else : ?>
    <img src="<?php bloginfo('template_directory'); ?>/image/post/home-3.png" alt="" />
    <?php endif; endif; ?>
    
    <h1>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gopicture_title') ) : ?>
	<?php get_option_tree( 'home_gopicture_title', '', 'true' ); ?>
	<?php else : ?>
    <span style="color:#259ae0;">Go!</span>Picture
    <?php endif; endif; ?>
    </h1>
    
    <p>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gopicture_detail') ) : ?>
	<?php get_option_tree( 'home_gopicture_detail', '', 'true' ); ?>
	<?php else : ?>
    Lorem Ipsum is simply dummy text of the printing and typesetting industry of setting dummy.
    <?php endif; endif; ?>
    </p>
    
    <a href="<?php bloginfo('url'); ?>/?page_id=<?php echo get_option_tree( 'home_gopicture_page' ); ?>" class="grey-button" style=" margin-left:5px;"><span class="grey-right"></span><img src="<?php bloginfo('template_directory'); ?>/image/theme/icon-picture.png" alt="" class="button-icon"  /> 
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gopicture_button') ) : ?>
	<?php get_option_tree( 'home_gopicture_button', '', 'true' ); ?>
	<?php else : ?>
    Look
    <?php endif; endif; ?>
    </a>
  </div>
  
  <!-- Go!Billboard-->
  <div class="grid_4 mini-advert">
	<?php if ( function_exists( 'get_option_tree') ) { ?>	
    <div id="image-hover-four"><a href="<?php bloginfo('url'); ?>/?page_id=<?php echo get_option_tree( 'home_gobillboard_page' ); ?>"><img src="<?php bloginfo('template_directory'); ?>/image/theme/spacer.png" alt="" width="220" height="110" /></a></div>
    <?php } ?>
    
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gobillboard_picture') ) : ?>
	<img src="<?php get_option_tree( 'home_gobillboard_picture', '', 'true' ); ?>" alt="" width="220" height="110" />
	<?php else : ?>
    <img src="<?php bloginfo('template_directory'); ?>/image/post/home-4.png" alt="" />
    <?php endif; endif; ?>
    
    <h1>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gobillboard_title') ) : ?>
	<?php get_option_tree( 'home_gobillboard_title', '', 'true' ); ?>
	<?php else : ?>
    <span style="color:#fba400;">Go!</span>Billboard
    <?php endif; endif; ?>
    </h1>
    
    <p>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gobillboard_detail') ) : ?>
	<?php get_option_tree( 'home_gobillboard_detail', '', 'true' ); ?>
	<?php else : ?>
    Lorem Ipsum is simply dummy text of the printing and typesetting industry of setting dummy.
    <?php endif; endif; ?>
    </p>
    
    <a href="<?php bloginfo('url'); ?>/?page_id=<?php echo get_option_tree( 'home_gobillboard_page' ); ?>" class="grey-button" style=" margin-left:5px;"><span class="grey-right"></span><img src="<?php bloginfo('template_directory'); ?>/image/theme/icon-okay.png" alt="" class="button-icon" />
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_gobillboard_button') ) : ?>
	<?php get_option_tree( 'home_gobillboard_button', '', 'true' ); ?>
	<?php else : ?>
    Review
    <?php endif; endif; ?>
    </a>
  </div>  
</div>

<!-- Dot-->
<div class="dot" style="margin-top:26px;"></div>
<?php endif; endif; ?>


<?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'latest_disable') ) : ?>
<?php else : ?>
<!-- Latest Elements and New Videos Start-->
<div class="container_16">

  <!-- Tab Menu Start -->
  <div style="margin-left:1px; margin-top:-10px;" class="grid_8 latest-elements">
    <h1>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_latestelements_title') ) : ?>
	<?php get_option_tree( 'home_latestelements_title', '', 'true' ); ?>
	<?php else : ?>
    Latest Element's
	<?php endif; endif; ?>
    </h1>
    <p>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_latestelements_detail') ) : ?>
	<?php get_option_tree( 'home_latestelements_detail', '', 'true' ); ?>
	<?php else : ?>
    Lorem Ipsum is simply dummy text of the printing.
	<?php endif; endif; ?>
    </p>
    
    <!-- Tab Title -->
    <div class='tabbed_content'>
      <div class='tabs'>
        <div class='moving_bg'>&nbsp;</div>
        
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu1_category') ) : ?>
        <span class='tab_item'><span class="tabs-right"></span>
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu1_title') ) : ?>
		<?php get_option_tree( 'home_tabmenu1_title', '', 'true' ); ?>
        <?php else : ?>
        News
        <?php endif; endif; ?>
        </span>
        <?php else : ?>
        <?php endif; endif; ?>
        
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu2_category') ) : ?>
        <span class='tab_item'><span class="tabs-right"></span>
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu2_title') ) : ?>
		<?php get_option_tree( 'home_tabmenu2_title', '', 'true' ); ?>
        <?php else : ?>
        Blog
        <?php endif; endif; ?>
        </span>
        <?php else : ?>
        <?php endif; endif; ?>
        
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu3_category') ) : ?>
        <span class='tab_item'><span class="tabs-right"></span>
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu3_title') ) : ?>
		<?php get_option_tree( 'home_tabmenu3_title', '', 'true' ); ?>
        <?php else : ?>
        Picture
        <?php endif; endif; ?>
        </span>
        <?php else : ?>
        <?php endif; endif; ?>
        
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu4_category') ) : ?>
        <span class='tab_item'><span class="tabs-right"></span>
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu4_title') ) : ?>
		<?php get_option_tree( 'home_tabmenu4_title', '', 'true' ); ?>
        <?php else : ?>
        List
        <?php endif; endif; ?>
        </span>
        <?php else : ?>
        <?php endif; endif; ?>
      </div>
    
      <!-- Tab Content -->
      <div class='slide_content'>
      <div class='tabslider'>
        
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu1_category') ) : ?>
        <!-- #1 -->
        <ul class="tab-menu">
		  <?php 
          $category = get_option_tree('home_tabmenu1_category');
          $number = get_option_tree('home_tabmenu1_post');
		  ?>
          <?php $showPostsInCategory = new WP_Query(); $showPostsInCategory->query('cat='. $category .'&showposts='. $number .'');  ?>
		  <?php if ($showPostsInCategory->have_posts()) :?>
          <?php while ($showPostsInCategory->have_posts()) : $showPostsInCategory->the_post(); ?>
          <?php $data = get_post_meta( $post->ID, 'key', true ); ?>
          <li>
          <?php if ( get_post_meta($post->ID, 'thumb', true) ) { ?>
          <a class="ajaxpicture" href="<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>">
          <img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=66&amp;w=67" alt="<?php the_title_limit( 30, '...'); ?>"/></a>
          <?php } ?>
          <span class="tab-date"><?php the_time('M j, Y') ?></span>
          <a href="<?php the_permalink() ?>" class="tab-menu-link"><?php the_title_limit( 30, '...'); ?></a>
          <p><?php the_content_rss('', TRUE, '', 25); ?></p>
          <a href="<?php the_permalink() ?>" class="middle-button" style="float:right; margin-bottom:8px; margin-top:-18px;"><span class="middle-right"></span>More</a>
          </li>
          <?php endwhile; endif; ?>
        </ul>
        <?php else : ?>
        <?php endif; endif; ?>
        
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu2_category') ) : ?>
        <!-- #2 -->
        <ul class="tab-menu">
          <?php 
          $category = get_option_tree('home_tabmenu2_category');
          $number = get_option_tree('home_tabmenu2_post');
		  ?>
          <?php $showPostsInCategory = new WP_Query(); $showPostsInCategory->query('cat='. $category .'&showposts='. $number .'');  ?>
		  <?php if ($showPostsInCategory->have_posts()) :?>
          <?php while ($showPostsInCategory->have_posts()) : $showPostsInCategory->the_post(); ?>
          <?php $data = get_post_meta( $post->ID, 'key', true ); ?>
          <li>
          <?php if ( get_post_meta($post->ID, 'thumb', true) ) { ?>
          <a class="ajaxpicture" href="<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>">
          <img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=66&amp;w=67" alt="<?php the_title_limit( 30, '...'); ?>" /></a>
          <?php } ?>
          <span class="tab-date"><?php the_time('M j, Y') ?></span>
          <a href="<?php the_permalink() ?>" class="tab-menu-link"><?php the_title_limit( 30, '...'); ?></a>
          <p><?php the_content_rss('', TRUE, '', 25); ?></p>
          <a href="<?php the_permalink() ?>" class="middle-button" style="float:right; margin-bottom:8px; margin-top:-18px;"><span class="middle-right"></span>More</a>
          </li>
          <?php endwhile; endif; ?>
        </ul>
        <?php else : ?>
        <?php endif; endif; ?>
        
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu3_category') ) : ?>     
        <!-- #3 -->
        <ul class="tab-menu-picture-list">
        <?php 
          $category = get_option_tree('home_tabmenu3_category');
          $number = get_option_tree('home_tabmenu3_post');
		  ?>
          <?php $showPostsInCategory = new WP_Query(); $showPostsInCategory->query('cat='. $category .'&showposts='. $number .'');  ?>
		  <?php if ($showPostsInCategory->have_posts()) :?>
          <?php while ($showPostsInCategory->have_posts()) : $showPostsInCategory->the_post(); ?>
          <?php $data = get_post_meta( $post->ID, 'key', true ); ?>
          <li>
          <?php if ( get_post_meta($post->ID, 'thumb', true) ) { ?>      
          <a rel="picture-album" href="<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>" title="<?php the_title_limit( 30, '...'); ?>"><img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=66&amp;w=67" alt="<?php the_title_limit( 30, '...'); ?>" /></a>
		  <?php } ?>
          </li>
          <?php endwhile; endif; ?>
        </ul>
        <?php else : ?>
        <?php endif; endif; ?>
        
        <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_tabmenu4_category') ) : ?>
        <!-- #4 -->
        <ul class="tab-menu-list">
		  <?php 
          $category = get_option_tree('home_tabmenu4_category');
          $number = get_option_tree('home_tabmenu4_post');
		  ?>
          <?php $showPostsInCategory = new WP_Query(); $showPostsInCategory->query('cat='. $category .'&showposts='. $number .'');  ?>
		  <?php if ($showPostsInCategory->have_posts()) :?>
          <?php while ($showPostsInCategory->have_posts()) : $showPostsInCategory->the_post(); ?>
          <?php $data = get_post_meta( $post->ID, 'key', true ); ?>
          <li>
          <span class="tab-date"><?php the_time('M j, Y') ?></span><a href="<?php the_permalink() ?>" class="tab-menu-link"><?php the_title_limit( 30, '...'); ?></a>
          </li>
          <?php endwhile; endif; ?>
        </ul>
        <?php else : ?>
        <?php endif; endif; ?>
        
      </div>
      </div>
      <!-- Content Finish -->    
    </div>
    <!-- Tab Finish -->  
  </div>
  <!-- Grid End -->
  
  <!-- New Video Start -->
  
  <div style="margin-left:9px; margin-top:-10px;" class="grid_8 latest-elements">
    <h1>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_newvideo_title') ) : ?>
	<?php get_option_tree( 'home_newvideo_title', '', 'true' ); ?>
	<?php else : ?>
    New Video
	<?php endif; endif; ?>
    </h1>
    <p>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_newvideo_detail') ) : ?>
	<?php get_option_tree( 'home_newvideo_detail', '', 'true' ); ?>
	<?php else : ?>
    Lorem Ipsum is simply dummy printing.
	<?php endif; endif; ?>
    </p>
    
	<?php 
	$category = get_option_tree('home_newvideo_category');
	$number = 1
	?>
    <?php $showPostsInCategory = new WP_Query(); $showPostsInCategory->query('cat='. $category .'&showposts='. $number .'');  ?>
	<?php if ($showPostsInCategory->have_posts()) :?>
    <?php while ($showPostsInCategory->have_posts()) : $showPostsInCategory->the_post(); ?>
    <?php $data = get_post_meta( $post->ID, 'key', true ); ?>   
    <div class="new-video">
      <?php if ( get_post_meta($post->ID, 'video', true) ) { ?>
      <iframe src="<?php echo get_post_meta($post->ID, "video", $single = true); ?>" width="460" height="345"> </iframe>
      <?php } ?>
      <h2 style="margin-top:3px;"><?php the_title_limit( 30, '...'); ?></h2>
      <p><?php the_content_rss('', TRUE, '', 48); ?></p>
      <a href="<?php the_permalink() ?>" class="middle-button" style="float:right; margin-bottom:8px; margin-top:3px;"><span class="middle-right"></span>More</a>
    </div>
    <?php endwhile; endif; ?>
  </div>
  <!-- End -->
</div>

<!-- Dot-->
<div class="dot" style="margin-top:26px;"></div>
<?php endif; endif; ?>



<!-- Poster Back-->
<?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'poster_disable') ) : ?>
<?php else : ?>
<div id="random-poster-back"></div>
<?php endif; endif; ?>
<div class="container_16">
  
  <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'poster_disable') ) : ?>
  <?php else : ?>
  <!-- Random Poster Start-->
  <div id="random-poster" class="grid_16">
    <h1>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_randomposter_title') ) : ?>
	<?php get_option_tree( 'home_randomposter_title', '', 'true' ); ?>
	<?php else : ?>
    Random Poster's
	<?php endif; endif; ?>
    </h1>
    <p>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_randomposter_detail') ) : ?>
	<?php get_option_tree( 'home_randomposter_detail', '', 'true' ); ?>
	<?php else : ?>
    Lorem Ipsum is simply dummy text of the printing.
	<?php endif; endif; ?>
    </p>
    
    <ul id="mycarousel" class="jcarousel-skin-tango">
    <?php 
	$category = get_option_tree('home_randomposter_category');
	$number = get_option_tree('home_randomposter_show');
	?>
    <?php $showPostsInCategory = new WP_Query(); $showPostsInCategory->query('cat='. $category .'&showposts='. $number .'');  ?>
	<?php if ($showPostsInCategory->have_posts()) :?>
    <?php while ($showPostsInCategory->have_posts()) : $showPostsInCategory->the_post(); ?>
    <?php $data = get_post_meta( $post->ID, 'key', true ); ?>
    <li>
    <?php if ( get_post_meta($post->ID, 'thumb', true) ) { ?>      
    <a rel="picture-album" href="<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>" title="<?php the_title_limit( 30, '...'); ?>">
    <img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo get_post_meta($post->ID, "thumb", $single = true); ?>&amp;h=147&amp;w=147&amp;zc=1&amp;a=t&amp;s=5" alt="<?php the_title_limit( 30, '...'); ?>" />
    </a>
    <?php } ?>
    </li>
    <?php endwhile; endif; ?>
    </ul>
  </div>
  <!-- Random Poster End-->
  <?php endif; endif; ?>
  
  <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'newusers_disable') ) : ?>
  <?php else : ?>
  <!-- New Users-->
  <div class="grid_8 new-users">
    <h1>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_newuser_title') ) : ?>
	<?php get_option_tree( 'home_newuser_title', '', 'true' ); ?>
	<?php else : ?>
    New User's
	<?php endif; endif; ?>
    </h1>
    
    <p>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_newuser_detail') ) : ?>
	<?php get_option_tree( 'home_newuser_detail', '', 'true' ); ?>
	<?php else : ?>
    Lorem Ipsum is simply dummy text of the printing.
	<?php endif; endif; ?>
    </p>
    
    <?php $usernames = $wpdb->get_results("SELECT user_nicename, user_email, user_url FROM $wpdb->users ORDER BY ID DESC LIMIT 18");
		foreach ($usernames as $username) { ?>    			

    <div class="new-users-list">
    <div class="user-mask"> <a href="#" title="<?php echo ($username->user_nicename);	?>"><img src="<?php bloginfo('template_directory'); ?>/image/theme/spacer.png" width="58" height="58" /></a></div>
    <?php echo get_avatar($username->user_email, 58);?>
    </div>
    
	<?php } ?>

  </div>
  
  <!-- New Comments-->
  <div class="grid_8 new-comments">
    <h1>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_newcomments_title') ) : ?>
	<?php get_option_tree( 'home_newcomments_title', '', 'true' ); ?>
	<?php else : ?>
    New Comment's
	<?php endif; endif; ?>
    </h1>
    
    <p>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'home_newcomments_detail') ) : ?>
	<?php get_option_tree( 'home_newcomments_detail', '', 'true' ); ?>
	<?php else : ?>
    Lorem Ipsum is simply dummy text of the printing.
	<?php endif; endif; ?>
    </p>
    
    <ul>
	<?php
    $comments = get_comments('number=5');
    foreach($comments as $comm) :
    
    $url = '<a href="'. get_permalink($comm->comment_post_ID).'#comment-'.$comm->comment_ID .'" title="'.$comm->comment_author .' | '.get_the_title($comm->comment_post_ID).'">' . $comm->comment_author . '</a>';
    ?>
    <li><span style="font-weight:bold"><?php echo $url; ?> write:</span>
    <?php echo get_the_title($comm->comment_post_ID); ?>
    </li>
	<?php endforeach; ?>    
    </ul>
  </div>
  <?php endif; endif; ?>  
</div>

<?php get_footer(); ?>