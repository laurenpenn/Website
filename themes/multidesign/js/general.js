/** Page Slide **/
(function($){
  $.fn.pageSlide = function(options) {
    
    var settings = $.extend({
		    width:          "300px", // Accepts fixed widths
		    duration:       "normal", // Accepts standard jQuery effects speeds (i.e. fast, normal or milliseconds)
		    direction:      "left", // default direction is left.
		    modal:          false, // if true, the only way to close the pageslide is to define an explicit close class. 
		    _identifier: $(this)
		}, options);
		
		// these are the minimum css requirements for the pageslide elements introduced in this plugin.
		
		var pageslide_slide_wrap_css = {
		  position: 'fixed',
      width: '0',
      top: '0',
      height: '100%',
      zIndex:'999'
		};
		
		var pageslide_body_wrap_css = {
		  position: 'relative',
		  zIndex: '0'
		};
		
		var pageslide_blanket_css = { 
	    position: 'absolute',
	    top: '0px',
	    left: '0px',
	    height: '100%',
	    width: '100%', 
	    opacity: '0.0',
	    backgroundColor: 'black',
	    zIndex: '1',
	    display: 'none'
	  };
		
		function _initialize(anchor) {
      
      // Create and prepare elements for pageSlide
      if ($("#pageslide-body-wrap, #pageslide-content, #pageslide-slide-wrap").size() == 0) {
        
        var psBodyWrap = document.createElement("div");
        $(psBodyWrap).css(pageslide_body_wrap_css);
        $(psBodyWrap).attr("id","pageslide-body-wrap").width( $("body").width() );
        $("body").contents().wrapAll( psBodyWrap );
  	    
        var psSlideContent = document.createElement("div");
        $(psSlideContent).attr("id","pageslide-content").width( settings.width );

        var psSlideWrap = document.createElement("div");
        $(psSlideWrap).css(pageslide_slide_wrap_css);
        $(psSlideWrap).attr("id","pageslide-slide-wrap").append( psSlideContent );
        $("body").append( psSlideWrap );
  	    
      }
      
      // introduce the blanket if modal option is set to true.
      if ($("#pageslide-blanket").size() == 0 && settings.modal == true) {
        var psSlideBlanket = document.createElement("div");
        $(psSlideBlanket).css(pageslide_blanket_css);
        $(psSlideBlanket).attr("id","pageslide-blanket");
        $("body").append( psSlideBlanket );
  	    $("#pageslide-blanket").click(function(){ return false; });
      }
          	    
	    // Callback events for window resizing
	    $(window).resize(function(){
        $("#pageslide-body-wrap").width( $("body").width() );
      });
      
      // mark the anchor!
      $(anchor).attr("rel","pageslide");
      
	  };
	  
		function _openSlide(elm) {
		  if($("#pageslide-slide-wrap").width() != 0) return false;
		  _showBlanket();
		  // decide on a direction
		  if (settings.direction == "right") {
		    direction = {right:"-"+settings.width};
		    $("#pageslide-slide-wrap").css({left:0});
        _overflowFixAdd();
		  } 
		  else {
		    direction = {left:"-"+settings.width};
		    $("#pageslide-slide-wrap").css({right:0});
		  }
    	$("#pageslide-slide-wrap").animate({width: settings.width}, settings.duration);
		  $("#pageslide-body-wrap").animate(direction, settings.duration, function() {
	      $.ajax({
  		      type: "GET",
  		      url: $(elm).attr("href"),
  		      success: function(data){
  		        $("#pageslide-content").css("width",settings.width).html(data)
  		          .queue(function(){
  		            $(this).dequeue();
  		            
  		            // add hook for a close button
  		            $(this).find('.pageslide-close').unbind('click').click(function(elm){
  		              _closeSlide(elm);
  		              $(this).find('pageslide-close').unbind('click');
  		            });
  		          });
  		      }
  		    });
		  });
		};
		
		function _closeSlide(event) {
		  if ($(event)[0].button != 2 && $("#pageslide-slide-wrap").css('width') != "0px") { // if not right click.
        $.fn.pageSlideClose(settings);
      }
		};
		
		// this is used to activate the modal blanket, if the modal setting is defined as true.
		function _showBlanket() {
	    if(settings.modal == true) {
	      $("#pageslide-blanket").toggle().animate({opacity:'0.8'}, 'fast','linear');
	    }
	  };

	  // fixes an annoying horizontal scrollbar.
	  function _overflowFixAdd(){($.browser.msie) ? $("body, html").css({overflowX:'hidden'}) : $("body").css({overflowX:'hidden'});}
		
    // Initalize pageslide, if it hasn't already been done.
    _initialize(this);
    return this.each(function(){
      $(this).unbind("click").bind("click", function(){
    	  _openSlide(this);
    	  $("#pageslide-slide-wrap").unbind('click').click(function(e){ if(e.target.tagName != "A") return false; });	  
    	  if (settings.modal != true) {
  	      $(document).unbind('click').click(function(e) { if(e.target.tagName != "A"){ _closeSlide(e); return false } });
  	    }
    	  return false;
    	});	
    });
    
  };
})(jQuery);

// pageSlideClose allows the system to automatically close any pageslide that is currently open in the view.
(function($){
  $.fn.pageSlideClose = function(options) {
    
    var settings = $.extend({
		    width:          "300px", // Accepts fixed widths
		    duration:       "normal", // Accepts standard jQuery effects speeds (i.e. fast, normal or milliseconds)
		    direction:      "left", // default direction is left.
		    modal:          false, // if true, the only way to close the pageslide is to define an explicit close class. 
		    _identifier: $(this)
		}, options);
		
		function _hideBlanket() { if(settings.modal == true && $("#pageslide-blanket").is(":visible")) {
      $("#pageslide-blanket").animate({opacity:'0.0'}, 'fast','linear',function(){$(this).hide();});
    }}
    
    function _overflowFixRemove(){($.browser.msie) ? $("body, html").css({overflowX:''}) : $("body").css({overflowX:''});}
		
    _hideBlanket();
    direction = ($("#pageslide-slide-wrap").css("left") != "0px") ? {left: "0"} : {right: "0"};
	  $("#pageslide-body-wrap").animate(direction, settings.duration);
    $("#pageslide-slide-wrap").animate({width: "0"}, settings.duration, function() {
      // clear bug
      $("#pageslide-content").css("width","0px").empty();
      $('#pageslide-body-wrap, #pageslide-slide-wrap').css('left','');
      $('#pageslide-body-wrap, #pageslide-slide-wrap').css('right','');
      _overflowFixRemove();
    });
    
  }
})(jQuery);

