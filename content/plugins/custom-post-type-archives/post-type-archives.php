<?php
/*
Plugin Name: Custom Post Type Archives
Plugin URI: http://ratvars.com/custom-post-type-archives
Description: Enables archives (with feeds and paging) for custom post types
Author: Rolands Atvars
Version: 1.5.1
Author URI: http://ratvars.com
*/
add_action('admin_menu', 'pta_create_menu');

function pta_create_menu() {
	global $pta_settings_page;
	
	$pta_settings_page = add_submenu_page( // will add submenu under 'Settings' menu
		'options-general.php',
		'Post Type Archives',
		'Post Type Archives',
		'administrator',
		'post-type-archives',
		'pta_render_menu'
	);
	
	add_action('admin_init', 'pta_register_settings');
	
	// add_meta_box(
	// 		'pta-menu-box', // html id attr
	// 		'Post Type Archive Links', // title for box
	// 		'pta_render_meta_box', // callback
	// 		'nav-menus', // page where to show the meta box
	// 		'side' // place where to show meta box in page
	// 	);
}

function pta_contextual_menu($contextual_help, $screen_id, $screen) {
	global $pta_settings_page;
	
	if($screen_id == $pta_settings_page) {
		include dirname(__FILE__) . '/contextual_help.php';
	}
}
add_action('contextual_help', 'pta_contextual_menu', 10, 3);

// function pta_render_meta_box() {
// 	global $_nav_menu_placeholder, $nav_menu_selected_id;
// 	$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
// 	
// 	include dirname(__FILE__) . '/meta-box.php';
// }

/**
 * Will register all options for this plugin.
 * This way WordPress will deal with all the saving and sanitizing itself.
 * Check 'pta_get_settings' for brief setting explanations.
 * @see pta_get_settings
 */
function pta_register_settings() {
	register_setting('pta-setting-group', 'pta-url-base');
	register_setting('pta-setting-group', 'pta-use-rewrite-slug');
	register_setting('pta-setting-group', 'pta-title');
	register_setting('pta-setting-group', 'pta-template-pattern');
	register_setting('pta-setting-group', 'pta-fallback-template');
	register_setting('pta-setting-group', 'pta-enable-feed-links');
	register_setting('pta-setting-group', 'pta-enabled-post-type-archives');
	register_setting('pta-setting-group', 'pta-enabled-post-type-customisations');
}

/**
 * Will get either all settings for this plugin or just the specified one.
 * Will store settings in static variable so that plugin doesn't have to check the values
 * each time someone requests a random option.
 * Possible options:
 *  - url_base : URL prefix for the custom post type archive.
 *  - use_rewrite_slug : whether to use the rewrite slug for custom post type
 *  - title : <title> attribute's value for custom post type archives.
 *    	Will replace {POST_TYPE_NAME} with post types name and {POST_TYPE_SINGULAR_NAME} with post types singular name
 *  - template_pattern : pattern for custom post type archive template. Will replace {POST_TYPE} with current post type
 *  - fallback_template : if no custom post template is found will try to load this file
 *  - enable_feed_links : whether to put links to post type feeds in the HTML <head>
 *  - enabled_post_type_archives : post types with archives enabled
 *  - enabled_post_type_customisations : customisations per post type (like title and rewrite slug)
 * @param string $option optional option name to return
 * @return mixed either all the options (if $option is false or non existing) or specified $option
 */
