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

if (!function_exists('podPress_webContent')) {
	function podPress_webContent($podPressTemplateData) {
		GLOBAL $podPress, $post;
		$divider = ' | ';
		$podPressPlayBlockScripts = '';
		$podPressContentAll = '';

		if ( TRUE == isset($podPress->settings['do_not_use_the_target_attribute']) AND TRUE === $podPress->settings['do_not_use_the_target_attribute'] ) {
			$target_attribute = '';
		} else {
			$target_attribute = ' target="new"';
		}

		foreach ($podPressTemplateData['files'] as $key=>$val) {
			$podPressContent = '';
			$podPressDownloadlinks = '';
			$GLOBALS['podPressPlayer']++;
			if(empty($val['dimensionW'])) {
				$val['dimensionW'] = "''";
			}
			if(empty($val['dimensionH'])) {
				$val['dimensionH'] = "''";
			}
			$dividerNeeded = false;
			
			$podPressEpisodeTitle = stripslashes(htmlspecialchars_decode(__($val['title'], 'podpress'))); // if the title is not given by the author then it will be a defaultTitle (see podPress_defaultTitles)
			
			if($val['enablePlayer']) {
				if($podPressContent != '') {			
					$podPressContent .= "<br />\n";
				}
				if ( 'audio_mp3' == $val['type'] AND TRUE == isset($podPress->settings['player']['listenWrapper']) AND TRUE == $podPress->settings['player']['listenWrapper'] AND FALSE == $podPress->settings['enablePodangoIntegration'] AND TRUE == isset($podPress->settings['mp3Player']) AND '1pixelout' == $podPress->settings['mp3Player'] ) {
					$podPressContent .= "\n".'<div class="podpress_listenwrapper_container" id="podpress_lwc_'.$GLOBALS['podPressPlayer'].'" style="background-image:url('.PODPRESS_URL.'/images/listen_wrapper.gif);"><div class="podpress_mp3_borderleft"></div><div class="podpress_1pixelout_container"><div id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'"><!-- podPress --></div></div></div>'."\n";
				} elseif ( 'audio_mp3' == $val['type'] AND (False == isset($podPress->settings['player']['listenWrapper']) OR FALSE == $podPress->settings['player']['listenWrapper']) AND FALSE == $podPress->settings['enablePodangoIntegration'] AND TRUE == isset($podPress->settings['mp3Player']) AND '1pixelout' == $podPress->settings['mp3Player'] ) {
					$podPressContent .= "\n".'<div class="podpress_playerspace podpress_mp3player"><div id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'"><!-- podPress --></div></div>'."\n";
				} else {
					$podPressContent .= "\n".'<div class="podpress_playerspace" id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'"><!-- podPress --></div>'."\n";
				}
			}
			
			$podPressDownloadlinks .= '<!-- Begin: podPress download link line -->'."\n".'<div class="podPress_downloadlinks">';
			
			if(isset($val['image'])) {
				if($val['enableDownload'] && !empty($val['URI'])) {
					if ( 0 === strpos($val['type'], 'embed_') ) {
						$podPressDownloadlinks .= '<a href="'.$val['URI'].'"'.$target_attribute.' title="'.attribute_escape(sprintf(__('Direct Link to %1$s', 'podpress'), $podPressEpisodeTitle)).'">';
					} else {
						$podPressDownloadlinks .= '<a href="'.$val['URI'].'"'.$target_attribute.' title="'.attribute_escape(sprintf(__('Download: %1$s', 'podpress'), $podPressEpisodeTitle)).'">';
					}
				}
				$podPressDownloadlinks .= '<img src="'.podPress_url().'images/'.$val['image'].'" class="podPress_imgicon" alt="" />';
				if($val['enableDownload'] && !empty($val['URI'])) {
					$podPressDownloadlinks .= '</a>';
				}
				if(!$podPressTemplateData['showDownloadText'] == 'enabled') {
					$val['enableDownload'] = false;
				}
			}
			
			// ntm: this is not in use unless the option at the podPress general settings page will uncommented
			if(TRUE == isset($val['enableTorrentDownload']) AND TRUE === $val['enableTorrentDownload']) {
				$podPressDownloadlinks .= '<a href="'.$val['URI_torrent'].'"'.$target_attribute.' title="'.attribute_escape(sprintf(__('Download: %1$s - .torrent file', 'podpress'), $podPressEpisodeTitle)).'">';
				if(strstr($val['image'], '_button')) {
					$torrentimg = 'misc_torrent_button.png';
				} else {
					$torrentimg = 'misc_torrent_icon.png';
				}
				$podPressDownloadlinks .= '<img src="'.podPress_url().'images/'.$torrentimg.'" class="podPress_imgicon" alt="" />';
				$podPressDownloadlinks .= '</a>';
			}

			$podPressDownloadlinks .= ' &nbsp;';
			$podPressDownloadlinks .= $podPressEpisodeTitle;

			if ( $podPressTemplateData['showDuration'] == 'enabled' && !empty($val['duration']) ) {
				$podPressDownloadlinks .= ' ['.$podPress->millisecondstostring($podPress->strtomilliseconds($val['duration']), 'h:m:s:ms').']';
			}			

			if($val['enablePlayer'] || $val['enablePopup'] || $val['enableDownload'] || !$val['authorized']) {
				$podPressDownloadlinks .= ' ';
			}

			if(!$val['authorized']) {
				$podPressDownloadlinks .= ' <a href="'.get_option('siteurl').'/wp-login.php">'.__('(Protected Content)', 'podpress').'</a><br/>'."\n";
			} else {
				if($val['enablePlayer']) {
					if ($dividerNeeded) {
						$hideplayerplaynow_divider = $divider;
					} else {
						$hideplayerplaynow_divider = '';
					}
					
					if(TRUE == isset($val['enableTorrentDownload']) or 'on' == $val['disablePreview']) {
						$previewVal = 'nopreview';
					} else {
						$previewVal = 'false';
					}
					
					if ($val['enablePlaylink']) {
						$podPressDownloadlinks .= '<a href="#podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'" onclick="javascript:podPressShowHidePlayer(\''.$GLOBALS['podPressPlayer'].'\', \''.js_escape($val['URI_Player']).'\', '.strval(intval($val['dimensionW'])).', '.strval(intval($val['dimensionH'])).', \'false\', \''.$val['previewImage'].'\', \''.js_escape($val['title']).'\', \''.js_escape($val['artist']).'\'); return false;"><span id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'_PlayLink" class="podPress_playerspace_playlink">'.$hideplayerplaynow_divider.__('Play Now', 'podpress').'</span></a>';
						$dividerNeeded = true;
					} else {
						$podPressDownloadlinks .= '<span id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'_PlayLink" style="display:none">'.$hideplayerplaynow_divider.__('Play Now', 'podpress').'</span>';
						$dividerNeeded = false;
					}
					
					if ($podPress->settings['contentAutoDisplayPlayer']) {
						$podPressPlayBlockScripts .= "\n".'podPressShowHidePlayer(\''.$GLOBALS['podPressPlayer'].'\', \''.js_escape($val['URI_Player']).'\', '.strval(intval($val['dimensionW'])).', '.strval(intval($val['dimensionH'])).', \''.$previewVal.'\', \''.js_escape($val['previewImage']).'\', \''.js_escape($val['title']).'\', \''.js_escape($val['artist']).'\');';
					}
				}

				if($val['enablePopup']) {
					if($dividerNeeded) {
						$podPressDownloadlinks .= $divider;
					}
					$podPressDownloadlinks .= '<a href="#podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'" onclick="javascript:podPressPopupPlayer(\''.$GLOBALS['podPressPlayer'].'\', \''.js_escape($val['URI_Player']).'\', '.strval(intval($val['dimensionW'])).', '.strval(intval($val['dimensionH'])).', \''.js_escape(get_bloginfo('name')).'\', \''.$post->ID.'\', \''.js_escape($val['title']).'\', \''.js_escape($val['artist']).'\'); return false;">'.__('Play in Popup', 'podpress').'</a>';
					$dividerNeeded = true;
				}

				if($val['enableDownload'] && $podPressTemplateData['showDownloadText'] == 'enabled') {
					if($dividerNeeded) {
						$podPressDownloadlinks .= $divider;
					}
					if ( 0 === strpos($val['type'], 'embed_') ) {
						$podPressDownloadlinks .= '<a href="'.$val['URI'].'"'.$target_attribute.'>'.__('Direct Link', 'podpress').'</a>';
						$val['stats'] = false;
					} else {
						$podPressDownloadlinks .= '<a href="'.$val['URI'].'"'.$target_attribute.'>'.__('Download', 'podpress').'</a>';
						if($val['stats'] && $podPressTemplateData['showDownloadStats'] == 'enabled') {
							$podPressDownloadlinks .= ' ('.$val['stats']['total'].')';
							$val['stats'] = false;
						}
					}
					$dividerNeeded = true;
				}

				if($val['stats'] && $podPressTemplateData['showDownloadStats'] == 'enabled') {
					if($dividerNeeded) {
						$podPressDownloadlinks .= $divider;
					}
					$podPressDownloadlinks .= ' '.__('Downloads', 'podpress').' '.$val['stats']['total'].'';
					$dividerNeeded = true;
				}

			}
			
			$podPressDownloadlinks .= '</div>'."\n".'<!-- End: podPress download link line -->'."\n";
			$podPressContentAll .= $podPressContent.apply_filters('podpress_downloadlinks', $podPressDownloadlinks);
		}
		if ($podPress->settings['contentAutoDisplayPlayer']) {
			$podPressPlayBlockScripts = '<script type="text/javascript">'."\n".'<!--'.$podPressPlayBlockScripts;
			$podPressPlayBlockScripts .= "\n".'-->'."\n".'</script>';
		}
		return apply_filters('podpress_post_content', "<!-- Begin: podPress -->\n".'<div class="podPress_content">'.$podPressContentAll.'</div>'."\n".$podPressPlayBlockScripts."\n<!-- End: podPress -->\n");
	}
}

if (!function_exists('podPress_defaultTitles')) {
	function podPress_defaultTitles($filetype) {
		switch($filetype) {
			case 'audio_mp3':
			case 'audio_ogg':
				return __('Standard Podcast', 'podpress');
				break;
			case 'audio_m4a':
			case 'audio_mp4':
				return __('Enhanced Podcast', 'podpress');
				break;
			case 'audio_m3u':
				return __('Streaming Audio', 'podpress');
				break;
			case 'video_m4v':
				return __('Podcast Video', 'podpress');
				break;
			case 'video_mp4':
			case 'video_mov':
			case 'video_qt':
				return __('Podcast Video', 'podpress');
				break;
			case 'video_ogv':
			case 'video_avi':
			case 'video_mpg':
			case 'video_asf':
			case 'video_wmv':
				return __('Online Video', 'podpress');
				break;
			case 'audio_wma':
				return __('Online Audio', 'podpress');
				break;
			case 'video_swf':
				return __('Flash Content', 'podpress');
				break;
			case 'video_flv':
				return __('Flash Video', 'podpress');
				break;
			case 'embed_youtube':
				return  __('YouTube', 'podpress');
				break;
			case 'ebook_pdf':
				return  __('eBook', 'podpress');
				break;
			case 'misc_torrent':
				return __('Torrent File', 'podpress');
			case 'misc_other':
			default:
				return  __('Other Media', 'podpress');
		}
	}
}
?>