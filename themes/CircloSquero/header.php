<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes() ?>>
<head>

<title><?php wp_title(' | ', 1, right); ?> <?php bloginfo('name'); ?> - <?php bloginfo('description'); ?></title>
<meta name="description" content="<?php bloginfo('description'); ?>" />

<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen,projection" />


<?php wp_head(); ?>

<?php if (get_option_tree( 'cs_font' ) == 'Sansation') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/fonts_sansation.css" />
<?php } elseif (get_option_tree( 'cs_font' ) == 'Luxi Sans') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/fonts_luxi.css" />
<?php } elseif (get_option_tree( 'cs_font' ) == 'Droid Serif') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/fonts_droid.css" />
<?php } elseif (get_option_tree( 'cs_font' ) == 'Liberation Sans') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/fonts_liberation.css" />
<?php } elseif (get_option_tree( 'cs_font' ) == 'Nobile') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/fonts_nobile.css" />
<?php }; ?>

<?php if (get_option_tree( 'cs_style' ) == 'Gray-Blue') { ?>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/gray_blue.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'White') { ?>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/white_blue.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Pink') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/pink.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_pink.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Soft Red') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/red_soft.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_red_soft.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Red') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/red.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_red.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Yellow') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/yellow.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_yellow.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Orange') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/orange.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_orange.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Brown') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/brown.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_brown.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Soft Brown') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/brown_soft.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_brown_soft.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Soft Green') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/green_soft.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_green_soft.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Green') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/green.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_green.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Deep Blue') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/blue_deep.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_blue_deep.js"></script>
<?php } elseif (get_option_tree( 'cs_style' ) == 'Blue') { ?>    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/blue.css" />
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/circlosquero_effects_blue.js"></script>
<?php }; ?>

<?php if (get_option_tree( 'cs_bg' ) == 'Noise') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg0.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Noise Dark') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg1.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 1') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg2.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 1 Dark') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg3.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 2') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg4.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 2 Dark') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg5.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 3') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg6.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 3 Dark') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg7.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 4') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg8.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 4 Dark') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg9.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 5') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg10.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 5 Dark') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg11.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 6') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg12.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 6 Dark') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg13.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 7') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg14.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 7 Dark') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg15.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 8') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg16.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 8 Dark') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg17.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 9') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg18.css" />
<?php } elseif (get_option_tree( 'cs_bg' ) == 'Image 9 Dark') { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/backgrounds/bg19.css" />
<?php }; ?>

<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/nivo_slider/nivo-slider.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/scripts/prettyPhoto.css" />



<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/superfish.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/twitter-rss-with-rt.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/featuredworks.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/supersubs.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/hoverIntent.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/jquery.backgroundPosition.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/jquery.color.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts/jquery.prettyPhoto.js"  charset="utf-8"></script>

<?php if ( function_exists('show_nivo_slider_css') ) { show_nivo_slider_css(); } ?>

<script type="text/javascript" >  
    jQuery(document).ready(function(){ 
        jQuery('#navigation ul').superfish({ 
            animation: {opacity:'show'},
	    autoArrows:  false,		// slide-down effect without fade-in 
            delay:     500               // 0.5 second delay on mouseout
        });
    });  
</script>

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/iesucks.css" />
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/nivo-inline.css" />
</head>

<body>
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
	<?php if (is_home()) { ?>    
	    <div id="sliderMy">
		<div id="<?php if (get_option_tree( 'homepage_slider' ) == 'Piecemaker') { echo 'sliderMyPiece';} else {echo 'sliderMy';}; ?>">
		<?php if (get_option_tree( 'homepage_slider' ) == 'Nivo Slider') { ?>
		    <?php if ( function_exists('show_nivo_slider_js') ) { show_nivo_slider_js(); } ?>
		<?php } elseif (get_option_tree( 'homepage_slider' ) == 'Piecemaker') { ?>
		    <?php if (function_exists(display_the_piecemaker())) display_the_piecemaker(); ?>
		<?php } else { ?>
		<img src="<?php echo get_option_tree( 'homepage_image' ); ?>" alt="<?php bloginfo('name'); ?>" />
		<?php }; ?>
		</div>
	    </div>
	<?php ;} ?>