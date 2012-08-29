<?php

class PodangoAPI
{
	var $serviceUrl = 'http://www.podango.com/api/';
	var $fileUploader = 'http://www.podango.com/podcasts/file_upload.php';
	var $mediaTrackerURL = 'http://download.podango.com/mediatracker/';
	var $responseCode;
	var $responseString;
	var $parsed_xml;
	
	var $accessKey;
	var $secretKey;
	var $defaultPodcast = '##ALL##';
	var $defaultTranscribe = 0;

	function PodangoAPI($accessKey, $secretKey)
	{
		$this->accessKey = $accessKey;
		$this->secretKey = $secretKey;
	}
	
	/*************************************************************/
	/* Functions for managing the users podcasts                 */
	/*************************************************************/

	function GetPodcasts($onlyBasics = false) {
		$this->_GetPodcasts('##ALL##', $onlyBasics);
		return $this->_ParsePodcasts($this->parsed_xml['Response']['_c']['User']['_c']['Podcast']);
	}
	
	function GetPodcast($podcastId, $onlyBasics = false) {
		$this->_GetPodcasts($podcastId);
		$result = $this->_ParsePodcasts($this->parsed_xml['Response']['_c']['Podcast'], $onlyBasics);
		return $result[$podcastId];
	}

	function CreatePodcast($title, $description)
	{	
		$params = array();
		$params['Title'] = $title;
		$params['Description'] = $description;		
		$result = $this->_call('podcast/', 'PUT', $params);
		if ($this->responseCode != 200) {
			return false;
		}
		return true;
	}
	
	function UpdatePodcast($podcastId, $title, $description)
	{	
		$params = array();
		$params['PodcastID'] = $podcastId;			
		$params['Title'] = $title;
		$params['Description'] = $description;
		$result = $this->_call('podcast/', 'POST', $params);
		if ($this->responseCode != 200) 
			return false;
			
		return true;
	}	
	
	function DeletePodcast($podcastId)
	{	
		$params = array();				
		$params['PodcastID'] = $podcastId;
		$result = $this->_call('podcast/', 'DELETE', $params);
		if ($this->responseCode != 200) {
			return false;
		}
		return true;
	}

	function _GetPodcasts($podcastId = '##NOTSET##') {
		$params = array();
		if($podcastId == '##NOTSET##') {
			$podcastId = $this->defaultPodcast;
		}
		if($podcastId != '##ALL##') {
			$params['PodcastID'] = $podcastId;
		}

		$result = $this->_call('podcast/', 'GET', $params);
		if ($this->responseCode != 200) {
			return false;
		}	
		return true;
	}

	/*************************************************************/
	/* Functions for managing the users episodes                 */
	/*************************************************************/

	function GetEpisodes($requireFileURI = false) {
		$this->_GetEpisodes();
		$podcasts = $this->_ParsePodcasts($this->parsed_xml['Response']['_c']['User']['_c']['Podcast']);

		foreach($podcasts as $podcast) {
			foreach ($podcast['Episode'] as $episode)
			{
				if(!$requireFileURI || !empty($episode['FileURI'])) {
					$result[$episode['ID']] = $episode;
					$result[$episode['ID']]['Podcast'] = $podcast['ID'];
				}
			}
		}
		return $result;
	}

	function GetEpisode($episodeId) {
		$this->_GetEpisodes($episodeId);
		$result = $this->_ParseEpisode($this->parsed_xml['Response']['_c']['Podcast']['_c']);
		return $result[$episodeId];
	}

	function CreateEpisode($podcastId, $title, $description, $transcript = '', $mediafile = '', $transcribe = '')
	{		
		$params = array();	
		$params['PodcastID'] = $podcastId;
		$params['Title'] = $title;
		$params['Description'] = $description;
		if(!empty($transcript)) {
			$params['Transcript'] = $transcript;
		}
		if(!empty($mediaFileId)) {
			$params['MediaFileId'] = $mediaFileId;
		}
		if(empty($transcribe)) {
			$params['Transcribe'] = $this->defaultTranscribe;
		} else {
			$params['Transcribe'] = $transcribe;
		}
		$result = $this->_call('episode/', 'PUT', $params);
		if ($this->responseCode != 200) {
			return false;
		}
		return $this->parsed_xml['Response']['_c']['Podcast']['_c']['Episode']['_c']['ID']['_v'];
	}
	
