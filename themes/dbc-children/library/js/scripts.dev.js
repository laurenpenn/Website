/* 
 * Compress the contents of this file with http://www.minifyjs.com/javascript-compressor/.
 * Paste the results into /dbc/library/js/scripts.js
 */

/*
 * FancyBox - jQuery Plugin
 * Simple and fancy lightbox alternative
 *
 * Examples and documentation at: http://fancybox.net
 *
 * Copyright (c) 2008 - 2010 Janis Skarnelis
 * That said, it is hardly a one-person project. Many people have submitted bugs, code, and offered their advice freely. Their support is greatly appreciated.
 *
 * Version: 1.3.4 (11/11/2010)
 * Requires: jQuery v1.3+
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
;(function($) {
	var tmp, loading, overlay, wrap, outer, content, close, title, nav_left, nav_right,

		selectedIndex = 0, selectedOpts = {}, selectedArray = [], currentIndex = 0, currentOpts = {}, currentArray = [],

		ajaxLoader = null, imgPreloader = new Image(), imgRegExp = /\.(jpg|gif|png|bmp|jpeg)(.*)?$/i, swfRegExp = /[^\.]\.(swf)\s*$/i,

		loadingTimer, loadingFrame = 1,

		titleHeight = 0, titleStr = '', start_pos, final_pos, busy = false, fx = $.extend($('<div/>')[0], { prop: 0 }),

		isIE6 = $.browser.msie && $.browser.version < 7 && !window.XMLHttpRequest,

		/*
		 * Private methods 
		 */

		_abort = function() {
			loading.hide();

			imgPreloader.onerror = imgPreloader.onload = null;

			if (ajaxLoader) {
				ajaxLoader.abort();
			}

			tmp.empty();
		},

		_error = function() {
			if (false === selectedOpts.onError(selectedArray, selectedIndex, selectedOpts)) {
				loading.hide();
				busy = false;
				return;
			}

			selectedOpts.titleShow = false;

			selectedOpts.width = 'auto';
			selectedOpts.height = 'auto';

			tmp.html( '<p id="fancybox-error">The requested content cannot be loaded.<br />Please try again later.</p>' );

			_process_inline();
		},

		_start = function() {
			var obj = selectedArray[ selectedIndex ],
				href, 
				type, 
				title,
				str,
				emb,
				ret;

			_abort();

			selectedOpts = $.extend({}, $.fn.fancybox.defaults, (typeof $(obj).data('fancybox') === 'undefined' ? selectedOpts : $(obj).data('fancybox')));

			ret = selectedOpts.onStart(selectedArray, selectedIndex, selectedOpts);

			if (ret === false) {
				busy = false;
				return;
			} else if (typeof ret === 'object') {
				selectedOpts = $.extend(selectedOpts, ret);
			}

			title = selectedOpts.title || (obj.nodeName ? $(obj).attr('title') : obj.title) || '';

			if (obj.nodeName && !selectedOpts.orig) {
				selectedOpts.orig = $(obj).children("img:first").length ? $(obj).children("img:first") : $(obj);
			}

			if (title === '' && selectedOpts.orig && selectedOpts.titleFromAlt) {
				title = selectedOpts.orig.attr('alt');
			}

			href = selectedOpts.href || (obj.nodeName ? $(obj).attr('href') : obj.href) || null;

			if ((/^(?:javascript)/i).test(href) || href === '#') {
				href = null;
			}

			if (selectedOpts.type) {
				type = selectedOpts.type;

				if (!href) {
					href = selectedOpts.content;
				}

			} else if (selectedOpts.content) {
				type = 'html';

			} else if (href) {
				if (href.match(imgRegExp)) {
					type = 'image';

				} else if (href.match(swfRegExp)) {
					type = 'swf';

				} else if ($(obj).hasClass("iframe")) {
					type = 'iframe';

				} else if (href.indexOf("#") === 0) {
					type = 'inline';

				} else {
					type = 'ajax';
				}
			}

			if (!type) {
				_error();
				return;
			}

			if (type === 'inline') {
				obj	= href.substr(href.indexOf("#"));
				type = $(obj).length > 0 ? 'inline' : 'ajax';
			}

			selectedOpts.type = type;
			selectedOpts.href = href;
			selectedOpts.title = title;

			if (selectedOpts.autoDimensions) {
				if (selectedOpts.type === 'html' || selectedOpts.type === 'inline' || selectedOpts.type === 'ajax') {
					selectedOpts.width = 'auto';
					selectedOpts.height = 'auto';
				} else {
					selectedOpts.autoDimensions = false;	
				}
			}

			if (selectedOpts.modal) {
				selectedOpts.overlayShow = true;
				selectedOpts.hideOnOverlayClick = false;
				selectedOpts.hideOnContentClick = false;
				selectedOpts.enableEscapeButton = false;
				selectedOpts.showCloseButton = false;
			}

			selectedOpts.padding = parseInt(selectedOpts.padding, 10);
			selectedOpts.margin = parseInt(selectedOpts.margin, 10);

			tmp.css('padding', (selectedOpts.padding + selectedOpts.margin));

			$('.fancybox-inline-tmp').unbind('fancybox-cancel').bind('fancybox-change', function() {
				$(this).replaceWith(content.children());				
			});

			switch (type) {
				case 'html' :
					tmp.html( selectedOpts.content );
					_process_inline();
				break;

				case 'inline' :
					if ( $(obj).parent().is('#fancybox-content') === true) {
						busy = false;
						return;
					}

					$('<div class="fancybox-inline-tmp" />')
						.hide()
						.insertBefore( $(obj) )
						.bind('fancybox-cleanup', function() {
							$(this).replaceWith(content.children());
						}).bind('fancybox-cancel', function() {
							$(this).replaceWith(tmp.children());
						});

					$(obj).appendTo(tmp);

					_process_inline();
				break;

				case 'image':
					busy = false;

					$.fancybox.showActivity();

					imgPreloader = new Image();

					imgPreloader.onerror = function() {
						_error();
					};

					imgPreloader.onload = function() {
						busy = true;

						imgPreloader.onerror = imgPreloader.onload = null;

						_process_image();
					};

					imgPreloader.src = href;
				break;

				case 'swf':
					selectedOpts.scrolling = 'no';

					str = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="' + selectedOpts.width + '" height="' + selectedOpts.height + '"><param name="movie" value="' + href + '"></param>';
					emb = '';

					$.each(selectedOpts.swf, function(name, val) {
						str += '<param name="' + name + '" value="' + val + '"></param>';
						emb += ' ' + name + '="' + val + '"';
					});

					str += '<embed src="' + href + '" type="application/x-shockwave-flash" width="' + selectedOpts.width + '" height="' + selectedOpts.height + '"' + emb + '></embed></object>';

					tmp.html(str);

					_process_inline();
				break;

				case 'ajax':
					busy = false;

					$.fancybox.showActivity();

					selectedOpts.ajax.win = selectedOpts.ajax.success;

					ajaxLoader = $.ajax($.extend({}, selectedOpts.ajax, {
						url	: href,
						data : selectedOpts.ajax.data || {},
						error : function(XMLHttpRequest, textStatus, errorThrown) {
							if ( XMLHttpRequest.status > 0 ) {
								_error();
							}
						},
						success : function(data, textStatus, XMLHttpRequest) {
							var o = typeof XMLHttpRequest === 'object' ? XMLHttpRequest : ajaxLoader;
							if (o.status === 200) {
								if ( typeof selectedOpts.ajax.win === 'function' ) {
									ret = selectedOpts.ajax.win(href, data, textStatus, XMLHttpRequest);

									if (ret === false) {
										loading.hide();
										return;
									} else if (typeof ret === 'string' || typeof ret === 'object') {
										data = ret;
									}
								}

								tmp.html( data );
								_process_inline();
							}
						}
					}));

				break;

				case 'iframe':
					_show();
				break;
			}
		},

		_process_inline = function() {
			var
				w = selectedOpts.width,
				h = selectedOpts.height;

			if (w.toString().indexOf('%') > -1) {
				w = parseInt( ($(window).width() - (selectedOpts.margin * 2)) * parseFloat(w) / 100, 10) + 'px';

			} else {
				w = w === 'auto' ? 'auto' : w + 'px';	
			}

			if (h.toString().indexOf('%') > -1) {
				h = parseInt( ($(window).height() - (selectedOpts.margin * 2)) * parseFloat(h) / 100, 10) + 'px';

			} else {
				h = h === 'auto' ? 'auto' : h + 'px';	
			}

			tmp.wrapInner('<div style="width:' + w + ';height:' + h + ';overflow: ' + (selectedOpts.scrolling === 'auto' ? 'auto' : (selectedOpts.scrolling === 'yes' ? 'scroll' : 'hidden')) + ';position:relative;"></div>');

			selectedOpts.width = tmp.width();
			selectedOpts.height = tmp.height();

			_show();
		},

		_process_image = function() {
			selectedOpts.width = imgPreloader.width;
			selectedOpts.height = imgPreloader.height;

			$("<img />").attr({
				'id' : 'fancybox-img',
				'src' : imgPreloader.src,
				'alt' : selectedOpts.title
			}).appendTo( tmp );

			_show();
		},

		_show = function() {
			var pos, equal;

			loading.hide();

			if (wrap.is(":visible") && false === currentOpts.onCleanup(currentArray, currentIndex, currentOpts)) {
				$.event.trigger('fancybox-cancel');

				busy = false;
				return;
			}

			busy = true;

			$(content.add( overlay )).unbind();

			$(window).unbind("resize.fb scroll.fb");
			$(document).unbind('keydown.fb');

			if (wrap.is(":visible") && currentOpts.titlePosition !== 'outside') {
				wrap.css('height', wrap.height());
			}

			currentArray = selectedArray;
			currentIndex = selectedIndex;
			currentOpts = selectedOpts;

			if (currentOpts.overlayShow) {
				overlay.css({
					'background-color' : currentOpts.overlayColor,
					'opacity' : currentOpts.overlayOpacity,
					'cursor' : currentOpts.hideOnOverlayClick ? 'pointer' : 'auto',
					'height' : $(document).height()
				});

				if (!overlay.is(':visible')) {
					if (isIE6) {
						$('select:not(#fancybox-tmp select)').filter(function() {
							return this.style.visibility !== 'hidden';
						}).css({'visibility' : 'hidden'}).one('fancybox-cleanup', function() {
							this.style.visibility = 'inherit';
						});
					}

					overlay.show();
				}
			} else {
				overlay.hide();
			}

			final_pos = _get_zoom_to();

			_process_title();

			if (wrap.is(":visible")) {
				$( close.add( nav_left ).add( nav_right ) ).hide();

				pos = wrap.position();

				start_pos = {
					top	 : pos.top,
					left : pos.left,
					width : wrap.width(),
					height : wrap.height()
				};

				equal = (start_pos.width === final_pos.width && start_pos.height === final_pos.height);

				content.fadeTo(currentOpts.changeFade, 0.3, function() {
					var finish_resizing = function() {
						content.html( tmp.contents() ).fadeTo(currentOpts.changeFade, 1, _finish);
					};

					$.event.trigger('fancybox-change');

					content
						.empty()
						.removeAttr('filter')
						.css({
							'border-width' : currentOpts.padding,
							'width'	: final_pos.width - currentOpts.padding * 2,
							'height' : selectedOpts.autoDimensions ? 'auto' : final_pos.height - titleHeight - currentOpts.padding * 2
						});

					if (equal) {
						finish_resizing();

					} else {
						fx.prop = 0;

						$(fx).animate({prop: 1}, {
							 duration : currentOpts.changeSpeed,
							 easing : currentOpts.easingChange,
							 step : _draw,
							 complete : finish_resizing
						});
					}
				});

				return;
			}

			wrap.removeAttr("style");

			content.css('border-width', currentOpts.padding);

			if (currentOpts.transitionIn === 'elastic') {
				start_pos = _get_zoom_from();

				content.html( tmp.contents() );

				wrap.show();

				if (currentOpts.opacity) {
					final_pos.opacity = 0;
				}

				fx.prop = 0;

				$(fx).animate({prop: 1}, {
					 duration : currentOpts.speedIn,
					 easing : currentOpts.easingIn,
					 step : _draw,
					 complete : _finish
				});

				return;
			}

			if (currentOpts.titlePosition === 'inside' && titleHeight > 0) {	
				title.show();	
			}

			content
				.css({
					'width' : final_pos.width - currentOpts.padding * 2,
					'height' : selectedOpts.autoDimensions ? 'auto' : final_pos.height - titleHeight - currentOpts.padding * 2
				})
				.html( tmp.contents() );

			wrap
				.css(final_pos)
				.fadeIn( currentOpts.transitionIn === 'none' ? 0 : currentOpts.speedIn, _finish );
		},

		_format_title = function(title) {
			if (title && title.length) {
				if (currentOpts.titlePosition === 'float') {
					return '<table id="fancybox-title-float-wrap" cellpadding="0" cellspacing="0"><tr><td id="fancybox-title-float-left"></td><td id="fancybox-title-float-main">' + title + '</td><td id="fancybox-title-float-right"></td></tr></table>';
				}

				return '<div id="fancybox-title-' + currentOpts.titlePosition + '">' + title + '</div>';
			}

			return false;
		},

		_process_title = function() {
			titleStr = currentOpts.title || '';
			titleHeight = 0;

			title
				.empty()
				.removeAttr('style')
				.removeClass();

			if (currentOpts.titleShow === false) {
				title.hide();
				return;
			}

			titleStr = $.isFunction(currentOpts.titleFormat) ? currentOpts.titleFormat(titleStr, currentArray, currentIndex, currentOpts) : _format_title(titleStr);

			if (!titleStr || titleStr === '') {
				title.hide();
				return;
			}

			title
				.addClass('fancybox-title-' + currentOpts.titlePosition)
				.html( titleStr )
				.appendTo( 'body' )
				.show();

			switch (currentOpts.titlePosition) {
				case 'inside':
					title
						.css({
							'width' : final_pos.width - (currentOpts.padding * 2),
							'marginLeft' : currentOpts.padding,
							'marginRight' : currentOpts.padding
						});

					titleHeight = title.outerHeight(true);

					title.appendTo( outer );

					final_pos.height += titleHeight;
				break;

				case 'over':
					title
						.css({
							'marginLeft' : currentOpts.padding,
							'width'	: final_pos.width - (currentOpts.padding * 2),
							'bottom' : currentOpts.padding
						})
						.appendTo( outer );
				break;

				case 'float':
					title
						.css('left', parseInt((title.width() - final_pos.width - 40)/ 2, 10) * -1)
						.appendTo( wrap );
				break;

				default:
					title
						.css({
							'width' : final_pos.width - (currentOpts.padding * 2),
							'paddingLeft' : currentOpts.padding,
							'paddingRight' : currentOpts.padding
						})
						.appendTo( wrap );
				break;
			}

			title.hide();
		},

		_set_navigation = function() {
			if (currentOpts.enableEscapeButton || currentOpts.enableKeyboardNav) {
				$(document).bind('keydown.fb', function(e) {
					if (e.keyCode === 27 && currentOpts.enableEscapeButton) {
						e.preventDefault();
						$.fancybox.close();

					} else if ((e.keyCode === 37 || e.keyCode === 39) && currentOpts.enableKeyboardNav && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'SELECT') {
						e.preventDefault();
						$.fancybox[ e.keyCode === 37 ? 'prev' : 'next']();
					}
				});
			}

			if (!currentOpts.showNavArrows) { 
				nav_left.hide();
				nav_right.hide();
				return;
			}

			if ((currentOpts.cyclic && currentArray.length > 1) || currentIndex !== 0) {
				nav_left.show();
			}

			if ((currentOpts.cyclic && currentArray.length > 1) || currentIndex !== (currentArray.length -1)) {
				nav_right.show();
			}
		},

		_finish = function () {
			if (!$.support.opacity) {
				content.get(0).style.removeAttribute('filter');
				wrap.get(0).style.removeAttribute('filter');
			}

			if (selectedOpts.autoDimensions) {
				content.css('height', 'auto');
			}

			wrap.css('height', 'auto');

			if (titleStr && titleStr.length) {
				title.show();
			}

			if (currentOpts.showCloseButton) {
				close.show();
			}

			_set_navigation();
	
			if (currentOpts.hideOnContentClick)	{
				content.bind('click', $.fancybox.close);
			}

			if (currentOpts.hideOnOverlayClick)	{
				overlay.bind('click', $.fancybox.close);
			}

			$(window).bind("resize.fb", $.fancybox.resize);

			if (currentOpts.centerOnScroll) {
				$(window).bind("scroll.fb", $.fancybox.center);
			}

			if (currentOpts.type === 'iframe') {
				$('<iframe id="fancybox-frame" name="fancybox-frame' + new Date().getTime() + '" frameborder="0" hspace="0" ' + ($.browser.msie ? 'allowtransparency="true""' : '') + ' scrolling="' + selectedOpts.scrolling + '" src="' + currentOpts.href + '"></iframe>').appendTo(content);
			}

			wrap.show();

			busy = false;

			$.fancybox.center();

			currentOpts.onComplete(currentArray, currentIndex, currentOpts);

			_preload_images();
		},

		_preload_images = function() {
			var href, 
				objNext;

			if ((currentArray.length -1) > currentIndex) {
				href = currentArray[ currentIndex + 1 ].href;

				if (typeof href !== 'undefined' && href.match(imgRegExp)) {
					objNext = new Image();
					objNext.src = href;
				}
			}

			if (currentIndex > 0) {
				href = currentArray[ currentIndex - 1 ].href;

				if (typeof href !== 'undefined' && href.match(imgRegExp)) {
					objNext = new Image();
					objNext.src = href;
				}
			}
		},

		_draw = function(pos) {
			var dim = {
				width : parseInt(start_pos.width + (final_pos.width - start_pos.width) * pos, 10),
				height : parseInt(start_pos.height + (final_pos.height - start_pos.height) * pos, 10),

				top : parseInt(start_pos.top + (final_pos.top - start_pos.top) * pos, 10),
				left : parseInt(start_pos.left + (final_pos.left - start_pos.left) * pos, 10)
			};

			if (typeof final_pos.opacity !== 'undefined') {
				dim.opacity = pos < 0.5 ? 0.5 : pos;
			}

			wrap.css(dim);

			content.css({
				'width' : dim.width - currentOpts.padding * 2,
				'height' : dim.height - (titleHeight * pos) - currentOpts.padding * 2
			});
		},

		_get_viewport = function() {
			return [
				$(window).width() - (currentOpts.margin * 2),
				$(window).height() - (currentOpts.margin * 2),
				$(document).scrollLeft() + currentOpts.margin,
				$(document).scrollTop() + currentOpts.margin
			];
		},

		_get_zoom_to = function () {
			var view = _get_viewport(),
				to = {},
				resize = currentOpts.autoScale,
				double_padding = currentOpts.padding * 2,
				ratio;

			if (currentOpts.width.toString().indexOf('%') > -1) {
				to.width = parseInt((view[0] * parseFloat(currentOpts.width)) / 100, 10);
			} else {
				to.width = currentOpts.width + double_padding;
			}

			if (currentOpts.height.toString().indexOf('%') > -1) {
				to.height = parseInt((view[1] * parseFloat(currentOpts.height)) / 100, 10);
			} else {
				to.height = currentOpts.height + double_padding;
			}

			if (resize && (to.width > view[0] || to.height > view[1])) {
				if (selectedOpts.type === 'image' || selectedOpts.type === 'swf') {
					ratio = (currentOpts.width ) / (currentOpts.height );

					if ((to.width ) > view[0]) {
						to.width = view[0];
						to.height = parseInt(((to.width - double_padding) / ratio) + double_padding, 10);
					}

					if ((to.height) > view[1]) {
						to.height = view[1];
						to.width = parseInt(((to.height - double_padding) * ratio) + double_padding, 10);
					}

				} else {
					to.width = Math.min(to.width, view[0]);
					to.height = Math.min(to.height, view[1]);
				}
			}

			to.top = parseInt(Math.max(view[3] - 20, view[3] + ((view[1] - to.height - 40) * 0.5)), 10);
			to.left = parseInt(Math.max(view[2] - 20, view[2] + ((view[0] - to.width - 40) * 0.5)), 10);

			return to;
		},

		_get_obj_pos = function(obj) {
			var pos = obj.offset();

			pos.top += parseInt( obj.css('paddingTop'), 10 ) || 0;
			pos.left += parseInt( obj.css('paddingLeft'), 10 ) || 0;

			pos.top += parseInt( obj.css('border-top-width'), 10 ) || 0;
			pos.left += parseInt( obj.css('border-left-width'), 10 ) || 0;

			pos.width = obj.width();
			pos.height = obj.height();

			return pos;
		},

		_get_zoom_from = function() {
			var orig = selectedOpts.orig ? $(selectedOpts.orig) : false,
				from = {},
				pos,
				view;

			if (orig && orig.length) {
				pos = _get_obj_pos(orig);

				from = {
					width : pos.width + (currentOpts.padding * 2),
					height : pos.height + (currentOpts.padding * 2),
					top	: pos.top - currentOpts.padding - 20,
					left : pos.left - currentOpts.padding - 20
				};

			} else {
				view = _get_viewport();

				from = {
					width : currentOpts.padding * 2,
					height : currentOpts.padding * 2,
					top	: parseInt(view[3] + view[1] * 0.5, 10),
					left : parseInt(view[2] + view[0] * 0.5, 10)
				};
			}

			return from;
		},

		_animate_loading = function() {
			if (!loading.is(':visible')){
				clearInterval(loadingTimer);
				return;
			}

			$('div', loading).css('top', (loadingFrame * -40) + 'px');

			loadingFrame = (loadingFrame + 1) % 12;
		};

	/*
	 * Public methods 
	 */

	$.fn.fancybox = function(options) {
		if (!$(this).length) {
			return this;
		}

		$(this)
			.data('fancybox', $.extend({}, options, ($.metadata ? $(this).metadata() : {})))
			.unbind('click.fb')
			.bind('click.fb', function(e) {
				e.preventDefault();

				if (busy) {
					return;
				}

				busy = true;

				$(this).blur();

				selectedArray = [];
				selectedIndex = 0;

				var rel = $(this).attr('rel') || '';

				if (!rel || rel === '' || rel === 'nofollow') {
					selectedArray.push(this);

				} else {
					selectedArray = $("a[rel=" + rel + "], area[rel=" + rel + "]");
					selectedIndex = selectedArray.index( this );
				}

				_start();

				return;
			});

		return this;
	};

	$.fancybox = function(obj) {
		var opts;

		if (busy) {
			return;
		}

		busy = true;
		opts = typeof arguments[1] !== 'undefined' ? arguments[1] : {};

		selectedArray = [];
		selectedIndex = parseInt(opts.index, 10) || 0;

		if ($.isArray(obj)) {
			for (var i = 0, j = obj.length; i < j; i++) {
				if (typeof obj[i] == 'object') {
					$(obj[i]).data('fancybox', $.extend({}, opts, obj[i]));
				} else {
					obj[i] = $({}).data('fancybox', $.extend({content : obj[i]}, opts));
				}
			}

			selectedArray = jQuery.merge(selectedArray, obj);

		} else {
			if (typeof obj == 'object') {
				$(obj).data('fancybox', $.extend({}, opts, obj));
			} else {
				obj = $({}).data('fancybox', $.extend({content : obj}, opts));
			}

			selectedArray.push(obj);
		}

		if (selectedIndex > selectedArray.length || selectedIndex < 0) {
			selectedIndex = 0;
		}

		_start();
	};

	$.fancybox.showActivity = function() {
		clearInterval(loadingTimer);

		loading.show();
		loadingTimer = setInterval(_animate_loading, 66);
	};

	$.fancybox.hideActivity = function() {
		loading.hide();
	};

	$.fancybox.next = function() {
		return $.fancybox.pos( currentIndex + 1);
	};

	$.fancybox.prev = function() {
		return $.fancybox.pos( currentIndex - 1);
	};

	$.fancybox.pos = function(pos) {
		if (busy) {
			return;
		}

		pos = parseInt(pos);

		selectedArray = currentArray;

		if (pos > -1 && pos < currentArray.length) {
			selectedIndex = pos;
			_start();

		} else if (currentOpts.cyclic && currentArray.length > 1) {
			selectedIndex = pos >= currentArray.length ? 0 : currentArray.length - 1;
			_start();
		}

		return;
	};

	$.fancybox.cancel = function() {
		if (busy) {
			return;
		}

		busy = true;

		$.event.trigger('fancybox-cancel');

		_abort();

		selectedOpts.onCancel(selectedArray, selectedIndex, selectedOpts);

		busy = false;
	};

	// Note: within an iframe use - parent.$.fancybox.close();
	$.fancybox.close = function() {
		if (busy || wrap.is(':hidden')) {
			return;
		}

		busy = true;

		if (currentOpts && false === currentOpts.onCleanup(currentArray, currentIndex, currentOpts)) {
			busy = false;
			return;
		}

		_abort();

		$(close.add( nav_left ).add( nav_right )).hide();

		$(content.add( overlay )).unbind();

		$(window).unbind("resize.fb scroll.fb");
		$(document).unbind('keydown.fb');

		content.find('iframe').attr('src', isIE6 && /^https/i.test(window.location.href || '') ? 'javascript:void(false)' : 'about:blank');

		if (currentOpts.titlePosition !== 'inside') {
			title.empty();
		}

		wrap.stop();

		function _cleanup() {
			overlay.fadeOut('fast');

			title.empty().hide();
			wrap.hide();

			$.event.trigger('fancybox-cleanup');

			content.empty();

			currentOpts.onClosed(currentArray, currentIndex, currentOpts);

			currentArray = selectedOpts	= [];
			currentIndex = selectedIndex = 0;
			currentOpts = selectedOpts	= {};

			busy = false;
		}

		if (currentOpts.transitionOut == 'elastic') {
			start_pos = _get_zoom_from();

			var pos = wrap.position();

			final_pos = {
				top	 : pos.top ,
				left : pos.left,
				width :	wrap.width(),
				height : wrap.height()
			};

			if (currentOpts.opacity) {
				final_pos.opacity = 1;
			}

			title.empty().hide();

			fx.prop = 1;

			$(fx).animate({ prop: 0 }, {
				 duration : currentOpts.speedOut,
				 easing : currentOpts.easingOut,
				 step : _draw,
				 complete : _cleanup
			});

		} else {
			wrap.fadeOut( currentOpts.transitionOut == 'none' ? 0 : currentOpts.speedOut, _cleanup);
		}
	};

	$.fancybox.resize = function() {
		if (overlay.is(':visible')) {
			overlay.css('height', $(document).height());
		}

		$.fancybox.center(true);
	};

	$.fancybox.center = function() {
		var view, align;

		if (busy) {
			return;	
		}

		align = arguments[0] === true ? 1 : 0;
		view = _get_viewport();

		if (!align && (wrap.width() > view[0] || wrap.height() > view[1])) {
			return;	
		}

		wrap
			.stop()
			.animate({
				'top' : parseInt(Math.max(view[3] - 20, view[3] + ((view[1] - content.height() - 40) * 0.5) - currentOpts.padding)),
				'left' : parseInt(Math.max(view[2] - 20, view[2] + ((view[0] - content.width() - 40) * 0.5) - currentOpts.padding))
			}, typeof arguments[0] == 'number' ? arguments[0] : 200);
	};

	$.fancybox.init = function() {
		if ($("#fancybox-wrap").length) {
			return;
		}

		$('body').append(
			tmp	= $('<div id="fancybox-tmp"></div>'),
			loading	= $('<div id="fancybox-loading"><div></div></div>'),
			overlay	= $('<div id="fancybox-overlay"></div>'),
			wrap = $('<div id="fancybox-wrap"></div>')
		);

		outer = $('<div id="fancybox-outer"></div>')
			.append('<div class="fancybox-bg" id="fancybox-bg-n"></div><div class="fancybox-bg" id="fancybox-bg-ne"></div><div class="fancybox-bg" id="fancybox-bg-e"></div><div class="fancybox-bg" id="fancybox-bg-se"></div><div class="fancybox-bg" id="fancybox-bg-s"></div><div class="fancybox-bg" id="fancybox-bg-sw"></div><div class="fancybox-bg" id="fancybox-bg-w"></div><div class="fancybox-bg" id="fancybox-bg-nw"></div>')
			.appendTo( wrap );

		outer.append(
			content = $('<div id="fancybox-content"></div>'),
			close = $('<a id="fancybox-close"></a>'),
			title = $('<div id="fancybox-title"></div>'),

			nav_left = $('<a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a>'),
			nav_right = $('<a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a>')
		);

		close.click($.fancybox.close);
		loading.click($.fancybox.cancel);

		nav_left.click(function(e) {
			e.preventDefault();
			$.fancybox.prev();
		});

		nav_right.click(function(e) {
			e.preventDefault();
			$.fancybox.next();
		});

		if ($.fn.mousewheel) {
			wrap.bind('mousewheel.fb', function(e, delta) {
				if (busy) {
					e.preventDefault();

				} else if ($(e.target).get(0).clientHeight == 0 || $(e.target).get(0).scrollHeight === $(e.target).get(0).clientHeight) {
					e.preventDefault();
					$.fancybox[ delta > 0 ? 'prev' : 'next']();
				}
			});
		}

		if (!$.support.opacity) {
			wrap.addClass('fancybox-ie');
		}

		if (isIE6) {
			loading.addClass('fancybox-ie6');
			wrap.addClass('fancybox-ie6');

			$('<iframe id="fancybox-hide-sel-frame" src="' + (/^https/i.test(window.location.href || '') ? 'javascript:void(false)' : 'about:blank' ) + '" scrolling="no" border="0" frameborder="0" tabindex="-1"></iframe>').prependTo(outer);
		}
	};

	$.fn.fancybox.defaults = {
		padding : 10,
		margin : 40,
		opacity : false,
		modal : false,
		cyclic : false,
		scrolling : 'auto',	// 'auto', 'yes' or 'no'

		width : 560,
		height : 340,

		autoScale : true,
		autoDimensions : true,
		centerOnScroll : false,

		ajax : {},
		swf : { wmode: 'transparent' },

		hideOnOverlayClick : true,
		hideOnContentClick : false,

		overlayShow : true,
		overlayOpacity : 0.7,
		overlayColor : '#777',

		titleShow : true,
		titlePosition : 'float', // 'float', 'outside', 'inside' or 'over'
		titleFormat : null,
		titleFromAlt : false,

		transitionIn : 'fade', // 'elastic', 'fade' or 'none'
		transitionOut : 'fade', // 'elastic', 'fade' or 'none'

		speedIn : 300,
		speedOut : 300,

		changeSpeed : 300,
		changeFade : 'fast',

		easingIn : 'swing',
		easingOut : 'swing',

		showCloseButton	 : true,
		showNavArrows : true,
		enableEscapeButton : true,
		enableKeyboardNav : true,

		onStart : function(){},
		onCancel : function(){},
		onComplete : function(){},
		onCleanup : function(){},
		onClosed : function(){},
		onError : function(){}
	};

	$(document).ready(function() {
		$.fancybox.init();
	});

})(jQuery);
/*
 * jQuery Orbit Plugin 1.2.3
 * www.ZURB.com/playground
 * Copyright 2010, ZURB
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
*/
(function($) {
	$.fn.orbit = function(options) {

		//Defaults to extend options
		var defaults = {
			animation: 'horizontal-push',			// fade, horizontal-slide, vertical-slide, horizontal-push
			animationSpeed: 600,				// how fast animtions are
			timer: true,						// true or false to have the timer
			advanceSpeed: 4000,					// if timer is enabled, time between transitions
			pauseOnHover: false,				// if you hover pauses the slider
			startClockOnMouseOut: false,			// if clock should start on MouseOut
			startClockOnMouseOutAfter: 1000,		// how long after MouseOut should the timer start again
			directionalNav: true,				// manual advancing directional navs
			captions: true,					// do you want captions?
			captionAnimation: 'fade',			// fade, slideOpen, none
			captionAnimationSpeed: 600,			// if so how quickly should they animate in
			bullets: false,					// true or false to activate the bullet navigation
			bulletThumbs: false,				// thumbnails for the bullets
			bulletThumbLocation: '',				// location from this file where thumbs will be
			afterSlideChange: function() {
			}		// empty function
		};

		//Extend those options
		var options = $.extend(defaults, options);

		return this.each( function() {

			// ==============
			// ! SETUP
			// ==============

			//Global Variables
			var activeSlide = 0,
			numberSlides = 0,
			orbitWidth,
			orbitHeight,
			locked;

			//Initialize
			var orbit = $(this).addClass('orbit'),
			orbitWrapper = orbit.wrap('<div class="orbit-wrapper" />').parent();
			orbit.add(orbitWidth).width('1px').height('1px');

			//Collect all slides and set slider size of largest image
			var slides = orbit.children('img, a, div');
			slides.each( function() {
				var _slide = $(this),
				_slideWidth = _slide.width(),
				_slideHeight = _slide.height();
				if(_slideWidth > orbit.width()) {
					orbit.add(orbitWrapper).width(_slideWidth);
					orbitWidth = orbit.width();
				}
				if(_slideHeight > orbit.height()) {
					orbit.add(orbitWrapper).height(_slideHeight);
					orbitHeight = orbit.height();
				}
				numberSlides++;
			});
			//Animation locking functions
			function unlock() {
				locked = false;
			}

			function lock() {
				locked = true;
			}

			//If there is only a single slide remove nav, timer and bullets
			if(slides.length === 1) {
				options.directionalNav = false;
				options.timer = false;
				options.bullets = false;
			}

			//Set initial front photo z-index and fades it in
			slides.eq(activeSlide)
			.css({"z-index" : 3})
			.fadeIn( function() {
				//brings in all other slides IF css declares a display: none
				slides.css({"display":"block"});
			});
			// ==============
			// ! TIMER
			// ==============

			//Timer Execution
			function startClock() {
				if(!options.timer  || options.timer === 'false') {
					return false;
					//if timer is hidden, don't need to do crazy calculations
				} else if(timer.is(':hidden')) {
					clock = setInterval( function(e) {
						shift("next");
					}, options.advanceSpeed);
					//if timer is visible and working, let's do some math
				} else {
					timerRunning = true;
					pause.removeClass('active');
					clock = setInterval( function(e) {
						var degreeCSS = "rotate("+degrees+"deg)";
						degrees += 2;
						rotator.css({
							"-webkit-transform": degreeCSS,
							"-moz-transform": degreeCSS,
							"-o-transform": degreeCSS
						});
						if(degrees > 180) {
							rotator.addClass('move');
							mask.addClass('move');
						}
						if(degrees > 360) {
							rotator.removeClass('move');
							mask.removeClass('move');
							degrees = 0;
							shift("next");
						}
					}, options.advanceSpeed/180);
				}
			}

			function stopClock() {
				if(!options.timer || options.timer === 'false') {
					return false;
				} else {
					timerRunning = false;
					clearInterval(clock);
					pause.addClass('active');
				}
			}

			//Timer Setup
			if(options.timer) {
				var timerHTML = '<div class="timer"><span class="mask"><span class="rotator"></span></span><span class="pause"></span></div>';
				orbitWrapper.append(timerHTML);
				var timer = $('div.timer'),
				timerRunning;
				if(timer.length !== 0) {
					var rotator = $('div.timer span.rotator'),
					mask = $('div.timer span.mask'),
					pause = $('div.timer span.pause'),
					degrees = 0,
					clock;
					startClock();
					timer.click( function() {
						if(!timerRunning) {
							startClock();
						} else {
							stopClock();
						}
					});
					if(options.startClockOnMouseOut) {
						var outTimer;
						orbitWrapper.mouseleave( function() {
							outTimer = setTimeout( function() {
								if(!timerRunning) {
									startClock();
								}
							}, options.startClockOnMouseOutAfter);
						});
						orbitWrapper.mouseenter( function() {
							clearTimeout(outTimer);
						});
					}
				}
			}

			//Pause Timer on hover
			if(options.pauseOnHover) {
				orbitWrapper.mouseenter( function() {
					stopClock();
				});
			}

			// ==============
			// ! CAPTIONS
			// ==============

			//Caption Execution
			function setCaption() {
				if(!options.captions || options.captions === "false") {
					return false;
				} else {
					var _captionLocation = slides.eq(activeSlide).data('caption'); //get ID from rel tag on image
					_captionHTML = $(_captionLocation).html(); //get HTML from the matching HTML entity
					//Set HTML for the caption if it exists
					if(_captionHTML) {
						caption
						.attr('id',_captionLocation) // Add ID caption
						.html(_captionHTML); // Change HTML in Caption
						//Animations for Caption entrances
						if(options.captionAnimation === 'none') {
							caption.show();
						}
						if(options.captionAnimation === 'fade') {
							caption.fadeIn(options.captionAnimationSpeed);
						}
						if(options.captionAnimation === 'slideOpen') {
							caption.slideDown(options.captionAnimationSpeed);
						}
					} else {
						//Animations for Caption exits
						if(options.captionAnimation === 'none') {
							caption.hide();
						}
						if(options.captionAnimation === 'fade') {
							caption.fadeOut(options.captionAnimationSpeed);
						}
						if(options.captionAnimation === 'slideOpen') {
							caption.slideUp(options.captionAnimationSpeed);
						}
					}
				}
			}

			//Caption Setup
			if(options.captions) {
				var captionHTML = '<div class="orbit-caption"></div>';
				orbitWrapper.append(captionHTML);
				var caption = orbitWrapper.children('.orbit-caption');
				setCaption();
			}
			
			// ==================
			// ! DIRECTIONAL NAV
			// ==================

			//DirectionalNav { rightButton --> shift("next"), leftButton --> shift("prev");
			if(options.directionalNav) {
				if(options.directionalNav === "false") {
					return false;
				}
				var directionalNavHTML = '<div class="slider-nav"><span class="right">Right</span><span class="left">Left</span></div>';
				orbitWrapper.append(directionalNavHTML);
				var leftBtn = orbitWrapper.children('div.slider-nav').children('span.left'),
				rightBtn = orbitWrapper.children('div.slider-nav').children('span.right');
				leftBtn.click( function() {
					stopClock();
					shift("prev");
				});
				rightBtn.click( function() {
					stopClock();
					shift("next");
				});
			}

			// ==================
			// ! BULLET NAV
			// ==================

			//Bullet Nav Setup
			if(options.bullets) {
				var bulletHTML = '<ul class="orbit-bullets"></ul>';
				orbitWrapper.append(bulletHTML);
				var bullets = $('ul.orbit-bullets');
				for(i=0; i<numberSlides; i++) {
					var liMarkup = $('<li>'+(i+1)+'</li>');
					if(options.bulletThumbs) {
						var	thumbName = slides.eq(i).data('thumb');
						if(thumbName) {
							var liMarkup = $('<li class="has-thumb">'+i+'</li>');
							liMarkup.css({"background" : "url("+options.bulletThumbLocation+thumbName+") no-repeat"});
						}
					}
					$('ul.orbit-bullets').append(liMarkup);
					liMarkup.data('index',i);
					liMarkup.click( function() {
						stopClock();
						shift($(this).data('index'));
					});
				}
				setActiveBullet();
			}

			//Bullet Nav Execution
			function setActiveBullet() {
				if(!options.bullets) {
					return false;
				} else {
					bullets.children('li').removeClass('active').eq(activeSlide).addClass('active');
				}
			}

			// ====================
			// ! SHIFT ANIMATIONS
			// ====================

			//Animating the shift!
			function shift(direction) {
				//remember previous activeSlide
				var prevActiveSlide = activeSlide,
				slideDirection = direction;
				//exit function if bullet clicked is same as the current image
				if(prevActiveSlide === slideDirection) {
					return false;
				}
				//reset Z & Unlock
				function resetAndUnlock() {
					slides
					.eq(prevActiveSlide)
					.css({"z-index" : 1});
					unlock();
					options.afterSlideChange.call(this);
				}

				if(slides.length === "1") {
					return false;
				}
				if(!locked) {
					lock();
					//deduce the proper activeImage
					if(direction === "next") {
						activeSlide++;
						if(activeSlide === numberSlides) {
							activeSlide = 0;
						}
					} else if(direction === "prev") {
						activeSlide--;
						if(activeSlide < 0) {
							activeSlide = numberSlides-1;
						}
					} else {
						activeSlide = direction;
						if (prevActiveSlide < activeSlide) {
							slideDirection = "next";
						} else if (prevActiveSlide > activeSlide) {
							slideDirection = "prev";
						}
					}
					//set to correct bullet
					setActiveBullet();

					//set previous slide z-index to one below what new activeSlide will be
					slides
					.eq(prevActiveSlide)
					.css({"z-index" : 2});

					//fade
					if(options.animation === "fade") {
						slides
						.eq(activeSlide)
						.css({"opacity" : 0, "z-index" : 3})
						.animate({"opacity" : 1}, options.animationSpeed, resetAndUnlock);
					}
					//horizontal-slide
					if(options.animation === "horizontal-slide") {
						if(slideDirection === "next") {
							slides
							.eq(activeSlide)
							.css({"left": orbitWidth, "z-index" : 3})
							.animate({"left" : 0}, options.animationSpeed, resetAndUnlock);
						}
						if(slideDirection === "prev") {
							slides
							.eq(activeSlide)
							.css({"left": -orbitWidth, "z-index" : 3})
							.animate({"left" : 0}, options.animationSpeed, resetAndUnlock);
						}
					}
					//vertical-slide
					if(options.animation === "vertical-slide") {
						if(slideDirection === "prev") {
							slides
							.eq(activeSlide)
							.css({"top": orbitHeight, "z-index" : 3})
							.animate({"top" : 0}, options.animationSpeed, resetAndUnlock);
						}
						if(slideDirection === "next") {
							slides
							.eq(activeSlide)
							.css({"top": -orbitHeight, "z-index" : 3})
							.animate({"top" : 0}, options.animationSpeed, resetAndUnlock);
						}
					}
					//push-over
					if(options.animation === "horizontal-push") {
						if(slideDirection === "next") {
							slides
							.eq(activeSlide)
							.css({"left": orbitWidth, "z-index" : 3})
							.animate({"left" : 0}, options.animationSpeed, resetAndUnlock);
							slides
							.eq(prevActiveSlide)
							.animate({"left" : -orbitWidth}, options.animationSpeed);
						}
						if(slideDirection === "prev") {
							slides
							.eq(activeSlide)
							.css({"left": -orbitWidth, "z-index" : 3})
							.animate({"left" : 0}, options.animationSpeed, resetAndUnlock);
							slides
							.eq(prevActiveSlide)
							.animate({"left" : orbitWidth}, options.animationSpeed);
						}
					}
					setCaption();
				} //lock
			}//orbit function

		});//each call
	};//orbit plugin call
})(jQuery);
        
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
 * Gentlemen, start your engines!
 */
jQuery(document).ready(function($) {

	// Equal column heights
	    // get the heights
	    l = $('#sidebar-primary').height();
	    r = $('#content').height();
	
	    // get maximum heights of all columns
	    h = Math.max(Math.max(l, r));
	
	    // apply it
	    $('#sidebar-primary').height(h);
	    $('#content').height(h);

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
         $('#slider').orbit({
			'animation' : 'horizontal-push',
			'timer' : true,
			'advanceSpeed' : 7000,
			'bullets' : true,
			'startClockOnMouseOut': true,
			'startClockOnMouseOutAfter': 0
         });
		
});