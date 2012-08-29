<?php
/*
Plugin Name: Post Expirator
Plugin URI: http://wordpress.org/extend/plugins/post-expirator/
Description: Allows you to add an expiration date (minute) to posts which you can configure to either delete the post, change it to a draft, or update the post categories at expiration time.
Author: Aaron Axelsen
Version: 1.6
Author URI: http://postexpirator.tuxdocs.net/
Translation: Thierry (http://palijn.info)
Text Domain: post-expirator
*/

define('POSTEXPIRATOR_VERSION','1.6');

/* Load translation, if it exists */
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'post-expirator', null, $plugin_dir.'/languages/' );

// Default Values
$expirationdateDefaultDateFormat = __('l F jS, Y','post-expirator');
$expirationdateDefaultTimeFormat = __('g:ia','post-expirator');
$expirationdateDefaultFooterContents = __('Post expires at EXPIRATIONTIME on EXPIRATIONDATE','post-expirator');
$expirationdateDefaultFooterStyle = 'font-style: italic;';

// Detect WPMU/MultiSite
function postExpirator_is_wpmu() {
	return is_multisite();
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
add_filter('cron_schedules','postExpiratorAddCronMinutes');

/**
 * Add admin notice hook if cron schedule needs to be reset
 */
function postExpirationAdminNotice() {
	if (postExpiratorCronEventStatus() === false) {
		echo '<div class="error fade post-expirator-adminnotice"><p><strong>';
		_e('Post Expirator cron events need to be reset. ','post-expirator');
		echo('<a href="'.admin_url('options-general.php?page=post-expirator.php&tab=diagnostics').'" style="color: blue;">');
		_e('Click here to reset','post-expirator');
		echo('</a></strong></p></div>');
	}
}
add_action('admin_notices','postExpirationAdminNotice');

/** 
 * Function that does the actual deleting - called by wp_cron
 */
function expirationdate_delete_expired_posts() {
	global $wpdb;
	postExpiratorTimezoneSetup();
	$time_delete = time();
	$sql = 'select post_id, meta_value from ' . $wpdb->postmeta . ' as postmeta, '.$wpdb->posts.' as posts where postmeta.post_id = posts.ID AND posts.post_status = "publish" AND postmeta.meta_key = "expiration-date" AND postmeta.meta_value <= "' . $time_delete . '"';
	$result = $wpdb->get_results($sql);

	$debugEnabled = postExpiratorDebugEnabled();
	if ($debugEnabled) {
		// Load Debug Class
		require_once(plugin_dir_path(__FILE__).'post-expirator-debug.php');

		$debug = new postExpiratorDebug();
		$startts = mktime();
		$debug->save(array('timestamp' => $startts,'message' => 'START'));
		$debug->save(array('timestamp' => $startts,'message' => 'SQL EXPIRE: '.$sql));
	}

	$processsql = 'select post_id from '.$wpdb->postmeta.' where meta_key = "_expiration-date-processed" AND meta_value = "1"';
	if ($debugEnabled) $debug->save(array('timestamp' => mktime(),'message' => 'SQL PROCESSED: '.$processsql));
	$processresult = $wpdb->get_col($processsql);
	if ($debugEnabled) $debug->save(array('timestamp' => mktime(),'message' => 'PROCESSED POST IDS: '.print_r($processresult,true)));

  	if (!empty($result)) foreach ($result as $a) {
		// Check to see if already proccessed
		if (in_array($a->post_id,$processresult)) continue;

		$post_result = $wpdb->get_var('select post_type from ' . $wpdb->posts .' where ID = '. $a->post_id);
		if ($post_result == 'post') {
			$expiredStatus = strtolower(get_option('expirationdateExpiredPostStatus'));
		} else if ($post_result == 'page') {
			$expiredStatus = strtolower(get_option('expirationdateExpiredPageStatus'));
		} else {
			$expiredStatus = 'draft';
		}

		// Check to see if expiration category is enabled and set - otherwise, proceed as normal
        	$catEnabled = get_option('expirationdateCategory');
		$cat = get_post_meta($a->post_id,'_expiration-date-category');
	        if (($catEnabled === false || $catEnabled == 1) && (isset($cat) && !empty($cat[0]))) {
			wp_update_post(array('ID' => $a->post_id, 'post_category' => $cat[0]));
			if ($debugEnabled) $debug->save(array('timestamp' => mktime(),'message' => 'EXPIRE: ID:'.$a->post_id.' CATEGORY:'.print_r($cat[0],true)));
		} else {
			if ($expiredStatus == 'delete') {
				wp_delete_post($a->post_id);
				if ($debugEnabled) $debug->save(array('timestamp' => mktime(),'message' => 'EXPIRE: ID:'.$a->post_id.' DELETE'));
			} else {
				wp_update_post(array('ID' => $a->post_id, 'post_status' => 'draft'));
        		        delete_post_meta($a->post_id, 'expiration-date');
       			        update_post_meta($a->post_id, 'expiration-date', $a->meta_value);
				if ($debugEnabled) $debug->save(array('timestamp' => mktime(),'message' => 'EXPIRE: ID:'.$a->post_id.' DRAFT'));
			}
		}

		// Mark as Processed
		update_post_meta($a->post_id, '_expiration-date-processed', 1);
		if ($debugEnabled) $debug->save(array('timestamp' => mktime(),'message' => 'PROCESSED: ID:'.$a->post_id));
	}
	if ($debugEnabled) $debug->save(array('timestamp' => mktime(),'message' => 'END - DURATION: '.(mktime() - $startts)));
}
if (postExpirator_is_wpmu())
	add_action ('expirationdate_delete_'.$current_blog->blog_id, 'expirationdate_delete_expired_posts');
else
	add_action ('expirationdate_delete', 'expirationdate_delete_expired_posts');

/**
 * adds an 'Expires' column to the post display table.
 */
function expirationdate_add_column ($columns) {
  	$columns['expirationdate'] = __('Expires','post-expirator').'<br/><span class="post-expirator-addcolumn">(YYYY/MM/DD HH:MM)</span>';
  	return $columns;
}
add_filter ('manage_posts_columns', 'expirationdate_add_column');
add_filter ('manage_pages_columns', 'expirationdate_add_column');

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
    		echo ($ed ? date('Y/m/d H:i',$ed) : __("Never",'post-expirator'));
  	}
}
add_action ('manage_posts_custom_column', 'expirationdate_show_value');
add_action ('manage_pages_custom_column', 'expirationdate_show_value');

