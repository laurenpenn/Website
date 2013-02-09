<?php
// ===================================================
// Load database info and local development parameters
// ===================================================
if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/local-config.php' );
} else {
	define( 'WP_LOCAL_DEV', false );
	define( 'DB_NAME',				'local_db_name' );
	define( 'DB_USER',				'local_db_user' );
	define( 'DB_PASSWORD',			'local_db_password' );
	define( 'DB_HOST',				'localhost' ); // Probably 'localhost'
	
	define( 'ENV_DOMAIN',			'dentonbible.org' );
	define( 'PRODUCTION_DOMAIN',	'dentonbible.org' );
	define( 'DOMAIN_CURRENT_SITE',	ENV_DOMAIN );
	define( 'WP_HOME',				'http://'. ENV_DOMAIN );
	define( 'WP_SITEURL',			'http://'. ENV_DOMAIN .'/wp' );

	// ==============================================================
	// Salts, for security
	// Grab these from: https://api.wordpress.org/secret-key/1.1/salt
	// ==============================================================
	define( 'AUTH_KEY',         'put your unique phrase here' );
	define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
	define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
	define( 'NONCE_KEY',        'put your unique phrase here' );
	define( 'AUTH_SALT',        'put your unique phrase here' );
	define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
	define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
	define( 'NONCE_SALT',       'put your unique phrase here' );
	
}

// ========================
// Custom Content Directory
// ========================
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/content' );

// =========
/* Multisite
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', true );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
define( 'SUNRISE', 'on' );
*/

// ================================================
// You almost certainly do not want to change these
// ================================================
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// ==============================================================
// Table prefix
// Change this if you have multiple installs in the same database
// ==============================================================
$table_prefix  = 'wp_';

// ================================
// Language
// Leave blank for American English
// ================================
define( 'WPLANG', '' );

// =============================================================
// Memory
// Since these sites tend to be large, increase the memory limit
// =============================================================
define( 'WP_MEMORY_LIMIT', '96M' );

// ===========
// Hide errors
// ===========
ini_set( 'display_errors', 0 );
define( 'WP_DEBUG_DISPLAY', false );

// ===================
// Bootstrap WordPress
// ===================
if ( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/wp/' );
require_once( ABSPATH . 'wp-settings.php' );