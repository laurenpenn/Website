<?php

/**
 * W3 Minify plugin
 */
require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_Minify
 */
class W3_Plugin_Minify extends W3_Plugin {
    /**
     * Minify reject reason
     *
     * @var string
     */
    var $minify_reject_reason = '';

    /**
     * Error
     *
     * @var string
     */
    var $error = '';

    /**
     * Array of printed styles
     *
     * @var array
     */
    var $printed_styles = array();

    /**
     * Array of printed scripts
     *
     * @var array
     */
    var $printed_scripts = array();

    /**
     * Array of replaced styles
     *
     * @var array
     */
    var $replaced_styles = array();

    /**
     * Array of replaced scripts
     *
     * @var array
     */
    var $replaced_scripts = array();

    /**
     * Runs plugin
     */
    function run() {
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

        if ($this->_config->get_boolean('minify.enabled')) {
            if ($this->_config->get_string('minify.engine') == 'file') {
                add_action('w3_minify_cleanup', array(
                    &$this,
                    'cleanup'
                ));
            }

            /**
             * Start minify
             */
            if ($this->can_minify()) {
                ob_start(array(
                    &$this,
                    'ob_callback'
                ));
            }
        }
    }

    /**
     * Returns instance
     *
     * @return W3_Plugin_Minify
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
     * Activate plugin action
     */
    function activate() {
        if (!@is_dir(W3TC_CONTENT_MINIFY_DIR) && !@mkdir(W3TC_CONTENT_MINIFY_DIR)) {
            w3_writable_error(W3TC_CONTENT_MINIFY_DIR);
        }

        $file_index = W3TC_CONTENT_MINIFY_DIR . '/index.php';

        if (!@copy(W3TC_INSTALL_MINIFY_DIR . '/index.php', $file_index)) {
            w3_writable_error($file_index);
        }

        if ($this->_config->get_boolean('minify.enabled') && $this->_config->get_boolean('minify.rewrite')) {
            if (w3_can_modify_rules(w3_get_minify_rules_core_path())) {
                $this->write_rules_core();
            }

            if ($this->_config->get_string('minify.engine') == 'file' && w3_can_modify_rules(w3_get_minify_rules_cache_path())) {
                $this->write_rules_cache();
            }
        }

        $this->schedule();
    }

    /**
     * Deactivate plugin action
     */
    function deactivate() {
        $this->unschedule();

        if (w3_can_modify_rules(w3_get_minify_rules_cache_path())) {
            $this->remove_rules_cache();
        }

        if (w3_can_modify_rules(w3_get_minify_rules_core_path())) {
            $this->remove_rules_core();
        }

        @unlink(W3TC_CONTENT_MINIFY_DIR . '/index.php');
    }

