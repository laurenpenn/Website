<?php

/**
 * Amazon S3 CDN engine
 */
require_once W3TC_LIB_W3_DIR . '/Cdn/Base.php';

if (!class_exists('S3')) {
    require_once W3TC_LIB_DIR . '/S3.php';
}

/**
 * Class W3_Cdn_S3
 */
class W3_Cdn_S3 extends W3_Cdn_Base
{
    /**
     * S3 object
     *
     * @var S3
     */
    var $_s3 = null;
    
    /**
     * gzip extension
     * 
     * @var string 
     */
    var $_gzip_extension = '.gzip';
    
    /**
     * Last error
     * 
     * @var string
     */
    var $_last_error = '';
    
    /**
     * Inits S3 object
     *
     * @param string $error
     * @return boolean
     */
    function _init(&$error)
    {
        if (empty($this->_config['key'])) {
            $error = 'Empty access key';
            
            return false;
        }
        
        if (empty($this->_config['secret'])) {
            $error = 'Empty secret key';
            
            return false;
        }
        
        if (empty($this->_config['bucket'])) {
            $error = 'Empty bucket';
            
            return false;
        }
        
        $this->_s3 = & new S3($this->_config['key'], $this->_config['secret'], false);
        
        return true;
    }
    
    /**
     * Uploads files to S3
     *
     * @param array $files
     * @param array $results
     * @param boolean $force_rewrite
     * @return boolean
     */
    function upload($files, &$results, $force_rewrite = false)
    {
        $count = 0;
        $error = null;
        
        if (!$this->_init($error)) {
            $results = $this->get_results($files, W3TC_CDN_RESULT_HALT, $error);
            return false;
        }
        
        foreach ($files as $local_path => $remote_path) {
            $result = $this->_upload($local_path, $remote_path, $force_rewrite);
            $results[] = $result;
            
            if ($result['result'] == W3TC_CDN_RESULT_OK) {
                $count++;
            }
            
            if ($this->_config['compression'] && $this->may_gzip($remote_path)) {
                $remote_path_gzip = $remote_path . $this->_gzip_extension;
                $result = $this->_upload_gzip($local_path, $remote_path_gzip, $force_rewrite);
                $results[] = $result;
                
                if ($result['result'] == W3TC_CDN_RESULT_OK) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Uploads single file to S3
     * 
     * @param string $local_path
     * @param string $remote_path
     * @param boolean $force_rewrite
     * @return array
     */
    function _upload($local_path, $remote_path, $force_rewrite = false)
    {
        if (!file_exists($local_path)) {
            return $this->get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Source file not found');
        }
        
        if (!$force_rewrite) {
            $info = @$this->_s3->getObjectInfo($this->_config['bucket'], $remote_path);
            
            if ($info) {
                $hash = @md5_file($local_path);
                $s3_hash = (isset($info['hash']) ? $info['hash'] : '');
                
                if ($hash === $s3_hash) {
                    return $this->get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Object already exists');
                }
            }
        }
        
        $headers = $this->get_headers($local_path);
        $result = @$this->_s3->putObjectFile($local_path, $this->_config['bucket'], $remote_path, S3::ACL_PUBLIC_READ, array(), $headers);
        
        return $this->get_result($local_path, $remote_path, ($result ? W3TC_CDN_RESULT_OK : W3TC_CDN_RESULT_ERROR), ($result ? 'OK' : 'Unable to put object'));
    }
    
    /**
     * Uploads gzip version of file
     * 
     * @param string $local_path
     * @param string $remote_path
     * @param boolean $force_rewrite
     * @return array
     */
    function _upload_gzip($local_path, $remote_path, $force_rewrite = false)
    {
        if (!function_exists('gzencode')) {
            return $this->get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, "GZIP library doesn't exists");
        }
        
        if (!file_exists($local_path)) {
            return $this->get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Source file not found');
        }
        
        $contents = @file_get_contents($local_path);
        
        if ($contents === false) {
            return $this->get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Unable to read file');
        }
        
        $data = gzencode($contents);
        
        if (!$force_rewrite) {
            $info = @$this->_s3->getObjectInfo($this->_config['bucket'], $remote_path);
            
            if ($info) {
                $hash = md5($data);
                $s3_hash = (isset($info['hash']) ? $info['hash'] : '');
                
                if ($hash === $s3_hash) {
                    return $this->get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Object already exists');
                }
            }
        }
        
        $headers = $this->get_headers($local_path);
        $headers = array_merge($headers, array(
            'Vary' => 'Accept-Encoding', 
            'Content-Encoding' => 'gzip'
        ));
        
        $result = @$this->_s3->putObjectString($data, $this->_config['bucket'], $remote_path, S3::ACL_PUBLIC_READ, array(), $headers);
        
        return $this->get_result($local_path, $remote_path, ($result ? W3TC_CDN_RESULT_OK : W3TC_CDN_RESULT_ERROR), ($result ? 'OK' : 'Unable to put object'));
    }
    
    /**
     * Deletes files from FTP
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function delete($files, &$results)
    {
        $error = null;
        $count = 0;
        
        if (!$this->_init($error)) {
            $results = $this->get_results($files, W3TC_CDN_RESULT_HALT, $error);
            return false;
        }
        
        foreach ($files as $local_path => $remote_path) {
            $result = @$this->_s3->deleteObject($this->_config['bucket'], $remote_path);
            $results[] = $this->get_result($local_path, $remote_path, ($result ? W3TC_CDN_RESULT_OK : W3TC_CDN_RESULT_ERROR), ($result ? 'OK' : 'Unable to delete object'));
            
            if ($result) {
                $count++;
            }
            
            if ($this->_config['compression']) {
                $remote_path_gzip = $remote_path . $this->_gzip_extension;
                $result = @$this->_s3->deleteObject($this->_config['bucket'], $remote_path_gzip);
                $results[] = $this->get_result($local_path, $remote_path_gzip, ($result ? W3TC_CDN_RESULT_OK : W3TC_CDN_RESULT_ERROR), ($result ? 'OK' : 'Unable to delete object'));
                
                if ($result) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Tests S3
     *
     * @param string $error
     * @return boolean
     */
    function test(&$error)
    {
        if (!parent::test($error)) {
            return false;
        }
        
        $string = 'test_s3_' . md5(time());
        
        if (!$this->_init($error)) {
            return false;
        }
        
        $this->set_error_handler();
        
        $buckets = @$this->_s3->listBuckets();
        
        if (!$buckets) {
            $error = sprintf('Unable to list buckets (%s).', $this->get_last_error());
            
            $this->restore_error_handler();
            
            return false;
        }
        
        if (!in_array($this->_config['bucket'], (array) $buckets)) {
            $error = sprintf('Bucket doesn\'t exist: %s', $this->_config['bucket']);
            
            $this->restore_error_handler();
            
            return false;
        }
        
        if (!@$this->_s3->putObjectString($string, $this->_config['bucket'], $string, S3::ACL_PUBLIC_READ)) {
            $error = sprintf('Unable to put object (%s).', $this->get_last_error());
            
            $this->restore_error_handler();
            
            return false;
        }
        
        if (!($object = @$this->_s3->getObject($this->_config['bucket'], $string))) {
            $error = sprintf('Unable to get object (%s).', $this->get_last_error());
            
            $this->restore_error_handler();
            
            return false;
        }
        
        if ($object->body != $string) {
            @$this->_s3->deleteObject($this->_config['bucket'], $string);
            
            $error = 'Objects are not equal.';
            
            $this->restore_error_handler();
            
            return false;
        }
        
        if (!@$this->_s3->deleteObject($this->_config['bucket'], $string)) {
            $error = sprintf('Unable to delete object (%s).', $this->get_last_error());
            
            $this->restore_error_handler();
            
            return false;
        }
        
        $this->restore_error_handler();
        
        return true;
    }
    
    /**
     * Returns CDN domain
     *
     * @return string
     */
    function get_domains()
    {
        if (!empty($this->_config['cname'])) {
            return (array) $this->_config['cname'];
        } elseif (!empty($this->_config['bucket'])) {
            $domain = sprintf('%s.s3.amazonaws.com', $this->_config['bucket']);
            
            return array(
                $domain
            );
        }
        
        return array();
    }
    
    /**
     * Returns via string
     *
     * @return string
     */
    function get_via()
    {
        return sprintf('Amazon Web Services: S3: %s', parent::get_via());
    }
    
    /**
     * Creates bucket
     *
     * @param string $container_id
     * @param string $error
     * @return boolean
     */
    function create_container(&$container_id, &$error)
    {
        if (!$this->_init($error)) {
            return false;
        }
        
        $this->set_error_handler();
        
        $buckets = @$this->_s3->listBuckets();
        
        if (!$buckets) {
            $error = sprintf('Unable to list buckets (%s).', $this->get_last_error());
            
            $this->restore_error_handler();
            
            return false;
        }
        
        if (in_array($this->_config['bucket'], (array) $buckets)) {
            $error = sprintf('Bucket already exists: %s.', $this->_config['bucket']);
            
            $this->restore_error_handler();
            
            return false;
        }
        
        if (!@$this->_s3->putBucket($this->_config['bucket'], S3::ACL_PUBLIC_READ)) {
            $error = sprintf('Unable to create bucket: %s (%s).', $this->_config['bucket'], $this->get_last_error());

            $this->restore_error_handler();
            
            return false;
        }
        
            $this->restore_error_handler();
        
        return true;
    }
    
    /**
     * Formats URL for object
     * @param string $path
     * @return string
     */
    function format_url($path)
    {
        $url = parent::format_url($path);
        
        if ($this->_config['compression'] && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && stristr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && $this->may_gzip($path)) {
            if (($qpos = strpos($url, '?')) !== false) {
                $url = substr_replace($url, $this->_gzip_extension, $qpos, 0);
            } else {
                $url .= $this->_gzip_extension;
            }
            
            return $url;
        }
        
        return $url;
    }
    
    /**
     * Our error handler 
     * 
     * @param integer $errno
     * @param string $errstr
     * @return boolean
     */
    function error_handler($errno, $errstr)
    {
        $this->_last_error = $errstr;
        
        return false;
    }
    
    /**
     * Returns last error
     * 
     * @return string
     */
    function get_last_error()
    {
        return $this->_last_error;
    }
    
    /**
     * Set our error handler
     * 
     * @return void
     */
    function set_error_handler()
    {
        set_error_handler(array(
            &$this, 
            'error_handler'
        ));
    }
    
    /**
     * Restore prev error handler
     * 
     * @return void
     */
    function restore_error_handler()
    {
        restore_error_handler();
    }
}
