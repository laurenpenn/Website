<?php
/**
 * @file This file defines the filter hooks for extending the WordPress Media
 * Library.
 */

global $wp_version;

add_action("wp_head", "jwplayer_wp_head");

function jwplayer_wp_head() {
  global $post;

  if (!(is_single() || is_page()) || !get_option(LONGTAIL_KEY . "facebook")) return;

  $config_values = array();
  $attachment = null;
  $settings = array();
  $meta_header_id = get_post_meta($post->ID, LONGTAIL_KEY . "fb_headers_id", true);
  $meta_header_config = get_post_meta($post->ID, LONGTAIL_KEY . "fb_headers_config", true);
  if (empty($meta_header_id)) {
    return;
  } else if (is_numeric($meta_header_id)) {
    $attachment = get_post($meta_header_id);
    $title = $attachment->post_title;
    $description = $attachment->post_content;
    $thumbnail = get_post_meta($meta_header_id, LONGTAIL_KEY . "thumbnail_url", true);
    if (!isset($thumbnail) || $thumbnail == null || $thumbnail == "") {
      $image_id = get_post_meta($meta_header_id, LONGTAIL_KEY . "thumbnail", true);
      if (isset($image_id)) {
        $image_attachment = get_post($image_id);
        $thumbnail = !empty($image_attachment) ? $image_attachment->guid : "";
      }
    }
    $settings[] = "file=" . $attachment->guid;
  } else {
    $title = $post->post_title;
    $description = $post->post_excerpt;
    $thumbnail = "";
    $settings[] = "file=$meta_header_id";
  }
  if (!empty($meta_header_config) && $meta_header_config != "") {
    LongTailFramework::setConfig($meta_header_config);
  } else {
    LongTailFramework::setConfig(get_option(LONGTAIL_KEY . "default"));
  }
  $config_values = LongTailFramework::getConfigValues();
  $width = isset($config_values["width"]) ? $config_values["width"] : "";
  $height = isset($config_values["height"]) ? $config_values["height"] : "";
  foreach ($config_values as $key => $value) {
    $settings[] = "$key=$value";
  }
  $settings_string = htmlspecialchars(implode("&", $settings));
  $facebook_url = LongTailFramework::getPlayerURL();
  if ($settings_string) $facebook_url .= "?$settings_string";
  $output = "";
  $output .= "<meta property='og:type' content='movie' />";
  $output .= "<meta property='og:video:width' content='$width' />";
  $output .= "<meta property='og:video:height' content='$height' />";
  $output .= "<meta property='og:video:type' content='application/x-shockwave-flash' />";
  $output .= "<meta property='og:title' content='" . htmlspecialchars($title) . "' />";
  $output .= "<meta property='og:description' content='" . htmlspecialchars($description) . "' />";
  $output .= "<meta property='og:image' content='$thumbnail' />";
  $output .= "<meta property='og:video' content='$facebook_url' />";
  echo $output;
}

// Filter hook for specifying which custom fields are save.
add_filter("attachment_fields_to_save", "jwplayer_attachment_fields_to_save", 10, 2);

/**
 * Handler function for saving custom fields.
 * @param array $post Array representing the post we are saving.
 * @param array $attachment Array representing the attachment fields being
 * saved.
 * @return array $post updated with the attachment fields to be saved.
 */
