<?PHP
//Option menu entry
function backwpup_admin_menu() {
	add_menu_page( __('BackWPup','backwpup'), __('BackWPup','backwpup'), BACKWPUP_USER_CAPABILITY, 'backwpup', 'backwpup_menu_page', BACKWPUP_PLUGIN_BASEURL.'/css/BackWPup16.png');
	$hook = add_submenu_page( 'backwpup', __('Jobs','backwpup'), __('Jobs','backwpup'), BACKWPUP_USER_CAPABILITY, 'backwpup', 'backwpup_menu_page' );
	add_action('load-'.$hook, 'backwpup_menu_page_header');
	$hook = add_submenu_page( 'backwpup', __('Add New','backwpup'), __('Add New','backwpup'), BACKWPUP_USER_CAPABILITY, 'backwpupeditjob', 'backwpup_menu_page' );
	add_action('load-'.$hook, 'backwpup_menu_page_header');
	$hook = add_submenu_page( 'backwpup', __('Working','backwpup'), __('Working','backwpup'), BACKWPUP_USER_CAPABILITY, 'backwpupworking', 'backwpup_menu_page' );
	add_action('load-'.$hook, 'backwpup_menu_page_header',1);
	$hook = add_submenu_page( 'backwpup', __('Logs','backwpup'), __('Logs','backwpup'), BACKWPUP_USER_CAPABILITY, 'backwpuplogs', 'backwpup_menu_page' );
	add_action('load-'.$hook, 'backwpup_menu_page_header');
	$hook = add_submenu_page( 'backwpup', __('Backups','backwpup'), __('Backups','backwpup'), BACKWPUP_USER_CAPABILITY, 'backwpupbackups', 'backwpup_menu_page' );
	add_action('load-'.$hook, 'backwpup_menu_page_header');
	$hook = add_submenu_page( 'backwpup', __('Tools','backwpup'), __('Tools','backwpup'), BACKWPUP_USER_CAPABILITY, 'backwpuptools', 'backwpup_menu_page' );
	add_action('load-'.$hook, 'backwpup_menu_page_header');
	$hook = add_submenu_page( 'backwpup', __('Settings','backwpup'), __('Settings','backwpup'), BACKWPUP_USER_CAPABILITY, 'backwpupsettings', 'backwpup_menu_page' );
	add_action('load-'.$hook, 'backwpup_menu_page_header');
}

function backwpup_menu_page() {
	global $backwpup_message,$backwpup_listtable,$current_screen;
	//check user premessions
	if (!current_user_can(BACKWPUP_USER_CAPABILITY))
		return;
	//Set pages that exists
	$menupages=array('backwpup','backwpupeditjob','backwpupworking','backwpuplogs','backwpupbackups','backwpuptools','backwpupsettings');
	//check called page exists
	if (!empty($_REQUEST['page']) and in_array($_REQUEST['page'],$menupages))
		require_once(dirname(__FILE__).'/pages/page_'.$_REQUEST['page'].'.php');
}

function backwpup_menu_page_header() {
	global $wp_version,$backwpup_message,$backwpup_listtable,$current_screen;
	//check user premessions
	if (!current_user_can(BACKWPUP_USER_CAPABILITY))
		return;
	//Set pages that exists
	$menupages=array('backwpup','backwpupeditjob','backwpupworking','backwpuplogs','backwpupbackups','backwpuptools','backwpupsettings');
	//check called page exists
	$page=$_REQUEST['page'];
	if (!empty($page) and in_array($page,$menupages)) {
		//Css for Admin Section
		if (is_file(dirname(__FILE__).'/css/'.$page.'.css')) {
			if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)
				wp_enqueue_style($page,BACKWPUP_PLUGIN_BASEURL.'/css/'.$page.'.css','',time(),'screen');
			else
				wp_enqueue_style($page,BACKWPUP_PLUGIN_BASEURL.'/css/'.$page.'.css','',BACKWPUP_VERSION,'screen');
		}
		//add java
		if (is_file(dirname(__FILE__).'/js/31'.$page.'.js') and version_compare($wp_version, '3.2', '<')) {
			if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)
				wp_enqueue_script($page,BACKWPUP_PLUGIN_BASEURL.'/js/31'.$page.'.js','',time(),true);
			else
				wp_enqueue_script($page,BACKWPUP_PLUGIN_BASEURL.'/js/31'.$page.'.js','',BACKWPUP_VERSION,true);
		} elseif (is_file(dirname(__FILE__).'/js/'.$page.'.js')) {
			if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)
				wp_enqueue_script($page,BACKWPUP_PLUGIN_BASEURL.'/js/'.$page.'.js','',time(),true);
			else
				wp_enqueue_script($page,BACKWPUP_PLUGIN_BASEURL.'/js/'.$page.'.js','',BACKWPUP_VERSION,true);
		}
		//incude functions
		if (is_file(dirname(__FILE__).'/pages/func_'.$page.'.php'))
			require_once(dirname(__FILE__).'/pages/func_'.$page.'.php');
		//include code
		if (is_file(dirname(__FILE__).'/pages/header_'.$page.'.php'))
			require_once(dirname(__FILE__).'/pages/header_'.$page.'.php');
	}
}

function backwpup_load_ajax() {
	//Set pages that exists
	$menupages=array('backwpup','backwpupeditjob','backwpupworking','backwpuplogs','backwpupbackups','backwpuptools','backwpupsettings');
	if (!empty($_POST['backwpupajaxpage']) and in_array($_POST['backwpupajaxpage'],$menupages)) {
		$page=$_POST['backwpupajaxpage'];
		//incude functions
		if (is_file(dirname(__FILE__).'/pages/func_'.$page.'.php'))
			require_once(dirname(__FILE__).'/pages/func_'.$page.'.php');
	}
}

function backwpup_contextual_help($help='') {
	global $current_screen;
	$help='<p>'.$help.'</p>';

	if ( method_exists( $current_screen, 'add_help_tab' ) ) {
		$current_screen->add_help_tab( array(
			'id'			=> 'plugininfo',
			'title'		 => __( 'Plugin Info', 'backwpup' ),
			'content'	   =>
			'<p><a href="http://backwpup.com" target="_blank">BackWPup</a> v. ' . BACKWPUP_VERSION . ', <a href="http://www.gnu.org/licenses/gpl-3.0" target="_blank">GPLv3</a> &copy 2012-' . date( 'Y' ) . ' <a href="http://inpsyde.com" target="_blank">Inpsyde GmbH</a> '.__( 'Author:','backwpup').' <a href="http://danielhuesken.de" target="_blank">Daniel H&uuml;sken</a></p><p>' . __( 'BackWPup comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under certain conditions.', 'backwpup' ) . '</p>'
		) );		
		$current_screen->add_help_tab( array(
			'id'			=> 'overview',
			'title'		 => __( 'Overview', 'backwpup' ),
			'content'	   => $help
		) );
		$current_screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'backwpup' ) . '</strong></p>' .
					'<p><a href="http://backwpup.com" target="_blank">BackWPup</a></p>' .
					'<p><a href="http://inpsyde.com" target="_blank">Inpsyde GmbH</a></p>' .
					'<p><a href="http://wordpress.org/extend/plugins/backwpup/faq/" target="_blank">'.__('FAQ','backwpup').'</a></p>' .
					'<p><a href="http://wordpress.org/support/plugin/backwpup/" target="_blank"'.__('Support Forums','backwpup').'</a></p>' .
					'<p><a href="https://plus.google.com/112659782148359984250/" target="_blank">'.__('Google+','backwpup').'</a></p>'
		);
		
	} elseif ( function_exists( 'add_contextual_help' ) ) { //for WP < 3.3 help
		add_contextual_help( $current_screen,$help.
				'<p><a href="http://backwpup.de" target="_blank">BackWPup</a> v. ' . BACKWPUP_VERSION . ', <a href="http://www.gnu.org/licenses/gpl-2.0" target="_blank">GPLv2</a> &copy 2009-' . date( 'Y' ) . ' <a href="http://danielhuesken.de" target="_blank">Daniel H&uuml;sken</a></p><p>' . __( 'BackWPup comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under certain conditions.', 'backwpup' ) . '</p>' .
						'<p><strong>' . __( 'For more information:', 'backwpup' ) . '</strong></p><p>' .
						' <a href="http://backwpup.com" target="_blank">BackWPup</a> |' .
						' <a href="http://inpsyde.com" target="_blank">Inpsyde GmbH</a> |' .
						' <a href="http://wordpress.org/extend/plugins/backwpup/faq/" target="_blank">'.__('FAQ', 'backwpup').'</a> |' .
						' <a href="http://wordpress.org/support/plugin/backwpup/" target="_blank">'.__('Support Forums', 'backwpup').'</a> |' .
						' <a href="https://plus.google.com/112659782148359984250/" target="_blank">'.__('Google+', 'backwpup').'</a> ' .
						'</p>'
		);
	}
}

