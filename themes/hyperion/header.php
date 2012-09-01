<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    
    <!--Style sheets-->
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/style/css/<?php echo strtolower(get_option('hp_color')); ?>.css" type="text/css" media="screen" />	
    <!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/style/css/ie6.css" />
	<![endif]-->
    <!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/style/css/ie7.css" />
	<![endif]-->
    
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

    <?php wp_head(); ?>
    
    <!--  WARNING  -->
    <!--IF YOU HAVE THE WP-prettyPhoto PLUGIN INSTALLED - DO NOT MOVE ANY OF THIS JAVASCRIPT ABOVE WP_HEAD (bad things will happen)-->
    <!--  JAVASCRIPT GOODIES-->

    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/inc/js/jquery-1.4.1.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/inc/js/superfish.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/inc/js/jquery.anchor.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/inc/js/hyperion.js"></script>
    
    <!--  SLIDER SCRIPTS - S3Slider, Infinite Carousel, and easing for the fancy effects -->
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/inc/js/jqueryeasing.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/inc/js/ic.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/inc/js/s3Slider.js"></script>
    
    <!--  ACTIVATE CUFON TEXT REPLACEMENT IF ENABLED IN THEME OPTIONS  -->
    <?php $cufon = get_option('hp_cufon'); ?>
	<?php if ($cufon== "true") : ?>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/inc/js/cufon-yui.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/inc/js/liberation_sans.js"></script> 
    <script type="text/javascript">
		Cufon.replace('h1, h2, h3, h4, h5, h6, .intouch-text p, .date, .toplink, p.post-meta, .post-meta, .bottom-left, .drop-cap, .contact-button ', { hover: true, });
	</script>
    <?php endif; ?>
    
    <style type="text/css">
	<?php echo get_option('hp_custom_css'); ?>
	</style>
        
</head>
	
<body <?php body_class(); ?>>
       
<div id="headerwrap"><!--  BEGIN HEADER WRAPPER  --> 
	<div id="header">
<!--  BEGIN HEADER CONTENT --> 
    	 	
        <!--  BEGIN LOGO  -->   
        <div class="logo">
        <a href="<?php echo get_option('home'); ?>/"><img src="<?php echo get_option('hp_logo'); ?>" alt="<?php bloginfo('name'); ?>"/></a>
        </div>
        <!--  END LOGO  -->  
        
        <!--  BEGIN PAGE MENU  -->
        <?php get_template_part( 'menu', 'primary' ); // Loads the menu-primary.php template. ?>
        
	</div><!--  END HEADER CONTENT  -->
</div><!--  END HEADER WRAPPER  -->     