function jwplayer_attachment_fields_to_save($post, $attachment) {
  $mime_type = substr($post["post_mime_type"], 0, 5);
  $rtmp = get_post_meta($post["ID"], LONGTAIL_KEY . "rtmp");
  if ($mime_type == "video" && isset($rtmp)) {
    update_post_meta($post["ID"], LONGTAIL_KEY . "streamer", isset($attachment[LONGTAIL_KEY . "streamer"]) ? $attachment[LONGTAIL_KEY . "streamer"] : "");
    update_post_meta($post["ID"], LONGTAIL_KEY . "file", isset($attachment[LONGTAIL_KEY . "file"]) ? $attachment[LONGTAIL_KEY . "file"] : "");
    update_post_meta($post["ID"], LONGTAIL_KEY . "provider", isset($attachment[LONGTAIL_KEY . "provider"]) ? $attachment[LONGTAIL_KEY . "provider"] : "");
  }
  if ($mime_type == "video" || $mime_type == "audio") {
    update_post_meta($post["ID"], LONGTAIL_KEY . "thumbnail", $attachment[LONGTAIL_KEY . "thumbnail"]);
    update_post_meta($post["ID"], LONGTAIL_KEY . "thumbnail_url", $attachment[LONGTAIL_KEY . "thumbnail_url"]);
    update_post_meta($post["ID"], LONGTAIL_KEY . "creator", $attachment[LONGTAIL_KEY . "creator"]);
    update_post_meta($post["ID"], LONGTAIL_KEY . "duration", $attachment[LONGTAIL_KEY . "duration"]);
    update_post_meta($post["ID"], LONGTAIL_KEY . "html5_file", $attachment[LONGTAIL_KEY . "html5_file"]);
    update_post_meta($post["ID"], LONGTAIL_KEY . "html5_file_selector", $attachment[LONGTAIL_KEY . "html5_file_selector"]);
    update_post_meta($post["ID"], LONGTAIL_KEY . "download_file", $attachment[LONGTAIL_KEY . "download_file"]);
    update_post_meta($post["ID"], LONGTAIL_KEY . "download_file_selector", $attachment[LONGTAIL_KEY . "download_file_selector"]);
  }
  if ($mime_type == "image") {
    update_post_meta($post["ID"], LONGTAIL_KEY . "duration", $attachment[LONGTAIL_KEY . "duration"]);
  }
  return $post;
}

// Filter hook for specifying additional fields to appear when editing
// attachments.
add_filter("attachment_fields_to_edit", "jwplayer_attachment_fields", 10, 2);

/**
 * Handler function for displaying custom fields.
 * @param array $form_fields The fields to appear on the attachment form.
 * @param array $post Object representing the post we are saving to.
 * @return array Updated $form_fields with the new fields.
 */
