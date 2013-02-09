<?php

if(!is_admin()) {
    wp_enqueue_script("gforms_ui_datepicker", WP_PLUGIN_URL . "/gravityforms/js/jquery-ui/ui.datepicker.js", array("jquery"), "1.4", true);
    wp_enqueue_script("gforms_datepicker", WP_PLUGIN_URL . "/gravityforms/js/datepicker.js", array("gforms_ui_datepicker"), "1.4", true);
    wp_enqueue_script("gforms_conditional_logic_lib", WP_PLUGIN_URL . "/gravityforms/js/conditional_logic.js", array("gforms_ui_datepicker"), "1.4", true);
    wp_enqueue_style("gforms_css", WP_PLUGIN_URL . "/gravityforms/css/forms.css");
}

/* Register post types. */
add_action('init', 'dbc_admin_register_post_types');

/* Disables sidebars. */
add_filter( 'sidebars_widgets', 'dbc_child_disable_sidebars' );

/* Modifies hybrid_site_title. */
add_filter( 'hybrid_site_title', 'dbc_child_site_title', 12 );

/* Modifies hybrid_entry_title. */
add_filter( 'hybrid_entry_title_shortcode', 'dbc_admin_entry_title' );

/* populate the field with "user_firstname" as the population parameter with the "first_name" of the current user. */
add_filter('gform_field_value_user_firstname', create_function("", '$value = populate_usermeta(\'first_name\'); return $value;' ));

/* populate the field with "user_lastname" as the population parameter with the "last_name" of the current user. */
add_filter('gform_field_value_user_lastname', create_function("", '$value = populate_usermeta(\'last_name\'); return $value;' ));

/* populate the field with "user_email" as the population parameter with the "user_email" of the current user. */
add_filter('gform_field_value_user_email', create_function("", '$value = populate_usermeta(\'user_email\'); return $value;' ));

/**
* Disable sidebars on the home page
*
* @since 0.1
*/
function dbc_child_disable_sidebars( $sidebars_widgets ) {

	if ( hybrid_get_setting( 'info' ) == 'true' ) $sidebars_widgets['home'] = true;
		
	return $sidebars_widgets;
}

/**
* If an image path exists for the logo, use it instead of plain text
*
* @since 0.1
*/
function dbc_child_site_title() {
	$title = get_bloginfo('name');
	$url = get_bloginfo('url');
	$img_src = hybrid_get_setting( 'logo_src' );
	
	if ( !empty( $img_src ) )
		echo '<div id="site-title"><a href="'. $url .'" title="'. $title .'"><img src="'. hybrid_get_setting( 'logo_src' ) .'" alt="'. $title .'" /></div></a>';
	else
		echo '<div id="site-title"><a href="'. $url .'" title="'. $title .'" class="test">'. $title . '</div></a>';		
}

/**
* This function is called by both filters and returns the requested user meta of the current user
*
* @since 0.1
*/
function populate_usermeta($meta_key){
    global $current_user;
    get_currentuserinfo();

    foreach($current_user as $key => $value){
        if($key == $meta_key)
            return $value;
    }

    return '';
}

/**
* Register post types
*
* @since 0.1
*/
function dbc_admin_register_post_types() {
	
	$labels = array(
		'name' => _x('Documentation', 'Documentation'),
		'singular_name' => _x('Documentation', 'Documentation'),
		'add_new' => _x('Add New', 'documentation'),
		'add_new_item' => __('Add New Documentation'),
		'edit_item' => __('Edit Documentation'),
		'new_item' => __('New Documentation'),
		'view_item' => __('View Documentation'),
		'search_items' => __('Search Documentation'),
		'not_found' =>  __('No documentation found'),
		'not_found_in_trash' => __('No documentation found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => 'Docs'
	);
	
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => 4,
		'supports' => array('title','editor','author','thumbnail','excerpt','comments')
	); 
	
	register_post_type('documentation',$args);
}

/**
* Modifies hybrid_entry_title
*
* @since 0.1
*/
function dbc_admin_entry_title() {
	global $post;

	//if ( is_page_template('page-template-home.php') && ( 'documentation' == get_post_type() )  )
		$title = the_title( 'test<h1 class="' . esc_attr( $post->post_type ) . '-title entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h1>', false );


	return $title;

}

// Filter only applied to Form id = 8
add_filter('gform_pre_render_8', 'prepopulate_firstcup_dates');
/**
 * Pre-populates radios based on previous name field
 * Searches post type 'person'
 *
 * @param array @form Current Form Object
 * @return array @form Modified Form Object
 */