function pta_get_settings($option = false) {
	static $settings; // static to store settings so that plugin doesn't have to pull and parse them on every call
	
	if(!isset($settings)) { // this, probably, is the first function call in this page load
		$url_base = get_option('pta-url-base', '/post-type');
		$use_rewrite_slug = (bool) get_option('pta-use-rewrite-slug', true);
		$title = get_option('pta-title', '');
		$template_pattern = get_option('pta-template-pattern', '{POST_TYPE}.php');
		$fallback_template = get_option('pta-fallback-template', 'index.php');
		$enable_feed_links = (bool) get_option('pta-enable-feed-links', true);
		$enabled_post_type_archives = get_option('pta-enabled-post-type-archives', array());
		$enabled_post_type_customisations = get_option('pta-enabled-post-type-customisations', array());

		if(!is_array($enabled_post_type_archives)) // this has to be an array
			$enabled_post_type_archives = array();
		if(!is_array($enabled_post_type_customisations))
			$enabled_post_type_customisations = array();
		if(empty($template_pattern))
			$template_pattern = '{POST_TYPE}.php';
		if(empty($fallback_template)) // I think that index.php should always be present
			$fallback_template = 'index.php';
		$url_base = trim($url_base, '/'); // trim un-necessary slashes
		$title = htmlspecialchars(trim($title)); // no HTML and whitespaces (in beginning and end)

		$settings = compact(
			'url_base',
			'use_rewrite_slug',
			'title',
			'template_pattern',
			'fallback_template',
			'enable_feed_links',
			'enabled_post_type_archives',
			'enabled_post_type_customisations'
		);
	}
	
	return ($option == false or !isset($settings[$option])) ? $settings : $settings[$option];
}

function pta_render_menu() {
	extract(pta_get_settings());
	
	include dirname(__FILE__) . '/options.php';
}

/**
 * This is a callback function to enable custom post type archive pages.
 * This function will add new rewrite rules to the global $wp_rewrite variable.
 * Custom rewrite rules will support feeds and paging without any extra coding. (yeeey)
 * This function will be called by 'generate_rewrite_rules' filter.
 * Note that it is necessary to flush permalinks for this method to be called.
 * It might be useful to know that these rewrite rules will create new param called 'post_type_index'.
 * It can be used to check whether we are in custom post archive.
 * @param WP_Rewrite $wp_rewrite object that contains all the routing data
 * @return WP_Rewrite modified $wp_rewrite with my custom rewrite rules
 */
