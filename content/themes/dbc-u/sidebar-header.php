<?php
/**
 * Sidebar Header Template
 *
 * Displays any widgets for the Header dynamic sidebar if they are available.
 *
 * @package Prototype
 * @subpackage Template
 */

if ( is_active_sidebar( 'header' ) ) : ?>

	<div id="sidebar-header" class="sidebar">

		<?php dynamic_sidebar( 'header' ); ?>

	</div><!-- #sidebar-header -->

<?php endif; ?>