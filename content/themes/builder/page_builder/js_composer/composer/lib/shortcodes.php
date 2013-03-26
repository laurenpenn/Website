<?php
/**
 * WPBakery Visual Composer Shortcodes main
 *
 * @package WPBakeryVisualComposer
 *
 */

/*
This is were shortcodes for default content elements are
defined. Each element should have shortcode for frontend
display (on a website).

This will add shortcode that will be used in frontend site
*/

define('VC_SHORTCODE_CUSTOMIZE_PREFIX', 'vc_theme_');
define('VC_SHORTCODE_BEFORE_CUSTOMIZE_PREFIX', 'vc_theme_before_');
define('VC_SHORTCODE_AFTER_CUSTOMIZE_PREFIX', 'vc_theme_after_');

abstract class WPBakeryShortCode extends WPBakeryVisualComposerAbstract {

    protected $shortcode;

    protected $atts, $settings;
    protected static $enqueue_index = 0;
    protected static $js_scripts = array();
    protected static $css_scripts = array();

    public function __construct($settings) {
        $this->settings = $settings;
        $this->shortcode = $this->settings['base'];

        $this->addAction('admin_init', 'enqueueAssets');

        $this->addShortCode($this->shortcode, Array($this, 'output'));
    }

    public function enqueueAssets() {
        if(!empty($this->settings['admin_enqueue_js'])) $this->registerJs($this->settings['admin_enqueue_js']);
        if(!empty($this->settings['admin_enqueue_css'])) $this->registerCss($this->settings['admin_enqueue_css']);
    }

    protected function registerJs($param) {
        if(is_array($param)) {
            foreach($param as $value) {
                $this->registerJs($value);
            }
        } elseif(is_string($param) && !empty($param)) {
            $name = $this->shortcode.'_enqueue_js_'.self::$enqueue_index++;
            self::$js_scripts[] = $name;
            wp_register_script( $name, $param,  array( 'jquery' ), time(), true);
        }
    }

    protected function registerCss($param) {
        if(is_array($param)) {
            foreach($param as $value) {
                $this->registerCss($value);
            }
        } elseif(is_string($param)) {
            $name = $this->shortcode.'_enqueue_css_'.self::$enqueue_index++;
            self::$css_scripts[] = $name;
            wp_register_style( $name, $param,  array( 'js_composer' ), time());
        }
    }

    public static function enqueueCss() {
        foreach(self::$css_scripts as $stylesheet) {
            wp_enqueue_style($stylesheet);
        }
    }
    public static function enqueueJs() {
        foreach(self::$js_scripts as $script) {
            wp_enqueue_script($script);
        }
    }
    public function shortcode($shortcode) {

    }

    abstract protected function content( $atts, $content = null );

    public function contentAdmin($atts, $content) {
        $element = $this->shortcode;
        $output = $custom_markup = $width = $el_position = '';

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
            if(isset($atts['el_position'])) $el_position = $atts['el_position'];
            $iner = $this->outputTitle($this->settings['name']);
            foreach ($this->settings['params'] as $param) {
                $param_value = $$param['param_name'];
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

            $inner = '';
            if ( isset($this->settings["custom_markup"]) && $this->settings["custom_markup"] != '' ) {
                if ( $content != '' ) {
                    $custom_markup = str_ireplace("%content%", $content, $this->settings["custom_markup"]);
                } else if ( $content == '' && isset($this->settings["default_content"]) && $this->settings["default_content"] != '' ) {
                    $custom_markup = str_ireplace("%content%", $this->settings["default_content"], $this->settings["custom_markup"]);
                }
                //$output .= do_shortcode($this->settings["custom_markup"]);
                $inner .= do_shortcode($custom_markup);
            }
            $elem = str_ireplace('%wpb_element_content%', $inner, $elem);
            $output .= $elem;
        }
        return $output;
    }
    public function isAdmin() {
        return is_admin() && !empty($_POST['action']) && preg_match('/^wpb\_/', $_POST['action']);
    }

