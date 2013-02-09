<?php
/*
This is a sample local-config.php file
In it, you *must* include the four main database defines

You may include other settings here that you only want enabled on your local development checkouts
*/

define( 'DB_NAME',			'local_db_name' );
define( 'DB_USER',			'local_db_user' );
define( 'DB_PASSWORD',		'local_db_password' );
define( 'DB_HOST',			'localhost' ); // Probably 'localhost'

/**
 * Forces new hostsnames 
 * 
 *         dev.example.local
 * maintenance.example.local
 *     staging.example.local
 *  production.example.local
 * 
 * @see wp-config.php
 * @link http://codex.wordpress.org/Editing_wp-config.php
 */
define( 'ENV_DOMAIN',			'local.dentonbible.org' );
define( 'PRODUCTION_DOMAIN',	'dentonbible.org' );
define( 'DOMAIN_CURRENT_SITE',	ENV_DOMAIN );
define( 'WP_HOME',				'http://'. ENV_DOMAIN );
define( 'WP_SITEURL',			'http://'. ENV_DOMAIN .'/wp' );

/**
 * Salts, for security
 * Grab these from: https://api.wordpress.org/secret-key/1.1/salt
 * @see AUTH_KEY, SECURE_AUTH_KEY, LOGGED_IN_KEY, NONCE_KEY, AUTH_SALT, SECURE_AUTH_SALT, LOGGED_IN_SALT, NONCE_SALT
 * @link http://codex.wordpress.org/Editing_wp-config.php#Security_Keys
 */
define( 'AUTH_KEY',         'put your unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
define( 'NONCE_KEY',        'put your unique phrase here' );
define( 'AUTH_SALT',        'put your unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
define( 'NONCE_SALT',       'put your unique phrase here' );

/**
 * Enabled WP_DEBUG mode
 * @see WP_DEBUG
 * @link http://codex.wordpress.org/Debugging_in_WordPress#WP_DEBUG
 */
define( 'WP_DEBUG',			true );

/**
 * Enable Debug logging to the /content/debug.log file
 * @see SCRIPT_DEBUG
 * @link http://codex.wordpress.org/Debugging_in_WordPress#WP_DEBUG_LOG
 */
define( 'WP_DEBUG_LOG',		true );

/**
 * Enable display of errors and warnings
 * @see SCRIPT_DEBUG
 * @link http://codex.wordpress.org/Debugging_in_WordPress#WP_DEBUG_DISPLAY
 */
define( 'WP_DEBUG_DISPLAY',	true );
@ini_set( 'display_errors',	1 );

/**
 * Saves the database queries to a array
 * @see SCRIPT_DEBUG
 * @link http://codex.wordpress.org/Debugging_in_WordPress#SAVEQUERIES
 */
define( 'SAVEQUERIES',		true );

/**
 * Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
 * @see SCRIPT_DEBUG
 * @link http://codex.wordpress.org/Debugging_in_WordPress#SCRIPT_DEBUG
 */
define( 'SCRIPT_DEBUG',		true );