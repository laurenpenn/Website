<?php

/**
 * @file This file contains the necessary functions for parsing the jwplayer
 * shortcode.  Re-implementation of the WordPress functionality was necessary
 * as it did not support '.'
 * @param string $the_content
 * @return string
 */
function jwplayer_tag_excerpt_callback($the_content = "") {
  $execute = $disable = false;
  if (is_archive()) {
    $archive_mode = get_option(LONGTAIL_KEY . "category_mode");
    $execute = $archive_mode == "excerpt";
    $disable = $archive_mode == "disable";
  } else if (is_search()) {
    $search_mode = get_option(LONGTAIL_KEY . "search_mode");
    $execute = $search_mode == "excerpt";
    $disable = $search_mode == "disable";
  } else if (is_tag()) {
    $tag_mode = get_option(LONGTAIL_KEY . "tag_mode");
    $execute = $tag_mode == "excerpt";
    $disable = $tag_mode == "disable";
  } else if (is_home()) {
    $home_mode = get_option(LONGTAIL_KEY . "home_mode");
    $execute = $home_mode == "excerpt";
    $disable = $home_mode == "disable";
  }
  $tag_regex = '/(.?)\[(jwplayer)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s';
  if ($execute) {
    $the_content = preg_replace_callback($tag_regex, "jwplayer_tag_parser", $the_content);
  } else if ($disable) {
    $the_content = preg_replace_callback($tag_regex, "jwplayer_tag_stripper", $the_content);
  }
  return $the_content;
}

/**
 * @param string $the_content
 * @return mixed|string
 */
function jwplayer_tag_widget_callback($the_content = "") {
  $tag_regex = '/(.?)\[(jwplayer)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s';
  $the_content = preg_replace_callback($tag_regex, "jwplayer_tag_parser", $the_content);
  return $the_content;
}

/**
 * Callback for locating [jwplayer] tag instances.
 * @param string $the_content The content to be parsed.
 * @return string The parsed and replaced [jwplayer] tag.
 */
function jwplayer_tag_callback($the_content = "") {
  $execute = $disable = false;
  if (is_archive()) {
    $archive_mode = get_option(LONGTAIL_KEY . "category_mode");
    $execute = $archive_mode == "content";
    $disable = $archive_mode == "disable";
  } else if (is_search()) {
    $search_mode = get_option(LONGTAIL_KEY . "search_mode");
    $execute = $search_mode == "content";
    $disable = $search_mode == "disable";
  } else if (is_tag()) {
    $tag_mode = get_option(LONGTAIL_KEY . "tag_mode");
    $execute = $tag_mode == "content";
    $disable = $tag_mode == "disable";
  } else if (is_home()) {
    $home_mode = get_option(LONGTAIL_KEY . "home_mode");
    $execute = $home_mode == "content";
    $disable = $home_mode == "disable";
  } else {
    $execute = true;
  }
  $tag_regex = '/(.?)\[(jwplayer)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s';
  if ($execute) {
    $the_content = preg_replace_callback($tag_regex, "jwplayer_tag_parser", $the_content);
  } else if($disable) {
    $the_content = preg_replace_callback($tag_regex, "jwplayer_tag_stripper", $the_content);
  }
  return $the_content;
}

/**
 * Parses the attributes of the [jwplayer] tag.
 * @param array $matches The match array
 * @return string The code that should replace the tag.
 */
function jwplayer_tag_parser($matches) {
  if ($matches[1] == "[" && $matches[6] == "]") {
    return substr($matches[0], 1, -1);
  }
  $param_regex = '/([\w.]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w.]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w.]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
  $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $matches[3]);
  $atts = array();
  if (preg_match_all($param_regex, $text, $match, PREG_SET_ORDER)) {
    foreach ($match as $p_match) {
      if (!empty($p_match[1]))
        $atts[$p_match[1]] = stripcslashes($p_match[2]);
      elseif (!empty($p_match[3]))
        $atts[$p_match[3]] = stripcslashes($p_match[4]);
      elseif (!empty($p_match[5]))
        $atts[$p_match[5]] = stripcslashes($p_match[6]);
      elseif (isset($p_match[7]) and strlen($p_match[7]))
        $atts[] = stripcslashes($p_match[7]);
      elseif (isset($p_match[8]))
        $atts[] = stripcslashes($p_match[8]);
    }
  } else {
    $atts = ltrim($text);
  }
  $player = jwplayer_handler($atts);
  return $matches[1] . $player . $matches[6];
}