    public function output($atts, $content = null, $base = '') {
        $this->atts = $atts;
        $output = '';

        $content = empty($content) && !empty($atts['content']) ? $atts['content'] : $content;

        if( $this->isAdmin() ) $output .= $this->contentAdmin( $this->atts, $content );

        if( empty($output) ) {
            $custom_output = VC_SHORTCODE_CUSTOMIZE_PREFIX.$this->shortcode;
            $custom_output_before = VC_SHORTCODE_BEFORE_CUSTOMIZE_PREFIX.$this->shortcode; // before shortcode function hook
            $custom_output_after = VC_SHORTCODE_AFTER_CUSTOMIZE_PREFIX.$this->shortcode; // after shortcode function hook

            // Before shortcode
            if(function_exists($custom_output_before)) {
                $output .= $custom_output_before($this->atts, $content);
            } else {
                $output .=  $this->beforeShortcode( $this->atts, $content );
            }

            // Shortcode content
            if(function_exists($custom_output)) {
                $output .= $custom_output($this->atts, $content);
            } else {
                $output .=  $this->content( $this->atts, $content );
            }
            // After shortcode
            if(function_exists($custom_output_after)) {
                $output .= $custom_output_after($this->atts, $content);
            } else {
                $output .=  $this->afterShortcode( $this->atts, $content );
            }
        }
        return $output;
    }

    /**
     * Creates html before shortcode html.
     * @param $atts - shortcode attributes list
     * @param $content - shortcode content
     * @return string - html which will be displayed before shortcode html.
     */
    public function beforeShortcode($atts, $content) {
        return '';
    }

    /**
     * Creates html before shortcode html.
     * @param $atts - shortcode attributes list
     * @param $content - shortcode content
     * @return string - html which will be displayed after shortcode html.
     */
    public function afterShortcode($atts, $content) {
        return '';
    }

    public function getExtraClass($el_class) {
        $output = '';
        if ( $el_class != '' ) {
            $output = " " . str_replace(".", "", $el_class);
        }
        return $output;
    }

    /**
     * Create HTML comment for blocks
     *
     * @param $string
     *
     * @return string
     */

    public function endBlockComment($string) {
        //return '';
        return ( !empty($_GET['wpb_debug']) && $_GET['wpb_debug']=='true' ? '<!-- END '.$string.' -->' : '' );
    }

    /**
     * Start row comment for html shortcode block
     *
     * @param $position - block position
     * @return string
     */

    public function startRow($position) {
        $output = '';
        return '';
        if ( strpos($position, 'first') !== false ) {
            $output = ( !empty($_GET['wpb_debug']) && $_GET['wpb_debug']=='true' ? "\n" . '<!-- START row -->' ."\n" : '' ) . '<div class="row-fluid">';
        }
        return $output;
    }

    /**
     * End row comment for html shortcode block
     *
     * @param $position -block position
     * @return string
     */

    public function endRow($position) {
        $output = '';
        return '';
        if ( strpos($position, 'last') !== false ) {
            $output = '</div>'. ( !empty($_GET['wpb_debug']) &&  $_GET['wpb_debug']=='true' ? "\n" .  '<!-- END row --> ' . "\n" : '' );
        }
        return $output;
    }

    public function settings($name) {
        return isset($this->settings[$name]) ? $this->settings[$name] : null;
    }
    public function getElementHolder($width) {

        $output = '';
        $column_controls = $this->getColumnControls($this->settings('controls'));

        $output .= '<div data-element_type="'.$this->settings["base"].'" class="wpb_'.$this->settings["base"].' wpb_content_element wpb_sortable '.wpb_translateColumnWidthToSpan($width).' '.$this->settings["class"].'">';
        $output .= '<input type="hidden" class="wpb_vc_sc_base" name="element_name-'.$this->shortcode.'" value="'.$this->settings["base"].'" />';
        $output .= str_replace("%column_size%", wpb_translateColumnWidthToFractional($width), $column_controls);
        $output .= $this->getCallbacks($this->shortcode);
        $output .= '<div class="wpb_element_wrapper '.$this->settings("wrapper_class").'">';

        $output .= '%wpb_element_content%';

        $output .= '</div>'; // <!-- end .wpb_element_wrapper -->';
        $output .= '</div>'; // <!-- end #element-'.$this->shortcode.' -->';

        return $output;
    }

