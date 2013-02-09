<?php
/*
Plugin Name: Flash Video Player
Version: 5.0.4
Plugin URI: http://www.mac-dev.net
Description: Simplifies the process of adding video to a WordPress blog. Powered by Jeroen Wijering's FLV Media Player and SWFObject by Geoff Stearns.
Author: Joshua Eldridge
Author URI: http://www.mac-dev.net

Flash Video Plugin for Wordpress Copyright 2010  Joshua Eldridge

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

1) Includes Jeroen Wijering's FLV Media Player (Creative Commons "BY-NC-SA" License) v5.1
   Website: http://www.jeroenwijering.com/?item=JW_FLV_Player
   License: http://creativecommons.org/licenses/by-nc-sa/2.0/
2) Includes Geoff Stearns' SWFObject Javascript Library (MIT License) v2.1
   Website: http://code.google.com/p/swfobject/
   License: http://www.opensource.org/licenses/mit-license.php
*/

// error_reporting(E_ALL);

// Global Options
$videoid = 0;
$site_url = get_option('siteurl');
$saved_options = get_option('FlashVideoPlayerPlugin_PlayerOptions');
$plugin_specific = $saved_options['Plugin-Specific'];

if(isset($saved_options['Plugin-Specific'])) {
	unset($saved_options['Plugin-Specific']);
}

// Widget Functions Start
function FlashVideoPlayerPlugin_widgetregister(){
    register_sidebar_widget('Flash Video Player 5', 'FlashVideoPlayerPlugin_widget');
    register_widget_control('Flash Video Player 5', 'FlashVideoPlayerPlugin_control');
 }
 	
function FlashVideoPlayerPlugin_widget($a) {
	$widget_options = get_option('FlashVideoPlayerPlugin_WidgetOptions');
    echo $a['before_widget'];
    echo $a['before_title'] . $widget_options['FlashVideoPlayerPlugin_WidgetOptions_title'] . $a['after_title'];
    echo FlashVideoPlayerPlugin_parsecontent($widget_options['FlashVideoPlayerPlugin_WidgetOptions_tag']);
    echo $a['after_widget'];
 }
 	
function FlashVideoPlayerPlugin_control() {
	if(isset($_POST['FlashVideoPlayerPlugin_WidgetSubmit'])) {
		update_option('FlashVideoPlayerPlugin_WidgetOptions', array('FlashVideoPlayerPlugin_WidgetOptions_title'=>$_POST['FlashVideoPlayerPlugin_WidgetOptions_title'], 'FlashVideoPlayerPlugin_WidgetOptions_tag'=>$_POST['FlashVideoPlayerPlugin_WidgetOptions_tag']));
	}
	$widget_options = get_option('FlashVideoPlayerPlugin_WidgetOptions');
	echo '<p><label>Title: <input name="FlashVideoPlayerPlugin_WidgetOptions_title" type="text" value="' . $widget_options['FlashVideoPlayerPlugin_WidgetOptions_title'] . '" /></label></p>';
	echo '<p><label>Tag: <textarea rows="5" name="FlashVideoPlayerPlugin_WidgetOptions_tag">' . $widget_options['FlashVideoPlayerPlugin_WidgetOptions_tag'] . '</textarea></label></p>';
	echo '<input type="hidden" name="FlashVideoPlayerPlugin_WidgetSubmit" value="1" />';
}

// Plugin Functions Start
function FlashVideoPlayerPlugin_parsecontent($content) {
	$content = preg_replace_callback("/\[flashvideo ([^]]*)\/\]/i", "FlashVideoPlayerPlugin_renderplayer", $content);
	return $content;
}

function FlashVideoPlayerPlugin_adminmenu() {
	add_options_page('Flash Video', 'Flash Video', '8', 'flash-video-player.php', 'FlashVideoPlayerPlugin_optionspage');
}

function FlashVideoPlayerPlugin_head() {
	global $site_url, $plugin_specific;
	switch($plugin_specific['swfobject']['v']) {
		case 'local':
			echo '<script type="text/javascript" src="' . $site_url . '/wp-content/plugins/flash-video-player/swfobject.js"></script>' . "\n";
		break;
		case 'google':
			echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js"></script>' . "\n";
		break;
		case 'none':
		break;
	}
}