function jwplayer_attachment_fields($form_fields, $post) {
  global $wp_version;
  $image_args = array(
    "post_type" => "attachment",
    "numberposts" => 50,
    "post_status" => null,
    "post_mime_type" => "image",
    "post_parent" => null
  );
  $image_attachments = get_posts($image_args);
  $video_args = array(
    "post_type" => "attachment",
    "numberposts" => 25,
    "post_status" => null,
    "post_mime_type" => "video",
    "post_parent" => null
  );
  $video_attachments = get_posts($video_args);
  $mime_type = substr($post->post_mime_type, 0, 5);
  switch($mime_type) {
    case "image":
      if (get_option(LONGTAIL_KEY . "image_duration")) {
        $form_fields[LONGTAIL_KEY . "duration"] = array(
          "label" => __("Duration", 'jw-player-plugin-for-wordpress'),
          "input" => "text",
          "value" => get_post_meta($post->ID, LONGTAIL_KEY . "duration", true)
        );
      }
      break;
    case "audio":
    case "video":
      $form_fields[LONGTAIL_KEY . 'thumbnail_url'] = array(
        "label" => __("Thumb URL", 'jw-player-plugin-for-wordpress'),
        "input" => "text",
        "value" => get_post_meta($post->ID, LONGTAIL_KEY . "thumbnail_url", true)
      );
      $form_fields[LONGTAIL_KEY . "thumbnail"] = array(
        "label" => __("Thumb", 'jw-player-plugin-for-wordpress'),
        "input" => "html",
        "html" => generateImageSelectorHTML($post->ID, $image_attachments)
      );
      $form_fields[LONGTAIL_KEY . "creator"] = array(
        "label" => __("Creator", 'jw-player-plugin-for-wordpress'),
        "input" => "text",
        "value" => get_post_meta($post->ID, LONGTAIL_KEY . "creator", true)
      );
      $form_fields[LONGTAIL_KEY . "duration"] = array(
        "label" => __("Duration", 'jw-player-plugin-for-wordpress'),
        "input" => "text",
        "value" => get_post_meta($post->ID, LONGTAIL_KEY . "duration", true)
      );
      $form_fields[LONGTAIL_KEY . 'html5_file'] = array(
        "label" => __("HTML5 file", 'jw-player-plugin-for-wordpress'),
        "input" => "text",
        "value" => get_post_meta($post->ID, LONGTAIL_KEY . "html5_file", true)
      );
      $form_fields[LONGTAIL_KEY . 'html5_file_selector'] = array(
        "label" => __("", 'jw-player-plugin-for-wordpress'),
        "input" => "html",
        "html" => generateVideoSelectorHTML($post->ID, "html5_file_selector", $video_attachments)
      );
      $form_fields[LONGTAIL_KEY . 'download_file'] = array(
        "label" => __("Download file", 'jw-player-plugin-for-wordpress'),
        "input" => "text",
        "value" => get_post_meta($post->ID, LONGTAIL_KEY . "download_file", true)
      );
      $form_fields[LONGTAIL_KEY . 'download_file_selector'] = array(
        "label" => __("", 'jw-player-plugin-for-wordpress'),
        "input" => "html",
        "html" => generateVideoSelectorHTML($post->ID, "download_file_selector", $video_attachments)
      );
      break;
  }
  $rtmp = get_post_meta($post->ID, LONGTAIL_KEY . "rtmp");
  if ($mime_type == "video" && isset($rtmp) && $rtmp) {
    unset($form_fields["url"]);
    $form_fields[LONGTAIL_KEY . "streamer"] = array(
        "label" => __("Streamer", 'jw-player-plugin-for-wordpress'),
        "input" => "text",
        "value" => get_post_meta($post->ID, LONGTAIL_KEY . "streamer", true)
    );
    $form_fields[LONGTAIL_KEY . "file"] = array(
        "label" => __("File", 'jw-player-plugin-for-wordpress'),
        "input" => "text",
        "value" => get_post_meta($post->ID, LONGTAIL_KEY . "file", true)
    );
    $form_fields[LONGTAIL_KEY . "provider"] = array(
        "label" => __("Provider", 'jw-player-plugin-for-wordpress'),
        "input" => "text",
        "value" => get_post_meta($post->ID, LONGTAIL_KEY . "provider", true)
    );
  }
  if (isset($_GET["post_id"]) && ($mime_type == "video" || $mime_type == "audio" || ($mime_type == "image" && get_option(LONGTAIL_KEY . "image_duration")))) {
    $insert = "<input type='submit' class='button-primary' name='send[$post->ID]' value='" . esc_attr__('Insert JW Player', 'jw-player-plugin-for-wordpress') . "' />";
    $form_fields[LONGTAIL_KEY . "player_select"] = array(
      "label" => __("Select Player", 'jw-player-plugin-for-wordpress'),
      "input" => "html",
      "html" => generatePlayerSelectorHTML($post->ID)
    );
    $form_fields["jwplayer"] = array("tr" => "\t\t<tr class='submit'><td></td><td class='savesend'>$insert</td></tr>\n");
  } else if (version_compare($wp_version, '3.5', '>=')) {
    if (($mime_type == "video" || $mime_type == "audio" || ($mime_type == "image" && get_option(LONGTAIL_KEY . "image_duration")))) {
      $insertJS = " <script type='text/javascript'>
                    function jwplayer_insert(id) {
                      var selected_player = jQuery('#jwplayermodule_player_select_" . $post->ID . "').val();
                      wp.media.post('send-attachment-to-editor', {
                        nonce: wp.media.view.settings.nonce.sendToEditor,
                        attachment: {
                          'id': " . $post->ID . ",
                          '" . LONGTAIL_KEY . "insert_type': 'jwplayer',
                          '" . LONGTAIL_KEY . "player_select': selected_player
                        },
                        html:       '',
                        post_id: wp.media.view.settings.post.id
                      }).done(function(response) {
                        send_to_editor(response);
                      });
				            }
                  </script>";
      $insert = "<a onclick='jwplayer_insert($post->ID);' id='jwplayermodule_insert_$post->ID' class='button-primary' name='send[$post->ID]'>Insert JW Player</a>";
      $form_fields[LONGTAIL_KEY . "player_select"] = array(
        "label" => __("Select Player", 'jw-player-plugin-for-wordpress'),
        "input" => "html",
        "html" => generatePlayerSelectorHTML($post->ID)
      );
      $form_fields["jwplayer"] = array("tr" => "\t\t<tr class='submit'><th valign='top' scope='row' class='label'><label><span></span></label></th><td class='savesend'>$insert</td></tr>\n$insertJS");
    }
  }
  return $form_fields;
}

