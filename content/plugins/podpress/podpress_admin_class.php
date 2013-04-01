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
	class podPressAdmin_class extends podPress_class
	{
		function podPressAdmin_class() {
			parent::cleanup_itunes_keywords();
			$this->podPress_class();
			return;
		}

		/*************************************************************/
		/* Functions for editing and saving posts                    */
		/*************************************************************/

		function page_form() {
			return $this->post_form('page');
		}

		function post_form($entryType = 'post') {
			global $wp_version;
			
			if ( TRUE == version_compare($wp_version, '2.7', '>=') AND TRUE == version_compare($wp_version, '2.8', '<')) {// for WP 2.7.x (because the plugins_url() worked differently in WP 2.7.x)
				$plugins_url = plugins_url('podpress', __FILE__);
			} else { 
				$plugins_url = plugins_url('', __FILE__);
			}
			
			if(!is_object($GLOBALS['post']) && isset($GLOBALS['post_cache'][$GLOBALS['post']])) {
				$post = $GLOBALS['post_cache'][$GLOBALS['post']];
			} else {
				$post = $GLOBALS['post'];
			}

			$post = $this->addPostData($post, true);
			
			if(($_GET['action'] == 'edit')) {
				$post_id = $_GET['post'];

				if(!is_array($post->podPressMedia)) {
					$post->podPressMedia = array();
				}
			}

			$files = array();
			$mediaFilePath = stripslashes($this->settings['mediaFilePath']);
			if(@is_dir($mediaFilePath)) {
				$dh  = opendir($mediaFilePath);
				while (false !== ($filename = readdir($dh))) {
					if($filename != '.' && $filename != '..' && !is_dir($mediaFilePath.'/'.$filename) && !in_array(podPress_getFileExt($filename), array('php', 'html'))) {
						$files[] = $filename;
					}
				}
				natcasesort($files);
			}

			if($this->settings['enablePodangoIntegration']) {
				if(!empty($post->podPressPostSpecific['PodangoEpisodeID'])) {
					if(empty($post->podPressPostSpecific['PodangoMediaFileID'])) {
						$x = $this->podangoapi->GetEpisode($post->podPressPostSpecific['PodangoEpisodeID']);
						$post->podPressPostSpecific['PodangoMediaFileID'] = $x['MediaFileId'];
						unset($x);
					}
					$podangoMediaFiles = $this->podangoapi->GetMediaFile($post->podPressPostSpecific['PodangoMediaFileID']);
				} else {
					$podangoMediaFiles = $this->podangoapi->GetMediaFiles();
				}
			}


			if (FALSE == isset($this->settings['videoDefaultPlayerSize_x'])) {
				$this->settings['videoDefaultPlayerSize_x'] = 320;
			}
			if (FALSE == isset($this->settings['videoDefaultPlayerSize_y'])) {
				$this->settings['videoDefaultPlayerSize_y'] = 240;
			}
			echo '<input type="hidden" id="podpress_mediadefault_dimensionW" value="'.$this->settings['videoDefaultPlayerSize_x'].'" />'."\n";
			echo '<input type="hidden" id="podpress_mediadefault_dimensionH" value="'.$this->settings['videoDefaultPlayerSize_y'].'" />'."\n";
			
			echo '<script type="text/javascript">'."\n";
			echo "var podPressMaxMediaFiles = ".$this->settings['maxMediaFiles'].";\n";
			$newMediaDefaults = array();
			echo "var newMediaDefaults = new Array();\n";
			$newMediaDefaults['URI'] = '';
			echo "newMediaDefaults['URI'] = '".$newMediaDefaults['URI']."';\n";
			$newMediaDefaults['title'] = '';
			echo "newMediaDefaults['title'] = '".$newMediaDefaults['title']."';\n";
			$newMediaDefaults['type'] = 'audio_mp3';
			echo "newMediaDefaults['type'] = '".$newMediaDefaults['type']."';\n";
			$newMediaDefaults['size'] = '';
			echo "newMediaDefaults['size'] = '".$newMediaDefaults['size']."';\n";
			$newMediaDefaults['duration'] = '';
			echo "newMediaDefaults['duration'] = '".$newMediaDefaults['duration']."';\n";
			$newMediaDefaults['dimensionW'] = strval($this->settings['videoDefaultPlayerSize_x']);
			echo "newMediaDefaults['dimensionW'] = '".$newMediaDefaults['dimensionW']."';\n";
			$newMediaDefaults['dimensionH'] = strval($this->settings['videoDefaultPlayerSize_y']);
			echo "newMediaDefaults['dimensionH'] = '".$newMediaDefaults['dimensionH']."';\n";
			$newMediaDefaults['previewImage'] = podPress_url().'images/vpreview_center.png';
			echo "newMediaDefaults['previewImage'] = '".$newMediaDefaults['previewImage']."';\n";
			$newMediaDefaults['rss'] = 'false';
			echo "newMediaDefaults['rss'] = ".$newMediaDefaults['rss'].";\n";
			$newMediaDefaults['atom'] = 'true';
			echo "newMediaDefaults['atom'] = ".$newMediaDefaults['atom'].";\n";
			$newMediaDefaults['feedonly'] = 'true';
			echo "newMediaDefaults['feedonly'] = ".$newMediaDefaults['feedonly'].";\n";
			$newMediaDefaults['disablePlayer'] = 'false';
			echo "newMediaDefaults['disablePlayer'] = ".$newMediaDefaults['disablePlayer'].";\n";
			$newMediaDefaults['disablePreview'] = 'false';
			echo "newMediaDefaults['disablePreview'] = ".$newMediaDefaults['disablePreview'].";\n";
			$newMediaDefaults['content_level'] = 'free';
			echo "newMediaDefaults['content_level'] = '".$newMediaDefaults['content_level']."';\n";
			$newMediaDefaults['showme'] = 'false';
			echo "newMediaDefaults['showme'] = ".$newMediaDefaults['showme'].";\n";

			if(empty($post->podPressMedia)) {
				$num = 0;
			} else {
				$num = count($post->podPressMedia);
			}

			while ($num < $this->settings['maxMediaFiles']) {
				$post->podPressMedia[$num] = $newMediaDefaults;
				$num++;
			}

			$num = 0;
			while ($num < $this->settings['maxMediaFiles']) {
				if(!isset($post->podPressMedia[$num]['showme'])) {
					$post->podPressMedia[$num]['showme'] = 'true';
				}
				if($post->podPressMedia[$num]['showme'] == 'false') {
					$num++;
					continue;
				}

				if($this->settings['enablePodangoIntegration']) {
					if($podangoMediaFiles[$post->podPressPostSpecific['PodangoMediaFileID']]['Filename'] == basename($post->podPressMedia[$num]['URI'])) {
						$post->podPressMedia[$num]['URI'] = 'Podango:'.$podangoMediaFiles[$post->podPressPostSpecific['PodangoMediaFileID']]['Podcast'].':'.$podangoMediaFiles[$post->podPressPostSpecific['PodangoMediaFileID']]['ID'].':'.$podangoMediaFiles[$post->podPressPostSpecific['PodangoMediaFileID']]['EpisodeID'].':'.$podangoMediaFiles[$post->podPressPostSpecific['PodangoMediaFileID']]['Filename'];
					}
				}

				if($post->podPressMedia[$num]['rss'] == 'on') {
					$post->podPressMedia[$num]['rss'] = 'true';
				} else {
					$post->podPressMedia[$num]['rss'] = 'false';
				}
				if($post->podPressMedia[$num]['atom'] == 'on') {
					$post->podPressMedia[$num]['atom'] = 'true';
				} else {
					$post->podPressMedia[$num]['atom'] = 'false';
				}
				if($post->podPressMedia[$num]['feedonly'] == 'on') {
					$post->podPressMedia[$num]['feedonly'] = 'true';
				} else {
					$post->podPressMedia[$num]['feedonly'] = 'false';
				}
				if(!isset($post->podPressMedia[$num]['disablePlayer']) || $post->podPressMedia[$num]['disablePlayer'] == false || $post->podPressMedia[$num]['disablePlayer'] == 'false') {
					$post->podPressMedia[$num]['disablePlayer'] = 'false';
				} else {
					$post->podPressMedia[$num]['disablePlayer'] = 'true';
				}

				if(!isset($post->podPressMedia[$num]['disablePreview']) || $post->podPressMedia[$num]['disablePreview'] == false || $post->podPressMedia[$num]['disablePreview'] == 'false') {
					$post->podPressMedia[$num]['disablePreview'] = 'false';
				} else {
					$post->podPressMedia[$num]['disablePreview'] = 'true';
				}

				if($post->podPressMedia[$num]['premium_only'] == 'on' || $post->podPressMedia[$num]['premium_only'] == true) {
					$post->podPressMedia[$num]['content_level'] = 'premium_content';
				}
				if(!isset($post->podPressMedia[$num]['content_level'])) {
					$post->podPressMedia[$num]['content_level'] = 'free';
				}
				echo "\n";
				echo "podPressAddMediaFile(".$post->podPressMedia[$num]['showme'].", '".js_escape($post->podPressMedia[$num]['URI'])."', '".js_escape($post->podPressMedia[$num]['URI_torrent'])."', '".js_escape($post->podPressMedia[$num]['title'])."', '".$post->podPressMedia[$num]['type']."', '".$post->podPressMedia[$num]['size']."', '".$post->podPressMedia[$num]['duration']."', '".$post->podPressMedia[$num]['dimensionW']."', '".$post->podPressMedia[$num]['dimensionH']."', '".$post->podPressMedia[$num]['previewImage']."', ".$post->podPressMedia[$num]['rss'].", ".$post->podPressMedia[$num]['atom'].", ".$post->podPressMedia[$num]['feedonly'].", ".$post->podPressMedia[$num]['disablePlayer'].", '".$post->podPressMedia[$num]['content_level']."');\n";
				$num++;
			}
			echo '</script>'."\n";


			echo '<input type="hidden" id="podPressMedia_defaultpreviewImage" value="'.PODPRESS_URL.'/images/vpreview_center.png" />'."\n";
			// NONCE_KEY has been introduced in WP 2.7 the first time
			echo '<input type="hidden" id="podPress_AJAX_sec" value="' . wp_create_nonce('Af|F07*wC7g-+OX$;|Z5;R@Pi]ZgoU|Zex8=`?mO-Mdvu+WC6l=6<O^2d~+~U3MM') . '" />'."\n";

			echo '<div id="podPressstuff" class="dbx-group">'."\n";
			echo '	<fieldset id="podpresscontent" class="dbx-box">'."\n";
			if ( 'page' == $entryType ) {
				echo '		<h3 class="dbx-handle">'.__('podPress - podcasting settings of this page', 'podpress').'</h3> '."\n";
			} else {
				echo '		<h3 class="dbx-handle">'.__('podPress - podcasting settings of this post', 'podpress').'</h3> '."\n";
			}
			
			echo '		<div class="dbx-content" id="podPress_mediaFileList">'."\n";
			echo '			<strong>'.__('Podcasting Files:', 'podpress').'</strong><br/>'."\n";
			
			// debug start
			$num = 0;
			while ($num < $this->settings['maxMediaFiles']) {
				if(!isset($post->podPressMedia[$num])) {
					$num++;
					continue;
				}
				$thisMedia = $post->podPressMedia[$num];
				if($thisMedia['showme'] == 'true') {
					$display_text = 'block';
				}	else {
					$display_text = 'none';
				}
				
				echo '     	<div id="podPressMediaFileContainer_'.$num.'" class="wrap" style="visibility: visible; display: '.$display_text.';">'."\n";

				echo '				<table border="0" style="width: 100%;"><tr><td style="border-right-style: none; text-align: left;"><label><strong>'.__('Media File:', 'podpress').'</strong> </label></td>';
				echo '				<td style="border-left-style: none; text-align: right;">';
				echo '						<input type="button" value="'.__('Move Up', 'podpress').'" onclick="podPressMoveFile('.$num.', \'up\'); podPressDisplayMediaFiles();"/>'."\n";
				echo '						<input type="button" value="'.__('Move Down', 'podpress').'" onclick="podPressMoveFile('.$num.', \'down\'); podPressDisplayMediaFiles();"/>'."\n";
				echo '						<input type="button" name="podPressAddAnother" value="'.__('Remove File', 'podpress').'" onclick="podPressRemoveFile('.$num.'); podPressDisplayMediaFiles();"/>';
				echo '				</td></tr></table>'."\n";
				echo '				<table border="0">'."\n";
				echo '					<tr>'."\n";
				echo '						<td>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_URI">'.__('Location', 'podpress').': </label>'."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				if(!empty($files) || $this->settings['enablePodangoIntegration']) {
					$fileOptionList = '';
					echo '						<select name="podPressMedia['.$num.'][URI]" id="podPressMedia_'.$num.'_URI" width="35" onchange="javascript: if(this.value==\'!\') { podPress_customSelectVal(this, \'Specifiy URL.\'); } podPressMediaFiles['.$num.'][\'URI\'] = this.value; podPressDetectType('.$num.');">'."\n";
					echo '							<option value="!">'.__('Specify URL ...', 'podpress').'</option>'."\n";
					$fileSelected = false;
					if($this->settings['enablePodangoIntegration']) {
						$podangoOptGroup = '';
						$podangoFirstOptGroup = true;
						foreach($podangoMediaFiles as $podangoMediaFile) {
							if(!empty($podangoMediaFile['EpisodeID']) && $podangoMediaFile['EpisodeID'] != $post->podPressPostSpecific['PodangoEpisodeID'] && $post->post_title != $podangoMediaFile['EpisodeTitle']) {
								continue;
							}
							if($podangoOptGroup != $podangoMediaFile['Podcast']) {
								$podangoOptGroup = $podangoMediaFile['Podcast'];
								if(!$podangoFirstOptGroup) {
									echo "							</optgroup>\n";
								}
								$x = $this->podangoapi->GetPodcast($podangoMediaFile['Podcast'], true);
								echo '							<optgroup name="PodangoOptGroup'.$podangoMediaFile['Podcast'].'" label="Podango Podcast: '.$x['Title'].'">'."\n";
								unset($x);
							}
							$key = 'Podango:'.$podangoMediaFile['Podcast'].':'.$podangoMediaFile['ID'].':'.$podangoMediaFile['EpisodeID'].':'.$podangoMediaFile['Filename'];
							if($key == $thisMedia['URI']) {
								$xSelected = ' selected="selected"';
								$fileSelected = true;
							} else {
								$xSelected = '';
							}
							echo '								<option value="'.$key.'"'.$xSelected.'>'.$podangoMediaFile['Filename'].'</option>'."\n";
						}
						echo "							</optgroup>\n";
						echo '							<optgroup name="LocallyHosted" label="Locally Hosted">'."\n";

					}
					foreach ($files as $key=>$val) {
						if(is_numeric($key)) {
							$key = $val;
						}
						if($key == $thisMedia['URI']) {
							$xSelected = ' selected="selected"';
							$fileSelected = true;
						} else {
							$xSelected = '';
						}
						if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) { // html_entity_decode can not handle the charset UTF-8 in most of the PHP 4 version
							$fileOptionList .= '							<option value="'.attribute_escape(htmlentities($key)).'"'.$xSelected.'>'.htmlentities(podPress_stringLimiter($val, 100, true)).'</option>'."\n";
						} else {
							if ( FALSE === stristr(PHP_OS, 'WIN') ) { 
								$fileOptionList .= '							<option value="'.attribute_escape(htmlentities($key, ENT_COMPAT, $blog_charset)).'"'.$xSelected.'>'.htmlentities(podPress_stringLimiter($val, 100, true), ENT_COMPAT, $blog_charset).'</option>'."\n";
							} else {
								$fileOptionList .= '							<option value="'.attribute_escape(htmlentities($key)).'"'.$xSelected.'>'.htmlentities(podPress_stringLimiter($val, 100, true)).'</option>'."\n";
							}
						}
					}
					if ( !$fileSelected ) {
						if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) { // html_entity_decode can not handle the charset UTF-8 in most of the PHP 4 version
							echo '							<option value="'.attribute_escape(htmlentities($thisMedia['URI'])).'" selected="selected">'.htmlentities(podPress_stringLimiter($thisMedia['URI'], 100, true)).'</option>'."\n";
						} else {
							if ( FALSE === stristr(PHP_OS, 'WIN') ) { 
								echo '							<option value="'.attribute_escape(htmlentities($thisMedia['URI']), ENT_COMPAT, $blog_charset).'" selected="selected">'.htmlentities(podPress_stringLimiter($thisMedia['URI'], 100, true), ENT_COMPAT, $blog_charset).'</option>'."\n";
							} else {
								echo '							<option value="'.attribute_escape($thisMedia['URI']).'" selected="selected">'.htmlentities(podPress_stringLimiter($thisMedia['URI'], 100, true), ENT_COMPAT, $blog_charset).'</option>'."\n";
							}
						}
					}
					echo $fileOptionList;
					unset($fileOptionList);
					if($this->settings['enablePodangoIntegration']) {
						echo "							</optgroup>\n";
					}

					echo '						</select>'."\n";
					echo '							<input type="hidden" id="podPressMedia_'.$num.'_cleanURI" value="no" />'."\n";
				} else {
					echo '							<input type="text" id="podPressMedia_'.$num.'_URI" name="podPressMedia['.$num.'][URI]" size="40" value="'.attribute_escape($thisMedia['URI']).'" onchange="javascript: podPressMediaFiles['.$num.'][\'URI\'] = this.value; podPressDetectType('.$num.');" />'."\n";
					echo '							<span id="podPressMedia_'.$num.'_URI_chrWarning" class="podpress_notice podPressMedia_URI_chrWarning"> '.sprintf(__('<strong>Notice:</strong> It is not recommended to use other than these characters: %1$s or whitespaces in file and folder names.', 'podpress'), 'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789 .:/_-\ ').'</span>'."\n";
					echo '							<input type="hidden" id="podPressMedia_'.$num.'_cleanURI" value="yes" />'."\n";
				}
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				if($this->settings['enableTorrentCasting']) {
					echo '					<tr>'."\n";
					echo '						<td>'."\n";
					echo '							<label for="podPressMedia_'.$num.'_URItorrent">'.__('.torrent Location', 'podpress').'</label>: '."\n";
					echo '						</td>'."\n";
					echo '						<td>'."\n";
					echo '							<input type="text" id="podPressMedia_'.$num.'_URItorrent" name="podPressMedia['.$num.'][URI_torrent]" size="40" value="'.attribute_escape($thisMedia['URI_torrent']).'" onchange="javascript: podPressMediaFiles['.$num.'][\'URI_torrent\'] = this.value;" />'."\n";
					echo '						</td>'."\n";
					echo '					</tr>'."\n";
				}
				echo '					<tr>'."\n";
				echo '						<td>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_title">'.__('Title', 'podpress').'</label>: '."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="text" id="podPressMedia_'.$num.'_title" name="podPressMedia['.$num.'][title]" size="40" value="'.attribute_escape($thisMedia['title']).'" onchange="javascript: podPressMediaFiles['.$num.'][\'title\'] = this.value;" />'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr>'."\n";
				echo '						<td>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_type">'.__('Type', 'podpress').'</label>: '."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="podPressMedia_'.$num.'_type" name="podPressMedia['.$num.'][type]" onchange="javascript: podPressMediaFiles['.$num.'][\'type\'] = this.value; podPressAdjustMediaFieldsBasedOnType('.$num.');" >'."\n"; podPress_mediaOptions();	echo '</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="podPressMediaSizeWrapper_'.$num.'" >'."\n";
				echo '						<td>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_size">'.__('File Size', 'podpress').'</label>: '."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="text" id="podPressMedia_'.$num.'_size" name="podPressMedia['.$num.'][size]" size="10" value="'.$thisMedia['size'].'" onchange="javascript: podPressMediaFiles['.$num.'][\'size\'] = this.value;"/> '.__('[in byte]', 'podpress').' <img src="'.$plugins_url.'/images/ajax-loader.gif" id="podPressMedia_'.$num.'_size_loadimg" class="podpress_ajax_loader_img" /><input type="button" id="podPressMedia_'.$num.'_size_detectbutton" value="'.__('Auto Detect', 'podpress').'" onclick="podPress_getfileinfo(\'size\', '.$num.');"/>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="podPressMediaDurationWrapper_'.$num.'" style="display: none;">'."\n";
				echo '						<td>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_duration">'.__('Duration', 'podpress').'</label>: '."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="text" id="podPressMedia_'.$num.'_duration" name="podPressMedia['.$num.'][duration]" size="10" value="'.$thisMedia['duration'].'" onchange="javascript: podPressMediaFiles['.$num.'][\'duration\'] = this.value;"/> <span class="podpress_abbr" title="'.__('hours:minutes:seconds - for example: 2:45:10 or 34:01 or 1:36 or 120:47 or 0:10', 'podpress').'">'.__('[hh:mm:ss]', 'podpress').'</span> <img src="'.$plugins_url.'/images/ajax-loader.gif" id="podPressMedia_'.$num.'_duration_loadimg" class="podpress_ajax_loader_img" /><input type="button" id="podPressMedia_'.$num.'_duration_detectbutton" value="'.__('Auto Detect', 'podpress').'" onclick="podPress_getfileinfo(\'duration\', '.$num.');"/> ('.sprintf(__('This may take some time for remote files. %1$s', 'podpress'), '<span class="podpress_abbr" title="'.__('If the file is not on the same server as your blog then podPress will attempt to download the file in order to get this information with the help of getID3() which is only able to retrieve ID3 tags from local files. podPress removes the temporary download file at the end of this process. It is likely that this feature works only for relative small files because the download is probably limited by execution time and memory limits on the server of your blog.', 'podpress').'">'.__('Because ...', 'podpress').'</span>').')'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				if(empty($thisMedia['previewImage'])) {
					$thisMedia['previewImage'] = podPress_url().'images/vpreview_center.png';
				}
				echo '					<tr id="podPressMediaPreviewImageWrapper_'.$num.'" style="display: none;">'."\n";
				echo '						<td>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_previewImage">'.__('Preview Image URL', 'podpress').'</label>: '."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="text" id="podPressMedia_'.$num.'_previewImage" name="podPressMedia['.$num.'][previewImage]" size="40" value="'.attribute_escape($thisMedia['previewImage']).'" onchange="javascript: podPressPreviewImageOnChange('.$num.', this.value);" />'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="podPressMediaPreviewImageDisplayWrapper_'.$num.'" style="display: none;">'."\n";
				echo '						<td>'."\n";
				echo '							'.__('Preview Image', 'podpress').': '."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<div id="podPressPlayerSpace_'.$num.'"></div>'."\n";
				echo '<script type="text/javascript"><!--'."\n";
				echo "	document.getElementById('podPressPlayerSpace_".$num."').innerHTML = podPressGenerateVideoPreview (".$num.", '', ".$thisMedia['dimensionW'].", ".$thisMedia['dimensionH'].", document.getElementById('podPressMedia_".$num."_previewImage').value, true);\n";
				echo "--></script>\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr id="podPressMediaDimensionWrapper_'.$num.'">'."\n";
				echo '						<td>'."\n";
				echo '							'.__('Dimensions', 'podpress').' (WxH): '."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="text" id="podPressMedia_'.$num.'_dimensionW" name="podPressMedia['.$num.'][dimensionW]" size="5" value="'.$thisMedia['dimensionW'].'" onchange="javascript: podPressMediaFiles['.$num.'][\'dimensionW\'] = this.value; podPressUpdateDimensionList(\''.$num.'\');" />x<input type="text" id="podPressMedia_'.$num.'_dimensionH" name="podPressMedia['.$num.'][dimensionH]" size="5" value="'.$thisMedia['dimensionH'].'" onchange="javascript: podPressMediaFiles['.$num.'][\'dimensionH\'] = this.value; podPressUpdateDimensionList(\''.$num.'\')" /> '."\n";
				echo '							 '.__('or', 'podpress').' ';
				echo '							<select id="podPressMedia_'.$num.'_dimensionList" onchange="javascript: podPressUpdateDimensions(\''.$num.'\', this.value);">'."\n"; podPress_videoDimensionOptions($thisMedia['dimensionW'].':'.$thisMedia['dimensionH']);	echo '</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";

				if($post->post_status == 'static') {
					echo '					<tr style="display: none;">'."\n";
				} else {
					echo '					<tr>'."\n";
				}
				echo '						<td nowrap="nowrap">'."\n";
				echo '							'.__('Included in', 'podpress').': '."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							&nbsp;<label for="podPressMedia_'.$num.'_rss">'.__('RSS2', 'podpress').'</label> <input type="checkbox" id="podPressMedia_'.$num.'_rss" name="podPressMedia['.$num.'][rss]" onchange="javascript: podPressMediaFiles['.$num.'][\'rss\'] = this.checked; podPressSetSingleRSS('.$num.');" />'."\n";
				echo '							&nbsp;<label for="podPressMedia_'.$num.'_atom">'.__('ATOM', 'podpress').'</label> <input type="checkbox" id="podPressMedia_'.$num.'_atom" name="podPressMedia['.$num.'][atom]" onchange="javascript: podPressMediaFiles['.$num.'][\'atom\'] = this.checked;" />'."\n";
				echo '							&nbsp;<label for="podPressMedia_'.$num.'_feedonly">'.__('Feed Only', 'podpress').'</label> <input type="checkbox" id="podPressMedia_'.$num.'_feedonly" name="podPressMedia['.$num.'][feedonly]" onchange="javascript: podPressMediaFiles['.$num.'][\'feedonly\'] = this.checked;" />'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				if($this->settings['enablePremiumContent']) {
					echo '					<tr>'."\n";
					echo '						<td nowrap="nowrap">'."\n";
					echo '							<label for="podPressMedia_'.$num.'_content_level">'.__('Subscription', 'podpress').'</label>:';
					echo '						</td>'."\n";
					echo '						<td>'."\n";
					echo '							<select id="podPressMedia_'.$num.'_content_level" name="podPressMedia['.$num.'][content_level]" onchange="javascript: podPressMediaFiles['.$num.'][\'content_level\'] = this.value;">'."\n";
					echo '								<option value="free" '; if(empty($thisMedia['content_level']) || $thisMedia['content_level'] == 'free') { echo 'selected="selected"';	}	echo '>'.__('Free', 'podpress').'</option>'."\n";
					foreach (podPress_getCapList(true) as $cap) {
						if(substr($cap, -8, 8) == '_content') {
							echo '								<option value="'.$cap.'" '; if($thisMedia['content_level'] == $cap) { echo 'selected="selected"';	}	echo '>'.__(podPress_getCapName($cap), 'podpress').'</option>'."\n";
						}
					}
					echo '							</select>'."\n";
					echo '						</td>'."\n";
					echo '					</tr>'."\n";
				}
				echo '					<tr>'."\n";
				echo '						<td nowrap="nowrap">'."\n";
				echo '							<label for="podPressMedia_'.$num.'_disablePlayer">'.__('Disable Player', 'podpress').'</label>:'."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="checkbox" id="podPressMedia_'.$num.'_disablePlayer" name="podPressMedia['.$num.'][disablePlayer]"'; if($thisMedia['disablePlayer'] != 'false') { echo 'checked="checked" '; } echo ' onchange="javascript: podPressMediaFiles['.$num.'][\'disablePlayer\'] = this.checked;" />'."\n";
				echo '							&nbsp;&nbsp; '.__('(Use if this media file is not compatible with one of the included players.)', 'podpress')."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr>'."\n";
				echo '						<td nowrap="nowrap">'."\n";
				echo '							<label for="podPressMedia_'.$num.'_disablePreview">'.__('Disable Preview Player', 'podpress').'</label>:'."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="checkbox" id="podPressMedia_'.$num.'_disablePreview" name="podPressMedia['.$num.'][disablePreview]"'; if($thisMedia['disablePreview'] != 'false') { echo 'checked="checked" '; } echo ' onchange="javascript: podPressMediaFiles['.$num.'][\'disablePreview\'] = this.checked;" />'."\n";
				echo '							&nbsp;&nbsp; '.__('(Use this to disable the "Click to Play" preview player.)', 'podpress')."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				$actionMedia = $thisMedia;
				$actionMedia['num'] = $num;
				do_action('podPress_customMediaData', array($actionMedia));
				echo '					<tr id="podPressMedia_'.$num.'_id3tags_details_row">'."\n";
				echo '						<td style="vertical-align: top;" nowrap="nowrap">'."\n";
				echo '							'.__('Tag (ID3) Info', 'podpress').":\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<img src="'.$plugins_url.'/images/ajax-loader.gif" id="podPressMedia_'.$num.'_id3tags_loadimg" class="podpress_ajax_loader_img" /><input type="button" id="podPressMedia_'.$num.'_id3tags_detectbutton" value="'.__('Show','podpress').'" onclick="javascript: podPressShowHideID3Tags('.$num.');" /> ('.sprintf(__('This may take some time for remote files. %1$s', 'podpress'), '<span class="podpress_abbr" title="'.__('If the file is not on the same server as your blog then podPress will attempt to download the file in order to get this information with the help of getID3() which is only able to retrieve ID3 tags from local files. podPress removes the temporary download file at the end of this process. It is likely that this feature works only for relative small files because the download is probably limited by execution time and memory limits on the server of your blog.', 'podpress').'">'.__('Because ...', 'podpress').'</span>').')'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr>'."\n";
				echo '						<td colspan="2">'."\n";
				echo '							<div id="podPressMedia_'.$num.'_id3tags" style="display: none; vertical-align: top;">'."\n";
				echo '							</div>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '				</table>'."\n";
				
				echo '			</div>'."\n";

				$num++;
			}


			echo '	<h3>'.sprintf(__('To control player location in your post, you may put %1$s where you want it to appear. You can choose the default postion on the general settings page of podPress.', 'podpress'), $this->podcasttag).'</h3> '."\n";
			echo '			<input type="button" name="podPressAddAnother" value="'.__('Add Media File','podpress').'" onclick="javascript: podPressAddMediaFile(true, \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'new\', true, false, false, \'free\'); podPressDisplayMediaFiles();"/>'."\n";
			if($entryType != 'page') {
				echo '			<br/>'."\n";
				echo '			<strong>'.__('Post specific settings for iTunes', 'podpress').': </strong>'."\n";
				echo '			<input type="button" name="iTunesSpecificSettings_button" id="iTunesSpecificSettings_button" value="Show" onclick="javascript: podPressShowHideDiv(\'iTunesSpecificSettings\');"/>'."\n";

				echo '     	<div class="wrap" id="iTunesSpecificSettings" style="display: none;">'."\n";
				echo '				<table border="0">'."\n";
				echo '					<tr>'."\n";
				echo '						<td width="1%" nowrap="nowrap">'."\n";
				echo '							<label for="iTunesSubtitleChoice">'.__('iTunes:Subtitle', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'iTunesSubtitleHelp\');">(?)</a>:'."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesSubtitleChoice" name="iTunesSubtitleChoice" onchange="javascript: if(this.value == \'Custom\') { document.getElementById(\'iTunesSubtitleWrapper\').style.display=\'\'; } else { document.getElementById(\'iTunesSubtitleWrapper\').style.display=\'none\'; }">'."\n";
				echo '								<option value="PostExcerpt" '; if($post->podPressPostSpecific['itunes:subtitle'] == '##PostExcerpt##') { echo 'selected="selected"';	}	echo '>'.__('Use the excerpt', 'podpress').'</option>'."\n";
				echo '								<option value="Custom" '; if($post->podPressPostSpecific['itunes:subtitle'] != '##PostExcerpt##') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="iTunesSubtitleHelp" style="display: none;">'."\n";
				echo '						<td colspan="2">'.sprintf(__('"%1$s" (default) podPress takes the first 255 characters from the excerpt of the excerpt and if there is none from the blog Post text.', 'podpress'),__('Use the excerpt', 'podpress')).'</td>'."\n";
				echo '					</tr>'."\n";
				if($post->podPressPostSpecific['itunes:subtitle'] == '##PostExcerpt##') { $tempShowMe = 'style="display: none;"';$post->podPressPostSpecific['itunes:subtitle'] = ''; } else { $tempShowMe = ''; }
				echo '					<tr id="iTunesSubtitleWrapper" '.$tempShowMe.'>'."\n";
				echo '						<td width="1%" nowrap="nowrap">&nbsp;</td>'."\n";
				echo '						<td>'."\n";
				echo '							<textarea name="iTunesSubtitle" rows="4" cols="40">'.stripslashes($post->podPressPostSpecific['itunes:subtitle']).'</textarea>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr>'."\n";
				echo '						<td width="1%" nowrap="nowrap">'."\n";
				echo '							<label for="iTunesSummaryChoice">'.__('iTunes:Summary', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'iTunesSummaryHelp\');">(?)</a>:'."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesSummaryChoice" name="iTunesSummaryChoice" onchange="javascript: if(this.value == \'Custom\') { document.getElementById(\'iTunesSummaryWrapper\').style.display=\'\'; } else { document.getElementById(\'iTunesSummaryWrapper\').style.display=\'none\'; }">'."\n";
				echo '								<option value="PostExcerpt" '; if($post->podPressPostSpecific['itunes:summary'] == '##PostExcerpt##') { echo 'selected="selected"';	}	echo '>'.__('Use the excerpt', 'podpress').'</option>'."\n";
				echo '								<option value="PostContentShortened" '; if($post->podPressPostSpecific['itunes:summary'] == '##PostContentShortened##') { echo 'selected="selected"';	}	echo '>'.__('autom. excerpt of the post content', 'podpress').'</option>'."\n";
				echo '								<option value="Global" '; if($post->podPressPostSpecific['itunes:summary'] == '##Global##') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').'</option>'."\n";
				echo '								<option value="Custom" '; if($post->podPressPostSpecific['itunes:summary'] != '##Global##' && $post->podPressPostSpecific['itunes:summary'] != '##PostExcerpt##' && $post->podPressPostSpecific['itunes:summary'] != '##PostContentShortened##') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="iTunesSummaryHelp" style="display: none;">'."\n";
				echo '						<td colspan="2">'.sprintf(__('"%1$s" (default) podPress takes the excerpt. If you have not written an excerpt then it takes a part from the blog Post text.', 'podpress'),__('Use the excerpt', 'podpress')).'</td>'."\n";
				echo '					</tr>'."\n";
				if($post->podPressPostSpecific['itunes:summary'] == '##Global##' || $post->podPressPostSpecific['itunes:summary'] == '##PostExcerpt##' || $post->podPressPostSpecific['itunes:summary'] ==  '##PostContentShortened##') { $tempShowMe = 'style="display: none;"';	$post->podPressPostSpecific['itunes:summary'] = ''; } else { $tempShowMe = ''; }
				echo '					<tr id="iTunesSummaryWrapper" '.$tempShowMe.'>'."\n";
				echo '						<td width="1%" nowrap="nowrap">&nbsp;</td>'."\n";
				echo '						<td>'."\n";
				echo '							<textarea name="iTunesSummary" rows="4" cols="40">'.stripslashes($post->podPressPostSpecific['itunes:summary']).'</textarea>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr>'."\n";
				echo '						<td width="1%" nowrap="nowrap">'."\n";
				echo '							<label for="iTunesKeywordsChoice">'.__('iTunes:Keywords', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'iTunesKeywordsHelp\');">(?)</a>:'."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesKeywordsChoice" name="iTunesKeywordsChoice" onchange="javascript: if(this.value == \'Custom\') { document.getElementById(\'iTunesKeywordsWrapper\').style.display=\'\'; } else { document.getElementById(\'iTunesKeywordsWrapper\').style.display=\'none\'; }">'."\n";
				echo '								<option value="WordPressCats" '; if($post->podPressPostSpecific['itunes:keywords'] == '##WordPressCats##') { echo 'selected="selected"';	}	echo '>'.__('Use WordPress Categories', 'podpress').'</option>'."\n";
				echo '								<option value="post_tags" '; if($post->podPressPostSpecific['itunes:keywords'] == '##post_tags##') { echo 'selected="selected"';	}	echo '>'.__('Use the tags of the post', 'podpress').'</option>'."\n";
				echo '								<option value="Global" '; if($post->podPressPostSpecific['itunes:keywords'] == '##Global##') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.podPress_stringLimiter(stripslashes($this->settings['iTunes']['keywords']), 40) .')</option>'."\n";
				echo '								<option value="Custom" '; if($post->podPressPostSpecific['itunes:keywords'] != '##Global##' && $post->podPressPostSpecific['itunes:keywords'] != '##WordPressCats##' && $post->podPressPostSpecific['itunes:keywords'] != '##post_tags##') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="iTunesKeywordsHelp" style="display: none;">'."\n";
				echo '						<td colspan="2">'.__('Not visible in iTunes, but used for searches.', 'podpress').'</td>'."\n";
				echo '					</tr>'."\n";
				if($post->podPressPostSpecific['itunes:keywords'] == '##Global##' || $post->podPressPostSpecific['itunes:keywords'] == '##WordPressCats##' || $post->podPressPostSpecific['itunes:keywords'] == '##post_tags##') { $tempShowMe = 'style="display: none;"';	$post->podPressPostSpecific['itunes:keywords'] = ''; } else { $tempShowMe = ''; }
				echo '					<tr id="iTunesKeywordsWrapper" '.$tempShowMe.'>'."\n";
				echo '						<td width="1%" nowrap="nowrap">&nbsp;</td>'."\n";
				echo '						<td>'."\n";
				echo '							'.__('a list of max. 12 comma separated words', 'podpress').'<br/><textarea name="iTunesKeywords" rows="4" cols="40">'.stripslashes($post->podPressPostSpecific['itunes:keywords']).'</textarea>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr>'."\n";
				echo '						<td width="1%" nowrap="nowrap">'."\n";
				echo '							<label for="iTunesAuthorChoice">'.__('iTunes:Author', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'iTunesAuthorHelp\');">(?)</a>:'."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesAuthorChoice" name="iTunesAuthorChoice" onchange="javascript: if(this.value == \'Custom\') { document.getElementById(\'iTunesAuthorWrapper\').style.display=\'\'; } else { document.getElementById(\'iTunesAuthorWrapper\').style.display=\'none\'; }">'."\n";
				echo '								<option value="Global" '; if($post->podPressPostSpecific['itunes:author'] == '##Global##') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.podPress_stringLimiter(ucfirst(stripslashes($this->settings['iTunes']['author'])), 40).')</option>'."\n";
				echo '								<option value="Custom" '; if($post->podPressPostSpecific['itunes:author'] != '##Global##') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr id="iTunesAuthorHelp" style="display: none;">'."\n";
				echo '						<td colspan="2">'.__('Used if this Author is different than the feeds author.', 'podpress').'</td>'."\n";
				echo '					</tr>'."\n";
				if($post->podPressPostSpecific['itunes:author'] == '##Global##') { $tempShowMe = 'style="display: none;"';	$post->podPressPostSpecific['itunes:author'] = ''; } else { $tempShowMe = ''; }
				echo '					<tr id="iTunesAuthorWrapper" '.$tempShowMe.'>'."\n";
				echo '						<td width="1%" nowrap="nowrap">&nbsp;</td>'."\n";
				echo '						<td><input type="text" name="iTunesAuthor" size="40" value="'.attribute_escape($post->podPressPostSpecific['itunes:author']).'" /></td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr>'."\n";
				echo '						<td width="1%" nowrap="nowrap">'."\n";
				echo '							<label for="iTunesExplicit">'.__('iTunes:Explicit', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'iTunesExplicitHelp\');">(?)</a>:'."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesExplicit" name="iTunesExplicit">'."\n";
				echo '								<option value="Default" '; if($post->podPressPostSpecific['itunes:explicit'] == 'Default') { echo 'selected="selected"';	}	echo '>'.__('Use Default', 'podpress').' ('.$this->settings['iTunes']['explicit'].')</option>'."\n";
				echo '								<option value="No" '; if($post->podPressPostSpecific['itunes:explicit'] == 'No') { echo 'selected="selected"';	}	echo '>'.__('No', 'podpress').'</option>'."\n";
				echo '								<option value="Yes" '; if($post->podPressPostSpecific['itunes:explicit'] == 'Yes') { echo 'selected="selected"';	}	echo '>'.__('Yes', 'podpress').'</option>'."\n";
				echo '								<option value="Clean" '; if($post->podPressPostSpecific['itunes:explicit'] == 'Clean') { echo 'selected="selected"';	}	echo '>'.__('Clean', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="iTunesExplicitHelp" style="display: none;">'."\n";
				echo '						<td colspan="2">'.__('Does your podcast contain explicit language or content which is not suitable for non-adult persons? (If you choose "Yes" or "Clean" then a corresponding notice will show up in iTunes (Explicit resp. Clean). If you choose "No" then there will be special notice.)', 'podpress').'</td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr>'."\n";
				echo '						<td width="1%" nowrap="nowrap">'."\n";
				echo '							<label for="iTunesBlock">'.__('iTunes:Block', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'itunesBlockHelp\');">(?)</a>:'."\n";
				echo '						</td>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesBlock" name="iTunesBlock">'."\n";
				echo '								<option value="Default" '; if($post->podPressPostSpecific['itunes:block'] == 'Default') { echo 'selected="selected"';	}	echo '>'.__('Use Default', 'podpress').' ('.$this->settings['iTunes']['block'].')</option>'."\n";
				echo '								<option value="No" '; if($post->podPressPostSpecific['itunes:block'] == 'No') { echo 'selected="selected"';	}	echo '>'.__('No', 'podpress').'</option>'."\n";
				echo '								<option value="Yes" '; if($post->podPressPostSpecific['itunes:block'] == 'Yes') { echo 'selected="selected"';	}	echo '>'.__('Yes', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="itunesBlockHelp" style="display: none;">'."\n";
				echo '						<td colspan="2">'.__('Prevent this episode or podcast from appearing in iTunes.', 'podpress').'</td>'."\n";
				echo '					</tr>'."\n";
				echo '				</table>'."\n";

				echo '			</div>'."\n";
			}
		
			if($this->settings['enablePodangoIntegration']) {
				echo "			<br/>\n";
				echo '			<strong>Podango File Uploader</strong>';
				if($this->settings['podangoDefaultPodcast'] == '##ALL##') {
					$podangoPodcastList = $this->podangoapi->GetPodcasts(true);
					echo ' <strong>for: </strong><select name="podPressPodangoPodcastID" id="podPressPodangoPodcastID" onChange="javascript: document.getElementById(\'podangoUploadFrame\').src=\''.$this->podangoapi->fileUploader.'?podcastId=\'+this.value">'."\n";
					foreach ($podangoPodcastList as $k=>$v) {
						if(!isset($podangoPodcastID)) {
							$podangoPodcastID = $k;
						}
						echo '						<option value="'.$k.'">'.$v['Title'].'</option>'."\n";
					}
					echo '					</select>'."\n";
				} else {
					$podangoPodcastID = $this->settings['podangoDefaultPodcast'];
				}
				echo '<br/>'."\n";
				echo '			<iframe src="'.$this->podangoapi->fileUploader.'?podcastId='.$podangoPodcastID.'" id="podangoUploadFrame" title="Podango Upload" border="0" width="560" height="110"> </iframe>'."\n";
			}
			echo '		</div>'."\n";
			echo '	<h3>'.__('End of podPress. File Uploading support is not part of podPress', 'podpress').'</h3> '."\n";
			echo '	</fieldset>'."\n";
			echo '</div><br/>'."\n";
			echo '<script type="text/javascript">podPressDisplayMediaFiles(); </script>'."\n";
		}


		// ################################################################
		// ### for modern WP versions:
		// ################################################################
		function post_form_wp25plus($entryType = 'post') {
			global $wp_version;
			$blog_charset = get_bloginfo('charset');
			
			if ( TRUE == version_compare($wp_version, '2.7', '>=') AND TRUE == version_compare($wp_version, '2.8', '<')) {// for WP 2.7.x (because the plugins_url() worked differently in WP 2.7.x)
				$plugins_url = plugins_url('podpress', __FILE__);
			} else { 
				$plugins_url = plugins_url('', __FILE__);
			}
			
			if(!is_object($GLOBALS['post']) && isset($GLOBALS['post_cache'][$GLOBALS['post']])) {
				$post = $GLOBALS['post_cache'][$GLOBALS['post']];
			} else {
				$post = $GLOBALS['post'];
			}

			$post = $this->addPostData($post, true);
			if(($_GET['action'] == 'edit')) {
				$post_id = $_GET['post'];

				if(!is_array($post->podPressMedia)) {
					$post->podPressMedia = array();
				}
			}

			$files = array();
			$mediaFilePath = stripslashes($this->settings['mediaFilePath']);
			if(@is_dir($mediaFilePath)) {
				$dh  = opendir($mediaFilePath);
				while (false !== ($filename = readdir($dh))) {
					if($filename != '.' && $filename != '..' && !is_dir($mediaFilePath.'/'.$filename) && !in_array(podPress_getFileExt($filename), array('php', 'html'))) {
						$files[] = $filename;
					}
				}
				natcasesort($files);
			}

			if($this->settings['enablePodangoIntegration']) {
				if(!empty($post->podPressPostSpecific['PodangoEpisodeID'])) {
					if(empty($post->podPressPostSpecific['PodangoMediaFileID'])) {
						$x = $this->podangoapi->GetEpisode($post->podPressPostSpecific['PodangoEpisodeID']);
						$post->podPressPostSpecific['PodangoMediaFileID'] = $x['MediaFileId'];
						unset($x);
					}
					$podangoMediaFiles = $this->podangoapi->GetMediaFile($post->podPressPostSpecific['PodangoMediaFileID']);
				} else {
					$podangoMediaFiles = $this->podangoapi->GetMediaFiles();
				}
			}

			if (FALSE == isset($this->settings['videoDefaultPlayerSize_x'])) {
				$this->settings['videoDefaultPlayerSize_x'] = 320;
			}
			if (FALSE == isset($this->settings['videoDefaultPlayerSize_y'])) {
				$this->settings['videoDefaultPlayerSize_y'] = 240;
			}

			echo '<input type="hidden" id="podpress_mediadefault_dimensionW" value="'.$this->settings['videoDefaultPlayerSize_x'].'" />'."\n";
			echo '<input type="hidden" id="podpress_mediadefault_dimensionH" value="'.$this->settings['videoDefaultPlayerSize_y'].'" />'."\n";

			echo '<script type="text/javascript">'."\n";
			echo "var podPressMaxMediaFiles = ".$this->settings['maxMediaFiles'].";\n";
			$newMediaDefaults = array();
			echo "var newMediaDefaults = new Array();\n";
			$newMediaDefaults['URI'] = '';
			echo "newMediaDefaults['URI'] = '".$newMediaDefaults['URI']."';\n";
			$newMediaDefaults['title'] = '';
			echo "newMediaDefaults['title'] = '".$newMediaDefaults['title']."';\n";
			$newMediaDefaults['type'] = 'audio_mp3';
			echo "newMediaDefaults['type'] = '".$newMediaDefaults['type']."';\n";
			$newMediaDefaults['size'] = '';
			echo "newMediaDefaults['size'] = '".$newMediaDefaults['size']."';\n";
			$newMediaDefaults['duration'] = '';
			echo "newMediaDefaults['duration'] = '".$newMediaDefaults['duration']."';\n";
			$newMediaDefaults['dimensionW'] = strval($this->settings['videoDefaultPlayerSize_x']);
			echo "newMediaDefaults['dimensionW'] = '".$newMediaDefaults['dimensionW']."';\n";
			$newMediaDefaults['dimensionH'] = strval($this->settings['videoDefaultPlayerSize_y']);
			echo "newMediaDefaults['dimensionH'] = '".$newMediaDefaults['dimensionH']."';\n";
			$newMediaDefaults['previewImage'] = podPress_url().'images/vpreview_center.png';
			echo "newMediaDefaults['previewImage'] = '".$newMediaDefaults['previewImage']."';\n";
			$newMediaDefaults['rss'] = 'false';
			echo "newMediaDefaults['rss'] = ".$newMediaDefaults['rss'].";\n";
			$newMediaDefaults['atom'] = 'true';
			echo "newMediaDefaults['atom'] = ".$newMediaDefaults['atom'].";\n";
			$newMediaDefaults['feedonly'] = 'true';
			echo "newMediaDefaults['feedonly'] = ".$newMediaDefaults['feedonly'].";\n";
			$newMediaDefaults['disablePlayer'] = 'false';
			echo "newMediaDefaults['disablePlayer'] = ".$newMediaDefaults['disablePlayer'].";\n";
			$newMediaDefaults['disablePreview'] = 'false';
			echo "newMediaDefaults['disablePreview'] = ".$newMediaDefaults['disablePreview'].";\n";
			$newMediaDefaults['content_level'] = 'free';
			echo "newMediaDefaults['content_level'] = '".$newMediaDefaults['content_level']."';\n";
			$newMediaDefaults['showme'] = 'false';
			echo "newMediaDefaults['showme'] = ".$newMediaDefaults['showme'].";\n";
			
			if ( FALSE !== empty($post->podPressMedia) ) {
				$num = 0;
			} else {
				$num = count($post->podPressMedia);
				$num = intval($num);
				if ($num > $this->settings['maxMediaFiles'] ) {
					$num = $this->settings['maxMediaFiles'];
				}
			}
			while ($num < $this->settings['maxMediaFiles']) {
				$post->podPressMedia[$num] = $newMediaDefaults;
				$num++;
			}

			$num = 0;
			while ($num < $this->settings['maxMediaFiles']) {
				if(!isset($post->podPressMedia[$num]['showme'])) {
					$post->podPressMedia[$num]['showme'] = 'true';
				}
				if($post->podPressMedia[$num]['showme'] == 'false') {
					$num++;
					continue;
				}

				if($this->settings['enablePodangoIntegration']) {
					if($podangoMediaFiles[$post->podPressPostSpecific['PodangoMediaFileID']]['Filename'] == basename($post->podPressMedia[$num]['URI'])) {
						$post->podPressMedia[$num]['URI'] = 'Podango:'.$podangoMediaFiles[$post->podPressPostSpecific['PodangoMediaFileID']]['Podcast'].':'.$podangoMediaFiles[$post->podPressPostSpecific['PodangoMediaFileID']]['ID'].':'.$podangoMediaFiles[$post->podPressPostSpecific['PodangoMediaFileID']]['EpisodeID'].':'.$podangoMediaFiles[$post->podPressPostSpecific['PodangoMediaFileID']]['Filename'];
					}
				}

				if($post->podPressMedia[$num]['rss'] == 'on') {
					$post->podPressMedia[$num]['rss'] = 'true';
				} else {
					$post->podPressMedia[$num]['rss'] = 'false';
				}
				if($post->podPressMedia[$num]['atom'] == 'on') {
					$post->podPressMedia[$num]['atom'] = 'true';
				} else {
					$post->podPressMedia[$num]['atom'] = 'false';
				}
				if($post->podPressMedia[$num]['feedonly'] == 'on') {
					$post->podPressMedia[$num]['feedonly'] = 'true';
				} else {
					$post->podPressMedia[$num]['feedonly'] = 'false';
				}
				if(!isset($post->podPressMedia[$num]['disablePlayer']) || $post->podPressMedia[$num]['disablePlayer'] == false || $post->podPressMedia[$num]['disablePlayer'] == 'false') {
					$post->podPressMedia[$num]['disablePlayer'] = 'false';
				} else {
					$post->podPressMedia[$num]['disablePlayer'] = 'true';
				}

				if(!isset($post->podPressMedia[$num]['disablePreview']) || $post->podPressMedia[$num]['disablePreview'] == false || $post->podPressMedia[$num]['disablePreview'] == 'false') {
					$post->podPressMedia[$num]['disablePreview'] = 'false';
				} else {
					$post->podPressMedia[$num]['disablePreview'] = 'true';
				}

				if($post->podPressMedia[$num]['premium_only'] == 'on' || $post->podPressMedia[$num]['premium_only'] == true) {
					$post->podPressMedia[$num]['content_level'] = 'premium_content';
				}
				if(!isset($post->podPressMedia[$num]['content_level'])) {
					$post->podPressMedia[$num]['content_level'] = 'free';
				}
				echo "\n";
				echo "podPressAddMediaFile(".$post->podPressMedia[$num]['showme'].", '".js_escape($post->podPressMedia[$num]['URI'])."', '".js_escape($post->podPressMedia[$num]['URI_torrent'])."', '".js_escape($post->podPressMedia[$num]['title'])."', '".$post->podPressMedia[$num]['type']."', '".$post->podPressMedia[$num]['size']."', '".$post->podPressMedia[$num]['duration']."', '".$post->podPressMedia[$num]['dimensionW']."', '".$post->podPressMedia[$num]['dimensionH']."', '".$post->podPressMedia[$num]['previewImage']."', ".$post->podPressMedia[$num]['rss'].", ".$post->podPressMedia[$num]['atom'].", ".$post->podPressMedia[$num]['feedonly'].", ".$post->podPressMedia[$num]['disablePlayer'].", '".$post->podPressMedia[$num]['content_level']."');\n";
				$num++;
			}
			echo '</script>'."\n";
			
			echo '<input type="hidden" id="podPressMedia_defaultpreviewImage" value="'.$plugins_url.'/images/vpreview_center.png" />'."\n";
			if ( defined('NONCE_KEY') AND is_string(constant('NONCE_KEY')) AND '' != trim(constant('NONCE_KEY')) ) {
				echo '<input type="hidden" id="podPress_AJAX_sec" value="' . wp_create_nonce(NONCE_KEY) . '" />'."\n";
			} else {
				echo '<input type="hidden" id="podPress_AJAX_sec" value="' . wp_create_nonce('Af|F07*wC7g-+OX$;|Z5;R@Pi]ZgoU|Zex8=`?mO-Mdvu+WC6l=6<O^2d~+~U3MM') . '" />'."\n";
			}
			
			echo '<p style="padding-bottom:1em;">'.sprintf(__('To control player location in your post, you may put %1$s where you want it to appear. You can choose the default postion on the general settings page of podPress.', 'podpress'), $this->podcasttag).' '.__('File Uploading support is not part of podPress.', 'podpress').'</p>'."\n";
			echo '<h4>'.__('Podcasting Files:', 'podpress').'</h4>'."\n";
		
			$num = 0;
			while ($num < $this->settings['maxMediaFiles']) {
				if(!isset($post->podPressMedia[$num])) {
					$num++;
					continue;
				}
				$thisMedia = $post->podPressMedia[$num];
			
				if ( $thisMedia['showme'] == 'true' ) {
					$display_text = 'block';
				} else {
					$display_text = 'none';
				}
				
				echo '     			<div id="podPressMediaFileContainer_'.$num.'" class="wrap" style="visibility: visible; display: '.$display_text.';">'."\n";
				echo '				<table class="podpress_editor_table">'."\n";
				echo '					<tr>'."\n";
				echo '						<th id="podpress_media_file_header"><em>'.__('Media File:', 'podpress').'</em></th>';
				echo '						<td id="podpress_media_file_buttons">';
				echo '						<input type="button" value="'.__('Move Up', 'podpress').'" onclick="podPressMoveFile('.$num.', \'up\'); podPressDisplayMediaFiles();"/>'."\n";
				echo '						<input type="button" value="'.__('Move Down', 'podpress').'" onclick="podPressMoveFile('.$num.', \'down\'); podPressDisplayMediaFiles();"/>'."\n";
				echo '						<input type="button" name="podPressAddAnother" value="'.__('Remove File', 'podpress').'" onclick="podPressRemoveFile('.$num.'); podPressDisplayMediaFiles();"/>';
				echo '						</td>';
				echo '					</tr>'."\n";
				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_URI">'.__('Location', 'podpress').'</label>: '."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				if(!empty($files) || $this->settings['enablePodangoIntegration']) {
					$fileOptionList = '';
					echo '						<select name="podPressMedia['.$num.'][URI]" id="podPressMedia_'.$num.'_URI" width="35" onchange="javascript: if(this.value==\'!\') { podPress_customSelectVal(this, \'Specifiy URL.\'); } podPressMediaFiles['.$num.'][\'URI\'] = this.value; podPressDetectType('.$num.');">'."\n";
					echo '							<option value="!">'.__('Specify URL ...', 'podpress').'</option>'."\n";
					$fileSelected = false;
					
					if($this->settings['enablePodangoIntegration']) {
						$podangoOptGroup = '';
						$podangoFirstOptGroup = true;
						foreach($podangoMediaFiles as $podangoMediaFile) {
							if(!empty($podangoMediaFile['EpisodeID']) && $podangoMediaFile['EpisodeID'] != $post->podPressPostSpecific['PodangoEpisodeID'] && $post->post_title != $podangoMediaFile['EpisodeTitle']) {
								continue;
							}
							if($podangoOptGroup != $podangoMediaFile['Podcast']) {
								$podangoOptGroup = $podangoMediaFile['Podcast'];
								if(!$podangoFirstOptGroup) {
									echo "							</optgroup>\n";
								}
								$x = $this->podangoapi->GetPodcast($podangoMediaFile['Podcast'], true);
								echo '							<optgroup name="PodangoOptGroup'.$podangoMediaFile['Podcast'].'" label="Podango Podcast: '.$x['Title'].'">'."\n";
								unset($x);
							}
							$key = 'Podango:'.$podangoMediaFile['Podcast'].':'.$podangoMediaFile['ID'].':'.$podangoMediaFile['EpisodeID'].':'.$podangoMediaFile['Filename'];
							if($key == $thisMedia['URI']) {
								$xSelected = ' selected="selected"';
								$fileSelected = true;
							} else {
								$xSelected = '';
							}
							echo '								<option value="'.$key.'"'.$xSelected.'>'.$podangoMediaFile['Filename'].'</option>'."\n";
						}
						echo "							</optgroup>\n";
						echo '							<optgroup name="LocallyHosted" label="Locally Hosted">'."\n";

					}
					
					foreach ($files as $key=>$val) {
						if(is_numeric($key)) {
							$key = $val;
						}
						if($key == $thisMedia['URI']) {
							$xSelected = ' selected="selected"';
							$fileSelected = true;
						} else {
							$xSelected = '';
						}
						if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) { // html_entity_decode can not handle the charset UTF-8 in most of the PHP 4 version
							$fileOptionList .= '							<option value="'.attribute_escape(htmlentities($key)).'"'.$xSelected.'>'.htmlentities(podPress_stringLimiter($val, 100, true)).'</option>'."\n";
						} else {
							if ( FALSE === stristr(PHP_OS, 'WIN') ) { 
								$fileOptionList .= '							<option value="'.attribute_escape(htmlentities($key, ENT_COMPAT, $blog_charset)).'"'.$xSelected.'>'.htmlentities(podPress_stringLimiter($val, 100, true), ENT_COMPAT, $blog_charset).'</option>'."\n";
							} else {
								$fileOptionList .= '							<option value="'.attribute_escape(htmlentities($key)).'"'.$xSelected.'>'.htmlentities(podPress_stringLimiter($val, 100, true)).'</option>'."\n";
							}
						}
					}
					if ( !$fileSelected ) {
						if ( TRUE == version_compare(PHP_VERSION, '5.0', '<') ) { // html_entity_decode can not handle the charset UTF-8 in most of the PHP 4 version
							echo '							<option value="'.attribute_escape(htmlentities($thisMedia['URI'])).'" selected="selected">'.htmlentities(podPress_stringLimiter($thisMedia['URI'], 100, true)).'</option>'."\n";
						} else {
							if ( FALSE === stristr(PHP_OS, 'WIN') ) { 
								echo '							<option value="'.attribute_escape(htmlentities($thisMedia['URI']), ENT_COMPAT, $blog_charset).'" selected="selected">'.htmlentities(podPress_stringLimiter($thisMedia['URI'], 100, true), ENT_COMPAT, $blog_charset).'</option>'."\n";
							} else {
								echo '							<option value="'.attribute_escape($thisMedia['URI']).'" selected="selected">'.htmlentities(podPress_stringLimiter($thisMedia['URI'], 100, true), ENT_COMPAT, $blog_charset).'</option>'."\n";
							}
						}
					}
					echo $fileOptionList;
					unset($fileOptionList);
					if ( $this->settings['enablePodangoIntegration'] ) {
						echo "							</optgroup>\n";
					}
					echo '						</select>'."\n";
					echo '							<input type="hidden" id="podPressMedia_'.$num.'_cleanURI" value="no" />'."\n";
				} else {
					echo '							<input type="text" id="podPressMedia_'.$num.'_URI" name="podPressMedia['.$num.'][URI]" class="podpress_wide_text_field" size="40" value="'.attribute_escape($thisMedia['URI']).'" onchange="javascript: podPressMediaFiles['.$num.'][\'URI\'] = this.value; podPressDetectType('.$num.'); podPressCheckForNotSafeFilenameChr( '.$num.', this.value );" />'."\n";
					echo '							<span id="podPressMedia_'.$num.'_URI_chrWarning" class="podpress_notice podPressMedia_URI_chrWarning">'.sprintf(__('<strong>Notice:</strong> It is not recommended to use other than these characters: %1$s or whitespaces in file and folder names.', 'podpress'), 'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789 .:/_-\ ').'</span>'."\n";
					echo '							<input type="hidden" id="podPressMedia_'.$num.'_cleanURI" value="yes" />'."\n";
				}
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				if($this->settings['enableTorrentCasting']) {
					echo '					<tr>'."\n";
					echo '						<th>'."\n";
					echo '							<label for="podPressMedia_'.$num.'_URItorrent">'.__('.torrent Location', 'podpress').'</label>: '."\n";
					echo '						</th>'."\n";
					echo '						<td>'."\n";
					echo '							<input type="text" id="podPressMedia_'.$num.'_URItorrent" name="podPressMedia['.$num.'][URI_torrent]" size="40" value="'.attribute_escape($thisMedia['URI_torrent']).'" onchange="javascript: podPressMediaFiles['.$num.'][\'URI_torrent\'] = this.value;" />'."\n";
					echo '						</td>'."\n";
					echo '					</tr>'."\n";
				}
				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_title">'.__('Title', 'podpress').'</label>: '."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="text" id="podPressMedia_'.$num.'_title" name="podPressMedia['.$num.'][title]" class="podpress_wide_text_field" size="40" value="'.attribute_escape($thisMedia['title']).'" onchange="javascript: podPressMediaFiles['.$num.'][\'title\'] = this.value;" />'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_type">'.__('Type', 'podpress').'</label>: '."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="podPressMedia_'.$num.'_type" name="podPressMedia['.$num.'][type]" onchange="javascript: podPressMediaFiles['.$num.'][\'type\'] = this.value; podPressAdjustMediaFieldsBasedOnType('.$num.');" >'."\n"; podPress_mediaOptions();	echo '</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="podPressMediaSizeWrapper_'.$num.'" >'."\n";
				echo '						<th>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_size">'.__('File Size', 'podpress').'</label>: '."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="text" id="podPressMedia_'.$num.'_size" name="podPressMedia['.$num.'][size]" size="10" value="'.$thisMedia['size'].'" onchange="javascript: podPressMediaFiles['.$num.'][\'size\'] = this.value;"/> '.__('[in byte]', 'podpress').' <img src="'.$plugins_url.'/images/ajax-loader.gif" id="podPressMedia_'.$num.'_size_loadimg" class="podpress_ajax_loader_img" /><input type="button" id="podPressMedia_'.$num.'_size_detectbutton" value="'.__('Auto Detect', 'podpress').'" onclick="podPress_getfileinfo(\'size\', '.$num.');" />'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="podPressMediaDurationWrapper_'.$num.'" style="display: none;">'."\n";
				echo '						<th>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_duration">'.__('Duration', 'podpress').'</label>: '."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="text" id="podPressMedia_'.$num.'_duration" name="podPressMedia['.$num.'][duration]" size="10" value="'.$thisMedia['duration'].'" onchange="javascript: podPressMediaFiles['.$num.'][\'duration\'] = this.value;"/> <span class="podpress_abbr" title="'.__('hours:minutes:seconds - for example: 2:45:10 or 34:01 or 1:36 or 120:47 or 0:10', 'podpress').'">'.__('[hh:mm:ss]', 'podpress').'</span> <img src="'.$plugins_url.'/images/ajax-loader.gif" id="podPressMedia_'.$num.'_duration_loadimg" class="podpress_ajax_loader_img" /><input type="button" id="podPressMedia_'.$num.'_duration_detectbutton" value="'.__('Auto Detect', 'podpress').'" onclick="podPress_getfileinfo(\'duration\', '.$num.');" /> ('.sprintf(__('This may take some time for remote files. %1$s', 'podpress'), '<span class="podpress_abbr" title="'.__('If the file is not on the same server as your blog then podPress will attempt to download the file in order to get this information with the help of getID3() which is only able to retrieve ID3 tags from local files. podPress removes the temporary download file at the end of this process. It is likely that this feature works only for relative small files because the download is probably limited by execution time and memory limits on the server of your blog.', 'podpress').'">'.__('Because ...', 'podpress').'</span>').')'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				if(empty($thisMedia['previewImage'])) {
					$thisMedia['previewImage'] = podPress_url().'images/vpreview_center.png';
				}
				echo '					<tr id="podPressMediaPreviewImageWrapper_'.$num.'" style="display: none;">'."\n";
				echo '						<th>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_previewImage">'.__('Preview Image URL', 'podpress').'</label>: '."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="text" id="podPressMedia_'.$num.'_previewImage" name="podPressMedia['.$num.'][previewImage]" class="podpress_wide_text_field" size="40" value="'.attribute_escape($thisMedia['previewImage']).'" onchange="javascript: podPressPreviewImageOnChange('.$num.', this.value);" />'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="podPressMediaPreviewImageDisplayWrapper_'.$num.'" style="display: none;">'."\n";
				echo '						<th>'."\n";
				echo '							'.__('Preview Image', 'podpress').': '."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<div id="podPressPlayerSpace_'.$num.'"></div>'."\n";
				echo '<script type="text/javascript"><!--'."\n";
				//echo "	document.getElementById('podPressPlayerSpace_".$num."').innerHTML = podPressGenerateVideoPreview (".$num.", '', , , document.getElementById('podPressMedia_".$num."_previewImage').value, true);\n";
				echo "	document.getElementById('podPressPlayerSpace_".$num."').innerHTML = podPressGenerateVideoPreview (".$num.", '', ".$thisMedia['dimensionW'].", ".$thisMedia['dimensionH'].", document.getElementById('podPressMedia_".$num."_previewImage').value, true);\n";
				//echo "	document.getElementById('podPressPlayerSpace_".$num."').innerHTML = podPressGenerateVideoPreview (".$num.", '', ".intval($thisMedia['dimensionW']).", ".intval($thisMedia['dimensionH']).", document.getElementById('podPressMedia_".$num."_previewImage').value, true);\n";
				echo "--></script>\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr id="podPressMediaDimensionWrapper_'.$num.'">'."\n";
				echo '						<th>'."\n";
				echo '							'.__('Dimensions', 'podpress').' (WxH): '."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="text" id="podPressMedia_'.$num.'_dimensionW" name="podPressMedia['.$num.'][dimensionW]" size="5" value="'.$thisMedia['dimensionW'].'" onchange="javascript: podPressMediaFiles['.$num.'][\'dimensionW\'] = this.value; podPressUpdateDimensionList(\''.$num.'\');" />x<input type="text" id="podPressMedia_'.$num.'_dimensionH" name="podPressMedia['.$num.'][dimensionH]" size="5" value="'.$thisMedia['dimensionH'].'" onchange="javascript: podPressMediaFiles['.$num.'][\'dimensionH\'] = this.value; podPressUpdateDimensionList(\''.$num.'\')" /> '."\n";
				echo '							 '.__('or', 'podpress').' ';
				echo '							<select id="podPressMedia_'.$num.'_dimensionList" onchange="javascript: podPressUpdateDimensions(\''.$num.'\', this.value);">'."\n"; podPress_videoDimensionOptions($thisMedia['dimensionW'].':'.$thisMedia['dimensionH']);	echo '</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";

				if($post->post_status == 'static') {
					echo '					<tr style="display: none;">'."\n";
				} else {
					echo '					<tr>'."\n";
				}
				echo '						<th>'."\n";
				echo '							'.__('Included in', 'podpress').': '."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							&nbsp;<label for="podPressMedia_'.$num.'_rss">'.__('RSS2', 'podpress').'</label> <input type="checkbox" id="podPressMedia_'.$num.'_rss" name="podPressMedia['.$num.'][rss]" onchange="javascript: podPressMediaFiles['.$num.'][\'rss\'] = this.checked; podPressSetSingleRSS('.$num.');" />'."\n";
				echo '							&nbsp;<label for="podPressMedia_'.$num.'_atom">'.__('ATOM', 'podpress').'</label> <input type="checkbox" id="podPressMedia_'.$num.'_atom" name="podPressMedia['.$num.'][atom]" onchange="javascript: podPressMediaFiles['.$num.'][\'atom\'] = this.checked;" />'."\n";
				echo '							&nbsp;<label for="podPressMedia_'.$num.'_feedonly">'.__('Feed Only', 'podpress').'</label> <input type="checkbox" id="podPressMedia_'.$num.'_feedonly" name="podPressMedia['.$num.'][feedonly]" onchange="javascript: podPressMediaFiles['.$num.'][\'feedonly\'] = this.checked;" />'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				if($this->settings['enablePremiumContent']) {
					echo '					<tr>'."\n";
					echo '						<th>'."\n";
					echo '							<label for="podPressMedia_'.$num.'_content_level">'.__('Subscription', 'podpress').'</label>:';
					echo '						</th>'."\n";
					echo '						<td>'."\n";
					echo '							<select id="podPressMedia_'.$num.'_content_level" name="podPressMedia['.$num.'][content_level]" onchange="javascript: podPressMediaFiles['.$num.'][\'content_level\'] = this.value;">'."\n";
					echo '								<option value="free" '; if(empty($thisMedia['content_level']) || $thisMedia['content_level'] == 'free') { echo 'selected="selected"';	}	echo '>'.__('Free', 'podpress').'</option>'."\n";
					foreach (podPress_getCapList(true) as $cap) {
						if(substr($cap, -8, 8) == '_content') {
							echo '								<option value="'.$cap.'" '; if($thisMedia['content_level'] == $cap) { echo 'selected="selected"';	}	echo '>'.__(podPress_getCapName($cap), 'podpress').'</option>'."\n";
						}
					}
					echo '							</select>'."\n";
					echo '						</td>'."\n";
					echo '					</tr>'."\n";
				}
				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_disablePlayer">'.__('Disable Player', 'podpress').'</label>:'."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="checkbox" id="podPressMedia_'.$num.'_disablePlayer" name="podPressMedia['.$num.'][disablePlayer]"'; if($thisMedia['disablePlayer'] != 'false') { echo 'checked="checked" '; } echo ' onchange="javascript: podPressMediaFiles['.$num.'][\'disablePlayer\'] = this.checked;" />'."\n";
				echo '							&nbsp;&nbsp; '.__('(Use if this media file is not compatible with one of the included players.)', 'podpress')."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="podPressMedia_'.$num.'_disablePreview">'.__('Disable Preview Player', 'podpress').'</label>:'."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<input type="checkbox" id="podPressMedia_'.$num.'_disablePreview" name="podPressMedia['.$num.'][disablePreview]"'; if($thisMedia['disablePreview'] != 'false') { echo 'checked="checked" '; } echo ' onchange="javascript: podPressMediaFiles['.$num.'][\'disablePreview\'] = this.checked;" />'."\n";
				echo '							&nbsp;&nbsp; '.__('(Use this to disable the "Click to Play" preview player.)', 'podpress')."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				$actionMedia = $thisMedia;
				$actionMedia['num'] = $num;
				do_action('podPress_customMediaData', array($actionMedia));
				echo '					<tr id="podPressMedia_'.$num.'_id3tags_details_row">'."\n";
				echo '						<th>'."\n";
				echo '							'.__('ID3 Tag Info', 'podpress').":\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<img src="'.$plugins_url.'/images/ajax-loader.gif" id="podPressMedia_'.$num.'_id3tags_loadimg" class="podpress_ajax_loader_img" /><input type="button" id="podPressMedia_'.$num.'_id3tags_detectbutton" value="'.__('Show','podpress').'" onclick="javascript: podPressShowHideID3Tags('.$num.');" /> ('.sprintf(__('This may take some time for remote files. %1$s', 'podpress'), '<span class="podpress_abbr" title="'.__('If the file is not on the same server as your blog then podPress will attempt to download the file in order to get this information with the help of getID3() which is only able to retrieve ID3 tags from local files. podPress removes the temporary download file at the end of this process. It is likely that this feature works only for relative small files because the download is probably limited by execution time and memory limits on the server of your blog.', 'podpress').'">'.__('Because ...', 'podpress').'</span>').')'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr>'."\n";
				echo '						<td colspan="2">'."\n";
				echo '							<div id="podPressMedia_'.$num.'_id3tags" style="display: none; vertical-align: top;">'."\n";
				echo '							</div>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '				</table>'."\n";
				echo '			</div> <!-- end: podPressMediaFileContainer_'.$num.' -->'."\n";
				$num++;
			}
			
			echo '			<input type="button" name="podPressAddAnother" value="'.__('Add Media File', 'podpress').'" onclick="javascript: podPressAddMediaFile(true, \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'new\', true, false, false, \'free\'); podPressDisplayMediaFiles();"/>'."\n";
			
			if($entryType != 'page') {
				echo '			<br/>'."\n";
				echo '			<h4>'.__('Post specific settings for iTunes:', 'podpress').'</h4>'."\n";
				//echo '			<input type="button" name="iTunesSpecificSettings_button" id="iTunesSpecificSettings_button" value="Show" onclick="javascript: podPressShowHideDiv(\'iTunesSpecificSettings\');"/>'."\n";

				echo '     			<div class="wrap" id="iTunesSpecificSettings">'."\n";
				echo '				<table class="podpress_editor_table">'."\n";
				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="iTunesSubtitleChoice">'.__('iTunes:Subtitle', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'iTunesSubtitleHelp\');">(?)</a>:'."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesSubtitleChoice" name="iTunesSubtitleChoice" onchange="javascript: if(this.value == \'Custom\') { document.getElementById(\'iTunesSubtitleWrapper\').style.display=\'\'; } else { document.getElementById(\'iTunesSubtitleWrapper\').style.display=\'none\'; }">'."\n";
				echo '								<option value="PostExcerpt" '; if($post->podPressPostSpecific['itunes:subtitle'] == '##PostExcerpt##') { echo 'selected="selected"';	}	echo '>'.__('Use the excerpt', 'podpress').'</option>'."\n";
				echo '								<option value="Custom" '; if($post->podPressPostSpecific['itunes:subtitle'] != '##PostExcerpt##') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="iTunesSubtitleHelp" style="display: none;">'."\n";
				echo '						<th>&nbsp;</th>'."\n";
				echo '						<td>'.sprintf(__('"%1$s" (default) podPress takes the first 255 characters from the excerpt of the excerpt and if there is none from the blog Post text.', 'podpress'),__('Use the excerpt', 'podpress')).'</td>'."\n";
				echo '					</tr>'."\n";
				if($post->podPressPostSpecific['itunes:subtitle'] == '##PostExcerpt##') { $tempShowMe = 'style="display: none;"';$post->podPressPostSpecific['itunes:subtitle'] = ''; } else { $tempShowMe = ''; }
				echo '					<tr id="iTunesSubtitleWrapper" '.$tempShowMe.'>'."\n";
				echo '						<th>&nbsp;</th>'."\n";
				echo '						<td>'."\n";
				echo '							<textarea name="iTunesSubtitle" class="podpress_wide_text_field" rows="4" cols="40">'.stripslashes($post->podPressPostSpecific['itunes:subtitle']).'</textarea>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				
				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="iTunesSummaryChoice">'.__('iTunes:Summary', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'iTunesSummaryHelp\');">(?)</a>:'."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesSummaryChoice" name="iTunesSummaryChoice" onchange="javascript: if(this.value == \'Custom\') { document.getElementById(\'iTunesSummaryWrapper\').style.display=\'\'; } else { document.getElementById(\'iTunesSummaryWrapper\').style.display=\'none\'; }">'."\n";
				echo '								<option value="PostExcerpt" '; if($post->podPressPostSpecific['itunes:summary'] == '##PostExcerpt##') { echo 'selected="selected"';	}	echo '>'.__('Use the excerpt', 'podpress').'</option>'."\n";
				echo '								<option value="PostContentShortened" '; if($post->podPressPostSpecific['itunes:summary'] == '##PostContentShortened##') { echo 'selected="selected"';	}	echo '>'.__('autom. excerpt of the post content', 'podpress').'</option>'."\n";
				echo '								<option value="Global" '; if($post->podPressPostSpecific['itunes:summary'] == '##Global##') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').'</option>'."\n";
				echo '								<option value="Custom" '; if($post->podPressPostSpecific['itunes:summary'] != '##Global##' && $post->podPressPostSpecific['itunes:summary'] != '##PostExcerpt##' && $post->podPressPostSpecific['itunes:summary'] != '##PostContentShortened##') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="iTunesSummaryHelp" style="display: none;">'."\n";
				echo '						<th>&nbsp;</th>'."\n";
				echo '						<td>'.sprintf(__('"%1$s" (default) podPress takes the excerpt. If you have not written an excerpt then it takes a part from the blog Post text.', 'podpress'),__('Use the excerpt', 'podpress')).'</td>'."\n";
				echo '					</tr>'."\n";
				
				if($post->podPressPostSpecific['itunes:summary'] == '##Global##' || $post->podPressPostSpecific['itunes:summary'] == '##PostExcerpt##' || $post->podPressPostSpecific['itunes:summary'] ==  '##PostContentShortened##') { $tempShowMe = 'style="display: none;"';	$post->podPressPostSpecific['itunes:summary'] = ''; } else { $tempShowMe = ''; }
				echo '					<tr id="iTunesSummaryWrapper" '.$tempShowMe.'>'."\n";
				echo '						<th>&nbsp;</th>'."\n";
				echo '						<td>'."\n";
				echo '							<textarea name="iTunesSummary" class="podpress_wide_text_field" rows="4" cols="40">'.stripslashes($post->podPressPostSpecific['itunes:summary']).'</textarea>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="iTunesKeywordsChoice">'.__('iTunes:Keywords', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'iTunesKeywordsHelp\');">(?)</a>:'."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesKeywordsChoice" name="iTunesKeywordsChoice" onchange="javascript: if(this.value == \'Custom\') { document.getElementById(\'iTunesKeywordsWrapper\').style.display=\'\'; } else { document.getElementById(\'iTunesKeywordsWrapper\').style.display=\'none\'; }">'."\n";
				echo '								<option value="WordPressCats" '; if($post->podPressPostSpecific['itunes:keywords'] == '##WordPressCats##') { echo 'selected="selected"';	}	echo '>'.__('Use WordPress Categories', 'podpress').'</option>'."\n";
				echo '								<option value="post_tags" '; if($post->podPressPostSpecific['itunes:keywords'] == '##post_tags##') { echo 'selected="selected"';	}	echo '>'.__('Use the tags of the post', 'podpress').'</option>'."\n";
				echo '								<option value="Global" '; if($post->podPressPostSpecific['itunes:keywords'] == '##Global##') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.podPress_stringLimiter(stripslashes($this->settings['iTunes']['keywords']), 40) .')</option>'."\n";
				echo '								<option value="Custom" '; if($post->podPressPostSpecific['itunes:keywords'] != '##Global##' AND $post->podPressPostSpecific['itunes:keywords'] != '##WordPressCats##' AND $post->podPressPostSpecific['itunes:keywords'] != '##post_tags##') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				
				echo '					<tr id="iTunesKeywordsHelp" style="display: none;">'."\n";
				echo '						<th>&nbsp;</th>'."\n";
				echo '						<td>'.__('Not visible in iTunes, but used for searches.', 'podpress').'</td>'."\n";
				echo '					</tr>'."\n";
				
				if ( $post->podPressPostSpecific['itunes:keywords'] == '##Global##' OR $post->podPressPostSpecific['itunes:keywords'] == '##WordPressCats##' OR $post->podPressPostSpecific['itunes:keywords'] == '##post_tags##' ) { $tempShowMe = 'style="display: none;"';	$post->podPressPostSpecific['itunes:keywords'] = ''; } else { $tempShowMe = ''; }
				echo '					<tr id="iTunesKeywordsWrapper" '.$tempShowMe.'>'."\n";
				echo '						<th>&nbsp;</th>'."\n";
				echo '						<td>'."\n";
				echo '							'.__('a list of max. 12 comma separated words', 'podpress').'<br/><textarea name="iTunesKeywords" class="podpress_wide_text_field" rows="4" cols="40">'.stripslashes($post->podPressPostSpecific['itunes:keywords']).'</textarea>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="iTunesAuthorChoice">'.__('iTunes:Author', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'iTunesAuthorHelp\');">(?)</a>:'."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesAuthorChoice" name="iTunesAuthorChoice" onchange="javascript: if(this.value == \'Custom\') { document.getElementById(\'iTunesAuthorWrapper\').style.display=\'\'; } else { document.getElementById(\'iTunesAuthorWrapper\').style.display=\'none\'; }">'."\n";
				echo '								<option value="Global" '; if($post->podPressPostSpecific['itunes:author'] == '##Global##') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.podPress_stringLimiter(ucfirst(stripslashes($this->settings['iTunes']['author'])), 40).')</option>'."\n";
				echo '								<option value="Custom" '; if($post->podPressPostSpecific['itunes:author'] != '##Global##') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr id="iTunesAuthorHelp" style="display: none;">'."\n";
				echo '						<th>&nbsp;</th>'."\n";
				echo '						<td>'.__('Used if this Author is different than the feeds author.', 'podpress').'</td>'."\n";
				echo '					</tr>'."\n";
				if($post->podPressPostSpecific['itunes:author'] == '##Global##') { $tempShowMe = 'style="display: none;"';	$post->podPressPostSpecific['itunes:author'] = ''; } else { $tempShowMe = ''; }
				echo '					<tr id="iTunesAuthorWrapper" '.$tempShowMe.'>'."\n";
				echo '						<th>&nbsp;</th>'."\n";
				echo '						<td><input type="text" name="iTunesAuthor" class="podpress_wide_text_field" size="40" value="'.attribute_escape($post->podPressPostSpecific['itunes:author']).'" /></td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="iTunesExplicit">'.__('iTunes:Explicit', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'iTunesExplicitHelp\');">(?)</a>:'."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesExplicit" name="iTunesExplicit">'."\n";
				echo '								<option value="Default" '; if($post->podPressPostSpecific['itunes:explicit'] == 'Default') { echo 'selected="selected"';	}	echo '>'.__('Use Default', 'podpress').' ('.$this->settings['iTunes']['explicit'].')</option>'."\n";
				echo '								<option value="No" '; if($post->podPressPostSpecific['itunes:explicit'] == 'No') { echo 'selected="selected"';	}	echo '>'.__('No', 'podpress').'</option>'."\n";
				echo '								<option value="Yes" '; if($post->podPressPostSpecific['itunes:explicit'] == 'Yes') { echo 'selected="selected"';	}	echo '>'.__('Yes', 'podpress').'</option>'."\n";
				echo '								<option value="Clean" '; if($post->podPressPostSpecific['itunes:explicit'] == 'Clean') { echo 'selected="selected"';	}	echo '>'.__('Clean', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="iTunesExplicitHelp" style="display: none;">'."\n";
				echo '						<th>&nbsp;</th>'."\n";
				echo '						<td>'.__('Does your podcast contain explicit language or adult content? (If you choose "Yes" or "Clean" then a corresponding notice will show up in iTunes (Explicit resp. Clean). If you choose "No" then there will be special notice.)', 'podpress').'</td>'."\n";
				echo '					</tr>'."\n";

				echo '					<tr>'."\n";
				echo '						<th>'."\n";
				echo '							<label for="iTunesBlock">'.__('iTunes:Block', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'itunesBlockHelp\');">(?)</a>:'."\n";
				echo '						</th>'."\n";
				echo '						<td>'."\n";
				echo '							<select id="iTunesBlock" name="iTunesBlock">'."\n";
				echo '								<option value="Default" '; if($post->podPressPostSpecific['itunes:block'] == 'Default') { echo 'selected="selected"';	}	echo '>'.__('Use Default', 'podpress').' ('.$this->settings['iTunes']['block'].')</option>'."\n";
				echo '								<option value="No" '; if($post->podPressPostSpecific['itunes:block'] == 'No') { echo 'selected="selected"';	}	echo '>'.__('No', 'podpress').'</option>'."\n";
				echo '								<option value="Yes" '; if($post->podPressPostSpecific['itunes:block'] == 'Yes') { echo 'selected="selected"';	}	echo '>'.__('Yes', 'podpress').'</option>'."\n";
				echo '							</select>'."\n";
				echo '						</td>'."\n";
				echo '					</tr>'."\n";
				echo '					<tr id="itunesBlockHelp" style="display: none;">'."\n";
				echo '						<th>&nbsp;</th>'."\n";
				echo '						<td>'.__('Prevent this episode or podcast from appearing in iTunes.', 'podpress').'</td>'."\n";
				echo '					</tr>'."\n";
				echo '				</table>'."\n";
				echo '			</div> <!-- end: iTunesSpecificSettings -->'."\n";
			}
		
			if($this->settings['enablePodangoIntegration']) {
				echo "			<br/>\n";
				echo '			<strong>Podango File Uploader</strong>';
				if($this->settings['podangoDefaultPodcast'] == '##ALL##') {
					$podangoPodcastList = $this->podangoapi->GetPodcasts(true);
					echo ' <strong>for: </strong><select name="podPressPodangoPodcastID" id="podPressPodangoPodcastID" onChange="javascript: document.getElementById(\'podangoUploadFrame\').src=\''.$this->podangoapi->fileUploader.'?podcastId=\'+this.value">'."\n";
					foreach ($podangoPodcastList as $k=>$v) {
						if(!isset($podangoPodcastID)) {
							$podangoPodcastID = $k;
						}
						echo '						<option value="'.$k.'">'.$v['Title'].'</option>'."\n";
					}
					echo '					</select>'."\n";
				} else {
					$podangoPodcastID = $this->settings['podangoDefaultPodcast'];
				}
				echo '<br/>'."\n";
				echo '			<iframe src="'.$this->podangoapi->fileUploader.'?podcastId='.$podangoPodcastID.'" id="podangoUploadFrame" title="Podango Upload" border="0" width="560" height="110"> </iframe>'."\n";
			}
			
			echo '<script type="text/javascript">podPressDisplayMediaFiles(); </script>'."\n";
		}


		function post_edit($post_id) {
			GLOBAL $post, $pagenow;
			
			if ($this->justposted) {
				return;
			}
			
			$blog_charset = get_bloginfo('charset');
			
			// this condition make sure that the podpress settings of a post or page will be saved only via this admin pages and not "quick edit"
			if ( "post.php" === $pagenow OR "page.php" === $pagenow OR "post-new.php" === $pagenow OR "page-new.php" === $pagenow ) {
				// patch for compat with post revisions (WP 2.6+)
				if ( isset($_POST['post_ID']) && (int) $_POST['post_ID'] )
					$post_id = (int) $_POST['post_ID'];

				$this->justposted = true;
				if($this->checkWritableTempFileDir()) {
					if(!empty($this->tempfilesystempath)) {
						$dh  = opendir($this->tempfilesystempath);
						while (false !== ($filename = readdir($dh))) {
							if(substr($filename, 0, 10) == 'feedcache_' && is_file($this->tempfilesystempath.'/'.$filename)) {
								unlink($this->tempfilesystempath.'/'.$filename);
							}
						}
					}
				}
				if(isset($_POST['podPressMedia'])) {
					$i=0;
					foreach ($_POST['podPressMedia'] as $mediafiledata) {
						if ( !empty($mediafiledata['URI']) ) {
							foreach ($mediafiledata as $key => $value) {
								switch ($key) {
									case 'URI' :
										if ( isset($mediafiledata['podPressMedia_'.$i.'_cleanURI']) AND 'yes' === $mediafiledata['podPressMedia_'.$i.'_cleanURI']) {
											$verifiedMedia[$i][$key] = clean_url($value, array('http', 'https'), 'db');
										} else {
											$verifiedMedia[$i][$key] = trim($value);
										}
									break;
									case 'title' :
										$verifiedMedia[$i][$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, $blog_charset);
									break;
									case 'size' :
									case 'dimensionW' :
									case 'dimensionH' :
										$value = strip_tags(trim($value));
										$verifiedMedia[$i][$key] = intval(preg_replace('/[^0-9]/', '', $value));
									break;
									case 'duration':
										$value = strip_tags(trim($value));
										$result = preg_match('/(^([0-9]{1,3})(:([0-5][0-9])){0,1}(:([0-5][0-9])$))/', $value, $b);
										if (!empty($b) AND 0 < $result) {
											$verifiedMedia[$i][$key] = $value;
										} else {
											$verifiedMedia[$i][$key] = '';
										}
									break;
									default :
										$verifiedMedia[$i][$key] = $value;
									break;
								}
							}
						}
						$i++;
					}
					delete_post_meta($post_id, '_podPressMedia');
					if(!empty($verifiedMedia)) {
						if($this->settings['enablePodangoIntegration']) {
							foreach ($verifiedMedia as $key=>$val) {
								if(substr($val['URI'], 0, strlen('Podango:')) == 'Podango:') {
									$fileNameParts = explode(':', $val['URI']);
									$podPressPostSpecific['PodangoPodcastID'] = $fileNameParts[1];
									$podPressPostSpecific['PodangoMediaFileID'] = $fileNameParts[2];
									$podPressPostSpecific['PodangoEpisodeID'] = $fileNameParts[3];

									if(empty($podPressPostSpecific['PodangoMediaFileID'])) {
										// need to add the mediafileID lookup
									}

									if(empty($podPressPostSpecific['PodangoEpisodeID'])) {
										if($this->settings['podangoDefaultPodcast'] == '##ALL##') {
											$podangoPodcastID = (int)$_POST['podPressPodangoPodcastID'];
										} else {
											$podangoPodcastID = $this->settings['podangoDefaultPodcast'];
										}
										$podPressPostSpecific['PodangoEpisodeID'] = $this->podangoapi->CreateEpisode($podangoPodcastID, $_POST['post_title'], $_POST['content'], '', $podPressPostSpecific['PodangoMediaFileID']);
									} else {
										$this->podangoapi->UpdateEpisode($podPressPostSpecific['PodangoEpisodeID'], $_POST['post_title'], $_POST['content'], '', $podPressPostSpecific['PodangoMediaFileID']);
									}
									$val['URI'] = $this->podangoapi->mediaTrackerURL.'555/'.$podPressPostSpecific['PodangoEpisodeID'].'/'.$fileNameParts[4];
									$verifiedMedia[$key] = $val;
									unset($fileNameParts);
								}
							}
						}
						podPress_add_post_meta($post_id, '_podPressMedia', $verifiedMedia, true) ;
					}
				}
				if(isset($_POST['iTunesSubtitleChoice']) AND $_POST['iTunesSummaryChoice'] AND $_POST['iTunesKeywordsChoice'] AND $_POST['iTunesAuthorChoice'] AND $_POST['iTunesExplicit'] AND $_POST['iTunesBlock']) {
					if($_POST['iTunesSubtitleChoice'] == 'Custom' && !empty($_POST['iTunesSubtitle'])) {
						$podPressPostSpecific['itunes:subtitle'] = htmlspecialchars(strip_tags(trim($_POST['iTunesSubtitle'])), ENT_QUOTES, $blog_charset);
					} else {
						$podPressPostSpecific['itunes:subtitle'] = '##PostExcerpt##';
					}

					if ($_POST['iTunesSummaryChoice'] == 'Custom' && !empty($_POST['iTunesSummary'])) {
						$podPressPostSpecific['itunes:summary'] = htmlspecialchars(strip_tags(trim($_POST['iTunesSummary'])), ENT_QUOTES, $blog_charset);
					} elseif ($_POST['iTunesSummaryChoice'] == 'Global') {
						$podPressPostSpecific['itunes:summary'] = '##Global##';
					} elseif ($_POST['iTunesSummaryChoice'] == 'PostContentShortened') {
						$podPressPostSpecific['itunes:summary'] = '##PostContentShortened##';
					} else {
						$podPressPostSpecific['itunes:summary'] = '##PostExcerpt##';
					}

					if($_POST['iTunesKeywordsChoice'] == 'Custom' && !empty($_POST['iTunesKeywords'])) {
						$podPressPostSpecific['itunes:keywords'] = $this->cleanup_itunes_keywords($_POST['iTunesKeywords']);
					} elseif ($_POST['iTunesKeywordsChoice'] == 'Global') {
						$podPressPostSpecific['itunes:keywords'] = '##Global##';
					} elseif ($_POST['iTunesKeywordsChoice'] == 'post_tags') {
						$podPressPostSpecific['itunes:keywords'] = '##post_tags##';
					} else {
						$podPressPostSpecific['itunes:keywords'] = '##WordPressCats##';
					}

					if($_POST['iTunesAuthorChoice'] == 'Custom' && !empty($_POST['iTunesAuthor'])) {
						$podPressPostSpecific['itunes:author'] = htmlspecialchars(strip_tags(trim($_POST['iTunesAuthor'])), ENT_QUOTES, $blog_charset);
					} else {
						$podPressPostSpecific['itunes:author'] = '##Global##';
					}

					if($_POST['iTunesExplicit']) {
						$podPressPostSpecific['itunes:explicit'] = $_POST['iTunesExplicit'];
					} else {
						$podPressPostSpecific['itunes:explicit'] = 'No';
					}

					if($_POST['iTunesBlock']) {
						$podPressPostSpecific['itunes:block'] = $_POST['iTunesBlock'];
					} else {
						$podPressPostSpecific['itunes:block'] = 'No';
					}
					
					delete_post_meta($post_id, '_podPressPostSpecific');
					podPress_add_post_meta($post_id, '_podPressPostSpecific', $podPressPostSpecific, true);
				}
				if (FALSE === is_object($post) OR TRUE === empty($post)) {
					$post = new stdClass;
				}
				$post->podPressPostSpecific = $podPressPostSpecific;
				
				
				/*
				if(class_exists(snoopy)) {
					if(!empty($this->settings['iTunes']['FeedID'])) {
						$client = new Snoopy();
						$client->_fp_timeout = 10;
						$x = $client->fetch('https://phobos.apple.com/WebObjects/MZFinance.woa/wa/pingPodcast?id='.$this->settings['iTunes']['FeedID']);
					} elseif(!empty($this->settings['iTunes']['feedURL'])) {
						$client = new Snoopy();
						$client->_fp_timeout = 10;
						$x = $client->fetch('https://phobos.apple.com/WebObjects/MZFinance.woa/wa/pingPodcast?feedURL='.$this->settings['iTunes']['feedURL']);
					}
				}
				 */
			} else {
				return;
			}
		}

		function edit_category_form($input) {
			global $wp_version, $action;
			//~ echo "\n<pre>\n";
			//~ var_dump('in function edit_category_form');
			//~ var_dump($input);
			//~ var_dump($input->taxonomy);
			//~ var_dump($action);
			//~ var_dump($_GET['action']);
			//~ var_dump($wp_version);
			//~ echo "\n</pre>\n";
		
			if ( ('edit' == $_GET['action'] AND TRUE == version_compare($wp_version, '2.7', '>=')) OR 'editedcat' == $action ) { // show the following form only when an existing category is going to be edited. 			
				if ( FALSE === isset($input->taxonomy) OR TRUE === empty($input->taxonomy) OR ('post_tag' !== $input->taxonomy AND 'category' !== $input->taxonomy) ) {
					$taxonomy = 'misc';
					$taxonomy_str = __('Taxonomy', 'podpress');
				} else {
					$taxonomy = $input->taxonomy;
					Switch ($taxonomy) {
						Case 'post_tag' :
							$taxonomy_str = __('Tag', 'podpress');
						break;
						Case 'category' :
							$taxonomy_str = __('Category', 'podpress');
						break;
					}
				}
				
			//~ printphpnotices_var_dump('print edit_'.$taxonomy.'_form');
			//~ printphpnotices_var_dump($input);
			
				$data = podPress_get_option('podPress_'.$taxonomy.'_'.$input->term_id);
				
				$blog_charset = get_bloginfo('charset');

				if ( empty($data['podcastFeedURL']) ) {
					if (TRUE == version_compare($wp_version, '2.9.3','>') ) { // since WP 3.0 the cat_ID isreplaced by tag_ID
						$data['podcastFeedURL'] = get_term_feed_link($input->term_id, $taxonomy);
					} elseif ( TRUE == version_compare($wp_version, '2.9.3','<=') AND TRUE == version_compare($wp_version, '2.4','>') ) {
						Switch ($taxonomy) {
							default:
							case 'post_tag' :
								$data['podcastFeedURL'] = get_tag_feed_link($input->term_id);
							break;
							case 'category' :
								$data['podcastFeedURL'] = get_category_feed_link($input->term_id);
							break;
						}
					} else {
						$data['podcastFeedURL'] = site_url().'/?feed=rss2&cat='.$input->term_id;
					}
				} else {
					$url_parts = parse_url($data['podcastFeedURL']);
					if (isset($url_parts['query'])) {
						$output='';
						parse_str($url_parts['query'], $output);
						if ( TRUE === isset($output['cat']) AND FALSE !== empty($output['cat']) ) {
							if (TRUE == version_compare($wp_version, '2.9.3','>') ) { // since WP 3.0 the cat_ID isreplaced by tag_ID
								$data['podcastFeedURL'] = get_term_feed_link($input->term_id, $taxonomy);
							} elseif ( TRUE == version_compare($wp_version, '2.9.3','<=') AND TRUE == version_compare($wp_version, '2.4','>') ) {
								Switch ($taxonomy) {
									default:
									case 'post_tag' :
										$data['podcastFeedURL'] = get_tag_feed_link($input->term_id);
									break;
									case 'category' :
										$data['podcastFeedURL'] = get_category_feed_link($input->term_id);
									break;
								}
							} else {
								$data['podcastFeedURL'] = site_url().'/?feed=rss2&cat='.$input->term_id;
							}
						}
					}
				}
				
				// some ids of category input fields have changed with WP 3.0
				$wp_version_parts = explode('.', $wp_version);
				if (is_array($wp_version_parts)) {
					$main_wp_version = $wp_version_parts[0];
				} else {
					$main_wp_version = 0;
				}
				
				echo '<div class="wrap">'."\n";
				
				if ( TRUE == version_compare($wp_version, '2.7', '>=') ) {
					echo '<div id="podpress-icon" class="icon32"><br /></div>';
				} 

				echo '	<h2>'.sprintf(__('podPress %1$s Casting', 'podpress'), $taxonomy_str).'</h2>'."\n";
				echo '	<label for="categoryCasting"><strong>'.sprintf(__('Enable %1$s Casting', 'podpress'), $taxonomy_str).'</strong></label>  <a href="javascript:void(null);" onclick="javascript: podPressShowHideDiv(\'categoryCastingHelp\');">(?)</a>:';
				echo '	<input type="checkbox" name="categoryCasting" id="categoryCasting" '; if($data['categoryCasting'] == 'true') { echo 'checked="checked"'; } echo ' onclick="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');"/>'."\n";
				echo '	<div id="categoryCastingHelp" style="display: none;">'."\n";
				echo '		'.__('This feature is for cases in which you want to host more than one podcast in one blog or if you want to have different podcast feeds with different media files of certain file types per feed (e.g a feed which contains only .mp3 files).<br />Basically this feature gives you the opportunity to modify some of the feed elements and set them to other then as the general value from the Feed/iTunes Settings of podPress.<br/>For instance: If you organize your audio episodes in one category and the video episodes in a different category then you can modify the feed content describing values like the title or the description in the form below. This your category feeds will be more distinguishable from another.', 'podpress').'<br />'."\n";
				echo '	</div>'."\n";

				echo '  <div class="wrap" id="iTunesSpecificSettings" style="display: none; border: 0;">'."\n";
				
				podPress_DirectoriesPreview('edit_category_form');
				
				echo '		<fieldset class="options">'."\n";
				echo '		<legend>'.sprintf(__('%1$s Feed Options', 'podpress'), $taxonomy_str).'</legend>'."\n";
				echo '		<h3>'.__('iTunes Settings', 'podpress').'</h3>'."\n";
				echo '		<table class="podpress_feed_gensettings">'."\n";
				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="iTunesSubtitleChoice">'.__('iTunes:Subtitle', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="iTunesSubtitleChoice" name="iTunesSubtitleChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Global" '; if($data['iTunesSubtitleChoice'] != 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').'</option>'."\n";
				echo '						<option value="Custom" '; if($data['iTunesSubtitleChoice'] == 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<div id="iTunesSubtitleWrapper" style="display: none;">'."\n";
				echo '						<textarea name="iTunesSubtitle" class="podpress_wide_text_field" rows="2" cols="40">'.stripslashes($data['iTunesSubtitle']).'</textarea>'."\n";
				echo '					</div>'."\n";
				echo '					<div id="iTunesSubtitleHelp">'."\n";
				echo '						'.__('A few words which describe the feed title a little bit more (max. 255 characters).', 'podpress').' '.__('By default this is taken from the default iTunes:subtitle.', 'podpress')."\n";
				echo '					</div>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";

				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="iTunesSummaryChoice">'.__('iTunes:Summary', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="iTunesSummaryChoice" name="iTunesSummaryChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Global" '; if($data['iTunesSummaryChoice'] != 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').'</option>'."\n";
				echo '						<option value="Custom" '; if($data['iTunesSummaryChoice'] == 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<input type="hidden" id="global_iTunesSummary" value="'.attribute_escape(stripslashes($this->settings['iTunes']['summary'])).'" />'."\n";
				echo '					<div id="iTunesSummaryWrapper" style="display: none;">'."\n";
				echo '						<textarea name="iTunesSummary" id="iTunesSummary" class="podpress_wide_text_field" rows="6" cols="40" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'.stripslashes($data['iTunesSummary']).'</textarea>'."\n";
				echo '					</div>'."\n";
				echo '					<div id="iTunesSummaryHelp">'."\n";
				echo '						'.__('The description of the podcast.', 'podpress').' ' .__('By default this is taken from the default iTunes:Summary or the default description.', 'podpress')."\n";
				echo '					</div>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";

				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="iTunesImageChoice">'.__('iTunes:Image', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					'.__('The iTunes image should be a square image with <a href="http://www.apple.com/itunes/podcasts/specs.html#image" target="_blank">at least 1400 x 1400 pixels</a> as Apple writes in "<a href="http://www.apple.com/itunes/podcasts/specs.html" target="_blank">Making a Podcast</a>" of their own Podcasting Resources. iTunes supports JPEG and PNG images (the file name extensions should ".jpg" or ".png").', 'podpress')."\n";
				echo '					<br/>';
				echo '					<select id="iTunesImageChoice" name="iTunesImageChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Global" '; if($data['iTunesImageChoice'] != 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').'</option>'."\n";
				echo '						<option value="Custom" '; if($data['iTunesImageChoice'] == 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<div id="iTunesImageWrapper" style="display: none;">'."\n";
				echo '						<br/>';
				echo '						<input id="iTunesImage" type="text" name="iTunesImage" value="'.$data['iTunesImage'].'" class="podpress_wide_text_field" size="40" onchange="podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');"/>'."\n";
				echo '						<input id="global_iTunesImage" type="hidden" value="'.$this->settings['iTunes']['image'].'"/>'."\n";
				echo '					</div>'."\n";
				echo '					<br/>';
				echo '					<img id="itunes_image_display" style="width:300px; height:300px;" alt="'.__('Podcast Image - Big (If you can not see an image then the URL is wrong.)', 'podpress').'" src="" /><br />'."\n";
				echo '					<em>'.__('(This image is only a preview which is limited to 300 x 300 pixels.) ', 'podpress').'</em>';
				echo '				</td>'."\n";
				echo '			</tr>'."\n";

				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="iTunesAuthorChoice">'.__('iTunes:Author/Owner', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="iTunesAuthorChoice" name="iTunesAuthorChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Global" '; if($data['iTunesAuthorChoice'] != 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.podPress_stringLimiter(ucfirst(stripslashes($this->settings['iTunes']['author'])), 40).')</option>'."\n";
				echo '						<option value="Custom" '; if($data['iTunesAuthorChoice'] == 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<div id="iTunesAuthorWrapper" style="display: none;">'."\n";
				echo '						<input type="text" name="iTunesAuthor" class="podpress_wide_text_field" size="40" id="iTunesAuthor" value="'.attribute_escape(stripslashes($data['iTunesAuthor'])).'" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');"/>';
				echo '						<input type="hidden" id="global_iTunesAuthor" value="'.attribute_escape(stripslashes($this->settings['iTunes']['author'])).'" />'."\n";
				echo '					</div>'."\n";
				echo '					<div id="iTunesAuthorHelp">'."\n";
				echo '						'.__('Used if this Author is different than the feeds author.', 'podpress')."\n";
				echo '					</div>'."\n";
				echo '				</td>'."\n";
				echo '			<tr>'."\n";
				echo '			</tr>'."\n";
				echo '				<th>';
				echo '					<label for="iTunesAuthorEmailChoice">'.__('Owner E-mail address', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="iTunesAuthorEmailChoice" name="iTunesAuthorEmailChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Global" '; if($data['iTunesAuthorEmailChoice'] != 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.podPress_stringLimiter(ucfirst(stripslashes(get_option('admin_email'))), 40).')</option>'."\n";
				echo '						<option value="Custom" '; if($data['iTunesAuthorEmailChoice'] == 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<div id="iTunesAuthorEmailWrapper" style="display: none;">'."\n";
				echo '						<input type="text" name="iTunesAuthorEmail" class="podpress_wide_text_field" size="40" id="iTunesAuthorEmail" value="'.attribute_escape(stripslashes($data['iTunesAuthorEmail'])).'" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');"/>';
				echo '						<input type="hidden" id="global_iTunesAuthorEmail" value="'.attribute_escape(stripslashes(get_option('admin_email'))).'" />'."\n";
				echo '					</div>'."\n";
				echo '					<div id="iTunesAuthorEmailHelp">'."\n";
				echo '						'.__('Used if the owner of this category is different than the feeds owner.', 'podpress')."\n";
				echo '					</div>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";

				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="iTunesKeywordsChoice">'.__('iTunes:Keywords', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="iTunesKeywordsChoice" name="iTunesKeywordsChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Global" '; if($data['iTunesKeywordsChoice'] != 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.podPress_stringLimiter(stripslashes($this->settings['iTunes']['keywords']), 40).')</option>'."\n";
				echo '						<option value="Custom" '; if($data['iTunesKeywordsChoice'] == 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<div id="iTunesKeywordsWrapper">'."\n";
				echo '						'.__('a list of max. 12 comma separated words', 'podpress').'<br/><textarea name="iTunesKeywords" rows="4" cols="40">'.stripslashes($data['iTunesKeywords']).'</textarea>'."\n";
				echo '					</div>'."\n";
				echo '					<div id="iTunesKeywordsHelp">'."\n";
				echo '						'.__('Not visible in iTunes, but used for searches.', 'podpress')."\n";
				echo '					</div>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";

				echo '			<tr> '."\n";
				echo '				<th>';
				echo '					<label for="iTunesCategory_0">'.__('iTunes:Categories', 'podpress').'</label>';
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="iTunesCategory_0" name="iTunesCategory[0]" onchange="podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<optgroup label="'.__('Select Primary', 'podpress').'">'."\n";
				if ('' == trim($this->settings['iTunes']['category'][0])) {
					$current_global = '[ '.__('nothing', 'podpress').' ]';
				} else {
					$current_global = $this->settings['iTunes']['category'][0];
				}
				echo '						<option value="##Global##" '; if($data['iTunesCategory'][0] == '##Global##' || empty($data['iTunesCategory'][0])) { echo 'selected="selected"'; } echo '>'.__('Use Global', 'podpress').' ('.$current_global.')</option>'."\n";
				if (empty($data['iTunesCategory'][0])) {
					podPress_itunesCategoryOptions('##Global##');
				} else {
					podPress_itunesCategoryOptions(stripslashes($data['iTunesCategory'][0]));
				}
				echo '						</optgroup>'."\n";
				echo '					</select><br/>'."\n";
				echo '					<input type="hidden" id="global_iTunesCategory" value="'.attribute_escape($this->settings['iTunes']['category'][0]).'" />'."\n";
				echo '					<select name="iTunesCategory[1]">'."\n";
				echo '						<optgroup label="'.__('Select Second', 'podpress').'">'."\n";
				if ('' == trim($this->settings['iTunes']['category'][1])) {
					$current_global = '[ '.__('nothing', 'podpress').' ]';
				} else {
					$current_global = $this->settings['iTunes']['category'][1];
				}
				echo '						<option value="##Global##" '; if($data['iTunesCategory'][1] == '##Global##' || empty($data['iTunesCategory'][1])) { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.$current_global.')</option>'."\n";
				if (empty($data['iTunesCategory'][1])) {
					podPress_itunesCategoryOptions('##Global##');
				} else {
					podPress_itunesCategoryOptions(stripslashes($data['iTunesCategory'][1]));
				}
				echo '						</optgroup>'."\n";
				echo '					</select><br/>'."\n";
				echo '					<select name="iTunesCategory[2]">'."\n";
				echo '						<optgroup label="'.__('Select Third', 'podpress').'">'."\n";
				if ('' == trim($this->settings['iTunes']['category'][2])) {
					$current_global = '[ '.__('nothing', 'podpress').' ]';
				} else {
					$current_global = $this->settings['iTunes']['category'][2];
				}
				echo '						<option value="##Global##" '; if($data['iTunesCategory'][2] == '##Global##' || empty($data['iTunesCategory'][2])) { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.$current_global.')</option>'."\n";
				if (empty($data['iTunesCategory'][2])) {
					podPress_itunesCategoryOptions('##Global##');
				} else {
					podPress_itunesCategoryOptions(stripslashes($data['iTunesCategory'][2]));
				}
				echo '						</optgroup>'."\n";
				echo '					</select>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";

				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="iTunesExplicit">'.__('iTunes:Explicit', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="iTunesExplicit" name="iTunesExplicit" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="##Global##" '; if($data['iTunesExplicit'] == '##Global##' || empty($data['iTunesExplicit'])) { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.$this->settings['iTunes']['explicit'].')</option>'."\n";
				echo '						<option value="No" '; if($data['iTunesExplicit'] == 'No') { echo 'selected="selected"';	}	echo '>'.__('No', 'podpress').'</option>'."\n";
				echo '						<option value="Yes" '; if($data['iTunesExplicit'] == 'Yes') { echo 'selected="selected"';	}	echo '>'.__('Yes', 'podpress').'</option>'."\n";
				echo '						<option value="Clean" '; if($data['iTunesExplicit'] == 'Clean') { echo 'selected="selected"';	}	echo '>'.__('Clean', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<div id="iTunesExplicitHelp">'."\n";
				echo '					'.__('Setting to indicate (in iTunes) whether or not your podcast contains explicit language or adult content', 'podpress')."\n";
				echo '					<br/>'.__('"No" (default) - no indicator will show up', 'podpress')."\n";
				echo '					<br/>'.__('"Yes" - an "EXPLICIT" parental advisory graphic will appear next to your podcast artwork or name in iTunes', 'podpress')."\n";
				echo '					<br/>'.__('"Clean" - means that you are sure that no explicit language or adult content is included any of the episodes, and a "CLEAN" graphic will appear', 'podpress')."\n";
				echo '					<p>'.__('You have also the possibility to adjust this option for each post or page with at least one podcast episode (in the post/page editor).', 'podpress').'</p>'."\n";
				echo '					</div>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";

				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="iTunesBlock">'.__('iTunes:Block', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="iTunesBlock" name="iTunesBlock" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="##Global##" '; if($data['iTunesBlock'] != '##Global##' || empty($data['itunesBlock'])) { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.$this->settings['iTunes']['block'].')</option>'."\n";
				echo '						<option value="No" '; if($data['iTunesBlock'] == 'No') { echo 'selected="selected"';	}	echo '>'.__('No', 'podpress').'</option>'."\n";
				echo '						<option value="Yes" '; if($data['iTunesBlock'] == 'Yes') { echo 'selected="selected"';	}	echo '>'.__('Yes', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<div id="itunesBlockHelp">'."\n";
				echo '					'.__('Use this if you are no longer creating a podcast and you want it removed from the iTunes Store.', 'podpress')."\n";
				echo '					<br/>'.__('"No" (default) - the podcast appears in the iTunes Podcast directory', 'podpress')."\n";
				echo '					<br/>'.__('"Yes" - prevent the entire podcast from appearing in the iTunes Podcast directory', 'podpress')."\n";
				echo '					<p>'.__('You can also use such an option for each of your podcast episodes (in the post/page editor).', 'podpress').'</p>'."\n";
				echo '					</div>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";
				
				echo '			<tr> '."\n";
				echo '				<th>';
				echo '					<label for="iTunesFeedID">'.__('iTunes:FeedID', 'podpress').'</label>';
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<input name="iTunesFeedID" id="iTunesFeedID" type="text" value="'.attribute_escape($data['iTunesFeedID']).'" size="10" /> '.__('(Only relevant for the podPress Feed Buttons widget)', 'podpress');
				echo '				</td>'."\n";
				echo '			</tr>'."\n";

				echo '			<tr> '."\n";
				echo '				<th>';
				echo '					<label for="iTunesNewFeedURL">'.__('iTunes:New-Feed-Url', 'podpress').'</label>';
				echo '				</th>';
				echo '				<td>';
				echo '					<select name="iTunesNewFeedURL" id="iTunesNewFeedURL">'."\n";
				echo '						<option value="##Global##" '; if($data['iTunesNewFeedURL'] == '##Global##' || empty($data['iTunesNewFeedURL'])) { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').'</option>'."\n";
				echo '						<option value="Disable" '; if($data['iTunesNewFeedURL'] == 'Disable') { echo 'selected="selected"'; } echo '>'.__('Disable', 'podpress').'</option>'."\n";
				echo '						<option value="Enable" '; if($data['iTunesNewFeedURL'] == 'Enable') { echo 'selected="selected"'; } echo '>'.__('Enable', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '				</td>';
				echo '				<td>';
				echo '					'.__('If you want to change the URL of your podcast feed which you have used in the iTunes Store then change the "Podcast Feed URL" and set this option to "Enable" until the iTunes Store recognizes the new URL. This may take several days. "Enable" will add the <code>&lt;itunes:new-feed-url&gt;</code> tag to the RSS feeds and set the "Podcast Feed URL" as the new URL. For further information about "<a href="http://www.apple.com/itunes/podcasts/specs.html#changing" title="iTunes Podcasting Resources: Changing Your Feed URL" target="_blank">Changing Your Feed URL</a>" read on in the <a href="http://www.apple.com/itunes/podcasts/specs.html" target="_blank" title="iTunes Podcasting Resources: Making a Podcast">iTunes Podcasting Resources</a>.', 'podpress')."\n";
				echo '					<p><label for="podcastFeedURL"><strong>'.__('the new Feed URL', 'podpress').'</strong></label>';
				echo '					<br/>';
				echo '					<input type="text" id="podcastFeedURL" name="podcastFeedURL" class="podpress_wide_text_field" size="40" value="'.attribute_escape($data['podcastFeedURL']).'" /><br />'.__('The URL of your Podcast Feed. If you want to register your podcast at the iTunes Store or if your podcast is already listed there then this input field should contain the same URL as in the iTunes Store settings. If you want change the URL at the iTunes Store then please read first the help text of the iTunes:New-Feed-Url option.', 'podpress');
				echo '					<br /><input type="button" value="'.__('Validate your Feed','podpress').'" onclick="javascript: if(document.getElementById(\'podcastFeedURL\').value != \'\') { window.open(\'http://www.feedvalidator.org/check.cgi?url=\'+document.getElementById(\'podcastFeedURL\').value); }"/>'."\n";
				echo '				</p></td>'."\n";
				echo '			</tr>'."\n";

				echo '		</table>'."\n";
				
				
				echo '		<h3>'.__('General Feed Settings', 'podpress').'</h3>'."\n";
				echo '		<table class="podpress_feed_gensettings">'."\n";
				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="blognameChoice">'.__('Podcast title / Feed title', 'podpress').'</label>';
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="blognameChoice" name="blognameChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Append" '; if ($data['blognameChoice'] == 'Append' || empty($data['blognameChoice']) ) { echo 'selected="selected"'; } echo '>'.sprintf(__('Use the Site Title and append the name of the %1$s', 'podpress'), $taxonomy_str).'</option>'."\n";
				echo '						<option value="Global" '; if ( $data['blognameChoice'] == 'Global' ) { echo 'selected="selected"';	} echo '>'.__('Use the Site Title', 'podpress').'</option>'."\n";
				echo '						<option value="CategoryName" '; if ( $data['blognameChoice'] == 'CategoryName' ) { echo 'selected="selected"'; } echo '>'.__('Use the name of the category', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<input type="hidden" id="global_blogname" value="'.attribute_escape(stripslashes(get_option('blogname'))).'" /></td>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";
				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="blogdescriptionChoice">'.__('Description (Tagline)', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="blogdescriptionChoice" name="blogdescriptionChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Global" '; if($data['blogdescriptionChoice'] != 'CategoryDescription') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').'</option>'."\n";
				echo '						<option value="CategoryDescription" '; if($data['blogdescriptionChoice'] == 'CategoryDescription') { echo 'selected="selected"';	}	echo '>'.sprintf(__('Use %1$s Description', 'podpress'), $taxonomy_str).'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<input type="hidden" id="global_blogdescription" value="'.attribute_escape(stripslashes(get_option('blogdescription'))).'" />'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";
				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="rss_imageChoice">'.__('Blog/RSS Image (144 x 144 pixels)', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="rss_imageChoice" name="rss_imageChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Global" '; if($data['rss_imageChoice'] != 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').'</option>'."\n";
				echo '						<option value="Custom" '; if($data['rss_imageChoice'] == 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<div id="rss_imageWrapper" style="display: none;">'."\n";
				echo '						<br/>';
				echo '						<input id="rss_image" type="text" name="rss_image" value="'.$data['rss_image'].'" class="podpress_wide_text_field" size="40" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');"/>'."\n";
				echo '						<input id="global_rss_image" type="hidden" value="'.get_option('rss_image').'"/>'."\n";
				echo '					</div>'."\n";
				echo '					<br/>';
				echo '					<img id="rss_image_Display" style="width:144px; height:144px;" alt="'.__('Podcast Image - Small (If you can not see an image then the URL is wrong.)', 'podpress').'" src="" />'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";
				echo '			<tr>'."\n";
				echo '				<th>';
				echo '					<label for="rss_language">'.__('Language', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				$langs = podPress_itunesLanguageArray();
				echo '					<select id="rss_language" name="rss_language" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<optgroup label="'.__('Select Language', 'podpress').'">'."\n";
				echo '						<option value="##Global##" '; if($data['rss_language'] == '##Global##' || empty($data['rss_language'])) { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' - '.$langs[get_option('rss_language')].' ['.get_option('rss_language').']</option>'."\n";
				podPress_itunesLanguageOptions($data['rss_language']);
				echo '						</optgroup>'."\n";
				echo '					</select>'."\n";
				echo '					<input type="hidden" id="global_rss_language" value="'.$langs[get_option('rss_language')].'['.attribute_escape(get_option('rss_language')).']" /></td>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";
				echo '		</table>'."\n";
				
				echo '		<h3>'.__('Further Feed Settings', 'podpress').'</h3>'."\n";
				echo '		<table class="podpress_feed_gensettings">'."\n";
				echo '			<tr>'."\n";
				echo '				<th>'."\n";
				echo '					<label for="rss_copyrightChoice">'.__('Feed Copyright / license name', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="rss_copyrightChoice" name="rss_copyrightChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Global" '; if($data['rss_copyrightChoice'] != 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.podPress_stringLimiter(ucfirst(stripslashes($this->settings['rss_copyright'])), 40).')</option>'."\n";
				echo '						<option value="Custom" '; if($data['rss_copyrightChoice'] == 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<div id="rss_copyrightWrapper" style="display: none;">'."\n";
				echo '						<input type="text" name="rss_copyright" class="podpress_wide_text_field" size="40" id="rss_copyright" value="'.attribute_escape(stripslashes($data['rss_copyright'])).'" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');"/>';
				echo '					</div>'."\n";
				echo '					<input type="hidden" id="global_rss_copyright" value="'.attribute_escape(stripslashes($this->settings['rss_copyright'])).'" />'."\n";
				echo '					<div id="rss_copyrightHelp">'."\n";
				echo '						'.__('Enter the copyright string or license name. For example: Copyright &#169 by Jon Doe, 2009 OR <a href="http://creativecommons.org/licenses/by-nc-sa/2.5/" target="_blank">CreativeCommons Attribution-Noncommercial-Share Alike 2.5</a>', 'podpress')."\n";
				echo '						<p>'."\n";
				echo '						'.__('Used if this copyright phrase should be different than the global copyright phrase.', 'podpress')."\n";
				echo '						</p>'."\n";
				echo '					</div>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";
				echo '			<tr>'."\n";
				echo '				<th>'."\n";
				echo '					<label for="rss_license_urlChoice">'.__('URL to the full Copyright / license text', 'podpress').'</label>'."\n";
				echo '				</th>';
				echo '				<td colspan="2">';
				echo '					<select id="rss_license_urlChoice" name="rss_license_urlChoice" onchange="javascript: podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\');">'."\n";
				echo '						<option value="Global" '; if($data['rss_license_urlChoice'] != 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Use Global', 'podpress').' ('.podPress_stringLimiter(ucfirst(stripslashes($this->settings['rss_license_url'])), 40).')</option>'."\n";
				echo '						<option value="Custom" '; if($data['rss_license_urlChoice'] == 'Custom') { echo 'selected="selected"';	}	echo '>'.__('Insert custom value', 'podpress').'</option>'."\n";
				echo '					</select>'."\n";
				echo '					<div id="rss_license_urlWrapper" style="display: none;">'."\n";
				echo '						<input name="rss_license_url" type="text" id="rss_license_url" class="podpress_wide_text_field" value="'.attribute_escape($data['rss_license_url']).'" size="65%" />'."\n";
				echo '					</div>'."\n";
				echo '					<input type="hidden" id="global_rss_license_url" value="'.attribute_escape($this->settings['rss_license_url']).'" />'."\n";
				echo '					<div id="rss_license_urlHelp">'."\n";
				echo '						'.__('If you use a special license like a <a href="http://creativecommons.org/licenses" target="_blank" title="Creative Commons">Creative Commons</a> License for your news feeds then enter the complete URL (e.g. <a href="http://creativecommons.org/licenses/by-nc-sa/2.5/" target="_blank">http://creativecommons.org/licenses/by-nc-sa/2.5/</a>) to the full text of this particular license here.', 'podpress')."\n";
				echo '						<p>'.__('Used if this license URL should be different than the global license URL.', 'podpress').'</p>'."\n";
				echo '					</div>'."\n";
				echo '				</td>'."\n";
				echo '			</tr>'."\n";
				echo '			<tr>'."\n";
				$filetypes = podPress_filetypes();
				$selected_types = $data['FileTypes'];
				if (FALSE === is_array($data['FileTypes'])) {
					$selected_types = array();
				}
				echo '				<th>';
				echo '				'.__('inlude only files of this(these) type(s)', 'podpress');
				echo '				</th>'."\n";
				echo '				<td>';
				echo '					<select id="filenameextensionfilter" name="FileTypes[]" size="5" multiple="multiple" style="height:15em;">'."\n";		
				echo '					<optgroup label="'.attribute_escape(__('Select file types', 'podpress')).'">'."\n";
				foreach ( $filetypes as $key => $value ) {
					if ( TRUE == in_array($key, $selected_types) ) {
						$selected = ' selected="selected"';
					} else {
						$selected = '';
					}
					if ($key !== 'audio_mp4') {
						echo '						<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
					}
				}
				echo '					</optgroup>'."\n";
				echo '					</select>'."\n";
				echo '				</td>'."\n";
				echo '				<td>';
				echo '					'.__('You can select one or more media file types and limit by this choice the number of posts which will appear in the this Feed. For instance: if you select MP4 Video then this Feed will only contain posts (of this category) with MP4 files. If a post has also files of other types then only the files of the selected type will be attached in this feed. (This filter bypasses the "Included in:" selection.)', 'podpress').'</p><p>'.__('Hold the key [SHIFT] or [CTRL] and use the left mouse button to select more than one value.<br />Hold [CTRL] and use the left mouse button to deselect values.', 'podpress');		
				echo '				</td>'."\n";
				echo '			</tr>'."\n";
				
				echo '			<tr>'."\n";
				echo '				<th>';
				echo '				<label for="show_only_podPress_podcasts">'.__('Include only posts with podPress attachments in this Feed', 'podpress').'</label>'."\n";
				echo '				</th>'."\n";
				echo '				<td>';
				if ( TRUE == isset($data['show_only_podPress_podcasts']) AND FALSE === $data['show_only_podPress_podcasts'] ) {
					echo '					<input type="checkbox" name="show_only_podPress_podcasts" id="show_only_podPress_podcasts" value="yes" />'."\n";
				} else {
					echo '					<input type="checkbox" name="show_only_podPress_podcasts" id="show_only_podPress_podcasts" value="yes" checked="checked" />'."\n";
				}
				echo '				</td>'."\n";
				echo '				<td>';
				echo '					'.__('works only while the File Type Filter is not in use', 'podpress');		
				echo '				</td>'."\n";
				echo '			</tr>'."\n";
				
				echo '		</table>'."\n";
				
				echo '		</fieldset>'."\n";
				echo '	</div>'."\n";

				echo '<script type="text/javascript">podPress_updateCategoryCasting('.$main_wp_version.', \''.$taxonomy.'\'); </script>';
				echo '</div>'."\n";
			}
		}

		/**
		* edit_category - saves Category Casting and Tag Casting settings (hooked in via the action hook edit_term)
		*
		* @package podPress
		* @since 8.8 (and before)
		*
		* @param str $term_id - the term id e.g. the ID number of a category
		* @param str $tt_id - taxonomy term id
		* @param str $taxonomy (optional) - the taxonomy type name e.g. category or post_tag (this parameter is the 3rd one of the action hook edit_term since WP 2.9)
		*
		*/
		function edit_category($term_id, $tt_id, $taxonomy='category') {
			global $wp_version;
			
			// because the $taxonomy parameter is only available since WP 2.9, it is necessary to determine the taxonomy name with own measures
			if ( TRUE == version_compare($wp_version, '2.9', '<') ) {
				$taxonomy = $this->get_taxonomy_by_ids($term_id, $tt_id);
			}
			
			if ( !isset($_POST['iTunesFeedID']) OR ('post_tag' !== $taxonomy AND 'category' !== $taxonomy) ) {
				return;
			}
			
			$blog_charset = get_bloginfo('charset');
			$data = array();
			if($_POST['categoryCasting'] == 'on') {
				$data['categoryCasting'] = 'true';
			} else {
				$data['categoryCasting'] = 'false';
			}

			$data['podcastFeedURL'] = clean_url($_POST['podcastFeedURL'], array('http', 'https'), 'db');
			$data['iTunesFeedID'] = intval(preg_replace('/[^0-9]/', '', $_POST['iTunesFeedID']));
			$data['iTunesNewFeedURL'] = $_POST['iTunesNewFeedURL'];
			$data['blognameChoice'] = $_POST['blognameChoice'];
			//~ // Attention: $data['blogname'] is here the name of the category
			//~ if ( TRUE == version_compare($wp_version, '3.0', '<') ) {
				//~ $data['blogname'] = htmlspecialchars(strip_tags(trim($_POST['cat_name'])), ENT_QUOTES, $blog_charset);
			//~ } else {
				//~ $data['blogname'] = htmlspecialchars(strip_tags(trim($_POST['name'])), ENT_QUOTES, $blog_charset);
			//~ }
			$data['blogdescriptionChoice'] = $_POST['blogdescriptionChoice'];
			$data['iTunesSubtitleChoice'] = $_POST['iTunesSubtitleChoice'];
			$data['iTunesSubtitle'] = htmlspecialchars(strip_tags(trim($_POST['iTunesSubtitle'])), ENT_QUOTES, $blog_charset);
			$data['iTunesSummaryChoice'] = $_POST['iTunesSummaryChoice'];
			$data['iTunesSummary'] = htmlspecialchars(strip_tags(trim($_POST['iTunesSummary'])), ENT_QUOTES, $blog_charset);
			$data['iTunesKeywordsChoice'] = $_POST['iTunesKeywordsChoice'];
			$data['iTunesKeywords'] = $this->cleanup_itunes_keywords($_POST['iTunesKeywords'], $blog_charset);
			$data['iTunesAuthorChoice'] = $_POST['iTunesAuthorChoice'];
			$data['iTunesAuthor'] = htmlspecialchars(strip_tags(trim($_POST['iTunesAuthor'])), ENT_QUOTES, $blog_charset);
			$data['iTunesAuthorEmailChoice'] = $_POST['iTunesAuthorEmailChoice'];
			$data['iTunesAuthorEmail'] = htmlspecialchars(strip_tags(trim($_POST['iTunesAuthorEmail'])), ENT_QUOTES, $blog_charset);
			$data['rss_language'] = htmlspecialchars(strip_tags(trim($_POST['rss_language'])), ENT_QUOTES, $blog_charset);
			$data['iTunesExplicit'] = $_POST['iTunesExplicit'];
			$data['iTunesBlock'] = $_POST['iTunesBlock'];
			$data['iTunesImageChoice'] = $_POST['iTunesImageChoice'];
			$data['iTunesImage'] = clean_url($_POST['iTunesImage'], array('http', 'https'), 'db');
			$data['rss_imageChoice'] = $_POST['rss_imageChoice'];
			$data['rss_image'] = clean_url($_POST['rss_image'], array('http', 'https'), 'db');
			$data['rss_copyrightChoice'] = $_POST['rss_copyrightChoice'];
			$data['rss_copyright'] = htmlspecialchars(strip_tags(trim($_POST['rss_copyright'])), ENT_QUOTES, $blog_charset);
			$data['rss_license_url'] = clean_url($_POST['rss_license_url'], array('http', 'https'), 'db');
			$data['rss_license_urlChoice'] = $_POST['rss_license_urlChoice'];
			$data['iTunesCategory'] = $_POST['iTunesCategory'];
			$data['FileTypes'] = $_POST['FileTypes'];
			if ( isset($_POST['show_only_podPress_podcasts']) ) {
				$data['show_only_podPress_podcasts'] = TRUE;
			} else {
				$data['show_only_podPress_podcasts'] = FALSE;
			}
			delete_option('podPress_'.$taxonomy.'_'.$term_id);
			podPress_add_option('podPress_'.$taxonomy.'_'.$term_id, $data);
		}

		//~ function delete_category($term_id) {
			//~ //echo 'told to delete';
			//~ //delete_option('podPress_category_'.$term_id);
		//~ }
		
		/**
		* get_taxonomy_by_ids - determines the taxonomy name with the help of the term id and the taxonomy term id
		*
		* @package podPress
		* @since 8.8.10.14
		*
		* @param str $term_id - the term id e.g. a category id
		* @param str $tt_id - taxonomy term id
		*
		* @return str - returns the real taxonomy name or as the default "category"
		*
		*/
		function get_taxonomy_by_ids($term_id, $tt_id) {
			GLOBAL $wpdb;
			if ( FALSE === empty($term_id) AND FALSE === empty($tt_id) ) {
				$taxonomy = $wpdb->get_var('SELECT taxonomy FROM '.$wpdb->prefix.'term_taxonomy WHERE term_taxonomy_id = '.$tt_id.' AND term_id =  '.$term_id);
				if ( TRUE === empty($taxonomy) ) {
					return 'category';
				} else {
					return $taxonomy;
				}
			} else {
				return 'category';
			}
		}
	}
?>