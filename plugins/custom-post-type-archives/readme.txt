=== Custom Post Type Archives ===
Contributors: rATRIJS
Donate Link: http://ratvars.com/custom-post-type-archives/
Tags: custom post types, custom post type, post types, post type, archive, archives, rewrite, feeds, paging, post type url, custom post type url, custom post type archive url, post type archive url, custom post type index, post type index
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.5.1

Enables custom post type archives that will support both paging and feeds. All fully customizable.

== Description ==

**Since version 3.1 WordPress has it's own implementation of custom post type archives so you can create them without this plugin - visit this page for more info -&gt; http://codex.wordpress.org/Post_Types. Nevertheless I do believe that this plugin is more flexible and you can still use it and it will still work as expected.**

This plugin will enable custom post type archives (also yearly, monthly and daily) together with feeds, customizable titles and paging.

These archives will work the same way as the category or tag archives work when you go to, for example, http://example.com/category/example.

WordPress 3.0 new custom post type feature is awesome. The only problem is - it lacks archive functionality for these post types so you can't easely assign one URL to just list your 'photo' post type posts. You also won't be able to get feeds just from this post type. This plugin adds this functionality to WordPress so that you can fully enjoy custom post types. It will also let you to create seperate templates for your post type archives which wasn't possible before. You will be able to use the same things you are familiar with if you are a theme developer as well as you will be able to enjoy this feature if you are not a developer but just want to enable this feature for your blog.

With this plugin you will be able to specify:

*	URL base for custom post types. Assuming you have a post type named 'photo', you can list all your post types in URL http://example.com/photo or http://example.com/post-type/photo - whatever you like by simply filling one field.
*	Whether to use the custom post type rewrite slug or custom post type name as URL parameter after 'URL base'. It defaults to true, because that's the way it should work, but it might be more efficient to disable this option. For more information please read the FAQ. Also if this option is enabled then you will be able to specify a custom rewrite slug for each enabled custom post type archive.
*	Title for post type archives. You can use {POST_TYPE_NAME} and {POST_TYPE_SINGULAR_NAME} variables into this option. {POST_TYPE_NAME} will be replaces with current post types name and {POST_TYPE_SINGULAR_NAME} will be replaces with current post types singular name. So if you are in post type 'photo' and this option is set to 'Post Type "{POST_TYPE_SINGULAR_NAME}"' then the title will say 'Post Type "Photos"'. You can also use {SEP}, {SEP_LEFT_SPACE}, {SEP_RIGHT_SPACE}, {SEP_SPACED}. These will be replaced with seperator specified in wp_title function. Spaced ones will have the space on defined side. {SEP_LEFT_SPACE} will have space in left side and vice verca. {SEP_SPACED} will have spaced on both sides. I had to do this in this way, because WordPress trims variables before saving. If left blank (this option), plugin won't change the title. You can also specify a custom title for each enabled custom post type archive.
*	What template file to use for rendering a custom post type. You can use either one template for all post types or just type 'post-type-{POST_TYPE}.php' and for post type 'photo' plugin will try to include 'post-type-photo.php' to render the custom post type.
*	What template file to load if above template file is not found. So it's easy to use index.php for all post types except 'photo' for example.
*	Whether to add a feed link for custom post type archive. Will work the same way as any other feed links, for example in category index pages where your visitors can subscribe to your site updates. Plugin will automatically insert the feed link if ['automatic-feed-links'](http://codex.wordpress.org/Function_Reference/add_theme_support) is enabled for your theme. If that's true then plugin won't show this option in plugin settings.
*	Post types that gets archives. Just tick the checkboxes next to those post types you want to see archives. Easy as that.

Note that if you update URL base field, disable/enable post type rewrite tag usage or enable/disable some post type archives, then you will have to visit 'Permalinks' section under 'Settings' to flush old permalinks and enable the changes.

Also a good thing for theme developers - you have four new functions to use:

*	pta_is_post_type_archive - this function will work similary as is_category or is_single and so on. It will return true if this page is a custom post type archive or false if it isn't. You can also specify an optional argument with post type name and then function will return boolean to say whether you're in post type archive for that post type or not. Simple and useful!
*	get_the_post_type_permalink - this function will return a link to custom post type archive for current post in the WordPress loop. Or you can specify a post type slug or post ID or post object as an argument if you are not in a loop. In this way you can always link to custom post type archives wherever you are.
*	the_post_type_permalink - uses get_the_post_type_permalink to echo the link rather than return it.
*	wp_get_post_type_archives - will work the same way as ['wp_get_archives'](http://codex.wordpress.org/Function_Reference/wp_get_archives) function that allows you to get yearly, monthly, daily (and so on) archives for custom post types.

If you have any issues at all, please try these steps that hopefully will help you:

*	check out the Faq section to search for an answer for your question
*	visit plugins homepage (http://ratvars.com/custom-post-type-archives) for more information (you can also leave a comment there)
*	create a new forum topic in here about the issue you're having

I will try to help you as good as I can.

== Installation ==

Plugin installation is simple and fast.

1.	Put the downloaded .zip file extracted contents (directory called 'custom-post-type-archives') in wp-content/plugins directory
1.	Activate the plugin through the 'Plugins' menu in WordPress
1.	Go to 'Post Type Archives' submenu under 'Settings' menu in WordPress admin section
1.	Check the checkboxes next to post type names to enable archives for them.
1.	Save the settings
1.	Visit 'Permalinks' submenu under 'Settings' menu in WordPress admin section
1.	Post type archives should now work for you - yeey ^_^

Note: if you left all the other options blank then post type archives will be accessible from URL 'http://example.com/{POST_TYPE}', where {POST_TYPE} is your post type slug (post type name thats safe for URL). Plugin will try to use {POST_TYPE}.php (from your theme root) to render the post type archive and if it won't exist, then index.php (from your theme root) will be used.

== Frequently Asked Questions ==

= Why my URLs doesn't change when I change the 'URL base' option or my post types still doesn't have archives when I add them to 'Enabled Custom Post Type Archives' option? =

You should go to 'Permalinks' submenu under 'Settings' menu in WordPress admin backend after you update 'URL base' or 'Enabled Custom Post Type Archives' options. This will force WordPress to flush the old rewrite rules and re-generate the new ones that will have the stuff plugin needs in them.

= What should I put in 'URL base' option? =

The prefix for URLs that will lead to your post type archives. If you want to access your post types from URL 'http://example.com/{POST_TYPE}' then leave this option blank. If you want to have an URL 'http://example.com/post-type/{POST_TYPE}' then you would put 'post-type' as this options value. And if you want 'http://example.com/some/long/link/to/post/type/{POST_TYPE}' then you would put 'some/long/link/to/post/type' as options value.

= What's the deal with 'Use Rewrite Slug' option? =

This might be a bit hard to understand. If you don't know what it is, then leaving it enabled, probably, is the best option. By default the rewrite slug will be the same as post type name, but you can change it if you like. Please look at these examples:
`
register_post_type('photo', array(
	/* other options */
)); // will have a rewrite slug 'photo'

register_post_type('photo', array(
	/* other options */
	'rewrite' => array('slug' => 'awesome-photo')
)); // will have a rewrite slug 'awesome-photo'
`
If you never specify a custom rewrite slug, then I would suggest disabling this option because it will work just fine, because the post type name will be the same as the rewrite slug. Although, if you specify custom rewrite slugs, then enabling this option is what you want, otherwise the URLs will have the post type name in them not post type rewrite slug. I made this choise, because I think (note the 'I think' - I havent tested it...yet), that disabling this option will make the code a bit faster.

= What should I put in 'Title' option? =

You put the title you want to appear in post type archive in here. You can use several variables in this option:

*	{POST_TYPE_NAME} - replaces with current post type name (in plural)
*	{POST_TYPE_SINGULAR_NAME} - replaces with current post type name (singular)
*	{SEP} - replaces with seperator (which is specified in wp_title function)
*	{SEP_LEFT_SPACE} - replaces with seperator prefixed with space
*	{SEP_RIGHT_SPACE} - replaces with seperator suffixed with space
*	{SEP_SPACED} - replaces with seperator wrapped in spaces

I have so many space variables because WordPress trims variables before saving. Anyway - if you have a post type with name 'Photo', a seperator '&raquo;' and 'Title' options value '{POST_TYPE_NAME} {SEP}', then title on 'Photo' post type archive would be 'Photos &raquo;'.

If you leave this option blank, then title won't be changed (it will probably be blank).

It's also worth noting that the same variables will be available for the custom - per custom post type - 'Title' options as well. If they are left blank then it will fall back to global 'Title' option.

= What should I put in 'Template Pattern' option? =

The template name that will be used to render post type archives. You can use a {POST_TYPE} variable in this name and that will be replaced with current post type. So if this option has a value 'post-type-{POST_TYPE}.php' and you are in 'photo' post type archive, then 'post-type-post.php' template will be used. Templates should live in the root of your theme directory. If you leave this field blank then it will default to '{POST_TYPE}.php'.

= What should I put in 'Fallback Template' option? =

Name of the template that will be used if the template that's stored in 'Template Pattern' option won't exist. So if you have 'Template Pattern' option value of '{POST_TYPE}.php', 'Fallback Template' option value of 'fallback.php' and you are in a post type archive for post type 'photo', but 'photo.php' doesn't exist then 'fallback.php' will be used. Template should live in the root of your theme directory. If you leave this field blank, then it defaults to 'index.php'.

= Why isn't the 'Enable feed links' option showing up in plugin settings? =

That's because the ['automatic-feed-links'](http://codex.wordpress.org/Function_Reference/add_theme_support) support is enabled for your theme. Don't worry - the feed links will be included automatically!

= Why can't I see the 'Rewrite Slug' option for each enabled custom post type archive? =

You can only see this option if 'Use Rewrite Slug' option is enabled.

= How can I disable feed link adding if 'automatic-feed-links' support is enabled for my theme? =

Sadly you can't do it from backend. But! You can do it using the 'pta_add_feed_link' filter! Just return false in it and thats it! Here's an example (put this in themes functions.php file):

`
function disable_pta_feed_links($add_feed_links) {
	return false;
}
add_filter('pta_add_feed_link', 'disable_pta_feed_links');
`

= Where do I find the feeds for post type archives? =

Just put '/feed/' at the end of your post type archive URL. That's how WordPress will know that you want an RSS feed of that. (it will also support all the other feed types as well, like atom and rdf)

= How do I use the get_the_post_type_permalink and the_post_type_permalink functions? =

get_the_post_type_permalink function will return the permalink where the_post_type_permalink will echo the link.

You can pass an optional argument for these functions:

*	If you pass nothing or false, then function will assume that it's called from WordPress loop and will get the post type link for current post in the loop.
*	If you pass an integer (cast it to integer using '(int) $id' just to be safe) then functions will assume that it's an post ID and will get post type link from post with this ID.
*	If you pass an object then functions will assume that it's an post object and will get the post type link from that post object.
*	Also you can pass a string that has to be a post type slug. Functions will use this string to create a link.

If at the end functions can't get the post type slug then will return/echo an empty string.

= How do I use the wp_get_post_type_archives function? =

The functon takes two arguments:

*	Post type rewrite slug to get archives from or 'all' to get archives for all the post types
*	Array of options. It uses the same options (because it uses that function) as the ['wp_get_archives'](http://codex.wordpress.org/Function_Reference/wp_get_archives) function so please read that documentation.

Function will do the same thing as the wp_get_archives function (it even uses the same function to get the data). I had to create a wrapper for that function because WordPress lacked hooks to let me fix the URLs that came out. Basically it it the same function with additional 'post_type' argument so please read the [wp_get_archives documentation](http://codex.wordpress.org/Function_Reference/wp_get_archives).

P.S. It might not work properly with 'weekly' archives.

== Screenshots ==

1. This is how the options page looks. Simple and clean I think.
2. This shows a custom post type archive of a post type 'photo' using this plugin.
3. Plugin will add a link to the custom post type feed if you choose so.

== Changelog ==

= 1.5.1 =
*	Fixed a bug in 'wp_get_post_type_archives' functions. It should now support $format = 'option' output type.

= 1.5 =
*	This version should now support WordPress 3.1.
*	Function 'is_post_type_archive' is renamed to 'pta_is_post_type_archive', because since v3.1 WordPress implements it's own 'is_post_type_archive' function and WordPress's function won't work correctly with this plugin so you should use 'pta_is_post_type_archive' if you use this plugin. 'is_post_type_archive' function implemented by this plugin will still be available if you have a WordPress install older than v3.1.

= 1.4 =
*	Added support for yearly, monthly and daily archives. Now you can just put year and month and day at the end of the custom post type archive URLs to show only posts from those dates. It works the same way as in other places.
*	You can also use the new function 'wp_get_post_type_archives' to render out these dated lists the same way as the 'wp_get_archives' function. You can use the same options as the 'wp_get_archives' function as it uses it to get the data.
*	Added some body classes for the custom post type archives - 'post-type-archive' and 'post-type-{POST_TYPE}-archive'

= 1.3 =
*	Added extra customisation per enabled custom post type archive. Now it is possible to specify a custom title and custom rewrite slug for every enabled custom post type archive. If you don't want to use them, then just leave them blank and they won't be used. 'Rewrite Slug' option will only be visible if 'Use Rewrite Slug' option is enabled.
*	Added contextual help in options page.

= 1.2.2 =
*	Changed the way that plugin deals with titles. This should fix the random 'colon bug' which showed up for some people. To see how the new system works, please read the changes in plugins homepage or wordpress plugin directory.
*	Added a new feature for is_post_type_archive() function. Now it is possible to add an optional argument for this function which should contain the post type name. If that's passed then function will tell you whether you are in a post type archive which has this name.

= 1.2.1 =
*	Plugin now supports custom post type rewrite slugs. Previously it just used the post type name as an URL parameter, now it will use the custom rewrite slug (if one is set). You can enable or disable this option. The default is enable! Please read the FAQ for more information.
*	Improved the options page.

= 1.2 =
*	Added feed links for post type archives. You can controll this from plugin settings if you don't have 'automatic-feed-links' support enabled for your theme or feed link will be automatically added if 'automatic-feed-links' support is enabled.

= 1.1.1.1 =
*	Updated the main plugin file to say the right version. Hope that this will fix the stuck WordPress Directory version number as well.

= 1.1.1 =
*	Added another function - 'get_the_post_type_permalink' which will return the link not echo it as 'the_post_type_permalink' does
*	'the_post_type_permalink' function now uses 'get_the_post_type_permalink' function to get the data
*	Added ability to pass an post ID and post object to these function to get the post type link.
*	These function will now return/echo an empty string if no post type was found.
*	Also I hope that this will fix the stuck 0.9 version that WordPress is saying on download page.

= 1.1 =
*	Added a 'Title' option that will allow users to customize the title that will be shown in post type archive pages.

= 1.0 =
*	Initial version. Can't say more :)

== Upgrade Notice ==

= 1.5.1 =
Fixes bug in 'wp_get_post_type_archives' function. $format = 'option' shouldn't now be supported.

= 1.5 =
Added support for WordPress 3.1. Renamed function 'is_post_type_archive' to 'pta_is_post_type_archive'. Use this function if you use WP 3.1 or newer.

= 1.4 =
Added support for daily, monthly and yearly archives and added a function like 'wp_get_archives' only for custom post types. Also added body classes.

= 1.3 =
Added extra customisation (titles and rewrite slugs) that can be specified for every enabled custom post type archive.

= 1.2.2 =
Improved the way titles are handled (should fix 'colon bug') and improved the is_post_type_archive function.

= 1.2.1 =
This version now supports custom rewrite slugs for custom post types. Please look at FAQ for more information.

= 1.2 =
Adds feed link support for post type archives.

= 1.1.1.1 =
Just fixes the main plugin file to show the correct plugin version.

= 1.1.1 =
Better the_post_type_permalink function and a new get_the_post_type_permalink function.

= 1.1 =
Previously the title was empty in post type archive pages. Now you can fully customize the title on post type archives.