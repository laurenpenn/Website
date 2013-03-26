/* =========================================================
 * wpbelement.js v1.0.0
 * =========================================================
 * Copyright 2012 Wpbakery
 *
 * Visual composer element's plugin. Helps to create and edit
 * shortcodes inside administration panel of wordpress edit
 * post page.
 * ========================================================= */

!function ($) {

    /* Visual composer Short Code */

    var WpbShortCodeModal = function(shortcode, html) {
        this.shortcode = shortcode;
        return this.init(html);
    };

    WpbShortCodeModal.prototype = {
        constructor: WpbShortCodeModal,
        $element: '',
        _attr: {},
        content: '',
        html : '',
        nested_objects:[],
        init: function(html) {
            this._create(html);
            this.$element.data('wpb_shortcode', this);
            return this;
        },
        _create: function(html) {
            this.html = html;
            this.$element = $(html);
        },
        get: function() {
            return this.$element;
        },
        setAttr: function(attr) {
            this._attr = attr;
            return this;
        },
        attr: function(name) {
            return this._attr[name];
        }

    };

    var WpbContainer = function(selector) {
        this.$view = $(selector);
        this.init();
    };


    WpbContainer.protype = {
        init: function() {

        }

    };

    var WpbModalView = function(selector) {
        this.$view = $(selector);
        this.init();
    };

    WpbModalView.prototype = {
        init: function() {
        },
        showShortCodesList: function() {
            $('.wpb-edit-form', this.$view).hide();
            $('.wpb-elements-list', this.$view).show();
            $('.wpb-content-layouts').isotope({
                itemSelector : '.wpb-layout-element-button',
                layoutMode : 'fitRows'
            });

            $('.wpb-content-layouts-container .isotope-filter a', this.$view).unbind('click.isotopeFilter').bind('click.isotopeFilter', function(e){
                e.stopPropagation();
                $('.wpb-content-layouts-container .isotope-filter .active').removeClass('active');
                $(this).parent().addClass('active');
                $('.wpb-content-layouts').isotope({ filter: $(this).attr('data-filter') });
                return false;
            });

            this.$view.modal('show');
            $('.wpb-content-layouts').isotope({layoutMode : 'fitRows'});
        },
        setModal: function(element) {
            this.modal = element;
            return this;
        },
        showShortCodeSettingsForm: function() {
            var that = this;
            $('.wpb-edit-form', this.$view).show();
            $('.wpb-elements-list', this.$view).hide();

            $('.wpb_save_edit_form', this.$view).unbind('click.wpbElementSettingsSubmit').bind('click.wpbElementSettingsSubmit', function(){
                that.saveElementSettings();
            });
            $('#cancel-background-options').unbind('click').click(function(){
                that.close();
            });
            return this;
        },
        renderSettingsForm: function(html) {
            $('.wpb-edit-form-inner', this.$view).html(html);
            return this;
        },
        close: function() {
            this.$view.modal('hide');
        },
        flash: function() {
            var that = this;
            this.$view.addClass('animated hideshow');
            $('.modal-backdrop').addClass('animated hideshow');
            window.setTimeout(function(){
                that.$view.removeClass('hideshow');
                $('.modal-backdrop').removeClass('hideshow');
            }, 1500);
        }
    };

    var WpbComposerController = function(options) {
        this.options = $.extend({
            container: 'body',
            content_container: '#visual_composer_content',
            modal: '#wpb-elements-list-modal',
            ajaxurl: '/'
        }, options);
        this.init();
    };

    WpbComposerController.prototype = {
        init: function() {
            this.modalWindow = $.extend(new WpbModalView(this.options.modal), this);
            this.stage = $.extend(new WpbContainer(this.options.content_container));
        },
        createShortCode: function(shortcode, $container, width) {
            var that = this,
                data = {
                    action: 'wpb_get_element_backend_html',
                    data_element: shortcode,
                    data_width: width
                };
            $.post(this.options.ajaxurl, data, $.proxy(function(response) {
                var element = new WpbShortCodeModal(shortcode, response);

                element.get().appendTo($container);
                $container.find(".wpb_vc_init_callback").each(function(index) {
                    var fn = window[$(this).attr("value")];
                    if ( typeof fn === 'function' ) {
                        fn($(this).closest('.wpb_content_element').removeClass('empty_column'));
                    }
                });

                $('body').scrollTo(element.get());
                element.get().addClass('fadeIn colorFlash animated');
                window.setTimeout(function(){
                    element.get().removeClass('fadeIn colorFlash animated');
                }, 2000);
                if(element.shortcode==='vc_column') {
                    this.modalWindow.flash();
                } else {
                    this.editShortCodeSettings(element);
                }

            }, this));
        },
        showElementsList: function() {
            var that = this;
            this.modalWindow.showShortCodesList();
            $(".clickable_action", this.modalWindow.$view).unbind('click.wpbElementButton').bind('click.wpbElementButton', function(e) {
                e.preventDefault();
                that.createShortCode($(this).attr('data-element'), $('.main_wrapper'), $(this).attr('data-width'));
            });
        },
        editShortCodeSettings: function(element) {
            var that = this;
            jQuery.post(this.options.ajaxurl, {
                action: 'wpb_show_edit_form',
                element: element.shortcode,
                shortcode: window.generateShortcodesFromHtml(element.get(), true)
            }, function(response) {
                that.modalWindow.setElement(element).renderSettingsForm(response).showShortCodeSettingsForm();
            });
        },
        saveElementSettings: function(element) {
        }
    };

    $.wpbComposerController = function(options, params) {
        return typeof(options)==='string' ? WpbComposerController[options](params) : new WpbComposerController(options);
    }









}(window.jQuery);