(function($) {
	$(function() {
		// Wire up the show/hide sites
		$('.plugin_count').click(function(){
			// Strip plugin_count_ to get id
			id = $(this).attr('id').substr(13);
			// Show/hide the appropriate ul
			$('ul#site_list_'+id).toggle();
			// Stop # jump
			return(false);
		});
	});
})(jQuery);