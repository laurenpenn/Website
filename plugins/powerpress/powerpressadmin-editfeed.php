<?php

if( !function_exists('add_action') )
	die("access denied.");
	
function powerpress_languages()
{
	// List copied from PodPress:
	$langs = array();
	$langs['af'] = __('Afrikaans', 'powerpress');
	$langs['sq'] = __('Albanian', 'powerpress');
	$langs['ar'] = __('Arabic', 'powerpress');
	$langs['ar-sa'] = __('Arabic (Saudi Arabia)', 'powerpress');
	$langs['ar-eg'] = __('Arabic (Egypt)', 'powerpress');
	$langs['ar-dz'] = __('Arabic (Algeria)', 'powerpress');
	$langs['ar-tn'] = __('Arabic (Tunisia)', 'powerpress');
	$langs['ar-ye'] = __('Arabic (Yemen)', 'powerpress');
	$langs['ar-jo'] = __('Arabic (Jordan)', 'powerpress');
	$langs['ar-kw'] = __('Arabic (Kuwait)', 'powerpress');
	$langs['ar-bh'] = __('Arabic (Bahrain)', 'powerpress');
	$langs['eu'] = __('Basque', 'powerpress');
	$langs['be'] = __('Belarusian', 'powerpress');
	$langs['bg'] = __('Bulgarian', 'powerpress');
	$langs['ca'] = __('Catalan', 'powerpress');
	$langs['zh-cn'] = __('Chinese (Simplified)', 'powerpress');
	$langs['zh-tw'] = __('Chinese (Traditional)', 'powerpress');
	$langs['hr'] = __('Croatian', 'powerpress');
	$langs['cs'] = __('Czech', 'powerpress');
	$langs['da'] = __('Danish', 'powerpress');
	$langs['nl'] = __('Dutch', 'powerpress');
	$langs['nl-be'] = __('Dutch (Belgium)', 'powerpress');
	$langs['nl-nl'] = __('Dutch (Netherlands)', 'powerpress');
	$langs['en'] = __('English', 'powerpress');
	$langs['en-au'] = __('English (Australia)', 'powerpress');
	$langs['en-bz'] = __('English (Belize)', 'powerpress');
	$langs['en-ca'] = __('English (Canada)', 'powerpress');
	$langs['en-ie'] = __('English (Ireland)', 'powerpress');
	$langs['en-jm'] = __('English (Jamaica)', 'powerpress');
	$langs['en-nz'] = __('English (New Zealand)', 'powerpress');
	$langs['en-ph'] = __('English (Phillipines)', 'powerpress');
	$langs['en-za'] = __('English (South Africa)', 'powerpress');
	$langs['en-tt'] = __('English (Trinidad)', 'powerpress');
	$langs['en-gb'] = __('English (United Kingdom)', 'powerpress');
	$langs['en-us'] = __('English (United States)', 'powerpress');
	$langs['en-zw'] = __('English (Zimbabwe)', 'powerpress');
	$langs['et'] = __('Estonian', 'powerpress');
	$langs['fo'] = __('Faeroese', 'powerpress');
	$langs['fi'] = __('Finnish', 'powerpress');
	$langs['fr'] = __('French', 'powerpress');
	$langs['fr-be'] = __('French (Belgium)', 'powerpress');
	$langs['fr-ca'] = __('French (Canada)', 'powerpress');
	$langs['fr-fr'] = __('French (France)', 'powerpress');
	$langs['fr-lu'] = __('French (Luxembourg)', 'powerpress');
	$langs['fr-mc'] = __('French (Monaco)', 'powerpress');
	$langs['fr-ch'] = __('French (Switzerland)', 'powerpress');
	$langs['gl'] = __('Galician', 'powerpress');
	$langs['gd'] = __('Gaelic', 'powerpress');
	$langs['de'] = __('German', 'powerpress');
	$langs['de-at'] = __('German (Austria)', 'powerpress');
	$langs['de-de'] = __('German (Germany)', 'powerpress');
	$langs['de-li'] = __('German (Liechtenstein)', 'powerpress');
	$langs['de-lu'] = __('German (Luxembourg)', 'powerpress');
	$langs['de-ch'] = __('German (Switzerland)', 'powerpress');
	$langs['el'] = __('Greek', 'powerpress');
	$langs['haw'] = __('Hawaiian', 'powerpress');
	$langs['he_IL'] = __('Hebrew', 'powerpress');
	$langs['hu'] = __('Hungarian', 'powerpress');
	$langs['is'] = __('Icelandic', 'powerpress');
	$langs['in'] = __('Indonesian', 'powerpress');
	$langs['ga'] = __('Irish', 'powerpress');
	$langs['it'] = __('Italian', 'powerpress');
	$langs['it-it'] = __('Italian (Italy)', 'powerpress');
	$langs['it-ch'] = __('Italian (Switzerland)', 'powerpress');
	$langs['ja'] = __('Japanese', 'powerpress');
	$langs['ko'] = __('Korean', 'powerpress');
	$langs['mk'] = __('Macedonian', 'powerpress');
	$langs['no'] = __('Norwegian', 'powerpress');
	$langs['pl'] = __('Polish', 'powerpress');
	$langs['pt'] = __('Portuguese', 'powerpress');
	$langs['pt-br'] = __('Portuguese (Brazil)', 'powerpress');
	$langs['pt-pt'] = __('Portuguese (Portugal)', 'powerpress');
	$langs['ro'] = __('Romanian', 'powerpress');
	$langs['ro-mo'] = __('Romanian (Moldova)', 'powerpress');
	$langs['ro-ro'] = __('Romanian (Romania)', 'powerpress');
	$langs['ru'] = __('Russian', 'powerpress');
	$langs['ru-mo'] = __('Russian (Moldova)', 'powerpress');
	$langs['ru-ru'] = __('Russian (Russia)', 'powerpress');
	$langs['sr'] = __('Serbian', 'powerpress');
	$langs['sk'] = __('Slovak', 'powerpress');
	$langs['sl'] = __('Slovenian', 'powerpress');
	$langs['es'] = __('Spanish', 'powerpress');
	$langs['es-ar'] = __('Spanish (Argentina)', 'powerpress');
	$langs['es-bo'] = __('Spanish (Bolivia)', 'powerpress');
	$langs['es-cl'] = __('Spanish (Chile)', 'powerpress');
	$langs['es-co'] = __('Spanish (Colombia)', 'powerpress');
	$langs['es-cr'] = __('Spanish (Costa Rica)', 'powerpress');
	$langs['es-do'] = __('Spanish (Dominican Republic)', 'powerpress');
	$langs['es-ec'] = __('Spanish (Ecuador)', 'powerpress');
	$langs['es-sv'] = __('Spanish (El Salvador)', 'powerpress');
	$langs['es-gt'] = __('Spanish (Guatemala)', 'powerpress');
	$langs['es-hn'] = __('Spanish (Honduras)', 'powerpress');
	$langs['es-mx'] = __('Spanish (Mexico)', 'powerpress');
	$langs['es-ni'] = __('Spanish (Nicaragua)', 'powerpress');
	$langs['es-pa'] = __('Spanish (Panama)', 'powerpress');
	$langs['es-py'] = __('Spanish (Paraguay)', 'powerpress');
	$langs['es-pe'] = __('Spanish (Peru)', 'powerpress');
	$langs['es-pr'] = __('Spanish (Puerto Rico)', 'powerpress');
	$langs['es-es'] = __('Spanish (Spain)', 'powerpress');
	$langs['es-uy'] = __('Spanish (Uruguay)', 'powerpress');
	$langs['es-ve'] = __('Spanish (Venezuela)', 'powerpress');
	$langs['sv'] = __('Swedish', 'powerpress');
	$langs['sv-fi'] = __('Swedish (Finland)', 'powerpress');
	$langs['sv-se'] = __('Swedish (Sweden)', 'powerpress');
	$langs['tr'] = __('Turkish', 'powerpress');
	$langs['uk'] = __('Ukranian', 'powerpress');
	return $langs;
}

