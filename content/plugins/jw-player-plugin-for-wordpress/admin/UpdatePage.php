<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

// JWP6_MIGRATION
if ( isset($_POST['migrate_to_jwp6']) ) {
  JWP6_Migrate::migrate();
}

define("DOWNLOAD_ERROR", "Download failed.");
define("WRITE_ERROR", "Write failed");
define("READ_ERROR", "Read failed");
define("ZIP_ERROR", "Zip classes missing");
define("SUCCESS", "Success");

?>

<div class="wrap">

<?php

// JWP6_MIGRATION
if ( isset($_GET[JWP6 . 'hide_migration_notice']) ) {
  JWP6_Migrate::hide_migration_notice();
}

if (isset($_POST["Non_commercial"]) || isset($_POST["Install"])) {
  download_state();
} else if (isset($_POST["Commercial"])) {
  upload_state();
} else {
  default_state();
}

function unpack_player_archive($player_package) {

  if (!class_exists("ZipArchive")) {
    return zip_fallback($player_package);
  }

  $zip = new ZipArchive();
  if ($zip->open($player_package)) {
    $contents = "";
    $dir = $zip->getNameIndex(0);
    $fp = $zip->getStream($dir . "player.swf");
    if (!$fp) return READ_ERROR;
    while(!feof($fp)) {
      $contents .= fread($fp, 2);
    }
    fclose($fp);
    $result = @file_put_contents(LongTailFramework::getPrimaryPlayerPath(), $contents);
    if (!$result) {
      return WRITE_ERROR;
    }
    chmod(LongTailFramework::getPrimaryPlayerPath(), 0755);
    $contents = "";
    $fp = $zip->getStream($dir . "yt.swf");
    if ($fp) {
      while (!feof($fp)) {
        $contents .= fread($fp, 2);
      }
      fclose($fp);
      $result = @file_put_contents(str_replace("player.swf", "yt.swf", LongTailFramework::getPrimaryPlayerPath()), $contents);
      if (!$result) {
        return WRITE_ERROR;
      }
      chmod(str_replace("player.swf", "yt.swf", LongTailFramework::getPrimaryPlayerPath()), 0755);
    }
    $fp = $zip->getStream($dir . "jwplayer.js");
    if ($fp) {
      $contents = "";
      while (!feof($fp)) {
        $contents .= fread($fp, 2);
      }
      fclose($fp);
      $result = @file_put_contents(LongTailFramework::getEmbedderPath(), $contents);
      if (!$result) {
        return WRITE_ERROR;
      }
      chmod(LongTailFramework::getEmbedderPath(), 0755);
    }
    $zip->close();
  }

  unlink($player_package);
  return SUCCESS;
}

function zip_fallback($player_package) {
  $player_found = false;
  require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
  $zip = new PclZip($player_package);
  $archive_files = $zip->extract(PCLZIP_OPT_EXTRACT_AS_STRING);
  $dir = $archive_files[0]["filename"];
  foreach($archive_files as $file) {
    $result = true;
    if ($file["filename"] == $dir . "player.swf" || $file["filename"] == $dir . "player-licensed.swf") {
      $result = @file_put_contents(LongTailFramework::getPrimaryPlayerPath(), $file["content"]);
      if (!$result) {
        return WRITE_ERROR;
      }
      $player_found = true;
    } else if ($file["filename"] == $dir . "yt.swf") {
      $result = @file_put_contents(str_replace("player.swf", "yt.swf", LongTailFramework::getPrimaryPlayerPath()), $file["content"]);
      if (!$result) {
        return WRITE_ERROR;
      }
    } else if ($file["filename"] == $dir . "jwplayer.js") {
      $result = @file_put_contents(LongTailFramework::getEmbedderPath(), $file["content"]);
      if (!$result) {
         return WRITE_ERROR;
      }
    }
  }
  if ($player_found) {
    unlink($player_package);
    return SUCCESS;
  }
  return ZIP_ERROR;
}

function player_download() {  
  $player_package = download_url("http://www.longtailvideo.com/wp/jwplayer.zip");
  if (is_wp_error($player_package)) {
    return DOWNLOAD_ERROR;
  }
  return unpack_player_archive($player_package);
}

function player_upload() {
  return unpack_player_archive($_FILES["file"]["tmp_name"]);
}

function default_state() { ?>
  <h2><?php _e("JW Player Upgrade", 'jw-player-plugin-for-wordpress'); ?></h2>
  <p/>
  <div id="poststuff">
  <?php
  // JWP6_MIGRATION
  JWP6_Migrate::migrate_section();
  // Please upgrade to JWP6!
  //upload_section();
  //download_section();
  ?>
  </div>
  <?php
}

