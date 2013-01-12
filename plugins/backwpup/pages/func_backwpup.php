<?PHP
if (!defined('ABSPATH')) 
  die();
  
include_once( trailingslashit(ABSPATH).'wp-admin/includes/class-wp-list-table.php');

class BackWPup_Jobs_Table extends WP_List_Table {
	
	function __construct() {
		parent::__construct( array(
			'plural' => 'jobs',
			'singular' => 'job',
			'ajax' => true
		) );
	}
	
	function ajax_user_can() {
		return current_user_can(BACKWPUP_USER_CAPABILITY);
	}
	
	function prepare_items() {
		global $mode;
		$jobs=get_option('backwpup_jobs');
		if (!empty($jobs) and is_array($jobs)) {
			foreach ($jobs as $key => $value) {
				$this->items[]=backwpup_get_job_vars($key);
			}
		} else {
			$this->items='';
		}
		$mode = empty( $_GET['mode'] ) ? 'list' : $_GET['mode'];
	}
	
	function pagination( $which ) {
		global $mode;

		parent::pagination( $which );

		if ( 'top' == $which )
			$this->view_switcher( $mode );
	}
	
	function no_items() {
		_e( 'No Jobs.','backwpup');
	}

	function get_bulk_actions() {
		$actions = array();
		$actions['export'] = __( 'Export','backwpup' );
		$actions['delete'] = __( 'Delete','backwpup' );

		return $actions;
	}
		
	function get_columns() {
		$jobs_columns = array();
		$jobs_columns['cb'] = '<input type="checkbox" />';
		$jobs_columns['id'] = __('ID','backwpup');
		$jobs_columns['jobname'] = __('Job Name','backwpup');
		$jobs_columns['type'] = __('Type','backwpup');
		$jobs_columns['info'] = __('Information','backwpup');
		$jobs_columns['next'] = __('Next Run','backwpup');
		$jobs_columns['last'] = __('Last Run','backwpup');
		return $jobs_columns;
	}
	
	function display_rows() {
		//check for running job
		$runningfile['JOBID']='';
		$runningfile=backwpup_get_working_file();
		$style = '';
		foreach ( $this->items as $jobvalue ) {
			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
			echo "\n\t", $this->single_row( $jobvalue, $runningfile, $style );
		}
	}
	
