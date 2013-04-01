<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

if (isset($_POST['Uninstall'])) {
  uninstall();
} else {
  if (isset($_POST["category_config"])) update_option(LONGTAIL_KEY . "category_mode", $_POST["category_config"]);
  if (isset($_POST["search_config"])) update_option(LONGTAIL_KEY . "search_mode", $_POST["search_config"]);
  if (isset($_POST["tag_config"])) update_option(LONGTAIL_KEY . "tag_mode", $_POST["tag_config"]);
  if (isset($_POST["home_config"])) update_option(LONGTAIL_KEY . "home_mode", $_POST["home_config"]);

  if (isset($_POST["player_location_enable"])) update_option(LONGTAIL_KEY . "player_location_enable", $_POST["player_location_enable"]);
  if (isset($_POST["player_location"])) update_option(LONGTAIL_KEY . "player_location", $_POST["player_location"]);
  if (isset($_POST["player_mode"])) update_option(LONGTAIL_KEY . "player_mode", $_POST["player_mode"]);

  if (isset($_POST["image_duration"])) update_option(LONGTAIL_KEY . "image_duration", $_POST["image_duration"]);
  if (isset($_POST["image_insert"])) update_option(LONGTAIL_KEY . "image_insert", $_POST["image_insert"]);
  if (isset($_POST["facebook"])) update_option(LONGTAIL_KEY . "facebook", $_POST["facebook"]);

  if (isset($_POST["ssl"])) update_option(LONGTAIL_KEY . "use_ssl", $_POST["ssl"]);
  if (isset($_POST["head_js"])) update_option(LONGTAIL_KEY . "use_head_js", $_POST["head_js"]);
  if (isset($_POST["allow_tracking"])) update_option(LONGTAIL_KEY . "allow_tracking", $_POST["allow_tracking"]);
}

function uninstall() {
  global $wpdb;

  $meta_query = "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '" . LONGTAIL_KEY . "%';";
  $option_query = "DELETE FROM $wpdb->options WHERE option_name LIKE '" . LONGTAIL_KEY . "%';";
  $post_query = "DELETE FROM $wpdb->posts WHERE post_type = 'jw_playlist';";

  $wpdb->query($meta_query);
  $wpdb->query($option_query);
  $wpdb->query($post_query);

  @unlink(LongTailFramework::getPlayerPath());
  @unlink(LongTailFramework::getEmbedderPath());
  @rmdir(JWPLAYER_FILES_DIR . "/player/");

  $handler = @opendir(JWPLAYER_FILES_DIR . "/configs");
  if ($handler) {
    while ($file = readdir($handler)) {
      if ($file != "." && $file != ".." && strstr($file, ".xml")) {
        @unlink(JWPLAYER_FILES_DIR . "/configs/$file");
      }
    }
    closedir($handler);
  }
  @rmdir(JWPLAYER_FILES_DIR . "/configs/");
  @rmdir(JWPLAYER_FILES_DIR);

  update_option(LONGTAIL_KEY . "uninstalled", true);
  feedback_message(__('Files and settings deleted.  The plugin can now be deactivated.', 'jw-player-plugin-for-wordpress'));
}

function feedback_message ($message, $timeout = 0) { ?>
  <div class="fade updated" id="message" onclick="this.parentNode.removeChild (this)">
    <p><strong><?php echo $message ?></strong></p>
  </div> <?php
}

?>
 