    /**
     * Schedules events
     */
    function schedule() {
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
    function unschedule() {
        if (wp_next_scheduled('w3_minify_cleanup')) {
            wp_clear_scheduled_hook('w3_minify_cleanup');
        }
    }

    /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup() {
        require_once W3TC_LIB_W3_DIR . '/Cache/File/Minify/Manager.php';

        $w3_cache_file_minify_manager = & new W3_Cache_File_Minify_Manager(array(
            'cache_dir' => W3TC_CACHE_FILE_MINIFY_DIR,
            'expire' => $this->_config->get_integer('minify.file.gc'),
            'clean_timelimit' => $this->_config->get_integer('timelimit.cache_gc')
        ));

        $w3_cache_file_minify_manager->clean();
    }

    /**
     * Cron schedules filter
     *
     * @paran array $schedules
     * @return array
     */
    function cron_schedules($schedules) {
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
    function ob_callback(&$buffer) {
        if ($buffer != '' && w3_is_xml($buffer)) {
            if ($this->can_minify2($buffer)) {
                /**
                 * Replace script and style tags
                 */
                if (function_exists('is_feed') && !is_feed()) {
                    $head_prepend = '';
                    $body_prepend = '';
                    $body_append = '';

                    if ($this->_config->get_boolean('minify.auto')) {
                        if ($this->_config->get_boolean('minify.css.enable')) {
                            $files_css = $this->get_files_css($buffer);
                            $this->remove_styles($buffer, $files_css);
                            $head_prepend .= $this->get_style_custom($files_css);
                        }

                        if ($this->_config->get_boolean('minify.js.enable')) {
                            $files_js = $this->get_files_js($buffer);
                            $this->remove_scripts($buffer, $files_js);
                            $head_prepend .= $this->get_script_custom($files_js);
                        }
                    } else {
                        if ($this->_config->get_boolean('minify.css.enable') && !in_array('include', $this->printed_styles)) {
                            $head_prepend .= $this->get_style_group('include');
                            $this->remove_styles_group($buffer, 'include');
                        }

                        if ($this->_config->get_boolean('minify.js.enable')) {
                            if (!in_array('include', $this->printed_scripts)) {
                                $this->remove_scripts_group($buffer, 'include');
                                $head_prepend .= $this->get_script_group('include');
                            }

                            if (!in_array('include-nb', $this->printed_scripts)) {
                                $this->remove_scripts_group($buffer, 'include-nb');
                                $head_prepend .= $this->get_script_group('include-nb');
                            }

                            if (!in_array('include-body', $this->printed_scripts)) {
                                $this->remove_scripts_group($buffer, 'include-body');
                                $body_prepend .= $this->get_script_group('include-body');
                            }

                            if (!in_array('include-body-nb', $this->printed_scripts)) {
                                $this->remove_scripts_group($buffer, 'include-body-nb');
                                $body_prepend .= $this->get_script_group('include-body-nb');
                            }

                            if (!in_array('include-footer', $this->printed_scripts)) {
                                $this->remove_scripts_group($buffer, 'include-footer');
                                $body_append .= $this->get_script_group('include-footer');
                            }

                            if (!in_array('include-footer-nb', $this->printed_scripts)) {
                                $this->remove_scripts_group($buffer, 'include-footer-nb');
                                $body_append .= $this->get_script_group('include-footer-nb');
                            }
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
                }

                /**
                 * Minify HTML/Feed
                 */
                if ($this->_config->get_boolean('minify.html.enable')) {
                    try {
                        $this->minify_html($buffer);
                    } catch (Exception $exception) {
                        $this->error = $exception->getMessage();
                    }
                }
            }

            if ($this->_config->get_boolean('minify.debug')) {
                $buffer .= "\r\n\r\n" . $this->get_debug_info();
            }
        }

        return $buffer;
    }

    /**
     * Parse buffer and return array of JS files from it
     *
     * @param string $buffer
     * @return array
     */
    function get_files_js(&$buffer) {
        $files = w3_extract_js($buffer);
        $files = $this->filter_files($files);

        return $files;
    }

    /**
     * Parse buffer and return array of CSS files from it
     *
     * @param string $buffer
     * @return array
     */
    function get_files_css(&$buffer) {
        $files = w3_extract_css($buffer);
        $files = $this->filter_files($files);

        return $files;
    }

    /**
     * Filters files
     *
     * @param array $files
     * @return array
     */
    function filter_files($files) {
        $files = array_map('w3_normalize_file_minify', $files);
        $files = array_filter($files, create_function('$el', 'return !w3_is_url($el);'));
        $files = array_values(array_unique($files));

        return $files;
    }

    /**
     * Removes style tags from the source
     *
     * @param string $content
     * @param array $files
     * @return void
     */
    function remove_styles(&$content, $files) {
        $regexps = array();
        $domain_url_regexp = w3_get_domain_url_regexp();

        foreach ($files as $file) {
            $this->replaced_styles[] = $file;

            if (w3_is_url($file) && !preg_match('~' . $domain_url_regexp . '~i', $file)) {
                // external CSS files
                $regexps[] = w3_preg_quote($file);
            } else {
                // local CSS files
                $file = ltrim(preg_replace('~' . $domain_url_regexp . '~i', '', $file), '/\\');
                $regexps[] = '(' . $domain_url_regexp . ')?/?' . w3_preg_quote($file);
            }
        }

        foreach ($regexps as $regexp) {
            $content = preg_replace('~<link\s+[^<>]*href=["\']?' . $regexp . '["\']?[^<>]*/?>(.*</link>)?~Uis', '', $content);
            $content = preg_replace('~@import\s+(url\s*)?\(?["\']?\s*' . $regexp . '\s*["\']?\)?[^;]*;?~is', '', $content);
        }

        $content = preg_replace('~<style[^<>]*>\s*</style>~', '', $content);
    }

    /**
     * Remove script tags from the source
     *
     * @param string $content
     * @param array $files
     * @return void
     */
    function remove_scripts(&$content, $files) {
        $regexps = array();
        $domain_url_regexp = w3_get_domain_url_regexp();

        foreach ($files as $file) {
            $this->replaced_scripts[] = $file;

            if (w3_is_url($file) && !preg_match('~' . $domain_url_regexp . '~i', $file)) {
                // external JS files
                $regexps[] = w3_preg_quote($file);
            } else {
                // local JS files
                $file = ltrim(preg_replace('~' . $domain_url_regexp . '~i', '', $file), '/\\');
                $regexps[] = '(' . $domain_url_regexp . ')?/?' . w3_preg_quote($file);
            }
        }

        foreach ($regexps as $regexp) {
            $content = preg_replace('~<script\s+[^<>]*src=["\']?' . $regexp . '["\']?[^<>]*>\s*</script>~Uis', '', $content);
        }
    }

    /**
     * Removes style tag from the source for group
     *
     * @param string $content
     * @param string $location
     * @return void
     */
    function remove_styles_group(&$content, $location) {
        $theme = $this->get_theme();
        $template = $this->get_template();

        $files = array();
        $groups = $this->_config->get_array('minify.css.groups');

        if (isset($groups[$theme]['default'][$location]['files'])) {
            $files = (array) $groups[$theme]['default'][$location]['files'];
        }

        if ($template != 'default' && isset($groups[$theme][$template][$location]['files'])) {
            $files = array_merge($files, (array) $groups[$theme][$template][$location]['files']);
        }

        $this->remove_styles($content, $files);
    }

    /**
     * Removes script tags from the source for group
     *
     * @param string $content
     * @param string $location
     * @return void
     */
    function remove_scripts_group(&$content, $location) {
        $theme = $this->get_theme();
        $template = $this->get_template();

        $files = array();
        $groups = $this->_config->get_array('minify.js.groups');

        if (isset($groups[$theme]['default'][$location]['files'])) {
            $files = (array) $groups[$theme]['default'][$location]['files'];
        }

        if ($template != 'default' && isset($groups[$theme][$template][$location]['files'])) {
            $files = array_merge($files, (array) $groups[$theme][$template][$location]['files']);
        }

        $this->remove_scripts($content, $files);
    }

    /**
     * Minifies HTML
     *
     * @param string $html
     * @return string
     */
    function minify_html(&$html) {
        require_once W3TC_LIB_W3_DIR . '/Minifier.php';
        $w3_minifier =& W3_Minifier::instance();

        $ignored_comments = $this->_config->get_array('minify.html.comments.ignore');

        if (count($ignored_comments)) {
            require_once W3TC_LIB_MINIFY_DIR . '/Minify/IgnoredCommentPreserver.php';

            $ignored_comments_preserver =& new Minify_IgnoredCommentPreserver();
            $ignored_comments_preserver->setIgnoredComments($ignored_comments);

            $ignored_comments_preserver->search($html);
        }

        if ($this->_config->get_boolean('minify.html.inline.js')) {
            $js_engine = $this->_config->get_string('minify.js.engine');

            if (!$w3_minifier->exists($js_engine) || !$w3_minifier->available($js_engine)) {
                $js_engine = 'js';
            }

            $js_minifier = $w3_minifier->get_minifier($js_engine);
            $js_options = $w3_minifier->get_options($js_engine);

            $w3_minifier->init($js_engine);

            require_once W3TC_LIB_MINIFY_DIR . '/Minify/Inline.php';
            require_once W3TC_LIB_MINIFY_DIR . '/Minify/Inline/JavaScript.php';

            $html = Minify_Inline_JavaScript::minify($html, $js_minifier, $js_options);
        }

        if ($this->_config->get_boolean('minify.html.inline.css')) {
            $css_engine = $this->_config->get_string('minify.css.engine');

            if (!$w3_minifier->exists($css_engine) || !$w3_minifier->available($css_engine)) {
                $css_engine = 'css';
            }

            $css_minifier = $w3_minifier->get_minifier($css_engine);
            $css_options = $w3_minifier->get_options($css_engine);

            $w3_minifier->init($css_engine);

            require_once W3TC_LIB_MINIFY_DIR . '/Minify/Inline.php';
            require_once W3TC_LIB_MINIFY_DIR . '/Minify/Inline/CSS.php';

            $html = Minify_Inline_CSS::minify($html, $css_minifier, $css_options);
        }

        $engine = $this->_config->get_string('minify.html.engine');

        if (!$w3_minifier->exists($engine) || !$w3_minifier->available($engine)) {
            $engine = 'html';
        }

        if (function_exists('is_feed') && is_feed()) {
            $engine .= 'xml';
        }

        $minifier = $w3_minifier->get_minifier($engine);
        $options = $w3_minifier->get_options($engine);

        $w3_minifier->init($engine);

        $html = call_user_func($minifier, $html, $options);

        if (isset($ignored_comments_preserver)) {
            $ignored_comments_preserver->replace($html);
        }
    }

    /**
     * Returns current theme
     *
     * @retun string
     */
    function get_theme() {
        static $theme = null;

        if ($theme === null) {
            $theme = w3_get_theme_key(get_theme_root(), get_template(), get_stylesheet());
        }

        return $theme;
    }

    /**
     * Returns current template
     *
     * @return string
     */
    function get_template() {
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
     * Returns style tag
     *
     * @param string $url
     * @param string $import
     * @return string
     */
    function get_style($url, $import = false) {
        if ($import) {
            return "<style type=\"text/css\" media=\"all\">@import url(\"" . $url . "\");</style>\r\n";
        } else {
            return "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . str_replace('&', '&amp;', $url) . "\" media=\"all\" />\r\n";
        }
    }

    /**
     * Prints script tag
     *
     * @param string $url
     * @param boolean $non_blocking
     * @return string
     */
    function get_script($url, $blocking = true) {
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
     * Returns style tag for style group
     *
     * @param string $location
     * @return array
     */
    function get_style_group($location) {
        $style = '';
        $type = 'css';
        $groups = $this->_config->get_array('minify.css.groups');
        $theme = $this->get_theme();
        $template = $this->get_template();

        if ($template != 'default' && empty($groups[$theme][$template][$location]['files'])) {
            $template = 'default';
        }

        if (!empty($groups[$theme][$template][$location]['files'])) {
            $url = $this->format_url_group($theme, $template, $location, $type);
            $import = (isset($groups[$theme][$template][$location]['import']) ? (boolean) $groups[$theme][$template][$location]['import'] : false);

            $style = $this->get_style($url, $import);
        }

        return $style;
    }

    /**
     * Returns script tag for script group
     *
     * @param string $location
     * @return array
     */
    function get_script_group($location) {
        $script = '';
        $type = 'js';
        $theme = $this->get_theme();
        $template = $this->get_template();
        $groups = $this->_config->get_array('minify.js.groups');

        if ($template != 'default' && empty($groups[$theme][$template][$location]['files'])) {
            $template = 'default';
        }

        if (!empty($groups[$theme][$template][$location]['files'])) {
            $url = $this->format_url_group($theme, $template, $location, $type);
            $blocking = (isset($groups[$theme][$template][$location]['blocking']) ? (boolean) $groups[$theme][$template][$location]['blocking'] : true);

            $script = $this->get_script($url, $blocking);
        }

        return $script;
    }

    /**
     * Returns script tag for custom files
     *
     * @param string|array $files
     * @param bool $blocking
     * @return string
     */
    function get_script_custom($files, $blocking = true) {
        $script = '';

        if (count($files)) {
            $url = $this->format_url_custom($files, 'js');
            $script = $this->get_script($url, $blocking);
        }

        return $script;
    }

    /**
     * Returns style tag for custom files
     *
     * @param string|array $files
     * @param boolean $import
     * @return string
     */
    function get_style_custom($files, $import = false) {
        $style = '';

        if (count($files)) {
            $url = $this->format_url_custom($files, 'css');
            $style = $this->get_style($url, $import);
        }

        return $style;
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
    function format_url_group($theme, $template, $location, $type, $rewrite = null) {
        require_once W3TC_LIB_W3_DIR . '/Minify.php';
        $w3_minify = & W3_Minify::instance();

        $id = $w3_minify->get_id_group($theme, $template, $location, $type);

        $site_url_ssl = w3_get_site_url_ssl();

        if ($this->_config->get_boolean('minify.rewrite')) {
            $url = sprintf('%s/%s/%s/%s.%s.%d.%s', $site_url_ssl, W3TC_CONTENT_MINIFY_DIR_NAME, $theme, $template, $location, $id, $type);
        } else {
            $url = sprintf('%s/%s/index.php?file=%s/%s.%s.%d.%s', $site_url_ssl, W3TC_CONTENT_MINIFY_DIR_NAME, $theme, $template, $location, $id, $type);
        }

        return $url;
    }

    /**
     * Formats custom URL
     *
     * @param array $files
     * @param string $type
     * @return string
     */
    function format_url_custom($files, $type) {
        require_once W3TC_LIB_W3_DIR . '/Minify.php';
        $w3_minify = & W3_Minify::instance();

        $w3_minify->set_custom_files($files, $type);
        $hash = $w3_minify->get_custom_files_hash($files);
        $id = $w3_minify->get_id_custom($hash, $type);

        $site_url_ssl = w3_get_site_url_ssl();

        if ($this->_config->get_boolean('minify.rewrite')) {
            $url = sprintf('%s/%s/%s.%d.%s', $site_url_ssl, W3TC_CONTENT_MINIFY_DIR_NAME, $hash, $id, $type);
        } else {
            $url = sprintf('%s/%s/index.php?file=%s.%d.%s', $site_url_ssl, W3TC_CONTENT_MINIFY_DIR_NAME, $hash, $id, $type);
        }

        return $url;
    }

    /**
     * Returns array of minify URLs
     *
     * @return array
     */
    function get_urls() {
        $files = array();

        $js_groups = $this->_config->get_array('minify.js.groups');
        $css_groups = $this->_config->get_array('minify.css.groups');

        foreach ($js_groups as $js_theme => $js_templates) {
            foreach ($js_templates as $js_template => $js_locations) {
                foreach ((array) $js_locations as $js_location => $js_config) {
                    if (!empty($js_config['files'])) {
                        $files[] = $this->format_url_group($js_theme, $js_template, $js_location, 'js');
                    }
                }
            }
        }

        foreach ($css_groups as $css_theme => $css_templates) {
            foreach ($css_templates as $css_template => $css_locations) {
                foreach ((array) $css_locations as $css_location => $css_config) {
                    if (!empty($css_config['files'])) {
                        $files[] = $this->format_url_group($css_theme, $css_template, $css_location, 'css');
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Returns debug info
     */
    function get_debug_info() {
        $debug_info = "<!-- W3 Total Cache: Minify debug info:\r\n";
        $debug_info .= sprintf("%s%s\r\n", str_pad('Engine: ', 20), w3_get_engine_name($this->_config->get_string('minify.engine')));
        $debug_info .= sprintf("%s%s\r\n", str_pad('Theme: ', 20), $this->get_theme());
        $debug_info .= sprintf("%s%s\r\n", str_pad('Template: ', 20), $this->get_template());

        if ($this->minify_reject_reason) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Reject reason: ', 20), $this->minify_reject_reason);
        }

        if ($this->error) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Errors: ', 20), $this->error);
        }

        if (count($this->replaced_styles)) {
            $debug_info .= "\r\nReplaced CSS files:\r\n";

            foreach ($this->replaced_styles as $index => $file) {
                $debug_info .= sprintf("%d. %s\r\n", $index + 1, w3_escape_comment($file));
            }
        }

        if (count($this->replaced_scripts)) {
            $debug_info .= "\r\nReplaced JavaScript files:\r\n";

            foreach ($this->replaced_scripts as $index => $file) {
                $debug_info .= sprintf("%d. %s\r\n", $index + 1, w3_escape_comment($file));
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
    function can_minify() {
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
         * Skip if user is logged in
         */
        if ($this->_config->get_boolean('minify.reject.logged') && !$this->check_logged_in()) {
            $this->minify_reject_reason = 'user is logged in';

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
    function can_minify2(&$buffer) {
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
    function check_ua() {
        foreach ($this->_config->get_array('minify.reject.ua') as $ua) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], $ua) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user is logged in
     *
     * @return boolean
     */
    function check_logged_in() {
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
     * Checks request URI
     *
     * @return boolean
     */
    function check_request_uri() {
        $auto_reject_uri = array(
            'wp-login',
            'wp-register'
        );

        foreach ($auto_reject_uri as $uri) {
            if (strstr($_SERVER['REQUEST_URI'], $uri) !== false) {
                return false;
            }
        }

        $reject_uri = $this->_config->get_array('minify.reject.uri');
        $reject_uri = array_map('w3_parse_path', $reject_uri);

        foreach ($reject_uri as $expr) {
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
    function generate_rules_core() {
        switch (true) {
            case w3_is_apache():
                return $this->generate_rules_core_apache();

            case w3_is_nginx():
                return $this->generate_rules_core_nginx();
        }

        return false;
    }

    /**
     * Generates rules
     *
     * @return string
     */
    function generate_rules_core_apache() {
        $cache_dir = str_replace(w3_get_document_root(), '', w3_path(W3TC_CACHE_FILE_MINIFY_DIR));

        $engine = $this->_config->get_string('minify.engine');
        $browsercache = $this->_config->get_integer('browsercache.enabled');
        $compression = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.compression'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CORE . "\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "    RewriteEngine On\n";
        $rules .= "    RewriteBase " . $cache_dir . "/\n";
        $rules .= "    RewriteRule ^w3tc_rewrite_test$ index.php?w3tc_rewrite_test=1 [L]\n";

        if ($engine == 'file') {
            if ($compression) {
                $rules .= "    RewriteCond %{HTTP:Accept-Encoding} gzip\n";
                $rules .= "    RewriteRule .* - [E=APPEND_EXT:.gzip]\n";
            }

            $rules .= "    RewriteCond %{REQUEST_FILENAME}%{ENV:APPEND_EXT} -f\n";
            $rules .= "    RewriteRule (.*) $1%{ENV:APPEND_EXT} [L]\n";
        }

        $rules .= "    RewriteRule ^(.+\\.(css|js))$ index.php?file=$1 [L]\n";

        $rules .= "</IfModule>\n";
        $rules .= W3TC_MARKER_END_MINIFY_CORE . "\n";

        return $rules;
    }

    /**
     * Generates rules
     *
     * @return string
     */
    function generate_rules_core_nginx() {
        $is_network = w3_is_network();

        $cache_root = w3_path(W3TC_CACHE_FILE_MINIFY_DIR);
        $cache_dir_condition = $cache_dir_rewrite = rtrim(str_replace(w3_get_document_root(), '', $cache_root), '/');
        $offset = 1;

        if ($is_network) {
            $cache_dir_condition = preg_replace('~/w3tc.*?/~', '/w3tc(.*?)/', $cache_dir_condition, 1);
            $cache_dir_rewrite = preg_replace('~/w3tc.*?/~', '/w3tc\$' . $offset++ . '/', $cache_dir_rewrite, 1);
        }

        $engine = $this->_config->get_string('minify.engine');
        $browsercache = $this->_config->get_integer('browsercache.enabled');
        $compression = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.compression'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CORE . "\n";
        $rules .= "rewrite ^" . $cache_dir_condition . "/w3tc_rewrite_test$ " . $cache_dir_rewrite . "/index.php?w3tc_rewrite_test=1 last;\n";

        if ($engine == 'file') {
            $rules .= "set \$w3tc_enc \"\";\n";

            if ($compression) {
                $rules .= "if (\$http_accept_encoding ~ gzip) {\n";
                $rules .= "    set \$w3tc_enc .gzip;\n";
                $rules .= "}\n";
            }

            $rules .= "if (-f \$request_filename\$w3tc_enc) {\n";
            $rules .= "    rewrite (.*) $1\$w3tc_enc break;\n";
            $rules .= "}\n";
        }

        $rules .= "rewrite ^" . $cache_dir_condition . "/(.+\\.(css|js))$ " . $cache_dir_rewrite . "/index.php?file=$" . $offset . " last;\n";
        $rules .= W3TC_MARKER_END_MINIFY_CORE . "\n";

        return $rules;
    }

    /**
     * Generates rules
     *
     * @return string
     */
    function generate_rules_cache() {
        switch (true) {
            case w3_is_apache():
                return $this->generate_rules_cache_apache();

            case w3_is_nginx():
                return $this->generate_rules_cache_nginx();
        }

        return false;
    }

    /**
     * Generates rules
     *
     * @return string
     */
    function generate_rules_cache_apache() {
        $browsercache = $this->_config->get_integer('browsercache.enabled');
        $compression = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.compression'));
        $expires = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.expires'));
        $lifetime = ($browsercache ? $this->_config->get_integer('browsercache.cssjs.lifetime') : 0);
        $cache_control = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.cache.control'));
        $etag = ($browsercache && $this->_config->get_integer('browsercache.html.etag'));
        $w3tc = ($browsercache && $this->_config->get_integer('browsercache.cssjs.w3tc'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CACHE . "\n";

        if ($etag) {
            $rules .= "FileETag MTime Size\n";
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
        }

        if ($expires) {
            $rules .= "<IfModule mod_expires.c>\n";
            $rules .= "    ExpiresActive On\n";
            $rules .= "    ExpiresByType text/css M" . $lifetime . "\n";
            $rules .= "    ExpiresByType application/x-javascript M" . $lifetime . "\n";
            $rules .= "</IfModule>\n";
        }

        if ($w3tc || $compression || $cache_control) {
            $rules .= "<IfModule mod_headers.c>\n";

            if ($w3tc) {
                $rules .= "    Header set X-Powered-By \"" . W3TC_POWERED_BY . "\"\n";
            }

            if ($compression) {
                $rules .= "    Header set Vary \"Accept-Encoding\"\n";
            }

            if ($cache_control) {
                $cache_policy = $this->_config->get_string('browsercache.cssjs.cache.policy');

                switch ($cache_policy) {
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

        $rules .= W3TC_MARKER_END_MINIFY_CACHE . "\n";

        return $rules;
    }

    /**
     * Generates rules
     *
     * @return string
     */
    function generate_rules_cache_nginx() {
        $cache_root = w3_path(W3TC_CACHE_FILE_MINIFY_DIR);
        $cache_dir = rtrim(str_replace(w3_get_document_root(), '', $cache_root), '/');

        if (w3_is_network()) {
            $cache_dir = preg_replace('~/w3tc.*?/~', '/w3tc.*?/', $cache_dir, 1);
        }

        $browsercache = $this->_config->get_integer('browsercache.enabled');
        $compression = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.compression'));
        $expires = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.expires'));
        $lifetime = ($browsercache ? $this->_config->get_integer('browsercache.cssjs.lifetime') : 0);
        $cache_control = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.cache.control'));
        $w3tc = ($browsercache && $this->_config->get_integer('browsercache.cssjs.w3tc'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CACHE . "\n";

        $common_rules = '';

        if ($expires) {
            $common_rules .= "    expires modified " . $lifetime . "s;\n";
        }

        if ($w3tc) {
            $common_rules .= "    add_header X-Powered-By \"" . W3TC_POWERED_BY . "\";\n";
        }

        if ($compression) {
            $common_rules .= "    add_header Vary \"Accept-Encoding\";\n";
        }

        if ($cache_control) {
            $cache_policy = $this->_config->get_string('browsercache.cssjs.cache.policy');

            switch ($cache_policy) {
                case 'cache':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"public\";\n";
                    break;

                case 'cache_validation':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"public, must-revalidate, proxy-revalidate\";\n";
                    break;

                case 'cache_noproxy':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"public, must-revalidate\";\n";
                    break;

                case 'cache_maxage':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\";\n";
                    break;

                case 'no_cache':
                    $common_rules .= "    add_header Pragma \"no-cache\";\n";
                    $common_rules .= "    add_header Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\";\n";
                    break;
            }
        }

        $rules .= "location ~ " . $cache_dir . ".*\\.js$ {\n";
        $rules .= "    types {}\n";
        $rules .= "    default_type application/x-javascript;\n";
        $rules .= $common_rules;
        $rules .= "}\n";

        $rules .= "location ~ " . $cache_dir . ".*\\.css$ {\n";
        $rules .= "    types {}\n";
        $rules .= "    default_type text/css;\n";
        $rules .= $common_rules;
        $rules .= "}\n";

        if ($compression) {
            $rules .= "location ~ " . $cache_dir . ".*js\\.gzip$ {\n";
            $rules .= "    gzip off;\n";
            $rules .= "    types {}\n";
            $rules .= "    default_type application/x-javascript;\n";
            $rules .= $common_rules;
            $rules .= "    add_header Content-Encoding gzip;\n";
            $rules .= "}\n";

            $rules .= "location ~ " . $cache_dir . ".*css\\.gzip$ {\n";
            $rules .= "    gzip off;\n";
            $rules .= "    types {}\n";
            $rules .= "    default_type text/css;\n";
            $rules .= $common_rules;
            $rules .= "    add_header Content-Encoding gzip;\n";
            $rules .= "}\n";
        }

        $rules .= W3TC_MARKER_END_MINIFY_CACHE . "\n";

        return $rules;
    }

    /**
     * Writes rules to file cache .htaccess
     *
     * @return boolean
     */
    function write_rules_core() {
        $path = w3_get_minify_rules_core_path();

        if (file_exists($path)) {
            $data = @file_get_contents($path);

            if ($data === false) {
                return false;
            }
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_MINIFY_CORE);
        $replace_end = strpos($data, W3TC_MARKER_END_MINIFY_CORE);

        if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
            $replace_length = $replace_end - $replace_start + strlen(W3TC_MARKER_END_MINIFY_CORE) + 1;
        } else {
            $replace_start = false;
            $replace_length = 0;

            $search = array(
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
                W3TC_MARKER_END_BROWSERCACHE_CACHE => strlen(W3TC_MARKER_END_BROWSERCACHE_CACHE) + 1,
                W3TC_MARKER_END_PGCACHE_CACHE => strlen(W3TC_MARKER_END_PGCACHE_CACHE) + 1,
                W3TC_MARKER_END_MINIFY_CACHE => strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1
            );

            foreach ($search as $string => $length) {
                $replace_start = strpos($data, $string);

                if ($replace_start !== false) {
                    $replace_start += $length;
                    break;
                }
            }
        }

        $rules = $this->generate_rules_core();

        if ($replace_start !== false) {
            $data = w3_trim_rules(substr_replace($data, $rules, $replace_start, $replace_length));
        } else {
            $data = w3_trim_rules($data . $rules);
        }

        return @file_put_contents($path, $data);
    }

    /**
     * Writes rules to file cache .htaccess
     *
     * @return boolean
     */
    function write_rules_cache() {
        $path = w3_get_minify_rules_cache_path();

        if (file_exists($path)) {
            $data = @file_get_contents($path);

            if ($data === false) {
                return false;
            }
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_MINIFY_CACHE);
        $replace_end = strpos($data, W3TC_MARKER_END_MINIFY_CACHE);

        if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
            $replace_length = $replace_end - $replace_start + strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1;
        } else {
            $replace_start = false;
            $replace_length = 0;

            $search = array(
                W3TC_MARKER_BEGIN_PGCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_MINIFY_CORE => 0,
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0
            );

            foreach ($search as $string => $length) {
                $replace_start = strpos($data, $string);

                if ($replace_start !== false) {
                    $replace_start += $length;
                    break;
                }
            }
        }

        $rules = $this->generate_rules_cache();

        if ($replace_start !== false) {
            $data = w3_trim_rules(substr_replace($data, $rules, $replace_start, $replace_length));
        } else {
            $data = w3_trim_rules($data . $rules);
        }

        return @file_put_contents($path, $data);
    }

    /**
     * Erases W3TC rules from config
     *
     * @param string $data
     * @return string
     */
    function erase_rules_core($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_MINIFY_CORE, W3TC_MARKER_END_MINIFY_CORE);

        return $data;
    }

    /**
     * Erases W3TC rules from config
     *
     * @param string $data
     * @return string
     */
    function erase_rules_cache($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_MINIFY_CACHE, W3TC_MARKER_END_MINIFY_CACHE);

        return $data;
    }

    /**
     * Removes W3TC rules from file cache dir
     *
     * @return boolean
     */
    function remove_rules_core() {
        $path = w3_get_minify_rules_core_path();

        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_core($data);

                return @file_put_contents($path, $data);
            }

            return false;
        }

        return true;
    }

    /**
     * Removes W3TC rules from file cache dir
     *
     * @return boolean
     */
    function remove_rules_cache() {
        $path = w3_get_minify_rules_cache_path();

        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_cache($data);

                return @file_put_contents($path, $data);
            }

            return false;
        }

        return true;
    }

    /**
     * Checks rules
     *
     * @return boolean
     */
    function check_rules_core() {
        $path = w3_get_minify_rules_core_path();
        $search = $this->generate_rules_core();

        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }

    /**
     * Checks rules
     *
     * @return boolean
     */
    function check_rules_cache() {
        $path = w3_get_minify_rules_cache_path();
        $search = $this->generate_rules_cache();

        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }
}