/**
 * @param $matches
 * @return string
 */
function jwplayer_tag_stripper($matches) {
  if ($matches[1] == "[" && $matches[6] == "]") {
    return substr($matches[0], 1, -1);
  }
  return $matches[1] . $matches[6];
}

/**
 * The handler for replacing the [jwplayer] shortcode.
 * @param array $atts The parsed attributes.
 * @return string The script to replace the tag.
 */
function jwplayer_handler($atts) {
  $embedder = file_exists(LongTailFramework::getEmbedderPath());
  $config = "";
  $default = get_option(LONGTAIL_KEY . "default");
  $image = "";
  if (isset($atts["config"]) && LongTailFramework::configExists($atts["config"])) {
    $config = $atts["config"];
  } else if (LongTailFramework::configExists($default)) {
    $config = $default;
  } else {
    unset($atts["config"]);
  }
  LongTailFramework::setConfig($config);
  if (isset($atts["mediaid"])) {
    resolve_media_id($atts);
  }
  if (!array_key_exists("file", $atts)) {
    $atts["file"] = isset($atts["html5_file"]) ? $atts["html5_file"] : "";
  }
  if (empty($image)) {
    $image = isset($atts["image"]) ? $atts["image"] : "";
  }
  if ($embedder) $atts["modes"] = create_mode_block($atts);
  if (isset($atts["playlistid"])) {
    $id = $atts["playlistid"];
    $playlist = "";
    if (is_numeric($id)) {
      $playlist = get_post($id);
    }
    if (($playlist)) {
      if ($embedder) {
        $atts["playlist"] = generate_playlist($id);
      } else {
        $atts["file"] = urlencode(get_option('siteurl') . '/' . 'index.php?xspf=true&id=' . $id);
      }
    } else {
      return __("[PLAYLIST not found]", 'jw-player-plugin-for-wordpress');
    }
    unset($atts["playlistid"]);
  }
  $loaded_config = LongTailFramework::getConfigValues(true);
  $atts = array_merge($loaded_config, $atts);
  unset($atts["config"]);
  unset($atts["html5_file"]);
  unset($atts["download_file"]);
  if (is_feed()) {
    $out = '';
    // remove media file from RSS feed
    if (!empty($image)) {
      $width = isset($atts["width"]) ? $atts["width"] : $loaded_config["width"];
      $height = isset($atts["height"]) ? $atts["height"] : $loaded_config["height"];
      $out .= '<br /><img src="' . $image . '" width="' . $width . '" height="' . $height . '" alt="media" /><br />' . "\n";
    }
    return $out;
  }
  return generate_embed_code($atts);
}

