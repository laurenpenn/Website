<?php
/**
 * Home Features Sidebar Template
 *
 * Displays widgets for the Home Features dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package DBC
 * @subpackage Template
 */

if ( is_active_sidebar( 'home-features' ) ) : ?>
	 
	<aside id="sidebar-home-features" class="sidebar">
		
		<div class="features">

			<?php dynamic_sidebar( 'home-features' ); ?>
			
		</div><!-- .features -->

	</aside><!-- #sidebar-home-features -->

<?php endif; ?>