<?php

require_once W3TC_LIB_W3_DIR . '/Cache/File.php';

class W3_Cache_File_Manager {
    var $_cache_dir = '';
    var $_clean_timelimit = 0;

    function __construct($config = array()) {
        $this->_cache_dir = (isset($config['cache_dir']) ? trim($config['cache_dir']) : 'cache');
        $this->_clean_timelimit = (isset($config['clean_timelimit']) ? (int) $config['clean_timelimit'] : 180);
    }

    function W3_Cache_File_Manager($config = array()) {
        $this->__construct($config);
    }

    function clean() {
        @set_time_limit($this->_clean_timelimit);

        $this->_clean($this->_cache_dir, false);
    }

    function _clean($path, $remove = true) {
        $dir = @opendir($path);

        if ($dir) {
            while (($entry = @readdir($dir)) !== false) {
                if ($entry != '.' && $entry != '..') {
                    $full_path = $path . '/' . $entry;

                    if (@is_dir($full_path)) {
                        $this->_clean($full_path);
                    } elseif (!$this->is_valid($full_path)) {
                        @unlink($full_path);
                    }
                }
            }

            @closedir($dir);

            if ($remove) {
                @rmdir($path);
            }
        }
    }

    function is_valid($file) {
        $valid = false;

        if (file_exists($file)) {
            $ftime = @filemtime($file);

            if ($ftime) {
                $fp = @fopen($file, 'rb');

                if ($fp) {
                    $expires = @fread($fp, 4);

                    if ($expires !== false) {
                        list(, $expire) = @unpack('L', $expires);
                        $expire = ($expire && $expire <= W3TC_CACHE_FILE_EXPIRE_MAX ? $expire : W3TC_CACHE_FILE_EXPIRE_MAX);
                        if ($ftime > (time() - $expire)) {
                            $valid = true;
                        }
                    }

                    @fclose($fp);
                }
            }
        }

        return $valid;
    }
}