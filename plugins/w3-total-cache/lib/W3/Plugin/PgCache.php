<?php

/**
 * W3 PgCache plugin
 */
require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_PgCache
 */
class W3_Plugin_PgCache extends W3_Plugin
{
    /**
     * Runs plugin
     */
    function run()
    {
        register_activation_hook(W3TC_FILE, array(
            &$this, 
            'activate'
        ));
        
        register_deactivation_hook(W3TC_FILE, array(
            &$this, 
            'deactivate'
        ));
        
        add_filter('cron_schedules', array(
            &$this, 
            'cron_schedules'
        ));
        
        if ($this->_config->get_boolean('pgcache.enabled')) {
            if ($this->_config->get_string('pgcache.engine') == 'file' || $this->_config->get_string('pgcache.engine') == 'file_pgcache') {
                add_action('w3_pgcache_cleanup', array(
                    &$this, 
                    'cleanup'
                ));
            }
            
            add_action('w3_pgcache_prime', array(
                &$this, 
                'prime'
            ));
            
            add_action('publish_phone', array(
                &$this, 
                'on_post_edit'
            ), 0);
            
            add_action('publish_post', array(
                &$this, 
                'on_post_edit'
            ), 0);
            
            add_action('edit_post', array(
                &$this, 
                'on_post_change'
            ), 0);
            
            add_action('delete_post', array(
                &$this, 
                'on_post_edit'
            ), 0);
            
            add_action('comment_post', array(
                &$this, 
                'on_comment_change'
            ), 0);
            
            add_action('edit_comment', array(
                &$this, 
                'on_comment_change'
            ), 0);
            
            add_action('delete_comment', array(
                &$this, 
                'on_comment_change'
            ), 0);
            
            add_action('wp_set_comment_status', array(
                &$this, 
                'on_comment_status'
            ), 0, 2);
            
            add_action('trackback_post', array(
                &$this, 
                'on_comment_change'
            ), 0);
            
            add_action('pingback_post', array(
                &$this, 
                'on_comment_change'
            ), 0);
            
            add_action('switch_theme', array(
                &$this, 
                'on_change'
            ), 0);
            
            add_action('edit_user_profile_update', array(
                &$this, 
                'on_change'
            ), 0);
        }
    }
    
    /**
     * Returns plugin instance
     *
     * @return W3_Plugin_PgCache
     */
    function &instance()
    {
        static $instances = array();
        
        if (!isset($instances[0])) {
            $class = __CLASS__;
            $instances[0] = & new $class();
        }
        
        return $instances[0];
    }
    
    /**
     * Activate plugin action
     */
    function activate()
    {
        if (!$this->update_wp_config()) {
            $activate_url = wp_nonce_url('plugins.php?action=activate&plugin=' . W3TC_FILE, 'activate-plugin_' . W3TC_FILE);
            $reactivate_button = sprintf('<input type="button" value="re-activate plugin" onclick="top.location.href = \'%s\'" />', addslashes($activate_url));
            $error = sprintf('<strong>%swp-config.php</strong> could not be written, please edit config and add:<br /><strong style="color:#f00;">define(\'WP_CACHE\', true);</strong> before <strong style="color:#f00;">require_once(ABSPATH . \'wp-settings.php\');</strong><br />then %s.', ABSPATH, $reactivate_button);
            
            w3_activate_error($error);
        }
        
        if ($this->_config->get_boolean('pgcache.enabled') && $this->_config->get_string('pgcache.engine') == 'file_pgcache') {
            /**
             * Disable enchanged mode if permalink structure is disabled
             */
            $permalink_structure = get_option('permalink_structure');
            
            if ($permalink_structure == '') {
                $this->_config->set('pgcache.engine', 'file');
                $this->_config->save();
            } else {
                if (!w3_is_multisite()) {
                    $this->write_rules_core();
                }
                
                $this->write_rules_cache();
            }
        }
        
        if (!$this->locked()) {
            if (@copy(W3TC_INSTALL_DIR . '/advanced-cache.php', WP_CONTENT_DIR . '/advanced-cache.php')) {
                @chmod(WP_CONTENT_DIR . '/advanced-cache.php', 0644);
            } else {
                w3_writable_error(WP_CONTENT_DIR . '/advanced-cache.php');
            }
        }
        
        $this->schedule();
        $this->schedule_prime();
    }
    
