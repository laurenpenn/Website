<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

include_once("AdminState.php");
include_once("WizardState.php");
include_once("FlashVarState.php");
include_once("PlayerState.php");
include_once("BasicState.php");
include_once("AdvancedState.php");
include_once("LTASState.php");
include_once("PluginState.php");

/**
 * @file AdminContext class file.  Manages wizard state. 
 */
class AdminContext {

  /**
   * Constructor
   */
  public function AdminContext() {}

  /**
   * Given the current wizard state, determines the next state.
   */
  public function processState() {
    $state = isset($_POST[LONGTAIL_KEY . "state"]) ? $_POST[LONGTAIL_KEY . "state"] : null;
    if (isset($_POST["breadcrumb"]) && !empty($_POST["breadcrumb"])) {
      $state = $_POST["breadcrumb"];
    }
    switch ($state) {
      case BasicState::getID() :
        $state = new BasicState($_POST[LONGTAIL_KEY . "config"]);
        break;
      case AdvancedState::getID() :
        $state = new AdvancedState($_POST[LONGTAIL_KEY . "config"]);
        break;
      case LTASState::getID() :
        $state = new LTASState($_POST[LONGTAIL_KEY . "config"]);
        break;
      case PluginState::getID() :
        $state = new PluginState($_POST[LONGTAIL_KEY . "config"]);
        break;
      default :
        $state = new PlayerState(isset($_POST[LONGTAIL_KEY . "config"]) ? $_POST[LONGTAIL_KEY . "config"] : "");
        break;
    }
    $this->processPost($state);
  }

  /**
   * Processes the POST data from the previous state.
   * @param AdminState $st The next state to be populated with the POST data
   * from the previous state.
   */
  private function processPost($st) {
    $state = $st;
    if (isset($_POST["Next"])) {
      if ($_POST["Next"] == "Delete") {
        LongTailFramework::setConfig($_POST[LONGTAIL_KEY . "config"]);
        LongTailFramework::deleteConfig();
        $configs = LongTailFramework::getConfigs();
        if ($configs && count($configs) >= 2 && $_POST[LONGTAIL_KEY . "config"] == get_option($_POST[LONGTAIL_KEY . "default"])) {
          update_option(LONGTAIL_KEY . "default", $configs[1]);
        } else if (!$configs || count($configs) == 1) {
          update_option(LONGTAIL_KEY . "default", "Out-of-the-Box");
        }
        $state = new PlayerState($_POST[LONGTAIL_KEY . "config"]);
        $del_player = $_POST[LONGTAIL_KEY . "config"];
        $this->feedback_message(sprintf(__("The '%s' Player was successfully deleted.", 'jw-player-plugin-for-wordpress'), $del_player));
        $state->render();
      } else {
        if ($_POST["Next"] == __("Create Custom Player", 'jw-player-plugin-for-wordpress')) {
          $_POST[LONGTAIL_KEY . "new_player"] = __("Custom Player", 'jw-player-plugin-for-wordpress');
        }
        $state->getNextState()->render();
      }
    } else if (isset($_POST["Previous"])) {
      $state->getPreviousState()->render();
    } else if (isset($_POST["Cancel"])) {
      $state->getCancelState()->render();
    } else if (isset($_POST["Save"])) {
      $config = $_POST[LONGTAIL_KEY . "config"];
      LongTailFramework::setConfig($config);
      $save_values = $this->processSubmit();
      $success = LongTailFramework::saveConfig($this->convertToXML($save_values), esc_html($_POST[LONGTAIL_KEY . "new_player"]));
      $configs = LongTailFramework::getConfigs();
      if ($configs && count($configs) == 2) {
        update_option(LONGTAIL_KEY . "default", $_POST[LONGTAIL_KEY . "config"] ? $_POST[LONGTAIL_KEY . "config"] : $_POST[LONGTAIL_KEY . "new_player"]);
        update_option(LONGTAIL_KEY . "ootb", false);
      }
      $save_player = $_POST[LONGTAIL_KEY . "new_player"] ? $_POST[LONGTAIL_KEY . "new_player"] : $config;
      if ($success) {
        $this->feedback_message(sprintf(__("The '%s' Player was successfully saved.", 'jw-player-plugin-for-wordpress'), $save_player));
      } else {
        $this->error_message(sprintf(__('The \'%1$s\' failed to save.  Please make sure the %2$s exists and is writable.  ', 'jw-player-plugin-for-wordpress') . JW_FILE_PERMISSIONS, $save_player, LongTailFramework::getConfigPath()));
      }
      $state->getSaveState()->render();
    } else {
      if (isset($_POST[LONGTAIL_KEY . "default"])) {
        update_option(LONGTAIL_KEY . "default", $_POST[LONGTAIL_KEY . "default"]);
      }
      if (isset($_POST[LONGTAIL_KEY . "show_archive"])) {
        update_option(LONGTAIL_KEY . "show_archive", true);
      } else if (!empty($_POST)) {
        update_option(LONGTAIL_KEY . "show_archive", false);
      }
      LongTailFramework::setConfig(isset($_POST[LONGTAIL_KEY . "config"]) ? $_POST[LONGTAIL_KEY . "config"] : "");
      $state->render();
    }
  }

