<?php
/*
Plugin Name: The Piecemaker
Plugin URI: http://www.vareen.co.cc/documentation/the-piecemaker-for-wordpress-%E2%80%93-documentation/
Description: Plugin to display piecemaker 3d image gallery to your wordpress site.
Author: Neerav D.
Version: 1.1
Author URI: http://www.vareen.co.cc

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


// Define certain terms which may be required throughout the plugin
define('TP_PATH', WP_PLUGIN_DIR . '/the-piecemaker');
define('TP_URL', WP_PLUGIN_URL . '/the-piecemaker');
define('TP_BASENAME',plugin_basename(__FILE__));

// Add Menu in Administration area for configuration page
add_action('admin_menu', 'the_piecemaker_admin_actions');

// Admin actions
function the_piecemaker_admin_actions() {
  // Add options page
  $tp_option_page = add_options_page('The Piecemaker Options', 'The Piecemaker', 'administrator', TP_BASENAME, 'the_piecemaker_options');

  // Register settings for plugin
  add_action('admin_init', 'the_piecemaker_options_init');
  // Add color picker script
  add_action("admin_print_scripts-$tp_option_page", 'the_piecemaker_admin_scripts');
}

// Include file containing options page form
function the_piecemaker_options() {
  include('the_picemaker_options.php');
}

// Register settings for plugin
function the_piecemaker_options_init() {
  register_setting('the_piecemaker_options', 'the_piecemaker');
}

// Add script requried in admin options page
function the_piecemaker_admin_scripts() {
  wp_enqueue_script('farbtastic', TP_URL . '/asset/farbtastic/farbtastic.js', 'jquery');
  echo '<link rel="stylesheet" href="' . TP_URL . '/asset/farbtastic/farbtastic.css" type="text/css" />' . "\n";
  echo '<link rel="stylesheet" href="' . TP_URL . '/asset/css/admin.css" type="text/css" />' . "\n";
}

// Add settings link on plugin list page
function the_piecemaker_settings_link($links) {
  $settings_link = '<a href="options-general.php?page='.TP_BASENAME.'">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}
add_filter("plugin_action_links_".TP_BASENAME, 'the_piecemaker_settings_link' );

// Register sidebar widget for easy implementation
wp_register_sidebar_widget('the_piecemaker', 'The Piecemaker', 'display_the_piecemaker');

/**
 * Display piecemaker image gallery as per configured
 * in The Piecemaker options page.
 * @param array $args
 * @return void
 */
function display_the_piecemaker($args = array()) {
  extract($args);
  echo $before_widget;
  echo $before_title;
  echo $widget_name;
  echo $after_title;
  $options = get_option('the_piecemaker');
  extract($options);
  $innerColor = str_replace('#', '0x', $innerColor);
  $textBackground = str_replace('#', '0x', $textBackground);
  $swf = ($shadow) ? 'piecemakerShadow.swf' : 'piecemakerNoShadow.swf';
  if ($cache) {
    if (-1 != $category) {
      $tp_posts = get_posts(array('category' => $category));

      foreach ($tp_posts as $tp_post) {
        $img = get_post_custom_values('the_piecemaker_image', $tp_post->ID);
        $images[] = $img[0];
        $desc = '<headline>' . $tp_post->post_title . '</headline><break>&nbsp;</break>';
        $desc .= '<paragraph>' . $tp_post->post_excerpt . '</paragraph><break>&nbsp;</break>';
        $link = get_permalink($tp_post->ID);
        $desc .= '<a href="' . $link . '">'.$readMore.'</a>';

        $descriptions[] = $desc;
      }
    }
    else {
      $images = explode(chr(10), $image_url);
      $descriptions = explode(chr(10), $image_description);
    }
    $count = count($images);
    if ($count) {
      $xml = TP_PATH . '/asset/xml/piecemakerXML.xml';
      if (!is_writable($xml)) {
        echo '<p>Please make sure "' . $xml . '" file is writable.</p>';
      }
      else {
        $playlist = <<<EOP
<?xml version="1.0" encoding="utf-8"?>
<Piecemaker>
  <Settings>
    <imageWidth>$width</imageWidth>
    <imageHeight>$height</imageHeight>
    <segments>$segments</segments>
    <tweenTime>$tweenTime</tweenTime>
    <tweenDelay>$tweenDelay</tweenDelay>
    <tweenType>$tweenType</tweenType>
    <zDistance>$zDistance</zDistance>
    <expand>$expand</expand>
    <innerColor>$innerColor</innerColor>
    <textBackground>$textBackground</textBackground>
    <shadowDarkness>$shadowDarkness</shadowDarkness>
    <textDistance>$textDistance</textDistance>
    <autoplay>$autoPlay</autoplay>
  </Settings>

EOP;
        for ($i = 0; $i < $count; $i++)
        {
          $image = rtrim($images[$i]);
          $description = rtrim($descriptions[$i]);
          $playlist .= <<<EOQ
  <Image Filename="$image">
    <Text>$description</Text>
  </Image>

EOQ;
        }
        $playlist .= '</Piecemaker>';
      }
      $fp = fopen($xml, 'w') or die('Cant create file pointer');
      fwrite($fp, $playlist) or die('Cant write to file');
      fclose($fp);
    }
  }
  ob_start();?>
  var flashvars = {};
  var cacheBuster = "?t=" + Date.parse(new Date());
  flashvars.xmlSource = "<?php echo TP_URL . '/asset/xml/piecemakerXML.xml'; ?>"+cacheBuster;
  flashvars.cssSource = "<?php echo TP_URL; ?>/asset/css/piecemakerCSS.css";
  flashvars.imageSource = "<?php echo bloginfo('home'); ?>";
  var attributes = {};
  attributes.wmode = "transparent";
  swfobject.embedSWF("<?php echo TP_URL . '/asset/swf/' . $swf; ?>", "the_piecemaker_slideshow", "<?php echo $width + 150; ?>", "<?php echo $height + 150; ?>", "10", "<?php echo TP_URL; ?>/asset/js/expressInstall.swf", flashvars, attributes);
  <?php
  $script = '
  <script type="text/javascript" src="' . TP_URL . '/asset/js/swfobject.js"></script>
  <script type="text/javascript">' . ob_get_clean() . '</script>'; ?>
  <table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <td align="center">
      <!-- this div will be overwritten by SWF object -->
      <div id="the_piecemaker_slideshow">
        <p>In order to view this object you need Flash Player 9+ support!</p>
        <a href="http://www.adobe.com/go/getflashplayer">
          <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif"
               alt="Get Adobe Flash player"/>
        </a>
      </div>
    </td>
  </table>
  <?php
  echo $after_widget;
  echo $script;
}