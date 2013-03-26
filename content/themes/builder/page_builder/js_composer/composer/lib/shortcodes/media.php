<?php
/**
 */

class WPBakeryShortCode_VC_Video extends WPBakeryShortCode {

    protected function content( $atts, $content = null ) {
        $title = $link = $size = $el_position = $width = $el_class = '';
        extract(shortcode_atts(array(
            'title' => '',
            'link' => 'http://vimeo.com/23237102',
            'size' => ( isset($content_width) ) ? $content_width : 500,
            'el_position' => '',
            'width' => '1/1',
            'el_class' => ''
        ), $atts));
        $output = '';

        if ( $link == '' ) { return null; }
        $el_class = $this->getExtraClass($el_class);
        $width = '';//wpb_translateColumnWidthToSpan($width);
        /*$size = str_replace(array( 'px', ' ' ), array( '', '' ), $size);
        $size = explode("x", $size);
        $video_w = $size[0];
        $video_h = '';
        if ( count($size) > 1 ) {
            $video_h = ' height="'.$size[1].'"';
        }*/
        $video_w = ( isset($content_width) ) ? $content_width : 500;
        $video_h = $video_w/1.61; //1.61 golden ratio
        global $wp_embed;
        $embed = $wp_embed->run_shortcode('[embed width="'.$video_w.'"'.$video_h.']'.$link.'[/embed]');

        $output .= "\n\t".'<div class="wpb_video_widget wpb_content_element'.$width.$el_class.'">';
        $output .= "\n\t\t".'<div class="wpb_wrapper">';
        //$output .= ($title != '' ) ? "\n\t\t\t".'<h2 class="wpb_heading wpb_video_heading">'.$title.'</h2>' : '';
        $output .= wpb_widget_title(array('title' => $title, 'extraclass' => 'wpb_video_heading'));
        $output .= '<div class="wpb_video_wrapper">' . $embed . '</div>';
        $output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
        $output .= "\n\t".'</div> '.$this->endBlockComment($width);

        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
        return $output;
    }
}
class WPBakeryShortCode_VC_Gmaps extends WPBakeryShortCode {

    protected function content( $atts, $content = null ) {

        $title = $link = $size = $zoom = $type = $el_position = $width = $el_class = '';
        extract(shortcode_atts(array(
            'title' => '',
            'link' => 'https://maps.google.com/maps?q=New+York&hl=en&sll=40.686236,-73.995409&sspn=0.038009,0.078192',
            'size' => 200,
            'zoom' => 14,
            'type' => 'm',
            'el_position' => '',
            'width' => '1/1',
            'el_class' => ''
        ), $atts));
        $output = '';

        if ( $link == '' ) { return null; }

        $el_class = $this->getExtraClass($el_class);
        $width = '';//wpb_translateColumnWidthToSpan($width);

        $size = str_replace(array( 'px', ' ' ), array( '', '' ), $size);

        $output .= "\n\t".'<div class="wpb_gmaps_widget wpb_content_element'.$width.$el_class.'">';
        $output .= "\n\t\t".'<div class="wpb_wrapper">';
        //$output .= ($title != '' ) ? "\n\t\t\t".'<h2 class="wpb_heading wpb_map_heading">'.$title.'</h2>' : '';
        $output .= wpb_widget_title(array('title' => $title, 'extraclass' => 'wpb_map_heading'));
        $output .= '<div class="wpb_map_wraper"><iframe width="100%" height="'.$size.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$link.'&amp;t='.$type.'&amp;z='.$zoom.'&amp;output=embed"></iframe></div>';

        $output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
        $output .= "\n\t".'</div> '.$this->endBlockComment($width);

        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
        return $output;
    }
}