/**
 * Adds hooks to get the meta box added to pages and custom post types
 */
function expirationdate_meta_custom() {
	$custom_post_types = get_post_types();
	foreach ($custom_post_types as $t) {
		add_meta_box('expirationdatediv', __('Post Expirator','post-expirator'), 'expirationdate_meta_box', $t, 'side', 'core');
	}
	add_meta_box('expirationdatediv', __('Post Expirator','post-expirator'), 'expirationdate_meta_box', 'page', 'side', 'core');
}
add_action ('add_meta_boxes','expirationdate_meta_custom');

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
		$disabled = ' disabled="disabled"';
		$categories = get_option('expirationdateCategoryDefaults');
	} else {
		$defaultmonth = date('F',$expirationdatets);
		$defaultday = date('d',$expirationdatets);
		$defaultyear = date('Y',$expirationdatets);
		$defaulthour = date('H',$expirationdatets);
		$defaultminute = date('i',$expirationdatets);
		$categories = get_post_meta($post->ID,'_expiration-date-category',true);
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
	$rv[] = '</tr><tr>';
	$rv[] = '<td>';	
	$rv[] = '<select name="expirationdate_year" id="expirationdate_year"'.$disabled.'>';
	$currentyear = date('Y');

	if ($defaultyear < $currentyear) $currentyear = $defaultyear;

	for($i = $currentyear; $i < $currentyear + 8; $i++) {
		if ($i == $defaultyear)
			$selected = ' selected="selected"';
		else
			$selected = '';
		$rv[] = '<option'.$selected.'>'.($i).'</option>';
	}
	$rv[] = '</select>';
	$rv[] = '</td><td>';
	$rv[] = '<select name="expirationdate_month" id="expirationdate_month"'.$disabled.'>';

	for($i = 1; $i <= 12; $i++) {
		if ($defaultmonth == date('F',mktime(0, 0, 0, $i, 1, date("Y"))))
			$selected = ' selected="selected"';
		else
			$selected = '';
		$rv[] = '<option value="'.date('m',mktime(0, 0, 0, $i, 1, date("Y"))).'"'.$selected.'>'.date_i18n('F',mktime(0, 0, 0, $i, 1, date("Y"))).'</option>';
	}

	$rv[] = '</select>';	 
	$rv[] = '</td><td>';
	$rv[] = '<input type="text" id="expirationdate_day" name="expirationdate_day" value="'.$defaultday.'" size="2"'.$disabled.' />,';
	$rv[] = '</td></tr><tr>';
	$rv[] = '<th style="text-align: left;"></th>';
	$rv[] = '<th style="text-align: left;">'.__('Hour','post-expirator').'('.date('T',mktime(0, 0, 0, $i, 1, date("Y"))).')</th>';
	$rv[] = '<th style="text-align: left;">'.__('Minute','post-expirator').'</th>';
	$rv[] = '</tr><tr>';
	$rv[] = '<td>@</td><td>';
 	$rv[] = '<select name="expirationdate_hour" id="expirationdate_hour"'.$disabled.'>';

	for($i = 1; $i <= 24; $i++) {
		if ($defaulthour == date('H',mktime($i, 0, 0, date("n"), date("j"), date("Y"))))
			$selected = ' selected="selected"';
		else
			$selected = '';
		$rv[] = '<option value="'.date('H',mktime($i, 0, 0, date("n"), date("j"), date("Y"))).'"'.$selected.'>'.date_i18n('H',mktime($i, 0, 0, date("n"), date("j"), date("Y"))).'</option>';
	}

	$rv[] = '</select></td><td>';
	$rv[] = '<input type="text" id="expirationdate_minute" name="expirationdate_minute" value="'.$defaultminute.'" size="2"'.$disabled.' />';
	$rv[] = '<input type="hidden" name="expirationdate_formcheck" value="true" />';
	$rv[] = '</td></tr></table>';
	echo implode("\n",$rv);

	$catEnabled = get_option('expirationdateCategory');
	if (($post->post_type != 'page') && ($catEnabled === false || $catEnabled == 1)) {
		$args = array('hide_empty' => 0,'orderby' => 'name','hierarchical' => 0);
		echo '<br/>'.__('Expiration Categories','post-expirator').':<br/>';

		echo '<div class="wp-tab-panel" id="post-expirator-cat-list">';
		echo '<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">';
		$walker = new Walker_PostExpirator_Category_Checklist();
		if (!empty($disabled)) $walker->setDisabled();
		wp_terms_checklist(0, array( 'taxonomy' => 'category', 'walker' => $walker, 'selected_cats' => $categories, 'checked_ontop' => false ) );
		echo '</ul>';
		echo '</div>';

		echo '<p class="howto">'.__('Setting an expiration category will change the category at expiration time, and override the default post action','post-expirator').'.</p>';
	}
	echo '<div id="expirationdate_ajax_result"></div>';
}

