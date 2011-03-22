<?php

/* Actions added by the Edit Roles component. */
add_action( 'members_pre_components_form', 'members_message_no_edit_roles' );
add_action( 'members_pre_edit_role_form', 'members_message_no_edit_roles' );
add_action( 'members_pre_edit_roles_form', 'members_message_no_edit_roles' );

/**
 * Message to show when a single role has been deleted.
 * @since 0.1
 */
function members_message_role_deleted() {
	$message = __('Role deleted.', 'members');
	members_admin_message( '', $message );
}

/**
 * Message to show when multiple roles have been deleted (bulk delete).
 * @since 0.1
 */
function members_message_roles_deleted() {
	$message = __('Selected roles deleted.', 'members');
	members_admin_message( '', $message );
}

/**
 * Message to show when no role has the 'edit_roles' capability.
 * @since 0.1
 */
function members_message_no_edit_roles() {
	if ( is_active_members_component( 'edit_roles' ) && !members_check_for_cap( 'edit_roles' ) ) {
		$message = __('No role currently has the <code>edit_roles</code> capability.  Please add this to each role that should be able to manage/edit roles. If you do not change this, any user that has the <code>edit_users</code> capability will be able to edit roles.', 'members');
		members_admin_message( '', $message );
	}
}

/**
 * Loads the settings pages for the Manage Roles component.
 * @since 0.1
 */
function members_component_load_edit_roles() {
	global $members_manage_roles_page;

	/* Capability to manage roles.  Users need to change this on initial setup by giving at least one role the 'edit_roles' capability. */
	if ( members_check_for_cap( 'edit_roles' ) )
		$edit_roles_cap = 'edit_roles';
	else
		$edit_roles_cap = 'edit_users';

	/* Create the Manage Roles page. */
	$members_edit_roles_page = add_submenu_page( 'users.php', __('Roles', 'members'), __('Roles', 'members'), $edit_roles_cap, 'roles', 'members_edit_roles_page' );
}

/**
 * Loads the Manage Roles page.
 * @since 0.1
 */
function members_edit_roles_page() {
	require_once( MEMBERS_COMPONENTS . '/edit-roles/manage-roles.php' );
}

?>