/* =========================================================
 * row.js v0.7.0
 * =========================================================
 * Copyright 2012 Wpbakery
 *
 * Visual composer row as a new structure element.
 * In ome cases
 * ========================================================= */

!function ($) {

    $.wpbRow = function ($element, layout, shortcode) {
        if($element===null && typeof layout !== 'undefined') {
            new $.wpbShortcode().fromEditor((layout.is_stage ? '[vc_row][vc_column width="1/1"][/vc_row]' : '[vc_row_inner][vc_column_inner width="1/1"][/vc_row_inner]'), layout, function(){
                $.jsComposer.save_composer_html();
            });
            return this;
        }
        this.$view = $element;
        this.$contentView = $element;
        this.$navigationView = $element;
    };

    $.wpbRow.prototype = $.extend({}, $.wpbLayout.prototype, {
        init:function () {
            this.initContent();
            // $.jsComposer.save_composer_html();
        },
        clearContent: function(){
            this.$contentView.css('minHeight', this.$contentView.height());
            this.$contentView.html('<span class="loading_message_block"><img src="images/wpspin_light.gif" alt="" />' + ' ' + $('#wpb_vc_loading_row').val() + '</span>');
        },
        initContent:function () {
            var that = this;
            this.$contentView.children('.wpb_sortable').each(function () {
                var wpb_element = new $.wpbShortcode($(this), null, that);
                wpb_element.init();
            });
            // this._initDragAndDrop();
        },
        checkIsEmpty:function () {
            if (!$('.wpb_main_sortable').hasClass('loading') && !$('.wpb_main_sortable > div').length) {
                $('.metabox-composer-content').addClass('empty-composer');
            } else {
                $('.metabox-composer-content').removeClass('empty-composer');
            }
            this.$contentView.css('minHeight', 0);

        },
        _setSortable: function() {
            var that = this;
            if(this.$contentView.find("> [data-element_type=vc_column], > [data-element_type=vc_column_inner]").length > 1) {
                this.$contentView.removeClass('wpb-not-sortable').sortable({
                    forcePlaceholderSize:true,
                    placeholder:"widgets-placeholder-column",
                    tolerance: "pointer",
                    // cursorAt: { left: 10, top : 20 },
                    cursor:"move",
                    //handle: '.controls',
                    items:"> [data-element_type=vc_column], > [data-element_type=vc_column_inner]", //wpb_sortablee
                    distance:0.5,
                    start:function (event, ui) {
                        $('#visual_composer_content').addClass('sorting-started');
                        ui.placeholder.width(ui.item.width());
                    },
                    stop:function (event, ui) {
                        $('#visual_composer_content').removeClass('sorting-started');
                    },
                    update:function () {
                        $.jsComposer.save_composer_html();
                    },
                    over:function (event, ui) {
                        ui.placeholder.css({maxWidth: ui.placeholder.parent().width()});
                        ui.placeholder.removeClass('hidden-placeholder');
                        // if (ui.item.hasClass('not-column-inherit') && ui.placeholder.parent().hasClass('not-column-inherit')) {
                        //     ui.placeholder.addClass('hidden-placeholder');
                        // }
                    },
                    beforeStop:function (event, ui) {
                        // if (ui.item.hasClass('not-column-inherit') && ui.placeholder.parent().hasClass('not-column-inherit')) {
                        //     return false;
                        // }
                    }
                });
            } else {
                if(this.$contentView.hasClass('ui-sortable')) this.$contentView.sortable('destroy');
                this.$contentView.addClass('wpb-not-sortable');
            }

            that.sizeRows();
        }
    });

}(window.jQuery);