function pta_register_post_type_rewrite_rules($wp_rewrite) {
	extract(pta_get_settings());
	if(empty($enabled_post_type_archives)) return $wp_rewrite; // if we don't have any enabled post type archives then might as well quit
	
	$custom_rules = array();
	$url_base = ($url_base == '') ? $url_base : $url_base . '/';
	
	if($use_rewrite_slug) {
		$customisations = $enabled_post_type_customisations; // just to make it shorter
		
		foreach($enabled_post_type_archives as $post_type_slug) {
			$post_type = get_post_type_object($post_type_slug);
		
			if(!isset($post_type))
				continue; // omg post type doesn't exist - continue with next one
			
			if(isset($customisations[$post_type_slug]) and !empty($customisations[$post_type_slug]['rewrite_slug']))
				$post_type = $enabled_post_type_customisations[$post_type_slug]['rewrite_slug'];
			elseif(!empty($post_type->rewrite) and !empty($post_type->rewrite['slug']))
				$post_type = $post_type->rewrite['slug'];
			else
				$post_type = $post_type_slug;
				
			$custom_rules["$url_base($post_type)/([0-9]+)/([0-9]{1,2})/([0-9]{1,2})/?$"] = // enable listing by day
				"index.php?post_type_index=1&post_type=$post_type_slug&year=" . $wp_rewrite->preg_index(2) . '&monthnum=' . $wp_rewrite->preg_index(3) . '&day=' . $wp_rewrite->preg_index(4);
			$custom_rules["$url_base($post_type)/([0-9]+)/([0-9]{1,2})/?$"] = // enabled listing by month
				"index.php?post_type_index=1&post_type=$post_type_slug&year=" . $wp_rewrite->preg_index(2) . '&monthnum=' . $wp_rewrite->preg_index(3);
			$custom_rules["$url_base($post_type)/([0-9]+/?$)"] = // enable listing by year
				"index.php?post_type_index=1&post_type=$post_type_slug&year=" . $wp_rewrite->preg_index(2);
			$custom_rules["$url_base($post_type)/page/?([0-9]{1,})/?$"] = // enable paging
				"index.php?post_type_index=1&post_type=$post_type_slug&paged=" . $wp_rewrite->preg_index(2);
			$custom_rules["$url_base($post_type)/(feed|rdf|rss|rss2|atom)/?$"] =// enable feeds
				"index.php?post_type_index=1&post_type=$post_type_slug&feed=" . $wp_rewrite->preg_index(2);
			$custom_rules["$url_base($post_type)/?$"] = // enable the index page
				"index.php?post_type_index=1&post_type=$post_type_slug";
		}
	}
	else {
		$enabled_post_type_archives = implode('|', $enabled_post_type_archives);
		
		$custom_rules = array(
			"$url_base($enabled_post_type_archives)/([0-9]+)/([0-9]{1,2})/([0-9]{1,2})/?$" =>
				'index.php?post_type_index=1&post_type=' . $wp_rewrite->preg_index(1) . '&year=' . $wp_rewrite->preg_index(2) . '&monthnum=' . $wp_rewrite->preg_index(3) . '&day=' . $wp_rewrite->preg_index(4),
			"$url_base($enabled_post_type_archives)/([0-9]+)/([0-9]{1,2})/?$" =>
				'index.php?post_type_index=1&post_type=' . $wp_rewrite->preg_index(1) . '&year=' . $wp_rewrite->preg_index(2) . '&monthnum=' . $wp_rewrite->preg_index(3),
			"$url_base($enabled_post_type_archives)/([0-9]+)/?$" =>
				'index.php?post_type_index=1&post_type=' . $wp_rewrite->preg_index(1) . '&year=' . $wp_rewrite->preg_index(2),
			"$url_base($enabled_post_type_archives)/page/?([0-9]{1,})/?$" => // enable paging
				'index.php?post_type_index=1&post_type=' . $wp_rewrite->preg_index(1) . '&paged=' . $wp_rewrite->preg_index(2),
			"$url_base($enabled_post_type_archives)/(feed|rdf|rss|rss2|atom)/?$" => // enable feeds
				'index.php?post_type_index=1&post_type=' . $wp_rewrite->preg_index(1) . '&feed=' . $wp_rewrite->preg_index(2),
			"$url_base($enabled_post_type_archives)/?$" => // enable the index page
				'index.php?post_type_index=1&post_type=' . $wp_rewrite->preg_index(1)
		);
	}
	
	$wp_rewrite->rules = array_merge($custom_rules, $wp_rewrite->rules); // merge existing rules with custom ones
	
	return $wp_rewrite;
}
add_filter('generate_rewrite_rules', 'pta_register_post_type_rewrite_rules', 100);

/**
 * This hook callback function will try to tell WordPress to include proper template
 * when in a custom post type archive. (created by 'pta_register_post_type_rewrite_rules')
 * Function will use locate_template to check whether the template exists.
 * Hook that calls this function is 'template_include'
 * @see pta_register_post_type_rewrite_rules
 * @uses locate_template
 * @link http://codex.wordpress.org/Function_Reference/locate_template
 * @param string $template path to template that WordPress thinks is necessary to include
 * @return string path to template to include. Will be changed if we're in custom post type area.
 */
function pta_register_post_type_redirects($template, $force_post_type = false) {
	if(!pta_is_post_type_archive() and $force_post_type === false) return $template;
	
	if(is_404()) // we are in a custom post type archive but there were no posts found for current request
		return $template; // we just continue with WordPress idea then
	
	extract(pta_get_settings()); // let's get the settings first
	
	$current_post_type =
		($force_post_type === false) ? get_query_var('post_type') : $force_post_type; // and get current custom post
	$template_pattern = str_replace('{POST_TYPE}', $current_post_type, $template_pattern); // create our template pattern
	$template_path = locate_template((array) $template_pattern); // and try to find the template file
	$fallback_template_path = locate_template((array) $fallback_template); // for safety lets find fallback template
	
	if($template_path != '')
		return $template_path;
	elseif($fallback_template_path != '')
		return $fallback_template_path;
	
	return $template; // if everything else fails then continue with WordPress idea
}
add_filter('template_include', 'pta_register_post_type_redirects');

