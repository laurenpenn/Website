/* =========================================================
 * settings.js v0.5.0
 * =========================================================
 * Copyright 2012 Wpbakery
 *
 * Visual composer shortcode settings object.
 * ========================================================= */

!function ($) {
    $.wpbShortCodeSettings = function ($wpb_shortcode_element, layout) {
        this.$element = $wpb_shortcode_element;
        this.layout = layout;
        this.$activeImageGallery = '';
        this.getFromServer();
    };
    $.wpbShortCodeSettings.prototype = {

        setSortableImage:function ($img_ul) {
            $img_ul.sortable({
                forcePlaceholderSize:true,
                placeholder:"widgets-placeholder-gallery",
                cursor:"move",
                items:"li",
                update:function () {
                    var img_ids = new Array();
                    $(this).find('.added img').each(function () {
                        img_ids.push($(this).attr("rel"));
                    });
                    $img_ul.closest('.edit_form_line').find('.gallery' +
                        '' +
                        '_widget_attached_images_ids').val(img_ids.join(','));
                }
            });
        },
        addImagesToGallery:function (html, $ids) {
            var $button = this.$activeImageGallery,
                $block = $button.closest('.edit_form_line'),
                hidden_ids =  $block.find('.gallery_widget_attached_images_ids'),
                $img_ul = $block.find('.gallery_widget_attached_images_list'),
                clear_button = $img_ul.next();

            $img_ul.html(html);

            clear_button.show();

            var hidden_ids_value = '';
            $img_ul.find('li').each(function () {
                hidden_ids_value += (hidden_ids_value.length > 0 ? ',' : '') + $(this).attr('media_id');
            });
            hidden_ids.val(hidden_ids_value);
            this.setSortableImage($img_ul);
            new $.wpbModal(this).switchTo('$settings');
        },
        initGalleries:function () { // Need refactor
            var that = this;
            /*
            $('.gallery_widget_add_images', this.$view).unbind('click').click(function (e) {
                that.$activeImageGallery = $(this);
                var $block = that.$activeImageGallery.closest('.edit_form_line');
                e.preventDefault();
                var selected_ids = $block.find('.gallery_widget_attached_images_ids').val(),
                    post_id = $('#post_ID').is('input') ? $('#post_ID').val() : '';

                // tb_show(i18nLocale.add_remove_picture, 'media-upload.php?type=image&post_id=' +  post_id +'&tab=composer_images&single_image=' + ($(this).attr('use-single')=='true' ? 'true' : 'false') + '&selected_ids=' + encodeURIComponent(selected_ids) + '&TB_iframe=true&height=343&width=800');
                var modal = new $.wpbModal(this);
                $.wpbGlobalSettings.currentObject = that;
                // TODO: Refactor this without iframe
                modal.showImageGallery('media-upload.php?type=image&modal_wpb=true&post_id=' + post_id + '&tab=composer_images&single_image=' + ($(this).attr('use-single') == 'true' ? 'true' : 'false') + '&selected_ids=' + encodeURIComponent(selected_ids) + '&body_class=black_velvet');
                return false;

            });
            */
            $('.gallery_widget_attached_images_list', this.$view).unbind('click.removeImage').on('click.removeImage', 'a.icon-remove', function(e){
                e.preventDefault();
                var $block = $(this).closest('.edit_form_line');
                $(this).parent().remove();
                var img_ids = new Array();
                $block.find('.added img').each(function () {
                    img_ids.push($(this).attr("rel"));
                });
                $block.find('.gallery_widget_attached_images_ids').val(img_ids.join(','));
            });
        },
        init:function () {
            var that = this;
            // Here goes all initializations of form objects.
            $('.wpb_save_edit_form', this.$view).unbind('click.wpbElementSettingsSubmit').bind('click.wpbElementSettingsSubmit', function (e) {
                e.preventDefault();
                that.save();
                var modal = new $.wpbModal($.wpbStage);
                modal.close(true);
            });
            $('#cancel-background-options', this.$view).unbind('click').click(function (e) {
                e.preventDefault();
                var modal = new $.wpbModal($.wpbStage);
                modal.close();
            });
            // setup dependencies
            $('[data-dependency]', this.$view).each(function () {
                var $this = $(this);
                $this.hide();
                var $element = $('[name=' + $this.attr('data-dependency') + ']:not(:hidden)');
                var callback_function = $this.attr('data-dependency-callback') != undefined ? $this.attr('data-dependency-callback') : false;
                $element.each(function () {
                    var $one_element = $(this);
                    if ($one_element.val().length > 0 && ( $one_element.is(':not(:checkbox)') || $one_element.is(':checked') )) {
                        if ($this.is('[data-dependency-not-empty=true]')) {
                            if (callback_function != false) window[callback_function]($one_element, $this);
                            $this.show();
                        } else if ($this.is('[data-dependency-value-' + $one_element.val() + '=' + $one_element.val() + ']')) {
                            if (callback_function != false)  window[callback_function]($one_element, $this);
                            $this.show();
                        }
                    }
                });

                $element.bind('change keyup', function () {

                    if ($(this).data('depended_objects') == undefined) {
                        $depended_objects = $('[data-dependency=' + $element.attr('name') + ']', that.$view);
                        $(this).data('depended_objects', $depended_objects);
                    } else {
                        $depended_objects = $(this).data('depended_objects');
                    }
                    if ($(this).is(':checkbox')) {
                        $depended_objects.filter('[data-dependency-value-' + $(this).val() + '=' + $(this).val() + ']').hide();
                        if (callback_function != false) window[callback_function]($(this), $depended_objects);
                    } else {
                        $depended_objects.hide();
                    }

                    if ($(this).val().length > 0 && ($(this).is(':not(:checkbox)') || $(this).is(':checked'))) {
                        $depended_objects.filter('[data-dependency-not-empty=true]').show();
                        $depended_objects.filter('[data-dependency-value-' + $(this).val() + '=' + $(this).val() + ']').show();
                        if (callback_function != false) window[callback_function]($(this), $depended_objects);
                    }
                });
            });
            //

            // $('.textarea_html', this.$view).each(function (index) {
            //     that.initTinyMce($(this));
            // });
            this.initGalleries();
            $('.gallery_widget_attached_images_list', this.$view).each(function (index) {
                that.setSortableImage($(this));
            });


            // Get callback function name
            var cb = this.$element.children(".wpb_vc_edit_callback");
            //
            if (cb.length == 1) {
                var fn = window[cb.attr("value")];
                if (typeof fn === 'function') {
                    var tmp_output = fn(element);
                }
            }

        },
        save:function () {
            var $element = this.$element;
            $('.wpb_vc_param_value', this.$view).each(function (index) {

                var element_to_update = $(this).attr("name"),
                    new_value = '';
                // Textfield - input
                if ($(this).hasClass("textfield")) {
                    new_value = $(this).val();
                }
                // Dropdown - select
                else if ($(this).hasClass("dropdown")) {
                    new_value = $(this).val(); // get selected element

                    var all_classes_ar = new Array(),
                        all_classes = '';
                    $(this).find('option').each(function () {
                        var val = $(this).attr('value');
                        all_classes_ar.push(val); //populate all posible dropdown values
                    });

                    all_classes = all_classes_ar.join(" "); // convert array to string

                    //element.removeClass(all_classes).addClass(new_value); // remove all possible class names and add only selected one
                    $element.children('.wpb_element_wrapper').removeClass(all_classes).addClass(new_value); // remove all possible class names and add only selected one
                }
                // WYSIWYG field
                else if ($(this).hasClass("textarea_html")) {
                    new_value = getTinyMceHtml($(this));
                }
                // Check boxes
                else if ($(this).hasClass("wpb-checkboxes")) {
                    var posstypes_arr = new Array();
                    $(this).closest('.edit_form_line').find('input').each(function (index) {
                        var self = $(this);
                        element_to_update = self.attr("name");
                        if (self.is(':checked')) {
                            posstypes_arr.push(self.attr("value"));
                        }
                    });
                    if (posstypes_arr.length > 0) {
                        new_value = posstypes_arr.join(',');
                    }
                }
                // Exploded textarea
                else if ($(this).hasClass("exploded_textarea")) {
                    new_value = $(this).val().replace(/\n/g, ",");
                }
                // Regular textarea
                else if ($(this).hasClass("textarea")) {
                    new_value = $(this).val();

                }
                else if ($(this).hasClass("textarea_raw_html")) {
                    new_value = $(this).val();
                    $element.find('[name=' + element_to_update + '_code]').val(base64_encode(rawurlencode(new_value)));
                    new_value = $("<div/>").text(new_value).html();
                }
                // Attach images
                else if ($(this).hasClass("attach_images")) {
                    new_value = $(this).val();
                    var $thumbnails = $element.find('[name=' + element_to_update + ']').next('.attachment-thumbnails');
                    var thumbnails_html = '';
                    $(this).parent().find('li.added').each(function(){
                        thumbnails_html += '<li><img src="' + $(this).find('img').attr('src') + '" alt=""></li>';
                    });
                    $thumbnails.html(thumbnails_html);
                    if($(this).parent().find('li.added').length) {
                        $thumbnails.removeClass('image-exists').next().addClass('image-exists');
                    } else {
                        $thumbnails.addClass('image-exists').next().removeClass('image-exists');
                    }
                }
                else if ($(this).hasClass("attach_image")) {
                    new_value = $(this).val();
                    /* KLUDGE: to change image */
                    var $thumbnail = $element.find('[name=' + element_to_update + ']').next('.attachment-thumbnail');
                    if( $(this).parent().find('li.added').length) {
                        $thumbnail.attr('src', $(this).parent().find('li.added img').attr('src')).show();
                        $thumbnail.next().addClass('image-exists').next().addClass('image-exists');
                    } else {
                        $thumbnail.attr('src', '').hide();
                        $thumbnail.next().removeClass('image-exists').next().removeClass('image-exists');
                    }
                }
                else {
                    new_value = $(this).val();
                }
                if($(this).data('js-function')!=undefined && typeof(window[$(this).data('js-function')])!== 'undefined') {
                    var fn = window[$(this).data('js-function')];
                    fn($element, this);
                }
                element_to_update = element_to_update.replace('wpb_tinymce_', '');
                if ($element.children('.wpb_element_wrapper').children('.' + element_to_update).is('div, h1,h2,h3,h4,h5,h6, span, i, b, strong, button')) {

                    //$element.find('.'+element_to_update).html(new_value);
                    $element.children('.wpb_element_wrapper').children('[name=' + element_to_update + ']').html(new_value);
                } else if($element.children('.wpb_element_wrapper').children('.' + element_to_update).is('img, iframe')) {
                    $element.children('.wpb_element_wrapper').children('[name=' + element_to_update + ']').attr('src', new_value);

                } else {

                    //$element.find('.'+element_to_update).val(new_value);
                    $element.children('.wpb_element_wrapper').children('[name=' + element_to_update + ']').val(new_value);
                }

                var $admin_label = $element.children('.wpb_element_wrapper').children('.admin_label_' + element_to_update);
                if($admin_label.length) {
                    $admin_label.html('<label>' + $admin_label.find('label').text() + '</label>: ' + new_value);
                    if(new_value!='')
                        $admin_label.removeClass('hidden-label');
                    else
                        $admin_label.addClass('hidden-label');
                }
            });

            // Get callback function name
            var cb = $element.children(".wpb_vc_save_callback");
            //
            if (cb.length == 1) {
                var fn = window[cb.attr("value")];
                if (typeof fn === 'function') {
                    var tmp_output = fn($element);
                }
            }
            $.wpb_stage.sizeRows();
            $.jsComposer.save_composer_html();
        },
        create:function (html) {
            this.$view = $(html);
        },
        getFromServer:function () {
            var that = this;
            $.ajax({
                type:'POST',
                url:$.wpbGlobalSettings.ajaxurl,
                data:{
                    action: 'wpb_show_edit_form',
                    element: this.$element.data('wpb_shortcode').shortcode,
                    shortcode: $.jsComposer.generateShortcodesFromHtml(this.$element, true)
                },
                dataType:'html',
                context:this.layout // Modal
            }).done(function (response) {
                    that.create(response);
                    this.addSettings(that.$view); // Add Element to stage or layout
                    that.init();
                });
        }
    };

}(window.jQuery);

