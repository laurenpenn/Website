<?php

/**
Plugin Name: MU Multi-Site
Plugin URI: http://www.jerseyconnect.net/development/
Description: Adds a Sites panel for site admins to create and manipulate multiple sites.
Version: 0.0.9
Author: David Dean
Author URI: http://www.jerseyconnect.net/
*/

/* ========================================================================== */

/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if(!defined('ENABLE_SITE_ZERO')) {

	/** true = enable the holding site, must be true to save orphaned blogs, below */
	define('ENABLE_SITE_ZERO',TRUE);
}

if (!defined('RESCUE_ORPHANED_BLOGS')) {

	/** 
	   true = redirect blogs from deleted site to holding site, instead of deleting them.  Requires site zero above.
	   false = allow blogs belonging to deleted sites to be deleted.
	 */
	define('RESCUE_ORPHANED_BLOGS',FALSE);
}

/** blog options affected by URL */
$options_list = array('siteurl','home','fileupload_url');

/** sitemeta options to be copied on clone */
$options_to_copy = array(
	'admin_email'				=> __('Site admin email'),
	'admin_user_id'				=> __('Admin user ID - deprecated'),
	'allowed_themes'			=> __('OLD List of allowed themes - deprecated'),
	'allowedthemes'				=> __('List of allowed themes'),
	'banned_email_domains'		=> __('Banned email domains'),
	'first_post'				=> __('Content of first post on a new blog'),
	'limited_email_domains'		=> __('Permitted email domains'),
	'site_admins'				=> __('List of site admin usernames'),
	'welcome_email'				=> __('Content of welcome email')
);

define('SITES_PER_PAGE',10);

if(!function_exists('are_new_wpdb_funcs_available')) {
	
	/**
	 * Check to see if new WPDB functions (insert, update, prepare) are available and cache the result
	 * @return boolean Result of check for these functions 
	 */
	function are_new_wpdb_funcs_available() {
		static $available = 'unchecked';

		if(is_bool($available))	return $available;
		
		global $wpdb;
		
		$available = method_exists($wpdb,'insert');
		return $available;
	}
}

if(!function_exists('site_exists')) {

	/**
	 * Check to see if a site exists. Will check the sites object before checking the database.
	 * @param integer $site_id ID of site to verify
	 * @return boolean TRUE if found, FALSE otherwise
	 */
	function site_exists($site_id) {
		global $sites, $wpdb;
		$site_id = (int)$site_id;
		foreach($sites as $site) {
			if($site_id == $site->id) {
				return TRUE;
			}
		}
		
		/* check db just to be sure */
		$site_list = $wpdb->get_results('SELECT id FROM ' . $wpdb->site);
		if($site_list) {
			foreach($site_list as $site) {
				if($site->id == $site_id) {
					return TRUE;
				}
			}
		}
		
		return FALSE;
	}
}

if(!function_exists('switch_to_site')) {

	/**
	 * Problem: the various *_site_options() functions operate only on the current site
	 * Workaround: change the current site
	 * @param integer $new_site ID of site to manipulate
	 */
	function switch_to_site($new_site) {
		global $tmpoldsitedetails, $wpdb, $site_id, $switched_site, $switched_site_stack, $current_site, $sites;

		if ( !site_exists($new_site) )
			$new_site = $site_id;

		if ( empty($switched_site_stack) )
			$switched_site_stack = array();

		$switched_site_stack[] = $site_id;

		if ( $new_site == $site_id )
			return;

		// backup
		$tmpoldsitedetails[ 'site_id' ] 	= $site_id;
		$tmpoldsitedetails[ 'id']			= $current_site->id;
		$tmpoldsitedetails[ 'domain' ]		= $current_site->domain;
		$tmpoldsitedetails[ 'path' ]		= $current_site->path;
		$tmpoldsitedetails[ 'site_name' ]	= $current_site->site_name;

		
		foreach($sites as $site) {
			if($site->id == $new_site) {
				$current_site = $site;
				break;
			}
		}


		$wpdb->siteid			 = $new_site;
		$current_site->site_name = get_site_option('site_name');
		$site_id = $new_site;

		do_action('switch_site', $site_id, $tmpoldsitedetails[ 'site_id' ]);

		$switched_site = true;
	}
}

if(!function_exists('restore_current_site')) {

	/**
	 * Return to the operational site after our operations
	 */
	function restore_current_site() {
		global $tmpoldsitedetails, $wpdb, $site_id, $switched_site, $switched_site_stack;

		if ( !$switched_site )
			return;

		$site_id = array_pop($switched_site_stack);

		if ( $site_id == $current_site->id )
			return;

		// backup

		$prev_site_id = $wpdb->site_id;

		$wpdb->siteid = $site_id;
		$current_site->id = $tmpoldsitedetails[ 'id' ];
		$current_site->domain = $tmpoldsitedetails[ 'domain' ];
		$current_site->path = $tmpoldsitedetails[ 'path' ];
		$current_site->site_name = $tmpoldsitedetails[ 'site_name' ];

		unset( $tmpoldsitedetails );

		do_action('switch_site', $site_id, $prev_site_id);

		$switched_site = false;
		
	}
}

