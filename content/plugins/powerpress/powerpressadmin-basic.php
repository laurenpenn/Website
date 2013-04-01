<?php

function powerpress_admin_basic()
{
	$General = powerpress_get_settings('powerpress_general');
	$General = powerpress_default_settings($General, 'basic');
	
	$FeedSettings = powerpress_get_settings('powerpress_feed');
	$FeedSettings = powerpress_default_settings($FeedSettings, 'editfeed');
?>
<script type="text/javascript"><!--
function CheckRedirect(obj)
{
	if( obj.value )
	{
		if( obj.value.indexOf('rawvoice') == -1 && obj.value.indexOf('techpodcasts') == -1 && 
			obj.value.indexOf('blubrry') == -1 && obj.value.indexOf('podtrac') == -1 )
		{
			if( !confirm('<?php echo __('The redirect entered is not recongized as a supported statistics redirect service.', 'powerpress'); ?>\n\n<?php echo __('Are you sure you wish to continue with this redirect url?', 'powerpress'); ?>') )
			{
				obj.value = '';
				return false;
			}
		}
	}
	return true;
}

function SelectEmbedField(checked)
{
	if( checked )
		jQuery('#embed_replace_player').removeAttr("disabled");
	else
		jQuery('#embed_replace_player').attr("disabled","disabled");
}

jQuery(document).ready(function($) {
	
	jQuery('#episode_box_player_links_options').change(function () {
		
		var objectChecked = jQuery('#episode_box_player_links_options').attr('checked');
		if(typeof jQuery.prop === 'function') {
			objectChecked = jQuery('#episode_box_player_links_options').prop('checked');
		}
		
		if( objectChecked == true ) {
			jQuery('#episode_box_player_links_options_div').css("display", 'block' );
		}
		else {
			jQuery('#episode_box_player_links_options_div').css("display", 'none' );
			jQuery('.episode_box_no_player_or_links').attr("checked", false );
			jQuery('#episode_box_no_player_and_links').attr("checked", false );
			if(typeof jQuery.prop === 'function') {
				jQuery('.episode_box_no_player_or_links').prop("checked", false );
				jQuery('#episode_box_no_player_and_links').prop("checked", false );
			}
		}
	} );
	
	jQuery('#episode_box_no_player_and_links').change(function () {
		
		var objectChecked = jQuery(this).attr("checked");
		if(typeof jQuery.prop === 'function') {
			objectChecked = jQuery(this).prop("checked");
		}
		
		if( objectChecked == true ) {
			jQuery('.episode_box_no_player_or_links').attr("checked", false );
			if(typeof jQuery.prop === 'function') {
				jQuery('.episode_box_no_player_or_links').prop("checked", false );
			}
		}
	} );

	jQuery('.episode_box_no_player_or_links').change(function () {
		var objectChecked = jQuery(this).attr("checked");
		if(typeof jQuery.prop === 'function') {
			objectChecked = jQuery(this).prop("checked");
		}
		
		if( objectChecked == true) {
			jQuery('#episode_box_no_player_and_links').attr("checked", false );
			if(typeof jQuery.prop === 'function') {
				jQuery('#episode_box_no_player_and_links').prop("checked", false );
			}
		}
	} );
	
	jQuery('#episode_box_feature_in_itunes').change( function() {
		var objectChecked = jQuery('#episode_box_feature_in_itunes').attr('checked');
		if(typeof jQuery.prop === 'function') {
			objectChecked = jQuery('#episode_box_feature_in_itunes').prop('checked');
		}
		if( objectChecked ) {
			$("#episode_box_order").attr("disabled", true);
		} else {
			$("#episode_box_order").removeAttr("disabled");
		}
	});
} );
//-->
</script>

<input type="hidden" name="action" value="powerpress-save-settings" />
<input type="hidden" id="save_tab_pos" name="tab" value="<?php echo (empty($_POST['tab'])?0:$_POST['tab']); ?>" />

<h2><?php echo __('Blubrry PowerPress Settings', 'powerpress'); ?></h2>

<div id="powerpress_settings_page" class="powerpress_tabbed_content"> 
  <ul class="powerpress_settings_tabs">
		<li><a href="#tab0"><span><?php echo htmlspecialchars(__('Welcome', 'powerpress')); ?></span></a></li> 
		<li><a href="#tab1"><span><?php echo htmlspecialchars(__('Basic Settings', 'powerpress')); ?></span></a></li> 
		<li><a href="#tab2"><span><?php echo htmlspecialchars(__('Services & Stats', 'powerpress')); ?></span></a></li>
		<li><a href="#tab3"><span><?php echo htmlspecialchars(__('Media Appearance', 'powerpress')); ?></span></a></li>
		<li><a href="#tab4"><span><?php echo htmlspecialchars(__('Feeds', 'powerpress')); ?></span></a></li>
		<li><a href="#tab5"><span><?php echo htmlspecialchars(__('iTunes', 'powerpress')); ?></span></a></li>
		<li><a href="#tab6"><span><?php echo htmlspecialchars(__('T.V.', 'powerpress')); ?></span></a></li>
  </ul>
	
	<div id="tab0" class="powerpress_tab">
	<?php	powerpressadmin_welcome($General); ?>
	</div>
	
  <div id="tab1" class="powerpress_tab">
		<?php
		powerpressadmin_edit_entry_options($General);
		powerpressadmin_edit_podpress_options($General);
		?>
	</div>
	
	<div id="tab2" class="powerpress_tab">
		<?php
		powerpressadmin_edit_blubrry_services($General);
		powerpressadmin_edit_media_statistics($General);
		?>
	</div>
	
	<div id="tab3" class="powerpress_tab">
		<?php
		powerpressadmin_appearance($General);
		?>
	</div>
	
	<div id="tab4" class="powerpress_tab">
		<?php
		powerpressadmin_edit_feed_general($FeedSettings, $General);
		powerpressadmin_edit_feed_settings($FeedSettings, $General);
		?>
	</div>
	
	<div id="tab5" class="powerpress_tab">
		<?php
		powerpressadmin_edit_itunes_general($General);
		powerpressadmin_edit_itunes_feed($FeedSettings, $General);
		?>
	</div>
	
	<div id="tab6" class="powerpress_tab">
		<?php
		powerpressadmin_edit_tv($FeedSettings);
		?>
	</div>
	
</div>
<div class="clear"></div>

<?php
	$ChannelsCheckbox = '';
	if( !empty($General['custom_feeds']) )
		$ChannelsCheckbox = ' onclick="alert(\''.  __('You must delete all of the Podcast Channels to disable this option.', 'powerpress')  .'\');return false;"';
	$CategoryCheckbox = '';
	//if( !empty($General['custom_cat_feeds']) ) // Decided ont to include this warning because it may imply that you have to delete the actual category, which is not true.
	//	$CategoryCheckbox = ' onclick="alert(\'You must remove podcasting from the categories to disable this option.\');return false;"';
?>
<div style="margin-left: 10px;">
	<h3><?php echo __('Advanced Options', 'powerpress'); ?></h3>
	<div style="margin-left: 50px;">
		<div>
			<input type="checkbox" name="NULL[player_options]" value="1" checked disabled /> 
			<strong><?php echo __('Audio Player Options', 'powerpress'); ?></strong> - 
			<?php echo __('Select from 6 different web based audio players.', 'powerpress'); ?> 
			<span style="font-size: 85%;">(<a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_player.php'); ?>"><?php echo __('configure audio player', 'powerpress'); ?></a>)</span>
			
			
		</div>
		<div>
			<input type="checkbox" name="NULL[video_player_options]" value="1" checked disabled /> 
			<strong><?php echo __('Video Player Options', 'powerpress'); ?></strong> - 
			<?php echo __('Select from 2 different web based video players.', 'powerpress'); ?> 
			<span style="font-size: 85%;">(<a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_videoplayer.php'); ?>"><?php echo __('configure video player', 'powerpress'); ?></a>)</span>
			
		</div>
		<div>
			<input type="checkbox" name="General[channels]" value="1" <?php echo ( !empty($General['channels']) ?' checked':''); echo $ChannelsCheckbox; ?> /> 
			<strong><?php echo __('Custom Podcast Channels', 'powerpress'); ?></strong> - 
			<?php echo __('Manage multiple media files and/or formats to one blog post.', 'powerpress'); ?> 
			<?php if( empty($General['channels']) ) { ?>
			<span style="font-size: 85%;">(<?php echo __('feature will appear in left menu when enabled', 'powerpress'); ?>)</span>
			<?php } else { ?>
			<span style="font-size: 85%;">(<a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_customfeeds.php'); ?>"><?php echo __('configure podcast channels', 'powerpress'); ?></a>)</span>
			<?php } ?>
		</div>
		<div>
			<input type="checkbox" name="General[cat_casting]" value="1" <?php echo ( !empty($General['cat_casting']) ?' checked':'');  echo $CategoryCheckbox;  ?> /> 
			<strong><?php echo __('Category Podcasting', 'powerpress'); ?></strong> - 
			<?php echo __('Manage category podcast feeds.', 'powerpress'); ?> 
			<?php if( empty($General['cat_casting']) ) { ?>
			<span style="font-size: 85%;">(<?php echo __('feature will appear in left menu when enabled', 'powerpress'); ?>)</span>
			<?php } else { ?>
			<span style="font-size: 85%;">(<a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_categoryfeeds.php'); ?>"><?php echo __('configure podcast categories', 'powerpress'); ?></a>)</span>
			<?php } ?>
		</div>
		<div>
			<input type="checkbox" name="General[metamarks]" value="1" <?php echo ( !empty($General['metamarks']) ?' checked':'');  ?> /> 
			<strong><?php echo __('Meta Marks', 'powerpress'); ?></strong> - 
			<?php echo __('Add additional meta information to your media for syndication.', 'powerpress'); ?> 
			<?php echo powerpress_help_link('http://www.powerpresspodcast.com/metamarks/'); ?> 
			<span style="font-size: 85%;">(<?php echo __('feature will appear in episode entry box', 'powerpress'); ?>)</span>
			
		</div>
	</div>
</div>

<div style="margin-left: 10px;">
	<h3 style="margin-bottom: 5px;"><?php echo __('Looking for Support, Consulting or Custom Development?', 'powerpress'); ?></h3>
	<p style="margin: 0  0 0 50px; font-size: 115%;">
		<?php echo __('Blubrry offers a variety of options, free and paid, to assist you with your podcasting and Internet media needs. Whether you need your theme customized for podcasting or you want consulting on what video format is best for your audience, we have the staff and knowledge to assist.', 'powerpress'); ?>
	</p>
	<p style="margin: 5px 0 0 50px; font-size: 115%;">
	<strong><?php echo '<a href="http://www.blubrry.com/support/" target="_blank">'. __('Learn More about Blubrry Support Options', 'powerpress') .'</a>'; ?></strong>
	</p>
</div>

<?php
	if( isset($General['timestamp']) && $General['timestamp'] > 0 && $General['timestamp'] < ( time()- (60*60*24*14) ) ) // Lets wait 14 days before we annoy them asking for support
	{
?>
<div style="margin-left: 10px;">
	<h3 style="margin-bottom: 5px;"><?php echo __('Like The Plugin?', 'powerpress'); ?></h3>
	<p style="margin-top: 0;">
		<?php echo __('This plugin is great, don\'t you think? If you like the plugin we\'d be ever so grateful if you\'d give it your support. Here\'s how:', 'powerpress'); ?>
	</p>
	<ul id="powerpress_support">
		<li><?php echo sprintf(__('Rate this plugin 5 stars in the %s.', 'powerpress'), 
			'<a href="http://wordpress.org/extend/plugins/powerpress/" target="_blank">'. __('WordPress Plugins Directory', 'powerpress') .'</a>');
		
		?>
		</li>
		<li><?php echo __('Tell the world about PowerPress by writing about it on your blog', 'powerpress'); ?>, 
		<a href="http://twitter.com/home/?status=<?php echo urlencode( __('I\'m podcasting with Blubrry PowerPress (http://blubrry.com/powerpress/) #powerpress #wordpress', 'powerpress') ); ?>" target="_blank"><?php echo __('Twitter', 'powerpress'); ?></a>, 
		<a href="http://www.facebook.com/share.php?u=<?php echo urlencode('http://www.blubrry.com/powerpress/'); ?>&t=<?php echo urlencode( __('I podcast with Blubrry PowerPress', 'powerpress')); ?>" target="_blank"><?php echo __('Facebook', 'powerpress'); ?></a>,
		<a href="http://digg.com/submit?phase=2&url=<?php echo urlencode('http://www.blubrry.com/powerpress'); ?>&title=<?php echo urlencode( __('Blubrry PowerPress Podcasting Plugin for WordPress', 'powerpress') ); ?>" target="_blank"><?php echo __('Digg', 'powerpress'); ?></a>,
		etc...</li>
		<li><a href="http://www.blubrry.com/contact.php" target="_blank"><?php echo __('Send us feedback', 'powerpress'); ?></a> (<?php echo __('we love getting suggestions for new features!', 'powerpress'); ?>)</li>
	</ul>
</div>
<?php
	}
}

function powerpressadmin_edit_entry_options($General)
{
	if( !isset($General['default_url']) )
		$General['default_url'] = '';
	if( !isset($General['episode_box_mode']) )
		$General['episode_box_mode'] = 0; // Default not set, 1 = no duration/file size, 2 = yes duration/file size (default if not set)
	if( !isset($General['episode_box_embed']) )
		$General['episode_box_embed'] = 0;
	if( !isset($General['set_duration']) )
		$General['set_duration'] = 0;
	if( !isset($General['set_size']) )
		$General['set_size'] = 0;
	if( !isset($General['auto_enclose']) )
		$General['auto_enclose'] = 0;
	if( !isset($General['episode_box_player_size']) )
		$General['episode_box_player_size'] = 0;
	if( !isset($General['episode_box_closed_captioned']) )
		$General['episode_box_closed_captioned'] = 0;
	if( !isset($General['episode_box_order']) )
		$General['episode_box_order'] = 0;	
	if( !isset($General['episode_box_feature_in_itunes']) )
		$General['episode_box_feature_in_itunes'] = 0;
		
?>
<h3><?php echo __('Episode Entry Options', 'powerpress'); ?></h3>


<table class="form-table">
<tr valign="top">
<th scope="row">

<?php echo __('Podcast Entry Box', 'powerpress'); ?></th> 
<td>
	<p style="margin-top: 5px;">
		<?php echo __('Configure your podcast episode entry box with the options that fit your needs.', 'powerpress'); ?>
	</p>
				<div id="episode_box_mode_adv">
				
					<p style="margin-top: 15px;"><input class="episode_box_option" name="Null[ignore]" type="checkbox" value="1" checked onclick="return false" onkeydown="return false" /> <?php echo __('Media URL', 'powerpress'); ?>
						(<?php echo __('Specify URL to episode\'s media file', 'powerpress'); ?>)</p>
					
					<p style="margin-top: 15px;"><input id="episode_box_cover_image" class="episode_box_option" name="General[episode_box_mode]" type="checkbox" value="2" <?php if( empty($General['episode_box_mode']) || $General['episode_box_mode'] != 1 ) echo ' checked'; ?> /> <?php echo __('Media File Size and Duration', 'powerpress'); ?>
						(<?php echo __('Specify episode\'s media file size and duration', 'powerpress'); ?>)</p>
						
					<p style="margin-top: 15px; margin-bottom: 0;"><input id="episode_box_embed" class="episode_box_option" name="General[episode_box_embed]" type="checkbox" value="1"<?php if( !empty($General['episode_box_embed']) ) echo ' checked'; ?> onclick="SelectEmbedField(this.checked);"  /> <?php echo __('Embed Field', 'powerpress'); ?>
						(<?php echo __('Enter embed code from sites such as YouTube, Viddler and Blip.tv', 'powerpress'); ?>)</p>
							<p style="margin-top: 5px; margin-left: 20px; font-size: 90%;"><input id="embed_replace_player" class="episode_box_option" name="General[embed_replace_player]" type="checkbox" value="1"<?php if( !empty($General['embed_replace_player']) ) echo ' checked'; ?> /> <?php echo __('Replace Player with Embed', 'powerpress'); ?>
								(<?php echo __('Do not display default player if embed present for episode.', 'powerpress'); ?>)</p>
					
					<p style="margin-top: 15px;"><input id="episode_box_player_links_options" class="episode_box_option" name="NULL[episode_box_player_links_options]" type="checkbox" value="1"<?php if( !empty($General['episode_box_no_player_and_links']) || !empty($General['episode_box_no_player']) || !empty($General['episode_box_no_links']) ) echo ' checked'; ?> /> <?php echo __('Display Player and Links Options', 'powerpress'); ?>
					</p>
					<div id="episode_box_player_links_options_div" style="margin-left: 20px;<?php if( empty($General['episode_box_no_player_and_links']) && empty($General['episode_box_no_player']) && empty($General['episode_box_no_links']) ) echo 'display:none;'; ?>">
						
						<p style="margin-top: 0px; margin-bottom: 5px;"><input id="episode_box_no_player_and_links" class="episode_box_option" name="General[episode_box_no_player_and_links]" type="checkbox" value="1"<?php if( !empty($General['episode_box_no_player_and_links']) ) echo ' checked'; ?> /> <?php echo htmlspecialchars(__('No Player & Links Option', 'powerpress')); ?>
							(<?php echo __('Disable media player and links on a per episode basis', 'powerpress'); ?>)</p>
						
						<p style="margin-top: 0; margin-bottom: 0; margin-left: 20px;"><?php echo __('- or -', 'powerpress'); ?></p>
						
						<p style="margin-top: 5px;  margin-bottom: 10px;"><input id="episode_box_no_player" class="episode_box_option episode_box_no_player_or_links" name="General[episode_box_no_player]" type="checkbox" value="1"<?php if( !empty($General['episode_box_no_player']) ) echo ' checked'; ?> /> <?php echo __('No Player Option', 'powerpress'); ?>
							(<?php echo __('Disable media player on a per episode basis', 'powerpress'); ?>)</p>
						
						<p style="margin-top: 5px;  margin-bottom: 20px;"><input id="episode_box_no_links" class="episode_box_option episode_box_no_player_or_links" name="General[episode_box_no_links]" type="checkbox" value="1"<?php if( !empty($General['episode_box_no_links']) ) echo ' checked'; ?> /> <?php echo __('No Links Option', 'powerpress'); ?>
							(<?php echo __('Disable media links on a per episode basis', 'powerpress'); ?>)</p>
						
					</div>
				
					<p style="margin-top: 15px;"><input id="episode_box_cover_image" class="episode_box_option" name="General[episode_box_cover_image]" type="checkbox" value="1"<?php if( !empty($General['episode_box_cover_image']) ) echo ' checked'; ?> /> <?php echo __('Poster Image', 'powerpress'); ?>
						(<?php echo __('Specify URL to poster artwork specific to each episode', 'powerpress'); ?>)</p>
						
					<p style="margin-top: 15px;"><input id="episode_box_player_size" class="episode_box_option" name="General[episode_box_player_size]" type="checkbox" value="1"<?php if( !empty($General['episode_box_player_size']) ) echo ' checked'; ?> /> <?php echo __('Player Width and Height', 'powerpress'); ?> 
						(<?php echo __('Customize player width and height on a per episode basis', 'powerpress'); ?>)</p>
					
					<em><strong><?php echo __('USE THE ITUNES FIELDS BELOW AT YOUR OWN RISK.', 'powerpress'); ?></strong></em>
					<p style="margin-top: 15px;"><input id="episode_box_keywords" class="episode_box_option" name="General[episode_box_keywords]" type="checkbox" value="1"<?php if( !empty($General['episode_box_keywords']) ) echo ' checked'; ?> /> <?php echo __('iTunes Keywords Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to use your blog post tags', 'powerpress'); ?>)</p>
					<p style="margin-top: 15px;"><input id="episode_box_subtitle" class="episode_box_option" name="General[episode_box_subtitle]" type="checkbox" value="1"<?php if( !empty($General['episode_box_subtitle']) ) echo ' checked'; ?> /> <?php echo __('iTunes Subtitle Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to use the first 250 characters of your blog post', 'powerpress'); ?>)</p>
					<p style="margin-top: 15px;"><input id="episode_box_summary" class="episode_box_option" name="General[episode_box_summary]" type="checkbox" value="1"<?php if( !empty($General['episode_box_summary']) ) echo ' checked'; ?> /> <?php echo __('iTunes Summary Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to use your blog post', 'powerpress'); ?>)</p>
					<p style="margin-top: 15px;"><input id="episode_box_author" class="episode_box_option" name="General[episode_box_author]" type="checkbox" value="1"<?php if( !empty($General['episode_box_author']) ) echo ' checked'; ?> /> <?php echo __('iTunes Author Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to the post author name', 'powerpress'); ?>)</p>
					<p style="margin-top: 15px;"><input id="episode_box_explicit" class="episode_box_option" name="General[episode_box_explicit]" type="checkbox" value="1"<?php if( !empty($General['episode_box_explicit']) ) echo ' checked'; ?> /> <?php echo __('iTunes Explicit Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to use your feed\'s explicit setting', 'powerpress'); ?>)</p>	
						
					<p style="margin-top: 15px;"><input id="episode_box_closed_captioned" class="episode_box_option" name="General[episode_box_closed_captioned]" type="checkbox" value="1"<?php if( !empty($General['episode_box_closed_captioned']) ) echo ' checked'; ?> /> <?php echo __('iTunes Closed Captioned', 'powerpress'); ?> <?php echo powerpressadmin_new(); ?>
						(<?php echo __('Leave unchecked if you do not distribute closed captioned media', 'powerpress'); ?>)</p>
						
					<p style="margin-top: 15px;"><input id="episode_box_order" class="episode_box_option" name="General[episode_box_order]" type="checkbox" value="1"<?php if( !empty($General['episode_box_order']) ) echo ' checked'; ?> <?php if( !empty($General['episode_box_feature_in_itunes']) ) echo ' disabled'; ?> /> <?php echo __('iTunes Order', 'powerpress'); ?> <?php echo powerpressadmin_new(); ?>
						(<?php echo __('Override the default ordering of episodes on the iTunes podcast directory', 'powerpress'); ?>)</p>
						<em><strong><?php echo __('If conflicting values are present the iTunes directory will use the default ordering.', 'powerpress'); ?></strong></em><br />
						<em><strong><?php echo __('This feature only applies to the default podcast feed and Custom Podcast Channel feeds added by PowerPress.', 'powerpress'); ?></strong></em>
					
					<p style="margin-top: 15px;"><input id="episode_box_feature_in_itunes" class="episode_box_option" name="General[episode_box_feature_in_itunes]" type="checkbox" value="1"<?php if( !empty($General['episode_box_feature_in_itunes']) ) echo ' checked'; ?> /> <?php echo __('Feature Episode in iTunes', 'powerpress'); ?> <?php echo powerpressadmin_new(); ?>
						(<?php echo __('Display selected episode at top of your iTunes Directory listing', 'powerpress'); ?>)</p>
						<em><strong><?php echo __('All other episodes will be listed following the featured episode.', 'powerpress'); ?></strong></em><br />
						<em><strong><?php echo __('This feature only applies to the default podcast feed and Custom Podcast Channel feeds added by PowerPress.', 'powerpress'); ?></strong></em>
						
