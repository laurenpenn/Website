<?php

/*
Plugin Name: JW Player 6 Plugin for Wordpress
Plugin URI: http://www.longtailvideo.com/
Description: Embed a JW Player 6 for HTML5 (or Flash) into your WordPress articles. <strong>Please note</strong>: The part of this plugin that works with JW Player 6 is new and might not be as polished as the part for player 5. If you find bugs, please report them in <a href="http://www.longtailvideo.com/support/forums/addons/working-with-wordpress">the Wordpress section of our forum</a>.
Version: 2.0.5
Author: LongTail Video Inc.
Author URI: http://www.longtailvideo.com/

Copyright 2013  LongTail Video Inc.  (email : wordpress@longtailvideo.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/*
If you want to host your own player, please update the following constant and
set it to the url for your player. E.g.:
define("JWP6_PLAYER_LOCATION", "https://www.example.com/assets/jwplayer/jwplayer.js");
*/
define("JWP6_PLAYER_LOCATION", null);

/*
By default the JW Player plugin uses custom filters to replace the shortcodes. This
allows for granular control of how the shortcode is replaced in different page types.
E.g.: Wordpress normally strips out shortcodes from excerpts, which means that you 
cannot show players in pages where a theme uses the "the_excerpt" function. However
if you are making video blog and also want to show players in e.g. your search results
or category pages, you can still do so because of our custom players.
If you run into problems with other filters you can change this setting and see if it
solves your problems ;)
*/
define("JWP6_USE_CUSTOM_SHORTCODE_FILTER", true);


/* DO NOT CHANGE ANYTHING BELOW HERE... */
/* Unless you know what you are doing off course ;) */

// version 5 prefix
define("LONGTAIL_KEY", "jwplayermodule_");
// version 6 prefix
define("JWP6", "jwp6_");
// Define the plugin root dir
define("JWP6_PLUGIN_DIR_NAME", dirname( __FILE__ ));
// Define the main plugin file
define("JWP6_PLUGIN_FILE", __FILE__);

// We check if this user is using the Player 5 or 6 and
// redirect to the respective plugin version.
$plugin_version = get_option(JWP6 . 'plugin_version');

// If no plugin version is stored, we'll have to find out otherwise.
if ( ! $plugin_version ) {
    global $wpdb;
    $option_query = "SELECT * FROM $wpdb->options WHERE option_name LIKE '" . LONGTAIL_KEY . "%';";
    $num_rows = $wpdb->query($option_query);
    // bigger than 1 because if the plugin is uninstalled the uninstalled var is set.
    if ( $num_rows > 1 ) {
        $plugin_version = 5;
    } 
    else {
        $plugin_version = 6;
    }
    add_option(JWP6 . 'plugin_version', $plugin_version, '', 'yes');
}

// Redirect to the appropriate plugin.
if ( $plugin_version >= 6 ) {
    // load version 6 plugin
    require_once dirname(__FILE__) . '/jwp6/jwp6-plugin.php';
}
else {
    // load tools for migration to version 6
    require_once dirname(__FILE__) . '/migrate.php';
    // load version 5 plugin
    require_once dirname(__FILE__) . '/jwp5.php';
}

