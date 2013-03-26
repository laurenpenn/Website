<?php
/**
 * WPBakery Visual Composer Media tab for adding media content to visual composer
 *
 * @package WPBakeryVisualComposer
 *
 */

require_once(ABSPATH . '/wp-includes/script-loader.php');

class WPBakeryMediaTab extends WPBakeryVisualComposerAbstract implements WPBakeryVisualComposerTemplateInterface {

    public function __construct() {

    }

    public function output($post = null)
    {

    }
}

class WPBakeryImagesMediaTab extends WPBakeryMediaTab {
    protected $title, $selected_ids;
    protected $pageLimit, $type, $errors, $id;
    protected $selected_objects_list = '';
    public function __construct() {
        parent::__construct();

        $this->title = __('WPB Images', 'js_composer');
        $this->pageLimit = 10;
        $this->modal_wpb = $this->get('modal_wpb');
        $this->selected_ids = $this->get('paged') ? Array() : preg_split('/\,/', preg_replace('/\s\t/', '', $this->get('selected_ids')));
        /* show tab only if $_GET['tab'] == 'composer_images' */
        if($this->get('tab')=='composer_images') {

            $this->addFilter('media_upload_tabs', 'buildTab');
            // $this->addAction('admin_head', 'header');
            $this->addAction('media_upload_composer_images','output');
        }
    }

    public function buildTab($tabs) {
        return array_merge($tabs, Array('composer_images' => $this->title));
    }
    public function output($type = 'file', $errors = null, $id = null) {
        return $this->get('paged') ? $this->getContentList() : wp_iframe(array($this, 'formTemplate'));
    }

    public function getMediaItem( $attachment_id, $args = null) {
        if ( ( $attachment_id = intval( $attachment_id ) ) && $thumb_url = wp_get_attachment_image_src( $attachment_id, Array(30,30) ) )
            $thumb_url = $thumb_url[0];
        else
            $thumb_url = false;
        return $thumb_url ? '<li class="wpb_media_block added' . (isset($args['used']) && $args['used'] ? ' used' : '') . '" media_id="' . $attachment_id . '"><a href="javascript:void();" class="wpb_media_block_link"><img src="' . $thumb_url . '" alt="" rel="' . $attachment_id . '" /></a><a href="#" class="icon-remove"></a></li>' : '';
    }

