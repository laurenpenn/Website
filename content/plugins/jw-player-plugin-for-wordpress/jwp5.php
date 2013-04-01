<?php

global $wp_version;

define("JW_PLAYER_GA_VARS", "?utm_source=WordPress&utm_medium=Product&utm_campaign=WordPress");
define("JW_FILE_PERMISSIONS", __('For tips on how to make sure this folder is writable please refer to <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a>.', 'jw-player-plugin-for-wordpress'));

// Check for WP2.7 installation
if (!defined ('IS_WP27')) {
  define('IS_WP27', version_compare($wp_version, '2.7', '>=') );
}

// This works only in WP2.7 or higher
if (IS_WP27 == FALSE) {
  add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' .
    __('Sorry, the JWPlayer Plugin for WordPress works only under WordPress 2.7 or higher.') . '</strong></p></div>\';', 'jw-player-plugin-for-wordpress'));
  return;
}

// The plugin is only compatible with PHP 5.0 or higher
if (version_compare(phpversion(), "5.0", '<')) {
  add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' .
    __('Sorry, the JWPlayer Plugin for WordPress only works with PHP Version 5 or higher.') . '</strong></p></div>\';', 'jw-player-plugin-for-wordpress'));
  return;
}

//Include core plugin files.
include_once (dirname (__FILE__) . "/framework/LongTailFramework.php");
include_once (dirname (__FILE__) . "/admin/AdminContext.php");
include_once (dirname (__FILE__) . "/media/JWMediaFunctions.php");
include_once (dirname (__FILE__) . "/media/JWShortcode.php");

register_deactivation_hook(JWP6_PLUGIN_FILE, "jwplayer_deactivation");
add_action('init', 'jwplayer_init');

//Define the plugin directory and url for file access.
$uploads = wp_upload_dir();
if (isset($uploads["error"]) && !empty($uploads["error"])) {
  add_action('admin_notices', 'jwplayer_uploads_error');
  return;
}
$use_ssl = get_option(LONGTAIL_KEY . "use_ssl");
$isHttps = is_ssl() && $use_ssl;
$pluginURL = $isHttps ? str_replace("http://", "https://", WP_PLUGIN_URL) : WP_PLUGIN_URL;
$uploadsURL = $isHttps ? str_replace("http://", "https://", $uploads["baseurl"]) : $uploads["baseurl"];
define("JWPLAYER_PLUGIN_DIR", WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)));
define("JWPLAYER_PLUGIN_URL", $pluginURL . "/" . plugin_basename(dirname(__FILE__)));
define("JWPLAYER_FILES_DIR", $uploads["basedir"] . "/" . plugin_basename(dirname(__FILE__)));
define("JWPLAYER_FILES_URL", $uploadsURL . "/" . plugin_basename(dirname(__FILE__)));

if (is_admin()) {
  add_action( 'plugins_loaded', create_function( '', 'global $adminContext; $adminContext = new AdminContext();' ) );
  add_action("admin_menu", "jwplayer_plugin_menu");
}

function jwplayer_deactivation() {
  delete_option(LONGTAIL_KEY . "uninstalled");
}

