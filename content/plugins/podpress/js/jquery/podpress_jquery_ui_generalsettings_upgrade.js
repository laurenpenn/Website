/**
* actions of the upgrade dialog on the General Settings page of podPress during the upgrade to 8.8.10.14
* since 8.8.10.14
*/
var podPress_dont_init_stats_upgr = true;
podPress_jQuery(document).ready( function() {
	var table = 'statcounts';
	var w = Math.round(window.innerWidth-(window.innerWidth * 0.1));
	if (w > 900) {
		w = 1000;
	}
	var h = Math.round(window.innerHeight-(window.innerHeight * 0.1));
	if (h > 700) {
		h = 700;
	}
	podPress_jQuery("#podpress_dialog_stats_upgr").dialog({
		modal: true,
		closeOnEscape: false, // close when the ESC button has been pressed
		resizable: true,
		minWidth: 400,
		minHeight: 200,
		width: w,
		height: h,
		open: function(event, ui) {
			jQuery('.ui-dialog-buttonset').prepend('<img src="'+podPressBackendURL + 'images/ajax-loader.gif" alt="Working ..." style="display:none;" id="podpress_upgr_ajax_loader_img" /> ');

			podPress_jQuery(window).bind('beforeunload', function() {
				podpress_stats_status('start');
			});
			
			// stop the download counter
			podpress_stats_status('stop');
			
			// check whether the statcounts table has rows -> if yes then show the statcounts part otherwise show the stats part
			if ( 'true' == jQuery('#podpress_is_statcounts_upgr').val() ) {
				table = 'statcounts';
				jQuery('#podpress_upgr_header_table_name').text(table);
				podpress_initial_db_upgrade(table);
			}
			var rows_to_go = parseInt(jQuery('#podpress_upgr_'+table+'_to_process').text());
			if ( 'true' == jQuery('#podpress_is_stats_upgr').val() && (isNaN(rows_to_go) || 0 >= rows_to_go) ) {
				table = 'stats';
				jQuery('#podpress_upgr_header_table_name').text(table);
				jQuery('#podpress_upgr_part_counter_current').text('2');
				podpress_initial_db_upgrade(table);
			}
		},
		close: function( event, ui ) {
			// start the download counter again
			podpress_stats_status('start');
		},
		buttons: [ 
			{
				id: 'podpressbutton',
				text: podPress_jQuery('input[name="podPress_upgrade_stats_table_Submit"]').val(),
				click: function() {
					var rows_to_go =  parseInt(jQuery('#podpress_upgr_'+table+'_to_process').text());
					if ( 0 < rows_to_go ) {
						podpress_upgr_stats(table);
					} else {
						if ( 'statcounts' == table && 'true' == jQuery('#podpress_is_stats_upgr').val() ) {
							table = 'stats';
							jQuery('#podpress_upgr_header_table_name').text(table);
							jQuery('#podpress_upgr_part_counter_current').text('2');
							podpress_initial_db_upgrade(table);
						} else {
							// hide Close button of the dialog
							podPress_jQuery(".ui-dialog-titlebar-close").hide();
							podPress_jQuery(this).dialog('close');
							location.reload();
						}
					}
				}
			}
		]
	});
});

function podpress_initial_db_upgrade(table) {
	if ('stats' == table || 'statcounts' == table) {
		var rows = podpress_current_nr_of_rows(table);
		if (rows > 0) {
			podpress_print_upgrade_form(table, 'true');
			jQuery('#podPress_upgrade_'+table+'_table').show();
			
			podpress_upgr_stats(table, 'true');
		}
	}
}