	function UpdateEpisode($episodeId, $title, $description, $transcript = '', $mediaFileId = '', $transcribe = '')
	{
		$params = array();	
		$params['EpisodeID'] = $episodeId;
		$params['Title'] = $title;
		$params['Description'] = $description;
		if(!empty($transcript)) {
			$params['Transcript'] = $transcript;
		}
		if(!empty($mediaFileId)) {
			$params['MediaFileId'] = $mediaFileId;
		}
		if(empty($transcribe)) {
			$params['Transcribe'] = $this->defaultTranscribe;
		} else {
			$params['Transcribe'] = $transcribe;
		}
		$result = $this->_call('episode/', 'PUT', $params);
		if ($this->responseCode != 200) {
			return false;
		}	
		return true;        
	}
	
	function DeleteEpisode($episodeId) {		
		$params = array();				
		$params['EpisodeID'] = $episodeId;
		$files = array();
		$result = $this->_call('episode/', 'DELETE', $params, $files);
		if ($this->responseCode != 200) {
			return false;
		}	
		return true;
	}

	function _GetEpisodes($episodeId = '##NOTSET##') {
		$params = array();
		if($podcastId == '##NOTSET##') {
			$podcastId = $this->defaultPodcast;
		}
		if($episodeId != '##ALL##') {
			$params['EpisodeID'] = $episodeId;
		}
		$result = $this->_call('episode/', 'GET', $params);
		if ($this->responseCode != 200) {
			return false;
		}
		return true;
	}

	/*************************************************************/
	/* Functions for managing the users media files              */
	/*************************************************************/

	function GetMediaFiles($podcastId = '##NOTSET##') {
		if($podcastId == '##NOTSET##') {
			$podcastId = $this->defaultPodcast;
		}

		$this->_GetMediaFiles($podcastId);
		if(isset($this->parsed_xml['Response']['_c']['User']['_c']['Podcast'])) {
			$podcasts = $this->_ParsePodcasts($this->parsed_xml['Response']['_c']['User']['_c']['Podcast']);
		} else {
			$podcasts = $this->_ParsePodcasts($this->parsed_xml['Response']['_c']['Podcast']);
		}

		foreach($podcasts as $podcast) {
			foreach ($podcast['MediaFile'] as $mediaFile)
			{
				$result[$mediaFile['ID']] = $mediaFile;
				$result[$mediaFile['ID']]['Podcast'] = $podcast['ID'];
			}
		}
		return $result;
	}

	function GetMediaFile($mediaFileId) {
		// temp solution while I work out some other bugs
		$result = $this->GetMediaFiles();
		return array($mediaFileId=>$result[$mediaFileId]);

		// Heres a start at the more correct implementation
		$this->_GetMediaFiles('##NOTSET##');
		$result = $this->_ParseEpisode($this->parsed_xml['Response']['_c']['Podcast']['_c']);
		return $result[$episodeId];
	}

	function GetMediaFileID($podcastId, $fileName) {
		$params = array();
		$params['PodcastID'] = $podcastId;
		$params['FileName'] = $fileName;
		$result = $this->_call('media_file/', 'GET', $params);
		if ($this->responseCode != 200) {
			return false;
		}
		return $this->parsed_xml['Response']['_c']['User']['_c']['Podcast']['_c']['MediaFile']['_c']['ID']['_v'];
	}

	/* Not yet implemented
	function CreateMediaFile($podcastId, $mediafile)
	{		
		$filename = basename($filePath);
		$params = array();	
		$params['EpisodeID'] = $episodeId;
		$params['Filename'] = $filename;		

		$fsize = filesize($filePath);		
		$readStream = new PodangoStreamReader();		
		$readStream->readHandle = fopen($filePath, "r");
		
		$this->_putObjectStream($params, 'media_file/', array($readStream, "stream_function"), "binary/octet-stream", $fsize);
	
		if ($this->responseCode != 200) {
			return false;
		}
		return true;
	}
	
	function UpdateMediaFile($mediaFileId, $content)
	{
		$params = array();	
		$params['MediaFileId'] = $mediaFileId;
		if ($filename != "") {		
			$filename = basename($filename);
			$params['Filename'] = $filename;
		}
		$result = $this->_call('media_file/', 'PUT', $params);
		if ($this->responseCode != 200) {
			return false;
		}	
		return true;        
	}
	*/

