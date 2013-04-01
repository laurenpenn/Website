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

		function settings_general_edit() {
			GLOBAL $wpdb, $wp_rewrite, $wp_version;
			podPress_isAuthorized();
			if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
				echo '<div id="message" class="updated fade"><p>'. __('Settings Saved', 'podpress').'</p></div>';
			} elseif (isset($_GET['updated']) && $_GET['updated'] != 'true') {
				echo '<div id="message" class="error fade"><p>'. __('<strong>Error:</strong> Unable to save the settings', 'podpress').'</p></div>';
			}

			// ntm: where is the check? some times this message appears despite the fact that the theme has the necessary functions and hooks
			// Because it is not a hundred percent reliable I deactivate it for now
			//~ if(!$this->settings['compatibilityChecks']['wp_head'] || !$this->settings['compatibilityChecks']['wp_footer']) {
				//~ echo '<div class="wrap">'."\n";
				//~ if($this->settings['compatibilityChecks']['themeTested']) {
					//~ echo '	<h2>'.__('Theme Compatibility Problem', 'podpress').'</h2>'."\n";
				//~ } else {
					//~ echo '	<h2>'.__('Theme Compatibility Check Required', 'podpress').'</h2>'."\n";
				//~ }
				//~ echo '	<fieldset class="options">'."\n";
				//~ if($this->settings['compatibilityChecks']['themeTested']) {
				//~ echo '		<legend>'.__('Current Theme is not compliant', 'podpress').'</legend>'."\n";
				//~ } else {
				//~ echo '		<legend>'.__('Current Theme needs to be tested', 'podpress').'</legend>'."\n";
				//~ }
				//~ echo '		<table width="100%" cellspacing="2" cellpadding="5" class="editform">'."\n";
				//~ echo '			<tr>'."\n";
				//~ echo '				<td>'."\n";
				//~ if($this->settings['compatibilityChecks']['themeTested']) {
					//~ echo '				podPress has found the "'.get_current_theme().'" theme fails to meet important requirements.<br /><br />'."\n";
				//~ } else {
					//~ echo '				podPress has not yet detected the "'.get_current_theme().'" theme to be compliant. Please visit your <a href="'.podPress_siteurl().'">main blog page</a> for podPress to re-check.<br /><br />'."\n";
				//~ }

				//~ if(!$this->settings['compatibilityChecks']['wp_head']) {
					//~ echo '				The header.php in your theme needs to be calling wp_head(); before the closing head tag.<br />'."\n";
					//~ echo '				Change this:<br />'."\n";
					//~ echo '				<code>&lt;head&gt;</code><br />'."\n";
					//~ echo '				To this:<br />'."\n";
					//~ echo '				<code>&lt;?php wp_head(); ?&gt;'."<br />\n".'&lt;head&gt;</code><br />'."\n";
					//~ echo '				<br />'."\n";
				//~ }
				//~ if(!$this->settings['compatibilityChecks']['wp_footer']) {
					//~ echo '				The footer.php in your theme needs to be calling wp_footer(); before the closing body tag.<br />'."\n";
					//~ echo '				Change this:<br />'."\n";
					//~ echo '				<code>&lt;/body&gt;</code><br />'."\n";
					//~ echo '				To this:<br />'."\n";
					//~ echo '				<code>&lt;?php wp_footer(); ?&gt;'."<br />\n".'&lt;/body&gt;</code><br />'."\n";
				//~ }
				//~ echo '				Look at the default theme files for example.<br />'."\n";
				//~ echo '				</td>'."\n";
				//~ echo '			</tr> '."\n";
				//~ echo '		</table>'."\n";
				//~ echo '	</fieldset>'."\n";
				//~ echo '</div>'."\n";
			//~ }

			echo '<div class="wrap">'."\n";
			if ( TRUE == version_compare($wp_version, '2.7', '>=') ) {
				echo '<div id="podpress-icon" class="icon32"><br /></div>';
			} 
			if ( TRUE == version_compare($wp_version, '2.8', '>=') ) {
				echo '	<h2>'.__('General Settings', 'podpress').'</h2>'."\n";
				// get the plugins version information via the WP plugins version check
				if ( TRUE == version_compare($wp_version, '2.9', '>=') ) {
					$versioninfo = get_site_transient( 'update_plugins' );
				} else {
					$versioninfo = get_transient( 'update_plugins' );
				}
				// If there is a new version then there is a 'response'. This is the method from the plugins page. 
				if ( FALSE !== isset($versioninfo->response[plugin_basename(dirname(__FILE__).'/podpress.php')]->new_version) ) {
					echo '<div class="podpress_notice"><p><a href="http://wordpress.org/extend/plugins/podpress/" target="_blank">'.__('a new podPress version is available', 'podpress').'</a></p></div>';
				}
			} else {
				echo '	<h2>'.__('General Settings', 'podpress').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.mightyseek.com/podpress/#download" target="_new"><img src="http://www.mightyseek.com/podpress_downloads/versioncheck.php?current='.PODPRESS_VERSION.'" alt="'.__('Checking for updates... Failed.', 'podpress').'" border="0" /></a></h2>'."\n";
			}
			
			
			$is_stats_upgr = podPress_isset_upgrade_status('podpress_update_stats_table');
			$is_statcounts_upgr = podPress_isset_upgrade_status('podpress_update_statcounts_table');
			
			if ( TRUE === $is_stats_upgr  OR TRUE === $is_statcounts_upgr ) {
				$upgr_process_nr = '11092011';
				
				echo "\n".'		<div id="podpress_dialog_stats_upgr" title="'.attribute_escape(__('Finish the plugin upgrade and correct the download statistic:', 'podpress')).'" style="display:none;">'."\n";
				
				if ( defined('NONCE_KEY') AND is_string(constant('NONCE_KEY')) AND '' != trim(constant('NONCE_KEY')) ) {
					$nonce_key = constant('NONCE_KEY');
				} else {
					$nonce_key = 'Af|F07*wC7g-+OX$;|Z5;R@Pi]ZgoU|Zex8=`?mO-Mdvu+WC6l=6<O^2d~+~U3MM';
				}
				$max_time = podPress_get_max_execution_time();
				echo '<h2 id="podpress_upgr_header">'.sprintf(__('Upgrade of the data base table: %1$s','podpress'), 'wp_podpress_<span id="podpress_upgr_header_table_name"></span>');
				if ( TRUE === $is_stats_upgr  AND TRUE === $is_statcounts_upgr ) {
					echo '<div id="podpress_upgr_part_counter" style="float:right"><span id="podpress_upgr_part_counter_current">1</span> / <span id="podpress_upgr_part_counter_total">2</span></div>';
				}
				echo '</h2>';
				echo '<p>';
				echo '<strong>'.__('What is the reason for this upgrade?', 'podpress').'</strong><br />';
				echo __('Some of the podPress versions since 8.8.10 have stored the download statistic results in some cases (if the file names contain <a href="http://en.wikipedia.org/wiki/Percent-encoding#Types_of_URI_characters" target="_blank" title="en.Wiki: persent-encoding">reserved URL characters</a>) with wrong encoded names in the data base. For instance: A file name with such reserved characters is <em>podcast(nr1).mp3</em>. It was possible that podPress had created an entry for <em>podcast(nr1).mp3</em> (with the brackets as they are) and one entry with a different encoding mechanism: <em>podcast%28nr1%29.mp3</em>. The problem is that only one of these entries would show up on the statistic result pages. But this upgrade will correct the entries in the wp_podpress_statcounts and wp_podpress_stats tables.', 'podpress');
				echo '</p><p>';
				echo '<strong>'.__('How it works and what you need to do', 'podpress').'</strong><br />';
				echo __('(The statistic feature is deativated while this dialog is open.)', 'podpress').'<br />';
				echo __('The upgrade of the statistics data base tables may require some time - probably more time than this part of the plugin is allowed to run at once. (The limit is called max_execution_time.) That is the reason why your help is needed to complete the process.', 'podpress');
				echo __('Basically you need to start the process again as long as there are not upgraded rows left in the data base tables.', 'podpress');
				echo '<br />'.__('You may choose the increment (number of rows which will be upgraded at once). You can speed up the process. But since the time which is needed to update a certain amount of rows may fluctuate depending on the usage of the web server of your blog, it is recommended to try a low increment value at first and increase it carefully.', 'podpress');
				echo '</p>';
				echo '<p>';
				echo sprintf(__('In this case the max. execution time for the blog software is: %1$s seconds.', 'podpress'), '<span id="podpress_upgr_max_execution_time">'.number_format($max_time).'</span>')."\n";
				echo '</p>';
				echo '<p>';
				echo sprintf(__('The time needed to correct %1$s rows in the data base: %2$s  seconds', 'podpress'), '<span id="podpress_upgr_used_increment">...</span>', '<span id="podpress_upgr_elapsed_time"></span>')."\n";
				//~ echo '<br />'.sprintf(__('first increment: %1$s.', 'podpress'), '<span id="podpress_upgr_increment_limit_nr1">10</span>');
				echo '</p>';
				echo '<input type="hidden" id="podpress_ajax_nonce_key" value="'.attribute_escape(wp_create_nonce($nonce_key)).'" /> '."\n";
				echo '<input type="hidden" id="podpress_upgr_process_nr" value="'.$upgr_process_nr.'" /> '."\n";
				if ( TRUE === $is_stats_upgr ) {
					echo '<input type="hidden" id="podpress_is_stats_upgr" value="true" /> '."\n";
				} else {
					echo '<input type="hidden" id="podpress_is_stats_upgr" value="false" /> '."\n";
				}
				if ( TRUE === $is_statcounts_upgr ) {
					echo '<input type="hidden" id="podpress_is_statcounts_upgr" value="true" /> '."\n";
				} else {
					echo '<input type="hidden" id="podpress_is_statcounts_upgr" value="false" /> '."\n";
				}
				
				echo '<div id="podPress_upgrade_form">';
				echo '</div>'."\n";
				
				echo '<input type="hidden" name="podPress_upgrade_stats_table_Submit" value="'.__('correct the entries ...', 'podpress').' &raquo;" /> '."\n";
				if ( TRUE === $is_stats_upgr AND TRUE === $is_statcounts_upgr ) {
					echo '<input type="hidden" name="podPress_upgrade_statcounts_table_finished" value="'.__('finish the first part of the upgrade', 'podpress').' &raquo;" /> '."\n";
					echo '<input type="hidden" name="podPress_upgrade_stats_table_finished" value="'.__('finish the upgrade', 'podpress').' &raquo;" /> '."\n";
				} else {
					echo '<input type="hidden" name="podPress_upgrade_statcounts_table_finished" value="'.__('finish the upgrade', 'podpress').' &raquo;" /> '."\n";
					echo '<input type="hidden" name="podPress_upgrade_stats_table_finished" value="'.__('finish the upgrade', 'podpress').' &raquo;" /> '."\n";
				}
				echo '	</div>'."\n";
			}
		
			
			
			echo '	<form method="post">'."\n";

			if ( function_exists('wp_nonce_field') ) { // since WP 2.0.4
				wp_nonce_field('podPress_general_options_nonce');
			}

			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('Location of the Media Files', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="mediaWebPath">'.__('URL of the media files directory', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'mediaWebPathHelp\');">(?)</a>:</th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="text" id="mediaWebPath" name="mediaWebPath" class="podpress_wide_text_field" size="40" value="'.attribute_escape(stripslashes($this->settings['mediaWebPath'])).'" /><br />'."\n";
			if(!isset($this->settings['mediaWebPath']) || empty($this->settings['mediaWebPath'])){
				echo "<br />\n";
				echo __('Suggested', 'podpress').': <code>'.$this->uploadurl.'</code>'."\n";
			}
			echo '				</td>'."\n";
			echo '			</tr>'."\n";

			echo '			<tr id="mediaWebPathHelp" style="display: none;">'."\n";
			echo '				<th>&nbsp;</th>'."\n";
			echo '				<td>';
			echo '					'.sprintf(__('Point this to the full URL where you put your media files. It can be an URL to a local or remote location. The default value is the URL of the upload folder of this blog for example: <code>%1$s</code>', 'podpress'), $this->uploadurl)."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr>'."\n";
			echo '				<th><label for="mediaFilePath">'.__('Absolute path of the media files directory (optional)', 'podpress').'</label> <a href="javascript:void(null);" onclick="javascript: podPressShowHideRow(\'mediaFilePathHelp\');">(?)</a>:</th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="text" id="mediaFilePath" name="mediaFilePath" class="podpress_wide_text_field" size="40" value="'.attribute_escape(stripslashes($this->settings['mediaFilePath'])).'" /><br />'."\n";
			$this->checkLocalPathToMediaFiles();
			if(!empty($this->settings['autoDetectedMediaFilePath']) AND !empty($this->settings['mediaFilePath'])){
				echo __('This directory is not valid or not accessible.', 'podpress').'<br /> '.__('Suggested', 'podpress').': <code>'.$this->settings['autoDetectedMediaFilePath'].'</code>'."\n";
			} elseif (empty($this->settings['mediaFilePath'])) {
				echo __('e.g.', 'podpress').': <code>'.$this->settings['autoDetectedMediaFilePath'].'</code>'."\n";
			}
			echo '				</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr id="mediaFilePathHelp" style="display: none;">'."\n";
			echo '				<th>&nbsp;</th>'."\n";
			echo '				<td>';
			echo '					'.sprintf(__('This is an optional feature which is used to speed up the time/duration detection process, and is only possible if you host the media files on the same server as your website. Instead of having to go download a file, it can read e.g. the ID3 information of a media file directly.<br />Insert the full path name of the folder which includes the media files. The URL and this path needs to point to the same directory. This path name could look like <code>/home/yoursite/http/wp-content/files/</code> or can be the path of the upload folder of this blog: <code>%1$s</code>.', 'podpress'), $this->uploadpath)."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";

			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";

			$home_path = get_home_path();
			
			$trac_folder_exists = is_dir(ABSPATH.'podpress_trac');
			$trac_htaccess_exists = file_exists(ABSPATH.'podpress_trac/.htaccess');
			$trac_index_exists = file_exists(ABSPATH.'podpress_trac/index.php');
			if ( TRUE == $trac_htaccess_exists AND TRUE == $trac_htaccess_exists AND TRUE == $trac_htaccess_exists ) {
				$podpress_trac_in_place = TRUE;
				$podpress_trac_str = '<input type="hidden" id="podpress_trac_folder_in_place" value="true" />';
			} else { 
				$podpress_trac_in_place = FALSE;
				$podpress_trac_str = '<input type="hidden" id="podpress_trac_folder_in_place" value="false" />'."\n";
				$podpress_trac_str .= "\t\t\t\t\t".'<input type="hidden" id="podpress_trac_folder_exists" value="'.var_export($trac_folder_exists, TRUE).'" />'."\n";
				$podpress_trac_str .= "\t\t\t\t\t".'<input type="hidden" id="podpress_trac_index_exists" value="'.var_export($trac_index_exists, TRUE).'" />'."\n";
				$podpress_trac_str .= "\t\t\t\t\t".'<input type="hidden" id="podpress_trac_htaccess_exists" value="'.var_export($trac_htaccess_exists, TRUE).'" />';
			}
			if (FALSE === $podpress_trac_in_place AND $this->settings['statMethod'] == 'podpress_trac_dir') {
				$showpodpress_tracWarning = 'style="display: block;"';
			} else {
				$showpodpress_tracWarning = 'style="display: none;"';
			}
			
			$permalink_structure = get_option('permalink_structure');
			if ( FALSE === empty($permalink_structure) ) { 
				$usingpi = TRUE;
				$usingpi_str = '<input type="hidden" id="podpress_usingpermalinks" value="true" />';
			} else { 
				$usingpi = FALSE;
				$usingpi_str = '<input type="hidden" id="podpress_usingpermalinks" value="false" />';
			}

			//if (!$usingpi && !$hasHtaccess && $this->settings['statMethod'] == 'permalinks') {
			if (!$usingpi && $this->settings['statMethod'] == 'permalinks') {
				$showPermalinksWarning = 'style="display: block;"';
			} else {
				$showPermalinksWarning = 'style="display: none;"';
			}
			
			if ( function_exists('get_admin_url') ) {
				$adminurl = get_admin_url(); // since WP 3.0
			} elseif ( function_exists('admin_url') ) {
				$adminurl = admin_url(); // since WP 2.6
			} else {
				$adminurl = site_url() . '/wp-admin';
			}
			$permalinksettingsurl = trailingslashit($adminurl).'options-permalink.php';
			
			if(!$this->settings['enableStats']){
				$showStatsOptions = 'style="display: none;"';
			}

			if ( TRUE == defined('MULTISITE') AND TRUE === constant('MULTISITE') AND function_exists('get_blog_count') ) { // get_blog_count exists only if it is a multi site installation 
				$multisite_permalink_msg =  '<p class="podpress_notice">'.sprintf(__('<strong>Notice:</strong> This blog is part of a multi site blog installation. It might be possible that the method "Use WP Permalinks" works without changing the permalink setting of this particular blog to something other than the default setting. Use the stats method test and control whether it is possible to download and play the media files in your posts. If those tests are not successful then adjust the <a href="%1$s">Permalink Settings of this blog</a>.', 'podpress'), $permalinksettingsurl).'</p>';
				$podpress_trac_msg = '<p class="podpress_notice">'.__('<strong>Notice:</strong> This blog is part of a multi site blog installation. It might be possible that the method "Optional Files podpress_trac directory" works without copying the podpress_trac folder and its files. Use the stats method test and control whether it is possible to download and play the media files in your posts. If those tests are not successful then follow the explanation of this method (see below).', 'podpress').'</p>';
			} else {
				$multisite_permalink_msg = '';
				$podpress_trac_msg = '';
			}

			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('Download Statistics', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="enableStats">'.__('Enable Statistics', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_settings_narrow_col">'."\n";
			echo '					<input type="checkbox" name="enableStats" id="enableStats" '; if($this->settings['enableStats']) { echo 'checked="checked"'; } echo " onclick=\"javascript: podPressShowHideRow('statWarning'); podPressShowHideRow('statMethodWrapper'); podPressShowHideRow('statMethodHelp'); podPressShowHideRow('statsmethodtest'); podPressShowHideRow('statsmethodtesthelp'); podPressShowHideRow('statLoggingWrapper'); podPressShowHideRow('statLoggingHelp'); podPressShowHideRow('3rdpartyinfo'); podPressShowHideRow('podtracrow'); podPressShowHideRow('blubrryrow'); podPressShowHideRow('statBluBrryWrapper'); podPressShowHideRow('3rdpartystatsrow'); podPressShowHideRow('3rdpartystatsnoticerow'); podPressShowHideRow('disabledashboardwidgetrow'); \"/>\n";			
			echo '					'.$usingpi_str."\n";
			echo '					'.$podpress_trac_str."\n";
			echo '				</td>'."\n";
			echo '				<td>'.__('This will enable the podPress statistics features.', 'podpress').' '.$perm.'</td>'."\n";
			//echo '				<td>'.__('This will enable the podPress statistics features and give possibility to use the statistics from <a href="http://www.blubrry.com/podpress/" target="_blank">blubrry</a> or <a href="http://www.podtrac.com/" target="_blank">Podtrac</a> the included stats support in podPress.', 'podpress').' '.$perm.'</td>'."\n";
			echo '			</tr> '."\n";
			
			echo '			<tr id="statWarning" '.$showStatsOptions.'>'."\n";
			echo '				<th></th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<div id="permalinksWarning" '.$showPermalinksWarning.'>'."\n";
			echo '						<p class="podpress_error">'.sprintf(__('<strong>Warning:</strong> It appears you are not using WordPress permalinks or at least not a non-default permalink setting. If you want to use this statistic method, you need to choose a permalink structure which is different to the default setting. Go to the <a href="%1$s">Permalink Settings of your blog</a> and change that first. Otherwise enabling this statistics feature will most likely cause downloads of media files which were added with podPress to fail.', 'podpress'), $permalinksettingsurl).'</p>'."\n";
			echo '						'.$multisite_permalink_msg."\n";
			echo '					</div>'."\n";
			echo '					<div id="podpress_trac_dirWarning" '.$showpodpress_tracWarning.'>'."\n";
			echo '						<p class="podpress_error">'.__('<strong>Warning:</strong> The "Optional Files podpress_trac directory" statistic method will not work. Because the podpress_trac folder or the files are not in the right place:', 'podpress');
			echo '						<br />'.__('- podpress_trac folder exists at the right place:', 'podpress');
			if (TRUE === $trac_folder_exists) { echo ' '.__('true', 'podpress')."\n"; } else { echo ' <strong>'.__('false', 'podpress').'</strong>'."\n"; }
			echo '						<br />'.__('- the .htacces file in the podpress_trac folder exists:', 'podpress');
			if (TRUE === $trac_htaccess_exists) { echo ' '.__('true', 'podpress')."\n"; } else { echo ' <strong>'.__('false', 'podpress').'</strong>'."\n"; }
			echo '						<br />'.__('- the index.php file in the podpress_trac folder exists:', 'podpress');
			if (TRUE === $trac_index_exists) { echo ' '.__('true', 'podpress')."\n"; } else { echo ' <strong>'.__('false', 'podpress').'</strong>'."\n"; }
			echo '						</p>'."\n";
			echo '						'.$podpress_trac_msg."\n";
			echo '					</div>'."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr id="statMethodWrapper" '.$showStatsOptions.'>'."\n";
			echo '				<th><label for="statMethod">'.__('Stat Method', 'podpress').':</label></th>'."\n";
			echo '				<td colspan="2">';
			echo '					<select name="statMethod" id="statMethod" onchange="podpress_check_method_requirements(this.value);">'."\n";
			echo '						<option value="permalinks" '; if($this->settings['statMethod'] == 'permalinks') { echo 'selected="selected"'; } echo '>'.__('Use WP Permalinks (recommended)', 'podpress').'</option>'."\n";
			echo '						<option value="podpress_trac_dir" '; if($this->settings['statMethod'] == 'podpress_trac_dir') { echo 'selected="selected"'; } echo '>'.__('Optional Files podpress_trac directory', 'podpress').'</option>'."\n";
			echo '						<option value="download.mp3" '; if($this->settings['statMethod'] == 'download.mp3') { echo 'selected="selected"'; } echo '>'.__('Use download.mp3', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr id="statMethodHelp" '.$showStatsOptions.'>'."\n";
			echo '				<th></th>'."\n";
			echo '				<td colspan="2" class="podpress_settings_description_cell">';
			echo '				<ul>'."\n";
			echo '				<li>'.sprintf(__('"Use WP Permalinks" (recommended) - Requires a non-default permalink structure (go to <a href="%1$s">Settings > Permalinks</a>). Activating any non-default permalink setting in Wordpress will create an <a href="http://en.wikipedia.org/wiki/.htaccess" target="_blank" title="en.Wikipedia: .htaccess">.htaccess</a> file (or <a href="http://en.wikipedia.org/wiki/Web.config" target="_blank" title="en.Wikipedia: Web.config">web.config</a> file on some web-servers) in the base directory of your blog which podpress needs to support tracking statistics. If enabling permalinks in Wordpress breaks your download links or results in a "File not found" error when using the media player, then you should look into using one of the other methods for tracking statistics.', 'podpress'), $permalinksettingsurl).'</li>';
			echo '				<li>'.__('"Optional Files podpress_trac directory" - If you cannot use WP permalinks and you run <a href="http://en.wikipedia.org/wiki/Apache_HTTP_Server" target="_blank" title="en.Wikipedia: Apache HTTP Server">Apache</a>, this option may work (depending on the webserver configuration - see details below*). If you choose this option then you need to copy the folder podpress_trac/ including the two files (index.php and .htaccess) to the root folder of your blog. After copying these files the root folder should contain wp-config.php and four subdirectories; wp-admin, wp-content, wp-includes and podpress_trac. The podpress_trac folder contains an .htaccess file and an index.php file which enable podpress to resolve URLs of the media files which will be tracked by the statistics features in podPress. The copied folder and the files should be given the same filesystem permissions as the other folders and files in your WordPress install.<br />*If this method fails after copying the required files and setting the permissions it could be that your server is configured to ignore directory-level .htaccess files. Shared hosting users may need to contact their support to allow these files. If you are configuring your own Apache server the podpress_trac folder needs to have <code>AllowOverride FileInfo Options</code> or <code>AllowOverride All</code>. You can find the necessary configuration details here: <a href="http://httpd.apache.org/docs/2.0/mod/core.html#allowoverride" target="_blank">http://httpd.apache.org/docs/2.0/mod/core.html#allowoverride</a>.', 'podpress').'</li>';
			echo '				<li>'.__('"Use download.mp3" - This is an alternative to using an .htaccess file. This is provided for sites which run webservers that do not use the .htaccess file for configuration, such as Microsoft Internet Information Server (IIS).<br />To use this option, you will need to configure your web server to process .mp3 files the same way it does .php files. This is only necessary for the podPress directory, so that the download.mp3 file will be processed as a .php file.<br />If you do not know the type or version of the webserver you are using you can retrieve the information by using WP plugins like <a href="http://wordpress.org/extend/plugins/wp-system-health/" target="_blank">WP System Health</a> or <a href="http://wordpress.org/extend/plugins/system-information/" target="_blank">System information</a>.', 'podpress').'</li>';
			echo '				</ul>'."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr id="statsmethodtest" '.$showStatsOptions.'>'."\n";
			echo '				<th><label for="statsmethodtest">'.__('Test the stat method', 'podpress').':</label></th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<input type="button" name="statTest" value="'.__('start the test', 'podpress').'" onclick="podPressTestStats(\''.site_url().'/podpress_trac/web/0/0/podPressStatTest.txt\')"/>'."\n"; // WP 3.0 compatible
			echo '					<input type="text" name="statTestResult" id="statTestResult" size="30" value="" readonly="readonly" />'."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr id="statsmethodtesthelp" '.$showStatsOptions.'>'."\n";
			echo '				<th></th>'."\n";
			echo '				<td colspan="2" class="podpress_settings_description_cell">';
			echo '				'.__('This test can help you to determine whether your podPress statistics method setting will work under the current circumstances or not.', 'podpress')."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			
			echo '			<tr id="statLoggingWrapper" '.$showStatsOptions.'>'."\n";
			echo '				<th><label for="statLogging">'.__('Stat Logging', 'podpress').':</label></th>'."\n";
			echo '				<td colspan="2">';
			echo '					<select name="statLogging" id="statLogging">'."\n";
			echo '						<option value="Counts" '; if($this->settings['statLogging'] == 'Counts') { echo 'selected="selected"'; } echo '>'.__('Counts Only (recommended)', 'podpress').'</option>'."\n";
			echo '						<option value="Full" '; if($this->settings['statLogging'] == 'Full') { echo 'selected="selected"'; } echo '>'.__('Full', 'podpress').'</option>'."\n";
			echo '						<option value="FullPlus" '; if($this->settings['statLogging'] == 'FullPlus') { echo 'selected="selected"'; } echo '>'.__('Full+', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			unset($x);
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr id="statLoggingHelp" '.$showStatsOptions.'>'."\n";
			echo '				<th></th>'."\n";
			echo '				<td colspan="2" class="podpress_settings_description_cell">';
			echo '				<ul>'."\n";
			echo '				<li>'.__('"Counts Only" (recommended) - podPress counts only how many times a media was downloaded from the website, the feeds and how often the player of this file was started. Your media files should have unique file names. (The db table name is e.g. wp_podpress_statcounts.)', 'podpress').'</li>';
			echo '				<li>'.__('"Full" - With this option podPress will log how many times a media was downloaded from the website, the feeds and how often the player of this file was started. It will also log on each download the ID of the post (or page), the IP address, the referrer, the browser type (User Agent) and the time of the download. Furthermore podPress parses the referer and user agent information and store the information in separate columns in the database.<br />Full includes also the posssibility to mark downloads on the basis of user agent names and IP addresses as downloads of <a href="http://en.wikipedia.org/wiki/Internet_bot" target="_blank" title="en.Wikipedia: Internet bot">Internets bots</a> and filter the statistic tables and graphs. (The db table name is e.g. wp_podpress_stats.) If you add more than one media file to a post (with podPress) then these files should have different file names.', 'podpress').'</li>';
			echo '				<li>'.__('"Full+" (experimental) - If you would like to know all the information "Full" gives you and additionally whether a download has been completed or not. podPress can only try to find out whether a file transfer was complete, if the file is on the same server as your blog (if it is a local file for the script). If you add more than one media file to a post (with podPress) then these files should have different file names. In order to get the information whetehr a download was complete or not podPress (or at least a PHP script of podPress) needs to run during the whole download. But this may lead to problems if the file is relative big or the maximum execution time for PHP scripts is relative short on the server of your blog. If the time limit is reached the download stops. So if you are not allowed to change the max_execution_time setting of the PHP configuration on the server of your blog or if you are unsure what this all means then please use the "Full" method (as recommended).', 'podpress').'</li>';
			echo '				</ul>'."\n";
			echo '				'.__('Note that if you enable the statistics, the Counts Only counter counts always even if you choose Full or Full+ but not vice versa.', 'podpress')."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			
			if ( TRUE == version_compare($wp_version, '2.5', '>=') AND TRUE == version_compare($wp_version, '2.7', '<') ) {
				$this->settings['disabledashboardwidget'] = TRUE;
				$descwidget_disabled = ' disabled = "disabled"';
				$descwidget_notice = __('podPress offers no Dashboard Widget for WP 2.5.x and WP 2.6.x.', 'podpress');
			} else {
				$descwidget_disabled = '';
				$descwidget_notice = '';
			}
			echo '			<tr id="disabledashboardwidgetrow" '.$showStatsOptions.'>'."\n";
			echo '				<th><label for="disabledashboardwidget">'.__('Disable the dashboard widget', 'podpress').':</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="checkbox" name="disabledashboardwidget" id="disabledashboardwidget" value="yes"'; if ( (isset($this->settings['disabledashboardwidget']) AND TRUE === $this->settings['disabledashboardwidget']) ) { echo 'checked="checked"'; } echo $descwidget_disabled.' />'."\n";
			echo '				</td>'."\n";
			echo '				<td>'.$descwidget_notice.'</td>'."\n";
			echo '			</tr> '."\n";

			// ntm: reactivate this feature with the constant PODPRESS_ACTIVATE_3RD_PARTY_STATS in the podpress.php files
			if ( TRUE == defined('PODPRESS_ACTIVATE_3RD_PARTY_STATS') AND TRUE === constant('PODPRESS_ACTIVATE_3RD_PARTY_STATS') ) {
				Switch ( $this->settings['enable3rdPartyStats'] ) { 
					case 'PodTrac' :
						$podtrac_checked = ' checked="checked"'; 
						$blubrry_checked = ''; 
						$disable3rdparty_checked = ''; 
					break;
					case 'Blubrry' :
						$podtrac_checked = ''; 
						$blubrry_checked = ' checked="checked"'; 
						$disable3rdparty_checked = ''; 
					break;
					default :
						$podtrac_checked = ''; 
						$blubrry_checked = ''; 
						$disable3rdparty_checked = ' checked="checked"';
					break;
				}
				$podtrac_disabled = '';
				$blubrry_disabled = '';
				$blubrry_readonly = '';
			} else {
				$podtrac_disabled = ' disabled = "disabled"';
				$blubrry_disabled = ' disabled = "disabled"';
				$blubrry_readonly = 'readonly = "readonly"';
				$podtrac_checked = '';
				$blubrry_checked = '';
				$disable3rdparty_checked = ' checked="checked"';
			}
			
			// ntm: reactivate this feature with the constant PODPRESS_ACTIVATE_3RD_PARTY_STATS in the podpress.php files
			if ( TRUE == defined('PODPRESS_ACTIVATE_3RD_PARTY_STATS') AND TRUE === constant('PODPRESS_ACTIVATE_3RD_PARTY_STATS') ) {
			echo '			<tr id="3rdpartyinfo" class="podpress_settings_headerrow" '.$showStatsOptions.'>'."\n";
			echo '				<th colspan="3"'.$thirdpartystats_class.'>'.__('In addition to the podPress own counter mechanisms, you can use one from a company:', 'podpress').'</th>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr id="podtracrow" '.$showStatsOptions.'>'."\n";
			echo '				<th><label for="enablePodTracStats">'.__('Enable Podtrac Statistics', 'podpress').':</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="radio" name="enable3rdPartyStats" id="enablePodTracStats" value="PodTrac"' . $podtrac_checked . $podtrac_disabled . ' />'."\n";
			echo '				</td>'."\n";
			echo '				<td>'.__('This will use the Podtrac service. <a href="http://www.podtrac.com/" target="_new">More info ...</a>', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr id="blubrryrow" '.$showStatsOptions.'>'."\n";
			echo '				<th><label for="enableBlubrryStats">'.__('Enable blubrry Statistics', 'podpress').':</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="radio" name="enable3rdPartyStats" id="enableBlubrryStats" value="Blubrry"' . $blubrry_checked . $blubrry_disabled .  ' />'."\n";
			echo '				</td>'."\n";
			echo '				<td>'.__('This will use the blubrry service. (You need use a non-default Permalink scheme.) <a href="http://www.blubrry.com/podpress/" target="_new">More info ...</a>', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr id="statBluBrryWrapper" '.$showStatsOptions.'>'."\n";
			echo '				<th></th>'."\n";
			echo '				<td></td>'."\n";
			echo '				<td>';
			echo '					<label for="statBluBrryProgramKeyword">'.__('Program Keyword', 'podpress').'</label>:';
			echo '					<input type="input" name="statBluBrryProgramKeyword" id="statBluBrryProgramKeyword" ' . $blubrry_readonly . ' value="'.$this->settings['statBluBrryProgramKeyword'].'"/>';
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			}
			
			echo '			<tr id="3rdpartystatsrow" '.$showStatsOptions.'>'."\n";
			echo '				<th><label for="disable3rdPartyStats">'.__('Disable 3rd Party Statistics', 'podpress').':</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="radio" name="enable3rdPartyStats" id="disable3rdPartyStats" value="No"' . $disable3rdparty_checked . ' />'."\n";
			echo '				</td>'."\n";
			echo '				<td></td>'."\n";
			echo '			</tr> '."\n";
			
			echo '			<tr id="3rdpartystatsnoticerow" '.$showStatsOptions.'>'."\n";
			echo '				<th></th>'."\n";
			echo '				<td colspan="2">'."\n";
			// ntm: reactivate this feature with the constant PODPRESS_ACTIVATE_3RD_PARTY_STATS in the podpress.php files
			if ( TRUE == defined('PODPRESS_ACTIVATE_3RD_PARTY_STATS') AND TRUE === constant('PODPRESS_ACTIVATE_3RD_PARTY_STATS') ) {
				echo '					'.__('You can use only one of these services together with the podPress statistics at the same time. If you want to have more or different statistics then you could use the service of e.g. Feedburner or eventually Libsyn.', 'podpress')."\n";
			} else {
				echo '					<span class="nonessential">'.__('This feature is deactivated and will maybe be removed in one of a future versions. If you want to activate this feature then ask for help in <a href="http://wordpress.org/tags/podpress?forum_id=10" target="_blank">this WP.org Forum</a>.', 'podpress').'</span>'."\n";
			}
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";

			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('Post Editing', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="maxMediaFiles">'.__('max. number of media files', 'podpress').':</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="text" maxlength="3" size="3" name="maxMediaFiles" id="maxMediaFiles" value="'.$this->settings['maxMediaFiles'].'"'." />\n";
			echo '				</td>'."\n";
			echo '				<td>'.__('which you want to add to single posts (or pages). The higher the number, the bigger the performance impact when loading the Posts editor. (default: 10)', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			if ( TRUE == version_compare($wp_version, '2.9', '>=') ) {
				$selected_types = $this->settings['metaboxforcustomposttypes'];
				if ( FALSE === is_array($selected_types) ) {
					$selected_types = array();
				}
				$args = array(
					'public'   => true,
					'_builtin' => false
				);
				$output = 'objects'; // names or objects
				$post_types = get_post_types($args, $output);
				echo '			<tr>'."\n";
				echo '				<th>'."\n";
				echo ' 					'.__('podPress meta box for custom post types', 'podpress');
				echo '				</th>'."\n";			
				echo '				<td>'."\n";
				if (TRUE == is_array($post_types) AND FALSE == empty($post_types) ) {
					echo '				<select name="metaboxforcustomposttypes[]" size="4" multiple="multiple" class="podpress_customposttype_select">'."\n";
					echo '					<optgroup label="'.esc_attr(__('public custom post types:', 'podpress')).'">'."\n";
					foreach ($post_types as $post_type ) {
						if ( TRUE == in_array($post_type->name, $selected_types) ) {
							$selected = ' selected="selected"';
						} else {
							$selected = '';
						}
						echo '				<option value="'.$post_type->name.'"'.$selected.'>'.$post_type->label. ' (' .$post_type->name.')</option>';
					}
					echo '					</optgroup>'."\n";
					echo '				</select>'."\n";
				} else {
					echo '				<em class="nonessential">('.__('Currently are no custom post types defined.', 'podpress').')</em>'."\n";
				}
				echo '				</td>'."\n";
				echo '				<td class="podpress_settings_description_cell">'."\n";
				echo ' 					'.__('Show a podPress meta box below or at the side of the editor of post of a custom post type. This makes it possbile to add podcasts with podPress to posts of a custom post type.', 'podpress');		
				echo '					<br /><span class="podpress_description">'.__('Hold the key [SHIFT] or [CTRL] and use the left mouse button to select more than one value.<br />Hold [CTRL] and use the left mouse button to deselect values.', 'podpress').'</span>';
				echo '				</td>'."\n";
				echo '			</tr> '."\n";
			}
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";

			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('Blog Search', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="activate_podpressmedia_search">'.__('expand the search to podPress media files', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_settings_narrow_col">'."\n";
			if ( TRUE == isset($this->settings['activate_podpressmedia_search']) AND TRUE === $this->settings['activate_podpressmedia_search'] ) {
			echo '					<input type="checkbox" name="activate_podpressmedia_search" id="activate_podpressmedia_search" value="true" checked="checked" />'."\n";
			} else {
			echo '					<input type="checkbox" name="activate_podpressmedia_search" id="activate_podpressmedia_search" value="true" />'."\n";
			}
			echo '				</td>'."\n";
			echo '				<td>'.__('Expand the blog search (which searches by default only through titles and content of posts, pages and attachments) to the titles and URLs (location) of the media files which have been attached with podPress.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";

			// ntm: reactivate this feature with the constant PODPRESS_ACTIVATE_PODANGO_INTEGRATION in the podpress.php files
			if ( TRUE == defined('PODPRESS_ACTIVATE_PODANGO_INTEGRATION') AND TRUE === constant( 'PODPRESS_ACTIVATE_PODANGO_INTEGRATION') ) {
			echo '	<fieldset class="options">'."\n";
			} else {
			echo '	<fieldset class="options nonessential">'."\n";
			}
			echo '		<legend>'.__('Podango Integration', 'podpress').'</legend>'."\n";
			echo '          	<p class="podpress_error">'.__('Podango Integration does not work anymore and causes probably long page loading times e.g. on the post/page editor pages of this blog. Since 2008/2009 Podango <a href="http://sites.google.com/site/podangohibernate/">is currently on vacation</a> and the API which podPress tries to use is unavailable. That is why it is most likely that you will experience a lot of warning and error messages if you activate this feature.', 'podpress').'</p>';
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="enablePodangoIntegration">'.__('Enable Podango Integration', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_settings_narrow_col">'."\n";
			// ntm: reactivate this feature with the constant PODPRESS_ACTIVATE_PODANGO_INTEGRATION in the podpress.php files
			if ( TRUE == defined('PODPRESS_ACTIVATE_PODANGO_INTEGRATION') AND TRUE === constant( 'PODPRESS_ACTIVATE_PODANGO_INTEGRATION') ) {
				echo '					<input type="checkbox" name="enablePodangoIntegration" id="enablePodangoIntegration" '; if($this->settings['enablePodangoIntegration']) { echo 'checked="checked"'; } echo " />\n";
			} else {
				echo '					<input type="checkbox" name="enablePodangoIntegration" id="enablePodangoIntegration" disabled="disabled" />'."\n";
			}
			echo '				</td>'."\n";
			echo '				<td>';
			if ( TRUE == defined('PODPRESS_ACTIVATE_PODANGO_INTEGRATION') AND TRUE === constant( 'PODPRESS_ACTIVATE_PODANGO_INTEGRATION') ) {
				echo '					'.__('podPress users can gain additional functionality when used in combination with Podango hosting.', 'podpress').'<br />'."\n";
			} else {
				echo '					<span class="nonessential">'.__('This feature is deactivated and will maybe be removed in one of a future versions. If you want to activate this feature despite the absence of the Podango platform then ask for help in <a href="http://wordpress.org/tags/podpress?forum_id=10" target="_blank">this WP.org Forum</a>.', 'podpress').'</span><br />'."\n";
			}
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";

			if ( FALSE == defined('PODPRESS_DEACTIVATE_PREMIUM') OR FALSE === constant('PODPRESS_DEACTIVATE_PREMIUM') ) {
				echo '	<fieldset class="options">'."\n";
				echo '		<legend>'.__('Premium Content', 'podpress').'</legend>'."\n";
				if (!$this->settings['enablePremiumContent']) {
						$showPremiumOptions = 'style="display: none;"';
				}
				$premiumcontenthelp = '		<tr id="premiumPodcastingHelp" '.$showPremiumOptions.'>'."\n";
				$premiumcontenthelp .= '			<th>&nbsp;</th>'."\n";
				$premiumcontenthelp .= '			<td colspan="2">';
				$premiumcontenthelp .= '				'.sprintf(__('<p>If you want use this part of the plugin then you should <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">read more about roles and capabilities</a> and you need to get and install a roles and capabilities management plugin like <a href="http://wordpress.org/extend/plugins/members/" target="_blank">Members</a> or <a href="http://wordpress.org/extend/plugins/capsman/" target="_blank">Capability Manager</a> (The former recommendation was the <a href="http://redalt.com/wiki/Role+Manager" target="_blank">Role Manager</a> plugin. But it is unclear whether it works with current WP version or not.)</p><p>Anyone that should have access to the premium podcasting files need to have the Premium Content role, which can be done by making them Premium Subscribers.<br />Then just in each post set the media file as premium content and normal visitors will not be able to see the content via the web or from the feed.</p><p>If you are using a WordPress version which is newer than v2.1, then users can just use <a href="%1$s">%1$s</a> for their premium content needs. User will be asked for their user/pass before giving the RSS feed. For instance Juice and iTunes support this fine.</p><p>Keep in mind, that this does NOT protect your content if someone discovers the URLs, it only hides the location from showing up on the site or in the feed. To fully protect your files you can use this feature in combination with an external service like the one from <a href="http://www.amember.com/" target="_blank">aMemberPro</a> which should work with podPress. aMemberPro will protect the files from being downloaded at all, unless authorized. It also handles monthly subscription issues through <a href="http://en.wikipedia.org/wiki/Paypal" target="_blank" title="en.Wikipedia: PayPal">PayPal</a> and such. If you combine such a service with WordPress and podPress you can have your own premium content podcasting service.</p>', 'podpress'), get_feed_link('premium'))."\n";
				// ntm: Parts of this text are obviously aut dated and I took the chance to modify it 
				//$premiumcontenthelp .= '				'.__('Full documentation is still under development on <a href="http://podcasterswiki.com/index.php?title=PodPress_Documentation#Premium_Podcasting">the wiki</a><br /><br />This is the short of it is that you need to get and install the <a href="http://redalt.com/wiki/Role+Manager">Role Manager plugin</a><br />Anyone that should have access to the premium podcasting files need to have the Premium Content role, which can be done by making them Premium Subscribers<br /><br />Then just in each post set the media file as premium content and normal visitors will not be able to see the content via the web or from the feed.<br />If your using Wordpress 2.1, then users can just use http://www.yoursite.com/?feed=premium for their premium content needs.<br />If your using WP 1.5 or 2.0.x, then you need to put premiumcast.php in your main wordpress dir and then have your subscribers use this file as their rss feed.<br />These will cause the site to ask for their user/pass before giving the RSS feed. Juice and iTunes supports this fine.<br /><br />Keep in mine, that this does NOT protect your content if someone discovers the URLS, it only hides the location from showing up on the site or in the feed. To fully protect your files I have also been able to get this working with <a href="http://www.amember.com/">aMemberPro</a><br />aMemberPro will protect the files from being downloaded at all, unless authorized. It also handles monthly subscription issues thru paypal and such. Its a great tool, and combines with WordPress and podPress you can have a full blown premium content podcasting service.', 'podpress')."\n";
				$premiumcontenthelp .= '			</td>'."\n";
				$premiumcontenthelp .= '		</tr> '."\n";
				echo '		<table class="editform podpress_settings_table">'."\n";
				echo '			<tr>'."\n";
				echo '				<th><label for="enablePremiumContent">'.__('Enable Premium Content:', 'podpress').'</label></th>'."\n";
				if(!podPress_WPVersionCheck('2.0.0')) {
					echo '			<td>&nbsp;</td>'."\n";
					echo '			<td>'.__('Only available in WordPress 2.0.0 or greater', 'podpress').'</td>'."\n";
					echo '		</tr> '."\n";
					
					echo $premiumcontenthelp;
					
				} else {
					echo '			<td colspan="2">'."\n";
					echo '				<input type="checkbox" name="enablePremiumContent" id="enablePremiumContent" '; if($this->settings['enablePremiumContent']) { echo 'checked="checked"'; } echo " onclick=\"javascript: podPressShowHideRow('premiumPodcastingHelp'); podPressShowHideRow('premiumMethodWrapper'); podPressShowHideRow('premiumContentFakeEnclosureWrapper');\" />\n";
					
					// ntm: the following section is for a future version. It is a check for the modules which are required for the authorization process of the premium festure
					//~ $loaded_extensions = get_loaded_extensions();
					//~ $mod_auth_basic_available = FALSE;
					//~ $mod_auth_digest_available = FALSE;
					//~ $apache_modules = Array();
					//~ if ( TRUE === is_array($loaded_extensions) ) {
						//~ foreach ($loaded_extensions as $extension_name) {
							//~ if ( FALSE !== stripos($extension_name, 'apache') ) {
								//~ $apache_modules = apache_get_modules();
								//~ if ( FALSE === is_array($apache_modules) ) {
									//~ break;
								//~ }
								//~ if ( TRUE === in_array('mo_auth_basic', $apache_modules) ) {
									//~ echo '				<p class="podpress_notice">'.__('mod_auth_basic is available', 'podpress').'</p>';
									//~ $mod_auth_basic_available = TRUE;
								//~ } else {
									//~ echo '				<p class="podpress_error">'.__('mod_auth_basic is not available', 'podpress').'</p>';
								//~ }
								//~ if ( TRUE === in_array('mod_auth_digest', $apache_modules) ) {
									//~ echo '				<p class="podpress_notice">'.__('mod_auth_digest is available', 'podpress').'</p>';
									//~ $mod_auth_digest_available = TRUE;
								//~ } else {
									//~ echo '				<p class="podpress_error">'.__('mod_auth_digest is not available', 'podpress').'</p>';
								//~ }
								//~ break;
							//~ }
						//~ }
					//~ }
					//~ if ( FALSE === $mod_auth_basic_available OR FALSE === $mod_auth_digest_available ) {
							//~ echo "				<p>all currently available Apache modules:<br />\n";
							//~ echo implode(', ', $apache_modules);
							//~ echo "				</p>\n";
					//~ }
					
					//ntm: with the podPressShowHideRow('protectedMediaFilePathWrapper'); podPressShowHideRow('protectedMediaFilePathHelp');
					//~ echo '				<input type="checkbox" name="enablePremiumContent" id="enablePremiumContent" '; if($this->settings['enablePremiumContent']) { echo 'checked="checked"'; } echo " onclick=\"javascript: podPressShowHideRow('premiumPodcastingHelp'); podPressShowHideRow('protectedMediaFilePathWrapper'); podPressShowHideRow('protectedMediaFilePathHelp'); podPressShowHideRow('premiumMethodWrapper'); podPressShowHideRow('premiumContentFakeEnclosureWrapper');\" />\n";
					echo '			</td>'."\n";
					echo '		</tr> '."\n";
					
					echo $premiumcontenthelp;
					
					// ntm: there is nothing behind this input that is why is deactivated
					//~ echo '		<tr id="protectedMediaFilePathWrapper" '.$showPremiumOptions.'>'."\n";
					//~ echo '			<th><label for="protectedMediaFilePath">'.__('Absolute path to protected media', 'podpress').':</label></th>'."\n";
					//~ echo '			<td colspan="2">'."\n";
					//~ echo '				<input type="text" id="protectedMediaFilePath" name="protectedMediaFilePath" class="podpress_wide_text_field" size="40" value="'.attribute_escape($this->settings['protectedMediaFilePath']).'" />'."\n";
					//~ echo '			</td>'."\n";
					//~ echo '		</tr>'."\n";
					//~ echo '		<tr id="protectedMediaFilePathHelp" '.$showPremiumOptions.'>'."\n";
					//~ echo '			<th>&nbsp;</th>'."\n";
					//~ echo '			<td colspan="2">';
					//~ echo '				'.sprintf(__('Insert here the complete path name of the folder which contains the premium meda files. This folder needs to be on the same server as your blog. But it should NOT be in a dir under your web root. It should be a dir outside of the web root so that users cannot simply browse to the dir and get access to the files. For example this could be <code>%1$s/premium_mp3s/</code> or maybe with random number as folder name: <code>%1$s/%2$s/premium_mp3s/</code>. Create this folder before you start to use this feature.', 'podpress'), $this->uploadpath, rand(10000, 99999))."\n";
					//~ echo '			</td>'."\n";
					//~ echo '		</tr> '."\n";

					echo '		<tr id="premiumMethodWrapper" '.$showPremiumOptions.'>'."\n";
					echo '			<th><label for="premiumMethod">'.__('Method', 'podpress').':</label></th>'."\n";
					echo '			<td>'."\n";
					echo '				<select name="premiumMethod" id="premiumMethod">'."\n";
					echo '					<option value="Digest" '; if($this->settings['premiumMethod'] != 'Basic') { echo 'selected="selected"'; } echo '>'.__('Digest', 'podpress').'</option>'."\n";
					echo '					<option value="Basic" '; if($this->settings['premiumMethod'] == 'Basic') { echo 'selected="selected"'; }  echo '>'.__('Basic', 'podpress').'</option>'."\n";
					echo '				</select>'."\n";
					echo '			</td>'."\n";
					echo '			<td>'.__('Digest auth is MUCH better than Basic, which is easily unencrypted.', 'podpress').'</td>'."\n";
					echo '		</tr> '."\n";
					
					echo '		<tr id="premiumContentFakeEnclosureWrapper" '.$showPremiumOptions.'>'."\n";
					echo '			<th><label for="premiumContentFakeEnclosure">'.__('Use fake enclosure', 'podpress').':</label></th>'."\n";
					echo '			<td>'."\n";
					echo '				<input type="checkbox" name="premiumContentFakeEnclosure" id="premiumContentFakeEnclosure" '; if($this->settings['premiumContentFakeEnclosure']) { echo 'checked="checked"'; } echo "/>\n";
					echo '			</td>'."\n";
					echo '			<td>'.__('If you want the <a href="http://en.wikipedia.org/wiki/RSS_enclosure" target="_blank" title="en.Wikipedia: RSS enclosures">enclosures</a> (elements of the news feeds which contain usually links to the media files) to always exist (so the feed will show up in iTunes) then check this. A fake enclosure contains a place holder URL and not the real one.', 'podpress').'</td>'."\n";
					echo '		</tr> '."\n";
				}
				echo '		</table>'."\n";
				echo '	</fieldset>'."\n";
			}
			
			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('Post Content', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="contentLocation">'.__('Location:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="contentLocation" id="contentLocation">'."\n";
			echo '						<option value="start" '; if($this->settings['contentLocation'] == 'start') { echo 'selected="selected"'; } echo '>'.__('Start', 'podpress').'</option>'."\n";
			echo '						<option value="end" '; if($this->settings['contentLocation'] != 'start') { echo 'selected="selected"'; }  echo '>'.__('End', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('Part of the Post where the podPress content (Player, and links) will go. Default is at the end.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="contentPlayer">'.__('Player:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="contentPlayer" id="contentPlayer">'."\n";
			echo '						<option value="both" '; if($this->settings['contentPlayer'] == 'both') { echo 'selected="selected"'; }  echo '>'.__('Enabled', 'podpress').'</option>'."\n";
			echo '						<option value="inline" '; if($this->settings['contentPlayer'] == 'inline') { echo 'selected="selected"'; }  echo '>'.__('Inline Only', 'podpress').'</option>'."\n";
			echo '						<option value="popup" '; if($this->settings['contentPlayer'] == 'popup') { echo 'selected="selected"'; } echo '>'.__('Popup Only', 'podpress').'</option>'."\n";
			echo '						<option value="disabled" '; if($this->settings['contentPlayer'] == 'disabled') { echo 'selected="selected"'; } echo '>'.__('Disabled', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('Allow users to make use of the web players for your content / Popup Only - Only the Popup Player is available and it is not possible to use the player in the posts. / Inline Only - The Popup player is not available and it is only possible to use the player in the posts.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="contentImage">'.__('Image:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="contentImage" id="contentImage">'."\n";
			echo '						<option value="button" '; if($this->settings['contentImage'] == 'button') { echo 'selected="selected"'; }  echo '>'.__('Button', 'podpress').'</option>'."\n";
			echo '						<option value="icon" '; if($this->settings['contentImage'] == 'icon') { echo 'selected="selected"'; }  echo '>'.__('Icon', 'podpress').'</option>'."\n";
			echo '						<option value="none" '; if($this->settings['contentImage'] == 'none') { echo 'selected="selected"'; } echo '>'.__('None', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('The image that shows up before links for the media file.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="contentHidePlayerPlayNow">'.__('Hide Player/Play Now:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="contentHidePlayerPlayNow" id="contentHidePlayerPlayNow">'."\n";
			echo '						<option value="enabled" '; if($this->settings['contentHidePlayerPlayNow'] == 'enabled') { echo 'selected="selected"'; }  echo '>'.__('Show', 'podpress').'</option>'."\n";
			echo '						<option value="disabled" '; if($this->settings['contentHidePlayerPlayNow'] == 'disabled') { echo 'selected="selected"'; } echo '>'.__('Hide', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('Show the Hide Player/Play Now link', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr>'."\n";
			echo '				<th><label for="contentDownload">'.__('Download:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="contentDownload" id="contentDownload">'."\n";
			echo '						<option value="enabled" '; if($this->settings['contentDownload'] == 'enabled') { echo 'selected="selected"'; }  echo '>'.__('Enabled', 'podpress').'</option>'."\n";
			echo '						<option value="disabled" '; if($this->settings['contentDownload'] == 'disabled') { echo 'selected="selected"'; } echo '>'.__('Disabled', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('Allow users to download the media files directly from the website.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr>'."\n";
			echo '				<th><label for="contentDownloadText">'.__('Show Download Text:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="contentDownloadText" id="contentDownloadText">'."\n";
			echo '						<option value="enabled" '; if($this->settings['contentDownloadText'] == 'enabled') { echo 'selected="selected"'; }  echo '>'.__('Enabled', 'podpress').'</option>'."\n";
			echo '						<option value="disabled" '; if($this->settings['contentDownloadText'] == 'disabled') { echo 'selected="selected"'; } echo '>'.__('Disabled', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('If disabled, users can still download using the icon link.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr>'."\n";
			echo '				<th><label for="contentDownloadStats">'.__('Show Download Stats:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="contentDownloadStats" id="contentDownloadStats">'."\n";
			echo '						<option value="enabled" '; if($this->settings['contentDownloadStats'] == 'enabled') { echo 'selected="selected"'; }  echo '>'.__('Enabled', 'podpress').'</option>'."\n";
			echo '						<option value="disabled" '; if($this->settings['contentDownloadStats'] == 'disabled') { echo 'selected="selected"'; } echo '>'.__('Disabled', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('Display download stats for each media file for everyone to see at the end of the podPress line. This will cause a performance hit of 2 extra SQL queries per post being displayed. Disable this feature if your site is slowing down when podpress is enabled.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr>'."\n";
			echo '				<th><label for="contentDuration">'.__('Show Duration:', 'podpress').'</label></th>'."\n";
			echo '				<td colspan="2" class="podpress_settings_description_cell">'."\n";
			echo '					<select name="contentDuration" id="contentDuration">'."\n";
			if ( $this->settings['contentDuration'] == 'enabled' ) {
				$this->settings['contentDuration'] = 'h:m:s';
			}
			$duration_schemes = Array('h:m:s' => __('1:23:45 (h:m:s)', 'podpress'), 'm:s' => __('83:45 (m:s)', 'podpress'), 'h:m:s:ms' => __('1:23:45:30 (h:m:s:ms)', 'podpress'), 'm:s:ms' => __('83:45:30 (m:s:ms)', 'podpress'), 's:ms' => __('5025:30 (s:ms)', 'podpress'), 'h' => __('1.40 (h)', 'podpress'), 'm' => __('83.75 (m)', 'podpress'), 's' => __('5025.03 (s)', 'podpress'), 'ms' => __('5025030 (ms)', 'podpress'));
			foreach ($duration_schemes as $duration_scheme => $scheme_name) {
				if ( $this->settings['contentDuration'] == $duration_scheme ) {
					echo '						<option value="'.$duration_scheme.'" selected="selected">'.$scheme_name.'</option>'."\n";
				} else {
					echo '						<option value="'.$duration_scheme.'">'.$scheme_name.'</option>'."\n";
				}
			}
			echo '						<option value="disabled" '; if($this->settings['contentDuration'] == 'disabled') { echo 'selected="selected"'; } echo '>'.__('Disabled', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				'.__('Display the duration for each media file. (The milliseconds value will only be visible if it is bigger than zero.)', 'podpress');
			echo '					<br /><label for="contentDurationdivider">'.__('Choose a duration format:', 'podpress').'</label> <select name="contentDurationdivider" id="contentDurationdivider">'."\n";
			if (version_compare($wp_version, '2.8', '<')) {
				$duration_dividers = Array(
				'colon' => '1:23:45:30', 
				'hminsms' => '1 '.__('h', 'podpress').' 23 '.__('min', 'podpress').' 45 '.__('s', 'podpress').' 30 '.__('ms', 'podpress'), 
				'hrminsecmsec' => '1 '.__('hr.', 'podpress').' 23 '.__('min.', 'podpress').' 45 '.__('sec.', 'podpress').' 30 '.__('msec.', 'podpress'), 
				'hoursminutessecondsmilliseconds' => '1 '.__ngettext('hour', 'hours', 1, 'podpress').' 23 '. __ngettext('minute', 'minutes', 23, 'podpress').' 45 '. __ngettext('second', 'seconds', 45, 'podpress').' 30 '. __ngettext('millisecond', 'milliseconds', 30, 'podpress') 
				);
			} else {
				$duration_dividers = Array(
				'colon' => '1:23:45:30',
				'hminsms' => '1 '.__('h', 'podpress').' 23 '.__('min', 'podpress').' 45 '.__('s', 'podpress').' 30 '.__('ms', 'podpress'),
				'hrminsecmsec' => '1 '.__('hr.', 'podpress').' 23 '.__('min.', 'podpress').' 45 '.__('sec.', 'podpress').' 30 '.__('msec.', 'podpress'),
				'hoursminutessecondsmilliseconds' => '1 '._n('hour', 'hours', 1, 'podpress').' 23 '. _n('minute', 'minutes', 23, 'podpress').' 45 '. _n('second', 'seconds', 45, 'podpress').' 30 '. _n('millisecond', 'milliseconds', 30, 'podpress')
				);
			}
//			$duration_dividers = Array('colon' => '1:23:45:30', 'hminsms' => __('1 h 23 min 45 s 30 ms', 'podpress'), 'hrminsecmsec' => __('1 hr 23 min 45 sec 30 msec', 'podpress'), 'hoursminutessecondsmilliseconds' => __('1 hour 23 minutes 45 seconds 30 milliseconds', 'podpress'));
			if ( FALSE == isset($this->settings['contentDurationdivider']) ) {
				$this->settings['contentDurationdivider'] = 'colon';
			}
			foreach ($duration_dividers as $duration_divider => $divider_name) {
				if ( $this->settings['contentDurationdivider'] == $duration_divider ) {
					echo '						<option value="'.$duration_divider.'" selected="selected">'.$divider_name.'</option>'."\n";
				} else {
					echo '						<option value="'.$duration_divider.'">'.$divider_name.'</option>'."\n";
				}
			}
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			
			echo '			<tr>'."\n";
			echo '				<th><label for="contentfilesize">'.__('Show File Size:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="contentfilesize" id="contentfilesize">'."\n";
			echo '						<option value="enabled" '; if($this->settings['contentfilesize'] == 'enabled') { echo 'selected="selected"'; }  echo '>'.__('Enabled', 'podpress').'</option>'."\n";
			echo '						<option value="disabled" '; if(FALSE == isset($this->settings['contentfilesize']) OR $this->settings['contentfilesize'] == 'disabled') { echo 'selected="selected"'; } echo '>'.__('Disabled', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('Display the file size (in MB) for each media file.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";

			echo '			<tr>'."\n";
			echo '				<th><label for="contentBeforeMore">'.__('Always before the &lt;!- More -&gt; tag:', 'podpress').'</label></th>'."\n";
			echo '				<td class="podpress_settings_description_cell">'."\n";
			echo '					<select name="contentBeforeMore" id="contentBeforeMore">'."\n";
			echo '						<option value="yes" '; if($this->settings['contentBeforeMore'] == 'yes') { echo 'selected="selected"'; } echo '>'.__('Yes', 'podpress').'</option>'."\n";
			echo '						<option value="no" '; if($this->settings['contentBeforeMore'] != 'yes') { echo 'selected="selected"'; }  echo '>'.__('No', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('This defines that the player and the download links will always be visible on the short version of a post.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			
			$incontentandexcerpt_vals = Array('in_content_and_excerpt' => __('in content sections and excerpts', 'podpress'), 'in_content_only' => __('in content sections only', 'podpress'), 'in_excerpt_only' => __('in excerpts only', 'podpress'));
			echo '			<tr>'."\n";
			echo '				<th><label for="incontentandexcerpt">'.__('Show the podPress elements in the content sections and the excerpts?', 'podpress').'</label></th>'."\n";
			echo '				<td colspan="2" class="podpress_settings_description_cell">'."\n";
			echo '					<select name="incontentandexcerpt" id="incontentandexcerpt">'."\n";
			foreach ($incontentandexcerpt_vals as $value => $optiontext) {
				if ( $this->settings['incontentandexcerpt'] == $value ) {
					echo '						<option value="'.$value.'" selected="selected">'.$optiontext.'</option>'."\n";
				} else {
					echo '						<option value="'.$value.'">'.$optiontext.'</option>'."\n";
				}
			}
			echo '					</select>'."\n";
			echo '				'.sprintf(__('Determine whether the podPress elements (player or player preview, download link, icon, etc.) should be visible in the content sections and the excerpts of posts and pages. (default: %1$s)', 'podpress'), __('in content sections and excerpts', 'podpress')).'</td>'."\n";
			echo '			</tr> '."\n";
			
			if ( TRUE == isset($this->settings['do_not_use_the_target_attribute']) AND TRUE === $this->settings['do_not_use_the_target_attribute'] ) {
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}
			echo '			<tr>'."\n";
			echo '				<th><label for="do_not_use_the_target_attribute">'.__('do not use the <code>target="new"</code> attribute in links of podPress', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="checkbox" name="do_not_use_the_target_attribute" id="do_not_use_the_target_attribute" value="yes"'.$checked.' />'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('If your theme uses a <a href="http://en.wikipedia.org/wiki/Document_Type_Declaration" target="_blank" title="en.Wikipedia: Document Type Declaration">DOCTYPE</a> which does not allow "target" attributes in hyper links (e.g. XHTML 1.0 Strict) then you can use this option to prompt podPress to create valid code for your theme. (default: not checked)', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";

			/*
			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('TorrentCasting', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="enableTorrentCasting">'.__('Enable TorrentCasting', 'podpress').':</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="checkbox" name="enableTorrentCasting" id="enableTorrentCasting" '; if($this->settings['enableTorrentCasting']) { echo 'checked="checked"'; } echo "/>\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr>'."\n";
			echo '				<td colspan="2">This just allows for you to define a location to the .torrent file for you content. If you enable this you should copy the torrentcast.php file from plugins/podpress/optional_files and into your main wordpress directory.</td>'."\n";
			echo '			</tr> '."\n";
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";
			*/
			
			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('System Information', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo ' 					'.__('Feed Caching', 'podpress');		
			echo '				</th>'."\n";			
			echo '				<td>'."\n";
			echo ' 					'.__('Feedcache files will be stored in the follow directory:', 'podpress').'<br /><code>'.$this->tempfilesystempath.'</code> '.$this->checkWritableTempFileDir(TRUE);		
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr>'."\n";
			echo '				<th></th>'."\n";
			echo '				<td>'.__('If you are using the index.php from optional_files in your main WordPress directory then you should set this value to match the $podPressFeedCacheDir value.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr>'."\n";
			echo '				<th>PODPRESS_DEBUG_LOG</th>'."\n";
			if ( defined( 'PODPRESS_DEBUG_LOG' ) AND TRUE === constant( 'PODPRESS_DEBUG_LOG' ) ) { 
				$result = '';
				$result = podPress_var_dump('podPress - general settings - permissions test');
				if (!empty($result)) {
					$errormsg = '<p class="podpress_error">'.$result.'</p>';
				} else {
					$errormsg = '';
				}
				echo '				<td><p>'.sprintf(__('is defined and set to: %1$s.', 'podpress'), 'TRUE');
			} elseif ( defined( 'PODPRESS_DEBUG_LOG' ) AND FALSE === constant( 'PODPRESS_DEBUG_LOG' ) ) {
				echo '				<td><p>'.sprintf(__('is defined and set to: %1$s.', 'podpress'), 'FALSE');
			} else {
				echo '				<td><p>'.sprintf(__('is not defined or set to something other than %1$s or %2$s.', 'podpress'), 'TRUE', 'FALSE');
			}
			echo ' '.sprintf(__('By default this constant is set to %2$s and is defined in the podpress.php file. If it is defined as %1$s then podPress logs its activities during the process of the detection of the file size, duration and ID3 tag information to a file with the name podpress_log.dat in the podPress folder.', 'podpress'), 'TRUE', 'FALSE').'</p>'.$errormsg.'</td>'."\n";
			echo '			</tr> '."\n";
			$version_from_db = get_option('podPress_version');
			if ( TRUE == version_compare($version_from_db, constant('PODPRESS_VERSION'), '>') ) {
				$set_version_back_to = '8.8.9';
				echo '			<tr>'."\n";
				echo '				<th><span class="podpress_error">'.__('Version Conflict', 'podpress').'</span></th>'."\n";
				echo '				<td>'.sprintf(__('The current podPress version is smaller than the version number which has been stored previously in the db: %1$s &lt; %2$s. You have had probably installed a podPress version with a higher version number. (Maybe you have tested a Development Version.) The difference between these two version numbers indicates that maybe some db entries are not up to date or settings are not right because the update procedure has not been started. This may cause various problems with the Feeds and the appearance of the podcast episodes in the posts.', 'podpress'), constant('PODPRESS_VERSION'), $version_from_db).'<br /><label for="version_set_back_to">'.sprintf(__('Set the version number in the db back to %1$s (to start possibly necessary upgrade actions):', 'podpress'), $set_version_back_to).'</label> <input type="checkbox" id="version_set_back_to" name="podpress_version_set_back_to" value="'.$set_version_back_to.'" /></td>'."\n";
				echo '			</tr> '."\n";
			} elseif ( TRUE == version_compare($version_from_db, constant('PODPRESS_VERSION'), '<') ) {
				echo '			<tr>'."\n";
				echo '				<th><span class="podpress_error">'.__('Version Conflict', 'podpress').'</span></th>'."\n";
				echo '				<td>'.sprintf(__('The version number in the db is smaller than the current podPress version: %1$s &lt; %2$s. That means most likely that the upgrade process is uncomplete. Because of that, it is possible that you experience problems with the Feeds and the appearance of the podcast episode in the posts. You may ask in the <a href="http://wordpress.org/tags/podpress" target="_blank">WordPress.org Forum</a> for help (Please, use "podpress" as a tag).', 'podpress'), $version_from_db, constant('PODPRESS_VERSION')).'</td>'."\n";
				echo '			</tr> '."\n";
			} else {
				echo '			<tr>'."\n";
				echo '				<th>'.__('current podPress version', 'podpress').'</th>'."\n";
				echo '				<td>'.constant('PODPRESS_VERSION').'</td>'."\n";
				echo '			</tr> '."\n";
			}
			
			$all_plugins = get_plugins();
			$podpress_version = $all_plugins[plugin_basename(dirname(__FILE__).'/podpress.php')]['Version'];
			if ( TRUE == version_compare($podpress_version, '8.8.10', '>=') ) {
				$nr_old_meta_keys_podPressMedia = intval($wpdb->get_var( "SELECT COUNT(*) as old_meta_keys FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'podPressMedia'" ));
				$nr_old_meta_keys_podPressPostSpecific = intval($wpdb->get_var( "SELECT COUNT(*) as old_meta_keys FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'podPressPostSpecific'" ));
				if ( ($nr_old_meta_keys_podPressMedia + $nr_old_meta_keys_podPressPostSpecific) > 0 ) {
					echo '			<tr>'."\n";
					echo '				<th><span class="podpress_error">'.__('old meta_key names detected', 'podpress').'</span></th>'."\n";
					echo '				<td><p>podPressMedia: '.$nr_old_meta_keys_podPressMedia.'<br />podPressPostSpecific: '.$nr_old_meta_keys_podPressPostSpecific.'</p><input type="checkbox" id="add_underscore_to_old_meta_keys" name="podpress_add_underscore_to_old_meta_keys" value="yes" /> <label for="add_underscore_to_old_meta_keys">'.sprintf(__('Rename all meta_keys with the values podPressMedia to _podPressMedia and podPressPostSpecific to _podPressPostSpecific', 'podpress'), $set_version_back_to).'</label></td>'."\n";
					echo '			</tr> '."\n";
				}
			}
			
			if ( TRUE == version_compare($podpress_version, constant('PODPRESS_VERSION'), '!=') ) {
				echo '			<tr>'."\n";
				echo '				<th><span class="podpress_error">'.__('Version Conflict', 'podpress').'</span></th>'."\n";
				echo '				<td><p>'.sprintf(__('podPress version: %1$s','podpress'), $podpress_version).'<br />PODPRESS_VERSION: '.constant('PODPRESS_VERSION').'<br />'.sprintf(__('version number from the db: %1$s','podpress'), $version_from_db).'</p></td>'."\n";
				echo '			</tr> '."\n";
			}

			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";

			echo '	<input type="hidden" name="podPress_submitted" value="general" />'."\n";
			echo '	<p class="submit"> '."\n";
			echo '		<input class="button-primary" type="submit" name="Submit" value="'.__('Update Options', 'podpress').' &raquo;" /> '."\n";
			echo '	</p> '."\n";

			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('Credit', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="enableFooter">'.__('Show podPress footer:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="checkbox" name="enableFooter" id="enableFooter" '; if($this->settings['enableFooter']) { echo 'checked="checked"'; } echo '/>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('Enabling this allows you to give us credit for making this plugin, and lets other podcasters find out what your using to publish your podcasts. If this feature makes your site look bad, please add in podPress with all the other credits, such as the ones in place for WordPress.', 'podpress')."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="enableVersionInFeeds">'.__('Add podPress version information to the feeds:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="checkbox" name="enableVersionInFeeds" id="enableVersionInFeeds" '; if($this->settings['enableVersionInFeeds']) { echo 'checked="checked"'; } echo '/>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('Enabling this will add a comment with the name of this plugin and maybe the current version number to all Feeds of this blog. This comment is usually only visible if you look into the source code of the Feeds and may e.g. help to debug problems.', 'podpress')."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="disableVersionNumber">'.__('Do not show the version number:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			echo '					<input type="checkbox" name="disableVersionNumber" id="disableVersionNumber" '; if($this->settings['disableVersionNumber']) { echo 'checked="checked"'; } echo '/>'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('Do not show the current version number in the footer nor in the Feeds of this blog.', 'podpress')."\n";
			echo '				</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="donation_button">'.__('Donations Appreciated:', 'podpress').'</label></th>'."\n";
			echo '				<td>'."\n";
			if ( TRUE == version_compare($wp_version, '2.7', '>=') AND TRUE == version_compare($wp_version, '2.8', '<')) {// for WP 2.7.x (because the plugins_url() worked differently in WP 2.7.x)
			echo '					<a id="donation_button" href="http://www.mightyseek.com/podpress_donate.php" title="'.__('Donation button of the original author of this project Dan Kuykendall (seek3r)', 'podpress').'" target="_blank"><img alt="'.__('Donation button of the original author of this project Dan Kuykendall (seek3r)', 'podpress').'" border="0" src="'.plugins_url('podpress/images/x-click-but04.gif', __FILE__).'" /></a>'."\n";
			} else { 
			echo '					<a id="donation_button" href="http://www.mightyseek.com/podpress_donate.php" title="'.__('Donation button of the original author of this project Dan Kuykendall (seek3r)', 'podpress').'" target="_blank"><img alt="'.__('Donation button of the original author of this project Dan Kuykendall (seek3r)', 'podpress').'" border="0" src="'.plugins_url('images/x-click-but04.gif', __FILE__).'" /></a>'."\n";
			}
			echo '				</td>'."\n";
			echo '				<td class="podpress_settings_description_cell">'.__('This project is a labor of love, feel no obligation what-so-ever to donate. For those that want to, here ya go.', 'podpress').' <span class="nonessential">('.__('Donation button of the original author of this project Dan Kuykendall (seek3r)', 'podpress').')</span></td>'."\n";
			echo '			</tr> '."\n";
			echo '		</table>'."\n";
			//~ ntm: Frappr.com seems to be down since 01/2010
			//~ echo '		<table class="editform podpress_settings_table">'."\n";
			//~ echo '			<tr>'."\n";
			//~ echo '				<th><label for="frapprmaplink">'.__('Frapper Map:', 'podpress').'</label></th>'."\n";
			//~ echo '				<td>'."\n";
			//~ echo '					<a id="frapprmaplink" href="http://www.frappr.com/mightyseek"><img src="http://www.frappr.com/i/frapper_sticker.gif" alt="Check out our Frappr!" title="Check out our Frappr!" border="0"></a>'."\n";
			//~ echo '				</td>'."\n";
			//~ echo '			</tr> '."\n";
			//~ echo '			<tr>'."\n";
			//~ echo '				<td colspan="2">Let us know where you are!</td>'."\n";
			//~ echo '			</tr> '."\n";
			//~ echo '		</table>'."\n";
			echo '	</fieldset>'."\n";
			
			echo '	</form> '."\n";

			$sql = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE meta_key IN('podPress_podcastStandardAudio',
			                                                                 'podPress_podcastStandardAudioSize',
			                                                                 'podPress_podcastStandardAudioDuration',
			                                                                 'podPress_podcastEnhancedAudio',
			                                                                 'podPress_podcastEnhancedAudioSize',
			                                                                 'podPress_podcastEnhancedAudioDuration',
			                                                                 'podPress_podcastVideo',
			                                                                 'podPress_podcastVideoSize',
			                                                                 'podPress_podcastVideoDuration',
			                                                                 'podPress_podcastVideoDimension',
			                                                                 'podPress_webVideo',
			                                                                 'podPress_webVideoSize',
			                                                                 'podPress_webVideoDuration',
			                                                                 'podPress_webVideoDimension',
			                                                                 'podPress_podcastEbook',
			                                                                 'podPress_podcastEbookSize',
			                                                                 'itunes:duration')";
			$stats = $wpdb->get_results($sql);
			if($stats) {
				echo '	<form method="post">'."\n";
				echo '	<fieldset class="options">'."\n";
				echo '		<legend>'.__('Complete Upgrade Process', 'podpress').'</legend>'."\n";
				echo '		<table width="100%" cellspacing="2" cellpadding="5" class="editform">'."\n";
				echo '			<tr>'."\n";
				echo '				<th><label for="cleanupOldMetaKeys">'.__('Remove Pre v.40 database clutter.', 'podpress').':</label></th>'."\n";
				echo '				<td>'."\n";
				echo '					<input type="checkbox" name="cleanupOldMetaKeys" id="cleanupOldMetaKeys" />'."\n";
				echo '				</td>'."\n";
				echo '			</tr> '."\n";
				echo '		</table>'."\n";
				echo '	</fieldset>'."\n";
				echo '	<input type="hidden" name="podPress_submitted" value="general" />'."\n";
				echo '	<p class="submit"> '."\n";
				echo '		<input class="button-primary" type="submit" name="Submit" value="'.__('Update Options', 'podpress').' &raquo;" /> '."\n";
				echo '	</p> '."\n";
				echo '	</form> '."\n";
			}

			echo '</div>'."\n";
		}

		function settings_general_save() {
			GLOBAL $wpdb;
			$blog_charset = get_bloginfo('charset');
			if ( function_exists('check_admin_referer') ) {
				check_admin_referer('podPress_general_options_nonce');
			}

			if(function_exists('wp_cache_flush')) {
				wp_cache_flush();
			}
			
			if(isset($_POST['mediaWebPath'])) {
				$mediaWebPath = trim($_POST['mediaWebPath']);
				$mediaWebPath = rtrim($mediaWebPath, '/');
				$this->settings['mediaWebPath'] = clean_url($mediaWebPath, array('http', 'https'), 'db');
			}

			if(isset($_POST['mediaFilePath'])) {
				$mediaFilePath = trim($_POST['mediaFilePath']);
				$mediaFilePath = rtrim($mediaFilePath, '/');
				$mediaFilePath = rtrim($mediaFilePath, '\\');
				$this->settings['mediaFilePath'] = strip_tags($mediaFilePath);
			}

			if(isset($_POST['enableStats'])) {
				$this->settings['enableStats'] = true;
				$this->createstatistictables();
			} else {
				$this->settings['enableStats'] = false;
			}
			
			if ( isset($_POST['disabledashboardwidget']) ) {
				$this->settings['disabledashboardwidget'] = TRUE;
			} else {
				$this->settings['disabledashboardwidget'] = FALSE;
			}

			if(isset($_POST['statMethod'])) {
				$this->settings['statMethod'] = $_POST['statMethod'];
			}

			if(isset($_POST['statLogging'])) {
				$this->settings['statLogging'] = $_POST['statLogging'];
			}

			if(isset($_POST['enable3rdPartyStats'])) {
				$this->settings['enable3rdPartyStats'] = $_POST['enable3rdPartyStats'];
			}

			if(isset($_POST['enableBlubrryStats'])) {
				$this->settings['enableBlubrryStats'] = true;
			} else {
				$this->settings['enableBlubrryStats'] = false;
			}
			if(isset($_POST['statBluBrryProgramKeyword'])) {
				$this->settings['statBluBrryProgramKeyword'] = $_POST['statBluBrryProgramKeyword'];
			}

			if(isset($_POST['maxMediaFiles'])) {
				$this->settings['maxMediaFiles'] = intval(preg_replace('/[^0-9]/', '', $_POST['maxMediaFiles']));
			}
			
			if ( TRUE == isset($_POST['activate_podpressmedia_search']) AND 'true' == $_POST['activate_podpressmedia_search'] ) {
				$this->settings['activate_podpressmedia_search'] = TRUE;
			} else {
				$this->settings['activate_podpressmedia_search'] = FALSE;
			}
			
			if(isset($_POST['enablePodangoIntegration'])) {
				$this->settings['enablePodangoIntegration'] = true;
			} else {
				$this->settings['enablePodangoIntegration'] = false;
			}

			if(isset($_POST['enablePremiumContent'])) {
				$this->settings['enablePremiumContent'] = true;
				$this->add_podpress_role();
				if(is_object($GLOBALS['wp_rewrite'])
					&& is_array($GLOBALS['wp_object_cache']) 
					&& is_array($GLOBALS['wp_object_cache']['cache']) 
					&& is_array($GLOBALS['wp_object_cache']['cache']['options']) 
					&& is_array($GLOBALS['wp_object_cache']['cache']['options']['alloptions']) 
					&& is_array($GLOBALS['wp_object_cache']['cache']['options']['alloptions']['rewrite_rules'])
					&& !strpos($GLOBALS['wp_object_cache']['cache']['options']['alloptions']['rewrite_rules'], 'playlist.xspf')
					) {
						$GLOBALS['wp_rewrite']->flush_rules();
				}
			} else {
				$this->settings['enablePremiumContent'] = false;
			}

			// ntm: this is not active because there is no further line of code outside this file which uses this protectedMediaFilePath
			//~ if(isset($_POST['protectedMediaFilePath'])) {
				//~ $this->settings['protectedMediaFilePath'] = $_POST['protectedMediaFilePath'];
			//~ }

			if(isset($_POST['premiumMethod']) && $_POST['premiumMethod'] == 'Basic') {
				$this->settings['premiumMethod'] = 'Basic';
			} else {
				$this->settings['premiumMethod'] = 'Digest';
			}

			if(isset($_POST['premiumContentFakeEnclosure'])) {
				$this->settings['premiumContentFakeEnclosure'] = true;
			} else {
				$this->settings['premiumContentFakeEnclosure'] = false;
			}

			if(isset($_POST['enableTorrentCasting'])) {
				$this->settings['enableTorrentCasting'] = true;
			} else {
				$this->settings['enableTorrentCasting'] = false;
			}

			if(isset($_POST['feedCacheDir'])) {
				$this->settings['feedCacheDir'] = $_POST['feedCacheDir'];
			}

			if(isset($_POST['contentBeforeMore'])) {
				$this->settings['contentBeforeMore'] = $_POST['contentBeforeMore'];
			}

			if(isset($_POST['contentLocation'])) {
				$this->settings['contentLocation'] = $_POST['contentLocation'];
			}

			if(isset($_POST['contentImage'])) {
				$this->settings['contentImage'] = $_POST['contentImage'];
			}

			if(isset($_POST['contentPlayer'])) {
				$this->settings['contentPlayer'] = $_POST['contentPlayer'];
			}
			
			if(isset($_POST['contentHidePlayerPlayNow'])) {
				$this->settings['contentHidePlayerPlayNow'] = $_POST['contentHidePlayerPlayNow'];
			}

			if(isset($_POST['contentDownload'])) {
				$this->settings['contentDownload'] = $_POST['contentDownload'];
			}

			if(isset($_POST['contentDownloadText'])) {
				$this->settings['contentDownloadText'] = $_POST['contentDownloadText'];
			}

			if(isset($_POST['contentDownloadStats'])) {
				$this->settings['contentDownloadStats'] = $_POST['contentDownloadStats'];
			}

			if ( TRUE == isset($_POST['contentDuration'])) {
				$duration_schemes = Array('h:m:s', 'm:s', 'h:m:s:ms', 'm:s:ms', 's:ms', 'h', 'm', 's', 'ms');
				if ( TRUE == in_array($_POST['contentDuration'], $duration_schemes) OR 'disabled' == $_POST['contentDuration']) {
					$this->settings['contentDuration'] = $_POST['contentDuration'];
				} else {
					$this->settings['contentDuration'] = 'h:m:s';
				}
			}
			
			if ( TRUE == isset($_POST['contentDurationdivider']) ) {
				$duration_dividers = Array('colon', 'hminsms', 'hrminsecmsec', 'hoursminutessecondsmilliseconds');
				if ( TRUE == in_array($_POST['contentDurationdivider'], $duration_dividers)) {
					$this->settings['contentDurationdivider'] = $_POST['contentDurationdivider'];
				} else {
					$this->settings['contentDurationdivider'] = 'colon';
				}
			}
			
			if ( TRUE == isset($_POST['contentfilesize']) AND ('disabled' === $_POST['contentfilesize'] OR 'enabled' === $_POST['contentfilesize']) ) {
				$this->settings['contentfilesize'] = $_POST['contentfilesize'];
			}
			
			if ( TRUE == isset($_POST['incontentandexcerpt']) ) {
				$incontentandexcerpt_vals = Array('in_content_and_excerpt', 'in_content_only', 'in_excerpt_only');
				if (TRUE == in_array($_POST['incontentandexcerpt'], $incontentandexcerpt_vals) ) {
					$this->settings['incontentandexcerpt'] = $_POST['incontentandexcerpt'];
				} else {
					$this->settings['incontentandexcerpt'] = 'in_content_and_excerpt';
				}
			}
			
			if ( isset($_POST['do_not_use_the_target_attribute']) ) {
				$this->settings['do_not_use_the_target_attribute'] = TRUE;
			} else {
				$this->settings['do_not_use_the_target_attribute'] = FALSE;
			}
			
			if ( TRUE == isset($_POST['metaboxforcustomposttypes']) AND TRUE == is_array($_POST['metaboxforcustomposttypes']) ) {
				$this->settings['metaboxforcustomposttypes'] = $_POST['metaboxforcustomposttypes'];
			} else {
				$this->settings['metaboxforcustomposttypes'] = Array();
				if ( FALSE == isset($_POST['metaboxforcustomposttypes']) ) {
					unset($this->settings['metaboxforcustomposttypes']);
				}
			}
			
			if(isset($_POST['enableFooter'])) {
				$this->settings['enableFooter'] = true;
			} else {
				$this->settings['enableFooter'] = false;
			}
			if(isset($_POST['enableVersionInFeeds'])) {
				$this->settings['enableVersionInFeeds'] = true;
			} else {
				$this->settings['enableVersionInFeeds'] = false;
			}
			if (isset($_POST['disableVersionNumber'])) {
				$this->settings['disableVersionNumber'] = true;
			} else {
				$this->settings['disableVersionNumber'] = false;
			}


			if(isset($_POST['cleanupOldMetaKeys'])) {
				$sql = "DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_key IN('podPress_podcastStandardAudio',
				                                                                 'podPress_podcastStandardAudioSize',
				                                                                 'podPress_podcastStandardAudioDuration',
				                                                                 'podPress_podcastEnhancedAudio',
				                                                                 'podPress_podcastEnhancedAudioSize',
				                                                                 'podPress_podcastEnhancedAudioDuration',
				                                                                 'podPress_podcastVideo',
				                                                                 'podPress_podcastVideoSize',
				                                                                 'podPress_podcastVideoDuration',
				                                                                 'podPress_podcastVideoDimension',
				                                                                 'podPress_webVideo',
				                                                                 'podPress_webVideoSize',
				                                                                 'podPress_webVideoDuration',
				                                                                 'podPress_webVideoDimension',
				                                                                 'podPress_podcastEbook',
				                                                                 'podPress_podcastEbookSize',
				                                                                 'itunes:duration',
				                                                                 'enclosure',
				                                                                 'enclosure_hold')";
				$wpdb->query($sql);
			}
			
			if ( TRUE == isset($_POST['podpress_add_underscore_to_old_meta_keys']) ) {
				// rename the post specific settings and the media meta keys. 
				$wpdb->query( "UPDATE ".$wpdb->prefix."postmeta SET meta_key = '_podPressPostSpecific' WHERE meta_key = 'podPressPostSpecific'" );
				$wpdb->query( "UPDATE ".$wpdb->prefix."postmeta SET meta_key = '_podPressMedia' WHERE meta_key = 'podPressMedia'" );
				
				// update the version number in the db
				$current = constant('PODPRESS_VERSION');
				update_option('podPress_version', $current);
			}
			
			if ( TRUE == isset($_POST['podpress_version_set_back_to']) ) {
				update_option( 'podPress_version', strip_tags(trim($_POST['podpress_version_set_back_to'])) );
			}
			
			$result = podPress_update_option('podPress_config', $this->settings);
			if ( FALSE !== $result ) {
				$location = site_url() . '/wp-admin/admin.php?page=podpress/podpress_general.php&updated=true';
			} else {
				$location = site_url() . '/wp-admin/admin.php?page=podpress/podpress_general.php&updated=false';
			}
			header('Location: '.$location);
			exit;
		}
	}
?>