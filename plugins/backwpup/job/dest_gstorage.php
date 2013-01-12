<?PHP
function dest_gstorage() {
  global $WORKING,$STATIC;
  trigger_error(sprintf(__('%d. try sending backup to Google Storage...','backwpup'),$WORKING['DEST_GSTORAGE']['STEP_TRY']),E_USER_NOTICE);
  $WORKING['STEPTODO']=2+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
  $WORKING['STEPDONE']=0;

  require_once(dirname(__FILE__).'/../libs/aws/sdk.class.php');
  need_free_memory(26214400*1.1);
  try {
    $gstorage = new AmazonS3(array('key'=>$STATIC['JOB']['GStorageAccessKey'],'secret'=>$STATIC['JOB']['GStorageSecret'],'certificate_authority'=>true));
    //set up s3 for google
    $gstorage->set_hostname('storage.googleapis.com');
    $gstorage->allow_hostname_override(false);
    if ($gstorage->if_bucket_exists($STATIC['JOB']['GStorageBucket'])) {
      trigger_error(sprintf(__('Connected to GStorage Bucket: %s','backwpup'),$STATIC['JOB']['GStorageBucket']),E_USER_NOTICE);
      //set curl Prozess bar
      $curlops=array();
      if (defined('CURLOPT_PROGRESSFUNCTION'))
        $curlops=array(CURLOPT_NOPROGRESS=>false,CURLOPT_PROGRESSFUNCTION=>'curl_progresscallback',CURLOPT_BUFFERSIZE=>1048576);
      trigger_error(__('Upload to GStorage now started... ','backwpup'),E_USER_NOTICE);
      //transferee file to GStorage
      $result=$gstorage->create_object($STATIC['JOB']['GStorageBucket'], $STATIC['JOB']['GStoragedir'].$STATIC['backupfile'], array('fileUpload' => $STATIC['JOB']['backupdir'].$STATIC['backupfile'],'acl' => 'private','curlopts'=>$curlops));
      $result=(array)$result;
      if ($result["status"]>=200 and $result["status"]<300)  {
        $WORKING['STEPTODO']=1+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
        trigger_error(sprintf(__('Backup transferred to %s','backwpup'),"https://storage.cloud.google.com/".$STATIC['JOB']['GStorageBucket']."/".$STATIC['JOB']['GStoragedir'].$STATIC['backupfile']),E_USER_NOTICE);
        $STATIC['JOB']['lastbackupdownloadurl']="https://storage.cloud.google.com/" . $STATIC['JOB']['GStorageBucket'] . "/" . $STATIC['JOB']['GStoragedir'] . $STATIC['backupfile'];
        $WORKING['STEPSDONE'][]='DEST_GSTORAGE'; //set done
      } else {
        trigger_error(sprintf(__('Can not transfer backup to GStorage! (%1$d) %2$s','backwpup'),$result["status"],$result["Message"]),E_USER_ERROR);
      }
    } else {
      trigger_error(sprintf(__('GStorage Bucket "%s" not exists!','backwpup'),$STATIC['JOB']['GStorageBucket']),E_USER_ERROR);
    }
  } catch (Exception $e) {
    trigger_error(sprintf(__('GStorage API: %s','backwpup'),$e->getMessage()),E_USER_ERROR);
    return;
  }
  try {
    if ($gstorage->if_bucket_exists($STATIC['JOB']['GStorageBucket'])) {
      if ($STATIC['JOB']['GStoragemaxbackups']>0) { //Delete old backups
        $backupfilelist=array();
        if (($contents = $gstorage->list_objects($STATIC['JOB']['GStorageBucket'],array('prefix'=>$STATIC['JOB']['GStoragedir']))) !== false) {
          foreach ($contents->body->Contents as $object) {
            $file=basename($object->Key);
            if ($STATIC['JOB']['fileprefix'] == substr($file,0,strlen($STATIC['JOB']['fileprefix'])) and $STATIC['JOB']['fileformart'] == substr($file,-strlen($STATIC['JOB']['fileformart'])))
              $backupfilelist[]=$file;
          }
        }
        if (sizeof($backupfilelist)>0) {
          rsort($backupfilelist);
          $numdeltefiles=0;
          for ($i=$STATIC['JOB']['GStoragemaxbackups'];$i<sizeof($backupfilelist);$i++) {
            if ($gstorage->delete_object($STATIC['JOB']['GStorageBucket'], $STATIC['JOB']['GStoragedir'].$backupfilelist[$i])) //delte files on S3
              $numdeltefiles++;
            else
              trigger_error(sprintf(__('Can not delete backup on GStorage://%s','backwpup'),$STATIC['JOB']['awsBucket'].'/'.$STATIC['JOB']['GStoragedir'].$backupfilelist[$i]),E_USER_ERROR);
          }
          if ($numdeltefiles>0)
            trigger_error(sprintf(_n('One file deleted on GStorage Bucket','%d files deleted on GStorage Bucket',$numdeltefiles,'backwpup'),$numdeltefiles),E_USER_NOTICE);
        }
      }
    }
  } catch (Exception $e) {
    trigger_error(sprintf(__('GStorage API: %s','backwpup'),$e->getMessage()),E_USER_ERROR);
    return;
  }

  $WORKING['STEPDONE']++;
}