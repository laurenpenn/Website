<?php
/**
 * Responsible for display the Basic Player configuration options.
 * @file The class definition for BasicState
 * @author Cameron
 * @see FlashVarState
 */
class BasicState extends FlashVarState {

  /**
   * @see FlashVarState::__construct()
   */
  public function __construct($player, $post_values = "") {
    parent::__construct($player, $post_values);
  }

  /**
   * @see FlashVarState::getId()
   */
  public static function getID() {
    return "basic";
  }

  /**
   * @see FlashVarState::getNextState()
   */
  public function getNextState() {
    LongTailFramework::setConfig($this->current_player);
    return new AdvancedState($this->current_player, $this->current_post_values);
  }

  /**
   * @see FlashVarState::getPreviousState()
   */
  public function getPreviousState() {
    return new PlayerState("");
  }

  /**
   * @see FlashVarState::getCancelState()
   */
  public function getCancelState() {
    return new PlayerState("");
  }

  /**
   * @see FlashVarState::getSaveState()
   */
  public function getSaveState() {
    return new PlayerState("");
  }

  /**
   * @see FlashVarState::flashVarCat()
   */
  protected function flashVarCat() {
    return LongTailFramework::BASIC;
  }

  /**
   * @see FlashVarState::getButtonBar()
   */
  protected function getButtonBar($show_previous = true) {
    $this->buttonBar(BasicState::getID(), $show_previous);
  }

  /**
   * @see FlashVarState::getTitle()
   */
  public static function getTitle() {
    return WizardState::BASIC_STATE;
  }

}
?>
