jQuery(document).ready( function($) {
	$('#mailmethod').change(function() {
		if ( 'SMTP' == $('#mailmethod').val()) {
			$('.mailsmtp').show();
			$('#mailsendmail').hide();
		} else if ( 'Sendmail' == $('#mailmethod').val()) {
			$('.mailsmtp').hide();
			$('#mailsendmail').show();
		} else {
			$('.mailsmtp').hide();
			$('#mailsendmail').hide();		
		}
	});
});

