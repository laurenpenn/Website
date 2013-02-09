<?php
/**
 Plugin Name: Secondary HTML Content
 Plugin URI: http://get10up.com/plugins/secondary-html-content-wordpress/
 Description: Add additional HTML editor blocks to your post types. Perfect for layouts with multiple distinct blocks, such as sidebars or multi-column designs.
 Version: 3.0.1
 Author: Jake Goldman (10up LLC)
 Author URI: http://www.get10up.com

    Plugin: Copyright 2011 Jake Goldman (email : jake@get10up.com)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

    Note: Previous versions of this plug-in may have been incidently attributed
    to employers of Mr. Goldman. The copyright belongs - and has always
	 belonged - to Mr. Goldman personally.
*/
class Secondary_HTML_Content_Mgmt {

	private $post_types;

	public function __construct() {
		// check for 3.3 functions and fail semi-gracefully if not present
		if ( ! function_exists( 'wp_editor' ) )
			return;

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_ajax_secondary_html_content', array( $this, 'ajax_save' ) );
	}

	public function init() {
		// register hidden post type - used to manage blocks
		register_post_type( 'secondary_html', array(
			'label' => 'Secondary HTML Blocks',
			'supports' => array('title'),
		));

		// register hidden taxonomy for properties of blocks
		register_taxonomy( 'secondary_html_features', 'secondary_html', array(
			'label' => 'Secondary HTML Features',
			'rewrite' => false,
			'query_var' => false,
			'public' => false,
			'show_ui' => false,
		));

		// upgrade stuff
		if ( get_option( 'secondary_html_content_version', 0 ) < 3.0 ) {
			// save version number first to prevent potential flooding
			update_option( 'secondary_html_content_version', 3.0 );

			// add configuration options to taxonomy
			if ( ! term_exists( 'Media Buttons', 'secondary_html_features' ) )
				 wp_insert_term( 'Media Buttons', 'secondary_html_features', array( 'description' => 'Show media buttons in the editor, i.e. the media uploader' ) );

			if ( ! term_exists( 'Tiny Editor', 'secondary_html_features' ) )
				wp_insert_term( 'Tiny Editor', 'secondary_html_features', array( 'description' => 'Show a simplified version of the editor, for more basic editing' ) );

			if ( ! term_exists( 'Inherit from Ancestors', 'secondary_html_features' ) )
				wp_insert_term( 'Inherit from Ancestors', 'secondary_html_features', array( 'description' => 'For hierarchical post types (e.g. pages), inherit secondary content from ancestors if empty' ) );

			if ( ! term_exists( 'Treat Home as Ancestor', 'secondary_html_features' ) )
				wp_insert_term( 'Treat Home as Ancestor', 'secondary_html_features', array( 'description' => 'When inheritance is turned on, treat home page as a top level ancestor for pages' ) );

			// upgrade old fields
			if ( ( $old_options = get_option( 'secondary_html_options' ) ) && isset( $old_options['pages'] ) && isset( $old_options['posts'] ) ) {
				global $wpdb;
				$blocks = max( (int) $old_options['pages'], (int) $old_options['posts'] );
				$post_options = array();

				if ( ! empty( $old_options['inherit'] ) ) {
					$term = get_term_by( 'name', 'Inherit from Ancestors', 'secondary_html_features' );
					$post_options[] = $term->slug;
				}
				if ( ! empty( $old_options['homepage'] ) ) {
					$term = get_term_by( 'name', 'Treat Home as Ancestor', 'secondary_html_features' );
					$post_options[] = $term->slug;
				}
				if ( ! empty( $old_options['media'] ) ) {
					$term = get_term_by( 'name', 'Media Buttons', 'secondary_html_features' );
					$post_options[] = $term->slug;
				}

				for ( $i=1; $i<=$blocks; $i++ ) {
					// create new versions of blocks
					if ( ! $block_id = wp_insert_post(array( 'post_type' => 'secondary_html', 'post_status' => 'publish', 'post_title' => (string) $i )) )
						continue;

					if ( $old_options['pages'] >= $i )
						add_post_meta( $block_id, '_secondary_post_types', 'page' );

					if ( $old_options['posts'] >= $i )
						add_post_meta( $block_id, '_secondary_post_types', 'post' );

					wp_set_object_terms( $block_id, $post_options, 'secondary_html_features' ); // delete old ones

					set_transient( 'secondary_html_block_id_' . $i,  $block_id ); // cache title / ID relationship

					// update post meta
					$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_secondary_html_" . $block_id . "' WHERE meta_key = '_secondary_content_$i'" );
				}

				delete_option( 'secondary_html_options' );
			}

			// attempt to upgrade widgets
			if ( $widget_options = get_option( 'widget_secondary-html-content' ) ) {
				foreach ( $widget_options as $key => $options ) {
					if ( ! empty( $options['block'] ) && ( $block_id = get_transient( 'secondary_html_block_id_' . $options['block'] ) ) )
						$widget_options[$key]['block'] = $block_id;
				}
				update_option( 'widget_secondary-html-content', $widget_options );
			}
		}
	}