function jwplayer_init() {
  clearstatcache();
  load_plugin_textdomain("jw-player-plugin-for-wordpress", false, basename(dirname(__FILE__)));
  if (!@is_dir(JWPLAYER_FILES_DIR)) {
    if (!@mkdir(JWPLAYER_FILES_DIR, 0755, true)) {
      add_action('admin_notices', "jwplayer_directory_error");
      return;
    }
    chmod(JWPLAYER_FILES_DIR, 0755);
    if (!@mkdir(JWPLAYER_FILES_DIR . "/player", 0755)) {
      add_action('admin_notices', "jwplayer_player_error");
      return;
    }
    chmod(JWPLAYER_FILES_DIR . "/player", 0755);
    if (!@mkdir(JWPLAYER_FILES_DIR . "/configs", 0755)) {
      add_action('admin_notices', "jwplayer_configs_error");
      return;
    }
    chmod(JWPLAYER_FILES_DIR . "/configs", 0755);
    if (@is_dir(JWPLAYER_PLUGIN_DIR . "/configs")) {
      foreach (get_old_configs() as $config) {
        @rename(JWPLAYER_PLUGIN_DIR . "/configs/$config.xml", JWPLAYER_FILES_DIR . "/configs/$config.xml");
      }
    }
  }
  if (!@is_dir(JWPLAYER_FILES_DIR)) {
    add_action('admin_notices', "jwplayer_total_error");
  } else if (!file_exists(LongTailFramework::getPlayerPath())) {
    // Error if the player doesn't exist
    add_action('admin_notices', "jwplayer_install_notices");
  }
  if (file_exists(LongTailFramework::getEmbedderPath())) {
    if (get_option(LONGTAIL_KEY . "player_location_enable")) {
      wp_register_script("jw-embedder", get_option(LONGTAIL_KEY . "player_location") . "jwplayer.js", array(), false);
    } else {
      wp_register_script("jw-embedder", LongTailFramework::getEmbedderURL(), array(), false);
    }
    if (get_option(LONGTAIL_KEY . "use_head_js")) {
      wp_enqueue_script('jw-embedder');
    }
  } else if (get_option(LONGTAIL_KEY . "use_head_js")) {
    wp_enqueue_script('swfobject');
  }
  if (!get_option(LONGTAIL_KEY . "uninstalled")) jwplayer_upgrade();
  add_filter("the_content", "jwplayer_tag_callback", 11);
  add_filter("the_excerpt", "jwplayer_tag_excerpt_callback", 11);
  add_filter("widget_text", "jwplayer_tag_widget_callback", 11);
  // Parse the $_GET vars for callbacks
  add_filter('query_vars', 'jwplayer_queryvars' );
  add_action('parse_request',  'jwplayer_parse_request', 9 );
  add_action("wp_ajax_verify_player", "verify_player");
}

function jwplayer_upgrade() {
  $version = get_option(LONGTAIL_KEY . "plugin_version");
  if (!$version || version_compare($version, '1.5.1', '<')) {
    if (!get_option(LONGTAIL_KEY . "default")) update_option(LONGTAIL_KEY . "default", "Out-of-the-Box");
    if (!get_option(LONGTAIL_KEY . "player_location_enable")) update_option(LONGTAIL_KEY . "player_location_enable", 0);
    if (!get_option(LONGTAIL_KEY . "image_duration")) update_option(LONGTAIL_KEY . "image_duration", true);
    if (!get_option(LONGTAIL_KEY . "image_insert")) update_option(LONGTAIL_KEY . "image_insert", true);
    if (!get_option(LONGTAIL_KEY . "facebook")) update_option(LONGTAIL_KEY . "facebook", true);
    if (get_option(LONGTAIL_KEY . "show_archive")) {
      if (!get_option(LONGTAIL_KEY . "category_mode")) update_option(LONGTAIL_KEY . "category_mode", "excerpt");
      if (!get_option(LONGTAIL_KEY . "search_mode")) update_option(LONGTAIL_KEY . "search_mode", "excerpt");
      if (!get_option(LONGTAIL_KEY . "tag_mode")) update_option(LONGTAIL_KEY . "tag_mode", "excerpt");
    } else {
      if (!get_option(LONGTAIL_KEY . "category_mode")) update_option(LONGTAIL_KEY . "category_mode", "content");
      if (!get_option(LONGTAIL_KEY . "search_mode")) update_option(LONGTAIL_KEY . "search_mode", "content");
      if (!get_option(LONGTAIL_KEY . "tag_mode")) update_option(LONGTAIL_KEY . "tag_mode", "content");
    }
    if (!get_option(LONGTAIL_KEY . "home_mode")) update_option(LONGTAIL_KEY . "home_mode", "content");
    update_option(LONGTAIL_KEY . "plugin_version", "1.5.1");
  }
  if (!$version || version_compare($version, '1.5.3', '<')) {
    update_option(LONGTAIL_KEY . "use_ssl", true);
    update_option(LONGTAIL_KEY . "plugin_version", "1.5.3");
  }
  if (!$version || version_compare($version, '1.5.4', '<')) {
    update_option(LONGTAIL_KEY . "use_head_js", true);
    update_option(LONGTAIL_KEY . "plugin_version", "1.5.4");
  }
  if (!$version || version_compare($version, '1.5.6', '<')) {
    update_option(LONGTAIL_KEY . "player_mode", "flash");
    update_option(LONGTAIL_KEY . "plugin_version", "1.5.6");
  }
  if (!$version || version_compare($version, '1.7.0', '<')) {
    update_option(LONGTAIL_KEY . "allow_tracking", true);
    update_option(LONGTAIL_KEY . "plugin_version", "1.7.0");
  }
}

