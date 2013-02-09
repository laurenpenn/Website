jQuery(document).ready(function() {
	try {

		if (! jQuery('#wpcf7-campaignmonitor-active').is(':checked'))
			jQuery('#cf7cmdiv .mail-fields').hide();

		jQuery('#wpcf7-campaignmonitor-active').click(function() {
			if (jQuery('#cf7cmdiv .mail-fields').is(':hidden')
			&& jQuery('#wpcf7-campaignmonitor-active').is(':checked')) {
				jQuery('#cf7cmdiv .mail-fields').slideDown('fast');
				if (jQuery('.campaignmonitor-custom-fields').is(':hidden')
				&& jQuery('#wpcf7-campaignmonitor-cf-active').is(':checked')) {
					jQuery('.campaignmonitor-custom-fields').slideDown('fast');
				}
			} else if (jQuery('#cf7cmdiv .mail-fields').is(':visible')
			&& jQuery('#wpcf7-campaignmonitor-active').not(':checked')) {
				jQuery('#cf7cmdiv .mail-fields').slideUp('fast');
			}
		});
		
		if (! jQuery('#wpcf7-campaignmonitor-cf-active').is(':checked'))
			jQuery('.campaignmonitor-custom-fields').hide();

		jQuery('#wpcf7-campaignmonitor-cf-active').click(function() {
			if (jQuery('.campaignmonitor-custom-fields').is(':hidden')
			&& jQuery('#wpcf7-campaignmonitor-cf-active').is(':checked')) {
				jQuery('.campaignmonitor-custom-fields').slideDown('fast');
			} else if (jQuery('.campaignmonitor-custom-fields').is(':visible')
			&& jQuery('#wpcf7-campaignmonitor-cf-active').not(':checked')) {
				jQuery('.campaignmonitor-custom-fields').slideUp('fast');
			}
		});

	} catch (e) {
	}
});