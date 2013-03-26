jQuery(document).ready(function() {
	var fileInput = '';

	jQuery('.upload_image_button').click(function() {
		fileInput = jQuery(this).prev('input');
		formfield = jQuery('#upload_image').attr('name');
		post_id = jQuery('#post_ID').val();
		tb_show('', 'media-upload.php?post_id='+post_id+'&type=image&TB_iframe=true');
		return false;
	});

	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html){

		if (fileInput) {
			fileurl = jQuery('img',html).attr('src');

			fileInput.val(fileurl);

			tb_remove();

		} else {
			window.original_send_to_editor(html);
		}
	};

});