function jwplayer_uploads_error() {
  $message = __('There was a problem completing activation of the JW Player Plugin for WordPress.  Please note that the JWPlayer Plugin for ' .
    'WordPress requires that the WordPress uploads directory exists and is writable.  ', 'jw-player-plugin-for-wordpress') . JW_FILE_PERMISSIONS;
  echo "<div id='message' class='fade updated'><p><strong>$message</strong></p></div>";
}

function jwplayer_directory_error() {
  $message = __('There was a problem completing activation of the plugin.  The wp-content/uploads/jw-player-plugin-for-wordpress directory could not be created.  ' .
    'Please ensure the WordPress uploads directory is writable.  ', 'jw-player-plugin-for-wordpress') . JW_FILE_PERMISSIONS;
  echo "<div id='message' class='fade updated'><p><strong>$message</strong></p></div>";
}

function jwplayer_player_error() {
  $message = __('There was a problem completing activation of the plugin.  The wp-content/uploads/jw-player-plugin-for-wordpress/player directory could not be created.  ' .
    'Please ensure the WordPress uploads directory is writable.  ', 'jw-player-plugin-for-wordpress') . JW_FILE_PERMISSIONS;
  echo "<div id='message' class='fade updated'><p><strong>$message</strong></p></div>";
}

function jwplayer_configs_error() {
  $message = __('There was a problem completing activation of the plugin.  The wp-content/uploads/jw-player-plugin-for-wordpress/configs directory could not be created.  ' .
    'Please ensure the WordPress uploads directory is writable.  ', 'jw-player-plugin-for-wordpress') . JW_FILE_PERMISSIONS;
  echo "<div id='message' class='fade updated'><p><strong>$message</strong></p></div>";
}

