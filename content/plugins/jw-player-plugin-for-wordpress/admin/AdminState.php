<?php
/**
 * This class is a base class for Wizard states.
 * @file The class definition for AdminState.
 */
abstract class AdminState {
  
  protected $current_player;
  protected $current_post_values;

  /**
   * Constructor.
   * @param String $player The currently selected player.
   * @param String $post_values String representation of current POST values.
   */
  public function __construct($player, $post_values = "") {
    $this->current_player = $player;
    $this->current_post_values = $post_values;
  }

  /**
   * Display an error message at the top of the current page.
   * @param String $message The message to be displayed.
   */
  protected function errorMessage($message) { ?>
    <div class="error fade" id="message">
      <p><strong><?php echo $message ?></strong></p>
    </div> <?php
  }

  /**
   * Display an info message at the top of the current page.
   * @param String $message The message to be displayed.
   */
  protected function infoMessage($message) { ?>
    <div class="fade updated" id="message" onclick="this.parentNode.removeChild (this)">
      <p><strong><?php echo $message ?></strong></p>
    </div> <?php
  }

  /**
   * Renders the currently selected player name.  Displays a text field when
   * creating or copying a player.
   */
  protected function selectedPlayer() { ?>
    <table class="form-table">
      <tr>
        <th>Selected Player:</th>
        <td>
          <?php $value = $this->current_player; ?>
          <?php $new_player = LONGTAIL_KEY . "new_player"; ?>
          <?php if (isset($_POST[$new_player]) && $_POST[$new_player] != "") { ?>
            <?php $value = $_POST[$new_player]; ?>
          <?php } ?>
          <input type="text" value="<?php echo $value; ?>" id="<?php echo $new_player; ?>" name="<?php echo $new_player; ?>" />
        </td>
      </tr>
    </table> <?php
  }

  /**
   * Displays the buttons at the bottom of a Wizard page.  Available buttons
   * can be controlled through parameter options.
   * @param string $id The id of the current state.
   * @param boolean $show_previous Show the previous button.
   * @param boolean $show_next Show the next button.
   * @param boolean $show_save Show the save button.
   * @param boolean $show_cancel Show the cancel button.
   */
  protected function buttonBar($id, $show_previous = true, $show_next = true, $show_save = true, $show_cancel = true) { ?>
    <p align="right" class="submit">
      <?php foreach ($_POST as $key => $val) { ?>
        <?php if (strstr($key, LONGTAIL_KEY) && !strstr($key, "state") && !strstr($key, "config") && !strstr($key, "new_player")) {?>
          <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>" />
        <?php } ?>
      <?php } ?>
      <input type="hidden" name="<?php echo LONGTAIL_KEY . "state" ?>" value="<?php echo $id; ?>" />
      <input type="hidden" id="<?php echo LONGTAIL_KEY . "config" ?>" name="<?php echo LONGTAIL_KEY . "config" ?>" value="<?php echo $this->current_player ?>" />
      <?php if ($show_save) { ?>
        <script type="text/javascript">
          function saveHandler(button) {
            var configs = eval('(' + '<?php echo json_encode(LongTailFramework::getConfigs()); ?>' + ')');
            var newVal = document.getElementById("<?php echo LONGTAIL_KEY . "new_player"; ?>");
            var configVal = document.getElementById("<?php echo LONGTAIL_KEY . "config"; ?>");
            if (configVal != null && newVal != null && configVal.value == newVal.value) {
              return true;
            }
            for (var config in configs) {
              if (newVal.value == configs[config]) {
                return confirm("<?php _e("A player with this name already exists and will be overwritten.  Would you like to continue?", 'jw-player-plugin-for-wordpress'); ?>");
              }
            }
            return true;
          }
        </script>
        <input align="left" class="button-primary" type="submit" name="Save" value="<?php _e("Save", 'jw-player-plugin-for-wordpress'); ?>" onclick="return saveHandler(this);"/>
      <?php } ?>
      <?php if ($show_previous) { ?><input type="submit" name="Previous" value="<?php _e("Previous", 'jw-player-plugin-for-wordpress'); ?>" /><?php } ?>
      <?php if ($show_cancel) { ?><input align="right" type="submit" name="Cancel" value="<?php _e("Cancel", 'jw-player-plugin-for-wordpress'); ?>" /><?php } ?>
      <?php if ($show_next) { ?><input align="right" type="submit" name="Next" value="<?php _e("Next", 'jw-player-plugin-for-wordpress'); ?>" /><?php } ?>
    </p><?php
  }

  /**
   * Get the id of the current state.
   * @return string The id of the current state.
   */
  abstract public static function getID();

  /**
   * This function gets the state to render itself.
   */
  abstract public function render();

  /**
   * Returns a reference to the state that should be rendered when the next
   * button is clicked.
   * @return AdminState A reference to the next AdminState.
   */
  abstract public function getNextState();

  /**
   * Returns a reference to the state that should be rendered when the previous
   * button is clicked.
   * @return AdminState A reference to the previous AdminState.
   */
  abstract public function getPreviousState();

  /**
   * Returns a reference to the state that should be rendered when the cancel
   * button is clicked.
   * @return AdminState A reference to the cancel AdminState.
   */
  abstract public function getCancelState();

  /**
   * Returns a reference to the state that should be rendered when the save
   * button is clicked.
   * @return AdminState A reference to the save AdminState.
   */
  abstract public function getSaveState();

}

?>
