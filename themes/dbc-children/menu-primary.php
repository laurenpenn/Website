<?php
/**
 * Primary Menu Template
 *
 * Displays the Primary Menu if it has active menu items.
 *
 * @package Prototype
 * @subpackage Template
 */

if ( has_nav_menu( 'primary' ) ) : ?>

	<?php do_atomic( 'before_menu_primary' ); // prototype_before_menu_primary ?>

	<div id="menu-primary" class="menu-container">

		<div class="wrap">

			<?php do_atomic( 'open_menu_primary' ); // prototype_open_menu_primary ?>

			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container_class' => 'menu', 'menu_class' => '', 'menu_id' => 'menu-primary-items', 'fallback_cb' => '' ) ); ?>

			<?php do_atomic( 'close_menu_primary' ); // prototype_close_menu_primary ?>

		</div>

	</div><!-- #menu-primary .menu-container -->

	<?php do_atomic( 'after_menu_primary' ); // prototype_after_menu_primary ?>

<?php endif; ?>