//On activate function
function backwpup_plugin_activate() {
	//Load Settings
	$cfg=get_option('backwpup');
	//Check only run once on update
	if ($cfg['last_activate']==BACKWPUP_VERSION or !is_main_site())
		return;
	else
		$cfg['last_activate']=BACKWPUP_VERSION;
	//check jobs
	$jobs=get_option('backwpup_jobs');
	if (isset($jobs[0]))
		unset($jobs[0]); //Delete old false job
	if (!empty($jobs)) {
		foreach ($jobs as $jobid => $jobvalue) {
			$checktjobs[$jobid]=backwpup_get_job_vars($jobid); //check jobvaules
		}
		//save job values
		update_option('backwpup_jobs',$checktjobs);
	}
	//remove old cron jobs
	wp_clear_scheduled_hook('backwpup_cron');
	//make new schedule round
	wp_schedule_event(time(), 'backwpup_int', 'backwpup_cron');
	//Set settings defaults
	if (empty($cfg['mailsndemail'])) $cfg['mailsndemail']=sanitize_email(get_bloginfo( 'admin_email' ));
	if (empty($cfg['mailsndname'])) $cfg['mailsndname']='BackWPup '.get_bloginfo( 'name' );
	if (empty($cfg['mailmethod'])) $cfg['mailmethod']='mail';
	if (empty($cfg['mailsendmail'])) $cfg['mailsendmail']=substr(ini_get('sendmail_path'),0,strpos(ini_get('sendmail_path'),' -'));
	if (isset($cfg['mailhost']) && false !== strpos($cfg['mailhost'],':'))
		list($cfg['mailhost'],$cfg['mailhostport'])=explode(':',$cfg['mailhost'],2);
	if (!isset($cfg['mailhost'])) $cfg['mailhost']='';
	if (!isset($cfg['mailhostport'])) $cfg['mailhostport']=25;
	if (!isset($cfg['mailsecure'])) $cfg['mailsecure']='';
	if (!isset($cfg['mailuser'])) $cfg['mailuser']='';
	if (!isset($cfg['mailpass'])) $cfg['mailpass']='';
	if (!isset($cfg['showadminbar'])) $cfg['showadminbar']=true;
	if (!isset($cfg['jobstepretry']) or !is_int($cfg['jobstepretry']) or 100<$cfg['jobstepretry'] or empty($cfg['jobstepretry'])) $cfg['jobstepretry']=3;
	if (!isset($cfg['jobscriptretry']) or !is_int($cfg['jobscriptretry']) or 100<$cfg['jobscriptretry'] or empty($cfg['jobscriptretry'])) $cfg['jobscriptretry']=5;
	if (!isset($cfg['maxlogs']) or !is_int($cfg['maxlogs'])) $cfg['maxlogs']=50;
	if (!function_exists('gzopen') or !isset($cfg['gzlogs'])) $cfg['gzlogs']=false;
	if (!class_exists('ZipArchive') or !isset($cfg['phpzip'])) $cfg['phpzip']=false;
	if (!isset($cfg['dirlogs']) or empty($cfg['dirlogs']) or !is_dir($cfg['dirlogs'])) {
		$rand = substr( md5( md5( SECURE_AUTH_KEY ) ), -5 );
		$cfg['dirlogs']=str_replace('\\','/',trailingslashit(WP_CONTENT_DIR)).'backwpup-'.$rand.'-logs/';
	}
	if (!isset($cfg['httpauthuser'])) $cfg['httpauthuser']='';
	if (!isset($cfg['httpauthpassword'])) $cfg['httpauthpassword']='';
	//remove old option
	unset($cfg['dirtemp']);
	unset($cfg['logfilelist']);
	unset($cfg['jobscriptruntime']);
	unset($cfg['jobscriptruntimelong']);
	unset($cfg['apicronservice']);
	unset($cfg['disablewpcron']);
	update_option('backwpup',$cfg);
	//delete not longer used options
	delete_option('backwpup_backups_chache');
	delete_option('backwpup_last_activate');
}

//on Plugin deaktivate
function backwpup_plugin_deactivate() {
	wp_clear_scheduled_hook('backwpup_cron'); //delete cron
	$cfg=get_option('backwpup');
	$cfg['last_activate']=''; //set to not activated
	update_option('backwpup',$cfg);
}

//get temp dir
function backwpup_get_temp() {
	$backwpuptmpfolder=str_replace('\\','/',dirname(__FILE__).'/tmp/');
	return trailingslashit($backwpuptmpfolder);
}
//checks the dir is in openbasedir
function backwpup_check_open_basedir($dir) {
	if (!ini_get('open_basedir'))
		return true;
	$openbasedirarray=explode(PATH_SEPARATOR,ini_get('open_basedir'));
	$dir=rtrim(str_replace('\\','/',$dir),'/').'/';
	if (!empty($openbasedirarray)) {
		foreach ($openbasedirarray as $basedir) {
			if (stripos($dir,rtrim(str_replace('\\','/',$basedir),'/').'/')==0)
				return true;
		}
	}
	return false;
}

