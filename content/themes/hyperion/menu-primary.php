<?php
/**
 * Primary Menu Template
 *
 * Displays the Primary Menu if it has active menu items.
 *
 * @package Hyperion
 * @subpackage Template
 */

if ( has_nav_menu( 'primary' ) ) : ?>

	<nav id="menu-primary" class="menu-container">

		<div class="wrap">

			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container_class' => 'menu', 'menu_class' => 'hp-menu', 'menu_id' => 'menu-primary-items', 'fallback_cb' => '' ) ); ?>

		</div>

	</nav><!-- #menu-primary .menu-container -->

<?php endif; ?>
