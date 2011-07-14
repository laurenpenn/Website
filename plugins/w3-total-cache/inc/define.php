<?php

define('W3TC_VERSION', '0.9.2.3');
define('W3TC_POWERED_BY', 'W3 Total Cache/' . W3TC_VERSION);
define('W3TC_EMAIL', 'w3tc@w3-edge.com');
define('W3TC_PAYPAL_URL', 'https://www.paypal.com/cgi-bin/webscr');
define('W3TC_PAYPAL_BUSINESS', 'w3tc-team@w3-edge.com');
define('W3TC_LINK_URL', 'http://www.w3-edge.com/wordpress-plugins/');
define('W3TC_LINK_NAME', 'WordPress Plugins');
define('W3TC_FEED_URL', 'http://feeds.feedburner.com/W3TOTALCACHE');
define('W3TC_README_URL', 'http://plugins.trac.wordpress.org/browser/w3-total-cache/trunk/readme.txt?format=txt');
define('W3TC_SUPPORT_US_TIMEOUT', 2592000);

define('W3TC_PHP5', PHP_VERSION >= 5);
define('W3TC_WIN', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

defined('W3TC_DIR') || define('W3TC_DIR', realpath(dirname(__FILE__) . '/..'));
define('W3TC_FILE', 'w3-total-cache/w3-total-cache.php');
define('W3TC_LIB_DIR', W3TC_DIR . '/lib');
define('W3TC_LIB_W3_DIR', W3TC_LIB_DIR . '/W3');
define('W3TC_LIB_MINIFY_DIR', W3TC_LIB_DIR . '/Minify');
define('W3TC_LIB_CF_DIR', W3TC_LIB_DIR . '/CF');
define('W3TC_LIB_CSSTIDY_DIR', W3TC_LIB_DIR . '/CSSTidy');
define('W3TC_LIB_MICROSOFT_DIR', W3TC_LIB_DIR . '/Microsoft');
define('W3TC_LIB_NUSOAP_DIR', W3TC_LIB_DIR . '/Nusoap');
define('W3TC_PLUGINS_DIR', W3TC_DIR . '/plugins');
define('W3TC_DB_DIR', W3TC_DIR . '/db');
define('W3TC_INSTALL_DIR', W3TC_DIR . '/wp-content');
define('W3TC_INSTALL_MINIFY_DIR', W3TC_INSTALL_DIR . '/w3tc/min');

define('W3TC_BLOGNAMES_PATH', WP_CONTENT_DIR . '/w3-total-cache-blognames.php');
define('W3TC_BLOGNAME', w3_get_blogname());
define('W3TC_SUFFIX', (W3TC_BLOGNAME != '' ? '-' . W3TC_BLOGNAME : ''));

defined('WP_CONTENT_DIR') || define('WP_CONTENT_DIR', realpath(W3TC_DIR . '/../..'));
define('WP_CONTENT_DIR_PATH', dirname(WP_CONTENT_DIR));
define('WP_CONTENT_DIR_NAME', basename(WP_CONTENT_DIR));
define('W3TC_CONTENT_DIR_NAME', WP_CONTENT_DIR_NAME . '/w3tc' . W3TC_SUFFIX);
define('W3TC_CONTENT_DIR', WP_CONTENT_DIR_PATH . '/' . W3TC_CONTENT_DIR_NAME);
define('W3TC_CONTENT_MINIFY_DIR_NAME', W3TC_CONTENT_DIR_NAME . '/min');
define('W3TC_CONTENT_MINIFY_DIR', WP_CONTENT_DIR_PATH . '/' . W3TC_CONTENT_DIR_NAME . '/min');
define('W3TC_CACHE_FILE_DBCACHE_DIR', W3TC_CONTENT_DIR . '/dbcache');
define('W3TC_CACHE_FILE_OBJECTCACHE_DIR', W3TC_CONTENT_DIR . '/objectcache');
define('W3TC_CACHE_FILE_PGCACHE_DIR', W3TC_CONTENT_DIR . '/pgcache');
define('W3TC_CACHE_FILE_MINIFY_DIR', W3TC_CONTENT_DIR . '/min');
define('W3TC_LOG_DIR', W3TC_CONTENT_DIR . '/log');
define('W3TC_TMP_DIR', W3TC_CONTENT_DIR . '/tmp');
define('W3TC_CONFIG_PATH', WP_CONTENT_DIR . '/w3-total-cache-config' . W3TC_SUFFIX . '.php');
define('W3TC_CONFIG_PREVIEW_PATH', WP_CONTENT_DIR . '/w3-total-cache-config' . W3TC_SUFFIX . '-preview.php');
define('W3TC_CONFIG_MASTER_PATH', WP_CONTENT_DIR . '/w3-total-cache-config.php');
define('W3TC_MINIFY_LOG_FILE', W3TC_LOG_DIR . '/minify.log');
define('W3TC_CDN_COMMAND_UPLOAD', 1);
define('W3TC_CDN_COMMAND_DELETE', 2);
define('W3TC_CDN_COMMAND_PURGE', 3);
define('W3TC_CDN_TABLE_QUEUE', 'w3tc_cdn_queue');
define('W3TC_CDN_LOG_FILE', W3TC_LOG_DIR . '/cdn.log');
define('W3TC_VARNISH_LOG_FILE', W3TC_LOG_DIR . '/varnish.log');

define('W3TC_MARKER_BEGIN_WORDPRESS', '# BEGIN WordPress');
define('W3TC_MARKER_BEGIN_PGCACHE_CORE', '# BEGIN W3TC Page Cache core');
define('W3TC_MARKER_BEGIN_PGCACHE_CACHE', '# BEGIN W3TC Page Cache cache');
define('W3TC_MARKER_BEGIN_PGCACHE_LEGACY', '# BEGIN W3TC Page Cache');
define('W3TC_MARKER_BEGIN_PGCACHE_WPSC', '# BEGIN WPSuperCache');
define('W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE', '# BEGIN W3TC Browser Cache');
define('W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP', '# BEGIN W3TC Skip 404 error handling by WordPress for static files');
define('W3TC_MARKER_BEGIN_MINIFY_CORE', '# BEGIN W3TC Minify core');
define('W3TC_MARKER_BEGIN_MINIFY_CACHE', '# BEGIN W3TC Minify cache');
define('W3TC_MARKER_BEGIN_MINIFY_LEGACY', '# BEGIN W3TC Minify');

define('W3TC_MARKER_END_WORDPRESS', '# END WordPress');
define('W3TC_MARKER_END_PGCACHE_CORE', '# END W3TC Page Cache core');
define('W3TC_MARKER_END_PGCACHE_CACHE', '# END W3TC Page Cache cache');
define('W3TC_MARKER_END_PGCACHE_LEGACY', '# END W3TC Page Cache');
define('W3TC_MARKER_END_PGCACHE_WPSC', '# END WPSuperCache');
define('W3TC_MARKER_END_BROWSERCACHE_CACHE', '# END W3TC Browser Cache');
define('W3TC_MARKER_END_BROWSERCACHE_NO404WP', '# END W3TC Skip 404 error handling by WordPress for static files');
define('W3TC_MARKER_END_MINIFY_CORE', '# END W3TC Minify core');
define('W3TC_MARKER_END_MINIFY_CACHE', '# END W3TC Minify cache');
define('W3TC_MARKER_END_MINIFY_LEGACY', '# END W3TC Minify');

define('W3TC_INSTALL_FILE_ADVANCED_CACHE', W3TC_INSTALL_DIR . '/advanced-cache.php');
define('W3TC_INSTALL_FILE_DB', W3TC_INSTALL_DIR . '/db.php');
define('W3TC_INSTALL_FILE_OBJECT_CACHE', W3TC_INSTALL_DIR . '/object-cache.php');

define('W3TC_ADDIN_FILE_ADVANCED_CACHE', WP_CONTENT_DIR . '/advanced-cache.php');
define('W3TC_ADDIN_FILE_DB', WP_CONTENT_DIR . '/db.php');
define('W3TC_ADDIN_FILE_OBJECT_CACHE', WP_CONTENT_DIR . '/object-cache.php');

require_once W3TC_DIR . '/inc/compat.php';
require_once W3TC_DIR . '/inc/plugin.php';

@ini_set('pcre.backtrack_limit', 4194304);
@ini_set('pcre.recursion_limit', 4194304);

/**
 * Deactivate plugin after activation error
 *
 * @return void
 */
function w3_activation_cleanup() {
    $active_plugins = (array) get_option('active_plugins');
    $active_plugins_network = (array) get_site_option('active_sitewide_plugins');

    // workaround for WPMU deactivation bug
    remove_action('deactivate_' . W3TC_FILE, 'deactivate_sitewide_plugin');

    do_action('deactivate_plugin', W3TC_FILE);

    $key = array_search(W3TC_FILE, $active_plugins);

    if ($key !== false) {
        array_splice($active_plugins, $key, 1);
    }

    unset($active_plugins_network[W3TC_FILE]);

    do_action('deactivate_' . W3TC_FILE);
    do_action('deactivated_plugin', W3TC_FILE);

    update_option('active_plugins', $active_plugins);
    update_site_option('active_sitewide_plugins', $active_plugins_network);
}

/**
 * W3 activate error
 *
 * @param string $error
 * @return void
 */
function w3_activate_error($error) {
    w3_activation_cleanup();

    include W3TC_DIR . '/inc/error.phtml';
    exit();
}

/**
 * W3 writable error
 *
 * @param string $path
 * @return string
 */
function w3_writable_error($path) {
    $activate_url = wp_nonce_url('plugins.php?action=activate&plugin=' . W3TC_FILE, 'activate-plugin_' . W3TC_FILE);
    $reactivate_button = sprintf('<input type="button" value="re-activate plugin" onclick="top.location.href = \'%s\'" />', addslashes($activate_url));

    if (w3_check_open_basedir($path)) {
        $error = sprintf('<strong>%s</strong> could not be created, please run following command:<br /><strong style="color: #f00;">chmod 777 %s</strong><br />then %s.', $path, (file_exists($path) ? $path : dirname($path)), $reactivate_button);
    } else {
        $error = sprintf('<strong>%s</strong> could not be created, <strong>open_basedir</strong> restriction in effect, please check your php.ini settings:<br /><strong style="color: #f00;">open_basedir = "%s"</strong><br />then %s.', $path, ini_get('open_basedir'), $reactivate_button);
    }

    w3_activate_error($error);
}

/**
 * W3 Network activation error
 *
 * @return void
 */
function w3_network_activate_error() {
    w3_activation_cleanup();
    wp_redirect(plugins_url('inc/network_activation.php', W3TC_FILE));

    echo '<p><strong>W3 Total Cache Error:</strong> plugin cannot be activated network-wide.</p>';
    echo '<p><a href="javascript:history.back(-1);">Back</a>';
    exit();
}

/**
 * Returns current microtime
 *
 * @return double
 */
function w3_microtime() {
    list ($usec, $sec) = explode(' ', microtime());

    return ((double) $usec + (double) $sec);
}

/**
 * Recursive creates directory
 *
 * @param string $path
 * @param integer $mask
 * @param string $curr_path
 * @return boolean
 */
function w3_mkdir($path, $mask = 0777, $curr_path = '') {
    $path = w3_realpath($path);
    $path = trim($path, '/');
    $dirs = explode('/', $path);

    foreach ($dirs as $dir) {
        if ($dir == '') {
            return false;
        }

        $curr_path .= ($curr_path == '' ? '' : '/') . $dir;

        if (!@is_dir($curr_path)) {
            if (!@mkdir($curr_path, $mask)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Recursive remove dir
 *
 * @param string $path
 * @param array $exclude
 * @param bool $remove
 * @return void
 */
function w3_rmdir($path, $exclude = array(), $remove = true) {
    $dir = @opendir($path);

    if ($dir) {
        while (($entry = @readdir($dir)) !== false) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            foreach ($exclude as $mask) {
                if (fnmatch($mask, basename($entry))) {
                    continue 2;
                }
            }

            $full_path = $path . DIRECTORY_SEPARATOR . $entry;

            if (@is_dir($full_path)) {
                w3_rmdir($full_path, $exclude);
            } else {
                @unlink($full_path);
            }
        }

        @closedir($dir);

        if ($remove) {
            @rmdir($path);
        }
    }
}

/**
 * Recursive empty dir
 *
 * @param string $path
 * @param array $exclude
 * @return void
 */
function w3_emptydir($path, $exclude = array()) {
    w3_rmdir($path, $exclude, false);
}

/**
 * Check if content is HTML or XML
 *
 * @param string $content
 * @return boolean
 */
function w3_is_xml($content) {
    if (strlen($content) > 1000) {
        $content = substr($content, 0, 1000);
    }

    if (strstr($content, '<!--') !== false) {
        $content = preg_replace('~<!--.*?-->~s', '', $content);
    }

    $content = ltrim($content);

    return (stripos($content, '<?xml') === 0 || stripos($content, '<html') === 0 || stripos($content, '<!DOCTYPE') === 0);
}

/**
 * Returns true if it's WPMU
 *
 * @return boolean
 */
function w3_is_wpmu() {
    static $wpmu = null;

    if ($wpmu === null) {
        $wpmu = file_exists(ABSPATH . 'wpmu-settings.php');
    }

    return $wpmu;
}

/**
 * Returns true if WPMU uses vhosts
 *
 * @return boolean
 */
function w3_is_subdomain_install() {
    return ((defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) || (defined('VHOST') && VHOST == 'yes'));
}

/**
 * Returns true if it's WP with enabled Network mode
 *
 * @return boolean
 */
function w3_is_multisite() {
    static $multisite = null;

    if ($multisite === null) {
        $multisite = ((defined('MULTISITE') && MULTISITE) || defined('SUNRISE') || w3_is_subdomain_install());
    }

    return $multisite;
}

/**
 * Returns if there is multisite mode
 *
 * @return boolean
 */
function w3_is_network() {
    return (w3_is_wpmu() || w3_is_multisite());
}

/**
 * Check if URL is valid
 *
 * @param string $url
 * @return boolean
 */
function w3_is_url($url) {
    return preg_match('~^(https?:)?//~', $url);
}

/**
 * Returns true if current connection is secure
 *
 * @return boolean
 */
function w3_is_https() {
    switch (true) {
        case (isset($_SERVER['HTTPS']) && w3_to_boolean($_SERVER['HTTPS'])):
        case (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] == 443):
            return true;
    }

    return false;
}

/**
 * Check if WP permalink directives exists
 *
 * @return boolean
 */
function w3_is_permalink_rules() {
    if (w3_is_apache() && !w3_is_network()) {
        $path = w3_get_home_root() . '/.htaccess';

        return (($data = @file_get_contents($path)) && strstr($data, W3TC_MARKER_BEGIN_WORDPRESS) !== false);
    }

    return true;
}

/**
 * Check if there was database error
 *
 * @param string $content
 * @return boolean
 */
function w3_is_database_error(&$content) {
    return (stristr($content, '<title>Database Error</title>') !== false);
}

/**
 * Returns true if preview config exists
 *
 * @return boolean
 */
function w3_is_preview_config() {
    return file_exists(W3TC_CONFIG_PREVIEW_PATH);
}

/**
 * Retuns true if preview settings active
 *
 * @return boolean
 */
function w3_is_preview_mode() {
    return (w3_is_preview_config() && (defined('WP_ADMIN') || isset($_REQUEST['w3tc_preview']) || (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], 'w3tc_preview') !== false)));
}

/**
 * Check if file is write-able
 *
 * @param string $file
 * @return boolean
 */
function w3_is_writable($file) {
    $exists = file_exists($file);

    $fp = @fopen($file, 'a');

    if ($fp) {
        fclose($fp);

        if (!$exists) {
            @unlink($file);
        }

        return true;
    }

    return false;
}

/**
 * Cehck if dir is write-able
 *
 * @param string $dir
 * @return boolean
 */
function w3_is_writable_dir($dir) {
    $file = $dir . '/' . uniqid(mt_rand()) . '.tmp';

    return w3_is_writable($file);
}

/**
 * Returns true if server is Apache
 *
 * @return boolean
 */
function w3_is_apache() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'apache') !== false);
}

