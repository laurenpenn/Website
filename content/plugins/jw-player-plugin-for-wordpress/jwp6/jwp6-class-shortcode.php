<?php

class JWP6_Shortcode {

    // JWP6_Player instance of the selected player
    protected $player;

    // Array that holds the actual content parameters
    protected $media_params = array(
        'mediaid'   => null,
        'file'      => null,
        'playlistid'=> null,
        'playlist'  => null,
        'image'     => null,
    );

    // Additional player config params
    protected $config_params;

    public function __construct($shortcode = null, $alternative_post_data = false) {
        if ( null === $shortcode ) {
            return $this->_init_from_post_data($alternative_post_data);
        } else {
            return $this->_init_from_shortcode($shortcode);
        }
    }

    protected function _init_from_post_data($alternative_post_data = false) {
        $post = ( $alternative_post_data && is_array($alternative_post_data) ) ? $alternative_post_data : $_POST;
        // First set the player
        $player = new JWP6_Player($post['player_name']);
        if ( $player && $player->is_existing() ) {
            $this->player = $player;
        }
        else {
            $this->player = new JWP6_Player(0);
        }

        foreach ($this->media_params as $param => $value) {
            if ( isset($post[JWP6 . $param]) && $post[JWP6 . $param] ) {
                $this->media_params[$param] = $post[JWP6 . $param];
            } else if ( isset($post[$param]) && $post[$param] ) {
                $this->media_params[$param] = $post[$param];
            }
        }

    }

    protected function _init_from_shortcode($shortcode) {
        $shortcode = JWP6_Legacy::check_shortcode($shortcode);

        // Player
        if ( isset($shortcode['player']) ) {
            $this->player = new JWP6_Player($shortcode['player']);
            unset($shortcode['player']);
        } else {
            $this->player = new JWP6_Player(0);
        }

        // Check fixed params
        foreach ($this->media_params as $param => $value) {
            if ( isset($shortcode[$param]) ) {
                $this->media_params[$param] = $shortcode[$param];
                unset($shortcode[$param]);
            }
        }

        // Check if at least on media item has been set.
        $media_is_set = false;
        foreach (array('mediaid', 'file', 'playlist', 'playlistid') as $param) {
            if ( ! is_null($this->media_params[$param]) ) $media_is_set = true;
        }
        if ( ! $media_is_set ) {
            exit('Error in shortcode. You need to specify at least mediaid, file, playlistid or playlist in the jwplayer shortcode.');
        }

        // The other items in the shortcode can be 
        $this->config_params = $shortcode;

    }

    // Adds the filters for this class
    public static function add_filters() {
        if (JWP6_USE_CUSTOM_SHORTCODE_FILTER) {
            add_filter('the_content', array('JWP6_Shortcode', 'the_content_filter'), 11);
            add_filter('the_excerpt', array('JWP6_Shortcode', 'the_excerpt_filter'), 11);
            add_filter('widget_text', array('JWP6_Shortcode', 'widget_text_filter'),  11);
        } else {
            add_shortcode('jwplayer', array('JWP6_Plugin', 'shortcode'));
        }
    }

    // outputs the short code for this object
    public function shortcode() {
        $params = array();
        // Player
        if ( $this->player->get_id() ) {
            $params['player'] = $this->player->get_id();
        }

        // Media
        foreach ($this->media_params as $param => $value) {
            if ( $value ) {
                $params[$param] = $value;
            }
        }

        $param_pairs = array();
        foreach ($params as $key => $value) {
            array_push($param_pairs , $key . '="' . $value . '"');
        }

        return '[jwplayer ' .join(" ", $param_pairs).']';
    }

    // outputs the embed code
    public function embedcode() {
        global $jwp6_global;
        $jwp6_global['player_embed_count'] = ( array_key_exists('player_embed_count', $jwp6_global) ) ?
            $jwp6_global['player_embed_count'] + 1 : 0;


        // Make the code a little easier to read
        foreach ($this->media_params as $param => $value) {
            $$param = $value;
        }

        // MAIN MEDIA
        $file_url = null;

        // mediaid
        if ( is_int($mediaid) || ctype_digit($mediaid) ) {
            $media_post = get_post($mediaid);
            $file_url = JWP6_Plugin::url_from_post($media_post);
        }
        // file parameter overrules the mediaid
        if ( $file ) $file_url = $file;

        // playlistid
        if ( ! $playlist ) {
            if ( is_int($playlistid) || ctype_digit($playlistid) ) {
                $playlist = JWP6_Plugin::playlist_object($playlistid);
            } else {
                $playlist = null;
            }
        }

        // If someone sets playlist and file, playlist has priority
        if ( $file_url && $playlist) unset($file_url); 


        // THUMBNAIL
        $image_url = null;
        // Direct image setting has priority
        if ( $image ) {
            if ( is_int($image) || ctype_digit($image) ) {
                $image_post = get_post($image);
                $image_url = $image_post->guid;
            }
            else {
                $image_url = $image;
            }
        }
        // Otherwise try to get it from the media
        else if ( $mediaid ) {
            $image_url = JWP6_Plugin::image_from_mediaid($mediaid);
        }

        return $this->player->embedcode(
            $jwp6_global['player_embed_count'],
            $file_url,
            $playlist,
            $image_url,
            $this->config_params
        );
    }


