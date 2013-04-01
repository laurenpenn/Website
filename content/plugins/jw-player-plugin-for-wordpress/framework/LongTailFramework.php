<?php

include "SWFObjectConfig.php";
include "JWEmbedderConfig.php";
include "FlashVar.php";
include "PlayerPlugin.php";

/**
 * Foundation class for player management, embedding and state.  It is
 * responsible for saving/loading of player config XML files, loading skins,
 * and reading descriptor XML files.
 * @file Class definition for LongTailFramework
 */
class LongTailFramework
{

  const BASIC = "Basic Player Settings";
  const ADVANCED = "Advanced Player Settings";

  private static $dir = JWPLAYER_PLUGIN_DIR;
  private static $url = JWPLAYER_PLUGIN_URL;
  private static $current_config = "";
  private static $current_config_values;
  private static $div_id = 1;
  private static $loaded_flash_vars;
  private static $loaded_additional_flash_vars;

  /**
   * Sets the current config being worked with.  The passed in config is set as
   * the current config and it's configuration is loaded into memory.
   * @param string $config The player config we would like to perform operations
   * on.
   */
  public static function setConfig($config) {
    LongTailFramework::$current_config = $config;
    LongTailFramework::$current_config_values = LongTailFramework::getConfigFile();
    LongTailFramework::loadPlayerFlashVars();
  }

  /**
   * Returns an array representation of the current config's configuration
   * values.
   * @param bool $flat
   * @return array The array representation of config values.
   */
  public static function getConfigValues($flat = false) {
    $target = array();
    if (LongTailFramework::$current_config_values != null) {
      foreach(LongTailFramework::$current_config_values as $flash_var) {
        if ($flash_var["type"] == "Additional") {
          if ($flat) {
            $target[$flash_var->getName()] = (string) $flash_var;
          } else {
            if (!array_key_exists("Additional", $target)) {
              $target["Additional"] = array();
            }
            $target["Additional"][$flash_var->getName()] = (string) $flash_var;
          }
        } else {
          $target[$flash_var->getName()] = (string) $flash_var;
        }
      }
    }
    return $target;
  }

  /**
   * Returns the flashvars available to the player with the defaults set to
   * the values set to the loaded config value where applicable.
   * @param string $flash_var_cat The category of flashvars to be returned.
   * Default is null which returns all flashvars.
   * @return array Structured array containing the specified flashvars.
   */
  public static function getPlayerFlashVars($flash_var_cat = null) {
    if ($flash_var_cat == null) {
      return LongTailFramework::$loaded_flash_vars;
    }
    return LongTailFramework::$loaded_flash_vars[$flash_var_cat];
  }

  /**
   * Returns the flashvars that are set in addition to those defined in the
   * player.xml file.
   * @return array Structured array containing the additional flashvars.
   */
  public static function getPlayerAdditionalFlashVars() {
    return LongTailFramework::$loaded_additional_flash_vars;
  }

  /**
   * Save the Player configuration to an xml file.
   * @param $xml_string
   * @param string $target Specified config file to save to.  Default is null,
   * in which case the currently loaded config is used.
   * @return bool
   *
   * @internal param string $xmlString The xml formatted content to be saved.
   *
   */
  public static function saveConfig($xml_string, $target = "") {
    $xml_file = "";
    if ($target == "") {
      $xml_file = LongTailFramework::getConfigPath();
    } else {
      $xml_file = JWPLAYER_FILES_DIR . "/configs/" . $target . ".xml";
    }
    $xml_handle = @fopen($xml_file, "w");
    if (!$xml_handle) return false;
    $configStr = "<?xml version='1.0' encoding='UTF-8' ?>\n<config>\n" . $xml_string . "</config>";
    fwrite($xml_handle, $configStr);
    fclose($xml_handle);
    chmod($xml_file, 0755);
    return true;
  }

  /**
   * Delete a Player configuration.
   */
  public static function deleteConfig() {
    $xml_file = LongTailFramework::getConfigPath();
    unlink($xml_file);
  }

