/* =========================================================
 * layout.js v0.5.0
 * =========================================================
 * Copyright 2012 Wpbakery
 *
 * Visual composer layout object. Helps to create and edit
 * shortcodes inside content layouts.
 * Require: $.wpbStage.
 * ========================================================= */


!function ($) {

    $.wpbLayout = function ($element) {
        this.$view = $element;
        this.$contentView = $element;
        this.$navigationView = $element;
    };
    $.wpbLayout.prototype = $.extend({}, $.wpbStage.prototype, {
        is_stage: false,
        way_to_add: 'prepend',
        elements: [],
        init:function () {
            this.initContent();
            this.checkIsEmpty();
            this.initHelperText();
        },
        initHelperText:function () {
            var that = this;
            this.$contentView.parent().on('click', '> .empty_container', function(e){
                e.preventDefault();
                var modal = new $.wpbModal(that);
                modal.showList();
            });
        },
        _initDragAndDrop:function () {
            var that = this;
            this.$contentView.droppable({
                greedy:true,
                accept: function($element){
                    if(($element.hasClass('dropable_row') || $element.hasClass('wpb_container_block')) && $(this).hasClass('wpb_vc_column_inner_container')) {
                        return false;
                    } else if ($element.hasClass('dropable_el') || $element.hasClass('dropable_row')) {
                        return true;
                    }// TODO: create data params to validate inner and simple column
                },
                accept_old: function (dropable_el) {
                    if (dropable_el.hasClass('dropable_el') && $(this).hasClass('ui-droppable') && dropable_el.hasClass('not_dropable_in_third_level_nav')) {
                        return false;
                    } else if (dropable_el.hasClass('dropable_el')) {
                        return true;
                    }
                },
                hoverClass:"wpb_ui-state-active",
                over:function (event, ui) {
                    $(this).parent().addClass("wpb_ui-state-active");
                },
                out:function (event, ui) {
                    $(this).parent().removeClass("wpb_ui-state-active");
                },
                drop:function (event, ui) {
                    $(this).parent().removeClass("wpb_ui-state-active");
                    if (ui.draggable.is('#wpb-add-new-element')) {
                        var modal = new $.wpbModal(that);
                        modal.showList();
                    } else if(ui.draggable.is('#wpb-add-new-row')) {
                        that.way_to_add = 'prepend';
                        var row_code = new $.wpbRow(null, that);
                    }
                }
            });
            this._setSortable();
        },
        append:function ($wpbShortcode, do_not_inform) {
            $wpbShortcode.appendTo(this.$contentView);
            this._initObject($wpbShortcode, do_not_inform);
            $.jsComposer.save_composer_html();
        }
    });

}(window.jQuery);