/**
 * Add's ajax javascript
 */
function expirationdate_js_admin_header() {
	// Define custom JavaScript function
	?>
<script type="text/javascript">
//<![CDATA[
function expirationdate_ajax_add_meta(expireenable) {
	var expire = document.getElementById(expireenable);

	if (expire.checked == true) {
		var enable = 'true';
		document.getElementById('expirationdate_month').disabled = false;
		document.getElementById('expirationdate_day').disabled = false;
		document.getElementById('expirationdate_year').disabled = false;
		document.getElementById('expirationdate_hour').disabled = false;
		document.getElementById('expirationdate_minute').disabled = false;
		var cats = document.getElementsByName('expirationdate_category[]');
		var max = cats.length;
		for (var i=0; i<max; i++) {
			cats[i].disabled = '';
		}
	} else {
		document.getElementById('expirationdate_month').disabled = true;
		document.getElementById('expirationdate_day').disabled = true;
		document.getElementById('expirationdate_year').disabled = true;
		document.getElementById('expirationdate_hour').disabled = true;
		document.getElementById('expirationdate_minute').disabled = true;
		var cats = document.getElementsByName('expirationdate_category[]');
		var max = cats.length;
		for (var i=0; i<max; i++) {
			cats[i].disabled = 'disable';
		}
		var enable = 'false';
	}
	
	return true;
}
//]]>
</script>
<?php
}
add_action('admin_head', 'expirationdate_js_admin_header' );

