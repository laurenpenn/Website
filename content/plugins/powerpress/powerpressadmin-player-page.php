<?php
// PowerPress Player settings page

require_once( POWERPRESS_ABSPATH. '/powerpress-player.php'); // Include, if not included already

function powerpressplayer_flowplayer_info()
{
?>
	<p>
		<?php echo __('Flow Player Classic is an open source flash player that supports both audio (mp3 and m4a) and video (mp4, m4v and flv) media files. It includes all the necessary features for playback including a play/pause button, scrollable position bar, ellapsed time, total time, mute button and volume control.', 'powerpress'); ?>
	</p>
	
	<p>
		<?php echo __('Flow Player Classic was chosen as the default player in Blubrry PowerPress because if its backwards compatibility with older versions of Flash and support for both audio and video.', 'powerpress'); ?>
	</p>
<?php
}

function powerpressplayer_videojs_info()
{
	$plugin_link = '';
	
	if( !function_exists('add_videojs_header') && file_exists( WP_PLUGIN_DIR . '/' . 'videojs-html5-video-player-for-wordpress' ) ) // plugin downloaded but not activated...
	{
		$plugin_file = 'videojs-html5-video-player-for-wordpress' . '/' . 'video-js.php';
		$plugin_link = '<a href="' . esc_url(wp_nonce_url(admin_url('plugins.php?plugin_status=active&action=activate&plugin=' . $plugin_file ), 'activate-plugin_' . $plugin_file)) .
										'"title="' . esc_attr__('Activate Plugin') . '"">' . __('VideoJS - HTML5 Video Player for WordPress plugin', 'powerpress') . '</a>';
	
	
	} else {
		$plugin_link = '<a href="'. esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . 'videojs-html5-video-player-for-wordpress' .
									'&TB_iframe=true&width=600&height=550' ) ) .'" class="thickbox" title="' .
									esc_attr__('Install Plugin') . '">'. __('VideoJS - HTML5 Video Player for WordPress plugin', 'powerpress') . '</a>';
	}
?>
	<p>
		<?php echo __('VideoJS is a HTML5 JavaScript and CSS video player with fallback to Flash. ', 'powerpress'); ?>
	</p>
	
	<?php if( $plugin_link ) { ?>
	<p <?php echo ( function_exists('add_videojs_header') ?'':' style="background-color: #FFFFE0; border: 1px solid #E6DB55; padding: 8px 12px; line-height: 29px; font-weight: bold; font-size: 14px; display:inline;"'); ?>>
		<?php echo sprintf(__('The %s must be installed and activated in order to enable this feature.', 'powerpress'), $plugin_link ); ?>
	</p>
	<?php } ?>
<?php
}

