<?php
/**
 * Header Template
 *
 * The header template is generally used on every page of your site. Nearly all other templates call it 
 * somewhere near the top of the file. It is used mostly as an opening wrapper, which is closed with the 
 * footer.php file. It also executes key functions needed by the theme, child themes, and plugins. 
 *
 * @package Prototype
 * @subpackage Template
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<title><?php hybrid_document_title(); ?></title>

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php wp_head(); // wp_head ?>

</head>

<body class="<?php hybrid_body_class(); ?>">

	<?php do_atomic( 'open_body' ); // dbc_open_body ?>
	
	<div id="container">
	
		<div id="header">

			<?php do_atomic( 'open_header' ); // prototype_open_header ?>

			<div class="wrap">

				<div id="branding">
					<?php dbc_child_site_title(); ?>
				</div><!-- #branding -->

				<?php get_sidebar( 'header' ); // Loads the sidebar-header.php template. ?>

				<?php do_atomic( 'header' ); // prototype_header ?>

			</div><!-- .wrap -->

			<?php do_atomic( 'close_header' ); // prototype_close_header ?>

		</div><!-- #header -->

		<?php do_atomic( 'after_header' ); // prototype_after_header ?>

		<?php get_template_part( 'menu', 'primary' ); // Loads the menu-secondary.php template. ?>

		<?php do_atomic( 'before_main' ); // prototype_before_main ?>

		<div id="main">

			<div class="wrap">

			<?php do_atomic( 'open_main' ); // prototype_open_main ?>