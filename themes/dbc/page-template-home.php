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
	
	<!--
	<style type="text/css">
		#facebook-invite {
			background: #3B5998;
			border: 1px solid #133783;
			box-shadow: 0 0 2px rgba(0, 0, 0, 0.52);
			color: #fff;
			font: bold 0.875em/1em "lucida grande",tahoma,verdana,arial,sans-serif;
			line-height: 1em;
			padding: 10px;
			text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.6);
		}
		#facebook-invite-intro span {
			display: inline-block;
			font: normal 2.25em/1em "LeagueGothicRegular";
			margin: 5px 15px 0 0;
			text-transform: uppercase;
			vertical-align: middle;
		}
		#facebook-invite a {
			text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.6);
			border: 1px solid #133783;
			background: none;
			color: #fff;
			float: right;
			padding: 8px 15px;
			-moz-box-shadow: 0 1px 0px rgba(255, 255, 255, 0.2), inset 0 1px 4px rgba(0, 0, 0, 0.2);
			-webkit-box-shadow: 0 1px 0px rgba(255, 255, 255, 0.2), inset 0 1px 4px rgba(0, 0, 0, 0.2);
			-o-box-shadow: 0 1px 0px rgba(255, 255, 255, 0.2), inset 0 1px 4px rgba(0, 0, 0, 0.2);
			box-shadow: 0 1px 0px rgba(255, 255, 255, 0.2), inset 0 1px 4px rgba(0, 0, 0, 0.2);
			cursor: pointer;
			border-image: initial;
			border-radius: 4px;
		}
		#facebook-invite a:hover {
			background: #2B4477;
		}
	</style>
	
	<div id="facebook-invite">
		
		<div id="facebook-invite-intro">
			
			<span>Invite a friend to church!</span> A neighbor, a co-worker, or an old friend ... invite your Facebook friends to visit DBC. <a href="http://dentonbible.org/share/">Get Started</a>
				
		</div>
		
	</div>
	
	-->

	<div id="content">		

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>

		<?php get_template_part( 'slider-home' ); // loads slider-home.php ?>

		<div id="mydbc">
			<a class="big-button" href="https://secure.accessacs.com/access/login_guest.aspx?sn=92231">Register for an<br /><span class="league-gothic">Event</span></a>
			<a class="big-button" href="https://secure.accessacs.com/access/login_guest.aspx?sn=92231&amp;sc=sgguest">Search for a<br /><span class="league-gothic">Small Group</span></a>
			<a class="big-button green" href="http://dentonbible.org/admin/mydbc-life-faq/">Connect to<br /><span class="league-gothic">MyDBC Life</span> <span class="icon">MyDBC Life</span></a>
		</div><!-- #mydbc -->
	
		<?php get_template_part( 'latest-message' ); // loads latest-message.php ?>
		
		<div id="home-features">
			
			<a class="feature-dbc-cares" href="#">
				<span class="logo">DBC Cares</span>
				<span class="new"><span>NEW</span> Ministry</span>
				<span class="text">Are you, or someone you know in our church family, in need?</span>
				
			</a>
					
			<a href="http://dentonbible.org/note/" class="feature-note"><img src="http://dentonbible.org/wp-content/themes/dbc/library/images/feature-notes.gif" alt="Notes from Tom Nelson" height="127" width="197" /></a>
			
			<a href="http://dentonbible.org/calendar/" class="feature-calendar"><img src="http://dentonbible.org/wp-content/themes/dbc/library/images/feature-calendar.gif" alt="Calendar of Events" height="127" width="197" /></a>
		
			<a href="http://dentonbible.org/about-us/publications/" class="feature-publication"><img src="http://dentonbible.org/wp-content/themes/dbc/library/images/feature-publications.gif" alt="Denton Bible Church Publications" height="127" width="198" /></a>
		
		</div><!-- #home-features -->
		
		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->
		
	<?php get_sidebar( 'home' ); ?>

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>