  /**
   * Processes the final submission of the wizard to be saved as a player
   * configuration.
   * @return Array The array of configuration options to be saved.
   */
  private function processSubmit() {
    $data = LongTailFramework::getConfigValues();
    $plugins = array();
    $additional_plugins = "";
    foreach ($_POST as $name => $value) {
      if (strstr($name, LONGTAIL_KEY . "player_")) {
        $val = esc_html($value);
        $new_val = $val;
        $new_name = str_replace(LONGTAIL_KEY . "player_", "", $name);
        if ($new_name == "skin") {
          if ($new_val != "[Default]") {
            $skins = LongTailFramework::getSkins();
            $skin_new = LongTailFramework::getSkinPath() . "$val/$val." . $skins[$val];
            if (file_exists($skin_new)) {
              $new_val = LongTailFramework::getSkinURL() . "$val/$val." . $skins[$val];
            } else {
              $new_val = LongTailFramework::getSkinURL() . "$val." . $skins[$val];
            }
            $data[$new_name] = $new_val;
          } else {
            unset($data[$new_name]);
          }
        } else if ($new_name == "playlist_position") {
          $data[str_replace("_", ".", $new_name)] = $new_val;
        } else if ($new_name == "flashvars") {
          $this->parseFlashvarString($new_val, $data);
        } else if ($new_name == "plugins") {
          $additional_plugins = $new_val;
        } else if (isset($new_val)) {
          $data[$new_name] = $new_val;
        } else {
          unset($data[$new_name]);
        }
      } else if(strstr($name, LONGTAIL_KEY . "plugin_") && strstr($name, "_enable")) {
        $plugins[str_replace("_enable", "", str_replace(LONGTAIL_KEY . "plugin_", "", $name))] = esc_html($value);
      //Process the plugin flashvars.
      } else if(strstr($name, LONGTAIL_KEY . "plugin_") && !empty($value)) {
        $plugin_key = preg_replace("/_/", ".", str_replace(LONGTAIL_KEY . "plugin_", "", $name), 1);
        $found = false;
        foreach(array_keys($plugins) as $repo) {
          $key = explode(".", $plugin_key);
          if (strstr($repo, $key[0]) && $plugins[$repo]) {
            $data[$plugin_key] = esc_html($value);
            $found = true;
            break;
          }
        }
        if (!$found) {
          unset($data[$plugin_key]);
        }
      }
    }
    $active_plugins = array();
    //Build final list of plugins to be used for this Player.
    foreach($plugins as $name => $enabled) {
      if ($enabled) {
        $active_plugins[] = $name;
      }
    }
    $plugin_string = implode(",", $active_plugins);
    $plugin_string = empty($plugin_string) ? $additional_plugins : $plugin_string . "," . $additional_plugins;
    if (!empty($plugins)) {
      $data["plugins"] = $plugin_string;
    }
    if (!isset($data["plugins"]) || $data["plugins"] == "" || empty($data["plugins"])) {
      unset($data["plugins"]);
    }
    return $data;
  }

  private function parseFlashvarString($fv_str, &$data) {
    $additional = array();
    $regex = "~^([a-zA-Z0-9._]+)=(.+)~m";
    preg_match_all($regex, $fv_str, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
      $additional[trim($matches[1][$i])] = trim($matches[2][$i]);
    }
    $data["Additional"] = $additional;
  }

  /**
   * Converts a one dimensional array into an XML string representation.
   * @param Array $target The one dimensional array to be converted to an XML
   * string.
   * @return An xml string representation of $target.
   */
  private function convertToXML($target) {
    $output = "";
    foreach($target as $name => $value) {
      if ($name == "Additional") {
        foreach ($value as $add_name => $add_value) {
          $output .= "<" . $add_name . " type='Additional'>" . $add_value . "</" . $add_name . ">\n";
        }
      } else {
        $output .= "<" . $name . ">" . $value . "</" . $name . ">\n";
      }
    }
    return $output;
  }

  /**
   * Displays a feedback message to the user.
   * @param String $message The message to be displayed.
   * @param int $timeout The duration the message should stay on screen.
   */
  private	function feedback_message ($message, $timeout = 0) { ?>
    <div class="fade updated" id="message" onclick="this.parentNode.removeChild (this)">
      <p><strong><?php echo $message ?></strong></p>
    </div> <?php
	}

  /**
   * Displays an error message to the user.
   * @param String $message  The message to be displayed.
   */
  private function error_message ($message) { ?>
    <div class="error fade" id="message">
      <p><strong><?php echo $message ?></strong></p>
    </div> <?php
  }

}

?>
