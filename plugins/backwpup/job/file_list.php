<?PHP
function file_list() {
	global $WORKING,$STATIC,$tempfilelist;
	//Make filelist
	trigger_error(sprintf(__('%d. try for make list of files to backup....','backwpup'),$WORKING['FILE_LIST']['STEP_TRY']),E_USER_NOTICE);
	$WORKING['STEPTODO']=2;

	//Check free memory for file list
	need_free_memory('20MB'); //10MB free memory for filelist
	//empty filelist
	$tempfilelist=array();
	//exlude of job
	$WORKING['FILEEXCLUDES']=explode(',',trim($STATIC['JOB']['fileexclude']));
	$WORKING['FILEEXCLUDES'][]='.tmp';  //do not backup .tmp files
	$WORKING['FILEEXCLUDES']=array_unique($WORKING['FILEEXCLUDES']);

	//File list for blog folders
	if ($STATIC['JOB']['backuproot'])
		_file_list($STATIC['WP']['ABSPATH'],100,array_merge($STATIC['JOB']['backuprootexcludedirs'],_get_exclude_dirs($STATIC['WP']['ABSPATH'])));
	if ($STATIC['JOB']['backupcontent'])
		_file_list($STATIC['WP']['WP_CONTENT_DIR'],100,array_merge($STATIC['JOB']['backupcontentexcludedirs'],_get_exclude_dirs($STATIC['WP']['WP_CONTENT_DIR'])));
	if ($STATIC['JOB']['backupplugins'])
		_file_list($STATIC['WP']['WP_PLUGIN_DIR'],100,array_merge($STATIC['JOB']['backuppluginsexcludedirs'],_get_exclude_dirs($STATIC['WP']['WP_PLUGIN_DIR'])));
	if ($STATIC['JOB']['backupthemes'])
		_file_list($STATIC['WP']['WP_THEMES_DIR'],100,array_merge($STATIC['JOB']['backupthemesexcludedirs'],_get_exclude_dirs($STATIC['WP']['WP_THEMES_DIR'])));
	if ($STATIC['JOB']['backupuploads'])
		_file_list($STATIC['WP']['WP_UPLOAD_DIR'],100,array_merge($STATIC['JOB']['backupuploadsexcludedirs'],_get_exclude_dirs($STATIC['WP']['WP_UPLOAD_DIR'])));

	//include dirs
	if (!empty($STATIC['JOB']['dirinclude'])) {
		$dirinclude=explode(',',$STATIC['JOB']['dirinclude']);
		$dirinclude=array_unique($dirinclude);
		//Crate file list for includes
		foreach($dirinclude as $dirincludevalue) {
			if (is_dir($dirincludevalue))
				_file_list($dirincludevalue,100);
		}
	}
	$tempfilelist=array_unique($tempfilelist); //all files only one time in list
	sort($tempfilelist);
	$WORKING['STEPDONE']=1; //Step done
	update_working_file();

	//Check abs path
	if ($STATIC['WP']['ABSPATH']=='/' or $STATIC['WP']['ABSPATH']=='')
		$removepath='';
	else
		$removepath=$STATIC['WP']['ABSPATH'];
	//make file list
	$filelist=array();
	for ($i=0; $i<count($tempfilelist); $i++) {
		$filestat=stat($tempfilelist[$i]);
		$WORKING['ALLFILESIZE']+=$filestat['size'];
		$outfile=str_replace($removepath,'',$tempfilelist[$i]);
		if (substr($outfile,0,1)=='/') //remove first /
			$outfile=substr($outfile,1);
		$filelist[]=array('FILE'=>$tempfilelist[$i],'OUTFILE'=>$outfile,'SIZE'=>$filestat['size'],'ATIME'=>$filestat['atime'],'MTIME'=>$filestat['mtime'],'CTIME'=>$filestat['ctime'],'UID'=>$filestat['uid'],'GID'=>$filestat['gid'],'MODE'=>$filestat['mode']);
	}
	add_file($filelist); //add files to list
	$WORKING['STEPDONE']=2;
	$WORKING['STEPSDONE'][]='FILE_LIST'; //set done
	unset($tempfilelist);

	$filelist=get_filelist(); //get files from list
	if (!is_array($filelist[0])) {
		trigger_error(__('No files to backup','backwpup'),E_USER_ERROR);
	} else {
		trigger_error(sprintf(__('%1$d files with %2$s to backup','backwpup'),count($filelist),formatBytes($WORKING['ALLFILESIZE'])),E_USER_NOTICE);
	}
}