  /**
   * Checks if a specified config exists.
   * @param string $conf The config to check for.
   * @return boolean If the config exists or not.
   */
  public static function configExists($conf) {
    if (!isset($conf)) {return false;}
    return file_exists(LongTailFramework::getConfigPath($conf));
  }

  /**
   * Given a Player config name, return the associated xml file.
   * @param string $conf The name of the Player configuration.  Default is null,
   * in which case it uses the currently loaded config.
   * @return bool|object
   */
  public static function getConfigFile($conf = "") {
    $config = $conf != "" ? $conf : LongTailFramework::$current_config;
    if ($config == "" || !file_exists(LongTailFramework::getConfigPath($config))) {
      return false;
    }
    return simplexml_load_file(LongTailFramework::getConfigPath());
  }

  /**
   * Get the complete URL for a given Player configuration.
   * @param string $conf The name of the Player configuration.  Default is null,
   * in which case it uses the currently loaded config.
   * @return string
   */
  public static function getConfigURL($conf = "") {
    $config = $conf != "" ? $conf : LongTailFramework::$current_config;
    if ($config == "") {
      return "";
    }
    return JWPLAYER_FILES_URL . "/configs/" . $config . ".xml";
  }

  /**
   * Get the relative path for a given Player configuration.
   * @param string $conf The name of the Player configuration.  Default is null,
   * in which case it uses the currently loaded config.
   * @return string
   */
  public static function getConfigPath($conf = "") {
    $config = $conf != "" ? $conf : LongTailFramework::$current_config;
    if ($config == "") {
      return "";
    }
    return JWPLAYER_FILES_DIR . "/configs/" . $config . ".xml";
  }

  /**
   * Get the list of currently saved Player configurations.
   * @return array The list of configurations.
   */
  public static function getConfigs() {
    $results = array();
    $handler = @opendir(JWPLAYER_FILES_DIR . "/configs");
    if (!$handler) return false;
    $results[] = "New Player";
    while ($file = readdir($handler)) {
      if ($file != "." && $file != ".." && strstr($file, ".xml")) {
        $results[] = str_replace(".xml", "", $file);
      }
    }
    closedir($handler);
    return $results;
  }

  /**
   * Checks if there are any custom Player configs available.
   * @return boolean If there are any configs or not.
   */
  public static function configsAvailable() {
    $configs = LongTailFramework::getConfigs();
    if ($configs && count($configs) > 1) {
      return true;
    }
    return false;
  }

  /**
   * Returns the path to the player.swf.
   * @return string The path to the player.swf.
   */
  public static function getPlayerPath() {
    if (file_exists(LongTailFramework::getPrimaryPlayerPath())) {
      return LongTailFramework::getPrimaryPlayerPath();
    }
    return LongTailFramework::getSecondaryPlayerPath();
  }

  /**
   * Get the complete path to the JW Embedder javascript file.
   * @return string The path to the JW Embedder.
   */
  public static function getEmbedderPath() {
    return JWPLAYER_FILES_DIR . "/player/jwplayer.js";
  }

  /**
   * Get the complete path to the primary (and excepted) player location.
   * @return string The path to the player.
   */
  public static function getPrimaryPlayerPath() {
    return JWPLAYER_FILES_DIR . "/player/player.swf";
  }

  /**
   * Get the complete path to the secondary player location.  This
   * is necessary to support older versions.
   * @return string The path to the player.
   */
  public static function getSecondaryPlayerPath() {
    return JWPLAYER_PLUGIN_DIR . "/player.swf";
  }

  /**
   * Get the complete path to the temporary uploaded player.
   * @return string The path to the temporary player.
   */
  public static function getTempPlayerPath() {
    return JWPLAYER_FILES_DIR . "/player/player_tmp.swf";
  }

