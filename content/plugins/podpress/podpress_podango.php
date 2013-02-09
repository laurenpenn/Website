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
if(!defined('PLUGINDIR')) {
	$pos = strpos($_SERVER['REQUEST_URI'], 'wp-content');
	header('Location: '.substr($_SERVER['REQUEST_URI'], 0, $pos).'wp-admin/admin.php?page=podpress/podpress_podango.php');
	exit;
}
require_once(ABSPATH.PLUGINDIR.'/podpress/podpress.php');
podPress_isAuthorized();
$podPress->settings_podango_edit();
