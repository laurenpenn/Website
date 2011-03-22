<?php
if (FALSE !== strpos((__FILE__), 'wp-content')) {
	$script_name_parts = explode('wp-content', (__FILE__));
	if ( 1 < count($script_name_parts)) {
		include_once($script_name_parts[0].'wp-config.php');
		GLOBAL $blog_id, $wp_version;
		if ( TRUE == version_compare($wp_version, '2.8', '>=') ) {
			$podpress_xspf_widget_temp = get_option('podpress_xspf_widget_temp');
			$widget_id = intval(end(explode('-', $podpress_xspf_widget_temp)));
			$xspf_widgets_options = get_option('widget_podpress_xspfplayer');
			if ( is_array($xspf_widgets_options) ) {
				$options = $xspf_widgets_options[$widget_id];
			}
		} else {
			$options = get_option('widget_podPressXspfPlayer');
		}
		if ( FALSE == isset($options['PlayerWidth']) OR 150 > intval($options['PlayerWidth']) ) {
			$options['PlayerWidth'] = 150; // min width
		}
		header('Content-type: text/xml');
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Wed, 28 Oct 2010 05:00:00 GMT'); // Date in the past
		if ( TRUE == isset($options['useSlimPlayer']) AND TRUE === $options['useSlimPlayer'] ) {
			if ( FALSE == isset($options['SlimPlayerHeight']) OR 30 > intval($options['SlimPlayerHeight']) ) {
				$options['SlimPlayerHeight'] = 30; // min height slim
			}
			echo podpress_xspf_jukebox_slim_skin_xml($options['PlayerWidth'], $options['SlimPlayerHeight'], $blog_id);
		} else {
			if ( FALSE == isset($options['PlayerHeight']) OR 100 > intval($options['PlayerHeight']) ) {
				$options['PlayerHeight'] = 100; // min height
			}
			echo podpress_xspf_jukebox_skin_xml($options['PlayerWidth'], $options['PlayerHeight'], $blog_id);
		}
		exit;
	} else {
		unset($script_name_parts);
		die('Error: unable to load the playlist');
	}
	unset($script_name_parts);
} else {
	die('podPress seems not to be in the wp-content folder.');
}