// this adds the ability to close pageSlide with the 'escape' key, if not modal.
(function($){
  $(document).ready(function(){
    $(document).keyup(function(event){
      if (!$("#pageslide-blanket").is(":visible") && event.keyCode == 27) $.fn.pageSlideClose();
    });
  });
})(jQuery);


/** Fon Style **/
if (document.layers) { 
document.write('<link rel=stylesheet href="js/font.css">') 
} 
else { 
document.write('<link rel=stylesheet href="js/font.css">') 
}

/** Qtips - Tool Tips **/
// qTip - CSS Tool Tips - by Craig Erskine
// http://qrayg.com
//
// Multi-tag support by James Crooke
// http://www.cj-design.com
//
// Inspired by code from Travis Beckham
// http://www.squidfingers.com | http://www.podlob.com
//
// Copyright (c) 2006 Craig Erskine
// Permission is granted to copy, distribute and/or modify this document
// under the terms of the GNU Free Documentation License, Version 1.3
// or any later version published by the Free Software Foundation;
// with no Invariant Sections, no Front-Cover Texts, and no Back-Cover Texts.
// A copy of the license is included in the section entitled "GNU
// Free Documentation License".

var qTipTag = "a,label,input"; //Which tag do you want to qTip-ize? Keep it lowercase!//
var qTipX = 0; //This is qTip's X offset//
var qTipY = 15; //This is qTip's Y offset//

//There's No need to edit anything below this line//
tooltip = {
  name : "qTip",
  offsetX : qTipX,
  offsetY : qTipY,
  tip : null
}

tooltip.init = function () {
	var tipNameSpaceURI = "http://www.w3.org/1999/xhtml";
	if(!tipContainerID){ var tipContainerID = "qTip";}
	var tipContainer = document.getElementById(tipContainerID);

	if(!tipContainer) {
	  tipContainer = document.createElementNS ? document.createElementNS(tipNameSpaceURI, "div") : document.createElement("div");
		tipContainer.setAttribute("id", tipContainerID);
	  document.getElementsByTagName("body").item(0).appendChild(tipContainer);
	}

	if (!document.getElementById) return;
	this.tip = document.getElementById (this.name);
	if (this.tip) document.onmousemove = function (evt) {tooltip.move (evt)};

	var a, sTitle, elements;
	
	var elementList = qTipTag.split(",");
	for(var j = 0; j < elementList.length; j++)
	{	
		elements = document.getElementsByTagName(elementList[j]);
		if(elements)
		{
			for (var i = 0; i < elements.length; i ++)
			{
				a = elements[i];
				sTitle = a.getAttribute("title");				
				if(sTitle)
				{
					a.setAttribute("tiptitle", sTitle);
					a.removeAttribute("title");
					a.removeAttribute("alt");
					a.onmouseover = function() {tooltip.show(this.getAttribute('tiptitle'))};
					a.onmouseout = function() {tooltip.hide()};
				}
			}
		}
	}
}