/**
 * For some reason WordPress thinks that custom post archives are is_home()
 * They are not. Must fix this!
 */
function pta_fix_post_type_context() {
	if(pta_is_post_type_archive()) {
		global $wp_query;
		
		$wp_query->is_home = false;
	}
}
add_filter('template_redirect', 'pta_fix_post_type_context');

/**
 * By default WordPress will have no title for custom post type archives.
 * This is where this fix comes in! ^_^
 * @uses option 'Title'
 * Will only change the title if we are in post type archive.
 * Won't change the title if saved 'Title' options value is empty.
 * Won't change the title if we are on non existing post type (don't think that this can happen though :P)
 * Will replace {POST_TYPE_NAME} with custom post types name.
 * Will replace {POST_TYPE_SINGULAR_NAME} with custom post types singular name.
 * This filter will tolorate the $seplocation.
 * @param string $wp_title prepared WordPress title (will be empty unless some other plugin wants to be smart)
 * @param string $sep the defined seperator
 * @param string $seplocation where is the seperator placed.
 */
function pta_fix_wp_title($wp_title, $sep, $seplocation) {
	if(pta_is_post_type_archive() and !is_404()) { // do stuff if we are in post type archive that has posts (is not 404)
		$query_post_type = get_query_var('post_type');
		$title = pta_get_settings('title'); // let's get the title now
		$customisations = pta_get_settings('enabled_post_type_customisations');
		
		if(isset($customisations[$query_post_type]) and !empty($customisations[$query_post_type]['title']))
			$title = $customisations[$query_post_type]['title'];
		if(empty($title)) return $wp_title; // ups - it's empty. Don't continue!
		
		$current_post_type = get_post_types(array(), 'object'); // lets get all the post types
		if(!isset($current_post_type[$query_post_type]))
			return $wp_title; // woops current one isn't in that list. END NOW!
		
		$current_post_type = $current_post_type[get_query_var('post_type')]; // ok it exists. Select it!
		
		$searches = array(
			'{POST_TYPE_NAME}',
			'{POST_TYPE_SINGULAR_NAME}',
			'{SEP}',
			'{SEP_LEFT_SPACE}',
			'{SEP_RIGHT_SPACE}',
			'{SEP_SPACED}'
		);
		$replaces = array(
			$current_post_type->labels->name,
			$current_post_type->labels->singular_name,
			$sep,
			" $sep",
			"$sep ",
			" $sep "
		);
		
		$wp_title = str_replace($searches, $replaces, $title);
	}
	
	return $wp_title; // we aren't in post type archive - quit FAST!
}
add_filter('wp_title', 'pta_fix_wp_title', 10, 3);

/**
 * Adds additional body classes 'post-type-archive' and 'post-type-{POST_TYPE}-archive'.
 * @param array $classes already added body classes
 * @return array body classes with additional body classes
 */
function pta_fix_body_class($classes) {
	if(!pta_is_post_type_archive()) return $classes;
	
	$archive_classes = array(
		'post-type-archive',
		'post-type-' . get_query_var('post_type') . '-archive'
	);
	
	return array_merge($classes, $archive_classes);
}
add_filter('body_class', 'pta_fix_body_class', 10);

/**
 * Function will add feed link for custom post type archives.
 * It will do that if 'automatic-feed-links' are enabled for theme or
 * if user has set 'enable_feed_links' in this plugins options.
 * The only way to disable feed links if 'automatic-feed-links' is enabled, is to use 'pta_add_feed_link' filter.
 * Is this a good idea? Should 'enable_feed_links' option be more important?
 */