/**
 * Returns true if server is nginx
 *
 * @return boolean
 */
function w3_is_nginx() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false);
}

/**
 * Returns true if CDN engine is mirror
 *
 * @param string $engine
 * @return bool
 */
function w3_is_cdn_mirror($engine) {
    return in_array($engine, array('mirror', 'netdna', 'cotendo', 'cf2'));
}

/**
 * Returns domain from host
 *
 * @param string $host
 * @return string
 */
function w3_get_domain($host) {
    $host = strtolower($host);

    if (strpos($host, 'www.') === 0) {
        $host = substr($host, 4);
    }

    if (($pos = strpos($host, ':')) !== false) {
        $host = substr($host, 0, $pos);
    }

    $host = rtrim($host, '.');

    return $host;
}

/**
 * Returns array of all available blognames
 *
 * @return array
 */
function w3_get_blognames() {
    global $wpdb;

    $blognames = array();

    $sql = sprintf('SELECT domain, path FROM %s', $wpdb->blogs);
    $blogs = $wpdb->get_results($sql);

    if ($blogs) {
        $base_path = w3_get_base_path();

        foreach ($blogs as $blog) {
            $blogname = trim(str_replace($base_path, '', $blog->path), '/');

            if ($blogname) {
                $blognames[] = $blogname;
            }
        }
    }

    return $blognames;
}