  /**
   * Get the complete path to the temporary uploaded player.
   * @return string The path to the temporary player.
   */
  public static function getTempPlayerJSPath() {
    return JWPLAYER_FILES_DIR . "/player/jwplayer_tmp.js";
  }

  /**
   * Get the complete URL for the Player swf.
   * @return string The complete URL.
   */
  public static function getPlayerURL() {
    if (file_exists(LongTailFramework::getPrimaryPlayerPath())) {
      return LongTailFramework::getPrimaryPlayerURL();
    }
    return LongTailFramework::getSecondaryPlayerURL();
  }

  /**
   * Get the complete URL for the JW Embedder javascript file.
   * @return string The complete URL to the JW Embedder.
   */
  public static function getEmbedderURL() {
    return JWPLAYER_FILES_URL . "/player/jwplayer.js";
  }

  /**
   * Get the complete url to the primary (and execpted) player location.
   * @return string The url to the player.
   */
  public static function getPrimaryPlayerURL() {
    return JWPLAYER_FILES_URL . "/player/player.swf";
  }

  /**
   * Get the complete url to the secondary player location.  This
   * is necessary to support older versions.
   * @return string The url to the player.
   */
  public static function getSecondaryPlayerURL() {
    return JWPLAYER_PLUGIN_URL . "/player.swf";
  }

  /**
   * Get the complete url to the temporary uploaded player.
   * @return string The url to the temporary player.
   */
  public static function getTempPlayerURL() {
    return JWPLAYER_FILES_URL . "/player/player_tmp.swf";
  }

  /**
   * For the given Player configuration, returns the LTAS details.

   *
   * @internal param string $config The name of the Player configuration
   * @return array An array containing the enabled state and channel code.
   */
  public static function getLTASConfig() {
    $ltas = array();
    if (file_exists(LongTailFramework::getConfigPath())) {
      $config_file = simplexml_load_file(LongTailFramework::getConfigPath());
      if (strstr((string) $config_file->plugins, "ltas")) {
        $ltas["enabled"] = true;
      }
      $ltas["channel_code"] = (string) $config_file->{"ltas.cc"};
    }
    return $ltas;
  }

  /**
   * Get the relative path for the plugins.
   * @return string The relative path
   */
  public static function getPluginPath() {
    return LongTailFramework::$dir . "/plugins/";
  }

  /**
   * Generates a list of the available plugins along with their flashvars and default values.
   * @param null $config_values
   *
   * @internal param string $config (optional) Pass in if you wish to load the plugin enabled state and flashvar values.
   * @return array The list of available plugins
   */
  public static function getPlugins(&$config_values = null) {
    $handler = opendir(LongTailFramework::getPluginPath());
    $plugins = array();
    while ($file = readdir($handler)) {
      if ($file != "." && $file != ".." && strstr($file, ".xml")) {
        $plugin = LongTailFramework::processPlugin($file, $config_values);
        $plugins[$plugin->getRepository()] = $plugin;
      }
    }
    return $plugins;
  }

  /**
   * Get the relative path of the Player skins.
   * @return string The relative path
   */
  public static function getSkinPath() {
    return LongTailFramework::$dir . "/skins/";
  }

  /**
   * Get the complete URL for a skin.
   * @return string The complete URL
   */
  public static function getSkinURL() {
    return LongTailFramework::$url . "/skins/";
  }

  /**
   * Get the list of available skins.
   * @return string The list of available skins
   */
  public static function getSkins() {
    $handler = opendir(LongTailFramework::getSkinPath());
    $skins = array();
    $skins["[Default]"] = "";
    while ($file = readdir($handler)) {
      if ($file != "." && $file != ".." && (strstr($file, ".zip") || strstr($file, ".swf"))) {
        $info = preg_split("/\./", $file);
        $skins[$info[0]] = $info[1];
      }
    }
    ksort($skins);
    return $skins;
  }