function podpress_stats_status(action) {
	if ( typeof action == 'undefined' ) { var action = 'stop'; }
	var result = 'failed';
	var upgr_process_nr = parseInt(jQuery('#podpress_upgr_process_nr').val());
	jQuery.ajax({
		async: false,
		url: podPressBackendURL + '/podpress_backend.php', 
		type: 'POST',
		dataType: 'text',
		data: '&action=upgradeaction&upgr_process_nr=' + encodeURIComponent(upgr_process_nr) + '&podpress_upgr_misc[startstopstats]=' + encodeURIComponent(action) + '&_ajax_nonce=' + encodeURIComponent(jQuery.trim(jQuery('#podpress_ajax_nonce_key').val())),
		success: function(data, textStatus, XMLHttpRequest) { 
			var podpress_result_obj = jQuery.parseJSON(XMLHttpRequest.responseText); 
			if ( '' != podpress_result_obj.code ) {
				result = 'success';
			}
		},
		complete: function (jqXHR, textStatus) {
			//~ alert('stats start stop complete' + '\n' + result);
		}
	});
}
function podpress_upgr_stats(table, isinit) {
	if ('stats' == table || 'statcounts' == table) {
		if ( typeof isinit == 'undefined' || ('false' != isinit && 'true' != isinit) ) { var isinit = 'false'; }
		if ( 'true' == isinit ) {
			var asyncmethod = false;
		} else {
			var asyncmethod = true;
		}
		podPress_jQuery('#podpressbutton').attr("disabled", true);
		jQuery('#podpress_upgr_'+table+'_increment').attr("disabled", true);
		jQuery('#podpress_upgr_ajax_loader_img').css('display', 'inline');
		var upgr_process_nr = parseInt(jQuery('#podpress_upgr_process_nr').val());
		var totalrows = parseInt(jQuery('#podpress_upgr_'+table+'_total_rows').text());
		jQuery.ajax({
			async: asyncmethod,
			url: podPressBackendURL + '/podpress_backend.php', 
			type: 'POST',
			dataType: 'text',
			data: '&action=upgradeaction&upgr_process_nr=' + encodeURIComponent(upgr_process_nr) + '&podpress_upgr_misc[tablename]=' + encodeURIComponent(table) + '&podpress_upgr_misc[totalrows]=' + encodeURIComponent(totalrows) + '&podpress_upgr_misc[increment]=' + encodeURIComponent(parseInt(jQuery('#podpress_upgr_'+table+'_increment').val())) + '&_ajax_nonce=' + encodeURIComponent(jQuery.trim(jQuery('#podpress_ajax_nonce_key').val())),
			success: function(data, textStatus, XMLHttpRequest){ 
				var podpress_result_obj = jQuery.parseJSON(XMLHttpRequest.responseText); 
				if ( '' != podpress_result_obj.code ) { 
					jQuery('#podpress_upgr_elapsed_time').text(podpress_result_obj.time);
					var max_execution_time = parseFloat(jQuery('#podpress_upgr_max_execution_time').text());
					var max_steps = Math.floor(max_execution_time / ((parseFloat(podpress_result_obj.time)+2.0) / parseFloat(podpress_result_obj.increment)));
					jQuery('#podpress_upgr_'+table+'_increment_limit').text(max_steps);
					var increment_options = jQuery('#podpress_upgr_'+table+'_increment').children();
					var increment_options_nr = increment_options.length;
					var total_rows = parseInt(jQuery('#podpress_upgr_'+table+'_total_rows').text());
					if (max_steps > total_rows) {
						max_steps = total_rows;
					}
					var last_enabled_option = 0;
					var selectedval = -1;
					for (i=0; i < increment_options_nr; i++) {
						if ( i==0 && increment_options[i].value > max_steps ) {
							var newoption = jQuery('<option></option>').attr('value', max_steps).text(max_steps);
							newoption.prependTo(jQuery('#podpress_upgr_'+table+'_increment:first'));
						} else if ( i > 0 && increment_options[(i-1)].value < max_steps && max_steps < increment_options[i].value ) {
							var newoption = jQuery('<option></option>').attr('value', max_steps).text(max_steps);
							newoption.insertAfter(jQuery(increment_options[(i-1)]));
						} else if ( i == (increment_options_nr-1) && increment_options[i].value < max_steps ) {
							jQuery('#podpress_upgr_'+table+'_increment').append(jQuery('<option></option>').attr('value', max_steps).text(max_steps));
						}
						if (increment_options[i].value > max_steps) {
							increment_options[i].disabled = true;
						} else {
							increment_options[i].disabled = false;
						}
						if ( increment_options[i].disabled == false ) {
							last_enabled_option = i;
						}
						if ( increment_options[i].selected == true ) {
							selectedval = i;
						}
					}
					jQuery('#podpress_upgr_used_increment').text(podpress_result_obj.increment);
					jQuery('#podpress_upgr_'+table+'_increment').attr("disabled", false);
					
					// select a enabled option (if none has been selected or if the previous value is now disabled)
					if ( selectedval > last_enabled_option || selectedval == -1 ) {
						increment_options[last_enabled_option].selected = true;
					}

					podpress_result_obj.increment = parseInt(jQuery('#podpress_upgr_'+table+'_increment').val());
					var rows_to_go = total_rows - podpress_result_obj.lastpos;
					if ( 0 > rows_to_go ) {
						var rows_to_go = 0;
					}
					jQuery('#podpress_upgr_'+table+'_lastpos').val(podpress_result_obj.lastpos);
					jQuery('#podpress_upgr_'+table+'_to_process').text(rows_to_go);
					if ( 0 < rows_to_go ) {
						if ( total_rows < (podpress_result_obj.lastpos+podpress_result_obj.increment) ) { 
							var newbuttontext = podPress_jQuery('input[name="podPress_upgrade_stats_table_Submit"]').val().replace(/\.{3}/, String(podpress_result_obj.lastpos) + ' - ' + String(total_rows));
						} else {
							var newbuttontext = podPress_jQuery('input[name="podPress_upgrade_stats_table_Submit"]').val().replace(/\.{3}/, String(podpress_result_obj.lastpos) + ' - ' + String((podpress_result_obj.lastpos+podpress_result_obj.increment)));
						}
					} else {
						var newbuttontext = podPress_jQuery('input[name="podPress_upgrade_stats_table_finished"]').val();
						podpress_finish_upgrade(table);
					}
					podPress_jQuery('#podpressbutton').text(newbuttontext);
					podPress_jQuery('#podpressbutton').attr("disabled", false);
				} else {
					Alert('error\n'+XMLHttpRequest.responseText);
				}
				jQuery('#podpress_upgr_ajax_loader_img').css('display', 'none');
			}
		});
	}
}

