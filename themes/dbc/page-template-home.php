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
	
	<?php get_template_part( 'facebook-invite' ); // loads facebook-invite.php ?>

	<div id="content" role="main">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>

		<?php get_template_part( 'slider-home' ); // loads slider-home.php ?>

		<div id="mydbc">
			<a class="big-button" href="https://secure.accessacs.com/access/login_guest.aspx?sn=92231">Register for an<br /><span class="league-gothic">Event</span></a>
			<a class="big-button" href="https://secure.accessacs.com/access/login_guest.aspx?sn=92231&amp;sc=sgguest">Search for a<br /><span class="league-gothic">Small Group</span></a>
			<a class="big-button green" href="http://dentonbible.org/admin/mydbc-life-faq/">Connect to<br /><span class="league-gothic">MyDBC Life</span> <span class="icon">MyDBC Life</span></a>
		</div><!-- #mydbc -->
	
		<?php get_template_part( 'latest-message' ); // loads latest-message.php ?>
		
		<div id="home-features">
			
			<a class="feature-dbc-cares" href="http://dentonbible.org/care/dbc-cares/">
				<span class="logo">DBC Cares</span>
				<span class="new"><span>NEW</span> Ministry</span>
				<span class="text">Are you, or someone you know in our church family, in need?</span>
				
			</a>
					
			<a href="http://dentonbible.org/note/" class="feature-note"><img src="http://dentonbible.org/wp-content/themes/dbc/images/feature-notes.gif" alt="Notes from Tom Nelson" height="127" width="197" /></a>
			
			<a href="http://dentonbible.org/about-us/calendar/" class="feature-calendar"><img src="http://dentonbible.org/wp-content/themes/dbc/images/feature-calendar.gif" alt="Calendar of Events" height="127" width="197" /></a>
		
			<a href="http://dentonbible.org/about-us/publications/" class="feature-publication"><img src="http://dentonbible.org/wp-content/themes/dbc/images/feature-publications.gif" alt="Denton Bible Church Publications" height="127" width="198" /></a>
		
		</div><!-- #home-features -->
		
		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->
		
	<?php get_sidebar( 'home' ); ?>

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>