<?php
/**
 * The New Roles component allows users with the 'create_roles' capability to 
 * create new roles for use on the site.  
 *
 * @package Members
 * @subpackage Components
 */

/* Add message when no role has the 'create_roles' capability. */
add_action( 'members_pre_components_form', 'members_message_no_create_roles' );
add_action( 'members_pre_new_role_form', 'members_message_no_create_roles' );

/**
 * Returns an array of capabilities that should be set on the New Role admin screen.
 * By default, the only capability checked is 'read' because it's fairly common.
 *
 * @since 0.1
 * @return $capabilities array Default capabilities for new roles.
 */
function members_new_role_default_capabilities() {

	$capabilities = array( 'read' );

	/* Filters should return an array. */
	return apply_filters( 'members_new_role_default_capabilities', $capabilities );
}

/**
 * Displays a message if the New Roles component is active and no 
 * roles have the 'create_roles' capability.
 *
 * @since 0.1
 */
function members_message_no_create_roles() {
	if ( is_active_members_component( 'new_roles' ) && !members_check_for_cap( 'create_roles' ) ) {
		$message = __('To create new roles, you must give the <code>create_roles</code> capability to at least one role.', 'members');
		members_admin_message( '', $message );
	}
}

/**
 * Loads the settings pages for the New Roles component.  For a logged-in
 * user to see this additional page, they must have the 'create_roles' capability.
 * In order to gain this capability, one should use the Edit Roles component to give
 * a role or multiple roles this capability.
 *
 * @since 0.1
 * @uses add_submenu_page() Adds a submenu to the users menu.
 */
function members_component_load_new_roles() {
	global $members_new_role_page;

	/* Create the New Role page. */
	$members_new_roles_page = add_submenu_page( 'users.php', __('New Role', 'members'), __('New Role', 'members'), 'create_roles', 'new-role', 'members_new_role_page' );
}

/**
 * Loads the New Role page when its needed.
 *
 * @since 0.1
 */
function members_new_role_page() {
	require_once( MEMBERS_COMPONENTS . '/new-roles/new-role.php' );
}

?>