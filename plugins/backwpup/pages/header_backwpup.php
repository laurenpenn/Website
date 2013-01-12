<?PHP
if (!defined('ABSPATH')) 
	die();


//Create Table
$backwpup_listtable = new BackWPup_Jobs_Table;

//get cuurent action
$doaction = $backwpup_listtable->current_action();
	
if (!empty($doaction)) {
	switch($doaction) {
	case 'delete': //Delete Job
		$jobs=get_option('backwpup_jobs');
		if (is_array($_GET['jobs'])) {
			check_admin_referer('bulk-jobs');
			foreach ($_GET['jobs'] as $jobid) {
				unset($jobs[$jobid]);
			}
		}
		//activate/deactivate seduling if not needed
		$activejobs=false;
		foreach ($jobs as $jobid => $jobvalue) {
			if (!empty($jobvalue['activated'])) 
				$activejobs=true;
		}
		if (!$activejobs and false !== wp_next_scheduled('backwpup_cron')) {
			wp_clear_scheduled_hook('backwpup_cron');
		}	
		update_option('backwpup_jobs',$jobs);
		break;
	case 'copy': //Copy Job
		$jobid = (int) $_GET['jobid'];
		check_admin_referer('copy-job_'.$jobid);
		$jobs=get_option('backwpup_jobs');
		//generate new ID
		$heighestid=0;
		foreach ($jobs as $jobkey => $jobvalue) {
			if ($jobkey>$heighestid) $heighestid=$jobkey;
		}
		$newjobid=$heighestid+1;
		$jobs[$newjobid]=$jobs[$jobid];
		$jobs[$newjobid]['name']=__('Copy of','backwpup').' '.$jobs[$newjobid]['name'];
		$jobs[$newjobid]['activated']=false;
		$jobs[$newjobid]['fileprefix']=str_replace($jobid,$newjobid,$jobs[$jobid]['fileprefix']);
		unset($jobs[$newjobid]['logfile']);
		unset($jobs[$newjobid]['starttime']);
		unset($jobs[$newjobid]['lastbackupdownloadurl']);
		unset($jobs[$newjobid]['lastruntime']);
		unset($jobs[$newjobid]['lastrun']);
		update_option('backwpup_jobs',$jobs);
		break;
	case 'export': //Copy Job
		if (is_array($_GET['jobs'])) {
			check_admin_referer('bulk-jobs');
			foreach ($_GET['jobs'] as $jobid) {
				$jobsexport[$jobid]=backwpup_get_job_vars($jobid);
				$jobsexport[$jobid]['activated']=false;
				unset($jobsexport[$jobid]['logfile']);
				unset($jobsexport[$jobid]['starttime']);
				unset($jobsexport[$jobid]['lastbackupdownloadurl']);
				unset($jobsexport[$jobid]['lastruntime']);
				unset($jobsexport[$jobid]['lastrun']);
			}
		}
		$export=serialize($jobsexport);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: text/plain");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=".sanitize_key(get_bloginfo('name'))."_BackWPupExport.txt;");
		header("Content-Transfer-Encoding: 8bit");
		header("Content-Length: ".strlen($export));
		echo $export;
		die();		
		break;
	case 'abort': //Abort Job
		check_admin_referer('abort-job');
		$runningfile=backwpup_get_working_file();
		$tempdir=backwpup_get_temp();
		//clean up temp
		if (is_dir($tempdir)) {
			if ($dir = opendir($tempdir)) {
				while (($file = readdir($dir)) !== false) {
					if (is_readable($tempdir.$file) and is_file($tempdir.$file)) {
						if ($file!='.' and $file!='..') {
							unlink($tempdir.$file);
						}
					}
				}
				closedir($dir);
			}
		}
		clearstatcache();
		if (!empty($runningfile['LOGFILE'])) {
			file_put_contents($runningfile['LOGFILE'], "<span class=\"timestamp\">".backwpup_date_i18n('Y/m/d H:i.s').":</span> <span class=\"error\">[ERROR]".__('Aborted by user!!!','backwpup')."</span><br />\n", FILE_APPEND);
			//write new log header
			$runningfile['WORKING']['ERROR']++;
			$fd=fopen($runningfile['LOGFILE'],'r+');
			while (!feof($fd)) {
				$line=fgets($fd);
				if (stripos($line,"<meta name=\"backwpup_errors\"") !== false) {
					fseek($fd,$filepos);
					fwrite($fd,str_pad("<meta name=\"backwpup_errors\" content=\"".$runningfile['WORKING']['ERROR']."\" />",100)."\n");
					break;
				}
				$filepos=ftell($fd);
			}
			fclose($fd);
		}
		$backwpup_message=__('Job will be terminated.','backwpup').'<br />';
		if (!empty($runningfile['WORKING']['PID']) and function_exists('posix_kill')) {
			if (posix_kill($runningfile['WORKING']['PID'],9))
				$backwpup_message.=__('Process killed with PID:','backwpup').' '.$runningfile['WORKING']['PID'];
			else 
				$backwpup_message.=__('Can\'t kill process with PID:','backwpup').' '.$runningfile['WORKING']['PID'];
		}
		//update job settings
		if (!empty($runningfile['JOBID'])) {
			$jobs=get_option('backwpup_jobs');
			if (isset($newlogfile) and !empty($newlogfile))
				$jobs[$runningfile['JOBID']]['logfile']=$newlogfile;
			$jobs[$runningfile['JOBID']]['lastrun']=$jobs[$runningfile['JOBID']]['starttime'];
			$jobs[$runningfile['JOBID']]['lastruntime']=$runningfile['timestamp']-$jobs[$runningfile['JOBID']]['starttime'];
			update_option('backwpup_jobs',$jobs); //Save Settings
		}
		break;
	}
}

//add Help
backwpup_contextual_help(__('Here is the job overview with some information. You can see some further information of the jobs, how many can be switched with the view button. Also you can manage the jobs or abbort working jobs. Some links are added to have direct access to the last log or download.','backwpup'));

$backwpup_listtable->prepare_items();