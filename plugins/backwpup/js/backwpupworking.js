jQuery(document).ready( function($) {
	if ($('#alternate_wp_cron').val()=='1') {
			$.ajax({
				type: 'POST',
				url: $('#backwpuprunurl').val(),
				cache: false,
				timeout: 3000,
				async: false,
				data: {
					nonce: $('#alternate_wp_cron_nonce').val(),
					type:  'javastart'
				}
			});
	};
	if ($('#logfile').length>0) {
		var refreshId = setInterval(function() {
			$.ajax({
				type: 'POST',
				url: $('#backwpupworkingajaxurl').val(),
				cache: false,
				data: {
					logfile: $('#logfile').val(),
					logpos:  $('#logpos').val()
				},
				dataType: 'json',
				success: function(rundata) {
					$('#logpos').val(rundata.logpos);
					if ( '' != rundata.LOG ) {
						$('#showworking').append(rundata.LOG);
						$('#showworking').scrollTop(rundata.logpos*12);
					}
					if ( 0 < rundata.ERROR ) {
						$('#errors').replaceWith('<span id="errors">'+rundata.ERROR+'</span>');
						$('#errorid').show();
					}
					if ( 0 < rundata.WARNING ) {
						$('#warnings').replaceWith('<span id="warnings">'+rundata.WARNING+'</span>');
						$('#warningsid').show();
					}
					if ( 0 < rundata.STEPSPERSENT ) {
						$('#progressstep').replaceWith('<div id="progressstep">'+rundata.STEPSPERSENT+'%</div>');
						$('#progressstep').css('width', parseFloat(rundata.STEPSPERSENT)+'%');
						$('.progressbar').show();
					}
					if ( 0 < rundata.STEPPERSENT ) {
						$('#progresssteps').replaceWith('<div id="progresssteps">'+rundata.STEPPERSENT+'%</div>');
						$('#progresssteps').css('width', parseFloat(rundata.STEPPERSENT)+'%');
						$('.progressbar').show();
					}						
				}
			});
			$("#stopworking").each(function(index) {
				$("#message").remove();
				clearInterval(refreshId);
			});
		}, 1000);
	}
});