     /* This returs block controls
---------------------------------------------------------- */
    public function getColumnControls($controls, $extended_css = '') {
        $controls_start = '<div class="controls'.(!empty($extended_css) ? " {$extended_css}" : '').'">';
        $controls_end = '</div>';

        $right_part_start = '<div class="controls_right">';
        $right_part_end = '</div>';
        
        //<div class="column_size_wrapper"> 
        $controls_column_size = '<a class="column_decrease controls-type-'.$controls.'" href="#" title="'.__('Decrease width', 'js_composer').'"></a> <span class="column_size">%column_size%</span> <a class="column_increase" href="#" title="'.__('Increase width', 'js_composer').'"></a>'; //</div>
        $controls_add = ' <a class="column_add controls-type-'.$controls.'" href="#" title="'.__('Add to '.$this->settings('name'), 'js_composer').'"></a>';

        $controls_edit = ' <a class="column_edit controls-type-'.$controls.'" href="#" title="'.__('Edit '.$this->settings('name'), 'js_composer').'"></a>';
        $controls_popup = ' <a class="column_popup controls-type-'.$controls.'" href="#" title="'.__('Pop up '.$this->settings('name'), 'js_composer').'"></a>';
        $controls_delete = ' <a class="column_clone controls-type-'.$controls.'" href="#" title="'.__('Clone '.$this->settings('name'), 'js_composer').'"></a> <a class="column_delete" href="#" title="'.__('Delete' .$this->settings('name'), 'js_composer').'"></a>';
        // $delete_edit_row = '<a class="row_delete" title="'.__('Delete %element%', "js_composer").'">'.__('Delete %element%', "js_composer").'</a>';

        $column_controls_full = $controls_start .  $right_part_start . $controls_add . $controls_popup . $controls_edit . $controls_delete . $right_part_end . $controls_end;

        $column_controls_size_delete = $controls_start .  $right_part_start . $controls_delete . $right_part_end . $controls_end;
        $column_controls_popup_delete = $controls_start . $right_part_start . $controls_popup . $controls_delete . $right_part_end . $controls_end;
        $column_controls_delete = $controls_start . $right_part_start . $controls_delete . $right_part_end . $controls_end;
        $column_controls_edit_popup_delete = $controls_start . $right_part_start . $controls_popup . $controls_edit . $controls_delete . $right_part_end . $controls_end;

        if ( $controls == 'popup_delete' ) {
            return $column_controls_popup_delete;
        } else if ( $controls == 'edit_popup_delete' ) {
            return $column_controls_edit_popup_delete;
        } else if ( $controls == 'size_delete' ) {
            return $column_controls_size_delete;
        } else if ( $controls == 'popup_delete' ) {
            return $column_controls_popup_delete;
        } else if ($controls == 'add') {
            return $controls_start . $controls_column_size . $right_part_start . $controls_add . $right_part_end . $controls_end;
        } else {
            return $column_controls_full;
        }
    }

    /* This will fire callbacks if they are defined in map.php
   ---------------------------------------------------------- */
    public function getCallbacks($id) {
        $output = '';

        if (isset($this->settings['js_callback'])) {
            foreach ($this->settings['js_callback'] as $text_val => $val) {
                /* TODO: name explain */
                $output .= '<input type="hidden" class="wpb_vc_callback wpb_vc_'.$text_val.'_callback " name="'.$text_val.'" value="'.$val.'" />';
            }
        }

        return $output;
    }

    public function singleParamHtmlHolder($param, $value) {
        $output = '';
            // Compatibility fixes
        $old_names = array('yellow_message', 'blue_message', 'green_message', 'button_green', 'button_grey', 'button_yellow', 'button_blue', 'button_red', 'button_orange');
        $new_names = array('alert-block', 'alert-info', 'alert-success', 'btn-success', 'btn', 'btn-info', 'btn-primary', 'btn-danger', 'btn-warning');
        $value = str_ireplace($old_names, $new_names, $value);
            //$value = __($value, "js_composer");
            //
        $param_name = isset($param['param_name']) ? $param['param_name'] : '';
        $type = isset($param['type']) ? $param['type'] : '';
        $class = isset($param['class']) ? $param['class'] : '';

        if ( isset($param['holder']) == false || $param['holder'] == 'hidden' ) {
        $output .= '<input type="hidden" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="'.$value.'" />';
        } elseif($param['holder'] == 'input') {
            $output .= '<'.$param['holder'].' readonly="true" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="'.$value.'">';
        } elseif(in_array($param['holder'], array('img', 'iframe'))) {
            $output .= '<'.$param['holder'].' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" src="'.$value.'">';
        }else {
            $output .= '<'.$param['holder'].' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">'.$value.'</'.$param['holder'].'>';
        }

        if(isset($param['admin_label']) && $param['admin_label'] === true) {
            $output .= '<span class="vc_admin_label admin_label_'.$param['param_name'].(empty($value) ? ' hidden-label' : '').'"><label>'.$param['heading'].'</label>: '.$value.'</span>';
        }

        return $output;
    }

