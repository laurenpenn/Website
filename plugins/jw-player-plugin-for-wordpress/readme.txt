=== JW Player for WordPress – Flash & HTML5 Video Player ===
Contributors: LongTail Video Inc.
Tags: JW Player, Video, Flash, LongTail Video, RTMP, Playlists, Audio, Image, HTML5, iPad, iPhone, iPod
Requires at least: 2.8.6
Tested up to: 3.3.2
Stable tag: 1.6.0

This video plugin is provided by LongTail Video Inc.  It enables you to embed Flash and HTML5 video using the JW Player on your WordPress website.

== Description ==

JW Player for WordPress makes it extremely easy to deliver Flash and HTML5 video through your WordPress website. This plugin has been developed by LongTail Video, the creator of the JW Player, and allows for easy customization and embedding of Flash and HTML5 video using the JW Player in your WordPress posts. It provides support for all of the player's configuration options, including skins, plugins and the LongTail Video AdSolution.

<strong>Key Features</strong>

* Full support for JW Player for Flash and HTML5.
* Manage multiple custom players.
* Full skin and plugin support with easy to use plugin customization interface.
* Full integration into the WordPress media library.  Embed video with the JW Player as you write your posts.
* Support for adding external video to your media library including Youtube and RTMP streams.
* A full featured playlist manager - order your media by simply dragging the mouse.
* A powerful shortcode system for customizations at embed time.

<strong>Documentation</strong>

* Full documentation for installation, setup and use on our <a href='http://www.longtailvideo.com/support/addons/jw-player-plugin-for-wordpress' target='_blank'>Support Site</a>.
* Important information about File Permissions:  http://codex.wordpress.org/Changing_File_Permissions
* If you have any questions, comments, problems or suggestions please post on our forum at: http://www.longtailvideo.com/support/forums
* The plugin has been tested on 1and1, MediaTemple, Dreamhost, and GoDaddy.

<strong>Troubleshooting</strong>

* If you're having issues please contact us at support@longtailvideo.com or our forum at: http://www.longtailvideo.com/support/forums.  We can't fix any issues you're having if we don't know they exist.
* Please include a description of the problem, a link to your site demonstrating the issue, and the host you're using.

== Installation ==

Installing for the first time:

1. Place the plugin folder in your plugin directory.
1. Visit the JW Player Upgrade page and download or upload your JW Player.
1. For more detailed instructions please visit: <a href='http://www.longtailvideo.com/support/addons/jw-player-plugin-for-wordpress/11513/getting-started-with-the-wordpress-plugin-for-the-jw-pla' target='_blank'>Getting Started with the WordPress Plugin for the JW Player</a>

Upgrading:

It is recommended that you back up your custom players if you are upgrading.

You can perform the automatic update, download the plugin and upload, or FTP the plugin to the plugins folder directly.

== Requirements ==

* WordPress 2.8.6 or higher
* PHP 5.0 or higher
* The wp-content/uploads directory needs to be writable by the plugin.  This is likely already the case as WordPress stores your media and various other uploads here.

== Usage ==

1. Go to Site Admin > Settings > JW Player Plugin
1. Click on the button to create a player.
1. Configure the Basic flashvars.
1. (Optional) Configure Advanced flashvars and add plugins.
1. Save your Player.
1. Create or edit a post.
1. Click on one of the Upload/Insert buttons
1. Using the Media Library tab, you should be able to edit your media.
1. For a video you want to insert, click the Insert JW Player button.
1. It should insert a tag that looks like the following: [jwplayer config="&lt;Player name&gt;" file="&lt;your video&gt;"] into the body.  &lt;your video&gt; is a url to your file.  The "config" attribute is only need when using a player other than the default.
1. Save your posts.

== Changelog ==

= 1.6.0 =

* FEATURE: Brand new Playlist Manager now found in the Media section of the Dashboard.  Now supports drag and drop, sorting and searching of your content.
* BUG: Thumbnail and HTML5 video selectors no longer load broken images.  A default image is shown instead.
* BUG: The LongTail Plugin class has been renamed.  This should avoid name collisions when running on IIS.

= 1.5.9 =

* BUG: Fixed unintended escaping of forward slashes in embed code

= 1.5.8 =

* ENHANCEMENT: updated sharing plugin to v3
* ENHANCEMENT: Improved parsing of additional flashvars.  Switched to one flashvar (name=value) per line for easier input.
* BUG: Added missing config blocks in mode blocks which could break HTML5 fallback
* BUG: Fixed encoding issues with complex flashvars in shortcode
* BUG: inline player javascript functions rendered more accurately
* BUG: improved compatibility with mootools based plugins and themes

= 1.5.7 =

* BUG: Fixed warning message introduced in 1.5.6 that was missed in testing
* BUG: Fixed notice message that could appear on Plugin page