/**
* podpress_xspf_jukebox_dynskin_xml - generates the content of a skin file of the XSPF player with the new width and height value
*
* @package podPress
* @since 8.8.5
*
* @param int $width
* @param int $height
* @param int $blog_id 
*
*/
function podpress_xspf_jukebox_skin_xml($width = 170, $height = 210, $blog_id=1) {
	GLOBAL $wp_version;
	if (1000 < $width) {
		$width = 600;
	} elseif (150 > $width) {
		$width = 150;
	}
	if (1000 < $height) {
		$height = 1000;
	} elseif (100 > $height) {
		$height = 100;
	}
	$top_row_h = 18;
	$bottom_row_w = $width;
	$bottom_row_h = 19;
	$scrollbar_w = 10;
	$middle_row_h = ($height - ($top_row_h+$bottom_row_h));
	$volume_display_w=14;
	$td_lb_tb_x = 59;
	$timedisplay_w = 26;
	$space_w = 3;
	$space_h = 3;
	$timebar_h = 13;
	$loadBar_h = 3;

	$player_buttons_h = $top_row_h-$space_h-1-$space_h-1;
		
	// colors
	$bgcolor = 'CCCCCC';
	$rowsandbars_bgcolor = 'EAEAEA';
	$buttons_color = '999999';
	$playlist_text_color = $button_text_color = '333333';
	$playlist_selectedtext_color = 'aa3333';
	$infodisplay_text_color = '000000';
	
	// misc.
	if ( TRUE === defined( 'PODPRESS_XSPF_SHOW_PREVIEW_IMAGE' ) AND TRUE === PODPRESS_XSPF_SHOW_PREVIEW_IMAGE ) { 
		$show_episode_image = TRUE;
	} else {
		$show_episode_image = FALSE;
	}
	
	$charset = get_bloginfo('charset');
	
	$output = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
	$output .= '<skin version="0" xmlns="http://xsml.org/ns/0/">'."\n";
	$output .= '	<width>'.$width.'</width>'."\n";
	$output .= '	<height>'.$height.'</height>'."\n";
	$output .= '	<name>SlimOriginal</name>'."\n";
	$output .= '	<author>Lacy Morrow</author>'."\n";
	$output .= '	<email>gojukebox@gmail.com</email>'."\n";
	$output .= '	<website>http://www.lacymorrow.com</website>'."\n";
	$output .= '	<comment>Blog ID: '.$blog_id.' | DYNAMIC SlimOriginal Skin for XSPF Jukebox (This is a derivate of the SlimOriginal skin.)</comment>'."\n";
	$output .= '	<objects>'."\n";
	$output .= '		<background color="'.$bgcolor.'" />'."\n";
	
	// playlist
	$output .= '		<playlist x="'.$space_w.'" y="'.($top_row_h+$space_h).'" width="'.($width-$space_w-$scrollbar_w-$space_w).'" height="'.$middle_row_h.'" size="10" font="Arial" color="'.$playlist_text_color.'" selectedColor="'.$playlist_selectedtext_color.'" />'."\n";
	// top row background
	$output .= '		<shape shape="rectangle" x="0" y="0" width="'.$width.'" height="'.$top_row_h.'" color="'.$rowsandbars_bgcolor.'" />'."\n";
	// scroll bar background
	$output .= '		<shape shape="rectangle" x="'.($width-$scrollbar_w).'" y="'.$top_row_h.'" width="'.$scrollbar_w.'" height="'.$middle_row_h.'" color="'.$rowsandbars_bgcolor.'" />'."\n";
	// bottom row background
	$output .= '		<shape shape="rectangle" x="0" y="'.($height-($bottom_row_h)).'" width="'.$bottom_row_w.'" height="'.$bottom_row_h.'" color="'.$rowsandbars_bgcolor.'" />'."\n";
	// "About" - button element	
	if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) {
	$output .= '		<text x="'.($width-33).'" y="'.($height-$bottom_row_h).'" size="10" text="'.html_entity_decode(__('About', 'podpress')).'" font="Arial" bold="0" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('About XSPF Jukebox player', 'podpress')).'" url="http://blog.lacymorrow.com" />'."\n";
	} else {
	$output .= '		<text x="'.($width-33).'" y="'.($height-$bottom_row_h).'" size="10" text="'.html_entity_decode(__('About', 'podpress'), ENT_COMPAT, $charset).'" font="Arial" bold="0" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('About XSPF Jukebox player', 'podpress'), ENT_COMPAT, $charset).'" url="http://blog.lacymorrow.com" />'."\n";
	}
	// player as Popup player - button element
	//~ if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) {
	//~ $output .= '		<text x="'.($width-85).'" y="'.($height-$bottom_row_h+2).'" size="10" text="'.html_entity_decode(__('Popup', 'podpress')).'" font="Arial" bold="0" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Popup', 'podpress')).'" url="../static/object-flash-xspf-popup.php" />'."\n";
	//~ } else {
	//~ $output .= '		<text x="'.($width-85).'" y="'.($height-$bottom_row_h+2).'" size="10" text="'.html_entity_decode(__('Popup', 'podpress'), ENT_COMPAT, $charset).'" font="Arial" bold="0" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Popup', 'podpress'), ENT_COMPAT, $charset).'" url="../static/object-flash-xspf-popup.php" />'."\n";
	//~ }
	$output .= '		<object label="prevButton" x="2" y="'.$space_h.'" width="11" height="'.$player_buttons_h.'" color="'.$buttons_color.'" />'."\n";
	$output .= '		<object label="playButton" x="19" y="'.$space_h.'" width="10" height="'.$player_buttons_h.'" color="'.$buttons_color.'" />'."\n";
	$output .= '		<object label="startButton" x="19" y="'.$space_h.'" width="10" height="'.$player_buttons_h.'" alpha="0" />'."\n";
	$output .= '		<object label="stopButton" x="32" y="'.$space_h.'" width="9" height="'.$player_buttons_h.'" color="'.$buttons_color.'" />'."\n";
	$output .= '		<object label="fwdButton" x="46" y="'.$space_h.'" width="11" height="'.$player_buttons_h.'" color="'.$buttons_color.'" />'."\n";
	
	$output .= '		<object label="trackDisplay" x="'.$td_lb_tb_x.'" y="0" width="'.($width-$td_lb_tb_x-$volume_display_w-4-26).'" size="10" font="Arial" color="'.$infodisplay_text_color.'" align="left" />'."\n";
	$output .= '		<object label="timeBar" x="'.$td_lb_tb_x.'" y="1" width="'.($width-$td_lb_tb_x-$volume_display_w-4).'" height="'.$timebar_h.'" alpha="60" color="cc9999" />'."\n";
	$output .= '		<object label="loadBar" x="'.$td_lb_tb_x.'" y="'.(1+$timebar_h).'" width="'.($width-$td_lb_tb_x-$volume_display_w-4).'" height="'.$loadBar_h.'" alpha="60" color="BBdddd" />'."\n";
	$output .= '		<object label="timeDisplay" x="'.($width-$volume_display_w-3-26).'" y="0" width="26" size="10" font="Arial" color="'.$infodisplay_text_color.'" />'."\n";
	$output .= '		<object label="volumeDisplay" x="'.($width-$volume_display_w-2).'" y="'.$space_h.'" width="'.$volume_display_w.'" height="'.$player_buttons_h.'" color="444444" />'."\n";
	
	if (TRUE == $show_episode_image) {
		$output .= '		<object label="imageDisplay" x="20" y="'.($height-$bottom_row_h-110).'" width="130" height="100" />'."\n";
	}
	//~ $output .= '		<object label="videoDisplay" x="20" y="20" width="130" height="100" />'."\n";
	
	$output .= '		<object label="scrollupButton" x="'.($width-6-$space_w).'" y="'.($top_row_h+2).'" width="6" height="6" color="'.$buttons_color.'" />'."\n";
	$output .= '		<object label="scrolldownButton" x="'.($width-6-$space_w).'" y="'.($top_row_h+13).'" width="6" height="6" color="'.$buttons_color.'" />'."\n";
	$output .= '		<object label="scrollButton" x="'.($width-6-$space_w).'" y="'.($top_row_h+25).'" width="6" height="'.($middle_row_h-25).'" color="'.$buttons_color.'" bgAlpha="0" />'."\n";
	
	if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) {
	$output .= '		<object label="shuffleButton" x="4" y="'.($height-$bottom_row_h+$space_h).'" width="20.7" height="11.7" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Shuffle', 'podpress')).'" />'."\n";
	$output .= '		<object label="repeatButton" x="27" y="'.($height-$bottom_row_h+$space_h).'" width="15.7" height="11.7" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Repeat', 'podpress')).'" />'."\n";
	} else {
	$output .= '		<object label="shuffleButton" x="4" y="'.($height-$bottom_row_h+$space_h).'" width="20.7" height="11.7" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Shuffle', 'podpress'), ENT_COMPAT, $charset).'" />'."\n";
	$output .= '		<object label="repeatButton" x="27" y="'.($height-$bottom_row_h+$space_h).'" width="15.7" height="11.7" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Repeat', 'podpress'), ENT_COMPAT, $charset).'" />'."\n";
	}
	
	//~ if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) {
	//~ $output .= '		<object label="infoButton" x="79" y="'.($height-$bottom_row_h-20).'" size="+10" color="'.$button_text_color.'" text="'.html_entity_decode(__('Info', 'podpress')).'" font="Arial" hoverMessage="'.html_entity_decode(__('Track Info', 'podpress')).'" />'."\n";
	//~ $output .= '		<object label="purchaseButton" x="52" y="'.($height-($bottom_row_h)).'" size="+10" color="'.$button_text_color.'" text="'.html_entity_decode(__('purchase', 'podpress')).'" font="Arial" hoverMessage="'.html_entity_decode(__('Purchase', 'podpress')).'" />'."\n";
	//~ $output .= '		<object label="downloadButton" x="101" y="'.($height-($bottom_row_h)).'" size="+10" color="'.$button_text_color.'" text="'.html_entity_decode(__('Save', 'podpress')).'" font="Arial" bold="0" hoverMessage="'.html_entity_decode(__('Download Track', 'podpress')).'" />'."\n";
	//~ } else {
	//~ $output .= '		<object label="infoButton" x="79" y="'.($height-$bottom_row_h-20).'" size="+10" color="'.$button_text_color.'" text="'.html_entity_decode(__('Info', 'podpress'), ENT_COMPAT, $charset).'" font="Arial" hoverMessage="'.html_entity_decode(__('Track Info', 'podpress'), ENT_COMPAT, $charset).'" />'."\n";
	//~ $output .= '		<object label="purchaseButton" x="52" y="'.($height-($bottom_row_h)).'" size="+10" color="'.$button_text_color.'" text="'.html_entity_decode(__('purchase', 'podpress'), ENT_COMPAT, $charset).'" font="Arial" hoverMessage="'.html_entity_decode(__('Purchase', 'podpress'), ENT_COMPAT, $charset).'" />'."\n";
	//~ $output .= '		<object label="downloadButton" x="101" y="'.($height-($bottom_row_h)).'" size="+10" color="'.$button_text_color.'" text="'.html_entity_decode(__('Save', 'podpress'), ENT_COMPAT, $charset).'" font="Arial" bold="0" hoverMessage="'.html_entity_decode(__('Download Track', 'podpress'), ENT_COMPAT, $charset).'" />'."\n";
	//~ }
	
	$output .= '	</objects>'."\n";
	$output .= '</skin>'."\n";
	return $output;
}


