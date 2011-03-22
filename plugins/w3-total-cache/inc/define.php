<?php

define('W3TC_VERSION', '0.9.1.3');
define('W3TC_POWERED_BY', 'W3 Total Cache/' . W3TC_VERSION);
define('W3TC_EMAIL', 'w3tc@w3-edge.com');
define('W3TC_PAYPAL_URL', 'https://www.paypal.com/cgi-bin/webscr');
define('W3TC_PAYPAL_BUSINESS', 'w3tc@w3-edge.com');
define('W3TC_LINK_URL', 'http://www.w3-edge.com/wordpress-plugins/');
define('W3TC_LINK_NAME', 'WordPress Plugins');
define('W3TC_FEED_URL', 'http://feeds.feedburner.com/W3TOTALCACHE');
define('W3TC_README_URL', 'http://plugins.trac.wordpress.org/browser/w3-total-cache/trunk/readme.txt?format=txt');
define('W3TC_TWITTER_STATUS', 'YES! I optimized my #wordpress site\'s #performance using the W3 Total Cache #plugin by @w3edge. Check it out! http://j.mp/A69xX');
define('W3TC_SUPPORT_US_TIMEOUT', 2592000);

define('W3TC_PHP5', PHP_VERSION >= 5);
define('W3TC_WIN', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

defined('W3TC_DIR') || define('W3TC_DIR', realpath(dirname(__FILE__) . '/..'));
define('W3TC_FILE', 'w3-total-cache/w3-total-cache.php');
define('W3TC_LIB_DIR', W3TC_DIR . '/lib');
define('W3TC_LIB_W3_DIR', W3TC_LIB_DIR . '/W3');
define('W3TC_LIB_MINIFY_DIR', W3TC_LIB_DIR . '/Minify');
define('W3TC_LIB_CF_DIR', W3TC_LIB_DIR . '/CF');
define('W3TC_PLUGINS_DIR', W3TC_DIR . '/plugins');
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
define('W3TC_CDN_TABLE_QUEUE', 'w3tc_cdn_queue');

@ini_set('pcre.backtrack_limit', 4194304);
@ini_set('pcre.recursion_limit', 4194304);

$_w3tc_actions = array();

/**
 * Deactivate plugin after activation error
 * 
 * @return void
 */
function w3_activation_cleanup()
{
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
function w3_activate_error($error)
{
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
function w3_writable_error($path)
{
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
function w3_network_activate_error()
{
    w3_activation_cleanup();
    wp_redirect(plugins_url('inc/network_activation.php', W3TC_FILE));
    
    echo '<p><strong>W3 Total Cache Error:</strong> plugin cannot be activated network-wide.</p>';
    echo '<p><a href="javascript:history.back(-1);">Back</a>';
    exit();
}

/**
 * Returns current microtime
 *
 * @return float
 */
function w3_microtime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

/**
 * Recursive creates directory
 *
 * @param string $path
 * @param integer $mask
 * @param string
 * @return boolean
 */
function w3_mkdir($path, $mask = 0755, $curr_path = '')
{
    $path = w3_realpath($path);
    $path = trim($path, '/');
    $dirs = explode('/', $path);
    
    foreach ($dirs as $dir) {
        if ($dir == '') {
            return false;
        }
        
        $curr_path .= ($curr_path == '' ? '' : '/') . $dir;
        
        if (!@is_dir($curr_path)) {
            if (@mkdir($curr_path, $mask)) {
                @chmod($curr_path, $mask);
            } else {
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
 * @return void
 */
function w3_rmdir($path, $exclude = array(), $remove = true)
{
    $dir = @opendir($path);
    
    if ($dir) {
        while (($entry = @readdir($dir)) !== false) {
            $full_path = $path . '/' . $entry;
            
            if ($entry != '.' && $entry != '..' && !in_array($full_path, $exclude)) {
                if (@is_dir($full_path)) {
                    w3_rmdir($full_path, $exclude);
                } else {
                    @unlink($full_path);
                }
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
function w3_emptydir($path, $exclude = array())
{
    w3_rmdir($path, $exclude, false);
}

/**
 * Check if content is HTML or XML
 *
 * @param string $content
 * @return boolean
 */
function w3_is_xml(&$content)
{
    return (stristr($content, '<?xml') !== false || stristr($content, '<html') !== false);
}

/**
 * Returns true if it's WPMU
 *
 * @return boolean
 */
function w3_is_wpmu()
{
    static $wpmu = null;
    
    if ($wpmu === null) {
        $wpmu = (w3_is_vhost() || file_exists(ABSPATH . 'wpmu-settings.php'));
    }
    
    return $wpmu;
}

/**
 * Returns true if it's WP with enabled Network mode
 *
 * @return boolean
 */
function w3_is_network_mode()
{
    static $network_mode = null;
    
    if ($network_mode === null) {
        $network_mode = (defined('MULTISITE') && MULTISITE);
    }
    
    return $network_mode;
}

/**
 * Returns if there is multisite mode
 * 
 * @return boolean
 */
function w3_is_multisite()
{
    return (w3_is_wpmu() || w3_is_network_mode());
}

/**
 * Returns true if WPMU uses vhosts
 *
 * @return boolean
 */
function w3_is_vhost()
{
    return ((defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) || (defined('VHOST') && VHOST == 'yes'));
}

/**
 * Check if URL is valid
 *
 * @param string $url
 * @return boolean
 */
function w3_is_url($url)
{
    return preg_match('~^https?://~', $url);
}

/**
 * Returns true if current connection is secure
 *
 * @return boolean
 */
function w3_is_https()
{
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
function w3_is_permalink_rules()
{
    $path = w3_get_home_root() . '/.htaccess';
    
    return (($data = @file_get_contents($path)) && (strstr($data, 'add a trailing slash to') !== false || strstr($data, 'BEGIN WordPress') !== false));
}

/**
 * Check if there was database error
 * 
 * @param string $content
 * @return boolean
 */
function w3_is_database_error(&$content)
{
    return (stristr($content, '<title>Database Error</title>') !== false);
}

/**
 * Returns true if preview config exists
 * 
 * @return boolean
 */
function w3_is_preview_config()
{
    return file_exists(W3TC_CONFIG_PREVIEW_PATH);
}

/**
 * Retuns true if preview settings active
 * 
 * @return boolean
 */
function w3_is_preview_mode()
{
    return (w3_is_preview_config() && (defined('WP_ADMIN') || isset($_REQUEST['w3tc_preview']) || strstr($_SERVER['HTTP_REFERER'], 'w3tc_preview') !== false));
}

/**
 * Check if file is write-able
 * 
 * @param string $path
 * @return boolean
 */
function w3_is_writable($file)
{
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
function w3_is_writable_dir($dir)
{
    $file = $dir . '/' . uniqid(mt_rand()) . '.tmp';
    
    return w3_is_writable($file);
}

/**
 * Returns domain from host
 *
 * @param string $host
 * @return string
 */
function w3_get_domain($host)
{
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
function w3_get_blognames()
{
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
function w3_load_blognames()
{
    $blognames = include W3TC_BLOGNAMES_PATH;
    
    return $blognames;
}

/**
 * Save blognames into file
 * 
 * @param string $blognames
 * @return boolean
 */
function w3_save_blognames($blognames = null)
{
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
function w3_get_blogname()
{
    static $blogname = null;
    
    if ($blogname === null) {
        if (w3_is_multisite()) {
            $host = w3_get_host();
            $domain = w3_get_domain($host);
            
            if (w3_is_vhost()) {
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
 * @param string
 */
function w3_get_blogname_from_uri($uri)
{
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
 * Returns URL regexp from URL
 * 
 * @param string $url
 * @return string
 */
function w3_get_url_regexp($url)
{
    $url = preg_replace('~https?://~i', '', $url);
    $url = preg_replace('~^www\.~i', '', $url);
    
    $regexp = 'https?://(www\.)?' . w3_preg_quote($url);
    
    return $regexp;
}

/**
 * Returns SSL URL if current connection is https
 * @param string $url
 * @return string
 */
function w3_get_url_ssl($url)
{
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

function w3_get_domain_url()
{
    $site_url = w3_get_site_url();
    $parse_url = @parse_url($site_url);
    
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
function w3_get_domain_url_regexp()
{
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
function w3_get_home_url()
{
    static $home_url = null;
    
    if ($home_url === null) {
        if (function_exists('get_option')) {
            $home_url = get_option('home');
        } else {
            $home_url = w3_get_site_url();
        }
        
        $home_url = rtrim($home_url, '/');
    }
    
    return $home_url;
}

/**
 * Returns SSL home url
 *
 * @return string
 */
function w3_get_home_url_ssl()
{
    $home_url = w3_get_home_url();
    $ssl = w3_get_url_ssl($home_url);
    
    return $ssl;
}

/**
 * Returns site URL
 * 
 * No trailing slash!
 *
 * @return string
 */
function w3_get_site_url()
{
    static $site_url = null;
    
    if ($site_url === null) {
        if (function_exists('get_option')) {
            $site_url = get_option('siteurl');
        } else {
            $site_url = sprintf('http://%s%s', w3_get_host(), w3_get_base_path());
        }
        
        $site_url = rtrim($site_url, '/');
    }
    
    return $site_url;
}

/**
 * Returns SSL site url
 *
 * @return string
 */
function w3_get_site_url_ssl()
{
    $site_url = w3_get_site_url();
    $ssl = w3_get_url_ssl($site_url);
    
    return $ssl;
}

/**
 * Returns site url regexp
 *
 * @return string
 */
function w3_get_site_url_regexp()
{
    $site_url = w3_get_site_url();
    $regexp = w3_get_url_regexp($site_url);
    
    return $regexp;
}

/**
 * Returns absolute path to document root
 * 
 * No trailing slash!
 * 
 * @return string
 */
function w3_get_document_root()
{
    static $document_root = null;
    
    if ($document_root === null) {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $document_root = substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['PHP_SELF']));
        } elseif (isset($_SERVER['PATH_TRANSLATED'])) {
            $document_root = substr($_SERVER['PATH_TRANSLATED'], 0, -strlen($_SERVER['PHP_SELF']));
        } elseif (isset($_SERVER['DOCUMENT_ROOT'])) {
            $document_root = $_SERVER['DOCUMENT_ROOT'];
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
function w3_get_home_root()
{
    $home_url = w3_get_home_url();
    $site_url = w3_get_site_url();
    
    if (w3_is_multisite()) {
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
function w3_get_site_root()
{
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
function w3_get_site_path()
{
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
function w3_get_home_path()
{
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
function w3_get_base_path()
{
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
function w3_get_host()
{
    static $host = null;
    
    if ($host === null) {
        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
    }
    
    return $host;
}

/**
 * Normalizes file name
 *
 * Relative to site root!
 * 
 * @param string $file
 * @return string
 */
function w3_normalize_file($file)
{
    if (w3_is_url($file)) {
        if (strstr($file, '?') === false) {
            $site_url_regexp = '~' . w3_get_site_url_regexp() . '~i';
            $file = preg_replace($site_url_regexp, '', $file);
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
function w3_normalize_file_minify($file)
{
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
 * Translates remote file to local file
 * 
 * @param string $file
 * @return string
 */
function w3_translate_file($file)
{
    if (!w3_is_url($file)) {
        $file = '/' . ltrim($file, '/');
        $regexp = '~^' . w3_preg_quote(w3_get_site_path()) . '~';
        $file = preg_replace($regexp, w3_get_base_path(), $file);
        $file = ltrim($file, '/');
    }
    
    return $file;
}

/**
 * Converts win path to unix
 *
 * @param string $path
 * @return string
 */
function w3_path($path)
{
    $path = preg_replace('~[/\\\]+~', '/', $path);
    $path = rtrim($path, '/');
    
    return $path;
}

/**
 * Returns realpath of given path
 *
 * @param string $path
 */
function w3_realpath($path)
{
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
function w3_dirname($path)
{
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
function w3_get_open_basedirs()
{
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
function w3_check_open_basedir($path)
{
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
function w3_http_request($method, $url, $data = '', $auth = '', $check_status = true)
{
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        
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
            
            $response = '';
            @fputs($fp, $request);
            
            while (!@feof($fp)) {
                $response .= @fgets($fp, 4096);
            }
            
            @fclose($fp);
            
            list($response_headers, $contents) = explode("\r\n\r\n", $response, 2);
            
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
 * @return string
 */
function w3_http_get($url, $auth = '', $check_status = true)
{
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
function w3_http_post($url, $data = '', $auth = '', $check_status = true)
{
    return w3_http_request('POST', $url, $data, $auth, $check_status);
}

/**
 * Send PURGE request to Varnish server
 * @param string $url
 * @param string $auth
 * $param boolean $check_status
 * @return string
 */
function w3_http_purge($url, $auth = '', $check_status = true)
{
    return w3_http_request('PURGE', $url, null, $auth, $check_status);
}

/**
 * Returns GMT date
 * @param integer $time
 * @return string
 */
function w3_http_date($time)
{
    return gmdate('D, d M Y H:i:s \G\M\T', $time);
}

/**
 * Downloads data to a file
 *
 * @param string $url
 * @param string $file
 * @return boolean
 */
function w3_download($url, $file)
{
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
function w3_upload_info()
{
    static $upload_info = null;
    
    if ($upload_info === null) {
        $upload_info = @wp_upload_dir();
        
        if (empty($upload_info['error'])) {
            $site_url = w3_get_site_url();
            
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
 * Redirects to URL
 *
 * @param string $url
 * @param string $params
 * @return string
 */
function w3_redirect($url = '', $params = array())
{
    $fragment = '';
    
    if ($url != '' && ($parse_url = @parse_url($url))) {
        $url = '';
        
        if (!empty($parse_url['scheme'])) {
            $url .= $parse_url['scheme'] . '://';
        }
        
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
        
        $url .= (!empty($parse_url['path']) ? $parse_url['path'] : '/');
        
        if (!empty($parse_url['query'])) {
            $old_params = array();
            parse_str($parse_url['query'], $old_params);
            
            $params = array_merge($old_params, $params);
        }
        
        if (!empty($parse_url['fragment'])) {
            $fragment = '#' . $parse_url['fragment'];
        }
    } else {
        $parse_url = array();
    }
    
    if (($count = count($params))) {
        $query = '';
        
        foreach ($params as $param => $value) {
            $count--;
            $query .= urlencode($param) . (!empty($value) ? '=' . urlencode($value) : '') . ($count ? '&' : '');
        }
        
        $url .= (strpos($url, '?') === false ? '?' : '&') . $query;
    }
    
    if ($fragment != '') {
        $url .= $fragment;
    }
    
    @header('Location: ' . $url);
    exit();
}

/**
 * Returns caching engine name
 *
 * @param $engine
 * @return string
 */
function w3_get_engine_name($engine)
{
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
        
        case 'file':
            $engine_name = 'disk';
            break;
        
        case 'file_pgcache':
            $engine_name = 'disk (enhanced)';
            break;
        
        case 'mirror':
            $engine_name = 'mirror';
            break;
        
        case 'netdna':
            $engine_name = 'mirror: netdna / maxcdn';
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
        
        case 'rscf':
            $engine_name = 'rackspace cloud files';
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
function w3_to_boolean($value)
{
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
 * Loads plugins
 *
 * @return void
 */
function w3_load_plugins()
{
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

/**
 * Returns file mime type
 *
 * @param string $file
 * @return string
 */
function w3_get_mime_type($file)
{
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
 * Send twitter update status request
 *
 * @param string $username
 * @param string $password
 * @param string $status
 * @param string $error
 * @return string
 */
function w3_twitter_status_update($username, $password, $status, &$error)
{
    $data = sprintf('status=%s', urlencode($status));
    $auth = sprintf('%s:%s', $username, $password);
    
    $xml = w3_http_post('http://twitter.com/statuses/update.xml', $data, $auth);
    
    if ($xml) {
        $matches = null;
        
        if (preg_match('~<id>(\d+)</id>~', $xml, $matches)) {
            return $matches[1];
        } elseif (preg_match('~<error>([^<]+)</error>~', $xml, $matches)) {
            $error = $matches[1];
        } else {
            $error = 'Unknown error.';
        }
    } else {
        $error = 'Unable to send request.';
    }
    
    return false;
}

/**
 * Quotes regular expression string
 *
 * @param string $regexp
 * @return string
 */
function w3_preg_quote($string, $delimiter = null)
{
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
function w3_zlib_output_compression()
{
    return w3_to_boolean(ini_get('zlib.output_compression'));
}

/**
 * Recursive strips slahes from the var
 *
 * @param mixed $var
 * @return mixed
 */
function w3_stripslashes($var)
{
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
    function file_put_contents($filename, $data, $flags = 0)
    {
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
 * Cleanup .htaccess rules
 *
 * @param string $rules
 * @return string
 */
function w3_clean_rules($rules)
{
    $rules = preg_replace('~[\r\n]+~', "\n", $rules);
    $rules = preg_replace('~^\s+~m', '', $rules);
    $rules = trim($rules);
    
    return $rules;
}

/**
 * Erases text from start to end
 *
 * @param string $text
 * @param string $start
 * @param string $end
 * @return string
 */
function w3_erase_text($text, $start, $end)
{
    $text = preg_replace('~' . w3_preg_quote($start) . '.*' . w3_preg_quote($end) . '~Us', '', $text);
    $text = trim($text);
    
    return $text;
}

/**
 * Return deafult htaccess rules for current WP version
 *
 * @return string
 */
function w3_get_permalink_rules()
{
    $rules = '';
    $base_path = w3_get_base_path();
    
    if (w3_is_wpmu()) {
        $rules .= "RewriteEngine On\n";
        $rules .= "RewriteBase " . $base_path . "\n\n";
        
        $rules .= "#uploaded files\n";
        $rules .= "RewriteRule ^(.*/)?files/$ index.php [L]\n";
        $rules .= "RewriteCond %{REQUEST_URI} !.*wp-content/plugins.*\n";
        $rules .= "RewriteRule ^(.*/)?files/(.*) wp-content/blogs.php?file=$2 [L]\n\n";
        
        $rules .= "# add a trailing slash to /wp-admin\n";
        $rules .= "RewriteCond %{REQUEST_URI} ^.*/wp-admin$\n";
        $rules .= "RewriteRule ^(.+)$ $1/ [R=301,L]\n\n";
        
        $rules .= "RewriteCond %{REQUEST_FILENAME} -f [OR]\n";
        $rules .= "RewriteCond %{REQUEST_FILENAME} -d\n";
        $rules .= "RewriteRule . - [L]\n";
        $rules .= "RewriteRule  ^([_0-9a-zA-Z-]+/)?(wp-.*) $2 [L]\n";
        $rules .= "RewriteRule  ^([_0-9a-zA-Z-]+/)?(.*\\.php)$ $2 [L]\n";
        $rules .= "RewriteRule . index.php [L]\n\n";
        
        $rules .= "<IfModule mod_security.c>\n";
        $rules .= "<Files async-upload.php>\n";
        $rules .= "SecFilterEngine Off\n";
        $rules .= "SecFilterScanPOST Off\n";
        $rules .= "</Files>\n";
        $rules .= "</IfModule>\n";
    } elseif (w3_is_network_mode()) {
        $subdomain_install = is_subdomain_install();
        
        $rules .= "# BEGIN WordPress\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "RewriteEngine On\n";
        $rules .= "RewriteBase " . $base_path . "\n";
        $rules .= "RewriteRule ^index\\.php$ - [L]\n\n";
        
        $rules .= "# uploaded files\n";
        $rules .= "RewriteRule ^" . ($subdomain_install ? '' : '([_0-9a-zA-Z-]+/)?') . "files/(.+) wp-includes/ms-files.php?file=$" . ($subdomain_install ? 1 : 2) . " [L]\n\n";
        
        if (!$subdomain_install) {
            $rules .= "# add a trailing slash to /wp-admin\n";
            $rules .= "RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ $1wp-admin/ [R=301,L]\n";
        }
        
        $rules .= "RewriteCond %{REQUEST_FILENAME} -f [OR]\n";
        $rules .= "RewriteCond %{REQUEST_FILENAME} -d\n";
        $rules .= "RewriteRule ^ - [L]\n";
        
        // @todo custom content dir.
        if (!$subdomain_install) {
            $rules .= "RewriteRule  ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]\n";
            $rules .= "RewriteRule  ^([_0-9a-zA-Z-]+/)?(.*\\.php)$ $2 [L]\n";
        }
        
        $rules .= "RewriteRule . index.php [L]\n";
        $rules .= "</IfModule>\n";
        $rules .= "# END WordPress\n";
    } else {
        $home_path = w3_get_home_path();
        
        $rules .= "# BEGIN WordPress\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "   RewriteEngine On\n";
        $rules .= "   RewriteBase " . $home_path . "\n";
        $rules .= "   RewriteCond %{REQUEST_FILENAME} !-f\n";
        $rules .= "   RewriteCond %{REQUEST_FILENAME} !-d\n";
        $rules .= "   RewriteRule . " . $home_path . "index.php [L]\n";
        $rules .= "</IfModule>\n";
        $rules .= "# END WordPress\n";
    }
    
    return $rules;
}

/**
 * Add W3TC action callback
 * 
 * @param string $action
 * @param mixed $callback
 * @return void
 */
function w3tc_add_action($action, $callback)
{
    global $_w3tc_actions;
    
    $_w3tc_actions[$action][] = $callback;
}

/**
 * Do W3TC action
 * 
 * @param string $action
 * @param mixed $value
 * @return mixed
 */
function w3tc_do_action($action, $value = null)
{
    global $_w3tc_actions;
    
    if (isset($_w3tc_actions[$action])) {
        foreach ((array) $_w3tc_actions[$action] as $callback) {
            if (is_callable($callback)) {
                $value = call_user_func($callback, $value);
            }
        }
    }
    
    return $value;
}
