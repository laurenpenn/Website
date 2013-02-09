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
		
		<?php do_atomic( 'after_sidebar_primary' ); // dbc_after_sidebar_primary ?>

	</div><!-- #sidebar-primary -->

<?php endif; ?>