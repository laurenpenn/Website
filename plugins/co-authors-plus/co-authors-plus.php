<?php
/*
Plugin Name: Co-Authors Plus
Plugin URI: http://wordpress.org/extend/plugins/co-authors-plus/
Description: Allows multiple authors to be assigned to a post. This plugin is an extended version of the Co-Authors plugin developed by Weston Ruter.
Version: 2.5.2
Author: Mohammad Jangda
Author URI: http://digitalize.ca
Copyright: Some parts (C) 2009-2011, Mohammad Jangda; Other parts (C) 2008, Weston Ruter

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

define( 'COAUTHORS_PLUS_VERSION', '2.5.2' );

if( ! defined( 'COAUTHORS_PLUS_DEBUG' ) )
	define( 'COAUTHORS_PLUS_DEBUG', false );

if( ! defined( 'COAUTHORS_DEFAULT_BEFORE' ) )
	define( 'COAUTHORS_DEFAULT_BEFORE', '' );

if( ! defined( 'COAUTHORS_DEFAULT_BETWEEN' ) )
	define( 'COAUTHORS_DEFAULT_BETWEEN', ', ' );

if( ! defined( 'COAUTHORS_DEFAULT_BETWEEN_LAST' ) )
	define( 'COAUTHORS_DEFAULT_BETWEEN_LAST', __( ', and ', 'co-authors-plus' ) );

if( ! defined( 'COAUTHORS_DEFAULT_AFTER' ) )
	define( 'COAUTHORS_DEFAULT_AFTER', '' );

define( 'COAUTHORS_PLUS_PATH', dirname( __FILE__ ) );
define( 'COAUTHORS_PLUS_URL', plugin_dir_url( __FILE__ ) );

class coauthors_plus {
	
	// Name for the taxonomy we're using to store coauthors
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
	
	var $_pages_whitelist = array( 'post.php', 'post-new.php' );
		
	/**
	 * __construct()
	 */
	function __construct() {
		global $pagenow;
		
		// Load admin_init function
		add_action( 'admin_init', array( $this,'admin_init' ) );
		
		// Load plugin options
		$this->load_options();
		
		// Register new taxonomy so that we can store all our authors
		register_taxonomy( $this->coauthor_taxonomy, 'post', array('hierarchical' => false, 'update_count_callback' => '_update_post_term_count', 'label' => false, 'query_var' => false, 'rewrite' => false, 'sort' => true, 'show_ui' => false ) );
		
		// Modify SQL queries to include coauthors
		add_filter( 'posts_where', array( $this, 'posts_where_filter' ) );
		add_filter( 'posts_join', array( $this, 'posts_join_filter' ) );
		add_filter( 'posts_groupby', array( $this, 'posts_groupby_filter' ) );
		
		// Action to set users when a post is saved
		add_action( 'save_post', array( $this, 'coauthors_update_post' ), 10, 2 );
		// Filter to set the post_author field when wp_insert_post is called
		add_filter( 'wp_insert_post_data', array( $this, 'coauthors_set_post_author_field' ) );
		
		// Action to reassign posts when a user is deleted
		add_action( 'delete_user',  array( $this, 'delete_user_action' ) );
		
		add_filter( 'get_usernumposts', array( $this, 'filter_count_user_posts' ), 10, 2 );
		
		// Action to set up author auto-suggest
		add_action( 'wp_ajax_coauthors_ajax_suggest', array( $this, 'ajax_suggest' ) );
		
		// Filter to allow coauthors to edit posts
		add_filter( 'user_has_cap', array( $this, 'add_coauthor_cap' ), 10, 3 );
		
		add_filter( 'comment_notification_headers', array( $this, 'notify_coauthors' ), 10, 3 );
		
		// Add the necessary pages for the plugin 
		add_action( 'admin_menu', array( $this, 'add_menu_items' ) );
		
		// Handle the custom author meta box
		add_action( 'add_meta_boxes', array( $this, 'add_coauthors_box' ) );
		add_action( 'add_meta_boxes', array( $this, 'remove_authors_box' ) );
		
		// Removes the author dropdown from the post quick edit 
		add_action( 'load-edit.php', array( $this, 'remove_quick_edit_authors_box' ) );
		
		// Fix for author info not properly displaying on author pages
		add_action( 'the_post', array( $this, 'fix_author_page' ) );
	}
	
	function coauthors_plus() {
		$this->__construct();
	}
	
	/**
	 * Initialize the plugin for the admin 
	 */
	function admin_init() {
		global $pagenow;
		
		// Register all plugin settings so that we can change them and such
		// Not really being used
		/*
		foreach( $this->options as $option => $value ) {
	    	register_setting( $this->options_group, $this->get_plugin_option_fullname( $option ) );
	    }
	    */
	    
		// Hook into load to initialize custom columns
		if( $this->is_valid_page() ) {
			add_action( 'load-' . $pagenow, array( $this, 'admin_load_page' ) );
		}
		
		// Hooks to add additional coauthors to author column to Edit page
		add_filter( 'manage_posts_columns', array( $this, '_filter_manage_posts_columns' ) );
		add_filter( 'manage_pages_columns', array( $this, '_filter_manage_posts_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, '_filter_manage_posts_custom_column' ) );
		add_action( 'manage_pages_custom_column', array( $this, '_filter_manage_posts_custom_column' ) );
	}
	
	function admin_load_page() {
		
		// Add the main JS script and CSS file
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		// Add necessary JS variables
		add_action( 'admin_print_scripts', array( $this, 'js_vars' ) );

	}
	
	/** 
	 * Checks to see if the post_type supports authors
	 */
	function authors_supported( $post_type ) {
		
		if( ! function_exists( 'post_type_supports' ) && in_array( $post_type, array( 'post', 'page' ) ) )
			return true;
		
		// Hacky way to prevent issues on the Edit page
		if( isset( $this->_edit_page_post_type_supported ) && $this->_edit_page_post_type_supported )
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
		
		// "Cache" it!
		if( isset( $this->_current_post_type ) )
			return $this->_current_post_type;
		
		if( $post && $post->post_type )
			$post_type = $post->post_type;
		elseif( $typenow )
			$post_type = $typenow;
		elseif( $current_screen && isset( $current_screen->post_type ) )
			$post_type = $current_screen->post_type;
		elseif( isset( $_REQUEST['post_type'] ) )
			$post_type = sanitize_key( $_REQUEST['post_type'] );
		else
			$post_type = '';
		
		if( $post_type )
			$this->_current_post_type = $post_type;
		
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
							<input type="text" name="coauthors[]" value="<?php echo esc_attr( $coauthor->user_login ); ?>" />
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
	 * It's a bit hacky, but the only way I can figure out :( 
	 */
	function remove_quick_edit_authors_box() {
		$post_type = $this->get_current_post_type();
		if( post_type_supports( $post_type, 'author' )) {
			$this->_edit_page_post_type_supported = true;
			remove_post_type_support( $post_type, 'author' );
		}
	}
	
	/**
	 * Adds menu items for the plugin
	 */
	function add_menu_items ( ) {
		// Add sub-menu page for Custom statuses		
		//add_options_page(__( 'Co-Authors Plus', 'co-authors-plus' ), __( 'Co-Authors Plus', 'co-authors-plus' ), 'manage_options', 'co-authors-plus', array( $this, 'settings_page' ) );
	}
	
	/**
	 * Add coauthors to author column on edit pages
	 * @param array $post_columns
	 */
	function _filter_manage_posts_columns($posts_columns) {
		$new_columns = array();
		
		foreach ($posts_columns as $key => $value) {
			$new_columns[$key] = $value;
			if( $key == 'title' )
				$new_columns['coauthors'] = __( 'Authors', 'co-authors-plus' );
			
			if ( $key == 'author' )
				unset($new_columns[$key]);
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
			$authors = get_coauthors( $post->ID );
			
			$count = 1;
			foreach( $authors as $author ) :
				?>
				<a href="edit.php?author=<?php echo $author->ID; ?>"><?php echo $author->display_name ?></a><?php echo ( $count < count( $authors ) ) ? ',' : ''; ?>
				<?php
				$count++;
			endforeach;
		}
	}
	
	/**
	 * Modify the author query posts SQL to include posts co-authored
	 */
	function posts_join_filter( $join ){
		global $wpdb, $wp_query;
				
		if( is_author() ){
			// Check to see that JOIN hasn't already been added. Props michaelingp and nbaxley
			$term_relationship_join = " INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)";
			$term_taxonomy_join = " INNER JOIN {$wpdb->term_taxonomy} ON ( {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id )";
			
			if( strpos( $join, trim( $term_relationship_join ) ) === false ) {
				$join .= $term_relationship_join;
			}
			if( strpos( $join, trim( $term_taxonomy_join ) ) === false ) {
				$join .= $term_taxonomy_join;
			}
		}
		
		return $join;
	}
	
	/**
	 * Modify
	 */
	function posts_where_filter( $where ){
		global $wpdb, $wp_query;
		
		if( is_author() ) {
			$author = get_userdata( $wp_query->query_vars['author'] );
			$term = get_term_by( 'name', $author->user_login, $this->coauthor_taxonomy );
				
			if( $author ) {
				$where = preg_replace( '/(\b(?:' . $wpdb->posts . '\.)?post_author\s*=\s*(\d+))/', '($1 OR (' . $wpdb->term_taxonomy . '.taxonomy = \''. $this->coauthor_taxonomy.'\' AND '. $wpdb->term_taxonomy .'.term_id = \''. $term->term_id .'\'))', $where, 1 ); #' . $wpdb->postmeta . '.meta_id IS NOT NULL AND 

			}
		}
		return $where;
	}
	
	/**
	 * 
	 */
	function posts_groupby_filter( $groupby ) {
		global $wpdb;
		
		if( is_author() ) {
			$groupby = $wpdb->posts .'.ID';
		}
		return $groupby;
	}
	
	/**
	 * Filters post data before saving to db to set post_author
	 */
	function coauthors_set_post_author_field( $data ) {
		
		// Bail on autosave
		if ( defined( 'DOING_AUTOSAVE' ) && !DOING_AUTOSAVE )
			return $data;
		
		// Bail on revisions
		if( $data['post_type'] == 'revision' )
			return $data;
		
		if( isset( $_REQUEST['coauthors-nonce'] ) && is_array( $_POST['coauthors'] ) ) {
			$author = $_POST['coauthors'][0];
			if( $author ) {
				$author_data = get_user_by( 'login', $author );
				$data['post_author'] = $author_data->ID;
			}
		} else {
			// If for some reason we don't have the coauthors fields set
			if( ! isset( $data['post_author'] ) ) {
				$user = wp_get_current_user();
				$data['post_author'] = $user->ID;
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
		
		if ( defined( 'DOING_AUTOSAVE' ) && !DOING_AUTOSAVE )
			return;
			
		if( isset( $_POST['coauthors-nonce'] ) && isset( $_POST['coauthors'] ) ) {
			check_admin_referer( 'coauthors-edit', 'coauthors-nonce' );
			
			if( $this->current_user_can_set_authors() ){
				$coauthors = (array) $_POST['coauthors'];
				$coauthors = array_map( 'esc_html', $coauthors );
				return $this->add_coauthors( $post_id, $coauthors );
			}
		}
	}
	
	/**
	 * Add a user as coauthor for a post
	 */
	function add_coauthors( $post_id, $coauthors, $append = false ) {
		global $current_user;
		
		$post_id = (int) $post_id;
		$insert = false;
		
		// if an array isn't returned, create one and populate with default author
		if ( !is_array( $coauthors ) || 0 == count( $coauthors ) || empty( $coauthors ) ) {
			$coauthors = array( $current_user->user_login );
		}
		
		// Add each co-author to the post meta
		foreach( array_unique( $coauthors ) as $author ){
			
			// Name and slug of term are the username; 
			$name = $author;
			
			// Add user as a term if they don't exist
			if( !term_exists( $name, $this->coauthor_taxonomy ) ) {
				$args = array( 'slug' => sanitize_title( $name ) );
				$insert = wp_insert_term( $name, $this->coauthor_taxonomy, $args );
			}
		}
		
		// Add authors as post terms
		if( !is_wp_error( $insert ) ) {
			$set = wp_set_post_terms( $post_id, $coauthors, $this->coauthor_taxonomy, $append );
		}
	}
	
	/**
	 * Action taken when user is deleted.
	 * - User term is removed from all associated posts
	 * - Option to specify alternate user in place for each post
	 * @param delete_id
	 */
	function delete_user_action($delete_id){
		global $wpdb;
		
		$reassign_id = absint( $_POST['reassign_user'] );
		
		// If reassign posts, do that -- use coauthors_update_post
		if($reassign_id) {
			// Get posts belonging to deleted author
			$reassign_user = get_profile_by_id( 'user_login', $reassign_id );
			// Set to new author
			if( $reassign_user ) {
				$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_author = %d", $delete_id ) );

				if ( $post_ids ) {
					foreach ( $post_ids as $post_id ) {
						$this->add_coauthors( $post_id, array( $reassign_user ), true );
					}
				}
			}
		}
		
		$delete_user = get_profile_by_id( 'user_login', $delete_id );
		if( $delete_user ) {
			// Delete term
			wp_delete_term( $delete_user, $this->coauthor_taxonomy );
		}
	}
	
	function filter_count_user_posts( $count, $user_id ) {
		$user = get_userdata( $user_id );
		
		$term = get_term_by( 'slug', $user->user_login, $this->coauthor_taxonomy );
		
		if( ! $term || is_wp_error( $term ) ) {
			$count = 0;
		} else {
			$count = $term->count;
		}
		
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
		if( ! $post_type ) return false;
		
		$post_type_object = get_post_type_object( $post_type );
		$can_set_authors = current_user_can( $post_type_object->cap->edit_others_posts );
		
		return apply_filters( 'coauthors_plus_edit_authors', $can_set_authors );
	}
	
	/**
	 * Fix for author info not properly displaying on author pages
	 *
	 * On an author archive, if the first story has coauthors and
	 * the first author is NOT the same as the author for the archive,
	 * the query_var is changed.
	 *
	 */
	function fix_author_page( &$post ) {

		if( is_author() ) {
			global $wp_query, $authordata;
	
			// Get the id of the author whose page we're on
			$author_id = $wp_query->get( 'author' );
	
			// Check that the the author matches the first author of the first post
			if( $author_id != $authordata->ID ) {
				// The IDs don't match, so we need to force the $authordata to the one we want		
				$authordata = get_userdata( $author_id );
			}
		}
	}
	
	/**
	 * Main function that handles search-as-you-type
	 */
	function ajax_suggest() {
		global $wpdb;
		
		global $user_level;
		
		if( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'coauthors-search' ) )
			die();
		
		if( empty( $_REQUEST['q'] ) )
			die();
		
		if( ! $this->current_user_can_set_authors() )
			die();
		
		$search = esc_html( strtolower( $_REQUEST['q'] ) );
		
		$authors = $this->search_authors( $search );
				
		foreach( $authors as $author ) {
			echo $author->ID ." | ". $author->user_login ." | ". $author->display_name ." | ". $author->user_email ."\n";		
		}
		
		if( COAUTHORS_PLUS_DEBUG ) {		
			echo 'queries:' . get_num_queries() ."\n";
			echo 'timer: ' . timer_stop(1) . "sec\n";
		}
		
		die();
			
	}
	
	function search_authors( $search = '' ) {
		$authors = array();
		$author_fields = array( 'ID', 'display_name', 'user_login', 'user_email' );
		
		if ( function_exists( 'get_users' ) ) {			
			$search = sprintf( '*%s*',  $search ); // Enable wildcard searching
			$authors = get_users( array( 'search' => $search, 'who' => 'authors', 'fields' => $author_fields ) );			
		} else {
			// Pre 3.1 support
			// This might not be the most eloquent way of doing things, but it's better than a nasty SQL query
			$matching_authors = get_editable_authors( get_current_user_id() );
			
			foreach( $matching_authors as $matching_author ) {
				foreach( $author_fields as $author_field ) {
					if( isset( $matching_author->$author_field ) && strpos( strtolower( $matching_author->$author_field ), $search ) !== false ) {
						$authors[] = $matching_author;
						break;
					}
				}
			}
		}
		
		return (array) $authors;
	}
	
	/**
	 * Functions to add scripts and css
	 */
	function enqueue_scripts($hook_suffix) {
		global $pagenow, $post;
		
		$post_type = $this->get_current_post_type();
		
		// TODO: Check if user can set authors? $this->current_user_can_set_authors()
		if( $this->is_valid_page() && $this->authors_supported( $post_type ) ) {
		
			wp_enqueue_style( 'co-authors-plus-css', COAUTHORS_PLUS_URL . 'css/co-authors-plus.css', false, COAUTHORS_PLUS_VERSION, 'all' );
			wp_enqueue_script( 'co-authors-plus-js', COAUTHORS_PLUS_URL . 'js/co-authors-plus.js', array('jquery', 'suggest'), COAUTHORS_PLUS_VERSION, true);
			
			$js_strings = array(
				'edit_label' => __( 'Edit', 'co-authors-plus' ),
				'delete_label' => __( 'Delete', 'co-authors-plus' ), 
				'confirm_delete' => __( 'Are you sure you want to delete this author?', 'co-authors-plus' ),
				'input_box_title' => __( 'Click to change this author', 'co-authors-plus' ),
				'search_box_text' => __( 'Search for an author', 'co-authors-plus' ),
				'help_text' => __( 'Click on an author to change them. Click on <strong>Delete</strong> to remove them.', 'co-authors-plus' ),
			);
			wp_localize_script( 'co-authors-plus-js', 'coAuthorsPlusStrings', $js_strings );
			
		}
	}	
	
	/**
	 * Adds necessary javascript variables to admin pages
	 */
	function js_vars() {
		
		$post_type = $this->get_current_post_type();
		
		if( $this->is_valid_page() && $this->authors_supported( $post_type ) && $this->current_user_can_set_authors() ) {
			?>
			<script type="text/javascript">
				// AJAX link used for the autosuggest
				var coAuthorsPlus_ajax_suggest_link = '<?php echo add_query_arg(
					array(
						'action' => 'coauthors_ajax_suggest',
						'post_type' => $post_type,
					),
					wp_nonce_url( 'admin-ajax.php', 'coauthors-search' )
				); ?>';
			</script>
			<?php
		}
	} // END: js_vars()
	
	/**
	 * Helper to only add javascript to necessary pages. Avoids bloat in admin.
	 */
	function is_valid_page() {
		global $pagenow;
		
		return in_array( $pagenow, $this->_pages_whitelist );
	} 
	
	function get_post_id() {
		global $post;
		$post_id = 0;
		
		if ( is_object( $post ) ) {
			$post_id = $post->ID;
		}
		
		if( ! $post_id ) {
			if ( isset( $_GET['post'] ) )
				$post_id = (int) $_GET['post'];
			elseif ( isset( $_POST['post_ID'] ) )
				$post_id = (int) $_POST['post_ID'];
		}
		
		return $post_id;
	}
	
	/**
	 * Allows coauthors to edit the post they're coauthors of
	 * Pieces of code borrowed from http://pastebin.ca/1909968
	 * 
	 */
	function add_coauthor_cap( $allcaps, $caps, $args ) {
		
		// Load the post data:
		$user_id = isset( $args[1] ) ? $args[1] : 0;
		$post_id = isset( $args[2] ) ? $args[2] : 0;
		
		if( ! $post_id )
			$post_id = $this->get_post_id();
		
		if( ! $post_id )
			return $allcaps;
		
		$post = get_post( $post_id );
		
		if( ! $post )
			return $allcaps;
		
		$post_type_object = get_post_type_object( $post->post_type );
		
		// Bail out if we're not asking about a post
		if ( ! in_array( $args[0], array( $post_type_object->cap->edit_post, $post_type_object->cap->edit_others_posts ) ) )
			return $allcaps;
		
		// Bail out for users who can already edit others posts
		if ( isset( $allcaps[$post_type_object->cap->edit_others_posts] ) && $allcaps[$post_type_object->cap->edit_others_posts] )
			return $allcaps;
		
		// Bail out for users who can't publish posts if the post is already published
		if ( 'publish' == $post->post_status && ( ! isset( $allcaps[$post_type_object->cap->publish_posts] ) || ! $allcaps[$post_type_object->cap->publish_posts] ) )
			return $allcaps;
		
		// Finally, double check that the user is a coauthor of the post
		if( is_coauthor_for_post( $user_id, $post_id ) ) {
			foreach($caps as $cap) {
				$allcaps[$cap] = true;
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
		
		foreach( $this->options as $option => $value ) {
			$name = $this->get_plugin_option_fullname( $option );
			$return = get_option( $name );
			if( $return === false ) {
				add_option( $name, $value );
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
		if( is_array( $this->options ) && $option = $this->options[$name] )
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
	
	function debug($msg, $object) {
		if( COAUTHORS_PLUS_DEBUG ) {
			echo '<hr />';
			echo sprintf('<p>%s</p>', $msg);
			echo '<pre>';
			var_dump($object);
			echo '</pre>';
		}
	}
}

/** Helper Functions **/

/**
 * Replacement for the default WordPress get_profile function, since that doesn't allow for search by user_id
 * Returns a the specified column value for the specified user
 */
 // TODO: Remove this function
if( ! function_exists( 'get_profile_by_id' ) ) {
	function get_profile_by_id( $field, $user_id ) {
		global $wpdb;
		
		if( $field && $user_id )
			return $wpdb->get_var( $wpdb->prepare( "SELECT $field FROM $wpdb->users WHERE ID = %d", $user_id ) );
		
		return false;
	}
}

function coauthors_plus_init() {
	
	$plugin_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	load_plugin_textdomain( 'co-authors-plus', null, $plugin_dir );

	// Check if we're running 3.0
	if( function_exists( 'post_type_exists' ) ) {
		// Create new instance of the coauthors_plus object
		global $coauthors_plus;
		$coauthors_plus = new coauthors_plus();
		
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