	function single_row($jobvalue, $runningfile, $style = '' ) {
		global $mode;
				
		list( $columns, $hidden, $sortable ) = $this->get_column_info();
		$r = "<tr id=\"jodid-".$jobvalue["jobid"]."\"".$style.">";
		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";
			
			switch( $column_name ) {
				case 'cb':
					$r .=  '<th scope="row" class="check-column"><input type="checkbox" name="jobs[]" value="'. esc_attr($jobvalue["jobid"]) .'" /></th>';
					break;
				case 'id':
					$r .=  "<td $attributes>".$jobvalue["jobid"]."</td>"; 
					break;
				case 'jobname':
					$r .=  "<td $attributes><strong><a href=\"".wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupeditjob&jobid='.$jobvalue["jobid"], 'edit-job')."\" title=\"".__('Edit:','backwpup').esc_html($jobvalue['name'])."\">".esc_html($jobvalue['name'])."</a></strong>";
					$actions = array();
					if ($runningfile==false) {
						$actions['edit'] = "<a href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupeditjob&jobid='.$jobvalue["jobid"], 'edit-job') . "\">" . __('Edit','backwpup') . "</a>";
						$actions['copy'] = "<a href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpup&action=copy&jobid='.$jobvalue["jobid"], 'copy-job_'.$jobvalue["jobid"]) . "\">" . __('Copy','backwpup') . "</a>";
						$actions['export'] = "<a href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpup&action=export&jobs[]='.$jobvalue["jobid"], 'bulk-jobs') . "\">" . __('Export','backwpup') . "</a>";
						$actions['delete'] = "<a class=\"submitdelete\" href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpup&action=delete&jobs[]='.$jobvalue["jobid"], 'bulk-jobs') . "\" onclick=\"return showNotice.warn();\">" . __('Delete','backwpup') . "</a>";
						$actions['runnow'] = "<a href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupworking&action=runnow&jobid='.$jobvalue["jobid"], 'runnow-job_'.$jobvalue["jobid"]) . "\">" . __('Run Now','backwpup') . "</a>";
					} else {
						if ($runningfile['JOBID']==$jobvalue["jobid"]) {
							$actions['working'] = "<a href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupworking', '') . "\">" . __('View!','backwpup') . "</a>";
							$actions['abort'] = "<a class=\"submitdelete\" href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpup&action=abort', 'abort-job') . "\">" . __('Abort!','backwpup') . "</a>";
						}
					}
					$r .= $this->row_actions($actions);
					$r .=  '</td>';
					break;	
				case 'type':
					$r .=  "<td $attributes>";
					$r .=  backwpup_backup_types($jobvalue['type'],false);
					$r .=  "</td>";
					break;
				case 'info':
					$r .=  "<td $attributes>";
					$r .=  "<img class=\"waiting\" src=\"".esc_url( backwpup_admin_url( 'images/wpspin_light.gif' ) )."\" id=\"image-wait-".$jobvalue["jobid"]."\" />";
					$r .=  "</td>";
					break;
				case 'next':
					$r .= "<td $attributes>";
					if ($runningfile['JOBID']==$jobvalue["jobid"] and $runningfile!=false) {
						$runtime=time()-$jobvalue['starttime'];
						$r .=  __('Running since:','backwpup').' '.$runtime.' '.__('sec.','backwpup');
					} elseif ($jobvalue['activated']) {
						$r .=  date_i18n(get_option('date_format').' @ '.get_option('time_format'),$jobvalue['cronnextrun']);
					} else {
						$r .= __('Inactive','backwpup');
					}
					if ( 'excerpt' == $mode ) {
						$r .= '<br />'.__('<a href="http://wikipedia.org/wiki/Cron" target="_blank">Cron</a>:','backwpup').' '.$jobvalue['cron'];
					}
					$r .=  "</td>";
					break;
				case 'last':
					$r .=  "<td $attributes>";
					if (isset($jobvalue['lastrun']) && $jobvalue['lastrun']) {
						$r .=  backwpup_date_i18n(get_option('date_format').' @ '.get_option('time_format'),$jobvalue['lastrun']); 
						if (isset($jobvalue['lastruntime']))
							$r .=  '<br />'.__('Runtime:','backwpup').' '.$jobvalue['lastruntime'].' '.__('sec.','backwpup').'<br />';
					} else {
						$r .= __('None','backwpup');
					}
					if (!empty($jobvalue['lastbackupdownloadurl']))
						$r .="<a href=\"" . wp_nonce_url($jobvalue['lastbackupdownloadurl'], 'download-backup') . "\" title=\"".__('Download last Backup','backwpup')."\">" . __('Download','backwpup') . "</a> | ";
					if (!empty($jobvalue['logfile']))
						$r .="<a href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupworking&logfile='.$jobvalue['logfile'], 'view-log_'.basename($jobvalue['logfile'])) . "\" title=\"".__('View last Log','backwpup')."\">" . __('Log','backwpup') . "</a><br />";

					$r .=  "</td>";
					break;
			}
		}
		$r .= '</tr>';
		return $r;
	}
}


//helper functions for detecting file size
function _backwpup_calc_file_size_file_list_folder( $folder = '', $levels = 100, $excludes=array(),$excludedirs=array()) {
	global $backwpup_temp_files;
	if ( !empty($folder) and $levels and $dir = @opendir( $folder )) {
		while (($file = readdir( $dir ) ) !== false ) {
			if ( in_array($file, array('.', '..','.svn') ) )
				continue;
			foreach ($excludes as $exclusion) { //exclude dirs and files
				if (false !== stripos($folder.$file,$exclusion) and !empty($exclusion) and $exclusion!='/')
					continue 2;
			}
			if ( @is_dir( $folder.$file )) {
				if (!in_array(trailingslashit($folder.$file),$excludedirs))
					_backwpup_calc_file_size_file_list_folder( trailingslashit($folder.$file), $levels - 1, $excludes);
			} elseif ((@is_file( $folder.$file ) or @is_executable($folder.$file)) and @is_readable($folder.$file)) {
				$backwpup_temp_files['num']++;
				$backwpup_temp_files['size']=$backwpup_temp_files['size']+filesize($folder.$file);
			} 
		}
		@closedir( $dir );
	}
}