tooltip.move = function (evt) {
	var x=0, y=0;
	if (document.all) {//IE
		x = (document.documentElement && document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
		y = (document.documentElement && document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
		x += window.event.clientX;
		y += window.event.clientY;
		
	} else {//Good Browsers
		x = evt.pageX;
		y = evt.pageY;
	}
	this.tip.style.left = (x + this.offsetX) + "px";
	this.tip.style.top = (y + this.offsetY) + "px";
}

tooltip.show = function (text) {
	if (!this.tip) return;
	this.tip.innerHTML = text;
	this.tip.style.display = "block";
}

tooltip.hide = function () {
	if (!this.tip) return;
	this.tip.innerHTML = "";
	this.tip.style.display = "none";
}

window.onload = function () {
	tooltip.init ();
}

/** Fancy Popup **/

$(document).ready(function() {
			/*
			*   Examples - images
			*/

			$("a#example1").fancybox();

			$("a#picture").fancybox({
				'overlayShow'	: true,
				'transitionIn'	: 'elastic',
				'transitionOut'	: 'elastic',
				'overlayColor'		: '#000',
				'overlayOpacity'	: 0.9
			});
			
			$("a.picture").fancybox({
				'overlayShow'	: true,
				'transitionIn'	: 'elastic',
				'transitionOut'	: 'elastic',
				'overlayColor'		: '#000',
				'overlayOpacity'	: 0.9
			});
			
			$("a.ajaxpicture").fancybox({
				'overlayShow'	: true,
				'transitionIn'	: 'elastic',
				'transitionOut'	: 'elastic',
				'overlayColor'		: '#000',
				'overlayOpacity'	: 0.9
			});
			
			$("a#iframe").fancybox({
				'overlayShow'	: true,
				'transitionIn'	: 'none',
				'transitionOut'	: 'none',
				'autoScale'			: false,
				'type'				: 'iframe',
				'overlayColor'		: '#000',
				'overlayOpacity'	: 0.9
			});
			
			$("a.iframe").fancybox({
				'width'				: '100%',
				'height'			: '100%',
				'overlayShow'	: true,
				'transitionIn'	: 'none',
				'transitionOut'	: 'none',
				'autoScale'			: true,
				'type'				: 'iframe',
				'overlayColor'		: '#000',
				'overlayOpacity'	: 0.9
			});
			
			$("a.popup").fancybox({
				'width'				: '100%',
				'height'			: '100%',
				'overlayShow'	: true,
				'transitionIn'	: 'none',
				'transitionOut'	: 'none',
				'autoScale'			: true,
				'type'				: 'iframe',
				'overlayColor'		: '#000',
				'overlayOpacity'	: 0.9
			});

			$("a#code").fancybox({
				'overlayShow'	: true,
				'transitionIn'	: 'none',
				'transitionOut'	: 'none',
				'titlePosition'		: 'inside',
				'overlayColor'		: '#000',
				'overlayOpacity'	: 0.9
			});
			
			$("a.code").fancybox({
				'overlayShow'	: true,
				'transitionIn'	: 'none',
				'transitionOut'	: 'none',
				'titlePosition'		: 'inside',
				'overlayColor'		: '#000',
				'overlayOpacity'	: 0.9
				
			});
			
			$("a.comment").fancybox({
				'overlayShow'	: true,
				'transitionIn'	: 'none',
				'transitionOut'	: 'none',
				'titlePosition'		: 'inside',
				'overlayColor'		: '#be324c',
				'overlayOpacity'	: 0.9
			});
			
			$("a.login").fancybox({
				'overlayShow'	: true,
				'transitionIn'	: 'none',
				'transitionOut'	: 'none',
				'titlePosition'		: 'inside',
				'overlayColor'		: '#158fd0',
				'overlayOpacity'	: 0.9
			});
			
			$("a.signup").fancybox({
				'overlayShow'	: true,
				'transitionIn'	: 'none',
				'transitionOut'	: 'none',
				'titlePosition'		: 'inside',
				'overlayColor'		: '#b2d22f',
				'overlayOpacity'	: 0.9
			});
			
			$("a.search").fancybox({
				'overlayShow'	: true,
				'transitionIn'	: 'none',
				'transitionOut'	: 'none',
				'titlePosition'		: 'inside',
				'overlayColor'		: '#f37123',
				'overlayOpacity'	: 0.9
			});

			$("a#example5").fancybox();

			$("a#example6").fancybox({
				'titlePosition'		: 'outside',
				'overlayColor'		: '#000',
				'overlayOpacity'	: 0.9
			});

			$("a#example7").fancybox({
				'titlePosition'	: 'inside'
			});

			$("a#example8").fancybox({
				'titlePosition'	: 'over'
			});

			$("a[rel=picture-album]").fancybox({
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'titlePosition' 	: 'over',
				'overlayColor'		: '#000',
				'overlayOpacity'	: 0.9,
				'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
					return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
				}
			});

			/*
			*   Examples - various
			*/

			$("#various1").fancybox({
				'titlePosition'		: 'inside',
				'transitionIn'		: 'none',
				'transitionOut'		: 'none'
			});

			$("#various2").fancybox();

			$("#various3").fancybox({
				'width'				: '75%',
				'height'			: '75%',
				'autoScale'			: false,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type'				: 'iframe'
			});

			$("#various4").fancybox({
				'padding'			: 0,
				'autoScale'			: false,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none'
			});
		});
		
/** Super Fish **/
/*
 * Superfish v1.4.8 - jQuery menu widget
 * Copyright (c) 2008 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 * CHANGELOG: http://users.tpg.com.au/j_birch/plugins/superfish/changelog.txt
 */

;(function($){
	$.fn.superfish = function(op){

		var sf = $.fn.superfish,
			c = sf.c,
			$arrow = $(['<span class="',c.arrowClass,'"> &#187;</span>'].join('')),
			over = function(){
				var $$ = $(this), menu = getMenu($$);
				clearTimeout(menu.sfTimer);
				$$.showSuperfishUl().siblings().hideSuperfishUl();
			},
			out = function(){
				var $$ = $(this), menu = getMenu($$), o = sf.op;
				clearTimeout(menu.sfTimer);
				menu.sfTimer=setTimeout(function(){
					o.retainPath=($.inArray($$[0],o.$path)>-1);
					$$.hideSuperfishUl();
					if (o.$path.length && $$.parents(['li.',o.hoverClass].join('')).length<1){over.call(o.$path);}
				},o.delay);	
			},
			getMenu = function($menu){
				var menu = $menu.parents(['ul.',c.menuClass,':first'].join(''))[0];
				sf.op = sf.o[menu.serial];
				return menu;
			},
			addArrow = function($a){ $a.addClass(c.anchorClass).append($arrow.clone()); };
			
		return this.each(function() {
			var s = this.serial = sf.o.length;
			var o = $.extend({},sf.defaults,op);
			o.$path = $('li.'+o.pathClass,this).slice(0,o.pathLevels).each(function(){
				$(this).addClass([o.hoverClass,c.bcClass].join(' '))
					.filter('li:has(ul)').removeClass(o.pathClass);
			});
			sf.o[s] = sf.op = o;
			
			$('li:has(ul)',this)[($.fn.hoverIntent && !o.disableHI) ? 'hoverIntent' : 'hover'](over,out).each(function() {
				if (o.autoArrows) addArrow( $('>a:first-child',this) );
			})
			.not('.'+c.bcClass)
				.hideSuperfishUl();
			
			var $a = $('a',this);
			$a.each(function(i){
				var $li = $a.eq(i).parents('li');
				$a.eq(i).focus(function(){over.call($li);}).blur(function(){out.call($li);});
			});
			o.onInit.call(this);
			
		}).each(function() {
			var menuClasses = [c.menuClass];
			if (sf.op.dropShadows  && !($.browser.msie && $.browser.version < 7)) menuClasses.push(c.shadowClass);
			$(this).addClass(menuClasses.join(' '));
		});
	};

	var sf = $.fn.superfish;
	sf.o = [];
	sf.op = {};
	sf.IE7fix = function(){
		var o = sf.op;
		if ($.browser.msie && $.browser.version > 6 && o.dropShadows && o.animation.opacity!=undefined)
			this.toggleClass(sf.c.shadowClass+'-off');
		};
	sf.c = {
		bcClass     : 'sf-breadcrumb',
		menuClass   : 'sf-js-enabled',
		anchorClass : 'sf-with-ul',
		arrowClass  : 'sf-sub-indicator',
		shadowClass : 'sf-shadow'
	};
	sf.defaults = {
		hoverClass	: 'sfHover',
		pathClass	: 'overideThisToUse',
		pathLevels	: 1,
		delay		: 800,
		animation	: {opacity:'show'},
		speed		: 'normal',
		autoArrows	: true,
		dropShadows : true,
		disableHI	: false,		// true disables hoverIntent detection
		onInit		: function(){}, // callback functions
		onBeforeShow: function(){},
		onShow		: function(){},
		onHide		: function(){}
	};
	$.fn.extend({
		hideSuperfishUl : function(){
			var o = sf.op,
				not = (o.retainPath===true) ? o.$path : '';
			o.retainPath = false;
			var $ul = $(['li.',o.hoverClass].join(''),this).add(this).not(not).removeClass(o.hoverClass)
					.find('>ul').hide().css('visibility','hidden');
			o.onHide.call($ul);
			return this;
		},
		showSuperfishUl : function(){
			var o = sf.op,
				sh = sf.c.shadowClass+'-off',
				$ul = this.addClass(o.hoverClass)
					.find('>ul:hidden').css('visibility','visible');
			sf.IE7fix.call($ul);
			o.onBeforeShow.call($ul);
			$ul.animate(o.animation,o.speed,function(){ sf.IE7fix.call($ul); o.onShow.call($ul); });
			return this;
		}
	});

})(jQuery);
jQuery(function(){ jQuery('ul.menu').superfish(); });
(function($){
	/* hoverIntent by Brian Cherne */
	$.fn.hoverIntent = function(f,g) {
		// default configuration options
		var cfg = {
			sensitivity: 7,
			interval: 100,
			timeout: 0
		};
		// override configuration options with user supplied object
		cfg = $.extend(cfg, g ? { over: f, out: g } : f );

		// instantiate variables
		// cX, cY = current X and Y position of mouse, updated by mousemove event
		// pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
		var cX, cY, pX, pY;

		// A private function for getting mouse position
		var track = function(ev) {
			cX = ev.pageX;
			cY = ev.pageY;
		};

		// A private function for comparing current and previous mouse position
		var compare = function(ev,ob) {
			ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
			// compare mouse positions to see if they've crossed the threshold
			if ( ( Math.abs(pX-cX) + Math.abs(pY-cY) ) < cfg.sensitivity ) {
				$(ob).unbind("mousemove",track);
				// set hoverIntent state to true (so mouseOut can be called)
				ob.hoverIntent_s = 1;
				return cfg.over.apply(ob,[ev]);
			} else {
				// set previous coordinates for next time
				pX = cX; pY = cY;
				// use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
				ob.hoverIntent_t = setTimeout( function(){compare(ev, ob);} , cfg.interval );
			}
		};

		// A private function for delaying the mouseOut function
		var delay = function(ev,ob) {
			ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
			ob.hoverIntent_s = 0;
			return cfg.out.apply(ob,[ev]);
		};

		// A private function for handling mouse 'hovering'
		var handleHover = function(e) {
			// next three lines copied from jQuery.hover, ignore children onMouseOver/onMouseOut
			var p = (e.type == "mouseover" ? e.fromElement : e.toElement) || e.relatedTarget;
			while ( p && p != this ) { try { p = p.parentNode; } catch(e) { p = this; } }
			if ( p == this ) { return false; }

			// copy objects to be passed into t (required for event object to be passed in IE)
			var ev = jQuery.extend({},e);
			var ob = this;

			// cancel hoverIntent timer if it exists
			if (ob.hoverIntent_t) { ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t); }

			// else e.type == "onmouseover"
			if (e.type == "mouseover") {
				// set "previous" X and Y position based on initial entry point
				pX = ev.pageX; pY = ev.pageY;
				// update "current" X and Y position based on mousemove
				$(ob).bind("mousemove",track);
				// start polling interval (self-calling timeout) to compare mouse coordinates over time
				if (ob.hoverIntent_s != 1) { ob.hoverIntent_t = setTimeout( function(){compare(ev,ob);} , cfg.interval );}

			// else e.type == "onmouseout"
			} else {
				// unbind expensive mousemove event
				$(ob).unbind("mousemove",track);
				// if hoverIntent state is true, then call the mouseOut function after the specified delay
				if (ob.hoverIntent_s == 1) { ob.hoverIntent_t = setTimeout( function(){delay(ev,ob);} , cfg.timeout );}
			}
		};

		// bind the function to the two event listeners
		return this.mouseover(handleHover).mouseout(handleHover);
	};
	
})(jQuery);

