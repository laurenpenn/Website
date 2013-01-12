<?php

/**
 * SugarSync class
 *
 * This source file can be used to communicate with SugarSync (http://sugarsync.com)
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them.
 * If you report a bug, make sure you give me enough information (include your code).
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
 * @version		2.0.0
 *
 * @copyright	Copyright (c), Daniel Huesken. All rights reserved.
 * @license		GPL3 License
 */

class SugarSync {


	// url for the sugarsync-api
	const API_URL = 'https://api.sugarsync.com';
	
	const PRIVATE_ACCESS_KEY= 'NzNmNDMwMDBiNTkwNDY0YzhjY2JiN2E5YWVkMjFmYmI';
	
	const ACCESS_KEY_ID = 'OTcwNjc5MTI5OTQxMzY1Njc5OA';
	 
	const APP_ID = '/sc/970679/36_3392235662';


	protected $folder = '';

	protected $ProgressFunction = false;

	protected $encoding = 'UTF-8';

    protected $refresh_token = '';

    protected $access_token = '';

	public function __construct($refresh_token=null ) {
		//auth xml
		$this->encoding= mb_internal_encoding();
        //get access token
        if (isset($refresh_token) and !empty($refresh_token)) {
            $this->refresh_token=$refresh_token;
            $this->get_Access_Token();
        }
	}

    /**
     * Make the call
     *
     * @return    string
     *
     * @param    string $url                        The url to call.
     * @param string $data
     * @param string $method
     * @throws SugarSyncException
     * @internal param $string [optiona] $data            File on put, xml on post.
     * @internal param $string [optional] $method        The method to use. Possible values are GET, POST, PUT, DELETE.
     */
	private function doCall( $url, $data = '', $method = 'GET' ) {
		// allowed methods
		$allowedMethods = array( 'GET', 'POST', 'PUT', 'DELETE' );

		// redefine
		$url    = (string) $url;
		$method = (string) $method;

		// validate method
		if ( ! in_array( $method, $allowedMethods ) )
			throw new SugarSyncException('Unknown method (' . $method . '). Allowed methods are: ' . implode( ', ', $allowedMethods ));

		// check auth token
		if ( empty($this->access_token) )
			throw new SugarSyncException('Auth Token not set correctly!!');
		else
			$headers[] = 'Authorization: ' . $this->access_token;
		$headers[] = 'Expect:';

		// init
		$curl = curl_init();
		//set otions
		curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_USERAGENT,'BackWPup');
        if ( ini_get( 'open_basedir' ) == '' && ini_get( 'safe_mode' == 'Off' ) ) curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt( $curl, CURLOPT_SSLVERSION, 3 );
		if ( is_file( dirname( __FILE__ ) . '/gd-class2-root.pem' ) )
			curl_setopt( $curl, CURLOPT_CAINFO, dirname( __FILE__ ) . '/gd-class2-root.pem' );

