podPress_jQuery(document).ready(function() {
	// Feed/iTunes Settings Accordion
	podPress_jQuery( '#podpress_accordion' ).accordion({
		header: 'h4',
		autoHeight: false
	});
	
	// Preview Windows for the Feed images
	podPress_jQuery( '.podpress_rssimage_preview' ).dialog({
		autoOpen: false,
		modal: true,
		minWidth: 200,
		minHeight: 144,
		open: function(event, ui) { 
			// hide all form elements which may cause problems with z-index in IE 6 and older versions
			if ( podPress_jQuery.browser.msie && 7 > parseInt(podPress_jQuery.browser.version) ) {
				podPress_jQuery(':input').css('visibility', 'hidden');
			}
		},
		close: function(event, ui) { 
			if ( podPress_jQuery.browser.msie && 7 > parseInt(podPress_jQuery.browser.version) ) {
				podPress_jQuery(':input').css('visibility', 'visible');
			}
		},
		resizable: false
	});
 	podPress_jQuery( '.podpress_itunesimage_preview' ).dialog({
		autoOpen: false,
		modal: true,
		minWidth: 320,
		minHeight: 300,
		open: function(event, ui) { 
			// hide all form elements which may cause problems with z-index in IE 6 and older versions
			if ( podPress_jQuery.browser.msie && 7 > parseInt(podPress_jQuery.browser.version) ) {
				podPress_jQuery(':input').css('visibility', 'hidden');
			}
		},
		close: function(event, ui) { 
			if ( podPress_jQuery.browser.msie && 7 > parseInt(podPress_jQuery.browser.version) ) {
				podPress_jQuery(':input').css('visibility', 'visible');
			}
		},
		resizable: false
	});
	
	//~ podPress_jQuery( '#podpress_its_preview' ).dialog({
		//~ autoOpen: false,
		//~ modal: true,
 		//~ minWidth: 700,
		//~ minHeight: 400,
		//~ resizable: true,
		//~ open: function(event, ui) {
			
			//~ podPress_jQuery( '#podpress_its_preview iframe' ).attr('src',  'http://phobos.apple.com/WebObjects/MZStore.woa/wa/viewPodcast?id=' + String(podPress_jQuery('#iTunesFeedID').val()) );
			
			//~ // hide all form elements which may cause problems with z-index in IE 6 and older versions
			//~ if ( podPress_jQuery.browser.msie && 7 > parseInt(podPress_jQuery.browser.version) ) {
				//~ podPress_jQuery(':input').css('visibility', 'hidden');
			//~ }
		//~ },
		//~ close: function(event, ui) { 
			//~ if ( podPress_jQuery.browser.msie && 7 > parseInt(podPress_jQuery.browser.version) ) {
				//~ podPress_jQuery(':input').css('visibility', 'visible');
			//~ }
		//~ }
	//~ });

});