<?php
if ( !class_exists( "pluginbuddy_backupbuddy_admin" ) ) {
	class pluginbuddy_backupbuddy_admin {
		function pluginbuddy_backupbuddy_admin( &$parent ) {
			$this->_parent = &$parent;
			$this->_var = &$parent->_var;
			$this->_name = &$parent->_name;
			$this->_options = &$parent->_options;
			$this->_pluginPath = &$parent->_pluginPath;
			$this->_pluginURL = &$parent->_pluginURL;
			$this->_selfLink = &$parent->_selfLink;
			
			add_action('admin_menu', array( &$this, 'admin_menu' ) ); // Add menu in admin.
			
			require_once( $this->_pluginPath . '/lib/zipbuddy/zipbuddy.php' );
			$this->_parent->_zipbuddy = new pluginbuddy_zipbuddy( $this->_options['backup_directory'] );
		}
		
		
		function alert() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'alert' ), $args );
		}
		
		function video() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'video' ), $args );
		}
		
		function tip() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'tip' ), $args );
		}
		
		function log() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'log' ), $args );
		}
		
		function plugin_info() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'plugin_info' ), $args );
		}
		
		function mail_error() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'mail_error' ), $args );
		}
		
		// backup.php uses this.
		function localize_time() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'localize_time' ), $args );
		}
		
		// backup.php uses this
		function backup_prefix() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'backup_prefix' ), $args );
		}
		
		// backup.php uses this
		function mkdir_recursive() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'mkdir_recursive' ), $args );
		}
		
		// backup.php uses this
		function delete_directory_recursive() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'delete_directory_recursive' ), $args );
		}
		
		// backup.php uses this
		function format_duration() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'format_duration' ), $args );
		}
		
		// backup.php uses this
		function mail_notify_manual() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'mail_notify_manual' ), $args );
		}
		
		function set_greedy_script_limits() {
			$args = func_get_args();
			return call_user_func_array( array( $this->_parent, 'set_greedy_script_limits' ), $args );
		}
		
		function save() {
			return $this->_parent->save();
		}
		
		function title( $title ) {
			echo '<h2><img src="' . $this->_pluginURL .'/images/icon.png" style="vertical-align: -7px;"> ' . $title . '</h2>';
		}
		
		
		function nonce() {
			wp_nonce_field( $this->_parent->_var . '-nonce' );
		}
		
		
		/**
		 *	savesettings()
		 *	
		 *	Saves a form into the _options array.
		 *	
		 *	Use savepoint to set the root array key path. Accepts variable depth, dividing array keys with pound signs.
		 *	Ex:	$_POST['savepoint'] value something like array_key_name#subkey
		 *		<input type="hidden" name="savepoint" value="files#exclusions" /> to set the root to be $this->_options['files']['exclusions']
		 *	
		 *	All inputs with the name beginning with pound will act as the array keys to be set in the _options with the associated posted value.
		 *	Ex:	$_POST['#key_name'] or $_POST['#key_name#subarray_key_name'] value is the array value to set.
		 *		<input type="text" name="#name" /> will save to $this->_options['name']
		 *		<input type="text" name="#group#17#name" /> will save to $this->_options['groups'][17]['name']
		 *
		 *	$savepoint_root		string		Override the savepoint. Same format as the form savepoint.
		 */
		function savesettings( $savepoint_root = '' ) {
			check_admin_referer( $this->_parent->_var . '-nonce' );
			
			if ( !empty( $savepoint_root ) ) { // Override savepoint.
				$_POST['savepoint'] = $savepoint_root;
			}
			
			if ( !empty( $_POST['savepoint'] ) ) {
				$savepoint_root = stripslashes( $_POST['savepoint'] ) . '#';
			} else {
				$savepoint_root = '';
			}
			
			$posted = stripslashes_deep( $_POST ); // Unescape all the stuff WordPress escaped. Sigh @ WordPress for being like PHP magic quotes.
			foreach( $posted as $index => $item ) {
				if ( substr( $index, 0, 1 ) == '#' ) {
					$savepoint_subsection = &$this->_options;
					$savepoint_levels = explode( '#', $savepoint_root . substr( $index, 1 ) );
					foreach ( $savepoint_levels as $savepoint_level ) {
						$savepoint_subsection = &$savepoint_subsection{$savepoint_level};
					}
					$savepoint_subsection = $item;
				}
			}
			
			$this->_parent->save();
			$this->alert( __('Settings saved...', 'it-l10n-backupbuddy') );
		}
		
		
		function admin_scripts() {
			wp_enqueue_script( $this->_var . '_tooltip', $this->_pluginURL . '/js/tooltip.js' );
			wp_print_scripts( $this->_var . '_tooltip' );
			wp_enqueue_script( $this->_var . '_admin', $this->_pluginURL . '/js/admin.js' );
			wp_print_scripts( $this->_var . '_admin' );
			
			echo '<link rel="stylesheet" href="'.$this->_pluginURL . '/css/admin.css" type="text/css" media="all" />';
		}
		
		
		/**
		 *	get_feed()
		 *
		 *	Gets an RSS or other feed and inserts it as a list of links...
		 *
		 *	$feed		string		URL to the feed.
		 *	$limit		integer		Number of items to retrieve.
		 *	$append		string		HTML to include in the list. Should usually be <li> items including the <li> code.
		 *	$replace	string		String to replace in every title returned. ie twitter includes your own username at the beginning of each line.
		 *	$cache_time	int			Amount of time to cache the feed, in seconds.
		 */
		function get_feed( $feed, $limit, $append = '', $replace = '', $cache_time = 300 ) {
			require_once(ABSPATH.WPINC.'/feed.php');  
			$rss = fetch_feed( $feed );
			if (!is_wp_error( $rss ) ) {
				$maxitems = $rss->get_item_quantity( $limit ); // Limit 
				$rss_items = $rss->get_items(0, $maxitems); 
				
				echo '<ul class="pluginbuddy-nodecor">';

				$feed_html = get_transient( md5( $feed ) );
				if ( $feed_html == '' ) {
					foreach ( (array) $rss_items as $item ) {
						$feed_html .= '<li>- <a href="' . $item->get_permalink() . '">';
						$title =  $item->get_title(); //, ENT_NOQUOTES, 'UTF-8');
						if ( $replace != '' ) {
							$title = str_replace( $replace, '', $title );
						}
						if ( strlen( $title ) < 30 ) {
							$feed_html .= $title;
						} else {
							$feed_html .= substr( $title, 0, 32 ) . ' ...';
						}
						$feed_html .= '</a></li>';
					}
					set_transient( md5( $feed ), $feed_html, $cache_time ); // expires in 300secs aka 5min
				}
				echo $feed_html;
				
				echo $append;
				echo '</ul>';
			} else {
				echo __('Temporarily unable to load feed...', 'it-l10n-backupbuddy');
			}
		}
		
		
		function view_gettingstarted() {
			if ( !empty( $_GET['custom'] ) ) {
				require( 'view_custom-' . $_GET['custom'] . '.php' );
			} else {
				$this->_parent->versions_confirm();
				require( 'view_gettingstarted.php' );
			}
		}
		
		
		function view_settings() {
			$this->_parent->versions_confirm();
			require( 'view_settings.php' );
		}
		
		
		function view_backup() {
			$this->_parent->versions_confirm();
			require( 'view_backup.php' );
		}
		
		
		function view_malware() {
			require( 'view_malware.php' );
		}
		
		
		function view_tools() {
			$this->_parent->versions_confirm();
			require( 'view_tools.php' );
		}
		
		
		function view_scheduling() {
			$this->_parent->versions_confirm();
			require( 'view_scheduling.php' );
		}

		
		/** admin_menu()
		 *
		 * Initialize menu for admin section.
		 *
		 */		
		function admin_menu() {
			if ( isset( $this->_parent->_series ) && ( $this->_parent->_series != '' ) ) {
				// Handle series menu. Create series menu if it does not exist.
				global $menu;
				$found_series = false;
				foreach ( $menu as $menus => $item ) {
					if ( $item[0] == $this->_parent->_series ) {
						$found_series = true;
					}
				}
				if ( $found_series === false ) {
					add_menu_page(  $this->_parent->_series . ' ' . __('Getting Started', 'it-l10n-backupbuddy'), 
									$this->_parent->_series, 
									'administrator', 
									'pluginbuddy-' . strtolower( $this->_parent->_series ), 
									array( &$this, 'view_gettingstarted'), 
									$this->_parent->_pluginURL.'/images/pluginbuddy.png' );

					add_submenu_page(   'pluginbuddy-' . strtolower( $this->_parent->_series ), 
										$this->_parent->_name . ' ' . __('Getting Started' , 'it-l10n-backupbuddy'), 
										__('Getting Started', 'it-l10n-backupbuddy'), 
										'administrator', 
										'pluginbuddy-' . strtolower( $this->_parent->_series ), 
										array(&$this, 'view_gettingstarted') );
				}
				// Register for getting started page
				global $pluginbuddy_series;
				if ( !isset( $pluginbuddy_series[ $this->_parent->_series ] ) ) {
					$pluginbuddy_series[ $this->_parent->_series ] = array();
				}
				$pluginbuddy_series[ $this->_parent->_series ][ $this->_parent->_name ] = $this->_pluginPath;
				
				add_submenu_page( 'pluginbuddy-' . strtolower( $this->_parent->_series ), $this->_parent->_name, $this->_parent->_name, 'administrator', $this->_parent->_var.'-settings', array(&$this, 'view_settings'));
			} else { // NOT IN A SERIES!
				// Add main menu (default when clicking top of menu)
				add_menu_page( $this->_parent->_name, $this->_parent->_name, 'administrator', $this->_parent->_var, array( &$this, 'view_gettingstarted' ), $this->_parent->_pluginURL.'/images/pluginbuddy.png');
				// Add sub-menu items (first should match default page above)
				add_submenu_page( $this->_parent->_var, $this->_parent->_name. ' ' . __('Getting Started', 'it-l10n-backupbuddy'), __('Getting Started', 'it-l10n-backupbuddy'), 'administrator', $this->_parent->_var, array(&$this, 'view_gettingstarted'));
				add_submenu_page( $this->_parent->_var, $this->_parent->_name. ' ' . __('Backup & Restore', 'it-l10n-backupbuddy'), __('Backup & Restore', 'it-l10n-backupbuddy'), 'administrator', $this->_parent->_var.'-backup', array(&$this, 'view_backup'));
				add_submenu_page( $this->_parent->_var, $this->_parent->_name. ' ' . __('Malware Scan', 'it-l10n-backupbuddy'), __('Malware Scan', 'it-l10n-backupbuddy'), 'administrator', $this->_parent->_var.'-malware', array(&$this, 'view_malware'));
				add_submenu_page( $this->_parent->_var, $this->_parent->_name. ' ' . __('Server Info.', 'it-l10n-backupbuddy'), __('Server Info.', 'it-l10n-backupbuddy'), 'administrator', $this->_parent->_var.'-tools', array(&$this, 'view_tools'));
				add_submenu_page( $this->_parent->_var, $this->_parent->_name. ' ' . __('Scheduling', 'it-l10n-backupbuddy'),__('Scheduling', 'it-l10n-backupbuddy'), 'administrator', $this->_parent->_var.'-scheduling', array(&$this, 'view_scheduling'));
				add_submenu_page( $this->_parent->_var, $this->_parent->_name. ' ' . __('Settings', 'it-l10n-backupbuddy'), __('Settings', 'it-l10n-backupbuddy'), 'administrator', $this->_parent->_var.'-settings', array(&$this, 'view_settings'));
			}
		}
		
		
		function ajax_remotedestination() {
			require_once( $this->_pluginPath . '/classes/ajax_remotedestination.php' );
		}
		
		function importbuddy_link() {
			//return admin_url( 'admin-ajax.php' ) . '?action=backupbuddy_importbuddy&pass=' . md5( $this->_options['import_password'] );
			if ( !empty( $this->_options['import_password'] ) ) {
				$import_pass_query = '&pass=' . md5( $this->_options['import_password'] );
			} else {
				$import_pass_query = '';
			}
			return admin_url( 'admin-ajax.php' ) . '?action=backupbuddy_importbuddy' . $import_pass_query;
		}
		
		
		
		
	} // End class
	//$pluginbuddy_backupbuddy_admin = new pluginbuddy_backupbuddy_admin( $this );
}
