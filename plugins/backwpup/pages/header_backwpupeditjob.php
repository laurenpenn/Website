<?PHP
if (!defined('ABSPATH'))
  die();

//Save Dropbox  settings
if (isset($_GET['dropboxauth']) and $_GET['dropboxauth']=='AccessToken')  {
  $jobid = (int) $_GET['jobid'];
  check_admin_referer('edit-job');
  $backwpup_message='';
  if ((int)$_GET['uid']>0 and !empty($_GET['oauth_token'])) {
    $reqtoken=get_transient('backwpup_dropboxrequest');
    if ($reqtoken['oAuthRequestToken']==$_GET['oauth_token']) {
      //Get Access Tokens
      require_once (dirname(__FILE__).'/../libs/dropbox.php');
      $jobs=get_option('backwpup_jobs');
      $dropbox = new backwpup_Dropbox('dropbox');
      $oAuthStuff = $dropbox->oAuthAccessToken($reqtoken['oAuthRequestToken'],$reqtoken['oAuthRequestTokenSecret']);
      //Save Tokens
      $jobs[$jobid]['dropetoken']=$oAuthStuff['oauth_token'];
      $jobs[$jobid]['dropesecret']=$oAuthStuff['oauth_token_secret'];
      update_option('backwpup_jobs',$jobs);
      $backwpup_message.=__('Dropbox authentication complete!','backwpup').'<br />';
    } else {
      $backwpup_message.=__('Wrong Token for Dropbox authentication received!','backwpup').'<br />';
    }
  } else {
    $backwpup_message.=__('No Dropbox authentication received!','backwpup').'<br />';
  }
  delete_transient('backwpup_dropboxrequest');
  $_POST['jobid']=$jobid;
}