//add edit setting to plugins page
function backwpup_plugin_options_link($links) {
	if (!current_user_can(BACKWPUP_USER_CAPABILITY))
		return $links;
	$settings_link='<a href="'.backwpup_admin_url('admin.php').'?page=backwpup" title="' . __('Go to Settings Page','backwpup') . '" class="edit">' . __('Settings','backwpup') . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

//add links on plugins page
function backwpup_plugin_links($links, $file) {
	if (!current_user_can('install_plugins'))
		return $links;
	if ($file == BACKWPUP_PLUGIN_BASEDIR.'/backwpup.php') {
		$links[] = '<a href="http://inpsyde.com" target="_blank">Inpsyde GmbH</a>';
		$links[] = '<a href="http://wordpress.org/extend/plugins/backwpup/faq/" target="_blank">' . __('FAQ','backwpup') . '</a>';
		$links[] = '<a href="http://wordpress.org/support/plugin/backwpup/" target="_blank">' . __('Support','backwpup') . '</a>';
	}
	return $links;
}

//Add cron interval
function backwpup_intervals($schedules) {
	$intervals['backwpup_int']=array('interval' => '60', 'display' => __('BackWPup', 'backwpup'));
	$schedules=array_merge($intervals,$schedules);
	return $schedules;
}

//
function backwpup_date_i18n( $dateformatstring, $unixtimestamp = false, $gmt = false ) {
	if ($unixtimestamp)
		$unixtimestamp=$unixtimestamp+get_option('gmt_offset')*3600;
	return date_i18n( $dateformatstring, $unixtimestamp, $gmt);
}

//cron work
function backwpup_cron() {
	if (is_file(backwpup_get_temp().'.running')) {
		$cfg=get_option('backwpup');
		$revtime=time()-600; //10 min no progress.
		$infile=backwpup_get_working_file();
		$httpauthheader='';
		if (!empty($cfg['httpauthuser']) and !empty($cfg['httpauthpassword']))
			$httpauthheader=array( 'Authorization' => 'Basic '.base64_encode($cfg['httpauthuser'].':'.backwpup_base64($cfg['httpauthpassword'])));
		if (!empty($infile['timestamp']) and $infile['timestamp']<$revtime) {
			wp_remote_post(BACKWPUP_PLUGIN_BASEURL.'/job/job_run.php', array('timeout' => 3, 'blocking' => false, 'sslverify' => false,'headers'=>$httpauthheader, 'body'=>array('nonce'=> $infile['WORKING']['NONCE'],'type'=>'restarttime'), 'user-agent'=>'BackWPup') );
		}
	} else {
		$jobs=get_option('backwpup_jobs');
		if (!empty($jobs)) {
			foreach ($jobs as $jobid => $jobvalue) {
				if (!isset($jobvalue['activated']) or !$jobvalue['activated'])
					continue;
				if ($jobvalue['cronnextrun']<=current_time('timestamp')) {
					require_once(dirname(__FILE__).'/job/job_start.php');
					backwpup_jobstart($jobid,true);
				}
			}
		}
	}
}

//file size
function backwpup_formatBytes($bytes, $precision = 2) {
	$units = array('B', 'KB', 'MB', 'GB', 'TB');
	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);
	$bytes /= pow(1024, $pow);
	return round($bytes, $precision) . ' ' . $units[$pow];
}

//echo long backup type name
function backwpup_backup_types($type='',$echo=false) {
	$typename='';
	if (!empty($type)) {
		$todo=explode('+',$type);
		foreach($todo as $key => $value) {
			switch($value) {
			case 'WPEXP':
				$typename.=__('WP XML Export','backwpup')."<br />";
				break;
			case 'FILE':
				$typename.=__('File Backup','backwpup')."<br />";
				break;
			case 'DB':
				$typename.=__('Database Backup','backwpup')."<br />";
				break;
			case 'OPTIMIZE':
				$typename.=__('Optimize Database Tables','backwpup')."<br />";
				break;
			case 'CHECK':
				$typename.=__('Check Database Tables','backwpup')."<br />";
				break;
			}
		}
	} else {
		$typename=array('WPEXP','DB','FILE','OPTIMIZE','CHECK');
	}

	if ($echo)
		echo $typename;
	else
		return $typename;
}

//read log file header
function backwpup_read_logheader($logfile) {
	$headers=array("backwpup_version" => "version","backwpup_logtime" => "logtime","backwpup_errors" => "errors","backwpup_warnings" => "warnings","backwpup_jobid" => "jobid","backwpup_jobname" => "name","backwpup_jobtype" => "type","backwpup_jobruntime" => "runtime","backwpup_backupfilesize" => "backupfilesize");
	if (!is_readable($logfile))
		return false;
	//Read file
	if (strtolower(substr($logfile,-3))==".gz") {
		$fp = gzopen( $logfile, 'r' );
		$file_data = gzread( $fp, 1536 ); // Pull only the first 1,5kiB of the file in.
		gzclose( $fp );
	} else {
		$fp = fopen( $logfile, 'r' );
		$file_data = fread( $fp, 1536 ); // Pull only the first 1,5kiB of the file in.
		fclose( $fp );
	}
	//get data form file
	foreach ($headers as $keyword => $field) {
		preg_match('/(<meta name="'.$keyword.'" content="(.*)" \/>)/i',$file_data,$content);
		if (!empty($content))
			$joddata[$field]=$content[2];
		else
			$joddata[$field]='';
	}
	if (empty($joddata['logtime']))
		$joddata['logtime']=filectime($logfile);
	return $joddata;
}

//Dashboard widget for Logs
function backwpup_dashboard_logs() {
	$cfg=get_option('backwpup');
	$widgets = get_option( 'dashboard_widget_options' );
	if (!isset($widgets['backwpup_dashboard_logs']) or $widgets['backwpup_dashboard_logs']<1 or $widgets['backwpup_dashboard_logs']>20)
		$widgets['backwpup_dashboard_logs'] =5;
	//get log files
	$logfiles=array();
	if ( $dir = @opendir( $cfg['dirlogs'] ) ) {
		while (($file = readdir( $dir ) ) !== false ) {
			if (is_file($cfg['dirlogs'].'/'.$file) and 'backwpup_log_' == substr($file,0,strlen('backwpup_log_')) and  ('.html' == substr($file,-5) or '.html.gz' == substr($file,-8)))
				$logfiles[]=$file;
		}
		closedir( $dir );
		rsort($logfiles);
	}
	echo '<ul>';
	if (count($logfiles)>0) {
		$count=0;
		foreach ($logfiles as $logfile) {
			$logdata=backwpup_read_logheader($cfg['dirlogs'].'/'.$logfile);
			echo '<li>';
			echo '<span>'.backwpup_date_i18n(get_option('date_format').' @ '.get_option('time_format'),$logdata['logtime']).'</span> ';
			echo '<a href="'.wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupworking&logfile='.$cfg['dirlogs'].'/'.$logfile, 'view-log_'.$logfile).'" title="'.__('View Log:','backwpup').' '.basename($logfile).'">'.$logdata['name'].'</i></a>';
			if ($logdata['errors']>0)
				printf(' <span style="color:red;font-weight:bold;">'._n("%d ERROR", "%d ERRORS", $logdata['errors'],'backwpup').'</span>', $logdata['errors']);
			if ($logdata['warnings']>0)
				printf(' <span style="color:#e66f00;font-weight:bold;">'._n("%d WARNING", "%d WARNINGS", $logdata['warnings'],'backwpup').'</span>', $logdata['warnings']);
			if($logdata['errors']==0 and $logdata['warnings']==0)
				echo ' <span style="color:green;font-weight:bold;">'.__('O.K.','backwpup').'</span>';
			echo '</li>';
			$count++;
			if ($count>=$widgets['backwpup_dashboard_logs'])
				break;
		}
		echo '</ul>';
	} else {
		echo '<i>'.__('none','backwpup').'</i>';
	}
}

