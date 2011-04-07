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
	if(!defined('DB_NAME')) { // everything is normal
		define('WP_USE_THEMES', false);
		require_once('../wp-config.php');
		if(isset($_GET['feed'])){
			$parts = explode('/', $_GET['feed']);
			podPress_processDownloadRedirect($parts[0], $parts[1], $parts[2], 'feed');
		} elseif(isset($_GET['play'])){ 
			$parts = explode('/', $_GET['play']);
			podPress_processDownloadRedirect($parts[0], $parts[1], $parts[2], 'play');
		} elseif(isset($_GET['web'])){ 
			$parts = explode('/', $_GET['web']);
			podPress_processDownloadRedirect($parts[0], $parts[1], $parts[2], 'web');
		} else {
			header('Location: '.get_option('siteurl'));
			exit;
		}
	}
?>