<?php

class JWP6_Media {

    public $players;

    public function __construct() {
        $this->players = get_option(JWP6 . 'players');
    }

    public static function actions_and_filters() {
        add_filter('media_upload_tabs', array('JWP6_Media', 'add_media_tabs'), 99999);
        add_filter('media_upload_jwp6_media_embed', array('JWP6_Media', 'render_media_embed_tab'));
        add_filter('media_upload_jwp6_media_embed_playlist', array('JWP6_Media', 'render_media_embed_playlist_tab'));
        add_filter('media_upload_jwp6_media_external', array('JWP6_Media', 'render_media_external_tab'));
        add_filter("attachment_fields_to_edit", array('JWP6_Media', 'attachment_fields_to_edit'), 99, 2);
        add_filter("attachment_fields_to_save", array('JWP6_Media', 'attachment_fields_to_save'), 99, 2);
        add_filter("get_attached_file", array('JWP6_Media', 'url_attached_file'), 10, 2);
        add_filter("wp_get_attachment_url", array('JWP6_Media', 'url_attachment_filter'), 10, 2);
        add_filter('media_send_to_editor', array('JWP6_Media', 'media_send_to_editor'), 11, 3);
        add_action('admin_menu', array('JWP6_Media', 'admin_menu'));
        add_action("init", array('JWP6_Media', 'enqueue_scripts'));
    }

    public static function admin_menu() {
        add_media_page(
            "JW Player Playlist Manager",    //$page_title
            "JW Player Playlists",                     //$menu_title
            "read",                          //$capability
            JWP6 . "playlists",              //$menu_slug
            array('JWP6_Media', 'playlist_manager') //$function
        );
    }

    public static function add_media_tabs($tabs) {
        $tabs["jwp6_media_external"] = 'Add External Media';
        if ( JWP6_EMBED_WIZARD ) {
            $tabs["jwp6_media_embed"] = 'Embed a JW Player';
        } else {
            $tabs["jwp6_media_embed_playlist"] = 'Insert JWP Playlist';
        }
        return $tabs;
    }

    public static function render_media_embed_tab() {
        wp_redirect(JWP6_PLUGIN_URL . 'jwp6-media-embed.php');
        exit();
    }

    public static function render_media_embed_playlist_tab() {
        $no_video_error = false;
        if ( isset($_POST[JWP6 . 'playlistid']) && $_POST[JWP6 . 'playlistid'] ) {
            $shortcode = new JWP6_Shortcode();
            media_send_to_editor($shortcode->shortcode());
            exit();
        } else if ( count($_POST) ) {
            $no_video_error = true;
        }

        wp_enqueue_style('media');
        JWP6_Media::enqueue_scripts();
        wp_enqueue_script('admin-gallery');
        require_once (dirname(__FILE__) . "/jwp6-media-embed-playlist.php");
        return wp_iframe("jwp6_media_embed_playlist", $no_video_error);
    }

    public static function render_media_external_tab() {
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
        wp_enqueue_style('media');
        require_once (dirname(__FILE__) . "/jwp6-media-external.php");
        return wp_iframe("jwp6_media_external_tab", $errors);
        //wp_redirect(JWP6_PLUGIN_URL . 'jwp6-media-external.php');
        //exit();
    }

    public static function playlist_manager() {
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script("jquery-ui-tabs");
        wp_enqueue_script("jquery-ui-button");
        wp_enqueue_script("jquery-ui-widget");
        wp_enqueue_script("jquery-ui-mouse");
        wp_enqueue_script("jquery-ui-draggable");
        wp_enqueue_script("jquery-ui-droppable");
        wp_enqueue_script("jquery-ui-sortable");
        wp_register_style('jwp6-admin.css', JWP6_PLUGIN_URL.'css/jwp6-admin.css');
        wp_enqueue_style('jwp6-admin.css');
        require_once(JWP6_PLUGIN_DIR . '/jwp6-playlist-manager.php');
    }

