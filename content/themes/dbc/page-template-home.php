<?php
/**
 * Template Name: Home
 *
 * This template is for the Home page
 *
 * @package DBC
 * @subpackage Template
 */

get_header(); ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>

	<div id="content" role="main">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>
		
		<div class="hfeed">

			<?php get_template_part( 'slider-home' ); // loads slider-home.php ?>
	
			<div id="mydbc">
				<a class="big-button" href="https://secure.accessacs.com/access/login_guest.aspx?sn=92231"><div class="inner">Register for an<br /><span class="league-gothic">Event</span></div></a>
				<a class="big-button" href="https://secure.accessacs.com/access/login_guest.aspx?sn=92231&amp;sc=sgguest"><div class="inner">Search for a<br /><span class="league-gothic">Small Group</span></div></a>
				<a class="big-button" href="<?php echo site_url(); ?>/about-us/publications/"><div class="inner">Read the latest<br /><span class="league-gothic">DBC News</span></div></a>
				<a class="big-button green" href="<?php echo site_url(); ?>/admin/mydbc-life-faq/"><div class="inner">Connect to<br /><span class="league-gothic">MyDBC Life</span></div></a>
			</div><!-- #mydbc -->
		
			<?php get_template_part( 'latest-message' ); // loads latest-message.php ?>
			
			<div class="clear"></div>
			
			<?php get_sidebar( 'home-features' ); // Loads sidebar-home-features.php ?>
						
		</div><!-- .hfeed -->
			
		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->
		
	<?php get_sidebar( 'home' ); ?>

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>
