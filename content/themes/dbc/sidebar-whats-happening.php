<?php
/**
 * What's Happening Sidebar Aside Template
 *
 * The What's Happening template houses the HTML used for the 'Home Sidebar' widget area.
 * It will first check if the widget area is active before displaying anything.
 * @link http://themehybrid.com/themes/hybrid/widget-areas
 *
 * @package Hybrid
 * @subpackage Template
 */

if ( is_active_sidebar( 'whats-happening' ) ) : ?>

	<aside id="whats-happening" class="sidebar aside">

		<h3>What's Happening</h3>		
		
		<?php dynamic_sidebar( 'whats-happening' ); ?>

	</aside><!-- #whats-happening .aside -->

<?php endif; ?>