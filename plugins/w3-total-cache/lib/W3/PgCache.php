<?php

/**
 * W3 PgCache
 */

/**
 * Class W3_PgCache
 */
class W3_PgCache {
    /**
     * Advanced cache config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * Caching flag
     *
     * @var boolean
     */
    var $_caching = false;

    /**
     * Time start
     *
     * @var double
     */
    var $_time_start = 0;

    /**
     * Lifetime
     * @var integer
     */
    var $_lifetime = 0;

    /**
     * Enhanced mode flag
     *
     * @var boolean
     */
    var $_enhanced_mode = false;

    /**
     * Debug flag
     *
     * @var boolean
     */
    var $_debug = false;

    /**
     * Request URI
     * @var string
     */
    var $_request_uri = '';

    /**
     * Page key
     * @var string
     */
    var $_page_key = '';

    /**
     * Shutdown buffer
     * @var string
     */
    var $_shutdown_buffer = '';

    /**
     * Shutdown compression
     * @var string
     */
    var $_shutdown_compression = '';

    /**
     * Mobile object
     * @var W3_Mobile
     */
    var $_mobile = null;

    /**
     * Referrer object
     * @var W3_Referrer
     */
    var $_referrer = null;

    /**
     * Cache reject reason
     *
     * @var string
     */
    var $cache_reject_reason = '';

    /**
     * PHP5 Constructor
     */
    function __construct() {
        require_once W3TC_LIB_W3_DIR . '/Config.php';

        $this->_config = & W3_Config::instance();
        $this->_debug = $this->_config->get_boolean('pgcache.debug');
        $this->_request_uri = $_SERVER['REQUEST_URI'];
        $this->_lifetime = $this->_config->get_integer('browsercache.html.lifetime');
        $this->_enhanced_mode = ($this->_config->get_string('pgcache.engine') == 'file_generic');

        if ($this->_config->get_boolean('mobile.enabled')) {
            require_once W3TC_LIB_W3_DIR . '/Mobile.php';
            $this->_mobile = & W3_Mobile::instance();
        }

        if ($this->_config->get_boolean('referrer.enabled')) {
            require_once W3TC_LIB_W3_DIR . '/Referrer.php';
            $this->_referrer = & W3_Referrer::instance();
        }
    }

    /**
     * PHP4 Constructor
     */
    function W3_PgCache() {
        $this->__construct();
    }

    /**
     * Do cache logic
     */
    function process() {
        if ($this->_config->get_boolean('pgcache.enabled')) {
            /**
             * Skip caching for some pages
             */
            switch (true) {
                case defined('DONOTCACHEPAGE'):
                case defined('DOING_AJAX'):
                case defined('DOING_CRON'):
                case defined('APP_REQUEST'):
                case defined('XMLRPC_REQUEST'):
                case defined('WP_ADMIN'):
                case (defined('SHORTINIT') && SHORTINIT):
                    return;
            }

            /**
             * Handle mobile or referrer redirects
             */
            if ($this->_mobile || $this->_referrer) {
                $mobile_redirect = $this->_mobile->get_redirect();
                $referrer_redirect = $this->_referrer->get_redirect();

                $redirect = ($mobile_redirect ? $mobile_redirect : $referrer_redirect);

                if ($redirect) {
                    w3_redirect($redirect);
                    exit();
                }
            }

            /**
             * Do page cache logic
             */
            if ($this->_debug) {
                $this->_time_start = w3_microtime();
            }

            $this->_caching = $this->_can_cache();

            if ($this->_caching && !$this->_enhanced_mode) {
                $cache = & $this->_get_cache();

                $mobile_group = $this->_get_mobile_group();
                $referrer_group = $this->_get_referrer_group();
                $encryption = $this->_get_encryption();
                $compression = $this->_get_compression();
                $raw = !$compression;
                $this->_page_key = $this->_get_page_key($this->_request_uri, $mobile_group, $referrer_group, $encryption, $compression);

                /**
                 * Check if page is cached
                 */
                $data = $cache->get($this->_page_key);

                /**
                 * Try to get uncompressed version of cache
                 */
                if ($compression && !$data) {
                    $raw = true;
                    $this->_page_key = $this->_get_page_key($this->_request_uri, $mobile_group, $referrer_group, $encryption, false);

                    $data = $cache->get($this->_page_key);
                }

                /**
                 * If cache exists
                 */
                if ($data) {
                    /**
                     * Do Bad Behavior check
                     */
                    $this->_bad_behavior();

                    if ($this->_enhanced_mode) {
                        $is_404 = false;
                        $headers = array();
                        $time = $cache->mtime($this->_page_key);
                        $content = & $data;
                    } else {
                        $is_404 = $data['404'];
                        $headers = $data['headers'];
                        $time = $data['time'];
                        $content = & $data['content'];
                    }

                    /**
                     * Calculate content etag
                     */
                    $etag = md5($content);

                    /**
                     * Send headers
                     */
                    $this->_send_headers($is_404, $time, $etag, $compression, $headers);

                    /**
                     * Do manual compression for uncompressed page
                     */
                    if ($raw) {
                        /**
                         * Append debug info
                         */
                        if ($this->_debug) {
                            $time_total = w3_microtime() - $this->_time_start;
                            $debug_info = $this->_get_debug_info(true, '', true, $time_total);
                            $content .= "\r\n\r\n" . $debug_info;
                        }

                        /**
                         * Parse dynamic tags
                         */
                        $this->_parse_dynamic($content);

                        /**
                         * Compress content
                         */
                        $this->_compress($content, $compression);
                    }

                    echo $content;
                    exit();
                }
            }

            /**
             * Start output buffering
             */
            ob_start(array(
                &$this,
                'ob_callback'
            ));
        }
    }

