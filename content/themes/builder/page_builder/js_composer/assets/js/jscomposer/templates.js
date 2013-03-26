/* =========================================================
 * templates.js v0.0.1
 * =========================================================
 * Copyright 2012 Wpbakery
 *
 * Visual composer templates. Helps you to create presets of
 * page's layout.
 * ========================================================= */

!function ($) {
    $.wpbTemplates = function ($view) {
        this.$view = $view;
        this.$templateView = $('.wpb_templates_list', this.$view);
        this.init();
    };
    $.wpbTemplates.prototype = {
        init:function () {
            var that = this;
            $('#wpb_save_template > a').unbind('click').click(function (e) {
                e.preventDefault();

                var template_name = prompt(window.i18nLocale.please_enter_templates_name, '');
                if (typeof(template_name) === 'string' && template_name.length) {
                    var template = $.jsComposer.generateShortcodesFromHtml($(".wpb_main_sortable"));
                    var data = {
                        action:'wpb_save_template',
                        template:template,
                        template_name:template_name
                    };
                    that.reloadTemplateList(data);
                } else if (template_name != null) {
                    alert(window.i18nLocale.error_please_try_again);
                }
            });
            this.$view.unbind('click.templateLink').on("click.templateLink", '[data-template_id]', function (e) {
                e.preventDefault();
                var data = {
                    action:'wpb_load_template',
                    template_id:$(this).attr('data-template_id')
                };
                $.post($.wpbGlobalSettings.ajaxurl, data, function (response) {
                    $.each(new $.wpbShortcode()._create(response, $.wpb_stage), function () {
                        $.wpb_stage.append(this.$element);
                        this.init();
                        $.wpb_stage.sizeRows();
                    });
                    $.jsComposer.save_composer_html();
                });
            });
            this.$templateView.unbind('click.removeTemplate').on("click.removeTemplate", '.wpb_remove_template', function (e) {
                e.preventDefault();
                var template_name = $(this).closest('.wpb_template_li').find('a').text();
                var answer = confirm(window.i18nLocale.confirm_deleting_template.replace('{template_name}', template_name));
                if (answer) {
                    //alert("delete");
                    var data = {
                        action:'wpb_delete_template',
                        template_id:$(this).closest('.wpb_template_li').find('a').attr('data-template_id')
                    };
                    that.reloadTemplateList(data);
                }
            });
        },
        reloadTemplateList:function (data) {
            var that = this;
            $.post($.wpbGlobalSettings.ajaxurl, data, function (response) {
                that.$templateView.html(response);
            });
        }
    }
}(window.jQuery);