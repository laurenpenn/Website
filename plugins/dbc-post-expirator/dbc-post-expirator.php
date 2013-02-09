<?php
/**
 * Plugin Name: DBC Post Expirator
 * Description: Allows you to add an expiration date to posts which you can configure to either delete the post or change it to a draft.
 * Version: 1.0
 * Author: Patrick Daly
 * Author URI: http://developdaly.com
 * Translation: Thierry (http://palijn.info)
 * Text Domain: post-expirator
 *
 * This plugin was forked from the original because it broke due to changes to WordPress core.
 * The new version attempts to cleanup code and remove settings unnecessary to DBC's use.
 * Particularly, this plugin will only expire 'post' post types and provide UI only for posts.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 * 
 * Post Expirator was originally developed buy Aaron Axelsen (http://postexpirator.tuxdocs.net/) and
 * hosted at http://wordpress.org/extend/plugins/post-expirator/
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package DBC Post Expirator
 * @version 1.0
 * @author Patrick Daly <patrick@developdaly.com>
 * @copyright Copyright (c) 2011, Patrick Daly
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Load translation, if it exists */
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'post-expirator', null, $plugin_dir.'/languages/' );

// Default Values
$expirationdateDefaultDateFormat = __( 'l F jS, Y', 'post-expirator' );
$expirationdateDefaultTimeFormat = __( 'g:ia', 'post-expirator' );
$expirationdateDefaultFooterContents = __( 'Post expires at EXPIRATIONTIME on EXPIRATIONDATE','post-expirator' );
$expirationdateDefaultFooterStyle = 'font-style: italic;';

register_activation_hook( __FILE__, 'expirationdate_activate' );
register_deactivation_hook( __FILE__, 'expirationdate_deactivate' );

add_shortcode( 'postexpirator', 'postexpirator_shortcode');

