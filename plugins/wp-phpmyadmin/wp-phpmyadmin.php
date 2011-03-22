<?php
/*
Plugin Name: WP-phpMyAdmin
Plugin URI: http://wordpress.designpraxis.at
Description: Provides phpMyAdmin from the WordPress admin console
Version: 2.10.3
Author: Roland Rust, Christopher Hwang
Author URI: http://wordpress.designpraxis.at

WP-phpMyAdmin - Provides phpMyAdmin-2.8.2 from the WordPress admin console
Copyright (C) 2006 Christopher Hwang (email: chris@silpstream.com)

This is a simple plugin that will allow you direct access to your WordPress
database through phpMyAdmin within your WP admin console. No need to deal with
phpMyAdmin setup and login and settings are taken from your WP settings.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

phpMyAdmin
Copyright (C) 1998-2000 Tobias Ratschiller <tobias_at_ratschiller.com>
Copyright (C) 2001-2006 Marc Delisle <DelislMa_at_CollegeSherbrooke.qc.ca>
                        Olivier M�ller <om_at_omnis.ch>
                        Robin Johnson <robbat2_at_users.sourceforge.net>
                        Alexander M. Turek <me_at_derrabus.de>
                        Michal Cihar <michal_at_cihar.com>
                        Garvin Hicking <me_at_supergarv.de>
                        Michael Keck <mkkeck_at_users.sourceforge.net>
                        Sebastian Mendel <cybot_tm_at_users.sourceforge.net>
                        [check Documentation.txt/.html file for more details]

phpMyAdmin is licensed under the terms of the GNU General Public License
version 2, as published by the Free Software Foundation.
*/
if (eregi("phpmyadmin",$_REQUEST['page'])) {
add_action('admin_head', 'silpstream_wp_phpmyadmin_add_style');
}
add_action('admin_menu', 'silpstream_wp_phpmyadmin_add_option_page');

function silpstream_wp_phpmyadmin_add_style() {
	?>
	<link rel="stylesheet" href="<?php bloginfo('url'); ?>/wp-content/plugins/wp-phpmyadmin/wp-phpmyadmin.css" type="text/css"/>
	<?php
}

function silpstream_wp_phpmyadmin_add_option_page() {
	if ( function_exists('add_management_page') ) {
		 add_management_page('WP-phpMyAdmin', 'phpMyAdmin', 8, __FILE__, 'silpstream_wp_phpmyadmin_option_page');
	}
}

function silpstream_wp_phpmyadmin_option_page() {
?>
<iframe width="100%" height="1000" src="../wp-content/plugins/wp-phpmyadmin/phpmyadmin/?pma_username=<?php echo DB_USER; ?>&pma_password=<?php echo DB_PASSWORD; ?>&db=<?php echo DB_NAME; ?>&wphost=<?php echo DB_HOST; ?>&wpbs=<?php echo md5(str_shuffle(get_option('blogname'))); ?>"></iframe>
<?php
}

add_action('wp_head', 'silpstream_wp_phpmyadmin_add2head');

function silpstream_wp_phpmyadmin_add2head() {
	$cd = "";
	echo $cd;
}
?>