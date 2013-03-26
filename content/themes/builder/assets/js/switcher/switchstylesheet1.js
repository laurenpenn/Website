/**
* Styleswitch stylesheet switcher built on jQuery
* Under an Attribution, Share Alike License
* By Kelvin Luck ( http://www.kelvinluck.com/ )
**/

(function($)
{
	$(document).ready(function() {
		$('.styleswitch_l').click(function()
		{
			switchStylestyle_l(this.getAttribute("rel"));
			return false;
		});
		var c = readCookie('style_l');
		if (c) switchStylestyle_l(c);
	});

	function switchStylestyle_l(styleName)
	{
		$('link[@rel*=style][title]').each(function(i) 
		{
			this.disabled = true;
			if (this.getAttribute('title') == styleName) this.disabled = false;
		});
		createCookie('style_l', styleName, 365);
	}
})(jQuery);
(function($)
{
	$(document).ready(function() {
		$('.bgstyleswitch_l').click(function()
		{
			bgswitchStylestyle_l(this.getAttribute("rel"));
			return false;
		});
		var c = readCookie('bgstyle_l');
		if (c) bgswitchStylestyle_l(c);
	});

	function bgswitchStylestyle_l(bgstyleName)
	{
		$('link[@rel*=bgstyle][title]').each(function(i) 
		{
			this.disabled = true;
			if (this.getAttribute('title') == bgstyleName) this.disabled = false;
		});
		createCookie('bgstyle_l', bgstyleName, 365);
	}
})(jQuery);
// cookie functions http://www.quirksmode.org/js/cookies.html
function createCookie(name,value,days)
{
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*100));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
function readCookie(name)
{
	var nameEQ = name + "l=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++)
	{
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
function eraseCookie(name)
{
	createCookie(name,"",-1);
}
// /cookie functions