<?php if( defined('POWERPRESS_NOT_SUPPORTED') ) { ?>
<fieldset style="border: 1px dashed #333333; margin: 10px 0 10px -20px;">
<legend style="margin: 0 20px; padding: 0 5px; font-weight: bold;"><?php echo __('Features Not Supported by PowerPress', 'powerpress');  ?></legend>
					<p style="margin: 5px 10px 0 20px;">
						<strong style="color: #CC0000;"><?php echo __('USE THE FOLLOWING SETTINGS AT YOUR OWN RISK.', 'powerpress'); ?></strong>
					</p>
					<p style="margin: 10px 10px 10px 20px;"><input id="episode_box_block" class="episode_box_option" name="General[episode_box_block]" type="checkbox" value="1"<?php if( !empty($General['episode_box_block']) ) echo ' checked'; ?> /> <?php echo __('iTunes Block', 'powerpress'); ?> 
						(<?php echo __('Prevent episodes from appearing in iTunes. Feature only applies to iTunes, episodes will still appear in other directories and applications', 'powerpress'); ?>)</p>

					
</fieldset>
<?php } ?>
					<em><?php echo __('NOTE: An invalid entry into any of the iTunes fields may cause problems with your iTunes listing. It is highly recommended that you validate your feed using feedvalidator.org everytime you modify any of the iTunes fields listed above.', 'powerpress'); ?></em><br />
				</div>

