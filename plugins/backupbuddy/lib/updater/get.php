<?php
/*
 *	PluginBuddy.com & iThemes.com
 *	Author: Dustin Bolton < http://dustinbolton.com >
 *
 *	Created:	February 24, 2010
 *	Updated:	July 1, 2011
 *
 *	Version:	1.0.4
 * 
 *	Gets updater webpage and returns it to user.
 *	This is used so that data may be passed back to the calling server directly.
 *
 */

auth_redirect(); // Handles login check and redirects to WP Login if needed.
if ( !current_user_can( 'install_themes' ) ) {
	die( 'ACCESS DENIED! You need higher access to do this. Error #534344 PluginBuddy.com.' );
}

$url = $_GET['url'];

if ( isset($_POST) ) {
	$response = wp_remote_post( $url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => $_POST,
			'cookies' => array()
		)
	);
} else {
	$response = wp_remote_get( $url, array(
			'method' => 'GET',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => null,
			'cookies' => array()
		)
	);
}

if( is_wp_error( $response ) ) {
	die( 'ERROR #5455. Unable to access PluginBuddy / iThemes updater & licensing system. Details: ' . $response->get_error_message() );
} else {
	$response = $response['body'];
}

$newline_pos = strpos($response,"\n");

// Take first line and unserialize it.  If its an array then enter the data.
$response_array = unserialize( substr($response, 0, $newline_pos) ); // Turn first line into array.
if ( is_array( $response_array ) ) { // First line is array data to server.
	$options = get_option( $_GET['var'] );
	
	if ( isset( $response_array['set_key'] ) ) {
		$options['updater']['key'] = $response_array['set_key']; // Change key value.
	} elseif ( isset( $response_array['unset_key'] ) ) {
		$options['updater']['key'] = '';
	} else {
		echo 'ERROR: UNKNOWN CALLBACK COMMAND! ERROR #85433256.';
	}
	
	$options['updater']['last_check'] = 0; // Force update on next refresh.
	update_option( $_GET['var'], $options );
	
	echo substr( $response, $newline_pos+1 );
} else {
	echo $response;
}
?>