    protected function outputTitle($title) {
        return  '<h4 class="wpb_element_title">'.$title.'</h4>';
    }
}

abstract class WPBakeryShortCode_UniversalAdmin extends WPBakeryShortCode {
    public function __construct($settings) {
        $this->settings = $settings;
        $this->addShortCode($this->settings['base'], Array($this, 'output'));
    }
    protected  function content( $atts, $content = null) {
        return '';
    }
    public function contentAdmin($atts,  $content) {

        $element = $this->settings['base'];
        $output = '';
        $this->loadParams();
        //if ( $content != NULL ) { $content = apply_filters('the_content', $content); }
        $content = $el_position = '';
        if ( isset($this->settings['params']) ) {
            $shortcode_attributes = array();
            foreach ( $this->settings['params'] as $param ) {
                if ( $param['param_name'] != 'content' ) {
                    $shortcode_attributes[$param['param_name']] = $param['value'];
                } else if ( $param['param_name'] == 'content' && $content == NULL ) {
                    $content = $param['value'];
                }
            }
            extract(shortcode_atts(
                $shortcode_attributes
                , $atts));

            $output .= '<div class="span12 wpb_edit_form_elements"><h2>'.__('Edit', 'js_composer').' ' .__($this->settings['name'], "js_composer").'</h2>';

            foreach ($this->settings['params'] as $param) {
                $param_value = '';
                $param_value = $$param['param_name'];

                if ( is_array($param_value)) {
                    // Get first element from the array
                    reset($param_value);
                    $first_key = key($param_value);
                    $param_value = $param_value[$first_key];
                }
                $output .= $this->singleParamEditHolder($param, $param_value);
            }

            $output .= '<div class="edit_form_actions"><a href="#" class="wpb_save_edit_form button-primary">'. __('Save', "js_composer") .'</a></div>';

            $output .= '</div>'; //close wpb_edit_form_elements
        }
    }

    protected function singleParamEditHolder($param, $param_value) {
        /**
         * @var $dependency - setup dependency settings for this field
         */
        $dependency = '';
        if( !empty($param['dependency']) && isset($param['dependency']['element']) ) {
            $dependency = ' data-dependency="' . $param['dependency']['element'] . '"';

            if(isset($param['dependency']['not_empty']) && $param['dependency']['not_empty']===true) {
                $dependency .= ' data-dependency-not-empty="true"';
            }

            if(isset($param['dependency']['value'])) {
                if(is_array($param['dependency']['value'])) {
                    foreach($param['dependency']['value'] as $val) {
                        $dependency .= ' data-dependency-value-'.$val.'="' . $val . '"';
                    }

                } else {
                    $dependency .= ' data-dependency-value-'.$param['dependency']['value'].'="' . $param['dependency']['value'] . '"';
                }
            }
            if(isset($param['dependency']['callback'])) {
                 $dependency .= ' data-dependency-callback="' . $param['dependency']['callback'] . '"';
            }
        }
        $output = '<div class="row-fluid wpb_el_type_'. $param['type'] .'"' . $dependency . '>';
        $output .= '<div class="wpb_element_label">'.__($param['heading'], "js_composer").'</div>';//span3

        $output .= '<div class="edit_form_line">';//span9
        $output .= $this->singleParamEditForm($param, $param_value);
        $output .= (isset($param['description'])) ? '<span class="description clear">'.__($param['description'], "js_composer").'</span>' : '';
        $output .= '</div>';

        $output .= '</div>';

        return $output;
    }

