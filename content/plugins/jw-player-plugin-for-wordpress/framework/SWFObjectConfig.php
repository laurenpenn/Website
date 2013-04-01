<?php

require_once("EmbedConfigInterface.php");

/**
 * A config object which functions as a represenation of a unique SWFObject.
 * It handles configuration and generation of the div used in embedding.
 * @file Class definition of SWFObjectConfig
 */
class SWFObjectConfig implements EmbedConfigInterface {

  private $id;
  private $path;
  private $conf;
  private $cls;
  private $no_flash;

  /**
   * Constructor.
   * @param int $divId The unique identifier for the div used in the embed process
   * @param $player_path
   * @param string $config The path to the Player configuration to be used
   * @param array $params The list of SWFObject params
   * @param array $flash_vars
   * @return \SWFObjectConfig
   *
   * @internal param string $playerPath The path to the player swf
   *
   * @internal param array $flashVars The list of additional flashvars to be used in the embed
   */
  function __construct($divId, $player_path, $config, $params = array(), $flash_vars = array()) {
    $this->id = "jwplayer-" . $divId;
    $this->path = $player_path;
    $this->conf = $config;
    $this->cls = $config;
    $this->init($params, $flash_vars);
  }

  /**
   * Perform the necessary initialization operations to prepare the SWFObject javascript object.
   * @param array $params The list of SWFObject params
   * @param $flash_vars
   *
   * @internal param array $flashVars The list of additional flashvars to be used in the embed
   */
  private function init($params, $flash_vars) {
    $wmode = isset($flash_vars["wmode"]) ? $flash_vars["wmode"] : "opaque";
    //Initialize defaults.
    $default_params = array(
      "width" => 400,
      "height" => 280,
      "allowfullscreen" => "true",
      "allowscriptaccess" => "always",
      "wmode" => $wmode,
      "version" => "9",
      "type" => "movie",
      "bgcolor" => "#FFFFFF",
      "express_redirect" => "/expressinstall.swf",
      "class" => "",
    );

    $params += $default_params;
    $width = $params["width"];
    $height = $params["height"];
    $express = $params["express_redirect"];
    $version = $params["version"];
    $bg_color = $params["bgcolor"];
    $this->cls = implode(" ", array($params["class"], "swfobject"));
    $this->no_flash = isset($params["no_flash"]) ? $params["no_flash"] : "";

    //Set the config flashvar to the Player configuration path
    if ($this->conf != "") {
      $flash_vars["config"] = $this->conf;
    }

    //Populate the values used for the embed process.
    $this->config["swfobject"]["files"][$this->id] = array(
      "url" => $this->path,
      "width" => isset($flash_vars["width"]) ? $flash_vars["width"] : $width,
      "height" => isset($flash_vars["height"]) ? $flash_vars["height"] : $height,
      "express_redirect" => $express,
      "version" => $version,
      "bgcolor" => $bg_color,
      //The id and name need to be set for LTAS support
      "attributes" => array("id" => $this->id, "name" => $this->id),
      "params" => $params,
      "flashVars" => $flash_vars,
    );
    //Clear the default array
    unset(
      $params["width"], $params["height"], $params["express_redirect"],
      $params["version"], $params["bgcolor"], $params["class"], $params["no_flash"]
    );
  }

  /**
   * Return the configuration values for generation of a SWFObject.
   * @return array The configuration values for generation of a SWFObject
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * Generate the div to be inserted into the html page.
   * @return string The div to be inserted into the html page
   */
  public function generateDiv() {
    //The outer div is needed for LTAS support.
    return  "<div id=\"$this->id-div\" name=\"$this->id-div\">\n" .
            "<div id=\"$this->id\" class=\"{$this->cls}\">{$this->no_flash}</div>\n" .
            "</div>\n";
  }

  /**
   * Get the ID of the player to be embedded.
   * @return string The id of the player to be embedded.
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Generate the embed script for the player.
   * @return string The embed script.
   */
  public function generateEmbedScript() {
    $id = $this->getId();
    $swf_config = $this->getConfig();
    $url = $swf_config["swfobject"]["files"][$id]["url"];
    $width = $swf_config["swfobject"]["files"][$id]["width"];
    $height = $swf_config["swfobject"]["files"][$id]["height"];
    $version = $swf_config["swfobject"]["files"][$id]["version"];
    $express_redirect = $swf_config["swfobject"]["files"][$id]["express_redirect"];
    $flash_vars = $this->transcribe_array($swf_config["swfobject"]["files"][$id]["flashVars"]);
    $params = $this->transcribe_array($swf_config["swfobject"]["files"][$id]["params"]);
    $attributes = $this->transcribe_array($swf_config["swfobject"]["files"][$id]["attributes"]);
    return $this->generateDiv() . "<script type=\"text/javascript\">swfobject.embedSWF(\"$url\", \"$id\", \"$width\", \"$height\", \"$version\", \"$express_redirect\", $flash_vars, $params, $attributes);</script>";
  }

  /**
   * Convert the PHP array of embed parameters to an array of JavaScript objects.
   * @param $target
   *
   * @internal param \The $array PHP array to be transcribed.
   * @return string The array of JavaScript objects.
   */
  private function transcribe_array($target) {
    $temp_array = array();
    foreach ($target as $key => $val) {
      $temp_array[] = "\"" . $key . "\":" . "\"" . $val . "\"";
    }
    return "{" . implode(", ", $temp_array) . "}";
  }

}

?>
