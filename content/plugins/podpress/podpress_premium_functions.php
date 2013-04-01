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
				if(preg_match('/HTTP_(.+)/i', $h, $hp)) {
					$headers[$hp[1]]=$v;
				}
			}
			return $headers;
		}
	}

#	function podPress_addDigestAuth($user) {
#		GLOBAL $podPress, $user_pass, $credentials;
#		podPress_var_dump('podPress_addDigestAuth');
#		podPress_var_dump($user);
#		podPress_var_dump($user_pass);
#		podPress_var_dump($credentials['user_password']);
#		if(!podPress_WPVersionCheck()) {
#			return;
#		}
#		$userdata = get_userdatabylogin($user);
#		$current_creds = get_usermeta($userdata->ID, 'premiumcast_creds');
#		$correct_creds = md5($user . ':' . $podPress->realm . ':' . $user_pass);
#		if ($current_creds == $correct_creds) {
#			return;
#		}
#		$x = update_usermeta($userdata->ID, 'premiumcast_creds', $correct_creds);
#		$current_creds = get_usermeta($userdata->ID, 'premiumcast_creds');
#	}
	function podPress_addDigestAuth($user, $password) {
		GLOBAL $podPress, $wp_version;
		podPress_var_dump('############### podPress_addDigestAuth ###############');
		if ( FALSE === empty($user) and FALSE === empty($password) ) {
			podPress_var_dump('#### Log In ####');
			podPress_var_dump($user);
			podPress_var_dump($podPress->realm);
			podPress_var_dump($password);
			if(!podPress_WPVersionCheck()) {
				return;
			}
			if ( version_compare($wp_version, '3.3', '>=') ) {
				$userdata = get_user_by('login', $user);
			} else { 
				$userdata = get_userdatabylogin($user);
			}
			if ( version_compare($wp_version, '3.0', '>=') ) {
				$current_creds = get_user_meta($userdata->ID, 'premiumcast_creds', TRUE);
			} else { 
				$current_creds = get_usermeta($userdata->ID, 'premiumcast_creds');
			}
			$correct_creds = md5($user . ':' . $podPress->realm . ':' . $password);
			podPress_var_dump($current_creds);
			podPress_var_dump($correct_creds);
			if ($current_creds != $correct_creds) {
				if ( version_compare($wp_version, '3.0', '>=') ) {
					$result = update_user_meta($userdata->ID, 'premiumcast_creds', $correct_creds);
				} else { 
					$result = update_usermeta($userdata->ID, 'premiumcast_creds', $correct_creds);
				}
				podPress_var_dump('update_usermeta');
				podPress_var_dump($result);
			}
		} else {
			podPress_var_dump('#### Log Out ($user or $password were empty) ####');
		}
		return $user;
	}

	// function to parse the http auth header
	function podPress_http_digest_parse($txt)
	{
		podPress_var_dump('############### podPress_http_digest_parse ###############');
		podPress_var_dump($txt);
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
			}
		}
		podPress_var_dump($data);
		if(!empty($needed_parts)) {
			return false;
		}
		return $data;
	}

	function podPress_http_basic_parse($txt)
	{
		$result = array();
		list($result['username'], $result['passwd']) = explode(':', base64_decode(substr($txt, 6)));
		podPress_var_dump('############### podPress_http_basic_parse ###############');
		podPress_var_dump($txt);
		return $result;
	}

	function podPress_requestLogin() {
		GLOBAL $podPress;
		if(!$podPress->settings['enablePremiumContent']) {
			die('Premium Content support is disabled in podPress.');			
		}
		$loginmethod = $podPress->settings['premiumMethod'];
		
		podPress_var_dump('############### podPress_requestLogin ###############');
		podPress_var_dump($loginmethod);
		if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
			podPress_var_dump($_SERVER['PHP_AUTH_DIGEST']);
		}
		switch ($podPress->settings['premiumMethod']) {
			case 'Digest':
				//if ( TRUE === empty($_SERVER['PHP_AUTH_DIGEST']) ) {
					status_header('401');
					header('WWW-Authenticate: Digest realm="'.$podPress->realm.'", qop="auth", nonce="'.uniqid(rand()).'", opaque="'.md5($podPress->realm).'", stale=false, algorithm=MD5');
					//header('HTTP/1.1 401 Unauthorized');
					#podPress_var_dump('WWW-Authenticate: Digest realm="'.$podPress->realm.'", qop="auth", nonce="'.uniqid(rand()).'", opaque="'.md5($podPress->realm).'", stale=false, algorithm=MD5');
					die('401 Unauthorized');
				//}
				break;
			case 'Basic':
			default:
				//if (!isset($_SERVER['PHP_AUTH_USER'])) {
					status_header('401');
					header('WWW-Authenticate: Basic realm="'.$podPress->realm.'"');
					die('401 Unauthorized');
				//}
				break;
		}
			
