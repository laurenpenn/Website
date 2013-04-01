<?php

class JWP6_Player {

    /*
    Please note.

    $this->config is a nested array.
    array(
        'logo' => array(
            'hide' => true,
        ),
    );

    $this->defaults is NOT nested (Same as JWP6_Plugin::$player_options).
    array (
        'logo__hide' => true,
    );

    */

    protected $id;

    protected $config = array();

    private $defaults = array(
        'width' => array('default' => 480),
        'height' => array('default' => 270),
        'description' => array('default' => ''),
        // The rest of the values will be added from JWP6_Plugin::$player_options;
    );

    private $translate_string_values = array(
        'true' => true,
        'false' => false,
        'NULL' => null,
    );

    public function __construct($id = 0, $config = false) {
        foreach(JWP6_Plugin::$player_options as $option => $settings) {
            if ( array_key_exists('default', $settings) ) {
                $this->defaults[$option] = array('default' => $settings['default']);
            }
        }
        $this->id = $id;
        if ( ! $this->id ) { // id = 0 and therefor default
            $this->set('description', 'Default and fallback player (unremovable).');
        }
        if ( $config && is_array($config) ) {
            $this->config = $config;
        } else {
            $saved_config = get_option(JWP6 . "player_config_" . $this->id);
            if ( $saved_config ) {
                $this->config = $saved_config;
            }
        }
    }

    public static function next_player_id() {
        $last_player_id = get_option(JWP6 . 'last_player_id');
        if ( false === $last_player_id ) return 0;
        return intval($last_player_id) + 1;
    }

    private function _validate_param_value($param, $value) {
        // TODO: More elaborate validation
        if ( array_key_exists($param, $this->defaults) ) {
            return true;
        }
        return false;
    }

    public function save() {
        $players = get_option(JWP6 . 'players');
        if ( ! $players ) $players = array();
        if ( ! in_array($this->id, $players) ) {
            $this->id = $this->next_player_id();            
            $players[] = $this->id;
            update_option(JWP6 . 'last_player_id', $this->id);
            update_option(JWP6 . 'players', $players);
        }
        update_option(JWP6 . 'player_config_' . $this->id, $this->config);
    }

    // Check and see if this player has been saved to the option table or not.
    public function is_existing() {
        $player_config = get_option(JWP6 . 'player_config_' . $this->id);
        if ( $player_config ) {
            return true;
        }
        return false;
    }

    public function purge() {
        if ( $this->id ) {
            delete_option(JWP6 . 'player_config_' . $this->id);
            $players = get_option(JWP6 . 'players');
            if (($key = array_search($this->id, $players)) !== false) {
                unset($players[$key]);
            }
            update_option(JWP6 . 'players', $players);
        }
    }

    public function admin_url($page, $action = 'edit') {
        $params = array( 'player_id' => $this->id );
        if ( 'copy' == $action || 'delete' == $action ) {
            $params['action'] = $action;
        }
        return $page->page_url($params);
    }