/**
 * Load blognames from file
 *
 * @return array
 */
function w3_load_blognames() {
    $blognames = include W3TC_BLOGNAMES_PATH;

    return $blognames;
}

/**
 * Save blognames into file
 *
 * @param string $blognames
 * @return boolean
 */
function w3_save_blognames($blognames = null) {
    if (!$blognames) {
        $blognames = w3_get_blognames();
    }

    $strings = array();

    foreach ($blognames as $blogname) {
        $strings[] = sprintf("'%s'", addslashes($blogname));
    }

    $data = sprintf('<?php return array(%s);', implode(', ', $strings));

    return @file_put_contents(W3TC_BLOGNAMES_PATH, $data);
}

/**
 * Detect WPMU blogname
 *
 * @return string
 */
function w3_get_blogname() {
    static $blogname = null;

    if ($blogname === null) {
        if (w3_is_network()) {
            $host = w3_get_host();
            $domain = w3_get_domain($host);

            if (w3_is_subdomain_install()) {
                $blogname = $domain;
            } else {
                $uri = $_SERVER['REQUEST_URI'];
                $base_path = w3_get_base_path();

                if ($base_path != '' && strpos($uri, $base_path) === 0) {
                    $uri = substr_replace($uri, '/', 0, strlen($base_path));
                }

                $blogname = w3_get_blogname_from_uri($uri);

                if ($blogname != '') {
                    $blogname = $blogname . '.' . $domain;
                } else {
                    $blogname = $domain;
                }
            }
        } else {
            $blogname = '';
        }
    }

    return $blogname;
}

