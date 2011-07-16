<?php
/*
 *	PluginBuddy.com & iThemes.com
 *	Author: Dustin Bolton < http://dustinbolton.com >
 *
 *	Created:	February 20, 2010
 *	Updated:	July 1, 2011
 * 
 *	Upgrade system for PluginBuddy and iThemes products.
 *
 */

// TODO: Implement version number into updater so it can be checked for compatability with backend.

if ( !class_exists( 'PluginBuddyUpdater' ) ) {
	class PluginBuddyUpdater {
		var $_version = '1.0.4';
		
		var $_updater_url = 'http://updater.ithemes.com/';
		var $_update_wait = '+10 minutes';
		var $_guid;
		var $_defaults = array(
			'key'			=>		'',
			'last_check'	=>		0,		// Timestamp of last server ping.
		);
		var $_checked = false;
		
		
		function PluginBuddyUpdater( &$parent ) {
			$this->_parent = &$parent;
			
			if ( empty( $this->_parent->_options ) ) {
				$this->_parent->load();
			}
			
			$this->_product = strtolower( $this->_parent->_var );
			$this->_product = str_replace( 'ithemes-', '', $this->_product );
			$this->_product = str_replace( 'pluginbuddy-', '', $this->_product );
			$this->_product = str_replace( 'pluginbuddy_', '', $this->_product );
			
			if (! array_key_exists( 'updater', $this->_parent->_options ) ) {
				$this->_parent->_options['updater'] = $this->_defaults;
				$this->_parent->save();
			}
			
			// Generate GUID if needed.
			$this->_guid = get_option( $this->_parent->_var . '-updater-guid' );
			if ( $this->_guid == '' ) {
				$this->_guid = uniqid( '' );
				add_option( $this->_parent->_var.'-updater-guid', $this->_guid, '', false ); // Create if needed.
				update_option( $this->_parent->_var.'-updater-guid', $this->_guid ); // Update.
			}
			add_action( 'wp_ajax_ithemes_updater', array( &$this, 'ajax' ) ); // Dont put within plugins.php check.
			add_action( 'update_option__transient_update_plugins', array( &$this, 'old_update_transient_option' ) ); // WP 2.8
			add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'update_transient_option' ) ); // WP 3.0
			
			if( "plugins.php" == basename($_SERVER['PHP_SELF']) ) {
				
				// Force refreshing of plugins despite last check time.
				if ( isset( $_GET['pluginbuddy_refresh'] ) ) {
					$this->_parent->_options['updater']['last_check'] = mktime() - 9000;
					$this->_parent->save();
					
					$option = get_transient( 'update_plugins' );
					$option->last_checked = '0';
					$option = '';
					set_transient( 'update_plugins', $option );
					set_site_transient( 'update_plugins', $option );
					
					wp_update_plugins();
				}
				
				add_action('after_plugin_row_'.$this->_product.'/'.$this->_product.'.php', array(&$this, 'plugin_row') );
				add_action('plugin_action_links_'.$this->_product.'/'.$this->_product.'.php', array(&$this, 'plugin_links') );
				add_filter( 'plugin_row_meta', array( &$this, 'plugin_right_links' ), 10, 2 );
			}
			add_action( 'install_plugins_pre_plugin-information', array( &$this, 'view_changelog' ) );
		}
		
		
		function ajax() {
			require_once( dirname( __FILE__ ) . '/get.php' );
			die();
		}
		
		
		function view_changelog() {
			if( $_GET["plugin"] != strtolower( $this->_product ) ) {
				return;
			}
			$data = $this->updater_post( 'action=changelog', false );
			echo $data['message'];
			
			die();
		}
		
		
		// WP 2.8. Basically a WP 2.8 wrapper for get_update_plugins_option().
		function old_update_transient_option() {
			if( !is_admin() ) {
				return;
			}
			
			if ( !isset( $this->_transient_set ) ) {
				$option = get_transient( 'update_plugins' );
				$option = $this->update_transient_option( $option );
				
				set_transient( 'update_plugins', $option );
				set_site_transient( 'update_plugins', $option );
				
				$this->_transient_set = true;
			}
		}
		
		
		// WP 3.0 direct, goes through old_update_transient_option() first for WP 2.8.
		function update_transient_option( $option ) {
			
			$plugin_name = strtolower( $this->_product ) . '/' . strtolower( $this->_product ) . '.php';
			
			// If the plugin isn't already in this, create a new stdClass for it...
			if( empty( $option->response[$plugin_name] ) ) {
				$option->response[$plugin_name] = new stdClass();
			}
			
			// Check if there are updates if we haven't checked yet this run...
			if ( !isset( $this->_check_status ) ) {
				$this->_check_status = $this->updater_post('action=check');
			}
			
			// If we have a useful response, continue. If key_status isn't set then we probably couldn't contact the authentication server so we leave it all alone!
			if ( isset( $this->_check_status['key_status'] ) ) {
				// If key status is bad OR there is no new version, dont queue an update.
				if( ($this->_check_status['key_status'] != 'ok') || ($this->_check_status['new_version'] == false) ){
					unset( $option->response[$plugin_name] );
				} else {
					$option->response[$plugin_name]->url = $this->_updater_url;
					$option->response[$plugin_name]->slug = strtolower( $this->_product );
					$option->response[$plugin_name]->package = $this->_check_status['download_url'];
					$option->response[$plugin_name]->new_version = $this->_check_status['latest_version'];
					$option->response[$plugin_name]->id = '0';
				}
			}
			
			return $option;
		}
		
		
		function plugin_links($val) {
			$this->_parent->load();
			if (array_key_exists('updater', $this->_parent->_options)) {
				if ( isset( $this->_parent->_options['updater']['key'] ) ) {
					$key = $this->_parent->_options['updater']['key'];
				} else {
					$key = '';
				}
			}
			
			$val[sizeof($val)] = '<a href="'.admin_url('admin-ajax.php').'?action=ithemes_updater&url='.urlencode('http://updater.ithemes.com/?action=licenses&product='.$this->_product.'&var=' . $this->_parent->_var . '&siteurl='.urlencode( site_url() ).'&key='.$key.'&guid='.$this->_guid.'&geturl='.admin_url('admin-ajax.php')).'&TB_iframe=true" class="thickbox" title="Manage Licenses"><img src="'.$this->_parent->_pluginURL.'/lib/updater/key.png" style="vertical-align: -3px;" /> Licenses</a>';
			
			return $val;
		}
		
		
		function plugin_row( $plugin_name ){
			if (strtolower( $this->_product ).'/'.strtolower( $this->_product ).'.php' != $plugin_name ) {
				return;
			}
			
			if ( !isset( $this->_check_status ) ) {
				$this->_check_status = $this->updater_post( 'action=check' );
			}
			
			if ( $this->_check_status['status'] != 'ok' ) {
				$this->output( 'ERROR checking update status: ' . $this->_check_status['message'] );
			} else {
				$print_text = "";
				$key_text = "";
				
				$this->_parent->load();
				if ( !isset( $this->_parent->_options ) ) {
					$this->_parent->load();
				}
				
				if ( isset( $this->_parent->_options['updater']['key'] ) ) {
					$key = $this->_parent->_options['updater']['key'];
				} else {
					$key = '';
				}
				
				$key_text='<span style="border-right: 1px solid #DFDFDF; margin-right: 5px;"><a href="'.admin_url('admin-ajax.php').'?action=ithemes_updater&url='.urlencode('http://updater.ithemes.com/?action=licenses&product=' . $this->_product . '&var=' . $this->_parent->_var . '&siteurl='.urlencode( site_url() ).'&key='.$key.'&guid='.$this->_guid.'&geturl='.admin_url('admin-ajax.php')).'&TB_iframe=true" class="thickbox" title="Manage Licenses"><img src="'.$this->_parent->_pluginURL.'/lib/updater/key.png" style="vertical-align: -3px;" /> Manage Licenses</a> </span>';
				
				if ($this->_check_status['key_status']!='ok') {
					if ( $this->_check_status['new_version'] == 'true' ) {
						$print_text .= 'There is a new version of this plugin available, '.$this->_check_status['latest_version'].'. ';
					} else {
						$print_text .= 'Plugin up to date. ';
					}
					$print_text .= 'No key set or invalid. Manage your license for automatic upgrades. ';
				}
				if (isset($this->_check_status['message'])) {
					$print_text .= $this->_check_status['message'];
				}
				if ( $print_text != '' ) {
					$this->output($key_text . $print_text);
				}
			}
		}
		
		
		function output($content) {
			echo '</tr>';
			
			wp_enqueue_script( 'thickbox' );
			wp_print_scripts( 'thickbox' );
			wp_print_styles( 'thickbox' );
			
			echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">'.$content.'</div></td>';
		}
		
		// If $cached === false then dont return cached response
		function updater_post($data, $cached_response = true ) {
			if ( array_key_exists('updater', $this->_parent->_options) ) {
				if ( isset( $this->_parent->_options['updater']['key'] ) ) {
					$key = $this->_parent->_options['updater']['key'];
				}
			}
			if ( !isset( $key ) || ( $key == '' ) ) {
				$key = '';
			}
			
			if ( $cached_response === true ) {
				// If recheck time has not passed, use cached response to limit traffic.
				if ( strtotime( $this->_update_wait, $this->_parent->_options['updater']['last_check']) > mktime() ) {
					return $this->_parent->_options['updater']['last_response'];
				}
			}
			
			$url = $this->_updater_url.'?product='.strtolower( $this->_product ).'&version='.$this->_parent->_version.'&siteurl='.urlencode(get_option('siteurl')).'&key='.$key.'&guid='.$this->_guid.'&'.$data;
			$response = wp_remote_get( $url, array(
					'method' => 'GET',
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(),
					'body' => null,
					'cookies' => array()
				)
			);
			if( is_wp_error( $response ) ) {
				$response = array( 'status' => 'fail', 'message' => 'Invalid server response. Details: ' . $response->get_error_message() );
			} else {
				$response = unserialize( $response['body'] );
			}
			
			// Only cache a normal checking action. Ex: dont cache changelog response.
			if ( $data == 'action=check' ) {
				$this->_parent->_options['updater']['last_response'] = $response;
				$this->_parent->_options['updater']['last_check'] = mktime();
				$this->_parent->save();
			}
			
			return $response;
		}
		
		function plugin_right_links($links, $plugin_name) {
			if (strtolower( $this->_product ).'/'.strtolower( $this->_product ).'.php' != $plugin_name ) {
				return $links;
			}
			$links[] = '<a href="?pluginbuddy_refresh=true" title="Check for PluginBuddy updates. Running Updater v' . $this->_version . ' from plugin \'' . basename( dirname(dirname( dirname( __FILE__ ) ) ) ) . '\'.">Check for Updates Now</a>';
			return $links;
		}
	}
}

$this->_updater = new PluginBuddyUpdater($this);
add_action('after_plugin_row_backupbuddy/backupbuddy.php', array(&$this->_updater, 'plugin_row') );
?>