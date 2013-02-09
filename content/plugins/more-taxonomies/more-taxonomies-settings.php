<?php

global $more_taxonomies, $more_taxonomies_settings, $more_types_settings;


if (!$this->navigation || $this->navigation == 'taxonomies') {

	echo '<p>';
	_e('Here you can create and edit taxonomies. Taxonomy is classification in essence, and taxonomies can be used to organize data and information.', 'more-plugins');
	echo '</p>';

	$defaults = array('category', 'post_tag', 'nav_menu', 'link_category'); //array_keys($more_taxonomies_settings->default_data());

	$titles = array('Taxonomy', 'Actions');
		
	$taxs = $more_taxonomies_settings->data;
	
//	__d($taxs);
	$ancestor_keys = array();
	$nbr = 0;
	$title = __('Taxonomies create with More Taxonomies', 'more-plugins'); 
	$caption = __('Taxonomies created here', 'more-plugins');
	$more_taxonomies_settings->table_header($titles);
	echo '<caption><h3>' . $title . '</h3><p>' . $caption . '</p></caption>';
	foreach ((array) $taxs['_plugin'] as $name => $tax) {
		if ($a = $tax['ancestor_key']) $ancestor_keys[] = $a;
		$label = $tax['labels']['singular_name'];
		$keys = '_plugin,' . $name;
		$data = array(
				$more_taxonomies_settings->settings_link($label, array('navigation' => 'taxonomy', 'action' => 'edit', 'keys' => $keys)),	
				$more_taxonomies_settings->settings_link(__('Edit', 'more-plugins'), array('navigation' => 'taxonomy', 'action' => 'edit', 'keys' => $keys)) . ' | ' .
				$more_taxonomies_settings->settings_link(__('Delete', 'more-plugins'), array('action' => 'delete','action_keys' => $keys, 'class' => 'more-common-delete')) . ' | ' .
				$more_taxonomies_settings->settings_link(__('Export', 'more-plugins'), array('navigation' => 'export', 'keys' => $keys)) . 
				$more_taxonomies_settings->updown_link($nbr, count($taxs['_plugin']))
			);
		$more_taxonomies_settings->table_row($data, $nbr++);
	}
	if (empty($taxs['_plugin'])) {
		$data = array(__('No Taxonomies defined', ''), '');
		$more_taxonomies_settings->table_row($data, $nbr++);
	
	}
	$more_taxonomies_settings->table_footer($titles);

	$new_key = '_plugin,'. $more_taxonomies_settings->add_key;
	$options = array('action' => 'add', 'navigation' => 'taxonomy', 'keys' => $new_key, 'class' => 'button-primary');
	echo '<p>' . $more_taxonomies_settings->settings_link(__('Add new Taxonomy', 'more_plugins'), $options) . '</p>';


	/*
	**		SAVED TAXONOMIES
	*/
	if (!empty($taxs['_plugin_saved'])) {
		$title = __('Saved taxonomies', 'more-plugins'); 
		$caption = __('Taxonomies created with this plugin saved as files', 'more-plugins');
		$more_taxonomies_settings->table_header($titles);
		echo '<caption><h3>' . $title . '</h3><p>' . $caption . '</p></caption>';
		foreach ($taxs['_plugin_saved'] as $name => $tax) {
			$keys = '_plugin_saved,' . $name;

			// Is this overwritten?
			$class = (in_array($name, $ancestor_keys)) ? 'disabled' : false;
			if (!$class) $class = (array_key_exists($name, $taxs['_plugin'])) ? 'disabled' : false ;

			$label = $tax['labels']['singular_name'];
			$data = array(
					$label,	
					$more_taxonomies_settings->settings_link(__('Override', 'more-plugins'), array('navigation' => 'taxonomy', 'action' => 'edit', 'keys' => $keys)) . ' | ' .
					$more_taxonomies_settings->settings_link(__('Export', 'more-plugins'), array('navigation' => 'export', 'keys' => $keys)) 
				);
			if ($class) $data = array($label, __('Overridden above', 'more-plugins'));
			$more_taxonomies_settings->table_row($data, $nbr++, $class);
		}

		$more_taxonomies_settings->table_footer($titles);
	}

	/*
	**		OTHER TAXONOMIES
	*/
	if (!empty($taxs['_other'])) {
		$title = __('Taxonomies elsewhere', 'more-plugins'); 
		$caption = __('Taxonomies created in function.php or elsewhere', 'more-plugins');
		$more_taxonomies_settings->table_header($titles);
		echo '<caption><h3>' . $title . '</h3><p>' . $caption . '</p></caption>';
		foreach ($taxs['_other'] as $name => $tax) {

			// Is this overwritten?
			$class = (in_array($name, $ancestor_keys)) ? 'disabled' : false;
			if (!$class) $class = (array_key_exists($name, $taxs['_plugin'])) ? 'disabled' : false ;

			$label = $tax['labels']['singular_name'];
			$data = array(
					$label,	
					$more_taxonomies_settings->settings_link(__('Overwrite', 'more-plugins'), array('navigation' => 'taxonomy', 'action' => 'edit', 'keys' => '_other,' . $name)) . ' | ' .
					$more_taxonomies_settings->settings_link(__('Export', 'more-plugins'), array('navigation' => 'export', 'keys' => '_other,' . $name)) 
				);
			if ($class) $data = array($label, __('Overridden above', 'more-plugins'));
			$more_taxonomies_settings->table_row($data, $nbr++, $class);
		}

		$more_taxonomies_settings->table_footer($titles);
	}

	/*
	**		DEFAULT TAXONOMIES
	*/
	if (!empty($taxs['_default'])) {
		$title = __('Default WordPress taxonomies', 'more-plugins');
		$caption = __('Taxonomies already built into WordPress  Please note - when messing with these defaults, prepare to die (but then, we\'re all going to anyway, eventually).', 'more-plugins');
		$more_taxonomies_settings->table_header($titles);
		echo '<caption><h3>' . $title . '</h3><p>' . $caption . '</p></caption>';
		foreach ($taxs['_default'] as $name => $tax) {

			// Is this overwritten?
			$class = (in_array($name, $ancestor_keys)) ? 'disabled' : false;
			if (!$class) $class = (array_key_exists($name, $taxs['_plugin'])) ? 'disabled' : false;
			$label = $tax['labels']['singular_name'];
			$keys = '_default,' . $name;
			$data = array(
					$label,	
					$more_taxonomies_settings->settings_link(__('Override', 'more-plugins'), array('navigation' => 'taxonomy', 'action' => 'edit', 'keys' => $keys)) . ' | ' .
					$more_taxonomies_settings->settings_link(__('Export', 'more-plugins'), array('navigation' => 'export', 'keys' => $keys)) 
				);

			if ($class == 'disabled') $data = array($label, __('Overwritten above', 'more-plugins'));
			$more_taxonomies_settings->table_row($data, $nbr++, $class);
		}

		$more_taxonomies_settings->table_footer($titles);
	}

} else if ($this->navigation == 'taxonomy') {


	// Set up the navigation
	
	$navtext = $more_taxonomies_settings->get_val('labels,singular_name');
	if (!$navtext) $navtext = __('Add new', 'more-plugins');
	$more_taxonomies_settings->navigation_bar(array($navtext));
	
	$more_taxonomies_settings->settings_form_header(array('navigation' => 'taxonomies', 'action' => 'save'));

	?>
		<table class="form-table">
	<?php
	


		$comment = __('This is the singular name of the taxonomy, e.g. \'Person\'.', 'more-plugins');
		$comment = $more_taxonomies_settings->format_comment($comment);
		$row = array(__('Taxonomy name singular', 'more-plugins'), $more_taxonomies_settings->settings_input('labels,singular_name') . $comment);
		$more_taxonomies_settings->setting_row($row);

		$comment = __('This is the plural name of the taxonomy, e.g. \'People\'.', 'more-plugins');
		$comment = $more_taxonomies_settings->format_comment($comment);
		$row = array(__('Taxonomy name plural', 'more-plugins'), $more_taxonomies_settings->settings_input('labels,name') . $comment);
		$more_taxonomies_settings->setting_row($row);

		$comment = __('Enables taxonomy items to be children of other items of the same taxonomy. E.g. in a standard WordPress installation, tags are not heirarchical whilst categories are.', 'more-plugins');
		$comment = $more_taxonomies_settings->format_comment($comment);
		$row = array(__('Hierarchical', 'more-plugins'), $more_taxonomies_settings->settings_bool('hierarchical') . $comment);
		$more_taxonomies_settings->setting_row($row);

		$comment = __('Create permalink structure for this taxonomy. In order for this to work permalinks must be enabled.', 'more-plugins');
		$comment = $more_taxonomies_settings->format_comment($comment);
		$row = array(__('Allow permalinks', 'more-plugins'), $more_taxonomies_settings->settings_bool('rewrite') . $comment);
		$more_taxonomies_settings->setting_row($row);

		$comment = __("If 'Allow permalinks' is set to true, then set the base permalink of this taxonomy here.", 'more-plugins');
		if ($base = $more_taxonomies_settings->get_val('rewrite_base')) {
			$comment .= ' ' . __('It is currently', 'more-plugins') . ' <code>' . get_option('siteurl') .  '/' . $base . '/</code>';
		}
		$pl = $more_taxonomies_settings->permalink_warning();
		$comment = $more_taxonomies_settings->format_comment($comment);
		$row = array(__('Taxonomy slug', 'more-plugins'), $more_taxonomies_settings->settings_input('rewrite_base') . $comment . $pl);
		$more_taxonomies_settings->setting_row($row);

		$comment = __('\'No\' to prevent the taxonomy being listed in the Tag Cloud Widget.', 'more-plugins');
		$comment = $more_taxonomies_settings->format_comment($comment);
		$row = array(__('Show tag cloud', 'more-plugins'), $more_taxonomies_settings->settings_bool('show_tagcloud') . $comment);
		$more_taxonomies_settings->setting_row($row);



		$selected = $more_taxonomies_settings->get_val('object_type');

		$id = $more_taxonomies_settings->keys[1];
		$types = $more_taxonomies_settings->get_post_types();
		$options = array();
		if (is_object($more_types_settings)) {
			$link = (method_exists($more_types_settings, 'settings')) ? $more_types_settings->settings['options_url'] : 'options-general.php';
			$text = __("If you want to use this taxonomy for this post type you need to import it using <a href='$link'>More Types</a>.", 'more-plugins');
			if (is_callable(array($more_types_settings, 'list_post_type_with_key')))
				$options = $more_types_settings->list_post_type_with_key('taxonomies', $id, $text);
		}
//		__d($selected);
//		__d($selected, $options);
		
		//$options = array();

//		if (!is_plugin_active('more-types/more-types.php')) {
			$comment = __('Select the post types which will use this taxonomy.', 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__('Available to', 'more-plugins'), $more_taxonomies_settings->checkbox_list('object_type', $types) . $comment);
			$more_taxonomies_settings->setting_row($row);
//		} else {
//			$comment = sprintf(__('To link taxonomies to post types use %s!', 'more-plugins'), '<a href="options-general.php?page=more-types">More Types</a>');
//			$comment = $more_taxonomies_settings->format_comment($comment);
//			$row = array(__('Available to', 'more-plugins'), $comment);
//			$more_taxonomies_settings->setting_row($row);
//		}
	?>

	</table>

	<div class="more-plugins-advanced-settings">
		<h3 class="more-advanced-settings-toggle"><a href="#">Advanced settings <span>show/hide</span></a></h3>
		<div class="more-advanced-settings">
		<table class="form-table">
	
		<?php

			$comment = __('Show the default taxonomy WordPress UI.', 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__('Show UI', 'more-plugins'), $more_taxonomies_settings->settings_bool('show_ui') . $comment);
			$more_taxonomies_settings->setting_row($row);
		
			$comment = __('Allow this taxonomy to be publically queriable', 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__('Allow queries', 'more-plugins'), $more_taxonomies_settings->settings_bool('query_var_bool') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("If queries are allowed, then this is the variable to be used when querying this taxonomy.", 'more-plugins');
			if ($query_var = $more_taxonomies_settings->get_val('query_var')) {
				$comment .= ' ' . __('Usage: ', 'more-plugins') . '<code>'. get_option('siteurl') . '/?' . $query_var . '=term_to_find</code>';
			}
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__('Query variable', 'more-plugins'), $more_taxonomies_settings->settings_input('query_var') . $comment);
			$more_taxonomies_settings->setting_row($row);

		/*
			$comment = __('Make this taxonomy available publically on your WordPress installation.', 'more-plugins');
			$comment = '<em>' . $comment . '</em>';
			$row = array(__('Public', 'more-plugins'), $more_taxonomies_settings->settings_bool('public') . $comment);
			$more_taxonomies_settings->setting_row($row);
		*/

			$roles = $more_taxonomies_settings->get_roles();

			$comment = __('The roles that can manage this taxonomy.', 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__('Manage capability', 'more-plugins') . $comment, $more_taxonomies_settings->checkbox_list('more_manage_cap', $roles));
			$more_taxonomies_settings->setting_row($row);
		
			$comment = __('The roles that can manage this taxonomy.', 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__('Edit capability', 'more-plugins') . $comment, $more_taxonomies_settings->checkbox_list('more_edit_cap', $roles));
			$more_taxonomies_settings->setting_row($row);
	
			$comment = __('The roles that can manage this taxonomy.', 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__('Delete capability', 'more-plugins') . $comment, $more_taxonomies_settings->checkbox_list('more_delete_cap', $roles));
			$more_taxonomies_settings->setting_row($row);
		
			$comment = __('The roles that can manage this taxonomy.', 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__('Assign capability', 'more-plugins') . $comment, $more_taxonomies_settings->checkbox_list('more_assign_cap', $roles));
			$more_taxonomies_settings->setting_row($row);


			// LABELS			
			$comment = __("Label for 'Search', e.g. 'Search categories'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Search' label text", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,search_items') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'Popular', e.g. 'Popular categories'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Popular' label text", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,popular_items') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'All', e.g. 'All categories'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'All' label text", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,all_items') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'Parent', e.g. 'Parent categories'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Parent' label text", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,parent_item') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'Parent' followed by a colon (':'), e.g. 'Parent categories:'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Parent:' label text", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,parent_item_colon') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'Edit', e.g. 'Edit categories'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Edit' label text", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,edit_item') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'Update', e.g. 'Update categories'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Update' label text", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,update_item') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'Add new', e.g. 'Add new category'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Add new' label text", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,add_new_item') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'Add new name', e.g. 'Add new category name'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Add new name' label text", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,new_item_name') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'Separate with commas' text, e.g. 'Separate categories with commas'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Separate with commas' label text", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,separate_items_with_commas') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'Add or remove item' text, e.g. 'Add or Remove Category'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Add or remove item' label", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,add_or_remove_items') . $comment);
			$more_taxonomies_settings->setting_row($row);

			$comment = __("Label for 'Choose from mosed used' text, e.g. 'Choose from the most used categories'", 'more-plugins');
			$comment = $more_taxonomies_settings->format_comment($comment);
			$row = array(__("'Choose from most used' label", 'more-plugins'), $more_taxonomies_settings->settings_input('labels,choose_from_most_used') . $comment);
			$more_taxonomies_settings->setting_row($row);

		?>
	
		</table>
		</div>
	</div>
		
	<?php $more_taxonomies_settings->settings_save_button(); ?>

	<?php
}

?>