<?php

/******************************************************************************************************************
 
Plugin Name: Theme Info

Plugin URI:

Description: WordPress plugin for letting site admins easily see what themes are actively used on their site

Version: 1.1

Author: Kevin Graeme & Deanna Schneider


Copyright:

    Copyright 2009 Board of Regents of the University of Wisconsin System
	Cooperative Extension Technology Services
	University of Wisconsin-Extension

            
*******************************************************************************************************************/

class cets_Plugin_Stats {


function cets_plugin_stats() {
	add_action('admin_menu', array(&$this, 'plugin_stats_add_page'));
}
			


function generate_plugin_blog_list() {
	global $wpdb, $current_site;
		
		$blogs  = $wpdb->get_results("SELECT blog_id, domain, path FROM " . $wpdb->blogs . " WHERE site_id = {$current_site->id} ORDER BY domain ASC");
		$blogplugins = array();
		$processedplugins = array();
		
		$plugins = get_plugins();
		if ($blogs) {
		foreach ($blogs as $blog) {
			
			switch_to_blog($blog->blog_id);
			if( constant( 'VHOST' ) == 'yes' ) {
				$blogurl = $blog->domain;			
			} else {
				$blogurl =  trailingslashit( $blog->domain . $blog->path );
			}
			
			$blog_info = array('name' => get_bloginfo('name'), 'url' => $blogurl);
			
			$active_plugins = get_option('active_plugins');
			if (sizeOf($active_plugins) > 0) {
				foreach($active_plugins as $plugin){
					$this_plugin = $plugins[$plugin];
					if (is_array($this_plugin['blogs'])){
						array_push($this_plugin['blogs'], $blog_info);
					}
					else {
						$this_plugin['blogs'] = array();
						array_push($this_plugin['blogs'], $blog_info);
					}
					unset($plugins[$plugin]);
					$plugins[$plugin] =  $this_plugin;
					
					}
				}		
			
			
			restore_current_blog();
			
			}
			
			
		}
	// Set the site option to hold all this
	add_site_option('cets_plugin_stats_data', $plugins);
	
	
	add_site_option('cets_plugin_stats_data_freshness', time());
	
	
}



	

// Create a function to add a menu item for site admins
function plugin_stats_add_page() {
	// Add a submenu
	if(is_site_admin()) {
	$page=	add_submenu_page('wpmu-admin.php', 'Plugin Stats', 'Plugin Stats', 0, basename(__FILE__), array(&$this, 'plugin_stats_page'));
	wp_enqueue_script('jquery');
	
	}

}




// Create a function to actually display stuff on theme usage
function plugin_stats_page(){
	
	// Get the time when the theme list was last generated
	$gen_time = get_site_option('cets_plugin_stats_data_freshness');
	
	
	if ((time() - $gen_time) > 3600 || $_POST['action'] == 'update')  {
		// if older than an hour, regenerate, just to be safe
			$this->generate_plugin_blog_list();	
	}
	$list = get_site_option('cets_plugin_stats_data');
	ksort($list);
	
	// if you're using plugin commander, these two values will be populated
	$auto_activate = explode(',',get_site_option('pc_auto_activate_list'));
	$user_control = explode(',',get_site_option('pc_user_control_list'));
	
	// if you're using plugin manager, these values will be populated
	$pm_auto_activate = explode(',',get_site_option('mp_pm_auto_activate_list'));
	$pm_user_control = explode(',',get_site_option('mp_pm_user_control_list'));
	$pm_supporter_control = explode(',',get_site_option('mp_pm_supporter_control_list'));
	$pm_auto_activate_status = ($pm_auto_activate[0] == '' || $pm_auto_active[0] == 'EMPTY' ? 0 : 1);
	$pm_user_control_status = ($pm_user_control[0] == '' || $pm_user_control == 'EMPTY' ? 0 : 1);
	$pm_supporter_control_status = ($pm_supporter_control[0] == '' || $pm_supporter_control == 'EMPTY' ? 0 : 1);
	
	
	// this is the built-in sitewide activation
	$active_sitewide_plugins = maybe_unserialize( get_site_option( 'active_sitewide_plugins') );
	
	$file = WP_CONTENT_URL . '/mu-plugins/cets_plugin_stats/lib/tablesort.js';
	?>
	<!-- This pulls in the table sorting script -->
	<SCRIPT LANGUAGE='JavaScript1.2' SRC='<?php echo $file; ?>'></SCRIPT>
	<!-- Some extra CSS -->
	<style type="text/css">
		.bloglist {
			display:none;
		}
		.pc_settings_heading {
			text-align: center; 
			border-right:  3px solid black;
			border-left: 3px solid black;
			
		}
		.pc_settings_left {
			border-left: 3px solid black;
		}
		.pc_settings_right {
			border-right: 3px solid black;
		}
	</style>
	
	<div class="wrap">
		<h2>Plugin Stats</h2>
		<table class="widefat" id="cets_active_plugins">
			
			<thead>
				<?php if (sizeOf($auto_activate) > 1 || sizeOf($user_control) > 1 || $pm_auto_activate_status == 1 || $pm_user_control_status == 1|| pm_supporter_control_status == 1 ) {
				?>
				<tr>
					<th style="width: 25%;" >&nbsp;</th>
					<?php if (sizeOf($auto_activate) > 1 || sizeOf($user_control) > 1){
					?>
					<th colspan="2" class="pc_settings_heading">Plugin Commander Settings</th>
					
					<?php	
					}
					if ($pm_auto_activate_status == 1 || $pm_user_control_status == 1|| pm_supporter_control_status == 1){
					?>
					<th colspan="3" align="center" class="pc_settings_heading">Plugin Manager Settings</th>
					<?php	
					}
					?>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th  style="width: 20%;">&nbsp;</th>
				</tr>
				<?php
				}
				?>
				<tr>
					<th class="nocase">Plugin</th>
					
					<?php if (sizeOf($auto_activate) > 1 || sizeOf($user_control) > 1){
					?>
					<th class="nocase pc_settings_left">Auto Activate</th>
					<th class="nocase pc_settings_right">User Controlled</th>
					<?php	
					}
					if ($pm_auto_activate_status == 1 || $pm_user_control_status == 1|| pm_supporter_control_status == 1){
					?>
					<th class="nocase pc_settings_left">Auto Activate</th>
					<th class="nocase">User Controlled</th>
					<th class="nocase pc_settings_right">Supporter Controlled</th>
					<?php	
					}
					?>
					<th class="nocase">Activated Sitewide</th>
					<th class="num">Total Blogs</th>
					<th width="200px">Blog Titles</th>

				</tr>
			</thead>
			<tbody id="plugins">
	<?php
	$counter = 0;
	foreach ($list as $file => $info){
		$counter = $counter + 1;
		
		echo('<tr valign="top"><td>');
		if (strlen($info['Name'])){
			$thisName = $info['Name'];
		}
		else $thisName = $file;
		echo ($thisName . '</td>');
		// plugin commander columns	
		if (sizeOf($auto_activate) > 1 || sizeOf($user_control) > 1) {
			echo ('<td align="center" class="pc_settings_left">');
			if (in_array($file, $auto_activate)) { echo ("Yes");}
		else {echo ("No");}
			echo('</td><td align="center" class="pc_settings_right">');
			if (in_array($file, $user_control)) { echo ("Yes");}
		else {echo ("No");}
			echo("</td>");
			
		}
		// plugin manager columns
		if ($pm_auto_activate_status == 1 || $pm_user_control_status == 1 || $pm_supporter_control_status == 1) {
			echo ('<td align="center" class="pc_settings_left">');
			if (in_array($file, $pm_auto_activate)) { echo ("Yes");}
		else {echo ("No");}
			echo('</td><td align="center">');
			if (in_array($file, $pm_user_control)) { echo ("Yes");}
		else {echo ("No");}
			echo('</td><td align="center" class="pc_settings_right">');
		if (in_array($file, $pm_supporter_control)) { echo ("Yes");}
		else {echo ("No");}
		echo("</td>");
			
		}
		
		echo ('<td align="center">');
		if (is_array($active_sitewide_plugins) && array_key_exists($file, $active_sitewide_plugins)) { echo ("Yes");}
		else {echo ("No");}
		
		echo ('</td><td align="center">' . sizeOf($info['blogs']) . '</td><td>');
		?>
		<a href="javascript:void(0)" onClick="jQuery('#bloglist_<?php echo $counter; ?>').toggle(400);">Show/Hide Blogs</a>
		
		
		<?php
		echo ('<ul class="bloglist" id="bloglist_' . $counter  . '">');
		if (is_array($info['blogs'])){
			foreach($info['blogs'] as $blog){
				echo ('<li><a href="http://' . $blog['url'] . '" target="new">' . $blog['name'] . '</a></li>');
				}
		
			}
		else echo ("<li>N/A</li>");	
		echo ('</ul></td>');
		
		
	}
	?>
		</tbody>
		</table>
		<p>
			This data is not updated as blog users update their plugins.  It was last generated  <?php if (time()-$gen_time > 60) {
			echo (round((time() - $gen_time)/60, 0) . " minutes");
			}
			else echo('less than 1 minute ') ?>  ago. <form name="themeinfoform" action="" method="post"><input type="submit" value="Regenerate"><input type="hidden" name="action" value="update" /></form>
			
		</p>
	

	<?php
	

	
	
		
}




}// end class


add_action( 'plugins_loaded', create_function( '', 'global $cets_Plugin_Stats; $cets_Plugin_Stats = new cets_Plugin_Stats();' ) );



?>