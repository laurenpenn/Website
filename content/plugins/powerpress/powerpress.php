<?php
/*
Plugin Name: Blubrry PowerPress
Plugin URI: http://www.blubrry.com/powerpress/
Description: <a href="http://www.blubrry.com/powerpress/" target="_blank">Blubrry PowerPress</a> adds podcasting support to your blog. Features include: media player, 3rd party statistics, iTunes integration, Blubrry Services (Media Statistics and Hosting) integration and a lot more.
Version: 4.0.7
Author: Blubrry
Author URI: http://www.blubrry.com/
Change Log:
	Please see readme.txt for detailed change log.

Contributors:
	Angelo Mandato, CIO RawVoice - Plugin founder, architect and lead developer
	Pat McSweeny, Developer for RawVoice - Developed initial version (v0.1.0) of plugin
	Jerry Stephens, Way of the Geek (http://wayofthegeek.org/) - Contributed initial code fix for excerpt bug resolved in v0.6.1
	
Credits:
	getID3(), License: GPL 2.0+ by James Heinrich <info [at] getid3.org> http://www.getid3.org
		Note: getid3.php analyze() function modified to prevent redundant filesize() function call.
	FlowPlayer, License: GPL 3.0+ http://flowplayer.org/; source: http://flowplayer.org/download.html
	flashembed(), License: MIT by Tero Piirainen (tipiirai [at] gmail.com)
		Note: code found at bottom of player.js
	
Copyright 2008-2011 RawVoice Inc. (http://www.rawvoice.com)

License: GPL (http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt)

	This project uses source that is GPL licensed.
*/


if( !function_exists('add_action') )
	die("access denied.");
	
// WP_PLUGIN_DIR (REMEMBER TO USE THIS DEFINE IF NEEDED)
define('POWERPRESS_VERSION', '4.0.7' );

// Translation support:
if ( !defined('POWERPRESS_ABSPATH') )
	define('POWERPRESS_ABSPATH', dirname(__FILE__) );

// Translation support loaded:
load_plugin_textdomain('powerpress', // domain / keyword name of plugin
		POWERPRESS_ABSPATH .'/languages', // Absolute path
		basename(POWERPRESS_ABSPATH).'/languages' ); // relative path in plugins folder

/////////////////////////////////////////////////////
// The following define options should be placed in your
// wp-config.php file so the setting is not disrupted when
// you upgrade the plugin.
/////////////////////////////////////////////////////

// Set specific play and download labels for your installation of PowerPress
if( !defined('POWERPRESS_LINKS_TEXT') )
	define('POWERPRESS_LINKS_TEXT', __('Podcast', 'powerpress') );
if( !defined('POWERPRESS_DURATION_TEXT') )
	define('POWERPRESS_DURATION_TEXT', __('Duration', 'powerpress') );
if( !defined('POWERPRESS_PLAY_IN_NEW_WINDOW_TEXT') )
	define('POWERPRESS_PLAY_IN_NEW_WINDOW_TEXT', __('Play in new window', 'powerpress') );	
if( !defined('POWERPRESS_DOWNLOAD_TEXT') )
	define('POWERPRESS_DOWNLOAD_TEXT', __('Download', 'powerpress') );	
if( !defined('POWERPRESS_PLAY_TEXT') )
	define('POWERPRESS_PLAY_TEXT', __('Play', 'powerpress') );
if( !defined('POWERPRESS_EMBED_TEXT') )	
	define('POWERPRESS_EMBED_TEXT', __('Embed', 'powerpress') );
if( !defined('POWERPRESS_READ_TEXT') )	
	define('POWERPRESS_READ_TEXT', __('Read', 'powerpress') );
	
if( !defined('POWERPRESS_BLUBRRY_API_URL') )
	define('POWERPRESS_BLUBRRY_API_URL', 'http://api.blubrry.com/');
	
// Display custom play image for quicktime media. Applies to on page player only.
//define('POWERPRESS_PLAY_IMAGE', 'http://www.blubrry.com/themes/blubrry/images/player/PlayerBadge150x50NoBorder.jpg');

if( !defined('POWERPRESS_CONTENT_ACTION_PRIORITY') )
	define('POWERPRESS_CONTENT_ACTION_PRIORITY', 10 );
	
// Added so administrators can customize what capability is needed for PowerPress
if( !defined('POWERPRESS_CAPABILITY_MANAGE_OPTIONS') )
	define('POWERPRESS_CAPABILITY_MANAGE_OPTIONS', 'manage_options');
if( !defined('POWERPRESS_CAPABILITY_EDIT_PAGES') )
	define('POWERPRESS_CAPABILITY_EDIT_PAGES', 'edit_pages');
	
//define('POWERPRESS_ENQUEUE_SCRIPTS', true); // Add this define to your wp-config.php if you want the audio.js enqueued with other plugin scripts in your blog.

//define('POWERPRESS_ENABLE_HTTPS_MEDIA', true); // Add this define to your wp-config.php if you wnat to allow media URLs that begin with https://

// Define variables, advanced users could define these in their own wp-config.php so lets not try to re-define
if( !defined('POWERPRESS_LINK_SEPARATOR') )
	define('POWERPRESS_LINK_SEPARATOR', '|');
if( !defined('POWERPRESS_TEXT_SEPARATOR') )
	define('POWERPRESS_TEXT_SEPARATOR', ':');
if( !defined('POWERPRESS_PLAY_IMAGE') )
	define('POWERPRESS_PLAY_IMAGE', 'play_video_default.jpg');
if( !defined('PHP_EOL') )
	define('PHP_EOL', "\n"); // We need this variable defined for new lines.

// Set regular expression values for determining mobile devices
if( !defined('POWERPRESS_MOBILE_REGEX') )
	define('POWERPRESS_MOBILE_REGEX', 'iphone|ipod|ipad|aspen|android|blackberry|opera mini|webos|incognito|webmate|silk');
	
$powerpress_feed = NULL; // DO NOT CHANGE