function FlashVideoPlayerPlugin_footer() {
	global $plugin_specific;
	if($plugin_specific['ltasurl']['v'] != '') {
		echo '<script language="JavaScript" src="' . $plugin_specific['ltasurl']['v'] . '"></script>';
	}
}

// Initialization
function FlashVideoPlayerPlugin_activate() {
	// update_option will add if it doesn't exist
	add_option('FlashVideoPlayerPlugin_WidgetOptions');
	update_option('FlashVideoPlayerPlugin_PlayerOptions', loadDefaultOptions());
}

function FlashVideoPlayerPlugin_deactivate() {
	delete_option('FlashVideoPlayerPlugin_PlayerOptions');
	delete_option('FlashVideoPlayerPlugin_WidgetOptions');
}

function FlashVideoPlayerPlugin_renderplayer($tag_string) {	
	global $site_url, $videoid, $saved_options;
	$rss_output = '';
	$output = '';
	$flashvars = array();
	// First thing we need to do is to clean up the tag options:
	// Clean up WordPress converted quotes
	$tag_string = str_replace(array('&#8221;','&#8243;'), '', $tag_string[1]);
	// Match key value pairs
	preg_match_all('/([.\w]*)=(.*?) /i', $tag_string, $pairs);		
	// Create an associative array with the matched pairs
	foreach ( (array) $pairs[1] as $key => $value ) {
		// Strip out legacy quotes and load inline options
		$inline_options[$value] = trim(str_replace('"', '', $pairs[2][$key]));
	}
	// Make sure the only required parameter has been provided
	if ( !array_key_exists('filename', $inline_options) && !array_key_exists('file', $inline_options) ) {
		return '<div style="background-color:#ff9;padding:10px;"><p>Error: Required parameter "file" is missing!</p></div>';
		exit;
	}
	// Deprecate filename in favor of file. 
	if(array_key_exists('filename', $inline_options)) {
		$inline_options['file'] = $inline_options['filename'];
		unset($inline_options['filename']);
	}
	// Override inline parameters 
	if ( array_key_exists('width', $inline_options) ) {
		$saved_options['Video Size']['width']['v'] = $inline_options['width'];
	}
	if ( array_key_exists('height', $inline_options) ) {
		$saved_options['Video Size']['height']['v'] = $inline_options['height'];
	}
	if ( array_key_exists('image', $inline_options) ) {
		// Respect remote images
		if(strpos($inline_options['image'], 'http://') === false) {
			$inline_options['image'] = $site_url . '/' . $inline_options['image'];
		}
		// If an image is found, embed it in the RSS feed.
		$rss_output .= '<img src="' . $saved_options['File Properties']['image']['v'] . '" />';
	} else {
		if ($saved_options['File Properties']['image']['v'] == '') {
			// Place the default image, since there isn't one set.
			$rss_output .= '<img src="' . $site_url . '/' . 'wp-content/plugins/flash-video-player/default_video_player.gif" />';
		} else {
			$rss_output .= '<img src="' . $saved_options['File Properties']['image']['v'] . '" />';
		}
	}
	
	if(strpos($inline_options['file'], 'http://') !== false || isset($inline_options['streamer']) || strpos($inline_options['file'], 'rtmp://') !== false || strpos($inline_options['file'], 'https://') !== false) {
		// This is a remote file, so leave it alone but clean it up a little
		$find = array('&#035;', '&#036;', '&#037;', '&#038;', '&#043;', '&#044;', '&#045;', '&#063;', '&#095;', '&amp;');
		$replace = array('#', '$', '%', '&', '+', ',', '-', '?', '_', '&');
		$inline_options['file'] = str_replace($find, $replace, $inline_options['file']);
	} else {
		$inline_options['file'] = $site_url . '/' . $inline_options['file'];
	}
	// Make RTMP Streams backward compatible
	if(strpos($inline_options['file'], 'rtmp://') !== false && strpos($inline_options['file'], '&id=') !== false) {
		list($inline_options['streamer'], $inline_options['file']) = split('&id=', $inline_options['file']);
		$inline_options['type'] = 'video';
	}
	
	$output .= "\n" . '<!-- Start Flash Video Player Plugin -->';
	
	$content_tag = "\n" . '<div id="video' . $videoid . '" class="flashvideo"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this content.</div>';

	$output = $content_tag;
		
    $output .= "\n" . '<script type="text/javascript">';
	foreach( (array) $saved_options as $k=>$v) {
			foreach ( (array) $v as $key=>$value ) {
			// Allow for inline override of all parameters			
			if ( array_key_exists($key, $inline_options) ) {
				$value['v'] = $inline_options[$key];
			}
			if ( $value['v'] != '' && $value['v'] != 'undefined') {
				// Replace underscores with periods, this is a fix for the new variable names introduced in the 5.1 player.
				if(strpos($key, '_') !== false) {
					$key =  str_replace('_', '.', $key);
				}
				// Check to see if we're processing a "skin". If so, make the filename absolute using the 
				// fully qualified path. This will ensure the player displays correctly on category pages as well.
				if($key == 'skin') {
					//if($value['v'] != 'undefined') {
						$flashvars[] = "\n\t'" . $key . "' : '" . $site_url . "/wp-content/plugins/flash-video-player/skins/" . $value['v'] . '/' . trim($value['v']) . ".swf'";
					//}
				} else {
					$flashvars[] = "\n\t'" . $key . "' : '" . trim($value['v']) . "'";
				}	
			}
		}
	}
	$flashvars = implode(",", $flashvars);
	$output .= "\n" . "var params = { 'allowfullscreen': 'true', 'allowscriptaccess': 'always', 'wmode': 'transparent' };";
	$output .= "\n" . "var attributes = { 'id': 'video" . $videoid . "', 'name': 'video" . $videoid . "'};";
	$output .= "\n" . 'var flashvars = {';
	$output .=  $flashvars;
	$output .= "\n" . ' };';
	$output .= "\n" . 'swfobject.embedSWF("' . $site_url .'/wp-content/plugins/flash-video-player/mediaplayer/player.swf", "video' . $videoid . '", "' . $saved_options['Video Size']['width']['v'] . '", "' . $saved_options['Video Size']['height']['v'] . '", "9.0.0","' . $site_url . '/wp-content/plugins/flash-video-player/mediaplayer/expressinstall.swf", flashvars, params, attributes);';
	$output .= "\n" . '</script>';
	$output .= "\n" . '<!-- End Flash Video Player Plugin -->' . "\n";
	$videoid++;
	if(is_feed()) {
		return $rss_output;
	} else {
		return $output;
	}	
}

