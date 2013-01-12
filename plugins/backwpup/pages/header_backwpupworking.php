<?PHP
if (!defined('ABSPATH')) 
	die();

nocache_headers(); //no chache

if (isset($_GET['action']) and $_GET['action']=='runnow' and !empty($_GET['jobid'])) {
	$jobid = (int) $_GET['jobid'];
	check_admin_referer('runnow-job_'.$jobid);
	if ($infile=backwpup_get_working_file()) {
		$backwpup_message=__('A job alredy running!!! Pleace try again if its done.','backwpup');
		$_GET['logfile']=$infile['LOGFILE'];
	} else {
		//include jobstart function
		require_once(dirname(__FILE__).'/../job/job_start.php');
		$_GET['logfile']=backwpup_jobstart($jobid,false);
	}
}
elseif (!empty($_GET['logfile'])) {
	check_admin_referer('view-log_'.basename(trim($_GET['logfile'])));
}
elseif ($infile=backwpup_get_working_file()) {
	$backwpup_message=__('A job is running!!!','backwpup');
	$_GET['logfile']=$infile['LOGFILE'];
}
elseif (!isset($_GET['action']) and !isset($_GET['logfile']) and empty($backwpup_message)) {
	$backwpup_message=__('Nothing...','backwpup');
	$_GET['logfile']='';
}

//add Help
backwpup_contextual_help(__('Here you see working jobs or logfiles','backwpup'));