<?php
// Enable the JW Player Embed Wizard with preview.
// This can be buggy!
define('JWP6_EMBED_WIZARD', false);


// Simple function to log to the debug log.
function jwp6_l( $message ) {
  if( WP_DEBUG === true ){
    if( is_array( $message ) || is_object( $message ) ){
      error_log( print_r( $message, true ) );
    } else {
      error_log( $message );
    }
  }
}
// Let's make some space in the log.
jwp6_l("\n--------------------------------------\nREQUEST: " . $_SERVER['REQUEST_URI'] . "\n\n");

define('JWP6_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('JWP6_PLUGIN_DIR', dirname(__FILE__));

// Global variable to manage states.
$jwp6_global = array();

require_once dirname(__FILE__) . '/jwp6-class-plugin.php';

require_once dirname(__FILE__) . '/jwp6-class-player.php';

require_once dirname(__FILE__) . '/jwp6-class-shortcode.php';

require_once dirname(__FILE__) . '/jwp6-class-legacy.php';


// Register the actions
JWP6_Plugin::register_actions();

if ( is_admin() ) {
    require_once dirname(__FILE__) . '/jwp6-class-admin.php';
    require_once dirname(__FILE__) . '/jwp6-class-media.php';
    $jwp6_admin = new JWP6_Admin();
}