function prepopulate_firstcup_dates($form){
	$dates = array();
	$values = array();
	$date = new DateTime('next sunday');
	$now = new DateTime(current_time('mysql'));
	$cutoff = clone $date;
	$cutoff->sub(new DateInterval('P5DT11H'));
	
	if($now>=$cutoff){
		$date->add(new DateInterval('P7D'));
		$dates[] = array('text' => $date->format('F jS, Y'), 'value' => $date->format('Ymd'));
		$inputs[] = array('label' => $date->format('F jS, Y'), 'id' => '23.1');
	}else{
		$dates[] = array('text' => $date->format('F jS, Y'), 'value' => $date->format('Ymd'));
		$inputs[] = array('label' => $date->format('F jS, Y'), 'id' => '23.1');
	}
	
	for($i=0; $i<21; $i++) {
		$date->add(new DateInterval('P7D'));
		$dates[] = array('text' => $date->format('F jS, Y'), 'value' => $date->format('Ymd'));
		$inputs[] = array('label' => $date->format('F jS, Y'), 'id' => '23.'.$i+2);
	}

	foreach($form['fields'] as &$field ) {
		if ($field['id'] == 23) {
			$field['choices'] = $dates;
            $field['inputs'] = $values;
		}
	}

	return $form;	
}
add_action('gform_post_submission_8', 'firstcup_entry', 10, 2);
function firstcup_entry($entry, $form){
	$link = mysql_connect('localhost', 'dbc_fcmanager', '&0P%!PFV;p1K');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	$db_selected = mysql_select_db('dbc_firstcup');
	if (!$db_selected) {
		die ('Can\'t use dbc_firstcup : ' . mysql_error());
	}
	for($i=1;$i<=22;$i++){
		if($entry["23.".$i]!= ""){
			$year = substr($entry["23.".$i],0,4);
			$month = substr($entry["23.".$i],4,2);
			$day = substr($entry["23.".$i],6,2);
			
			//Build Query
			$sql = "INSERT INTO `submissions` (`id`, `gf_id`, `name`, `email`, `phone`, `ministry`, `ministry_email`, `year`, `month`, `day`, `title`, `subtitle`, `content`, `additional_info`, `file`, `submitted`) VALUES (NULL, '".$entry["id"]."', '".mysql_real_escape_string($entry["1.3"].' '.$entry["1.6"])."', '".mysql_real_escape_string($entry["2"])."', '".mysql_real_escape_string($entry["3"])."', '".mysql_real_escape_string($entry["5"])."', '".$entry["6"]."', '".$year."', '".$month."', '".$day."', '".mysql_real_escape_string($entry["10"])."', '".mysql_real_escape_string($entry["11"])."', '".mysql_real_escape_string($entry["12"])."', '".mysql_real_escape_string($entry["21"])."', '".mysql_real_escape_string($entry["22"])."', CURRENT_TIMESTAMP);";
			
			//Execute Query, log any errors
			if(!mysql_query($sql, $link)) { error_log("Invalid Query: ".mysql_error($link)); }
	
		}
	}
	mysql_close($link);
}

//Create custom 'Announcement Date' Merge Tag
add_filter('gform_custom_merge_tags', 'custom_merge_tags', 10, 4);
function custom_merge_tags($merge_tags, $form_id, $fields, $element_id) {
    
    // Add custom announcement dates tag
    if($form_id==8)
        $merge_tags[] = array('label' => 'Announcement Dates', 'tag' => '{announcement_dates}');
    
    return $merge_tags;
}

//Replace 'Announcement Dates' with better format
add_filter('gform_replace_merge_tags', 'replace_announcement_dates', 10, 7);
function replace_announcement_dates($text, $form, $entry, $url_encode, $esc_html, $nl2br, $format) {
	$custom_merge_tag = '{announcement_dates}';
	$replacement_text = '';
	
	//Make sure we are doing this in the right place
	if(strpos($text, $custom_merge_tag) === false)
		return $text;
		
	//Find selections and build output text
	for($i=1;$i<=22;$i++){
		if($entry["23.".$i]!= ""){
			$buffer = $entry["23.".$i];
			$date = new DateTime(substr($buffer,0,4).'-'.substr($buffer,4,2).'-'.substr($buffer,6,2));
			$replacement_text .= $date->format('F jS, Y').'<br>';
		}
	}
	//Replace text
	$text = str_replace($custom_merge_tag, $replacement_text, $text);
	
	return $text;
}

//Add attachments from form 8
add_filter("gform_user_notification_attachments_8", "add_attachment", 10, 3);
function add_attachment($attachments, $lead, $form){
    $fileupload_fields = GFCommon::get_fields_by_type($form, array("fileupload"));

    if(!is_array($fileupload_fields))
        return $attachments;

    $attachments = array();
    $upload_root = RGFormsModel::get_upload_root();
    foreach($fileupload_fields as $field){
        $url = $lead[$field["id"]];		
		$attachment = preg_replace('|^(.*?)/gravity_forms/|', $upload_root, $url);
		if($attachment){
			$attachments[] = $attachment;
		}            
	}

    return $attachments;
}
?>