= 1.5.6 =

* ENHANCEMENT: Playlists now correctly support HTML5 fallback
* ENHANCEMENT: Refactored embed code generation (optimized and more maintainable).
* ENHANCEMENT: Improved player installation messaging
* BUG: Removal of automatic HTML5 skin extraction.  This was causing a serious issue for some reasons.
* BUG: Removed deprecated flashvars for tweetit and facebookit
* BUG: Uninstall now removes directories created by the plugin
* BUG: Player version being saved is no longer required.  Messaging improved to indicate this.

= 1.5.5 =

* BUG: Fixed broken Playlist Manager rendering that occurred after the WordPress 3.3 update.
* BUG: Fixed WordPress notice message about imcorrectly enqueued script file.

= 1.5.4 =

* ENHANCEMENT: jwplayer.js and swfobject.js can now be configured to load on every page or only when there's a jwplayer shortcode.
* BUG: Custom player config xml files are now saved with the XML namespace.
* BUG: html5_file should now behave correctly in the shortcode.

= 1.5.3 =

* ENHANCEMENT: jwplayer.js and swfobject.js now only loaded if a player is embedded on the page
* ENHANCEMENT: New setting on settings page to control whether the plugin uses https URLs when saving a player configuration.
* BUG: The plugin should now correctly activate across a WordPress Network.  This is a workaround for a WordPress bug.

= 1.5.2 =

* BUG: newtube skin is now all lower case.  You should no longer see a warning message during plugin installation.
* BUG: Added a fallback to use the old skin location if the new skin directory structure wasn't updated.

= 1.5.1 =

* BUG: The plugin should now upgrade properly and the shortcode should behave as expected on your site.
* BUG: The shortcode settings should now save properly.
* BUG: Playlist videos without a duration set should no longer incorrectly default to 10 seconds.

= 1.5.0 =

* FEATURE: HTML 5 Skin support! When you update the plugin will automatically unzip the included skins.  (This may take a minute or two).
* FEATURE: HTML 5 visual playlist support!
* FEATURE: Additional Plugins field.  You can now specify extra plugins within the custom player itself.
* FEATURE: New settings page
* ENHANCEMENT: A large number of new skins have been added.
* ENHANCEMENT: Duration is now an option for videos.  Useful for visual playlists.
* ENHANCEMENT: You can now specify where the player is located.
* ENHANCEMENT: Finer control over shortcode behavior
* ENHANCEMENT: Option to hide Insert Button and Duration for images
* ENHANCEMENT: Facebook OpenGraph data is now optional
* ENHANCEMENT: Custom player name added as a CSS class to enclosing player div.  This should help with theming the player.
* BUG: Additional flashvars field should now save as expected.  Removing flashvars should now work.
* BUG: Existing custom players can have their skin set back to the default.
* BUG: Removed deprecated swf only skins.
* BUG: Some code changes to the Playlist Manager.  This should hopefully fix any lingering white screen issues.
* BUG: added is_tag() to shortcode processing
* BUG: Provider should now save properly in the Media Library
* BUG: Playlist location flashvar updated with new name (playlist.location).  Playlist position should now behave as expected.
* BUG: Audio player wrapper now resizes correctly
* BUG: YouTube preview images should repopulate again
* BUG: Script registrations moved to init function
* BUG: Fixed missing variable declaration

= 1.4.3 =

* BUG: Playlist manager should now work correctly in WordPress version 3.2 and greater
* BUG: Fixed improper embed code generation when using the new html5_file and download_file attributes

= 1.4.2 =

* ENHANCEMENT: Use WordPress' built in ssl detection
* BUG: Adjusted category/archive page behavior.  Checkbox now controls whether the jwplayer tag filter runs on the_excerpt() or the_content().

= 1.4.1 = 

* BUG: Tweaked handling of https
* BUG: Fixed a warning message

= 1.4.0 =

* FEATURE: Support for Modes.  You can now specify different files for HTML5 mode.  This can be done in the shortcode (html5_file and download_file) or when editing a piece of content.
* FEATURE: Support for posting to Facebook.  Handled automatically when inserting via the media overlay.  Alternatively you can use two custom fields for the post (jwplayermodule_fb_headers_id and jwplayermodule_fb_headers_config).
* ENHANCEMENT: Rewrite of code handling category behavior.  If checked in the JW Player afdmin section players should now appear when viewing categories.  If unchecked players and code will not show up in category view.
* ENHANCEMENT: wmode is now a configurable option for players.
* ENHANCEMENT: Add more robust URL loading for YouTube XML
* ENHANCEMENT: Better handling of audio controlbar
* ENHANCEMENT: Switched to JSON playlists.  Playlists should now work for HTML5 player and no longer break as a result of theme formatting.
* ENHANCEMENT: The plugin now behaves correctly with HTTPS sites
* ENHANCEMENT: Increased security when setting file permissions.
* BUG: Fixed error in .zip uploading
* BUG: Dynamic error messaging for plugin directories.  This should make it easier to debug network sites.
* BUG: Error handling on youtube api failure

