<?php
/**
* Handle AJAX requests in Frontend 
* 
* @author 	Kolja Schleich
* @package	LeagueManager
* @copyright 	Copyright 2009
*/

require_once('../../../wp-load.php');

if ( isset( $_POST['action'] ) ) {
	do_action( 'wp_ajax_' . $_POST['action'] );
	die(0);
}
?>
