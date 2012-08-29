<?php
/**
 * Description of WizardState
 *
 * @author Cameron
 */
abstract class WizardState extends AdminState {

  const BASIC_STATE = "Basic Settings";
  const ADVANCED_STATE = "Advanced Settings";
  const LTAS_STATE = "Ad Serving";
  const PLUGIN_STATE = "Plugins";

  /**
   * @see AdminState::__construct()
   * @param $player
   * @param string $post_values
   * @return \WizardState
   */
  public function __construct($player, $post_values = "") {
    parent::__construct($player, $post_values);
  }

  protected function getBreadcrumbBar() { ?>
    <script type="text/javascript">
      jQuery(function() {
        jQuery("#breadcrumbs").buttonset();
      });
    </script>
    <div id="breadcrumbs" style="padding: 14px 15px 3px 0;">
      <input type="radio" id="<?php echo LONGTAIL_KEY . BasicState::getID(); ?>" name="breadcrumb" <?php checked(BasicState::getID(), $this->getID()); ?> value="<?php echo BasicState::getID(); ?>" onchange="form.submit();"/>
      <label for="<?php echo LONGTAIL_KEY . BasicState::getID(); ?>">
        <?php echo BasicState::getTitle(); ?>
      </label>
      <input type="radio" id="<?php echo LONGTAIL_KEY . AdvancedState::getID(); ?>" name="breadcrumb" <?php checked(AdvancedState::getID(), $this->getID()); ?> value="<?php echo AdvancedState::getID(); ?>" onchange="form.submit();" />
      <label for="<?php echo LONGTAIL_KEY . AdvancedState::getID(); ?>">
        <?php echo AdvancedState::getTitle(); ?>
      </label>
      <input type="radio" id="<?php echo LONGTAIL_KEY . LTASState::getID(); ?>" name="breadcrumb" <?php checked(LTASState::getID(), $this->getID()); ?> value="<?php echo LTASState::getID(); ?>" onchange="form.submit();" />
      <label for="<?php echo LONGTAIL_KEY . LTASState::getID(); ?>">
        <?php echo LTASState::getTitle(); ?>
      </label>
      <input type="radio" id="<?php echo LONGTAIL_KEY . PluginState::getID(); ?>" name="breadcrumb" <?php checked(PluginState::getID(), $this->getID()); ?> value="<?php echo PluginState::getID(); ?>" onchange="form.submit();" />
      <label for="<?php echo LONGTAIL_KEY . PluginState::getID(); ?>">
        <?php echo PluginState::getTitle(); ?>
      </label>
    </div>
  <?php }

  abstract public static function getTitle();

}
?>
