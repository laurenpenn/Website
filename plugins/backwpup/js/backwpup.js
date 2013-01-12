jQuery(document).ready( function($) {
	$('.waiting').each( function (index) {
		var jobid = $(this).attr('id').replace('image-wait-',''),
		    data = {
				action: 'backwpup_show_info_td',
				backwpupajaxpage: 'backwpup',
				jobid: jobid,
				mode: jQuery('input[name="mode"]').val(),
				_ajax_nonce: jQuery('#backwpupajaxnonce').val()
			};
		$.post(ajaxurl, data, function(response) {
			$('#image-wait-' + jobid).css('display','none');
			$('#image-wait-' + jobid).after(response);
		});		
	});	
});

