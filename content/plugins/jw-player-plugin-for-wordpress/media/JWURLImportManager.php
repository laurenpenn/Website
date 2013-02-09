<?php
/**
 * @file This file contains the functions for rendering the External Media tab.
 * This is a combination of the default WordPress From Computer and URL Import
 * tabs.
 */

/**
 * Renders the form for inserting URL files into the Media Library.
 * @global string $redir_tab The tab to redirect to on form submits.
 * @global string $type The type of media being considered.
 * @param undefined $errors Any errors the occurred.
 */
function media_jwplayer_url_insert_form($errors) {
  global $redir_tab, $type;

  $redir_tab = 'jwplayer_url';
  media_upload_header();

  $post_id = intval($_REQUEST['post_id']);

  $form_action_url = admin_url("media-upload.php?type=$type&tab=$redir_tab&post_id=$post_id");
  $form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);

  if (isset($_POST["insertonlybutton"])) {
    $youtube_pattern = "/youtube.com\/watch\?v=([0-9a-zA-Z_-]*)/i";
    $url = $_POST["insertonly"]["href"];
    $attachment = array(
      "post_mime_type" => "video/x-flv",
      "guid" => $url,
      "post_parent" => $post_id,
    );
    if (preg_match($youtube_pattern, $url, $match)) {
      $youtube_api = get_youtube_meta_data($match[1]);
      if ($youtube_api) {
        $attachment["post_title"] = $youtube_api["title"];
        $attachment["post_content"] = $youtube_api["description"];
      }
    } else {
      $file_info = wp_check_filetype($url);
      if ($file_info["type"] != null) {
        $attachment["post_mime_type"] = $file_info["type"];
        $attachment["post_content"]="";
	      $attachment["post_title"]="";
      }
    }
    $id = wp_insert_attachment($attachment, $url, $post_id);
    if ($youtube_api) {
      update_post_meta($id, LONGTAIL_KEY . "thumbnail_url", $youtube_api["thumbnail_url"]);
      update_post_meta($id, LONGTAIL_KEY . "creator", $youtube_api["author"]);
    } else if (strstr($url, "rtmp://")) {
      update_post_meta($id, LONGTAIL_KEY . "streamer", str_replace(basename($url), "", $url));
      update_post_meta($id, LONGTAIL_KEY . "file", basename($url));
      update_post_meta($id, LONGTAIL_KEY . "rtmp", true);
    }
    update_post_meta($id, LONGTAIL_KEY . "external", true);
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $url));
  }
  ?>

  <form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="media-upload-form type-form validate" id="<?php echo $type; ?>-form">
    <input type="submit" class="hidden" name="save" value="" />
    <input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
  <?php wp_nonce_field('media-form'); ?>

    <h3 class="media-title"><?php _e('Add media file from URL', 'jw-player-plugin-for-wordpress'); ?></h3>

    <div id="url-upload-ui">
      <table class="describe">
        <tbody>
          <tr>
            <th valign="top" scope="row" class="label">
              <span class="alignleft"><label for="insertonly[href]"><?php _e('URL', 'jw-player-plugin-for-wordpress') ?></label></span>
              <span class="alignright"><abbr title="required" class="required">*</abbr></span>
            </th>
            <td class="field"><input id="insertonly[href]" name="insertonly[href]" value="" type="text" aria-required="true"></td>
          </tr>
          <tr>
            <td></td>
            <td>
              <input type="submit" class="button" name="insertonlybutton" value="<?php echo esc_attr__('Add Media', 'jw-player-plugin-for-wordpress'); ?>" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <script type="text/javascript">
      //<![CDATA[
      jQuery(function($){
        var preloaded = $(".media-item.preloaded");
        if ( preloaded.length > 0 ) {
          preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
        }
        updateMediaForm();
      });
      //]]>
    </script>
    <div id="media-items">
  <?php
  if (isset($id)) {
    if (!is_wp_error($id)) {
      add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2);
      echo get_media_items($id, $errors);
    } else {
      echo '<div id="media-upload-error">' . esc_html($id->get_error_message()) . '</div>';
      exit;
    }
  }
  ?>
    </div>
    <p class="savebutton ml-submit">
      <input type="submit" class="button" name="save" value="<?php esc_attr_e('Save all changes', 'jw-player-plugin-for-wordpress'); ?>" />
    </p>
  </form>
  <?php
}

/**
 * In the case of a YouTube URL this function retrieves the relevant metadata
 * from the YouTube API.
 * from the YouTube API.
 * @param string $video_id The YouTube video id.
 * @return array The array of relevant YouTube metadata.
 */
function get_youtube_meta_data($video_id = "") {
  if ($video_id == "") {
    return "";
  }
  $youtube_meta = array();
  $youtube_url = "http://gdata.youtube.com/feeds/api/videos/" . $video_id;
  $youtube_file = download_url($youtube_url);
  if (is_wp_error($youtube_file)) {
    return false;
  }
  $youtube_xml = simplexml_load_file($youtube_file);
  $youtube_meta["author"] = (string) $youtube_xml->author->name;
  $youtube_media = $youtube_xml->children("http://search.yahoo.com/mrss/");
  $youtube_meta["title"] = $youtube_media->group->title;
  $youtube_meta["description"] = $youtube_media->group->description;
  $thumbnails = $youtube_xml->xpath("media:group/media:thumbnail");
  $youtube_meta["thumbnail_url"] = (string) $thumbnails[0]["url"];
  unlink($youtube_file);
  return $youtube_meta;
}
?>