/**
 * Generates the HTML for rendering the thumbnail image selector.
 * @param int $id The id of the current attachment.
 * @param $attachments
 * @return string The HTML to render the image selector.
 */
function generateImageSelectorHTML($id, $attachments) {
  global $wp_version;
  $output = "";
  $sel = false;
  if ($attachments) {
    $output .= "<link rel='stylesheet' type='text/css' href='" . WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . "/msdropdown/dd.css' />\n";
    if (version_compare($wp_version, '3.5', '>=')) {
      $output .= "<select onchange='jQuery(\"#" . LONGTAIL_KEY . "player_select_$id\").change()' name='attachments[$id][" . LONGTAIL_KEY . "thumbnail]' id='imageselector$id' width='175' style='width:100%;'>\n";
    } else {
      $output .= "<select name='attachments[$id][" . LONGTAIL_KEY . "thumbnail]' id='imageselector$id' width='175' style='width:100%;'>\n";
    }
    $output .= "<option value='-1' title='" . JWPLAYER_PLUGIN_URL . "/video_noimage.png'>None</option>\n";
    $image_id = get_post_meta($id, LONGTAIL_KEY . "thumbnail", true);
    $thumbnail_url = get_post_meta($id, LONGTAIL_KEY . "thumbnail_url", true);
    foreach($attachments as $post) {
      if (substr($post->post_mime_type, 0, 5) == "image") {
        if ($post->ID == $image_id) {
          $selected = "selected='selected'";
          $sel = true;
        } else {
          $selected = "";
        }
        $output .= "<option value='" . $post->ID . "' title='" . $post->guid . "' " . $selected . ">" . $post->post_title . "</option>\n";
      }
    }
    if (!$sel && isset($image_post) && isset($image_id) && $image_id != -1 && isset($thumbnail_url) && !$thumbnail_url) {
      $image_post = get_post($image_id);
      $output .= "<option value='" . $image_post->ID . "' title='" . $image_post->guid . "' selected=selected >" . $image_post->post_title . "</option>\n";
    }
    $output .= "</select>\n";
    $output .= " <script type='text/javascript'>
                  jQuery.getScript('" . WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . "/msdropdown/js/uncompressed.jquery.dd.js', function(data, textStatus, jqxhr) {
                    jQuery(\"#imageselector$id\").msDropDown({
                      visibleRows:3,
                      rowHeight:50
                    });
                  });
                </script>";
  }
  return $output;
}

