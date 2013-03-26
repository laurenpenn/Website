<?php
/**
 * WPBakery Visual Composer shortcodes
 *
 * @package WPBakeryVisualComposer
 *
 */

class WPBakeryShortCode_VC_Accordion_tab extends WPBakeryShortCode_VC_Tab {
    protected  $predefined_atts = array(
        'el_class' => '',
        'width' => '',
        'title' => ''
    );

    public function content( $atts, $content = null ) {
        $title = '';

        extract(shortcode_atts(array(
            'title' => __("Section", "js_composer")
        ), $atts));

        $output = '';

        $output .= "\n\t\t\t" . '<div class="wpb_accordion_section group">';
        $output .= "\n\t\t\t\t" . '<h3 class="ui-accordion-header"><a href="#">'.$title.'</a></h3>';
        //$output .= "\n\t\t\t\t" . '<div><div class="row-fluid">';
        $output .= "\n\t\t\t\t" . '<div class="wpb_row vc_row-fluid ui-accordion-content">';
        $output .= ($content=='' || $content==' ') ? __("Empty section. Edit page to add content here.", "js_composer") : "\n\t\t\t\t" . wpb_js_remove_wpautop($content);
        $output .= "\n\t\t\t\t" . '</div>';
        //$output .= "\n\t\t\t\t" . '</div></div>';
        $output .= "\n\t\t\t" . '</div> ' . $this->endBlockComment('.wpb_accordion_section') . "\n";

        return $output;
    }
/*
    public function contentAdmin( $atts, $content = null ) {
        $title = '';
        $defaults = array( 'title' => __('Section', 'js_composer') );
        extract( shortcode_atts( $defaults, $atts ) );

        return '<div class="group">
		<h3><a href="#">'.$title.'</a></h3>
		<div>
			<div class="row-fluid wpb_column_container not-column-inherit wpb_sortable_container">
				'. do_shortcode($content) . WPBakeryVisualComposer::getInstance()->getLayout()->getContainerHelper() . '
			</div>
		</div>
	    </div>';
    }
*/

    public function contentAdmin($atts, $content = null) {
        $width = $el_class = $title = '';
        extract(shortcode_atts($this->predefined_atts, $atts));
        $output = '';

        $column_controls = $this->getColumnControls($this->settings('controls'));
        $column_controls_bottom =  $this->getColumnControls('add', 'bottom-controls');

        if ( $width == 'column_14' || $width == '1/4' ) {
            $width = array('span3');
        }
        else if ( $width == 'column_14-14-14-14' ) {
            $width = array('span3', 'span3', 'span3', 'span3');
        }

        else if ( $width == 'column_13' || $width == '1/3' ) {
            $width = array('span4');
        }
        else if ( $width == 'column_13-23' ) {
            $width = array('span4', 'span8');
        }
        else if ( $width == 'column_13-13-13' ) {
            $width = array('span4', 'span4', 'span4');
        }

        else if ( $width == 'column_12' || $width == '1/2' ) {
            $width = array('span6');
        }
        else if ( $width == 'column_12-12' ) {
            $width = array('span6', 'span6');
        }

        else if ( $width == 'column_23' || $width == '2/3' ) {
            $width = array('span8');
        }
        else if ( $width == 'column_34' || $width == '3/4' ) {
            $width = array('span9');
        }
        else if ( $width == 'column_16' || $width == '1/6' ) {
            $width = array('span2');
        }
        else {
            $width = array('');
        }


        for ( $i=0; $i < count($width); $i++ ) {
            $output .= '<div class="group wpb_sortable">';
            $output .= '<div class="wpb_element_wrapper">';
            $output .= '<div class="row-fluid wpb_row_container not-row-inherit">';
            $output .= '<h3><a href="#">'.$title.'</a></h3>';
            $output .= '<div '.$this->mainHtmlBlockParams($width, $i).'>';
            $output .= '<input type="hidden" class="wpb_vc_sc_base" name="" value="'.$this->settings['base'].'" />';
            $output .= str_replace("%column_size%", wpb_translateColumnWidthToFractional($width[$i]), $column_controls);
            $output .= '<div class="wpb_element_wrapper">';
            $output .= '<div class="row-fluid wpb_column_container wpb_sortable_container wpb_'.$this->settings['base'].'_container wpb_container_block">';
            $output .= do_shortcode( shortcode_unautop($content) );
            $output .= WPBakeryVisualComposer::getInstance()->getLayout()->getContainerHelper();
            $output .= '</div>';
            if ( isset($this->settings['params']) ) {
                $inner = '';
                foreach ($this->settings['params'] as $param) {
                    $param_value = $$param['param_name'];
                    //var_dump($param_value);
                    if ( is_array($param_value)) {
                        // Get first element from the array
                        reset($param_value);
                        $first_key = key($param_value);
                        $param_value = $param_value[$first_key];
                    }
                    $inner .= $this->singleParamHtmlHolder($param, $param_value);
                }
                $output .= $inner;
            }
            $output .= '</div>';
            $output .= str_replace("%column_size%", wpb_translateColumnWidthToFractional($width[$i]), $column_controls_bottom);
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
        }
        return $output;
    }