function resolve_media_id(&$atts) {
  $id = $atts["mediaid"];
  $post = get_post($id);
  if (!isset($atts["image"])) {
    $thumbnail = get_post_meta($id, LONGTAIL_KEY . "thumbnail_url", true);
    if (!isset($thumbnail) || $thumbnail == null || $thumbnail == "") {
      $image_id = get_post_meta($id, LONGTAIL_KEY . "thumbnail", true);
      if (isset($image_id)) {
        $image_attachment = get_post($image_id);
        $atts["image"] = isset($image_attachment) ? $image_attachment->guid : "";
      }
    } else {
      $atts["image"] = $thumbnail;
    }
  }
  $mime_type = substr($post->post_mime_type, 0, 5);
  if ($mime_type == "image") {
    $duration = get_post_meta($id, LONGTAIL_KEY . "duration", true);
    $atts["duration"] = $duration ? $duration : 10;
    $atts["image"] = $post->guid;
  } else if ($mime_type == "audio") {
    if (empty($atts["image"]) && empty($atts["width"]) && empty($atts["height"])) {
      $atts["playerReady"] = "function(obj) { document.getElementById(obj['id']).parentNode.style.height = document.getElementById(obj['id']).getPluginConfig('controlbar')['height'] %2B 'px'; }";
      $atts["icons"] = false;
      $atts["controlbar"] = "bottom";
    }
  }
  if (!isset($atts["file"])) {
    $rtmp = get_post_meta($id, LONGTAIL_KEY . "rtmp");
    if (isset($rtmp) && $rtmp) {
      $atts["streamer"] = get_post_meta($id, LONGTAIL_KEY . "streamer", true);
      $atts["file"] = get_post_meta($id, LONGTAIL_KEY . "file", true);
    } else {
      $atts["file"] = $post->guid;
    }
  }
  if (!array_key_exists("html5_file", $atts)) {
    if ($html5_file = retrieve_file("html5", $id)) $atts["html5_file"] = $html5_file;
  }
  if (!array_key_exists("download_file", $atts)) {
    if ($download_file = retrieve_file("download", $id)) $atts["download_file"] = $download_file;
  }
  $creator = get_post_meta($id, LONGTAIL_KEY . "creator", true);
  $atts["title"] = $atts["title"] ? $atts["title"] : $post->post_title;
  $atts["creator"] = $atts["creator"] ? $atts["creator"] : $creator;
  $atts["author"] = $atts["creator"] ? $atts["creator"] : $creator;
  $atts["date"] = $atts["date"] ? $atts["date"] : $post->post_date;
  $atts["description"] = $atts["description"] ? $atts["description"] : $post->post_content;
}

function generate_embed_code($atts) {
  $version = version_compare(get_option(LONGTAIL_KEY . "version"), "5.3", ">=");
  $embedder = file_exists(LongTailFramework::getEmbedderPath());
  if (!$embedder && !$version && preg_match("/iP(od|hone|ad)/i", $_SERVER["HTTP_USER_AGENT"])) {
    $youtube_pattern = "/youtube.com\/watch\?v=([0-9a-zA-Z_-]*)/i";
    $loaded_config = LongTailFramework::getConfigValues();
    $width = isset($atts["width"]) ? $atts["width"] : $loaded_config["width"];
    $height = isset($atts["height"]) ? $atts["height"] : $loaded_config["height"];
    $output = "";
    if (preg_match($youtube_pattern, $atts["file"], $match)) {
      $output = '<object width="' . $width . '" height="' . $height . '"><param name="movie" value="http://www.youtube.com/v/' . $match[1] . '&amp;hl=en_US&amp;fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $match[1] . '&amp;hl=en_US&amp;fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="' . $width . '" height="' . $height . '"></embed></object>';
    } else {
      $output = '<video src="' . $atts["file"] . '" width="' . $width . '" height="' . $height . '" controls="controls"></video>';
    }
    return $output;
  } else {
    if (get_option(LONGTAIL_KEY . "player_location_enable")) {
      $swf = LongTailFramework::generateSWFObject($atts, $embedder, get_option(LONGTAIL_KEY . "player_location"));
    } else {
      $swf = LongTailFramework::generateSWFObject($atts, $embedder);
    }
    if (!get_option(LONGTAIL_KEY . "use_head_js")) {
      insert_embedder($embedder);
    }
    return $swf->generateEmbedScript();
  }
}

