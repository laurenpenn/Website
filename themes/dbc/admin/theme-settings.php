<?php
/**
 * Handles the display and functionality of the theme settings page. This provides the needed hooks and
 * meta box calls for developers to create any number of theme settings needed.
 *
 * Provides the ability for developers to add custom meta boxes to the theme settings page by using the 
 * add_meta_box() function.  Developers should hook their meta box registration function to 'admin_menu' 
 * and register the meta box for 'appearance_page-theme-settings'. If data needs to be saved, devs can 
 * use the '$prefix_update_settings_page' action hook to save their data.
 *
 * @package HybridCore
 * @subpackage Admin
 */

/* Hook the settings page function to 'admin_menu'. */
add_action( 'admin_menu', 'dbc_settings_page_init' );

/**
 * Initializes all the theme settings page functions. This function is used to create the theme settings 
 * page, then use that as a launchpad for specific actions that need to be tied to the settings page.
 *
 * Users or developers can set a custom capability (default is 'edit_theme_options') for access to the
 * settings page using the "$prefix_settings_capability" filter hook.
 *
 * @since 0.7.0
 * @global string $hybrid The global theme object.
 */
function dbc_settings_page_init() {
	global $hybrid;

	/* Get theme information. */
	$theme_data = get_theme_data( trailingslashit( TEMPLATEPATH ) . 'style.css' );
	$prefix = hybrid_get_prefix();
	$prefix = 'dbc';
		
	/* Register the default theme settings meta boxes. */
	add_action( "load-appearance_page_theme-settings", 'dbc_create_settings_meta_boxes' );

}

/**
 * Creates the default meta boxes for the theme settings page. Parent/child theme and plugin developers
 * should use add_meta_box() to create additional meta boxes.
 *
 * @since 0.7.0
 * @global string $hybrid The global theme object.
 */
function dbc_create_settings_meta_boxes() {
	global $hybrid;

	/* Get theme information. */
	$prefix = hybrid_get_prefix();
	$prefix = 'dbc';
	$theme_data = get_theme_data( trailingslashit( TEMPLATEPATH ) . 'style.css' );

	add_meta_box( "{$prefix}-style-settings-meta-box", __( 'Theme style settings', $prefix ), 'dbc_style_settings_meta_box', 'appearance_page_theme-settings', 'normal', 'high' );
	add_meta_box( "{$prefix}-home-settings-meta-box", __( 'Home template settings', $prefix ), 'dbc_home_settings_meta_box', 'appearance_page_theme-settings', 'normal', 'high' );
	add_meta_box( "{$prefix}-visitor-info-settings-meta-box", __( 'Visitor info settings', $prefix ), 'dbc_visitor_info_settings_meta_box', 'appearance_page_theme-settings', 'normal', 'high' );
	add_meta_box( "{$prefix}-sidebar-settings-meta-box", __( 'Sidebar settings', $prefix ), 'dbc_sidebar_settings_meta_box', 'appearance_page_theme-settings', 'normal', 'high' );
	
}

/**
 * Creates a settings box that allows users to customize theme styles
 *
 * @since 0.2
 */
function dbc_style_settings_meta_box() {
	$prefix = 'dbc'; ?>

	<table class="form-table">
	
		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'logo_src' ); ?>"><?php _e( 'Image path:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'logo_src' ); ?>" name="<?php echo hybrid_settings_field_name( 'logo_src' ); ?>" type="text" style="width: 98%;" value="<?php echo wp_htmledit_pre( stripslashes( hybrid_get_setting( 'logo_src' ) ) ); ?>" />
				<p><?php _e( 'Set the path to an image to use in the header next to the DBC logo.', $prefix ); ?></p>
			</td>
		</tr>

	</table><!-- .form-table --><?php
}

/**
 * Creates a settings box that allows users to customize the home page template
 *
 * @since 0.2
 */
function dbc_home_settings_meta_box() {
	$prefix = 'dbc'; 
	$categories = get_terms( array( 'category' ) );
	$categories[] = false;
	?>				
				
	<table class="form-table">

		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'slider' ); ?>"><?php _e( 'Slider:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'slider' ); ?>" name="<?php echo hybrid_settings_field_name( 'slider' ); ?>" type="checkbox" <?php if ( wp_htmledit_pre( stripslashes( hybrid_get_setting( 'slider' ) ) ) == 'true' ) { echo 'checked="checked"'; } ?> value="true"  />
				<?php _e( 'Select this to display the featured category slider home page.', $prefix ); ?>
			</td>
		</tr>

		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'slider_16x9' ); ?>"><?php _e( 'Slider size:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'slider_16x9' ); ?>" name="<?php echo hybrid_settings_field_name( 'slider_16x9' ); ?>" type="checkbox" <?php if ( wp_htmledit_pre( stripslashes( hybrid_get_setting( 'slider_16x9' ) ) ) == 'true' ) { echo 'checked="checked"'; } ?> value="true"  />
				<?php _e( 'Select this to use a 16 x 9 slider (rather than the default of 4 x 1).', $prefix ); ?>
			</td>
		</tr>

		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'latest-message' ); ?>"><?php _e( 'Latest message:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'latest-message' ); ?>" name="<?php echo hybrid_settings_field_name( 'latest-message' ); ?>" type="checkbox" <?php if ( wp_htmledit_pre( stripslashes( hybrid_get_setting( 'latest-message' ) ) ) == 'true' ) { echo 'checked="checked"'; } ?> value="true"  />
				<?php _e( 'Select this to display the latest message from dbcmedia.org on the home page.', $prefix ); ?>
			</td>
		</tr>
		
		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'feature_category' ); ?>"><?php _e( 'Slider categories:', $prefix ); ?></label></th>
			<td>
				<select id="<?php echo hybrid_settings_field_id( 'feature_category' ); ?>" name="<?php echo hybrid_settings_field_name( 'feature_category' ); ?>">

					<?php foreach( $categories as $category ) : ?>
					<option value="<?php echo $category->term_id ?>" <?php if( hybrid_get_setting( 'feature_category' ) == $category->term_id ) echo ' selected="selected"'; ?>><?php echo $category->name; ?></option>
					<?php endforeach; ?>
				</select>
				<?php _e( 'Leave blank to use sticky posts.', $prefix ); ?>
			</td>
		</tr>

		<tr>
			<th>
				<label for="<?php echo hybrid_settings_field_id( 'feature_num_posts' ); ?>"><?php _e('Number of slider posts:', $prefix); ?></label>
			</th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'feature_num_posts' ); ?>" name="<?php echo hybrid_settings_field_name( 'feature_num_posts' ); ?>" value="<?php echo hybrid_get_setting( 'feature_num_posts' ); ?>" size="2" maxlength="2" />
				<label for="<?php echo hybrid_settings_field_id( 'feature_num_posts' ); ?>"><?php _e('How many feature posts should be shown?','hybrid'); ?></label>
			</td>
		</tr>

	</table><!-- .form-table --><?php
}