    protected function singleParamEditForm($param, $param_value) {
        $param_line = '';
        $dependency = '';
        if( !empty($param['dependency']) && isset($param['dependency']['element']) ) {
            $dependency = ' data-dependency-element="true"';
        }
        // Textfield - input
        if ( $param['type'] == 'textfield' ) {
            $value = __($param_value, "js_composer");
            $value = $param_value;
            $param_line .= '<input name="'.$param['param_name'].'" class="wpb_vc_param_value wpb-textinput '.$param['param_name'].' '.$param['type'].'" type="text" value="'.$value.'" ' . $dependency . '/>';
        }

        // Dropdown - select
        else if ( $param['type'] == 'dropdown' ) {
            $param_line .= '<select name="'.$param['param_name'].'" class="wpb_vc_param_value wpb-input wpb-select '.$param['param_name'].' '.$param['type'].'"' . $dependency . '>';

            foreach ( $param['value'] as $text_val => $val ) {
                if ( is_numeric($text_val) && is_string($val) || is_numeric($text_val) && is_numeric($val) ) {
                    $text_val = $val;
                }
                $text_val = __($text_val, "js_composer");
                $val = strtolower(str_replace(array(" "), array("_"), $val));
                $selected = '';
                if ( $val == $param_value ) $selected = ' selected="selected"';
                $param_line .= '<option class="'.$val.'" value="'.$val.'"'.$selected.'>'.$text_val.'</option>';
            }
            $param_line .= '</select>';
        }
        // WYSIWYG field
        else if ( $param['type'] == 'textarea_html' ) {
           $param_line .= do_shortcode_param_settings_field('textarea_html', $param, $param_value);
           // $param_line .= $this->getTinyHtmlTextArea($param, $param_value, $dependency);
        }
        // Checkboxes with post types
        else if ( $param['type'] == 'posttypes' ) {
            $param_line .= '<input class="wpb_vc_param_value wpb-checkboxes" type="hidden" value="" name="" ' . $dependency . '/>';
            $args = array(
                'public'   => true
            );
            $post_types = get_post_types($args);
            foreach ( $post_types as $post_type ) {
                $checked = "";
                if ( $post_type != 'attachment' ) {
                    if ( in_array($post_type, explode(",", $param_value)) ) $checked = ' checked="checked"';
                    $param_line .= ' <input id="'. $param['param_name'] . '-' . $post_type .'" value="' . $post_type . '" class="'.$param['param_name'].' '.$param['type'].'" type="checkbox" name="'.$param['param_name'].'"'.$checked.'' . $dependency . '> ' . $post_type;
                }
            }
        }
        else if ( $param['type'] == 'taxomonies' ) {
            $param_line .= '<input class="wpb_vc_param_value wpb-checkboxes" type="hidden" value="" name="" ' . $dependency . '/>';
            $post_types = get_post_types(array('public' => false, 'name' => 'attachment'), 'names', 'NOT');
            foreach($post_types as $type) {
                $taxonomies = get_object_taxonomies($type , '');

                foreach ( $taxonomies as $tax ) {
                    $checked = "";
                    if ( in_array($tax->name, explode(",", $param_value)) ) $checked = ' checked="checked"';
                    $param_line .= ' <label data-post-type="' . $type . '"><input id="'. $param['param_name'] . '-' . $tax->name .'" value="' . $tax->name . '" data-post-type="' . $type . '" class="'.$param['param_name'].' '.$param['type'].'" type="checkbox" name="'.$param['param_name'].'"'.$checked.'' . $dependency . '> ' . $tax->label. '</label>';
                }
            }
        }
        // Exploded textarea
        else if ( $param['type'] == 'exploded_textarea' ) {
            $param_value = str_replace(",", "\n", $param_value);
            $param_line .= '<textarea name="'.$param['param_name'].'" class="wpb_vc_param_value wpb-textarea '.$param['param_name'].' '.$param['type'].'"' . $dependency . '>'.$param_value.'</textarea>';
        }
        // Big Regular textarea
        else if ( $param['type'] == 'textarea_raw_html' ) {
            // $param_value = __($param_value, "js_composer");
            $param_line .= '<textarea name="'.$param['param_name'].'" class="wpb_vc_param_value wpb-textarea_raw_html '.$param['param_name'].' '.$param['type'].'" rows="16"' . $dependency . '>' . htmlentities(rawurldecode(base64_decode($param_value)), ENT_COMPAT, 'UTF-8' ) . '</textarea>';
        }
        // Regular textarea
        else if ( $param['type'] == 'textarea' ) {
            $param_value = __($param_value, "js_composer");
            $param_line .= '<textarea name="'.$param['param_name'].'" class="wpb_vc_param_value wpb-textarea '.$param['param_name'].' '.$param['type'].'"' . $dependency . '>'.$param_value.'</textarea>';
        }
        // Attach images
        else if ( $param['type'] == 'attach_images' ) {
            // TODO: More native way
            $param_value = wpb_removeNotExistingImgIDs($param_value);
            $param_line .= '<input type="hidden" class="wpb_vc_param_value gallery_widget_attached_images_ids '.$param['param_name'].' '.$param['type'].'" name="'.$param['param_name'].'" value="'.$param_value.'" ' . $dependency . '/>';
            //$param_line .= '<a class="button gallery_widget_add_images" href="#" title="'.__('Add images', "js_composer").'">'.__('Add images', "js_composer").'</a>';
            $param_line .= '<div class="gallery_widget_attached_images">';
            $param_line .= '<ul class="gallery_widget_attached_images_list">';
            $param_line .= ($param_value != '') ? fieldAttachedImages(explode(",", $param_value)) : '';
            $param_line .= '</ul>';
            $param_line .= '</div>';
            $param_line .= '<div class="gallery_widget_site_images">';
            // $param_line .= siteAttachedImages(explode(",", $param_value));
            $param_line .= '</div>';
            $param_line .= '<a class="gallery_widget_add_images" href="#" title="'.__('Add images', "js_composer").'">'.__('Add images', "js_composer").'</a>';//class: button
            //$param_line .= '<div class="wpb_clear"></div>';
        }
		else if ( $param['type'] == 'attach_image' ) {
			$param_value = wpb_removeNotExistingImgIDs(preg_replace('/[^\d]/', '', $param_value));
			$param_line .= '<input type="hidden" class="wpb_vc_param_value gallery_widget_attached_images_ids '.$param['param_name'].' '.$param['type'].'" name="'.$param['param_name'].'" value="'.$param_value.'" ' . $dependency . '/>';
			//$param_line .= '<a class="button gallery_widget_add_images" href="#" use-single="true" title="'.__('Add image', "js_composer").'">'.__('Add image', "js_composer").'</a>';
			$param_line .= '<div class="gallery_widget_attached_images">';
			$param_line .= '<ul class="gallery_widget_attached_images_list">';
			$param_line .= ($param_value != '') ? fieldAttachedImages(explode(",", $param_value)) : '';
			$param_line .= '</ul>';
			$param_line .= '</div>';
			$param_line .= '<div class="gallery_widget_site_images">';
			// $param_line .= siteAttachedImages(explode(",", $param_value));
			$param_line .= '</div>';
			$param_line .= '<a class="gallery_widget_add_images" href="#" use-single="true" title="'.__('Add image', "js_composer").'">'.__('Add image', "js_composer").'</a>';//class: button
			//$param_line .= '<div class="wpb_clear"></div>';
		}       //
        else if ( $param['type'] == 'widgetised_sidebars' ) {
            $wpb_sidebar_ids = Array();
            $sidebars = $GLOBALS['wp_registered_sidebars'];

            $param_line .= '<select name="'.$param['param_name'].'" class="wpb_vc_param_value dropdown wpb-input wpb-select '.$param['param_name'].' '.$param['type'].'"' . $dependency . '>';
            foreach ( $sidebars as $sidebar ) {
                $selected = '';
                if ( $sidebar["id"] == $param_value ) $selected = ' selected="selected"';
                $sidebar_name = __($sidebar["name"], "js_composer");
                $param_line .= '<option value="'.$sidebar["id"].'"'.$selected.'>'.$sidebar_name.'</option>';
            }
            $param_line .= '</select>';
        } else {
            $param_line .= do_shortcode_param_settings_field($param['type'], $param, $param_value);
        }


        return $param_line;
    }

