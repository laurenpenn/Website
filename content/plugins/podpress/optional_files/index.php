<?php
	/******************************************************************************************/
	/* This enables very simple feed caching, which cuts down on major server processor load. */
	/* From database   my server would take 0.406738 to 0.511147 seconds to generate the feed */
	/* From cache file my server would take 0.000579 to 0.000593 seconds to generate the feed */
	/* As you can see, this is a MAJOR difference.                                            */
	/******************************************************************************************/
	/* Version 1.2                                                                            */
	/*                                                                                        */
	/* CONFIG VALUES - START                                                                  */
	/* $podPressFeedCacheDir - Set this to a dir that your web server is able to write        */
	/*                         files in.                                                      */
	/*                                                                                        */
	/* $podPressFeedCacheTTL - Set this to however long (in seconds) you want your            */
	/*                         feed to cache for.                                             */
	/*                         60 = 1min, 3600 = 1hr, 21600 = 6hrs,                           */
	/*                         43200 = 12hrs, 86400 = 24hrs, 604800 = 1week, etc etc etc      */
	/******************************************************************************************/

 	$podPressUploadsDir = getcwd().'/wp-content/uploads';
 	$podPressFeedCacheDir = $podPressUploadsDir.'/podpress_temp';
	$podPressFeedCacheTTL = 3600;

	/******************************************************************************************/
	/* CONFIG VALUES - END                                                                    */
	/******************************************************************************************/
	$podPressFeedCacheProcess = false;
	if(isset($_GET['feed'])) {
		$podPressFeedCacheProcess = true;
		$podPressFeedCache = $_GET['feed'];
		if(isset($_GET['category_name'])) {
			$podPressFeedCache .= '_'.$_GET['category_name'];
		} elseif(isset($_GET['cat'])) {
			$podPressFeedCache .= '_cat'.$_GET['cat'];
		} elseif(isset($_GET['p'])) {
			$podPressFeedCache .= '_p'.$_GET['p'];
		}
	} elseif(strpos($_SERVER['REQUEST_URI'], '/feed') !== false) {
		$podPressFeedCacheProcess = true;
		/******************************************************************************************/
		/* start - customize if needed - this may need to be customized for your environment      */
		/******************************************************************************************/
		$x = substr(str_replace('/feed', '', $_SERVER['REQUEST_URI']), 1);
		if(empty($x)) {
			$podPressFeedCache = 'rss2'; 
		} else {
			$podPressFeedCache = str_replace('/', '_', $x);
		}
		/******************************************************************************************/
		/* end   - customize if needed                                                            */
		/******************************************************************************************/	
	}

	if($podPressFeedCacheProcess == true) {
		$podPress_continue = true;
		if(!is_writable($podPressUploadsDir)) {
			$podPress_continue = false;
		} elseif(!is_writable($podPressFeedCacheDir)) {
			if(!mkdir($podPressFeedCacheDir, 0700)) {
				if(!is_writable($podPressFeedCacheDir)) {
					$podPress_continue = false;
				}
			}
		}
		if($podPress_continue) {
			function podPressFeedCache($buffer) {
				GLOBAL $podPressFeedCache;
				$goodfeed = false;
				if(substr($buffer, 0, 5) == '<?xml') {
					if ($handle = fopen($podPressFeedCache, 'w')) {
						fwrite($handle, $buffer);
						fclose($handle);
					}
				}
				return $buffer;
			}
			$podPressFeedCache = $podPressFeedCacheDir.'/feedcache_'.$podPressFeedCache.'.xml';
			$currenttime = mktime();
			if(@file_exists($podPressFeedCache) && @filemtime($podPressFeedCache)+$podPressFeedCacheTTL > $currenttime && @filesize($podPressFeedCache) > 10) {
				header('X-FromPodPressCache: true', true);
				header('Content-type: text/xml;', true);
				readfile ($podPressFeedCache);
				exit;
			} else {
				header('X-FromPodPressCache: false', true);
				ob_start('podPressFeedCache');
				define('WP_USE_THEMES', true);
				require('./wp-blog-header.php');
				exit;
			}
		} else {
			header('X-FromPodPressCache: na', true);
		}
	} else {
		header('X-FromPodPressCache: na', true);
	}
	/* Original WordPress code */
	define('WP_USE_THEMES', true);
	require('./wp-blog-header.php');
?>

