<?php
// This is a podPress replacement file.

if (empty($doing_rss)) {
    $doing_rss = 1;
    require(dirname(__FILE__) . '/wp-blog-header.php');
}

// Remove the pad, if present.
$feed = preg_replace('/^_+/', '', $feed);

if ($feed == '' || $feed == 'feed') {
    $feed = 'rss2';
}

if ( is_single() || ($withcomments == 1) ) {
    require(ABSPATH . 'wp-commentsrss2.php');
} else {
    switch ($feed) {
    case 'atom':
        require(ABSPATH . 'wp-atom.php');
        break;
    case 'rdf':
        require(ABSPATH . 'wp-rdf.php');
        break;
    case 'rss':
        require(ABSPATH . 'wp-rss.php');
        break;
    case 'rss2':
        require(ABSPATH . 'wp-rss2.php');
        break;
    case 'comments-rss2':
        require(ABSPATH . 'wp-commentsrss2.php');
        break;
		case 'blog-header':
		case 'commentsrss2':
		case 'config':
		case 'config-sample':
		case 'feed':
		case 'links-opml':
		case 'login':
		case 'mail':
		case 'pass':
		case 'register':
		case 'settings':
		case 'trackback':
				break;
		default:
			$feed = str_replace('/', '', $feed);
			$feed = str_replace('\\', '', $feed);
			if(file_exists(ABSPATH . 'wp-'.$feed.'.php')) {
        require(ABSPATH . 'wp-'.$feed.'.php');
			}	
    }
}
