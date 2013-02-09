<?php
/**
* podPress Custom Settings
* rev. 1.1
*/

// Usage:
// - Create a new folder in the plugins folder of your blog with the name podpress_options (e.g. /wp-content/plugins/podpress_options/).
// - Copy the podpress_config-sample.php file to this folder.
// - Rername the file to podpress_config.php.
// - Configure the settings in this file.

// If you set this constant from FALSE to TRUE then all premium feed option will not be visible and it will not be possible to create a Feed with the slug name "premium". Existing "premium" Feeds will be removed automatically during the next update of the Feed/iTunes settings of a blog. This constant effects all blogs of a multi site WP installation.
if ( ! defined('PODPRESS_DEACTIVATE_PREMIUM') ) { define('PODPRESS_DEACTIVATE_PREMIUM', FALSE); }

// If you set this constant from FALSE to TRUE then 3rd party statistics option will be available again.
if ( ! defined( 'PODPRESS_ACTIVATE_3RD_PARTY_STATS' ) ) { define( 'PODPRESS_ACTIVATE_3RD_PARTY_STATS', FALSE ); }
?>