function powerpress_admin_capabilities()
{
	global $wp_roles;
	
	$capnames = array();
	// Get Role List
	foreach($wp_roles->role_objects as $key => $role) {
		foreach($role->capabilities as $cap => $grant) {
			$capnames[$cap] = ucwords( str_replace('_', ' ',  $cap) );
		}
	}

	$capnames = array_unique($capnames);
	$remove_keys = array('level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5', 'level_6', 'level_7', 'level_8', 'level_9', 'level_10');
	while( list($null,$key) = each($remove_keys) )
		unset($capnames[ $key ]);
	asort($capnames);
	return $capnames;
}


// powerpressadmin_editfeed.php
function powerpress_admin_editfeed($feed_slug=false, $cat_ID =false)
{
	$SupportUploads = powerpressadmin_support_uploads();
	$General = powerpress_get_settings('powerpress_general');
	
	
	if( $feed_slug )
	{
		$FeedSettings = powerpress_get_settings('powerpress_feed_'.$feed_slug);
		if( !$FeedSettings )
		{
			$FeedSettings = array();
			$FeedSettings['title'] = '';
			if( !empty($General['custom_feeds'][$feed_slug]) )
				$FeedSettings['title'] = $General['custom_feeds'][$feed_slug];
		}
		$FeedSettings = powerpress_default_settings($FeedSettings, 'editfeed_custom');
		
		if( !isset($General['custom_feeds'][$feed_slug]) )
			$General['custom_feeds'][$feed_slug] = __('Podcast (default)', 'powerpress');
	}
	else if( $cat_ID )
	{
		$FeedSettings = powerpress_get_settings('powerpress_cat_feed_'.$cat_ID);
		$FeedSettings = powerpress_default_settings($FeedSettings, 'editfeed_custom');
	}
	else
	{
		$FeedSettings = powerpress_get_settings('powerpress_feed');
		$FeedSettings = powerpress_default_settings($FeedSettings, 'editfeed');
	}
		
	$FeedTitle = __('Feed Settings', 'powerpress');
	if( $feed_slug )
	{
		$FeedTitle = sprintf( 'Edit Podcast Channel: %s', $General['custom_feeds'][$feed_slug]);
		echo sprintf('<input type="hidden" name="feed_slug" value="%s" />', $feed_slug);
	}
	else if( $cat_ID )
	{
		$category = get_category_to_edit($cat_ID);
		$FeedTitle = sprintf( __('Edit Category Feed: %s', 'powerpress'), $category->name);
		echo sprintf('<input type="hidden" name="cat" value="%s" />', $cat_ID);
	}
	
		echo '<h2>'. $FeedTitle .'</h2>';
	
	if( $cat_ID && (isset($_GET['from_categories']) || isset($_POST['from_categories'])) )
	{
		echo '<input type="hidden" name="from_categories" value="1" />';
	}
	
?>
<div id="powerpress_settings_page" class="powerpress_tabbed_content"> 
  <ul class="powerpress_settings_tabs">
		<li><a href="#feed_tab_feed"><span><?php echo htmlspecialchars(__('Feed Settings', 'powerpress')); ?></span></a></li>
		<li><a href="#feed_tab_itunes"><span><?php echo htmlspecialchars(__('iTunes Settings', 'powerpress')); ?></span></a></li>
		<li><a href="#feed_tab_tv"><span><?php echo htmlspecialchars(__('T.V.', 'powerpress')); ?></span></a></li>
	<?php if( $feed_slug ) { ?>
		<li><a href="#feed_tab_appearance"><span><?php echo htmlspecialchars(__('Media Appearance', 'powerpress')); ?></span></a></li>
		<li><a href="#feed_tab_other"><span><?php echo htmlspecialchars(__('Other Settings', 'powerpress')); ?></span></a></li> 
	<?php } ?>
	<?php if( $cat_ID ) { ?>
		<li><a href="#feed_tab_other"><span><?php echo htmlspecialchars(__('Other Settings', 'powerpress')); ?></span></a></li> 
	<?php } ?>
  </ul>
	
	
	<div id="feed_tab_feed" class="powerpress_tab">
		<?php
		//powerpressadmin_edit_feed_general($FeedSettings, $General);
		//powerpressadmin_edit_feed_settings($FeedSettings, $General);
		powerpressadmin_edit_feed_settings($FeedSettings, $General, $cat_ID, $feed_slug );
		?>
	</div>
	
	<div id="feed_tab_itunes" class="powerpress_tab">
		<?php
		//powerpressadmin_edit_itunes_general($General);
		if( $feed_slug != 'podcast' )
			powerpressadmin_edit_itunes_general($General, $FeedSettings, $feed_slug, $cat_ID);
		powerpressadmin_edit_itunes_feed($FeedSettings, $General, $feed_slug, $cat_ID);
		?>
	</div>
	
	<div id="feed_tab_tv" class="powerpress_tab">
		<?php
		powerpressadmin_edit_tv($FeedSettings, $feed_slug);
		?>
	</div>
	
	<?php if( $feed_slug ) { ?>
	<div id="feed_tab_appearance" class="powerpress_tab">
		<?php
		//powerpressadmin_appearance($General);
		powerpressadmin_edit_appearance_feed($General, $FeedSettings, $feed_slug);
		?>
	</div>
	
	<div id="feed_tab_other" class="powerpress_tab">
		<?php
		powerpressadmin_edit_basics_feed($General, $FeedSettings, $feed_slug)
		?>
	</div>
	<?php } ?>
	
	<?php if( $cat_ID ) { ?>
	<div id="feed_tab_other" class="powerpress_tab">
		<?php
		powerpressadmin_edit_basics_feed($General, $FeedSettings, $feed_slug, $cat_ID)
		?>
	</div>
	<?php } ?>
	
</div>
<div class="clear"></div>
<?php




		//if( !$cat_ID && !$feed_slug )
		//	powerpressadmin_edit_feed_general($FeedSettings, $General);
		
		
		
		
}

function powerpressadmin_edit_podcast_channel($FeedSettings, $General)
{
	// TODO
?>
<input type="hidden" name="action" value="powerpress-save-customfeed" />
<p style="margin-bottom: 0;">
	<?php echo __('Configure your custom podcast feed.', 'powerpress'); ?>
</p>
<?php
}

function powerpressadmin_edit_category_feed($FeedSettings, $General)
{
?>
<input type="hidden" name="action" value="powerpress-save-categoryfeedsettings" />
<p style="margin-bottom: 0;">
	<?php echo __('Configure your category feed to support podcasting.', 'powerpress'); ?>
</p>
<?php
}