	public function admin_init() {
		add_settings_section( 'secondary_html_content', 'Secondary HTML Content', array( $this, 'settings_section' ), 'writing' );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * used by ajax and sesttings section to display a row about the block
	 */
	public function secondary_html_info_row( $block = 0 ) {
		if ( empty( $block ) ) {
			$block = new stdClass;
			$block->ID = 0;
			$block->post_title = '';
		} elseif ( is_int( $block ) ) {
			$block = get_post( $block );
		}

		if ( ! isset( $block->ID ) || ! isset( $block->post_title ) )
			return;

		$block_id = (int) $block->ID;
?>
		<tr valign="top" class="topicrow" style="line-height: 2em;" id="secondary-block-<?php echo $block_id; ?>">
			<td class="secondary-block-title">
				<input type="text" value="<?php echo esc_attr( $block->post_title ); ?>" name="secondary-html-title[<?php echo $block_id; ?>]" id="secondary-html-title-<?php echo $block_id; ?>" class="regular-text secondary-html-title" style="width: 90%;" />
			</td>
			<td class="secondary-block-post-types"><?php
				$post_types = empty( $this->post_types ) ? get_post_types(array( 'show_ui' => true ), 'all' ) : $this->post_types;
				$block_post_types = ( $block_id ) ? get_post_meta( $block_id, '_secondary_post_types' ) : array();

				foreach ( $post_types as $post_type ) {
					if ( ! post_type_supports( $post_type->name, 'editor' ) ) // maybe change...
						continue;

					echo '
						<label class="selectit" style="white-space: nowrap;">
							<input type="checkbox" name="secondary-html-types[' . $block_id . '][]" value="' . esc_attr( $post_type->name ) . '" ' . checked( in_array( $post_type->name, $block_post_types ), true, false ) . ' class="secondary-html-types" />
							<span>' . $post_type->labels->name . '</span> &nbsp;
						</label>
					';
				}
			?></td>
			<td class="secondary-block-features"><?php $this->options_checklist( $block_id ); ?></td>
			<td class="secondary-block-buttons">
				<input type="hidden" name="secondary-html-id[<?php echo $block_id; ?>]" value="<?php echo $block_id; ?>" />
			<?php if ( $block_id ) : ?>
				<input type="button" class="button save-button" onclick="SaveSecondary(<?php echo $block_id; ?>);" value="<?php _e( 'Save' ); ?>" />
				<input type="button" class="button" onclick="RemoveSecondary(<?php echo $block_id; ?>);" value="<?php _e( 'Remove' ); ?>" />
			<?php else : ?>
				<input type="button" class="button" onclick="SaveSecondary(0);" value="<?php _e( 'Add' ); ?>" id="new-secondary-html-add" />
			<?php endif; ?>
			</td>
		</tr>
<?php
	}

	function settings_section() {
?>
		<p><?php _e( 'Manage additional HTML blocks for all of your content types. Added by <a href="http://www.get10up.com/plugins/secondary-html-content-wordpress/">Secondary HTML Content plug-in</a>.', 'secondary_html_content' ); ?></p>
		<p class="hide-if-js"><?php _e( 'Secondary HTML Content requires JavaScript to be enabled.', 'secondary_html_content' ); ?></p>

		<script type="text/javascript">
		function SaveSecondary(block_id) {
			var block_id = parseInt( block_id );
			var label = jQuery('#secondary-html-title-'+block_id).val().trim();
	    	if ( label == '' || jQuery('#secondary-block-'+block_id+' .secondary-block-post-types input:checked').length < 1 ) {
	    		alert('<?php _e( 'Secondary HTML blocks must have a label and at least one post type selected.', 'secondary_html_content' ); ?>');
	    		jQuery('#secondary-html-title-'+block_id).focus();
    		} else {
    			var formdata = jQuery('#secondary-block-'+block_id+' input').serializeArray();
    			jQuery('#secondary-block-'+block_id+' input').attr('disabled','true');
    			jQuery('#secondary-block-'+block_id+' .button').hide();
    			jQuery('#secondary-block-'+block_id+' .secondary-block-buttons').prepend('<img src="<?php echo admin_url('images/wpspin_light.gif'); ?>" alt="loading" id="secondary-html-spin-'+block_id+'" />');
    			jQuery.post( ajaxurl, { action: 'secondary_html_content', subaction: 'save', actiondata: formdata }, function(response){
    				if ( block_id == 0 ) {
    					jQuery('#secondary-html-blocks').append(response);
    					jQuery('#secondary-block-'+block_id+' input').removeAttr('disabled');
						jQuery('#secondary-html-title-0').val('');
						jQuery('#secondary-block-0 input:checked').attr('checked',false);
    				} else {
    					jQuery('#secondary-block-'+block_id).replaceWith(response);
    				}
	            	jQuery('#secondary-html-spin-'+block_id).remove();
	            	jQuery('#secondary-block-'+block_id+' .button').show();
    			});
    		}
	    }

	    function RemoveSecondary(block_id) {
	    	if ( confirm('<?php _e( 'Are you certain you want to remove this block? Removing a secondary block will also delete all content stored in that blocks!', 'secondary_html_content' ); ?>') ) {
	    		block_id = parseInt( block_id );
	    		jQuery('#secondary-block-'+block_id+' .secondary-block-buttons').prepend('<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="loading" id="secondary-loading-'+block_id+'" />').children('.button').hide();
	    		jQuery.post( ajaxurl, { action: 'secondary_html_content', subaction: 'delete', actiondata: block_id }, function(response) {
	    			if ( response == 1 ) jQuery('#secondary-block-'+block_id).remove();
	    			else jQuery('#secondary-loading-'+block_id).remove().siblings('.button').show();
    			});
   			}
	    }

	    jQuery(document).ready(function(){
	    	jQuery('#secondary-html-blocks input').change(function(){ jQuery(this).closest('tr').find('.save-button').addClass('button-primary'); jQuery('#secondary-html-blocks input').unbind('change'); });
			jQuery('#secondary-block-0 input').change(function(){
				if ( jQuery('#secondary-html-title-0').val().trim() != '' && jQuery('#secondary-block-0 .secondary-block-post-types input:checked').length > 0 ) {
					jQuery('#new-secondary-html-add').addClass('button-primary');
					jQuery('#secondary-block-0 input').unbind('change');
				}
			});
	    });
	    </script>

		<table class="wp-list-table widefat fixed hide-if-no-js" id="secondary-html-content-table">
            <thead>
    			<tr valign="top">
    				<th scope="row" style="width: 15%;"><?php _e( 'Label' ); ?></th>
    				<th scope="row"><?php _e( 'Post Types', 'secondary_html_content' ); ?></th>
    				<th scope="row" style="width: 50%;"><?php _e( 'Options' ); ?></th>
    				<th scope="row" style="width: 140px;"></th>
    			</tr>
            </thead>
            <tfoot>
            	<?php $this->secondary_html_info_row(); ?>
            </tfoot>
            <tbody id="secondary-html-blocks">
			<?php
				$secondary_blocks = get_posts(array( 'post_type' => 'secondary_html', 'numberposts' => 100, 'orderby' => 'menu_order' ));

				foreach ( $secondary_blocks as $block )
					$this->secondary_html_info_row( $block );
			?>
            </tbody>
		</table>
<?php
	}

	private function options_checklist( $block_id = 0 ) {
		$block_id = (int) $block_id;
		$block_options = empty( $block_id ) ? array() : $block_options = wp_get_object_terms( (int) $block_id, 'secondary_html_features', array( 'fields' => 'slugs' ) );

		$options = get_terms( 'secondary_html_features', array( 'orderby' => 'nothing', 'hide_empty' => false ) );

		foreach( $options as $option ) {
		?>
			<label class="selectit" title="<?php esc_attr_e( $option->description ); ?>" style="white-space: nowrap;">
				<input value="<?php echo esc_attr( $option->slug ); ?>" type="checkbox" name="secondary_html_features[<?php echo $block_id; ?>][]" <?php checked( in_array( $option->slug, $block_options ) ); ?> />
				<?php esc_attr_e( $option->name ); ?>
			</label> &nbsp;
		<?php
		}
	}

	public function ajax_save() {
		if ( ! current_user_can('manage_options') || empty( $_POST['subaction'] ) || empty( $_POST['actiondata'] ) )
			die;

		extract( $_POST );

		if ( $subaction == 'save' ) {
			$post_array = array( 'post_type' => 'secondary_html', 'post_status' => 'publish' );
			$post_types = array();
			$post_options = array();

			foreach( $actiondata as $post_data ) {
				if ( strpos( $post_data['name'], 'secondary-html-title' ) !== false )
					$post_array['post_title'] = wp_kses( $post_data['value'], array() );
				elseif ( strpos( $post_data['name'], 'secondary-html-types' ) !== false )
					$post_types[] = sanitize_title_with_dashes( $post_data['value'] );
				elseif ( strpos( $post_data['name'], 'secondary_html_features' ) !== false )
					$post_options[] = sanitize_title_with_dashes( $post_data['value'] );
				elseif ( strpos( $post_data['name'], 'secondary-html-id' ) !== false )
					$post_array['ID'] = (int) $post_data['value'];
			}

			if ( empty( $post_array['post_title'] ) || empty( $post_types ) )
				die;

			if ( ! $block_id = wp_insert_post( $post_array ) )
				die;

			if ( isset( $post_array['ID'] ) )
				delete_post_meta( $block_id, '_secondary_post_types' );

			foreach ( $post_types as $post_type ) {
				add_post_meta( $block_id, '_secondary_post_types', $post_type );
			}

			wp_set_object_terms( $block_id, $post_options, 'secondary_html_features' ); // delete old ones

			set_transient( 'secondary_html_block_id_' . sanitize_title_with_dashes( $post_array['post_title'] ),  $block_id ); // cache title / ID relationship

			$this->secondary_html_info_row( $block_id );

			die;
		}

		if ( $subaction == 'delete' ) {
			$actiondata = (int) $actiondata;
			delete_post_meta_by_key( '_secondary_html_' . $actiondata );
			delete_transient( 'secondary_html_block_id_' . sanitize_title_with_dashes( get_the_title( $actiondata ) ) );
			$status = wp_delete_post( $actiondata, true ) ? '1' : '0';
			die( $status );
		}

		die;
	}

	function plugin_action_links( $links ) {
		array_unshift( $links, '<a href="' . admin_url( 'options-writing.php#secondary-html-content-table' ) . '">' . __('Settings') . '</a>' );
		return $links;
	}
}

$secondary_html_content_mgmt = new Secondary_HTML_Content_Mgmt;

/**
 * handle adding html blocks to edit pages
 */
class Secondary_HTML_Content_Input {