    public static function attachment_array($type, $allowed_extensions) {
        $attachments = array();
        $args = array(
            'numberposts'    => 1000,
            'orderby'        => 'post_date',
            'order'          => 'DESC',
            'post_type'      => 'attachment',
            'post_mime_type' => $type,
            'post_status'    => null,
        );
        $posts = get_posts( $args );
        if ( $posts ) {
            foreach ( $posts as $post ) {
                $url = wp_get_attachment_url($post->ID);
                $extension = pathinfo($url, PATHINFO_EXTENSION);
                if ( in_array($extension, $allowed_extensions) )  {
                    array_push($attachments, array(
                        'id'    => $post->ID,
                        'title' => $post->post_title,
                        'name'  => $post->post_name,
                        'url'   => $url,
                    ));
                }
            }
        }
        return $attachments;
    }

    public function videos() {
        return $this->attachment_array('video', JWP6_Plugin::$supported_video_extensions);
    }

    public function images() {
        return $this->attachment_array('image', JWP6_Plugin::$supported_image_extensions);
    }

    public function playlists() {
        $params = array(
            "post_type" => 'jw_playlist',
            "post_status" => 'publish, private,draft',
            'sort_column' => 'post_title',
        );
        return get_posts($params);
    }

    public function playlist_name_with_info($playlist){
        $videosstring = get_post_meta($playlist->ID, LONGTAIL_KEY . "playlist_items", true);
        $videosarray = explode(',', $videosstring);
        $videoscount = count($videosarray);
        if ( 1 === $videoscount ) {
            return $playlist->post_title . " ($videoscount video)";
        } else {
            return $playlist->post_title . " ($videoscount videos)";
        }
    }

    public static function enqueue_scripts() {
        // Add javascript for thumb/playlistpickers
        wp_enqueue_script(
            'jquerySelect2', 
            JWP6_PLUGIN_URL.'js/jquery.select2.js',
            array('jquery')
        );
        wp_enqueue_script(
            'jwp6media', 
            JWP6_PLUGIN_URL.'js/jwp6-media.js',
            array('jquerySelect2')
        );
        wp_enqueue_style(
            'jquerySelect2Style',
            JWP6_PLUGIN_URL.'css/jquery.select2.css'
        );
   }

    public static function attachment_fields_to_edit($form_fields, $post) {
        $image_args = array(
            "post_type" => "attachment",
            "numberposts" => 50,
            "post_status" => null,
            "post_mime_type" => "image",
            "post_parent" => null
        );
        $image_attachments = get_posts($image_args);
        $mime_type = substr($post->post_mime_type, 0, 5);
        if ( 'video' == $mime_type || 'audio' == $mime_type ) {
            $poster_html = JWP6_Media::thumb_select_html($post->ID, $image_attachments);
            if ( ! isset($_REQUEST["post_id"]) ) {
                $poster_html .= JWP6_Media::insert_javascript_for_attachment_fields($post);
            }
            $form_fields[LONGTAIL_KEY . "thumbnail"] = array(
                "label" => "Poster image",
                "input" => "html",
                "html" => $poster_html
            );
            if ( isset($_REQUEST["post_id"]) ) {
                $form_fields[JWP6 . "insert_with_player"] = array(
                    "label" => "Embed with",
                    "input" => "html",
                    "html" => JWP6_Media::insert_player_html($post),
                );
            }
        }
        return $form_fields;
    }

    public static function attachment_fields_to_save($post, $attachment) {
        global $jwp6_global;
        $mime_type = substr($post["post_mime_type"], 0, 5);
        if ($mime_type == "video" || $mime_type == "audio") {
            update_post_meta($post["ID"], LONGTAIL_KEY . "thumbnail", $attachment[LONGTAIL_KEY . "thumbnail"]);
            // setting the selected player
            if ( isset($attachment[JWP6 . 'insert_with_player']) ) {
                $jwp6_global['insert_with_player'] = $attachment[JWP6 . 'insert_with_player'];
            }
        }
        return $post;
    }

    /**
     * Handler for modifying the path to the attached file.
     * @param string $file The current file path.
     * @param int $attachment_id The id of the attachmenet.
     * @return string The modified file path.
     */
    public static function url_attached_file($file, $attachment_id) {
        global $post;
        $external = get_post_meta($attachment_id, LONGTAIL_KEY . "external", true);
        if ((isset($post) && substr($post->post_mime_type, 0, 5) == "video") || $external) {
            $upload_dir = wp_upload_dir();
            return str_replace($upload_dir["basedir"] . "/", "", $file);
        }
        return $file;
    }

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

