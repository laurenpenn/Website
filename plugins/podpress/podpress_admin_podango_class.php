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
			$this->podPress_class();
			return;
		}

		/*************************************************************/
		/* Functions for editing and saving posts                    */
		/*************************************************************/

		function settings_podango_edit() {
			GLOBAL $wpdb, $wp_rewrite;
			podPress_isAuthorized();
			if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
				echo '<div id="message" class="updated fade"><p>'. __('Settings Saved', 'podpress').'</p></div>';
			}

			echo '<div class="wrap">'."\n";
			echo '	<h2>'.__('Podango Options', 'podpress').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.mightyseek.com/podpress/#download" target="_new"><img src="http://www.mightyseek.com/podpress_downloads/versioncheck.php?current='.PODPRESS_VERSION.'" alt="'.__('Checking for updates... Failed.', 'podpress').'" border="0" /></a></h2>'."\n";
			echo '	<form method="post">'."\n";
			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('Podango Integration', 'podpress').' <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'podangoIntegrationHelp\');">(?)</a></legend>'."\n";
			echo '		<table width="100%" cellspacing="2" cellpadding="5" class="editform">'."\n";
			echo '			<tr>'."\n";
			echo '				<th width="33%" valign="top"><label for="enablePodangoIntegration">'.__('Enable Podango Integration', 'podpress').':</label></th>'."\n";
			if(!$this->settings['enablePodangoIntegration']){
				$showPodangoOptions = 'style="display: none;"';
			}
			echo '				<td>'."\n";
			echo '					<input type="checkbox" name="enablePodangoIntegration" id="enablePodangoIntegration" '; if($this->settings['enablePodangoIntegration']) { echo 'checked="checked"'; } echo " onclick=\"javascript: podPressShowHideRow('podangoUserKeyWrapper'); podPressShowHideRow('podangoPassKeyWrapper'); podPressShowHideRow('podangoDefaultPodcastWrapper'); podPressShowHideRow('podangoTranscribeWrapper');\" />\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr id="podangoUserKeyWrapper" '.$showPodangoOptions.'>'."\n";
			echo '				<th width="33%" valign="top"><label for="podangoUserKey">'.__('User Access Key', 'podpress').':</label></th>'."\n";
			echo '				<td><input name="podangoUserKey" id="podangoUserKey" size="30" type="text" value="'.htmlentities($this->settings['podangoUserKey']).'"> <a href="http://www.podango.com/podcasts/api_setup.php">Can be found here</a></td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr id="podangoPassKeyWrapper" '.$showPodangoOptions.'>'."\n";
			echo '				<th width="33%" valign="top"><label for="podangoPassKey">'.__('Pass Key', 'podpress').':</label></th>'."\n";
			echo '				<td><input name="podangoPassKey" id="podangoPassKey" size="30" type="text" value="'.htmlentities($this->settings['podangoPassKey']).'"> <a href="http://www.podango.com/podcasts/api_setup.php">Can be found here</a></td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr id="podangoDefaultPodcastWrapper" '.$showPodangoOptions.'>'."\n";
			echo '				<th width="33%" valign="top"><label for="podangoDefaultPodcast">'.__('Default Podcast', 'podpress').':</label></th>'."\n";
			echo '				<td>'."\n";

			$podangoPodcastList = array(0=>array('title'=>'Save Options to update this list'));
			if($this->settings['enablePodangoIntegration']){
				$podangoPodcastList = $this->podangoAPI->GetPodcasts(true);
				if(count($podangoPodcastList) == 0) {
					$podangoPodcastList = array(0=>array('title'=>'Could not find your Podcast/Channel'));
				}
			}
			echo '					<select name="podangoDefaultPodcast" id="podangoDefaultPodcast">'."\n";
			echo '						<option value="##ALL##" '; if($this->settings['podangoDefaultPodcast'] == '##ALL##') { echo 'selected="selected"'; } echo '>Enable them all on this site</option>'."\n";
			foreach ($podangoPodcastList as $k=>$v) {
				echo '						<option value="'.$k.'" '; if($this->settings['podangoDefaultPodcast'] == $k) { echo 'selected="selected"'; } echo '>'.$v['Title'].'</option>'."\n";
			}
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr id="podangoTranscribeWrapper" '.$showPodangoOptions.'>'."\n";
			echo '				<th width="33%" valign="top"><label for="podangoDefaultTranscribe">'.__('Transcribe', 'podpress').':</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="checkbox" name="podangoDefaultTranscribe" id="podangoDefaultTranscribe" '; if($this->settings['podangoDefaultTranscribe']) { echo 'checked="checked"'; } echo "/>&nbsp;&nbsp;Transcriptions cost $1.00/minute, and currently are deducted from your ad earnings.\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr id="podangoIntegrationHelp" style="display: none;">'."\n";
			echo '				<td colspan="2">';
			echo '					podPress users can gain additional functionality when used in combination with Podango hosting.<br/>'."\n";
			echo '					Letm e count the ways<br/>'."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";

			echo '	<input type="hidden" name="podPress_submitted" value="podango" />'."\n";
			echo '	<p class="submit"> '."\n";
			echo '	<input type="submit" name="Submit" value="'.__('Update Options', 'podpress').' &raquo;" /> '."\n";
			echo '	</p> '."\n";
			echo '	</form> '."\n";

			if(true) {
				echo '	<form method="post">'."\n";
				echo '	<fieldset class="options">'."\n";
				echo '		<legend>'.__('Migration Process', 'podpress').'</legend>'."\n";
				echo '		<table width="100%" cellspacing="2" cellpadding="5" class="editform">'."\n";
				echo '			<tr>'."\n";
				echo '				<th width="33%" valign="top"><label for="podangdoMigration">'.__('Re-reference all media files to the podango hosted version.', 'podpress').':</label></th>'."\n";
				echo '				<td valign="top">'."\n";
				echo '					<input type="checkbox" name="podangdoMigration" id="podangdoMigration"/>'."\n";
				echo '				</td>'."\n";
				echo '			</tr> '."\n";
				echo '			<tr>'."\n";
				echo '				<th width="33%" valign="top"><label for="podangoUnMigration">'.__('Remove Podango references from all media files to local URL.', 'podpress').':</label></th>'."\n";
				echo '				<td valign="top">'."\n";
				echo '					<input type="checkbox" name="podangoUnMigration" id="podangoUnMigration"/>'."\n";
				echo '				</td>'."\n";
				echo '			</tr> '."\n";
				echo '		</table>'."\n";
				echo '	</fieldset>'."\n";
				echo '	<input type="hidden" name="podPress_submitted" value="podango" />'."\n";
				echo '	<p class="submit"> '."\n";
				echo '	<input type="submit" name="Submit" value="'.__('Update Options', 'podpress').' &raquo;" /> '."\n";
				echo '	</p> '."\n";
				echo '	</form> '."\n";
			}
			echo '</div>'."\n";
		}

		function settings_podango_save() {
			GLOBAL $wpdb;
			if(function_exists('wp_cache_flush')) {
				wp_cache_flush();
			}

			if(isset($_POST['podangdoMigration'])) {
				$podangoMediaFiles = $this->podangoAPI->GetMediaFiles();
				if(is_array($podangoMediaFiles) && !empty($podangoMediaFiles)) {
					foreach ($podangoMediaFiles as $v) {
						if(!empty($v['EpisodeID'])) {
							$pmf[$v['Filename']] = array('EpisodeID'=>$v['EpisodeID'], 'ID'=>$v['ID']);
						}
					}

					$sql = "SELECT *
					        FROM ".$wpdb->prefix."postmeta 
									WHERE meta_key = '_podPressMedia'";
									
					$metadata = $wpdb->get_results($sql);
					if($metadata) {
						$posts2convert = array();
						foreach ($metadata as $v) {
							$save = false;
							$v->meta_value = unserialize($v->meta_value);
							foreach ($v->meta_value as $k=>$subv) {
								$fn = podpress_getfilename($subv['URI']);
								if(isset($pmf[$fn])) {
									$v->meta_value[$k]['URI'] = 'Podango:'.$this->settings['podangoDefaultPodcast'].':'.$pmf[$fn]['ID'].':'.$pmf[$fn]['EpisodeID'].':'.$fn;
									$save = true;
								}
							}
							if($save) {
								$v->meta_value = serialize($v->meta_value);
								$sql = "update ".$wpdb->prefix."postmeta set meta_value = '".$v->meta_value."' where meta_id=".$v->meta_id;
								$wpdb->query($sql);
							}
						}
					}
				}
			} elseif(isset($_POST['podangoUnMigration'])) {
					$sql = "SELECT *
					        FROM ".$wpdb->prefix."postmeta 
									WHERE meta_key = '_podPressMedia'";
									
					$metadata = $wpdb->get_results($sql);
					if($metadata) {
						$posts2convert = array();
						foreach ($metadata as $v) {
							$save = false;
							$v->meta_value = unserialize($v->meta_value);
							foreach ($v->meta_value as $k=>$subv) {
								$fn = podpress_getfilename($subv['URI']);
								if(isset($fn)) {
									$pos = strrpos($fn, ':');
									$v->meta_value[$k]['URI'] =  substr($fn, $pos);
									$save = true;
								}
							}
							if($save) {
								$v->meta_value = serialize($v->meta_value);
								$sql = "update ".$wpdb->prefix."postmeta set meta_value = '".$v->meta_value."' where meta_id=".$v->meta_id;
								$wpdb->query($sql);
							}
						}
					}
			} else {
				if(isset($_post['enablepodangointegration'])) {
					$this->settings['enablepodangointegration'] = true;
				} else {
					$this->settings['enablepodangointegration'] = false;
				}

				if(isset($_POST['podangoUserKey'])) {
					$this->settings['podangoUserKey'] = $_POST['podangoUserKey'];
				}

				if(isset($_POST['podangoPassKey'])) {
					$this->settings['podangoPassKey'] = $_POST['podangoPassKey'];
				}

				if(isset($_POST['podangoDefaultPodcast']) && $_POST['podangoDefaultPodcast'] !== 0) {
					$this->settings['podangoDefaultPodcast'] = $_POST['podangoDefaultPodcast'];
				}

				if(isset($_POST['podangoDefaultTranscribe'])) {
					$this->settings['podangoDefaultTranscribe'] = true;
				} else {
					$this->settings['podangoDefaultTranscribe'] = false;
				}

				delete_option('podPress_config');
				podPress_add_option('podPress_config', $this->settings);
			}

			$location = get_option('siteurl') . '/wp-admin/admin.php?page=podpress/podpress_podango.php&updated=true';
			header('Location: '.$location);
			exit;
		}
	}
?>