function generateVideoSelectorHTML($id, $field, $attachments) {
  global $wp_version;
  $output = "";
  $sel = false;
  if ($attachments) {
    if (version_compare($wp_version, '3.5', '>=')) {
      $output .= "<select onchange='jQuery(\"#" . LONGTAIL_KEY . "player_select_$id\").change()' name='attachments[$id][" . LONGTAIL_KEY . "$field]' id='" . $field . "_selector$id' width='175' style='width:100%;'>\n";
    } else {
      $output .= "<select name='attachments[$id][" . LONGTAIL_KEY . "$field]' id='" . $field . "_selector$id' width='175' style='width:100%;'>\n";
    }
    $output .= "<option value='-1' title='" . JWPLAYER_PLUGIN_URL . "/video_noimage.png'>None</option>\n";
    $video_id = get_post_meta($id, LONGTAIL_KEY . $field, true);
    foreach($attachments as $post) {
      if (substr($post->post_mime_type, 0, 5) == "video") {
        if ($post->ID == $video_id) {
          $selected = "selected='selected'";
          $sel = true;
        } else {
          $selected = "";
        }
        $thumbnail = get_post_meta($post->ID, LONGTAIL_KEY . "thumbnail_url", true);
        if (!isset($thumbnail) || $thumbnail == null || $thumbnail == "") {
          $thumbnail_id = get_post_meta($post->ID, LONGTAIL_KEY . "thumbnail", true);
          if (isset($thumbnail_id)) {
            $image_attachment = get_post($thumbnail_id);
            $thumbnail = isset($image_attachment) ? $image_attachment->guid : JWPLAYER_PLUGIN_URL . "/video_noimage.png";
          }
        }
        $title = $post->post_title ? $post->post_title : $post->guid;
        $output .= "<option value='" . $post->ID . "' title='" . $thumbnail . "' " . $selected . ">" . $title . "</option>\n";
      }
    }
    if (!$sel && $video_id != -1 && !empty($video_id)) {
      $video_post = get_post($video_id);
      $output .= "<option value='" . $video_post->ID . "' title='" . $video_post->guid . "' selected=selected >" . $video_post->post_title . "</option>\n";
    }
    $output .= "</select>\n";
    $output .= " <script type='text/javascript'>
                  jQuery.getScript('" . WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . "/msdropdown/js/uncompressed.jquery.dd.js', function(data, textStatus, jqxhr) {
                    jQuery(\"#" . $field . "_selector$id\").msDropDown({
                      visibleRows:3,
                      rowHeight:50
                    });
                  });
                </script>";
  }
  return $output;
}

/**
 * Generates the combobox of available players.
 * @param int $id The attachment id.
 * @return string The HTML to render the player selector.
 */
function generatePlayerSelectorHTML($id) {
  $player_select = "<select name='attachments[$id][" . LONGTAIL_KEY . "player_select]' id='" . LONGTAIL_KEY . "player_select_" . $id . "'>\n";
  $player_select .= "<option value='Default'>Default</option>\n";
  $configs = LongTailFramework::getConfigs();
  if ($configs) {
    foreach ($configs as $config) {
      if ($config != "New Player") {
        $player_select .= "<option value='" . $config . "'>" . $config . "</option>\n";
      }
    }
  }
  $player_select .= "</select>\n";
  return $player_select;
}

// Filter hook for modifying the text inserted into the post body.
add_filter("media_send_to_editor", "jwplayer_tag_to_editor", 11, 3);

/**
 * Handler function for modifying the text entered into the post body.  If the
 * "Insert JW Player" button isn't hit the standard WordPress behavior is used.
 * Otherwise the jwplayer tag is inserted.
 * @param string $html The html WordPress will insert.
 * @param string $send_id The id of the object triggering the insert.
 * @param array $attachment The attachment to be inserted.
 * @return string The text to be inserted.
 */