    public function contentAdmin_old($atts, $content = null) {
        $width = $el_class = $title = '';
        extract(shortcode_atts($this->predefined_atts, $atts));
        $output = '';
        $column_controls = $this->getColumnControls($this->settings('controls'));
        for ( $i=0; $i < count($width); $i++ ) {
            $output .= '<div class="group wpb_sortable">';
            $output .= '<div class="wpb_element_wrapper">';
            $output .= '<div class="row-fluid wpb_row_container not-row-inherit">';
            $output .= '<h3><a href="#">'.$title.'</a></h3>';
            $output .= '<div '.$this->customAdminBockParams().' data-element_type="'.$this->settings["base"].'" class=" wpb_'.$this->settings['base'].' wpb_sortable wpb_container_block">';
            $output .= '<input type="hidden" class="wpb_vc_sc_base" name="" value="'.$this->settings['base'].'" />';

            $output .= '<div class="wpb_element_wrapper">';
            $output .= '<div class="row-fluid wpb_row_container not-row-inherit">';
            $output .= do_shortcode( shortcode_unautop($content) );
            $output .= '</div>';
            if ( isset($this->settings['params']) ) {
                $inner = '';
                foreach ($this->settings['params'] as $param) {
                    $param_value = $$param['param_name'];
                    //var_dump($param_value);
                    if ( is_array($param_value)) {
                        // Get first element from the array
                        reset($param_value);
                        $first_key = key($param_value);
                        $param_value = $param_value[$first_key];
                    }
                    $inner .= $this->singleParamHtmlHolder($param, $param_value);
                }
                $output .= $inner;
            }
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
        }

        return $output;
    }

    protected function outputTitle($title) {
        return  '';
    }

    public function customAdminBlockParams() {
        return '';
    }

    public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="'.$this->settings["base"].'" class="wpb_'.$this->settings['base'].' wpb_sortable wpb_container_block wpb_content_holder"'.$this->customAdminBlockParams();
    }
}
wpb_map( array(
    "name"		=> __("Accordion tab", "js_composer"),
    "base"		=> "vc_accordion_tab",
    "class"		=> "",
    "icon"      => "",
    "wrapper_class" => "",
    "controls"	=> "full",
    "content_element" => false,
    "params"	=> array(
        array(
            "type" => "tab_accordion_title",
            "heading" => __("Title", "js_composer"),
            "param_name" => "title",
            "value" => "",
            "description" => __("Title which will be displayed as the tab anchor", "js_composer")
        ),
    )
) );

function tab_accordion_title_field($settings, $value) {
    $dependency = vc_generate_dependencies_attributes($settings);
    return '<div class="my_param_block">'
        .'<input name="'.$settings['param_name']
        .'" class="wpb_vc_param_value wpb-textinput'
        .$settings['param_name'].' '.$settings['type'].'_field" type="text" value="'
        .$value.'" ' . $dependency . ' data-js-function="wpb_change_accordion_tab_title" />'
        .'</div>';
}