    /**
     * Deactivate plugin action
     */
    function deactivate()
    {
        $this->unschedule_prime();
        $this->unschedule();
        
        if (!$this->locked()) {
            @unlink(WP_CONTENT_DIR . '/advanced-cache.php');
        }
        
        $this->remove_rules_cache();
        
        if (!w3_is_multisite()) {
            $this->remove_rules_core();
        }
    }
    
    /**
     * Schedules events
     */
    function schedule()
    {
        if ($this->_config->get_boolean('pgcache.enabled') && ($this->_config->get_string('pgcache.engine') == 'file' || $this->_config->get_string('pgcache.engine') == 'file_pgcache')) {
            if (!wp_next_scheduled('w3_pgcache_cleanup')) {
                wp_schedule_event(time(), 'w3_pgcache_cleanup', 'w3_pgcache_cleanup');
            }
        } else {
            $this->unschedule();
        }
    }
    
    /**
     * Schedule prime event
     */
    function schedule_prime()
    {
        if ($this->_config->get_boolean('pgcache.enabled') && $this->_config->get_boolean('pgcache.prime.enabled')) {
            if (!wp_next_scheduled('w3_pgcache_prime')) {
                wp_schedule_event(time(), 'w3_pgcache_prime', 'w3_pgcache_prime');
            }
        } else {
            $this->unschedule_prime();
        }
    }
    
    /**
     * Unschedules events
     */
    function unschedule()
    {
        if (wp_next_scheduled('w3_pgcache_cleanup')) {
            wp_clear_scheduled_hook('w3_pgcache_cleanup');
        }
    }
    
    /**
     * Unschedules prime
     */
    function unschedule_prime()
    {
        if (wp_next_scheduled('w3_pgcache_prime')) {
            wp_clear_scheduled_hook('w3_pgcache_prime');
        }
    }
    
    /**
     * Updates WP config
     *
     * @return boolean
     */
    function update_wp_config()
    {
        static $updated = false;
        
        // added checking WP_CACHE value for WP3.0 compatibility
        if ((defined('WP_CACHE') && WP_CACHE) || $updated) {
            return true;
        }
        
        $config_path = ABSPATH . 'wp-config.php';
        $config_data = @file_get_contents($config_path);
        
        if (!$config_data) {
            return false;
        }
        
        $config_data = preg_replace('~<\?(php)?~', "\\0\r\n/** Enable W3 Total Cache **/\r\ndefine('WP_CACHE', true); // Added by W3 Total Cache\r\n", $config_data, 1);
        
        if (!@file_put_contents($config_path, $config_data)) {
            return false;
        }
        
        $updated = true;
        
        return true;
    }
    
    /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup()
    {
        $engine = $this->_config->get_string('pgcache.engine');
        
        switch ($engine) {
            case 'file':
                require_once W3TC_LIB_W3_DIR . '/Cache/File/Manager.php';
                
                $w3_cache_file_manager = & new W3_Cache_File_Manager(array(
                    'cache_dir' => W3TC_CACHE_FILE_PGCACHE_DIR
                ));
                
                $w3_cache_file_manager->clean();
                break;
            
            case 'file_pgcache':
                require_once W3TC_LIB_W3_DIR . '/Cache/File/PgCache/Manager.php';
                
                $w3_cache_file_pgcache_manager = & new W3_Cache_File_PgCache_Manager(array(
                    'cache_dir' => W3TC_CACHE_FILE_PGCACHE_DIR, 
                    'expire' => $this->_config->get_integer('browsercache.html.lifetime')
                ));
                
                $w3_cache_file_pgcache_manager->clean();
                break;
        }
    }
    
