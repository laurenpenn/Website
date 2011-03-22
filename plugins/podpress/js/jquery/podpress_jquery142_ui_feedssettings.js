podPress_jQuery142(document).ready(function() {
	// Feed/iTunes Settings Accordion
	podPress_jQuery142( '#podpress_accordion' ).accordion({
		header: 'h4',
		autoHeight: false
	});
	
	// Preview Windows for the Feed images
	podPress_jQuery142( '.podpress_rssimage_preview' ).dialog({
		autoOpen: false,
		modal: true,
		minWidth: 200,
		minHeight: 144,
		open: function(event, ui) { 
			// hide all form elements which may cause problems with z-index in IE 6 and older versions
			if ( podPress_jQuery142.browser.msie && 7 > parseInt(podPress_jQuery142.browser.version) ) {
				podPress_jQuery142(':input').css('visibility', 'hidden');
			}
		},
		close: function(event, ui) { 
			if ( podPress_jQuery142.browser.msie && 7 > parseInt(podPress_jQuery142.browser.version) ) {
				podPress_jQuery142(':input').css('visibility', 'visible');
			}
		},
		resizable: false
	});
 	podPress_jQuery142( '.podpress_itunesimage_preview' ).dialog({
		autoOpen: false,
		modal: true,
		minWidth: 320,
		minHeight: 300,
		open: function(event, ui) { 
			// hide all form elements which may cause problems with z-index in IE 6 and older versions
			if ( podPress_jQuery142.browser.msie && 7 > parseInt(podPress_jQuery142.browser.version) ) {
				podPress_jQuery142(':input').css('visibility', 'hidden');
			}
		},
		close: function(event, ui) { 
			if ( podPress_jQuery142.browser.msie && 7 > parseInt(podPress_jQuery142.browser.version) ) {
				podPress_jQuery142(':input').css('visibility', 'visible');
			}
		},
		resizable: false
	});
});