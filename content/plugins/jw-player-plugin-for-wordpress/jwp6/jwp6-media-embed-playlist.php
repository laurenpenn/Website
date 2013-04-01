<?php

function jwp6_media_embed_playlist($no_video_error = false) {
    global $type, $wp_version; //$redir_tab, $type;

    $jwp6m = new JWP6_Media();

    define('MEDIA_MANAGER_35', version_compare($wp_version, '3.5', '>=') );

    if (! MEDIA_MANAGER_35 ) {
        media_upload_header();
    }
    ?>

    <script type="text/javascript">
    var JWP6_AJAX_URL = "<?php echo JWP6_PLUGIN_URL . 'jwp6-ajax.php'; ?>";
    jQuery(function () {
        jQuery('#player_name, #<?php echo JWP6; ?>playlistid')
            .select2(jwp6media.SELECT2_SETTINGS)
        ;
        jwp6media.init_media_wizard();
    });
    </script>

    <form method="post" action="" name="jwp6_wizard_form" id="jwp6_wizard_form">

    <?php if ( ! MEDIA_MANAGER_35 ): ?>
    <h3 class="media-title">Insert JW Player Playlist</h3>
    <?php endif; ?>

    <?php if ( $no_video_error ): ?>
    <div class="notice">
        <strong>Please note:</strong>
        You need to pick at least a playlist to embed the JW Player.
    </div>
    <?php endif; ?>

    <p>Embed a playlist into your post with JW Player</p>

    <table>
        <tbody>
            <tr>
                <th scope="row" class="label">
                    <span class="alignleft"><label for="<?php echo JWP6; ?>playlistid">Playlist</label></span>
                </th>
                <td class="field">
                    <select name="<?php echo JWP6; ?>playlistid" id="<?php echo JWP6; ?>playlistid" data-placeholder="Pick a playlist..." style="width: 90%;">
                        <option value=""></option>
                        <?php foreach ($jwp6m->playlists() as $playlist): ?>
                        <option value="<?php echo $playlist->ID; ?>">
                            <?php echo $jwp6m->playlist_name_with_info($playlist); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th></th>
                <td>
                    <p class="description">
                        Create playlists in  <a target="_top" href="<?php echo admin_url("upload.php?page=". JWP6 . "playlists"); ?>">the media section</a>.
                        <br />&nbsp;
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row" class="label">
                    <span class="alignleft"><label for="player_name">JW PLayer</label></span>
                </th>
                <td class="field">
                    <select id="player_name" name="player_name" style="width: 90%;">
                        <?php foreach ($jwp6m->players as $player_id): ?>
                        <?php $player = new JWP6_Player($player_id); ?>
                        <option value="<?php echo $player_id; ?>"><?php echo $player->full_description(); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th></th>
                <td>
                    <p class="description">
                        Select the player you would like to use. You can add and manage players on the 
                        <a href="">Player configuration page</a>
                        <br />&nbsp;
                    </p>
                </td>
            </tr>
            <tr>
                <th></th>
                <td class="field">
                    <button type="submit" class="button-primary" name="insert">Insert this player into your post</button>
                </td>
            </tr>
        </tbody>
    </table>

    </form><!-- end of jwp6_wizard_form -->

    </div><!-- end of wrapper -->
    <?php
}
?>