    /**
     * Prime cache
     * 
     * @param integer $start
     * @return void
     */
    function prime($start = 0)
    {
        /**
         * Don't start cache prime if queues are still scheduled
         */
        if ($start == 0) {
            $crons = _get_cron_array();
            
            foreach ($crons as $timestamp => $hooks) {
                foreach ($hooks as $hook => $keys) {
                    foreach ($keys as $key => $data) {
                        if ($hook == 'w3_pgcache_prime' && count($data['args'])) {
                            return;
                        }
                    }
                }
            }
        }
        
        $interval = $this->_config->get_integer('pgcache.prime.interval');
        $limit = $this->_config->get_integer('pgcache.prime.limit');
        $sitemap_url = $this->_config->get_string('pgcache.prime.sitemap');
        $sitemap_xml = w3_http_get($sitemap_url);
        
        $queue = array();
        
        /**
         * Parse XML sitemap
         */
        if ($sitemap_xml) {
            $url_matches = null;
            
            if (preg_match_all('~<url>(.*)</url>~Uis', $sitemap_xml, $url_matches)) {
                $loc_matches = null;
                $priority_matches = null;
                
                $locs = array();
                
                foreach ($url_matches[1] as $url_match) {
                    $loc = '';
                    $priority = 0;
                    
                    if (preg_match('~<loc>(.*)</loc>~is', $url_match, $loc_matches)) {
                        $loc = trim($loc_matches[1]);
                    }
                    
                    if (preg_match('~<priority>(.*)</priority>~is', $url_match, $priority_matches)) {
                        $priority = (double) trim($priority_matches[1]);
                    }
                    
                    if ($loc && $priority) {
                        $locs[$loc] = $priority;
                    }
                }
                
                arsort($locs);
                
                $queue = array_keys($locs);
            }
        }
        
        /**
         * Queue URLs
         */
        $urls = array_slice($queue, $start, $limit);
        
        if (count($queue) > ($start + $limit)) {
            wp_schedule_single_event(time() + $interval, 'w3_pgcache_prime', array(
                $start + $limit
            ));
        }
        
        /**
         * Make HTTP requests and prime cache
         */
        foreach ($urls as $url) {
            w3_http_get($url);
        }
    }
    
    /**
     * Cron schedules filter
     *
     * @paran array $schedules
     * @return array
     */
    function cron_schedules($schedules)
    {
        $gc_interval = $this->_config->get_integer('pgcache.file.gc');
        $prime_interval = $this->_config->get_integer('pgcache.prime.interval');
        
        return array_merge($schedules, array(
            'w3_pgcache_cleanup' => array(
                'interval' => $gc_interval, 
                'display' => sprintf('[W3TC] Page Cache file GC (every %d seconds)', $gc_interval)
            ), 
            'w3_pgcache_prime' => array(
                'interval' => $prime_interval, 
                'display' => sprintf('[W3TC] Page Cache prime (every %d seconds)', $prime_interval)
            )
        ));
    }
    
    /**
     * Post edit action
     *
     * @param integer $post_id
     */
    function on_post_edit($post_id)
    {
        if ($this->_config->get_boolean('pgcache.cache.flush')) {
            $this->on_change();
        } else {
            $this->on_post_change($post_id);
        }
    }
    
    /**
     * Post change action
     *
     * @param integer $post_id
     */
    function on_post_change($post_id)
    {
        static $flushed_posts = array();
        
        if (!in_array($post_id, $flushed_posts)) {
            require_once W3TC_LIB_W3_DIR . '/PgCache.php';
            
            $w3_pgcache = & W3_PgCache::instance();
            $w3_pgcache->flush_post($post_id);
            
            $flushed_posts[] = $post_id;
        }
    }
    
    /**
     * Comment change action
     *
     * @param integer $comment_id
     */
    function on_comment_change($comment_id)
    {
        $post_id = 0;
        
        if ($comment_id) {
            $comment = get_comment($comment_id, ARRAY_A);
            $post_id = !empty($comment['comment_post_ID']) ? (int) $comment['comment_post_ID'] : 0;
        }
        
        $this->on_post_change($post_id);
    }
    
    /**
     * Comment status action
     *
     * @param integer $comment_id
     * @param string $status
     */
    function on_comment_status($comment_id, $status)
    {
        if ($status === 'approve' || $status === '1') {
            $this->on_comment_change($comment_id);
        }
    }
    
    /**
     * Change action
     */
    function on_change()
    {
        static $flushed = false;
        
        if (!$flushed) {
            require_once W3TC_LIB_W3_DIR . '/PgCache.php';
            
            $w3_pgcache = & W3_PgCache::instance();
            $w3_pgcache->flush();
        }
    }
    