#		header('Date: ' . gmdate("D, d M Y H:i:s") . ' GMT');
#		header('Content-Type: application/rss+xml');
		
#		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">'."\n";
#		echo "<HTML>\n";
#		echo "  <HEAD>\n";
#		echo "    <TITLE>".__('Error', 'podpress')."</TITLE>\n";
#		echo '    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">'."\n";
#		echo "  </HEAD>\n";
#		echo "  <BODY>\n";
#		echo "  <H1>401 Unauthorized.</H1>\n";
#		echo __('The contents of this feed are only available to paying subscribers.', 'podpress')."\n";
#		echo "  </BODY>\n";
#		die("</HTML>\n");
	}
	
	function podPress_reloadCurrentUser() {
		global $wp, $user_login, $userdata, $user_level, $user_ID, $user_email, $user_url, $user_pass_md5, $user_identity, $current_user, $wp_version;
		podPress_var_dump('############### podPress_reloadCurrentUser ###############');
		podPress_var_dump($user_ID);
		podPress_var_dump(defined('PODPRESS_PREMIUMLOGIN'));
		podPress_var_dump(defined('PODPRESS_PREMIUMID'));
		podPress_var_dump($GLOBALS['current_user']->ID);
		if(defined('PODPRESS_PREMIUMLOGIN') && $GLOBALS['current_user']->ID == 0) {
			$current_user = new WP_User(PODPRESS_PREMIUMID, PODPRESS_PREMIUMLOGIN);
			$user_login = PODPRESS_PREMIUMLOGIN;
			if ( version_compare($wp_version, '3.3', '>=') ) {
				$userdata = get_user_by('login', $user_login);
			} else { 
				$userdata = get_userdatabylogin($user_login);
			}
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
		GLOBAL $wp_version, $user_login, $userdata, $user_level, $user_ID, $user_email, $user_url, $user_pass_md5, $user_identity, $current_user, $podPress;

		if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
			return false;
		}
		podPress_var_dump('############### podPress_validateLogin ###############');

		$http_headers = getallheaders();
		podPress_var_dump('$http_headers');
		podPress_var_dump($http_headers);
		
		if ( empty($http_headers['Authorization']) ) {
			if ( empty($http_headers['AUTHORIZATION']) ) {
				if ( empty($http_headers['REDIRECT_HTTP_AUTHORIZATION']) ) {
					podPress_requestLogin();
					return false;
				} else {
					$http_headers['Authorization'] = stripslashes(stripslashes($http_headers['REDIRECT_HTTP_AUTHORIZATION']));
				}
			} else {
				$http_headers['Authorization'] = stripslashes(stripslashes($http_headers['AUTHORIZATION']));
			}
		}
		
		switch ($podPress->settings['premiumMethod']) {
			case 'Digest':
				$data = podPress_http_digest_parse($http_headers['Authorization']);
				if (!$data) {
					die('Wrong Credentials!');
				}
				if ( version_compare($wp_version, '3.3', '>=') ) {
					$x = get_user_by('login', $data['username']);
				} else { 
					$x = get_userdatabylogin($data['username']);
				}
				if ( version_compare($wp_version, '3.0', '>=') ) {
					$A1 = get_user_meta($x->ID, 'premiumcast_creds', TRUE);
				} else { 
					$A1 = get_usermeta($x->ID, 'premiumcast_creds');
				}
				podPress_var_dump('$A1');
				podPress_var_dump($A1);

				$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
				podPress_var_dump('$A2');
				podPress_var_dump($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
				podPress_var_dump($A2);
				
				$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
				podPress_var_dump($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
				podPress_var_dump('$valid_response');
				podPress_var_dump($valid_response);
				podPress_var_dump($data['response']);
				
				if ($data['response'] == $valid_response) {
					$user_login = $data['username'];
					$authresult = TRUE;
				} else {
					$authresult = FALSE;
				}
				break;
			case 'Basic':
			default:
				$authparts = podPress_http_basic_parse($http_headers['Authorization']);
				$user_login = $authparts['username'];
				if (version_compare($wp_version ,'2.5', '<')) {
					$authresult = wp_login($user_login, $authparts['passwd']);
				} else {
						$creds = array();
						$creds['user_login'] = $user_login;
						$creds['user_password'] = $authparts['passwd'];
						$creds['remember'] = true;
						$authresult = wp_signon($creds, false);
				}
				break;
		}
		
		podPress_var_dump('$authresult');
		podPress_var_dump($authresult);
		
		if ( isset($GLOBALS['wp_object_cache']->cache['userlogins'][$user_login]) ) {
			$podPress_x = $GLOBALS['wp_object_cache']->cache['userlogins'][$user_login];
		} else {
			$podPress_x = 0;
		}
		if ( is_object($podPress_x) ) {
			if ( isset($podPress_x->wp_capabilities['premium_subscriber']) AND $podPress_x->wp_capabilities['premium_subscriber'] != 1 AND isset($podPress_x->wp20_capabilities['premium_subscriber']) AND $podPress_x->wp20_capabilities['premium_subscriber'] != 1 ) {
				$authresult = false;
			}
		} elseif ( isset($GLOBALS['wp_object_cache']->cache['user_meta'][$podPress_x]) AND is_array($GLOBALS['wp_object_cache']->cache['user_meta'][$podPress_x]) AND is_array($GLOBALS['wp_object_cache']->cache['user_meta'][$podPress_x]['wp_capabilities']) ) {
			podPress_var_dump('user_meta is object');
			$user_has_cap = FALSE;
			foreach ($GLOBALS['wp_object_cache']->cache['user_meta'][$podPress_x]['wp_capabilities'] as $capability_str) {
				if (FALSE != stristr($capability_str, 'premium_subscriber') ) {
					$user_has_cap = TRUE;
					break;
				}
			}
			if ( FALSE === $user_has_cap ) {
				$authresult = false;
			} 
		} else {
			if ( isset($GLOBALS['wp_object_cache']->cache['users'][$podPress_x]->wp_capabilities['premium_subscriber']) AND $GLOBALS['wp_object_cache']->cache['users'][$podPress_x]->wp_capabilities['premium_subscriber'] != 1 AND isset($GLOBALS['wp_object_cache']->cache['users'][$podPress_x]->wp20_capabilities['premium_subscriber']) AND $GLOBALS['wp_object_cache']->cache['users'][$podPress_x]->wp20_capabilities['premium_subscriber'] != 1 ) {
				$authresult = false;
			} 
		}
		unset($podPress_x);
		
		podPress_var_dump('$authresult');
		podPress_var_dump($authresult);
		podPress_var_dump(isset($authresult->errors));
		
		if(FALSE === $authresult OR TRUE === isset($authresult->errors)) {
			podPress_requestLogin();
			die('401 Unauthorized');
			//~ return false;
			//~ $current_user = new WP_User(0);
			//~ return false;
		}
		
		if ( version_compare($wp_version, '3.3', '>=') ) {
			$userdata = get_user_by('login', $user_login);
		} else { 
			$userdata = get_userdatabylogin($user_login);
		}
		$user_level = $userdata->user_level;
		$user_ID = $userdata->ID;
		$user_email = $userdata->user_email;
		$user_url = $userdata->user_url;
		$user_pass_md5 = md5($userdata->user_pass);
		$user_identity = $userdata->display_name;
		define('PODPRESS_PREMIUMLOGIN', $user_login);
		define('PODPRESS_PREMIUMID', $userdata->ID);

		if ( empty($current_user) ) {
			$current_user = new WP_User($user_ID);
		}
	}
	if ( !function_exists('podPress_var_dump') ) {
		function podPress_var_dump($var) {
			if ( defined( 'PODPRESS_DEBUG_LOG' ) AND TRUE === constant( 'PODPRESS_DEBUG_LOG' ) ) { 
				$returnmsg = '';
				// write the out put to the log file
				$filename = PODPRESS_DIR.'/podpress_log.dat';
				if ( is_file($filename) ) {
					$result = @chmod($filename, 0777);
					if (FALSE === $result) {		
						return sprintf(__('This PHP script has not the permission to use chmod (or chown) for the %1$s file.', 'podpress'), $filename);
					}
					if ( (filesize($filename)/1024) > 100 ) { // delete the Logfile if it is bigger than 100 kByte
						$result = @unlink($filename); 
						if (FALSE === $result) {
							$returnmsg = sprintf(' '.__('This PHP script has not the permission to delete the %1$s file (unlink).', 'podpress'), $filename);
						}
					} 
				}
				$handle = @fopen($filename, "a");
				if ( FALSE !== $handle ) { 
					//@fputs($handle, '['.date('j.m.Y - H:i:s', time()).'] '.var_export($var, TRUE)."\n");
					@fputs($handle, '['.date('j.m.Y - H:i:s').'] '.var_export($var, TRUE)."\n");
					$status = @fclose($handle);
					$returnmsg .= '';
				} else {
					$returnmsg .= sprintf(__('This PHP script has not the permission to use create or open the %1$s file for writing (fopen).', 'podpress'), $filename);
				}
				if ( is_file($filename) ) { 
					$result = @chmod($filename, 0644); 
					if (FALSE === $result) {
						$returnmsg = sprintf(' '.__('This PHP script has not the permission to use chmod (or chown) for the %1$s file.', 'podpress'), $filename);
					}
				}
				return $returnmsg;
			}
		}
	}
?>