/**
* podpress_xspf_jukebox_slim_skin_xml - generates the content of a skin file of the slim XSPF player with the new width and height value
*
* @package podPress
* @since 8.8.5
*
* @param int $width
* @param int $height
* @param int $blog_id 
*
*/
function podpress_xspf_jukebox_slim_skin_xml($width = 170, $height = 30, $blog_id=1) {
	GLOBAL $wp_version;
	if (1000 < $width) {
		$width = 1000;
	} elseif (150 > $width) {
		$width = 150;
	}
	if (100 < $height) {
		$height = 100;
	} elseif (30 > $height) {
		$height = 30;
	}
	$top_row_h = 18;
	$bottom_row_w = $width;
	$bottom_row_h = 12;
	$middle_row_h = ($height - ($top_row_h+$bottom_row_h));
	$volume_display_w=14;
	$td_lb_tb_x = 59;
	$timedisplay_w = 26;
	$space_w = 3;
	$space_h = 3;
	$timebar_h = 13;
	$loadBar_h = 3;

	$player_buttons_h = $top_row_h-$space_h-1-$space_h-1;
		
	// colors
	$bgcolor = 'CCCCCC';
	$rowsandbars_bgcolor = 'EAEAEA';
	$buttons_color = '999999';
	$playlist_text_color = $button_text_color = '333333';
	$playlist_selectedtext_color = 'aa3333';
	$infodisplay_text_color = '000000';
		
	// misc.
	$show_episode_image = TRUE;
		
	$charset = get_bloginfo('charset');
		
	$output = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
	$output .= '<skin version="0" xmlns="http://xsml.org/ns/0/">'."\n";
	$output .= '	<width>'.$width.'</width>'."\n";
	$output .= '	<height>'.$height.'</height>'."\n";
	$output .= '	<name>SlimOriginal</name>'."\n";
	$output .= '	<author>Lacy Morrow</author>'."\n";
	$output .= '	<email>gojukebox@gmail.com</email>'."\n";
	$output .= '	<website>http://www.lacymorrow.com</website>'."\n";
	$output .= '	<comment>Blog ID: '.$blog_id.' | DYNAMIC SlimOriginal Skin for XSPF Jukebox (This is a derivate of the SlimOriginal skin for the slim player.)</comment>'."\n";
	$output .= '	<objects>'."\n";
	$output .= '		<background color="'.$bgcolor.'" />'."\n";
		
	// top row background
	$output .= '		<shape shape="rectangle" x="0" y="0" width="'.$width.'" height="'.$top_row_h.'" color="'.$rowsandbars_bgcolor.'" />'."\n";
	// bottom row background
	$output .= '		<shape shape="rectangle" x="0" y="'.($height-($bottom_row_h)).'" width="'.$bottom_row_w.'" height="'.$bottom_row_h.'" color="'.$rowsandbars_bgcolor.'" />'."\n";
	// "About" - button element
	if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) {
	$output .= '		<text x="'.($width-33).'" y="'.($height-$bottom_row_h-$space_h).'" size="10" text="'.html_entity_decode(__('About', 'podpress')).'" font="Arial" bold="0" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('About XSPF Jukebox player', 'podpress')).'" url="http://blog.lacymorrow.com" />'."\n";
	} else {
	$output .= '		<text x="'.($width-33).'" y="'.($height-$bottom_row_h-$space_h).'" size="10" text="'.html_entity_decode(__('About', 'podpress'), ENT_COMPAT, $charset).'" font="Arial" bold="0" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('About XSPF Jukebox player', 'podpress'), ENT_COMPAT, $charset).'" url="http://blog.lacymorrow.com" />'."\n";
	}
	// player as Popup player - button element
	//~ if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) {
	//~ $output .= '		<text x="'.($width-85).'" y="'.($height-$bottom_row_h+2).'" size="10" text="'.html_entity_decode(__('Popup', 'podpress')).'" font="Arial" bold="0" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Popup', 'podpress')).'" url="../static/object-flash-xspf-popup.php" />'."\n";
	//~ } else {
	//~ $output .= '		<text x="'.($width-85).'" y="'.($height-$bottom_row_h+2).'" size="10" text="'.html_entity_decode(__('Popup', 'podpress'), ENT_COMPAT, $charset).'" font="Arial" bold="0" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Popup', 'podpress'), ENT_COMPAT, $charset).'" url="../static/object-flash-xspf-popup.php" />'."\n";
	//~ }

	$output .= '		<object label="prevButton" x="2" y="'.$space_h.'" width="11" height="'.$player_buttons_h.'" color="'.$buttons_color.'" />'."\n";
	$output .= '		<object label="playButton" x="19" y="'.$space_h.'" width="10" height="'.$player_buttons_h.'" color="'.$buttons_color.'" />'."\n";
	$output .= '		<object label="startButton" x="19" y="'.$space_h.'" width="10" height="'.$player_buttons_h.'" alpha="0" />'."\n";
	$output .= '		<object label="stopButton" x="32" y="'.$space_h.'" width="9" height="'.$player_buttons_h.'" color="'.$buttons_color.'" />'."\n";
	$output .= '		<object label="fwdButton" x="46" y="'.$space_h.'" width="11" height="'.$player_buttons_h.'" color="'.$buttons_color.'" />'."\n";
		
	$output .= '		<object label="trackDisplay" x="'.$td_lb_tb_x.'" y="0" width="'.($width-$td_lb_tb_x-$volume_display_w-4-26).'" size="10" font="Arial" color="'.$infodisplay_text_color.'" align="left" />'."\n";
	$output .= '		<object label="timeBar" x="'.$td_lb_tb_x.'" y="1" width="'.($width-$td_lb_tb_x-$volume_display_w-4).'" height="'.$timebar_h.'" alpha="60" color="cc9999" />'."\n";
	$output .= '		<object label="loadBar" x="'.$td_lb_tb_x.'" y="'.(1+$timebar_h).'" width="'.($width-$td_lb_tb_x-$volume_display_w-4).'" height="'.$loadBar_h.'" alpha="60" color="BBdddd" />'."\n";
	$output .= '		<object label="timeDisplay" x="'.($width-$volume_display_w-3-26).'" y="0" width="26" size="10" font="Arial" color="'.$infodisplay_text_color.'" />'."\n";
	$output .= '		<object label="volumeDisplay" x="'.($width-$volume_display_w-2).'" y="'.$space_h.'" width="'.$volume_display_w.'" height="'.$player_buttons_h.'" color="444444" />'."\n";
		
	if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) {
	$output .= '		<object label="shuffleButton" x="4" y="'.($height-$bottom_row_h).'" width="17.1" height="10" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Shuffle', 'podpress')).'" />'."\n";
	$output .= '		<object label="repeatButton" x="27" y="'.($height-$bottom_row_h).'" width="12.1" height="10" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Repeat', 'podpress')).'" />'."\n";
	//~ $output .= '		<object label="infoButton" x="79" y="'.($height-$bottom_row_h+2).'" size="+10" color="'.$button_text_color.'" text="'.html_entity_decode(__('Info', 'podpress'), ENT_COMPAT, $charset).'" font="Arial" hoverMessage="'.html_entity_decode(__('Track Info', 'podpress')).'" />'."\n";
	} else {
	$output .= '		<object label="shuffleButton" x="4" y="'.($height-$bottom_row_h).'" width="17.1" height="10" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Shuffle', 'podpress'), ENT_COMPAT, $charset).'" />'."\n";
	$output .= '		<object label="repeatButton" x="27" y="'.($height-$bottom_row_h).'" width="12.1" height="10" color="'.$button_text_color.'" hoverMessage="'.html_entity_decode(__('Repeat', 'podpress'), ENT_COMPAT, $charset).'" />'."\n";
	//~ $output .= '		<object label="infoButton" x="79" y="'.($height-$bottom_row_h+2).'" size="+10" color="'.$button_text_color.'" text="'.html_entity_decode(__('Info', 'podpress'), ENT_COMPAT, $charset).'" font="Arial" hoverMessage="'.html_entity_decode(__('Track Info', 'podpress'), ENT_COMPAT, $charset).'" />'."\n";
	}
	
	$output .= '	</objects>'."\n";
	$output .= '</skin>'."\n";
	return $output;
}
?>