function powerpress_admin_players($type='audio')
{
	$General = powerpress_get_settings('powerpress_general');
	
	if( empty($General['player']) )
		$General['player'] = 'default';
		
	if( empty($General['video_player']) )
		$General['video_player'] = '';
	if( empty($General['audio_custom_play_button']) )
		$General['audio_custom_play_button'] = '';
	
	$select_player = false;
	if( isset($_GET['sp']) )
		$select_player = true;
		
	if( $General['player'] == 'audio-player' )
		unset($General['player']); // Force the user to select a different player

	if( $type == 'video' )
	{
		if( !isset($General['video_player']) )
			$select_player = true;
	}
	else
	{
		if( !isset($General['player']) )
			$select_player = true;
	}
	
		
	$Audio = array();
	$Audio['default'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/FlowPlayerClassic.mp3';
	$Audio['audio-player'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/1_Pixel_Out_Flash_Player.mp3';
	$Audio['flashmp3-maxi'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/Flash_Maxi_Player.mp3';
	$Audio['simple_flash'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/Simple_Flash_MP3_Player.mp3';
	$Audio['audioplay'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/AudioPlay.mp3';
	$Audio['html5audio'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/html5.mp3';
		
	
	$Video = array();
	$Video['flare-player'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/FlarePlayer.mp4';
	$Video['flow-player-classic'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/flow.mp4';
	$Video['html5video'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/html5.mp4';
	$Video['videojs-html5-video-player-for-wordpress'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/videojs.mp4';
		/*
		<div><
		object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" width="30" height="30">
		<PARAM NAME=movie VALUE="http://www.strangecube.com/audioplay/online/audioplay.swf?file=http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/AudioPlay.mp3&auto=no&sendstop=yes&repeat=1&buttondir=http://www.strangecube.com/audioplay/online/alpha_buttons/negative&bgcolor=0xffffff&mode=playpause"><PARAM NAME=quality VALUE=high><PARAM NAME=wmode VALUE=transparent><embed src="http://www.strangecube.com/audioplay/online/audioplay.swf?file=http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/AudioPlay.mp3&auto=no&sendstop=yes&repeat=1&buttondir=http://www.strangecube.com/audioplay/online/alpha_buttons/negative&bgcolor=0xffffff&mode=playpause" quality=high wmode=transparent width="30" height="30" align="" TYPE="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></object></div><!-- End of generated code -->
		*/
		
		if( $type == 'video' && function_exists('add_videojs_header') )
			add_videojs_header();
?>
<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>3rdparty/colorpicker/css/colorpicker.css" type="text/css" />
<script type="text/javascript" src="<?php echo powerpress_get_root_url(); ?>3rdparty/colorpicker/js/colorpicker.js"></script>
<script type="text/javascript" src="<?php echo powerpress_get_root_url(); ?>player.js"></script>
<script type="text/javascript"><!--

powerpress_url = '<?php echo powerpress_get_root_url(); ?>';

function rgb2hex(rgb) {
 
 rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
 function hex(x) {
  hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
  return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
 }
 
 if( rgb )
	return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
 return '';
}

function UpdatePlayerPreview(name, value)
{
	if( typeof(generator) != "undefined" ) // Update the Maxi player...
	{
		generator.updateParam(name, value);
		generator.updatePlayer();
	}
	
	if( typeof(update_audio_player) != "undefined" ) // Update the 1 px out player...
		update_audio_player();
}
				
jQuery(document).ready(function($) {
	
	jQuery('.color_preview').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			jQuery(el).css({ 'background-color' : '#' + hex });
			jQuery(el).ColorPickerHide();
			var Id = jQuery(el).attr('id');
			Id = Id.replace(/_prev/, '');
			jQuery('#'+ Id  ).val( '#' + hex );
			UpdatePlayerPreview(Id, '#'+hex );
		},
		onBeforeShow: function () {
			jQuery(this).ColorPickerSetColor( rgb2hex( jQuery(this).css("background-color") ) );
		}
	})
	.bind('keyup', function(){
		jQuery(this).ColorPickerSetColor( rgb2hex( jQuery(this).css("background-color") ) );
	});
	
	jQuery('.color_field').bind('change', function () {
		var Id = jQuery(this).attr('id');
		jQuery('#'+ Id + '_prev'  ).css( { 'background-color' : jQuery(this).val() } );
		if( typeof(update_audio_player) != "undefined" ) // Update the 1 px out player...
			update_audio_player();
	});
	
	jQuery('.other_field').bind('change', function () {
		if( typeof(update_audio_player) != "undefined" ) // Update the 1 px out player...
			update_audio_player();
	});

});
//-->
</script>


<!-- special page styling goes here -->
<style type="text/css">
div.color_control { display: block; float:left; width: 100%; padding:  0; }
div.color_control input { display: inline; float: left; }
div.color_control div.color_picker { display: inline; float: left; margin-top: 3px; }
#player_preview { margin-bottom: 0px; height: 50px; margin-top: 8px;}
input#colorpicker-value-input {
	width: 60px;
	height: 16px;
	padding: 0;
	margin: 0;
	font-size: 12px;
	border-spacing: 0;
	border-width: 0;
}
table.html5formats {
	width: 600px;
	margin: 0;
	padding: 0;
}
table.html5formats tr {
	margin: 0;
	padding: 0;
}
table.html5formats tr th {
	font-weight: bold;
	border-bottom: 1px solid #000000;
	margin: 0;
	padding: 0 5px;
	width: 25%;
}
table.html5formats tr td {
	
	border-right: 1px solid #000000;
	border-bottom: 1px solid #000000;
	margin: 0;
	padding: 0 10px;
}
table.html5formats tr > td:first-child {
	border-left: 1px solid #000000;
}
</style>
<?php
	
	// mainly 2 pages, first page selects a player, second configures the player, if there are optiosn to configure for that player. If the user is on the second page,
	// a link should be provided to select a different player.
	if( $select_player )
	{
?>
<input type="hidden" name="action" value="powerpress-select-player" />
<h2><?php echo __('Blubrry PowerPress Player Options', 'powerpress'); ?></h2>
<p style="margin-bottom: 0;"><?php echo __('Select the media player you would like to use.', 'powerpress'); ?></p>

<?php
		if( $type == 'video' )
		{
?>
<table class="form-table">
<tr valign="top">
<th scope="row">&nbsp;</th>  
<td>
	<ul>
		<li><label><input type="radio" name="VideoPlayer[video_player]" id="player_flow_player_classic_player" value="flow-player-classic" <?php if( $General['video_player'] == 'flow-player-classic' ) echo 'checked'; ?> />
		<?php echo __('Flow Player Classic', 'powerpress'); ?></label>
			 <strong style="padding-top: 8px; margin-left: 20px;"><a href="#" id="activate_flow_player_classic_player" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
<?php
		echo powerpressplayer_build_flowplayerclassic( $Video['flow-player-classic'] );
?>
			</p>
<?php
	powerpressplayer_flowplayer_info();
?>
		</li>
		
		<li><label><input type="radio" name="VideoPlayer[video_player]" id="player_html5video" value="html5video" <?php if( $General['video_player'] == 'html5video' ) echo 'checked'; ?> /> <?php echo __('HTML5 Video Player', 'powerpress'); ?>  </label>
			<strong style="padding-top: 8px; margin-left: 20px;"><a href="#" id="activate_html5video" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
            <?php
						echo powerpressplayer_build_html5video($Video['html5video']);
					?>
			</p>
			<p>
				<?php echo __('HTML5 Video is an element introduced in the latest HTML specification (HTML5) for the purpose of playing videos.', 'powerpress'); ?>
			</p>
			<p>
				<?php echo __('HTML5 Video Player is not format specific. See table below for a list of browsers and supported formats.', 'powerpress'); ?>
			</p>
			<table class="html5formats" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<th><?php echo __('Browser', 'powerpress'); ?></th>
					<th>H.264 (.mp4/.m4v)</th>
					<th>WebM (.webm)</th>
					<th>Theora (.ogg/.ogv)</th>
				</tr>
				<tr>
					<td><i><?php echo __('Internet Explorer', 'powerpress'); ?></i></td>
					<td><strong>9.0+</strong></td>
					<td>-</td>
					<td>-</td>
				</tr>
				<tr>
					<td><i><?php echo __('Firefox', 'powerpress'); ?></i></td>
					<td>-</td>
					<td><strong>4.0+</strong></td>
					<td><strong>3.5+</strong></td>
				</tr>
				<tr>
					<td><i><?php echo __('Chrome', 'powerpress'); ?> <sup>1</sup></i></td>
					<td>-</td>
					<td><strong>6.0+</strong></td>
					<td><strong>5.0+</strong></td>
				</tr>
				<tr>
					<td><i><?php echo __('Opera', 'powerpress'); ?></i></td>
					<td>-</td>
					<td><strong>10.6+</strong></td>
					<td><strong>10.5+</strong></td>
				</tr>
				<tr>
					<td><i><?php echo __('Safari', 'powerpress'); ?> <sup>2</sup></i></td>
					<td><strong>3.0+</strong></td>
					<td>-</td>
					<td>-</td>
				</tr>
			</table>
			<div><sup>1</sup> <?php echo __('Chrome supported H.264 in previous versions, but no longer supports the format.', 'powerpress'); ?></div>
			<div><sup>2</sup> <?php echo __('Safari requires QuickTime installed for HTML5 playback.', 'powerpress'); ?></div>
			<p>
				<?php echo __('Flow Player Classic is used when HTML5 support is not available.', 'powerpress'); ?>
			</p>
		</li>
		
		<!-- videojs-html5-video-player-for-wordpress -->
		<li><label><input type="radio" name="VideoPlayer[video_player]" id="player_videojs_html5_video_player_for_wordpress" value="videojs-html5-video-player-for-wordpress" <?php if( $General['video_player'] == 'videojs-html5-video-player-for-wordpress' ) echo 'checked'; ?> <?php echo (function_exists('add_videojs_header')?'':'disabled');  ?> />
		<?php echo __('VideoJS', 'powerpress'); ?></label> <?php echo powerpressadmin_new(); ?>
		<?php if ( function_exists('add_videojs_header') ) { ?>
			 <strong style="padding-top: 8px; margin-left: 20px;"><a href="#" id="activate_videojs_html5_video_player_for_wordpress" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		<?php } ?>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
<?php
		if ( function_exists('add_videojs_header') ) {
			echo powerpressplayer_build_videojs( $Video['videojs-html5-video-player-for-wordpress'] );
		}
?>
			</p>
<?php
	powerpressplayer_videojs_info();
?>
		</li>
		
		
		
	</ul>

</td>
</tr>
</table>
<?php
		}
		else
		{
?>
<table class="form-table">
<tr valign="top">
<th scope="row">&nbsp;</th>  
<td>
	<ul>
		<li><label><input type="radio" name="Player[player]" id="player_default" value="default" <?php if( $General['player'] == 'default' ) echo 'checked'; ?> />
		<?php echo __('Flow Player Classic (default)', 'powerpress'); ?></label>
			 <strong style="padding-top: 8px; margin-left: 20px;"><a href="#" id="activate_default" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
<?php
			echo powerpressplayer_build_flowplayerclassic( $Audio['default'] );
?>
			</p>
			<?php powerpressplayer_flowplayer_info(); ?>
		</li>
		
		<li><label><input type="radio" name="Player[player]" id="player_audio_player" value="audio-player" <?php if( $General['player'] == 'audio-player' ) echo 'checked'; ?> disabled="disabled" /> <del><?php echo __('1 Pixel Out Audio Player', 'powerpress'); ?></del></label>
			<strong style="padding-top: 8px; margin-left: 20px;">(<?php echo __("Currently Not Available", 'powerpress'); ?>)</strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
				<?php  // echo powerpressplayer_build_1pxoutplayer( $Audio['audio-player'] ); ?>
			</p>
			<p><del>
				<?php echo __('1 Pixel Out Audio Player is a popular customizable audio (mp3 only) flash player. Features include an animated play/pause button, scrollable position bar, ellapsed/remaining time, volume control and color styling options.', 'powerpress'); ?>
			</del></p>
			<p>
			<strong>
			<?php echo __("Due to concerns of possible security exploits, the 1 Pixel Out Audio Player has been removed from PowerPress.", "powerpress"); ?> <br />
			<a href="http://blog.blubrry.com/?p=1163" target="_blank"><?php echo __("Learn More", "powerpress"); ?></a>
			</strong>
			</p>
		</li>
		
		<li><label><input type="radio" name="Player[player]" id="player_flashmp3_maxi" value="flashmp3-maxi" <?php if( $General['player'] == 'flashmp3-maxi' ) echo 'checked'; ?> /> <?php echo __('Mp3 Player Maxi', 'powerpress'); ?></label>
			<strong style="padding-top: 8px; margin-left: 20px;"><a href="#" id="activate_flashmp3_maxi" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
				<?php  echo powerpressplayer_build_flashmp3maxi( $Audio['flashmp3-maxi'] ); ?>
			</p>
			<p>
				<?php echo __('Flash Mp3 Maxi Player is a customizable open source audio (mp3 only) flash player. Features include pause/play/stop/file info buttons, scrollable position bar, volume control and color styling options.', 'powerpress'); ?>
			</p>
		</li>
		
		<li><label><input type="radio" name="Player[player]" id="player_simple_flash" value="simple_flash" <?php if( $General['player'] == 'simple_flash' ) echo 'checked'; ?> /> <?php echo __('Simple Flash MP3 Player', 'powerpress'); ?></label>
			<strong style="padding-top: 8px; margin-left: 20px;"><a href="#" id="activate_simple_flash" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
			<?php
			echo powerpressplayer_build_simpleflash($Audio['simple_flash']);
			?>
			</p>
			<p>
				<?php echo __('Simple Flash MP3 Player is a free and simple audio (mp3 only) flash player. Features include play/pause and stop buttons.', 'powerpress'); ?>
			</p>
		</li>
		
		<li><label><input type="radio" name="Player[player]" id="player_audioplay" value="audioplay" <?php if( $General['player'] == 'audioplay' ) echo 'checked'; ?> /> <?php echo __('AudioPlay', 'powerpress'); ?></label>
			<strong style="padding-top: 8px; margin-left: 20px;"><a href="#" id="activate_audioplay" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
			<?php
			echo powerpressplayer_build_audioplay($Audio['audioplay']);
			?>
			</p>
			<p>
				<?php echo __('AudioPlay is one button freeware audio (mp3 only) flash player. Features include a play/stop or play/pause button available in two sizes in either black or white.', 'powerpress'); ?>
			</p>
		</li>
		
		<li><label><input type="radio" name="Player[player]" id="player_html5audio" value="html5audio" <?php if( $General['player'] == 'html5audio' ) echo 'checked'; ?> /> <?php echo __('HTML5 Audio Player', 'powerpress'); ?>  </label>
			<strong style="padding-top: 8px; margin-left: 20px;"><a href="#" id="activate_html5audio" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
			<?php
			echo powerpressplayer_build_html5audio($Audio['html5audio']);
			?>
			</p>
			<p>
				<?php echo __('HTML5 audio is an element introduced in the latest HTML specification (HTML5) for the purpose of playing audio.', 'powerpress'); ?>
			</p>
			<p>
				<?php echo __('HTML5 Audio Player is not format specific. See table below for a list of browsers and supported formats.', 'powerpress'); ?>
			</p>
			<table class="html5formats" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<th><?php echo __('Browser', 'powerpress'); ?></th>
					<th>Mpeg3 (.mp3)</th>
					<th>AAC (.m4a)</th>
					<th>Vorbis (.ogg/.oga)</th>
				</tr>
				<tr>
					<td><i><?php echo __('Internet Explorer', 'powerpress'); ?></i></td>
					<td><strong>9.0+</strong></td>
					<td><strong>9.0+</strong></td>
					<td>-</td>
				</tr>
				<tr>
					<td><i><?php echo __('Firefox', 'powerpress'); ?></i></td>
					<td>-</td>
					<td>-</td>
					<td><strong>3.5+</strong></td>
				</tr>
				<tr>
					<td><i><?php echo __('Chrome', 'powerpress'); ?> <sup>1</sup></i></td>
					<td><strong>5.0+</strong></td>
					<td>-</td>
					<td><strong>5.0+</strong></td>
				</tr>
				<tr>
					<td><i><?php echo __('Opera', 'powerpress'); ?></i></td>
					<td>-</td>
					<td>-</td>
					<td><strong>10.5+</strong></td>
				</tr>
				<tr>
					<td><i><?php echo __('Safari', 'powerpress'); ?> <sup>2</sup></i></td>
					<td><strong>3.0+</strong></td>
					<td><strong>3.0+</strong></td>
					<td>-</td>
				</tr>
			</table>
			<div><sup>1</sup> <?php echo __('Chrome supported AAC in previous versions, but no longer supports the format.', 'powerpress'); ?></div>
			<div><sup>2</sup> <?php echo __('Safari requires QuickTime installed for HTML5 playback.', 'powerpress'); ?></div>
			<p>
				<?php echo __('Flow Player Classic is used when HTML5 support is not available.', 'powerpress'); ?>
			</p>
		</li>
		
	</ul>

</td>
</tr>
</table>
<?php
		}
?>
<h4 style="margin-bottom: 0;"><?php echo __('Click \'Save Changes\' to activate and configure selected player.', 'powerpress'); ?></h4>
<?php
	}
	else
	{
?>
<h2><?php echo __('Configure Player', 'powerpress'); ?></h2>
<?php if( $type == 'audio' ) { ?>
<p style="margin-bottom: 20px;"><strong><a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_player.php&amp;sp=1"); ?>"><?php echo __('Select a different audio player', 'powerpress'); ?></a></strong></p>
<?php } else { ?>
<p style="margin-bottom: 20px;"><strong><a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_videoplayer.php&amp;sp=1"); ?>"><?php echo __('Select a different video player', 'powerpress'); ?></a></strong></p>
<?php 
	}
		
	 // Start adding logic here to display options based on the player selected...
	 if( $type == 'audio' )
	 {
		switch( $General['player'] )
		{
			/*
			case 'audio-player': {
			
				$PlayerSettings = powerpress_get_settings('powerpress_audio-player');
				if($PlayerSettings == ""):
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
						'rtl' => 'no',
						'initialvolume'=>'60',
						'animation'=>'yes',
						'remaining'=>'no',
					);
				endif;

				if( empty($PlayerSettings['remaining']) )
					$PlayerSettings['remaining'] = 'no'; // New default setting
				if( !isset($PlayerSettings['buffer']) )
					$PlayerSettings['buffer'] = ''; // New default setting	
				if( !isset($PlayerSettings['titles']) )
					$PlayerSettings['titles'] = '';
?>
<script type="text/javascript"><!--

function update_audio_player()
{
	var myParams = new Array("lefticon","leftbg", "bg", "voltrack", "rightbg", "rightbghover", "righticon", "righticonhover", "loader", "track", "tracker", "border", "skip", "text", "pagebg", "rtl", "animation", "titles", "initialvolume");
	var myWidth = document.getElementById('player_width').value;
	var myBackground = '';
	if( myWidth < 10 || myWidth > 900 )
		myWidth = 290;
	
	var out = '<object type="application/x-shockwave-flash" data="<?php echo powerpress_get_root_url();?>/audio-player.swf" width="'+myWidth+'" height="24">'+"\n";
	out += '    <param name="movie" value="<?php echo powerpress_get_root_url();?>/audio-player.swf" />'+"\n";
	out += '    <param name="FlashVars" value="playerID=1&amp;soundFile=<?php echo $Audio['audio-player']; ?>';
	
	var x = 0;
	for( x = 0; x < myParams.length; x++ )
	{
		if( myParams[ x ] == 'border' )
			var Element = document.getElementById( 'player_border' );
		else
			var Element = document.getElementById( myParams[ x ] );
		
		if( Element )
		{
			if( Element.value != '' )
			{
				out += '&amp;';
				out += myParams[ x ];
				out += '=';
				out += Element.value.replace(/^#/, '');
				if( myParams[ x ] == 'pagebg' )
				{
					myBackground = '<param name="bgcolor" value="'+ Element.value +'" />';
					out += '&amp;transparentpagebg=no';
				}
			}
			else
			{
				if( myParams[ x ] == 'pagebg' )
				{
					out += '&amp;transparentpagebg=yes';
					myBackground = '<param name="wmode" value="transparent" />';
				}
			}
		}
	}
	
	out += '" />'+"\n";
	out += '<param name="quality" value="high" />';
	out += '<param name="menu" value="false" />';
	out += myBackground;
	out += '</object>';
	
	var player = document.getElementById("player_preview");
	player.innerHTML = out;
}

function audio_player_defaults()
{
 	if( confirm('<?php echo __("Set defaults, are you sure?\\n\\nAll of the current settings will be overwritten!", 'powerpress'); ?>') )
	{
		jQuery('#player_width').val('290');
		UpdatePlayerPreview('player_width',jQuery('#player_width').val() );
		
		jQuery('#transparentpagebg').val( 'yes');
		UpdatePlayerPreview('transparentpagebg',jQuery('#transparentpagebg').val() );
		
		jQuery('#lefticon').val( '#333333');
		UpdatePlayerPreview('lefticon',jQuery('#lefticon').val() );
		jQuery('#lefticon_prev'  ).css( { 'background-color' : '#333333' } );
		
		jQuery('#leftbg').val( '#CCCCCC');
		UpdatePlayerPreview('leftbg',jQuery('#leftbg').val() );
		jQuery('#leftbg_prev'  ).css( { 'background-color' : '#CCCCCC' } );
		
		jQuery('#bg').val( '#E5E5E5');
		UpdatePlayerPreview('bg',jQuery('#bg').val() );
		jQuery('#bg_prev'  ).css( { 'background-color' : '#E5E5E5' } );
		
		jQuery('#voltrack').val( '#F2F2F2');
		UpdatePlayerPreview('voltrack',jQuery('#voltrack').val() );
		jQuery('#voltrack_prev'  ).css( { 'background-color' : '#F2F2F2' } );
		
		jQuery('#volslider').val( '#666666');
		UpdatePlayerPreview('volslider',jQuery('#volslider').val() );
		jQuery('#volslider_prev'  ).css( { 'background-color' : '#666666' } );
		
		jQuery('#rightbg').val( '#B4B4B4');
		UpdatePlayerPreview('rightbg',jQuery('#rightbg').val() );
		jQuery('#rightbg_prev'  ).css( { 'background-color' : '#B4B4B4' } );
		
		jQuery('#rightbghover').val( '#999999');
		UpdatePlayerPreview('rightbghover',jQuery('#rightbghover').val() );
		jQuery('#rightbghover_prev'  ).css( { 'background-color' : '#999999' } );
		
		jQuery('#righticon').val( '#333333');
		UpdatePlayerPreview('righticon',jQuery('#righticon').val() );
		jQuery('#righticon_prev'  ).css( { 'background-color' : '#333333' } );
		
		jQuery('#righticonhover').val( '#FFFFFF');
		UpdatePlayerPreview('righticonhover',jQuery('#righticonhover').val() );
		jQuery('#righticonhover_prev'  ).css( { 'background-color' : '#FFFFFF' } );
		
		jQuery('#loader').val( '#009900');
		UpdatePlayerPreview('loader',jQuery('#loader').val() );
		jQuery('#loader_prev'  ).css( { 'background-color' : '#009900' } );
		
		jQuery('#track').val( '#FFFFFF');
		UpdatePlayerPreview('track',jQuery('#track').val() );
		jQuery('#track_prev'  ).css( { 'background-color' : '#FFFFFF' } );
		
		jQuery('#tracker').val( '#DDDDDD');
		UpdatePlayerPreview('tracker',jQuery('#tracker').val() );
		jQuery('#tracker_prev'  ).css( { 'background-color' : '#DDDDDD' } );
		
		jQuery('#player_border').val( '#CCCCCC');
		UpdatePlayerPreview('player_border',jQuery('#player_border').val() );
		jQuery('#player_border_prev'  ).css( { 'background-color' : '#CCCCCC' } );
		
		jQuery('#skip').val( '#666666');
		UpdatePlayerPreview('skip',jQuery('#skip').val() );
		jQuery('#skip_prev'  ).css( { 'background-color' : '#666666' } );
		
		jQuery('#text').val( '#333333');
		UpdatePlayerPreview('text',jQuery('#text').val() );
		jQuery('#text_prev'  ).css( { 'background-color' : '#333333' } );
		
		jQuery('#pagebg').val( '');
		UpdatePlayerPreview('pagebg',jQuery('#pagebg').val() );
		
		jQuery('#animation').val( 'yes');
		UpdatePlayerPreview('animation',jQuery('#animation').val() );
		
		jQuery('#remaining').val( 'no');
		UpdatePlayerPreview('remaining',jQuery('#remaining').val() );
		
		jQuery('#buffer').val( '');
		UpdatePlayerPreview('buffer',jQuery('#buffer').val() );
		
		jQuery('#rtl' ).val( 'no' );
		UpdatePlayerPreview('rtl',jQuery('#rtl').val() );
		
		jQuery('#initialvolume').val('60');
		UpdatePlayerPreview('initialvolume',jQuery('#initialvolume').val() );
		
		update_audio_player();
	}
}
//-->
</script>
	<input type="hidden" name="action" value="powerpress-audio-player" />
	<?php echo __('Configure the 1 pixel out Audio Player', 'powerpress'); ?>
	
	
<table class="form-table">
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?>
		</th>
		<td><div id="player_preview">
		<?php
			echo powerpressplayer_build_1pxoutplayer($Audio['audio-player'], array('nodiv'=>true) );
		?>
			</div>
		</td>
	</tr>
</table>

<div id="powerpress_settings_page" class="powerpress_tabbed_content" style="position: relative;">
	<div style="position: absolute; top: 6px; right:0px;">
		<a href="#" onclick="audio_player_defaults();return false;"><?php echo __('Set Defaults', 'powerpress'); ?></a>
	</div>
  <ul class="powerpress_settings_tabs"> 
		<li><a href="#tab_general"><span><?php echo __('Basic Settings', 'powerpress'); ?></span></a></li> 
		<li><a href="#tab_progress"><span><?php echo __('Progress Bar', 'powerpress'); ?></span></a></li> 
		<li><a href="#tab_volume"><span><?php echo __('Volume Button', 'powerpress'); ?></span></a></li>
		<li><a href="#tab_play"><span><?php echo __('Play / Pause Button', 'powerpress'); ?></span></a></li>
  </ul>
	
 <div id="tab_general" class="powerpress_tab">
 <h3><?php echo __('General Settings', 'powerpress'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Page Background Color', 'powerpress'); ?>
                        
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="pagebg" name="Player[pagebg]" class="color_field" value="<?php echo $PlayerSettings['pagebg']; ?>" maxlength="20" />
				<img id="pagebg_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['pagebg']; ?>;" class="color_preview" />
			</div>
			<small>(<?php echo __('leave blank for transparent', 'powerpress'); ?>)</small>
		</td>
	</tr>	<tr valign="top">
		<th scope="row">
			<?php echo __('Player Background Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="bg" name="Player[bg]" class="color_field" value="<?php echo $PlayerSettings['bg']; ?>" maxlength="20" />
				<img id="bg_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['bg']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Width (in pixels)', 'powerpress'); ?>
		</th>
		<td>
          <input type="text" style="width: 50px;" id="player_width" name="Player[width]" class="other_field" value="<?php echo $PlayerSettings['width']; ?>" maxlength="20" />
				<?php echo __('width of the player. e.g. 290 (290 pixels) or 100%', 'powerpress'); ?>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Right-to-Left', 'powerpress'); ?>
		</th>
		<td>
			<select style="width: 102px;" id="rtl" name="Player[rtl]" class="other_field"> 
<?php
			$options = array( 'yes'=>__('Yes', 'powerpress'), 'no'=>__('No', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['rtl']);
?>
          </select>			<?php echo __('switches the layout to animate from the right to the left', 'powerpress'); ?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Loading Bar Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="loader" name="Player[loader]" class="color_field" value="<?php echo $PlayerSettings['loader']; ?>" maxlength="20" />
				<img id="loader_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['loader']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Text Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
                <input type="text" style="width: 100px;" id="text" name="Player[text]" class="color_field" value="<?php echo $PlayerSettings['text']; ?>" maxlength="20" />
						<img id="text_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['text']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Text In Player', 'powerpress'); ?> 
		</th>
		<td>
          <div><input type="text" style="width: 60%;" id="titles" name="Player[titles]" class="other_field" value="<?php echo $PlayerSettings['titles']; ?>" maxlength="100" /></div>
				<small><?php echo sprintf(__('Enter \'%s\' to display track name from mp3. Only works if media is hosted on same server as blog.', 'powerpress'), __('TRACK', 'powerpress') ); ?></small>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Play Animation', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
<select style="width: 102px;" id="animation" name="Player[animation]" class="other_field"> 
<?php
			$options = array( 'yes'=>__('Yes', 'powerpress'), 'no'=>__('No', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['animation']);
?>
                                </select>			<?php echo __('if no, player is always open', 'powerpress'); ?></div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Display Remaining Time', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
<select style="width: 102px;" id="remaining" name="Player[remaining]" class="other_field">
<?php
			$options = array( 'yes'=>__('Yes', 'powerpress'), 'no'=>__('No', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['remaining']);
?>
                                </select>			<?php echo __('if yes, shows remaining track time rather than ellapsed time (default: no)', 'powerpress'); ?></div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Buffering Time', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
<select style="width: 200px;" id="buffer" name="Player[buffer]" class="other_field"> 
<?php
			$options = array('0'=>__('No buffering', 'powerpress'), ''=>__('Default (5 seconds)', 'powerpress'),'10'=>__('10 seconds', 'powerpress'),'15'=>__('15 seconds', 'powerpress'),'20'=>__('20 seconds', 'powerpress'),'30'=>__('30 seconds', 'powerpress'),'60'=>__('60 seconds', 'powerpress'));
			powerpress_print_options( $options, $PlayerSettings['buffer']);
?>
                                </select>		<?php echo __('buffering time in seconds', 'powerpress'); ?></div>
		</td>
	</tr>
	
	
</table>
</div>

 <div id="tab_progress" class="powerpress_tab">
	<h3><?php echo __('Progress Bar', 'powerpress'); ?></h3>
<table class="form-table">
        <tr valign="top">
		<th scope="row">
			<?php echo __('Progress Bar Background', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
										<input type="text" style="width: 100px;" id="track" name="Player[track]" class="color_field" value="<?php echo $PlayerSettings['track']; ?>" maxlength="20" />
										<img id="track_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['track']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Progress Bar Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
                            <input type="text" style="width: 100px;" id="tracker" name="Player[tracker]" class="color_field" value="<?php echo $PlayerSettings['tracker']; ?>" maxlength="20" />
											<img id="tracker_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['tracker']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Progress Bar Border', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
                            <input type="text" style="width: 100px;" id="player_border" name="Player[border]" class="color_field" value="<?php echo $PlayerSettings['border']; ?>" maxlength="20" />
											<img id="player_border_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['border']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>       
	</table>
	</div>
	
	
<div id="tab_volume" class="powerpress_tab">
	<h3><?php echo __('Volume Button Settings', 'powerpress'); ?></h3>
	<table class="form-table">	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Initial Volume', 'powerpress'); ?> 
		</th>
		<td>
			<select style="width: 100px;" id="initialvolume" name="Player[initialvolume]" class="other_field">
<?php
			
			for($x = 0; $x <= 100; $x +=5 )
			{
				echo '<option value="'. $x .'"'. ($PlayerSettings['initialvolume'] == $x?' selected':'') .'>'. $x .'%</option>';
			}
?>
			</select> <?php echo __('initial volume level (default: 60)', 'powerpress'); ?>
		</td>
	</tr>
				
	<tr valign="top">
		<th scope="row">
			<?php echo __('Volumn Background Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="leftbg" name="Player[leftbg]" class="color_field" value="<?php echo $PlayerSettings['leftbg']; ?>" maxlength="20" />
				<img id="leftbg_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['leftbg']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Speaker Icon Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="lefticon" name="Player[lefticon]" class="color_field" value="<?php echo $PlayerSettings['lefticon']; ?>" maxlength="20" />
				<img id="lefticon_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['lefticon']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Volume Icon Background', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="voltrack" name="Player[voltrack]" class="color_field" value="<?php echo $PlayerSettings['voltrack']; ?>" maxlength="20" />
				<img id="voltrack_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['voltrack']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Volume Slider Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="volslider" name="Player[volslider]" class="color_field" value="<?php echo $PlayerSettings['volslider']; ?>" maxlength="20" />
				<img id="volslider_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['volslider']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
</table>
</div>

<div id="tab_play" class="powerpress_tab">
	<h3><?php echo __('Play / Pause Button Settings', 'powerpress'); ?></h3>
	<table class="form-table">	
        <tr valign="top">
		<th scope="row">
			<?php echo __('Play/Pause Background Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="rightbg" name="Player[rightbg]" class="color_field" value="<?php echo $PlayerSettings['rightbg']; ?>" maxlength="20" />
				<img id="rightbg_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['rightbg']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Play/Pause Hover Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="rightbghover" name="Player[rightbghover]" class="color_field" value="<?php echo $PlayerSettings['rightbghover']; ?>" maxlength="20" />
				<img id="rightbghover_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['rightbghover']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Play/Pause Icon Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="righticon" name="Player[righticon]" class="color_field" value="<?php echo $PlayerSettings['righticon']; ?>" maxlength="20" />
				<img id="righticon_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['righticon']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Play/Pause Icon Hover Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="righticonhover" name="Player[righticonhover]" class="color_field" value="<?php echo $PlayerSettings['righticonhover']; ?>" maxlength="20" />
				<img id="righticonhover_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['righticonhover']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>

</table>
</div> <!-- end tab -->
</div> <!-- end tab wrapper -->

<?php
			}; break;
			*/
                        case 'simple_flash': { ?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?>
		</th>
		<td><p>
		<?php
		// TODO
			echo powerpressplayer_build_simpleflash($Audio['simple_flash']);
		?>
			</p>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			&nbsp;
		</th>
		<td>
			<p><?php echo __('Simple Flash Player has no additional settings.', 'powerpress'); ?></p>
		</td>
	</tr>
</table>                            
              <?php          }; break;

			case 'flashmp3-maxi': {
				//get settings for Flash MP3 Maxi player
				$PlayerSettings = powerpress_get_settings('powerpress_flashmp3-maxi');
				
				//set array values for dropdown lists
				$autoload = array('always'=>'Always','never'=>'Never','autohide'=>'Auto Hide');
				$volume = array('0'=>'0','25'=>'25','50'=>'50','75'=>'75','100'=>'100','125'=>'125','150'=>'150','175'=>'175','200'=>'200');
				
				//set PlayerSettings as blank array for initial setup
						//This keeps the foreach loop from returning an error
						
				if( empty($PlayerSettings) ){
					$PlayerSettings = array(
						'bgcolor1'=>'#7c7c7c',
						'bgcolor2'=>'#333333',
						'bgcolor'=>'',
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
						'sliderovercolor' => '#eeee00'
					);
				}
?>
<script type="text/javascript"><!--

function audio_player_defaults()
{
	if( confirm('<?php echo __("Set defaults, are you sure?\\n\\nAll of the current settings will be overwritten!'", 'powerpress'); ?>) )
	{
		jQuery('#bgcolor1').val('#7c7c7c');
		UpdatePlayerPreview('bgcolor1',jQuery('#bgcolor1').val() );
		jQuery('#bgcolor1_prev'  ).css( { 'background-color' : '#7c7c7c' } );
		
		jQuery('#bgcolor2').val('#333333' );
		UpdatePlayerPreview('bgcolor2',jQuery('#bgcolor2').val() );
		jQuery('#bgcolor2_prev'  ).css( { 'background-color' : '#333333' } );
		
		jQuery('#textcolor' ).val( '#FFFFFF' );
		UpdatePlayerPreview('textcolor',jQuery('#textcolor').val() );
		jQuery('#textcolor_prev'  ).css( { 'background-color' : '#FFFFFF' } );
		
		jQuery('#buttoncolor' ).val( '#FFFFFF' );
		UpdatePlayerPreview('buttoncolor',jQuery('#buttoncolor').val() );
		jQuery('#buttoncolor_prev'  ).css( { 'background-color' : '#FFFFFF' } );
		
		jQuery('#buttonovercolor' ).val( '#FFFF00' );
		UpdatePlayerPreview('buttonovercolor',jQuery('#buttonovercolor').val() );
		jQuery('#buttonovercolor_prev'  ).css( { 'background-color' : '#FFFF00' } );
		
		jQuery('#showstop' ).val( '0' );
		UpdatePlayerPreview('showstop',jQuery('#showstop').val() );
		jQuery('#showinfo' ).val( '0' );
		UpdatePlayerPreview('showinfo',jQuery('#showinfo').val() );
		jQuery('#showvolume' ).val( '1' );
		UpdatePlayerPreview('showvolume',jQuery('#showvolume').val() );
		
		jQuery('#player_height' ).val( '20' );
		UpdatePlayerPreview('height',jQuery('#player_height').val() );
		
		jQuery('#player_width' ).val( '200' );
		UpdatePlayerPreview('width',jQuery('#player_width').val() );
		
		jQuery('#showloading' ).val( 'autohide' );
		UpdatePlayerPreview('showloading',jQuery('#showloading').val() );
		
		
		jQuery('#slidercolor1').val('#cccccc' );
		UpdatePlayerPreview('slidercolor1',jQuery('#slidercolor1').val() );
		jQuery('#slidercolor1_prev'  ).css( { 'background-color' : '#cccccc' } );
		
		jQuery('#slidercolor2').val('#888888' );
		UpdatePlayerPreview('slidercolor2',jQuery('#slidercolor2').val() );
		jQuery('#slidercolor2_prev'  ).css( { 'background-color' : '#888888' } );
		
		jQuery('#sliderheight' ).val( '10' );
		UpdatePlayerPreview('sliderheight',jQuery('#sliderheight').val() );
		jQuery('#sliderwidth' ).val( '20' );
		UpdatePlayerPreview('sliderwidth',jQuery('#sliderwidth').val() );
		
		jQuery('#loadingcolor' ).val( '#FFFF00' );
		UpdatePlayerPreview('loadingcolor',jQuery('#loadingcolor').val() );
		jQuery('#loadingcolor_prev'  ).css( { 'background-color' : '#FFFF00' } );
		
		jQuery('#bgcolor').val('');
		UpdatePlayerPreview('bgcolor',jQuery('#bgcolor').val() );
		jQuery('#bgcolor_prev'  ).css( { 'background-color' : '' } );
		
		jQuery('#volumeheight' ).val( '6' );
		UpdatePlayerPreview('volumeheight',jQuery('#volumeheight').val() );
		jQuery('#volumewidth' ).val( '30' );
		UpdatePlayerPreview('volumewidth',jQuery('#volumewidth').val() );
		
		jQuery('#sliderovercolor' ).val( '#eeee00' );
		UpdatePlayerPreview('sliderovercolor',jQuery('#sliderovercolor').val() );
		jQuery('#sliderovercolor_prev'  ).css( { 'background-color' : '#eeee00' } );
		
		jQuery('#volume' ).val( '100' );
		UpdatePlayerPreview('volume',jQuery('#volume').val() );
		
		jQuery('#showslider' ).val( '1' );
		UpdatePlayerPreview('showslider',jQuery('#showslider').val() );
		
		jQuery('#buttonwidth' ).val( '26' );
		UpdatePlayerPreview('buttonwidth',jQuery('#buttonwidth').val() );
		
		//update_audio_player();
		generator.updatePlayer();
	}
}
//-->
</script>
	<input type="hidden" name="action" value="powerpress-flashmp3-maxi" />
	<p><?php echo __('Configure Flash Mp3 Maxi Player', 'powerpress'); ?></p>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td>
			<div id="player_preview"></div>

<script type="text/javascript" src="<?php echo powerpress_get_root_url(); ?>3rdparty/maxi_player/generator.js"></script>
<input type="hidden" id="gen_mp3" name="gen_mp3" value="<?php echo $Audio['flashmp3-maxi']; ?>" />


		</td>
	</tr>
</table>

<div id="powerpress_settings_page" class="powerpress_tabbed_content" style="position: relative;">
	<div style="position: absolute; top: 6px; right:0px;">
		<a href="#" onclick="audio_player_defaults();return false;"><?php echo __('Set Defaults', 'powerpress'); ?></a>
	</div>
  <ul class="powerpress_settings_tabs"> 
		<li><a href="#tab_general"><span><?php echo __('Basic Settings', 'powerpress'); ?></span></a></li> 
		<li><a href="#tab_buttons"><span><?php echo __('Button Settings', 'powerpress'); ?></span></a></li> 
		<li><a href="#tab_volume"><span><?php echo __('Volume Settings', 'powerpress'); ?></span></a></li>
		<li><a href="#tab_slider"><span><?php echo __('Slider Settings', 'powerpress'); ?></span></a></li>
  </ul>
	
 <div id="tab_general" class="powerpress_tab">
		<h3><?php echo __('General Settings', 'powerpress'); ?></h3>
		<table class="form-table">
        <tr valign="top">
            <td colspan="2">
            
            <?php echo __('leave blank for default values', 'powerpress'); ?>
            </td>
        </tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Player Gradient Color Top', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="bgcolor1"  name="Player[bgcolor1]" class="color_field" value="<?php echo $PlayerSettings['bgcolor1']; ?>" maxlength="20" />
				<img id="bgcolor1_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['bgcolor1']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Player Gradient Color Bottom', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="bgcolor2" name="Player[bgcolor2]" class="color_field" value="<?php echo $PlayerSettings['bgcolor2']; ?>" maxlength="20" />
				<img id="bgcolor2_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['bgcolor2']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Background Color', 'powerpress'); ?>
                        
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="bgcolor" name="Player[bgcolor]" class="color_field" value="<?php echo $PlayerSettings['bgcolor']; ?>" maxlength="20" />
				<img id="bgcolor_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['bgcolor']; ?>;" class="color_preview" />
			</div>
			<small><?php echo __('leave blank for transparent', 'powerpress'); ?></small>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Text Color', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="textcolor" name="Player[textcolor]" class="color_field" value="<?php echo $PlayerSettings['textcolor']; ?>" maxlength="20" />
				<img id="textcolor_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['textcolor']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Player Height (in pixels)', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 50px;" id="player_height" name="Player[height]" value="<?php echo $PlayerSettings['height']; ?>" maxlength="20" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Player Width (in pixels)', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 50px;" id="player_width" name="Player[width]" value="<?php echo $PlayerSettings['width']; ?>" maxlength="20" />
			</div>
		</td>
	</tr>
</table>
</div>

 <div id="tab_buttons" class="powerpress_tab">
		<h3><?php echo __('Button Settings', 'powerpress'); ?></h3>
		<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Button Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="buttoncolor" name="Player[buttoncolor]" class="color_field" value="<?php echo $PlayerSettings['buttoncolor']; ?>" maxlength="20" />
				<img id="buttoncolor_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['buttoncolor']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Button Hover Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="buttonovercolor" name="Player[buttonovercolor]" class="color_field" value="<?php echo $PlayerSettings['buttonovercolor']; ?>" maxlength="20" />
				<img id="buttonovercolor_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['buttonovercolor']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Button Width (in pixels)', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 50px;" id="buttonwidth" name="Player[buttonwidth]" value="<?php echo $PlayerSettings['buttonwidth']; ?>" maxlength="20" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Show Stop Button', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<select style="width: 100px;" id="showstop" name="Player[showstop]">
<?php
			$options = array( '1'=>__('Yes', 'powerpress'), '0'=>__('No', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['showstop']);
?>
                                </select>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Show Info', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<select style="width: 100px;" id="showinfo" name="Player[showinfo]">
<?php
			$options = array( '1'=>__('Yes', 'powerpress'), '0'=>__('No', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['showinfo']);
?>
                                </select>
			</div>
		</td>
	</tr>
</table>
</div>

 <div id="tab_volume" class="powerpress_tab">
		<h3><?php echo __('Volume Settings', 'powerpress'); ?></h3>
		<table class="form-table">
        
        <tr valign="top">
		<th scope="row">
			<?php echo __('Show Volume', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<select style="width: 100px;" id="showvolume" name="Player[showvolume]">
<?php
			$options = array( '1'=>__('Yes', 'powerpress'), '0'=>__('No', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['showvolume']);
?>
                                </select>
			</div>
		</td>
	</tr>	
        <tr valign="top">
		<th scope="row">
			<?php echo __('Volume', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<select style="width: 100px;" id="volume" name="Player[volume]">
<?php
			powerpress_print_options( $volume, $PlayerSettings['volume']);
?>
                                </select>
			</div>
		</td>
	</tr>	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Volume Height (in pixels)', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 50px;" id="volumeheight" name="Player[volumeheight]" value="<?php echo $PlayerSettings['volumeheight']; ?>" maxlength="20" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Volume Width (in pixels)', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 50px;" id="volumewidth" name="Player[volumewidth]" value="<?php echo $PlayerSettings['volumewidth']; ?>" maxlength="20" />
			</div>
		</td>
	</tr>

</table>
</div>

 <div id="tab_slider" class="powerpress_tab">
		<h3><?php echo __('Slider Settings', 'powerpress'); ?></h3>
		<table class="form-table">
		
        <tr valign="top">
		<th scope="row">
			<?php echo __('Show Slider', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<select style="width: 100px;" id="showslider" name="Player[showslider]">
<?php
			$options = array( '1'=>__('Yes', 'powerpress'), '0'=>__('No', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['showslider']);
?>
                                </select>
			</div>
		</td>
	</tr>	
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Slider Color Top', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="slidercolor1" name="Player[slidercolor1]" class="color_field" value="<?php echo $PlayerSettings['slidercolor1']; ?>" maxlength="20" />
				<img id="slidercolor1_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['slidercolor1']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Slider Color Bottom', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="slidercolor2" name="Player[slidercolor2]" class="color_field" value="<?php echo $PlayerSettings['slidercolor2']; ?>" maxlength="20" />
				<img id="slidercolor2_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['slidercolor2']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Slider Hover Color', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="sliderovercolor" name="Player[sliderovercolor]" class="color_field" value="<?php echo $PlayerSettings['sliderovercolor']; ?>" maxlength="20" />
				<img id="sliderovercolor_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['sliderovercolor']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Slider Height (in pixels)', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 50px;" id="sliderheight" name="Player[sliderheight]" value="<?php echo $PlayerSettings['sliderheight']; ?>" maxlength="20" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Slider Width (in pixels)', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 50px;" id="sliderwidth" name="Player[sliderwidth]" value="<?php echo $PlayerSettings['sliderwidth']; ?>" maxlength="20" />
			</div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Show Loading Buffer', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<select style="width: 100px;" id="showloading" name="Player[showloading]">
<?php
			powerpress_print_options( $autoload, $PlayerSettings['showloading']);
?>
                                </select>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Loading Buffer Color', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="loadingcolor" name="Player[loadingcolor]" class="color_field" value="<?php echo $PlayerSettings['loadingcolor']; ?>" maxlength="20" />
				<img id="loadingcolor_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['loadingcolor']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>

</table>
</div> <!-- end tab -->
</div><!-- end tab container -->

<script type="text/javascript"><!--

	generator.player = '<?php echo powerpress_get_root_url(); ?>player_mp3_maxi.swf';
	generator.addParam("gen_mp3", "mp3", "url", '');
	generator.addParam("player_height", "height", "int", "20");
	generator.addParam("player_width", "width", "int", "200");
	generator.addParam("bgcolor1", "bgcolor1", "color", "#7c7c7c");
	generator.addParam("bgcolor2", "bgcolor2", "color", "#333333");
	generator.addParam("bgcolor", "bgcolor", "color", "");
	generator.addParam("textcolor", "textcolor", "color", "#FFFFFF");
	generator.addParam("loadingcolor", "loadingcolor", "color", "#FFFF00");
	generator.addParam("buttoncolor", "buttoncolor", "color", "#FFFFFF");
	generator.addParam("buttonovercolor", "buttonovercolor", "color", "#FFFF00");
	generator.addParam("showloading", "showloading", "text", "autohide");
	generator.addParam("showinfo", "showinfo", "bool", "0");
	generator.addParam("showstop", "showstop", "int", "0");
	generator.addParam("showvolume", "showvolume", "int", "0");
	generator.addParam("buttonwidth", "buttonwidth", "int", "26");
	generator.addParam("volume", "volume", "int", "100");
	generator.addParam("volumeheight", "volumeheight", "int", "6");
	generator.addParam("volumewidth", "volumewidth", "int", "30");
	generator.addParam("sliderovercolor", "sliderovercolor", "color", "#eeee00");
	generator.addParam("showslider", "showslider", "bool", "1");
	generator.addParam("slidercolor1", "slidercolor1", "color", "#cccccc");
	generator.addParam("slidercolor2", "slidercolor2", "color", "#888888");
	generator.addParam("sliderheight", "sliderheight", "int", "10");
	generator.addParam("sliderwidth", "sliderwidth", "int", "20");
	
	generator.updatePlayer();
//-->
</script>
<?php
			}; break;
			
			case 'audioplay': {
				$PlayerSettings = powerpress_get_settings('powerpress_audioplay');
				if( empty($PlayerSettings) ) {
					$PlayerSettings = array(
						'bgcolor' => '',
						'buttondir' => 'negative',
						'mode' => 'playpause'
					);
				}
?>
        	<input type="hidden" name="action" value="powerpress-audioplay" />
	<?php echo __('Configure the AudioPlay Player', 'powerpress'); ?><br clear="all" />

<table class="form-table">
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td colspan="2">
			<div id="player_preview">
                        
<?php                                                                         
	echo powerpressplayer_build_audioplay($Audio['audioplay']);
?>
                </div>
            </td>
        </tr>
</table>
				
		<h2><?php echo __('General Settings', 'powerpress'); ?></h2>
	<table class="form-table">
        <tr valign="top">
		<th scope="row">
			<?php echo __('Background Color', 'powerpress'); ?>
                        
		</th>
		<td valign="top">
			<div class="color_control">
				<input type="text" style="width: 100px;" id="bgcolor" name="Player[bgcolor]" class="color_field" value="<?php echo $PlayerSettings['bgcolor']; ?>" maxlength="20" />
				<img id="bgcolor_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['bgcolor']; ?>;" class="color_preview" />
			</div>
			<small><?php echo __('leave blank for transparent', 'powerpress'); ?></small>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Player Mode', 'powerpress'); ?>
		</th>
		<td valign="top">
			<div class="color_control">
                            <select name="Player[mode]" id="mode">
<?php
			$options = array( 'playpause'=>__('Play/Pause', 'powerpress'), 'playstop'=>__('Play/Stop', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['mode']);
?>     
                            </select>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Player Button', 'powerpress'); ?>
		</th>
		<td valign="top">
			<div class="color_control">
                        <table cellpadding="0" cellspacing="0">
                                <?php $options = array('classic','classic_small','negative','negative_small');
                                 foreach($options as $option){
                                        if($PlayerSettings['buttondir'] == $option):
                                            $selected = " CHECKED";
                                        else:
                                            $selected = "";
                                        endif;
                                        if(($option == "classic") || ($option == "classic_small")){
                                            $td = '<td style="background: #999;" align="center">';
                                            $warning = "(ideal for dark backgrounds)";
                                            if($option == "classic_small") {
                                                $name = __('Small White', 'powerpress');
                                            }else{
                                                $name = __('Large White', 'powerpress');
                                            }
                                        }
                                        else {
                                            $td = '<td align="center">';
                                            $warning = "";
                                            if($option == "negative_small") {
                                                $name = __('Small Black', 'powerpress');
                                            }else{
                                                $name = __('Large Black', 'powerpress');
                                            }

                                        }
                                        echo '<tr><td><input type="radio" name="Player[buttondir]" value="'. $option .'"'. $selected .' /></td>'.$td.'<img src="'. powerpress_get_root_url().'buttons/'.$option.'/playup.png" /></td><td>'.$name.' Button '.$warning.'</td></tr>';
                                }?>
                                
                            </table>
			</div>
		</td>
	</tr>

</table>
<?php
			}; break;
			case 'html5audio': {
				$SupportUploads = powerpressadmin_support_uploads();
?>
<p><?php echo __('Configure HTML5 Audio Player', 'powerpress'); ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td>
			<p>
<?php
			echo powerpressplayer_build_html5audio( $Audio['default'] );
?>
			</p>
		</td>
	</tr>

	
	<tr>
	<th scope="row">
	<?php echo __('Play Icon', 'powerpress'); ?></th>
	<td>

	<input type="text" id="audio_custom_play_button" name="General[audio_custom_play_button]" style="width: 60%;" value="<?php echo $General['audio_custom_play_button']; ?>" maxlength="250" />
	<a href="#" onclick="javascript: window.open( document.getElementById('audio_custom_play_button').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

	<p><?php echo __('Place the URL to the play icon above.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?>: http://example.com/images/audio_play_icon.jpg<br /><br />
	<?php echo __('Leave blank to use default play icon image.', 'powerpress'); ?></p>

	<?php if( $SupportUploads ) { ?>
	<p><input name="audio_custom_play_button_checkbox" type="checkbox" onchange="powerpress_show_field('audio_custom_play_button_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?> </p>
	<div style="display:none" id="audio_custom_play_button_upload">
		<label for="audio_custom_play_button_file"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" name="audio_custom_play_button_file"  />
	</div>
	<?php } ?>
	</td>
	</tr>
</table>

<?php
			}; break;
		
			default: {
			
				if( empty($General['player_width_audio']) )
					$General['player_width_audio'] = '';
			
?>
<p><?php echo __('Configure Flow Player Classic', 'powerpress'); ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td>
			<p>
<?php
			echo powerpressplayer_build_flowplayerclassic( $Audio['default'] );
?>
			</p>
		</td>
	</tr>
</table>

<h2><?php echo __('General Settings', 'powerpress'); ?></h2>
	<table class="form-table">
        <tr valign="top">
		<th scope="row">
			<?php echo __('Width', 'powerpress'); ?>   
		</th>
		<td valign="top">
				<input type="text" style="width: 50px;" id="player_width" name="General[player_width_audio]" class="player-width" value="<?php echo $General['player_width_audio']; ?>" maxlength="4" />
			<?php echo __('Width of Audio mp3 player (leave blank for 320 default)', 'powerpress'); ?>
		</td>
	</tr>
</table>
<?php
			} break;
		}
	 }
	 else // Video
	 {
			switch( $General['video_player'] )
			{
				case 'flow-player-classic': {
					?>
					<p><?php echo __('Configure Flow Player Classic', 'powerpress'); ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td>
			<p>
<?php
			echo powerpressplayer_build_flowplayerclassic( $Video['flow-player-classic'] );
?>
			</p>
		</td>
	</tr>
</table>

					<?php
				}; break;
				case 'html5video': {
					?>
					<p><?php echo __('Configure HTML5 Video Player', 'powerpress'); ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td>
			<p>
<?php
				echo powerpressplayer_build_html5video( $Video['html5video'] );
?>
			</p>
		</td>
	</tr>
</table>

					<?php
				}; break;
				case 'videojs-html5-video-player-for-wordpress': {
					?>
					<p><?php echo __('Configure VideoJS', 'powerpress'); ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td>
			<p>
<?php
				echo powerpressplayer_build_videojs( $Video['videojs-html5-video-player-for-wordpress'] );
?>
			</p>
		</td>
	</tr>
</table>
<h3><?php echo __('VideoJS Settings', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('VideoJS CSS Class', 'powerpress'); ?>
</th>
<td>
<p>
<input type="text" name="General[videojs_css_class]" style="width: 150px;" value="<?php echo ( empty($General['videojs_css_class']) ?'':htmlspecialchars($General['videojs_css_class']) ); ?>" /> 
<?php echo __('Apply specific CSS styling to your Video JS player.', 'powerpress'); ?>
</p>
</td>
</tr>
</table>
					<?php
				}; break;
			}
			
			if( !isset($General['poster_play_image']) )
				$General['poster_play_image'] = 1;
			if( !isset($General['poster_image_audio']) )
				$General['poster_image_audio'] = 0;
			if( !isset($General['player_width']) )
				$General['player_width'] = '';
			if( !isset($General['player_height']) )
				$General['player_height'] = '';
			if( !isset($General['poster_image']) )
				$General['poster_image'] = '';
			
			if( !isset($General['video_custom_play_button']) )
				$General['video_custom_play_button'] = '';

?>
<!-- Global Video Player settings (Appy to all video players -->
<input type="hidden" name="action" value="powerpress-save-videocommon" />
<h3><?php echo __('Common Settings', 'powerpress'); ?></h3>
<p><?php echo __('The following video settings apply to the video player above as well as to classic video &lt;embed&gt; formats such as Microsoft Windows Media (.wmv), QuickTime (.mov) and RealPlayer.', 'powerpress'); ?></p>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Player Width', 'powerpress'); ?>
</th>
<td>
<input type="text" name="General[player_width]" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" value="<?php echo $General['player_width']; ?>" maxlength="4" />
<?php echo __('Width of player (leave blank for 400 default)', 'powerpress'); ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Player Height', 'powerpress'); ?>
</th>
<td>
<input type="text" name="General[player_height]" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" value="<?php echo $General['player_height']; ?>" maxlength="4" />
<?php echo __('Height of player (leave blank for 225 default)', 'powerpress'); ?>
</td>
</tr>
<?php
		$SupportUploads = powerpressadmin_support_uploads();
		
// Play icon, only applicable to HTML5/FlowPlayerClassic
		if( in_array($General['video_player'], array('flow-player-classic','html5video') ) )
		{
?>
<tr valign="top">
<th scope="row">
<?php echo __('QuickTime Scale', 'powerpress'); ?></th>
<td>
	<select name="General[player_scale]" class="bpp_input_sm" onchange="javascript:jQuery('#player_scale_custom').css('display', (this.value=='tofit'||this.value=='aspect'? 'none':'inline' ))">
<?php
	$scale_options = array('tofit'=>__('ToFit (default)', 'powerpress'), 'aspect'=>__('Aspect', 'powerpress') ); 
	if( !isset($General['player_scale']) )
		$General['player_scale'] = 'tofit'; // Tofit works in almost all cases
	
	if( is_numeric($General['player_scale']) )
		$scale_options[ $General['player_scale'] ]= __('Custom', 'powerpress');
	else
		$scale_options['custom']= __('Custom', 'powerpress');

while( list($value,$desc) = each($scale_options) )
	echo "\t<option value=\"$value\"". ($General['player_scale']==$value?' selected':''). ">$desc</option>\n";
	
?>
</select>
<span id="player_scale_custom" style="display: <?php echo (is_numeric($General['player_scale'])?'inline':'none'); ?>">
	<?php echo __('Scale:', 'powerpress'); ?> <input type="text" name="PlayerScaleCustom" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9.]/g, '');" value="<?php echo (is_numeric($General['player_scale'])?$General['player_scale']:''); ?>" maxlength="4" /> <?php echo __('e.g.', 'powerpress'); ?> 1.5
</span>
<p style="margin-top: 5px; margin-bottom: 0;">
	<?php echo __('If you do not see video, adjust the width, height and scale settings above.', 'powerpress'); ?>
</p>
</td>
</tr>
<?php
		}
?>
<tr>
<th scope="row">
<?php echo __('Default Poster Image', 'powerpress'); ?></th>
<td>

<input type="text" id="poster_image" name="General[poster_image]" style="width: 60%;" value="<?php echo $General['poster_image']; ?>" maxlength="250" />
<a href="#" onclick="javascript: window.open( document.getElementById('poster_image').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

<p><?php echo __('Place the URL to the poster image above.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?>: http://example.com/images/poster.jpg<br /><br />
<?php echo __('Image should be at minimum the same width/height as the player above. Leave blank to use default black background image.', 'powerpress'); ?></p>

<?php if( $SupportUploads ) { ?>
<p><input name="poster_image_checkbox" type="checkbox" onchange="powerpress_show_field('poster_image_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?> </p>
<div style="display:none" id="poster_image_upload">
	<label for="poster_image_file"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" name="poster_image_file"  />
</div>
<?php } ?>
<?php
		if( in_array($General['video_player'], array('flow-player-classic','html5video') ) )
		{
?>
<p><input name="General[poster_play_image]" type="checkbox" value="1" <?php echo ($General['poster_play_image']?'checked':''); ?> /> <?php echo __('Include play icon over poster image when applicable', 'powerpress'); ?> </p>
<p><input name="General[poster_image_audio]" type="checkbox" value="1" <?php echo ($General['poster_image_audio']?'checked':''); ?> /> <?php echo __('Use poster image, player width and height above for audio (Flow Player only)', 'powerpress'); ?> </p>
<?php } ?>
</td>
</tr>

<?php
		// Play icon, only applicable to HTML5/FlowPlayerClassic
		if( in_array($General['video_player'], array('flow-player-classic','html5video') ) )
		{
?>
<tr>
<th scope="row">
<?php echo __('Play Icon', 'powerpress'); ?></th>
<td>

<input type="text" id="video_custom_play_button" name="General[video_custom_play_button]" style="width: 60%;" value="<?php echo $General['video_custom_play_button']; ?>" maxlength="250" />
<a href="#" onclick="javascript: window.open( document.getElementById('video_custom_play_button').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

<p><?php echo __('Place the URL to the play icon above.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?>: http://example.com/images/video_play_icon.jpg<br /><br />
<?php echo __('Image should 60 pixels by 60 pixels. Leave blank to use default play icon image.', 'powerpress'); ?></p>

<?php if( $SupportUploads ) { ?>
<p><input name="video_custom_play_button_checkbox" type="checkbox" onchange="powerpress_show_field('video_custom_play_button_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?> </p>
<div style="display:none" id="video_custom_play_button_upload">
	<label for="video_custom_play_button_file"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" name="video_custom_play_button_file"  />
</div>
<?php } ?>
</td>
</tr>
<?php
		}
?>
</table>
<?php
	 }
?>

<?php
	}
}

?>