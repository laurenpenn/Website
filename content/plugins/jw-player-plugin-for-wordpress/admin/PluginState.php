<?php
/**
 * Responsible for rendering plugin selection and configuration.
 * @file Class definition of PluginState
 * @see AdminState
 */
class PluginState extends WizardState {

  /**
   * @see AdminState::__construct()
   * @param $player
   * @param string $post_values
   * @return \PluginState
   */
  public function __construct($player, $post_values = "") {
    parent::__construct($player, $post_values);
  }

  /**
   * @see AdminState::getID()
   * @return string
   */
  public static function getID() {
    return "plugin";
  }

  /**
   * @see AdminState::getNextState()
   */
  public function getNextState() {
    echo "This shouldn't be called";
  }

  /**
   * @see AdminState::getPreviousState()
   * @return \LTASState
   */
  public function getPreviousState() {
    LongTailFramework::setConfig($this->current_player);
    return new LTASState($this->current_player);
  }

  /**
   * @see AdminState::getCancelState()
   * @return \PlayerState
   */
  public function getCancelState() {
    return new PlayerState("");
  }

  /**
   * @see AdminState::getSaveState()
   * @return \PlayerState
   */
  public function getSaveState() {
    return new PlayerState("");
  }

  public static function getTitle() {
    return WizardState::PLUGIN_STATE;
  }