function FlashVideoPlayerPlugin_optionspage() {
	global $site_url, $saved_options, $plugin_specific;
	$saved_options['Plugin-Specific'] = $plugin_specific;
	$message = '';	
	// Process form submission
	if ($_POST) {
		foreach($saved_options as $h=>$o) {			
			foreach( (array) $o as $key=>$value) {
				// Handle Checkboxes that don't send a value in the POST
				if($value['t'] == 'cb' && !isset($_POST[$key])) {
					$saved_options[$h][$key]['v'] = 'false';
				}
				if($value['t'] == 'cb' && isset($_POST[$key])) {
					$saved_options[$h][$key]['v'] = 'true';
				}
				// Handle all other changed values
				if(isset($_POST[$key]) && $value['t'] != 'cb') {
					$saved_options[$h][$key]['v'] = $_POST[$key];
				}
			}
		}
		update_option('FlashVideoPlayerPlugin_PlayerOptions', $saved_options);
		$message = '<div class="updated"><p><strong>Options saved.</strong></p></div>';
	}
	//$swfobject = get_option('FlashVideoPlayerPlugin_SWFObject');
	echo '<div class="wrap">';
	echo '<div id="icon-options-general" class="icon32"><br /></div>';
	echo '<h2>Flash Video Options</h2>';
	echo $message;
	echo '<form method="post" action="options-general.php?page=flash-video-player.php">';
	echo "<p>Welcome to the flash video player plugin options menu! Here you can set all (or none) of the available player variables to default values for your website. If you have a question what valid values for the variables are, please consult the <a href='http://mac-dev.net/blog/flash-video-player-plugin-customization/'>online documentation</a>. If your question isn't answered there or in the <a href='http://mac-dev.net/blog/frequently-asked-questions/'>F.A.Q.</a>, please ask in the <a href='http://www.mac-dev.net/blog/forum'>forum</a>.</p>";
	// Locate the skins directory
	$skins_dir = str_replace('wp-admin', 'wp-content', dirname(__FILE__)) . '/skins/';
	$skins = array();
	// Poll the directories listed in the skins folder to generate the dropdown list with valid skin files
	chdir($skins_dir);
	if ($handle = opendir($skins_dir)) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." && $file != "..") {
				if(is_dir($file)) {
					$skins[] = $file;
				}
	        }
	    }
	    closedir($handle);
	}
	// Add the default value onto the beginning of the skins array
	array_unshift($skins, 'undefined');
	$saved_options['Layout']['skin']['op'] = $skins;
	foreach( (array) $saved_options as $key=>$value) {
		echo '<h3>' . $key . '</h3>' . "\n";
		echo '<table class="form-table">' . "\n";
		foreach( (array) $value as $k=>$v) {
		// Adding Support for "Do Not Display"
		if($v['t'] != 'dnd') {
			echo '<tr><th scope="row">' . $v['dn'] . '</th><td>' . "\n";
			switch ($v['t']) {
				case 'tx':
					$size = '';
					if(isset($v['sz'])) {
						$size = ' size="' . $v['sz'] . '"';	
					}
					echo '<input type="text" name="' . $k . '" value="' . $v['v'] . '"' . $size . ' />';
					break;
				case 'dd':
					echo '<select name="' . $k . '">';
					foreach( (array) $v['op'] as $o) {
						$selected = '';
						if($o == $v['v']) {
							$selected = ' selected';
						}
						echo '<option value="' . $o . '"' . $selected . '>' . ucfirst($o) . '</option>';
					}
					echo '</select>';
					break;
				case 'cb':
					echo '<input type="checkbox" class="check" name="' . $k . '" ';
					if($v['v'] == 'true') {
						echo 'checked="checked"';
					}
					echo ' />';
					break;
				}
				echo '</td></tr>' . "\n";
			}
		}
		echo '</table>' . "\n";
	}

	echo '<p class="submit"><input class="button-primary" type="submit" method="post" value="Update Options"></p>';
	echo '</form>';
	echo '</div>';
}

