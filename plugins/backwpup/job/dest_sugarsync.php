<?PHP
function dest_sugarsync() {
	global $WORKING,$STATIC;
	$WORKING['STEPTODO']=2+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
	$WORKING['STEPDONE']=0;
	trigger_error(sprintf(__('%d. try sending backup to SugarSync...','backwpup'),$WORKING['DEST_SUGARSYNC']['STEP_TRY']),E_USER_NOTICE);

	require_once(realpath(dirname(__FILE__).'/../libs/sugarsync.php'));

	try {
		$sugarsync = new SugarSync($STATIC['JOB']['sugarrefreshtoken']);
		//Check Quota
		$user=$sugarsync->user();
		if (!empty($user->nickname)) {
			trigger_error(sprintf(__('Authed to SugarSync with Nick %s','backwpup'),$user->nickname),E_USER_NOTICE);
		}
		$sugarsyncfreespase=(float)$user->quota->limit-(float)$user->quota->usage; //float fixes bug for display of no free space
		if (filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile'])>$sugarsyncfreespase) {
			trigger_error(__('No free space left on SugarSync!!!','backwpup'),E_USER_ERROR);
			$WORKING['STEPTODO']=1+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
			$WORKING['STEPSDONE'][]='DEST_SUGARSYNC'; //set done
			return;
		} else {
			trigger_error(sprintf(__('%s free on SugarSync','backwpup'),formatBytes($sugarsyncfreespase)),E_USER_NOTICE);
		}
		//Create and change folder
		$sugarsync->mkdir($STATIC['JOB']['sugardir'],$STATIC['JOB']['sugarroot']);
		$dirid=$sugarsync->chdir($STATIC['JOB']['sugardir'],$STATIC['JOB']['sugarroot']);
		//Upload to Sugarsync
		$sugarsync->setProgressFunction('curl_progresscallback');
		trigger_error(__('Upload to SugarSync now started... ','backwpup'),E_USER_NOTICE);
		$reponse=$sugarsync->upload($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
		if (is_object($reponse)) {
			$STATIC['JOB']['lastbackupdownloadurl']=$STATIC['WP']['ADMINURL'].'?page=backwpupbackups&action=downloadsugarsync&file='.(string)$reponse.'&jobid='.$STATIC['JOB']['jobid'];
			$WORKING['STEPDONE']++;
			$WORKING['STEPSDONE'][]='DEST_SUGARSYNC'; //set done
			trigger_error(sprintf(__('Backup transferred to %s','backwpup'),'https://'.$user->nickname.'.sugarsync.com/'.$sugarsync->showdir($dirid).$STATIC['backupfile']),E_USER_NOTICE);
		} else {
			trigger_error(__('Can not transfer backup to SugarSync!','backwpup'),E_USER_ERROR);
			return;
		}
		$sugarsync->setProgressFunction('');

		if ($STATIC['JOB']['sugarmaxbackups']>0) { //Delete old backups
			$backupfilelist=array();
			$getfiles=$sugarsync->getcontents('file');
			if (is_object($getfiles)) {
				foreach ($getfiles->file as $getfile) {
					if ($STATIC['JOB']['fileprefix'] == substr($getfile->displayName,0,strlen($STATIC['JOB']['fileprefix'])) and $STATIC['JOB']['fileformart'] == substr($getfile->displayName,-strlen($STATIC['JOB']['fileformart'])))
						$backupfilelist[]=$getfile->displayName;
						$backupfileref[utf8_encode($getfile->displayName)]=$getfile->ref;
				}
			}
			if (sizeof($backupfilelist)>0) {
				rsort($backupfilelist);
				$numdeltefiles=0;
				for ($i=$STATIC['JOB']['sugarmaxbackups'];$i<count($backupfilelist);$i++) {
					$sugarsync->delete($backupfileref[utf8_encode($backupfilelist[$i])]); //delete files on Cloud
					$numdeltefiles++;
				}
				if ($numdeltefiles>0)
					trigger_error(sprintf(_n('One file deleted on SugarSync folder','%d files deleted on SugarSync folder',$numdeltefiles,'backwpup'),$numdeltefiles),E_USER_NOTICE);
			}
		}
	} catch (Exception $e) {
		trigger_error(sprintf(__('SugarSync API: %s','backwpup'),$e->getMessage()),E_USER_ERROR);
	}

	$WORKING['STEPDONE']++;
}