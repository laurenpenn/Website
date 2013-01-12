<?PHP
function dest_s3() {
  global $WORKING,$STATIC;
  trigger_error(sprintf(__('%d. try sending backup file to Amazon S3...','backwpup'),$WORKING['DEST_S3']['STEP_TRY']),E_USER_NOTICE);
  $WORKING['STEPTODO']=2+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
  $WORKING['STEPDONE']=0;

  require_once(dirname(__FILE__).'/../libs/aws/sdk.class.php');
  need_free_memory(26214400*1.1);

  try {
    $s3 = new AmazonS3(array('key'=>$STATIC['JOB']['awsAccessKey'],'secret'=>$STATIC['JOB']['awsSecretKey'],'certificate_authority'=>true));
    if ($s3->if_bucket_exists($STATIC['JOB']['awsBucket'])) {
      trigger_error(sprintf(__('Connected to S3 Bucket: %s','backwpup'),$STATIC['JOB']['awsBucket']),E_USER_NOTICE);
      //Transfer Backup to S3
      if ($STATIC['JOB']['awsrrs']) //set reduced redundancy or not
        $storage=AmazonS3::STORAGE_REDUCED;
      else
        $storage=AmazonS3::STORAGE_STANDARD;
      //set curl Progress bar
      $curlops=array();
      if (defined('CURLOPT_PROGRESSFUNCTION'))
          $curlops=array(CURLOPT_NOPROGRESS=>false,CURLOPT_PROGRESSFUNCTION=>'curl_progresscallback',CURLOPT_BUFFERSIZE=>1048576);
      trigger_error(__('Upload to Amazon S3 now started... ','backwpup'),E_USER_NOTICE);
      //transferee file to S3
      $result=$s3->create_object($STATIC['JOB']['awsBucket'], $STATIC['JOB']['awsdir'].$STATIC['backupfile'], array('fileUpload' => $STATIC['JOB']['backupdir'].$STATIC['backupfile'],'acl' => AmazonS3::ACL_PRIVATE,'storage' => $storage,'curlopts'=>$curlops));
      $result=(array)$result;
      if ($result["status"]>=200 and $result["status"]<300)  {
        $WORKING['STEPTODO']=1+filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
        trigger_error(sprintf(__('Backup transferred to %s','backwpup'),$result["header"]["_info"]["url"]),E_USER_NOTICE);
        $STATIC['JOB']['lastbackupdownloadurl']=$STATIC['WP']['ADMINURL'].'?page=backwpupbackups&action=downloads3&file='.$STATIC['JOB']['awsdir'].$STATIC['backupfile'].'&jobid='.$STATIC['JOB']['jobid'];
        $WORKING['STEPSDONE'][]='DEST_S3'; //set done
      } else {
        trigger_error(sprintf(__('Can not transfer backup to S3! (%1$d) %2$s','backwpup'),$result["status"],$result["Message"]),E_USER_ERROR);
      }
    } else {
      trigger_error(sprintf(__('S3 Bucket "%s" not exists!','backwpup'),$STATIC['JOB']['awsBucket']),E_USER_ERROR);
    }
  } catch (Exception $e) {
    trigger_error(sprintf(__('Amazon API: %s','backwpup'),$e->getMessage()),E_USER_ERROR);
    return;
  }
  try {
    if ($s3->if_bucket_exists($STATIC['JOB']['awsBucket'])) {
      if ($STATIC['JOB']['awsmaxbackups']>0) { //Delete old backups
        $backupfilelist=array();
        if (($contents = $s3->list_objects($STATIC['JOB']['awsBucket'],array('prefix'=>$STATIC['JOB']['awsdir']))) !== false) {
          foreach ($contents->body->Contents as $object) {
            $file=basename($object->Key);
            if ($STATIC['JOB']['fileprefix'] == substr($file,0,strlen($STATIC['JOB']['fileprefix'])) and $STATIC['JOB']['fileformart'] == substr($file,-strlen($STATIC['JOB']['fileformart'])))
              $backupfilelist[]=$file;
          }
        }
        if (sizeof($backupfilelist)>0) {
          rsort($backupfilelist);
          $numdeltefiles=0;
          for ($i=$STATIC['JOB']['awsmaxbackups'];$i<sizeof($backupfilelist);$i++) {
            if ($s3->delete_object($STATIC['JOB']['awsBucket'], $STATIC['JOB']['awsdir'].$backupfilelist[$i])) //delte files on S3
              $numdeltefiles++;
            else
              trigger_error(sprintf(__('Can not delete backup on S3://%s','backwpup'),$STATIC['JOB']['awsBucket'].'/'.$STATIC['JOB']['awsdir'].$backupfilelist[$i]),E_USER_ERROR);
          }
          if ($numdeltefiles>0)
            trigger_error(sprintf(_n('One file deleted on S3 Bucket','%d files deleted on S3 Bucket',$numdeltefiles,'backwpup'),$numdeltefiles),E_USER_NOTICE);
        }
      }
    }
  } catch (Exception $e) {
    trigger_error(sprintf(__('Amazon API: %s','backwpup'),$e->getMessage()),E_USER_ERROR);
    return;
  }

  $WORKING['STEPDONE']++;
}