if (!function_exists('add_site')) {

	/**
	 * Add a new site
	 * @param string $domain domain name for new site - for VHOST=no, this shouyld be FQDN, otherwise domain only
	 * @param string $path path to root of site hierarchy - should be '/' unless WPMu is cohabiting with another product on a domain
	 * @param string $blog_name Name of the root blog to be created on the new site
	 * @param integer $cloneSite ID of site whose sitemeta values are to be copied - default NULL
	 * @param array $options_to_clone override default sitemeta options to copy when cloning - default NULL
	 * @return integer ID of newly created site
	 */
	function add_site($domain, $path, $blog_name = NULL, $cloneSite = NULL, $options_to_clone = NULL) {

		if($blog_name == NULL) $blog_name = __('New Site Created');

		global $wpdb, $sites, $options_to_copy;
		if(is_null($options_to_clone)) {
			$options_to_clone = array_keys($options_to_copy);
		}
		$query = "SELECT * FROM {$wpdb->site} WHERE domain='" . $wpdb->escape($domain) . "' AND path='" . $wpdb->escape($path) . "' LIMIT 1";
		$site = $wpdb->get_row($query);
		if($site) {
			return new WP_Error('site_exists',__('Site already exists.'));
		}
		
		if(are_new_wpdb_funcs_available()) {

			$wpdb->insert($wpdb->site,array(
				'domain'	=> $domain,
				'path'		=> $path
			));
			$new_site_id =  $wpdb->insert_id;
			
		} else {
		
			$query = "INSERT INTO {$wpdb->site} (domain, path) VALUES ('" . $wpdb->escape($domain) . "','" . $wpdb->escape($path) . "')";
			$wpdb->query($query);
			$new_site_id =  $wpdb->insert_id;

		}
		
		/* update site list */
		$sites = $wpdb->get_results('SELECT * FROM ' . $wpdb->site);

		if($new_site_id) {

			/* prevent ugly database errors - #184 */
			if(!defined('WP_INSTALLING')) {
				define('WP_INSTALLING',TRUE);
			}

			$new_blog_id = wpmu_create_blog($domain,$path,$blog_name,get_current_user_id(),'',(int)$new_site_id);
			if(is_a($new_blog_id,'WP_Error')) {
				return $new_blog_id;
			}
		}
		
		/** if selected, clone the sitemeta from an existing site */
				
		if(!is_null($cloneSite) && site_exists($cloneSite)) {

			$optionsCache = array();
			
			switch_to_site((int)$cloneSite);
			
			foreach($options_to_clone as $option) {
				$optionsCache[$option] = get_site_option($option);
			}
			
			restore_current_site();

			switch_to_site($new_site_id);
			
			foreach($options_to_clone as $option) {
				if($optionsCache[$option] !== false) {
					add_site_option($option, $optionsCache[$option]);
				}
			}
			unset($optionsCache);
			
			restore_current_site();

		}

		do_action( 'wpmu_add_site' , $new_site_id );

		return $new_site_id;
	}
}

if (!function_exists('update_site')) {

	/**
	 * Modify the domain and path of an existing site - and update all of its blogs
	 * @param integer id ID of site to modify
	 * @param string $domain new domain for site
	 * @param string $path new path for site
	 */
	function update_site($id, $domain, $path='') {

		global $wpdb, $wpmuBaseTablePrefix;
		global $options_list;

		if(!site_exists((int)$id)) {
			return new WP_Error('site_not_exist',__('Site does not exist.'));
		}

		$query = "SELECT * FROM {$wpdb->site} WHERE id=" . (int)$id;
		$site = $wpdb->get_row($query);
		if(!$site) {
			return new WP_Error('site_not_exist',__('Site does not exist.'));
		}

		if(are_new_wpdb_funcs_available()) {

			$update = array('domain'	=> $domain);
			if($path != '') {
				$update['path'] = $path;
			}

			$where = array('id'	=> (int)$id);
			$update_result = $wpdb->update($wpdb->site,$update,$where);

		} else {

			$domain = $wpdb->escape($domain);
			$path   = $wpdb->escape($path);
			
			$query = "UPDATE {$wpdb->site} SET domain='" . $domain . "' ";
			if($path != '') {
				$query .= ", path='" . $path . "' ";
			}
			$query .= ' WHERE id=' . (int)$id;
			$update_result = $wpdb->query($query);

		}

		if(!$update_result) {
			return new WP_Error('site_not_updated',__('Site could not be updated.'));
		}

		$path = (($path != '') ? $path : $site->path );
		$fullPath = $domain . $path;
		$oldPath = $site->domain . $site->path;

		/** also updated any associated blogs */
		$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id=" . (int)$id;
		$blogs = $wpdb->get_results($query);
		if($blogs) {
			foreach($blogs as $blog) {
				$domain = str_replace($site->domain,$domain,$blog->domain);
				
				if(are_new_wpdb_funcs_available()) {
					
					$wpdb->update(
						$wpdb->blogs,
						array(	'domain'	=> $domain,
								'path'		=> $path
							),
						array(	'blog_id'	=> (int)$blog->blog_id	)
					);
					
				} else {
					
					$query = "UPDATE {$wpdb->blogs} SET domain='" . $domain . "', path='" . $path . "' WHERE blog_id=" . (int)$blog->blog_id;
					$wpdb->query($query);					
					
				}

				/** fix options table values */
				$optionTable = $wpmuBaseTablePrefix . (int)$blog->blog_id . "_options";

				foreach($options_list as $option_name) {
					$option_value = $wpdb->get_row("SELECT * FROM $optionTable WHERE option_name='$option_name'");
					if($option_value) {
						$newValue = str_replace($oldPath,$fullPath,$option_value->option_value);
						update_blog_option($blog->blog_id,$option_name,$newValue);
//						$wpdb->query("UPDATE $optionTable SET option_value='$newValue' WHERE option_name='$option_name'");
					}
				}
			}
		}
		
		do_action( 'wpmu_update_site' , $id, array('domain'=>$site->domain, 'path'=>$site->path) );
		
	}
}

if (!function_exists('delete_site')) {

	/**
	 * Delete a site and all its blogs
	 * @param integer id ID of site to delete
	 * @param boolean $delete_blogs flag to permit blog deletion - default setting of FALSE will prevent deletion of occupied sites
	 */
	function delete_site($id,$delete_blogs = FALSE) {
		global $wpdb;

		$override = $delete_blogs;

		/* ensure we got a valid site id */
		$query = "SELECT * FROM {$wpdb->site} WHERE id=" . (int)$id;
		$site = $wpdb->get_row($query);
		if(!$site) {
			return new WP_Error('site_not_exist',__('Site does not exist.'));
		}

		/* ensure there are no blogs attached to this site */
		$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id=" . (int)$id;
		$blogs = $wpdb->get_results($query);
		if($blogs && !$override) {
			return new WP_Error('site_not_empty',__('Cannot delete site with blogs.'));
		}

		if($override) {
			if($blogs) {
				foreach($blogs as $blog) {
					if(RESCUE_ORPHANED_BLOGS && ENABLE_SITE_ZERO) {
						move_blog($blog->blog_id,0);
					} else {
						wpmu_delete_blog($blog->blog_id,true);
					}
				}
			}
		}

		$query = "DELETE FROM {$wpdb->site} WHERE id=" . (int)$id;
		$wpdb->query($query);

		$query = "DELETE FROM {$wpdb->sitemeta} WHERE site_id=" . (int)$id;
		$wpdb->query($query);
		
		do_action( 'wpmu_delete_site' , $site );
	}
}