//Save Job settings
if ((isset($_POST['savebackwpup']) or isset($_POST['dropboxauth']) or isset($_POST['dropboxauthdel']) or isset($_POST['authbutton'])) and !empty($_POST['jobid'])) {
  check_admin_referer('edit-job');
  $jobvalues['jobid']=(int) $_POST['jobid'];
  $jobvalues['type']= implode('+',(array)$_POST['type']);
  $jobvalues['name']= esc_html($_POST['name']);
  $jobvalues['activated']= (isset($_POST['activated']) && $_POST['activated']==1) ? true : false;
  $jobvalues['cronselect']= $_POST['cronselect']=='basic' ? 'basic':'advanced';
  if ($jobvalues['cronselect']=='advanced') {
    if (empty($_POST['cronminutes']) or $_POST['cronminutes'][0]=='*') {
      if (!empty($_POST['cronminutes'][1]))
        $_POST['cronminutes']=array('*/'.$_POST['cronminutes'][1]);
      else
        $_POST['cronminutes']=array('*');
    }
    if (empty($_POST['cronhours']) or $_POST['cronhours'][0]=='*') {
      if (!empty($_POST['cronhours'][1]))
        $_POST['cronhours']=array('*/'.$_POST['cronhours'][1]);
      else
        $_POST['cronhours']=array('*');
    }
    if (empty($_POST['cronmday']) or $_POST['cronmday'][0]=='*') {
      if (!empty($_POST['cronmday'][1]))
        $_POST['cronmday']=array('*/'.$_POST['cronmday'][1]);
      else
        $_POST['cronmday']=array('*');
    }
    if (empty($_POST['cronmon']) or $_POST['cronmon'][0]=='*') {
      if (!empty($_POST['cronmon'][1]))
        $_POST['cronmon']=array('*/'.$_POST['cronmon'][1]);
      else
        $_POST['cronmon']=array('*');
    }
    if (empty($_POST['cronwday']) or $_POST['cronwday'][0]=='*') {
      if (!empty($_POST['cronwday'][1]))
        $_POST['cronwday']=array('*/'.$_POST['cronwday'][1]);
      else
        $_POST['cronwday']=array('*');
    }
    $jobvalues['cron']=implode(",",$_POST['cronminutes']).' '.implode(",",$_POST['cronhours']).' '.implode(",",$_POST['cronmday']).' '.implode(",",$_POST['cronmon']).' '.implode(",",$_POST['cronwday']);
  } else {
    if ($_POST['cronbtype']=='mon') {
      $jobvalues['cron']=$_POST['moncronminutes'].' '.$_POST['moncronhours'].' '.$_POST['moncronmday'].' * *';
    }
    if ($_POST['cronbtype']=='week') {
      $jobvalues['cron']=$_POST['weekcronminutes'].' '.$_POST['weekcronhours'].' * * '.$_POST['weekcronwday'];
    }
    if ($_POST['cronbtype']=='day') {
      $jobvalues['cron']=$_POST['daycronminutes'].' '.$_POST['daycronhours'].' * * *';
    }
    if ($_POST['cronbtype']=='hour') {
      $jobvalues['cron']=$_POST['hourcronminutes'].' * * * *';
    }
  }
  $jobvalues['cronnextrun']=backwpup_cron_next($jobvalues['cron']);
  $jobvalues['mailaddresslog']= isset($_POST['mailaddresslog']) ? sanitize_email($_POST['mailaddresslog']) : '';
  $jobvalues['mailerroronly']= (isset($_POST['mailerroronly']) && $_POST['mailerroronly']==1) ? true : false;
  $checedtables=array();
  if (isset($_POST['jobtabs'])) {
    foreach ($_POST['jobtabs'] as $dbtable) {
      $checedtables[]=backwpup_base64($dbtable);
    }
  }
  global $wpdb;
  $tables=$wpdb->get_col('SHOW TABLES FROM `'.DB_NAME.'`');
  $jobvalues['dbexclude']=array();
  foreach ($tables as $dbtable) {
    if (!in_array($dbtable,$checedtables))
      $jobvalues['dbexclude'][]=$dbtable;
  }
  $jobvalues['dbshortinsert']= (isset($_POST['dbshortinsert']) && $_POST['dbshortinsert']==1) ? true : false;
  $jobvalues['maintenance']= (isset($_POST['maintenance']) && $_POST['maintenance']==1) ? true : false;
  $jobvalues['fileexclude']=isset($_POST['fileexclude']) ? stripslashes($_POST['fileexclude']) : '';
  $jobvalues['dirinclude']=isset($_POST['dirinclude']) ? stripslashes($_POST['dirinclude']) : '';
  $jobvalues['backuproot']= (isset($_POST['backuproot']) && $_POST['backuproot']==1) ? true : false;
  $jobvalues['backuprootexcludedirs']=!empty($_POST['backuprootexcludedirs']) ? (array)$_POST['backuprootexcludedirs'] : array();
  $jobvalues['backupcontent']= (isset($_POST['backupcontent']) && $_POST['backupcontent']==1) ? true : false;
  $jobvalues['backupcontentexcludedirs']=!empty($_POST['backupcontentexcludedirs']) ? (array)$_POST['backupcontentexcludedirs'] : array();
  $jobvalues['backupplugins']= (isset($_POST['backupplugins']) && $_POST['backupplugins']==1) ? true : false;
  $jobvalues['backuppluginsexcludedirs']=!empty($_POST['backuppluginsexcludedirs']) ? (array)$_POST['backuppluginsexcludedirs'] : array();
  $jobvalues['backupthemes']= (isset($_POST['backupthemes']) && $_POST['backupthemes']==1) ? true : false;
  $jobvalues['backupthemesexcludedirs']=!empty($_POST['backupthemesexcludedirs']) ? (array)$_POST['backupthemesexcludedirs'] : array();
  $jobvalues['backupuploads']= (isset($_POST['backupuploads']) && $_POST['backupuploads']==1) ? true : false;
  $jobvalues['backupuploadsexcludedirs']=!empty($_POST['backupuploadsexcludedirs']) ? (array)$_POST['backupuploadsexcludedirs'] : array();
  $jobvalues['fileprefix']= isset($_POST['fileprefix']) ? $_POST['fileprefix'] : '';
  $jobvalues['fileformart']=$_POST['fileformart'];
  $jobvalues['mailefilesize']=isset($_POST['mailefilesize']) ? (float)$_POST['mailefilesize'] : 0;
  $jobvalues['backupdir']=isset($_POST['backupdir']) ? stripslashes($_POST['backupdir']) : '';
  $jobvalues['maxbackups']=isset($_POST['maxbackups']) ? (int)$_POST['maxbackups'] : 0;
  $jobvalues['ftphost']=isset($_POST['ftphost']) ? $_POST['ftphost'] : '';
  $jobvalues['ftphostport']=!empty($_POST['ftphostport']) ? (int)$_POST['ftphostport'] : 21;
  $jobvalues['ftpuser']=isset($_POST['ftpuser']) ? $_POST['ftpuser'] : '';
  $jobvalues['ftppass']=isset($_POST['ftppass']) ? base64_encode($_POST['ftppass']) : '';
  $jobvalues['ftpdir']=isset($_POST['ftpdir']) ? stripslashes($_POST['ftpdir']) : '';
  $jobvalues['ftpmaxbackups']=isset($_POST['ftpmaxbackups']) ? (int)$_POST['ftpmaxbackups'] : 0;
  $jobvalues['ftpssl']= (isset($_POST['ftpssl']) && $_POST['ftpssl']==1) ? true : false;
  $jobvalues['ftppasv']= (isset($_POST['ftppasv']) && $_POST['ftppasv']==1) ? true : false;
  $jobvalues['dropemaxbackups']=isset($_POST['dropemaxbackups']) ? (int)$_POST['dropemaxbackups'] : 0;
  $jobvalues['droperoot']=isset($_POST['droperoot']) ? $_POST['droperoot'] : 'dropbox';
  $jobvalues['dropedir']=isset($_POST['dropedir']) ? $_POST['dropedir'] : '';
  $jobvalues['awsAccessKey']=isset($_POST['awsAccessKey']) ? $_POST['awsAccessKey'] : '';
  $jobvalues['awsSecretKey']=isset($_POST['awsSecretKey']) ? $_POST['awsSecretKey'] : '';
  $jobvalues['awsrrs']= (isset($_POST['awsrrs']) && $_POST['awsrrs']==1) ? true : false;
  $jobvalues['awsBucket']=isset($_POST['awsBucket']) ? $_POST['awsBucket'] : '';
  $jobvalues['awsdir']=isset($_POST['awsdir']) ? stripslashes($_POST['awsdir']) : '';
  $jobvalues['awsmaxbackups']=isset($_POST['awsmaxbackups']) ? (int)$_POST['awsmaxbackups'] : 0;
  $jobvalues['GStorageAccessKey']=isset($_POST['GStorageAccessKey']) ? $_POST['GStorageAccessKey'] : '';
  $jobvalues['GStorageSecret']=isset($_POST['GStorageSecret']) ? $_POST['GStorageSecret'] : '';
  $jobvalues['GStorageBucket']=isset($_POST['GStorageBucket']) ? $_POST['GStorageBucket'] : '';
  $jobvalues['GStoragedir']=isset($_POST['GStoragedir']) ? stripslashes($_POST['GStoragedir']) : '';
  $jobvalues['GStoragemaxbackups']=isset($_POST['GStoragemaxbackups']) ? (int)$_POST['GStoragemaxbackups'] : 0;
  $jobvalues['msazureHost']=isset($_POST['msazureHost']) ? $_POST['msazureHost'] : 'blob.core.windows.net';
  $jobvalues['msazureAccName']=isset($_POST['msazureAccName']) ? $_POST['msazureAccName'] : '';
  $jobvalues['msazureKey']=isset($_POST['msazureKey']) ? $_POST['msazureKey'] : '';
  $jobvalues['msazureContainer']=isset($_POST['msazureContainer']) ? $_POST['msazureContainer'] : '';
  $jobvalues['msazuredir']=isset($_POST['msazuredir']) ? stripslashes($_POST['msazuredir']) : '';
  $jobvalues['msazuremaxbackups']=isset($_POST['msazuremaxbackups']) ? (int)$_POST['msazuremaxbackups'] : 0;
  $jobvalues['sugardir']=isset($_POST['sugardir']) ? stripslashes($_POST['sugardir']) : '';
  $jobvalues['sugarroot']=isset($_POST['sugarroot']) ? $_POST['sugarroot'] : '';
  $jobvalues['sugarmaxbackups']=isset($_POST['sugarmaxbackups']) ? (int)$_POST['sugarmaxbackups'] : 0;
  $jobvalues['rscUsername']=isset($_POST['rscUsername']) ? $_POST['rscUsername'] : '';
  $jobvalues['rscAPIKey']=isset($_POST['rscAPIKey']) ? $_POST['rscAPIKey'] : '';
  $jobvalues['rscContainer']=isset($_POST['rscContainer']) ? $_POST['rscContainer'] : '';
  $jobvalues['rscdir']=isset($_POST['rscdir']) ? stripslashes($_POST['rscdir']) : '';
  $jobvalues['rscmaxbackups']=isset($_POST['rscmaxbackups']) ? (int)$_POST['rscmaxbackups'] : 0;
  $jobvalues['mailaddress']=isset($_POST['mailaddress']) ? sanitize_email($_POST['mailaddress']) : '';


  if (!empty($_POST['newawsBucket']) and !empty($_POST['awsAccessKey']) and !empty($_POST['awsSecretKey'])) { //create new s3 bucket if needed
    if (!class_exists('CFRuntime'))
      require_once(dirname(__FILE__).'/../libs/aws/sdk.class.php');
    try {
	  CFCredentials::set(array('backwpup' => array('key'=>$_POST['awsAccessKey'],'secret'=>$_POST['awsSecretKey'],'default_cache_config'=>'','certificate_authority'=>true),'@default' => 'backwpup'));
      $s3 = new AmazonS3();
      $s3->create_bucket($_POST['newawsBucket'], $_POST['awsRegion']);
      $jobvalues['awsBucket']=$_POST['newawsBucket'];
    } catch (Exception $e) {
      $backwpup_message.=__($e->getMessage(),'backwpup').'<br />';
    }
  }

  if (!empty($_POST['GStorageAccessKey']) and !empty($_POST['GStorageSecret']) and !empty($_POST['newGStorageBucket'])) { //create new google storage bucket if needed
    if (!class_exists('CFRuntime'))
      require_once(dirname(__FILE__).'/../libs/aws/sdk.class.php');
    try {
	  CFCredentials::set(array('backwpup' => array('key'=>$_POST['GStorageAccessKey'],'secret'=>$_POST['GStorageSecret'],'default_cache_config'=>'','certificate_authority'=>true),'@default' => 'backwpup'));
      $gstorage = new AmazonS3();
      $gstorage->set_hostname('storage.googleapis.com');
      $gstorage->allow_hostname_override(false);
      $gstorage->create_bucket($_POST['newGStorageBucket'],'');
      $jobvalues['GStorageBucket']=$_POST['newGStorageBucket'];
      sleep(1); //creation take a moment
    } catch (Exception $e) {
      $backwpup_message.=__($e->getMessage(),'backwpup').'<br />';
    }
  }

  if (!empty($_POST['newmsazureContainer'])  and !empty($_POST['msazureHost']) and !empty($_POST['msazureAccName']) and !empty($_POST['msazureKey'])) { //create new s3 bucket if needed
    if (!class_exists('Microsoft_WindowsAzure_Storage_Blob')) {
      require_once(dirname(__FILE__).'/../libs/Microsoft/WindowsAzure/Storage/Blob.php');
    }
    try {
      $storageClient = new Microsoft_WindowsAzure_Storage_Blob($_POST['msazureHost'],$_POST['msazureAccName'],$_POST['msazureKey']);
      $result = $storageClient->createContainer($_POST['newmsazureContainer']);
      $jobvalues['msazureContainer']=$result->Name;
    } catch (Exception $e) {
      $backwpup_message.=__($e->getMessage(),'backwpup').'<br />';
    }
  }

  if (!empty($_POST['rscUsername']) and !empty($_POST['rscAPIKey']) and !empty($_POST['newrscContainer'])) { //create new Rackspase Container if needed
    if (!class_exists('CF_Authentication'))
      require_once(dirname(__FILE__).'/../libs/rackspace/cloudfiles.php');
    try {
      $auth = new CF_Authentication($_POST['rscUsername'], $_POST['rscAPIKey']);
      if ($auth->authenticate()) {
        $conn = new CF_Connection($auth);
        $public_container = $conn->create_container($_POST['newrscContainer']);
        $public_container->make_private();
      }
    } catch (Exception $e) {
      $backwpup_message.=__($e->getMessage(),'backwpup').'<br />';
    }
  }


  if (isset($_POST['dropboxauthdel']) and !empty($_POST['dropboxauthdel'])) {
    $jobvalues['dropetoken']='';
    $jobvalues['dropesecret']='';
    $backwpup_message.=__('Dropbox authentication deleted!','backwpup').'<br />';
  }

	if (!empty($_POST['sugaremail']) && !empty($_POST['sugarpass']) && $_POST['authbutton']==__( 'Sugarsync authenticate!', 'backwpup' )) {
		if (!class_exists('SugarSync'))
			include_once(realpath(dirname(__FILE__).'/../libs/sugarsync.php'));
		try {
			$sugarsync = new SugarSync();
			$refresh_token=$sugarsync->get_Refresh_Token($_POST['sugaremail'],$_POST['sugarpass']);
			if (!empty($refresh_token)) {
				$jobvalues['sugarrefreshtoken']=$refresh_token;
				$backwpup_message.=__('SugarSync authentication complete!','backwpup').'<br />';
			}
		} catch ( Exception $e ) {
			$backwpup_message.= 'SUGARSYNC: ' . $e->getMessage() . '<br />';
		}
	}
	if (isset($_POST['authbutton']) && $_POST['authbutton']==__( 'Delete Sugarsync authentication!', 'backwpup' )) {
		$jobvalues['sugarrefreshtoken']='';
		$backwpup_message.=__('SugarSync authentication deleted!','backwpup').'<br />';
	}
	if (isset($_POST['authbutton']) && $_POST['authbutton']==__( 'Create Sugarsync Account', 'backwpup' )) {
		if (!class_exists('SugarSync'))
			include_once(realpath(dirname(__FILE__).'/../libs/sugarsync.php'));
		try {
			$sugarsync = new SugarSync();
			$sugarsync->create_account($_POST['sugaremail'],$_POST['sugarpass']);
			$backwpup_message.=__('SugarSync account created!','backwpup').'<br />';
		} catch ( Exception $e ) {
			$backwpup_message.= 'SUGARSYNC: ' . $e->getMessage() . '<br />';
		}
	}

  //save chages
  $jobs=get_option('backwpup_jobs'); //Load Settings
  $jobs[$jobvalues['jobid']]=backwpup_get_job_vars($jobvalues['jobid'],$jobvalues);
  update_option('backwpup_jobs',$jobs);

  //activate/deactivate seduling if not needed
  $activejobs=false;
  foreach ($jobs as $jobid => $jobvalue) {
    if (!empty($jobvalue['activated']))
      $activejobs=true;
  }
  if ($activejobs and false === wp_next_scheduled('backwpup_cron')) {
    wp_schedule_event(time(), 'backwpup_int', 'backwpup_cron');
  }
  if (!$activejobs and false !== wp_next_scheduled('backwpup_cron')) {
    wp_clear_scheduled_hook('backwpup_cron');
  }

  //get dropbox auth
  if (isset($_POST['dropboxauth']) and !empty($_POST['dropboxauth'])) {
    require_once (dirname(__FILE__).'/../libs/dropbox.php');
    $dropbox = new backwpup_Dropbox('dropbox');
    // let the user authorize (user will be redirected)
    $response = $dropbox->oAuthAuthorize(backwpup_admin_url('admin.php').'?page=backwpupeditjob&jobid='.$jobvalues['jobid'].'&dropboxauth=AccessToken&_wpnonce='.wp_create_nonce('edit-job'));
    // save oauth_token_secret
    set_transient('backwpup_dropboxrequest',array('oAuthRequestToken'=>$response['oauth_token'],'oAuthRequestTokenSecret' => $response['oauth_token_secret']),600);
    //forward to auth page
    wp_redirect($response['authurl']);
  }

  $_POST['jobid']=$jobvalues['jobid'];
  $backwpup_message.=str_replace('%1',$jobvalues['name'],__('Job \'%1\' changes saved.', 'backwpup')).' <a href="'.backwpup_admin_url('admin.php').'?page=backwpup">'.__('Jobs overview.', 'backwpup').'</a>';
}

//load java
wp_enqueue_script('common');
wp_enqueue_script('wp-lists');
wp_enqueue_script('postbox');

//add columns
add_screen_option('layout_columns', array('max' => 2, 'default' => 2));

//add Help
backwpup_contextual_help();