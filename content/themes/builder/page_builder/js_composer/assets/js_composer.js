;
(function ($) {
    $.wpb_composer = {
        isMainContainerEmpty:function () {
            if (!jQuery('.wpb_main_sortable > div').length) {
                $('.metabox-composer-content').addClass('empty-composer');
            } else {
                $('.metabox-composer-content').removeClass('empty-composer');
            }
        },
        cloneSelectedImagesFromMediaTab:function (html, $ids) {
            var $button = $('.wpb_current_active_media_button_' + $('body').data('gallery_image_button_ident')).removeClass('.wpb_current_active_media_button_' + $('body').data('gallery_image_button_ident'));

            var attached_img_div = $button.next(),
                site_img_div = $button.next().next();

            var hidden_ids = attached_img_div.prev().prev(),
                img_ul = attached_img_div.find('.gallery_widget_attached_images_list'),
                clear_button = img_ul.next();

            img_ul.html(html);
            clear_button.show();
            var hidden_ids_value = '';
            img_ul.find('li').each(function () {
                hidden_ids_value += (hidden_ids_value.length > 0 ? ',' : '') + $(this).attr('media_id');
            });

            hidden_ids.val(hidden_ids_value);
            attachedImgSortable(img_ul);
            tb_remove();

        },
        galleryImagesControls:function () {
            return false;
            $('.gallery_widget_add_images').live("click", function (e) {
                var ident = new Date().getTime();
                $(this).addClass('wpb_current_active_media_button_' + ident);
                $('body').data('gallery_image_button_ident', ident);

                e.preventDefault();
                var selected_ids = $(this).parent().find('.gallery_widget_attached_images_ids').val(),
                    post_id = $('#post_ID').is('input') ? $('#post_ID').val() : '';

                tb_show(i18nLocale.add_remove_picture, 'media-upload.php?type=image&post_id=' + post_id + '&tab=composer_images&single_image=' + ($(this).attr('use-single') == 'true' ? 'true' : 'false') + '&selected_ids=' + encodeURIComponent(selected_ids) + '&TB_iframe=true&height=343&width=800');

                return false;

                var attached_img_div = $(this).next(),
                    site_img_div = $(this).next().next();

                if (attached_img_div.css('display') == 'block') {
                    $(this).addClass('button-primary').text(i18nLocale.finish_adding_text);
                    attached_img_div.hide();
                    site_img_div.show();

                    // hideEditFormSaveButton();
                } else {
                    $(this).removeClass('button-primary').text($(this).attr('use-single') == 'true' ? i18nLocale.add_image : i18nLocale.add_images);
                    //
                    attached_img_div.show();        // $this->addAction('admin_head', 'header');.show();
                    site_img_div.hide();

                    this.cloneSelectedImages(site_img_div, attached_img_div);

                    // showEditFormSaveButton();
                }
            });

            $('.gallery_widget_img_select li').live("click", function (e) {
                $(this).toggleClass('added');

                var hidden_ids = $(this).parent().parent().prev().prev().prev(),
                    ids_array = (hidden_ids.val().length > 0) ? hidden_ids.val().split(",") : new Array(),
                    img_rel = $(this).find("img").attr("rel"),
                    id_pos = $.inArray(img_rel, ids_array);

                /* if not found */
                if (id_pos == -1) {
                    ids_array.push(img_rel);
                }
                else {
                    ids_array.splice(id_pos, 1);
                }

                hidden_ids.val(ids_array.join(","));

            });
        },
        initializeFormEditing:function (element) {

            // setup dependencies

            $('#visual_composer_edit_form').find('[data-dependency]').each(function () {

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
                        $depended_objects = $('#visual_composer_edit_form [data-dependency=' + $element.attr('name') + ']');
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
            $('#visual_composer_edit_form .wp-editor-wrap .textarea_html').each(function (index) {
                initTinyMce($(this));
            });

            $('#visual_composer_edit_form .gallery_widget_attached_images_list').each(function (index) {
                attachedImgSortable($(this));
            });


            // Get callback function name
            var cb = element.children(".wpb_vc_edit_callback");
            //
            if (cb.length == 1) {
                var fn = window[cb.attr("value")];
                if (typeof fn === 'function') {
                    var tmp_output = fn(element);
                }
            }

            $('.wpb_save_edit_form').unbind('click').click(function (e) {
                e.preventDefault();
                saveFormEditing(element);//(element);

            });

            $('#cancel-background-options').unbind('click').click(function (e) {
                e.preventDefault();
                $('.wpb_main_sortable, #wpb_visual_composer-elements, .wpb_switch-to-composer').show();
                $('.visual_composer_tinymce').each(function () {
                    tinyMCE.execCommand("mceRemoveControl", true, $(this).attr('id'));
                });

                $('#visual_composer_edit_form').html('').hide();
                $('body, html').scrollTop(current_scroll_pos);
                $("#publish").show();

            });

        },
        onDragPlaceholder:function () {
            return $('<div id="drag_placeholder"></div>');
        },
        addLastClass:function (dom_tree) {
            var total_width, width, next_width;
            total_width = 0;
            width = 0;
            next_width = 0;
            $dom_tree = $(dom_tree);

            $dom_tree.children(".wpb_sortable").removeClass("wpb_first wpb_last");
            if ($dom_tree.hasClass("wpb_main_sortable")) {
                $dom_tree.find(".wpb_sortable .wpb_sortable").removeClass("sortable_1st_level");
                $dom_tree.children(".wpb_sortable").addClass("sortable_1st_level");
                $dom_tree.children(".wpb_sortable:eq(0)").addClass("wpb_first");
                $dom_tree.children(".wpb_sortable:last").addClass("wpb_last");
            }

            if ($dom_tree.hasClass("wpb_column_container")) {
                $dom_tree.children(".wpb_sortable:eq(0)").addClass("wpb_first");
                $dom_tree.children(".wpb_sortable:last").addClass("wpb_last");
            }

            $dom_tree.children(".wpb_sortable").each(function (index) {

                var cur_el = $(this);

                // Width of current element
                if (cur_el.hasClass("span12")
                    || cur_el.hasClass("wpb_widget")) {
                    width = 12;
                }
                else if (cur_el.hasClass("span10")) {
                    width = 10;
                }
                else if (cur_el.hasClass("span9")) {
                    width = 9;
                }
                else if (cur_el.hasClass("span8")) {
                    width = 8;
                }
                else if (cur_el.hasClass("span6")) {
                    width = 6;
                }
                else if (cur_el.hasClass("span4")) {
                    width = 4;
                }
                else if (cur_el.hasClass("span3")) {
                    width = 3;
                }
                else if (cur_el.hasClass("span2")) {
                    width = 2;
                }
                total_width += width;// + next_width;

                //console.log(next_width+" "+total_width);

                if (total_width > 10 && total_width <= 12) {
                    cur_el.addClass("wpb_last");
                    cur_el.next('.wpb_sortable').addClass("wpb_first");
                    total_width = 0;
                }
                if (total_width > 12) {
                    cur_el.addClass('wpb_first');
                    cur_el.prev('.wpb_sortable').addClass("wpb_last");
                    total_width = width;
                }

                if (cur_el.hasClass('wpb_vc_column') || cur_el.hasClass('wpb_vc_tabs') || cur_el.hasClass('wpb_vc_tour') || cur_el.hasClass('wpb_vc_accordion')) {

                    if (cur_el.find('.wpb_element_wrapper .wpb_column_container').length > 0) {
                        cur_el.removeClass('empty_column');
                        cur_el.addClass('not_empty_column');
                        //addLastClass(cur_el.find('.wpb_element_wrapper .wpb_column_container'));
                        cur_el.find('.wpb_element_wrapper .wpb_column_container').each(function (index) {
                            $.wpb_composer.addLastClass($(this)); // Seems it does nothing

                            if ($(this).find('div:not(.container-helper)').length == 0) {
                                $(this).addClass('empty_column');
                                $(this).html($('#container-helper-block').html());
                            } else {
                                $(this).removeClass('empty_column');
                            }
                        });
                    }
                    else if (cur_el.find('.wpb_element_wrapper .wpb_column_container').length == 0) {
                        cur_el.removeClass('not_empty_column');
                        cur_el.addClass('empty_column');
                    }
                    else {
                        cur_el.removeClass('empty_column not_empty_column');
                    }
                }

                //if ( total_width == 0 ) {
                //	cur_el.next('.wpb_sortable').addClass("wpb_first");
                //}

                //total_width += width;

                /*
                 // If total_width > 0.95 and <= 1 then add 'last' class name to the column
                 if (total_width >= 0.95 && total_width <= 1) {
                 cur_el.addClass("last");
                 cur_el.next('.column').addClass("first");
                 total_width = 0;
                 }
                 // If total_width > 1 then add 'first' class name to the current column and
                 // 'last' to the previous. 'first' class name is needed to clear floats
                 if (total_width > 1) {
                 cur_el.addClass("first");
                 cur_el.prev(".column").addClass("last");
                 total_width = width;
                 }

                 // If current column have column elements inside, then go throw them too
                 //if (cur_el.children(".column").length > 1) {
                 if (cur_el.hasClass('wpb_vc_column')) {
                 if (cur_el.children(".column").length > 0) {
                 cur_el.removeClass('empty_column');
                 cur_el.addClass('not_empty_column');
                 jQuery.wpb_composer.addLastClass(cur_el);
                 }
                 else if (cur_el.children(".column").length == 0) {
                 cur_el.removeClass('not_empty_column');
                 cur_el.addClass('empty_column');
                 }
                 else {
                 cur_el.removeClass('empty_column not_empty_column');
                 }
                 }
                 */
            });
            //$(dom_tree).children(".column:first").addClass("first");
            //$(dom_tree).children(".column:last").addClass("last");
        }, // endjQuery.wpb_composer.addLastClass()
        save_composer_html:function () {
            this.addLastClass($(".wpb_main_sortable"));

            var shortcodes = generateShortcodesFromHtml($(".wpb_main_sortable"));
            //console.log(shortcodes);

            //console.log(tinyMCE.ed.isHidden());

            //if ( tinyMCE.activeEditor == null ) {

            //setActive(wpb_def_wp_editor.editorId);
            tinyMCE.get('content').setContent(shortcodes, {format:'html'});
            /*
             if ( isTinyMceActive() != true ) {
             //tinyMCE.activeEditor.setContent(shortcodes, {format : 'html'});
             $('#content').val(shortcodes);
             } else {
             tinyMCE.get('content').setContent(shortcodes, {format : 'html'});
             }
             */


            /*var val = $.trim($(".wpb_main_sortable").html());
             $("#visual_composer_html_code_holder").val(val);

             var shortcodes = generateShortcodesFromHtml($(".wpb_main_sortable"));
             $("#visual_composer_code_holder").val(shortcodes);

             var tiny_val = switchEditors.wpautop(shortcodes);

             //[REVISE] Should determine what mode is currently on Visual/HTML
             tinyMCE.get('content').setContent(tiny_val, {format : 'raw'});

             /*try {
             tinyMCE.get('content').setContent(tiny_val, {format : 'raw'});
             }
             catch (err) {
             switchEditors.go('content', 'html');
             $('#content').val(shortcodes);
             }*/
        }
    };
    $(document).ready(function () {
        /*** Gallery Controls / Site attached images ***/
        $.wpb_composer.galleryImagesControls();
        /* Actions for gallery images handling */
        /*jQuery('.gallery_widget_attached_images_list').each(function(index) {
         attachedImgSortable(jQuery(this));
         });*/
    });

})(jQuery);
function open_elements_dropdown() {
    jQuery('.wpb_content_elements:first').trigger('click');
}

function open_layouts_dropdown() {
    jQuery('.wpb_popular_layouts:first').trigger('click');
}


function hideEditFormSaveButton() {
    jQuery('#visual_composer_edit_form .edit_form_actions').hide();
}
function showEditFormSaveButton() {
    jQuery('#visual_composer_edit_form .edit_form_actions').show();
}

/* Updates ids order in hidden input field, on drag-n-drop reorder */
function updateSelectedImagesOrderIds(img_ul) {
    var img_ids = new Array();

    jQuery(img_ul).find('.added img').each(function () {
        img_ids.push(jQuery(this).attr("rel"));
    });

    jQuery(img_ul).parent().prev().prev().val(img_ids.join(','));
}

/* Takes ids from hidden field and clone li's */
function cloneSelectedImages(site_img_div, attached_img_div) {
    var hidden_ids = jQuery(attached_img_div).prev().prev(),
        ids_array = (hidden_ids.val().length > 0) ? hidden_ids.val().split(",") : new Array(),
        img_ul = attached_img_div.find('.gallery_widget_attached_images_list');

    img_ul.html('');

    jQuery.each(ids_array, function (index, value) {
        jQuery(site_img_div).find('img[rel=' + value + ']').parent().clone().appendTo(img_ul);
    });
    attachedImgSortable(img_ul);
}

function attachedImgSortable(img_ul) {
    jQuery(img_ul).sortable({
        forcePlaceholderSize:true,
        placeholder:"widgets-placeholder",
        cursor:"move",
        items:"li",
        update:function () {
            updateSelectedImagesOrderIds(img_ul);
        }
    });
}


/* Get content from tinyMCE editor and convert it to Visual
 Composer
 ---------------------------------------------------------- */
function wpb_shortcodesToVisualEditor() {
    var content = wpb_getContentFromTinyMCE();
    jQuery('.wpb_main_sortable').html('');
    new jQuery.wpbShortcode().fromEditor(content, jQuery.wpb_stage);
    return;
    var load_img = '<img src="' + jQuery('img.ajax-loading').attr('src') + '" />';
    jQuery('.wpb_main_sortable').html(load_img + ' ' + jQuery('#wpb_vc_loading').val());

    var data = {
        action:'wpb_shortcodes_to_visualComposer',
        content:content
    };

    jQuery.post(ajaxurl, data, function (response) {
        jQuery('.wpb_main_sortable').html(response);
        jQuery.wpb_composer.isMainContainerEmpty();
        //
        //console.log(response);
        jQuery.wpb_composer.addLastClass(jQuery(".wpb_main_sortable"));
        initDroppable();

        //Fire INIT callback if it is defined
        jQuery('.wpb_main_sortable').find(".wpb_vc_init_callback").each(function (index) {
            var fn = window[jQuery(this).attr("value")];
            if (typeof fn === 'function') {
                fn(jQuery(this).closest('.wpb_sortable'));
            }
        });
    });
}


/* This makes layout elements droppable, so user can drag
 them from on column to another and sort them (re-order)
 within the current column
 ---------------------------------------------------------- */
function initDroppable() {
    jQuery('.wpb_sortable_container').sortable({
        forcePlaceholderSize:true,
        connectWith:".wpb_sortable_container",
        placeholder:"widgets-placeholder",
        scrollSensitivity: 100,
        // cursorAt: { left: 10, top : 20 },
        cursor:"move",
        items:"div.wpb_sortable", //wpb_sortablee
        distance:10,//0.5,
        start:function () {
            jQuery('#visual_composer_content').addClass('sorting-started');
        },
        stop:function (event, ui) {
            jQuery('#visual_composer_content').removeClass('sorting-started');
        },
        update:function () {
            jQuery.wpb_composer.save_composer_html();
        },
        over:function (event, ui) {
            ui.placeholder.css({maxWidth:ui.placeholder.parent().width()});
            ui.placeholder.removeClass('hidden-placeholder');
            if (ui.item.hasClass('not-column-inherit') && ui.placeholder.parent().hasClass('not-column-inherit')) {
                ui.placeholder.addClass('hidden-placeholder');
            }

        },
        beforeStop:function (event, ui) {
            if (ui.item.hasClass('not-column-inherit') && ui.placeholder.parent().hasClass('not-column-inherit')) {
                return false;
            }
        }
    });


    /*
     jQuery('.wpb_column_container').sortable({
     connectWith: ".wpb_column_container, .wpb_main_sortable",
     //connectWith: ".sortable_1st_level.wpb_vc_column",
     forcePlaceholderSize: true,
     placeholder: "widgets-placeholder",
     // cursorAt: { left: 10, top : 20 },
     cursor: "move",
     items: "div.wpb_sortable:not(.wpb_vc_column)",
     update: function() { jQuery.wpb_composer.save_composer_html(); },
     });

     jQuery('.wpb_main_sortable').droppable({
     greedy: true,
     accept: ".dropable_el, .dropable_column",
     hoverClass: "wpb_ui-state-active",
     drop: function( event, ui ) {
     if(ui.draggable.is('#wpb-add-new-element')) {

     jQuery.wpb_composer.controller.showElementsList();
     } else {
     getElementMarkup(jQuery(this), ui.draggable, "addLastClass");
     }

     }
     });

     jQuery('#wpb-add-new-element').click(function(){
     jQuery('#wpb-elements-list-modal .wpb-elements-list').show(); // TODO: Move modal to new file
     jQuery('#wpb-elements-list-modal').modal('show');
     });
     */
    jQuery('.wpb_column_container').droppable({
        greedy:true,
        accept:function (dropable_el) {
            if (dropable_el.hasClass('dropable_el') && jQuery(this).hasClass('ui-droppable') && dropable_el.hasClass('not_dropable_in_third_level_nav')) {
                return false;
            } else if (dropable_el.hasClass('dropable_el') == true) {
                return true;
            }

            //".dropable_el",
        },
        hoverClass:"wpb_ui-state-active",
        over:function (event, ui) {
            jQuery(this).parent().addClass("wpb_ui-state-active");
        },
        out:function (event, ui) {
            jQuery(this).parent().removeClass("wpb_ui-state-active");
        },
        drop:function (event, ui) {
            //console.log(jQuery(this));
            jQuery(this).parent().removeClass("wpb_ui-state-active");
            getElementMarkup(jQuery(this), ui.draggable, "addLastClass");
        }
    });
}

/* Custom Callbacks
 ---------------------------------------------------------- */
/* Tabs Callbacks
 ---------------------------------------------------------- */

function wpb_init_tab_controls($tabs, row) {
    var $ = jQuery;
    $(".column_delete", row.$elementControls).unbind('click').click(function (e) {
        e.preventDefault();
        var answer = confirm(i18nLocale.press_ok_to_delete_section);
        if (answer) {
            $parent = $(this).closest(".wpb_sortable");
            $prev = $tabs.find(".tabs_controls [href=#" +  $parent.attr('id') + "]").parent().prev('li');
            $next = $tabs.find(".tabs_controls [href=#" +  $parent.attr('id') + "]").parent().next('li');
            $tabs.find(".tabs_controls [href=#" +  $parent.attr('id') + "]").parent().remove();
            $parent.remove();
            $.wpb_stage.checkIsEmpty();
            $.wpb_stage.sizeRows();
            if($prev.is('li')) {
                $prev.find('a').trigger('click');
            } else if($next.is('li')) {
                $next.find('a').trigger('click');
            }
            $.jsComposer.save_composer_html();
        }
    });
    $(".column_clone", row.$elementControls).unbind('click').click(function (e) {
        e.preventDefault();
        var wpb_clone = row.cloneShortCode(),
            tabs_count = $tabs.tabs("length"),
            tab_id = (+new Date() + '-' + tabs_count + '-' + Math.floor(Math.random() * 11));
        wpb_clone.$element.attr('id', 'tab-' + tab_id);
        $tabs.tabs("add", "#tab-" + tab_id, row.$element.find('> .wpb_element_wrapper > .wpb_vc_param_value[name=title]').val(), $('[aria-controls=tab-' + row.$element.find('> .wpb_element_wrapper >[name=tab_id]').val() + ']').index());
        wpb_clone.$element.find('> .wpb_element_wrapper >[name=tab_id]').val(tab_id);
        wpb_clone.init();
        wpb_clone.initColumn();
        wpb_clone.column._setSortable();
        wpb_init_tab_controls($tabs, wpb_clone);
        $.wpb_stage.sizeRows();
        $.jsComposer.save_composer_html();
    });
}

function wpbTabsInitCallBack(element) {
    element.find('.wpb_tabs_holder').not('.wpb_initialized').each(function (index) {

        jQuery(this).addClass('wpb_initialized');
        //var tab_counter = 4;
        //
        var $tabs,
            new_tab_button_id = (+new Date() + '-' + Math.floor(Math.random() * 11)),
            that = this;
            // edit_btn = jQuery(this).closest('.wpb_element_wrapper').find('.edit_tab'),
            // delete_btn = jQuery(this).closest('.wpb_element_wrapper').find('.delete_tab');
        //
        if(!jQuery('.new_element_button', jQuery(this)).length) {
            jQuery(this).append('<div id="new-tab-' + new_tab_button_id + '" class="new_element_button"></div>');
            jQuery(this).find(".tabs_controls").append('<li class="add_tab_block"><a href="#new-tab-'+ new_tab_button_id +'" class="add_tab" title="' + i18nLocale.add_tab +'"></a></li>');
        }
        $tabs = jQuery(this).tabs({
            panelTemplate: element.find('.wpb_template').html(),
            add:function (event, ui) {
                var tabs_count = jQuery(this).tabs("length") - 2;
                jQuery(this).tabs("select", tabs_count);
                //
            },
            select: function(event, ui) {
                if(jQuery(ui.tab).hasClass('add_tab')) {
                    var $ = jQuery,
                        $tabs = $(this),
                        tab_title = ( $(this).closest('.wpb_sortable').hasClass('wpb_vc_tour')) ? i18nLocale.slide : i18nLocale.tab,
                        tabs_count = $(this).tabs("length"),
                        tab_id = (+new Date() + '-' + tabs_count + '-' + Math.floor(Math.random() * 11));
                    $tabs.tabs("add", "#tab-" + tab_id, tab_title, $tabs.tabs("length") - 1);
                    $tabs.find('.wpb_vc_tab:last .wpb_vc_param_value[name=tab_id]').val(tab_id);
                    var row = new $.wpbShortcode($tabs.find('.wpb_vc_tab:last'), '1/1', $.wpb_stage);

                    row.init();
                    row.initColumn();
                    row.column._setSortable();
                    wpb_init_tab_controls($tabs, row);

                    save_composer_html();
                    return false;
                }
                return true;
            }
        });
        var sort_axis = ( jQuery(this).closest('.wpb_sortable').hasClass('wpb_vc_tour')) ? 'y' : 'x';

        $tabs.find(".ui-tabs-nav").sortable({
            axis:sort_axis,
            stop:function (event, ui) {
                $tabs.find('ul li').each(function (index) {
                    var href = jQuery(this).find('a').attr('href').replace("#", "");
                    $tabs.find('#' + href).appendTo($tabs);
                });
                //
                save_composer_html();
            }
        });

        jQuery(this).find('.wpb_vc_tab').each(function(){
            var $ = jQuery,
                row = new $.wpbShortcode($(this), '1/1', $.wpb_stage);
            row.init();
            row.initColumn();
            row.column._setSortable();
            wpb_init_tab_controls($tabs, row);
        });
    });

}

function wpbTabsGenerateShortcodeCallBack(current_top_level, inner_element_count) {
    var tab_title = current_top_level.find(".ui-tabs-nav li:eq(" + inner_element_count + ") a").text();
    output = '[vc_tab title="' + tab_title + '" tab_id="' + (+new Date() + '-' + Math.floor(Math.random() * 11)) + '-' + inner_element_count + '"] %inner_shortcodes [/vc_tab]';
    return output;
}

/* Accordion Callback
 ---------------------------------------------------------- */
function wpb_init_accordion_tab_controls($tabs, row) {
    var $ = jQuery;
    $(".column_delete", row.$elementControls).unbind('click').click(function (e) {
        e.preventDefault();
        var answer = confirm(i18nLocale.press_ok_to_delete_section);
        if (answer) {
            $parent = $(this).closest(".group");
            $parent.remove();
            $.wpb_stage.checkIsEmpty();
            $.wpb_stage.sizeRows();
            $.jsComposer.save_composer_html();
        }
    });
    $(".column_clone", row.$elementControls).unbind('click').click(function (e) {
        e.preventDefault();
        var $group = row.$element.closest('.group').clone(false);
        $group.insertAfter(row.$element.closest('.group'));
        $group.find('h3').removeClass('ui-accordion-header-active ui-state-active');
        var new_row = new $.wpbShortcode($group.find('[data-element_type=vc_accordion_tab]'), '1/1', $.wpb_stage);
        new_row.init();
        new_row.initColumn();
        new_row.column._setSortable();
        wpb_init_accordion_tab_controls($tabs, new_row);
        save_composer_html();
        $.wpb_stage.sizeRows();
        $tabs.accordion("destroy")
            .accordion({
                header:"h3",
                autoHeight:false
            }).sortable({
                axis:"y",
                handle:"h3",
                stop:function (event, ui) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.children("h3").triggerHandler("focusout");
                    //
                    save_composer_html();
                }
            });
    });
}

function wpbAccordionInitCallBack(element) {
    element.find('.wpb_accordion_holder').not('.wpb_initialized').each(function (index) {
        jQuery(this).addClass('wpb_initialized');
        //var tab_counter = 4;
        //
        var $tabs,
            that = this,
            $add_btn = jQuery(this).closest('.wpb_element_wrapper').find('.add_tab'),
            // edit_btn = jQuery(this).closest('.wpb_element_wrapper').find('.edit_tab'),
            // delete_btn = jQuery(this).closest('.wpb_element_wrapper').find('.delete_tab');
        //
        $tabs = jQuery(that).accordion({
            header:"h3",
            autoHeight:false
        }).sortable({
                axis:"y",
                handle:"h3",
                stop:function (event, ui) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.prev().triggerHandler("focusout");
                    // so
                    save_composer_html();
                }
            });
        jQuery(this).find('.wpb_vc_accordion_tab').each(function(){
            var $ = jQuery,
                row = new $.wpbShortcode($(this), '1/1', $.wpb_stage);
            row.init();
            row.initColumn();
            row.column._setSortable();
            wpb_init_accordion_tab_controls($tabs, row);
            // save_composer_html();
        });
        $add_btn.unbind('click').click(function(e){
            var $ = jQuery,
                row;
            e.preventDefault();
            $tabs.append($tabs.find('.wpb_template').html());
            $tabs.accordion('destroy').accordion({
                    header:"h3",
                    autoHeight:false
                }).sortable({
                    axis:"y",
                    handle:"h3",
                    stop:function (event, ui) {
                        // IE doesn't register the blur when sorting
                        // so trigger focusout handlers to remove .ui-state-focus
                        ui.item.children("h3").triggerHandler("focusout");
                        //
                        save_composer_html();
                    }
                });
            row = new $.wpbShortcode( $tabs.find('.wpb_vc_accordion_tab:last'), '1/1', $.wpb_stage);
            row.init();
            row.initColumn();
            row.column._setSortable();
            wpb_init_accordion_tab_controls($tabs, row);
            save_composer_html();
        });
        /*
        delete_btn.click(function (e) {
            e.preventDefault();

            var tab_name = $tabs.find('h3.ui-state-active a').text();

            var answer = confirm(i18nLocale.press_ok_delete_section.replace('{tab_name}', tab_name));
            if (answer) {
                $tabs.find('h3.ui-state-active a').closest('.group').remove();
                //
                save_composer_html();
            }
        });

        add_btn.click(function (e) {
            e.preventDefault();
            var tab_title = i18nLocale.section_default_title,
                section_template = '<div class="group"><h3><a href="#">' + i18nLocale.section_default_title + '</a></h3><div class="row-fluid wpb_column_container wpb_sortable_container not-column-inherit"></div></div>';
            $tabs.append(section_template);
            $tabs.accordion("destroy")
                .accordion({
                    header:"> div > h3",
                    autoHeight:false
                })
                .sortable({
                    axis:"y",
                    handle:"h3",
                    stop:function (event, ui) {
                        // IE doesn't register the blur when sorting
                        // so trigger focusout handlers to remove .ui-state-focus
                        ui.item.children("h3").triggerHandler("focusout");
                        //
                        save_composer_html();
                    }
                });
            new jQuery.wpbLayout($tabs.find('.wpb_sortable_container:last'));
            //$tabs.tabs( "add", "#tabs-" + tabs_count, tab_title );
            //tab_counter++;
            //
            // initDroppable();
            save_composer_html();
        });

        edit_btn.click(function () {
            var tab_name = $tabs.find('h3.ui-state-active a').text();

            var tab_title = prompt(i18nLocale.please_enter_section_title, tab_name);
            if (tab_title != null && tab_title != "") {
                $tabs.find('h3.ui-state-active a').text(tab_title);
                //
                save_composer_html();
            }
            return false;
        });
        */

    });
    // initDroppable();
}

