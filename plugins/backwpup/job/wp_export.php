<?PHP
if (!defined('BACKWPUP_JOBRUN_FOLDER')) {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	header("Status: 404 Not Found");
	die();
}

function wp_export() {
	global $WORKING,$STATIC;
	$WORKING['STEPTODO']=1;
	trigger_error(sprintf(__('%d. try for wordpress export to XML file...','backwpup'),$WORKING['WP_EXPORT']['STEP_TRY']),E_USER_NOTICE);
	need_free_memory(10485760); //10MB free memory
	if (function_exists('curl_exec')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, substr($STATIC['JOBRUNURL'],0,-11).'wp_export_generate.php');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('nonce'=>$WORKING['NONCE'],'type'=>'getxmlexport'));
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'BackWPup');
		if (defined('CURLOPT_PROGRESSFUNCTION')) {
			curl_setopt($ch, CURLOPT_NOPROGRESS, false);
			curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'curl_progresscallback');
			curl_setopt($ch, CURLOPT_BUFFERSIZE, 1048576);
		} 
		if (!empty($STATIC['CFG']['httpauthuser']) and !empty($STATIC['CFG']['httpauthpassword'])) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_USERPWD, $STATIC['CFG']['httpauthuser'].':'.backwpup_base64($STATIC['CFG']['httpauthpassword']));
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
		$return=curl_exec($ch);
		$status=curl_getinfo($ch);
		if ($status['http_code']>=300 or $status['http_code']<200 or curl_errno($ch)>0) {
			if (0!=curl_errno($ch)) 
				trigger_error(__('cURL:','backwpup').' ('.curl_errno($ch).') '.curl_error($ch),E_USER_ERROR);
			else 
				trigger_error(__('cURL:','backwpup').' ('.$status['http_code'].')  Invalid response.',E_USER_ERROR);	
		} else {
			file_put_contents($STATIC['TEMPDIR'].preg_replace( '/[^a-z0-9_\-]/', '', strtolower($STATIC['WP']['BLOGNAME'])).'.wordpress.'.date( 'Y-m-d' ).'.xml', $return);
		}
		curl_close($ch);
	} else {
		//use fopen if no curl
		$urlParsed=parse_url(substr($STATIC['JOBRUNURL'],0,-11).'wp_export_generate.php');
		if ($urlParsed['scheme'] == 'https') {
			$host = 'ssl://' . $urlParsed['host'];
			$port = (!empty($urlParsed['port'])) ? $urlParsed['port'] : 443;
		} else {
			$host = $urlParsed['host'];
			$port = (!empty($urlParsed['port'])) ? $urlParsed['port'] : 80;
		}
		$query=http_build_query(array('nonce'=>$WORKING['NONCE'],'type'=>'getxmlexport'));
		$path=(isset($urlParsed['path']) ? $urlParsed['path'] : '/').(isset($urlParsed['query']) ? '?' . $urlParsed['query'] : '');
		$header = "POST ".$path." HTTP/1.1\r\n";
		$header.= "Host: ".$urlParsed['host']."\r\n";
		$header.= "User-Agent: BackWPup\r\n";
		$header.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header.= "Content-Length: ".strlen($query)."\r\n";
		if (!empty($STATIC['CFG']['httpauthuser']) and !empty($STATIC['CFG']['httpauthpassword'])) 
			$header.= "Authorization: Basic ".base64_encode($STATIC['CFG']['httpauthuser'].':'.backwpup_base64($STATIC['CFG']['httpauthpassword']))."\r\n";
		$header.= "Connection: Close\r\n\r\n";
		$header.=$query;
		$fp=fsockopen($host, $port, $errno, $errstr, 300);
		fwrite($fp,$header);
		$responseHeader='';
		do {
			$responseHeader.= fread($fp, 1);
        } while (!preg_match('/\\r\\n\\r\\n$/', $responseHeader));
		while (!feof($fp)) {
			update_working_file();
			file_put_contents($STATIC['TEMPDIR'].preg_replace( '/[^a-z0-9_\-]/', '', strtolower($STATIC['WP']['BLOGNAME'])).'.wordpress.'.date( 'Y-m-d' ).'.xml', fgets($fp, 256), FILE_APPEND);
		}
		fclose($fp);
	}
	//add XML file to backupfiles
	if (is_readable($STATIC['TEMPDIR'].preg_replace( '/[^a-z0-9_\-]/', '', strtolower($STATIC['WP']['BLOGNAME'])).'.wordpress.'.date( 'Y-m-d' ).'.xml')) {
		$filestat=stat($STATIC['TEMPDIR'].preg_replace( '/[^a-z0-9_\-]/', '', strtolower($STATIC['WP']['BLOGNAME'])).'.wordpress.'.date( 'Y-m-d' ).'.xml');
		trigger_error(sprintf(__('Add XML export "%1$s" to backup list with %2$s','backwpup'),preg_replace( '/[^a-z0-9_\-]/', '', strtolower($STATIC['WP']['BLOGNAME'])).'.wordpress.'.date( 'Y-m-d' ).'.xml',formatbytes($filestat['size'])),E_USER_NOTICE);
		$WORKING['ALLFILESIZE']+=$filestat['size'];
		add_file(array(array('FILE'=>$STATIC['TEMPDIR'].preg_replace( '/[^a-z0-9_\-]/', '', strtolower($STATIC['WP']['BLOGNAME'])).'.wordpress.'.date( 'Y-m-d' ).'.xml','OUTFILE'=>preg_replace( '/[^a-z0-9_\-]/', '', strtolower($STATIC['WP']['BLOGNAME'])).'.wordpress.'.date( 'Y-m-d' ).'.xml','SIZE'=>$filestat['size'],'ATIME'=>$filestat['atime'],'MTIME'=>$filestat['mtime'],'CTIME'=>$filestat['ctime'],'UID'=>$filestat['uid'],'GID'=>$filestat['gid'],'MODE'=>$filestat['mode'])));
	}
	$WORKING['STEPDONE']=1;
	$WORKING['STEPSDONE'][]='WP_EXPORT'; //set done
}