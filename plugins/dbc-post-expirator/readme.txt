=== Post Expirator ===
Contributors: developdaly, axelseaa
Tags: expire, posts, pages, schedule
Requires at least: 3.1.2
Tested up to: 3.1.2
Stable tag: 1.0

Allows you to add an expiration date (minute) to posts which you can configure to either delete the post or change it to a draft.

== Description ==

The Post Expirator plugin allows the user to set expiration dates for both posts and pages.  There is a configuration option page in the plugins 
area that will allow you to seperataly control whether or not posts/pages are wither deleted or changed to draft status.

The plugin hooks into the wp cron processes and runs every hour.

The expiration date can be displayed within the actual post by using the [postexpirator] tag.  The format attribute will override the plugin 
default display format.  See the [PHP Date Function](http://us2.php.net/manual/en/function.date.php) for valid date/time format options. 

Plugin homepage [WordPress Post Expirator](http://postexpirator.tuxdocs.net).

**[postexpirator] shortcode attributes**

* type - defaults to full - valid options are full,date,time
* dateformat - format set here will override the value set on the settings page
* timeformat - format set here will override the value set on the settings page 

This plugin was forked from the original because it broke due to changes to WordPress core.
The new version attempts to cleanup code and remove settings unnecessary to DBC's use.
Particularly, this plugin will only expire 'post' post types and provide UI only for posts.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
that you can use any other version of the GPL.
 
== Wordpress MU ==

This plugin is compataibile with Wordpress MU 1.5+, however currently it will not work in the mu-plugins folder due to the plugin activation 
functions.

== Credits ==

Plugin is based on the orginial [Expiration Date](http://www.hostscope.com/wordpress-plugins/the-expirationdate-wordpress-plugin/) plugin by jrrl. 

Post Expirator was originally developed buy Aaron Axelsen (http://postexpirator.tuxdocs.net/) and
hosted at http://wordpress.org/extend/plugins/post-expirator/

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

**Version 1.0**

* Initial Release
