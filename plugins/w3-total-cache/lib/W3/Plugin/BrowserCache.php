<?php

/**
 * W3 ObjectCache plugin
 */
require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_BrowserCache
 */
class W3_Plugin_BrowserCache extends W3_Plugin
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
        
        if ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.html.w3tc')) {
            add_action('send_headers', array(
                &$this, 
                'send_headers'
            ));
        }
    }
    
    /**
     * Returns plugin instance
     *
     * @return W3_Plugin_BrowserCache
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
        if ($this->_config->get_boolean('browsercache.enabled') && !w3_is_multisite()) {
            $this->write_rules_cache();
            
            if ($this->_config->get_boolean('browsercache.no404wp')) {
                $this->write_rules_no404wp();
            }
        }
    }
    
    /**
     * Deactivate plugin action
     */
    function deactivate()
    {
        $this->remove_rules_no404wp();
        $this->remove_rules_cache();
    }
    
    /**
     * Send headers
     */
    function send_headers()
    {
        @header('X-Powered-By: ' . W3TC_POWERED_BY);
    }
    
    /**
     * Returns CSS/JS mime types
     * 
     * @return array
     */
    function get_cssjs_types()
    {
        $mime_types = include W3TC_DIR . '/inc/mime/cssjs.php';
        
        return $mime_types;
    }
    
    /**
     * Returns HTML mime types
     * 
     * @return array
     */
    function get_html_types()
    {
        $mime_types = include W3TC_DIR . '/inc/mime/html.php';
        
        return $mime_types;
    }
    
    /**
     * Returns other mime types
     * 
     * @return array
     */
    function get_other_types()
    {
        $mime_types = include W3TC_DIR . '/inc/mime/other.php';
        
        return $mime_types;
    }
    
    /**
     * Returns cache rules
     * 
     * @return string
     */
    function generate_rules_cache()
    {
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
        $rules .= "# BEGIN W3TC Browser Cache\n";
        
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
            //$rules .= "        SetEnvIfNoCase Request_URI \\.(jpg|jpe?g|gif|png|tiff?|as[fx]|wax|wm[vx]|avi|divx|mov|qt|mpe?g|mpg|mp[34]|m[34]a|ram?|ogg|wma|swf|gz|g?zip)$ no-gzip dont-vary\n";
            $rules .= "    </IfModule>\n";
            $rules .= "    <IfModule mod_headers.c>\n";
            $rules .= "        Header append Vary User-Agent env=!dont-vary\n";
            $rules .= "    </IfModule>\n";
            $rules .= "    AddOutputFilterByType DEFLATE " . implode(' ', $compression_types) . "\n";
            $rules .= "</IfModule>\n";
        }
        
        $this->_generate_rules_cache($rules, $cssjs_types, 'cssjs');
        $this->_generate_rules_cache($rules, $html_types, 'html');
        $this->_generate_rules_cache($rules, $other_types, 'other');
        
        $rules .= "# END W3TC Browser Cache\n\n";
        
        return $rules;
    }
    
    /**
     * Writes cache rules
     * @param string $rules
     * @param array $mime_types
     * @param string $section
     * @return void
     */
    function _generate_rules_cache(&$rules, $mime_types, $section)
    {
        $cache_control = $this->_config->get_boolean('browsercache.' . $section . '.cache.control');
        $cache_policy = $this->_config->get_string('browsercache.' . $section . '.cache.policy');
        $etag = $this->_config->get_boolean('browsercache.' . $section . '.etag');
        $w3tc = $this->_config->get_boolean('browsercache.' . $section . '.w3tc');
        
        $rules .= "<FilesMatch \"\\.(" . implode('|', array_keys($mime_types)) . ")$\">\n";
        
        if ($cache_control) {
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
     * Generate rules related to prevent for media 404 error by WP
     *
     * @return string
     */
    function generate_rules_no404wp()
    {
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
        $rules .= "# BEGIN W3TC Skip 404 error handling by WordPress for static files\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "    RewriteEngine On\n";
        
        if (count($exceptions)) {
            $rules .= "    RewriteCond %{REQUEST_URI} !(" . implode('|', $exceptions) . ")\n";
        }
        
        $rules .= "    RewriteCond %{REQUEST_FILENAME} !-f\n";
        $rules .= "    RewriteCond %{REQUEST_FILENAME} !-d\n";
        $rules .= "    RewriteCond %{REQUEST_FILENAME} \\.(" . implode('|', $extensions) . ")$ [NC]\n";
        $rules .= "    RewriteRule .* - [L]\n";
        $rules .= "</IfModule>\n";
        $rules .= "# END W3TC Skip 404 error handling by WordPress for static files\n\n";
        
        return $rules;
    }
    
    /**
     * Writes cache rules
     * 
     * @return boolean
     */
    function write_rules_cache()
    {
        $path = w3_get_document_root() . '/.htaccess';
        
        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_cache($data);
            } else {
                return false;
            }
        } else {
            $data = '';
        }
        
        $rules = $this->generate_rules_cache();
        
        $search = array(
            '# BEGIN W3TC Page Cache', 
            '# BEGIN WordPress'
        );
        
        foreach ($search as $string) {
            $rules_pos = strpos($data, $string);
            
            if ($rules_pos !== false) {
                break;
            }
        }
        
        if ($rules_pos !== false) {
            $data = trim(substr_replace($data, $rules, $rules_pos, 0));
        } else {
            $data = trim($rules . $data);
        
        }
        
        return @file_put_contents($path, $data);
    }
    
    /**
     * Writes no 404 by WP rules
     * 
     * @return boolean
     */
    function write_rules_no404wp()
    {
        $path = w3_get_home_root() . '/.htaccess';
        
        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_no404wp($data);
            } else {
                return false;
            }
        } else {
            $data = '';
        }
        
        $rules = $this->generate_rules_no404wp();
        
        $search = array(
            '# BEGIN W3TC Browser Cache', 
            '# BEGIN W3TC Page Cache', 
            '# BEGIN WordPress'
        );
        
        foreach ($search as $string) {
            $rules_pos = strpos($data, $string);
            
            if ($rules_pos !== false) {
                break;
            }
        }
        
        if ($rules_pos !== false) {
            $data = trim(substr_replace($data, $rules, $rules_pos, 0));
        } else {
            $data = trim($rules . $data);
        
        }
        
        return @file_put_contents($path, $data);
    }
    
    /**
     * Erases rules
     * 
     * @param string $data
     * @return string
     */
    function erase_rules_cache($data)
    {
        $data = w3_erase_text($data, '# BEGIN W3TC Browser Cache', '# END W3TC Browser Cache');
        
        return $data;
    }
    
    /**
     * Erases rules
     * 
     * @param string $data
     * @return string
     */
    function erase_rules_no404wp($data)
    {
        $data = w3_erase_text($data, '# BEGIN W3TC Skip 404 error handling by WordPress for static files', '# END W3TC Skip 404 error handling by WordPress for static files');
        
        return $data;
    }
    
    /**
     * Removes rules
     * 
     * @return boolean
     */
    function remove_rules_cache()
    {
        $path = w3_get_document_root() . '/.htaccess';
        
        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_cache($data);
                
                if ($data) {
                    return @file_put_contents($path, $data);
                } else {
                    @unlink($path);
                    
                    return true;
                }
            }
        } else {
            return true;
        }
        
        return false;
    }
    
    /**
     * Removes rules
     * 
     * @return boolean
     */
    function remove_rules_no404wp()
    {
        $path = w3_get_home_root() . '/.htaccess';
        
        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_no404wp($data);
                
                return @file_put_contents($path, $data);
            }
        } else {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check cache rules
     * 
     * @return boolean
     */
    function check_rules_cache()
    {
        $path = w3_get_document_root() . '/.htaccess';
        $search = $this->generate_rules_cache();
        
        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    
    }
    
    /**
     * Check 404 rules
     * 
     * @return boolean
     */
    function check_rules_no404wp()
    {
        $path = w3_get_home_root() . '/.htaccess';
        $search = $this->generate_rules_no404wp();
        
        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }
    
    /**
     * Returns cache config for CDN
     * 
     * @return array
     */
    function get_cache_config()
    {
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
    function _get_cache_config(&$config, $mime_types, $section)
    {
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