	function DeleteMediaFile($mediaFileId) {		
		$params = array();				
		$params['MediaFileId'] = $mediaFileId;
		$result = $this->_call('media_file/', 'DELETE', $params);
		if ($this->responseCode != 200) {
			return false;
		}	
		return true;
	}

	function _GetMediaFiles($podcastId = '##NOTSET##') {
		$params = array();
		if($podcastId == '##NOTSET##') {
			$podcastId = $this->defaultPodcast;
		}
		if($podcastId != '##ALL##') {
			$params['PodcastID'] = $podcastId;
		}
		$result = $this->_call('media_file/', 'GET', $params);
		if ($this->responseCode != 200) {
			return false;
		}
		return true;
	}

	/*************************************************************/
	/* Parsing functions to handle the API responses             */
	/*************************************************************/
	function _ParsePodcasts($input, $onlyBasics = false) {
		$result = array();
		if (is_array($input)) {
			if (isset($input['_c'])) {
				$input[] = $input;
			}
			foreach ($input as $podcast) {
				if (isset($podcast['_a']['ID'])) {
					$id = $podcast['_a']['ID'];
				} else {
					$id = $podcast['_c']['ID']['_v'];
				}
				if(!empty($id)) {
					$result[$id] = array();
					$result[$id]['ID'] = $id;
					if(isset($podcast['_c']['Title'])) {
						$result[$id]['Title'] = $podcast['_c']['Title']['_v'];
					}
					if(isset($podcast['_c']['Description'])) {
						$result[$id]['Description'] = $podcast['_c']['Description']['_v'];
					}
					if(isset($podcast['_c']['RSSFeed'])) {
						$result[$id]['RSSFeed'] = $podcast['_c']['RSSFeed']['_v'];
					}
					if(isset($podcast['_c']['Keywords'])) {
						$result[$id]['Keywords'] = $podcast['_c']['Keywords']['_v'];
					}
					if(isset($podcast['_c']['Explicit'])) {
						$result[$id]['Explicit'] = $podcast['_c']['Explicit']['_v'];
					}
					if(!$onlyBasics) {
						if(isset($podcast['_c']['Episode'])) {
							$result[$id]['Episode'] = $this->_ParseEpisodes($podcast['_c']['Episode']);
						}
						if(isset($podcast['_c']['MediaFile'])) {
							$result[$id]['MediaFile'] = $this->_ParseMediaFiles($podcast['_c']['MediaFile']);
						}
					}
				}
			}
		}
		return $result;
	}

	function _ParseEpisodes($input) {
		if (is_array($input)) {
			if (isset($input['_c'])) {
				$input[] = $input;
			}
			return $this->_ParseEpisode($input);
		}
		return array();
	}

	function _ParseEpisode($input) {
		$result = array();
		if (is_array($input)) {
			if (isset($input['_c'])) {
				$input[] = $input;
			}
			foreach ($input as $episode) {
				if (isset($episode['_a']['ID'])) {
					$id = $episode['_a']['ID'];
				} else {
					$id = $episode['_c']['ID']['_v'];
				}
				if(!empty($id)) {
					$result[$id]['ID'] = $id;
					if(isset($episode['_c']['Title'])) {
						$result[$id]['Title'] = $episode['_c']['Title']['_v'];
					}
					if(isset($episode['_c']['Date'])) {
						$result[$id]['Date'] = $episode['_c']['Date']['_v'];
					}
					if(isset($episode['_c']['Description'])) {
						$result[$id]['Description'] = $episode['_c']['Description']['_v'];
					}
					if(isset($episode['_c']['Transcript'])) {
						$result[$id]['Transcript'] = $episode['_c']['Transcript']['_v'];
					}
					if(isset($episode['_c']['MediaFileId'])) {
						$result[$id]['MediaFileId'] = $episode['_c']['MediaFileId']['_v'];
					}
					if(isset($episode['_c']['FileURI']) && $episode['_c']['FileURI']['_v'] != 'sucksforyou-no-url') {
						$result[$id]['FileURI'] = $episode['_c']['FileURI']['_v'];
						$result[$id]['Filename'] = basename($episode['_c']['FileURI']['_v']);
					}
					if(isset($episode['_c']['Length'])) {
						$result[$id]['Length'] = $episode['_c']['Length']['_v'];
					}
					if(isset($episode['_c']['FileSize'])) {
						$result[$id]['FileSize'] = $episode['_c']['FileSize']['_v'];
					}
					if(isset($episode['_c']['ImageURI'])) {
						$result[$id]['ImageURI'] = $episode['_c']['ImageURI']['_v'];
					}
					if(isset($episode['_c']['Status'])) {
						$result[$id]['Status'] = $episode['_c']['Status']['_v'];
					}
				}
			}
		}
		return $result;
	}

