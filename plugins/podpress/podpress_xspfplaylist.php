<?php
if (FALSE !== strpos((__FILE__), 'wp-content')) {
	$script_name_parts = explode('wp-content', (__FILE__));
	if ( 1 < count($script_name_parts)) {
		include_once($script_name_parts[0].'wp-config.php');
		GLOBAL $blog_id, $wp_version;
		if ( defined('PODPRESS_CUSTOM_XSPF_URL_'.$blog_id) AND '' !== constant('PODPRESS_CUSTOM_XSPF_URL_'.$blog_id) ) {
			header('Location:'.constant('PODPRESS_CUSTOM_XSPF_URL_'.$blog_id));
		} else {
			if ( TRUE == version_compare($wp_version, '2.8', '>=') ) {
				$podpress_xspf_widget_temp = get_option('podpress_xspf_widget_temp');
				$widget_id = intval(end(explode('-', $podpress_xspf_widget_temp)));
				$xspf_widgets_options = get_option('widget_podpress_xspfplayer');
				if ( is_array($xspf_widgets_options) ) {
					$options = $xspf_widgets_options[$widget_id];
				}
				delete_option('podpress_xspf_widget_temp');
			} else {
				$options = get_option('widget_podPressXspfPlayer');
			}
			if ( isset($options['xspf_use_custom_playlist']) AND isset($options['xspf_custom_playlist_url']) AND TRUE === $options['xspf_use_custom_playlist'] AND FALSE == empty($options['xspf_custom_playlist_url']) ) {
				header('Location:'.$options['xspf_custom_playlist_url']);
			} else {
				header('Location:'.get_feed_link('playlist.xspf'));
			}
		}
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Wed, 28 Oct 2010 05:00:00 GMT'); // Date in the past
		exit;
	} else {
		unset($script_name_parts);
		die('Error: unable to load the playlist');
	}
	unset($script_name_parts);
} else {
	die('podPress seems not to be in the wp-content folder.');
}
?>