    /**
     * Get the necessary embed parameters for embedding a flash object.  For now we assume
     * the flash object will be as big as the dimensions of the player.

     *
     * @internal param $string @config The Player configuration that is going to be embedded
     * @return array The array with the flash object dimensions
     */
  public static function getEmbedParameters() {
    //If no config has been passed, use the player defaults.
    if (LongTailFramework::$current_config == "") {
      LongTailFramework::loadPlayerFlashVars();
    }
    $flash_vars = LongTailFramework::$loaded_flash_vars;
    $params = array(
      "width" => $flash_vars["Basic Player Settings"]["General"]["width"]->getDefaultValue(),
      "height" => $flash_vars["Basic Player Settings"]["General"]["height"]->getDefaultValue(),
      "controlbar" => $flash_vars["Basic Player Settings"]["Appearance"]["controlbar"]->getDefaultValue()
    );
    return $params;
  }

  /**
   * Generates the SWFObjectConfig object which acts as a wrapper for the SWFObject javascript library.
   * @param $flash_vars
   * @param bool $useJWEmbedder
   * @param string $customLocation
   *
   * @internal param array $flashVars The array of flashVars to be used in the embedding
   * @return SWFObjectConfig The configured SWFObjectConfig object to be used for embedding
   */
  public static function generateSWFObject($flash_vars, $useJWEmbedder = false, $customLocation = "") {
    $playerLocation = $customLocation ? $customLocation . "player.swf" : LongTailFramework::getPlayerURL();
    if ($useJWEmbedder) {
      return new JWEmbedderConfig(LongTailFramework::$div_id++, $playerLocation, LongTailFramework::getConfigURL(), LongTailFramework::getEmbedParameters(), $flash_vars, LongTailFramework::$current_config);
    }
    return new SWFObjectConfig(LongTailFramework::$div_id++, $playerLocation, LongTailFramework::getConfigURL(), LongTailFramework::getEmbedParameters(), $flash_vars, LongTailFramework::$current_config);
  }

  /**
   * Generates the SWFObjectConfig object which acts as a wrapper for the SWFObject javascript library.
   * This will embed the temporary swf file uploaded by a plugin.
   * @param array $flash_vars The array of flashvars to be used in the embedding
   * @return SWFObjectConfig The configured SWFObjectConfig object to be used for embedding
   */
  public static function generateTempSWFObject($flash_vars) {
    return new SWFObjectConfig(LongTailFramework::$div_id++, LongTailFramework::getTempPlayerURL(), LongTailFramework::getConfigURL(), LongTailFramework::getEmbedParameters(), $flash_vars);
  }

  /**
   * Helper function to flatten the additional flashvars into a string representation.
   * @param $flashvars
   *
   * @internal param \The $array array of additional flashvars
   * @return string The string representation of the additional flashvars
   */
  private static function flattenAdditionalFlashVars($flashvars) {
    $output = "";
    $output_array = array();
    foreach ($flashvars as $key => $value) {
      $output_array[] = $key . "=" . $value;
    }
    $output = implode("\n", $output_array);
    return $output;
  }