//Dashboard widget for Logs config
function backwpup_dashboard_logs_config() {
	if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
		$widget_options = array();

	if ( !isset($widget_options['backwpup_dashboard_logs']) )
		$widget_options['backwpup_dashboard_logs'] = 5;

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['backwpup_dashboard_logs']) ) {
		$number = absint( $_POST['backwpup_dashboard_logs'] );
		$widget_options['backwpup_dashboard_logs'] = $number;
		update_option( 'dashboard_widget_options', $widget_options );
	}

	echo '<p><label for="backwpup-logs">'.__('How many of the lastes logs would you like to display?','backwpup').'</label>';
	echo '<select id="backwpup-logs" name="backwpup_dashboard_logs">';
	for ($i=0;$i<=20;$i++)
		echo '<option value="'.$i.'" '.selected($i,$widget_options['backwpup_dashboard_logs']).'>'.$i.'</option>';
	echo '</select>';

}

//Dashboard widget for Jobs
function backwpup_dashboard_activejobs() {
	$jobs=get_option('backwpup_jobs');
	if (empty($jobs)) {
		echo '<ul><li><i>'.__('none','backwpup').'</i></li></ul>';
		return;
	}
	$runningfile=backwpup_get_working_file();
	$tmp = Array();
	foreach($jobs as &$ma)
		$tmp[] = &$ma["cronnextrun"];
	array_multisort($tmp, SORT_DESC, $jobs);
	$count=0;
	echo '<ul>';
	foreach ($jobs as $jobid => $jobvalue) {
		if (!empty($runningfile['JOBID']) and $runningfile['JOBID']==$jobvalue['jobid']) {
			$runtime=time()-$jobvalue['starttime'];
			echo '<li><span style="font-weight:bold;">'.$jobvalue['jobid'].'. '.$jobvalue['name'].': </span>';
			printf('<span style="color:#e66f00;">'.__('working since %d sec.','backwpup').'</span>',$runtime);
			echo " <a style=\"color:green;\" href=\"" . backwpup_admin_url('admin.php').'?page=backwpupworking' . "\">" . __('View!','backwpup') . "</a>";
			echo " <a style=\"color:red;\" href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpup&action=abort', 'abort-job') . "\">" . __('Abort!','backwpup') . "</a>";
			echo "</li>";
			$count++;
		} elseif ($jobvalue['activated']) {
			echo '<li><span>'.date_i18n(get_option('date_format'),$jobvalue['cronnextrun']).' @ '.date_i18n(get_option('time_format'),$jobvalue['cronnextrun']).'</span>';
			echo ' <a href="'.wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupeditjob&jobid='.$jobvalue['jobid'], 'edit-job').'" title="'.__('Edit Job','backwpup').'">'.$jobvalue['name'].'</a><br />';
			echo "</li>";
			$count++;
		}
	}

	if ($count==0)
		echo '<li><i>'.__('none','backwpup').'</i></li>';
	echo '</ul>';
}

//add dashboard widget
function backwpup_add_dashboard() {
	if (!current_user_can(BACKWPUP_USER_CAPABILITY))
		return;
	wp_add_dashboard_widget( 'backwpup_dashboard_widget_logs', __('BackWPup Logs','backwpup'), 'backwpup_dashboard_logs' , 'backwpup_dashboard_logs_config');
	wp_add_dashboard_widget( 'backwpup_dashboard_widget_activejobs', __('BackWPup Active Jobs','backwpup'), 'backwpup_dashboard_activejobs' );
}

//add admin bar menu
function backwpup_add_adminbar() {
	global $wp_admin_bar;
	$cfg=get_option('backwpup'); //Load Settings
	if (!$cfg['showadminbar'] || !current_user_can(BACKWPUP_USER_CAPABILITY) || !is_super_admin() || !is_admin_bar_showing())
		return;
    /* Add the main siteadmin menu item */
    $wp_admin_bar->add_menu(array( 'id' => 'backwpup', 'title' => __( 'BackWPup', 'backwpup' ), 'href' => backwpup_admin_url('admin.php').'?page=backwpup'));
	if (backwpup_get_working_file())
		$wp_admin_bar->add_menu(array( 'id' => 'backwpup_working','parent' => 'backwpup', 'title' => __('See Working!','backwpup'), 'href' => backwpup_admin_url('admin.php').'?page=backwpupworking'));
    $wp_admin_bar->add_menu(array( 'id' => 'backwpup_jobs', 'parent' => 'backwpup', 'title' => __('Jobs','backwpup'), 'href' => backwpup_admin_url('admin.php').'?page=backwpup'));
	$wp_admin_bar->add_menu(array( 'id' => 'backwpup_logs', 'parent' => 'backwpup', 'title' => __('Logs','backwpup'), 'href' => backwpup_admin_url('admin.php').'?page=backwpuplogs'));
	$wp_admin_bar->add_menu(array( 'id' => 'backwpup_backups', 'parent' => 'backwpup', 'title' => __('Backups','backwpup'), 'href' => backwpup_admin_url('admin.php').'?page=backwpupbackups'));
	$wp_admin_bar->add_menu(array( 'id' => 'backwpup_newjob', 'parent' => 'new-content', 'title' => __('BackWPup Job','backwpup'), 'href' => backwpup_admin_url('admin.php').'?page=backwpupeditjob'));
}

function backwpup_get_upload_dir() {
	$upload_path = get_option('upload_path');
	$upload_path = trim($upload_path);
	if ( empty($upload_path) ) {
		$dir = WP_CONTENT_DIR . '/uploads';
	} else {
		$dir = $upload_path;
		if ( 'wp-content/uploads' == $upload_path ) {
			$dir = WP_CONTENT_DIR . '/uploads';
		} elseif ( 0 !== strpos($dir, ABSPATH) ) {
			// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
			$dir = path_join( ABSPATH, $dir );
		}
	}
	if (defined('UPLOADS') && !is_multisite()) {
		$dir = ABSPATH . UPLOADS;
	}
	if (is_multisite()) {
			$dir = untrailingslashit(WP_CONTENT_DIR).'/blogs.dir';
	}
	return str_replace('\\','/',trailingslashit($dir));
}

function backwpup_get_exclude_wp_dirs($folder) {
	$cfg=get_option('backwpup'); //Load Settings
	$folder=trailingslashit(str_replace('\\','/',$folder));
	$excludedir=array();
	$excludedir[]=backwpup_get_temp(); //exclude temp
	$excludedir[]=rtrim(str_replace('\\','/',$cfg['dirlogs']),'/').'/'; //exclude logfiles
	if (false !== strpos(trailingslashit(str_replace('\\','/',ABSPATH)),$folder) and trailingslashit(str_replace('\\','/',ABSPATH))!=$folder)
		$excludedir[]=trailingslashit(str_replace('\\','/',ABSPATH));
	if (false !== strpos(trailingslashit(str_replace('\\','/',WP_CONTENT_DIR)),$folder) and trailingslashit(str_replace('\\','/',WP_CONTENT_DIR))!=$folder)
		$excludedir[]=trailingslashit(str_replace('\\','/',WP_CONTENT_DIR));
	if (false !== strpos(trailingslashit(str_replace('\\','/',WP_PLUGIN_DIR)),$folder) and trailingslashit(str_replace('\\','/',WP_PLUGIN_DIR))!=$folder)
		$excludedir[]=trailingslashit(str_replace('\\','/',WP_PLUGIN_DIR));
	if (false !== strpos(str_replace('\\','/',trailingslashit(WP_CONTENT_DIR).'themes/'),$folder) and str_replace('\\','/',trailingslashit(WP_CONTENT_DIR).'themes/')!=$folder)
		$excludedir[]=str_replace('\\','/',trailingslashit(WP_CONTENT_DIR).'themes/');
	if (false !== strpos(backwpup_get_upload_dir(),$folder) and backwpup_get_upload_dir()!=$folder)
		$excludedir[]=backwpup_get_upload_dir();
	//Exclude Backup dirs
	$jobs=get_option('backwpup_jobs');
	if (!empty($jobs)) {
		foreach($jobs as $jobsvale) {
			if (!empty($jobsvale['backupdir']) and $jobsvale['backupdir']!='/')
				$excludedir[]=$jobsvale['backupdir'];
		}
	}
	return $excludedir;
}

