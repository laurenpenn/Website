<?php

/**
 * Description of JWEmbedderConfig
 *
 * @author Cameron
 */
class JWEmbedderConfig implements EmbedConfigInterface {

  private static $events = array(
    "onBufferChange" => array(
       "bufferPercent" => "number"
    ),
    "onBufferFull" => array(),
    "onError" => array(
      "message" => "string"
    ),
    "onFullscreen" => array(
      "fullscreen" => "boolean"
    ),
    "onMeta" => array(
      "metadata" => "object"
    ),
    "onMute" => array(
      "mute" => "boolean"
    ),
    "onPlaylist" => array(
      "playlist" => "array"
    ),
    "onPlaylistItem" => array(
      "index" => "number"
    ),
    "onReady" => array(),
    "onResize" => array(
      "width" => "number",
      "height" => "number"
    ),
    "onPlay" => array(
      "oldstate" => "string",
      "newstate" => "string"
    ),
    "onPause" => array(
      "oldstate" => "string",
      "newstate" => "string"
    ),
    "onBuffer" => array(
      "oldstate" => "string",
      "newstate" => "string"
    ),
    "onIdle" => array(
      "oldstate" => "string",
      "newstate" => "string"
    ),
    "onComplete" => array(),
    "onTime" => array(
      "duration" => "number",
      "position" => "number"
    ),
    "onVolume" => array(
      "volume" => "number"
    )
  );
  
  private $id;
  private $divID;
  private $path;
  private $conf;
  private $fvars;
  private $dim;
  private $config;

  function  __construct($divId, $player_path, $config, $params = array(), $flash_vars = array(), $config_name = "") {
    $this->id = "jwplayer-" . $divId;
    $this->divID = $divId;
    $this->path = $player_path;
    $this->conf = $config;
    $this->config = $config_name;
    $this->dim = $params;
    $this->fvars = $flash_vars;
  }

  public function generateDiv() {
    global $wp;
    $features = $this->fvars;
    if (array_key_exists("modes", $features)) {
      $features["modes"] = "_";
    }
    if (array_key_exists("playlist", $features)) {
      $features["playlist"] = "_";
    }
    $host = "http://i.n.jwpltx.com/v1/wordpress/ping.gif";
    $url = $host . "?e=features&s=" . urlencode(add_query_arg($wp->query_string, '', home_url($wp->request))) .
      "&" . http_build_query($features);
    //The outer div is needed for LTAS support.
    $output = "<div id=\"$this->id-div\" class=\"$this->config\">\n";
    if (get_option(LONGTAIL_KEY . "allow_tracking")) {
      $output .= "<div id=\"$this->id\"></div>\n";
      $output .= "<script type='text/javascript'>
                    function addLoadEvent$this->divID(func) {
                      var oldonload = window.onload;
                      if (typeof window.onload != 'function') {
                        window.onload = func
                      } else {
                        window.onload = function() {
                          if (oldonload) {
                            oldonload()
                          }
                          func()
                        }
                      }
                    }

                    function ping$this->divID() {
                      var ping = new Image();
                      ping.src = '$url';
                    }

                    addLoadEvent$this->divID(ping$this->divID);
                  </script>";
    } else {
      $output .= "<div id=\"$this->id\"></div>\n";
    }
    $output .= "</div>\n";
    return $output;
  }

  private function generateSetup() {
    $events = array();
    $eventValues = array();
    $eventKeys = array();
    $config = array(
      "flashplayer" => $this->path,
      "width" => $this->dim["width"],
      "height" => $this->dim["height"],
      "controlbar" => $this->dim["controlbar"]
    );
    foreach ($this->fvars as $key => $value) {
      if (isset (self::$events[$key])) {
        $eventValues[] = urldecode(html_entity_decode($value));
        $events[$key] = "%function_$key%";
        $eventKeys[] = "\"%function_$key%\"";
      } else {
        $config[$key] = is_array($value) ? $value : urldecode(html_entity_decode($value));
      }
    }
    if (count($events) > 0) {
      $config["events"] = $events;
    }
    $json = str_replace("\\/", "/", json_encode($config));
    return str_replace($eventKeys, $eventValues, $json);
  }

  public function generateEmbedScript() {
    $script = $this->generateDiv();
    $script .= "<script type='text/javascript'>jwplayer('" . $this->id . "').setup(" . $this->generateSetup() . ");</script>";
    return $script;
  }

  public function getConfig() {
    return $this->config;
  }

  public function getId() {
    return $this->id;
  }
}
?>