= 1.3.3 =

* FEATURE: Simple event handlers (with anonymous functions) can now be added via the shortcode.
* ENHANCEMENT: Option to toggle player embed on Category pages.
* ENHANCEMENT: Mootools no longer breaks the playlist manager.
* BUG: Added 'none' option for controlbar.
* BUG: Controlbar settings are now correctly applied.
* BUG: Urldecode passed in attributes in case they have been encoded.
* BUG: Fixed image priority.
* BUG: Now decodes the flashvar values to compensate for wordpress encoding.
* BUG: Removed unnecessary check for zip file that was causing errors
* BUG: Removed unnecessary shortcode call

= 1.3.2 =

* BUG: Fixed bug with playlist generation introduced in 1.3.0
* BUG: Fixed issue where thumbnail selector was causing high memory usage
* BUG: Player no longer embeds on category view or in search results.  This should prevent the embed code being visible.
* BUG: Added some checks to prevent null errors in playlist manager.

= 1.3.1 =

* BUG: Fixed bug where the player was sometimes not respecting the dimensions set in the custom configuration.

= 1.3.0 =

* FEATURE: Full support for JW Player 5.3 for Flash and HTML5 added.
* ENHANCEMENT: Player upload now accepts uploading of the player package zip file.
* ENHANCEMENT: Fallback added so that users can properly download or upload player zips even if they don't have the ZipArchive PHP module installed.

= 1.2.5 =

* BUG: Fixed automatic download error due to .zip file name change from 5.3 player release.

= 1.2.4 =

* BUG: Fixed bug in Playlist Manager.  Would not remember selected playlist when paging through media.
* ENHANCEMENT: Improved error messaging.  It should be very clear what is preventing the plugin from working correctly.
* BUG: SWFObject code inserted on a single line.  Should theme formatting from breaking the javascript.

= 1.2.3 =

* ENHANCEMENT: Permission error handling tweaks
* BUG: Fix to JW Player plugins settings not being saved

= 1.2.2 =
* BUG: Additional adjustment to permissions.
* BUG: Reduction of archive size to try and avoid zip errors.

= 1.2.1 =
* BUG: File permission issues should now be fixed.
* BUG: Problem where uploaded player was incorrectly marked as invalid should now be fixed.
* BUG: Automated player download problem should now be fixed.

= 1.2.0 =
* FEATURE: Installation of the JW Player is now handled entirely within the plugin.  Simply click a button to install the JW PLayer.  Additionally, upload of licensed players can be done through the plugin as well.
* FEATURE: Content Aware Embed - when embedding audio without a thumbnail, only the controlbar will show
* FEATURE: RTMP Media Library support - can add RTMP streams to the media library and specify streamer and file at the media level. (plugin will make best guess at streamer and file on import)
* FEATURE: Should now be able to use the shortcode in widgets
* FEATURE: Will use the video tag (for .mp4) or youtube embed (for youtube videos) when your blog is viewed on an iPod, iPhone or iPad.
* ENHANCEMENT: Player and configs relocated to the WordPress uploads directly.  This should minimize file permission issues going forward.
* ENHANCEMENT: Image Embedding now officially supported - Can specify the duration flashvar on image media.
* ENHANCEMENT: Audio embedding now officially supported - Can specify the thumbnail flashvar on audio media
* ENHANCEMENT: Arbitrary flashvar field added - useful when using custom plugins and you need to specify flashvars that aren't listed in the JW Player Setup
* ENHANCEMENT: Plugin now uses .zip skins.  Number of available skins has been greatly expanded.
* ENHANCEMENT: Top Nav Bar has been added to the JW Player Setup wizard interface - should make specific edits to your players much easier now
* ENHANCEMENT: Significant redesign of the Playlist Manager - Improved usability and better handling of large media libraries.
* BUG: spaces removed from plugin list - should address unexpected plugin behavior (eg. LTAS not working correctly).
* BUG: Provider/Streamer flashvars have been added back to JW Player Setup
* BUG: Image editing should no longer fail while the plugin is active

= 1.1.2 =
* reimplemented path generation and usage
* Fixed links to longtailvideo.com
* Added links to plugin pages for plugins

= 1.1.1 =
* Improved path resolution.

= 1.1.0 =
* Fixes path resolution of player.swf on the LAMP stack.

= 1.0.0 =
* Initial release of the JW Player Plugin for WordPress
