<?php
/**
 * After Singular Sidebar Template
 *
 * Displays any widgets for the After Singular dynamic sidebar if they are available.
 *
 * @package Prototype
 * @subpackage Template
 */

if ( is_active_sidebar( 'after-singular' ) ) : ?>

	<aside id="sidebar-after-singular" class="sidebar">

		<?php dynamic_sidebar( 'after-singular' ); ?>

	</aside><!-- #sidebar-after-singular -->

<?php endif; ?>