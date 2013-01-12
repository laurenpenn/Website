<?PHP
function backup_create() {
	global $WORKING,$STATIC;
	if ($WORKING['ALLFILESIZE']==0)
		return;
	$filelist=get_filelist(); //get file list
	$WORKING['STEPTODO']=count($filelist);
	if (empty($WORKING['STEPDONE']))
		$WORKING['STEPDONE']=0;

	if (strtolower($STATIC['JOB']['fileformart'])==".zip") { //Zip files
		if ($STATIC['CFG']['phpzip']) {  //use php zip lib
			trigger_error(sprintf(__('%d. try to create backup zip archive...','backwpup'),$WORKING['BACKUP_CREATE']['STEP_TRY']),E_USER_NOTICE);
			$zip = new ZipArchive();
			if ($res=$zip->open($STATIC['JOB']['backupdir'].$STATIC['backupfile'],ZIPARCHIVE::CREATE) === TRUE) {
				for ($i=$WORKING['STEPDONE'];$i<$WORKING['STEPTODO'];$i++) {
					if (!$zip->addFile($filelist[$i]['FILE'], $filelist[$i]['OUTFILE']))
						trigger_error(sprintf(__('Can not add "%s" to zip archive!','backwpup'),$filelist[$i]['OUTFILE']),E_USER_ERROR);
					$WORKING['STEPDONE']++;
					update_working_file();
				}
				if ($zip->status>0) {
					$ziperror=$zip->status;
					if ($zip->status==4)
						$ziperror=__('(4) ER_SEEK','backwpup');
					if ($zip->status==5)
						$ziperror=__('(5) ER_READ','backwpup');
					if ($zip->status==9)
						$ziperror=__('(9) ER_NOENT','backwpup');
					if ($zip->status==10)
						$ziperror=__('(10) ER_EXISTS','backwpup');
					if ($zip->status==11)
						$ziperror=__('(11) ER_OPEN','backwpup');
					if ($zip->status==14)
						$ziperror=__('(14) ER_MEMORY','backwpup');
					if ($zip->status==18)
						$ziperror=__('(18) ER_INVAL','backwpup');
					if ($zip->status==19)
						$ziperror=__('(19) ER_NOZIP','backwpup');
					if ($zip->status==21)
						$ziperror=__('(21) ER_INCONS','backwpup');
					trigger_error(sprintf(__('Zip returns status: %s','backwpup'),$zip->status),E_USER_ERROR);
				}
				$res2=$zip->close();
				trigger_error(__('Backup zip archive create done!','backwpup'),E_USER_NOTICE);
				$WORKING['STEPSDONE'][]='BACKUP_CREATE'; //set done
			} else {
				trigger_error(sprintf(__('Can not create backup zip archive $s!','backwpup'),$res),E_USER_ERROR);
			}
		} else { //use PclZip
			define('PCLZIP_TEMPORARY_DIR', $STATIC['TEMPDIR']);
			require_once($STATIC['WP']['ABSPATH'].'wp-admin/includes/class-pclzip.php');
			if ( ini_get( 'mbstring.func_overload' ) && function_exists( 'mb_internal_encoding' ) ) {
				$previous_encoding = mb_internal_encoding();
				mb_internal_encoding( 'ISO-8859-1' );
			}
			//Create Zip File
			if (is_array($filelist[0])) {
				trigger_error(sprintf(__('%d. try to create backup zip (PclZip) archive...','backwpup'),$WORKING['BACKUP_CREATE']['STEP_TRY']),E_USER_NOTICE);
				for ($i=0;$i<$WORKING['STEPTODO'];$i++) { //must begin at 0 for PCLzip
					$files[$i][79001]=$filelist[$i]['FILE'];
					$files[$i][79003]=$filelist[$i]['OUTFILE'];
					$files[$i][79004]=$filelist[$i]['MTIME'];
				}
				need_free_memory('20M'); //20MB free memory for zip
				$zipbackupfile = new PclZip($STATIC['JOB']['backupdir'].$STATIC['backupfile']);
				if (0==$zipbackupfile->create($files,PCLZIP_CB_POST_ADD,'_pclzipPostAddCallBack',PCLZIP_OPT_TEMP_FILE_THRESHOLD, 5)) {
					trigger_error(sprintf(__('Zip archive create error: %s','backwpup'),$zipbackupfile->errorInfo(true)),E_USER_ERROR);
				} else {
					$WORKING['STEPDONE']=count($filelist);
					unset($files);
					trigger_error(__('Backup zip archive create done','backwpup'),E_USER_NOTICE);
				}
			}
			if ( isset($previous_encoding) )
				mb_internal_encoding( $previous_encoding );
		}
	} elseif (strtolower($STATIC['JOB']['fileformart'])==".tar.gz" or strtolower($STATIC['JOB']['fileformart'])==".tar.bz2" or strtolower($STATIC['JOB']['fileformart'])==".tar") { //tar files

		if (strtolower($STATIC['JOB']['fileformart'])=='.tar.gz') {
			$tarbackup=fopen('compress.zlib://'.$STATIC['JOB']['backupdir'].$STATIC['backupfile'],'wb');
		} elseif (strtolower($STATIC['JOB']['fileformart'])=='.tar.bz2') {
			$tarbackup=fopen('compress.bzip2://'.$STATIC['JOB']['backupdir'].$STATIC['backupfile'],'wb');
		} else {
			$tarbackup=fopen($STATIC['JOB']['backupdir'].$STATIC['backupfile'],'wb');
		}

		if (!$tarbackup) {
			trigger_error(__('Can not create tar archive file!','backwpup'),E_USER_ERROR);
			return;
		} else {
			trigger_error(sprintf(__('%1$d. try to create %2$s archive file...','backwpup'),$WORKING['BACKUP_CREATE']['STEP_TRY'],substr($STATIC['JOB']['fileformart'],1)),E_USER_NOTICE);
		}

		for ($index=$WORKING['STEPDONE'];$index<$WORKING['STEPTODO'];$index++) {
			need_free_memory(2097152); //2MB free memory for tar
			$files=$filelist[$index];
			//check file readable
			if ( empty($files['FILE']) or !file_exists($files['FILE']) or !is_readable($files['FILE'])) {
				trigger_error(sprintf(__('File "%s" not readable!','backwpup'),$files['FILE']),E_USER_WARNING);
				$WORKING['STEPDONE']++;
				continue;
			}

			//split filename larger than 100 chars
			if (strlen($files['OUTFILE'])<=100) {
				$filename=$files['OUTFILE'];
				$filenameprefix="";
			} else {
				$filenameofset=strlen($files['OUTFILE'])-100;
				$dividor=strpos($files['OUTFILE'],'/',$filenameofset);
				$filename=substr($files['OUTFILE'],$dividor+1);
				$filenameprefix=substr($files['OUTFILE'],0,$dividor);
				if (strlen($filename)>100)
					trigger_error(sprintf(__('File name "%1$s" to long to save corectly in %2$s archive!','backwpup'),$files['OUTFILE'],substr($STATIC['JOB']['fileformart'],1)),E_USER_WARNING);
				if (strlen($filenameprefix)>155)
					trigger_error(sprintf(__('File path "%1$s" to long to save corectly in %2$s archive!','backwpup'),$files['OUTFILE'],substr($STATIC['JOB']['fileformart'],1)),E_USER_WARNING);
			}
			//Set file user/group name if linux
			$fileowner="Unknown";
			$filegroup="Unknown";
			if (function_exists('posix_getpwuid')) {
				$info=posix_getpwuid($files['UID']);
				$fileowner=$info['name'];
				$info=posix_getgrgid($files['GID']);
				$filegroup=$info['name'];
			}

			// Generate the TAR header for this file
			$header = pack("a100a8a8a8a12a12a8a1a100a6a2a32a32a8a8a155a12",
					  $filename,  									//name of file  100
					  sprintf("%07o", $files['MODE']), 				//file mode  8
					  sprintf("%07o", $files['UID']),				//owner user ID  8
					  sprintf("%07o", $files['GID']),				//owner group ID  8
					  sprintf("%011o", $files['SIZE']),				//length of file in bytes  12
					  sprintf("%011o", $files['MTIME']),			//modify time of file  12
					  "        ",									//checksum for header  8
					  0,											//type of file  0 or null = File, 5=Dir
					  "",											//name of linked file  100
					  "ustar",										//USTAR indicator  6
					  "00",											//USTAR version  2
					  $fileowner,									//owner user name 32
					  $filegroup,									//owner group name 32
					  "",											//device major number 8
					  "",											//device minor number 8
					  $filenameprefix,								//prefix for file name 155
					  "");											//fill block 512K

			// Computes the unsigned Checksum of a file's header
			$checksum = 0;
			for ($i = 0; $i < 512; $i++)
				$checksum += ord(substr($header, $i, 1));
			$checksum = pack("a8", sprintf("%07o", $checksum));

			$header = substr_replace($header, $checksum, 148, 8);

			fwrite($tarbackup, $header);

			// read/write files in 512K Blocks
			if ($fd=fopen($files['FILE'],'rb')) {
				while(!feof($fd)) {
					$filedata=fread($fd,512);
					if (strlen($filedata)>0)
						fwrite($tarbackup,pack("a512", $filedata));
				}
				fclose($fd);
			}
			$WORKING['STEPDONE']++;
			update_working_file();
		}
        fwrite($tarbackup, pack("a1024", "")); // Add 1024 bytes of NULLs to designate EOF
        fclose($tarbackup);

		trigger_error(sprintf(__('%s archive creation done','backwpup'),substr($STATIC['JOB']['fileformart'],1)),E_USER_NOTICE);
	}
	$WORKING['STEPSDONE'][]='BACKUP_CREATE'; //set done
	if ($filesize=filesize($STATIC['JOB']['backupdir'].$STATIC['backupfile']))
		trigger_error(sprintf(__('Archive size is %s','backwpup'),formatBytes($filesize)),E_USER_NOTICE);
}


function _pclzipPostAddCallBack($p_event, &$p_header) {
	global $WORKING,$STATIC;
	if ($p_header['status'] != 'ok')
		trigger_error(sprintf(__('PCL ZIP Error "%1$s" on file %2$s!','backwpup'),$p_header['status'],$p_header['filename']),E_USER_ERROR);
	$WORKING['STEPDONE']++;
	update_working_file();
}