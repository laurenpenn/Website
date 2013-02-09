<?php
// +----------------------------------------------------------------------+
// | Decode and Encode data in Bittorrent format                          |
// +----------------------------------------------------------------------+
// | Copyright (C) 2004-2005                                              |
// |   Justin Jones <j.nagash@gmail.com>                                  |
// |   Markus Tacker <m@tacker.org>                                       |
// | Copyright (C) 2007-2008                                              |
// |   Dan Kuykendall <dan@kuykendall.org>                                |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// |                                                                      |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// |                                                                      |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the                |
// | Free Software Foundation, Inc.                                       |
// | 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA               |
// +----------------------------------------------------------------------+
/* This code was pulled and adapted from the torrent code PEAR*/
class podPressTorrentEncode_class
{
	function encode($mixed)
	{
		switch (gettype($mixed)) {
			case is_null($mixed):
				return $this->encode_string('');
			case 'string':
				return $this->encode_string($mixed);
			case 'integer':
			case 'double':
				return  $this->encode_int(round($mixed));
			case 'array':
				return $this->encode_array($mixed);
			default:
				die( 'podPressTorrentEncode_class::encode() - Unsupported type.'."Variable must be one of 'string', 'integer', 'double' or 'array'");
		}
	}

  function encode_string($str)
  {
    return strlen($str) . ':' . $str;
  }
  function encode_int($int)
  {
    return 'i' . $int . 'e';
  }

	function encode_array($array)
  {
		// Check for strings in the keys
		$isList = true;
		foreach (array_keys($array) as $key) {
			if (!is_int($key)) {
				$isList = false;
				break;
			}
		}
		if ($isList) {
			// Wie build a list
			ksort($array, SORT_NUMERIC);
			$return = 'l';
			foreach ($array as $val) {
				$return .= $this->encode($val);
			}
			$return .= 'e';
		} else {
			// We build a Dictionary
			ksort($array, SORT_STRING);
			$return = 'd';
			foreach ($array as $key => $val) {
				$return .= $this->encode(strval($key));
				$return .= $this->encode($val);
			}
			$return .= 'e';
		}
		return $return;
  }
}

class podPressTorrentMake_class
{
	var $_path = '';
	var $_is_file = false;
	var $_is_dir = false;
	var $_announce = '';
	var $_announce_list = array();
	var $_comment = '';
	var $_created_by = 'podPress Torrent Maker';
	var $_name = '';
	var $_pieces = '';
	var $_piece_length = 524288;
	var $_files = array();
	var $_data_gap = false;
	var $_fp;
	var $last_error;

	function podPressTorrentMake_class($path)
	{
		$this->setPath($path);
	}

	function setAnnounce($announce)
	{
		$this->_announce = strval($announce);
		return true;
	}

	function setAnnounceList($announce_list)
	{
		if (!is_array($announce_list)) {
			$this->last_error = __CLASS__ . '::'. __FUNCTION__ . '() - No array given.';
			return false;
		}
		$this->_announce_list = $announce_list;
		return true;
	}

	function setComment($comment)
	{
		$this->_comment = strval($comment);
		return true;
	}

	function setPath($path)
	{
		$this->_path = $path;
		if (is_dir($path)) {
			$this->_is_dir = true;
			$this->_name = basename($path);
		} else if (is_file($path)) {
			$this->_is_file = true;
			$this->_name = basename($path);
		} else {
			$this->_path = '';
		}
		return true;
	}

	function setPieceLength($piece_length)
	{
		if ($piece_length < 32 or $piece_length > 4096) {
			$this->last_error = __CLASS__ . '::'. __FUNCTION__ . '() - Invalid piece lenth: "' . $piece_length . '"';
			return false;
		}
		$this->_piece_length = $piece_length * 1024;
		return true;
	}

	function buildTorrent()
	{
		if ($this->_is_file) {
			if (!$info = $this->_addFile($this->_path)) {
				return false;
			}
			if (!$metainfo = $this->_encodeTorrent($info)) {
				return false;
			}
		} else if ($this->_is_dir) {
			if (!$diradd_ok = $this->_addDir($this->_path)) {
				return false;
			}
			$metainfo = $this->_encodeTorrent();
		} else {
			$this->last_error = __CLASS__ . '::'. __FUNCTION__ . '() - You must provide a file or directory.';
			return false;
		}
		return $metainfo;
	}

