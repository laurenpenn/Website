=== The Piecemaker ===
Contributors: n33rav
Tags: widget, sidebar, piecemaker, the piecemaker, flash, slideshow, image gallery
Donate Link: http://www.vareen.co.cc/
Requires at least: 2.7
Tested up to: 3.0.1
Stable tag: 1.1

The Piecemaker allows you to display famous Piecemaker 3D flash gallery by modular web in your wordpress blog with ease.

== Description ==

The Piecemaker allows you to add famous Piecemaker 3D flash gallery by http://www.modularweb.net/ to your wordpress blog.

You either can select one category to display it's articles as slides of gallery or can configure gallery to display your custom images with custom description tag. Limited html tag support is available for description text. You can add as much images as you want to slideshow.

If you select category to display it's articles as slides, you have to add extra custom field with each post named 'the_piecemaker_image' and relative URL of image from wordpress root as it's value. Post excerpt will be used as description of image.

It has number of options available to change color skim and animation to customize as per your test. Some of them are

1. Segments
1. Tween Time
1. Tween Delay
1. Tween Type
1. Z Distance
1. Expand
1. Inner Color
1. Text Background Color
1. Shadow Darkness
1. Text Distance
1. Autoplay

For detailed documentation visit http://www.vareen.co.cc/documentation/the-piecemaker-for-wordpress-%E2%80%93-documentation/

== Installation ==

1. Upload to the "the-piecemaker" directory to `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Publish The Piecemaker widget at suitable sidebar position or place `<?php if (function_exists(display_the_piecemaker())) display_the_piecemaker(); ?>` in your templates

== Changelog ==

= 1.1 =
* Custom read more link
* Fix cache issue

= 1.0 =
* Initial version.
