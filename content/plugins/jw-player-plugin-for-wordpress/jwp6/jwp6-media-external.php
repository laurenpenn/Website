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
function jwp6_media_external_tab($errors) {
    global $redir_tab, $type, $wp_version; //$redir_tab, $type;

    $redir_tab = "jwp6_media_external";

    define('MEDIA_MANAGER_35', version_compare($wp_version, '3.5', '>=') );

    if (! MEDIA_MANAGER_35 ) {
        media_upload_header();
    }

    $post_id = intval($_REQUEST['post_id']);

    $form_action_url = admin_url("media-upload.php?type=$type&tab=" . JWP6 . "media_external&post_id=$post_id");
    $form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);

    if ( isset($_POST["insertonlybutton"]) ) {
        $youtube_pattern = "/youtube.com\/watch\?v=([0-9a-zA-Z_-]*)/i";
        $url = $_POST["insertonly"]["href"];
        $attachment = array(
            "post_mime_type" => "video/mp4",
            "guid" => $url,
            "post_parent" => $post_id,
        );
        if (preg_match($youtube_pattern, $url, $match)) {
            $youtube_api = JWP6_Media::get_youtube_meta_data($match[1]);
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
        if ( isset($youtube_api) && $youtube_api ) {
            update_post_meta($id, LONGTAIL_KEY . "thumbnail", $youtube_api["thumbnail_url"]);
        } else if ( strstr($url, "rtmp://") ) {
            // update_post_meta($id, LONGTAIL_KEY . "streamer", str_replace(basename($url), "", $url));
            // update_post_meta($id, LONGTAIL_KEY . "file", basename($url));
            // update_post_meta($id, LONGTAIL_KEY . "rtmp", true);
            update_post_meta($id, LONGTAIL_KEY . "rtmp", $url);
        }
        update_post_meta($id, LONGTAIL_KEY . "external", true);

        wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $url));
    }
    ?>

    <form enctype="multipart/form-data" class="jwp6-media-form <?php if ( MEDIA_MANAGER_35 ) { echo "jwp6-media-35"; }?>" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="media-upload-form type-form validate" id="<?php echo $type; ?>-form">
        <input type="submit" class="hidden" name="save" value="" />
        <input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
        <?php wp_nonce_field('media-form'); ?>

        <?php if ( ! MEDIA_MANAGER_35 ): ?>
        <h3 class="media-title">Add external media</h3>
        <?php endif; ?>
        <?php if ( ! $_POST ): ?>
        <p> Add external media to your library to embed it with the JW Player</p>

        <div id="url-upload-ui">
            <table>
                <tbody>
                    <tr>
                        <th scope="row" class="label">
                            <span class="alignleft"><label for="insertonly[href]">Media URL</label></span>
                        </th>
                        <td class="field"><input id="insertonly[href]" name="insertonly[href]" value="" type="text" aria-required="true" 
                            style="width: 95%;" placeholder="Add the URL to your media here."></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td class="description">
                            <p>The following types of external media are supported:</p>
                            <ol>
                                <li>
                                    MP4/FLV video (http://example.com/video.mp4)
                                </li>
                                <li>
                                    MP3/AAC Audio (http://example.com/audio.mp3)
                                </li>
                                <li>
                                    YouTube video (http://youtu.be/dQw4w9WgXcQ)
                                </li>
                                <li>
                                    MP4/FLV video (rtmp://example/com/vode/mp4:video.mp4)
                                </li>
                            </ol>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" class="button button-primary" name="insertonlybutton" value="Add to library" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <script type="text/javascript">
            //<![CDATA[
            jQuery(function($){
                var preloaded = $(".media-item.preloaded");
                if ( preloaded.length > 0 ) {
                    preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
                }
                updateMediaForm();
                $('tr.url button, p.help').remove();
                <?php if ( MEDIA_MANAGER_35 ): ?>
                jQuery('ul#sidemenu').css('display', 'none');
                jQuery('a.describe-toggle-off').css('display', 'none');
                <?php endif; ?>
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
?>
