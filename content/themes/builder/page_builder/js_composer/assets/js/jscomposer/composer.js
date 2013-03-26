/* =========================================================
 * composer.js v0.0.1
 * =========================================================
 * Copyright 2012 Wpbakery
 *
 * Visual composer main object. Collection of functions for
 * parsing, converting and controlling data in js composer.
 * ========================================================= */

!function ($) {
    $.jsComposer = {
        getParamValue: function($element) {
            var new_value;
            if ($element.is('div, h1,h2,h3,h4,h5,h6, span, i, b, strong')) {
                new_value = $element.html();
            } else if ($element.is('button')) {
                new_value = $element.text();
            } else if($element.is('img,iframe')) {
                new_value = $element.attr('src');
            } else {
                new_value = $element.val();
            }
            return new_value;
        },
        generateShortcodesFromHtml:function (dom_tree, single_element) {
            var that = this,
                output = '';
            if (single_element) {
                // this is used to generate shortcode for a single content element
                selector_to_go_throw = $(dom_tree);
            } else {
                selector_to_go_throw = $(dom_tree).children(".wpb_sortable");
            }
            selector_to_go_throw.each(function (index) {
                //$(dom_tree.selector+" > .wpb_sortable").each(function(index) {
                var element = $(this),
                    current_top_level = element,
                    sc_base = element.find('> .wpb_vc_sc_base').val(),
                    column_el_width = that.getColumnSize(element),
                    params = '',
                    sc_ending = ']';
                // Parsing all params fields
                element.children('.wpb_element_wrapper').children('.wpb_vc_param_value').each(function (index) {
                    var param_name = $(this).attr("name"),
                        new_value = '';
                    if ($(this).hasClass("textfield")) {
                        new_value = that.getParamValue($(this));
                    }
                    else if ($(this).hasClass("dropdown")) {
                        new_value= that.getParamValue($(this));
                    }
                    else if ($(this).hasClass("textarea_raw_html") && element.children('.wpb_sortable').length == 0) {
                        content_value = that.getParamValue($(this).next('.' + param_name + '_code'));
                        sc_ending = '] ' + content_value + ' [/' + sc_base + ']';
                    }
                    else if ($(this).hasClass("textarea_html") && element.children('.wpb_sortable').length == 0) {
                        content_value = $(this).html();
                        sc_ending = '] ' + content_value + ' [/' + sc_base + ']';
                    } else {
                        new_value = that.getParamValue($(this));
                    }
                    new_value = $.trim(new_value);
                    if (new_value != '') {
                        params += ' ' + param_name + '="' + new_value + '"';
                    }
                });

                if (element.hasClass("wpb_first") || element.hasClass("wpb_last")) {
                    var wpb_first = (element.hasClass("wpb_first")) ? 'first' : '';
                    var wpb_last = (element.hasClass("wpb_last")) ? 'last' : '';
                    var pos_space = (element.hasClass("wpb_last") && element.hasClass("wpb_first")) ? ' ' : '';
                    params += ' el_position="' + wpb_first + pos_space + wpb_last + '"';
                }

                if( column_el_width[3] != undefined )params += ' width="' + column_el_width[3] + '"'
                if(sc_base!=undefined) {
                    output += '[' + sc_base + params + sc_ending + ' ';
                }
                //deeper
                //if ( element.children('.wpb_element_wrapper').children('.wpb_column_container').children('.wpb_sortable').length > 0 ) {
                if (element.children('.wpb_element_wrapper').children('.wpb_column_container,.wpb_row_container, .wpb_holder').length > 0) {
                    //output += generateShortcodesFromHtml(element.children('.wpb_element_wrapper').children('.wpb_column_container'));

                    // Get callback function name
                    var cb = element.children(".wpb_vc_shortcode_callback"),
                        inner_element_count = 0;
                    //
                    element.children('.wpb_element_wrapper').children('.wpb_column_container,.wpb_row_container, .wpb_holder').each(function (index) {
                        //output += '[aaa]'+generateShortcodesFromHtml($(this))+'[/aaa]';
                        var sc = that.generateShortcodesFromHtml($(this));
                        // Fire SHORTCODE GENERATION callback if it is defined
                        if (cb.length == 1) {
                            var fn = window[cb.attr("value")];
                            if (typeof fn === 'function') {
                                var tmp_output = fn(current_top_level, inner_element_count);
                            }
                            sc = " " + tmp_output.replace("%inner_shortcodes", sc) + " ";

                            //var tmp_output = eval(cb.attr("value")+"("+current_top_level+")");
                            //var tmp_output = eval(cb.attr("value")+"('"+current_top_level+"', "+inner_element_count+")");
                            //sc = " " + tmp_output.replace("%inner_shortcodes", sc);
                            inner_element_count++;
                        }
                        //else {
                        //	output += sc;
                        //}
                        output += sc;
                    });
                    if(sc_base!=undefined) output += '[/' + sc_base + '] ';
                }
            });
            return output;
        },
        convertToRowType: function($columns, layout) {
            var column_shortcodes = [],
                shortcode = $($columns[0]).children('.wpb_vc_sc_base').val(),
                layout_split = layout.toString().split(/\_/),
                shortcodes_split = [];
            $columns.each(function(){
                if($(this).find("> .wpb_element_wrapper > .wpb_column_container > .wpb_sortable").length)
                    shortcodes_split.push($.jsComposer.generateShortcodesFromHtml($(this).find("> .wpb_element_wrapper > .wpb_column_container")));
                else
                    shortcodes_split.push('');
            });
            $.each(layout_split, function(i, value) {
                var column_data = $.map(value.toString().split(''), function(v,i){return parseInt(v)});
                column_shortcodes.push(['[' + shortcode + ' width="' + column_data[0] + '/' + column_data[1] + '"]', (shortcodes_split[i]!=undefined ? shortcodes_split[i] : ''),'[/' + shortcode + ']']);
            });
            if(layout_split.length<shortcodes_split.length) {
                $.each(shortcodes_split.slice(layout_split.length), function(i, value){
                    column_shortcodes[layout_split.length-1][1] += value;
                });
            }
          return $.map(column_shortcodes, function(v,i){ return v.join('')}).join('');
        },
        save_composer_html:function () {
            this.checkColumnsIsEmpty();
            this.addLastClass($(".wpb_main_sortable"));
            var shortcodes = this.generateShortcodesFromHtml($(".wpb_main_sortable"));
            window.tinyMCE.get('content').setContent(shortcodes, {format:'html'});
        },
        getColumnSize: function ($column) {
            if ($column.hasClass("span12")) //full-width
                return new Array("span12", "span2", "span10", "1/1", 12);

            else if ($column.hasClass("span10")) //three-fourth
                return new Array("span10", "span12", "span9", "5/6", 10);

            else if ($column.hasClass("span9")) //three-fourth
                return new Array("span9", "span10", "span8", "3/4", 9);

            else if ($column.hasClass("span8")) //two-third
                return new Array("span8", "span9", "span6", "2/3", 8);

            else if ($column.hasClass("span6")) //one-half
                return new Array("span6", "span8", "span4", "1/2", 6);

            else if ($column.hasClass("span4")) // one-third
                return new Array("span4", "span6", "span3", "1/3", 4);

            else if ($column.hasClass("span3")) // one-fourth
                return new Array("span3", "span4", "span2", "1/4", 3);
            else if ($column.hasClass("span2")) // one-fourth
                return new Array("span2", "span3", "span12", "1/6", 2);
            else
                return false;
        },
        checkColumnsIsEmpty: function() {
            $('.wpb_content_holder').each(function(){
                if($(this).find('.wpb_sortable').length==0) {
                    $(this).addClass('empty_column').find('> .wpb_element_wrapper > div').addClass('empty_container');
                } else {
                    $(this).removeClass('empty_column').find('> .wpb_element_wrapper > div').removeClass('empty_container');

                }
            });
        },
        addLastClass:function (dom_tree) {
            var that = this,
                total_width, width, next_width;
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
                var $cur_el = $(this),
                    column_size = that.getColumnSize($cur_el);
                // Width of current element
                width = column_size !== false ? column_size[4] : 0;

                total_width += width;// + next_width;
                if (total_width > 10 && total_width <= 12) {
                    $cur_el.addClass("wpb_last");
                    $cur_el.nextAll('.wpb_sortable:first').addClass("wpb_first");
                    total_width = 0;
                }

                if (total_width > 12) {
                    $cur_el.addClass('wpb_first');
                    $cur_el.prevAll('.wpb_sortable:last').addClass("wpb_last");
                    total_width = width;
                }


                if ($cur_el.hasClass('wpb_vc_row') || $cur_el.hasClass('wpb_vc_column') || $cur_el.hasClass('wpb_vc_tabs') || $cur_el.hasClass('wpb_vc_tour') || $cur_el.hasClass('wpb_vc_accordion')) {
                    if ($cur_el.find('> .wpb_element_wrapper .wpb_column_container').length > 0) {
                        // $cur_el.removeClass('empty_column');
                        // $cur_el.addClass('not_empty_column');
                        //addLastClass($cur_el.find('.wpb_element_wrapper .wpb_column_container'));
                        $cur_el.find('> .wpb_element_wrapper .wpb_column_container').each(function (index) {
                            window.addLastClass($(this)); // Seems it does nothing

                            if ($(this).find('div:not(.container-helper)').length == 0) {
                                // $(this).addClass('empty_column');
                                // $(this).html($('#container-helper-block').html());
                            } else {
                                // $(this).removeClass('empty_column');
                            }
                        });
                    }
                    else if ($cur_el.find('> .wpb_element_wrapper .wpb_column_container > div:not(.container-helper)').length == 0) {
                        // $cur_el.removeClass('not_empty_column');
                        // $cur_el.addClass('empty_column');
                    }
                    else {
                        // $cur_el.removeClass('empty_column not_empty_column');
                    }
                }

            });
            //$(dom_tree).children(".column:first").addClass("first");
            //$(dom_tree).children(".column:last").addClass("last");
        } // endjQuery.wpb_composer.addLastClass()
    };

}(window.jQuery);


