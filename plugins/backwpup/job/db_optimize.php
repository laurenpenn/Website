<?PHP
function db_optimize() {
	global $WORKING,$STATIC;
	trigger_error(sprintf(__('%d. try for database optimize...','backwpup'),$WORKING['DB_OPTIMIZE']['STEP_TRY']),E_USER_NOTICE);
	if (!isset($WORKING['DB_OPTIMIZE']['DONETABLE']) or !is_array($WORKING['DB_OPTIMIZE']['DONETABLE']))
		$WORKING['DB_OPTIMIZE']['DONETABLE']=array();
	
	mysql_update();
	//to backup
	$tabelstobackup=array();
	$result=mysql_query("SHOW TABLES FROM `".$STATIC['WP']['DB_NAME']."`"); //get table status
	if (!$result)
		trigger_error(sprintf(__('Database error %1$s for query %2$s','backwpup'), mysql_error(), "SHOW TABLE STATUS FROM `".$STATIC['WP']['DB_NAME']."`;"),E_USER_ERROR);
	while ($data = mysql_fetch_row($result)) {
		if (!in_array($data[0],$STATIC['JOB']['dbexclude']))
			$tabelstobackup[]=$data[0];
	}	
	//Set num of todos
	$WORKING['STEPTODO']=count($tabelstobackup);
	
	if (count($tabelstobackup)>0) {
		maintenance_mode(true);
		foreach ($tabelstobackup as $table) {
			if (in_array($table, $WORKING['DB_OPTIMIZE']['DONETABLE']))
				continue;
			$result=mysql_query('OPTIMIZE TABLE `'.$table.'`');
			if (!$result) {
				trigger_error(sprintf(__('Database error %1$s for query %2$s','backwpup'), mysql_error(), "OPTIMIZE TABLE `".$table."`"),E_USER_ERROR);
				continue;
			}
			$optimize=mysql_fetch_assoc($result);
			$WORKING['DB_OPTIMIZE']['DONETABLE'][]=$table;
			$WORKING['STEPDONE']=count($WORKING['DB_OPTIMIZE']['DONETABLE']);
			if ($optimize['Msg_type']=='error')
				trigger_error(sprintf(__('Result of table optimize for %1$s is: %2$s','backwpup'), $table, $optimize['Msg_text']),E_USER_ERROR);
			elseif ($optimize['Msg_type']=='warning')
				trigger_error(sprintf(__('Result of table optimize for %1$s is: %2$s','backwpup'), $table, $optimize['Msg_text']),E_USER_WARNING);
			else
				trigger_error(sprintf(__('Result of table optimize for %1$s is: %2$s','backwpup'), $table, $optimize['Msg_text']),E_USER_NOTICE);
		}
		trigger_error(__('Database optimize done!','backwpup'),E_USER_NOTICE);
		maintenance_mode(false);
	} else {
		trigger_error(__('No tables to optimize','backwpup'),E_USER_WARNING);
	}
	$WORKING['STEPSDONE'][]='DB_OPTIMIZE'; //set done
}