function download_state() { ?>
  <h2><?php _e("JW Player Install", 'jw-player-plugin-for-wordpress'); ?></h2>
  <p/>
  <?php
  $result = player_download();
  if ($result == SUCCESS) { ?>
  <div id="info" class="fade updated">
    <p><strong><span id="player_version"><?php _e("Successfully downloaded and installed the latest player version, JW Player ", 'jw-player-plugin-for-wordpress'); ?></span></strong></p>
    <p><?php _e("If you have a specific version of the JW Player you wish to install (eg. licensed version), then you can install it using the <a href='admin.php?page=jwplayer-update'>upgrade page</a>.", 'jw-player-plugin-for-wordpress'); ?></p>
  </div>
  <form name="<?php echo LONGTAIL_KEY . "form"; ?>" method="post" action="">
    <table class="form-table">
      <tr>
        <td colspan="2">
          <?php embed_demo_player(true); ?>
        </td>
      </tr>
    </table>
  </form>
  <?php } else if ($result == DOWNLOAD_ERROR) {
    error_message(sprintf(__("Not able to download JW Player.  Please check your internet connection. <br/>
    If you already have the JW Player then you can install it using the <a href='admin.php?page=jwplayer-update'>upgrade page</a>.<br/>
    Alternatively you may FTP the player files directly to your site.  Place the player.swf and jwplayer.js files in %s/player/. <br/> ", 'jw-player-plugin-for-wordpress'), JWPLAYER_FILES_DIR) . JW_FILE_PERMISSIONS);
  } else if ($result == WRITE_ERROR) {
    error_message(sprintf(__("Not able to install JW Player.
    Please make sure the %s/player/ directory exists (and is writabe) and then visit the <a href='admin.php?page=jwplayer-update'>upgrade page</a>.<br/>
    Alternatively you may FTP the player.swf and jwplayer.js files directly to your site. <br/>", 'jw-player-plugin-for-wordpress'), JWPLAYER_FILES_DIR) . JW_FILE_PERMISSIONS);
  } else if ($result == ZIP_ERROR) {
    error_message(sprintf(__("The necessary zip classes are missing.  Please FTP the player manually.  <br/>Place the player.swf and jwplayer.js files in %s/player/.", 'jw-player-plugin-for-wordpress'), JWPLAYER_FILES_DIR));
  } else if ($result == READ_ERROR) {
    error_message(sprintf(__("Could not find player.swf or jwplayer.js.  Either they are not present or the archive is invalid.<br/>
    Alternatively you may FTP the player files directly to your site.  Place the player.swf and jwplayer.js files in %s/player/.", 'jw-player-plugin-for-wordpress'), JWPLAYER_FILES_DIR));
  }
}

function upload_state() { ?>
  <h2><?php _e("JW Player Install", 'jw-player-plugin-for-wordpress'); ?></h2>
  <p/>
  <?php $result = player_upload() ?>
  <?php if ($result == SUCCESS) { ?>
  <div id="info" class="fade updated" style="display: none;">
    <p><strong><span id="player_version"><?php _e("Successfully installed your player, JW Player ", 'jw-player-plugin-for-wordpress'); ?></span></strong></p>
  </div>
  <div id="error" class="error fade" style="display: none;">
    <p><strong><?php _e("JW Player was not detected.", 'jw-player-plugin-for-wordpress'); ?></strong></p>
  </div>
  <form name="<?php echo LONGTAIL_KEY . "form"; ?>" method="post" action="">
    <table class="form-table">
      <tr>
        <td colspan="2">
          <?php embed_demo_player(); ?>
        </td>
      </tr>
    </table>
  </form>
  <?php } else if ($result == WRITE_ERROR) {
    error_message(sprintf(__("Not able to install JW Player.  Please make sure the %s directory exists (and is writabe) and then visit the <a href='admin.php?page=jwplayer-update'>upgrade page</a>.  ", 'jw-player-plugin-for-wordpress'), LongTailFramework::getPlayerPath()) . JW_FILE_PERMISSIONS);
    default_state();
  } else if ($result == ZIP_ERROR) {
    error_message(__("The necessary zip classes are missing.  Please upload the player manually instead using the <a href='admin.php?page=jwplayer-update'>upgrade page</a>.", 'jw-player-plugin-for-wordpress'));
    default_state();
  } else if ($result == READ_ERROR) {
    error_message(__("Could not find player.swf or yt.swf.  Either they are not present or the archive is invalid.", 'jw-player-plugin-for-wordpress'));
    default_state();
  } else {
    error_message(__("Not a valid zip archive.", 'jw-player-plugin-for-wordpress'));
    default_state();
  }
}

function error_message($message) { ?>
  <div id="error" class="error fade">
    <p><strong><?php echo $message; ?></strong></p>
  </div> <?php
}

function embed_demo_player($download = false) {
  $atts = array(
    "file" => "http://content.longtailvideo.com/videos/bunny.flv",
    "image" => "http://content.longtailvideo.com/videos/bunny.jpg",
    "id" => "jwplayer-1"
  );
  wp_print_scripts('swfobject');
  $swf = LongTailFramework::generateSWFObject($atts, false); ?>
  <script type="text/javascript">
    var player, t;

    jQuery(document).ready(function() {
      t = setTimeout(playerNotReady, 2000);
    });

    function playerNotReady() {
      var data = {
        action: "verify_player",
        version: null,
        type: <?php echo (int) $download; ?>
      };
      document.getElementById("version").value = null;
      document.getElementById("type").value = <?php echo (int) $download; ?>;
      jQuery.post(ajaxurl, data, function(response) {
        var download = <?php echo (int) $download; ?>;
        if (!download) {
          document.getElementById("error").style.display = "block";
        }
      });
    }

    function playerReady(object) {
      player = document.getElementById(object.id);
      var data = {
        action: "verify_player",
        version: player.getConfig().version,
        type: <?php echo (int) $download; ?>
      };
      clearTimeout(t);
      document.getElementById("version").value = player.getConfig().version;
      document.getElementById("type").value = <?php echo (int) $download; ?>;
      jQuery.post(ajaxurl, data, function(response) {
        var download = <?php echo (int) $download; ?>;
        if (!download) {
          document.getElementById("error").style.display = "none";
          document.getElementById("info").style.display = "block";
        }
        document.getElementById("player_version").innerHTML = document.getElementById("player_version").innerHTML + player.getConfig().version;
      });
    }
  </script>
  <?php echo $swf->generateEmbedScript(); ?>
  <input id="type" class="hidden" type="text" name="Type" />
  <input id="version" class="hidden" type="text" name="Version" />
<?php }

function upload_section() { ?>
  <form name="<?php echo LONGTAIL_KEY . "form"; ?>" method="post" action="" enctype="multipart/form-data" onsubmit="return fileValidation();">
    <div id="post-body">
      <div id="post-body-content">
        <div class="stuffbox">
          <h3 class="hndle"><span><?php echo "Manually Upgrade"; ?></span></h3>
          <div class="inside" style="margin: 10px;">
            <script type="text/javascript">
              function fileValidation() {
                var file = document.getElementById("file").value;
                var extension = file.substring(file.length - 4, file.length);
                if (extension === ".zip") {
                  return true;
                } else {
                  alert("File must be a Zip.");
                  return false;
                }
              }
            </script>
            <table class="form-table">
              <tr>
                <td colspan="2">
                  <p>
                    <span><?php printf(__("Upload your own zip package. Use this to upgrade to the licensed version or to install a specific version of the player.  To obtain a licensed player, please purchase a license from <a href=\"https://www.longtailvideo.com/order/%s\" target=_blank>LongTail Video</a>.", 'jw-player-plugin-for-wordpress'), JW_PLAYER_GA_VARS); ?></span>
                  </p>
                  <p>
                    <label for="file"><?php _("Install JW Player:"); ?></label>
                    <input id="file" type="file" name="file" />
                    <input class="button-secondary" type="submit" name="Commercial" value="<?php _e("Upload", 'jw-player-plugin-for-wordpress'); ?>" />
                  </p>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </form>
<?php }

function download_section() { ?>
  <form name="<?php echo LONGTAIL_KEY . "form"; ?>" method="post" action="">
    <div id="post-body">
      <div id="post-body-content">
        <div class="stuffbox">
          <h3 class="hndle"><span><?php _e("Automatically Upgrade", 'jw-player-plugin-for-wordpress'); ?></span></h3>
          <div class="inside" style="margin: 10px;">
            <table class="form-table">
              <tr>
                <td colspan="2">
                  <p>
                    <span><?php _e("Automatically download the latest Non-commercial version of the JW Player to your web server.", 'jw-player-plugin-for-wordpress'); ?></span>
                  </p>
                  <p>
                    <input class="button-secondary" type="submit" name="Non_commercial" value="<?php _e("Install Latest JW Player", 'jw-player-plugin-for-wordpress'); ?>" />
                  </p>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </form>
<?php } ?>

</div>
