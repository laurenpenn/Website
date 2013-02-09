<?php
/**
 * Header Sidebar Template
 *
 * The Heder Sidebar template houses the HTML used for the 'Utility: Header' 
 * widget area. It will first check if the widget area is active before displaying anything.
 *
 * @package Structure
 * @subpackage Template
 */

	if ( is_active_sidebar( 'utility-header' ) ) : ?>

		<div id="utility-header" class="sidebar utility">

			<?php dynamic_sidebar( 'utility-header' ); ?>

		</div><!-- #utility-header .utility -->

	<?php endif; ?>