    /**
     * Generates rules for WP dir
     *
     * @return string
     */
    function generate_rules_core()
    {
        /**
         * Auto reject cookies
         */
        $reject_cookies = array(
            'comment_author', 
            'wp-postpass'
        );
        
        /**
         * Auto reject URIs
         */
        $reject_uris = array(
            '\/wp-admin\/', 
            '\/xmlrpc.php', 
            '\/wp-(app|cron|login|register|mail)\.php'
        );
        
        /**
         * Reject cache for logged in users
         */
        if ($this->_config->get_boolean('pgcache.reject.logged')) {
            $reject_cookies = array_merge($reject_cookies, array(
                'wordpress_[a-f0-9]+', 
                'wordpress_logged_in'
            ));
        }
        
        /**
         * Reject cache for home page
         */
        if (!$this->_config->get_boolean('pgcache.cache.home')) {
            $reject_uris[] = '^(\/|\/index.php)$';
        }
        
        /**
         * Reject cache for feeds
         */
        if (!$this->_config->get_boolean('pgcache.cache.feed')) {
            $reject_uris[] = '\/feed\/';
        }
        
        /**
         * Custom config
         */
        $reject_cookies = array_merge($reject_cookies, $this->_config->get_array('pgcache.reject.cookie'));
        $reject_uris = array_merge($reject_uris, $this->_config->get_array('pgcache.reject.uri'));
        $reject_user_agents = $this->_config->get_array('pgcache.reject.ua');
        $accept_files = $this->_config->get_array('pgcache.accept.files');
        
        /**
         * WPMU support
         */
        $is_multisite = w3_is_multisite();
        $is_vhost = w3_is_vhost();
        
        /**
         * Generate directives
         */
        $base_path = w3_get_base_path();
        $home_path = ($is_multisite ? $base_path : w3_get_home_path());
        
        $cache_dir = w3_path(W3TC_CACHE_FILE_PGCACHE_DIR);
        
        $rules = '';
        $rules .= "# BEGIN W3TC Page Cache\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "    RewriteEngine On\n";
        $rules .= "    RewriteBase " . $home_path . "\n";
        
        /**
         * Network mode rules
         */
        if ($is_multisite) {
            /**
             * Detect domain
             */
            $rules .= "    RewriteCond %{HTTP_HOST} ^(www\\.)?([a-z0-9\\-\\.]+\\.[a-z]+)\\.?(:[0-9]+)?$\n";
            $rules .= "    RewriteRule .* - [E=W3TC_DOMAIN:%2]\n";
            
            $replacement = '/w3tc-%{ENV:W3TC_DOMAIN}/';
            
            /**
             * If VHOST is off, detect blogname from URI
             */
            if (!$is_vhost) {
                $blognames = w3_get_blognames();
                
                if (count($blognames)) {
                    $rules .= "    RewriteCond %{REQUEST_URI} ^" . $base_path . "(" . implode('|', array_map('w3_preg_quote', $blognames)) . ")/\n";
                    $rules .= "    RewriteRule .* - [E=W3TC_BLOGNAME:%1.]\n";
                    
                    $replacement = '/w3tc-%{ENV:W3TC_BLOGNAME}%{ENV:W3TC_DOMAIN}/';
                }
            }
            
            $cache_dir = preg_replace('~/w3tc.*/~U', $replacement, $cache_dir, 1);
        }
        
        /**
         * Check mobile groups
         */
        if ($this->_config->get_boolean('mobile.enabled')) {
            $mobile_groups = array_reverse($this->_config->get_array('mobile.rgroups'));
            
            foreach ($mobile_groups as $mobile_group => $mobile_config) {
                $mobile_agents = (isset($mobile_config['agents']) ? (array) $mobile_config['agents'] : '');
                $mobile_redirect = (isset($mobile_config['redirect']) ? $mobile_config['redirect'] : '');
                
                if (count($mobile_agents) && !$mobile_redirect) {
                    $rules .= "    RewriteCond %{HTTP_USER_AGENT} (" . implode('|', $mobile_agents) . ") [NC]\n";
                    $rules .= "    RewriteRule .* - [E=W3TC_UA:_" . $mobile_group . "]\n";
                }
            }
        }
        
        /**
         * Check HTTPS
         */
        $rules .= "    RewriteCond %{HTTPS} =on\n";
        $rules .= "    RewriteRule .* - [E=W3TC_SSL:_ssl]\n";
        $rules .= "    RewriteCond %{SERVER_PORT} =443\n";
        $rules .= "    RewriteRule .* - [E=W3TC_SSL:_ssl]\n";
        
        /**
         * Check Accept-Encoding
         */
        if ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.html.compression')) {
            $rules .= "    RewriteCond %{HTTP:Accept-Encoding} gzip\n";
            $rules .= "    RewriteRule .* - [E=W3TC_ENC:.gzip]\n";
        }
        