//Calcs next run for a cron string as timestamp
function backwpup_cron_next($cronstring) {
	//Cronstring zerlegen
	list($cronstr['minutes'],$cronstr['hours'],$cronstr['mday'],$cronstr['mon'],$cronstr['wday'])=explode(' ',$cronstring,5);

	//make arrys form string
	foreach ($cronstr as $key => $value) {
		if (strstr($value,','))
			$cronarray[$key]=explode(',',$value);
		else
			$cronarray[$key]=array(0=>$value);
	}
	//make arrys complete with ranges and steps
	foreach ($cronarray as $cronarraykey => $cronarrayvalue) {
		$cron[$cronarraykey]=array();
		foreach ($cronarrayvalue as $key => $value) {
			//steps
			$step=1;
			if (strstr($value,'/'))
				list($value,$step)=explode('/',$value,2);
			//replase weekday 7 with 0 for sundays
			if ($cronarraykey=='wday')
				$value=str_replace('7','0',$value);
			//ranges
			if (strstr($value,'-')) {
				list($first,$last)=explode('-',$value,2);
				if (!is_numeric($first) or !is_numeric($last) or $last>60 or $first>60) //check
					return 2147483647;
				if ($cronarraykey=='minutes' and $step<5)  //set step ninimum to 5 min.
					$step=5;
				$range=array();
				for ($i=$first;$i<=$last;$i=$i+$step)
					$range[]=$i;
				$cron[$cronarraykey]=array_merge($cron[$cronarraykey],$range);
			} elseif ($value=='*') {
				$range=array();
				if ($cronarraykey=='minutes') {
					if ($step<5) //set step ninimum to 5 min.
						$step=5;
					for ($i=0;$i<=59;$i=$i+$step)
						$range[]=$i;
				}
				if ($cronarraykey=='hours') {
					for ($i=0;$i<=23;$i=$i+$step)
						$range[]=$i;
				}
				if ($cronarraykey=='mday') {
					for ($i=$step;$i<=31;$i=$i+$step)
						$range[]=$i;
				}
				if ($cronarraykey=='mon') {
					for ($i=$step;$i<=12;$i=$i+$step)
						$range[]=$i;
				}
				if ($cronarraykey=='wday') {
					for ($i=0;$i<=6;$i=$i+$step)
						$range[]=$i;
				}
				$cron[$cronarraykey]=array_merge($cron[$cronarraykey],$range);
			} else {
				//Month names
				if (strtolower($value)=='jan')
					$value=1;
				if (strtolower($value)=='feb')
					$value=2;
				if (strtolower($value)=='mar')
					$value=3;
				if (strtolower($value)=='apr')
					$value=4;
				if (strtolower($value)=='may')
					$value=5;
				if (strtolower($value)=='jun')
					$value=6;
				if (strtolower($value)=='jul')
					$value=7;
				if (strtolower($value)=='aug')
					$value=8;
				if (strtolower($value)=='sep')
					$value=9;
				if (strtolower($value)=='oct')
					$value=10;
				if (strtolower($value)=='nov')
					$value=11;
				if (strtolower($value)=='dec')
					$value=12;
				//Week Day names
				if (strtolower($value)=='sun')
					$value=0;
				if (strtolower($value)=='sat')
					$value=6;
				if (strtolower($value)=='mon')
					$value=1;
				if (strtolower($value)=='tue')
					$value=2;
				if (strtolower($value)=='wed')
					$value=3;
				if (strtolower($value)=='thu')
					$value=4;
				if (strtolower($value)=='fri')
					$value=5;
				if (!is_numeric($value) or $value>60) //check
					return 2147483647;
				$cron[$cronarraykey]=array_merge($cron[$cronarraykey],array(0=>$value));
			}
		}
	}
	//generate next 10 years
	for ($i=date('Y');$i<2038;$i++)
		$cron['year'][]=$i;

	//calc next timestamp
	$currenttime=current_time('timestamp');
	foreach ($cron['year'] as $year) {
		foreach ($cron['mon'] as $mon) {
			foreach ($cron['mday'] as $mday) {
				foreach ($cron['hours'] as $hours) {
					foreach ($cron['minutes'] as $minutes) {
						$timestamp=mktime($hours,$minutes,0,$mon,$mday,$year);
						if ($timestamp and in_array(date('j',$timestamp),$cron['mday']) and in_array(date('w',$timestamp),$cron['wday']) and $timestamp>$currenttime) {
							return $timestamp;
						}
					}
				}
			}
		}
	}
	return 2147483647;
}

function backwpup_get_working_file() {
	$tempdir=backwpup_get_temp();
	if (is_file($tempdir.'.running')) {
		if ($runningfile=file_get_contents($tempdir.'.running'))
			return unserialize(trim($runningfile));
		else
			return false;
	} else {
		return false;
	}
}

function backwpup_admin_url($url) {
	if (is_multisite()) {
		if  (is_super_admin())
			return network_admin_url($url);
	} else {
		return admin_url($url);
	}
}

function backwpup_base64($data) {
	if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data))
		$data=base64_decode($data);
	return $data;
}

function backwpup_env_checks() {
	global $wp_version,$backwpup_admin_message;
	$message='';
	$checks=true;
	$cfg=get_option('backwpup');
	if (version_compare($wp_version, BACKWPUP_MIN_WORDPRESS_VERSION, '<')) { // check WP Version
		$message.=str_replace('%d',BACKWPUP_MIN_WORDPRESS_VERSION,__('- WordPress %d or higher is needed!','backwpup')) . '<br />';
		$checks=false;
	}
	if (version_compare(phpversion(), '5.2.4', '<')) { // check PHP Version
		$message.=__('- PHP 5.2.4 or higher is needed!','backwpup') . '<br />';
		$checks=false;
	}
	if (!is_dir(backwpup_get_temp())) { // create logs folder if it not exists
		@mkdir(untrailingslashit(backwpup_get_temp()),0777,true);
	}
	if (!is_dir(backwpup_get_temp())) { // check logs folder
		$message.=sprintf(__("- Temp folder '%s' does not exists!",'backwpup'),backwpup_get_temp()).'<br />';
	}
	if (!is_writable(backwpup_get_temp())) { // check logs folder
		$message.=sprintf(__("- Temp folder '%s' is not writeable!",'backwpup'),backwpup_get_temp()).'<br />';
	}
	if (!empty($cfg['dirlogs']) and !is_dir($cfg['dirlogs'])) { // create logs folder if it not exists
		@mkdir(untrailingslashit($cfg['dirlogs']),0777,true);
	}
	if (!is_dir($cfg['dirlogs'])) { // check logs folder
		$message.=sprintf(__("- Log folder '%s' does not exists!",'backwpup'),$cfg['dirlogs']).'<br />';
	}
	if (!is_writable($cfg['dirlogs'])) { // check logs folder
		$message.=sprintf(__("- Log folder '%s' is not writeable!",'backwpup'),$cfg['dirlogs']).'<br />';
	}
	if (!backwpup_check_open_basedir($cfg['dirlogs'])) { // check logs folder
		$message.=sprintf(__("- Log folder '%s' is not in open_basedir path!",'backwpup'),$cfg['dirlogs']).'<br />';
	}
	if (strtolower(substr(WP_CONTENT_URL,0,7))!='http://' and strtolower(substr(WP_CONTENT_URL,0,8))!='https://') { // check logs folder
		$message.=sprintf(__("- WP_CONTENT_URL '%s' must set as a full URL!",'backwpup'),WP_CONTENT_URL).'<br />';
	}
	if (strtolower(substr(WP_PLUGIN_URL,0,7))!='http://' and strtolower(substr(WP_PLUGIN_URL,0,8))!='https://') { // check logs folder
		$message.=sprintf(__("- WP_PLUGIN_URL '%s' must set as a full URL!",'backwpup'),WP_PLUGIN_URL).'<br />';
	}
	//if (false !== $nextrun=wp_next_scheduled('backwpup_cron')) {
	//	if (empty($nextrun) or $nextrun<(time()-(3600*24))) {  //check cron jobs work
	//		$message.=__("- WP-Cron isn't working, please check it!","backwpup") .'<br />';
	//	}
	//}
	//put massage if one
	if (!empty($message))
		$backwpup_admin_message = '<div id="message" class="error fade"><strong>BackWPup:</strong><br />'.$message.'</div>';
	return $checks;
}