function powerpressadmin_edit_feed_general($FeedSettings, $General)
{
	$warning = '';
	$episode_count = powerpress_get_episode_count('podcast');
	if( $episode_count == 0 )
	{
		$warning = __('WARNING: You must create at least one podcast episode for your podcast feed to be valid.', 'powerpress');
	}
?>
<h3>Podcast Feeds</h3>
<table class="form-table">

<tr valign="top">
<th scope="row">

<?php echo __('Enhance Feeds', 'powerpress'); ?></th> 
<td>
	<ul>
		<li><label><input type="radio" name="Feed[apply_to]" value="1" <?php if( $FeedSettings['apply_to'] == 1 ) echo 'checked'; ?> /> <?php echo __('Enhance All Feeds', 'powerpress'); ?></label> (<?php echo __('Recommended', 'powerpress'); ?>)</li>
		<li>
			<ul>
				<li><?php echo __('Adds podcasting support to all feeds', 'powerpress'); ?></li>
				<li><?php echo __('Allows for Category Podcasting', 'powerpress'); ?> (<?php echo __('Visitors may subscribe to your categories as a podcast', 'powerpress'); ?>)</li>
				<li><?php echo __('Allows for Tag/Keyword Casting', 'powerpress'); ?> (<?php echo __('Visitors may subscribe to your tags as a podcast', 'powerpress'); ?>)</li>
			</ul>
		</li>
		<li><label><input type="radio" name="Feed[apply_to]" value="2" <?php if( $FeedSettings['apply_to'] == 2 ) echo 'checked'; ?> /> <?php echo __('Enhance Main Feed Only', 'powerpress'); ?></label></li>
		<li>
			<ul>
				<li><?php echo __('Adds podcasting support to your main feed only', 'powerpress'); ?></li>
			</ul>
		</li>
		<li><label><input type="radio" name="Feed[apply_to]" value="0" <?php if( $FeedSettings['apply_to'] == 0 ) echo 'checked'; ?> /> <?php echo __('Do Not Enhance Feeds', 'powerpress'); ?></label></li>
		<li>
			<ul>
				<li><?php echo __('Feed Settings below will only apply to your podcast channel feeds', 'powerpress'); ?></li>
			</ul>
		</li>
	</ul>
</td>
</tr>

<?php /* ?>
<tr valign="top">
<th scope="row">
<?php echo __('Main Site Feed', 'powerpress'); ?></th> 
<td>
	<p style="margin-top: 5px; margin-bottom: 0;"><?php echo __('Main RSS2 Feed', 'powerpress'); ?>: <a href="<?php echo get_bloginfo('rss2_url'); ?>" title="<?php echo __('Main RSS 2 Feed', 'powerpress'); ?>" target="_blank"><?php echo get_bloginfo('rss2_url'); ?></a> | <a href="http://www.feedvalidator.org/check.cgi?url=<?php echo urlencode(get_bloginfo('rss2_url')); ?>" target="_blank"><?php echo __('validate', 'powerpress'); ?></a></p>
	<p><?php echo __('Note: We do not recommend submitting your main site feed to podcast directories such as iTunes. iTunes and many other podcast directories work best with feeds that do not have regular blog posts mixed in.', 'powerpress');  ?></p>
</td>
</tr>
<?php */ ?>

<tr valign="top">
<th scope="row">

<?php echo __('Podcast Feeds', 'powerpress'); ?></th> 
<td>
<?php if( $warning ) { ?>
<span class="powerpress-error" style="background-color: #FFEBE8; border-color: #CC0000; padding: 6px 10px;"><?php echo $warning; ?></span>
<?php } ?>
<?php
	
	//$General = get_option('powerpress_general');
	$Feeds = array('podcast'=> __('Special Podcast only Feed', 'powerpress') );
	if( isset($General['custom_feeds']['podcast']) )
		$Feeds = $General['custom_feeds'];
	else if( isset($General['custom_feeds'])&& is_array($General['custom_feeds']) )
		$Feeds += $General['custom_feeds'];
		
	while( list($feed_slug, $feed_title) = each($Feeds) )
	{
		$edit_link = admin_url( 'admin.php?page=powerpress/powerpressadmin_customfeeds.php&amp;action=powerpress-editfeed&amp;feed_slug=') . $feed_slug;
?>
<p><?php echo $feed_title; ?>: <a href="<?php echo get_feed_link($feed_slug); ?>" title="<?php echo $feed_title; ?>" target="_blank"><?php echo get_feed_link($feed_slug); ?></a>
| <a href="http://www.feedvalidator.org/check.cgi?url=<?php echo urlencode(get_feed_link($feed_slug)); ?>" target="_blank"><?php echo __('validate', 'powerpress'); ?></a>
	<?php if( false && $feed_slug != 'podcast' ) { ?>
	| <a href="<?php echo $edit_link; ?>" title="<?php echo __('Edit Podcast Channel', 'powerpress'); ?>"><?php echo __('edit', 'powerpress'); ?></a>
	<?php } ?>
</p>
<?php } ?>
<p><?php echo __('These are podcast only feeds suitable for submission podcast directories such as iTunes.', 'powerpress'); ?></p>
<p><?php echo __('Note: We do not recommend submitting your main site feed to podcast directories such as iTunes. iTunes and many other podcast directories work best with feeds that do not have regular blog posts mixed in.', 'powerpress');  ?></p>

</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Feed Discovery', 'powerpress'); ?></th>
<td>

<p><label><input type="checkbox" name="General[feed_links]" value="1" <?php if( !empty($General['feed_links']) && $General['feed_links'] == 1 ) echo 'checked '; ?>/> <?php echo __('Include podcast feed links in HTML headers.', 'powerpress'); ?></label></p>
<p><?php echo __('Adds "feed discovery" links to your web site\'s headers allowing web browsers and feed readers to auto-detect your podcast feeds.', 'powerpress'); ?></p>
</td>
</tr>


</table>
<?php
}