    public function showImagesList( $post_id) {
        $attachments = array();
        if ( $post_id ) {
            $post = get_post($post_id);
            if ( $post && $post->post_type == 'attachment' )
                $attachments = array($post->ID => $post);
            else
                $attachments = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'orderby' => 'menu_order ASC, ID', 'order' => 'DESC') );
        } else {
            if ( is_array($GLOBALS['wp_the_query']->posts) )
                foreach ( $GLOBALS['wp_the_query']->posts as $attachment )
                    $attachments[$attachment->ID] = $attachment;
        }

        $output = '';
        foreach ( (array) $attachments as $id => $attachment ) {
            if ( $attachment->post_status == 'trash' )
                continue;
            if ( $item = $this->getMediaItem( $id, array( 'used' => in_array((string)$id, $this->selected_ids), 'errors' => isset($this->errors[$id]) ? $this->errors[$id] : null) ) )

                    $output .= "\n$item\n";

        }

        return $output;
    }

    public function getContentList() {
        global $wpdb, $wp_query, $post_mime_types;

        $_GET['paged'] = isset( $_GET['paged'] ) ? intval($_GET['paged']) : 0;
        if ( $_GET['paged'] < 1 )
            $_GET['paged'] = 1;
        $start = ( $_GET['paged'] - 1 ) * $this->pageLimit;
        if ( $start < 1 )
            $start = 0;
        add_filter( 'post_limits', create_function( '$a', "return 'LIMIT $start, $this->pageLimit';" ) );


        list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query();

        /* Add selected media items to selected items block */
        foreach($this->selected_ids as $id) {
            if ( $item = $this->getMediaItem( $id, array( 'errors' => isset($this->errors[$id]) ? $this->errors[$id] : null) ) )
            $this->selected_objects_list .= "\n$item\n";
        }

        ?>
        <?php if(isset($_GET['body_class']) && strlen($_GET['body_class'])>0): ?>
        <script type="text/javascript">
            document.body.className = document.body.className.replace('js', 'js <?php echo $_GET['body_class'] ?>');
        </script>
        <?php endif; ?>
        <ul id="wpb_media-items">
            <?php add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2); ?>
            <?php echo $this->showImagesList(null, $this->errors); ?>
        </ul>
        <div style="clear:both;"></div>
        <?php
        $page_links = paginate_links( array(
            'base' => add_query_arg( 'paged', '%#%' ),
            'format' => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => ceil($wp_query->found_posts / $this->pageLimit),
            'current' => $_GET['paged']
        ));

        if ( $page_links )
            echo '<br/><div class="tablenav"><div id="wpb_spinner" style="display: none;"><img src="'.get_site_url().'/wp-admin/images/wpspin_light.gif" /> '.__("Loading, please wait...", "js_composer").'</div><div class="tablenav-pages">' . $page_links . '</div></div>';

    }

    public function formTemplate($type = 'file', $errors = null, $id = null)
    {
        wp_register_style( 'js_composer', $this->assetURL( 'js_composer.css' ), false, WPB_VC_VERSION, false );
        wp_enqueue_style('js_composer');

        wp_register_style( 'bootstrap', $this->assetURL( 'bootstrap/css/bootstrap.css' ), false, WPB_VC_VERSION, false );
        wp_enqueue_style('bootstrap');

        wp_register_style( 'ui-custom-theme', $this->assetURL( 'ui-custom-theme/jquery-ui-1.8.18.custom.css' ), false, WPB_VC_VERSION, false );
        wp_enqueue_style('ui-custom-theme');

        wp_register_script('wpb_js_composer_js', $this->assetURL( 'js_composer.js' ), array('jquery'), WPB_VC_VERSION, true);
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-droppable');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-sortable');

        wp_enqueue_script('bootstrap-js');

        global $is_iphone;
        global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;
        if ( $is_iphone )
            return;

        $post_id = isset( $_REQUEST['post_id'] )? intval( $_REQUEST['post_id'] ) : 0;

        $form_action_url = admin_url("media-upload.php?type=$this->type&tab=type&post_id=$post_id");
        $form_action_url = apply_filters('media_upload_form_url', $form_action_url, $this->type);
        $form_class = 'media-upload-form type-form validate';
        if ( get_user_setting('uploader') )
            $form_class .= ' html-uploader';
        ?>
        <div class="log"></div>
        <div class="wpb_media_tab row-fluid">

           <div class="<?php echo $this->get('single_image')=='true' ? 'span12' : 'span6' ?>">
            <h2 class="media-title"><?php _e('Click image from media library or drag it to the "Selected images" area', 'js_composer'); ?></h2>
            <div id="wpb_composer_media_list">
                <?php echo $this->getContentList(); ?>
            </div>
            <script type="text/javascript">
                post_id = <?php echo $post_id ?>;
            </script>
            <form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="<?php echo $form_class; ?>" id="<?php echo $this->type; ?>-form">
                <?php submit_button( '', 'hidden', 'save', false ); ?>
                <input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
                <?php wp_nonce_field('media-form'); ?>

                <h2 class="media-title"><?php _e('or add images from your computer.', 'js_composer'); ?></h2>

                <?php media_upload_form( $this->errors ); ?>

                <div id="media-items"><?php

                    if ( $this->id ) {
                        if ( !is_wp_error($this->id) ) {
                            add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2);
                            echo get_media_items( $this->id, $this->errors );
                        } else {
                            echo '<div id="media-upload-error">'.esc_html($this->id->get_error_message()).'</div></div>';
                            exit;
                        }
                    }
                    ?></div>

                <p class="savebutton ml-submit" style="display:none;">
                    <!-- <?php submit_button( __( 'Save all changes', "js_composer" ), 'button', 'save', false ); ?> -->
                </p>
            </form>
            </div>
			<script type="text/javascript">
                jQuery.currentLibrary_page = window.location.href + '&paged=1';
                jQuery(document).ready(function($){

                    $.select_images_from_lib = {};

                    $.updateLibraryBlock = function() {

                        $.ajax({
                            type: 'GET',
                            url: $.currentLibrary_page,
                            success: function(data) {
                                $('#wpb_composer_media_list').html(data).find('.wpb_media_block').hide();
                                for(i in $.select_images_from_lib) {
                                    if($.select_images_from_lib[i] != null) $('#wpb_composer_media_list .wpb_media_block[media_id=' + $.select_images_from_lib[i] + ']').addClass('used');
                                }

                                $('#wpb_composer_media_list .wpb_media_block').show();
                                $.initDragAndDrop();
                            }
                        });
                        $('#wpb_composer_media_list').load();
                    };

                    $('#wpb_composer_media_list').on('click', '.page-numbers',function(e){
                        e.preventDefault();
                        $.currentLibrary_page = $(this).attr('href');
                        $.updateLibraryBlock();
                    });

                    $.generateItemBlock = function($item) {
                        return $('<li id="wpb_selected_media_block" media_id="' + $item.attr('media_id') +'" style="display:none;">'  + $item.html() + '<i class="icon-remove"></i></a></li>');
                    };
                });
			</script>
            <?php if($this->get('single_image')!= 'true'): ?>
            <div class="span6 selected_items">
                <h2><?php _e('Selected images', 'js_composer') ?></h2>
                <p><?php _e('Use your mouse to drag images around to place them in the desired order.', 'js_composer') ?></p>
                <div class="border">
                    <ul id="wpb-items-list">
                       <?php echo $this->selected_objects_list; ?>
                    </ul>
                    <div style="clear:both;"></div>
                </div>
                <br/>
                <button class="button-primary" id="vpb_send_selected_files"><?php _e('Update files list', 'js_composer') ?></button>
            </div>
            <script type="text/javascript">
                var modal_wpb = '<?php echo $this->get('modal_wpb'); ?>';
                jQuery(document).ready(function($){

                    parent.jQuery(window).bind('load resize', function(){
                        parent.jQuery('#TB_window').css({width: '98%', marginLeft: '-49%'}).find('iframe').css({'width': '98%', marginLeft: '1%'});
                    });
                    $('#wpb-items-list li').each(function(){
						$.select_images_from_lib['media_' + $(this).attr('media_id')] =  $(this).attr('media_id');
					});

                    $('#wpb_composer_media_list').on('click', '.wpb_media_block_link', function(e){
                        e.preventDefault();
                        if($(this).parent().hasClass('used')) return false;
                        var $block = $(this).parent().clone();
                        $block.appendTo('#wpb-items-list');
                        $.select_images_from_lib['media_' + $block.attr('media_id')] = $block.attr('media_id');
                        $(this).parent().addClass('used');
                        // $block.show(300);
                        // $.initDragAndDrop();
                    });

                    $.initDragAndDrop = function() {

                        $('#wpb_media-items .wpb_media_block').draggable({
                            connectToSortable: "#wpb-items-list",
                            helper: "clone",
                            distance: 0.5,
                            revert: "invalid",
                            start: function(event, ui) {
                                if($(this).hasClass('used')) return false;
                            }
                        });

                        $('#wpb-items-list').sortable({
                            revert: true,
                            distance: 0.5,
                            update: function(event, ui) {
                                $block = ui.item;
                                $.select_images_from_lib['media_' + $block.attr('media_id')] = $block.attr('media_id');
                                $('#wpb_media-items .wpb_media_block[media_id=' + $block.attr('media_id') + ']').addClass('used');
                            }
                        });


                    };

                    $('.wpb_media_tab').on('click', '.icon-remove', function(e){
                        e.preventDefault();
                        $.select_images_from_lib['media_' + $(this).parent().attr('media_id')] = null;
                        $('#wpb_media-items .wpb_media_block[media_id=' + $(this).parent().attr('media_id') + ']').removeClass('used');
                        $(this).parent().remove();
                        // $.updateLibraryBlock();
                    });

                    $('#vpb_send_selected_files').click(function(e){
                        e.preventDefault();
                        if( parent != undefined ) {
                            if(modal_wpb==='true') {
                                parent.jQuery.wpbGlobalSettings.currentObject.addImagesToGallery($('#wpb-items-list').html(), $.select_images_from_lib);
                            } else {
                                parent.jQuery.wpb_composer.cloneSelectedImagesFromMediaTab($('#wpb-items-list').html(), $.select_images_from_lib);
                            }
                        }
                    });

                    $.initDragAndDrop();

                    $('.log').ajaxStart(function(){
                        $('#wpb_spinner').show();
                    }).ajaxComplete(function(e, xhr, settings) {
                            $('#wpb_spinner').hide();
                            if(settings.url=='async-upload.php') {
                                $('#media-items .media-item:visible').each(function(){
                                    $item  = $(this);
                                    $item.find('.toggle').remove();
                                    img_url = $item.find('img:first').attr('src');
                                    var media_id = $item.find('input[type=hidden]:first').attr('id');
                                    if(media_id) {
                                        media_id = media_id.replace(/[^\d]/g, '');
                                        if(typeof($.select_images_from_lib['media_' + media_id]) == 'undefined' || $.select_images_from_lib['media_' + media_id] == null) {
                                            $('<li class="wpb_media_block added" media_id="' + media_id + '" style=""><a href="#" class="wpb_media_block_link"><img src="' + img_url + '" alt=""></a><a href="#" class="icon-remove"></a></li>').appendTo('#wpb-items-list');
                                        }
                                        $.select_images_from_lib['media_' + media_id] = media_id;
                                    }
                                });
                                $('table.describe').remove();
                                $.initDragAndDrop();
                                $.updateLibraryBlock();
                            }
                        });

                });
            </script>
			<?php else: ?>
			<div id="wpb_selected_item" style="display:none;">
			</div>
            <script type="text/javascript">
                var modal_wpb = '<?php echo $this->get('modal_wpb'); ?>';
                jQuery(document).ready(function($){
					$('#wpb_composer_media_list').on('click', '.wpb_media_block_link', function(e){
						var $block = $(this).parent().clone();
						$block.appendTo('#wpb_selected_item');
						e.preventDefault();
						if( parent != undefined ) {
							$.select_images_from_lib['media_' + $(this).parent().attr('media_id')] = $(this).parent().attr('media_id');
                            if(modal_wpb==='true') {
                                parent.jQuery.wpbGlobalSettings.currentObject.addImagesToGallery($('#wpb_selected_item').html(), $.select_images_from_lib);
                            } else {
                                parent.jQuery.wpb_composer.cloneSelectedImagesFromMediaTab($('#wpb_selected_item').html(), $.select_images_from_lib);
                            }

                        }
					});
                    $('.log').ajaxStart(function(){
                        $('#wpb_spinner').show();
                    }).ajaxComplete(function(e, xhr, settings) {
                            $('#wpb_spinner').hide();
						if(settings.url=='async-upload.php') {
							$item = $('#media-items .media-item:visible').hide();
							img_url = $item.find('img:first').attr('src');
                            var media_id = $item.find('input[type=hidden]:first').attr('id');
                            media_id = media_id.replace(/[^\d]/g, '');
							$.select_images_from_lib['media_' + media_id] = media_id;
                            $('<li class="wpb_media_block added" media_id="' + media_id + '" style=""><a href="#" class="wpb_media_block_link"><img src="' + img_url + '" alt=""></a><a href="#" class="icon-remove"></a></li>').appendTo('#wpb_selected_item');
                            if( parent != undefined ) {
								$.select_images_from_lib['media_' + media_id] = media_id;
                                if(modal_wpb==='true') {
                                    parent.jQuery.wpbGlobalSettings.currentObject.addImagesToGallery($('#wpb_selected_item').html(), $.select_images_from_lib);
                                } else {
                                    parent.jQuery.wpb_composer.cloneSelectedImagesFromMediaTab($('#wpb_selected_item').html(), $.select_images_from_lib);
                                }
                            }
                            $('table.describe').remove();
						}
					});
				});
			</script>
            <?php endif; ?>
        </div>

    <?php
    }


    public function header() {

    }
}
