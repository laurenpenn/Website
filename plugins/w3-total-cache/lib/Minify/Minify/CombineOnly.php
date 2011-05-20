<?php

/**
 * Combine only minifier
 */
class Minify_CombineOnly {
    /**
     * Minifies content
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function minify($content, $options = array()) {
        $browsercache_id = isset($options['browserCacheId']) ? $options['browserCacheId'] : 0;
        $browsercache_extensions = (isset($options['browserCacheExtensions']) ? $options['browserCacheExtensions'] : array());

        if (isset($options['currentDir'])) {
            require_once W3TC_LIB_MINIFY_DIR . '/Minify/CSS/UriRewriter.php';

            $document_root = isset($options['docRoot']) ? $options['docRoot'] : $_SERVER['DOCUMENT_ROOT'];
            $symlinks = isset($options['symlinks']) ? $options['symlinks'] : array();

            $content = Minify_CSS_UriRewriter::rewrite($content, $options['currentDir'], $document_root, $symlinks, $browsercache_id, $browsercache_extensions);
        } elseif (isset($options['prependRelativePath'])) {
            require_once W3TC_LIB_MINIFY_DIR . '/Minify/CSS/UriRewriter.php';

            $content = Minify_CSS_UriRewriter::prepend($content, $options['prependRelativePath'], $browsercache_id, $browsercache_extensions);
        }

        return $content;
    }
}