</td>
</tr>
</table>
<script language="javascript"><!--
SelectEmbedField(<?php echo $General['episode_box_embed']; ?>);
//-->
</script>

<?php
	
	$AdvanecdOptions = false;
	if( !empty($General['default_url']) )
		$AdvanecdOptions = true;
	if( !empty($General['set_duration']) )
		$AdvanecdOptions = true;
	if( !empty($General['set_size']) )
		$AdvanecdOptions = true;
	if( !empty($General['auto_enclose']) )
		$AdvanecdOptions = true;
	if( !empty($General['permalink_feeds_only']) )
		$AdvanecdOptions = true;
	if( !empty($General['hide_warnings']) )
		$AdvanecdOptions = true;
		

	if( !$AdvanecdOptions ) {
?>
	<div style="margin-left: 10px; font-weight: bold;"><a href="#" onclick="document.getElementById('advanced_basic_options').style.display='block';return false;"><?php echo __('Show Advanced Episode Entry Settings', 'powerpress'); ?></a></div>
<?php } ?>
<!-- start advanced features -->
<div id="advanced_basic_options" <?php echo ($AdvanecdOptions?'':'style="display:none;"'); ?>>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Default Media URL', 'powerpress'); ?></th> 
<td>
	<input type="text" style="width: 80%;" name="General[default_url]" value="<?php echo $General['default_url']; ?>" maxlength="250" />
	<p><?php echo __('e.g. http://example.com/mediafolder/', 'powerpress'); ?></p>
	<p><?php echo __('URL above will prefix entered file names that do not start with \'http://\'. URL above must end with a trailing slash. You may leave blank if you always enter the complete URL to your media when creating podcast episodes.', 'powerpress'); ?>
	</p>