function jwplayer_tag_to_editor($html, $send_id, $attachment) {
  global $wp_version;
  if ($attachment[LONGTAIL_KEY . "insert_type"] == "jwplayer") {
    $output = "[jwplayer ";
    if ($attachment[LONGTAIL_KEY . "player_select"] != "Default") {
      $output .= "config=\"" . $attachment[LONGTAIL_KEY . "player_select"] . "\" ";
      update_post_meta($_POST["post_id"], LONGTAIL_KEY . "fb_headers_config", $attachment[LONGTAIL_KEY . "player_select"]);
    }
    $output .= "mediaid=\"" . $send_id . "\"]";
    update_post_meta($_POST["post_id"], LONGTAIL_KEY . "fb_headers_id", $send_id);
    return $output;
  }
  if ($_POST["send"][$send_id] == "Insert JW Player") {
    $output = "[jwplayer ";
    if ($attachment[LONGTAIL_KEY . "player_select"] != "Default") {
      $output .= "config=\"" . $attachment[LONGTAIL_KEY . "player_select"] . "\" ";
      update_post_meta($_GET["post_id"], LONGTAIL_KEY . "fb_headers_config", $attachment[LONGTAIL_KEY . "player_select"]);
    }
    $output .= "mediaid=\"" . $send_id . "\"]";
    update_post_meta($_GET["post_id"], LONGTAIL_KEY . "fb_headers_id", $send_id);
    return $output;
  }
  return $html;
}

// Action hook for defining what the URL tab should use to render itself.
add_action("media_upload_jwplayer_url", "jwplayer_url_render");

/**
 * Handler for rendering the External Media tab.
 * @return string The HTML to render the tab.
 */
function jwplayer_url_render() {
  $errors = null;
  if (!empty($_POST)) {
    $return = media_upload_form_handler();

    if (is_string($return)) {
      return $return;
    }
    if (is_array($return)) {
      $errors = $return;
    }
  }
  require_once (dirname(__FILE__) . "/JWURLImportManager.php");
  return wp_iframe("media_jwplayer_url_insert_form", $errors);
}

// Action hook for defining what the Playlist tab should use to render itself.
add_action("media_upload_jwplayer", "jwplayer_render");

/**
 * Handler for rendering the JW Playlist Manager tab.
 * @return string The HTML to render the tab.
 */
function jwplayer_render() {
  $errors = null;
  if (!empty($_POST)) {
    $return = media_upload_form_handler();

    if (is_string($return)) {
      return $return;
    }
    if (is_array($return)) {
      $errors = $return;
    }
  }
  wp_enqueue_script('admin-gallery');
  require_once (dirname(__FILE__) . "/JWPlaylistImportManager.php");
  return wp_iframe("media_jwplayer_insert_form", $errors);
}

// Filter hook for adding additional tabs.
add_filter("media_upload_tabs", "jwplayer_tab");

/**
 * Handler for adding additional tabs.
 * @param array $_default_tabs The array of tabs.
 * @return array $_default_tabs with the new tabs added.
 */
function jwplayer_tab($_default_tabs) {
  $_default_tabs["jwplayer_url"] = "External Media";
  $_default_tabs["jwplayer"] = "Playlists";
  return $_default_tabs;
}

// Filter hook for modifying the URL that displays for URL attachments.
add_filter("wp_get_attachment_url", "url_attachment_filter", 10, 2);

/**
 * Handler for modifying the attachment url.
 * @param string $url The current URL.
 * @param <type> $id The id of the post.
 * @return string The modified URL.
 */
function url_attachment_filter($url, $id) {
  preg_match_all("/http:\/\/|rtmp:\/\//", $url, $matches);
  if (count($matches[0]) > 1) {
    $upload_dir = wp_upload_dir();
    return str_replace($upload_dir["baseurl"] . "/", "", $url);
  }
  return $url;
}

// Filter hook for modifying the file value that appears in the Media Library.
add_filter("get_attached_file", "url_attached_file", 10, 2);

/**
 * Handler for modifying the path to the attached file.
 * @param string $file The current file path.
 * @param int $attachment_id The id of the attachmenet.
 * @return string The modified file path.
 */
function url_attached_file($file, $attachment_id) {
  global $post;
  $external = get_post_meta($attachment_id, LONGTAIL_KEY . "external", true);
  if ((isset($post) && substr($post->post_mime_type, 0, 5) == "video") || $external) {
    $upload_dir = wp_upload_dir();
    return str_replace($upload_dir["basedir"] . "/", "", $file);
  }
  return $file;
}

?>