function backwpup_admin_notice() {
	global $backwpup_admin_message;
	if (current_user_can(BACKWPUP_USER_CAPABILITY))
		echo $backwpup_admin_message;
	return;
}

//Checking,upgrade and default job setting
function backwpup_get_job_vars($jobid='',$jobnewsettings='') {
	global $wpdb;
	//get job data
	$jobs=get_option('backwpup_jobs'); //load jobs
	if (!empty($jobid)) {
		if (isset($jobs[$jobid]))
			$jobsettings=$jobs[$jobid];
		$jobsettings['jobid']=$jobid;
	} else {
		if (empty($jobsettings['jobid'])) {  //generate jobid if not exists
			$heighestid=0;
			if (is_array($jobs)) {
				foreach ($jobs as $jobkey => $jobvalue) {
					if ($jobkey>$heighestid)
						$heighestid=$jobkey;
				}
			}
			$jobsettings['jobid']=$heighestid+1;
		}
	}
	unset($jobs);
	unset($jobid);
	if (!empty($jobnewsettings) && is_array($jobnewsettings)) { //overwrite with new settings
		$jobsettings=array_merge($jobsettings,$jobnewsettings);
	}

	//check job type
	if (!isset($jobsettings['type']) or !is_string($jobsettings['type']))
		$jobsettings['type']='DB+FILE';
	$todo=explode('+',strtoupper($jobsettings['type']));
	foreach($todo as $key => $value) {
		if (!in_array($value,backwpup_backup_types()))
			unset($todo[$key]);
	}
	$jobsettings['type']=implode('+',$todo);
	if (empty($jobsettings['type']))
		$jobsettings['type']='DB+FILE';

	if (empty($jobsettings['name']) or !is_string($jobsettings['name']))
		$jobsettings['name']= __('New', 'backwpup');

	if (!isset($jobsettings['activated']) or !is_bool($jobsettings['activated']))
		$jobsettings['activated']=false;

	if (!isset($jobsettings['cronselect']) and !isset($jobsettings['cron']))
		$jobsettings['cronselect']='basic';
	elseif (!isset($jobsettings['cronselect']) and isset($jobsettings['cron']))
		$jobsettings['cronselect']='advanced';

	if ($jobsettings['cronselect']!='advanced' and $jobsettings['cronselect']!='basic')
		$jobsettings['cronselect']='advanced';

	if (!isset($jobsettings['cron']) or !is_string($jobsettings['cron']))
		$jobsettings['cron']='0 3 * * *';

	if (!isset($jobsettings['cronnextrun']) or !is_numeric($jobsettings['cronnextrun']))
		$jobsettings['cronnextrun']=backwpup_cron_next($jobsettings['cron']);

	if (!isset($jobsettings['mailaddresslog']) or !is_string($jobsettings['mailaddresslog']))
		$jobsettings['mailaddresslog']=get_option('admin_email');

	if (!isset($jobsettings['mailerroronly']) or !is_bool($jobsettings['mailerroronly']))
		$jobsettings['mailerroronly']=true;


	//Tables to backup (old)
	if (isset($jobsettings['dbtables']) and is_array($jobsettings['dbtables'])) {
		$tables=$wpdb->get_col('SHOW TABLES FROM `'.DB_NAME.'`');
		foreach ($tables as $table) {
			if (!in_array($table,$jobsettings['dbtables']))
				$jobsettings['dbexclude'][]=$table;
		}
	}

	//don not backup tables
	if (!isset($jobsettings['dbexclude']) or !is_array($jobsettings['dbexclude'])) {
		$jobsettings['dbexclude']=array();
		$tables=$wpdb->get_col('SHOW TABLES FROM `'.DB_NAME.'`');
		foreach ($tables as $table) {
			if (substr($table,0,strlen($wpdb->prefix))!=$wpdb->prefix)
				$jobsettings['dbexclude'][]=$table;
		}
	}

	if (!isset($jobsettings['dbshortinsert']) or !is_bool($jobsettings['dbshortinsert']))
		$jobsettings['dbshortinsert']=false;

	if (!isset($jobsettings['maintenance']) or !is_bool($jobsettings['maintenance']))
		$jobsettings['maintenance']=false;

	if (!isset($jobsettings['fileexclude']) or !is_string($jobsettings['fileexclude']))
		$jobsettings['fileexclude']='';
	$fileexclude=explode(',',$jobsettings['fileexclude']);
	foreach($fileexclude as $key => $value) {
		$fileexclude[$key]=str_replace('//','/',str_replace('\\','/',trim($value)));
		if (empty($fileexclude[$key]))
			unset($fileexclude[$key]);
	}
	sort($fileexclude);
	$jobsettings['fileexclude']=implode(',',$fileexclude);

	if (!isset($jobsettings['dirinclude']) or !is_string($jobsettings['dirinclude']))
		$jobsettings['dirinclude']='';
	$dirinclude=explode(',',$jobsettings['dirinclude']);
	foreach($dirinclude as $key => $value) {
		$dirinclude[$key]=trailingslashit(str_replace('//','/',str_replace('\\','/',trim($value))));
		if ($dirinclude[$key]=='/' or empty($dirinclude[$key]) or !is_dir($dirinclude[$key]))
			unset($dirinclude[$key]);
	}
	sort($dirinclude);
	$jobsettings['dirinclude']=implode(',',$dirinclude);

	if (!isset($jobsettings['backuproot']) or !is_bool($jobsettings['backuproot']))
		$jobsettings['backuproot']=true;

	if (!isset($jobsettings['backupcontent']) or !is_bool($jobsettings['backupcontent']))
		$jobsettings['backupcontent']=true;

	if (!isset($jobsettings['backupplugins']) or !is_bool($jobsettings['backupplugins']))
		$jobsettings['backupplugins']=true;

	if (!isset($jobsettings['backupthemes']) or !is_bool($jobsettings['backupthemes']))
		$jobsettings['backupthemes']=true;

	if (!isset($jobsettings['backupuploads']) or !is_bool($jobsettings['backupuploads']))
		$jobsettings['backupuploads']=true;

	if (!isset($jobsettings['backuprootexcludedirs']) or !is_array($jobsettings['backuprootexcludedirs']))
		$jobsettings['backuprootexcludedirs']=array();
	foreach($jobsettings['backuprootexcludedirs'] as $key => $value) {
		$jobsettings['backuprootexcludedirs'][$key]=str_replace('//','/',str_replace('\\','/',trim($value)));
		if (empty($jobsettings['backuprootexcludedirs'][$key]) or $jobsettings['backuprootexcludedirs'][$key]=='/' or !is_dir($jobsettings['backuprootexcludedirs'][$key]))
			unset($jobsettings['backuprootexcludedirs'][$key]);
	}
	sort($jobsettings['backuprootexcludedirs']);

	if (!isset($jobsettings['backupcontentexcludedirs']) or !is_array($jobsettings['backupcontentexcludedirs']))
		$jobsettings['backupcontentexcludedirs']=array();
	foreach($jobsettings['backupcontentexcludedirs'] as $key => $value) {
		$jobsettings['backupcontentexcludedirs'][$key]=str_replace('//','/',str_replace('\\','/',trim($value)));
		if (empty($jobsettings['backupcontentexcludedirs'][$key]) or $jobsettings['backupcontentexcludedirs'][$key]=='/' or !is_dir($jobsettings['backupcontentexcludedirs'][$key]))
			unset($jobsettings['backupcontentexcludedirs'][$key]);
	}
	sort($jobsettings['backupcontentexcludedirs']);

	if (!isset($jobsettings['backuppluginsexcludedirs']) or !is_array($jobsettings['backuppluginsexcludedirs']))
		$jobsettings['backuppluginsexcludedirs']=array();
	foreach($jobsettings['backuppluginsexcludedirs'] as $key => $value) {
		$jobsettings['backuppluginsexcludedirs'][$key]=str_replace('//','/',str_replace('\\','/',trim($value)));
		if (empty($jobsettings['backuppluginsexcludedirs'][$key]) or $jobsettings['backuppluginsexcludedirs'][$key]=='/' or !is_dir($jobsettings['backuppluginsexcludedirs'][$key]))
			unset($jobsettings['backuppluginsexcludedirs'][$key]);
	}
	sort($jobsettings['backuppluginsexcludedirs']);

	if (!isset($jobsettings['backupthemesexcludedirs']) or !is_array($jobsettings['backupthemesexcludedirs']))
		$jobsettings['backupthemesexcludedirs']=array();
	foreach($jobsettings['backupthemesexcludedirs'] as $key => $value) {
		$jobsettings['backupthemesexcludedirs'][$key]=str_replace('//','/',str_replace('\\','/',trim($value)));
		if (empty($jobsettings['backupthemesexcludedirs'][$key]) or $jobsettings['backupthemesexcludedirs'][$key]=='/' or !is_dir($jobsettings['backupthemesexcludedirs'][$key]))
			unset($jobsettings['backupthemesexcludedirs'][$key]);
	}
	sort($jobsettings['backupthemesexcludedirs']);

	if (!isset($jobsettings['backupuploadsexcludedirs']) or !is_array($jobsettings['backupuploadsexcludedirs']))
		$jobsettings['backupuploadsexcludedirs']=array();
	foreach($jobsettings['backupuploadsexcludedirs'] as $key => $value) {
		$jobsettings['backupuploadsexcludedirs'][$key]=str_replace('//','/',str_replace('\\','/',trim($value)));
		if (empty($jobsettings['backupuploadsexcludedirs'][$key]) or $jobsettings['backupuploadsexcludedirs'][$key]=='/' or !is_dir($jobsettings['backupuploadsexcludedirs'][$key]))
			unset($jobsettings['backupuploadsexcludedirs'][$key]);
	}
	sort($jobsettings['backupuploadsexcludedirs']);

	$fileformarts=array('.zip','.tar.gz','.tar.bz2','.tar');
	if (!isset($jobsettings['fileformart']) or !in_array($jobsettings['fileformart'],$fileformarts))
		$jobsettings['fileformart']='.zip';

	if (!isset($jobsettings['fileprefix']) or !is_string($jobsettings['fileprefix']))
		$jobsettings['fileprefix']='backwpup_'.$jobsettings['jobid'].'_';

	if (!isset($jobsettings['mailefilesize']) or !is_float($jobsettings['mailefilesize']))
		$jobsettings['mailefilesize']=0;

	if (!isset($jobsettings['backupdir']))
		$jobsettings['backupdir']='';
	if (substr($jobsettings['backupdir'],0,1)!='/' and substr($jobsettings['backupdir'],1,1)!=':' and !empty($jobsettings['backupdir'])) //add abspath if not absolute
		$jobsettings['backupdir']=rtrim(str_replace('\\','/',ABSPATH),'/').'/'.$jobsettings['backupdir'];
	$jobsettings['backupdir']=trailingslashit(str_replace('//','/',str_replace('\\','/',trim($jobsettings['backupdir']))));
	if ( $jobsettings[ 'backupdir' ] == '/' || $jobsettings[ 'backupdir' ] == trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) ) || $jobsettings[ 'backupdir' ] == trailingslashit( str_replace( '\\', '/', ABSPATH ) ) )
		$jobsettings['backupdir']='';

	if (!isset($jobsettings['maxbackups']) or !is_int($jobsettings['maxbackups']))
		$jobsettings['maxbackups']=0;

	if (!empty($jobsettings['ftphost']) and false !== strpos($jobsettings['ftphost'],':'))
		list($jobsettings['ftphost'],$jobsettings['ftphostport'])=explode(':',$jobsettings['ftphost'],2);

	if (!isset($jobsettings['ftphost']) or !is_string($jobsettings['ftphost']))
		$jobsettings['ftphost']='';

	$jobsettings['ftphost']=str_replace(array('http://','ftp://'),'',$jobsettings['ftphost']);

	if (!isset($jobsettings['ftphostport']) or !is_int($jobsettings['ftphostport']))
		$jobsettings['ftphostport']=21;

	if (!isset($jobsettings['ftpuser']) or !is_string($jobsettings['ftpuser']))
		$jobsettings['ftpuser']='';

	if (!isset($jobsettings['ftppass']) or !is_string($jobsettings['ftppass']))
		$jobsettings['ftppass']='';

	if (!isset($jobsettings['ftpdir']) or !is_string($jobsettings['ftpdir']))
		$jobsettings['ftpdir']='';
    if (!empty($jobsettings['ftpdir']))
	    $jobsettings['ftpdir']=trailingslashit(ltrim(str_replace('//','/',str_replace('\\','/',trim($jobsettings['ftpdir'])))),'/');

	if (!isset($jobsettings['ftpmaxbackups']) or !is_int($jobsettings['ftpmaxbackups']))
		$jobsettings['ftpmaxbackups']=0;

	if (!isset($jobsettings['ftppasv']) or !is_bool($jobsettings['ftppasv']))
		$jobsettings['ftppasv']=true;

	if (!isset($jobsettings['ftpssl']) or !is_bool($jobsettings['ftpssl']))
		$jobsettings['ftpssl']=false;

	if (!isset($jobsettings['awsAccessKey']) or !is_string($jobsettings['awsAccessKey']))
		$jobsettings['awsAccessKey']='';

	if (!isset($jobsettings['awsSecretKey']) or !is_string($jobsettings['awsSecretKey']))
		$jobsettings['awsSecretKey']='';

	if (!isset($jobsettings['awsrrs']) or !is_bool($jobsettings['awsrrs']))
		$jobsettings['awsrrs']=false;

	if (!isset($jobsettings['awsBucket']) or !is_string($jobsettings['awsBucket']))
		$jobsettings['awsBucket']='';

	if (!isset($jobsettings['awsdir']) or !is_string($jobsettings['awsdir']) or $jobsettings['awsdir']=='/')
		$jobsettings['awsdir']='';
	$jobsettings['awsdir']=trailingslashit(str_replace('//','/',str_replace('\\','/',trim($jobsettings['awsdir']))));
	if (substr($jobsettings['awsdir'],0,1)=='/')
		$jobsettings['awsdir']=substr($jobsettings['awsdir'],1);

	if (!isset($jobsettings['awsmaxbackups']) or !is_int($jobsettings['awsmaxbackups']))
		$jobsettings['awsmaxbackups']=0;

	if (!isset($jobsettings['GStorageAccessKey']) or !is_string($jobsettings['GStorageAccessKey']))
		$jobsettings['GStorageAccessKey']='';

	if (!isset($jobsettings['GStorageSecret']) or !is_string($jobsettings['GStorageSecret']))
		$jobsettings['GStorageSecret']='';

	if (!isset($jobsettings['GStorageBucket']) or !is_string($jobsettings['GStorageBucket']))
		$jobsettings['GStorageBucket']='';

	if (!isset($jobsettings['GStoragedir']) or !is_string($jobsettings['GStoragedir']) or $jobsettings['GStoragedir']=='/')
		$jobsettings['GStoragedir']='';
	$jobsettings['GStoragedir']=trailingslashit(str_replace('//','/',str_replace('\\','/',trim($jobsettings['GStoragedir']))));
	if (substr($jobsettings['GStoragedir'],0,1)=='/')
		$jobsettings['GStoragedir']=substr($jobsettings['GStoragedir'],1);

	if (!isset($jobsettings['GStoragemaxbackups']) or !is_int($jobsettings['GStoragemaxbackups']))
		$jobsettings['GStoragemaxbackups']=0;

	if (!isset($jobsettings['msazureHost']) or !is_string($jobsettings['msazureHost']))
		$jobsettings['msazureHost']='blob.core.windows.net';

	if (!isset($jobsettings['msazureAccName']) or !is_string($jobsettings['msazureAccName']))
		$jobsettings['msazureAccName']='';

	if (!isset($jobsettings['msazureKey']) or !is_string($jobsettings['msazureKey']))
		$jobsettings['msazureKey']='';

	if (!isset($jobsettings['msazureContainer']) or !is_string($jobsettings['msazureContainer']))
		$jobsettings['msazureContainer']='';

	if (!isset($jobsettings['msazuredir']) or !is_string($jobsettings['msazuredir']) or $jobsettings['msazuredir']=='/')
		$jobsettings['msazuredir']='';
	$jobsettings['msazuredir']=trailingslashit(str_replace('//','/',str_replace('\\','/',trim($jobsettings['msazuredir']))));
	if (substr($jobsettings['msazuredir'],0,1)=='/')
		$jobsettings['msazuredir']=substr($jobsettings['msazuredir'],1);

	if (!isset($jobsettings['msazuremaxbackups']) or !is_int($jobsettings['msazuremaxbackups']))
		$jobsettings['msazuremaxbackups']=0;

	if (!isset($jobsettings['rscUsername']) or !is_string($jobsettings['rscUsername']))
		$jobsettings['rscUsername']='';

	if (!isset($jobsettings['rscAPIKey']) or !is_string($jobsettings['rscAPIKey']))
		$jobsettings['rscAPIKey']='';

	if (!isset($jobsettings['rscContainer']) or !is_string($jobsettings['rscContainer']))
		$jobsettings['rscContainer']='';

	if (!isset($jobsettings['rscdir']) or !is_string($jobsettings['rscdir']) or $jobsettings['rscdir']=='/')
		$jobsettings['rscdir']='';
	$jobsettings['rscdir']=trailingslashit(str_replace('//','/',str_replace('\\','/',trim($jobsettings['rscdir']))));
	if (substr($jobsettings['rscdir'],0,1)=='/')
		$jobsettings['rscdir']=substr($jobsettings['rscdir'],1);

	if (!isset($jobsettings['rscmaxbackups']) or !is_int($jobsettings['rscmaxbackups']))
		$jobsettings['rscmaxbackups']=0;

	if (!isset($jobsettings['dropetoken']) or !is_string($jobsettings['dropetoken']))
		$jobsettings['dropetoken']='';

	if (!isset($jobsettings['dropesecret']) or !is_string($jobsettings['dropesecret']))
		$jobsettings['dropesecret']='';

	if (!isset($jobsettings['dropedir']) or !is_string($jobsettings['dropedir']) or $jobsettings['dropedir']=='/')
		$jobsettings['dropedir']='';
	$jobsettings['dropedir']=trailingslashit(str_replace('//','/',str_replace('\\','/',trim($jobsettings['dropedir']))));
	if (substr($jobsettings['dropedir'],0,1)=='/')
		$jobsettings['dropedir']=substr($jobsettings['dropedir'],1);

	if (!isset($jobsettings['droperoot']) or ($jobsettings['droperoot']!='dropbox' and $jobsettings['droperoot']!='sandbox'))
		$jobsettings['droperoot']='dropbox';

	if (!isset($jobsettings['dropemaxbackups']) or !is_int($jobsettings['dropemaxbackups']))
		$jobsettings['dropemaxbackups']=0;

	if (!isset($jobsettings['sugarrefreshtoken']) or !is_string($jobsettings['sugarrefreshtoken']))
		$jobsettings['sugarrefreshtoken']='';

	if (!isset($jobsettings['sugarroot']) or !is_string($jobsettings['sugarroot']))
		$jobsettings['sugarroot']='';

	if (!isset($jobsettings['sugardir']) or !is_string($jobsettings['sugardir']) or $jobsettings['sugardir']=='/')
		$jobsettings['sugardir']='';
	$jobsettings['sugardir']=trailingslashit(str_replace('//','/',str_replace('\\','/',trim($jobsettings['sugardir']))));
	if (substr($jobsettings['sugardir'],0,1)=='/')
		$jobsettings['sugardir']=substr($jobsettings['sugardir'],1);

	if (!isset($jobsettings['sugarmaxbackups']) or !is_int($jobsettings['sugarmaxbackups']))
		$jobsettings['sugarmaxbackups']=0;

	if (!isset($jobsettings['mailaddress']) or !is_string($jobsettings['mailaddress']))
		$jobsettings['mailaddress']='';

	//unset old not nedded vars
	unset($jobsettings['scheduletime']);
	unset($jobsettings['scheduleintervaltype']);
	unset($jobsettings['scheduleintervalteimes']);
	unset($jobsettings['scheduleinterval']);
	unset($jobsettings['dropemail']);
	unset($jobsettings['dropepass']);
	unset($jobsettings['dbtables']);
	unset($jobsettings['dropesignmethod']);
	unset($jobsettings['sugarpass']);
	unset($jobsettings['sugaruser']);

	return $jobsettings;
}