</td>
</tr>
</table>

<div id="episode_entry_settings">
<table class="form-table">
<tr valign="top">
<th scope="row">

<?php echo __('File Size Default', 'powerpress'); ?></th> 
<td>
		<select name="General[set_size]" class="bpp_input_med">
<?php
$options = array(0=>__('Auto detect file size', 'powerpress'), 1=>__('User specify', 'powerpress') );
	
while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($General['set_size']==$value?' selected':''). ">$desc</option>\n";
	
?>
		</select> (<?php echo __('specify default file size option when creating a new episode', 'powerpress'); ?>)
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Duration Default', 'powerpress'); ?></th> 
<td>
		<select name="General[set_duration]" class="bpp_input_med">
<?php
$options = array(0=>__('Auto detect duration (mp3\'s only)', 'powerpress'), 1=>__('User specify', 'powerpress'), -1=>__('Not specified (not recommended)', 'powerpress') );
	
while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($General['set_duration']==$value?' selected':''). ">$desc</option>\n";
	
?>
		</select> (<?php echo __('specify default duration option when creating a new episode', 'powerpress'); ?>)
</td>
</tr>
</table>
</div>

<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Auto Add Media', 'powerpress'); ?></th> 
<td>
		<select name="General[auto_enclose]" class="bpp_input_med">
<?php
$options = array(0=>__('Disabled (default)', 'powerpress'), 1=>__('First media link found in post content', 'powerpress'), 2=>__('Last media link found in post content', 'powerpress') );
	
while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($General['auto_enclose']==$value?' selected':''). ">$desc</option>\n";
	
?>
		</select>
		<p><?php echo __('When enabled, the first or last media link found in the post content is automatically added as your podcast episode.', 'powerpress'); ?></p>
		<p style="margin-bottom: 0;"><em><?php echo __('NOTE: Use this feature with caution. Links to media files could unintentionally become podcast episodes.', 'powerpress'); ?></em></p>
		<p><em><?php echo __('WARNING: Episodes created with this feature will <u>not</u> include Duration (total play time) information.', 'powerpress'); ?></em></p>
</td>
</tr>
<tr valign="top">
<th scope="row">
<?php echo __('Disable Warnings', 'powerpress'); ?></th> 
<td>
		<select name="General[hide_warnings]" class="bpp_input_med">
<?php
$options = array(0=>__('No (default)', 'powerpress'), 1=>__('Yes', 'powerpress') );
$current_value = (!empty($General['hide_warnings'])?$General['hide_warnings']:0);
while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($current_value==$value?' selected':''). ">$desc</option>\n";
	
?>
		</select>
		<p><?php echo __('Disable warning messages displayed in episode entry box. Errors are still displayed.', 'powerpress'); ?></p>
</td>
</tr>
</table>
</div>
<!-- end advanced features -->

<?php
		global $wp_rewrite;
		if( $wp_rewrite->permalink_structure ) // Only display if permalinks is enabled in WordPress
		{
?>
<h3><?php echo __('Permalinks', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Podcast Permalinks', 'powerpress'); ?></th> 
<td>
		<select name="General[permalink_feeds_only]" class="bpp_input_med">
<?php
$options = array(0=>__('Default WordPress Behavior', 'powerpress'), 1=>__('Match Feed Name to Page/Category', 'powerpress') );
$current_value = (!empty($General['permalink_feeds_only'])?$General['permalink_feeds_only']:0);

while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($current_value==$value?' selected':''). ">$desc</option>\n";
	
?>
		</select>
		<p><?php echo sprintf(__('When configured, %s/podcast/ is matched to page/category named \'podcast\'.', 'powerpress'), get_bloginfo('url') ); ?></p>
</td>
</tr>
</table>
<?php
		}
?>


<?php
}