if(!function_exists('move_blog')) {

	/**
	 * Move a blog from one site to another
	 * @param integer $blog_id ID of blog to move
	 * @param integer $new_site_id ID of destination site
	 */
	function move_blog($blog_id, $new_site_id) {

		global $wpdb, $wpmuBaseTablePrefix;

		global $options_list;

		/* sanity checks */
		$query = "SELECT * FROM {$wpdb->blogs} WHERE blog_id=" . (int)$blog_id;
		$blog = $wpdb->get_row($query);
		if(!$blog) {
			return new WP_Error('blog not exist',__('Blog does not exist.'));
		}

		if((int)$new_site_id == $blog->site_id) { return true;	}
		
		$old_site_id = $blog->site_id;
		
		if(ENABLE_SITE_ZERO && $blog->site_id == 0) {
			$oldSite->domain = 'holding.blogs.local';
			$oldSite->path = '/';
			$oldSite->id = 0;
		} else {
			$query = "SELECT * FROM {$wpdb->site} WHERE id=" . (int)$blog->site_id;
			$oldSite = $wpdb->get_row($query);
			if(!$oldSite) {
				return new WP_Error('site_not_exist',__('Site does not exist.'));
			}
		}

		if($new_site_id == 0 && ENABLE_SITE_ZERO) {
			$newSite->domain = 'holding.blogs.local';
			$newSite->path = '/';
			$newSite->id = 0;
		} else {
			$query = "SELECT * FROM {$wpdb->site} WHERE id=" . (int)$new_site_id;
			$newSite = $wpdb->get_row($query);
			if(!$newSite) {
				return new WP_Error('site_not_exist',__('Site does not exist.'));
			}
		}

		if(defined('VHOST') && VHOST == 'yes') {

			$exDom = substr($blog->domain,0,(strpos($blog->domain,'.')+1));
			$domain = $exDom . $newSite->domain;
			
		} else {

			$domain = $newSite->domain;
			
		}
		$path = $newSite->path . substr($blog->path,strlen($oldSite->path) );
		
		if(are_new_wpdb_funcs_available()) {
			
			$update_result = $wpdb->update(
				$wpdb->blogs,
				array(	'site_id'	=> $newSite->id,
						'domain'	=> $domain,
						'path'		=> $path
				),
				array(	'blog_id'	=> $blog->blog_id)
			);
			
		} else {
		
			$update_result = $query = "UPDATE {$wpdb->blogs} SET site_id=" . $newSite->id . ", domain='" . $domain . "', path='" . $path . "' WHERE blog_id=" . $blog->blog_id;
			$wpdb->query($query);
			
		}
		
		if(!$update_result) {
			return new WP_Error('blog_not_moved',__('Blog could not be moved.'));
		}
		

		/** change relevant blog options */

		$optionsTable = $wpmuBaseTablePrefix . (int)$blog->blog_id . "_options";

		$oldDomain = $oldSite->domain . $oldSite->path;
		$newDomain = $newSite->domain . $newSite->path;

		foreach($options_list as $option_name) {
			$option = $wpdb->get_row("SELECT * FROM $optionsTable WHERE option_name='" . $option_name . "'");
			$newValue = str_replace($oldDomain,$newDomain,$option->option_value);
			update_blog_option($blog->blog_id,$option_name,$newValue);
//			$query = "UPDATE $optionsTable SET option_value='$newValue' WHERE option_name='$option_name'";
//			$wpdb->query($query);
		}
		
		do_action( 'wpmu_move_blog' , $blog_id, $old_site_id, $new_site_id );
	}
}

class dd_Sites
{

	function dd_Sites()
	{
		if(function_exists('add_action')) {
			add_action('admin_menu', array(&$this, 'add_sites_menu'));
			add_action('wpmublogsaction',array(&$this,'assign_blogs_link'));
		}
		
	}

	function assign_blogs_link() {
		global $blog;
		echo '<a href="wpmu-admin.php?page=sites&amp;action=move&amp;blog_id=' . $blog['blog_id'] . '" class="edit">' . __('Move') . '</a>';			
	}

	function add_sites_menu()
	{
		add_submenu_page('wpmu-admin.php', __('Sites'), __('Sites'), 'manage_options', 'sites', array(&$this, 'sites_page'));
	}