    public function get_defaults() {
        return $this->defaults;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_config() {
        return $this->config;
    }

    public function full_description() {
        $desc = $this->get('description');
        if ( ! $desc ) $desc = 'New player';
        return "{$this->id}: {$desc}";
    }

    // Properties
    public function get($param) {
        if ( strpos($param, '__') ) {
            $parts = explode('__', $param);
            $last_part = end($parts);
            $a = $this->config;
            foreach ($parts as $part) {
                if ( array_key_exists($part, $a) ) {
                    if ( $last_part == $part ) {
                        return $a[$part];
                    } else {
                        $a = $a[$part];
                    }
                } else {
                    break;
                }
            }
        } else {
            if ( array_key_exists($param, $this->config) ) {
                return $this->config[$param];
            }
        }
        $default = $this->defaults[$param]['default'];
        $this->set($param, $default);
        return $default;
    }

    public function set($param, $value = NULL) {
        if ( ! $param ) return false;
        $value = ( array_key_exists(strval($value), $this->translate_string_values) ) ? $this->translate_string_values[$value] : $value;
        // if ( $this->_validate_param_value($param, $value) ) {
        if ( strpos($param, '__') ) {
            $parts = explode('__', $param);
            $last_part = end($parts);
            $a = &$this->config;
            foreach ($parts as $part) {
                if ( $part == $last_part ) {
                    $a[$part] = $value;
                } else {
                    if ( !array_key_exists($part, $a) ) {
                        $a[$part] = array();
                    }
                    $a = &$a[$part];
                }
            }
        } else {
            $this->config[$param] = $value;
        }
        return true;
        // }
        // return false;
    }

    private function _tracking_code($id) {
        global $wp;
        $host = "http://i.n.jwpltx.com/v1/wordpress/ping.gif";
        $tracking_url = JWP6_Plugin::$ping_image;
        $tracking_url.= "?e=features&s=" . urlencode(add_query_arg($wp->query_string, '', home_url($wp->request)));
        $tracking_url.= "&" . http_build_query($this->get_config());
        return "function ping{$id}() { var ping = new Image(); ping.src = '{$tracking_url}'; } jwp6AddLoadEvent(ping{$id});\n";
    }


    private function _embed_params($params = null, $parent = null) {
        $po = JWP6_Plugin::$player_options;
        if ( is_null($params) ) {
            $params = $this->config;
            unset($params['description']);
        }
        $new_params = array();
        foreach ($params as $param => $value) {
            if ( !$param ) continue;
            $check_param = ( is_null($parent) ) ? $param : $parent . "__" . $param;
            // If the value is an array, we recurse into a deeper level
            if ( is_array($value) ) {
                $new_value = $this->_embed_params($value, $check_param);
                if ( is_array($new_value) && count($new_value) ) {
                    $new_params[$param] = $new_value;
                }
            // Check if this param has a value that should exclude it's complete parent.
            } else if ( $parent && isset($po[$parent]['not_if'][$param]) && $value == $po[$parent]['not_if'][$param] ) {
                return null;
            // if the param exists in the player options, it's a built-in option and we can
            // perform additional checks
            } else if ( array_key_exists($check_param, JWP6_Plugin::$player_options) ) {
                // Check to see if the option is available for this license.
                if ( JWP6_Plugin::option_available($check_param) ) {
                    $check_for_default = ( isset($po[$check_param]['discard_if_default']) && $po[$check_param]['discard_if_default'] ) ? true : false;
                    if ( !$check_for_default || $value != $po[$check_param]['default'] ) {
                        if ( true === $value && isset($po[$check_param]['embedval']) ) $value = json_decode($po[$check_param]['embedval']);
                        $new_params[$param] = $value;
                    }
                }
            // no further checking the param was set by the user and we assume he/she
            // knows what he/she is doing.
            } else {
                $new_params[$param] = $value;
            }
        }
        return $new_params;
    }

    public function embedcode($id, $file = null, $playlist=null, $image = null, $config = null) {
        if ( ! is_null($config) ) {
            foreach ($config as $param => $value) {
                $this->set($param, $value);
            }
        }

        $params = $this->_embed_params();
        
        if ( $image ) {
            $params['image'] = $image;
        }
        if ( $file && ! $playlist ) {
            if ( $this->get('streamer') ) {
                $file = $this->get('streamer') . $file;
            }
            if ( "/" == substr($file, 0, 1) ) {
                $protocol = ( is_ssl() ) ? "https://" : "http://";
                $file = $protocol . $_SERVER['SERVER_NAME'] . $file;
            }
            // Comment out the line below if you are using relative urls to the page (officially not supported)
            // if ( ! strpos($file, "://") ) $file = site_url() . "/" . $file;
            $params['file'] = $file;
        } else if ( $playlist ) {
            $params['playlist'] = $playlist;
        }

        $embedcode = "<div class='jwplayer' id='jwplayer-{$id}'></div>";

        // $embedcode .= "<pre>" . json_encode($params) . "</pre>";

        $embedcode .= "<script type='text/javascript'>";
        if ( get_option(JWP6 . 'allow_anonymous_tracking') ) { 
            $embedcode .= $this->_tracking_code($id);
        }
        $embedcode .= "jwplayer('jwplayer-{$id}').setup(" . str_replace("&amp;", "&", json_encode($params)) . ");\n";
        $embedcode .= "</script>";

        return $embedcode;
    }

}

