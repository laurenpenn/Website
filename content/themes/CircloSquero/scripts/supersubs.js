
/*
 * Supersubs v0.2b - jQuery plugin
 * Copyright (c) 2008 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 *
 * This plugin automatically adjusts submenu widths of suckerfish-style menus to that of
 * their longest list item children. If you use this, please expect bugs and report them
 * to the jQuery Google Group with the word 'Superfish' in the subject line.
 *
 */

;(function(jQuery){ // jQuery will refer to jQuery within this closure

	jQuery.fn.supersubs = function(options){
		var opts = jQuery.extend({}, jQuery.fn.supersubs.defaults, options);
		// return original object to support chaining
		return this.each(function() {
			// cache selections
			var jQueryjQuery = jQuery(this);
			// support metadata
			var o = jQuery.meta ? jQuery.extend({}, opts, jQueryjQuery.data()) : opts;
			// get the font size of menu.
			// .css('fontSize') returns various results cross-browser, so measure an em dash instead
			var fontsize = jQuery('<li id="menu-fontsize">&#8212;</li>').css({
				'padding' : 0,
				'position' : 'absolute',
				'top' : '-999em',
				'width' : 'auto'
			}).appendTo(jQueryjQuery).width(); //clientWidth is faster, but was incorrect here
			// remove em dash
			jQuery('#menu-fontsize').remove();
			// cache all ul elements
			jQueryULs = jQueryjQuery.find('ul');
			// loop through each ul in menu
			jQueryULs.each(function(i) {	
				// cache this ul
				var jQueryul = jQueryULs.eq(i);
				// get all (li) children of this ul
				var jQueryLIs = jQueryul.children();
				// get all anchor grand-children
				var jQueryAs = jQueryLIs.children('a');
				// force content to one line and save current float property
				var liFloat = jQueryLIs.css('white-space','nowrap').css('float');
				// remove width restrictions and floats so elements remain vertically stacked
				var emWidth = jQueryul.add(jQueryLIs).add(jQueryAs).css({
					'float' : 'none',
					'width'	: 'auto'
				})
				// this ul will now be shrink-wrapped to longest li due to position:absolute
				// so save its width as ems. Clientwidth is 2 times faster than .width() - thanks Dan Switzer
				.end().end()[0].clientWidth / fontsize;
				// add more width to ensure lines don't turn over at certain sizes in various browsers
				emWidth += o.extraWidth;
				// restrict to at least minWidth and at most maxWidth
				if (emWidth > o.maxWidth)		{ emWidth = o.maxWidth; }
				else if (emWidth < o.minWidth)	{ emWidth = o.minWidth; }
				emWidth += 'em';
				// set ul to width in ems
				jQueryul.css('width',emWidth);
				// restore li floats to avoid IE bugs
				// set li width to full width of this ul
				// revert white-space to normal
				jQueryLIs.css({
					'float' : liFloat,
					'width' : '100%',
					'white-space' : 'normal'
				})
				// update offset position of descendant ul to reflect new width of parent
				.each(function(){
					var jQuerychildUl = jQuery('>ul',this);
					var offsetDirection = jQuerychildUl.css('left')!==undefined ? 'left' : 'right';
					jQuerychildUl.css(offsetDirection,emWidth);
				});
			});
			
		});
	};
	// expose defaults
	jQuery.fn.supersubs.defaults = {
		minWidth		: 9,		// requires em unit.
		maxWidth		: 25,		// requires em unit.
		extraWidth		: 0			// extra width can ensure lines don't sometimes turn over due to slight browser differences in how they round-off values
	};
	
})(jQuery); // plugin code ends
