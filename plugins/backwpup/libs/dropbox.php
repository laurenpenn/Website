<?PHP

/**
 * Dropbox class
 *
 * This source file can be used to communicate with DropBox (http://dropbox.com)
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them.
 * If you report a bug, make sure you give me enough information (include your code).
 *
 *
 *
 * License
 * Copyright (c), Daniel Huesken. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 * @author		Daniel Huesken <daniel@huesken-net.de>
 * @version		2.0.2
 *
 * @copyright	Copyright (c), Daniel Huesken. All rights reserved.
 * @license		GPL3 License
 */

class backwpup_Dropbox {
	const API_URL = 'https://api.dropbox.com/';
	const API_CONTENT_URL = 'https://api-content.dropbox.com/';
	const API_WWW_URL = 'https://www.dropbox.com/';
	const API_VERSION_URL = '1/';

	private $root = 'dropbox';
	private $ProgressFunction = false;
	private $oauth_app_key ='q2jbt0unkkc54u2';
    private $oauth_app_secret ='t5hlbxtz473hchy';
    private $oauth_token  ='';
    private $oauth_token_secret ='';

	public function __construct($dropbox=false) {
		if ($dropbox)
			$this->root = 'dropbox';
		else
			$this->root = 'sandbox';
	}

	public function setOAuthTokens($token,$secret) {
        $this->oauth_token          = $token;
        $this->oauth_token_secret   = $secret;
	}

	public function setProgressFunction($function=null) {
		if (function_exists($function))
			$this->ProgressFunction = $function;
		else
			$this->ProgressFunction = false;
	}

	public function accountInfo(){
		$url = self::API_URL.self::API_VERSION_URL.'account/info';
		return $this->request($url);
	}

	public function upload($file, $path = '',$overwrite=true){
		$file = str_replace("\\", "/",$file);
		if (!is_readable($file) or !is_file($file))
			throw new backwpup_DropboxException("Error: File \"$file\" is not readable or doesn't exist.");
        $filesize=filesize($file);
        if ($filesize<41943040) {  //chunk transfer on bigger uploads <40MB
            $filehandel = fopen($file,'r');
            $url = self::API_CONTENT_URL.self::API_VERSION_URL.'files_put/'.$this->root.'/'.trim($path, '/');
            $output = $this->request($url, array('overwrite' => ($overwrite)? 'true' : 'false'), 'PUT', $filehandel, $filesize);
            fclose($filehandel);
        } else {
            $output = $this->chunked_upload($file, $path,$overwrite);
        }
        return $output;
	}

    public function chunked_upload($file, $path = '',$overwrite=true){
        $file = str_replace("\\", "/",$file);
        if (!is_readable($file) or !is_file($file))
            throw new backwpup_DropboxException("Error: File \"$file\" is not readable or doesn't exist.");
        $file_handel=fopen($file,'r');
        $uploadid=null;
        $offset=0;
        $ProgressFunction=null;
        while ($data=fread($file_handel,4194304)) {  //4194304 = 4MB
            $chunkHandle = fopen('php://temp', 'w+');
            fwrite($chunkHandle,$data);
            rewind($chunkHandle);
            //overwrite progress function
            if (!empty($this->ProgressFunction) and function_exists($this->ProgressFunction)) {
                $ProgressFunction=$this->ProgressFunction;
                $this->ProgressFunction=false;
            }
            $url = self::API_CONTENT_URL.self::API_VERSION_URL.'chunked_upload';
            $output = $this->request($url, array('upload_id' => $uploadid,'offset'=>$offset), 'PUT', $chunkHandle, strlen($data));
            fclose($chunkHandle);
            if ($ProgressFunction) {
                call_user_func($ProgressFunction,0,0,0,$offset);
                $this->ProgressFunction=$ProgressFunction;
            }
            //args for next chunk
            $offset=$output['offset'];
            $uploadid=$output['upload_id'];
            fseek($file_handel,$offset);
        }
        fclose($file_handel);
        $url = self::API_CONTENT_URL.self::API_VERSION_URL.'commit_chunked_upload/'.$this->root.'/'.trim($path, '/');
        return $this->request($url, array('overwrite' => ($overwrite)? 'true' : 'false','upload_id'=>$uploadid), 'POST');
    }

	public function download($path,$echo=false){
		$url = self::API_CONTENT_URL.self::API_VERSION_URL.'files/'.$this->root.'/'.trim($path,'/');
		if (!$echo)
			return $this->request($url);
		else
			$this->request($url,'','GET','','',true);
	}

	public function metadata($path = '', $listContents = true, $fileLimit = 10000){
		$url = self::API_URL.self::API_VERSION_URL.'metadata/'.$this->root.'/'.trim($path,'/');
		return $this->request($url, array('list' => ($listContents)? 'true' : 'false', 'file_limit' => $fileLimit));
	}