    protected function getTinyHtmlTextArea($param = array(), $param_value, $dependency = '') {
        $param_line = '';

        //$upload_media_btns = '<div class="wpb_media-buttons hide-if-no-js"> '.__('Upload/Insert').' <a title="'.__('Add an Image').'" class="wpb_insert-image" href="#"><img alt="'.__('Add an Image').'" src="'.home_url().'/wp-admin/images/media-button-image.gif"></a> <a class="wpb_switch-editors" title="'.__('Switch Editors').'" href="#">HTML mode</a></div>';

                if ( function_exists('wp_editor') ) {
                    $default_content = __($param_value, "js_composer");
                    $output_value = '';
                    // WP 3.3+
                    ob_start();
                    wp_editor($default_content, 'wpb_tinymce_'.$param['param_name'], array('editor_class' => 'wpb_vc_param_value wpb-textarea visual_composer_tinymce '.$param['param_name'].' '.$param['type'], 'media_buttons' => true ) );
                    $output_value = ob_get_contents();
                    ob_end_clean();
                    $param_line .= $output_value;
                }


        // $param_line = '<textarea name="'.$param['param_name'].'" class="wpb_vc_param_value wpb-textarea visual_composer_tinymce '.$param['param_name'].' '.$param['type'].' '.$param['param_name'].'_tinymce"' . $dependency . '>'.$param_value.'</textarea>';
        return $param_line;
    }
}


