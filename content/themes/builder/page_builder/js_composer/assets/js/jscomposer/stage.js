/* =========================================================
 * stage.js v0.5.0
 * =========================================================
 * Copyright 2012 Wpbakery
 *
 * Visual composer stage object. Helps to create and edit
 * shortcodes inside administration panel of wordpress edit
 * post page.
 * Main container and layouts used to work with.
 * ========================================================= */

!function ($) {
    // Global settings for options like ajaxurl, version and so on
    $.wpbGlobalSettings = {
        ajaxurl:window.ajaxurl,
        version:'0.1a',
        post_id:$('#post_ID').val()
    };
    // Logging to console

    $.fn.log = function (msg) {
        if (typeof(window.console) !== 'undefined' && window.console.log) window.console.log("%s: %o", msg, this);
        return this;
    };

    $.log = function (text) {
        if (typeof(window.console) !== 'undefined' && window.console.log) window.console.log(text);
    };

    $.wpbStage = function () {
        this.$view = $('#wpb_visual_composer');
        this.$postView = $('#postdivrich');
        this.accessPolicy = $('.wpb_js_composer_group_access_show_rule').val();
        this.$contentView = $('#visual_composer_content');
        this.$navigationView = $('#wpb_visual_composer-elements');
        this.$vcStatus = $('#wpb_vc_js_status');
        this.init();
    };

    $.wpbStage.prototype = {
        is_stage: true,
        way_to_add: 'append',
        /** Initialization methods */
        // {{
        init:function () {
            var that = this;
            if (this.$view.is('div')) {
                $(document).ready(function () {
                    that.initSwitchButton();
                    that.initNavigation();
                    that.initContent();
                    that.initHelperText();
                });

                $(window).load(function () {
                    that.showOnLoad();
                });
            }
        },
        initHelperText:function () {
            var that = this;
            $('#wpb-empty-blocks .add-text-block-to-content').unbind('click.addTextBlock').bind('click.addTextBlock', function(e){
                e.preventDefault();
                var shortcode = new $.wpbShortcode('vc_column_text', '', that);
            });
            $('#wpb-empty-blocks .add-element-to-layout').unbind('click.addNewElementStage').bind('click.addNewElementStage', function(e){
                e.preventDefault();
                var modal = new $.wpbModal(that);
                modal.showList();
            });
        },
        showOnLoad:function () {
            if ((this.$vcStatus.val() == 'true' && $('#wp-content-wrap').hasClass('tmce-active')) || this.accessPolicy == 'only') {
                this.show();
            }
        },
        initSwitchButton:function () {
            var that = this;
            if (this.$switchButton !== undefined || this.accessPolicy == 'only' || this.accessPolicy == 'no') return;
            this.$switchButton = $('<a class="wpb_switch-to-composer button-primary" href="#">' + window.i18nLocale.main_button_title + '</a>').insertAfter('div#titlediv').wrap('<p class="composer-switch" />');
            this.$switchButton.unbind('click').click(function (e) {
                e.preventDefault();
                if (that.$postView.is(':visible')) {
                    that.way_to_add = 'append';
                    that.show();
                } else {
                    that.hide();
                }
            });
        },
        initNavigation:function () {
            var that = this;
            $('#wpb-add-new-element', this.$navigationView).unbind('click').click(function () {
                var modal = new $.wpbModal(that);
                modal.showList();
            });
            $('.dropable_el,.dropable_row', this.$navigationView).draggable({
                helper:function () {
                    return $('<div id="drag_placeholder"></div>').appendTo('body')
                },
                zIndex:99999,
                // cursorAt: { left: 10, top : 20 },
                cursor:"move",
                // appendTo: "body",
                revert:"invalid",
                start:function (event, ui) {
                    $("#drag_placeholder").addClass("column_placeholder").html(i18nLocale.drag_drop_me_in_column);
                }
            });
            this.presets = new $.wpbTemplates($('.wpb_templates_ul', this.$navigationView));
            /* Make menu elements droppable */
            try {
                $('.dropdown-toggle').dropdown();
            } catch (err) {
            }

            $(".clickable_action, .clickable_layout_action", this.$navigationView).click(function (e) {
                e.preventDefault();
                var shortCode = new $.wpbShortcode($(this).attr('data-element'), $(this).attr('data-width'), that.layout);
            });
            // Button creates row
            $('#wpb-add-new-row', this.$navigationView).unbind('click').click(function(e){
                e.preventDefault();
                that.way_to_add = 'append';
                var row_code = new $.wpbRow(null, that);
            });
            // Convert
            $('#wpb-convert').unbind('click').click(function(e){
                e.preventDefault();
                if(confirm((window.i18nLocale.are_you_sure_convert_to_new_version)))
                $.ajax({
                    type:'POST',
                    url:$.wpbGlobalSettings.ajaxurl,
                    data:{
                        action: 'wpb_get_convert_elements_backend_html',
                        data:that._getContent()
                    }
                }).done(function (response) {
                        window.tinyMCE.get('content').setContent(response, {format:'html'});
                        that.show();
                        $('#wpb_vc_js_interface_version').val('2');
                        $('#wpb-convert-message').hide();
                });
            });
            $('#wpb-save-post').unbind('click').click(function(e){
                e.preventDefault();
                $('#publish').trigger('click');
            })
        },
        initContent:function () {
            var that = this;
            this.$contentView.children('.wpb_sortable').each(function () {
                var wpb_element = new $.wpbShortcode($(this), null, that);
                wpb_element.init();
            });
            this._initDragAndDrop();
        },
        sizeRows: function() {
            $.each($('.wpb_row_container').get().reverse(), function(){
                var max_height = 35;
                $(this).find('> .wpb_vc_column, > .wpb_vc_column_inner').each(function(){
                    var content_height = $(this).find('> .wpb_element_wrapper > .wpb_column_container').css({minHeight: 0}).innerHeight();
                    /*
                    $(this).find('> .wpb_element_wrapper > .wpb_column_container > [data-element_type]').each(function(){
                        content_height += $(this).outerHeight(true);
                    });
                    */
                    if(content_height > max_height) max_height = content_height;

                });
                $(this).find('> .wpb_vc_column, > .wpb_vc_column_inner').each(function(){
                    $(this).find('> .wpb_element_wrapper > .wpb_column_container').css({minHeight: max_height });
                });
            });
        },
        _setSortable: function() {
            var that = this;

            $('.wpb_main_sortable').sortable({
                forcePlaceholderSize:true,
                placeholder:"widgets-placeholder",
                // cursorAt: { left: 10, top : 20 },
                cursor:"move",
                items:"> .wpb_vc_row", //wpb_sortablee
                handle: '.column_move',
                distance:0.5,
                start:function () {
                    $('#visual_composer_content').addClass('sorting-started');
                },
                stop:function (event, ui) {
                    $('#visual_composer_content').removeClass('sorting-started');
                },
                update:function () {
                    $.jsComposer.save_composer_html();
                },
                over:function (event, ui) {
                    ui.placeholder.css({maxWidth:ui.placeholder.parent().width()});
                }
            });
            $('.wpb_column_container').sortable({
                forcePlaceholderSize:true,
                connectWith:".wpb_column_container",
                placeholder:"widgets-placeholder",
                // cursorAt: { left: 10, top : 20 },
                cursor:"move",
                items:"> div.wpb_sortable", //wpb_sortablee
                distance:0.5,
                tolerance: 'pointer',
                start:function () {
                    $('#visual_composer_content').addClass('sorting-started');
                    $('.vc_not_inner_content').addClass('dragging_in');
                },
                stop:function (event, ui) {
                    $('#visual_composer_content').removeClass('sorting-started');
                    $('.dragging_in').removeClass('dragging_in');
                },
                update:function () {
                    that.sizeRows();
                    $.jsComposer.save_composer_html();
                },
                over:function (event, ui) {
                    /*
                    if(!ui.placeholder.parent().hasClass('wpb_column_container')) {
                        ui.placeholder.addClass('hidden-placeholder');
                        return false;
                    }
                    ui.placeholder.removeClass('hidden-placeholder');
                    */
                    if(ui.placeholder.parent().hasClass('wpb_vc_column_inner_container') && ui.item.hasClass('wpb_container_block')) {
                        ui.placeholder.addClass('hidden-placeholder');
                        return false;
                    }
                    ui.placeholder.removeClass('hidden-placeholder');

                    ui.placeholder.css({maxWidth:ui.placeholder.parent().width()});
                },

                beforeStop:function (event, ui) {
                    if(ui.placeholder.parent().hasClass('wpb_vc_column_inner_container') && ui.item.hasClass('wpb_container_block')) {
                        $('#visual_composer_content').removeClass('sorting-started');
                        return false;
                    }
                }
            });
        },
        _initDragAndDrop:function () {
            var that = this;
            // If element is dropped on main page.
            this.$contentView.droppable({
                greedy:true,
                accept:".dropable_el,.dropable_row",
                hoverClass:"wpb_ui-state-active",
                drop:function (event, ui) {
                    if (ui.draggable.is('#wpb-add-new-element')) {
                        var modal = new $.wpbModal(that);
                        modal.showList();
                    } else if(ui.draggable.is('#wpb-add-new-row')) {
                        that.way_to_add = 'append';
                        var row_code = new $.wpbRow(null, that);
                    }
                }
            });
            this._setSortable();
        },
        checkIsEmpty:function () {
            if (!$('.wpb_main_sortable').hasClass('loading') && !$('.wpb_main_sortable > div').length) {
                $('.metabox-composer-content').addClass('empty-composer');
            } else {
                $('.metabox-composer-content').removeClass('empty-composer');
            }
        },
        checkContentForVersion: function() {
            if(this.is_stage && this.$contentView.find('> [data-element_type]:not(.wpb_vc_row)').length > 0) {
                $('#wpb-convert-message').show();
            } else {
                $('#wpb-convert-message').hide();
            }

        },
        add:function ($wpbShortcode) {
            if (this.way_to_add === 'prepend')
                this.prepend($wpbShortcode);
            else
                this.append($wpbShortcode);
            // Define next step in "creation wizard". Usually inside modal window.
            if ($wpbShortcode.data('wpb_shortcode').shortcode === 'vc_column') {
                var modal = new $.wpbModal(this);
                modal.flash();
            } else {
                var modal = new $.wpbModal(this);
                if($wpbShortcode.data('wpb_shortcode').shortcode == 'vc_row' || $wpbShortcode.data('wpb_shortcode').shortcode == 'vc_row_inner' || $('.wpb-content-layouts-container [data-element=' + $wpbShortcode.data('wpb_shortcode').shortcode + ']').hasClass('dont-show-settings-on-create')) {
                    modal.close();
                } else {
                    modal.showSettings($wpbShortcode);
                }
            }
        },
        prepend:function ($wpbShortcode, do_not_inform) {
            $wpbShortcode.prependTo(this.$contentView);
            this._initObject($wpbShortcode, do_not_inform);
        },
        append:function ($wpbShortcode, do_not_inform) {
            $wpbShortcode.appendTo(this.$contentView);
            this._initObject($wpbShortcode, do_not_inform);
        },
        insertAfter:function ($wpbShortcode, $preobject) {
            $wpbShortcode.insertAfter($preobject);
            this._initObject($wpbShortcode, true);
        },
        _initObject:function ($wpbShortcode, do_not_inform) {
            this.checkIsEmpty();
            // this.$contentView.removeClass('empty_column');
            // TODO: refactor this without chasing all this elements
            this.$view.find(".wpb_vc_init_callback").each(function (index) {
                var fn = window[$(this).attr("value")];
                if (typeof fn === 'function') {
                    fn($(this).closest('.wpb_content_element').removeClass('empty_column'));
                }
            });
            if (typeof(do_not_inform) === 'undefined' || do_not_inform !== true) {
                // Animated scroll to element
                //$('body').scrollTo($wpbShortcode);
                // Show animation that will inform the user about new element on current layout and it position.
                $wpbShortcode.data('wpb_shortcode').animateAsNew();
            }
            this.checkContentForVersion();
            // this._setSortable();
            // $.jsComposer.save_composer_html();
        },
        _getContent:function () {
            var content;
            try {
                content = window.tinyMCE.get('content').save();
                if(window.tinyMCE.settings.apply_source_formatting!= undefined && window.tinyMCE.settings.apply_source_formatting === true) {
                    content = window.switchEditors._wp_Nop(content);
                }
            } catch(e) {}
            if(typeof(content)==='undefined' || content === null) {
                content = typeof(window.switchEditors)!=='undefined' ?  window.switchEditors._wp_Nop($('#content').val()) : $('#content').val();
            }
            return content;
        },
        save:function () {
            $.jsComposer.save_composer_html();
        },
        show:function () {
            this.$contentView.addClass('loading').html('<span class="loading_message_block"><img src="images/wpspin_light.gif" alt="" />' + ' ' + $('#wpb_vc_loading').val() + '</span>');
            window.setTimeout(function(){window.switchEditors.go('content', 'tmce')}, 1500);
            this.$postView.hide();
            this.$view.show();
            var content = this._getContent();
            new $.wpbShortcode().fromEditor(content, this);
            if (this.$switchButton !== undefined) this.$switchButton.html(window.i18nLocale.main_button_title_revert);
            wpb_navOnScroll();
            this.$vcStatus.val("true");
        },
        hide:function () {
            this.$postView.show();
            this.$view.hide();
            this.$vcStatus.val("false");
            if (this.$switchButton !== undefined) this.$switchButton.html(window.i18nLocale.main_button_title);
        },
        removeAjaxLoader: function() {
            $('.loading_message_block', this.$contentView.removeClass('loading')).remove();
        }
        // }}
    };

    // Create js Composer stage
    $.wpb_stage = new $.wpbStage();

}(window.jQuery);