/**
 * Get correct URL (HTTP or HTTPS)
 */
function expirationdate_get_blog_url() {
	if (postExpirator_is_wpmu())	
		echo network_home_url('/');
	else
        	echo home_url('/');
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
	$category = $_POST['expirationdate_category'];
	if (isset($_POST['enable-expirationdate'])) {
	        postExpiratorTimezoneSetup();
        	// Format Date
        	$ts = mktime($hour,$minute,0,$month,$day,$year);

        	// Update Post Meta
		delete_post_meta($id, '_expiration-date-category');
		delete_post_meta($id, 'expiration-date');
	        update_post_meta($id, 'expiration-date', $ts);

        	$catEnabled = get_option('expirationdateCategory');
	        if ((isset($category) && !empty($category)) && ($catEnabled === false || $catEnabled == 1)) {
			if (!empty($category)) update_post_meta($id, '_expiration-date-category', $category);
		}
		update_post_meta($id, '_expiration-date-processed', 0);
	} else {
		delete_post_meta($id, 'expiration-date');
		delete_post_meta($id, '_expiration-date-category');
		delete_post_meta($id, '_expiration-date-processed');
	}
}
add_action('save_post','expirationdate_update_post_meta');

/**
 * Build the menu for the options page
 */
function postExpiratorMenuTabs($tab) {
        echo '<p>';
	if (empty($tab)) $tab = 'general';
        echo '<a href="'.admin_url('options-general.php?page=post-expirator.php&tab=general').'"'.($tab == 'general' ? ' style="font-weight: bold; text-decoration:none;"' : '').'>'.__('General Settings','post-expirator').'</a> | ';
        echo '<a href="'.admin_url('options-general.php?page=post-expirator.php&tab=diagnostics').'"'.($tab == 'diagnostics' ? ' style="font-weight: bold; text-decoration:none;"' : '').'>'.__('Diagnostics','post-expirator').'</a>';
	echo ' | <a href="'.admin_url('options-general.php?page=post-expirator.php&tab=viewdebug').'"'.($tab == 'viewdebug' ? ' style="font-weight: bold; text-decoration:none;"' : '').'>'.__('View Debug Logs','post-expirator').'</a>';
        echo '</p><hr/>';
}

/**
 *
 */
function postExpiratorMenu() {
        $tab = $_GET['tab'];

	echo '<div class="wrap">';
        echo '<h2>'.__('Post Expirator Options','post-expirator').'</h2>';

	postExpiratorMenuTabs($tab);
	if (empty($tab) || $tab == 'general') {
		postExpiratorMenuGeneral();
	} elseif ($tab == 'diagnostics') {
		postExpiratorMenuDiagnostics();
	} elseif ($tab == 'viewdebug') {
		postExpiratorMenuViewdebug();
	}
	echo '</div>';
}

/**
 * Hook's to add plugin page menu
 */
