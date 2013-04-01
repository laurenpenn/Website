<?php
/**
 * @file The script to dynamically generate the playlist XML given a playlist
 * id.
 */

$playlist_id = $_GET['id'];

$title = 'JW Player Playlist';

$themediafiles = array();
$limit = '';

$playlist_id = intval($_GET['id']);
$playlist = get_post($playlist_id);
if ($playlist) {
  $playlist_items = explode(",", get_post_meta($playlist_id, LONGTAIL_KEY. "playlist_items", true));
}

header("content-type:text/xml;charset=utf-8");
	
echo "\n"."<playlist version='1' xmlns='http://xspf.org/ns/0/' xmlns:jwplayer='http://developer.longtailvideo.com/trac/wiki/FlashFormats'>";
echo "\n\t".'<title>' . esc_attr($title) . '</title>';
echo "\n\t".'<trackList>';
	
if (is_array ($playlist_items)) {
	foreach ($playlist_items as $playlist_item_id) {
    $playlist_item = get_post($playlist_item_id);
    $image_id = get_post_meta($playlist_item_id, LONGTAIL_KEY . "thumbnail", true);
    $creator = get_post_meta($playlist_item_id, LONGTAIL_KEY . "creator", true);
    $thumbnail = get_post_meta($playlist_item_id, LONGTAIL_KEY . "thumbnail_url", true);
    $streamer = get_post_meta($playlist_item_id, LONGTAIL_KEY . "streamer", true);
    $file = get_post_meta($playlist_item_id, LONGTAIL_KEY . "file", true);
    if (empty($thumbnail)) {
      $temp = get_post($image_id);
      $image = $temp->guid;
    } else {
      $image = $thumbnail;
    }

		echo "\n\t\t".'<track>';
		echo "\n\t\t\t".'<title>' . esc_attr(stripslashes(__($playlist_item->post_title))) . '</title>';
		echo "\n\t\t\t".'<creator>' . esc_attr($creator) . '</creator>';
    if (!empty($streamer)) {
      echo "\n\t\t\t".'<jwplayer:streamer>' . esc_attr($streamer) . '</jwplayer:streamer>';
      echo "\n\t\t\t".'<location>' . esc_attr($file) . '</location>';
    } else {
      echo "\n\t\t\t".'<location>' . esc_attr($playlist_item->guid) . '</location>';
    }
    if (substr($playlist_item->post_mime_type, 0, 5) == "image") {
      $duration = get_post_meta($playlist_item_id, LONGTAIL_KEY . "duration", true);
      echo "\n\t\t\t"."<jwplayer:duration>" . ($duration ? $duration : 10) . "</jwplayer:duration>";
      echo "\n\t\t\t".'<image>' . esc_attr($playlist_item->guid) . '</image>';
    } else {
      echo "\n\t\t\t".'<image>' . esc_attr($image) . '</image>';
    }
		echo "\n\t\t\t".'<annotation>' . esc_attr(stripslashes($playlist_item->post_content)) .  '</annotation>';
    echo "\n\t\t\t".'<jwplayer:mediaid>' . $playlist_item_id . '</jwplayer:mediaid>';
		echo "\n\t\t\t".'<id>' . $playlist_item_id . '</id>';
		echo "\n\t\t".'</track>';
	}
}
	 
echo "\n\t".'</trackList>';
echo "\n"."</playlist>\n";	

?>