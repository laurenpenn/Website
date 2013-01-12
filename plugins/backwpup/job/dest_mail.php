<?PHP
function dest_mail() {
	global $WORKING,$STATIC;
	$WORKING['STEPTODO']=filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
	$WORKING['STEPDONE']=0;
	trigger_error(sprintf(__('%d. try to sending backup with mail...','backwpup'),$WORKING['DEST_MAIL']['STEP_TRY']),E_USER_NOTICE);

	//Create PHP Mailer
	require_once(realpath($STATIC['WP']['ABSPATH'].$STATIC['WP']['WPINC']).'/class-phpmailer.php');
	$phpmailer = new PHPMailer();
    $phpmailer->CharSet=$STATIC['WP']['CHARSET'];
	//Setting den methode
	if ($STATIC['CFG']['mailmethod']=="SMTP") {
		require_once(realpath($STATIC['WP']['ABSPATH'].$STATIC['WP']['WPINC']).'/class-smtp.php');
		$phpmailer->Host=$STATIC['CFG']['mailhost'];
		$phpmailer->Port=$STATIC['CFG']['mailhostport'];
		$phpmailer->SMTPSecure=$STATIC['CFG']['mailsecure'];
		$phpmailer->Username=$STATIC['CFG']['mailuser'];
		$phpmailer->Password=backwpup_base64($STATIC['CFG']['mailpass']);
		if (!empty($STATIC['CFG']['mailuser']) and !empty($STATIC['CFG']['mailpass']))
			$phpmailer->SMTPAuth=true;
		$phpmailer->IsSMTP();
		trigger_error(__('Send mail with SMTP','backwpup'),E_USER_NOTICE);
	} elseif ($STATIC['CFG']['mailmethod']=="Sendmail") {
		$phpmailer->Sendmail=$STATIC['CFG']['mailsendmail'];
		$phpmailer->IsSendmail();
		trigger_error(__('Send mail with Sendmail','backwpup'),E_USER_NOTICE);
	} else {
		$phpmailer->IsMail();
		trigger_error(__('Send mail with PHP mail','backwpup'),E_USER_NOTICE);
	}

	trigger_error(__('Creating mail','backwpup'),E_USER_NOTICE);
	$phpmailer->From     = $STATIC['CFG']['mailsndemail'];
	$phpmailer->FromName = $STATIC['CFG']['mailsndname'];
	$phpmailer->AddAddress($STATIC['JOB']['mailaddress']);
	$phpmailer->Subject  =  sprintf(__('BackWPup archive from %1$s: %2$s','backwpup'),date('Y/m/d @ H:i',$STATIC['JOB']['starttime']+$STATIC['WP']['TIMEDIFF']),$STATIC['JOB']['name']);
	$phpmailer->IsHTML(false);
	$phpmailer->Body  =  sprintf(__('Backup archive: %s','backwpup'),$STATIC['backupfile']);

	//check file Size
	if (!empty($STATIC['JOB']['mailefilesize'])) {
		if (filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile'])>abs($STATIC['JOB']['mailefilesize']*1024*1024)) {
			trigger_error(__('Backup archive too big for sending by mail!','backwpup'),E_USER_ERROR);
			$WORKING['STEPDONE']=1;
			$WORKING['STEPSDONE'][]='DEST_MAIL'; //set done
			return;
		}
	}

	trigger_error(__('Adding backup archive to mail','backwpup'),E_USER_NOTICE);
	need_free_memory(filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile'])*6);
	$phpmailer->AddAttachment($STATIC['JOB']['backupdir'].$STATIC['backupfile']);

	trigger_error(__('Send mail....','backwpup'),E_USER_NOTICE);
	if (false == $phpmailer->Send()) {
		trigger_error(sprintf(__('Error "%s" on sending mail!','backwpup'),$phpmailer->ErrorInfo),E_USER_ERROR);
	} else {
		$WORKING['STEPTODO']=filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
		trigger_error(__('Mail send!!!','backwpup'),E_USER_NOTICE);
	}
	$WORKING['STEPSDONE'][]='DEST_MAIL'; //set done
}