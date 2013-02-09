=== Post Expirator ===
Contributors: axelseaa
Tags: expire, posts, pages, schedule
Requires at least: 3.2
Tested up to: 3.4
Stable tag: 1.6.2

Allows you to add an expiration date to posts which you can configure to either delete the post, change it to a draft, or update the 
post categories.

== Description ==

The Post Expirator plugin allows the user to set expiration dates for both posts and pages.  There is a configuration option page in the plugins 
area that will allow you to seperataly control whether or not posts/pages are either deleted or changed to draft status.  Additionally you can
also choose to have the post categories change at expiration time.  If you choose to change the post category, the default action of changing 
the status will be ignored.

The plugin hooks into the wp cron processes and runs every minute by default, but can be configured to use any cron schedule (hourly, twicedaily, daily, etc).

The expiration date can be displayed within the actual post by using the [postexpirator] tag.  The format attribute will override the plugin 
default display format.  See the [PHP Date Function](http://us2.php.net/manual/en/function.date.php) for valid date/time format options. 

Plugin homepage [WordPress Post Expirator](http://postexpirator.tuxdocs.net).

New! [Feature Requests](http://postexpirator.uservoice.com) Please enter all feature requests here.  Requests entered via the plugin website or support forum may be missed.

**[postexpirator] shortcode attributes**

* type - defaults to full - valid options are full,date,time
* dateformat - format set here will override the value set on the settings page
* timeformat - format set here will override the value set on the settings page 

This plugin is fully compatible with WordPress Multisite Mode.

== Installation ==

This section describes how to install the plugin and get it working.

1. Unzip the plugin contents to the `/wp-content/plugins/post-expirator/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Adding expiration date to a post
2. Viewing the exipiration dates on the post overview screen
3. Settings screen

== Changelog ==

**Version 1.6.2**

* Added the ability to configure the post expirator to be enabled by default for all new posts
* Changed some instances of mktime to time
* Fixed missing global call for MS installs

**Version 1.6.1**

* Tweaked error messages, removed clicks for reset cron event
* Switched cron schedule functions to use "current_time('timestamp')"
* Cleaned up default values code
* Added option to allow user to select any cron schedule (minute, hourly, twicedaily, daily) - including other defined schedules
* Added option to set default expiration duration - options are none, custom, or publish time
* Code cleanup - php notice

**Version 1.6**

* Fixed invalid html
* Fixed i18n issues with dates
* Fixed problem when using "Network Activate" - reworked plugin activation process
* Replaced "Upgrade" tab with new "Diagnostics" tab
* Reworked expire logic to limit the number of sql queries needed
* Added debugging
* Various code cleanup

**Version 1.5.4**

* Cleaned up deprecated function calls

**Version 1.5.3**

* Fixed bug with sql expiration query (props to Robert & John)

**Version 1.5.2**

* Fixed bug with shortcode that was displaying the expiration date in the incorrect timezone
* Fixed typo on settings page with incorrect shortcode name

**Version 1.5.1**

* Fixed bug that was not allow custom post types to work

**Version 1.5**

* Moved Expirator Box to Sidebar and cleaned up meta code
* Added ability to expire post to category

**Version 1.4.3**

* Fixed issue with 3.0 multisite detection

**Version 1.4.2**

* Added post expirator POT to /languages folder
* Fixed issue with plugin admin navigation
* Fixed timezone issue on plugin options screen

**Version 1.4.1**

* Added support for custom post types (Thanks Thierry)
* Added i18n support (Thanks Thierry)
* Fixed issue where expiration date was not shown in the correct timezone in the footer
* Fixed issue where on some systems the expiration did not happen when scheduled

**Version 1.4**

NOTE: After upgrading, you may need to reset the cron schedules.  Following onscreen notice if prompted.  Previously scheduled posts will not be updated, they will be deleted referncing the old timezone setting.  If you wish to update them, you will need to manually update the expiration time.

* Fixed compatability issues with Wordpress - plugin was originally coded for WPMU - should now work on both
* Added ability to schedule post expiration by minute
* Fixed timezone - now uses the same timezone as configured by the blog

**Version 1.3.1**

* Fixed sporadic issue of expired posts not being removed

**Version 1.3**

* Expiration date is now retained across all post status changes
* Modified date/time format options for shortcode postexpirator tag
* Added the ability to add text automatically to the post footer if expiration date is set

**Version 1.2.1**

* Fixed issue with display date format not being recognized after upgrade

**Version 1.2**

* Changed wording from "Expiration Date" to "Post Expirator" and moved the configuration options to the "Settings" tab.
* Added shortcode tag [postexpirator] to display the post expiration date within the post
** Added new setting for the default format
* Fixed bug where expiration date was removed when a post was auto saved

**Version 1.1**

* Expired posts retain expiration date

**Version 1.0**

* Initial Release

== Upgrade Notice ==

= 1.6.1 =
Tweaked error messages, added option to allow user to select cron schedule and set default exiration duration

= 1.6 =
Fixed invalid html
Fixed i18n issues with dates
Fixed problem when using "Network Activate" - reworked plugin activation process
Replaced "Upgrade" tab with new "Diagnostics" tab
Reworked expire logic to limit the number of sql queries needed
Added debugging

= 1.5.4 =
Cleaned up deprecated function calls

= 1.5.3 =
Fixed bug with sql expiration query (props to Robert & John)

= 1.5.2 =
Fixed shortcode timezone issue
