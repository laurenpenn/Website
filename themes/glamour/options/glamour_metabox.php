<?php
	add_action('admin_menu', 'myplugin_add_custom_box');
	add_action('save_post', 'myplugin_save_postdata');

	function myplugin_add_custom_box() {

	  if( function_exists( 'add_meta_box' )) {
		add_meta_box( 'metabox', __( 'The Deluxe Post Section Title', 'myplugin_textdomain' ),  'myplugin_inner_custom_box', 'post', 'normal', 'high');
	  	add_meta_box( 'metabox', __( 'The Deluxe Section Title', 'myplugin_textdomain' ),  'myplugin_page_custom_box', 'page', 'normal', 'high');
	  } else {
		add_action('dbx_post_advanced', 'myplugin_old_custom_box' );
	    add_action('dbx_page_advanced', 'myplugin_old_page_custom_box' );
	 }
	  
	}
	

function myplugin_page_custom_box() {
	global $post;
	
	echo '<input type="hidden" name="myplugin_noncename" id="myplugin_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	echo '<label for="myplugin_new_field">' . __("Choose category to join:", 'myplugin_textdomain' ) . '</label>';
	$current_value =  get_post_meta($post->ID, "page_id", true);
	list_categories("page_id",$current_value);
}
	
function myplugin_inner_custom_box() {
	global $post;
	echo '<input type="hidden" name="myplugin_noncename" id="myplugin_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
?>
<style>
.one_preview img {
	width:100px;
	height:100px;
}
.one_preview {
	width:100px;
	height:100px;
	padding:5px;
	margin:10px;
	border:1px solid #ececec;
	display:table;
	background-color:#f9f9f9;
}
</style>
<table>

	<tr>
		<td>
			<span id="thumb_one_preview" class="one_preview"><img src="<?php echo get_post_meta($post->ID, "thumb_one", true); ?>"></span>
			<center><a href="media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true" id="thumb_one" class="set_input thickbox button" title='Add an Image' onclick="return false;">Upload Picture</a></center>
		</td>
		<td style="width:100%;">Thumbnail Image Link (URL): <br /><br /><input type="text" name="thumb_one" id="thumb_one" class="upload_input" tabindex="1" value="<?php echo get_post_meta($post->ID, "thumb_one", true); ?>" style="width:100%;"/></td>
	</tr>
	
	<tr>
		<td colspan="2"><br /><br /></td>
	</tr>
	
	
	<tr>
		<td>
			<span id="big_one_preview" class="one_preview"><img src="<?php echo get_post_meta($post->ID, "big_one", true); ?>"></span>
			<center><a href="media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true" id="big_one" class="set_input thickbox button" title='Add an Image' onclick="return false;">Upload Picture</a></center>
		</td>
		<td style="width:100%;">FullSize Image Link (URL): <br /><br /><input type="text" name="big_one" id="big_one" class="upload_input" tabindex="1" value="<?php echo get_post_meta($post->ID, "big_one", true); ?>" style="width:100%;"/></td>
	</tr>
		<tr>
		<td colspan="2"><br /><br /></td>
	</tr>

	<tr>
		<td colspan="2">
			Short Desription:<br /><br />
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			<textarea cols=10 row=10 name="short_desc" id="short_desc" style="width:99%"><?php echo get_post_meta($post->ID, "short_desc", true); ?></textarea>
		</td>
	</tr>
	
	<tr>
		<td colspan="2"><br /><br /></td>
	</tr>
		<tr>
		<td colspan="2">
			Video Embed <font size="1" color="#ccc"><i>(if you need to add video)</i>:<br /><br />
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			<textarea cols=10 row=10 name="video_one" id="video_one" style="width:99%"><?php echo get_post_meta($post->ID, "video_one", true); ?></textarea>
		</td>
	</tr>
	

	

</table>

<?php
}

function myplugin_old_page_custom_box() {
	echo '<div class="dbx-b-ox-wrapper">' . "\n";
	echo '<fieldset id="myplugin_fieldsetid" class="dbx-box">' . "\n";
	echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . __( 'My Post Section Title', 'myplugin_textdomain' ) . "</h3></div>";   
    echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';
	myplugin_page_custom_box();
	echo "</div></div></fieldset></div>\n";
}


function myplugin_old_custom_box() {
	echo '<div class="dbx-b-ox-wrapper">' . "\n";
	echo '<fieldset id="myplugin_fieldsetid" class="dbx-box">' . "\n";
	echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . __( 'My Post Section Title', 'myplugin_textdomain' ) . "</h3></div>";   
    echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';
	myplugin_inner_custom_box();
	echo "</div></div></fieldset></div>\n";
}

function myplugin_save_postdata( $post_id ) {

	if ( !wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename(__FILE__) )) {
    	return $post_id;
  	}

  	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    	return $post_id;
	
	if ( 'page' == $_POST['post_type'] ) {
    	if ( !current_user_can( 'edit_page', $post_id ) )
     		 return $post_id;
  	} else {
    	if ( !current_user_can( 'edit_post', $post_id ) )
      		return $post_id;
  	}
	
	if ( $parent_id = wp_is_post_revision($post_id) )
	{
		$post_id = $parent_id;
	}

	if (!get_post_meta($post_id, "thumb_one")) {
		add_post_meta($post_id, "thumb_one", $_POST["thumb_one"]);
  	}else{
  		update_post_meta($post_id, "thumb_one", $_POST["thumb_one"]);
  	}
	if ($_POST["thumb_one"] == "") {
		  delete_post_meta($post_id, "thumb_one");
	}

  	if (!get_post_meta($post_id, "big_one")) {
		add_post_meta($post_id, "big_one", $_POST["big_one"]);
  	}else{
  		update_post_meta($post_id, "big_one", $_POST["big_one"]);
  	}
	if ($_POST["big_one"] == "") {
		  delete_post_meta($post_id, "big_one");
	}
	
	if (!get_post_meta($post_id, "video_one")) {
		add_post_meta($post_id, "video_one", $_POST["video_one"]);
  	}else{
  		update_post_meta($post_id, "video_one", $_POST["video_one"]);
  	}
	if ($_POST["video_one"] == "") {
		  delete_post_meta($post_id, "video_one");
	}

  	if (!get_post_meta($post_id, "short_desc")) {
		add_post_meta($post_id, "short_desc", $_POST["short_desc"]);
  	}else{
  		update_post_meta($post_id, "short_desc", $_POST["short_desc"]);
  	}
	if ($_POST["short_desc"] == "") {
		  delete_post_meta($post_id, "short_desc");
	}

	if (!get_post_meta($post_id, "page_id")) {
		add_post_meta($post_id, "page_id", $_POST["page_id"]);
  	}else{
  		update_post_meta($post_id, "page_id", $_POST["page_id"]);
  	}
	if ($_POST["page_id"] == "") {
		  delete_post_meta($post_id, "page_id");
	}
}
?>