	public function __construct() {
		// check for 3.3 functions and fail semi-gracefully if not present
		if ( ! function_exists( 'wp_editor' ) )
			return;

		add_action( 'add_meta_boxes', array( $this, 'setup_editor' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	public function setup_editor( $post_type, $post ) {
		// fetch relevant featured blocks
		$secondary_blocks = get_posts(array( 'post_type' => 'secondary_html', 'numberposts' => 100, 'orderby' => 'menu_order', 'meta_key' => '_secondary_post_types', 'meta_value' => $post_type ));
		if ( empty( $secondary_blocks ) )
			return;

		$secondary_blocks = apply_filters( 'secondary_html_blocks_pre_editor', $secondary_blocks ); // allows more specific control over which secondary editors to show (e.g. restrict based on terms)

		foreach ( $secondary_blocks as $block ) {
			add_meta_box( 'secondary_html_content_' . $block->ID, $block->post_title, array( $this, 'meta_box_inner' ), $post_type, 'normal', 'high', $block->ID );
			add_filter( 'postbox_classes_' . $post_type . '_secondary_html_content_' . $block->ID, array( $this, 'meta_box_class' ) );
		}

		add_action( 'admin_head', array( $this, 'editor_css' ) );
	}

	public function meta_box_class( $classes ) {
		$classes[] = 'secondary_content_postbox';
		return $classes;
	}

	public function meta_box_inner( $post, $info ) {
		if ( empty( $info['args'] ) )
			return;

		$block_id = (int) $info['args'];
		$media_buttons = has_term( 'Media Buttons', 'secondary_html_features', $block_id );
		$teeny = has_term( 'Tiny Editor', 'secondary_html_features', $block_id );

		wp_nonce_field( 'secondary_html_nonce_' . $block_id, 'secondary_html_nonce_' . $block_id );
		$secondary_content = get_post_meta( $post->ID, '_secondary_html_' . $block_id, true );
		wp_editor( $secondary_content, '_secondary_html_' . $block_id, array( 'media_buttons' => $media_buttons, 'teeny' => $teeny ) );
	}

	public function editor_css() {
?>
		<style type="text/css" media="screen">
		#poststuff .secondary_content_postbox .inside { margin: -1px -1px -30px -1px; padding: 0; background-color: #fff; }
		#poststuff .secondary_content_postbox .inside .wp-editor-wrap { position: relative; top: -30px; }
		.secondary_content_postbox .wp-editor-tools { padding-right: 30px; }
		.secondary_content_postbox .wp-media-buttons { text-align: right;  }
		.secondary_content_postbox .wp-switch-editor { margin: 5px 0 0 5px; }
		.secondary_content_postbox .handlediv { position: relative; z-index: 100; }
		.secondary_content_postbox .wp-editor-container { border-top-right-radius: 0; border-top-left-radius: 0; }
		</style>
<?php
	}

	public function save_post( $post_id, $post ) {
		if ( ! current_user_can( 'edit_post', $post_id ) )
 			return;

		$post_type = ( $post->post_type == 'revision' ) ? get_post_type( wp_is_post_revision( $post_id ) ) : get_post_type( $post_id );

 		$secondary_blocks = get_posts(array( 'post_type' => 'secondary_html', 'numberposts' => 100, 'orderby' => 'menu_order', 'meta_key' => '_secondary_post_types', 'meta_value' => $post_type ));
		if ( empty( $secondary_blocks ) )
			return;

		foreach ( $secondary_blocks as $block ) {
			if ( isset( $_POST['secondary_html_nonce_' . $block->ID ] ) && wp_verify_nonce( $_POST['secondary_html_nonce_' . $block->ID], 'secondary_html_nonce_' . $block->ID ) )
				empty( $_POST['_secondary_html_' . $block->ID] ) ? delete_post_meta( $post_id, '_secondary_html_' . $block->ID) : update_post_meta( $post_id, '_secondary_html_' . $block->ID, apply_filters( 'content_save_pre', $_POST['_secondary_html_' . $block->ID] ) );
		}
	}
}

$secondary_html_content_input = new Secondary_HTML_Content_Input;

/**
 * Retrieve a secondary HTML content block
 *
 * @since 2.0
 *
 * @param string $block Title of the secondary block (post type); if empty, get the first one (really for backwards compatibility)
 * @param int|null $post_id Post ID for post whose secondary content we want to retrieve (null to inherit from query)
 * @return Formatted secondary HTML content
 */
function get_secondary_content( $block_id = '', $post_id = NULL ) {
	// can only grab post ID if singular
	if ( empty( $post_id ) && ! is_singular() )
		return false;
	elseif ( empty( $post_id ) )
		$post_id = get_the_ID();

	$post_type = get_post_type( $post_id );

	if ( empty( $block_id ) ) {
		// retrieve first created block for this post type
		$blocks = get_posts(array( 'post_type' => 'secondary_html', 'numberposts' => 1, 'orderby' => 'ID', 'order' => 'ASC', 'meta_key' => '_secondary_post_types', 'meta_value' => $post_type ));
		if ( empty( $blocks ) )
			return false;
		$block_id = $blocks[0]->ID;
	} elseif ( is_string( $block_id ) ) {
		// try caching in a transient
		$cache_name = 'secondary_html_block_id_' . sanitize_title_with_dashes( $block_id );
		if ( ! $id = get_transient( $cache_name ) ) {
			global $wpdb;
			$id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'secondary_html'", $block_id ) );
			if ( ! empty( $id ) )
				set_transient( $cache_name, $id );
			elseif ( is_int( $block_id ) )
				$id = $block_id;
			else
				return false;
		}
		$block_id = $id;
	} elseif ( ! is_int( $block_id ) ) {
		return false;
	}

	// does this post type even get this type of content?
	$post_types = get_post_meta( $block_id, '_secondary_post_types' );
	if ( ! in_array( $post_type, $post_types ) )
		return false;

	$secondary_content = get_post_meta( $post_id, '_secondary_html_' . $block_id, true );

 	if ( empty( $secondary_content ) && is_post_type_hierarchical( $post_type ) && has_term( 'Inherit from Ancestors', 'secondary_html_features', $block_id ) ) {
 		// attempt to find in ancestors
	 	$ancestors = get_post_ancestors( $post_id );
 		if ( is_page( $post_id ) && get_option( 'show_on_front' ) == 'page' && ( $front_page =  get_option( 'page_on_front' ) ) && has_term( 'Treat Home as Ancestor', 'secondary_html_features', $block_id ) )
 			$ancestors[] = $front_page;

		foreach ( $ancestors as $ancestor ) {
			if ( $secondary_content = get_post_meta( $ancestor, '_secondary_html_' . $block_id, true ) )
				break;
		}
 	}

 	if ( empty( $secondary_content ) )
 		return false;

 	$secondary_content = apply_filters( 'get_secondary_content', $secondary_content, get_the_title( $block_id ), $post_id );
 	return apply_filters( 'the_content', $secondary_content );
}

/**
 * Output a secondary HTML content block
 *
 * @since 2.0
 *
 * @param string $block Title or ID of the secondary block
 * @param int|null $post_id Post ID or null to inherit from query
 * @return Output a secondary HTML content block onto page
 */
function the_secondary_content( $block_id = '', $post_id = NULL ) {
	if ( $secondary_content = get_secondary_content( $block_id, $post_id ) )
		echo apply_filters( 'the_secondary_content', $secondary_content );
}

/**
 * widget
 */
class Secondary_HTML_Content extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'content2_block', 'description' => __( 'Display a Secondary HTML Content block.', 'secondary_html_content' ) );
		$this->WP_Widget( 'secondary-html-content', __( 'Secondary HTML Content', 'secondary_html_content' ), $widget_ops );
	}