function jwplayer_total_error() {
  $message = sprintf(__('Activation of the JW Player Plugin for WordPress could not complete successfully.  The following directories could not be created automatically: </p><ul><li>-
     %s</li><li>- %s/configs</li><li>- %s/player</li></ul><p>Please ensure these directories are writable.  ', 'jw-player-plugin-for-wordpress'), JWPLAYER_FILES_DIR, JWPLAYER_FILES_DIR, JWPLAYER_FILES_DIR) . JW_FILE_PERMISSIONS;
  echo "<div id='message' class='fade updated'><p><strong>$message</strong></p></div>";
}

function jwplayer_install_notices() {
  if (isset($_GET["page"]) && $_GET["page"] == "jwplayer-update") {
    return;
  } ?>
  <div id="message" class="fade updated">
    <form name="<?php echo LONGTAIL_KEY . "install"; ?>" method="post" action="<?php echo "admin.php?page=jwplayer-update"; ?>">
      <p>
        <strong><?php _e("To complete installation of the JW Player Plugin for WordPress, please click install.  ", 'jw-player-plugin-for-wordpress'); ?></strong>
        <input class="button-secondary" type="submit" name="Install" value="Install Latest JW Player" />
      </p>
    </form>
  </div>
<?php }

// Build the admin and menu.
function jwplayer_plugin_menu() {
  $admin = add_menu_page("JW Player Title", "JW Player", "administrator", "jwplayer", "jwplayer_plugin_pages", JWPLAYER_PLUGIN_URL . "/wordpress.png");
  // Please upgrade to JWP6!
  //add_submenu_page("jwplayer", "JW Player Plugin Licensing", __("Licensing", 'jw-player-plugin-for-wordpress'), "administrator", "jwplayer-license", "jwplayer_plugin_pages");
  add_submenu_page("jwplayer", "JW Player Plugin Update", __("Upgrade", 'jw-player-plugin-for-wordpress'), "administrator", "jwplayer-update", "jwplayer_plugin_pages");
  add_submenu_page("jwplayer", "JW Player Plugin Settings", __("Settings", 'jw-player-plugin-for-wordpress'), "administrator", "jwplayer-settings", "jwplayer_plugin_pages");
  $media = add_media_page("JW Player Plugin Playlists", __("Playlists", 'jw-player-plugin-for-wordpress'), "read", "jwplayer-playlists", "jwplayer_media_pages");
  add_action("admin_print_scripts-$admin", "add_admin_js");
  add_action("admin_print_scripts-$media", "add_admin_js");
}

// Add js for plugin tabs.
function add_admin_js() {
  wp_enqueue_script("jquery-ui-core");
  wp_enqueue_script("jquery-ui-tabs");
  wp_enqueue_script("jquery-ui-button");
  wp_enqueue_script("jquery-ui-widget");
  wp_enqueue_script("jquery-ui-mouse");
  wp_enqueue_script("jquery-ui-draggable");
  wp_enqueue_script("jquery-ui-droppable");
  wp_enqueue_script("jquery-ui-sortable");
  echo '<link rel="stylesheet" href="'. WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ).'/' .
    'css/smoothness/jquery.ui.jw.css" type="text/css" media="print, projection, screen" />'."\n";
}

// Entry point to the Player configuration wizard.
function jwplayer_plugin_pages() {
  switch ($_GET["page"]) {
    case "jwplayer" :
      global $adminContext;
      $adminContext->processState();
      break;
    case "jwplayer-license" :
      require_once (dirname(__FILE__) . "/admin/LicensePage.php");
      break;
    case "jwplayer-update" :
      require_once (dirname(__FILE__) . "/admin/UpdatePage.php");
      break;
    case "jwplayer-settings" :
      require_once (dirname(__FILE__) . "/admin/SettingsPage.php");
      break;
  }
}

function jwplayer_media_pages() {
  require_once(dirname(__FILE__) . "/media/JWPlaylistManager.php");
}

// Process xspf playlist requests.
function jwplayer_queryvars($query_vars) {
  $query_vars[] = 'xspf';
	return $query_vars;
}

// Parse xspf playlist requests.
function jwplayer_parse_request($wp) {
  if (array_key_exists('xspf', $wp->query_vars) && $wp->query_vars['xspf'] == 'true') {
  require_once (dirname (__FILE__) . '/media/JWPlaylistGenerator.php');
    exit();
  }
}

// Handles Ajax call with submitted player version
function verify_player() {
  $response = false;
  if ($_POST["version"] != "null") {
    $response = true;
    update_option(LONGTAIL_KEY . "version", $_POST["version"]);
  }
  echo (int) $response;
  exit;
}

// Grab existing configs for migration to new configs directory
function get_old_configs() {
  $results = array();
  $handler = @opendir(JWPLAYER_PLUGIN_DIR . "/configs");
  $results[] = "New Player";
  while ($file = @readdir($handler)) {
    if ($file != "." && $file != ".." && strstr($file, ".xml")) {
      $results[] = str_replace(".xml", "", $file);
    }
  }
  @closedir($handler);
  return $results;
}

function skin_unzip($archive) {
  if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    if ($zip->open($archive)) {
      $zip->extractTo(LongTailFramework::getSkinPath());
      $zip->close();
      return true;
    }
  } else {
    require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
    $zip = new PclZip($archive);
    $zip->extract(PCLZIP_OPT_PATH, LongTailFramework::getSkinPath());
    return true;
  }
  return false;
}

?>