<?php
/*
PowerPress player options
*/


function powerpressplayer_get_next_id()
{
	if( !isset($GLOBALS['g_powerpress_player_id']) ) // Use the global unique player id variable for the surrounding div
		$GLOBALS['g_powerpress_player_id'] = rand(0, 10000);
	else
		$GLOBALS['g_powerpress_player_id']++; // increment the player id for the next div so it is unique
	return $GLOBALS['g_powerpress_player_id'];
}

function powerpressplayer_get_extension($media_url, $EpisodeData = array() )
{
	$extension = 'unknown';
	$parts = pathinfo($media_url);
	if( !empty($parts['extension']) )
		$extension = strtolower($parts['extension']);
	
	// Hack to use the audio/mp3 content type to set extension to mp3, some folks use tinyurl.com to mp3 files which remove the file extension...
	if( isset($EpisodeData['type']) && $EpisodeData['type'] == 'audio/mpeg' && $extension != 'mp3' )
		$extension = 'mp3';
	
	return $extension;
}

/*
Initialize powerpress player handling
*/
function powerpressplayer_init($GeneralSettings)
{
	if( isset($_GET['powerpress_pinw']) )
		powerpress_do_pinw($_GET['powerpress_pinw'], !empty($GeneralSettings['process_podpress']) );
		
	if( isset($_GET['powerpress_embed']) )
	{
		$player = ( !empty($_GET['powerpress_player']) ? $_GET['powerpress_player'] : 'default' );
		powerpress_do_embed($player, $_GET['powerpress_embed'], !empty($GeneralSettings['process_podpress']) );
	}
	
	// If we are to process podpress data..
	if( !empty($GeneralSettings['process_podpress']) )
	{
		add_shortcode('display_podcast', 'powerpress_shortcode_handler');
	}
	
	if( defined('POWERPRESS_ENQUEUE_SCRIPTS') )
	{
		// include what's needed for each plaer
		wp_enqueue_script( 'powerpress-player', powerpress_get_root_url() .'player.js');
	}
	
	/*
	if( !empty($GeneralSettings['display_player_disable_mobile']) && powerpress_is_mobile_client() )
	{
		// Remove all known filters for player embeds...
		remove_filter('powerpress_player', 'powerpressplayer_player_audio', 10, 3);
		remove_filter('powerpress_player', 'powerpressplayer_player_video', 10, 3);
		remove_filter('powerpress_player', 'powerpressplayer_player_other', 10, 3);
	}
	*/
}


function powerpress_shortcode_handler( $attributes, $content = null )
{
	global $post, $g_powerpress_player_added;
	
	// We can't add flash players to feeds
	if( is_feed() )
		return '';
	
	$return = '';
	$feed = '';
	$url = '';
	$image = '';
	$width = '';
	$height = '';
	
	extract( shortcode_atts( array(
			'url' => '',
			'feed' => '',
			'image' => '',
			'width' => '',
			'height' => ''
		), $attributes ) );
	
	if( !$url && $content )
	{
		$content_url = trim($content);
		if( @parse_url($content_url) )
			$url = $content_url;
	}
	
	if( $url )
	{
		$url = powerpress_add_redirect_url($url);
		$content_type = '';
		// Handle the URL differently...
		$return = apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($url, 'p'), array('image'=>$image, 'type'=>$content_type,'width'=>$width, 'height'=>$height) );
	}
	else if( $feed )
	{
		$EpisodeData = powerpress_get_enclosure_data($post->ID, $feed);
		if( !empty($EpisodeData['embed']) )
			$return = $EpisodeData['embed'];
		
		// Shortcode over-ride settings:
		if( !empty($image) )
			$EpisodeData['image'] = $image;
		if( !empty($width) )
			$EpisodeData['width'] = $width;
		if( !empty($height) )
			$EpisodeData['height'] = $height;
		
		if( !isset($EpisodeData['no_player']) )
		{
			if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] && !powerpress_premium_content_authorized($feed) )
			{
				$return .= powerpress_premium_content_message($post->ID, $feed, $EpisodeData);
				continue;
			}
			
			if( !isset($EpisodeData['no_player']) )
				$return = apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), array('id'=>$post->ID,'feed'=>$feed, 'image'=>$image, 'type'=>$EpisodeData['type'],'width'=>$width, 'height'=>$height) );
			if( empty($EpisodeData['no_links']) )
				$return .= apply_filters('powerpress_player_links', '',  powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
		}
	}
	else
	{
		$GeneralSettings = get_option('powerpress_general');
		if( !isset($GeneralSettings['custom_feeds']['podcast']) )
			$GeneralSettings['custom_feeds']['podcast'] = 'Podcast Feed'; // Fixes scenario where the user never configured the custom default podcast feed.
		
		while( list($feed_slug,$feed_title)  = each($GeneralSettings['custom_feeds']) )
		{
			if( isset($GeneralSettings['disable_player']) && isset($GeneralSettings['disable_player'][$feed_slug]) )
				continue;
			
			$EpisodeData = powerpress_get_enclosure_data($post->ID, $feed_slug);
			if( !$EpisodeData && !empty($GeneralSettings['process_podpress']) && $feed_slug == 'podcast' )
				$EpisodeData = powerpress_get_enclosure_data_podpress($post->ID);
				
			if( !$EpisodeData )
				continue;
				
			if( !empty($EpisodeData['embed']) )
				$return .= $EpisodeData['embed'];
			
			// Shortcode over-ride settings:
			if( !empty($image) )
				$EpisodeData['image'] = $image;
			if( !empty($width) )
				$EpisodeData['width'] = $width;
			if( !empty($height) )
				$EpisodeData['height'] = $height;
				
			if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] && !powerpress_premium_content_authorized($GeneralSettings) )
			{
				$return .= powerpress_premium_content_message($post->ID, $feed_slug, $EpisodeData);
				continue;
			}
				
			if( !isset($EpisodeData['no_player']) )
			{
				$return .= apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
			}
			if( !isset($EpisodeData['no_links']) )
			{
				$return .= apply_filters('powerpress_player_links', '',  powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
			}
		}
	}
	
	return $return;
}

add_shortcode('powerpress', 'powerpress_shortcode_handler');
if( !defined('PODCASTING_VERSION') )
{
	add_shortcode('podcast', 'powerpress_shortcode_handler');
}




