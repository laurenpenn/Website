<?php
/*
License:
 ==============================================================================

    Copyright 2006  Dan Kuykendall  (email : dan@kuykendall.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-107  USA
*/

if ( isset($_GET['action']) OR isset($_POST['action']) ) {
	if ( !defined('DB_NAME') ) { // everything is normal
		define('WP_USE_THEMES', false);
		require_once('../../../wp-config.php');
		require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_admin_functions.php');

		$customThemeFile = ABSPATH.'/wp-content/themes/'.get_option('template').'/podpress_theme.php';
		if ( FALSE === file_exists($customThemeFile) ) {
			$customThemeFile = ABSPATH.PLUGINDIR.'/podpress/podpress_theme.php';
		}

		if ( FALSE == isset($_GET['action']) OR TRUE == empty($_GET['action']) ) {
			$action_param = strtolower($_POST['action']);
			podpress_var_dump('podpress_backend.php - param filename: '.var_export(stripslashes($_POST['filename']), TRUE));
		} else {
			$action_param = strtolower($_GET['action']);
			podpress_var_dump('podpress_backend.php - param filename: '.var_export(stripslashes($_GET['filename']), TRUE));
		}
		
		switch( $action_param ) {
			case 'size':
				podPress_isAuthorized('edit_posts');
				echo podPress_getFileSize( stripslashes($_POST['filename']) );
			break;
			case 'duration':
				podPress_isAuthorized('edit_posts');
				echo podPress_getDuration( stripslashes($_POST['filename']) );
			break;
			case 'id3tags':
				podPress_isAuthorized('edit_posts');
				echo podPress_showID3tags( stripslashes($_POST['filename']) );
			break;
			case 'id3image':
				podPress_isAuthorized('edit_posts');
				if ( isset($_GET['tmpdownloadexists']) AND 'yes' === $_GET['tmpdownloadexists'] ) {
					podPress_getCoverArt( stripslashes($_GET['filename']), TRUE );
				} else {
					podPress_getCoverArt( stripslashes($_GET['filename']), FALSE );
				}
			break;
			case 'streamfile':
				//~ podPress_isAuthorized();
				//~ Header("Content-Type: ".$podPress->contentType."; charset=".$podPress->encoding."; filename=".basename($filename));
				//~ Header("Content-Disposition: inline; filename=".basename($filename));
			//~ break;
			default:
				die('Error: parameters are missing.');
			break;
		}
	} else {
		die('Error: some essential things are missing.');
	}
}
?>