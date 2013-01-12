<?PHP
function dest_rsc() {
	global $WORKING,$STATIC;
	trigger_error($WORKING['DEST_RSC']['STEP_TRY'].'. '.__('Try to sending backup file to Rackspace Cloud...','backwpup'),E_USER_NOTICE);
	$WORKING['STEPTODO']=2+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
	$WORKING['STEPDONE']=0;
	require_once(dirname(__FILE__).'/../libs/rackspace/cloudfiles.php');
	
	$auth = new CF_Authentication($STATIC['JOB']['rscUsername'], $STATIC['JOB']['rscAPIKey']);
	$auth->ssl_use_cabundle();
	try {
		if ($auth->authenticate())
			trigger_error(__('Connected to Rackspase ...','backwpup'),E_USER_NOTICE);			
		$conn = new CF_Connection($auth);
		$conn->ssl_use_cabundle();
		$is_container=false;
		$containers=$conn->get_containers();
		foreach ($containers as $container) {
			if ($container->name == $STATIC['JOB']['rscContainer'] )
				$is_container=true;
		}
		if (!$is_container) {
			$public_container = $conn->create_container($STATIC['JOB']['rscContainer']);
			$public_container->make_private();
			if (empty($public_container))
				$is_container=false;
		}	
	} catch (Exception $e) {
		trigger_error(__('Rackspase Cloud API:','backwpup').' '.$e->getMessage(),E_USER_ERROR);
		return;
	}
	
	if (!$is_container) {
		trigger_error(__('Rackspase Cloud Container not exists:','backwpup').' '.$STATIC['JOB']['rscContainer'],E_USER_ERROR);
		return;
	}
	
	try {
		//Transfer Backup to Rackspace Cloud
		$backwpupcontainer = $conn->get_container($STATIC['JOB']['rscContainer']);
		//if (!empty($STATIC['JOB']['rscdir'])) //make the foldder
		//	$backwpupcontainer->create_paths($STATIC['JOB']['rscdir']); 
		$backwpupbackup = $backwpupcontainer->create_object($STATIC['JOB']['rscdir'].$STATIC['backupfile']);
		//set content Type
		if ($STATIC['JOB']['fileformart']=='.zip')
			$backwpupbackup->content_type='application/zip';
		if ($STATIC['JOB']['fileformart']=='.tar')
			$backwpupbackup->content_type='application/x-ustar';
		if ($STATIC['JOB']['fileformart']=='.tar.gz')
			$backwpupbackup->content_type='application/x-compressed';
		if ($STATIC['JOB']['fileformart']=='.tar.bz2')
			$backwpupbackup->content_type='application/x-compressed';			
		trigger_error(__('Upload to RSC now started ... ','backwpup'),E_USER_NOTICE);
		if ($backwpupbackup->load_from_filename($STATIC['JOB']['backupdir'].$STATIC['backupfile'])) {
			$WORKING['STEPTODO']=1+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
			trigger_error(__('Backup File transferred to RSC://','backwpup').$STATIC['JOB']['rscContainer'].'/'.$STATIC['JOB']['rscdir'].$STATIC['backupfile'],E_USER_NOTICE);
			$STATIC['JOB']['lastbackupdownloadurl']=$STATIC['WP']['ADMINURL'].'?page=backwpupbackups&action=downloadrsc&file='.$STATIC['JOB']['rscdir'].$STATIC['backupfile'].'&jobid='.$STATIC['JOB']['jobid'];
			$WORKING['STEPSDONE'][]='DEST_RSC'; //set done
		} else {
			trigger_error(__('Can not transfer backup to RSC.','backwpup'),E_USER_ERROR);
		}
	} catch (Exception $e) {
		trigger_error(__('Rackspase Cloud API:','backwpup').' '.$e->getMessage(),E_USER_ERROR);
	}
	try {	
		if ($STATIC['JOB']['rscmaxbackups']>0) { //Delete old backups
			$backupfilelist=array();
			$contents = $backwpupcontainer->list_objects(0,NULL,NULL,$STATIC['JOB']['rscdir']);
			if (is_array($contents)) {
				foreach ($contents as $object) {
					$file=basename($object);
					if ($STATIC['JOB']['rscdir'].$file == $object) {//only in the folder and not in complete bucket
						if ($STATIC['JOB']['fileprefix'] == substr($file,0,strlen($STATIC['JOB']['fileprefix'])) and $STATIC['JOB']['fileformart'] == substr($file,-strlen($STATIC['JOB']['fileformart'])))
							$backupfilelist[]=$file;
					}
				}
			}
			if (sizeof($backupfilelist)>0) {
				rsort($backupfilelist);
				$numdeltefiles=0;
				for ($i=$STATIC['JOB']['rscmaxbackups'];$i<sizeof($backupfilelist);$i++) {
					if ($backwpupcontainer->delete_object($STATIC['JOB']['rscdir'].$backupfilelist[$i])) //delte files on Cloud
						$numdeltefiles++;
					else
						trigger_error(__('Can not delete file on RSC://','backwpup').$STATIC['JOB']['rscContainer'].$STATIC['JOB']['rscdir'].$backupfilelist[$i],E_USER_ERROR);
				}
				if ($numdeltefiles>0)
					trigger_error(sprintf(_n('One file deleted on RSC container','%d files deleted on RSC container',$numdeltefiles,'backwpup'),$numdeltefiles),E_USER_NOTICE);
			}
		}	
	} catch (Exception $e) {
		trigger_error(__('Rackspase Cloud API:','backwpup').' '.$e->getMessage(),E_USER_ERROR);
	} 

	$WORKING['STEPDONE']++;
}