function powerpress_player_filter($content, $media_url, $ExtraData = array() )
{
	global $g_powerpress_player_id;
	if( !isset($g_powerpress_player_id) )
		$g_powerpress_player_id = rand(0, 10000);
	else
		$g_powerpress_player_id++;
		
	// Very important setting, we need to know if the media should auto play or not...
	$autoplay = false; // (default)
	if( isset($ExtraData['autoplay']) && $ExtraData['autoplay'] )
		$autoplay = true;
	$cover_image = '';
	if( $ExtraData['image'] )
		$cover_image = $ExtraData['image'];
	
	// Based on $ExtraData, we can determine which type of player to handle here...
	$Settings = get_option('powerpress_general');
	$player_width = 400;
	$player_height = 225;
	if( isset($Settings['player_width']) && $Settings['player_width'] )
		$player_width = $Settings['player_width'];
	if( isset($Settings['player_height']) && $Settings['player_height'] )
		$player_height = $Settings['player_height'];
	
	// Used with some types
	//$content_type = powerpress_get_contenttype($media_url);
	
	$parts = pathinfo($media_url);
	// Hack to use the audio/mp3 content type to set extension to mp3, some folks use tinyurl.com to mp3 files which remove the file extension...
	// This hack only covers mp3s.
	if( isset($EpisodeData['type']) && $EpisodeData['type'] == 'audio/mpeg' && $parts['extension'] != 'mp3' )
		$parts['extension'] = 'mp3';
	
	$ogg_video = false; // Extra securty make sure it is definitely set to video
	switch( strtolower($parts['extension']) )
	{
		// Quicktime old formats:
		case 'm4a':
		case 'avi':
		case 'mpg':
		case 'mpeg':
		
		case 'm4b':
		case 'm4r':
		case 'qt':
		case 'mov': {
			
			$GeneralSettings = get_option('powerpress_general');
			if( !isset($GeneralSettings['player_scale']) )
				$GeneralSettings['player_scale'] = 'tofit';
				
			// If there is no cover image specified, lets use the default...
			if( $cover_image == '' )
				$cover_image = powerpress_get_root_url() . 'play_video_default.jpg';
			
			if( $autoplay )
			{
				$content .= '<div class="powerpress_player" id="powerpress_player_'. $g_powerpress_player_id .'"></div>'.PHP_EOL;
				$content .= '<script type="text/javascript"><!--'.PHP_EOL;
				$content .= "powerpress_embed_quicktime('powerpress_player_{$g_powerpress_player_id}', '{$media_url}', {$player_width}, {$player_height}, '{$GeneralSettings['player_scale']}');\n";
				$content .= "//-->\n";
				$content .= "</script>\n";
			}
			else
			{
				$content .= '<div class="powerpress_player" id="powerpress_player_'. $g_powerpress_player_id .'">'.PHP_EOL;
				$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="';
				$content .= "return powerpress_embed_quicktime('powerpress_player_{$g_powerpress_player_id}', '{$media_url}', {$player_width}, {$player_height}, '{$GeneralSettings['player_scale']}' );";
				$content .= '">';
				$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" />';
				$content .= '</a>';
				$content .= "</div>\n";
			}
			
		}; break;
		
		// Windows Media:
		case 'wma':
		case 'wmv':
		case 'asf': {
			
			$content .= '<div class="powerpress_player" id="powerpress_player_'. $g_powerpress_player_id .'">';
			$firefox = (stristr($_SERVER['HTTP_USER_AGENT'], 'firefox') !== false );
			
			if( (!$cover_image && !$firefox ) || $autoplay ) // if we don't have a cover image or we're supposed to auto play the media anyway...
			{
				$content .= '<object id="winplayer" classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="'. $player_width .'" height="'. $player_height .'" standby="..." type="application/x-oleobject">';
				$content .= '	<param name="url" value="'. $media_url .'" />';
				$content .= '	<param name="AutoStart" value="'. ($autoplay?'true':'false') .'" />';
				$content .= '	<param name="AutoSize" value="true" />';
				$content .= '	<param name="AllowChangeDisplaySize" value="true" />';
				$content .= '	<param name="standby" value="Media is loading..." />';
				$content .= '	<param name="AnimationAtStart" value="true" />';
				$content .= '	<param name="scale" value="aspect" />';
				$content .= '	<param name="ShowControls" value="true" />';
				$content .= '	<param name="ShowCaptioning" value="false" />';
				$content .= '	<param name="ShowDisplay" value="false" />';
				$content .= '	<param name="ShowStatusBar" value="false" />';
				$content .= '	<embed type="application/x-mplayer2" src="'. $media_url .'" width="'. $player_width .'" height="'. $player_height .'" scale="ASPECT" autostart="'. ($autoplay?'1':'0') .'" ShowDisplay="0" ShowStatusBar="0" autosize="1" AnimationAtStart="1" AllowChangeDisplaySize="1" ShowControls="1"></embed>';
				$content .= '</object>';
			}
			else
			{
				if( $cover_image == '' )
					$cover_image = powerpress_get_root_url() . 'play_video_default.jpg';
				
				$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="';
				$content .= "return powerpress_embed_winplayer('powerpress_player_{$g_powerpress_player_id}', '{$media_url}', {$player_width}, {$player_height} );";
				$content .= '">';
				$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" />';
				$content .= '</a>';
			}
			
			if( $firefox )
			{
				$content .= '<p style="font-size: 85%;margin-top:0;">'. __('Best viewed with', 'powerpress');
				$content .= ' <a href="http://support.mozilla.com/en-US/kb/Using+the+Windows+Media+Player+plugin+with+Firefox#Installing_the_plugin" target="_blank">';
				$content .= __('Windows Media Player plugin for Firefox', 'powerpress') .'</a></p>';
			}
			
			$content .= "</div>\n";
			
		}; break;
		
		// Flash:
		case 'swf': {
			
			// If there is no cover image specified, lets use the default...
			if( $cover_image == '' )
				$cover_image = powerpress_get_root_url() . 'play_video_default.jpg';
			
			$content .= '<div class="powerpress_player" id="powerpress_player_'. $g_powerpress_player_id .'">';
			if( !$autoplay )
			{
				$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="';
				$content .= "return powerpress_embed_swf('powerpress_player_{$g_powerpress_player_id}', '{$media_url}', {$player_width}, {$player_height} );";
				$content .= '">';
				$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" />';
				$content .= '</a>';
			}
			$content .= "</div>\n";
			if( $autoplay )
			{
				$content .= '<script type="text/javascript"><!--'.PHP_EOL;
				$content .= "powerpress_embed_swf('powerpress_player_{$g_powerpress_player_id}', '{$media_url}', {$player_width}, {$player_height} );\n";
				$content .= "//-->\n";
				$content .= "</script>\n";
			}
			
		}; break;
			
		// Default, just display the play image. If it is set for auto play, then we don't wnat to open a new window, otherwise we want to open this in a new window..
		default: {
		
			if( $cover_image == '' )
				$cover_image = powerpress_get_root_url() . 'play_video_default.jpg';
			
			$content .= '<div class="powerpress_player" id="powerpress_player_'. $g_powerpress_player_id .'">';
			$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'"'. ($autoplay?'':' target="_blank"') .'>';
			$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" />';
			$content .= '</a>';
			$content .= "</div>\n";
			
		}; break;
	}
	
	return $content;
}

//add_filter('powerpress_player', 'powerpress_player_filter', 10, 3);


