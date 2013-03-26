<!DOCTYPE html>

<html <?php language_attributes(); ?>><head>

        <meta charset="utf-8">

        <title><?php wp_title('|',true,'right'); ?><?php bloginfo('name'); ?></title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="<?php bloginfo('description'); ?>" />  

        <meta name="keywords" content="<?php bloginfo('name'); ?>" />

        

        <?php  global $data; ?>

        

        <?php

		$title = get_the_title();

		if ( $title == "Home Page 2 Example") { $data['revolution_homepage'] = true; };

		if ( $title == "Home Page 4 Example") { $data['revolution_homepage'] = true; };

		if ( $title == "Home Page 5 Example") { $data['revolution_homepage'] = true; };

		?>

        <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />

        <!-- Le styles -->

        <?php if ($data['theme_layout'] == "Boxed and 1170px container") { ?>

            <link href="<?php echo get_template_directory_uri(); ?>/assets/css/wide_layout.css" rel="stylesheet">

        <?php } ?>

         <?php if ($data['theme_layout'] == "Fullwidth and 1170px container") { ?>

            <link href="<?php echo get_template_directory_uri(); ?>/assets/css/wide_layout.css" rel="stylesheet">

        <?php } ?>

        <?php include_once('inc/style.php'); ?> <!-- Load Custom CSS -->

        <!-- Le fav and touch icons -->

        

        <link rel="shortcut icon" href="<?php echo stripslashes($data['header_favicon']) ?>">

		<?php wp_enqueue_style('prepared-styles',get_template_directory_uri().'/admin/layouts/'.$data['alt_stylesheet'],'','','all'); ?>

        <link href="<?php echo get_template_directory_uri().'/admin/layouts/'.$data['alt_stylesheet']?>" type="text/css" media="screen" rel="stylesheet">

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->

        <!--[if lt IE 9]>

          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>

        <![endif]-->

        <!--[if lte IE 8]>

    	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/css/ie.css" />

		<![endif]-->

		<?php wp_head(); ?>



        <?php wp_link_pages(); ?>



    </head>



	<body  <?php body_class(); ?>>

    

	

    <div class="wide_cont">

    <!--TOP-->

    <?php if($data['top_line_show'] == true ) { ?>

  	<div class="top_line">

    	<div class="container">

        	<div class="row">

            	<div class="span6">

					<p class="feed"><?php echo stripslashes($data['header_top_line']) ?></p>

    			</div>

                <div class="span6 soc_icons">

                	<?php if($data['header_social_yt']) { ?>

                		<a href="<?php echo stripslashes($data['header_social_yt']) ?>" target="_blank"><div class="icon_youtube"></div></a>

                    <?php } ?>

					<?php if($data['header_social_fl']) { ?>

                    	<a href="<?php echo stripslashes($data['header_social_fl']) ?>" target="_blank"><div class="icon_flickr"></div></a>

                    <?php } ?>

					<?php if($data['header_social_dr']) { ?>

                    	<a href="<?php echo stripslashes($data['header_social_dr']) ?>" target="_blank"><div class="icon_dribbble"></div></a>

                    <?php } ?>

					<?php if($data['header_social_g']) { ?>

                    	<a href="<?php echo stripslashes($data['header_social_g']) ?>" target="_blank"><div class="icon_google"></div></a>

                    <?php } ?>

					<?php if($data['header_social_fb']) { ?>

                    	<a href="<?php echo stripslashes($data['header_social_fb']) ?>" target="_blank"><div class="icon_facebook"></div></a>

                    <?php } ?>

                    <?php if($data['header_social_in']) { ?>

                    	<a href="<?php echo stripslashes($data['header_social_in']) ?>" target="_blank"><div class="icon_in"></div></a>

                    <?php } ?>

                    <?php if($data['header_social_pi']) { ?>

                    	<a href="<?php echo stripslashes($data['header_social_pi']) ?>" target="_blank"><div class="icon_pi"></div></a>

                    <?php } ?>

                    <?php if($data['header_social_tw']) { ?>

                    	<a href="<?php echo stripslashes($data['header_social_tw']) ?>" target="_blank"><div class="icon_t"></div></a>

                    <?php } ?>

                </div>

    		</div>

    	</div>

    </div>

    <!--/TOP-->

    <?php } ?>

    

    

    <!--PAGE HEAD-->

    <div class="page_head" <?php if($data['tag_line_show'] == false ) { ?>style="border-bottom: 1px solid <?php echo $data['tag_line_border_top']; ?>" <?php } ?>>

    	<div class="container">

        	<div class="row">

            	<div class="span3">

                	<div class="logo">

                    	<a href="<?php echo home_url(); ?>"> <img src="<?php echo stripslashes($data['header_logo']) ?>" alt="<?php bloginfo('name'); ?>" /></a>

                    </div>

                </div>

                <div class="span9">

                	<nav>

                    	<?php wp_nav_menu( array('theme_location' => 'main_menu', 'menu_class' => 'menu')); ?>

                    </nav>

                </div>

    		</div>

    	</div>

    </div>

    <!--/PAGE HEAD-->

    

    <?php if(($data['tag_line_show'] == true) & ($data['tag_line_position'] == "Before Slider")) { ?>

    <!--WELCOME AREA-->

    <div class="tag_line">

        <div class="container">

            <div class="row">

                <div class="span12">

                    <div class="welcome">

                        <h3><?php if (is_front_page()){ ?><?php echo stripslashes($data['header_tagline']) ?><?php } else { ?><?php if (!(is_archive()) & (!(is_search()))) { ?> <span class="colored"><?php the_title(); ?></span><?php if (get_post_meta($post->ID, 'description', true)) { ?><span class="colored">:</span><?php } ?> <?php echo get_post_meta($post->ID, 'description', 1); ?><?php if (get_post_meta($post->ID, 'port-descr', true)) { ?><span class="colored">:</span><?php } ?> <?php echo get_post_meta($post->ID, 'port-descr', 1); ?> <?php } ?><?php } ?><?php if ((is_archive() & (!(is_post_type_archive('portfolio-type'))))) { ?><span class="colored"><?php echo stripslashes($data['blog_archive_title']) ?></span><?php } ?><?php if (is_search()) { ?><span class="colored"><?php _e("Search Results:","commander"); ?></span> <?php the_search_query(); ?><?php } ?><?php if (is_404('404.php')){?><span class="colored"><?php _e("404 Error","commander"); ?></span> <?php } ?><?php if ((is_post_type_archive('portfolio-type'))) { ?><span class="colored"><?php the_title(); ?></span><?php if (get_post_meta($post->ID, 'description', true)) { ?><span class="colored">:</span><?php } ?><?php echo get_post_meta($post->ID, 'description', 1); ?><?php if (get_post_meta($post->ID, 'port-descr', true)) { ?><span class="colored">:</span><?php } ?> <?php echo get_post_meta($post->ID, 'port-descr', 1); ?><?php } ?></h3>

						<?php if($data['breadcumbs'] == true ) { ?>

						<?php if (!is_front_page()){ ?>

							<?php kama_breadcrumbs(); ?>

                        <?php } ?>

                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <?php } ?>

    

	<?php include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); ?>

    <?php if (is_plugin_active('revslider/revslider.php')) { ?>



    <?php if($data['revolution_homepage'] == true ) { ?>

	<?php $title = get_the_title();?>

	<?php if (is_front_page() || $title == "Home Page 2 Example" || $title == "Home Page 3 Example" || $title == "Home Page 4 Example"  || $title == "Home Page 5 Example"){ ?><?php putRevSlider("main_slider") ?><?php }?>

    <?php } ?>



	<?php } ?>

    

    <?php if(($data['tag_line_show'] == true) & ($data['tag_line_position'] == "After Slider")) { ?>

    <!--WELCOME AREA-->

    <div class="tag_line">

        <div class="container">

            <div class="row">

                <div class="span12">

                    <div class="welcome">

                        <h3><?php if (is_front_page()){ ?><?php echo stripslashes($data['header_tagline']) ?><?php } else { ?><?php if (!(is_archive()) & (!(is_search()))) { ?> <span class="colored"><?php the_title(); ?></span><?php if (get_post_meta($post->ID, 'description', true)) { ?><span class="colored">:</span><?php } ?> <?php echo get_post_meta($post->ID, 'description', 1); ?><?php if (get_post_meta($post->ID, 'port-descr', true)) { ?><span class="colored">:</span><?php } ?> <?php echo get_post_meta($post->ID, 'port-descr', 1); ?> <?php } ?><?php } ?><?php if ((is_archive() & (!(is_post_type_archive('portfolio-type'))))) { ?><span class="colored"><?php echo stripslashes($data['blog_archive_title']) ?></span><?php } ?><?php if (is_search()) { ?><span class="colored"><?php _e("Search Results:","commander"); ?></span> <?php the_search_query(); ?><?php } ?><?php if (is_404('404.php')){?><span class="colored"><?php _e("404 Error","commander"); ?></span> <?php } ?><?php if ((is_post_type_archive('portfolio-type'))) { ?><span class="colored"><?php the_title(); ?></span><?php if (get_post_meta($post->ID, 'description', true)) { ?><span class="colored">:</span><?php } ?><?php echo get_post_meta($post->ID, 'description', 1); ?><?php if (get_post_meta($post->ID, 'port-descr', true)) { ?><span class="colored">:</span><?php } ?> <?php echo get_post_meta($post->ID, 'port-descr', 1); ?><?php } ?></h3>

                    	<?php if($data['breadcumbs'] == true ) { ?>

						<?php if (!is_front_page()){ ?>

							<?php kama_breadcrumbs(); ?>

                        <?php } ?>

                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <?php } ?>