function powerpressadmin_edit_podpress_options($General)
{
	if( !empty($General['process_podpress']) || powerpress_podpress_episodes_exist() )
	{
		if( !isset($General['process_podpress']) )
			$General['process_podpress'] = 0;
		if( !isset($General['podpress_stats']) )	
			$General['podpress_stats'] = 0;
?>

<h3><?php echo __('PodPress Options', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">

<?php echo __('PodPress Episodes', 'powerpress'); ?></th> 
<td>
<select name="General[process_podpress]" class="bpp_input_med">
<?php
$options = array(0=>__('Ignore', 'powerpress'), 1=>__('Include in Posts and Feeds', 'powerpress') );

while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($General['process_podpress']==$value?' selected':''). ">$desc</option>\n";
	
?>
</select>  (<?php echo __('includes podcast episodes previously created in PodPress', 'powerpress'); ?>)
</td>
</tr>
	<?php if( !empty($General['podpress_stats']) || powerpress_podpress_stats_exist() ) { ?>
	<tr valign="top">
	<th scope="row">

	<?php echo __('PodPress Stats Archive', 'powerpress'); ?></th> 
	<td>
	<select name="General[podpress_stats]" class="bpp_input_sm">
	<?php
	$options = array(0=>__('Hide', 'powerpress'), 1=>__('Display', 'powerpress') );

	while( list($value,$desc) = each($options) )
		echo "\t<option value=\"$value\"". ($General['podpress_stats']==$value?' selected':''). ">$desc</option>\n";
		
	?>
	</select>  (<?php echo __('display archive of old PodPress statistics', 'powerpress'); ?>)
	</td>
	</tr>
	<?php } ?>
	</table>
<?php
	}
}


function powerpressadmin_edit_itunes_general($General, $FeedSettings = false, $feed_slug='podcast', $cat_ID=false)
{
	// Set default settings (if not set)
	if( $FeedSettings )
	{
		if( !isset($FeedSettings['itunes_url']) )
			$FeedSettings['itunes_url'] = '';
	}
	if( !isset($General['itunes_url']) )
		$General['itunes_url'] = '';
		
	
	$OpenSSLSupport = extension_loaded('openssl');
	if( !$OpenSSLSupport && function_exists('curl_version') )
	{
		$curl_info = curl_version();
		$OpenSSLSupport = ($curl_info['features'] & CURL_VERSION_SSL );
	}
		
	if( $OpenSSLSupport == false )
	{
?>
<div class="error powerpress-error"><?php echo __('Ping iTunes requires OpenSSL in PHP. Please refer to your php.ini to enable the php_openssl module.', 'powerpress'); ?></div>
<?php } // End if !$OpenSSLSupport ?>

<h3><?php echo __('iTunes Listing Information', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('iTunes Subscription URL', 'powerpress'); ?></th> 
<td>
<?php
	if( $FeedSettings ) {
?>
<input type="text" style="width: 80%;" name="Feed[itunes_url]" value="<?php echo $FeedSettings['itunes_url']; ?>" maxlength="250" />
<?php } else { ?>
<input type="text" style="width: 80%;" name="General[itunes_url]" value="<?php echo $General['itunes_url']; ?>" maxlength="250" />
<?php } ?>
<p><?php echo sprintf(__('e.g. %s', 'powerpress'), 'http://itunes.apple.com/podcast/title-of-podcast/id<strong>000000000</strong>'); ?></p>

<p><?php echo sprintf(__('You may use the older style Subscription URL: %s', 'powerpress'), 'http://itunes.apple.com/WebObjects/MZStore.woa/wa/viewPodcast?id=<strong>000000000</strong>'); ?></p>

<p><?php echo sprintf( __('Click the following link to %s.', 'powerpress'), '<a href="https://buy.itunes.apple.com/WebObjects/MZFinance.woa/wa/publishPodcast" target="_blank">'. __('Publish a Podcast on iTunes', 'powerpress') .'</a>'); ?>
 <?php echo __('iTunes will email your Subscription URL to the <em>iTunes Email</em> entered below when your podcast is accepted into the iTunes Directory.', 'powerpress'); ?>
</p>
<p>
<?php echo __('Recommended feed to submit to iTunes: ', 'powerpress'); ?>
<?php
	if( $cat_ID )
	{
		echo get_category_feed_link($cat_ID);
	}
	else
	{
		echo get_feed_link($feed_slug);
	}
?>
</p>

</td>
</tr>
</table>

<!-- start advanced features -->
<table class="form-table">
<tr valign="top">
<th scope="row">

<?php echo __('Update iTunes Listing', 'powerpress'); ?></th> 
<td>
<p style="margin-top: 5px;"><?php echo __('This option is no longer available.', 'powerpress'); ?> 
	<?php echo __('Learn more:', 'powerpress'); ?> <a href="http://blog.blubrry.com/2011/02/11/apple-drops-itunes-podcast-directory-update-listing-ping-functionality/" target="_blank"><?php echo __('Apple Drops iTunes Podcast Directory Update Listing/Ping (pingPodcast) Function', 'powerpress'); ?></a>
</p>

</td>
</tr>
</table>
<!-- end advanced features -->
<?php

?>
<?php
} // end itunes general

function powerpressadmin_edit_blubrry_services($General)
{
	
	$ModeDesc = 'None';
	if( !empty($General['blubrry_auth']) )
		$ModeDesc = 'Media Statistics Only';
	if( !empty($General['blubrry_hosting']) )
		$ModeDesc = 'Media Statistics and Hosting';
	$StatsInDashboard = true;
	if( !empty($General['disable_dashboard_widget']) )
		$StatsInDashboard = false;
		
?>
<h3><?php echo __('Integrate Blubrry Services', 'powerpress'); ?>  &nbsp; <span style="color: #CC0000; font-size: 11px;"><?php echo __('optional', 'powerpress'); ?></span></h3>
<p style="margin-bottom: 0;">
	<?php echo __('Add Blubrry Media Statistics to your WordPress dashboard.','powerpress'); ?>
</p>
<p style="margin-top: 5px;">
	<?php echo __('Blubrry Media Hosting users can also quickly upload and publish media directly from their blog.','powerpress'); ?>
</p>

<div style="margin-left: 40px;">
	<p>
		<?php echo __('Have an account on Blubrry.com?','powerpress'); ?>
	</p>
	<p style="font-size: 110%;">
		<strong><a href="<?php echo admin_url(); echo wp_nonce_url( "admin.php?action=powerpress-jquery-account", 'powerpress-jquery-account'); ?>&amp;KeepThis=true&amp;TB_iframe=true&amp;width=600&amp;height=400&amp;modal=true" target="_blank" class="thickbox" style="color: #3D517E;"><?php echo __('Click here to configure Blubrry Statistics and Hosting services', 'powerpress'); ?></a></strong>
	</p>
	<p style="margin-left: 40px;">
		<input name="StatsInDashboard" type="checkbox" value="1"<?php if( $StatsInDashboard == true ) echo ' checked'; ?> /> 
		<?php echo __('Display Blubrry Media Statistics in your dashboard', 'powerpress'); ?>
	</p>
	<p style="margin-bottom: 0;">
		<?php echo __('Don\'t have an account at Blubrry.com?','powerpress'); ?>
	</p>
	<p style="margin-top: 5px;">
		<?php
		echo sprintf(__('%s offers an array of services to media creators including a %s %s. Our %s, which includes U.S. downloads, trending, exporting, is available for $5 month. Need a reliable place to host your media? %s media hosting packages start at $12. %s', 'powerpress'),
			'<a href="http://www.blubrry.com/" target="_blank">Blubrry.com</a>',
			'<strong style="color: #CC0000;">'.__('FREE','powerpress').'</strong>',
			'<a href="http://www.blubrry.com/podcast_statistics/" target="_blank">'. __('Basic Stats Service', 'powerpress') .'</a>',
			'<a href="https://secure.blubrry.com/podcast-statistics-premium/" target="_blank">'. __('Premium Media Statistics', 'powerpress') .'</a>',
			'<a href="https://secure.blubrry.com/podcast-publishing-premium-with-hosting/" target="_blank" style="text-decoration: none;">'. __('Blubrry Media Hosting', 'powerpress') .'</a>',
			'<a href="https://secure.blubrry.com/podcast-publishing-premium-with-hosting/" target="_blank">'. __('Learn More', 'powerpress') .'</a>'
		);
		?>
	</p>
</div>

<?php /*  ?>
<p>
	<?php //echo sprintf(
		__('Adds %s to your blog\'s %s plus features for %s users to quickly upload and publish media directly from their blog.', 'powerpress'),
		'<a href="http://www.blubrry.com/podcast_statistics/" target="_blank">'. __('Blubrry Media Statistics', 'powerpress') .'</a>',
		'<a href="'. admin_url() .'">'. __('WordPress Dashboard', 'powerpress') .'</a>',
		'<a href="https://secure.blubrry.com/podcast-publishing-premium-with-hosting/" target="_blank">'. __('Blubrry Media Hosting', 'powerpress') .'</a>' );
	?>
</p>
<p>
	<em><?php echo __('Note: <b>No membership or service is required</b> to use this free open source podcasting plugin.', 'powerpress'); ?></em>
</p>
<table class="form-table">
	<tr valign="top">
	<th scope="row">
	<?php echo __('Blubrry Services', 'powerpress'); ?>*
	</th>
	<td>
		<p style="margin-top: 5px;"><span id="service_mode"><?php echo $ModeDesc; ?></span> (<strong><a href="<?php echo admin_url(); echo wp_nonce_url( "admin.php?action=powerpress-jquery-account", 'powerpress-jquery-account'); ?>&amp;KeepThis=true&amp;TB_iframe=true&amp;width=600&amp;height=400&amp;modal=true" target="_blank" class="thickbox" style="color: #3D517E;"><?php echo __('Click here to configure Blubrry Services', 'powerpress'); ?></a></strong>)</p>
	</td>
	</tr>
	
	<tr valign="top">
	<th scope="row">
	<?php echo __('Dashboard Integration', 'powerpress'); ?> 
	</th>
	<td>
		<p style="margin-top: 5px;"><input name="StatsInDashboard" type="checkbox" value="1"<?php if( $StatsInDashboard == true ) echo ' checked'; ?> /> 
		<?php echo __('Display Statistics in WordPress Dashboard', 'powerpress'); ?></p>
	</td>
	</tr>
</table>
<p>
*<em>The Blubrry basic statistics service is FREE. Our 
<a href="https://secure.blubrry.com/podcast-statistics-premium/" target="_blank">Premium Statistics Service</a>,
which includes U.S. downloads, trending and exporting, is available for $5 month. Blubrry
<a href="https://secure.blubrry.com/podcast-publishing-premium-with-hosting/" target="_blank">Media Hosting</a>
packages start at $12.</em>
</p>
<?php
	*/
}

function powerpressadmin_edit_media_statistics($General)
{
	if( !isset($General['redirect1']) )
		$General['redirect1'] = '';
	if( !isset($General['redirect2']) )
		$General['redirect2'] = '';
	if( !isset($General['redirect3']) )
		$General['redirect3'] = '';
?>
<h3><?php echo __('Media Statistics', 'powerpress'); ?>  &nbsp; <span style="color: #CC0000; font-size: 11px;"><?php echo __('optional', 'powerpress'); ?></span></h3>
<div style="margin-left: 40px;">
	<p>
	<?php echo __('Enter your Redirect URL issued by your media statistics service provider below.', 'powerpress'); ?>
	</p>

	<div style="position: relative; margin-left: 40px; padding-bottom: 10px;">
		<table class="form-table">
		<tr valign="top">
		<th scope="row">
		<?php echo __('Redirect URL 1', 'powerpress'); ?> 
		</th>
		<td>
		<input type="text" style="width: 60%;" name="General[redirect1]" value="<?php echo $General['redirect1']; ?>" onChange="return CheckRedirect(this);" maxlength="250" /> 
		</td>
		</tr>
		</table>
		<?php if( empty($General['redirect2']) && empty($General['redirect3']) ) { ?>
		<div style="position: absolute;bottom: -2px;left: -40px;" id="powerpress_redirect2_showlink">
			<a href="javascript:void();" onclick="javascript:document.getElementById('powerpress_redirect2_table').style.display='block';document.getElementById('powerpress_redirect2_showlink').style.display='none';return false;"><?php echo __('Add Another Redirect', 'powerpress'); ?></a>
		</div>
		<?php } ?>
	</div>
	
		
	<div id="powerpress_redirect2_table" style="position: relative;<?php if( empty($General['redirect2']) && empty($General['redirect3']) ) echo 'display:none;'; ?> margin-left: 40px; padding-bottom: 10px;">
		<table class="form-table">
		<tr valign="top">
		<th scope="row">
		<?php echo __('Redirect URL 2', 'powerpress'); ?> 
		</th>
		<td>
		<input type="text"  style="width: 60%;" name="General[redirect2]" value="<?php echo $General['redirect2']; ?>" onblur="return CheckRedirect(this);" maxlength="250" />
		</td>
		</tr>
		</table>
		<?php if( $General['redirect3'] == '' ) { ?>
		<div style="position: absolute;bottom: -2px;left: -40px;" id="powerpress_redirect3_showlink">
			<a href="javascript:void();" onclick="javascript:document.getElementById('powerpress_redirect3_table').style.display='block';document.getElementById('powerpress_redirect3_showlink').style.display='none';return false;"><?php echo __('Add Another Redirect', 'powerpress'); ?></a>
		</div>
		<?php } ?>
	</div>

	<div id="powerpress_redirect3_table" style="<?php if( empty($General['redirect3']) ) echo 'display:none;'; ?> margin-left: 40px;">
		<table class="form-table">
		<tr valign="top">
		<th scope="row">
		<?php echo __('Redirect URL 3', 'powerpress'); ?> 
		</th>
		<td>
		<input type="text" style="width: 60%;" name="General[redirect3]" value="<?php echo $General['redirect3']; ?>" onblur="return CheckRedirect(this);" maxlength="250" />
		</td>
		</tr>
		</table>
	</div>
	<style type="text/css">
	#TB_window {
		border: solid 1px #3D517E;
	}
	</style>
	
	<p>
		<?php echo __('Need a media statistics provider?', 'powerpress'); ?> 
		<a href="https://secure.blubrry.com/podcast-statistics-premium/" target="_blank" style="text-decoration: none;">
		<?php
			echo sprintf( __('Blubrry.com offers %s access to the best statistics!', 'powerpress'),
				'<strong style="color: #CC0000;">'.__('FREE', 'powerpress').'</strong>' );
		?>
		</a>
	</p>

	<div id="blubrry_stats_box">
		<div style="font-family: Arial, Helvetica, sans-serif; border: solid 1px #ADDA13; background-color:#DFF495;padding:10px; margin-top:10px; position: relative;">
			<p style="font-size: 14px; margin-top: 0;">
			<?php echo __('Blubrry brings you the most all-inclusive digital media statistics service available. Gain unsurpassed insights into your audience. Find out who is linking to you, listener-base demographics and geographical data with worldwide mapping. Try us! You\'ll find our custom reports and daily email summaries are info you can trust, track and build your media program on.', 'powerpress'); ?>
			</p>
			<p style="font-size: 14px;">
			<?php echo sprintf(__('* Get %s Media Statistics by taking a few minutes and adding your podcast to Blubrry.com. What\'s the catch? Nothing! For many, our free service is all you will need. But if you\'re looking to further your abilities with media download information, we hope you consider upgrading to our paid Premium Statistics Service.', 'powerpress'),
				'<strong style="color: #990000;">'. __('FREE', 'powerpress') .'</strong>'
				); ?>
			</p>
		
			<div style="text-align: center; font-size: 24px; font-weight: normal; margin-bottom: 8px;"><a href="http://www.blubrry.com/addpodcast.php?feed=<?php echo urlencode(get_feed_link('podcast')); ?>" target="_blank" style="color: #3D517E;"><?php echo __('Sign Up Now!', 'powerpress'); ?></a></div>
			<div style="font-size: 85%; position: absolute; bottom: 4px; right: 8px;"><i><?php echo __('* some restrictions apply', 'powerpress'); ?> <a href="http://www.blubrry.com/podcast_statistics/" target="_blank"><?php echo __('learn more', 'powerpress'); ?></a></i></div>
		</div>
	</div>
</div>
<?php
}
	
function powerpressadmin_appearance($General=false)
{
	if( $General === false )
		$General = powerpress_get_settings('powerpress_general');
	$General = powerpress_default_settings($General, 'appearance');
	if( !isset($General['player_function']) )
		$General['player_function'] = 1;
	if( !isset($General['player_aggressive']) )
		$General['player_aggressive'] = 0;
	if( !isset($General['new_window_width']) )
		$General['new_window_width'] = '';
	if( !isset($General['new_window_height']) )
		$General['new_window_height'] = '';
	if( !isset($General['player_width']) )
		$General['player_width'] = '';
	if( !isset($General['player_height']) )
		$General['player_height'] = '';
	if( !isset($General['player_width_audio']) )
		$General['player_width_audio'] = '';	
	if( !isset($General['disable_appearance']) )
		$General['disable_appearance'] = false;	
		
	
	$Players = array('podcast'=>__('Default Podcast (podcast)', 'powerpress') );
	if( isset($General['custom_feeds']) )
	{
		while( list($podcast_slug, $podcast_title) = each($General['custom_feeds']) )
		{
			if( $podcast_slug == 'podcast' )
				continue;
			$Players[$podcast_slug] = sprintf('%s (%s)', $podcast_title, $podcast_slug);
		}
	}

?>

<h3><?php echo __('Media Appearance Settings', 'powerpress'); ?></h3>

<div id="enable_presentation_settings">

<table class="form-table">
<tr valign="top">
<th scope="row">&nbsp;	</th> 
<td>
	<ul>
		<li><label><input type="radio" name="General[disable_appearance]" value="0" <?php if( $General['disable_appearance'] == 0 ) echo 'checked'; ?> onclick="javascript: jQuery('#presentation_settings').css('display', (this.checked?'block':'none') );" /> <?php echo __('Enable PowerPress Media Players and Links', 'powerpress'); ?></label> (<?php echo __('default', 'powerpress'); ?>)</li>
		<li>
			<ul>
				<li><?php echo __('PowerPress will add media players and links to your site.', 'powerpress'); ?></li>
			</ul>
		</li>
		
		<li><label><input type="radio" name="General[disable_appearance]" value="1" <?php if( $General['disable_appearance'] == 1 ) echo 'checked'; ?> onclick="javascript: jQuery('#presentation_settings').css('display', (this.checked?'none':'block') );" /> <?php echo __('Disable PowerPress Media Players and Links', 'powerpress'); ?></label></li>
		<li>
			<ul>
				<li><?php echo __('PowerPress will <u>not</u> add any media players or media links to your site. PowerPress will only be used to add podcasting support to your feeds.', 'powerpress'); ?></li>
			</ul>
		</li>
	</ul>
</td>
</tr>
</table>
</div>

<div id="presentation_settings"<?php if($General['disable_appearance']) echo ' style="display: none;"'; ?>><!-- start presentation settings -->
<h3><?php echo __('Blog Posts and Pages', 'powerpress'); ?></h3>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Display Media & Links', 'powerpress'); ?></th> 
<td>
	<ul>
		<li><label><input type="radio" name="General[display_player]" value="1" <?php if( $General['display_player'] == 1 ) echo 'checked'; ?> /> <?php echo __('Below page content', 'powerpress'); ?></label> (<?php echo __('default', 'powerpress'); ?>)</li>
		<li>
			<ul>
				<li><?php echo __('Player and media links will appear <u>below</u> your post and page content.', 'powerpress'); ?></li>
			</ul>
		</li>
		
		<li><label><input type="radio" name="General[display_player]" value="2" <?php if( $General['display_player'] == 2 ) echo 'checked'; ?> /> <?php echo __('Above page content', 'powerpress'); ?></label></li>
		<li>
			<ul>
				<li><?php echo __('Player and media links will appear <u>above</u> your post and page content.', 'powerpress'); ?></li>
			</ul>
		</li>
		<li><label><input type="radio" name="General[display_player]" value="0" <?php if( $General['display_player'] == 0 ) echo 'checked'; ?> /> <?php echo __('Disable', 'powerpress'); ?></label></li>
		<li>
			<ul>
				<li><?php echo __('Player and media links will <u>NOT</u> appear in your post and page content. Media player and links can be added manually by using the <i>shortcode</i> below.', 'powerpress'); ?></li>
			</ul>
		</li>
	</ul>
	<p><input name="General[display_player_excerpt]" type="checkbox" value="1" <?php if( !empty($General['display_player_excerpt']) ) echo 'checked '; ?>/> <?php echo __('Display media / links in:', 'powerpress'); ?> <a href="http://codex.wordpress.org/Template_Tags/the_excerpt" title="<?php echo __('WordPress Excerpts', 'powerpress'); ?>" target="_blank"><?php echo __('WordPress Excerpts', 'powerpress'); ?></a>  (<?php echo __('e.g. search results', 'powerpress'); ?>)</p>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('PowerPress Shortcode', 'powerpress'); ?></th>
<td>
<p>
<?php echo sprintf(__('The %s shortcode is used to position your media presentation (player and download links) exactly where you want within your post or page content.', 'powerpress'), '<code>[powerpress]</code>'); ?> 
<?php echo __('Simply insert the following code on a new line in your content.', 'powerpress'); ?>
</p>
<div style="margin-left: 30px;">
	<code>[powerpress]</code>
</div>
<p>
<?php echo sprintf(__('Please visit the %s page for additional options.', 'powerpress'), '<a href="http://help.blubrry.com/blubrry-powerpress/shortcode/" target="_blank">'. __('PowerPress Shortcode', 'powerpress') .'</a>' ); ?>
</p>
<p>
<?php echo __('Note: When specifying a URL to media in the powerpress shortcode, only the player is included. The Media Links will <u>NOT</u> be included since there is not enough meta information to display them.', 'powerpress'); ?>
</p>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Media Player', 'powerpress'); ?></th>
<td>

<p><label><input type="checkbox" name="PlayerSettings[display_media_player]" value="2" <?php if( $General['player_function'] == 1 || $General['player_function'] == 2 ) echo 'checked '; ?>/> <?php echo __('Display Player', 'powerpress'); ?></label></p>
<?php /* ?>
<p style="margin-left: 35px;"><input type="checkbox" name="General[display_player_disable_mobile]" value="1" <?php if( !empty($General['display_player_disable_mobile']) ) echo 'checked '; ?>/> <?php echo __('Disable Media Player for known mobile devices.', 'powerpress'); ?></p>
<?php */ ?>
<p><?php echo __('Detected mobile and tablet devices use an HTML5 player with a fallback link to download the media.', 'powerpress'); ?></p>
</td>
</tr>
</table>




<table class="form-table">

<tr valign="top">
<th scope="row">

<?php echo __('Media Links', 'powerpress'); ?></th> 
<td>
	<p><label><input type="checkbox" name="PlayerSettings[display_pinw]" value="3" <?php if( $General['player_function'] == 3 || $General['player_function'] == 1 ) echo 'checked '; ?>/> <?php echo __('Display Play in new Window Link', 'powerpress'); ?></label></p>
	
	<p><label><input type="checkbox" name="PlayerSettings[display_download]" value="1" <?php if( $General['podcast_link'] != 0 ) echo 'checked '; ?>/> <?php echo __('Display Download Link', 'powerpress'); ?></label></p>
	
	<p style="margin-left: 35px;"><input type="checkbox" id="display_download_size" name="PlayerSettings[display_download_size]" value="1" <?php if( $General['podcast_link'] == 2 || $General['podcast_link'] == 3 ) echo 'checked'; ?> onclick="if( !this.checked ) { jQuery('#display_download_duration').removeAttr('checked'); }" /> <?php echo __('Include file size', 'powerpress'); ?>
	<input type="checkbox" style="margin-left: 30px;" id="display_download_duration" name="PlayerSettings[display_download_duration]" value="1" <?php if( $General['podcast_link'] == 3 ) echo 'checked'; ?> onclick="if( this.checked ) { jQuery('#display_download_size').attr('checked','checked'); }" /> <?php echo __('Include file size and duration', 'powerpress'); ?></p>
	
	<p><label><input type="checkbox" name="General[podcast_embed]" value="1" <?php if( !empty($General['podcast_embed']) ) echo 'checked '; ?>/> <?php echo __('Display Player Embed Link', 'powerpress'); ?> </label></p>
	<p style="margin-left: 35px;">
		<input type="checkbox" name="General[podcast_embed_in_feed]" value="1" <?php if( !empty($General['podcast_embed_in_feed']) ) echo 'checked'; ?>  /> <?php echo __('Include embed in feeds', 'powerpress'); ?>
	</p>
	<p><?php echo __('Embed option uses Flow Player Classic for audio and HTML5 Video player for video.', 'powerpress'); ?></p>
</td>
</tr>
</table>




<table class="form-table">
<tr valign="top">
<th scope="row" style="background-image: url(../wp-includes/images/smilies/icon_exclaim.gif); background-position: 10px 10px; background-repeat: no-repeat; ">

<div style="margin-left: 24px;"><?php echo __('Having Issues?', 'powerpress'); ?></div></th>
<td>
	<select name="General[player_aggressive]" class="bpp_input_med">
<?php
$linkoptions = array(0=>__('No, everything is working', 'powerpress'), 1=>__('Yes, please try to fix', 'powerpress') );
	
while( list($value,$desc) = each($linkoptions) )
	echo "\t<option value=\"$value\"". ($General['player_aggressive']==$value?' selected':''). ">$desc</option>\n";
	
?>
</select>
<p style="margin-top: 5px;">
	<?php echo __('Use this option if you are having problems with the players not appearing on some or all of your pages.', 'powerpress'); ?>
</p>
<p style="margin-top: 20px; margin-bottom:0;">
	<?php echo __('If the above option fixes the player issues, then you most likely have a conflicting theme or plugin activated. You can verify your theme is not causing the problem by testing your site using the default WordPress twentyelevent or twentytwelve theme. For plugins, disable them one by one until the player re-appears, which indicates the last plugin deactivated caused the conflict.', 'powerpress'); ?>
</p>
</td>
</tr>
</table>


<!-- start advanced features -->
<div id="new_window_settings" style="display: <?php echo ( $General['player_function']==1 || $General['player_function']==3 ?'block':'none'); ?>">
<h3><?php echo __('Play in New Window Settings', 'powerpress'); ?></h3>
<table class="form-table">

<tr valign="top">
<th scope="row">
<?php echo __('New Window Width', 'powerpress'); ?>
</th>
<td>
<input type="text" name="General[new_window_width]" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" value="<?php echo $General['new_window_width']; ?>" maxlength="4" />
<?php echo __('Width of new window (leave blank for 420 default)', 'powerpress'); ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('New Window Height', 'powerpress'); ?>
</th>
<td>
<input type="text" name="General[new_window_height]" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" value="<?php echo $General['new_window_height']; ?>" maxlength="4" />
<?php echo __('Height of new window (leave blank for 240 default)', 'powerpress'); ?>
</td>
</tr>
</table>
</div>
<!-- end advanced features -->

<h3><?php echo __('Media Format Settings', 'powerpress'); ?></h3>
<table class="form-table">

<tr valign="top">
<th scope="row">
<?php echo __('AAC Audio (.m4a)', 'powerpress'); ?>
</th>
<td>

	<p style="margin: 8px 0 0 0;">
		<input type="checkbox" name="General[m4a]" value="use_players" <?php if( !empty($General['m4a']) && $General['m4a'] == 'use_players' ) echo 'checked'; ?>  /> <?php echo __('Use Flow Player Classic / HTML5 Audio player', 'powerpress'); ?>
	</p>
	<div style="margin-left: 20px;"><?php echo __('Leave this option unchecked if you want m4a chapter markers, images and information displayed.', 'powerpress'); ?></div>
	<div style="margin: 10px 0 0 20px;"><?php echo __('When unchecked, m4a will be played with the quicktime video embed. Video player width/height settings apply.', 'powerpress'); ?></div>

</td>
</tr>
</table>


</div>
<!-- end presentation settings -->
<?php  
} // End powerpress_admin_appearance()


function powerpressadmin_welcome($GeneralSettings)
{
?>
<div>
	<div class="powerpress-welcome-news">
		<h2><?php echo __('Blubrry PowerPress and Community Podcast', 'powerpress'); ?></h2>
		<?php powerpressadmin_community_news(); ?>
		<p style="margin-bottom: 0; font-size: 85%;">
			<input type="checkbox" name="General[disable_dashboard_news]" value="1" <?php echo (empty($GeneralSettings['disable_dashboard_news'])?'':'checked'); ?> /> <?php echo __('Remove from dashboard', 'powerpress'); ?>
		</p>
	</div>
	<div class="powerpress-welcome-highlighted">
		<div>
			<h2><?php echo __('Highlighted Topics', 'powerpress'); ?></h2>
			<?php powerpressadmin_community_highlighted(); ?>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php
} // End powerpressadmin_welcome()

function powerpressadmin_edit_tv($FeedSettings = false, $feed_slug='podcast', $cat_ID=false)
{
	if( !isset($FeedSettings['parental_rating']) )
		$FeedSettings['parental_rating'] = '';

?>
<h3><?php echo __('T.V. Settings', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
 <?php echo __('Parental Rating', 'powerpress'); ?>  </th>
<td>
	<p><?php echo sprintf(__('A parental rating is used to display your content on %s applications available on Internet connected TV\'s. The TV Parental Rating applies to both audio and video media.', 'powerpress'), '<strong><a href="http://www.blubrry.com/roku_blubrry/" target="_blank">Blubrry</a></strong>'); ?></p>
<?php
	$Ratings = array(''=>__('No rating specified', 'powerpress'),
			'TV-Y'=>__('Children of all ages', 'powerpress'),
			'TV-Y7'=>__('Children 7 years and older', 'powerpress'),
			'TV-Y7-FV'=>__('Children 7 years and older [fantasy violence]', 'powerpress'),
			'TV-G'=>__('General audience', 'powerpress'),
			'TV-PG'=>__('Parental guidance suggested', 'powerpress'),
			'TV-14'=>__('May be unsuitable for children under 14 years of age', 'powerpress'),
			'TV-MA'=>__('Mature audience - may be unsuitable for children under 17', 'powerpress')
		);
	$RatingsTips = array(''=>'',
				'TV-Y'=>__('Whether animated or live-action, the themes and elements in this program are specifically designed for a very young audience, including children from ages 2-6. These programs are not expected to frighten younger children.  Examples of programs issued this rating include Sesame Street, Barney & Friends, Dora the Explorer, Go, Diego, Go! and The Backyardigans.', 'powerpress'),
				'TV-Y7'=>__('These shows may or may not be appropriate for some children under the age of 7. This rating may include crude, suggestive humor, mild fantasy violence, or content considered too scary or controversial to be shown to children under seven. Examples include Foster\'s Home for Imaginary Friends, Johnny Test, and SpongeBob SquarePants.', 'powerpress'),
				'TV-Y7-FV'=>__('When a show has noticeably more fantasy violence, it is assigned the TV-Y7-FV rating. Action-adventure shows such Pokemon series and the Power Rangers series are assigned a TV-Y7-FV rating.', 'powerpress'),
				'TV-G'=>__('Although this rating does not signify a program designed specifically for children, most parents may let younger children watch this program unattended. It contains little or no violence, no strong language and little or no sexual dialogue or situation. Networks that air informational, how-to content, or generally inoffensive content.', 'powerpress'),
				'TV-PG'=>__('This rating signifies that the program may be unsuitable for younger children without the guidance of a parent. Many parents may want to watch it with their younger children. Various game shows and most reality shows are rated TV-PG for their suggestive dialog, suggestive humor, and/or coarse language. Some prime-time sitcoms such as Everybody Loves Raymond, Fresh Prince of Bel-Air, The Simpsons, Futurama, and Seinfeld  usually air with a TV-PG rating.', 'powerpress'),
				'TV-14'=>__('Parents are strongly urged to exercise greater care in monitoring this program and are cautioned against letting children of any age watch unattended. This rating may be accompanied by any of the following sub-ratings:', 'powerpress'),
				'TV-MA'=>__('A TV-MA rating means the program may be unsuitable for those below 17. The program may contain extreme graphic violence, strong profanity, overtly sexual dialogue, very coarse language, nudity and/or strong sexual content. The Sopranos is a popular example.', 'powerpress')
		);
			
	
	while( list($rating,$title) = each($Ratings) )
	{
		$tip = $RatingsTips[ $rating ];
?>
	<div style="margin-bottom: 10px;"><label><input type="radio" name="Feed[parental_rating]" value="<?php echo $rating; ?>" <?php if( $FeedSettings['parental_rating'] == $rating) echo 'checked'; ?> /> <?php if( $rating ) { ?><strong><?php echo $rating; ?></strong><?php } else { ?><strong><?php echo htmlspecialchars($title); ?></strong><?php } ?></label>
	<?php if( $rating ) { ?>  <span style="margin-left: 8px;"><a href="#" class="powerpress-parental-rating-tip" id="rating_tip_<?php echo $rating; ?>"><?php echo htmlspecialchars($title); ?></a><?php } ?></span>
	<p style="margin: 5px 50px; display: none;" id="rating_tip_<?php echo $rating; ?>_p" class="powerpress-parental-rating-tip-p"><?php echo htmlspecialchars($tip); ?></p>
	</div>
	<?php
	}
?>
</td>
</tr>
</table>


<?php
}

?>