/*
// Everything in $ExtraData except post_id
*/
function powerpress_generate_embed($player, $EpisodeData) // $post_id, $feed_slug, $width=false, $height=false, $media_url = false, $autoplay = false)
{
	if( empty($EpisodeData['id']) && empty($EpisodeData['feed']) )
		return '';
	
	$width = 0;
	$height = 0;
	if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
		$width = $EpisodeData['width'];
	if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) )
		$height = $EpisodeData['height'];
	
	// More efficient, only pull the general settings if necessary
	if( $height == 0 || $width == 0 )
	{
		$GeneralSettings = get_option('powerpress_general');
		if( $width == 0 )
		{
			$width = 400;
			if( !empty($GeneralSettings['player_width']) )
				$width = $GeneralSettings['player_width'];
		}
		
		if( $height == 0 )
		{
			$height = 400;
			if( !empty($GeneralSettings['player_height']) )
				$height = $GeneralSettings['player_height'];
		}
		
		$extension = powerpressplayer_get_extension($EpisodeData['url']);
		if(  ($extension == 'mp3' || $extension == 'm4a') && empty($Settings['poster_image_audio']) )
		{
			$height = 24; // Hack for audio to only include the player without the poster art
			$width = 320;
			if( !empty($GeneralSettings['player_width_audio']) )
				$width = $GeneralSettings['player_width_audio'];
		}
	}
	
	$embed = '';
	$url = get_bloginfo('url') .'/?powerpress_embed=' . $EpisodeData['id'] .'-'. $EpisodeData['feed'];
	if( isset($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$url .= '&amp;autoplay=true';
		
	$url .= '&amp;powerpress_player='.$player;
	$embed .= '<iframe';
	//$embed .= ' class="powerpress-player-embed"';
	$embed .= ' width="'. $width .'"';
	$embed .= ' height="'. $height .'"';
	$embed .= ' src="'. $url .'"';
	$embed .= ' frameborder="0" scrolling="no"';
	$embed .= '></iframe>';
	return $embed;
}

function powerpressplayer_build_embed($player, $media_url, $EpisodeData = array() )
{
	if( !$player )
		return '';
	
	if( empty($EpisodeData['id']) )
	{
		if( get_the_ID() )
		 $EpisodeData['id'] = get_the_ID();
	}
	
	// We don't have enough info to build an embed for this post or page
	if( empty($EpisodeData['id']) && empty($EpisodeData['feed']) )
		return '';
	
	$extension = powerpressplayer_get_extension($media_url, $ExtraData);
	$width = 400;
	$height = 225;
	
	// Configure width/height based on player
	switch( $player )
	{
		case 'audio-player': {

			$height = 25;
			$width = 290;
			if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
			{
				$width = $EpisodeData['width'];
			}
			else
			{
				$PlayerSettings = get_option('powerpress_audio-player');
				if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
					$width = $EpisodeData['width'];
			}
		
		}; break;
		case 'flashmp3-maxi': {
			
			$PlayerSettings = get_option('powerpress_flashmp3-maxi');
			$height = 20;
			$width = 200;
			if( !empty($PlayerSettings['width']) && is_numeric($PlayerSettings['width']) )
				$width = $PlayerSettings['width'];
			if( !empty($PlayerSettings['height']) && is_numeric($PlayerSettings['height']) )
				$height = $PlayerSettings['height'];
			if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
				$width = $EpisodeData['width'];
			if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) )
				$height = $EpisodeData['height'];
			
		}; break;
		default: { // Other players are currently not supported
			return '';
		};
	}

	$embed = '';
	$url = get_bloginfo('url') .'/?powerpress_embed=' . $EpisodeData['id'] .'-'. $EpisodeData['feed'];
	if( isset($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$url .= '&amp;autoplay=true';
		
	$url .= '&amp;powerpress_player='.$player;
	$embed .= '<iframe';
	$embed .= ' class="powerpress-player-embed"';
	$embed .= ' width="'. $width .'"';
	$embed .= ' height="'. $height .'"';
	$embed .= ' src="'. $url .'"';
	$embed .= ' frameborder="0"';
	$embed .= '></iframe>';
	return $embed;
}


function powerpressplayer_in_embed($player, $media_url, $EpisodeData = array())
{
	$content = '';
	$content .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'. PHP_EOL;
	$content .= '<html xmlns="http://www.w3.org/1999/xhtml">'. PHP_EOL;
	$content .= '<head>'. PHP_EOL;
	$content .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'. PHP_EOL;
	$content .= '<title>'. __('Blubrry PowerPress Player', 'powerpress') .'</title>'. PHP_EOL;
	$content .= '<meta name="robots" content="noindex" />'. PHP_EOL;
	$content .= '<script type="text/javascript" src="'. powerpress_get_root_url() .'player.js"></script>'. PHP_EOL;
	// Include jQuery for convenience
	$content .= '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>'. PHP_EOL;
	$content .= '<script language="javascript" type="text/javascript"><!--'. PHP_EOL;
	$content .= 'powerpress_url = \''. powerpress_get_root_url() .'\''. PHP_EOL;
	$content .= 'jQuery(document).ready(function($) {'. PHP_EOL;
	$content .= '  powerpress_resize_player();'. PHP_EOL;
	$content .= '  $(window).resize(function() {'. PHP_EOL;
	$content .= '    powerpress_resize_player();'. PHP_EOL;
	$content .= '  });'. PHP_EOL;

	$content .= '});'. PHP_EOL;
	$content .= 'function powerpress_resize_player() {'. PHP_EOL;
	$content .= '  jQuery(\'video\').css(\'width\', jQuery(window).width() );'. PHP_EOL;
	$content .= '  jQuery(\'video\').css(\'height\', jQuery(window).height() );'. PHP_EOL;
	$content .= '  jQuery(\'.powerpress_player .powerpress-player-poster\').css(\'width\', jQuery(window).width() );'. PHP_EOL;
	$content .= '  jQuery(\'.powerpress_player .powerpress-player-poster\').css(\'height\', jQuery(window).height() );'. PHP_EOL;
	$content .= '  jQuery(\'.powerpress_player .powerpress-player-play-image\').css(\'bottom\', Math.floor( (jQuery(window).height()/2)-30)+\'px\');'. PHP_EOL;
	$content .= '  jQuery(\'.powerpress_player .powerpress-player-play-image\').css(\'left\', Math.floor( (jQuery(window).width()/2)-30)+\'px\');'. PHP_EOL;
	$content .= '  jQuery(\'embed\').css(\'width\', jQuery(window).width() );'. PHP_EOL;
	$content .= '  jQuery(\'embed\').css(\'height\', jQuery(window).height() );'. PHP_EOL;
	$content .= '}'. PHP_EOL;
	$content .= "//-->\n";
	$content .= '</script>'. PHP_EOL;
	
	// Head specific settings for player
	switch( $player )
	{
		case 'html5video': {
			//$Settings = get_option('powerpress_general');
			// TODO: Need to include javascript to insert HTML5 player (with FlowPlayer Classic fallback for mp4)

		}; break;
		case 'flow-player-classic': {
			//$Settings = get_option('powerpress_general');
			// TODO: Need to include javascript to insert HTML5 player (with FlowPlayer Classic fallback for mp4)

		}; break;
		case 'default': {
			//$Settings = get_option('powerpress_general');
			// TODO: Need to include javascript to insert HTML5 player (with FlowPlayer Classic fallback for mp4)

		}; break;
	}
	
	$content .= '<style type="text/css" media="screen">' . PHP_EOL;
	$content .= '	body { font-size: 13px; font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; } img { border: 0; }' . PHP_EOL;
	$content .= '</style>' . PHP_EOL;
	$content .= '</head>'. PHP_EOL;
	$content .= '<body>'. PHP_EOL;
	
	// Body specific content for player
	switch( $player )
	{
		case 'default':
		case 'flow-player-classic': {
		
			if( !is_array($EpisodeData) )
				$EpisodeData = array();

			$content .= powerpressplayer_build_flowplayerclassic($media_url, $EpisodeData + array('jquery_autowidth'=>true) );
			
			// $content .=  'Video Flow Player Classic coming soon!';
		}; break;
		case 'html5video': {
			$content .= powerpressplayer_build_html5video($media_url, $EpisodeData);
		}; break;
		default: {
			$content .= '<strong>'. __('Player Not Available', 'powerpress') .'</strong>';
		};
	}
	
	$content .= '</body>'. PHP_EOL;
	$content .= '</html>'. PHP_EOL;
	return $content;
}


/*
Audio Players - Flash/HTML5 compliant mp3 audio

@since 2.0
@content - 
@param string $content Content of post or page to add player to
@param string $media_url Media URL to add player for
@param array $EpisodeData Array of key/value settings that optionally can contribute to player being added
@return string $content The content, possibly modified wih player added
*/
function powerpressplayer_player_audio($content, $media_url, $EpisodeData = array() )
{
	if( powerpress_is_mobile_client() ) // Mobile clients are handled in powerpressplayer_player_other
		return $content;
	
	$extension = powerpressplayer_get_extension($media_url);
	switch( $extension )
	{
		// MP3
		case 'mp3':
		{
			$Settings = get_option('powerpress_general');
			if( !isset($Settings['player']) )
				$Settings['player'] = 'default';
			
			switch( $Settings['player'] )
			{
				case 'default':
				case 'flow-player-classic': {
					$content .= powerpressplayer_build_flowplayerclassic($media_url, $EpisodeData);
				}; break;
				case 'audio-player': {
					$content .= powerpressplayer_build_1pxoutplayer($media_url, $EpisodeData);
				}; break;
				case 'flashmp3-maxi': {
					$content .= powerpressplayer_build_flashmp3maxi($media_url, $EpisodeData);
				}; break;
				case 'audioplay': {
					$content .= powerpressplayer_build_audioplay($media_url, $EpisodeData);
				}; break;
				case 'simple_flash': {
					$content .= powerpressplayer_build_simpleflash($media_url, $EpisodeData);
				}; break;
				case 'html5audio': {
					$content .= powerpressplayer_build_html5audio($media_url, $EpisodeData);
				}; break;
			}
		
		}; break;
		case 'm4a': {
		
			$Settings = get_option('powerpress_general');
			if( empty($Settings['m4a']) || $Settings['m4a'] != 'use_players' )
				break;
			
			if( !isset($Settings['player']) )
				$Settings['player'] = 'default';
			
			switch( $Settings['player'] )
			{
				case 'default':
				case 'flow-player-classic': {
					$content .= powerpressplayer_build_flowplayerclassic($media_url, $EpisodeData);
				}; break;
				case 'html5audio': {
					$content .= powerpressplayer_build_html5audio($media_url, $EpisodeData);
				}; break;
				default: {
					$content .= powerpressplayer_build_playimageaudio($media_url, true);
				};
			}
		
			// Use Flow player if configured
		}; break;
		case 'ogg': {
			if( defined('POWERPRESS_OGG_VIDEO') && POWERPRESS_OGG_VIDEO )
				return $content; // Ogg is handled as video
		}
		case 'oga': {
			$content .= powerpressplayer_build_html5audio($media_url, $EpisodeData);
		}; break;
	}

	return $content;
}

/*
Video Players - HTML5/Flash compliant video formats
*/
function powerpressplayer_player_video($content, $media_url, $EpisodeData = array() )
{
	if( powerpress_is_mobile_client() ) // Mobile clients are handled in powerpressplayer_player_other
		return $content;
	
	$extension = powerpressplayer_get_extension($media_url);
	switch( $extension )
	{
		// OGG (audio or video)
		case 'ogg': {
			// Ogg special case, we treat as audio unless specified otherwise
			if( !defined('POWERPRESS_OGG_VIDEO') || POWERPRESS_OGG_VIDEO == false )
				return $content;
		}
		// OGG Video / WebM
		case 'webm': 
		case 'ogv': { // Use native player when possible
			$Settings = get_option('powerpress_general');
			if( !isset($Settings['video_player']) )
				$Settings['video_player'] = 'html5video';
			
			// HTML5 Video as an embed
			switch( $Settings['video_player'] )
			{
				case 'videojs-html5-video-player-for-wordpress': {
					$content .= powerpressplayer_build_videojs($media_url, $EpisodeData);
				}; break;
				default: {
					$content .= powerpressplayer_build_html5video($media_url, $EpisodeData);
				}; break;
			}
		}; break;
		// H.264
		case 'm4v':
		case 'mp4':
		// Okay, lets see if we we have a player setup to handle this
		{
			
			$Settings = get_option('powerpress_general');
			if( !isset($Settings['video_player']) )
				$Settings['video_player'] = 'html5video';
			
			switch( $Settings['video_player'] )
			{
				case 'default':
				case 'flow-player-classic': {
					
					$content .= powerpressplayer_build_flowplayerclassic($media_url, $EpisodeData);
				}; break;
				case 'videojs-html5-video-player-for-wordpress': {
					$content .= powerpressplayer_build_videojs($media_url, $EpisodeData);
				}; break;
				case 'html5video': {
					// HTML5 Video as an embed
					$content .= powerpressplayer_build_html5video($media_url, $EpisodeData);
				}; break;
			}
		}; break;
	}
	
	return $content;
}

function powerpressplayer_player_other($content, $media_url, $EpisodeData = array() )
{
	if( powerpress_is_mobile_client() )
	{
		$content .= powerpressplayer_build_html5mobile($media_url, $EpisodeData);
		return $content;
	}
	
	// Very important setting, we need to know if the media should auto play or not...
	$autoplay = false; // (default)
	if( isset($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$autoplay = true;
	$cover_image = '';
	if( !empty($EpisodeData['image']) )
		$cover_image = $EpisodeData['image'];
	
	$extension = powerpressplayer_get_extension($media_url);
	
	switch( $extension )
	{
		// Common formats, we already handle them separately
		case 'mp3':
		case 'mp4':
		case 'm4v':
		case 'webm';
		case 'ogg':
		case 'ogv':
		case 'oga':
		{
			return $content; 
		}; break;
		case 'flv': {
			$content .= powerpressplayer_build_flowplayerclassic($media_url, $EpisodeData);
		}; break;
		case 'm4a': // Special case for thos audiobook folks (could be modern player, could be old embed)
		// Old Quicktime formats:
		case 'avi':
		case 'mpg':
		case 'mpeg':
		case 'm4b':
		case 'm4r':
		case 'qt':
		case 'mov': {
			
			$Settings = get_option('powerpress_general');
			
			// Special case for thos audiobook folks
			if( $extension == 'm4a' && !empty($Settings['m4a']) && $Settings['m4a'] == 'use_players' )
				break;
			
			$player_id = powerpressplayer_get_next_id();
			$player_width = 400;
			$player_height = 225;
			$scale = 'tofit';
			if( !empty($Settings['player_width']) )
				$player_width = $Settings['player_width'];
			if( !empty($Settings['player_height']) )
				$player_height = $Settings['player_height'];
			if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
				$player_width = $EpisodeData['width'];
			if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) )
				$player_height = $EpisodeData['height'];
				
			if( !empty($Settings['player_scale']) )
				$scale = $Settings['player_scale'];
				
			// If there is no cover image specified, lets use the default...
			if( $cover_image == '' )
				$cover_image = powerpress_get_root_url() . 'play_video_default.jpg';
			
			if( $autoplay )
			{
				$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'"></div>'.PHP_EOL;
				$content .= '<script type="text/javascript"><!--'.PHP_EOL;
				$content .= "powerpress_embed_quicktime('powerpress_player_{$player_id}', '{$media_url}', {$player_width}, {$player_height}, '{$scale}');\n";
				$content .= "//-->\n";
				$content .= "</script>\n";
			}
			else
			{
				$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">'.PHP_EOL;
				$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="';
				$content .= "return powerpress_embed_quicktime('powerpress_player_{$player_id}', '{$media_url}', {$player_width}, {$player_height}, '{$scale}' );";
				$content .= '">';
				$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="width: '. $player_width .'px; height: '.$player_height .'px;" />';
				$content .= '</a>';
				$content .= "</div>\n";
			}
			
		}; break;
		
		// Windows Media:
		case 'wma':
		case 'wmv':
		case 'asf': {
			
			$Settings = get_option('powerpress_general');
			$player_id = powerpressplayer_get_next_id();
			$player_width = 400;
			$player_height = 225;
			$scale = 'tofit';
			if( !empty($Settings['player_width']) )
				$player_width = $Settings['player_width'];
			if( !empty($Settings['player_height']) )
				$player_height = $Settings['player_height'];
			if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
				$player_width = $EpisodeData['width'];
			if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) )
				$player_height = $EpisodeData['height'];
				
			// If there is no cover image specified, lets use the default...
			if( $cover_image == '' )
				$cover_image = powerpress_get_root_url() . 'play_video_default.jpg';
				
			$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
			$firefox = (stristr($_SERVER['HTTP_USER_AGENT'], 'firefox') !== false );
			
			if( (!$cover_image && !$firefox ) || $autoplay ) // if we don't have a cover image or we're supposed to auto play the media anyway...
			{
				$content .= '<object id="winplayer" classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="'. $player_width .'" height="'. $player_height .'" standby="..." type="application/x-oleobject">';
				$content .= '	<param name="url" value="'. $media_url .'" />';
				$content .= '	<param name="AutoStart" value="'. ($autoplay?'true':'false') .'" />';
				$content .= '	<param name="AutoSize" value="true" />';
				$content .= '	<param name="AllowChangeDisplaySize" value="true" />';
				$content .= '	<param name="standby" value="Media is loading..." />';
				$content .= '	<param name="AnimationAtStart" value="true" />';
				$content .= '	<param name="scale" value="aspect" />';
				$content .= '	<param name="ShowControls" value="true" />';
				$content .= '	<param name="ShowCaptioning" value="false" />';
				$content .= '	<param name="ShowDisplay" value="false" />';
				$content .= '	<param name="ShowStatusBar" value="false" />';
				$content .= '	<embed type="application/x-mplayer2" src="'. $media_url .'" width="'. $player_width .'" height="'. $player_height .'" scale="ASPECT" autostart="'. ($autoplay?'1':'0') .'" ShowDisplay="0" ShowStatusBar="0" autosize="1" AnimationAtStart="1" AllowChangeDisplaySize="1" ShowControls="1"></embed>';
				$content .= '</object>';
			}
			else
			{
				if( $cover_image == '' )
					$cover_image = powerpress_get_root_url() . 'play_video_default.jpg';
				
				$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="';
				$content .= "return powerpress_embed_winplayer('powerpress_player_{$player_id}', '{$media_url}', {$player_width}, {$player_height} );";
				$content .= '">';
				$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" />';
				$content .= '</a>';
			}
			
			if( $firefox )
			{
				$content .= '<p style="font-size: 85%;margin-top:0;">'. __('Best viewed with', 'powerpress');
				$content .= ' <a href="http://support.mozilla.com/en-US/kb/Using+the+Windows+Media+Player+plugin+with+Firefox#Installing_the_plugin" target="_blank">';
				$content .= __('Windows Media Player plugin for Firefox', 'powerpress') .'</a></p>';
			}
			
			$content .= "</div>\n";
			
		}; break;
		
		// Flash:
		case 'swf': {
		
			$Settings = get_option('powerpress_general');
			$player_id = powerpressplayer_get_next_id();
			$player_width = 400;
			$player_height = 225;
			if( !empty($Settings['player_width']) )
				$player_width = $Settings['player_width'];
			if( !empty($Settings['player_height']) )
				$player_height = $Settings['player_height'];
			if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
				$player_width = $EpisodeData['width'];
			if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) )
				$player_height = $EpisodeData['height'];
				
			// If there is no cover image specified, lets use the default...
			if( $cover_image == '' )
				$cover_image = powerpress_get_root_url() . 'play_video_default.jpg';
			
			$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
			if( !$autoplay )
			{
				$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="';
				$content .= "return powerpress_embed_swf('powerpress_player_{$player_id}','{$media_url}',{$player_width},{$player_height});";
				$content .= '">';
				$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" />';
				$content .= '</a>';
			}
			$content .= "</div>\n";
			if( $autoplay )
			{
				$content .= '<script type="text/javascript"><!--'.PHP_EOL;
				$content .= "powerpress_embed_swf('powerpress_player_{$player_id}','{$media_url}',{$player_width},{$player_height});\n";
				$content .= "//-->\n";
				$content .= "</script>\n";
			}
			
		}; break;
		
		case 'pdf': {
			$content .= powerpressplayer_build_playimagepdf($media_url, true);
		}; break;
		case 'epub': {
			$content .= powerpressplayer_build_playimageepub($media_url, true);
		}; break;
			
		// Default, just display the play image. 
		default: {
			
			$content .= powerpressplayer_build_playimage($media_url, $EpisodeData, true);
			
		}; break;
	}
	
	return $content;
}