	function _ParseMediaFiles($input) {
		if (is_array($input)) {
			if (isset($input['_c'])) {
				$input[] = $input;
			}
			return $this->_ParseMediaFile($input);
		}
		return array();
	}

	function _ParseMediaFile($input) {
		$result = array();
		if (is_array($input)) {
			if (isset($input['_c'])) {
				$input[] = $input;
			}
			foreach ($input as $mediaFile) {
				if (isset($mediaFile['_a']['ID'])) {
					$id = $mediaFile['_a']['ID'];
				} else {
					$id = $mediaFile['_c']['ID']['_v'];
				}
				if(!empty($id)) {
					$result[$id]['ID'] = $id;
					if(isset($mediaFile['_c']['Filename'])) {
						$result[$id]['Filename'] = $mediaFile['_c']['Filename']['_v'];
					}
					if(isset($mediaFile['_c']['FileURI'])) {
						$result[$id]['FileURI'] = $mediaFile['_c']['FileURI']['_v'];
					}
					if(empty($result[$id]['Filename'])) {
						$result[$id]['Filename'] = basename($result[$id]['FileURI']);
					}
					if(isset($mediaFile['_c']['ByteSize'])) {
						$result[$id]['ByteSize'] = $mediaFile['_c']['ByteSize']['_v'];
					}
					if(isset($mediaFile['_c']['Episode']['_c']['ID'])) {
						$result[$id]['EpisodeID'] = $mediaFile['_c']['Episode']['_c']['ID']['_v'];
					}
					if(isset($mediaFile['_c']['Episode']['_c']['Title'])) {
						$result[$id]['EpisodeTitle'] = $mediaFile['_c']['Episode']['_c']['Title']['_v'];
					}
				}
			}
		}
		return $result;
	}


	/*************************************************************/
	/* Functions to handle the actual API calls                  */
	/*************************************************************/
	function _call($URIFolder, $method, $params, $files = '##NOTSET##')
	{
		if (!isset($params)) {
			$params = array();
		}

		if ($files == '##NOTSET##') {
			$files = array();
		}

		$timestamp = gmdate('Y-m-d\TH:i:s\Z');
		
		$stringToSign = $method.$timestamp.$this->accessKey; 
		
		// Add Actions
		//$params['Action'] = $action;
		$params['Version'] = '2007-01-15';
		$params['AccessKey'] = $this->accessKey;
		$params['Authorization'] =  $this->_constructSig($stringToSign);
		$params['Timestamp'] = 	$timestamp;
		
		$request = '';
		foreach ($params as $name => $value) {
			$request .= $name . '=' .  urlencode($value) . '&';
		}
		$this->_pchop($request);
		$req = & new PodangoHTTPRequest($this->serviceUrl.$URIFolder);
		$req->setMethod($method);	
		$req->setQueryString($request);
		
		$this->responseString = $req->DownloadToString();	
		$this->responseCode = $req->getResponseCode();
		$this->parsed_xml = $this->_getParsedXMLString($this->responseString);
	}	
	
	function _hex2b64($str)
	{
		$raw = '';
		for ($i=0; $i < strlen($str); $i+=2) {
			$raw .= chr(hexdec(substr($str, $i, 2)));
		}
		return base64_encode($raw);
	}
		 
	function _constructSig($str)
	{
		return $this->_hex2b64($this->_cryptHMAC($this->secretKey, $str));
	}	
	