/**
 * Returns blogname from URI
 *
 * @param string $uri
 * @return string
 */
function w3_get_blogname_from_uri($uri) {
    $blogname = '';
    $matches = null;
    $uri = strtolower($uri);

    if (preg_match('~^/([a-z0-9-]+)/~', $uri, $matches)) {
        if (file_exists(W3TC_BLOGNAMES_PATH)) {
            // Get blognames from cache
            $blognames = w3_load_blognames();
        } elseif (isset($GLOBALS['wpdb'])) {
            // Get blognames from DB
            $blognames = w3_get_blognames();
        } else {
            $blognames = array();
        }

        if (is_array($blognames) && in_array($matches[1], $blognames)) {
            $blogname = $matches[1];
        }
    }

    return $blogname;
}

/**
 * Returns current blog ID
 *
 * @return integer
 */
function w3_get_blog_id() {
    return (isset($GLOBALS['blog_id']) ? (int) $GLOBALS['blog_id'] : 0);
}

/**
 * Returns URL regexp from URL
 *
 * @param string $url
 * @return string
 */
function w3_get_url_regexp($url) {
    $url = preg_replace('~(https?:)?//~i', '', $url);
    $url = preg_replace('~^www\.~i', '', $url);

    $regexp = '(https?:)?//(www\.)?' . w3_preg_quote($url);

    return $regexp;
}

/**
 * Returns SSL URL if current connection is https
 * @param string $url
 * @return string
 */
function w3_get_url_ssl($url) {
    if (w3_is_https()) {
        $url = str_replace('http://', 'https://', $url);
    }

    return $url;
}

/**
 * Get domain URL
 *
 * @return string
 */

function w3_get_domain_url() {
    $home_url = w3_get_home_url();
    $parse_url = @parse_url($home_url);

    if ($parse_url && isset($parse_url['scheme']) && isset($parse_url['host'])) {
        $scheme = $parse_url['scheme'];
        $host = $parse_url['host'];
        $port = (isset($parse_url['port']) && $parse_url['port'] != 80 ? ':' . (int) $parse_url['port'] : '');
        $domain_url = sprintf('%s://%s%s', $scheme, $host, $port);

        return $domain_url;
    }

    return false;
}

/**
 * Returns domain url regexp
 *
 * @return string
 */
function w3_get_domain_url_regexp() {
    $domain_url = w3_get_domain_url();
    $regexp = w3_get_url_regexp($domain_url);

    return $regexp;
}

/**
 * Returns home URL
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_home_url() {
    static $home_url = null;

    if ($home_url === null) {
        $home_url = get_option('home');
        $home_url = rtrim($home_url, '/');
    }

    return $home_url;
}

/**
 * Returns SSL home url
 *
 * @return string
 */
function w3_get_home_url_ssl() {
    $home_url = w3_get_home_url();
    $ssl = w3_get_url_ssl($home_url);

    return $ssl;
}

/**
 * Returns home url regexp
 *
 * @return string
 */
function w3_get_home_url_regexp() {
    $home_url = w3_get_home_url();
    $regexp = w3_get_url_regexp($home_url);

    return $regexp;
}

/**
 * Returns site URL
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_site_url() {
    static $site_url = null;

    if ($site_url === null) {
        $site_url = get_option('siteurl');
        $site_url = rtrim($site_url, '/');
    }

    return $site_url;
}

/**
 * Returns SSL site URL
 *
 * @return string
 */
function w3_get_site_url_ssl() {
    $site_url = w3_get_site_url();
    $ssl = w3_get_url_ssl($site_url);

    return $ssl;

}

/**
 * Returns absolute path to document root
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_document_root() {
    static $document_root = null;

    if ($document_root === null) {
        if (!empty($_SERVER['SCRIPT_FILENAME'])) {
            $document_root = substr(w3_path($_SERVER['SCRIPT_FILENAME']), 0, -strlen(w3_path($_SERVER['PHP_SELF'])));
        } elseif (!empty($_SERVER['PATH_TRANSLATED'])) {
            $document_root = substr(w3_path($_SERVER['PATH_TRANSLATED']), 0, -strlen(w3_path($_SERVER['PHP_SELF'])));
        } elseif (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $document_root = w3_path($_SERVER['DOCUMENT_ROOT']);
        } else {
            $document_root = w3_get_site_root();
        }

        $document_root = realpath($document_root);
        $document_root = w3_path($document_root);
    }

    return $document_root;
}

/**
 * Returns absolute path to home directory
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com
 * Install dir=/var/www/vhosts/domain.com/site/blog
 * home=http://domain.com/site
 * siteurl=http://domain.com/site/blog
 * return /var/www/vhosts/domain.com/site
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_home_root() {
    if (w3_is_network()) {
        $path = w3_get_base_path();
    } else {
        $path = w3_get_home_path();
    }

    $home_root = w3_get_document_root() . $path;
    $home_root = realpath($home_root);
    $home_root = w3_path($home_root);

    return $home_root;
}

/**
 * Returns absolute path to blog install dir
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com
 * install dir=/var/www/vhosts/domain.com/site/blog
 * return /var/www/vhosts/domain.com/site/blog
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_site_root() {
    $site_root = ABSPATH;
    $site_root = realpath($site_root);
    $site_root = w3_path($site_root);

    return $site_root;
}

/**
 * Returns blog path
 *
 * Example:
 *
 * siteurl=http://domain.com/site/blog
 * return /site/blog/
 *
 * With trailing slash!
 *
 * @return string
 */
function w3_get_site_path() {
    $site_url = w3_get_site_url();
    $parse_url = @parse_url($site_url);

    if ($parse_url && isset($parse_url['path'])) {
        $site_path = '/' . ltrim($parse_url['path'], '/');
    } else {
        $site_path = '/';
    }

    if (substr($site_path, -1) != '/') {
        $site_path .= '/';
    }

    return $site_path;
}

/**
 * Returns home path
 *
 * Example:
 *
 * home=http://domain.com/site/
 * siteurl=http://domain.com/site/blog
 * return /site/
 *
 * With trailing slash!
 *
 * @return string
 */
