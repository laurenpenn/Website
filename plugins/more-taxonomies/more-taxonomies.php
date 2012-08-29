<?php
/*
Plugin Name: More Taxonomies
Version: 1.1
Author URI: http://labs.dagensskiva.com/
Plugin URI: http://labs.dagensskiva.com/plugins/more-taxonomies/
Description:  Add more taxonomies to your WordPress installation. You can use taxonomies to label and categorize your posts/pages.
Author: Henrik Melin, Kal Ström
License: GPL2

	Copyright (C) 2010  Henrik Melin, Kal Ström
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
    
*/
// Reset taxonomies
if (0) update_option('more_taxonomies', array());


// Plugin settings
$fields = array(
		'var' => array('hierarchical', 'public', 'label', 'singular_label', 'name', 'show_ui', 'rewrite', 'rewrite_base', 'show_tagcloud', 'query_var_bool', 'query_var'),
		'array' => array('object_type', 'more_manage_cap', 'more_edit_cap', 'more_delete_cap', 'more_assign_cap', 'labels' => array('name', 'singular_name', 'search_items', 'popular_items', 'all_items', 'parent_item', 'parent_item_colon', 'edit_item', 'update_item', 'add_new_item', 'new_item_name', 'separate_items_with_commas', 'add_or_remove_items', 'choose_from_most_used'))
);

$default = array(
		'hierarchical' => true,
		'public' => true,
		'show_ui' => true,
		'query_var_bool' => true,
		'show_tag_cloud' => true,
		'object_type' => array('post', 'page'),
		'labels' => array(
			'search_items' => __('Search', 'more-plugins'),
			'popular_items' => __('Popular', 'more-plugins'),
			'all_items' => __('All', 'more-plugins'),
			'parent_item' => __('Parent', 'more-plugins'),
			'parent_item_colon' => __('Parent', 'more-plugins'),
			'edit_item' => __('Edit', 'more-plugins'),
			'update_item' => __('Update', 'more-plugins'),
			'add_new_item' => __('Add New', 'more-plugins'),
			'new_item_name' => __('New Name', 'more-plugins'),
			'separate_items_with_commas' => __('Separate with commas', 'more-plugins'),
			'add_or_remove_items' => __('Add or Remove', 'more-plugins'),
			'choose_from_most_used' => __('Choose from the most commonly used', 'more-plugins')
		),
);

$default_keys = array('post_tag', 'category', 'link_category', 'nav_menu');
$settings = array(
		'name' => 'More Taxonomies', 
		'option_key' => 'more_taxonomies',
		'fields' => $fields,
		'default' => $default,
		'default_keys' => $default_keys,
		'file' => __FILE__,
);

// Always on components
if (!defined('MORE_PLUGINS_DEV')) include('more-plugins/more-plugins.php');
else include(ABSPATH . '/wp-content/plugins/more-plugins.php');
include('more-taxonomies-object.php');
$more_taxonomies = new more_taxonomies_object($settings);

// Load admin components
if (is_admin()) {
	if (!defined('MORE_PLUGINS_DEV')) include('more-plugins/more-plugins-admin.php');
	else include(ABSPATH . '/wp-content/plugins/more-plugins-admin.php');
	include('more-taxonomies-settings-object.php');
	$more_taxonomies_settings = new more_taxonomies_admin($settings);
}


?>