//helper functions for detecting file size
function backwpup_calc_file_size($jobvalues) {
	global $backwpup_temp_files;
	$backwpup_temp_files=array('size'=>0,'num'=>0);
	//Exclude Files
	$backwpup_exclude=explode(',',trim($jobvalues['fileexclude']));
	$backwpup_exclude[]='.tmp';  //do not backup .tmp files
	$backwpup_exclude=array_unique($backwpup_exclude);

	//File list for blog folders
	if ($jobvalues['backuproot'])
		_backwpup_calc_file_size_file_list_folder(trailingslashit(str_replace('\\','/',ABSPATH)),100,$backwpup_exclude,array_merge($jobvalues['backuprootexcludedirs'],backwpup_get_exclude_wp_dirs(ABSPATH)));
	if ($jobvalues['backupcontent'])
		_backwpup_calc_file_size_file_list_folder(trailingslashit(str_replace('\\','/',WP_CONTENT_DIR)),100,$backwpup_exclude,array_merge($jobvalues['backupcontentexcludedirs'],backwpup_get_exclude_wp_dirs(WP_CONTENT_DIR)));
	if ($jobvalues['backupplugins'])
		_backwpup_calc_file_size_file_list_folder(trailingslashit(str_replace('\\','/',WP_PLUGIN_DIR)),100,$backwpup_exclude,array_merge($jobvalues['backuppluginsexcludedirs'],backwpup_get_exclude_wp_dirs(WP_PLUGIN_DIR)));
	if ($jobvalues['backupthemes'])
		_backwpup_calc_file_size_file_list_folder(trailingslashit(trailingslashit(str_replace('\\','/',WP_CONTENT_DIR)).'themes'),100,$backwpup_exclude,array_merge($jobvalues['backupthemesexcludedirs'],backwpup_get_exclude_wp_dirs(trailingslashit(WP_CONTENT_DIR).'themes')));
	if ($jobvalues['backupuploads'])
		_backwpup_calc_file_size_file_list_folder(trailingslashit(str_replace('\\','/',backwpup_get_upload_dir())),100,$backwpup_exclude,array_merge($jobvalues['backupuploadsexcludedirs'],backwpup_get_exclude_wp_dirs(backwpup_get_upload_dir())));

	//include dirs
	if (!empty($jobvalues['dirinclude'])) {
		$dirinclude=explode(',',$jobvalues['dirinclude']);
		$dirinclude=array_unique($dirinclude);
		//Crate file list for includes
		foreach($dirinclude as $dirincludevalue) {
			if (is_dir($dirincludevalue))
				_backwpup_calc_file_size_file_list_folder(trailingslashit($dirincludevalue),100,$backwpup_exclude);
		}
	}
	
	return $backwpup_temp_files;
	
}

//ajax show info div for jobs
function backwpup_show_info_td() {
	check_ajax_referer('backwpup_ajax_nonce');
	if (!current_user_can(BACKWPUP_USER_CAPABILITY))
		die('-1');
	global $wpdb;
	$mode=$_POST['mode'];
	$jobvalue=backwpup_get_job_vars($_POST['jobid']);
	if (in_array('DB',explode('+',$jobvalue['type'])) or in_array('OPTIMIZE',explode('+',$jobvalue['type'])) or in_array('CHECK',explode('+',$jobvalue['type']))) {
		$dbsize=array('size'=>0,'num'=>0,'rows'=>0);
		$status=$wpdb->get_results("SHOW TABLE STATUS FROM `".DB_NAME."`;", ARRAY_A);
		foreach($status as $tablekey => $tablevalue) {
			if (!in_array($tablevalue['Name'],$jobvalue['dbexclude'])) {
				$dbsize['size']=$dbsize['size']+$tablevalue["Data_length"]+$tablevalue["Index_length"];
				$dbsize['num']++;
				$dbsize['rows']=$dbsize['rows']+$tablevalue["Rows"];
			}
		}
		echo __("DB Size:","backwpup")." ".backwpup_formatBytes($dbsize['size'])."<br />";
		if ( 'excerpt' == $mode ) {
			echo  __("DB Tables:","backwpup")." ".$dbsize['num']."<br />";
			echo  __("DB Rows:","backwpup")." ".$dbsize['rows']."<br />";
		}
	}
	if (in_array('FILE',explode('+',$jobvalue['type']))) {
		$files=backwpup_calc_file_size($jobvalue);
		echo __("Files Size:","backwpup")." ".backwpup_formatBytes($files['size'])."<br />";
		if ( 'excerpt' == $mode ) {
			echo __("Files count:","backwpup")." ".$files['num']."<br />";
		}
	}	
	die();
}
//add ajax function
add_action('wp_ajax_backwpup_show_info_td', 'backwpup_show_info_td');