<?php
# This file contains functions which are used during certain upgrade processes (wp-config.php should be included or required before this file gets included or required.)

/**
* podPress_upgrade_via_ajax - is used to be called from an AJAX reuest during the upgrade. It looks for the name of the desired action and starts it.
*
* @package podPress
* @since 8.8.10.14
*/
function podPress_upgrade_via_ajax($upgr_process_nr=0, $misc='') {
	if (FALSE === empty($upgr_process_nr)) {
		Switch ($upgr_process_nr) {
			case '11092011' :
				if ( TRUE == is_array($misc) AND isset($misc['increment']) AND FALSE === empty($misc['increment']) AND TRUE == is_array($misc) AND isset($misc['totalrows']) AND FALSE === empty($misc['totalrows'])) {
					if (FALSE === isset($misc['tablename']) OR ('stats' !== $misc['tablename'] AND 'statcounts' !== $misc['tablename']) ) {
						echo '{ "code": "error" }';
					} else {
						if ('stats' === $misc['tablename']) {
							$status = podPress_correct_stats_table( intval($misc['increment']), intval($misc['totalrows']) );
						} else {
							$status = podPress_correct_statcounts_table( intval($misc['increment']), intval($misc['totalrows']) );
						}
						echo '{ "code": "'.$upgr_process_nr.'", "time": "'.attribute_escape($status['elapsed_time']).'", "lastpos": '.$status['lastpos'].', "increment": '.$misc['increment'].' }';
					}
				} elseif ( TRUE == is_array($misc) AND isset($misc['startstopstats']) AND ('start' === $misc['startstopstats'] OR 'stop' === $misc['startstopstats']) ) {
					if ( 'stop' === $misc['startstopstats'] ) {
						podPress_stop_statistics();
					} else {
						podPress_start_statistics();
					}
				} else {
					echo '{ "code": "error" }';
				}
			break;
			default:
				die();
		}
	} else {
		die();
	}
}

function podPress_stop_statistics($action = 'stop') {
	$podpress_config = podPress_get_option('podPress_config');
	$do_update = FALSE;
	$result = FALSE;
	if ( TRUE === is_Array($podpress_config) AND TRUE === isset($podpress_config['enableStats']) ) {
		Switch ($action) {
			Case 'stop':
				$podpress_config['enableStats'] = FALSE;
				$do_update = TRUE;
				break;
			Case 'start':
				$podpress_config['enableStats'] = TRUE;
				$do_update = TRUE;
				break;
		}
		if ( TRUE === $do_update ) {
			$result = podPress_update_option('podPress_config', $podpress_config);
		}
	}
	echo '{ "code": "'.$podpress_config.' - '.$result.'" , "result": "'.$action.'" }';
}

function podPress_start_statistics() {
	 podPress_stop_statistics($action = 'start');
}

function podPress_correct_stats_table($increment = 10, $rows = 0) {
	GLOBAL $wpdb;
	if ( 0 >= $rows ) {
		return Array('elapsed_time' => 0, 'lastpos' => 0);
	}
	$time_start = microtime(true);
	$upgrade_status_data = get_option( '_podPress_upgrade' );
	$i = 0;
	if ( FALSE !== $upgrade_status_data AND TRUE === is_array($upgrade_status_data) ) {
		foreach ( $upgrade_status_data as $upgrade_action ) {
			if ( isset($upgrade_action['action']) AND 'podpress_update_stats_table' == $upgrade_action['action'] ) {
				break;
			}
			$i++;
		}
	} else {
		// no upgrade status found -> return
		return Array('elapsed_time' => 0, 'lastpos' => 0);
	}
	$upgrade_action['lastpos'] = intval($upgrade_action['lastpos']);
	if ( isset($upgrade_action['lastpos']) AND 0 < $upgrade_action['lastpos'] AND $upgrade_action['lastpos'] < $rows) {
		$lastpos = $upgrade_action['lastpos'];
	} else {
		$lastpos = 0;
	}
	// correct the entries in the wp_podpress_stats table (Full and Full+):
	// reserved characters like in http://en.wikipedia.org/wiki/Percent-encoding#Types_of_URI_characters but some characters which would not work in the media file names
	$reserved_chars = Array(' ' => '%20', '!' => '%21', '(' => '%28', ')' => '%29', ';' => '%3B', '@' => '%40', '&' => '%26', '=' => '%3D', '$' => '%24', ',' => '%2C', '[' => '%5B', ']' => '%5D');
	// Previous podPress versions stored the filenames as they were handed by the browser. The browsers hand encode only alphybetic non-ASCII characters but not these characters. The HTML5 player counter method has stored the results under the filename which has been encoded with rawurlencode() which encodes all the special characters.
	// In order to fix this the following db queries will replace the reserved unencoded characters in the media column with the encoded versions.
	$querystring = "UPDATE ".$wpdb->prefix."podpress_stats SET media = ";
	$subquerystring = 'media';
	foreach ( $reserved_chars as $chr_oldval => $chr_newval ) {
		$subquerystring = "REPLACE(".$subquerystring.", '".$chr_oldval."', '".$chr_newval."')";
	}
	$newlastpos = ($lastpos + $increment);
	$result = $wpdb->query($querystring.$subquerystring. " WHERE id IN(SELECT b.id FROM (SELECT a.id FROM ".$wpdb->prefix."podpress_stats as a LIMIT ".$lastpos.",".$increment.") as b);");
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	$upgrade_status_data[$i]['lastpos'] = $newlastpos;
	
	// if query result is okay then ...
	$result = update_option( '_podPress_upgrade', $upgrade_status_data );

	return Array('elapsed_time' => $time, 'lastpos' => $newlastpos);
}