    // Needed for JWP5 version type of embedding
    public static function media_send_to_editor($html, $send_id, $attachment) {
        if ( isset($_POST['attachment']) && isset($_POST['action']) && $_POST['action'] == 'send-attachment-to-editor' && isset($_POST['attachment'][JWP6 . 'insert_jwplayer']) ) {
            $shortcode = new JWP6_Shortcode(null, $_POST['attachment']);
        } else if ( isset($_POST['send'][$send_id]) &&  isset($_POST['attachments'][$send_id][JWP6 . 'insert_with_player']) ) {
            $player_name = ( $_POST['attachments'][$send_id][JWP6 . 'insert_with_player'] ) ? $_POST['attachments'][$send_id][JWP6 . 'insert_with_player'] : 0;
            $shortcode = new JWP6_Shortcode(null, array(
                'player_name' => $player_name,
                'mediaid' => $send_id,
            ));
        }
        if ( isset($shortcode) ) return $shortcode->shortcode();
        return $html;
    }

    public static function thumb_select_html($id, $attachments) {
        $output = $image_id = $image_url = $image_id_class = $image_url_class = '';
        $thumbnail = get_post_meta($id, LONGTAIL_KEY . "thumbnail", true);
        $sel = false;
        if ( $attachments ) {
            if ( is_int($thumbnail) || ctype_digit($thumbnail) ) {
                $image_id = $thumbnail;
                $image_url_class = 'hidden';
            } else if ($thumbnail) {
                $image_url = JWP6_Plugin::image_from_mediaid($id);
                $image_id_class = 'hidden';
            } else {
                $image_url_class = 'hidden';
            }
        } else if ( $thumbnail ) {
            $image_url = $thumbnail;
        }

        $output .= "<input name='attachments[$id][" . LONGTAIL_KEY . "thumbnail]' id='".JWP6."the_image_value' type='hidden' value='{$thumbnail}' />";

        if ( $attachments ) {
            $output .= "<span id='thumb_select_group' style='width: 100%' class='{$image_id_class}'>";
            $output .= "<select name='".JWP6."the_image_id' id='".JWP6."the_image_id' style='width:100%;'>";
            $output .= "<option value='' title='No thumb' data-thumb='" . JWP6_Plugin::default_image_url() . "'>No thumbnail</option>";

            foreach($attachments as $post) {
                if ( substr($post->post_mime_type, 0, 5 ) == "image") {
                    if ( $post->ID == $image_id ) {
                        $selected = "selected='selected'";
                        $sel = true;
                    } else {
                        $selected = "";
                    }
                    $output .= "<option value='" . $post->ID . "' data-thumb='" . $post->guid . "' " . $selected . ">" . $post->post_title . "</option>";
                }
            }
            if ( !$sel && isset($image_post) && isset($image_id) && $image_id != -1 /*&& isset($thumbnail_url) && !$thumbnail_url*/ ) {
                $image_post = get_post($image_id);
                $output .= "<option value='" . $image_post->ID . "' data-thumb='" . $image_post->guid . "' selected=selected >" . $image_post->post_title . "</option>";
            }
            $output .= "</select>";
            $output .= "

                    <p class='description'>
                        or <a href='#mm_thumb_url' class='fieldset_toggle' id=''>enter a url to your thumbnail</a>.
                    </p>
                </span>
            ";
        }
        $output .= "
            <span id='thumb_url_group' class='{$image_url_class}'>
                <input type='text' name='".JWP6."the_image_url' id='".JWP6."the_image_url' value='{$image_url}' placeholder='e.g. http://example.com/thumbs/your_thumb.jpg' style='width:100%;' />
        ";
        if ( $attachments ) {
            $output .= "
                <p class='description'>
                    or <a href='#mm_thumb_select' class='fieldset_toggle' id=''>pick a thumbnail from the media library</a>.
                </p>
            ";
        }
        $output .= "
            </span>
        ";
        if ( isset($_REQUEST["post_id"]) ) {
            $output .= "
                <div class='jwp6_hr'></div>
            ";
        }
        return $output;
    }


