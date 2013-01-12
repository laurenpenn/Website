<?PHP
function dest_msazure() {
	global $WORKING,$STATIC;
	$WORKING['STEPTODO']=2+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
	trigger_error(sprintf(__('%d. try sending backup to a Microsoft Azure (Blob)...','backwpup'),$WORKING['DEST_MSAZURE']['STEP_TRY']),E_USER_NOTICE);

	require_once(dirname(__FILE__).'/../libs/Microsoft/WindowsAzure/Storage/Blob.php');
	need_free_memory(4194304*1.5); 
	
	try {
		$storageClient = new Microsoft_WindowsAzure_Storage_Blob($STATIC['JOB']['msazureHost'],$STATIC['JOB']['msazureAccName'],$STATIC['JOB']['msazureKey']);

		if(!$storageClient->containerExists($STATIC['JOB']['msazureContainer'])) {
			trigger_error(sprintf(__('Microsoft Azure container "%s" not exists!','backwpup'),$STATIC['JOB']['msazureContainer']),E_USER_ERROR);
			return;
		} else {
			trigger_error(sprintf(__('Connected to Microsoft Azure container "%s"','backwpup'),$STATIC['JOB']['msazureContainer']),E_USER_NOTICE);
		}
		
		trigger_error(__('Upload to MS Azure now started... ','backwpup'),E_USER_NOTICE);
		$result = $storageClient->putBlob($STATIC['JOB']['msazureContainer'], $STATIC['JOB']['msazuredir'].$STATIC['backupfile'], $STATIC['JOB']['backupdir'].$STATIC['backupfile']);
		
		if ($result->Name==$STATIC['JOB']['msazuredir'].$STATIC['backupfile']) {
			$WORKING['STEPTODO']=1+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
			trigger_error(sprintf(__('Backup transferred to %s','backwpup'),'https://'.$STATIC['JOB']['msazureAccName'].'.'.$STATIC['JOB']['msazureHost'].'/'.$STATIC['JOB']['msazuredir'].$STATIC['backupfile']),E_USER_NOTICE);
			$STATIC['JOB']['lastbackupdownloadurl']=$STATIC['WP']['ADMINURL'].'?page=backwpupbackups&action=downloadmsazure&file='.$STATIC['JOB']['msazuredir'].$STATIC['backupfile'].'&jobid='.$STATIC['JOB']['jobid'];
			$WORKING['STEPSDONE'][]='DEST_MSAZURE'; //set done
		} else {
			trigger_error(__('Can not transfer backup to Microsoft Azure!','backwpup'),E_USER_ERROR);
		}

		if ($STATIC['JOB']['msazuremaxbackups']>0) { //Delete old backups
			$backupfilelist=array();
			$blobs = $storageClient->listBlobs($STATIC['JOB']['msazureContainer'],$STATIC['JOB']['msazuredir']);
			if (is_array($blobs)) {
				foreach ($blobs as $blob) {
					$file=basename($blob->Name);
					if ($STATIC['JOB']['fileprefix'] == substr($file,0,strlen($STATIC['JOB']['fileprefix'])) and $STATIC['JOB']['fileformart'] == substr($file,-strlen($STATIC['JOB']['fileformart'])))
						$backupfilelist[]=$file;
				}
			}
			if (sizeof($backupfilelist)>0) {
				rsort($backupfilelist);
				$numdeltefiles=0;
				for ($i=$STATIC['JOB']['msazuremaxbackups'];$i<sizeof($backupfilelist);$i++) {
					$storageClient->deleteBlob($STATIC['JOB']['msazureContainer'],$STATIC['JOB']['msazuredir'].$backupfilelist[$i]); //delte files on Cloud
					$numdeltefiles++;
				}
				if ($numdeltefiles>0)
					trigger_error(sprintf(_n('One file deleted on Microsoft Azure container','%d files deleted on Microsoft Azure container',$numdeltefiles,'backwpup'),$numdeltefiles),E_USER_NOTICE);
			}
		}
		
	} catch (Exception $e) {
		trigger_error(sprintf(__('Microsoft Azure API: %s','backwpup'),$e->getMessage()),E_USER_ERROR);
	} 
		
	$WORKING['STEPDONE']++;
}