add_filter('powerpress_player', 'powerpressplayer_player_audio', 10, 3); // Audio players (mp3)
add_filter('powerpress_player', 'powerpressplayer_player_video', 10, 3); // Video players (mp4/m4v, webm, ogv)
add_filter('powerpress_player', 'powerpressplayer_player_other', 10, 3); // Audio/Video flv, wmv, wma, oga, m4a and other non-standard media files

/*
Filters for media links, appear below the selected player
*/
function powerpressplayer_link_download($content, $media_url, $ExtraData = array() )
{
	$GeneralSettings = get_option('powerpress_general');
	if( !isset($GeneralSettings['podcast_link']) )
		$GeneralSettings['podcast_link'] = 1;
	
	$player_links = '';
	if( $GeneralSettings['podcast_link'] == 1 )
	{
		$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_d\" title=\"". POWERPRESS_DOWNLOAD_TEXT ."\" rel=\"nofollow\">". POWERPRESS_DOWNLOAD_TEXT ."</a>".PHP_EOL;
	}
	else if( $GeneralSettings['podcast_link'] == 2 )
	{
		$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_d\" title=\"". POWERPRESS_DOWNLOAD_TEXT ."\" rel=\"nofollow\">". POWERPRESS_DOWNLOAD_TEXT ."</a> (".powerpress_byte_size($ExtraData['size']).") ".PHP_EOL;
	}
	else if( $GeneralSettings['podcast_link'] == 3 )
	{
		if( $ExtraData['duration'] && ltrim($ExtraData['duration'], '0:') != '' )
			$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_d\" title=\"". POWERPRESS_DOWNLOAD_TEXT ."\" rel=\"nofollow\">". POWERPRESS_DOWNLOAD_TEXT ."</a> (". htmlspecialchars(POWERPRESS_DURATION_TEXT) .": " . powerpress_readable_duration($ExtraData['duration']) ." &#8212; ".powerpress_byte_size($ExtraData['size']).")".PHP_EOL;
		else
			$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_d\" title=\"". POWERPRESS_DOWNLOAD_TEXT ."\" rel=\"nofollow\">". POWERPRESS_DOWNLOAD_TEXT ."</a> (".powerpress_byte_size($ExtraData['size']).")".PHP_EOL;
	}
	
	if( $player_links && !empty($content) )
		$content .= ' '.POWERPRESS_LINK_SEPARATOR .' ';
	
	return $content . $player_links;
}

function powerpressplayer_link_pinw($content, $media_url, $ExtraData = array() )
{
	$GeneralSettings = get_option('powerpress_general');
	if( !isset($GeneralSettings['player_function']) )
		$GeneralSettings['player_function'] = 1;
	$is_pdf = (strtolower( substr($media_url, -3) ) == 'pdf' );
	
	$player_links = '';
	switch( $GeneralSettings['player_function'] )
	{
		case 1: // Play on page and new window
		case 3: // Play in new window only
		case 5: { // Play in page and new window
			if( $is_pdf )
				$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_pinw\" target=\"_blank\" title=\"". __('Open in New Window', 'powerpress') ."\" rel=\"nofollow\">". __('Open in New Window', 'powerpress') ."</a>".PHP_EOL;
			else if( !empty($ExtraData['id']) && !empty($ExtraData['feed']) )
				$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_pinw\" target=\"_blank\" title=\"". POWERPRESS_PLAY_IN_NEW_WINDOW_TEXT ."\" onclick=\"return powerpress_pinw('{$ExtraData['id']}-{$ExtraData['feed']}');\" rel=\"nofollow\">". POWERPRESS_PLAY_IN_NEW_WINDOW_TEXT ."</a>".PHP_EOL;
			else
				$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_pinw\" target=\"_blank\" title=\"". POWERPRESS_PLAY_IN_NEW_WINDOW_TEXT ."\" rel=\"nofollow\">". POWERPRESS_PLAY_IN_NEW_WINDOW_TEXT ."</a>".PHP_EOL;
		}; break;
	}//end switch	
	
	if( $player_links && !empty($content) )
		$content .= ' '.POWERPRESS_LINK_SEPARATOR .' ';
	
	return $content . $player_links;
}

