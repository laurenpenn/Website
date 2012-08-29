=== Secondary HTML Content ===
Contributors: jakemgold, 10up
Donate link: http://www.get10up.com/plugins/secondary-html-content-wordpress/
Tags: HTML, editor, WYSIWYG, tinymce, widget, sidebar, content
Requires at least: 3.3
Tested up to: 3.3
Stable tag: 3.0.1

Add additional HTML blocks to any post type. Perfect for layouts with distinct content blocks, such as a sidebar or two column view.


== Description ==

Add unlimited extra HTML content blocks to pages, posts, and custom post types. A perfect solution for layouts with distinct content "blocks", such as a sidebar or multi-column view. When editing content, the secondary editors appear beneath the usual editor.

Name the new content blocks anything, and assign each block to any of your site's public post types. Hierarchical post types, like pages, can optionally inherit their content from ancestors (including their parent page); perfect for section wide sidebars. Each block can individually be customized to offer media buttons and the full or more stripped down version of the editor.

Secondary content can be added to your site by using the Secondary HTML Content widget. Theme developers can also call secondary blocks by using the built in functions (see "Installation").

REQUIRES WordPress 3.3 or newer.


== Installation ==

1. Install easily with the WordPress plugin control panel or manually download the plugin and upload the extracted folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the Plugins menu in WordPress.
1. Define new secondary HTML blocks using the new Secondary HTML Content section on the Writing settings page.
1. Start entering secondary content by editing your content!
1. Output secondary blocks by using the Secondary HTML Content widget (Appearance > Widgets) or by using the `get_secondary_content()` and `the_secondary_content()` functions in your template!

= Theme Code Example =

`the_secondary_content( 'More Info', 20 );`

Prints out content from the secondary block named "More Info" assigned to page ID 20.

`the_secondary_content( 'Contact Information' );`

Prints out content from the secondary block named "Contact Information" for the current post.

`the_secondary_content();`

Prints out content from the first secondary block assigned to the current post type, for the current post. Useful in situations where there is only one secondary content block.


== Screenshots ==

1. Editing a page with a secondary content block named "Specifications".
2. Managing secondary content blocks.
3. Add secondary HTML content to the site using a widget.


== Changelog ==

= 3.0.1 =
* Fixed a backwards compatibility glitch related to `get_secondary_content` failing when passing in an integer to identify the old secondary block number (e.g. `get_secondary_content(2);`)

= 3.0 =
* Effectively a complete rewrite!
* Unlimited secondary HTML content blocks (well, up to 100)
* Name secondary blocks anything!
* Full support for visual / HTML modes in the editor
* Full support for custom post types
* Per block settings for media buttons
* New per block option to use a more basic editor
* Filters for developers

= 2.0 =
* Add up to 5 blocks for pages and posts (configured independently)
* Multiwidget support & specify which block to use in the widget
* Optionally add media buttons to secondary content blocks
* Various other improvements to the code base

= 1.5 =
* Option to use on pages, posts, or both (only pages before)
* Option to inherit ancestor secondary HTML content on pages
* Many under the hood changes and enhancements


== Upgrade Notice ==

= 3.0.1 =
Version 3.0 onwards *REQUIRES* WordPress 3.3 or newer; settings have moved to Settings > Writing. 3.0.1 fixes a compatibility glitch related to the get_secondary_content function for users upgrading from 2.0.

= 3.0 =
Virtually a complete rewrite, 3.0 *REQUIRES* WordPress 3.3 or newer. The plug-in will stop working on older versions (you can manually downgrade). A seamless upgrade path from v2.0 of the plug-in is built it. Note that plug-in settings have been *moved to Settings > Writing*.