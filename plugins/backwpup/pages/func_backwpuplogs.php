<?PHP
if (!defined('ABSPATH')) 
  die();
	
include_once(trailingslashit(ABSPATH).'wp-admin/includes/class-wp-list-table.php');

class BackWPup_Logs_Table extends WP_List_Table {
	function __construct() {
		parent::__construct( array(
			'plural' => 'logs',
			'singular' => 'log',
			'ajax' => true
		) );
	}
	
	function ajax_user_can() {
		return current_user_can(BACKWPUP_USER_CAPABILITY);
	}	
	
	function prepare_items() {
		
		$per_page = $this->get_items_per_page('backwpuplogs_per_page');
		if ( empty( $per_page ) || $per_page < 1 )
			$per_page = 20;	
			
		//load logs
		$cfg=get_option('backwpup');
		$logfiles=array();
		if ( $dir = @opendir( $cfg['dirlogs'] ) ) {
			while (($file = readdir( $dir ) ) !== false ) {
				if (is_file($cfg['dirlogs'].'/'.$file) and 'backwpup_log_' == substr($file,0,strlen('backwpup_log_')) and  ('.html' == substr($file,-5) or '.html.gz' == substr($file,-8))) 
					$logfiles[]=$file;
			}
			closedir( $dir );
		}
		//ordering
		$order=isset($_GET['order']) ? $_GET['order'] : 'desc';
		$orderby=isset($_GET['orderby']) ? $_GET['orderby'] : 'log';
		if ($orderby=='log') {
			if ($order=='asc')
				sort($logfiles);
			else
				rsort($logfiles);
		}
		//by page
		$start=intval( ( $this->get_pagenum() - 1 ) * $per_page );
		$end=$start+$per_page;
		if ($end>count($logfiles))
			$end=count($logfiles);
		
		$this->items=array();
		for ($i=$start;$i<$end;$i++)
			$this->items[]=$logfiles[$i];
		
		$this->set_pagination_args( array(
			'total_items' => count($logfiles),
			'per_page' => $per_page,
			'orderby' => $orderby,
			'order' => $order
		) );

	}

	function get_sortable_columns() {
		return array(
			'log'    => array('log',false),
		);
	}
	
	function no_items() {
		_e( 'No Logs.','backwpup');
	}
	
	function get_bulk_actions() {
		$actions = array();
		$actions['delete'] = __( 'Delete','backwpup' );
		return $actions;
	}
	
	function get_columns() {
		$posts_columns = array();
		$posts_columns['cb'] = '<input type="checkbox" />';
		$posts_columns['id'] = __('Job','backwpup');
		$posts_columns['type'] = __('Type','backwpup');
		$posts_columns['log'] = __('Backup/Log Date/Time','backwpup');
		$posts_columns['status'] = __('Status','backwpup');
		$posts_columns['size'] = __('Size','backwpup');
		$posts_columns['runtime'] = __('Runtime','backwpup');
		return $posts_columns;
	}
	
	function display_rows() {
		$style = '';
		$cfg=get_option('backwpup');
		foreach ( $this->items as $logfile ) {
			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
			$logdata=backwpup_read_logheader($cfg['dirlogs'].$logfile);
			echo "\n\t", $this->single_row( $cfg['dirlogs'].$logfile, $logdata, $style );
		}
	}
	
	function single_row( $logfile, $logdata, $style = '' ) {
		list( $columns, $hidden, $sortable ) = $this->get_column_info();
		$r = "<tr id='".basename($logfile)."'$style>";
		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";
			
			switch($column_name) {
				case 'cb':
					$r .= '<th scope="row" class="check-column"><input type="checkbox" name="logfiles[]" value="'. esc_attr(basename($logfile)) .'" /></th>';
					break;
				case 'id':
					$r .= "<td $attributes>".$logdata['jobid']."</td>"; 
					break;
				case 'type':
					$r .= "<td $attributes>";
					$r .= backwpup_backup_types($logdata['type'],false);
					$r .= "</td>"; 
					break;
				case 'log':				
					$r .= "<td $attributes><strong><a href=\"".wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupworking&logfile='.$logfile, 'view-log_'.basename($logfile))."\" title=\"".__('View log','backwpup')."\">".backwpup_date_i18n(get_option('date_format')." @ ".get_option('time_format'),$logdata['logtime']).": <i>".$logdata['name']."</i></a></strong>";
					$actions = array();
					$actions['view'] = "<a href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupworking&logfile='.$logfile, 'view-log_'.basename($logfile)) . "\">" . __('View','backwpup') . "</a>";
					$actions['delete'] = "<a class=\"submitdelete\" href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpuplogs&action=delete&paged='.$this->get_pagenum().'&logfiles[]='.basename($logfile), 'bulk-logs') . "\" onclick=\"return showNotice.warn();\">" . __('Delete','backwpup') . "</a>";
					$actions['download'] = "<a href=\"" . wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpuplogs&action=download&file='.$logfile, 'download-backup_'.basename($logfile)) . "\">" . __('Download','backwpup') . "</a>";
					$r .= $this->row_actions($actions);
					$r .= "</td>";
					break;
				case 'status':
					$r .= "<td $attributes>";
					if ($logdata['errors']>0)
						$r .= str_replace('%d',$logdata['errors'],'<span style="color:red;font-weight:bold;">'._n("%d ERROR", "%d ERRORS", $logdata['errors'],'backwpup').'</span><br />'); 
					if ($logdata['warnings']>0)
						$r .= str_replace('%d',$logdata['warnings'],'<span style="color:#e66f00;font-weight:bold;">'._n("%d WARNING", "%d WARNINGS", $logdata['warnings'],'backwpup').'</span><br />'); 
					if($logdata['errors']==0 and $logdata['warnings']==0) 
						$r .= '<span style="color:green;font-weight:bold;">'.__('O.K.','backwpup').'</span>';
					$r .= "</td>"; 
					break;
				case 'size':
					$r .= "<td $attributes>";
					if (!empty($logdata['backupfilesize'])) {
						$r .= backwpup_formatBytes($logdata['backupfilesize']);
					} else {
						$r .= __('only Log','backwpup');
					}
					$r .= "</td>"; 
					break;
				case 'runtime':
					$r .= "<td $attributes>";
					$r .= $logdata['runtime'].' '.__('sec.','backwpup');
					$r .= "</td>"; 
					break;					
			}
		}
		$r .= '</tr>';
		return $r;
	}
}