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

		function settings_feed_edit() {
			GLOBAL $wp_version;
			podPress_isAuthorized();
			if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
				echo '<div id="message" class="updated fade"><p>'. __('Settings Saved', 'podpress').'</p></div>';
			} elseif (isset($_GET['updated']) && $_GET['updated'] != 'true') {
				echo '<div id="message" class="error fade"><p>'. __('<strong>Error:</strong> Unable to save the settings', 'podpress').'</p></div>';
			}
			
			$blog_charset = get_bloginfo('charset');
			echo '<div class="wrap">'."\n";
			if ( TRUE == version_compare($wp_version, '2.7', '>=') ) {
				echo '<div id="podpress-icon" class="icon32"><br /></div>';
			}
			if ( TRUE == version_compare($wp_version, '2.8', '>=') ) {
				echo '	<h2>'.__('Feed/iTunes Settings', 'podpress').'</h2>'."\n";
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
				echo '	<h2>'.__('Feed/iTunes Settings', 'podpress').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.mightyseek.com/podpress/#download" target="_new"><img src="http://www.mightyseek.com/podpress_downloads/versioncheck.php?current='.PODPRESS_VERSION.'" alt="'.__('Checking for updates... Failed.', 'podpress').'" border="0" /></a></h2>'."\n";
			}
			
			echo '	<form method="post">'."\n";
			if ( function_exists('wp_nonce_field') ) { // since WP 2.0.4
				wp_nonce_field('podPress_feed_settings_nonce');
			}
			
			podPress_DirectoriesPreview('feed_edit');

			echo '	<fieldset class="options">'."\n";
			echo '		<legend>'.__('Settings for the default Feeds', 'podpress').'</legend>'."\n";
			/*
			echo '		<p class="submit"> '."\n";
			echo '		<input type="submit" name="Submit" value="'.__('Update Options', 'podpress').' &raquo;" /> '."\n";
			echo '		</p> '."\n";
			*/
			echo '		<p>'.sprintf(__('podPress adds automatically additional elements (e.g. <a href="http://www.apple.com/itunes/podcasts/specs.html#rss" target="_blank">iTunes RSS tags</a>) to the default RSS Feeds of your weblog. But it uses the RSS image, the copyright name, the license URL and the News Feed language also for the ATOM Feeds. You can configure these elements in the following section. But podPress adds also new Feeds to your weblog (see <a href="#podpressfeeds">%1$s</a> below).', 'podpress'), __('podPress Feeds', 'podpress')).'</p>'."\n";
			echo '		<h3>'.__('iTunes Settings', 'podpress').'</h3>'."\n";
			echo '		<p>'.__('These settings are for the <a href="http://www.apple.com/itunes/podcasts/specs.html#rss" target="_blank">iTunes RSS tags</a>.', 'podpress').'</p>'."\n";
			echo '		<table class="podpress_feed_gensettings">'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="iTunesFeedID">'.__('iTunes:FeedID', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<input name="iTunes[FeedID]" id="iTunesFeedID" type="text" value="'.$this->settings['iTunes']['FeedID'].'" size="10" />';
			//~ //echo '					<input type="button" name="Ping_iTunes_update" value="'.__('Ping iTunes Update', 'podpress').'" onclick="javascript: if(document.getElementById(\'iTunesFeedID\').value != \'\') { window.open(\'https://phobos.apple.com/WebObjects/MZFinance.woa/wa/pingPodcast?id=\'+document.getElementById(\'iTunesFeedID\').value); }"/>'."\n";
			//~ if ( !empty($this->settings['iTunes']['FeedID'] ) ) {
				//~ echo '					<label for="podpress_its_preview">'.__('iTunes Store Preview', 'podpress').'</label> <a href="" class="podpress_image_preview_link" title="'.__('open the iTunes Store page of your Feed in a preview frame', 'podpress').'" onclick="podPress_jQuery(\'#podpress_its_preview\').dialog(\'open\'); return false;">'.__('open the preview frame', 'podpress').'</a>'."\n";
				//~ echo '					<div id="podpress_its_preview" title="'.__('iTunes Store Preview', 'podpress').'" class="podpress_its_preview">'."\n";
				//~ echo '						<iframe src="" style="width:100%; height:80%;">'."\n";
				//~ echo '							<img id="podpress_its_preview_loader_img" src="'.PODPRESS_URL.'/images/ajax-loader.gif">'."\n";
				//~ echo '						</iframe>'."\n";
				//~ echo '					</div>'."\n";
			//~ }
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="iTunesNewFeedURL">'.__('iTunes:New-Feed-Url', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="iTunes[new-feed-url]" id="iTunesNewFeedURL">'."\n";
			echo '						<option value="Disable" '; if($this->settings['iTunes']['new-feed-url'] != 'Enable') { echo 'selected="selected"'; } echo '>'.__('Disable', 'podpress').'</option>'."\n";
			echo '						<option value="Enable" '; if($this->settings['iTunes']['new-feed-url'] == 'Enable') { echo 'selected="selected"'; } echo '>'.__('Enable', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td>'."\n";
			echo '					'.__('If you want to change the URL of your podcast feed which you have used in the iTunes Store then change the "Podcast Feed URL" and set this option to "Enable" until the iTunes Store recognizes the new URL. This may take several days. "Enable" will add the <code>&lt;itunes:new-feed-url&gt;</code> tag to the RSS feeds and set the "Podcast Feed URL" as the new URL. For further information about "<a href="http://www.apple.com/itunes/podcasts/specs.html#changing" title="iTunes Podcasting Resources: Changing Your Feed URL" target="_blank">Changing Your Feed URL</a>" read on in the <a href="http://www.apple.com/itunes/podcasts/specs.html" target="_blank" title="iTunes Podcasting Resources: Making a Podcast">iTunes Podcasting Resources</a>.', 'podpress');
			echo '					<p><label for="podcastFeedURL"><strong>'.__('the new Feed URL', 'podpress').'</strong></label>';
			echo '					<br/>';
			echo '					<input type="text" id="podcastFeedURL" class="podpress_wide_text_field" name="podcastFeedURL" size="40" value="'.attribute_escape($this->settings['podcastFeedURL']).'" onchange="podPress_updateFeedSettings();" /><br />'.__('The URL of your Podcast Feed. If you want to register your podcast at the iTunes Store or if your podcast is already listed there then this input field should contain the same URL as in the iTunes Store settings. If you want change the URL at the iTunes Store then please read first the help text of the iTunes:New-Feed-Url option.', 'podpress');
			echo '					<br /><input type="button" value="'.__('Validate your Feed','podpress').'" onclick="javascript: if(document.getElementById(\'podcastFeedURL\').value != \'\') { window.open(\'http://www.feedvalidator.org/check.cgi?url=\'+document.getElementById(\'podcastFeedURL\').value); }"/>'."\n";
			echo '					</p>';
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="iTunesSummary">'.__('iTunes:Summary', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<textarea id="iTunesSummary" name="iTunes[summary]" class="podpress_wide_text_field" rows="4" cols="40" onchange="podPress_updateFeedSettings();">'.stripslashes($this->settings['iTunes']['summary']).'</textarea>';
			echo '					<br />'.__('Used as iTunes description.', 'podpress')."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="iTunesImage">'.__('iTunes:Image', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					'.__('The iTunes image should be a square image with <a href="http://www.apple.com/itunes/podcasts/specs.html#image" target="_blank">at least 600 x 600 pixels</a> as Apple writes in "<a href="http://www.apple.com/itunes/podcasts/specs.html" target="_blank">Making a Podcast</a>" of their own Podcasting Resources. iTunes supports JPEG and PNG images (the file name extensions should ".jpg" or ".png").', 'podpress')."\n";
			echo '					<br/>';
			echo '					<input type="text" id="iTunesImage" name="iTunes[image]" class="podpress_wide_text_field" value="'.attribute_escape($this->settings['iTunes']['image']).'" size="40" onchange="podPress_updateFeedSettings();"/>'."\n";
			echo '					<br />';
			echo '					<img id="iTunesImagePreview" style="width:300px; height:300px;" alt="Podcast Image - Big" src="" />'."<br />\n";
			echo '					<em>'.__('(This image is only a preview which is limited to 300 x 300 pixels.) ', 'podpress').'</em>';
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="iTunesAuthor">'.__('iTunes:Author/Owner', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<input type="text" name="iTunes[author]" id="iTunesAuthor" class="podpress_wide_text_field" value="'.attribute_escape(stripslashes($this->settings['iTunes']['author'])).'" size="40" onchange="podPress_updateFeedSettings();"/>';
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="iTunesSubtitle">'.__('iTunes:Subtitle', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<textarea name="iTunes[subtitle]" id="iTunesSubtitle" class="podpress_wide_text_field" rows="4" cols="40">'.stripslashes($this->settings['iTunes']['subtitle']).'</textarea>';
			echo '					<br/>'.__('Used as default Podcast Episode Title (255 characters)', 'podpress')."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="iTunesKeywords">'.__('iTunes:Keywords', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<textarea name="iTunes[keywords]" id="iTunesKeywords" class="podpress_wide_text_field" rows="4" cols="40">'.stripslashes($this->settings['iTunes']['keywords']).'</textarea>';
			echo '					<br/>('.__('a list of max. 12 comma separated words', 'podpress').', '.__('max 8', 'podpress').')';
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="iTunesCategory_0">'.__('iTunes:Categories', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<select id="iTunesCategory_0" name="iTunes[category][0]" onchange="podPress_updateFeedSettings();">'."\n";
			echo '						<optgroup label="'.__('Select Primary', 'podpress').'">'."\n";
			podPress_itunesCategoryOptions(stripslashes($this->settings['iTunes']['category'][0]));
			echo '						</optgroup>'."\n";
			echo '					</select><br />'."\n";
			echo '					<select name="iTunes[category][1]">'."\n";
			echo '						<optgroup label="'.__('Select Second', 'podpress').'">'."\n";
			podPress_itunesCategoryOptions(stripslashes($this->settings['iTunes']['category'][1]));
			echo '						</optgroup>'."\n";
			echo '					</select><br />'."\n";
			echo '					<select name="iTunes[category][2]">'."\n";
			echo '						<optgroup label="'.__('Select Third', 'podpress').'">'."\n";
			podPress_itunesCategoryOptions(stripslashes($this->settings['iTunes']['category'][2]));
			echo '						</optgroup>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="iTunesExplicit">'.__('iTunes:Explicit', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="iTunes[explicit]" id="iTunesExplicit">'."\n";
			echo '						<option value="No" '; if($this->settings['iTunes']['explicit'] == 'No') { echo 'selected="selected"'; } echo '>'.__('No', 'podpress').'</option>'."\n";
			echo '						<option value="Yes" '; if($this->settings['iTunes']['explicit'] == 'Yes') { echo 'selected="selected"'; } echo '>'.__('Yes', 'podpress').'</option>'."\n";
			echo '						<option value="Clean" '; if($this->settings['iTunes']['explicit'] == 'Clean') { echo 'selected="selected"'; } echo '>'.__('Clean', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td>'."\n";
			echo '					'.__('Setting to indicate (in iTunes) whether or not your podcast contains explicit language or content which is not suitable for non-adult persons.', 'podpress')."\n";
			echo '					<br/>'.__('"No" (default) - no indicator will show up', 'podpress')."\n";
			echo '					<br/>'.__('"Yes" - an "EXPLICIT" parental advisory graphic will appear next to your podcast artwork or name in iTunes', 'podpress')."\n";
			echo '					<br/>'.__('"Clean" - means that you are sure that no explicit language or adult content is included any of the episodes, and a "CLEAN" graphic will appear', 'podpress')."\n";
			echo '					<p>'.__('You have also the possibility to adjust this option for each post or page with at least one podcast episode (in the post/page editor).', 'podpress').'</p>'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="iTunesBlock">'.__('iTunes:Block', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td>'."\n";
			echo '					<select name="iTunes[block]" id="iTunesBlock">'."\n";
			echo '						<option value="No" '; if($this->settings['iTunes']['block'] != 'Yes') { echo 'selected="selected"'; } echo '>'.__('No', 'podpress').'</option>'."\n";
			echo '						<option value="Yes" '; if($this->settings['iTunes']['block'] == 'Yes') { echo 'selected="selected"'; } echo '>'.__('Yes', 'podpress').'</option>'."\n";
			echo '					</select>'."\n";
			echo '				</td>'."\n";
			echo '				<td>'."\n";
			echo '					'.__('Use this if you are no longer creating a podcast and you want it removed from iTunes.', 'podpress')."\n";
			echo '					<br/>'.__('"No" (default) - the podcast appears in the iTunes Podcast directory', 'podpress')."\n";
			echo '					<br/>'.__('"Yes" - prevent the entire podcast from appearing in the iTunes Podcast directory', 'podpress')."\n";
			echo '					<p>'.__('You can also use such an option for each of your podcast episodes (in the post/page editor).', 'podpress').'</p>'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '		</table>'."\n";
		
			if ( function_exists('get_admin_url') ) {
				$adminurl = get_admin_url(); // since WP 3.0
			} elseif ( function_exists('admin_url') ) {
				$adminurl = admin_url(); // since WP 2.6
			} else {
				$adminurl = get_option( 'siteurl' ) . '/wp-admin';
			}
			
			echo '		<h3>'.__('General Feed Settings', 'podpress').'</h3>'."\n";
			echo '		<p>'.__('These settings are blog settings.', 'podpress').'</p>'."\n";
			echo '		<table class="podpress_feed_gensettings">'."\n";
			echo '			<tr>'."\n";
			echo '				<td class="podpress_th_full_width" colspan="3">'."\n";
			echo '					'.sprintf(__('You can modify the following %2$s options on the %1$s page of this blog.', 'podpress'), '<a href="'.trailingslashit($adminurl).'options-general.php'.'">'.__('General Settings').'</a>', __('three', 'podpress'));
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="blogname">'.__('Blog/Podcast title', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<em class="podpress_static_feed_settings" id="blogname">'.stripslashes(get_option('blogname')).'</em><br />'.__('Used as the Feed title', 'podpress');
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="blogdescription">'.__('Blog Tagline', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<em class="podpress_static_feed_settings" id="blogdescription">'.stripslashes(get_option('blogdescription')).'</em><br />'.__('used as the Feed description', 'podpress')."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="admin_email">'.__('Owner E-mail address', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<em class="podpress_static_feed_settings">'.stripslashes(get_option('admin_email')).'</em><br />'.__('used besides the itunes:author value in the &lt;managingEditor&gt; and the &lt;webMaster&gt; elements of the RSS Feeds', 'podpress');
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<td class="podpress_th_full_width" colspan="3">'."\n";
			echo '					'.sprintf(__('You can modify the following %2$s options on the %1$s page of this blog.', 'podpress'), '<a href="'.trailingslashit($adminurl).'options-reading.php'.'">'.__('Reading Settings').'</a>', __('two', 'podpress'));
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="posts_per_rss">'.__('Syndication feeds show the most recent', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<em class="podpress_static_feed_settings">'.get_option('posts_per_rss').'</em> '.__('posts', 'podpress'); 
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="blog_charset">'.__('Encoding for pages and feeds').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<em class="podpress_static_feed_settings">'.$blog_charset.'</em><br />'.__('The <a href="http://codex.wordpress.org/Glossary#Character_set">character encoding</a> of your site  (UTF-8 is <a href="http://www.apple.com/itunes/podcasts/specs.html#encoding" target="_blank" title="iTunes Podcast Resources - Making a Podcast">recommended</a>)', 'podpress');
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="rss_ttl">'.__('TTL', 'podpress').' ('.__('time-to-live', 'podpress').')</label>';
			echo '				</th>'."\n";
			echo '				<td>'."\n";
			$data['rss_ttl'] = get_option('rss_ttl');
			if(!empty($data['rss_ttl']) && $data['rss_ttl'] < 1440) {
				$data['rss_ttl'] = 1440;
			}
			echo '					<input name="rss_ttl" id="rss_ttl" type="text" value="'; if($data['rss_ttl']) { echo $data['rss_ttl']; } else { echo '1440'; } echo '" size="4" />';
			echo '				</td>'."\n";
			echo '				<td>'."\n";
			echo '					'.__('minutes', 'podpress').' - '.__('Minimum is 24 hours which is 1440 minutes.', 'podpress').' <a href="http://cyber.law.harvard.edu/rss/rss.html#ltttlgtSubelementOfLtchannelgt" title="RSS 2.0 Specification - TTL">'.__('More about TTL ...', 'podpress').'</a>'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="rss_image">'.__('Blog/RSS Image (144 x 144 pixels)', 'podpress').'</label>'."\n";
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<input type="text" id="rss_image" name="rss_image" class="podpress_wide_text_field" value="'.attribute_escape(get_option('rss_image')).'" size="40" onchange="podPress_updateFeedSettings();"/>'."\n";
			echo '					<br />';
			echo '					<img id="rss_imagePreview" style="width:144px; height:144px;" alt="Podcast Image - Small" src="" />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="rss_language">'.__('Language of the News Feed content', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			$rss_language = get_option('rss_language');
			echo '					<select id="rss_language" name="rss_language" onchange="podPress_updateFeedSettings();">'."\n";
			echo '						<optgroup label="'.__('Select Language', 'podpress').'">'."\n";
			podPress_itunesLanguageOptions($rss_language);
			echo '						</optgroup>'."\n";
			echo '					</select>'."\n";
			//~ echo '<br /><em class="podpress_error">'.sprintf(__('Changes here will affect %1$s!', 'podpress'),__('the language of the main feeds of this blog', 'podpress')).'</em> <em>'.__('(This select box is not the only but probably the most comfortable way to change this option. So change it back if you do not want to use this plugin anymore.)', 'podpress').'</em>';
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '		</table>'."\n";
			
			
			echo '		<h3>'.__('Further Feed Settings', 'podpress').'</h3>'."\n";
			echo '		<table class="podpress_feed_gensettings">'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="rss_category">'.__('RSS Category', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<input type="text" name="rss_category" id="rss_category" class="podpress_wide_text_field" value="'.attribute_escape(stripslashes($this->settings['rss_category'])).'" size="45" />'."\n";
			echo '					<br />'.__('A category for your RSS feeds. (This is for everyone except iTunes).', 'podpress')."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="rss_copyright">'.__('Feed Copyright / license name', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<input type="text" name="rss_copyright" id="rss_copyright" class="podpress_wide_text_field" value="'.attribute_escape(stripslashes($this->settings['rss_copyright'])).'" size="65" />'."\n";
			echo '					<br />'.__('Enter the copyright string resp. the license name. For example: Copyright &#169 by Jon Doe, 2009 OR <a href="http://creativecommons.org/licenses/by-nc-sa/2.5/" target="_blank">CreativeCommons Attribution-Noncommercial-Share Alike 2.5</a>', 'podpress')."\n";
			echo '					<br /><br />'."\n";
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			echo '			<tr>'."\n";
			echo '				<th>'."\n";
			echo '					<label for="rss_license_url">'.__('URL to the full Copyright / license text', 'podpress').'</label>';
			echo '				</th>'."\n";
			echo '				<td colspan="2">'."\n";
			echo '					<input type="text" name="rss_license_url" id="rss_license_url" class="podpress_wide_text_field" class="podpress_wide_text_input_field" value="'.attribute_escape($this->settings['rss_license_url']).'" size="65" />'."\n";
			echo '					<br />'.__('If you use a special license like a <a href="http://creativecommons.org/licenses" target="_blank" title="Creative Commons">Creative Commons</a> License for your news feeds then enter the complete URL (e.g. <a href="http://creativecommons.org/licenses/by-nc-sa/2.5/" target="_blank">http://creativecommons.org/licenses/by-nc-sa/2.5/</a>) to the full text of this particular license here.', 'podpress')."<br />\n";
			echo '					<p>'.__('Notice: You can set post specific license URLs and names by defining two custom fields per post. One with the name <strong>podcast_episode_license_name</strong> and one custom field with the name <strong>podcast_episode_license_url</strong>. If you want to set post specific values then it is necessary to define at least the custom field with the URL. If the license name is not defined then the name will be the URL.', 'podpress').'</p>';			
			echo '				</td>'."\n";
			echo '			</tr>'."\n";
			
			//~ echo '			<tr>'."\n";
			//~ echo '				<th>'."\n";
			//~ echo '					<label for="protectFeed">'.__('Aggressively Protect the news feeds', 'podpress').'</label>';
			//~ echo '				</th>'."\n";
			//~ echo '				<td>'."\n";
			//~ echo '					<select name="protectFeed" id="protectFeed">'."\n";
			//~ echo '						<option value="No" '; if($this->settings['protectFeed'] != 'Yes') { echo 'selected="selected"'; } echo '>'.__('No', 'podpress').'</option>'."\n";
			//~ echo '						<option value="Yes" '; if($this->settings['protectFeed'] == 'Yes') { echo 'selected="selected"'; } echo '>'.__('Yes', 'podpress').'</option>'."\n";
			//~ echo '					</select>'."\n";
			//~ echo '				</td>'."\n";
			//~ echo '				<td>'."\n";
			//~ echo '					'.__('"No" (default) will convert only ampersand, less-than, greater-than, apostrophe and quotation signs to their numeric character references.', 'podpress')."\n";
			//~ echo '					<br/>'.__('"Yes" will convert any invalid characters to their numeric character references in the feeds.', 'podpress')."\n";
			//~ echo '				</td>'."\n";
			//~ echo '			</tr>'."\n";
			// this section is deactivated since 8.8.5 and since 8.8.10.8 the upgrade_class will remove the value during the upgrade process. (the only encoded content section is the <description> on <item> level and podPress does touch this value)
			//~ echo '			<tr>'."\n";
			//~ echo '				<th>'."\n";
			//~ echo '					<label for="rss_showlinks">'.__('Show Download Links in RSS Encoded Content', 'podpress').'</label>';
			//~ echo '				</th>'."\n";
			//~ echo '				<td>'."\n";
			//~ echo '					<select name="rss_showlinks" id="rss_showlinks">'."\n";
			//~ echo '						<option value="yes" '; if($this->settings['rss_showlinks'] == 'yes') { echo 'selected="selected"'; } echo '>'.__('Yes', 'podpress').'</option>'."\n";
			//~ echo '						<option value="no" '; if($this->settings['rss_showlinks'] != 'yes') { echo 'selected="selected"'; }  echo '>'.__('No', 'podpress').'</option>'."\n";
			//~ echo '					</select>'."\n";
			//~ echo '				</td>'."\n";
			//~ echo '				<td>'."\n";
			//~ echo '					'.__('Yes will put download links in the RSS encoded content. That means users can download from any site displaying the link.', 'podpress')."\n";
			//~ echo '				</td>'."\n";
			//~ echo '			</tr>'."\n";
			echo '		</table>'."\n";
			echo '	</fieldset>'."\n";
			
			echo '	<fieldset class="options">'."\n";
			echo '		<a name="podpressfeeds" id="podpressfeeds"></a><legend>'.__('podPress Feeds', 'podpress').'</legend>'."\n";
			

			$permalinksettingsurl = trailingslashit($adminurl).'options-permalink.php';
			$widgetsettingsurl = trailingslashit($adminurl).'widgets.php';
			$generalsettingspodpressurl = trailingslashit($adminurl).'admin.php?page=podpress/podpress_general.php';
			
			echo '		<p>'.sprintf(__('podPress is capable of creating additional Feeds for your blog. These Feeds are RSS or ATOM Feeds. The content of such Feed maybe consist of all posts, posts with podPress attachment, posts of one more categories or posts with podPress attachments of certain file types. For instance you can create a Feed which contains only posts with audio files and one which contains only posts with video files. Furthermore the following section contains diverse options to customize these additional Feeds.<br />It is also possible to activate or deactivate these Feeds separately.<br /><strong>It is necessary to (re-)save the <a href="%1$s">Permalink structure</a> and the podPress Feed Buttons <a href="%2$s">widget settings</a> after the slug name of one of these Feeds has been modified OR after you have (de-)activated one of these Feeds.</strong>', 'podpress'), $permalinksettingsurl, $widgetsettingsurl).'</p>'."\n";			
			echo '		<p class="podpress_notice">'.__('<strong>Notice:</strong> After an upgrade from podPress v8.8.6.3 or older version to the current version, you need to control the following forms (and fill out empty fields eventually) of the Feeds you like to keep on using. You may copy and paste the meta information from the input fields above. But you could also use the new section below to customize these information for each Feed. The additional Feeds like the one with the slug name "podcast" do not automatically share those meta information any longer with the default Feeds of the blog.', 'podpress').'</p>'."\n";			
			echo '		<div id="podpress_accordion">'."\n";
			$filetypes = podPress_filetypes();
			$allcategories = get_categories( Array( 'orderby' => 'name', 'order' => 'ASC' ) );
			$i=0;
			if ( is_array($this->settings['podpress_feeds']) ) {
				foreach ($this->settings['podpress_feeds'] as $feed) {
					$selected_types = $feed['FileTypes'];
					if ( FALSE === is_array($feed['FileTypes']) ) {
						$selected_types = array();
					}
					$selected_categories = $feed['inclCategories'];
					if ( FALSE === is_array($selected_categories) ) {
						$selected_categories = array();
					}
					if ( FALSE == empty($feed['slug']) ) {
						$ftitle = $feed['slug'];
					} else {
						$ftitle = $i;
					}
					echo '			<h4><a href="">'.__('Feed', 'podpress').' '.$ftitle.'</a></h4>'."\n";
					echo '			<div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					if (TRUE === $feed['use']) {
						echo '					<input type="checkbox" name="podpress_feeds['.$i.'][use]" id="podpress_feed_'.$i.'_use" value="yes" checked="checked" /> <label for="podpress_feed_'.$i.'_use">'.__('Activate Feed', 'podpress').'</label>'."\n";
					} else {
						echo '					<input type="checkbox" name="podpress_feeds['.$i.'][use]" id="podpress_feed_'.$i.'_use" value="yes" /> <label for="podpress_feed_'.$i.'_use">'.__('Activate Feed', 'podpress').'</label>'."\n";
					}
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					if ( FALSE == defined('PODPRESS_DEACTIVATE_PREMIUM') OR FALSE === constant('PODPRESS_DEACTIVATE_PREMIUM') ) {
						if (TRUE === $feed['premium'] ) {
							echo '					<input type="checkbox" name="podpress_feeds['.$i.'][premium]" id="podpress_feed_'.$i.'_premium" value="yes" checked="checked" /> <label for="podpress_feed_'.$i.'_premium">'.__('Premium Feed', 'podpress').'</label>'."\n";
						} else {
							echo '					<input type="checkbox" name="podpress_feeds['.$i.'][premium]" id="podpress_feed_'.$i.'_premium" value="yes" /> <label for="podpress_feed_'.$i.'_premium">'.__('Premium Feed', 'podpress').'</label>'."\n";
						}
						if ( FALSE == isset($this->settings['enablePremiumContent']) OR TRUE !== $this->settings['enablePremiumContent'] ) {
							echo '					<br /><span class="podpress_description">'.sprintf(__('If this Feed should be a Premium Feed then you need to activate the Premium Content feature at the <a href="%1$s">general settings page of podPress</a>.', 'podpress'), $generalsettingspodpressurl).'</span>';
						}
					}
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_name">'.__('Feed Name', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][name]" id="podpress_feed_'.$i.'_name" class="podpress_feeds_text_field" value="'.attribute_escape(stripslashes($feed['name'])).'" /><br /><span class="podpress_description">'.__('(Leave the Feed Name field empty to delete this Feeds settings.)', 'podpress').'</span><br />'."\n";
					switch ( $feed['feedtitle'] ) {
						default:
						case 'append' :
							$selected_append = ' selected="selected"';
							$selected_blognameastitle = '';
							$selected_feednameastitle = '';
						break;
						case 'blognameastitle' :
							$selected_append = '';
							$selected_blognameastitle = ' selected="selected"';
							$selected_feednameastitle = '';
						break;
						case 'feednameastitle' :
							$selected_append = '';
							$selected_blognameastitle = '';
							$selected_feednameastitle = ' selected="selected"';
						break;
					}
					echo '					<label for="podpress_feed_'.$i.'_feedtitle">'.__('How-to build the Feed title:', 'podpress').'</label> ';
					echo '					<select  id="podpress_feed_'.$i.'_feedtitle" name="podpress_feeds['.$i.'][feedtitle]">'."\n";
					echo '						<option value="append"'.$selected_append.'>'.__('use the blog name and append the Feed Name', 'podpress').'</option>'."\n";
					echo '						<option value="blognameastitle"'.$selected_blognameastitle.'>'.__('use only the blog name as the title', 'podpress').'</option>'."\n";
					echo '						<option value="feednameastitle"'.$selected_feednameastitle.'>'.__('use the Feed Name as the title', 'podpress').'</option>'."\n";
					echo '					</select>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_slug">'.__('Slug Name', 'podpress').'</label> <span class="podpress_description">'.__('- the name for this Feed in the URL', 'podpress').'</span><br /><input type="text" name="podpress_feeds['.$i.'][slug]" id="podpress_feed_'.$i.'_slug" class="podpress_feeds_text_field" value="'.attribute_escape(stripslashes($feed['slug'])).'" /><br /><span class="podpress_description">'.__('Please, use only these characters: a-z, 0-9, underscore and hiphen.', 'podpress').'</span>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_fullwidth_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_feedurl">'.__('current Feed URL', 'podpress').'</label><br /><span id="podpress_feed_'.$i.'_feedurl">'.get_feed_link($feed['slug']).'</span>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_subtitle">'.__('iTunes:Subtitle', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][subtitle]" id="podpress_feed_'.$i.'_subtitle" class="podpress_feeds_text_field" value="'.attribute_escape(stripslashes($feed['subtitle'])).'" />'."\n";
					echo '					<br /><br />'."\n";
					echo '					<label for="podpress_feed_'.$i.'_rss_category">'.__('RSS Category', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][rss_category]" id="podpress_feed_'.$i.'_rss_category" class="podpress_feeds_text_field" value="'.attribute_escape(stripslashes($feed['rss_category'])).'" size="40" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_descr">'.__('Description (RSS) / Subtitle (ATOM) / iTunes:Summary', 'podpress').'</label><br /><textarea name="podpress_feeds['.$i.'][descr]" id="podpress_feed_'.$i.'_descr" class="podpress_feeds_text_field" rows="4" cols="40">'.stripslashes(stripslashes($feed['descr'])).'</textarea>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_iTunesCategory_0">'.__('iTunes:Categories', 'podpress').'</label><br />'."\n";
					echo '					<select id="podpress_feed_'.$i.'_iTunesCategory_0" name="podpress_feeds['.$i.'][itunes-category][0]">'."\n";
					echo '						<optgroup label="'.__('Select Primary', 'podpress').'">'."\n";
					podPress_itunesCategoryOptions(stripslashes($feed['itunes-category'][0]));
					echo '						</optgroup>'."\n";
					echo '					</select><br />'."\n";
					echo '					<select name="podpress_feeds['.$i.'][itunes-category][1]">'."\n";
					echo '						<optgroup label="'.__('Select Second', 'podpress').'">'."\n";
					podPress_itunesCategoryOptions(stripslashes($feed['itunes-category'][1]));
					echo '						</optgroup>'."\n";
					echo '					</select><br />'."\n";
					echo '					<select name="podpress_feeds['.$i.'][itunes-category][2]">'."\n";
					echo '						<optgroup label="'.__('Select Third', 'podpress').'">'."\n";
					podPress_itunesCategoryOptions(stripslashes($feed['itunes-category'][2]));
					echo '						</optgroup>'."\n";
					echo '					</select>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_iTunesKeywords">'.__('iTunes:Keywords', 'podpress').'</label><br /><textarea name="podpress_feeds['.$i.'][itunes-keywords]" id="podpress_feed_'.$i.'_iTunesKeywords" class="podpress_feeds_text_field" rows="4" cols="40">'.stripslashes(stripslashes($feed['itunes-keywords'])).'</textarea><br /><span class="podpress_description">'.__('a list of max. 12 comma separated words', 'podpress').'</span>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_iTunesAuthor">'.__('iTunes:Author/Owner', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][itunes-author]" id="podpress_feed_'.$i.'_iTunesAuthor" class="podpress_feeds_text_field" value="'.attribute_escape(stripslashes($feed['itunes-author'])).'" size="40" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_email">'.__('Owner E-mail address', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][email]" id="podpress_feed_'.$i.'_email" class="podpress_feeds_text_field" value="'.attribute_escape(stripslashes($feed['email'])).'" size="40" />'."\n";
					echo '				</div>'."\n";
					if ( FALSE == isset($feed['itunes-image']) OR empty($feed['itunes-image']) ) {
						$feed['itunes-image'] = $this->settings['iTunes']['image'];
					}
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_iTunesImage">'.__('iTunes:Image', 'podpress').'</label> <a href="" class="podpress_image_preview_link" title="'.__('Feed', 'podpress').' '.$feed['slug'].' - '.__('iTunes:Image', 'podpress').'" onclick="podPress_jQuery(\'#podpress-itunesimage-preview-'.$i.'\').dialog(\'open\'); return false;">'.__('Preview', 'podpress').'</a><br /><input type="text" name="podpress_feeds['.$i.'][itunes-image]" id="podpress_feed_'.$i.'_iTunesImage" class="podpress_feeds_text_field" value="'.attribute_escape($feed['itunes-image']).'" size="40" />'."\n";
					echo '					<div id="podpress-itunesimage-preview-'.$i.'" title="'.attribute_escape(__('Feed', 'podpress').' '.$feed['slug'].' - '.__('iTunes:Image', 'podpress')).'" class="podpress_itunesimage_preview">'."\n";
					echo '						<img src="'.$feed['itunes-image'].'" />'."\n";
					echo '					</div>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_copyright">'.__('Feed Copyright / license name', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][copyright]" id="podpress_feed_'.$i.'_copyright" class="podpress_feeds_text_field" value="'.attribute_escape(stripslashes($feed['copyright'])).'" size="40" />'."\n";
					echo '				</div>'."\n";
					if ( FALSE == isset($feed['rss_image']) OR empty($feed['rss_image']) ) {
						$feed['rss_image'] = get_option('rss_image');
					}
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_rss_image">'.__('RSS Image (144 x 144 pixels)', 'podpress').'</label> <a href="" class="podpress_image_preview_link" title="'.__('Feed', 'podpress').' '.$feed['slug'].' - '.__('RSS Image', 'podpress').'" onclick="podPress_jQuery(\'#podpress-rssimage-preview-'.$i.'\').dialog(\'open\'); return false;">'.__('Preview', 'podpress').'</a><br /><input type="text" name="podpress_feeds['.$i.'][rss_image]" id="podpress_feed_'.$i.'_rss_image" class="podpress_feeds_text_field" value="'.attribute_escape($feed['rss_image']).'" size="40" />'."\n";
					echo '					<div id="podpress-rssimage-preview-'.$i.'" title="'.attribute_escape(__('Feed', 'podpress').' '.$feed['slug'].' - '.__('RSS Image', 'podpress')).'" class="podpress_rssimage_preview">'."\n";
					echo '						<img src="'.$feed['rss_image'].'" />'."\n";
					echo '					</div>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_license_url">'.__('URL to the full Copyright / license text', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][license_url]" id="podpress_feed_'.$i.'_license_url" class="podpress_feeds_text_field" value="'.attribute_escape($feed['license_url']).'" size="40" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_language">'.__('Language of this Feed', 'podpress').'</label><br />'."\n";
					if ( FALSE == isset($feed['language']) OR empty($feed['language']) ) {
						$feed['language'] = $rss_language;
					}
					echo '					<select id="podpress_feed_'.$i.'_language" name="podpress_feeds['.$i.'][language]">'."\n";
					echo '						<optgroup label="'.__('Select a language', 'podpress').'">'."\n";
					podPress_itunesLanguageOptions($feed['language']);
					echo '						</optgroup>'."\n";
					echo '					</select>'."\n";
					echo '					<br />'."\n";
					echo '					<br />'."\n";
					if ( FALSE == isset($feed['charset']) OR empty($feed['charset']) ) {
						$feed['charset'] = $blog_charset;
					}
					echo '					<label for="podpress_feed_'.$i.'_blog_charset">'.__('Encoding for this Feed').'</label><br /><input type="text" name="podpress_feeds['.$i.'][charset]" id="podpress_feed_'.$i.'_blog_charset" size="20" value="'.attribute_escape($feed['charset']).'" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_filetypefilter">'.__('File Type Filter', 'podpress').'</label><br />'."\n";
					echo '					<select id="podpress_feed_'.$i.'_filetypefilter" name="podpress_feeds['.$i.'][FileTypes][]" size="5" multiple="multiple" class="podpress_filetypefilter_select">'."\n";
					echo '					<optgroup label="'.attribute_escape(__('Select file types', 'podpress')).'">'."\n";
					foreach ( $filetypes as $key => $value ) {
						if ( TRUE == in_array($key, $selected_types) ) {
							$selected = ' selected="selected"';
						} else {
							$selected = '';
						}
						if ($key !== 'audio_mp4') {
							echo '						<option value="'.$key.'"'.$selected.'>'.$value.'</option>'."\n";
						}
					}
					echo '					</optgroup>'."\n";
					echo '					</select>'."\n";
					echo '					<span class="podpress_description">'.__('Select one or more file types to include in this Feed only posts which have attached media files of these file types. (This filter bypasses the "Included in:" selection.)', 'podpress').'</span>';
					echo '					<br /><span class="podpress_description">'.__('Hold the key [SHIFT] or [CTRL] and use the left mouse button to select more than one value.<br />Hold [CTRL] and use the left mouse button to deselect values.', 'podpress').'</span>';
					echo '				</div>'."\n";
					Switch ( $feed['itunes-explicit'] ) {
						default:
						case 'No' :
							$selected_no = ' selected="selected"';
							$selected_yes = '';
							$selected_clean = '';
						break;
						case 'Yes' :
							$selected_no = '';
							$selected_yes = ' selected="selected"';
							$selected_clean = '';
						break;
						case 'Clean' :
							$selected_no = '';
							$selected_yes = '';
							$selected_clean = ' selected="selected"';
						break;
					}
					echo '				<div class="podpress_feed_settings_left_col">';
					echo '					<label for="podpress_feed_'.$i.'_iTunesExplicit">'.__('iTunes:Explicit', 'podpress').'</label><br/>';
					echo '					<select  id="podpress_feed_'.$i.'_iTunesExplicit" name="podpress_feeds['.$i.'][itunes-explicit]">'."\n";
					echo '						<option value="No"'.$selected_no.'>'.__('No', 'podpress').'</option>'."\n";
					echo '						<option value="Yes"'.$selected_yes.'>'.__('Yes', 'podpress').'</option>'."\n";
					echo '						<option value="Clean"'.$selected_clean.'>'.__('Clean', 'podpress').'</option>'."\n";
					echo '					</select>'."\n";
					echo '					<br />'."\n";
					echo '					<br />'."\n";
					echo '					<label for="podpress_feed_'.$i.'_iTunesBlock">'.__('iTunes:Block', 'podpress').'</label><br />'."\n";
					if ( 'Yes' == $feed['itunes-block'] ) {
						$no_selected = '';
						$yes_selected = ' selected="selected"';
					} else {
						$no_selected = ' selected="selected"';
						$yes_selected = '';
					}
					echo '					<select name="podpress_feeds['.$i.'][itunes-block]" id="podpress_feed_'.$i.'_iTunesBlock">'."\n";
					echo '						<option value="No"'.$no_selected.'>'.__('No', 'podpress').'</option>'."\n";
					echo '						<option value="Yes"'.$yes_selected.'>'.__('Yes', 'podpress').'</option>'."\n";
					echo '					</select>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">';
					echo '					<label for="podpress_feed_'.$i.'_categoryfilter">'.__('Category Filter', 'podpress').'</label><br />'."\n";
					echo '					<select id="podpress_feed_'.$i.'_categoryfilter" name="podpress_feeds['.$i.'][inclCategories][]" size="5" multiple="multiple" class="podpress_categoryfilter_select">'."\n";
					echo '					<optgroup label="'.attribute_escape(__('Select categories', 'podpress')).'">'."\n";
					foreach( $allcategories as $category ) {
						if ( TRUE == in_array($category->term_id, $selected_categories) ) {
							$selected = ' selected="selected"';
						} else {
							$selected = '';
						}
						echo '						<option value="' . $category->term_id . '"'.$selected.'>'.$category->name.'</option>'."\n";
					}
					echo '					</optgroup>'."\n";
					echo '					</select>'."\n";
					echo '					<span class="podpress_description">'.__('Select one or more categories if this Feed should contain only posts of these categories. If the Feed should contain posts of all categories select none.', 'podpress').'</span>';
					echo '					<br /><span class="podpress_description">'.__('Hold the key [SHIFT] or [CTRL] and use the left mouse button to select more than one value.<br />Hold [CTRL] and use the left mouse button to deselect values.', 'podpress').'</span>';
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">';
					echo '					<label for="podpress_feed_'.$i.'_iTunesNewFeedURL">'.__('iTunes:New-Feed-Url', 'podpress').'</label><br/>'."\n";
					if ( 'Enable' == $feed['itunes-newfeedurl'] ) {
						$disable_selected = '';
						$enable_selected = ' selected="selected"';
					} else {
						$disable_selected = ' selected="selected"';
						$enable_selected = '';
					}
					echo '					<select name="podpress_feeds['.$i.'][itunes-newfeedurl]" id="podpress_feed_'.$i.'_iTunesNewFeedURL">'."\n";
					echo '						<option value="Disable"'.$disable_selected.'>'.__('Disable', 'podpress').'</option>'."\n";
					echo '						<option value="Enable"'.$enable_selected.'>'.__('Enable', 'podpress').'</option>'."\n";
					echo '					</select>'."\n";
					echo '					<br />'."\n";
					echo '					<br />'."\n";
					echo '					<label for="podpress_feed_'.$i.'_newfeedurl">'.__('iTunes:New-Feed-Url', 'podpress').' - '.__('the new Feed URL', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][newfeedurl]" id="podpress_feed_'.$i.'_newfeedurl" class="podpress_feeds_text_field" value="'.attribute_escape($feed['newfeedurl']).'" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					if ( TRUE == isset($feed['show_only_podPress_podcasts']) AND FALSE === $feed['show_only_podPress_podcasts'] ) {
						echo '					<input type="checkbox" name="podpress_feeds['.$i.'][show_only_podPress_podcasts]" id="podpress_feed_'.$i.'_show_only_podPress_podcasts" value="yes" /> <label for="podpress_feed_'.$i.'_show_only_podPress_podcasts">'.__('Include only posts with podPress attachments in this Feed', 'podpress').'</label> <span class="podpress_description">'.__('- while the File Type and Category Filters are not in use.', 'podpress').'</span>'."\n";
					} else {
						echo '					<input type="checkbox" name="podpress_feeds['.$i.'][show_only_podPress_podcasts]" id="podpress_feed_'.$i.'_show_only_podPress_podcasts" value="yes" checked="checked" /> <label for="podpress_feed_'.$i.'_show_only_podPress_podcasts">'.__('Include only posts with podPress attachments in this Feed', 'podpress').'</label> <span class="podpress_description">'.__('- while the File Type and Category Filters are not in use.', 'podpress').'</span>'."\n";
					}
					echo '					<br />'."\n";
					echo '					<br />'."\n";
					if ( TRUE == isset($feed['bypass_incl_selection']) AND TRUE === $feed['bypass_incl_selection']) {
						echo '					<input type="checkbox" name="podpress_feeds['.$i.'][bypass_incl_selection]" id="podpress_feed_'.$i.'_bypass_incl_selection" value="yes" checked="checked" /> <label for="podpress_feed_'.$i.'_bypass_incl_selection">'.__('Bypass the "Included in:" selection for this Feed', 'podpress').'</label> <span class="podpress_description">'.__('- If this option is selected then the first media file of the right file type will be the enclosure of a post in this Feed - if it is a RSS Feed. Posts in ATOM Feeds will contain all of their media files as enclosures (and not the ones you have marked while editing a post).', 'podpress').'</span>'."\n";
					} else {
						echo '					<input type="checkbox" name="podpress_feeds['.$i.'][bypass_incl_selection]" id="podpress_feed_'.$i.'_bypass_incl_selection" value="yes" /> <label for="podpress_feed_'.$i.'_bypass_incl_selection">'.__('Bypass the "Included in:" selection for this Feed', 'podpress').'</label> <span class="podpress_description">'.__('- If this option is selected then the first media file of the right file type will be the enclosure of a post in this Feed - if it is a RSS Feed. Posts in ATOM Feeds will contain all of their media files as enclosures (and not the ones you have marked while editing a post).', 'podpress').'</span>'."\n";
					}
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'.__('Feed Type:', 'podpress').'<br />'."\n";
					if ('atom' === $feed['feedtype']) {
						echo '					<input type="radio" name="podpress_feeds['.$i.'][feedtype]" id="podpress_feed_'.$i.'_feedtype_rss" value="rss" /> <label for="podpress_feed_'.$i.'_feedtype_rss">'.__('RSS', 'podpress').'</label><br />'."\n";
						echo '					<input type="radio" name="podpress_feeds['.$i.'][feedtype]" id="podpress_feed_'.$i.'_feedtype_atom" value="atom" checked="checked" /> <label for="podpress_feed_'.$i.'_feedtype_atom">'.__('ATOM', 'podpress').'</label>'."\n";
					} else {
						echo '					<input type="radio" name="podpress_feeds['.$i.'][feedtype]" id="podpress_feed_'.$i.'_feedtype_rss" value="rss" checked="checked" /> <label for="podpress_feed_'.$i.'_feedtype_rss">'.__('RSS', 'podpress').'</label><br />'."\n";
						echo '					<input type="radio" name="podpress_feeds['.$i.'][feedtype]" id="podpress_feed_'.$i.'_feedtype_atom" value="atom" /> <label for="podpress_feed_'.$i.'_feedtype_atom">'.__('ATOM', 'podpress').'</label>'."\n";
					}
					echo '				</div>'."\n";
					if ( FALSE == isset($feed['ttl']) OR (!empty($feed['ttl']) AND $feed['ttl'] < 1440) ) {
						$feed['ttl'] = 1440;
					}
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_rss_ttl">'.__('TTL (time-to-live)', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][ttl]" id="podpress_feed_'.$i.'_rss_ttl" value="'.$feed['ttl'].'" size="4" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_fullwidth_col">'."\n";
					echo '					<label for="podpress_feed_'.$i.'_iTunesFeedID">'.__('iTunes:FeedID', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][itunes-feedid]" id="podpress_feed_'.$i.'_iTunesFeedID" value="'.$feed['itunes-feedid'].'" size="10" />'."\n";
					//echo '					<label for="podpress_feed_'.$i.'_iTunesFeedID">'.__('iTunes:FeedID', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$i.'][itunes-feedid]" id="podpress_feed_'.$i.'_iTunesFeedID" value="'.$feed['itunes-feedid'].'" size="10" /> <input type="button" value="'.__('Ping iTunes Update', 'podpress').'" onclick="javascript: if(document.getElementById(\'podpress_feed_'.$i.'_iTunesFeedID\').value != \'\') { window.open(\'https://phobos.apple.com/WebObjects/MZFinance.woa/wa/pingPodcast?id=\'+document.getElementById(\'podpress_feed_'.$i.'_iTunesFeedID\').value); }" />'."\n";
					echo '				</div>'."\n";
					
					echo '				<div class="podpress_feed_settings_fullwidth_col">'."\n";
					if ( TRUE === $feed['use_headerlink'] ) {
						echo '					<input type="checkbox" name="podpress_feeds['.$i.'][use_headerlink]" id="podpress_feed_'.$i.'_use_headerlink" value="yes" checked="checked" /> <label for="podpress_feed_'.$i.'_use_headerlink">'.__('add Feed link to the blog header', 'podpress').'</label> <span class="podpress_description">'.__('(into &lt;head&gt;)', 'podpress').'</span>'."\n";
					} else {
						echo '					<input type="checkbox" name="podpress_feeds['.$i.'][use_headerlink]" id="podpress_feed_'.$i.'_use_headerlink" value="yes" /> <label for="podpress_feed_'.$i.'_use_headerlink">'.__('add Feed link to the blog header', 'podpress').'</label> <span class="podpress_description">'.__('(into &lt;head&gt;)', 'podpress').'</span>'."\n";
					}
					echo '				</div>'."\n";

					echo '			</div><!-- end accordion element -->'."\n";
					$i++;
				}
			}
			
			
			if ( $i < PODPRESS_FEEDS_MAX_NUMBER ) {
				for ($j=$i; $j<PODPRESS_FEEDS_MAX_NUMBER; $j++) {
					echo '			<h4><a href="">'.__('Feed', 'podpress').' ...</a></h4>'."\n";
					echo '			<div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<input type="checkbox" name="podpress_feeds['.$j.'][use]" id="podpress_feed_'.$j.'_use" value="yes" /> <label for="podpress_feed_'.$j.'_use">'.__('Activate Feed', 'podpress').'</label>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					if ( FALSE == defined('PODPRESS_DEACTIVATE_PREMIUM') OR FALSE === constant('PODPRESS_DEACTIVATE_PREMIUM') ) {
						echo '					<input type="checkbox" name="podpress_feeds['.$j.'][premium]" id="podpress_feed_'.$j.'_premium" value="yes" /> <label for="podpress_feed_'.$j.'_premium">'.__('Premium Feed', 'podpress').'</label>'."\n";
						if ( FALSE == isset($this->settings['enablePremiumContent']) OR TRUE !== $this->settings['enablePremiumContent'] ) {
							echo '					<br /><span class="podpress_description">'.sprintf(__('If this Feed should be a Premium Feed then you need to activate the Premium Content feature at the <a href="%1$s">general settings page of podPress</a>.', 'podpress'), $generalsettingspodpressurl).'</span>';
						}
					}
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_name">'.__('Feed Name', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][name]" id="podpress_feed_'.$j.'_name" class="podpress_feeds_text_field" value="" />'."\n";
					echo '					<br/><label for="podpress_feed_'.$j.'_feedtitle">'.__('How-to build the Feed title:', 'podpress').'</label> ';
					echo '					<select  id="podpress_feed_'.$j.'_feedtitle" name="podpress_feeds['.$j.'][feedtitle]">'."\n";
					echo '						<option value="No" selected="selected">'.__('use the blog name and append the Feed Name', 'podpress').'</option>'."\n";
					echo '						<option value="Yes">'.__('use only the blog name as the title', 'podpress').'</option>'."\n";
					echo '						<option value="Clean">'.__('use the Feed Name as the title', 'podpress').'</option>'."\n";
					echo '					</select>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_slug">'.__('Slug Name', 'podpress').'</label> <span class="podpress_description">'.__('- the name for this Feed in the URL', 'podpress').'</span><br /><input type="text" name="podpress_feeds['.$j.'][slug]" id="podpress_feed_'.$j.'_slug" class="podpress_feeds_text_field" value="" /><br /><span class="podpress_description">'.__('Please, use only these characters: a-z, 0-9, underscore and hiphen.', 'podpress').'</span>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_fullwidth_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_feedurl">'.__('Feed URL', 'podpress').'</label><br /><span id="podpress_feed_'.$j.'_feedurl">'.__('http://', 'podpress').'</span>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_subtitle">'.__('iTunes:Subtitle', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][subtitle]" id="podpress_feed_'.$j.'_subtitle" class="podpress_feeds_text_field" value="" />'."\n";
					echo '					<br /><br />'."\n";
					echo '					<label for="podpress_feed_'.$j.'_rss_category">'.__('RSS Category', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][rss_category]" id="podpress_feed_'.$j.'_rss_category" class="podpress_feeds_text_field" value="" size="40" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_descr">'.__('Description (RSS) / Subtitle (ATOM) / iTunes:Summary', 'podpress').'</label><br /><textarea name="podpress_feeds['.$j.'][descr]" id="podpress_feed_'.$j.'_descr" class="podpress_feeds_text_field" rows="4" cols="40"></textarea>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">';
					echo '					<label for="podpress_feed_'.$j.'_iTunesCategory_0">'.__('iTunes:Categories', 'podpress').'</label><br/>'."\n";
					echo '					<select id="podpress_feed_'.$j.'_iTunesCategory_0" name="podpress_feeds['.$j.'][itunes-category][0]">'."\n";
					echo '						<optgroup label="'.__('Select Primary', 'podpress').'">'."\n";
					podPress_itunesCategoryOptions();
					echo '						</optgroup>'."\n";
					echo '					</select><br />'."\n";
					echo '					<select name="podpress_feeds['.$j.'][itunes-category][1]">'."\n";
					echo '						<optgroup label="'.__('Select Second', 'podpress').'">'."\n";
					podPress_itunesCategoryOptions();
					echo '						</optgroup>'."\n";
					echo '					</select><br />'."\n";
					echo '					<select name="podpress_feeds['.$j.'][itunes-category][2]">'."\n";
					echo '						<optgroup label="'.__('Select Third', 'podpress').'">'."\n";
					podPress_itunesCategoryOptions();
					echo '						</optgroup>'."\n";
					echo '					</select>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_iTunesKeywords">'.__('iTunes:Keywords', 'podpress').'</label><br /><textarea name="podpress_feeds['.$j.'][itunes-keywords]" id="podpress_feed_'.$j.'_iTunesKeywords" class="podpress_feeds_text_field" rows="4" cols="40"></textarea><br /><span class="podpress_description">'.__('a list of max. 12 comma separated words', 'podpress').'</span>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_iTunesAuthor">'.__('iTunes:Author/Owner', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][itunes-author]" id="podpress_feed_'.$j.'_iTunesAuthor" class="podpress_feeds_text_field" value="" size="40" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_email">'.__('Owner E-mail address', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][email]" id="podpress_feed_'.$j.'_email" class="podpress_feeds_text_field" value="" size="40" />'."\n";
					echo '				</div>'."\n";
					$itunesimageurl = attribute_escape($this->settings['iTunes']['image']);
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_iTunesImage">'.__('iTunes:Image', 'podpress').'</label> <a href="" class="podpress_image_preview_link" title="'.__('Feed', 'podpress').' '.$j.' - '.__('iTunes:Image', 'podpress').'" onclick="podPress_jQuery(\'#podpress-itunesimage-preview-'.$j.'\').dialog(\'open\'); return false;">'.__('Preview', 'podpress').'</a><br /><input type="text" name="podpress_feeds['.$j.'][itunes-image]" id="podpress_feed_'.$j.'_iTunesImage" class="podpress_feeds_text_field" value="'.$itunesimageurl.'" size="40" />'."\n";
					echo '					<div id="podpress-itunesimage-preview-'.$j.'" title="'.attribute_escape(__('iTunes:Image', 'podpress')).'" class="podpress_itunesimage_preview">'."\n";
					echo '						<img src="'.$itunesimageurl.'" />'."\n";
					echo '					</div>'."\n";
					echo '				</div>'."\n";
					$rssimageurl = attribute_escape(get_option('rss_image'));
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_copyright">'.__('Feed Copyright / license name', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][copyright]" id="podpress_feed_'.$j.'_copyright" class="podpress_feeds_text_field" value="" size="40" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_rss_image">'.__('RSS Image (144 x 144 pixels)', 'podpress').'</label> <a href="" class="podpress_image_preview_link" title="'.__('Feed', 'podpress').' '.$j.' - '.__('RSS Image', 'podpress').'" onclick="podPress_jQuery(\'#podpress-rssimage-preview-'.$j.'\').dialog(\'open\'); return false;">'.__('Preview', 'podpress').'</a><br /><input type="text" name="podpress_feeds['.$j.'][rss_image]" id="podpress_feed_'.$j.'_rss_image" class="podpress_feeds_text_field" value="'.$rssimageurl.'" size="40" />'."\n";
					echo '					<div id="podpress-rssimage-preview-'.$j.'" title="'.attribute_escape(__('RSS Image', 'podpress')).'" class="podpress_rssimage_preview">'."\n";
					echo '						<img src="'.$rssimageurl.'" />'."\n";
					echo '					</div>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_license_url">'.__('URL to the full Copyright / license text', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][license_url]" id="podpress_feed_'.$j.'_license_url" class="podpress_feeds_text_field" value="" size="40" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_language">'.__('Language of this Feed', 'podpress').'</label><br />';
					echo '					<select id="podpress_feed_'.$j.'_language" name="podpress_feeds['.$j.'][language]">'."\n";
					echo '						<optgroup label="'.__('Select a language', 'podpress').'">'."\n";
					podPress_itunesLanguageOptions();
					echo '						</optgroup>'."\n";
					echo '					</select>'."\n";
					echo '					<br />'."\n";
					echo '					<br />'."\n";
					echo '					<label for="podpress_feed_'.$j.'_blog_charset">'.__('Encoding for this Feed').'</label><br /><input type="text" name="podpress_feeds['.$j.'][charset]" id="podpress_feed_'.$j.'_blog_charset" size="20" value="'.attribute_escape($blog_charset).'" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_filetypefilter">'.__('File Type Filter', 'podpress').'</label><br />'."\n";
					echo '					<select id="podpress_feed_'.$j.'_filetypefilter" name="podpress_feeds['.$j.'][FileTypes][]" size="5" multiple="multiple" class="podpress_filetypefilter_select">'."\n";
					echo '					<optgroup label="'.attribute_escape(__('Select file types', 'podpress')).'">'."\n";
					foreach ( $filetypes as $key => $value ) {
						if ($key !== 'audio_mp4') {
							echo '						<option value="'.$key.'">'.$value.'</option>'."\n";
						}
					}
					echo '					</optgroup>'."\n";
					echo '					</select>'."\n";
					echo '					<span class="podpress_description">'.__('Select one or more file types to include in this Feed only posts which have attached media files of these file types. (This filter bypasses the "Included in:" selection.)', 'podpress').'</span>';
					echo '					<br /><span class="podpress_description">'.__('Hold the key [SHIFT] or [CTRL] and use the left mouse button to select more than one value.<br />Hold [CTRL] and use the left mouse button to deselect values.', 'podpress').'</span>';
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">';
					echo '					<label for="podpress_feed_'.$j.'_iTunesExplicit">'.__('iTunes:Explicit', 'podpress').'</label><br/>';
					echo '					<select  id="podpress_feed_'.$j.'_iTunesExplicit" name="podpress_feeds['.$j.'][itunes-explicit]">'."\n";
					echo '						<option value="No" selected="selected">'.__('No', 'podpress').'</option>'."\n";
					echo '						<option value="Yes">'.__('Yes', 'podpress').'</option>'."\n";
					echo '						<option value="Clean">'.__('Clean', 'podpress').'</option>'."\n";
					echo '					</select>'."\n";
					echo '					<br />'."\n";
					echo '					<br />'."\n";
					echo '					<label for="podpress_feed_'.$j.'_iTunesBlock">'.__('iTunes:Block', 'podpress').'</label><br />'."\n";
					echo '					<select name="podpress_feeds['.$j.'][itunes-block]" id="podpress_feed_'.$j.'_iTunesBlock">'."\n";
					echo '						<option value="No" selected="selected">'.__('No', 'podpress').'</option>'."\n";
					echo '						<option value="Yes">'.__('Yes', 'podpress').'</option>'."\n";
					echo '					</select>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">';
					echo '					<label for="podpress_feed_'.$j.'_categoryfilter">'.__('Category Filter', 'podpress').'</label><br />'."\n";
					echo '					<select id="podpress_feed_'.$j.'_categoryfilter" name="podpress_feeds['.$j.'][inclCategories][]" size="5" multiple="multiple" class="podpress_categoryfilter_select">'."\n";
					echo '					<optgroup label="'.attribute_escape(__('Select categories', 'podpress')).'">'."\n";
					foreach( $allcategories as $category ) { 
						echo '						<option value="' . $category->term_id . '">'.$category->name.'</option>'."\n";
					}
					echo '					</optgroup>'."\n";
					echo '					</select>'."\n";
					echo '					<span class="podpress_description">'.__('Select one or more categories if this Feed should contain only posts of these categories. If the Feed should contain posts of all categories select none.', 'podpress').'</span>';
					echo '					<span class="podpress_description">'.__('Hold the key [SHIFT] or [CTRL] and use the left mouse button to select more than one value.<br />Hold [CTRL] and use the left mouse button to deselect values.', 'podpress').'</span>';
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_iTunesNewFeedURL">'.__('iTunes:New-Feed-Url', 'podpress').'</label><br />'."\n";
					echo '					<select name="podpress_feeds['.$j.'][itunes-newfeedurl]" id="podpress_feed_'.$j.'_iTunesNewFeedURL">'."\n";
					echo '						<option value="Disable" selected="selected">'.__('Disable', 'podpress').'</option>'."\n";
					echo '						<option value="Enable">'.__('Enable', 'podpress').'</option>'."\n";
					echo '					</select>'."\n";
					echo '					<br />'."\n";
					echo '					<br />'."\n";
					echo '					<label for="podpress_feed_'.$j.'_newfeedurl">'.__('iTunes:New-Feed-Url - the new Feed URL', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][newfeedurl]" id="podpress_feed_'.$j.'_newfeedurl" class="podpress_feeds_text_field" value="" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<input type="checkbox" name="podpress_feeds['.$j.'][show_only_podPress_podcasts]" id="podpress_feed_'.$j.'_show_only_podPress_podcasts" value="yes" checked="checked" /> <label for="podpress_feed_'.$j.'_show_only_podPress_podcasts">'.__('Include only posts with podPress attachments in this Feed', 'podpress').'</label> <span class="podpress_description">'.__('- while the File Type and Category Filters are not in use.', 'podpress').'</span>'."\n";
					echo '					<br />'."\n";
					echo '					<br />'."\n";
					echo '					<input type="checkbox" name="podpress_feeds['.$j.'][bypass_incl_selection]" id="podpress_feed_'.$j.'_bypass_incl_selection" value="yes" /> <label for="podpress_feed_'.$j.'_bypass_incl_selection">'.__('Bypass the "Included in:" selection for this Feed', 'podpress').'</label> <span class="podpress_description">'.__('- If this option is selected then the first media file of the right file type will be the enclosure of a post in this Feed - if it is a RSS Feed. Posts in ATOM Feeds will contain all of their media files as enclosures (and not the ones you have marked while editing a post).', 'podpress').'</span>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_left_col">'.__('Feed Type:', 'podpress').'<br />'."\n";
					echo '					<input type="radio" name="podpress_feeds['.$j.'][feedtype]" id="podpress_feed_'.$j.'_feedtype_rss" value="rss" checked="checked" /> <label for="podpress_feed_'.$j.'_feedtype_rss">'.__('RSS', 'podpress').'</label><br />'."\n";
					echo '					<input type="radio" name="podpress_feeds['.$j.'][feedtype]" id="podpress_feed_'.$j.'_feedtype_atom" value="atom" /> <label for="podpress_feed_'.$j.'_feedtype_atom">'.__('ATOM', 'podpress').'</label>'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_right_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_rss_ttl">'.__('TTL (time-to-live)', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][ttl]" id="podpress_feed_'.$j.'_rss_ttl" value="1440" size="4" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_fullwidth_col">'."\n";
					echo '					<label for="podpress_feed_'.$j.'_iTunesFeedID">'.__('iTunes:FeedID', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][itunes-feedid]" id="podpress_feed_'.$j.'_iTunesFeedID" value="" size="10" />'."\n";
					//echo '					<label for="podpress_feed_'.$j.'_iTunesFeedID">'.__('iTunes:FeedID', 'podpress').'</label><br /><input type="text" name="podpress_feeds['.$j.'][itunes-feedid]" id="podpress_feed_'.$j.'_iTunesFeedID" value="" size="10" /> <input type="button" value="'.__('Ping iTunes Update', 'podpress').'" onclick="javascript: if(document.getElementById(\'podpress_feed_'.$j.'_iTunesFeedID\').value != \'\') { window.open(\'https://phobos.apple.com/WebObjects/MZFinance.woa/wa/pingPodcast?id=\'+document.getElementById(\'podpress_feed_'.$j.'_iTunesFeedID\').value); }" />'."\n";
					echo '				</div>'."\n";
					echo '				<div class="podpress_feed_settings_fullwidth_col">'."\n";
					echo '					<input type="checkbox" name="podpress_feeds['.$j.'][use_headerlink]" id="podpress_feed_'.$j.'_use_headerlink" value="yes" /> <label for="podpress_feed_'.$j.'_use_headerlink">'.__('add Feed link to the blog header', 'podpress').'</label> <span class="podpress_description">'.__('(into &lt;head&gt;)', 'podpress').'</span>'."\n";
					echo '				</div>'."\n";
					echo '			</div><!-- end accordion element -->'."\n";
				}
			}
			echo '		</div><!-- end accordion -->'."\n";
			echo '		<p class="submit"> '."\n";
			echo '			<input class="button-primary" type="submit" name="Submit" value="'.__('Update Options', 'podpress').' &raquo;" /> '."\n";
			echo '		</p> '."\n";
			echo '	</fieldset>'."\n";
			echo '	<script type="text/javascript">'." podPress_updateFeedSettings();</script>";
			echo '	<input type="hidden" name="podPress_submitted" value="feed" />'."\n";
			echo '	</form> '."\n";
			echo '</div>'."\n";
		} // end of settings_feed_edit function
		
		function settings_feed_save() {
			if ( function_exists('check_admin_referer') ) {
				check_admin_referer('podPress_feed_settings_nonce');
			}
			$blog_charset = get_bloginfo('charset');
			if(function_exists('wp_cache_flush')) {
				wp_cache_flush();
			}
			if(isset($_POST['iTunes'])) {
				$iTunesSettings = $_POST['iTunes'];
				$iTunesSettings['summary'] = htmlspecialchars(strip_tags(trim($_POST['iTunes']['summary'])), ENT_QUOTES, $blog_charset);
				$iTunesSettings['image'] = clean_url($_POST['iTunes']['image'], array('http', 'https'), 'db');
				$iTunesSettings['author'] = htmlspecialchars(strip_tags(trim($_POST['iTunes']['author'])), ENT_QUOTES, $blog_charset);
				$iTunesSettings['subtitle'] = htmlspecialchars(strip_tags(trim($_POST['iTunes']['subtitle'])), ENT_QUOTES, $blog_charset);
				$iTunesSettings['keywords'] = $this->cleanup_itunes_keywords($_POST['iTunes']['keywords'], $blog_charset);
				$iTunesSettings['category'] = array();
				if(is_array($_POST['iTunes']['category'])) {
					foreach ($_POST['iTunes']['category'] as $value) {
						if('#' != $value AND '[ '.__('nothing', 'podpress').' ]' != $value) {
							$iTunesSettings['category'][] = $value;
						}
					}
				}
				$this->settings['iTunes'] = $iTunesSettings;
			}
			
			//~ if(isset($_POST['blogname'])) { podPress_update_option('blogname', htmlspecialchars(strip_tags(trim($_POST['blogname'])), ENT_QUOTES, $blog_charset)); }
			//~ if(isset($_POST['blogdescription'])) { podPress_update_option('blogdescription', htmlspecialchars(strip_tags(trim($_POST['blogdescription'])), ENT_QUOTES, $blog_charset)); }
			//~ if(isset($_POST['admin_email'])) { podPress_update_option('admin_email', htmlspecialchars(strip_tags(trim($_POST['admin_email'])), ENT_QUOTES, $blog_charset)); }

			//~ if(isset($_POST['blog_charset'])) { podPress_update_option('blog_charset', htmlspecialchars(strtoupper(strip_tags(trim($_POST['blog_charset']))), ENT_QUOTES, $blog_charset)); }
			//~ if(isset($_POST['posts_per_rss'])) { podPress_update_option('posts_per_rss', intval(preg_replace('/[^0-9]/', '', $_POST['posts_per_rss']))); }

			if(isset($_POST['rss_language'])) { podPress_update_option('rss_language', htmlspecialchars(strip_tags(trim($_POST['rss_language'])), ENT_QUOTES, $blog_charset));	}
			if(isset($_POST['rss_ttl'])) { podPress_update_option('rss_ttl', intval(preg_replace('/[^0-9]/', '', $_POST['rss_ttl'])));	}
			if(isset($_POST['rss_image'])) { podPress_update_option('rss_image', htmlspecialchars(strip_tags(trim($_POST['rss_image'])), ENT_QUOTES, $blog_charset));	}

			if(isset($_POST['rss_category'])) {
				$this->settings['rss_category'] = htmlspecialchars(strip_tags(trim($_POST['rss_category'])), ENT_QUOTES, $blog_charset);
			}
			if(isset($_POST['rss_copyright'])) {
				$this->settings['rss_copyright'] = htmlspecialchars(strip_tags(trim($_POST['rss_copyright'])), ENT_QUOTES, $blog_charset);
			}
			if(isset($_POST['rss_license_url'])) {
				$this->settings['rss_license_url'] = clean_url($_POST['rss_license_url'], array('http', 'https'), 'db');
			}
			//~ if( isset($_POST['protectFeed']) AND 'yes' == strtolower($_POST['protectFeed']) ) {
				//~ $this->settings['protectFeed'] = 'Yes';
			//~ } else {
				//~ $this->settings['protectFeed'] = 'No';
			//~ }
			//~ if(isset($_POST['rss_showlinks'])) {
				//~ $this->settings['rss_showlinks'] = $_POST['rss_showlinks'];
			//~ }
			if(isset($_POST['podcastFeedURL'])) {
				$this->settings['podcastFeedURL'] = clean_url($_POST['podcastFeedURL'], array('http', 'https'), 'db');
			}
			if ( isset($_POST['podpress_feeds']) ) {
				$i = 0;
				foreach ($_POST['podpress_feeds'] as $feed) {
					$name = htmlspecialchars(strip_tags(trim($feed['name'])), ENT_QUOTES, $blog_charset);
					if ( empty($feed['slug']) ) {
						$feed['slug'] = $name;
					}
					$slug = sanitize_title_with_dashes(trim($feed['slug']));
					if ( TRUE == defined('PODPRESS_DEACTIVATE_PREMIUM') AND TRUE === constant('PODPRESS_DEACTIVATE_PREMIUM') AND 'premium' == $slug )  {
						$name = '';
					}
					if ( FALSE == empty($name) AND FALSE == empty($slug) ) {
						if ( isset($feed['use']) ) {
							$this->settings['podpress_feeds'][$i]['use'] = TRUE;
						} else {
							$this->settings['podpress_feeds'][$i]['use'] = FALSE;
						}
						if ( isset($feed['premium']) ) {
							$this->settings['podpress_feeds'][$i]['premium'] = TRUE;
						} else {
							$this->settings['podpress_feeds'][$i]['premium'] = FALSE;
						}
						if ( TRUE == defined('PODPRESS_DEACTIVATE_PREMIUM') AND TRUE === constant('PODPRESS_DEACTIVATE_PREMIUM') )  {
							$this->settings['podpress_feeds'][$i]['premium'] = FALSE;
						}
						$this->settings['podpress_feeds'][$i]['name'] = $name;
						if ( isset($feed['feedtitle']) AND 'blognameastitle' === $feed['feedtitle'] ) {
							$this->settings['podpress_feeds'][$i]['feedtitle'] = 'blognameastitle';
						} elseif ( isset($feed['feedtitle']) AND 'feednameastitle' === $feed['feedtitle'] ) {
							$this->settings['podpress_feeds'][$i]['feedtitle'] = 'feednameastitle';
						} else {
							$this->settings['podpress_feeds'][$i]['feedtitle'] = 'append';
						}						
						$this->settings['podpress_feeds'][$i]['slug'] = $slug;
						$this->settings['podpress_feeds'][$i]['subtitle'] = htmlspecialchars(strip_tags(trim($feed['subtitle'])), ENT_QUOTES, $blog_charset);
						if ( isset($feed['itunes-newfeedurl']) AND 'Enable' === $feed['itunes-newfeedurl'] ) {
							$this->settings['podpress_feeds'][$i]['itunes-newfeedurl'] = 'Enable';
						} else {
							$this->settings['podpress_feeds'][$i]['itunes-newfeedurl'] = 'Disable';
						}
						$this->settings['podpress_feeds'][$i]['newfeedurl'] = clean_url($feed['newfeedurl'], array('http', 'https'), 'db');
						if ( FALSE !== empty($this->settings['podpress_feeds'][$i]['newfeedurl']) ) {	
							$this->settings['podpress_feeds'][$i]['itunes-newfeedurl'] = 'Disable';
						}
						$this->settings['podpress_feeds'][$i]['descr'] = htmlspecialchars(strip_tags(trim($feed['descr'])), ENT_QUOTES, $blog_charset);
						$this->settings['podpress_feeds'][$i]['rss_category'] = htmlspecialchars(strip_tags(trim($feed['rss_category'])), ENT_QUOTES, $blog_charset);
						if ( FALSE == is_array($feed['itunes-category']) ) {
							$this->settings['podpress_feeds'][$i]['itunes-category'] = Array();
						} else {
							$this->settings['podpress_feeds'][$i]['itunes-category'] = $feed['itunes-category'];
						}
						$this->settings['podpress_feeds'][$i]['itunes-keywords'] = $this->cleanup_itunes_keywords($feed['itunes-keywords'], $blog_charset);
						$this->settings['podpress_feeds'][$i]['itunes-author'] = htmlspecialchars(strip_tags(trim($feed['itunes-author'])), ENT_QUOTES, $blog_charset);
						$this->settings['podpress_feeds'][$i]['email'] = htmlspecialchars(strip_tags(trim($feed['email'])), ENT_QUOTES, $blog_charset);
						$this->settings['podpress_feeds'][$i]['itunes-image'] = clean_url($feed['itunes-image'], array('http', 'https'), 'db');
						$this->settings['podpress_feeds'][$i]['rss_image'] = clean_url($feed['rss_image'], array('http', 'https'), 'db');
						$this->settings['podpress_feeds'][$i]['copyright'] = htmlspecialchars(strip_tags(trim($feed['copyright'])), ENT_QUOTES, $blog_charset);
						$this->settings['podpress_feeds'][$i]['license_url'] = clean_url($feed['license_url'], array('http', 'https'), 'db');
						$this->settings['podpress_feeds'][$i]['language'] = htmlspecialchars(strip_tags(trim($feed['language'])), ENT_QUOTES, $blog_charset);
						$this->settings['podpress_feeds'][$i]['charset'] = htmlspecialchars(strip_tags(trim($feed['charset'])), ENT_QUOTES, $blog_charset);
						if ( FALSE == is_array($feed['FileTypes']) ) {
							$this->settings['podpress_feeds'][$i]['FileTypes'] = Array();
						} else {
							$this->settings['podpress_feeds'][$i]['FileTypes'] = $feed['FileTypes'];
						}
						if ( FALSE == is_array($feed['inclCategories']) ) {
							$this->settings['podpress_feeds'][$i]['inclCategories'] = Array();
						} else {
							$this->settings['podpress_feeds'][$i]['inclCategories'] = Array();
							foreach ($feed['inclCategories'] as $category) {
								$this->settings['podpress_feeds'][$i]['inclCategories'][] = intval(preg_replace('/[^0-9]/', '', $category));
							}
						}
						if ( isset($feed['itunes-explicit']) AND 'Yes' === $feed['itunes-explicit'] ) {
							$this->settings['podpress_feeds'][$i]['itunes-explicit'] = 'Yes';
						} elseif ( isset($feed['itunes-explicit']) AND 'Clean' === $feed['itunes-explicit'] ) {
							$this->settings['podpress_feeds'][$i]['itunes-explicit'] = 'Clean';
						} else {
							$this->settings['podpress_feeds'][$i]['itunes-explicit'] = 'No';
						}
						if ( isset($feed['show_only_podPress_podcasts']) ) {
							$this->settings['podpress_feeds'][$i]['show_only_podPress_podcasts'] = TRUE;
						} else {
							$this->settings['podpress_feeds'][$i]['show_only_podPress_podcasts'] = FALSE;
						}
						if ( isset($feed['bypass_incl_selection']) ) {
							$this->settings['podpress_feeds'][$i]['bypass_incl_selection'] = TRUE;
						} else {
							$this->settings['podpress_feeds'][$i]['bypass_incl_selection'] = FALSE;
						}
						if ( isset($feed['feedtype']) AND 'atom' === $feed['feedtype'] ) {
							$this->settings['podpress_feeds'][$i]['feedtype'] = 'atom';
						} else {
							$this->settings['podpress_feeds'][$i]['feedtype'] = 'rss';
						}
						$feed['ttl'] = intval(preg_replace('/[^0-9]/', '', $feed['ttl']));
						if ( 1440 > $feed['ttl'] ) {
							$feed['ttl'] = 1440;
						}
						$this->settings['podpress_feeds'][$i]['ttl'] = $feed['ttl'];
						$this->settings['podpress_feeds'][$i]['itunes-feedid'] = htmlspecialchars(strip_tags(trim($feed['itunes-feedid'])), ENT_QUOTES, $blog_charset);
						if ( isset($feed['itunes-block']) AND 'Yes' === $feed['itunes-block'] ) {
							$this->settings['podpress_feeds'][$i]['itunes-block'] = 'Yes';
						} else {
							$this->settings['podpress_feeds'][$i]['itunes-block'] = 'No';
						}
						if ( isset($feed['use_headerlink']) ) {
							$this->settings['podpress_feeds'][$i]['use_headerlink'] = TRUE;
						} else {
							$this->settings['podpress_feeds'][$i]['use_headerlink'] = FALSE;
						}
						$i++;
					}
				}
				$report = $i.'_podpress_feeds_send';
				// $i is the number of the submitted podPress Feeds with a proper Feed name. If a Feed name is empty for instance then delete the settings of this Feed.
				for ($j = $i; $j < PODPRESS_FEEDS_MAX_NUMBER; $j++) {
					unset($this->settings['podpress_feeds'][$j]);
				}					
			} else {
				$report = 'no_podpress_feeds_send';
			}

			$result = podPress_update_option('podPress_config', $this->settings);
			if ( FALSE !== $result ) {
				$location = get_option('siteurl') . '/wp-admin/admin.php?page=podpress/podpress_feed.php&updated=true'.'&debugreport='.$report;
			} else {
				$location = get_option('siteurl') . '/wp-admin/admin.php?page=podpress/podpress_feed.php&updated=false'.'&debugreport='.$report;
			}
			header('Location: '.$location);
			exit;
		}
	}
?>