	public function search($path = '', $query , $fileLimit = 1000){
		if (strlen($query)>=3)
			throw new backwpup_DropboxException("Error: Query \"$query\" must three characters long.");
		$url = self::API_URL.self::API_VERSION_URL.'search/'.$this->root.'/'.trim($path,'/');
		return $this->request($url, array('query' => $query, 'file_limit' => $fileLimit));
	}

	public function shares($path = ''){
		$url = self::API_URL.self::API_VERSION_URL.'shares/'.$this->root.'/'.trim($path,'/');
		return $this->request($url);
	}

	public function media($path = ''){
		$url = self::API_URL.self::API_VERSION_URL.'media/'.$this->root.'/'.trim($path,'/');
		return $this->request($url);
	}

	public function fileopsDelete($path){
		$url = self::API_URL.self::API_VERSION_URL.'fileops/delete';
		return $this->request($url, array('path' => '/'.trim($path,'/'), 'root' => $this->root));
	}

	public function fileopsCreate_folder($path){
		$url = self::API_URL.self::API_VERSION_URL.'fileops/create_folder';
		return $this->request($url, array('path' => '/'.trim($path,'/'), 'root' => $this->root));
	}

	public function oAuthAuthorize($callback_url) {
        $headers[] = 'Authorization: OAuth oauth_version="1.0", oauth_signature_method="PLAINTEXT", oauth_consumer_key="'.$this->oauth_app_key.'", oauth_signature="'.$this->oauth_app_secret.'&"';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::API_URL . self::API_VERSION_URL . 'oauth/request_token' );
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_SSLVERSION,3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		if (is_file(dirname(__FILE__).'/aws/lib/requestcore/cacert.pem'))
			curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/aws/lib/requestcore/cacert.pem');
		curl_setopt($ch, CURLOPT_AUTOREFERER , true);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($status>=200 and $status<300 and 0==curl_errno($ch) ) {
			parse_str($content, $oauth_token);
		} else {
			$output = json_decode($content, true);
			if(isset($output['error']) && is_string($output['error'])) $message = $output['error'];
			elseif(isset($output['error']['hash']) && $output['error']['hash'] != '') $message = (string) $output['error']['hash'];
			elseif (0!=curl_errno($ch)) $message = '('.curl_errno($ch).') '.curl_error($ch);
			else $message = '('.$status.') Invalid response.';
			throw new backwpup_DropboxException($message);
		}
		curl_close($ch);
		return array( 'authurl'		   => self::API_WWW_URL . self::API_VERSION_URL . 'oauth/authorize?oauth_token='.$oauth_token['oauth_token'].'&oauth_callback='.urlencode($callback_url),
					  'oauth_token'	   => $oauth_token['oauth_token'],
					  'oauth_token_secret'=> $oauth_token['oauth_token_secret'] );
	}

	public function oAuthAccessToken($oauth_token, $oauth_token_secret) {
		$headers[] = 'Authorization: OAuth oauth_version="1.0", oauth_signature_method="PLAINTEXT", oauth_consumer_key="'.$this->oauth_app_key.'", oauth_token="'.$oauth_token.'", oauth_signature="'.$this->oauth_app_secret.'&'.$oauth_token_secret.'"';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::API_URL . self::API_VERSION_URL . 'oauth/access_token' );
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_SSLVERSION,3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		if (is_file(dirname(__FILE__).'/aws/lib/requestcore/cacert.pem'))
			curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/aws/lib/requestcore/cacert.pem');
		curl_setopt($ch, CURLOPT_AUTOREFERER , true);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($status>=200 and $status<300  and 0==curl_errno($ch)) {
			parse_str($content, $oauth_token);
			$this->setOAuthTokens($oauth_token['oauth_token'], $oauth_token['oauth_token_secret']);
			return $oauth_token;
		} else {
			$output = json_decode($content, true);
			if(isset($output['error']) && is_string($output['error'])) $message = $output['error'];
			elseif(isset($output['error']['hash']) && $output['error']['hash'] != '') $message = (string) $output['error']['hash'];
			elseif (0!=curl_errno($ch)) $message = '('.curl_errno($ch).') '.curl_error($ch);
			else $message = '('.$status.') Invalid response.';
			throw new backwpup_DropboxException($message);
		}
	}

	private function request($url, $args = null, $method = 'GET', $filehandel = null, $filesize=0, $echo=false){
		$args = (is_array($args)) ? $args : array();
		$url = $this->url_encode($url);

		/* Header*/
		$headers[]='Authorization: OAuth oauth_version="1.0", oauth_signature_method="PLAINTEXT", oauth_consumer_key="'.$this->oauth_app_key.'", oauth_token="'.$this->oauth_token.'", oauth_signature="'.$this->oauth_app_secret.'&'.$this->oauth_token_secret.'"';
		$headers[]='Expect:';

		/* Build cURL Request */
		$ch = curl_init();
		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
			curl_setopt($ch, CURLOPT_URL, $url);
		} elseif ($method == 'PUT') {
			curl_setopt($ch,CURLOPT_PUT,true);
			curl_setopt($ch,CURLOPT_INFILE,$filehandel);
			curl_setopt($ch,CURLOPT_INFILESIZE,$filesize);
			$args = (is_array($args)) ? '?'.http_build_query($args, '', '&') : $args;
			curl_setopt($ch, CURLOPT_URL, $url.$args);
		} else {
			$args = (is_array($args)) ? '?'.http_build_query($args, '', '&') : $args;
			curl_setopt($ch, CURLOPT_URL, $url.$args);
		}
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSLVERSION,3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		if (is_file(dirname(__FILE__).'/aws/lib/requestcore/cacert.pem'))
			curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/aws/lib/requestcore/cacert.pem');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if (!empty($this->ProgressFunction) and function_exists($this->ProgressFunction) and defined('CURLOPT_PROGRESSFUNCTION') and $method == 'PUT') {
			curl_setopt($ch, CURLOPT_NOPROGRESS, false);
			curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $this->ProgressFunction);
			curl_setopt($ch, CURLOPT_BUFFERSIZE, 512);
		}
		if ($echo) {
			echo curl_exec($ch);
			$output='';
		} else {
            curl_setopt( $ch, CURLOPT_HEADER, true );
            list($header,$content) = explode("\r\n\r\n", curl_exec( $ch ), 2);
			$output = json_decode($content, true);
		}
		$status = curl_getinfo($ch);

        if ( $status['http_code'] == 503 )  {
            $wait=1;
			if ( preg_match( '/Retry-After:(.*?)\r/i', $header, $matches ) )
                $wait = trim($matches[1]);
			//trigger_error($header,E_WARNING);
            trigger_error(sprintf('(503) Your app is making too many requests and is being rate limited. 503s can trigger on a per-app or per-user basis. Wait for %d seconds.',$wait),E_USER_WARNING);
            sleep($wait);
            return $this->request( $url, $args, $method, $filehandel, $filesize, $echo );
        } elseif (isset($output['error']) or $status['http_code']>=300 or $status['http_code']<200 or curl_errno($ch)>0) {
			if(isset($output['error']) && is_string($output['error'])) $message = '('.$status['http_code'].') '.$output['error'];
			elseif(isset($output['error']['hash']) && $output['error']['hash'] != '') $message = (string) '('.$status['http_code'].') '.$output['error']['hash'];
			elseif (0!=curl_errno($ch)) $message = '('.curl_errno($ch).') '.curl_error($ch);
			elseif ($status['http_code']==304) $message = '(304) The folder contents have not changed (relies on hash parameter).';
			elseif ($status['http_code']==400) $message = '(400) Bad input parameter: '.strip_tags($content);
			elseif ($status['http_code']==401) $message = '(401) Bad or expired token. This can happen if the user or Dropbox revoked or expired an access token. To fix, you should re-authenticate the user.';
			elseif ($status['http_code']==403) $message = '(403) Bad OAuth request (wrong consumer key, bad nonce, expired timestamp, ...). Unfortunately, reauthenticating the user won\'t help here.';
			elseif ($status['http_code']==404) $message = '(404) The file was not found at the specified path, or was not found at the specified rev.';
			elseif ($status['http_code']==405) $message = '(405) Request method not expected (generally should be GET,PUT or POST).';
			elseif ($status['http_code']==406) $message = '(406) There are too many file entries to return.';
			elseif ($status['http_code']==411) $message = '(411) Chunked encoding was attempted for this upload, but is not supported by Dropbox.';
			elseif ($status['http_code']==415) $message = '(415) The image is invalid and cannot be thumbnailed.';
			elseif ($status['http_code']==503) $message = '(503) Your app is making too many requests and is being rate limited. 503s can trigger on a per-app or per-user basis.';
			elseif ($status['http_code']==507) $message = '(507) User is over Dropbox storage quota.';
			else $message = '('.$status['http_code'].') Invalid response.';
			throw new backwpup_DropboxException($message);
		} else {
			curl_close($ch);
			if (!is_array($output))
				return $content;
			else
				return $output;
		}
	}

	private function url_encode($string) {
		$string = str_replace('?','%3F',$string);
		$string = str_replace('=','%3D',$string);
		$string = str_replace(' ','%20',$string);
		$string = str_replace('(','%28',$string);
		$string = str_replace(')','%29',$string);
		$string = str_replace('&','%26',$string);
		$string = str_replace('@','%40',$string);
		return $string;
	}

}

class backwpup_DropboxException extends Exception {
}