function postExpiratorPluginMenu() {
	add_submenu_page('options-general.php',__('Post Expirator Options','post-expirator'),__('Post Expirator','post-expirator'),'edit_plugins',basename(__FILE__),'postExpiratorMenu');
}
add_action('admin_menu', 'postExpiratorPluginMenu');

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
		update_option('expirationdateCategory',$_POST['expired-category']);
		update_option('expirationdateCategoryDefaults',$_POST['expirationdate_category']);
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

	$categories = get_option('expirationdateCategoryDefaults');

	$expiredcategory = get_option('expirationdateCategory');
	if (!isset($expiredcategory))
		$expiredcategory = 1;

	$expireddisplayfooter = get_option('expirationdateDisplayFooter');
	if (empty($expireddisplayfooter))
		$expireddisplayfooter = 0;

	$expireddisplayfooterenabled = '';
	$expireddisplayfooterdisabled = '';
	if ($expireddisplayfooter == 0)
		$expireddisplayfooterdisabled = 'checked="checked"';
	else if ($expireddisplayfooter == 1)
		$expireddisplayfooterenabled = 'checked="checked"';
	
	if ($expiredcategory == 0)
		$expiredcategorydisabled = 'checked="checked"';
	else if ($expiredcategory == 1)
		$expiredcategoryenabled = 'checked="checked"';

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
	<?php _e('Valid [postexpirator] attributes:','post-expirator'); ?>
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
					<input type="text" name="expired-default-date-format" id="expired-default-date-format" value="<?php echo $expirationdateDefaultDateFormat ?>" size="25" /> (<?php echo date_i18n("$expirationdateDefaultDateFormat") ?>)
					<br/>
					<?php _e('The default format to use when displaying the expiration date within a post using the [postexpirator] shortcode or within the footer.  For information on valid formatting options, see: <a href="http://us2.php.net/manual/en/function.date.php" target="_blank">PHP Date Function</a>.','post-expirator'); ?>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="expired-default-time-format"><?php _e('Time Format:','post-expirator');?></label></th>
				<td>
					<input type="text" name="expired-default-time-format" id="expired-default-time-format" value="<?php echo $expirationdateDefaultTimeFormat ?>" size="25" /> (<?php echo date_i18n("$expirationdateDefaultTimeFormat") ?>)
					<br/>
					<?php _e('The default format to use when displaying the expiration time within a post using the [postexpirator] shortcode or within the footer.  For information on valid formatting options, see: <a href="http://us2.php.net/manual/en/function.date.php" target="_blank">PHP Date Function</a>.','post-expirator'); ?>
				</td>
			</tr>
		</table>
		<h3><?php _e('Category Expiration','post-expirator');?></h3>
		<table class="form-table">
			<tr valign-"top">
				<th scope="row"><?php _e('Enable Post Expiration to Category?','post-expirator');?></th>
				<td>
					<input type="radio" name="expired-category" id="expired-category-true" value="1" <?php echo $expiredcategoryenabled ?>/> <label for="expired-category-true"><?php _e('Enabled','post-expirator');?></label> 
					<input type="radio" name="expired-category" id="expired-category-false" value="0" <?php echo $expiredcategorydisabled ?>/> <label for="expired-category-false"><?php _e('Disabled','post-expirator');?></label>
					<br/>
					<?php _e('This will enable or disable the ability to expire a post to a category.','post-expirator');?>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><?php _e('Default Expiration Category','post-expirator');?>:</th>
				<td>
		<?php
                echo '<div class="wp-tab-panel" id="post-expirator-cat-list">';
                echo '<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">';
                $walker = new Walker_PostExpirator_Category_Checklist();
                wp_terms_checklist(0, array( 'taxonomy' => 'category', 'walker' => $walker, 'selected_cats' => $categories, 'checked_ontop' => false ) );
                echo '</ul>';
                echo '</div>';
		?>
					<br/>
					<?php _e("Set's the default expiration category for the post.",'post-expirator');?>
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
						<li>EXPIRATIONFULL -> <?php echo date_i18n("$expirationdateDefaultDateFormat $expirationdateDefaultTimeFormat") ?></li>
						<li>EXPIRATIONDATE -> <?php echo date_i18n("$expirationdateDefaultDateFormat") ?></li>
						<li>EXPIRATIONTIME -> <?php echo date_i18n("$expirationdateDefaultTimeFormat") ?></li>
					</ul>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="expired-footer-style"><?php _e('Footer Style:','post-expirator');?></label></th>
				<td>
					<input type="text" name="expired-footer-style" id="expired-footer-style" value="<?php echo $expirationdateFooterStyle ?>" size="25" />
					(<span style="<?php echo $expirationdateFooterStyle ?>"><?php _e('This post will expire on','post-expirator');?> <?php echo date_i18n("$expirationdateDefaultDateFormat $expirationdateDefaultTimeFormat"); ?></span>)
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

