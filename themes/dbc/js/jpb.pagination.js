jQuery(document).ready(function($) {
	
	$('#pagination a').click(function(e){
		e.preventDefault();
		var data = {
			action : 'jpb_ajax_pagination'
		};
		
		data.paged = /\/page\/\d+\/?$/i.test($(this).attr('href')) ? parseInt( $(this).attr('href').replace( /\/page\/(\d+)\/?$/i, '$1' )) : 1;
		
		$.post( JPB.ajaxurl, data, function( text ){
			$('#first-cup').empty().append( text );
		});
	});
});
