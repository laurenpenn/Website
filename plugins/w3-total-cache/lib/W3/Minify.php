<?php

/**
 * W3 Minify object
 */

/**
 * Class W3_Minify
 */
class W3_Minify {
    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * PHP5 constructor
     */
    function __construct() {
        require_once W3TC_LIB_W3_DIR . '/Config.php';

        $this->_config = & W3_Config::instance();
    }

    /**
     * PHP4 constructor
     * @return W3_Minify
     */
    function W3_Minify() {
        $this->__construct();
    }

    /**
     * Runs minify
     */
    function process() {
        require_once W3TC_LIB_W3_DIR . '/Request.php';

        /**
         * Check for rewrite test request
         */
        $rewrite_test = W3_Request::get_boolean('w3tc_rewrite_test');

        if ($rewrite_test) {
            echo 'OK';
            exit();
        }

        $file = W3_Request::get_string('file');

        if (!$file) {
            $this->send_error('File param is missing.');
        }

        $hash = '';
        $matches = null;

        if (preg_match('~^([a-f0-9]+)\\.[0-9]+\\.(css|js)$~', $file, $matches)) {
            list(, $hash, $type) = $matches;
        } elseif (preg_match('~^([a-f0-9]+)\\/(.+)\\.(include(\\-(footer|body))?(-nb)?)\\.[0-9]+\\.(css|js)$~', $file, $matches)) {
            list(, $theme, $template, $location, , , , $type) = $matches;
        } else {
            $this->send_error('Bad file param format.');
        }

        require_once W3TC_LIB_MINIFY_DIR . '/Minify.php';
        require_once W3TC_LIB_MINIFY_DIR . '/HTTP/Encoder.php';

        /**
         * Fix DOCUMENT_ROOT for minify
         */
        $_SERVER['DOCUMENT_ROOT'] = w3_get_document_root();

        Minify::setCache($this->_get_cache());

        if ($this->_config->get_boolean('minify.debug')) {
            require_once W3TC_LIB_MINIFY_DIR . '/Minify/Logger.php';
            Minify_Logger::setLogger($this);
        }

        $browsercache = $this->_config->get_boolean('browsercache.enabled');

        $serve_options = array_merge($this->_config->get_array('minify.options'), array(
            'debug' => $this->_config->get_boolean('minify.debug'),
            'maxAge' => $this->_config->get_integer('browsercache.cssjs.lifetime'),
            'encodeOutput' => ($browsercache && $this->_config->get_boolean('browsercache.cssjs.compression')),
            'bubbleCssImports' => ($this->_config->get_string('minify.css.imports') == 'bubble'),
            'processCssImports' => ($this->_config->get_string('minify.css.imports') == 'process'),
            'cacheHeaders' => array(
                'use_etag' => ($browsercache && $this->_config->get_boolean('browsercache.cssjs.etag')),
                'expires_enabled' => ($browsercache && $this->_config->get_boolean('browsercache.cssjs.expires')),
                'cacheheaders_enabled' => ($browsercache && $this->_config->get_boolean('browsercache.cssjs.cache.control')),
                'cacheheaders' => $this->_config->get_string('browsercache.cssjs.cache.policy')
            )
        ));

        if ($hash) {
            $_GET['f'] = $this->get_files($hash, $type);
        } else {
            $_GET['g'] = $location;
            $serve_options['minApp']['groups'] = $this->get_groups($theme, $template, $type);
        }

        require_once W3TC_LIB_W3_DIR . '/Minifier.php';
        $w3_minifier =& W3_Minifier::instance();

        if ($type == 'js') {
            $minifier_type = 'application/x-javascript';

            switch (true) {
                case (($hash || $location == 'include' || $location == 'include-nb') && $this->_config->get_boolean('minify.js.combine.header')):
                case (($location == 'include-body' || $location == 'include-body-nb') && $this->_config->get_boolean('minify.js.combine.body')):
                case (($location == 'include-footer' || $location == 'include-footer-nb') && $this->_config->get_boolean('minify.js.combine.footer')):
                    $engine = 'combinejs';
                    break;

                default:
                    $engine = $this->_config->get_string('minify.js.engine');

                    if (!$w3_minifier->exists($engine) || !$w3_minifier->available($engine)) {
                        $engine = 'js';
                    }
                    break;
            }

        } elseif ($type == 'css') {
            $minifier_type = 'text/css';

            if (($hash || $location == 'include') && $this->_config->get_boolean('minify.css.combine')) {
                $engine = 'combinecss';
            } else {
                $engine = $this->_config->get_string('minify.css.engine');

                if (!$w3_minifier->exists($engine) || !$w3_minifier->available($engine)) {
                    $engine = 'css';
                }
            }
        }

        $w3_minifier->init($engine);

        $serve_options['minifiers'][$minifier_type] = $w3_minifier->get_minifier($engine);
        $serve_options['minifierOptions'][$minifier_type] = $w3_minifier->get_options($engine);

        if ($hash) {
            $id = $this->get_id_custom($hash, $type);
            $cache_id = $this->get_cache_id_custom($hash, $id, $type);
        } else {
            $id = $this->get_id_group($theme, $template, $location, $type);
            $cache_id = $this->get_cache_id_group($theme, $template, $location, $id, $type);
        }

        Minify::setCacheId($cache_id);

        if ($browsercache && $this->_config->get_boolean('browsercache.cssjs.w3tc')) {
            @header('X-Powered-By: ' . W3TC_POWERED_BY);
        }

        try {
            Minify::serve('MinApp', $serve_options);
        } catch (Exception $exception) {
            printf('<strong>W3 Total Cache Error:</strong> Minify error: %s', $exception->getMessage());
        }
    }

