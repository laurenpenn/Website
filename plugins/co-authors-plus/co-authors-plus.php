<?php
/*
Plugin Name: Co-Authors Plus
Plugin URI: http://wordpress.org/extend/plugins/co-authors-plus/
Description: Allows multiple authors to be assigned to a post. This plugin is an extended version of the Co-Authors plugin developed by Weston Ruter.
Version: 2.1.1
Author: Mohammad Jangda
Author URI: http://digitalize.ca
Copyright: Some parts (C) 2009, Mohammad Jangda; Other parts (C) 2008, Weston Ruter

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

$plugin_dir = basename( dirname( __FILE__ ) );
// TODO: Use plugins_dir
load_plugin_textdomain( 'co-authors-plus', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );

define( 'COAUTHORS_DEBUG', false );
define( 'COAUTHORS_FILE_PATH', '' );
define( 'COAUTHORS_DEFAULT_BEFORE', '' );
define( 'COAUTHORS_DEFAULT_BETWEEN', ', ' );
define( 'COAUTHORS_DEFAULT_BETWEEN_LAST', __( ' and ', 'co-authors-plus' ) );
define( 'COAUTHORS_DEFAULT_AFTER', '' );
define( 'COAUTHORS_PLUS_VERSION', '2.1' );

class coauthors_plus {
	
	// Name for the post type we're using to store coauthors (3.0+)
	var $coauthor_post_type = 'coauthor';
	// Name for the taxonomy we're using to store coauthors (2.x)
	var $coauthor_taxonomy = 'author';
	// Unique identified added as a prefix to all options
	var $options_group = 'coauthors_plus_';
	// Initially stores default option values, but when load_options is run, it is populated with the options stored in the WP db
	var $options = array(
		'allow_subscribers_as_authors' => 0,
	);
	
	var $coreauthors_meta_box_name = 'authordiv';
	var $coauthors_meta_box_name = 'coauthorsdiv';
	
	var $gravatar_size = 25;
	
	function __construct() {
		global $pagenow;
		
		// Load admin_init function
		add_action( 'admin_init', array( &$this,'admin_init' ) );
		
		// Load plugin options
		$this->load_options();
		
		// Register new taxonomy so that we can store all our authors
		register_taxonomy( $this->coauthor_taxonomy, 'post', array('hierarchical' => false, 'update_count_callback' => '_update_post_term_count', 'label' => false, 'query_var' => false, 'rewrite' => false, 'sort' => true, 'show_ui' => false ) );
		
		// Modify SQL queries to include coauthors
		add_filter('posts_where', array(&$this, 'posts_where_filter'));
		add_filter('posts_join', array(&$this, 'posts_join_filter'));
		add_filter('posts_groupby', array(&$this, 'posts_groupby_filter'));
		
		// Hooks to add additional coauthors to author column to Edit Posts page
		if($pagenow == 'edit.php') {
			add_filter('manage_posts_columns', array(&$this, '_filter_manage_posts_columns'));
			add_action('manage_posts_custom_column', array(&$this, '_filter_manage_posts_custom_column'));
		}
		
		// Action to set users when a post is saved
		add_action( 'save_post', array( &$this, 'coauthors_update_post' ), 10, 2 );
		// Filter to set the post_author field when wp_insert_post is called
		add_filter( 'wp_insert_post_data', array( &$this, 'coauthors_set_post_author_field' ) );
		
		// Action to reassign posts when a user is deleted
		add_action( 'delete_user',  array( &$this, 'delete_user_action' ) );
		
		add_filter( 'get_usernumposts', array( &$this, 'filter_count_user_posts' ) );
		
		// Action to set up author auto-suggest
		add_action('wp_ajax_coauthors_ajax_suggest', array(&$this, 'ajax_suggest') );
		
		// Filter to allow coauthors to edit posts
		add_filter('user_has_cap', array(&$this, 'add_coauthor_cap'), 10, 3 );
		
		add_filter('comment_notification_headers', array( &$this, 'notify_coauthors'), 10, 3);
		
		// Add the main JS script and CSS file
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		
		// Add necessary JS variables
		add_action( 'admin_print_scripts', array( &$this, 'js_vars' ) );
		
		// Add the necessary pages for the plugin 
		add_action( 'admin_menu', array( &$this, 'add_menu_items' ) );
		
		// Handle the custom author meta box
		add_action( 'add_meta_boxes', array( &$this, 'add_coauthors_box' ) );
		add_action( 'add_meta_boxes', array( &$this, 'remove_authors_box' ) );
		
		// Removes the author dropdown from the post quick edit 
		add_action( 'load-edit.php', array( &$this, 'remove_quick_edit_authors_box' ) );
		
	}
	
	function coauthors_plus() {
		$this->__construct();
	}
	
	/**
	 * Initialize the plugin for the admin 
	 */
	function admin_init() {
		// Register all plugin settings so that we can change them and such
		foreach($this->options as $option => $value) {
	    	register_setting($this->options_group, $this->get_plugin_option_fullname($option));
	    }
	}
	
	/** 
	 * Checks to see if the post_type supports authors
	 */
	function authors_supported( $post_type ) {
		
		if( !function_exists( 'post_type_supports' ) && in_array( $post_type, array( 'post', 'page' ) ) )
			return true;
		
		if( post_type_supports( $post_type, 'author' ) )
			return true;
		
		return false;
	}
	
	/**
	 * Gets the current global post type if one is set
	 */
	function get_current_post_type() {
		global $post, $typenow, $current_screen;
		
		if( $post && $post->post_type )
			$post_type = $post->post_type;
		elseif( $typenow )
			$post_type = $typenow;
		elseif( $current_screen && $current_screen->post_type )
			$post_type = $current_screen->post_type;
		elseif( $_REQUEST['post_type'] )
			$post_type = sanitize_key( $_REQUEST['post_type'] );
		else
			$post_type = '';
		
		return $post_type;
	}
	
	/**
	 * Removes the standard WordPress Author box.
	 * We don't need it because the Co-Authors one is way cooler.
	 */
	function remove_authors_box() {
		
		$post_type = $this->get_current_post_type();
		
		if( $this->authors_supported( $post_type ) )
			remove_meta_box( $this->coreauthors_meta_box_name, $post_type, 'normal' );
	}
	
	/**
	 * Adds a custom Authors box
	 */
	function add_coauthors_box() {
		
		$post_type = $this->get_current_post_type();
		
		if( $this->authors_supported( $post_type ) && $this->current_user_can_set_authors() )
			add_meta_box($this->coauthors_meta_box_name, __('Post Authors', 'co-authors-plus'), array( &$this, 'coauthors_meta_box' ), $post_type, 'normal', 'high');
	}
	
	/**
	 * Callback for adding the custom author box
	 */
	function coauthors_meta_box( $post ) {
		global $post;
		
		$post_id = $post->ID;
		
		if( !$post_id || $post_id == 0 || !$post->post_author )
			$coauthors = array( wp_get_current_user() );
		else 
			$coauthors = get_coauthors();
		
		$count = 0;
		if( !empty( $coauthors ) ) :
			?>
			<div id="coauthors-readonly" class="hide-if-js1">
				<ul>
				<?php
				foreach( $coauthors as $coauthor ) :
					$count++;
					?>
					<li>
						<?php echo get_avatar( $coauthor->user_email, $this->gravatar_size ); ?>
						<span id="coauthor-readonly-<?php echo $count; ?>" class="coauthor-tag">
							<input type="text" name="coauthorsinput[]" readonly="readonly" value="<?php echo esc_attr( $coauthor->display_name ); ?>" />
							<input type="text" name="coauthors[]" value="<?php echo esc_attr( $coauthor->ID ); ?>" />
							<input type="text" name="coauthorsemails[]" value="<?php echo esc_attr( $coauthor->user_email ); ?>" />
						</span>
					</li>
					<?php
				endforeach;
				?>
				</ul>				
				<div class="clear"></div>
				<p><?php _e( '<strong>Note:</strong> To edit post authors, please enable javascript or use a javascript-capable browser', 'co-authors-plus' ); ?></p>
			</div>
			<?php
		endif;
		?>
		
		<div id="coauthors-edit" class="hide-if-no-js">
			<p><?php _e( 'Click on an author to change them. Click on <strong>Delete</strong> to remove them.', 'co-authors-plus' ); ?></p>
		</div>
		
		<?php wp_nonce_field( 'coauthors-edit', 'coauthors-nonce' ); ?>
		
		<?php
	}
	
	/**
	 * Removes the author dropdown from the post quick edit 
	 */
	function remove_quick_edit_authors_box() {
		$post_type = $this->get_current_post_type();
		remove_post_type_support( $post_type, 'author' );
	}
	
	/**
	 * Adds menu items for the plugin
	 */
	function add_menu_items ( ) {
		// Add sub-menu page for Custom statuses		
		add_options_page(__('Co-Authors Plus', 'co-authors-plus'), __('Co-Authors Plus', 'co-authors-plus'), 8, __FILE__, array(&$this, 'settings_page'));
	}
	
	/**
	 * Add coauthors to author column on edit pages
	 * @param array $post_columns
	 */
	function _filter_manage_posts_columns($posts_columns) {
		$new_columns = array();
		foreach ($posts_columns as $key => $value) {
			$new_columns[$key] = $value;
			if ($key == 'author') {
				unset($new_columns[$key]);
				$new_columns['coauthors'] = __('Authors', 'co-authors-plus');
			}
		}
		return $new_columns;
	} // END: _filter_manage_posts_columns
	
	/**
	 * Insert coauthors into post rows on Edit Page
	 * @param string $column_name
	 **/
	function _filter_manage_posts_custom_column($column_name) {
		if ($column_name == 'coauthors') {
			global $post;
			$authors = get_coauthors($post->ID);
			
			$count = 1;
			foreach($authors as $author) :
				?>
				<a href="edit.php?author=<?php echo $author->ID; ?>"><?php echo $author->display_name ?></a><?php echo ($count < count($authors)) ? ',' : ''; ?>
				<?php
				$count++;
			endforeach;
		}
	}
	
	/**
	 * Modify the author query posts SQL to include posts co-authored
	 */
	function posts_join_filter($join){
		global $wpdb,$wp_query;
				
		if(is_author()){
			//$join .= " INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)";
		}
		return $join;
	}
	/**
	 * Modify
	 */
	function posts_where_filter($where){
		global $wpdb, $wp_query;
		
		if(is_author()) {
			$author = get_userdata( get_query_var('author') );
			$author_id = $author->ID;
			
			echo '<p>pre-where: ' . $where;
			
			$coauthor_posts_query = $wpdb->prepare( "SELECT DISTINCT(post_parent) FROM $wpdb->posts WHERE post_type = %s AND post_author = %d", $this->coauthor_post_type, $author_id );
			$where = preg_replace( '/(\b(?:' . $wpdb->posts . '\.)?post_author\s*=\s*(\d+))/', '' . $wpdb->posts . '.ID IN ( ' . $coauthor_posts_query .' )', $where, 1);
			
			echo '<p>post-where: ' . $where;
			/*
			$term = get_term_by('name', $author->user_login, $this->coauthor_taxonomy);
				
			if($author) {
				$where = preg_replace('/(\b(?:' . $wpdb->posts . '\.)?post_author\s*=\s*(\d+))/', '($1 OR (' . $wpdb->term_taxonomy . '.taxonomy = \''. $this->coauthor_taxonomy.'\' AND '. $wpdb->term_taxonomy .'.term_id = \''. $term->term_id .'\'))', $where, 1); #' . $wpdb->postmeta . '.meta_id IS NOT NULL AND 
			}
			*/
			
		}
		return $where;
	}
	/**
	 * 
	 */
	function posts_groupby_filter($groupby){
		global $wpdb;
		
		if(is_author()) {
			$groupby = $wpdb->posts .'.ID';
		}
		return $groupby;
	}
	
	/**
	 * Filters post data before saving to db to set post_author
	 */
	function coauthors_set_post_author_field( $data ) {
		$post_type = $data['post_type'];
		
		if ( ( !defined( DOING_AUTOSAVE ) || !DOING_AUTOSAVE ) && $post_type != $this->coauthor_post_type ) {
			if( isset( $_REQUEST['coauthors-nonce'] ) && is_array( $_POST['coauthors'] ) ) {
				$author_id = $_POST['coauthors'][0];
				if( $author ) {
					$data['post_author'] = $author_id;
				}
			} else {
				// If for some reason we don't have the coauthors fields set
				if( !$data['post_author'] ) {
					$user = wp_get_current_user();
					$data['post_author'] = $user->ID;
				}
			}
		}
		return $data;
	}
	
	/**
	 * Update a post's co-authors
	 * @param $post_ID
	 * @return 
	 */
	function coauthors_update_post( $post_id, $post ) {
		$post_type = $post->post_type;
		
		if ( ( !defined( DOING_AUTOSAVE ) || !DOING_AUTOSAVE ) && $post_type != $this->coauthor_post_type ) {
			
			if( isset( $_REQUEST['coauthors-nonce'] ) ) {
				check_admin_referer( 'coauthors-edit', 'coauthors-nonce' );
				
				if( $this->current_user_can_set_authors() ){
					$coauthors = array_map( 'intval', $_POST['coauthors'] );
					return $this->add_coauthors( $post_id, $coauthors );
				}
			}
		}
	}
	
	/**
	 * Add a user as coauthor for a post
	 */
	function add_coauthors( $post_id, $coauthors, $append = false ) {
		global $current_user;
		
		// TODO: need to respect $append
		
		$post_id = (int) $post_id;
		
		// if an array isn't returned, create one and populate with default author
		if ( !is_array( $coauthors ) || 0 == count( $coauthors ) || empty( $coauthors ) ) {
			$coauthors = array( $current_user->ID );
		}
		
		// Delete all existing coauthors, if we're not just appending
		if( !$append ) $this->delete_coauthors( $post_id );	
		
		// Default values for author as post
		$coauthor_as_post = array(
			'post_type' => $this->coauthor_post_type
			, 'post_parent' => $post_id
			, 'post_status' => 'publish'
			, 'post_title' => ' ' // TODO: Can use this to enable bylines
			, 'post_content' => ' '
		);
		
		// Add each co-author
		foreach( array_unique( $coauthors ) as $coauthor ) {
			$coauthor_as_post['post_author'] = $coauthor;
			$insert = wp_insert_post( $coauthor_as_post );
		}
	}
	
	function delete_coauthors( $post_id ) {
		// get post ids for all coauthors
		$coauthors_as_posts = _coauthors_get_as_posts( $post_id );
		
		foreach( $coauthors_as_posts as $coauthors_as_post )
			wp_delete_post( $coauthors_as_post->ID, true );
	}
	
	/**
	 * Action taken when user is deleted.
	 * - User term is removed from all associated posts
	 * - Option to specify alternate user in place for each post
	 * @param delete_id
	 */
	function delete_user_action( $delete_id ) {
		global $wpdb;
		
		// TODO: change this to work with new system!
		$reassign_id = absint( $_POST['reassign_user'] );
		
		// If reassign posts, do that
		if( $reassign_id ) {
			// Swap post_author entries with reassigned user
			$wpdb->update( $wpdb->posts, array( 'post_author' => $reassign_id ), array( 'post_author' => $delete_id, 'post_type' => $this->coauthor_post_type ), array( '%d' ), array( '%d', '%s' ) );
		}
		
		// Delete any coauthor entries we missed
		$delete_query = $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_type = %s AND post_author = %d", $this->coauthor_post_type, $delete_id );
		$wpdb->query( $delete_query );
	}
	
	function filter_count_user_posts( $count, $user_id ) {
		global $wpdb;
		
		// TODO: Find a way to optimize this
		$query = $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_parent IN (SELECT ID FROM $wpdb->posts WHERE post_type NOT IN ('revision', 'attachment', 'auto-save')) AND post_type = %s AND post_author = %d", $this->coauthor_post_type, $user_id );
		$count = $wpdb->get_var( $query );
		
		if( !$count || is_wp_error( $count ) )
			$count = 0;
		
		return $count;
	}
	
	/**
	 * Checks to see if the current user can set authors or not
	 */
	function current_user_can_set_authors( ) {
		global $post, $typenow;
		
		// TODO: Enable Authors to set Co-Authors
		
		$post_type = $this->get_current_post_type();
		// TODO: need to fix this; shouldn't just say no if don't have post_type
		if( !$post_type ) return false;
		
		$post_type_object = get_post_type_object( $post_type );
		$can_set_authors = current_user_can( $post_type_object->cap->edit_others_posts );
		
		return $can_set_authors;
	}
	
	function filter_search_by_editable_users( &$query ) {
		global $current_user, $user_ID;
		
		if( COAUTHORS_DOING_USER_SEARCH == true ) {
			// TODO: post type support
			$authors = get_editable_user_ids( $current_user->id, true, 'post' );
			$query->query_where .= ' AND ID IN ( '. implode( $authors , ',' ) .' )';
		}
	}
	
	/**
	 * Main function that handles search-as-you-type
	 */
	function ajax_suggest() {
		global $wpdb;
		
		global $user_level;
		
		if( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'coauthors-search' ) )
			die('');
		
		if( $this->current_user_can_set_authors() ) {
			$q = strtolower( $_REQUEST["q"] );
			if ( !$q ) return;
			
			/**/ // Option 1: wp_user_search
			add_action( 'pre_user_search', array( &$this, 'filter_search_by_editable_users' ) );
			
			define( 'COAUTHORS_DOING_USER_SEARCH', true );
			
			$user_search = new WP_User_Search( $q );
			$user_ids = $user_search->get_results();
			
			//echo '<p>results:';
			//print_r($user_ids);
			
			if( count( $user_ids ) ) {
				foreach( $user_ids as $user_id ) {
					$user = new WP_User( $user_id );
					echo $user->ID ." | ". $user->user_login ." | ". $user->display_name ." | ". $user->user_email ."\n";
				}
			}
			/**/
			
			/* // Option 2: get_editable_authors
			$search_fields = array( 'user_login', 'user_nicename', 'display_name', 'user_email' );
			$authors = get_editable_authors( $current_user->ID );
			if( is_array( $authors ) ) {
				foreach( $authors as $author ) {
					foreach( $search_fields as $search_field ) {
						//echo 'strpos:';
						//print_r(strpos( $author->$search_field, $q )); echo "\n";
						if( strpos( strtolower( $author->$search_field ), $q ) !== false ) {
							echo $author->ID ." | ". $author->user_login ." | ". $author->display_name ." | ". $author->user_email ."\n";
							break;
						}
					}
				}
			}
			/*
			
			/* // Option 3: custom query
			$q = '%' . $q . '%';
			
			// Set the minimum level of users to return
			if(!$this->get_plugin_option('allow_subscribers_as_authors')) {
				$user_level_where = "WHERE meta_key = '".$wpdb->prefix."user_level' AND meta_value >= 1";
			}
	
			$authors_query = $wpdb->prepare("SELECT DISTINCT u.ID, u.user_login, u.display_name, u.user_email FROM $wpdb->users AS u"
											." INNER JOIN $wpdb->usermeta AS um ON u.ID = um.user_id"
											." WHERE ID = ANY (SELECT user_id FROM $wpdb->usermeta $user_level_where)"
											." AND (um.meta_key = 'first_name' OR um.meta_key = 'last_name' OR um.meta_key = 'nickname')"
											." AND (u.user_login LIKE %s"
												." OR u.user_nicename LIKE %s"
												." OR u.display_name LIKE %s"
												." OR u.user_email LIKE %s"
												." OR um.meta_value LIKE %s)",$q,$q,$q,$q,$q);
												
			//echo $authors_query;
			$authors = $wpdb->get_results($authors_query, ARRAY_A);
		
			if(is_array($authors)) {
				foreach ($authors as $author) {
					echo $author['ID'] ." | ". $author['user_login']." | ". $author['display_name'] ." | ".$author['user_email'] ."\n";
				}
			}
		/**/
		}
		
		echo 'queries:' . get_num_queries() ."\n";
		echo 'timer: ';
		timer_stop(1);
		echo "seconds\n";
		
		die();
			
	}
	
	/**
	 * Functions to add scripts and css
	 */
	function enqueue_scripts($hook_suffix) {
		global $pagenow, $post;
		
		$post_type = $this->get_current_post_type();
		
		if( $this->is_valid_page() && $this->authors_supported( $post_type ) ) {
			wp_enqueue_style( 'co-authors-plus-css', plugins_url('co-authors-plus/css/co-authors-plus.css'), false, COAUTHORS_PLUS_VERSION, 'all');
			wp_enqueue_script( 'co-authors-plus-js', plugins_url('co-authors-plus/js/co-authors-plus.js'), array('jquery', 'suggest'), COAUTHORS_PLUS_VERSION, true);
		}
	}	
	
	/**
	 * Adds necessary javascript variables to admin pages 
	 */
	function js_vars() {
		global $current_user, $post, $post_ID;

		get_currentuserinfo();
		
		if( $this->is_valid_page() && $this->authors_supported( $post->post_type ) && $this->current_user_can_set_authors() ) {
			?>
			<script type="text/javascript">
			
				// AJAX link used for the autosuggest
				var coauthor_ajax_suggest_link = '<?php echo wp_nonce_url( 'admin-ajax.php', 'coauthors-search' ) . '&action=coauthors_ajax_suggest' . '&post_type=' . $post->post_type ?>';
				
				if(!i18n || i18n == 'undefined') var i18n = {};
				i18n.coauthors = {};
				i18n.coauthors.edit_label = "<?php _e('Edit', 'co-authors-plus')?>";
				i18n.coauthors.delete_label = "<?php _e('Delete', 'co-authors-plus')?>";
				i18n.coauthors.confirm_delete = "<?php _e('Are you sure you want to delete this author?', 'co-authors-plus')?>";
				i18n.coauthors.input_box_title = "<?php _e('Click to change this author', 'co-authors-plus')?>";
				i18n.coauthors.search_box_text = "<?php _e('Search for an author', 'co-authors-plus')?>";				
				i18n.coauthors.help_text = "<?php _e('Click on an author to change them. Click on <strong>Delete</strong> to remove them.', 'co-authors-plus')?>";
				
			</script>
			<?php
		}
	} // END: js_vars()
	
	/**
	 * Helper to only add javascript to necessary pages. Avoids bloat in admin.
	 */
	function is_valid_page() {
		global $pagenow;
		
		$pages = array('edit.php', 'post.php', 'post-new.php', 'page.php', 'page-new.php');
		
		if(in_array($pagenow, $pages)) return true;
		
		return false;
	} 
	
	/**
	 * Allows coauthors to edit the post they're coauthors of
	 */
	function add_coauthor_cap( $allcaps, $caps, $args ) {
		// TODO: custom post type support
		
		if(in_array('edit_post', $args) || in_array('edit_others_posts', $args) || in_array('edit_page', $args) || in_array('edit_others_pages', $args)) {
			// @TODO: Fix this disgusting hardcodedness. Ew.
			$user_id = $args[1];
			$post_id = $args[2];
			if(is_coauthor_for_post($user_id, $post_id)) {
				// @TODO check to see if can edit publish posts if post is published
				// @TODO check to see if can edit posts at all
				foreach($caps as $cap) {
					$allcaps[$cap] = 1;
				}
			}
		}
		return $allcaps;
	}
	
	/**
	 * Emails all coauthors when comment added instead of the main author
	 * 
	 */
	function notify_coauthors( $message_headers, $comment_id ) {
		// TODO: this is broken!
		$comment = get_comment($comment_id);
		$post = get_post($comment->comment_post_ID);
		$coauthors = get_coauthors($comment->comment_post_ID);
	
		$message_headers .= 'cc: ';
		$count = 0;
		foreach($coauthors as $author) {
			$count++;
			if($author->ID != $post->post_author){
				$message_headers .= $author->user_email;
				if($count < count($coauthors)) $message_headers .= ',';
			}
		}
		$message_headers .= "\n";
		return $message_headers;
	}
	
	/**
	 * Loads options for the plugin.
	 * If option doesn't exist in database, it is added
	 *
	 * Note: default values are stored in the $this->options array
	 * Note: a prefix unique to the plugin is appended to all options. Prefix is stored in $this->options_group 
	 */
	function load_options ( ) {
		
		$new_options = array();
		
		foreach($this->options as $option => $value) {
			$name = $this->get_plugin_option_fullname($option);
			$return = get_option($name);
			if($return === false) {
				add_option($name, $value);
				$new_array[$option] = $value;
			} else {
				$new_array[$option] = $return;
			}
		}
		$this->options = $new_array;
		
	} // END: load_options

	
	/**
	 * Returns option for the plugin specified by $name, e.g. custom_stati_enabled
	 *
	 * Note: The plugin option prefix does not need to be included in $name 
	 * 
	 * @param string name of the option
	 * @return option|null if not found
	 *
	 */
	function get_plugin_option ( $name ) {
		if(is_array($this->options) && $option = $this->options[$name])
			return $option;
		else 
			return null;
	} // END: get_option
	
	// Utility function: appends the option prefix and returns the full name of the option as it is stored in the wp_options db
	function get_plugin_option_fullname ( $name ) {
		return $this->options_group . $name;
	}
	
	/**
	 * Adds Settings page for Edit Flow
	 */
	function settings_page( ) {
		global $wp_roles;
		
		?>
			<div class="wrap">
				<div class="icon32" id="icon-options-general"><br/></div>
				<h2><?php _e('Co-Authors Plus', 'co-authors-plus') ?></h2>
				
				<form method="post" action="options.php">
					<?php settings_fields($this->options_group); ?>
					
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><strong><?php _e('Roles', 'co-authors-plus') ?></strong></th>
							<td>
								<p>
									<label for="allow_subscribers_as_authors">
										<?php /*<input type="checkbox" name="<?php echo $this->get_plugin_option_fullname('allow_subscribers_as_authors') ?>" value="1" <?php echo ($this->get_plugin_option('allow_subscribers_as_authors')) ? 'checked="checked"' : ''; ?> id="allow_subscribers_as_authors" /> <?php _e('Allow subscribers as authors', 'co-authors-plus') ?>*/ ?>
										<input type="checkbox" disabled="disabled" name="<?php echo $this->get_plugin_option_fullname('allow_subscribers_as_authors') ?>" value="1" id="allow_subscribers_as_authors" /> <?php _e('Allow subscribers as authors', 'co-authors-plus') ?>
									</label> <br />
									<span class="description"><?php _e('Enabling this option will allow you to add users with the subscriber role as authors for posts.', 'co-authors-plus') ?></span>
									<br />
									<span class="description"><strong>Note:</strong> This option has been removed as of v2.5</span>
								</p>
							</td>
						</tr>
						
					</table>
									
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'co-authors-plus') ?>" />
					</p>
				</form>
			</div>
		<?php 
	}
}

