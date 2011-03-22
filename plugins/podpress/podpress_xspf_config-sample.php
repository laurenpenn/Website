<?php
/* podPress Options File for podPress v8.8.9 or higher */
/* file rev. 2.0 */
// If you want to use custom skins for the XSPF players then edit this file and rename it to podpress_xspf_config.php, create a folder called podpress_options as a sub folder of the plugins folder e.g. /wp-content/plugins/podpress_options (by default part of the PODPRESS_OPTIONS_URL) and copy this file to this folder.

// Begin - XSPF Jukebox player configuration:
// It is possible to define these constants for each blog in a multi site blog installation. All of these constants are ending with an underscore and a number. This number is the blog ID. 1 is the ID of the first resp. main blog. In a single blog installation the blog as the ID 1.

// Before you use these options please read the player documentation http://lacymorrow.com/projects/jukebox/xspfdoc.html and the skin documentation http://lacymorrow.com/projects/jukebox/skindoc.html
// podPress uses a derivate of the SlimOriginal skin.

// If PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE is defined then the skin files in the dynamic and dynamic_slim folder will not 
// be overwritten by changes in the widgets settings. If this is defined as TRUE then saving the widgets settings will only affect the size of the <object> of the XSPF players. That is why it will be necessary to 
// define the further constants (see below) right.
if ( ! defined('PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_1') ) { define('PODPRESS_XSPF_PLAYER_USE_CUSTOM_SKINFILE_1', TRUE); }

// If you want to use a custom skin for the XSPF player then uncomment the line above and place the custom skin files in a sub folder of the podPress options folder e.g. /wp-content/plugins/podpress_options/xspf_jukebox/custom/ 
// and define the URLs of the custom skin files with the constants PODPRESS_XSPF_SKIN_URL_1 and PODPRESS_XSPF_SLIM_SKIN_URL_1.
// These folders should contain skin files - for each width and height combination one. The width and height values in the name(s) of the skin file(s) need to be the same as in the widget settings.
// The file name of a skin file consists 3 parts: It starts with the word 'skin', the blog ID number (if you are using a one blog installation or for the main blog it is 1 ) and the dimensions (width x height) of the player for which the skin is designed.
// For instance skin_2_230x210.xml would be a skin file for a sub blog in a multi blog installation (of WP 3.x) with the ID 2, the width 230 pixel and the height 210 pixel.
// If you want to use such custom skin files then it is necessary to define the URL(s) of the file(s). The following exemplary lines should show how one can do that. In this example the PODPRESS_XSPF_SKIN_URL or PODPRESS_XSPF_SLIM_SKIN_URL constants
//  consists of the PODPRESS_OPTIONS_URL and two further sub folder names. The PODPRESS_OPTIONS_URL is defined in the podpress.php file and by default it is a sub folder of the plugins/ folder with the 
// name podpress_options/. You may modify the sub folder names if you want to. If these folders do not exist, it is necessary to create them manually, too. But it is also possible to define a custom URL e.g. 
// 'http://www.example.com/wp-content/plugins/podpress_options/xspf_jukebox/custom'. In other words it is possbile to place the skin files somewhere else in the folders of your blog, too. Make sure that the folders and files are readable (0644).
// The URLs should not end with a slash.
if ( ! defined('PODPRESS_XSPF_SKIN_URL_1' ) ) { define( 'PODPRESS_XSPF_SKIN_URL_1', PODPRESS_OPTIONS_URL.'/xspf_options/custom' ); }
if ( ! defined('PODPRESS_XSPF_SLIM_SKIN_URL_1' ) ) { define( 'PODPRESS_XSPF_SLIM_SKIN_URL_1', PODPRESS_OPTIONS_URL.'/xspf_options/custom_slim' ); }

// The following constants are no URL. That are the paths to the folders of the skins. They need to point to the same folders as the URL above.
if ( ! defined('PODPRESS_XSPF_SKIN_DIR_1' ) ) { define( 'PODPRESS_XSPF_SKIN_DIR_1', PODPRESS_OPTIONS_DIR.'/xspf_options/custom' ); }
if ( ! defined('PODPRESS_XSPF_SLIM_SKIN_DIR_1' ) ) { define( 'PODPRESS_XSPF_SLIM_SKIN_DIR_1', PODPRESS_OPTIONS_DIR.'/xspf_options/custom_slim' ); }

//  BTW: Since v8.8.8 podPress hands dynamically the skin information to the player and does not use static .xml files anymore. Examples .xml of the default skin are in the folder /wp-content/plugins/podpress/players/xspf_jukebox/skin_and_variables_files_examples/.


// Background-color of the player <object> (if this constant is not defined then the color is FFFFFF by default.)
if ( ! defined( 'PODPRESS_XSPF_BACKGROUND_COLOR_1' ) ) { define( 'PODPRESS_XSPF_BACKGROUND_COLOR_1', 'FFFFFF' ); }

// If you want to let the player show the episode preview images then uncomment the following line (This has only an effect if you are using the default player skins of podPress):
if ( ! defined('PODPRESS_XSPF_SHOW_PREVIEW_IMAGE_1') ) { define('PODPRESS_XSPF_SHOW_PREVIEW_IMAGE_1', TRUE); }

// podPress uses the parameters: &autoload=true&autoplay=false&loaded=true to load the XSPF player
// If you want to use custom parameters then uncomment the following lines and edit or replace the variables.txt files in the folders /podpress/players/xspf_jukebox/dynamic/ and /podpress/players/xspf_jukebox/dynamic_slim/.
//~ if ( ! defined('PODPRESS_XSPF_USE_CUSTOM_VARIABLES_1') ) { define('PODPRESS_XSPF_USE_CUSTOM_VARIABLES_1', TRUE); }
//~ if ( ! defined('PODPRESS_XSPF_SLIM_USE_CUSTOM_VARIABLES_1') ) { define('PODPRESS_XSPF_SLIM_USE_CUSTOM_VARIABLES_1', TRUE); }

// Remove the comment characters of the following line to define a custom URL for the XSPF player. The URL has to be an URL to a playlist which is on the same domain/server as your blog! 
// This constant overwrites the playlist URLs of all XSPF player widget of one blog! (But you can define via the widgets settings an individual URL for each XSPF widget.)
//~ if ( ! defined( 'PODPRESS_CUSTOM_XSPF_URL_1' ) ) { define( 'PODPRESS_CUSTOM_XSPF_URL_1', 'http://www.example.com/?feed=playlist.xspf' ); }
// End - XSPF Jukebox player configuration
?>