    /**
    * @file This file contains the necessary functions for parsing the jwplayer
    * shortcode.  Re-implementation of the WordPress functionality was necessary
    * as it did not support '.'
    * @param string $the_content
    * @return string
    */
    public static function the_excerpt_filter($the_content = "") {
        $execute = $disable = false;
        if (is_archive()) {
            $archive_mode = get_option(JWP6 . "category_config");
            $execute = $archive_mode == "excerpt";
            $disable = $archive_mode == "disable";
        } else if (is_search()) {
            $search_mode = get_option(JWP6 . "search_config");
            $execute = $search_mode == "excerpt";
            $disable = $search_mode == "disable";
        } else if (is_tag()) {
            $tag_mode = get_option(JWP6 . "tag_config");
            $execute = $tag_mode == "excerpt";
            $disable = $tag_mode == "disable";
        } else if (is_home()) {
            $home_mode = get_option(JWP6 . "home_config");
            $execute = $home_mode == "excerpt";
            $disable = $home_mode == "disable";
        }
        $tag_regex = '/(.?)\[(jwplayer)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s';
        if ($execute) {
            $the_content = preg_replace_callback($tag_regex, array("JWP6_Shortcode", "tag_parser"), $the_content);
        } else if ($disable) {
            $the_content = preg_replace_callback($tag_regex, array("JWP6_Shortcode", "tag_stripper"), $the_content);
        }
        return $the_content;
    }

    /**
    * @param string $the_content
    * @return mixed|string
    */
    public static function widget_text_filter($the_content = "") {
        $tag_regex = '/(.?)\[(jwplayer)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s';
        $the_content = preg_replace_callback($tag_regex,  array("JWP6_Shortcode", "tag_parser"), $the_content);
        return $the_content;
    }

    /**
    * Callback for locating [jwplayer] tag instances.
    * @param string $the_content The content to be parsed.
    * @return string The parsed and replaced [jwplayer] tag.
    */
    public static function the_content_filter($the_content = "") {
        $execute = $disable = false;
        if (is_archive()) {
            $archive_mode = get_option(JWP6 . "category_config");
            $execute = $archive_mode == "content";
            $disable = $archive_mode == "disable";
        } else if (is_search()) {
            $search_mode = get_option(JWP6 . "search_config");
            $execute = $search_mode == "content";
            $disable = $search_mode == "disable";
        } else if (is_tag()) {
            $tag_mode = get_option(JWP6 . "tag_config");
            $execute = $tag_mode == "content";
            $disable = $tag_mode == "disable";
        } else if (is_home()) {
            $home_mode = get_option(JWP6 . "home_config");
            $execute = $home_mode == "content";
            $disable = $home_mode == "disable";
        } else {
            $execute = true;
        }
        $tag_regex = '/(.?)\[(jwplayer)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s';
        if ($execute) {
            $the_content = preg_replace_callback($tag_regex,  array("JWP6_Shortcode", "tag_parser"), $the_content);
        } else if($disable) {
            $the_content = preg_replace_callback($tag_regex,  array("JWP6_Shortcode", "tag_stripper"), $the_content);
        }
        return $the_content;
    }

    /**
    * Parses the attributes of the [jwplayer] tag.
    * @param array $matches The match array
    * @return string The code that should replace the tag.
    */
    public static function tag_parser($matches) {
        if ($matches[1] == "[" && $matches[6] == "]") {
            return substr($matches[0], 1, -1);
        }
        $param_regex = '/([\w.]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w.]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w.]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $matches[3]);
        $atts = array();
        if (preg_match_all($param_regex, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $p_match) {
                if (!empty($p_match[1]))
                    $atts[$p_match[1]] = stripcslashes($p_match[2]);
                elseif (!empty($p_match[3]))
                    $atts[$p_match[3]] = stripcslashes($p_match[4]);
                elseif (!empty($p_match[5]))
                    $atts[$p_match[5]] = stripcslashes($p_match[6]);
                elseif (isset($p_match[7]) and strlen($p_match[7]))
                    $atts[] = stripcslashes($p_match[7]);
                elseif (isset($p_match[8]))
                    $atts[] = stripcslashes($p_match[8]);
            }
        } else {
            $atts = ltrim($text);
        }

        $shortcode = new JWP6_Shortcode($atts);
        return $matches[1] . $shortcode->embedcode() . $matches[6];
    }

    /**
    * @param $matches
    * @return string
    */
    public static function tag_stripper($matches) {
        if ($matches[1] == "[" && $matches[6] == "]") {
            return substr($matches[0], 1, -1);
        }
        return $matches[1] . $matches[6];
    }


}
