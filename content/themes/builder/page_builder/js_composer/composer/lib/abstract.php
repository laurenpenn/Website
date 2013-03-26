<?php
/**
 * WPBakery Visual Composer Abstract classes
 *
 * @package WPBakeryVisualComposer
 *
 */

/* abstract VisualComposer class to create structural object of any type */

abstract class WPBakeryVisualComposerAbstract {

    public static $config;
    protected $is_plugin = true;
    protected $is_theme = false;
    public function __construct() {
    }

    public function init( $settings ) {
        self::$config = (array)$settings;
    }

    public function addAction($action, $method, $priority = 10) {
        add_action($action, array(&$this, $method), $priority);
    }

    public function addFilter($filter, $method, $priority = 10) {
        add_filter($filter, array(&$this, $method), $priority);
    }
    /* Shortcode methods */
    public function addShortCode($tag, $func) {
        add_shortcode($tag,$func);
    }

    public function doShortCode($content) {
        do_shortcode($content);
    }

    public function removeShortCode($tag) {
        remove_shortcode($tag);
    }

    public function post($param) {
        return isset($_POST[$param]) ? $_POST[$param] : null;
    }

    public function get($param) {
        return isset($_GET[$param]) ? $_GET[$param] : null;
    }
    public function assetURL($asset) {
        return $this->is_plugin ? plugins_url( preg_replace('/\s/', '%20', self::$config['APP_DIR'] . self::$config['ASSETS_DIR'] . $asset), preg_replace('/\s/', '%20', self::$config['APP_DIR']) ) : get_template_directory_uri() .'/'. self::$config['APP_DIR'] . self::$config['ASSETS_DIR'] . $asset;
    }

    public static function config($name) {
        return isset(self::$config[$name]) ? self::$config[$name] : null;
    }
}

interface WPBakeryVisualComposerTemplateInterface
{
    public function output($post = null);
}