  /**
   * @see AdminState::render()
   */
  public function render() {
    $plugins = LongTailFramework::getPlugins();
    $configValues = LongTailFramework::getConfigValues();
    $pluginString = array();
    if (array_key_exists("plugins", $configValues)) {
      $pluginString = explode(",", $configValues["plugins"]);
    }
    $pluginList = array();
    foreach ($pluginString as $pluginStr) {
      $pluginList[$pluginStr] = $pluginStr;
    }
    ?>
    <div class="wrap">

      <script type="text/javascript">
        jQuery(function () {
          jQuery('#tabs').tabs();
          removeAllTabs();
          jQuery("<?php echo "#" . LONGTAIL_KEY . "plugin_selector_tab"; ?>").css("display", "block");
          <?php foreach ($plugins as $plugin) { ?>
            <?php if ($plugin->isEnabled()) { ?>
              jQuery("<?php echo "#" . LONGTAIL_KEY . "plugin_" . $plugin->getRepository() . "_tab"; ?>").css("display", "block");
            <?php } ?>
          <?php } ?>
          jQuery("#tabs").css("display", "block");
          jQuery(":checkbox").change(function() {
            var name = "#" + jQuery(this)[0].name.replace("enable", "tab");
            var hidden = jQuery(this)[0].name + "_hidden";
            if(jQuery(this)[0].checked) {
              jQuery(name).css("display", "block");
              jQuery(name).val(true);
            } else {
              jQuery(name).css("display", "none");
              jQuery(name).val(false);
            }
          })
        });

        function removeAllTabs() {
          jQuery("#tabNavigation").children().css("display","none");
        }
      </script>

      <form name="<?php echo LONGTAIL_KEY . "form" ?>" method="post" action="">
        <?php parent::getBreadcrumbBar(); ?>
        <?php $this->selectedPlayer(); ?>
        <p/>
        <div id="tabs">

          <ul id="tabNavigation">
            <?php $id = LONGTAIL_KEY . "plugin_selector"; ?>
            <li id="<?php echo $id . "_tab"; ?>"><a href="<?php echo "#" . $id; ?>">Plugin Selector</a></li>
            <?php foreach ($plugins as $plugin) { ?>
              <?php $id = LONGTAIL_KEY . "plugin_" . $plugin->getRepository(); ?>
              <li id="<?php echo $id . "_tab"; ?>"><a href="<?php echo "#" . $id; ?>"><?php echo $plugin->getTitle(); ?></a></li>
            <?php } ?>
          </ul>

          <div id="<?php echo LONGTAIL_KEY . "plugin_selector"; ?>">
            <table class="form-table">
              <?php foreach($plugins as $plugin) { ?>
                <?php $name = LONGTAIL_KEY . "plugin_" . $plugin->getRepository() . "_" . "enable"; ?>
                <?php $value = isset($_POST[$name]) ? $_POST[$name] : $plugin->isEnabled(); ?>
                <?php unset($_POST[$name]); ?>
                <tr valign="top">
                  <th>Enable <?php echo $plugin->getTitle(); ?>:</th>
                  <td>
                    <input name="<?php echo $name; ?>" type="checkbox" value="1" <?php checked(true , $value); ?> />
                    <input name="<?php echo $name . "_hidden"; ?>" type="hidden" value="0"/>
                    <span class="description"><?php echo __($plugin->getDescription(), 'jw-player-plugin-for-wordpress') . "  <a href=" . $plugin->getPage() . JW_PLAYER_GA_VARS . " target=_blank>" . __("Learn more...", 'jw-player-plugin-for-wordpress') . "</a>"; ?></span>
                  </td>
                </tr>
                <tr>
                  <td colspan="2"></td>
                </tr>
              <?php } ?>
            </table>
          </div>

          <?php foreach($plugins as $plugin) { ?>
            <?php unset($pluginList[$plugin->getRepository()]); ?>
            <div id="<?php echo LONGTAIL_KEY . "plugin_" . $plugin->getRepository(); ?>">
              <table class="form-table">
                <?php foreach(array_keys($plugin->getFlashVars()) as $plugin_flash_vars) { ?>
                  <?php $p_vars = $plugin->getFlashVars(); ?>
                  <?php foreach($p_vars[$plugin_flash_vars] as $plugin_flash_var) { ?>
                    <tr valign="top">
                      <?php $name = LONGTAIL_KEY . "plugin_" . $plugin->getPluginPrefix() . "_" . $plugin_flash_var->getName(); ?>
                      <?php $value = isset($_POST[$name]) ? $_POST[$name] : $plugin_flash_var->getDefaultValue(); ?>
                      <?php unset($_POST[$name]); ?>
                      <th><?php echo $plugin->getPluginPrefix() . "." . $plugin_flash_var->getName(); ?>:</th>
                      <td>
                        <?php if ($plugin_flash_var->getType() == FlashVar::SELECT) { ?>
                          <select size="1" name="<?php echo $name; ?>">
                            <?php foreach($plugin_flash_var->getValues() as $val) { ?>
                              <option value="<?php echo $val ?>" <?php selected($val, $value); ?>>
                                <?php echo htmlentities($val) ?>
                              </option>
                            <?php } ?>
                          </select>
                        <?php } else { ?>
                        <input type="text" value="<?php echo $value; ?>" name="<?php echo $name; ?>" />
                        <?php } ?>
                        <span class="description"><?php echo $plugin_flash_var->getDescription(); ?></span>
                      </td>
                    </tr>
                  <?php } ?>
                <?php } ?>
              </table>
            </div>
          <?php } ?>
        </div>

        <?php $this->getFooter($pluginList); ?>
        <?php $this->buttonBar(PluginState::getID(), true, false); ?>

      </form>
    </div>
  <?php
  }

  protected function getFooter($pluginList) { ?>
    <p/>
    <div id="poststuff">
      <div id="post-body">
        <div id="post-body-content">
          <div class="stuffbox">
            <h3 class="hndle"><span><?php echo "Additional Plugins"; ?></span></h3>
            <div class="inside" style="margin: 15px;">
              <table class="form-table">
                <tr valign="top">
                  <th><?php echo "plugins:"; ?></th>
                  <td>
                    <?php $name = LONGTAIL_KEY . "player_plugins"; ?>
                    <?php $value = isset($_POST[$name]) ? $_POST[$name] : implode(",", $pluginList); ?>
                    <?php unset($_POST[$name]); ?>
                    <textarea name="<?php echo $name; ?>" cols="80" rows="2"><?php echo $value; ?></textarea>
                    <br/>
                    <span class="description"><?php _e("Enter a comma delimited list of additional plugins you would like to be used by this player.  <strong>Note:</strong> Flashvars for these plugins will need to be set in the Additional Flashvars section under the Advanced Settings tab.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div> <?php
  }

}

?>
