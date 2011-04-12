<?php
/**
 * Header Template
 *
 * The header template is generally used on every page of your site. Nearly all other templates call it 
 * somewhere near the top of the file. It is used mostly as an opening wrapper, which is closed with the 
 * footer.php file. It also executes key functions needed by the theme, child themes, and plugins. 
 *
 * @package DBC
 * @subpackage Template
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<meta property="fb:page_id" content="206268862487" />
<title><?php hybrid_document_title(); ?></title>

<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="all" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php wp_head(); // wp_head ?>
		
</head>

<body class="<?php hybrid_body_class(); ?>">

	<?php do_atomic( 'open_body' ); // dbc_children_open_body ?>
	
	<div id="container">
	
		<div id="header">

			<?php do_atomic( 'open_header' ); // dbc_children_open_header ?>

			<div class="wrap">

				<div id="branding">
					<?php hybrid_site_title(); ?>
				</div><!-- #branding -->

				<?php get_sidebar( 'header' ); // Loads the sidebar-header.php template. ?>
				
				<?php get_template_part( 'menu', 'primary' ); // Loads the menu-secondary.php template. ?>
				
				<div class="clear"></div>
				
				<?php do_atomic( 'header' ); // dbc_children_header ?>

			</div><!-- .wrap -->

			<?php do_atomic( 'close_header' ); // dbc_children_close_header ?>

		</div><!-- #header -->

		<?php do_atomic( 'after_header' ); // dbc_children_after_header ?>

		

		<?php do_atomic( 'before_main' ); // dbc_children_before_main ?>

		<div id="main">

			<div class="wrap">

			<?php do_atomic( 'open_main' ); // dbc_children_open_main ?>