function pta_add_feed_link() {
	if(!pta_is_post_type_archive()) return; // if we are not in post type archive - quit!
	
	// only add the feed link if theme supports auto feed links or user enabled it
	$add_feed_link = (current_theme_supports('automatic-feed-links') or pta_get_settings('enable_feed_links'));
	$add_feed_link = apply_filters('pta_add_feed_link', $add_feed_link);
	
	if($add_feed_link) {
		$post_type = get_post_type_object(get_query_var('post_type')); // let's get the post type object
		
		if(!isset($post_type)) return; // some kind of bug... don't continue
		
		$post_type = $post_type->labels->name; // and get only the name (in plural)
		$title = get_bloginfo('name') . ' &raquo; ' . $post_type . ' Feed'; // this should be a standart URL
		
		// and combine the link. This should be valid and correct
		echo '<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . get_the_post_type_permalink(get_query_var('post_type')) . 'feed" />';
	}
}
add_action('wp_head', 'pta_add_feed_link');

/**
 * Similar function as 'is_home' or 'is_category'.
 * Will tell you if this is custom post type archive page.
 * Uses the custom rewrite rule created 'post_type_index' param.
 * If that param exists and equals to '1' then this is a custom post type archive.
 * Will also check if this custom post is saved as a enabled post type archive
 * @since 1.2.2 it is possible to add an optional argument to tell if we are in specific post type
 * Will store the result in static variable so that plugin doesn't have to do this calculation on every call.
 * @global $wp
 * @param string | bool $specific_post_type optional post type name to know whether we are on this specific post type
 * @return bool
 */
function pta_is_post_type_archive($specific_post_type = false) {
	static $is_post_type_index;
	
	if(!isset($is_post_type_index)) {
		global $wp; // this is where the matched query is stored

		$q = $wp->matched_query;
		parse_str($q, $q); // make query string into an array

		$post_type_index = isset($q['post_type_index']) ? $q['post_type_index'] : false;
		$post_type = get_query_var('post_type');
		$enabled_post_type_archives = pta_get_settings('enabled_post_type_archives');
	
		if($post_type_index == '1' and in_array($post_type, $enabled_post_type_archives))
			$is_post_type_index = true;
		else
			$is_post_type_index = false;
	}
	
	if($is_post_type_index and $specific_post_type !== false)
		return ($specific_post_type == get_query_var('post_type'));
	
	return $is_post_type_index;
}

/**
 * Since WP v3.1 WordPress implements it's own is_post_type_archive function so I had to rename it to
 * pta_is_post_type_archive.
 * To make things backwards compatible (with pre-3.1 releases) we just implement is_post_type_archive function
 * and forward it to pta_ist_post_type_archive.
 */
if(!function_exists('is_post_type_archive')) {
	function is_post_type_archive($specific_post_type = false) {
		return pta_is_post_type_archive($specific_post_type);
	}
}

/**
 * Just a wrapper around get_the_post_type_permalink to quickly echo the link.
 * @uses get_the_post_type_permalink
 */
function the_post_type_permalink($post_type = false) {
	echo get_the_post_type_permalink($post_type);
}

/**
 * Will return a link to custom post type archive.
 * If used in a loop then there is no need to specify $post_type
 * @param string|int|object|bool $post_type optional post type to link to.
 * 	If nothing or false is passed then assumes, that we are in a loop.
 * 	If int or object is passed then assumes that that's an post ID or post object and gets post type from that.
 * 	If string is passed then assumes that that's post types slug and uses that.
 * @return string will return link to post type or empty string if no post type was found.
 */