function podPress_correct_statcounts_table($increment = 1, $rows = 0) {
	GLOBAL $wpdb;
	if ( 0 >= $rows ) {
		return Array('elapsed_time' => 0, 'lastpos' => 0);
	}
	$time_start = microtime(true);
	$upgrade_status_data = get_option( '_podPress_upgrade' );
	$i = 0;
	if ( FALSE !== $upgrade_status_data AND TRUE === is_array($upgrade_status_data) ) {
		foreach ( $upgrade_status_data as $upgrade_action ) {
			if ( isset($upgrade_action['action']) AND 'podpress_update_statcounts_table' == $upgrade_action['action'] ) {
				break;
			}
			$i++;
		}
	} else {
		// no upgrade status found -> return
		return Array('elapsed_time' => 0, 'lastpos' => 0);
	}
	if ( isset($upgrade_action['lastpos']) AND 0 < $upgrade_action['lastpos'] AND $upgrade_action['lastpos'] < $rows ) {
		$lastpos = intval($upgrade_action['lastpos']);
	} else {
		$lastpos = 0;
	}
	
	$querystring = 'SELECT * FROM '.$wpdb->prefix.'podpress_statcounts ORDER BY postID ASC';
	$statnrs = $wpdb->get_results($querystring);

	$newlastpos = $lastpos + $increment;
	
	for ( $j = $lastpos; $j < $newlastpos; $j++ ) {
		$val = $statnrs[$j];
		// decode the the file name
		$decodedval = rawurldecode($val->media);
		$encodedval = rawurlencode($val->media);
		// if there is no difference between the file name and the decoded version then it is most likely that there are two rows in the table which should be merged (search for a row with the encoded version of meta)
		if ($decodedval == $val->media AND $encodedval != $val->media) {
			$foundasecondline = FALSE;
			// go through the rows and search for the encoded media file name
			foreach ($statnrs as $val2) {
				$decodedval2 = rawurldecode($val2->media);
				// if there is another rows then merge the numbers and delete the row with the encoded file name
				if ($val->media != $val2->media AND $val->media == $decodedval2 AND $val2->postID == $val->postID) {
					$querystring = "UPDATE ".$wpdb->prefix."podpress_statcounts SET total=%d, feed=%d, web=%d, play=%d WHERE (media=%s AND postID=%d)";
					$sqlstr = $wpdb->prepare($querystring, array((intval($val->total)+intval($val2->total)), (intval($val->feed)+intval($val2->feed)), (intval($val->web)+intval($val2->web)), (intval($val->play)+intval($val2->play)), $val2->media, $val2->postID));
					$result = $wpdb->query($sqlstr);
					if (FALSE !== $result) {
						$querystring = "DELETE FROM ".$wpdb->prefix."podpress_statcounts WHERE (media=%s AND postID=%d)";
						$sqlstr = $wpdb->prepare($querystring, array($val->media, $val->postID));
						$result = $wpdb->query($sqlstr);
					}
					$foundasecondline = TRUE;
				}
			}
			// if there is no other row then rename the media value (file name value)
			if (FALSE === $foundasecondline) {
				$querystring = "UPDATE ".$wpdb->prefix."podpress_statcounts SET media=%s WHERE (media=%s AND postID=%d)";
				$sqlstr = $wpdb->prepare($querystring, array((rawurlencode($val->media)), $val->media, $val->postID));
				$result = $wpdb->query($sqlstr);
			}
		}
	}
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	
	$upgrade_status_data[$i]['lastpos'] = $newlastpos;
	
	// if query result is okay then ...
	$result = update_option( '_podPress_upgrade', $upgrade_status_data );

	return Array('elapsed_time' => $time, 'lastpos' => $newlastpos);
}

