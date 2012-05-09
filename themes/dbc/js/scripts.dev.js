/* 
 * Compress the contents of this file with http://www.refresh-sf.com/yui/
 * Paste the results into /dbc/library/js/scripts.js
 */
        
/*
quickTree 0.4 - Simple jQuery plugin to create tree-structure navigation from an unordered list
http://scottdarby.com/

Copyright (c) 2009 Scott Darby

Dual licensed under the MIT and GPL licenses:
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html
*/
jQuery.fn.quickTree = function() {
    return this.each(function(){

        //set variables
        var $tree = jQuery(this);
        var $roots = $tree.find('li');

        //set last list-item as variable (to allow different background graphic to be applied)
        $tree.find('li:last-child').addClass('last');

        //add class to allow styling
        $tree.addClass('tree');

        //hide all lists inside of main list by default
        $tree.find('ul').hide();

        //iterate through all list items
        $roots.each(function(){

            //if list-item contains a child list
            if (jQuery(this).children('ul').length > 0) {

                //add expand/contract control
                jQuery(this).addClass('root').prepend('<span class="toggle off" />');

            }

        }); //end .each

        //handle clicking on expand/contract control
        jQuery('span.expand').toggle(
            //if it's clicked once, find all child lists and expand
            function(){
                jQuery(this).toggleClass('contract').nextAll('ul').show();
            },
            //if it's clicked again, find all child lists and contract
            function(){
                jQuery(this).toggleClass('contract').nextAll('ul').hide();
            }
            );
    });
};

/*jslint browser: true */ /*global jQuery: true */

/**
 * jQuery Cookie plugin
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

// TODO JsDoc

/**
 * Create a cookie with the given key and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *       used when the cookie was set.
 *
 * @param String key The key of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given key.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String key The key of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
jQuery.cookie = function (key, value, options) {
    
    // key and at least value given, set cookie...
    if (arguments.length > 1 && String(value) !== "[object Object]") {
        options = jQuery.extend({}, options);

        if (value === null || value === undefined) {
            options.expires = -1;
        }

        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }
        
        value = String(value);
        
        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? value : encodeURIComponent(value),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }

    // key and possibly options given, get cookie...
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};


/*
 * jQuery Sticky Sidebar
 * http://www.profilepicture.co.uk/tutorials/sticky-sidebar-jquery-plugin/
 */

(function ($) {"use strict";

	var settings = {
		speed : 350,//animation duration
		easing : "linear",//use easing plugin for more options
		padding : 10,
		constrain : false
	}, $window = $(window), stickyboxes = [], methods = {

		init : function (opts) {
			settings = $.extend(settings, opts);
			return this.each(function () {
				var $this = $(this);
				setPosition($this);
				stickyboxes[stickyboxes.length] = $this;
				moveIntoView();
			});
		},
		remove : function () {
			return this.each(function () {
				var sticky = this;
				$.each(stickyboxes, function (i, $sb) {
					if ($sb.get(0) === sticky) {
						reset(null, $sb);
						stickyboxes.splice(i, 1);
						return false;
					}
				});
			});
		},
		destroy : function () {
			$.each(stickyboxes, function (i, $sb) {
				reset(null, $sb);
			});
			stickyboxes = [];
			$window.unbind("scroll", moveIntoView);
			$window.unbind("resize", reset);
			return this;
		}
	};

	var moveIntoView = function () {
		$.each(stickyboxes, function (i, $sb) {
			var $this = $sb, data = $this.data("stickySB");
			if (data) {
				var sTop = $window.scrollTop() - data.offs.top, currOffs = $this.offset(), origTop = data.orig.offset.top - data.offs.top, animTo = origTop;
				//scrolled down out of view
				if (origTop < sTop) {
					//make sure to stop inside parent
					if ((sTop + settings.padding) > data.offs.bottom) {
						animTo = data.offs.bottom;
					} else {
						animTo = sTop + settings.padding;
					}
				}
				$this.stop().animate({
					top : animTo
				}, settings.speed, settings.easing);
			}
		});
	}
	var setPosition = function ($sb) {
		if ($sb) {
			var $this = $sb, $parent = $this.parent(), parentOffs = $parent.offset(), currOff = $this.offset(), data = $this.data("stickySB");
			if (!data) {
				data = {
					offs : {}// our parents offset
					,
					orig : {// cache for original css
						top : $this.css("top"),
						left : $this.css("left"),
						position : $this.css("position"),
						marginTop : $this.css("marginTop"),
						marginLeft : $this.css("marginLeft"),
						offset : $this.offset()
					}
				}
			}
			//go up the tree until we find an elem to position from
			while (parentOffs && "top" in parentOffs && $parent.css("position") === "static") {
				$parent = $parent.parent();
				parentOffs = $parent.offset();
			}
			if (parentOffs) {// found a postioned ancestor
				var padBtm = parseInt($parent.css("paddingBottom"));
				padBtm = isNaN(padBtm) ? 0 : padBtm;
				data.offs = parentOffs;
				data.offs.bottom = settings.constrain ? Math.abs(($parent.innerHeight() - padBtm) - $this.outerHeight()) : $(document).height();
			} else {
				data.offs = {// went to far set to doc
					top : 0,
					left : 0,
					bottom : $(document).height()
				};
			}
			$this.css({
				position : "absolute",
				top : Math.floor(currOff.top - data.offs.top) + "px",
				left : Math.floor(currOff.left - data.offs.left) + "px",
				margin : 0,
				width : $this.width()
			}).data("stickySB", data);
		}
	}
	var reset = function (ev, $toReset) {
		var stickies = stickyboxes;
		if ($toReset) {// just resetting selected items
			stickies = [$toReset];
		}
		$.each(stickies, function (i, $sb) {
			var data = $sb.data("stickySB");
			if (data) {
				$sb.css({
					position : data.orig.position,
					marginTop : data.orig.marginTop,
					marginLeft : data.orig.marginLeft,
					left : data.orig.left,
					top : data.orig.top
				});
				if(!$toReset) {// just resetting
					setPosition($sb);
					moveIntoView();
				}
			}
		});
	}

	$window.bind("scroll", moveIntoView);
	$window.bind("resize", reset);

	$.fn.stickySidebar = function (method) {

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (!method || typeof method == "object") {
			return methods.init.apply(this, arguments);
		}

	}
})(jQuery);
			
