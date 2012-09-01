<?php /* Multimedia Theme*/ ?>
<!DOCTYPE html> 
<html <?php language_attributes(); ?>>
<head> 
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=940" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	?></title>

<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>

<!-- Theme Style -->
<link href="<?php bloginfo('template_directory'); ?>/css/app/general.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/style/style.css" type="text/css" />

<?php if ( function_exists( 'get_option_tree') ) :
	if( get_option_tree( 'theme_style') ) : ?>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/style/<?php get_option_tree( 'theme_style', '', 'true' ); ?>.css" type="text/css" />
<?php else : ?>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/style/default.css" type="text/css" />
<?php endif; endif; ?>

<!-- App Plugin Style -->
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<!--[if IE 7]> <style> @import url("<?php bloginfo('template_directory'); ?>/css/style/ie7.css"); </style> <![endif]-->

<!-- JS -->
<script>
/** Fon Style **/
if (document.layers) { 
document.write('<link rel=stylesheet href="<?php bloginfo('template_directory'); ?>/js/font.css">') 
} 
else { 
document.write('<link rel=stylesheet href="<?php bloginfo('template_directory'); ?>/js/font.css">') 
}
</script>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.4.2.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/general.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/function.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/filter.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/styleswitch.js"></script>
</head>

<body>
<!-- Container Start -->
<div class="container_16">
  
  <!-- Top Back -->
  <div id="top"></div>
  
  <!-- Logo -->
  <div class="grid_3 logo">
    <a href="<?php bloginfo('url'); ?>">
    <?php if ( function_exists( 'get_option_tree') ) :
	if( get_option_tree( 'logo_url') ) : ?>
	<img src="<?php get_option_tree( 'logo_url', '', 'true' ); ?>" width="160" height="58" alt="" />
	<?php else : ?>
	<img src="<?php bloginfo('template_directory'); ?>/image/theme/logo.png" alt="" />
	<?php endif; endif; ?>
    </a>
  </div>
  
  <!-- Menu -->
  <div class="grid_11">
    <?php wp_nav_menu( array( 'container_id' => 'topmenu', 'theme_location' => 'topmenu' ) ); ?>
  </div>
  <!-- Mini Button -->
  <div class="grid_2 nav-button">
  	<a href="http://www.dentonbible.org" title="Denton Bible Church Student Ministries"><img src="http://dbcstudents.org/wp-content/blogs.dir/9/files/2011/06/ministryofdbc5.png" /></a>
 
  </div>
		
  <div style="display: none;">
    <div class="sidebar-normal" id="001" style="overflow:hidden;">
	  <?php dynamic_sidebar(1); ?>
    </div>
  </div>
  
  <div style="display: none;">
    <div class="sidebar-normal" id="002" style="overflow:hidden;">
	  <?php dynamic_sidebar(2); ?>
    </div>
  </div>
  
  <div style="display: none;">
    <div class="sidebar-normal" id="003" style="overflow:hidden;">
	  <?php dynamic_sidebar(3); ?>
    </div>
  </div>	

<!-- Container End -->
</div>