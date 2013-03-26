<?php
/**
 */

class WPBakeryShortCode_VC_gallery extends WPBakeryShortCode {
    protected function content( $atts, $content = null ) {
        $title = $type = $onclick = $custom_links = $img_size = $custom_links_target = '' ;
        $width = $images = $el_class = $interval = $el_position = '';
        extract(shortcode_atts(array(
            'title' => '',
            'type' => 'flexslider',
            'onclick' => 'link_image',
            'custom_links' => '',
            'custom_links_target' => '',
            'img_size' => 'thumbnail',
            'width' => '1/1',
            'images' => '',
            'el_class' => '',
            'interval' => '5',
            'el_position' => ''
        ), $atts));
        $output = '';
        $gal_images = '';
        $link_start = '';
        $link_end = '';
        $el_start = '';
        $el_end = '';
        $slides_wrap_start = '';
        $slides_wrap_end = '';

        $el_class = $this->getExtraClass($el_class);
        $width = '';//wpb_translateColumnWidthToSpan($width);

        if ( $type == 'nivo' ) {
            $type = ' wpb_slider_nivo';
            wp_enqueue_script( 'nivo-slider' );
            wp_enqueue_style( 'nivo-slider-css' );
            
            $slides_wrap_start = '<div class="nivoSlider">';
            $slides_wrap_end = '</div>';
        } else if ( $type == 'flexslider' || $type == 'flexslider_fade' || $type == 'flexslider_slide' || $type == 'fading' ) {
            $el_start = '<li>';
            $el_end = '</li>';
            $slides_wrap_start = '<ul class="slides">';
            $slides_wrap_end = '</ul>';
            wp_enqueue_style('flexslider');
            wp_enqueue_script('flexslider');
        } else if ( $type == 'image_grid' ) {
            wp_enqueue_script( 'isotope' );

            $el_start = '<li class="isotope-item">';
            $el_end = '</li>';
            $slides_wrap_start = '<ul class="wpb_image_grid_ul">';
            $slides_wrap_end = '</ul>';
        }

        if ( $onclick == 'link_image' ) {
            wp_enqueue_script( 'prettyphoto' );
            wp_enqueue_style( 'prettyphoto' );
        }

        $flex_fx = '';
        if ( $type == 'flexslider' || $type == 'flexslider_fade' || $type == 'fading' ) {
            $type = ' wpb_flexslider flexslider_fade flexslider';
            $flex_fx = ' data-flex_fx="fade"';
        } else if ( $type == 'flexslider_slide' ) {
            $type = ' wpb_flexslider flexslider_slide flexslider';
            $flex_fx = ' data-flex_fx="slide"';
        } else if ( $type == 'image_grid' ) {
            $type = ' wpb_image_grid';
        }


        /*
       else if ( $type == 'fading' ) {
          $type = ' wpb_slider_fading';
          $el_start = '<li>';
          $el_end = '</li>';
          $slides_wrap_start = '<ul class="slides">';
          $slides_wrap_end = '</ul>';
          wp_enqueue_script( 'cycle' );
      }*/

        //if ( $images == '' ) return null;
        if ( $images == '' ) $images = '-1,-2,-3';

        $pretty_rel_random = 'rel-'.rand();

        if ( $onclick == 'custom_link' ) { $custom_links = explode( ',', $custom_links); }
        $images = explode( ',', $images);
        $i = -1;

        foreach ( $images as $attach_id ) {
            $i++;
            if ($attach_id > 0) {
              $post_thumbnail = wpb_getImageBySize(array( 'attach_id' => $attach_id, 'thumb_size' => $img_size ));
            }
            else {
              $different_kitten = 400 + $i;
              $post_thumbnail = array();
              $post_thumbnail['thumbnail'] = '<img src="http://placekitten.com/g/'.$different_kitten.'/300" />';
              $post_thumbnail['p_img_large'][0] = 'http://placekitten.com/g/1024/768';
            }
            
            if ( wpb_debug() ) {
                //var_dump($post_thumbnail);
            }
            $thumbnail = $post_thumbnail['thumbnail'];
            $p_img_large = $post_thumbnail['p_img_large'];
            $link_start = $link_end = '';
            

            if ( $onclick == 'link_image' ) {
                 $link_start = '<div class="portfolio_item"><div class="view view-first nolink">
					<div class="mask">
						<a style=" margin-top:-20px;" href="'.$p_img_large[0].'" rel="prettyPhoto['.$pretty_rel_random.']"  class="info"></a>
					</div>';
                $link_end = '</div></div>'; 
            }
            else if ( $onclick == 'custom_link' && isset( $custom_links[$i] ) && $custom_links[$i] != '' ) {
                $link_start = '<a href="'.$custom_links[$i].'"' . (!empty($custom_links_target) ? ' target="'.$custom_links_target.'"' : '') . '>';
                $link_end = '</a>';
            }
            $gal_images .= $el_start . $link_start . $thumbnail . $link_end . $el_end;
        }

        $output .= "\n\t".'<div class="wpb_gallery wpb_content_element'.$width.$el_class.' clearfix">';
        $output .= "\n\t\t".'<div class="wpb_wrapper">';
        //$output .= ($title != '' ) ? "\n\t\t\t".'<h2 class="wpb_heading wpb_gallery_heading">'.$title.'</h2>' : '';
        $output .= wpb_widget_title(array('title' => $title, 'extraclass' => 'wpb_gallery_heading'));
        $output .= '<div class="wpb_gallery_slides'.$type.'" data-interval="'.$interval.'"'.$flex_fx.'>'.$slides_wrap_start.$gal_images.$slides_wrap_end.'</div>';
        $output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
        $output .= "\n\t".'</div> '.$this->endBlockComment($width);

        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
        
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
        }
        else {
            $output .= '<'.$param['holder'].' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">'.$value.'</'.$param['holder'].'>';
        }
        if($param_name == 'images') {
            $images_ids = empty($value) ? array() : explode(',', trim($value));
            $output .= '<ul class="attachment-thumbnails'.( empty($images_ids) ? ' image-exists' : '' ).'">';
            foreach($images_ids as $image) {
                $img = wpb_getImageBySize(array( 'attach_id' => (int)$image, 'thumb_size' => 'thumbnail' ));
                $output .= ( $img ? '<li>'.$img['thumbnail'].'</li>' : '<li><img width="150" height="150" test="'.$image.'" src="' . WPBakeryVisualComposer::getInstance()->assetURL('vc/blank.gif') . '" class="attachment-thumbnail" alt="" title="" /></li>');
            }
            $output .= '</ul>';
            $output .= '<a href="#" class="column_edit_trigger' . ( !empty($images_ids) ? ' image-exists' : '' ) . '">' . __( 'Add images', 'js_composer' ) . '</a>';

        }
        return $output;
    }


}

