<?PHP
function backwpup_read_logfile($logfile) {
	if (is_file($logfile) and substr($logfile,-3)=='.gz')
		$logfiledata=gzfile($logfile);
	elseif (is_file($logfile.'.gz'))
		$logfiledata=gzfile($logfile.'.gz');
	elseif (is_file($logfile))
		$logfiledata=file($logfile);	
	else
		return array();
	$lines=array();
	$start=false;
	foreach ($logfiledata as $line){
		$line=trim($line);
		if (strripos($line,'<body')!== false) {  // jop over header
			$start=true;
			continue;
		}
		if ($line!='</body>' and $line!='</html>' and $start) //no Footer
			$lines[]=$line;
	}
	return $lines;
}

//read log file header
function backwpup_read_logheader($logfile) {
	$headers=array("backwpup_version" => "version","backwpup_logtime" => "logtime","backwpup_errors" => "errors","backwpup_warnings" => "warnings","backwpup_jobid" => "jobid","backwpup_jobname" => "name","backwpup_jobtype" => "type","backwpup_jobruntime" => "runtime","backwpup_backupfilesize" => "backupfilesize");
	if (!is_readable($logfile))
		return false;
	//Read file
	if (substr($logfile,-3)==".gz") {
		$fp = gzopen( $logfile, 'r' );
		$file_data = gzread( $fp, 1536 ); // Pull only the first 1,5kiB of the file in.
		gzclose( $fp );
	} else {
		$fp = fopen( $logfile, 'r' );
		$file_data = fread( $fp, 1536 ); // Pull only the first 1,5kiB of the file in.
		fclose( $fp );
	}
	//get data form file
	foreach ($headers as $keyword => $field) {
		preg_match('/(<meta name="'.$keyword.'" content="(.*)" \/>)/i',$file_data,$content);
		if (!empty($content))
			$joddata[$field]=$content[2];
		else
			$joddata[$field]='';
	}
	if (empty($joddata['logtime']))
		$joddata['logtime']=filectime($logfile);
	return $joddata;
}

if (isset($_POST['logfile']))
	$logfile=realpath($_POST['logfile']);
if (substr($logfile,-5)!='.html' && substr(basename($logfile),0,13)!='backwpup_log_')
	die();

if (is_file($logfile.'.gz'))
	$logfile.='.gz';
	
$backwpupjobtemp=str_replace('\\','/',dirname(__FILE__).'/../tmp/');
$backwpupjobtemp=rtrim(realpath($backwpupjobtemp),'/');	

$log='';
if (is_file($logfile)) {
	$logpos=(int)$_POST['logpos'];
	$logfilarray=backwpup_read_logfile($logfile);
	$newpos=count($logfilarray);
	for ($i=$logpos;$i<count($logfilarray);$i++)
			$log.=$logfilarray[$i];
	if (!empty($newpos) && $newpos>0)
		$logpos=$newpos;
	
	if (is_file($backwpupjobtemp.'/.running') && $runningfile=file_get_contents($backwpupjobtemp.'/.running')) {
		$infile=unserialize($runningfile);
		$warnings=$infile['WORKING']['WARNING'];
		$errors=$infile['WORKING']['ERROR'];
		$stepspersent=$infile['STEPSPERSENT'];
		$steppersent=$infile['STEPPERSENT'];
	} else {
		$logheader=backwpup_read_logheader($logfile);
		$warnings=$logheader['warnings'];
		$errors=$logheader['errors'];
		$stepspersent=100;
		$steppersent=100;
		$log.='<span id="stopworking"></span>';		
	}
	echo json_encode(array('logpos'=>$logpos,'LOG'=>$log,'WARNING'=>$warnings,'ERROR'=>$errors,'STEPSPERSENT'=>$stepspersent,'STEPPERSENT'=>$steppersent));
}
die();