    /**
     * Output buffering callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer) {
        if ($buffer != '' && w3_is_xml($buffer)) {
            $compression = false;
            $has_dynamic = $this->_has_dynamic($buffer);
            $can_cache = $this->_can_cache2($buffer);

            if ($can_cache) {
                $mobile_group = $this->_get_mobile_group();
                $referrer_group = $this->_get_referrer_group();
                $encryption = $this->_get_encryption();
                $compression = $this->_get_compression();

                /**
                 * Don't use compression for debug mode or dynamic tags
                 * because we need to modify buffer before send it to client
                 */
                $raw = ($this->_debug || $has_dynamic);

                if ($raw) {
                    $compressions = array(
                        false
                    );
                } else {
                    $compressions = $this->_get_compressions();
                }

                if ($this->_enhanced_mode) {
                    $is_404 = false;
                    $headers = array();
                } else {
                    $is_404 = (function_exists('is_404') ? is_404() : false);
                    $headers = $this->_get_cached_headers();
                }

                $time = time();
                $cache = & $this->_get_cache();

                /**
                 * Store different versions of cache
                 */
                $buffers = array();

                foreach ($compressions as $_compression) {
                    $_page_key = $this->_get_page_key($this->_request_uri, $mobile_group, $referrer_group, $encryption, $_compression);

                    /**
                     * Compress content
                     */
                    $buffers[$_compression] = $buffer;

                    $this->_compress($buffers[$_compression], $_compression);

                    /**
                     * Store cache data
                     */
                    if ($this->_enhanced_mode) {
                        $cache->set($_page_key, $buffers[$_compression]);
                    } else {
                        $_data = array(
                            '404' => $is_404,
                            'headers' => $headers,
                            'time' => $time,
                            'content' => &$buffers[$_compression]
                        );

                        $cache->set($_page_key, $_data, $this->_lifetime);
                    }
                }

                /**
                 * Change buffer if using compression
                 */
                if ($compression && isset($buffers[$compression])) {
                    $buffer = & $buffers[$compression];
                }

                /**
                 * Calculate content etag
                 */
                $etag = md5($buffer);

                /**
                 * Send headers
                 */
                $this->_send_headers($is_404, $time, $etag, $compression, $headers);

