<?php
/**
 * @file Definition of the JW Playlist Import Manager.
 */

/**
 * This file contains the necessary methods for rendering the Playlist Manager
 * tab in the WordPress media popup.  The code is largely borrowed from the
 * WordPress Gallery with necessary modifications for managing playlists and
 * showing all uploaded media.
 * @global string $redir_tab Global reference to the tab to redirect to on
 * submit.
 * @global string $type Global reference to the type of content being managed.
 * @param mixed $errors List of any errors encountered.
 */
function media_jwplayer_insert_form($errors) {
  global $redir_tab, $type;

  $redir_tab = 'jwplayer';
  media_upload_header();

  $post_id = intval($_REQUEST['post_id']);
  $form_action_url = admin_url("media-upload.php?type=$type&tab=jwplayer&post_id=$post_id");
  $form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);
  $playlists = jwplayer_get_playlists();

  $current_playlist = $playlists[0]->ID;
?>

<script type="text/javascript">

  function insertPlaylist() {
    var s;
    var playlist_dropdown = document.getElementById("<?php echo LONGTAIL_KEY . "playlist_select"; ?>");
    var player_dropdown = document.getElementById("<?php echo LONGTAIL_KEY . "player_select"; ?>");
    s = "[jwplayer ";
    if (player_dropdown.value != "Default") {
      s += "config=\"" + player_dropdown.value + "\" ";
    }
    s += "playlistid=\"" + playlist_dropdown.value + "\"]";
    getJWWin().send_to_editor(s);
  }

  function getJWWin() {
    return window.dialogArguments || opener || parent || top;
  }

</script>

<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="media-upload-form validate" id="playlist-form" style="width: 626px;">
  <?php wp_nonce_field('media-form'); ?>

  <h3 class="media-title"><?php _e("Insert a playlist", 'jw-player-plugin-for-wordpress'); ?></h3>
  <p><?php _e("This tab allows you to insert one of your playlists into your post.  To construct a playlist please visit the Playlist Media page.", 'jw-player-plugin-for-wordpress'); ?></p>
  <div id="media-items">
    <div class="media-item">
      <div class="alignleft actions" style="margin: 1em;">
        <div class="hide-if-no-js">
          <label for="<?php echo LONGTAIL_KEY . "playlist_select"; ?>"><strong><?php _e("Select Playlist:", 'jw-player-plugin-for-wordpress'); ?></strong></label>
          <select id="<?php echo LONGTAIL_KEY . "playlist_select"; ?>" name="<?php echo LONGTAIL_KEY . "playlist_select"; ?>">
            <?php foreach ($playlists as $playlist_list) { ?>
            <option value="<?php echo $playlist_list->ID; ?>" <?php selected($playlist_list->ID, $current_playlist); ?>>
              <?php echo $playlist_list->post_title; ?>
            </option>
            <?php } ?>
          </select>
          <input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
          <input type="hidden" name="type" value="<?php echo esc_attr( $GLOBALS['type'] ); ?>" />
          <input type="hidden" name="tab" value="<?php echo esc_attr( $GLOBALS['tab'] ); ?>" />
        </div>
      </div>

      <div class="clear"></div>

      <p class="ml-submit" style="padding: 0 0; margin: 1em;">
        <label for="<?php echo LONGTAIL_KEY . "player_select"; ?>"><strong><?php _e("Select Player:", 'jw-player-plugin-for-wordpress'); ?></strong></label>
        <select name="<?php echo LONGTAIL_KEY . "player_select"; ?>" id="<?php echo LONGTAIL_KEY . "player_select"; ?>">
          <option value="Default">Default</option>
          <?php $configs = LongTailFramework::getConfigs(); ?>
          <?php if ($configs) { ?>
          <?php foreach ($configs as $config) { ?>
            <?php if ($config != "New Player") { ?>
              <option value="<?php echo $config; ?>"><?php echo $config; ?></option>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        </select>
        <input type="button" class="button-primary" onmousedown="insertPlaylist();" name="insert-gallery" id="insert-gallery" value="<?php esc_attr_e('Insert Playlist', 'jw-player-plugin-for-wordpress'); ?>" />
      </p>
    </div>
  </div>
</form>
<?php
}

/**
 * Builds the argument array for retrieving the playlist type custom post.
 * @return array The arguments for retrieving the playlists.
 */
function jwplayer_get_playlists() {
  $playlist = array(
    "post_type" => "jw_playlist",
    "post_status" => null,
    "post_parent" => null,
    "nopaging" => true,
  );
  return query_posts($playlist);
}

?>