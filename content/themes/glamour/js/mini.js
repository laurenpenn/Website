jQuery.noConflict();

jQuery(document).ready(function()
{
	jQuery('.upload_input').each(function()
	{
		jQuery(this).bind('change focus blur canyon', function()
		{	
			$get_div = '#' + jQuery(this).attr('name') + '_preview';
			$src = '<img src ="'+jQuery(this).val()+'" />';
			jQuery($get_div).html('').append($src).find('img');
		});
	});
	
	$set = jQuery('.set_input');
	
	window.get_id = false;
	
	$set.click(function()
	{
		window.get_id = jQuery(this).attr('id');			
	});
	
	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html)
	{	
		if (get_id) 
		{
			$img = jQuery(html).attr('src') || jQuery(html).find('img').attr('src') || jQuery(html).attr('href');
			jQuery('input[name='+get_id+']').val($img).trigger('canyon');
			get_id = false;
			window.tb_remove();
		}else{
			window.original_send_to_editor(html);
		}
	};
});
