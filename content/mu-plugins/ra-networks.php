<?php
/*
Plugin Name: WP Networks
Plugin URI: http://musupport.net
Description: Networks+ for WordPress.
Version: 0.2.2
Author: Ron Rennick (http://ronandandrea.com)

*/
/* Copyright:	(C) 2009-2011 Ron Rennick, All rights reserved.

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

function ra_add_network_page() {
	global $current_site;
	if( $current_site->id == 1 )
		add_submenu_page( 'sites.php', 'Networks+', 'Networks+', 'manage_networks', 'ra_network_page', 'ra_network_page' );
}
add_action( 'network_admin_menu', 'ra_add_network_page' );

function ra_super_admins_filter( $option_value ) {
	$results = array( 'admin' );
	if( is_array( $option_value ) ) {
		if( in_array( $results[0], $option_value ) )
			$results = $option_value;
		else
			$results = array_merge( $results, $option_value );
	}
	return $results;
}
add_filter( 'site_option_site_admins', 'ra_super_admins_filter', 1, 2 );

function ra_network_page() {
	global $wpdb, $current_site;
	if( !current_user_can( 'manage_networks' ) )
		wp_die( __( 'You do not have permission to access this page.' ) );

if ( $_POST ) {
	switch($_POST['action']) {
		case 'addsite': 
			check_admin_referer( 'ra-networks-add' );
			$domain = ra_get_clean_basedomain( $_POST[ 'basedomain' ] );
			if( !$domain ) {
				$msg = 'The domain name provided was not valid.';
			} else { 
				$weblog_title = stripslashes_deep( $_POST[ 'weblog_title' ] );
				if( strlen( $weblog_title ) > 0 )
					$msg = ra_add_network( $domain, $weblog_title );
				else
					$msg = 'Please provide a network title.';
			}
			do_action( 'netplus_addsite' );
			break;
		case 'deletesite':
			check_admin_referer( 'ra-networks' );
			if( $_POST['have_backup'] == 'yes' && $_POST['sites'] && is_array( $_POST['sites'] ) ) {
				$delete_blog = ( $_POST['del_blogs'] == 'yes' );
				$msg = '';
				foreach( $_POST['sites'] as $site ) {
					$siteinfo = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->site} WHERE id = %d", $site ) );
					if( $siteinfo ) {
						if( $delete_blog ) {
							$blogs = $wpdb->get_col(
								 $wpdb->prepare( "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = %d ORDER BY blog_id DESC", $site ) );
							if( $blogs ) {
								foreach( $blogs as $blog ) {
									wpmu_delete_blog( $blog, true );
									$msg .= "Deleted Site {$blog}<br />";
								}
							}
						} else {
							$wpdb->query(
								 $wpdb->prepare( "UPDATE {$wpdb->blogs} SET site_id = 1 WHERE site_id = %d", $site ) );
							$msg .= "Moved network {$siteinfo->domain} sites to {$current_site->domain}<br />";
						}
						$wpdb->query(
							$wpdb->prepare( "DELETE from {$wpdb->sitemeta} where site_id = %d", $site ) );
						$wpdb->query(
							$wpdb->prepare( "DELETE from {$wpdb->site} where id = %d", $site ) );
						$msg .= "Deleted network {$siteinfo->domain}<br />";
					}
				}
			}
			do_action( 'netplus_deletesite' );
			break;
	}
}	?>
<div class="wrap">
<?php	if($msg) { ?> 
		<div id="message" class="updated fade"><p><?php echo $msg ?></p></div>
<?php	} 
	$sites = $wpdb->get_results( 'SELECT s.id, CONCAT(s.domain,s.path) as uri, COUNT(*) as sites ' .
			"FROM {$wpdb->site} s JOIN {$wpdb->blogs} b ON s.id = b.site_id WHERE s.id > 1 GROUP BY s.id");
	$net_scheme = is_ssl() ? 'https://' : 'http://';
	$admin_scheme = force_ssl_admin() ? 'https://' : 'http://';

	if( is_array( $sites ) && !empty( $sites ) ) { ?>
		<form method='post'>
		<h2><?php _e( 'Networks' ); ?></h2>
<?php		wp_nonce_field( 'ra-networks' ); ?>
		<table width="100%" cellpadding="3" cellspacing="3" class="widefat">
			<thead>
				<tr>
					<th scope="col"><?php _e('delete'); ?></th>
					<th scope="col"><?php _e('id'); ?></th>
					<th scope="col"><?php _e('URL'); ?></th>
					<th scope="col"><?php _e('Admin'); ?></th>
					<th scope="col"><?php _e('Sites'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php		foreach( $sites as $site ) { ?>
				<tr>
					<td><input type="checkbox" id="site-<?php echo $site->id; ?>" name="sites[]" value="<?php echo $site->id; ?>" /></td>
					<td><?php echo $site->id; ?></td>
					<td><a href="<?php echo $net_scheme . $site->uri; ?>"><?php echo untrailingslashit( $site->uri ); ?></a></td>
					<td><a href="<?php echo $admin_scheme . $site->uri; ?>wp-admin/"><?php _e('Dashboard'); ?></a></td>
					<td><?php echo $site->sites; ?></td>
				</tr>
<?php		} ?> 
				<tr>
					<td class='submit'><input class="button" name='delete' type='submit' value='Delete Checked Networks' /></td>
					<td>
						<input type="checkbox" id="del_blogs" name="del_blogs" value='yes' />&nbsp;<?php _e("Delete sites in this network."); ?>
						<input type="hidden" name="action" value="deletesite" /><br /><strong><?php
							_e('Leave unchecked to move sites to the main network'); ?></strong>
					</td>
					<td><input type="checkbox" id="have_backup" name="have_backup" value='yes' />&nbsp;<?php _e('I have a current database backup.'); ?></td>
				</tr>
			</tbody>
		</table></form>
<?php	} ?>
	<form method='post'><?php	

	wp_nonce_field( 'ra-networks-add' ); 
	
	?><h2>Add Network</h2>
		<table class="form-table">  
			<tr> 
				<th scope='row'>Domain Name</th> 
				<td>
					<input type='text' name='basedomain' value='<?php echo $domain ?>' />
					
				</td> 
			</tr>
			<tr> 
				<th scope='row'>Network&nbsp;Title</th>
				<td>
					<input name='weblog_title' type='text' size='45' value='<?php echo $weblog_title ?>' />
					<br />What would you like to call your Network?
				</td> 
			</tr> 
			<tr> 
				<td class='submit'><input class="button" name='submit' type='submit' value='Add' /></td>
				<td><input type="hidden" name="action" value="addsite" /></td>
			</tr>
		</table><?php
		
	do_action( 'netplus_extra_fields' );
	
	?></form></div><?php
 }
function ra_add_network( $domain, $network_title ) {
	global $wpdb, $current_site, $current_user;
	
	if( $domain && $network_title ) {
		do_action( 'netplus_before_new_network', $domain, $network_title );
		
		if( !( $blog_id = apply_filters( 'netplus_new_network_blog_id', false ) ) )
			$blog_id = wpmu_create_blog( $domain, $current_site->path, $network_title, $current_user->id, array( 'blog_public' => 1, 'public' => 1 ) );
			
		if( is_wp_error( $blog_id ) )
			return $blog_id->get_error_message();

		if( $blog_id ) {
			$result = ra_populate_network( $blog_id, $domain, $current_user->user_email, $network_title, $current_site->path, is_subdomain_install() );
			if( !is_wp_error( $result ) || 'no_wildcard_dns' == $result->get_error_code() )
				$wpdb->update( $wpdb->blogs, array( 'site_id' => $blog_id ), array( 'blog_id' => $blog_id ) );

			if( is_wp_error( $result ) )
				return $result->get_error_message();
		}
		
		do_action( 'netplus_after_new_network', $blog_id, $domain, $current_site->path, $network_title );
		
		return __( 'Network created' );
	}
}
// add define for BP just in case
if ( !defined( 'BP_ROOT_BLOG' ) && isset( $current_site->blog_id ) )
	define( 'BP_ROOT_BLOG', $current_site->blog_id );

function ra_get_clean_basedomain( $domain ) {
	$domain = strtolower( preg_replace( '|https?://|', '', $domain ) );
	if ( ( $slash = strpos( $domain, '/' ) ) )
		$domain = substr( $domain, 0, $slash );
		
	return $domain;
}

function ra_populate_network( $network_id = 1, $domain = '', $email = '', $site_name = '', $path = '/', $subdomain_install = false ) {
	global $wpdb, $current_site, $wp_db_version, $wp_rewrite;

	$errors = new WP_Error();
	if( '' == $domain )
		$errors->add( 'empty_domain', __( 'You must provide a domain name.' ) );
	if( '' == $site_name )
		$errors->add( 'empty_sitename', __( 'You must provide a name for your network of sites.' ) );

	// check for network collision
	if( $network_id == $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->site WHERE id = %d", $network_id ) ) )
		$errors->add( 'siteid_exists', __( 'The network already exists.' ) );

	$site_user = get_user_by_email( $email );
	if( ! is_email( $email ) )
		$errors->add( 'invalid_email', __( 'You must provide a valid e-mail address.' ) );

	if( $errors->get_error_code() )
		return $errors;

	// set up site tables
	$template = get_option( 'template' );
	$stylesheet = get_option( 'stylesheet' );
	$allowed_themes = array( $stylesheet => true );
	if( $template != $stylesheet )
		$allowed_themes[ $template ] = true;
	if( WP_DEFAULT_THEME != $stylesheet && WP_DEFAULT_THEME != $template )
		$allowed_themes[ WP_DEFAULT_THEME ] = true;

	$blog_id = $network_id;
	if( 1 == $network_id ) {
		$wpdb->insert( $wpdb->site, array( 'domain' => $domain, 'path' => $path ) );
		$network_id = $wpdb->insert_id;
	} else {
		$wpdb->insert( $wpdb->site, array( 'domain' => $domain, 'path' => $path, 'id' => $network_id ) );
	}

	if( !is_multisite() ) {
		$site_admins = array( $site_user->user_login );
		$users = get_users_of_blog();
		if ( $users ) {
			foreach ( $users as $user ) {
				if ( is_super_admin( $user->ID ) && !in_array( $user->user_login, $site_admins ) )
					$site_admins[] = $user->user_login;
			}
		}
	} else {
		$site_admins = get_site_option( 'site_admins' );
	}

	if( !( $welcome_email = get_site_option( 'welcome_email', false ) ) ) {
		$welcome_email = __( 'Dear User,

Your new SITE_NAME site has been successfully set up at:
BLOG_URL

You can log in to the administrator account with the following information:
Username: USERNAME
Password: PASSWORD
Login Here: BLOG_URLwp-login.php

We hope you enjoy your new site.
Thanks!

--The Team @ SITE_NAME' );
	}
	
	$sitemeta = array(
		'site_name' => $site_name,
		'admin_email' => $site_user->user_email,
		'admin_user_id' => $site_user->ID,
		'registration' => 'none',
		'upload_filetypes' => 'jpg jpeg png gif mp3 mov avi wmv midi mid pdf',
		'blog_upload_space' => 10,
		'fileupload_maxk' => 1500,
		'site_admins' => $site_admins,
		'allowedthemes' => $allowed_themes,
		'illegal_names' => array( 'www', 'web', 'root', 'admin', 'main', 'invite', 'administrator', 'files' ),
		'wpmu_upgrade_site' => $wp_db_version,
		'welcome_email' => $welcome_email,
		'first_post' => __( 'Welcome to <a href="SITE_URL">SITE_NAME</a>. This is your first post. Edit or delete it, then start blogging!' ),
		// @todo - network admins should have a method of editing the network siteurl (used for cookie hash)
		'siteurl' => get_option( 'siteurl' ) . '/',
		'add_new_users' => '0',
		'upload_space_check_disabled' => '0',
		'subdomain_install' => intval( $subdomain_install ),
		'global_terms_enabled' => global_terms_enabled() ? '1' : '0'
	);
	if ( !intval( $subdomain_install ) )
		$sitemeta['illegal_names'][] = 'blog';

	$sitemeta = apply_filters( 'netplus_sitemeta', $sitemeta, $blog_id, $domain );
	$insert = '';
	foreach( $sitemeta as $meta_key => $meta_value ) {
		$meta_key = $wpdb->escape( $meta_key );
		if ( is_array( $meta_value ) )
			$meta_value = serialize( $meta_value );
		$meta_value = $wpdb->escape( $meta_value );
		if ( !empty( $insert ) )
			$insert .= ', ';
		$insert .= "( $network_id, '$meta_key', '$meta_value')";
	}
	$wpdb->query( "INSERT INTO $wpdb->sitemeta ( site_id, meta_key, meta_value ) VALUES " . $insert );

	if( !is_multisite() ) {
		$wpdb->insert( $wpdb->blogs, array( 'site_id' => $network_id, 'domain' => $domain, 'path' => $path, 'registered' => current_time( 'mysql' ) ) );
		$blog_id = $wpdb->insert_id;
		update_user_meta( $site_user->ID, 'source_domain', $domain );
		update_user_meta( $site_user->ID, 'primary_blog', $blog_id );
		if ( !$upload_path = get_option( 'upload_path' ) ) {
			$upload_path = substr( WP_CONTENT_DIR, strlen( ABSPATH ) ) . '/uploads';
			update_option( 'upload_path', $upload_path );
		}
		update_option( 'fileupload_url', get_option( 'siteurl' ) . '/' . $upload_path );
	}

	if( is_multisite() )
		switch_to_blog( $blog_id );
		
	if( $subdomain_install )
		update_option( 'permalink_structure', '/%year%/%monthnum%/%day%/%postname%/');
	else
		update_option( 'permalink_structure', '/blog/%year%/%monthnum%/%day%/%postname%/');

	update_option( 'rewrite_rules', '' );
	
	if( is_multisite() )
		restore_current_blog();

	if ( $subdomain_install ) {
		$vhost_ok = false;
		$errstr = '';
		$hostname = substr( md5( time() ), 0, 6 ) . '.' . $domain; // Very random hostname!
		$page = wp_remote_get( 'http://' . $hostname, array( 'timeout' => 5, 'httpversion' => '1.1' ) );
		if ( is_wp_error( $page ) )
			$errstr = $page->get_error_message();
		elseif ( 200 == $page['response']['code'] )
				$vhost_ok = true;

		if ( ! $vhost_ok ) {
			$msg = '<p><strong>' . __( 'Warning! Wildcard DNS may not be configured correctly!' ) . '</strong></p>';
			$msg .= '<p>' . sprintf( __( 'The installer attempted to contact a random hostname (<code>%1$s</code>) on your domain.' ), $hostname );
			if ( ! empty ( $errstr ) )
				$msg .= ' ' . sprintf( __( 'This resulted in an error message: %s' ), '<code>' . $errstr . '</code>' );
			$msg .= '</p>';
			$msg .= '<p>' . _e( 'To use a subdomain configuration, you must have a wildcard entry in your DNS. This usually means adding a <code>*</code> hostname record pointing at your web server in your DNS configuration tool.' ) . '</p>';
			$msg .= '<p>' . __( 'You can still use your site but any subdomain you create may not be accessible. If you know your DNS is correct, ignore this message.' ) . '</p>';
			return new WP_Error( 'no_wildcard_dns', $msg );
		}
	}

	do_action( 'netplus_after_populate_network', $network_id, $blog_id, $domain, $email, $site_name, $path, $subdomain_install );
	
	return true;
}
