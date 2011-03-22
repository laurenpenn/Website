<?php

/**
 * W3 Total Cache plugin
 */
require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_TotalCache
 */
class W3_Plugin_TotalCache extends W3_Plugin
{
    /**
     * Current page
     * @var string
     */
    var $_page = 'w3tc_general';
    
    /**
     * Notes
     * @var array
     */
    var $_notes = array();
    
    /**
     * Errors
     * @var array
     */
    var $_errors = array();
    
    /**
     * Show support reminder flag
     * @var boolean
     */
    var $_support_reminder = false;
    
    /**
     * Used in PHPMailer init function
     * @var string
     */
    var $_phpmailer_sender = '';
    
    /**
     * Array of request types
     * @var array
     */
    var $_request_types = array(
        'bug_report' => 'Submit a Bug Report', 
        'new_feature' => 'Suggest a New Feature', 
        'email_support' => '<15 Minute Email Support Response (M-F 9AM - 5PM EDT): $75 USD', 
        'phone_support' => '<15 Minute Phone Support Response (M-F 9AM - 5PM EDT): $150 USD', 
        'plugin_config' => 'Professional Plugin Configuration: Starting @ $100 USD', 
        'theme_config' => 'Theme Performance Optimization & Plugin Configuration: Starting @ $150 USD', 
        'linux_config' => 'Linux Server Optimization & Plugin Configuration: Starting @ $200 USD'
    );
    
    /**
     * Array of request groups
     * @var array
     */
    var $_request_groups = array(
        'General Support' => array(
            'bug_report', 
            'new_feature'
        ), 
        'Professional Services (per site pricing)' => array(
            'email_support', 
            'phone_support', 
            'plugin_config', 
            'theme_config', 
            'linux_config'
        )
    );
    
    /**
     * Request price list
     * @var array
     */
    var $_request_prices = array(
        'email_support' => 75, 
        'phone_support' => 150, 
        'plugin_config' => 100, 
        'theme_config' => 150, 
        'linux_config' => 200
    );
    
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
        
        add_action('admin_init', array(
            &$this, 
            'admin_init'
        ));
        
        add_action('admin_menu', array(
            &$this, 
            'admin_menu'
        ));
        
        add_filter('contextual_help_list', array(
            &$this, 
            'contextual_help_list'
        ));
        
        add_filter('plugin_action_links_' . W3TC_FILE, array(
            &$this, 
            'plugin_action_links'
        ));
        
        add_filter('favorite_actions', array(
            &$this, 
            'favorite_actions'
        ));
        
        add_action('init', array(
            &$this, 
            'init'
        ));
        
        add_action('in_plugin_update_message-' . W3TC_FILE, array(
            &$this, 
            'in_plugin_update_message'
        ));
        
        if ($this->_config->get_boolean('widget.latest.enabled')) {
            add_action('wp_dashboard_setup', array(
                &$this, 
                'wp_dashboard_setup'
            ));
        }
        
        if ($this->_config->get_boolean('pgcache.enabled') || $this->_config->get_boolean('minify.enabled')) {
            add_filter('pre_update_option_active_plugins', array(
                &$this, 
                'pre_update_option_active_plugins'
            ));
        }
        
        if ($this->_config->get_boolean('cdn.enabled') && $this->_config->get_string('cdn.engine') != 'mirror') {
            add_filter('media_row_actions', array(
                &$this, 
                'media_row_actions'
            ), null, 2);
        }
        
        if ($this->_config->get_boolean('pgcache.enabled')) {
            add_filter('post_row_actions', array(
                &$this, 
                'post_row_actions'
            ), null, 2);
        }
        