add_action( 'admin_notices','postExpirationAdminNotice' );
add_action( 'manage_posts_custom_column', 'expirationdate_show_value' );
add_action( 'manage_pages_custom_column', 'expirationdate_show_value' );
add_action( 'load-post.php', 'postExpirator_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'postExpirator_post_meta_boxes_setup' );
add_action( 'wp_ajax_expirationdate_ajax','expirationdate_ajax_process' );
add_action( 'admin_print_scripts', 'expirationdate_js_admin_header' );
add_action( 'save_post','expirationdate_update_post_meta' );
add_action( 'admin_menu', 'postExpiratorPluginMenu' );
add_action( 'the_content','postexpirator_add_footer', 0 );

add_filter( 'cron_schedules','postExpiratorAddCronMinutes' );
add_filter( 'manage_posts_columns', 'expirationdate_add_column' );

// Detect WPMU/MultiSite
function postExpirator_is_wpmu() {
	if ( function_exists( 'is_multisite' ) )
		return is_multisite();
	else
		return file_exists( ABSPATH.'/wpmu-settings.php' );
}

// Timezone Setup
function postExpiratorTimezoneSetup() {
        if ( !$timezone_string = get_option( 'timezone_string' ) ) {
                return false;
        }

	@date_default_timezone_set($timezone_string);
}

// Add cron interval of 60 seconds
function postExpiratorAddCronMinutes($array) {
       $array['postexpiratorminute'] = array(
               'interval' => 60,
               'display' => __('Once a Minute','post-expirator')
       );
	return $array;
}

/**
 * Add admin notice hook if cron schedule needs to be reset
 */
function postExpirationAdminNotice() {
	if (postExpiratorCronStatus() === false) {
		echo '<div class="error fade" style="background-color:red;"><p><strong>';
		_e('Post Expirator cron schedules need to be reset','post-expirator');
		echo('<a href="'.admin_url('options-general.php?page=post-expirator.php&tab=upgrade').'" style="color: blue;">');
		_e('Click here to reset','post-expirator');
		echo('</a></strong></p></div>');
	}
}

/** 
 * Function that does the actualy deleting - called by wp_cron
 */
function expirationdate_delete_expired_posts() {
	global $wpdb;
	postExpiratorTimezoneSetup();
	$time_delete = time();
	$result = $wpdb->get_results('select post_id, meta_value from ' . $wpdb->postmeta . ' as postmeta, '.$wpdb->posts.' as posts where postmeta.post_id = posts.ID AND posts.post_status = "publish" AND postmeta.meta_key = "expiration-date" AND postmeta.meta_value <= "' . $time_delete . '"');
  	if (!empty($result)) foreach ($result as $a) {
		$post_result = $wpdb->get_var('select post_type from ' . $wpdb->posts .' where ID = '. $a->post_id);
		if ($post_result == 'post') {
			$expiredStatus = strtolower(get_option('expirationdateExpiredPostStatus'));
		} else if ($post_result == 'page') {
			$expiredStatus = strtolower(get_option('expirationdateExpiredPageStatus'));
		} else {
			$expiredStatus = 'draft';
		}

		if ($expiredStatus == 'delete')
			wp_delete_post($a->post_id);
		else {
			wp_update_post(array('ID' => $a->post_id, 'post_status' => 'draft'));
	                delete_post_meta($a->post_id, 'expiration-date');
        	        update_post_meta($a->post_id, 'expiration-date', $a->meta_value, true);
		}
	}
}
if (postExpirator_is_wpmu())
	add_action ('expirationdate_delete_'.$current_blog->blog_id, 'expirationdate_delete_expired_posts');
else
	add_action ('expirationdate_delete', 'expirationdate_delete_expired_posts');

/** 
 * Called at plugin activation
 */
function expirationdate_activate () {
	global $current_blog,$expirationdateDefaultDateFormat,$expirationdateDefaultTimeFormat,$expirationdateDefaultFooterContents,$expirationdateDefaultFooterStyle;
	update_option('expirationdateExpiredPostStatus','Draft');
	update_option('expirationdateExpiredPageStatus','Draft');
	update_option('expirationdateDefaultDateFormat',$expirationdateDefaultDateFormat);
	update_option('expirationdateDefaultTimeFormat',$expirationdateDefaultTimeFormat);
	update_option('expirationdateFooterContents',$expirationdateDefaultFooterContents);
	update_option('expirationdateFooterStyle',$expirationdateDefaultFooterStyle);
        update_option('expirationdateDisplayFooter',0);
        postExpiratorTimezoneSetup();

	if (postExpirator_is_wpmu())
		wp_schedule_event(mktime(date('H'),0,0,date('m'),date('d'),date('Y')), 'postexpiratorminute', 'expirationdate_delete_'.$current_blog->blog_id);
	else
		wp_schedule_event(mktime(date('H'),0,0,date('m'),date('d'),date('Y')), 'postexpiratorminute', 'expirationdate_delete');
}

/**
 * Called at plugin deactivation
 */
function expirationdate_deactivate () {
	global $current_blog;
	delete_option('expirationdateExpiredPostStatus');
	delete_option('expirationdateExpiredPageStatus');
	delete_option('expirationdateDefaultDateFormat');
	delete_option('expirationdateDefaultTimeFormat');
        delete_option('expirationdateDisplayFooter');
        delete_option('expirationdateFooterContents');
        delete_option('expirationdateFooterStyle');
	if (postExpirator_is_wpmu())
		wp_clear_scheduled_hook('expirationdate_delete_'.$current_blog->blog_id);
	else
		wp_clear_scheduled_hook('expirationdate_delete');
}

/**
 * adds an 'Expires' column to the post display table.
 */
function expirationdate_add_column ($columns) {
  	$columns['expirationdate'] = __('Expires','post-expirator');
  	return $columns;
}

/**
 * fills the 'Expires' column of the post display table.
 */
function expirationdate_show_value ($column_name) {
	global $wpdb, $post;
	$id = $post->ID;
	if ($column_name === 'expirationdate') {
	        postExpiratorTimezoneSetup();
    		$query = "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = \"expiration-date\" AND post_id=$id";
    		$ed = $wpdb->get_var($query);
    		echo ($ed ? date('M j, Y @ H:i',$ed) : __("Never",'post-expirator'));
  	}
}

/* Meta box setup function. */
function postExpirator_post_meta_boxes_setup() {

	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'expirationdate_meta_post' );
	
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function expirationdate_meta_post() {
	add_meta_box(
		'expirationdatediv',
		 esc_html__('Post Expirator', 'post-expirator'),
		 'expirationdate_meta_box',
		 'post',
		 'advanced',
		 'high'
	);
}

/**
 * Actually adds the meta box
 */
function expirationdate_meta_box($post) { 
	// Get default month
	postExpiratorTimezoneSetup();
	$expirationdatets = get_post_meta($post->ID,'expiration-date',true);
	if (empty($expirationdatets)) {
		$defaultmonth = date('F');
		$defaultday = date('d');
		$defaulthour = date('H');
		$defaultyear = date('Y');
		$defaultminute = date('i');
		$disabled = 'disabled="disabled"';
	} else {
		$defaultmonth = date('F',$expirationdatets);
		$defaultday = date('d',$expirationdatets);
		$defaultyear = date('Y',$expirationdatets);
		$defaulthour = date('H',$expirationdatets);
		$defaultminute = date('i',$expirationdatets);

		$enabled = ' checked="checked"';
		$disabled = '';
	}

	$rv = array();
	$rv[] = '<p><input type="checkbox" name="enable-expirationdate" id="enable-expirationdate" value="checked"'.$enabled.' onclick="expirationdate_ajax_add_meta(\'enable-expirationdate\')" />';
	$rv[] = '<label for="enable-expirationdate">'.__('Enable Post Expiration','post-expirator').'</label></p>';
	$rv[] = '<table><tr>';
	   $rv[] = '<th style="text-align: left;">'.__('Year','post-expirator').'</th>';
	   $rv[] = '<th style="text-align: left;">'.__('Month','post-expirator').'</th>';
	   $rv[] = '<th style="text-align: left;">'.__('Day','post-expirator').'</th>';
	   $rv[] = '<th style="text-align: left;"></th>';
	   $rv[] = '<th style="text-align: left;">'.__('Hour','post-expirator').' ('.date('T',mktime(0, 0, 0, $i, 1, date("Y"))).')</th>';
	   $rv[] = '<th style="text-align: left;">'.__('Minute','post-expirator').'</th>';
	$rv[] = '</tr><tr>';
	$rv[] = '<td>';	
		$rv[] = '<select name="expirationdate_year" id="expirationdate_year"'.$disabled.'">';
		$currentyear = date('Y');
		if ($defaultyear < $currentyear)
			$currentyear = $defaultyear;
		for($i = $currentyear; $i < $currentyear + 8; $i++) {
			if ($i == $defaultyear)
				$selected = ' selected="selected"';
			else
				$selected = '';
			$rv[] = '<option'.$selected.'>'.($i).'</option>';
		}
		$rv[] = '</select>';
	$rv[] = '</td><td>';
		$rv[] = '<select name="expirationdate_month" id="expirationdate_month"'.$disabled.'">';
		for($i = 1; $i <= 12; $i++) {
			if ($defaultmonth == date('F',mktime(0, 0, 0, $i, 1, date("Y"))))
				$selected = ' selected="selected"';
			else
				$selected = '';
			$rv[] = '<option value="'.date('m',mktime(0, 0, 0, $i, 1, date("Y"))).'"'.$selected.'>'.date(__('F','post-expirator'),mktime(0, 0, 0, $i, 1, date("Y"))).'</option>';
		}
	$rv[] = '</select>';	 
	$rv[] = '</td><td>';
		$rv[] = '<input type="text" id="expirationdate_day" name="expirationdate_day" value="'.$defaultday.'" size="2"'.$disabled.'" />,';
	$rv[] = '<td>@</td><td>';
	 /* $rv[] = '<input type="text" id="expirationdate_hour" name="expirationdate_hour" value="'.$defaulthour.'" size="2"'.$disabled.'" />'; */
	 	$rv[] = '<select style="width:4em;" name="expirationdate_hour" id="expirationdate_hour" '. $disabled .'>';
		for($i = 1; $i <= 24; $i++) {
			if ($defaulthour == date('H',mktime($i, 0, 0, date("n"), date("j"), date("Y"))))
				$selected = ' selected="selected"';
			else
				$selected = '';
			$rv[] = '<option value="'.date('H',mktime($i, 0, 0, date("n"), date("j"), date("Y"))).'"'.$selected.'>'.date(__('H','post-expirator'),mktime($i, 0, 0, date("n"), date("j"), date("Y"))).'</option>';
		}
		$rv[] = '</td><td>';
		$rv[] = '<input type="text" id="expirationdate_minute" name="expirationdate_minute" value="'.$defaultminute.'" size="2"'.$disabled.'" />';
		$rv[] = '<input type="hidden" name="expirationdate_formcheck" value="true" />';
	$rv[] = '</td></tr></table>';

	$rv[] = '<div id="expirationdate_ajax_result"></div>';

	echo implode("\n",$rv);
}

/**
 * PHP Code to be executed by ajax function call - currently nothing happens
 */
function expirationdate_ajax_process() {
	// Gather Values
	$enable = $_POST['enable'];
	
	die(0);
}

/**
 * Add's ajax javascript
 */
function expirationdate_js_admin_header() {
	// use JavaScript SACK library for Ajax
	wp_print_scripts( array( 'sack' ));

	// Define custom JavaScript function
	?>
<script type="text/javascript">
//<![CDATA[
function expirationdate_ajax_add_meta(expireenable) {
	var mysack = new sack("<?php expirationdate_get_blog_url(); ?>wp-admin/admin-ajax.php");

	var expire = document.getElementById(expireenable);

	if (expire.checked == true) {
		var enable = 'true';
		document.getElementById('expirationdate_month').disabled = false;
		document.getElementById('expirationdate_day').disabled = false;
		document.getElementById('expirationdate_year').disabled = false;
		document.getElementById('expirationdate_hour').disabled = false;
		document.getElementById('expirationdate_minute').disabled = false;
	} else {
		document.getElementById('expirationdate_month').disabled = true;
		document.getElementById('expirationdate_day').disabled = true;
		document.getElementById('expirationdate_year').disabled = true;
		document.getElementById('expirationdate_hour').disabled = true;
		document.getElementById('expirationdate_minute').disabled = true;
		var enable = 'false';
	}
	
	mysack.execute = 1;
	mysack.method = 'POST';
	mysack.setVar( "action", "expirationdate_ajax" );
	mysack.setVar( "enable", enable );
	mysack.encVar( "cookie", document.cookie, false );
	mysack.onError = function() { alert('Ajax error in enabling post expiration' )};
	mysack.runAJAX();

	return true;
}
//]]>
</script>
<?php
}

/**
 * Get correct URL (HTTP or HTTPS)
 */
function expirationdate_get_blog_url() {
	global $current_blog;
	$schema = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
	
	if (postExpirator_is_wpmu())	
        	echo $schema.$current_blog->domain.$current_blog->path;
	else
        	echo get_bloginfo('siteurl').'/';
}

/**
 * Called when post is saved - stores expiration-date meta value
 */
function expirationdate_update_post_meta($id) {
	if (!isset($_POST['expirationdate_formcheck']))
		return false;

        $month = $_POST['expirationdate_month'];
        $day = $_POST['expirationdate_day'];
        $year = $_POST['expirationdate_year'];
        $hour = $_POST['expirationdate_hour'];
        $minute = $_POST['expirationdate_minute'];

	if (isset($_POST['enable-expirationdate'])) {
	        postExpiratorTimezoneSetup();
        	// Format Date
        	$ts = mktime($hour,$minute,0,$month,$day,$year);

        	// Update Post Meta
		delete_post_meta($id, 'expiration-date');
	        update_post_meta($id, 'expiration-date', $ts, true);
	} else {
		delete_post_meta($id, 'expiration-date');
	}
}

/**
 * Build the menu for the options page
 */
function postExpiratorMenuTabs($tab) {
        echo '<h2>'.__('Post Expirator Options','post-expirator').'</h2>';
        echo '<p>';
	if (empty($tab)) $tab = 'general';
        echo '<a href="'.admin_url('options-general.php?page=post-expirator.php&tab=general').'"'.($tab == 'general' ? ' style="font-weight: bold; text-decoration:none;"' : '').'>'.__('General Settings','post-expirator').'</a> | ';
        echo '<a href="'.admin_url('options-general.php?page=post-expirator.php&tab=upgrade').'"'.($tab == 'upgrade' ? ' style="font-weight: bold; text-decoration:none;"' : '').'>'.__('Upgrade','post-expirator').'</a>';
        echo '</p><hr/>';
}

/**
 *
 */
function postExpiratorMenu() {
        $tab = $_GET['tab'];

	echo '<div class="wrap">';
	postExpiratorMenuTabs($tab);
	if (empty($tab) || $tab == 'general') {
		postExpiratorMenuGeneral();
	} elseif ($tab == 'upgrade') {
		postExpiratorMenuUpgrade();
	}
	echo '</div>';
}

/**
 * Hook's to add plugin page menu
 */
function postExpiratorPluginMenu() {
	add_submenu_page('options-general.php',__('Post Expirator Options','post-expirator'),__('Post Expirator','post-expirator'),9,basename(__FILE__),'postExpiratorMenu');
}

/**
 * Show the Expiration Date options page
 */
function postExpiratorMenuGeneral() {

	if ($_POST['expirationdateSave']) {
		update_option('expirationdateExpiredPostStatus',$_POST['expired-post-status']);
		update_option('expirationdateExpiredPageStatus',$_POST['expired-page-status']);
		update_option('expirationdateDefaultDateFormat',$_POST['expired-default-date-format']);
		update_option('expirationdateDefaultTimeFormat',$_POST['expired-default-time-format']);
		update_option('expirationdateDisplayFooter',$_POST['expired-display-footer']);
		update_option('expirationdateFooterContents',$_POST['expired-footer-contents']);
		update_option('expirationdateFooterStyle',$_POST['expired-footer-style']);
                echo "<div id='message' class='updated fade'><p>";
                _e('Saved Options!','post-expirator');
                echo "</p></div>";
	}
	postExpiratorTimezoneSetup();
	// Get Option
	$expirationdateExpiredPostStatus = get_option('expirationdateExpiredPostStatus');
	if (empty($expirationdateExpiredPostStatus))
		$expirationdateExpiredPostStatus = 'Draft';

	$expirationdateExpiredPageStatus = get_option('expirationdateExpiredPageStatus');
	if (empty($expirationdateExpiredPageStatus))
		$expirationdateExpiredPageStatus = 'Draft';

	$expirationdateDefaultDateFormat = get_option('expirationdateDefaultDateFormat');
	if (empty($expirationdateDefaultDateFormat)) {
		global $expirationdateDefaultDateFormat;
		$expirationdateDefaultDateFormat = $expirationdateDefaultDateFormat;
	}

	$expirationdateDefaultTimeFormat = get_option('expirationdateDefaultTimeFormat');
	if (empty($expirationdateDefaultTimeFormat)) {
		global $expirationdateDefaultTimeFormat;
		$expirationdateDefaultTimeFormat = $expirationdateDefaultTimeFormat;
	}

	$expireddisplayfooter = get_option('expirationdateDisplayFooter');
	if (empty($expireddisplayfooter))
		$expireddisplayfooter = 0;

	$expireddisplayfooterenabled = '';
	$expireddisplayfooterdisabled = '';
	if ($expireddisplayfooter == 0)
		$expireddisplayfooterdisabled = 'checked="checked"';
	else if ($expireddisplayfooter == 1)
		$expireddisplayfooterenabled = 'checked="checked"';
	
	$expirationdateFooterContents = get_option('expirationdateFooterContents');
	if (empty($expirationdateFooterContents)) {
		global $expirationdateDefaultFooterContents;
		$expirationdateFooterContents = $expirationdateDefaultFooterContents;
	}

	$expirationdateFooterStyle = get_option('expirationdateFooterStyle');
	if (empty($expirationdateFooterStyle)) {
		global $expirationdateDefaultFooterStyle;
		$expirationdateFooterStyle = $expirationdateDefaultFooterStyle;
	}

	?>
	<p>
	<?php _e('The post expirator plugin sets a custom meta value, and then optionally allows you to select if you want the post changed to a draft status or deleted when it expires.','post-expirator'); ?>
	</p>
	<p>
	<?php _e('Valid [postexpiration] attributes:','post-expirator'); ?>
	<ul>
		<li><?php _e('type - defaults to full - valid options are full,date,time','post-expirator');?></li>
		<li><?php _e('dateformat - format set here will override the value set on the settings page','post-expirator');?></li>
		<li><?php _e('timeformat - format set here will override the value set on the settings page','post-expirator');?></li>
	</ul>
	</p>
	<form method="post" id="expirationdate_save_options">
		<h3><?php _e('Defaults','post-expirator'); ?></h3>
		<table class="form-table">
			<tr valign-"top">
				<th scope="row"><label for="expired-post-status"><?php _e('Set Post To:','post-expirator'); ?></label></th>
				<td>
					<select name="expired-post-status" id="expired-post-status">
					<option <?php if ($expirationdateExpiredPostStatus == 'Draft'){ echo 'selected="selected"';} ?> value="Draft"><?php _e('Draft','post-expirator');?></option>
					<option <?php if ($expirationdateExpiredPostStatus == 'Delete'){ echo 'selected="selected"';} ?> value="Delete"><?php _e('Delete','post-expirator');?></option>
					</select>	
					<br/>
					<?php _e('Select whether the post should be deleted or changed to a draft at expiration time.','post-expirator');?>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="expired-page-status"><?php _e('Set Page To:','post-expirator');?></label></th>
				<td>
					<select name="expired-page-status" id="expired-page-status">
					<option<?php if ($expirationdateExpiredPageStatus == 'Draft'){ echo ' selected="selected"';}?> value="Draft"><?php _e('Draft','post-expirator');?></option>
					<option<?php if ($expirationdateExpiredPageStatus == 'Delete'){ echo ' selected="selected"';}?> value="Delete"><?php _e('Delete','post-expirator');?></option>
					</select>	
					<br/>
					<?php _e('Select whether the page should be deleted or changed to a draft at expiration time.','post-expirator');?>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="expired-default-date-format"><?php _e('Date Format:','post-expirator');?></label></th>
				<td>
					<input type="text" name="expired-default-date-format" id="expired-default-date-format" value="<?php echo $expirationdateDefaultDateFormat ?>" size="25" /> (<?php echo date("$expirationdateDefaultDateFormat") ?>)
					<br/>
					<?php _e('The default format to use when displaying the expiration date within a post using the [postexpirator] shortcode or within the footer.  For information on valid formatting options, see: <a href="http://us2.php.net/manual/en/function.date.php" target="_blank">PHP Date Function</a>.','post-expirator'); ?>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="expired-default-time-format"><?php _e('Time Format:','post-expirator');?></label></th>
				<td>
					<input type="text" name="expired-default-time-format" id="expired-default-time-format" value="<?php echo $expirationdateDefaultTimeFormat ?>" size="25" /> (<?php echo date("$expirationdateDefaultTimeFormat") ?>)
					<br/>
					<?php _e('The default format to use when displaying the expiration time within a post using the [postexpirator] shortcode or within the footer.  For information on valid formatting options, see: <a href="http://us2.php.net/manual/en/function.date.php" target="_blank">PHP Date Function</a>.','post-expirator'); ?>
				</td>
			</tr>
		</table>
		<h3><?php _e('Post Footer Display','post-expirator');?></h3>
		<p><?php _e('Enabling this below will display the expiration date automatically at the end of any post which is set to expire.','post-expirator');?></p>
		<table class="form-table">
			<tr valign-"top">
				<th scope="row"><?php _e('Show in post footer?','post-expirator');?></th>
				<td>
					<input type="radio" name="expired-display-footer" id="expired-display-footer-true" value="1" <?php echo $expireddisplayfooterenabled ?>/> <label for="expired-display-footer-true"><?php _e('Enabled','post-expirator');?></label> 
					<input type="radio" name="expired-display-footer" id="expired-display-footer-false" value="0" <?php echo $expireddisplayfooterdisabled ?>/> <label for="expired-display-footer-false"><?php _e('Disabled','post-expirator');?></label>
					<br/>
					<?php _e('This will enable or disable displaying the post expiration date in the post footer.','post-expirator');?>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="expired-footer-contents"><?php _e('Footer Contents:','post-expirator');?></label></th>
				<td>
					<textarea id="expired-footer-contents" name="expired-footer-contents" rows="3" cols="50"><?php echo $expirationdateFooterContents; ?></textarea>
					<br/>
					<?php _e('Enter the text you would like to appear at the bottom of every post that will expire.  The following placeholders will be replaced with the post expiration date in the following format:','post-expirator');?>
					<ul>
						<li>EXPIRATIONFULL -> <?php echo date("$expirationdateDefaultDateFormat $expirationdateDefaultTimeFormat") ?></li>
						<li>EXPIRATIONDATE -> <?php echo date("$expirationdateDefaultDateFormat") ?></li>
						<li>EXPIRATIONTIME -> <?php echo date("$expirationdateDefaultTimeFormat") ?></li>
					</ul>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="expired-footer-style"><?php _e('Footer Style:','post-expirator');?></label></th>
				<td>
					<input type="text" name="expired-footer-style" id="expired-footer-style" value="<?php echo $expirationdateFooterStyle ?>" size="25" />
					(<span style="<?php echo $expirationdateFooterStyle ?>"><?php _e('This post will expire on','post-expirator');?> <?php echo date("$expirationdateDefaultDateFormat $expirationdateDefaultTimeFormat"); ?></span>)
					<br/>
					<?php _e('The inline css which will be used to style the footer text.','post-expirator');?>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="expirationdateSave" value="<?php _e('Save','post-expirator');?>" />
		</p>
	</form>
	<?php
}

function postExpiratorCronStatus() {
	$names = array('expirationdate_delete','expirationdate_delete_');
	// WPMU
	if (postExpirator_is_wpmu()) {
		global $current_blog;
		$names[] = 'expirationdate_delete_'.$current_blog->blog_id;
	}
	$results = array();
	foreach ( $names as $name ) {
		array_push($results,wp_get_schedule($name));
	}

	foreach ( $results as $result ) {
		if ($result == 'hourly') return false;
	}
	return true;
}

/**
 * Reset all cron schedules for Post Expirator Plugin
 */
function postExpiratorResetCron() {
        postExpiratorTimezoneSetup();
        if (postExpirator_is_wpmu()) {
		global $current_blog;
                wp_clear_scheduled_hook('expirationdate_delete_'.$current_blog->blog_id);
                wp_schedule_event(mktime(date('H'),0,0,date('m'),date('d'),date('Y')), 'postexpiratorminute', 'expirationdate_delete_'.$current_blog->blog_id);
        } else {
                wp_clear_scheduled_hook('expirationdate_delete');
                wp_clear_scheduled_hook('expirationdate_delete_');
                wp_schedule_event(mktime(date('H'),0,0,date('m'),date('d'),date('Y')), 'postexpiratorminute', 'expirationdate_delete');
	}
}

function postExpiratorMenuUpgrade() {
	if (isset($_POST['reset-cron-schedules'])) {
		postExpiratorResetCron();
                echo "<div id='message' class='updated fade'><p>"; _e('Cron Schedules Reset!','post-expirator'); echo "</p></div>";
	}

	$status = postExpiratorCronStatus();
	if ($status) 
		$cronstatus = '<span style="color:green">'.__('OK','post-expirator').'</span>';
	else
		$cronstatus = '<span style="color:red">'.__('RESET NEEDED','post-expirator').'</span>';

	?>
        <form method="post" id="postExpiratorMenuUpgrade">
                <h3><?php _e('Upgrade','post-expirator');?></h3>
                <table class="form-table">
                        <tr valign-"top">
                                <th scope="row"><label for="reset-cron-schedules"><?php _e('Reset Cron Schedules:','post-expirator');?></label></th>
                                <td>
					<input type="submit" name="reset-cron-schedules" id="reset-cron-schedules" value="<?php _e('Reset','post-expirator');?>" />
					<?php _e('Status:','post-expirator');?> <?php echo $cronstatus; ?>
                                        <br/>
					<?php _e('Resets the cron scheduler and removes any old or stray entries.','post-expirator');?>
                                </td>
                        </tr>
                </table>
        </form>
	<?php
}


// [postexpirator format="l F jS, Y g:ia" tz="foo"]
function postexpirator_shortcode($atts) {
	global $post;

        $expirationdatets = get_post_meta($post->ID,'expiration-date',true);
	if (empty($expirationdatets))
		return false;

	extract(shortcode_atts(array(
		'dateformat' => get_option('expirationdateDefaultDateFormat'),
		'timeformat' => get_option('expirationdateDefaultTimeFormat'),
		'type' => full,
		'tz' => date('T')
	), $atts));

	if (empty($dateformat)) {
		global $expirationdateDefaultDateFormat;
		$dateformat = $expirationdateDefaultDateFormat;		
	}

	if (empty($timeformat)) {
		global $expirationdateDefaultTimeFormat;
		$timeformat = $expirationdateDefaultTimeFormat;		
	}

	if ($type == 'full') 
		$format = $dateformat.' '.$timeformat;
	else if ($type == 'date')
		$format = $dateformat;
	else if ($type == 'time')
		$format = $timeformat;

	return date("$format",$expirationdatets);
}


function postexpirator_add_footer($text) {
	global $post;

	// Check to see if its enabled
	$displayFooter = get_option('expirationdateDisplayFooter');
	if ($displayFooter === false || $displayFooter == 0)
		return $text;

	postExpiratorTimezoneSetup();
        $expirationdatets = get_post_meta($post->ID,'expiration-date',true);
	if (!is_numeric($expirationdatets))
		return $text;

        $dateformat = get_option('expirationdateDefaultDateFormat');
	if (empty($dateformat)) {
		global $expirationdateDefaultDateFormat;
		$dateformat = $expirationdateDefaultDateFormat;		
	}

        $timeformat = get_option('expirationdateDefaultTimeFormat');
	if (empty($timeformat)) {
		global $expirationdateDefaultTimeFormat;
		$timeformat = $expirationdateDefaultTimeFormat;		
	}

        $expirationdateFooterContents = get_option('expirationdateFooterContents');
        if (empty($expirationdateFooterContents)) {
                global $expirationdateDefaultFooterContents;
                $expirationdateFooterContents = $expirationdateDefaultFooterContents;
        }
	
        $expirationdateFooterStyle = get_option('expirationdateFooterStyle');
        if (empty($expirationdateFooterStyle)) {
                global $expirationdateDefaultFooterStyle;
                $expirationdateFooterStyle = $expirationdateDefaultFooterStyle;
        }
	
	$search = array(
		'EXPIRATIONFULL',
		'EXPIRATIONDATE',
		'EXPIRATIONTIME'
	);
	$replace = array(
		date("$dateformat $timeformat",$expirationdatets),
		date("$dateformat",$expirationdatets),
		date("$timeformat",$expirationdatets)
	);

	$add_to_footer = '<p style="'.$expirationdateFooterStyle.'">'.str_replace($search,$replace,$expirationdateFooterContents).'</p>';
	return $text.$add_to_footer;
}