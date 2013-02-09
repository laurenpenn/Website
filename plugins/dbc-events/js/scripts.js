jQuery(document).ready(function ($) {
	$(".dbc-event-date").datepicker({
		dateFormat: 'D, M d, yy',
		showOn: 'button',
		buttonImage: 'http://dentonbible.org/wp-content/plugins/dbc-events/images/icon-datepicker.png',
		buttonImageOnly: true,
		numberOfMonths: 3,
		beforeShow: function(input, inst) {       
			window.setTimeout(function(){
				$(inst.dpDiv).find('.ui-state-highlight.ui-state-hover').removeClass('ui-state-highlight ui-state-hover')      
			},0)     
		}
	});

});