function get_the_post_type_permalink($post_type = false) {
	if($post_type === false) // if there is no post type passed then assume that we are in the WP loop
		$post_type = get_post_type();
	elseif(is_int($post_type) or is_object($post_type)) // if we have something and its either an ID or post object then get it's post type
		$post_type = get_post_type($post_type);
		
	if(empty($post_type)) // if we didn't get a post type (and it wasn't passed), then just return an empty string
		return '';
	
	$url_base = pta_get_settings('url_base');
	$use_rewrite_slug = pta_get_settings('use_rewrite_slug');
	
	if($use_rewrite_slug) { // if we want to use the rewrite slug
		$given_post_type = $post_type; // then save the given post type name for safety
		$post_type = get_post_type_object($post_type); // and get the actual post type object
		$customisations = pta_get_settings('enabled_post_type_customisations');
		
		if(!isset($post_type)) // if it's not set
			return ''; // then we can't do anything else
		
		if(isset($customisations[$given_post_type]) and !empty($customisations[$given_post_type]['rewrite_slug']))
			$post_type = $customisations[$given_post_type]['rewrite_slug']; // customisations take priority
		elseif(!empty($post_type->rewrite) and !empty($post_type->rewrite['slug'])) // if we have rewrite slug
			$post_type = $post_type->rewrite['slug']; // that that is the thing we use
		else // otherwise
			$post_type = $given_post_type; // it's a good thing that we were safe! Use what was passed originally.
			
		unset($given_post_type); // and free the memory
	}
	
	if($url_base == '')
		$url_base = '/';
	else
		$url_base = "/$url_base/";
	
	$url = get_bloginfo('url') . $url_base . $post_type . '/';
	
	return $url;
}

/**
 * Hooks into 'getarchives_where' filter to change the WHERE constraint to support post type filtering.
 * Will replace "post_type = 'post'" with "post_type = '{POST_TYPE}'". 
 * That part will be removed if 'post_type' in $options is set to 'all'.
 * @param string $where the WHERE constraint
 * @param array $options options that are passed to the 'wp_get_archives' function
 * @return string the modified (or not) WHERE constraint
 */
function pta_wp_get_archives_filter($where, $options) {
	if(!isset($options['post_type'])) return $where; // OK - this is regular wp_get_archives call - don't do anything
	
	global $wpdb; // get the DB engine
	
	$post_type = $wpdb->escape($options['post_type']); // escape the passed value to be SQL safe
	if($post_type == 'all') $post_type = ''; // if we want to have archives for all post types
	else $post_type = "post_type = '$post_type' AND"; // otherwise just for specific one
	
	$where = str_replace('post_type = \'post\' AND', $post_type, $where);
	
	return $where;
}
add_filter('getarchives_where', 'pta_wp_get_archives_filter', 10, 2);

/**
 * This function is a wrapper for 'wp_get_archives' function to support post type filtering.
 * It's necessary to have this function so that the links could be fixed to link to proper archives.
 * This needs to be done here, because WordPress lacks hooks in 'wp_get_archives' function.
 * The links won't be changed if post type is 'all' or type in options is 'postbypost' or 'alpha'.
 * @param string $post_type post type to get archives from. Or you can use 'all' to include all archives.
 * @param array $args optional args. You can use the same options as in 'wp_get_archives' function.
 * @return string the HTML with correct links if 'echo' option is false. Otherwise will echo that.
 * @see wp_get_archives
 * @link http://codex.wordpress.org/Function_Reference/wp_get_archives
 */
function wp_get_post_type_archives($post_type, $args = array()) {
	$echo = isset($args['echo']) ? $args['echo'] : true;
	$type = isset($args['type']) ? $args['type'] : 'monthly';
	$format = isset($args['format']) ? $args['format'] : 'html';
	
	$args['post_type'] = $post_type;
	$args['echo'] = false;
	
	$html = wp_get_archives($args); // let WP do the hard stuff
	
	if($post_type != 'all' and $type != 'postbypost' and $type != 'alpha') {
		if($format == 'option') {
			$pattern = 'value=\'' . get_bloginfo('url') . '/';
			$replacement = 'value=\'' . get_the_post_type_permalink($post_type);
		}
		else {
			$pattern = 'href=\'' . get_bloginfo('url') . '/';
			$replacement = 'href=\'' . get_the_post_type_permalink($post_type);
		}
		
		$html = str_replace($pattern, $replacement, $html);
	}
	
	if($echo)
		echo $html;
	else
		return $html;
}
?>