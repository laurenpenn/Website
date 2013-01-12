<?PHP	
function backwpup_jobstart($jobid='',$cronstart=false) {
	global $wpdb,$wp_version;
	$jobid=(int)trim($jobid);
	if (empty($jobid) or !is_integer($jobid)) {
		return false;
	}
	//check if a job running
	if ($infile=backwpup_get_working_file()) {
		trigger_error(__("A job already running!","backwpup"),E_USER_WARNING);
		return false;
	}

	//clean var
	$backwpup_static = array();
	$backwpup_working = array();
	//get temp dir
	$backwpup_static['TEMPDIR']=backwpup_get_temp();
	if (!is_writable($backwpup_static['TEMPDIR'])) {
		trigger_error(__("Temp dir not writeable","backwpup"),E_USER_ERROR);
		return false;
	} else {  //clean up old temp files
		if ($dir = opendir($backwpup_static['TEMPDIR'])) {
			while (($file = readdir($dir)) !== false) {
				if (is_readable($backwpup_static['TEMPDIR'].$file) and is_file($backwpup_static['TEMPDIR'].$file)) {
					if ($file!='.' and $file!='..') {
						unlink($backwpup_static['TEMPDIR'].$file);
					}
				}
			}
			closedir($dir);
		}
		//create .htaccess for apache and index.php for folder security
		if (!is_file($backwpup_static['TEMPDIR'].'.htaccess')) 
			file_put_contents($backwpup_static['TEMPDIR'].'.htaccess',"<Files \"*\">\n<IfModule mod_access.c>\nDeny from all\n</IfModule>\n<IfModule !mod_access_compat>\n<IfModule mod_authz_host.c>\nDeny from all\n</IfModule>\n</IfModule>\n<IfModule mod_access_compat>\nDeny from all\n</IfModule>\n</Files>");
		if (!is_file($backwpup_static['TEMPDIR'].'index.php')) 			
			file_put_contents($backwpup_static['TEMPDIR'].'index.php',"\n");	
	}	
	//Write running file to prevent dobble runnging
	file_put_contents($backwpup_static['TEMPDIR'].'.running',serialize(array('timestamp'=>time(),'JOBID'=>$jobid,'LOGFILE'=>'','STEPSPERSENT'=>0,'STEPPERSENT'=>0,'WORKING'=>array('PID'=>0))));

	//Set needed WP vars
	$backwpup_static['WP']['DB_NAME']=DB_NAME;
	$backwpup_static['WP']['DB_USER']=DB_USER;
	$backwpup_static['WP']['DB_PASSWORD']=DB_PASSWORD;
	$backwpup_static['WP']['DB_HOST']=DB_HOST;
	$backwpup_static['WP']['DB_CHARSET']=DB_CHARSET;
	$backwpup_static['WP']['DB_COLLATE']=DB_COLLATE;
	$backwpup_static['WP']['OPTIONS_TABLE']=$wpdb->options;
	$backwpup_static['WP']['TABLE_PREFIX']=$wpdb->prefix;
	$backwpup_static['WP']['BLOGNAME']=get_bloginfo('name');
	if (defined('WP_SITEURL'))
		$backwpup_static['WP']['SITEURL']=trailingslashit(WP_SITEURL);
	else
		$backwpup_static['WP']['SITEURL']=trailingslashit(get_option('siteurl'));
	$backwpup_static['WP']['TIMEDIFF']=get_option('gmt_offset')*3600;
	$backwpup_static['WP']['WPLANG']=WPLANG;
	$backwpup_static['WP']['VERSION']=$wp_version;
	$backwpup_static['WP']['CHARSET']=get_option('blog_charset');
	$backwpup_static['WP']['MEMORY_LIMIT']=WP_MEMORY_LIMIT;
	if (defined('ALTERNATE_WP_CRON'))
		$backwpup_static['WP']['ALTERNATE_CRON']=ALTERNATE_WP_CRON;
	else
		$backwpup_static['WP']['ALTERNATE_CRON']=false;
	//WP folder
	$backwpup_static['WP']['ABSPATH']=rtrim(str_replace('\\','/',ABSPATH),'/').'/';
	$backwpup_static['WP']['WP_CONTENT_DIR']=rtrim(str_replace('\\','/',WP_CONTENT_DIR),'/').'/';
	$backwpup_static['WP']['WP_PLUGIN_DIR']=rtrim(str_replace('\\','/',WP_PLUGIN_DIR),'/').'/';
	$backwpup_static['WP']['WP_THEMES_DIR']=rtrim(str_replace('\\','/',trailingslashit(WP_CONTENT_DIR).'themes/'),'/').'/';
	$backwpup_static['WP']['WP_UPLOAD_DIR']=backwpup_get_upload_dir();
	$backwpup_static['WP']['WPINC']=WPINC;
	$backwpup_static['WP']['MULTISITE']=is_multisite();
	$backwpup_static['WP']['ADMINURL']=backwpup_admin_url('admin.php');
	//Set plugin data
	$backwpup_static['BACKWPUP']['PLUGIN_BASEDIR']=BACKWPUP_PLUGIN_BASEDIR;
	$backwpup_static['BACKWPUP']['VERSION']=BACKWPUP_VERSION;
	$backwpup_static['BACKWPUP']['BACKWPUP_DESTS']=BACKWPUP_DESTS;
	//Set config data
	$backwpup_static['CFG']=get_option('backwpup');
	//check exists gzip functions
	if(!function_exists('gzopen'))
		$backwpup_static['CFG']['gzlogs']=false;
	if(!class_exists('ZipArchive'))
		$backwpup_static['CFG']['phpzip']=false;
	//Check working times
	if (empty($backwpup_static['CFG']['jobstepretry']) or !is_int($backwpup_static['CFG']['jobstepretry']) or $backwpup_static['CFG']['jobstepretry']>100)
		$backwpup_static['CFG']['jobstepretry']=3;
	if (empty($backwpup_static['CFG']['jobscriptretry']) or !is_int($backwpup_static['CFG']['jobscriptretry']) or $backwpup_static['CFG']['jobscriptretry']>100)
		$backwpup_static['CFG']['jobscriptretry']=5;
	if (empty($backwpup_static['CFG']['jobscriptruntime']) or !is_int($backwpup_static['CFG']['jobscriptruntime']) or $backwpup_static['CFG']['jobscriptruntime']>100)
		$backwpup_static['CFG']['jobscriptruntime']=ini_get('max_execution_time');
	if (empty($backwpup_static['CFG']['jobscriptruntimelong']) or !is_int($backwpup_static['CFG']['jobscriptruntimelong']) or $backwpup_static['CFG']['jobscriptruntimelong']>1000)
		$backwpup_static['CFG']['jobscriptruntimelong']=300;
	//Set job data
	$backwpup_static['JOB']=backwpup_get_job_vars($jobid);
	//STATIC data
	$backwpup_static['JOBRUNURL']=BACKWPUP_PLUGIN_BASEURL.'/job/job_run.php';
	//Setup Logs dir
	$backwpup_static['CFG']['dirlogs']=rtrim(str_replace('\\','/',$backwpup_static['CFG']['dirlogs']),'/').'/'; 
	if (!is_dir($backwpup_static['CFG']['dirlogs'])) {
		if (!mkdir(rtrim($backwpup_static['CFG']['dirlogs'],'/'),0777,true)) {
			trigger_error(printf(__('Can not create folder for log files: %s','backwpup'),$backwpup_static['CFG']['dirlogs']),E_USER_ERROR);
			return false;
		}
	}
	//create .htaccess for apache and index.php for folder security
	if (!is_file($backwpup_static['CFG']['dirlogs'].'.htaccess')) 
		file_put_contents($backwpup_static['CFG']['dirlogs'].'.htaccess',"<Files \"*\">\n<IfModule mod_access.c>\nDeny from all\n</IfModule>\n<IfModule !mod_access_compat>\n<IfModule mod_authz_host.c>\nDeny from all\n</IfModule>\n</IfModule>\n<IfModule mod_access_compat>\nDeny from all\n</IfModule>\n</Files>"); 
	if (!is_file($backwpup_static['CFG']['dirlogs'].'index.php'))		
		file_put_contents($backwpup_static['CFG']['dirlogs'].'index.php',"\n");		
	
	if (!is_writable($backwpup_static['CFG']['dirlogs'])) {
		trigger_error(__("Log folder not writeable!","backwpup"),E_USER_ERROR);
		return false;
	}
	//set Logfile
	$backwpup_static['LOGFILE']=$backwpup_static['CFG']['dirlogs'].'backwpup_log_'.backwpup_date_i18n('Y-m-d_H-i-s').'.html';
	//create log file
	$fd=fopen($backwpup_static['LOGFILE'],'w');
	//Create log file header
	fwrite($fd,"<html>\n<head>\n");
	fwrite($fd,"<meta name=\"robots\" content=\"noindex, nofollow\" />\n");
	fwrite($fd,"<meta name=\"backwpup_version\" content=\"".BACKWPUP_VERSION."\" />\n");
	fwrite($fd,"<meta name=\"backwpup_logtime\" content=\"".time()."\" />\n");
	fwrite($fd,str_pad("<meta name=\"backwpup_errors\" content=\"0\" />",100)."\n");
	fwrite($fd,str_pad("<meta name=\"backwpup_warnings\" content=\"0\" />",100)."\n");
	fwrite($fd,"<meta name=\"backwpup_jobid\" content=\"".$backwpup_static['JOB']['jobid']."\" />\n");
	fwrite($fd,"<meta name=\"backwpup_jobname\" content=\"".$backwpup_static['JOB']['name']."\" />\n");
	fwrite($fd,"<meta name=\"backwpup_jobtype\" content=\"".$backwpup_static['JOB']['type']."\" />\n");
	fwrite($fd,str_pad("<meta name=\"backwpup_backupfilesize\" content=\"0\" />",100)."\n");
	fwrite($fd,str_pad("<meta name=\"backwpup_jobruntime\" content=\"0\" />",100)."\n");
	fwrite($fd,"<style type=\"text/css\">\n");
	fwrite($fd,".timestamp {background-color:grey;}\n");
	fwrite($fd,".warning {background-color:yellow;}\n");
	fwrite($fd,".error {background-color:red;}\n");
	fwrite($fd,"#body {font-family:monospace;font-size:12px;white-space:nowrap;}\n");
	fwrite($fd,"</style>\n");
	fwrite($fd,"<title>".sprintf(__('BackWPup log for %1$s from %2$s at %3$s','backwpup'),$backwpup_static['JOB']['name'],backwpup_date_i18n(get_option('date_format')),backwpup_date_i18n(get_option('time_format')))."</title>\n</head>\n<body id=\"body\">\n");
	fclose($fd);
	//Set job start settings
	$jobs=get_option('backwpup_jobs');
	$jobs[$backwpup_static['JOB']['jobid']]['starttime']=time(); //set start time for job
	$backwpup_static['JOB']['starttime']=$jobs[$backwpup_static['JOB']['jobid']]['starttime'];
	$jobs[$backwpup_static['JOB']['jobid']]['logfile']=$backwpup_static['LOGFILE'];	   //Set current logfile
	$jobs[$backwpup_static['JOB']['jobid']]['cronnextrun']=backwpup_cron_next($jobs[$backwpup_static['JOB']['jobid']]['cron']);  //set next run
	$backwpup_static['JOB']['cronnextrun']=$jobs[$backwpup_static['JOB']['jobid']]['cronnextrun'];
	$jobs[$backwpup_static['JOB']['jobid']]['lastbackupdownloadurl']='';
	$backwpup_static['JOB']['lastbackupdownloadurl']='';
	update_option('backwpup_jobs',$jobs); //Save job Settings	
	//Set todo
	$backwpup_static['TODO']=explode('+',$backwpup_static['JOB']['type']);
	//only for jos that makes backups
	if (in_array('FILE',$backwpup_static['TODO']) or in_array('DB',$backwpup_static['TODO']) or in_array('WPEXP',$backwpup_static['TODO'])) {
		//make emty file list
		$backwpup_working['FILELIST']=array();
		$backwpup_working['ALLFILESIZE']=0;
		//set Backup Dir if not set
		if (empty($backwpup_static['JOB']['backupdir'])) {
			$backwpup_static['JOB']['backupdir']=$backwpup_static['TEMPDIR'];
		} else {
			//clear path
			$backwpup_static['JOB']['backupdir']=rtrim(str_replace('\\','/',$backwpup_static['JOB']['backupdir']),'/').'/'; 
			//create backup dir if it not exists
			if (!is_dir($backwpup_static['JOB']['backupdir'])) {
				if (!mkdir(rtrim($backwpup_static['JOB']['backupdir'],'/'),0777,true)) {
					trigger_error(sprintf(__('Can not create folder for backups: %1$s','backwpup'),$backwpup_static['JOB']['backupdir']),E_USER_ERROR);
					return false;
				}
			}
			//create .htaccess and index.php for folder security
			if (!is_file($backwpup_static['JOB']['backupdir'].'.htaccess')) 
				file_put_contents($backwpup_static['JOB']['backupdir'].'.htaccess',"<Files \"*\">\n<IfModule mod_access.c>\nDeny from all\n</IfModule>\n<IfModule !mod_access_compat>\n<IfModule mod_authz_host.c>\nDeny from all\n</IfModule>\n</IfModule>\n<IfModule mod_access_compat>\nDeny from all\n</IfModule>\n</Files>");
			if (!is_file($backwpup_static['JOB']['backupdir'].'index.php')) 				
				file_put_contents($backwpup_static['JOB']['backupdir'].'index.php',"\n");				
		}
		//check backup dir				
		if (!is_writable($backwpup_static['JOB']['backupdir'])) {
			trigger_error(__("Backup folder not writeable!","backwpup"),E_USER_ERROR);
			return false;
		}
		//set Backup file Name
		$backwpup_static['backupfile']=$backwpup_static['JOB']['fileprefix'].backwpup_date_i18n('Y-m-d_H-i-s').$backwpup_static['JOB']['fileformart'];
	}
	$backwpup_static['CRONSTART']=$cronstart;
	$backwpup_working['NONCE']=wp_create_nonce('BackWPupJob');
	$backwpup_working['PID']=0;
	$backwpup_working['WARNING']=0;
	$backwpup_working['ERROR']=0;
	$backwpup_working['RESTART']=0;
	$backwpup_working['STEPSDONE']=array();
	$backwpup_working['STEPTODO']=0;
	$backwpup_working['STEPDONE']=0;
	//build working steps
	$backwpup_working['STEPS']=array();
	//setup job steps
	if (in_array('DB',$backwpup_static['TODO']))
		$backwpup_working['STEPS'][]='DB_DUMP';
	if (in_array('WPEXP',$backwpup_static['TODO']))
		$backwpup_working['STEPS'][]='WP_EXPORT';
	if (in_array('FILE',$backwpup_static['TODO']))
		$backwpup_working['STEPS'][]='FILE_LIST';
	if (in_array('DB',$backwpup_static['TODO']) or in_array('WPEXP',$backwpup_static['TODO']) or in_array('FILE',$backwpup_static['TODO'])) {
		$backwpup_working['STEPS'][]='BACKUP_CREATE';		
		//ADD Destinations
		if (!empty($backwpup_static['JOB']['backupdir']) and $backwpup_static['JOB']['backupdir']!='/' and $backwpup_static['JOB']['backupdir']!=$backwpup_static['TEMPDIR'])
			$backwpup_working['STEPS'][]='DEST_FOLDER';
		if (!empty($backwpup_static['JOB']['mailaddress']))
			$backwpup_working['STEPS'][]='DEST_MAIL';	
		if (!empty($backwpup_static['JOB']['ftphost']) and !empty($backwpup_static['JOB']['ftpuser']) and !empty($backwpup_static['JOB']['ftppass']) and in_array('FTP',explode(',',strtoupper(BACKWPUP_DESTS))))
			$backwpup_working['STEPS'][]='DEST_FTP';
		if (!empty($backwpup_static['JOB']['dropetoken']) and !empty($backwpup_static['JOB']['dropesecret']) and in_array('DROPBOX',explode(',',strtoupper(BACKWPUP_DESTS))))
			$backwpup_working['STEPS'][]='DEST_DROPBOX';
		if (!empty($backwpup_static['JOB']['sugarrefreshtoken']) and !empty($backwpup_static['JOB']['sugarroot']) and in_array('SUGARSYNC',explode(',',strtoupper(BACKWPUP_DESTS))))		
			$backwpup_working['STEPS'][]='DEST_SUGARSYNC';
		if (!empty($backwpup_static['JOB']['awsAccessKey']) and !empty($backwpup_static['JOB']['awsSecretKey']) and !empty($backwpup_static['JOB']['awsBucket']) and in_array('S3',explode(',',strtoupper(BACKWPUP_DESTS))))
			$backwpup_working['STEPS'][]='DEST_S3';
		if (!empty($backwpup_static['JOB']['GStorageAccessKey']) and !empty($backwpup_static['JOB']['GStorageSecret']) and !empty($backwpup_static['JOB']['GStorageBucket']) and in_array('GSTORAGE',explode(',',strtoupper(BACKWPUP_DESTS))))
			$backwpup_working['STEPS'][]='DEST_GSTORAGE';
		if (!empty($backwpup_static['JOB']['rscUsername']) and !empty($backwpup_static['JOB']['rscAPIKey']) and !empty($backwpup_static['JOB']['rscContainer']) and in_array('RSC',explode(',',strtoupper(BACKWPUP_DESTS))))
			$backwpup_working['STEPS'][]='DEST_RSC';
		if (!empty($backwpup_static['JOB']['msazureHost']) and !empty($backwpup_static['JOB']['msazureAccName']) and !empty($backwpup_static['JOB']['msazureKey']) and !empty($backwpup_static['JOB']['msazureContainer']) and in_array('MSAZURE',explode(',',strtoupper(BACKWPUP_DESTS))))
			$backwpup_working['STEPS'][]='DEST_MSAZURE';
	}
	if (in_array('CHECK',$backwpup_static['TODO']))
		$backwpup_working['STEPS'][]='DB_CHECK';
	if (in_array('OPTIMIZE',$backwpup_static['TODO']))
		$backwpup_working['STEPS'][]='DB_OPTIMIZE';	
	$backwpup_working['STEPS'][]='JOB_END';
	//mark all as not done
	foreach($backwpup_working['STEPS'] as $step) 
		$backwpup_working[$step]['DONE']=false;
	//write working file
	file_put_contents($backwpup_static['TEMPDIR'].'.running',serialize(array('timestamp'=>time(),'JOBID'=>$backwpup_static['JOB']['jobid'],'LOGFILE'=>$backwpup_static['LOGFILE'],'STEPSPERSENT'=>0,'STEPPERSENT'=>0,'WORKING'=>$backwpup_working)));
	//write static file
	file_put_contents($backwpup_static['TEMPDIR'].'.static',serialize($backwpup_static));
	//Run job
	$httpauthheader='';
	if (!empty($backwpup_static['CFG']['httpauthuser']) and !empty($backwpup_static['CFG']['httpauthpassword']))
		 $httpauthheader=array( 'Authorization' => 'Basic '.base64_encode($backwpup_static['CFG']['httpauthuser'].':'.backwpup_base64($backwpup_static['CFG']['httpauthpassword'])));
	if (!$backwpup_static['WP']['ALTERNATE_CRON'])
		wp_remote_post($backwpup_static['JOBRUNURL'], array('timeout' => 3, 'blocking' => false, 'sslverify' => false, 'headers'=>$httpauthheader ,'body'=>array('nonce'=>$backwpup_working['NONCE'], 'type'=>'start'), 'user-agent'=>'BackWPup'));
	return $backwpup_static['LOGFILE'];
}