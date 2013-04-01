<?php

// Class to import JW Player 5 configs and playlists

class JWP6_Legacy {
    

    static $optionmap = array(

        'controlbar' => array(
            'new' => 'controls',
            'default' => 'true',
            'options' => array(
                'none' => 'false',
            ),
        ),

        'autostart' => false,

        'height' => false,

        'width' => false,

        'playlist.position' => array(
            'new' => 'listbar__position',
            'default' => 'none',
            'options' => array(
                'bottom' => 'bottom',
                'right' => 'right',
                'over' => 'bottom',
            ),
        ),

        'playlistsize' => array(
            'new' => 'listbar__size',
            'options' => false,
        ),

        'repeat' => array(
            'new' => 'repeat',
            'default' => 'true',
            'options' => array(
                'none' => 'false',
                'list' => 'false',
            ),
        ),

        'stretching' => false,

        'mute' => false,

        'skin' => array(
            'new' => 'skin',
            'default' => 'NULL',
            'option_value' => array('JWP6_Legacy', 'skin_name_from_path'),
            'options' => array(
                'beelden' => 'beelden',
                'bekle' => 'bekle', 
                'five' => 'five', 
                'glow' => 'glow',
                'modieus' => 'modieus',
                'stormtrooper' => 'stormtrooper',
            ),
        ),

        'gapro.tracktime' => array(
            'new' => 'ga',
            'default' => 'true',
            'options' => array(),
        ),

        'gapro.trackstarts' => array(
            'new' => 'ga',
            'default' => 'true',
            'options' => array(),
        ),

        'gapro.trackpercentage' => array(
            'new' => 'ga',
            'default' => 'true',
            'options' => array(),
        ),

        'streamer' => false,

    );

    static $additional_options = array(
        'primary' => array(
            'default' => 'html5',
            'mode' => 'wp_option',
            'option_name' => 'jwplayermodule_player_mode'
        ),
    );

    static $jwp5_settings_to_import = array(
        'allow_tracking' => array('name' => 'allow_anonymous_tracking', 'autoload' => true),
        'category_mode' => array('name' => 'category_config', 'autoload' => false),
        'search_mode' => array('name' => 'search_config', 'autoload' => false),
        'tag_mode' => array('name' => 'tag_config', 'autoload' => false),
        'home_mode' => array('name' => 'home_config', 'autoload' => false)
    );

    public static function slugify($text) { 
        // slugify function as per 
        // http://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        // trim
        $text = trim($text, '-');
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }

    public static function get_jwp5_players() {
        $players = array();
        $uploads = wp_upload_dir();
        $config_dir = $uploads["basedir"] . "/" . plugin_basename(JWP6_PLUGIN_DIR_NAME) . "/configs";
        $handler = @opendir($config_dir);
        if (!$handler) return false;
        while ($file = readdir($handler)) {
            if ($file != "." && $file != ".." && strstr($file, ".xml")) {
                $name = str_replace(".xml", "", $file);
                $players[$name] = $config_dir . '/' . $file;
            }
        }
        closedir($handler);
        return $players;
    }

    static function skin_name_from_path($path) {
        $path = explode('skins/', $path);
        $path = explode('.', $path[1]);
        return $path[0];
    }

    static function map_jwp5_config($old_config) {
        $new_config = array();

        foreach ($old_config as $option => $value) {
            $option = strval($option);
            $value = strval($value);
            if ( array_key_exists($option, JWP6_Legacy::$optionmap) ) {
                $optionmap = JWP6_Legacy::$optionmap[$option];
                // Options that can be mapped one on one
                if ( $optionmap ) {
                    $option = ( array_key_exists('new', $optionmap) ) ? $optionmap['new'] : $option;
                    if ( array_key_exists('option_value', $optionmap) && is_callable($optionmap['option_value']) ) {
                        $value = call_user_func($optionmap['option_value'], $value);
                    }
                    if ( false !== $optionmap['options'] ) {
                        $value = ( array_key_exists($value, $optionmap['options']) ) ? $optionmap['options'][$value] : $optionmap['default'];
                    }
                } else {
                    if ( 'false' == strtolower($value) ) $value = false;
                    if ( 'true' == strtolower($value) ) $value = true;
                }
                $new_config[$option] = $value;
            }
        }

        foreach (JWP6_Legacy::$additional_options as $option => $info) {
            if ( "wp_option" == $info['mode'] ) {
                $value = get_option($info['option_name']);
            }
            $value = ( $value ) ? $value : $info['default'];
            $new_config[$option] = $value;
        }

        return $new_config;
    }