/*
* Slides, A Slideshow Plugin for jQuery
* Intructions: http://slidesjs.com
* By: Nathan Searles, http://nathansearles.com
* Version: 1.0.9
* Updated: January 4th, 2011
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

(function($){$.fn.slides=function(option){option=$.extend({},$.fn.slides.option,option);return this.each(function(){$('.'+option.container,$(this)).children().wrapAll('<div class="slides_control"/>');var elem=$(this),control=$('.slides_control',elem),total=control.children().size(),width=control.children().outerWidth(),height=control.children().outerHeight(),start=option.start-1,effect=option.effect.indexOf(',')<0?option.effect:option.effect.replace(' ','').split(',')[0],paginationEffect=option.effect.indexOf(',')<0?effect:option.effect.replace(' ','').split(',')[1],next=0,prev=0,number=0,current=0,loaded,active,clicked,position,direction;if(total<2){return;}
if(start<0){start=0;};if(start>total){start=total-1;};if(option.start){current=start;};if(option.randomize){control.randomize();}
$('.'+option.container,elem).css({overflow:'hidden',position:'relative'});control.css({position:'relative',width:(width*3),height:height,left:-width});control.children().css({position:'absolute',top:0,left:width,zIndex:0,display:'none'});if(option.autoHeight){control.animate({height:control.children(':eq('+start+')').outerHeight()},option.autoHeightSpeed);}
if(option.preload&&control.children()[0].tagName=='IMG'){elem.css({background:'url('+option.preloadImage+') no-repeat 50% 50%'});var img=$('img:eq('+start+')',elem).attr('src')+'?'+(new Date()).getTime();$('img:eq('+start+')',elem).attr('src',img).load(function(){$(this).fadeIn(option.fadeSpeed,function(){$(this).css({zIndex:5});elem.css({background:''});loaded=true;});});}else{control.children(':eq('+start+')').fadeIn(option.fadeSpeed,function(){loaded=true;});}
if(option.bigTarget){control.children().css({cursor:'pointer'});control.children().click(function(){animate('next',effect);return false;});}
if(option.hoverPause&&option.play){control.children().bind('mouseover',function(){stop();});control.children().bind('mouseleave',function(){pause();});}
if(option.generateNextPrev){$('.'+option.container,elem).after('<a href="#" class="'+option.prev+'">Prev</a>');$('.'+option.prev,elem).after('<a href="#" class="'+option.next+'">Next</a>');}
$('.'+option.next,elem).click(function(e){e.preventDefault();if(option.play){pause();};animate('next',effect);});$('.'+option.prev,elem).click(function(e){e.preventDefault();if(option.play){pause();};animate('prev',effect);});if(option.generatePagination){elem.append('<ul class='+option.paginationClass+'></ul>');control.children().each(function(){$('.'+option.paginationClass,elem).append('<li><a href="#'+number+'">'+(number+1)+'</a></li>');number++;});}else{$('.'+option.paginationClass+' li a',elem).each(function(){$(this).attr('href','#'+number);number++;});}
$('.'+option.paginationClass+' li:eq('+start+')',elem).addClass('current');$('.'+option.paginationClass+' li a',elem).click(function(){if(option.play){pause();};clicked=$(this).attr('href').match('[^#/]+$');if(current!=clicked){animate('pagination',paginationEffect,clicked);}
return false;});$('a.link',elem).click(function(){if(option.play){pause();};clicked=$(this).attr('href').match('[^#/]+$')-1;if(current!=clicked){animate('pagination',paginationEffect,clicked);}
return false;});if(option.play){playInterval=setInterval(function(){animate('next',effect);},option.play);elem.data('interval',playInterval);};function stop(){clearInterval(elem.data('interval'));};function pause(){if(option.pause){clearTimeout(elem.data('pause'));clearInterval(elem.data('interval'));pauseTimeout=setTimeout(function(){clearTimeout(elem.data('pause'));playInterval=setInterval(function(){animate("next",effect);},option.play);elem.data('interval',playInterval);},option.pause);elem.data('pause',pauseTimeout);}else{stop();}};function animate(direction,effect,clicked){if(!active&&loaded){active=true;switch(direction){case'next':prev=current;next=current+1;next=total===next?0:next;position=width*2;direction=-width*2;current=next;break;case'prev':prev=current;next=current-1;next=next===-1?total-1:next;position=0;direction=0;current=next;break;case'pagination':next=parseInt(clicked,10);prev=$('.'+option.paginationClass+' li.current a',elem).attr('href').match('[^#/]+$');if(next>prev){position=width*2;direction=-width*2;}else{position=0;direction=0;}
current=next;break;}
if(effect==='fade'){option.animationStart();if(option.crossfade){control.children(':eq('+next+')',elem).css({zIndex:10}).fadeIn(option.fadeSpeed,function(){if(option.autoHeight){control.animate({height:control.children(':eq('+next+')',elem).outerHeight()},option.autoHeightSpeed,function(){control.children(':eq('+prev+')',elem).css({display:'none',zIndex:0});control.children(':eq('+next+')',elem).css({zIndex:0});option.animationComplete(next+1);active=false;});}else{control.children(':eq('+prev+')',elem).css({display:'none',zIndex:0});control.children(':eq('+next+')',elem).css({zIndex:0});option.animationComplete(next+1);active=false;}});}else{option.animationStart();control.children(':eq('+prev+')',elem).fadeOut(option.fadeSpeed,function(){if(option.autoHeight){control.animate({height:control.children(':eq('+next+')',elem).outerHeight()},option.autoHeightSpeed,function(){control.children(':eq('+next+')',elem).fadeIn(option.fadeSpeed);});}else{control.children(':eq('+next+')',elem).fadeIn(option.fadeSpeed,function(){if($.browser.msie){$(this).get(0).style.removeAttribute('filter');}});}
option.animationComplete(next+1);active=false;});}}else{control.children(':eq('+next+')').css({left:position,display:'block'});if(option.autoHeight){option.animationStart();control.animate({left:direction,height:control.children(':eq('+next+')').outerHeight()},option.slideSpeed,function(){control.css({left:-width});control.children(':eq('+next+')').css({left:width,zIndex:5});control.children(':eq('+prev+')').css({left:width,display:'none',zIndex:0});option.animationComplete(next+1);active=false;});}else{option.animationStart();control.animate({left:direction},option.slideSpeed,function(){control.css({left:-width});control.children(':eq('+next+')').css({left:width,zIndex:5});control.children(':eq('+prev+')').css({left:width,display:'none',zIndex:0});option.animationComplete(next+1);active=false;});}}
if(option.pagination){$('.'+option.paginationClass+' li.current',elem).removeClass('current');$('.'+option.paginationClass+' li:eq('+next+')',elem).addClass('current');}}};});};$.fn.slides.option={preload:false,preloadImage:'/img/loading.gif',container:'slides_container',generateNextPrev:false,next:'next',prev:'prev',pagination:true,generatePagination:true,paginationClass:'pagination',fadeSpeed:350,slideSpeed:350,start:1,effect:'slide',crossfade:false,randomize:false,play:0,pause:0,hoverPause:false,autoHeight:false,autoHeightSpeed:350,bigTarget:false,animationStart:function(){},animationComplete:function(){}};$.fn.randomize=function(callback){function randomizeOrder(){return(Math.round(Math.random())-0.5);}
return($(this).each(function(){var $this=$(this);var $children=$this.children();var childCount=$children.length;if(childCount>1){$children.hide();var indices=[];for(i=0;i<childCount;i++){indices[indices.length]=i;}
indices=indices.sort(randomizeOrder);$.each(indices,function(j,k){var $child=$children.eq(k);var $clone=$child.clone(true);$clone.show().appendTo($this);if(callback!==undefined){callback($child,$clone);}
$child.remove();});}}));};})(jQuery);

/* =========================================================

// jquery.innerfade.js

// Datum: 2008-02-14
// Firma: Medienfreunde Hofmann & Baldes GbR
// Author: Torsten Baldes
// Mail: t.baldes@medienfreunde.com
// Web: http://medienfreunde.com

// based on the work of Matt Oakes http://portfolio.gizone.co.uk/applications/slideshow/
// and Ralf S. Engelschall http://trainofthoughts.org/

 *
 *  <ul id="news"> 
 *      <li>content 1</li>
 *      <li>content 2</li>
 *      <li>content 3</li>
 *  </ul>
 *  
 *  $('#news').innerfade({ 
 *	  animationtype: Type of animation 'fade' or 'slide' (Default: 'fade'), 
 *	  speed: Fading-/Sliding-Speed in milliseconds or keywords (slow, normal or fast) (Default: 'normal'), 
 *	  timeout: Time between the fades in milliseconds (Default: '2000'), 
 *	  type: Type of slideshow: 'sequence', 'random' or 'random_start' (Default: 'sequence'), 
 * 		containerheight: Height of the containing element in any css-height-value (Default: 'auto'),
 *	  runningclass: CSS-Class which the container get's applied (Default: 'innerfade'),
 *	  children: optional children selector (Default: null)
 *  }); 
 *

// ========================================================= */