function postExpiratorCronEventStatus() {
	$names = array();
	// WPMU
	if (postExpirator_is_wpmu()) {
		global $current_blog;
		$names[] = 'expirationdate_delete_'.$current_blog->blog_id;
	} else {
		$names[] = 'expirationdate_delete';
	}

	$results = array();
	foreach ( $names as $name ) {
		if (wp_get_schedule($name) !== false) array_push($results,wp_get_schedule($name));
	}

	if (empty($results)) return false;

 	foreach ( $results as $result ) {
		if ($result == 'hourly') return false;
	}
	return true;
}

function postExpiratorCronScheduleStatus() {
	$schedules = wp_get_schedules();

	if (isset($schedules['postexpiratorminute'])) {
		if ($schedules['postexpiratorminute']['interval'] == '60')
			return true;
	}
	return false;
}

/**
 * Reset all cron events for Post Expirator Plugin
 */
function postExpiratorResetCronEvent() {
	postExpiratorTimezoneSetup();

	wp_clear_scheduled_hook('expirationdate_delete');
	wp_clear_scheduled_hook('expirationdate_delete_');

	if (postExpirator_is_wpmu()) {
		global $current_blog;
		wp_clear_scheduled_hook('expirationdate_delete_'.$current_blog->blog_id);
		wp_schedule_event(mktime(date('H'),0,0,date('m'),date('d'),date('Y')), 'postexpiratorminute', 'expirationdate_delete_'.$current_blog->blog_id);
	} else {
		wp_schedule_event(mktime(date('H'),0,0,date('m'),date('d'),date('Y')), 'postexpiratorminute', 'expirationdate_delete');
	}
}