function wpbAccordionGenerateShortcodeCallBack(current_top_level, inner_element_count) {
    var tab_title = current_top_level.find(".group:eq(" + inner_element_count + ") h3").text();
    output = '[vc_accordion_tab title="' + tab_title + '"] %inner_shortcodes [/vc_accordion_tab]';
    return output;
}

/* Message box Callbacks
 ---------------------------------------------------------- */
function wpbMessageInitCallBack(element) {
    var el = element.find('.wpb_vc_param_value.color');
    var class_to_set = el.val();
    el.closest('.wpb_element_wrapper').addClass(class_to_set);
}

/* Text Separator Callbacks
 ---------------------------------------------------------- */
function wpbTextSeparatorInitCallBack(element) {
    var el = element.find('.wpb_vc_param_value.title_align');
    var class_to_set = el.val();
    el.closest('.wpb_element_wrapper').addClass(class_to_set);
}

/* Call to action Callbacks
 ---------------------------------------------------------- */
function wpbCallToActionInitCallBack(element) {
    var el = element.find('.wpb_vc_param_value.position');
    var class_to_set = el.val();
    el.closest('.wpb_element_wrapper').addClass(class_to_set);
}
function wpbCallToActionSaveCallBack(element) {
    var el_class = element.find('.wpb_vc_param_value.color').val() + " " + element.find('.wpb_vc_param_value.icon').val();
    //
    element.find('.wpb_element_wrapper').removeClass(el_class);
}

