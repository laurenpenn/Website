<?PHP	
// Remove header and footer form logfile
function backwpup_read_logfile($logfile) {
	if (is_file($logfile) and strtolower(substr($logfile,-3))=='.gz')
		$logfiledata=gzfile($logfile);
	elseif (is_file($logfile.'.gz'))
		$logfiledata=gzfile($logfile.'.gz');
	elseif (is_file($logfile))
		$logfiledata=file($logfile);	
	else
		return false;
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