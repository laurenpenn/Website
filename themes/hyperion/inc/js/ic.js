/*
 * jQuery infinitecarousel plugin
 * @author admin@catchmyfame.com - http://www.catchmyfame.com
 * @version 1.2.2
 * @date August 31, 2009
 * @category jQuery plugin
 * @copyright (c) 2009 admin@catchmyfame.com (www.catchmyfame.com)
 * @license CC Attribution-Share Alike 3.0 - http://creativecommons.org/licenses/by-sa/3.0/
 */

(function($){
	$.fn.extend({ 
		infiniteCarousel: function(options)
		{
			var defaults = 
			{
				transitionSpeed : 800,
				displayTime : 4000,
				textholderHeight : .2,
				displayProgressBar : 1,
				displayThumbnails: 1,
				displayThumbnailNumbers: 1,
				displayThumbnailBackground: 1,
				easeLeft: 'linear',
				easeRight: 'linear',				
				thumbnailWidth: '20px',
				thumbnailHeight: '20px',
				thumbnailFontSize: '.7em'
			};
		var options = $.extend(defaults, options);
	
    		return this.each(function() {
    			var randID = Math.round(Math.random()*100000000);
				var o=options;
				var obj = $(this);
				var curr = 1;

				var numImages = $('img', obj).length; // Number of images
				var imgHeight = $('img:first', obj).height();
				var imgWidth = $('img:first', obj).width();
				var autopilot = 1;
		
				$('span', obj).hide(); // Hide any text paragraphs in the carousel
				$(obj).width(imgWidth).height(imgHeight);
			
				// Build progress bar
				if(o.displayProgressBar)
				{
					$(obj).append('<div id="progress'+randID+'" style="position:absolute;bottom:0;background:#bbb;left:'+$(obj).css('paddingLeft')+'"></div>');
					$('#progress'+randID).width(imgWidth).height(5).css('opacity','.5');
				}
			
				// Move last image and stick it on the front
				$(obj).css({'position':'relative'});
				$('li:last', obj).prependTo($('ul', obj));
				$('ul', obj).css('left',-imgWidth+'px');
				$('ul',obj).width(9999);

				$('ul',obj).css({'list-style':'none','margin':'0','padding':'0','position':'relative'});
				$('li',obj).css({'display':'inline','float':'left'});
			
				// Build textholder div thats as wide as the carousel and 20%-25% of the height
				$(obj).append('<div id="textholder'+randID+'" class="textholder" style="position:absolute;bottom:0px;margin-bottom:'+-imgHeight*o.textholderHeight+'px;left:'+$(obj).css('paddingLeft')+'"></div>');
				var correctTHWidth = parseInt($('#textholder'+randID).css('paddingTop'));
				var correctTHHeight = parseInt($('#textholder'+randID).css('paddingRight'));
				$('#textholder'+randID).width(imgWidth-(correctTHWidth * 2)).height((imgHeight*o.textholderHeight)-(correctTHHeight * 2));
				showtext($('li:eq(1) span', obj).html());
			
				// Prev/next button(img) 
				html = '';
				html += '';
				$(obj).append(html);
			 
				// Pause/play button(img)	
				html = '';
				html += '';
				$(obj).append(html);
				$('#pause_btn'+randID).css('opacity','.5').hover(function(){$(this).animate({opacity:'1'},250)},function(){$(this).animate({opacity:'.5'},250)});
				$('#pause_btn'+randID).click(function(){
					autopilot = 0;
					$('#progress'+randID).stop().fadeOut();
					clearTimeout(clearInt);
					$('#pause_btn'+randID).fadeOut(250);
					$('#play_btn'+randID).fadeIn(250);
					showminmax();
				});
				$('#play_btn'+randID).css('opacity','.5').hover(function(){$(this).animate({opacity:'1'},250)},function(){$(this).animate({opacity:'.5'},250)});
				$('#play_btn'+randID).click(function(){
					autopilot = 1;
					anim('next');
					$('#play_btn'+randID).hide();
					clearInt=setInterval(function(){anim('next');},o.displayTime+o.transitionSpeed);
					setTimeout(function(){$('#pause_btn'+randID).show();$('#progress'+randID).fadeIn().width(imgWidth).height(5);},o.transitionSpeed);
				});
				
				// Left and right arrow image button actions
				$('#btn_rt'+randID).click(function(){
					autopilot = 0;
					$('#progress'+randID).stop().fadeOut();
					anim('next');
					setTimeout(function(){$('#play_btn'+randID).fadeIn(250);},o.transitionSpeed);
					clearTimeout(clearInt);
				});
				$('#btn_lt'+randID).css('opacity','1').click(function(){
					autopilot = 0;
					$('#progress'+randID).stop().fadeOut();
					anim('prev');
					setTimeout(function(){$('#play_btn'+randID).fadeIn(250);},o.transitionSpeed);
					clearTimeout(clearInt);
				});

				if(o.displayThumbnails)
				{
					// Build thumbnail viewer and thumbnail divs
					$(obj).after('<div class="thumbs" id="thumbs'+randID+'"></div>');
					$('#thumbs'+randID);
					for(i=0;i<=numImages-1;i++)
					{
						thumb = $('img:eq('+(i+1)+')', obj).attr('src');
						$('#thumbs'+randID).append('<a class="thumb" id="thumb'+randID+'_'+(i+1)+'">'+(i+1)+'</a>');
						if(i==0) $('#thumb'+randID+'_1').addClass("active");
					}
					// Next two lines are a special case to handle the first list element which was originally the last
					thumb = $('img:first', obj).attr('src');
					$('#thumb'+randID+'_'+numImages);
					$('#thumbs'+randID+' a.thumb:not(:first)').css({'opacity':'.65'}).removeClass("active"); // makes all thumbs 65% opaque except the first one
					$('#thumbs'+randID+' a.thumb').hover(function(){$(this).animate({'opacity':.99},150)},function(){if(curr!=this.id.split('_')[1]) $(this).animate({'opacity':.65},250)}); // add hover to thumbs

					// Assign click handler for the thumbnails. Normally the format $('.thumb') would work but since it's outside of our object (obj) it would get called multiple times
					$('#thumbs'+randID+' a').bind('click', thumbclick); // We use bind instead of just plain click so that we can repeatedly remove and reattach the handler
				
					if(!o.displayThumbnailNumbers) $('#thumbs'+randID+' a').text('');
					if(!o.displayThumbnailBackground) $('#thumbs'+randID+' a');
				}
				function thumbclick(event)
				{
					target_num = this.id.split('_'); // we want target_num[1]
					if(curr != target_num[1])
					{
						$('#thumb'+randID+'_'+curr).css({'border-color':'#ccc'});
						$('#progress'+randID).stop().fadeOut();
						clearTimeout(clearInt);
						//alert(event.data.src+' '+this.id+' '+target_num[1]+' '+curr);
						$('#thumbs'+randID+' a').css({'cursor':'default'}).unbind('click'); // Unbind the thumbnail click event until the transition has ended
						autopilot = 0;
						setTimeout(function(){$('#play_btn'+randID).fadeIn(250);},o.transitionSpeed);
					}
					if(target_num[1] > curr)
					{
						diff = target_num[1] - curr;
						anim('next',diff);
					}
					if(target_num[1] < curr)
					{
						diff = curr - target_num[1];
						anim('prev', diff);
					}
				}

				function showtext(t)
				{
					// the text will always be the text of the second list item (if it exists)
					if(t != null)
					{
						$('#textholder'+randID).html(t).animate({marginBottom:'-80px', 'opacity':1},500); // Raise textholder
						showminmax();
					}
				}
				function showminmax()
				{
						if(!autopilot)
						{
							
							$('#textholder'+randID).append(html);
							$('#min').fadeIn(250).click(function(){$('#textholder'+randID).animate({marginBottom:'-80px','opacity':1},500,function(){$("#min,#max").toggle();});});
							$('#max').click(function(){$('#textholder'+randID).animate({marginBottom:'-80px', 'opacity':1},500,function(){$("#min,#max").toggle();});});
							$('#close').fadeIn(250).click(function(){$('#textholder'+randID).animate({marginBottom:'-80px', 'opacity':1},500);});
						}
				}
				function borderpatrol(elem)
				{
					$('#thumbs'+randID+' a').css({'border-color':'#ccc'}).animate({opacity: 0.65},500).removeClass("active");
					setTimeout(function(){elem.addClass("active").animate({'opacity': .99},500);},o.transitionSpeed);
				}
				function anim(direction,dist)
				{
					// Fade left/right arrows out when transitioning
					//$('#btn_rt'+randID).fadeOut(500);
					//$('#btn_lt'+randID).fadeOut(500);
					
					// animate textholder out of frame
					$('#textholder'+randID).animate({'opacity':0},500);					
					//?? Fade out play/pause?
					$('#pause_btn'+randID).fadeOut(250);
					$('#play_btn'+randID).fadeOut(250);

					if(direction == "next")
					{
						if(curr==numImages) curr=0;
						if(dist>1)
						{
							borderpatrol($('#thumb'+randID+'_'+(curr+dist)));
							$('li:lt(2)', obj).clone().insertAfter($('li:last', obj));
							$('ul', obj).animate({left:-imgWidth*(dist+1)},o.transitionSpeed,o.easeLeft,function(){
								$('li:lt(2)', obj).remove();
								for(j=1;j<=dist-2;j++)
								{
									$('li:first', obj).clone().insertAfter($('li:last', obj));
									$('li:first', obj).remove();
								}
								//$('#btn_rt'+randID).fadeIn(500);
								//$('#btn_lt'+randID).fadeIn(500);
								$('#play_btn'+randID).fadeIn(250);
								showtext($('li:eq(1) span', obj).html());
								$(this).css({'left':-imgWidth});
								curr = curr+dist;
								$('#thumbs'+randID+' a').bind('click', thumbclick).css({'cursor':'pointer'});
							});
						}
						else
						{
							borderpatrol($('#thumb'+randID+'_'+(curr+1)));
							$('#thumbs'+randID+' a').css({'cursor':'default'}).unbind('click'); // Unbind the thumbnail click event until the transition has ended
							// Copy leftmost (first) li and insert it after the last li
							$('li:first', obj).clone().insertAfter($('li:last', obj));	
							// Update width and left position of ul and animate ul to the left
							$('ul', obj)
								.animate({left:-imgWidth*2},o.transitionSpeed,o.easeLeft,function(){
									$('li:first', obj).remove();
									$('ul', obj).css('left',-imgWidth+'px');
									//$('#btn_rt'+randID).fadeIn(500);
									//$('#btn_lt'+randID).fadeIn(500);
									if(autopilot) $('#pause_btn'+randID).fadeIn(250);
									showtext($('li:eq(1) span', obj).html());
									if(autopilot)
									{
										$('#progress'+randID).width(imgWidth).height(5);
										$('#progress'+randID).animate({'width':0},o.displayTime,function(){
											$('#pause_btn'+randID).fadeOut(50);
											setTimeout(function(){$('#pause_btn'+randID).fadeIn(250)},o.transitionSpeed,o.easeLeft)
										});
									}
									curr=curr+1;
									$('#thumbs'+randID+' a').bind('click', thumbclick).css({'cursor':'pointer'});
								});
						}
					}
					if(direction == "prev")
					{
						if(dist>1)
						{
							borderpatrol($('#thumb'+randID+'_'+(curr-dist)));
							$('li:gt('+(numImages-(dist+1))+')', obj).clone().insertBefore($('li:first', obj));
							$('ul', obj).css({'left':(-imgWidth*(dist+1))}).animate({left:-imgWidth},o.transitionSpeed,function(){
								$('li:gt('+(numImages-1)+')', obj).remove();
								//$('#btn_rt'+randID).fadeIn(500);
								//$('#btn_lt'+randID).fadeIn(500);
								$('#play_btn'+randID).fadeIn(250);
								showtext($('li:eq(1) span', obj).html());
								curr = curr - dist;
								$('#thumbs'+randID+' a').bind('click', thumbclick).css({'cursor':'pointer'});
							});
						}
						else
						{
							borderpatrol($('#thumb'+randID+'_'+(curr-1)));
							$('#thumbs'+randID+' a').css({'cursor':'default'}).unbind('click'); // Unbind the thumbnail click event until the transition has ended
							// Copy rightmost (last) li and insert it after the first li
							$('li:last', obj).clone().insertBefore($('li:first', obj));
							// Update width and left position of ul and animate ul to the right
							$('ul', obj)
								.css('left',-imgWidth*2+'px')
								.animate({left:-imgWidth},o.transitionSpeed,function(){
									$('li:last', obj).remove();
									//$('#btn_rt'+randID).fadeIn(500);
									//$('#btn_lt'+randID).fadeIn(500);
									if(autopilot) $('#pause_btn'+randID).fadeIn(250);
									showtext($('li:eq(1) span', obj).html());
									curr=curr-1;
									if(curr==0) curr=numImages;
									$('#thumbs'+randID+' a').bind('click', thumbclick).css({'cursor':'pointer'});
								});
						}
					}
				}

				var clearInt = setInterval(function(){anim('next');},o.displayTime+o.transitionSpeed);
				$('#progress'+randID).animate({'width':0},o.displayTime+o.transitionSpeed,function(){
					$('#pause_btn'+randID).fadeOut(100);
					setTimeout(function(){$('#pause_btn'+randID).fadeIn(250)},o.transitionSpeed)
				});
  		});
    	}
	});
})(jQuery);