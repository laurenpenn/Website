<?PHP
if (!defined('ABSPATH'))
	die();

global $wpdb,$screen_layout_columns;

$dests=explode(',',strtoupper(BACKWPUP_DESTS));

//may be needed to ensure that a special box is always available
add_meta_box('backwpup_jobedit_save', __('Job Type','backwpup'), 'backwpup_jobedit_metabox_save', $current_screen->id, 'side', 'high');
add_meta_box('backwpup_jobedit_schedule', __('Job Schedule','backwpup'), 'backwpup_jobedit_metabox_schedule', $current_screen->id, 'side', 'core');
//all other
add_meta_box('backwpup_jobedit_backupfile', __('Backup File','backwpup'), 'backwpup_jobedit_metabox_backupfile', $current_screen->id, 'side', 'default');
add_meta_box('backwpup_jobedit_sendlog', __('Send log','backwpup'), 'backwpup_jobedit_metabox_sendlog', $current_screen->id, 'side', 'default');
add_meta_box('backwpup_jobedit_destfolder', __('Backup to Folder','backwpup'), 'backwpup_jobedit_metabox_destfolder', $current_screen->id, 'advanced', 'core');
add_meta_box('backwpup_jobedit_destmail', __('Backup to E-Mail','backwpup'), 'backwpup_jobedit_metabox_destmail', $current_screen->id, 'advanced', 'core');
if (in_array('FTP',$dests))
    add_meta_box('backwpup_jobedit_destftp', __('Backup to FTP Server','backwpup'), 'backwpup_jobedit_metabox_destftp', $current_screen->id, 'advanced', 'default');
if (in_array('DROPBOX',$dests))
    add_meta_box('backwpup_jobedit_destdropbox', __('Backup to Dropbox','backwpup'), 'backwpup_jobedit_metabox_destdropbox', $current_screen->id, 'advanced', 'default');
if (in_array('SUGARSYNC',$dests))
    add_meta_box('backwpup_jobedit_destsugarsync', __('Backup to SugarSync','backwpup'), 'backwpup_jobedit_metabox_destsugarsync', $current_screen->id, 'advanced', 'default');
if (in_array('S3',$dests))
    add_meta_box('backwpup_jobedit_dests3', __('Backup to Amazon S3','backwpup'), 'backwpup_jobedit_metabox_dests3', $current_screen->id, 'advanced', 'default');
if (in_array('GSTORAGE',$dests))
    add_meta_box('backwpup_jobedit_destgstorage', __('Backup to Google storage','backwpup'), 'backwpup_jobedit_metabox_destgstorage', $current_screen->id, 'advanced', 'default');
if (in_array('MSAZURE',$dests))
    add_meta_box('backwpup_jobedit_destazure', __('Backup to Micosoft Azure (Blob)','backwpup'), 'backwpup_jobedit_metabox_destazure', $current_screen->id, 'advanced', 'default');
if (in_array('RSC',$dests))
    add_meta_box('backwpup_jobedit_destrsc', __('Backup to Rackspace Cloud','backwpup'), 'backwpup_jobedit_metabox_destrsc', $current_screen->id, 'advanced', 'default');


//get and check job id
if (isset($_REQUEST['jobid']) and !empty($_REQUEST['jobid'])) {
	check_admin_referer('edit-job');
	$jobvalue=backwpup_get_job_vars((int) $_REQUEST['jobid']);
} else {
	$jobvalue=backwpup_get_job_vars();
}
//set extra vars
$todo=explode('+',$jobvalue['type']);
?>
<div class="wrap columns-<?php echo (int) $screen_layout_columns ? (int) $screen_layout_columns : 'auto'; ?>">
<?php
screen_icon();
echo "<h2>".esc_html( __('BackWPup Job Settings', 'backwpup'))."&nbsp;<a href=\"".wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupeditjob', 'edit-job')."\" class=\"add-new-h2\">".esc_html__('Add New','backwpup')."</a></h2>";
?>

