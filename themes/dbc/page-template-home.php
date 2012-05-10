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
			<a class="big-button" href="#">Sign up for the<br /><span class="league-gothic">Newsletter</span></a>
			<a class="big-button green" href="<?php echo site_url(); ?>/admin/mydbc-life-faq/">Connect to<br /><span class="icon">MyDBC Life</span><span class="league-gothic">MyDBC Life</span></a>
		</div><!-- #mydbc -->
	
		<?php get_template_part( 'latest-message' ); // loads latest-message.php ?>
		
		<div class="clear"></div>
		
		<div id="home-features">
			
			<div class="features">

				<a href="<?php echo site_url(); ?>/care/dbc-cares/" class="feature" id="feature-dbccares">
					<div class="feature-inner">
						<h4 class="widget-title">DBC Cares</h4>
						<p><img src="<?php echo get_template_directory_uri(); ?>/images/dbc-cares-logo.png" class="alignright" />Are you, or someone you know in our church family, in need?</p>
					</div>
				</a>
				
				<a href="<?php echo site_url(); ?>/notes/" class="feature" id="feature-notes">
					<div class="feature-inner">
						<h4 class="widget-title">Notes from Tom's  desk</h4>
						<p><img src="<?php echo get_template_directory_uri(); ?>/images/feature-notes-tom.png" class="alignright" style="margin: 0 0 0.75em 0.75em;" />Occasionally, senior pastor Tom Nelson has a letter to share with the church body</p>
					</div>
				</a>
							
				<a href="<?php echo site_url(); ?>/about-us/calendar/" class="feature" id="feature-calendar">
					<div class="feature-inner">
						<h4 class="widget-title">Calendar of Events</h4>
						<p><img src="<?php echo get_template_directory_uri(); ?>/images/feature-calendar.png" class="alignright" width="68" />Find out what's going on this weekend or other ways you can get involved</p>
					</div>
				</a>
				
			</div>
			
		</div><!-- #home-features -->
				
		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->
		
	<?php get_sidebar( 'home' ); ?>

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>