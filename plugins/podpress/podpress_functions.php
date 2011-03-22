<?php
/*
License:
 ==============================================================================

    Copyright 2006  Dan Kuykendall  (email : dan@kuykendall.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-107  USA
*/

	if(!function_exists('getmicrotime')) {
		function getmicrotime() {
			list($usec, $sec) = explode(" ",microtime());
			return ((float)$usec + (float)$sec);
		}
	}

	function podPress_WPVersionCheck($input = '2.0.0') {
		GLOBAL $wp_version;
		//~ printphpnotices_var_dump('podPress_WPVersionCheck');
		//~ $wp_version_test = '3.0.4';
		//~ printphpnotices_var_dump($input);
		//~ printphpnotices_var_dump($wp_version);
		if ( substr($wp_version, 0, 12) == 'wordpress-mu' ) {
			return true;
		}
		//~ printphpnotices_var_dump('podPress_WPVersionCheck b');
		//~ printphpnotices_var_dump($input.' <= '.$wp_version_test);
		//~ printphpnotices_var_dump( version_compare($input, $wp_version_test, '<=') );
		//~ printphpnotices_var_dump( (float) $input <= (float) $wp_version_test );
		//~ printphpnotices_var_dump($input.' <= '.$wp_version);
		//~ printphpnotices_var_dump( (float) $input <= (float) $wp_version );
		//~ printphpnotices_var_dump('podPress_WPVersionCheck c');
		return ( (float) $input <= (float) $wp_version );
	}

	function podPress_iTunesLink() {
		GLOBAL $podPress;
		echo $podPress->iTunesLink();
	}

	function podPress_siteurl($noDomain = false) {
		if (!defined('PODPRESSSITEURL') || $noDomain) {
			$result = '';
			$urlparts = parse_url(get_option('siteurl'));
			if(!$noDomain) {
				if(empty($urlparts['scheme'])) {
					$urlparts['scheme'] = 'http';
				}
				$result .= $urlparts['scheme'].'://'.$_SERVER['HTTP_HOST'];
				if($urlparts['port'] != '' && $urlparts['port'] != '80') {
					$result .= ':'.$urlparts['port'];
				}
			}
			if(isset($urlparts['path'])) {
				$result .= $urlparts['path'];
			}

			if(substr($result, -1, 1) != '/') {
				$result .= '/';
			}

			if( TRUE == isset($urlparts['query']) AND '' != $urlparts['query'] ) {
				$result .= '?'.$urlparts['query'];
			}
			if( TRUE == isset($urlparts['fragment']) AND '' != $urlparts['fragment']) {
				$result .= '#'.$urlparts['fragment'];
			}
			if($noDomain) {
				return $result.'wp-content/plugins/';
			}
			define('PODPRESSSITEURL', $result.'wp-content/plugins/');
		}
		return PODPRESSSITEURL;
	}

	function podPress_url($noDomain = false) {
		if($noDomain) {
			if (!defined('PODPRESSURL')) {
				define('PODPRESSURL', podPress_siteurl($noDomain).'podpress/');
			}
			return PODPRESSURL;
		} else {
			//~ $result = get_option('siteurl');
			//~ if(substr($result, -1, 1) != '/') {
				//~ $result .= '/';
			//~ }
			//~ return $result.'wp-content/plugins/podpress/';
			return PODPRESS_URL.'/';
		}
	}

	function podPress_getFileExt($str)
	{
		$pos = strrpos($str, '.');
		$pos = $pos+1;
		return substr(strtolower($str), $pos);
	}

	function podPress_getFileName($str)
	{
		if(strrpos($str, '/')) {
			$pos = strrpos($str, '/');
			$pos = $pos+1;
			return substr($str, $pos);
		} elseif(strrpos($str, ':')) {
			$pos = strrpos($str, ':');
			$pos = $pos+1;
			return substr($str, $pos);
		} else {
			return $str;
		}
	}

	function podPress_wordspaceing($txt, $number = 5, $paddingchar = ' ') {
		$txt_array = array();
		$len = strlen($txt);
		$count=$len/$number;

		$i=0;
		while($i<=$count) {
			if($i==0) {$ib=0;} else {$ib=($i*$number)+1;}
			$txt_array[$i]=substr($txt, $ib, $number);
			$i++;
		}

		$i=0;
		$count_array=count($txt_array)-1; 
		while ($i<=$count_array) {
			if ($i==0) {$txt=$txt_array[$i].$paddingchar;} else {$txt.=''.$txt_array[$i].' ';}
			$i++;
		}
		return $txt;
	}
	
	function podPress_stringLimiter($str, $len, $snipMiddle = false)
	{
		if (strlen($str) > $len) {
			if($snipMiddle) {
				$startlen = $len / 3;
				$startlen = $startlen - 1;
				$endlen = $startlen * 2;
				$endlen = $endlen - $endlen - $endlen;
				return substr($str, 0, $startlen).'...'.substr($str, $endlen);
			} else {
				$len = $len - 3;
				return substr($str, 0, $len).'...';
			}
		} else {
			return $str;
		}
	}

	/**
	* podPress_strlimiter2 - if the input phrase is longer then maxlength then cut out character from the middle of the phrase
	*
	* @package podPress
	* @since 8.8.5 beta 3
	*
	* @param str $phrase input string
	* @param int $maxlength [optional] - max. length of the output string
	* @param bool $abbrev [optional] - use the abbr-tag with the original string as the title element
	* @param str $paddingchar [optional] - character(s) which should symbolize the shortend string / placed in the middle of the shortend string
	* @param str $classname [optional] - name(s) of the CSS class(es) of the abbr-tag
	*
	* @return str phrase with max. length
	*/
	function podPress_strlimiter2($phrase, $maxlength = 25, $abbrev = FALSE, $paddingchar = ' ... ', $classname = 'podpress_abbr') {
		$len = strlen($phrase);
		$maxlen = ($maxlength-strlen($paddingchar));
		if ( $len > $maxlen ) {
			$part1_len = floor($maxlen/2);
			$part1 = substr($phrase, 0,  $part1_len);
			$part2_len = ceil($maxlen/2);
			$part2 = substr($phrase, -$part2_len, $len);
			if ($abbrev == TRUE) {
				if ( Trim($classname) != '' ) {
					return '<span class="'.$classname.'" title="'.attribute_escape(str_replace('"', '\'', $phrase)).'">' . $part1 . $paddingchar . $part2 . '</span>';
				} else {
					return '<span title="'.attribute_escape(str_replace('"', '\'', $phrase)).'">' . $part1 . $paddingchar . $part2 . '</span>';
				}
			} else {
				return $part1 . $paddingchar. $part2;
			}
		} else {
			return $phrase;
		}
	}	
		
	if(!function_exists('html_print_r')) {
		function html_print_r($v, $n = '', $ret = false) {
			if($ret) {
				ob_start();
			}	
			echo $n.'<pre>';
			print_r($v);
			echo '</pre>';
			if($ret) {
				$result = ob_get_contents();
				ob_end_clean();
				return $result;
			}
		}
	}

	if(!function_exists('comment_print_r')) {
		function comment_print_r($v, $n = '', $ret = false) {
			$result = "<!-- \n";
			$result .= html_print_r($v, $n, true);
			$result .= " -->\n";
			if($ret) {
				return $result;
			}
			echo $result;
		}
	}

	if(!function_exists('maybe_unserialize')) {
		function maybe_unserialize($original, $ss = false) {
			if($ss) {
				$original = stripslashes($original);
			}
			if ( false !== $gm = @ unserialize($original) ) {
				return $gm;
			} else {
				return $original;
			}
		}
	}

	if(!function_exists('isBase64')) {
		function isBase64($str)
		{
			$_tmp=preg_replace("/[^A-Z0-9\+\/\=]/i",'',$str);
			return (strlen($_tmp) % 4 == 0 ) ? true : false;
		}
	}

	function podPress_mimetypes($ext, $mp4_type = 'audio') {
		$ext = strtolower($ext);
		$ext_list = array (
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpe' => 'image/jpeg',
			'gif' => 'image/gif',
			'png' => 'image/png',
			'bmp' => 'image/bmp',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'ico' => 'image/x-icon',
			'flv' => 'video/flv',
			'asf' => 'video/asf',
			'wmv' => 'video/wmv',
			'asx' => 'video/asf',
			'wax' => 'video/asf',
			'wmx' => 'video/asf',
			'avi' => 'video/avi',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',
			'm4v' => 'video/x-m4v',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mpe' => 'video/mpeg',
			'txt' => 'text/plain',
			'c' => 'text/plain',
			'cc' => 'text/plain',
			'h' => 'text/plain',
			'rtx' => 'text/richtext',
			'css' => 'text/css',
			'htm' => 'text/html',
			'html' => 'text/html',
			'mp3' => 'audio/mpeg',
			'mp4' => $mp4_type.'/mpeg',
			'm4a' => 'audio/x-m4a',
			'aa' => 'audio/audible',
			'ra' => 'audio/x-realaudio',
			'ram' => 'audio/x-realaudio',
			'wav' => 'audio/wav',
			'ogg' => 'audio/ogg',
			'ogv' => 'video/ogg',
			'mid' => 'audio/midi',
			'midi' => 'audio/midi',
			'wma' => 'audio/wma',
			'rtf' => 'application/rtf',
			'js' => 'application/javascript',
			'pdf' => 'application/pdf',
			'doc' => 'application/msword',
			'pot' => 'application/vnd.ms-powerpoint',
			'pps' => 'application/vnd.ms-powerpoint',
			'ppt' => 'application/vnd.ms-powerpoint',
			'wri' => 'application/vnd.ms-write',
			'xla' => 'application/vnd.ms-excel',
			'xls' => 'application/vnd.ms-excel',
			'xlt' => 'application/vnd.ms-excel',
			'xlw' => 'application/vnd.ms-excel',
			'mdb' => 'application/vnd.ms-access',
			'mpp' => 'application/vnd.ms-project',
			'swf' => 'application/x-shockwave-flash',
			'class' => 'application/java',
			'tar' => 'application/x-tar',
			'zip' => 'application/zip',
			'gz' => 'application/x-gzip',
			'gzip' => 'application/x-gzip',
			'torrent' => 'application/x-bittorrent',
			'exe' => 'application/x-msdownload'
		);
		if(!isset($ext_list[$ext])) {
			return 'application/unknown';
		}
		return $ext_list[$ext];
	}

	function podPress_maxMemory() {
		$max = ini_get('memory_limit');

		if (preg_match('/^([\d\.]+)([gmk])?$/i', $max, $m)) {
			$value = $m[1];
			if (isset($m[2])) {
				switch(strtolower($m[2])) {
					case 'g': $value *= 1024;  # fallthrough
					case 'm': $value *= 1024;  # fallthrough
					case 'k': $value *= 1024; break;
					default: $value = 2048000;
				}
			}
			$max = $value;
		} else {
		  $max = 2048000;
		}
		return $max/2;
	}
	
	/**************************************************************/
	/* Functions for supporting the widgets */
	/**************************************************************/
	/* for WP < 2.8 only */
	function podPress_loadWidgets () {
		global $wp_version;		
		if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') ) {
			return;
		}
		if (TRUE == version_compare($wp_version, '2.2', '>=')) {
			// Feed Buttons widget
			$widget_ops = array(
				'classname' => 'podpress_feedbuttons', 
				'description' => __('Shows buttons for the podcast feeds in the sidebar', 'podpress')
			);
			$control_ops = array('width' => 400, 'height' => 700,	'id_base' => 'podpressfeedbuttons');
			$id = $control_ops['id_base'];
			wp_register_sidebar_widget($id, __('podPress - Feed Buttons','podpress'), 'podPress_feedButtons', $widget_ops);
			wp_register_widget_control($id, __('podPress - Feed Buttons','podpress'), 'podPress_feedButtons_control', $control_ops);
			
			// XSPF Player widget
			$widget_ops = array(
				'classname' => 'podpress_xspfplayer', 
				'description' => __('Shows a XSPF Player in the sidebar which uses e.g. the XSPF playlist of your podcast episodes', 'podpress')
			);
			$control_ops = array('width' => 400, 'height' => 400,	'id_base' => 'podpressxspfplayer');
			$id = $control_ops['id_base'];
			wp_register_sidebar_widget($id, __('podPress - XSPF Player','podpress'), 'podPress_xspfPlayer', $widget_ops);
			wp_register_widget_control($id, __('podPress - XSPF Player','podpress'), 'podPress_xspfPlayer_control', $control_ops);
		} else {
			// Feed Buttons widget
			register_sidebar_widget(array('podPress - Feed Buttons', 'widgets'), 'podPress_feedButtons', $widget_ops);
			register_widget_control(array('podPress - Feed Buttons', 'widgets'), 'podPress_feedButtons_control', 400, 700);
			
			// XSPF Player widget
			register_sidebar_widget(array('podPress - XSPF Player', 'widgets'), 'podPress_xspfPlayer');
			register_widget_control(array('podPress - XSPF Player', 'widgets'), 'podPress_xspfPlayer_control', 400, 400);
		}
	}

	/* for WP < 2.8 only */
	function podPress_feedButtons_control() {
		GLOBAL $podPress, $wp_version, $wpdb;
		$options = get_option('widget_podPressFeedButtons');
		$newoptions = $options;
		if ( isset($_POST['podPressFeedButtons-submit']) ) {
			$newoptions['blog'] = isset($_POST['podPressFeedButtons-posts']);
			$newoptions['comments'] = isset($_POST['podPressFeedButtons-comments']);
			$newoptions['entries-atom'] = isset($_POST['podPressFeedButtons-entries-atom']);
			$newoptions['comments-atom'] = isset($_POST['podPressFeedButtons-comments-atom']);
			$newoptions['posts_buttonurl'] =  clean_url($_POST['podPressFeedButtons-posts_buttonurl'], array('http', 'https'), 'db');
			$newoptions['comments_buttonurl'] =  clean_url($_POST['podPressFeedButtons-comments_buttonurl'], array('http', 'https'), 'db');
			$newoptions['entries-atom_buttonurl'] =  clean_url($_POST['podPressFeedButtons-entries-atom_buttonurl'], array('http', 'https'), 'db');
			$newoptions['comments-atom_buttonurl'] =  clean_url($_POST['podPressFeedButtons-comments-atom_buttonurl'], array('http', 'https'), 'db');			
			$newoptions['posts_altfeedurl'] =  clean_url($_POST['podPressFeedButtons-posts_altfeedurl'], array('http', 'https'), 'db');
			$newoptions['comments_altfeedurl'] =  clean_url($_POST['podPressFeedButtons-comments_altfeedurl'], array('http', 'https'), 'db');
			$newoptions['entries-atom_altfeedurl'] =  clean_url($_POST['podPressFeedButtons-entries-atom_altfeedurl'], array('http', 'https'), 'db');
			$newoptions['comments-atom_altfeedurl'] =  clean_url($_POST['podPressFeedButtons-comments-atom_altfeedurl'], array('http', 'https'), 'db');
			$newoptions['itunes'] = isset($_POST['podPressFeedButtons-itunes']);
			$newoptions['itunes_buttonurl'] = clean_url($_POST['podPressFeedButtons-itunes_buttonurl'], array('http', 'https'), 'db');
			// iscifi new option for itunes protocol
			$newoptions['iprot'] = isset($_POST['podPressItunesProtocol-iprot']);
			$blog_charset = get_bloginfo('charset');
			$newoptions['title'] = htmlspecialchars(strip_tags(trim($_POST['podPressFeedButtons-title'])), ENT_QUOTES, $blog_charset);
			$newoptions['buttons-or-text'] = $_POST['podPressFeedButtons-buttons-or-text'];
			// CategoryCasting Feeds:
			if ( is_array($_POST['podPressFeedButtons-catcast']) ) {
				foreach ( $_POST['podPressFeedButtons-catcast'] as $cat_id => $feed_options ) {
					if ( 'yes' === $feed_options['use'] ) {
						$newoptions['catcast'][$cat_id]['use'] = 'yes';
					} else {
						$newoptions['catcast'][$cat_id]['use'] = 'no';
					}
					$newoptions['catcast'][$cat_id]['buttonurl'] = clean_url($feed_options['buttonurl'], array('http', 'https'), 'db');
					$newoptions['catcast'][$cat_id]['altfeedurl'] = clean_url($feed_options['altfeedurl'], array('http', 'https'), 'db');
				}
			}
			// podPress Feeds:
			if ( is_array($_POST['podpressfeeds']) ) {
				foreach ( $_POST['podpressfeeds'] as $feed_slug => $feed_options ) {
					if ( 'yes' === $feed_options['use'] ) {
						$newoptions['podpressfeeds'][$feed_slug]['use'] = 'yes';
					} else {
						$newoptions['podpressfeeds'][$feed_slug]['use'] = 'no';
					}
					$newoptions['podpressfeeds'][$feed_slug]['button'] = $feed_options['button'];
					if ( 'custom' === $feed_options['button']  ) {
						$newoptions['podpressfeeds'][$feed_slug]['buttonurl'] = clean_url($feed_options['buttonurl'], array('http', 'https'), 'db');
					} else {
						$newoptions['podpressfeeds'][$feed_slug]['buttonurl'] = PODPRESS_URL.'/images/'.$feed_options['button'];
					}
					$newoptions['podpressfeeds'][$feed_slug]['altfeedurl'] = clean_url($feed_options['altfeedurl'], array('http', 'https'), 'db');
				}
			}
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_podPressFeedButtons', $options);
		}
		if(!isset($options['blog'])) {
			$options['blog'] = false;
		}
		if(!isset($options['comments'])) {
			$options['comments'] = false;
		}
		if(!isset($options['entries-atom'])) {
			$options['entries-atom'] = false;
		}
		if(!isset($options['comments-atom'])) {
			$options['comments-atom'] = false;
		}
		if(!isset($options['itunes'])) {
			$options['itunes'] = true;
		}
		if (!isset($options['iprot'])) {
			$options['iprot'] = false;
		}
		if (!isset($options['buttons-or-text'])) {
			$options['buttons-or-text'] = 'buttons';
		}
		if (!isset($options['itunes_buttonurl'])) {
			$options['itunes_buttonurl'] = PODPRESS_URL.'/images/itunes.png';
		}
		if (!isset($options['posts_buttonurl'])) {
			$options['posts_buttonurl'] = PODPRESS_URL.'/images/feed_button-rss-blog.png';
		}
		if (!isset($options['comments_buttonurl'])) {
			$options['comments_buttonurl'] = PODPRESS_URL.'/images/feed_button-rss-comments.png';
		}
		if (!isset($options['entries-atom_buttonurl'])) {
			$options['entries-atom_buttonurl'] = PODPRESS_URL.'/images/feed_button-atom-blog.png';
		}
		if (!isset($options['comments-atom_buttonurl'])) {
			$options['comments-atom_buttonurl'] = PODPRESS_URL.'/images/feed_button-atom-comments.png';
		}

		$blog = $options['blog'] ? 'checked="checked"' : '';
		$comments = $options['comments'] ? 'checked="checked"' : '';
		$entries_atom = $options['entries-atom'] ? 'checked="checked"' : '';
		$comments_atom = $options['comments-atom'] ? 'checked="checked"' : '';
		$itunes  = $options['itunes'] ? 'checked="checked"' : '';
		$iprot   = $options['iprot'] ? 'checked="checked"' :'';
		if ( 'text' == $options['buttons-or-text'] ) {
			$text = 'checked="checked"';
			$buttons = '';
		} else {
			$text = '';
			$buttons = 'checked="checked"';
		}
		
		if(!isset($options['title'])) {
			$options['title'] = __('Podcast Feeds', 'podpress');
		}
		$title = attribute_escape(stripslashes($options['title']));
		?>
		<p><label for="podPressFeedButtons-title"><?php _e('Title:', 'podpress'); ?></label> <input class="podpress_widget_settings_title" id="podPressFeedButtons-title" name="podPressFeedButtons-title" type="text" value="<?php echo $title; ?>" /></p>
		<p><?php _e('Show the buttons for the following feeds:', 'podpress'); ?></p>
		
		<div class="podpress_widget_accordion"><!-- Begin: podPress Widget Accordion -->
			<h5><a href=""><?php _e('iTunes Button', 'podpress'); ?></a></h5>
			<div>
				<input class="checkbox" type="checkbox" <?php echo $itunes; ?> id="podPressFeedButtons-itunes" name="podPressFeedButtons-itunes" /> <label for="podPressFeedButtons-itunes"><?php _e('Show iTunes button', 'podpress'); ?></label><br />
				<input class="checkbox" type="checkbox" <?php echo $iprot; ?> id="podPressFeedButtons-iprot" name="podPressItunesProtocol-iprot" /> <label for="podPressFeedButtons-iprot"><?php _e('Use iTunes protocol for URL', 'podpress'); ?> <?php _e('(itpc://)', 'podpress'); ?></label><br />
				<span class="nonessential"><?php _e('The user subscribes immediatly with the click. Otherwise the iTunes Store page of the podcast will be displayed first and the user can subscribe manually.', 'podpress'); ?></span><br />
				<label for="podPressFeedButtons-itunes_buttonurl"><?php _e('Button URL:', 'podpress'); ?></label> <input type="text" id="podPressFeedButtons-itunes_buttonurl" name="podPressFeedButtons-itunes_buttonurl" class="widefat" value="<?php echo $options['itunes_buttonurl']; ?>" />
			</div>
			<?php
			if ( version_compare( $wp_version, '2.1', '>=' ) ) { // ntm: the add_feed() functions exists since WP 2.1 and widgets are probably possible in earlier WP versions with a plugin. 
				$feedbuttons = podpress_get_feed_buttons();
				if ( is_array($podPress->settings['podpress_feeds']) AND FALSE == empty($podPress->settings['podpress_feeds']) ) {
					foreach ($podPress->settings['podpress_feeds'] as $feed) {
						if ( TRUE === $feed['use'] AND FALSE == empty($feed['slug']) ) {
							if ( FALSE == empty($feed['descr']) ) {
								$descr = '<br /><span class="nonessential">'.$feed['descr'].'</span>';
							} else {
								$descr = '';
							}
							// take over the old widget settings
							if ( TRUE == is_array($old_widget_options) AND FALSE == empty($old_widget_options) ) {
								Switch ($feed['slug']) {
									case 'podcast' :
									case 'enhancedpodcast' :
									case 'torrent' :
										$options['podpressfeeds'][$feed['slug']]['use'] = $old_widget_options[$feed['slug']];
										if ( 'podcast' === $feed['slug'] ) {
											$options['podpressfeeds'][$feed['slug']]['button'] = 'feed_button-rss-'.$feed['slug'].'.png';
										} else {
											$options['podpressfeeds'][$feed['slug']]['button'] = 'feed_button-'.$feed['slug'].'.png';
										}
									break;
								}
							}
							if ( TRUE == isset($options['podpressfeeds'][$feed['slug']]['use']) AND 'yes' === $options['podpressfeeds'][$feed['slug']]['use'] ) {
								$podpressfeed_checked = ' checked="checked"';
							} else {
								$podpressfeed_checked = '';
							}
							echo '<h5><a href="">'.$feed['name'].'</a></h5>'."\n";
							echo '<div class="podpress_widget_settings_row_div">'."\n";
							echo '<input type="checkbox"'.$podpressfeed_checked.' id="podPressFeedButtons-'.$feed['slug'].'_use" name="podpressfeeds['.$feed['slug'].'][use]" value="yes" /> <label for="podPressFeedButtons-'.$feed['slug'].'_use">'.sprintf(__('Show %1$s button', 'podpress'), $feed['name']).'</label>'."\n";
							echo $descr."\n";
							echo '<br />'.__('Select a feed button:', 'podpress').'<br />'."\n";
							echo '<span class="podpress_feedbuttonsselectbox">'."\n";
							$id_base = 'podPressFeedButtons-'.$feed['slug'].'_'.$feed['button'];
							$i=0;
							$feedbutton_checked_nr = 0;
							foreach ($feedbuttons as $feedbutton) {
								if ( TRUE == isset($options['podpressfeeds'][$feed['slug']]['button']) AND $feedbutton == $options['podpressfeeds'][$feed['slug']]['button'] ) {
									$feedbutton_checked_nr = $i;
								}
								$i++;
							}
							$i=0;
							foreach ($feedbuttons as $feedbutton) {
								if ( $i == $feedbutton_checked_nr ) {
									$feedbutton_checked = ' checked="checked"';
								} else {
									$feedbutton_checked = '';
								}
								echo '<input type="radio" name="podpressfeeds['.$feed['slug'].'][button]" id="'.$id_base.''.$i.'" value="'.$feedbutton.'"'.$feedbutton_checked.' /> <label for="'.$id_base.''.$i.'"><img src="'.PODPRESS_URL.'/images/'.$feedbutton.'" alt="" /></label><br />'."\n";
								$i++;
							}
							if ( TRUE == isset($options['podpressfeeds'][$feed['slug']]['button']) AND 'custom' == $options['podpressfeeds'][$feed['slug']]['button'] ) {
								echo '<input type="radio" name="podpressfeeds['.$feed['slug'].'][button]" id="'.$id_base.''.$i.'" value="custom" checked="checked" /> <input type="text" id="podPressFeedButtons-'.$feed['slug'].'_'.$feed['custombuttonurl'].'" name="podpressfeeds['.$feed['slug'].'][buttonurl]" class="widefat podpress_customfeedbuttonurl" value="'.$options['podpressfeeds'][$feed['slug']]['buttonurl'].'" />'."\n";
							} else {
								echo '<input type="radio" name="podpressfeeds['.$feed['slug'].'][button]" id="'.$id_base.''.$i.'" value="custom" /> <input type="text" id="podPressFeedButtons-'.$feed['slug'].'_'.$feed['custombuttonurl'].'" name="podpressfeeds['.$feed['slug'].'][buttonurl]" class="widefat podpress_customfeedbuttonurl" value="" />'."\n";
							}
							echo '</span>'."\n";
							echo '<label for="podPressFeedButtons-'.$feed['slug'].'_altfeedurl">'.__('Alternative Feed URL:', 'podpress').'</label> <input type="text" id="podPressFeedButtons-'.$feed['slug'].'_altfeedurl" name="podpressfeeds['.$feed['slug'].'][altfeedurl]" class="widefat" value="'.$options['podpressfeeds'][$feed['slug']]['altfeedurl'].'" />'."\n";
							echo '</div>'."\n";
						}
					}
				}
			} 
			?>
			<h5><a href=""><?php _e('Entries RSS Feed', 'podpress'); ?></a></h5>
			<div>
			<input class="checkbox" type="checkbox" <?php echo $blog; ?> id="podPressFeedButtons-posts" name="podPressFeedButtons-posts" /> <label for="podPressFeedButtons-posts"><?php _e('Entries RSS Feed', 'podpress'); ?></label><br />
			<label for="podPressFeedButtons-posts_buttonurl"><?php _e('Button URL:', 'podpress'); ?></label> <input type="text" id="podPressFeedButtons-posts_buttonurl" name="podPressFeedButtons-posts_buttonurl" class="widefat" value="<?php echo $options['posts_buttonurl']; ?>" /><br />
			<label for="podPressFeedButtons-posts_altfeedurl"><?php _e('Alternative Feed URL:', 'podpress'); ?></label> <input type="text" id="podPressFeedButtons-posts_altfeedurl" name="podPressFeedButtons-posts_altfeedurl" class="widefat" value="<?php echo $options['posts_altfeedurl']; ?>" />
			</div>
			<h5><a href=""><?php _e('Comments RSS Feed', 'podpress'); ?></a></h5>
			<div>
			<input class="checkbox" type="checkbox" <?php echo $comments; ?> id="podPressFeedButtons-comments" name="podPressFeedButtons-comments" /> <label for="podPressFeedButtons-comments"><?php _e('Comments RSS Feed', 'podpress'); ?></label><br />
			<label for="podPressFeedButtons-posts_buttonurl"><?php _e('Button URL:', 'podpress'); ?></label> <input type="text" id="podPressFeedButtons-comments_buttonurl" name="podPressFeedButtons-comments_buttonurl" class="widefat" value="<?php echo $options['comments_buttonurl']; ?>" /><br />
			<label for="podPressFeedButtons-posts_altfeedurl"><?php _e('Alternative Feed URL:', 'podpress'); ?></label> <input type="text" id="podPressFeedButtons-comments_altfeedurl" name="podPressFeedButtons-comments_altfeedurl" class="widefat" value="<?php echo $options['comments_altfeedurl']; ?>" />
			</div>
			<h5><a href=""><?php _e('Entries ATOM Feed', 'podpress'); ?></a></h5>
			<div>
			<input class="checkbox" type="checkbox" <?php echo $entries_atom; ?> id="podPressFeedButtons-entries-atom" name="podPressFeedButtons-entries-atom" /> <label for="podPressFeedButtons-entries-atom"><?php _e('Entries ATOM Feed', 'podpress'); ?></label><br />
			<label for="podPressFeedButtons-posts_buttonurl"><?php _e('Button URL:', 'podpress'); ?></label> <input type="text" id="podPressFeedButtons-entries-atom_buttonurl" name="podPressFeedButtons-entries-atom_buttonurl" class="widefat" value="<?php echo $options['entries-atom_buttonurl']; ?>" /><br />
			<label for="podPressFeedButtons-posts_altfeedurl"><?php _e('Alternative Feed URL:', 'podpress'); ?></label> <input type="text" id="podPressFeedButtons-entries-atom_altfeedurl" name="podPressFeedButtons-entries-atom_altfeedurl" class="widefat" value="<?php echo $options['entries-atom_altfeedurl']; ?>" />
			</div>
			<h5><a href=""><?php _e('Comments ATOM Feed', 'podpress'); ?></a></h5>
			<div>
			<input class="checkbox" type="checkbox" <?php echo $comments_atom; ?> id="podPressFeedButtons-comments-atom" name="podPressFeedButtons-comments-atom" /> <label for="podPressFeedButtons-comments-atom"><?php _e('Comments ATOM Feed', 'podpress'); ?></label><br />
			<label for="podPressFeedButtons-posts_buttonurl"><?php _e('Button URL:', 'podpress'); ?></label> <input type="text" id="podPressFeedButtons-comments-atom_buttonurl" name="podPressFeedButtons-comments-atom_buttonurl" class="widefat" value="<?php echo $options['comments-atom_buttonurl']; ?>" /><br />
			<label for="podPressFeedButtons-posts_altfeedurl"><?php _e('Alternative Feed URL:', 'podpress'); ?></label> <input type="text" id="podPressFeedButtons-comments-atom_altfeedurl" name="podPressFeedButtons-comments-atom_altfeedurl" class="widefat" value="<?php echo $options['comments-atom_altfeedurl']; ?>" />
			</div>
			<?php
			$query_string = 'SELECT option_name, option_value FROM '.$wpdb->prefix.'options WHERE INSTR(option_name, "podPress_category_")';
			$category_feeds = $wpdb->get_results($query_string);			
			if ( isset($category_feeds) AND FALSE == empty($category_feeds) ) {
				foreach ($category_feeds as $feed_options) {
					$feed = maybe_unserialize($feed_options->option_value);
					if ( isset($feed['categoryCasting']) AND 'true' == $feed['categoryCasting'] ) {
						$cat_id = end(explode('_', $feed_options->option_name));
						$checked = $options['catcast'][$cat_id] ? 'checked="checked"' :'';
						echo '<h5><a href="">'.sprintf(__('Category "%1$s" RSS Feed', 'podpress'), get_cat_name($cat_id)).'</a></h5>'."\n";
						echo '<div>'."\n";
						echo '<input type="checkbox" '.$checked.' id="podPressFeedButtons-catcast_'.$cat_id.'_use" name="podPressFeedButtons-catcast['.$cat_id.'][use]" /> <label for="podPressFeedButtons-catcast_'.$cat_id.'_use">'.sprintf(__('Category "%1$s" RSS Feed', 'podpress'), get_cat_name($cat_id)).'</label><br />'."\n";
						echo '<label for="podPressFeedButtons-catcast_'.$cat_id.'_buttonurl">'.__('Button URL:', 'podpress').'</label> <input type="text" id="podPressFeedButtons-catcast_'.$cat_id.'_buttonurl" name="podPressFeedButtons-catcast['.$cat_id.'][buttonurl]" class="widefat" value="'.$options['podPressFeedButtons-catcast'][$cat_id]['buttonurl'].'" /><br />'."\n";							
						echo '<label for="podPressFeedButtons-catcast_'.$cat_id.'_altfeedurl">'.__('Alternative Feed URL:', 'podpress').'</label> <input type="text" id="podPressFeedButtons-catcast_'.$cat_id.'_altfeedurl" name="podPressFeedButtons-catcast['.$cat_id.'][altfeedurl]" class="widefat" value="'.$options['podPressFeedButtons-catcast'][$cat_id]['altfeedurl'].'" />'."\n";
						echo '</div>'."\n";
					}
				}
			}
			?>			
		</div><!-- End: podPress Widget Accordion -->
		<p class="podpress_widget_settings_row"><?php _e('Show buttons or text?', 'podpress'); ?></p>
		<p><label for="podPressFeedButtons-buttons"><?php _e('Buttons', 'podpress'); ?></label> <input type="radio" <?php echo $buttons; ?> value="buttons" id="podPressFeedButtons-buttons" name="podPressFeedButtons-buttons-or-text" /> <input type="radio" <?php echo $text; ?> value="text" id="podPressFeedButtons-text" name="podPressFeedButtons-buttons-or-text" /> <label for="podPressFeedButtons-text"><?php _e('Text', 'podpress'); ?></label></p>
		<input type="hidden" id="podPressFeedButtons-submit" name="podPressFeedButtons-submit" value="1" />
		<?php
	}

	/* for WP < 2.8 only */
	function podPress_feedButtons ($args) {
		GLOBAL $podPress, $wp_version;
		extract($args);
		$options = get_option('widget_podPressFeedButtons');
		if ( version_compare( $wp_version, '2.2', '>=' ) ) { // the rss.png is in wp_includes since WP 2.2 (this is only necessary until the required  WP version will be changed to e.g 2.3)
			$feed_icon = '<img src="'.get_option('siteurl') . '/' . WPINC . '/images/rss.png" class="podpress_feed_icon" alt="" />';
		} else {
			$feed_icon = apply_filters('podpress_legacy_support_feed_icon', '');
		}
		if(!isset($options['title'])) {
			$options['title'] = __('Podcast Feeds', 'podpress');
		} else {
			$options['title'] = stripslashes($options['title']);
		}
		if(!isset($options['blog'])) {
			$options['blog'] = false;
		}
		if(!isset($options['comments'])) {
			$options['comments'] = false;
		}
		if(!isset($options['entries-atom'])) {
			$options['entries-atom'] = false;
		}
		if(!isset($options['comments-atom'])) {
			$options['comments-atom'] = false;
		}
		if(!isset($options['itunes'])) {
			$options['itunes'] = true;
		}
		if (!isset($options['iprot'])) {
			$options['iprot'] = false;
		}
		if (!isset($options['buttons-or-text'])) {
			$options['buttons-or-text'] = 'buttons';
		}

		echo $before_widget;
		echo $before_title . $options['title'] . $after_title;
		echo '<ul class="podpress_feed_buttons_list">'."\n";
		switch ($options['buttons-or-text']) {
			default:
			case 'buttons' :
				if ($options['itunes']) {
					// for more info: http://www.apple.com/itunes/podcasts/specs.html#linking
					if ($options['iprot'] ) {
						echo ' <li><a href="itpc://'.preg_replace('/^https?:\/\//i', '', $podPress->settings['podcastFeedURL']).'"';
					} else {
						echo ' <li><a href="http://www.itunes.com/podcast?id='.$podPress->settings['iTunes']['FeedID'].'"';
					}
					if ( FALSE == empty($options['itunes_buttonurl']) ) {
						echo ' title="'.__('Subscribe to the Podcast Feed with iTunes', 'podpress').'"><img src="'.$options['itunes_buttonurl'].'" class="podpress_feed_buttons" alt="'.__('Subscribe with iTunes', 'podpress').'" /></a></li>'."\n";
					} else {
						echo ' title="'.__('Subscribe to the Podcast Feed with iTunes', 'podpress').'"><img src="'.podPress_url().'images/itunes.png" class="podpress_feed_buttons" alt="'.__('Subscribe with iTunes', 'podpress').'" /></a></li>'."\n";
					}
				}
				
				// podPress feeds:
				if ( is_array($options['podpressfeeds']) AND FALSE == empty($options['podpressfeeds']) ) {
					foreach ($options['podpressfeeds'] as $feed_slug => $feed_options) {
						if ( 'yes' === $feed_options['use'] AND is_array($podPress->settings['podpress_feeds']) ) {
							foreach ( $podPress->settings['podpress_feeds'] AS $feed ) {
								if ( $feed_slug === $feed['slug'] AND TRUE === $feed['use'] ) {
									if ( FALSE == empty($feed_options['altfeedurl']) ) {
										$feed_link = $feed_options['altfeedurl'];
									} else {
										$feed_link = get_feed_link($feed_slug);
									}
									if ( FALSE == empty($feed['slug']) ) {
										$descr = $feed['descr'];
									} else {
										$descr = __('Subscribe to this Feed with any feed reader', 'podpress');
									}
									if ( FALSE == empty($feed_options['buttonurl']) ) {
										echo '	<li><a href="'.$feed_link.'" title="'.attribute_escape($descr).'"><img src="'.$feed_options['buttonurl'].'" class="podpress_feed_buttons" alt="'.attribute_escape($feed['name']).'" /></a></li>'."\n";
									} else {
										echo '	<li><a href="'.$feed_link.'" title="'.attribute_escape($descr).'">'.$feed_icon.' '.$feed['name'].'</a></li>'."\n";
									}
								}
							}
						}
					}
				}				
				
				if($options['blog']) {
					if ( FALSE === empty($options['posts_altfeedurl']) ) {
						$feedlink = $options['posts_altfeedurl'];
					} else {
						$feedlink = get_bloginfo('rss2_url');
					}
					if ( FALSE == empty($options['posts_buttonurl']) ) {
						echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main RSS Feed with any feed reader', 'podpress').'"><img src="'.$options['posts_buttonurl'].'" class="podpress_feed_buttons" alt="'.__('Subscribe to the RSS Feed', 'podpress').'" /></a></li>'."\n";
					} else {
						echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main RSS Feed with any feed reader', 'podpress').'"><img src="'.podPress_url().'images/feed_button-rss-blog.png" class="podpress_feed_buttons" alt="'.__('Subscribe to the RSS Feed', 'podpress').'" /></a></li>'."\n";
					}
				}
				if($options['comments']) {
					if ( FALSE === empty($options['comments_altfeedurl']) ) {
						$feedlink = $options['comments_altfeedurl'];
					} else {
						$feedlink = get_bloginfo('comments_rss2_url');
					}
					if ( FALSE == empty($options['comments_buttonurl']) ) {
						echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments RSS Feed with any feed reader', 'podpress').'"><img src="'.$options['comments_buttonurl'].'" class="podpress_feed_buttons" alt="'.__('Subscribe to the comments RSS Feed', 'podpress').'" /></a></li>'."\n";
					} else {
						echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments RSS Feed with any feed reader', 'podpress').'"><img src="'.podPress_url().'images/feed_button-rss-comments.png" class="podpress_feed_buttons" alt="'.__('Subscribe to the comments RSS Feed', 'podpress').'" /></a></li>'."\n";
					}
				}
				if($options['entries-atom']) {
					if ( FALSE === empty($options['entries-atom_altfeedurl']) ) {
						$feedlink = $options['entries-atom_altfeedurl'];
					} else {
						$feedlink = get_bloginfo('atom_url');
					}
					if ( FALSE == empty($options['entries-atom_buttonurl']) ) {
						echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main ATOM Feed with any feed reader', 'podpress').'"><img src="'.$options['entries-atom_buttonurl'].'" class="podpress_feed_buttons" alt="'.__('Subscribe to the ATOM Feed', 'podpress').'" /></a></li>'."\n";
					} else {
						echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main ATOM Feed with any feed reader', 'podpress').'"><img src="'.podPress_url().'images/feed_button-atom-blog.png" class="podpress_feed_buttons" alt="'.__('Subscribe to the ATOM Feed', 'podpress').'" /></a></li>'."\n";
					}
				}
				if($options['comments-atom']) {
					if ( FALSE === empty($options['comments-atom_altfeedurl']) ) {
						$feedlink = $options['comments-atom_altfeedurl'];
					} else {
						$feedlink = get_bloginfo('comments_atom_url');
					}
					if ( FALSE == empty($options['comments-atom_buttonurl']) ) {
						echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments ATOM Feed with any feed reader', 'podpress').'"><img src="'.$options['comments-atom_buttonurl'].'" class="podpress_feed_buttons" alt="'.__('Subscribe to the comments ATOM Feed', 'podpress').'" /></a></li>'."\n";
					} else {
						echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments ATOM Feed with any feed reader', 'podpress').'"><img src="'.podPress_url().'images/feed_button-atom-comments.png" class="podpress_feed_buttons" alt="'.__('Subscribe to the comments ATOM Feed', 'podpress').'" /></a></li>'."\n";
					}
				}
				if ( is_array($options['catcast']) AND FALSE == empty($options['catcast']) ) {
					foreach ($options['catcast'] as $cat_id => $catcast_options) {
						if ( 'yes' === $catcast_options['use'] ) {
							if ( FALSE == empty($catcast_options['altfeedurl']) ) {
								$cat_feed_link = $catcast_options['altfeedurl'];
							} else {
								if (TRUE == version_compare($wp_version, '2.9.3','>') ) {
									$cat_feed_link = get_term_feed_link($cat_id);
								} elseif ( TRUE == version_compare($wp_version, '2.9.3','<=') AND TRUE == version_compare($wp_version, '2.4','>') ) {
									$cat_feed_link = get_category_feed_link($cat_id);
								} else {
									$cat_feed_link = get_option('siteurl').'/?feed=rss2&cat='.$cat_id;
								}
							}
							if ( FALSE == empty($catcast_options['buttonurl']) ) {
								echo '	<li><a href="'.$cat_feed_link.'" title="'.__('Subscribe to this Category RSS Feed with any feed reader', 'podpress').'"><img src="'.$catcast_options['buttonurl'].'" class="podpress_feed_buttons" alt="'.attributes_escape(sprintf(__('Category "%1$s" RSS Feed', 'podpress'), get_cat_name($cat_id))).'" /></a></li>'."\n";
							} else {
								echo '	<li><a href="'.$cat_feed_link.'" title="'.__('Subscribe to this Category RSS Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.sprintf(__('Category "%1$s" RSS Feed', 'podpress'), get_cat_name($cat_id)).'</a></li>'."\n";
							}
						}
					}
				}
			break;
			case 'text' :
				if ($options['itunes']) {
					// for more info: http://www.apple.com/itunes/podcasts/specs.html#linking
					if ($options['iprot'] ) {
						echo ' <li><a href="itpc://'.preg_replace('/^https?:\/\//i', '', $podPress->settings['podcastFeedURL']).'"';
					} else {
						echo ' <li><a href="http://www.itunes.com/podcast?id='.$podPress->settings['iTunes']['FeedID'].'"';
					}
					echo ' title="'.__('Subscribe to the Podcast Feed with iTunes', 'podpress').'">'.$feed_icon.' '.__('Subscribe with iTunes', 'podpress').'</a></li>'."\n";
				}
				
				// podPress feeds:
				if ( is_array($options['podpressfeeds']) AND FALSE == empty($options['podpressfeeds']) ) {
					foreach ($options['podpressfeeds'] as $feed_slug => $feed_options) {
						if ( 'yes' === $feed_options['use'] AND is_array($podPress->settings['podpress_feeds']) ) {
							foreach ( $podPress->settings['podpress_feeds'] AS $feed ) {
								if ( $feed_slug === $feed['slug'] AND TRUE === $feed['use'] ) {
									if ( FALSE == empty($feed_options['altfeedurl']) ) {
										$feed_link = $feed_options['altfeedurl'];
									} else {
										$feed_link = get_feed_link($feed_slug);
									}
									if ( FALSE == empty($feed['slug']) ) {
										$descr = $feed['descr'];
									} else {
										$descr = __('Subscribe to this Feed with any feed reader', 'podpress');
									}
									echo '	<li><a href="'.$feed_link.'" title="'.attribute_escape($descr).'">'.$feed_icon.' '.$feed['name'].'</a></li>'."\n";
								}
							}
						}
					}
				}

				if($options['blog']) {
					if ( FALSE === empty($options['posts_altfeedurl']) ) {
						$feedlink = $options['posts_altfeedurl'];
					} else {
						$feedlink = get_bloginfo('rss2_url');
					}
					echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main RSS Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.__('Entries RSS Feed', 'podpress').'</a></li>'."\n";
				}
				if($options['comments']) {
					if ( FALSE === empty($options['comments_altfeedurl']) ) {
						$feedlink = $options['comments_altfeedurl'];
					} else {
						$feedlink = get_bloginfo('comments_rss2_url');
					}
					echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments RSS Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.__('Comments RSS Feed', 'podpress').'</a></li>'."\n";
				}
				if($options['entries-atom']) {
					if ( FALSE === empty($options['entries-atom_altfeedurl']) ) {
						$feedlink = $options['entries-atom_altfeedurl'];
					} else {
						$feedlink = get_bloginfo('atom_url');
					}
					echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main ATOM Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.__('Entries ATOM Feed', 'podpress').'</a></li>'."\n";
				}
				if($options['comments-atom']) {
					if ( FALSE === empty($options['comments-atom_altfeedurl']) ) {
						$feedlink = $options['comments-atom_altfeedurl'];
					} else {
						$feedlink = get_bloginfo('comments_atom_url');
					}
					echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments ATOM Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.__('Comments ATOM Feed', 'podpress').'</a></li>'."\n";
				}
				if ( is_array($options['catcast']) AND FALSE == empty($options['catcast']) ) {
					foreach ($options['catcast'] as $cat_id => $catcast_options) {
						if ( 'yes' === $catcast_options['use'] ) {
							if ( FALSE == empty($catcast_options['altfeedurl']) ) {
								$cat_feed_link = $catcast_options['altfeedurl'];
							} else {
								if (TRUE == version_compare($wp_version, '2.9.3','>') ) {
									$cat_feed_link = get_term_feed_link($cat_id);
								} elseif ( TRUE == version_compare($wp_version, '2.9.3','<=') AND TRUE == version_compare($wp_version, '2.4','>') ) {
									$cat_feed_link = get_category_feed_link($cat_id);
								} else {
									$cat_feed_link = get_option('siteurl').'/?feed=rss2&cat='.$cat_id;
								}
							}
							echo '	<li><a href="'.$cat_feed_link.'" title="'.__('Subscribe to this Category RSS Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.sprintf(__('Category "%1$s" RSS Feed', 'podpress'), get_cat_name($cat_id)).'</a></li>'."\n";
						}
					}
				}
			break;
		}
		echo "</ul>\n";
		echo $after_widget;
	}

	/* for WP < 2.8 only */
	function podPress_xspfPlayer_control() {
		global $blog_id;
		$xspf_width_const_msg = '';
		$xspf_height_const_msg = '';
		$xspf_heightslim_const_msg = '';
		$xspf_width_readonly = '';
		$xspf_height_readonly = '';
		$xspf_heightslim_readonly = '';
		$options = get_option('widget_podPressXspfPlayer');
		$blog_charset = get_bloginfo('charset');

		if ( isset($_POST['podPressXspfPlayer-submit']) ) {
			$options['title'] = htmlspecialchars(strip_tags(trim($_POST['podPressXspfPlayer-title'])), ENT_QUOTES, $blog_charset);
			$options['useSlimPlayer'] = isset($_POST['podPressXspfPlayer-useSlimPlayer']);
			$options['PlayerWidth'] = intval(preg_replace('/[^0-9]/', '',$_POST['podPressXspfPlayer-width'])); // only numeric values are allowed
			$options['PlayerHeight'] = intval(preg_replace('/[^0-9]/', '',$_POST['podPressXspfPlayer-height'])); // only numeric values are allowed
			$options['useSlimPlayer'] = isset($_POST['podPressXspfPlayer-useSlimPlayer']);
			$options['SlimPlayerHeight'] = intval(preg_replace('/[^0-9]/', '',$_POST['podPressXspfPlayer-heightslim'])); // only numeric values are allowed
		}
		
		if ( 150 > intval($options['PlayerWidth']) ) {
			$options['PlayerWidth'] = 150; // min width
		}
		if ( 100 > intval($options['PlayerHeight']) ) {
			$options['PlayerHeight'] = 100; // min height
		}
		if ( 100 < intval($options['SlimPlayerHeight']) ) {
			$options['SlimPlayerHeight'] = 100; // max height slim
		} elseif ( 30 > intval($options['SlimPlayerHeight']) ) {
			$options['SlimPlayerHeight'] = 30; // min height slim
		}
		
		if ( isset($_POST['podPressXspfPlayer-submit']) ) {
			if ( isset($_POST['podPressXspfPlayer-xspf_use_custom_playlist']) ) {
				$options['xspf_use_custom_playlist'] = TRUE;
			} else {
				$options['xspf_use_custom_playlist'] = FALSE;
			}
			$options['xspf_custom_playlist_url'] = clean_url($_POST['podPressXspfPlayer-xspf_custom_playlist_url'], array('http', 'https'), 'db');
			
			update_option('widget_podPressXspfPlayer', $options);
			$updated = true; // So that we don't go through this more than once
		}

		if (!isset($options['title'])) {
			$options['title'] = __('Podcast Player', 'podpress');
		}
		$title = attribute_escape(stripslashes($options['title']));
		$useSlimPlayer = $options['useSlimPlayer'] ? ' checked="checked"' : '';
		?>
		<p><label for="podPressXspfPlayer-title"><?php _e('Title:'); ?></label> <input type="text" id="podPressXspfPlayer-title" name="podPressXspfPlayer-title" value="<?php echo $title; ?>" class="podpress_widget_settings_title" /></p>
		<p><label for="podPressXspfPlayer-width"><?php _e('Player Width:', 'podpress'); ?></label> <input type="text" id="podPressXspfPlayer-width" name="podPressXspfPlayer-width" maxlength="3" value="<?php echo $options['PlayerWidth']; ?>" class="podpress_widget_settings_3digits" /> <?php _e('px', 'podpress'); ?> <?php echo '<span class="nonessential">'.__('(150 <= w < 1000)', 'podpress').'</span>'; ?></p>
		<p><label for="podPressXspfPlayer-height"><?php _e('Player Height:', 'podpress'); ?></label> <input type="text" id="podPressXspfPlayer-height" name="podPressXspfPlayer-height" maxlength="3" value="<?php echo $options['PlayerHeight']; ?>" class="podpress_widget_settings_3digits" /> <?php _e('px', 'podpress'); ?> <?php echo '<span class="nonessential">'.__('(100 <= h < 1000)', 'podpress').'</span>'; ?></p>
		<p><label for="podPressXspfPlayer-useSlimPlayer"><?php _e('Use Slim Player', 'podpress'); ?></label> <input type="checkbox" id="podPressXspfPlayer-useSlimPlayer" name="podPressXspfPlayer-useSlimPlayer"<?php echo $useSlimPlayer; ?> class="checkbox" /></p>
		<p><label for="podPressXspfPlayer-heightslim"><?php _e('Slim Player Height:', 'podpress'); ?></label> <input type="text" id="podPressXspfPlayer-heightslim" name="podPressXspfPlayer-heightslim" maxlength="3" value="<?php echo $options['SlimPlayerHeight']; ?>" class="podpress_widget_settings_3digits" /> <?php _e('px', 'podpress'); ?> <?php echo '<span class="nonessential">'.__('(30 <= h <= 100)', 'podpress').'</span>'; ?></p>
		<?php
		if ( defined('PODPRESS_CUSTOM_XSPF_URL_'.$blog_id) AND '' !== constant('PODPRESS_CUSTOM_XSPF_URL_'.$blog_id) ) {
			$xspf_custom_playlist_url_readonly = ' readonly="readonly"';
			$xspf_custom_playlist_url = attribute_escape(constant('PODPRESS_CUSTOM_XSPF_URL_'.$blog_id));
			$xspf_use_custom_playlist_disabled = ' disabled="disabled"';
			$xspf_use_custom_playlist_checked = ' checked="checked"';
			$xspf_custom_playlist_msg = '<p class="podpress_notice">'.sprintf(__('<strong>Notice:</strong> The custom playlist URL is currently defined via the constant PODPRESS_CUSTOM_XSPF_URL_%1$s and this constant overwrites the custom XSPF playlist settings.', 'podpress'), $blog_id).'</p>';
		} else {
			$xspf_custom_playlist_url_readonly = '';
			$xspf_custom_playlist_url = attribute_escape($options['xspf_custom_playlist_url']);
			$xspf_use_custom_playlist_disabled = '';
			if ( TRUE === $options['xspf_use_custom_playlist'] ) {
				$xspf_use_custom_playlist_checked = ' checked="checked"';
			} else {
				$xspf_use_custom_playlist_checked = '';
			}
			$xspf_custom_playlist_msg = '';
		}
		echo '<p><label for="xspf_use_custom_playlist">'.__('use a custom XSPF playlist:', 'podpress').'</label> <input type="checkbox" name="podPressXspfPlayer-xspf_use_custom_playlist" id="xspf_use_custom_playlist"'.$xspf_use_custom_playlist_checked.$xspf_use_custom_playlist_disabled.' /></p>'."\n";
		echo '<p><label for="xspf_custom_playlist_url">'.__('custom playlist URL:', 'podpress').'</label><br /><input type="text" name="podPressXspfPlayer-xspf_custom_playlist_url" id="xspf_custom_playlist_url" class="podpress_full_width_text_field" size="40" value="'.$xspf_custom_playlist_url.'"'.$xspf_custom_playlist_url_readonly.' /><span class="nonessential">'.__('The custom playlist URL has to be an URL to a playlist which is on the same domain/server as your blog. The files in the playlist can be located some where else.', 'podpress').'</span></p>'.$xspf_custom_playlist_msg."\n";
		if ( TRUE == defined('PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_'.$blog_id) AND TRUE === constant('PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_'.$blog_id) ) { 
			echo '<p class="podpress_notice">'.sprintf(__('<strong>Notice:</strong> PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_%1$s is defined. This widget uses custom skin files. Make sure that there is a custom skin file for these dimensions (see podpress_xspf_config.php).', 'podpress'), $blog_id).'</p>';
		}
		?>
		<input type="hidden" id="podPressXspfPlayer-submit" name="podPressXspfPlayer-submit" value="1" />
		<?php
	}
	
	/* for WP < 2.8 only */
	function podPress_xspfPlayer($args) {
		GLOBAL $podPress, $blog_id;
		extract($args);
		$options = get_option('widget_podPressXspfPlayer');
		if ( !isset($options['title']) ) {
			$options['title'] = __('Podcast Player', 'podpress');
		} else {
			$options['title'] = stripslashes($options['title']);
		}
		if ( !isset($options['useSlimPlayer']) ) {
			$options['useSlimPlayer'] = false;
		}
		if ( 150 > intval($options['PlayerWidth']) ) {
			$options['PlayerWidth'] = 150; // min width
		}
		
		$skin_variables_url = PODPRESS_OPTIONS_URL.'/xspf_options/variables';
		$skin_variables_dir = PODPRESS_OPTIONS_DIR.'/xspf_options/variables';
		$skin_file = PODPRESS_URL.'/podpress_xspfskinfile.php';		
		
		echo $before_widget."\n";
		echo $before_title . $options['title'] . $after_title."\n";
		if ( TRUE === $options['useSlimPlayer'] ) {
			if ( 30 > intval($options['SlimPlayerHeight']) ) {
				$options['SlimPlayerHeight'] = 30; // min height slim
			}
			if ( TRUE == defined('PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_'.$blog_id) AND TRUE == defined('PODPRESS_XSPF_SLIM_SKIN_URL_'.$blog_id) AND TRUE == defined('PODPRESS_XSPF_SLIM_SKIN_DIR_'.$blog_id) AND TRUE == is_readable(constant('PODPRESS_XSPF_SLIM_SKIN_DIR_'.$blog_id).'/skin_'.$blog_id.'_'.$options['PlayerWidth'].'x'.$options['SlimPlayerHeight'].'.xml') ) {
				$skin_file = constant('PODPRESS_XSPF_SLIM_SKIN_URL_'.$blog_id).'/skin_'.$blog_id.'_'.$options['PlayerWidth'].'x'.$options['SlimPlayerHeight'].'.xml';
			}
			$skin_variables_url .= '_slim';
			$skin_variables_dir .= '_slim';
			if ( TRUE === defined('PODPRESS_XSPF_SLIM_USE_CUSTOM_VARIABLES_'.$blog_id) AND TRUE === constant('PODPRESS_XSPF_SLIM_USE_CUSTOM_VARIABLES_'.$blog_id) AND TRUE == is_readable($skin_variables_dir.'/variables_'.$blog_id.'.txt')) { 
				$variables = '&skin_url='.$skin_file.'&loadurl='.$skin_variables_url.'/variables_'.$blog_id.'.txt';
			} else {
				$variables = '&skin_url='.$skin_file.'&autoload=true&autoplay=false&loaded=true';
			}
			$data_string = PODPRESS_URL.'/players/xspf_jukebox/xspf_jukebox.swf?playlist_url='.(PODPRESS_URL.'/podpress_xspfplaylist.php').$variables;
			$data_string = htmlspecialchars($data_string);
			echo '<object type="application/x-shockwave-flash" width="'.$options['PlayerWidth'].'" height="'.$options['PlayerHeight'].'" id="podpress_xspf_player_slim" data="'.$data_string.'">'."\n";
		} else {
			if ( 100 > intval($options['PlayerHeight']) ) {
				$options['PlayerHeight'] = 100; // min height
			}
			if ( TRUE == defined('PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_'.$blog_id) AND TRUE == defined('PODPRESS_XSPF_SKIN_URL_'.$blog_id) AND TRUE == defined('PODPRESS_XSPF_SKIN_DIR_'.$blog_id) AND TRUE == is_readable(constant('PODPRESS_XSPF_SKIN_DIR_'.$blog_id).'/skin_'.$blog_id.'_'.$options['PlayerWidth'].'x'.$options['PlayerHeight'].'.xml') ) {
				$skin_file = constant('PODPRESS_XSPF_SKIN_URL_'.$blog_id).'/skin_'.$blog_id.'_'.$options['PlayerWidth'].'x'.$options['PlayerHeight'].'.xml';
			}
			if ( TRUE === defined('PODPRESS_XSPF_USE_CUSTOM_VARIABLES_'.$blog_id) AND TRUE === constant('PODPRESS_XSPF_USE_CUSTOM_VARIABLES_'.$blog_id) AND TRUE == is_readable($skin_variables_dir.'/variables_'.$blog_id.'.txt')) {
				$variables = '&skin_url='.$skin_file.'&loadurl='.$skin_variables_url.'/variables_'.$blog_id.'.txt';
			} else {
				$variables = '&skin_url='.$skin_file.'&autoload=true&autoplay=false&loaded=true';
			}
			$data_string = PODPRESS_URL.'/players/xspf_jukebox/xspf_jukebox.swf?playlist_url='.(PODPRESS_URL.'/podpress_xspfplaylist.php').$variables;
			$data_string = htmlspecialchars($data_string);
			echo '<object type="application/x-shockwave-flash" width="'.$options['PlayerWidth'].'" height="'.$options['PlayerHeight'].'" id="podpress_xspf_player" data="'.$data_string.'">'."\n";
		}
		echo '	<param name="movie" value="'.$data_string.'" />'."\n";
		if ( defined('PODPRESS_XSPF_BACKGROUND_COLOR_'.$blog_id) AND '' !== constant('PODPRESS_XSPF_BACKGROUND_COLOR_'.$blog_id) ) {
			echo '	<param name="bgcolor" value="#'.constant('PODPRESS_XSPF_BACKGROUND_COLOR_'.$blog_id).'" />'."\n";
		} else {
			echo '	<param name="bgcolor" value="#FFFFFF" />'."\n";
		}
		echo '</object>'."\n";
		echo $after_widget;
	}

	
	/**
	* podPress Feed Buttons Widget Class
	* since podPress v8.8.7 beta 2
	* for WP > = 2.8
	*/
	if ( TRUE == version_compare($wp_version, '2.8', '>=') ) {
		class podpress_feedbuttons extends WP_Widget {
			/** constructor */
			function podpress_feedbuttons() {
				$widget_ops = array(
					'classname' => 'podpress_feedbuttons', 
					'description' => __('Shows buttons for the podcast feeds in the sidebar', 'podpress')
				);
				$control_ops = array('width' => 400, 'height' => 300);
				
				parent::WP_Widget(false, $name = __('podPress - Feed Buttons','podpress'), $widget_ops, $control_ops);
			}

			/** @see WP_Widget::widget */
			function widget($args, $instance) {
				GLOBAL $podPress, $wp_version;
				extract( $args );
				$title = apply_filters('widget_title', $instance['title']);
				$feed_icon = '<img src="'.get_option('siteurl') . '/' . WPINC . '/images/rss.png" class="podpress_feed_icon" alt="" />';
				echo $before_widget;
				echo $before_title . $title . $after_title;
				echo '<ul class="podpress_feed_buttons_list">'."\n";
				switch ($instance['buttons-or-text']) {
					default:
					case 'buttons' :
						if ($instance['itunes']) {
							// for more info: http://www.apple.com/itunes/podcasts/specs.html#linking
							if ($instance['iprot'] ) {
								echo ' <li><a href="itpc://'.preg_replace('/^https?:\/\//i', '', $podPress->settings['podcastFeedURL']).'"';
							} else {
								echo ' <li><a href="http://www.itunes.com/podcast?id='.$podPress->settings['iTunes']['FeedID'].'"';
							}
							if ( FALSE == empty($instance['itunes_buttonurl']) ) {
								echo ' title="'.__('Subscribe to the Podcast Feed with iTunes', 'podpress').'"><img src="'.$instance['itunes_buttonurl'].'" class="podpress_feed_buttons" alt="'.__('Subscribe with iTunes', 'podpress').'" /></a></li>'."\n";
							} else {
								echo ' title="'.__('Subscribe to the Podcast Feed with iTunes', 'podpress').'"><img src="'.podPress_url().'images/itunes.png" class="podpress_feed_buttons" alt="'.__('Subscribe with iTunes', 'podpress').'" /></a></li>'."\n";
							}
						}
						
						// podPress feeds:
						if ( is_array($instance['podpressfeeds']) AND FALSE == empty($instance['podpressfeeds']) ) {
							foreach ($instance['podpressfeeds'] as $feed_slug => $feed_options) {
								if ( 'yes' === $feed_options['use'] AND is_array($podPress->settings['podpress_feeds']) ) {
									foreach ( $podPress->settings['podpress_feeds'] AS $feed ) {
										if ( $feed_slug === $feed['slug'] AND TRUE === $feed['use'] ) {
											if ( FALSE == empty($feed_options['altfeedurl']) ) {
												$feed_link = $feed_options['altfeedurl'];
											} else {
												$feed_link = get_feed_link($feed_slug);
											}
											if ( FALSE == empty($feed['slug']) ) {
												$descr = $feed['descr'];
											} else {
												$descr = __('Subscribe to this Feed with any feed reader', 'podpress');
											}
											if ( FALSE == empty($feed_options['buttonurl']) ) {
												echo '	<li><a href="'.$feed_link.'" title="'.$descr.'"><img src="'.$feed_options['buttonurl'].'" class="podpress_feed_buttons" alt="'.$feed['name'].'" /></a></li>'."\n";
											} else {
												echo '	<li><a href="'.$feed_link.'" title="'.$descr.'">'.$feed_icon.' '.$feed['name'].'</a></li>'."\n";
											}
										}
									}
								}
							}
						}

						if($instance['posts']) {
							if ( FALSE == empty($instance['posts_altfeedurl']) ) {
								$feedlink = $instance['posts_altfeedurl'];
							} else {
								$feedlink = get_bloginfo('rss2_url');
							}
							if ( FALSE == empty($instance['posts_buttonurl']) ) {
								echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main RSS Feed with any feed reader', 'podpress').'"><img src="'.$instance['posts_buttonurl'].'" class="podpress_feed_buttons" alt="'.__('Subscribe to the RSS Feed', 'podpress').'" /></a></li>'."\n";
							} else {
								echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main RSS Feed with any feed reader', 'podpress').'"><img src="'.podPress_url().'images/feed_button-rss-blog.png" class="podpress_feed_buttons" alt="'.__('Subscribe to the RSS Feed', 'podpress').'" /></a></li>'."\n";
							}
						}
						if($instance['comments']) {
							if ( FALSE == empty($instance['comments_altfeedurl']) ) {
								$feedlink = $instance['comments_altfeedurl'];
							} else {
								$feedlink = get_bloginfo('comments_rss2_url');
							}
							if ( FALSE == empty($instance['comments_buttonurl']) ) {
								echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments RSS Feed with any feed reader', 'podpress').'"><img src="'.$instance['comments_buttonurl'].'" class="podpress_feed_buttons" alt="'.__('Subscribe to the comments RSS Feed', 'podpress').'" /></a></li>'."\n";
							} else {
								echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments RSS Feed with any feed reader', 'podpress').'"><img src="'.podPress_url().'images/feed_button-rss-comments.png" class="podpress_feed_buttons" alt="'.__('Subscribe to the comments RSS Feed', 'podpress').'" /></a></li>'."\n";
							}
						}
						if($instance['entries-atom']) {
							if ( FALSE == empty($instance['entries-atom_altfeedurl']) ) {
								$feedlink = $instance['entries-atom_altfeedurl'];
							} else {
								$feedlink = get_bloginfo('atom_url');
							}
							if ( FALSE == empty($instance['entries-atom_buttonurl']) ) {
								echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main ATOM Feed with any feed reader', 'podpress').'"><img src="'.$instance['entries-atom_buttonurl'].'" class="podpress_feed_buttons" alt="'.__('Subscribe to the ATOM Feed', 'podpress').'" /></a></li>'."\n";
							} else {
								echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main ATOM Feed with any feed reader', 'podpress').'"><img src="'.podPress_url().'images/feed_button-atom-blog.png" class="podpress_feed_buttons" alt="'.__('Subscribe to the ATOM Feed', 'podpress').'" /></a></li>'."\n";
							}
						}
						if($instance['comments-atom']) {
							if ( FALSE === empty($instance['comments-atom_altfeedurl']) ) {
								$feedlink = $instance['comments-atom_altfeedurl'];
							} else {
								$feedlink = get_bloginfo('comments_atom_url');
							}
							if ( FALSE == empty($instance['comments-atom_buttonurl']) ) {
								echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments ATOM Feed with any feed reader', 'podpress').'"><img src="'.$instance['comments-atom_buttonurl'].'" class="podpress_feed_buttons" alt="'.__('Subscribe to the comments ATOM Feed', 'podpress').'" /></a></li>'."\n";
							} else {
								echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments ATOM Feed with any feed reader', 'podpress').'"><img src="'.podPress_url().'images/feed_button-atom-comments.png" class="podpress_feed_buttons" alt="'.__('Subscribe to the comments ATOM Feed', 'podpress').'" /></a></li>'."\n";
							}
						}
						if ( is_array($instance['catcast']) AND FALSE == empty($instance['catcast']) ) {
							foreach ($instance['catcast'] as $cat_id => $catcast_options) {
								if ( 'yes' === $catcast_options['use'] ) {
									if ( FALSE == empty($catcast_options['altfeedurl']) ) {
										$cat_feed_link = $catcast_options['altfeedurl'];
									} else {
										if (TRUE == version_compare($wp_version, '2.9.3','>') ) {
											$cat_feed_link = get_term_feed_link($cat_id);
										} else {
											$cat_feed_link = get_category_feed_link($cat_id);
										}
									}
									if ( FALSE == empty($catcast_options['buttonurl']) ) {
										echo '	<li><a href="'.$cat_feed_link.'" title="'.__('Subscribe to this Category RSS Feed with any feed reader', 'podpress').'"><img src="'.$catcast_options['buttonurl'].'" class="podpress_feed_buttons" alt="'.esc_attr(sprintf(__('Category "%1$s" RSS Feed', 'podpress'), get_cat_name($cat_id))).'" /></a></li>'."\n";
									} else {
										echo '	<li><a href="'.$cat_feed_link.'" title="'.__('Subscribe to this Category RSS Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.sprintf(__('Category "%1$s" RSS Feed', 'podpress'), get_cat_name($cat_id)).'</a></li>'."\n";
									}
								}
							}
						}
					break;
					case 'text' :
						if ($instance['itunes']) {
							// for more info: http://www.apple.com/itunes/podcasts/specs.html#linking
							if ($instance['iprot'] ) {
								echo ' <li><a href="itpc://'.preg_replace('/^https?:\/\//i', '', $podPress->settings['podcastFeedURL']).'"';
							} else {
								echo ' <li><a href="http://www.itunes.com/podcast?id='.$podPress->settings['iTunes']['FeedID'].'"';
							}
							echo ' title="'.__('Subscribe to the Podcast Feed with iTunes', 'podpress').'">'.$feed_icon.' '.__('Subscribe with iTunes', 'podpress').'</a></li>'."\n";
						}
						// podPress feeds:
						if ( is_array($instance['podpressfeeds']) AND FALSE == empty($instance['podpressfeeds']) ) {
							foreach ($instance['podpressfeeds'] as $feed_slug => $feed_options) {
								if ( 'yes' === $feed_options['use'] AND is_array($podPress->settings['podpress_feeds']) ) {
									foreach ( $podPress->settings['podpress_feeds'] AS $feed ) {
										if ( $feed_slug === $feed['slug'] AND TRUE === $feed['use'] ) {
											if ( FALSE == empty($feed_options['altfeedurl']) ) {
												$feed_link = $feed_options['altfeedurl'];
											} else {
												$feed_link = get_feed_link($feed_slug);
											}
											if ( FALSE == empty($feed['slug']) ) {
												$descr = $feed['descr'];
											} else {
												$descr = __('Subscribe to this Feed with any feed reader', 'podpress');
											}
											echo '	<li><a href="'.$feed_link.'" title="'.$descr.'">'.$feed_icon.' '.$feed['name'].'</a></li>'."\n";
										}
									}
								}
							}
						}
						if($instance['posts']) {
							if ( FALSE == empty($instance['posts_altfeedurl']) ) {
								$feedlink = $instance['posts_altfeedurl'];
							} else {
								$feedlink = get_bloginfo('rss2_url');
							}
							echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main RSS Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.__('Entries RSS Feed', 'podpress').'</a></li>'."\n";
						}
						if($instance['comments']) {
							if ( FALSE == empty($instance['comments_altfeedurl']) ) {
								$feedlink = $instance['comments_altfeedurl'];
							} else {
								$feedlink = get_bloginfo('comments_rss2_url');
							}
							echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments RSS Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.__('Comments RSS Feed', 'podpress').'</a></li>'."\n";
						}
						if($instance['entries-atom']) {
							if ( FALSE == empty($instance['entries-atom_altfeedurl']) ) {
								$feedlink = $instance['entries-atom_altfeedurl'];
							} else {
								$feedlink = get_bloginfo('atom_url');
							}
							echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the main ATOM Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.__('Entries ATOM Feed', 'podpress').'</a></li>'."\n";
						}
						if($instance['comments-atom']) {
							if ( FALSE === empty($instance['comments-atom_altfeedurl']) ) {
								$feedlink = $instance['comments-atom_altfeedurl'];
							} else {
								$feedlink = get_bloginfo('comments_atom_url');
							}
							echo '	<li><a href="'.$feedlink.'" title="'.__('Subscribe to the comments ATOM Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.__('Comments ATOM Feed', 'podpress').'</a></li>'."\n";
						}
						if ( is_array($instance['catcast']) AND FALSE == empty($instance['catcast']) ) {
							foreach ($instance['catcast'] as $cat_id => $catcast_options) {
								if ( 'yes' === $catcast_options['use'] ) {
									if ( FALSE == empty($catcast_options['altfeedurl']) ) {
										$cat_feed_link = $catcast_options['altfeedurl'];
									} else {
										if (TRUE == version_compare($wp_version, '2.9.3','>') ) {
											$cat_feed_link = get_term_feed_link($cat_id);
										} else {
											$cat_feed_link = get_category_feed_link($cat_id);
										}
									}
									echo '	<li><a href="'.$cat_feed_link.'" title="'.__('Subscribe to this Category RSS Feed with any feed reader', 'podpress').'">'.$feed_icon.' '.sprintf(__('Category "%1$s" RSS Feed', 'podpress'), get_cat_name($cat_id)).'</a></li>'."\n";
								}
							}
						}
					break;
				}
				echo "</ul>\n";
				echo $after_widget;
			}

			/** @see WP_Widget::update */
			function update($new_instance, $old_instance) {
				$blog_charset = get_bloginfo('charset');
				$instance = $old_instance;
				$instance['title'] = htmlspecialchars(strip_tags(trim($new_instance['title'])), ENT_QUOTES, $blog_charset);
				$instance['posts'] = $new_instance['posts'];
				$instance['comments'] = $new_instance['comments'];
				$instance['entries-atom'] = $new_instance['entries-atom'];
				$instance['comments-atom'] = $new_instance['comments-atom'];
				$instance['posts_buttonurl'] =  clean_url($new_instance['posts_buttonurl'], array('http', 'https'), 'db');
				$instance['comments_buttonurl'] =  clean_url($new_instance['comments_buttonurl'], array('http', 'https'), 'db');
				$instance['entries-atom_buttonurl'] =  clean_url($new_instance['entries-atom_buttonurl'], array('http', 'https'), 'db');
				$instance['comments-atom_buttonurl'] =  clean_url($new_instance['comments-atom_buttonurl'], array('http', 'https'), 'db');
				$instance['posts_altfeedurl'] =  clean_url($new_instance['posts_altfeedurl'], array('http', 'https'), 'db');
				$instance['comments_altfeedurl'] =  clean_url($new_instance['comments_altfeedurl'], array('http', 'https'), 'db');
				$instance['entries-atom_altfeedurl'] =  clean_url($new_instance['entries-atom_altfeedurl'], array('http', 'https'), 'db');
				$instance['comments-atom_altfeedurl'] =  clean_url($new_instance['comments-atom_altfeedurl'], array('http', 'https'), 'db');
				$instance['itunes'] = $new_instance['itunes'];
				$instance['itunes_buttonurl'] =  clean_url($new_instance['itunes_buttonurl'], array('http', 'https'), 'db');
				$instance['iprot'] = $new_instance['iprot'];
				$instance['buttons-or-text'] = $new_instance['buttons-or-text'];
				// CategoryCasting Feeds:
				if ( is_array($new_instance['catcast']) ) {
					foreach ( $new_instance['catcast'] as $cat_id => $feed_options ) {
						if ( 'yes' === $feed_options['use'] ) {
							$instance['catcast'][$cat_id]['use'] = 'yes';
						} else {
							$instance['catcast'][$cat_id]['use'] = 'no';
						}
						$instance['catcast'][$cat_id]['buttonurl'] = clean_url($feed_options['buttonurl'], array('http', 'https'), 'db');
						$instance['catcast'][$cat_id]['altfeedurl'] = clean_url($feed_options['altfeedurl'], array('http', 'https'), 'db');
					}
				}
				// podPress Feeds:
				if ( is_array($new_instance['podpressfeeds']) ) {
					foreach ( $new_instance['podpressfeeds'] as $feed_slug => $feed_options ) {
						if ( 'yes' === $feed_options['use'] ) {
							$instance['podpressfeeds'][$feed_slug]['use'] = 'yes';
						} else {
							$instance['podpressfeeds'][$feed_slug]['use'] = 'no';
						}
						$instance['podpressfeeds'][$feed_slug]['button'] = $feed_options['button'];
						if ( 'custom' === $feed_options['button'] ) {
							$instance['podpressfeeds'][$feed_slug]['buttonurl'] = clean_url($feed_options['buttonurl'], array('http', 'https'), 'db');
						} else {
							$instance['podpressfeeds'][$feed_slug]['buttonurl'] = PODPRESS_URL.'/images/'.$feed_options['button'];
						}
						$instance['podpressfeeds'][$feed_slug]['altfeedurl'] = clean_url($feed_options['altfeedurl'], array('http', 'https'), 'db');
					}
				}
				
				// delete the old widget settings
				$old_widget_options = get_option('widget_podPressFeedButtons');
				if ( FALSE !== $old_widget_options ) {
					delete_option('widget_podPressFeedButtons');
				}
				return $instance;
			}

			/** @see WP_Widget::form */
			function form($instance) {
				GLOBAL $podPress, $wpdb;
				
				// take over the old widget settings (of podPress 8.8.5.x widgets)
				$old_widget_options = get_option('widget_podPressFeedButtons');
				if ( TRUE == is_array($old_widget_options) AND FALSE == empty($old_widget_options) ) {
					$instance['title'] = $old_widget_options['title'];
					$instance['posts'] = $old_widget_options['blog'];
					$instance['comments'] = $old_widget_options['comments'];
					$instance['entries-atom'] = $old_widget_options['entries-atom'];
					$instance['comments-atom'] = $old_widget_options['comments-atom'];
					$instance['itunes'] = $old_widget_options['itunes'];
					$instance['iprot'] = $old_widget_options['iprot'];
					$instance['buttons-or-text'] = $old_widget_options['buttons-or-text'];					
				}
				
				// first time use - default settings
				if (!isset($instance['posts'])) {
					$instance['posts'] = false;
				}
				if (!isset($instance['comments'])) {
					$instance['comments'] = false;
				}
				if (!isset($instance['entries-atom'])) {
					$instance['entries-atom'] = false;
				}
				if (!isset($instance['comments-atom'])) {
					$instance['comments-atom'] = false;
				}
				if (!isset($instance['itunes'])) {
					$instance['itunes'] = true;
				}
				if (!isset($instance['iprot'])) {
					$instance['iprot'] = false;
				}
				if (!isset($instance['buttons-or-text'])) {
					$instance['buttons-or-text'] = 'buttons';
				}
				if (!isset($instance['itunes_buttonurl'])) {
					$instance['itunes_buttonurl'] = PODPRESS_URL.'/images/itunes.png';
				}
				if (!isset($instance['posts_buttonurl'])) {
					$instance['posts_buttonurl'] = PODPRESS_URL.'/images/feed_button-rss-blog.png';
				}
				if (!isset($instance['comments_buttonurl'])) {
					$instance['comments_buttonurl'] = PODPRESS_URL.'/images/feed_button-rss-comments.png';
				}
				if (!isset($instance['entries-atom_buttonurl'])) {
					$instance['entries-atom_buttonurl'] = PODPRESS_URL.'/images/feed_button-atom-blog.png';
				}
				if (!isset($instance['comments-atom_buttonurl'])) {
					$instance['comments-atom_buttonurl'] = PODPRESS_URL.'/images/feed_button-atom-comments.png';
				}
				
				$blog = $instance['posts'] ? 'checked="checked"' : '';
				$comments = $instance['comments'] ? 'checked="checked"' : '';
				$entries_atom = $instance['entries-atom'] ? 'checked="checked"' : '';
				$comments_atom = $instance['comments-atom'] ? 'checked="checked"' : '';
				$itunes = $instance['itunes'] ? 'checked="checked"' : '';
				$iprot = $instance['iprot'] ? 'checked="checked"' :'';
				if ( 'text' == $instance['buttons-or-text'] ) {
					$text = 'checked="checked"';
					$buttons = '';
				} else {
					$text = '';
					$buttons = 'checked="checked"';
				}
				
				if(!isset($instance['title'])) {
					$instance['title'] = __('Podcast Feeds', 'podpress');
				}
				$title = esc_attr($instance['title']);
				?>
				<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'podpress'); ?></label> <input class="podpress_widget_settings_title" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
				<p><?php _e('Show the buttons for the following feeds:', 'podpress'); ?></p>

				<div id="<?php echo $this->get_field_id('podpress_widget_accordion'); ?>" class="podpress_widget_accordion"><!-- Begin: podPress Widget Accordion -->
				<h5><a href=""><?php _e('iTunes Button', 'podpress'); ?></a></h5>
				<div>
					<input type="checkbox" <?php echo $itunes; ?> id="<?php echo $this->get_field_id('itunes'); ?>" name="<?php echo $this->get_field_name('itunes'); ?>" /> <label for="<?php echo $this->get_field_id('itunes'); ?>"><?php printf(__('Show %1$s button', 'podpress'), __('iTunes', 'podpress')); ?></label><br />
					<input type="checkbox" <?php echo $iprot; ?> id="<?php echo $this->get_field_id('iprot'); ?>" name="<?php echo $this->get_field_name('iprot'); ?>" /> <label for="<?php echo $this->get_field_id('iprot'); ?>"><?php _e('Use iTunes protocol for URL', 'podpress'); ?> <?php _e('(itpc://)', 'podpress'); ?></label><br />
					<span class="nonessential"><?php _e('The user subscribes immediatly with the click. Otherwise the iTunes Store page of the podcast will be displayed first and the user can subscribe manually.', 'podpress'); ?></span><br />
					<label for="<?php echo $this->get_field_id('itunes_buttonurl'); ?>"><?php _e('Button URL:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('itunes_buttonurl'); ?>" name="<?php echo $this->get_field_name('itunes_buttonurl'); ?>" class="widefat" value="<?php echo $instance['itunes_buttonurl']; ?>" />
				</div>
				<?php
				$feedbuttons = podpress_get_feed_buttons();
				if ( is_array($podPress->settings['podpress_feeds']) AND FALSE == empty($podPress->settings['podpress_feeds']) ) {
					foreach ($podPress->settings['podpress_feeds'] as $feed) {
						if ( TRUE === $feed['use'] AND FALSE == empty($feed['slug']) ) {
							if ( FALSE == empty($feed['descr']) ) {
								$descr = '<br /><span class="nonessential">'.$feed['descr'].'</span>';
							} else {
								$descr = '';
							}
							// take over the old widget settings (of podPress 8.8.5.x widgets)
							if ( TRUE == is_array($old_widget_options) AND FALSE == empty($old_widget_options) ) {
								Switch ($feed['slug']) {
									case 'podcast' :
									case 'enhancedpodcast' :
									case 'torrent' :
										$instance['podpressfeeds'][$feed['slug']]['use'] = $old_widget_options[$feed['slug']];
										if ( 'podcast' === $feed['slug'] ) {
											$instance['podpressfeeds'][$feed['slug']]['button'] = 'feed_button-rss-'.$feed['slug'].'.png';
										} else {
											$instance['podpressfeeds'][$feed['slug']]['button'] = 'feed_button-'.$feed['slug'].'.png';
										}
									break;
								}
							}
							if ( TRUE == isset($instance['podpressfeeds'][$feed['slug']]['use']) AND 'yes' == $instance['podpressfeeds'][$feed['slug']]['use'] ) {
								$podpressfeed_checked = ' checked="checked"';
							} else {
								$podpressfeed_checked = '';
							}
							echo '<h5><a href="">'.$feed['name'].'</a></h5>'."\n";
							echo '<div class="podpress_widget_settings_row_div">'."\n";
							echo '<input type="checkbox"'.$podpressfeed_checked.' id="'.$this->get_field_id($feed['slug'].'_use').'" name="'.$this->get_field_name('podpressfeeds').'['.$feed['slug'].'][use]" value="yes" /> <label for="'.$this->get_field_id($feed['slug'].'_use').'">'.sprintf(__('Show %1$s button', 'podpress'), $feed['name']).'</label>'."\n";
							echo $descr."\n";
							echo '<br />'.__('Select a feed button:', 'podpress').'<br />'."\n";
							echo '<span class="podpress_feedbuttonsselectbox">'."\n";
							$id_base = $this->get_field_id($feed['slug'].'_'.$feed['button']);
							$i=0;
							$feedbutton_checked_nr = 0;
							foreach ($feedbuttons as $feedbutton) {
								if ( TRUE == isset($instance['podpressfeeds'][$feed['slug']]['button']) AND $feedbutton == $instance['podpressfeeds'][$feed['slug']]['button'] ) {
									$feedbutton_checked_nr = $i;
								}
								$i++;
							}
							$i=0;
							foreach ($feedbuttons as $feedbutton) {
								if ( $i == $feedbutton_checked_nr ) {
									$feedbutton_checked = ' checked="checked"';
								} else {
									$feedbutton_checked = '';
								}
								echo '<input type="radio" name="'.$this->get_field_name('podpressfeeds').'['.$feed['slug'].'][button]" id="'.$id_base.''.$i.'" value="'.$feedbutton.'"'.$feedbutton_checked.' /> <label for="'.$id_base.''.$i.'"><img src="'.PODPRESS_URL.'/images/'.$feedbutton.'" alt="'.$feedbutton.'" title="'.$feedbutton.'" /></label><br />'."\n";
								$i++;
							}
							if ( TRUE == isset($instance['podpressfeeds'][$feed['slug']]['button']) AND 'custom' == $instance['podpressfeeds'][$feed['slug']]['button'] ) {
								echo '<input type="radio" name="'.$this->get_field_name('podpressfeeds').'['.$feed['slug'].'][button]" id="'.$id_base.''.$i.'" value="custom" checked="checked" /> <input type="text" id="'.$this->get_field_id($feed['slug'].'_'.$feed['custombuttonurl']).'" name="'.$this->get_field_name('podpressfeeds').'['.$feed['slug'].'][buttonurl]" class="widefat podpress_customfeedbuttonurl" value="'.$instance['podpressfeeds'][$feed['slug']]['buttonurl'].'" />'."\n";
							} else {
								echo '<label for="'.$this->get_field_id($feed['slug'].'_'.$feed['custombuttonurl']).'"><input type="radio" name="'.$this->get_field_name('podpressfeeds').'['.$feed['slug'].'][button]" id="'.$id_base.''.$i.'" value="custom" /></label> <input type="text" id="'.$this->get_field_id($feed['slug'].'_'.$feed['custombuttonurl']).'" name="'.$this->get_field_name('podpressfeeds').'['.$feed['slug'].'][buttonurl]" class="widefat podpress_customfeedbuttonurl" value="" />'."\n";
							}
							echo '</span>'."\n";
							echo '<label for="'.$this->get_field_id($feed['slug'].'_altfeedurl').'">'.__('Alternative Feed URL:', 'podpress').'</label> <input type="text" id="'.$this->get_field_id($feed['slug'].'_altfeedurl').'" name="'.$this->get_field_name('podpressfeeds').'['.$feed['slug'].'][altfeedurl]" class="widefat" value="'.$instance['podpressfeeds'][$feed['slug']]['altfeedurl'].'" />'."\n";
							echo '</div>'."\n";	
						}
					}
				}
				?>
				<h5><a href=""><?php _e('Entries RSS Feed', 'podpress'); ?></a></h5>
				<div>
				<input type="checkbox" <?php echo $blog; ?> id="<?php echo $this->get_field_id('posts'); ?>" name="<?php echo $this->get_field_name('posts'); ?>" /> <label for="<?php echo $this->get_field_id('posts'); ?>"><?php printf(__('Show %1$s button', 'podpress'), __('Entries RSS Feed', 'podpress')); ?></label><br />
				<label for="<?php echo $this->get_field_id('posts_buttonurl'); ?>"><?php _e('Button URL:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('posts_buttonurl'); ?>" name="<?php echo $this->get_field_name('posts_buttonurl'); ?>" class="widefat" value="<?php echo $instance['posts_buttonurl']; ?>" /><br />
				<label for="<?php echo $this->get_field_id('posts_altfeedurl'); ?>"><?php _e('Alternative Feed URL:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('posts_altfeedurl'); ?>" name="<?php echo $this->get_field_name('posts_altfeedurl'); ?>" class="widefat" value="<?php echo $instance['posts_altfeedurl']; ?>" />
				</div>
				<h5><a href=""><?php _e('Comments RSS Feed', 'podpress'); ?></a></h5>
				<div>
				<input type="checkbox" <?php echo $comments; ?> id="<?php echo $this->get_field_id('comments'); ?>" name="<?php echo $this->get_field_name('comments'); ?>" /> <label for="<?php echo $this->get_field_id('comments'); ?>"><?php printf(__('Show %1$s button', 'podpress'), __('Comments RSS Feed', 'podpress')); ?></label><br />
				<label for="<?php echo $this->get_field_id('comments_buttonurl'); ?>"><?php _e('Button URL:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('comments_buttonurl'); ?>" name="<?php echo $this->get_field_name('comments_buttonurl'); ?>" class="widefat" value="<?php echo $instance['comments_buttonurl']; ?>" /><br />
				<label for="<?php echo $this->get_field_id('comments_altfeedurl'); ?>"><?php _e('Alternative Feed URL:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('comments_altfeedurl'); ?>" name="<?php echo $this->get_field_name('comments_altfeedurl'); ?>" class="widefat" value="<?php echo $instance['comments_altfeedurl']; ?>" />
				</div>
				<h5><a href=""><?php _e('Entries ATOM Feed', 'podpress'); ?></a></h5>
				<div>
				<input type="checkbox" <?php echo $entries_atom; ?> id="<?php echo $this->get_field_id('entries-atom'); ?>" name="<?php echo $this->get_field_name('entries-atom'); ?>" /> <label for="<?php echo $this->get_field_id('entries-atom'); ?>"><?php printf(__('Show %1$s button', 'podpress'), __('Entries ATOM Feed', 'podpress')); ?></label><br />
				<label for="<?php echo $this->get_field_id('entries-atom_buttonurl'); ?>"><?php _e('Button URL:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('entries-atom_buttonurl'); ?>" name="<?php echo $this->get_field_name('entries-atom_buttonurl'); ?>" class="widefat" value="<?php echo $instance['entries-atom_buttonurl']; ?>" /><br />
				<label for="<?php echo $this->get_field_id('entries-atom_altfeedurl'); ?>"><?php _e('Alternative Feed URL:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('entries-atom_altfeedurl'); ?>" name="<?php echo $this->get_field_name('entries-atom_altfeedurl'); ?>" class="widefat" value="<?php echo $instance['entries-atom_altfeedurl']; ?>" />
				</div>
				<h5><a href=""><?php _e('Comments ATOM Feed', 'podpress'); ?></a></h5>
				<div>
				<input type="checkbox" <?php echo $comments_atom; ?> id="<?php echo $this->get_field_id('comments-atom'); ?>" name="<?php echo $this->get_field_name('comments-atom'); ?>" /> <label for="<?php echo $this->get_field_id('comments-atom'); ?>"><?php printf(__('Show %1$s button', 'podpress'), __('Comments ATOM Feed', 'podpress')); ?></label><br />
				<label for="<?php echo $this->get_field_id('comments-atom_buttonurl'); ?>"><?php _e('Button URL:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('comments-atom_buttonurl'); ?>" name="<?php echo $this->get_field_name('comments-atom_buttonurl'); ?>" class="widefat" value="<?php echo $instance['comments-atom_buttonurl']; ?>" /><br />
				<label for="<?php echo $this->get_field_id('comments-atom_altfeedurl'); ?>"><?php _e('Alternative Feed URL:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('comments-atom_altfeedurl'); ?>" name="<?php echo $this->get_field_name('comments-atom_altfeedurl'); ?>" class="widefat" value="<?php echo $instance['comments-atom_altfeedurl']; ?>" />
				</div>
				<?php
				$query_string = 'SELECT option_name, option_value FROM '.$wpdb->prefix.'options WHERE INSTR(option_name, "podPress_category_")';
				$category_feeds = $wpdb->get_results($query_string);			
				if ( isset($category_feeds) AND FALSE == empty($category_feeds) ) {
					foreach ($category_feeds as $feed_options) {
						$feed = maybe_unserialize($feed_options->option_value);
						if ( isset($feed['categoryCasting']) AND 'true' == $feed['categoryCasting'] ) {
							$cat_id = end(explode('_', $feed_options->option_name));
							$checked = $instance['catcast'][$cat_id]['use'] == 'yes' ? 'checked="checked"' :'';
							echo '<h5><a href="">'.sprintf(__('Category "%1$s" RSS Feed', 'podpress'), get_cat_name($cat_id)).'</a></h5>'."\n";
							echo '<div>'."\n";
							echo '<input type="checkbox" '.$checked.' id="'.$this->get_field_id('catcast_'.$cat_id.'_use').'" name="'.$this->get_field_name('catcast').'['.$cat_id.'][use]" value="yes" /> <label for="'.$this->get_field_id('catcast_'.$cat_id.'_use').'">'.sprintf( __('Show %1$s button', 'podpress'), sprintf(__('Category "%1$s" RSS Feed', 'podpress'), get_cat_name($cat_id)) ).'</label><br />'."\n";
							echo '<label for="'.$this->get_field_id('catcast_'.$cat_id.'_buttonurl').'">'.__('Button URL:', 'podpress').'</label> <input type="text" id="'.$this->get_field_id('catcast_'.$cat_id.'_buttonurl').'" name="'.$this->get_field_name('catcast').'['.$cat_id.'][buttonurl]" class="widefat" value="'.$instance['catcast'][$cat_id]['buttonurl'].'" /><br />'."\n";							
							echo '<label for="'.$this->get_field_id('catcast_'.$cat_id.'_altfeedurl').'">'.__('Alternative Feed URL:', 'podpress').'</label> <input type="text" id="'.$this->get_field_id('catcast_'.$cat_id.'_altfeedurl').'" name="'.$this->get_field_name('catcast').'['.$cat_id.'][altfeedurl]" class="widefat" value="'.$instance['catcast'][$cat_id]['altfeedurl'].'" />'."\n";							
							echo '</div>'."\n";
						}
					}
				}
				?>
				</div><!-- End: podPress Widget Accordion -->
				<p class="podpress_widget_settings_row">
				<?php _e('Show buttons or text?', 'podpress'); ?><br />
				<label for="<?php echo $this->get_field_id('buttons'); ?>"><?php _e('Buttons', 'podpress'); ?></label> <input type="radio" <?php echo $buttons; ?> value="buttons" id="<?php echo $this->get_field_id('buttons'); ?>" name="<?php echo $this->get_field_name('buttons-or-text'); ?>" /> <input type="radio" <?php echo $text; ?> value="text" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('buttons-or-text'); ?>" /> <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text', 'podpress'); ?></label>
				</p>
				<?php 
			}
		} // class podPress Feed Buttons Widget
	}
	
	/**
	* podpress_get_feed_buttons - retrieves a list of all feed button files from a certian directory
	*
	* @package podPress
	* @since 8.8.8
	*
	* @param str $dir (optional) - the full directory name of the directory which inludes the feed buttons
	* @param str $exceptions (optional) - an Array of names of files which should not be in the result list / if it is empty then it means no exceptions
	* @param str $needle (optional) - a string which should be a part of the file names 
	*
	* @return Array all file which inlude "feed_button" in their names or an empty array
	*/
	function podpress_get_feed_buttons($dir = '', $exceptions = Array('feed_button-atom-blog.png', 'feed_button-atom-comments.png', 'feed_button-rss-blog.png', 'feed_button-rss-comments.png'), $needle = '') {
		if ( FALSE != empty($dir) OR FALSE == is_dir($dir) ) {
			$dir = PODPRESS_DIR.'/images/';
		}
		if ( FALSE == is_dir($dir) OR FALSE == is_readable($dir) ) {
			return Array();
		}
		if ( FALSE != empty($exceptions)  ) {
			$use_exceptions = FALSE;
		} else {
			$use_exceptions = TRUE;
		}
		if ( FALSE != empty($needle) ) {
			$needle = 'feed_button';
		}
		$feed_buttons= Array();
		if ( $handle = opendir($dir) ) {
			while ( false !== ($file = readdir($handle)) ) {
				if ( $file != "." AND $file != ".." AND FALSE !== stripos($file, $needle) ) {
					if ( TRUE === $use_exceptions ) {
						if ( FALSE == in_array($file, $exceptions) ) {
							$feed_buttons[] = $file;
						}
					} else {
						$feed_buttons[] = $file;
					}
				}
			}
			closedir($handle);
		}
		sort($feed_buttons);
		return $feed_buttons;
	}	
	
	/**
	* podPress XSPF Player Widget Class
	* since podPress v8.8.7 beta 2
	* for WP >= 2.8
	*/
	if ( TRUE == version_compare($wp_version, '2.8', '>=') ) {
		class podpress_xspfplayer extends WP_Widget {
			/** constructor */
			function podpress_xspfplayer() {
				$widget_ops = array(
					'classname' => 'podpress_xspfplayer', 
					'description' => __('Shows a XSPF Player in the sidebar which uses e.g. the XSPF playlist of your podcast episodes', 'podpress')
				);
				$control_ops = array('width' => 400, 'height' => 300);
				
				parent::WP_Widget(false, $name = __('podPress - XSPF Player','podpress'), $widget_ops, $control_ops);
			}

			/** @see WP_Widget::widget */
			function widget($args, $instance) {
				GLOBAL $podPress, $blog_id;
				extract($args);
				if (!isset($instance['title'])) {
					$instance['title'] = __('Podcast Player', 'podpress');
				} else {
					$instance['title'] = stripslashes($instance['title']);
				}
				$title = apply_filters('widget_title', $instance['title']);
				if (!isset($instance['useSlimPlayer'])) {
					$instance['useSlimPlayer'] = false;
				}
				if ( 150 > intval($instance['PlayerWidth']) ) {
					$instance['PlayerWidth'] = 150; // min width
				}
				
				// save the current (the first) XSPF player widget ID (of a page) in the db
				$isset_podpress_xspf_widget_temp = get_option('podpress_xspf_widget_temp');
				if (FALSE === $isset_podpress_xspf_widget_temp) {
					update_option('podpress_xspf_widget_temp', $widget_id);
				}
				// podpress_xspf_widget_temp is going to be deleted from the db after the playlist has been loaded. (The XSPF loads the skin file before the playlist.)
				
				$skin_variables_url = PODPRESS_OPTIONS_URL.'/xspf_options/variables';
				$skin_variables_dir = PODPRESS_OPTIONS_DIR.'/xspf_options/variables';
				$skin_file = PODPRESS_URL.'/podpress_xspfskinfile.php';
				
				echo $before_widget."\n";
				echo $before_title . $title . $after_title."\n";
				
				if (defined('PODPRESS_ONE_XSPF_IS_ACTIVE')) {
					echo '<div class="message error"><p>'.__('Please use this widget only once per page.', 'podpress').'</p></div>';
				} else {
					define('PODPRESS_ONE_XSPF_IS_ACTIVE', TRUE);
					if ( TRUE === $instance['useSlimPlayer'] ) {
						if ( 30 > intval($instance['SlimPlayerHeight']) ) {
							$instance['SlimPlayerHeight'] = 30; // min height slim
						}
						
						if ( TRUE == defined('PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_'.$blog_id) AND TRUE == defined('PODPRESS_XSPF_SLIM_SKIN_URL_'.$blog_id) AND TRUE == defined('PODPRESS_XSPF_SLIM_SKIN_DIR_'.$blog_id) AND TRUE == is_readable(constant('PODPRESS_XSPF_SLIM_SKIN_DIR_'.$blog_id).'/skin_'.$blog_id.'_'.$instance['PlayerWidth'].'x'.$instance['SlimPlayerHeight'].'.xml') ) {
							$skin_file = constant('PODPRESS_XSPF_SLIM_SKIN_URL_'.$blog_id).'/skin_'.$blog_id.'_'.$instance['PlayerWidth'].'x'.$instance['SlimPlayerHeight'].'.xml';
						}
						
						$skin_variables_url .= '_slim';
						$skin_variables_dir .= '_slim';
						if ( TRUE === defined('PODPRESS_XSPF_SLIM_USE_CUSTOM_VARIABLES_'.$blog_id) AND TRUE === constant('PODPRESS_XSPF_SLIM_USE_CUSTOM_VARIABLES_'.$blog_id) AND TRUE == is_readable($skin_variables_dir.'/variables_'.$blog_id.'.txt')) {
							$variables = '&skin_url='.$skin_file.'&loadurl='.$skin_variables_url.'/variables_'.$blog_id.'.txt';
						} else {
							$variables = '&skin_url='.$skin_file.'&autoload=true&autoplay=false&loaded=true';
						}
						$data_string = PODPRESS_URL.'/players/xspf_jukebox/xspf_jukebox.swf?playlist_url='.(PODPRESS_URL.'/podpress_xspfplaylist.php').$variables;
						$data_string = htmlspecialchars($data_string);
						echo '<object type="application/x-shockwave-flash" width="'.$instance['PlayerWidth'].'" height="'.$instance['SlimPlayerHeight'].'" id="podpress_xspf_player_slim" data="'.$data_string.'">'."\n";
					} else {
						if ( 100 > intval($instance['PlayerHeight']) ) {
							$instance['PlayerHeight'] = 100; // min height
						}
						
						if ( TRUE == defined('PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_'.$blog_id) AND TRUE == defined('PODPRESS_XSPF_SKIN_URL_'.$blog_id) AND TRUE == defined('PODPRESS_XSPF_SKIN_DIR_'.$blog_id) AND TRUE == is_readable(constant('PODPRESS_XSPF_SKIN_DIR_'.$blog_id).'/skin_'.$blog_id.'_'.$instance['PlayerWidth'].'x'.$instance['PlayerHeight'].'.xml') ) {
							$skin_file = constant('PODPRESS_XSPF_SKIN_URL_'.$blog_id).'/skin_'.$blog_id.'_'.$instance['PlayerWidth'].'x'.$instance['PlayerHeight'].'.xml';
						}
						if ( TRUE === defined('PODPRESS_XSPF_USE_CUSTOM_VARIABLES_'.$blog_id) AND TRUE === constant('PODPRESS_XSPF_USE_CUSTOM_VARIABLES_'.$blog_id) AND TRUE == is_readable($skin_variables_dir.'/variables_'.$blog_id.'.txt')) {
							$variables = '&skin_url='.$skin_file.'&loadurl='.$skin_variables_url.'/variables_'.$blog_id.'.txt';
						} else {
							$variables = '&skin_url='.$skin_file.'&autoload=true&autoplay=false&loaded=true';
						}
						
						$data_string = PODPRESS_URL.'/players/xspf_jukebox/xspf_jukebox.swf?playlist_url='.(PODPRESS_URL.'/podpress_xspfplaylist.php').$variables;
						$data_string = htmlspecialchars($data_string);
						echo '<object type="application/x-shockwave-flash" width="'.$instance['PlayerWidth'].'" height="'.$instance['PlayerHeight'].'" id="podpress_xspf_player" data="'.$data_string.'">'."\n";
					}
					echo '	<param name="movie" value="'.$data_string.'" />'."\n";			
					if ( defined('PODPRESS_XSPF_BACKGROUND_COLOR_'.$blog_id) AND '' !== constant('PODPRESS_XSPF_BACKGROUND_COLOR_'.$blog_id) ) {
						echo '	<param name="bgcolor" value="#'.constant('PODPRESS_XSPF_BACKGROUND_COLOR_'.$blog_id).'" />'."\n";
					} else {
						echo '	<param name="bgcolor" value="#FFFFFF" />'."\n";
					}
					echo '</object>'."\n";
				}
				echo $after_widget;
			}

			/** @see WP_Widget::update */
			function update($new_instance, $old_instance) {
				GLOBAL $blog_id;
				$blog_charset = get_bloginfo('charset');
				$instance = $old_instance;
				$instance['title'] = htmlspecialchars(strip_tags(trim($new_instance['title'])), ENT_QUOTES, $blog_charset);
				$instance['PlayerWidth'] = intval(preg_replace('/[^0-9]/', '',$new_instance['width'])); // only numeric values are allowed
				$instance['PlayerHeight'] = intval(preg_replace('/[^0-9]/', '',$new_instance['height'])); // only numeric values are allowed
				$instance['useSlimPlayer'] = isset($new_instance['useSlimPlayer']);
				$instance['SlimPlayerHeight'] = intval(preg_replace('/[^0-9]/', '',$new_instance['heightslim'])); // only numeric values are allowed
				if ( 150 > intval($instance['PlayerWidth']) ) {
					$instance['PlayerWidth'] = 150; // min width
				}			
				if ( 100 > intval($instance['PlayerHeight']) ) {
					$instance['PlayerHeight'] = 100; // min height
				}
				if ( 100 < intval($instance['SlimPlayerHeight']) ) {
					$instance['SlimPlayerHeight'] = 100; // max height slim
				} elseif ( 30 > intval($instance['SlimPlayerHeight']) ) {
					$instance['SlimPlayerHeight'] = 30; // min height slim
				}
				if ( isset($new_instance['xspf_use_custom_playlist']) ) {
					$instance['xspf_use_custom_playlist'] = TRUE;
				} else {
					$instance['xspf_use_custom_playlist'] = FALSE;
				}
				$instance['xspf_custom_playlist_url'] = clean_url($new_instance['xspf_custom_playlist_url'], array('http', 'https'), 'db');
				
				// delete the old widget settings
				$old_widget_options = get_option('widget_podPressXspfPlayer');
				if ( FALSE !== $old_widget_options ) {
					delete_option('widget_podPressXspfPlayer');
				}				
				
				return $instance;
			}

			/** @see WP_Widget::form */
			function form($instance) {
				GLOBAL $blog_id;
				
				// take over the old widget settings (of podPress 8.8.5.x widgets)
				$old_widget_options = get_option('widget_podPressXspfPlayer');
				if ( TRUE == is_array($old_widget_options) AND FALSE == empty($old_widget_options) ) {
					$instance['title'] = $old_widget_options['title'];
					$instance['PlayerWidth'] = $old_widget_options['PlayerWidth'];
					$instance['PlayerHeight'] = $old_widget_options['PlayerHeight'];
					$instance['useSlimPlayer'] = $old_widget_options['useSlimPlayer'];
					$instance['SlimPlayerHeight'] = $old_widget_options['SlimPlayerHeight'];
				}
				
				if(!isset($instance['title'])) {
					$instance['title'] = __('Podcast Player', 'podpress');
				}
				$title = esc_attr(stripslashes($instance['title']));

				if ( 150 > intval($instance['PlayerWidth']) ) {
					$instance['PlayerWidth'] = 150; // min width
				}			
				if ( 100 > intval($instance['PlayerHeight']) ) {
					$instance['PlayerHeight'] = 100; // min height
				}
				$useSlimPlayer = $instance['useSlimPlayer'] ? ' checked="checked"' : '';
				if ( 100 < intval($instance['SlimPlayerHeight']) ) {
					$instance['SlimPlayerHeight'] = 100; // max height slim
				} elseif ( 30 > intval($instance['SlimPlayerHeight']) ) {
					$instance['SlimPlayerHeight'] = 30; // min height slim
				}
				?>
				<p><?php _e('<strong>Notice:</strong> This widget works only one time per blog page.', 'podpress'); ?></p>
				<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="podpress_widget_settings_title" /></p>
				<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Player Width:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" maxlength="3" value="<?php echo $instance['PlayerWidth']; ?>" class="podpress_widget_settings_3digits" /> <?php _e('px', 'podpress'); ?> <?php echo '<span class="nonessential">'.__('(150 <= w < 1000)', 'podpress').'</span>'; ?></p>
				<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Player Height:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" maxlength="3" value="<?php echo $instance['PlayerHeight']; ?>" class="podpress_widget_settings_3digits" /> <?php _e('px', 'podpress'); ?> <?php echo '<span class="nonessential">'.__('(100 <= h < 1000)', 'podpress').'</span>'; ?></p>
				<p><label for="<?php echo $this->get_field_id('useSlimPlayer'); ?>"><?php _e('Use Slim Player:', 'podpress'); ?></label> <input type="checkbox" id="<?php echo $this->get_field_id('useSlimPlayer'); ?>" name="<?php echo $this->get_field_name('useSlimPlayer'); ?>"<?php echo $useSlimPlayer; ?> /></p>
				<p><label for="<?php echo $this->get_field_id('heightslim'); ?>"><?php _e('Slim Player Height:', 'podpress'); ?></label> <input type="text" id="<?php echo $this->get_field_id('heightslim'); ?>" name="<?php echo $this->get_field_name('heightslim'); ?>" maxlength="3" value="<?php echo $instance['SlimPlayerHeight']; ?>" class="podpress_widget_settings_3digits" /> <?php _e('px', 'podpress'); ?> <?php echo '<span class="nonessential">'.__('(30 <= h <= 100)', 'podpress').'</span>'; ?></p>
				<?php
				if ( defined('PODPRESS_CUSTOM_XSPF_URL_'.$blog_id) AND '' !== constant('PODPRESS_CUSTOM_XSPF_URL_'.$blog_id) ) {
					$xspf_custom_playlist_url_readonly = ' readonly="readonly"';
					$xspf_custom_playlist_url = esc_attr(constant('PODPRESS_CUSTOM_XSPF_URL_'.$blog_id));
					$xspf_use_custom_playlist_disabled = ' disabled="disabled"';
					$xspf_use_custom_playlist_checked = ' checked="checked"';
					$xspf_custom_playlist_msg = '<p class="podpress_notice">'.sprintf(__('<strong>Notice:</strong> The custom playlist URL is currently defined via the constant PODPRESS_CUSTOM_XSPF_URL_%1$s and this constant overwrites the custom XSPF playlist settings.', 'podpress'), $blog_id).'</p>';
				} else {
					$xspf_custom_playlist_url_readonly = '';
					$xspf_custom_playlist_url = esc_attr($instance['xspf_custom_playlist_url']);
					$xspf_use_custom_playlist_disabled = '';
					if ( TRUE === $instance['xspf_use_custom_playlist'] ) {
						$xspf_use_custom_playlist_checked = ' checked="checked"';
					} else {
						$xspf_use_custom_playlist_checked = '';
					}
					$xspf_custom_playlist_msg = '';
				}
				echo '<p><label for="'.$this->get_field_id('xspf_use_custom_playlist').'">'.__('use a custom XSPF playlist:', 'podpress').'</label> <input type="checkbox" name="'.$this->get_field_name('xspf_use_custom_playlist').'" id="'.$this->get_field_id('xspf_use_custom_playlist').'"'.$xspf_use_custom_playlist_checked.$xspf_use_custom_playlist_disabled.' /></p>'."\n";
				echo '<p><label for="'.$this->get_field_id('xspf_custom_playlist_url').'">'.__('custom playlist URL:', 'podpress').'</label><br /><input type="text" name="'.$this->get_field_name('xspf_custom_playlist_url').'" id="'.$this->get_field_id('xspf_custom_playlist_url').'" class="podpress_full_width_text_field" size="40" value="'.$xspf_custom_playlist_url.'"'.$xspf_custom_playlist_url_readonly.' /><span class="nonessential">'.__('The custom playlist URL has to be an URL to a playlist which is on the same domain/server as your blog. The files in the playlist can be located some where else.', 'podpress').'</span></p>'.$xspf_custom_playlist_msg."\n";
				if ( TRUE == defined('PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_'.$blog_id) AND TRUE === constant('PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_'.$blog_id) ) { 
					echo '<p class="podpress_notice">'.sprintf(__('<strong>Notice:</strong> PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_%1$s is defined. This widget uses custom skin files. Make sure that there is a custom skin file for these dimensions (see podpress_xspf_config.php).', 'podpress'), $blog_id).'</p>';
				}
			}
		} // class podPress XSPF Player Widget
	}
	
	/**************************************************************/
	/* Functions for supporting the downloader */
	/**************************************************************/
	
	function podPress_StatCounter($postID, $media, $method) {
		global $wpdb;
		switch($method) {
			case 'feed':
			case 'web':
			case 'play':
				$sqlIoU = "INSERT INTO ".$wpdb->prefix."podpress_statcounts (postID, media, $method) VALUES ($postID, '$media', 1) ON DUPLICATE KEY UPDATE $method = $method+1, total = total+1";
				$result = $wpdb->query($sqlIoU);
				break;
			default:
				return;
		}
	}
	
	function podPress_StatCollector($postID, $media, $method) {
		global $wpdb;

		$media	= addslashes($media);
		$method	= addslashes($method);

		$ip		= addslashes($_SERVER['REMOTE_ADDR']);
		//$cntry	= addslashes(podPress_determineCountry($ip));
		$cntry	= addslashes('');
		$lang	= addslashes(podPress_determineLanguage());
		$ref	= addslashes($_SERVER['HTTP_REFERER']);
		$url 	= parse_url($ref);
		$domain	= addslashes(eregi_replace('^www.','',$url['host']));
		//$res	= $_SERVER['REQUEST_URI'];
		$ua   = addslashes($_SERVER['HTTP_USER_AGENT']);
		$br		= podPress_parseUserAgent($_SERVER['HTTP_USER_AGENT']);
		$dt		= time();
	
		$query = "INSERT INTO ".$wpdb->prefix."podpress_stats (postID, media, method, remote_ip, country, language, domain, referer, user_agent, platform, browser, version, dt) VALUES ('$postID', '$media', '$method', '".$ip."', '$cntry', '$lang', '$domain', '$ref', '$ua', '".addslashes($br['platform'])."', '".addslashes($br['browser'])."', '".addslashes($br['version'])."', $dt)";
		$result = $wpdb->query($query);
		return $wpdb->insert_id;
	}
	
	function podPress_determineCountry($ip) {
		$coinfo = @file('http://www.hostip.info/api/get.html?ip=' . $ip);
		$country_string = explode(':',$coinfo[0]);
		$country = trim($country_string[1]);

		if($country == '(Private Address) (XX)' 
		|| $country == '(Unknown Country?) (XX)' 
		|| $country == '' 
		|| !$country 
		  )return 'Indeterminable';
			
		return $country;
	}
	
	function podPress_parseUserAgent($ua) {
		$browser['platform'] = "Indeterminable";
		$browser['browser'] = "Indeterminable";
		$browser['version'] = "Indeterminable";
		$browser['majorver'] = "Indeterminable";
		$browser['minorver'] = "Indeterminable";
		
		// Test for platform
		if (FALSE !== stripos($ua, 'Win95')) {
			$browser['platform'] = "Windows 95";
			}
		else if (FALSE !== stripos($ua, 'Win98')) {
			$browser['platform'] = "Windows 98";
			}
		else if (FALSE !== stripos($ua, 'Win 9x 4.90')) {
			$browser['platform'] = "Windows ME";
			}
		else if (FALSE !== stripos($ua, 'Windows NT 5.0')) {
			$browser['platform'] = "Windows 2000";
			}
		else if (FALSE !== stripos($ua, 'Windows NT 5.1')) {
			$browser['platform'] = "Windows XP";
			}
		else if (FALSE !== stripos($ua, 'Windows NT 5.2')) {
			$browser['platform'] = "Windows 2003";
			}
		else if (FALSE !== stripos($ua, 'Windows NT 6.0')) {
			$browser['platform'] = "Windows Vista";
			}
		else if (FALSE !== stripos($ua, 'Windows NT 6.1')) {
			$browser['platform'] = "Windows 7";
			}
		else if (FALSE !== stripos($ua, 'Windows')) {
			$browser['platform'] = "Windows";
			}
		else if (FALSE !== stripos($ua, 'Mac OS X')) {
			$browser['platform'] = "Mac OS X";
			}
		else if (FALSE !== stripos($ua, 'iphone') || FALSE !== stripos($ua, 'ios')) {
			$browser['platform'] = "iPhone OS / iOS";
			}
		else if (FALSE !== stripos($ua, 'Mac OS X')) {
			$browser['platform'] = "Mac OS X";
			}
		else if (FALSE !== stripos($ua, 'Macintosh')) {
			$browser['platform'] = "Mac OS Classic";
			}
		else if (FALSE !== stripos($ua, 'Linux')) {
			$browser['platform'] = "Linux";
			}
		else if (FALSE !== stripos($ua, 'BSD') || FALSE !== stripos($ua, 'FreeBSD') || FALSE !== stripos($ua, 'NetBSD')) {
			$browser['platform'] = "BSD";
			}
		else if (FALSE !== stripos($ua, 'SunOS')) {
			$browser['platform'] = "Solaris";
			}
			
			
		$browsernames = Array(
			'Firefox' => 'Firefox', 
			'Opera' => 'Opera', 
			'Safari' => 'Safari', 
			'MSIE' => 'Internet Explorer', 
			'Chrome' => 'Chrome', 
			'iCab' => 'iCab', 
			'Camino' => 'Camino', 
			'Konqueror' => 'Konqueror',
			'Iceweasel' => 'Iceweasel',
			'Midori' => 'Midori',
			'K-Meleon' => 'K-Meleon',
			'Chimera' => 'Chimera',
			'Firebird' => 'Firebird',
			'Netscape' => 'Netscape',
			'MSN Explorer' => 'MSN Explorer',
			'K-Meleon' => 'K-Meleon', 
			'AOL' => 'America Online Browser',
			'America Online Browser' => 'America Online Browser',
			'Beonex' => 'Beonex',
			'OmniWeb' => 'OmniWeb',
			'Galeon' => 'Galeon',
			'Kazehakase' => 'Kazehakase',
			'Amaya' => 'Amaya',
			'Lynx' => 'Lynx',
			'Links' => 'Links',
			'ELinks' => 'ELinks',
			
			'Crawl' => 'Crawler/Search Engine',
			'bot' => 'Crawler/Search Engine',
			'slurp' => 'Crawler/Search Engine',
			'spider' => 'Crawler/Search Engine'
		);
		$foundbrowser = FALSE;
		foreach ($browsernames as $browserid => $browsername) {
			$result = preg_match('/'.$browserid.'\/[0-9]+\.[0-9]+/i', $ua, $b);
			if (0 < $result) {
				$b_parts = explode('/', $b[0]);
				$browser['browser'] = $browsername;
				$browser['version'] = $b_parts[1];
				$foundbrowser = TRUE;
				break;
			}
		}
		if ( FALSE == $foundbrowser ) {
			foreach ($browsernames as $browserid => $browsername) {
				$result = preg_match('/'.$browserid.' [0-9]+\.[0-9]+/i', $ua, $b);
				if (0 < $result) {
					$b_parts = explode(' ', $b[0]);
					$browser['browser'] = $browsername;
					$browser['version'] = $b_parts[1];
					break;
				}
			}
		}
		
		if (empty($browser['version']) || $browser['version']=='.0') {
			$browser['version'] = "Indeterminable";
			$browser['majorver'] = "Indeterminable";
			$browser['minorver'] = "Indeterminable";
		}
		
		return $browser;
	}
	
	function podPress_determineLanguage() {
		$lang_choice = "empty"; 
		if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			// Capture up to the first delimiter (, found in Safari)
			preg_match("/([^,;]*)/",$_SERVER["HTTP_ACCEPT_LANGUAGE"],$langs);
			$lang_choice = $langs[0];
		}
		return $lang_choice;
	}
	
	function podPress_statsDownloadRedirect($requested = '##NOTSET##') {
		GLOBAL $podPress;
		if($requested == '##NOTSET##') {
			$requested = parse_url($_SERVER['REQUEST_URI']);
			$requested = $requested['path'];
		}
		$pos = 0;
		if (is_404() || $pos = strpos($requested, 'podpress_trac')) {
			if($pos == 0) {
				$pos = strpos($requested, 'podpress_trac');
			}
			$pos = $pos+14;
			if(substr($requested, $pos, 1) == '/') {
				$pos = $pos+1;
			}
			$requested = substr($requested, $pos);
			$parts = explode('/', $requested);
			if(count($parts) == 4) {
				podPress_processDownloadRedirect($parts[1], $parts[2], $parts[3], $parts[0]);
			}
		}
	}

	function podPress_processDownloadRedirect($postID, $mediaNum, $filename, $method = '') {
		GLOBAL $podPress, $wpdb;
		$allowedMethods = array('feed', 'play', 'web');
		$realURL = false;
		$realSysPath = false;
		$statID = false;

		if(substr($filename, -20, 20) == 'podPressStatTest.txt') {
			status_header('200');
			echo 'Worked'; // Don't translate this!
			exit;
		}

		if (in_array($method, $allowedMethods) && is_numeric($postID) && is_numeric($mediaNum)) {
			$mediaFiles = podPress_get_post_meta($postID, 'podPressMedia', true);
			if(isset($mediaFiles[$mediaNum])) {			
				if($mediaFiles[$mediaNum]['URI'] == urldecode($filename)) {
					$realURL = $filename;
				} elseif(podPress_getFileName($mediaFiles[$mediaNum]['URI']) == urldecode($filename)) {
					$realURL = $mediaFiles[$mediaNum]['URI'];
				} elseif(podPress_getFileName($mediaFiles[$mediaNum]['URI_torrent']) == urldecode($filename)) {
					$realURL = $mediaFiles[$mediaNum]['URI_torrent'];
				}
			}
		}

		if(!$realURL) {
			header('X-PodPress-Location: '.get_option('siteurl'));
			header('Location: '.get_option('siteurl'));
			exit;
		}
		$badextensions = array('.smi', '.jpg', '.png', '.gif');
		if($filename && !in_array(strtolower(substr($filename, -4)), $badextensions)) {
			podPress_StatCounter($postID, $filename, $method);
			if($podPress->settings['statLogging'] == 'Full' || $podPress->settings['statLogging'] == 'FullPlus') {
				$statID = podPress_StatCollector($postID, $filename, $method);
			}
		}
	
		$realSysPath = $podPress->convertPodcastFileNameToSystemPath(str_replace('%20', ' ', $realURL));
		if (FALSE === $realSysPath) {
			$realSysPath = $podPress->TryToFindAbsFileName(str_replace('%20', ' ', $realURL));
		}
		$realURL = $podPress->convertPodcastFileNameToValidWebPath($realURL);
	
		if($podPress->settings['enable3rdPartyStats'] == 'PodTrac') {
			$realURL = str_replace(array('ftp://', 'http://', 'https://'), '', $realURL);
			$realURL = $podPress->podtrac_url.$realURL;
		} elseif( strtolower($podPress->settings['enable3rdPartyStats']) == 'blubrry' && !empty($podPress->settings['statBluBrryProgramKeyword'])) {
			$realURL = str_replace('http://', '', $realURL);
			$realURL = $podPress->blubrry_url.$podPress->settings['statBluBrryProgramKeyword'].'/'.$realURL;
		} elseif ($podPress->settings['statLogging'] == 'FullPlus' && $realSysPath !== false) {
			status_header('200');
			$content_type = podPress_mimetypes(podPress_getFileExt($realSysPath));
			if($method == 'web') {
				header("Pragma: ");
				header("Cache-Control: ");
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
				header("Cache-Control: post-check=0, pre-check=0", false);
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header("Content-Description: ".trim(htmlentities($filename)));
				header("Connection: close");
				if(substr($content_type, 0, 4) != 'text') {
					header("Content-Transfer-Encoding: binary");
				}
			} else {
				header("Connection: Keep-Alive");
			}
			header("X-ForcedBy: podPress");
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Content-type: '.$content_type);
			header('Content-Length: '.filesize($realSysPath));
			set_time_limit(0);
			$chunksize = 1*(1024*1024); // how many bytes per chunk
			if ($handle = fopen($realSysPath, 'rb')) {
				while (!feof($handle) && connection_status()==0) {
					echo fread($handle, $chunksize);
					ob_flush();
					flush();
				}
				fclose($handle);
			}
			
			if($statID !== false && connection_status() == 0 && !connection_aborted()) {
				$sqlU = "UPDATE ".$wpdb->prefix."podpress_stats SET completed=1 WHERE id=".$statID;
				$wpdb->hide_errors();
				$result = $wpdb->query($sqlI);
				if(!$result) {
					$wpdb->query($sqlU);
				}
			}
			exit;
		}
		$realURL = str_replace(' ', '%20', $realURL);
		status_header('302');
		header('X-PodPress-Location: '.$realURL, true, 302);
		header('Location: '.$realURL, true, 302);
		header('Content-Length: 0');
		exit;
	}

	function podPress_remote_version_check() {
		$current = PODPRESS_VERSION;
		$latestVersionCache = podPress_get_option('podPress_versionCheck');
		if(($latestVersionCache['cached']+86400) < time() ) {
			$current = $latestVersionCache['version'];
		} elseif (class_exists(snoopy)) {
			$client = new Snoopy();
			$client->_fp_timeout = 10;
			if (@$client->fetch('http://www.mightyseek.com/podpress_downloads/versioncheck.php?url='.get_option('siteurl').'&current='.PODPRESS_VERSION) === false) {
				return -1;
			} else {
				$remote = $client->results;
				if (!$remote || strlen($remote) > 8 ) {
					return -1;
				}
				$current = $remote;
			}
			delete_option('podPress_versionCheck');
			podPress_add_option('podPress_versionCheck', array('version'=>$current, 'cached'=> time()), 'Latest version available', 'yes'); 
		}
	
		if ($current > PODPRESS_VERSION) {
			return 1;
		} else {
			return 0;
		}
	}
	
	/**************************************************************/
	/* Functions for supporting version of WordPress before 2.0.0 */
	/**************************************************************/
	
	function podPress_add_post_meta($post_id, $key, $value, $unique = false) {
		GLOBAL $wpdb;
		if(!podPress_WPVersionCheck('2.0.0')) {
			if ( is_array($value) || is_object($value) ) {
				$value = $wpdb->escape(serialize($value));
			}
		}
		return add_post_meta($post_id, $key, $value, $unique);
	}

	function podPress_get_post_meta($post_id, $key, $single = false) {
		if(podPress_WPVersionCheck('2.0.0') === false) {
			return maybe_unserialize(get_post_meta($post_id, $key, $single));
		}
		return get_post_meta($post_id, $key, $single);
	}
		
	function podPress_add_option($name, $value = '', $description = '', $autoload = 'yes') {
		if(!podPress_WPVersionCheck('2.0.0')) {
			if ( is_array($value) || is_object($value) ) {
				$value = serialize($value);
			}
		}
		return add_option($name, $value, $description, $autoload);
	}

	function podPress_get_option($option) {
		if(!podPress_WPVersionCheck('2.0.0')) {
			return maybe_unserialize(get_option($option));
		}
		return get_option($option);
	}

	function podPress_update_option($option_name, $option_value) {
		delete_option($option_name); 
		$result = podPress_add_option($option_name, $option_value);
		return $result;
	}
?>