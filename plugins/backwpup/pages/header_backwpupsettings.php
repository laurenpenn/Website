<?PHP
if (!defined('ABSPATH')) 
	die();


if (isset($_POST['submit']) and isset($_POST['action']) and $_POST['action']=='update') {
	check_admin_referer('backwpup-cfg');
	$cfg=get_option('backwpup'); //Load Settings
	$cfg['mailsndemail']=sanitize_email($_POST['mailsndemail']);
	$cfg['mailsndname']=$_POST['mailsndname'];
	$cfg['mailmethod']=$_POST['mailmethod'];
	$cfg['mailsendmail']=untrailingslashit(str_replace('//','/',str_replace('\\','/',stripslashes($_POST['mailsendmail']))));
	$cfg['mailsecure']=$_POST['mailsecure'];
	$cfg['mailhost']=$_POST['mailhost'];
	$cfg['mailhostport']=empty($_POST['mailhostport']) ? 25 : (int)$_POST['mailhostport'];
	$cfg['mailuser']=$_POST['mailuser'];
	$cfg['mailpass']=base64_encode($_POST['mailpass']);
	$cfg['showadminbar']=isset($_POST['showadminbar']) ? true : false;
    if (100>$_POST['jobstepretry'] and 0<$_POST['jobstepretry']) 
		$cfg['jobstepretry']=(int)$_POST['jobstepretry'];
	if (100>$_POST['jobscriptretry'] and 0<$_POST['jobscriptretry']) 
		$cfg['jobscriptretry']=(int)$_POST['jobscriptretry'];
	if (empty($cfg['jobstepretry']) or !is_int($cfg['jobstepretry']))
		$cfg['jobstepretry']=3;
	if (empty($cfg['jobscriptretry']) or !is_int($cfg['jobscriptretry']))
		$cfg['jobscriptretry']=5;
	$cfg['maxlogs']=abs((int)$_POST['maxlogs']);
	$cfg['gzlogs']=isset($_POST['gzlogs']) ? true : false;
	$cfg['phpzip']=isset($_POST['phpzip']) ? true : false;
	$cfg['httpauthuser']=$_POST['httpauthuser'];
	$cfg['httpauthpassword']=base64_encode($_POST['httpauthpassword']);
	$cfg['dirlogs']=trailingslashit(str_replace('//','/',str_replace('\\','/',stripslashes(trim($_POST['dirlogs'])))));
	//set def. folders
	if (!isset($cfg['dirlogs']) or $cfg['dirlogs']=='/' or empty($cfg['dirlogs'])) {
		$rand = substr( md5( md5( SECURE_AUTH_KEY ) ), -5 );
		$cfg['dirlogs']=str_replace('\\','/',trailingslashit(WP_CONTENT_DIR)).'backwpup-'.$rand.'-logs/';
	}
	if (substr($cfg['dirlogs'],0,1)!='/' and substr($cfg['dirlogs'],1,1)!=':') //add abspath if not absolute
		$cfg['dirlogs']=rtrim(str_replace('\\','/',ABSPATH),'/').'/'.$cfg['dirlogs'];
	if (!isset($cfg['mailsendmail']) or empty($cfg['mailsendmail'])) {
		$cfg['mailsendmail']=substr(ini_get('sendmail_path'),0,strpos(ini_get('sendmail_path'),' -'));
	}
	if (update_option('backwpup',$cfg))
		$backwpup_message=__('Settings saved', 'backwpup');
}

//add Help
backwpup_contextual_help();