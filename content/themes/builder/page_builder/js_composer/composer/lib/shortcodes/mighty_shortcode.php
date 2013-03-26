<?php
/**
 * Created by Wpbakery.com
 * User: time2type
 * Date: 10/12/12
 * mighty_shortcode.php
 *
 * New version of shortcode abstract class which will helps you to customize shortcode in
 * more flexible way
 * @package WpBakeryJsComposerDev
 */


define('VC_SHORTCODE_EXTENSION_PREFIX', 'vc_theme_');
define('VC_SHORTCODE_EXTANDABLE_CLASS_PREFIX', 'VCExtended_');

abstract class WPBakeryMightyShortCode extends WPBakeryVisualComposerAbstract {
    protected $shortcode;

    protected $atts, $settings;

    protected $content_tag = 'div';

    protected $extended_class;
    protected $extension_class_prefix = VC_SHORTCODE_EXTENSION_PREFIX;

    public function __construct($settings) {
        $this->settings = $settings;
        $this->shortcode = $this->settings['base'];
        $this->addShortCode($this->shortcode, Array($this, 'output'));
    }

    /**
     * Main tag of shortcode. Can be used to change it away.
     * @return string
     */

    public function contentTag() {
        return $this->content_tag;
    }

    public function contentTagCss() {
        return '';
    }

    /**
     * Content to show before shortcode html.
     * @return string
     */
    public function shortcodeBefore() {
        return '';
    }

    /**
     * Content to show inside content tag before content.
     * @return string
     */
    public function shortcodePrepend() {
        return '';
    }

    /**
     * Html inside content tag.
     * @return string
     */
    public function shortcodeMainContent() {
        return '';
    }
    /**
     * Content to show after shortcode html.
     * @return string
     */
    public function shortcodeAppend() {
        return '';
    }

    /**
     * Main content method
     * @return string
     */

    public function render() {
        return '';
    }

    final public function content() {
        $output = $this->_callContent('Before');
        $output .= '<'.$this->contentTag().' class="'.$this->contentTagCss().'">';
        $output .= $this->shortcodePrepend();
        $output .= $this->render();
        $output .= $this->shortcodeAppend();
        $output .= '</'.$this-contentTag().'>';
        $output .= $this->shortcodeAfter();
        return $output;
    }

    // @protected

    /**
     * This method searches for existing extend classes and function before using method inside
     * shortode class.
     * @return string
     */

    protected function _callContent($content_type) {
        $method_name = 'shortcode'.$content_type;
        if($this->_extentableClassMethodExists($method_name)) {
            return $this->extended_class->$method_name();
        } elseif(function_exists($this->extension_class_prefix.$this->shortcode.'_'.$content_type)) {
            $function = $this->extension_class_prefix.$this->shortcode.'_'.$content_type;
            return $function($this->atts, $this->settings);
        }
        return $this->$method_name();
    }

    protected function _extandableClassMethodExists($method_name) {
        if($this->extended_class==null) {
            $extended_class = VC_SHORTCODE_EXTANDABLE_CLASS_PREFIX.$this->shortcode;
            if(class_exists($extended_class)) {
                $this->extended_class = new $extended_class();
            } else {
                $this->extended_class = false;
            }
        }

        return ($this->extended_class!==false && method_exists($this->extended_class, $method_name));
    }

    public function output($atts, $content = null, $base = '') {
        $this->atts = $atts;
        $output = '';

        $content = empty($content) && !empty($atts['content']) ? $atts['content'] : $content;

        if( is_admin() ) $output .= $this->contentAdmin( $this->atts, $content );

        if( empty($output) ) {
            $custom_output = 'vc_theme_'.$this->shortcode;
            if(function_exists($custom_output)) {
                $output .= $custom_output($this->atts, $content);
            } else {
                $output .=  $this->content();
            }
        }

        return $output;
    }

    public function contentAdmin($atts, $content) {
        $element = $this->shortcode;
        $output = $custom_markup = $width = '';

        if ( $content != NULL ) { $content = wpautop(stripslashes($content)); }

        if ( isset($this->settings['params']) ) {
            $shortcode_attributes = array('width' => '1/1');
            foreach ( $this->settings['params'] as $param ) {
                if ( $param['param_name'] != 'content' ) {
                    //var_dump($param['value']);
                    if ( isset($param['value']) ) {
                        $shortcode_attributes[$param['param_name']] = is_string($param['value']) ? __($param['value'], "js_composer") : $param['value'];
                    } else {
                        $shortcode_attributes[$param['param_name']] = '';
                    }
                } else if ( $param['param_name'] == 'content' && $content == NULL ) {
                    $content = __($param['value'], "js_composer");
                }
            }
            extract(shortcode_atts(
                $shortcode_attributes
                , $atts));
            $elem = $this->getElementHolder($width);

            $iner = '';
            foreach ($this->settings['params'] as $param) {
                $param_value = $$param['param_name'];
                //var_dump($param_value);
                if ( is_array($param_value)) {
                    // Get first element from the array
                    reset($param_value);
                    $first_key = key($param_value);
                    $param_value = $param_value[$first_key];
                }
                $iner .= $this->singleParamHtmlHolder($param, $param_value);
            }
            $elem = str_ireplace('%wpb_element_content%', $iner, $elem);
            $output .= $elem;
        } else {
            //This is used for shortcodes without params (like simple divider)
            // $column_controls = $this->getColumnControls($this->settings['controls']);
            $width = '1/1';

            $elem = $this->getElementHolder($width);

            $iner = '';
            if ( isset($this->settings["custom_markup"]) && $this->settings["custom_markup"] != '' ) {
                if ( $content != '' ) {
                    $custom_markup = str_ireplace("%content%", $content, $this->settings["custom_markup"]);
                } else if ( $content == '' && isset($this->settings["default_content"]) && $this->settings["default_content"] != '' ) {
                    $custom_markup = str_ireplace("%content%", $this->settings["default_content"], $this->settings["custom_markup"]);
                }
                //$output .= do_shortcode($this->settings["custom_markup"]);
                $iner .= do_shortcode($custom_markup);
            }
            $elem = str_ireplace('%wpb_element_content%', $iner, $elem);
            $output .= $elem;
        }

        return $output;
    }

}

function wp_get_shortcode_helpers($shortcode) {
    $prefix = 'vc_theme_';
    $methods_list = array(
        'content_tag' => __('Main tag of shortcode.', 'js_composer'),
        'before' => __('Content will be shown before shortcode html.', 'js_composer'),
        'prepend' => __('', 'js_composer')
    );
}