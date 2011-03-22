jQuery(document).ready(function($) {
//Ministry Guide Open/Close
	$('#ministry-guide-link a').click(function() {
		$(this).toggleClass('open');
		$('#ministry-guide').slideToggle('slow');
		return false;
	});
});