<?php

/**
 * W3 ObjectCache plugin
 */
require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_BrowserCache
 */
class W3_Plugin_BrowserCache extends W3_Plugin {
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

        if ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.html.w3tc')) {
            add_action('send_headers', array(
                &$this,
                'send_headers'
            ));
        }

        if ($this->can_ob()) {
            ob_start(array(
                &$this,
                'ob_callback'
            ));
        }
    }

    /**
     * Returns plugin instance
     *
     * @return W3_Plugin_BrowserCache
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
        if ($this->_config->get_boolean('browsercache.enabled')) {
            if (w3_can_modify_rules(w3_get_browsercache_rules_cache_path())) {
                $this->write_rules_cache();
            }

            if ($this->_config->get_boolean('browsercache.no404wp') && w3_can_modify_rules(w3_get_browsercache_rules_no404wp_path())) {
                $this->write_rules_no404wp();
            }
        }
    }

    /**
     * Deactivate plugin action
     */
    function deactivate() {
        if (w3_can_modify_rules(w3_get_browsercache_rules_no404wp_path())) {
            $this->remove_rules_no404wp();
        }

        if (w3_can_modify_rules(w3_get_browsercache_rules_cache_path())) {
            $this->remove_rules_cache();
        }
    }

    /**
     * Check if we can start OB
     *
     * @return boolean
     */
    function can_ob() {
        /**
         * Browser cache should be enabled
         */
        if (!$this->_config->get_boolean('browsercache.enabled')) {
            return false;
        }

        /**
         * Replace feature should be enabled
         */
        if (!$this->_config->get_boolean('browsercache.cssjs.replace') && !$this->_config->get_boolean('browsercache.html.replace') && !$this->_config->get_boolean('browsercache.other.replace')) {
            return false;
        }

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

        return true;
    }

    /**
     * Output buffer callback
     *
     * @param string $buffer
     * @return mixed
     */
    function ob_callback(&$buffer) {
        global $wpdb;

        if ($buffer != '' && w3_is_xml($buffer)) {
            $domain_url_regexp = w3_get_domain_url_regexp();

            $buffer = preg_replace_callback('~(href|src|action)=[\'"]((' . $domain_url_regexp . ')?(/.*?\.([a-z-_]+?)(\?.*?)?))[\'"]~', array(
                &$this,
                'link_replace_callback'
            ), $buffer);
        }

        return $buffer;

    }

    /**
     * Link replace callback
     *
     * @param string $matches
     * @return string
     */
    function link_replace_callback($matches) {
        static $id = null, $extensions = null;

        if ($id === null) {
            $id = $this->_config->get_integer('browsercache.id', date('Ymd'));
        }

        if ($extensions === null) {
            $types = array();
            $extensions = array();

            if ($this->_config->get_boolean('browsercache.cssjs.replace')) {
                $types = array_merge($types, array_keys($this->get_cssjs_types()));
            }

            if ($this->_config->get_boolean('browsercache.html.replace')) {
                $types = array_merge($types, array_keys($this->get_html_types()));
            }

            if ($this->_config->get_boolean('browsercache.other.replace')) {
                $types = array_merge($types, array_keys($this->get_other_types()));
            }

            foreach ($types as $type) {
                $extensions = array_merge($extensions, explode('|', $type));
            }
        }

        list ($match, $attr, $url, , , , $extension) = $matches;

        if (in_array($extension, $extensions)) {
            $url = w3_remove_wp_query($url);
            $url .= (strstr($url, '?') !== false ? '&' : '?') . $id;

            return sprintf('%s="%s"', $attr, $url);
        }

        return $match;
    }

    /**
     * Send headers
     */
    function send_headers() {
        @header('X-Powered-By: ' . W3TC_POWERED_BY);
    }

    /**
     * Returns CSS/JS mime types
     *
     * @return array
     */
    function get_cssjs_types() {
        $mime_types = include W3TC_DIR . '/inc/mime/cssjs.php';

        return $mime_types;
    }

    /**
     * Returns HTML mime types
     *
     * @return array
     */
    function get_html_types() {
        $mime_types = include W3TC_DIR . '/inc/mime/html.php';

        return $mime_types;
    }

    /**
     * Returns other mime types
     *
     * @return array
     */
    function get_other_types() {
        $mime_types = include W3TC_DIR . '/inc/mime/other.php';

        return $mime_types;
    }

    /**
     * Returns cache rules
     *
     * @param bool $cdn
     * @return string
     */
    function generate_rules_cache($cdn = false) {
        switch (true) {
            case w3_is_apache():
                return $this->generate_rules_cache_apache($cdn);

            case w3_is_nginx():
                return $this->generate_rules_cache_nginx($cdn);
        }

        return false;
    }

    /**
     * Returns cache rules
     *
     * @param bool $cdn
     * @return string
     */
    function generate_rules_cache_apache($cdn = false) {
        $cssjs_types = $this->get_cssjs_types();
        $html_types = $this->get_html_types();
        $other_types = $this->get_other_types();

        $cssjs_expires = $this->_config->get_boolean('browsercache.cssjs.expires');
        $html_expires = $this->_config->get_boolean('browsercache.html.expires');
        $other_expires = $this->_config->get_boolean('browsercache.other.expires');

        $cssjs_lifetime = $this->_config->get_integer('browsercache.cssjs.lifetime');
        $html_lifetime = $this->_config->get_integer('browsercache.html.lifetime');
        $other_lifetime = $this->_config->get_integer('browsercache.other.lifetime');

        $mime_types = array();

        if ($cssjs_expires && $cssjs_lifetime) {
            $mime_types = array_merge($mime_types, $cssjs_types);
        }

        if ($html_expires && $html_lifetime) {
            $mime_types = array_merge($mime_types, $html_types);
        }

        if ($other_expires && $other_lifetime) {
            $mime_types = array_merge($mime_types, $other_types);
        }

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE . "\n";

        if ($cdn) {
            $rules .= "<IfModule mod_headers.c>\n";
            $rules .= "    Header set Access-Control-Allow-Origin *\n";
            $rules .= "</IfModule>\n";
        }

        if (count($mime_types)) {
            $rules .= "<IfModule mod_mime.c>\n";

            foreach ($mime_types as $ext => $mime_type) {
                $extensions = explode('|', $ext);

                $rules .= "    AddType " . $mime_type;

                foreach ($extensions as $extension) {
                    $rules .= " ." . $extension;
                }

                $rules .= "\n";
            }

            $rules .= "</IfModule>\n";

            $rules .= "<IfModule mod_expires.c>\n";
            $rules .= "    ExpiresActive On\n";

            if ($cssjs_expires && $cssjs_lifetime) {
                foreach ($cssjs_types as $mime_type) {
                    $rules .= "    ExpiresByType " . $mime_type . " A" . $cssjs_lifetime . "\n";
                }
            }

            if ($html_expires && $html_lifetime) {
                foreach ($html_types as $mime_type) {
                    $rules .= "    ExpiresByType " . $mime_type . " A" . $html_lifetime . "\n";
                }
            }

            if ($other_expires && $other_lifetime) {
                foreach ($other_types as $mime_type) {
                    $rules .= "    ExpiresByType " . $mime_type . " A" . $other_lifetime . "\n";
                }
            }

            $rules .= "</IfModule>\n";
        }

        $cssjs_compression = $this->_config->get_boolean('browsercache.cssjs.compression');
        $html_compression = $this->_config->get_boolean('browsercache.html.compression');
        $other_compression = $this->_config->get_boolean('browsercache.other.compression');

        if ($cssjs_compression || $html_compression || $other_compression) {
            $compression_types = array();

            if ($cssjs_compression) {
                $compression_types = array_merge($compression_types, $cssjs_types);
            }

            if ($html_compression) {
                $compression_types = array_merge($compression_types, $html_types);
            }

            if ($other_compression) {
                $compression_types = array_merge($compression_types, array(
                    'ico' => 'image/x-icon'
                ));
            }

            $rules .= "<IfModule mod_deflate.c>\n";
            $rules .= "    <IfModule mod_setenvif.c>\n";
            $rules .= "        BrowserMatch ^Mozilla/4 gzip-only-text/html\n";
            $rules .= "        BrowserMatch ^Mozilla/4\\.0[678] no-gzip\n";
            $rules .= "        BrowserMatch \\bMSIE !no-gzip !gzip-only-text/html\n";
            $rules .= "        BrowserMatch \\bMSI[E] !no-gzip !gzip-only-text/html\n";
            $rules .= "    </IfModule>\n";
            $rules .= "    <IfModule mod_headers.c>\n";
            $rules .= "        Header append Vary User-Agent env=!dont-vary\n";
            $rules .= "    </IfModule>\n";
            $rules .= "    <IfModule mod_filter.c>\n";
            $rules .= "        AddOutputFilterByType DEFLATE " . implode(' ', $compression_types) . "\n";
            $rules .= "    </IfModule>\n";
            $rules .= "</IfModule>\n";
        }

        $this->_generate_rules_cache_apache($rules, $cssjs_types, 'cssjs');
        $this->_generate_rules_cache_apache($rules, $html_types, 'html');
        $this->_generate_rules_cache_apache($rules, $other_types, 'other');

        $rules .= W3TC_MARKER_END_BROWSERCACHE_CACHE . "\n";

        return $rules;
    }

    /**
     * Writes cache rules
     *
     * @param string $rules
     * @param array $mime_types
     * @param string $section
     * @return void
     */
    function _generate_rules_cache_apache(&$rules, $mime_types, $section) {
        $cache_control = $this->_config->get_boolean('browsercache.' . $section . '.cache.control');
        $etag = $this->_config->get_boolean('browsercache.' . $section . '.etag');
        $w3tc = $this->_config->get_boolean('browsercache.' . $section . '.w3tc');

        $extensions = array_keys($mime_types);
        $extensions_lowercase = array_map('strtolower', $extensions);
        $extensions_uppercase = array_map('strtoupper', $extensions);

        $rules .= "<FilesMatch \"\\.(" . implode('|', array_merge($extensions_lowercase, $extensions_uppercase)) . ")$\">\n";

        if ($cache_control) {
            $cache_policy = $this->_config->get_string('browsercache.' . $section . '.cache.policy');

            switch ($cache_policy) {
                case 'cache':
                    $rules .= "    <IfModule mod_headers.c>\n";
                    $rules .= "        Header set Pragma \"public\"\n";
                    $rules .= "        Header set Cache-Control \"public\"\n";
                    $rules .= "    </IfModule>\n";
                    break;

                case 'cache_validation':
                    $rules .= "    <IfModule mod_headers.c>\n";
                    $rules .= "        Header set Pragma \"public\"\n";
                    $rules .= "        Header set Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                    $rules .= "    </IfModule>\n";
                    break;

                case 'cache_noproxy':
                    $rules .= "    <IfModule mod_headers.c>\n";
                    $rules .= "        Header set Pragma \"public\"\n";
                    $rules .= "        Header set Cache-Control \"public, must-revalidate\"\n";
                    $rules .= "    </IfModule>\n";
                    break;

                case 'cache_maxage':
                    $expires = $this->_config->get_boolean('browsercache.' . $section . '.expires');
                    $lifetime = $this->_config->get_integer('browsercache.' . $section . '.lifetime');

                    $rules .= "    <IfModule mod_headers.c>\n";
                    $rules .= "        Header set Pragma \"public\"\n";

                    if ($expires) {
                        $rules .= "        Header append Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                    } else {
                        $rules .= "        Header set Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\"\n";
                    }

                    $rules .= "    </IfModule>\n";
                    break;

                case 'no_cache':
                    $rules .= "    <IfModule mod_headers.c>\n";
                    $rules .= "        Header set Pragma \"no-cache\"\n";
                    $rules .= "        Header set Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\"\n";
                    $rules .= "    </IfModule>\n";
                    break;
            }
        }

        if ($etag) {
            $rules .= "    FileETag MTime Size\n";
        } else {
            $rules .= "    FileETag None\n";
        }

        if ($w3tc) {
            $rules .= "    <IfModule mod_headers.c>\n";
            $rules .= "         Header set X-Powered-By \"" . W3TC_POWERED_BY . "\"\n";
            $rules .= "    </IfModule>\n";
        }

        $rules .= "</FilesMatch>\n";

        return $rules;
    }

    /**
     * Returns cache rules
     *
     * @param bool $cdn
     * @return string
     */
    function generate_rules_cache_nginx($cdn = false) {
        $cssjs_types = $this->get_cssjs_types();
        $html_types = $this->get_html_types();
        $other_types = $this->get_other_types();

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE . "\n";

        if ($cdn) {
            $rules .= "add_header Access-Control-Allow-Origin \"*\"\n";
        }

        $cssjs_compression = $this->_config->get_boolean('browsercache.cssjs.compression');
        $html_compression = $this->_config->get_boolean('browsercache.html.compression');
        $other_compression = $this->_config->get_boolean('browsercache.other.compression');

        if ($cssjs_compression || $html_compression || $other_compression) {
            $compression_types = array();

            if ($cssjs_compression) {
                $compression_types = array_merge($compression_types, $cssjs_types);
            }

            if ($html_compression) {
                $compression_types = array_merge($compression_types, $html_types);
            }

            if ($other_compression) {
                $compression_types = array_merge($compression_types, array(
                    'ico' => 'image/x-icon'
                ));
            }

            unset($compression_types['html|htm']);

            $rules .= "gzip on;\n";
            $rules .= "gzip_types " . implode(' ', $compression_types) . ";\n";
        }

        $this->_generate_rules_cache_nginx($rules, $cssjs_types, 'cssjs');
        $this->_generate_rules_cache_nginx($rules, $html_types, 'html');
        $this->_generate_rules_cache_nginx($rules, $other_types, 'other');

        $rules .= W3TC_MARKER_END_BROWSERCACHE_CACHE . "\n";

        return $rules;
    }

    /**
     * Writes cache rules
     *
     * @param string $rules
     * @param array $mime_types
     * @param string $section
     * @return void
     */
    function _generate_rules_cache_nginx(&$rules, $mime_types, $section) {
        $expires = $this->_config->get_boolean('browsercache.' . $section . '.expires');
        $cache_control = $this->_config->get_boolean('browsercache.' . $section . '.cache.control');
        $w3tc = $this->_config->get_boolean('browsercache.' . $section . '.w3tc');

        if ($expires || $cache_control || $w3tc) {
            $lifetime = $this->_config->get_integer('browsercache.' . $section . '.lifetime');

            $rules .= "location ~ \\.(" . implode('|', array_keys($mime_types)) . ")$ {\n";

            if ($expires) {
                $rules .= "    expires " . $lifetime . "s;\n";
            }

            if ($cache_control) {
                $cache_policy = $this->_config->get_string('browsercache.cssjs.cache.policy');

                switch ($cache_policy) {
                    case 'cache':
                        $rules .= "    add_header Pragma \"public\";\n";
                        $rules .= "    add_header Cache-Control \"public\";\n";
                        break;

                    case 'cache_validation':
                        $rules .= "    add_header Pragma \"public\";\n";
                        $rules .= "    add_header Cache-Control \"public, must-revalidate, proxy-revalidate\";\n";
                        break;

                    case 'cache_noproxy':
                        $rules .= "    add_header Pragma \"public\";\n";
                        $rules .= "    add_header Cache-Control \"public, must-revalidate\";\n";
                        break;

                    case 'cache_maxage':
                        $rules .= "    add_header Pragma \"public\";\n";
                        $rules .= "    add_header Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\";\n";
                        break;

                    case 'no_cache':
                        $rules .= "    add_header Pragma \"no-cache\";\n";
                        $rules .= "    add_header Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\";\n";
                        break;
                }
            }

            if ($w3tc) {
                $rules .= "    add_header X-Powered-By \"" . W3TC_POWERED_BY . "\";\n";
            }

            $rules .= "}\n";
        }
    }

    /**
     * Generate rules related to prevent for media 404 error by WP
     *
     * @return string
     */
    function generate_rules_no404wp() {
        switch (true) {
            case w3_is_apache():
                return $this->generate_rules_no404wp_apache();

            case w3_is_nginx():
                return $this->generate_rules_no404wp_nginx();
        }

        return false;
    }

    /**
     * Generate rules related to prevent for media 404 error by WP
     *
     * @return string
     */
    function generate_rules_no404wp_apache() {
        $cssjs_types = $this->get_cssjs_types();
        $html_types = $this->get_html_types();
        $other_types = $this->get_other_types();

        $extensions = array_merge(array_keys($cssjs_types), array_keys($html_types), array_keys($other_types));

        $permalink_structure = get_option('permalink_structure');
        $permalink_structure_ext = ltrim(strrchr($permalink_structure, '.'), '.');

        if ($permalink_structure_ext != '') {
            foreach ($extensions as $index => $extension) {
                if (strstr($extension, $permalink_structure_ext) !== false) {
                    $extensions[$index] = preg_replace('~\|?' . w3_preg_quote($permalink_structure_ext) . '\|?~', '', $extension);
                }
            }
        }

        $exceptions = $this->_config->get_array('browsercache.no404wp.exceptions');

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP . "\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "    RewriteEngine On\n";
        $rules .= "    RewriteCond %{REQUEST_FILENAME} !-f\n";
        $rules .= "    RewriteCond %{REQUEST_FILENAME} !-d\n";

        if (count($exceptions)) {
            $rules .= "    RewriteCond %{REQUEST_URI} !(" . implode('|', $exceptions) . ")\n";
        }

        $rules .= "    RewriteCond %{REQUEST_FILENAME} \\.(" . implode('|', $extensions) . ")$ [NC]\n";
        $rules .= "    RewriteRule .* - [L]\n";
        $rules .= "</IfModule>\n";
        $rules .= W3TC_MARKER_END_BROWSERCACHE_NO404WP . "\n";

        return $rules;
    }

    /**
     * Generate rules related to prevent for media 404 error by WP
     *
     * @return string
     */
    function generate_rules_no404wp_nginx() {
        $cssjs_types = $this->get_cssjs_types();
        $html_types = $this->get_html_types();
        $other_types = $this->get_other_types();

        $extensions = array_merge(array_keys($cssjs_types), array_keys($html_types), array_keys($other_types));

        $permalink_structure = get_option('permalink_structure');
        $permalink_structure_ext = ltrim(strrchr($permalink_structure, '.'), '.');

        if ($permalink_structure_ext != '') {
            foreach ($extensions as $index => $extension) {
                if (strstr($extension, $permalink_structure_ext) !== false) {
                    $extensions[$index] = preg_replace('~\|?' . w3_preg_quote($permalink_structure_ext) . '\|?~', '', $extension);
                }
            }
        }

        $exceptions = $this->_config->get_array('browsercache.no404wp.exceptions');

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP . "\n";
        $rules .= "if (-f \$request_filename) {\n";
        $rules .= "    break;\n";
        $rules .= "}\n";
        $rules .= "if (-d \$request_filename) {\n";
        $rules .= "    break;\n";
        $rules .= "}\n";

        if (count($exceptions)) {
            $rules .= "if (\$request_uri ~ \"(" . implode('|', $exceptions) . ")\") {\n";
            $rules .= "    break;\n";
            $rules .= "}\n";
        }

        $rules .= "if (\$request_uri ~* \\.(" . implode('|', $extensions) . ")$) {\n";
        $rules .= "    return 404;\n";
        $rules .= "}\n";
        $rules .= W3TC_MARKER_END_BROWSERCACHE_NO404WP . "\n";

        return $rules;
    }

    /**
     * Writes cache rules
     *
     * @return boolean
     */
    function write_rules_cache() {
        $path = w3_get_browsercache_rules_cache_path();

        if (file_exists($path)) {
            $data = @file_get_contents($path);

            if ($data === false) {
                return false;
            }
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE);
        $replace_end = strpos($data, W3TC_MARKER_END_BROWSERCACHE_CACHE);

        if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
            $replace_length = $replace_end - $replace_start + strlen(W3TC_MARKER_END_BROWSERCACHE_CACHE) + 1;
        } else {
            $replace_start = false;
            $replace_length = 0;

            $search = array(
                W3TC_MARKER_BEGIN_MINIFY_CORE => 0,
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
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

        $rules = $this->generate_rules_cache();

        if ($replace_start !== false) {
            $data = w3_trim_rules(substr_replace($data, $rules, $replace_start, $replace_length));
        } else {
            $data = w3_trim_rules($data . $rules);
        }

        return @file_put_contents($path, $data);
    }

    /**
     * Writes no 404 by WP rules
     *
     * @return boolean
     */
    function write_rules_no404wp() {
        $path = w3_get_browsercache_rules_no404wp_path();

        if (file_exists($path)) {
            $data = @file_get_contents($path);

            if ($data === false) {
                return false;
            }
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP);
        $replace_end = strpos($data, W3TC_MARKER_END_BROWSERCACHE_NO404WP);

        if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
            $replace_length = $replace_end - $replace_start + strlen(W3TC_MARKER_END_BROWSERCACHE_NO404WP) + 1;
        } else {
            $replace_start = false;
            $replace_length = 0;

            $search = array(
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
                W3TC_MARKER_END_PGCACHE_CORE => strlen(W3TC_MARKER_END_PGCACHE_CORE) + 1,
                W3TC_MARKER_END_MINIFY_CORE => strlen(W3TC_MARKER_END_MINIFY_CORE) + 1,
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

        $rules = $this->generate_rules_no404wp();

        if ($replace_start !== false) {
            $data = w3_trim_rules(substr_replace($data, $rules, $replace_start, $replace_length));
        } else {
            $data = w3_trim_rules($data . $rules);
        }

        return @file_put_contents($path, $data);
    }

    /**
     * Erases rules
     *
     * @param string $data
     * @return string
     */
    function erase_rules_cache($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE, W3TC_MARKER_END_BROWSERCACHE_CACHE);

        return $data;
    }

    /**
     * Erases rules
     *
     * @param string $data
     * @return string
     */
    function erase_rules_no404wp($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP, W3TC_MARKER_END_BROWSERCACHE_NO404WP);

        return $data;
    }

    /**
     * Removes rules
     *
     * @return boolean
     */
    function remove_rules_cache() {
        $path = w3_get_browsercache_rules_cache_path();

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
     * Removes rules
     *
     * @return boolean
     */
    function remove_rules_no404wp() {
        $path = w3_get_browsercache_rules_no404wp_path();

        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_no404wp($data);

                return @file_put_contents($path, $data);
            }

            return false;
        }

        return true;
    }

    /**
     * Check cache rules
     *
     * @return boolean
     */
    function check_rules_cache() {
        $path = w3_get_browsercache_rules_cache_path();
        $search = $this->generate_rules_cache();

        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }

    /**
     * Check 404 rules
     *
     * @return boolean
     */
    function check_rules_no404wp() {
        $path = w3_get_browsercache_rules_no404wp_path();
        $search = $this->generate_rules_no404wp();

        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }

    /**
     * Returns cache config for CDN
     *
     * @return array
     */
    function get_cache_config() {
        $config = array();

        $cssjs_types = $this->get_cssjs_types();
        $html_types = $this->get_html_types();
        $other_types = $this->get_other_types();

        $this->_get_cache_config($config, $cssjs_types, 'cssjs');
        $this->_get_cache_config($config, $html_types, 'html');
        $this->_get_cache_config($config, $other_types, 'other');

        return $config;
    }

    /**
     * Writes cache config
     *
     * @param string $config
     * @param string $mime_types
     * @param array $section
     * @return void
     */
    function _get_cache_config(&$config, $mime_types, $section) {
        $expires = $this->_config->get_boolean('browsercache.' . $section . '.expires');
        $lifetime = $this->_config->get_integer('browsercache.' . $section . '.lifetime');
        $cache_control = $this->_config->get_boolean('browsercache.' . $section . '.cache.control');
        $cache_policy = $this->_config->get_string('browsercache.' . $section . '.cache.policy');
        $etag = $this->_config->get_boolean('browsercache.' . $section . '.etag');
        $w3tc = $this->_config->get_boolean('browsercache.' . $section . '.w3tc');

        foreach ($mime_types as $mime_type) {
            $config[$mime_type] = array(
                'etag' => $etag,
                'w3tc' => $w3tc,
                'lifetime' => ($expires ? $lifetime : 0),
                'cache_control' => ($cache_control ? $cache_policy : false)
            );
        }
    }
}
