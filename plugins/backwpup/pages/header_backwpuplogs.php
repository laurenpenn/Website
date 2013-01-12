<?PHP
if (!defined('ABSPATH')) 
	die();


//Create Table
$backwpup_listtable = new BackWPup_Logs_Table;

//get cuurent action
$doaction = $backwpup_listtable->current_action();
	
if (!empty($doaction)) {
	switch($doaction) {
	case 'delete': 
		$cfg=get_option('backwpup'); //Load Settings
		if (is_array($_GET['logfiles'])) {
			check_admin_referer('bulk-logs');
			$num=0;
			foreach ($_GET['logfiles'] as $logfile) {
				if (is_file($cfg['dirlogs'].'/'.$logfile))
					unlink($cfg['dirlogs'].'/'.$logfile);
				$num++;
			}
		}
		break;
	case 'download': //Download Backup
		check_admin_referer('download-backup_'.basename(trim($_GET['file'])));
		if (is_file(trim($_GET['file']))) {
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment; filename=".basename(trim($_GET['file'])).";");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize(trim($_GET['file'])));
			@readfile(trim($_GET['file']));
			die();
		} else {
			header('HTTP/1.0 404 Not Found');
			die();
		}
		break;
	}
}
//Save per page
if (isset($_POST['screen-options-apply']) and isset($_POST['wp_screen_options']['option']) and isset($_POST['wp_screen_options']['value']) and $_POST['wp_screen_options']['option']=='backwpuplogs_per_page') {
	check_admin_referer( 'screen-options-nonce', 'screenoptionnonce' );
	global $current_user;
	if ($_POST['wp_screen_options']['value']>0 and $_POST['wp_screen_options']['value']<1000) {
		update_user_option($current_user->ID,'backwpuplogs_per_page',(int) $_POST['wp_screen_options']['value']);
		wp_redirect( remove_query_arg( array('pagenum', 'apage', 'paged'), wp_get_referer() ) );
		exit;
	}
}

//add Help
backwpup_contextual_help(__('Here you can manage the log files of the jobs. You can download, view, or delete them.','backwpup'));

add_screen_option( 'per_page', array('label' => __('Logs','backwpup'), 'default' => 20, 'option' =>'backwpuplogs_per_page') );

$backwpup_listtable->prepare_items();