/* Button Callbacks
 ---------------------------------------------------------- */
function wpbButtonInitCallBack(element) {
    var el_class = element.find('.wpb_vc_param_value.color').val() + ' ' + element.find('.wpb_vc_param_value.size').val() + ' ' + element.find('.wpb_vc_param_value.icon').val();
    //
    element.find('button.title').attr({ "class":"wpb_vc_param_value title textfield wpb_button " + el_class });

    var icon = element.find('.wpb_vc_param_value.icon').val();
    if (icon != 'none' && element.find('button i.icon').length == 0) {
        element.find('button.title').append(' <i class="icon"></i>');
    }
}

function wpbButtonSaveCallBack(element) {
    var el_class = element.find('.wpb_vc_param_value.color').val() + ' ' + element.find('.wpb_vc_param_value.size').val() + ' ' + element.find('.wpb_vc_param_value.icon').val();
    //
    element.find('.wpb_element_wrapper').removeClass(el_class);
    element.find('button.title').attr({ "class":"wpb_vc_param_value title textfield wpb_button " + el_class });

    var icon = element.find('.wpb_vc_param_value.icon').val();
    if (icon != 'none' && element.find('button i.icon').length == 0) {
        element.find('button.title').append(' <i class="icon"></i>');
    } else {
        element.find('button.title i.icon').remove();
    }
}

/**
 * Taxomonies filter
 *
 * Show or hide taxomonies depending on selected post types

 * @param $element - post type checkbox object
 * @param $object -
 */
function wpb_grid_post_types_for_taxomonies_handler($element, $object) {

    var $labels = $object.find('label[data-post-type]');
    $labels.hide();

    jQuery('.grid_posttypes:checkbox').change(function () {
        if (jQuery(this).is(':checked')) {
            $labels.filter('[data-post-type=' + jQuery(this).val() + ']').show();
        } else {
            $labels.filter('[data-post-type=' + jQuery(this).val() + ']').hide();
        }
    }).each(function () {
            if (jQuery(this).is(':checked')) $labels.filter('[data-post-type=' + jQuery(this).val() + ']').show();
        });
}


