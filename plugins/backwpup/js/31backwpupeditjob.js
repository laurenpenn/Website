jQuery(document).ready( function($) {

	$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	
	$('.jobtype-select').change(function() {
		if ( true == $('#jobtype-select-FILE').attr('checked') || true ==  $('#jobtype-select-DB').attr('checked') || true == $('#jobtype-select-WPEXP').attr('checked')) {
			$('#backwpup_jobedit_destfolder').show();
			$('#backwpup_jobedit_destftp').show();
			$('#backwpup_jobedit_dests3').show();
			$('#backwpup_jobedit_destgstorage').show();
			$('#backwpup_jobedit_destazure').show();
			$('#backwpup_jobedit_destrsc').show();
			$('#backwpup_jobedit_destdropbox').show();
			$('#backwpup_jobedit_destsugarsync').show();
			$('#backwpup_jobedit_destfile').show();
			$('#backwpup_jobedit_destmail').show();
		} else {
			$('#backwpup_jobedit_destfolder').hide();
			$('#backwpup_jobedit_destftp').hide();
			$('#backwpup_jobedit_dests3').hide();
			$('#backwpup_jobedit_destgstorage').hide();
			$('#backwpup_jobedit_destazure').hide();
			$('#backwpup_jobedit_destrsc').hide();
			$('#backwpup_jobedit_destdropbox').hide();
			$('#backwpup_jobedit_destsugarsync').hide();
			$('#backwpup_jobedit_destfile').hide();
			$('#backwpup_jobedit_destmail').hide();
		}
		if ( true == $('#jobtype-select-DB').attr('checked') || true == $('#jobtype-select-CHECK').attr('checked') || true == $('#jobtype-select-OPTIMIZE').attr('checked')) {
			$('#databasejobs').show();
		} else {
			$('#databasejobs').hide();
		}
		if ( true == $('#jobtype-select-DB').attr('checked')) {
			$('#dbshortinsert').show();
		} else {
			$('#dbshortinsert').hide();
		}
		if ( true == $('#jobtype-select-FILE').attr('checked')) {
			$('#filebackup').show();
		} else {
			$('#filebackup').hide();
		}
	});
	
	$('input[name="cronselect"]').change(function() {
		if ( 'basic' == $('input[name="cronselect"]:checked').val()) {
			$('#schedadvanced').hide();
			$('#schedbasic').show();
			cronstampbasic();
		} else {
			$('#schedadvanced').show();
			$('#schedbasic').hide();
			cronstampadvanced();
		}
	});
	
	$('input[name="fileprefix"]').keyup(function() {
		$('#backupfileprefix').replaceWith('<span id="backupfileprefix">'+$(this).val()+'</span>');
	});
	
	$('input[name="fileformart"]').change(function() {
		$('#backupfileformart').replaceWith('<span id="backupfileformart">'+$(this).val()+'</span>');
	});
	
	function cronstampadvanced() {
		var cronminutes = [];
		var cronhours = [];
		var cronmday = [];
		var cronmon = [];
		var cronwday = [];
		$('input[name="cronminutes[]"]:checked').each(function() {
			cronminutes.push($(this).val());
		});
		$('input[name="cronhours[]"]:checked').each(function() {
			cronhours.push($(this).val());
		});
		$('input[name="cronmday[]"]:checked').each(function() {
			cronmday.push($(this).val());
		});
		$('input[name="cronmon[]"]:checked').each(function() {
			cronmon.push($(this).val());
		});
		$('input[name="cronwday[]"]:checked').each(function() {
			cronwday.push($(this).val());
		});		
		var data = {
			action: 'backwpup_get_cron_text',
			backwpupajaxpage: 'backwpupeditjob',
			cronminutes: cronminutes,
			cronhours: cronhours,
			cronmday: cronmday,
			cronmon: cronmon,
			cronwday: cronwday,
			_ajax_nonce: jQuery('#backwpupeditjobajaxnonce').val()
		};
		$.post(ajaxurl, data, function(response) {
			$('#cron-text').replaceWith(response);
		});		
	}
	$('input[name="cronminutes[]"]').change(function() {cronstampadvanced();});
	$('input[name="cronhours[]"]').change(function() {cronstampadvanced();});
	$('input[name="cronmday[]"]').change(function() {cronstampadvanced();});
	$('input[name="cronmon[]"]').change(function() {cronstampadvanced();});
	$('input[name="cronwday[]"]').change(function() {cronstampadvanced();});

	function cronstampbasic() {
		var cronminutes = [];
		var cronhours = [];
		var cronmday = [];
		var cronmon = [];
		var cronwday = [];
		if ( 'mon' == $('input[name="cronbtype"]:checked').val()) {
			cronminutes.push($('select[name="moncronminutes"]').val());
			cronhours.push($('select[name="moncronhours"]').val());
			cronmday.push($('select[name="moncronmday"]').val());
			cronmon.push('*');
			cronwday.push('*');		
		}
		if ( 'week' == $('input[name="cronbtype"]:checked').val()) {
			cronminutes.push($('select[name="weekcronminutes"]').val());
			cronhours.push($('select[name="weekcronhours"]').val());
			cronmday.push('*');
			cronmon.push('*');
			cronwday.push($('select[name="weekcronwday"]').val());	
		}
		if ( 'day' == $('input[name="cronbtype"]:checked').val()) {
			cronminutes.push($('select[name="daycronminutes"]').val());
			cronhours.push($('select[name="daycronhours"]').val());
			cronmday.push('*');
			cronmon.push('*');
			cronwday.push('*');	
		}
		if ( 'hour' == $('input[name="cronbtype"]:checked').val()) {
			cronminutes.push($('select[name="hourcronminutes"]').val());
			cronhours.push('*');
			cronmday.push('*');
			cronmon.push('*');
			cronwday.push('*');
		}	
		var data = {
			action: 'backwpup_get_cron_text',
			backwpupajaxpage: 'backwpupeditjob',
			cronminutes: cronminutes,
			cronhours: cronhours,
			cronmday: cronmday,
			cronmon: cronmon,
			cronwday: cronwday,
			_ajax_nonce: jQuery('#backwpupeditjobajaxnonce').val()
		};
		$.post(ajaxurl, data, function(response) {
			$('#cron-text').replaceWith(response);
		});		
	}
	$('input[name="cronbtype"]').change(function() {cronstampbasic();});
	$('select[name="moncronmday"]').change(function() {cronstampbasic();});
	$('select[name="moncronhours"]').change(function() {cronstampbasic();});
	$('select[name="moncronminutes"]').change(function() {cronstampbasic();});
	$('select[name="weekcronwday"]').change(function() {cronstampbasic();});
	$('select[name="weekcronhours"]').change(function() {cronstampbasic();});
	$('select[name="weekcronminutes"]').change(function() {cronstampbasic();});
	$('select[name="daycronhours"]').change(function() {cronstampbasic();});
	$('select[name="daycronminutes"]').change(function() {cronstampbasic();});
	$('select[name="hourcronminutes"]').change(function() {cronstampbasic();});
	

	function awsgetbucket() {
		var data = {
			action: 'backwpup_get_aws_buckets',
			backwpupajaxpage: 'backwpupeditjob',
			awsAccessKey: jQuery('#awsAccessKey').val(),
			awsSecretKey: jQuery('#awsSecretKey').val(),
			awsselected: jQuery('#awsBucketselected').val(),
			_ajax_nonce: jQuery('#backwpupeditjobajaxnonce').val()
		};
		$.post(ajaxurl, data, function(response) {
			$('#awsBucket').remove();
			$('#awsBucketselected').after(response);
		});		
	}
	$('#awsAccessKey').change(function() {awsgetbucket();});
	$('#awsSecretKey').change(function() {awsgetbucket();});

	function gstoragegetbucket() {
		var data = {
			action: 'backwpup_get_gstorage_buckets',
			backwpupajaxpage: 'backwpupeditjob',
			GStorageAccessKey: jQuery('#GStorageAccessKey').val(),
			GStorageSecret: jQuery('#GStorageSecret').val(),
			GStorageselected: jQuery('#GStorageselected').val(),
			_ajax_nonce: jQuery('#backwpupeditjobajaxnonce').val()
		};
		$.post(ajaxurl, data, function(response) {
			$('#GStorageBucket').remove();
			$('#GStorageselected').after(response);
		});		
	}
	$('#GStorageAccessKey').change(function() {gstoragegetbucket();});
	$('#GStorageSecret').change(function() {gstoragegetbucket();});	
	
	function msazuregetcontainer() {
		var data = {
			action: 'backwpup_get_msazure_container',
			backwpupajaxpage: 'backwpupeditjob',
			msazureHost: jQuery('#msazureHost').val(),
			msazureAccName: jQuery('#msazureAccName').val(),
			msazureKey: jQuery('#msazureKey').val(),
			msazureselected: jQuery('#msazureContainerselected').val(),
			_ajax_nonce: jQuery('#backwpupeditjobajaxnonce').val()
		};
		$.post(ajaxurl, data, function(response) {
			$('#msazureContainer').remove();
			$('#msazureContainerselected').after(response);
		});		
	}
	$('#msazureHost').change(function() {msazuregetcontainer();});
	$('#msazureAccName').change(function() {msazuregetcontainer();});
	$('#msazureKey').change(function() {msazuregetcontainer();});
	
	function rscgetcontainer() {
		var data = {
			action: 'backwpup_get_rsc_container',
			backwpupajaxpage: 'backwpupeditjob',
			rscUsername: jQuery('#rscUsername').val(),
			rscAPIKey: jQuery('#rscAPIKey').val(),
			rscselected: jQuery('#rscContainerselected').val(),
			_ajax_nonce: jQuery('#backwpupeditjobajaxnonce').val()
		};
		$.post(ajaxurl, data, function(response) {
			$('#rscContainer').remove();
			$('#rscContainerselected').after(response);
		});		
	}
	$('#rscUsername').change(function() {rscgetcontainer();});
	$('#rscAPIKey').change(function() {rscgetcontainer();});

	function sugarsyncgetroot() {
		var data = {
			action: 'backwpup_get_sugarsync_root',
			backwpupajaxpage: 'backwpupeditjob',
			sugaruser: jQuery('#sugaruser').val(),
			sugarpass: jQuery('#sugarpass').val(),
			sugarrootselected: jQuery('#sugarrootselected').val(),
			_ajax_nonce: jQuery('#backwpupeditjobajaxnonce').val()
		};
		$.post(ajaxurl, data, function(response) {
			$('#sugarroot').remove();
			$('#sugarrootselected').after(response);
		});		
	}
	$('#sugaruser').change(function() {sugarsyncgetroot();});
	$('#sugarpass').change(function() {sugarsyncgetroot();});
	
	if ( $('#title').val() == '' )
		$('#title').siblings('#title-prompt-text').css('visibility', '');
	$('#title-prompt-text').click(function(){
		$(this).css('visibility', 'hidden').siblings('#title').focus();
	});
	$('#title').blur(function(){
		if (this.value == '')
			$(this).siblings('#title-prompt-text').css('visibility', '');
	}).focus(function(){
		$(this).siblings('#title-prompt-text').css('visibility', 'hidden');
	}).keydown(function(e){
		$(this).siblings('#title-prompt-text').css('visibility', 'hidden');
		$(this).unbind(e);
	});

});

