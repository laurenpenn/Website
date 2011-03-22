podPress_jQuery142(document).ready(function() {
	// Widget Settings Accordion (on page loading)
	podPress_jQuery142( '.podpress_widget_accordion' ).accordion({
		header: 'h5',
		autoHeight: false
	});
	
	// bind the Accordion effect after adding the widget to a sidebar in a pre WP 2.8 sidebar
	podPress_jQuery142('a.widget-control-add').bind('click', function() {
		var podpress_a_widget_id = String(podPress_jQuery142(this).closest('li').attr('id'));
		if ( podpress_a_widget_id.search(/podpressfeedbuttons/i)  != -1 ) {
			if ( podPress_jQuery142.browser.msie ) {
				podPress_jQuery142( '.podpress_widget_accordion' ).accordion( 'destroy' );
			}
			podPress_jQuery142( '.podpress_widget_accordion' ).accordion({
				header: 'h5',
				autoHeight: false
			});
			if ( podPress_jQuery142.browser.safari ) {
				if (podPress_jQuery142( '#podPressFeedButtons-buttons' ).attr('checked') == 'checked') {
					podPress_jQuery142( '#podPressFeedButtons-buttons' ).click();
				} else {
					podPress_jQuery142( '#podPressFeedButtons-text' ).click();
				}
			}
		}
	});
});