(function($) {

    $.fn.innerfade = function(options) {
        return this.each(function() {   
            $.innerfade(this, options);
        });
    };

    $.innerfade = function(container, options) {
        var settings = {
        		'animationtype':    'fade',
            'speed':            'normal',
            'type':             'sequence',
            'timeout':          2000,
            'containerheight':  'auto',
            'runningclass':     'innerfade',
            'children':         null
        };
        if (options)
            $.extend(settings, options);
        if (settings.children === null)
            var elements = $(container).children();
        else
            var elements = $(container).children(settings.children);
        if (elements.length > 1) {
            $(container).css('position', 'relative').css('height', settings.containerheight).addClass(settings.runningclass);
            for (var i = 0; i < elements.length; i++) {
                $(elements[i]).css('z-index', String(elements.length-i)).css('position', 'absolute').hide();
            };
            if (settings.type == "sequence") {
                setTimeout(function() {
                    $.innerfade.next(elements, settings, 1, 0);
                }, settings.timeout);
                $(elements[0]).show();
            } else if (settings.type == "random") {
            		var last = Math.floor ( Math.random () * ( elements.length ) );
                setTimeout(function() {
                    do { 
												current = Math.floor ( Math.random ( ) * ( elements.length ) );
										} while (last == current );             
										$.innerfade.next(elements, settings, current, last);
                }, settings.timeout);
                $(elements[last]).show();
						} else if ( settings.type == 'random_start' ) {
								settings.type = 'sequence';
								var current = Math.floor ( Math.random () * ( elements.length ) );
								setTimeout(function(){
									$.innerfade.next(elements, settings, (current + 1) %  elements.length, current);
								}, settings.timeout);
								$(elements[current]).show();
						}	else {
							alert('Innerfade-Type must either be \'sequence\', \'random\' or \'random_start\'');
						}
				}
    };

    $.innerfade.next = function(elements, settings, current, last) {
        if (settings.animationtype == 'slide') {
            $(elements[last]).slideUp(settings.speed);
            $(elements[current]).slideDown(settings.speed);
        } else if (settings.animationtype == 'fade') {
            $(elements[last]).fadeOut(settings.speed);
            $(elements[current]).fadeIn(settings.speed, function() {
							removeFilter($(this)[0]);
						});
        } else
            alert('Innerfade-animationtype must either be \'slide\' or \'fade\'');
        if (settings.type == "sequence") {
            if ((current + 1) < elements.length) {
                current = current + 1;
                last = current - 1;
            } else {
                current = 0;
                last = elements.length - 1;
            }
        } else if (settings.type == "random") {
            last = current;
            while (current == last)
                current = Math.floor(Math.random() * elements.length);
        } else
            alert('Innerfade-Type must either be \'sequence\', \'random\' or \'random_start\'');
        setTimeout((function() {
            $.innerfade.next(elements, settings, current, last);
        }), settings.timeout);
    };

})(jQuery);

