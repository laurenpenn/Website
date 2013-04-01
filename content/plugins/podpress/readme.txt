=== podPress ===
Tags: post, podcast, podcasting, audio, video, admin, feed, widget, stats, statistics, iTunes, mp3, m4a, ogg, ogv, xspf
Contributors: seek3r, macx, iscifi, ntm
Donate link: http://www.manvswebapp.com/podpress/podpress-donors
Requires at least: 2.3
Tested up to: 3.5.1
Stable Tag: 8.8.10.13
License: GPLv2 or later

A plugin for Podcasters using WordPress.


== Description ==
podPress adds a lot of features designed to make WordPress the ideal platform for hosting a podcast.

podPress 8.8.10.17 includes a lot of bug fixes and some new festures like a Post Type Filter for the podPress Feeds and customization settings for tag feeds. You can read more about this and the other bug fixes of this version in the [Changelog](http://wordpress.org/extend/plugins/podpress/changelog/) and pay attention to the [Upgrade](http://wordpress.org/extend/plugins/podpress/other_notes/) section.

Features:

* Full featured and automatic feed generation (RSS2, iTunes and ATOM and XSPF playlist)
* Podcast Download stats, with graphs.
* View MP3 files ID3 tags when your Posting
* Control over where the player will display within your post and what it will look like.
* Support for various formats, including Video Podcasting
* Supports unlimited number of media files.
* Automatic Media player for MP3, OGG, OGV, MP4, M4A, M4V, MOV, QT, FLV, ASF, WMA, WMV, AVI, RM, and more, with inline and Popup Window support.
* Supports also PDF and EPUB files
* Preview image for videos
* Support for seperate Category podcasts
* Support for Premium Content (Pay Only)


Available in these languages:

* English
* German


If you discover a problem with this plugin, please report it in the [WP.org forum](http://wordpress.org/tags/podpress?forum_id=10) and add the tag "podpress" to your post.<br />
Thank you to all who have reported bugs and have made improvement proposals. Special thanks to [Ray (raymer)](http://wordpress.org/support/profile/68146) who did extensive tests with the development versions since v8.8.5 and helped a lot! 

This plugin is currently maintained by [Tim Berger (ntm)](http://profiles.wordpress.org/ntm/).<br />Latest news and announcements regarding podPress on Twitter: [podpress2010](http://twitter.com/podPress2010)

If somebody else is also interested in maintaining podPress while the main developers are out, please send an email to admin [at] laptoptips.ca.

For further information you may also visit the website
http://www.manvswebapp.com/podpress/changelog


== Changelog ==

Please, read the [Upgrade instructions](http://wordpress.org/extend/plugins/podpress/other_notes/).

= v8.8.10.17 / v8.8.10.16 / v8.8.10.15 / v8.8.10.14 =
* bug fix: now, the ATOM feeds will include the podPress version number according to your settings on the general settings page of podPress (like it has worked in the RSS feeds)
* new: It is now possible to alter the default size for the video and enhanced audio player (preview). You may change it on the Player Settings page.
* change of the last character of the Podtrac URL
* bug fix for a problem with the appearance of attached episodes in the podPress post editor box
* bug fixes for problems with the Premium Feed authentication method Digest
* minor fixes for the JS at the editor page (m4a preview player size)
* new iTunes:image size is 1400x1400 pixels / bug fix: for a warning which occured with PHP 5.4 (Thank you [enderandrew](http://wordpress.org/support/profile/enderandrew) for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-php-54-warning).)
* new: option which makes the 1PixelOut player to stay always open
* new: option which lets the 1PixelOut player show the remaining track time rather than the elapsed time
* If a request of a blog page is a request via HTTPS then podPress will load icons and default images with the related URL scheme (https:// instead of http://).
* podPress uses an improved method to determine the location of the wp-config.php during some AJAX actions (e.g. related to file size and duration detection)
* The detection of the duration and the ID3 tags of a media file will work more reliable for all media files which are stored in a sub folder of the blog
* bug fix: podPress did not display the player and other elements in password protected posts (Thank you [Stariel](http://wordpress.org/support/profile/stariel) for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-podpress-no-longer-working).)
* version for debbuging a premium feed problem (podpress_premium_functions.php does also contain podPress_var_dump() calls now)
* minor bug fixes in the podpress_feed_functions.php file and further modifications in the podpress_premium_functions.php file
* Bitlove scripts for podPress - the folder /optional_files contains now a mini plugin which loads certain Javascript files of Bitlove.org
* podPress uses cURL by default (if it is available) to retrieve file size, duration and ID3 informations
* the folder /optional_files contains now a further mini plugin which makes it possible to use podPress and the plugin WP e-Commerce at the same time
* new: option which makes it possible to include the code of the podPress post elements into the content:encoded sections
* compatibility issues with WP 3.5 fixed (Thank you [Rafael](http://wordpress.org/support/profile/rfischmann) for [reporting this problem](http://wordpress.org/support/topic/podpress-incompatible-with-wordpress-35) and thank you [Bridger](http://wordpress.org/support/profile/bridger) for [reporting this problem](http://wordpress.org/support/topic/plugin-podpress-category-casting-not-working-and-custom-categories).)
* podPress Tag Casting - is (like podPress Category Casting) the possibility to customize syndication feeds which contain only posts with a certain tag. Usage: Posts > Tags > Edit Tag > section podPress Tag Casting 
* minor rearrangements and other modifications of the feed settings pages / .css files are now in a separate folder
* new: option for podPress Feeds to limit the max. amount of posts per feed
* new: option for podPress Feeds: now it is possible to make to inlcude posts of custom post types into such a feed or to make a podPress Feed basically a custom post type feed which contains only posts of one or multiple custom post types.
* bug fix - update for the 1PixelOut player which solved a XSS vulnerability
* 8.8.10.16 bug fix: for a problem in a part of the upgrade procedures (Thank you all for [reporting the problem](http://wordpress.org/support/topic/upgrade-not-complete).)
* 8.8.10.17 bug fix: for a problem with the podPress has added the custom feeds and regenerated the rewrite_rules (Thank you MCM and funcshn@funcshn.com for reporting the problem [1](http://wordpress.org/support/topic/custom-post-types-pages-no-longer-working), [2](http://wordpress.org/support/topic/custom-posts-broke).)

= v8.8.10.13 =
* bug fix: for a problem which has prevented WP 3.3.x from saving the sidebar widget settings. Rearranging widgets or modifying the widget settings should be possible again. (Thank you for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-broken-widgets-sitewide).
* new: It is now possible the determine the number of rows in almost all statistic tables and to save further appearance options.
* bug fix: for a problem with the code of the <object> of m4v, mp4, mov, mpg and other video files which has lead to problems with the appearance of the control elements of some browser plugins.
* minor corrections for a better output of multiple of custom feed elements like itunes:subtitle of category feeds
* minor CSS adjustments for the statistic result pages

= v8.8.10.12 =
*  This is a bug fix version for 8.8.10.11. I have forgotten to remove some code which has helped me to debug some things but which brought you trouble. I'm very sorry for my mistake! (Thank you [c365954](http://wordpress.org/support/profile/c365954) and [themacmommy](http://wordpress.org/support/profile/themacmommy) for [reporting the problem](http://wordpress.org/support/topic/cannot-download-or-play-podcast-via-player).)

= v8.8.10.11 =
* bug fix: It solves a problem which could occur since 8.8.10.8 on servers with a PHP installation which does not include the [Multibyte String extension (mbstring)](http://www.php.net/manual/en/book.mbstring.php) (functions like `mb_substr()` or `mb_strrpos()` are not usable without this extension). If your blog is on such server then this will help you - but probably only if you write posts in English. But if your blog is on such server and if you write posts in a different language which contains for instance characters with diacritical marks then you should make sure that the PHP on the server of your blog provides such functions. These functions are also used by WP and other plugins when it is necessary to process non-English text. If you have no problems with the RSS feed while podPress 8.8.10.10 is running then this should not concern you (mbstring is probably available). You may find out whether the PHP on the server of your blog supports mbstring functions or not with the help of a different plugin e.g. [WordPress System Health](http://wordpress.org/extend/plugins/wp-system-health) (look for "mbstring" in System > Server Setup (> show detailed) >  Loaded Extensions) or [PHP Server Info](http://wordpress.org/extend/plugins/php-server-info) (look for "mbstring") (Thank you [ptobia](http://wordpress.org/support/profile/ptobia) for [reporting the problem](http://wordpress.org/support/topic/podpress-881010-broken-rss-feed).)
* new: This is for all who like to define custom CSS for the podPress output: All the elements in the line below the player have now an HTML attribute `class`. These attributes consist at least of a class name and the class name which depends on the type of the media file. For instance the Download link has the class names: `podpress_downloadlink podpress_downloadlink_audio_mp3` if the the file is a MP3 audio file. podPress uses audio, video, ebook, embed and misc as group type and all the file types which you can choose from the list in the podPress box below the post editor. For instace for a PDF file the file specific class would have the suffix `_ebook_pdf` (Thank you [david hickox](http://wordpress.org/support/profile/david-hickox) for [the idea](http://wordpress.org/support/topic/adding-classes-to-the-different-display-links).)

= v8.8.10.10 =
* bug fix: it corrects a problem with the function which limits the characters of several RSS tags. (Thank you [IAmediaworks](http://wordpress.org/support/profile/iamediaworks) for [reporting the problem](http://wordpress.org/support/topic/bugs-wp32podpress88108).)

= v8.8.10.9 =
* bug fix: it corrects a problem with the function which limits the characters of several RSS tags. (Thank you [johncsnider](http://wordpress.org/support/profile/johncsnider) for [reporting the problem](http://wordpress.org/support/topic/podcast-feed-404).)
* bug fix: In 8.8.10.8 the Category Casting settings did not work in combination with the default Permalink scheme. (Thank you [ip_tara](http://wordpress.org/support/profile/ip_tara) for [reporting the problem](http://wordpress.org/support/topic/bugs-wp32podpress88108).)
* bug fix: The Reading and General Settings link on the Feed Settings page of podPress are working now correctly. (Thank you [Madsex](http://wordpress.org/support/profile/madsex) for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-podpress-problem-with-options-generalphp-and-options-readingphp).)
* bug fix: for a problem with the iTunes:category setting which could occur after switching the language of the blog after saving the feed settings
* new: podpress_post_scriptblock is a new [filter hook](http://codex.wordpress.org/Plugin_API#Filters) of podPress. Read more about it in the [Other Notes](http://wordpress.org/extend/plugins/podpress/other_notes/) section.
* CSS adjustment for WP 3.2 (background-color of .podpress_editor_table has changed)

= v8.8.10.8 =
**Notice:** Upgrading to this version from version 8.8.9.2 or older will rename the podPress `meta_key`s in the postmeta data base table of your blog (see bug fixes). Downgrading to a previous version is only possible if you reverse these changes.

* bug fix: an upgrade to this version will remove an old deprecated option which could lead to the situation that podPress would add "Download Standard Podcast" somewhere in the iTunes:summary or iTunes:subtitle of post.  (Thank you [IAmediaworks](http://wordpress.org/support/profile/iamediaworks) for [reporting the problem](http://wordpress.org/support/topic/upgrade-added-download-standard-podcast-to-itunes-episode-description).)
* bug fix: The `<link>` of the RSS image of the `<channel>` will include the 'Site address (URI)' set in Settings > General. (Thank you [lopo](http://wordpress.org/support/profile/lopo) for [reporting the problem and making suggestions](http://wordpress.org/support/topic/feed-validation-warning-image-link-doesnt-match-channel-link-wordaround).)
* bug fix: If the option "Aggressively Protect the news feeds" was set to "Yes" then podPress has removed the formatting from the post content (`<content:encoded>`) in the feeds. Because this option is deprecated, it is removed now. (Thank you [sephage](http://wordpress.org/support/profile/sephage) for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-podpress-88107-breaks-rss-formatting).)
* bug fix: Fix for a bug in the appearance of the iTunes:Keywords option in the podPress box below the post editor which occured when the new option "Use the tags of the post" was active. 
* bug fix: The "Disable Player:" and "Disable Preview Player:" in the podPress box below the post editor are working again. (Thank you Philip for mentioning the problem.)
* bug fix: The result of the duration auto detection could have a leading white space under some circumstances which is not desirable. (Thank you [Puck](http://wordpress.org/support/profile/puck) for [reporting the problem and making suggestions](http://wordpress.org/support/topic/plugin-podpress-podcast-duration-lostnot-being-saved-workaround-provided).)
* bug fix: Chrome/Chromium >= v10 displayed the HTML5 audio player depending on certain CSS definitions of the theme without displaying the control elements properly. This podPress version includes a workaround/bug fix for this problem. (Thank you [Twanislas](http://wordpress.org/support/profile/twanislas) for [reporting the problem](http://wordpress.org/support/topic/podpress-suggestions).)
* change: The options for the feed title of a category feed are re-arranged. Further it is possible to use the Site Title as the title of such a feed.
* This version includes also some security improvements.

= v8.8.10.7 =
**Notice:** Upgrading to this version from version 8.8.9.2 or older will rename the podPress `meta_key`s in the postmeta data base table of your blog (see bug fixes). Downgrading to a previous version is only possible if you reverse these changes.

* bug fix: the function which limits the number of characters for several feed elements is multibyte-character-safe and it does not cut through htmlentities anymore (Thank you [franky1029](http://wordpress.org/support/profile/franky1029), [welshes99](http://wordpress.org/support/profile/welshes99) and [drezac](http://wordpress.org/support/profile/drezac) for [reporting the problem](http://wordpress.org/support/topic/podpress-player-doesnt-show-in-post).)
* bug fix: the HTML5 player appears also in IE9 (JS error fixed) (Thank you [dael3](http://wordpress.org/support/profile/dael3) for [reporting the problem](http://wordpress.org/support/topic/no-player).)
* bug fix: the "Validate your feed" button at the Category Casting settings works again (Thank you [meetwp](http://wordpress.org/support/profile/meetwp) for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-category-casting-category-title-appears-twice-in-feed-title).)
* new: It is now possible to "Use the tags of the post" as itunes:keywords. There is a new option with this name in the Post specific settings for iTunes (below the post editor). (Suggested by [Twanislas](http://wordpress.org/support/profile/twanislas) in [this thread](http://wordpress.org/support/topic/podpress-suggestions))
* new: max. 12 itunes:keywords possible

= v8.8.10.6 =
**Notice:** Upgrading to this version from version 8.8.9.2 or older will rename the podPress `meta_key`s in the postmeta data base table of your blog (see bug fixes). Downgrading to a previous version is only possible if you reverse these changes.

* bug fix: a further bug fix for the feed modifying functions. podPress is using now the WP-own list of HTML entities and their numeric equivalents (ent2ncr();) to transform non-ASCII characters in the additional feed elements into RSS-feed-save text. (Thank you [Aaron Frankel](http://wordpress.org/support/profile/aaronfrankel) for [reporting the problem](http://wordpress.org/support/topic/88105-xml-feed-still-down).)

= v8.8.10.5 =
**Notice:** Upgrading to this version from version 8.8.9.2 or older will rename the podPress `meta_key`s in the postmeta data base table of your blog (see bug fixes). Downgrading to a previous version is only possible if you reverse these changes.

* bug fix: a further bug fix for the feed modifying functions
* bug fix: podPresshas displayed under some circumstances the player and links twice on single post views

= v8.8.10.4 =
**Notice:** Upgrading to this version from version 8.8.9.2 or older will rename the podPress `meta_key`s in the postmeta data base table of your blog (see bug fixes). Downgrading to a previous version is only possible if you reverse these changes.

* bug fix: for a problem with not escaped ampersands in iTunes:category names which appeared in 8.8.10.3 (Thank you [BibleStudyRadio](http://wordpress.org/support/profile/biblestudyradio), [UULosAlamos Webmaster](http://wordpress.org/support/profile/uulosalamos-webmaster),  [AJRitz](http://wordpress.org/support/profile/ajritz) and [raymer](http://wordpress.org/support/profile/raymer) for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-itune-feed-incorrect).)
* bug fix: The iTunes:category settings for the podPress Feeds work now. Depending on your previous iTunes:category settings it might be necessary to adjust them now. The podPress Feeds will no longer use the comparable settings of the main feed settings.

= v8.8.10.3 =
**Notice:** Upgrading to this version from version 8.8.9.2 or older will rename the podPress `meta_key`s in the postmeta data base table of your blog (see bug fixes). Downgrading to a previous version is only possible if you reverse these changes.

* The 3rd party statistic options are available again by default. If you have used them before then please control the related settings after the upgrade.
* bug fix: podPress statistics counts HTML5 play events correct, now. It counts when the "onplaying" event happens (when someone clicks on play and not during when the the HTML5 player buffers (preload) the file). The download numbers with 8.8.10 - 8.8.10.2 are probably to high depending on how many of your listners have used Chrome/Chromium (or Safari desktop versions) to listen your podcasts.
* bug fix: podPress will use the excerpt of a post if there is one as the iTunes:summary and 255 characters of it as iTunes:subtitle. If a post has no excerpt then podPress takes automatically the post content (or the first parts of it). In older versions podPress has ignored the excerpts. Furthermore podPress will respect the password protected posts and adds only the summary, subtitle and enclosure if there is no password protection or if the user has already entered the password for the post. (Thank you [MedHead](http://wordpress.org/support/profile/medhead) for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-itunes-summary-send-only-excerpt).)
* bug fix: the iTunes:block options in the podPress Feeds section will show the correct status / current setting (so far it displayed always 'No' regardless of the setting) (Thank you [maydaytothemoon](http://wordpress.org/support/profile/maydaytothemoon) for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-podpress-settingspreferences-are-not-being-saved).)
* bug fix: Now it is possible to deselect the iTunes feed button (Feed Buttons Widget). (Thank you [maydaytothemoon](http://wordpress.org/support/profile/maydaytothemoon) for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-podpress-settingspreferences-are-not-being-saved).)
* bug fix: podPress checks whether or the stats tables exist and creates them if necessary when someone activates the stats feature. The db queries do not long include a specific table type. That prevents problems in cases in which the type was not the one which was hardcoded in previous versions of podPress.
* podPress version number appears only if you want it to in the footer or the Feeds of your blog. It is also possible show only the name of the plugin without the version number.
* The auto detection of the meta information of the media files can retrieve the cover art of m4a files.
* on a first installation of podPress, only the podPress Feed with the slug "podcast" will be active by default

= v8.8.10.2 =
**Notice:** Upgrading to this version from version 8.8.9.2 or older will rename the podPress `meta_key`s in the postmeta data base table of your blog (see bug fixes). Downgrading to a previous version is only possible if you reverse these changes.

* bug fix: podPress displays the download number for files with white spaces in their names in the "Downloads Per Media File" table and in the line below the player (Thank you [SmartPeoplePod](http://wordpress.org/support/profile/smartpeoplepod) for [reporting the problem](http://wordpress.org/support/topic/plugin-podpress-podpress-stats-per-post-are-gone-help).)
* bug fix: ebooks will have no longer a "Play in Popup" link

= v8.8.10.1 =
**Notice:** Upgrading to this version from version 8.8.9.2 or older will rename the podPress `meta_key`s in the postmeta data base table of your blog (see bug fixes). Downgrading to a previous version is only possible if you reverse these changes.

* bug fix: the jQuery and jQueryUI libraries are getting loaded correctly, regardless whether the file system on the server is case sensitive or not

= v8.8.10 =
**Notice:** Upgrading to this version will rename the podPress `meta_key`s in the postmeta data base table of your blog (see bug fixes). Downgrading to a previous version is only possible if you reverse these changes.

* a new option at the General Settings page of podPress to display the file size of the media files along with the duration in the line below the player (or player preview)
* new options at the General Settings page of podPress which make it possible to customize the displayed duration value
* podPress uses the HTML 5 `<audio>` tag (instead of the 1PixelOut and Podango player) for MP3 files if the browser engine of a visitor is Webkit 525.x or newer (e.g. the current versions of Safari incl. the variants on iPhones, iPads and iPod Touch and of Chrome) or if it is an Internet Explorer 9.0 or newer. (see Player Settings page)
* a new option at the General Settings page of podPress which can be used to expand the blog-own search to search also in the podcast episode titles and URLs
* a new option at the General Settings page of podPress which will make it possible to display the podPress meta box also below the post editor box of posts of custom post types. That makes it possible to add podcasts to post of custom post types. (this requires at least WP 2.9)
* a new option for the XSPF widget. Now, it is possible to customize the playlist entires. 
* upgrade of the [XSPF Jukebox](http://blog.lacymorrow.com/projects/xspf-jukebox/) player to version 5.9.6
* the list of the languages for the RSS language option consists now of the [ISO 639](http://www.loc.gov/standards/iso639-2/) languages with a two-letter code and the [RSS languages](http://www.rssboard.org/rss-language-codes#table) (Thanks to [Wiziris](http://wordpress.org/support/profile/wiziris) who has [reported](http://wordpress.org/support/topic/plugin-podpress-pinging-no-longer-works) a missing language.)
* a possibility to set (and overwrite) a few podPress settings via PHP constants in a config file which should be placed in a separate folder (by default: /wp-content/plugins/podpress_options). For instance it is possible to hide all Premium Feed options in the main blog and in all sub blogs. For more information look into the podpress_config-sample.php file.
* support for [EPUB](http://en.wikipedia.org/wiki/EPUB) files
* upgrade to [getID3](http://www.getid3.org) v1.8.5 (1.8.5-20110218)
* Apple has withdrawn the service which allowed it to update the list of the episodes in the iTunes Store manually (independly of the automatic update interval). That is why the "Ping iTunes Update" buttons are removed from the Feed/iTunes settings page of podPress. (Thanks to [bstritesky](http://wordpress.org/support/profile/bstritesky) who has [made me aware of this problem](http://wordpress.org/support/topic/plugin-podpress-pinging-no-longer-works).)
* bug fix: podPress uses the HTML 5 `<audio>` and `<video>` tags for OGG Audio and Video only if the browser engine of a visitor is Gecko 1.9.1 or newer (e.g. FireFox) or Presto 2.5 or newer (e.g. Opera)
* bug fix: podPress uses different `meta_key`s for the meta data of the media files and the post specific settings. `podPressMedia` becomes `_podPressMedia` and `podPressPostSpecific` becomes `_podPressPostSpecific`. podPress is going to rename all existing values automatically. The background is that there are function like `the_meta();` which display custom field data sets of a post. These podPress meta data sets and the custom field data sets are stored in the same db table and for instance the_meta(); takes all data sets but the ones with an underscore `_` character at the beginning of the `meta_key` and displays them as custom field name and value. But podPressMedia and podPressPostSpecific are no custom field data sets. (Thanks to [cozbaldwin](http://wordpress.org/support/profile/cozbaldwin) who has [made me aware of this problem](http://wordpress.org/support/topic/podpresspostspecific-code-showing-in-post-footer).)
* bug fix: the RSS category appears again in the RSS Feeds
* bug fix: for a problem with the settings update messsage on the settings pages of podPress (occured in older WP versions)
* bug fix: the list of posts at the "Downloads Per Post" (Stat Method: Full/Full+) shows only posts with downloaded media files (and does no longer show empty rows if a posts has been deleted)
* bug fix: if you choose Category Name as the Feed title in the podPress Category Casting options then the feed title will be only the name of the category
* bug fix: if the post specific itunes setting for the keywords is "WordPress Categories" and a post is in categories with names which consists of more than one word then podPress will use category names which consist only of one word
* bug fix: for Javascript error at the Feed/iTunes Settings page of podPress
* minimal required WP version: 2.2 - recommended WP version: at least 2.9 to be able to use all features
* 2 new CSS classes `.podpress_mediafile_title` and `.podpress_mediafile_dursize` for the media file title and the duration/file size section in the line below the player
* 3 new filter hooks (see the "Filter Hooks of podPress" section at the [Other Notes](http://wordpress.org/extend/plugins/podpress/other_notes/))

= v8.8.9.2 =
* until this version most parts of podPress were restricted to users with the [user level](http://codex.wordpress.org/Roles_and_Capabilities#User_Levels) 7. But these user levels are deprecated for WP 3.0 and newer versions. From now on the file size, duration and ID tag auto detection are available for users which are allowed to [edit_posts](http://codex.wordpress.org/Roles_and_Capabilities#Contributor) and the settings pages of podPress as well as the detailed statistics pages are available for user which are allowed to [manage_categories](http://codex.wordpress.org/Roles_and_Capabilities#Editor).
* a new option at the General Settings page of podPress to determine whether the podPress elements (player or player preview, download link, icon, etc.) should be visible in the content sections and the excerpts of posts and pages or only one of these sections.
* a new option in the podPress Feeds section of the Feed/iTunes Settings page: you can choose to add a link of each active podPress Feed to the `<head>` sections of your blog. The users will be able to subribe to these Feeds via the Feed icon in the address bar of their browser.
* the plugin is able to use Curl procedures to detect the file size of a remote media file or to download a remote file to detect the duration and other ID3 data (if fsockopen() is not available or fails)
* with this version, the keyword clean up procedure allows only separate words as keywords
* podPress does no longer add filters to the hooks get_attached_file and wp_get_attachment_metadata
* bug fix: this version contains also some bug fixes which make it possible to use custom skin files for the XSPF player again.
* bug fix: two changes in the CSS of the frontend elements of podPress (`.podpress_playerspace` and `.podPress_downloadlinks`)
* bug fix: for several issues with escaped quotation marks
* bug fix: for a problem with duration strings of the scheme mmm:ss or hhh:mm:ss. Part of this bug fix is also a revised millisecondstostr() function. 
* CSS modifications in preparation of WP 3.1 (podPress-own CSS classes for warning and error messages)

= v8.8.9.1 =
* the option "Bypass the "Included in:" selection for this Feed" works now as intended and as the name it suggests. Posts in RSS Feeds will contain the first of their media file as an enclosure. Posts in ATOM Feeds will contain the all of their media files as enclosures.
* there is also a new notice for the behaviour of the file type filters. They filter the file types but they also bypass the "Included in:" selection. That is new for the ATOM Feeds.
* minor CSS modifications

= v8.8.9 =
* a Category Filter for the podPress Feeds - You can control the Feed content by choosing one or more categories of your posts.
* an option to bypass "Included in" selection for a certain podPress Feed - You can choose in the podPress box below the editor which file should be included in which Feed. This setting might be useful to select the media file which should be included e.g. in the main RSS Feed but now you can bypass it and the first media file with the right file type will be the enclosure of a post in the (RSS) Feed. A post in an ATOM Feed will contain all attached media files as enclosures.
* an option to include not only posts with podPress attachment in podPress (and CategoryCasting) Feeds while the file type filter and the category filter is not in use
* podPress Feeds settings: if you leave the Feed Name field empty and you save the Feed/iTunes settings then you delete the settings of this podPress Feed.
* the plugin gets initiated via the Action Hooks "plugins_loaded" and "init" (internal process)
* the plugin will show error messages if saving the plugins settings went wrong
* minor CSS modifications
* minor improvement of the file type filter
* bug fix: for a problem with the upgrade function (see this [forum thread](http://wordpress.org/support/topic/podpress-fatal-error-on-activate-podpress_upgradecategory))

= v8.8.8.5 =
* bug fix: for a problem which occurred in some blogs after an WordPress upgrade (see e.g. this [WP forum thread](http://wordpress.org/support/topic/wordpres-304-not-compatible-with-podpress))
* bug fix: This version contains several modifications of the code which makes the jQuery UI Accordion effect at the widgets settings page. These modifictions solve problems with this effect which occured during the usage of podPress with WP 2.9 and WP 2.8.
* bug fix: The file type filter works case insensitive now.
* correction of some misspelling mistakes

= v8.8.8.4 =
* bug fix: The file type filter works now as intended: For instance, if you have posts with a mp3 and a m4a file and you have a Feed for the posts with mp3 files and one for posts with m4a files then such post with files of both types will show up in both Feeds. In the mp3-Feed with the mp3 file as the enclosure and in the m4a-Feed with the m4a file as the enclosure. 
* works parallel to the [xLanguage](http://wordpress.org/extend/plugins/xlanguage/) plugin ( Read on in this [forum thread](http://wordpress.org/support/topic/plugin-podpress-podpress-and-xlanguage-plugin-conflict) )

= v8.8.8.3 =
* bug fix: small bug fix for the upgrade mechanism

= v8.8.8.2 =
* now, podPress is going to take over the meta information of the Feed with the slug name "podcast" during a direct upgrade from podPress v8.8.6.3 or an older version to v8.8.8.2. But you should also control all the Feed Settings after the upgrade.
* bug fix: a further fix for the *Always before the <!- More -> tag* option
* bug fix: now, the filter filters not only the posts with certain podPress attachments, it filters also the enclosures in the feed. (If you choose .m4a files the for instance a post with a m4a file and a mp3 file will appear in the Feed but only the m4a file will be visible as an enclosure of this post.)

= v8.8.8.1 =
* bug fix: the feed with the name "podcast" contain only posts with podPress podcasts again
* bug fix: the category Feeds which have no active CategoryCasting show the right meta information again
* new option at the General Settings page of podPress to regulate the usage of the hyper link attribute "target" in the HTML output of podPress

= v8.8.8 =
* fully customizable Podcast Feeds with file type filter | NOTICE: Please, read the [Upgrade instructions](http://wordpress.org/extend/plugins/podpress/other_notes/). 
* The Feed Buttons widget was modified accordingly to these new capabilities and has now the possibility to define custom button URLs and alternative Feed URLs (e.g for Feedburner URLs). (The presentation of these new options is made with jQuery UI elements.) NOTICE: Please, check the Feed/iTunes and the widget settings of podPress after the upgrade to this version.
* a new feed button for the Premium Feed
* multiple Feed Buttons and XSPF player widgets in combination with WP 2.8+ | NOTICE: after installing v8.8.8 due to the massive modifications on both podPress widgets you will need to add the widgets again to your sidebars. The widgets will try to take over the old settings as far as possible.
* the customization of the XSPF player is different now but hopefully more comfortable for multi site blogs. Please, read the instructions in the new podpress_xspf_config-sample.php file an in the widgets settings.
* CategoryCasting Feeds have a file type filter
* possibility to add the CategoryCasting Feed to the sidebar via the Feed Buttons widget
* option to deactivate the statistic dashboard widget of podPress
* the color maps for the player customization is now converted to numbers instead of static English descriptions
* all image files which are 80x15 buttons for Feeds have a new name scheme: feed_button-{name of the feed}.png
* redesign of the Feed/iTunes Settings page
* a new filter hook *podpress_post_content* for the complete podPress section of posts with podPress attachment (for the sections `<!-- Begin: podPress --> ... <!-- End: podPress -->`)
* upgrade to [getID3](http://www.getid3.org) v1.8.1 (1.8.1-20101125)
* bug fix: for a JS problem with the new input field ids of the category form in WP 3.x
* bug fix: minor fixes for the CategoryCasting feeds
* bug fix: non-ASCII characters in the ID3-tags will be displayed properly
* bug fix: improved support for media files with names with non-ASCII characters and white spaces
* bug fix: the default video player preview image setting from player settings page works as the preview image proposal for video player previews of new posts
* bug fix: whether podPress shows the player elements while only the short version of a post is visible or not does not longer depend on a certain CSS class of the `<!--more-->` element (Thanks to [dayanayfreddy](http://wordpress.org/support/profile/dayanayfreddy) for the [bug report and the patch](http://wordpress.org/support/topic/plugin-podpress-bugfix-post-short-version-versiones-cortas-de-entradas))
* bug fix: although it is very recommended to use podPress in combination with the most current WP version, this version includes several bug fixes for problems with older WP versions.

(8.8.7 was only available as beta version resp. as preview for some features of 8.8.8)

= v8.8.6.3 =
* bug fix: for the "Play in Popup" feature which did not work for non-mp3 files or while the podango player was the mp3 player (in 8.8.6 - 8.8.6.2)

= v8.8.6.2 =
* possibility to define an URL of an custom XSPF playlist in the settings of the widget
* possibility to choose between an unsigned and a signed version of the Cortado player (Site Admin > podPress > player settings)
* bug fix: for the problem(s) with described in the WP.org forum thread "Podpress won't work on single posts" (http://wordpress.org/support/topic/podpress-wont-work-on-single-posts)

= v8.8.6.1 =
* bug fix: contains a fix for a problem with the URL sanitation while the "Absolute path of the media files directory" feature is in use (Thanks to mocinoz who has [reported this problem](http://wordpress.org/support/topic/podpress-886-breaks-file-selection))

= v8.8.6 =
* the process the 1PixelOut player will integrated into the posts is now as it is described in the [documentation of the player](http://wpaudioplayer.com/standalone). One of the advantages that the player software inserts now an object element which is individually assembled for the browser that the visitor/listener uses. This is an further effort to get a cross-browser compatibility of the mp3 player object. Furthermore it will bring back the functionality that a player stops playing if you start another one in the same browser window.
* podPress includes the Cortado Player 0.6.0 for Ogg Vorbis audio (.ogg) and Ogg Theora video (.ogv) files. Ogg files are going to be embedded with the HTML5 audio and video element. The Cortado player is the fallback option.
* refreshed statistic settings section on the general settings page of podPress (expanded explanations for all the options, all explanation are now visible all the time if statistic feature is enabled)
* bug fix: The Quick Counts statistic table (stat logging: Counts Only) shows now by default the Downloads per Media File and if desired the same data sorted by the date (resp. ID) of the posts in which the media files were last published.
* PODPRESS_DEBUG_LOG = FALSE is a default setting again. Now, It is also possible to check the status of this constant at the general settings page of podPress.
* bug fix: further CSS modifications for the (video) player preview
* bug fix: podPress loads the CategoryCasting settings in WP 3.0.x
* bug fix: the update check of podPress 8.8.5.x has disturbed the dashboard in WP 2.7
* a link in the popup player window which opens the single post view of the post with the media file
* a new option on the general settings page of podPress to Show/Hide the "Hide Player"/"Play Now" link
* a possibility to change the playlist URL of the XSPF player: define the constant PODPRESS_CUSTOM_XSPF_URL with your custom URL. At the beginning of the file podpress.php is a prepared line and a short how-to.
* localization added: German

= v8.8.5.3 =
* adjustments in the podpress.js file for a better cross-browser compatibility of the mp3 player object

= v8.8.5.2 =
* 4 new option for the 1PixelOut player: "Initial Volume Level", "Buffering Time", "Use a cross-domain policy file" and "Use custom values for titles and artists instead of the ID3 data".  "Use a cross-domain policy file" can help to display the ID3 information in the player if the mp3 files are not located on the same domain as your blog (and the player shows "Track #1"). With "Use custom values for titles and artists instead of the ID3 data", you can order the 1PixelOut player to use custom values of artist und title instead of the values from the ID3 tags.
* adjustments of the structure of the mp3 player object element
* on an upgrade from 8.8.4 or older version to 8.8.5.2 the color settings for the player will not be overwritten anymore. Despite this new behaviour you should definitely have a look to the player settings. There are new options available.
* improved file type filter for the Endhanced Podcast and Torrent feed
* misc_torrent_button.png has the design of the other "_button.png" - files
* minor CSS adjustments on the admin pages

= v8.8.5.1 =
* the option "Disable Video Preview" on the player settings page of podPress works again: When checked the preview player for video files will be invisible.

= v8.8.5 =
* the podPress_downloadFile function is respecting time limits
* the htmlentities-decoding for the skin files of the XSPF player works with the blogs charset, now

= v8.8.5 RC 3.1 =
* the log procedure PODPRESS_DEBUG_LOG writes log entries with timestamp

= v8.8.5 RC 3 =
* removed the automatic update of the XSPF skin file on reactivation because it worked not with "Network Activate" in a multi site blog. But it is possible that such a functionality will be in a future version again. (in podpress.php). Please, save the player width manually on the widgets page of your blog after an upgrade.
* additional error message in case that the user has not the permission to use chmod()  (for the XSPF skin file)

= v8.8.5 RC 2.3 =
* correct import of the global $wp_version (in podpress.php)
* modified the way podPress adds the custom role and capabilities

= v8.8.5 RC 2.2 =
* this is a rollback to RC 2 (the changes of RC 2.1 caused a lot of problems)

= v8.8.5 RC 2.1 =
* podPress uses now the init action hook to register, enque and and print scripts and styles to the frontend header

= v8.8.5 RC 2 =
* with the new constant PODPRESS_DEBUG_LOG (= TRUE) it is possible to log the duration and ID3 tag retrieval procedures (for debugging purposes)

= v8.8.5 RC 1.1 =
* minor changes of the method the plugin retrieves the ID3 tags
* parsing of the UserAgent data without the PHP function eregi
* the generated skin files for the XSPF player will no longer contain htmlentities-encoded strings

= v8.8.5 beta 14.1 =
* fixed the get_bloginfo('siteurl') / get_option('siteurl') confusion

= v8.8.5 beta 13 =
* fixed a problem with Listenwrapper

= v8.8.5 beta 12 =
* fixed a lot of problems related to the new botlist feature

= v8.8.5 beta 11 =
* "Play in Popup" works again with IE browsers
* with podPress embedded YouTube videos are now visible in IE6
* possibility to set the dimensions of the YouTube videos available (again)
* further HTML and CSS adjustments to separate code and style

= v8.8.5 beta 10 =
* "work in progress"-icon during the ID3 tag retrieval
* minor changes on several JS scripts
* fix for a problem on the CategoryCasting pages
* The text phrases in podpress_admin.js are ready for localization via .po and .mo files

= v8.8.5 beta 9 =
* further HTML and CSS adjustments
* "work in progress"-icon during the file size and duration detection
* "Theme Compatibility Check" message temporarily deactivate because it does not work reliably (podpress_admin_general_class.php)

= v8.8.5 beta 8 =
* adjustments on the podPress player settings page: new color map for the Podango player, new color options for the updated 1PixelOut player

= v8.8.5 beta 7 =
* a lot of deprecated function calls revised 
* further CSS adjustments

= v8.8.5 beta 6 =
* enhanced the compatibility for the WP 3.0 multi blogs mode
* XSPF player widget ready for the usage in a WP installation with multiple blogs
* some CSS adjustments

= v8.8.5 beta 5 =
* a fix for a problem ([WP.org forum post 379290](http://wordpress.org/support/topic/379290#post-1499746)) with single and double quotation marks in the media file titles while podpress is used with older WP versions (Thanks to [John Halton](http://wordpress.org/support/profile/140408))
* some style clean up on the statistic pages
* some old and unnecessary flash players removed

= v8.8.5 beta 4 =
* podPress uses the DB_COLLATE or DB_CHARSET to create its 2 statistic tables
* CategoryCasting page WP 3.0 ready
* podpress_tmp/-folder procedures are WP 3.0 ready

= v8.8.5 beta 3 =
* new ATOM feeds: podPress adds now an feed with the name "torrent" which contains only posts (or pages) with .torrent files and a feed with the name "enhancedpodcast" which contains only posts (or pages) with .m4a/.m4v files
* more options for the podPress - Feed Buttons widget: more buttons (new buttons added: feed-enhpodcast.png, feed-torrent.png, button_comments_rss_blog.png, button_atom_blog.png, button_comments_atom_blog.png)
* more options for the podPress - Feed Buttons widget: feed buttons mode or text mode (In the text mode there is in front of each text a feed icon, but only for WP 2.2+. For older WP versions it is possible to add an icon via the Filter Hook: podpress_legacy_support_feed_icon)
* more phrases ready for internationalization (http://codex.wordpress.org/I18n_for_WordPress_Developers)
* frappr.com maps advice on the General Settings page of podPress removed
* contains a fix for a problem ([WP.org forum post 379290](http://wordpress.org/support/topic/379290#post-1499746)) with single and double quotation marks in the media file titles (Thanks to [John Halton](http://wordpress.org/support/profile/140408))
* the appearance (incl. paging) of the tables on the statistic pages is updated
* new feature: a bot filter for the download statistic - users can select (and deselected) IP addresses and user agent names which are probably search engine bots. The statistics will be computed without the download numbers of these bots. (for FULL / FULL+ stat logging) 
* The display of the statistics of the different collecting method Count Only, FULL and FULL+ are now separated. If the Count Only logging is active then the statistic page will have now besides the Quick Counts a comparable graph. This table and the graph and the Statistic Summary on the Dashboard are based on the numbers of the db table wp_podpress_statcounts. In Full or Full+ logging mode the number come from the wp_podpress_stats. That is important to know because the counter which counts the downloads on the wp_podpress_statcounts table counts when one of the 3 logging modes is active but the other counter which collects the data in the wp_podpress_stats table counts only during Full and Full+ mode. In other words: the numbers of the 2 tables can differ if someone uses the Counts Only mode for a while and than one of the others. Furthermore it is not possible to display the numbers of the Counts Only mode without the download numbers of the bots. 
* a new "Downloads Per Media File" and a new "Downloads Per Post" overview (for FULL / FULL+ stat logging)
* The <object> element of the players has the class "podpress_player_object".
* each podPress page and the Admin Menu has an icon (podpress_icon_r2_v2_32.png and podpress_icon_r2_v2_16.png added)
* a new button and icon for YouTube videos embedded with podPress (embed_youtube_button.png / embed_youtube_icon.png)
* a new button and icon for WMA files embedded with podPress (audio_wma_button.png / audio_wma_icon.png) (Now, podPress handles WMA file internally as audio_wma and not as video_wma.)
* [Ticket #1089](http://plugins.trac.wordpress.org/ticket/1089) - props: a new customizable XSPF player
* [Ticket #1085](http://plugins.trac.wordpress.org/ticket/1085) - enhancement for loading time of the podpress pages
* [Ticket #1083](http://plugins.trac.wordpress.org/ticket/1083) - a reworked player preview 
* [Ticket #1080](http://plugins.trac.wordpress.org/ticket/1080) - fix for podPress_downloadlinks container
* [Ticket #1079](http://plugins.trac.wordpress.org/ticket/1079) - fix for the "Before <!- More -> tag:"-functionality
* [Ticket #1074](http://plugins.trac.wordpress.org/ticket/1074) - better handling of invalid input data in the feed generating functions
* [Ticket #1073](http://plugins.trac.wordpress.org/ticket/1073) - additional descriptions for the feed settings pages of podPress
* [Ticket #1068](http://plugins.trac.wordpress.org/ticket/1068) - patch for an error-free stats counter (Thanks to [avatarworf](http://wordpress.org/support/profile/17483))
* [Ticket #1066](http://plugins.trac.wordpress.org/ticket/1066) - Podango message at the general settings page of podPress
* [Ticket #1064](http://plugins.trac.wordpress.org/ticket/1064) - compared to 8.8.5 beta 2 the last patch of this ticket makes that the enclosures which are added with podPress listed always before the enclosures from the custom fields with the name "enclosure". that is important because pdocatchers like iTunes tend to recognize only the first enclosure of a RSS item. (Is a fix for [this problem](http://wordpress.org/support/topic/372625). Many thanks to [gamebynight](http://wordpress.org/support/profile/3657145) for the very good bug report!)
* [Ticket #1061](http://plugins.trac.wordpress.org/ticket/1061) - update to 1PixelOut player a.k.a. Audio Player 2.0

= v8.8.5 beta 2 =
* [Ticket #1064](http://plugins.trac.wordpress.org/ticket/1064) - multiple fixes on the feed feed/iTunes settings page and feed functions
* [Ticket #1062](http://plugins.trac.wordpress.org/ticket/1062) - Fix for the Video Player Preview and the Listen Wrapper (contains the [Ticket #1060](http://plugins.trac.wordpress.org/ticket/1060) - little enhancement for the variables tempFileSystemPath and tempFileURLPath)

= v8.8.5 beta =
* [Ticket #1059](http://plugins.trac.wordpress.org/ticket/1059) - fixed color picker (on the player settings page)
* [Ticket #1058](http://plugins.trac.wordpress.org/ticket/1058) - feed stats overview as the dashboard widgets in WP versions >= 2.7
* [Ticket #1057](http://plugins.trac.wordpress.org/ticket/1057) - fix for the php warning while saving the "Quick Edit"-changes in WP versions >= 2.7

= v8.8.4 =
* [Ticket #1056](http://plugins.trac.wordpress.org/ticket/1056) - fix for a problem which appeared in 8.8.2/.3 while the option "Local path to media files directory (optional)" (on podpress General settings page) was in use

= v8.8.3 =
* [Ticket #1055](http://plugins.trac.wordpress.org/ticket/1055) - fix for wrong itunes tags in the rss feed

= v8.8.2 =
* [Ticket #1037](http://plugins.trac.wordpress.org/ticket/1037) - multiple enhancements and fixes

A changelog of earlier versions can be found at
[http://www.mightyseek.com/podpress/changelog/](http://www.mightyseek.com/podpress/changelog/)


== Installation ==

If you have ever installed a plugin, then this will be pretty easy.

1. Extract the files. Copy the "podpress" directory into `/wp-content/plugins/`
1. Activate the plugin through the "Plugins" menu in WordPress
1. Configure the Feed/iTunes Settings and add eventually one of the podPress widgets to one of the sidebars (OR If you are using a WP version that is older than WP 2.5 and you want to add a link to itunes on your website then set the FeedID in the podPress options page, and then add this code in your template `<?php podPress_iTunesLink(); ?>`)

Details about all the optional_files are in optional_files/details.txt

= Requirements =
podPress requires at least WP 2.1 but it is very recommended to use at least WP 2.3. Many features like the widgets, the custom feeds or the CategoryCasting feature will work only with WP 2.3 or later WP versions. Multiple Widgets are available in combination with WP 2.8.x or newer.

The auto detection of the duration and other meta information requires PHP 5.0.5. But all other elements of the plugin work with PHP 4.x, too.

= Included Software =
podPress v8.8.10.17 includes:

* 1PixelOut Audio Player v2.0.4.6 - http://www.1pixelout.net/code/audio-player-wordpress-plugin/
* Podango player - http://sites.google.com/site/podangohibernate/
* XSPF Jukebox v5.9.6 - http://lacymorrow.com/projects/jukebox/
* Cortado Player 0.6 (cortado-ovt-stripped-0.6.0.jar and cortado-signed-0.6.0.jar) - http://www.theora.org/cortado/
* a flvplayer.swf
* getID3 v1.8.5 (1.8.5-20110218) - http://www.getid3.org
* jQuery 1.4.2 + jQuery UI 1.8.5 Dialog + Accordion (jQuery is integrated with the object name podPress_jQuery for parallel usage with the jQuery versions of the different WP versions.)


== Frequently Asked Questions ==

Please, use the WP.org [Plugins and Hacks forum](http://wordpress.org/tags/podpress?forum_id=10) to ask your questions or for bug reports. When you post in this forum please add at least the tag "podpress" to your post. That makes it very much easier to get a notice of your post (There is a [RSS feed of forum posts which are tagged with "podpress"](http://wordpress.org/support/rss/tags/podpress).).
(The domain name of the official [podPress FAQ](http://podcasterswiki.com/index.php?title=PodPress_FAQ "Official podPress FAQ") page is currently (03/2010) unavailable resp. parked. You can still find some [traces of this Wiki at Archive.org](http://web.archive.org/web/20080708140114/podcasterswiki.com/index.php?title=PodPress).)


= What does podPress with the Feeds? =

* podPress adds automatically additional elements (e.g. [iTunes RSS tags](http://www.apple.com/itunes/podcasts/specs.html#rss)) to the default RSS Feeds of your weblog. But it can be used to modify also diverse elements of the ATOM Feeds of a blog. You can find most of the related options at the Feed/iTunes settings page of podPress.
Furthermore it is possible to configure these options for each category Feed separately. This feeature is called Category Casting and you can activate and configure it for existing post categories at the page where you can edit the settings of a category.
* But podPress adds also completely new Feeds to your weblog. These Feeds might be RSS or ATOM Feeds and you can control the content of the Feed for instance with a category filter or a file fype filter. You can configure those Feeds also at the Feed/iTunes Settings page of podPress in the section podPress Feeds.

= How can I create multiple Podcast Feeds? =

There are basically two ways:

* One way is to use the Category Casting feature to adjust all kinds of podcast related elements in the category feeds of the blog. WordPress produces these category Feeds and you can customize them with podPress. These Feeds containing posts of one category. You activate the Category Casting feature while you edit an existing category in which you edit an existing category.
* The other possibility is to add one or more additional, new Feeds to the blog with the help of the settings of the podPress Feeds section at the Feed/iTunes Settings page of podPress. You can influence the content of these podPress Feeds with the help of a category filter and a file type filter. 
With the file type filter you can determine that a Feed contains for instance only posts with .mp3 or .m4a podcast episodes regardless of the categories of the posts. But it is also possible to create something like category Feed with the category filter. But with the differences that you can select more than category per Feed and that such podPress Feeds have maybe nicer URLs.

(Notice: After creating a podPress Feed, after changing a slug name or after (de-)activating such a Feed, you need to re-save the Permalink settings of your blog.)

= How can I add podcast episodes with podPress? =

First you need to upload the podcast files with WordPress or probably with a FTP client software. (Often podcast files are to big to upload them with a web formular like the WordPress Media Uploader. in such case you will need to use a FTP client software or comparable program.) It is basically a good idea to store the podcast files in sub folders of the /wp-content/uploads/ folder of your blog.
After you have uploaded a media file, you create or edit the post (or page) which should contain an episode. Below the editor box you will find a separate box with the title "podPress - postcasting settings for this post". There you insert the complete URL of the podcast file and enter or determine automatically some meta information about the current episode like the duration or the file type and size. While saving or publishing the post these podcast information are getting saved as well.

(You can get probably a good impression how it works from [this WP.org forum thread](http://wordpress.org/support/topic/356947) although the original question is a little bit different. This [other WP.org forum thread](http://wordpress.org/support/topic/425141) might also be helpful.)

= Does podPress has HTML5 support? =

Yes. podPress (since 8.8.10) embeds .mp3, .ogg and .ogv media files with the HTML5 `<audio>` and `<video>` tags if the browser of the visitor supports these media file types in combination with these HTML5 tags. This means that podPress will embed MP3 files for all browsers with the WebKit layout engine of the version 525.x and newer (e.g. Safari, Chrome) and the Internet Explorer 9 (and newer) with the <audio> tag. Ogg Audio and Video files getting embedded with the HTML5 media tags for browsers with the Gecko engine of version 1.9.1 and newer (e.g. FireFox) and for browsers with the Presto engine of the version 2.5 and newer (e.g. Opera).
For more information see the [Comparison of the layout engines](http://en.wikipedia.org/wiki/Comparison_of_layout_engines_%28HTML5_Media%29#Audio_format_support).
Otherwise podPress embeds the media files as before with Flash-based players or as objects which require other browser plugins.

It is possible to disable the usage of these HTML5 tags (podPress / Player Settings).

The desktop versions of the browsers which support these HTML5 media tags have different behaviours regarding the buffering of the media files. For instance Safari and Chrome start to download/buffer all media files of one page which are embedded with these HTML5 tags after the page is loaded.
Because this would probably increase the [web traffic](http://en.wikipedia.org/wiki/Web_traffic) podPress shows by default a Play button and only a click on such a button will make the player visible and start it.
But if the amount of [web traffic](http://en.wikipedia.org/wiki/Web_traffic) does not matter then it is possible to let podPress show the HTML5 players directly.

= How can I submit podcast episodes to iTunes? =

podPress creates a RSS feed with additional information for iTunes. You can use the "Feed/iTunes settings"-page of podPress to determine what the content of these [iTunes RSS tags](http://www.apple.com/uk/itunes/podcasts/specs.html#rss) should be. It is possible to subscribe to this feed directly with the iTunes client program.
But you can add your podcast Feed to the iTunes Store. For more information go to the [iTunes Podcast Resources / Making a Podcast / Submitting Your Podcast to the iTunes Store](http://www.apple.com/itunes/podcasts/specs.html#submitting).

= What is this iTunes:FeedID? =

If you submit your podcast to the iTunes Store then your podcast will get an ID. Insert this ID on the "Feed/iTunes settings"-page of podPress e.g. if you want to use the Feed Buttons widget of podPress. podPress creates the link to your podcast in the iTunes Store with this ID.

= How can I change the my podcast feed URL in the iTunes Store? =

Apple describes [how it works](http://www.apple.com/itunes/podcasts/specs.html#changing) basically in their [iTunes Podcasting Resources](http://www.apple.com/itunes/podcasts/specs.html) and how you can do this with podPress is dicussed in this WP.org [forum thread](http://wordpress.org/support/topic/249345).

= How can I set a post specific license and license URL? =

Define two custom field per post. One with the name **podcast_episode_license_name** and the name of the license as the value and one custom field with then name **podcast_episode_license_url** and the URL to the full license text. It is necessary to define at least the URL. If the name is not defined then the name will be the URL.

= I have a post and have added two (or more) media files via podPress. Why is it not possible to mark them all for the usage in a RSS Feed? =

podPress allows only one media files enclosure per post in the RSS Feeds because the RSS specification is unclear about this point and [W3.org Feed Validator would say](http://feed2.w3.org/docs/warning/DuplicateEnclosure.html) that it is not recommended to have more than one `<enclosure>` per RSS post element (`<item>`). You can find a summary about this matter in [part 1](http://www.therssweblog.com/?guid=20070520140855) and [part 2](http://www.therssweblog.com/?guid=20070522234541) of in article at the therssweblog.com. But probably more important is that iTunes recognizes only the first `<enclosure>` of each `<item>`. That is also valid for multiple enclosure elements per `<entry>` element of an ATOM Feed. But if like to have more than one media file per post in an News Feed then you could use such a Feed.
You can use the "Include in:" option in the box below the post editor to choose the media file which should be in the default RSS Feeds.

= Why are no iTunes-tags in the ATOM Feeds? =

Because the [iTunes-tags](http://www.apple.com/itunes/podcasts/specs.html#rss) are only defined for RSS Feeds. (But iTunes is able to handle ATOM Feeds with podcast episodes or other media files.)

= Blank screen after activating podPress =

Some PHP5 users end up with a blank screen after activating the podPress plugin. For reasons yet fully understood some PHP5 installations consume double the memory compared to a PHP4 install when dealing with WordPress. Some notes I have seen blame it on a bug with caching objects in session data, but I have not debugged it to that level yet.
The solution is to increase the memory_limit in your php.ini from 8MB to at last 12MB 

= How do I upgrade PodPress? =

In general this just requires that you replace the existing files with the new ones. Sometimes it is a good idea to delete all the files in wp-content/plugins/podpress/ and re-upload them fresh.


== Screenshots ==

1. Write a page and at the end of your Post add your mp3 filename or full URL.
2. Players automatically added to your blog
3. Edit config settings and preview what your podcast will look like in various podcasting directories including iTunes.
4. Stats graph by podcast
5. Stats graph by date


== Upgrade Notice ==

= 8.8.10.17 =
make a data base backup before you upgrade


== Other Notes ==

= Upgrade =
Upgrading from 8.8.6.3 (or older version) to 8.8.8.x (or newer versions):
If you have used the widgets of podPress then it will be necessary to reactivate these widgets manually after an upgrade. The reason for these cirumstances are the massive modifications on both podPress widgets. The widgets will try to take over the old settings as far as possible.
podPress adds feeds to your blog. These Feeds have the names podcast, premium, enhancedpodcast (since 8.8.5) and torrent (since 8.8.5). In the past versions, you could not control these Feeds and their meta information via a settings page.
That is why v8.8.8.x has a new section at the Feed/iTunes Settings page (which has also a new design). With this new podPress Feeds section in place, the purpose of the Feed Settings section has changed. Now, the Feed Settings section is for controlling the
iTunes tags and other settings only of all the main Feeds of the blog (like the default Entries RSS2 Feed) and the podPress Feeds section is for controlling the additional Feeds which podPress adds to your blog (like podcast). 
In the past all the Feeds have shared the same information from the Feed Settings section. If you are using at least one of these Feeds to publish your podcast episodes or to route them to Feedburner or iTunes or a comparable service then you need to fill
in the information about this Feed like the content description or the URL of Feed icon into the new podPress Feeds section. You may copy and paste the information from the Feed Settings section or you could use the chance to customize these information.
If you are using a non-default Permalink scheme then you should save the Permalink settings after the podPress upgrade. You should also re-save the Permalink setting after you have changed, activated, deactivated or renamed a Feed or if you are using the podPress the first time in your blog). 
Upgrading to v8.8.10 is going to rename the podPress `meta_key`s in the postmeta data base table of your blog (`podPressMedia` becomes `_podPressMedia` and `podPressPostSpecific` becomes `_podPressPostSpecific`). Downgrading to a previous version is only possible if you reverse these changes.

podPress v8.8.5 and newer versions require at least WP 2.1 but it is recommended to use at least WP 2.3. To be able to use the full feature set (e.g. for multiple widget support) of podPress, you should use at least WP 2.9.x.

Upgrading to 4.0:
The plugins/podpress.php file is no longer needed and MUST be deleted. The podpress.php file now lives in `plugins/podpress/`

About wp-rss2.php:
Only users that have not upgraded to a version of WordPress above 2.0.0 need the custom wp-rss2.php file. If you have upgraded past 2.0.0 then use the normal wp-rss2.php that came with WordPress.

About wp-commentsrss2.php: 
No one should be using the custom version of this file anymore. It is not supported and may cause problems.

= Configuration =

Go to the new podPress menu and start configuring your Feed settings, player appearance, statistics and more general settings.

= Feeds of podPress =

podPress adds not only iTunes-tags and <enclosure>-tags to your usual RSS feed. It adds e.g. a feed called "podcast" (http://example.com/?feed=podcast), a feed called "playlist.xspf" (http://example.com/?feed=playlist.xspf) and a feed called "premium" (http://example.com/?feed=premium).
The podcast feed contains all the posts which have at least one media file added with podPress despite the categories the post is in. It is not necessary to put all the post with podcast episodes in a category called podcast. (There is one constellation you should avaoid: If you name a sub category podcast and you are using e.g. the Permalink structure "Date and name" then the URL http://example.com/category/parentcategoryname/podcast/ leads to the feed called podcast and not to the category view.)

The playlist.xspf feed is a feed in a special XML format, the [XML Shareable Playlist Format](http://en.wikipedia.org/wiki/XML_Shareable_Playlist_Format "Wikipedia: XSPF (en)") (XSPF). It is a playlist of all the .mp3 files which are added to posts with podPress.

The premium feed contains the premium content added with podPress.

Since v8.8.5 podPress produces by default a torrent feed again and has a new feed called "enhancedpodcast". The "torrent" feed contains only posts which have .torrent files attached with podPress. The 'enhancedpodcast' feed contains only posts which have .m4a or .m4v files attached with podPress. If a post has different types of media files attached then only the right media files will show up in these feeds. In other if you have a post with an .m4a podcast and you have a torrent for this file you can add these file to your post.
(If you are not using the default permalink structure then you should save the permalink settings after the update to v8.8.5 or higher.)
Since v8.8.8 you can customize all aspects of these additional feeds. For instance it is possible to change the file type filter setting and it is possible to activate/deactivate these feeds.

Furthermore you can customize the feed settings of category feeds. This feature is called CategoryCasting and you can find it when you edit a category.

= Filter Hooks of podPress =

Since v8.8.5 you can filter some parts of the output of podPress

* podpress_entry_enclosuretags: a filter for each `<link rel="enclosure">` tag which podPress adds to ATOM feed entries
* podpress_xspf_trackinformation: a filter for each `<track>` tag which podPress adds to the XSPF playlist
* podpress_item_enclosure_and_itunesduration: a filter for each `<enclosure>` tag which podPress adds to the RSS feed items
* nonpodpress_atom_enclosure: podPress adds the media files which are added with podPress at first to the news feeds. `<enclosure>` tags of media files which are added a different way (e.g. automatically via a link the post content) will arranged after the podPress `<enclosure>` tags by podPress. This filter is for filtering each of these non-podPress `<link rel="enclosure">` tags in ATOM feeds.
* nonpodpress_rss_enclosure: This filter is for filtering each of these non-podPress `<enclosure>` tags in RSS feeds.
* podpress_downloadlinks: a filter for the podPress row below the player (the `<div class="podPress_downloadlinks">` container). If you want to use this filter hook then make sure that the filtered result still contains the span element with an id like this: `'<span id="podPressPlayerSpace_'.$GLOBALS['podPressPlayer'].'_PlayLink" style="display:none">| '.__('Play Now', 'podpress').'</span>'`. This span element is in the filter input. Do not replace all of the content.
* podpress_post_content (since 8.8.8): a filter for the complete podPress section of posts with podPress attachment (for the sections `<!-- Begin: podPress --> ... <!-- End: podPress -->`)
* podpress_post_scriptblock (since 8.8.10.9): a filter for the Javascript block of the podPress section of posts with podPress attachment
* podpress_legacy_support_feed_icon (only in combination with WP < v2.2): it is a possibility to add a feed icon in front of each line of the Feed Buttons widget in text mode
* podpress_posts_distinct (since 8.8.10): a filter for the modifications which podPress adds to the DISTINCT part of the posts query of WP
* podpress_posts_join (since 8.8.10): a filter for the modifications which podPress adds to the JOIN part of the posts query of WP
* podpress_posts_where (since 8.8.10): a filter for the modifications which podPress adds to the WHERE part of the posts query of WP

= How-To use a different skin for the XSPF player: =
Since v8.8.5 podPress uses the [XSPF player created by Lacy Morrow](http://blog.lacymorrow.com/projects/xspf-jukebox/) which has the possibility to use customized player skins. podPress includes a default skin. But it is also possible to use different skin files. These skin files are XML files and need to be placed in sub folders of the wp-content/ folder e.g. /wp-content/plugins/podpress_options/xspf_options/custom/skin_1_{width}x{height}.xml. Since v8.8.8 the default folder for these files is outside of the folder of podPress and the naming scheme of the skin files is different.
Furthermore it is necessary to copy the podpress_xspf_config-sample.php file into the /wp-content/plugins/podpress_options/ folder, to rename it to podpress_xspf_config.php and to edit it.

If you place that configuration file into a separate folder as suggested then it will endure an automatic plugin upgrade.

The name of the skin file and the custom variables file are skin_1_{width}x{height}.xml and variables_1.txt. The "_1" in the file names (and in the names of the constants) stands for the blog ID. In the single blog mode the blog ID is 1. If you have an multiple site blog installation (WP 3.+ or WPMU), the main blog ID is also 1. The skin file names need to contain also the width and height. You can find these values inside these the skin.xml files. It is also necessary to use these width and height values in the widgets settings. If you change with or height in the widgets settings then you need to rename the file again or you need a separate file which has the new values in the file name. 
For instance if you want to use the player in one sidebar of the main blog with the width 210 pixels and on an other page with the width 600 pixels then you need two files e.g. skin_1_210x210.xml and skin_1_600x210.xml. A skin file name for a widget of the first sub blog would be e.g. skin_2_170x210.xml.
Note that the width needs to be at least 150px, the height at least 100px and the height of the slim player at least 30px. 

Please, look also into the podpress_xspf_config-sample.php file (new since v8.8.8) for further information about the usage of such skin files.

The "[XSPF Jukebox skin.xml Specification](http://lacymorrow.com/projects/jukebox/skindoc.html)" are documents which explain how-to make such a file. 
But you can also use one of the [existing skins](http://blog.lacymorrow.com/projects/xspf-jukebox/). podPress uses a derivative of the SlimOriginal skin by default. (Since v8.8.8, podPress uses for the player skin no separate files.)

= How-To change the XSPF playlist URL =
If you have a custom XSPF playlist e.g. a .xspf file and you would like that the player uses this custom playlist then you can insert the new URL in the widgets settings (or define the constant PODPRESS_CUSTOM_XSPF_URL_x with the URL of this custom playlist (At the end of the file podpress_xspf_config-sample.php is a prepared line of code.). The `x` in the name of the constant stays for the ID of the blog.). The URL has to be an URL to a playlist which is on the same domain/server as your blog! But it is allowed that the tracks in the playlist can be situated on a different server.

= How-To use custom variables for the XSPF player: =
Create a sub folder in the plugins folder of your blog e.g. /wp-content/plugins/podpress_options/xspf_options/variables (create a different one for the slim player e.g. /wp-content/plugins/podpress_options/xspf_options/variables_slim/). Copy the podpress_xspf_config-sample.php file from the podpress folder to the podpress_options/ folder. Rename it to podpress_xspf_config.php and uncomment the line with constant PODPRESS_XSPF_USE_CUSTOM_VARIABLES_1 (or PODPRESS_XSPF_SLIM_USE_CUSTOM_VARIABLES_1 for custom variables for the slim player).
The number 1 in the name of the name of the constant is stands for the ID of the blog (it is 1 for the main blog of a multi site installation or a single blog). If you want to define different variables for XSPF player in different (sub) blogs then you need to define constant and variables files with their IDs too. 
The last stept is to create the variables files and place them in the variables (or variables_slim) folder. They have the naming scheme variables_{blog ID}.txt. For more information about all possible variables see the [documentation of the XSPF Jukebox](http://lacymorrow.com/projects/jukebox/xspfdoc.html).