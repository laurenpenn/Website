<?php
/*
Plugin Name: Network Roles for WordPress
Plugin URI: http://musupport.net
Description: Assign network roles in a WordPress network.
Version: 0.1.1
Author: Ron Rennick (http://ronandandrea.com)
Network: true

    Copyright:	(C) 2010 Ron Rennick, All rights reserved. Distributed under GPLv2
*/

class RA_Network_Roles {
	var $role_key = null;
	var $site_key = null;
	var $caps_key = null;
	var $site_option_key = 'ra_network_role_site';
	var $user_column = '_network_role';
	var $site_column = '_role_site';
	var $cap_user = null;
	var $cap_filter = false;
	var $network_site = null;
	var $network_admin = false;

	function RA_Network_Roles() {
		$this->__construct();
	}
	function  __construct() {
		global $current_site, $wpdb, $wp_version;
		if( !is_multisite() )
			return;
		$this->role_key = 'ra_' . $current_site->id . $this->user_column;
		$this->site_key = 'ra_' . $current_site->id . $this->site_column;
		$this->network_site = get_site_option( $this->site_option_key , $current_site->blog_id );
		$this->caps_key = $wpdb->get_blog_prefix( $this->network_site ) . 'capabilities';

		add_action( 'wpmu_blogs_columns', array( &$this, 'wpmu_blogs_columns' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'wpmu_users_columns', array( &$this, 'wpmu_users_columns' ) );
		add_action( 'init', array( &$this, 'init' ) );
		if( ( $this->network_admin = version_compare( $wp_version, '3.0.4', '>' ) ) ) {
			add_action( 'manage_sites_custom_column', array( &$this, 'manage_blogs_column' ), 10, 2 );
			add_filter( 'manage_users_custom_column', array( &$this, 'network_users_column' ), 10, 3 );
		} else {
			add_action( 'manage_users_custom_column', array( &$this, 'manage_users_column' ), 10, 2 );
			add_action( 'manage_blogs_custom_column', array( &$this, 'manage_blogs_column' ), 10, 2 );
		}
	}
	function wpmu_users_columns( $columns ) {
		if( current_user_can( 'manage_network_users' ) )
			$columns[$this->user_column] = __( 'Network Role' );

		return $columns;
	}
	function wpmu_blogs_columns( $columns ) {
		if( current_user_can( 'manage_network_users' ) )
			$columns[$this->site_column] = __( 'Network Role Site' );

		return $columns;
	}
	function network_users_column( $empty, $column, $user_id ) {
		if( $column == $this->user_column )
			return $this->manage_users_column( $column, $user_id );

		return $empty;
	}
	function manage_users_column( $column, $user_id ) {
		$output = '';
		if( $column != $this->user_column )
			return;
		if( is_super_admin( $user_id ) )
			$output = __( 'N/A' );
		else {
			$user_role = $this->get_user_role( $user_id );
			if( $this->network_admin )
				$url = wp_nonce_url( network_admin_url( 'users.php' ), $this->role_key . '-' . $user_id );
			else
				$url = wp_nonce_url( admin_url( 'users.php' ), $this->role_key . '-' . $user_id );
			if( get_user_meta( $user_id, $this->role_key, true ) )
				$output = sprintf( '<a href="%s">%s</a>', add_query_arg( array(  'revoke' . $this->user_column => $user_role, 'id' => $user_id ), $url ), sprintf( __( 'Remove %s permission' ), $user_role ) );
			else
				$output = sprintf( '<a href="%s">%s</a>', add_query_arg( array( 'grant' . $this->user_column => $user_role, 'id' => $user_id ), $url ), sprintf( __( 'Grant %s permission' ), $user_role ) );
		}
		if( $this->network_admin )
			return $output;

		echo $output;
	}
	function manage_blogs_column( $column, $blog_id ) {
		if( $column != $this->site_column )
			return;
		if( $blog_id == $this->network_site )
			echo '<strong>' . __( 'Current' ) . '</strong>';
		else {
			if( $this->network_admin )
				$url = wp_nonce_url( network_admin_url( 'users.php' ), $this->site_key . '-' . $blog_id );
			else
				$url = wp_nonce_url( admin_url( 'users.php' ), $this->site_key . '-' . $blog_id );
			printf( '<a href="%s">%s</a>', add_query_arg( array( $this->site_key => $blog_id ), $url ), __( 'Use for network roles' ) );
		}
	}
	function init() {
		if( !is_super_admin() && $this->network_site ) {
			$user = wp_get_current_user();
			if( get_user_meta( $user->ID, $this->role_key, true ) ) {
				add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
				if( $wpdb->blogid != $this->network_site ) {
					$this->cap_user = new WP_User( $user->ID );
					$this->cap_user->for_blog( $this->network_site );
					add_filter( 'user_has_cap', array( &$this, 'user_has_cap' ), 10, 3 );
				}
			}
		}
	}
	function admin_init() {
		global $parent_file;
		if( 'users.php' != $parent_file || !current_user_can( 'manage_network_users' ) || ( defined( 'WP_NETWORK_ADMIN' ) && !WP_NETWORK_ADMIN ) )
			return;

		if( !empty( $_GET['id'] ) && wp_verify_nonce( $_GET['_wpnonce'], $this->role_key . '-' . $_GET['id'] ) ) {
			$user_id = (int) $_GET['id'];
			$user_role = $this->get_user_role( $user_id );

			if( !empty( $_GET['revoke' . $this->user_column] ) && $user_role == $_GET['revoke' . $this->user_column] )
				delete_user_meta( $user_id, $this->role_key );
			elseif( !empty( $_GET['grant' . $this->user_column] ) && $user_role == $_GET['grant' . $this->user_column] )
				update_user_meta( $user_id, $this->role_key, '1' );
			wp_redirect( wp_get_referer() );
		} elseif( !empty( $_GET[$this->site_key] ) && wp_verify_nonce( $_GET['_wpnonce'], $this->site_key . '-' . $_GET[$this->site_key] ) ) {
			update_site_option( $this->site_option_key, $_GET[$this->site_key] );
			wp_redirect( wp_get_referer() );
		}
	}
	function user_has_cap( $allcaps, $caps, $args ) {
		if( !$this->cap_filter && $args[1] == $this->cap_user->ID ) {
			$this->cap_filter = true;
			foreach( $caps as $cap ) {
				if( $this->cap_user->has_cap( $cap ) )
					$allcaps[$cap] = true;
				else
					break;
			}
			$this->cap_filter = false;
		}
		return $allcaps;
	}
	function admin_menu() {
		add_submenu_page( 'index.php', __( 'Network Sites' ), __( 'Network Sites' ), 'read', 'network-sites', array( &$this, 'network_sites' ) );
	}
	function get_user_role( $user_id ) {
		$user_cap = get_user_meta( $user_id, $this->caps_key, true );
		if( empty( $user_cap ) )
			return 'login';

		reset( $user_cap );
		return key( $user_cap );
	}
	function network_sites() {
		global $wpdb;
		if( !$this->network_site )
			wp_die( __( 'You do not have sufficient permissions to view this page.' ) );

		$per_page = 40;
		$site_count = get_blog_count();
		$pages = ceil( $site_count / $per_page );
		$paged = 1;
		if( !empty( $_GET['paged'] ) )
			$paged = (int)$_GET['paged'];
		$start = ( ( $paged - 1 ) * $per_page );

		$blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid AND spam = '0' AND deleted = '0' and archived = '0' ORDER BY blog_id LIMIT $start,$per_page" );
		$num = count( $blogs );
		$cols = 2;
		if ( $num >= 20 )
			$cols = 4;
		$num_rows = ceil( $num / $cols );
		$split = 0;
		for ( $i = 1; $i <= $num_rows; $i++ ) {
			$rows[] = array_slice( $blogs, $split, $cols );
			$split = $split + $cols;
		} ?>
<div class="wrap">
<h2><?php echo esc_html( __( 'Network Sites' ) ); ?></h2>
<?php		$page_links = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $pages,
			'current' => $paged
		) );
		if ( $page_links ) {
			printf( '<div class="alignright tablenav-pages"><span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s</div>',
					number_format_i18n( ( $paged - 1 ) * $per_page + 1 ),
					number_format_i18n( min( $paged * $per_page, $site_count ) ),
					number_format_i18n( $site_count ),
					$page_links
					);
		} ?>
			<table class="widefat fixed">
<?php			$c = '';
		foreach ( $rows as $row ) {
			$c = $c == 'alternate' ? '' : 'alternate';
			echo "<tr class='$c'>";
			$i = 0;
			foreach ( $row as $blog ) {
				$s = $i == 3 ? '' : 'border-right: 1px solid #ccc;';
				echo "<td valign='top' style='$s'>";
				echo '<h3>' . get_blog_option( $blog, 'blogname' ) . '</h3>';
				echo '<p><a href="' . esc_url( get_home_url( $blog ) ) . '">' . __( 'Visit' ) . '</a> | <a href="' . esc_url( get_admin_url( $blog ) ) . '">' . __( 'Dashboard' ) . '</a></p>';
				echo "</td>";
				$i++;
			}
			echo "</tr>";
		} ?>
			</table></div>
<?php	}
}
if( defined( 'ABSPATH' ) )
	$ra_network_roles = new RA_Network_Roles();
?>