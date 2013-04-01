<?php
/**
* podPress Custom Settings
* rev. 1.2
*/

// Usage:
// - Create a new folder in the plugins folder of your blog with the name podpress_options (e.g. /wp-content/plugins/podpress_options/).
// - Copy the podpress_config-sample.php file to this folder.
// - Rername the file to podpress_config.php.
// - Configure the settings in this file.

// maximum number of the additional podPress Feeds (default: 5)
if ( ! defined('PODPRESS_FEEDS_MAX_NUMBER') ) { define('PODPRESS_FEEDS_MAX_NUMBER', 5); }

// If you set this constant from FALSE to TRUE then all premium feed option will not be visible and it will not be possible to create a Feed with the slug name "premium". Existing "premium" Feeds will be removed automatically during the next update of the Feed/iTunes settings of a blog. This constant effects all blogs of a multi site WP installation. (default: FALSE)
if ( ! defined('PODPRESS_DEACTIVATE_PREMIUM') ) { define('PODPRESS_DEACTIVATE_PREMIUM', FALSE); }

// If you set this constant from FALSE to TRUE then 3rd party statistics option will be available again. (default: FALSE)
if ( ! defined('PODPRESS_ACTIVATE_3RD_PARTY_STATS') ) { define('PODPRESS_ACTIVATE_3RD_PARTY_STATS', FALSE); }

// You can log some of the procedures of podPress (like the auto detection of the duration of media files) if you define this constant as true. The log file is podpress_log.dat. (default: FALSE)
if ( ! defined('PODPRESS_DEBUG_LOG') ) { define('PODPRESS_DEBUG_LOG', FALSE); }
?>