function create_mode_block($atts) {
  $modes = array();
  $playerMode = get_option(LONGTAIL_KEY . "player_mode", true);
  $flashMode = new stdClass();
  $flashMode->type = "flash";
  $flashMode->src = LongTailFramework::getPlayerURL();
  $html5Mode = new stdClass();
  $html5Mode->type = "html5";
  $html5Mode->config = new stdClass();
  $html5Mode->config->streamer = "";
  $html5Mode->config->provider = "";
  if (array_key_exists("html5_file", $atts)) $html5Mode->config->file = $atts["html5_file"];
  $downloadMode = new stdClass();
  $downloadMode->type = "download";
  $downloadMode->config = new stdClass();
  $downloadMode->config->streamer = "";
  $downloadMode->config->provider = "";
  if (array_key_exists("download_file", $atts)) $downloadMode->config->file = $atts["download_file"];
  if ($playerMode == "html5") {
    $modes[] = $html5Mode;
    $modes[] = $flashMode;
  } else {
    $modes[] = $flashMode;
    $modes[] = $html5Mode;
  }
  $modes[] = $downloadMode;
  return $modes;
}

function generate_playlist($playlist_id) {
  $output = array();
  $playlist = get_post($playlist_id);
  $playlist_items = "";
  if ($playlist) {
    $playlist_items = explode(",", get_post_meta($playlist_id, LONGTAIL_KEY . "playlist_items", true));
  }
  if (is_array($playlist_items)) {
    foreach ($playlist_items as $playlist_item_id) {
      $p_item = array();
      $playlist_item = get_post($playlist_item_id);
      $image_id = get_post_meta($playlist_item_id, LONGTAIL_KEY . "thumbnail", true);
      $creator = get_post_meta($playlist_item_id, LONGTAIL_KEY . "creator", true);
      $thumbnail = get_post_meta($playlist_item_id, LONGTAIL_KEY . "thumbnail_url", true);
      $streamer = get_post_meta($playlist_item_id, LONGTAIL_KEY . "streamer", true);
      $file = get_post_meta($playlist_item_id, LONGTAIL_KEY . "file", true);
      $html5_file = retrieve_file("html5", $playlist_item_id);
      $download_file = retrieve_file("download", $playlist_item_id);
      if (empty($thumbnail)) {
        $temp = get_post($image_id);
        $image = isset($temp) ? $temp->guid : "";
      } else {
        $image = $thumbnail;
      }
      $p_item["title"] = $playlist_item->post_title;
      $p_item["creator"] = $creator;
      $p_item["author"] = $creator;
      $p_item["date"] = $playlist_item->post_date;
      $p_item["levels"] = array();
      $flashFile = new stdClass();
      if (!empty($streamer)) {
        $p_item["streamer"] = $streamer;
        $flashFile->file = $file;
        $p_item["levels"][] = $flashFile;
      } else {
        $flashFile->file = $playlist_item->guid;
        $p_item["levels"][] = $flashFile;
      }
      if ($html5_file) {
        $html5File = new stdClass();
        $html5File->file = $html5_file;
        $p_item["levels"][] = $html5File;
      }
      if ($download_file) {
        $downloadFile = new stdClass();
        $downloadFile->file = $download_file;
        $p_item["levels"][] = $downloadFile;
      }
      $duration = get_post_meta($playlist_item_id, LONGTAIL_KEY . "duration", true);
      if ($duration) $p_item["duration"] = $duration;
      if (substr($playlist_item->post_mime_type, 0, 5) == "image") {
        $p_item["image"] = $playlist_item->guid;
      } else {
        $p_item["image"] = $image;
      }
      $p_item["description"] = $playlist_item->post_content;
      $p_item["mediaid"] = $playlist_item_id;
      $p_item["id"] = $playlist_item_id;
      $output[] = $p_item;
    }
  }
  return $output;
}

function retrieve_file($fileType, $id) {
  $file = get_post_meta($id, LONGTAIL_KEY . $fileType . "_file", true);
  if (!isset($file) || $file == null || $file == "") {
    $file_id = get_post_meta($id, LONGTAIL_KEY . $fileType . "_file_selector", true);
    if (isset($file_id) && $file_id > -1) {
      $file_attachment = get_post($file_id);
      $file = $file_attachment->guid;
    }
  }
  if (!isset($file) || $file == null || $file == "") {
    return false;
  }
  return $file;
}

function insert_embedder($embedderExists) {
  if ($embedderExists) {
    wp_print_scripts('jw-embedder');
  } else {
    wp_print_scripts('swfobject');
  }
}

?>