    public static function insert_player_html($post) {
        global $jwp6_global, $wp_version;
        $insert_with_player = ( isset($jwp6_global['insert_with_player']) ) ? $jwp6_global['insert_with_player'] : 0; 
        $html = "";
        $html .="
            <select id='jwp6_insert_with_player' name='attachments[{$post->ID}][" . JWP6 . "insert_with_player]' style='width: 100%;'>
        ";
         foreach (get_option(JWP6 . 'players') as $player_id) {
            $selected = ( $insert_with_player == $player_id ) ? 'selected="selected"' : '';
            $player = new JWP6_Player($player_id);
            $html .= "
                <option value='{$player_id}' {$selected}>{$player->full_description()}</option>
            ";
         }
         $html .= "
            </select>

            <p class='description'>Player template to use.</p>

            <div class='jwp6_hr'></div>
        ";
        if ( isset($_GET["post_id"]) || version_compare($wp_version, '3.5', '<') ) { 
            $html .= "
                <input type='submit' class='button button-primary media-button' name='send[{$post->ID}]' value='Insert with JW Player' />
            ";
        } else {
            $html .= "
                <button class='insert_with_jwp6 button button-primary media-button'
                    data-url='". JWP6_PLUGIN_URL . "jwp6-media-embed.php'>
                    Insert with JW Player
                </button>
            ";
        }
        $html .= JWP6_Media::insert_javascript_for_attachment_fields($post);
        return $html;
    }

    public static function insert_javascript_for_attachment_fields($post) {
        return "
            <script language='javascript'>
                function jwp6_insert_player() {
                    var selected_player = jQuery('#jwp6_insert_with_player').val();
                    wp.media.post('send-attachment-to-editor', {
                        nonce: wp.media.view.settings.nonce.sendToEditor,
                        attachment: {
                            'id': " . $post->ID . ",
                            'player_name': selected_player,
                            '" . JWP6 . "insert_jwplayer': true,
                            '" . JWP6 . "mediaid': " . $post->ID . "
                        },
                        html:       '',
                        post_id: wp.media.view.settings.post.id
                    }).done(function(response) {
                        send_to_editor(response);
                    });
                }

                // Hack to resize the incredibly small sidebar
                // function jwp6_resize_sidebar() {
                //     if (jQuery(window).width() > 980) {
                //         jQuery('ul.attachments, div.media-toolbar').css('right', '400px');
                //         jQuery('div.media-sidebar').css('width', '363px');
                //     }
                // }
                function jwp6_init_select2() {
                    jwp6media.init_fieldset_toggles();
                    jQuery('div.wrap table.compat-attachment-fields').css('width', '100%');
                    jQuery('div.wrap table.compat-attachment-fields td').css('width', '75%');
                    jQuery('#".JWP6."the_image_id, #jwp6_insert_with_player').select2(jwp6media.SELECT2_SETTINGS).bind('change', jwp6media.select2_change)
                    jQuery('#".JWP6."the_image_id, #".JWP6."the_image_url').bind('change.value', jwp6media.setThumbnailValue);
                    jQuery('button.insert_with_jwp6').bind('click', jwp6_insert_player);
                    // jwp6_resize_sidebar();
                }
                jQuery(document).ready( function(e) {
                    var 
                        in_media_manager = jQuery('.media-modal').length > 0,
                        timeout = (in_media_manager) ? 200 : 0;
                    setTimeout(jwp6_init_select2, timeout);
                });
            </script>
        ";
    }

    /**
     * In the case of a YouTube URL this function retrieves the relevant metadata
     * from the YouTube API.
     * from the YouTube API.
     * @param string $video_id The YouTube video id.
     * @return array The array of relevant YouTube metadata.
     */
    public static function get_youtube_meta_data($video_id = "") {
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
        $youtube_media = $youtube_xml->children("http://search.yahoo.com/mrss/");
        $youtube_meta["title"] = $youtube_media->group->title;
        $youtube_meta["description"] = $youtube_media->group->description;
        $thumbnails = $youtube_xml->xpath("media:group/media:thumbnail");
        $youtube_meta["thumbnail_url"] = (string) $thumbnails[0]["url"];
        unlink($youtube_file);
        return $youtube_meta;
    }


}