<?php if (isset($backwpup_message) and !empty($backwpup_message)) : ?>
<div id="message" class="updated"><p><?php echo $backwpup_message; ?></p></div>
<?php endif; ?>

<form name="editjob" id="editjob" method="post" action="<?PHP echo backwpup_admin_url('admin.php').'?page=backwpupeditjob';?>">
	<input type="hidden" name="jobid" value="<?PHP echo $jobvalue['jobid'];?>" />
	<?php wp_nonce_field('edit-job'); ?>
	<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
	<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
	<?php wp_nonce_field('backwpupeditjob_ajax_nonce', 'backwpupeditjobajaxnonce', false ); ?>
		<div id="poststuff">
		<?php if (function_exists('wp_get_themes')) { ?>
			<div id="post-body" class="metabox-holder columns-<?php echo 1 == $screen_layout_columns ? '1' : '2'; ?>">
				<div id="post-body-content">

					<div id="titlediv">
						<div id="titlewrap">
							<label class="hide-if-no-js" style="visibility:hidden" id="title-prompt-text" for="title"><?php _e( 'Enter Job name here', 'backwpup' ); ?></label>
							<input type="text" name="name" size="30" tabindex="1" value="<?php echo $jobvalue['name'];?>" id="title" autocomplete="off" />
						</div>
					</div>

				</div>

				<div id="postbox-container-1" class="postbox-container">

					<?php do_meta_boxes($current_screen->id, 'side', $jobvalue); ?>

				</div>

				<div id="postbox-container-2" class="postbox-container">

		<?php } else { //for wp version older 3.4?>
			<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
				<div id="side-info-column" class="inner-sidebar">
				<?php do_meta_boxes($current_screen->id, 'side', $jobvalue); ?>
				</div>

				<div id="post-body">
					<div id="post-body-content">

						<div id="titlediv">
							<div id="titlewrap">
								<label class="hide-if-no-js" style="visibility:hidden" id="title-prompt-text" for="title"><?PHP _e('Enter Job name here','backwpup'); ?></label>
								<input type="text" name="name" size="30" tabindex="1" value="<?PHP echo $jobvalue['name'];?>" id="title" autocomplete="off" />
							</div>
						</div>
		<?php } ?>
					<div id="databasejobs" class="stuffbox" <?PHP if (!in_array("CHECK",$todo) and !in_array("DB",$todo) and !in_array("OPTIMIZE",$todo)) echo 'style="display:none;"';?>>
						<h3><label for="dbtables"><?PHP _e('Database Jobs','backwpup'); ?></label></h3>
						<div class="inside">
							<div>
							<b><?PHP _e('Database tables to use:','backwpup'); ?></b>
								<div id="dbtables">
								<?php
								$tables=$wpdb->get_col('SHOW TABLES FROM `'.DB_NAME.'`');
								foreach ($tables as $table) {
									echo '	<input class="checkbox" type="checkbox"'.checked(!in_array($table,$jobvalue['dbexclude']),true,false).' name="jobtabs[]" value="'.base64_encode($table).'"/> '.$table.'<br />';
								}
								?>
								</div>
							</div>
							<span id="dbshortinsert" <?PHP if (!in_array("DB",$todo)) echo 'style="display:none;"';?>><input class="checkbox" type="checkbox"<?php checked($jobvalue['dbshortinsert'],true,true);?> name="dbshortinsert" value="1"/> <?php _e('Use short INSERTs instead of full (with keys)','backwpup');?><br /></span>
							<input class="checkbox" type="checkbox"<?php checked($jobvalue['maintenance'],true,true);?> name="maintenance" value="1"/> <?php _e('Set Blog Maintenance Mode on Database Operations','backwpup');?><br />
						</div>
					</div>

					<div id="filebackup" class="stuffbox" <?PHP if (!in_array("FILE",$todo)) echo 'style="display:none;"';?>>
						<h3><label for="backuproot"><?PHP _e('File Backup','backwpup'); ?></label></h3>
						<div class="inside">
							<b><?PHP _e('Blog Folders to Backup:','backwpup'); ?></b><br />&nbsp;<br />
							<div>
								<div style="width:20%; float: left;">
									&nbsp;<b><input class="checkbox" type="checkbox"<?php checked($jobvalue['backuproot'],true,true);?> name="backuproot" value="1"/> <?php _e('root','backwpup');?></b><br />
									<div style="border-color:#CEE1EF; border-style:solid; border-width:2px; height:10em; width:90%; margin:2px; overflow:auto;">
									<?PHP
									echo '<i>'.__('Exclude:','backwpup').'</i><br />';
									$folder=untrailingslashit(str_replace('\\','/',ABSPATH));
									if ( $dir = @opendir( $folder ) ) {
										while (($file = readdir( $dir ) ) !== false ) {
											if ( !in_array($file, array('.', '..','.svn')) and is_dir($folder.'/'.$file) and !in_array($folder.'/'.$file.'/',backwpup_get_exclude_wp_dirs($folder)))
												echo '<nobr><input class="checkbox" type="checkbox"'.checked(in_array($folder.'/'.$file.'/',$jobvalue['backuprootexcludedirs']),true,false).' name="backuprootexcludedirs[]" value="'.$folder.'/'.$file.'/"/> '.$file.'</nobr><br />';
										}
										@closedir( $dir );
									}
									?>
									</div>
								</div>
								<div style="width:20%;float: left;">
									&nbsp;<b><input class="checkbox" type="checkbox"<?php checked($jobvalue['backupcontent'],true,true);?> name="backupcontent" value="1"/> <?php _e('Content','backwpup');?></b><br />
									<div style="border-color:#CEE1EF; border-style:solid; border-width:2px; height:10em; width:90%; margin:2px; overflow:auto;">
									<?PHP
									echo '<i>'.__('Exclude:','backwpup').'</i><br />';
									$folder=untrailingslashit(str_replace('\\','/',WP_CONTENT_DIR));
									if ( $dir = @opendir( $folder ) ) {
										while (($file = readdir( $dir ) ) !== false ) {
											if ( !in_array($file, array('.', '..','.svn')) and is_dir($folder.'/'.$file) and !in_array($folder.'/'.$file.'/',backwpup_get_exclude_wp_dirs($folder)))
												echo '<nobr><input class="checkbox" type="checkbox"'.checked(in_array($folder.'/'.$file.'/',$jobvalue['backupcontentexcludedirs']),true,false).' name="backupcontentexcludedirs[]" value="'.$folder.'/'.$file.'/"/> '.$file.'</nobr><br />';
										}
										@closedir( $dir );
									}
									?>
									</div>
								</div>
								<div style="width:20%; float: left;">
									&nbsp;<b><input class="checkbox" type="checkbox"<?php checked($jobvalue['backupplugins'],true,true);?> name="backupplugins" value="1"/> <?php _e('Plugins','backwpup');?></b><br />
									<div style="border-color:#CEE1EF; border-style:solid; border-width:2px; height:10em; width:90%; margin:2px; overflow:auto;">
									<?PHP
									echo '<i>'.__('Exclude:','backwpup').'</i><br />';
									$folder=untrailingslashit(str_replace('\\','/',WP_PLUGIN_DIR));
									if ( $dir = @opendir( $folder ) ) {
										while (($file = readdir( $dir ) ) !== false ) {
											if ( !in_array($file, array('.', '..','.svn')) and is_dir($folder.'/'.$file) and !in_array($folder.'/'.$file.'/',backwpup_get_exclude_wp_dirs($folder)))
												echo '<nobr><input class="checkbox" type="checkbox"'.checked(in_array($folder.'/'.$file.'/',$jobvalue['backuppluginsexcludedirs']),true,false).' name="backuppluginsexcludedirs[]" value="'.$folder.'/'.$file.'/"/> '.$file.'</nobr><br />';
										}
										@closedir( $dir );
									}
									?>
									</div>
								</div>
								<div style="width:20%; float: left;">
									&nbsp;<b><input class="checkbox" type="checkbox"<?php checked($jobvalue['backupthemes'],true,true);?> name="backupthemes" value="1"/> <?php _e('Themes','backwpup');?></b><br />
									<div style="border-color:#CEE1EF; border-style:solid; border-width:2px; height:10em; width:90%; margin:2px; overflow:auto;">
									<?PHP
									echo '<i>'.__('Exclude:','backwpup').'</i><br />';
									$folder=untrailingslashit(str_replace('\\','/',trailingslashit(WP_CONTENT_DIR).'themes'));
									if ( $dir = @opendir( $folder ) ) {
										while (($file = readdir( $dir ) ) !== false ) {
											if ( !in_array($file, array('.', '..','.svn')) and is_dir($folder.'/'.$file) and !in_array($folder.'/'.$file.'/',backwpup_get_exclude_wp_dirs($folder)))
												echo '<nobr><input class="checkbox" type="checkbox"'.checked(in_array($folder.'/'.$file.'/',$jobvalue['backupthemesexcludedirs']),true,false).' name="backupthemesexcludedirs[]" value="'.$folder.'/'.$file.'/"/> '.$file.'</nobr><br />';
										}
										@closedir( $dir );
									}
									?>
									</div>
								</div>
								<div style="width:20%; float: left;">
									&nbsp;<b><input class="checkbox" type="checkbox"<?php checked($jobvalue['backupuploads'],true,true);?> name="backupuploads" value="1"/> <?php _e('Blog Uploads','backwpup');?></b><br />
									<div style="border-color:#CEE1EF; border-style:solid; border-width:2px; height:10em; width:90%; margin:2px; overflow:auto;">
									<?PHP
									echo '<i>'.__('Exclude:','backwpup').'</i><br />';
									$folder=untrailingslashit(backwpup_get_upload_dir());
									if ( $dir = @opendir( $folder ) ) {
										while (($file = readdir( $dir ) ) !== false ) {
											if ( !in_array($file, array('.', '..','.svn')) and is_dir($folder.'/'.$file) and !in_array($folder.'/'.$file,backwpup_get_exclude_wp_dirs($folder)))
												echo '<nobr><input class="checkbox" type="checkbox"'.checked(in_array($folder.'/'.$file.'/',$jobvalue['backupuploadsexcludedirs']),true,false).' name="backupuploadsexcludedirs[]" value="'.$folder.'/'.$file.'/"/> '.$file.'</nobr><br />';
										}
										@closedir( $dir );
									}
									?>
									</div>
								</div>
							</div>
							<br />&nbsp;<br />
							<b><?PHP _e('Include Folders to Backup:','backwpup'); ?></b><br />
							<?PHP _e('Example:','backwpup'); ?> <?PHP echo str_replace('\\','/',ABSPATH); ?>,...<br />
							<input name="dirinclude" id="dirinclude" type="text" value="<?PHP echo $jobvalue['dirinclude'];?>" class="large-text" /><br />
							<br />
							<b><?PHP _e('Exclude Files/Folders from Backup:','backwpup'); ?></b><br />
							<?PHP _e('Example:','backwpup'); ?> /logs/,.log,.tmp,/temp/,....<br />
							<input name="fileexclude" id="fileexclude" type="text" value="<?PHP echo $jobvalue['fileexclude'];?>" class="large-text" /><br />
						</div>
					</div>


					<?php do_meta_boxes( $current_screen->id, 'normal', $jobvalue ); ?>

					<?php do_meta_boxes( $current_screen->id, 'advanced', $jobvalue ); ?>

				</div>
			</div>
		</div>

</form>
</div>

<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function($) {
		postboxes.add_postbox_toggles('<?php echo $current_screen->id; ?>');
	});
	//]]>
</script>