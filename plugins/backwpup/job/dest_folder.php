<?PHP
function dest_folder() {
	global $WORKING,$STATIC;
	$WORKING['STEPTODO']=1;
	$WORKING['STEPDONE']=0;
	$STATIC['JOB']['lastbackupdownloadurl']=$STATIC['WP']['ADMINURL'].'?page=backwpupbackups&action=download&file='.$STATIC['JOB']['backupdir'].$STATIC['backupfile'];
	//Delete old Backupfiles
	$backupfilelist=array();
	if ($STATIC['JOB']['maxbackups']>0) {
		if ( $dir = @opendir($STATIC['JOB']['backupdir']) ) { //make file list
			while (($file = readdir($dir)) !== false ) {
				if ($STATIC['JOB']['fileprefix'] == substr($file,0,strlen($STATIC['JOB']['fileprefix'])) and $STATIC['JOB']['fileformart'] == substr($file,-strlen($STATIC['JOB']['fileformart'])))
					$backupfilelist[]=$file;
			}
			@closedir($dir);
		}
		if (sizeof($backupfilelist)>0) {
			rsort($backupfilelist);
			$numdeltefiles=0;
			for ($i=$STATIC['JOB']['maxbackups'];$i<sizeof($backupfilelist);$i++) {
				unlink($STATIC['JOB']['backupdir'].$backupfilelist[$i]);
				$numdeltefiles++;
			}
			if ($numdeltefiles>0)
				trigger_error(sprintf(_n('One backup file deleted','%d backup files deleted',$numdeltefiles,'backwpup'),$numdeltefiles),E_USER_NOTICE);
		}
	}
	$WORKING['STEPDONE']++;
	$WORKING['STEPSDONE'][]='DEST_FOLDER'; //set done
}