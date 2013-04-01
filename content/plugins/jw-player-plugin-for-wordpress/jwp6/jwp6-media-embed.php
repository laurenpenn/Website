<?php

// Include the necessary admin stuff.
require_once('../../../../wp-load.php');
require_once('../../../../wp-admin/includes/admin.php');

if ( !current_user_can('administrator') && !current_user_can('editor') && !current_user_can('contributor') ) {
    exit();
}

//global $wp_version;
define('MEDIA_MANAGER_35', version_compare($wp_version, '3.5', '>=') );

require_once( JWP6_PLUGIN_DIR . '/jwp6-class-plugin.php' );
require_once( JWP6_PLUGIN_DIR . '/jwp6-class-media.php' );
require_once( JWP6_PLUGIN_DIR . '/jwp6-class-shortcode.php' );
$jwp6m = new JWP6_Media();

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    if ( $_POST[JWP6 . 'mediaid'] || $_POST[JWP6 . 'file'] || $_POST[JWP6 . 'playlistid']) {
        $shortcode = new JWP6_Shortcode();
        media_send_to_editor($shortcode->shortcode());
        exit();
    } else {
        $no_video_error = true;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="<?php echo JWP6_PLUGIN_URL; ?>/css/jquery.select2.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo JWP6_PLUGIN_URL; ?>/css/jwp6-media.css" type="text/css" media="screen" />
    <script type="text/javascript" src="<?php echo JWP6_Plugin::player_url(); ?>"></script>
    <script type="text/javascript" src="<?php echo includes_url(); ?>js/jquery/jquery.js"></script>
    <script type="text/javascript" src="<?php echo JWP6_PLUGIN_URL; ?>js/jquery.select2.js"></script>
    <script type="text/javascript" src="<?php echo JWP6_PLUGIN_URL; ?>js/jwp6-media.js"></script>
    <?php JWP6_Plugin::insert_license_key(); ?>
    <script type="text/javascript">
    var JWP6_AJAX_URL = "<?php echo JWP6_PLUGIN_URL . 'jwp6-ajax.php'; ?>";
    jQuery(function () {
        jQuery('#player_name, #<?php echo JWP6; ?>mediaid, #<?php echo JWP6; ?>playlistid, #<?php echo JWP6; ?>imageid')
            .select2(jwp6media.SELECT2_SETTINGS)
            .bind('change', jwp6media.preview_player)
        ;
        jwp6media.init_media_wizard();
    });
    </script>
    <title>Add a JW Player</title>
</head>

<body>

<?php if ( ! MEDIA_MANAGER_35 ): ?>
<ul class="tabs">
    <li>
        <a href="<?php echo admin_url(); ?>media-upload.php">← Wordpress media manager</a>
    </li>
    <li class="active">
        <a href="">Embed a JW Player</a>
    </li>
</ul>
<?php endif; ?>

<div id="wrapper" class="jwp6-media-tab">

<?php if ( ! MEDIA_MANAGER_35 ): ?>
<h1>JW Player Embed Wizard</h1>
<?php endif; ?>

<?php if ( isset($no_video_error) ): ?>
<div class="notice">
    <strong>Please note:</strong>
    You need to pick at least a video file to embed a JW Player.
</div>
<?php endif; ?>

<form method="post" action="<?php echo JWP6_PLUGIN_URL; ?>/jwp6-media-embed.php" name="jwp6_wizard_form" id="jwp6_wizard_form">

<p>
    This wizard uses the media in your media library, which means you must 
    <a href="<?php echo admin_url(); ?>media-upload.php">upload files to your media 
    library</a> <em>before</em> you can embed it with the jw player.
</p>

<h2>Pick your player</h2>
<div class="select">
    <select id="player_name" name="player_name" style="width: 45%;">
        <?php foreach ($jwp6m->players as $player): ?>
        <?php $selected = ( 'default' == $player ) ? 'selected="selected"' : ''; ?>
        <option value="<?php echo $player; ?>"<?php echo $selected; ?>><?php echo $player; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<p class="info">
    Select the player you would like to use. You can add and manage players on the 
    <a href="">Player configuration page</a>
</p>

<div class="group" id="mediaid_group">
    <h2>Pick a video</h2>
    <div class="select">
        <select name="<?php echo JWP6; ?>mediaid" id="<?php echo JWP6; ?>mediaid" data-placeholder="Pick a video..." style="width: 90%;">
            <option value=""></option>
            <?php foreach ($jwp6m->videos() as $attachment): ?>
            <option value="<?php echo $attachment['id']; ?>" title="<?php echo $attachment['url']; ?>"
                data-thumb="<?php echo JWP6_Plugin::image_from_mediaid($attachment['id'], true); ?>">
                <?php echo $attachment['title']; ?> (<?php echo $attachment['name']; ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <p class="info">
        You can also 
        <a href="#file" class="fieldset_toggle">use a direct url to your video file</a> 
        or
        <a href="#playlistid" class="fieldset_toggle">select a playlist</a>.
    </p>
</div>
<div class="group hidden" id="file_group">
    <h2>Insert a video URL</h2>
    <div class="input">
        <input type="text" name="<?php echo JWP6; ?>file" />
    </div>
    <p class="info">
        You can also 
        <a href="#mediaid" class="fieldset_toggle">pick a video from the media library</a> 
        or
        <a href="#playlistid" class="fieldset_toggle">select a playlist</a>.
    </p>
</div>
<div class="group hidden" id="playlistid_group">
    <h2>Pick a playlist</h2>
    <div class="select">
        <select name="<?php echo JWP6; ?>playlistid" id="<?php echo JWP6; ?>playlistid" data-placeholder="Pick a playlist..." style="width: 90%;">
            <option value=""></option>
            <?php foreach ($jwp6m->playlists() as $playlist): ?>
            <option value="<?php echo $playlist->ID; ?>">
                <?php echo $jwp6m->playlist_name_with_info($playlist); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <p class="info">
        You can also 
        <a href="#mediaid" class="fieldset_toggle">pick a video from the media library</a> 
        or
        <a href="#file" class="fieldset_toggle">use a direct url to your video file</a>.
    </p>
</div>
<div class="group" id="image_yesno_group">
    <p class="info">
        You can use the thumbnail image associated with the video or
        <a href="#image" class="fieldset_toggle">select a separate thumbnail</a>.
    </p>
</div>
<div class="group hidden" id="imageid_group">
    <h2>Pick a video thumbnail</h2>
    <div class="select">
        <select type="hidden" name="<?php echo JWP6; ?>imageid" id="<?php echo JWP6; ?>imageid" data-placeholder="Pick a thumbnail..." style="width: 90%;">
            <option value=""></option>
            <?php foreach ($jwp6m->images() as $attachment): ?>
            <option value="<?php echo $attachment['id']; ?>" title="<?php echo $attachment['url']; ?>"
                data-thumb="<?php echo $attachment['url']; ?>">
                <?php echo $attachment['name']; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <p class="info">
        The thumbnail will show before the video starts playing.
        <a href="#image" class="fieldset_toggle">You can also use a direct url to your thumbnail</a>
        or
        <a href="#image_yesno" class="fieldset_toggle">use the image associated with the video</a>.
    </p>
</div>
<div class="group hidden" id="image_group">
    <h2>Insert a thumbnail url</h2>
    <div class="input">
        <input type="text" name="<?php echo JWP6; ?>image" />
    </div>
    <p class="info">
        The thumbnail will show before the video starts playing.
        <a href="#imageid" class="fieldset_toggle">You can also pick an image from the media library</a>
        or
        <a href="#image_yesno" class="fieldset_toggle">use the image associated with the video</a>.
    </p>
</div>

<div class="group submit">
    <button type="submit" class="button-primary" name="insert">Insert this player into your post</button>
</div>

</form><!-- end of jwp6_wizard_form -->

<h2>Preview</h2>

<div class="preview" id="player-preview">
    <p class="info">The preview of the player will show after you select a player and a video/video url/playlist.</p>
</div>

</div><!-- end of wrapper -->
</body>

</html>
