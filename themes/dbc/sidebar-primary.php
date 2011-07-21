<?php
/**
 * Primary Sidebar Template
 *
 * Displays widgets for the Primary dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package DBC
 * @subpackage Template
 */

if ( is_active_sidebar( 'primary' ) ) : ?>
	
	<?php do_atomic( 'before_sidebar_primary' ); // dbc_before_sidebar_primary ?>
 
	<div id="sidebar-primary" class="sidebar">

		<?php do_atomic( 'open_sidebar_primary' ); // dbc_open_sidebar_primary ?>

		<?php dynamic_sidebar( 'primary' ); ?>
		
		<?php if ( hybrid_get_setting( 'sidebar' ) == 'true' ) { ?>

			<?php if ( is_tax( 'note' ) || get_post_type() == 'note' ) { ?>
				
				<p class="intro-title"><img src="http://dentonbible.org/wp-content/themes/dbc/library/images/tom-square.png" class="alignleft" /> Tom Nelson</p>
				<p class="intro">Every once in a while senior pastor Tom Nelson gets a wave of inspiration he'd like to share with the church. Find them all here.</p>
	
			<?php } ?>
				
			<p>Denton Bible Church archives sermons from each Sunday and makes them available online. Watch, listen, download and share!</p>
			
			<p><a href="http://dbcmedia.org/" class="button" title="Denton Bible Media">Check Out DBC Media</a></p>
			
			<p>What better way to connect online with others in the church than Facebook?</p>
			
			<p><a href="http://facebook.com/dentonbible" class="button" title="Denton Bible on Facebook">Are you on Facebook too?</a></p>
			
		<?php } ?>
		
		</div><!-- #primary .widget-area -->		

		<?php do_atomic( 'after_sidebar_primary' ); // dbc_after_sidebar_primary ?>

	</div><!-- #sidebar-primary -->

<?php endif; ?>