	/**
	* implementation of Perl's "chop" function
	*/
	function _pchop($string) {
		if (is_array($string)) {
			foreach($string as $i => $val) {
				$endchar = $this->_pchop($string[$i]);
			}
		} else {
			$endchar = substr("$string", strlen("$string") - 1, 1);
			$string = substr("$string", 0, -1);
		}
		return $endchar;
	}
	
	function _putObjectStream($params, $URIFolder, $streamFunction, $contentType, $contentLength)
	{
		$timestamp = gmdate('Y-m-d\TH:i:s\Z');
		$stringToSign = 'PUT'.$timestamp.$this->accessKey;         
		$params['Version'] = '2007-01-15';
		$params['AccessKey'] = $this->accessKey;
		$params['Authorization'] =  $this->_constructSig($stringToSign);
		$params['Timestamp'] = 	$timestamp;
		
		$request = '';
		foreach ($params as $name => $value) {
			$request .= $name . '=' .  urlencode($value) . '&';
		}
		$this->_pchop($request);

        $curl_inst = curl_init();

        curl_setopt ($curl_inst, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt ($curl_inst, CURLOPT_LOW_SPEED_LIMIT, 1);
        curl_setopt ($curl_inst, CURLOPT_LOW_SPEED_TIME, 180);
        curl_setopt ($curl_inst, CURLOPT_NOSIGNAL, 1);
        curl_setopt ($curl_inst, CURLOPT_READFUNCTION, $streamFunction);
        curl_setopt ($curl_inst, CURLOPT_URL, $this->serviceUrl.$URIFolder.'?'.$request);
        curl_setopt ($curl_inst, CURLOPT_UPLOAD, true);
        curl_setopt ($curl_inst, CURLINFO_CONTENT_LENGTH_UPLOAD, $contentLength);
		
        $header[] = "Date: $timestamp";
        $header[] = "Content-Type: $contentType";
        $header[] = "Content-Length: $contentLength";
        $header[] = "Expect: ";
        $header[] = "Transfer-Encoding: ";
        

        curl_setopt($curl_inst, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_inst, CURLOPT_RETURNTRANSFER, 1);
		
		$this->responseString = curl_exec ($curl_inst);
		$this->responseCode = curl_getinfo($curl_inst, CURLINFO_HTTP_CODE);
		$this->parsed_xml = $this->_getParsedXMLString($this->responseString);   

        curl_close($curl_inst);
	}
 	/* function to generate HMAC hash */
	function _cryptHMAC($key, $data) {
		$func = 'sha1';
		$pack = 'H40';
		if (strlen($key) > 64) {
			$key =  pack($pack, $func($key));
		}
		if (strlen($key) < 64) {
			$key = str_pad($key, 64, chr(0));
		}
		$ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
		$opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));
		return $func($opad . pack($pack, $func($ipad . $data)));
	}

	// _Internal: Remove recursion in result array
	function _del_p($ary) {
		foreach ($ary as $k=>$v) {
			if ($k==='_p') { 
				unset($ary[$k]); 
			} elseif (is_array($ary[$k])) {
				$this->_del_p($ary[$k]);
			}
		}
	}


	function _getParsedXMLString($xml) {
		/*Format XML->Array
			_c - children
			_v - value
			_a - attributes
		*/

		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $xml, $vals, $index);
		xml_parser_free($parser);

		$mnary=array();
		$ary=&$mnary;
		foreach ($vals as $r) {
			$t=$r['tag'];
			if ($r['type']=='open') {
				if (isset($ary[$t])) {
					if (isset($ary[$t][0])) {
						$ary[$t][]=array();
					} else {
						$ary[$t]=array($ary[$t], array());
					}
					$cv=&$ary[$t][count($ary[$t])-1];
				} else $cv=&$ary[$t];
					if (isset($r['attributes'])) {
						foreach ($r['attributes'] as $k=>$v) {
							$cv['_a'][$k]=$v;
						}
					}
					$cv['_c']=array();
					$cv['_c']['_p']=&$ary;
					$ary=&$cv['_c'];
			} elseif ($r['type']=='complete') {
				if (isset($ary[$t])) { // same as open
                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                $cv=&$ary[$t][count($ary[$t])-1];
				} else $cv=&$ary[$t]; {
					if (isset($r['attributes'])) {
						foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;
					}
					$cv['_v']=(isset($r['value']) ? $r['value'] : '');
				}
			} elseif ($r['type']=='close') {
				$ary=&$ary['_p'];
			}
    }    
		
		$this->_del_p($mnary);
		return $mnary;
	}
}
 