function powerpress_content($content)
{
	global $post, $g_powerpress_excerpt_post_id;
	
	if( defined('PODPRESS_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return $content;
		
	if( empty($post->ID) || !is_object($post) )
		return $content;
		
	if( defined('POWERPRESS_DO_ENCLOSE_FIX') )
		$content = preg_replace('/\<!--.*added by PowerPress.*-->/im', '', $content );
	
	if( is_feed() )
		return $content; // We don't want to do anything to the feed
		
	if( function_exists('post_password_required') )
	{
		if( post_password_required($post) )
			return $content;
	}
	
	// PowerPress settings:
	$GeneralSettings = get_option('powerpress_general');
	
	// No player or links to add to content...
	if( !empty($GeneralSettings['disable_appearance']) )
		return $content;
		
	// check for themes/plugins where we know we need to do this...
	if( !empty($GLOBALS['fb_ver']) && version_compare($GLOBALS['fb_ver'], '1.0',  '<=')	) {
		$GeneralSettings['player_aggressive'] = true;
	}
	if( defined('JETPACK__VERSION') && version_compare(JETPACK__VERSION, '2.0',  '>=')	) {
		$GeneralSettings['player_aggressive'] = true;
	}
	
	if( !empty($GeneralSettings['player_aggressive']) )
	{
		if( strstr($content, '<!--powerpress_player-->') !== false )
			return $content; // The players were already added to the content
		
		if( $g_powerpress_excerpt_post_id > 0 )
			$g_powerpress_excerpt_post_id = 0; // Hack, set this to zero so it always goes past...
	}
	
	// Problem: If the_excerpt is used instead of the_content, both the_exerpt and the_content will be called here.
	// Important to note, get_the_excerpt will be called before the_content is called, so we add a simple little hack
	if( current_filter() == 'get_the_excerpt' )
	{
		$g_powerpress_excerpt_post_id = $post->ID;
		return $content; // We don't want to do anything to this content yet...
	}
	else if( current_filter() == 'the_content' && $g_powerpress_excerpt_post_id == $post->ID )
	{
		return $content; // We don't want to do anything to this excerpt content in this call either...
	}
	
	
	if( !isset($GeneralSettings['custom_feeds']) )
    $GeneralSettings['custom_feeds'] = array('podcast'=>'Default Podcast Feed');
	
	// Re-order so the default podcast episode is the top most...
	$Temp = $GeneralSettings['custom_feeds'];
	$GeneralSettings['custom_feeds'] = array();
	$GeneralSettings['custom_feeds']['podcast'] = 'Default Podcast Feed';
	while( list($feed_slug, $feed_title) = each($Temp) )
	{
		if( $feed_slug == 'podcast' )
			continue;
		$GeneralSettings['custom_feeds'][ $feed_slug ] = $feed_title;
	}
	
	if( !isset($GeneralSettings['display_player']) )
			$GeneralSettings['display_player'] = 1;
	if( !isset($GeneralSettings['player_function']) )
		$GeneralSettings['player_function'] = 1;
	if( !isset($GeneralSettings['podcast_link']) )
		$GeneralSettings['podcast_link'] = 1;
	
	// The blog owner doesn't want anything displayed, so don't bother wasting anymore CPU cycles
	if( $GeneralSettings['display_player'] == 0 )
		return $content;
		
	if( current_filter() == 'the_excerpt' && !$GeneralSettings['display_player_excerpt'] )
		return $content; // We didn't want to modify this since the user didn't enable it for excerpts
		
	// Figure out which players are alerady in the body of the page...
	$ExcludePlayers = array();
	if( isset($GeneralSettings['disable_player']) )
		$ExcludePlayers = $GeneralSettings['disable_player']; // automatically disable the players configured
		
	if( !empty($GeneralSettings['process_podpress']) && strstr($content, '[display_podcast]') )
		return $content;
	
	if( preg_match_all('/(.?)\[(powerpress)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s', $content, $matches) )
	{
		if( isset($matches[3]) )
		{
			while( list($key,$row) = each($matches[3]) )
			{
				$attributes = shortcode_parse_atts($row);
				if( isset($attributes['url']) )
				{
					// not a problem...
				}
				else if( isset($attributes['feed']) )
				{
					// we want to exclude this feed from the links aera...
					$ExcludePlayers[ $attributes['feed'] ] = true;
				}
				else
				{
					// we don't want to include any players below...
					$ExcludePlayers = $GeneralSettings['custom_feeds'];
				}
			}
		}
	}
	
	// LOOP HERE TO DISPLAY EACH MEDIA TYPE
	$new_content = '';
	while( list($feed_slug,$feed_title) = each($GeneralSettings['custom_feeds']) )
	{
		// Get the enclosure data
		$EpisodeData = powerpress_get_enclosure_data($post->ID, $feed_slug);
		
		if( !$EpisodeData && !empty($GeneralSettings['process_podpress']) && $feed_slug == 'podcast' )
			$EpisodeData = powerpress_get_enclosure_data_podpress($post->ID);
		
		if( !$EpisodeData || !$EpisodeData['url'] )
			continue;
	
		// Just in case, if there's no URL lets escape!
		if( !$EpisodeData['url'] )
			continue;
		
		// If the player is not already inserted in the body of the post using the shortcode...
		//if( preg_match('/\[powerpress(.*)\]/is', $content) == 0 )
		if( !isset($ExcludePlayers[ $feed_slug ]) ) // If the player is not in our exclude list because it's already in the post body somewhere...
		{
			if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] && !powerpress_premium_content_authorized($feed_slug) )
			{
				$new_content .=  powerpress_premium_content_message($post->ID, $feed_slug, $EpisodeData);
			}
			else
			{
				if( $GeneralSettings['player_function'] != 3 && $GeneralSettings['player_function'] != 0 ) // Play in new window only or disabled
				{
					$AddDefaultPlayer = empty($EpisodeData['no_player']);
					
					if( $EpisodeData && !empty($EpisodeData['embed']) )
					{
						$new_content .=  trim($EpisodeData['embed']);
						if( !empty($GeneralSettings['embed_replace_player']) )
							$AddDefaultPlayer = false;
					}
						
					if( $AddDefaultPlayer )
					{
						$image = '';
						if( isset($EpisodeData['image']) && $EpisodeData['image'] != '' )
							$image = $EpisodeData['image'];
						
						$new_content .= apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
					}
				}
				
				if( !isset($EpisodeData['no_links']) )
					$new_content .= apply_filters('powerpress_player_links', '',  powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
					//$new_content .= powerpress_get_player_links($post->ID, $feed_slug, $EpisodeData);
			}
		}
	}
	
	if( $new_content == '' )
		return $content;
		
	switch( $GeneralSettings['display_player'] )
	{
		case 1: { // Below posts
			return $content.$new_content.( !empty($GeneralSettings['player_aggressive']) ?'<!--powerpress_player-->':'');
		}; break;
		case 2: { // Above posts
			return ( !empty($GeneralSettings['player_aggressive']) ?'<!--powerpress_player-->':'').$new_content.$content;
		}; break;
	}
	return $content;
}//end function

add_filter('get_the_excerpt', 'powerpress_content', (POWERPRESS_CONTENT_ACTION_PRIORITY - 1) );
add_filter('the_content', 'powerpress_content', POWERPRESS_CONTENT_ACTION_PRIORITY);
add_filter('the_excerpt', 'powerpress_content', POWERPRESS_CONTENT_ACTION_PRIORITY);

function powerpress_header()
{
	// PowerPress settings:
	$Powerpress = get_option('powerpress_general');
	if( !isset($Powerpress['custom_feeds']) )
    $Powerpress['custom_feeds'] = array('podcast'=>'Default Podcast Feed');
	
	if( empty($Powerpress['disable_appearance']) || $Powerpress['disable_appearance'] == false )
	{
		if( !isset($Powerpress['player_function']) || $Powerpress['player_function'] > 0 ) // Don't include the player in the header if it is not needed...
		{
			$PowerpressPluginURL = powerpress_get_root_url();
			if( !defined('POWERPRESS_ENQUEUE_SCRIPTS') )
			{
				echo "<script type=\"text/javascript\" src=\"". powerpress_get_root_url() ."player.js\"></script>\n";
			}
?>
<script type="text/javascript"><!--
<?php
		$new_window_width = 420;
		$new_window_height = 240;

		if( isset($Powerpress['new_window_width']) && $Powerpress['new_window_width'] > 100 )
			$new_window_width = $Powerpress['new_window_width'];
		if( isset($Powerpress['new_window_height']) && $Powerpress['new_window_height'] > 40 )
			$new_window_height = $Powerpress['new_window_height'];
?>
function powerpress_pinw(pinw){window.open('<?php echo get_bloginfo('url'); ?>/?powerpress_pinw='+pinw, 'PowerPressPlayer','toolbar=0,status=0,resizable=1,width=<?php echo ($new_window_width + 40); ?>,height=<?php echo ($new_window_height + 80); ?>');	return false;}
powerpress_url = '<?php echo powerpress_get_root_url(); ?>';
//-->
</script>
<?php
		}
	}
	
	if( !empty($Powerpress['feed_links']) )
	{
		// Loop through podcast feeds and display them here 
		while( list($feed_slug, $title) = each($Powerpress['custom_feeds']) )
		{
			$href = get_feed_link($feed_slug);
			if ( isset($title) && isset($href) )
				echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr( $title ) . '" href="' . esc_url( $href ) . '" />' . "\n";
		}
		reset($Powerpress['custom_feeds']);
	}
}

add_action('wp_head', 'powerpress_header');

function powerpress_rss2_ns()
{
	if( !powerpress_is_podcast_feed() )
		return;
	
	// Okay, lets add the namespace
	echo 'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"'.PHP_EOL;
	if( !defined('POWERPRESS_RAWVOICE_RSS') || POWERPRESS_RAWVOICE_RSS != false )
	{
		echo 'xmlns:rawvoice="http://www.rawvoice.com/rawvoiceRssModule/"'.PHP_EOL;
	}
}

add_action('rss2_ns', 'powerpress_rss2_ns');

function powerpress_rss2_head()
{
	global $powerpress_feed;
	
	if( !powerpress_is_podcast_feed() )
		return; // Not a feed we manage
	
	$feed_slug = get_query_var( 'feed' );
	$cat_ID = get_query_var('cat');
	
	$Feed = get_option('powerpress_feed'); // Get the main feed settings
	if( is_category() )
	{
		$CustomFeed = get_option('powerpress_cat_feed_'.$cat_ID); // Get the custom podcast feed settings saved in the database
		if( $CustomFeed )
			$Feed = powerpress_merge_empty_feed_settings($CustomFeed, $Feed);
	}
	else if( powerpress_is_custom_podcast_feed() ) // If we're handling a custom podcast feed...
	{
		$CustomFeed = get_option('powerpress_feed_'.$feed_slug); // Get the custom podcast feed settings saved in the database
		$Feed = powerpress_merge_empty_feed_settings($CustomFeed, $Feed);
	}
	
	if( !isset($Feed['url']) || trim($Feed['url']) == '' )
	{
		if( is_category() )
			$Feed['url'] = get_category_link($cat_ID);
		else
			$Feed['url'] = get_bloginfo('url');
	}
	
	$General = get_option('powerpress_general');
	
	// We made it this far, lets write stuff to the feed!
	echo '<!-- podcast_generator="Blubrry PowerPress/'. POWERPRESS_VERSION .'" -->'.PHP_EOL;
		
	// add the itunes:new-feed-url tag to feed
	if( powerpress_is_custom_podcast_feed() )
	{
		if( !empty($Feed['itunes_new_feed_url']) )
		{
			$Feed['itunes_new_feed_url'] = str_replace('&amp;', '&', $Feed['itunes_new_feed_url']);
			echo "\t<itunes:new-feed-url>". htmlspecialchars(trim($Feed['itunes_new_feed_url'])) .'</itunes:new-feed-url>'.PHP_EOL;
		}
	}
	else if( !empty($Feed['itunes_new_feed_url']) && ($feed_slug == 'feed' || $feed_slug == 'rss2') ) // If it is the default feed (We don't wnat to apply this to category or tag feeds
	{
		$Feed['itunes_new_feed_url'] = str_replace('&amp;', '&', $Feed['itunes_new_feed_url']);
		echo "\t<itunes:new-feed-url>". htmlspecialchars(trim($Feed['itunes_new_feed_url'])) .'</itunes:new-feed-url>'.PHP_EOL;
	}
	
	if( !empty($Feed['itunes_summary']) )
		echo "\t".'<itunes:summary>'. powerpress_format_itunes_value( $Feed['itunes_summary'], 'summary' ) .'</itunes:summary>'.PHP_EOL;
	else
		echo "\t".'<itunes:summary>'.  powerpress_format_itunes_value( get_bloginfo('description'), 'summary' ) .'</itunes:summary>'.PHP_EOL;
	
	if( !empty($powerpress_feed['itunes_talent_name']) )
		echo "\t<itunes:author>" . esc_html($powerpress_feed['itunes_talent_name']) . '</itunes:author>'.PHP_EOL;
	
	if( !empty($powerpress_feed['explicit']) )
		echo "\t".'<itunes:explicit>' . $powerpress_feed['explicit'] . '</itunes:explicit>'.PHP_EOL;
		
	if( !empty($Feed['itunes_block']) )
		echo "\t<itunes:block>yes</itunes:block>".PHP_EOL;
	
	if( !empty($Feed['itunes_complete']) )
		echo "\t<itunes:complete>yes</itunes:complete>".PHP_EOL;
		
	if( !empty($Feed['itunes_image']) )
	{
		echo "\t".'<itunes:image href="' . esc_html( str_replace(' ', '+', $Feed['itunes_image']), 'double') . '" />'.PHP_EOL;
	}
	else
	{
		echo "\t".'<itunes:image href="' . powerpress_get_root_url() . 'itunes_default.jpg" />'.PHP_EOL;
	}
	
	if( !empty($Feed['email']) )
	{
		echo "\t".'<itunes:owner>'.PHP_EOL;
		echo "\t\t".'<itunes:name>' . esc_html($powerpress_feed['itunes_talent_name']) . '</itunes:name>'.PHP_EOL;
		echo "\t\t".'<itunes:email>' . esc_html($Feed['email']) . '</itunes:email>'.PHP_EOL;
		echo "\t".'</itunes:owner>'.PHP_EOL;
		echo "\t".'<managingEditor>'. esc_html($Feed['email'] .' ('. $powerpress_feed['itunes_talent_name'] .')') .'</managingEditor>'.PHP_EOL;
	}
	
	if( !empty($Feed['copyright']) )
	{
		// In case the user entered the copyright html version or the copyright UTF-8 or ASCII symbol or just (c)
		$Feed['copyright'] = str_replace(array('&copy;', '(c)', '(C)', chr(194) . chr(169), chr(169) ), '&#xA9;', $Feed['copyright']);
		echo "\t".'<copyright>'. esc_html($Feed['copyright']) . '</copyright>'.PHP_EOL;
	}
	
	if( !empty($Feed['itunes_subtitle']) )
		echo "\t".'<itunes:subtitle>' . powerpress_format_itunes_value($Feed['itunes_subtitle'], 'subtitle') . '</itunes:subtitle>'.PHP_EOL;
	else
		echo "\t".'<itunes:subtitle>'.  powerpress_format_itunes_value( get_bloginfo('description'), 'subtitle') .'</itunes:subtitle>'.PHP_EOL;
	
	if( !empty($Feed['itunes_keywords']) )
		echo "\t".'<itunes:keywords>' . powerpress_format_itunes_value($Feed['itunes_keywords'], 'keywords') . '</itunes:keywords>'.PHP_EOL;
		
	if( !empty($Feed['rss2_image']) || !empty($Feed['itunes_image']) )
	{
		if( !empty($Feed['rss2_image']) ) // If the RSS image is set, use it, otherwise use the iTunes image...
			$rss_image = $Feed['rss2_image'];
		else
			$rss_image = $Feed['itunes_image'];
		
		echo "\t". '<image>' .PHP_EOL;
		if( is_category() && !empty($Feed['title']) )
			echo "\t\t".'<title>' . esc_html( get_bloginfo_rss('name') ) . '</title>'.PHP_EOL;
		else
			echo "\t\t".'<title>' . esc_html( get_bloginfo_rss('name') . get_wp_title_rss() ) . '</title>'.PHP_EOL;
		echo "\t\t".'<url>' . esc_html( str_replace(' ', '+', $rss_image)) . '</url>'.PHP_EOL;
		echo "\t\t".'<link>'. $Feed['url'] . '</link>' . PHP_EOL;
		echo "\t".'</image>' . PHP_EOL;
	}
	else // Use the default image
	{
		echo "\t". '<image>' .PHP_EOL;
		if( is_category() && !empty($Feed['title']) )
			echo "\t\t".'<title>' . esc_html( get_bloginfo_rss('name') ) . '</title>'.PHP_EOL;
		else
			echo "\t\t".'<title>' . esc_html( get_bloginfo_rss('name') . get_wp_title_rss() ) . '</title>'.PHP_EOL;
		echo "\t\t".'<url>' . powerpress_get_root_url() . 'rss_default.jpg</url>'.PHP_EOL;
		echo "\t\t".'<link>'. $Feed['url'] . '</link>' . PHP_EOL;
		echo "\t".'</image>' . PHP_EOL;
	}
	
	
	// Handle iTunes categories
	$Categories = powerpress_itunes_categories();
	$Cat1 = false; $Cat2 = false; $Cat3 = false;
	if( !empty($Feed['itunes_cat_1']) )
			list($Cat1, $SubCat1) = explode('-', $Feed['itunes_cat_1']);
	if( !empty($Feed['itunes_cat_2']) )
			list($Cat2, $SubCat2) = explode('-', $Feed['itunes_cat_2']);
	if( !empty($Feed['itunes_cat_3']) )
			list($Cat3, $SubCat3) = explode('-', $Feed['itunes_cat_3']);
 
	if( $Cat1 )
	{
		$CatDesc = $Categories[$Cat1.'-00'];
		$SubCatDesc = $Categories[$Cat1.'-'.$SubCat1];
		if( $Cat1 != $Cat2 && $SubCat1 == '00' )
		{
			echo "\t".'<itunes:category text="'. esc_html($CatDesc) .'" />'.PHP_EOL;
		}
		else
		{
			echo "\t".'<itunes:category text="'. esc_html($CatDesc) .'">'.PHP_EOL;
			if( $SubCat1 != '00' )
				echo "\t\t".'<itunes:category text="'. esc_html($SubCatDesc) .'" />'.PHP_EOL;
			
			// End this category set
			if( $Cat1 != $Cat2 )
				echo "\t".'</itunes:category>'.PHP_EOL;
		}
	}
 
	if( $Cat2 )
	{
		$CatDesc = $Categories[$Cat2.'-00'];
		$SubCatDesc = $Categories[$Cat2.'-'.$SubCat2];
	 
		// It's a continuation of the last category...
		if( $Cat1 == $Cat2 )
		{
			if( $SubCat2 != '00' )
				echo "\t\t".'<itunes:category text="'. esc_html($SubCatDesc) .'" />'.PHP_EOL;
			
			// End this category set
			if( $Cat2 != $Cat3 )
				echo "\t".'</itunes:category>'.PHP_EOL;
		}
		else // This is not a continuation, lets start a new category set
		{
			if( $Cat2 != $Cat3 && $SubCat2 == '00' )
			{
				echo "\t".'<itunes:category text="'. esc_html($CatDesc) .'" />'.PHP_EOL;
			}
			else // We have nested values
			{
				if( $Cat1 != $Cat2 ) // Start a new category set
					echo "\t".'<itunes:category text="'. esc_html($CatDesc) .'">'.PHP_EOL;
				if( $SubCat2 != '00' )
				echo "\t\t".'<itunes:category text="'. esc_html($SubCatDesc) .'" />'.PHP_EOL;
				if( $Cat2 != $Cat3 ) // End this category set
					echo "\t".'</itunes:category>'.PHP_EOL;
			}
		}
	}
 
	if( $Cat3 )
	{
		$CatDesc = $Categories[$Cat3.'-00'];
		$SubCatDesc = $Categories[$Cat3.'-'.$SubCat3];
	 
		// It's a continuation of the last category...
		if( $Cat2 == $Cat3 )
		{
			if( $SubCat3 != '00' )
				echo "\t\t".'<itunes:category text="'. esc_html($SubCatDesc) .'" />'.PHP_EOL;
			
			// End this category set
			echo "\t".'</itunes:category>'.PHP_EOL;
		}
		else // This is not a continuation, lets start a new category set
		{
			if( $Cat2 != $Cat3 && $SubCat3 == '00' )
			{
				echo "\t".'<itunes:category text="'. esc_html($CatDesc) .'" />'.PHP_EOL;
			}
			else // We have nested values
			{
				if( $Cat2 != $Cat3 ) // Start a new category set
					echo "\t".'<itunes:category text="'. esc_html($CatDesc) .'">'.PHP_EOL;
				if( $SubCat3 != '00' )
					echo "\t\t".'<itunes:category text="'. esc_html($SubCatDesc) .'" />'.PHP_EOL;
				// End this category set
				echo "\t".'</itunes:category>'.PHP_EOL;
			}
		}
	}
	// End Handle iTunes categories
	
	// RawVoice RSS Tags
	if( !defined('POWERPRESS_RAWVOICE_RSS') || POWERPRESS_RAWVOICE_RSS != false )
	{
		if( !empty($Feed['parental_rating']) )
			echo "\t\t<rawvoice:rating>". $Feed['parental_rating'] ."</rawvoice:rating>".PHP_EOL;
		if( !empty($Feed['location']) )
			echo "\t\t<rawvoice:location>". htmlspecialchars($Feed['location']) ."</rawvoice:location>".PHP_EOL;
		if( !empty($Feed['frequency']) )
			echo "\t\t<rawvoice:frequency>". htmlspecialchars($Feed['frequency']) ."</rawvoice:frequency>".PHP_EOL;
	}
}

add_action('rss2_head', 'powerpress_rss2_head');

function powerpress_rss2_item()
{
	global $post, $powerpress_feed;
	
	// are we processing a feed that powerpress should handle
	if( !powerpress_is_podcast_feed() )
		return;
	
	if( function_exists('post_password_required') )
	{
		if( post_password_required($post) )
			return $content;
	}
		
	// Check and see if we're working with a podcast episode
	$custom_enclosure = false;
	if( powerpress_is_custom_podcast_feed() && get_query_var('feed') != 'podcast' && !is_category() )
	{
		$EpisodeData = powerpress_get_enclosure_data($post->ID, get_query_var('feed') );
		$custom_enclosure = true;
	}
	else
	{
		$EpisodeData = powerpress_get_enclosure_data($post->ID, 'podcast');
		if( !$EpisodeData && !empty($powerpress_feed['process_podpress']) )
		{
			$EpisodeData = powerpress_get_enclosure_data_podpress($post->ID);
			$custom_enclosure = true;
		}
	}
	
	if( !$EpisodeData || !$EpisodeData['url'] )
		return;
	
	// If enclosure not added, check to see why...
	if( defined('POWERPRESS_ENCLOSURE_FIX') && POWERPRESS_ENCLOSURE_FIX && !$custom_enclosure && $GLOBALS['powerpress_rss_enclosure_post_id'] != $post->ID )
	{
		$enclosure_in_wp = apply_filters('rss_enclosure', '<enclosure url="' . trim(htmlspecialchars($EpisodeData['url']) . '" length="' . $EpisodeData['size'] . '" type="' . $EpisodeData['type'] . '" />' . "\n") );
		if( !$enclosure_in_wp )
			$custom_enclosure = true;
	}
	
	$author = $powerpress_feed['itunes_talent_name'];
	if( isset($powerpress_feed['itunes_author_post']) )
		$author = get_the_author();
	
	$explicit = $powerpress_feed['explicit'];
	$summary = false;
	$subtitle = false;
	$keywords = false;
	$block = false;
	$cc = false;
	
	if( isset( $EpisodeData['summary'] )  && strlen($EpisodeData['summary']) > 1 )
		$summary = $EpisodeData['summary'];
	if( isset( $EpisodeData['subtitle'] )  && strlen($EpisodeData['subtitle']) > 1 )
		$subtitle = $EpisodeData['subtitle'];
	if( isset( $EpisodeData['keywords'] ) && strlen($EpisodeData['keywords']) > 1 )
		$keywords = $EpisodeData['keywords'];
	if( isset( $EpisodeData['explicit'] ) && is_numeric($EpisodeData['explicit']) )
	{
		$explicit_array = array("no", "yes", "clean");
		$explicit = $explicit_array[$EpisodeData['explicit']];
	}
	
	// Code for future use:
	if( !empty( $EpisodeData['author'] ) )
		$author = $EpisodeData['author'];
	if( !empty( $EpisodeData['block'] ) )
		$block = 'yes';
	if( !empty( $EpisodeData['cc'] ) )
		$cc = 'yes';
		
	if( $custom_enclosure ) // We need to add the enclosure tag here...
	{
		if( empty($EpisodeData['size']) )
			$EpisodeData['size'] = 5242880; // Use the dummy 5MB size since we don't have a size to quote
			
		echo "\t". sprintf('<enclosure url="%s" length="%d" type="%s" />%s',
			trim($EpisodeData['url']),
			trim($EpisodeData['size']),
			trim($EpisodeData['type']),
			PHP_EOL);
	}
		
	// Get the post tags:
	if( !$keywords )
	{
		// Lets try to use the page tags...
		$tagobject = wp_get_post_tags( $post->ID );
		if( count($tagobject) )
		{
			$tags = array();
			for($c = 0; $c < count($tagobject) && $c < 12; $c++) // iTunes only accepts up to 12 keywords
				$tags[] = $tagobject[$c]->name;
			
			if( count($tags) > 0 )
				$keywords = implode(",", $tags);
		}
	}
	
	if( empty($powerpress_feed['feed_maximizer_on']) && $keywords )
		echo "\t\t<itunes:keywords>" . powerpress_format_itunes_value($keywords, 'keywords') . '</itunes:keywords>'.PHP_EOL;
	
	$excerpt_no_html = '';
	$content_no_html = '';
	if( !$subtitle || !$summary )
		$excerpt_no_html = strip_tags($post->post_excerpt);
	if( (!$subtitle && !$excerpt_no_html) || (!$summary && !$powerpress_feed['enhance_itunes_summary'] && !$excerpt_no_html ) )
	{
		// Strip and format the wordpress way, but don't apply any other filters for these itunes tags
		$content_no_html = $post->post_content;
		$content_no_html = strip_shortcodes( $content_no_html ); 
		$content_no_html = str_replace(']]>', ']]&gt;', $content_no_html);
		$content_no_html = strip_tags($content_no_html);
	}
	
	if( $subtitle )
		echo "\t<itunes:subtitle>". powerpress_format_itunes_value($subtitle, 'subtitle') .'</itunes:subtitle>'.PHP_EOL;
	else if( $excerpt_no_html )
		echo "\t<itunes:subtitle>". powerpress_format_itunes_value($excerpt_no_html, 'subtitle') .'</itunes:subtitle>'.PHP_EOL;
	else	
		echo "\t<itunes:subtitle>". powerpress_format_itunes_value($content_no_html, 'subtitle') .'</itunes:subtitle>'.PHP_EOL;
	
	if( empty($powerpress_feed['feed_maximizer_on']) )
	{
		if( $summary )
			echo "\t\t<itunes:summary>". powerpress_format_itunes_value($summary, 'summary') .'</itunes:summary>'.PHP_EOL;
		else if( $powerpress_feed['enhance_itunes_summary'] )
			echo "\t\t<itunes:summary>". powerpress_itunes_summary($post->post_content) .'</itunes:summary>'.PHP_EOL;
		else if( $excerpt_no_html )
			echo "\t\t<itunes:summary>". powerpress_format_itunes_value($excerpt_no_html, 'summary') .'</itunes:summary>'.PHP_EOL;
		else
			echo "\t\t<itunes:summary>". powerpress_format_itunes_value($content_no_html, 'summary') .'</itunes:summary>'.PHP_EOL;
		
		if( $author )
			echo "\t\t<itunes:author>" . esc_html($author) . '</itunes:author>'.PHP_EOL;
		else
			echo "\t\t<itunes:author>".'NO AUTHOR</itunes:author>'.PHP_EOL;
	}
	
	if( $explicit )
		echo "\t\t<itunes:explicit>" . $explicit . '</itunes:explicit>'.PHP_EOL;
	
	if( $EpisodeData['duration'] && preg_match('/^(\d{1,2}:){0,2}\d{1,2}$/i', ltrim($EpisodeData['duration'], '0:') ) ) // Include duration if it is valid
		echo "\t\t<itunes:duration>" . ltrim($EpisodeData['duration'], '0:') . '</itunes:duration>'.PHP_EOL;
		
	if( $block && $block == 'yes' )
		echo "\t\t<itunes:block>yes</itunes:block>".PHP_EOL;
	
	if( $cc && $cc == 'yes' )
		echo "\t\t<itunes:isClosedCaptioned>yes</itunes:isClosedCaptioned>".PHP_EOL;	
	
	if( !empty($powerpress_feed['itunes_feature']) ) // We are using the itunes:order option to feature a specific episode.
	{
		// Skip inserting the order tag
	}
	else
	{
		if( isset( $EpisodeData['order'] ) && is_numeric($EpisodeData['order']) )
			echo "\t\t<itunes:order>". $EpisodeData['order'] ."</itunes:order>".PHP_EOL;	
	}
	
	// RawVoice RSS Tags
	if( empty($powerpress_feed['feed_maximizer_on']) )
	{
		if( !defined('POWERPRESS_RAWVOICE_RSS') || POWERPRESS_RAWVOICE_RSS != false )
		{
			if( !empty($EpisodeData['ishd']) )
				echo "\t\t<rawvoice:isHD>yes</rawvoice:isHD>".PHP_EOL;;
			if( !empty($EpisodeData['image']) )
				echo "\t\t<rawvoice:poster url=\"". $EpisodeData['image'] ."\" />".PHP_EOL;
			if( !empty($EpisodeData['embed']) )
				echo "\t\t<rawvoice:embed>". htmlspecialchars($EpisodeData['embed']) ."</rawvoice:embed>".PHP_EOL;
			else if( !empty($powerpress_feed['podcast_embed_in_feed']) && function_exists('powerpress_generate_embed') )
			{
				$player = powerpressplayer_embedable($EpisodeData['url'], $EpisodeData);
				$embed_content = '';
				// TODO:
				if( $player )
					$embed_content = powerpress_generate_embed($player, $EpisodeData);
				if( $embed_content )
					echo "\t\t<rawvoice:embed>". htmlspecialchars( $embed_content ) ."</rawvoice:embed>".PHP_EOL;
			}
				
			if( !empty($EpisodeData['webm_src']) )
			{
				echo "\t\t<rawvoice:webm src=\"". $EpisodeData['webm_src'] ."\"";
				if( $EpisodeData['webm_length'] )
					echo " length=\"". $EpisodeData['webm_length'] ."\"";
				echo " type=\"video/webm\" />".PHP_EOL;
			}
			
			$GeneralSettings = get_option('powerpress_general');
			
			if( !empty($GeneralSettings) && !empty($GeneralSettings['metamarks']) )
			{
				require_once(POWERPRESS_ABSPATH .'/powerpressadmin-metamarks.php');
				powerpress_metamarks_print_rss2($EpisodeData);
			}
		}
	}
}

add_action('rss2_item', 'powerpress_rss2_item');

function powerpress_filter_rss_enclosure($content)
{
	if( defined('PODPRESS_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return $content; // Another podcasting plugin is enabled...
		
		
	if( powerpress_is_custom_podcast_feed() && get_query_var('feed') != 'podcast' && !is_category() )
		return ''; // We will handle this enclosure in the powerpress_rss2_item() function

	$match_count = preg_match('/\surl="([^"]*)"/', $content, $matches);
	if( count($matches) != 2)
		return $content;
		
	// Original Media URL
	$OrigURL = $matches[1];
	
	if( substr($OrigURL, 0, 5) != 'http:' && substr($OrigURL, 0, 6) != 'https:' )
		return ''; // The URL value is invalid
		
	global $post, $powerpress_rss_enclosure_post_id;
	if( empty($powerpress_rss_enclosure_post_id) )
		$powerpress_rss_enclosure_post_id = -1;
	
	if( $powerpress_rss_enclosure_post_id == $post->ID )
		return ''; // we've already included one enclosure, lets not allow anymore
	$powerpress_rss_enclosure_post_id = $post->ID;
	
	// Modified Media URL
	$ModifiedURL = powerpress_add_redirect_url($OrigURL);
	
	// Check that the content type is a valid one...
	$match_count = preg_match('/\stype="([^"]*)"/', $content, $matches);
	if( count($matches) > 1 && strstr($matches[1], '/') == false )
	{
		$ContentType = powerpress_get_contenttype($ModifiedURL);
		$content = str_replace("type=\"{$matches[1]}\"", "type=\"$ContentType\"", $content);
	}
	
	// Check that the content length is a digit greater that zero
	$match_count = preg_match('/\slength="([^"]*)"/', $content, $matches);
	if( count($matches) > 1 && empty($matches[1]) )
	{
		$content = str_replace("length=\"{$matches[1]}\"", "length=\"5242880\"", $content);
	}
	
	// Replace the original url with the modified one...
	if( $OrigURL != $ModifiedURL )
		return str_replace($OrigURL, $ModifiedURL, $content);
	return $content;
}


add_filter('rss_enclosure', 'powerpress_filter_rss_enclosure', 11);

function powerpress_bloginfo_rss($content, $field = '')
{
	if( powerpress_is_custom_podcast_feed() )
	{
		if( is_category() )
			$Feed = get_option('powerpress_cat_feed_'.get_query_var('cat') );
		else
			$Feed = get_option('powerpress_feed_'.get_query_var('feed') );
		//$Feed = true;
		if( $Feed )
		{
			switch( $field )
			{
				case 'description': {
					if( !empty($Feed['description']) )
						return $Feed['description'];
					else if( is_category() )
					{
						$category = get_category( get_query_var('cat') );
						if( $category->description )
							return $category->description;
					}
				}; break;
				case 'url': {
					if( !empty($Feed['url']) )
						return trim($Feed['url']);
					else if( is_category() )
						return get_category_link( get_query_var('cat') );
				}; break;
				case 'name': {
					if( !empty($Feed['title']) )
						return $Feed['title'];
				}; break;
				case 'language': {
					// Get the feed language
					$lang = '';
					if( isset($Feed['rss_language']) && $Feed['rss_language'] != '' )
						$lang = $Feed['rss_language'];
					if( strlen($lang) == 5 )
						$lang = substr($lang,0,3) .  strtoupper( substr($lang, 3) ); // Format example: en-US for English, United States
					if( !empty($lang) )
						return $lang;
				}; break;
			}
		}
	}
	
	return $content;
}

add_filter('get_bloginfo_rss', 'powerpress_bloginfo_rss', 10, 2);


function powerpress_wp_title_rss($title)
{
	if( powerpress_is_custom_podcast_feed() )
	{
		if( is_category() )
		{
			$Feed = get_option('powerpress_cat_feed_'.get_query_var('cat') );
			if( $Feed && isset($Feed['title']) && $Feed['title'] != '' )
				return ''; // We alrady did a custom title, lets not add the category to it...
		}
		else
		{
			return ''; // It is not a category, lets not mess with our beautiful title then
		}
	}
	return $title;
}

add_filter('wp_title_rss', 'powerpress_wp_title_rss');


// Following code only works for WP 3.3 or older. WP 3.4+ now uses the get_locale setting, so we have to override directly in the get_bloginfo_rss functoin.
if( version_compare($GLOBALS['wp_version'], '3.4', '<') )
{
	function powerpress_rss_language($value)
	{
		if( powerpress_is_custom_podcast_feed() )
		{
			global $powerpress_feed;
			if( $powerpress_feed && isset($powerpress_feed['rss_language']) && $powerpress_feed['rss_language'] != '' )
				$value = $powerpress_feed['rss_language'];
		}
		return $value;
	}

	add_filter('option_rss_language', 'powerpress_rss_language');
}

function powerpress_do_podcast_feed($for_comments=false)
{
	global $wp_query, $powerpress_feed;
	
	powerpress_is_podcast_feed(); // Loads the feed settings if not already loaded...
	
	$GeneralSettings = get_option('powerpress_general');
	if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] )
	{
		$feed_slug = get_query_var('feed');
		
		if( $feed_slug != 'podcast' )
		{
			$FeedSettings = get_option('powerpress_feed_'.$feed_slug);
			if( !empty($FeedSettings['premium']) )
			{
				require_once( POWERPRESS_ABSPATH.'/powerpress-feed-auth.php');
				powerpress_feed_auth( $feed_slug );
			}
		}
	}
	
	//$wp_query->get_posts(); // No longer needed as it duplicates the existing get posts query already performed
	if( !empty($GeneralSettings['episode_box_feature_in_itunes']) ||  !empty($powerpress_feed['maximize_feed'])  )
	{
		// Use the template for the always featured option
		load_template( POWERPRESS_ABSPATH . '/feed-podcast.php' );
	}
	else
	{
		do_feed_rss2($for_comments);
	}
	
}

function powerpress_template_redirect()
{
	if( is_feed() && powerpress_is_custom_podcast_feed() )
	{
		remove_action('template_redirect', 'ol_feed_redirect'); // Remove this action so feedsmith doesn't redirect
		global $powerpress_feed;
		if( !isset($powerpress_feed['feed_redirect_url']) )
			$powerpress_feed['feed_redirect_url'] = '';
		$redirect_value = ( !empty($_GET['redirect'])? $_GET['redirect'] : false );
		if( is_array($powerpress_feed) && trim($powerpress_feed['feed_redirect_url']) != '' && !preg_match("/feedburner|feedsqueezer|feedvalidator/i", $_SERVER['HTTP_USER_AGENT'] ) && $redirect_value != 'no' )
		{
			if (function_exists('status_header'))
				status_header( 302 );
			header("Location: " . trim($powerpress_feed['feed_redirect_url']));
			header("HTTP/1.1 302 Temporary Redirect");
			exit();
		}
	}
}

add_action('template_redirect', 'powerpress_template_redirect', 0);


function powerpress_rewrite_rules_array($array)
{
	global $wp_rewrite;
	$settings = get_option('powerpress_general');
	
	$podcast_feeds = array('podcast'=>true);
	if( isset($settings['custom_feeds']) && is_array($settings['custom_feeds']) )
		$podcast_feeds = array_merge($settings['custom_feeds'], $podcast_feeds );
	
	$merged_slugs = '';
	while( list($feed_slug, $feed_title) = each($podcast_feeds) )
	{
		if( $merged_slugs != '' )
			$merged_slugs .= '|';
		$merged_slugs .= $feed_slug;
	}
	
	// $wp_rewrite->index most likely index.php
	$new_array[ 'feed/('.$merged_slugs.')/?$' ] = $wp_rewrite->index. '?feed='. $wp_rewrite->preg_index(1);
	
	// If feature is not enabled, use the default permalinks
	if( empty($settings['permalink_feeds_only']) )
		return array_merge($new_array, $array);
	
	global $wpdb;
	reset($podcast_feeds);
	while( list($feed_slug, $feed_title) = each($podcast_feeds) )
	{
		$page_name_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_name = '".$feed_slug."'");
		if( $page_name_id )
		{
			$new_array[ $feed_slug.'/?$' ] = $wp_rewrite->index. '?pagename='. $feed_slug.'&page_id='.$page_name_id;
			unset($podcast_feeds[ $feed_slug ]);
			continue;
		}
	
		$category = get_category_by_slug($feed_slug);
		if( $category )
		{
			$new_array[ $feed_slug.'/?$' ] = $wp_rewrite->index. '?cat='. $category->term_id; // category_name='. $feed_slug .'&
			unset($podcast_feeds[ $feed_slug ]);
		}
	}
	
	if( count($podcast_feeds) > 0 )
	{
		reset($podcast_feeds);
		$remaining_slugs = '';
		while( list($feed_slug, $feed_title) = each($podcast_feeds) )
		{
			if( $remaining_slugs != '' )
				$remaining_slugs .= '|';
			$remaining_slugs .= $feed_slug;
		}
		
		$new_array[ '('.$remaining_slugs.')/?$' ] = $wp_rewrite->index. '?pagename='. $wp_rewrite->preg_index(1);
	}
	
	return array_merge($new_array, $array);
}

add_filter('rewrite_rules_array', 'powerpress_rewrite_rules_array');


function powerpress_pre_transient_rewrite_rules($return_rules)
{
	global $wp_rewrite;
	$GeneralSettings = get_option('powerpress_general');
	if( !in_array('podcast', $wp_rewrite->feeds) )
		$wp_rewrite->feeds[] = 'podcast';
	
	if( $GeneralSettings && isset($GeneralSettings['custom_feeds']) && is_array($GeneralSettings['custom_feeds']) )
	{
		while( list($feed_slug,$null) = each($GeneralSettings['custom_feeds']) )
		{
			if( !in_array($feed_slug, $wp_rewrite->feeds) )
				$wp_rewrite->feeds[] = $feed_slug;
		}
	}
	
	return $return_rules;
}

add_filter('pre_transient_rewrite_rules', 'powerpress_pre_transient_rewrite_rules');

function powerpress_init()
{
	$GeneralSettings = get_option('powerpress_general');
	
	if( empty($GeneralSettings['disable_appearance']) || $GeneralSettings['disable_appearance'] == false )
	{
		require_once( POWERPRESS_ABSPATH.'/powerpress-player.php');
		powerpressplayer_init($GeneralSettings);
	}
	
	if( defined('PODPRESS_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return false; // Another podcasting plugin is enabled...
	
	// If we are to process podpress data..
	if( !empty($GeneralSettings['process_podpress']) )
	{
		powerpress_podpress_redirect_check();
	}
	
	// Add the podcast feeds;
	add_feed('podcast', 'powerpress_do_podcast_feed');
	if( $GeneralSettings && isset($GeneralSettings['custom_feeds']) && is_array($GeneralSettings['custom_feeds']) )
	{
		while( list($feed_slug,$feed_title) = each($GeneralSettings['custom_feeds']) )
		{
			if( $feed_slug != 'podcast' )
				add_feed($feed_slug, 'powerpress_do_podcast_feed');
		}
	}

	// CHECK if the Google Analytics for WordPress plugin is enabled, if so, lets add our players after it modifies the post content...
	if( defined('GAWP_VERSION') && POWERPRESS_CONTENT_ACTION_PRIORITY < 120 )
	{
		remove_filter('get_the_excerpt', 'powerpress_content', (POWERPRESS_CONTENT_ACTION_PRIORITY - 1) );
		remove_filter('the_content', 'powerpress_content', POWERPRESS_CONTENT_ACTION_PRIORITY);
		remove_filter('the_excerpt', 'powerpress_content', POWERPRESS_CONTENT_ACTION_PRIORITY);
		
		add_filter('get_the_excerpt', 'powerpress_content', (120-1) );
		add_filter('the_content', 'powerpress_content', 120);
		add_filter('the_excerpt', 'powerpress_content', 120);
	}
}

add_action('init', 'powerpress_init', -100); // We need to add the feeds before other plugins start screwing with them

function powerpress_request($qv)
{
	if( !empty($qv['feed']) )
	{
		$podcast_feed_slug = false;
		if( $qv['feed'] == 'podcast' ) {
			$podcast_feed_slug = 'podcast';
		} else if( $qv['feed'] == 'rss' || $qv['feed'] == 'rss2' || $qv['feed'] == 'atom' || $qv['feed'] == 'rdf' || $qv['feed'] == 'feed' ) { //  'feed', 'rdf', 'rss', 'rss2', 'atom'
			// Skip
		} else {
			$GeneralSettings = get_option('powerpress_general');
			if( isset($GeneralSettings['custom_feeds']) && is_array($GeneralSettings['custom_feeds']) && !empty($GeneralSettings['custom_feeds'][ $qv['feed'] ] ) )
				$podcast_feed_slug = $qv['feed'];
		}
		
		if( $podcast_feed_slug )
		{
			if( $qv['feed'] == 'podcast' )
				$qv['post_type'] = 'post';
			else {
				$qv['post_type'] = get_post_types( array('public'=> true, 'capability_type'=>'post') );
				if( !empty($qv['post_type']['attachment']) )
					unset($qv['post_type']['attachment']);
			}
			
			$FeedCustom = get_option('powerpress_feed_'.$podcast_feed_slug); // Get custom feed specific settings
			// See if the user set a custom post type only...
			if( !empty($FeedCustom) && !empty( $FeedCustom['custom_post_type']) )
				$qv['post_type'] = $FeedCustom['custom_post_type'];
		}
	}
	return $qv;
}

add_filter('request', 'powerpress_request');

// May be used for future use
/*
function powerpress_plugins_loaded()
{
	
}
add_action('plugins_loaded', 'powerpress_plugins_loaded');
*/

// Load the general feed settings for feeds handled by powerpress
function powerpress_load_general_feed_settings()
{
	global $wp_query;
	global $powerpress_feed;
	
	if( $powerpress_feed !== false ) // If it is not false (either NULL or an array) then we already looked these settings up
	{
		$powerpress_feed = false;
		
		// Get the powerpress settings
		$GeneralSettings = get_option('powerpress_general');
		if( !isset($GeneralSettings['custom_feeds']['podcast']) )
			$GeneralSettings['custom_feeds']['podcast'] = 'Podcast Feed'; // Fixes scenario where the user never configured the custom default podcast feed.
		
		if( $GeneralSettings )
		{
			$FeedSettingsBasic = get_option('powerpress_feed'); // Get overall feed settings
				
			// If we're in advanced mode and we're dealing with a category feed we're extending, lets work with it...
			if( is_category() && isset($GeneralSettings['custom_cat_feeds']) && is_array($GeneralSettings['custom_cat_feeds']) && in_array( get_query_var('cat'), $GeneralSettings['custom_cat_feeds']) )
			{
				$cat_ID = get_query_var('cat');
				$FeedCustom = get_option('powerpress_cat_feed_'.$cat_ID); // Get custom feed specific settings
				$Feed = powerpress_merge_empty_feed_settings($FeedCustom, $FeedSettingsBasic);
				
				$powerpress_feed = array();
				$powerpress_feed['is_custom'] = true;
				$powerpress_feed['category'] = $cat_ID;
				$powerpress_feed['process_podpress'] = !empty($GeneralSettings['process_podpress']); // Category feeds could originate from Podpress
				$powerpress_feed['rss_language'] = ''; // default, let WordPress set the language
				$powerpress_feed['default_url'] = rtrim($GeneralSettings['default_url'], '/') .'/';
				$explicit_array = array("no", "yes", "clean");
				$powerpress_feed['explicit'] = $explicit_array[$Feed['itunes_explicit']];
				if( $Feed['itunes_talent_name'] )
					$powerpress_feed['itunes_talent_name'] = $Feed['itunes_talent_name'];
				else
					$powerpress_feed['itunes_talent_name'] = get_bloginfo_rss('name');
				$powerpress_feed['enhance_itunes_summary'] = $Feed['enhance_itunes_summary'];
				$powerpress_feed['posts_per_rss'] = false;
				if( !empty($Feed['posts_per_rss']) && is_numeric($Feed['posts_per_rss']) && $Feed['posts_per_rss'] > 0 )
					$powerpress_feed['posts_per_rss'] = $Feed['posts_per_rss'];
				if( $Feed['feed_redirect_url'] != '' )
					$powerpress_feed['feed_redirect_url'] = $Feed['feed_redirect_url'];
				if( $Feed['itunes_author_post'] == true )
					$powerpress_feed['itunes_author_post'] = true;
				if( $Feed['rss_language'] != '' )
					$powerpress_feed['rss_language'] = $Feed['rss_language'];
				
				if( !empty($GeneralSettings['podcast_embed_in_feed']) )
					$powerpress_feed['podcast_embed_in_feed'] = true;
				return;
			}
			
			$feed_slug = get_query_var('feed');
			
			if( isset($GeneralSettings['custom_feeds']) && is_array($GeneralSettings['custom_feeds']) && isset($GeneralSettings['custom_feeds'][ $feed_slug ] ))
			{
				$FeedCustom = get_option('powerpress_feed_'.$feed_slug); // Get custom feed specific settings
				$Feed = powerpress_merge_empty_feed_settings($FeedCustom, $FeedSettingsBasic);
				
				$powerpress_feed = array();
				$powerpress_feed['is_custom'] = true;
				$powerpress_feed['feed-slug'] = $feed_slug;
				$powerpress_feed['process_podpress'] = ($feed_slug=='podcast'? !empty($GeneralSettings['process_podpress']): false); // We don't touch podpress data for custom feeds
				$powerpress_feed['rss_language'] = ''; // RSS language should be set by WordPress by default
				$powerpress_feed['default_url'] = '';
				if( !empty($powerpress_feed['default_url']) )
					$powerpress_feed['default_url'] = rtrim($GeneralSettings['default_url'], '/') .'/';
				$explicit = array("no", "yes", "clean");
				$powerpress_feed['explicit'] ='no';
				if( !empty($Feed['itunes_explicit']) )
					$powerpress_feed['explicit'] = $explicit[ $Feed['itunes_explicit'] ];
				if( !empty($Feed['itunes_talent_name']) )
					$powerpress_feed['itunes_talent_name'] = $Feed['itunes_talent_name'];
				else
					$powerpress_feed['itunes_talent_name'] = get_bloginfo_rss('name');
				$powerpress_feed['enhance_itunes_summary'] = $Feed['enhance_itunes_summary'];
				$powerpress_feed['posts_per_rss'] = false;
				if( !empty($Feed['posts_per_rss']) && is_numeric($Feed['posts_per_rss']) && $Feed['posts_per_rss'] > 0 )
					$powerpress_feed['posts_per_rss'] = $Feed['posts_per_rss'];
				if( !empty($Feed['feed_redirect_url']) )
					$powerpress_feed['feed_redirect_url'] = $Feed['feed_redirect_url'];
				if( !empty($Feed['itunes_author_post'] ) )
					$powerpress_feed['itunes_author_post'] = true;
				if( !empty($Feed['rss_language']) )
					$powerpress_feed['rss_language'] = $Feed['rss_language'];
				if( !empty($GeneralSettings['podcast_embed_in_feed']) )
					$powerpress_feed['podcast_embed_in_feed'] = true;
				if( !empty($Feed['maximize_feed']) )
					$powerpress_feed['maximize_feed'] = true;	
				return;
			}
			
			if( !isset($FeedSettingsBasic['apply_to']) )
				$FeedSettingsBasic['apply_to'] = 1;

			// We fell this far,we must be in simple mode or the user never saved customized their custom feed settings
			switch( $FeedSettingsBasic['apply_to'] )
			{
				case 0: // enhance only the podcast feed added by PowerPress, with the logic above this code should never be reached but it is added for readability.
				{
					if( $feed_slug != 'podcast' )
						break;
				} // important: no break here!
				case 2: // RSS2 Main feed and podcast feed added by PowerPress only
				{
					if( $feed_slug != 'feed' && $feed_slug != 'rss2' && $feed_slug != 'podcast' )
						break; // We're only adding podcasts to the rss2 feed in this situation
					
					if( $wp_query->is_category ) // don't touch the category feeds...
						break;
					
					if( $wp_query->is_tag ) // don't touch the tag feeds...
						break;
						
					if( $wp_query->is_comment_feed ) // don't touch the comments feeds...
						break;	
				} // important: no break here!
				case 1: // All feeds
				{
					$powerpress_feed = array(); // Only store what's needed for each feed item
					$powerpress_feed['is_custom'] = false; // ($feed_slug == 'podcast'?true:false);
					$powerpress_feed['feed-slug'] = $feed_slug;
					$powerpress_feed['process_podpress'] = !empty($GeneralSettings['process_podpress']); // We don't touch podpress data for custom feeds
					$powerpress_feed['default_url'] = '';
					if( !empty($GeneralSettings['default_url']) )
						$powerpress_feed['default_url'] = rtrim($GeneralSettings['default_url'], '/') .'/';
					$explicit = array("no", "yes", "clean");
					$powerpress_feed['explicit'] = 'no';
					if( !empty($FeedSettingsBasic['itunes_explicit']) )
						$powerpress_feed['explicit'] = $explicit[$FeedSettingsBasic['itunes_explicit']];
					if( !empty($FeedSettingsBasic['itunes_talent_name']) )
						$powerpress_feed['itunes_talent_name'] = $FeedSettingsBasic['itunes_talent_name'];
					else
						$powerpress_feed['itunes_talent_name'] = get_bloginfo_rss('name');
					$powerpress_feed['enhance_itunes_summary'] = 0;
					if( isset($FeedSettingsBasic['enhance_itunes_summary']) )
						$powerpress_feed['enhance_itunes_summary'] = $FeedSettingsBasic['enhance_itunes_summary'];
					$powerpress_feed['posts_per_rss'] = false;
					if( !empty($FeedSettingsBasic['posts_per_rss']) && is_numeric($FeedSettingsBasic['posts_per_rss']) && $FeedSettingsBasic['posts_per_rss'] > 0 )
						$powerpress_feed['posts_per_rss'] = $FeedSettingsBasic['posts_per_rss'];
					if( !empty($FeedSettingsBasic['itunes_author_post']) )
						$powerpress_feed['itunes_author_post'] = true;
					$powerpress_feed['rss_language'] = ''; // Cannot set the language setting in simple mode
					if( !empty($GeneralSettings['podcast_embed_in_feed']) )
						$powerpress_feed['podcast_embed_in_feed'] = true;
				}; break;
				// All other cases we let fall through
			}
		}
	}
}

// Returns true of the feed should be treated as a podcast feed
function powerpress_is_podcast_feed()
{
	if( defined('PODPRESS_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return false; // Another podcasting plugin is enabled...
	
	global $powerpress_feed;
	if( $powerpress_feed !== false && !is_array($powerpress_feed) )
		powerpress_load_general_feed_settings();
	if( $powerpress_feed === false )
		return false;
	return true;
}

// Returns true if the feed is a custom feed added by PowerPress
function powerpress_is_custom_podcast_feed()
{
	if( defined('PODPRESS_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return false; // Another podcasting plugin is enabled...
		
	global $powerpress_feed;
	if( $powerpress_feed !== false && !is_array($powerpress_feed) )
		powerpress_load_general_feed_settings();
	if( $powerpress_feed === false )
		return false;
	return $powerpress_feed['is_custom'];
}

function powerpress_posts_join($join)
{	
	if( is_category() )
		return $join;
		
	if( is_feed() && (powerpress_is_custom_podcast_feed() || get_query_var('feed') == 'podcast' ) && !is_category() )
	{
		global $wpdb;
		$join .= " INNER JOIN {$wpdb->postmeta} ";
		$join .= " ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ";
	}

  return $join;
}

add_filter('posts_join', 'powerpress_posts_join' );

function powerpress_posts_where($where)
{
	if( is_category() )
		return $where;
		
	if( is_feed() && (powerpress_is_custom_podcast_feed() || get_query_var('feed') == 'podcast' ) )
	{
		global $wpdb, $powerpress_feed;
		$where .= " AND (";
		
		if( powerpress_is_custom_podcast_feed() && get_query_var('feed') != 'podcast' && !is_category() )
			$where .= " {$wpdb->postmeta}.meta_key = '_". get_query_var('feed') .":enclosure' ";
		else	
			$where .= " {$wpdb->postmeta}.meta_key = 'enclosure' ";
	
		// Include Podpress data if exists...
		if( !empty($powerpress_feed['process_podpress']) && get_query_var('feed') == 'podcast' )
			$where .= " OR {$wpdb->postmeta}.meta_key = 'podPressMedia' OR {$wpdb->postmeta}.meta_key = '_podPressMedia' ";
		
		$where .= ") ";
	}
	return $where;
}

add_filter('posts_where', 'powerpress_posts_where' );

// Add the groupby needed for enclosures only
function powerpress_posts_groupby($groupby)
{
	if( is_category() )
		return $groupby;
		
	if( is_feed() && (powerpress_is_custom_podcast_feed() || get_query_var('feed') == 'podcast' ) )
	{
		global $wpdb;
		$groupby = " {$wpdb->posts}.ID ";
	}
	return $groupby;
}
add_filter('posts_groupby', 'powerpress_posts_groupby');

function powerpress_post_limits($limits)
{
	if( is_feed() && powerpress_is_custom_podcast_feed() )
	{
		global $powerpress_feed;
		if( $powerpress_feed['posts_per_rss'] && preg_match('/^(\d)+$/', trim($powerpress_feed['posts_per_rss'])) )
			$limits = "LIMIT 0, {$powerpress_feed['posts_per_rss']}";
	}
	return $limits;
}
add_filter('post_limits', 'powerpress_post_limits');


function powerpress_do_all_pings()
{
	global $wpdb;
	$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_encloseme' ");
	
	// Now call the WordPress do_all_pings()...
	do_all_pings();
	remove_action('do_pings', 'do_all_pings');
}

remove_action('do_pings', 'do_all_pings');
add_action('do_pings', 'powerpress_do_all_pings', 1, 1);

/*
Helper functions:
*/
function powerpress_podpress_redirect_check()
{
	if( preg_match('/podpress_trac\/([^\/]+)\/([^\/]+)\/([^\/]+)\/(.*)$/', $_SERVER['REQUEST_URI'], $matches) )
	{
		$post_id = $matches[2];
		$mediaNum = $matches[3];
		//$filename = $matches[4];
		//$method = $matches[1];
		
		if( is_numeric($post_id) && is_numeric($mediaNum))
		{
			$EpisodeData = powerpress_get_enclosure_data_podpress($post_id, $mediaNum);	
			if( $EpisodeData && isset($EpisodeData['url']) )
			{
				if( strpos($EpisodeData['url'], 'http://' ) !== 0 && strpos($EpisodeData['url'], 'https://' ) !== 0 )
				{
					die('Error occurred obtaining the URL for the requested media file.');
					exit;
				}
				
				$EnclosureURL = str_replace(' ', '%20', $EpisodeData['url']);
				header('Location: '.$EnclosureURL, true, 302);
				header('Content-Length: 0');
				exit;
			}
			// Let the WordPress 404 page load as normal
		}
	}
}

function the_powerpress_content()
{
	echo get_the_powerpress_content();
}

function get_the_powerpress_content()
{
	global $post;
	
	if( defined('PODPRESS_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return '';
		
	if( function_exists('post_password_required') )
	{
		if( post_password_required($post) )
			return '';
	}
	
	// PowerPress settings:
	$GeneralSettings = get_option('powerpress_general');
	
	// No player or links to add to content...
	if( !empty($GeneralSettings['disable_appearance']) )
		return $content;
		
	if( !isset($GeneralSettings['custom_feeds']) )
    $GeneralSettings['custom_feeds'] = array('podcast'=>'Default Podcast Feed');
	
	// Re-order so the default podcast episode is the top most...
	$Temp = $GeneralSettings['custom_feeds'];
	$GeneralSettings['custom_feeds'] = array();
	$GeneralSettings['custom_feeds']['podcast'] = 'Default Podcast Feed';
	while( list($feed_slug, $feed_title) = each($Temp) )
	{
		if( $feed_slug == 'podcast' )
			continue;
		$GeneralSettings['custom_feeds'][ $feed_slug ] = $feed_title;
	}
	
	if( !isset($GeneralSettings['display_player']) )
			$GeneralSettings['display_player'] = 1;
	if( !isset($GeneralSettings['player_function']) )
		$GeneralSettings['player_function'] = 1;
	if( !isset($GeneralSettings['podcast_link']) )
		$GeneralSettings['podcast_link'] = 1;
	
	// Figure out which players are alerady in the body of the page...
	$ExcludePlayers = array();
	if( isset($GeneralSettings['disable_player']) )
		$ExcludePlayers = $GeneralSettings['disable_player']; // automatically disable the players configured
	
	// LOOP HERE TO DISPLAY EACH MEDIA TYPE
	$new_content = '';
	while( list($feed_slug,$feed_title) = each($GeneralSettings['custom_feeds']) )
	{
		// Get the enclosure data
		$EpisodeData = powerpress_get_enclosure_data($post->ID, $feed_slug);
		
		if( !$EpisodeData && !empty($GeneralSettings['process_podpress']) && $feed_slug == 'podcast' )
			$EpisodeData = powerpress_get_enclosure_data_podpress($post->ID);
		
		if( !$EpisodeData || !$EpisodeData['url'] )
			continue;
	
		// Just in case, if there's no URL lets escape!
		if( !$EpisodeData['url'] )
			continue;
		
		// If the player is not already inserted in the body of the post using the shortcode...
		//if( preg_match('/\[powerpress(.*)\]/is', $content) == 0 )
		if( !isset($ExcludePlayers[ $feed_slug ]) ) // If the player is not in our exclude list because it's already in the post body somewhere...
		{
			if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] && !powerpress_premium_content_authorized($feed_slug) )
			{
				$new_content .=  powerpress_premium_content_message($post->ID, $feed_slug, $EpisodeData);
			}
			else
			{
				if( $GeneralSettings['player_function'] != 3 && $GeneralSettings['player_function'] != 0 ) // Play in new window only or disabled
				{
					$AddDefaultPlayer = empty($EpisodeData['no_player']);
					
					if( $EpisodeData && !empty($EpisodeData['embed']) )
					{
						$new_content .=  trim($EpisodeData['embed']);
						if( !empty($GeneralSettings['embed_replace_player']) )
							$AddDefaultPlayer = false;
					}
						
					if( $AddDefaultPlayer )
					{
						$image = '';
						$width = '';
						$height = '';
						if( isset($EpisodeData['image']) && $EpisodeData['image'] != '' )
							$image = $EpisodeData['image'];
						if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
							$width = $EpisodeData['width'];
						if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) )
							$height = $EpisodeData['height'];
						
						$new_content .= apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
					}
				}
				
				if( !isset($EpisodeData['no_links']) )
					$new_content .= apply_filters('powerpress_player_links', '',  powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
			}
		}
	}
	
	return $new_content;
}



// Adds content types that are missing from the default wp_check_filetype function
function powerpress_get_contenttype($file, $use_wp_check_filetype = true)
{
	$parts = pathinfo($file);
	if( !empty($parts['extension']) )
	{
		switch( strtolower($parts['extension']) )
		{
			// Audio formats
			case 'mp3': // most common
			case 'mpga':
			case 'mp2':
			case 'mp2a':
			case 'm2a':
			case 'm3a':
				return 'audio/mpeg';
			case 'm4a':
				return 'audio/x-m4a';
			case 'm4b': // Audio book format
				return 'audio/m4b';
			case 'm4r': // iPhone ringtone format
				return 'audio/m4r';
			// OGG Internet contnet types as set forth by rfc5334 (http://tools.ietf.org/html/rfc5334)
			case 'oga':
			case 'spx':
				return 'audio/ogg';
			case 'wma':
				return 'audio/x-ms-wma';
			case 'wax':
				return 'audio/x-ms-wax';
			case 'ra':
			case 'ram':
				return 'audio/x-pn-realaudio';
			case 'mp4a':
				return 'audio/mp4';
				
			// Video formats
			case 'm4v':
				return 'video/x-m4v';
			case 'mpeg':
			case 'mpg':
			case 'mpe':
			case 'm1v':
			case 'm2v':
				return 'video/mpeg';
			case 'mp4':
			case 'mp4v':
			case 'mpg4':
				return 'video/mp4';
			case 'asf':
			case 'asx':
				return 'video/x-ms-asf';
			case 'wmx':
				return 'video/x-ms-wmx';
			case 'avi':
				return 'video/x-msvideo';
			case 'wmv':
				return 'video/x-ms-wmv'; // Check this
			case 'flv':
				return 'video/x-flv';
			case 'mov':
			case 'qt':
				return 'video/quicktime';
			case 'divx':
				return 'video/divx';
			case '3gp':
				return 'video/3gpp';
			case 'webm':
				return 'video/webm';
			case 'ogg':
			case 'ogv':
				return 'video/ogg';
				
			// rarely used
			case 'mid':
			case 'midi':
				return'audio/midi';
			case 'wav':
				return 'audio/wav';
			case 'aa':
				return 'audio/audible';
			case 'pdf':
				return 'application/pdf';
			case 'torrent':
				return 'application/x-bittorrent';
			case 'swf':
				return 'application/x-shockwave-flash';
			case 'ogx':
				return 'application/ogg';
				
			// Most recently added by Apple:
			case 'epub':
				return 'document/x-epub';
			
			default: // Let it fall through
		}
	}
	
	// Last case let wordpress detect it:
	if( $use_wp_check_filetype )
	{
		$FileType = wp_check_filetype($file);
		if( $FileType && isset($FileType['type']) )
			return $FileType['type'];
	}
	return '';
}

function powerpress_itunes_summary($html)
{
	// Do some smart conversion of the post html to readable text without HTML.
	
	// First, convert: <a href="link"...>label</a>
	// to: label (link)
	$html = preg_replace_callback('/(\<a[^\>]*href="([^"]*)"[^\>]*>([^\<]*)<\/a\>)/i', 
				create_function(
					'$matches',
					'return "{$matches[3]} ({$matches[2]})";'
			), 
				$html);
	
	// Second, convert: <img src="link" title="title" />
	// to: if no title (image: link) or (image title: link)
	$html = preg_replace_callback('/(\<img[^\>]*src="([^"]*)"[^\>]*[^\>]*\>)/i', 
				create_function(
					'$matches',
					'return "({$matches[2]})";'
			), 
				$html);
	
	// For now make them bullet points...
	$html = str_replace('<li>', '<li>* ', $html);
	
	// Now do all the other regular conversions...
	$html = strip_shortcodes( $html ); 
	$html = str_replace(']]>', ']]&gt;', $html);
	$content_no_html = strip_tags($html);
	$content_no_html = powerpress_format_itunes_value($content_no_html, 'summary');
	$content_no_html = preg_replace('/(\(http:\/\/[^\)\s]*)$/i', '', $content_no_html);
	return $content_no_html;
}

function powerpress_itunes_categories($PrefixSubCategories = false)
{
	$temp = array();
	$temp['01-00'] = 'Arts';
		$temp['01-01'] = 'Design';
		$temp['01-02'] = 'Fashion & Beauty';
		$temp['01-03'] = 'Food';
		$temp['01-04'] = 'Literature';
		$temp['01-05'] = 'Performing Arts';
		$temp['01-06'] = 'Visual Arts';

	$temp['02-00'] = 'Business';
		$temp['02-01'] = 'Business News';
		$temp['02-02'] = 'Careers';
		$temp['02-03'] = 'Investing';
		$temp['02-04'] = 'Management & Marketing';
		$temp['02-05'] = 'Shopping';

	$temp['03-00'] = 'Comedy';

	$temp['04-00'] = 'Education';
		$temp['04-01'] = 'Education Technology';
		$temp['04-02'] = 'Higher Education';
		$temp['04-03'] = 'K-12';
		$temp['04-04'] = 'Language Courses';
		$temp['04-05'] = 'Training';
		 
	$temp['05-00'] = 'Games & Hobbies';
		$temp['05-01'] = 'Automotive';
		$temp['05-02'] = 'Aviation';
		$temp['05-03'] = 'Hobbies';
		$temp['05-04'] = 'Other Games';
		$temp['05-05'] = 'Video Games';

	$temp['06-00'] = 'Government & Organizations';
		$temp['06-01'] = 'Local';
		$temp['06-02'] = 'National';
		$temp['06-03'] = 'Non-Profit';
		$temp['06-04'] = 'Regional';

	$temp['07-00'] = 'Health';
		$temp['07-01'] = 'Alternative Health';
		$temp['07-02'] = 'Fitness & Nutrition';
		$temp['07-03'] = 'Self-Help';
		$temp['07-04'] = 'Sexuality';

	$temp['08-00'] = 'Kids & Family';
 
	$temp['09-00'] = 'Music';
 
	$temp['10-00'] = 'News & Politics';
 
	$temp['11-00'] = 'Religion & Spirituality';
		$temp['11-01'] = 'Buddhism';
		$temp['11-02'] = 'Christianity';
		$temp['11-03'] = 'Hinduism';
		$temp['11-04'] = 'Islam';
		$temp['11-05'] = 'Judaism';
		$temp['11-06'] = 'Other';
		$temp['11-07'] = 'Spirituality';
	 
	$temp['12-00'] = 'Science & Medicine';
		$temp['12-01'] = 'Medicine';
		$temp['12-02'] = 'Natural Sciences';
		$temp['12-03'] = 'Social Sciences';
	 
	$temp['13-00'] = 'Society & Culture';
		$temp['13-01'] = 'History';
		$temp['13-02'] = 'Personal Journals';
		$temp['13-03'] = 'Philosophy';
		$temp['13-04'] = 'Places & Travel';

	$temp['14-00'] = 'Sports & Recreation';
		$temp['14-01'] = 'Amateur';
		$temp['14-02'] = 'College & High School';
		$temp['14-03'] = 'Outdoor';
		$temp['14-04'] = 'Professional';
		 
	$temp['15-00'] = 'Technology';
		$temp['15-01'] = 'Gadgets';
		$temp['15-02'] = 'Tech News';
		$temp['15-03'] = 'Podcasting';
		$temp['15-04'] = 'Software How-To';

	$temp['16-00'] = 'TV & Film';

	if( $PrefixSubCategories )
	{
		while( list($key,$val) = each($temp) )
		{
			$parts = explode('-', $key);
			$cat = $parts[0];
			$subcat = $parts[1];
		 
			if( $subcat != '00' )
				$temp[$key] = $temp[$cat.'-00'].' > '.$val;
		}
		reset($temp);
	}
 
	return $temp;
}

function powerpress_get_root_url()
{
	/*
	// OLD CODE:
	$powerpress_dirname = basename( POWERPRESS_ABSPATH );
	return WP_PLUGIN_URL . '/'. $powerpress_dirname .'/';
	*/
	$local_path = __FILE__;
	if( DIRECTORY_SEPARATOR == '\\' ) { // Win32 fix
		$local_path = basename(dirname(__FILE__)) .'/'. basename(__FILE__);
	}
	$plugin_url = plugins_url('', $local_path);
	return $plugin_url . '/';
}

function powerpress_format_itunes_value($value, $tag)
{
	if( !defined('DB_CHARSET') || DB_CHARSET != 'utf8' ) // Check if the string is UTF-8
		$value = utf8_encode($value); // If it is not, convert to UTF-8 then decode it...
	
	// Code added to solve issue with KimiliFlashEmbed plugin and also remove the shortcode for the WP Audio Player
	// 99.9% of the time this code will not be necessary
	$value = preg_replace("/\[(kml_(flash|swf)embed|audio\:)\b(.*?)(?:(\/))?(\]|$)/isu", '', $value);
	
	if(version_compare("5", phpversion(), ">"))
		$value = preg_replace( '/&nbsp;/ui' , ' ', $value); // Best we can do for PHP4
	else
		$value = @html_entity_decode($value, ENT_COMPAT, 'UTF-8'); // Remove any additional entities such as &nbsp;
	$value = preg_replace( '/&amp;/ui' , '&', $value); // Best we can do for PHP4. precaution in case it didn't get removed from function above.
	
	return esc_html( powerpress_trim_itunes_value($value, $tag) );
}

function powerpress_trim_itunes_value($value, $tag = 'summary')
{
	$value = trim($value); // First we need to trim the string
	$length = (function_exists('mb_strlen')?mb_strlen($value):strlen($value) );
	$trim_at = false;
	$remove_new_lines = false;
	
	switch($tag)
	{
		case 'summary': {
			// 4000 character limit
			if( $length > 4000 )
				$trim_at = 4000;
		}; break;
		case 'subtitle':
		case 'keywords':
		case 'author':
		case 'name':
		default: {
			$remove_new_lines = true;
			// 255 character limit
			if( $length > 255 )
				$trim_at = 255;
		};
	}
	
	if( $trim_at )
	{
		// Start trimming
		$value = (function_exists('mb_substr')?mb_substr($value, 0, $trim_at):substr($value, 0, $trim_at) );
		$clean_break = false;
		if( preg_match('/(.*[,\n.\?!])[^,\n.\?!]/isu', $value, $matches) ) // pattern modifiers: case (i)nsensitive, entire (s)tring and (u)nicode
		{
			if( isset( $matches[1]) )
			{
				$detected_eof_pos = (function_exists('mb_strlen')?mb_strlen($matches[1]):strlen($matches[1]) );
				// Look back at most 50 characters...
				if( $detected_eof_pos > 3950 || ($detected_eof_pos > 205 && $detected_eof_pos < 255 ) )
				{
					$value = $matches[1];
					$clean_break = true;
				}
				// Otherwise we want to continue with the same value we started with...
			}
		}
		
		if( $clean_break == false && $tag = 'subtitle' ) // Subtitle we want to add a ... at the end
			$value = (function_exists('mb_substr')?mb_substr($value, 0, 252):substr($value, 0, 252) ). '...';
	}
	
	if( $remove_new_lines )
		$value = str_replace( array("\r\n\r\n", "\n", "\r", "\t","-  "), array(' - ',' ', '', '  ', ''), $value );
	
	return $value;
}

function powerpress_add_redirect_url($MediaURL, $GeneralSettings = false)
{
	if( preg_match('/^http\:/i', $MediaURL) === false )
		return $MediaURL; // If the user is hosting media not via http (e.g. https or ftp) then we can't handle the redirect
		
	$NewURL = $MediaURL;
	if( !$GeneralSettings ) // Get the general settings if not passed to this function, maintain the settings globally for further use
	{
		global $powerpress_general_settings;
		if( !$powerpress_general_settings )
		{
			$powerpress_general_settings = get_option('powerpress_general');
			if( !empty($powerpress_general_settings['cat_casting']) ) // If category podcasting...
			{
				if( is_category() ) // Special case where we want to track the category separately
				{
					$FeedCatSettings = get_option('powerpress_cat_feed_'.get_query_var('cat') );
					if( $FeedCatSettings && !empty($FeedCatSettings['redirect']) )
						$powerpress_general_settings['redirect0'] = $FeedCatSettings['redirect'];
				}
				else if( is_single() )
				{
					$categories = wp_get_post_categories( get_the_ID() );
					if( count($categories) == 1 )
					{
						list($null,$cat_id) = each($categories);
						$FeedCatSettings = get_option('powerpress_cat_feed_'.$cat_id );
						if( $FeedCatSettings && !empty($FeedCatSettings['redirect']) )
							$powerpress_general_settings['redirect0'] = $FeedCatSettings['redirect'];
						// See if only one category is associated with this post
					}
				}
			}
		}
		$GeneralSettings = $powerpress_general_settings;
	}
	
	for( $x = 3; $x >= 0; $x-- )
	{
		$key = sprintf('redirect%d', $x);
		if( !empty($GeneralSettings[ $key ]) )
		{
			$RedirectClean = str_replace('http://', '', trim($GeneralSettings[ $key ]) );
			if( !strstr($NewURL, $RedirectClean) )
				$NewURL = 'http://'. $RedirectClean . str_replace('http://', '', $NewURL);
		}
	}

	return $NewURL;
}

function powerpress_add_flag_to_redirect_url($MediaURL, $Flag)
{
	return preg_replace('/(media\.(blubrry|techpodcasts|rawvoice|podcasternews)\.com\/[A-Za-z0-9-_]+\/)('.$Flag.'\/)?/i', '$1'."$Flag/", $MediaURL);
}

/*
Code contributed from upekshapriya on the Blubrry Forums
*/
function powerpress_byte_size($ppbytes) 
{
	$ppsize = $ppbytes / 1024;
	if($ppsize < 1024)
	{
		$ppsize = number_format($ppsize, 1);
		$ppsize .= 'KB';
	} 
	else 
	{
		if($ppsize / 1024 < 1024) 
		{
			$ppsize = number_format($ppsize / 1024, 1);
			$ppsize .= 'MB';
		}
		else if ($ppsize / 1024 / 1024 < 1024)   
		{
		$ppsize = number_format($ppsize / 1024 / 1024, 1);
		$ppsize .= 'GB';
		} 
	}
	return $ppsize;
}

// Merges settings from feed settings page to empty custom feed settings
function powerpress_merge_empty_feed_settings($CustomFeedSettings, $FeedSettings)
{
	// Remove settings from main $FeedSettings that should not be copied to custom feed.
	unset($FeedSettings['itunes_new_feed_url']);
	unset($FeedSettings['apply_to']);
	unset($FeedSettings['feed_redirect_url']);
	unset($FeedSettings['itunes_complete']);
	unset($FeedSettings['itunes_block']);
	unset($FeedSettings['maximize_feed']);
	
	// If the setting is not already set, set the enhnaced itunes setting if they have PHP5+ on by default
	if( !isset($FeedSettings['enhance_itunes_summary']) )
		$FeedSettings['enhance_itunes_summary'] = 0;
 
	if( !$CustomFeedSettings )
		return $FeedSettings; // If the $CustomFeedSettings is false
 
	while( list($key,$value) = each($CustomFeedSettings) )
	{
		if( $value !== '' || !isset($FeedSettings[$key]) )
			$FeedSettings[$key] = $value;
	}
	
	return $FeedSettings;
}

function powerpress_readable_duration($duration, $include_hour=false)
{
	$seconds = 0;
	$parts = explode(':', $duration);
	if( count($parts) == 3 )
		$seconds = $parts[2] + ($parts[1]*60) + ($parts[0]*60*60);
	else if ( count($parts) == 2 )
		$seconds = $parts[1] + ($parts[0]*60);
	else
		$seconds = $parts[0];
	
	$hours = 0;
	$minutes = 0;
	if( $seconds >= (60*60) )
	{
		$hours = floor( $seconds /(60*60) );
		$seconds -= (60*60*$hours);
	}
	if( $seconds >= (60) )
	{
		$minutes = floor( $seconds /(60) );
		$seconds -= (60*$minutes);
	}
	
	if( $hours || $include_hour ) // X:XX:XX (readable)
		return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
	
	return sprintf('%d:%02d', $minutes, $seconds); // X:XX or 0:XX (readable)
}

// Duratoin in form of seconds (parses hh:mm:ss)
function powerpress_raw_duration($duration)
{
	$duration = trim($duration);
	$Parts = explode(':',$duration);
	if( empty($Parts) )
		return $duration;
	
	if( count($Parts) == 3 )
		return (($Parts[0]*60*60) + ($Parts[1]*60) +$Parts[2]);
	else if( count($Parts) == 2 )
		return (($Parts[0]*60) +$Parts[1]);
	//else if( count($Parts) == 1 )
	//	return ($Parts[0]);
 
	// We never found any colons, so we assume duration is seconds
	return $duration;
}

// For grabbing data from Podpress data stored serialized, the strings for some values can sometimes get corrupted, so we fix it...
function powerpress_repair_serialize($string)
{
	if( @unserialize($string) )
		return $string; // Nothing to repair...
	$string = preg_replace_callback('/(s:(\d+):"([^"]*)")/', 
			create_function(
					'$matches',
					'if( strlen($matches[3]) == $matches[2] ) return $matches[0]; return sprintf(\'s:%d:"%s"\', strlen($matches[3]), $matches[3]);'
			), 
			$string);
	
	if( substr($string, 0, 2) == 's:' ) // Sometimes the serialized data is double serialized, so we need to re-serialize the outside string
	{
		$string = preg_replace_callback('/(s:(\d+):"(.*)";)$/', 
			create_function(
					'$matches',
					'if( strlen($matches[3]) == $matches[2] ) return $matches[0]; return sprintf(\'s:%d:"%s";\', strlen($matches[3]), $matches[3]);'
			), 
			$string);
	}
	
	return $string;
}

/*
	powerpress_get_post_meta()
	Safe function to retrieve corrupted PodPress data from the database
	@post_id - post id to retrieve post meta for
	@key - key to retrieve post meta for
*/
function powerpress_get_post_meta($post_id, $key)
{
	$pp_meta_cache = wp_cache_get($post_id, 'post_meta');
	if ( !$pp_meta_cache ) {
		update_postmeta_cache($post_id);
		$pp_meta_cache = wp_cache_get($post_id, 'post_meta');
	}
	
	$meta = false;
	if ( isset($pp_meta_cache[$key]) )
		$meta = $pp_meta_cache[$key][0];
	
	if ( is_serialized( $meta ) ) // Logic used up but not including WordPress 2.8, new logic doesn't make sure if unserialized failed or not
	{
		if ( false !== ( $gm = @unserialize( $meta ) ) )
			return $meta;
	}
	
	return $meta;
}

function powerpress_get_enclosure($post_id, $feed_slug = 'podcast')
{
	$Data = powerpress_get_enclosure_data($post_id, $feed_slug);
	if( $Data )
		return $Data['url'];
	return false;
}

function powerpress_get_enclosure_data($post_id, $feed_slug = 'podcast')
{
	if( $feed_slug == 'podcast' || $feed_slug == '' )
		$MetaData = get_post_meta($post_id, 'enclosure', true);
	else
		$MetaData = get_post_meta($post_id, '_'. $feed_slug .':enclosure', true);
	
	if( !$MetaData )
		return false;
	
	$MetaParts = explode("\n", $MetaData, 4);
	
	$Serialized = false;
	$Data = array();
	$Data['id'] = $post_id;
	$Data['feed'] = $feed_slug;
	$Data['url'] = '';
	$Data['duration'] = '';
	$Data['size'] = '';
	$Data['type'] = '';
	$Data['width'] = '';
	$Data['height'] = '';
	
	if( count($MetaParts) > 0 )
		$Data['url'] = powerpress_add_redirect_url( trim($MetaParts[0]) );
	if( count($MetaParts) > 1 )
		$Data['size'] = trim($MetaParts[1]);
	if( count($MetaParts) > 2 )
		$Data['type'] = trim($MetaParts[2]);
	if( count($MetaParts) > 3 )
		$Serialized = $MetaParts[3];
	
	if( $Serialized )
	{
		$ExtraData = unserialize($Serialized);
		if( $ExtraData && is_array($ExtraData) )
		{
			while( list($key,$value) = each($ExtraData) )
				$Data[ $key ] = $value;
				
			if( isset($Data['length']) ) // Setting from the "Podcasting" plugin...
				$Data['duration'] = powerpress_readable_duration($Data['length'], true);
				
			if( isset($Data['webm_src']) )
				$Data['webm_src'] = powerpress_add_redirect_url( trim($Data['webm_src']) );
				
			if( strpos($MetaParts[0], 'http://') !== 0 && !empty($Data['hosting']) ) // if the URL is not set (just file name) and we're a hosting customer...
			{
				$post_status = get_post_status($post_id);
				switch( $post_status )
				{
					case 'pending':
					case 'draft':
					case 'auto-draft': {
						// Determine if audio or video, then set the demo episode here...
						$Data['url'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/preview.mp3'; // audio
						if( strstr($Data['type'], 'video') )
							$Data['url'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/preview.mp4'; // video
					}; break;
				}
			}
		}
	}
	
	// Check that the content type is a valid one...
	if( strstr($Data['type'], '/') == false )
		$Data['type'] = powerpress_get_contenttype($Data['url']);
		
	return $Data;
}

function powerpress_get_enclosure_data_podpress($post_id, $mediaNum = 0, $include_premium = false)
{
	$podPressMedia = powerpress_get_post_meta($post_id, 'podPressMedia');
	if( !$podPressMedia )
		$podPressMedia = powerpress_get_post_meta($post_id, '_podPressMedia'); // handles latest verions of PodPress
	if( $podPressMedia )
	{
		
		if( !is_array($podPressMedia) )
		{
			// Sometimes the stored data gets messed up, we can fix it here:
			$podPressMedia = powerpress_repair_serialize($podPressMedia);
			$podPressMedia = @unserialize($podPressMedia);
		}
		
		// Do it a second time in case it is double serialized
		if( !is_array($podPressMedia) )
		{
			// Sometimes the stored data gets messed up, we can fix it here:
			$podPressMedia = powerpress_repair_serialize($podPressMedia);
			$podPressMedia = @unserialize($podPressMedia);
		}
		
		if( is_array($podPressMedia) && isset($podPressMedia[$mediaNum]) && isset($podPressMedia[$mediaNum]['URI']) )
		{
			if( $include_premium == false && isset($podPressMedia[$mediaNum]['premium_only']) && ($podPressMedia[$mediaNum]['premium_only'] == 'on' || $podPressMedia[$mediaNum]['premium_only'] == true) )
				return false;
			
			$Data = array();
			$Data['id'] = $post_id;
			$Data['feed'] = 'podcast';
			$Data['duration'] = 0;
			$Data['url'] = '';
			$Data['size'] = 0;
			$Data['type'] = '';
			$Data['width'] = '';
			$Data['height'] = '';
			
			$Data['url'] = $podPressMedia[$mediaNum]['URI'];
			if( isset($podPressMedia[$mediaNum]['size']) )
				$Data['size'] = $podPressMedia[$mediaNum]['size'];
			if( isset($PodPressSettings[$mediaNum]['duration']) )
				$Data['duration'] = $podPressMedia[$mediaNum]['duration'];
			if( isset($PodPressSettings[$mediaNum]['previewImage']) )
				$Data['image'] = $podPressMedia[$mediaNum]['previewImage'];
			
			if( strpos($Data['url'], 'http://' ) !== 0 && strpos($Data['url'], 'https://' ) !== 0 )
			{
				$PodPressSettings = get_option('podPress_config');
				if( $PodPressSettings && isset($PodPressSettings['mediaWebPath']) )
					$Data['url'] = rtrim($PodpressSettings['mediaWebPath'], '/') . '/' . ltrim($Data['url'], '/');
				unset($PodPressSettings);
			}
			
			if( strpos($Data['url'], 'http://' ) !== 0 && strpos($Data['url'], 'https://' ) !== 0 )
			{
				$Settings = get_option('powerpress_general');
				if( $Settings && isset($Settings['default_url']) )
					$Data['url'] = rtrim($Settings['default_url'], '/') . '/' . ltrim($Data['url'], '/');
			}
			
			if( strpos($Data['url'], 'http://' ) !== 0 && strpos($Data['url'], 'https://' ) !== 0 )
				return false;
				
			$Data['type'] = powerpress_get_contenttype($Data['url']); // Detect the content type
			$Data['url'] = powerpress_add_redirect_url($Data['url']); // Add redirects to Media URL
			return $Data;
		}
	}
	return false;
}

function powerpress_get_apple_id($url)
{
	$results = preg_match('/id\=(\d+)/i', $url, $matches);
	if( !$results )
		$results = preg_match('/\/id(\d+)/i', $url, $matches);
	if( $results )
		return $matches[1];
	return 0;
}


function the_powerpress_all_players($slug = false, $no_link=false)
{
	echo get_the_powerpress_all_players($slug, $no_link);
}

function get_the_powerpress_all_players($slug = false, $no_link=false)
{
	$return = '';
	//Use this function to insert the Powerpress player anywhere in the page.
	//Made by Nicolas Bouliane (http://nicolasbouliane.com/)

	/*We're going to use the Loop to retrieve the latest post with the 'enclosure' custom key set
	//then interpret it and manually launch powerpressplayer_build with the URL contained within
	//that data.*/

	//Let's reset the Loop to make sure we look through all posts
	rewind_posts();
	
	// Get the list of podcast channel slug names...
	$GeneralSettings = get_option('powerpress_general');
	
	// No player or links to add to content...
	if( !empty($GeneralSettings['disable_appearance']) )
		return $return;
		
	$ChannelSlugs = array('podcast');
	if( $slug == false )
	{
		if( isset($GeneralSettings['custom_feeds']['podcast']) )
			$ChannelSlugs = array(); // Reset the array so it is added from the list in specified order
		while( list($feed_slug,$null) = each($GeneralSettings['custom_feeds']) )
			$ChannelSlugs[] = $feed_slug;
	}
	else if( is_array($slug) )
	{
		$ChannelSlugs = $slug;
	}
	else
	{
		$ChannelSlugs = array($slug);
	}
	
	// Loop through the posts
	while( have_posts() )
	{
		the_post();
		//$PostMetaKeys = get_post_custom_keys($post->ID);
		//print_r($PostMetaKeys);
		while( list($null,$feed_slug) = each($ChannelSlugs) )
		{
			// Do we follow the global settings to disable a player?
			if( isset($GeneralSettings['disable_player']) && isset($GeneralSettings['disable_player'][$feed_slug]) && $slug == false )
				continue;
			
			$EpisodeData = powerpress_get_enclosure_data(get_the_ID(), $feed_slug);
			if( !$EpisodeData && !empty($GeneralSettings['process_podpress']) && $feed_slug == 'podcast' )
				$EpisodeData = powerpress_get_enclosure_data_podpress(get_the_ID());
				
			if( !$EpisodeData )
				continue;
			
			$AddDefaultPlayer = true;
			if( !empty($EpisodeData['embed']) )
			{
				$return .= $EpisodeData['embed'];
				if( !empty($GeneralSettings['embed_replace_player']) )
					$AddDefaultPlayer = false;
			}
			
			if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] && !powerpress_premium_content_authorized($GeneralSettings) )
			{
				$return .= powerpress_premium_content_message(get_the_ID(), $feed_slug, $EpisodeData);
				continue;
			}
				
			if( !isset($EpisodeData['no_player']) && $AddDefaultPlayer )
			{
				$return .= apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
			}
			if( !isset($EpisodeData['no_links']) && $no_link == false )
			{
				$return .= apply_filters('powerpress_player_links', '',  powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
				// $return .= powerpress_get_player_links(get_the_ID(), $feed_slug, $EpisodeData );
			}
		}
		reset($ChannelSlugs);
	}
	
	return $return;
}


function powerpress_premium_content_authorized($feed_slug)
{
	if( $feed_slug != 'podcast' )
	{
		$FeedSettings = get_option('powerpress_feed_'. $feed_slug);
		if( isset($FeedSettings['premium']) && $FeedSettings['premium'] != '' )
			return current_user_can($FeedSettings['premium']);
	}
	return true; // any user can access this content
}

function powerpress_premium_content_message($post_id, $feed_slug, $EpisodeData = false)
{
	if( !$EpisodeData && $post_id )
		$EpisodeData = powerpress_get_enclosure_data($post_id, $feed_slug);
		
	if( !$EpisodeData )
		return '';
	$FeedSettings = get_option('powerpress_feed_'.$feed_slug);
	
	$extension = 'unknown';
	$parts = pathinfo($EpisodeData['url']);
	if( $parts && isset($parts['extension']) )
		$extension  = strtolower($parts['extension']);
		
	if( isset($FeedSettings['premium_label']) && $FeedSettings['premium_label'] != '' ) // User has a custom label
		return '<p class="powerpress_links powerpress_links_'. $extension .'">'. $FeedSettings['premium_label'] . '</p>'.PHP_EOL;
	
	return '<p class="powerpress_links powerpress_links_'. $extension .'">'. htmlspecialchars($FeedSettings['title']) .': <a href="'. get_bloginfo('url') .'/wp-login.php" title="Protected Content">(Protected Content)</a></p>'.PHP_EOL;
}

function powerpress_is_mobile_client()
{
	if( preg_match('/('.POWERPRESS_MOBILE_REGEX.')/i', $_SERVER['HTTP_USER_AGENT'], $matches) )
	{
		if( !empty($matches[1]) )
			return $matches[1];
	}
	
	return false;
}
/*
End Helper Functions
*/

// Are we in the admin?
if( is_admin() )
{
	require_once(POWERPRESS_ABSPATH.'/powerpressadmin.php');
	register_activation_hook( __FILE__, 'powerpress_admin_activate' );
}

?>