        /**
         * Check for mobile redirect
         */
        if ($this->_config->get_boolean('mobile.enabled')) {
            $mobile_groups = $this->_config->get_array('mobile.rgroups');
            
            foreach ($mobile_groups as $mobile_group => $mobile_config) {
                $mobile_agents = (isset($mobile_config['agents']) ? (array) $mobile_config['agents'] : '');
                $mobile_redirect = (isset($mobile_config['redirect']) ? $mobile_config['redirect'] : '');
                
                if (count($mobile_agents) && $mobile_redirect) {
                    $rules .= "    RewriteCond %{HTTP_USER_AGENT} (" . implode('|', array_map('w3_preg_quote', $mobile_agents)) . ") [NC]\n";
                    $rules .= "    RewriteRule .* " . $mobile_redirect . " [R,L]\n";
                }
            }
        }
        
        /**
         * Don't accept POSTs
         */
        $rules .= "    RewriteCond %{REQUEST_METHOD} !=POST\n";
        
        /**
         * Query string should be empty
         */
        $rules .= "    RewriteCond %{QUERY_STRING} =\"\"\n";
        
        /**
         * Accept only URIs with trailing slash
         */
        $rules .= "    RewriteCond %{REQUEST_URI} \\/$\n";
        
        /**
         * Don't accept rejected URIs
         */
        $rules .= "    RewriteCond %{REQUEST_URI} !(" . implode('|', $reject_uris) . ")";
        
        /**
         * Exclude files from rejected URIs list
         */
        if (count($accept_files)) {
            $rules .= " [NC,OR]\n    RewriteCond %{REQUEST_URI} (" . implode('|', array_map('w3_preg_quote', $accept_files)) . ") [NC]\n";
        } else {
            $rules .= "\n";
        }
        
        /**
         * Check for rejected cookies
         */
        $rules .= "    RewriteCond %{HTTP_COOKIE} !(" . implode('|', array_map('w3_preg_quote', $reject_cookies)) . ") [NC]\n";
        
        /**
         * Check for rejected user agents
         */
        if (count($reject_user_agents)) {
            $rules .= "    RewriteCond %{HTTP_USER_AGENT} !(" . implode('|', array_map('w3_preg_quote', $reject_user_agents)) . ") [NC]\n";
        }
        
        /**
         * Check if cache file exists
         */
        $rules .= "    RewriteCond \"" . $cache_dir . $home_path . "$1/_index%{ENV:W3TC_UA}%{ENV:W3TC_SSL}.html%{ENV:W3TC_ENC}\" -f\n";
        
        /**
         * Make final rewrite
         */
        $rules .= "    RewriteRule (.*) \"" . $base_path . ltrim(str_replace(w3_get_site_root(), '', $cache_dir), '/') . $home_path . "$1/_index%{ENV:W3TC_UA}%{ENV:W3TC_SSL}.html%{ENV:W3TC_ENC}\" [L]\n";
        $rules .= "</IfModule>\n";
        
        $rules .= "# END W3TC Page Cache\n\n";
        
