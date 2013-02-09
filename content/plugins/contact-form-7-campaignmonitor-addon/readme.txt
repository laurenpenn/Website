=== Contact Form 7 - Campaign Monitor Addon ===
Contributors: joshuabettigole
Donate link: http://www.bettigole.us/donate/
Tags: Campaign Monitor, Contact Form 7, Newsletter, Opt In, Email Marketing
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: 1.06

Add the capability to create newsletter opt-in forms with Contact Form 7. Automatically submit subscribers to predetermined lists in Campaign Monitor.

== Description ==

> **This plugin requires Contact Form 7, version 3.1**

- - -

The Contact Form 7 - Campaign Monitor Addon plugin adds functionality into Contact Form 7 generated forms to automatically submit subscribers to a predetermined list within a Campaign Monitor client account. The plugin settings are configured on a per-form basis on the Contact Form 7 configuration pages.

> If you like this plugin, consider [donating](http://www.bettigole.us/donate/) to help me offset the time spent working on it. In return, I promise not to bother you with nag dialogs on your Wordpress admin pages!


### Requirements

#### WordPress
This plugin was built and tested on WordPress version 3.3.1 It should work with version 3.2, but this configuration is untested. Earlier versions are not supported by Contact Form 7, therefore, can not be supported by this plugin.

#### Contact Form 7
Contact Form 7 provides the form configuration and processing functionality necessary for this plugin to work. There are no configuration options for this plugin outside of the Contact Form 7 configuration screens. You will also need a basic understanding of how to configure Contact Form 7. View the Contact Form 7 plugin [documentation](http://contactform7.com/docs/) for an explanation of fields and how to configure forms.

#### Campaign Monitor Reseller Account
The Campaign Monitor API (Application Programming Interface) requires the API Key provided to members with accounts directly on Campaign Monitor. Client accounts do not have access to this key. If you did not sign up directly with Campaign Monitor, you will need to ask your service provider for the API Key.

== Installation ==

1. Upload `contact-form-7-campaign-monitor-addon` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Obtain API Key, API Client ID and the API Subscriber List ID from Campaign Monitor
1. Enable and configure the Campaign Monitor section on the Contact Form 7 "Edit" page.

For detailed configuration, [download the complete manual](http://www.bettigole.us/downloads/cf7_cm_user_manual.pdf).

== Frequently Asked Questions ==

= My configuration disappeared when I updated =

Unfortunately, Contact Form 7 changed the way it handles the ID of your form. The CM settings were dependent on the old ID prior to your update. You will either need to reenter your settings, or go in the database, look for cf7_cm_# in the wp_options table, and update the # to the new ID of your form.

= Does this plugin allow for an opt-in checkbox =

Yes, by including a checkbox tag in the form such as:
[checkbox add-email-list default:1 "Add Me To Your Mailing List"]

Then add [add-email-list] to the "Required Acceptance Field" option in the Campaign Monitor section.

= Can I submit to multiple lists =

Yes, in the List ID field, add all desired list IDs separated by commas.

= Can I let the visitor select which lists they belong to =

Yes, by including a checkbox tag in the form such as:
[checkbox addmetolists "List 1|3126f7e296c347fa53128df941a4f20c" "List 2|1ee18c1ac11ab6eb81624ae1c713a572" "List 3|33dad8d632595e2339b523839fe6d4e9"]

Then add [addmetolists] to the List ID field.

= Where is the settings page =

There is no settings page specifically for this plugin. Configuration occurs within the Contact Form 7 form administration pages. Each form gets its own configuration.

= This plugin doesn't appear to have been updated in a while =

Simply put, it doesn't need to. Very little of this plugin relies on Wordpress core functionality, and Campaign Monitor doesn't change their API all that often, so unless something is completely broken, updating it every other week isn't necessary.

== Screenshots ==

1. Campaign Monitor Addon Configuration
2. Form Configuration with Opt In

== Changelog ==

= 1.04 =
- Fixed issues related to the Contact Form 7, v3.1 update which prevented submitting data to Campaign Monitor.
- Updated admin config to use a repositionable meta box 

= 1.02 =
- Fixed incompatibility issue when installed along with other Campaign Monitor plugins

= 1.00 =
- Added custom field handling (Courtesy of Josh Middleton). Added multiple list capability. Updated to latest Campaign Monitor API

= 0.99 =
- First Release

== ToDo ==

* Add support for the same languages supported by Contact Form 7. (In need of assistance with this)
* White Label the configuration options.
