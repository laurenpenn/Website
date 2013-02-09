podPress_jQuery(document).ready(function() {
	// Widget Settings Accordion (on page loading)
	podPress_jQuery( '.podpress_widget_accordion' ).accordion({
		header: 'h5',
		autoHeight: false
	});
	
	// bind the Accordion effect after adding the widget to a sidebar in a pre WP 2.8 sidebar
	podPress_jQuery('a.widget-control-add').bind('click', function() {
		var podpress_a_widget_id = String(podPress_jQuery(this).closest('li').attr('id'));
		if ( podpress_a_widget_id.search(/podpressfeedbuttons/i)  != -1 ) {
			if ( podPress_jQuery.browser.msie ) {
				podPress_jQuery( '.podpress_widget_accordion' ).accordion( 'destroy' );
			}
			podPress_jQuery( '.podpress_widget_accordion' ).accordion({
				header: 'h5',
				autoHeight: false
			});
			if ( podPress_jQuery.browser.safari ) {
				if (podPress_jQuery( '#podPressFeedButtons-buttons' ).attr('checked') == 'checked') {
					podPress_jQuery( '#podPressFeedButtons-buttons' ).click();
				} else {
					podPress_jQuery( '#podPressFeedButtons-text' ).click();
				}
			}
		}
	});
});