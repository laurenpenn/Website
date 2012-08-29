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
  feedback_message(__('Files and settings deleted.  The plugin can now be deactivated.'));
}

function feedback_message ($message, $timeout = 0) { ?>
  <div class="fade updated" id="message" onclick="this.parentNode.removeChild (this)">
    <p><strong><?php echo $message ?></strong></p>
  </div> <?php
}

?>
 
<div class="wrap">
  <h2><?php echo "JW Player Plugin Settings"; ?></h2>
  <form name="<?php echo LONGTAIL_KEY . "form" ?>" method="post" action="">
    <div id="poststuff">
      <div id="post-body">
        <div id="post-body-content">
          <div class="stuffbox">
            <h3 class="hndle"><span>Shortcode Settings</span></h3>
            <div class="inside" style="margin: 15px;">
              <p><em>Configure the source for each page type.  This is dependent on your theme which can either pull from <strong>the_content</strong> or <strong>the_excerpt</strong>.  Optionally you can disable embedding on a specific page type.  This will strip out the shortcode.</em></p>
              <table class="form-table">
                <tr valign="top">
                  <th>Category Pages:</th>
                  <td>
                    <label for="category_excerpt">Excerpt</label>
                    <input id="category_excerpt" type="radio" value="excerpt" name="category_config" onclick="form.submit();" <?php checked("excerpt", get_option(LONGTAIL_KEY . "category_mode")); ?> />
                    <label for="category_content">Content</label>
                    <input id="category_content" type="radio" value="content" name="category_config" onclick="form.submit();" <?php checked("content", get_option(LONGTAIL_KEY . "category_mode")); ?> />
                    <label for="category_disable">Disable</label>
                    <input id="category_disable" type="radio" value="disable" name="category_config" onclick="form.submit();" <?php checked("disable", get_option(LONGTAIL_KEY . "category_mode")); ?> />
                    <span class="description">Configure JW Player shortcode behavior on category pages.</span>
                  </td>
                </tr>
                <tr>
                  <th>Search Pages:</th>
                  <td>
                    <label for="search_excerpt">Excerpt</label>
                    <input id="search_excerpt" type="radio" value="excerpt" name="search_config" onclick="form.submit();" <?php checked("excerpt", get_option(LONGTAIL_KEY . "search_mode")); ?> />
                    <label for="search_content">Content</label>
                    <input id="search_content" type="radio" value="content" name="search_config" onclick="form.submit();" <?php checked("content", get_option(LONGTAIL_KEY . "search_mode")); ?> />
                    <label for="search_disable">Disable</label>
                    <input id="search_disable" type="radio" value="disable" name="search_config" onclick="form.submit();" <?php checked("disable", get_option(LONGTAIL_KEY . "search_mode")); ?> />
                    <span class="description">Confgiure JW Player shortcode behavior on search result pages.</span>
                  </td>
                </tr>
                <tr>
                  <th>Tag Pages:</th>
                  <td>
                    <label for="tag_excerpt">Excerpt</label>
                    <input id="tag_excerpt" type="radio" value="excerpt" name="tag_config" onclick="form.submit();" <?php checked("excerpt", get_option(LONGTAIL_KEY . "tag_mode")); ?> />
                    <label for="tag_content">Content</label>
                    <input id="tag_content" type="radio" value="content" name="tag_config" onclick="form.submit();" <?php checked("content", get_option(LONGTAIL_KEY . "tag_mode")); ?> />
                    <label for="tag_disable">Disable</label>
                    <input id="tag_disable" type="radio" value="disable" name="tag_config" onclick="form.submit();" <?php checked("disable", get_option(LONGTAIL_KEY . "tag_mode")); ?> />
                    <span class="description">Confgiure JW Player shortcode behavior on tag pages.</span>
                  </td>
                </tr>
                <tr>
                  <th>Home Page:</th>
                  <td>
                    <label for="home_excerpt">Excerpt</label>
                    <input id="home_excerpt" type="radio" value="excerpt" name="home_config" onclick="form.submit();" <?php checked("excerpt", get_option(LONGTAIL_KEY . "home_mode")); ?> />
                    <label for="home_content">Content</label>
                    <input id="home_content" type="radio" value="content" name="home_config" onclick="form.submit();" <?php checked("content", get_option(LONGTAIL_KEY . "home_mode")); ?> />
                    <label for="home_disable">Disable</label>
                    <input id="home_disable" type="radio" value="disable" name="home_config" onclick="form.submit();" <?php checked("disable", get_option(LONGTAIL_KEY . "home_mode")); ?> />
                    <span class="description">Confgiure JW Player shortcode behavior on the home page.</span>
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
            <h3 class="hndle"><span>Player Settings</span></h3>
            <div class="inside" style="margin: 15px;">
              <table class="form-table">
                <tr valign="top">
                  <th>Enable Alternative Player Location:</th>
                  <td>
                    <label for="player_location_yes">Yes</label>
                    <input id="player_location_yes" type="radio" value="1" name="player_location_enable" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "player_location_enable")); ?> />
                    <label for="player_location_no">No</label>
                    <input id="player_location_no" type="radio" value="0" name="player_location_enable" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "player_location_enable")); ?> />
                    <span class="description">When enabled the plugin will load the player from the specified location.</span>
                  </td>
                </tr>
                <tr valign="top">
                  <th>Player Location:</th>
                  <td>
                    <input type="text" id="player_location" name="player_location" value="<?php echo get_option(LONGTAIL_KEY . "player_location"); ?>" onblur="form.submit();" style="width: 300px;"/>
                    <span class="description">Configure the location the player.swf and jwplayer.js files should be loaded from.</span>
                  </td>
                </tr>
                <tr valign="top">
                  <th>Select Primary Mode:</th>
                  <td>
                    <label for="player_mode_yes">Flash</label>
                    <input id="player_mode_yes" type="radio" value="flash" name="player_mode" onclick="form.submit();" <?php checked("flash", get_option(LONGTAIL_KEY . "player_mode")); ?> />
                    <label for="player_mode_no">HTML5</label>
                    <input id="player_mode_no" type="radio" value="html5" name="player_mode" onclick="form.submit();" <?php checked("html5", get_option(LONGTAIL_KEY . "player_mode")); ?> />
                    <span class="description">Select which mode the player will default to (Flash or HTML5).</span>
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
            <h3 class="hndle"><span>Content Settings</span></h3>
            <div class="inside" style="margin: 15px;">
              <table class="form-table">
                <tr valign="top">
                  <th>Show Duration on Images:</th>
                  <td>
                    <label for="image_duration_yes">Yes</label>
                    <input id="image_duration_yes" type="radio" value="1" name="image_duration" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "image_duration")); ?> />
                    <label for="image_duration_no">No</label>
                    <input id="image_duration_no" type="radio" value="0" name="image_duration" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "image_duration")); ?> />
                    <span class="description">Controls whether the duration field is visible when editing the meta data for images.</span>
                  </td>
                </tr>
                <tr valign="top">
                  <th>Show Insert Button on Images:</th>
                  <td>
                    <label for="image_insert_yes">Yes</label>
                    <input id="image_insert_yes" type="radio" value="1" name="image_insert" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "image_insert")); ?> />
                    <label for="image_insert_no">No</label>
                    <input id="image_insert_no" type="radio" value="0" name="image_insert" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "image_insert")); ?> />
                    <span class="description">Controls whether the insert button is visible when editing the meta data for images.</span>
                  </td>
                </tr>
                <tr valign="top">
                  <th>Enable Facebook Open Graph Data:</th>
                  <td>
                    <label for="facebook_yes">Yes</label>
                    <input id="facebook_yes" type="radio" value="1" name="facebook" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "facebook")); ?> />
                    <label for="facebook_no">No</label>
                    <input id="facebook_no" type="radio" value="0" name="facebook" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "facebook")); ?> />
                    <span class="description">Whether or not Facebook Open Graph information should be inserted into the page for sharing on Facebook.</span>
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
            <h3 class="hndle"><span>Site Settings</span></h3>
            <div class="inside" style="margin: 15px;">
              <table class="form-table">
                <tr valign="top">
                  <th>Use SSL when loading the player:</th>
                  <td>
                    <label for="ssl_yes">Yes</label>
                    <input id="ssl_yes" type="radio" value="1" name="ssl" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "use_ssl")); ?> />
                    <label for="ssl_no">No</label>
                    <input id="ssl_no" type="radio" value="0" name="ssl" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "use_ssl")); ?> />
                    <span class="description">Controls whether the plugin will load the player, configs and skins using https if your site is https.  <strong>Note:</strong>You will need to resave your players after making a change.</span>
                  </td>
                </tr>
                <tr valign="top">
                  <th>Load jwplayer.js/swfobject.js in page head:</th>
                  <td>
                    <label for="head_js_yes">Yes</label>
                    <input id="head_js_yes" type="radio" value="1" name="head_js" onclick="form.submit();" <?php checked(true, get_option(LONGTAIL_KEY . "use_head_js")); ?> />
                    <label for="head_js_no">No</label>
                    <input id="head_js_no" type="radio" value="0" name="head_js" onclick="form.submit();" <?php checked(0, get_option(LONGTAIL_KEY . "use_head_js")); ?> />
                    <span class="description">Controls whether the plugin will insert the jwplayer.js or swfobject.js files into the head of every page.  If set to No jwplayer.js or swfobject.js will only be included on pages where the jwplayer.js shortcode is used.</span>
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
            <h3 class="hndle"><span>Uninstall</span></h3>
            <div class="inside" style="margin: 15px;">
              <table>
                <tr valign="top">
                  <td>
                    <div>
                      <p><?php _e('To fully remove the plugin, click the Uninstall button.  Deactivating without uninstalling will not remove the data created by the plugin.') ;?></p>
                    </div>
                    <p><span style="color: red; "><strong><?php _e('WARNING:') ;?></strong><br />
                    <?php _e('This cannot be undone.  Since this is deleting data from your database, it is recommended that you create a backup.') ;?></span></p>
                    <div align="left">
                      <input type="submit" name="Uninstall" class="button-secondary delete" value="<?php _e('Uninstall plugin') ?>" onclick="return confirm('<?php _e('You are about to Uninstall this plugin from WordPress.\nThis action is not reversible.\n\nChoose [Cancel] to Stop, [OK] to Uninstall.\n'); ?>');"/>
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