    static function check_shortcode($shortcode) {
        // Code to find attributes with a dot.
        foreach ($shortcode as $key => $value) {
            if ( is_int($key) ) {
                unset($shortcode[$key]);
                preg_match('/^(.+)=["\']{1}(.+)["\']{1}$/', $value, $matches);
                if ( 3 == count($matches) ) {
                    $shortcode[$matches[1]] = $matches[2];
                }
            }
        }

        if ( array_key_exists('config', $shortcode) ) {
            $imported_players = get_option(JWP6 . 'imported_jwp5_players');
            if ( isset($imported_players[$shortcode['config']]) ) {
                $shortcode['player'] = $imported_players[$shortcode['config']];
            }
            unset($shortcode['config']);
        }

        foreach ($shortcode as $option => $value) {
            if ( array_key_exists($option, JWP6_Legacy::$optionmap) ) {
                $optionmap = JWP6_Legacy::$optionmap[$option];
                // Options that can be mapped one on one
                if ( $optionmap ) {
                    unset($shortcode[$option]);
                    $option = ( array_key_exists('new', $optionmap) ) ? $optionmap['new'] : $option;
                    if ( array_key_exists('option_value', $optionmap) && is_callable($optionmap['option_value']) ) {
                        $value = call_user_func($optionmap['option_value'], $value);
                    }
                    if ( false !== $optionmap['options'] ) {
                        $value = ( array_key_exists($value, $optionmap['options']) ) ? $optionmap['options'][$value] : $optionmap['default'];
                    }
                }
                $shortcode[$option] = $value;
            }
        }

        return $shortcode;
    }

    static function import_jwp5_player_from_xml($name, $config_file) {
        $old_config = simplexml_load_file($config_file);

        $new_config = JWP6_Legacy::map_jwp5_config($old_config);
        $new_player_id = JWP6_Player::next_player_id();

        $player = new JWP6_Player($new_player_id, $new_config);
        $player->set('description', $name);
        $player->save();

        return $new_player_id;
    }

    static function import_jwp5_players() {
        $players = JWP6_Legacy::get_jwp5_players();
        foreach ($players as $name => $xml_file) {
            $new_player_id = JWP6_Legacy::import_jwp5_player_from_xml($name, $xml_file);
            $players[$name] = $new_player_id;
        }
        return $players;
    }

    static function import_jwp5_settings() {
        foreach (JWP6_Legacy::$jwp5_settings_to_import as $name => $new) {
            $value = get_option(LONGTAIL_KEY . $name);
            add_option(JWP6 . $new['name'], $value, '', $new['autoload']);
        }
    }

    static function purge_jwp5_settings() {
        global $wpdb;

        $meta_query = "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '" . LONGTAIL_KEY . "%';";
        $option_query = "DELETE FROM $wpdb->options WHERE option_name LIKE '" . LONGTAIL_KEY . "%';";
        $post_query = "DELETE FROM $wpdb->posts WHERE post_type = 'jw_playlist';";

        $wpdb->query($meta_query);
        $wpdb->query($option_query);
        $wpdb->query($post_query);

        require_once(dirname(__FILE__) . '/../framework/LongTailFramework.php');

        // echo "\nUnlinking: " . LongTailFramework::getPlayerPath();
        // echo "\nUnlinking: " . LongTailFramework::getEmbedderPath();
        @unlink(LongTailFramework::getPlayerPath());
        @unlink(LongTailFramework::getEmbedderPath());
        @rmdir(JWPLAYER_FILES_DIR . "/player/");

        $uploads = wp_upload_dir();
        $jwp5_files_dir = $uploads["basedir"] . "/" . plugin_basename(JWP6_PLUGIN_DIR_NAME);

        $handler = @opendir($jwp5_files_dir . "/configs");
        if ($handler) {
            while ($file = readdir($handler)) {
                if ($file != "." && $file != ".." && strstr($file, ".xml")) {
                    // echo "\nUnlinking: " . $jwp5_files_dir . "/configs/$file";
                    @unlink($jwp5_files_dir . "/configs/$file");
                }
            }
            closedir($handler);
        }
        // echo "Deleting: directories.";
        @rmdir($jwp5_files_dir . "/configs/");
        @rmdir($jwp5_files_dir);

        add_option(JWP6 . 'jwp5_purged', true);
        return True;
    }


}

