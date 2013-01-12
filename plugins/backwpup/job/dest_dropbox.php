<?PHP
function dest_dropbox() {
  global $WORKING,$STATIC;
  $WORKING['STEPTODO']=2+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
  $WORKING['STEPDONE']=0;
  trigger_error(sprintf(__('%d. Try to sending backup file to DropBox...','backwpup'),$WORKING['DEST_DROPBOX']['STEP_TRY']),E_USER_NOTICE);

  require_once(realpath(dirname(__FILE__).'/../libs/dropbox.php'));
  try {
    need_free_memory(10000000);
    //set boxtype
    $dropbox = new backwpup_Dropbox('dropbox');

    // set the tokens
    $dropbox->setOAuthTokens($STATIC['JOB']['dropetoken'],$STATIC['JOB']['dropesecret']);
    $info=$dropbox->accountInfo();
    if (!empty($info['uid'])) {
      trigger_error(sprintf(__('Authed with DropBox from %s','backwpup'),$info['display_name']),E_USER_NOTICE);
    }
    //Check Quota
    $dropboxfreespase=(float)$info['quota_info']['quota']-(float)$info['quota_info']['shared']-(float)$info['quota_info']['normal'];
    if (filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile'])>$dropboxfreespase) {
      trigger_error(__('No free space left on DropBox!!!','backwpup'),E_USER_ERROR);
      $WORKING['STEPSDONE'][]='DEST_DROPBOX'; //set done
      return;
    } else {
      trigger_error(sprintf(__('%s free on DropBox','backwpup'),formatBytes($dropboxfreespase)),E_USER_NOTICE);
    }
    //set callback function
    $dropbox->setProgressFunction('curl_progresscallback');
    // put the file
    trigger_error(__('Upload to DropBox now started... ','backwpup'),E_USER_NOTICE);
    $response = $dropbox->upload($STATIC['JOB']['backupdir'].$STATIC['backupfile'],$STATIC['JOB']['dropedir'].$STATIC['backupfile']);
    if ($response['bytes']==filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile'])) {
      $STATIC['JOB']['lastbackupdownloadurl']=$STATIC['WP']['ADMINURL'].'?page=backwpupbackups&action=downloaddropbox&file='.$STATIC['JOB']['dropedir'].$STATIC['backupfile'].'&jobid='.$STATIC['JOB']['jobid'];
      $WORKING['STEPDONE']++;
      $WORKING['STEPSDONE'][]='DEST_DROPBOX'; //set done
      trigger_error(sprintf(__('Backup transferred to %s','backwpup'),'https://api-content.dropbox.com/1/files/'.$STATIC['JOB']['droperoot'].'/'.$STATIC['JOB']['dropedir'].$STATIC['backupfile']),E_USER_NOTICE);
    }
    //unset calback function
    $dropbox->setProgressFunction();
  } catch (Exception $e) {
    trigger_error(sprintf(__('DropBox API: %s','backwpup'),$e->getMessage()),E_USER_ERROR);
  }
  try {
    if ($STATIC['JOB']['dropemaxbackups']>0 and is_object($dropbox)) { //Delete old backups
      $backupfilelist=array();
      $metadata = $dropbox->metadata($STATIC['JOB']['dropedir']);
      if (is_array($metadata)) {
        foreach ($metadata['contents'] as $data) {
          $file=basename($data['path']);
          if ($data['is_dir']!=true and $STATIC['JOB']['fileprefix'] == substr($file,0,strlen($STATIC['JOB']['fileprefix'])) and $STATIC['JOB']['fileformart'] == substr($file,-strlen($STATIC['JOB']['fileformart'])))
            $backupfilelist[]=$file;
        }
      }
      if (sizeof($backupfilelist)>0) {
        rsort($backupfilelist);
        $numdeltefiles=0;
        for ($i=$STATIC['JOB']['dropemaxbackups'];$i<count($backupfilelist);$i++) {
          $dropbox->fileopsDelete($STATIC['JOB']['dropedir'].$backupfilelist[$i]); //delete files on Cloud
          $numdeltefiles++;
        }
        if ($numdeltefiles>0)
          trigger_error(sprintf(_n('One file deleted on DropBox','%d files deleted on DropBox',$numdeltefiles,'backwpup'),$numdeltefiles),E_USER_NOTICE);
      }
    }
  } catch (Exception $e) {
    trigger_error(sprintf(__('DropBox API: %s','backwpup'),$e->getMessage()),E_USER_ERROR);
  }

  $WORKING['STEPDONE']++;
}