/*************************************************************/
/* Support classes for specific tasks                        */
/*************************************************************/
class PodangoStreamReader
{
	var $readHandle;
	
	function stream_function($handle, $fd, $length)
	{
		return fread($this->readHandle, $length);
	}
}

class PodangoHTTPRequest
{
	var $_fp;        // HTTP socket
	var $_url;        // full URL
	var $_method;        // HTTP method
	var $_host;        // HTTP host
	var $_protocol;    // protocol (HTTP/HTTPS)
	var $_uri;        // request URI
	var $_port;        // port
	var $_GET_params;
	var $_POST_params;
	var $_raw_request;
	var $_headers;
 
	// constructor
	function PodangoHTTPRequest($url) {
		$this->_url = $url;
		$this->_scan_url();
	}

	function setMethod($method) {
		$this->_method = $method;
	}

	function setQueryString($str) {
		$this->_GET_params = $str;
	}

	function setPostData($str) {
		$this->_POST_params = $str;
	}

	function getResponseCode() {
		$pos = strpos($this->_headers['status'], ' ');
		return substr($this->_headers['status'], $pos+1, 3);
	}

	// scan url
	function _scan_url()
	{
		$req = $this->_url;
    
		$pos = strpos($req, '://');
		$this->_protocol = strtolower(substr($req, 0, $pos));
     
		$req = substr($req, $pos+3);
		$pos = strpos($req, '/');
		if($pos === false) {
			$pos = strlen($req);
		}
		$host = substr($req, 0, $pos);
    
		if(strpos($host, ':') !== false) {
			list($this->_host, $this->_port) = explode(':', $host);
		} else {
			$this->_host = $host;
			$this->_port = ($this->_protocol == 'https') ? 443 : 80;
		}
     
		$this->_uri = substr($req, $pos);
		if($this->_uri == '') {
			$this->_uri = '/';
		}
	}
	function parse_http_headers(){
		if($this->_raw_header === false){
			return false;
		}
		$this->_raw_header = str_replace("\r","",$this->_raw_header);
		$headers = explode("\n",$this->_raw_header);
		foreach($headers as $value){
			$header = explode(": ",$value);
			if($header[0] && !$header[1]){
				$headerdata['status'] = $header[0];
			} elseif($header[0] && $header[1]) {
				$headerdata[$header[0]] = $header[1];
			}
		}
		$this->_headers = $headerdata;
		return $this->_headers;
	}


	// download URL to string
	function DownloadToString() {
		$crlf = "\r\n";
    
		// generate request
		$this->_raw_request = $this->_method.' '.$this->_uri;
		if(!empty($this->_GET_params)) {
			$this->_raw_request .= '?'.$this->_GET_params;
		}
		$this->_raw_request .= ' HTTP/1.0'.$crlf.'Host: '.$this->_host.$crlf.$crlf;
		if(!empty($this->_POST_params)) {
			$this->_raw_request .= '?'.$this->_GET_params;
		}
		// fetch
		$this->_fp = fsockopen(($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host, $this->_port);
		fwrite($this->_fp, $this->_raw_request);
		while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp)) {
			$response .= fread($this->_fp, 1024);
		}
		fclose($this->_fp);
    
		// split header and body
		$pos = strpos($response, $crlf . $crlf);
		if($pos === false) {
			return($response);
		}
		$this->_raw_header = substr($response, 0, $pos);
		$body = substr($response, $pos + 2 * strlen($crlf));
    
		// parse headers
		$headers = array();
		$lines = explode($crlf, $this->_raw_header);
		foreach($lines as $line) {
			if(($pos = strpos($line, ':')) !== false) {
				$headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));
			}
		}

		$this->_headers = $this->parse_http_headers();

		// redirection?
		if(isset($headers['location'])) {
			if(substr($headers['location'],0,1) == '/') {
				$headers['location'] = $this->_protocol . '://' . $this->_host . $headers['location'];
			}
			$http = new PodangoHTTPRequest($headers['location']);
			return($http->DownloadToString());
		} else {
				return($body);
		}
	}
}
