<?php
/*
License:
 ==============================================================================

    Copyright 2006  Dan Kuykendall  (email : dan@kuykendall.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-107  USA
*/

if ( isset($_GET['action']) OR isset($_POST['action']) ) {
	if ( !defined('DB_NAME') ) { // everything is normal
	
		// determine the root folder of the WP blog and the one above this folder
		$podpress_abspath_parts = explode(DIRECTORY_SEPARATOR.'wp-content', __FILE__);
		$podpress_abspath = $podpress_abspath_parts[0];
		$podpress_abspath_parts = explode(DIRECTORY_SEPARATOR, $podpress_abspath);
		$podpress_nr_parts = count($podpress_abspath_parts);
		$i = 0;
		$podpress_abspath = '';
		$podpress_path_above_of_WP = '';
		foreach ($podpress_abspath_parts as $podpress_abspath_part) {
			if ( $i < $podpress_nr_parts and FALSE === empty($podpress_abspath_part) ) {
				$podpress_abspath .= DIRECTORY_SEPARATOR . $podpress_abspath_part;
				if ( $i < ($podpress_nr_parts-1) ) {
					$podpress_path_above_of_WP .= DIRECTORY_SEPARATOR . $podpress_abspath_part;
				}
			}
			$i++;
		}
		// if the first part includes a double point then it is maybe a drive letter and no folder name
		if ( FALSE !== stripos($podpress_abspath_parts[0], ':') ) {
			if (FALSE !== stripos( php_uname(), 'win' )) {
				$podpress_abspath = ltrim($podpress_abspath, DIRECTORY_SEPARATOR);
				$podpress_path_above_of_WP = ltrim($podpress_path_above_of_WP, DIRECTORY_SEPARATOR);
			}
		}
		define('WP_USE_THEMES', false);
		
		// require the wp-config.php and make all the WP functions, constants and variables available
		if ( TRUE === is_readable($podpress_abspath.'/wp-config.php') ) {
			require_once($podpress_abspath.'/wp-config.php');
		} elseif( TRUE === is_readable($podpress_path_above_of_WP.'/wp-config.php') ) {
			require_once($podpress_path_above_of_WP.'/wp-config.php');
		} else {
			die('Error: could not find wp-config.php.');
		}

		require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_admin_functions.php');

		$customThemeFile = ABSPATH.'/wp-content/themes/'.get_option('template').'/podpress_theme.php';
		if ( FALSE === file_exists($customThemeFile) ) {
			$customThemeFile = ABSPATH.PLUGINDIR.'/podpress/podpress_theme.php';
		}

		if ( FALSE == isset($_GET['action']) OR TRUE == empty($_GET['action']) ) {
			$action_param = strtolower($_POST['action']);
			podpress_var_dump('podpress_backend.php - param filename: '.var_export(stripslashes($_POST['filename']), TRUE));
		} else {
			$action_param = strtolower($_GET['action']);
			podpress_var_dump('podpress_backend.php - param filename: '.var_export(stripslashes($_GET['filename']), TRUE));
		}
		
		if ( defined('NONCE_KEY') AND is_string(constant('NONCE_KEY')) AND '' != trim(constant('NONCE_KEY')) ) {
			$nonce_key = constant('NONCE_KEY');
		} else {
			$nonce_key = 'Af|F07*wC7g-+OX$;|Z5;R@Pi]ZgoU|Zex8=`?mO-Mdvu+WC6l=6<O^2d~+~U3MM';
		}
		
		switch( $action_param ) {
			case 'getrealurl' :
				if ( isset($_POST['_ajax_nonce']) AND TRUE == function_exists('wp_verify_nonce') AND TRUE == wp_verify_nonce($_POST['_ajax_nonce'], $nonce_key) ) {
					podPress_get_real_url($_POST['url']);
				} else {
					die('Error: Security check failed.');
				}
			break;
			case 'size':
				if ( isset($_POST['_ajax_nonce']) AND TRUE == function_exists('wp_verify_nonce') AND TRUE == wp_verify_nonce($_POST['_ajax_nonce'], $nonce_key) ) {
					podPress_isAuthorized('edit_posts');
					echo trim( podPress_getFileSize( stripslashes($_POST['filename']) ) );
				} else {
					die('Error: Security check failed.');
				}
			break;
			case 'duration':
				if ( isset($_POST['_ajax_nonce']) AND TRUE == function_exists('wp_verify_nonce') AND TRUE == wp_verify_nonce($_POST['_ajax_nonce'], $nonce_key) ) {
					podPress_isAuthorized('edit_posts');
					echo trim( podPress_getDuration( stripslashes($_POST['filename']) ) );
				} else {
					die('Error: Security check failed.');
				}
			break;
			case 'id3tags':
				if ( isset($_POST['_ajax_nonce']) AND TRUE == function_exists('wp_verify_nonce') AND TRUE == wp_verify_nonce($_POST['_ajax_nonce'], $nonce_key) ) {
					podPress_isAuthorized('edit_posts');
					echo podPress_showID3tags( stripslashes($_POST['filename']) );
				} else {
					die('Error: Security check failed.');
				}
			break;
			case 'id3image':
				if ( isset($_GET['_ajax_nonce']) AND TRUE == function_exists('wp_verify_nonce') AND TRUE == wp_verify_nonce($_GET['_ajax_nonce'], $nonce_key) ) {
					podPress_isAuthorized('edit_posts');
					if ( isset($_GET['tmpdownloadexists']) AND 'yes' === $_GET['tmpdownloadexists'] ) {
						podPress_getCoverArt( stripslashes($_GET['filename']), TRUE );
					} else {
						podPress_getCoverArt( stripslashes($_GET['filename']), FALSE );
					}
				} else {
					die('Error: Security check failed.');
				}
			break;
			case 'upgradeaction' :
				if ( isset($_POST['_ajax_nonce']) AND TRUE == function_exists('wp_verify_nonce') AND TRUE == wp_verify_nonce($_POST['_ajax_nonce'], $nonce_key) ) {
					require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_upgrade_functions.php');
					podPress_upgrade_via_ajax($_POST['upgr_process_nr'], $_POST['podpress_upgr_misc']);
				} else {
					die('Error: Security check failed.');
				}
			break;
			case 'curnrofrows' : // is an upgrade action
				if ( isset($_POST['_ajax_nonce']) AND TRUE == function_exists('wp_verify_nonce') AND TRUE == wp_verify_nonce($_POST['_ajax_nonce'], $nonce_key) ) {
					require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_upgrade_functions.php');
					podPress_current_nr_of_rows($_POST['podpress_upgr_misc']['table'], 'false', $_POST['upgr_process_nr'], 'ajax');
				} else {
					die('Error: Security check failed.');
				}
			break;
			case 'printupgradeform' : // is an upgrade action
				if ( isset($_POST['_ajax_nonce']) AND TRUE == function_exists('wp_verify_nonce') AND TRUE == wp_verify_nonce($_POST['_ajax_nonce'], $nonce_key) ) {
					require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_upgrade_functions.php');
					if ( isset($_POST['podpress_upgr_misc']['table']) AND ('stats' == $_POST['podpress_upgr_misc']['table'] OR 'statcounts' == $_POST['podpress_upgr_misc']['table']) ) {
						if ( isset($_POST['podpress_upgr_misc']['isinit']) AND ('false' == $_POST['podpress_upgr_misc']['isinit'] OR 'true' == $_POST['podpress_upgr_misc']['isinit']) ) {
							podPress_print_upgrade_form($_POST['podpress_upgr_misc']['table'], $_POST['podpress_upgr_misc']['isinit']);
						} else {
							podPress_print_upgrade_form($_POST['podpress_upgr_misc']['table'], 'false');
						}
					} else {
						if ( isset($_POST['podpress_upgr_misc']['isinit']) AND ('false' == $_POST['podpress_upgr_misc']['isinit'] OR 'true' == $_POST['podpress_upgr_misc']['isinit']) ) {
							podPress_print_upgrade_form('statcounts', $_POST['podpress_upgr_misc']['isinit']);
						} else {
							podPress_print_upgrade_form('statcounts', 'false');
						}
					}
				} else {
					die('Error: Security check failed.');
				}
			break;
			case 'finishupgradeaction' : // is an upgrade action
				if ( isset($_POST['_ajax_nonce']) AND TRUE == function_exists('wp_verify_nonce') AND TRUE == wp_verify_nonce($_POST['_ajax_nonce'], $nonce_key) ) {
					if ( isset($_POST['podpress_upgr_misc']['table']) AND ('stats' == $_POST['podpress_upgr_misc']['table'] OR 'statcounts' == $_POST['podpress_upgr_misc']['table']) ) {
						podpress_delete_upgrade_status('podpress_update_'.$_POST['podpress_upgr_misc']['table'].'_table');
					} else {
						die('Error: correct parameter is missing.');
					}
				} else {
					die('Error: Security check failed.');
				}
			break;

			case 'streamfile':
				//~ podPress_isAuthorized();
				//~ Header("Content-Type: ".$podPress->contentType."; charset=".$podPress->encoding."; filename=".basename($filename));
				//~ Header("Content-Disposition: inline; filename=".basename($filename));
			//~ break;
			default:
				die('Error: parameters are missing.');
			break;
		}
	} else {
		die('Error: some essential things are missing.');
	}
}
?>