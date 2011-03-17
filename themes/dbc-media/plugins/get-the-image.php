<?php
/*
Plugin Name: Get The Image
Plugin URI: http://justintadlock.com/archives/2008/05/27/get-the-image-wordpress-plugin
Description: This is a highly intuitive script that can grab an image by custom field input, post attachment, or extracting it from the post's content.
Version: 0.3.1
Author: Justin Tadlock
Author URI: http://justintadlock.com
License: GPL
*/

/**
* This is a highly intuitive script file that gets images
* It first calls for custom field keys
* If no custom field key is set, check for images "attached" to post
* Check for image order if looking for attached images
* Scan the post for images if $image_scan = true
* Check for default image if $default_image = true
*
* Entirely rewrote the system in 0.4
*
* @package Hybrid
* @subpackage Media
*
* @since 0.1
* @filter get_the_image_args
*/
function get_the_image($args = array()) {

	$defaults = array(
		'custom_key' => array('Thumbnail','thumbnail'),
		'post_id' => false, // Build functionality in later
		'attachment' => true,
		'default_size' => 'thumbnail',
		'default_image' => false,
		'order_of_image' => 1,
		'link_to_post' => true,
		'image_class' => false,
		'image_scan' => false,
		'width' => false,
		'height' => false,
		'format' => 'img',
		'echo' => true
	);

	$args = apply_filters('get_the_image_args', $args);

	$args = wp_parse_args($args, $defaults);

	extract($args);

	if(!is_array($custom_key)) :
		$custom_key = str_replace(' ', '', $custom_key);
		$custom_key = str_replace(array('+'), ',', $custom_key);
		$custom_key = explode(',', $custom_key);
		$args['custom_key'] = $custom_key;
	endif;

	if($custom_key && $custom_key !== 'false' && $custom_key !== '0') $image = image_by_custom_field($args);

	if(!$image && $attachment && $attachment !== 'false' && $attachment !== '0') $image = image_by_attachment($args);

	if(!$image && $image_scan) $image = image_by_scan($args);

	if(!$image && $default_image) $image = image_by_default($args);

	if($image)
		$image = display_the_image($args, $image);

	else
		$image = '<!-- No images were added to this post. -->';

	if($echo && $echo !== 'false' && $echo !== '0' && $format !== 'array')
		echo $image;
	else
		return $image;
}

/**
* Calls images by custom field key
* Allow looping through multiple custom fields
*
* @since 0.4
* @param $args Not Optional
* @return array $image, $classes, $alt
*/
function image_by_custom_field($args = array()) {

	extract($args);

	if(!$post_id)
		global $post;

	if(isset($custom_key)) :
		foreach($custom_key as $custom) :
			$image = get_post_meta($post->ID, $custom, true);
			if($image) :
				break;
			endif;
		endforeach;
		if(!$image) :
			return false;
		endif;
	endif;

	return array('image' => $image);
}

/**
* Check for attachment images
* Uses get_children() to check if the post has images attached
*
* @since 0.4
* @param $args Not Optional
* @return array $image, $classes, $alt, $caption
*/
function image_by_attachment($args = array()) {

	extract($args);

	if(!$post_id)
		global $post;

	/*
	* Use a WP 2.6 function to check
	*/
	if(function_exists('wp_enqueue_style')) :
		$attachments = get_children(array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID'));

	/*
	* WP 2.5 compatibility
	*/
	else :
		$attachments = get_children("post_parent=$post->ID&post_type=attachment&post_mime_type=image&orderby=\"menu_order ASC, ID ASC\"");

	endif;

	if(empty($attachments)) :
		return false;
	else :
		foreach($attachments as $id => $attachment) :
			$i++;
			if($i == $order_of_image) :
				$image = wp_get_attachment_image_src($id, $default_size);
				$image = $image[0];
				break;
			endif;
		endforeach;
	endif;

	return array('image' => $image);
}

/**
* Scans the post for images within the content
* Not called by default with get_the_image()
* Shouldn't use if using large images within posts, better to use the other options
*
* @since 0.4
* @param $args Not Optional
* @return $image, $classes, $alt
*/
function image_by_scan($args = array()) {

	if(!$post_id)
		global $post;

	preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post->post_content, $matches);

	if(isset($matches)) $image = $matches[1][0];

	if($matches[1][0])
		return array('image' => $image);
	else
		return false;
}

/**
* Used for setting a default image
* Not used with get_the_image() by default
*
* @since 0.4
* @param $args Not Optional
* @return array $image, $classes, $alt
*/
function image_by_default($args = array()) {

	extract($args);

	$image = $default_image;

	return array('image' => $image);
}

/**
* Formats an image with appropriate alt text and class
* Adds a link to the post if argument is set
* Should only be called if there is an image to display, but will handle it if not
*
* @since 0.1
* @param $args Not Optional
* @param $arr Array of image info ($image, $classes, $alt, $caption)
* @return string Formatted image (w/link to post if the option is set)
*/
function display_the_image($args = array(), $arr = false) {
	global $post;

	extract($arr);

	if(!$image)
		return;

	extract($args);

	if($width) $width = ' width="' . $width . '"';
	if($height) $height = ' height="' . $height . '"';

	$img = $image;

	if(is_array($custom_key)) :
		foreach($custom_key as $key) :
			if($key !== 'false' && $key !== '0') :
				$classes[] = str_replace(' ', '-', strtolower($key));
			endif;
		endforeach;
	endif;

	$classes[] = $default_size;
	$classes[] = $image_class;

	$class = join(' ', $classes);

	$image = '';

	if($format == 'array') :
		$image = array(
			'url' => $img,
			'alt' => the_title_attribute('echo=0'),
			'class' => $class,
			'link' => get_permalink($post->ID),
		);
		return $image;
	endif;

	if($link_to_post)
		$image .= '<a href="' . get_permalink($post->ID) . '" title="' . the_title_attribute('echo=0') . '">';

	$image .= '<img src="' . $img . '" alt="' . the_title_attribute('echo=0') . '" class="' . $class . '"' . $width . $height . ' />';

	if($link_to_post)
		$image .= '</a>';

	return $image;
}

/**
* Deprecated function needs to be replaced with get_the_image()
*
* @since 0.1
* @deprecated 0.4
*/
function get_the_image_link($deprecated = false, $deprecated_2 = false, $deprecated_3 = false) {
	_e('The function has been deprecated. You need to update your template file calls to <code>get_the_image()</code>.','get_the_image');
}
?>