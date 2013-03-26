/* =========================================================
 * shortcode.js v0.5.0
 * =========================================================
 * Copyright 2012 Wpbakery
 *
 * Visual composer shortcode object.
 * ========================================================= */

!function ($) {

    $.wpbShortcode = function (shortcode, width, container) {
        if (typeof(shortcode) === 'object') {
            this.$element = shortcode;
            this.shortcode = shortcode.find('.wpb_vc_sc_base').val();
            this.$elementControls = shortcode.children('.controls');
            this.container = typeof(container) === 'undefined' ? new $.wpbStage() : container;
            this.width = width;
            this.has_settings = this.$element.find('.wpb_vc_param_value').length > 0;
            this.$element.data('wpb_shortcode', this);
        } else if (shortcode !== undefined) {
            var container = typeof(container) === 'undefined' ? new $.wpbStage() : container; // here comes layout or main stage
            this.getFromServer(shortcode, width, container);
        }
    };

    $.wpbShortcode.prototype = {
        fromEditor:function (shortcodes, container, callback) {
            var that = this;
            $.ajax({
                type:'POST',
                url:$.wpbGlobalSettings.ajaxurl,
                data:{
                    action:'wpb_shortcodes_to_visualComposer',
                    content:shortcodes
                },
                dataType:'html',
                context:container
            }).done(function (response) {
                    $.each(that._create(response, this), function () {
                        if(container.way_to_add == 'prepend') {
                            container.prepend(this.$element, true);
                            this.init();
                        } else {
                            container.append(this.$element, true);
                            this.init();
                        }
                    });
                    container.removeAjaxLoader();
                    container.checkIsEmpty();
                    addLastClass(container.$contentView);
                    container._setSortable();
                    container.sizeRows();
                    if(typeof callback === 'function') {
                        callback(this);
                    }
            });
        },
        init:function () {
            // this.$element.extend(this);
            this.initControls();
            if (this.shortcode == 'vc_column' || this.shortcode == 'vc_column_inner') {
                this.initColumn();
            } else if (this.shortcode == 'vc_row' || this.shortcode == 'vc_row_inner') {
                this.initRow();
            } else {
                this.initContent();
            }
        },
        initContent:function () {
            $('.wpb_element_wrapper > .toggle_title', this.$element).unbind('click').click(function (e) {
                if ($(this).hasClass('toggle_title_active')) {
                    $(this).removeClass('toggle_title_active').next().hide();
                } else {
                    $(this).addClass('toggle_title_active').next().show();
                }
                $.wpb_stage.sizeRows();
            });
        },
        setActiveRowLayout: function(layout_type) {
            if(this.row!=undefined) {
                this.$elementControls.find('a[data-cells].vc_active').removeClass('vc_active');

                if(typeof(layout_type)!='undefined') {
                    this.$elementControls.find('a[data-cells=' + layout_type +']').addClass('vc_active');
                } else {
                    layout_ident = [];
                    layout_count = 0;
                    this.row.$contentView.find('> [data-vc-column-width]').each(function(){
                        layout_ident.push(_.reduce($(this).attr('data-vc-column-width').split(""), function(memo, num){ return memo + parseInt(num)}, 0));
                        layout_count++;
                    });
                    var selector =layout_count + '' + _.reduce(layout_ident, function(memo, num){ return memo + parseInt(num)}, 0);
                    this.$elementControls.find('a[data-cells-mask=' + selector +']').addClass('vc_active');
                }
            }
        },
        initRow:function () {
            this.row = new $.wpbRow(this.$element.find('> .wpb_element_wrapper > .wpb_row_container'));
            this.row.init();
            this.row._setSortable();
            this.setActiveRowLayout();
        },
        initColumn:function () {
            this.column = new $.wpbLayout(this.$element.find('> .wpb_element_wrapper > .wpb_column_container'));
            this.column.init();
        },
        initControls:function () {
            var that = this;
            $(".column_delete", this.$elementControls).unbind('click').click(function (e) {
                e.preventDefault();
                var answer = confirm(i18nLocale.press_ok_to_delete_section);
                if (answer) {
                    that.$element.remove();
                    that.container.checkIsEmpty();
                    that.container.sizeRows();
                    $.jsComposer.save_composer_html();
                }
            });
            $(".column_clone", this.$elementControls).unbind('click').click(function (e) {
                e.preventDefault();
                var wpb_shortcode = that.cloneShortCode();
                $.wpb_stage.sizeRows();
                wpb_shortcode.init();
                $.jsComposer.save_composer_html();
            });

            $(".column_popup", this.$elementControls).unbind('click').click(function (e) {
                e.preventDefault();
                var answer = confirm(i18nLocale.press_ok_to_pop_section);
                if (answer) {
                    that.$element.appendTo('.wpb_main_sortable');//insertBefore('.wpb_main_sortable div.wpb_clear:last');
                    $.jsComposer.save_composer_html();
                }
            });
            $(".column_edit", this.$elementControls).unbind('click').click(function (e) {
                e.preventDefault();
                var modal = new $.wpbModal($.wpbStage);
                modal.showSettings(that.$element);
            });
            $('.wpb_element_wrapper > .column_edit_trigger', this.$element).unbind('click').click(function (e) {
                e.preventDefault();
                var modal = new $.wpbModal($.wpbStage);
                modal.showSettings(that.$element);
            });
            $(".column_add", this.$elementControls).unbind('click').click(function (e) {
                e.preventDefault();
                if (typeof that.column !== 'undefined' && that.column !== null) {
                    var modal = new $.wpbModal(that.column);
                    that.column.way_to_add = $(this).closest('.controls').hasClass('bottom-controls') ? 'append' : 'prepend';
                    modal.showList();
                }
            });
            $('.set_columns', this.$elementControls).unbind('click').click(function(e){
                e.preventDefault();
                if($(this).is('.vc_active')) return false;
                var $content = that.$element.find('> .wpb_element_wrapper > .wpb_row_container'),
                    new_shortcodes = $.jsComposer.convertToRowType($content.children('.wpb_sortable'), $(this).data('cells'));
                that.row.clearContent();
                that.setActiveRowLayout($(this).data('cells'));
                that.row.way_to_add = 'append'; // Remove this to create another way to append/prepend
                new $.wpbShortcode().fromEditor(new_shortcodes, that.row);
            });
            $(".column_increase", this.$elementControls).unbind('click').click(function (e) {
                e.preventDefault();
                var column = $(this).closest(".wpb_sortable"),
                    sizes = getColumnSize(column);
                if (sizes[1]) {
                    column.removeClass(sizes[0]).addClass(sizes[1]);
                    /* get updated column size */
                    sizes = getColumnSize(column);
                    $(column).find(".column_size:first").html(sizes[3]);
                    $.jsComposer.save_composer_html();
                }
            });
            $(".column_decrease", this.$elementControls).unbind('click').click(function (e) {
                e.preventDefault();
                var column = $(this).closest(".wpb_sortable"),
                    sizes = getColumnSize(column);
                if (sizes[2]) {
                    column.removeClass(sizes[0]).addClass(sizes[2]);
                    /* get updated column size */
                    sizes = getColumnSize(column);
                    $(column).find(".column_size:first").html(sizes[3]);
                    $.jsComposer.save_composer_html();
                }
            });
        },
        _create:function (html, container) {
            var $html = $(html),
                list = [];
            $html.each(function () {
                if ($(this).is('div')) {
                    var width = '';
                    list.push(new $.wpbShortcode($(this), '1/1', container));
                }
            });
            return list;
        },
        // TODO: rewrite it with backbones and templates.
        getFromServer:function (shortcode, width, container) {
            var that = this;
            if(!container.is_stage && shortcode=='vc_row') {
                shortcode = 'vc_row_inner';
            }
            $.ajax({
                type:'POST',
                url:$.wpbGlobalSettings.ajaxurl,
                data:{
                    action:(container.is_stage && shortcode!='vc_row' && shortcode!='vc_row_inner' ? 'wpb_get_row_element_backend_html' : 'wpb_get_element_backend_html'),
                    data_element:shortcode,
                    data_width:width
                },
                dataType:'html',
                context:container
            }).done(function (response) {
                    var container = this;
                    $.each(that._create(response, this), function() {
                        if(this.shortcode == 'vc_row' && shortcode!='vc_row') {
                            container.append(this.$element);
                            this.init();
                            if(shortcode!='vc_column' && this.shortcode!=shortcode) {
                                var modal = new $.wpbModal(container);
                                if($('.wpb-content-layouts-container [data-element=' + shortcode + ']').hasClass('dont-show-settings-on-create')) {
                                    modal.close();
                                } else {
                                    modal.showSettings(this.row.$view.find('[data-element_type=' + shortcode +']'));

                                }
                            }
                        } else {
                            if(this.has_settings) {
                                container.add(this.$element);
                            } else {
                                if(container.way_to_add == 'prepend') {
                                    container.prepend(this.$element);
                                } else {
                                    container.append(this.$element);
                                }

                                new $.wpbModal().close(true);
                            }
                            this.init();
                        }

                    });
                    container._setSortable();
                    container.sizeRows();
                    container.save();
                });
        },
        animateAsNew:function () {
            var $element = this.$element;
            $element.addClass('fadeIn colorFlash animated');
            window.setTimeout(function () {
                $element.removeClass('fadeIn colorFlash animated');
            }, 2000);
        },
        cloneShortCode:function () {
            var $wpb_shortcode = new $.wpbShortcode(this.$element.clone(), this.width, this.container);
            $wpb_shortcode.$element.find('.wpb_initialized').removeClass('wpb_initialized');
            this.container.insertAfter($wpb_shortcode.$element, this.$element);
            return $wpb_shortcode;
        }
    };
}(window.jQuery);