<div class="wrap">
  <h2><?php _e("JW Player Plugin Settings", 'jw-player-plugin-for-wordpress'); ?></h2>
  <form name="<?php echo LONGTAIL_KEY . "form" ?>" method="post" action="">
    <div id="poststuff">
      <div id="post-body">
        <div id="post-body-content">
          <div class="stuffbox">
            <h3 class="hndle"><span><?php _e("Shortcode Settings", 'jw-player-plugin-for-wordpress'); ?></span></h3>
            <div class="inside" style="margin: 15px;">
              <p><em><?php _e("Configure the source for each page type.  This is dependent on your theme which can either pull from <strong>the_content</strong> or <strong>the_excerpt</strong>.  Optionally you can disable embedding on a specific page type.  This will strip out the shortcode.", 'jw-player-plugin-for-wordpress'); ?></em></p>
              <table class="form-table">
                <tr valign="top">
                  <th><?php _e("Category Pages:", 'jw-player-plugin-for-wordpress'); ?></th>
                  <td>
                    <label for="category_excerpt"><?php _e("Excerpt", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="category_excerpt" type="radio" value="excerpt" name="category_config" onclick="form.submit();" <?php checked("excerpt", get_option(LONGTAIL_KEY . "category_mode")); ?> />
                    <label for="category_content"><?php _e("Content", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="category_content" type="radio" value="content" name="category_config" onclick="form.submit();" <?php checked("content", get_option(LONGTAIL_KEY . "category_mode")); ?> />
                    <label for="category_disable"><?php _e("Disable", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="category_disable" type="radio" value="disable" name="category_config" onclick="form.submit();" <?php checked("disable", get_option(LONGTAIL_KEY . "category_mode")); ?> />
                    <span class="description"><?php _e("Configure JW Player shortcode behavior on category pages.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
                <tr>
                  <th><?php _e("Search Pages:", 'jw-player-plugin-for-wordpress'); ?></th>
                  <td>
                    <label for="search_excerpt"><?php _e("Excerpt", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="search_excerpt" type="radio" value="excerpt" name="search_config" onclick="form.submit();" <?php checked("excerpt", get_option(LONGTAIL_KEY . "search_mode")); ?> />
                    <label for="search_content"><?php _e("Content", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="search_content" type="radio" value="content" name="search_config" onclick="form.submit();" <?php checked("content", get_option(LONGTAIL_KEY . "search_mode")); ?> />
                    <label for="search_disable"><?php _e("Disable", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="search_disable" type="radio" value="disable" name="search_config" onclick="form.submit();" <?php checked("disable", get_option(LONGTAIL_KEY . "search_mode")); ?> />
                    <span class="description"><?php _e("Configure JW Player shortcode behavior on search result pages.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
                <tr>
                  <th>Tag Pages:</th>
                  <td>
                    <label for="tag_excerpt"><?php _e("Excerpt", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="tag_excerpt" type="radio" value="excerpt" name="tag_config" onclick="form.submit();" <?php checked("excerpt", get_option(LONGTAIL_KEY . "tag_mode")); ?> />
                    <label for="tag_content"><?php _e("Content", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="tag_content" type="radio" value="content" name="tag_config" onclick="form.submit();" <?php checked("content", get_option(LONGTAIL_KEY . "tag_mode")); ?> />
                    <label for="tag_disable"><?php _e("Disable", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="tag_disable" type="radio" value="disable" name="tag_config" onclick="form.submit();" <?php checked("disable", get_option(LONGTAIL_KEY . "tag_mode")); ?> />
                    <span class="description"><?php _e("Configure JW Player shortcode behavior on tag pages.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
                <tr>
                  <th>Home Page:</th>
                  <td>
                    <label for="home_excerpt"><?php _e("Excerpt", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="home_excerpt" type="radio" value="excerpt" name="home_config" onclick="form.submit();" <?php checked("excerpt", get_option(LONGTAIL_KEY . "home_mode")); ?> />
                    <label for="home_content"><?php _e("Content", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="home_content" type="radio" value="content" name="home_config" onclick="form.submit();" <?php checked("content", get_option(LONGTAIL_KEY . "home_mode")); ?> />
                    <label for="home_disable"><?php _e("Disable", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="home_disable" type="radio" value="disable" name="home_config" onclick="form.submit();" <?php checked("disable", get_option(LONGTAIL_KEY . "home_mode")); ?> />
                    <span class="description"><?php _e("Configure JW Player shortcode behavior on the home page.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="poststuff">
      <div id="post-body">
        <div id="post-body-content">
          <div class="stuffbox">
            <h3 class="hndle"><span><?php _e("Player Settings", 'jw-player-plugin-for-wordpress'); ?></span></h3>
            <div class="inside" style="margin: 15px;">
              <table class="form-table">
                <tr valign="top">
                  <th><?php _e("Enable Alternative Player Location:", 'jw-player-plugin-for-wordpress'); ?></th>
                  <td>
                    <label for="player_location_yes"><?php _e("Yes", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="player_location_yes" type="radio" value="1" name="player_location_enable" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "player_location_enable")); ?> />
                    <label for="player_location_no"><?php _e("No", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="player_location_no" type="radio" value="0" name="player_location_enable" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "player_location_enable")); ?> />
                    <span class="description"><?php _e("When enabled the plugin will load the player from the specified location.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
                <tr valign="top">
                  <th><?php _e("Player Location:", 'jw-player-plugin-for-wordpress'); ?></th>
                  <td>
                    <input type="text" id="player_location" name="player_location" value="<?php echo get_option(LONGTAIL_KEY . "player_location"); ?>" onblur="form.submit();" style="width: 300px;"/>
                    <span class="description"><?php _e("Configure the location the player.swf and jwplayer.js files should be loaded from.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
                <tr valign="top">
                  <th><?php _e("Select Primary Mode:", 'jw-player-plugin-for-wordpress') ?></th>
                  <td>
                    <label for="player_mode_yes"><?php _e("Flash", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="player_mode_yes" type="radio" value="flash" name="player_mode" onclick="form.submit();" <?php checked("flash", get_option(LONGTAIL_KEY . "player_mode")); ?> />
                    <label for="player_mode_no"><?php _e("HTML5", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="player_mode_no" type="radio" value="html5" name="player_mode" onclick="form.submit();" <?php checked("html5", get_option(LONGTAIL_KEY . "player_mode")); ?> />
                    <span class="description"><?php _e("Select which mode the player will default to (Flash or HTML5).", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="poststuff">
      <div id="post-body">
        <div id="post-body-content">
          <div class="stuffbox">
            <h3 class="hndle"><span><?php _e("Content Settings", 'jw-player-plugin-for-wordpress'); ?></span></h3>
            <div class="inside" style="margin: 15px;">
              <table class="form-table">
                <tr valign="top">
                  <th><?php _e("Show Duration on Images:", 'jw-player-plugin-for-wordpress'); ?></th>
                  <td>
                    <label for="image_duration_yes"><?php _e("Yes", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="image_duration_yes" type="radio" value="1" name="image_duration" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "image_duration")); ?> />
                    <label for="image_duration_no"><?php _e("No", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="image_duration_no" type="radio" value="0" name="image_duration" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "image_duration")); ?> />
                    <span class="description"><?php _e("Controls whether the duration field is visible when editing the meta data for images.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
                <tr valign="top">
                  <th><?php _e("Show Insert Button on Images:", 'jw-player-plugin-for-wordpress'); ?></th>
                  <td>
                    <label for="image_insert_yes"><?php _e("Yes", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="image_insert_yes" type="radio" value="1" name="image_insert" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "image_insert")); ?> />
                    <label for="image_insert_no"><?php _e("No", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="image_insert_no" type="radio" value="0" name="image_insert" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "image_insert")); ?> />
                    <span class="description"><?php _e("Controls whether the insert button is visible when editing the meta data for images.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
                <tr valign="top">
                  <th><?php _e("Enable Facebook Open Graph Data:", 'jw-player-plugin-for-wordpress'); ?></th>
                  <td>
                    <label for="facebook_yes"><?php _e("Yes", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="facebook_yes" type="radio" value="1" name="facebook" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "facebook")); ?> />
                    <label for="facebook_no"><?php _e("No", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="facebook_no" type="radio" value="0" name="facebook" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "facebook")); ?> />
                    <span class="description"><?php _e("Whether or not Facebook Open Graph information should be inserted into the page for sharing on Facebook.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="poststuff">
      <div id="post-body">
        <div id="post-body-content">
          <div class="stuffbox">
            <h3 class="hndle"><span><?php _e("Site Settings", 'jw-player-plugin-for-wordpress'); ?></span></h3>
            <div class="inside" style="margin: 15px;">
              <table class="form-table">
                <tr valign="top">
                  <th><?php _e("Use SSL when loading the player:", 'jw-player-plugin-for-wordpress'); ?></th>
                  <td>
                    <label for="ssl_yes"><?php _e("Yes", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="ssl_yes" type="radio" value="1" name="ssl" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "use_ssl")); ?> />
                    <label for="ssl_no"><?php _e("No", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="ssl_no" type="radio" value="0" name="ssl" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "use_ssl")); ?> />
                    <span class="description"><?php _e("Controls whether the plugin will load the player, configs and skins using https if your site is https.  <strong>Note:</strong>You will need to resave your players after making a change.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
                <tr valign="top">
                  <th><?php _e("Load jwplayer.js/swfobject.js in page head:", 'jw-player-plugin-for-wordpress'); ?></th>
                  <td>
                    <label for="head_js_yes"><?php _e("Yes", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="head_js_yes" type="radio" value="1" name="head_js" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "use_head_js")); ?> />
                    <label for="head_js_no"><?php _e("No", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="head_js_no" type="radio" value="0" name="head_js" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "use_head_js")); ?> />
                    <span class="description"><?php _e("Controls whether the plugin will insert the jwplayer.js or swfobject.js files into the head of every page.  If set to No jwplayer.js or swfobject.js will only be included on pages where the jwplayer.js shortcode is used.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
                <tr valign="top">
                  <th><?php _e("Allow anonymous analytics tracking:", 'jw-player-plugin-for-wordpress'); ?></th>
                  <td>
                    <label for="tracking_yes"><?php _e("Yes", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="tracking_yes" type="radio" value="1" name="allow_tracking" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "allow_tracking")); ?> />
                    <label for="tracking_no"><?php _e("No", 'jw-player-plugin-for-wordpress'); ?></label>
                    <input id="tracking_no" type="radio" value="0" name="allow_tracking" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "allow_tracking")); ?> />
                    <span class="description"><?php _e("Allow LongTail Video to track plugin feature usage.  This will help us improve the plugin in the future.  <strong>Note: Tracking is done anonymously.</strong>", 'jw-player-plugin-for-wordpress'); ?></span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="poststuff">
      <div id="post-body">
        <div id="post-body-content">
          <div class="stuffbox">
            <h3 class="hndle"><span><?php _e("Uninstall", 'jw-player-plugin-for-wordpress'); ?></span></h3>
            <div class="inside" style="margin: 15px;">
              <table>
                <tr valign="top">
                  <td>
                    <div>
                      <p><?php _e('To fully remove the plugin, click the Uninstall button.  Deactivating without uninstalling will not remove the data created by the plugin.', 'jw-player-plugin-for-wordpress') ;?></p>
                    </div>
                    <p><span style="color: red; "><strong><?php _e('WARNING:', 'jw-player-plugin-for-wordpress') ;?></strong><br />
                    <?php _e('This cannot be undone.  Since this is deleting data from your database, it is recommended that you create a backup.', 'jw-player-plugin-for-wordpress') ;?></span></p>
                    <div align="left">
                      <input type="submit" name="Uninstall" class="button-secondary delete" value="<?php _e('Uninstall plugin', 'jw-player-plugin-for-wordpress') ?>" onclick="return confirm('<?php _e('You are about to Uninstall this plugin from WordPress.\nThis action is not reversible.\n\nChoose [Cancel] to Stop, [OK] to Uninstall.\n', 'jw-player-plugin-for-wordpress'); ?>');"/>
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