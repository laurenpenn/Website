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

	<div id="content">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>

		<?php get_template_part( 'slider-home' ); // loads slider-home.php ?>

		<div id="mydbc">
			<a class="big-button" href="https://secure.accessacs.com/access/login_guest.aspx?sn=92231">Checkout Upcoming<br /><span class="league-gothic">Events</span></a>
			<a class="big-button" href="https://secure.accessacs.com/access/login_guest.aspx?sn=92231&amp;sc=sgguest">Sign Up Today For<br /><span class="league-gothic">Small Groups</span></a>
			<a class="big-button green" href="http://dentonbible.org/admin/mydbc-life-faq/"><span class="mydbc-life-desc">Are you a member? Click to learn more</span><br /><span class="league-gothic">Join MyDBC Life</span></a>
		</div><!-- #mydbc -->
	
		<?php get_template_part( 'latest-message' ); // loads latest-message.php ?>
		
		<div id="home-ads">
		
			<a href="http://dentonbible.org/note/" class="ad-note"><img src="http://dentonbible.org/wp-content/themes/dbc/library/images/ad-notes.gif" alt="Notes from Tom Nelson" height="192" width="300" /></a>
		
			<a href="http://dentonbible.org/about-us/publications/" class="ad-publication"><img src="http://dentonbible.org/wp-content/themes/dbc/library/images/ad-publications.gif" alt="Denton Bible Church Publications" height="192" width="300" /></a>
		
		</div><!-- #home-ads -->
		
		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->
		
	<?php get_sidebar( 'home' ); ?>

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>