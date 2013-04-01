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
		GLOBAL $podPress, $post, $wp_version;
		$divider = ' | ';
		$podPressPlayBlockScripts = '';
		$podPressContentAll = '';
		
		if ( TRUE == version_compare($wp_version, '2.7', '>=') AND TRUE == version_compare($wp_version, '2.8', '<')) {// for WP 2.7.x (because the plugins_url() worked differently in WP 2.7.x)
			$plugins_url = plugins_url('podpress', __FILE__);
		} else { 
			$plugins_url = plugins_url('', __FILE__);
		}
		
		if ( TRUE === defined('PODPRESS_SHOW_SECTION_MARKERS') AND TRUE === constant('PODPRESS_SHOW_SECTION_MARKERS') ) {
			$podpress_section_begin = "<!-- Begin: podPress -->\n";
			$podpress_section_end = "\n<!-- End: podPress -->\n";
			$podpress_downloadlinks_section_begin = "<!-- Begin:  podPress download link line -->\n";
			$podpress_downloadlinks_section_end = "\n<!-- End:  podPress download link line -->\n";
		} else {
			$podpress_section_begin = '';
			$podpress_section_end = '';
			$podpress_downloadlinks_section_begin = '';
			$podpress_downloadlinks_section_end = '';
		}		

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
			
			$val['image'] = podpress_siteurl_is_ssl($val['image']);
			$val['URI'] = podpress_siteurl_is_ssl($val['URI']);
			$val['URI_Player'] = podpress_siteurl_is_ssl($val['URI_Player']);
			$val['previewImage'] = podpress_siteurl_is_ssl($val['previewImage']);
			
			$podPressEpisodeTitle = stripslashes(htmlspecialchars_decode(__($val['title'], 'podpress'))); // if the title is not given by the author then it will be a defaultTitle (see podPress_defaultTitles)

			if($val['enablePlayer']) {
				if($podPressContent != '') {			
					$podPressContent .= "<br />\n";
				}
				if ( $podPress->settings['contentAutoDisplayPlayer'] ) {
					$style = ' style="display:block;" ';
					$style_wrap_1pixelout = ' display:block;';
				} else {
					$style = ' style="display:none;" ';
					$style_wrap_1pixelout = ' display:none;';
				}
				if ( 'audio_mp3' == $val['type'] AND TRUE == isset($podPress->settings['player']['listenWrapper']) AND TRUE == $podPress->settings['player']['listenWrapper'] AND (FALSE == isset($podPress->settings['enablePodangoIntegration']) OR FALSE == $podPress->settings['enablePodangoIntegration']) AND TRUE == isset($podPress->settings['mp3Player']) AND '1pixelout' == $podPress->settings['mp3Player'] ) {
					$podPressContent .= "\n".'<div class="podpress_listenwrapper_container" id="podpress_lwc_'.$GLOBALS['podPressPlayer'].'" style="background-image:url('.$plugins_url.'/images/listen_wrapper.gif);'.$style_wrap_1pixelout.'"><div class="podpress_mp3_borderleft"></div><div class="podpress_1pixelout_container"><div id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'"><!-- podPress --></div></div></div>'."\n";
				} elseif ( 'audio_mp3' == $val['type'] AND (False == isset($podPress->settings['player']['listenWrapper']) OR FALSE == $podPress->settings['player']['listenWrapper']) AND (FALSE == isset($podPress->settings['enablePodangoIntegration']) OR FALSE == $podPress->settings['enablePodangoIntegration']) AND TRUE == isset($podPress->settings['mp3Player']) AND '1pixelout' == $podPress->settings['mp3Player'] ) {
					$podPressContent .= "\n".'<div class="podpress_playerspace podpress_playerspace_'.$val['type'].' podpress_mp3player"'.$style.'><div id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'"><!-- podPress --></div></div>'."\n";
				} else {
					$podPressContent .= "\n".'<div class="podpress_playerspace podpress_playerspace_'.$val['type'].'"><div id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'"'.$style.'><!-- podPress --></div></div>'."\n";
				}
			}
			$podPressDownloadlinks .= $podpress_downloadlinks_section_begin.'<div class="podPress_downloadlinks podPress_downloadlinks_'.$val['type'].'">';
			
			if(isset($val['image'])) {
				if($val['enableDownload'] && !empty($val['URI'])) {
					if ( 0 === strpos($val['type'], 'embed_') ) {
						$podPressDownloadlinks .= '<a href="'.$val['URI'].'"'.$target_attribute.' title="'.attribute_escape(sprintf(__('Direct Link to %1$s', 'podpress'), $podPressEpisodeTitle)).'" class="podpress_downloadimglink podpress_downloadimglink_'.$val['type'].'">';
					} else {
						$podPressDownloadlinks .= '<a href="'.$val['URI'].'"'.$target_attribute.' title="'.attribute_escape(sprintf(__('Download: %1$s', 'podpress'), $podPressEpisodeTitle)).'" class="podpress_downloadimglink podpress_downloadimglink_'.$val['type'].'">';
					}
				}
				$podPressDownloadlinks .= '<img src="'.$plugins_url.'/images/'.$val['image'].'" class="podPress_imgicon podpress_imgicon_'.$val['type'].'" alt="" />';
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
				$podPressDownloadlinks .= '<img src="'.$plugins_url.'/images/'.$torrentimg.'" class="podPress_imgicon" alt="" />';
				$podPressDownloadlinks .= '</a>';
			}

			$podPressDownloadlinks .= ' ';
			$podPressDownloadlinks .= '<span class="podpress_mediafile_title podpress_mediafile_title_'.$val['type'].'">'.$podPressEpisodeTitle.'</span>';

			if ( isset($podPressTemplateData['showDuration']) AND 'disabled' != $podPressTemplateData['showDuration'] AND FALSE == empty($val['duration'])) {
				if ( empty($podPressTemplateData['showDuration']) OR 'enabled' == $podPressTemplateData['showDuration'] ) {
					$podPressTemplateData['showDuration'] = 'colon';
				}
				if ( isset($podPress->settings['contentDurationdivider']) ) {
					$podPressDownloadlinks .= ' <span class="podpress_mediafile_dursize podpress_mediafile_dursize_'.$val['type'].'">[ '.$podPress->millisecondstostring($podPress->strtomilliseconds($val['duration']), 'h:m:s:ms', $podPress->settings['contentDurationdivider']);
				} else {
					$podPressDownloadlinks .= ' <span class="podpress_mediafile_dursize podpress_mediafile_dursize_'.$val['type'].'">[ '.$podPress->millisecondstostring($podPress->strtomilliseconds($val['duration']), 'h:m:s:ms');
				}
				$durationfilesizeseparator = ' | ';
			} else {
				$durationfilesizeseparator = ' <span class="podpress_mediafile_dursize podpress_mediafile_dursize_'.$val['type'].'">[ ';
			}
			
			if ( 'enabled' == $podPressTemplateData['showfilesize'] AND FALSE == empty($val['size']) AND FALSE === stristr($val['type'], 'embed_') ) {
				$size_mb = round(($val['size']/1048576), 2);
				if ( 0.01 > $size_mb ) {
					$size_mb = 0.01;
				}
				$podPressDownloadlinks .= $durationfilesizeseparator.$size_mb.' '.__('MB', 'podpress').' ]</span>';
			} else {
				if ( ' <span class="podpress_mediafile_dursize podpress_mediafile_dursize_'.$val['type'].'">[ ' != $durationfilesizeseparator) {
					$podPressDownloadlinks .= ' ]</span>';
				}
			}
			
			if($val['enablePlayer'] || $val['enablePopup'] || $val['enableDownload'] || !$val['authorized']) {
				$podPressDownloadlinks .= ' ';
			}

			if(!$val['authorized']) {
				$podPressDownloadlinks .= ' <a href="'.site_url().'/wp-login.php" class="podpress_protected_link">'.__('(Protected Content)', 'podpress').'</a><br/>'."\n";
			} else {
				if($val['enablePlayer']) {
					if ($dividerNeeded) {
						$hideplayerplaynow_divider = $divider;
					} else {
						$hideplayerplaynow_divider = '';
					}
					
					if ( (TRUE == isset($val['enableTorrentDownload']) AND TRUE == $val['enableTorrentDownload']) OR (TRUE == isset($val['enableTorrentDownload']) AND 'on' == $val['disablePreview']) ) {
						$previewVal = 'nopreview';
					} else {
						$previewVal = 'false';
					}
					
					if ($val['enablePlaylink']) {
						if ($podPress->settings['contentAutoDisplayPlayer']) {
							$podPressDownloadlinks .= '<a href="#podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'" class="podpress_playlink podpress_playlink_'.$val['type'].'" onclick="javascript:podPressShowHidePlayer(\''.$GLOBALS['podPressPlayer'].'\', \''.js_escape($val['URI_Player']).'\', '.strval(intval($val['dimensionW'])).', '.strval(intval($val['dimensionH'])).', \''.$previewVal.'\', \''.js_escape($val['previewImage']).'\', \''.js_escape($val['title']).'\', \''.js_escape($val['artist']).'\'); return false;"><span id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'_PlayLink" class="podPress_playerspace_playlink">'.$hideplayerplaynow_divider.__('Play Now', 'podpress').'</span></a>';
						} else {
							$podPressDownloadlinks .= '<a href="#podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'" class="podpress_playlink podpress_playlink_'.$val['type'].'" onclick="javascript:podPressShowHidePlayer(\''.$GLOBALS['podPressPlayer'].'\', \''.js_escape($val['URI_Player']).'\', '.strval(intval($val['dimensionW'])).', '.strval(intval($val['dimensionH'])).', \'force\', \''.js_escape($val['previewImage']).'\', \''.js_escape($val['title']).'\', \''.js_escape($val['artist']).'\'); return false;"><span id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'_PlayLink" class="podPress_playerspace_playlink">'.$hideplayerplaynow_divider.__('Play Now', 'podpress').'</span></a>';
						}
						$dividerNeeded = true;
					} else {
						$podPressDownloadlinks .= '<span id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'_PlayLink" style="display:none">'.$hideplayerplaynow_divider.__('Play Now', 'podpress').'</span>';
						$dividerNeeded = false;
					}
					$podPressDownloadlinks .= '<input type="hidden" id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'_OrigURL" value="'.attribute_escape($podPress->convertPodcastFileNameToValidWebPath($val['URI_orig'])).'" />';
					
					if ($podPress->settings['contentAutoDisplayPlayer']) {
						$podPressPlayBlockScripts .= 'podPressShowHidePlayer(\''.$GLOBALS['podPressPlayer'].'\', \''.js_escape($val['URI_Player']).'\', '.strval(intval($val['dimensionW'])).', '.strval(intval($val['dimensionH'])).', \''.$previewVal.'\', \''.js_escape($val['previewImage']).'\', \''.js_escape($val['title']).'\', \''.js_escape($val['artist']).'\');';
					}
				}

				if ( $val['enablePopup'] AND FALSE === strpos($val['type'], 'ebook_') ) {
					if($dividerNeeded) {
						$podPressDownloadlinks .= $divider;
					}
					$podPressDownloadlinks .= '<a href="#podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'" class="podpress_playinpopup podpress_playinpopup_'.$val['type'].'" onclick="javascript:podPressPopupPlayer(\''.$GLOBALS['podPressPlayer'].'\', \''.js_escape($val['URI_Player']).'\', '.strval(intval($val['dimensionW'])).', '.strval(intval($val['dimensionH'])).', \''.js_escape(get_bloginfo('name')).'\', \''.$post->ID.'\', \''.js_escape($val['title']).'\', \''.js_escape($val['artist']).'\'); return false;">'.__('Play in Popup', 'podpress').'</a>';
					$dividerNeeded = true;
				}

				if($val['enableDownload'] && $podPressTemplateData['showDownloadText'] == 'enabled') {
					if($dividerNeeded) {
						$podPressDownloadlinks .= $divider;
					}
					if ( 0 === strpos($val['type'], 'embed_') ) {
						$podPressDownloadlinks .= '<a href="'.$val['URI'].'"'.$target_attribute.' class="podpress_downloadlink podpress_downloadlink_'.$val['type'].'">'.__('Direct Link', 'podpress').'</a>';
						$val['stats'] = false;
					} else {
						$podPressDownloadlinks .= '<a href="'.$val['URI'].'"'.$target_attribute.' class="podpress_downloadlink podpress_downloadlink_'.$val['type'].'">'.__('Download', 'podpress').'</a>';
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
					$podPressDownloadlinks .= ' <span class="podpress_downloadnr podpress_downloadnr_'.$val['type'].'">'.__('Downloads', 'podpress').' '.$val['stats']['total'].'</span>';
					$dividerNeeded = true;
				}

			}
			
			$podPressDownloadlinks .= '</div>'.$podpress_downloadlinks_section_end;
			$podPressContentAll .= $podPressContent.apply_filters('podpress_downloadlinks', $podPressDownloadlinks);
		}
		if ($podPress->settings['contentAutoDisplayPlayer']) {
			if ( TRUE === defined('PODPRESS_ADD_CDATA_SECTION_TO_POSTSCRIPT') AND TRUE === constant('PODPRESS_ADD_CDATA_SECTION_TO_POSTSCRIPT') ) {
				$cdata_section_begin = '/* <![CDATA[ */ ';
				$cdata_section_end = ' /* ]]> */';
			} else {
				$cdata_section_begin = '';
				$cdata_section_end = '';
			}
			$podPressPlayBlockScripts = apply_filters('podpress_post_scriptblock', '<script type="text/javascript">' . $cdata_section_begin . $podPressPlayBlockScripts . $cdata_section_end . '</script>');
		}
		
		return apply_filters('podpress_post_content', $podpress_section_begin.'<div class="podPress_content podPress_content_'.$val['type'].'">'.$podPressContentAll.'</div>'."\n".$podPressPlayBlockScripts.$podpress_section_end);
	}
}

if (!function_exists('podPress_defaultTitles')) {
	function podPress_defaultTitles($filetype) {
		switch($filetype) {
			case 'audio_mp3':
			case 'audio_ogg':
			case 'audio_opus':
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
			case 'ebook_epub':
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