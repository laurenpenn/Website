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
	/*************************************************************/
	/* feed generation functions                                 		 */
	/*************************************************************/
	
	function podPress_feedSafeContent($input, $aggressive = FALSE, $removescripts = FALSE) {
		GLOBAL $podPress;
		
		// All values should be plain text (no markup or HTML). [...] CDATA sections are strongly discouraged. (see http://www.apple.com/itunes/podcasts/specs.html#encoding)
		if (TRUE === $removescripts) { // this option is only reachable via php source code and via the WP backend
			$input = preg_replace('/<script[\w\W]*<\/script>/i', '', $input);
		}
		$input = strip_tags($input);

		// replace the relevant characters with their HTML entities
		if ( TRUE === $aggressive ) {
			if (TRUE === version_compare(PHP_VERSION, '5.2.3', '>=')) {
				$result = htmlentities($input, ENT_NOQUOTES, get_bloginfo('charset'), FALSE);
			} else {
				$result = htmlentities($input, ENT_NOQUOTES, get_bloginfo('charset'));
			}
		} else {
			if (TRUE === version_compare(PHP_VERSION, '5.2.3', '>=')) {
				$result = htmlspecialchars($input, ENT_NOQUOTES, get_bloginfo('charset'), FALSE);
			} else {
				$result = htmlspecialchars($input, ENT_NOQUOTES, get_bloginfo('charset'));
			}
		}		
		$input = $result;
		$result = str_replace('&nbsp;', ' ', $input);
		
		// transform all HTML entities in to their numeric equivalents
		$result = ent2ncr($result);
		
		return $result;
	}

	function podPress_rss2_ns() {
		echo 'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"'."\n";
		echo "\t".'xmlns:media="http://search.yahoo.com/mrss/"'."\n";
		//echo '"\t".xmlns:dtvmedia="http://participatoryculture.org/RSSModules/dtv/1.0"'."\n";
	}

	function podPress_rss2_head() {
		GLOBAL $podPress, $posts, $post_meta_cache, $blog_id;
		if($podPress->settings['enablePremiumContent']) {
			podPress_reloadCurrentUser();
		}
		
		// more info about $post_meta_cache see http://ifacethoughts.net/2006/06/14/post_meta_cache/
		if(is_array($post_meta_cache[$blog_id])) {
			foreach($post_meta_cache[$blog_id] as $key=>$val) {
				if(isset($post_meta_cache[$blog_id][$key]['enclosure']) && isset($post_meta_cache[$blog_id][$key]['_podPressMedia'])) {
					$post_meta_cache[$blog_id][$key]['enclosure_podPressHold'] = $post_meta_cache[$blog_id][$post->ID]['enclosure'];
					unset($post_meta_cache[$blog_id][$key]['enclosure']);
				}
			}
		}
		
		$data = $podPress->settings['iTunes'];
		$data['podcastFeedURL'] = $podPress->settings['podcastFeedURL'];
		if (0 >= strlen(trim($data['author']))) {
			if (isset($podPress->settings['iTunesAuthor']) AND 0 < strlen($podPress->settings['iTunesAuthor'])) {
				$data['author'] = stripslashes($podPress->settings['iTunesAuthor']);
			} else {
				$data['author'] = get_bloginfo('name');
			}
		}
		$data['subtitle'] = stripslashes($data['subtitle']);
		$data['summary'] = stripslashes($data['summary']);
		$data['keywords'] = stripslashes($data['keywords']);
		$data['rss_copyright'] = stripslashes($podPress->settings['rss_copyright']);
		$data['rss_license_url'] = $podPress->settings['rss_license_url'];
		$data['rss_image'] = get_option('rss_image');
		$data['rss_category'] = stripslashes($podPress->settings['rss_category']);
		$data['admin_email'] = stripslashes(get_option('admin_email'));
		$data['rss_ttl'] = get_option('rss_ttl');

		if ( (isset($podPress->settings['category_data']['categoryCasting']) AND 'true' == $podPress->settings['category_data']['categoryCasting']) ) {
			$data['podcastFeedURL'] = $podPress->settings['category_data']['podcastFeedURL'];
			if($podPress->settings['category_data']['iTunesNewFeedURL'] != '##Global##') {
				$data['new-feed-url'] = $podPress->settings['category_data']['iTunesNewFeedURL'];
			}
			if($podPress->settings['category_data']['iTunesSummaryChoice'] == 'Custom') {
				$data['summary'] = stripslashes($podPress->settings['category_data']['iTunesSummary']);
			}
			if($podPress->settings['category_data']['iTunesSubtitleChoice'] == 'Custom') {
				$data['subtitle'] = stripslashes($podPress->settings['category_data']['iTunesSubtitle']);
			}
			if($podPress->settings['category_data']['iTunesKeywordsChoice'] == 'Custom') {
				$data['keywords'] = stripslashes($podPress->settings['category_data']['iTunesKeywords']);
			}
			if($podPress->settings['category_data']['iTunesAuthorChoice'] == 'Custom' && !empty($podPress->settings['category_data']['iTunesAuthor'])) {
				$data['author'] = stripslashes($podPress->settings['category_data']['iTunesAuthor']);
			}
			if($podPress->settings['category_data']['iTunesAuthorEmailChoice'] == 'Custom') {
				$data['admin_email'] = $podPress->settings['category_data']['iTunesAuthorEmail'];
			}
			if($podPress->settings['category_data']['iTunesBlock'] != '##Global##' && !empty($podPress->settings['category_data']['iTunesBlock'])) {
				$data['block'] = $podPress->settings['category_data']['iTunesBlock'];
			}
			if($podPress->settings['category_data']['iTunesExplicit'] != '##Global##' && !empty($podPress->settings['category_data']['iTunesExplicit'])) {
				$data['explicit'] = $podPress->settings['category_data']['iTunesExplicit'];
			}
			if($podPress->settings['category_data']['iTunesImageChoice'] == 'Custom') {
				$data['image'] = $podPress->settings['category_data']['iTunesImage'];
			}
			if($podPress->settings['category_data']['rss_imageChoice'] == 'Custom') {
				$data['rss_image'] = $podPress->settings['category_data']['rss_image'];
			}
			if($podPress->settings['category_data']['rss_copyrightChoice'] == 'Custom') {
				$data['rss_copyright'] = stripslashes($podPress->settings['category_data']['rss_copyright']);
			}
			if($podPress->settings['category_data']['rss_license_urlChoice'] == 'Custom') {
				$data['rss_license_url'] = $podPress->settings['category_data']['rss_license_url'];
			}
		}

		$feedslug = get_query_var('feed');
		$is_podpress_feed = FALSE;
		foreach ($podPress->settings['podpress_feeds'] as $feed) {
			if ( $feedslug === $feed['slug'] ) {
				$is_podpress_feed = TRUE;
				break;
			}
		}
		if ( TRUE === $is_podpress_feed ) {
			$data['podcastFeedURL'] = get_feed_link($feed['slug']);
			$data['new-feed-url'] = $feed['itunes-newfeedurl'];
			if ( 'Enable' === $data['new-feed-url'] ) {
				$data['podcastFeedURL'] = $feed['newfeedurl'];
			}
			$data['subtitle'] = stripslashes($feed['subtitle']);
			$data['summary'] = stripslashes($feed['descr']);
			$data['keywords'] = stripslashes($feed['itunes-keywords']);
			$data['author'] = stripslashes($feed['itunes-author']);
			$data['admin_email'] = stripslashes($feed['email']);
			$data['block'] = $feed['itunes-block'];
			$data['explicit'] = $feed['itunes-explicit'];
			$data['image'] = $feed['itunes-image'];
			$data['rss_image'] = $feed['rss_image'];
			$data['rss_copyright'] = stripslashes($feed['copyright']);
			$data['rss_license_url'] = $feed['license_url'];
			$data['rss_category'] = stripslashes($feed['rss_category']);
			$data['rss_ttl'] = $feed['ttl'];
		}
		
		if (isset($podPress->settings['enableVersionInFeeds']) AND TRUE === $podPress->settings['enableVersionInFeeds'] ) {
			if (isset($podPress->settings['disableVersionNumber']) AND TRUE === $podPress->settings['disableVersionNumber'] ) {
				echo '	<!-- podcast_generator="podPress" -->'."\n";
			} else {
				echo '	<!-- podcast_generator="podPress/'.PODPRESS_VERSION.'" -->'."\n";
			}
		}
		
		if (empty($data['rss_copyright'])) {
			echo '	<copyright>'.podPress_feedSafeContent(__('Copyright', 'podpress').' &#xA9; '. date('Y',time())).' '.get_bloginfo('blogname').' '.$data['rss_license_url'].'</copyright>'."\n";
		} else {
			echo '	<copyright>'.podPress_feedSafeContent($data['rss_copyright']).' '.$data['rss_license_url'].'</copyright>'."\n";
		}
		
		if (FALSE !== $data['admin_email'] AND FALSE === empty($data['admin_email'])) {
			if ('' != trim($data['author'])) {
				$admin_name = ' ('.podPress_feedSafeContent($data['author']).')';
			} else {
				$admin_name = '';
			}
			echo '	<managingEditor>'.podPress_feedSafeContent($data['admin_email']).$admin_name.'</managingEditor>'."\n";
			echo '	<webMaster>'.podPress_feedSafeContent($data['admin_email']).$admin_name.'</webMaster>'."\n";
		}
		
		if (FALSE === empty($data['rss_category'])) {
			echo '	<category>'.podPress_feedSafeContent($data['rss_category']).'</category>'."\n";
		}		

		if(!empty($data['rss_ttl']) && $data['rss_ttl'] < 1440) {
			$data['rss_ttl'] = 1440;
		}
		if(!empty($data['rss_ttl'])) {
			echo '	<ttl>'.$data['rss_ttl'].'</ttl>'."\n";
		}
		
		if ('' != trim($data['rss_image'])) {
			echo '	<image>'."\n";
			echo '		<url>'.$data['rss_image'].'</url>'."\n";
			echo '		<title>'.get_bloginfo('name').'</title>'."\n";
			echo '		<link>'.site_url().'</link>'."\n";
			echo '		<width>144</width>'."\n";
			echo '		<height>144</height>'."\n";
			echo '	</image>'."\n";
		}
		
		// iTunes - tags for the channnel:
		if ( $data['new-feed-url'] == 'Enable' ) {
			if ( !empty($data['podcastFeedURL']) && !strpos(strtolower($data['podcastFeedURL']), 'phobos.apple.com') && !strpos(strtolower($data['podcastFeedURL']), 'itpc://') ) {
				echo '	<itunes:new-feed-url>'.podPress_feedSafeContent($data['podcastFeedURL']).'</itunes:new-feed-url>'."\n";
			}
		}
		
		echo '	<itunes:subtitle>'.podPress_strlimiter_end(podPress_feedSafeContent($data['subtitle'], FALSE, TRUE), 254, '[...]', TRUE).'</itunes:subtitle>'."\n";
		echo '	<itunes:summary>'.podPress_strlimiter_end(podPress_feedSafeContent($data['summary'], FALSE, TRUE), 4000, '[...]', TRUE).'</itunes:summary>'."\n";
		echo '	<itunes:keywords>'.podPress_feedSafeContent($data['keywords']).'</itunes:keywords>'."\n";
		if ( TRUE === $is_podpress_feed ) {
			echo podPress_getiTunesCategoryTags($feed['itunes-category']);
		} else {
			echo podPress_getiTunesCategoryTags();			
		}
		echo '	<itunes:author>'.podPress_feedSafeContent($data['author']).'</itunes:author>'."\n";
		echo '	<itunes:owner>'."\n";
		echo '		<itunes:name>'.stripslashes(podPress_feedSafeContent($data['author'])).'</itunes:name>'."\n";
		echo '		<itunes:email>'.podPress_feedSafeContent($data['admin_email']).'</itunes:email>'."\n";
		echo '	</itunes:owner>'."\n";
		if ('yes' == strtolower($data['block']) OR 'no' == strtolower($data['block'])) {
			echo '	<itunes:block>'.strtolower($data['block']).'</itunes:block>'."\n";
		} else {
			echo '	<itunes:block>no</itunes:block>'."\n";
		}
		echo '	<itunes:explicit>'.podPress_feedSafeContent(strtolower($data['explicit'])).'</itunes:explicit>'."\n";
		if ('' != trim($data['image'])) {
			echo '	<itunes:image href="'.$data['image'].'" />'."\n";
		}
	}
	
	/**
	* podpress_get_content_rss - retrieves the excerpt or alternatively the content
	* @package podPress
	* @since 8.8.10.7
	* @return str - the excerpt content
	*/
	function podpress_get_excerpt_rss() {
		global $post, $wp_version;
		$output = $post->post_excerpt;
		if ( empty($output) ) {
			if ( TRUE == version_compare($wp_version, '2.9', '>=') ) {
				$output = get_the_content_feed();
			} else {
				$output = podpress_get_content_rss();
			}
		}
		return apply_filters('the_excerpt_rss', $output);
	}
	
	/**
	* podpress_get_content_rss - retrieves the_content_rss (only for WP < 2.9)
	* @package podPress
	* @since 8.8.10.7
	* @return str - the content 
	*/
	function podpress_get_content_rss() {
		$more_link_text = '';
		$stripteaser = false;
		$more_file = '';
		$content = get_the_content($more_link_text, $stripteaser, $more_file);
		$content = apply_filters('the_content_rss', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}
	
	/**
	* podpress_post_is_password_protected - checks whether a password is required for the current post (with backward compatibility)
	* @package podPress
	* @since 8.8.10.3
	* @return bool
	*/	
	function podpress_post_is_password_protected() {
		GLOBAL $post, $wp_version;
		if ( TRUE === version_compare($wp_version, '2.7', '>=') ) {
			if ( TRUE === post_password_required($post) ) {
				$is_password_protected = TRUE;
			} else {
				$is_password_protected = FALSE;
			}
		} else {
			if ( !empty($post->post_password) ) { // if there's a password
				if ( !isset($_COOKIE['wp-postpass_'.COOKIEHASH]) || $_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password ) {  // and it doesn't match the cookie
					$is_password_protected = TRUE;
				} else {
					$is_password_protected = FALSE;
				}
			} else {
				$is_password_protected = FALSE;
			}
		}
		return $is_password_protected;
	}
	
	function podPress_rss2_item() {
		GLOBAL $podPress, $post, $post_meta_cache, $blog_id, $wp_version;
		
		$enclosureTag = podPress_getEnclosureTags();
		
		if ( $enclosureTag != '' ) { // if no enclosure tag, no need for iTunes tags
			$is_password_protected = podpress_post_is_password_protected();
			if ( FALSE === $is_password_protected ) {
				echo $enclosureTag;
			}
global $podpress_is_itunessubtitle_or_summary;
$podpress_is_itunessubtitle_or_summary = TRUE;
			if ( $post->podPressPostSpecific['itunes:subtitle'] == '##PostExcerpt##' ) {
				if ( TRUE === $is_password_protected ) {
					$post->podPressPostSpecific['itunes:subtitle'] = __('This post is password protected.', 'podPress');
				} else {
					$post->podPressPostSpecific['itunes:subtitle'] = trim(podpress_get_excerpt_rss());
				}
			}
			if ( TRUE == empty($post->podPressPostSpecific['itunes:subtitle']) ) {
				$post->podPressPostSpecific['itunes:subtitle'] = get_the_title_rss();
			}
			echo '		<itunes:subtitle>'.podPress_strlimiter_end(podPress_feedSafeContent(stripslashes($post->podPressPostSpecific['itunes:subtitle']), FALSE, TRUE), 254, '[...]', TRUE).'</itunes:subtitle>'."\n";
			
			if ( FALSE == isset($post->podPressPostSpecific['itunes:summary']) ) {
				$post->podPressPostSpecific['itunes:summary'] = '##PostContentShortened##';
			}

			Switch ($post->podPressPostSpecific['itunes:summary']) {
				case '##Global##' :
					$post->podPressPostSpecific['itunes:summary'] = $podPress->settings['iTunes']['summary'];
				break;
				case '##PostExcerpt##' :
					if ( TRUE === $is_password_protected ) {
						$post->podPressPostSpecific['itunes:summary'] = __('This post is password protected.', 'podPress');
					} else {
						$post->podPressPostSpecific['itunes:summary'] = trim(podpress_get_excerpt_rss());
					}
				break;
				case '##PostContentShortened##' :
					if ( TRUE === $is_password_protected ) {
						$post->podPressPostSpecific['itunes:summary'] = __('This post is password protected.', 'podPress');
					} else {
						if ( TRUE == version_compare($wp_version, '2.9', '>=') ) {
							$post->podPressPostSpecific['itunes:summary'] = get_the_content_feed();
						} else {
							$post->podPressPostSpecific['itunes:summary'] = trim(podpress_get_content_rss());
						}
					}
				break;
			}
$podpress_is_itunessubtitle_or_summary = FALSE;
			
			if ( FALSE == empty($post->podPressPostSpecific['itunes:summary']) ) {
				echo '		<itunes:summary>'.podPress_strlimiter_end(podPress_feedSafeContent(stripslashes($post->podPressPostSpecific['itunes:summary']), FALSE, TRUE), 4000, '[...]', TRUE).'</itunes:summary>'."\n";
			}
			
			Switch ( $post->podPressPostSpecific['itunes:keywords'] ) {
				default:
				case '##WordPressCats##' :
					$categories = get_the_category();
					$post->podPressPostSpecific['itunes:keywords'] = '';
					if ( TRUE == is_array($categories) AND FALSE == empty($categories) ) {
						$category_names = Array();
						$b = '';
						foreach ($categories as $category) {
							$result = preg_match('/\S+\s+\S+/', $category->cat_name, $b);
							// take the category name only if it does not contain inner white spaces (if it does not consist of more than one word)
							if (TRUE == empty($b)) {
								$category_names[] = $category->cat_name;
							}
						}
						if ( TRUE == is_array($category_names) AND FALSE == empty($category_names) ) {
							$nr_category_names = count($category_names);
							if ( $nr_category_names > 1 ) {
								for ($i=0; $i < min($nr_category_names, 12); $i++) { // max. 12 keywords are allowed
									if ( 0 == $i ) {
										$post->podPressPostSpecific['itunes:keywords'] = $category_names[$i];
									} else {
										$post->podPressPostSpecific['itunes:keywords'] .= ', '.$category_names[$i];
									}
								}
							} elseif ( $nr_category_names === 1 ) {
								$post->podPressPostSpecific['itunes:keywords'] = $category_names[0];
							}
						}
					}
				break;
				case '##post_tags##' :
					$posttags = get_the_tags();
					$post->podPressPostSpecific['itunes:keywords'] = '';
					if ( is_array($posttags) AND FALSE == empty($posttags) ) {
						$posttags_nr = count($posttags);
						$i = 0;
						foreach ( $posttags as $tag ) {
							if ( $i == ($posttags_nr-1) ) {
								$post->podPressPostSpecific['itunes:keywords'] .= $tag->name; 
							} else {
								$post->podPressPostSpecific['itunes:keywords'] .= $tag->name . ', '; 
							}
							$i++;
						}
					}
				break;
				case '##Global##' :
					$post->podPressPostSpecific['itunes:keywords'] = $podPress->settings['iTunes']['keywords'];
				break;
			}
			if ( FALSE == empty($post->podPressPostSpecific['itunes:keywords']) ) {
				echo '		<itunes:keywords>'.podPress_feedSafeContent( $podPress->cleanup_itunes_keywords(stripslashes($post->podPressPostSpecific['itunes:keywords']), '', FALSE) ).'</itunes:keywords>'."\n";
			}
			
			if($post->podPressPostSpecific['itunes:author'] == '##Global##') {
				$post->podPressPostSpecific['itunes:author'] = $podPress->settings['iTunes']['author'];
				if(empty($post->podPressPostSpecific['itunes:author'])) {
					$post->podPressPostSpecific['itunes:author'] = stripslashes(get_option('admin_email'));
				}
			}
			echo '		<itunes:author>'.podPress_feedSafeContent($post->podPressPostSpecific['itunes:author']).'</itunes:author>'."\n";

			if ($post->podPressPostSpecific['itunes:explicit'] == 'Default') {
				$post->podPressPostSpecific['itunes:explicit'] = $podPress->settings['iTunes']['explicit'];
			}
			if ( '' == trim($post->podPressPostSpecific['itunes:explicit']) OR ('' != trim($post->podPressPostSpecific['itunes:explicit']) AND 'no' != strtolower($post->podPressPostSpecific['itunes:explicit']) AND 'yes' != strtolower($post->podPressPostSpecific['itunes:explicit']) AND 'clean' != strtolower($post->podPressPostSpecific['itunes:explicit'])) ) {
				$post->podPressPostSpecific['itunes:explicit'] = 'No';
			}
			echo '		<itunes:explicit>'.podPress_feedSafeContent(strtolower($post->podPressPostSpecific['itunes:explicit'])).'</itunes:explicit>'."\n";

			if ($post->podPressPostSpecific['itunes:block'] == 'Default') {
				$post->podPressPostSpecific['itunes:block'] = $podPress->settings['iTunes']['block'];
			}
			if ( '' == trim($post->podPressPostSpecific['itunes:block']) OR ('' != trim($post->podPressPostSpecific['itunes:block']) AND 'no' != strtolower($post->podPressPostSpecific['itunes:block']) AND 'yes' != strtolower($post->podPressPostSpecific['itunes:block'])) ) {
				$post->podPressPostSpecific['itunes:block'] = 'No';
			}
			echo '		<itunes:block>'.podPress_feedSafeContent(strtolower($post->podPressPostSpecific['itunes:block'])).'</itunes:block>'."\n";
			
			//echo '<comments>'. get_comments_link() .'</comments>'."\n";
			
			$episodeLicenseTags = podPress_getEpisodeLicenseTags();
			if ( $episodeLicenseTags != '' ) {
				echo $episodeLicenseTags;
			}
		}
		if (isset($post_meta_cache[$blog_id][$post->ID]['enclosure_podPressHold'])) {
			$post_meta_cache[$blog_id][$post->ID]['enclosure'] = $post_meta_cache[$blog_id][$post->ID]['enclosure_podPressHold'];
			unset($post_meta_cache[$blog_id][$post->ID]['enclosure_podPressHold']);
		}
		
		// add the enclosures which are not added with podPress at last
		podPress_add_nonpodpress_enclosures('rss2');
	}

	function podPress_atom_head() {
		GLOBAL $podPress;
		
		$data['rss_image'] = get_option('rss_image');
		$data['rss_copyright'] = $podPress->settings['rss_copyright'];
		$data['rss_license_url'] = $podPress->settings['rss_license_url'];
		
		if($podPress->settings['category_data']['categoryCasting'] == 'true') {
			if($podPress->settings['category_data']['rss_imageChoice'] == 'Custom') {
				$data['rss_image'] = $podPress->settings['category_data']['rss_image'];
			}
			if($podPress->settings['category_data']['rss_copyrightChoice'] == 'Custom') {
				$data['rss_copyright'] = $podPress->settings['category_data']['rss_copyright'];
			}
			if($podPress->settings['category_data']['rss_rss_license_urlChoice'] == 'Custom') {
				$data['rss_license_url'] = $podPress->settings['category_data']['rss_license_url'];
			}
		}
		$feedslug = get_query_var('feed');
		$is_podpress_feed = FALSE;
		foreach ($podPress->settings['podpress_feeds'] as $feed) {
			if ( $feedslug === $feed['slug'] ) {
				$is_podpress_feed = TRUE;
				break;
			}
		}		
		if ( TRUE == $is_podpress_feed ) {
			$data['rss_image'] = $feed['rss_image'];
			$data['rss_copyright'] = $feed['copyright'];
			$data['rss_license_url'] = $feed['license_url'];
		}
		
		if (isset($podPress->settings['enableVersionInFeeds']) AND TRUE === $podPress->settings['enableVersionInFeeds'] ) {
			if (isset($podPress->settings['disableVersionNumber']) AND TRUE === $podPress->settings['disableVersionNumber'] ) {
				echo '	<!-- podcast_generator="podPress" -->'."\n";
			} else {
				echo '	<!-- podcast_generator="podPress/'.PODPRESS_VERSION.'" -->'."\n";
			}
		}
		
		echo "\t".'<logo>'.podPress_feedSafeContent($data['rss_image']).'</logo>'."\n";

		if (empty($data['rss_copyright'])) {
			echo "\t".'<rights>'.podPress_feedSafeContent(__('Copyright', 'podpress').' &#xA9; '. date('Y',time())).' '.get_bloginfo('blogname').'</rights>'."\n";
		} else {
			echo "\t".'<rights>'.podPress_feedSafeContent($data['rss_copyright']).'</rights>'."\n";
		}

		if ( !empty($podPress->settings['rss_license_url']) ) {
			echo "\t".'<link rel="license" type="text/html" href="'.$data['rss_license_url'].'" />'."\n";
		}
	}

	function podPress_atom_entry() {
		$enclosureTag = podPress_getEnclosureTags('atom');
		if ($enclosureTag != '') { // if no enclosure tag, no need for iTunes tags
		
			$is_password_protected = podpress_post_is_password_protected();
			if ( FALSE === $is_password_protected ) {
				echo $enclosureTag;
			}
			
			$episodeLicenseTags = podPress_getEpisodeLicenseTags('atom');
			if ($episodeLicenseTags != '')	{
				echo $episodeLicenseTags;
			}
		}
		
		// add the enclosures which are not added with podPress at last
		podPress_add_nonpodpress_enclosures('atom');
	}

	function podPress_xspf_playlist() {
		GLOBAL $podPress, $more, $posts, $post;
		header('HTTP/1.0 200 OK');
		header('Content-type: application/xspf+xml; charset=' . get_bloginfo('charset'), true);
		header('Content-Disposition: attachment; filename="playlist.xspf"');
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Wed, 28 Oct 2010 05:00:00 GMT'); // Date in the past
		$more = 1;
		echo '<?xml version="1.0" encoding="'.get_bloginfo('charset').'" ?'.">\n";
		echo '<playlist version="1" xmlns="http://xspf.org/ns/0/">'."\n";
		echo "\t".'<title>'. podPress_feedSafeContent(get_bloginfo('blogname')) . '</title>'."\n";
		echo "\t".'<annotation><![CDATA['. $podPress->settings['iTunes']['summary'].']]></annotation>'."\n";
		if (empty($podPress->settings['iTunes']['author'])) {
			$creator = get_bloginfo('blogname');
		} else {
			$creator = $podPress->settings['iTunes']['author'];
		}
		echo "\t".'<creator>'. podPress_feedSafeContent($creator). '</creator>'."\n";
		echo "\t".'<location>'.get_feed_link('playlist.xspf').'</location>'."\n";
		if ( !empty($podPress->settings['rss_license_url']) ) {
			echo "\t".'<license>'.$podPress->settings['rss_license_url'].'</license>'."\n";
		}
		echo "\t".'<trackList>'."\n";
		if (isset($posts)) {
			foreach ($posts as $post) {
				start_wp(); /* This is a call to a very very old function and it seems to be not necessary if $post is global. */
				$enclosureTag = podPress_getEnclosureTags('xspf');
				if ( $enclosureTag != '' ) {// if no enclosure tag, no need for track tags
					$is_password_protected = podpress_post_is_password_protected();
					if ( FALSE === $is_password_protected ) {
						echo $enclosureTag;
					}
				}
			}
		}
		echo "\t".'</trackList>'."\n";
		echo '</playlist>'."\n";
		exit;
	}
	
	function podPress_getEpisodeLicenseTags($feedtype = 'rss2') {
		GLOBAL $podPress, $post, $wpdb;
		$result = '';
		$hasMediaFileAccessible = FALSE;
		if (is_array($post->podPressMedia)) {
			reset($post->podPressMedia);
			while ( list($key, $val) = each($post->podPressMedia) ) {
				// get the post_meta 
				$querystring = 'SELECT meta_key, meta_value FROM '.$wpdb->postmeta." WHERE post_id='".$post->ID."' and (meta_key='podcast_episode_license_url' or meta_key='podcast_episode_license_name')";
				$episode_license_infos = $wpdb->get_results($querystring);
				$license = array();
				if ( 0 < count($episode_license_infos) ) {
					foreach ($episode_license_infos as $episode_license_info) {
						$license[$episode_license_info->meta_key] = $episode_license_info->meta_value;
					}
				} 
				if (TRUE == isset($license['podcast_episode_license_url'])) {
					switch ($feedtype) {
						case 'rss2' :
						case 'rss' :
						case 'rdf' : // license tags for entries with the help of the Dublin Core
							if (TRUE == isset($license['podcast_episode_license_url']) AND FALSE == isset($license['podcast_episode_license_name'])) {
								$result = "\t\t".'<dc:rights>'.$license['podcast_episode_license_url'].'</dc:rights>'."\n";
							} elseif (TRUE == isset($license['podcast_episode_license_name']) AND TRUE == isset($license['podcast_episode_license_name'])) {
								$result = "\t\t".'<dc:rights>'.podPress_feedSafeContent($license['podcast_episode_license_name']).' '.$license['podcast_episode_license_url'].'</dc:rights>'."\n";
							}
						break;
						case 'atom' : // Atom License Extension -  http://tools.ietf.org/html/rfc4946
							if (TRUE == isset($license['podcast_episode_license_url']) AND FALSE == isset($license['podcast_episode_license_name'])) {
								$result = "\t".'<rights>'.$license['podcast_episode_license_url'].'</rights>'."\n";
								$result .= "\t".'<link rel="license" type="text/html" href="'.$license['podcast_episode_license_url'].'" />'."\n";
							} elseif (TRUE == isset($license['podcast_episode_license_name']) AND TRUE == isset($license['podcast_episode_license_name'])) {
								$result = "\t".'<rights>'.podPress_feedSafeContent($license['podcast_episode_license_name']).'</rights>'."\n";
								$result .= "\t".'<link rel="license" type="text/html" href="'.$license['podcast_episode_license_url'].'" />'."\n";
							}
						break;
						default : // no entry license tags for all other feed types like xspf
							$result = '';
						break;
					}
				}
			}
		}
		return $result;
	}

	function podPress_getEnclosureTags($feedtype = 'rss2') {
		GLOBAL $podPress, $post, $podpress_allowed_ext, $wp_query;
		$result = '';
		$hasMediaFileAccessible = false;
		$same_enclosure_URL_in_postmeta_exists = false;
		
		$is_podpress_feed = FALSE;
		$feedslug = $wp_query->query_vars['feed'];
		foreach ( $podPress->settings['podpress_feeds'] as $feed ) {
			if ( $feedslug === $feed['slug'] ) {
				$is_podpress_feed = TRUE;
				break;
			}
		}
		if ( TRUE === $is_podpress_feed AND TRUE == isset($feed['bypass_incl_selection']) AND TRUE === $feed['bypass_incl_selection'] ) {
			$ignore_incl_selection = TRUE;
		} else {
			$ignore_incl_selection = FALSE;
		}
		if(is_array($post->podPressMedia)) {
			$foundPreferred = false;
			reset($post->podPressMedia);
			while (list($key, $val) = each($post->podPressMedia)) {
				$preferredFormat = false;
				if(!$post->podPressMedia[$key]['authorized']) {
					if ( $podPress->settings['premiumContentFakeEnclosure'] ) {
						$post->podPressMedia[$key]['URI'] = 'podPress_Protected_Content.mp3';
					} else {
						continue;
					}
				}
				if ( defined('PODPRESS_TORRENTCAST') && !empty($post->podPressMedia[$key]['authorized']['URI_torrent']) ) {
					$post->podPressMedia[$key]['URI'] = $post->podPressMedia[$key]['URI_torrent'];
				}
				$hasMediaFileAccessible = true;
				if ( is_array($podpress_allowed_ext) ) {
					if ( FALSE == in_array($post->podPressMedia[$key]['ext'], $podpress_allowed_ext) ) {
						continue;
					} else {
						$preferredFormat = true;
					}
				} 
				if(isset($_GET['format']) && $_GET['format'] == $post->podPressMedia[$key]['ext']) {
					$preferredFormat = true;
				}
				if ( (isset($post->podPressMedia[$key]['rss']) AND $post->podPressMedia[$key]['rss'] == 'on') || (isset($post->podPressMedia[$key]['atom']) AND $post->podPressMedia[$key]['atom'] == 'on') || $preferredFormat === true || $ignore_incl_selection === TRUE ) {
					if ($feedtype == 'atom' && ( $post->podPressMedia[$key]['atom'] == 'on' OR $preferredFormat === TRUE OR $ignore_incl_selection === TRUE) ) {
						$post->podPressMedia[$key]['URI'] = $podPress->convertPodcastFileNameToWebPath($post->ID, $key, $post->podPressMedia[$key]['URI'], 'feed');
						$result .= '<link rel="enclosure" type="'.$post->podPressMedia[$key]['mimetype'].'" href="'.$post->podPressMedia[$key]['URI'].'" length="'.$post->podPressMedia[$key]['size'].'" />'."\n";
					} elseif ($feedtype == 'xspf') {
						$post->podPressMedia[$key]['URI'] = $podPress->convertPodcastFileNameToValidWebPath($post->podPressMedia[$key]['URI']);
						//$post->podPressMedia[$key]['URI'] = $podPress->convertPodcastFileNameToWebPath($post->ID, $key, $post->podPressMedia[$key]['URI'], 'feed');
						if ( 'mp3' == podPress_getFileExt($post->podPressMedia[$key]['URI']) ) {
							$result .= "\t\t".'<track>'."\n";
							$result .= "\t\t\t".'<location>'.$post->podPressMedia[$key]['URI']."</location>\n";
							if (!empty($post->podPressMedia[$key]['title'])) {
								$result .= "\t\t\t".'<annotation>'.podPress_feedSafeContent(html_entity_decode($post->podPressMedia[$key]['title']))."</annotation>\n";
								$result .= "\t\t\t".'<title>'.podPress_feedSafeContent(html_entity_decode($post->podPressMedia[$key]['title']))."</title>\n";
							} else {
								$result .= "\t\t\t".'<annotation>'.podPress_feedSafeContent(html_entity_decode($post->post_title))."</annotation>\n";
								$result .= "\t\t\t".'<title>'.podPress_feedSafeContent(html_entity_decode($post->post_title))."</title>\n";
							}
							if ( '##Global##' == $post->podPressPostSpecific['itunes:author']) {
								if (empty($podPress->settings['iTunes']['author'])) {
									$creator = get_bloginfo('blogname');
								} else {
									$creator = $podPress->settings['iTunes']['author'];
								}
								$result .= "\t\t\t".'<creator>'.podPress_feedSafeContent(html_entity_decode($creator)).'</creator>'."\n";
							} else {
								$result .= "\t\t\t".'<creator>'.podPress_feedSafeContent(html_entity_decode($post->podPressPostSpecific['itunes:author'])).'</creator>'."\n";
							}
							if ( 'UNKNOWN' != $post->podPressMedia[$key]['duration'] AND FALSE === empty($post->podPressMedia[$key]['duration'])) {
								$result .= "\t\t\t".'<duration>'.$podPress->strtomilliseconds($post->podPressMedia[$key]['duration']).'</duration>'."\n";
							} 
							if(!empty($post->podPressMedia[$key]['previewImage'])) {
								$result .= "\t\t\t".'<image>'.$post->podPressMedia[$key]['previewImage']."</image>\n";
							}
							$result .= "\t\t".'</track>'."\n";
						}
					} elseif ($feedtype == 'rss2') {
						$post->podPressMedia[$key]['URI'] = $podPress->convertPodcastFileNameToWebPath($post->ID, $key, $post->podPressMedia[$key]['URI'], 'feed');
						if(!isset($post->podPressMedia[$key]['duration']) || !preg_match("/([0-9]):([0-9])/", $post->podPressMedia[$key]['duration'])) {
							$post->podPressMedia[$key]['duration'] = '00:01'; // m:s
						}
						
						// for more info: http://www.apple.com/itunes/podcasts/specs.html#duration
						// the duration string is sometimes in the format mmm:ss. That is why it is a rebuilding this string might be necessary.
						$durationTag = '<itunes:duration>'.$podPress->millisecondstostring($podPress->strtomilliseconds($post->podPressMedia[$key]['duration']), 'h:m:s').'</itunes:duration>'."\n";

						// $foundPreferred is the limiter for enclosures in RSS items
						// $preferredFormat signals whether a file type filter is active or not
						if ( (isset($post->podPressMedia[$key]['rss']) AND $post->podPressMedia[$key]['rss'] == 'on') || $ignore_incl_selection === TRUE) {
							if ( !$preferredFormat && $foundPreferred ) {
								continue;
							} elseif ( $preferredFormat ) {
								$foundPreferred = true;
							}
							$result .= "\t\t".'<enclosure url="'.$post->podPressMedia[$key]['URI'].'" length="'.$post->podPressMedia[$key]['size'].'" type="'.$post->podPressMedia[$key]['mimetype'].'" />'."\n";
							$result .= "\t\t".$durationTag;
						} elseif ( $preferredFormat && !$foundPreferred ) {
							$result .= "\t\t".'<enclosure url="'.$post->podPressMedia[$key]['URI'].'" length="'.$post->podPressMedia[$key]['size'].'" type="'.$post->podPressMedia[$key]['mimetype'].'" />'."\n";
							$result .= "\t\t".$durationTag;
							$foundPreferred = true;
						}
					} 
				}
			}
		}
		if ($hasMediaFileAccessible && $result == '' && $feedtype != 'xspf' ) {
			echo "<!-- Media File exists for this post, but its not enabled for this feed -->\n";
		}
		switch ($feedtype) {
			case 'atom' : 
				return apply_filters('podpress_entry_enclosuretags', $result);
			break;
			case 'xspf' : 
				return apply_filters('podpress_xspf_trackinformation', $result);
			break;
			case 'rss2' : 
			default:
				return apply_filters('podpress_item_enclosure_and_itunesduration', $result);
			break;
		}
	}
	
	// This function removes the enclosure tags off the feed of the enclosures which were not added with podPress
	// For instance modern WP versions adding an custom field with the name "enclosure" to posts with links to media files in their content. The value of such a custom field consists of the data for enclosure tag attributes.
	// When WP builds a feed e.g. the main RSS or ATOM feed it adds these custom fields data as enclosures to the feed items/entries.
	// This little filter function prevents that. But podPress_add_nonpodpress_enclosures adds these enclosure tags later. This way the podPress enclosure will always be the first.
	// The experience shows that only the first enclosure of each item (RSS) is recognized by iTunes and other feed readers like FireFox. 
	function podPress_dont_print_nonpodpress_enclosures($enclosure_tag = '') {
		return '';
	}
	
	// This function prints the enclosure tags of the enclosure which were not added with podPress
	function podPress_add_nonpodpress_enclosures($feedtype) {
		GLOBAL $podPress, $post, $podpress_allowed_ext;
		if ( podpress_post_is_password_protected() ) { return; }
		foreach ( (array) get_post_custom() as $key => $val) {
			if ($key == 'enclosure') {
				foreach ( (array) $val as $enc ) {
					$is_a_link_to_podPress_media = FALSE;
					$enclosure = explode("\n", $enc);
					$enclosure_url = trim(htmlspecialchars($enclosure[0]));
					
					// do not include non-podPress enclosures if they are not of the right type for the current feed
					$ext = end(explode('.', $enclosure_url));
					if ( is_array($podpress_allowed_ext) AND FALSE == empty($ext) ) {
						if (FALSE == in_array($ext, $podpress_allowed_ext)) {
							continue;
						}
					}

					// check whether the enclosure URL is equal to a podPress enclosure URL
					// If not then print the enclosure tag else don't.
					if ( TRUE == is_array($post->podPressMedia) ) {
						foreach ($post->podPressMedia as $key => $value) {
							if ( TRUE == isset($post->podPressMedia[$key]['URI']) AND $enclosure_url == $post->podPressMedia[$key]['URI'] ) {
								$is_a_link_to_podPress_media = TRUE;
							}
						}
					}
					if (FALSE == $is_a_link_to_podPress_media) {
						// only get the the first element eg, audio/mpeg from 'audio/mpeg mpga mp2 mp3'
						$t = preg_split('/[ \t]/', trim($enclosure[2]) );
						$type = $t[0];
						switch ($feedtype) {
							case 'atom' :
								echo apply_filters('nonpodpress_atom_enclosure', "\t\t".'<link href="' . $enclosure_url . '" rel="enclosure" length="' . trim($enclosure[1]) . '" type="' . $type . '" />' . "\n");
							break;
							case 'rss2':
							default :
								echo apply_filters('nonpodpress_rss_enclosure', "\t\t".'<enclosure url="' . $enclosure_url . '" length="' . trim($enclosure[1]) . '" type="' . $type . '" />' . "\n");
							break;
						}
					} 
				}
			}
		}
	}

	function podPress_getiTunesCategoryTags($podpress_feed_cat = '') {
		GLOBAL $podPress, $post;
		$result = '';
		$data = array();
		if( (isset($podPress->settings['category_data']['categoryCasting']) AND 'true' == $podPress->settings['category_data']['categoryCasting']) && is_array($podPress->settings['category_data']['iTunesCategory'])) {
			foreach ($podPress->settings['category_data']['iTunesCategory'] as $key=>$value) {
				if($value == '##Global##') {
					if(!empty($podPress->settings['iTunes']['category'][$key])) {
						$data[] = $podPress->settings['iTunes']['category'][$key];
					}
				} else {
					$data[] = $value;
				}
			}
		}
		if(empty($data)) {
			if ( TRUE == is_array($podpress_feed_cat) ) {
				$data = $podpress_feed_cat;
			} else {
				$data = $podPress->settings['iTunes']['category'];
			}
		}
		if(is_array($data)) {
			foreach($data as $thiscat) {
				if (strstr($thiscat, ':')) {
					list($cat, $subcat) = explode(":", $thiscat);
					$result .= "\t".'<itunes:category text="'.podPress_feedSafeContent($cat).'">'."\n";
					$result .= "\t\t".'<itunes:category text="'.podPress_feedSafeContent($subcat).'" />'."\n";
					$result .= "\t".'</itunes:category>'."\n";
				} elseif (!empty($thiscat) AND '[ nothing ]' != $thiscat ) {
					$result .= "\t".'<itunes:category text="'.podPress_feedSafeContent($thiscat).'" />'."\n";
				}
			}
		}
		if(empty($result)) {
			$result .= "\t".'<itunes:category text="Society &#38; Culture" />'."\n";
		}
		return $result;
	}

	function podPress_getCategoryCastingFeedData($selection, $input) {
		GLOBAL $podPress;

		$feedslug = get_query_var('feed');		
		$is_podpress_feed = FALSE;
		foreach ($podPress->settings['podpress_feeds'] as $feed) {
			if ( $feedslug === $feed['slug'] ) {
				$is_podpress_feed = TRUE;
				break;
			}
		}
		
		if ( empty($feedslug) OR (FALSE == isset($podPress->settings['category_data']['categoryCasting']) AND FALSE === $is_podpress_feed) ) {
			return $input;
		} else {
			if ( isset($podPress->settings['category_data']['categoryCasting']) AND empty($podPress->settings['category_data']['categoryCasting']) ) {
				$podPress->settings['category_data']['categoryCasting'] = 'true';
			}
			if ( isset($podPress->settings['category_data']['categoryCasting']) AND 'true' == $podPress->settings['category_data']['categoryCasting'] ) {
				switch ($selection) {
					case 'blogname' :
						switch ($podPress->settings['category_data']['blognameChoice']) {
							case 'Global' : // use the blogname as category feed title
								if ( empty($podPress->settings['category_data']['blogname']) ) {
									return $input;
								} else {
									add_filter('wp_title_rss', 'podPress_customfeedtitleonly'); // this filter works since WP 2.2
									return $podPress->settings['category_data']['blogname'];
								}
							break;
							case 'CategoryName' :
								if ( empty($podPress->settings['category_data']['cat_name']) ) {
									return $input;
								} else {
									add_filter('wp_title_rss', 'podPress_customfeedtitleonly'); // this filter works since WP 2.2
									return stripslashes($podPress->settings['category_data']['cat_name']);
								}
							break;
							default:
							case 'Append' : // use the default WP scheme for category feed titles (Site Title >> Category Name)
								return $input;
							break;
						}
					break;
					case 'blogdescription' :
						if($podPress->settings['category_data']['blogdescriptionChoice'] == 'CategoryDescription' && !empty($podPress->settings['category_data']['cat_description'])) {
							return stripslashes($podPress->settings['category_data']['cat_description']);
						}
						return $input;
					break;
					case 'rss_language' :
						if($podPress->settings['category_data']['rss_language'] == '##Global##' || empty($podPress->settings['category_data']['rss_language'])) {
							return $input;
						} else {
							return $podPress->settings['category_data']['rss_language'];
						}
					break;
					case 'rss_image' :
						if($podPress->settings['category_data']['rss_imageChoice'] == 'Global' || empty($podPress->settings['category_data']['rss_image'])) {
							return $input;
						} else {
							return $podPress->settings['category_data']['rss_image'];
						}
					break;
					default:
						return $input;
					break;
				}
			} elseif ( TRUE === $is_podpress_feed ) {
				switch ($selection) {
					case 'blogname' :
						switch ( $feed['feedtitle'] ) {
							default:
							case 'append' :
								return stripslashes($input.' &#187; '.$feed['name']);
							break;
							case 'blognameastitle' :
								return $input;
							break;
							case 'feednameastitle' :
								return stripslashes($feed['name']);
							break;
						}
					break;
					case 'blogdescription' :
						$selection = 'descr';
					case 'rss_language' :
					case 'rss_image' :
						if ( FALSE == empty($feed[$selection]) ) {
							return stripslashes($feed[$selection]);
						} else {
							return $input;
						}
					break;
					default:
						return $input;
					break;
				}
			} else {
				return $input;
			}
		}
	}
	
	function podPress_feedBlogName ($input) {
		return podPress_getCategoryCastingFeedData('blogname', $input);
	}
	function podPress_customfeedtitleonly($input) {
		return '';
	}
	
	function podPress_feedBlogDescription ($input) {
		return podPress_getCategoryCastingFeedData('blogdescription', $input);
	}

	function podPress_feedBlogRssLanguage ($input) {
		return podPress_getCategoryCastingFeedData('rss_language', $input);
	}

	function podPress_feedBlogRssImage ($input) {
		return podPress_getCategoryCastingFeedData('rss_image', $input);
	}