/** Helper Functions **/

/**
 * Replacement for the default WordPress get_profile function, since that doesn't allow for search by user_id
 * Returns a the specified column value for the specified user
 */
 // TODO: Remove this function
if(!function_exists('get_profile_by_id')) {
	function get_profile_by_id($field, $user_id) {
		global $wpdb;
		if($field && $user_id) return $wpdb->get_var( $wpdb->prepare("SELECT $field FROM $wpdb->users WHERE ID = %d", $user_id) );
		return false;
	}
}

function coauthors_plus_init() {
	// Check if we're running 3.0
	if( function_exists( 'post_type_exists' ) ) {
		// Create new instance of the coauthors_plus object
		global $coauthors_plus;
		$coauthors_plus = new coauthors_plus();
		
		// 	Core hooks to initialize the plugin
		add_action('init', array(&$coauthors_plus,'init'));
		
		// Add template tags
		require_once('template-tags.php');
		
	} else {
		// TODO: show error
		
	}
}

/**
 * Function to trigger actions when plugin is activated
 */
function coauthors_plus_activate_plugin() {}

/** Let's get the plugin rolling **/
add_action( 'init', 'coauthors_plus_init' );

// Hook to perform action when plugin activated
register_activation_hook( __FILE__, 'coauthors_plus_activate_plugin' );

?>