class WPBakeryShortCode_Settings extends WPBakeryShortCode_UniversalAdmin {

    public function content( $atts, $content = null ) {
        return '';
    }

    public function contentAdmin($atts, $content) {
        $this->loadDefaultParams();
        $output = $el_position = '';

        //if ( $content != NULL ) { $content = apply_filters('the_content', $content); }
        if ( isset($this->settings['params']) ) {
            $shortcode_attributes = array();
            foreach ( $this->settings['params'] as $param ) {
                if ( $param['param_name'] != 'content' ) {
                    $shortcode_attributes[$param['param_name']] = isset($param['value']) ? $param['value'] : null;
                } else if ( $param['param_name'] == 'content' && $content == NULL ) {
                    $content = $param['value'];
                }
            }
            extract(shortcode_atts(
                $shortcode_attributes
                , $atts));

            $output .= '<div class="span12 wpb_edit_form_elements"><h2>'.__('Edit', 'js_composer').' ' .__($this->settings['name'], "js_composer").'</h2>';

            foreach ($this->settings['params'] as $param) {
                $param_value = '';
                $param_value = $$param['param_name'];
                if ( is_array($param_value)) {
                    // Get first element from the array
                    reset($param_value);
                    $first_key = key($param_value);
                    $param_value = $param_value[$first_key];
                }
                $output .= $this->singleParamEditHolder($param, $param_value);
                if($param['param_name'] == 'el_position') $el_position = $param_value;

            }

            $output .= '<div class="edit_form_actions"><a href="#" class="wpb_save_edit_form button-primary">'. __('Save', "js_composer") .'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" id="cancel-background-options">' . __('Cancel', 'js_composer') . '</a></div>';

            $output .= '</div>'; //close wpb_edit_form_elements

            foreach(WpbakeryShortcodeParams::getScripts() as $script) {
                $output .= "\n\n".'<script type="text/javascripts" src="'.$script.'"></script>';
            }

        }

        return $output;
    }

    public function loadDefaultParams() {
        global $vc_params_list;
        if(empty($vc_params_list)) return false;
        $script_url = WPBakeryVisualComposer::getInstance()->assetURL('params/all.js');
        foreach($vc_params_list as $param) {
            add_shortcode_param($param, 'vc_'.$param.'_form_field', $script_url);
        }
    }
}


class WPBakeryShortCodeFishBones extends WPBakeryShortCode {

    public function __construct($settings) {
        $this->settings = $settings;
        $this->shortcode = $this->settings['base'];
        $this->addAction('admin_print_scripts-post.php', 'enqueueAssets');
        $this->addAction('admin_print_scripts-post-new.php', 'enqueueAssets');
        if($this->isAdmin()) {
            $this->removeShortCode($this->shortcode);
            $this->addShortCode($this->shortcode, Array($this, 'output'));
        }
    }

    protected function content( $atts, $content = null ) {
        return ''; // this method is not used
    }
}