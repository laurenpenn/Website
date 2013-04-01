<?php

define("JW_SETUP_DESC", sprintf(__("The JW Player&trade; is used to deliver video content through your WordPress website.  For more information please visit <a href=http://www.longtailvideo.com/%s target=_blank>LongTail Video</a>.", 'jw-player-plugin-for-wordpress'), JW_PLAYER_GA_VARS));
define("JW_SETUP_EDIT_PLAYERS", __("This section allows you to create custom players. It is possible to configure flashvars, skins and plugins.", 'jw-player-plugin-for-wordpress'));
define("JW_LICENSED", __("To obtain a licensed player, please purchase a license from LongTail Video.", 'jw-player-plugin-for-wordpress'));

/**
 * Responsible for the display of the Player management page.
 * @file Class definition of PlayerState.
 * @see AdminState
 */
class PlayerState extends AdminState {

  /**
   * @see AdminState::__construct()
   * @param $player
   * @param string $post_values
   * @return \PlayerState
   */
  public function __construct($player, $post_values = "") {
    parent::__construct($player, $post_values);
  }

  /**
   * @see AdminState::getID()
   * @return string
   */
  public static function getID() {
    return "player";
  }

  /**
   * @see AdminState::getNextState()
   * @return \BasicState
   */
  public function getNextState() {
    LongTailFramework::setConfig($this->current_player);
    return new BasicState($this->current_player, $this->current_post_values);
  }

  /**
   * @see AdminState::getPreviousState()
   */
  public function getPreviousState() {
    echo "This shouldn't be called";
  }

  /**
   * @see AdminState::getCancelState()
   */
  public function getCancelState() {
    echo "This shouldn't be called";
  }

  /**
   * @see AdminState::getSaveState()
   */
  public function getSaveState() {
    echo "This shouldn't be called";
  }

