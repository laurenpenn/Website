<?php

class Minify_CSSTidy {
    public static function minify($css, $options = array()) {
        $options = array_merge(array(
            'remove_bslash' => true,
            'compress_colors' => true,
            'compress_font-weight' => true,
            'lowercase_s' => false,
            'optimise_shorthands' => 1,
            'remove_last_;' => false,
            'case_properties' => 1,
            'sort_properties' => false,
            'sort_selectors' => false,
            'merge_selectors' => 2,
            'discard_invalid_properties' => false,
            'css_level' => 'CSS2.1',
            'preserve_css' => false,
            'timestamp' => false,
            'template' => 'default'
        ), $options);

        set_include_path(get_include_path() . PATH_SEPARATOR . W3TC_LIB_CSSTIDY_DIR);

        require_once 'class.csstidy.php';

        $csstidy = new csstidy();

        foreach ($options as $option => $value) {
            $csstidy->set_cfg($option, $value);
        }

        $csstidy->load_template($options['template']);
        $csstidy->parse($css);

        $css = $csstidy->print->plain();

        if (isset($options['currentDir']) || isset($options['prependRelativePath'])) {
            require_once W3TC_LIB_MINIFY_DIR . '/Minify/CSS/UriRewriter.php';

            $browsercache_id = (isset($options['browserCacheId']) ? $options['browserCacheId'] : 0);
            $browsercache_extensions = (isset($options['browserCacheExtensions']) ? $options['browserCacheExtensions'] : array());

            if (isset($options['currentDir'])) {
                $document_root = (isset($options['docRoot']) ? $options['docRoot'] : $_SERVER['DOCUMENT_ROOT']);
                $symlinks = (isset($options['symlinks']) ? $options['symlinks'] : array());

                return Minify_CSS_UriRewriter::rewrite($css, $options['currentDir'], $document_root, $symlinks, $browsercache_id, $browsercache_extensions);
            } else {
                return Minify_CSS_UriRewriter::prepend($css, $options['prependRelativePath'], $browsercache_id, $browsercache_extensions);
            }
        }

        return $css;
    }
}
