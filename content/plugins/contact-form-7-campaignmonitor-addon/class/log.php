<?php
define('CF7CM_CS_REST_LOG_VERBOSE', 1000);
define('CF7CM_CS_REST_LOG_WARNING', 500);
define('CF7CM_CS_REST_LOG_ERROR', 250);
define('CF7CM_CS_REST_LOG_NONE', 0);

class CF7CM_CS_REST_Log {
    var $_level;

    function CF7CM_CS_REST_Log($level) {
        $this->_level = $level;
    }

    function log_message($message, $module, $level) {
        if($this->_level >= $level) {
            error_log($module.': '.$message);
        }
    }
}