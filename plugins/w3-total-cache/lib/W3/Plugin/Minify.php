<?php

/**
 * W3 Minify plugin
 */
require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_Minify
 */
class W3_Plugin_Minify extends W3_Plugin
{
    /**
     * Minify reject reason
     *
     * @var string
     */
    var $minify_reject_reason = '';
    
    /**
     * Error
     * @var string
     */
    var $error = '';
    
    /**
     * Array of printed styles
     * @var array
     */
    var $printed_styles = array();
    
    /**
     * Array of printed scripts
     * @var array
     */
    var $printed_scripts = array();
    
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
        
        if ($this->_config->get_boolean('minify.enabled') && $this->_config->get_string('minify.engine') == 'file') {
            add_action('w3_minify_cleanup', array(
                &$this, 
                'cleanup'
            ));
        }
        
        if ($this->can_minify()) {
            ob_start(array(
                &$this, 
                'ob_callback'
            ));
        }
    }
    
    /**
     * Returns instance
     *
     * @return W3_Plugin_Minify
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
        if (!@is_dir(W3TC_CONTENT_MINIFY_DIR)) {
            if (@mkdir(W3TC_CONTENT_MINIFY_DIR, 0755)) {
                @chmod(W3TC_CONTENT_MINIFY_DIR, 0755);
            } else {
                w3_writable_error(W3TC_CONTENT_MINIFY_DIR);
            }
        }
        
        $file_index = W3TC_CONTENT_MINIFY_DIR . '/index.php';
        
        if (@copy(W3TC_INSTALL_MINIFY_DIR . '/index.php', $file_index)) {
            @chmod($file_index, 0644);
        } else {
            w3_writable_error($file_index);
        }
        
        if ($this->_config->get_boolean('minify.rewrite')) {
            $this->write_rules();
        }
        
        $this->schedule();
    }
    
    /**
     * Deactivate plugin action
     */
    function deactivate()
    {
        $this->unschedule();
        
        $this->remove_rules();
        
        @unlink(W3TC_CONTENT_MINIFY_DIR . '/index.php');
    }
    
    /**
     * Schedules events
     */
    function schedule()
    {
        if ($this->_config->get_boolean('minify.enabled') && $this->_config->get_string('minify.engine') == 'file') {
            if (!wp_next_scheduled('w3_minify_cleanup')) {
                wp_schedule_event(time(), 'w3_minify_cleanup', 'w3_minify_cleanup');
            }
        } else {
            $this->unschedule();
        }
    }
    
    /**
     * Unschedules events
     */
    function unschedule()
    {
        if (wp_next_scheduled('w3_minify_cleanup')) {
            wp_clear_scheduled_hook('w3_minify_cleanup');
        }
    }
    
    /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup()
    {
        require_once W3TC_LIB_W3_DIR . '/Cache/File/Minify/Manager.php';
        
        $w3_cache_file_minify_manager = & new W3_Cache_File_Minify_Manager(array(
            'cache_dir' => W3TC_CACHE_FILE_MINIFY_DIR, 
            'expire' => $this->_config->get_integer('minify.file.gc')
        ));
        
        $w3_cache_file_minify_manager->clean();
    }
    
    /**
     * Cron schedules filter
     *
     * @paran array $schedules
     * @return array
     */
    function cron_schedules($schedules)
    {
        $gc = $this->_config->get_integer('minify.file.gc');
        
        return array_merge($schedules, array(
            'w3_minify_cleanup' => array(
                'interval' => $gc, 
                'display' => sprintf('[W3TC] Minify file GC (every %d seconds)', $gc)
            )
        ));
    }
    
    /**
     * OB callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer)
    {
        if ($buffer != '' && w3_is_xml($buffer)) {
            if ($this->can_minify2($buffer)) {
                $head_prepend = '';
                $body_prepend = '';
                $body_append = '';
                
                if ($this->_config->get_boolean('minify.css.enable') && !in_array('include', $this->printed_styles)) {
                    $head_prepend .= $this->get_styles('include');
                }
                
                if ($this->_config->get_boolean('minify.js.enable')) {
                    if (!in_array('include', $this->printed_scripts)) {
                        $head_prepend .= $this->get_scripts('include');
                    }
                    
                    if (!in_array('include-nb', $this->printed_scripts)) {
                        $head_prepend .= $this->get_scripts('include-nb');
                    }
                    
                    if (!in_array('include-body', $this->printed_scripts)) {
                        $body_prepend .= $this->get_scripts('include-body');
                    }
                    
                    if (!in_array('include-body-nb', $this->printed_scripts)) {
                        $body_prepend .= $this->get_scripts('include-body-nb');
                    }
                    
                    if (!in_array('include-footer', $this->printed_scripts)) {
                        $body_append .= $this->get_scripts('include-footer');
                    }
                    
                    if (!in_array('include-footer-nb', $this->printed_scripts)) {
                        $body_append .= $this->get_scripts('include-footer-nb');
                    }
                }
                
                if ($head_prepend != '') {
                    $buffer = preg_replace('~<head(\s+[^<>]+)*>~Ui', '\\0' . $head_prepend, $buffer, 1);
                }
                
                if ($body_prepend != '') {
                    $buffer = preg_replace('~<body(\s+[^<>]+)*>~Ui', '\\0' . $body_prepend, $buffer, 1);
                }
                
                if ($body_append != '') {
                    $buffer = preg_replace('~<\\/body>~', $body_append . '\\0', $buffer, 1);
                }
                
                $this->clean($buffer);
            }
            
            if ($this->_config->get_boolean('minify.debug')) {
                $buffer .= "\r\n\r\n" . $this->get_debug_info();
            }
        }
        
        return $buffer;
    }
    
    /**
     * Cleans content
     *
     * @param string $content
     * @return string
     */
    function clean(&$content)
    {
        if (function_exists('is_feed') && !is_feed()) {
            if ($this->_config->get_boolean('minify.css.enable')) {
                $this->clean_styles($content);
            }
            
            if ($this->_config->get_boolean('minify.js.enable')) {
                $this->clean_scripts($content);
            }
        }
        
        if ($this->_config->get_boolean('minify.html.enable')) {
            try {
                $this->minify_html($content);
            } catch (Exception $exception) {
                $this->error = $exception->getMessage();
            }
        }
    }
    
    /**
     * Cleans styles
     *
     * @param string $content
     * @return string
     */
    function clean_styles(&$content)
    {
        $theme = $this->get_theme();
        $template = $this->get_template();
        
        $groups = $this->_config->get_array('minify.css.groups');
        
        $locations = array();
        
        if (isset($groups[$theme]['default'])) {
            $locations = (array) $groups[$theme]['default'];
        }
        
        if ($template != 'default' && isset($groups[$theme][$template])) {
            $locations = array_merge_recursive($locations, (array) $groups[$theme][$template]);
        }
        
        $regexps = array();
        $domain_url_regexp = w3_get_domain_url_regexp();
        
        foreach ($locations as $location => $config) {
            if (!empty($config['files'])) {
                foreach ((array) $config['files'] as $file) {
                    if (w3_is_url($file) && !preg_match('~' . $domain_url_regexp . '~i', $file)) {
                        // external CSS files
                        $regexps[] = w3_preg_quote($file);
                    } else {
                        // local CSS files
                        $file = ltrim(preg_replace('~' . $domain_url_regexp . '~i', '', $file), '/\\');
                        $regexps[] = '(' . $domain_url_regexp . ')?/?' . w3_preg_quote($file);
                    }
                }
            }
        }
        
        foreach ($regexps as $regexp) {
            $content = preg_replace('~<link\s+[^<>]*href=["\']?' . $regexp . '["\']?[^<>]*/?>(.*</link>)?~Uis', '', $content);
            $content = preg_replace('~@import\s+(url\s*)?\(?["\']?\s*' . $regexp . '\s*["\']?\)?[^;]*;?~is', '', $content);
        }
        
        $content = preg_replace('~<style[^<>]*>\s*</style>~', '', $content);
    }
    
    /**
     * Cleans scripts
     *
     * @param string $content
     * @return string
     */
    function clean_scripts(&$content)
    {
        $theme = $this->get_theme();
        $template = $this->get_template();
        
        $groups = $this->_config->get_array('minify.js.groups');
        
        $locations = array();
        
        if (isset($groups[$theme]['default'])) {
            $locations = (array) $groups[$theme]['default'];
        }
        
        if ($template != 'default' && isset($groups[$theme][$template])) {
            $locations = array_merge_recursive($locations, (array) $groups[$theme][$template]);
        }
        
        $regexps = array();
        $domain_url_regexp = w3_get_domain_url_regexp();
        
        foreach ($locations as $location => $config) {
            if (!empty($config['files'])) {
                foreach ((array) $config['files'] as $file) {
                    if (w3_is_url($file) && !preg_match('~' . $domain_url_regexp . '~i', $file)) {
                        // external JS files
                        $regexps[] = w3_preg_quote($file);
                    } else {
                        // local JS files
                        $file = ltrim(preg_replace('~' . $domain_url_regexp . '~i', '', $file), '/\\');
                        $regexps[] = '(' . $domain_url_regexp . ')?/?' . w3_preg_quote($file);
                    }
                }
            }
        }
        
        foreach ($regexps as $regexp) {
            $content = preg_replace('~<script\s+[^<>]*src=["\']?' . $regexp . '["\']?[^<>]*>\s*</script>~is', '', $content);
        }
    }
    
    /**
     * Minifies HTML
     *
     * @param string $content
     * @return string
     */
    function minify_html(&$content)
    {
        require_once W3TC_LIB_MINIFY_DIR . '/Minify/HTML.php';
        require_once W3TC_LIB_MINIFY_DIR . '/Minify/CSS.php';
        require_once W3TC_LIB_MINIFY_DIR . '/JSMin.php';
        
        $options = array(
            'xhtml' => true, 
            'stripCrlf' => $this->_config->get_boolean('minify.html.strip.crlf'), 
            'cssStripCrlf' => $this->_config->get_boolean('minify.css.strip.crlf'), 
            'cssStripComments' => $this->_config->get_boolean('minify.css.strip.comments'), 
            'jsStripCrlf' => $this->_config->get_boolean('minify.js.strip.crlf'), 
            'jsStripComments' => $this->_config->get_boolean('minify.js.strip.comments'), 
            'ignoredComments' => $this->_config->get_array('minify.html.comments.ignore')
        );
        
        if ($this->_config->get_boolean('minify.html.inline.css')) {
            $options['cssMinifier'] = array(
                'Minify_CSS', 
                'minify'
            );
        }
        
        if ($this->_config->get_boolean('minify.html.inline.js')) {
            $options['jsMinifier'] = array(
                'JSMin', 
                'minify'
            );
        }
        
        $content = Minify_HTML::minify($content, $options);
    }
    
    /**
     * Returns current theme
     */
    function get_theme()
    {
        static $theme = null;
        
        if ($theme === null) {
            $theme = substr(md5(get_theme_root() . get_template() . get_stylesheet()), 0, 6);
        }
        
        return $theme;
    }
    
    /**
     * Returns current template
     *
     * @return string
     */
    function get_template()
    {
        static $template = null;
        
        if ($template === null) {
            switch (true) {
                case (is_404() && ($template_file = get_404_template())):
                case (is_search() && ($template_file = get_search_template())):
                case (is_tax() && ($template_file = get_taxonomy_template())):
                case (is_front_page() && function_exists('get_front_page_template') && $template = get_front_page_template()):
                case (is_home() && ($template_file = get_home_template())):
                case (is_attachment() && ($template_file = get_attachment_template())):
                case (is_single() && ($template_file = get_single_template())):
                case (is_page() && ($template_file = get_page_template())):
                case (is_category() && ($template_file = get_category_template())):
                case (is_tag() && ($template_file = get_tag_template())):
                case (is_author() && ($template_file = get_author_template())):
                case (is_date() && ($template_file = get_date_template())):
                case (is_archive() && ($template_file = get_archive_template())):
                case (is_comments_popup() && ($template_file = get_comments_popup_template())):
                case (is_paged() && ($template_file = get_paged_template())):
                    break;
                
                default:
                    if (function_exists('get_index_template')) {
                        $template_file = get_index_template();
                    } else {
                        $template_file = 'index.php';
                    }
                    break;
            }
            
            $template = basename($template_file, '.php');
        }
        
        return $template;
    }
    
    /**
     * Returns style link
     *
     * @param string $url
     * @param string $import
     */
    function get_style($url, $import = false)
    {
        if ($import) {
            return "<style type=\"text/css\" media=\"all\">@import url(\"" . $url . "\");</style>\r\n";
        } else {
            return "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . str_replace('&', '&amp;', $url) . "\" media=\"all\" />\r\n";
        }
    }
    
    /**
     * Prints script link
     *
     * @param string $url
     * @param boolean $non_blocking
     */
    function get_script($url, $blocking = true)
    {
        static $non_blocking_function = false;
        
        if ($blocking) {
            return '<script type="text/javascript" src="' . str_replace('&', '&amp;', $url) . '"></script>';
        } else {
            $script = '';
            
            if (!$non_blocking_function) {
                $non_blocking_function = true;
                $script = "<script type=\"text/javascript\">function w3tc_load_js(u){var d=document,p=d.getElementsByTagName('HEAD')[0],c=d.createElement('script');c.type='text/javascript';c.src=u;p.appendChild(c);}</script>";
            }
            
            $script .= "<script type=\"text/javascript\">w3tc_load_js('" . $url . "');</script>";
            
            return $script;
        }
    }
    
    /**
     * Returns style link for styles group
     *
     * @param string $location
     * @param string $template
     * @param string $theme
     * @return array
     */
    function get_styles($location, $template = null, $theme = null)
    {
        $styles = '';
        $groups = $this->_config->get_array('minify.css.groups');
        
        if (!$theme) {
            $theme = $this->get_theme();
        }
        
        if (!$template) {
            $template = $this->get_template();
        }
        
        if ($template != 'default' && empty($groups[$theme][$template][$location]['files'])) {
            $template = 'default';
        }
        
        if (!empty($groups[$theme][$template][$location]['files'])) {
            $url = $this->format_url($theme, $template, $location, 'css');
            $import = (isset($groups[$theme][$template]['import']) ? (boolean) $groups[$theme][$template]['import'] : false);
            
            $styles .= $this->get_style($url, $import);
        }
        
        return $styles;
    }
    
    /**
     * Returns script linkg for scripts group
     *
     * @param string $location
     * @param string $template
     * @param string $theme
     * @return array
     */
    function get_scripts($location, $template = null, $theme = null)
    {
        $scripts = '';
        $groups = $this->_config->get_array('minify.js.groups');
        
        if (!$theme) {
            $theme = $this->get_theme();
        }
        
        if (!$template) {
            $template = $this->get_template();
        }
        
        if ($template != 'default' && empty($groups[$theme][$template][$location]['files'])) {
            $template = 'default';
        }
        
        if (!empty($groups[$theme][$template][$location]['files'])) {
            $url = $this->format_url($theme, $template, $location, 'js');
            $blocking = (isset($groups[$theme][$template]['blocking']) ? (boolean) $groups[$theme][$template]['blocking'] : true);
            
            $scripts .= $this->get_script($url, $blocking);
        }
        
        return $scripts;
    }
    
    /**
     * Formats URL
     *
     * @param string $theme
     * @param string $template
     * @param string $location
     * @param string $type
     * @return string
     */
    function format_url($theme, $template, $location, $type)
    {
        $site_url_ssl = w3_get_site_url_ssl();
        
        if ($this->_config->get_boolean('minify.rewrite')) {
            require_once W3TC_LIB_W3_DIR . '/Minify.php';
            $w3_minify = & W3_Minify::instance();
            
            $id = $w3_minify->get_id($theme, $template, $location, $type);
            
            return sprintf('%s/%s/%s/%s.%s.%s.%s', $site_url_ssl, W3TC_CONTENT_MINIFY_DIR_NAME, $theme, $template, $location, $id, $type);
        }
        
        return sprintf('%s/%s/index.php?tt=%s&gg=%s&g=%s&t=%s', $site_url_ssl, W3TC_CONTENT_MINIFY_DIR_NAME, $theme, $template, $location, $type);
    }
    
    /**
     * Returns array of minify URLs
     *
     * @return array
     */
    function get_urls()
    {
        $files = array();
        
        $js_groups = $this->_config->get_array('minify.js.groups');
        $css_groups = $this->_config->get_array('minify.css.groups');
        
        foreach ($js_groups as $js_theme => $js_templates) {
            foreach ($js_templates as $js_template => $js_locations) {
                foreach ((array) $js_locations as $js_location => $js_config) {
                    if (!empty($js_config['files'])) {
                        $files[] = $this->format_url($js_theme, $js_template, $js_location, 'js');
                    }
                }
            }
        }
        
        foreach ($css_groups as $css_theme => $css_templates) {
            foreach ($css_templates as $css_template => $css_locations) {
                foreach ((array) $css_locations as $css_location => $css_config) {
                    if (!empty($css_config['files'])) {
                        $files[] = $this->format_url($css_theme, $css_template, $css_location, 'css');
                    }
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Returns debug info
     */
    function get_debug_info()
    {
        $theme = $this->get_theme();
        $template = $this->get_template();
        
        $debug_info = "<!-- W3 Total Cache: Minify debug info:\r\n";
        $debug_info .= sprintf("%s%s\r\n", str_pad('Engine: ', 20), w3_get_engine_name($this->_config->get_string('minify.engine')));
        $debug_info .= sprintf("%s%s\r\n", str_pad('Theme: ', 20), $theme);
        $debug_info .= sprintf("%s%s\r\n", str_pad('Template: ', 20), $template);
        
        if ($this->minify_reject_reason) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Reject reason: ', 20), $this->minify_reject_reason);
        }
        
        if ($this->error) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Errors: ', 20), $this->error);
        }
        
        $document_root = w3_get_document_root();
        
        require_once W3TC_LIB_W3_DIR . '/Minify.php';
        $w3_minify = & W3_Minify::instance();
        
        $js_groups = $w3_minify->get_groups($theme, $template, 'js');
        
        if (count($js_groups)) {
            $debug_info .= "JavaScript info:\r\n";
            $debug_info .= sprintf("%s | %s | % s | %s\r\n", str_pad('Location', 15, ' ', STR_PAD_BOTH), str_pad('Last modified', 19, ' ', STR_PAD_BOTH), str_pad('Size', 12, ' ', STR_PAD_LEFT), 'Path');
            
            foreach ($js_groups as $js_group => $js_files) {
                foreach ($js_files as $js_file => $js_file_path) {
                    if (is_a($js_file_path, 'Minify_Source')) {
                        $js_file_path = $js_file_path->filepath;
                        $js_file_info = sprintf('%s (%s)', $js_file, $js_file_path);
                    } else {
                        $js_file_path = $js_file_info = $document_root . '/' . $js_file;
                    }
                    
                    $debug_info .= sprintf("%s | %s | % s | %s\r\n", str_pad($js_group, 15, ' ', STR_PAD_BOTH), str_pad(date('Y-m-d H:i:s', filemtime($js_file_path)), 19, ' ', STR_PAD_BOTH), str_pad(filesize($js_file_path), 12, ' ', STR_PAD_LEFT), $js_file_info);
                }
            }
        }
        
        $css_groups = $w3_minify->get_groups($theme, $template, 'css');
        
        if (count($css_groups)) {
            $debug_info .= "Stylesheet info:\r\n";
            $debug_info .= sprintf("%s | %s | % s | %s\r\n", str_pad('Location', 15, ' ', STR_PAD_BOTH), str_pad('Last modified', 19, ' ', STR_PAD_BOTH), str_pad('Size', 12, ' ', STR_PAD_LEFT), 'Path');
            
            foreach ($css_groups as $css_group => $css_files) {
                foreach ($css_files as $css_file => $css_file_path) {
                    if (is_a($css_file_path, 'Minify_Source')) {
                        $css_file_path = $css_file_path->filepath;
                        $css_file_info = sprintf('%s (%s)', $css_file, $css_file_path);
                    } else {
                        $css_file_path = $css_file_info = $document_root . '/' . $css_file;
                    }
                    
                    $debug_info .= sprintf("%s | %s | % s | %s\r\n", str_pad($css_group, 15, ' ', STR_PAD_BOTH), str_pad(date('Y-m-d H:i:s', filemtime($css_file_path)), 19, ' ', STR_PAD_BOTH), str_pad(filesize($css_file_path), 12, ' ', STR_PAD_LEFT), $css_file_info);
                }
            }
        }
        
        $debug_info .= '-->';
        
        return $debug_info;
    }
    
    /**
     * Check if we can do minify logic
     *
     * @return boolean
     */
    function can_minify()
    {
        /**
         * Skip if Minify is disabled
         */
        if (!$this->_config->get_boolean('minify.enabled')) {
            $this->minify_reject_reason = 'minify is disabled';
            
            return false;
        }
        
        /**
         * Skip if doint AJAX
         */
        if (defined('DOING_AJAX')) {
            $this->minify_reject_reason = 'doing AJAX';
            
            return false;
        }
        
        /**
         * Skip if doing cron
         */
        if (defined('DOING_CRON')) {
            $this->minify_reject_reason = 'doing cron';
            
            return false;
        }
        
        /**
         * Skip if APP request
         */
        if (defined('APP_REQUEST')) {
            $this->minify_reject_reason = 'application request';
            
            return false;
        }
        
        /**
         * Skip if XMLRPC request
         */
        if (defined('XMLRPC_REQUEST')) {
            $this->minify_reject_reason = 'XMLRPC request';
            
            return false;
        }
        
        /**
         * Skip if Admin
         */
        if (defined('WP_ADMIN')) {
            $this->minify_reject_reason = 'wp-admin';
            
            return false;
        }
        
        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            $this->minify_reject_reason = 'Short init';
            
            return false;
        }
        
        /**
         * Check User agent
         */
        if (!$this->check_ua()) {
            $this->minify_reject_reason = 'user agent is rejected';
            
            return false;
        }
        
        /**
         * Check request URI
         */
        if (!$this->check_request_uri()) {
            $this->minify_reject_reason = 'request URI is rejected';
            
            return false;
        }
        
        /**
         * Check feed
         */
        if ($this->_config->get_boolean('minify.html.reject.feed') && function_exists('is_feed') && is_feed()) {
            $this->minify_reject_reason = 'feed is rejected';
            
            return false;
        }
        
        return true;
    }
    
    /**    
     * Returns true if we can minify
     * 
     * @return string
     * @return boolean
     */
    function can_minify2(&$buffer)
    {
        /**
         * Check for database error
         */
        if (w3_is_database_error($buffer)) {
            $this->minify_reject_reason = 'Database Error occurred';
            
            return false;
        }
        
        /**
         * Check for DONOTMINIFY constant
         */
        if (defined('DONOTMINIFY') && DONOTMINIFY) {
            $this->minify_reject_reason = 'DONOTMINIFY constant is defined';
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Checks User Agent
     *
     * @return boolean
     */
    function check_ua()
    {
        foreach ($this->_config->get_array('minify.reject.ua') as $ua) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], $ua) !== false) {
                return false;
            }
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
        $auto_reject_uri = array(
            'wp-login', 
            'wp-register'
        );
        
        foreach ($auto_reject_uri as $uri) {
            if (strstr($_SERVER['REQUEST_URI'], $uri) !== false) {
                return false;
            }
        }
        
        foreach ($this->_config->get_array('minify.reject.uri') as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $_SERVER['REQUEST_URI'])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Generates rules
     *
     * @return string
     */
    function generate_rules()
    {
        $engine = $this->_config->get_string('minify.engine');
        $browsercache = $this->_config->get_integer('browsercache.enabled');
        $compression = $this->_config->get_boolean('browsercache.cssjs.compression');
        $expires = $this->_config->get_boolean('browsercache.cssjs.expires');
        $lifetime = $this->_config->get_integer('browsercache.cssjs.lifetime');
        
        $rules = '';
        $rules .= "# BEGIN W3TC Minify\n";
        
        if ($engine == 'file') {
            if ($browsercache && $expires && $lifetime) {
                $rules .= "<IfModule mod_expires.c>\n";
                $rules .= "    ExpiresActive On\n";
                $rules .= "    ExpiresByType text/css M" . $lifetime . "\n";
                $rules .= "    ExpiresByType application/x-javascript M" . $lifetime . "\n";
                $rules .= "</IfModule>\n";
            }
            
            if ($compression) {
                $rules .= "<IfModule mod_mime.c>\n";
                $rules .= "    AddEncoding gzip .gzip\n";
                $rules .= "    <Files *.css.gzip>\n";
                $rules .= "        ForceType text/css\n";
                $rules .= "    </Files>\n";
                $rules .= "    <Files *.js.gzip>\n";
                $rules .= "        ForceType application/x-javascript\n";
                $rules .= "    </Files>\n";
                $rules .= "</IfModule>\n";
                $rules .= "<IfModule mod_deflate.c>\n";
                $rules .= "    <IfModule mod_setenvif.c>\n";
                $rules .= "        SetEnvIfNoCase Request_URI \\.gzip$ no-gzip\n";
                $rules .= "    </IfModule>\n";
                $rules .= "</IfModule>\n";
            } else {
                $rules .= "<IfModule mod_deflate.c>\n";
                $rules .= "    <IfModule mod_env.c>\n";
                $rules .= "        SetEnv no-gzip\n";
                $rules .= "    </IfModule>\n";
                $rules .= "</IfModule>\n";
            }
            
            $rules .= "<IfModule mod_headers.c>\n";
            
            if ($browsercache && $this->_config->get_integer('browsercache.cssjs.w3tc')) {
                $rules .= "    Header set X-Powered-By \"" . W3TC_POWERED_BY . "\"\n";
            }
            
            if ($browsercache && $compression) {
                $rules .= "    Header set Vary \"Accept-Encoding\"\n";
            }
            
            if ($this->_config->get_boolean('browsercache.cssjs.cache.control')) {
                switch ($this->_config->get_string('browsercache.cssjs.cache.policy')) {
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
        }
        
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "    RewriteEngine On\n";
        $rules .= "    RewriteBase " . str_replace(w3_get_document_root(), '', w3_path(W3TC_CACHE_FILE_MINIFY_DIR)) . "/\n";
        
        if ($engine == 'file') {
            if ($browsercache && $compression) {
                $rules .= "    RewriteCond %{HTTP:Accept-Encoding} gzip\n";
                $rules .= "    RewriteRule .* - [E=APPEND_EXT:.gzip]\n";
            }
            
            $rules .= "    RewriteCond %{REQUEST_FILENAME}%{ENV:APPEND_EXT} -f\n";
            $rules .= "    RewriteRule (.*) $1%{ENV:APPEND_EXT} [L]\n";
        }
        
        $rules .= "    RewriteRule ^([a-f0-9]+)\\/(.+)\\.(include(\\-(footer|body))?(-nb)?)\\.[0-9]+\\.(css|js)$ index.php?tt=$1&gg=$2&g=$3&t=$7 [L]\n";
        $rules .= "</IfModule>\n";
        $rules .= "# END W3TC Minify\n";
        
        return $rules;
    }
    
    /**
     * Writes rules to file cache .htaccess
     *
     * @return boolean
     */
    function write_rules()
    {
        $path = W3TC_CONTENT_MINIFY_DIR . '/.htaccess';
        
        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules($data);
            } else {
                return false;
            }
        } else {
            $data = '';
        }
        
        $data = trim($this->generate_rules() . $data);
        
        return @file_put_contents($path, $data);
    }
    
    /**
     * Erases W3TC rules from config
     *
     * @param string $data
     * @return string
     */
    function erase_rules($data)
    {
        $data = w3_erase_text($data, '# BEGIN W3TC Minify', '# END W3TC Minify');
        
        return $data;
    }
    
    /**
     * Removes W3TC rules from file cache dir
     *
     * @return boolean
     */
    function remove_rules()
    {
        $path = W3TC_CONTENT_MINIFY_DIR . '/.htaccess';
        
        return @unlink($path);
    }
    
    /**
     * Checks rules
     *
     * @return boolean
     */
    function check_rules()
    {
        $path = W3TC_CACHE_FILE_MINIFY_DIR . '/.htaccess';
        $search = $this->generate_rules();
        
        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }
}

/**
 * Prints script link for scripts group
 *
 * @param string $location
 * @param string $group
 */
function w3tc_scripts($location, $group = null)
{
    $w3_plugin_minify = & W3_Plugin_Minify::instance();
    $w3_plugin_minify->printed_scripts[] = $location;
    
    echo $w3_plugin_minify->get_scripts($location, $group);
}

/**
 * Prints style link for styles group
 *
 * @param string $location
 * @param string $group
 */
function w3tc_styles($location, $group = null)
{
    $w3_plugin_minify = & W3_Plugin_Minify::instance();
    $w3_plugin_minify->printed_styles[] = $location;
    
    echo $w3_plugin_minify->get_styles($location, $group);
}