add_shortcode_param('tab_accordion_title', 'tab_accordion_title_field');


class WPBakeryShortCode_VC_Accordion extends WPBakeryShortCode {

    public function __construct($settings) {
        parent::__construct($settings);
        // WPBakeryVisualComposer::getInstance()->addShortCode( array( 'base' => 'vc_accordion_tab' ) );
    }

    protected function content( $atts, $content = null ) {
        wp_enqueue_style( 'ui-custom-theme' );
        wp_enqueue_script('jquery-ui-accordion');
        $title = $interval = $width = $el_position = $el_class = '';
        //
        extract(shortcode_atts(array(
            'title' => '',
            'interval' => 0,
            'width' => '1/1',
            'el_position' => '',
            'el_class' => ''
        ), $atts));
        $output = '';

        $el_class = $this->getExtraClass($el_class);
        $width = '';//wpb_translateColumnWidthToSpan($width);

        $output .= "\n\t".'<div class="wpb_accordion wpb_content_element '.$width.$el_class.' not-column-inherit">'; //data-interval="'.$interval.'"
        $output .= "\n\t\t".'<div class="wpb_wrapper wpb_accordion_wrapper ui-accordion">';
        //$output .= ($title != '' ) ? "\n\t\t\t".'<h2 class="wpb_heading wpb_accordion_heading">'.$title.'</h2>' : '';
        $output .= wpb_widget_title(array('title' => $title, 'extraclass' => 'wpb_accordion_heading'));

        $output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
        $output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
        $output .= "\n\t".'</div> '.$this->endBlockComment($width);

        //
        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
        return $output;
    }

    public function contentAdmin( $atts, $content ) {
        $width = $custom_markup = '';
        $shortcode_attributes = array('width' => '1/1');
        foreach ( $this->settings['params'] as $param ) {
            if ( $param['param_name'] != 'content' ) {
                if ( is_string($param['value']) ) {
                    $shortcode_attributes[$param['param_name']] = __($param['value'], "js_composer");
                } else {
                    $shortcode_attributes[$param['param_name']] = $param['value'];
                }
            } else if ( $param['param_name'] == 'content' && $content == NULL ) {
                $content = __($param['value'], "js_composer");
            }
        }
        extract(shortcode_atts(
            $shortcode_attributes
            , $atts));

        $output = '';

        $elem = $this->getElementHolder($width);

        $iner = '';
        foreach ($this->settings['params'] as $param) {
            $param_value = '';
            $param_value = $$param['param_name'];
            if ( is_array($param_value)) {
                // Get first element from the array
                reset($param_value);
                $first_key = key($param_value);
                $param_value = $param_value[$first_key];
            }
            $iner .= $this->singleParamHtmlHolder($param, $param_value);
        }
        //$elem = str_ireplace('%wpb_element_content%', $iner, $elem);
        $tmp = '';
        $template = '<div class="wpb_template">'.do_shortcode('[vc_accordion_tab title="New Section"][/vc_accordion_tab]').'</div>';

        if ( isset($this->settings["custom_markup"]) && $this->settings["custom_markup"] != '' ) {
            if ( $content != '' ) {
                $custom_markup = str_ireplace("%content%", $tmp.$content.$template, $this->settings["custom_markup"]);
            } else if ( $content == '' && isset($this->settings["default_content"]) && $this->settings["default_content"] != '' ) {
                $custom_markup = str_ireplace("%content%", $this->settings["default_content"].$template, $this->settings["custom_markup"]);
            }
            //$output .= do_shortcode($this->settings["custom_markup"]);
            $iner .= do_shortcode($custom_markup);
        }
        $elem = str_ireplace('%wpb_element_content%', $iner, $elem);
        $output = $elem;

        return $output;
    }
}