// **** remove Opacity-Filter in ie ****
function removeFilter(element) {
	if(element.style.removeAttribute){
		element.style.removeAttribute('filter');
	}
}


/** Carusel Slider **/
/*!
 * jCarousel - Riding carousels with jQuery
 *   http://sorgalla.com/jcarousel/
 *
 * Copyright (c) 2006 Jan Sorgalla (http://sorgalla.com)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Built on top of the jQuery library
 *   http://jquery.com
 *
 * Inspired by the "Carousel Component" by Bill Scott
 *   http://billwscott.com/carousel/
 */

(function(i){var q={vertical:false,rtl:false,start:1,offset:1,size:null,scroll:3,visible:null,animation:"normal",easing:"swing",auto:0,wrap:null,initCallback:null,reloadCallback:null,itemLoadCallback:null,itemFirstInCallback:null,itemFirstOutCallback:null,itemLastInCallback:null,itemLastOutCallback:null,itemVisibleInCallback:null,itemVisibleOutCallback:null,buttonNextHTML:"<div></div>",buttonPrevHTML:"<div></div>",buttonNextEvent:"click",buttonPrevEvent:"click",buttonNextCallback:null,buttonPrevCallback:null, itemFallbackDimension:null},r=false;i(window).bind("load.jcarousel",function(){r=true});i.jcarousel=function(a,c){this.options=i.extend({},q,c||{});this.autoStopped=this.locked=false;this.buttonPrevState=this.buttonNextState=this.buttonPrev=this.buttonNext=this.list=this.clip=this.container=null;if(!c||c.rtl===undefined)this.options.rtl=(i(a).attr("dir")||i("html").attr("dir")||"").toLowerCase()=="rtl";this.wh=!this.options.vertical?"width":"height";this.lt=!this.options.vertical?this.options.rtl? "right":"left":"top";for(var b="",d=a.className.split(" "),f=0;f<d.length;f++)if(d[f].indexOf("jcarousel-skin")!=-1){i(a).removeClass(d[f]);b=d[f];break}if(a.nodeName.toUpperCase()=="UL"||a.nodeName.toUpperCase()=="OL"){this.list=i(a);this.container=this.list.parent();if(this.container.hasClass("jcarousel-clip")){if(!this.container.parent().hasClass("jcarousel-container"))this.container=this.container.wrap("<div></div>");this.container=this.container.parent()}else if(!this.container.hasClass("jcarousel-container"))this.container= this.list.wrap("<div></div>").parent()}else{this.container=i(a);this.list=this.container.find("ul,ol").eq(0)}b!==""&&this.container.parent()[0].className.indexOf("jcarousel-skin")==-1&&this.container.wrap('<div class=" '+b+'"></div>');this.clip=this.list.parent();if(!this.clip.length||!this.clip.hasClass("jcarousel-clip"))this.clip=this.list.wrap("<div></div>").parent();this.buttonNext=i(".jcarousel-next",this.container);if(this.buttonNext.size()===0&&this.options.buttonNextHTML!==null)this.buttonNext= this.clip.after(this.options.buttonNextHTML).next();this.buttonNext.addClass(this.className("jcarousel-next"));this.buttonPrev=i(".jcarousel-prev",this.container);if(this.buttonPrev.size()===0&&this.options.buttonPrevHTML!==null)this.buttonPrev=this.clip.after(this.options.buttonPrevHTML).next();this.buttonPrev.addClass(this.className("jcarousel-prev"));this.clip.addClass(this.className("jcarousel-clip")).css({overflow:"hidden",position:"relative"});this.list.addClass(this.className("jcarousel-list")).css({overflow:"hidden", position:"relative",top:0,margin:0,padding:0}).css(this.options.rtl?"right":"left",0);this.container.addClass(this.className("jcarousel-container")).css({position:"relative"});!this.options.vertical&&this.options.rtl&&this.container.addClass("jcarousel-direction-rtl").attr("dir","rtl");var j=this.options.visible!==null?Math.ceil(this.clipping()/this.options.visible):null;b=this.list.children("li");var e=this;if(b.size()>0){var g=0,k=this.options.offset;b.each(function(){e.format(this,k++);g+=e.dimension(this, j)});this.list.css(this.wh,g+100+"px");if(!c||c.size===undefined)this.options.size=b.size()}this.container.css("display","block");this.buttonNext.css("display","block");this.buttonPrev.css("display","block");this.funcNext=function(){e.next()};this.funcPrev=function(){e.prev()};this.funcResize=function(){e.reload()};this.options.initCallback!==null&&this.options.initCallback(this,"init");if(!r&&i.browser.safari){this.buttons(false,false);i(window).bind("load.jcarousel",function(){e.setup()})}else this.setup()}; var h=i.jcarousel;h.fn=h.prototype={jcarousel:"0.2.7"};h.fn.extend=h.extend=i.extend;h.fn.extend({setup:function(){this.prevLast=this.prevFirst=this.last=this.first=null;this.animating=false;this.tail=this.timer=null;this.inTail=false;if(!this.locked){this.list.css(this.lt,this.pos(this.options.offset)+"px");var a=this.pos(this.options.start,true);this.prevFirst=this.prevLast=null;this.animate(a,false);i(window).unbind("resize.jcarousel",this.funcResize).bind("resize.jcarousel",this.funcResize)}}, reset:function(){this.list.empty();this.list.css(this.lt,"0px");this.list.css(this.wh,"10px");this.options.initCallback!==null&&this.options.initCallback(this,"reset");this.setup()},reload:function(){this.tail!==null&&this.inTail&&this.list.css(this.lt,h.intval(this.list.css(this.lt))+this.tail);this.tail=null;this.inTail=false;this.options.reloadCallback!==null&&this.options.reloadCallback(this);if(this.options.visible!==null){var a=this,c=Math.ceil(this.clipping()/this.options.visible),b=0,d=0; this.list.children("li").each(function(f){b+=a.dimension(this,c);if(f+1<a.first)d=b});this.list.css(this.wh,b+"px");this.list.css(this.lt,-d+"px")}this.scroll(this.first,false)},lock:function(){this.locked=true;this.buttons()},unlock:function(){this.locked=false;this.buttons()},size:function(a){if(a!==undefined){this.options.size=a;this.locked||this.buttons()}return this.options.size},has:function(a,c){if(c===undefined||!c)c=a;if(this.options.size!==null&&c>this.options.size)c=this.options.size;for(var b= a;b<=c;b++){var d=this.get(b);if(!d.length||d.hasClass("jcarousel-item-placeholder"))return false}return true},get:function(a){return i(".jcarousel-item-"+a,this.list)},add:function(a,c){var b=this.get(a),d=0,f=i(c);if(b.length===0){var j,e=h.intval(a);for(b=this.create(a);;){j=this.get(--e);if(e<=0||j.length){e<=0?this.list.prepend(b):j.after(b);break}}}else d=this.dimension(b);if(f.get(0).nodeName.toUpperCase()=="LI"){b.replaceWith(f);b=f}else b.empty().append(c);this.format(b.removeClass(this.className("jcarousel-item-placeholder")), a);f=this.options.visible!==null?Math.ceil(this.clipping()/this.options.visible):null;d=this.dimension(b,f)-d;a>0&&a<this.first&&this.list.css(this.lt,h.intval(this.list.css(this.lt))-d+"px");this.list.css(this.wh,h.intval(this.list.css(this.wh))+d+"px");return b},remove:function(a){var c=this.get(a);if(!(!c.length||a>=this.first&&a<=this.last)){var b=this.dimension(c);a<this.first&&this.list.css(this.lt,h.intval(this.list.css(this.lt))+b+"px");c.remove();this.list.css(this.wh,h.intval(this.list.css(this.wh))- b+"px")}},next:function(){this.tail!==null&&!this.inTail?this.scrollTail(false):this.scroll((this.options.wrap=="both"||this.options.wrap=="last")&&this.options.size!==null&&this.last==this.options.size?1:this.first+this.options.scroll)},prev:function(){this.tail!==null&&this.inTail?this.scrollTail(true):this.scroll((this.options.wrap=="both"||this.options.wrap=="first")&&this.options.size!==null&&this.first==1?this.options.size:this.first-this.options.scroll)},scrollTail:function(a){if(!(this.locked|| this.animating||!this.tail)){this.pauseAuto();var c=h.intval(this.list.css(this.lt));c=!a?c-this.tail:c+this.tail;this.inTail=!a;this.prevFirst=this.first;this.prevLast=this.last;this.animate(c)}},scroll:function(a,c){if(!(this.locked||this.animating)){this.pauseAuto();this.animate(this.pos(a),c)}},pos:function(a,c){var b=h.intval(this.list.css(this.lt));if(this.locked||this.animating)return b;if(this.options.wrap!="circular")a=a<1?1:this.options.size&&a>this.options.size?this.options.size:a;for(var d= this.first>a,f=this.options.wrap!="circular"&&this.first<=1?1:this.first,j=d?this.get(f):this.get(this.last),e=d?f:f-1,g=null,k=0,l=false,m=0;d?--e>=a:++e<a;){g=this.get(e);l=!g.length;if(g.length===0){g=this.create(e).addClass(this.className("jcarousel-item-placeholder"));j[d?"before":"after"](g);if(this.first!==null&&this.options.wrap=="circular"&&this.options.size!==null&&(e<=0||e>this.options.size)){j=this.get(this.index(e));if(j.length)g=this.add(e,j.clone(true))}}j=g;m=this.dimension(g);if(l)k+= m;if(this.first!==null&&(this.options.wrap=="circular"||e>=1&&(this.options.size===null||e<=this.options.size)))b=d?b+m:b-m}f=this.clipping();var p=[],o=0,n=0;j=this.get(a-1);for(e=a;++o;){g=this.get(e);l=!g.length;if(g.length===0){g=this.create(e).addClass(this.className("jcarousel-item-placeholder"));j.length===0?this.list.prepend(g):j[d?"before":"after"](g);if(this.first!==null&&this.options.wrap=="circular"&&this.options.size!==null&&(e<=0||e>this.options.size)){j=this.get(this.index(e));if(j.length)g= this.add(e,j.clone(true))}}j=g;m=this.dimension(g);if(m===0)throw Error("jCarousel: No width/height set for items. This will cause an infinite loop. Aborting...");if(this.options.wrap!="circular"&&this.options.size!==null&&e>this.options.size)p.push(g);else if(l)k+=m;n+=m;if(n>=f)break;e++}for(g=0;g<p.length;g++)p[g].remove();if(k>0){this.list.css(this.wh,this.dimension(this.list)+k+"px");if(d){b-=k;this.list.css(this.lt,h.intval(this.list.css(this.lt))-k+"px")}}k=a+o-1;if(this.options.wrap!="circular"&& this.options.size&&k>this.options.size)k=this.options.size;if(e>k){o=0;e=k;for(n=0;++o;){g=this.get(e--);if(!g.length)break;n+=this.dimension(g);if(n>=f)break}}e=k-o+1;if(this.options.wrap!="circular"&&e<1)e=1;if(this.inTail&&d){b+=this.tail;this.inTail=false}this.tail=null;if(this.options.wrap!="circular"&&k==this.options.size&&k-o+1>=1){d=h.margin(this.get(k),!this.options.vertical?"marginRight":"marginBottom");if(n-d>f)this.tail=n-f-d}if(c&&a===this.options.size&&this.tail){b-=this.tail;this.inTail= true}for(;a-- >e;)b+=this.dimension(this.get(a));this.prevFirst=this.first;this.prevLast=this.last;this.first=e;this.last=k;return b},animate:function(a,c){if(!(this.locked||this.animating)){this.animating=true;var b=this,d=function(){b.animating=false;a===0&&b.list.css(b.lt,0);if(!b.autoStopped&&(b.options.wrap=="circular"||b.options.wrap=="both"||b.options.wrap=="last"||b.options.size===null||b.last<b.options.size||b.last==b.options.size&&b.tail!==null&&!b.inTail))b.startAuto();b.buttons();b.notify("onAfterAnimation"); if(b.options.wrap=="circular"&&b.options.size!==null)for(var f=b.prevFirst;f<=b.prevLast;f++)if(f!==null&&!(f>=b.first&&f<=b.last)&&(f<1||f>b.options.size))b.remove(f)};this.notify("onBeforeAnimation");if(!this.options.animation||c===false){this.list.css(this.lt,a+"px");d()}else this.list.animate(!this.options.vertical?this.options.rtl?{right:a}:{left:a}:{top:a},this.options.animation,this.options.easing,d)}},startAuto:function(a){if(a!==undefined)this.options.auto=a;if(this.options.auto===0)return this.stopAuto(); if(this.timer===null){this.autoStopped=false;var c=this;this.timer=window.setTimeout(function(){c.next()},this.options.auto*1E3)}},stopAuto:function(){this.pauseAuto();this.autoStopped=true},pauseAuto:function(){if(this.timer!==null){window.clearTimeout(this.timer);this.timer=null}},buttons:function(a,c){if(a==null){a=!this.locked&&this.options.size!==0&&(this.options.wrap&&this.options.wrap!="first"||this.options.size===null||this.last<this.options.size);if(!this.locked&&(!this.options.wrap||this.options.wrap== "first")&&this.options.size!==null&&this.last>=this.options.size)a=this.tail!==null&&!this.inTail}if(c==null){c=!this.locked&&this.options.size!==0&&(this.options.wrap&&this.options.wrap!="last"||this.first>1);if(!this.locked&&(!this.options.wrap||this.options.wrap=="last")&&this.options.size!==null&&this.first==1)c=this.tail!==null&&this.inTail}var b=this;if(this.buttonNext.size()>0){this.buttonNext.unbind(this.options.buttonNextEvent+".jcarousel",this.funcNext);a&&this.buttonNext.bind(this.options.buttonNextEvent+ ".jcarousel",this.funcNext);this.buttonNext[a?"removeClass":"addClass"](this.className("jcarousel-next-disabled")).attr("disabled",a?false:true);this.options.buttonNextCallback!==null&&this.buttonNext.data("jcarouselstate")!=a&&this.buttonNext.each(function(){b.options.buttonNextCallback(b,this,a)}).data("jcarouselstate",a)}else this.options.buttonNextCallback!==null&&this.buttonNextState!=a&&this.options.buttonNextCallback(b,null,a);if(this.buttonPrev.size()>0){this.buttonPrev.unbind(this.options.buttonPrevEvent+ ".jcarousel",this.funcPrev);c&&this.buttonPrev.bind(this.options.buttonPrevEvent+".jcarousel",this.funcPrev);this.buttonPrev[c?"removeClass":"addClass"](this.className("jcarousel-prev-disabled")).attr("disabled",c?false:true);this.options.buttonPrevCallback!==null&&this.buttonPrev.data("jcarouselstate")!=c&&this.buttonPrev.each(function(){b.options.buttonPrevCallback(b,this,c)}).data("jcarouselstate",c)}else this.options.buttonPrevCallback!==null&&this.buttonPrevState!=c&&this.options.buttonPrevCallback(b, null,c);this.buttonNextState=a;this.buttonPrevState=c},notify:function(a){var c=this.prevFirst===null?"init":this.prevFirst<this.first?"next":"prev";this.callback("itemLoadCallback",a,c);if(this.prevFirst!==this.first){this.callback("itemFirstInCallback",a,c,this.first);this.callback("itemFirstOutCallback",a,c,this.prevFirst)}if(this.prevLast!==this.last){this.callback("itemLastInCallback",a,c,this.last);this.callback("itemLastOutCallback",a,c,this.prevLast)}this.callback("itemVisibleInCallback", a,c,this.first,this.last,this.prevFirst,this.prevLast);this.callback("itemVisibleOutCallback",a,c,this.prevFirst,this.prevLast,this.first,this.last)},callback:function(a,c,b,d,f,j,e){if(!(this.options[a]==null||typeof this.options[a]!="object"&&c!="onAfterAnimation")){var g=typeof this.options[a]=="object"?this.options[a][c]:this.options[a];if(i.isFunction(g)){var k=this;if(d===undefined)g(k,b,c);else if(f===undefined)this.get(d).each(function(){g(k,this,d,b,c)});else{a=function(m){k.get(m).each(function(){g(k, this,m,b,c)})};for(var l=d;l<=f;l++)l!==null&&!(l>=j&&l<=e)&&a(l)}}}},create:function(a){return this.format("<li></li>",a)},format:function(a,c){a=i(a);for(var b=a.get(0).className.split(" "),d=0;d<b.length;d++)b[d].indexOf("jcarousel-")!=-1&&a.removeClass(b[d]);a.addClass(this.className("jcarousel-item")).addClass(this.className("jcarousel-item-"+c)).css({"float":this.options.rtl?"right":"left","list-style":"none"}).attr("jcarouselindex",c);return a},className:function(a){return a+" "+a+(!this.options.vertical? "-horizontal":"-vertical")},dimension:function(a,c){var b=a.jquery!==undefined?a[0]:a,d=!this.options.vertical?(b.offsetWidth||h.intval(this.options.itemFallbackDimension))+h.margin(b,"marginLeft")+h.margin(b,"marginRight"):(b.offsetHeight||h.intval(this.options.itemFallbackDimension))+h.margin(b,"marginTop")+h.margin(b,"marginBottom");if(c==null||d==c)return d;d=!this.options.vertical?c-h.margin(b,"marginLeft")-h.margin(b,"marginRight"):c-h.margin(b,"marginTop")-h.margin(b,"marginBottom");i(b).css(this.wh, d+"px");return this.dimension(b)},clipping:function(){return!this.options.vertical?this.clip[0].offsetWidth-h.intval(this.clip.css("borderLeftWidth"))-h.intval(this.clip.css("borderRightWidth")):this.clip[0].offsetHeight-h.intval(this.clip.css("borderTopWidth"))-h.intval(this.clip.css("borderBottomWidth"))},index:function(a,c){if(c==null)c=this.options.size;return Math.round(((a-1)/c-Math.floor((a-1)/c))*c)+1}});h.extend({defaults:function(a){return i.extend(q,a||{})},margin:function(a,c){if(!a)return 0; var b=a.jquery!==undefined?a[0]:a;if(c=="marginRight"&&i.browser.safari){var d={display:"block","float":"none",width:"auto"},f,j;i.swap(b,d,function(){f=b.offsetWidth});d.marginRight=0;i.swap(b,d,function(){j=b.offsetWidth});return j-f}return h.intval(i.css(b,c))},intval:function(a){a=parseInt(a,10);return isNaN(a)?0:a}});i.fn.jcarousel=function(a){if(typeof a=="string"){var c=i(this).data("jcarousel"),b=Array.prototype.slice.call(arguments,1);return c[a].apply(c,b)}else return this.each(function(){i(this).data("jcarousel", new h(this,a))})}})(jQuery);