function powerpressplayer_embedable($media_url, $ExtraData = array())
{
	if( empty($ExtraData['id']) || empty($ExtraData['feed']) )
		return false;
	
	$extension = powerpressplayer_get_extension($media_url);
	$player = false;
	if( preg_match('/(mp3|m4a|mp4|m4v|webm|ogg|ogv)/i', $extension ) )
	{
		$GeneralSettings = get_option('powerpress_general');
		if( empty($GeneralSettings['podcast_embed']) )
			return false;
		if( !isset($GeneralSettings['player']) )
			$GeneralSettings['player'] = 'default';
		if( !isset($GeneralSettings['video_player']) )
			$GeneralSettings['video_player'] = 'flow-player-classic';
		
		switch( $extension )
		{
			case 'mp3':
			case 'm4a': {
				//if( $GeneralSettings['player'] == 'default' )
				//	$player = $GeneralSettings['player'];
				$player = 'default';
			}; break;
			case 'mp4':
			case 'm4v': {
				//if( $GeneralSettings['video_player'] == 'flow-player-classic' || $GeneralSettings['video_player'] == 'html5video' )
				//	$player = $GeneralSettings['video_player'];
				$player = 'html5video';
			}; break;
			case 'webm':
			case 'ogg':
			case 'ogv': {
				//if( $GeneralSettings['video_player'] == 'html5video' )
				//	$player = $GeneralSettings['video_player'];
				$player = 'html5video';
			}; break;
		}
	}
	
	return $player;
}

function powerpressplayer_link_embed($content, $media_url, $ExtraData = array() )
{
	$player_links = '';
	
	$player = powerpressplayer_embedable($media_url, $ExtraData);
	if( $player )
	{
		$player_links .= "<a href=\"#\" class=\"powerpress_link_e\" title=\"". htmlspecialchars(POWERPRESS_EMBED_TEXT) ."\" onclick=\"return powerpress_show_embed('{$ExtraData['id']}-{$ExtraData['feed']}');\" rel=\"nofollow\">". htmlspecialchars(POWERPRESS_EMBED_TEXT) ."</a>";
	}
	
	if( $player_links && !empty($content) )
		$content .= ' '.POWERPRESS_LINK_SEPARATOR .' ';
	return $content . $player_links;
}

function powerpressplayer_link_title($content, $media_url, $ExtraData = array() )
{
	if( $content )
	{
		$extension = 'unknown';
		$parts = pathinfo($media_url);
		if( $parts && isset($parts['extension']) )
			$extension  = strtolower($parts['extension']);
		
		$prefix = '';
		if( $extension == 'pdf' )
			$prefix .= __('E-Book PDF', 'powerpress') . ( $ExtraData['feed']=='pdf'||$ExtraData['feed']=='podcast'?'':" ({$ExtraData['feed']})") .POWERPRESS_TEXT_SEPARATOR;
		else if( $ExtraData['feed'] != 'podcast' )
			$prefix .= htmlspecialchars(POWERPRESS_LINKS_TEXT) .' ('. htmlspecialchars($ExtraData['feed']) .')'. POWERPRESS_TEXT_SEPARATOR;
		else
			$prefix .= htmlspecialchars(POWERPRESS_LINKS_TEXT) . POWERPRESS_TEXT_SEPARATOR;
		if( !empty($prefix) )
			$prefix .= ' ';
		
		$return = '<p class="powerpress_links powerpress_links_'. $extension .'">'. $prefix . $content . '</p>'.PHP_EOL;
		$player = powerpressplayer_embedable($media_url, $ExtraData);
		if( $player )
		{
			$iframe_src = powerpress_generate_embed($player, $ExtraData);
			$return .= '<p class="powerpress_embed_box" id="powerpress_embed_'. "{$ExtraData['id']}-{$ExtraData['feed']}" .'" style="display: none;">';
			$return .= '<input id="powerpress_embed_'. "{$ExtraData['id']}-{$ExtraData['feed']}" .'_t" type="text" value="'. htmlspecialchars($iframe_src) .'" onclick="javascript: this.select();" onfocus="javascript: this.select();" style="width: 70%;" readOnly>';
			$return .= '</p>';
		}
		return $return;
	}
	return '';
}

add_filter('powerpress_player_links', 'powerpressplayer_link_pinw', 30, 3);
add_filter('powerpress_player_links', 'powerpressplayer_link_download', 50, 3);
add_filter('powerpress_player_links', 'powerpressplayer_link_embed', 50, 3);
add_filter('powerpress_player_links', 'powerpressplayer_link_title', 1000, 3);

/*
Do Play in new Window
*/
function powerpress_do_pinw($pinw, $process_podpress)
{
	if( !WP_DEBUG && defined('POWERPRESS_FIX_WARNINGS') )
	{
		@error_reporting( E_ALL | E_CORE_ERROR | E_COMPILE_ERROR  | E_PARSE );
	}
	
	list($post_id, $feed_slug) = explode('-', $pinw, 2);
	$EpisodeData = powerpress_get_enclosure_data($post_id, $feed_slug);
	
	if( $EpisodeData == false && $process_podpress && $feed_slug == 'podcast' )
	{
		$EpisodeData = powerpress_get_enclosure_data_podpress($post_id);
	}
	
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo __('Blubrry PowerPress Player', 'powerpress'); ?></title>
	<meta name="robots" content="noindex" />
<?php 

	if( defined('POWERPRESS_ENQUEUE_SCRIPTS') )
		wp_enqueue_script( 'powerpress-player', powerpress_get_root_url() .'player.js');
	
	wp_head();
?>
<style type="text/css">
body { font-size: 13px; font-family: Arial, Helvetica, sans-serif; }
</style>
</head>
<body>
<div style="margin: 5px;">
<?php
	$GeneralSettings = get_option('powerpress_general');
	if( !$EpisodeData )
	{
		echo '<p>'.  __('Unable to retrieve media information.', 'powerpress') .'</p>';
	}
	else if( !empty($GeneralSettings['premium_caps']) && !powerpress_premium_content_authorized($feed_slug) )
	{
		echo powerpress_premium_content_message($post_id, $feed_slug, $EpisodeData);
	}
	else if( !empty($EpisodeData['embed']) )
	{
		echo $EpisodeData['embed'];
	}
	else //  if( !isset($EpisodeData['no_player']) ) // Even if there is no player set, if the play in new window option is enabled then it should play here...
	{
		echo apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), array('feed'=>$feed_slug, 'autoplay'=>true, 'type'=>$EpisodeData['type']) );
	}
	
?>
</div>
</body>
</html>
<?php
	exit;
}

/*
Do embed
*/
function powerpress_do_embed($player, $embed, $process_podpress)
{
	list($post_id, $feed_slug) = explode('-', $embed, 2);
	$EpisodeData = powerpress_get_enclosure_data($post_id, $feed_slug);
	
	if( $EpisodeData == false && $process_podpress && $feed_slug == 'podcast' )
	{
		$EpisodeData = powerpress_get_enclosure_data_podpress($post_id);
	}
	
	// Embeds are only available for the following players
	echo powerpressplayer_in_embed($player, $EpisodeData['url'], $EpisodeData);
	exit;
}

