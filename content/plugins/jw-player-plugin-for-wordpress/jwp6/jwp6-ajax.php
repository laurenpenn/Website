<?php

// Include the necessary admin stuff.
require_once('../../../../wp-load.php');
require_once('../../../../wp-admin/includes/admin.php');

// Only for logged in users.
if ( !current_user_can('administrator') && !current_user_can('editor') && !current_user_can('contributor') ) {
    exit();
}

// Call parameter is compulsory
if ( ! $call = $_GET['call'] ) {
    exit();
}

require_once( JWP6_PLUGIN_DIR . '/jwp6-class-plugin.php' );
require_once( JWP6_PLUGIN_DIR . '/jwp6-class-media.php' );
require_once( JWP6_PLUGIN_DIR . '/jwp6-class-shortcode.php' );

if ( 'embedcode' == $call ) {
    $sc = new JWP6_Shortcode();
    echo $sc->embedcode();
    exit();
}

?>