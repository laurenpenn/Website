<?php
/**
 * Sidebar Header Template
 *
 * Displays any widgets for the Header dynamic sidebar if they are available.
 *
 * @package DBC
 * @subpackage Template
 */

if ( is_active_sidebar( 'header' ) ) : ?>

	<aside id="sidebar-header" class="sidebar">

		<?php dynamic_sidebar( 'header' ); ?>

	</aside><!-- #sidebar-header -->

<?php endif; ?>