	function _encodeTorrent($info = array())
	{
		$bencdata = array();
		$bencdata['info'] = array();
		if ($this->_is_file) {
			$bencdata['info']['length'] = $info['length'];
			$bencdata['info']['md5sum'] = $info['md5sum'];
		} else if ($this->_is_dir) {
			if ($this->_data_gap !== false) {
				$this->_pieces .= pack('H*', sha1($this->_data_gap));
				$this->_data_gap = false;
			}
			$bencdata['info']['files'] = $this->_files;
		} else {
			$this->last_error = __CLASS__ . '::'. __FUNCTION__ . '() - Use ' .  __CLASS__ . '::setPath() to define a file or directory.';
			return false;
		}
		$bencdata['info']['name']         = $this->_name;
		$bencdata['info']['piece length'] = $this->_piece_length;
		$bencdata['info']['pieces']       = $this->_pieces;
		$bencdata['announce']             = $this->_announce;
		$bencdata['creation date']        = time();
		$bencdata['comment']              = $this->_comment;
		$bencdata['created by']           = $this->_created_by;
		// $bencdata['announce-list'] = array($this->_announce)
		// Encode it
		$Encoder = new podPressTorrentEncode_class;
		return $Encoder->encode_array($bencdata);
	}

	function _addFile($file)
	{
		if (!$this->_openFile($file)) {
			$this->last_error = __CLASS__ . '::'. __FUNCTION__ . "() - Failed to open file '$file'.";
			return false;
		}

		$filelength = 0;
		$md5sum = md5_file($file);

		while (!feof($this->_fp)) {
			$data = '';
			$datalength = 0;

			if ($this->_is_dir && $this->_data_gap !== false) {
				$data = $this->_data_gap;
				$datalength = strlen($data);
				$this->_data_gap = false;
			}

			while (!feof($this->_fp) && ($datalength < $this->_piece_length)) {
				$readlength = 8192;
				if (($datalength + 8192) > $this->_piece_length) {
					$readlength = $this->_piece_length - $datalength;
				}

				$tmpdata = fread($this->_fp, $readlength);
				$actual_readlength = strlen($tmpdata);
				$datalength += $actual_readlength;
				$filelength += $actual_readlength;

				$data .= $tmpdata;
			}

			// We've either reached the end of the file, or
			// we have a whole piece, or both.
			if ($datalength == $this->_piece_length) {
				// We have a piece.
				$this->_pieces .= pack('H*', sha1($data));
			}
			if (($datalength != $this->_piece_length) && feof($this->_fp)) {
				// We've reached the end of the file, and
				// we dont have a whole piece.
				if ($this->_is_dir) {
					$this->_data_gap = $data;
				} else {
					$this->_pieces .= pack('H*', sha1($data));
				}
			}
		}

		// Close the file pointer.
		$this->_closeFile();
		$info = array('length' => $filelength, 'md5sum' => $md5sum);
		return $info;
	}

	function _addDir($path)
	{
		$filelist = $this->_dirList($path);
		sort($filelist);

		foreach ($filelist as $file) {
			$filedata = $this->_addFile($file);
			if ($filedata !== false) {
				$filedata['path'] = array();
				$filedata['path'][] = basename($file);
				$dirname = dirname($file);
				while (basename($dirname) != $this->_name) {
					$filedata['path'][] = basename($dirname);
					$dirname = dirname($dirname);
				}
				$filedata['path'] = array_reverse($filedata['path'], false);
				$this->_files[] = $filedata;
			}
		}
		return true;
	}

	function _dirList($dir)
	{
		$dir = realpath($dir);
		$file_list = '';
		$stack[] = $dir;

		while ($stack) {
			$current_dir = array_pop($stack);
			if ($dh = opendir($current_dir)) {
				while ( ($file = readdir($dh)) !== false ) {
					if ($file{0} =='.') continue;
					$current_file = $current_dir . '/' . $file;
					if (is_file($current_file)) {
						$file_list[] = $current_dir . '/' . $file;
					} else if (is_dir($current_file)) {
						$stack[] = $current_file;
					}
				}
			}
		}
		return $file_list;
	}

	function _filesize($file)
	{
		$size = @filesize($file);
		if ($size == 0) {
			if (PHP_OS != 'Linux') return false;
			$size = exec('du -b ' . escapeshellarg($file));
		}
		return $size;
	}

	function _openFile($file)
	{
		$fsize = $this->_filesize($file);
		if ($fsize <= 2*1024*1024*1024) {
			if (!$this->_fp = fopen($file, 'r')) {
				$this->last_error = __CLASS__ . '::'. __FUNCTION__ . '() - Failed to open "' . $file . '"';
				return false;
			}
			$this->_fopen = true;
		} else {
			if (PHP_OS != 'Linux') {
				$this->last_error = __CLASS__ . '::'. __FUNCTION__ . '() - File size is greater than 2GB. This is only supported under Linux.';
				return false;
			}
			$this->_fp = popen('cat ' . escapeshellarg($file), 'r');
			$this->_fopen = false;
		}
		return true;
	}

	function _closeFile()
	{
		if ($this->_fopen) {
			fclose($this->_fp);
		} else {
			pclose($this->_fp);
		}
	}
}
