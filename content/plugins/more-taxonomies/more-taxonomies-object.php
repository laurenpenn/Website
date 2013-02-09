<?php


class more_taxonomies_object extends more_plugins_object_sputnik_8 {
	
	var $settings;

	function init ($settings) {
		add_action('init', array(&$this, 'load_taxonomies'), 20);
		add_action('init', array(&$this, 'set_default_data'), 1);
		// Get modified post type array
		add_action('init', array(&$this, 'set_modified_data'), 19);
	}
	function set_default_data() {
		global $wp_taxonomies;
		$this->data_default = $wp_taxonomies;		
	}
	function set_modified_data() {
		global $wp_taxonomies;
		$this->data_modified = $wp_taxonomies;	
	}
	/*
	function load_data() {
		global $_more_taxonomies_registered;
		$data = (array) get_option($this->settings['option_key']);


		return $data;
	
	}
	*/
	/*
	function read_data() {
		global $wp_taxonomies;

		return $this->load_objects();

		$data = get_option($this->settings['option_key'], array());
	
		// Data save to file
		$data = $this->saved_data($data);

		// Data added eslewhere
		if (!$this->wp_taxonomies) $this->wp_taxonomies = $wp_taxonomies;
		$data = $this->elsewhere_data($data, $this->wp_taxonomies);
		
		return $data;
	
	}
	*/
	function load_taxonomies() {	
		global $wp_roles, $wp_taxonomies;
		$data = $this->get_objects(array('_plugin_saved', '_plugin'));

		// Give More Types priority
		$plugins = get_option('active_plugins', array());
		$more_types = 'more-types/more-types.php';

		$caps = array(
			'manage_cap' => 'manage_%', 
			'edit_cap' => 'edit_%', 
			'delete_cap' => 'delete_%'
		);

		foreach ($data as $name => $taxonomy) {
		
			foreach ($caps as $cap_key => $template) {
			
				// Create the capability name
				$capability = str_replace('%', $name, $template);

				// Add capabilities to the post type if there are defined roles
				if (!empty($taxonomy['more_' . $cap_key])) 
					$taxonomy[$cap_key] = $capability;

				// Add capability!
				if (array_key_exists('more_' . $cap_key, $taxonomy))
					foreach ((array) $taxonomy['more_' . $cap_key] as $role) 
						if (is_object($wp_roles))
							$wp_roles->add_cap($role, $capability);
			}	
		
		
			// If this post type has a ancestor key, then
			// we need to remove it (it's been overridden).
			if ($k = $taxonomy['ancestor_key']) unset($wp_taxonomies[$k]);
			
			// Configure slug
			if ($taxonomy['rewrite'] && ($slug = $taxonomy['rewrite_base'])) 
				$taxonomy['rewrite'] = array('slug' => $slug);

			// If more types is installed don't associate with any particular post type. 
			//if (in_array($more_types, $plugins)) {
			//	register_taxonomy($name, '', $taxonomy);
			//} else {
				// Link taxonomy to particular objects
				//foreach ((array) $taxonomy['object_type'] as $type) {
					register_taxonomy($name, (array) $taxonomy['object_type'], $taxonomy);
				//}
			//}
		}

	}
	
} // End class


?>