  /**
   * Generates the list of flashvars supported by this version of the player along with
   * their defaults.
   */
  private static function loadPlayerFlashVars() {
    $f_vars = array();
    //Load the player xml file.
    $xml = simplexml_load_file(LongTailFramework::$dir . "/player.xml");
    $config_file = LongTailFramework::$current_config_values;
    $config_values = LongTailFramework::getConfigValues();
    //Process the flashvars in the player xml file.
    foreach ($xml->flashvars as $flash_vars) {
      $f_var = array();
      $f_var_section = (string) $flash_vars["section"];
      $f_var_advanced = (string) $flash_vars["type"];
      //Ignore the flashvars categorized as "None."
      if ($f_var_advanced != "None") {
        foreach ($flash_vars as $flash_var) {
          $default = (string) $flash_var->{"default"};
          //If the config file was loaded and has an entry for the current flashvar
          //use the value in the config file.
          if ($config_file && $config_file->{$flash_var->name}) {
            unset($config_values[(string) $flash_var->name]);
            $default = (string) $config_file->{$flash_var->name};
            $default = str_replace(LongTailFramework::getSkinURL(), "", $default);
            $default = preg_replace("/(\.swf|\.zip)/", "", $default);
            $parts = explode("/", $default);
            $default = empty($parts) || $flash_var->name != "skin" ? $default : $parts[0];
          }
          $values = (array) $flash_var->select;
          $val = isset($values["option"]) ? $values["option"] : "";
          $type = (string) $flash_var["type"];
          //Load the possible values for the skin flashvar.
          if ($flash_var->name == "skin") {
            $type = "select";
            $val = array_keys(LongTailFramework::getSkins());
          }
          $temp_var = new FlashVar(
            (string) $flash_var->name, $default, (string) $flash_var->description, $val, $type
          );
          $f_var[(string) $flash_var->name] = $temp_var;
        }
        $f_vars[$f_var_advanced][$f_var_section] = $f_var;
      }
    }
    unset($config_values["plugins"]);
    unset($config_values["ltas.cc"]);
    LongTailFramework::getPlugins($config_values);
    if ($config_values && array_key_exists("Additional", $config_values)) {
      LongTailFramework::$loaded_additional_flash_vars = LongTailFramework::flattenAdditionalFlashVars($config_values["Additional"]);
    }
    LongTailFramework::$loaded_flash_vars = $f_vars;
  }

  /**
   * Creates a Plugin object which represents a given Player plugin.
   * @param object $file The xml file which represents the Plugin
   * @param &$config_values array currently loaded config values.  Used to
   * distinguish between standard flashvars and additional flashvars.
   * @return Plugin new Plugin object
   */
  private static function processPlugin($file, &$config_values = null) {
    $plugin_xml = simplexml_load_file(LongTailFramework::getPluginPath() . $file);
    $title = (string)$plugin_xml->{"title"};
    $version = (string) $plugin_xml->{"version"};
    $file_name = (string) $plugin_xml->{"filename"};
    $repository = (string) $plugin_xml->{"repository"};
    $description = (string) $plugin_xml->{"description"};
    $href = (string) $plugin_xml->{"page"};
    $enabled = false;
    $config_found = true;
    $plugin_name = str_replace(".swf", "", $file_name);
    //Check if the config file exists.
    if (file_exists(LongTailFramework::getConfigPath())) {
      $config_file = simplexml_load_file(LongTailFramework::getConfigPath());
    } else {
      $config_found = false;
    }
    $enabled = isset($config_file) && strstr((string) $config_file->plugins, $repository) ? true : false;
    $f_vars = array();
    //Process the flashvars in the plugin xml file.
    foreach($plugin_xml->flashvars as $flash_vars) {
      $f_var = array();
      $f_var_section = (string) $flash_vars["section"];
      $f_var_section = $f_var_section ? $f_var_section : "FlashVars";
      foreach ($flash_vars as $flash_var) {
        $default = (string) $flash_var->{"default"};
        //If the config file was loaded and has an entry for the current flashvar
        //use the value in the config file and set the plugin as enabled.
        if ($config_found && $config_file->{$plugin_name . "." . $flash_var->name}) {
          $p_key = $plugin_name . "." . $flash_var->name;
          if ($config_values != null) {
            unset($config_values[(string) $p_key]);
          }
          $default = (string) $config_file->{$plugin_name . "." . $flash_var->name};
        }
        $values = (array) $flash_var->select;
        $value_option = isset($values["option"]) ? (array) $values["option"] : array();
        $temp_var = new FlashVar(
          (string) $flash_var->name, $default, (string) $flash_var->description,
          $value_option, (string) $flash_var["type"]
        );
        $f_var[(string) $flash_var->name] = $temp_var;
      }
      $f_vars[$f_var_section] = $f_var;
    }
    $plugin = new Plugin($title, $version, $repository, $file_name, $enabled, $description, $f_vars, $href);
    return $plugin;
  }
}

?>