    /**
     * Flushes cache
     */
    function flush() {
        $cache = & $this->_get_cache();

        return $cache->flush();
    }

    /**
     * Returns object instance
     *
     * @return W3_Minify
     */
    function &instance() {
        static $instances = array();

        if (!isset($instances[0])) {
            $class = __CLASS__;
            $instances[0] = & new $class();
        }

        return $instances[0];
    }

    /**
     * Log
     *
     * @param mixed $object
     * @param string $label
     * @return bool
     */
    function log($object, $label = null) {
        $data = sprintf("[%s] [%s] [%s] %s\n", date('r'), $_SERVER['REQUEST_URI'], $label, $object);

        return @file_put_contents(W3TC_MINIFY_LOG_FILE, $data, FILE_APPEND);
    }

    /**
     * Returns files array
     *
     * @param string $hash
     * @param string $type
     * @return string
     */
    function get_files($hash, $type) {
        $files = $this->get_custom_files($hash, $type);
        $files = implode(',', $files);

        return $files;
    }

    /**
     * Returns minify groups
     *
     * @param string $theme
     * @param string $template
     * @param string $type
     * @return array
     */
    function get_groups($theme, $template, $type) {
        $result = array();

        switch ($type) {
            case 'css':
                $groups = $this->_config->get_array('minify.css.groups');
                break;

            case 'js':
                $groups = $this->_config->get_array('minify.js.groups');
                break;

            default:
                return $result;
        }

        if (isset($groups[$theme]['default'])) {
            $locations = (array) $groups[$theme]['default'];
        } else {
            $locations = array();
        }

        if ($template != 'default' && isset($groups[$theme][$template])) {
            $locations = array_merge_recursive($locations, (array) $groups[$theme][$template]);
        }

        foreach ($locations as $location => $config) {
            if (!empty($config['files'])) {
                foreach ((array) $config['files'] as $file) {
                    $file = w3_normalize_file_minify2($file);

                    if (w3_is_url($file)) {
                        $precached_file = $this->_precache_file($file, $type);

                        if ($precached_file) {
                            $result[$location][$file] = $precached_file;
                        } else {
                            $this->_handle_error($file);
                        }
                    } else {
                        $file = urldecode($file);
                        $path = w3_get_document_root() . '/' . $file;

                        if (file_exists($path)) {
                            $result[$location][$file] = '//' . $file;
                        } else {
                            $this->_handle_error($file);
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns cache id for group
     *
     * @param string $theme
     * @param string $template
     * @param string $location
     * @param integer $id
     * @param string $type
     * @return string
     */
    function get_cache_id_group($theme, $template, $location, $id, $type) {
        if ($this->_config->get_string('minify.engine') == 'file') {
            $cache_id = sprintf('%s/%s.%s.%d.%s', $theme, $template, $location, $id, $type);
        } else {
            $cache_id = sprintf('w3tc_%s_minify_%s', w3_get_host_id(), md5($theme . $template . $location . $id . $type));
        }

        return $cache_id;
    }

    /**
     * Returns cache id for custom files
     *
     * @param string $hash
     * @param integer $id
     * @param string $type
     * @return string
     */
    function get_cache_id_custom($hash, $id, $type) {
        if ($this->_config->get_string('minify.engine') == 'file') {
            $cache_id = sprintf('%s.%d.%s', $hash, $id, $type);
        } else {
            $cache_id = sprintf('w3tc_%s_minify_%s', w3_get_host_id(), md5($hash . $id . $type));
        }

        return $cache_id;
    }

    /**
     * Returns array of group sources
     *
     * @param string $theme
     * @param string $template
     * @param string $location
     * @param string $type
     * @return array
     */
    function get_sources_group($theme, $template, $location, $type) {
        $sources = array();
        $groups = $this->get_groups($theme, $template, $type);

        if (isset($groups[$location])) {
            $files = (array) $groups[$location];

            $document_root = w3_get_document_root();

            foreach ($files as $file) {
                if (is_a($file, 'Minify_Source')) {
                    $path = $file->filepath;
                } else {
                    $path = $document_root . '/' . $file;
                }

                $sources[] = $path;
            }
        }

        return $sources;
    }

    /**
     * Returns array of custom sources
     *
     * @param string $hash
     * @param string $type
     * @return array
     */
    function get_sources_custom($hash, $type) {
        $sources = array();
        $files = $this->get_custom_files($hash, $type);

        if (count($files)) {
            $document_root = w3_get_document_root();

            foreach ($files as $file) {
                $sources[] = $document_root . '/' . $file;
            }
        }

        return $sources;
    }

    /**
     * Returns ID key for group
     *
     * @param  $theme
     * @param  $template
     * @param  $location
     * @param  $type
     * @return string
     */
    function get_id_key_group($theme, $template, $location, $type) {
        if ($this->_config->get_string('minify.engine') == 'file') {
            $key = sprintf('%s/%s.%s.%s.id', $theme, $template, $location, $type);
        } else {
            $key = sprintf('w3tc_%s_minify_id_%s', w3_get_host_id(), md5($theme . $template . $location . $type));
        }

        return $key;
    }

    /**
     * Returns ID key for custom files
     *
     * @param string $hash
     * @param string $type
     * @return string
     */
    function get_id_key_custom($hash, $type) {
        if ($this->_config->get_string('minify.engine') == 'file') {
            $key = sprintf('%s.%s.id', $hash, $type);
        } else {
            $key = sprintf('w3tc_%s_minify_id_%s', w3_get_host_id(), md5($hash . $type));
        }

        return $key;
    }

    /**
     * Returns id for custom files
     *
     * @param string $hash
     * @param string $type
     * @return integer
     */
    function get_id_custom($hash, $type) {
        $key = $this->get_id_key_custom($hash, $type);
        $id = $this->_cache_get($key);

        if ($id === false) {
            $sources = $this->get_sources_custom($hash, $type);
            $id = $this->_generate_id($sources);
            $this->_cache_set($key, $id);
        }

        return (int) $id;
    }

    /**
     * Returns id for group
     *
     * @param string $theme
     * @param string $template
     * @param string $location
     * @param string $type
     * @return integer
     */
    function get_id_group($theme, $template, $location, $type) {
        $key = $this->get_id_key_group($theme, $template, $location, $type);
        $id = $this->_cache_get($key);

        if ($id === false) {
            $sources = $this->get_sources_group($theme, $template, $location, $type);
            $id = $this->_generate_id($sources);
            $this->_cache_set($key, $id);
        }

        return (int) $id;
    }

    /**
     * Returns custom files hash
     *
     * @param array $files
     * @return string
     */
    function get_custom_files_hash($files) {
        return md5(implode('', $files));
    }

    /**
     * Returns custom files key
     *
     * @param string $hash
     * @param string $type
     * @return string
     */
    function get_custom_files_key($hash, $type) {
        if ($this->_config->get_string('minify.engine') == 'file') {
            $key = sprintf('%s.%s.files', $hash, $type);
        } else {
            $key = sprintf('w3tc_%s_minify_files_%s', w3_get_host_id(), md5($hash . $type));
        }

        return $key;
    }

    /**
     * Sets custom files
     *
     * @param array $files
     * @param string $type
     * @return bool
     */
    function set_custom_files($files, $type) {
        $hash = $this->get_custom_files_hash($files);
        $key = $this->get_custom_files_key($hash, $type);

        return $this->_cache_set($key, $files);
    }

    /**
     * Returns custom files
     *
     * @param string $hash
     * @param string $type
     * @return array
     */
    function get_custom_files($hash, $type) {
        $key = $this->get_custom_files_key($hash, $type);
        $files = (array) $this->_cache_get($key);
        $files = array_map('w3_normalize_file_minify2', $files);

        return $files;
    }

    /**
     * Sends error response
     *
     * @param string $error
     * @return void
     */
    function send_error($error) {
        header('HTTP/1.0 400 Bad Request');
        die($error);
    }

    /**
     * Pre-caches external file
     *
     * @param string $url
     * @param string $type
     * @return string
     */
    function _precache_file($url, $type) {
        $lifetime = $this->_config->get_integer('minify.lifetime');
        $file_path = sprintf('%s/minify_%s.%s', W3TC_CACHE_FILE_MINIFY_DIR, md5($url), $type);
        $file_exists = file_exists($file_path);

        if ($file_exists && @filemtime($file_path) >= (time() - $lifetime)) {
            return $this->_get_minify_source($file_path, $url);
        }

        if (@is_dir(W3TC_CACHE_FILE_MINIFY_DIR)) {
            if (w3_download($url, $file_path) !== false) {
                return $this->_get_minify_source($file_path, $url);
            } else {
                $this->log(sprintf('Unable to download URL: %s', $url));
            }
        } else {
            $this->log(sprintf('The cache directory %s does not exist', W3TC_CACHE_FILE_MINIFY_DIR));
        }

        return ($file_exists ? $this->_get_minify_source($file_path, $url) : false);
    }

    /**
     * Returns minify source
     *
     * @param $file_path
     * @param $url
     * @return Minify_Source
     */
    function _get_minify_source($file_path, $url) {
        require_once W3TC_LIB_MINIFY_DIR . '/Minify/Source.php';

        return new Minify_Source(array(
            'filepath' => $file_path,
            'minifyOptions' => array(
                'prependRelativePath' => $url
            )
        ));
    }

    /**
     * Returns minify cache object
     *
     * @return object
     */
    function &_get_cache() {
        static $cache = array();

        if (!isset($cache[0])) {
            switch ($this->_config->get_string('minify.engine')) {
                case 'memcached':
                    require_once W3TC_LIB_W3_DIR . '/Cache/Memcached.php';
                    require_once W3TC_LIB_MINIFY_DIR . '/Minify/Cache/Memcache.php';
                    $w3_cache_memcached = & new W3_Cache_Memcached(array(
                        'servers' => $this->_config->get_array('minify.memcached.servers'),
                        'persistant' => $this->_config->get_boolean('minify.memcached.persistant')
                    ));
                    $cache[0] = & new Minify_Cache_Memcache($w3_cache_memcached);
                    break;

                case 'apc':
                    require_once W3TC_LIB_MINIFY_DIR . '/Minify/Cache/APC.php';
                    $cache[0] = & new Minify_Cache_APC();
                    break;

                case 'eaccelerator':
                    require_once W3TC_LIB_MINIFY_DIR . '/Minify/Cache/Eaccelerator.php';
                    $cache[0] = & new Minify_Cache_Eaccelerator();
                    break;

                case 'xcache':
                    require_once W3TC_LIB_MINIFY_DIR . '/Minify/Cache/XCache.php';
                    $cache[0] = & new Minify_Cache_XCache();
                    break;

                case 'wincache':
                    require_once W3TC_LIB_MINIFY_DIR . '/Minify/Cache/Wincache.php';
                    $cache[0] = & new Minify_Cache_Wincache();
                    break;

                case 'file':
                default:
                    if (!@is_dir(W3TC_CACHE_FILE_MINIFY_DIR)) {
                        $this->log(sprintf('Cache directory %s does not exist', W3TC_CACHE_FILE_MINIFY_DIR));
                    }

                    require_once W3TC_LIB_MINIFY_DIR . '/Minify/Cache/File.php';

                    $cache[0] = & new Minify_Cache_File(
                        W3TC_CACHE_FILE_MINIFY_DIR,
                        $this->_config->get_boolean('minify.file.locking'),
                        $this->_config->get_integer('timelimit.cache_flush')
                    );
                    break;
            }
        }

        return $cache[0];
    }

    /**
     * Handle minify error
     *
     * @param string file
     * @return void
     */
    function _handle_error($file) {
        $notification = $this->_config->get_string('minify.error.notification');

        if ($notification) {
            if (stristr($notification, 'admin') !== false) {
                $this->_config->set('notes.minify_error', true);
            }

            if (stristr($notification, 'email') !== false) {
                $last = $this->_config->get_integer('minify.error.notification.last');

                /**
                 * Prevent email flood: send email every 5 min
                 */
                if ((time() - $last) > 300) {
                    $this->_config->set('minify.error.notification.last', time());
                    $this->_send_notification();
                }
            }

            $this->_config->save();
        }
    }

    /**
     * Send E-mail notification when error occured
     *
     * @return boolean
     */
    function _send_notification() {
        $from_email = 'wordpress@' . w3_get_domain($_SERVER['SERVER_NAME']);
        $from_name = get_option('blogname');
        $to_name = $to_email = get_option('admin_email');
        $body = @file_get_contents(W3TC_DIR . '/inc/email/minify_error_notification.html');

        $headers = array(
            sprintf('From: "%s" <%s>', addslashes($from_name), $from_email),
            sprintf('Reply-To: "%s" <%s>', addslashes($to_name), $to_email),
            'Content-Type: text/html; charset=UTF-8'
        );

        @set_time_limit($this->_config->get_integer('timelimit.email_send'));

        $result = @wp_mail($to_email, 'W3 Total Cache Error Notification', $body, implode("\n", $headers));

        return $result;
    }

    /**
     * Generates file ID
     *
     * @param array $sources
     * @return string
     */
    function _generate_id($sources) {
        $hash = '';

        foreach ($sources as $source) {
            if (file_exists($source)) {
                $data = @file_get_contents($source);

                if ($data !== false) {
                    $hash .= $source . $data;
                }
            }
        }

        $id = abs(crc32($hash));

        return $id;
    }

    /**
     * Returns cache data
     *
     * @param string $key
     * @return string
     */
    function _cache_get($key) {
        $cache =& $this->_get_cache();

        $value = @unserialize($cache->fetch($key));

        return $value;
    }

    /**
     * Sets cache date
     *
     * @param string $key
     * @param string $value
     * @return boolean
     */
    function _cache_set($key, $value) {
        $cache =& $this->_get_cache();

        return $cache->store($key, serialize($value));
    }
}