/*
HTTML 5 Video Player
*/
function powerpressplayer_build_html5video($media_url, $EpisodeData=array(), $embed = false )
{
	$player_id = powerpressplayer_get_next_id();
	$cover_image = '';
	$player_width = 400;
	$player_height = 225;
	$autoplay = false;
	// Global Settings
	$Settings = get_option('powerpress_general');
	if( !empty($Settings['player_width']) )
		$player_width = $Settings['player_width'];
	if( !empty($Settings['player_height']) )
		$player_height = $Settings['player_height'];
	if( !empty($Settings['poster_image']) )
		$cover_image = $Settings['poster_image'];
	// Episode Settings
	if( !empty($EpisodeData['image']) )
		$cover_image = $EpisodeData['image'];
	if( !empty($EpisodeData['width']) )
		$player_width = $EpisodeData['width'];
	if( !empty($EpisodeData['height']) )
		$player_height = $EpisodeData['height'];
	if( !empty($EpisodeData['autoplay']) )
		$autoplay = true;
	
	$content = '';
	if( $embed )
	{
		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">'.PHP_EOL;
		$content .= '<video width="'. $player_width .'" height="'. $player_height .'" controls="controls"';
		if( $cover_image )
			$content .= ' poster="'. $cover_image .'"';
		if( $autoplay )
			$content .= ' autoplay="autoplay"';
		else
			$content .= ' preload="none"';
		$content .= '>'.PHP_EOL;
		$content_type = powerpress_get_contenttype($media_url);
		$content .='<source src="'. $media_url .'" type="'. $content_type .'" />';
		
		if( !empty($EpisodeData['webm_src']) )
		{
			$EpisodeData['webm_src'] = powerpress_add_flag_to_redirect_url($EpisodeData['webm_src'], 'p');
			$content .='<source src="'. $EpisodeData['webm_src'] .'" type="video/webm" />';
		}
		
		$content .= powerpressplayer_build_playimage($media_url, $EpisodeData);
		$content .= '</video>'.PHP_EOL;
		$content .= '</div>'.PHP_EOL;
	}
	else
	{
		
		if( !$cover_image )
			$cover_image = powerpress_get_root_url() . 'black.png';
		$webm_src = '';
		if( !empty($EpisodeData['webm_src']) )
			$webm_src = powerpress_add_flag_to_redirect_url($EpisodeData['webm_src'], 'p');
		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
		$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="return powerpress_embed_html5v(\''.$player_id.'\',\''.$media_url.'\',\''. $player_width .'\',\''. $player_height .'\', \''. $webm_src .'\');" target="_blank" style="position: relative;">';
		if( !empty($EpisodeData['custom_play_button']) ) // This currently does not apply
		{
			$cover_image = $EpisodeData['custom_play_button'];
			$Settings['poster_play_image'] = false;
			$content .= '<img class="powerpress-player-poster" src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" />';
		}
		else
		{
			$content .= '<img class="powerpress-player-poster" src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="width: '. $player_width .'px; height: '. $player_height .'px;" />';
		}
		
		if(!isset($Settings['poster_play_image']) || $Settings['poster_play_image'] )
		{
			$play_image_button_url = powerpress_get_root_url() .'play_video.png';
			if( !empty($Settings['video_custom_play_button']) )
				$play_image_button_url = $Settings['video_custom_play_button'];
			
			$bottom = floor(($player_height/2)-30);
			if( $bottom < 0 )
				$bottom = 0;
			$left = floor(($player_width/2)-30);
			if( $left < 0 )
				$left = 0;
			$content .= '<img class="powerpress-player-play-image" src="'. $play_image_button_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="position: absolute; bottom: '. $bottom .'px; left: '. $left .'px; border:0;" />';
		}
		$content .= '</a>';
		$content .= "</div>\n";
		
		if( $autoplay )
		{
			$content .= '<script type="text/javascript"><!--'.PHP_EOL;
			$content .= "powerpress_embed_html5v('{$player_id}','{$media_url}',{$player_width},{$player_height},'{$webm_src}');\n";
			$content .= "//-->\n";
			$content .= "</script>\n";
		}
	}
	return $content;
}

/*
HTTML 5 Audio Player
*/
function powerpressplayer_build_html5audio($media_url, $EpisodeData=array(), $embed = false )
{
	$player_id = powerpressplayer_get_next_id();
	$autoplay = false;
	// Episode Settings
	if( !empty($EpisodeData['autoplay']) )
		$autoplay = true;
	$content = '';
	if( $embed )
	{
		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">'.PHP_EOL;
		$content .= '<audio controls="controls"';
		$content .=' src="'. $media_url .'"';
		if( $cover_image )
			$content .= ' poster="'. $cover_image .'"';
		if( $autoplay )
			$content .= ' autoplay="autoplay"';
		else
			$content .= ' preload="none"';
		$content .= '>'.PHP_EOL;
		
		$content .= powerpressplayer_build_playimageaudio($media_url);
		$content .= '</audio>'.PHP_EOL;
		$content .= '</div>'.PHP_EOL;
	}
	else
	{
		$GeneralSettings = get_option('powerpress_general');
		$cover_image = powerpress_get_root_url() . 'play_audio.png';
		if( !empty($EpisodeData['custom_play_button']) )
		{
			$cover_image = $EpisodeData['custom_play_button'];
		}
		else if( !empty($GeneralSettings['audio_custom_play_button']) )
		{
			$cover_image = $GeneralSettings['audio_custom_play_button'];
		}
		
		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
		$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="return powerpress_embed_html5a(\''.$player_id.'\',\''.$media_url.'\');" target="_blank">';
		$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="border:0;" />';
		$content .= '</a>';
		$content .= "</div>\n";
		
		if( $autoplay )
		{
			$content .= '<script type="text/javascript"><!--'.PHP_EOL;
			$content .= "powerpress_embed_html5a('{$player_id}','{$media_url}');\n";
			$content .= "//-->\n";
			$content .= "</script>\n";
		}
	}
	
	return $content;
}

/*
HTTML 5 Mobile Player
*/
function powerpressplayer_build_html5mobile($media_url, $EpisodeData)
{
	$content = '';
	$html5 = true;
	// Special logic, we need to check if we're dealing with Android 2.2 or older, in which case we don't want to use HTML5 audio/video due to controls bug
	if( preg_match('/android ([\d\.]+)/i', $_SERVER['HTTP_USER_AGENT'], $matches) )
	{
		if( !empty($matches[1]) && version_compare($matches[1], "2.3") < 0 )
			$html5 = false;
	}
	
	$extension = powerpressplayer_get_extension($media_url);
	switch( $extension )
	{
		case 'mp4':
		case 'webm':
		case 'm4v':
		case 'ogg':
		case 'ogv': {
			// Video
			if( $html5 )
				$content .= powerpressplayer_build_html5video($media_url, $EpisodeData, true);
			else
				$content .= powerpressplayer_build_playimage($media_url, $EpisodeData, true);
		}; break;
		case 'mp3':
		case 'm4a':
		case 'oga': {
			// Audio
			if( $html5 )
				$content .= powerpressplayer_build_html5audio($media_url, $EpisodeData, true);
			else
				$content .= powerpressplayer_build_playimageaudio($media_url, true);
		}; break;
	}
	
	return $content;
}

/*
Flow Player Classic
*/
function powerpressplayer_build_flowplayerclassic($media_url, $EpisodeData = array())
{
	// Very important setting, we need to know if the media should auto play or not...
	$autoplay = false; // (default)
	if( !empty($EpisodeData['autoplay']) )
		$autoplay = true;
	$cover_image = '';
	$player_width = 400;
	$player_height = 225;
	$Settings = get_option('powerpress_general');
	// Global Settings
	if( !empty($Settings['player_width']) )
		$player_width = $Settings['player_width'];
	if( !empty($Settings['player_height']) )
		$player_height = $Settings['player_height'];
	if( !empty($Settings['poster_image']) )
		$cover_image = $Settings['poster_image'];
	// Episode Settings
	if( !empty($EpisodeData['width']) )
		$player_width = $EpisodeData['width'];
	if( !empty($EpisodeData['height']) )
		$player_height = $EpisodeData['height'];
	if( !empty($EpisodeData['image']) )
		$cover_image = $EpisodeData['image'];
		
	$extension = powerpressplayer_get_extension($media_url, $EpisodeData);
	if( ($extension == 'mp3' || $extension == 'm4a') && empty($Settings['poster_image_audio']) )
	{
		// FlowPlayer has differeent sizes for audio than for video
		$player_width = 320;
		if( !empty($Settings['player_width_audio']) )
			$player_width = $Settings['player_width_audio'];
		
		if( !empty($EpisodeData['width']) && !empty($Settings['player_width_audio']) )
			$player_width = $EpisodeData['width'];
		
		$cover_image = ''; // Audio should not have a cover image
		$player_height = 24;
	}
	
	// Build player...
	$player_id = powerpressplayer_get_next_id();
	$content = '';
	$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'"></div>'.PHP_EOL;
	$content .= '<script type="text/javascript"><!--'.PHP_EOL;
	if( !empty($EpisodeData['jquery_autowidth']) )
	{
		$player_width = 'jQuery(window).width()';
		if( preg_match('/(mp4|m4v|ogg|ogv|webm)/i', $extension) )
		{
			$player_height = 'jQuery(window).height()';
		}
	}
	
	if( empty($EpisodeData['type']) )
	{
		$EpisodeData['type']  = powerpress_get_contenttype('test.'.$extension);
	}
	
	$content .= "pp_flashembed(\n";
	$content .= "	'powerpress_player_{$player_id}',\n";
	
	$content .= "	{src: '". powerpress_get_root_url() ."FlowPlayerClassic.swf', ";
	if( preg_match('/^jQuery\(/', $player_width) ) // Only add single quotes if jQuery( ... is not in the value
		$content .= "width: {$player_width}, ";
	else
		$content .= "width: '{$player_width}', ";
	if( preg_match('/^jQuery\(/', $player_height) ) // Only add single quotes if jQuery( ... is not in the value
		$content .= "height: {$player_height}, ";
	else
		$content .= "height: '{$player_height}', ";
	$content .= "wmode: 'transparent' },\n";
	if( $cover_image )
		$content .= "	{config: { autoPlay: ". ($autoplay?'true':'false') .", autoBuffering: false, showFullScreenButton: ". (preg_match('/audio\//', $EpisodeData['type'])?'false':'true' ) .", showMenu: false, videoFile: '{$media_url}', splashImageFile: '{$cover_image}', scaleSplash: true, loop: false, autoRewind: true } }\n";
	else
		$content .= "	{config: { autoPlay: ". ($autoplay?'true':'false') .", autoBuffering: false, showFullScreenButton: ". (preg_match('/audio\//', $EpisodeData['type'])?'false':'true' ) .", showMenu: false, videoFile: '{$media_url}', loop: false, autoRewind: true } }\n";
	$content .= ");\n";
	$content .= "//-->\n";
	$content .= "</script>\n";
	return $content;
}


