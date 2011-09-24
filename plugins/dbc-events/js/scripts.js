jQuery(document).ready(function ($) {
	$(".dbc-event-date").datepicker({
		dateFormat: 'D, M d, yy',
		showOn: 'button',
		buttonImage: 'http://dentonbible.org/wp-content/plugins/dbc-events/images/icon-datepicker.png',
		buttonImageOnly: true,
		numberOfMonths: 3
	});
});