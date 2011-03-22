<?php
/*
License:
 ==============================================================================

    Copyright 2006  Dan Kuykendall  (email : dan@kuykendall.org)
    Modifications:  Jeff Norris     (email : jeff@iscifi.tv)
    Thanks to The P2R Team ( prepare2respond.com ) for stats suggestions.


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
class podPressAdmin_class extends podPress_class {
	function podPressAdmin_class() {
		GLOBAL $wpdb;
		
		$this->path = get_option('siteurl').'/wp-admin/admin.php?page=podpress/podpress_stats.php';
		if (isset($_GET['display']) && is_string($_GET['display'])) {
			$this->path .= '&amp;display='.$_GET['display'];
		}
		
		// since 8.8.5 beta 3
		parent::wherestr_to_exclude_bots();
		
		$this->wpdb                 = $wpdb;

		/* set default language for graphs */
		$languages        = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$primary_language = $languages[0];
		if (in_array($primary_language, array('de-de', 'de'))) {
			/* German specification */
			setlocale(LC_TIME, 'de_DE@euro:UTF-8', 'de_de:UTF-8', 'de_DE:UTF-8', 'de:UTF-8', 'ge:UTF-8', 'German_Germany.1252:UTF-8');

			$local_settings = array(
			                        'numbers'       => array(',', '.'),
			                        'short_date'    => '%a.%n%d.%m.%y',
			                        'creation_date' => '%d.%m.%y',
			                      );
		} else {
			/* default specification: english */
			setlocale(LC_TIME, 'en_US:UTF-8', 'en_en:UTF-8', 'en:UTF-8');

			$local_settings = array(
			                        'numbers' => array('.', ','),
			                        'short_date' => '%a.%n%m/%d/%y',
			                        'creation_date' => '%m/%d/%y',
			                       );
		}
		$this->local_settings = $local_settings;

		$this->podPress_class();
		return;
	}

	#############################################
	#############################################

	function podSafeDigit($digit = 0) {
		if (is_numeric($digit) && ($digit < 10000)) {
			$digit = abs((int)$digit);
		} else {
			$digit = 0;
		}
		return $digit;
	}

	#############################################
	#############################################

	function paging($start, $limit, $total, $text = null) {
		$pages = ceil($total / $limit);
		$actualpage = ceil($start / $limit);

		if ($pages > 0) {
			if ($start > 0) {
				$start_new = ($start - $limit < 0) ? 0 : ($start - $limit);
				$right = '<a href="'.$this->path.'&amp;start='.$start_new.'">'.__('Next Entries &raquo;').'</a>';
			}

			if ($start + $limit < $total) {
				$start_new = ($start + $limit);
				$left = '<a href="'.$this->path.'&amp;start='.$start_new.'">'.__('&laquo; Previous Entries').'</a>';
			}

			echo '<div id="podPress_paging">'."\n";
			echo '	<div id="podPress_pagingLeft">'."\n";
			if ($pages > 1) {
				if (!is_null($text)) {
					echo $text;
				}
				echo '    <select name="podPress_pageSelect" onchange="javascript: window.location = \''.$this->path.'&start=\'+this.value;">';
				$i = 0;
				while ($i < $total) {
					$selected = ($i == $start) ? ' selected="selected"': null;
					$newi = ($i + $limit);
					if ($newi > $total) {
						$newi = $total;
					}
					echo '    	<option value="'.$i.'"'.$selected.'>'.ceil($total - ($newi - 1)).' - '.ceil($total - ($i)).'</option>';
					#$i = ($newi + 1);
					$i = $newi;
				}
				echo '	</select>';

				echo '	'.__('of', 'podpress').' '.$total;
			}
			echo '	</div>'."\n";

			echo '	<div id="podPress_pagingRight">';
				if ($start + $limit < $total) {
					echo $left;
				}
				if (($start + $limit < $total) AND ($start > 0)) {
					echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
				}
				if ($start > 0) {
					echo $right;
				}
			echo "	</div>\n";
			echo "</div>\n";
		}
	}
	
	// ntm: paging() revised for v8.8.5 beta 3
	// -'previous/next entries' string swaped
	// - '$text from x to y' - select box revised
	function paging2($start, $limit, $total, $text = null) {
		$pages = ceil($total / $limit);
		if ($pages > 0) {
			echo '<div id="podPress_paging">'."\n";
			echo '	<div id="podPress_pagingLeft">'."\n";
			if ($pages > 1) {
				if (!is_null($text)) {
					echo $text;
				}
				echo '    <select name="podPress_pageSelect" onchange="javascript: window.location = \''.$this->path.'&start=\'+this.value;">';
				$low = (1);
				for ($i=$low; $i <= $total; $i++) {
					$selected = (($i-1) == $start) ? ' selected="selected"': '';
					if ( ($i % $limit) == 1 ) {
						if ((($low-1)+$limit) < $total) {
							$high = ($low-1)+$limit;
						} else {
							$high = $total;
						}
						if ($low != $high) {
							echo '    		<option value="'.($i-1).'"'.$selected.'>'.$low.' - '.$high.'</option>';
						} else {
							echo '    		<option value="'.($i-1).'"'.$selected.'>'.$high.'</option>';
						}
						$low += $limit;
					} 
				}
				echo '	</select>';

				echo '	'.__('of', 'podpress').' '.$total;
			}
			//echo '<p>'.$start.' | '.$limit.' | '.$total.' | '.$text.'</p>';
			echo '	</div>'."\n";

			if ($start + $limit < $total) {
				$right = '<a href="'.$this->path.'&amp;start='.($start+$limit).'">'.__('Next Entries &raquo;').'</a>';
			}
			if ($start > 0) {
				$left = '<a href="'.$this->path.'&amp;start='.($start-$limit).'">'.__('&laquo; Previous Entries').'</a>';
			}

			echo '	<div id="podPress_pagingRight">';
				if ($start > 0) {
					echo $left;
				}
				if (($start + $limit < $total) AND ($start > 0)) {
					echo '&nbsp;|&nbsp;';
				}
				if ($start + $limit < $total) {
					echo $right;
				}
			echo "	</div>\n";
			echo "</div>\n";
		}
	}

	#############################################
	#############################################

	function podGetHighest($data) {
		$highest = 0;
		foreach ($data AS $key => $idata) {
			if ($idata->value > $highest) {
				$highest = $idata->value;
			}
		}
		return $highest;
	}

	#############################################
	#############################################

	function podGraph($data, $splitter = null) {
		$cnt_data   = count($data);

		if ($cnt_data > 0) {
    		$box_width  = 600;
    		$box_height = 400;
    		$bar_margin = 2;
    		$bar_width  = floor(($box_width - ($cnt_data * $bar_margin)) / $cnt_data);
    		$bar_width  = ($bar_width < 2) ? 2: $bar_width;
    		$highest    = $this->podGetHighest($data);
    		$factor     = ($highest / $box_height);

    		$output     = '<div id="podGraph" style="height: '.$box_height.'px; width: '.$box_width.'px; float: left; border: 1px solid #ccc; padding: 0.5em;">';
    		foreach ($data AS $key => $idata) {
    			$image_height  = floor($idata->value / $factor);
    			$margin  = ($box_height - $image_height);
    			$bgcolor = (is_array($splitter) && ($idata->$splitter[0] == $splitter[1])) ? '#aaa': '#ccc';
    			$bgcolor = ($image_height == $box_height) ? '#d00': $bgcolor;
    			$output .= '    <div style="float: left; margin-top: '.$margin.'px; margin-right: '.$bar_margin.'px; height: '.$image_height.'px; background-color: '.$bgcolor.'; width: '.$bar_width.'px; cursor: help;'.$split.'" title="'.$idata->title.' ('.number_format($idata->value, 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).' x)"></div>'."\n";
    		}
    		$output .= "</div>\n";
		} else {
            $output = '<p>'.__('We\'re sorry. At the moment we don\'t have enough data collected to display the graph.', 'podpress')."</p>\n";
		}

		return $output;
	}

	#############################################
	#############################################

	function settings_stats_edit() {
		GLOBAL $wpdb, $wp_version;
		podPress_isAuthorized();
		$baseurl = get_option('siteurl') . '/wp-admin/admin.php?page=podpress/podpress_stats.php&display=';
		echo '<div class="wrap">'."\n";
		
		if ( TRUE == version_compare($wp_version, '2.7', '>=') ) {
			echo '<div id="podpress-icon" class="icon32"><br /></div>';
		} 
		if ( TRUE == version_compare($wp_version, '2.8', '>=') ) {
			echo '	<h2>'.__('Download Statistics', 'podpress').'</h2>'."\n";
			// get the plugins version information via the WP plugins version check
			if ( TRUE == version_compare($wp_version, '2.9', '>=') ) {
				$versioninfo = get_site_transient( 'update_plugins' );
			} else {
				$versioninfo = get_transient( 'update_plugins' );
			}
			// If there is a new version then there is a 'response'. This is the method from the plugins page. 
			if ( FALSE !== isset($versioninfo->response[plugin_basename(dirname(__FILE__).'/podpress.php')]->new_version) ) {
				echo '<div class="message updated"><p><a href="http://wordpress.org/extend/plugins/podpress/" target="_blank">'.__('a new podPress version is available', 'podpress').'</a></p></div>';
			}
		} else {
			echo '	<h2>'.__('Download Statistics', 'podpress').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.mightyseek.com/podpress/#download" target="_new"><img src="http://www.mightyseek.com/podpress_downloads/versioncheck.php?current='.PODPRESS_VERSION.'" alt="'.__('Checking for updates... Failed.', 'podpress').'" border="0" /></a></h2>'."\n";
		}
		
		if($this->settings['statLogging'] == 'Full' || $this->settings['statLogging'] == 'FullPlus') {
			// Show all statistics which are based on wp_stats [stat logging: Full and Full+]
			$navi = array(
				'downloads_per_media_file' => __('Downloads Per Media File', 'podpress'),
				'downloads_per_post' => __('Downloads Per Post', 'podpress'),
				'topips' => __('Downloads Per IP Address', 'podpress'),
				'graphbydate' => __('Graph by Date', 'podpress'),
				'rawstats' => __('Raw Stats', 'podpress'),
			);
			echo '	<ul id="podPress_navi">'."\n";
			foreach ($navi AS $key => $value) {
				$active = (($_GET['display'] == $key) OR (!$_GET['display'] AND ($key == 'downloads_per_media_file'))) ? ' class="current"': null;
				echo '        	<li class="podpress_stats_sub_menu_item"><a href="'.$baseurl.$key.'"'.$active.'>'.$value.'</a>';
				if($this->checkGD()) {
					if ($value == __('Graph by Date', 'podpress')) {
						echo ' (<a href="'.$baseurl.'graphbydatealt" title="'.sprintf(__('An alternative view of the %1$s', 'podpress'), __('Graph by Date', 'podpress')).'">'. __('alt', 'podpress').'</a>)';
					}
				}
				echo '</li>'."\n";
			}
			echo '	</ul>'."\n";
			
			// bot management menu (since 8.8.5 beta 3)
			$navi2 = array(
				'botdb_mark_bots' => __('Select / Deselect Bots', 'podpress'),
				'botdb_list' => __('List of Bots', 'podpress'),
			);
			echo '	<ul id="podPress_navi">'."\n";
			foreach ($navi2 AS $key => $value) {
				$active = (($_GET['display'] == $key) OR (!$_GET['display'] AND ($key == 'quickcounts'))) ? ' class="current"': null;
				echo '        	<li class="podpress_stats_sub_menu_item"><a href="'.$baseurl.$key.'"'.$active.'>'.$value.'</a></li>'."\n";
			}
			echo '	</ul>'."\n";
			
		} else {
		
			// Show all statistics which are based on wp_statcounts	[stat logging: Counts Only]
			// old: $_GET['display'] = 'downloads_per_media_file';
			$navi = array(
				'quickcounts' => __('Quick Counts', 'podpress'),
				'graphbypost' => __('Graph by Post', 'podpress'),
			);
			echo '	<ul id="podPress_navi">'."\n";
			foreach ($navi AS $key => $value) {
				$active = (($_GET['display'] == $key) OR (!$_GET['display'] AND ($key == 'quickcounts'))) ? ' class="current"': null;
				echo '        	<li class="podpress_stats_sub_menu_item"><a href="'.$baseurl.$key.'"'.$active.'>'.$value.'</a>';
				if($this->checkGD()) {
					if($value == __('Graph by Post', 'podpress')) {
						echo ' (<a href="'.$baseurl.'graphbypostalt" title="'.sprintf(__('An alternative view of the %1$s', 'podpress'), __('Graph by Post', 'podpress')).'">'. __('alt', 'podpress').'</a>)';
					}
				}
				echo '</li>'."\n";
			}
			echo '	</ul>'."\n";

		}

		// Set Paging-Settings
		$start = (isset($_GET['start'])) ? $this->podSafeDigit($_GET['start']): 0;
		
		//~ ####################
		//~ Limit is limits the number of rows of the statistic tables
		$limit = 25;
		//~ ####################
		
		// ntm: since 8.8.5 beta 3 there are two different default statistic tables quickcounts and downloads_per_media_file that is why the default value has to be found before that Switch (and not with this Switch)
		if (FALSE == isset($_GET['display']) OR FALSE !== empty($_GET['display'])) {
			if ($this->settings['statLogging'] == 'Full' || $this->settings['statLogging'] == 'FullPlus') {
				$show_this_page = 'downloads_per_media_file';
			} else {
				$show_this_page = 'quickcounts';
			}
		} else {
			$show_this_page = $_GET['display'];
		}
		Switch($show_this_page) {
			case 'botdb_list':
				// ntm: stat logging: Full and Full+
				podPress_isAuthorized();
				$botdb = get_option('podpress_botdb');
				if ( 'botdb' == $_POST['podPress_submitted'] ) {
					if ( function_exists('check_admin_referer') ) {
						check_admin_referer('podPress_botdb_nonce');
					}
					$IPs = $_POST['podpress_remote_ips'];
					$fullbotnames = stripslashes_deep($_POST['podpress_user_agents']);
					if ( is_array($botdb) ) {
						$something_removed=FALSE;
						if ( is_array($IPs) and is_array($botdb['IP'])) {
							$botdb['IP'] = array_diff($botdb['IP'], $IPs);
							sort($botdb['IP']);
						}
						if ( is_array($fullbotnames) and is_array($botdb['fullbotnames'])) {
							$botdb['fullbotnames'] = array_diff($botdb['fullbotnames'], $fullbotnames);
							sort($botdb['fullbotnames']);
						}
					}
					$updated=update_option('podpress_botdb', $botdb);
					if (isset($updated) AND $updated == TRUE) {
						echo '<div id="message" class="updated fade"><p>'. __('Settings Saved', 'podpress').'</p></div>';
					}
				}
				
				echo '	<div class="wrap">'."\n";
				echo '		<fieldset class="options">'."\n";
				echo '			<legend>'.__('The list of IP addresses and names of bots', 'podpress').'</legend>'."\n";
				echo '			<form method="post">'."\n";
				if ( function_exists('wp_nonce_field') ) { // since WP 2.0.4
					wp_nonce_field('podPress_botdb_nonce');
				}
				echo '			<table class="the-list-x widefat">'."\n";
				echo '				<thead>'."\n";
				echo '				<tr><th rowspan="2">'.__('Nr.', 'podpress').'</th><th rowspan="2">'.__('Bot IP Address', 'podpress').'</th><th rowspan="2">'.__('Bot User Agent', 'podpress').'</th><th colspan="2">'.__('Remove this', 'podpress').'</th></tr><tr><th>'.__('IP', 'podpress').'</th><th>'.__('Name', 'podpress').'</th></tr>'."\n";
				echo '				</thead>'."\n";
				echo '				<tbody>'."\n";
				$nobots = FALSE;
				if (is_array($botdb) and (is_array($botdb['fullbotnames']) OR is_array($botdb['IP'])) ) {
					$botnames_len = count($botdb['fullbotnames']);
					$IPs_len = count($botdb['IP']);
					$rows_total = max($botnames_len, $IPs_len);
					if ( $rows_total > ($start+$limit) ) {
						$high = $start+$limit;
					} else {
						$high = $rows_total;
					}
					$low = ($start+1);
					for ($i=$low; $i <= $high; $i++) {
						$style = ($i % 2) ? '' : ' class="alternate"';
						echo '				<tr'.$style.'>'."\n";
							echo '                  			<td>'.($start +$i).'.</td>'."\n";
						if ($i <= ($IPs_len-$start)) {
							$col_ip = '                  			<td>'.stripslashes($botdb['IP'][($i-1)]).'</td>'."\n";
							$col_ip_chb = '                  			<td><input type="checkbox" id="podpress_remote_ip_'.$i.'" name="podpress_remote_ips[]" value="'.$botdb['IP'][($i-1)].'" '.$ip_chb_checked.' /></td>'."\n";
						} else {
							$col_ip = $col_ip_chb = '                  			<td></td>'."\n";
						}
						if ($i <= ($botnames_len-$start)) {
							$col_botname = '                  			<td>'.stripslashes($botdb['fullbotnames'][($i-1)]).'</td>'."\n";
							$col_botname_chb = '                  			<td><input type="checkbox" id="podpress_user_agent_'.$i.'" name="podpress_user_agents[]" value="'.attribute_escape($botdb['fullbotnames'][($i-1)]).'" '.$name_chb_checked.' /></td>'."\n";
						} else {
							$col_botname = $col_botname_chb = '                  			<td></td>'."\n";
						}
						echo $col_ip.$col_botname.$col_ip_chb.$col_botname_chb;
						echo '              			</tr>'."\n";
					}
					if ( 0 == $botnames_len AND 0 == $IPs_len ) {
						$nobots = TRUE;
					}
				}  else {
					$nobots = TRUE;
				}				
				if (TRUE == $nobots) {
					echo '				<td colspan="5">'.__('Currently are no IP addresses or user agents marked as bots.', 'podpress').'</td>'."\n";
				}
				echo '				</tbody>'."\n";
				echo '				<tfood>'."\n";
				echo '				<tr>'."\n";
				echo '				<th colspan="5">'."\n";
					// Show paging
					echo $this->paging2($start, $limit, $rows_total, __('names and IPs','podpress'));
				echo '				</th>'."\n";
				echo '              			</tr>'."\n";
				echo '				</tfood>'."\n";
				echo '			</table>'."\n";				
				echo '			<p class="submit"> '."\n";
				echo '				<input class="button-primary" type="submit" name="Submit" value="'.__('Remove elements', 'podpress').' &raquo;" /><br />'."\n";
				echo '			</p> '."\n";
				echo '			<input type="hidden" name="podPress_submitted" value="botdb" />'."\n";
				echo '			</form>'."\n";
				echo '		</fieldset>'."\n";
				echo '	</div>';
				break;
			case 'botdb_mark_bots':
				// ntm: stat logging: Full and Full+
				podPress_isAuthorized();
				$blog_charset = get_bloginfo('charset');
				$botdb = get_option('podpress_botdb');
				if ( 'botdb' == $_POST['podPress_submitted'] ) {
					if ( function_exists('check_admin_referer') ) {
						check_admin_referer('podPress_botdb_nonce');
					}
					$IPs = $_POST['podpress_remote_ips'];
					$fullbotnames = stripslashes_deep($_POST['podpress_user_agents']);
					
					if (is_array($botdb)) {
						$current_IP_set = $_POST['podpress_current_IP_set'];
						$unique_current_data_IPs = array_unique($_POST['podpress_current_IP_set']);
						
						//add new bots
						if (is_array($IPs)) {
							$unique_IPs = array_unique($IPs);
							foreach ($unique_IPs as $IP) {
								if (is_array($botdb['IP'])) {
									if ( FALSE === array_search($IP, $botdb['IP']) )  {
										$botdb['IP'][] = $IP;
									}
								} else {
									$botdb['IP'][] = $IP;
								}
							}
							
							// eventually remove bots
							$unmarked_IPs = array_diff($unique_current_data_IPs, $unique_IPs);
							$botdb['IP'] = array_diff($botdb['IP'], $unmarked_IPs);
							sort($botdb['IP']);
						} else {
							if (is_array($botdb['IP'])) {
								$botdb['IP'] = array_diff($botdb['IP'], $unique_current_data_IPs);
								sort($botdb['IP']);
							}
						}
						
						$current_user_agent_set = $_POST['podpress_current_user_agent_set'];
						$unique_current_data_fbnames = array_unique($_POST['podpress_current_user_agent_set']);
						if (is_array($fullbotnames)) {
							$unique_fullbotnames = array_unique($fullbotnames);
							foreach ($unique_fullbotnames as $fullbotname) {
								if (is_array($botdb['fullbotnames'])) {
									if ( FALSE === array_search($fullbotname, $botdb['fullbotnames']) ) {
										$botdb['fullbotnames'][] = $fullbotname;
									}
								} else {
									$botdb['fullbotnames'][] = $fullbotname;
								}
							}
							
							// eventually remove bots
							$unmarked_fullbotnames = array_diff($unique_current_data_fbnames, $unique_fullbotnames);
							$botdb['fullbotnames'] = array_diff($botdb['fullbotnames'], $unmarked_fullbotnames);
							sort($botdb['fullbotnames']);
						} else {
							// eventually remove bots
							if (is_array($botdb['fullbotnames'])) {
								$botdb['fullbotnames'] = array_diff($botdb['fullbotnames'], $unique_current_data_fbnames);
								sort($botdb['fullbotnames']);
							}
						}
					} else {
						//add new bots (first time)
						if (is_array($IPs)) {
							$unique_IPs = array_unique($IPs);
							foreach ($unique_IPs as $IP) {
								$botdb['IP'][] = $IP;
							}
						}
						if (is_array($fullbotnames)) {
							$unique_fullbotnames = array_unique($fullbotnames);
							foreach ($unique_fullbotnames as $fullbotname) {
								$botdb['fullbotnames'][] = $fullbotname;
							}
						}
					}
					
					$updated=update_option('podpress_botdb', $botdb);
					if (isset($updated) AND $updated == TRUE) {
						echo '<div id="message" class="updated fade"><p>'. __('Settings Saved', 'podpress').'</p></div>';
					}
				}
				$where='';
				$rows_total = intval($wpdb->get_var('SELECT COUNT(DISTINCT remote_ip, user_agent) AS total FROM '.$wpdb->prefix.'podpress_stats '.$where));
				$query_string = 'SELECT DISTINCT remote_ip, user_agent FROM '.$wpdb->prefix.'podpress_stats '.$where.'ORDER BY dt DESC LIMIT '.$start.', '.$limit;
				$stats = $wpdb->get_results($query_string);
	
				echo '	<div class="wrap">'."\n";
				echo '		<fieldset class="options">'."\n";
				echo '			<legend>'.__('Which IP address or user agent name is from a web bot?', 'podpress').'</legend>'."\n";
				echo '			<p>'.__('Probably not every counted download is a download of a real human listener. Some hits are eventually from so called <a href="http://en.wikipedia.org/wiki/Internet_bot" target="_blank" title="en.Wikipedia: Internet Bot">Internet bots</a>. Every downloader has an <a href="http://en.wikipedia.org/wiki/IP_address" target="_blank" title="en.Wikipedia: IP Address">IP address</a> and a <a href="http://en.wikipedia.org/wiki/User_agent" target="_blank" title="en.Wikipedia: User Agent">user agent</a> name. The user agent name indicates often very good whether it is the name of a browser of a real listener or a name of a bot. Whether a IP address is one of a bot or not is more difficult. But there are some websites which can help to find out more about an IP address. (Some bots are using IP addresses only temporarily.)<br />The list below shows all unique combinations of IP addresses and user agent names. It is possible to select (resp. deselect) only the IP address or the user agent name or both. Downloads of the selected IP addresses or user agents do not appear in the statistics.', 'podpress').'</p>'."\n";				
				echo '			<form method="post">'."\n";
				if ( function_exists('wp_nonce_field') ) { // since WP 2.0.4
					wp_nonce_field('podPress_botdb_nonce');
				}
				echo "\n".'			<table class="the-list-x widefat">'."\n";
				echo '				<thead>'."\n";
				echo '				<tr><th rowspan="2">'.__('Nr.', 'podpress').'</th><th rowspan="2">'.__('IP Address', 'podpress').'</th><th rowspan="2">'.__('User Agent', 'podpress').'</th><th colspan="2">'.__('Is it a bot?', 'podpress').'</th></tr><tr><th>'.__('IP', 'podpress').'</th><th>'.__('Name', 'podpress').'</th></tr>'."\n";
				echo '				</thead>'."\n";
				echo '				<tbody>'."\n";
				if(0 < count($stats)) {
					$i = 0;
					foreach ($stats as $stat) {
						++$i;
						$alternate = ($i % 2) ? '' : 'alternate';
						$bot_style = '';
						$ip_chb_checked = '';
						$name_chb_checked = '';
						if (TRUE == is_array($botdb)) {
							if (TRUE == is_array($botdb['IP']) AND FALSE !== array_search($stat->remote_ip, $botdb['IP'])) {
								$bot_style = ' podpress_is_bot';
								$ip_chb_checked = ' checked="checked"';
							}
							if (TRUE == is_array($botdb['fullbotnames']) AND FALSE !== array_search($stat->user_agent, $botdb['fullbotnames'])) {
								$bot_style = ' podpress_is_bot';
								$name_chb_checked = ' checked="checked"';
							}
						}
						echo '				<tr id ="podpress_ip_user_agent_row_'.($i).'" class="'.$alternate.$bot_style.'">'."\n";
						echo '                  			<td>'.($start+$i).'</td>'."\n";
						echo '                  			<td>'.$stat->remote_ip.'<input type="hidden" name="podpress_current_IP_set[]" value="'.$stat->remote_ip.'" /></td>'."\n";
						echo '                  			<td>'.podPress_strlimiter2($stat->user_agent, 80, TRUE).'<input type="hidden" name="podpress_current_user_agent_set[]" value="'.attribute_escape($stat->user_agent).'" /></td>'."\n";
						echo '                  			<td><input type="checkbox" id="podpress_remote_ip_'.$i.'" name="podpress_remote_ips[]" value="'.$stat->remote_ip.'" onclick="podpress_mark_same_all_bots( \'remote_ips\' , '.$i.', '.($start).' );" '.$ip_chb_checked.' /></td>'."\n";
						echo '                  			<td><input type="checkbox" id="podpress_user_agent_'.$i.'" name="podpress_user_agents[]" value="'.attribute_escape($stat->user_agent).'" onclick="podpress_mark_same_all_bots( \'user_agent\' , '.$i.', '.($start).' );" '.$name_chb_checked.' /></td>'."\n";
						echo '              			</tr>'."\n";
					}
				} else {
					echo '<td colspan="5">'.__('No downloads yet.','podpress')."</td>\n";
				}

				echo '				</tbody>'."\n";
				echo '				<tfood>'."\n";
				echo '				<tr>'."\n";
				echo '				<th colspan="5">'."\n";
					// Show paging
					echo $this->paging2($start, $limit, $rows_total, __('names and IPs','podpress'));
				echo '				</th>'."\n";
				echo '              			</tr>'."\n";
				echo '				</tfood>'."\n";
				echo '			</table>'."\n";				
				
				echo '				<p class="submit"> '."\n";
				echo '					<input class="button-primary" type="submit" name="Submit" value="'.__('Update Options', 'podpress').' &raquo;" /><br />'."\n";
				echo '				</p> '."\n";
				echo '				<input type="hidden" name="podPress_submitted" value="botdb" />'."\n";
				echo '			</form>'."\n";
				echo '		</fieldset>'."\n";
				echo '	</div>';
				break;
			case 'downloads_per_media_file':
				// ntm: stat logging: Full and Full+
				$where = $this->wherestr_to_exclude_bots('pod');
				
				$query_string = "SELECT COUNT(DISTINCT pod.media) as total_rows FROM ".$wpdb->prefix."podpress_stats as pod ".$where;
				$rows_total = intval($wpdb->get_var($query_string));

				$query_string = "SELECT DISTINCT (pod.media) FROM ".$wpdb->prefix."podpress_stats as pod ".$where." LIMIT ".$start.", ".$limit;
				$posts_with_podpressmedia = $wpdb->get_results($query_string);
				$nr_postswpm = count($posts_with_podpressmedia);
				if ( 0 < $nr_postswpm) {
					$i=1;
					if (FALSE == empty($where)) {
						$where_posts = "AND pod.media IN (";
					} else {
						$where_posts = "WHERE pod.media IN (";
					}
					foreach ($posts_with_podpressmedia as $post) {
						if ($i == $nr_postswpm) {
							$where_posts .= "'".$post->media."'";
						} else {
							$where_posts .= "'".$post->media."', ";
						}
						$i++;
					}
					$where_posts .= ") ";
				} else {
					$where_posts = '';
				}

				$query_string="SELECT pod.media, pod.method, COUNT(*) as downloads FROM ".$wpdb->prefix."podpress_stats as pod ".$where.$where_posts."GROUP BY pod.method, pod.media ORDER BY pod.media DESC";
				$stat_data_sets = $wpdb->get_results($query_string);
				
				if (FALSE == empty($where)) {
					$where_or_and = "AND";
				} else {
					$where_or_and = "WHERE";
				}				
				$methods = Array('feed', 'web', 'play');
				foreach ($methods as $method) {
					$query_string="SELECT COUNT(*) as downloads, pod.media FROM ".$wpdb->prefix."podpress_stats AS pod ".$where.$where_or_and." pod.method='".$method."' GROUP BY pod.media ORDER BY downloads DESC";
					$downloads_col = $wpdb->get_col($query_string);
					switch ($method) {
						case 'feed' :
							$feed_max = intval($downloads_col[0]);
						break;
						case 'web' :
							$web_max = intval($downloads_col[0]);
						break;
						case 'play' :
							$play_max = intval($downloads_col[0]);
						break;
					}		
				}
				$query_string="SELECT COUNT(*) as downloads, pod.media FROM ".$wpdb->prefix."podpress_stats AS pod ".$where." GROUP BY pod.media ORDER BY downloads DESC";
				$downloads_col = $wpdb->get_col($query_string);
				$total_max = intval($downloads_col[0]);
				
				// prepare the query result for the output:
				// - if a media file was not downloaded  by one or more method then add this method  and a int(0) to the media file 		
				foreach ($stat_data_sets as $stat_data_set) {
					$feed = $web = $play = 0;
					switch ($stat_data_set->method) {
						case 'feed' :
							$feed = intval($stat_data_set->downloads);
						break;
						case 'web' :
							$web = intval($stat_data_set->downloads);
						break;
						case 'play' :
							$play = intval($stat_data_set->downloads);
						break;
					}
					$total_sum = $feed+$web+$play;
					$stats[$stat_data_set->media]['feed'] += $feed;
					$stats[$stat_data_set->media]['web'] += $web;
					$stats[$stat_data_set->media]['play'] += $play;
					$stats[$stat_data_set->media]['total'] += $total_sum;
				}
				
				// sort the media files by their 'total' value
				if (is_array($stats)) {
					uasort($stats, array(self, 'sort_downloads_per_media_desc'));
				}
				echo '	<div class="wrap">'."\n";
				echo '		<fieldset class="options">'."\n";
				echo '			<legend>'.__('Downloads Per Media File', 'podpress').'</legend>'."\n";
				echo '			<table class="the-list-x widefat">'."\n";
				echo '				<thead>';
				echo "				<tr>\n";
				echo '                  			<th rowspan="2">'.__('Nr.', 'podpress')."</th>\n";
				echo '                  			<th rowspan="2">'.__('Media File', 'podpress')."</th>\n";
				echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Feed', 'podpress')."</th>\n";
				echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Web', 'podpress')."</th>\n";
				echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Play', 'podpress')."</th>\n";
				echo '                  			<th class="podpress_stats_nr_head" rowspan="2">'.__('Total', 'podpress')."</th>\n";
				echo '              			</tr>'."\n";
				echo '				<tr>'."\n";
				echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
				echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
				echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
				echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
				echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
				echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
				echo '              			</tr>'."\n";
				echo '				</thead>';
				echo '				<tbody>';
				if (0 < count($stat_data_sets)) {
					$i = 0;
					foreach ( $stats as $media => $downloads_per_method ) {
						$i++;
						$style = ($i % 2 != 0) ? '' : ' class="alternate"';
						$highest_feed = ($downloads_per_method['feed'] == $feed_max AND 0 < $feed_max) ? ' podpress_stats_highest': '';
						$highest_web = ($downloads_per_method['web'] == $web_max AND 0 < $web_max) ? ' podpress_stats_highest': '';
						$highest_play = ($downloads_per_method['play'] == $play_max AND 0 < $play_max) ? ' podpress_stats_highest': '';
						$highest_total = ($downloads_per_method['total'] == $total_max AND 0 < $total_max) ? ' podpress_stats_highest': '';
						$perc_feed = number_format(($downloads_per_method['feed'] * 100 / $downloads_per_method['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
						$perc_web = number_format(($downloads_per_method['web']  * 100 / $downloads_per_method['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
						$perc_play = number_format(($downloads_per_method['play'] * 100 / $downloads_per_method['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
						echo '		<tr'.$style.'>'."\n";
						echo '                  	<td>'.($start +$i).'.</td>'."\n";
						echo '                  	<td>'.podPress_strlimiter2(urldecode($media), 50, TRUE).'</td>'."\n";
						echo '                  	<td class="podpress_stats_numbers_abs'.$highest_feed.'">'.number_format($downloads_per_method['feed'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
						echo '                  	<td class="podpress_stats_numbers_percent'.$highest_feed.'">'.$perc_feed."</td>\n";
						echo '                  	<td class="podpress_stats_numbers_abs'.$highest_web.'" >'.number_format($downloads_per_method['web'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
						echo '                  	<td class="podpress_stats_numbers_percent'.$highest_web.'">'.$perc_web."</td>\n";
						echo '                  	<td class="podpress_stats_numbers_abs'.$highest_play.'">'.number_format($downloads_per_method['play'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
						echo '                  	<td class="podpress_stats_numbers_percent'.$highest_play.'">'.$perc_play."</td>\n";
						echo '                  	<td class="podpress_stats_numbers_total'.$highest_total.'">'.number_format($downloads_per_method['total'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
						echo '		</tr>'."\n";
					}
				} else {
					if (FALSE == empty($where)) {
						echo '<td colspan="9">'.__('No downloads yet. (Bots have been filtered.)','podpress')."</td>\n";
					} else {
						echo '<td colspan="9">'.__('No downloads yet.','podpress')."</td>\n";
					}
				}
				echo '				</tbody>';
				echo '				<tfood>'."\n";
				echo '				<tr>'."\n";
				echo '				<th colspan="9">'."\n";
					// Show paging
					echo $this->paging2($start, $limit, $rows_total, __('Ranks','podpress'));
				echo '				</th>'."\n";
				echo '              			</tr>'."\n";
				echo '				</tfood>'."\n";
				echo '			</table>'."\n";
				echo '		</fieldset>'."\n";
				echo '	</div>';
				break;
			case 'rawstats':
				// ntm: stat logging: Full and Full+
				$date_format = get_option('date_format');
				$time_format = get_option('time_format');
				$botdb = get_option('podpress_botdb');
				$where = '';
				$rows_total = $wpdb->get_var('SELECT COUNT(*) FROM '.$wpdb->prefix.'podpress_stats '.$where);
				$stats = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'podpress_stats '.$where.'ORDER BY id DESC LIMIT '.$start.', '.$limit);
				echo '	<div class="wrap">'."\n";
				echo '		<fieldset class="options">'."\n";
				echo '			<legend>'.__('The raw list', 'podpress').'</legend>'."\n";
				echo '			<table class="the-list-x widefat">'."\n"; //width="100%" cellpadding="1" cellspacing="1"
				echo '				<thead>';
				echo '				<tr><th>'.__('Hit', 'podpress').'</th><th>'.__('Media File', 'podpress').'</th><th>'.__('Method', 'podpress').'</th><th>'.__('IP', 'podpress').'</th><th>'.__('User Agent', 'podpress').'</th><th>'.__('Timestamp', 'podpress').'</th></tr>'."\n";
				echo '				</thead>';
				echo '				<tbody>';
				if(0 < count($stats)) {
					if ( TRUE === is_array($botdb['IP']) OR TRUE === is_array($botdb['fullbotnames']) ) {
						$i = 0;
						foreach ($stats as $stat) {
							++$i;
							$style = ($i % 2) ? '' : 'alternate';
							
							if ( (FALSE !== empty($botdb['fullbotnames']) OR FALSE === array_search($stat->user_agent, $botdb['fullbotnames']))) {
								if ( (FALSE !== empty($botdb['IP']) OR FALSE === array_search($stat->remote_ip, $botdb['IP']))) {
									$bot_style = '';
								} else {
									$bot_style = ' podpress_is_bot';
								}
							} else {
								$bot_style = ' podpress_is_bot';
							}
							echo '		<tr class="'.$style.$bot_style.'">'."\n";
							echo '                  	<td>'.($start +$i).'.</td>'."\n";
							echo '                  	<td>'.podPress_strlimiter2(urldecode($stat->media), 20, TRUE).'</td>'."\n";
							echo '                  	<td>'.$stat->method.'</td>'."\n";
							// iscifi : mod of stats output to create a link to domaintools.com whois lookup
							// domaintools seems faster and provides more concise infomation, url can not have trailing /

							echo '                  	<td><a href="http://whois.domaintools.com/'.$stat->remote_ip.'" target="_blank" title="'.__('Look for more details about this IP at whois.domaintools.com', 'podpress').'">'.$stat->remote_ip.'</a></td>'."\n";
							// OLD code where this .echo '                  <td>'.$stat->remote_ip.'</td>'."\n";
							echo '                  	<td>'.podPress_strlimiter2($stat->user_agent, 50, TRUE).'</td>'."\n";
							echo '                  	<td>'.date($date_format.' - '.$time_format,  intval($stat->dt)).'</td>'."\n";
							echo '		</tr>'."\n";
						}
					} else {
						$i = 0;
						foreach ($stats as $stat) {
							++$i;
							$style = ($i % 2) ? '' : 'alternate';
							echo '				<tr class="'.$style.'">'."\n";
							echo '                  <td>'.($start +$i).'.</td>'."\n";
							echo '                  <td>'.podPress_strlimiter2(urldecode($stat->media), 20, TRUE).'</td>'."\n";
							echo '                  <td>'.$stat->method.'</td>'."\n";
							// iscifi : mod of stats output to create a link to domaintools.com whois lookup
							// domaintools seems faster and provides more concise infomation, url can not have trailing /

							echo '                  <td><a href="http://whois.domaintools.com/'.$stat->remote_ip.'" target="_blank">'.$stat->remote_ip.'</a></td>'."\n";
							// OLD code where this .echo '                  <td>'.$stat->remote_ip.'</td>'."\n";
							echo '                  <td>'.podPress_strlimiter2($stat->user_agent, 50, TRUE).'</td>'."\n";
							echo '                  <td>'.date($date_format.' - '.$time_format,  intval($stat->dt)).'</td>'."\n";
							echo '              </tr>'."\n";
						}
					}
				} else {
					echo '<td colspan="6">'.__('No downloads yet.','podpress').'</td>'."\n";
				}
				echo '				</tbody>';
				echo '				<tfood>'."\n";
				echo '				<tr>'."\n";
				echo '				<th colspan="6">'."\n";
					// Show paging
					echo $this->paging2($start, $limit, $rows_total, __('Hit','podpress'));
				echo '				</th>'."\n";
				echo '              			</tr>'."\n";
				echo '				</tfood>'."\n";
				echo '			</table>'."\n";
				echo '		</fieldset>'."\n";
				echo '	</div>';
				break;
			case 'topips':
				// ntm: stat logging: Full and Full+
				$where = $this->wherestr_to_exclude_bots();
				$rows_total = ($wpdb->get_var('SELECT COUNT(DISTINCT remote_ip) as uniq FROM '.$wpdb->prefix.'podpress_stats '.$where));
				
				$sql   = 'SELECT remote_ip AS IPAddress, COUNT(DISTINCT remote_ip, media) as uniq, COUNT( * ) AS total FROM '.$wpdb->prefix.'podpress_stats '.$where.'GROUP BY remote_ip ORDER BY total DESC LIMIT '.$start.', '.$limit;
				$stats = $wpdb->get_results($sql);
				
				echo '	<div class="wrap">'."\n";
				echo '		<fieldset class="options">'."\n";
				echo '			<legend>'.__('Top IP Addresses', 'podpress').'</legend>'."\n";
				echo '			<table class="the-list-x widefat">'."\n";
				echo '				<thead>';
				echo '				<tr><th>'.__('Nr.', 'podpress').'</th><th>'.__('IP Address', 'podpress').'</th><th><abbr class="podpress_abbr" title="'.__('Only one download per file from this IP address has been counted. In other words: It is the number of different files which were downloaded from this IP address.','podpress').'">'.__('Unique Files', 'podpress').'</abbr></th><th>'.__('Total', 'podpress').'</th></tr>'."\n";
				echo '				</thead>';
				echo '				<tbody>';
				if(0<count($stats)) {
					$i = 0;
					foreach ($stats as $stat) {
						++$i;
						$style = ($i % 2) ? '' : ' class="alternate"';
						echo '		<tr'.$style.'>'."\n";
						echo '                  	<td>'.($start +$i).'.</td>'."\n";
						echo '                  	<td>'.$stat->IPAddress.'</td>'."\n";
						echo '                  	<td>'.$stat->uniq.'</td>'."\n";
						echo '                  	<td>'.$stat->total.'</td>'."\n";
						echo '             	</tr>'."\n";
					}
				} else {
					if (FALSE == empty($where)) {
						echo '<td colspan="4">'.__('No downloads yet. (Bots have been filtered.)','podpress')."</td>\n";
					} else {
						echo '<td colspan="4">'.__('No downloads yet.','podpress')."</td>\n";
					}
				}
				echo '				</tbody>';
				echo '				<tfood>'."\n";
				echo '				<tr>'."\n";
				echo '				<th colspan="4">'."\n";
					// Show paging
					echo $this->paging2($start, $limit, $rows_total, __('IP Address','podpress'));
				echo '				</th>'."\n";
				echo '              			</tr>'."\n";
				echo '				</tfood>'."\n";
				echo '			</table>'."\n";
				echo '		</fieldset>'."\n";
				echo '	</div>';
				break;
			case 'graphbydate':
				// ntm: stat logging: Full and Full+
				if ($this->checkGD() ) {//&& ($this->settings['statLogging'] == 'Full' || $this->settings['statLogging'] == 'FullPlus')
					$this->graphByDate();
				} else {
					$this->graphByDateAlt('With <a href="http://us2.php.net/manual/en/ref.image.php">gdlib-support</a> you\'ll have access to more detailed graphicals stats. Please ask your provider.');
				}
				break;
			case 'graphbydatealt':
				// ntm: stat logging: Full and Full+
				$this->graphByDateAlt();
				break;
			case 'graphbypost':
				// ntm: stat logging: Counts Only
				echo '<p>'.__('<strong>Notice:</strong> This graph is only faultless if your podcast posts contain only one media file per post, each file has a unique name and if you change the media file name on a re-post (deleting a post and publishing the content in a new post)!', 'podpress').'</p>';
				if ($this->checkGD()) {
					$this->graphByPost();
				} else {
					$this->graphByPostAlt('With <a href="http://us2.php.net/manual/en/ref.image.php">gdlib-support</a> you\'ll have access to more detailed graphicals stats. Please ask your provider.');
				}
				break;
			case 'graphbypostalt':
				// ntm: stat logging: Counts Only
				echo '<p>'.__('<strong>Notice:</strong> This graph is only faultless if your podcast posts contain only one media file per post, each file has a unique name and if you change the media file name on a re-post (deleting a post and publishing the content in a new post)!', 'podpress').'</p>';
				$this->graphByPostAlt();
				break;
			case 'downloads_per_post' :
				// ntm: stat logging: Full and Full+
				$where = $this->wherestr_to_exclude_bots('pod');
				
				// get the number of all post with podPress podcasts
				$query_string = "SELECT COUNT(DISTINCT postID) as posts FROM ".$wpdb->prefix."podpress_stats ".$where;
				// get all post with podPress podcasts
				$query_string="SELECT DISTINCT(pod.postID), p.post_title FROM ".$wpdb->prefix."podpress_stats AS pod LEFT JOIN ".$wpdb->prefix."posts AS p ON pod.postID = p.ID ".$where." ORDER BY pod.postID DESC";
				$posts_with_podpressmedia = $wpdb->get_results($query_string);
				
				echo '	<div class="wrap">'."\n";
				echo '		<fieldset class="options">'."\n";
				echo '			<legend>'.__('Downloads Per Post', 'podpress').'</legend>'."\n";
				echo '			<form method="post">'."\n";
				if ( function_exists('wp_nonce_field') ) { // since WP 2.0.4
					wp_nonce_field('podPress_downloads_per_post_nonce');
				}
				echo '				<label>'.__('Select a post with a media file (attached with podPress):', 'podpress').'</label><br />'."\n";
				echo '				<select id="post_with_podpressmedia" name="post_with_podpressmedia" size="5">'."\n";
				foreach ($posts_with_podpressmedia as $post) {
					if ($post->postID == $_POST['post_with_podpressmedia']) {
						$selected = ' selected="selected"';
					} else {
						$selected = '';
					}
					echo '				<option value="'.$post->postID.'"'.$selected.'>'.$post->post_title.'</option>'."\n";
					$post_titles[$post->postID] = $post->post_title;
				}
				echo '				</select>'."\n";
				echo '				<p class="submit"> '."\n";
				echo '					<input type="submit" name="Submit" value="'.__('Show the stats for this post', 'podpress').' &raquo;" /><br />'."\n";
				echo '				</p> '."\n";
				echo '				<input type="hidden" name="podPress_submitted" value="downloadsperpost" />'."\n";
				echo '			</form>'."\n";
				echo '		</fieldset>'."\n";
				echo '	</div><!-- .wrap -->'."\n";
				
				// show the statistics of the media files of the submitted post
				if ( 'downloadsperpost' == $_POST['podPress_submitted'] ) {
					if ( function_exists('check_admin_referer') ) {
						check_admin_referer('podPress_downloads_per_post_nonce');
					}

					if (FALSE == empty($where)) {
						$where_postID = "AND pod.postID = '".$_POST['post_with_podpressmedia']."' ";
					} else {
						$where_postID = "WHERE pod.postID = '".$_POST['post_with_podpressmedia']."' ";
					}
					$query_string="SELECT pod.media, pod.method, COUNT(*) as downloads FROM ".$wpdb->prefix."podpress_stats as pod ".$where.$where_postID."GROUP BY pod.method, pod.media ORDER BY media DESC";
					$stat_data_sets = $wpdb->get_results($query_string);
					
					// prepare the query result for the output:
					// - find the maximal downloads value for each download method
					// - if a media file was not downloaded  by one or more method then add this method  and a int(0) to the media file 		
					$feed_max = 0;
					$web_max = 0;
					$play_max = 0;
					$total_max = 0;
					foreach ($stat_data_sets as $stat_data_set) {
						$feed = 0;
						$web = 0;
						$play = 0;
						switch ($stat_data_set->method) {
							case 'feed' :
								$feed = intval($stat_data_set->downloads);
								$feed_max = max($feed_max, $feed);
							break;
							case 'web' :
								$web = intval($stat_data_set->downloads);
								$web_max = max($web_max, $web);
							break;
							case 'play' :
								$play = intval($stat_data_set->downloads);
								$play_max = max($play_max, $play);
							break;
						}
						$stats[$stat_data_set->media]['feed'] += $feed;
						$stats[$stat_data_set->media]['web'] += $web;
						$stats[$stat_data_set->media]['play'] += $play;
						$stats[$stat_data_set->media]['total'] += ($feed + $web + $play);
						$total_max = max($total_max, $stats[$stat_data_set->media]['total']);
					}
				
					// sort the media files by their 'total' value
					uasort($stats, array(self, 'sort_downloads_per_media_desc'));

					echo '	<div class="wrap">'."\n";
					echo '		<fieldset class="options">'."\n";
					echo '			<legend>'.__('Post:', 'podpress')." ".$post_titles[$_POST['post_with_podpressmedia']]." - ".__('Downloads Per Media File', 'podpress').'</legend>'."\n";
					echo '			<table class="the-list-x widefat">'."\n";
					echo '				<thead>';
					echo "				<tr>\n";
					echo '                  			<th rowspan="2">'.__('Nr.', 'podpress')."</th>\n";
					echo '                  			<th rowspan="2">'.__('Media File', 'podpress')."</th>\n";
					echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Feed', 'podpress')."</th>\n";
					echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Web', 'podpress')."</th>\n";
					echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Play', 'podpress')."</th>\n";
					echo '                  			<th class="podpress_stats_nr_head" rowspan="2">'.__('Total', 'podpress')."</th>\n";
					echo '              			</tr>'."\n";
					echo '				<tr>'."\n";
					echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
					echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
					echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
					echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
					echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
					echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
					echo '              			</tr>'."\n";
					echo '				</thead>';
					echo '				<tbody>';
					$mark_highest = FALSE;
					$nr_stat_data_sets = count($stat_data_sets);
					if (0 < $nr_stat_data_sets) {
						if ( 1 < $nr_stat_data_sets ) {
							$mark_highest = TRUE;
						}
						$i = 0;
						foreach ( $stats as $media => $downloads_per_method ) {
							$i++;
							$style = ($i % 2 != 0) ? '' : ' class="alternate"';
							if (TRUE === $mark_highest) {
								$highest_feed = ($downloads_per_method['feed'] == $feed_max AND 0 < $feed_max) ? ' podpress_stats_highest': '';
								$highest_web = ($downloads_per_method['web'] == $web_max AND 0 < $web_max) ? ' podpress_stats_highest': '';
								$highest_play = ($downloads_per_method['play'] == $play_max AND 0 < $play_max) ? ' podpress_stats_highest': '';
								$highest_total = ($downloads_per_method['total'] == $total_max AND 0 < $total_max) ? ' podpress_stats_highest': '';
							}
							$perc_feed = number_format(($downloads_per_method['feed'] * 100 / $downloads_per_method['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
							$perc_web = number_format(($downloads_per_method['web']  * 100 / $downloads_per_method['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
							$perc_play = number_format(($downloads_per_method['play'] * 100 / $downloads_per_method['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
							echo '		<tr'.$style.'>'."\n";
							echo '                  	<td>'.($start +$i).'.</td>'."\n";
							echo '                  	<td>'.podPress_strlimiter2(urldecode($media), 50, TRUE).'</td>'."\n";
							echo '                  	<td class="podpress_stats_numbers_abs'.$highest_feed.'">'.number_format($downloads_per_method['feed'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
							echo '                  	<td class="podpress_stats_numbers_percent'.$highest_feed.'">'.$perc_feed."</td>\n";
							echo '                  	<td class="podpress_stats_numbers_abs'.$highest_web.'" >'.number_format($downloads_per_method['web'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
							echo '                  	<td class="podpress_stats_numbers_percent'.$highest_web.'">'.$perc_web."</td>\n";
							echo '                  	<td class="podpress_stats_numbers_abs'.$highest_play.'">'.number_format($downloads_per_method['play'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
							echo '                  	<td class="podpress_stats_numbers_percent'.$highest_play.'">'.$perc_play."</td>\n";
							echo '                  	<td class="podpress_stats_numbers_total'.$highest_total.'">'.number_format($downloads_per_method['total'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
							echo '		</tr>'."\n";
						}
					} else {
						if (FALSE == empty($where)) {
							echo '<td colspan="9">'.__('No downloads yet. (Bots have been filtered.)','podpress')."</td>\n";
						} else {
							echo '<td colspan="9">'.__('No downloads yet.','podpress')."</td>\n";
						}
					}
					echo '				</tbody>';
					echo '				<tfood>'."\n";
					echo '				<tr>'."\n";
					echo '				<th colspan="9">'."\n";
					echo '				</th>'."\n";
					echo '              			</tr>'."\n";
					echo '				</tfood>'."\n";
					echo '			</table>'."\n";
					echo '		</fieldset>'."\n";
					echo '	</div><!-- .wrap -->'."\n";
				}
				break;
			case 'quickcounts':
				// ntm: stat logging: Counts Only
				// ntm: 'quickcounts' takes the data from the wp_podpress_statcounts table.
				$total= $wpdb->get_var('SELECT COUNT(postID) FROM '.$wpdb->prefix.'podpress_statcounts WHERE postID != 0;');
				if ($total > 0) {
					// Load highest values
					$sql = "SELECT 'blah' AS topic, MAX(total) AS total, MAX(feed) AS feed, MAX(web) AS web, MAX(play) AS play FROM ".$wpdb->prefix.'podpress_statcounts GROUP BY topic';
					$highest = $wpdb->get_results($sql);
					$highest = $highest[0];
					//~ $sql = 'SELECT sc.postID, sc.media, sc.total, sc.feed, sc.web, sc.play, p.post_title, p.post_date, UNIX_TIMESTAMP(p.post_date) AS pdate '
					//~ 	. 'FROM '.$wpdb->prefix.'podpress_statcounts AS sc, '.$wpdb->prefix.'posts AS p '
					//~ 	. 'WHERE (sc.postID = p.ID) ORDER BY p.post_date DESC LIMIT '.$start.', '.$limit.'';
					$sql = 'SELECT sc.media, sc.total, sc.feed, sc.web, sc.play '
						. 'FROM '.$wpdb->prefix.'podpress_statcounts AS sc '
						. 'ORDER BY sc.media DESC LIMIT '.$start.', '.$limit.'';
					$stats         = $wpdb->get_results($sql);
					$cnt_stats     = count($stats);
					
					if ( isset($_POST['podPress_submitted']) AND 'sortquickcountsbypost' == $_POST['podPress_submitted'] ) {
						foreach ($stats as $stat) {
							$where_instr_ar[] = 'INSTR(pm.meta_value, "'.$stat->media.'")';
						}
						if ( $cnt_stats > 1 ) {
							$where_instr = implode(' OR ', $where_instr_ar);
						} else {
							$where_instr = $where_instr_ar[0];
						} 
						$where = 'pm.meta_key="podPressMedia" AND ('.$where_instr.')';
						$sql = 'SELECT pm.post_id, pm.meta_key, pm.meta_value, p.post_title FROM '.$wpdb->prefix.'postmeta AS pm LEFT JOIN '.$wpdb->prefix.'posts AS p ON pm.post_id = p.ID WHERE '.$where.' ORDER BY pm.post_id DESC';
						$postmeta_data = $wpdb->get_results($sql);
						
						// get the highest post_id of posts which have a certain media file attached with podPress
						// in other words get the last post which has a certain media file attached 
						foreach ($postmeta_data as $postmeta) {
							$postmeta->post_id=intval($postmeta->post_id);
							foreach ($stats as $stat) {
								if (FALSE !== stristr($postmeta->meta_value, $stat->media)) {
									//echo $postmeta->post_id.': '.$stat->media."\n";
									$max_post_ids[$stat->media]['ID'] = max($max_post_ids[$stat->media]['ID'], $postmeta->post_id);
									if ($max_post_ids[$stat->media]['ID'] == $postmeta->post_id) {
										$max_post_ids[$stat->media]['title'] = $postmeta->post_title;
									}
								}
							}
						}
						foreach ($max_post_ids as $media => $last_published_in_post) {
							foreach ($stats as $stat) {
								if ($stat->media == $media) {
									$stat->postID = $last_published_in_post['ID'];
									$stat->post_title = $last_published_in_post['title'];
									$stats_new[] = $stat;
								}
							}
						}
						$stats=$stats_new;
						unset($stats_new);
						
						echo '	<div class="wrap">'."\n";
						echo '		<fieldset class="options">'."\n";
						echo '			<legend>'.__('Downloads Per Media File', 'podpress').'</legend>'."\n";
						echo '			<p>'.__('sorted by the IDs of the posts in which the media files were last published (descending)', 'podpress').'</p>';
						echo '			<table class="the-list-x widefat">'."\n";
						echo '				<thead>';
						echo "				<tr>\n";
						echo '                  			<th rowspan="2">'.__('Nr.', 'podpress')."</th>\n";
						echo '                  			<th rowspan="2">'.__('Media File', 'podpress')."</th>\n";
						echo '                  			<th rowspan="2">'.__('last published in post', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Feed', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Download', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Play', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head" rowspan="2">'.__('Total', 'podpress')."</th>\n";
						echo '              			</tr>'."\n";
						echo '				<tr>'."\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
						echo '              			</tr>'."\n";
						echo '				</thead>';
						echo '				<tbody>';

						$date_format = get_option('date_format');
						$time_format = get_option('time_format');
						
						if ($cnt_stats > 0) {
							$i = 0;
							foreach ($stats AS $stat) {
								$i++;
								$style = ($i % 2 != 0) ? '' : ' class="alternate"';
								$highest_feed = ($stat->feed == $highest->feed AND 0 < $highest->feed) ? ' podpress_stats_highest': '';
								$highest_web = ($stat->web == $highest->web AND 0 < $highest->web) ? ' podpress_stats_highest': '';
								$highest_play = ($stat->play == $highest->play AND 0 < $highest->play) ? ' podpress_stats_highest': '';
								$highest_total = ($stat->total == $highest->total) ? ' podpress_stats_highest': '';
								$perc_feed = number_format(($stat->feed * 100 / $stat->total), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
								$perc_web = number_format(($stat->web  * 100 / $stat->total), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
								$perc_play = number_format(($stat->play * 100 / $stat->total), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
								echo '		<tr'.$style.'>'."\n";
								echo '                  	<td>'.($start +$i).'.</td>'."\n";
								echo '                  	<td>'.podPress_strlimiter2(urldecode($stat->media), 30, TRUE).'</td>'."\n";
								if ( TRUE == version_compare($wp_version, '2.3', '<') ) {
									echo '                  	<td><a href="'.get_option('siteurl').'/wp-admin/post.php?action=edit&amp;post='.$stat->postID.'" title="'.__('Edit this post','podpress').'">'.podPress_strlimiter2($stat->post_title, 30, TRUE).'</a></td>'."\n";
								} else {
									echo '                  	<td><a href="'.get_edit_post_link($stat->postID).'" title="'.__('Edit this post','podpress').'">'.podPress_strlimiter2($stat->post_title, 30, TRUE).'</a></td>'."\n";
								}
								echo '                  	<td class="podpress_stats_numbers_abs'.$highest_feed.'">'.number_format($stat->feed, 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_percent'.$highest_feed.'">'.$perc_feed."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_abs'.$highest_web.'" >'.number_format($stat->web, 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_percent'.$highest_web.'">'.$perc_web."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_abs'.$highest_play.'">'.number_format($stat->play, 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_percent'.$highest_play.'">'.$perc_play."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_total'.$highest_total.'">'.number_format($stat->total, 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
								echo '              </tr>'."\n";
							}
						}
						echo '				</tbody>'."\n";
						echo '				<tfood>'."\n";
						echo '				<tr>'."\n";
						echo '				<th colspan="10">'."\n";
					} else {
						echo '	<div class="wrap">'."\n";
						echo '		<fieldset class="options">'."\n";
						//echo '			<legend>'.__('The counts', 'podpress').'</legend>'."\n";
						echo '			<legend>'.__('Downloads Per Media File', 'podpress').'</legend>'."\n";
						echo '			<p>'.__('sorted by the names of the media files (descending)', 'podpress').'</p>';
						echo '			<table class="the-list-x widefat">'."\n";
						echo '				<thead>';
						echo "				<tr>\n";
						echo '                  			<th rowspan="2">'.__('Nr.', 'podpress')."</th>\n";
						//~ echo '                  			<th rowspan="2">'.__('Post', 'podpress')."</th>\n";
						//~ echo '                  			<th rowspan="2">'.__('Publication Date', 'podpress')."</th>\n";
						echo '                  			<th rowspan="2">'.__('Media File', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Feed', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Download', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head" colspan="2">'.__('Play', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head" rowspan="2">'.__('Total', 'podpress')."</th>\n";
						echo '              			</tr>'."\n";
						echo '				<tr>'."\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('Files', 'podpress')."</th>\n";
						echo '                  			<th class="podpress_stats_nr_head">'.__('%', 'podpress')."</th>\n";
						echo '              			</tr>'."\n";
						echo '				</thead>';
						echo '				<tbody>';

						$date_format = get_option('date_format');
						$time_format = get_option('time_format');
						
						if ($cnt_stats > 0) {
							$i = 0;
							foreach ($stats AS $stat) {
								$i++;
								$style = ($i % 2 != 0) ? '' : ' class="alternate"';
								$highest_feed = ($stat->feed == $highest->feed AND 0 < $highest->feed) ? ' podpress_stats_highest': '';
								$highest_web = ($stat->web == $highest->web AND 0 < $highest->web) ? ' podpress_stats_highest': '';
								$highest_play = ($stat->play == $highest->play AND 0 < $highest->play) ? ' podpress_stats_highest': '';
								$highest_total = ($stat->total == $highest->total) ? ' podpress_stats_highest': '';
								$perc_feed = number_format(($stat->feed * 100 / $stat->total), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
								$perc_web = number_format(($stat->web  * 100 / $stat->total), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
								$perc_play = number_format(($stat->play * 100 / $stat->total), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
								echo '		<tr'.$style.'>'."\n";
								echo '                  	<td>'.($start +$i).'.</td>'."\n";
								//~ if ( TRUE == version_compare($wp_version, '2.3', '<') ) {
									//~ echo '                  	<td><a href="'.get_option('siteurl').'/wp-admin/post.php?action=edit&amp;post='.$stat->postID.'" title="'.__('Edit this post','podpress').'">'.podPress_strlimiter2($stat->post_title, 30, TRUE).'</a></td>'."\n";
								//~ } else {
									//~ echo '                  	<td><a href="'.get_edit_post_link($stat->postID).'" title="'.__('Edit this post','podpress').'">'.podPress_strlimiter2($stat->post_title, 30, TRUE).'</a></td>'."\n";
								//~ }
								//~ echo '                  	<td>'.date($date_format.' - '.$time_format, $stat->pdate).'</td>'."\n";
								echo '                  	<td>'.podPress_strlimiter2(urldecode($stat->media), 30, TRUE).'</td>'."\n";
								echo '                  	<td class="podpress_stats_numbers_abs'.$highest_feed.'">'.number_format($stat->feed, 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_percent'.$highest_feed.'">'.$perc_feed."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_abs'.$highest_web.'" >'.number_format($stat->web, 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_percent'.$highest_web.'">'.$perc_web."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_abs'.$highest_play.'">'.number_format($stat->play, 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_percent'.$highest_play.'">'.$perc_play."</td>\n";
								echo '                  	<td class="podpress_stats_numbers_total'.$highest_total.'">'.number_format($stat->total, 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])."</td>\n";
								echo '              </tr>'."\n";
							}
						}
						echo '				</tbody>'."\n";
						echo '				<tfood>'."\n";
						echo '				<tr>'."\n";
						//~ echo '				<th colspan="11">'."\n";
						echo '				<th colspan="9">'."\n";
					}
					
					// Show paging
					if ($_GET['display'] != 'graphbydate' && $_GET['display'] != 'graphbypost') {
						echo $this->paging2($start, $limit, $total, __('Nr.', 'podpress'));
					}
					
					echo '				</th>'."\n";
					echo '              			</tr>'."\n";
					echo '				</tfood>'."\n";
					echo '			</table>'."\n";
					
					echo '			<form method="post">'."\n";
					echo '			<p class="submit"> '."\n";
					
					if ( isset($_POST['podPress_submitted']) AND 'sortquickcountsbypost' == $_POST['podPress_submitted'] ) {
						echo '				<input type="submit" name="Submit" value="'.__('sort the media files by their names', 'podpress').' &raquo;" /><br />'."\n";
					} else {
						echo '				<input type="submit" name="Submit" value="'.__('sort the media files by the IDs of the posts in which they were last published', 'podpress').' &raquo;" /><br />'."\n";
						echo '				<input type="hidden" name="podPress_submitted" value="sortquickcountsbypost" />'."\n";
					}
					echo '			</p> '."\n";
					echo '			</form>'."\n";
					
					echo '		</fieldset>'."\n";
					echo '	</div>';
				} else {
					echo '<p>'.__('We\'re sorry. At the moment we don\'t have enough data collected to display the graph.', 'podpress')."</p>\n";
				}
			break;
			default:
			return;
		}
		echo '<div style="clear: both;"></div>'."\n";
		echo '</div>';
	}
	
	#############################################
	#############################################

	/**
	* sort_downloads_per_media_asc - is a callback function to sort arrays by their 'total' value ascending
	* for more info: http://www.php.net/manual/en/function.uasort.php
	*
	* @package podPress
	* @since 8.8.5 beta 3
	*
	* @param int $a
	* @param int $b
	*
	* @return int 0 => equal, -1 => $a < $b, 1 => $a > $b
	*/
	function sort_downloads_per_media_asc($a, $b) {
		if ($a['total'] == $b['total']) {
			return 0;
		}
		return ($a['total'] < $b['total']) ? -1 : 1;
	}
	
	#############################################
	#############################################

	/**
	* sort_downloads_per_media_desc - is a callback function to sort arrays by their 'total' value descending
	* for more info: http://www.php.net/manual/en/function.uasort.php
	*
	* @package podPress
	* @since 8.8.5 beta 3
	*
	* @param int $a
	* @param int $b
	*
	* @return int 0 => equal, -1 => $a < $b, 1 => $a > $b
	*/
	function sort_downloads_per_media_desc($a, $b) {
		if ($a['total'] == $b['total']) {
			return 0;
		}
		return ($a['total'] > $b['total']) ? -1 : 1;
	}
	
	#############################################
	#############################################

	/**
	 * Check GD-Library-Support
	 *
	 * @return unknown
	 */
	function checkGD() {
	    if (!function_exists('gd_info')) {
	        /* php > 4.3 */
    		$info = gd_info();
    		if (isset($info) AND ($info['JPG Support'] == 1) AND ($info['FreeType Support'] == 1)) {
    			return true;
    		}
	    } else {
	        /* php < 4.3 */
            ob_start();
            phpinfo(8);
            $info   = ob_get_contents();
            ob_end_clean();
            $info   = stristr($info, 'gd version');
            preg_match('/\d/', $info, $match);
            $gd_ver = $match[0];
            if ($gd_ver > 0) {
                return true;
            }
	   }
		return false;
	}

	#############################################
	#############################################

	function checkFontFile() {
		clearstatcache();

		/* check font */
		$file     = 'share-regular.ttf';
		$fontface = ABSPATH.PLUGINDIR.'/podpress/optional_files/'.$file;
		if (!file_exists($fontface)) {
			$fontface = '../optional_files/'.$file;
		}
		if (!is_readable($fontface)) {
			echo '<p>'.__('Could not include font-file. Please reinstall podPress.', 'podpress')."</p>\n";
			return false;
		}
		$this->fontface = $fontface;

		return true;
	}

	#############################################
	#############################################

	function graphByDate() {
		// ntm: stat logging: Full and Full+
		if ($this->checkWritableTempFileDir() && $this->checkFontFile()) {
			$chronometry1 = getmicrotime();
			$start        = (isset($_GET['start'])) ? $this->podSafeDigit($_GET['start']): 0;
			$image_width  = 1800;
			$image_height = 470;
			$col_width    = 20;
			$filename     = 'graph-by-date.jpg';
			$file         = $this->tempFileSystemPath.'/'.$filename;
			$sidemargin   = 20;
			$baseline     = ($image_height - 90);
			$topmargin    = 60;
			$maxheight    = ($baseline - $topmargin);
			$weeks        = 15;
			$timelimit    = (time() - (($weeks * 7) * 86400));
			$min_downloaddays = 4;
			
			ini_set('memory_limit', '120M');
			ini_set('max_execution_time', '60');
			$start_memory = memory_get_usage();
			$where = $this->wherestr_to_exclude_bots('', 'AND');
			/* get the data in time range ($timelimit) */
			$query        = 'SELECT dt AS timestamp, DAYOFWEEK(FROM_UNIXTIME(dt)) AS weekday, method, '
                          . 'DATE_FORMAT(FROM_UNIXTIME(dt), "%Y-%m-%d") AS day '
                          . 'FROM '.$this->wpdb->prefix."podpress_stats WHERE dt >= '".$timelimit."' ".$where
                          . 'ORDER BY dt ASC;';
			$data         = $this->wpdb->get_results($query);

			/* create array with grouped stats */
			$stats        = array();
			$stats_total  = array();
			foreach ($data AS $idata) {
				$stats[$idata->day][$idata->method]++;
				$stats[$idata->day]['total']++;
				$stats_total[$idata->method]++;
				$stats_total['total']++;
			}

			/* get min an max values */
			$value_min = 0;
			$value_max = 0;
			$i         = 0;
			$cnt_days  = count($stats);
			foreach ($stats AS $day => $idata) {
				/* set min and max values */
				if ($value_min == 0) {
					$value_min = $idata['total'];
				} elseif ($idata['total'] < $value_min) {
					$value_min = $idata['total'];
				} elseif ($idata['total'] > $value_max) {
					$value_max = $idata['total'];
				}
				/* set first and last day */
				if ($i == 0) {
					$first_day = strtotime($day);
				} elseif ($i == ($cnt_days - 1)) {
					$last_day  = strtotime($day);
				}
				$i++;
			}

			/* Do we have enough data? */
			if (($value_max > 0) && ($cnt_days >= $min_downloaddays)) {
				$h_cscale = ($maxheight / $value_max);
				$h_vscale = ($maxheight / $value_min / 4);
				$pos_x    = ($sidemargin + 35);
				$points   = array( 'total' => array(0 => $pos_x, 1 => $baseline) );
				foreach ($stats AS $day => $idata) {
					$points['total'][] = $pos_x;
					$points['total'][] = ($baseline - ($idata['total'] * $h_cscale));
					$points['feed'][]  = ($baseline - ($idata['feed'] * $h_cscale));
					$points['web'][]   = ($baseline - ($idata['web'] * $h_cscale));
					$points['play'][]  = ($baseline - ($idata['play'] * $h_cscale));
					$pos_x             = ($pos_x + $col_width);
				}
				$points['total'][] = ($pos_x - $col_width);
				$points['total'][] = $baseline;


				/* create image */
				$chronometry2 = getmicrotime();
				$image        = imagecreatetruecolor($image_width, $image_height);
				if (function_exists('imageantialias')) {
					imageantialias($image, 1);
				}

				/* set colors */
				$colors = array(
						'background' => imagecolorallocate($image, 51, 51, 51),
						'line'       => imagecolorallocate($image, 79, 79, 79),
						'total'      => imagecolorallocate($image, 79, 79, 79),
						'feed'       => imagecolorallocate($image, 255, 0, 0),
						'web'        => imagecolorallocate($image, 12, 223, 0),
						'play'       => imagecolorallocate($image, 223, 204, 0),
						'text'       => imagecolorallocate($image, 255, 255, 255),
						'copytext'   => imagecolorallocate($image, 79, 79, 79),
						'days_first' => imagecolorallocate($image, 143, 143, 143),
						'days_other' => imagecolorallocate($image, 95, 95, 95),
						/* gray-scales: 175 159 143 127 111 95 79 63 */
					       );
				imagefill($image, 0, 0, $colors['background']);

				/* create the legend */
				imagettftext($image, 14, 0, $sidemargin, 25, $colors['text'], $this->fontface, get_option('blogname'));
				imagettftext($image, 8, 0, $sidemargin, 42, $colors['text'], $this->fontface, sprintf(__('last %1$s weeks', 'podpress'), $weeks).' &#187; '.strftime('%d.%m.%Y', $first_day).' - '.strftime('%d.%m.%Y', $last_day));

				/* Total stats */
				$text_total = number_format($stats_total['total'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
				$text_feed  = number_format($stats_total['feed'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).' ('.number_format(($stats_total['feed'] * 100 / $stats_total['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).'%)';
				$text_web   = number_format($stats_total['web'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).' ('.number_format(($stats_total['web'] * 100 / $stats_total['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).'%)';
				$text_play  = number_format($stats_total['play'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).' ('.number_format(($stats_total['play'] * 100 / $stats_total['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).'%)';
				$text_total = html_entity_decode(__('Total stats in time range', 'podpress')).':  '.$text_total.'  /  '.__('Feed', 'podpress').': '.$text_feed.'  /  '.__('Download', 'podpress').': '.$text_web.'  /  '.__('Play', 'podpress').': '.$text_play.' ';
				imagettftext($image, 8, 0, $sidemargin, ($image_height - 15), $colors['text'], $this->fontface, $text_total);

				$pos_y = ($image_height - 32);

				imagefilledrectangle($image, ($sidemargin + 0), ($pos_y - 10), ($sidemargin + 10), ($pos_y - 0), $colors['total']);
				imagettftext($image, 8, 0, ($sidemargin + 15), $pos_y, $colors['text'], $this->fontface, __('Total', 'podpress'));

				imageline($image, ($sidemargin + 50), ($pos_y - 4), ($sidemargin + 60), ($pos_y - 4), $colors['feed']);
				imagettftext($image, 8, 0, ($sidemargin + 65), $pos_y, $colors['text'], $this->fontface, __('Feed', 'podpress'));

				imageline($image, ($sidemargin + 100), ($pos_y - 4), ($sidemargin + 110), ($pos_y - 4), $colors['web']);
				imagettftext($image, 8, 0, ($sidemargin + 115), $pos_y, $colors['text'], $this->fontface, __('Download', 'podpress'));

				imageline($image, ($sidemargin + 175), ($pos_y - 4), ($sidemargin + 185), ($pos_y - 4), $colors['play']);
				imagettftext($image, 8, 0, ($sidemargin + 190), $pos_y, $colors['text'], $this->fontface, __('Play', 'podpress'));

				imagettftext($image, 23, 0, ($image_width - 128), 30, $colors['copytext'], $this->fontface, 'podPress');
				imagettftext($image, 8, 0, ($image_width - 115), 43, $colors['copytext'], $this->fontface, __('Plugin for WordPress', 'podpress'));

				imagettftext($image, 6, 90, ($image_width - 15), ($image_height - 10), $colors['copytext'], $this->fontface, strftime($this->local_settings['creation_date'], time()).' ('.PODPRESS_VERSION.')');

				/* draw total-stats */
				$cnt_total = (count($points['total']) / 2);
				imagefilledpolygon($image, $points['total'], $cnt_total, $colors['total']);

				/* draw background-lines and scale-text */
				$step = 0;
				$h    = (($baseline - $topmargin) / 10);
				while ($step <=  10) {
					$pos_y = ($topmargin + ($h * $step));
					imageline($image, 0, ($topmargin + ($h * $step)), $image_width, ($topmargin + ($h * $step)), $colors['line']);
					imagettftext($image, 8, 0, ($sidemargin), ($pos_y + 13), $colors['line'], $this->fontface, number_format((($baseline - $pos_y) / $h_cscale), 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]));
					$step++;
				}

				$pos_x    = ($sidemargin + 35);
				foreach ($stats AS $day => $idata) {
					/* Web einzeichnen */
					$weekday          = date('w', strtotime($day));
					$col_total_height = ($idata['total'] * $h_cscale);

					if ($weekday == 1) {
						$pos_y_start = ($baseline + 27);
						imagettftext($image, 8, 0, ($pos_x + 3), ($baseline + 13), $colors['text'], $this->fontface, strftime($this->local_settings['short_date'], strtotime($day)));
						$color       = $colors['days_first'];
					} else {
						$pos_y_start = $baseline;
						$color       = $colors['days_other'];
					}
					imageline($image, $pos_x, $pos_y_start, $pos_x, ($baseline - $col_total_height), $color);

					$pos_x = ($pos_x + $col_width);
				}


				/* Linien zeichen */
				$topics = array('feed', 'web', 'play');
				foreach ($topics AS $topic) {
					$cnt_points = count($points[$topic]);
					$pos_x      = ($sidemargin + 35);
					for ($i = 0; $i < ($cnt_points - 1); $i++) {
						imageline($image, $pos_x, $points[$topic][$i], ($pos_x + $col_width), $points[$topic][$i + 1], $colors[$topic]);
						$pos_x = ($pos_x + $col_width);
					}
				}

				imagejpeg($image, $file, 100);
				$chronometry_end = getmicrotime();
				$chronometry1    = ($chronometry_end - $chronometry1);
				$chronometry2    = ($chronometry_end - $chronometry2);
				imagedestroy($image);

						$end_memory = memory_get_usage();
						$memory_used = podPress_bytes($end_memory-$start_memory);
				/* Output */
				echo '<div id="podPress_graph" style="width: '.$image_width.'px;">'."\n";
				echo '    <p><img src="'.$this->tempFileURLPath.'/'.$filename.'" width="'.$image_width.'" height="'.$image_height.'" alt="podPress-Statistics" /></p>'."\n";
				echo "</div>\n";
				echo '<p>'.__('Time to generate the graph', 'podpress').': '.number_format($chronometry1, 3, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).' '.__('seconds', 'podpress').' ('.__('image', 'podpress').': '.number_format($chronometry2, 3, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).' '.__('seconds', 'podpress').").\n";
				echo '<br/>'.__('Memory to generate the graph', 'podpress').': '.$memory_used.".</p>\n";
			} else {
				echo '<p>'.sprintf(__('We\'re sorry. At the moment we don\'t have enough data collected to display the graph. (Downloads on %1$s different days are necessary)', 'podpress'), $min_downloaddays)."</p>\n";
			}
		}
	}

	#############################################
	#############################################

	function graphByDateAlt($msg = '') {
		// ntm: stat logging: Full and Full+
		GLOBAL $wpdb;
		$where = $this->wherestr_to_exclude_bots();
		$sql      = 'SELECT DATE_FORMAT(FROM_UNIXTIME(dt), "%a, %Y-%m-%d") AS title, DAYOFWEEK(FROM_UNIXTIME(dt)) AS weekday, COUNT(id) AS value '
		          . 'FROM '.$wpdb->prefix.'podpress_stats '.$where.'GROUP BY title ORDER BY dt DESC LIMIT 0, 100;';
 		$data     = $wpdb->get_results($sql);
 		$data     = array_reverse($data);
 		$splitter = array('weekday', '2');

 		echo '	<div class="wrap">'."\n";
 		echo '		<fieldset class="options">'."\n";
 		echo '			<legend>'.__('Downloads by date', 'podpress').' ('.__('Last 100 days', 'podpress').')</legend>'."\n";
 		echo $this->podGraph($data, $splitter);
		if($msg != '') {
			echo '      <p>'.$msg."</p>\n";
		}
		echo '		</fieldset>'."\n";
		echo '	</div>';
	}

	#############################################
	#############################################

	function graphByPost() {
		// ntm: stat logging: Counts Only
		if ($this->checkWritableTempFileDir() && $this->checkFontFile()) {
			$chronometry1 = getmicrotime();
			$start        = (isset($_GET['start'])) ? $this->podSafeDigit($_GET['start']): 0;
			$limit        = 20;
			$image_width  = 1200;
			$image_height = 470;
			$col_width    = 25;
			$col_space    = 15;
			$filename     = 'graph-by-post.jpg';
			$file         = $this->tempFileSystemPath.'/'.$filename;
			$sidemargin   = 20;
			$baseline     = ($image_height - 90);
			$topmargin    = 60;
			$maxheight    = ($baseline - $topmargin);
			$timelimit    = (time() - ((5 * 7) * 86400));

			ini_set('memory_limit', '120M');
			ini_set('max_execution_time', '60');
			$start_memory = memory_get_usage();
			/* get data */
			$total        = $this->wpdb->get_var('SELECT COUNT(postID) FROM '.$this->wpdb->prefix."podpress_statcounts WHERE postID != 0;");
			$query        = 'SELECT post_title AS title, total, feed, web, play, UNIX_TIMESTAMP(post_date) AS post_date '
			              . 'FROM '.$this->wpdb->prefix.'podpress_statcounts, '.$this->wpdb->prefix.'posts '
			              . 'WHERE '.$this->wpdb->prefix.'posts.ID = '.$this->wpdb->prefix.'podpress_statcounts.postID '
			              . 'AND postID !=0 GROUP BY postID ORDER BY post_date DESC LIMIT '.$start.', '.$limit.';';
			$data         = $this->wpdb->get_results($query);
			
			$cnt_data     = count($data);

			$first_post   = $data[($cnt_data - 1)]->post_date;
			$last_post    = $data[0]->post_date;

			$stats        = array();
			foreach ($data AS $idata) {
				$stats[$idata->day][$idata->method]++;
				$stats[$idata->day]['total']++;
				$stats_total[$idata->method]++;
				$stats_total['total']++;
			}

			/* get min an max values */
			$value_min   = 0;
			$value_max   = 0;
			$stats_total = array();
			foreach ($data AS $idata) {
				if ($value_min == 0) {
					$value_min = $idata->total;
				} elseif ($idata->total < $value_min) {
					$value_min = $idata->total;
				}
				if ($idata->total > $value_max) {
					$value_max = $idata->total;
				}
				$stats_total['feed']  = ($stats_total['feed'] + $idata->feed);
				$stats_total['web']   = ($stats_total['web'] + $idata->web);
				$stats_total['play']  = ($stats_total['play'] + $idata->play);
				$stats_total['total'] = ($stats_total['total'] + $idata->total);
			}

			/* Do we have enough data? */
			if (intval($value_max) > 0) {
				$h_cscale    = ($maxheight / $value_max);
				$h_vscale    = ($maxheight / $value_min / 4);
				$w_scale     = intval($image_width - (2 * $sidemargin) + intval(($image_width - (2 * $sidemargin)) / ($limit) / 4)) / ($limit);

				/* create image */
				$chronometry2 = getmicrotime();
				$image        = imagecreatetruecolor($image_width, $image_height);
				if (function_exists('imageantialias')) {
					imageantialias($image, 1);
				}

				$colors = array(
						'background' => imagecolorallocate($image, 51, 51, 51),
						'line'       => imagecolorallocate($image, 79, 79, 79),
						'text'       => imagecolorallocate($image, 255, 255, 255),
						'copytext'   => imagecolorallocate($image, 79, 79, 79),
						'total'      => imagecolorallocate($image, 0, 0, 0),
						'feed'       => imagecolorallocate($image, 143, 53, 53),
						'web'        => imagecolorallocate($image, 71, 143, 88),
						'play'       => imagecolorallocate($image, 142, 143, 71),
					      );

				imagefill($image, 0, 0, $colors['background']);

				/* draw background-lines and scale-text */
				$step = 0;
				$h    = (($baseline - $topmargin) / 10);
				while ($step <=  10) {
					$pos_y = ($topmargin + ($h * $step));
					imageline($image, 0, ($topmargin + ($h * $step)), $image_width, ($topmargin + ($h * $step)), $colors['line']);
					imagettftext($image, 8, 0, ($sidemargin), ($pos_y + 13), $colors['line'], $this->fontface, number_format((($baseline - $pos_y) / $h_cscale), 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]));
					$step++;
				}

				/* create the legend */
				imagettftext($image, 14, 0, $sidemargin, 25, $colors['text'], $this->fontface, get_option('blogname'));
				imagettftext($image, 8, 0, $sidemargin, 42, $colors['text'], $this->fontface, __('Posts', 'podpress').' &#187; '.strftime('%d.%m.%Y', $first_post).' - '.strftime('%d.%m.%Y', $last_post));

				$text_total = number_format($stats_total['total'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
				$text_feed  = number_format($stats_total['feed'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).' ('.number_format(($stats_total['feed'] * 100 / $stats_total['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).'%)';
				$text_web   = number_format($stats_total['web'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).' ('.number_format(($stats_total['web'] * 100 / $stats_total['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).'%)';
				$text_play  = number_format($stats_total['play'], 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).' ('.number_format(($stats_total['play'] * 100 / $stats_total['total']), 1, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).'%)';
				$text_total = __('Total stats from displayed posts', 'podpress').':  '.$text_total.'  /  '.__('Feed', 'podpress').': '.$text_feed.'  /  '.__('Download', 'podpress').': '.$text_web.'  /  '.__('Play', 'podpress').': '.$text_play.' ';
				imagettftext($image, 8, 0, $sidemargin, ($image_height - 15), $colors['text'], $this->fontface, $text_total);

				$pos_y = ($image_height - 32);

				imagefilledrectangle($image, ($sidemargin + 0), ($pos_y - 10), ($sidemargin + 10), $pos_y, $colors['feed']);
				imagettftext($image, 8, 0, ($sidemargin + 15), $pos_y, $colors['text'], $this->fontface, __('Feed', 'podpress'));

				imagefilledrectangle($image, ($sidemargin + 50), ($pos_y - 10), ($sidemargin + 60), $pos_y, $colors['web']);
				imagettftext($image, 8, 0, ($sidemargin + 65), $pos_y, $colors['text'], $this->fontface, __('Download', 'podpress'));

				imagefilledrectangle($image, ($sidemargin + 125), ($pos_y - 10), ($sidemargin + 135), $pos_y, $colors['play']);
				imagettftext($image, 8, 0, ($sidemargin + 140), $pos_y, $colors['text'], $this->fontface, __('Play', 'podpress'));

				imagettftext($image, 23, 0, ($image_width - 128), 30, $colors['copytext'], $this->fontface, 'podPress');
				imagettftext($image, 8, 0, ($image_width - 115), 43, $colors['copytext'], $this->fontface, __('Plugin for WordPress', 'podpress'));

				imagettftext($image, 6, 90, ($image_width - 15), ($image_height - 10), $colors['copytext'], $this->fontface, strftime($this->local_settings['creation_date'], time()).' ('.PODPRESS_VERSION.')');

				$pos_x = ($image_width - $sidemargin - 15);

				/* draw the posts */
				foreach ($data AS $idata) {
					/* Total stats */
					$col_total_height = ($idata->total * $h_cscale);

					imageline($image, ($pos_x - 2), $baseline, ($pos_x - 2), ($baseline - $col_total_height), $colors['total']);
					imagettftext($image, 8, 0, ($pos_x - $col_width - 3), ($baseline - 3 - ($idata->total * $h_cscale)), $colors['text'], $this->fontface, $idata->total);

					/* Feeds */
					$perc_feed       = number_format(($idata->feed * 100 / $idata->total), 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
					$col_feed_height = ($idata->feed * $h_cscale);
					if ($col_feed_height < 0) {
						imagefilledrectangle($image, ($pos_x - $col_width - 3), $baseline, ($pos_x - 3), ($baseline - $col_feed_height), $colors['feed']);
					} else {
						imagefilledrectangle($image, ($pos_x - $col_width - 3), ($baseline - $col_feed_height), ($pos_x - 3), $baseline, $colors['feed']);
					}
					if ($col_feed_height > 11) {
						imagettftext($image, 8, 0, ($pos_x - $col_width - 2), (($baseline - ($idata->feed * $h_cscale)) + 11), $colors['text'], $this->fontface, $perc_feed.'%');
					}

					/* Web */
					$perc_web        = number_format(($idata->web * 100 / $idata->total), 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
					$col_web_height = ($idata->web * $h_cscale);
					if ($col_web_height < 0) {
						imagefilledrectangle($image, ($pos_x - $col_width - 3), ($baseline - $col_feed_height), ($pos_x - 3), ($baseline - $col_web_height - $col_feed_height), $colors['web']);
					} else {
						imagefilledrectangle($image, ($pos_x - $col_width - 3), ($baseline - $col_web_height - $col_feed_height), ($pos_x - 3), ($baseline - $col_feed_height), $colors['web']);
					}
					if ($col_web_height > 11) {
						imagettftext($image, 8, 0, ($pos_x - $col_width - 2), (($baseline - $col_web_height - $col_feed_height) + 11), $colors['text'], $this->fontface, $perc_web.'%');
					}

					/* Play */
					$perc_play       = number_format(($idata->play * 100 / $idata->total), 0, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]);
					$col_play_height = ($idata->play * $h_cscale);
					if ($col_play_height < 0) {
						imagefilledrectangle($image, ($pos_x - $col_width - 3), ($baseline - $col_feed_height - $col_web_height), ($pos_x - 3), ($baseline - $col_play_height - $col_web_height - $col_feed_height), $colors['play']);
					} else {
						imagefilledrectangle($image, ($pos_x - $col_width - 3), ($baseline - $col_play_height - $col_web_height - $col_feed_height), ($pos_x - 3), ($baseline - $col_feed_height - $col_web_height), $colors['play']);
					}
					if ($col_play_height > 11) {
						imagettftext($image, 8, 0, ($pos_x - $col_width - 2), (($baseline - $col_play_height - $col_web_height - $col_feed_height) + 11), $colors['text'], $this->fontface, $perc_play.'%');
					}

					/* Set Date and Title */
					$title = (strlen($idata->title) > 70) ? substr($idata->title, 0, 70).'...' : $idata->title;
					imagettftext($image, 8, 90, ($pos_x + 10), $baseline, $colors['text'], $this->fontface, $title);
					imagettftext($image, 8, 0, ($pos_x - $col_width - 3), ($baseline + 14), $colors['text'], $this->fontface, strftime($this->local_settings['short_date'], $idata->post_date));

					$pos_x = ($pos_x - $col_width - $col_space - 15);
				}

				imagejpeg($image, $file, 100);
				$chronometry_end = getmicrotime();
				$chronometry1    = ($chronometry_end - $chronometry1);
				$chronometry2    = ($chronometry_end - $chronometry2);
				imagedestroy($image);

				$end_memory = memory_get_usage();
				$memory_used = podPress_bytes($end_memory-$start_memory);
				echo '<div id="podPress_graph" style="width: '.$image_width.'px;">'."\n";
				echo '    <p style="padding-top: 0;"><img src="'.$this->tempFileURLPath.'/'.$filename.'" width="'.$image_width.'" height="'.$image_height.'" alt="podPress-Statistics" /></p>'."\n";
				echo $this->paging($start, $limit, $total, 'Posts');
				echo '    <div class="clear"></div>'."\n";
				echo "</div>\n";
				echo '<p>'.__('Time to generate the graph', 'podpress').': '.number_format($chronometry1, 3, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1]).' seconds (image: '.number_format($chronometry2, 3, $this->local_settings['numbers'][0], $this->local_settings['numbers'][1])." seconds).\n";
				echo '<br/>'.__('Memory to generate the graph', 'podpress').': '.$memory_used.".</p>\n";
			} else {
				echo '<p>'.__('We\'re sorry. At the moment we don\'t have enough data collected to display the graph.', 'podpress')."</p>\n";
			}
		}
	}

	#############################################
	#############################################

	function graphByPostAlt($msg = '') {
		// ntm: stat logging: Counts Only
		global $wpdb;
		
		$sql  = 'SELECT post_title AS title, SUM(total) AS value '
			. 'FROM '.$wpdb->prefix.'podpress_statcounts, '.$wpdb->prefix.'posts '
			. 'WHERE '.$wpdb->prefix.'posts.ID = '.$wpdb->prefix.'podpress_statcounts.postID '
			. 'AND postID !=0 GROUP BY postID ORDER BY post_date DESC LIMIT 0, 100;';
		
		$data = $wpdb->get_results($sql);
		$data = array_reverse($data);
		echo '	<div class="wrap">'."\n";
 		echo '		<fieldset class="options">'."\n";
 		echo '			<legend>'.__('Downloads by Post', 'podpress').' ('.__('Last 100 posts', 'podpress').')</legend>'."\n";
 		echo $this->podGraph($data);
 		echo '		</fieldset>'."\n";

 		if ($msg != '') {
			echo '      <p>'.$msg."</p>\n";
		}
		echo '	</div>';
	}

	#############################################
	#############################################

}
?>