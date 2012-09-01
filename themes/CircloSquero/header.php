<!DOCTYPE HTML>
<html <?php language_attributes(); ?>xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	
	<title><?php wp_title(' | ', 1, right); ?> <?php bloginfo('name'); ?> - <?php bloginfo('description'); ?></title>
	
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
	<meta name="description" content="<?php bloginfo('description'); ?>" />
	
	<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="screen,projection" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<script type="text/javascript" >  
	    jQuery(document).ready(function(){ 
	        jQuery('#navigation ul').superfish({ 
	            animation: {opacity:'show'},
		    autoArrows:  false,		// slide-down effect without fade-in 
	            delay:     500               // 0.5 second delay on mouseout
	        });
	    });  
	</script>
	
	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
<div class="wrapper">
	<div id="topbg">
	    <div id="logoandNav">
		<div id="logo">
		    <a href="<?php echo home_url(); ?>"> <img src="<?php echo get_option_tree( 'cs_logo' ); ?>" alt="<?php bloginfo('name'); ?>" /></a>
		</div>
		<div id="navigation">
		    <div id="navWrap"> <?php wp_nav_menu( array( 'theme_location' => 'header-menu' ) ); ?> </div>
		</div>
	    </div>
	</div>
	<div class="shadowBg">
		<?php get_template_part( 'slider' ); ?>