        return $rules;
    }
    
    /**
     * Generates directives for file cache dir
     *
     * @return string
     */
    function generate_rules_cache()
    {
        $charset = get_option('blog_charset');
        $browsercache = $this->_config->get_integer('browsercache.enabled');
        $compression = $this->_config->get_boolean('browsercache.html.compression');
        $expires = $this->_config->get_boolean('browsercache.html.expires');
        $lifetime = $this->_config->get_integer('browsercache.html.lifetime');
        
        $rules = '';
        $rules .= "# BEGIN W3TC Page Cache\n";
        
        if ($browsercache && $this->_config->get_integer('browsercache.html.etag')) {
            $rules .= "FileETag MTime Size\n";
        }
        
        $rules .= "AddDefaultCharset " . ($charset ? $charset : 'UTF-8') . "\n";
        
        if ($browsercache && $compression) {
            $rules .= "<IfModule mod_mime.c>\n";
            $rules .= "    AddType text/html .gzip\n";
            $rules .= "    AddEncoding gzip .gzip\n";
            $rules .= "</IfModule>\n";
            $rules .= "<IfModule mod_deflate.c>\n";
            $rules .= "    SetEnvIfNoCase Request_URI \\.gzip$ no-gzip\n";
            $rules .= "</IfModule>\n";
        }
        
        if ($browsercache && $expires && $lifetime) {
            $rules .= "<IfModule mod_expires.c>\n";
            $rules .= "    ExpiresActive On\n";
            $rules .= "    ExpiresByType text/html M" . $lifetime . "\n";
            $rules .= "</IfModule>\n";
        }
        
        $rules .= "<IfModule mod_headers.c>\n";
        $rules .= "    Header set X-Pingback \"" . get_bloginfo('pingback_url') . "\"\n";
        
        if ($browsercache && $this->_config->get_integer('browsercache.html.w3tc')) {
            $rules .= "    Header set X-Powered-By \"" . W3TC_POWERED_BY . "\"\n";
        }
        
        if ($browsercache && $compression) {
            $rules .= "    Header set Vary \"Accept-Encoding, Cookie\"\n";
        } else {
            $rules .= "    Header set Vary \"Cookie\"\n";
        }
        
        if ($this->_config->get_boolean('browsercache.html.cache.control')) {
            switch ($this->_config->get_string('browsercache.html.cache.policy')) {
                case 'cache':
                    $rules .= "    Header set Pragma \"public\"\n";
                    $rules .= "    Header set Cache-Control \"public\"\n";
                    break;
                
                case 'cache_validation':
                    $rules .= "    Header set Pragma \"public\"\n";
                    $rules .= "    Header set Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                    break;
                
                case 'cache_noproxy':
                    $rules .= "    Header set Pragma \"public\"\n";
                    $rules .= "    Header set Cache-Control \"public, must-revalidate\"\n";
                    break;
                
                case 'cache_maxage':
                    $rules .= "    Header set Pragma \"public\"\n";
                    
                    if ($expires) {
                        $rules .= "    Header append Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                    } else {
                        $rules .= "    Header set Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\"\n";
                    }
                    break;
                
                case 'no_cache':
                    $rules .= "    Header set Pragma \"no-cache\"\n";
                    $rules .= "    Header set Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\"\n";
                    break;
            }
        }
        
        $rules .= "</IfModule>\n";
        $rules .= "# END W3TC Page Cache\n";
        
        return $rules;
    }
    
    /**
     * Writes directives to WP .htaccess
     *
     * @return boolean
     */
    function write_rules_core()
    {
        $path = w3_get_home_root() . '/.htaccess';
        
        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_w3tc($data);
                $data = $this->erase_rules_wpsc($data);
            } else {
                return false;
            }
        } else {
            $data = '';
        }
        
        $w3tc_rules = $this->generate_rules_core();
        
        $wp_rules_pos = strpos($data, '# BEGIN WordPress');
        
        if ($wp_rules_pos !== false) {
            $data = trim(substr_replace($data, $w3tc_rules, $wp_rules_pos, 0));
        } else {
            $data = trim($w3tc_rules . $data);
        }
        
        return @file_put_contents($path, $data);
    }
    
    /**
     * Writes directives to file cache .htaccess
     *
     * @return boolean
     */
    function write_rules_cache()
    {
        $path = W3TC_CACHE_FILE_PGCACHE_DIR . '/.htaccess';
        
        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_w3tc($data);
            } else {
                return false;
            }
        } else {
            $data = '';
        }
        
        $data = trim($this->generate_rules_cache() . $data);
        
        return @file_put_contents($path, $data);
    }
    
    /**
     * Erases W3TC directives from config
     *
     * @param string $data
     * @return string
     */
    function erase_rules_w3tc($data)
    {
        $data = w3_erase_text($data, '# BEGIN W3TC Page Cache', '# END W3TC Page Cache');
        
        return $data;
    }
    
    /**
     * Erases WP Super Cache rules directives config
     *
     * @param string $data
     * @return string
     */
    function erase_rules_wpsc($data)
    {
        $data = w3_erase_text($data, '# BEGIN WPSuperCache', '# END WPSuperCache');
        
        return $data;
    }
    
    /**
     * Removes W3TC directives from WP .htaccess
     *
     * @return boolean
     */
    function remove_rules_core()
    {
        $path = w3_get_home_root() . '/.htaccess';
        
        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_w3tc($data);
                
                return @file_put_contents($path, $data);
            }
        } else {
            return true;
        }
        
        return false;
    }
    
    /**
     * Removes W3TC directives from file cache dir
     *
     * @return boolean
     */
    function remove_rules_cache()
    {
        $path = W3TC_CACHE_FILE_PGCACHE_DIR . '/.htaccess';
        
        return @unlink($path);
    }
    
    /**
     * Checks core directives
     *
     * @return boolean
     */
    function check_rules_core()
    {
        $path = w3_get_home_root() . '/.htaccess';
        $search = $this->generate_rules_core();
        
        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }
    
    /**
     * Checks cache directives
     *
     * @return boolean
     */
    function check_rules_cache()
    {
        $path = W3TC_CACHE_FILE_PGCACHE_DIR . '/.htaccess';
        $search = $this->generate_rules_cache();
        
        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }
}