function w3_get_home_path() {
    $home_url = w3_get_home_url();
    $parse_url = @parse_url($home_url);

    if ($parse_url && isset($parse_url['path'])) {
        $home_path = '/' . ltrim($parse_url['path'], '/');
    } else {
        $home_path = '/';
    }

    if (substr($home_path, -1) != '/') {
        $home_path .= '/';
    }

    return $home_path;
}

/**
 * Returns path to WP directory relative to document root
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com/
 * Install dir=/var/www/vhosts/domain.com/site/blog/
 * return /site/blog/
 *
 * With trailing slash!
 *
 * @return string
 */
function w3_get_base_path() {
    $document_root = w3_get_document_root();
    $site_root = w3_get_site_root();

    $base_path = str_replace($document_root, '', $site_root);
    $base_path = '/' . ltrim($base_path, '/');

    if (substr($base_path, -1) != '/') {
        $base_path .= '/';
    }

    return $base_path;
}

/**
 * Returns server hostname
 *
 * @return string
 */
function w3_get_host() {
    static $host = null;

    if ($host === null) {
        $host = (!empty($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
    }

    return $host;
}

/**
 * Returns host ID
 *
 * @return string
 */
function w3_get_host_id() {
    static $host_id = null;

    if ($host_id === null) {
        $host = w3_get_host();
        $blog_id = w3_get_blog_id();

        $host_id = sprintf('%s_%d', $host, $blog_id);
    }

    return $host_id;
}

/**
 * Returns nginx rules path
 *
 * @return string
 */
function w3_get_nginx_rules_path() {
    require_once W3TC_LIB_W3_DIR . '/Config.php';
    $config =& W3_Config::instance();

    $path = $config->get_string('config.path');

    if (!$path) {
        $path = w3_get_document_root() . '/nginx.conf';
    }

    return $path;
}

/**
 * Returns path of pagecache core rules file
 *
 * @return string
 */
function w3_get_pgcache_rules_core_path() {
    switch (true) {
        case w3_is_apache():
            return w3_get_home_root() . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of pgcache cache rules file
 *
 * @return string
 */
function w3_get_pgcache_rules_cache_path() {
    switch (true) {
        case w3_is_apache():
            return W3TC_CACHE_FILE_PGCACHE_DIR . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of browsercache cache rules file
 *
 * @return string
 */
function w3_get_browsercache_rules_cache_path() {
    switch (true) {
        case w3_is_apache():
            return w3_get_home_root() . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of browsercache no404wp rules file
 *
 * @return string
 */
function w3_get_browsercache_rules_no404wp_path() {
    switch (true) {
        case w3_is_apache():
            return w3_get_home_root() . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of minify rules file
 *
 * @return string
 */
function w3_get_minify_rules_core_path() {
    switch (true) {
        case w3_is_apache():
            return W3TC_CACHE_FILE_MINIFY_DIR . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of minify rules file
 *
 * @return string
 */
function w3_get_minify_rules_cache_path() {
    switch (true) {
        case w3_is_apache():
            return W3TC_CACHE_FILE_MINIFY_DIR . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns WP config file path
 *
 * @return string
 */
function w3_get_wp_config_path() {
    $search = array(
        ABSPATH . 'wp-config.php',
        dirname(ABSPATH) . '/wp-config.php'
    );

    foreach ($search as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    return false;
}

/**
 * Returns theme key
 *
 * @param string $theme_root
 * @param string $template
 * @param string $stylesheet
 * @return string
 */
function w3_get_theme_key($theme_root, $template, $stylesheet) {
    $site_root = w3_get_site_root();
    $theme_path = ltrim(str_replace($site_root, '', w3_path($theme_root)), '/');

    return substr(md5($theme_path . $template . $stylesheet), 0, 5);
}

/**
 * Returns theme key (legacy support)
 *
 * @param string $theme_root
 * @param string $template
 * @param string $stylesheet
 * @return string
 */
function w3_get_theme_key_legacy($theme_root, $template, $stylesheet) {
    return substr(md5($theme_root . $template . $stylesheet), 0, 6);
}

/**
 * Returns path of minify rules file
 *
 * @return string
 */
function w3_get_cdn_rules_path() {
    switch (true) {
        case w3_is_apache():
            return '.htaccess';

        case w3_is_nginx():
            return 'nginx.conf';
    }

    return false;
}

/**
 * Returns true if we can check rules
 *
 * @return bool
 */
function w3_can_check_rules() {
    return (w3_is_apache() || w3_is_nginx());
}

/**
 * Returns true if we can modify rules
 *
 * @param string $path
 * @return boolean
 */
function w3_can_modify_rules($path) {
    if (w3_is_network()) {
        if (w3_is_apache()) {
            switch ($path) {
                case w3_get_pgcache_rules_cache_path():
                case w3_get_minify_rules_core_path():
                case w3_get_minify_rules_cache_path():
                    return true;
            }
        }

        return false;
    }

    return true;
}

/**
 * Returns true if CDN engine is supporting purge
 *
 * @param string $engine
 * @return bool
 */
function w3_can_cdn_purge($engine) {
    return in_array($engine, array('ftp', 's3', 'cf', 'cf2', 'rscf', 'azure', 'netdna', 'cotendo'));
}

/**
 * Parses path
 *
 * @param string $path
 * @return mixed
 */
function w3_parse_path($path) {
    $path = str_replace(array(
        '%BLOG_ID%',
        '%POST_ID%',
        '%BLOGNAME%',
        '%HOST%',
        '%DOMAIN%',
        '%BASE_PATH%'
    ), array(
        (isset($GLOBALS['blog_id']) ? (int) $GLOBALS['blog_id'] : 0),
        (isset($GLOBALS['post_id']) ? (int) $GLOBALS['post_id'] : 0),
        w3_get_blogname(),
        w3_get_host(),
        w3_get_domain(w3_get_host()),
        trim(w3_get_base_path(), '/')
    ), $path);

    return $path;
}

/**
 * Normalizes file name
 *
 * Relative to site root!
 *
 * @param string $file
 * @return string
 */
function w3_normalize_file($file) {
    if (w3_is_url($file)) {
        if (strstr($file, '?') === false) {
            $home_url_regexp = '~' . w3_get_home_url_regexp() . '~i';
            $file = preg_replace($home_url_regexp, '', $file);
        }
    }

    if (!w3_is_url($file)) {
        $file = w3_path($file);
        $file = str_replace(w3_get_site_root(), '', $file);
        $file = ltrim($file, '/');
    }

    return $file;
}

/**
 * Normalizes file name for minify
 *
 * Relative to document root!
 *
 * @param string $file
 * @return string
 */
function w3_normalize_file_minify($file) {
    if (w3_is_url($file)) {
        if (strstr($file, '?') === false) {
            $domain_url_regexp = '~' . w3_get_domain_url_regexp() . '~i';
            $file = preg_replace($domain_url_regexp, '', $file);
        }
    }

    if (!w3_is_url($file)) {
        $file = w3_path($file);
        $file = str_replace(w3_get_document_root(), '', $file);
        $file = ltrim($file, '/');
    }

    return $file;
}

/**
 * Normalizes file name for minify
 *
 * Relative to document root!
 *
 * @param string $file
 * @return string
 */
function w3_normalize_file_minify2($file) {
    $file = w3_remove_query($file);
    $file = w3_normalize_file_minify($file);
    $file = w3_translate_file($file);

    return $file;
}

/**
 * Translates remote file to local file
 *
 * @param string $file
 * @return string
 */
function w3_translate_file($file) {
    if (!w3_is_url($file)) {
        $file = '/' . ltrim($file, '/');
        $regexp = '~^' . w3_preg_quote(w3_get_site_path()) . '~';
        $file = preg_replace($regexp, w3_get_base_path(), $file);
        $file = ltrim($file, '/');
    }

    return $file;
}

/**
 * Remove WP query string from URL
 *
 * @param string $url
 * @return string
 */
function w3_remove_query($url) {
    $url = preg_replace('~[&\?]+(ver=[a-z0-9-_\.]+|[0-9-]+)~i', '', $url);

    return $url;
}

/**
 * Converts win path to unix
 *
 * @param string $path
 * @return string
 */
function w3_path($path) {
    $path = preg_replace('~[/\\\]+~', '/', $path);
    $path = rtrim($path, '/');

    return $path;
}

/**
 * Returns real path of given path
 *
 * @param string $path
 * @return string
 */
function w3_realpath($path) {
    $path = w3_path($path);
    $parts = explode('/', $path);
    $absolutes = array();

    foreach ($parts as $part) {
        if ('.' == $part) {
            continue;
        }
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }

    return implode('/', $absolutes);
}

/**
 * Returns dirname of path
 *
 * @param string $path
 * @return string
 */
function w3_dirname($path) {
    $dirname = dirname($path);

    if ($dirname == '.' || $dirname == '/' || $dirname == '\\') {
        $dirname = '';
    }

    return $dirname;
}

/**
 * Returns open basedirs
 *
 * @return array
 */
function w3_get_open_basedirs() {
    $open_basedir_ini = ini_get('open_basedir');
    $open_basedirs = (W3TC_WIN ? preg_split('~[;,]~', $open_basedir_ini) : explode(':', $open_basedir_ini));
    $result = array();

    foreach ($open_basedirs as $open_basedir) {
        $open_basedir = trim($open_basedir);
        if ($open_basedir != '') {
            $result[] = w3_realpath($open_basedir);
        }
    }

    return $result;
}

/**
 * Checks if path is restricted by open_basedir
 *
 * @param string $path
 * @return boolean
 */
function w3_check_open_basedir($path) {
    $path = w3_realpath($path);
    $open_basedirs = w3_get_open_basedirs();

    if (!count($open_basedirs)) {
        return true;
    }

    foreach ($open_basedirs as $open_basedir) {
        if (strstr($path, $open_basedir) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * Request URL
 *
 * @param string $method
 * @param string $url
 * @param string $data
 * @param string $auth
 * @param boolean $check_status
 * @return string
 */
function w3_http_request($method, $url, $data = '', $auth = '', $check_status = true) {
    $status = 0;
    $method = strtoupper($method);

    if (function_exists('curl_init')) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, W3TC_POWERED_BY);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;

            case 'PURGE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PURGE');
                break;
        }

        if ($auth) {
            curl_setopt($ch, CURLOPT_USERPWD, $auth);
        }

        $contents = curl_exec($ch);

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
    } else {
        $parse_url = @parse_url($url);

        if ($parse_url && isset($parse_url['host'])) {
            $host = $parse_url['host'];
            $port = (isset($parse_url['port']) ? (int) $parse_url['port'] : 80);
            $path = (!empty($parse_url['path']) ? $parse_url['path'] : '/');
            $query = (isset($parse_url['query']) ? $parse_url['query'] : '');
            $request_uri = $path . ($query != '' ? '?' . $query : '');

            $request_headers_array = array(
                sprintf('%s %s HTTP/1.0', $method, $request_uri),
                sprintf('Host: %s', $host),
                sprintf('User-Agent: %s', W3TC_POWERED_BY),
                'Connection: close'
            );

            if (!empty($data)) {
                $request_headers_array[] = sprintf('Content-Length: %d', strlen($data));
            }

            if (!empty($auth)) {
                $request_headers_array[] = sprintf('Authorization: Basic %s', base64_encode($auth));
            }

            $request_headers = implode("\r\n", $request_headers_array);
            $request = $request_headers . "\r\n\r\n" . $data;
            $errno = null;
            $errstr = null;

            $fp = @fsockopen($host, $port, $errno, $errstr, 10);

            if (!$fp) {
                return false;
            }

            @stream_set_timeout($fp, 60);

            $response = '';
            @fputs($fp, $request);

            while (!@feof($fp)) {
                $response .= @fgets($fp, 4096);
            }

            @fclose($fp);

            list ($response_headers, $contents) = explode("\r\n\r\n", $response, 2);

            $matches = null;

            if (preg_match('~^HTTP/1.[01] (\d+)~', $response_headers, $matches)) {
                $status = (int) $matches[1];
            }
        }
    }

    if (!$check_status || $status == 200) {
        return $contents;
    }

    return false;
}

/**
 * Download url via GET
 *
 * @param string $url
 * @param string $auth
 * $param boolean $check_status
 * @param bool $check_status
 * @return string
 */
function w3_http_get($url, $auth = '', $check_status = true) {
    return w3_http_request('GET', $url, null, $auth, $check_status);
}

/**
 * Send POST request to URL
 *
 * @param string $url
 * @param string $data
 * @param string $auth
 * @param boolean $check_status
 * @return string
 */
function w3_http_post($url, $data = '', $auth = '', $check_status = true) {
    return w3_http_request('POST', $url, $data, $auth, $check_status);
}

/**
 * Send PURGE request to Varnish server
 *
 * @param string $url
 * @param string $auth
 * $param boolean $check_status
 * @param bool $check_status
 * @return string
 */
function w3_http_purge($url, $auth = '', $check_status = true) {
    return w3_http_request('PURGE', $url, null, $auth, $check_status);
}

/**
 * Returns GMT date
 * @param integer $time
 * @return string
 */
function w3_http_date($time) {
    return gmdate('D, d M Y H:i:s \G\M\T', $time);
}

/**
 * Downloads data to a file
 *
 * @param string $url
 * @param string $file
 * @return boolean
 */
function w3_download($url, $file) {
    if (strpos($url, '//') === 0) {
        $url = (w3_is_https() ? 'https:' : 'http:') . $url;
    }

    $data = w3_http_get($url);

    if ($data !== false) {
        return @file_put_contents($file, $data);
    }

    return false;
}

/**
 * Returns upload info
 *
 * @return array
 */
function w3_upload_info() {
    static $upload_info = null;

    if ($upload_info === null) {
        $upload_info = @wp_upload_dir();

        if (empty($upload_info['error'])) {
            $parse_url = @parse_url($upload_info['baseurl']);

            if ($parse_url) {
                $baseurlpath = (!empty($parse_url['path']) ? trim($parse_url['path'], '/') : '');
            } else {
                $baseurlpath = 'wp-content/uploads';
            }

            $upload_info['baseurlpath'] = '/' . $baseurlpath . '/';
        } else {
            $upload_info = false;
        }
    }

    return $upload_info;
}

/**
 * Formats URL
 *
 * @param string $url
 * @param array $params
 * @param boolean $skip_empty
 * @param string $separator
 * @return string
 */
function w3_url_format($url = '', $params = array(), $skip_empty = false, $separator = '&') {
    if ($url != '') {
        $parse_url = @parse_url($url);
        $url = '';

        if (!empty($parse_url['scheme'])) {
            $url .= $parse_url['scheme'] . '://';

            if (!empty($parse_url['user'])) {
                $url .= $parse_url['user'];

                if (!empty($parse_url['pass'])) {
                    $url .= ':' . $parse_url['pass'];
                }
            }

            if (!empty($parse_url['host'])) {
                $url .= $parse_url['host'];
            }

            if (!empty($parse_url['port']) && $parse_url['port'] != 80) {
                $url .= ':' . (int) $parse_url['port'];
            }
        }

        if (!empty($parse_url['path'])) {
            $url .= $parse_url['path'];
        }

        if (!empty($parse_url['query'])) {
            $old_params = array();
            parse_str($parse_url['query'], $old_params);

            $params = array_merge($old_params, $params);
        }

        $query = w3_url_query($params);

        if ($query != '') {
            $url .= '?' . $query;
        }

        if (!empty($parse_url['fragment'])) {
            $url .= '#' . $parse_url['fragment'];
        }
    } else {
        $query = w3_url_query($params, $skip_empty, $separator);

        if ($query != '') {
            $url = '?' . $query;
        }
    }

    return $url;
}

/**
 * Formats query string
 *
 * @param array $params
 * @param boolean $skip_empty
 * @param string $separator
 * @return string
 */
function w3_url_query($params = array(), $skip_empty = false, $separator = '&') {
    $str = '';
    static $stack = array();

    foreach ((array) $params as $key => $value) {
        if ($skip_empty === true && empty($value)) {
            continue;
        }

        array_push($stack, $key);

        if (is_array($value)) {
            if (count($value)) {
                $str .= ($str != '' ? '&' : '') . w3_url_query($value, $skip_empty, $key);
            }
        } else {
            $name = '';
            foreach ($stack as $key) {
                $name .= ($name != '' ? '[' . $key . ']' : $key);
            }
            $str .= ($str != '' ? $separator : '') . $name . '=' . rawurlencode($value);
        }

        array_pop($stack);
    }

    return $str;
}


/**
 * Redirects to URL
 *
 * @param string $url
 * @param array $params
 * @return string
 */
function w3_redirect($url = '', $params = array()) {
    $url = w3_url_format($url, $params);

    @header('Location: ' . $url);
    exit();
}

/**
 * Returns caching engine name
 *
 * @param $engine
 * @return string
 */
function w3_get_engine_name($engine) {
    switch ($engine) {
        case 'memcached':
            $engine_name = 'memcached';
            break;

        case 'apc':
            $engine_name = 'apc';
            break;

        case 'eaccelerator':
            $engine_name = 'eaccelerator';
            break;

        case 'xcache':
            $engine_name = 'xcache';
            break;

        case 'wincache':
            $engine_name = 'wincache';
            break;

        case 'file':
            $engine_name = 'disk';
            break;

        case 'file_generic':
            $engine_name = 'disk (enhanced)';
            break;

        case 'mirror':
            $engine_name = 'mirror';
            break;

        case 'netdna':
            $engine_name = 'netdna / maxcdn';
            break;

        case 'cotendo':
            $engine_name = 'cotendo';
            break;

        case 'ftp':
            $engine_name = 'self-hosted / file transfer protocol upload';
            break;

        case 's3':
            $engine_name = 'amazon simple storage service (s3)';
            break;

        case 'cf':
            $engine_name = 'amazon cloudfront';
            break;

        case 'cf2':
            $engine_name = 'amazon cloudfront';
            break;

        case 'rscf':
            $engine_name = 'rackspace cloud files';
            break;

        case 'azure':
            $engine_name = 'microsoft azure storage';
            break;

        default:
            $engine_name = 'n/a';
            break;
    }

    return $engine_name;
}

/**
 * Converts value to boolean
 *
 * @param mixed $value
 * @return boolean
 */
function w3_to_boolean($value) {
    if (is_string($value)) {
        switch (strtolower($value)) {
            case '+':
            case '1':
            case 'y':
            case 'on':
            case 'yes':
            case 'true':
            case 'enabled':
                return true;

            case '-':
            case '0':
            case 'n':
            case 'no':
            case 'off':
            case 'false':
            case 'disabled':
                return false;
        }
    }

    return (boolean) $value;
}

/**
 * Returns file mime type
 *
 * @param string $file
 * @return string
 */
function w3_get_mime_type($file) {
    static $cache = array();

    if (!isset($cache[$file])) {
        $mime_type = false;

        /**
         * Try to detect by extension (fast)
         */
        $mime_types = include W3TC_DIR . '/inc/mime/all.php';

        foreach ($mime_types as $extension => $type) {
            if (preg_match('~\.(' . $extension . ')$~i', $file)) {
                $mime_type = $type;
                break;
            }
        }

        /**
         * Try to detect using file info function
         */
        if (!$mime_type && function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME);

            if (!$finfo) {
                $finfo = @finfo_open(FILEINFO_MIME);
            }

            if ($finfo) {
                $mime_type = @finfo_file($finfo, $file);

                if ($mime_type) {
                    $extra_mime_type_info = strpos($mime_type, "; ");

                    if ($extra_mime_type_info) {
                        $mime_type = substr($mime_type, 0, $extra_mime_type_info);
                    }

                    if ($mime_type == 'application/octet-stream') {
                        $mime_type = false;
                    }
                }

                @finfo_close($finfo);
            }
        }

        /**
         * Try to detect using mime type function
         */
        if (!$mime_type && function_exists('mime_content_type')) {
            $mime_type = @mime_content_type($file);
        }

        /**
         * If detection failed use default mime type
         */
        if (!$mime_type) {
            $mime_type = 'application/octet-stream';
        }

        $cache[$file] = $mime_type;
    }

    return $cache[$file];
}

/**
 * Quotes regular expression string
 *
 * @param string $string
 * @param string $delimiter
 * @return string
 */
function w3_preg_quote($string, $delimiter = null) {
    $string = preg_quote($string, $delimiter);
    $string = strtr($string, array(
        ' ' => '\ '
    ));

    return $string;
}

/**
 * Returns true if zlib output compression is enabled otherwise false
 *
 * @return boolean
 */
function w3_zlib_output_compression() {
    return w3_to_boolean(ini_get('zlib.output_compression'));
}

/**
 * Recursive strips slahes from the var
 *
 * @param mixed $var
 * @return mixed
 */
function w3_stripslashes($var) {
    if (is_string($var)) {
        return stripslashes($var);
    } elseif (is_array($var)) {
        $var = array_map('w3_stripslashes', $var);
    }

    return $var;
}

if (!function_exists('file_put_contents')) {
    if (!defined('FILE_APPEND')) {
        define('FILE_APPEND', 8);
    }

    /**
     * Puts contents to the file
     *
     * @param string $filename
     * @param string $data
     * @param integer $flags
     * @return boolean
     */
    function file_put_contents($filename, $data, $flags = 0) {
        $fp = fopen($filename, ($flags & FILE_APPEND ? 'a' : 'w'));

        if ($fp) {
            fputs($fp, $data);
            fclose($fp);

            return true;
        }

        return false;
    }
}

/**
 * Trim rules
 *
 * @param string $rules
 * @return string
 */
function w3_trim_rules($rules) {
    $rules = trim($rules);

    if ($rules != '') {
        $rules .= "\n";
    }

    return $rules;
}

/**
 * Cleanup rewrite rules
 *
 * @param string $rules
 * @return string
 */
function w3_clean_rules($rules) {
    $rules = preg_replace('~[\r\n]+~', "\n", $rules);
    $rules = preg_replace('~^\s+~m', '', $rules);
    $rules = w3_trim_rules($rules);

    return $rules;
}

/**
 * Erases text from start to end
 *
 * @param string $rules
 * @param string $start
 * @param string $end
 * @return string
 */
function w3_erase_rules($rules, $start, $end) {
    $rules = preg_replace('~' . w3_preg_quote($start) . "\n.*?" . w3_preg_quote($end) . "\n*~s", '', $rules);
    $rules = w3_trim_rules($rules);

    return $rules;
}

/**
 * Check if rules exist
 *
 * @param string $rules
 * @param string $start
 * @param string $end
 * @return int
 */
function w3_has_rules($rules, $start, $end) {
    return preg_match('~' . w3_preg_quote($start) . "\n.*?" . w3_preg_quote($end) . "\n*~s", $rules);
}

/**
 * Extracts JS files from content
 *
 * @param string $content
 * @return array
 */
function w3_extract_js($content) {
    $matches = null;
    $files = array();

    $content = preg_replace('~<!--.*?-->~s', '', $content);

    if (preg_match_all('~<script\s+[^<>]*src=["\']?([^"\']+)["\']?[^<>]*>\s*</script>~is', $content, $matches)) {
        $files = $matches[1];
    }

    $files = array_values(array_unique($files));

    return $files;
}

/**
 * Extract CSS files from content
 *
 * @param string $content
 * @return array
 */
function w3_extract_css($content) {
    $matches = null;
    $files = array();

    $content = preg_replace('~<!--.*?-->~s', '', $content);

    if (preg_match_all('~<link\s+([^>]+)/?>(.*</link>)?~Uis', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $attrs = array();
            $attr_matches = null;

            if (preg_match_all('~(\w+)=["\']([^"\']*)["\']~', $match[1], $attr_matches, PREG_SET_ORDER)) {
                foreach ($attr_matches as $attr_match) {
                    $attrs[$attr_match[1]] = trim($attr_match[2]);
                }
            }

            if (isset($attrs['href']) && isset($attrs['rel']) && stristr($attrs['rel'], 'stylesheet') !== false && (!isset($attrs['media']) || stristr($attrs['media'], 'print') === false)) {
                $files[] = $attrs['href'];
            }
        }
    }

    if (preg_match_all('~@import\s+(url\s*)?\(?["\']?\s*([^"\'\)\s]+)\s*["\']?\)?[^;]*;?~is', $content, $matches)) {
        $files = array_merge($files, $matches[2]);
    }

    $files = array_values(array_unique($files));

    return $files;
}

/**
 * Escapes HTML comment
 *
 * @param string $comment
 * @return mixed
 */
function w3_escape_comment($comment) {
    while (strstr($comment, '--') !== false) {
        $comment = str_replace('--', '- -', $comment);
    }

    return $comment;
}

/**
 * Loads plugins
 *
 * @return void
 */
function w3_load_plugins() {
    $dir = @opendir(W3TC_PLUGINS_DIR);

    if ($dir) {
        while (($entry = @readdir($dir)) !== false) {
            if (strrchr($entry, '.') === '.php') {
                require_once W3TC_PLUGINS_DIR . '/' . $entry;
            }
        }
        @closedir($dir);
    }
}
