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
	class podPressUpgrade_class extends podPress_class	
	{
		function podPressUpgrade_class($current = '0') {
			GLOBAL $wpdb, $wp_version, $blog_id;
			
			// $current is the version from the DB if this function was called from podpress.php
			$result = @preg_match('/^(\d+\.)?(\d+\.)?(\d+\.)?(\*|\d+)(\s((alpha)|(beta)|(RC)|(final)))?(\s\d+)?$/i', $current, $b); // is it a version string like 8.8.6.3 beta 2 (max. for numeric values separated by dots, eventually followed by a whitespace and "alpha", "beta" or "RC", eventually followed by a whitespace and a further numeric value)
			if ( empty($b) ) {
				$current = '0';
			}
		
			require_once(ABSPATH.PLUGINDIR.'/podpress/podpress_admin_functions.php');
			
			if ( TRUE === version_compare(PODPRESS_VERSION, $current, '<=') ) {
				// if the version in the DB is the same as or newer than the version of this plugin then an upgrade is not nescessary
				$this->podPress_class();
				return;
			}
			if ( function_exists('wp_cache_flush') ) {
				wp_cache_flush();
			}
			
			if ( '0' == $current ) { // if no version number was in the db or if something is wrong with it.
				$this->activate();
			}
			
			// upgrade from a podPress version which is older than 8.8 to podPress v8.8
			if ( TRUE === version_compare('8.8', $current, '>=') ) {
				$this->do_legacy_upgrades($current);
			}
			
			// upgrade from a version newer than 8.8 to the current one
			// from 8.8, 8.8.1, 8.8.2, 8.8.3, 8.8.4, 8.8.5.x, 8.8.6.x to 8.8.8:
			$create_table = "ALTER TABLE ".$wpdb->prefix."podpress_stats ADD COLUMN completed TINYINT(1) UNSIGNED DEFAULT 0";
			podPress_maybe_add_column($wpdb->prefix.'podpress_stats', 'completed', $create_table);
			
			// rename the post specific settings and the media meta keys. 
			$wpdb->query( "UPDATE ".$wpdb->prefix."postmeta SET meta_key = '_podPressPostSpecific' WHERE meta_key = 'podPressPostSpecific'" );
			$wpdb->query( "UPDATE ".$wpdb->prefix."postmeta SET meta_key = '_podPressMedia' WHERE meta_key = 'podPressMedia'" );
			
			if ( TRUE === version_compare('8.8.8', $current, '>') ) {
				// remove the portectedMediaFile setting because it was and it is not in use and the option has been removed from the podPress general options in 8.8.8 (again) 
				$settings = podPress_get_option('podPress_config');
				if ( FALSE !== $settings ) {
					unset($settings['protectedMediaFilePath']);
					podPress_update_option('podPress_config', $settings);
				}
			}
			if ( TRUE === version_compare('8.8.10.8', $current, '>') ) {
				$settings = podPress_get_option('podPress_config');
				if ( FALSE !== $settings ) {
				
					unset( $settings['protectFeed'] ); // is obsolete podPress will produce specification complaint RSS/ATOM feed without the option "Aggressively Protect the news feeds"
					
					unset( $settings['rss_showlinks'] );	// remove the rss_showlinks setting because this option is hidden since 8.8.5 and will be removed in 8.8.10.8 (the purpose of it was to show download links for the media files of a post in the feed element with encoded content -> <description> .
					
					if ( (FALSE === isset($settings['blognameChoice'])) OR (isset($settings['blognameChoice']) AND $settings['blognameChoice'] == 'Global') ) {
						$settings['blognameChoice'] = 'Append'; // Append is (like) the default setting for category feeds in WP (Site Title >> Category Name). The setting 'Global' stay for 'Use the  Site Title'
					}
					
					podPress_update_option('podPress_config', $settings);
				}
			}
			if ( TRUE === version_compare('8.8.10.14', $current, '>') ) {
				// During the 8.8.10 and 8.8.10.12 podPress collected the numbers with under two different file name versions and not under one name. 
				// The file names results from the HTML5 players had a different encoding. The file have been encoed with rawurlencode() while all the file names of all other downloads have not been encoded with this function.
				// For example: example(nr1).mp3 would have been saved as media = example(nr1).mp3 and media = example%28nr1%29.mp3
				$result = $wpdb->get_results("SELECT COUNT(*) as rows FROM ".$wpdb->prefix."podpress_statcounts", ARRAY_A);
				if ( FALSE === empty($result[0]) AND TRUE === isset($result[0]['rows']) AND 0 < intval($result[0]['rows']) ) {
					// stats table exists and has entries
					if ( function_exists('get_admin_url') ) {
						$adminurl = get_admin_url(); // since WP 3.0
					} elseif ( function_exists('admin_url') ) {
						$adminurl = admin_url(); // since WP 2.6
					} else {
						$adminurl = site_url() . '/wp-admin';
					}
					$ppgeneralsettingsurl = trailingslashit($adminurl).'admin.php?page=podpress/podpress_general.php';
					podpress_add_upgrade_status('podpress_update_statcounts_table', sprintf(__('<strong>Notice:</strong> The podPress upgrade is not complete. Please, <a href="%1$s">go to the General Settings page of podPress</a> to finish this process manually.', 'podPress'), $ppgeneralsettingsurl), '', Array('key'=>'enableStats', 'value' => TRUE));
				}
				$result = $wpdb->get_results("SELECT COUNT(*) as rows FROM ".$wpdb->prefix."podpress_stats", ARRAY_A);
				if ( FALSE === empty($result[0]) AND TRUE === isset($result[0]['rows']) AND 0 < intval($result[0]['rows']) ) {
					// stats table exists and has entries
					if ( function_exists('get_admin_url') ) {
						$adminurl = get_admin_url(); // since WP 3.0
					} elseif ( function_exists('admin_url') ) {
						$adminurl = admin_url(); // since WP 2.6
					} else {
						$adminurl = site_url() . '/wp-admin';
					}
					$ppgeneralsettingsurl = trailingslashit($adminurl).'admin.php?page=podpress/podpress_general.php';
					podpress_add_upgrade_status('podpress_update_stats_table', sprintf(__('<strong>Notice:</strong> The podPress upgrade is not complete. Please, <a href="%1$s">go to the General Settings page of podPress</a> to finish this process manually.', 'podPress'), $ppgeneralsettingsurl), '', Array('key'=>'enableStats', 'value' => TRUE));
				}
			}

			// update the version number in the db
			$current = constant('PODPRESS_VERSION');
			update_option('podPress_version', $current);

			$this->podPress_class();
			$this->checkSettings();
		}
		
		function do_legacy_upgrades($current) {
			GLOBAL $wpdb;
			while ( TRUE == version_compare(PODPRESS_VERSION, $current, '>=') AND TRUE == version_compare('8.8', $current, '>=') ) {
				settype($current, 'string');
				switch($current) {
					case '0':
						$current = PODPRESS_VERSION;
						break;
					case '1.4':
						$posts_that_need_upgrades = array();
						$posts = $wpdb->get_results("SELECT ID, post_content FROM ".$wpdb->posts);
						if($posts) {
							foreach ($posts as $post) {
								if(preg_match($this->podcasttag_regexp, $post->post_content, $matches)) {
									$podcastTagFileName = $matches[1];
								} else {
									$podcastTagFileName = false;
								}
								if($podcastTagFileName){
									$posts_that_need_upgrades[$post->ID] = $post->post_content;
								}
							}
							reset($posts_that_need_upgrades);
							foreach($posts_that_need_upgrades as $key => $value){
								$wpdb->query("UPDATE ".$wpdb->posts." SET post_content = '".preg_replace($this->podcasttag_regexp, '', $value)."' WHERE ID=".$key);
								if(preg_match($this->podcasttag_regexp, $content, $matches)) {
									$podcastTagFileName = $matches[1];
								} else {
									$podcastTagFileName = false;
								}
								delete_post_meta($key, 'podPress_podcastStandardAudio');
								add_post_meta($key, 'podPress_podcastStandardAudio', $podcastTagFileName, true);
							}
						}
						break;
					case '3.8':
						$this->settings['audioWebPath'] = get_option('podPress_audioWebPath');			
						$this->settings['audioFilePath'] = get_option('podPress_audioFilePath');
						if($this->settings['audioWebPath'] == '') {
							add_option('podPress_mediaWebPath', $this->settings['audioWebPath'], "Web path to Podcast media files", true);
							$this->settings['audioWebPath'] = $this->settings['audioWebPath'];
							delete_option('podPress_audioWebPath'); 
						}

						if($this->settings['audioFilePath'] == '') {
							add_option('podPress_mediaFilePath', $this->settings['audioFilePath'], "File path to Podcast media files", true);
							$this->settings['mediaFilePath'] = $this->settings['audioFilePath'];
							delete_option('podPress_audioFilePath'); 
						}
						
						$posts = $wpdb->get_results("SELECT ID FROM ".$wpdb->prefix."posts");
						if($posts) {
							foreach ($posts as $post) {
								$sql = "SELECT meta_key, meta_value
								        FROM ".$wpdb->prefix."postmeta 
									WHERE meta_key  IN(
										'podPress_podcastStandardAudio', 
										'podPress_podcastStandardAudioSize',
										'podPress_podcastStandardAudioDuration',
										'podPress_podcastEnhancedAudio',
										'podPress_podcastEnhancedAudioSize',
										'podPress_podcastEnhancedAudioDuration',
										'podPress_podcastVideo',
										'podPress_podcastVideoSize',
										'podPress_podcastVideoDuration',
										'podPress_podcastVideoDimension',
										'podPress_webVideo',
										'podPress_webVideoSize',
										'podPress_webVideoDuration',
										'podPress_webVideoDimension',
										'podPress_podcastEbook',
										'podPress_podcastEbookSize',
										'itunes:duration',
										'enclosure'
									) AND post_id = ".$post->ID;
								$metadata = $wpdb->get_results($sql);
								if($metadata) {
									$posts2convert = array();
									foreach ($metadata as $stat) {
										$posts2convert[$post->ID][$stat->meta_key] = $stat->meta_value;
									}

									$rssaddedyet = false;
									$podPressMedia = array();
									foreach ($posts2convert as $key=>$val) {
										if(isset($val['enclosure'])) {
											$encParts = split( "\n", $val['enclosure']);
											$data = $this->upgrade_convert39to40mediaFile (trim(htmlspecialchars($encParts[0])), trim(htmlspecialchars($encParts[1])));
											if(!$rssaddedyet) {
												$rssaddedyet = true;
												$data['rss'] = 'on';
											}
											$podPressMedia[] = $data;
										}

										if(isset($val['podPress_podcastStandardAudio'])) {
											$data = $this->upgrade_convert39to40mediaFile ($val['podPress_podcastStandardAudio'], $val['podPress_podcastStandardAudioSize'], $val['podPress_podcastStandardAudioDuration']);
											if(!$rssaddedyet) {
												$rssaddedyet = true;
												$data['rss'] = 'on';
											}
											$podPressMedia[] = $data;
										}

										if(isset($val['podPress_podcastEnhancedAudio'])) {
											$data = $this->upgrade_convert39to40mediaFile ($val['podPress_podcastEnhancedAudio'], $val['podPress_podcastEnhancedAudioSize'], $val['podPress_podcastEnhancedAudioDuration']);
											if(!$rssaddedyet) {
												$rssaddedyet = true;
												$data['rss'] = 'on';
											}
											$podPressMedia[] = $data;
										}
							
										if(isset($val['podPress_podcastVideo'])) {
											$data = $this->upgrade_convert39to40mediaFile ($val['podPress_podcastVideo'], $val['podPress_podcastVideoSize'], $val['podPress_podcastVideoDuration'], $val['podPress_podcastVideoDimension']);
											if(!$rssaddedyet) {
												$rssaddedyet = true;
												$data['rss'] = 'on';
											}
											$podPressMedia[] = $data;
										}
							
										if(isset($val['podPress_webVideo'])) {
											$data = $this->upgrade_convert39to40mediaFile ($val['podPress_webVideo'], $val['podPress_webVideoSize'], $val['podPress_webVideoDuration'], $val['podPress_webVideoDimension']);
											$podPressMedia[] = $data;
										}
							
										if(isset($val['podPress_podcastEbook'])) {
											$data = $this->upgrade_convert39to40mediaFile ($val['podPress_podcastEbook'], $val['podPress_podcastEbookSize']);
											if(!$rssaddedyet) {
												$rssaddedyet = true;
												$data['rss'] = 'on';
											}
											$podPressMedia[] = $data;
										}
									}
									if(is_array($podPressMedia)) {
										delete_post_meta($post->ID, 'podPressMedia');
										podPress_add_post_meta($post->ID, 'podPressMedia', $podPressMedia, true) ;
									}
								}	
							}
						}
						$sql = "UPDATE ".$wpdb->prefix."postmeta SET meta_key='enclosure_hold' WHERE meta_key='enclosure'";
						$wpdb->query($sql);
						break;
					case '4.2':
						$playerOptions['bg'] = str_replace('0x', '#', get_option('podPress_player_bgcolor'));
						delete_option('podPress_player_bgcolor');
						$playerOptions['text'] = str_replace('0x', '#', get_option('podPress_player_textcolor'));
						delete_option('podPress_player_textcolor');
						$playerOptions['leftbg'] = str_replace('0x', '#', get_option('podPress_player_leftbgcolor'));
						delete_option('podPress_player_leftbgcolor');
						$playerOptions['lefticon'] = str_replace('0x', '#', get_option('podPress_player_lefticoncolor'));
						delete_option('podPress_player_lefticoncolor');
						$playerOptions['rightbg'] = str_replace('0x', '#', get_option('podPress_player_rightbgcolor'));
						delete_option('podPress_player_rightbgcolor');
						$playerOptions['rightbghover'] = str_replace('0x', '#', get_option('podPress_player_rightbghovercolor'));
						delete_option('podPress_player_rightbghovercolor');
						$playerOptions['righticon'] = str_replace('0x', '#', get_option('podPress_player_righticoncolor'));
						delete_option('podPress_player_righticoncolor');
						$playerOptions['righticonhover'] = str_replace('0x', '#', get_option('podPress_player_righticonhovercolor'));
						delete_option('podPress_player_righticonhovercolor');
						$playerOptions['slider'] = str_replace('0x', '#', get_option('podPress_player_slidercolor'));
						delete_option('podPress_player_slidercolor');
						$playerOptions['track'] = str_replace('0x', '#', get_option('podPress_player_trackcolor'));
						delete_option('podPress_player_trackcolor');
						$playerOptions['loader'] = str_replace('0x', '#', get_option('podPress_player_loadercolor'));
						delete_option('podPress_player_loadercolor');
						$playerOptions['border'] = str_replace('0x', '#', get_option('podPress_player_bordercolor'));
						delete_option('podPress_player_bordercolor');
						
						podPress_update_option('podPress_playerOptions', $playerOptions);
						break;
					case '4.4':
						if(!is_array($this->settings['iTunes'])) {
							$this->settings['iTunes'] = array();
						}
						
						$x = stripslashes(get_option('itunesAdminName'));
						delete_option('itunesAdminName');						
						if(!empty($x)) {
							$this->settings['iTunes']['author'] = $x;
						}

						$this->settings['iTunes']['block'] = 'No';

						$this->settings['iTunes']['category'] = array();
						$cat1 = get_option('itunesCategory1');
						$cat2 = get_option('itunesCategory2');
						$cat3 = get_option('itunesCategory3');
						if(!empty($cat1)) {
							$this->settings['iTunes']['category'][] = $cat1;
						}
						if(!empty($cat2)) {
							$this->settings['iTunes']['category'][] = $cat2;
						}
						if(!empty($cat3)) {
							$this->settings['iTunes']['category'][] = $cat3;
						}
						
						$x = stripslashes(get_option('itunesImageBig'));
						delete_option('itunesImageBig');						
						if(!empty($x)) {
							$this->settings['iTunes']['image'] = $x;
						}
						
						$x = stripslashes(get_option('itunesDefaultExplicit'));
						delete_option('itunesDefaultExplicit');						
						if(!empty($x)) {
							$this->settings['iTunes']['explicit'] = $x;
						}

						$x = stripslashes(get_option('itunesFeedKeywords'));
						delete_option('itunesFeedKeywords');						
						if(!empty($x)) {
							$this->settings['iTunes']['keywords'] = $x;
						}

						$x = stripslashes(get_option('podcastdescription'));
						delete_option('podcastdescription');						
						if(!empty($x)) {
							$this->settings['iTunes']['summary'] = $x;
						}

						$x = stripslashes(get_option('itunesFeedID'));
						delete_option('itunesFeedID');						
						if(!empty($x)) {
							$this->settings['iTunes']['FeedID'] = $x;
						}
						
						delete_option('itunesUseKeyword'); 
						delete_option('itunesFeedURL'); 

						$x = stripslashes(get_option('itunesImageSmall'));
						delete_option('itunesImageSmall');						
						if(!empty($x)) {
							add_option('rss_image', $x);
						}
						
						$x = stripslashes(get_option('itunesTTL'));
						delete_option('itunesTTL');						
						if(!empty($x)) {
							add_option('rss_ttl', $x);
						}

						$x = stripslashes(get_option('downloadLinksInRSS'));
						delete_option('downloadLinksInRSS');						
						if(!empty($x)) {
							add_option('podPress_rss_showlinks', $x);
						}
						
						$x = stripslashes(get_option('channelCat'));
						delete_option('channelCat');						
						if(!empty($x)) {
							add_option('podPress_rss_category', $x);
						}
						
						delete_option('podPress_iTunes');
						podPress_add_option('podPress_iTunes', $this->settings['iTunes']);

						$x = podPress_get_option('podPress_playerOptions');
						delete_option('podPress_playerOptions');						
						if(!empty($x)) {
							podPress_add_option('podPress_playerSettings', $x);
						}
											
						$x = stripslashes(get_option('podPress_BeforeMore'));
						delete_option('podPress_BeforeMore');						
						if(!empty($x)) {
							add_option('podPress_contentBeforeMore', $x);
						}

						$sql = "SELECT media, 
						               method, 
						               COUNT(media) as cnt_total, 
						               COUNT(CASE WHEN method = 'feed' THEN 1 END) as cnt_feed ,
						               COUNT(CASE WHEN method = 'web' THEN 1 END) as cnt_web ,
						               COUNT(CASE WHEN method = 'play' THEN 1 END) as cnt_play 
						        FROM ".$wpdb->prefix."podpress_stats 
						        GROUP BY media 
						        ORDER BY cnt_total DESC";
						$stats = $wpdb->get_results($sql);
						if($stats) {
							$i = 0;
							foreach ($stats as $stat) {
								++$i;
								$sqlI = "INSERT INTO ".$wpdb->prefix."podpress_statcounts (media, total, feed, web, play) VALUES ('".$stat->media."', ".$stat->cnt_total.", ".$stat->cnt_feed.", ".$stat->cnt_web.", ".$stat->cnt_play.")";
								$wpdb->query($sqlI);
							}
						}
						
						break;
					case '4.5':
						$x = stripslashes(get_option('channelCat'));
						delete_option('channelCat');						
						if(!empty($x)) {
							add_option('podPress_rss_category', $x);
						}
						break;
					case '5.3':
						$posts = $wpdb->get_results("SELECT ID FROM ".$wpdb->prefix."posts");
						if($posts) {
							foreach ($posts as $post) {
								$x['itunes:subtitle'] = get_post_meta($post->ID, 'itunes:subtitle', true);
								delete_post_meta($post->ID, 'itunes:subtitle');
								if(!isset($x['itunes:subtitle'])) {
									unset($x['itunes:subtitle']);
								}

								$x['itunes:summary'] = get_post_meta($post->ID, 'itunes:summary', true);
								delete_post_meta($post->ID, 'itunes:summary');
								if(!isset($x['itunes:summary'])) {
									unset($x['itunes:summary']);
								}
								$x['itunes:keywords'] = get_post_meta($post->ID, 'itunes:keywords', true);
								delete_post_meta($post->ID, 'itunes:keywords');
								if(!isset($x['itunes:keywords'])) {
									unset($x['itunes:keywords']);
								}
								$x['itunes:author'] = get_post_meta($post->ID, 'itunes:author', true);
								delete_post_meta($post->ID, 'itunes:author');
								if(!isset($x['itunes:author'])) {
									unset($x['itunes:author']);
								}
								$x['itunes:explicit'] = get_post_meta($post->ID, 'itunes:explicit', true);
								delete_post_meta($post->ID, 'itunes:explicit');
								if(!isset($x['itunes:explicit'])) {
									unset($x['itunes:explicit']);
								}
								$x['itunes:block'] = get_post_meta($post->ID, 'itunes:block', true);
								delete_post_meta($post->ID, 'itunes:block');
								if(!isset($x['itunes:block'])) {
									unset($x['itunes:block']);
								}
								if(!empty($x)) {
									podPress_add_post_meta($post->ID, 'podPressPostSpecific', $x, true) ;
								}
							}
						}

						break;
					case '5.6':
						$x = podPress_get_option('podPress_config');
						$y = true;
						if($y) {
							$newSettings = array();
							$newSettings['enableStats'] = get_option('podPress_enableStats');
						 	$newSettings['statMethod'] = get_option('podPress_statMethod');
						 	$newSettings['statLogging'] = get_option('podPress_statLogging');
						 	$newSettings['enablePodTracStats'] = get_option('podPress_enablePodTracStats');
						 	$newSettings['enablePremiumContent'] = get_option('podPress_enablePremiumContent');
						 	$newSettings['enableTorrentCasting'] = get_option('podPress_enableTorrentCasting');
							$newSettings['mediaWebPath'] = get_option('podPress_mediaWebPath');			
							$newSettings['mediaFilePath'] = get_option('podPress_mediaFilePath');
							$newSettings['contentBeforeMore'] = get_option('podPress_contentBeforeMore');
							$newSettings['contentLocation'] = get_option('podPress_contentLocation');
							$newSettings['contentImage'] = get_option('podPress_contentImage');		
							$newSettings['contentPlayer'] = get_option('podPress_contentPlayer');
							$newSettings['contentDownload'] = get_option('podPress_contentDownload');
							$newSettings['contentDownloadText'] = get_option('podPress_contentDownloadText');
							$newSettings['contentDownloadStats'] = get_option('podPress_contentDownloadStats');
							$newSettings['contentDuration'] = get_option('podPress_contentDuration');
							$newSettings['rss_showlinks'] = get_option('podPress_rss_showlinks');			
							$newSettings['rss_category']  = get_option('podPress_rss_category'); 
							$newSettings['enableFooter'] = get_option('podPress_enableFooter');

							$newSettings['player'] = podPress_get_option('podPress_playerSettings');
							$newSettings['iTunes'] = podPress_get_option('podPress_iTunes');

							delete_option('podPress_config');
							add_option('podPress_config', $newSettings, "podPress Configuration", true);
						}
						break;
					case '5.7':
							// in the next release I will clean up old crap data that is still laying around
							delete_option('podPress_enableStats');
							delete_option('podPress_statMethod');
							delete_option('podPress_statLogging');
							delete_option('podPress_enablePodTracStats');
							delete_option('podPress_enablePremiumContent');
							delete_option('podPress_enableTorrentCasting');
							delete_option('podPress_mediaWebPath');
							delete_option('podPress_mediaFilePath');
							delete_option('podPress_contentBeforeMore');
							delete_option('podPress_contentLocation');
							delete_option('podPress_contentImage');
							delete_option('podPress_contentPlayer');
							delete_option('podPress_contentDownload');
							delete_option('podPress_contentDownloadText');
							delete_option('podPress_contentDownloadStats');
							delete_option('podPress_contentDuration');
							delete_option('podPress_rss_showlinks');
							delete_option('podPress_rss_category');
							delete_option('podPress_enableFooter');
							delete_option('podPress_iTunes'); 
							delete_option('podPress_playerSettings'); 
							delete_option('podPress_enableLogging'); 
							delete_option('podPress_downloadLinksInRSS'); 
							delete_option('podPress_categoryCasting'); 
							delete_option('podPress_beforeMore'); 
							$sql = "DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_key='podPressiTunesPostSpecific'";
							$wpdb->query($sql);
						break;
					case '6.3':
						$x = podPress_get_option('podPress_config');
						$x['iTunes']['category'][0] = podPress_upgradeCategory($x['iTunes']['category'][0]);
						if(isset($x['iTunes']['category'][1])) {
							$x['iTunes']['category'][1] = podPress_upgradeCategory($x['iTunes']['category'][1]);
						}
						if(isset($x['iTunes']['category'][2])) {
							$x['iTunes']['category'][2] = podPress_upgradeCategory($x['iTunes']['category'][2]);
						}
						podPress_update_option('podPress_config', $x);
						break;
					case '6.7':
						$create_table = "ALTER TABLE ".$wpdb->prefix."podpress_statcounts ADD COLUMN postID  int(11) NOT NULL DEFAULT 0 FIRST;";
						podPress_maybe_add_column($wpdb->prefix.'podpress_statcounts', 'postID', $create_table);

						$sql = 'ALTER TABLE '.$wpdb->prefix.'podpress_statcounts ADD PRIMARY KEY (media(255),postID), DROP PRIMARY KEY;';
						$wpdb->get_results($sql);

						$create_table = "ALTER TABLE ".$wpdb->prefix."podpress_stats ADD COLUMN postID  int(11) NOT NULL DEFAULT 0 AFTER id;";
						podPress_maybe_add_column($wpdb->prefix.'podpress_stats', 'postID', $create_table);

						$mappings = array();

						$sql = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'podPressMedia' ORDER BY meta_id;";
						$posts = $wpdb->get_results($sql);
						if ($posts) {
							foreach ($posts as $post) {
								$mediaFiles = unserialize($post->meta_value);
								if(is_array($mediaFiles)) {
									foreach ($mediaFiles as $mediaFile) {
										if(!isset($mappings[$mediaFile['URI']])){
											$filename = podPress_getFileName($mediaFile['URI']);
											$mappings[$filename] = $post->post_id;
										}
									}
								}
							}
							reset($mappings);
							foreach($mappings as $key => $val) {
								$wpdb->query('UPDATE '.$wpdb->prefix."podpress_statcounts SET postID = '".$val."' WHERE media='".$key."'");
								$wpdb->query('UPDATE '.$wpdb->prefix."podpress_stats SET postID = '".$val."' WHERE media='".$key."'");
							}
						}
					case '7.9':
						$create_table = "ALTER TABLE ".$wpdb->prefix."podpress_stats ADD COLUMN completed TINYINT(1) UNSIGNED DEFAULT 0;";
						podPress_maybe_add_column($wpdb->prefix.'podpress_stats', 'completed', $create_table);
						break;
					case '8.3':
						if($this->settings['enablePodTracStats']) {
							$this->settings['enable3rdPartyStats'] = 'PodTrac';
						}
						break;
					default:
						// do nothing
					break;
				}
				if(function_exists('wp_cache_flush')) {
					wp_cache_flush();
				}
				if ( version_compare(PODPRESS_VERSION, $current, '>=') ) {
					update_option('podPress_version', $current);
				}
				$current = $current+0.1;
			}
		}


		function upgrade_convert39to40mediaFile ($url, $size = '', $duration = '', $dimension = '') {
			$result = array();
			$result['URI'] = $url;
			$result['size'] = $size;
			$result['duration'] = $duration;
			$result['atom'] = 'on';
			$filext = podPress_getFileExt($url);
			switch ($filext) {
				case 'mp3':
					$result['type'] = 'audio_mp3';
					break;
				case 'ogg':
					$result['type'] = 'audio_ogg';
					break;
				case 'm4a':
					$result['type'] = 'audio_m4a';
					break;
				case 'mp4':
					$result['type'] = 'video_mp4';
					break;
				case 'm4v':
					$result['type'] = 'video_m4v';
					break;
				case 'mov':
					$result['type'] = 'video_mov';
					break;
				case 'qt':
					$result['type'] = 'video_qt';
					break;
				case 'avi':
					$result['type'] = 'video_avi';
					break;
				case 'mpg':
				case 'mpeg':
					$result['type'] = 'video_mpg';
					break;
				case 'asf':
					$result['type'] = 'video_asf';
					break;
				case 'wma':
					$result['type'] = 'audio_wma';
					break;
				case 'wmv':
					$result['type'] = 'video_wmv';
					break;
				case 'flv':
					$result['type'] = 'video_flv';
					break;
				case 'swf':
					$result['type'] = 'video_swf';
					break;
				case 'pdf':
					$result['type'] = 'ebook_pdf';
					break;
				case 'epub':
					$result['type'] = 'ebook_epub';
					break;
				default:
					$result['type'] = 'misc_other';
			}
			if(strpos ($dimension, ':')) {
				$dimensionParts = explode(':', $dimension);
				$result['dimensionW'] = $dimensionParts[0];
				$result['dimensionH'] = $dimensionParts[1];
			}
			return $result;
		}
	}
?>