/* This function helps when you need to determine current
 column size.

 Returns Array("current size", "larger size", "smaller size", "size string");
 ---------------------------------------------------------- */
function getColumnSize(column) {
    return jQuery.jsComposer.getColumnSize(column);
} // end getColumnSize()

/* This functions goes throw the dom tree and automatically
 adds 'last' class name to the columns elements.
 ---------------------------------------------------------- */
function addLastClass(dom_tree) {
    jQuery.jsComposer.checkColumnsIsEmpty();
    return jQuery.jsComposer.addLastClass(dom_tree);
} // end jQuery.wpb_composer.addLastClass()


function isTinyMceActive() {
    var rich = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
    return rich;
}

function getTinyMceHtml(obj) {

    var mce_id = obj.attr('id'),
        html_back;

    //html_back = tinyMCE.get(mce_id).getContent();

    //tinyMCE.execCommand('mceRemoveControl', false, mce_id);
    try {
        html_back = tinyMCE.get(mce_id).getContent();
        tinyMCE.execCommand('mceRemoveControl', false, mce_id);
    }
    catch (err) {
        html_back = switchEditors.wpautop(obj.val());
    }

    return html_back;
}


/* get content from tinyMCE editor
 ---------------------------------------------------------- */
function wpb_getContentFromTinyMCE() {
    var content = '';

    //if ( tinyMCE.activeEditor ) {
    // if ( isTinyMceActive() ) {
    var wpb_ed = tinyMCE.get('content'); // get editor instance
    content = wpb_ed.save();
    // }
    //PBPB: Patch for visual composer + tinymce + acf
    if (content == '') {
        content = jQuery('#content').val();
    }

    return content;
}
/* This function adds a class name to the div#drag_placeholder,
 and this helps us to give a style to the draging placeholder
 ---------------------------------------------------------- */
function renderCorrectPlaceholder(event, ui) {
    jQuery("#drag_placeholder").addClass("column_placeholder").html(i18nLocale.drag_drop_me_in_column);
}
var $win, $nav, navTop, isFixed = 0;
function wpb_navOnScroll() {
    $win = jQuery(window);
    $nav = jQuery('#wpb_visual_composer-elements');
    navTop = jQuery('#wpb_visual_composer-elements').length && jQuery('#wpb_visual_composer-elements').offset().top - 28;
    isFixed = 0;

    wpb_processScroll();
    $win.on('scroll', wpb_processScroll);
}
function wpb_processScroll() {
    var i,
        scrollTop = $win.scrollTop();

    if (scrollTop >= navTop && !isFixed) {
        isFixed = 1;
        $nav.addClass('subnav-fixed')
    } else if (scrollTop <= navTop && isFixed) {
        isFixed = 0;
        $nav.removeClass('subnav-fixed');
    }
}

function save_composer_html() {
    jQuery.jsComposer.save_composer_html();
}