	/** ====== config_page ====== */
	function sites_page()
	{
		global $wpdb;
		global $wpmuBaseTablePrefix;
		global $options_to_copy;

		if ( !is_site_admin() ) {
		    wp_die( __('<p>You do not have permission to access this page.</p>') );
		}

		if(isset($_POST['update']) && isset($_GET['id'])) {
			$this->update_site_page();
		}

		if(isset($_POST['delete']) && isset($_GET['id'])) {
			$this->delete_site_page();
		}
		
		if(isset($_POST['delete_multiple']) && isset($_POST['deleted_sites'])) {
			$this->delete_multiple_site_page();
		}

		if(isset($_POST['add']) && isset($_POST['domain']) && isset($_POST['path'])) {
			$this->add_site_page();
		}

		if(isset($_POST['move']) && isset($_GET['blog_id'])) {
			$this->move_blog_page();
		}
		
		if(isset($_POST['reassign']) && isset($_GET['id'])) {
			$this->reassign_blog_page();
		}

		if (isset($_GET['updated'])) {
		    ?><div id="message" class="updated fade"><p><?php _e('Options saved.') ?></p></div><?php
		} else if(isset($_GET['added'])) {
			?><div id="message" class="updated fade"><p><?php _e('Site created.'); ?></p></div><?php
		} else if(isset($_GET['deleted'])) {
			?><div id="message" class="updated fade"><p><?php _e('Site(s) deleted.'); ?></p></div><?php
		}

		print '<div class="wrap" style="position: relative">';

		switch( $_GET[ 'action' ] ) {
		case 'move':
			$this->move_blog_page();
			break;
		case 'assignblogs':
			$this->reassign_blog_page();
			break;
		case 'deletesite':
			$this->delete_site_page();
			break;
		case 'editsite':
			$this->update_site_page();
			break;
		case 'delete_multisites':
			$this->delete_multiple_site_page();
			break;
	    default:
			
			/** strip off the action tag */
            $queryStr = substr($_SERVER['REQUEST_URI'],0,(strpos($_SERVER['REQUEST_URI'],'?')+1));
			$getParams = array();
			$badParams = array('action','id','updated','deleted');
			foreach($_GET as $getParam => $getValue) {
				if(!in_array($getParam,$badParams)) {
					$getParams[] = $getParam . '=' . $getValue;
				}
			}
			$queryStr .= implode('&',$getParams);

			$searchConditions = '';				
			if(isset($_GET['s'])) {
				if(isset($_GET['search']) && $_GET['search'] == __('Search Domains')) {
					$searchConditions = 'WHERE ' . $wpdb->site . '.domain LIKE ' . "'%" . $wpdb->escape($_GET['s']) . "%'";
				}
			}

			$count = $wpdb->get_col('SELECT COUNT(id) FROM ' . $wpdb->site . $searchConditions);
			$total = $count[0];

			if( isset( $_GET[ 'start' ] ) == false ) {
				$start = 1;
			} else {
				$start = intval( $_GET[ 'start' ] );
			}
			if( isset( $_GET[ 'num' ] ) == false ) {
				$num = SITES_PER_PAGE;
			} else {
				$num = intval( $_GET[ 'num' ] );
			}
				
			$query = "SELECT {$wpdb->site}.*, COUNT({$wpdb->blogs}.blog_id) as blogs, {$wpdb->blogs}.path as blog_path 
				FROM {$wpdb->site} LEFT JOIN {$wpdb->blogs} ON {$wpdb->blogs}.site_id = {$wpdb->site}.id $searchConditions GROUP BY {$wpdb->site}.id" ; 

			if( isset( $_GET[ 'sortby' ] ) == false ) {
				$_GET[ 'sortby' ] = 'ID';
			}

		switch($_GET['sortby']) {
			case 'Domain':
				$query .= ' ORDER BY ' . $wpdb->site . '.domain ';
				break;
			case 'ID':
				$query .= ' ORDER BY ' . $wpdb->site . '.id ';
				break;
			case 'Path':
				$query .= ' ORDER BY ' . $wpdb->site . '.path ';
				break;
			case 'Blogs':
				$query .= ' ORDER BY blogs ';
				break;
		}

		if( $_GET[ 'order' ] == 'DESC' ) {
			$query .= 'DESC';
		} else {
			$query .= 'ASC';
		}

		$query .= ' LIMIT ' . (((int)$start - 1 ) * $num ) . ', ' . intval( $num );

		$blog_list = $wpdb->get_results( $query, ARRAY_A );
		if( count( $blog_list ) < $num ) {
			$next = false;
		} else {
			$next = true;
		}

?>

<h2><?php _e ('Sites') ?></h2>
<form name="searchform" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="get" style="position: absolute; right: 0pt; top: 0pt;"> 
	<input type="text" name="s" />
	<input type="hidden" name="page" value="sites" />
	<input type="submit" name="search" id="search" class="button" value="<?php _e('Search Domains'); ?>" />
</form>
<?php

// define the columns to display, the syntax is 'internal name' => 'display name'
$sites_columns = array(
  'id'      => __('ID'),
  'domain'	=> __('Domain'),
  'path'	=> __('Path'),
  'blogs'	=> __('Blogs'),
);
$sites_columns = apply_filters('manage_sites_columns', $sites_columns);

// you can not edit these at the moment
$sites_columns['control_backend']      = '';
$sites_columns['control_edit']      = '';
$sites_columns['control_delete']    = '';

	// Pagination
	$site_navigation = paginate_links( array(
		'base' => add_query_arg( 'start', '%#%' ),
		'format' => '',
		'total' => ceil($total / $num),
		'current' => $start
	));


?>
<form name='formlist' action='<?php echo $_SERVER['REQUEST_URI'] . "&amp;action=delete_multisites"; ?>' method='POST'>
<div class="tablenav">
	<?php if ( $site_navigation ) echo "<div class='tablenav-pages'>$site_navigation</div>"; ?>	
	<div class="alignleft">
		<input type="submit" class="button-secondary delete" name="allsite_delete" value="<?php _e('Delete'); ?>" />
		<?php if(isset($_GET['s'])) { ?>
			<p><?php _e('Filter'); ?>: <a href="./wpmu-admin.php?page=sites" title="<?php _e('Remove this filter') ?>"><?php echo $wpdb->escape($_GET['s']) ?></a></p>
		<?php } ?>
	</div>
</div>
<br class="clear" />
<table width="100%" cellpadding="3" cellspacing="3" class="widefat"> 
	<thead>
        <tr>

<?php foreach($sites_columns as $col_name => $column_display_name) { ?>
        <th scope="col">
        	<?php if($col_name == 'id') { ?>
        		<input type="checkbox" id="select_all">
        	<?php } ?>
        	<a href="wpmu-admin.php?page=sites&sortby=<?php echo urlencode( $column_display_name ) ?>&<?php if( $_GET[ 'sortby' ] == $column_display_name ) { if( $_GET[ 'order' ] == 'DESC' ) { echo "order=ASC&" ; } else { echo "order=DESC&"; } } ?>start=<?php echo $start ?>"><?php echo $column_display_name; ?></a>
        </th>
<?php } ?>

        </tr>
	</thead>
<?php

if ($blog_list) {
        $bgcolor = '';
        foreach ($blog_list as $blog) { 
                $class = ('alternate' == $class) ? '' : 'alternate';
                print '<tr class="' . $class . '">';
                if( constant( "VHOST" ) == 'yes' ) { 
                        $blogname = str_replace( '.' . $current_site->domain, '', $blog[ 'domain' ] ); 
                } else { 
                        $blogname = $blog[ 'path' ]; 
                }

foreach($sites_columns as $column_name=>$column_display_name) {

        switch($column_name) {

        case 'id':
                ?>
                <th scope="row" class="check-column" style="width: auto"><input type='checkbox' id='<?php echo $blog[ 'id' ] ?>' name='allsites[]' value='<?php echo $blog[ 'id' ] ?>'> <label for='<?php echo $blog[ 'id' ] ?>'><?php echo $blog[ 'id' ] ?></label></th>
                <?php
                break;
	case 'domain':
		?>
		<td valign='top'><label for='<?php echo $blog[ 'id' ] ?>'><?php echo $blog['domain'] ?></label></td>
		<?php
		break;
	case 'path':
		?>
		<td valign='top'><label for='<?php echo $blog[ 'id' ] ?>'><?php echo $blog['path'] ?></label></td>
		<?php
		break;
	case 'blogs':
		?>
		<td valign='top'><a href="http://<?php echo $blog['domain'] . $blog['blog_path'];?>wp-admin/wpmu-blogs.php" title="<?php _e('Blogs on this site'); ?>"><?php echo $blog['blogs'] ?></a></td>
		<?php
		break;
	case 'control_edit':
		?>
		<td valign="top"><a class="edit" href="<?php echo $queryStr . "&amp;action=editsite&amp;id=" .  $blog['id']; ?>" title="<?php _e('Edit this site'); ?>"><?php _e('Edit'); ?></a></td>
		<?php
		break;
	case 'control_backend':
		?>
		<td valign="top"><a class="edit" href="<?php echo $queryStr . "&amp;action=assignblogs&amp;id=" . $blog['id']; ?>" title="<?php _e('Assign blogs to this site'); ?>"><?php _e('Assign Blogs'); ?></a></td>
		<?php
		break;
	case 'control_delete':
		if($blog['blogs'] == 0 || $blog['id'] != 1) {
		?>
		<td valign="top"><a class="delete" href="<?php echo $queryStr . "&amp;action=deletesite&amp;id=" . $blog['id']; ?> " title="<?php _e('Delete this site'); ?>"><?php _e('Delete'); ?></a></td>
		<?php
		}
		break;
        default:
                ?>
                <td valign='top'><?php do_action('manage_sites_custom_column', $column_name, $blog['id']); ?></td>
                <?php
                break;
        }
}
?>
        </tr>
<?php
}
} else {
?>
  <tr style=''>
    <td colspan="8"><?php _e('No sites found.') ?></td> 
  </tr> 
<?php
} // end if ($blogs)
?>
</table>
</form>
<h2><?php _e('Add Site'); ?></h2>
<form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] . "&amp;action=addsite"; ?>">
	<table class="form-table">
		<tr><th scope="row"><label for="newName"><?php _e('Site Name'); ?>:</label></th><td><input type="text" name="name" id="newName" title="<?php _e('A friendly name for your new site'); ?>" /></td></tr>
		<tr><th scope="row"><label for="newDom"><?php _e('Domain'); ?>:</label></th><td> http://<input type="text" name="domain" id="newDom" title="<?php _e('The domain for your new site'); ?>" /></td></tr>
		<tr><th scope="row"><label for="newPath"><?php _e('Path'); ?>:</label></th><td><input type="text" name="path" id="newPath" title="<?php _e('If you are unsure, put in /'); ?>" /></td></tr>
		<tr><th scope="row"><label for="newBlog"><?php _e('Blog Name'); ?>:</label></th><td><input type="text" name="newBlog" id="newBlog" title="<?php _e('The name for the new site\'s blog'); ?>" /></td></tr>
	</table>
	<div class="metabox-holder meta-box-sortables" id="advanced_site_options">
	<div class="postbox if-js-closed">
		<div title="Click to toggle" class="handlediv"><br/></div>
		<h3><span><?php _e('Advanced Options'); ?></span></h3>
		<div class="inside">

			<table class="form-table">
			<tr>
				<th scope="row"><label for="cloneSite"><?php _e('Clone Site'); ?>:</label></th>
				<?php	$site_list = $wpdb->get_results( 'SELECT id, domain FROM ' . $wpdb->site , ARRAY_A );	?>
				<td colspan="2"><select name="cloneSite" id="cloneSite"><option value="0"><?php _e('Do Not Clone'); ?></option><?php foreach($site_list as $site) { echo '<option value="' . $site['id'] . '"' . ($site['id'] == 1 ? ' selected' : '' ) . '>' . $site['domain'] . '</option>'; } ?></select></td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label><?php _e('Options to Clone'); ?>:</label></th>
				<td>
				</td>
				<td valign="top">
					<p><?php _e('Options added by plugins may not exist on all sites.'); ?></p>
				</td>
			</tr>
			<tr>
				<td></td>
					<?php
						$all_site_options = $wpdb->get_results('SELECT DISTINCT meta_key FROM ' . $wpdb->sitemeta);
						
						$known_sitemeta_options = $options_to_copy;
						$known_sitemeta_options = apply_filters( 'manage_sitemeta_descriptions' , $known_sitemeta_options );
						
						$options_to_copy = apply_filters( 'manage_site_clone_options' , $options_to_copy);
					?>
				<td colspan="2">
					<table class="widefat">
						<thead>
							<tr>
								<th scope="col" class="check-column"></th>
								<th scope="col"><?php _e('Meta Value'); ?></th>
								<th scope="col"><?php _e('Description'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($all_site_options as $count => $option) { ?>
							<tr class="<?php echo $class = ('alternate' == $class) ? '' : 'alternate'; ?>">
								<th scope="row" class="check-column"><input type="checkbox" id="option_<?php echo $count; ?>" name="options_to_clone[<?php echo $option->meta_key; ?>]"<?php echo (array_key_exists($option->meta_key,$options_to_copy) ? ' checked' : '' ); ?> /></th>
								<td><label for="option_<?php echo $count; ?>"><?php echo $option->meta_key; ?></label></td>
								<td><label for="option_<?php echo $count; ?>"><?php echo (array_key_exists($option->meta_key,$known_sitemeta_options) ? __($known_sitemeta_options[$option->meta_key]) : '' ); ?></label></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</td>
			</table>
		</div>
	</div>
	<p><?php _e('A blog will be created at the root of the new site'); ?>.</p>
	</div>
	<input type="submit" class="button" name="add" value="<?php _e('Create Site'); ?>" />
</form>
</div>
<script type="text/javascript">
jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
jQuery('.postbox').children('h3').click(function() {
	if (jQuery(this.parentNode).hasClass('closed')) {
		jQuery(this.parentNode).removeClass('closed');
	} else {
		jQuery(this.parentNode).addClass('closed');
	}
});
</script>
<?php

break;
} // end switch( $action )
?> 
</div>
<?php

	}
	
	function move_blog_page() {

		global $wpmuBaseTablePrefix, $wpdb;

		if(isset($_POST['move']) && isset($_GET['blog_id'])) {

			if(isset($_POST['from']) && isset($_POST['to'])) {
				move_blog($_GET['blog_id'],$_POST['to']);
				$_GET['updated'] = 'yes';
				$_GET['action'] = 'saved';
			}
			
		} else {
		
			if(!isset($_GET['blog_id'])) {
				die(__('You must select a blog to move.'));
			}
			$query = "SELECT * FROM {$wpdb->blogs} WHERE blog_id=" . (int)$_GET['blog_id'];
			$blog = $wpdb->get_row($query);
			if(!$blog) {
				die(__('Invalid blog id.'));
			}
			$tableName = $wpmuBaseTablePrefix . (int)$blog->blog_id . "_options";
			$details = $wpdb->get_row("SELECT * FROM {$tableName} WHERE option_name='blogname'");
			if(!$details) {
				die(__('Invalid blog id.'));
			}

			$sites = $wpdb->get_results("SELECT * FROM {$wpdb->site}");
			foreach($sites as $key => $site) {
				if($site->id == $blog->site_id) {
					$mySite = $sites[$key];
				}
			}
			?>
			<h2><?php echo __('Moving') . ' ' . stripslashes($details->option_value); ?></h2>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<table class="widefat">
					<thead>
						<tr>
							<th scope="col"><?php _e('From'); ?>:</th>
							<th scope="col"><label for="to"><?php _e('To'); ?>:</label></th>
						</tr>
					</thead>
					<tr>
						<td><?php echo $mySite->domain; ?></td>
						<td>
							<select name="to" id="to">
								<option value="0"><?php _e('Select a Site'); ?></option>
								<?php
								foreach($sites as $site) {
									if($site->id != $mySite->id) {
										echo '<option value="' . $site->id . '">' . $site->domain . '</option>' . "\n";
									}
								}
								?>
							</select>
						</td>
					</tr>
				</table>
				<br />
				<?php if(has_action('add_move_blog_option')) { ?>
				<table class="widefat">
					<thead>
						<tr scope="col"><th colspan="2"><?php _e('Options'); ?>:</th></tr>
					</thead>
					<?php do_action('add_move_blog_option',$blog->blog_id); ?>
				</table>
				<br />
				<?php } ?>
				<div>
					<input type="hidden" name="from" value="<?php echo $blog->site_id; ?>" />
					<input class="button" type="submit" name="move" value="<?php _e('Move Blog'); ?>" />
					<a class="button" href="./wpmu-blogs.php"><?php _e('Cancel'); ?></a>
				</div>
			</form>
			<?php
		}
	}

	function reassign_blog_page() {
		
		global $wpdb, $wpmuBaseTablePrefix;
		
		if(isset($_POST['reassign']) && isset($_GET['id'])) {
			if(isset($_POST['jsEnabled'])) {
				/** Javascript enabled for client - check the 'to' box */
				if(!isset($_POST['to'])) {
					die(__('No blogs selected.'));
				}
				$blogs = $_POST['to'];
			} else {
				/** Javascript disabled for client - check the 'from' box */
				if(!isset($_POST['from'])) {
					die(__('No blogs seleceted.'));
				}
				$blogs = $_POST['from'];
			}

			$currentBlogs = $wpdb->get_results("SELECT * FROM {$wpdb->blogs} WHERE site_id=" . (int)$_GET['id']);

			foreach($blogs as $blog) {
				move_blog($blog,(int)$_GET['id']);
			}

			/* true sync - move any unlisted blogs to 'zero' site */
			if(ENABLE_SITE_ZERO) {
				foreach($currentBlogs as $currentBlog) {
					if(!in_array($currentBlog->blog_id,$blogs)) {
						move_blog($currentBlog->blog_id,0);
					}
				}
			}

			$_GET['updated'] = 'yes';
			$_GET['action'] = 'saved';

		} else {
			
			// get site by id
			$query = "SELECT * FROM {$wpdb->site} WHERE id=" . (int)$_GET['id'];
			$site = $wpdb->get_row($query);
			if(!$site) {
				die(__('Invalid site id.'));
			}
			$blogs = $wpdb->get_results("SELECT * FROM {$wpdb->blogs}");
			if(!$blogs) {
				die(__('Blog table inaccessible.'));
			}
			foreach($blogs as $key => $blog) {
				$tableName = $wpmuBaseTablePrefix . (int)$blog->blog_id . "_options";
				$blog_name = $wpdb->get_row("SELECT * FROM $tableName WHERE option_name='blogname'");
				if(!$blog_name) {
					die(__('Invalid blog.'));
				}
				$blogs[$key]->name = stripslashes($blog_name->option_value);
			}
			?>
			<h2><?php _e('Assign Blogs to'); ?>: <?php echo $site->domain . $site->path ?></h2>
			<noscript>
				<div id="message" class="updated"><p><?php _e('Select the blogs you want to assign to this site from the column at left, and click "Update Assignments."'); ?></p></div>
			</noscript>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<table class="widefat">
				<thead>
					<tr>
						<th><?php _e('Available'); ?></th>
						<th style="width: 2em;"></th>
						<th><?php _e('Assigned'); ?></th>
					</tr>
				</thead>
				<tr>
					<td>
						<select name="from[]" id="from" multiple style="height: auto; width: 98%">
						<?php
							foreach($blogs as $blog) {
								if($blog->site_id != $site->id) echo '<option value="' . $blog->blog_id . '">' . $blog->name  . ' (' . $blog->domain . ')</option>';
							}
						?>
						</select>
					</td>
					<td>
						<input type="button" name="unassign" id="unassign" value="<<" /><br />
						<input type="button" name="assign" id="assign" value=">>" />
					</td>
					<td valign="top">
						<?php if(!ENABLE_SITE_ZERO) { ?><ul style="margin: 0; padding: 0; list-style-type: none;">
							<?php foreach($blogs as $blog) { 
								if ($blog->site_id == $site->id) { ?>
								<li><?php echo $blog->name . ' (' . $blog->domain . ')'; ?></li>
							<?php } } ?>
						</ul><?php } ?>
						<select name="to[]" id="to" multiple style="height: auto; width: 98%">
						<?php
						if(ENABLE_SITE_ZERO) {
							foreach($blogs as $blog) {
								if($blog->site_id == $site->id) echo '<option value="' . $blog->blog_id . '">' . $blog->name . ' (' . $blog->domain . ')</option>';
							}
						}
						?>
						</select>
					</td>
				</tr>
			</table>
			<br class="clear" />
				<?php if(has_action('add_move_blog_option')) { ?>
				<table class="widefat">
					<thead>
						<tr scope="col"><th colspan="2"><?php _e('Options'); ?>:</th></tr>
					</thead>
					<?php do_action('add_move_blog_option',$blog->blog_id); ?>
				</table>
				<br />
				<?php } ?>
			<input type="submit" name="reassign" value="<?php _e('Update Assigments'); ?>" class="button" />
			<a href="./wpmu-admin.php?page=sites"><?php _e('Cancel'); ?></a>
			</form>
			<script type="text/javascript">
				if(document.getElementById) {

					var unassignButton = document.getElementById('unassign');
					var assignButton = document.getElementById('assign');
					var fromBox = document.getElementById('from');
					var toBox = document.getElementById('to');

					/* add field to signal javascript is enabled */
					var myJSVerifier = document.createElement('input');
					myJSVerifier.type = "hidden";
					myJSVerifier.name = "jsEnabled";
					myJSVerifier.value = "true";

					assignButton.parentNode.appendChild(myJSVerifier);

					assignButton.onclick   = function() { move(fromBox, toBox); };
					unassignButton.onclick = function() { move(toBox, fromBox); };
					assignButton.form.onsubmit = function() { selectAll(toBox); };
				}
	
				// PickList II script (aka Menu Swapper)- By Phil Webb (http://www.philwebb.com)
				// Visit JavaScript Kit (http://www.javascriptkit.com) for this JavaScript and 100s more
				// Please keep this notice intact
	
			function move(fbox, tbox) {
			     var arrFbox = new Array();
			     var arrTbox = new Array();
			     var arrLookup = new Array();
			     var i;
			     for(i=0; i<tbox.options.length; i++) {
			          arrLookup[tbox.options[i].text] = tbox.options[i].value;
			          arrTbox[i] = tbox.options[i].text;
			     }
			     var fLength = 0;
			     var tLength = arrTbox.length
			     for(i=0; i<fbox.options.length; i++) {
			          arrLookup[fbox.options[i].text] = fbox.options[i].value;
			          if(fbox.options[i].selected && fbox.options[i].value != "") {
			               arrTbox[tLength] = fbox.options[i].text;
			               tLength++;
			          } else {
			               arrFbox[fLength] = fbox.options[i].text;
			               fLength++;
			          }
			     }
			     arrFbox.sort();
			     arrTbox.sort();
			     fbox.length = 0;
			     tbox.length = 0;
			     var c;
			     for(c=0; c<arrFbox.length; c++) {
			          var no = new Option();
			          no.value = arrLookup[arrFbox[c]];
			          no.text = arrFbox[c];
			          fbox[c] = no;
			     }
			     for(c=0; c<arrTbox.length; c++) {
			     	var no = new Option();
			     	no.value = arrLookup[arrTbox[c]];
			     	no.text = arrTbox[c];
			     	tbox[c] = no;
			     }
			}

			function selectAll(box) {    for(var i=0; i<box.length; i++) {  box[i].selected = true;  } }

			</script>
			<?php
			
		}
	}

	function add_site_page() {
		
		global $wpdb, $options_to_copy;
		
		if(isset($_POST['add']) && isset($_POST['domain']) && isset($_POST['path'])) {

			/** grab custom options to clone if set */
			if(isset($_POST['options_to_clone']) && is_array($_POST['options_to_clone'])) {
				$options_to_clone = array_keys($_POST['options_to_clone']);
			} else {
				$options_to_clone = $options_to_copy;
			}

			$result = add_site(
				$_POST['domain'],
				$_POST['path'], 
				(isset($_POST['newBlog']) ? $_POST['newBlog'] : __('New Site Created') ) ,
				(isset($_POST['cloneSite']) ? $_POST['cloneSite'] : NULL ), 
				$options_to_clone 
			);
			if($result) {
				if(isset($_POST['name'])) {
					switch_to_site($result);
					add_site_option('site_name',$_POST['name']);
					restore_current_site();
				}

				$_GET['updated'] = 'yes';
				$_GET['action'] = 'saved';
			}
			
		} else {
			
			// integrated with main page
			
		}
	}

	function update_site_page() {
		
		global $wpdb;
		
		if(isset($_POST['update']) && isset($_GET['id'])) {
			
			$query = "SELECT * FROM {$wpdb->site} WHERE id=" . (int)$_GET['id'];
			$site = $wpdb->get_row($query);
			if(!$site) {
				die(__('Invalid site id.'));
			}
			update_site((int)$_GET['id'],$_POST['domain'],$_POST['path']);
			$_GET['updated'] = 'true';
			$_GET['action'] = 'saved';

		} else {
			
			// get site by id
			$query = "SELECT * FROM {$wpdb->site} WHERE id=" . (int)$_GET['id'];
			$site = $wpdb->get_row($query);
			if(!$site) {
				wp_die(__('Invalid site id.'));
			}
			
			/* strip off the action tag */
			$queryStr = substr($_SERVER['REQUEST_URI'],0,(strpos($_SERVER['REQUEST_URI'],'?')+1));
			$getParams = array();
			foreach($_GET as $getParam => $getValue) {
				if($getParam != 'action') {
					$getParams[] = $getParam . '=' . $getValue;
				}
			}
			$queryStr .= implode('&',$getParams);
			
			?>
			<h2><?php _e('Edit Site'); ?>: <a href="http://<?php echo $site->domain . $site->path ?>"><?php echo $site->domain . $site->path ?></a></h2>
			<form method="post" action="<?php echo $queryStr; ?>">
				<table class="form-table">
					<tr class="form-field"><th scope="row"><label for="domain"><?php _e('Domain'); ?></label></th><td> http://<input type="text" id="domain" name="domain" value="<?php echo $site->domain; ?>"></td></tr>
					<tr class="form-field"><th scope="row"><label for="path"><?php _e('Path'); ?></label></th><td><input type="text" id="path" name="path" value="<?php echo $site->path; ?>" /></td></tr>
				</table>
				<?php if(has_action('add_edit_site_option')) { ?>
				<h3>Options:</h3>
				<table class="form-table">
					<?php do_action('add_edit_site_option'); ?>
				</table>
				<?php } ?>
				<p>
					<input type="hidden" name="siteId" value="<?php echo $site->id; ?>" />
					<input class="button" type="submit" name="update" value="<?php _e('Update Site'); ?>" />
					<a href="./wpmu-admin.php?page=sites"><?php _e('Cancel'); ?></a>
				</p>
			</form>
			<?php			
			
		}	
	}

	function delete_site_page() {
		
		global $wpdb;
		
		if(isset($_POST['delete']) && isset($_GET['id'])) {
			
			$result = delete_site((int)$_GET['id'],(isset($_POST['override'])));
			if(is_a($result,'WP_Error')) {
				wp_die($result->get_error_message());
			}
			$_GET['deleted'] = 'yes';
			$_GET['action'] = 'saved';
			
		} else {
			
			// get site by id
			$query = "SELECT * FROM {$wpdb->site} WHERE id=" . (int)$_GET['id'];
			$site = $wpdb->get_row($query);
			if(!$site) {
				die(__('Invalid site id.'));
			}

			$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id=" . (int)$_GET['id'];
			$blogs = $wpdb->get_results($query);

			/* strip off the action tag */
			$queryStr = substr($_SERVER['REQUEST_URI'],0,(strpos($_SERVER['REQUEST_URI'],'?')+1));
			$getParams = array();
			foreach($_GET as $getParam => $getValue) {
				if($getParam != 'action') {
					$getParams[] = $getParam . '=' . $getValue;
				}
			}
			$queryStr .= implode('&',$getParams);

			?>
			<form method="POST" action="<?php echo $queryStr; ?>">
				<div>
					<h2><?php _e('Delete Site'); ?>: <a href="http://<?php echo $site->domain . $site->path ?>"><?php echo $site->domain . $site->path ?></a></h2>
<?php if($blogs) {
	if(RESCUE_ORPHANED_BLOGS && ENABLE_SITE_ZERO) {
 ?>
					<div id="message" class="error">
						<p><?php _e('There are blogs associated with this site. ');  _e('Deleting it will move them to the holding site.'); ?></p>
						<p><label for="override"><?php _e('If you still want to delete this site, check the following box'); ?>:</label> <input type="checkbox" name="override" id="override" /></p>
					</div>
<?php } else { ?>
					<div id="message" class="error">
						<p><?php _e('There are blogs associated with this site. '); _e('Deleting it will delete those blogs as well.'); ?></p>
						<p><label for="override"><?php _e('If you still want to delete this site, check the following box'); ?>:</label> <input type="checkbox" name="override" id="override" /></p>
					</div>
<?php	} ?>
<?php } ?>
					<p><?php _e('Are you sure you want to delete this site?'); ?></p>
					<input type="submit" name="delete" value="<?php _e('Delete Site'); ?>" class="button" /> <a href="./wpmu-admin.php?page=sites"><?php _e('Cancel'); ?></a>
				</div>
			</form>
			<?php
			
		}
	}
	
	function delete_multiple_site_page() {
		
		global $wpdb;
				
		if(isset($_POST['delete_multiple']) && isset($_POST['deleted_sites'])) {
			foreach($_POST['deleted_sites'] as $deleted_site) {
				$result = delete_site((int)$deleted_site,(isset($_POST['override'])));
				if(is_a($result,'WP_Error')) {
					wp_die($result->get_error_message());
				}
			}
			$_GET['deleted'] = 'yes';
			$_GET['action'] = 'saved';
		} else {
			
			/** ensure a list of sites was sent */
			if(!isset($_POST['allsites'])) {
				wp_die(__('You have not selected any sites to delete.'));
			}
			$allsites = array_map(create_function('$val','return (int)$val;'),$_POST['allsites']);
			
			/** ensure each site is valid */
			foreach($allsites as $site) {
				if(!site_exists((int)$site)) {
					wp_die(__('You have selected an invalid site for deletion.'));
				}
			}
			/** remove primary site from list */
			if(in_array(1,$allsites)) {
				$sites = array();
				foreach($allsites as $site) {
					if($site != 1) $sites[] = $site;
				}
				$allsites = $sites;
			}
			
			$query = "SELECT * FROM {$wpdb->site} WHERE id IN (" . implode(',',$allsites) . ')';
			$site = $wpdb->get_results($query);
			if(!$site) {
				wp_die(__('You have selected an invalid site or sites for deletion'));
			}
			
			$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id IN (" . implode(',',$allsites) . ')';
			$blogs = $wpdb->get_results($query);
			
			?>
			<form method="POST" action="./wpmu-admin.php?page=sites"><div>
			<h2><?php _e('Delete Multiple Sites'); ?></h2>
			<?php
			
			if($blogs) {
				if(RESCUE_ORPHANED_BLOGS && ENABLE_SITE_ZERO) {
					?>
					
			<div id="message" class="error">
				<h3><?php _e('You have selected the following sites for deletion'); ?>:</h3>
				<ul>
				<?php foreach($site as $deleted_site) { ?>
					<li><input type="hidden" name="deleted_sites[]" value="<?php echo $deleted_site->id; ?>" /><?php echo $deleted_site->domain . $deleted_site->path ?></li>
				<?php } ?>
				</ul>
				<p><?php _e('There are blogs associated with one or more of these sites.  Deleting them will move these blgos to the holding site.'); ?></p>
				<p><label for="override"><?php _e('If you still want to delete these sites, check the following box'); ?>:</label> <input type="checkbox" name="override" id="override" /></p>
			</div>
					
					<?php
				} else {
					?>
					
			<div id="message" class="error">
				<h3><?php _e('You have selected the following sites for deletion'); ?>:</h3>
				<ul>
				<?php foreach($site as $deleted_site) { ?>
					<li><input type="hidden" name="deleted_sites[]" value="<?php echo $deleted_site->id; ?>" /><?php echo $deleted_site->domain . $deleted_site->path ?></li>
				<?php } ?>
				</ul>
				<p><?php _e('There are blogs associated with one or more of these sites.  Deleting them will delete those blogs as well.'); ?></p>
				<p><label for="override"><?php _e('If you still want to delete these sites, check the following box'); ?>:</label> <input type="checkbox" name="override" id="override" /></p>
			</div>
					
					<?php
				}
			} else {

				?>
					
			<div id="message">
				<h3><?php _e('You have selected the following sites for deletion'); ?>:</h3>
				<ul>
				<?php foreach($site as $deleted_site) { ?>
					<li><input type="hidden" name="deleted_sites[]" value="<?php echo $deleted_site->id; ?>" /><?php echo $deleted_site->domain . $deleted_site->path ?></li>
				<?php } ?>
				</ul>
			</div>
					
				<?php
				
			}
			?>
				<p><?php _e('Are you sure you want to delete these sites?'); ?></p>
				<input type="submit" name="delete_multiple" value="<?php _e('Delete Sites'); ?>" class="button" /> <input type="submit" name="cancel" value="<?php _e('Cancel'); ?>" class="button" />
			</div></form>
			<?php
		}
		
	}
}

$njslSites = new dd_sites();

?>