/*
 * Gentlemen, start your engines!
 */
jQuery(document).ready(function($) {

    $('#myfacebookbox').append('<div class="fb-like-box" data-href="http://www.facebook.com/dentonbible" data-height="422" data-show-faces="false" data-stream="true" data-header="false" data-force-wall="true"></div>'); 
  
    jQuery.getScript('http://connect.facebook.net/en_US/all.js#xfbml=1', function() { 
        FB.init({status: true, cookie: true, xfbml: true}); 
    }); 
    
	// for PDF auto-detection
		$('a[href$=".pdf"]').addClass('fancybox-pdf');
	
	// setup FB for PDF using type iframe
		$('a.fancybox-pdf').fancybox({
		    'type'          : 'iframe',
		    'titleShow'     : false,
		    'autoScale'     : false,
		    'width'         : '80%',
		    'height'		: '90%'	
		});

	// Publication pagination
		$('#first-cup-pagination a').live('click', function(e){
			e.preventDefault();
			var link = $(this).attr('href');
			$('#first-cup').fadeOut(500).load(link + ' #first-cup-publications-inner', function(){ $('#first-cup').fadeIn(500); });
		});
	
		$('#common-ground-pagination a').live('click', function(e){
			e.preventDefault();
			var link = $(this).attr('href');
			$('#common-ground').fadeOut(500).load(link + ' #common-ground-publications-inner', function(){ $('#common-ground').fadeIn(500); });
		});
	
	//Opens links with rel="external" in a new window
		$('a[rel="external"]').click(function(){ // When a link with rel="external"is clicked ...
			window.open( $(this).attr('href') ); // ... open a new window using the link's href attribute ...
			return false; // ... and prevent the default browser functionality (opening the link in the same window).
		});
	
	//Converts "safe" email links with a class of "email" into mailto links
		jQuery.fn.mailto = function() {
			return this.each(function(){
				var email = $(this).html().replace(/\s*\(.+\)\s*/, "@");
				$(this).before('<a href="mailto:' + email + '" rel="nofollow" title="Email ' + email + '">' + email + '</a>').remove();
			});
		};
		$('.email').mailto();
	
	//Sidebar navigation expand/contract
		$('ul.quickTree').quickTree();
		$('.xoxo.pages').quickTree();
		$('.off').click(
			function () {
				$(this).toggleClass('contract');
				$(this).toggleClass('off');
				$(this).siblings('ul').slideToggle('fast');
			}
		);
		$('.contract').click(
			function () {				
				$(this).toggleClass('contract');
				$(this).toggleClass('off');
				$(this).siblings('ul').slideToggle('fast');				
			}
		);
		$('.current_page_item .toggle').toggleClass('contract').siblings('.children').slideToggle('fast');
		$('.current_page_ancestor .toggle').toggleClass('contract').siblings('.children').slideToggle('fast');
	
	//Slider
		$('.flexslider').flexslider({
			animation:			'slide',
			controlsContainer:	'.flex-container',
			pausePlay:			false
		});
         
     //Sticky sidebars
     	$('#sidebar-sticky').stickySidebar();

});