function postExpiratorMenuDiagnostics() {
	if (isset($_POST['reset-cron-event'])) {
		postExpiratorResetCronEvent();
                echo "<div id='message' class='updated fade'><p>"; _e('Cron Events Reset! (Reload page to remove warning)','post-expirator'); echo "</p></div>";
	} elseif (isset($_POST['debugging-disable'])) {
		update_option('expirationdateDebug',0);
                echo "<div id='message' class='updated fade'><p>"; _e('Debugging Disabled','post-expirator'); echo "</p></div>";
	} elseif (isset($_POST['debugging-enable'])) {
		update_option('expirationdateDebug',1);
                echo "<div id='message' class='updated fade'><p>"; _e('Debugging Enabled','post-expirator'); echo "</p></div>";
	} elseif (isset($_POST['purge-debug'])) {
		require_once(plugin_dir_path(__FILE__).'post-expirator-debug.php');
		$debug = new postExpiratorDebug();
		$debug->purge();
                echo "<div id='message' class='updated fade'><p>"; _e('Debugging Table Emptied','post-expirator'); echo "</p></div>";
	}

	$status = postExpiratorCronEventStatus();
	if ($status) $cronstatus = '<span style="color:green">'.__('OK','post-expirator').'</span>';
	else $cronstatus = '<span style="color:red">'.__('RESET NEEDED','post-expirator').'</span>';

	$schedule = postExpiratorCronScheduleStatus();
	if ($schedule) $cronschedulestatus = '<span style="color:green">'.__('OK','post-expirator').'</span>';
	else $cronschedulestatus = '<span style="color:red">'.__('ERROR WITH FILTER','post-expirator').'</span>';

	$debug = postExpiratorDebugEnabled();

	?>
        <form method="post" id="postExpiratorMenuUpgrade">
                <h3><?php _e('Cron Settings','post-expirator');?></h3>
                <table class="form-table">
                        <tr valign-"top">
                                <th scope="row"><label for="reset-cron-event"><?php _e('Reset Cron Event:','post-expirator');?></label></th>
                                <td>
					<input type="submit" name="reset-cron-event" id="reset-cron-event" value="<?php _e('Reset','post-expirator');?>" />
					<?php _e('Status:','post-expirator');?> <?php echo $cronstatus; ?>
                                        <br/>
					<?php _e('Resets the cron event and removes any old or stray entries.','post-expirator');?>
                                </td>
                        </tr>
                        <tr valign-"top">
                                <th scope="row"><label for="reset-cron-schedule"><?php _e('Reset Cron Schedule:','post-expirator');?></label></th>
                                <td>
					<?php _e('Status:','post-expirator');?> <?php echo $cronschedulestatus; ?>
                                        <br/>
					<?php _e('Displays the cron minute schedule status for post expirator.','post-expirator');?>
                                </td>
                        </tr>
		</table>

                <h3><?php _e('Advanced Diagnostics','post-expirator');?></h3>
                <table class="form-table">		
                        <tr valign-"top">
                                <th scope="row"><label for="postexpirator-log"><?php _e('Post Expirator Debug Logging:','post-expirator');?></label></th>
                                <td>
					<?php
					if ($debug) { 
						echo __('Status: Enabled','post-expirator').'<br/>';
						echo '<input type="submit" name="debugging-disable" id="debugging-disable" value="'.__('Disable Debugging','post-expirator').'" />';
					} else {
						echo __('Status: Disabled','post-expirator').'<br/>';
						echo '<input type="submit" name="debugging-enable" id="debugging-enable" value="'.__('Enable Debugging','post-expirator').'" />';
					}
					?>
                                        <br/>
					<a href="<?php echo admin_url('options-general.php?page=post-expirator.php&tab=viewdebug') ?>">View Debug Logs</a>
                                </td>
                        </tr>
                        <tr valign-"top">
                                <th scope="row"><?php _e('Purge Debug Log:','post-expirator');?></th>
                                <td>
					<input type="submit" name="purge-debug" id="purge-debug" value="<?php _e('Purge Debug Log','post-expirator');?>" />
				</td>
			</tr/>
                        <tr valign-"top">
                                <th scope="row"><label for="cron-schedule"><?php _e('Current Cron Schedule:','post-expirator');?></label></th>
                                <td>
					<?php _e('The below table will show all currently scheduled cron events with the next run time.','post-expirator');?><br/>
					<?php _e('Single site (non multisite) users should see an event named expirationdate_delete with a schedule of postexpiratorminute.','post-expirator');?><br/>
					<?php if (postExpirator_is_wpmu()) _e('Multisite users should see an event named expirationdate_delete_blogid (where the blogid is replaced with the numeric id of the blog) with a schedule of postexpiratorminute','post-expirator');?>
					<table>
						<tr>
							<th><?php _e('Date','post-expirator');?></th>
							<th><?php _e('Event','post-expirator');?></th>
							<th><?php _e('Schedule','post-expirator');?></th>
							<th><?php _e('Interval (seconds)','post-expirator');?></th>
						</tr>
					<?php
					$cron = _get_cron_array();
					foreach ($cron as $key=>$value) {
						foreach ($value as $eventkey=>$eventvalue) {
						print '<tr>';
						print '<td>'.date_i18n('r',$key).'</td>';
						print '<td>'.$eventkey.'</td>';
						$arrkey = array_keys($eventvalue);
						print '<td>'.$eventvalue[$arrkey[0]]['schedule'].'</td>';
						print '<td>'.$eventvalue[$arrkey[0]]['interval'].'</td>';
						print '</tr>';
						}
					}
					?>
					</table>
                                </td>
                        </tr>
                </table>
        </form>
	<?php
}

function postExpiratorMenuViewdebug() {
	require_once(plugin_dir_path(__FILE__).'post-expirator-debug.php');
	print "<p>".__('Below is a dump of the debugging table, this should be useful for troubleshooting.','post-expirator')."</p>";
	$debug = new postExpiratorDebug();
	$debug->getTable();
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

	postExpiratorTimezoneSetup();
	return date("$format",$expirationdatets);
}
add_shortcode('postexpirator', 'postexpirator_shortcode');

function postexpirator_add_footer($text) {
	global $post;

	// Check to see if its enabled
	$displayFooter = get_option('expirationdateDisplayFooter');
	if ($displayFooter === false || $displayFooter == 0)
		return $text;

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
	
	postExpiratorTimezoneSetup();
	$search = array(
		'EXPIRATIONFULL',
		'EXPIRATIONDATE',
		'EXPIRATIONTIME'
	);
	$replace = array(
		date_i18n("$dateformat $timeformat",$expirationdatets),
		date_i18n("$dateformat",$expirationdatets),
		date_i18n("$timeformat",$expirationdatets)
	);

	$add_to_footer = '<p style="'.$expirationdateFooterStyle.'">'.str_replace($search,$replace,$expirationdateFooterContents).'</p>';
	return $text.$add_to_footer;
}
add_action('the_content','postexpirator_add_footer',0);

