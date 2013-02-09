<?php 
/*
 *  Plugin Name: DBC Admin
 *  Description: Custom Admin site functions
 *  Version: 1.0
 *  Author: Patrick Daly
 *  Author URI: http://developdaly.com/
 *  
 */
 
add_filter("gform_form_tag_9", "form_tag", 10, 2);
function form_tag($form_tag, $form) {
	
	if ( $entry['8'] != 'Technology Request' )
		return $form_tag;
	
	$form_tag = preg_replace("|action='(.*?)'|", "action='http://support.dentonbible.org/open.php'", $form_tag);
	return $form_tag;
}