        if (isset($_REQUEST['w3tc_theme']) && isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] == W3TC_POWERED_BY) {
            add_filter('template', array(
                &$this, 
                'template_preview'
            ));
            
            add_filter('stylesheet', array(
                &$this, 
                'stylesheet_preview'
            ));
        } elseif ($this->_config->get_boolean('mobile.enabled')) {
            add_filter('template', array(
                &$this, 
                'template'
            ));
            
            add_filter('stylesheet', array(
                &$this, 
                'stylesheet'
            ));
        }
        
        if ($this->_config->get_string('common.support') == 'footer') {
            add_action('wp_footer', array(
                &$this, 
                'footer'
            ));
        }
        
        ob_start(array(
            &$this, 
            'ob_callback'
        ));
        
        /**
         * Run DbCache plugin
         */
        require_once W3TC_DIR . '/lib/W3/Plugin/DbCache.php';
        $w3_plugin_dbcache = & W3_Plugin_DbCache::instance();
        $w3_plugin_dbcache->run();
        
        /**
         * Run ObjectCache plugin
         */
        require_once W3TC_DIR . '/lib/W3/Plugin/ObjectCache.php';
        $w3_plugin_objectcache = & W3_Plugin_ObjectCache::instance();
        $w3_plugin_objectcache->run();
        
        /**
         * Run PgCache plugin
         */
        require_once W3TC_DIR . '/lib/W3/Plugin/PgCache.php';
        $w3_plugin_pgcache = & W3_Plugin_PgCache::instance();
        $w3_plugin_pgcache->run();
        
        /**
         * Run CDN plugin
         */
        require_once W3TC_DIR . '/lib/W3/Plugin/Cdn.php';
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        $w3_plugin_cdn->run();
        
        /**
         * Run Minify plugin
         */
        if (W3TC_PHP5) {
            require_once W3TC_DIR . '/lib/W3/Plugin/Minify.php';
            $w3_plugin_minify = & W3_Plugin_Minify::instance();
            $w3_plugin_minify->run();
        }
        
        /**
         * Run BrowserCache plugin
         */
        require_once W3TC_DIR . '/lib/W3/Plugin/BrowserCache.php';
        $w3_plugin_browsercache = & W3_Plugin_BrowserCache::instance();
        $w3_plugin_browsercache->run();
    }
    
    /**
     * Returns plugin instance
     *
     * @return W3_Plugin_TotalCache
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
        /**
         * Disable buggy sitewide activation in WPMU and WP 3.0
         */
        if ((w3_is_wpmu() && isset($_GET['sitewide'])) || (w3_is_network_mode() && isset($_GET['networkwide']))) {
            w3_network_activate_error();
        }
        
        /**
         * Check installation files
         */
        $files = array(
            W3TC_INSTALL_DIR . '/db.php', 
            W3TC_INSTALL_DIR . '/advanced-cache.php', 
            W3TC_INSTALL_DIR . '/object-cache.php'
        );
        
        $nonexistent_files = array();
        
        foreach ($files as $file) {
            if (!file_exists($file)) {
                $nonexistent_files[] = $file;
            }
        }
        
        if (count($nonexistent_files)) {
            $error = sprintf('Unfortunately core file(s): (<strong>%s</strong>) are missing, so activation will fail. Please re-start the installation process from the beginning.', implode(', ', $nonexistent_files));
            
            w3_activate_error($error);
        }
        
        if (!@is_dir(W3TC_CONTENT_DIR)) {
            if (@mkdir(W3TC_CONTENT_DIR, 0755)) {
                @chmod(W3TC_CONTENT_DIR, 0755);
            } else {
                w3_writable_error(W3TC_CONTENT_DIR);
            }
        }
        
        if (!@is_dir(W3TC_CACHE_FILE_DBCACHE_DIR)) {
            if (@mkdir(W3TC_CACHE_FILE_DBCACHE_DIR, 0755)) {
                @chmod(W3TC_CACHE_FILE_DBCACHE_DIR, 0755);
            } else {
                w3_writable_error(W3TC_CACHE_FILE_DBCACHE_DIR);
            }
        }
        
        if (!@is_dir(W3TC_CACHE_FILE_OBJECTCACHE_DIR)) {
            if (@mkdir(W3TC_CACHE_FILE_OBJECTCACHE_DIR, 0755)) {
                @chmod(W3TC_CACHE_FILE_OBJECTCACHE_DIR, 0755);
            } else {
                w3_writable_error(W3TC_CACHE_FILE_OBJECTCACHE_DIR);
            }
        }
        
        if (!@is_dir(W3TC_CACHE_FILE_PGCACHE_DIR)) {
            if (@mkdir(W3TC_CACHE_FILE_PGCACHE_DIR, 0755)) {
                @chmod(W3TC_CACHE_FILE_PGCACHE_DIR, 0755);
            } else {
                w3_writable_error(W3TC_CACHE_FILE_PGCACHE_DIR);
            }
        }
        
        if (!@is_dir(W3TC_CACHE_FILE_MINIFY_DIR)) {
            if (@mkdir(W3TC_CACHE_FILE_MINIFY_DIR, 0755)) {
                @chmod(W3TC_CACHE_FILE_MINIFY_DIR, 0755);
            } else {
                w3_writable_error(W3TC_CACHE_FILE_MINIFY_DIR);
            }
        }
        
        if (!@is_dir(W3TC_LOG_DIR)) {
            if (@mkdir(W3TC_LOG_DIR, 0755)) {
                @chmod(W3TC_LOG_DIR, 0755);
            } else {
                w3_writable_error(W3TC_LOG_DIR);
            }
        }
        
        if (!@is_dir(W3TC_TMP_DIR)) {
            if (@mkdir(W3TC_TMP_DIR, 0755)) {
                @chmod(W3TC_TMP_DIR, 0755);
            } else {
                w3_writable_error(W3TC_TMP_DIR);
            }
        }
        
        if (w3_is_multisite() && file_exists(W3TC_CONFIG_MASTER_PATH)) {
            /**
             * For multisite load master config
             */
            $this->_config->load_master();
            
            if (!$this->_config->save(false)) {
                w3_writable_error(W3TC_CONFIG_PATH);
            }
        } elseif (!file_exists(W3TC_CONFIG_PATH)) {
            /**
             * Set default settings
             */
            $this->_config->set_defaults();
            
            /**
             * If config doesn't exist enable preview mode
             */
            if (!$this->_config->save(true)) {
                w3_writable_error(W3TC_CONFIG_PREVIEW_PATH);
            }
        }
        
        /**
         * Save blognames into file
         */
        if (w3_is_multisite() && !w3_is_vhost()) {
            if (!w3_save_blognames()) {
                w3_writable_error(W3TC_BLOGNAMES_PATH);
            }
        }
        
        delete_option('w3tc_request_data');
        add_option('w3tc_request_data', '', null, 'no');
        
        $this->link_update();
    }
    
    /**
     * Deactivate plugin action
     */
    function deactivate()
    {
        $this->link_delete();
        
        delete_option('w3tc_request_data');
        
        // keep for other blogs
        if (!$this->locked()) {
            @unlink(W3TC_BLOGNAMES_PATH);
        }
        
        @unlink(W3TC_CONFIG_PREVIEW_PATH);
        
        w3_rmdir(W3TC_TMP_DIR);
        w3_rmdir(W3TC_LOG_DIR);
        w3_rmdir(W3TC_CACHE_FILE_MINIFY_DIR);
        w3_rmdir(W3TC_CACHE_FILE_PGCACHE_DIR);
        w3_rmdir(W3TC_CACHE_FILE_DBCACHE_DIR);
        w3_rmdir(W3TC_CACHE_FILE_OBJECTCACHE_DIR);
        w3_rmdir(W3TC_CONTENT_DIR);
    }
    
    /**
     * Init action
     */
    function init()
    {
        $this->check_request();
    }
    
    /**
     * Load action
     */
    function load()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $this->_page = W3_Request::get_string('page');
        
        switch (true) {
            case ($this->_page == 'w3tc_general'):
            case ($this->_page == 'w3tc_pgcache'):
            case ($this->_page == 'w3tc_minify' && W3TC_PHP5):
            case ($this->_page == 'w3tc_dbcache'):
            case ($this->_page == 'w3tc_objectcache'):
            case ($this->_page == 'w3tc_browsercache'):
            case ($this->_page == 'w3tc_mobile'):
            case ($this->_page == 'w3tc_cdn'):
            case ($this->_page == 'w3tc_install'):
            case ($this->_page == 'w3tc_faq'):
            case ($this->_page == 'w3tc_about'):
            case ($this->_page == 'w3tc_support'):
                break;
            
            default:
                $this->_page = 'w3tc_general';
        }
        
        /**
         * Flush all caches
         */
        if (isset($_REQUEST['flush_all'])) {
            $this->flush_all();
            
            $this->redirect(array(
                'w3tc_note' => 'flush_all'
            ), true);
        }
        
        /**
         * Flush memcached cache
         */
        if (isset($_REQUEST['flush_memcached'])) {
            $this->flush_memcached();
            
            $this->redirect(array(
                'w3tc_note' => 'flush_memcached'
            ), true);
        }
        
        /**
         * Flush APC cache
         */
        if (isset($_REQUEST['flush_opcode'])) {
            $this->flush_opcode();
            
            $this->redirect(array(
                'w3tc_note' => 'flush_opcode'
            ), true);
        }
        
        /**
         * Flush disk cache
         */
        if (isset($_REQUEST['flush_file'])) {
            $this->flush_file();
            
            $this->redirect(array(
                'w3tc_note' => 'flush_file'
            ), true);
        }
        
        /**
         * Flush page cache
         */
        if (isset($_REQUEST['flush_pgcache'])) {
            $this->flush_pgcache();
            
            $this->_config->set('notes.need_empty_pgcache', false);
            $this->_config->set('notes.plugins_updated', false);
            
            if (!$this->_config->save()) {
                $this->redirect(array(
                    'w3tc_error' => 'config_save'
                ), true);
            }
            
            $this->redirect(array(
                'w3tc_note' => 'flush_pgcache'
            ), true);
        }
        
        /**
         * Flush db cache
         */
        if (isset($_REQUEST['flush_dbcache'])) {
            $this->flush_dbcache();
            
            $this->redirect(array(
                'w3tc_note' => 'flush_dbcache'
            ), true);
        }
        
        /**
         * Flush object cache
         */
        if (isset($_REQUEST['flush_objectcache'])) {
            $this->flush_objectcache();
            
            $this->redirect(array(
                'w3tc_note' => 'flush_objectcache'
            ), true);
        }
        
        /**
         * Flush minify cache
         */
        if (isset($_REQUEST['flush_minify'])) {
            $this->flush_minify();
            
            $this->_config->set('notes.need_empty_minify', false);
            
            if (!$this->_config->save()) {
                $this->redirect(array(
                    'w3tc_error' => 'config_save'
                ), true);
            }
            
            $this->redirect(array(
                'w3tc_note' => 'flush_minify'
            ), true);
        }
        
        /**
         * Hide notes
         */
        if (isset($_REQUEST['hide_note'])) {
            $setting = sprintf('notes.%s', W3_Request::get_string('hide_note'));
            
            $this->_config->set($setting, false);
            
            if (!$this->_config->save()) {
                $this->redirect(array(
                    'w3tc_error' => 'config_save'
                ), true);
            }
            
            $this->redirect(array(), true);
        }
        
        /**
         * Save config
         */
        if (isset($_REQUEST['save_config'])) {
            if ($this->_config->save()) {
                $this->redirect(array(
                    'w3tc_note' => 'config_save'
                ), true);
            } else {
                $this->redirect(array(
                    'w3tc_error' => 'config_save'
                ), true);
            }
        }
        
        /**
         * Write page cache rules
         */
        if (isset($_REQUEST['pgcache_write_rules_core'])) {
            require_once W3TC_LIB_W3_DIR . '/Plugin/PgCache.php';
            $w3_plugin_pgcache = & W3_Plugin_PgCache::instance();
            
            if ($w3_plugin_pgcache->write_rules_core()) {
                $this->redirect(array(
                    'w3tc_note' => 'pgcache_write_rules_core'
                ));
            } else {
                $this->redirect(array(
                    'w3tc_error' => 'pgcache_write_rules_core'
                ));
            }
        }
        
        if (isset($_REQUEST['pgcache_write_rules_cache'])) {
            require_once W3TC_LIB_W3_DIR . '/Plugin/PgCache.php';
            $w3_plugin_pgcache = & W3_Plugin_PgCache::instance();
            
            if ($w3_plugin_pgcache->write_rules_cache()) {
                $this->redirect(array(
                    'w3tc_note' => 'pgcache_write_rules_cache'
                ));
            } else {
                $this->redirect(array(
                    'w3tc_error' => 'pgcache_write_rules_cache'
                ));
            }
        }
        
        /**
         * Write browser cache rules
         */
        if (isset($_REQUEST['browsercache_write_rules_cache'])) {
            require_once W3TC_LIB_W3_DIR . '/Plugin/BrowserCache.php';
            $w3_plugin_browsercache = & W3_Plugin_BrowserCache::instance();
            
            if ($w3_plugin_browsercache->write_rules_cache()) {
                $this->redirect(array(
                    'w3tc_note' => 'browsercache_write_rules_cache'
                ));
            } else {
                $this->redirect(array(
                    'w3tc_error' => 'browsercache_write_rules_cache'
                ));
            }
        }
        
        if (isset($_REQUEST['browsercache_write_rules_no404wp'])) {
            require_once W3TC_LIB_W3_DIR . '/Plugin/BrowserCache.php';
            $w3_plugin_browsercache = & W3_Plugin_BrowserCache::instance();
            
            if ($w3_plugin_browsercache->write_rules_no404wp()) {
                $this->redirect(array(
                    'w3tc_note' => 'browsercache_write_rules_no404wp'
                ));
            } else {
                $this->redirect(array(
                    'w3tc_error' => 'browsercache_write_rules_no404wp'
                ));
            }
        }
        
        /**
         * Write minify rules
         */
        if (W3TC_PHP5 && isset($_REQUEST['minify_write_rules'])) {
            require_once W3TC_LIB_W3_DIR . '/Plugin/Minify.php';
            $w3_plugin_minify = & W3_Plugin_Minify::instance();
            
            if ($w3_plugin_minify->write_rules()) {
                $this->redirect(array(
                    'w3tc_note' => 'minify_write_rules'
                ));
            } else {
                $this->redirect(array(
                    'w3tc_error' => 'minify_write_rules'
                ));
            }
        }
        
        /**
         * Save support us options
         */
        if (isset($_REQUEST['save_support_us'])) {
            $support = W3_Request::get_string('support');
            
            $this->_config->set('common.support', $support);
            
            if (!$this->_config->save()) {
                $this->redirect(array(
                    'w3tc_error' => 'config_save'
                ));
            }
            
            $this->link_update();
            
            $this->redirect(array(
                'w3tc_note' => 'config_save'
            ));
        }
        
        /**
         * Import config action
         */
        if (isset($_REQUEST['config_import'])) {
            $this->config_import();
        }
        
        /**
         * Export config action
         */
        if (isset($_REQUEST['config_export'])) {
            $this->config_export();
        }
        
        /**
         * Reset config action
         */
        if (isset($_REQUEST['config_reset'])) {
            $this->config_reset();
        }
        
        /**
         * Support select action
         */
        if (isset($_REQUEST['support_select'])) {
            $this->support_select();
        }
        
        /**
         * Deploy preview settings
         */
        if (isset($_REQUEST['preview_deploy'])) {
            $this->preview_deploy();
        }
        
        /**
         * Run plugin action
         */
        if (isset($_REQUEST['w3tc_action'])) {
            $action = trim($_REQUEST['w3tc_action']);
            
            if (method_exists($this, $action)) {
                call_user_func(array(
                    &$this, 
                    $action
                ));
                exit();
            }
        }
        
        /**
         * Save options
         */
        if (isset($_REQUEST['options_save'])) {
            $this->options_save();
        }
        
        /**
         * Save preview mode
         */
        if (isset($_REQUEST['preview_save'])) {
            $this->preview_save();
        }
        
        /**
         * Support request
         */
        if (isset($_REQUEST['support_request'])) {
            $this->support_request();
        }
        
        /**
         * CDN Purge
         */
        if (isset($_REQUEST['cdn_purge_attachment'])) {
            $this->cdn_purge_attachment();
        }
        
        /**
         * PgCache purge
         */
        if (isset($_REQUEST['pgcache_purge_post'])) {
            $this->pgcache_purge_post();
        }
        
        $this->_support_reminder = ($this->_config->get_boolean('notes.support_us') && $this->_config->get_integer('common.install') < (time() - W3TC_SUPPORT_US_TIMEOUT) && !$this->is_supported());
    }
    
    /**
     * Dashboard setup action
     */
    function wp_dashboard_setup()
    {
        wp_add_dashboard_widget('w3tc_latest', 'The Latest from W3 EDGE', array(
            &$this, 
            'widget_latest'
        ), array(
            &$this, 
            'widget_latest_control'
        ));
    }
    
    /**
     * Prints latest widget contents
     */
    function widget_latest()
    {
        global $wp_version;
        
        $items = array();
        $items_count = $this->_config->get_integer('widget.latest.items');
        
        if ($wp_version >= 2.8) {
            include_once (ABSPATH . WPINC . '/feed.php');
            $feed = fetch_feed(W3TC_FEED_URL);
            
            if (!is_wp_error($feed)) {
                $feed_items = $feed->get_items(0, $items_count);
                
                foreach ($feed_items as $feed_item) {
                    $items[] = array(
                        'link' => $feed_item->get_link(), 
                        'title' => $feed_item->get_title(), 
                        'description' => $feed_item->get_description()
                    );
                }
            }
        } else {
            include_once (ABSPATH . WPINC . '/rss.php');
            $rss = fetch_rss(W3TC_FEED_URL);
            
            if (is_object($rss)) {
                $items = array_slice($rss->items, 0, $items_count);
            }
        }
        
        include W3TC_DIR . '/inc/widget/latest.phtml';
    }
    
    /**
     * Latest widget control
     */
    function widget_latest_control($widget_id, $form_inputs = array())
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once W3TC_LIB_W3_DIR . '/Request.php';
            
            $this->_config->set('widget.latest.items', W3_Request::get_integer('w3tc_latest_items', 3));
            $this->_config->save();
        } else {
            include W3TC_DIR . '/inc/widget/latest_control.phtml';
        }
    }
    
    /**
     * Admin init
     */
    function admin_init()
    {
        wp_register_style('w3tc-options', WP_PLUGIN_URL . '/w3-total-cache/inc/css/options.css');
        wp_register_style('w3tc-lightbox', WP_PLUGIN_URL . '/w3-total-cache/inc/css/lightbox.css');
        
        wp_register_script('w3tc-options', WP_PLUGIN_URL . '/w3-total-cache/inc/js/options.js');
        wp_register_script('w3tc-lightbox', WP_PLUGIN_URL . '/w3-total-cache/inc/js/lightbox.js');
    }
    
    /**
     * Admin menu
     */
    function admin_menu()
    {
        $pages = array(
            'w3tc_general' => array(
                'General Settings', 
                'General Settings'
            ), 
            'w3tc_pgcache' => array(
                'Page Cache', 
                'Page Cache'
            ), 
            'w3tc_minify' => array(
                'Minify', 
                'Minify'
            ), 
            'w3tc_dbcache' => array(
                'Database Cache', 
                'Database Cache'
            ), 
            'w3tc_objectcache' => array(
                'Object Cache', 
                'Object Cache'
            ), 
            'w3tc_browsercache' => array(
                'Browser Cache', 
                'Browser Cache'
            ), 
            'w3tc_mobile' => array(
                'User Agent Groups', 
                'User Agent Groups'
            ), 
            'w3tc_cdn' => array(
                'Content Delivery Network', 
                '<acronym title="Content Delivery Network">CDN</acronym>'
            ), 
            'w3tc_faq' => array(
                'FAQ', 
                'FAQ'
            ), 
            'w3tc_support' => array(
                'Support', 
                '<span style="color: red;">Support</span>'
            ), 
            'w3tc_install' => array(
                'Install', 
                'Install'
            ), 
            'w3tc_about' => array(
                'About', 
                'About'
            )
        );
        
        if (!W3TC_PHP5) {
            unset($pages['minify']);
        }
        
        add_menu_page('Performance', 'Performance', 'manage_options', 'w3tc_general', '', plugins_url('w3-total-cache/inc/images/logo_small.png'));
        
        $submenu_pages = array();
        
        foreach ($pages as $slug => $titles) {
            $submenu_pages[] = add_submenu_page('w3tc_general', $titles[0] . ' | W3 Total Cache', $titles[1], 'manage_options', $slug, array(
                &$this, 
                'options'
            ));
        }
        
        if (current_user_can('manage_options')) {
            
            /**
             * Only admin can modify W3TC settings
             */
            foreach ($submenu_pages as $submenu_page) {
                add_action('load-' . $submenu_page, array(
                    &$this, 
                    'load'
                ));
                
                add_action('admin_print_styles-' . $submenu_page, array(
                    &$this, 
                    'admin_print_styles'
                ));
                
                add_action('admin_print_scripts-' . $submenu_page, array(
                    &$this, 
                    'admin_print_scripts'
                ));
            }
            
            /**
             * Only admin can see W3TC notices and errors
             */
            add_action('admin_notices', array(
                &$this, 
                'admin_notices'
            ));
        }
    }
    
    /**
     * Print styles
     */
    function admin_print_styles()
    {
        wp_enqueue_style('w3tc-options');
        wp_enqueue_style('w3tc-lightbox');
    }
    
    /**
     * Print scripts
     */
    function admin_print_scripts()
    {
        wp_enqueue_script('w3tc-options');
        wp_enqueue_script('w3tc-lightbox');
        
        switch ($this->_page) {
            case 'w3tc_minify':
            case 'w3tc_mobile':
            case 'w3tc_cdn':
                wp_enqueue_script('jquery-ui-sortable');
                break;
        }
    }
    
    /**
     * Contextual help list filter
     * 
     * @param string $list
     * @return string
     */
    function contextual_help_list($list)
    {
        $faq = $this->parse_faq();
        
        if (isset($faq['Usage'])) {
            $columns = array_chunk($faq['Usage'], ceil(count($faq['Usage']) / 3));
            
            ob_start();
            include W3TC_DIR . '/inc/options/common/help.phtml';
            $help = ob_get_contents();
            ob_end_clean();
            
            $hook = get_plugin_page_hookname($this->_page, 'w3tc_general');
            
            $list[$hook] = $help;
        }
        
        return $list;
    }
    
    /**
     * Plugin action links filter
     *
     * @return array
     */
    function plugin_action_links($links)
    {
        array_unshift($links, '<a class="edit" href="admin.php?page=w3tc_general">Settings</a>');
        
        return $links;
    }
    
    /**
     * favorite_actions filter
     */
    function favorite_actions($actions)
    {
        $actions['admin.php?page=w3tc_general&amp;flush_all'] = array(
            'Empty Caches', 
            'manage_options'
        );
        
        return $actions;
    }
    
    /**
     * Check request and handle w3tc_request_data requests
     */
    function check_request()
    {
        $pos = strpos($_SERVER['REQUEST_URI'], '/w3tc_request_data/');
        
        if ($pos !== false) {
            $hash = substr($_SERVER['REQUEST_URI'], $pos + 19, 32);
            
            if (strlen($hash) == 32) {
                $request_data = (array) get_option('w3tc_request_data');
                
                if (isset($request_data[$hash])) {
                    echo '<pre>';
                    foreach ($request_data[$hash] as $key => $value) {
                        printf("%s: %s\n", $key, $value);
                    }
                    echo '</pre>';
                    
                    unset($request_data[$hash]);
                    update_option('w3tc_request_data', $request_data);
                } else {
                    echo 'Requested hash expired or invalid';
                }
                
                exit();
            }
        }
    }
    
    /**
     * Admin notices action
     */
    function admin_notices()
    {
        $home_root = w3_get_home_root();
        $document_root = w3_get_document_root();
        $config_path = (w3_is_preview_config() ? W3TC_CONFIG_PREVIEW_PATH : W3TC_CONFIG_PATH);
        
        $pgcache_rules_core_path = $home_root . '/.htaccess';
        $pgcache_rules_cache_path = W3TC_CACHE_FILE_PGCACHE_DIR . '/.htaccess';
        $browsercache_rules_cache_path = $document_root . '/.htaccess';
        $browsercache_rules_no404wp_path = $home_root . '/.htaccess';
        $minify_rules_path = W3TC_CACHE_FILE_MINIFY_DIR . '/.htaccess';
        
        $error_messages = array(
            'config_save' => sprintf('The settings could not be saved because the config file is not write-able. Please run <strong>chmod 777 %s</strong> to resolve this issue.', (file_exists($config_path) ? $config_path : dirname($config_path))), 
            'fancy_permalinks_disabled_pgcache' => sprintf('Fancy permalinks are disabled. Please %s it first, then re-attempt to enabling the enhanced disk mode.', $this->button_link('enable', 'options-permalink.php')), 
            'fancy_permalinks_disabled_browsercache' => sprintf('Fancy permalinks are disabled. Please %s it first, then re-attempt to enabling the \'Do not process 404 errors for static objects with WordPress\'.', $this->button_link('enable', 'options-permalink.php')), 
            'pgcache_write_rules_core' => sprintf('The page cache rules could not be modified. Please %srun <strong>chmod 777 %s</strong> to resolve this issue.', (file_exists($pgcache_rules_core_path) ? '' : sprintf('create an empty file in <strong>%s</strong> and ', $pgcache_rules_core_path)), $pgcache_rules_core_path), 
            'pgcache_write_rules_cache' => sprintf('The page cache rules could not be modified. Please run <strong>chmod 777 %s</strong> to resolve this issue.', (file_exists($pgcache_rules_cache_path) ? $pgcache_rules_cache_path : dirname($pgcache_rules_cache_path))), 
            'browsercache_write_rules_cache' => sprintf('The browser cache rules could not be modified. Please %srun <strong>chmod 777 %s</strong> to resolve this issue.', (file_exists($browsercache_rules_cache_path) ? '' : sprintf('create an empty file in <strong>%s</strong> and ', $browsercache_rules_cache_path)), $browsercache_rules_cache_path), 
            'browsercache_write_rules_no404wp' => sprintf('The browser cache rules could not be modified. Please %srun <strong>chmod 777 %s</strong> to resolve this issue.', (file_exists($browsercache_rules_no404wp_path) ? '' : sprintf('create an empty file in <strong>%s</strong> and ', $browsercache_rules_no404wp_path)), $browsercache_rules_no404wp_path), 
            'browsercache_write_rules_cdn' => sprintf('The browser cache rules for CDN could not be modified. Please check CDN settings.'), 
            'minify_write_rules' => sprintf('The minify cache rules could not be modified. Please run <strong>chmod 777 %s</strong> to resolve this issue.', (file_exists($minify_rules_path) ? $minify_rules_path : dirname($minify_rules_path))), 
            'support_request_type' => 'Please select request type.', 
            'support_request_url' => 'Please enter the address of your site in the site <acronym title="Uniform Resource Locator">URL</acronym> field.', 
            'support_request_name' => 'Please enter your name in the Name field', 
            'support_request_email' => 'Please enter valid email address in the E-Mail field.', 
            'support_request_phone' => 'Please enter your phone in the phone field.', 
            'support_request_subject' => 'Please enter subject in the subject field.', 
            'support_request_description' => 'Please describe the issue in the issue description field.', 
            'support_request_wp_login' => 'Please enter an administrator login. Remember you can create a temporary one just for this support case.', 
            'support_request_wp_password' => 'Please enter WP Admin password, be sure it\'s spelled correctly.', 
            'support_request_ftp_host' => 'Please enter <acronym title="Secure Shell">SSH</acronym> or <acronym title="File Transfer Protocol">FTP</acronym> host for your site.', 
            'support_request_ftp_login' => 'Please enter <acronym title="Secure Shell">SSH</acronym> or <acronym title="File Transfer Protocol">FTP</acronym> login for your server. Remember you can create a temporary one just for this support case.', 
            'support_request_ftp_password' => 'Please enter <acronym title="Secure Shell">SSH</acronym> or <acronym title="File Transfer Protocol">FTP</acronym> password for your <acronym title="File Transfer Protocol">FTP</acronym> account.', 
            'support_request' => 'Unable to send your support request.', 
            'config_import_no_file' => 'Please select config file.', 
            'config_import_upload' => 'Unable to upload config file.', 
            'config_import_import' => sprintf('Configuration file could not be imported. Please run <strong>chmod 777 %s</strong> to make the configuration file write-able, then try again.', (file_exists($config_path) ? $config_path : dirname($config_path))), 
            'config_reset' => sprintf('Default settings could not be restored. Please run <strong>chmod 777 %s</strong> to make the configuration file write-able, then try again.', (file_exists(W3TC_CONFIG_PREVIEW_PATH) ? W3TC_CONFIG_PREVIEW_PATH : W3TC_CONFIG_PREVIEW_PATH)), 
            'preview_enable' => sprintf('Preview mode could not be enabled. Please run <strong>chmod 777 %s</strong> to make the configuration file write-able, then try again.', (file_exists(W3TC_CONFIG_PREVIEW_PATH) ? W3TC_CONFIG_PREVIEW_PATH : dirname(W3TC_CONFIG_PREVIEW_PATH))), 
            'preview_disable' => sprintf('Preview mode could not be disabled. Please run <strong>chmod 777 %s</strong> to make the configuration file write-able, then try again.', (file_exists($config_path) ? $config_path : dirname($config_path))), 
            'preview_deploy' => sprintf('Preview settings could not be deployed. Please run <strong>chmod 777 %s</strong> to make the configuration file write-able, then try again.', (file_exists(W3TC_CONFIG_PATH) ? W3TC_CONFIG_PATH : dirname(W3TC_CONFIG_PATH))), 
            'cdn_purge_attachment' => 'Unable to purge attachment.', 
            'pgcache_purge_post' => 'Unable to purge post.'
        );
        
        $note_messages = array(
            'config_save' => 'Plugin configuration successfully updated.', 
            'flush_all' => 'All caches successfully emptied.', 
            'flush_memcached' => 'Memcached cache(s) successfully emptied.', 
            'flush_opcode' => 'Opcode cache(s) successfully emptied.', 
            'flush_file' => 'Disk cache successfully emptied.', 
            'flush_pgcache' => 'Page cache successfully emptied.', 
            'flush_dbcache' => 'Database cache successfully emptied.', 
            'flush_objectcache' => 'Object cache successfully emptied.', 
            'flush_minify' => 'Minify cache successfully emptied.', 
            'pgcache_write_rules_core' => 'Page cache rewrite rules have been successfully written.', 
            'pgcache_write_rules_cache' => 'Page cache rewrite rules have been successfully written.', 
            'browsercache_write_rules_cache' => 'Browser cache directives have been successfully written.', 
            'browsercache_write_rules_no404wp' => 'Browser cache directives have been successfully written.', 
            'minify_write_rules' => 'Minify rewrite rules have been successfully written.', 
            'support_request' => 'Your support request has been successfully sent.', 
            'config_import' => 'Settings successfully imported.', 
            'config_reset' => 'Settings successfully restored.', 
            'preview_enable' => 'Preview mode was successfully enabled', 
            'preview_disable' => 'Preview mode was successfully disabled', 
            'preview_deploy' => 'Preview settings successfully deployed.', 
            'cdn_purge_attachment' => 'Attachment successfully purged.', 
            'pgcache_purge_post' => 'Post successfully purged.'
        );
        
        $errors = array();
        $notes = array();
        
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $error = W3_Request::get_string('w3tc_error');
        $note = W3_Request::get_string('w3tc_note');
        
        /**
         * Handle messages from reqeust
         */
        if (isset($error_messages[$error])) {
            $errors[] = $error_messages[$error];
        }
        
        if (isset($note_messages[$note])) {
            $notes[] = $note_messages[$note];
        }
        
        /**
         * Check config file
         */
        if (!w3_is_preview_config() && !file_exists(W3TC_CONFIG_PATH)) {
            $errors[] = sprintf('<strong>W3 Total Cache Error:</strong> Default settings are in use. The configuration file could not be read or doesn\'t exist. Please %s to create the file.', $this->button_link('save your settings', sprintf('admin.php?page=%s&save_config', $this->_page)));
        }
        
        /**
         * CDN notifications
         */
        if ($this->_config->get_boolean('cdn.enabled') && !in_array($this->_config->get_string('cdn.engine'), array(
            'mirror', 
            'netdna'
        ))) {
            /**
             * Show notification after theme change
             */
            if ($this->_config->get_boolean('notes.theme_changed')) {
                $notes[] = sprintf('Your active theme has changed, please %s now to ensure proper operation. %s', $this->button_popup('upload active theme files', 'cdn_export', 'cdn_export_type=theme'), $this->button_hide_note('Hide this message', 'theme_changed'));
            }
            
            /**
             * Show notification after WP upgrade
             */
            if ($this->_config->get_boolean('notes.wp_upgraded')) {
                $notes[] = sprintf('Have you upgraded WordPress? Please %s files now to ensure proper operation. %s', $this->button_popup('upload wp-includes', 'cdn_export', 'cdn_export_type=includes'), $this->button_hide_note('Hide this message', 'wp_upgraded'));
            }
            
            /**
             * Show notification after CDN enable
             */
            if ($this->_config->get_boolean('notes.cdn_upload')) {
                $cdn_upload_buttons = array();
                
                if ($this->_config->get_boolean('cdn.includes.enable')) {
                    $cdn_upload_buttons[] = $this->button_popup('wp-includes', 'cdn_export', 'cdn_export_type=includes');
                }
                
                if ($this->_config->get_boolean('cdn.theme.enable')) {
                    $cdn_upload_buttons[] = $this->button_popup('theme files', 'cdn_export', 'cdn_export_type=theme');
                }
                
                if ($this->_config->get_boolean('minify.enabled') && $this->_config->get_boolean('cdn.minify.enable')) {
                    $cdn_upload_buttons[] = $this->button_popup('minify files', 'cdn_export', 'cdn_export_type=minify');
                }
                
                if ($this->_config->get_boolean('cdn.custom.enable')) {
                    $cdn_upload_buttons[] = $this->button_popup('custom files', 'cdn_export', 'cdn_export_type=custom');
                }
                
                $notes[] = sprintf('Make sure to %s and upload your %s, files to the CDN to ensure proper operation. %s', $this->button_popup('export your media library', 'cdn_export_library'), implode(', ', $cdn_upload_buttons), $this->button_hide_note('Hide this message', 'cdn_upload'));
            }
        }
        
        /**
         * Show notification after plugin activate/deactivate
         */
        if ($this->_config->get_boolean('notes.plugins_updated')) {
            $texts = array();
            
            if ($this->_config->get_boolean('pgcache.enabled')) {
                $texts[] = $this->button_link('empty the page cache', sprintf('admin.php?page=%s&flush_pgcache', $this->_page));
            }
            
            if ($this->_config->get_boolean('minify.enabled')) {
                $texts[] = sprintf('check your %s to maintain the desired user experience', $this->button_hide_note('minify settings', 'plugins_updated', 'admin.php?page=w3tc_minify'));
            }
            
            if (count($texts)) {
                $notes[] = sprintf('One or more plugins have been activated or deactivated, please %s. %s', implode(' and ', $texts), $this->button_hide_note('Hide this message', 'plugins_updated'));
            }
        }
        
        /**
         * Show notification when page cache needs to be emptied
         */
        if ($this->_config->get_boolean('pgcache.enabled') && $this->_config->get('notes.need_empty_pgcache') && !w3_is_preview_config()) {
            $notes[] = sprintf('The setting change(s) made either invalidate your cached data or modify the behavior of your site. %s now to provide a consistent user experience.', $this->button_link('Empty the page cache', sprintf('admin.php?page=%s&flush_pgcache', $this->_page)));
        }
        
        /**
         * Minify notifications
         */
        if ($this->_config->get_boolean('minify.enabled')) {
            /**
             * Minify error occured
             */
            if ($this->_config->get('notes.minify_error')) {
                $errors[] = sprintf('Recently an error occurred while creating the CSS / JS minify cache: Some files were unavailable, please check your settings to ensure your site is working as intended. %s', $this->button_hide_note('Hide this message', 'minify_error'));
            }
            
            /**
             * Show notification when minify needs to be emptied
             */
            if ($this->_config->get('notes.need_empty_minify') && !w3_is_preview_config()) {
                $notes[] = sprintf('The setting change(s) made either invalidate your cached data or modify the behavior of your site. %s now to provide a consistent user experience.', $this->button_link('Empty the minify cache', sprintf('admin.php?page=%s&flush_minify', $this->_page)));
            }
        }
        
        /**
         * Show messages
         */
        foreach ($errors as $error) {
            echo sprintf('<div class="error"><p>%s</p></div>', $error);
        }
        
        foreach ($notes as $note) {
            echo sprintf('<div class="updated fade"><p>%s</p></div>', $note);
        }
    }
    
    /**
     * Active plugins pre update option filter
     */
    function pre_update_option_active_plugins($new_value)
    {
        $old_value = (array) get_option('active_plugins');
        
        if ($new_value !== $old_value && in_array(W3TC_FILE, (array) $new_value) && in_array(W3TC_FILE, (array) $old_value)) {
            $this->_config->set('notes.plugins_updated', true);
            $this->_config->save();
        }
        
        return $new_value;
    }
    
    /**
     * Show plugin changes
     */
    function in_plugin_update_message()
    {
        $data = w3_http_get(W3TC_README_URL);
        
        if ($data) {
            $matches = null;
            $regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote(W3TC_VERSION) . '\s*=|$)~Uis';
            
            if (preg_match($regexp, $data, $matches)) {
                $changelog = (array) preg_split('~[\r\n]+~', trim($matches[1]));
                
                echo '<div style="color: #f00;">Take a minute to update, here\'s why:</div><div style="font-weight: normal;">';
                $ul = false;
                
                foreach ($changelog as $index => $line) {
                    if (preg_match('~^\s*\*\s*~', $line)) {
                        if (!$ul) {
                            echo '<ul style="list-style: disc; margin-left: 20px;">';
                            $ul = true;
                        }
                        $line = preg_replace('~^\s*\*\s*~', '', htmlspecialchars($line));
                        echo '<li style="width: 50%; margin: 0; float: left; ' . ($index % 2 == 0 ? 'clear: left;' : '') . '">' . $line . '</li>';
                    } else {
                        if ($ul) {
                            echo '</ul><div style="clear: left;"></div>';
                            $ul = false;
                        }
                        echo '<p style="margin: 5px 0;">' . htmlspecialchars($line) . '</p>';
                    }
                }
                
                if ($ul) {
                    echo '</ul><div style="clear: left;"></div>';
                }
                
                echo '</div>';
            }
        }
    }
    
    /**
     * media_row_actions filter
     */
    function media_row_actions($actions, $post)
    {
        $actions = array_merge($actions, array(
            'cdn_purge' => sprintf('<a href="admin.php?page=w3tc_general&cdn_purge_attachment&attachment_id=%d">Purge from CDN</a>', $post->ID)
        ));
        
        return $actions;
    }
    
    /**
     * post_row_actions filter
     */
    function post_row_actions($actions, $post)
    {
        $actions = array_merge($actions, array(
            'pgcache_purge' => sprintf('<a href="admin.php?page=w3tc_general&pgcache_purge_post&post_id=%d">Purge from Page Cache</a>', $post->ID)
        ));
        
        return $actions;
    }
    
    /**
     * Template filter
     * 
     * @param $template
     * @return string
     */
    function template($template)
    {
        require_once W3TC_LIB_W3_DIR . '/Mobile.php';
        $w3_mobile = & W3_Mobile::instance();
        
        $mobile_template = $w3_mobile->get_template();
        
        if ($mobile_template) {
            return $mobile_template;
        }
        
        return $template;
    }
    
    /**
     * Stylesheet filter
     * 
     * @param $stylesheet
     * @return string
     */
    function stylesheet($stylesheet)
    {
        require_once W3TC_LIB_W3_DIR . '/Mobile.php';
        $w3_mobile = & W3_Mobile::instance();
        
        $mobile_stylesheet = $w3_mobile->get_stylesheet();
        
        if ($mobile_stylesheet) {
            return $mobile_stylesheet;
        }
        
        return $stylesheet;
    }
    
    /**
     * Template filter
     * 
     * @param $template
     * @return string
     */
    function template_preview($template)
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        $theme_name = W3_Request::get_string('w3tc_theme');
        
        $theme = get_theme($theme_name);
        
        if ($theme) {
            return $theme['Template'];
        }
        
        return $template;
    }
    
    /**
     * Stylesheet filter
     * 
     * @param $stylesheet
     * @return string
     */
    function stylesheet_preview($stylesheet)
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        $theme_name = W3_Request::get_string('w3tc_theme');
        
        $theme = get_theme($theme_name);
        
        if ($theme) {
            return $theme['Stylesheet'];
        }
        
        return $stylesheet;
    }
    
    /**
     * Footer plugin action
     */
    function footer()
    {
        echo '<div style="text-align: center;">Performance Optimization <a href="http://www.w3-edge.com/wordpress-plugins/" rel="external">WordPress Plugins</a> by W3 EDGE</div>';
    }
    
    /**
     * Options page
     */
    function options()
    {
        /**
         * Check for page cache availability
         */
        if ($this->_config->get_boolean('pgcache.enabled')) {
            if (!$this->check_advanced_cache()) {
                $this->_errors[] = sprintf('Page caching is not available: advanced-cache.php is not installed. Either the <strong>%s</strong> directory is not write-able or you have another caching plugin installed. This error message will automatically disappear once the change is successfully made.', WP_CONTENT_DIR);
            } elseif (!defined('WP_CACHE')) {
                $this->_errors[] = sprintf('Page caching is not available: please add: <strong>define(\'WP_CACHE\', true);</strong> to <strong>%swp-config.php</strong>. This error message will automatically disappear once the change is successfully made.', ABSPATH);
            } else {
                switch ($this->_config->get_string('pgcache.engine')) {
                    case 'file_pgcache':
                        require_once W3TC_LIB_W3_DIR . '/Plugin/PgCache.php';
                        $w3_plugin_pgcache = & W3_Plugin_PgCache::instance();
                        
                        if ($this->_config->get_boolean('notes.pgcache_rules_core') && !$w3_plugin_pgcache->check_rules_core()) {
                            $pgcache_rules_core_path = w3_get_home_root() . '/.htaccess';
                            
                            if (w3_is_multisite()) {
                                $this->_errors[] = sprintf('Enhanced mode page cache is not operational. Your .htaccess rules could not be modified. Please verify <strong>%s</strong> has the following rules: %s <textarea class="w3tc-rules" cols="120" rows="10" readonly="readonly">%s</textarea> %s', $pgcache_rules_core_path, $this->button('view code', '', 'w3tc-show-rules'), htmlspecialchars($w3_plugin_pgcache->generate_rules_core()), $this->button_hide_note('Hide this message', 'pgcache_rules_core'));
                            } else {
                                $this->_errors[] = sprintf('You\'ve selected disk caching with enhanced mode however the .htaccess file is not properly configured. Please %srun <strong>chmod 777 %s</strong>, then %s. To manually modify your server configuration for enhanced mode append the following code: %s <textarea class="w3tc-rules" cols="120" rows="10" readonly="readonly">%s</textarea> and %s.', (file_exists($pgcache_rules_core_path) ? '' : sprintf('create an empty file in <strong>%s</strong> and ', $pgcache_rules_core_path)), $pgcache_rules_core_path, $this->button_link('try again', sprintf('admin.php?page=%s&pgcache_write_rules_core', $this->_page)), $this->button('view code', '', 'w3tc-show-rules'), htmlspecialchars($w3_plugin_pgcache->generate_rules_core()), $this->button_hide_note('hide this message', 'pgcache_rules_core'));
                            }
                        }
                        
                        if ($this->_config->get_boolean('notes.pgcache_rules_cache') && !$w3_plugin_pgcache->check_rules_cache()) {
                            $pgcache_rules_cache_path = W3TC_CACHE_FILE_PGCACHE_DIR . '/.htaccess';
                            
                            $this->_errors[] = sprintf('You\'ve selected disk caching with enhanced mode however the .htaccess file is not properly configured. Please run <strong>chmod 777 %s</strong>, then %s. To manually modify your server configuration for enhanced mode append the following code: %s <textarea class="w3tc-rules" cols="120" rows="10" readonly="readonly">%s</textarea> and %s.', (file_exists($pgcache_rules_cache_path) ? $pgcache_rules_cache_path : dirname($pgcache_rules_cache_path)), $this->button_link('try again', sprintf('admin.php?page=%s&pgcache_write_rules_cache', $this->_page)), $this->button('view code', '', 'w3tc-show-rules'), htmlspecialchars($w3_plugin_pgcache->generate_rules_cache()), $this->button_hide_note('hide this message', 'pgcache_rules_cache'));
                        }
                        break;
                    
                    case 'memcached':
                        $pgcache_memcached_servers = $this->_config->get_array('pgcache.memcached.servers');
                        
                        if (!$this->is_memcache_available($pgcache_memcached_servers)) {
                            $this->_errors[] = sprintf('Page caching is not working properly. Memcached server(s): <strong>%s</strong> may not running or not responding. This error message will automatically disappear once the issue is resolved.', implode(', ', $pgcache_memcached_servers));
                        }
                        break;
                }
            }
        }
        
        /**
         * Check for minify availability
         */
        if ($this->_config->get_boolean('minify.enabled')) {
            if (W3TC_PHP5 && $this->_config->get_boolean('minify.rewrite')) {
                require_once W3TC_LIB_W3_DIR . '/Plugin/Minify.php';
                $w3_plugin_minify = & W3_Plugin_Minify::instance();
                
                if ($this->_config->get_boolean('notes.minify_rules') && !$w3_plugin_minify->check_rules()) {
                    $minify_rules_path = W3TC_CACHE_FILE_MINIFY_DIR . '/.htaccess';
                    
                    $this->_errors[] = sprintf('The "Rewrite URL Structure" feature, requires rewrite rules be present. Please run <strong>chmod 777 %s</strong>, then %s. To manually modify your server configuration for minify append the following code: %s <textarea class="w3tc-rules" cols="120" rows="10" readonly="readonly">%s</textarea> and %s.', (file_exists($minify_rules_path) ? $minify_rules_path : dirname($minify_rules_path)), $this->button_link('try again', sprintf('admin.php?page=%s&minify_write_rules', $this->_page)), $this->button('view code', '', 'w3tc-show-rules'), htmlspecialchars($w3_plugin_minify->generate_rules()), $this->button_hide_note('hide this message', 'minify_rules'));
                }
            }
            
            if ($this->_config->get_string('minify.engine') == 'memcached') {
                $minify_memcached_servers = $this->_config->get_array('minify.memcached.servers');
                
                if (!$this->is_memcache_available($minify_memcached_servers)) {
                    $this->_errors[] = sprintf('Minify is not working properly. Memcached server(s): <strong>%s</strong> may not running or not responding. This error message will automatically disappear once the issue is resolved.', implode(', ', $minify_memcached_servers));
                }
            }
        }
        
        /**
         * Check for database cache availability
         */
        if ($this->_config->get_boolean('dbcache.enabled')) {
            if (!$this->check_db()) {
                $this->_errors[] = sprintf('Database caching is not available: db.php is not installed. Either the <strong>%s</strong> directory is not write-able or you have another caching plugin installed. This error message will automatically disappear once the change is successfully made.', WP_CONTENT_DIR);
            } elseif ($this->_config->get_string('dbcache.engine') == 'memcached') {
                $dbcache_memcached_servers = $this->_config->get_array('dbcache.memcached.servers');
                
                if (!$this->is_memcache_available($dbcache_memcached_servers)) {
                    $this->_errors[] = sprintf('Database caching is not working properly. Memcached server(s): <strong>%s</strong> may not running or not responding. This error message will automatically disappear once the issue is successfully resolved.', implode(', ', $dbcache_memcached_servers));
                }
            }
        }
        
        /**
         * Check for object cache availability
         */
        if ($this->_config->get_boolean('objectcache.enabled')) {
            if (!$this->check_objectcache()) {
                $this->_errors[] = sprintf('Object caching is not available: object-cache.php is not installed. Either the <strong>%s</strong> directory is not write-able or you have another caching plugin installed. This error message will automatically disappear once the change is successfully made.', WP_CONTENT_DIR);
            } elseif ($this->_config->get_string('objectcache.engine') == 'memcached') {
                $objectcache_memcached_servers = $this->_config->get_array('objectcache.memcached.servers');
                
                if (!$this->is_memcache_available($objectcache_memcached_servers)) {
                    $this->_errors[] = sprintf('Object caching is not working properly. Memcached server(s): <strong>%s</strong> may not running or not responding. This error message will automatically disappear once the issue is successfully resolved.', implode(', ', $objectcache_memcached_servers));
                }
            }
        }
        
        /**
         * Check for browser cache availability
         */
        if ($this->_config->get_boolean('browsercache.enabled')) {
            require_once W3TC_LIB_W3_DIR . '/Plugin/BrowserCache.php';
            $w3_plugin_browsercache = & W3_Plugin_BrowserCache::instance();
            
            if ($this->_config->get_boolean('notes.browsercache_rules_cache') && !$w3_plugin_browsercache->check_rules_cache()) {
                $browsercache_rules_cache_path = w3_get_document_root() . '/.htaccess';
                
                if (w3_is_multisite()) {
                    $this->_errors[] = sprintf('Browser Cache feature is not operational. Your .htaccess rules could not be modified. Please verify <strong>%s</strong> has the following rules: %s <textarea class="w3tc-rules" cols="120" rows="10" readonly="readonly">%s</textarea> %s', $browsercache_rules_cache_path, $this->button('view code', '', 'w3tc-show-rules'), htmlspecialchars($w3_plugin_browsercache->generate_rules_cache()), $this->button_hide_note('Hide this message', 'browsercache_rules_cache'));
                } else {
                    $this->_errors[] = sprintf('You\'ve enabled Browser Cache feature however the .htaccess file is not properly configured. Please %srun <strong>chmod 777 %s</strong>, then %s. To manually modify these settings use the following code: %s <textarea class="w3tc-rules" cols="120" rows="10" readonly="readonly">%s</textarea> and %s.', (file_exists($browsercache_rules_cache_path) ? '' : sprintf('create an empty file in <strong>%s</strong> and ', $browsercache_rules_cache_path)), $browsercache_rules_cache_path, $this->button_link('try again', sprintf('admin.php?page=%s&browsercache_write_rules_cache', $this->_page)), $this->button('view code', '', 'w3tc-show-rules'), htmlspecialchars($w3_plugin_browsercache->generate_rules_cache()), $this->button_hide_note('hide this message', 'browsercache_rules_cache'));
                }
            }
            
            if ($this->_config->get_boolean('notes.browsercache_rules_no404wp') && $this->_config->get_boolean('browsercache.no404wp') && !$w3_plugin_browsercache->check_rules_no404wp()) {
                $browsercache_rules_no404wp_path = w3_get_home_root() . '/.htaccess';
                
                if (w3_is_multisite()) {
                    $this->_errors[] = sprintf('Browser Cache feature is not operational. Your .htaccess rules could not be modified. Please verify <strong>%s</strong> has the following rules: %s <textarea class="w3tc-rules" cols="120" rows="10" readonly="readonly">%s</textarea> %s', $browsercache_rules_no404wp_path, $this->button('view code', '', 'w3tc-show-rules'), htmlspecialchars($w3_plugin_browsercache->generate_rules_no404wp()), $this->button_hide_note('Hide this message', 'browsercache_rules_no404wp'));
                } else {
                    $this->_errors[] = sprintf('You\'ve enabled Browser Cache feature however the .htaccess file is not properly configured. Please %srun <strong>chmod 777 %s</strong>, then %s. To manually modify these settings use the following code: %s <textarea class="w3tc-rules" cols="120" rows="10" readonly="readonly">%s</textarea> and %s.', (file_exists($browsercache_rules_no404wp_path) ? '' : sprintf('create an empty file in <strong>%s</strong> and ', $browsercache_rules_no404wp_path)), $browsercache_rules_no404wp_path, $this->button_link('try again', sprintf('admin.php?page=%s&browsercache_write_rules_no404wp', $this->_page)), $this->button('view code', '', 'w3tc-show-rules'), htmlspecialchars($w3_plugin_browsercache->generate_rules_no404wp()), $this->button_hide_note('hide this message', 'browsercache_rules_no404wp'));
                }
            }
        }
        
        /**
         * Check PHP version
         */
        if (!W3TC_PHP5 && $this->_config->get_boolean('notes.php_is_old')) {
            $this->_notes[] = sprintf('Unfortunately, <strong>PHP5</strong> is required for full functionality of this plugin; incompatible features are automatically disabled. Please upgrade if possible. %s', $this->button_hide_note('Hide this message', 'php_is_old'));
        }
        
        /**
         * Check CURL extension
         */
        if ($this->_config->get_boolean('notes.no_curl') && $this->_config->get_boolean('cdn.enabled') && !function_exists('curl_init')) {
            $this->_notes[] = sprintf('The <strong>CURL PHP</strong> extension is not available. Please install it to enable S3 or CloudFront functionality. %s', $this->button_hide_note('Hide this message', 'no_curl'));
        }
        
        /**
         * Check Zlib extension
         */
        if ($this->_config->get_boolean('notes.no_zlib') && !function_exists('gzencode')) {
            $this->_notes[] = sprintf('Unfortunately the PHP installation is incomplete, the <strong>zlib module is missing</strong>. This is a core PHP module. Please notify your server administrator and ask for it to be installed. %s', $this->button_hide_note('Hide this message', 'no_zlib'));
        }
        
        /**
         * Check if Zlib output compression is enabled
         */
        if ($this->_config->get_boolean('notes.zlib_output_compression') && w3_zlib_output_compression()) {
            $this->_notes[] = sprintf('Either the PHP configuration, Web Server configuration or a script somewhere in your WordPress installation is has set <strong>zlib.output_compression</strong> to enabled.<br />Please locate and disable this setting to ensure proper HTTP compression management. %s', $this->button_hide_note('Hide this message', 'zlib_output_compression'));
        }
        
        /**
         * Check wp-content permissions
         */
        if (!W3TC_WIN && $this->_config->get_boolean('notes.wp_content_perms')) {
            $wp_content_stat = stat(WP_CONTENT_DIR);
            $wp_content_mode = ($wp_content_stat['mode'] & 0777);
            
            if ($wp_content_mode != 0755) {
                $this->_notes[] = sprintf('<strong>%s</strong> is write-able. If you\'ve finished installing the plugin, change the permissions back to the default: <strong>chmod 755 %s</strong>. %s', WP_CONTENT_DIR, WP_CONTENT_DIR, $this->button_hide_note('Hide this message', 'wp_content_perms'));
            }
        }
        
        /**
         * Check permalinks
         */
        if ($this->_config->get_boolean('notes.no_permalink_rules') && (($this->_config->get_boolean('pgcache.enabled') && $this->_config->get_string('pgcache.engine') == 'file_pgcache') || ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.no404wp'))) && !w3_is_permalink_rules()) {
            $this->_errors[] = sprintf('The required directives for fancy permalinks could not be detected, please confirm they are available: %s <textarea class="w3tc-rules" cols="120" rows="10" readonly="readonly">%s</textarea> %s', $this->button('view code', '', 'w3tc-show-rules'), htmlspecialchars(w3_get_permalink_rules()), $this->button_hide_note('Hide this message', 'no_permalink_rules'));
        }
        
        /**
         * CDN
         */
        if ($this->_config->get_boolean('cdn.enabled')) {
            /**
             * Check upload settings
             */
            $upload_info = w3_upload_info();
            
            if (!$upload_info) {
                $upload_path = get_option('upload_path');
                $upload_path = trim($upload_path);
                
                if (empty($upload_path)) {
                    $upload_path = WP_CONTENT_DIR . '/uploads';
                    
                    $this->_errors[] = sprintf('Your store uploads folder is not available. Default WordPress directories will be created: <strong>%s</strong>.', $upload_path);
                }
                
                $this->_errors[] = sprintf('The path found in the database (%s) is inconsistent with the current paths found on your server. Please manually adjust the upload path either in miscellaneous settings or in the site\'s edit page if in network mode.', $upload_path);
            }
            
            /**
             * Check CDN settings
             */
            $cdn_engine = $this->_config->get_string('cdn.engine');
            
            switch (true) {
                case ($cdn_engine == 'mirror' && !count($this->_config->get_array('cdn.mirror.domain'))):
                    $this->_errors[] = 'Content Delivery Network Error: The <strong>"Replace default hostname with"</strong> field must be populated.';
                    break;
                
                case ($cdn_engine == 'netdna' && !count($this->_config->get_array('cdn.netdna.domain'))):
                    $this->_errors[] = 'Content Delivery Network Error: The <strong>"Replace default hostname with"</strong> field must be populated.';
                    break;
                
                case ($cdn_engine == 'ftp' && !count($this->_config->get_array('cdn.ftp.domain'))):
                    $this->_errors[] = 'Content Delivery Network Error: The <strong>"Replace default hostname with"</strong> field must be populated. Enter the hostname of your <acronym title="Content Delivery Network">CDN</acronym> provider. <em>This is the hostname you would enter into your address bar in order to view objects in your browser.</em>';
                    break;
                
                case ($cdn_engine == 's3' && ($this->_config->get_string('cdn.s3.key') == '' || $this->_config->get_string('cdn.s3.secret') == '' || $this->_config->get_string('cdn.s3.bucket') == '')):
                    $this->_errors[] = 'Content Delivery Network Error: The <strong>"Access key", "Secret key" and "Bucket"</strong> fields must be populated.';
                    break;
                
                case ($cdn_engine == 'cf' && ($this->_config->get_string('cdn.cf.key') == '' || $this->_config->get_string('cdn.cf.secret') == '' || $this->_config->get_string('cdn.cf.bucket') == '' || ($this->_config->get_string('cdn.cf.id') == '' && !count($this->_config->get_array('cdn.cf.cname'))))):
                    $this->_errors[] = 'Content Delivery Network Error: The <strong>"Access key", "Secret key", "Bucket" and "Replace default hostname with"</strong> fields must be populated.';
                    break;
                
                case ($cdn_engine == 'rscf' && ($this->_config->get_string('cdn.rscf.user') == '' || $this->_config->get_string('cdn.rscf.key') == '' || $this->_config->get_string('cdn.rscf.container') == '' || ($this->_config->get_string('cdn.rscf.id') == '' && !count($this->_config->get_array('cdn.rscf.cname'))))):
                    $this->_errors[] = 'Content Delivery Network Error: The <strong>"Username", "API key", "Container" and "Replace default hostname with"</strong> fields must be populated.';
                    break;
            }
        }
        
        /**
         * Preview mode
         */
        
        if (w3_is_preview_config()) {
            $this->_notes[] = sprintf('Preview mode is active: Changed settings will not take effect until you %s or %s preview mode. %s any changed settings (without deploying), or make additional changes.', $this->button_link('deploy', sprintf('admin.php?page=%s&preview_deploy', $this->_page)), $this->button_link('disable', sprintf('admin.php?page=%s&preview_save&preview=0', $this->_page)), $this->button_link('Preview', w3_get_site_url() . '/?w3tc_preview=1', true));
        }
        
        /**
         * Show tab
         */
        switch ($this->_page) {
            case 'w3tc_general':
                $this->options_general();
                break;
            
            case 'w3tc_pgcache':
                $this->options_pgcache();
                break;
            
            case 'w3tc_minify':
                $this->options_minify();
                break;
            
            case 'w3tc_dbcache':
                $this->options_dbcache();
                break;
            
            case 'w3tc_objectcache':
                $this->options_objectcache();
                break;
            
            case 'w3tc_browsercache':
                $this->options_browsercache();
                break;
            
            case 'w3tc_mobile':
                $this->options_mobile();
                break;
            
            case 'w3tc_cdn':
                $this->options_cdn();
                break;
            
            case 'w3tc_faq':
                $this->options_faq();
                break;
            
            case 'w3tc_support':
                $request_type = W3_Request::get_string('request_type');
                
                if (!$request_type || !isset($this->_request_types[$request_type])) {
                    $this->options_support_select();
                } else {
                    $payment = W3_Request::get_boolean('payment');
                    
                    if (isset($this->_request_prices[$request_type]) && !$payment) {
                        $this->options_support_payment();
                    } else {
                        $this->options_support();
                    }
                }
                break;
            
            case 'w3tc_support_select':
                $this->options_support_select();
                break;
            
            case 'w3tc_support_payment':
                $this->options_support_payment();
                break;
            
            case 'w3tc_install':
                $this->options_install();
                break;
            
            case 'w3tc_about':
                $this->options_about();
                break;
        }
    }
    
    /**
     * General tab
     */
    function options_general()
    {
        $preview = w3_is_preview_config();
        
        $pgcache_enabled = $this->_config->get_boolean('pgcache.enabled');
        $dbcache_enabled = $this->_config->get_boolean('dbcache.enabled');
        $objectcache_enabled = $this->_config->get_boolean('objectcache.enabled');
        $browsercache_enabled = $this->_config->get_boolean('browsercache.enabled');
        $minify_enabled = $this->_config->get_boolean('minify.enabled');
        $cdn_enabled = $this->_config->get_boolean('cdn.enabled');
        
        $enabled = ($pgcache_enabled || $minify_enabled || $dbcache_enabled || $objectcache_enabled || $browsercache_enabled || $cdn_enabled);
        $enabled_checked = ($pgcache_enabled && $minify_enabled && $dbcache_enabled && $objectcache_enabled && $browsercache_enabled && $cdn_enabled);
        
        $check_apc = function_exists('apc_store');
        $check_eaccelerator = function_exists('eaccelerator_put');
        $check_xcache = function_exists('xcache_set');
        $check_curl = function_exists('curl_init');
        $check_memcached = class_exists('Memcache');
        $check_ftp = function_exists('ftp_connect');
        
        $pgcache_engine = $this->_config->get_string('pgcache.engine');
        $dbcache_engine = $this->_config->get_string('dbcache.engine');
        $objectcache_engine = $this->_config->get_string('objectcache.engine');
        $minify_engine = $this->_config->get_string('minify.engine');
        
        $opcode_engines = array(
            'apc', 
            'eaccelerator', 
            'xcache'
        );
        $file_engines = array(
            'file', 
            'file_pgcache'
        );
        
        $can_empty_memcache = ($pgcache_enabled && $pgcache_engine == 'memcached');
        $can_empty_memcache = $can_empty_memcache || ($pgcache_enabled && $pgcache_engine == 'memcached');
        $can_empty_memcache = $can_empty_memcache || ($dbcache_enabled && $dbcache_engine == 'memcached');
        $can_empty_memcache = $can_empty_memcache || ($objectcache_enabled && $objectcache_engine == 'memcached');
        $can_empty_memcache = $can_empty_memcache || ($minify_enabled && $minify_engine == 'memcached');
        
        $can_empty_opcode = ($pgcache_enabled && in_array($pgcache_engine, $opcode_engines));
        $can_empty_opcode = $can_empty_opcode || ($dbcache_enabled && in_array($dbcache_engine, $opcode_engines));
        $can_empty_opcode = $can_empty_opcode || ($objectcache_enabled && in_array($objectcache_engine, $opcode_engines));
        $can_empty_opcode = $can_empty_opcode || ($minify_enabled && in_array($minify_engine, $opcode_engines));
        
        $can_empty_file = ($pgcache_enabled && in_array($pgcache_engine, $file_engines));
        $can_empty_file = $can_empty_file || ($dbcache_enabled && in_array($dbcache_engine, $file_engines));
        $can_empty_file = $can_empty_file || ($objectcache_enabled && in_array($objectcache_engine, $file_engines));
        $can_empty_file = $can_empty_file || ($minify_enabled && in_array($minify_engine, $file_engines));
        
        $debug = ($this->_config->get_boolean('dbcache.debug') || $this->_config->get_boolean('objectcache.debug') || $this->_config->get_boolean('pgcache.debug') || $this->_config->get_boolean('minify.debug') || $this->_config->get_boolean('cdn.debug'));
        $file_locking = ($this->_config->get_boolean('dbcache.file.locking') || $this->_config->get_boolean('objectcache.file.locking') || $this->_config->get_boolean('pgcache.file.locking') || $this->_config->get_boolean('minify.file.locking'));
        
        $support = $this->_config->get_string('common.support');
        $supports = $this->get_supports();
        
        include W3TC_DIR . '/inc/options/general.phtml';
    }
    
    /**
     * Page cache tab
     */
    function options_pgcache()
    {
        $pgcache_enabled = $this->_config->get_boolean('pgcache.enabled');
        
        include W3TC_DIR . '/inc/options/pgcache.phtml';
    }
    
    /**
     * Minify tab
     */
    function options_minify()
    {
        $minify_enabled = $this->_config->get_boolean('minify.enabled');
        
        $themes = $this->get_themes();
        $templates = array();
        
        $current_theme = get_current_theme();
        $current_theme_key = '';
        
        foreach ($themes as $theme_key => $theme_name) {
            if ($theme_name == $current_theme) {
                $current_theme_key = $theme_key;
            }
            
            $templates[$theme_key] = $this->get_theme_templates($theme_name);
        }
        
        $js_theme = W3_Request::get_string('js_theme', $current_theme_key);
        $js_groups = $this->_config->get_array('minify.js.groups');
        
        $css_theme = W3_Request::get_string('css_theme', $current_theme_key);
        $css_groups = $this->_config->get_array('minify.css.groups');
        
        include W3TC_DIR . '/inc/options/minify.phtml';
    }
    
    /**
     * Database cache tab
     */
    function options_dbcache()
    {
        $dbcache_enabled = $this->_config->get_boolean('dbcache.enabled');
        
        include W3TC_DIR . '/inc/options/dbcache.phtml';
    }
    
    /**
     * Objects cache tab
     */
    function options_objectcache()
    {
        $objectcache_enabled = $this->_config->get_boolean('objectcache.enabled');
        
        include W3TC_DIR . '/inc/options/objectcache.phtml';
    }
    
    /**
     * Objects cache tab
     */
    function options_browsercache()
    {
        $browsercache_enabled = $this->_config->get_boolean('browsercache.enabled');
        $browsercache_expires = ($this->_config->get_boolean('browsercache.cssjs.expires') || $this->_config->get_boolean('browsercache.html.expires') || $this->_config->get_boolean('browsercache.other.expires'));
        $browsercache_cache_control = ($this->_config->get_boolean('browsercache.cssjs.cache.control') || $this->_config->get_boolean('browsercache.html.cache.control') || $this->_config->get_boolean('browsercache.other.cache.control'));
        $browsercache_etag = ($this->_config->get_boolean('browsercache.cssjs.etag') || $this->_config->get_boolean('browsercache.html.etag') || $this->_config->get_boolean('browsercache.other.etag'));
        $browsercache_w3tc = ($this->_config->get_boolean('browsercache.cssjs.w3tc') || $this->_config->get_boolean('browsercache.html.w3tc') || $this->_config->get_boolean('browsercache.other.w3tc'));
        $browsercache_compression = ($this->_config->get_boolean('browsercache.cssjs.compression') || $this->_config->get_boolean('browsercache.html.compression') || $this->_config->get_boolean('browsercache.other.compression'));
        
        include W3TC_DIR . '/inc/options/browsercache.phtml';
    }
    
    /**
     * Mobile tab
     */
    function options_mobile()
    {
        $mobile_enabled = $this->_config->get_boolean('mobile.enabled');
        $groups = $this->_config->get_array('mobile.rgroups');
        
        require_once W3TC_LIB_W3_DIR . '/Mobile.php';
        $w3_mobile = & W3_Mobile::instance();
        
        $themes = $w3_mobile->get_themes();
        
        include W3TC_DIR . '/inc/options/mobile.phtml';
    }
    
    /**
     * CDN tab
     */
    function options_cdn()
    {
        $cdn_enabled = $this->_config->get_boolean('cdn.enabled');
        $cdn_engine = $this->_config->get_string('cdn.engine');
        $cdn_mirror = ($cdn_engine == 'mirror' || $cdn_engine == 'netdna');
        
        $minify_enabled = $this->_config->get_boolean('minify.enabled');
        
        include W3TC_DIR . '/inc/options/cdn.phtml';
    }
    
    /**
     * FAQ tab
     */
    function options_faq()
    {
        $faq = $this->parse_faq();
        
        include W3TC_DIR . '/inc/options/faq.phtml';
    }
    
    /**
     * Support tab
     */
    function options_support()
    {
        global $current_user;
        
        $name = '';
        $email = '';
        
        if (is_a($current_user, 'WP_User')) {
            if ($current_user->first_name) {
                $name = $current_user->first_name;
            }
            
            if ($current_user->last_name) {
                $name .= ($name != '' ? ' ' : '') . $current_user->last_name;
            }
            
            if (strcmp($name, 'admin') == 0) {
                $name = '';
            }
            
            if ($current_user->user_email) {
                $email = $current_user->user_email;
            }
        }
        
        $theme = get_theme(get_current_theme());
        $template_files = (isset($theme['Template Files']) ? (array) $theme['Template Files'] : array());
        
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $ajax = W3_Request::get_boolean('ajax');
        $request_type = W3_Request::get_string('request_type');
        $payment = W3_Request::get_boolean('payment');
        $url = W3_Request::get_string('url', w3_get_domain_url());
        $name = W3_Request::get_string('name', $name);
        $email = W3_Request::get_string('email', $email);
        $twitter = W3_Request::get_string('twitter');
        $phone = W3_Request::get_string('phone');
        $subject = W3_Request::get_string('subject');
        $description = W3_Request::get_string('description');
        $templates = W3_Request::get_array('templates');
        $forum_url = W3_Request::get_string('forum_url');
        $wp_login = W3_Request::get_string('wp_login');
        $wp_password = W3_Request::get_string('wp_password');
        $ftp_host = W3_Request::get_string('ftp_host');
        $ftp_login = W3_Request::get_string('ftp_login');
        $ftp_password = W3_Request::get_string('ftp_password');
        
        include W3TC_DIR . '/inc/options/support.phtml';
    }
    
    /**
     * Support select tab
     */
    function options_support_select()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $ajax = W3_Request::get_boolean('ajax');
        
        include W3TC_DIR . '/inc/options/support_select.phtml';
    }
    
    /**
     * Support payment tab
     */
    function options_support_payment()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $ajax = W3_Request::get_boolean('ajax');
        $request_type = W3_Request::get_string('request_type');
        
        $base_url = w3_get_site_url() . '/wp-admin/admin.php';
        
        $return_url = $base_url . '?page=w3tc_support&request_type=' . $request_type . '&payment=1';
        $cancel_url = $base_url . '?page=w3tc_general';
        
        include W3TC_DIR . '/inc/options/support_payment.phtml';
    }
    
    /**
     * Install tab
     */
    function options_install()
    {
        $document_root_htaccess = w3_get_document_root() . '/.htaccess';
        $home_root_htaccess = w3_get_home_root() . '/.htaccess';
        $pgcache_htaccess = W3TC_CACHE_FILE_PGCACHE_DIR . '/.htaccess';
        $minify_htaccess = W3TC_CONTENT_MINIFY_DIR . '/.htaccess';
        $cdn_htaccess = '';
        
        $document_root_rules = '';
        $home_root_rules = '';
        $pgcache_rules = '';
        $minify_rules = '';
        $cdn_rules = '';
        
        if ($this->_config->get_boolean('browsercache.enabled')) {
            require_once W3TC_LIB_W3_DIR . '/Plugin/BrowserCache.php';
            $w3_plugin_browsercache = & W3_Plugin_BrowserCache::instance();
            
            $document_root_rules .= $w3_plugin_browsercache->generate_rules_cache();
            
            if ($this->_config->get_boolean('browsercache.no404wp')) {
                $home_root_rules .= $w3_plugin_browsercache->generate_rules_no404wp();
            }
            
            if ($this->_config->get_boolean('cdn.enabled') && $this->_config->get_string('cdn.engine') == 'ftp') {
                require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
                
                $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
                $cdn = & $w3_plugin_cdn->get_cdn();
                
                $domain = $cdn->get_domain();
                
                if ($domain) {
                    $cdn_ftp_htaccess = $domain . '/.htaccess';
                    $cdn_ftp_rules .= $w3_plugin_browsercache->generate_rules_cache();
                }
            }
        }
        
        if ($this->_config->get_boolean('pgcache.enabled')) {
            require_once W3TC_LIB_W3_DIR . '/Plugin/PgCache.php';
            $w3_plugin_pgcache = & W3_Plugin_PgCache::instance();
            
            $home_root_rules .= $w3_plugin_pgcache->generate_rules_core();
            $pgcache_rules .= $w3_plugin_pgcache->generate_rules_cache();
        }
        
        if ($this->_config->get_boolean('minify.enabled')) {
            require_once W3TC_LIB_W3_DIR . '/Plugin/Minify.php';
            $w3_plugin_minify = & W3_Plugin_Minify::instance();
            
            $minify_rules .= $w3_plugin_minify->generate_rules();
        }
        
        include W3TC_DIR . '/inc/options/install.phtml';
    }
    
    /**
     * About tab
     */
    function options_about()
    {
        include W3TC_DIR . '/inc/options/about.phtml';
    }
    
    /**
     * Options save action
     */
    function options_save()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        /**
         * Redirect params
         */
        $params = array();
        
        /**
         * Read config
         * We should use new instance of WP_Config object here
         */
        $config = & new W3_Config();
        $config->read_request();
        
        /**
         * General tab
         */
        if ($this->_page == 'w3tc_general') {
            $debug = W3_Request::get_array('debug');
            $file_locking = W3_Request::get_boolean('file_locking');
            
            $config->set('dbcache.debug', in_array('dbcache', $debug));
            $config->set('objectcache.debug', in_array('objectcache', $debug));
            $config->set('pgcache.debug', in_array('pgcache', $debug));
            $config->set('minify.debug', in_array('minify', $debug));
            $config->set('cdn.debug', in_array('cdn', $debug));
            
            $config->set('dbcache.file.locking', $file_locking);
            $config->set('objectcache.file.locking', $file_locking);
            $config->set('pgcache.file.locking', $file_locking);
            $config->set('minify.file.locking', $file_locking);
            
            /**
             * Check permalinks for page cache
             */
            if ($config->get_boolean('pgcache.enabled') && $config->get_string('pgcache.engine') == 'file_pgcache' && !get_option('permalink_structure')) {
                $this->redirect(array(
                    'w3tc_error' => 'fancy_permalinks_disabled_pgcache'
                ));
            }
        }
        
        /**
         * Minify tab
         */
        if ($this->_page == 'w3tc_minify') {
            $js_groups = array();
            $css_groups = array();
            
            $js_files = W3_Request::get_array('js_files');
            $css_files = W3_Request::get_array('css_files');
            
            foreach ($js_files as $theme => $templates) {
                foreach ($templates as $template => $locations) {
                    foreach ((array) $locations as $location => $files) {
                        switch ($location) {
                            case 'include':
                                $js_groups[$theme][$template][$location]['blocking'] = true;
                                break;
                            
                            case 'include-nb':
                                $js_groups[$theme][$template]['blocking'] = false;
                                break;
                            
                            case 'include-body':
                                $js_groups[$theme][$template]['blocking'] = true;
                                break;
                            
                            case 'include-body-nb':
                                $js_groups[$theme][$template]['blocking'] = false;
                                break;
                            
                            case 'include-footer':
                                $js_groups[$theme][$template]['blocking'] = true;
                                break;
                            
                            case 'include-footer-nb':
                                $js_groups[$theme][$template]['blocking'] = false;
                                break;
                        }
                        
                        foreach ((array) $files as $file) {
                            if (!empty($file)) {
                                $js_groups[$theme][$template][$location]['files'][] = w3_normalize_file_minify($file);
                            }
                        }
                    }
                }
            }
            
            foreach ($css_files as $theme => $templates) {
                foreach ($templates as $template => $locations) {
                    foreach ((array) $locations as $location => $files) {
                        foreach ((array) $files as $file) {
                            if (!empty($file)) {
                                $css_groups[$theme][$template][$location]['files'][] = w3_normalize_file_minify($file);
                            }
                        }
                    }
                }
            }
            
            $config->set('minify.js.groups', $js_groups);
            $config->set('minify.css.groups', $css_groups);
            
            $js_theme = W3_Request::get_string('js_theme');
            $css_theme = W3_Request::get_string('css_theme');
            
            $params = array_merge($params, array(
                'js_theme' => $js_theme, 
                'css_theme' => $css_theme
            ));
        }
        
        /**
         * Browser Cache tab
         */
        if ($this->_page == 'w3tc_browsercache') {
            if ($config->get_boolean('browsercache.enabled') && $config->get_boolean('browsercache.no404wp') && !get_option('permalink_structure')) {
                $this->redirect(array(
                    'w3tc_error' => 'fancy_permalinks_disabled_browsercache'
                ));
            }
        }
        
        /**
         * Mobile tab
         */
        if ($this->_page == 'w3tc_mobile') {
            $groups = W3_Request::get_array('mobile_groups');
            
            $mobile_groups = array();
            $cached_mobile_groups = array();
            
            foreach ($groups as $group => $group_config) {
                $group = strtolower($group);
                $group = preg_replace('~[^0-9a-z_]+~', '_', $group);
                $group = trim($group, '_');
                
                if ($group) {
                    $theme = (isset($group_config['theme']) ? trim($group_config['theme']) : 'default');
                    $enabled = (isset($group_config['enabled']) ? (boolean) $group_config['enabled'] : true);
                    $redirect = (isset($group_config['redirect']) ? trim($group_config['redirect']) : '');
                    $agents = (isset($group_config['agents']) ? explode("\r\n", trim($group_config['agents'])) : array());
                    
                    $mobile_groups[$group] = array(
                        'theme' => $theme, 
                        'enabled' => $enabled, 
                        'redirect' => $redirect, 
                        'agents' => $agents
                    );
                    
                    $cached_mobile_groups[$group] = $agents;
                }
            }
            
            /**
             * Allow plugins modify WPSC mobile groups
             */
            $cached_mobile_groups = apply_filters('cached_mobile_groups', $cached_mobile_groups);
            
            /**
             * Merge existent and delete removed groups
             */
            foreach ($mobile_groups as $group => $group_config) {
                if (isset($cached_mobile_groups[$group])) {
                    $mobile_groups[$group]['agents'] = (array) $cached_mobile_groups[$group];
                } else {
                    unset($mobile_groups[$group]);
                }
            }
            
            /**
             * Add new groups
             */
            foreach ($cached_mobile_groups as $group => $agents) {
                if (!isset($mobile_groups[$group])) {
                    $mobile_groups[$group] = array(
                        'theme' => '', 
                        'enabled' => true, 
                        'redirect' => '', 
                        'agents' => $agents
                    );
                }
            }
            
            /**
             * Allow plugins modify W3TC mobile groups
             */
            $mobile_groups = apply_filters('w3tc_mobile_groups', $mobile_groups);
            
            /**
             * Sanitize mobile groups
             */
            foreach ($mobile_groups as $group => $group_config) {
                $mobile_groups[$group] = array_merge(array(
                    'theme' => '', 
                    'enabled' => true, 
                    'redirect' => '', 
                    'agents' => array()
                ), $group_config);
                
                $mobile_groups[$group]['agents'] = array_unique($mobile_groups[$group]['agents']);
                $mobile_groups[$group]['agents'] = array_map('strtolower', $mobile_groups[$group]['agents']);
                sort($mobile_groups[$group]['agents']);
            }
            
            $config->set('mobile.rgroups', $mobile_groups);
        }
        
        /**
         * CDN tab
         */
        if ($this->_page == 'w3tc_cdn') {
            $cdn_cnames = W3_Request::get_array('cdn_cnames');
            $cdn_domains = array();
            
            foreach ($cdn_cnames as $cdn_cname) {
                $cdn_cname = trim($cdn_cname);
                
                /**
                 * Auto expand wildcard domain to 10 subdomains
                 */
                $matches = null;
                
                if (preg_match('~^\*\.(.*)$~', $cdn_cname, $matches)) {
                    $cdn_domains = array();
                    
                    for ($i = 1; $i <= 10; $i++) {
                        $cdn_domains[] = sprintf('cdn%d.%s', $i, $matches[1]);
                    }
                    
                    break;
                }
                
                if ($cdn_cname) {
                    $cdn_domains[] = $cdn_cname;
                }
            }
            
            switch ($this->_config->get_string('cdn.engine')) {
                case 'mirror':
                    $config->set('cdn.mirror.domain', $cdn_domains);
                    break;
                
                case 'netdna':
                    $config->set('cdn.netdna.domain', $cdn_domains);
                    break;
                
                case 'ftp':
                    $config->set('cdn.ftp.domain', $cdn_domains);
                    break;
                
                case 's3':
                    $config->set('cdn.s3.cname', $cdn_domains);
                    break;
                
                case 'cf':
                    $config->set('cdn.cf.cname', $cdn_domains);
                    break;
                
                case 'rscf':
                    $config->set('cdn.rscf.cname', $cdn_domains);
                    break;
            }
        }
        
        if ($this->config_save($this->_config, $config)) {
            $this->redirect(array_merge($params, array(
                'w3tc_note' => 'config_save'
            )));
        } else {
            $this->redirect(array_merge($params, array(
                'w3tc_error' => 'config_save'
            )));
        }
    }
    
    /**
     * Save config action
     * 
     * Do some actions on config keys update
     * Used in several places such as:
     * 
     * 1. common config save
     * 2. import settings
     * 3. enable/disable preview mode
     * 
     * @param W3_Config $old_config
     * @param W3_Config $new_config
     * @param boolean $preview
     * @return void
     */
    function config_save(&$old_config, &$new_config, $preview = null)
    {
        /**
         * Handle settings change that require pgcache and minify empty
         */
        $pgcache_dependencies = array(
            'pgcache.debug', 
            'dbcache.enabled', 
            'minify.enabled', 
            'cdn.enabled', 
            'mobile.enabled'
        );
        
        $minify_dependencies = array();
        
        if ($new_config->get_boolean('dbcache.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'dbcache.debug'
            ));
        }
        
        if ($new_config->get_boolean('objectcache.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'objectcache.debug'
            ));
        }
        
        if ($new_config->get_boolean('minify.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'minify.debug', 
                'minify.rewrite', 
                'minify.options', 
                'minify.html.enable', 
                'minify.html.inline.css', 
                'minify.html.inline.js', 
                'minify.html.strip.crlf', 
                'minify.css.enable', 
                'minify.css.groups', 
                'minify.js.enable', 
                'minify.js.groups'
            ));
        }
        
        if ($new_config->get_boolean('cdn.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'cdn.debug', 
                'cdn.engine', 
                'cdn.includes.enable', 
                'cdn.includes.files', 
                'cdn.theme.enable', 
                'cdn.theme.files', 
                'cdn.minify.enable', 
                'cdn.custom.enable', 
                'cdn.custom.files', 
                'cdn.ftp.domain', 
                'cdn.s3.bucket', 
                'cdn.cf.id', 
                'cdn.cf.cname'
            ));
        }
        
        if ($new_config->get_boolean('mobile.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'mobile.rgroups'
            ));
        }
        
        if (($new_config->get_boolean('minify.css.enable') && count($new_config->get_array('minify.css.groups'))) || ($new_config->get_boolean('minify.js.enable') && count($new_config->get_array('minify.js.groups')))) {
            $minify_dependencies = array_merge(array(
                'minify.debug', 
                'minify.css.combine', 
                'minify.css.strip.comments', 
                'minify.css.strip.crlf', 
                'minify.css.groups', 
                'minify.js.combine.header', 
                'minify.js.combine.body', 
                'minify.js.combine.footer', 
                'minify.js.strip.comments', 
                'minify.js.strip.crlf', 
                'minify.js.groups'
            ));
        }
        
        $old_pgcache_dependencies_values = array();
        $new_pgcache_dependencies_values = array();
        
        $old_minify_dependencies_values = array();
        $new_minify_dependencies_values = array();
        
        foreach ($pgcache_dependencies as $pgcache_dependency) {
            $old_pgcache_dependencies_values[] = $old_config->get($pgcache_dependency);
            $new_pgcache_dependencies_values[] = $new_config->get($pgcache_dependency);
        }
        
        foreach ($minify_dependencies as $minify_dependency) {
            $old_minify_dependencies_values[] = $old_config->get($minify_dependency);
            $new_minify_dependencies_values[] = $new_config->get($minify_dependency);
        }
        
        /**
         * Show need empty page cache notification
         */
        if ($new_config->get_boolean('pgcache.enabled') && serialize($old_pgcache_dependencies_values) != serialize($new_pgcache_dependencies_values)) {
            $new_config->set('notes.need_empty_pgcache', true);
        }
        
        /**
         * Show need empty minify notification
         */
        if ($new_config->get_boolean('minify.enabled') && serialize($old_minify_dependencies_values) != serialize($new_minify_dependencies_values)) {
            $new_config->set('notes.need_empty_minify', true);
        }
        
        /**
         * Show notification when CDN enabled
         */
        if (!$old_config->get_boolean('cdn.enabled') && $new_config->get_boolean('cdn.enabled') && !in_array($new_config->get_string('cdn.engine'), array(
            'mirror', 
            'netdna'
        ))) {
            $new_config->set('notes.cdn_upload', true);
        }
        
        /**
         * Save config
         */
        if ($new_config->save($preview)) {
            require_once W3TC_LIB_W3_DIR . '/Plugin/PgCache.php';
            require_once W3TC_LIB_W3_DIR . '/Plugin/DbCache.php';
            require_once W3TC_LIB_W3_DIR . '/Plugin/ObjectCache.php';
            require_once W3TC_LIB_W3_DIR . '/Plugin/BrowserCache.php';
            require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
            
            $w3_plugin_pgcache = & W3_Plugin_PgCache::instance();
            $w3_plugin_dbcache = & W3_Plugin_DbCache::instance();
            $w3_plugin_objectcache = & W3_Plugin_ObjectCache::instance();
            $w3_plugin_browsercache = & W3_Plugin_BrowserCache::instance();
            $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
            
            if (W3TC_PHP5) {
                require_once W3TC_LIB_W3_DIR . '/Plugin/Minify.php';
                $w3_plugin_minify = & W3_Plugin_Minify::instance();
            }
            
            /**
             * Empty caches on engine change or cache enable/disable
             */
            if ($old_config->get_string('pgcache.engine') != $new_config->get_string('pgcache.engine') || $old_config->get_string('pgcache.enabled') != $new_config->get_string('pgcache.enabled')) {
                $this->flush_pgcache();
            }
            
            if ($old_config->get_string('dbcache.engine') != $new_config->get_string('dbcache.engine') || $old_config->get_string('dbcache.enabled') != $new_config->get_string('dbcache.enabled')) {
                $this->flush_dbcache();
            }
            
            if ($old_config->get_string('objectcache.engine') != $new_config->get_string('objectcache.engine') || $old_config->get_string('objectcache.enabled') != $new_config->get_string('objectcache.enabled')) {
                $this->flush_objectcache();
            }
            
            if ($old_config->get_string('minify.engine') != $new_config->get_string('minify.engine') || $old_config->get_string('minify.enabled') != $new_config->get_string('minify.enabled')) {
                $this->flush_minify();
            }
            
            /**
             * Unschedule events if changed file gc interval
             */
            if ($old_config->get_integer('pgcache.file.gc') != $new_config->get_integer('pgcache.file.gc')) {
                $w3_plugin_pgcache->unschedule();
            }
            
            if ($old_config->get_integer('pgcache.prime.interval') != $new_config->get_integer('pgcache.prime.interval')) {
                $w3_plugin_pgcache->unschedule_prime();
            }
            
            if ($old_config->get_integer('dbcache.file.gc') != $new_config->get_integer('dbcache.file.gc')) {
                $w3_plugin_dbcache->unschedule();
            }
            
            if ($old_config->get_integer('objectcache.file.gc') != $new_config->get_integer('objectcache.file.gc')) {
                $w3_plugin_objectcache->unschedule();
            }
            
            if ($old_config->get_integer('cdn.autoupload.interval') != $new_config->get_integer('cdn.autoupload.interval')) {
                $w3_plugin_cdn->unschedule_upload();
            }
            
            if (W3TC_PHP5 && $old_config->get_integer('minify.file.gc') != $new_config->get_integer('minify.file.gc')) {
                $w3_plugin_minify->unschedule();
            }
            
            /**
             * Refresh config
             */
            $old_config->load();
            
            /**
             * Schedule events
             */
            $w3_plugin_pgcache->schedule();
            $w3_plugin_pgcache->schedule_prime();
            $w3_plugin_dbcache->schedule();
            $w3_plugin_objectcache->schedule();
            $w3_plugin_cdn->schedule();
            $w3_plugin_cdn->schedule_upload();
            
            if (W3TC_PHP5) {
                $w3_plugin_minify->schedule();
            }
            
            /**
             * Update support us option
             */
            $this->link_update();
            
            /**
             * Write page cache rewrite rules
             */
            if (!w3_is_multisite()) {
                $w3_plugin_pgcache->remove_rules_core();
            }
            
            $w3_plugin_pgcache->remove_rules_cache();
            
            if ($new_config->get_boolean('pgcache.enabled') && $new_config->get_string('pgcache.engine') == 'file_pgcache') {
                if (!w3_is_multisite()) {
                    $w3_plugin_pgcache->write_rules_core();
                }
                
                $w3_plugin_pgcache->write_rules_cache();
            }
            
            /**
             * Write browsercache rules
             */
            if (!w3_is_multisite()) {
                $w3_plugin_browsercache->remove_rules_cache();
                $w3_plugin_browsercache->remove_rules_no404wp();
            }
            
            if ($new_config->get_boolean('browsercache.enabled') && !w3_is_multisite()) {
                $w3_plugin_browsercache->write_rules_cache();
                
                if ($new_config->get_boolean('browsercache.no404wp')) {
                    $w3_plugin_browsercache->write_rules_no404wp();
                }
            }
            
            /**
             * Write minify rewrite rules
             */
            if (W3TC_PHP5) {
                $w3_plugin_minify->remove_rules();
                
                if ($new_config->get_boolean('minify.enabled') && $new_config->get_boolean('minify.rewrite')) {
                    $w3_plugin_minify->write_rules();
                }
            }
            
            /**
             * Auto upload minify files to CDN
             */
            if ($new_config->get_boolean('minify.enabled') && $new_config->get_boolean('minify.upload') && $new_config->get_boolean('cdn.enabled') && !in_array($new_config->get_string('cdn.engine'), array(
                'mirror', 
                'netdna'
            ))) {
                $this->cdn_upload_minify();
            }
            
            /**
             * Auto upload browsercache files to CDN
             */
            if ($new_config->get_boolean('cdn.enabled') && $new_config->get_string('cdn.engine') == 'ftp') {
                $this->cdn_delete_browsercache();
                
                if ($new_config->get_boolean('browsercache.enabled')) {
                    $this->cdn_upload_browsercache();
                }
            }
            
            /**
             * Save blognames into file
             */
            if (w3_is_multisite() && !w3_is_vhost()) {
                w3_save_blognames();
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Import config action
     * 
     * @return void
     */
    function config_import()
    {
        $error = '';
        
        $config = & new W3_Config();
        
        if (!isset($_FILES['config_file']['error']) || $_FILES['config_file']['error'] == UPLOAD_ERR_NO_FILE) {
            $error = 'config_import_no_file';
        } elseif ($_FILES['config_file']['error'] != UPLOAD_ERR_OK) {
            $error = 'config_import_upload';
        } elseif (!$config->read($_FILES['config_file']['tmp_name'])) {
            $error = 'config_import_import';
        }
        
        if ($error) {
            $this->redirect(array(
                'w3tc_error' => $error
            ), true);
        }
        
        if ($this->config_save($this->_config, $config)) {
            $this->redirect(array(
                'w3tc_note' => 'config_import'
            ), true);
        
        } else {
            $this->redirect(array(
                'w3tc_error' => 'config_save'
            ), true);
        }
    }
    
    /**
     * Export config action
     * 
     * @return void
     */
    function config_export()
    {
        @header(sprintf('Content-Disposition: attachment; filename=%s', basename(W3TC_CONFIG_PATH)));
        @readfile(W3TC_CONFIG_PATH);
        die();
    }
    
    /**
     * Reset config action
     * 
     * @return void
     */
    function config_reset()
    {
        $config = & new W3_Config();
        $config->load_defaults();
        $config->set_defaults();
        
        if ($this->config_save($this->_config, $config, true)) {
            $this->redirect(array(
                'w3tc_note' => 'config_reset'
            ), true);
        
        } else {
            $this->redirect(array(
                'w3tc_error' => 'config_reset'
            ), true);
        }
    }
    
    /**
     * Save preview option
     */
    function preview_save()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $preview = W3_Request::get_boolean('preview');
        
        if ($preview) {
            if ($this->_config->save(true)) {
                $this->redirect(array(
                    'w3tc_note' => 'preview_enable'
                ));
            } else {
                $this->redirect(array(
                    'w3tc_error' => 'preview_enable'
                ));
            }
        } else {
            $config = & new W3_Config(false);
            
            if (@unlink(W3TC_CONFIG_PREVIEW_PATH) && $this->config_save($this->_config, $config, false)) {
                $this->redirect(array(
                    'w3tc_note' => 'preview_disable'
                ));
            } else {
                $this->redirect(array(
                    'w3tc_error' => 'preview_disable'
                ));
            }
        }
    }
    
    /**
     * Depoly preview settings action
     */
    function preview_deploy()
    {
        if ($this->_config->save(false)) {
            $this->flush_all();
            
            $this->redirect(array(
                'w3tc_note' => 'preview_deploy'
            ));
        } else {
            $this->redirect(array(
                'w3tc_error' => 'preview_deploy'
            ));
        }
    }
    
    /**
     * Select support type action
     */
    function support_select()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $request_type = W3_Request::get_string('request_type');
        
        if ($request_type == '' || !isset($this->_request_types[$request_type])) {
            $this->redirect(array(
                'w3tc_error' => 'support_request_type'
            ));
        }
        
        if (isset($this->_request_prices[$request_type])) {
            $tab = 'support_payment';
        } else {
            $tab = 'support';
        }
        
        $this->redirect(array(
            'tab' => $tab, 
            'request_type' => $request_type
        ));
    }
    
    /**
     * Send support request
     */
    function support_request()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $required = array(
            'bug_report' => 'url,name,email,subject,description', 
            'new_feature' => 'url,name,email,subject,description', 
            'email_support' => 'url,name,email,subject,description', 
            'phone_support' => 'url,name,email,subject,description,phone', 
            'plugin_config' => 'url,name,email,subject,description,wp_login,wp_password', 
            'theme_config' => 'url,name,email,subject,description,wp_login,wp_password,ftp_host,ftp_login,ftp_password', 
            'linux_config' => 'url,name,email,subject,description,wp_login,wp_password,ftp_host,ftp_login,ftp_password'
        );
        
        $request_type = W3_Request::get_string('request_type');
        $payment = W3_Request::get_boolean('payment');
        $request_type_text = (isset($this->_request_types[$request_type]) ? $this->_request_types[$request_type] : 'Unknown');
        $url = W3_Request::get_string('url');
        $name = W3_Request::get_string('name');
        $email = W3_Request::get_string('email');
        $twitter = W3_Request::get_string('twitter');
        $phone = W3_Request::get_string('phone');
        $subject = W3_Request::get_string('subject');
        $description = W3_Request::get_string('description');
        $templates = W3_Request::get_array('templates');
        $forum_url = W3_Request::get_string('forum_url');
        $wp_login = W3_Request::get_string('wp_login');
        $wp_password = W3_Request::get_string('wp_password');
        $ftp_host = W3_Request::get_string('ftp_host');
        $ftp_login = W3_Request::get_string('ftp_login');
        $ftp_password = W3_Request::get_string('ftp_password');
        
        $params = array(
            'request_type' => $request_type, 
            'payment' => $payment, 
            'url' => $url, 
            'name' => $name, 
            'email' => $email, 
            'twitter' => $twitter, 
            'phone' => $phone, 
            'subject' => $subject, 
            'description' => $description, 
            'forum_url' => $forum_url, 
            'wp_login' => $wp_login, 
            'wp_password' => $wp_password, 
            'ftp_host' => $ftp_host, 
            'ftp_login' => $ftp_login, 
            'ftp_password' => $ftp_password
        );
        
        foreach ($templates as $template_index => $template) {
            $template_key = sprintf('templates[%d]', $template_index);
            $params[$template_key] = $template;
        }
        
        if (strstr($required[$request_type], 'url') !== false && $url == '') {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_url'
            )));
        }
        
        if (strstr($required[$request_type], 'name') !== false && $name == '') {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_name'
            )));
        }
        
        if (strstr($required[$request_type], 'email') !== false && !preg_match('~^[a-z0-9_\-\.]+@[a-z0-9-\.]+\.[a-z]{2,5}$~', $email)) {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_email'
            )));
        }
        
        if (strstr($required[$request_type], 'phone') !== false && !preg_match('~^[0-9\-\.\ \(\)\+]+$~', $phone)) {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_phone'
            )));
        }
        
        if (strstr($required[$request_type], 'subject') !== false && $subject == '') {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_subject'
            )));
        }
        
        if (strstr($required[$request_type], 'description') !== false && $description == '') {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_description'
            )));
        }
        
        if (strstr($required[$request_type], 'wp_login') !== false && $wp_login == '') {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_wp_login'
            )));
        }
        
        if (strstr($required[$request_type], 'wp_password') !== false && $wp_password == '') {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_wp_password'
            )));
        }
        
        if (strstr($required[$request_type], 'ftp_host') !== false && $ftp_host == '') {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_ftp_host'
            )));
        }
        
        if (strstr($required[$request_type], 'ftp_login') !== false && $ftp_login == '') {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_ftp_login'
            )));
        }
        
        if (strstr($required[$request_type], 'ftp_password') !== false && $ftp_password == '') {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request_ftp_password'
            )));
        }
        
        /**
         * Add attachments
         */
        $attachments = array();
        
        $config_files = array(
            W3TC_CONFIG_PATH, 
            W3TC_CONFIG_PREVIEW_PATH, 
            W3TC_CONFIG_MASTER_PATH
        );
        
        foreach ($config_files as $config_file) {
            if (file_exists($config_file) && !in_array($config_file, $attachments)) {
                $attachments[] = $config_file;
            }
        }
        
        /**
         * Attach server info
         */
        $server_info = print_r($this->get_server_info(), true);
        $server_info = str_replace("\n", "\r\n", $server_info);
        
        $server_info_path = W3TC_TMP_DIR . '/server_info.txt';
        
        if (@file_put_contents($server_info_path, $server_info)) {
            $attachments[] = $server_info_path;
        }
        
        /**
         * Attach phpinfo
         */
        ob_start();
        phpinfo();
        $php_info = ob_get_contents();
        ob_end_clean();
        
        $php_info_path = W3TC_TMP_DIR . '/php_info.html';
        
        if (@file_put_contents($php_info_path, $php_info)) {
            $attachments[] = $php_info_path;
        }
        
        /**
         * Attach minify log
         */
        if (file_exists(W3TC_MINIFY_LOG_FILE)) {
            $attachments[] = W3TC_MINIFY_LOG_FILE;
        }
        
        /**
         * Attach templates
         */
        foreach ($templates as $template) {
            if (!empty($template)) {
                $attachments[] = $template;
            }
        }
        
        /**
         * Attach other files
         */
        if (!empty($_FILES['files'])) {
            $files = (array) $_FILES['files'];
            for ($i = 0, $l = count($files); $i < $l; $i++) {
                if (isset($files['tmp_name'][$i]) && isset($files['name'][$i]) && isset($files['error'][$i]) && $files['error'][$i] == UPLOAD_ERR_OK) {
                    $path = W3TC_TMP_DIR . '/' . $files['name'][$i];
                    if (@move_uploaded_file($files['tmp_name'][$i], $path)) {
                        $attachments[] = $path;
                    }
                }
            }
        }
        
        $data = array();
        
        if (!empty($wp_login) && !empty($wp_password)) {
            $data['WP Admin login'] = $wp_login;
            $data['WP Admin password'] = $wp_password;
        }
        
        if (!empty($ftp_host) && !empty($ftp_login) && !empty($ftp_password)) {
            $data['SSH / FTP host'] = $ftp_host;
            $data['SSH / FTP login'] = $ftp_login;
            $data['SSH / FTP password'] = $ftp_password;
        }
        
        /**
         * Store request data for future access
         */
        if (count($data)) {
            $hash = md5(microtime());
            $request_data = get_option('w3tc_request_data', array());
            $request_data[$hash] = $data;
            
            update_option('w3tc_request_data', $request_data);
            
            $request_data_url = sprintf('%s/w3tc_request_data/%s', w3_get_site_url(), $hash);
        } else {
            $request_data_url = null;
        }
        
        /**
         * Get body contents
         */
        ob_start();
        include W3TC_DIR . '/inc/email/support_request.phtml';
        $body = ob_get_contents();
        ob_end_clean();
        
        /**
         * Send email
         */
        $subject = sprintf('[W3TC %s] #%s: %s', $request_type_text, date('YmdHi'), $subject);
        
        $headers = array(
            sprintf('From: "%s" <%s>', addslashes($name), $email), 
            sprintf('Reply-To: "%s" <%s>', addslashes($name), $email), 
            'Content-Type: text/html; charset=UTF-8'
        );
        
        $this->_phpmailer_sender = $email;
        
        add_action('phpmailer_init', array(
            &$this, 
            'phpmailer_init'
        ));
        
        @set_time_limit(120);
        
        $result = @wp_mail(W3TC_EMAIL, $subject, $body, implode("\n", $headers), $attachments);
        
        /**
         * Remove temporary files
         */
        foreach ($attachments as $attachment) {
            if (strstr($attachment, W3TC_TMP_DIR) !== false) {
                @unlink($attachment);
            }
        }
        
        if ($result) {
            $this->redirect(array(
                'tab' => 'general', 
                'w3tc_note' => 'support_request'
            ));
        } else {
            $this->redirect(array_merge($params, array(
                'request_type' => $request_type, 
                'w3tc_error' => 'support_request'
            )));
        }
    }
    
    /**
     * PHPMailer init function
     *
     * @param PHPMailer $phpmailer
     * @return void
     */
    function phpmailer_init(&$phpmailer)
    {
        $phpmailer->Sender = $this->_phpmailer_sender;
    }
    
    /**
     * Returns button html
     *
     * @param string $text
     * @param string $onclick
     * @param string $class
     * @return string
     */
    function button($text, $onclick = '', $class = '')
    {
        return sprintf('<input type="button" class="button %s" value="%s" onclick="%s" />', htmlspecialchars($class), htmlspecialchars($text), htmlspecialchars($onclick));
    }
    
    /**
     * Returns button link html
     *
     * @param string $text
     * @param string $url
     * @param boolean $new_window
     * @return string
     */
    function button_link($text, $url, $new_window = false)
    {
        if ($new_window) {
            $onclick = sprintf('window.open(\'%s\');', addslashes($url));
        } else {
            $onclick = sprintf('document.location.href = \'%s\';', addslashes($url));
        }
        
        return $this->button($text, $onclick);
    }
    
    /**
     * Returns hide note button html
     *
     * @param string $text
     * @param string $note
     * @param string $redirect
     * @return string
     */
    function button_hide_note($text, $note, $redirect = '')
    {
        $url = sprintf('admin.php?page=%s&hide_note=%s', $this->_page, $note);
        
        if ($redirect != '') {
            $url .= '&redirect=' . urlencode($redirect);
        }
        
        return $this->button_link($text, $url);
    }
    
    /**
     * Returns popup button html
     *
     * @param string $text
     * @param string $w3tc_action
     * @param string $params
     * @param integer $width
     * @param integer $height
     * @return string
     */
    function button_popup($text, $w3tc_action, $params = '', $width = 800, $height = 600)
    {
        $onclick = sprintf('window.open(\'admin.php?page=w3tc_general&w3tc_action=%s%s\', \'%s\', \'width=%d,height=%d,status=no,toolbar=no,menubar=no,scrollbars=yes\');', $w3tc_action, ($params != '' ? '&' . $params : ''), $w3tc_action, $width, $height);
        
        return $this->button($text, $onclick);
    }
    
    /**
     * CDN queue action
     */
    function cdn_queue()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        $cdn_queue_action = W3_Request::get_string('cdn_queue_action');
        $cdn_queue_tab = W3_Request::get_string('cdn_queue_tab');
        
        $notes = array();
        
        switch ($cdn_queue_tab) {
            case 'upload':
            case 'delete':
                break;
            
            default:
                $cdn_queue_tab = 'upload';
        }
        
        switch ($cdn_queue_action) {
            case 'delete':
                $cdn_queue_id = W3_Request::get_integer('cdn_queue_id');
                if (!empty($cdn_queue_id)) {
                    $w3_plugin_cdn->queue_delete($cdn_queue_id);
                    $notes[] = 'File successfully deleted from the queue.';
                }
                break;
            
            case 'empty':
                $cdn_queue_type = W3_Request::get_integer('cdn_queue_type');
                if (!empty($cdn_queue_type)) {
                    $w3_plugin_cdn->queue_empty($cdn_queue_type);
                    $notes[] = 'Queue successfully emptied.';
                }
                break;
        }
        
        $queue = $w3_plugin_cdn->queue_get();
        $title = 'Unsuccessful file transfer queue.';
        
        include W3TC_DIR . '/inc/popup/cdn_queue.phtml';
    }
    
    /**
     * CDN export library action
     */
    function cdn_export_library()
    {
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        
        $total = $w3_plugin_cdn->get_attachments_count();
        $title = 'Media Library export';
        
        include W3TC_DIR . '/inc/popup/cdn_export_library.phtml';
    }
    
    /**
     * CDN export library process
     */
    function cdn_export_library_process()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        
        $limit = W3_Request::get_integer('limit');
        $offset = W3_Request::get_integer('offset');
        
        $count = null;
        $total = null;
        $results = array();
        
        @$w3_plugin_cdn->export_library($limit, $offset, $count, $total, $results);
        
        $response = array(
            'limit' => $limit, 
            'offset' => $offset, 
            'count' => $count, 
            'total' => $total, 
            'results' => $results
        );
        
        echo json_encode($response);
    }
    
    /**
     * CDN import library action
     */
    function cdn_import_library()
    {
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        $cdn = & $w3_plugin_cdn->get_cdn();
        
        $total = $w3_plugin_cdn->get_import_posts_count();
        $cdn_host = $cdn->get_domain();
        
        $title = 'Media Library import';
        
        include W3TC_DIR . '/inc/popup/cdn_import_library.phtml';
    }
    
    /**
     * CDN import library process
     */
    function cdn_import_library_process()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        
        $limit = W3_Request::get_integer('limit');
        $offset = W3_Request::get_integer('offset');
        
        $count = null;
        $total = null;
        $results = array();
        
        @$w3_plugin_cdn->import_library($limit, $offset, $count, $total, $results);
        
        $response = array(
            'limit' => $limit, 
            'offset' => $offset, 
            'count' => $count, 
            'total' => $total, 
            'results' => $results
        );
        
        echo json_encode($response);
    }
    
    /**
     * CDN rename domain action
     */
    function cdn_rename_domain()
    {
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        
        $total = $w3_plugin_cdn->get_rename_posts_count();
        
        $title = 'Modify attachment URLs';
        
        include W3TC_DIR . '/inc/popup/cdn_rename_domain.phtml';
    }
    
    /**
     * CDN rename domain process
     */
    function cdn_rename_domain_process()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        
        $limit = W3_Request::get_integer('limit');
        $offset = W3_Request::get_integer('offset');
        $names = W3_Request::get_array('names');
        
        $count = null;
        $total = null;
        $results = array();
        
        @$w3_plugin_cdn->rename_domain($names, $limit, $offset, $count, $total, $results);
        
        $response = array(
            'limit' => $limit, 
            'offset' => $offset, 
            'count' => $count, 
            'total' => $total, 
            'results' => $results
        );
        
        echo json_encode($response);
    }
    
    /**
     * CDN export action
     */
    function cdn_export()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        
        $cdn_export_type = W3_Request::get_string('cdn_export_type', 'custom');
        
        switch ($cdn_export_type) {
            case 'includes':
                $title = 'Includes files export';
                $files = $w3_plugin_cdn->get_files_includes();
                break;
            
            case 'theme':
                $title = 'Theme files export';
                $files = $w3_plugin_cdn->get_files_theme();
                break;
            
            case 'minify':
                $title = 'Minify files export';
                $files = $w3_plugin_cdn->get_files_minify();
                break;
            
            default:
            case 'custom':
                $title = 'Custom files export';
                $files = $w3_plugin_cdn->get_files_custom();
                break;
        }
        
        include W3TC_DIR . '/inc/popup/cdn_export_file.phtml';
    }
    
    /**
     * CDN export process
     */
    function cdn_export_process()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        
        $files = W3_Request::get_array('files');
        $document_root = w3_get_document_root();
        
        $upload = array();
        $results = array();
        
        foreach ($files as $file) {
            $upload[$document_root . '/' . $file] = $file;
        }
        
        $w3_plugin_cdn->upload($upload, false, $results);
        
        $response = array(
            'results' => $results
        );
        
        echo json_encode($response);
    }
    
    /**
     * Uploads minify files to CDN
     * 
     * @return array
     */
    function cdn_upload_minify()
    {
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        
        $files = $w3_plugin_cdn->get_files_minify();
        $document_root = w3_get_document_root();
        
        $upload = array();
        $results = array();
        
        foreach ($files as $file) {
            $upload[$document_root . '/' . $file] = $file;
        }
        
        return $w3_plugin_cdn->upload($upload, true, $results);
    }
    
    /**
     * Uploads browsercache .htaccess to FTP
     * 
     * @return boolean
     */
    function cdn_upload_browsercache()
    {
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        require_once W3TC_LIB_W3_DIR . '/Plugin/BrowserCache.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        $w3_plugin_browsercache = & W3_Plugin_BrowserCache::instance();
        
        $tmp_root = W3TC_TMP_DIR;
        $document_root = w3_get_document_root();
        $tmp_path = ltrim(str_replace($document_root, '', $tmp_root), '/');
        
        $path = W3TC_TMP_DIR . '/htaccess_ftp.txt';
        $rules = $w3_plugin_browsercache->generate_rules_cache();
        
        if (@file_put_contents($path, $rules)) {
            $results = array();
            $upload = array(
                $path => $tmp_path . '/.htaccess'
            );
            
            return $w3_plugin_cdn->upload($upload, true, $results);
        }
        
        return false;
    }
    
    /**
     * Deletes browsercache .htaccess to FTP
     * 
     * @return boolean
     */
    function cdn_delete_browsercache()
    {
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        require_once W3TC_LIB_W3_DIR . '/Plugin/BrowserCache.php';
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        $w3_plugin_browsercache = & W3_Plugin_BrowserCache::instance();
        
        $tmp_root = W3TC_TMP_DIR;
        $document_root = w3_get_document_root();
        $tmp_path = ltrim(str_replace($document_root, '', $tmp_root), '/');
        
        $path = W3TC_TMP_DIR . '/htaccess_ftp.txt';
        
        $results = array();
        $delete = array(
            $path => $tmp_path . '/.htaccess'
        );
        
        return $w3_plugin_cdn->delete($delete, true, $results);
    }
    
    /**
     * CDN Test action
     * 
     * @return void
     */
    function cdn_test()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        require_once W3TC_LIB_W3_DIR . '/Cdn.php';
        
        $engine = W3_Request::get_string('engine');
        $config = W3_Request::get_array('config');
        
        switch ($engine) {
            case 'mirror':
            case 'netdna':
            case 'ftp':
            case 's3':
            case 'cf':
            case 'rscf':
                $result = true;
                break;
            
            default:
                $result = false;
                $error = 'Incorrect engine.';
                break;
        }
        
        if ($result) {
            $w3_cdn = & W3_Cdn::instance($engine, $config);
            $error = null;
            @set_time_limit(120);
            
            if ($w3_cdn->test($error)) {
                $result = true;
                $error = 'Test passed';
            } else {
                $result = false;
                $error = sprintf('Error: %s', $error);
            }
        }
        
        $response = array(
            'result' => $result, 
            'error' => $error
        );
        
        echo json_encode($response);
    }
    
    /**
     * Create container action
     */
    function cdn_create_container()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        require_once W3TC_LIB_W3_DIR . '/Cdn.php';
        
        $engine = W3_Request::get_string('engine');
        $config = W3_Request::get_array('config');
        
        $result = false;
        $error = 'Incorrect type.';
        $container_id = '';
        
        switch ($engine) {
            case 's3':
            case 'cf':
            case 'rscf':
                $result = true;
                break;
        }
        
        if ($result) {
            $w3_cdn = & W3_Cdn::instance($engine, $config);
            
            @set_time_limit(120);
            
            if ($w3_cdn->create_container($container_id, $error)) {
                $result = true;
                $error = 'Created successfully.';
            } else {
                $result = false;
                $error = sprintf('Error: %s', $error);
            }
        }
        
        $response = array(
            'result' => $result, 
            'error' => $error, 
            'container_id' => $container_id
        );
        
        echo json_encode($response);
    }
    
    /**
     * CDN Purge Post
     */
    function cdn_purge_attachment()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
        
        $attachment_id = W3_Request::get_integer('attachment_id');
        
        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
        
        if ($w3_plugin_cdn->purge_attachment($attachment_id)) {
            $this->redirect(array(
                'w3tc_note' => 'cdn_purge_attachment'
            ), true);
        } else {
            $this->redirect(array(
                'w3tc_error' => 'cdn_purge_attachment'
            ), true);
        }
    }
    
    /**
     * PgCache purge post
     */
    function pgcache_purge_post()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        require_once W3TC_DIR . '/lib/W3/PgCache.php';
        
        $post_id = W3_Request::get_integer('post_id');
        
        $w3_pgcache = & W3_PgCache::instance();
        
        if ($w3_pgcache->flush_post($post_id)) {
            $this->redirect(array(
                'w3tc_note' => 'pgcache_purge_post'
            ), true);
        
        } else {
            $this->redirect(array(
                'w3tc_error' => 'pgcache_purge_post'
            ), true);
        }
    }
    
    /**
     * Check if memcache is available
     *
     * @param array $servers
     * @return boolean
     */
    function is_memcache_available($servers)
    {
        static $results = array();
        $key = md5(serialize($servers));
        
        if (!isset($results[$key])) {
            require_once W3TC_LIB_W3_DIR . '/Cache/Memcached.php';
            
            $memcached = & new W3_Cache_Memcached(array(
                'servers' => $servers, 
                'persistant' => false
            ));
            
            $test_string = sprintf('test_' . md5(time()));
            $memcached->set($test_string, $test_string, 60);
            
            $results[$key] = ($memcached->get($test_string) == $test_string);
        }
        
        return $results[$key];
    }
    
    /**
     * Test memcached
     */
    function test_memcached()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $servers = W3_Request::get_array('servers');
        
        if ($this->is_memcache_available($servers)) {
            $result = true;
            $error = 'Test passed.';
        } else {
            $result = false;
            $error = 'Test failed.';
        }
        
        $response = array(
            'result' => $result, 
            'error' => $error
        );
        
        echo json_encode($response);
    }
    
    /**
     * Update plugin link
     */
    function link_update()
    {
        $this->link_delete();
        $this->link_insert();
    }
    
    /**
     * Insert plugin link into Blogroll
     */
    function link_insert()
    {
        $support = $this->_config->get_string('common.support');
        $matches = null;
        if ($support != '' && preg_match('~^link_category_(\d+)$~', $support, $matches)) {
            require_once ABSPATH . 'wp-admin/includes/bookmark.php';
            
            wp_insert_link(array(
                'link_url' => W3TC_LINK_URL, 
                'link_name' => W3TC_LINK_NAME, 
                'link_category' => array(
                    (int) $matches[1]
                )
            ));
        }
    }
    /**
     * Deletes plugin link from Blogroll
     */
    function link_delete()
    {
        $bookmarks = get_bookmarks();
        $link_id = 0;
        foreach ($bookmarks as $bookmark) {
            if ($bookmark->link_url == W3TC_LINK_URL) {
                $link_id = $bookmark->link_id;
                break;
            }
        }
        if ($link_id) {
            require_once ABSPATH . 'wp-admin/includes/bookmark.php';
            wp_delete_link($link_id);
        }
    }
    
    /**
     * Flush specified cache
     *
     * @param string $type
     */
    function flush($type)
    {
        if ($this->_config->get_string('pgcache.engine') == $type && $this->_config->get_boolean('pgcache.enabled')) {
            $this->_config->set('notes.need_empty_pgcache', false);
            $this->_config->set('notes.plugins_updated', false);
            
            if (!$this->_config->save()) {
                $this->redirect(array(
                    'w3tc_error' => 'config_save'
                ));
            }
            
            $this->flush_pgcache();
        }
        
        if ($this->_config->get_string('dbcache.engine') == $type && $this->_config->get_boolean('dbcache.enabled')) {
            $this->flush_dbcache();
        }
        
        if ($this->_config->get_string('objectcache.engine') == $type && $this->_config->get_boolean('objectcache.enabled')) {
            $this->flush_objectcache();
        }
        
        if ($this->_config->get_string('minify.engine') == $type && $this->_config->get_boolean('minify.enabled')) {
            $this->_config->set('notes.need_empty_minify', false);
            
            if (!$this->_config->save()) {
                $this->redirect(array(
                    'w3tc_error' => 'config_save'
                ));
            }
            
            $this->flush_minify();
        }
    }
    
    /**
     * Flush memcached cache
     *
     * @return void
     */
    function flush_memcached()
    {
        $this->flush('memcached');
    }
    
    /**
     * Flush APC cache
     * @return void
     */
    function flush_opcode()
    {
        $this->flush('apc');
        $this->flush('eaccelerator');
        $this->flush('xcache');
    }
    
    /**
     * Flush file cache
     *
     * @return void
     */
    function flush_file()
    {
        $this->flush('file');
        $this->flush('file_pgcache');
    }
    
    /**
     * Flush all cache
     * 
     * @return void
     */
    function flush_all()
    {
        $this->flush_memcached();
        $this->flush_opcode();
        $this->flush_file();
    }
    
    /**
     * Flush page cache
     */
    function flush_pgcache()
    {
        require_once W3TC_DIR . '/lib/W3/PgCache.php';
        $w3_pgcache = & W3_PgCache::instance();
        $w3_pgcache->flush();
    }
    
    /**
     * Flush page cache
     */
    function flush_dbcache()
    {
        require_once W3TC_DIR . '/lib/W3/Db.php';
        $w3_db = & W3_Db::instance();
        $w3_db->flush_cache();
    }
    
    /**
     * Flush page cache
     */
    function flush_objectcache()
    {
        require_once W3TC_DIR . '/lib/W3/ObjectCache.php';
        $w3_objectcache = & W3_ObjectCache::instance();
        $w3_objectcache->flush();
    }
    
    /**
     * Flush minify cache
     */
    function flush_minify()
    {
        if (W3TC_PHP5) {
            require_once W3TC_DIR . '/lib/W3/Minify.php';
            $w3_minify = & W3_Minify::instance();
            $w3_minify->flush();
        }
    }
    
    /**
     * Checks if advanced-cache.php exists
     *
     * @return boolean
     */
    function check_advanced_cache()
    {
        return (file_exists(WP_CONTENT_DIR . '/advanced-cache.php') && ($script_data = @file_get_contents(WP_CONTENT_DIR . '/advanced-cache.php')) && strstr($script_data, 'W3_PgCache') !== false);
    }
    
    /**
     * Checks if db.php exists
     *
     * @return boolean
     */
    function check_db()
    {
        return (file_exists(WP_CONTENT_DIR . '/db.php') && ($script_data = @file_get_contents(WP_CONTENT_DIR . '/db.php')) && strstr($script_data, 'W3_Db') !== false);
    }
    
    /**
     * Checks if db.php exists
     *
     * @return boolean
     */
    function check_objectcache()
    {
        return (file_exists(WP_CONTENT_DIR . '/object-cache.php') && ($script_data = @file_get_contents(WP_CONTENT_DIR . '/object-cache.php')) && strstr($script_data, 'W3_ObjectCache') !== false);
    }
    
    /**
     * Output buffering callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer)
    {
        global $wpdb;
        
        if ($buffer != '' && w3_is_xml($buffer)) {
            if (w3_is_database_error($buffer)) {
                @header('HTTP/1.1 503 Service Unavailable');
            } elseif ($this->can_modify_contents()) {
                /**
                 * Replace links for preview mode
                 */
                if (w3_is_preview_mode() && isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] != W3TC_POWERED_BY) {
                    $domain_url_regexp = w3_get_domain_url_regexp();
                    
                    $buffer = preg_replace_callback('~(href|src|action)=([\'"])(' . $domain_url_regexp . ')?(/[^\'"]*)~', array(
                        &$this, 
                        'link_replace_callback'
                    ), $buffer);
                }
                
                /**
                 * Add footer comment
                 */
                $date = date('Y-m-d H:i:s');
                $host = (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
                
                if ($this->is_supported()) {
                    $buffer .= sprintf("\r\n<!-- Served from: %s @ %s by W3 Total Cache -->", $host, $date);
                } else {
                    $buffer .= "\r\n<!-- Performance optimized by W3 Total Cache. Learn more: http://www.w3-edge.com/wordpress-plugins/\r\n\r\n";
                    
                    if ($this->_config->get_boolean('minify.enabled')) {
                        require_once W3TC_LIB_W3_DIR . '/Plugin/Minify.php';
                        $w3_plugin_minify = & W3_Plugin_Minify::instance();
                        
                        $buffer .= sprintf("Minified using %s%s\r\n", w3_get_engine_name($this->_config->get_string('minify.engine')), ($w3_plugin_minify->minify_reject_reason != '' ? sprintf(' (%s)', $w3_plugin_minify->minify_reject_reason) : ''));
                    }
                    
                    if ($this->_config->get_boolean('pgcache.enabled')) {
                        require_once W3TC_LIB_W3_DIR . '/PgCache.php';
                        $w3_pgcache = & W3_PgCache::instance();
                        
                        $buffer .= sprintf("Page Caching using %s%s\r\n", w3_get_engine_name($this->_config->get_string('pgcache.engine')), ($w3_pgcache->cache_reject_reason != '' ? sprintf(' (%s)', $w3_pgcache->cache_reject_reason) : ''));
                    }
                    
                    if ($this->_config->get_boolean('dbcache.enabled') && is_a($wpdb, 'W3_Db')) {
                        $append = (is_user_logged_in() ? ' (user is logged in)' : '');
                        
                        if ($wpdb->query_hits) {
                            $buffer .= sprintf("Database Caching %d/%d queries in %.3f seconds using %s%s\r\n", $wpdb->query_hits, $wpdb->query_total, $wpdb->time_total, w3_get_engine_name($this->_config->get_string('dbcache.engine')), $append);
                        } else {
                            $buffer .= sprintf("Database Caching using %s%s\r\n", w3_get_engine_name($this->_config->get_string('dbcache.engine')), $append);
                        }
                    }
                    
                    if ($this->_config->get_boolean('objectcache.enabled')) {
                        require_once W3TC_LIB_W3_DIR . '/ObjectCache.php';
                        $w3_objectcache = & W3_ObjectCache::instance();
                        
                        $buffer .= sprintf("Object Caching %d/%d objects using %s\r\n", $w3_objectcache->cache_hits, $w3_objectcache->cache_total, w3_get_engine_name($this->_config->get_string('objectcache.engine')));
                    }
                    
                    if ($this->_config->get_boolean('cdn.enabled')) {
                        require_once W3TC_LIB_W3_DIR . '/Plugin/Cdn.php';
                        
                        $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
                        $cdn = & $w3_plugin_cdn->get_cdn();
                        $via = $cdn->get_via();
                        
                        $buffer .= sprintf("Content Delivery Network via %s%s\r\n", ($via ? $via : 'N/A'), ($w3_plugin_cdn->cdn_reject_reason != '' ? sprintf(' (%s)', $w3_plugin_cdn->cdn_reject_reason) : ''));
                    }
                    
                    $buffer .= sprintf("\r\nServed from: %s @ %s -->", $host, $date);
                }
            }
        }
        
        return $buffer;
    }
    
    /**
     * Check if we can do modify contents
     * @return boolean
     */
    function can_modify_contents()
    {
        /**
         * Skip if admin
         */
        if (defined('WP_ADMIN')) {
            return false;
        }
        
        /**
         * Skip if doint AJAX
         */
        if (defined('DOING_AJAX')) {
            return false;
        }
        
        /**
         * Skip if doing cron
         */
        if (defined('DOING_CRON')) {
            return false;
        }
        
        /**
         * Skip if APP request
         */
        if (defined('APP_REQUEST')) {
            return false;
        }
        
        /**
         * Skip if XMLRPC request
         */
        if (defined('XMLRPC_REQUEST')) {
            return false;
        }
        
        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            return false;
        }
        
        /**
         * Check request URI
         */
        if (!$this->check_request_uri()) {
            return false;
        }
        
        /**
         * Skip if debug mode is enabled
         */
        $debug = ($this->_config->get_boolean('pgcache.enabled') && $this->_config->get_boolean('pgcache.debug'));
        $debug = $debug || ($this->_config->get_boolean('dbcache.enabled') && $this->_config->get_boolean('dbcache.debug'));
        $debug = $debug || ($this->_config->get_boolean('objectcache.enabled') && $this->_config->get_boolean('objectcache.debug'));
        $debug = $debug || ($this->_config->get_boolean('minify.enabled') && $this->_config->get_boolean('minify.debug'));
        $debug = $debug || ($this->_config->get_boolean('cdn.enabled') && $this->_config->get_boolean('cdn.debug'));
        
        if ($debug) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Checks request URI
     *
     * @return boolean
     */
    function check_request_uri()
    {
        $reject_uri = array(
            'wp-login', 
            'wp-register'
        );
        
        foreach ($reject_uri as $uri) {
            if (strstr($_SERVER['REQUEST_URI'], $uri) !== false) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Returns server info
     */
    function get_server_info()
    {
        global $wp_version, $wp_db_version, $wpdb;
        
        $wordpress_plugins = get_plugins();
        $wordpress_plugins_active = array();
        
        foreach ($wordpress_plugins as $wordpress_plugin_file => $wordpress_plugin) {
            if (is_plugin_active($wordpress_plugin_file)) {
                $wordpress_plugins_active[$wordpress_plugin_file] = $wordpress_plugin;
            }
        }
        
        $w3tc_config = (array) @include W3TC_CONFIG_PATH;
        $mysql_version = $wpdb->get_var('SELECT VERSION()');
        $mysql_variables_result = (array) $wpdb->get_results('SHOW VARIABLES', ARRAY_N);
        $mysql_variables = array();
        
        foreach ($mysql_variables_result as $mysql_variables_row) {
            $mysql_variables[$mysql_variables_row[0]] = $mysql_variables_row[1];
        }
        
        $server_info = array(
            'wp' => array(
                'version' => $wp_version, 
                'db_version' => $wp_db_version, 
                'abspath' => ABSPATH, 
                'home' => get_option('home'), 
                'siteurl' => get_option('siteurl'), 
                'email' => get_option('admin_email'), 
                'upload_info' => (array) w3_upload_info(), 
                'theme' => get_theme(get_current_theme()), 
                'plugins' => $wordpress_plugins_active, 
                'wp_cache' => ((defined('WP_CACHE') && WP_CACHE) ? 'true' : 'false')
            ), 
            'w3tc' => array(
                'version' => W3TC_VERSION, 
                'dir' => W3TC_DIR, 
                'content_dir' => W3TC_CONTENT_DIR, 
                'blogname' => W3TC_BLOGNAME, 
                'document_root' => w3_get_document_root(), 
                'home_root' => w3_get_home_root(), 
                'site_root' => w3_get_site_root(), 
                'base_path' => w3_get_base_path(), 
                'home_path' => w3_get_home_path(), 
                'site_path' => w3_get_site_path()
            ), 
            'mysql' => array(
                'version' => $mysql_version, 
                'variables' => $mysql_variables
            )
        );
        
        return $server_info;
    }
    
    /**
     * Support Us action
     */
    function support_us()
    {
        $supports = $this->get_supports();
        
        include W3TC_DIR . '/inc/lightbox/support_us.phtml';
    }
    
    /**
     * Tweet action
     */
    function tweet()
    {
        include W3TC_DIR . '/inc/lightbox/tweet.phtml';
    }
    
    /**
     * Tweet action
     */
    function minify_recommendations()
    {
        $themes = $this->get_themes();
        
        $current_theme = get_current_theme();
        $current_theme_key = array_search($current_theme, $themes);
        
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $theme_key = W3_Request::get_string('theme_key', $current_theme_key);
        $theme_name = (isset($themes[$theme_key]) ? $themes[$theme_key] : $current_theme);
        
        $templates = $this->get_theme_templates($theme_name);
        $recommendations = $this->get_theme_recommendations($theme_name);
        
        list($js_groups, $css_groups) = $recommendations;
        
        $minify_js_groups = $this->_config->get_array('minify.js.groups');
        $minify_css_groups = $this->_config->get_array('minify.css.groups');
        
        $checked_js = array();
        $checked_css = array();
        
        $locations_js = array();
        
        if (isset($minify_js_groups[$theme_key])) {
            foreach ((array) $minify_js_groups[$theme_key] as $template => $locations) {
                foreach ((array) $locations as $location => $config) {
                    if (isset($config['files'])) {
                        foreach ((array) $config['files'] as $file) {
                            if (!isset($js_groups[$template]) || !in_array($file, $js_groups[$template])) {
                                $js_groups[$template][] = $file;
                            }
                            
                            $checked_js[$template][$file] = true;
                            $locations_js[$template][$file] = $location;
                        }
                    }
                }
            }
        }
        
        if (isset($minify_css_groups[$theme_key])) {
            foreach ((array) $minify_css_groups[$theme_key] as $template => $locations) {
                foreach ((array) $locations as $location => $config) {
                    if (isset($config['files'])) {
                        foreach ((array) $config['files'] as $file) {
                            if (!isset($css_groups[$template]) || !in_array($file, $css_groups[$template])) {
                                $css_groups[$template][] = $file;
                            }
                            
                            $checked_css[$template][$file] = true;
                        }
                    }
                }
            }
        }
        
        include W3TC_DIR . '/inc/lightbox/minify_recommendations.phtml';
    }
    
    /**
     * Update twitter status
     */
    function twitter_status_update()
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $username = W3_Request::get_string('username');
        $password = W3_Request::get_string('password');
        
        $error = 'OK';
        
        if (w3_twitter_status_update($username, $password, W3TC_TWITTER_STATUS, $error)) {
            $this->_config->set('common.tweeted', time());
            
            if ($this->_config->save()) {
                $result = true;
            } else {
                $error = 'Unable to save config.';
                $result = false;
            }
        } else {
            $result = false;
        }
        
        $response = array(
            'result' => $result, 
            'error' => $error
        );
        
        echo json_encode($response);
    }
    
    /**
     * Returns list of support types
     * @return array
     */
    function get_supports()
    {
        $supports = array(
            'footer' => 'page footer'
        );
        
        $link_categories = get_terms('link_category', array(
            'hide_empty' => 0
        ));
        
        foreach ($link_categories as $link_category) {
            $supports['link_category_' . $link_category->term_id] = strtolower($link_category->name);
        }
        
        return $supports;
    }
    
    /**
     * Returns true if is supported
     * @return boolean
     */
    function is_supported()
    {
        return ($this->_config->get_string('common.support') != '' || $this->_config->get_string('common.tweeted'));
    }
    
    /**
     * Redirect function
     *
     * @param boolean $check_referer
     */
    function redirect($params = array(), $check_referer = false)
    {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        
        $url = W3_Request::get_string('redirect');
        
        if ($url == '') {
            if ($check_referer && !empty($_SERVER['HTTP_REFERER'])) {
                $url = $_SERVER['HTTP_REFERER'];
            } else {
                $url = 'admin.php';
                $params = array_merge(array(
                    'page' => $this->_page
                ), $params);
            }
        }
        
        w3_redirect($url, $params);
    }
    
    /**
     * Returns array of theme groups
     * 
     * @param string $theme_name
     * @return array
     */
    function get_theme_files($theme_name)
    {
        $patterns = array(
            '404', 
            'search', 
            'taxonomy(-.*)?', 
            'front-page', 
            'home', 
            'index', 
            '(image|video|text|audio|application).*', 
            'attachment', 
            'single(-.*)?', 
            'page(-.*)?', 
            'category(-.*)?', 
            'tag(-.*)?', 
            'author(-.*)?', 
            'date', 
            'archive', 
            'comments-popup', 
            'paged'
        );
        
        $templates = array();
        $theme = get_theme($theme_name);
        
        if ($theme && isset($theme['Template Files'])) {
            $template_files = (array) $theme['Template Files'];
            
            foreach ($template_files as $template_file) {
                /**
                 * Check file name
                 */
                $template = basename($template_file, '.php');
                
                foreach ($patterns as $pattern) {
                    $regexp = '~^' . $pattern . '$~';
                    
                    if (preg_match($regexp, $template)) {
                        $templates[] = $template_file;
                        continue 2;
                    }
                }
                
                /**
                 * Check get_header function call
                 */
                $template_content = @file_get_contents($template_file);
                
                if ($template_content && preg_match('~\s*get_header[0-9_]*\s*\(~', $template_content)) {
                    $templates[] = $template_file;
                }
            }
            
            sort($templates);
            reset($templates);
        }
        
        return $templates;
    }
    
    /**
     * Returns minify groups
     * 
     * @return array
     */
    function get_theme_templates($theme_name)
    {
        $groups = array(
            'default' => 'All Templates'
        );
        
        $templates = $this->get_theme_files($theme_name);
        
        foreach ($templates as $template) {
            $basename = basename($template, '.php');
            
            $groups[$basename] = ucfirst($basename);
        }
        
        return $groups;
    }
    
    /**
     * Returns array of detected URLs for theme templates
     * 
     * @param string  $theme_name
     * @return array
     */
    function get_theme_urls($theme_name)
    {
        $urls = array();
        $theme = get_theme($theme_name);
        
        if ($theme && isset($theme['Template Files'])) {
            $front_page_template = false;
            
            if (get_option('show_on_front') == 'page') {
                $front_page_id = get_option('page_on_front');
                
                if ($front_page_id) {
                    $front_page_template_file = get_post_meta($front_page_id, '_wp_page_template', true);
                    
                    if ($front_page_template_file) {
                        $front_page_template = basename($front_page_template_file, '.php');
                    }
                }
            }
            
            $home_url = w3_get_home_url();
            $template_files = (array) $theme['Template Files'];
            
            $mime_types = get_allowed_mime_types();
            $custom_mime_types = array();
            
            foreach ($mime_types as $mime_type) {
                list($type1, $type2) = explode('/', $mime_type);
                $custom_mime_types = array_merge($custom_mime_types, array(
                    $type1, 
                    $type2, 
                    $type1 . '_' . $type2
                ));
            }
            
            foreach ($template_files as $template_file) {
                $link = false;
                $template = basename($template_file, '.php');
                
                /**
                 * Check common templates
                 */
                switch (true) {
                    /**
                     * Handle home.php or index.php or front-page.php
                     */
                    case (!$front_page_template && $template == 'home'):
                    case (!$front_page_template && $template == 'index'):
                    case (!$front_page_template && $template == 'front-page'):
                    
                    /**
                     * Handle custom home page
                     */
                    case ($template == $front_page_template):
                        $link = $home_url . '/';
                        break;
                    
                    /**
                     * Handle 404.php
                     */
                    case ($template == '404'):
                        $link = sprintf('%s/%s/', $home_url, '404_test');
                        break;
                    
                    /**
                     * Handle search.php
                     */
                    case ($template == 'search'):
                        $link = sprintf('%s/?s=%s', $home_url, 'search_test');
                        break;
                    
                    /**
                     * Handle date.php or archive.php
                     */
                    case ($template == 'date'):
                    case ($template == 'archive'):
                        $posts = get_posts(array(
                            'numberposts' => 1, 
                            'orderby' => 'rand'
                        ));
                        if (is_array($posts) && count($posts)) {
                            $time = strtotime($posts[0]->post_date);
                            $link = get_day_link(date('Y', $time), date('m', $time), date('d', $time));
                        }
                        break;
                    
                    /**
                     * Handle author.php
                     */
                    case ($template == 'author'):
                        $author_ids = get_author_user_ids();
                        if (is_array($author_ids) && count($author_ids)) {
                            $link = get_author_posts_url($author_ids[0]);
                        }
                        break;
                    
                    /**
                     * Handle category.php
                     */
                    case ($template == 'category'):
                        $category_ids = get_all_category_ids();
                        if (is_array($category_ids) && count($category_ids)) {
                            $link = get_category_link($category_ids[0]);
                        }
                        break;
                    
                    /**
                     * Handle tag.php
                     */
                    case ($template == 'tag'):
                        $term_ids = get_terms('post_tag', 'fields=ids');
                        if (is_array($term_ids) && count($term_ids)) {
                            $link = get_term_link($term_ids[0], 'post_tag');
                        }
                        break;
                    
                    /**
                     * Handle taxonomy.php
                     */
                    case ($template == 'taxonomy'):
                        $taxonomy = '';
                        if (isset($GLOBALS['wp_taxonomies']) && is_array($GLOBALS['wp_taxonomies'])) {
                            foreach ($GLOBALS['wp_taxonomies'] as $wp_taxonomy) {
                                if (!in_array($wp_taxonomy->name, array(
                                    'category', 
                                    'post_tag', 
                                    'link_category'
                                ))) {
                                    $taxonomy = $wp_taxonomy->name;
                                    break;
                                }
                            }
                        }
                        if ($taxonomy) {
                            $terms = get_terms($taxonomy, array(
                                'number' => 1
                            ));
                            if (is_array($terms) && count($terms)) {
                                $link = get_term_link($terms[0], $taxonomy);
                            }
                        }
                        break;
                    
                    /**
                     * Handle attachment.php
                     */
                    case ($template == 'attachment'):
                        $attachments = get_posts(array(
                            'post_type' => 'attachment', 
                            'numberposts' => 1, 
                            'orderby' => 'rand'
                        ));
                        if (is_array($attachments) && count($attachments)) {
                            $link = get_attachment_link($attachments[0]->ID);
                        }
                        break;
                    
                    /**
                     * Handle single.php
                     */
                    case ($template == 'single'):
                        $posts = get_posts(array(
                            'numberposts' => 1, 
                            'orderby' => 'rand'
                        ));
                        if (is_array($posts) && count($posts)) {
                            $link = get_permalink($posts[0]->ID);
                        }
                        break;
                    
                    /**
                     * Handle page.php
                     */
                    case ($template == 'page'):
                        $pages_ids = get_all_page_ids();
                        if (is_array($pages_ids) && count($pages_ids)) {
                            $link = get_page_link($pages_ids[0]);
                        }
                        break;
                    
                    /**
                     * Handle comments-popup.php
                     */
                    case ($template == 'comments-popup'):
                        $posts = get_posts(array(
                            'numberposts' => 1, 
                            'orderby' => 'rand'
                        ));
                        if (is_array($posts) && count($posts)) {
                            $link = sprintf('%s/?comments_popup=%d', $home_url, $posts[0]->ID);
                        }
                        break;
                    
                    /**
                     * Handle paged.php
                     */
                    case ($template == 'paged'):
                        global $wp_rewrite;
                        if ($wp_rewrite->using_permalinks()) {
                            $link = sprintf('%s/page/%d/', $home_url, 1);
                        } else {
                            $link = sprintf('%s/?paged=%d', 1);
                        }
                        break;
                    
                    /**
                     * Handle author-id.php or author-nicename.php
                     */
                    case preg_match('~^author-(.+)$~', $template, $matches):
                        if (is_numeric($matches[1])) {
                            $link = get_author_posts_url($matches[1]);
                        } else {
                            $link = get_author_posts_url(null, $matches[1]);
                        }
                        break;
                    
                    /**
                     * Handle category-id.php or category-slug.php
                     */
                    case preg_match('~^category-(.+)$~', $template, $matches):
                        if (is_numeric($matches[1])) {
                            $link = get_category_link($matches[1]);
                        } else {
                            $term = get_term_by('slug', $matches[1], 'category');
                            if (is_object($term)) {
                                $link = get_category_link($term->term_id);
                            }
                        }
                        break;
                    
                    /**
                     * Handle tag-id.php or tag-slug.php
                     */
                    case preg_match('~^tag-(.+)$~', $template, $matches):
                        if (is_numeric($matches[1])) {
                            $link = get_tag_link($matches[1]);
                        } else {
                            $term = get_term_by('slug', $matches[1], 'post_tag');
                            if (is_object($term)) {
                                $link = get_tag_link($term->term_id);
                            }
                        }
                        break;
                    
                    /**
                     * Handle taxonomy-taxonomy-term.php
                     */
                    case preg_match('~^taxonomy-(.+)-(.+)$~', $template, $matches):
                        $link = get_term_link($matches[2], $matches[1]);
                        break;
                    
                    /**
                     * Handle taxonomy-taxonomy.php
                     */
                    case preg_match('~^taxonomy-(.+)$~', $template, $matches):
                        $terms = get_terms($matches[1], array(
                            'number' => 1
                        ));
                        if (is_array($terms) && count($terms)) {
                            $link = get_term_link($terms[0], $matches[1]);
                        }
                        break;
                    
                    /**
                     * Handle MIME_type.php
                     */
                    case in_array($template, $custom_mime_types):
                        $posts = get_posts(array(
                            'post_mime_type' => '%' . $template . '%', 
                            'post_type' => 'attachment', 
                            'numberposts' => 1, 
                            'orderby' => 'rand'
                        ));
                        if (is_array($posts) && count($posts)) {
                            $link = get_permalink($posts[0]->ID);
                        }
                        break;
                    
                    /**
                     * Handle single-posttype.php
                     */
                    case preg_match('~^single-(.+)$~', $template, $matches):
                        $posts = get_posts(array(
                            'post_type' => $matches[1], 
                            'numberposts' => 1, 
                            'orderby' => 'rand'
                        ));
                        
                        if (is_array($posts) && count($posts)) {
                            $link = get_permalink($posts[0]->ID);
                        }
                        break;
                    
                    /**
                     * Handle page-id.php or page-slug.php
                     */
                    case preg_match('~^page-(.+)$~', $template, $matches):
                        if (is_numeric($matches[1])) {
                            $link = get_permalink($matches[1]);
                        } else {
                            $posts = get_posts(array(
                                'pagename' => $matches[1], 
                                'post_type' => 'page', 
                                'numberposts' => 1
                            ));
                            
                            if (is_array($posts) && count($posts)) {
                                $link = get_permalink($posts[0]->ID);
                            }
                        }
                        break;
                    
                    /**
                     * Try to handle custom template
                     */
                    default:
                        $posts = get_posts(array(
                            'pagename' => $template, 
                            'post_type' => 'page', 
                            'numberposts' => 1
                        ));
                        
                        if (is_array($posts) && count($posts)) {
                            $link = get_permalink($posts[0]->ID);
                        }
                        break;
                }
                
                if ($link && !is_wp_error($link)) {
                    $urls[$template] = $link;
                }
            }
        }
        
        return $urls;
    }
    
    /**
     * Returns theme recommendations
     * 
     * @param string $theme_name
     * @return array
     */
    function get_theme_recommendations($theme_name)
    {
        $urls = $this->get_theme_urls($theme_name);
        
        @set_time_limit(600);
        
        $js_groups = array();
        $css_groups = array();
        
        foreach ($urls as $template => $url) {
            /**
             * Append theme identifier
             */
            $url .= (strstr($url, '?') !== false ? '&' : '?') . 'w3tc_theme=' . urlencode($theme_name);
            
            /**
             * If preview mode enabled append w3tc_preview
             */
            if (w3_is_preview_config()) {
                $url .= '&w3tc_preview=1';
            }
            
            /**
             * Get page contents
             * Don't check response code for 404 template
             */
            $content = w3_http_get($url, null, ($template != '404'));
            
            if ($content) {
                $js_files = $this->get_recommendations_js($content);
                $css_files = $this->get_recommendations_css($content);
                
                $js_groups[$template] = $js_files;
                $css_groups[$template] = $css_files;
            }
        }
        
        $js_groups = $this->_get_theme_recommendations($js_groups);
        $css_groups = $this->_get_theme_recommendations($css_groups);
        
        $recommendations = array(
            $js_groups, 
            $css_groups
        );
        
        return $recommendations;
    }
    
    /**
     * Find common files and place them into default group
     * 
     * @param array $groups
     * @return array
     */
    function _get_theme_recommendations($groups)
    {
        /**
         * Replace CDN hosts to local host
         */
        if ($this->_config->get_boolean('cdn.enabled')) {
            require_once W3TC_DIR . '/lib/W3/Plugin/Cdn.php';
            
            $w3_plugin_cdn = & W3_Plugin_Cdn::instance();
            $cdn = & $w3_plugin_cdn->get_cdn();
            
            $domains = $cdn->get_domains();
            $domain = w3_get_domain(w3_get_host());
            
            foreach ($groups as $template => $files) {
                foreach ($files as $index => $file) {
                    $groups[$template][$index] = str_replace($domains, $domain, $file);
                }
            }
        }
        
        /**
         * First calculate file usage count
         */
        $all_files = array();
        
        foreach ($groups as $template => $files) {
            foreach ($files as $file) {
                if (!isset($all_files[$file])) {
                    $all_files[$file] = 0;
                }
                
                $all_files[$file]++;
            }
        }
        
        /**
         * Determine default group files
         */
        $default_files = array();
        $count = count($groups);
        
        foreach ($all_files as $all_file => $all_file_count) {
            /**
             * If file usage count == groups count then file is common
             */
            if ($count == $all_file_count) {
                $default_files[] = $all_file;
                
                /**
                 * If common file found unset it from all groups
                 */
                foreach ($groups as $template => $files) {
                    foreach ($files as $index => $file) {
                        if ($file == $all_file) {
                            array_splice($groups[$template], $index, 1);
                            if (!count($groups[$template])) {
                                unset($groups[$template]);
                            }
                            break;
                        }
                    }
                }
            }
        }
        
        /**
         * If there are common files append add them into default group
         */
        if (count($default_files)) {
            $new_groups = array();
            $new_groups['default'] = $default_files;
            
            foreach ($groups as $template => $files) {
                $new_groups[$template] = $files;
            }
            
            $groups = $new_groups;
        }
        
        /**
         * Unset empty templates
         */
        foreach ($groups as $template => $files) {
            if (!count($files)) {
                unset($groups[$template]);
            }
        }
        
        return $groups;
    }
    
    /**
     * Parse content and return JS recommendations
     * 
     * @param string $content
     * @return array
     */
    function get_recommendations_js($content)
    {
        $matches = null;
        $files = array();
        
        if (preg_match_all('~<script\s+[^<>]*src=["\']?([^"\']+)["\']?[^<>]*>\s*</script>~is', $content, $matches)) {
            $files = $matches[1];
        }
        
        $files = array_filter($files, create_function('$el', 'return (strstr($el, W3TC_CONTENT_MINIFY_DIR_NAME) ? false : true);'));
        $files = array_map('w3_normalize_file_minify', $files);
        $files = array_unique($files);
        
        return $files;
    }
    
    /**
     * Parse content and return CSS recommendations
     * 
     * @param string $content
     * @return array
     */
    function get_recommendations_css($content)
    {
        $matches = null;
        $files = array();
        
        $content = preg_replace('~<!--\[if.*\]-->~sU', '', $content);
        
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
        
        $files = array_filter($files, create_function('$el', 'return (strstr($el, W3TC_CONTENT_MINIFY_DIR_NAME) ? false : true);'));
        $files = array_map('w3_normalize_file_minify', $files);
        $files = array_unique($files);
        
        return $files;
    }
    
    /**
     * Self test action
     */
    function self_test()
    {
        include W3TC_DIR . '/inc/lightbox/self_test.phtml';
    }
    
    /**
     * Returns themes array
     * 
     * @return array
     */
    function get_themes()
    {
        $themes = array();
        $wp_themes = get_themes();
        $theme_root = get_theme_root();
        
        foreach ($wp_themes as $wp_theme) {
            $theme_key = substr(md5($theme_root . $wp_theme['Template'] . $wp_theme['Stylesheet']), 0, 6);
            $themes[$theme_key] = $wp_theme['Name'];
        }
        
        return $themes;
    }
    
    /**
     * Preview link replace callback
     * 
     * @param array $matches
     * @return string
     */
    function link_replace_callback($matches)
    {
        list($match, $attr, $quote, $domain_url, $www, $path) = $matches;
        
        $path .= (strstr($path, '?') !== false ? '&' : '?') . 'w3tc_preview=1';
        
        return sprintf('%s=%s%s%s', $attr, $quote, $domain_url, $path);
    }
    
    /**
     * Parses FAQ XML file into array
     * 
     * @return array
     */
    function parse_faq()
    {
        $faq = array();
        $file = W3TC_DIR . '/inc/options/faq.xml';
        
        $xml = @file_get_contents($file);
        
        if ($xml) {
            if (function_exists('xml_parser_create')) {
                $parser = @xml_parser_create('UTF-8');
                
                xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
                xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
                xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
                $values = null;
                
                $result = xml_parse_into_struct($parser, $xml, $values);
                xml_parser_free($parser);
                
                if ($result) {
                    $index = 0;
                    $current_section = '';
                    $current_entry = array();
                    
                    foreach ($values as $value) {
                        switch ($value['type']) {
                            case 'open':
                                if ($value['tag'] === 'section') {
                                    $current_section = $value['attributes']['name'];
                                }
                                break;
                            
                            case 'complete':
                                switch ($value['tag']) {
                                    case 'question':
                                        $current_entry['question'] = $value['value'];
                                        break;
                                    
                                    case 'answer':
                                        $current_entry['answer'] = $value['value'];
                                        break;
                                }
                                break;
                            
                            case 'close':
                                if ($value['tag'] == 'entry') {
                                    $current_entry['index'] = ++$index;
                                    $faq[$current_section][] = $current_entry;
                                }
                                break;
                        }
                    }
                }
            }
        }
        
        return $faq;
    }
}
