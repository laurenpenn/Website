<?php
/**
 * Responsible for displaying the Advanced Player configuration options.
 * @file The class definition for AdvancedState
 * @see FlashVarState
 */
class AdvancedState extends FlashVarState {

  /**
   * @see FlashVarState::__construct()
   * @param $player
   * @param string $post_values
   * @return \AdvancedState
   */
  public function __construct($player, $post_values = "") {
    parent::__construct($player, $post_values);
  }

  /**
   * @see FlashVarState::getID()
   * @return string
   */
  public static function getID() {
    return "advanced";
  }

  /**
   * @see FlashVarState::getNextState()
   * @return \LTASState
   */
  public function getNextState() {
    LongTailFramework::setConfig($this->current_player);
    return new LTASState($this->current_player, $this->current_post_values);
  }

  /**
   * @see FlashVarState::getPreviousState()
   * @return \BasicState
   */
  public function getPreviousState() {
    LongTailFramework::setConfig($this->current_player);
    return new BasicState($this->current_player);
  }

  /**
   * @see FlashVarState::getCancelState()
   * @return \PlayerState
   */
  public function getCancelState() {
    return new PlayerState("");
  }

  /**
   * @see FlashVarState::getSaveState()
   * @return \PlayerState
   */
  public function getSaveState() {
    return new PlayerState("");
  }

  /**
   * @see FlashVarState::flashVarCat()
   * @return \LongTailFramework.ADVANCED
   */
  protected function flashVarCat() {
    return LongTailFramework::ADVANCED;
  }

  /**
   * @see FlashVarState::getButtonBar()
   * @param bool $show_previous
   */
  protected function getButtonBar($show_previous = true) {
    $this->buttonBar(AdvancedState::getID());
  }

  /**
   * @see FlashVarState::getTitle()
   * @return \WizardState.ADVANCED_STATE
   */
  public static function getTitle() {
    return WizardState::ADVANCED_STATE;
  }

  /**
   * @see FlashVarState::getFooter()
   */
  protected function getFooter() { ?>
    <div class="stuffbox">
      <h3 class="hndle"><span><?php _e("Additional Flashvars", 'jw-player-plugin-for-wordpress'); ?></span></h3>
      <div class="inside" style="margin: 15px;">
        <table class="form-table">
          <tr valign="top">
            <th><?php echo "flashvars:"; ?></th>
            <td>
              <?php $name = LONGTAIL_KEY . "player_flashvars"; ?>
              <?php $value = isset($_POST[$name]) ? $_POST[$name] : LongTailFramework::getPlayerAdditionalFlashVars(); ?>
              <?php unset($_POST[$name]); ?>
              <textarea name="<?php echo $name; ?>" cols="80" rows="2"><?php echo $value; ?></textarea>
              <br/>
              <span class="description"><?php _e("Enter one additional flashvar (name=value) per line that you would like to be used by this player.  <strong>Note:</strong> Overriding the standard flashvars could lead to unexpected behavior. ", 'jw-player-plugin-for-wordpress'); ?></span>
            </td>
          </tr>
        </table>
      </div>
    </div> <?php
  }

}
?>