function podPress_current_nr_of_rows($table = 'statcounts', $isinit = 'false', $upgr_process_nr=0, $rqtype = '') {
	global $wpdb;
	
	if ('stats' == $table OR 'statcounts' == $table) {
		$querystring = "SELECT COUNT(*) as rows FROM ".$wpdb->prefix."podpress_".$table;
		$result_statistics = $wpdb->get_results($querystring, ARRAY_A);
		if (FALSE === $result_statistics OR FALSE === isset($result_statistics[0]['rows'])) {
			if ( 'ajax' == $rqtype ) {
				echo '{ "code": "error" }';
			} else {
				return FALSE;
			}
		} else {
			if ( 'true' == $isinit ) {
				// Check whether the total number of rows is different compared to the last time the update has been started. If that is the case then set the laspos value to zero in order start the update again.
				$upgrade_status_data = get_option( '_podPress_upgrade' );
				$i = 0;
				
				if ( FALSE !== $upgrade_status_data AND TRUE === is_array($upgrade_status_data) ) {
					foreach ( $upgrade_status_data as $upgrade_action ) {
						if ( isset($upgrade_action['action']) AND 'podpress_update_'.$table.'_table' == $upgrade_action['action'] ) {
							break;
						}
						$i++;
					}
					if ( FALSE === isset($upgrade_status_data[$i]['rowstotal']) OR $upgrade_status_data[$i]['rowstotal'] != $result_statistics[0]['rows'] ) {
						$upgrade_status_data[$i]['lastpos'] = 0;
						$upgrade_status_data[$i]['rowstotal'] = $result_statistics[0]['rows'];
						$result = update_option( '_podPress_upgrade', $upgrade_status_data );
					}
				}
			}
			if ( 'ajax' == $rqtype ) {
				echo '{ "code": "'.$upgr_process_nr.'", "table": "'.$table.'", "rows": '.attribute_escape($result_statistics[0]['rows']).' }';
			} else {
				return $result_statistics[0]['rows'];
			}
		}
	} else {
		Switch ($rqtype) {
			case 'ajax' :
				echo '{ "code": "error" }';
			break;
			default :
				return FALSE;
			break;
		}
	}
}

function podPress_print_upgrade_form($table = 'statcounts', $isinit = 'false') {
	global $wpdb;
	if ('stats' == $table OR 'statcounts' == $table) {
		$result_statistics = podPress_current_nr_of_rows($table, $isinit);
		echo '<div id="podPress_upgrade_'.$table.'_table" style="display:none;">';
			echo '<input type="hidden" id="podpress_upgr_'.$table.'_lastpos" value="0" /> '."\n";
			echo '<p>';
			echo __('Select an increment:', 'podpress').' <select id="podpress_upgr_'.$table.'_increment" name="podpress_upgr_'.$table.'_increment" onchange="podpress_update_button_text_on_increment_change(\''.$table.'\', this.value);">';
			if ( $result_statistics < 1 ) {
				echo '	<option value="0">0</option>';
			}
			if ( $result_statistics >= 1 AND $result_statistics < 10 ) {
				echo '	<option value="'.$result_statistics.'">'.$result_statistics.'</option>';
			}
			for ( $i = 10; $i < intval($result_statistics); $i*=10 ) {
				for ($j=1; $j < 10; $j++) {
					$nr = ($i*$j);
					if ( $nr < intval($result_statistics) ) {
						echo '	<option value="'.$nr.'">'.$nr.'</option>';
					} else {
						break;
					}
				}
			}
			echo '</select> &lt;= <span id="podpress_upgr_'.$table.'_increment_limit">10</span> <span class="podpress_description">'.__('(The increment should be smaller than this value)', 'podpress').'</span>';
			echo '</p>';
			echo '<p>';
			echo sprintf(__('Number of rows in the data base table %1$s: %2$s', 'podpress'), '<em>'.$wpdb->prefix.'podpress_'.$table.'</em>', '<span id="podpress_upgr_'.$table.'_total_rows">'.$result_statistics.'</span>')."\n";
			echo '</p>';
			echo '<p>';
			echo sprintf(__('Number of rows which need be processed: %1$s', 'podpress'), '<span id="podpress_upgr_'.$table.'_to_process"></span>')."\n";
			echo '</p>';
		echo '</div>'."\n";
	}
}
?>