class WPBakeryShortCode_VC_Single_image extends WPBakeryShortCode {

    public function content( $atts, $content = null ) {

        $el_class = $width = $el_position = $image = $img_size = $img_link = $img_link_target = '';

        extract(shortcode_atts(array(
            'title' => '',
            'width' => '1/2',
            'image' => $image,
            'el_position' => '',
            'img_size'  => 'thumbnail',
            'img_link' => '',
            'img_link_target' => '_self',
            'el_class' => ''
        ), $atts));

        $output = '';
        $img = wpb_getImageBySize(array( 'attach_id' => preg_replace('/[^\d]/', '', $image), 'thumb_size' => $img_size ));
        if ( $img == NULL ) $img['thumbnail'] = '<img src="http://placekitten.com/g/400/300" /> <small>'.__('This is image placeholder, edit your page to replace it.', 'js_composer').'</small>';
        $el_class = $this->getExtraClass($el_class);
        $width = '';//wpb_translateColumnWidthToSpan($width);
        // $content =  !empty($image) ? '<img src="'..'" alt="">' : '';
        $image_string = !empty($img_link) ? '<a href="'.$img_link.'"'.($img_link_target!='_self' ? ' target="'.$img_link_target.'"' : '').'>'.$img['thumbnail'].'</a>' : $img['thumbnail'];
        $content = '';
        $output .= "\n\t".'<div class="wpb_single_image wpb_content_element'.$width.$el_class.'">';
        $output .= "\n\t\t".'<div class="wpb_wrapper">';
        $output .= "\n\t\t\t".wpb_widget_title(array('title' => $title, 'extraclass' => 'wpb_singleimage_heading'));
        $output .= "\n\t\t\t".$image_string;
        $output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
        $output .= "\n\t".'</div> '.$this->endBlockComment($width);
        //
        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
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
            if(($param['type'])=='attach_image') {
                $img = wpb_getImageBySize(array( 'attach_id' => (int)preg_replace('/[^\d]/', '', $value), 'thumb_size' => 'thumbnail' ));
                $output .= ( $img ? $img['thumbnail'] : '<img width="150" height="150" src="' . WPBakeryVisualComposer::getInstance()->assetURL('vc/blank.gif') . '" class="attachment-thumbnail" alt="" title="" style="display: none;" />') . '<img src="' . WPBakeryVisualComposer::getInstance()->assetURL('vc/elements_icons/single_image.png') . '" class="no_image_image' . ( $img && !empty($img['p_img_large'][0]) ? ' image-exists' : '' ) . '" /><a href="#" class="column_edit_trigger' . ( $img && !empty($img['p_img_large'][0]) ? ' image-exists' : '' ) . '">' . __( 'Add image', 'js_composer' ) . '</a>';
            }
        }
        else {
            $output .= '<'.$param['holder'].' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">'.$value.'</'.$param['holder'].'>';
        }
        return $output;
    }
}