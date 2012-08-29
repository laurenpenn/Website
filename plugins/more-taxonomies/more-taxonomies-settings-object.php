<?php

class more_taxonomies_admin extends more_plugins_admin_object_sputnik_8 {

	function validate_sumbission() {
		if ($this->action == 'save') {
				
			$a = esc_attr($_POST['labels,name']);
			$b = esc_attr($_POST['labels,singular_name']);
			if (!$a && !$b) {
				$this->set_navigation('taxonomy');
				return $this->error(__('You need both a plural and singular label for the taxonomy!', 'more-plugins')); 
			}
			if (!$a) {
				$this->set_navigation('taxonomy');
				return $this->error(__('You need to enter a plural name for the taxonomy!', 'more-plugins')); 
			}
			if (!$b) {
				$this->set_navigation('taxonomy');
				return $this->error(__("You need to enter a singular name for the taxonomy!", 'more-plugins')); 
			}
			// Default slug
			if (!$_POST['rewrite_base']) $_POST['rewrite_base'] = sanitize_title($a);

			$defaults = array('Category' => 'category', 'Post Tags' => 'post_tag', 'Navigation Menu' => 'nav_menu', 'Category' => 'link_category');
			$_POST['index'] = $this->get_index('labels,singular_name');

		}
		
		// If all is OK
		return true;
	}
	function load_objects() {
		global $more_taxonomies;
		$this->data = $more_taxonomies->load_objects();
		return $this->data;	
	}
	function default_data () {
		global $wp_taxonomies;
		return $this->object_to_array($wp_taxonomies);
	}	
	function get_post_type_taxonomies() {
		global $wp_post_types;
		$arr = array();
		foreach ($wp_post_types as $key => $type) $arr[$key] = $type->taxonomies;
// 		foreach ($this->data as $key)

		return $arr[$this->keys[0]];
	}
	
	/*
	**	after_request_handler()
	**
	**	Handles cross-functionality between More Types and More Fields - any changes
	** 	made here are reflected in the More Types admin too.
	*/
	function after_request_handler() {
		global $more_taxonomies, $more_types_settings;
		if ($this->action == 'save') {	
			if (is_callable(array($more_types_settings, 'update_from_more_plugin')))
				$more_types_settings->update_from_more_plugin($more_taxonomies, 'object_type', 'taxonomies');
		}
	}	
	
	
	
} // End class


?>