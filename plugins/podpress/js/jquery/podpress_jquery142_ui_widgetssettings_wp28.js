podPress_jQuery142(document).ready(function() {
	// Widget Settings Accordion (on page loading)
	podPress_jQuery142( '.podpress_widget_accordion' ).accordion({
		header: 'h5',
		autoHeight: false
	});
	
	// bind the Accordion effect after saving the widgets settings
	podPress_jQuery142('input.widget-control-save').bind('click', function() {
		// get after the click on the Save button the ID of the current widget
		var podpress_a_widget_id = podPress_jQuery142(this).closest('div.widget').attr('id');
		// check whether or not it is a podPress Feed Buttons Widget (podpress_feedbuttons is part of the ID in WP >= 2.8)
		if ( podpress_a_widget_id.search(/podpress_feedbuttons/i)  != -1 ) {
			// if it is a podPress Widget at the accordion effect after the AJAXified saving of the settings is complete (The action is also called "save-widget" when a widget will be removed. But in this case the "delete_widget=1" paramter exists.) 
			jQuery('#'+podpress_a_widget_id).ajaxComplete( function(event, request, settings) {
				if ( settings.data.search(/action=save-widget/i) != -1 && settings.data.search(/delete_widget=1/i) == -1 ) {
					podPress_jQuery142( '.podpress_widget_accordion' ).accordion({
						header: 'h5',
						autoHeight: false
					});
					//alert('A bind\n' + settings.data);
				}
			});
		}
	});
	
	// bind the Accordion effect after adding the widget to a sidebar in a WP 2.8 sidebar
	jQuery('div.widgets-sortables').bind( 'sortstop', function(e, ui) {
		// the sidebar element is a jQuery Sortable list. After you drop a widget on a sidebar (or when you change the order of the list elements) this event happens.
		var podpress_a_widget_id = ui.item.find('a.widget-action').closest('div.widget').attr('id');
		var podpress_fbw_status = 'notloaded';
		if ( podpress_a_widget_id.search(/podpress_feedbuttons/i)  != -1 ) {
			jQuery('#'+podpress_a_widget_id).ajaxComplete( function(event, request, settings) {
				if ( podpress_fbw_status == 'notloaded' && settings.data.search(/action=save-widget/i) != -1 && settings.data.search(/delete_widget=1/i) == -1 ) {
					podPress_jQuery142( '.podpress_widget_accordion' ).accordion({
						header: 'h5',
						autoHeight: false
					});
					podpress_wp28_bind_click(); // in WP 2.8 it is necessary to bind the event handler to the newly added widget
					podpress_fbw_status = 'loaded';
				}
			});
		}
	});
});

function podpress_wp28_bind_click() {
	podPress_jQuery142('input.widget-control-save').bind('click', function() {
		var podpress_a_widget_id = podPress_jQuery142(this).closest('div.widget').attr('id');
		if ( podpress_a_widget_id.search(/podpress_feedbuttons/i)  != -1 ) {
			jQuery('#'+podpress_a_widget_id).ajaxComplete( function(event, request, settings) {
				if ( settings.data.search(/action=save-widget/i) != -1 && settings.data.search(/delete_widget=1/i) == -1 ) {
					podPress_jQuery142( '.podpress_widget_accordion' ).accordion({
						header: 'h5',
						autoHeight: false
					});
					//alert('B bind\n' + settings.data);
				}
			});
		}
	});
}