function _file_list( $folder = '', $levels = 100, $excludedirs=array()) {
	global $WORKING,$tempfilelist;
	if( empty($folder) )
		return false;
	if( ! $levels )
		return false;
	if ($levels == 100 or $levels == 95)
		update_working_file();
	$folder=rtrim($folder,'/').'/';
	if ( $dir = @opendir( $folder ) ) {
		while (($file = readdir( $dir ) ) !== false ) {
			if ( in_array($file, array('.', '..','.svn') ) )
				continue;
			foreach ($WORKING['FILEEXCLUDES'] as $exclusion) { //exclude dirs and files
				$exclusion=trim($exclusion);
				if (false !== stripos($folder.$file,$exclusion) and !empty($exclusion) and $exclusion!='/')
					continue 2;
			}
			if (in_array(rtrim($folder.$file,'/').'/',$excludedirs) and is_dir( $folder.$file ))
				continue;
			if ( !is_readable($folder.$file)) {
				trigger_error(sprintf(__('File or folder "%s" is not readable!','backwpup'),$folder.$file),E_USER_WARNING);
			} elseif ( is_link($folder.$file) ) {
				trigger_error(sprintf(__('Link "%s" not followed','backwpup'),$folder.$file),E_USER_WARNING);
			} elseif ( is_dir( $folder.$file )) {
				_file_list( rtrim($folder.$file,'/'), $levels - 1,$excludedirs);
			} elseif ( is_file( $folder.$file ) or is_executable($folder.$file)) { //add file to filelist
				$tempfilelist[]=$folder.$file;
			} else {
				trigger_error(sprintf(__('"%s" is not a file or directory','backwpup'),$folder.$file),E_USER_WARNING);
			}

		}
		@closedir( $dir );
	}
}

function _get_exclude_dirs($folder) {
	global $WORKING,$STATIC;
	$excludedir=array();
	$excludedir[]=$STATIC['TEMPDIR']; //exclude temp dir
	$excludedir[]=$STATIC['CFG']['dirlogs'];
	if (false !== strpos($STATIC['WP']['ABSPATH'],$folder) and $STATIC['WP']['ABSPATH']!=$folder)
		$excludedir[]=$STATIC['WP']['ABSPATH'];
	if (false !== strpos($STATIC['WP']['WP_CONTENT_DIR'],$folder) and $STATIC['WP']['WP_CONTENT_DIR']!=$folder)
		$excludedir[]=$STATIC['WP']['WP_CONTENT_DIR'];
	if (false !== strpos($STATIC['WP']['WP_PLUGIN_DIR'],$folder) and $STATIC['WP']['WP_PLUGIN_DIR']!=$folder)
		$excludedir[]=$STATIC['WP']['WP_PLUGIN_DIR'];
	if (false !== strpos($STATIC['WP']['WP_THEMES_DIR'],$folder) and $STATIC['WP']['WP_THEMES_DIR']!=$folder)
		$excludedir[]=$STATIC['WP']['WP_THEMES_DIR'];
	if (false !== strpos($STATIC['WP']['WP_UPLOAD_DIR'],$folder) and $STATIC['WP']['WP_UPLOAD_DIR']!=$folder)
		$excludedir[]=$STATIC['WP']['WP_UPLOAD_DIR'];
	//Exclude Backup dirs
	$jobs=get_option('backwpup_jobs');
	if (!empty($jobs)) {
		foreach($jobs as $jobsvalue) {
			if (!empty($jobsvalue['backupdir']) and $jobsvalue['backupdir']!='/')
				$excludedir[]=$jobsvalue['backupdir'];
		}
	}
	return $excludedir;
}