function powerpressplayer_build_playimage($media_url, $EpisodeData = array(), $include_div = false)
{
	$content = '';
	$autoplay = false;
	if( !empty($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$autoplay = true;
	$player_width = 400;
	$player_height = 225;
	$cover_image = '';
	// Global settings
	$Settings = get_option('powerpress_general');
	if( !empty($Settings['player_width']) )
		$player_width = $Settings['player_width'];
	if( !empty($Settings['player_height']) )
		$player_height = $Settings['player_height'];
	if( !empty($Settings['poster_image']) )
		$cover_image = $Settings['poster_image'];
	// episode settings
	if( !empty($EpisodeData['width']) )
		$player_width = $EpisodeData['width'];
	if( !empty($EpisodeData['height']) )
		$player_height = $EpisodeData['height'];
	if( !empty($EpisodeData['image']) )
		$cover_image = $EpisodeData['image'];
		
	if( !$cover_image )
		$cover_image = powerpress_get_root_url() . 'black.png';
	
	if( $include_div )
		$content .= '<div class="powerpress_player" id="powerpress_player_'. powerpressplayer_get_next_id() .'">';
	$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" target="_blank" style="position: relative;">';
	$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="width: '. $player_width .'px; height: '. $player_height .'px;" />';
	if(!isset($Settings['poster_play_image']) || $Settings['poster_play_image'] )
	{
		$play_image_button_url = powerpress_get_root_url() .'play_video.png';
		if( !empty($Settings['video_custom_play_button']) )
			$play_image_button_url = $Settings['video_custom_play_button'];
			
		$bottom = floor(($player_height/2)-30);
		if( $bottom < 0 )
			$bottom = 0;
		$left = floor(($player_width/2)-30);
		if( $left < 0 )
			$left = 0;
		$content .= '<img src="'. $play_image_button_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="position: absolute; bottom:'. $bottom .'px; left:'. $left .'px; border:0;" />';
	}
	$content .= '</a>';
	if( $include_div )
		$content .= "</div>\n";
	return $content;
}

function powerpressplayer_build_playimageaudio($media_url, $include_div = false)
{
	$content = '';
	$cover_image = powerpress_get_root_url() . 'play_audio.png';
	$GeneralSettings = get_option('powerpress_general');
	if( !empty($GeneralSettings['custom_play_button']) )
		$cover_image = $GeneralSettings['custom_play_button'];
		
	if( $include_div )
		$content .= '<div class="powerpress_player" id="powerpress_player_'. powerpressplayer_get_next_id() .'">';
	$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" target="_blank">';
	$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="border:0;" />';
	$content .= '</a>';
	if( $include_div )
		$content .= "</div>\n";
	return $content;
}

function powerpressplayer_build_playimagepdf($media_url, $include_div = false)
{
	$content = '';
	$cover_image = powerpress_get_root_url() . 'play_pdf.png';
	$GeneralSettings = get_option('powerpress_general');
	if( !empty($GeneralSettings['pdf_custom_play_button']) )
		$cover_image = $GeneralSettings['pdf_custom_play_button'];
		
	if( $include_div )
		$content .= '<div class="powerpress_player" id="powerpress_player_'. powerpressplayer_get_next_id() .'">';
	$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" target="_blank">';
	$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" style="border:0;" />';
	$content .= '</a>';
	if( $include_div )
		$content .= "</div>\n";
	return $content;
}

function powerpressplayer_build_playimageepub($media_url, $include_div = false)
{
	$content = '';
	$cover_image = powerpress_get_root_url() . 'play_epub.png';
	$GeneralSettings = get_option('powerpress_general');
	if( !empty($GeneralSettings['epub_custom_play_button']) )
		$cover_image = $GeneralSettings['epub_custom_play_button'];
		
	if( $include_div )
		$content .= '<div class="powerpress_player" id="powerpress_player_'. powerpressplayer_get_next_id() .'">';
	$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" target="_blank">';
	$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" style="border:0;" />';
	$content .= '</a>';
	if( $include_div )
		$content .= "</div>\n";
	return $content;
}

/*
1 pixel out player
*/
function powerpressplayer_build_1pxoutplayer($media_url, $EpisodeData = array())
{
	$content = '';
	$autoplay = false;
	if( isset($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$autoplay = true; // TODO: We need to handle this

	$PlayerSettings = get_option('powerpress_audio-player');
	if( !$PlayerSettings )
	{
		$PlayerSettings = array(
			'width'=>'290',
			'transparentpagebg' => 'yes',
			'lefticon' => '#333333',
			'leftbg' => '#CCCCCC',
			'bg' => '#E5E5E5',
			'voltrack' => '#F2F2F2',
			'volslider' => '#666666',
			'rightbg' => '#B4B4B4',
			'rightbghover' => '#999999',
			'righticon' => '#333333',
			'righticonhover' => '#FFFFFF',
			'loader' => '#009900',
			'track' => '#FFFFFF',
			'tracker' => '#DDDDDD',
			'border' => '#CCCCCC',
			'skip' => '#666666',
			'text' => '#333333',
			'pagebg' => '',
			'noinfo'=>'yes',
			'rtl' => 'no' );
	}

	if( empty($PlayerSettings['titles']) )
		$PlayerSettings['titles'] = 'Blubrry PowerPress';
	else if( strtoupper($PlayerSettings['titles']) == __('TRACK', 'powerpress') )
		unset( $PlayerSettings['titles'] );

	// Set player width
	if( !isset($PlayerSettings['width']) )	
		$PlayerSettings['width'] = 290;
	if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
		$PlayerSettings['width'] = $EpisdoeData['width'];
	
	$transparency = '<param name="wmode" value="transparent" />';
	$PlayerSettings['transparentpagebg'] = 'yes';
	if( !empty($PlayerSettings['pagebg']) )
	{
		$transparency = '<param name="bgcolor" value="'.$PlayerSettings['pagebg'].'" />';
		$PlayerSettings['transparentpagebg'] = 'no';
	}
	
	$flashvars ='';
	while( list($key,$value) = each($PlayerSettings) )
	{
		$flashvars .= '&amp;'. $key .'='. preg_replace('/\#/','',$value);
	}
	
	if( $autoplay )
	{
		$flashvars .= '&amp;autostart=yes';
	}
	
	// TODO: Add 1 px out audio-player player here
	$player_id = powerpressplayer_get_next_id();
	if( empty($EpisodeData['nodiv']) )
		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
	$content .= '<object type="application/x-shockwave-flash" data="'.powerpress_get_root_url().'audio-player.swf" id="'.$player_id.'" height="24" width="'. $PlayerSettings['width'] .'">'.PHP_EOL;
	$content .= '<param name="movie" value="'.powerpress_get_root_url().'audio-player.swf" />'.PHP_EOL;
	$content .= '<param name="FlashVars" value="playerID='.$player_id.'&amp;soundFile='.urlencode($media_url).$flashvars.'" />'.PHP_EOL;
	$content .= '<param name="quality" value="high" />'.PHP_EOL;
	$content .= '<param name="menu" value="false" />'.PHP_EOL;
	$content .= '<param name="wmode" value="transparent" />'.PHP_EOL;
	// $content .= powerpressplayer_build_html5audio($media_url, $EpisodeData, true); // Feature removed since it causes double players to be insrted in Safari/Firefox
	$content .=  powerpressplayer_build_playimageaudio($media_url);
	$content .= '</object>'.PHP_EOL;
	if( empty($EpisodeData['nodiv']) )
		$content .= '</div>'.PHP_EOL;
	
	return $content;
}

/*
Flash Mp3 player Maxi
*/
function powerpressplayer_build_flashmp3maxi($media_url, $EpisodeData = array())
{
	$autoplay = false;
	if( isset($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$autoplay = true; // TODO: We need to handle this
		
	$PlayerSettings = get_option('powerpress_flashmp3-maxi');
	$keys = array('bgcolor1','bgcolor2','bgcolor','textcolor','buttoncolor','buttonovercolor','showstop','showinfo','showvolume','height','width','showloading','buttonwidth','volume','showslider');
		
		//set PlayerSettings as blank array for initial setup
				//This keeps the foreach loop from returning an error
	if( empty($PlayerSettings) )
	{
		$PlayerSettings = array(
			'bgcolor1'=>'#7c7c7c',
			'bgcolor2'=>'#333333',
			'textcolor' => '#FFFFFF',
			'buttoncolor' => '#FFFFFF',
			'buttonovercolor' => '#FFFF00',
			'showstop' => '0',
			'showinfo' => '0',
			'showvolume' => '1',
			'height' => '20',
			'width' => '200',
			'showloading' => 'autohide',
			'buttonwidth' => '26',
			'volume' => '100',
			'showslider' => '1',
			'slidercolor1'=>'#cccccc',
			'slidercolor2'=>'#888888',
			'sliderheight' => '10',
			'sliderwidth' => '20',
			'loadingcolor' => '#FFFF00', 
			'volumeheight' => '6',
			'volumewidth' => '30',
			'sliderovercolor' => '#eeee00');
	}

	$flashvars = '';
	$flashvars .= "mp3=" . urlencode($media_url);
	if( $autoplay ) 
		$flashvars .= '&amp;autoplay=1';

	//set non-blank options without dependencies as flash variables for preview
	foreach($keys as $key)
	{
		if( !empty($PlayerSettings[$key]) )
		{
			$flashvars .= '&amp;'. $key .'='. preg_replace('/\#/','',$PlayerSettings[''.$key.'']);
		}
	}
	
	//set slider dependencies
	if( !empty($PlayerSettings['showslider']) ) // IF not zero
	{
		if( !empty($PlayerSettings['sliderheight']) ) {
			$flashvars .= '&amp;sliderheight='. $PlayerSettings['sliderheight'];
		}
		if( !empty($PlayerSettings['sliderwidth']) ) {
			$flashvars .= '&amp;sliderwidth='. $PlayerSettings['sliderwidth'];
		}
		if( !empty($PlayerSettings['sliderovercolor']) ){
			$flashvars .= '&amp;sliderovercolor='. preg_replace('/\#/','',$PlayerSettings['sliderovercolor']);
		}
	}
	
	//set volume dependencies
	if($PlayerSettings['showvolume'] != "0")
	{
		if( !empty($PlayerSettings['volumeheight']) ) {
			$flashvars .= '&amp;volumeheight='. $PlayerSettings['volumeheight'];
		}
		if( !empty($PlayerSettings['volumewidth']) ) {
			$flashvars .= '&amp;volumewidth='. $PlayerSettings['volumewidth'];
		}
	}
	
	//set autoload dependencies
	if($PlayerSettings['showloading'] != "never")
	{
		if( !empty($PlayerSettings['loadingcolor']) ) {
			$flashvars .= '&amp;loadingcolor='. preg_replace('/\#/','',$PlayerSettings['loadingcolor']);
		}
	}

	//set default width for object
	if( empty($PlayerSettings['width']) )
		$width = '200';
	else
		$width = $PlayerSettings['width'];
	if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']))
		$width = $EpisodeData['width'];
	
	if( empty($PlayerSettings['height']) )
		$height = '20';
	else
		$height = $PlayerSettings['height'];
	if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) ) 
		$height = $EpisodeData['height'];
	
	//set background transparency
	if( !empty($PlayerSettings['bgcolor']) )
		$transparency = '<param name="bgcolor" value="'. $PlayerSettings['bgcolor'] .'" />';
	else
		$transparency = '<param name="wmode" value="transparent" />';
	
	// Add flashmp3-maxi player here
	$player_id = powerpressplayer_get_next_id();
	$content = '';
	$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">'.PHP_EOL;
	$content .= '<object type="application/x-shockwave-flash" data="'. powerpress_get_root_url().'player_mp3_maxi.swf" id="player_mp3_maxi_'.$player_id.'" width="'. $width.'" height="'. $height .'">'.PHP_EOL;
	$content .=  '<param name="movie" value="'. powerpress_get_root_url().'player_mp3_maxi.swf" />'.PHP_EOL;
	$content .= $transparency.PHP_EOL;
	$content .= '<param name="FlashVars" value="'. $flashvars .'" />'.PHP_EOL;
	// $content .= powerpressplayer_build_html5audio($media_url, $EpisodeData, true);  // Feature removed since it causes double players to be insrted in Safari/Firefox
	$content .=  powerpressplayer_build_playimageaudio($media_url);
	$content .= '</object>'.PHP_EOL;
	$content .= '</div>'.PHP_EOL;
	return $content;
}

/*
Audio Play player
*/
function powerpressplayer_build_audioplay($media_url, $EpisodeData = array())
{
	$autoplay = false;
	if( isset($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$autoplay = true;
			
	$PlayerSettings = get_option('powerpress_audioplay');
	if( empty($PlayerSettings) )
	{
		$PlayerSettings = array(
			'bgcolor' => '',
			'buttondir' => 'negative',
			'mode' => 'playpause');
	}

	$width = $height = (strstr($PlayerSettings['buttondir'], 'small')===false?30:15);

	// Set standard variables for player
	$flashvars = 'file='.urlencode($media_url) ;
	$flashvars .= '&amp;repeat=1';
	if( $autoplay )
		$flashvars .= '&amp;auto=yes';

	if( empty($PlayerSettings['bgcolor']) )
	{
		$flashvars .= "&amp;usebgcolor=no";
		$transparency = '<param name="wmode" value="transparent" />';
		$htmlbg = "";
	}
	else
	{
		$flashvars .= "&amp;bgcolor=". preg_replace('/\#/','0x',$PlayerSettings['bgcolor']);
		$transparency = '<param name="bgcolor" value="'. $PlayerSettings['bgcolor']. '" />';
		$htmlbg = 'bgcolor="'. $PlayerSettings['bgcolor'].'"';
	}

	if( empty($PlayerSettings['buttondir']) )
		$flashvars .= "&amp;buttondir=".powerpress_get_root_url()."buttons/negative";
	else
		$flashvars .= "&amp;buttondir=".powerpress_get_root_url().'buttons/'.$PlayerSettings['buttondir'];

	$flashvars .= '&amp;mode='. $PlayerSettings['mode'];
  
	// Add audioplay player here
	$player_id = powerpressplayer_get_next_id();
	$content = '';
	$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
	$content .= '<object type="application/x-shockwave-flash" width="'. $width .'" height="'. $height .'" id="audioplay_'.$player_id.'" data="'. powerpress_get_root_url().'audioplay.swf?'.$flashvars.'">'.PHP_EOL;
	$content .= '<param name="movie" value="'. powerpress_get_root_url().'audioplay.swf?'.$flashvars.'" />'.PHP_EOL;
	$content .= '<param name="quality" value="high" />'.PHP_EOL;
	$content .= $transparency.PHP_EOL;
	$content .= '<param name="FlashVars" value="'.$flashvars.'" />'.PHP_EOL;
	$content .= '<embed src="'. powerpress_get_root_url().'audioplay.swf?'.$flashvars.'" quality="high"  width="30" height="30" type="application/x-shockwave-flash">'.PHP_EOL;
	// $content .= powerpressplayer_build_html5audio($media_url, $EpisodeData, true);  // Feature removed since it causes double players to be insrted in Safari/Firefox
	$content .=  powerpressplayer_build_playimageaudio($media_url);
	$content .= "</embed>\n		</object>\n";
	$content .= "</div>\n";
	return $content;
}

/*
Simple Flash player
*/
function powerpressplayer_build_simpleflash($media_url, $EpisodeData = array())
{
	$autoplay = false;
	if( isset($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$autoplay = true; // TODO: We need to handle this
	
	$player_id = powerpressplayer_get_next_id();
	$content = '';
	$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
	$content .= '<object type="application/x-shockwave-flash" data="'. powerpress_get_root_url() .'simple_mp3.swf" id="simple_mp3_'.$player_id.'" width="150" height="50">';
	$content .= '<param name="movie" value="'. powerpress_get_root_url().'simple_mp3.swf" />';
	$content .= '<param name="wmode" value="transparent" />';
	$content .= '<param name="FlashVars" value="'. get_bloginfo('url') .'?url='. urlencode($media_url).'&amp;autostart='. ($autoplay?'true':'false') .'" />';
	$content .= '<param name="quality" value="high" />';
	$content .= '<embed wmode="transparent" src="'. get_bloginfo('url') .'?url='.urlencode($media_url).'&amp;autostart='. ($autoplay?'true':'false') .'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="150" height="50">';
	// $content .= powerpressplayer_build_html5audio($media_url, $EpisodeData, true);  // Feature removed since it causes double players to be insrted in Safari/Firefox
	$content .=  powerpressplayer_build_playimageaudio($media_url);
	$content .= '</embed>';
	$content .= '</object>';
	$content .= "</div>\n";
	return $content;
}

/*
VideoJS for PowerPress 4.0
*/
function powerpressplayer_build_videojs($media_url, $EpisodeData = array())
{
	$content = '';
	if( function_exists('add_videojs_header') )
	{
		// Global Settings
		$Settings = get_option('powerpress_general');
		
		$player_id = powerpressplayer_get_next_id();
		$cover_image = '';
		$player_width = 400;
		$player_height = 225;
		$autoplay = false;
		
		if( !empty($Settings['player_width']) )
			$player_width = $Settings['player_width'];
		if( !empty($Settings['player_height']) )
			$player_height = $Settings['player_height'];
		if( !empty($Settings['poster_image']) )
			$cover_image = $Settings['poster_image'];
		
		// Episode Settings
		if( !empty($EpisodeData['image']) )
			$cover_image = $EpisodeData['image'];
		if( !empty($EpisodeData['width']) )
			$player_width = $EpisodeData['width'];
		if( !empty($EpisodeData['height']) )
			$player_height = $EpisodeData['height'];
		if( !empty($EpisodeData['autoplay']) )
			$autoplay = true;

		// Poster image supplied
		$poster_attribute = '';
		if ($cover_image)
			$poster_attribute = ' poster="'.$cover_image.'"';

		// Autoplay the video?
		$autoplay_attribute = '';
		if ( $autoplay )
			$autoplay_attribute = ' autoplay';
			
		// We never do pre-poading for podcasting as it inflates statistics
		
		// Is there a custom class?
		$class = '';
		if ( !empty($Settings['videojs_css_class']) )
			$class = ' '. $Settings['videojs_css_class'];

		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';

		$content .= '<video id="videojs_player_'. $player_id .'" class="video-js vjs-default-skin'. $class .'" width="'. $player_width .'" height="'. $player_height .'"'. $poster_attribute .' controls '. $autoplay_attribute .' data-setup="{}">';
		
		$content_type = powerpress_get_contenttype($media_url);
		if( $content_type == 'video/x-m4v' )
			$content_type = 'video/mp4'; // Mp4
		$content .='<source src="'. $media_url .'" type="'. $content_type .'" />';
		
		if( !empty($EpisodeData['webm_src']) )
		{
			$EpisodeData['webm_src'] = powerpress_add_flag_to_redirect_url($EpisodeData['webm_src'], 'p');
			$content .='<source src="'. $EpisodeData['webm_src'] .'" type="video/webm; codecs="vp8, vorbis" />';
		}

		$content .= '</video>';
		$content .= "</div>\n";
	}
	else
	{
		$content .= powerpressplayer_build_html5video($media_url, $EpisodeData);
	}

	return $content;
}



?>