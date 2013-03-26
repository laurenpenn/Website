<?php
/**
 * WPBakery Visual Composer shortcodes
 *
 * @package WPBakeryVisualComposer
 *
 */

class WPBakeryShortCode_VC_Column extends WPBakeryShortCode {
    protected  $predefined_atts = array(
        'el_class' => '',
        'el_position' => '',
        'width' => '1/1'
    );
    public function getColumnControls($controls, $extended_css = '') {
        $controls_start = '<div class="controls'.(!empty($extended_css) ? " {$extended_css}" : '').'">';
        $controls_end = '</div>';

        $right_part_start = '<div class="controls_right">';
        $right_part_end = '</div>';

        //<div class="column_size_wrapper">
        $controls_column_size = '<a class="column_decrease controls-type-'.$controls.'" href="#" title="'.__('Decrease width', 'js_composer').'"></a> <span class="column_size">%column_size%</span> <a class="column_increase" href="#" title="'.__('Increase width', 'js_composer').'"></a>'; //</div>
        $controls_add = ' <a class="column_add controls-type-'.$controls.'" href="#" title="'.__(($extended_css=='bottom-controls' ? 'Append to this column' : 'Prepend to this column'), 'js_composer').'"></a>';

        $controls_edit = ' <a class="column_edit controls-type-'.$controls.'" href="#" title="'.__('Edit this column', 'js_composer').'"></a>';
        $controls_popup = ' <a class="column_popup controls-type-'.$controls.'" href="#" title="'.__('Pop up this column', 'js_composer').'"></a>';
        $controls_delete = ' <a class="column_clone controls-type-'.$controls.'" href="#" title="'.__('Clone this column', 'js_composer').'"></a> <a class="column_delete" href="#" title="'.__('Delete this column', 'js_composer').'"></a>';
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

    public function content( $atts, $content = null ) {

        $el_class = $width = $el_position = '';

        extract(shortcode_atts(array(
            'el_class' => '',
            'el_position' => '',
            'width' => '1/1'
        ), $atts));

        $output = '';

        $el_class = $this->getExtraClass($el_class);
        $width = wpb_translateColumnWidthToSpan($width);

        if ( $this->shortcode == 'vc_column' ) {
            $el_class .= ' wpb_column column_container';
        }
        else if ( $this->shortcode == 'vc_column_text' ) {
            $el_class .= ' wpb_text_column';
        }

        $output .= "\n\t".'<div class="'.$width.$el_class.'">';//class: wpb_content_element removed
        $output .= "\n\t\t".'<div class="wpb_wrapper">';
        $output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
        $output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
        $output .= "\n\t".'</div> '.$this->endBlockComment($width) . "\n";

        //
        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
        return $output;
    }

    public function singleParamHtmlHolder($param, $value) {
        $output = '';
        // Compatibility fixes.
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
        }
        else {
            $output .= '<'.$param['holder'].' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">'.$value.'</'.$param['holder'].'>';
        }
        return $output;
    }

    public function contentAdmin($atts, $content = null) {
        $width = $el_class = '';
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
        } else if ( $width == 'column_56' || $width == '5/6' ) {
            $width = array('span10');
        } else {
            $width = array('');
        }


        for ( $i=0; $i < count($width); $i++ ) {
            $output .= '<div '.$this->mainHtmlBlockParams($width, $i).'>';
            $output .= '<input type="hidden" class="wpb_vc_sc_base" name="" value="'.$this->settings['base'].'" />';
            $output .= str_replace("%column_size%", wpb_translateColumnWidthToFractional($width[$i]), $column_controls);
            $output .= '<div class="wpb_element_wrapper">';
            $output .= '<div '.$this->containerHtmlBlockParams($width, $i).'>';
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
        }
        return $output;
    }
    public function customAdminBlockParams() {
        return '';
    }

    public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="'.$this->settings["base"].'" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_'.$this->settings['base'].' wpb_sortable '.$width[$i].' wpb_container_block wpb_content_holder"'.$this->customAdminBlockParams();
    }

    public function containerHtmlBlockParams($width, $i) {
        return 'class="row-fluid wpb_column_container wpb_sortable_container wpb_'.$this->settings['base'].'_container wpb_container_block"';
    }
}


wpb_map( array(
    "name"		=> __("Column", "js_composer"),
    "base"		=> "vc_column",
    "class"		=> "",
    "icon"      => "",
    "wrapper_class" => "",
    "controls"	=> "full",
    "content_element" => false,
    "params"	=> array(
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", "js_composer"),
            "param_name" => "el_class",
            "value" => "",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
        )
    )
) );


class WPBakeryShortCode_VC_Column_Inner extends WPBakeryShortCode_VC_Column {

}

wpb_map( array(
    "name"		=> __("Column", "js_composer"),
    "base"		=> "vc_column_inner",
    "class"		=> "",
    "icon"      => "",
    "wrapper_class" => "",
    "controls"	=> "full",
    "content_element" => false,
    "params"	=> array(
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", "js_composer"),
            "param_name" => "el_class",
            "value" => "",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "js_composer")
        )
    )
) );