/**
 * Creates a settings box that allows users to customize the sidebar
 *
 * @since 0.2
 */
function dbc_sidebar_settings_meta_box() {
	$prefix = 'dbc'; ?>

	<table class="form-table">
	
		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'sidebar' ); ?>"><?php _e( 'Sidebar:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'sidebar' ); ?>" name="<?php echo hybrid_settings_field_name( 'sidebar' ); ?>" type="checkbox" <?php if ( wp_htmledit_pre( stripslashes( hybrid_get_setting( 'sidebar' ) ) ) == 'true' ) { echo 'checked="checked"'; } ?> value="true"  />
				<?php _e( 'Select this to show the DBC sidebar content (i.e. DBC Media and Facebook links).', $prefix ); ?>
			</td>
		</tr>

	</table><!-- .form-table --><?php
}

/**
 * Creates a settings box that allows users to customize the home page template
 *
 * @since 0.2
 */
function dbc_visitor_info_settings_meta_box() {
	$prefix = 'dbc'; 
	?>

	<table class="form-table">

		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'info' ); ?>"><?php _e( 'Info box:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'info' ); ?>" name="<?php echo hybrid_settings_field_name( 'info' ); ?>" type="checkbox" <?php if ( wp_htmledit_pre( stripslashes( hybrid_get_setting( 'info' ) ) ) == 'true' ) { echo 'checked="checked"'; } ?> value="true"  />
				<?php _e( 'Select this to display the visitor\'s information box on the home page.', $prefix ); ?>
			</td>
		</tr>

		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'info_title' ); ?>"><?php _e( 'Title:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'info_title' ); ?>" name="<?php echo hybrid_settings_field_name( 'info_title' ); ?>" type="text" style="width: 98%;" value="<?php echo wp_htmledit_pre( stripslashes( hybrid_get_setting( 'info_title' ) ) ); ?>" />
				<p><?php _e( 'The main title of the box.', $prefix ); ?></p>
			</td>
		</tr>

		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'info_service_times_title' ); ?>"><?php _e( 'Service times title:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'info_service_times_title' ); ?>" name="<?php echo hybrid_settings_field_name( 'info_service_times_title' ); ?>" type="text" style="width: 98%;" value="<?php echo wp_htmledit_pre( stripslashes( hybrid_get_setting( 'info_service_times_title' ) ) ); ?>" />
				<p><?php _e( 'This text should say something like "Service Times".', $prefix ); ?></p>
			</td>
		</tr>

		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'info_service_times_data' ); ?>"><?php _e( 'Service times:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'info_service_times_data' ); ?>" name="<?php echo hybrid_settings_field_name( 'info_service_times_data' ); ?>" type="text" style="width: 98%;" value="<?php echo wp_htmledit_pre( stripslashes( hybrid_get_setting( 'info_service_times_data' ) ) ); ?>" />
				<p><?php _e( 'List the times of your service.', $prefix ); ?></p>
			</td>
		</tr>

		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'info_location' ); ?>"><?php _e( 'Location:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'info_location' ); ?>" name="<?php echo hybrid_settings_field_name( 'info_location' ); ?>" type="text" style="width: 98%;" value="<?php echo wp_htmledit_pre( stripslashes( hybrid_get_setting( 'info_location' ) ) ); ?>" />
				<p><?php _e( 'Link to a Google map.', $prefix ); ?></p>
			</td>
		</tr>

		<tr>
			<th><label for="<?php echo hybrid_settings_field_id( 'info_link' ); ?>"><?php _e( 'Link to more info:', $prefix ); ?></label></th>
			<td>
				<input id="<?php echo hybrid_settings_field_id( 'info_link' ); ?>" name="<?php echo hybrid_settings_field_name( 'info_link' ); ?>" type="text" style="width: 98%;" value="<?php echo wp_htmledit_pre( stripslashes( hybrid_get_setting( 'info_link' ) ) ); ?>" />
				<p><?php _e( 'Link to a page on your site for more information.', $prefix ); ?></p>
			</td>
		</tr>
										
	</table><!-- .form-table --><?php
}

?>
