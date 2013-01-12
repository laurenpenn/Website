<?PHP
if (!defined('ABSPATH')) 
	die();


echo "<div class=\"wrap\">";
screen_icon();
echo "<h2>".esc_html( __('BackWPup Tools', 'backwpup'))."</h2>";
if (isset($backwpup_message) and !empty($backwpup_message)) 
	echo "<div id=\"message\" class=\"updated\"><p>".$backwpup_message."</p></div>";
echo "<form id=\"posts-filter\" enctype=\"multipart/form-data\" action=\"".backwpup_admin_url('admin.php')."?page=backwpuptools\" method=\"post\">";
wp_nonce_field('backwpup-tools');
?>
<input type="hidden" name="action" value="update" />
<h3><?PHP _e('Database restore','backwpup'); ?></h3> 
<table class="form-table"> 
<tr valign="top">
<th scope="row"><label for="mailsndemail"><?PHP _e('DB Restore','backwpup'); ?></label></th>
<td>
<?PHP
if (isset($_POST['dbrestore']) and $_POST['dbrestore']==__('Restore', 'backwpup') and is_file(trim($_POST['sqlfile']))) {
	check_admin_referer('backwpup-tools');
	$sqlfile=trim($_POST['sqlfile']);
	require(dirname(__FILE__).'/tools/db_restore.php');
} else {
	if ( $dir = @opendir(ABSPATH)) {
		$sqlfile="";
		while (($file = readdir( $dir ) ) !== false ) {
			if (strtolower(substr($file,-4))==".sql") {
				$sqlfile=$file;
				break;
			}	
		}
		@closedir( $dir );
	}
	if (!empty($sqlfile)) {
		echo __('SQL File to restore:','backwpup').' '.trailingslashit(ABSPATH).$sqlfile."<br />";
		?>
		<input type="hidden" class="regular-text" name="sqlfile" id="sqlfile" value="<?PHP echo trailingslashit(ABSPATH).$sqlfile;?>" />
		<input type="submit" name="dbrestore" class="button-primary" value="<?php _e('Restore', 'backwpup'); ?>" />
		<?PHP
	} else {
		echo __('Copy SQL file to blog root folder to use for a restoring.', 'backwpup')."<br />";
	}
}
?>
</td>
</tr>
</table>

<h3><?PHP _e('Import Jobs settings','backwpup'); ?></h3>
<table class="form-table"> 
<tr valign="top"> 
<th scope="row"><label for="importfile"><?php _e('Select file to import:', 'backwpup'); ?></label></th> 
<td><input name="importfile" type="file" id="importfile" class="regular-text code" /> 
<input type="submit" name="upload" class="button-primary" value="<?php _e('Upload', 'backwpup'); ?>" />
</td> 
</tr>
<tr valign="top"> 
<?PHP
if (isset($_POST['upload']) and is_uploaded_file($_FILES['importfile']['tmp_name']) and $_POST['upload']==__('Upload', 'backwpup')) {
	echo "<th scope=\"row\"><label for=\"maxlogs\">".__('Select jobs to import','backwpup')."</label></th><td>";
	$import=file_get_contents($_FILES['importfile']['tmp_name']);
	$oldjobs=get_option('backwpup_jobs');
	foreach ( unserialize($import) as $jobid => $jobvalue ) {
		echo "<select name=\"importtype[".$jobid."]\" title=\"".__('Import Type', 'backwpup')."\"><option value=\"not\">".__('No Import', 'backwpup')."</option>";
		if (is_array($oldjobs[$jobid]))
			echo "<option value=\"over\">".__('Overwrite', 'backwpup')."</option><option value=\"append\">".__('Append', 'backwpup')."</option>"; 
		else
			echo "<option value=\"over\">".__('Import', 'backwpup')."</option>";
		echo "</select>";
		echo '&nbsp;<span class="description">'.$jobid.". ".$jobvalue['name'].'</span><br />';
	}
	echo "<input type=\"hidden\" name=\"importfile\" value=\"".urlencode($import)."\" />";
	echo "<input type=\"submit\" name=\"import\" class=\"button-primary\" value=\"".__('Import', 'backwpup')."\" />";
}
if (isset($_POST['import']) and $_POST['import']==__('Import', 'backwpup') and !empty($_POST['importfile'])) {
	echo "<th scope=\"row\"><label for=\"maxlogs\">".__('Import','backwpup')."</label></th><td>";
	$oldjobs=get_option('backwpup_jobs');
	$import=unserialize(urldecode($_POST['importfile']));
	foreach ( $_POST['importtype'] as $id => $type ) {
		if ($type=='over') {
			unset($oldjobs[$id]);
			$oldjobs[$id]=$import[$id];
			$oldjobs[$id]['activated']=false;
			$oldjobs[$id]['cronnextrun']='';
			$oldjobs[$id]['starttime']='';
			$oldjobs[$id]['logfile']='';
			$oldjobs[$id]['lastlogfile']='';
			$oldjobs[$id]['lastrun']='';
			$oldjobs[$id]['lastruntime']='';
			$oldjobs[$id]['lastbackupdownloadurl']='';								
		} elseif ($type=='append') {
			if (is_array($oldjobs)) { //generate a new id for new job
				$heighestid=0;
				foreach ($oldjobs as $jobkey => $jobvalue) 
					if ($jobkey>$heighestid) $heighestid=$jobkey;
				$jobid=$heighestid+1;
			} else {
				$jobid=1;
			}
			$oldjobs[$jobid]=$import[$id];
			$oldjobs[$jobid]['activated']=false;
			$oldjobs[$jobid]['cronnextrun']='';
			$oldjobs[$jobid]['starttime']='';
			$oldjobs[$jobid]['logfile']='';
			$oldjobs[$jobid]['lastlogfile']='';
			$oldjobs[$jobid]['lastrun']='';
			$oldjobs[$jobid]['lastruntime']='';
			$oldjobs[$jobid]['lastbackupdownloadurl']='';
		} 
	}
	update_option('backwpup_jobs',$oldjobs);
	_e('Jobs imported!', 'backwpup');
}
?>
</td>
</tr>
</table>

</form>
</div>