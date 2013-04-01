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

		function settings_players_edit() {
			GLOBAL $wp_version;
			podPress_isAuthorized();
			if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
				echo '<div id="message" class="updated fade"><p>'. __('Settings Saved', 'podpress').'</p></div>';
			} elseif (isset($_GET['updated']) && $_GET['updated'] != 'true') {
				echo '<div id="message" class="error fade"><p>'. __('<strong>Error:</strong> Unable to save the settings', 'podpress').'</p></div>';
			}
			
			if($this->settings['player']['bg'] == '') {
				$this->resetPlayerSettings();
			}

			echo '<div class="wrap">'."\n";
			if ( TRUE == version_compare($wp_version, '2.7', '>=') ) {
				echo '<div id="podpress-icon" class="icon32"><br /></div>';
			} 
			if ( TRUE == version_compare($wp_version, '2.8', '>=') ) {
				echo '	<h2>'.__('Player Settings', 'podpress').'</h2>'."\n";
				// get the plugins version information via the WP plugins version check
				if ( TRUE == version_compare($wp_version, '2.9', '>=') ) {
					$versioninfo = get_site_transient( 'update_plugins' );
				} else {
					$versioninfo = get_transient( 'update_plugins' );
				}
				// If there is a new version then there is a 'response'. This is the method from the plugins page. 
				if ( FALSE !== isset($versioninfo->response[plugin_basename(dirname(__FILE__).'/podpress.php')]->new_version) ) {
					echo '<div class="message updated"><p><a href="http://wordpress.org/extend/plugins/podpress/" target="_blank">'.__('a new podPress version is available', 'podpress').'</a></p></div>';
				}
			} else {
				echo '	<h2>'.__('Player Settings', 'podpress').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.mightyseek.com/podpress/#download" target="_new"><img src="http://www.mightyseek.com/podpress_downloads/versioncheck.php?current='.PODPRESS_VERSION.'" alt="'.__('Checking for updates... Failed.', 'podpress').'" border="0" /></a></h2>'."\n";
			}
			
			echo '	<form method="post">'."\n";

			if ( function_exists('wp_nonce_field') ) { // since WP 2.0.4
				wp_nonce_field('podPress_player_settings_nonce');
			}
			
			if ( TRUE == version_compare($wp_version, '2.7', '>=') AND TRUE == version_compare($wp_version, '2.8', '<')) {// for WP 2.7.x (because the plugins_url() worked differently in WP 2.7.x)
				$plugins_url = plugins_url('podpress', __FILE__);
			} else { 
				$plugins_url = plugins_url('', __FILE__);
			}
			
			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('MP3 Player', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'.__('Player', 'podpress').':</th>'."\n";
			echo '				<td colspan="2"><select name="mp3Player"><option value="1pixelout"'; if($this->settings['mp3Player'] == '1pixelout') { echo ' selected="selected"'; } echo '>'.__('1PixelOut','podpress').'</option><option value="podango"'; if($this->settings['mp3Player'] == 'podango') { echo ' selected="selected"'; } echo '>'.__('Podango','podpress').'</option></select></td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'.__('Colour map', 'podpress').':</th>'."\n";
			if ($this->settings['mp3Player'] == '1pixelout') {
				echo '				<td colspan="2"><img src="'.$plugins_url.'/images/colormap_1pixelout_numbers.png" alt="'.__('1PixelOut Player Color Map', 'podpress').'" /></td>'."\n";
			} else {
				echo '				<td colspan="2"><img src="'.$plugins_url.'/images/colormap_podango_numbers.png" alt="'.__('Podango Player Color Map', 'podpress').'" /></td>'."\n";
			}
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_lefticon_">'.__('Left icon', 'podpress').' (1):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_lefticon_" name="playerSettings[lefticon]" size="40" value="'.$this->settings['player']['lefticon'].'" style="background-color: '.$this->settings['player']['lefticon'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" />'."\n";
			echo '				</td>'."\n";
			echo '				<td rowspan="16" class="podpress_player_color_cell_right_col">'."\n";
			echo '			<br/>	'.__('Pick the field you want to change the color for. Then mouse over the color selector, and you will see the color change for the field you chose. Click on the color selector to lock in your selection.','podpress').'<br/><br/>

	<div id="podpress_color_picker_box"><!-- color picker box begin -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#000000"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#000000\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#000033"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#000033\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#000066"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#000066\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#000099"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#000099\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#0000cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#0000cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#0000ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#0000ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#006600"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#006600\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#006633"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#006633\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#006666"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#006666\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#006699"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#006699\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#0066cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#0066cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#0066ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#0066ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00cc00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00cc00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00cc33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00cc33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00cc66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00cc66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00cc99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00cc99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00cccc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00cccc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00ccff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00ccff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#003300"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#003300\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#003333"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#003333\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#003366"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#003366\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#003399"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#003399\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#0033cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#0033cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#0033ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#0033ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#009900"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#009900\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#009933"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#009933\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#009966"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#009966\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#009999"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#009999\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#0099cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#0099cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#0099ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#0099ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00ff00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00ff00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00ff33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00ff33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00ff66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00ff66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00ff99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00ff99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00ffcc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00ffcc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#00ffff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#00ffff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#330000"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#330000\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#330033"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#330033\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#330066"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#330066\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#330099"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#330099\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#3300cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#3300cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#3300ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#3300ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#336600"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#336600\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#336633"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#336633\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#336666"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#336666\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#336699"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#336699\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#3366cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#3366cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#3366ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#3366ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33cc00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33cc00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33cc33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33cc33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33cc66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33cc66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33cc99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33cc99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33cccc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33cccc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33ccff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33ccff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#333300"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#333300\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#333333"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#333333\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#333366"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#333366\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#333399"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#333399\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#3333cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#3333cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#3333ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#3333ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#339900"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#339900\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#339933"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#339933\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#339966"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#339966\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#339999"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#339999\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#3399cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#3399cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#3399ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#3399ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33ff00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33ff00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33ff33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33ff33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33ff66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33ff66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33ff99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33ff99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33ffcc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33ffcc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#33ffff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#33ffff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#660000"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#660000\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#660033"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#660033\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#660066"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#660066\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#660099"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#660099\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#6600cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#6600cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#6600ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#6600ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#666600"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#666600\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#666633"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#666633\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#666666"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#666666\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#666699"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#666699\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#6666cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#6666cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#6666ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#6666ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66cc00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66cc00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66cc33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66cc33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66cc66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66cc66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66cc99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66cc99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66cccc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66cccc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66ccff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66ccff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#663300"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#663300\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#663333"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#663333\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#663366"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#663366\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#663399"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#663399\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#6633cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#6633cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#6633ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#6633ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#669900"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#669900\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#669933"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#669933\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#669966"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#669966\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#669999"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#669999\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#6699cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#6699cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#6699ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#6699ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66ff00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66ff00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66ff33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66ff33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66ff66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66ff66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66ff99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66ff99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66ffcc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66ffcc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#66ffff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#66ffff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#990000"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#990000\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#990033"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#990033\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#990066"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#990066\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#990099"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#990099\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#9900cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#9900cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#9900ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#9900ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#996600"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#996600\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#996633"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#996633\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#996666"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#996666\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#996699"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#996699\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#9966cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#9966cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#9966ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#9966ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99cc00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99cc00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99cc33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99cc33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99cc66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99cc66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99cc99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99cc99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99cccc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99cccc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99ccff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99ccff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#993300"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#993300\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#993333"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#993333\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#993366"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#993366\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#993399"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#993399\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#9933cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#9933cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#9933ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#9933ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#999900"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#999900\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#999933"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#999933\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#999966"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#999966\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#999999"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#999999\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#9999cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#9999cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#9999ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#9999ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99ff00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99ff00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99ff33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99ff33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99ff66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99ff66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99ff99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99ff99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99ffcc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99ffcc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#99ffff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#99ffff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#cc0000"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc0000\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc0033"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc0033\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc0066"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc0066\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc0099"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc0099\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc00cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc00cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc00ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc00ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc6600"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc6600\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc6633"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc6633\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc6666"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc6666\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc6699"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc6699\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc66cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc66cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc66ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc66ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cccc00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cccc00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cccc33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cccc33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cccc66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cccc66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cccc99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cccc99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cccccc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cccccc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ccccff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ccccff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#cc3300"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc3300\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc3333"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc3333\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc3366"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc3366\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc3399"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc3399\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc33cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc33cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc33ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc33ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc9900"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc9900\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc9933"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc9933\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc9966"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc9966\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc9999"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc9999\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc99cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc99cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#cc99ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#cc99ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ccff00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ccff00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ccff33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ccff33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ccff66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ccff66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ccff99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ccff99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ccffcc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ccffcc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ccffff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ccffff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#ff0000"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff0000\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff0033"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff0033\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff0066"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff0066\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff0099"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff0099\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff00cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff00cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff00ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff00ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff6600"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff6600\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff6633"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff6633\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff6666"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff6666\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff6699"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff6699\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff66cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff66cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff66ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff66ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffcc00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffcc00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffcc33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffcc33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffcc66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffcc66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffcc99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffcc99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffcccc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffcccc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffccff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffccff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		<div class="podpress_color_picker_row">
			<span class="podpress_color_picker_field" style="background-color:#ff3300"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff3300\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff3333"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff3333\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff3366"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff3366\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff3399"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff3399\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff33cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff33cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff33ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff33ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff9900"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff9900\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff9933"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff9933\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff9966"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff9966\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff9999"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff9999\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff99cc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff99cc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ff99ff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ff99ff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffff00"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffff00\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffff33"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffff33\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffff66"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffff66\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffff99"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffff99\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffffcc"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffffcc\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
			<span class="podpress_color_picker_field" style="background-color:#ffffff"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#ffffff\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>
		</div><!-- color picker row end -->
		
		'."\n";
		
		$hex = Array( 0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => 'A', 11 => 'B', 12 => 'C', 13 => 'D', 14 => 'E', 15 => 'F' );
		// gray scale
		echo '<div class="podpress_color_picker_row">'."\n";
		for ($i=0; $i < 16; $i++) {
			echo '		<span class="podpress_color_picker_field" style="background-color:#'.$hex[$i].$hex[$i].$hex[$i].$hex[$i].$hex[$i].$hex[$i].'"><a href="javascript: podPress_colorLock()" onmouseover="javascript: podPress_colorSet(\'#'.$hex[$i].$hex[$i].$hex[$i].$hex[$i].$hex[$i].$hex[$i].'\'); return true"><img src="'.$plugins_url.'/images/podpress_spacer.gif" alt="." /></a></span>'."\n";
		}
		echo '</div>'."\n";
	echo '</div><!-- color picker box end -->'."\n";
	echo '<br/>'."\n";
	echo '<input type="button" name="ResetColors" value="'.__('Reset Colors to Defaults','podpress').'" onclick="javascript: podPress_colorReset();" />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_leftbg_">'.__('Left background', 'podpress').' (2):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_leftbg_" name="playerSettings[leftbg]" size="40" value="'.$this->settings['player']['leftbg'].'" style="background-color: '.$this->settings['player']['leftbg'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" />'."\n";
			echo '				</td>'."\n";			
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_volslider_">'.__('Volume control slider', 'podpress').' (3):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_volslider_" name="playerSettings[volslider]" size="40" value="'.$this->settings['player']['volslider'].'" style="background-color: '.$this->settings['player']['volslider'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_voltrack_">'.__('Volume control track', 'podpress').' (4):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_voltrack_" name="playerSettings[voltrack]" size="40" value="'.$this->settings['player']['voltrack'].'" style="background-color: '.$this->settings['player']['voltrack'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_text_">'.__('Text', 'podpress').' (5):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_text_" name="playerSettings[text]" size="40" value="'.$this->settings['player']['text'].'" style="background-color: '.$this->settings['player']['text'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_tracker_">'.__('Progress bar', 'podpress').' (6):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_tracker_" name="playerSettings[tracker]" size="40" value="'.$this->settings['player']['tracker'].'" style="background-color: '.$this->settings['player']['tracker'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_slider_">'.__('Podango Progress bar track', 'podpress').' (6p):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_slider_" name="playerSettings[slider]" size="40" value="'.$this->settings['player']['slider'].'" style="background-color: '.$this->settings['player']['slider'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" /> (*)'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_loader_">'.__('Loading bar', 'podpress').' (7):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_loader_" name="playerSettings[loader]" size="40" value="'.$this->settings['player']['loader'].'" style="background-color: '.$this->settings['player']['loader'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" /> (*)'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_track_">'.__('Progress bar track', 'podpress').' (8):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_track_" name="playerSettings[track]" size="40" value="'.$this->settings['player']['track'].'" style="background-color: '.$this->settings['player']['track'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_border_">'.__('Progress bar border', 'podpress').' (9):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_border_" name="playerSettings[border]" size="40" value="'.$this->settings['player']['border'].'" style="background-color: '.$this->settings['player']['border'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" /> (*)'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_bg_">'.__('Background', 'podpress').' (10):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_bg_" name="playerSettings[bg]" size="40" value="'.$this->settings['player']['bg'].'" style="background-color: '.$this->settings['player']['bg'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" /> (*)<br />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_skip_">'.__('Next / Previous buttons', 'podpress').' (11):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_skip_" name="playerSettings[skip]" size="40" value="'.$this->settings['player']['skip'].'" style="background-color: '.$this->settings['player']['skip'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_righticon_">'.__('Right icon', 'podpress').' (12):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_righticon_" name="playerSettings[righticon]" size="40" value="'.$this->settings['player']['righticon'].'" style="background-color: '.$this->settings['player']['righticon'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" /> (*)'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_righticonhover_">'.__('Right icon (hover)', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_righticonhover_" name="playerSettings[righticonhover]" size="40" value="'.$this->settings['player']['righticonhover'].'" style="background-color: '.$this->settings['player']['righticonhover'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" /> (*)'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_rightbg_">'.__('Right background', 'podpress').' (13):</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_rightbg_" name="playerSettings[rightbg]" size="40" value="'.$this->settings['player']['rightbg'].'" style="background-color: '.$this->settings['player']['rightbg'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" /> (*)'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_rightbghover_">'.__('Right background (hover)', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_color_cell">'."\n";
			echo '					<input type="text" id="playerSettings_rightbghover_" name="playerSettings[rightbghover]" size="40" value="'.$this->settings['player']['rightbghover'].'" style="background-color: '.$this->settings['player']['rightbghover'].';" onfocus="javascript: podPress_switchColorInputs(this.id);" onchange="javascript: this.style.background=this.value;" /> (*)'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>&nbsp;</th>'."\n";
			echo '				<td>&nbsp;</td>'."\n";
			echo '				<td class="podpress_player_color_cell_right_col">'."\n";
			echo '					 '.__('(*) is also a Podango Player option (all other options are for the 1PixelOut player only)', 'podpress')."<br />\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_listenWrapper">'.__('Enable Listen Wrapper', 'podpress').':</label></th>'."\n";
			echo '				<td colspan="2">'."\n";
			if ( FALSE == $this->settings['enablePodangoIntegration'] AND TRUE == isset($this->settings['mp3Player']) AND '1pixelout' == $this->settings['mp3Player'] ) {
			echo '					<input type="checkbox" name="playerSettings[listenWrapper]" id="playerSettings_listenWrapper" '; if($this->settings['player']['listenWrapper']) { echo 'checked="checked"'; } echo ' onclick="javascript: if (this.checked == true) { document.getElementById(\'podpress_lwc_1\').style.backgroundImage = \'url('.$plugins_url.'/images/listen_wrapper.gif)\';} else { document.getElementById(\'podpress_lwc_1\').style.backgroundImage = \'\'; }" />'."\n";
			} else {
			echo '					<input type="checkbox" name="playerSettings[listenWrapper]" id="playerSettings_listenWrapper" '; if($this->settings['player']['listenWrapper']) { echo 'checked="checked"'; } echo " onclick=\"javascript: podPressShowHideWrapper('podPressPlayerSpace_1', '".$plugins_url.'/images/listen_wrapper.gif'."');\"/>\n";
			}
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>&nbsp;</th>'."\n";
			echo '				<td colspan="2">'."\n";
			if ( FALSE == $this->settings['enablePodangoIntegration'] AND TRUE == isset($this->settings['mp3Player']) AND '1pixelout' == $this->settings['mp3Player'] ) {
				if (TRUE == isset($this->settings['player']['listenWrapper']) AND TRUE == $this->settings['player']['listenWrapper']) {
					echo '					<div class="podpress_listenwrapper_container" id="podpress_lwc_1" style="background-image:url('.$plugins_url.'/images/listen_wrapper.gif);"><div class="podpress_mp3_borderleft"></div><div class="podpress_1pixelout_container"><div id="podPressPlayerSpace_1"></div></div></div>'."\n";
				} else {
					echo '					<div class="podpress_listenwrapper_container" id="podpress_lwc_1"><div class="podpress_mp3_borderleft"></div><div class="podpress_1pixelout_container"><div id="podPressPlayerSpace_1"></div></div></div>'."\n";
				}
			} else {
				echo '					<div id="podPressPlayerSpace_1"></div>'."\n";
			}
			echo '					<div id = "podPressPlayerSpace_1_PlayLink"></div>'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '		</table>'."\n";
			echo '	</fieldset><br />'."\n";
			
			
			if ( FALSE == $this->settings['enablePodangoIntegration'] AND TRUE == isset($this->settings['mp3Player']) AND '1pixelout' == $this->settings['mp3Player'] ) {
				echo '	<script type="text/javascript"><!--'."\n";
				echo '		podpressAudioPlayer.embed("podPressPlayerSpace_1", { soundFile: "sample.mp3", width: 290, height: 24, autostart: "no" });'."\n"; // titles: "'.js_escape($val['artist']).'", artists: "'.js_escape($val['title']).'",
				echo '	--></script>'."\n";
			} else {
				echo '<script type="text/javascript"><!--'."\n";
				if (!$this->settings['player']['listenWrapper']) { 
					echo '	podPressMP3PlayerWrapper = false;'."\n";
				} else { 
					echo '	podPressMP3PlayerWrapper = true;'."\n";
				} 
				echo "	document.getElementById('podPressPlayerSpace_1').innerHTML = podPressGeneratePlayer(1, 'sample.mp3', '', '');\n";
				echo "--></script>\n";
			}
			
			
			echo '	<fieldset class="options">'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_initialvolume_">'.__('Initial Volume Level', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col">'."\n";
			echo '					<select name="playerSettings[initialvolume]">'."\n";
			if ( FALSE == isset($this->settings['player']['initialvolume']) ) {
				$initialvolume = 70;
			} else {
				$initialvolume = intval($this->settings['player']['initialvolume']);
			}
			for ( $i = 100; $i >= 0; $i--) {
				if ($i == $initialvolume) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				echo '						<option value="'.$i.'"'.$selected.'>'.$i.'</option>'."\n";
			}
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td>'.__('(default: 70)', 'podpress').'</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_buffer_">'.__('Buffering Time', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col">'."\n";
			echo '					<select name="playerSettings[buffer]">'."\n";
			if ( FALSE == isset($this->settings['player']['buffer']) ) {
				$buffer = 5;
			} else {
				$buffer = intval($this->settings['player']['buffer']);
			}
			for ( $i = 5; $i <= 60; $i++) {
				if ($i == $buffer) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				echo '						<option value="'.$i.'"'.$selected.'>'.$i.'</option>'."\n";
			}
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td>'.__('It is the time span in seconds (default: 5) which the player uses to load a part of the mp3 before it starts to play the mp3 file.', 'podpress').'</td>'."\n";
			echo '			</tr>'."\n";
			if (TRUE == isset($this->settings['player']['checkpolicy']) AND 'yes' == $this->settings['player']['checkpolicy'] AND (FALSE == isset($this->settings['player']['overwriteTitleandArtist']) OR 'yes' !== $this->settings['player']['overwriteTitleandArtist']) ) { $checked = ' checked="checked"'; }  else { $checked = ''; }
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_checkpolicy_">'.__('Use a cross-domain policy file', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col">'."\n";
			echo '					<input type="checkbox" name="playerSettings[checkpolicy]" id="playerSettings_checkpolicy_"'.$checked.' value="yes" onclick="podpress_checkonlyone( \'playerSettings_overwriteTitleandArtist_\', this.id );" />'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_player_description_cell">'.__('By default, the 1PixelOut player gets the track information from the <a href="http://en.wikipedia.org/wiki/Id3" target="_blank" title="en.Wikipedia: ID3 tags">ID3 tags</a> of a mp3 file. These tags are usually set by (resp. with the help of) the software which you use to create the file. If they are set correctly, you should see the artist and title in the player. But if your mp3 files are not located in one of the sub folders of your blog then the player won\'t be able to read the ID3 tags even if the file is located on a subdomain (e.g. your blog url is yourdomain.com and your files are on www.yourdomain.com). This is a security feature of the <a href="http://en.wikipedia.org/wiki/Adobe_Flash_Player" title="en.Wikipedia: Adobe Flash Player">Adobe Flash Player</a> of your web browser. The 1PixelOut player shows in such situations e.g. "<strong>Track #1</strong>" instead of the ID3 information.<br />But you can allow the player from certain domains the access to the ID3 tags with the help of a crossdomain policy file. A policy file is a simple <a href="http://en.wikipedia.org/wiki/XML" title="en.Wikipedia: XML">XML</a> file that you place in the root of the server where you host your mp3 files. Here is the syntax:', 'podpress');
			echo '				<br /><code style="display:block;">';
			echo '				&lt;?xml version="1.0"?&gt;'."<br />\n";
			echo '				&lt;!DOCTYPE cross-domain-policy SYSTEM "http://www.adobe.com/xml/dtds/cross-domain-policy.dtd"&gt;'."<br />\n";
			echo '				&lt;cross-domain-policy&gt;'."<br />\n";
			echo '					&lt;allow-access-from domain="www.yourdomain.com"/&gt;'."<br />\n";
			echo '				&lt;/cross-domain-policy&gt;';
			echo '				</code>';
			echo '				'.__('Replace yourdomain.com with the domain on which your blog and the player is hosted. You can also use wildcards to allow access from any subdomain (*.yourdomain.com). But it is recommended to be as restrictive as possible. More information, read Adobe\'s full <a href="http://www.adobe.com/devnet/articles/crossdomain_policy_file_spec.html" target="_blank">cross-domain policy file specification</a>. Name the policy file <em>crossdomain.xml</em>.<br />Enable this option only if all your mp3 files are located on a server with a policy file.<br />This option works only if you are not using the statistic features of podPress.', 'podpress')."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			if (TRUE == isset($this->settings['player']['overwriteTitleandArtist']) AND 'yes' == $this->settings['player']['overwriteTitleandArtist'] AND (FALSE == isset($this->settings['player']['checkpolicy']) OR 'yes' !== $this->settings['player']['checkpolicy']) ) { $checked = ' checked="checked"'; }  else { $checked = ''; }
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_overwriteTitleandArtist_">'.__('Use custom values for titles and artists instead of the ID3 data', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col">'."\n";
			echo '					<input type="checkbox" name="playerSettings[overwriteTitleandArtist]" id="playerSettings_overwriteTitleandArtist_"'.$checked.' value="yes" onclick="podpress_checkonlyone( \'playerSettings_checkpolicy_\', this.id );" />'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_player_description_cell">'.sprintf(__('With this option the displayed title and artist of a mp3 file will be overwritten by the custom title which you can insert for each file at the editor page and the post specific value of iTunes:Author. This helps in cases when the "%1$s" option is not helping e.g. if your media files are on a server or domain and you can not use a cross-domain policy file or if you are using the statistic features of podPress.', 'podpress'), __('Use a cross-domain policy file', 'podpress')).'</td>'."\n";
			echo '			</tr>'."\n";
			if (TRUE == isset($this->settings['player']['animation']) AND 'no' == $this->settings['player']['animation'] ) { $checked = ' checked="checked"'; }  else { $checked = ''; }
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_animation_">'.__('1Pixelout Player always open', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col">'."\n";
			echo '					<input type="checkbox" name="playerSettings[animation]" id="playerSettings_animation_"'.$checked.' value="yes" />'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_player_description_cell">'.__('The player is always open.', 'podpress').'</td>'."\n";
			echo '			</tr>'."\n";		
			if (TRUE == isset($this->settings['player']['remaining']) AND 'yes' == $this->settings['player']['remaining'] ) { $checked = ' checked="checked"'; }  else { $checked = ''; }
			echo '			<tr>'."\n";
			echo '				<th><label for="playerSettings_remaining_">'.__('1Pixelout Player shows remaining time', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col">'."\n";
			echo '					<input type="checkbox" name="playerSettings[remaining]" id="playerSettings_remaining_"'.$checked.' value="yes" />'."\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_player_description_cell">'.__('The player shows the remaining track time rather than the ellapsed time.', 'podpress').'</td>'."\n";
			echo '			</tr>'."\n";		
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";
			
			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('OGG/OGV Player Settings', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'.__('choose a player:', 'podpress').'</th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col" style="width:15%;">'."\n";
			if ( !isset($this->settings['cortado_version']) OR 'cortado_default' === $this->settings['cortado_version'] ) {
				$cortado_default = ' checked="checked"';
				$cortado_signed = '';
			} else {
				$cortado_default = '';
				$cortado_signed = ' checked="checked"';
			}
			echo '					<input type="radio" name="cortado_version" id="cortado_default" value="cortado_default"'.$cortado_default.' /> <label for="cortado_default">'.__('default version', 'podpress').'</label><br />'."\n";
			echo '					<input type="radio" name="cortado_version" id="cortado_signed" value="cortado_signed"' .$cortado_signed.' /> <label for="cortado_signed">'.__('signed version', 'podpress').'</label>'."\n";
			echo '				</td>'."\n";
			echo '				<td>'.__('If your media files are stored not under the same domain as your blog and the default player then use the signed version of the <a href="http://www.theora.org/cortado/" target="_blank" title="theora.org: more details about this player">Cortado player</a>. But users will be asked to approve the certificate.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";
			
			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('Video Player', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="disableVideoPreview">'.__('Disable Video Preview', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col">'."\n";
			echo '					<input type="checkbox" name="disableVideoPreview" id="disableVideoPreview" '; if($this->settings['disableVideoPreview']) { echo 'checked="checked"'; } echo " onclick=\"javascript: podPressShowHideRow('videoPreviewImageWrapper'); podPressShowHideRow('videoPreviewPlayerWrapper');\"/>\n";
			echo '				</td>'."\n";
			echo '				<td>'.__('When checked there will be no previews of the video players in all posts (and pages).', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			if($this->settings['disableVideoPreview']){
				$showVideoPreviewOptions = 'style="display: none;"';
			}
			echo '			<tr id="videoPreviewImageWrapper" '.$showVideoPreviewOptions.'>'."\n";
			echo '				<th><label for="videoPreviewImage">'.__('Preview Image URL', 'podpress').':</label></th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<input type="text" id="videoPreviewImage" name="videoPreviewImage" class="podpress_wide_text_field" size="40" value="'.attribute_escape($this->settings['videoPreviewImage']).'" onchange="javascript: document.getElementById(\'podPress_previewImageIMG_2\').src = this.value.replace(/http:/gi, window.location.protocol);" />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr id="videoPreviewPlayerWrapper" '.$showVideoPreviewOptions.'>'."\n";
			echo '				<th><label for="videoPreview">'.__('Preview Image', 'podpress').':</label></th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<div id="podPressPlayerSpace_2"></div>'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr id="videoDefaultPlayerSizeWrapper" '.$showVideoPreviewOptions.'>'."\n";
			echo '				<th><label for="videoDefaultPlayerSize">'.__('Default (preview) player size', 'podpress').':</label></th>'."\n";
			echo '				<td colspan="2">'."\n";
			if (FALSE == isset($this->settings['videoDefaultPlayerSize_x'])) {
				$this->settings['videoDefaultPlayerSize_x'] = 320;
			}
			if (FALSE == isset($this->settings['videoDefaultPlayerSize_y'])) {
				$this->settings['videoDefaultPlayerSize_y'] = 240;
			}
			echo "\t\t\t\t\t".sprintf(__('width %1$s px (default: 320 px)', 'podpress'), '<input type="text" id="videoDefaultPlayerSize_x" name="videoDefaultPlayerSize_x" size="5" value="'.attribute_escape($this->settings['videoDefaultPlayerSize_x']).'" />')."<br />\n";
			echo "\t\t\t\t\t".sprintf(__('height %1$s px (default: 240 px)', 'podpress'), '<input type="text" id="videoDefaultPlayerSize_y" name="videoDefaultPlayerSize_y" size="5" value="'.attribute_escape($this->settings['videoDefaultPlayerSize_y']).'" />')."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '		</table>'."\n";
			echo '<script type="text/javascript"><!--'."\n";
			echo "	document.getElementById('podPressPlayerSpace_2').innerHTML = podPressGenerateVideoPreview(2, '', 320, 240, '".podpress_siteurl_is_ssl($this->settings['videoPreviewImage'])."', true);\n";
			echo "--></script>\n";
			echo '	</fieldset>'."\n";
			
			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('All Players', 'podpress').'</legend>'."\n";
			echo '		<table class="editform podpress_settings_table">'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="contentAutoDisplayPlayer">'.__('Display Player/Preview', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col">'."\n";
			echo '					<input type="checkbox" name="contentAutoDisplayPlayer" id="contentAutoDisplayPlayer" '; if($this->settings['contentAutoDisplayPlayer']) { echo 'checked="checked"'; } echo "/>\n";
			echo '				</td>'."\n";
			echo '				<td class="podpress_player_description_cell">'.__('When checked the player/preview will be visible by default.', 'podpress').'</td>'."\n";
			echo '			</tr> '."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="use_html5_media_tags">'.__('Use HTML5 tags', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col">'."\n";
			$showhtml5playeralways_disabled = ' disabled="disabled"';
			if ( TRUE == isset($this->settings['use_html5_media_tags']) AND FALSE === $this->settings['use_html5_media_tags'] ) {
				echo '					<input type="checkbox" name="use_html5_media_tags" id="use_html5_media_tags" value="yes" onclick="podPress_show_HTML5_player_always(this.id, \'showhtml5playersonpageload\')" />'."\n";
				$this->settings['showhtml5playersonpageload'] = FALSE;
			} else {
				echo '					<input type="checkbox" name="use_html5_media_tags" id="use_html5_media_tags" checked="checked" value="yes" onclick="podPress_show_HTML5_player_always(this.id, \'showhtml5playersonpageload\')" />'."\n";
				$showhtml5playeralways_disabled = '';		
			}
			echo '				</td>'."\n";
			echo '				<td class="podpress_player_description_cell">'.__('If this option is active (recommended) then podPress will embed MP3, OGG and OGV media files by using HTML5 elements, but only if the visitor of your blog uses a web browser which supports this. Otherwise podPress will embed the files as before with Flash-based players (e.g. the 1PixelOut player) or as objects which require other browser plugins.<br />The Listen Wrapper background image for the MP3 players works only in combination with the Flash-based players.<br /><a href="http://en.wikipedia.org/wiki/Comparison_of_layout_engines_%28HTML5_Media%29#Audio_format_support" title="en.Wikipedia: Comparison of layout engines HTML5 (audio)">Some web browsers support the HTML5 &lt;audio&gt;</a> and <a href="http://en.wikipedia.org/wiki/HTML5_video#Browser_support" title="en.Wikipedia: HTML5 (video)">&lt;video&gt;</a> elements. These browsers show their native players instead of <a href="http://en.wikipedia.org/wiki/Adobe_Flash" title="en.Wikipedia: Adobe Flash">Flash</a>-based or other plugin players. Currently (03/2011) the browsers with the <a href="http://en.wikipedia.org/wiki/WebKit" title="en.Wikipedia: WebKit">WebKit</a> (>= 525) engine like Safari and Chrome or the Internet Explorer since version 9 support HTML5 &lt;audio&gt; and MP3. (The Safari browser on iPhones, iPads and iPod Touch supports especially the HTML5 &lt;audio&gt; and &lt;video&gt; elements but not Flash-based players.) Browsers with the <a href="http://en.wikipedia.org/wiki/Gecko_%28layout_engine%29" title="en.Wikipedia: Gecko layout engine">Gecko</a> (>= 1.9.1) or <a href="http://en.wikipedia.org/wiki/Presto_%28layout_engine%29" title="en.Wikipedia: Presto layout engine">Presto</a> (>= 2.5) engine like FireFox and Opera support HTML5 &lt;audio&gt;/&lt;video&gt; elements and OGG/OGV.', 'podpress').'</p>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th><label for="showhtml5playersonpageload">'.__('show HTML5 players always on page load', 'podpress').':</label></th>'."\n";
			echo '				<td class="podpress_player_narrowmiddle_col">'."\n";
			if ( TRUE == isset($this->settings['showhtml5playersonpageload']) AND TRUE === $this->settings['showhtml5playersonpageload'] ) {
				echo '					<input type="checkbox" name="showhtml5playersonpageload" id="showhtml5playersonpageload" checked="checked" value="yes"'.$showhtml5playeralways_disabled.' />'."\n";
			} else {
				echo '					<input type="checkbox" name="showhtml5playersonpageload" id="showhtml5playersonpageload" value="yes"'.$showhtml5playeralways_disabled.' />'."\n";
			}
			echo '				</td>'."\n";
			echo '				<td class="podpress_player_description_cell">'.sprintf(__('Some of the web browsers (e.g. Safari and Chrome except Safari on iPhones, iPads, iPods) which support HTML5 &lt;audio&gt; (and &lt;video&gt;) elements start to download (to buffer) all media files which are embedded with HTML5 elements after the blog page is loaded. This may cause a lot of <a href="http://en.wikipedia.org/wiki/Web_traffic" title="en.Wikipedia: web traffic">traffic</a> which could lead to higher costs. Because of these possible consequences podPress shows by default a Play button and only a click on such a button activates the HTML5 player in those browsers. But if you activate this option then podPress will always show the HTML5 players directly. This option works only in combination with "%1$s". (default: not checked)', 'podpress'), __('Use HTML5 tags', 'podpress')).'</p>'."\n";
			echo '			</tr>'."\n";
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";
			echo '	<input type="hidden" name="podPress_submitted" value="players" />'."\n";
			echo '	<p class="submit"> '."\n";
			echo '		<input class="button-primary" type="submit" name="Submit" value="'.__('Update Options', 'podpress').' &raquo;" /> '."\n";
			echo '	</p> '."\n";
			echo '	</form> '."\n";
			echo '</div>'."\n";
		}

		function settings_players_save() {
			if ( function_exists('check_admin_referer') ) {
				check_admin_referer('podPress_player_settings_nonce');
			}
			if(function_exists('wp_cache_flush')) {
				wp_cache_flush();
			}

			if(isset($_POST['contentAutoDisplayPlayer'])) {
				$this->settings['contentAutoDisplayPlayer'] = true;
			} else {
				$this->settings['contentAutoDisplayPlayer'] = false;
			}

			$this->settings['videoPreviewImage'] = clean_url($_POST['videoPreviewImage'], array('http', 'https'), 'db');

			$this->settings['videoDefaultPlayerSize_x'] = intval(preg_replace('/[^0-9]/', '', $_POST['videoDefaultPlayerSize_x']));
			if ($this->settings['videoDefaultPlayerSize_x'] < 0) {
				$this->settings['videoDefaultPlayerSize_x'] = 320;
			}
			$this->settings['videoDefaultPlayerSize_y'] = intval(preg_replace('/[^0-9]/', '', $_POST['videoDefaultPlayerSize_y']));
			if ($this->settings['videoDefaultPlayerSize_y'] < 0) {
				$this->settings['videoDefaultPlayerSize_y'] = 240;
			}
			
			if(isset($_POST['disableVideoPreview'])) {
				$this->settings['disableVideoPreview'] = true;
			} else {
				$this->settings['disableVideoPreview'] = false;
			}

			if(isset($_POST['mp3Player']) && $_POST['mp3Player'] == '1pixelout') {
				$this->settings['mp3Player'] = '1pixelout';
			} else {
				$this->settings['mp3Player'] = 'podango';
			}
			
			if ( isset($_POST['cortado_version']) AND 'cortado_signed' == $_POST['cortado_version'] ) {
				$this->settings['cortado_version'] = 'cortado_signed';
			} else {
				$this->settings['cortado_version'] = 'cortado_default';
			}
			
		 	$this->settings['player'] = $_POST['playerSettings'];
			
			// ntm: the listenWrapper value is now TRUE or FALSE (bool) 
			if (isset($_POST['playerSettings']['listenWrapper'])) {
				$this->settings['player']['listenWrapper'] = true;
			} else {
				$this->settings['player']['listenWrapper'] = false;
			}
			if (isset($_POST['playerSettings']['checkpolicy'])) {
				$this->settings['player']['checkpolicy'] = 'yes';
			} else {
				$this->settings['player']['checkpolicy'] = 'no';
			}
			if (isset($_POST['playerSettings']['overwriteTitleandArtist'])) {
				$this->settings['player']['overwriteTitleandArtist'] = 'yes';
			} else {
				$this->settings['player']['overwriteTitleandArtist'] = 'no';
			}
			
			if (isset($_POST['playerSettings']['remaining'])) {
				$this->settings['player']['remaining'] = 'yes';
			} else {
				$this->settings['player']['remaining'] = 'no';
			}
			if (isset($_POST['playerSettings']['animation'])) {
				$this->settings['player']['animation'] = 'no';
			} else {
				$this->settings['player']['animation'] = 'yes';
			}
			
			
			if ( TRUE == isset($_POST['use_html5_media_tags']) ) {
				$this->settings['use_html5_media_tags'] = TRUE;
			} else {
				$this->settings['use_html5_media_tags'] = FALSE;
			}
			
			if ( TRUE == isset($_POST['showhtml5playersonpageload']) ) {
				$this->settings['showhtml5playersonpageload'] = TRUE;
			} else {
				$this->settings['showhtml5playersonpageload'] = FALSE;
			}
			
			$result = podPress_update_option('podPress_config', $this->settings);
			if ( FALSE !== $result ) {
				$location = site_url() . '/wp-admin/admin.php?page=podpress/podpress_players.php&updated=true';
			} else {
				$location = site_url() . '/wp-admin/admin.php?page=podpress/podpress_players.php&updated=false';
			}
			header('Location: '.$location);
			exit;
		}
	}
?>