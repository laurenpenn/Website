<?php
/**
 */
define('TAB_TITLE', __("Tab", "js_composer"));
class WPBakeryShortCode_VC_Tab extends WPBakeryShortCode_VC_Column {
    protected $predefined_atts = array(
                        'tab_id' => TAB_TITLE,
                        'title' => ''
                        );
    public function content( $atts, $content = null ) {
        wp_enqueue_script('jquery_ui_tabs_rotate');
        $title = $tab_id = '';
        extract(shortcode_atts($this->predefined_atts, $atts));
        $output = '';
        $output .= "\n\t\t\t" . '<div id="tab-'. (empty($tab_id) ? sanitize_title( $title ) : $tab_id) .'" class="wpb_tab wpb_row vc_row-fluid ui-tabs-panel wpb_ui-tabs-hide clearfix">';
        $output .= ($content=='' || $content==' ') ? __("Empty section. Edit page to add content here.", "js_composer") : "\n\t\t\t\t" . wpb_js_remove_wpautop($content);
        $output .= "\n\t\t\t" . '</div> ' . $this->endBlockComment('.wpb_tab');
        return $output;
    }
    public function customAdminBlockParams() {
        return ' id="tab-'.$this->atts['tab_id'] .'"';
    }
    public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="'.$this->settings["base"].'" class="wpb_'.$this->settings['base'].' wpb_sortable wpb_container_block wpb_content_holder"'.$this->customAdminBlockParams();
    }
    public function containerHtmlBlockParams($width, $i) {
        return 'class="row-fluid wpb_column_container wpb_sortable_container wpb_'.$this->settings['base'].'_container wpb_container_block wpb_no_content_element_inside"';
    }
}

wpb_map( array(
    "name"		=> __("Tab", "js_composer"),
    "base"		=> "vc_tab",
    "class"		=> "",
    "icon"      => "",
    "wrapper_class" => "",
    "controls"	=> "full",
    "content_element" => false,
    "params"	=> array(
        array(
            "type" => "textfield",
            "heading" => __("Title", "js_composer"),
            "param_name" => "title",
            "value" => "",
            "description" => __("Title which will be displayed as the tab anchor in navigation control", "js_composer")
        ),
        array(
            "type" => "tab_id",
            "heading" => __("Tab_id", "js_composer"),
            "param_name" => "tab_id",
            "value" => "",
            "description" => __("Unique identifier for this tab. Generated automatically.", "js_composer")
        )
    )
) );

function tab_id_settings_field($settings, $value) {
    $dependency = vc_generate_dependencies_attributes($settings);
    return '<div class="my_param_block">'
        .'<input name="'.$settings['param_name']
        .'" class="wpb_vc_param_value wpb-textinput '
        .$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'
        .$value.'" ' . $dependency . ' data-js-function="wpb_change_tab_title" />'
        .'<label>'.$value.'</label>'
        .'</div>';
    // TODO: Add data-js-function to documentation
}

add_shortcode_param('tab_id', 'tab_id_settings_field');


class WPBakeryShortCode_VC_Tabs extends WPBakeryShortCode {

    public function __construct($settings) {
        parent::__construct($settings);
        // WPBakeryVisualComposer::getInstance()->addShortCode( array( 'base' => 'vc_tab' ) );
    }

