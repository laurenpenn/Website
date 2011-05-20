<?php

/**
 * Varnish purge object
 */

/**
 * Class W3_Varnish
 */
class W3_Varnish {
    /**
     * Debug flag
     *
     * @var bool
     */
    var $_debug = false;

    /**
     * Varnish servers
     *
     * @var array
     */
    var $_servers = array();

    /**
     * Operation timeout
     *
     * @var int
     */
    var $_timeout = 30;

    /**
     * PHP5-style constructor
     */
    function __construct() {
        require_once W3TC_LIB_W3_DIR . '/Config.php';
        $config = & W3_Config::instance();

        $this->_debug = $config->get_boolean('varnish.debug');
        $this->_servers = $config->get_array('varnish.servers');
        $this->_timeout = $config->get_integer('timelimit.varnish_purge');
    }

    /**
     * PHP4-style constructor
     */
    function W3_Varnish() {
        $this->__construct();
    }

    /**
     * Returns object instance
     *
     * @return W3_Varnish
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
     * Purge URI
     *
     * @param string $uri
     * @return bool
     */
    function purge($uri) {
        @set_time_limit($this->_timeout);

        if (strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
        }

        foreach ((array) $this->_servers as $server) {
            $url = sprintf('http://%s%s', $server, $uri);

            $response = w3_http_purge($url, '', true);

            if ($this->_debug) {
                $this->_log($url, ($response !== false ? 'OK' : 'Bad response code.'));
            }

            if ($response === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Write log entry
     *
     * @param string $url
     * @param string $error
     * @return bool|int
     */
    function _log($url, $error) {
        $data = sprintf("[%s] [%s] %s\n", date('r'), $url, $error);

        return @file_put_contents(W3TC_VARNISH_LOG_FILE, $data, FILE_APPEND);
    }
}