/**
 * Check for Debug
 */
function postExpiratorDebugEnabled() {
	$debug = get_option('expirationdateDebug');
	if ($debug == 1) return true;
	return false;
}

/**
 * Add Stylesheet
 */
function postexpirator_css() {
        $myStyleUrl = plugins_url('style.css', __FILE__); // Respects SSL, Style.css is relative to the current file
        $myStyleFile = WP_PLUGIN_DIR . '/post-expirator/style.css';
        if ( file_exists($myStyleFile) ) {
            wp_register_style('postexpirator-css', $myStyleUrl);
            wp_enqueue_style('postexpirator-css');
        }

}
add_action('admin_init','postexpirator_css');

/**
 * Post Expirator Activation/Upgrade
 */
function postexpirator_upgrade() {
	// Check for current version, if not exists, run activation
	$version = get_option('postexpiratorVersion');
	if ($version === false) { //not installed, run default activation
		postexpirator_activate();
		update_option('postexpiratorVersion',POSTEXPIRATOR_VERSION);
	}
}
add_action('admin_init','postexpirator_upgrade');

/** 
 * Called at plugin activation
 */
function postexpirator_activate () {
	global $current_blog,$expirationdateDefaultDateFormat,$expirationdateDefaultTimeFormat,$expirationdateDefaultFooterContents,$expirationdateDefaultFooterStyle;
	if (get_option('expirationdateExpiredPostStatus') === false) 	update_option('expirationdateExpiredPostStatus','Draft');
	if (get_option('expirationdateExpiredPageStatus') === false)	update_option('expirationdateExpiredPageStatus','Draft');
	if (get_option('expirationdateDefaultDateFormat') === false)	update_option('expirationdateDefaultDateFormat',$expirationdateDefaultDateFormat);
	if (get_option('expirationdateDefaultTimeFormat') === false)	update_option('expirationdateDefaultTimeFormat',$expirationdateDefaultTimeFormat);
	if (get_option('expirationdateFooterContents') === false)	update_option('expirationdateFooterContents',$expirationdateDefaultFooterContents);
	if (get_option('expirationdateFooterStyle') === false)		update_option('expirationdateFooterStyle',$expirationdateDefaultFooterStyle);
	if (get_option('expirationdateDisplayFooter') === false)	update_option('expirationdateDisplayFooter',0);
	if (get_option('expirationdateCategory') === false)		update_option('expirationdateCategory',1);
	if (get_option('expirationdateDebug') === false)		update_option('expirationdateDebug',0);
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
	delete_option('expirationdateCategory');
	delete_option('expirationdateDebug');
	delete_option('postexpiratorVersion');
	if (postExpirator_is_wpmu())
		wp_clear_scheduled_hook('expirationdate_delete_'.$current_blog->blog_id);
	else
		wp_clear_scheduled_hook('expirationdate_delete');
	require_once(plugin_dir_path(__FILE__).'post-expirator-debug.php');
	$debug = new postExpiratorDebug();
	$debug->removeDbTable();
}
register_deactivation_hook (__FILE__, 'expirationdate_deactivate');

class Walker_PostExpirator_Category_Checklist extends Walker {
        var $tree_type = 'category';
        var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	var $disabled = '';

	function setDisabled() {
		$this->disabled = 'disabled="disabled"';
	}

        function start_lvl(&$output, $depth, $args) {
                $indent = str_repeat("\t", $depth);
                $output .= "$indent<ul class='children'>\n";
        }

        function end_lvl(&$output, $depth, $args) {
                $indent = str_repeat("\t", $depth);
                $output .= "$indent</ul>\n";
        }

        function start_el(&$output, $category, $depth, $args) {
                extract($args);
                if ( empty($taxonomy) )
                        $taxonomy = 'category';

		$name = 'expirationdate_category';

                $class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
                $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' '.$this->disabled.'/> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
        }

        function end_el(&$output, $category, $depth, $args) {
                $output .= "</li>\n";
        }
}

