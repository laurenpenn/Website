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
	/**************************************************************/
	/* Functions for supporting premiumcasting */
	/**************************************************************/	

	if(!function_exists('getallheaders')) {
		function getallheaders() {
			$headers = array();
			foreach($_SERVER as $h=>$v) {
				if(ereg('HTTP_(.+)',$h,$hp)) {
					$headers[$hp[1]]=$v;
				}
			}
 	  	return $headers;
		}
	}

	function podPress_addDigestAuth($user) {
		GLOBAL $podPress, $user_pass;
		if(!podPress_WPVersionCheck()) {
			return;
		}
		$userdata = get_userdatabylogin($user);
		$current_creds = get_usermeta($userdata->ID, 'premiumcast_creds');
		$correct_creds = md5($user . ':' . $podPress->realm . ':' . $user_pass);
		if($current_creds == $correct_creds) {
			return;
		}
		$x = update_usermeta($userdata->ID, 'premiumcast_creds', $correct_creds);
		$current_creds = get_usermeta($userdata->ID, 'premiumcast_creds');
	}

	// function to parse the http auth header
	function podPress_http_digest_parse($txt)
	{
		// protect against missing data
		$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
		$data = array();

		if(substr($txt, 0, 6) == 'Digest') {
			$txt = substr($txt, 7);
			$attribs = explode(',', $txt);
			foreach ($attribs as $attrib) {
				$divider = strpos($attrib, '=');
				$name = trim(substr($attrib, 0, $divider));
				$val = trim(substr($attrib, $divider+1));
				if(substr($val, 0, 1) == '"' || substr($val, 0, 1) == "'") {
					$val = substr($val, 1, strlen($val)-2);
				}
				unset($needed_parts[$name]);
				$data[$name] = $val;
				//echo $name.' = '.$val."<br/>\n";
			}
		}
		if(!empty($needed_parts)) {
			return false;
		}
		return $data;
	}

	function podPress_http_basic_parse($txt)
	{
		$result = array();
		list($result['username'], $result['passwd']) = explode(':', base64_decode(substr($txt, 6)));
		return $result;
	}

	function podPress_requestLogin() {
		GLOBAL $podPress;
		if(!$podPress->settings['enablePremiumContent']) {
			die('Premium Content support is disabled in podPress.');			
		}
		header('HTTP/1.0 401 Unauthorized');
		switch (PODPRESS_PREMIUM_METHOD) {
			case 'Digest':
				header('WWW-Authenticate: Digest realm="'.$podPress->realm.'", qop="auth", nonce="'.uniqid(rand()).'", opaque="'.md5($podPress->realm).'", stale=false, algorithm=MD5');
				break;
			case 'Basic':
			default:
				header('WWW-Authenticate: Basic realm="'.$podPress->realm.'"');
				break;
		}
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">'."\n";
		echo "<HTML>\n";
		echo "  <HEAD>\n";
		echo "    <TITLE>".__('Error', 'podpress')."</TITLE>\n";
		echo '    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">'."\n";
		echo "  </HEAD>\n";
		echo "  <BODY>\n";
		echo "  <H1>401 Unauthorized.</H1>\n";
		echo __('The contents of this feed are only available to paying subscribers.', 'podpress')."\n";
		echo "  </BODY>\n";
		die("</HTML>\n");
	}
	
	function podPress_reloadCurrentUser() {
		global $wp, $user_login, $userdata, $user_level, $user_ID, $user_email, $user_url, $user_pass_md5, $user_identity, $current_user;
		if(defined('PODPRESS_PREMIUMLOGIN') && $GLOBALS['current_user']->ID == 0) {
			$current_user = new WP_User(PODPRESS_PREMIUMID, PODPRESS_PREMIUMLOGIN);
			$user_login = PODPRESS_PREMIUMLOGIN;
			$userdata    = get_userdatabylogin($user_login);
			$user_level  = $userdata->user_level;
			$user_ID     = $userdata->ID;
			$user_email  = $userdata->user_email;
			$user_url    = $userdata->user_url;
			$user_pass_md5 = md5($userdata->user_pass);
			$user_identity = $userdata->display_name;	
			$wp->query_posts();		
		}
	}

	if (defined('PREMIUMCAST')) {
		function podPress_get_currentuserinfo() {
			podPress_validateLogin();
		}
	}

	function podPress_validateLogin() {
		GLOBAL $user_login, $userdata, $user_level, $user_ID, $user_email, $user_url, $user_pass_md5, $user_identity, $current_user;

		if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
			return false;
		}
	
		$http_headers = getallheaders();
		if (empty($http_headers['Authorization'])) {
			
			if (empty($http_headers['AUTHORIZATION'])) {
				podPress_requestLogin();
				return false;
			} else {
				$http_headers['Authorization'] = stripslashes(stripslashes($http_headers['AUTHORIZATION']));
			}
		}
		switch (PODPRESS_PREMIUM_METHOD) {
			case 'Digest':
				$data = podPress_http_digest_parse($http_headers['Authorization']);
				if (!$data) {
					die('Wrong Credentials!');
				}
				$x = get_userdatabylogin($data['username']);
				$A1 = get_usermeta($x->ID, 'premiumcast_creds');
				$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
				$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

				if ($data['response'] == $valid_response) {
					$user_login = $data['username'];
					$authresult = wp_login($user_login, md5($x->user_pass), true);
				}
				break;
			case 'Basic':
			default:
				$authparts = podPress_http_basic_parse($http_headers['Authorization']);
				$user_login = $authparts['username'];
				$authresult = wp_login($user_login, $authparts['passwd']);
				break;
		}

		$podPress_x = $GLOBALS['wp_object_cache']->cache['userlogins'][$user_login];
		if(is_object($GLOBALS['wp_object_cache']->cache['userlogins'][$user_login])) {
			if($podPress_x->wp_capabilities['premium_subscriber'] != 1 && 
			   $podPress_x->wp20_capabilities['premium_subscriber'] != 1) {
				$authresult = false;
			}
		} else {
			if($GLOBALS['wp_object_cache']->cache['users'][$podPress_x]->wp_capabilities['premium_subscriber'] != 1 && 
			   $GLOBALS['wp_object_cache']->cache['users'][$podPress_x]->wp20_capabilities['premium_subscriber'] != 1) {
				$authresult = false;
			}
		}
		unset($podPress_x);

		if(!$authresult) {
			podPress_requestLogin();
			return false;
			$current_user = new WP_User(0);
			return false;
		}

		$userdata    = get_userdatabylogin($user_login);
		$user_level  = $userdata->user_level;
		$user_ID     = $userdata->ID;
		$user_email  = $userdata->user_email;
		$user_url    = $userdata->user_url;
		$user_pass_md5 = md5($userdata->user_pass);
		$user_identity = $userdata->display_name;
		define('PODPRESS_PREMIUMLOGIN', $user_login);
		define('PODPRESS_PREMIUMID', $userdata->ID);

		if ( empty($current_user) ) {
			$current_user = new WP_User($user_ID);
		}
	}
?>