function loadDefaultOptions() {
	$options = array (
		'File Properties' => array (
			'author' => array ('dn' => 'Author', 't' => 'dnd', 'v' => ''),
			'date' => array ('dn' => 'Publish Date', 't' => 'dnd', 'v' => ''),
			'description' => array ('dn' => 'Description', 't' => 'dnd', 'v' => ''),
			'duration' => array ('dn' => 'Duration', 't' => 'dnd', 'v' => ''),
			'file' => array ('dn' => 'File Name', 't' => 'dnd', 'v' => ''),
			'image' => array ('dn' => 'Preview Image', 't' => 'tx', 'v' => ''),
			'start' => array ('dn' => 'Start', 't' => 'dnd', 'v' => ''),
			'streamer' => array ('dn' => 'Streamer', 't' => 'tx', 'v' => ''),
			'tags' => array ('dn' => 'Tags', 't' => 'dnd', 'v' => ''),
			'title' => array ('dn' => 'Title', 't' => 'dnd', 'v' => ''),
			'provider' => array ('dn' => 'Provider', 't' => 'dd', 'v' => 'undefined', 'op'=> array('undefined','video','sound','image','youtube','http','rtmp'))
		),
		'Video Size' => array (
			'width' => array ('dn' => 'Player Width', 't' => 'tx', 'v' => '400', 'sz'=>'4'),
			'height' => array ('dn' => 'Player Height', 't' => 'tx', 'v' => '280', 'sz'=>'4')
		),
		'Colors' => array (
			'backcolor' => array ('dn' => 'Background Color', 't' => 'tx', 'v' => '', 'sz'=>'8'),
			'frontcolor' => array ('dn' => 'Foreground Color', 't' => 'tx', 'v' => '', 'sz'=>'8'),
			'lightcolor' => array ('dn' => 'Light Color', 't' => 'tx', 'v' => '', 'sz'=>'8'),
			'screencolor' => array ('dn' => 'Screen Color', 't' => 'tx', 'v' => '', 'sz'=>'8')
		),
		'Layout' => array (
			'controlbar' => array ('dn' => 'Controlbar', 't' => 'dd', 'v' => 'bottom', 'op'=> array('none', 'bottom', 'over')),
			'dock' => array ('dn' => 'Dock', 't' => 'cb', 'v' => 'false'),
			'icons' => array ('dn' => 'Play Icon', 't' => 'cb', 'v' => 'true'),
			'logo_file' => array ('dn' => 'Logo File (Licensed)', 't' => 'tx', 'v' => '', 'sz'=>'8'),
			'logo_link' => array ('dn' => 'Logo Link (Licensed)', 't' => 'tx', 'v' => '', 'sz'=>'8'),
			'logo_hide' => array ('dn' => 'Logo Hide (Licensed)', 't' => 'cb', 'v' => 'false'),
			'logo_position' => array ('dn' => 'Logo Position (Licensed)', 't' => 'dd', 'v' => 'bottom-left', 'op'=>array('bottom-left', 'bottom-right', 'top-left', 'top-right')),
			'playlist' => array ('dn' => 'Playlist', 't' => 'dd', 'v' => 'none', 'op'=> array('none','bottom', 'over', 'right')),
			'playlistsize' => array ('dn' => 'Playlist Size', 't' => 'tx', 'v' => '', 'sz'=>'4')
		),
		'Skin' => array (
			'skin'=> array ('dn' => 'Skin', 't' => 'dd', 'v' => 'undefined', 'op'=> array('undefined', 'bright', 'overlay', 'simple', 'stylish', 'swift', 'thin'))
		),
		'Behavior' => array (
			'autostart' => array ('dn' => 'Auto Start', 't' => 'cb', 'v' => 'false'),
			'bufferlength' => array ('dn' => 'Buffer Length', 't' => 'tx', 'v' => '1', 'sz'=>'1'),
			// FUTURE
			//'displaytitle' => array ('dn' => 'Display Title', 't' => 'cb', 'v' => 'false'),
			'item' => array ('dn' => 'Playlist Item', 't' => 'tx', 'v' => '0', 'sz'=>'1'),
			'mute' => array ('dn' => 'Mute Sounds', 't' => 'cb', 'v' => 'false'),
			'repeat' => array ('dn' => 'Repeat', 't' => 'dd', 'v' => 'none', 'op' => array('none', 'list', 'always', 'single')),
			'shuffle' => array ('dn' => 'Shuffle', 't' => 'cb', 'v' => 'false'),
			'smoothing' => array ('dn' => 'Smoothing', 't' => 'cb', 'v' => 'true'),
			'stretching' => array ('dn' => 'Stretching', 't' => 'dd', 'v' => 'uniform', 'op' => array('none', 'exactfit', 'uniform', 'fill')),
			'volume' => array ('dn' => 'Startup Volume', 't' => 'dd', 'v' => '90', 'op' => array('0', '10', '20', '30', '40', '50', '60', '70', '80', '90', '100'))
		),
		'External' => array (
			'plugins' => array ('dn' => 'Plugins', 't' => 'tx', 'v' => '', 'sz'=>'8'),
			'ltas_cc' => array ('dn' =>'Long Tail Channel', 't' => 'tx', 'v' => '', 'sz'=>'8'),
			'config' => array ('dn' =>'Config XML', 't' => 'tx', 'v' => '', 'sz'=>'8'),
			'debug' => array ('dn' =>'Player Debug', 't' => 'dd', 'v' => 'undefined', 'op' => array('undefined','arthropod', 'console', 'trace'))
		),
		'Plugin-Specific' => array (
			'swfobject' => array ('dn' => 'SWFObject Source', 't' => 'dd', 'v' => 'local', 'op' => array('local', 'google', 'none'))
		)
	);
return $options;
}

// WordPress Plugin Hooks
register_activation_hook( __FILE__, 'FlashVideoPlayerPlugin_activate');
register_deactivation_hook( __FILE__, 'FlashVideoPlayerPlugin_deactivate');
add_action('admin_menu', 'FlashVideoPlayerPlugin_adminmenu');
add_filter('the_content', 'FlashVideoPlayerPlugin_parsecontent');
add_action('wp_head', 'FlashVideoPlayerPlugin_head');
add_action('wp_footer', 'FlashVideoPlayerPlugin_footer');

// WordPress Widget Hook
add_action('plugins_loaded', 'FlashVideoPlayerPlugin_widgetregister');
?>