<?php

// Include the necessary admin stuff.
require_once('../../../../wp-load.php');
require_once('../../../../wp-admin/includes/admin.php');

// Check for administrator
if ( !current_user_can('administrator') ) {
    exit();
}

// Check for valid action.
$available_actions = array('migrate', 'revert');
if ( ! isset($_GET['a']) || ! in_array($_GET['a'], $available_actions) ) {
    exit('error...');
}

require_once( JWP6_PLUGIN_DIR . '/jwp6-class-plugin.php' );
require_once( JWP6_PLUGIN_DIR . '/jwp6-class-player.php' );
require_once( JWP6_PLUGIN_DIR . '/jwp6-class-legacy.php' );

if ( 'migrate' == $_GET['a'] ) {
    // Create the default player
    $default = new JWP6_Player();
    $default->save();

    // Import various plugin preferences
    JWP6_Legacy::import_jwp5_settings();

    // Import JWP5 player configs
    $players = JWP6_Legacy::import_jwp5_players();
    add_option(JWP6 . 'imported_jwp5_players', $players);

    // Set update message
    $license_page = admin_url('admin.php?page=' . JWP6 . 'menu_licensing');
    $message = "You have upgraded to JW Player 6. Please remember to ";
    $message.= "<strong><a href='{$license_page}'>enter your player license key</a></strong> ";
    $message.= "to enable all the license specific settings of the player.";
    update_option(JWP6 . 'notification', $message);

    // Redirect to the new overview
    wp_redirect(admin_url('admin.php?page=' . JWP6 . 'menu_import'));
}

if ( 'revert' == $_GET['a'] ) {
    JWP6_Plugin::purge_settings(false);
    add_option(JWP6 . 'plugin_version', $plugin_version, '', 'yes');
    wp_redirect(admin_url('admin.php?page=jwplayer-update'));
}


/*
Disabled. Will be available with future release.
if ( 'purge' == $_GET['a'] ) {
    $ok = JWP6_Legacy::purge_jwp5_settings();
    $message = "Your JW Player 5 (plugin) settings and files (that the script had permission for) have been removed from your system.";
    update_option(JWP6 . 'notification', $message);
    delete_option(JWP6 . 'jwp5_players_imported');
    delete_option(JWP6 . 'jwp5_playlists_imported');
    wp_redirect(admin_url('admin.php?page=' . JWP6 . 'menu'));
}
*/
