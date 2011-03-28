=== White Label CMS ===

Contributors: B.Porteous, T.Dean, M.Rupisan
Plugin Name: White Label CMS
Plugin URI: http://www.videousermanuals.com/white-label-cms/
Tags: cms, custom, admin, branding, dashboard, administration, plugin, login, client, menu, navigation, appearance, menus, widgets
Author URI: http://www.videousermanuals.com
Author:  Video User Manuals
Donate link: 
Requires at least: 2.8 
Tested up to: 3.0
Stable tag: 1.2
Version: 1.2

Allows customization of dashboard and logos, removal of menus, giving editors access to widgets and menus plus lots more.

== Description ==
The White Label CMS plugin is for developers who want to give their clients a more personalised and less confusing CMS.

You have the ability to choose which menus appear.  We have 3 CMS profiles of Website, Blog or Custom so you can modify the menu system to suit the CMS purpose. These only apply to user role of Editor and below.

For WordPress 3 users, you can give Editors access to the Menu and Widgets, but the switch theme option will not appear.

White Label CMS allows you to remove all the panels from the WordPress dashboard and insert your own panel, which you can use to write a personalised message to your client and link to the important elements in the CMS.

It also allows you to add custom logos to the header and footer as well as the all important login page, giving your client a better branded experience of using their new website.

There is also the option to hide the nag updates as well.

No longer will you have to tell your clients to ignore the dashboard!

For a video overview of this plugin please visit the [White Label CMS](http://www.videousermanuals.com/white-label-cms/ "White Label CMS") home hosted on the [WordPress Manual Plugin](http://www.videousermanuals.com/ "WordPress Manual Plugin") website.

== Installation ==
1. Download the White Label CMS plugin
2. Upload it to your plugins directory
3. Go to the plugins directory and activate the plugin
4. Go to Settings->White Label CMS and use the menu system to change the default values.

Please note:
All custom images should be uploaded to the current themes image directory.
The default filenames for the logo images are:
Header Logo: custom-logo.gif
Footer Logo: custom-logo.gif
Custom Login Logo: custom-login-logo.gif

In order to specify a with for the Header Logo or Footer Logo, please ensure you append px to the number.

== Upgrade Notice ==
Allow editors access to widgets and menus in appearance.

== Screenshots ==
1. An example of a custom login
2. An example of how your clients dashboard could look
3. Customize which menus appear for editors
4. Simple menu options
5. New feature - Show Widgets and Menus for editors

== Changelog ==
= 1.2 =
Ability to show Menu & Widgets menu for Editors
Removed WordPress link and ALT text from login page.
Custom css for forgotten link on login page.
Tested on multi user sites.
Fixed a bug which was stopping the Profile from appearing.

= 1.1 =
Ability to remove menus
Added widths for header and footer logos.

= 1.0.5 =
Updated terminology

= 1.0.4 =
Updated custom login image file height

= 1.0.3 =
Updated logo filename

= 1.0.2 =
Added update log!

== Frequently Asked Questions ==
= Who is this plugin for?=
For developers who handover websites to their clients and use WordPress as a CMS.

=How to I add links to my own panel?=
Your custom panel accepts html, so just write the markup as you normally would in html.

=How do I remove menu items?=
Click on the Remove Menus section and either choose a CMS profile, or manually select which menu items to be removed. Please not this will only effect user roles of Editor and below. So you will need to logout in order to see the difference.

=How do you recommend using this plugin?=
We have been using this for a number of months now, and we have found clients respond best when it is set up in the following fashion:

* Use the clients logo for the login (it gives the CMS a bit of a wow factor!)
* Use the clients logo in the header
* Use your own logo in the footer, and place a link back to your website
* Remove all panels from the dashboard (they have a lot of information that just confuses the client)
* Add your own panel. Personalise the experience for your client by welcoming them to their website. Provide links to the most relevant elements in the CMS. Provide a link back to your support system if you have one.

=Appearance Menu=
With WordPress 3 comes the much need Menus option. However, this sits inside the Appearance menu which is hidden to editors. You can you make the Menus and Widgets menus available to editors, but can keep the switch theme menu removed.

Please note: The plugin works by granting Editor's the 'switch_themes' and 'edit_theme_options' privilege which gives them access to the Appearance menu, but removes the switch themes menu from the Appearance menu and the WordPress 3 Right Now dashboard. However it could still be possible for a Editor to switch themes, if they knew the direct url path. Unlikely, but you should be aware of this before you choose to enable these options.

Menus is only available to WordPress 3 users.

If you see menus like background or header, then you must modify your theme in order to remove them.

When the plugin is uninstalled, the Editor privileges are reset to their original values.

=How do I use it on Multi Site?=
* You must install the plugin network wide in order for it to work on all sites.
* You must save the options on each new site in order for the default options to appear.
* You can have separate login logos for each mini site. Simply change the filename, and upload it to the relevant theme.

=Troubleshooting=
I installed the plugin and the logos disappear?: You need to upload your logos to you current themes images directory.

The menus have not changes?: Make sure you are logged in as the editor

Lost Password CSS not working?: Make sure you use the example format. The color must be the last css style and it must not have a closing ; as !important is appended to the end of the style to overwrite and existing style.

== Donations ==