		if ( $method == 'POST' ) {
			$headers[] = 'Content-Type: application/xml; charset=UTF-8';
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
			curl_setopt( $curl, CURLOPT_POST, true );
			$headers[] = 'Content-Length: ' . strlen( $data );
		} elseif ( $method == 'PUT' ) {
			if ( is_file( $data ) && is_readable( $data ) ) {
				$headers[]  = 'Content-Length: ' . filesize( $data );
				$datafilefd = fopen( $data, 'r' );
				curl_setopt( $curl, CURLOPT_PUT, true );
				curl_setopt( $curl, CURLOPT_INFILE, $datafilefd );
				curl_setopt( $curl, CURLOPT_INFILESIZE, filesize( $data ) );
				if (function_exists($this->ProgressFunction) and defined('CURLOPT_PROGRESSFUNCTION')) {
					curl_setopt($curl, CURLOPT_NOPROGRESS, false);
					curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, $this->ProgressFunction);
					curl_setopt($curl, CURLOPT_BUFFERSIZE, 1048576);
				}
			} else {
				throw new SugarSyncException('Is not a readable file:' . $data);
			}
		} elseif ( $method == 'DELETE' ) {
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
		} else {
			curl_setopt( $curl, CURLOPT_POST, false );
		}

		// set headers
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $curl, CURLINFO_HEADER_OUT, true );
		// execute
		$response    = curl_exec( $curl );
		$curlgetinfo = curl_getinfo( $curl );

		// fetch curl errors
		if ( curl_errno( $curl ) != 0 )
			throw new SugarSyncException('cUrl Error: ' . curl_error( $curl ));
		curl_close( $curl );
		if ( ! empty($datafilefd) && is_resource( $datafilefd ) )
			fclose( $datafilefd );

		if ( $curlgetinfo['http_code'] >= 200 && $curlgetinfo['http_code'] < 300 ) {
			if ( false !== stripos( $curlgetinfo['content_type'], 'xml' ) && ! empty($response) )
				return simplexml_load_string( $response );
			else
				return $response;
		} else {
			if ( $curlgetinfo['http_code'] == 401 )
				throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' Authorization required.');
			elseif ( $curlgetinfo['http_code'] == 403 )
				throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' (Forbidden)  Authentication failed.');
			elseif ( $curlgetinfo['http_code'] == 404 )
				throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' Not found');
			else
				throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code']);
		}
	}

	private function _read_cb( $curl, $fd, $length ) {
		$data = fread( $fd, $length );
		$len  = strlen( $data );
		if ( isset($this->ProgressFunction) ) {
			call_user_func( $this->ProgressFunction, $len );
		}
		return $data;
	}


    private function get_Access_Token()  {
        $auth = '<?xml version="1.0" encoding="UTF-8" ?>';
        $auth .= '<tokenAuthRequest>';
        $auth .= '<accessKeyId>'.self::ACCESS_KEY_ID.'</accessKeyId>';
        $auth .= '<privateAccessKey>'.self::PRIVATE_ACCESS_KEY.'</privateAccessKey>';
        $auth .= '<refreshToken>' . trim($this->refresh_token) . '</refreshToken>';
        $auth .= '</tokenAuthRequest>';
        // init
        $curl = curl_init();
        //set options
        curl_setopt( $curl, CURLOPT_URL, self::API_URL . '/authorization' );
        curl_setopt( $curl, CURLOPT_USERAGENT, 'BackWPup');
        if ( ini_get( 'open_basedir' ) == '' && ini_get( 'safe_mode' == 'Off' ) ) curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt( $curl, CURLOPT_SSLVERSION, 3 );
        if ( is_file( dirname( __FILE__ ) . '/gd-class2-root.pem' ) )
            curl_setopt( $curl, CURLOPT_CAINFO, dirname( __FILE__ ) . '/gd-class2-root.pem' );
        curl_setopt( $curl, CURLOPT_HEADER, true );
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/xml; charset=UTF-8','Content-Length: '.strlen($auth) ) );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $auth );
        curl_setopt( $curl, CURLOPT_POST, true );
        // execute
        $response    = curl_exec( $curl );
        $curlgetinfo = curl_getinfo( $curl );
        // fetch curl errors
        if ( curl_errno( $curl ) != 0 )
            throw new SugarSyncException('cUrl Error: ' . curl_error( $curl ));

        curl_close( $curl );

        if ( $curlgetinfo['http_code'] >= 200 && $curlgetinfo['http_code'] < 300 ) {
            if ( preg_match( '/Location:(.*?)\r/i', $response, $matches ) )
                $this->access_token = trim($matches[1]);
                return $this->access_token;
        } else {
            if ( $curlgetinfo['http_code'] == 401 )
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' Authorization required.');
            elseif ( $curlgetinfo['http_code'] == 403 )
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' (Forbidden)  Authentication failed.');
            elseif ( $curlgetinfo['http_code'] == 404 )
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' Not found');
            else
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code']);
        }
    }

    public function get_Refresh_Token($email,$password)  {
        $auth = '<?xml version="1.0" encoding="UTF-8" ?>';
        $auth .= '<appAuthorization>';
        $auth .= '<username>' .mb_convert_encoding( $email,'UTF-8',$this->encoding ) . '</username>';
        $auth .= '<password>' . mb_convert_encoding( $password,'UTF-8',$this->encoding ) . '</password>';
        $auth .= '<application>'.self::APP_ID.'</application>';
        $auth .= '<accessKeyId>'.self::ACCESS_KEY_ID.'</accessKeyId>';
        $auth .= '<privateAccessKey>'.self::PRIVATE_ACCESS_KEY.'</privateAccessKey>';
        $auth .= '</appAuthorization>';
        // init
        $curl = curl_init();
        //set options
        curl_setopt( $curl, CURLOPT_URL, self::API_URL . '/app-authorization' );
        curl_setopt( $curl, CURLOPT_USERAGENT, 'BackWPup');
        if ( ini_get( 'open_basedir' ) == '' && ini_get( 'safe_mode' == 'Off' ) ) curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt( $curl, CURLOPT_SSLVERSION, 3 );
        if ( is_file( dirname( __FILE__ ) . '/gd-class2-root.pem' ) )
            curl_setopt( $curl, CURLOPT_CAINFO, dirname( __FILE__ ) . '/gd-class2-root.pem' );
        curl_setopt( $curl, CURLOPT_HEADER, true );
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/xml; charset=UTF-8','Content-Length: '.strlen($auth) ) );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $auth );
        curl_setopt( $curl, CURLOPT_POST, true );
        // execute
        $response    = curl_exec( $curl );
        $curlgetinfo = curl_getinfo( $curl );
        // fetch curl errors
        if ( curl_errno( $curl ) != 0 )
            throw new SugarSyncException('cUrl Error: ' . curl_error( $curl ));

        curl_close( $curl );

        if ( $curlgetinfo['http_code'] >= 200 && $curlgetinfo['http_code'] < 300 ) {
            if ( preg_match( '/Location:(.*?)\r/i', $response, $matches ) )
                $this->refresh_token = trim($matches[1]);
                return $this->refresh_token;
        } else {
            if ( $curlgetinfo['http_code'] == 401 )
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' Authorization required.');
            elseif ( $curlgetinfo['http_code'] == 403 )
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' (Forbidden)  Authentication failed.');
            elseif ( $curlgetinfo['http_code'] == 404 )
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' Not found');
            else
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code']);
        }
    }

    public function create_account($email,$password)  {
        $auth = '<?xml version="1.0" encoding="UTF-8" ?>';
        $auth .= '<user>';
        $auth .= '<email>' . mb_convert_encoding( $email,'UTF-8',$this->encoding ) . '</email>';
        $auth .= '<password>' . mb_convert_encoding( $password,'UTF-8',$this->encoding ) . '</password>';
        $auth .= '<accessKeyId>'.self::ACCESS_KEY_ID.'</accessKeyId>';
        $auth .= '<privateAccessKey>'.self::PRIVATE_ACCESS_KEY.'</privateAccessKey>';
        $auth .= '</user>';
        // init
        $curl = curl_init();
        //set options
        curl_setopt( $curl, CURLOPT_URL, 'https://provisioning-api.sugarsync.com/users' );
        curl_setopt( $curl, CURLOPT_USERAGENT, 'BackWPup');
        if ( ini_get( 'open_basedir' ) == '' && ini_get( 'safe_mode' == 'Off' ) ) curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt( $curl, CURLOPT_SSLVERSION, 3 );
        if ( is_file( dirname( __FILE__ ) . '/gd-class2-root.pem' ) )
            curl_setopt( $curl, CURLOPT_CAINFO, dirname( __FILE__ ) . '/gd-class2-root.pem' );
        curl_setopt( $curl, CURLOPT_HEADER, true );
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/xml; charset=UTF-8','Content-Length: '.strlen($auth) ) );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $auth );
        curl_setopt( $curl, CURLOPT_POST, true );
        // execute
        $response    = curl_exec( $curl );
        $curlgetinfo = curl_getinfo( $curl );
        // fetch curl errors
        if ( curl_errno( $curl ) != 0 )
            throw new SugarSyncException('cUrl Error: ' . curl_error( $curl ));

        curl_close( $curl );

        if ( $curlgetinfo['http_code'] == 201 ) {
            throw new SugarSyncException('Account created.');
        } else {
            if ( $curlgetinfo['http_code'] == 400 )
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' '.substr($response,$curlgetinfo['header_size']));
            elseif ( $curlgetinfo['http_code'] == 401 )
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' Developer credentials cannot be verified. Either a developer with the specified accessKeyId does not exist or the privateKeyID does not match an assigned accessKeyId.');
            elseif ( $curlgetinfo['http_code'] == 403 )
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' '.substr($response,$curlgetinfo['header_size']));
            elseif ( $curlgetinfo['http_code'] == 503 )
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code'] . ' '.substr($response,$curlgetinfo['header_size']));
            else
                throw new SugarSyncException('Http Error: ' . $curlgetinfo['http_code']);
        }
    }

	public function chdir( $folder, $root = '' ) {
		$folder = rtrim( $folder, '/' );
		if ( substr( $folder, 0, 1 ) == '/' || empty($this->folder) ) {
			if ( ! empty($root) )
				$this->folder = $root;
			else
				throw new SugarSyncException('chdir: root folder must set!');
		}
		$folders = explode( '/', $folder );
		foreach ( $folders as $dir ) {
			if ( $dir == '..' ) {
				$contents = $this->doCall( $this->folder );
				if ( ! empty($contents->parent) )
					$this->folder = $contents->parent;
			} elseif ( ! empty($dir) && $dir != '.' ) {
				$isdir    = false;
				$contents = $this->getcontents( 'folder' );
				foreach ( $contents->collection as $collection ) {
					if ( strtolower( $collection->displayName ) == strtolower( $dir ) ) {
						$isdir        = true;
						$this->folder = $collection->ref;
						break;
					}
				}
				if ( ! $isdir )
					throw new SugarSyncException('chdir: Folder ' . $folder . ' not exitst');
			}
		}
		return $this->folder;
	}

	public function showdir( $folderid ) {
		$showfolder = '';
		while ( $folderid ) {
			$contents   = $this->doCall( $folderid );
			$showfolder = $contents->displayName . '/' . $showfolder;
			if ( isset($contents->parent) )
				$folderid = $contents->parent;
			else
				break;
		}
		return $showfolder;
	}

	public function mkdir( $folder, $root = '' ) {
		$savefolder = $this->folder;
		$folder     = rtrim( $folder, '/' );
		if ( substr( $folder, 0, 1 ) == '/' || empty($this->folder) ) {
			if ( ! empty($root) )
				$this->folder = $root;
			else
				throw new SugarSyncException('mkdir: root folder must set!');
		}
		$folders = explode( '/', $folder );
		foreach ( $folders as $dir ) {
			if ( $dir == '..' ) {
				$contents = $this->doCall( $this->folder );
				if ( ! empty($contents->parent) )
					$this->folder = $contents->parent;
			} elseif ( ! empty($dir) && $dir != '.' ) {
				$isdir    = false;
				$contents = $this->getcontents( 'folder' );
				foreach ( $contents->collection as $collection ) {
					if ( strtolower( $collection->displayName ) == strtolower( $dir ) ) {
						$isdir        = true;
						$this->folder = $collection->ref;
						break;
					}
				}
				if ( ! $isdir ) {
					$request  = $this->doCall( $this->folder, '<?xml version="1.0" encoding="UTF-8"?><folder><displayName>' . mb_convert_encoding( $dir,'UTF-8',$this->encoding )  . '</displayName></folder>', 'POST' );
					$contents = $this->getcontents( 'folder' );
					foreach ( $contents->collection as $collection ) {
						if ( strtolower( $collection->displayName ) == strtolower( $dir ) ) {
							$isdir        = true;
							$this->folder = $collection->ref;
							break;
						}
					}
				}
			}
		}
		$this->folder = $savefolder;
		return true;
	}


	public function user() {
		return $this->doCall( self::API_URL . '/user' );
	}


	public function get( $url ) {
		return $this->doCall( $url, '', 'GET' );
	}

	public function download( $url ) {
		return $this->doCall( $url . '/data' );
	}

	public function delete( $url ) {
		return $this->doCall( $url, '', 'DELETE' );
	}


	public function getcontents( $type = '', $start = 0, $max = 500 ) {
		$parameters = '';
		if ( strtolower( $type ) == 'folder' || strtolower( $type ) == 'file' )
			$parameters .= 'type=' . strtolower( $type );
		if ( ! empty($start) && is_integer( $start ) ) {
			if ( ! empty($parameters) )
				$parameters .= '&';
			$parameters .= 'start=' . $start;

		}
		if ( ! empty($max) && is_integer( $max ) ) {
			if ( ! empty($parameters) )
				$parameters .= '&';
			$parameters .= 'max=' . $max;
		}

		$request = $this->doCall( $this->folder . '/contents?' . $parameters );
		return $request;
	}

	public function upload( $file, $name = '' ) {
		if ( empty($name) )
			$name = basename( $file );
		$xmlrequest = '<?xml version="1.0" encoding="UTF-8"?>';
		$xmlrequest .= '<file>';
		$xmlrequest .= '<displayName>' . mb_convert_encoding( $name,'UTF-8',$this->encoding ) . '</displayName>';
		if ( ! is_file( $file ) ) {
			$finfo = fopen( $file, 'r' );
			$xmlrequest .= '<mediaType>' . mime_content_type( $finfo ) . '</mediaType>';
			fclose( $finfo );
		}
		$xmlrequest .= '</file>';
		$request  = $this->doCall( $this->folder, $xmlrequest, 'POST' );
		$getfiles = $this->getcontents( 'file' );
		foreach ( $getfiles->file as $getfile ) {
			if ( $getfile->displayName == $name ) {
				$this->doCall( $getfile->ref . '/data', $file, 'PUT' );
				return $getfile->ref;
			}
		}
	}

	public function setProgressFunction($function) {
		if (function_exists($function))
			$this->ProgressFunction = $function;
		else
			$this->ProgressFunction = false;
	}
}


/**
 * SugarSync Exception class
 *
 * @author	Daniel Huesken <daniel@huersken-net.de>
 */
class SugarSyncException extends Exception {
}