function powerpressadmin_edit_feed_settings($FeedSettings, $General, $cat_ID = false, $feed_slug = false)
{
	$SupportUploads = powerpressadmin_support_uploads();
	if( !isset($FeedSettings['posts_per_rss']) )
		$FeedSettings['posts_per_rss'] = '';
	if( !isset($FeedSettings['rss2_image']) )
		$FeedSettings['rss2_image'] = '';
	if( !isset($FeedSettings['copyright']) )
		$FeedSettings['copyright'] = '';
	if( !isset($FeedSettings['title']) )
		$FeedSettings['title'] = '';
	if( !isset($FeedSettings['rss_language']) )
		$FeedSettings['rss_language'] = '';	
	
	if( $cat_ID || $feed_slug )
	{
?>
<h3><?php echo __('Feed Information', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Feed URL', 'powerpress'); ?>
</th>
<td>
<?php if( $cat_ID ) { ?>
<p style="margin-top: 0;"><a href="<?php echo get_category_feed_link($cat_ID); ?>" target="_blank"><?php echo get_category_feed_link($cat_ID); ?></a> | <a href="http://www.feedvalidator.org/check.cgi?url=<?php echo urlencode( str_replace('&amp;', '&', get_category_feed_link($cat_ID))); ?>" target="_blank"><?php echo __('validate', 'powerpress'); ?></a></p>
<?php } else { ?>
<p style="margin-top: 0;"><a href="<?php echo get_feed_link($feed_slug); ?>" target="_blank"><?php echo get_feed_link($feed_slug); ?></a><?php if( empty($FeedSettings['premium']) ) { ?> | <a href="http://www.feedvalidator.org/check.cgi?url=<?php echo urlencode(get_feed_link($feed_slug)); ?>" target="_blank"><?php echo __('validate', 'powerpress'); ?></a><?php } ?></p>
	<?php
		if( !empty($FeedSettings['premium']) )
		{
			echo __('WARNING: This feed is password protected, it cannot be accessed by public services such as feedvalidator.org or the iTunes podcast directory.', 'powerpress');
		} ?>
<?php } ?>
</td>
</tr>
</table>
<?php
	}
?>
<h3><?php echo __('Feed Settings', 'powerpress'); ?></h3>
<table class="form-table">

<?php
if( $feed_slug || $cat_ID )
{
?>
<tr valign="top">
<th scope="row">
<?php echo __('Feed Title', 'powerpress'); ?>
</th>
<td>
<input type="text" name="Feed[title]"style="width: 60%;"  value="<?php echo $FeedSettings['title']; ?>" maxlength="250" />
<?php if( $cat_ID ) { ?>
(<?php echo __('leave blank to use default category title', 'powerpress'); ?>)
<?php } else { ?>
(<?php echo __('leave blank to use blog title', 'powerpress'); ?>)
<?php } ?>
<?php if( $cat_ID ) { 
	$category = get_category_to_edit($cat_ID);
	$CategoryName = htmlspecialchars($category->name);
?>
<p><?php echo __('Default Category title:', 'powerpress') .' '. get_bloginfo_rss('name') . ' &#187; '. $CategoryName; ?></p>
<?php } else { ?>
<p><?php echo __('Blog title:', 'powerpress') .' '. get_bloginfo_rss('name'); ?></p>
<?php } ?>
</td>
</tr>
<tr valign="top">
<th scope="row">
<?php echo __('Feed Description', 'powerpress'); ?>
</th>
<td>
<input type="text" name="Feed[description]"style="width: 60%;"  value="<?php echo ( !empty($FeedSettings['description'])? $FeedSettings['description']:''); ?>" maxlength="1000" /> 
<?php if( $cat_ID ) { ?>
(<?php echo __('leave blank to use category description', 'powerpress'); ?>)
<?php } else { ?>
(<?php echo __('leave blank to use blog description', 'powerpress'); ?>)
<?php } ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Feed Landing Page URL', 'powerpress'); ?> <br />
</th>
<td>
<input type="text" name="Feed[url]"style="width: 60%;"  value="<?php echo ( !empty($FeedSettings['url'])? $FeedSettings['url']:''); ?>" maxlength="250" />
<?php if( $cat_ID ) { ?>
(<?php echo __('leave blank to use category page', 'powerpress'); ?>)
<?php } else { ?>
(<?php echo __('leave blank to use home page', 'powerpress'); ?>)
<?php } ?>
<?php if( $cat_ID ) { ?>
<p><?php echo __('Category page URL', 'powerpress'); ?>: <?php echo get_category_link($cat_ID); ?></p>
<?php } else { ?>
<p>e.g. <?php echo get_bloginfo('url'); ?>/custom-page/</p>
<?php } ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('FeedBurner Feed URL', 'powerpress'); ?>
</th>
<td>
<input type="text" name="Feed[feed_redirect_url]"style="width: 60%;"  value="<?php echo (!empty($FeedSettings['feed_redirect_url'])? $FeedSettings['feed_redirect_url']:''); ?>" maxlength="100" />  (<?php echo __('leave blank to use current feed', 'powerpress'); ?>)
<p><?php echo __('Use this option to redirect this feed to a hosted feed service such as FeedBurner.', 'powerpress'); ?></p>
<?php
if( $cat_ID )
	$link = get_category_feed_link($cat_ID);
else
	$link = get_feed_link($feed_slug);
	
if( strstr($link, '?') )
	$link .= "&redirect=no";
else
	$link .= "?redirect=no";
?>
<p><?php echo __('Bypass Redirect URL', 'powerpress'); ?>: <a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a></p>
</td>
</tr>

<?php } // End $feed_slug ?>

<tr valign="top">
<th scope="row">
<?php echo __('Show the most recent', 'powerpress'); ?>
</th>
<td>
<input type="text" name="Feed[posts_per_rss]"style="width: 50px;"  value="<?php echo ( !empty($FeedSettings['posts_per_rss'])? $FeedSettings['posts_per_rss']:''); ?>" maxlength="5" /> <?php echo __('episodes / posts per feed (leave blank to use blog default', 'powerpress'); ?>: <?php form_option('posts_per_rss'); ?>)
<?php if( !$feed_slug && !$cat_ID ) { ?>
<p style="margin-top: 5px; margin-bottomd: 0;"><?php echo __('Note: Setting above applies only to podcast channel feeds', 'powerpress'); ?></p>
<?php } ?>
<p style="margin-top: 5px; margin-bottomd: 0;"><?php echo __('WARNING: Setting this value larger than 10 may cause feed timeout errors and delay podcast directory listings from updating.', 'powerpress'); ?></p>
</td>
</tr>

<?php
	if( $feed_slug )
	{
?>
<tr valign="top">
<th scope="row">
<?php echo __('Feed Episode Maximizer', 'powerpress'); ?>  <?php echo powerpressadmin_new(); ?>
</th>
<td>
<input type="checkbox" name="Feed[maximize_feed]" value="1" <?php if( !empty($FeedSettings['maximize_feed']) ) echo 'checked'; ?> />
		<?php echo __('The latest 10 episodes in feed will remain as normal. The remaining 11+ older episodes in feed will have only the bare essential tags in order to maximize the number of episodes in the feed.', 'powerpress'); ?>
		
		<p style="margin-top: 0px; margin-bottomd: 0;"><?php echo powerpressadmin_notice( __('This feature is experimental, use at your own risk.', 'powerpress') ); ?></p>
		

<p style="margin-top: 0px; margin-bottomd: 0;"><?php echo __('NOTE: This feature may allow you to enter a larger value for the "Show the most recent" setting above. You must make sure that your feed does not exceed 512KB (1/2 MB) in size.', 'powerpress'); ?></p>
</td>
</tr>
<?php
	}
?>
<tr valign="top">
<th scope="row">
<?php echo __('RSS2 Image', 'powerpress'); ?> <br />
</th>
<td>
<input type="text" id="rss2_image" name="Feed[rss2_image]" style="width: 60%;" value="<?php echo ( !empty($FeedSettings['rss2_image'])? $FeedSettings['rss2_image']:''); ?>" maxlength="250" />
<a href="#" onclick="javascript: window.open( document.getElementById('rss2_image').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

<p><?php echo __('Place the URL to the RSS image above.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?> http://mysite.com/images/rss.jpg</p>

<p><?php echo __('RSS image should be at least 88 pixels wide and at least 31 pixels high in either .gif, .jpg and .png format.', 'powerpress'); ?></p>
<p><strong><?php echo __('A square image that is 300 x 300 pixel or larger in .jpg format is recommended.', 'powerpress'); ?></strong></p>

<?php if( $SupportUploads ) { ?>
<p><input name="rss2_image_checkbox" type="checkbox" onchange="powerpress_show_field('rss_image_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?></p>
<div style="display:none" id="rss_image_upload">
	<label for="rss2_image"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" name="rss2_image_file"  />
</div>
<?php } ?>
</td>
</tr>

<!-- start advanced features -->
<tr valign="top">
<th scope="row">

<?php echo __('Feed Language', 'powerpress'); ?></th>
<td>
<select name="Feed[rss_language]" class="bpp_input_med">
<?php
$Languages = powerpress_languages();

echo '<option value="">'. __('Blog Default Language', 'powerpress') .'</option>';
while( list($value,$desc) = each($Languages) )
	echo "\t<option value=\"$value\"". ($FeedSettings['rss_language']==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";
?>
</select>
<?php
	$rss_language = get_bloginfo_rss('language');
	$rss_language = strtolower($rss_language);
if( isset($Languages[ $rss_language ]) )
{
?>
 <?php echo __('Blog Default', 'powerpress'); ?>: <?php echo $Languages[ $rss_language ]; ?>
 <?php } else {  ?>
<?php echo __('Blog Default', 'powerpress'); ?>: <?php echo $rss_language; ?>
 <?php } ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Copyright', 'powerpress'); ?>
</th>
<td>
<input type="text" name="Feed[copyright]" style="width: 60%;" value="<?php echo $FeedSettings['copyright']; ?>" maxlength="250" />
</td>
</tr>
<!-- end advanced features -->

</table>

<!-- Location and frequency information -->
<?php
	if( !isset($FeedSettings['location']) )
		$FeedSettings['location'] = '';
	if( !isset($FeedSettings['frequency']) )
		$FeedSettings['frequency'] = '';
?>
<h3><?php echo __('Basic Show Information', 'powerpress'); ?></h3>
<div id="rawvoice_basic_options">
<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Location', 'powerpress'); ?></th> 
<td>
	<input type="text" style="width: 300px;" name="Feed[location]" value="<?php echo $FeedSettings['location']; ?>" maxlength="50" /> (<?php echo __('optional', 'powerpress'); ?>)
	<p><?php echo __('e.g. Cleveland, Ohio', 'powerpress'); ?></p>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php echo __('Episode Frequency', 'powerpress'); ?></th> 
<td>
	<input type="text" style="width: 300px;" name="Feed[frequency]" value="<?php echo $FeedSettings['frequency']; ?>" maxlength="50" /> (<?php echo __('optional', 'powerpress'); ?>)
	<p><?php echo __('e.g. Weekly', 'powerpress'); ?></p>
</td>
</tr>
</table>
</div>
<?php
}


function powerpressadmin_edit_basics_feed($General, $FeedSettings, $feed_slug, $cat_ID = false)
{
	if( !isset($FeedSettings['redirect']) )
		$FeedSettings['redirect'] = '';
	if( !isset($FeedSettings['premium_label']) )
		$FeedSettings['premium_label'] = '';

	if( $cat_ID )
	{
?>
	<h3><?php echo __('Media Statistics', 'powerpress'); ?></h3>
	<p>
	<?php echo __('Enter your Redirect URL issued by your media statistics service provider below.', 'powerpress'); ?>
	</p>

	<table class="form-table">
	<tr valign="top">
	<th scope="row">
	<?php echo __('Redirect URL', 'powerpress'); ?> 
	</th>
	<td>
	<input type="text" style="width: 60%;" name="Feed[redirect]" value="<?php echo $FeedSettings['redirect']; ?>" maxlength="250" />
	<p><?php echo __('Note: Category Media Redirect URL is applied to category feeds and pages only. The redirect will also apply to single pages if this is the only category associated with the blog post.', 'powerpress'); ?></p>
	</td>
	</tr>
	</table>
<?php
	}
	else // end if category, else channel...
	{
?>

<h3><?php echo __('Episode Entry Box', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Background Color', 'powerpress'); ?>
</th>
<td>
<input type="text" id="episode_background_color" name="EpisodeBoxBGColor[<?php echo $feed_slug; ?>]" style="width: 100px; float:left; border: 1px solid #333333; <?php if( !empty($General['episode_box_background_color'][ $feed_slug ]) ) echo 'background-color: '.$General['episode_box_background_color'][ $feed_slug ]; ?>;" value="<?php if( !empty($General['episode_box_background_color'][ $feed_slug ]) )  echo $General['episode_box_background_color'][ $feed_slug ]; ?>" maxlength="10" onblur="jQuery('#episode_background_color').css({'background-color' : this.value });" />
<div style="background-color: #FFDFEF;" class="powerpress_color_box" onclick="document.getElementById('episode_background_color').value='#FFDFEF'; jQuery('#episode_background_color').css({'background-color' :'#FFDFEF' });"></div>
<div style="background-color: #FBECD8;" class="powerpress_color_box" onclick="document.getElementById('episode_background_color').value='#FBECD8'; jQuery('#episode_background_color').css({'background-color' :'#FBECD8' });"></div>
<div style="background-color: #FFFFCC;" class="powerpress_color_box" onclick="document.getElementById('episode_background_color').value='#FFFFCC'; jQuery('#episode_background_color').css({'background-color' :'#FFFFCC' });"></div>
<div style="background-color: #DFFFDF;" class="powerpress_color_box" onclick="document.getElementById('episode_background_color').value='#DFFFDF'; jQuery('#episode_background_color').css({'background-color' :'#DFFFDF' });"></div>

<div style="background-color: #EBFFFF;" class="powerpress_color_box" onclick="document.getElementById('episode_background_color').value='#EBFFFF'; jQuery('#episode_background_color').css({'background-color' :'#EBFFFF' });"></div>
<div style="background-color: #D9E0EF;" class="powerpress_color_box" onclick="document.getElementById('episode_background_color').value='#D9E0EF'; jQuery('#episode_background_color').css({'background-color' :'#D9E0EF' });"></div>
<div style="background-color: #EBE0EB;" class="powerpress_color_box" onclick="document.getElementById('episode_background_color').value='#EBE0EB'; jQuery('#episode_background_color').css({'background-color' :'#EBE0EB' });"></div>
 &nbsp; (<?php echo __('leave blank for default', 'powerpress'); ?>)

<p class="clear"><?php echo __('Use a distinctive background color for this podcast channel\'s episode box.', 'powerpress'); ?></p>
</td>
</tr>
</table>

<!-- password protected feed option -->

<?php
		if( @$General['premium_caps'] && $feed_slug && $feed_slug != 'podcast' )
		{
?>
<h3><?php echo __('Password Protect Podcast Channel', 'powerpress'); ?></h3>
<p>
	<?php echo __('Require visitors to have membership to your blog in order to gain access to this channel\'s Premium Content.', 'powerpress'); ?>
</p>
<table class="form-table">
<tr valign="top">
<th scope="row">

<?php echo __('Protect Content', 'powerpress'); ?></th>
<td>
	<p style="margin-top: 5px;"><input type="checkbox" name="ProtectContent" value="1" <?php echo ( !empty($FeedSettings['premium']) ?'checked ':''); ?> onchange="powerpress_toggle_premium_content(this.checked);" /> <?php echo __('Require user to be signed-in to access feed.', 'powerpress'); ?></p>
<?php ?>
	<div style="margin-left: 20px; display: <?php echo ( !empty($FeedSettings['premium'])?'block':'none'); ?>;" id="premium_role"><?php echo __('User must have the following capability', 'powerpress'); ?>:
<select name="Feed[premium]" class="bpp_input_med">
<?php
			$caps = powerpress_admin_capabilities();
			$actual_premium_value = 'premium_content';
			if( !empty($FeedSettings['premium']) )
				$actual_premium_value = $FeedSettings['premium'];
			
			echo '<option value="">'.  __('None', 'powerpress') .'</option>';
			while( list($value,$desc) = each($caps) )
				echo "\t<option value=\"$value\"". ($actual_premium_value==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";
?>
</select></div>
</td>
</tr>
</table>
<div id="protected_content_message" style="display: <?php echo ( !empty($FeedSettings['premium'])?'block':'none'); ?>;">
<script language="Javascript" type="text/javascript"><!--
function powerpress_toggle_premium_content(enabled)
{
	jQuery('#premium_role').css('display', (enabled?'block':'none') );
	jQuery('#protected_content_message').css('display', (enabled?'block':'none') );
}	
function powerpress_premium_label_append_signin_link()
{
	jQuery('#premium_label').val( jQuery('#premium_label').val() + '<a href="<?php echo get_option('siteurl'); ?>/wp-login.php" title="<?php echo __('Sign In', 'powerpress'); ?>"><?php echo __('Sign In', 'powerpress'); ?><\/a>'); 
}
function powerpress_default_premium_label(event)
{
	if( confirm('<?php echo __('Use default label, are you sure?', 'powerpress'); ?>') )
	{
		jQuery('#premium_label_custom').css('display', (this.checked==false?'block':'none') );
		jQuery('#premium_label').val('');
	}
	else
	{
		return false;
	}
	return true;
}
//-->
</script>
	<table class="form-table">
	<tr valign="top">
	<th scope="row">
	<?php echo __('Unauthorized Label', 'powerpress'); ?>
	</th>
	<td>
	<p style="margin-top: 5px;"><input type="radio" name="PremiumLabel" value="0" <?php echo ($FeedSettings['premium_label']==''?'checked ':''); ?> onclick="return powerpress_default_premium_label(this)" />
		<?php echo __('Use default label', 'powerpress'); ?>:
	</p>
	<p style="margin-left: 20px;">
	<?php echo $FeedSettings['title']; ?>: <a href="<?php echo get_option('siteurl'); ?>/wp-login.php" target="_blank" title="Protected Content">(<?php echo __('Protected Content', 'powerpress'); ?>)</a>
	</p>
	<p style="margin-top: 5px;"><input type="radio" name="PremiumLabel" id="premium_label_1" value="1" <?php echo ($FeedSettings['premium_label']!=''?'checked ':''); ?> onchange="jQuery('#premium_label_custom').css('display', (this.checked?'block':'none') );" />
		<?php echo __('Use a custom label', 'powerpress'); ?>:
	</p>
	
	<div id="premium_label_custom" style="margin-left: 20px; display: <?php echo ($FeedSettings['premium_label']!=''?'block':'none'); ?>;">
	<textarea name="Feed[premium_label]" id="premium_label" style="width: 80%; height: 65px; margin-bottom: 0; padding-bottom: 0;"><?php echo htmlspecialchars($FeedSettings['premium_label']); ?></textarea>
		<div style="width: 80%; font-size: 85%; text-align: right;">
			<a href="#" onclick="powerpress_premium_label_append_signin_link();return false;"><?php echo __('Add sign in link to message', 'powerpress'); ?></a>
		</div>
		<p style="width: 80%;">
			<?php echo __('Label above appears in place of the in-page player and links when the current signed-in user does not have access to the protected content.', 'powerpress'); ?>
		</p>
	</div>
	</td>
	</tr>
	</table>
</div>
<?php
		}
		else if( !empty($General['premium_caps']) && $feed_slug )
		{
?>
<h3><?php echo __('Password Protect Podcast Channel', 'powerpress'); ?></h3>
<p>
	<?php echo __('This feature is not available for the default podcast channel.', 'powerpress'); ?>
</p>
<?php
		}
		
		// Podcast Channels and Custom Post Types...
		?>
<h3><?php echo __('Custom Post Types', 'powerpress'); ?></h3>
<p>
	<?php echo __('Set whether all post types or a specific custom post type may use this podcast channel. Custom post type must be of type \'Posts\'. Other post types such as \'Pages\' or \'Categories\' do not apply.', 'powerpress'); ?>
</p>
<table class="form-table">
<tr valign="top">
<th scope="row">

<?php echo __('Custom Post Type', 'powerpress'); ?></th>
<td>
<?php ?>
<select name="Feed[custom_post_type]" class="bpp_input_med">
<?php
			$post_types = powerpress_admin_get_post_types_by_capability_type('post');
			$custom_post_type = '';
			if( !empty($FeedSettings['custom_post_type']) )
				$custom_post_type = $FeedSettings['custom_post_type'];
			
			echo '<option value="">'. __('All Post Types (default)', 'powerpress') .'</option>';
			while( list($index,$value) = each($post_types) )
			{
				$desc = $value;
				// TODO: See if we can get a post type label somehow
				$postTypeObj = get_post_type_object($value);
				if( !empty($postTypeObj->labels->name ) )
					$desc = $postTypeObj->labels->name . ' ('. $value .')';
				echo "\t<option value=\"$value\"". ($custom_post_type==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";
			}
?>
</select>
<p>
	<?php echo __('Use the default setting if you do not understand custom post types.', 'powerpress'); ?>
</p>
</td>
</tr>
</table>
		<?php
		
	} // else if channel
}

function powerpressadmin_edit_appearance_feed($General,  $FeedSettings, $feed_slug)
{
	// Appearance Settings
?>
<h3><?php echo __('Appearance Settings', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Disable Player', 'powerpress'); ?>
</th>
<td>
	<input name="DisablePlayerFor" type="checkbox" <?php if( isset($General['disable_player'][$feed_slug]) ) echo 'checked '; ?> value="1" /> <?php echo __('Do not display web player or links for this podcast channel.', 'powerpress'); ?>
	<input type="hidden" name="UpdateDisablePlayer" value="<?php echo $feed_slug; ?>" />
</td>
</tr>
</table>
<?php

}

function powerpressadmin_edit_itunes_feed($FeedSettings, $General, $feed_slug=false, $cat_ID=false)
{
	$SupportUploads = powerpressadmin_support_uploads();
	if( !isset($FeedSettings['itunes_subtitle']) )
		$FeedSettings['itunes_subtitle'] = '';
	if( !isset($FeedSettings['itunes_summary']) )
		$FeedSettings['itunes_summary'] = '';
	if( !isset($FeedSettings['itunes_keywords']) )
		$FeedSettings['itunes_keywords'] = '';	
	if( !isset($FeedSettings['itunes_cat_1']) )
		$FeedSettings['itunes_cat_1'] = '';
	if( !isset($FeedSettings['itunes_cat_2']) )
		$FeedSettings['itunes_cat_2'] = '';
	if( !isset($FeedSettings['itunes_cat_3']) )
		$FeedSettings['itunes_cat_3'] = '';
	if( !isset($FeedSettings['itunes_explicit']) )
		$FeedSettings['itunes_explicit'] = 0;
	if( !isset($FeedSettings['itunes_talent_name']) )
		$FeedSettings['itunes_talent_name'] = '';
	if( !isset($FeedSettings['email']) )
		$FeedSettings['email'] = '';
	if( !isset($FeedSettings['itunes_new_feed_url_podcast']) )
		$FeedSettings['itunes_new_feed_url_podcast'] = '';
	if( !isset($FeedSettings['itunes_new_feed_url']) )
		$FeedSettings['itunes_new_feed_url'] = '';
	
?>
<h3><?php echo __('iTunes Feed Settings', 'powerpress'); ?></h3>

<?php
	$AdvancediTunesSettings = !empty($FeedSettings['itunes_summary']);
	if( !empty($FeedSettings['itunes_subtitle']) )
		$AdvancediTunesSettings = true;
	
	if( !$AdvancediTunesSettings ) {
?>
	<div style="margin-left: 230px; margin-bottom: 10px; font-weight: bold;"><a href="#" onclick="document.getElementById('advanced_itunes_settings').style.display='block';return false;"><?php echo __('Show Advanced iTunes Settings', 'powerpress'); ?></a></div>
<?php } ?>
<!-- start advanced features -->
<div id="advanced_itunes_settings" <?php echo ($AdvancediTunesSettings?'':'style="display: none;"'); ?>>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Program Subtitle', 'powerpress'); ?> <br />
</th>
<td>
<input type="text" name="Feed[itunes_subtitle]"style="width: 60%;"  value="<?php echo $FeedSettings['itunes_subtitle']; ?>" maxlength="250" />
</td>
</tr>

<tr valign="top">
<th scope="row">

<?php echo __('iTunes Program Summary', 'powerpress'); ?></th>
<td>
<p style="margin-top: 5px;"><?php echo __('Your summary may not contain HTML and cannot exceed 4,000 characters in length.', 'powerpress'); ?></p>

<textarea name="Feed[itunes_summary]" rows="5" style="width:80%;" ><?php echo $FeedSettings['itunes_summary']; ?></textarea>
</td>
</tr>

<tr valign="top">
<th scope="row">

<?php echo __('iTunes Episode Summary', 'powerpress'); ?></th>
<td>

<?php if ( version_compare( '5', phpversion(), '<=' ) ) { ?>
<div><input type="checkbox" name="Feed[enhance_itunes_summary]" value="1" <?php echo ( !empty($FeedSettings['enhance_itunes_summary'])?'checked ':''); ?>/> <?php echo __('Optimize iTunes Summary from Blog Posts', 'powerpress'); ?> (<?php echo __('We no longer recommend using this setting, see note below', 'powerpress'); ?>)
</div>
<p>
	<?php echo __('Creates a friendlier view of your post/episode content by converting web links and images to clickable links in the iTunes application.', 'powerpress'); ?>
</p>
<p><strong>
	<?php echo __('Note: With the recent launch of iTunes web site during the summer of 2010, Optimize iTunes Summary\'s clickable links do not appear online in the same way they do in the iTunes application. For this reason, we no longer recommend using this feature.', 'powerpress'); ?>
</strong></p>
<?php } else { ?>

	<strong><?php echo __('Option Not Available', 'powerpress'); ?></strong>

<p>
	<?php echo __('This feature requires PHP version 5 or newer.', 'powerpress'); ?>
	<?php echo sprintf( __('Your server\'s version of PHP is %s','powerpress'),phpversion() ); ?>. 
</p>
<?php } ?>
</td>
</tr>
</table>
</div>
<!-- end advanced features -->

<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Program Keywords', 'powerpress'); ?> <br />
</th>
<td>
<input type="text" name="Feed[itunes_keywords]" style="width: 60%;"  value="<?php echo $FeedSettings['itunes_keywords']; ?>" maxlength="250" />
<p><?php echo __('Enter up to 12 keywords separated by commas.', 'powerpress'); ?></p>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('iTunes Category', 'powerpress'); ?> 
</th>
<td>
<select name="Feed[itunes_cat_1]" class="bpp_input_med">
<?php

$MoreCategories = false;
if( !empty($FeedSettings['itunes_cat_2']) )
	$MoreCategories = true;
else if( !empty($FeedSettings['itunes_cat_3']) ) 
	$MoreCategories = true;

$Categories = powerpress_itunes_categories(true);

echo '<option value="">'. __('Select Category', 'powerpress') .'</option>';

while( list($value,$desc) = each($Categories) )
	echo "\t<option value=\"$value\"". ($FeedSettings['itunes_cat_1']==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";

reset($Categories);
?>
</select>
<?php
	if( !$MoreCategories ) { ?>
	<a href="#" onclick="document.getElementById('more_itunes_cats').style.display='block';return false;"><?php echo __('more', 'powerpress'); ?></a>
<?php } ?>
</td>
</tr>
</table>


<!-- start advanced features -->
<div id="more_itunes_cats" style="display: <?php echo ($MoreCategories?'block':'none'); ?>;">
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Category 2', 'powerpress'); ?> 
</th>
<td>
<select name="Feed[itunes_cat_2]" class="bpp_input_med">
<?php


echo '<option value="">'. __('Select Category', 'powerpress') .'</option>';

while( list($value,$desc) = each($Categories) )
	echo "\t<option value=\"$value\"". ($FeedSettings['itunes_cat_2']==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";

reset($Categories);

?>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('iTunes Category 3', 'powerpress'); ?> 
</th>
<td>
<select name="Feed[itunes_cat_3]" class="bpp_input_med">
<?php

echo '<option value="">'. __('Select Category', 'powerpress') .'</option>';

while( list($value,$desc) = each($Categories) )
	echo "\t<option value=\"$value\"". ($FeedSettings['itunes_cat_3']==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";

reset($Categories);
?>
</select>
</td>
</tr>
</table>
</div>
<!-- end advanced features -->


<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Explicit', 'powerpress'); ?> 
</th>
<td>
<select name="Feed[itunes_explicit]" class="bpp_input_med">
<?php
$explicit = array(0=> __('No - display nothing', 'powerpress'), 1=>__('Yes - explicit content', 'powerpress'), 2=>__('Clean - no explicit content', 'powerpress'));

while( list($value,$desc) = each($explicit) )
	echo "\t<option value=\"$value\"". ($FeedSettings['itunes_explicit']==$value?' selected':''). ">$desc</option>\n";

?>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('iTunes Image', 'powerpress'); ?> 
</th>
<td>
<input type="text" id="itunes_image" name="Feed[itunes_image]" style="width: 60%;" value="<?php echo ( !empty($FeedSettings['itunes_image'])? $FeedSettings['itunes_image']:''); ?>" maxlength="250" />
<a href="#" onclick="javascript: window.open( document.getElementById('itunes_image').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

<p><?php echo powerpressadmin_notice( __('iTunes image specifications changed in May, 2012', 'powerpress') ); ?></p>
<p><?php echo __('iTunes image should be at least 1400 x 1400 pixels in .jpg or .png format using RGB color space.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?>: http://example.com/images/itunes.jpg
 </p>

<p><strong><?php echo __('A square image that is 1400 x 1400 pixels in .jpg format is recommended.', 'powerpress'); ?></strong></p>

<p>
<?php echo __('This image is for your listing on the iTunes podcast directory and may also be used by other directories such as Zune and Blubrry. It is not the artwork that is displayed during episode playback. That artwork needs to be saved into the media file in the form of tags (ID3 tags for mp3) following the production of the media file.', 'powerpress'); ?>
</p>

<p><?php echo __('Note: If you change the iTunes image without changing the file name it may take some time (days or even months) for iTunes to update the image in the iTunes Podcast Directory.', 'powerpress'); ?> 
<?php echo sprintf( __('Please contact %s if you are having issues with your image changes not appearing in iTunes.', 'powerpress'), '<a href="http://www.apple.com/support/itunes/">'. __('iTunes Support', 'powerpress') .'</a>'); ?></p>
<?php if( $SupportUploads ) { ?>

<p><input name="itunes_image_checkbox" type="checkbox" onchange="powerpress_show_field('itunes_image_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?> &nbsp; 
	<span style="font-size:85%;">(<?php echo __('Using this option should update your image on iTunes within 24 hours', 'powerpress'); ?>)</span>
</p>
<div style="display:none" id="itunes_image_upload">
	<label for="itunes_image_file"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" name="itunes_image_file"  /><br />
	<div style="margin-left: 85px;"><input name="itunes_image_checkbox_as_rss" type="checkbox" value="1" /> <?php echo __('Also use as RSS image', 'powerpress'); ?></div>
</div>
<?php } ?>
</td>
</tr>


<!-- start advanced features -->
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Talent Name', 'powerpress'); ?> <br />
</th>
<td>
<input type="text" name="Feed[itunes_talent_name]" class="bpp_input_med" value="<?php echo $FeedSettings['itunes_talent_name']; ?>" maxlength="250" /><br />
<div><input type="checkbox" name="Feed[itunes_author_post]" value="1" <?php echo ( !empty($FeedSettings['itunes_author_post'])?'checked ':''); ?>/> <?php echo __('Use blog post author\'s name for individual episodes.', 'powerpress'); ?></div>

</td>
</tr>
<!-- end advanced features -->


<tr valign="top">
<th scope="row">
<?php echo __('iTunes Email', 'powerpress'); ?>
</th>
<td>
<input type="text" name="Feed[email]" class="bpp_input_med" value="<?php echo $FeedSettings['email']; ?>" maxlength="250" />
<div>(<?php echo __('iTunes will email this address when your podcast is accepted into the iTunes Directory.', 'powerpress'); ?>)</div>
</td>
</tr>

<!-- start advanced features -->
	<tr valign="top">
	<th scope="row" >

<?php echo __('iTunes New Feed URL', 'powerpress'); ?></th> 
	<td>
		<div id="new_feed_url_step_1" style="display: <?php echo ( !empty($FeedSettings['itunes_new_feed_url']) || !empty($FeedSettings['itunes_new_feed_url_podcast'])  ?'none':'block'); ?>;">
			 <p style="margin-top: 5px;"><strong><a href="#" onclick="return powerpress_new_feed_url_prompt();"><?php echo __('Set iTunes New Feed URL', 'powerpress'); ?></a></strong></p>
			 <p><strong>
			 <?php echo __('The iTunes New Feed URL option works primarily for Apple\'s iTunes application only, and should only be used if you are unable to implement a HTTP 301 redirect.', 'powerpress'); ?>
			 <?php echo __('A 301 redirect will route <u>all podcast clients including iTunes</u> to your new feed address.', 'powerpress'); ?>
			 </strong> 
			 </p>
			 <p>
			 <?php echo __('Learn more:', 'powerpress'); ?> <a href="http://www.podcastfaq.com/syndicating-your-podcast/changing-your-podcast-rss-feed-address-url/" target="_blank"><?php echo __('Changing Your Podcast RSS Feed Address (URL)', 'powerpress'); ?></a>
			</p>
		</div>
		<div id="new_feed_url_step_2" style="display: <?php echo ( !empty($FeedSettings['itunes_new_feed_url']) || !empty($FeedSettings['itunes_new_feed_url_podcast'])  ?'block':'none'); ?>;">
			<p style="margin-top: 5px;"><strong><?php echo __('WARNING: Changes made here are permanent. If the New Feed URL entered is incorrect, you will lose subscribers and will no longer be able to update your listing in the iTunes Store.', 'powerpress'); ?></strong></p>
			<p><strong><?php echo __('DO NOT MODIFY THIS SETTING UNLESS YOU ABSOLUTELY KNOW WHAT YOU ARE DOING.', 'powerpress'); ?></strong></p>
			<p>
				<?php echo htmlspecialchars( sprintf(__('Apple recommends you maintain the %s tag in your feed for at least two weeks to ensure that most subscribers will receive the new New Feed URL.', 'powerpress'), '<itunes:new-feed-url>' ) ); ?>
			</p>
			<p>
			<?php 
			$FeedName = __('Main RSS2 feed', 'powerpress');
			$FeedURL = get_feed_link('rss2');
			if( $cat_ID )
			{
				$category = get_category_to_edit($cat_ID);
				$FeedName = sprintf( __('%s category feed', 'powerpress'), htmlspecialchars($category->name) );
				$FeedURL = get_category_feed_link($cat_ID);
			}
			else if( $feed_slug )
			{
				if( !empty($General['custom_feeds'][ $feed_slug ]) )
					$FeedName = $General['custom_feeds'][ $feed_slug ];
				else
					$FeedName = __('Podcast', 'powerpress');
				$FeedName = trim($FeedName).' '.__('feed', 'powerpress');
				$FeedURL = get_feed_link($feed_slug);
			}
			
			echo sprintf(__('The New Feed URL value below will be applied to the %s (%s).', 'powerpress'), $FeedName, $FeedURL);
?>
			</p>
			<p style="margin-bottom: 0;">
				<label style="width: 25%; float:left; display:block; font-weight: bold;"><?php echo __('New Feed URL', 'powerpress'); ?></label>
				<input type="text" name="Feed[itunes_new_feed_url]"style="width: 55%;"  value="<?php echo $FeedSettings['itunes_new_feed_url']; ?>" maxlength="250" />
			</p>
			<p style="margin-left: 25%;margin-top: 0;font-size: 90%;">(<?php echo __('Leave blank for no New Feed URL', 'powerpress'); ?>)</p>
			
			<p><a href="http://www.apple.com/itunes/whatson/podcasts/specs.html#changing" target="_blank"><?php echo __('More information regarding the iTunes New Feed URL is available here.', 'powerpress'); ?></a></p>
			<p>
<?php
			if( !$cat_ID && !$feed_slug )
			{
				if( empty($General['channels']) )
					echo sprintf(__('Please activate the \'Custom Podcast Channels\' Advanced Option to set the new-feed-url for your podcast only feed (%s)', 'powerpress'), get_feed_link('podcast') );
				else
					echo sprintf(__('Please navigate to the \'Custom Podcast Channels\' section to set the new-feed-url for your podcast only feed (%s)', 'powerpress'), get_feed_link('podcast') );
			}
?>
			</p>
		</div>
	</td>
	</tr>
</table>
	<!-- end advanced features -->
<?php if( defined('POWERPRESS_NOT_SUPPORTED') ) { // start powerpress not supported features ?>
<fieldset style="border: 1px dashed #333333;">
<legend style="margin: 0 20px; padding: 0 5px; font-weight: bold;"><?php echo __('Features Not Supported by PowerPress', 'powerpress');  ?></legend>

	<div style="margin-left: 230px; margin-bottom: 10px;">
		<p>
			<strong style="color: #CC0000; font-weight: bold;"><?php echo __('USE THE FOLLOWING SETTINGS AT YOUR OWN RISK.', 'powerpress'); ?></strong>
		</p>
		<p style="margin-bottom: 0;">
			<?php echo __('Feeds affected', 'powerpress'); ?>: 
		</p>
		<div style="margin-left: 20px;">
			<?php
			// $General, $feed_slug=false, $cat_ID=false
			
			if( $feed_slug )
			{
				echo '<a href="';
				echo get_feed_link($feed_slug);
				echo '" target="_blank">';
				echo get_feed_link($feed_slug);
				echo '</a>';
			}
			else if( $cat_ID )
			{
				echo '<a href="';
				echo get_category_feed_link( $cat_ID );
				echo '" target="_blank">';
				echo get_category_feed_link( $cat_ID );
				echo '</a>';
			}
			else
			{
				echo '<a href="';
				echo get_feed_link('feed');
				echo '" target="_blank">';
				echo get_feed_link('feed');
				echo '</a>';
				
				if( empty($General['custom_feeds']['podcast']) )
				{
					echo '<br /><a href="';
					echo get_feed_link('podcast');
					echo '" target="_blank">';
					echo get_feed_link('podcast');
					echo '</a>';
				}
			}
			
			?>
		</div>
		
	</div>
<!-- start advanced features -->
<div id="permanent_itunes_settings">
<table class="form-table">
	
	<tr valign="top">
	<th scope="row" >

<?php echo __('iTunes Block', 'powerpress'); ?></th> 
	<td>
		<input type="checkbox" name="Feed[itunes_block]" value="1" <?php if( !empty($FeedSettings['itunes_block']) ) echo 'checked'; ?> />
		<?php echo __('Prevent the entire podcast from appearing in the iTunes Podcast directory.', 'powerpress'); ?>
	</td>
	</tr>
	
	<tr valign="top">
	<th scope="row" >

<?php echo __('iTunes Complete', 'powerpress'); ?></th> 
	<td>
		<input type="checkbox" name="Feed[itunes_complete]" value="1" <?php if( !empty($FeedSettings['itunes_complete']) ) echo 'checked'; ?> />
		<?php echo __('Indicate the completion of a podcast. iTunes will no longer update your listing in the iTunes Podcast directory.', 'powerpress'); ?>
	</td>
	</tr>
</table>
</div>
</fieldset>
<?php } // End PowerPress not supported features ?>
<?php
}
	
?>