function podpress_update_button_text_on_increment_change(table, increment) {
	if ('stats' == table || 'statcounts' == table) {
		if ( typeof increment == 'undefined' ) { 
			var increment = 1; 
		} else {
			increment = parseInt(increment);
			if (increment < 1) { increment = 1; }
		}
		var lastpos = parseInt(jQuery('#podpress_upgr_'+table+'_lastpos').val());
		var total_rows = parseInt(jQuery('#podpress_upgr_'+table+'_total_rows').text());
		var rows_to_go =  parseInt(jQuery('#podpress_upgr_'+table+'_to_process').text());
		if ( 0 < rows_to_go ) {
			if ( total_rows < (lastpos+increment) ) { 
				var newbuttontext = podPress_jQuery('input[name="podPress_upgrade_stats_table_Submit"]').val().replace(/\.{3}/, String(lastpos) + ' - ' + String(total_rows));
			} else {
				var newbuttontext = podPress_jQuery('input[name="podPress_upgrade_stats_table_Submit"]').val().replace(/\.{3}/, String(lastpos) + ' - ' + String((lastpos+increment)));
			}
		} else {
			var newbuttontext = podPress_jQuery('input[name="podPress_upgrade_stats_table_finished"]').val();
		}
		podPress_jQuery('#podpressbutton').text(newbuttontext);
	}
}

function podpress_current_nr_of_rows(table, isinit) {
	var result = 'failed';
	if ('stats' == table || 'statcounts' == table) {
		if ( typeof isinit == 'undefined' || ('false' != isinit && 'true' != isinit) ) { var isinit = 'false'; }
		var upgr_process_nr = parseInt(jQuery('#podpress_upgr_process_nr').val());
		jQuery.ajax({
			async: false,
			url: podPressBackendURL + '/podpress_backend.php', 
			type: 'POST',
			dataType: 'text',
			data: '&action=curnrofrows&upgr_process_nr=' + encodeURIComponent(upgr_process_nr) + '&podpress_upgr_misc[table]=' + encodeURIComponent(table) + '&podpress_upgr_misc[isinit]=' + encodeURIComponent(isinit) + '&_ajax_nonce=' + encodeURIComponent(jQuery.trim(jQuery('#podpress_ajax_nonce_key').val())),
			success: function(data, textStatus, XMLHttpRequest) { 
				var podpress_result_obj = jQuery.parseJSON(XMLHttpRequest.responseText); 
				if ( '' != podpress_result_obj.code ) {
					result = 'success';
				}
				result = podpress_result_obj.rows;
			},
			complete: function (jqXHR, textStatus) {
				//~ alert('current nr of rows complete' + '\n' + result);
			}
		});
	}
	return Number(result);
}

function podpress_print_upgrade_form(table, isinit) {
	if ('stats' == table || 'statcounts' == table) {
		if ( typeof isinit == 'undefined' || ('false' != isinit && 'true' != isinit) ) { var isinit = 'false'; }
		var upgr_process_nr = parseInt(jQuery('#podpress_upgr_process_nr').val());
		jQuery.ajax({
			async: false,
			url: podPressBackendURL + '/podpress_backend.php', 
			type: 'POST',
			dataType: 'text',
			data: '&action=printupgradeform&upgr_process_nr=' + encodeURIComponent(upgr_process_nr) + '&podpress_upgr_misc[table]=' + encodeURIComponent(table) + '&podpress_upgr_misc[isinit]=' + encodeURIComponent(isinit) + '&_ajax_nonce=' + encodeURIComponent(jQuery.trim(jQuery('#podpress_ajax_nonce_key').val())),
			success: function(data, textStatus, XMLHttpRequest) { 
				jQuery('#podPress_upgrade_form').html(XMLHttpRequest.responseText);
			},
			complete: function (jqXHR, textStatus) {
				//~ alert('podpress_print_upgrade_form complete');
			}
		});
	}
}

function podpress_finish_upgrade(table) {
	if ('stats' == table || 'statcounts' == table) {
		var upgr_process_nr = parseInt(jQuery('#podpress_upgr_process_nr').val());
		jQuery.ajax({
			async: false,
			url: podPressBackendURL + '/podpress_backend.php', 
			type: 'POST',
			dataType: 'text',
			data: '&action=finishupgradeaction&upgr_process_nr=' + encodeURIComponent(upgr_process_nr) + '&podpress_upgr_misc[table]=' + encodeURIComponent(table) + '&_ajax_nonce=' + encodeURIComponent(jQuery.trim(jQuery('#podpress_ajax_nonce_key').val())),
			success: function(data, textStatus, XMLHttpRequest) { 
				jQuery('#podPress_upgrade_form').html(XMLHttpRequest.responseText);
			},
			complete: function (jqXHR, textStatus) {
				//~ alert('podpress_print_upgrade_form complete');
			}
		});
	}
}