    public function contentAdmin($atts, $content = null) {
        $width = $custom_markup = '';
        $shortcode_attributes = array('width' => '1/1');
        foreach ( $this->settings['params'] as $param ) {
            if ( $param['param_name'] != 'content' ) {
                //$shortcode_attributes[$param['param_name']] = $param['value'];
                if ( is_string($param['value']) ) {
                    $shortcode_attributes[$param['param_name']] = __($param['value'], "js_composer");
                } else {
                    $shortcode_attributes[$param['param_name']] = $param['value'];
                }
            } else if ( $param['param_name'] == 'content' && $content == NULL ) {
                //$content = $param['value'];
                $content = __($param['value'], "js_composer");
            }
        }
        extract(shortcode_atts(
            $shortcode_attributes
            , $atts));

        // Extract tab titles

        preg_match_all( '/vc_tab title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE );
        /*
        $tab_titles = array();
        if ( isset($matches[1]) ) { $tab_titles = $matches[1]; }
        */
        $output = '';
        $tab_titles = array();

        if ( isset($matches[0]) ) { $tab_titles = $matches[0]; }
        $tmp = '';
        if( count($tab_titles) ) {
            $tmp .= '<ul class="clearfix tabs_controls">';
            foreach ( $tab_titles as $tab ) {
                preg_match('/title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $tab[0], $tab_matches, PREG_OFFSET_CAPTURE );
                if(isset($tab_matches[1][0])) {
                    $tmp .= '<li><a href="#tab-'. (isset($tab_matches[3][0]) ? $tab_matches[3][0] : sanitize_title( $tab_matches[1][0] ) ) .'">' . $tab_matches[1][0] . '</a></li>';

                }
            }
            $tmp .= '</ul>'."\n";
        } else {
            $output .= do_shortcode( $content );
        }



        /*
        if ( count($tab_titles) ) {
            $tmp .= '<ul class="clearfix">';
            foreach ( $tab_titles as $tab ) {
                $tmp .= '<li><a href="#tab-'. sanitize_title( $tab[0] ) .'">' . $tab[0] . '</a></li>';
            }
            $tmp .= '</ul>';
        } else {
            $output .= do_shortcode( $content );
        }
        */
        $elem = $this->getElementHolder($width);

        $iner = '';
        foreach ($this->settings['params'] as $param) {
            $param_value = $custom_markup = '';
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

        if ( isset($this->settings["custom_markup"]) &&$this->settings["custom_markup"] != '' ) {
            if ( $content != '' ) {
                $custom_markup = str_ireplace("%content%", $tmp.$content, $this->settings["custom_markup"]);
            } else if ( $content == '' && isset($this->settings["default_content"]) && $this->settings["default_content"] != '' ) {
                $custom_markup = str_ireplace("%content%",$this->settings["default_content"],$this->settings["custom_markup"]);
            }
            //$output .= do_shortcode($this->settings["custom_markup"]);
            $iner .= do_shortcode($custom_markup);
        }
        $iner .= $this->getTabTemplate();
        $elem = str_ireplace('%wpb_element_content%', $iner, $elem);
        $output = $elem;

        return $output;
    }

    public function getTabTemplate() {
        return '<div class="wpb_template">'.do_shortcode('[vc_tab title="Tab" tab_id=""][/vc_tab]').'</div>';
    }

    public function content($atts, $content =null)
    {
        wp_enqueue_style( 'ui-custom-theme' );
        wp_enqueue_script('jquery-ui-tabs');
        //
        $title = $interval = $width = $el_position = $el_class = '';
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

        $element = 'wpb_tabs';
        if ( 'vc_tour' == $this->shortcode) $element = 'wpb_tour';

        // Extract tab titles
        preg_match_all( '/vc_tab title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE );
        $tab_titles = array();

        /**
         * vc_tabs
         *
         */
        if ( isset($matches[0]) ) { $tab_titles = $matches[0]; }
        $tabs_nav = '';
        $tabs_nav .= '<ul class="clearfix ui-tabs-nav">';
        foreach ( $tab_titles as $tab ) {
            preg_match('/title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $tab[0], $tab_matches, PREG_OFFSET_CAPTURE );
            if(isset($tab_matches[1][0])) {
                $tabs_nav .= '<li><a href="#tab-'. (isset($tab_matches[3][0]) ? $tab_matches[3][0] : sanitize_title( $tab_matches[1][0] ) ) .'">' . $tab_matches[1][0] . '</a></li>';

            }
        }
        $tabs_nav .= '</ul>'."\n";
        /*
        if ( isset($matches[1]) ) { $tab_titles = $matches[1]; }
        $tabs_nav = '';
        $tabs_nav .= '<ul class="clearfix">';
        foreach ( $tab_titles as $tab ) {
            $tabs_nav .= '<li><a href="#tab-'. sanitize_title( $tab[0] ) .'">' . $tab[0] . '</a></li>';
        }
        $tabs_nav .= '</ul>'."\n";
        */
        $output .= "\n\t".'<div class="'.$element.' wpb_content_element '.$width.$el_class.'" data-interval="'.$interval.'">';
        $output .= "\n\t\t".'<div class="wpb_wrapper wpb_tour_tabs_wrapper ui-tabs clearfix">';
        //$output .= ($title != '' ) ? "\n\t\t\t".'<h2 class="wpb_heading '.$element.'_heading">'.$title.'</h2>' : '';
        $output .= wpb_widget_title(array('title' => $title, 'extraclass' => $element.'_heading'));
        $output .= "\n\t\t\t".$tabs_nav;
        $output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
        if ( 'vc_tour' == $this->shortcode) {
            $output .= "\n\t\t\t" . '<div class="wpb_tour_next_prev_nav clearfix"> <span class="wpb_prev_slide"><a href="#prev" title="'.__('Previous slide').'">'.__('Previous slide').'</a></span> <span class="wpb_next_slide"><a href="#next" title="'.__('Next slide').'">'.__('Next slide').'</a></span></div>';
        }
        $output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
        $output .= "\n\t".'</div> '.$this->endBlockComment($width);

        //
        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
        return $output;
    }
}