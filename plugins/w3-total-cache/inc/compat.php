<?php

if (!function_exists('json_encode')) {
    function json_encode($string) {
        global $json;

        if (!is_a($json, 'Services_JSON')) {
            require_once W3TC_LIB_DIR . '/JSON.php';
            $json = new Services_JSON();
        }

        return $json->encodeUnsafe($string);
    }
}

if (!function_exists('json_decode')) {
    function json_decode($string, $assoc_array = false) {
        global $json;

        if (!is_a($json, 'Services_JSON')) {
            require_once W3TC_LIB_DIR . '/JSON.php';
            $json = new Services_JSON();
        }

        $res = $json->decode($string);

        if ($assoc_array) {
            $res = _json_decode_object_helper($res);
        }

        return $res;
    }

    function _json_decode_object_helper($data) {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        return (is_array($data) ? array_map(__FUNCTION__, $data) : $data);
    }
}
