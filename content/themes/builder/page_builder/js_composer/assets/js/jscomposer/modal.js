/* =========================================================
 * modal.js v0.5.0
 * =========================================================
 * Copyright 2012 Wpbakery
 *
 * Visual composer modal object. Helps to create and edit
 * shortcodes inside administration panel of wordpress edit
 * post page.
 * Modal helps to configurate elements on the main stage and
 * layouts.
 * Require: bootstrap.modal.js
 * ========================================================= */


!function ($) {
    // Init jQuery DOM. Like singleton :)
    var $view = $('#wpb-elements-list-modal');
    $view.extend({
        $list:$('.wpb-elements-list', $view),
        $settings:$('.wpb-edit-form', $view),
        $image_gallery:$('.wpb-image-gallery', $view)
    });


    $.wpbModal = function (layout) {
        // This still initialize from html from server.
        // Maybe better to render modal layout and content right here with ajax and mustache(for example).
        this.$view = $view;
        this.$header = this.$view.find('.modal-header > h3');
        this.layout = layout;
        this.init();
    };

    $.wpbModal.prototype = {
        init:function () {
        },
        _initList:function () {
            var that = this,
                $list = this.$view.$list;
            var item_selector = '.wpb-layout-element-button';
            // Hide tabs, tour and accordion to not to allow to add to other tab, accordion.
            if (this.layout.$contentView.is('.wpb_vc_column_inner_container')) {
               $('.vc_not_inner_content_o', this.$view).hide();
               $('.is_row_o', this.$view).show();
               item_selector += ':not(.vc_not_inner_content_o)';
            } else if (this.layout.$contentView.is('.wpb_no_content_element_inside')) {
                $('.vc_not_inner_content_o:not(.is_row_o)', this.$view).hide();
                item_selector += ':not(.vc_not_inner_content_o:not(.is_row_o))';
            } else {
                $('.vc_not_inner_content_o,.is_row_o', this.$view).show();
            }

            $('.wpb-content-layouts', $list).isotope({
                itemSelector:item_selector,
                layoutMode:'fitRows',
                filter:null
            });
            $('.wpb-content-layouts', $list).isotope('reloadItems');
            $('.wpb-content-layouts-container .isotope-filter a', $list).unbind('click.isotopeFilter').bind('click.isotopeFilter', function (e) {
                e.stopPropagation();
                $('.wpb-content-layouts-container .isotope-filter .active', $list).removeClass('active');
                $(this).parent().addClass('active');
                $('.wpb-content-layouts', $list).isotope({ filter:$(this).attr('data-filter') });
                return false;
            });
            $('.wpb-content-layouts-container .isotope-filter a:first', $list).trigger('click');
            $(".clickable_action", this.$view).unbind('click.wpbElementButton').bind('click.wpbElementButton', function (e) {
                e.preventDefault();
                var shortCode = new $.wpbShortcode($(this).attr('data-element'), $(this).attr('data-width'), that.layout);
            });
            $list = null;
        },
        showList:function () {
            this.$view.$settings.hide();
            this.$view.$list.show();
            this.$view.$image_gallery.hide();
            this._initList();
            this.$header.text(window.i18nLocale.header_select_element_type);
            this.show();
            $('.wpb-content-layouts', this.$view.$list).isotope({layoutMode:'fitRows'});
        },
        showSettings:function ($element) {
            $('.wpb-edit-form-inner', this.$view.$settings).html('<img src="images/wpspin_light.gif" alt="spinner" />');
            new $.wpbShortCodeSettings($element, this);
            this.$view.$settings.show();
            this.$view.$list.hide();
            this.$view.$image_gallery.hide();
            this.$header.text(window.i18nLocale.header_element_settings);
            this.show(true);
        },
        addSettings:function ($settings) {
            var $header = $settings.find('h2:first');
            this.$header.text($header.text());
            $header.remove();
            $settings.appendTo($('.wpb-edit-form-inner', this.$view.$settings).html(''));
        },
        showImageGallery:function (url) {
            this.$view.$image_gallery.html('<iframe width="100%" height="700px" frameborder="no" src="' + url + '" ></iframe>').show();
            this.$view.$settings.hide();
            this.$view.$list.hide();
            this.$header.text(window.i18nLocale.header_media_gallery);
            this.show(true);
        },
        switchTo:function (tab_name) {
            switch (tab_name) {
                case '$settings':
                    this.$view.$settings.show();
                    this.$view.$list.hide();
                    this.$view.$image_gallery.hide();
                    break;
                case '$list':
                    this.$view.$settings.hide();
                    this.$view.$list.show();
                    this.$view.$image_gallery.hide();
                    break;
                case '$image_gallery':
                    this.$view.$settings.hide();
                    this.$view.$list.hide();
                    this.$view.$image_gallery.show();
                    break;
            }
        },
        flash:function () {
            var that = this;
            this.$view.addClass('animated hideshow');
            $('.modal-backdrop').addClass('animated hideshow');
            window.setTimeout(function () {
                that.$view.removeClass('hideshow');
                $('.modal-backdrop').removeClass('hideshow');
            }, 1500);
        },
        show:function (notification_on_close) {
            var that = this;
            this.$view.modal('show').unbind('hide.wpbModal').bind('hide.wpbModal', function (e) {
                if (typeof notification_on_close !== 'undefined'
                    && notification_on_close === true
                    && !confirm(window.i18nLocale.if_close_data_lost)
                    ) return false;
                $('textarea.textarea_html', this.$view).each(function(){
                    var id = $(this).attr('id');
                    window.tinyMCE.execCommand("mceRemoveControl", true, id);
                });
                that.$view.$image_gallery.html('');
                $('.wpb-edit-form-inner', that.$view.$settings).html('');
                return true;
            });
        },
        close:function (disable_notification_on_close) {
            if (typeof disable_notification_on_close !== 'undefined' && disable_notification_on_close === true) {
                this.$view.unbind('hide.wpbModal');
                $('.wpb-edit-form-inner', this.$view.$settings).html('');
            }
            this.$view.$image_gallery.html('');
            this.$view.modal('hide');
        }

    };
}(window.jQuery);