    public function widget($args, $instance) {
    	if ( ! is_singular() )
    		return;

		extract( $args );

		if ( empty( $instance['block'] ) ) {
			$blocks = get_posts(array( 'post_type' => 'secondary_html', 'numberposts' => 1, 'orderby' => 'ID', 'order' => 'ASC', 'meta_key' => '_secondary_post_types', 'meta_value' => get_post_type() ));
			if ( empty( $blocks ) )
				return false;

			$block = $blocks[0]->ID;
		} else {
			$block = (int) $instance['block'];
		}

		if ( ! $secondary_content = get_secondary_content( $block ) )
			return;

		echo $before_widget;

		if ( ! empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'the_title', get_the_title( $instance['block'] ) ) . $after_title;

		echo apply_filters( 'the_secondary_content_widget', $secondary_content  ) . $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$new_instance['block'] = (int) $new_instance['block'];
		$new_instance['title'] = empty( $new_instance['title'] ) ? false : true;
		return $new_instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( $instance, array( 'block' => '', 'title' => false ) );
		$secondary_blocks = get_posts(array( 'post_type' => 'secondary_html', 'numberposts' => 100, 'orderby' => 'ID', 'order' => 'ASC' ));

		if ( empty( $secondary_blocks ) ) {
			echo '<p>' . __( 'No Secondary HTML Content blocks have been created. Visit the Writing settings page to create secondary HTML content blocks.', 'secondary_html_content' ) . '</p>';
			return;
		}
	?>
		<p>
			<label for="<?php echo $this->get_field_id('block'); ?>"><?php _e( 'Which Secondary HTML Content block?', 'secondary_html_content' ); ?></label>
			<select name="<?php echo $this->get_field_name('block'); ?>" id="<?php echo $this->get_field_id('block'); ?>" class="widefat">
			<?php
				foreach( $secondary_blocks as $block )
					echo '<option value="' . $block->ID . '" ' . selected( $instance['block'], $block->ID, false ) . '">' . esc_html( $block->post_title ) . "</option>\n";
			?>
			</select>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" class="checkbox" type="checkbox" <?php checked( $instance['title'] ); ?> />
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Display label as widget title', 'secondary_html_content' ); ?></label>
		</p>
	<?php
	}
}

function secondary_html_widget() {
	register_widget( 'Secondary_HTML_Content' );
}

add_action( 'widgets_init', 'secondary_html_widget' );

/**
 * uninstall hook... leave no trace
 */
function secondary_html_content_uninstall() {
	$secondary_blocks = get_posts(array( 'post_type' => 'secondary_html', 'numberposts' => 100 ));
	if ( ! empty( $secondary_blocks ) ) {
		foreach ( $secondary_blocks as $block ) {
			delete_post_meta_by_key( '_secondary_html_' . $block->ID );
			delete_transient( 'secondary_html_block_id_' . sanitize_title_with_dashes( $block->post_title ) );
			wp_delete_post( $block->ID, true );
		}
	}

	$options = get_terms( 'secondary_html_features', array( 'hide_empty' => false ) );
	foreach( $options as $option ) {
		wp_delete_term( $option->term_id, 'secondary_html_features' );
	}

	delete_option( 'widget_secondary-html-content' );
	delete_option( 'secondary_html_content_version' );
}

register_uninstall_hook( __FILE__, 'secondary_html_content_uninstall' );