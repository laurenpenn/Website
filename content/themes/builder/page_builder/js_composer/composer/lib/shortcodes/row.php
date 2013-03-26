<?php
/**
 * WPBakery Visual Composer row
 *
 * @package WPBakeryVisualComposer
 *
 */

class WPBakeryShortCode_VC_Row extends WPBakeryShortCode {
    protected $predefined_atts = array(
        'el_class' => '',
    );

    public function content( $atts, $content = null ) {
        $el_class = '';
        $output = '';
        extract(shortcode_atts(array(
            'el_class' => '',
        ), $atts));
        wp_enqueue_style( 'js_composer_front' );
        wp_enqueue_script( 'wpb_composer_front_js' );
        $output .= '<div class="wpb_row '.get_row_css_class().' '.$el_class.'">';
        $output .= wpb_js_remove_wpautop($content);
        $output .= '</div>'.$this->endBlockComment('row');
        return $output;
    }

    /* This returs block controls
   ---------------------------------------------------------- */
    public function getColumnControls($controls) {
        $controls_start = '<div class="controls row">';
        $controls_end = '</div>';

        $right_part_start = '';//'<div class="controls_right">';
        $right_part_end = '';//'</div>';

        //Create columns
        $controls_center_start = '<span>';
        $controls_layout = wpb_get_row_columns_control();

        $controls_column_size = ''; // '<span class="column_size">%column_size%</span>'; //</div>
        $controls_move = ' <a class="column_move" href="#" title="'.__('Drag row to reorder', 'js_composer').'"></a>';

        $controls_add = ' <a class="column_add" href="#" title="'.__('Add to this row', 'js_composer').'"></a>';

        $controls_edit = ' <a class="column_edit" href="#" title="'.__('Edit this row', 'js_composer').'"></a>';
        $controls_popup = ' <a class="column_popup" href="#" title="'.__('Pop up this row', 'js_composer').'"></a>';
        $controls_delete = ' <a class="column_clone" href="#" title="'.__('Clone this row', 'js_composer').'"></a> <a class="column_delete" href="#" title="'.__('Delete this row', 'js_composer').'"></a>';
        $controls_center_end = '</span>';
        // $delete_edit_row = '<a class="row_delete" title="'.__('Delete %element%', "js_composer").'">'.__('Delete %element%', "js_composer").'</a>';

        $column_controls_full =  $controls_start. $controls_move . $controls_center_start . $controls_layout . $controls_add . $controls_edit . $controls_delete . $controls_center_end . $controls_end;
        $column_controls_size_delete = $controls_move . $controls_start . $controls_column_size . $right_part_start . $controls_delete . $right_part_end . $controls_end;
        $column_controls_popup_delete = $controls_start . $right_part_start . $controls_delete . $right_part_end . $controls_end;
        $column_controls_delete = $controls_start . $right_part_start . $controls_delete . $right_part_end . $controls_end;
        $column_controls_edit_popup_delete = $controls_start . $right_part_start . $controls_edit . $controls_delete . $right_part_end . $controls_end;

        if ( $controls == 'popup_delete' ) {
            return $column_controls_popup_delete;
        }
        else if ( $controls == 'edit_popup_delete' ) {
            return $column_controls_edit_popup_delete;
        }
        else if ( $controls == 'size_delete' ) {
            return $column_controls_size_delete;
        }
        else if ( $controls == 'popup_delete' ) {
            return $column_controls_popup_delete;
        }
        else {
            return $column_controls_full;
        }
    }

    public function contentAdmin($atts, $content = null) {
        $width = $el_class = '';
        extract(shortcode_atts($this->predefined_atts, $atts));

        $output = '';

        $column_controls = $this->getColumnControls($this->settings('controls'));

        for ( $i=0; $i < count($width); $i++ ) {
            $output .= '<div'.$this->customAdminBockParams().' data-element_type="'.$this->settings["base"].'" class="wpb_'.$this->settings['base'].' wpb_sortable wpb_container_block">';
            $output .= '<input type="hidden" class="wpb_vc_sc_base" name="" value="'.$this->settings['base'].'" />';
            $output .= str_replace("%column_size%", 1, $column_controls);
            $output .= '<div class="wpb_element_wrapper">';
            $output .= '<div class="row-fluid wpb_row_container not-row-inherit">';
            if($content=='' && !empty($this->settings["default_content"])) {
                $output .= do_shortcode( shortcode_unautop($this->settings["default_content"]) );
            } else {
                $output .= do_shortcode( shortcode_unautop($content) );

            }
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
        }

        return $output;
    }
    public function customAdminBockParams() {
        return '';
    }
}


class WPBakeryShortCode_VC_Row_Inner extends WPBakeryShortCode_VC_Row {

}