                if ($raw) {
                    if ($this->_debug) {
                        /**
                         * Set page key for debug
                         */
                        $this->_page_key = $this->_get_page_key($this->_request_uri, $mobile_group, $referrer_group, $encryption, $compression);

                        /**
                         * Append debug info
                         */
                        $time_total = w3_microtime() - $this->_time_start;
                        $debug_info = $this->_get_debug_info(true, '', false, $time_total);
                        $buffer .= "\r\n\r\n" . $debug_info;
                    }

                    /**
                     * Don't use shutdown function below
                     */
                    if (!$has_dynamic) {
                        $this->_compress($buffer, $compression);
                    }
                }
            } elseif ($this->_debug) {
                $mobile_group = $this->_get_mobile_group();
                $referrer_group = $this->_get_referrer_group();
                $encryption = $this->_get_encryption();

                /**
                 * Set page key for debug
                 */
                $this->_page_key = $this->_get_page_key($this->_request_uri, $mobile_group, $referrer_group, $encryption, $compression);

                /**
                 * Append debug info
                 */
                $time_total = w3_microtime() - $this->_time_start;
                $debug_info = $this->_get_debug_info(false, $this->cache_reject_reason, false, $time_total);
                $buffer .= "\r\n\r\n" . $debug_info;
            }

            /**
             * We can't capture output in ob_callback
             * so we use shutdown function
             */
            if ($has_dynamic) {
                $this->_shutdown_buffer = $buffer;
                $this->_shutdown_compression = $compression;

                $buffer = '';

                register_shutdown_function(array(
                    &$this,
                    'shutdown'
                ));
            }
        }

        return $buffer;
    }

    /**
     * Shutdown callback
     * @return void
     */
    function shutdown() {
        /**
         * Parse dynamic content
         */
        $this->_parse_dynamic($this->_shutdown_buffer);

        /**
         * Compress page
         */
        $this->_compress($this->_shutdown_buffer, $this->_shutdown_compression);

        echo $this->_shutdown_buffer;
    }

    /**
     * Flushes all caches
     *
     * @return boolean
     */
    function flush() {
        $cache = & $this->_get_cache();

        return $cache->flush();
    }

    /**
     * Flushes post cache
     *
     * @param integer $post_id
     * @return boolean
     */
    function flush_post($post_id = null) {
        if (!$post_id) {
            $post_id = $this->_detect_post_id();
        }

        if ($post_id) {
            $uris = array();
            $domain_url = w3_get_domain_url();
            $feeds = $this->_config->get_array('pgcache.purge.feed.types');

            if ($this->_config->get_boolean('pgcache.purge.terms') || $this->_config->get_boolean('pgcache.purge.feed.terms')) {
                $taxonomies = get_post_taxonomies($post_id);
                $terms = wp_get_post_terms($post_id, $taxonomies);
            }

            switch (true) {
                case $this->_config->get_boolean('pgcache.purge.author'):
                case $this->_config->get_boolean('pgcache.purge.archive.daily'):
                case $this->_config->get_boolean('pgcache.purge.archive.monthly'):
                case $this->_config->get_boolean('pgcache.purge.archive.yearly'):
                case $this->_config->get_boolean('pgcache.purge.feed.author'):
                    $post = get_post($post_id);
            }

            /**
             * Home URL
             */
            if ($this->_config->get_boolean('pgcache.purge.home')) {
                $home_path = w3_get_home_path();
                $site_path = w3_get_site_path();

                $uris[] = $home_path;

                if ($site_path != $home_path) {
                    $uris[] = $site_path;
                }
            }

            /**
             * Post URL
             */
            if ($this->_config->get_boolean('pgcache.purge.post')) {
                $post_link = post_permalink($post_id);
                $post_uri = str_replace($domain_url, '', $post_link);

                $uris[] = $post_uri;
            }

            /**
             * Post comments URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.comments') && function_exists('get_comments_pagenum_link')) {
                $comments_number = get_comments_number($post_id);
                $comments_per_page = get_option('comments_per_page');
                $comments_pages_number = @ceil($comments_number / $comments_per_page);

                for ($pagenum = 1; $pagenum <= $comments_pages_number; $pagenum++) {
                    $comments_pagenum_link = $this->_get_comments_pagenum_link($post_id, $pagenum);
                    $comments_pagenum_uri = str_replace($domain_url, '', $comments_pagenum_link);

                    $uris[] = $comments_pagenum_uri;
                }
            }

            /**
             * Post author URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.author') && $post) {
                $posts_number = count_user_posts($post->post_author);
                $posts_per_page = get_option('posts_per_page');
                $posts_pages_number = @ceil($posts_number / $posts_per_page);

                $author_link = get_author_link(false, $post->post_author);
                $author_uri = str_replace($domain_url, '', $author_link);

                for ($pagenum = 1; $pagenum <= $posts_pages_number; $pagenum++) {
                    $author_pagenum_link = $this->_get_pagenum_link($author_uri, $pagenum);
                    $author_pagenum_uri = str_replace($domain_url, '', $author_pagenum_link);

                    $uris[] = $author_pagenum_uri;
                }
            }

            /**
             * Post terms URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.terms')) {
                $posts_per_page = get_option('posts_per_page');

                foreach ($terms as $term) {
                    $term_link = get_term_link($term, $term->taxonomy);
                    $term_uri = str_replace($domain_url, '', $term_link);
                    $posts_pages_number = @ceil($term->count / $posts_per_page);

                    for ($pagenum = 1; $pagenum <= $posts_pages_number; $pagenum++) {
                        $term_pagenum_link = $this->_get_pagenum_link($term_uri, $pagenum);
                        $term_pagenum_uri = str_replace($domain_url, '', $term_pagenum_link);

                        $uris[] = $term_pagenum_uri;
                    }
                }
            }

            /**
             * Daily archive URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.archive.daily') && $post) {
                $post_date = strtotime($post->post_date);
                $post_year = gmdate('Y', $post_date);
                $post_month = gmdate('m', $post_date);
                $post_day = gmdate('d', $post_date);

                $posts_per_page = get_option('posts_per_page');
                $posts_number = $this->_get_archive_posts_count($post_year, $post_month, $post_day);
                $posts_pages_number = @ceil($posts_number / $posts_per_page);

                $day_link = get_day_link($post_year, $post_month, $post_day);
                $day_uri = str_replace($domain_url, '', $day_link);

                for ($pagenum = 1; $pagenum <= $posts_pages_number; $pagenum++) {
                    $day_pagenum_link = $this->_get_pagenum_link($day_uri, $pagenum);
                    $day_pagenum_uri = str_replace($domain_url, '', $day_pagenum_link);

                    $uris[] = $day_pagenum_uri;
                }
            }

            /**
             * Monthly archive URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.archive.monthly') && $post) {
                $post_date = strtotime($post->post_date);
                $post_year = gmdate('Y', $post_date);
                $post_month = gmdate('m', $post_date);

                $posts_per_page = get_option('posts_per_page');
                $posts_number = $this->_get_archive_posts_count($post_year, $post_month);
                $posts_pages_number = @ceil($posts_number / $posts_per_page);

                $month_link = get_month_link($post_year, $post_month);
                $month_uri = str_replace($domain_url, '', $month_link);

                for ($pagenum = 1; $pagenum <= $posts_pages_number; $pagenum++) {
                    $month_pagenum_link = $this->_get_pagenum_link($month_uri, $pagenum);
                    $month_pagenum_uri = str_replace($domain_url, '', $month_pagenum_link);

                    $uris[] = $month_pagenum_uri;
                }
            }

            /**
             * Yearly archive URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.archive.yearly') && $post) {
                $post_date = strtotime($post->post_date);
                $post_year = gmdate('Y', $post_date);

                $posts_per_page = get_option('posts_per_page');
                $posts_number = $this->_get_archive_posts_count($post_year);
                $posts_pages_number = @ceil($posts_number / $posts_per_page);

                $year_link = get_year_link($post_year);
                $year_uri = str_replace($domain_url, '', $year_link);

                for ($pagenum = 1; $pagenum <= $posts_pages_number; $pagenum++) {
                    $year_pagenum_link = $this->_get_pagenum_link($year_uri, $pagenum);
                    $year_pagenum_uri = str_replace($domain_url, '', $year_pagenum_link);

                    $uris[] = $year_pagenum_uri;
                }
            }

            /**
             * Feed URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.feed.blog')) {
                foreach ($feeds as $feed) {
                    $feed_link = get_feed_link($feed);
                    $feed_uri = str_replace($domain_url, '', $feed_link);

                    $uris[] = $feed_uri;
                }
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.comments')) {
                foreach ($feeds as $feed) {
                    $post_comments_feed_link = get_post_comments_feed_link($post_id, $feed);
                    $post_comments_feed_uri = str_replace($domain_url, '', $post_comments_feed_link);

                    $uris[] = $post_comments_feed_uri;
                }
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.author') && $post) {
                foreach ($feeds as $feed) {
                    $author_feed_link = get_author_feed_link($post->post_author, $feed);
                    $author_feed_uri = str_replace($domain_url, '', $author_feed_link);

                    $uris[] = $author_feed_uri;
                }
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.terms')) {
                foreach ($terms as $term) {
                    foreach ($feeds as $feed) {
                        $term_feed_link = get_term_feed_link($term->term_id, $term->taxonomy, $feed);
                        $term_feed_uri = str_replace($domain_url, '', $term_feed_link);

                        $uris[] = $term_feed_uri;
                    }
                }
            }

            /**
             * Flush cache
             */
            if (count($uris)) {
                $cache = & $this->_get_cache();
                $mobile_groups = $this->_get_mobile_groups();
                $referrer_groups = $this->_get_referrer_groups();
                $encryptions = $this->_get_encryptions();
                $compressions = $this->_get_compressions();

                foreach ($uris as $uri) {
                    foreach ($mobile_groups as $mobile_group) {
                        foreach ($referrer_groups as $referrer_group) {
                            foreach ($encryptions as $encryption) {
                                foreach ($compressions as $compression) {
                                    $page_key = $this->_get_page_key($uri, $mobile_group, $referrer_group, $encryption, $compression);

                                    $cache->delete($page_key);
                                }
                            }
                        }
                    }
                }

                /**
                 * Purge varnish servers
                 */
                if ($this->_config->get_boolean('varnish.enabled')) {
                    require_once W3TC_LIB_W3_DIR . '/Varnish.php';

                    $varnish =& W3_Varnish::instance();

                    foreach ($uris as $uri) {
                        $varnish->purge($uri);
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Returns object instance
     *
     * @return W3_PgCache
     */
    function &instance() {
        static $instances = array();

        if (!isset($instances[0])) {
            $class = __CLASS__;
            $instances[0] = & new $class();
        }

        return $instances[0];
    }

    /**
     * Checks if can we do cache logic
     *
     * @return boolean
     */
    function _can_cache() {
        /**
         * Don't cache in console mode
         */
        if (PHP_SAPI === 'cli') {
            $this->cache_reject_reason = 'Console mode';

            return false;
        }

        /**
         * Skip if session defined
         */
        if (defined('SID') && SID != '') {
            $this->cache_reject_reason = 'Session started';

            return false;
        }

        /**
         * Skip if posting
         */
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->cache_reject_reason = 'Requested method is POST';

            return false;
        }

        /**
         * Skip if there is query in the request uri
         */
        if (!$this->_config->get_boolean('pgcache.cache.query') && strstr($this->_request_uri, '?') !== false) {
            $this->cache_reject_reason = 'Requested URI contains query';

            return false;
        }

        /**
         * Check request URI
         */
        if (!in_array($_SERVER['PHP_SELF'], $this->_config->get_array('pgcache.accept.files')) && !$this->_check_request_uri()) {
            $this->cache_reject_reason = 'Requested URI is rejected';

            return false;
        }

        /**
         * Check User Agent
         */
        if (!$this->_check_ua()) {
            $this->cache_reject_reason = 'User agent is rejected';

            return false;
        }

        /**
         * Check WordPress cookies
         */
        if (!$this->_check_cookies()) {
            $this->cache_reject_reason = 'Cookie is rejected';

            return false;
        }

        /**
         * Skip if user is logged in
         */
        if ($this->_config->get_boolean('pgcache.reject.logged') && !$this->_check_logged_in()) {
            $this->cache_reject_reason = 'User is logged in';

            return false;
        }

        return true;
    }

    /**
     * Checks if can we do cache logic
     *
     * @param string $buffer
     * @return boolean
     */
    function _can_cache2(&$buffer) {
        /**
         * Skip if caching is disabled
         */
        if (!$this->_caching) {
            return false;
        }

        /**
         * Check for request URI trailing slash
         */
        if ($this->_enhanced_mode) {
            $permalink_structure = get_option('permalink_structure');
            $permalink_structure_slash = (substr($permalink_structure, -1) == '/');
            $request_uri_slash = (substr($this->_request_uri, -1) == '/');

            if ($permalink_structure_slash != $request_uri_slash) {
                if ($permalink_structure_slash) {
                    if (!$this->_check_accept_uri()) {
                        $this->cache_reject_reason = 'Requested URI doesn\'t have a trailing slash';

                        return false;
                    }
                } else {
                    $this->cache_reject_reason = 'Requested URI has a trailing slash';

                    return false;
                }
            }
        }

        /**
         * Check for database error
         */
        if (w3_is_database_error($buffer)) {
            $this->cache_reject_reason = 'Database error occurred';

            return false;
        }

        /**
         * Check for DONOTCACHEPAGE constant
         */
        if (defined('DONOTCACHEPAGE') && DONOTCACHEPAGE) {
            $this->cache_reject_reason = 'DONOTCACHEPAGE constant is defined';

            return false;
        }

        /**
         * Don't cache 404 pages
         */
        if (!$this->_config->get_boolean('pgcache.cache.404') && function_exists('is_404') && is_404()) {
            $this->cache_reject_reason = 'Page is 404';

            return false;
        }

        /**
         * Don't cache homepage
         */
        if (!$this->_config->get_boolean('pgcache.cache.home') && function_exists('is_home') && is_home()) {
            $this->cache_reject_reason = 'Page is home';

            return false;
        }

        /**
         * Don't cache feed
         */
        if (!$this->_config->get_boolean('pgcache.cache.feed') && function_exists('is_feed') && is_feed()) {
            $this->cache_reject_reason = 'Page is feed';

            return false;
        }

        /**
         * Check if page contains dynamic tags
         */
        if ($this->_enhanced_mode && $this->_has_dynamic($buffer)) {
            $this->cache_reject_reason = 'Page contains dynamic tags (mfunc or mclude) can not be cached in enhanced mode';

            return false;
        }

        return true;
    }

    /**
     * Returns cache object
     *
     * @return W3_Cache_Base
     */
    function &_get_cache() {
        static $cache = array();

        if (!isset($cache[0])) {
            $engine = $this->_config->get_string('pgcache.engine');

            switch ($engine) {
                case 'memcached':
                    $engineConfig = array(
                        'servers' => $this->_config->get_array('pgcache.memcached.servers'),
                        'persistant' => $this->_config->get_boolean('pgcache.memcached.persistant')
                    );
                    break;

                case 'file':
                    $engineConfig = array(
                        'cache_dir' => W3TC_CACHE_FILE_PGCACHE_DIR,
                        'locking' => $this->_config->get_boolean('pgcache.file.locking'),
                        'flush_timelimit' => $this->_config->get_integer('timelimit.cache_flush')
                    );
                    break;

                case 'file_generic':
                    $engineConfig = array(
                        'exclude' => array(
                            '.htaccess'
                        ),
                        'expire' => $this->_lifetime,
                        'cache_dir' => W3TC_CACHE_FILE_PGCACHE_DIR,
                        'locking' => $this->_config->get_boolean('pgcache.file.locking'),
                        'flush_timelimit' => $this->_config->get_integer('timelimit.cache_flush')
                    );
                    break;

                default:
                    $engineConfig = array();
            }

            require_once W3TC_LIB_W3_DIR . '/Cache.php';
            $cache[0] = & W3_Cache::instance($engine, $engineConfig);
        }

        return $cache[0];
    }

    /**
     * Checks request URI
     *
     * @return boolean
     */
    function _check_request_uri() {
        $auto_reject_uri = array(
            'wp-login',
            'wp-register'
        );

        foreach ($auto_reject_uri as $uri) {
            if (strstr($this->_request_uri, $uri) !== false) {
                return false;
            }
        }

        $reject_uri = $this->_config->get_array('pgcache.reject.uri');
        $reject_uri = array_map('w3_parse_path', $reject_uri);

        foreach ($reject_uri as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $this->_request_uri)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks accept URI
     *
     * @return boolean
     */
    function _check_accept_uri() {
        $accept_uri = $this->_config->get_array('pgcache.accept.uri');
        $accept_uri = array_map('w3_parse_path', $accept_uri);

        foreach ($accept_uri as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $this->_request_uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks User Agent
     *
     * @return boolean
     */
    function _check_ua() {
        $uas = array_merge($this->_config->get_array('pgcache.reject.ua'), array(
            W3TC_POWERED_BY
        ));

        foreach ($uas as $ua) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], $ua) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks WordPress cookies
     *
     * @return boolean
     */
    function _check_cookies() {
        foreach (array_keys($_COOKIE) as $cookie_name) {
            if ($cookie_name == 'wordpress_test_cookie') {
                continue;
            }
            if (preg_match('/^(wp-postpass|comment_author)/', $cookie_name)) {
                return false;
            }
        }

        foreach ($this->_config->get_array('pgcache.reject.cookie') as $reject_cookie) {
            foreach (array_keys($_COOKIE) as $cookie_name) {
                if (strstr($cookie_name, $reject_cookie) !== false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if user is logged in
     *
     * @return boolean
     */
    function _check_logged_in() {
        foreach (array_keys($_COOKIE) as $cookie_name) {
            if ($cookie_name == 'wordpress_test_cookie') {
                continue;
            }
            if (strpos($cookie_name, 'wordpress') === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Compress data
     *
     * @param string $data
     * @param string $compression
     * @return string
     */
    function _compress(&$data, $compression) {
        switch ($compression) {
            case 'gzip':
                $data = gzencode($data);
                break;

            case 'deflate':
                $data = gzdeflate($data);
                break;
        }
    }

    /**
     * Returns current mobile group
     *
     * @return string
     */
    function _get_mobile_group() {
        if ($this->_mobile) {
            return $this->_mobile->get_group();
        }

        return '';
    }

    /**
     * Returns current referrer group
     *
     * @return string
     */
    function _get_referrer_group() {
        if ($this->_referrer) {
            return $this->_referrer->get_group();
        }

        return '';
    }

    /**
     * Returns current encryption
     *
     * @return string
     */
    function _get_encryption() {
        if (w3_is_https()) {
            return 'ssl';
        }

        return '';
    }

    /**
     * Returns current compression
     *
     * @return boolean
     */
    function _get_compression() {
        if (!w3_zlib_output_compression() && !headers_sent() && !$this->_is_buggy_ie()) {
            $compressions = $this->_get_compressions();

            foreach ($compressions as $compression) {
                if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && stristr($_SERVER['HTTP_ACCEPT_ENCODING'], $compression) !== false) {
                    return $compression;
                }
            }
        }

        return false;
    }

    /**
     * Returns array of mobile groups
     *
     * @return array
     */
    function _get_mobile_groups() {
        $mobile_groups = array('');

        if ($this->_mobile) {
            $mobile_groups = array_merge($mobile_groups, array_keys($this->_mobile->groups));
        }

        return $mobile_groups;
    }

    /**
     * Returns array of referrer groups
     *
     * @return array
     */
    function _get_referrer_groups() {
        $referrer_groups = array('');

        if ($this->_referrer) {
            $referrer_groups = array_merge($referrer_groups, array_keys($this->_referrer->groups));
        }

        return $referrer_groups;
    }

    /**
     * Returns array of encryptions
     *
     * @return array
     */
    function _get_encryptions() {
        return array(false, 'ssl');
    }

    /**
     * Returns array of compressions
     *
     * @return array
     */
    function _get_compressions() {
        $compressions = array(
            false
        );

        if ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.html.compression') && function_exists('gzencode')) {
            $compressions[] = 'gzip';
        }

        return $compressions;
    }

    /**
     * Returns array of response headers
     *
     * @return array
     */
    function _get_response_headers() {
        $headers = array();

        if (function_exists('headers_list')) {
            $headers_list = headers_list();
            if ($headers_list) {
                foreach ($headers_list as $header) {
                    list ($header_name, $header_value) = explode(': ', $header, 2);
                    $headers[$header_name] = $header_value;
                }
            }
        }

        return $headers;
    }

    /**
     * Checks for buggy IE6 that doesn't support compression
     *
     * @return boolean
     */
    function _is_buggy_ie() {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];

            if (strpos($ua, 'Mozilla/4.0 (compatible; MSIE ') === 0 && strpos($ua, 'Opera') === false) {
                $version = (float) substr($ua, 30);

                return ($version < 6 || ($version == 6 && strpos($ua, 'SV1') === false));
            }
        }

        return false;
    }

    /**
     * Returns array of data headers
     *
     * @return array
     */
    function _get_cached_headers() {
        $data_headers = array();
        $cache_headers = $this->_config->get_array('pgcache.cache.headers');
        $response_headers = $this->_get_response_headers();

        foreach ($response_headers as $header_name => $header_value) {
            foreach ($cache_headers as $cache_header_name) {
                if (strcasecmp($header_name, $cache_header_name) == 0) {
                    $data_headers[$header_name] = $header_value;
                }
            }
        }

        return $data_headers;
    }

    /**
     * Returns page key
     *
     * @param string $request_uri
     * @param string $mobile_group
     * @param string $referrer_group
     * @param string $encryption
     * @param string $compression
     * @return string
     */
    function _get_page_key($request_uri, $mobile_group = '', $referrer_group = '', $encryption = false, $compression = false) {
        // replace fragment
        $key = preg_replace('~#.*$~', '', $request_uri);

        if ($this->_enhanced_mode) {
            // URL decode
            $key = urldecode($key);

            // replace double slashes
            $key = preg_replace('~[/\\\]+~', '/', $key);

            // replace query string
            $key = preg_replace('~\?.*$~', '', $key);

            // replace index.php
            $key = str_replace('/index.php', '/', $key);

            // trim slash
            $key = ltrim($key, '/');

            if ($key && substr($key, -1) != '/') {
                $key .= '/';
            }

            $key .= '_index';
        } else {
            $key = sprintf('w3tc_%s_page_%s', w3_get_host_id(), md5($key));
        }

        /**
         * Append mobile group
         */
        if ($mobile_group) {
            $key .= '_' . $mobile_group;
        }

        /**
         * Append referrer group
         */
        if ($referrer_group) {
            $key .= '_' . $referrer_group;
        }

        /**
         * Append encryption
         */
        if ($encryption) {
            $key .= '_' . $encryption;
        }

        if ($this->_enhanced_mode) {
            /**
             * Append HTML extension
             */
            $key .= '.html';

            /**
             * Append compression extension
             */
            if ($compression) {
                $key .= '.' . $compression;
            }
        } else {
            /**
             * Append compression
             */
            if ($compression) {
                $key .= '_' . $compression;
            }
        }

        /**
         * Allow to modify page key by W3TC plugins
         */
        $key = w3tc_do_action('w3tc_pgcache_cache_key', $key);

        return $key;
    }

    /**
     * Detects post ID
     *
     * @return integer
     */
    function _detect_post_id() {
        global $posts, $comment_post_ID, $post_ID;

        if ($post_ID) {
            return $post_ID;
        } elseif ($comment_post_ID) {
            return $comment_post_ID;
        } elseif (is_single() || is_page() && count($posts)) {
            return $posts[0]->ID;
        } elseif (isset($_REQUEST['p'])) {
            return (integer) $_REQUEST['p'];
        }

        return 0;
    }

    /**
     * Returns debug info
     *
     * @param boolean $cache
     * @param string $reason
     * @param boolean $status
     * @param double $time
     * @return string
     */
    function _get_debug_info($cache, $reason, $status, $time) {
        $debug_info = "<!-- W3 Total Cache: Page cache debug info:\r\n";
        $debug_info .= sprintf("%s%s\r\n", str_pad('Engine: ', 20), w3_get_engine_name($this->_config->get_string('pgcache.engine')));
        $debug_info .= sprintf("%s%s\r\n", str_pad('Cache key: ', 20), $this->_page_key);
        $debug_info .= sprintf("%s%s\r\n", str_pad('Caching: ', 20), ($cache ? 'enabled' : 'disabled'));

        if (!$cache) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Reject reason: ', 20), $reason);
        }

        $debug_info .= sprintf("%s%s\r\n", str_pad('Status: ', 20), ($status ? 'cached' : 'not cached'));
        $debug_info .= sprintf("%s%.3fs\r\n", str_pad('Creation Time: ', 20), $time);

        $headers = $this->_get_response_headers();

        if (count($headers)) {
            $debug_info .= "Header info:\r\n";

            foreach ($headers as $header_name => $header_value) {
                $debug_info .= sprintf("%s%s\r\n", str_pad($header_name . ': ', 20), w3_escape_comment($header_value));
            }
        }

        $debug_info .= '-->';

        return $debug_info;
    }

    /**
     * Sends headers
     *
     * @param array $headers
     * @return boolean
     */
    function _headers($headers) {
        if (!headers_sent()) {
            foreach ($headers as $name => $value) {
                $header = ($name == 'Status' ? $value : $name . ': ' . $value);
                @header($header);
            }

            return true;
        }

        return false;
    }

    /**
     * Sends headers
     * @param boolean $is_404
     * @param string $etag
     * @param integer $time
     * @param string $compression
     * @param array $custom_headers
     * @return boolean
     */
    function _send_headers($is_404, $time, $etag, $compression, $custom_headers = array()) {
        $exit = false;
        $headers = array();
        $curr_time = time();
        $expires = $time + $this->_lifetime;
        $max_age = ($expires > $curr_time ? $expires - $curr_time : 0);

        if ($is_404) {
            /**
             * Add 404 header
             */
            $headers = array_merge($headers, array(
                'Status' => 'HTTP/1.1 404 Not Found'
            ));
        } elseif ($this->_check_modified_since($time) || $this->_check_match($etag)) {
            /**
             * Add 304 header
             */
            $headers = array_merge($headers, array(
                'Status' => 'HTTP/1.1 304 Not Modified'
            ));

            /**
             * Don't send content if it isn't modified
             */
            $exit = true;
        }

        /**
         * Add default headers
         */
        $headers = array_merge($headers, array(
            'Last-Modified' => w3_http_date($time),
            'Vary' => 'Cookie'
        ));

        if ($this->_config->get_boolean('browsercache.enabled')) {
            if ($this->_config->get_boolean('browsercache.html.expires')) {
                $headers = array_merge($headers, array(
                    'Expires' => w3_http_date($expires)
                ));
            }

            if ($this->_config->get_boolean('browsercache.html.cache.control')) {
                switch ($this->_config->get_string('browsercache.html.cache.policy')) {
                    case 'cache':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'public',
                            'Cache-Control' => 'public'
                        ));
                        break;

                    case 'cache_validation':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'public',
                            'Cache-Control' => 'public, must-revalidate, proxy-revalidate'
                        ));
                        break;

                    case 'cache_noproxy':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'public',
                            'Cache-Control' => 'public, must-revalidate'
                        ));
                        break;

                    case 'cache_maxage':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'public',
                            'Cache-Control' => sprintf('max-age=%d, public, must-revalidate, proxy-revalidate', $max_age)
                        ));
                        break;

                    case 'no_cache':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'no-cache',
                            'Cache-Control' => 'max-age=0, private, no-store, no-cache, must-revalidate'
                        ));
                        break;
                }
            }

            if ($this->_config->get_boolean('browsercache.html.etag')) {
                $headers = array_merge($headers, array(
                    'Etag' => $etag
                ));
            }

            if ($this->_config->get_boolean('browsercache.html.w3tc')) {
                $headers = array_merge($headers, array(
                    'X-Powered-By' => W3TC_POWERED_BY
                ));
            }
        }

        if ($compression) {
            /**
             * Add Content-Encoding header
             */
            $headers = array_merge($headers, array(
                'Vary' => 'Accept-Encoding, Cookie',
                'Content-Encoding' => $compression
            ));
        }

        /**
         * Add custom headers
         */
        $headers = array_merge($headers, $custom_headers);

        /**
         * Disable caching for preview mode
         */
        if (w3_is_preview_mode()) {
            $headers = array_merge($headers, array(
                'Pragma' => 'private',
                'Cache-Control' => 'private'
            ));
        }

        /**
         * Send headers to client
         */
        $result = $this->_headers($headers);

        if ($exit) {
            exit();
        }

        return $result;
    }

    /**
     * Check if content was modified by time
     * @param integer $time
     * @return boolean
     */
    function _check_modified_since($time) {
        if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $if_modified_since = $_SERVER['HTTP_IF_MODIFIED_SINCE'];

            // IE has tacked on extra data to this header, strip it
            if (($semicolon = strrpos($if_modified_since, ';')) !== false) {
                $if_modified_since = substr($if_modified_since, 0, $semicolon);
            }

            return ($time == strtotime($if_modified_since));
        }

        return false;
    }

    /**
     * Check if content was modified by etag
     * @param string $etag
     * @return boolean
     */
    function _check_match($etag) {
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
            $if_none_match = (get_magic_quotes_gpc() ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : $_SERVER['HTTP_IF_NONE_MATCH']);
            $client_etags = explode(',', $if_none_match);

            foreach ($client_etags as $client_etag) {
                $client_etag = trim($client_etag);

                if ($etag == $client_etag) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Bad Behavior support
     * @return void
     */
    function _bad_behavior() {
        if (file_exists(WP_CONTENT_DIR . '/plugins/bad-behavior/bad-behavior-generic.php')) {
            $bb_file = WP_CONTENT_DIR . '/plugins/bad-behavior/bad-behavior-generic.php';
        } elseif (file_exists(WP_CONTENT_DIR . '/plugins/Bad-Behavior/bad-behavior-generic.php')) {
            $bb_file = WP_CONTENT_DIR . '/plugins/Bad-Behavior/bad-behavior-generic.php';
        } else {
            $bb_file = false;
        }

        if ($bb_file) {
            require_once $bb_file;
        }
    }

    /**
     * Workaround for get_pagenum_link function
     *
     * @param string $url
     * @param int $pagenum
     * @return string
     */
    function _get_pagenum_link($url, $pagenum = 1) {
        $request_uri = $_SERVER['REQUEST_URI'];
        $_SERVER['REQUEST_URI'] = $url;

        $link = get_pagenum_link($pagenum);

        $_SERVER['REQUEST_URI'] = $request_uri;

        return $link;
    }

    /**
     * Workaround for get_comments_pagenum_link function
     *
     * @param integer $post_id
     * @return string
     */
    function _get_comments_pagenum_link($post_id, $pagenum = 1, $max_page = 0) {
        if (isset($GLOBALS['post']) && is_object($GLOBALS['post'])) {
            $old_post = &$GLOBALS['post'];
        } else {
            $GLOBALS['post'] = & new stdClass();
            $old_post = null;
        }

        $GLOBALS['post']->ID = $post_id;

        $link = get_comments_pagenum_link($pagenum, $max_page);

        if ($old_post) {
            $GLOBALS['post'] = &$old_post;
        }

        return $link;
    }

    /**
     * Parses dynamic tags
     * @return string
     */
    function _parse_dynamic(&$buffer) {
        $buffer = preg_replace_callback('~<!--\s*mfunc(.*)-->(.*)<!--\s*/mfunc\s*-->~Uis', array(
            &$this,
            '_parse_dynamic_mfunc'
        ), $buffer);

        $buffer = preg_replace_callback('~<!--\s*mclude(.*)-->(.*)<!--\s*/mclude\s*-->~Uis', array(
            &$this,
            '_parse_dynamic_mclude'
        ), $buffer);
    }

    /**
     * Parse dynamic mfunc callback
     * @param array $matches
     * @return string
     */
    function _parse_dynamic_mfunc($matches) {
        $code1 = trim($matches[1]);
        $code2 = trim($matches[2]);
        $code = ($code1 ? $code1 : $code2);

        if ($code) {
            $code = trim($code, ';') . ';';

            ob_start();
            $result = eval($code);
            $output = ob_get_contents();
            ob_end_clean();

            if ($result === false) {
                $output = sprintf('Unable to execute code: %s', htmlspecialchars($code));
            }
        } else {
            $output = htmlspecialchars('Invalid mfunc tag syntax. The correct format is: <!-- mfunc PHP code --><!-- /mfunc --> or <!-- mfunc -->PHP code<!-- /mfunc -->.');
        }

        return $output;
    }

    /**
     * Parse dynamic mclude callback
     * @param array $matches
     * @return string
     */
    function _parse_dynamic_mclude($matches) {
        $file1 = trim($matches[1]);
        $file2 = trim($matches[2]);

        $file = ($file1 ? $file1 : $file2);

        if ($file) {
            $file = ABSPATH . $file;

            if (file_exists($file) && is_readable($file)) {
                ob_start();
                include $file;
                $output = ob_get_contents();
                ob_end_clean();
            } else {
                $output = sprintf('Unable to open file: %s', htmlspecialchars($file));
            }
        } else {
            $output = htmlspecialchars('Incorrect mclude tag syntax. The correct format is: <!-- mclude path/to/file.php --><!-- /mclude --> or <!-- mclude -->path/to/file.php<!-- /mclude -->.');
        }

        return $output;
    }

    /**
     * Checks if buffer has dynamic tags
     *
     * @param string $buffer
     * @return boolean
     */
    function _has_dynamic(&$buffer) {
        return preg_match('~<!--\s*m(func|clude)(.*)-->(.*)<!--\s*/m(func|clude)\s*-->~Uis', $buffer);
    }

    /**
     * Returns number of posts in the archive
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     */
    function _get_archive_posts_count($year = 0, $month = 0, $day = 0) {
        global $wpdb;

        $filters = array(
            'post_type = "post"',
            'post_status = "publish"'
        );

        if ($year) {
            $filters[] = sprintf('YEAR(post_date) = %d', $year);
        }

        if ($month) {
            $filters[] = sprintf('MONTH(post_date) = %d', $month);
        }

        if ($day) {
            $filters[] = sprintf('DAY(post_date) = %d', $day);
        }

        $where = implode(' AND ', $filters);

        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $wpdb->posts, $where);

        $count = (int) $wpdb->get_var($sql);

        return $count;
    }
}