  /**
   * @see AdminState::render()
   */
  public function render() {
    $players = LongTailFramework::getConfigs();
    if (!$players) $this->infoMessage(sprintf(__("If you wish to create custom players please make sure the %s/configs/" . " directory exists and is writable.  This directory is necessary for creating custom players.  ", 'jw-player-plugin-for-wordpress') . JW_FILE_PERMISSIONS, JWPLAYER_FILES_DIR)); ?>
    <div class="wrap">

      <script type="text/javascript">

        function updateHandler(button) {
          button.form.submit();
        }

        function selectionHandler(button) {
          var field = document.getElementById("<?php echo LONGTAIL_KEY . "player" ?>");
          field.setAttribute("value", button.id.replace("<?php echo LONGTAIL_KEY . "player_"; ?>", ""));
          var field = document.getElementById("<?php echo LONGTAIL_KEY . "new_player"; ?>");
          field.setAttribute("value", button.id.replace("<?php echo LONGTAIL_KEY . "player_"; ?>", ""));
        }

        function copyHandler(button) {
          var field = document.getElementById("<?php echo LONGTAIL_KEY . "player" ?>");
          field.setAttribute("value", button.id.replace("<?php echo LONGTAIL_KEY . "player_"; ?>", ""));
          var field = document.getElementById("<?php echo LONGTAIL_KEY . "new_player"; ?>");
          field.setAttribute("value", button.id.replace("<?php echo LONGTAIL_KEY . "player_"; ?>", "") + "_copy");
        }

        function deleteHandler(button) {
          var result = confirm(__("Are you sure wish to delete the Player?", 'jw-player-plugin-for-wordpress'));
          if (result) {
            selectionHandler(button);
            return true;
          }
          return false;
        }

      </script>

      <h2>JW Player Setup</h2>
      <p><span><?php echo JW_SETUP_EDIT_PLAYERS; ?></span><p>
      <?php /* Please upgrade to JWP6! if (file_exists(LongTailFramework::getPrimaryPlayerPath())) { ?>
        <form name="<?php echo LONGTAIL_KEY . "upgrade_form" ?>" method="post" action="admin.php?page=jwplayer-update">
          <?php $version = get_option(LONGTAIL_KEY . "version"); ?>
          <?php $alternate = get_option(LONGTAIL_KEY . "player_location_enable"); ?>
          <?php $location = get_option(LONGTAIL_KEY . "player_location"); ?>
          <?php $jwPlayer = file_exists(LongTailFramework::getPlayerPath()); ?>
          <?php $jwEmbedder = file_exists(LongTailFramework::getEmbedderPath()); ?>
            <div id="poststuff">
              <div id="post-body">
                <div id="post-body-content">
                  <div class="stuffbox">
                    <h3 class="hndle"><span><?php _e("JW Player Status", 'jw-player-plugin-for-wordpress'); ?></span></h3>
                    <div class="inside" style="margin: 15px;">
                      <table>
                        <tr valign="top">
                          <td>
                            <div>
                            <?php if (!$alternate) { ?>
                              <p>
                                <span><strong>JW Player:</strong> <?php echo $jwPlayer ? __("Installed", 'jw-player-plugin-for-wordpress') : __("Not detected", 'jw-player-plugin-for-wordpress'); echo $version && $jwPlayer ? " (JW Player $version)" : ""; ?></span>
                              </p>
                              <p><span><strong>JW Embedder:</strong></span> <?php echo $jwEmbedder ? __("Installed", 'jw-player-plugin-for-wordpress') : __("Not detected (SWFObject will be used instead)", 'jw-player-plugin-for-wordpress'); ?></p>
                              <?php if (!strstr($version, "Licensed")) { ?>
                                <p><span><strong><?php _e("Please Note:", 'jw-player-plugin-for-wordpress');?></strong> <?php _e("The JW Player Plugin for WordPress does not yet support JW Player 6.  We are working quickly to update the plugin over the next few weeks. By early 2013, you will be able to use WordPress with JW Player 6.  For now, we recommend sticking with JW Player 5. Please stay tuned!", 'jw-player-plugin-for-wordpress'); ?></span></p>
                                <p><strong><?php _e("Supported Versions:", 'jw-player-plugin-for-wordpress'); ?></strong></p>
                                <table style="border: 1px solid gray;">
                                  <tr>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.0</td>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.1</td>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.2</td>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.3</td>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.4</td>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.5</td>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.6</td>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.7</td>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.8</td>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.9</td>
                                    <td style="border: 1px solid gray; width: 30px; text-align: center">5.10</td>
                                  </tr>
                                </table>
                                <p><em><?php _e("If you have already purchased a commercial upgrade for JW6, we can either process a refund, or you can keep your commercial license until the plugin is updated.  We apologize for the inconvenience.");?></em></p>
                              <?php } ?>
                            <?php } else if ($alternate) { ?>
                              <p><span><?php echo "<strong>" . __("Current Player", 'jw-player-plugin-for-wordpress') . ":</strong> " . __("Version Unknown", 'jw-player-plugin-for-wordpress'); ?></span></p>
                              <p><span><?php printf(__("The player is being loaded from an alternate location (<strong>%s</strong>) and is not being managed by the plugin.", 'jw-player-plugin-for-wordpress'), $location); ?></span></p>
                            <?php } else { ?>
                              <p><span><?php _e("<strong>Current Player:</strong> Version Unknown", 'jw-player-plugin-for-wordpress'); ?></span></p>
                              <p><input class="button-secondary" type="submit" name="Update_Player" value="<?php _e("Click Here to Reinstall", 'jw-player-plugin-for-wordpress'); ?>" /></p>
                            <?php } ?>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      <?php } else if (file_exists(LongTailFramework::getSecondaryPlayerPath())) { ?>
        <form name="<?php echo LONGTAIL_KEY . "upgrade_form" ?>" method="post" action="admin.php?page=jwplayer-update">
          <span><?php _e("<strong>Current Player:</strong> Version Unknown ", 'jw-player-plugin-for-wordpress'); ?></span>
          <input class="button-secondary" type="submit" name="Update_Player" value="<?php _e("Click Here to Reinstall", 'jw-player-plugin-for-wordpress'); ?>" />
        </form>
      <?php } */?>
      <form name="<?php echo LONGTAIL_KEY . "form" ?>" method="post" action="">
      <div id="poststuff">
        <div id="post-body">
          <div id="post-body-content">
            <div class="stuffbox">
              <h3 class="hndle"><span>Manage Players</span></h3>
              <div class="inside" style="margin: 15px;">
                <table style="width: 100%;">
                  <tr valign="top">
                    <td>
                      <div>
                          <table class="widefat" cellspacing="0">
                            <thead>
                              <tr>
                                <th class="manage-column column-name" style="text-shadow: none;">Default</th>
                                <th class="manage-column column-name">Players</th>
                                <th class="manage-column column-name">Control Bar</th>
                                <th class="manage-column column-name">Skin</th>
                                <th class="manage-column column-name">Dock</th>
                                <th class="manage-column column-name">Autostart</th>
                                <th class="manage-column column-name">Height</th>
                                <th class="manage-column column-name">Width</th>
                                <th class="manage-column column-name" style="text-shadow: none;">Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td style="vertical-align: middle">
                                  <input onchange="updateHandler(this);" type="radio" id="<?php echo LONGTAIL_KEY . "default_Out-of-the-Box"; ?>" name="<?php echo LONGTAIL_KEY . "default"; ?>" value="<?php _e("Out-of-the-Box", 'jw-player-plugin-for-wordpress'); ?> <?php checked("Out-of-the-Box", get_option(LONGTAIL_KEY . "default")); ?>"/>
                                </td>
                                <td style="vertical-align: middle;"><span><?php echo "Out-of-the-Box"; ?></span></td>
                                <td style="vertical-align: middle;"><span><?php echo "bottom"; ?></span></td>
                                <td style="vertical-align: middle;"><span><?php echo "default"; ?></span></td>
                                <td style="vertical-align: middle;"><span><?php echo "false"; ?></span></td>
                                <td style="vertical-align: middle;"><span><?php echo "false"; ?></span></td>
                                <td style="vertical-align: middle;"><span><?php echo "300"; ?></span></td>
                                <td style="vertical-align: middle;"><span><?php echo "400"; ?></span></td>
                                <td style="vertical-align: middle;"><input class="button-secondary action" id="<?php echo LONGTAIL_KEY . "player_Out-of-the-Box"; ?>" type="submit" name="Next" value="<?php _e("Copy", 'jw-player-plugin-for-wordpress'); ?>" onclick="copyHandler(this)"/></td>
                              </tr>
                              <?php $alternate = false; ?>
                              <?php if ($players) { ?>
                                <?php foreach ($players as $player) { ?>
                                  <?php if ($player != __("New Player", 'jw-player-plugin-for-wordpress')) { ?>
                                    <?php $alternate = !$alternate; ?>
                                    <?php LongTailFramework::setConfig($player); ?>
                                    <?php $details = LongTailFramework::getPlayerFlashVars(LongTailFramework::BASIC); ?>
                                    <tr <?php if ($alternate) echo "class=\"alternate\""; ?> >
                                      <td style="vertical-align: middle;">
                                        <input onchange="updateHandler(this);" type="radio" id="<?php echo LONGTAIL_KEY . "default_" . $player; ?>" name="<?php echo LONGTAIL_KEY . "default"; ?>" value="<?php echo $player; ?>" <?php checked($player, get_option(LONGTAIL_KEY . "default")); ?>/>
                                      </td>
                                      <td style="vertical-align: middle;"><span><?php echo $player ?></span></td>
                                      <?php foreach (array_keys($details) as $detail) { ?>
                                        <?php foreach($details[$detail] as $fvar) { ?>
                                          <td style="vertical-align: middle;"><span><?php echo $fvar->getDefaultValue() ? $fvar->getDefaultValue() : "default"; ?></span></td>
                                        <?php } ?>
                                      <?php } ?>
                                      <td>
                                        <input class="button-secondary action" id="<?php echo LONGTAIL_KEY . "player_" . $player; ?>" type="submit" name="Next" value="<?php _e("Copy", 'jw-player-plugin-for-wordpress'); ?>" onclick="copyHandler(this)"/>
                                        <input class="button-secondary action" id="<?php echo LONGTAIL_KEY . "player_" . $player; ?>" type="submit" name="Next" value="<?php _e("Edit", 'jw-player-plugin-for-wordpress'); ?>" onclick="selectionHandler(this)"/>
                                        <input class="button-secondary action" id="<?php echo LONGTAIL_KEY . "player_" . $player; ?>" type="submit" name="Next" value="<?php _e("Delete", 'jw-player-plugin-for-wordpress'); ?>" onclick="return deleteHandler(this)"/>
                                      </td>
                                    </tr>
                                  <?php } ?>
                                <?php } ?>
                              <?php } ?>
                            </tbody>
                          </table>
                          <br/>
                          <input class="button-secondary action" type="submit" name="Next" value="<?php _e("Create Custom Player", 'jw-player-plugin-for-wordpress'); ?>"/>
                          <input id="<?php echo LONGTAIL_KEY . "new_player"; ?>" type="hidden" name="<?php echo LONGTAIL_KEY . "new_player"; ?>" value=""/>
                          <input id="<?php echo LONGTAIL_KEY . "player"; ?>" type="hidden" name="<?php echo LONGTAIL_KEY . "config" ?>" value=""/>
                          <input type="hidden" name="<?php echo LONGTAIL_KEY . "state" ?>" value=<?php echo PlayerState::getID(); ?> />
                        </div>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <?php
  }

}
?>
