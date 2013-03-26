<?php
/**
 * WPBakery Visual Composer helpers functions
 *
 * @package WPBakeryVisualComposer
 *
 */


function wpb_map ( $attributes ) {
  if( !isset($attributes['base']) ) {
    trigger_error(__("Wrong wpb_map object. Base attribute is required", 'js_composer'), E_USER_ERROR);
    die();
  }
  WPBMap::map($attributes['base'], $attributes);
}

function wpb_remove($shortcode) {
  WPBMap::dropShortcode($shortcode);
}


function wpb_getImageBySize ( $params = array( 'post_id' => NULL, 'attach_id' => NULL, 'thumb_size' => 'thumbnail' ) ) {
    //array( 'post_id' => $post_id, 'thumb_size' => $grid_thumb_size )
    if ( (!isset($params['attach_id']) || $params['attach_id'] == NULL) && (!isset($params['post_id']) || $params['post_id'] == NULL) ) return;
    $post_id = isset($params['post_id']) ? $params['post_id'] : 0;

    if ( $post_id ) $attach_id = get_post_thumbnail_id($post_id);
    else $attach_id = $params['attach_id'];

    $thumb_size = $params['thumb_size'];

    global $_wp_additional_image_sizes;
    $thumbnail = '';

    if ( is_string($thumb_size) && ((!empty($_wp_additional_image_sizes[$thumb_size]) && is_array($_wp_additional_image_sizes[$thumb_size])) || in_array($thumb_size, array('thumbnail', 'thumb', 'medium', 'large', 'full') ) ) ) {
        //$thumbnail = get_the_post_thumbnail( $post_id, $thumb_size );
        $thumbnail = wp_get_attachment_image( $attach_id, $thumb_size );
        //TODO APPLY FILTER
    }

    if ( $thumbnail == '' &&  $attach_id ) {
        if ( is_string($thumb_size) ) {
            $thumb_size = str_replace(array( 'px', ' ', '*', '×' ), array( '', '', 'x', 'x' ), $thumb_size);
            $thumb_size = explode("x", $thumb_size);
        }
        
        // Resize image to custom size
        $p_img = wpb_resize($attach_id, null, $thumb_size[0], $thumb_size[1], true);
        $alt = trim(strip_tags( get_post_meta($attach_id, '_wp_attachment_image_alt', true) ));

        if ( empty($alt) ) {
            $attachment = get_post($attach_id);
            $alt = trim(strip_tags( $attachment->post_excerpt )); // If not, Use the Caption
        }
        if ( empty($alt) )
            $alt = trim(strip_tags( $attachment->post_title )); // Finally, use the title
        /*if ( wpb_debug() ) {
              var_dump($p_img);
          }*/
        if ( $p_img ) {
            $img_class = '';
            //if ( $grid_layout == 'thumbnail' ) $img_class = ' no_bottom_margin'; class="'.$img_class.'"
            $thumbnail = '<img src="'.$p_img['url'].'" width="'.$p_img['width'].'" height="'.$p_img['height'].'" alt="'.$alt.'" />';
            //TODO: APPLY FILTER
        }
    }
    $p_img_large = wp_get_attachment_image_src($attach_id, 'large' );
    return array( 'thumbnail' => $thumbnail, 'p_img_large' => $p_img_large );
}

function wpb_getColumnControls($width) {
  switch ( $width ) {
    case "span2" :
      $w = "1/6";
      break;
    case "span3" :
      $w = "1/4";
      break;    
    case "span4" :
      $w = "1/3";
      break;      
    case "span6" :
      $w = "1/2";
      break;    
    case "span8" :
      $w = "2/3";
      break;    
    case "span9" :
      $w = "3/4";
      break;    
    case "span12" :
      $w = "1/1";
      break;
    
    default :
      $w = $width;
  }
  return $w;
}
/* Convert span3 to 1/4
---------------------------------------------------------- */
function wpb_translateColumnWidthToFractional($width) {
  switch ( $width ) {
    case "span2" :
      $w = "1/6";
      break;    
    case "span3" :
      $w = "1/4";
      break;    
    case "span4" :
      $w = "1/3";
      break;    
    case "span6" :
      $w = "1/2";
      break;
    case "span8" :
      $w = "2/3";
      break;    
    case "span9" :
      $w = "3/4";
      break;    
    case "span12" :
      $w = "1/1";
      break;    

    default :
      $w = $width;
  }
  return $w;
}

/* Convert 2 to
---------------------------------------------------------- */
function wpb_translateColumnsCountToSpanClass($grid_columns_count) {
  $teaser_width = '';
  switch ($grid_columns_count) {
    case '1' :
      $teaser_width = 'span12';
      break;
    case '2' :
      $teaser_width = 'span6';
      break;
    case '3' :
      $teaser_width = 'span4';
      break;
    case '4' :
      $teaser_width = 'span3';
      break;
    case '5':
      $teaser_width = 'span10';
      break;
    case '6' :
      $teaser_width = 'span2';
      break;
  }
  //return $teaser_width;
  $custom = get_custom_column_class($teaser_width);
  return $custom ? $custom : 'vc_'.$teaser_width;
}

function wpb_translateColumnWidthToSpan($width) {
  switch ( $width ) {
    case "1/6" :
      $w = "span2";
      break;    
    case "1/4" :
      $w = "span3";
      break;
    case "1/3" :
      $w = "span4";
      break;    
    case "1/2" :
      $w = "span6";
      break;    
    case "2/3" :
      $w = "span8";
      break;    
    case "3/4" :
      $w = "span9";
      break;    
    case "5/6" :
      $w = "span10";
      break;    
    case "1/1" :
      $w = "span12";
      break;
    
    default :
      $w = $width;
  }
  $custom = get_custom_column_class($w);
  return $custom ? $custom : 'vc_'.$w;
}

function wpb_js_remove_wpautop($content) {
  $content = do_shortcode( shortcode_unautop($content) );
  $content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content );
  return $content;
}

if ( ! function_exists( 'shortcode_exists' ) ) {
    /**
     * Check if a shortcode is registered in WordPress.
     *
     * Examples: shortcode_exists( 'caption' ) - will return true.
     * shortcode_exists( 'blah' ) - will return false.
     */
    function shortcode_exists( $shortcode = false ) {
        global $shortcode_tags;

        if ( ! $shortcode )
            return false;

        if ( array_key_exists( $shortcode, $shortcode_tags ) )
            return true;

        return false;
    }
}

/* Helper function which returs list of site attached images,
   and if image is attached to the current post it adds class
   'added'
---------------------------------------------------------- */
if (!function_exists('siteAttachedImages')) {
    function siteAttachedImages($att_ids = array()) {
        $output = '';

        global $wpdb;
        $media_images = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'attachment' order by ID desc");
        foreach ( $media_images as $image_post ) {
            $thumb_src = wp_get_attachment_image_src($image_post->ID, 'thumbnail');
            $thumb_src = $thumb_src[0];

            $class = (in_array($image_post->ID, $att_ids)) ? ' class="added"' : '';

            $output .= '<li'.$class.'>
						<img rel="'.$image_post->ID.'" src="'. $thumb_src .'" />
						<span class="img-added">'. __('Added', "js_composer") .'</span>
					</li>';
        }

        if ( $output != '' ) {
            $output = '<ul class="gallery_widget_img_select">' . $output . '</ul>';
        }
        return $output;
    } // end siteAttachedImages()
}

function fieldAttachedImages($att_ids = array()) {
    $output = '';

    foreach ( $att_ids as $th_id ) {
        $thumb_src = wp_get_attachment_image_src($th_id, 'thumbnail');
        if ( $thumb_src ) {
            $thumb_src = $thumb_src[0];
            $output .= '
			<li class="added">
				<img rel="'.$th_id.'" src="'. $thumb_src .'" />
				<a href="#" class="icon-remove"></a>
			</li>';
        }
    }
    if ( $output != '' ) {
        return $output;
    }
}

function wpb_removeNotExistingImgIDs($param_value) {
    $tmp = explode(",", $param_value);
    $return_ar = array();
    foreach ( $tmp as $id ) {
        if ( wp_get_attachment_image($id) ) {
            $return_ar[] = $id;
        }
    }
    $tmp = implode(",", $return_ar);
    return $tmp;
}


/*
* Resize images dynamically using wp built in functions
* Victor Teixeira
*
* php 5.2+
*
* Exemplo de uso:
*
* <?php
 * $thumb = get_post_thumbnail_id();
 * $image = vt_resize( $thumb, '', 140, 110, true );
 * ?>
* <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
*
* @param int $attach_id
* @param string $img_url
* @param int $width
* @param int $height
* @param bool $crop
* @return array
*/
if (!function_exists( 'wpb_resize' )) {
    function wpb_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {

        // this is an attachment, so we have the ID
        if ( $attach_id ) {
            $image_src = wp_get_attachment_image_src( $attach_id, 'full' );
            $actual_file_path = get_attached_file( $attach_id );
            // this is not an attachment, let's use the image url
        } else if ( $img_url ) {
            $file_path = parse_url( $img_url );
            $actual_file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];
            $actual_file_path = ltrim( $file_path['path'], '/' );
            $actual_file_path = rtrim( ABSPATH, '/' ).$file_path['path'];
            $orig_size = getimagesize( $actual_file_path );
            $image_src[0] = $img_url;
            $image_src[1] = $orig_size[0];
            $image_src[2] = $orig_size[1];
        }
        $file_info = pathinfo( $actual_file_path );
        $extension = '.'. $file_info['extension'];

        // the image path without the extension
        $no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

        $cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

        // checking if the file size is larger than the target size
        // if it is smaller or the same size, stop right here and return
        if ( $image_src[1] > $width || $image_src[2] > $height ) {

            // the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
            if ( file_exists( $cropped_img_path ) ) {
                $cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
                $vt_image = array (
                    'url' => $cropped_img_url,
                    'width' => $width,
                    'height' => $height
                );
                return $vt_image;
            }

            // $crop = false
            if ( $crop == false ) {
                // calculate the size proportionaly
                $proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
                $resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;

                // checking if the file already exists
                if ( file_exists( $resized_img_path ) ) {
                    $resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

                    $vt_image = array (
                        'url' => $resized_img_url,
                        'width' => $proportional_size[0],
                        'height' => $proportional_size[1]
                    );
                    return $vt_image;
                }
            }

            // no cache files - let's finally resize it
            $img_editor =  wp_get_image_editor($actual_file_path);

            if ( is_wp_error( $img_editor->resize($width, $height, $crop)) ) {
                return array (
                    'url' => '',
                    'width' => '',
                    'height' => ''
                );
            }

            $new_img_path = $img_editor->generate_filename();

            if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
                return array (
                    'url' => '',
                    'width' => '',
                    'height' => ''
                );
            }

            if ( wpb_debug() ) {
                var_dump(file_exists($actual_file_path));
                var_dump($actual_file_path);
            }

            if(!is_string($new_img_path)) {
                return array (
                    'url' => '',
                    'width' => '',
                    'height' => ''
                );
            }

            $new_img_size = getimagesize( $new_img_path );
            $new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

            // resized output
            $vt_image = array (
                'url' => $new_img,
                'width' => $new_img_size[0],
                'height' => $new_img_size[1]
            );
            return $vt_image;
        }

        // default output - without resizing
        $vt_image = array (
            'url' => $image_src[0],
            'width' => $image_src[1],
            'height' => $image_src[2]
        );
        return $vt_image;
    }
}

if (!function_exists( 'wpb_debug' )) {
    function wpb_debug() {
        if ( isset($_GET['wpb_debug']) && $_GET['wpb_debug'] == 'wpb_debug' ) return true;
        else return false;
    }
}

function js_composer_body_class($classes) {
    $classes[] = 'wpb-js-composer js-comp-ver-'.WPB_VC_VERSION;
    return $classes;
}

function wpb_js_force_send($args) {
    $args['send'] = true;
    return $args;
}


/* Update notifier system
---------------------------------------------------------- */
function wpb_js_composer_check_version_schedule_activation() {
	wp_clear_scheduled_hook('wpb_check_for_update');
	delete_option('wpb_js_composer_show_new_version_message');
	add_option( 'wpb_js_composer_do_activation_redirect', true );
	wp_schedule_event(time() + 60, 'twicedaily', 'wpb_check_for_update'); //twicedaily hourly
}

function wpb_js_composer_check_version_schedule() {
    $version = file_get_contents('http://wpbakery.com/version/?'.time());
    if(!empty($version) && version_compare($version, WPB_VC_VERSION) > 0) {
        add_option('wpb_js_composer_show_new_version_message', $version);
    } else {
        delete_option('wpb_js_composer_show_new_version_message');
    }
}

function wpb_js_composer_check_version_schedule_deactivation() {
	wp_clear_scheduled_hook('wpb_check_for_update');
	delete_option('wpb_js_composer_show_new_version_message');
}

function vc_get_initerface_version() {
    global $post_id;
    if($post_id===NULL) return 2;
    return (int)get_post_meta($post_id, '_wpb_vc_js_interface_version', true);
}

function vc_convert_shortcode($m) {
    list($output, $m_one, $tag, $attr_string, $m_four, $content) = $m;
    $result = $width = $el_position = '';
    $shortcode_attr = shortcode_parse_atts( $attr_string );
    extract(shortcode_atts(array(
        'width' => '1/1',
        'el_class' => '',
        'el_position' => ''
    ), $shortcode_attr));
    if($tag == 'vc_row') return $output;
    // Start
    if(preg_match('/first/', $el_position) || empty($shortcode_attr['width']) || $shortcode_attr['width']==='1/1')  $result = '[vc_row]';
    if($tag!='vc_column') $result .= "\n".'[vc_column width="'.$width.'"]';

    // Tag
    $pattern = get_shortcode_regex();
    if($tag == 'vc_column') {
        $result .= "[{$m_one}{$tag} {$attr_string}]".preg_replace_callback( "/{$pattern}/s", 'vc_convert_inner_shortcode', $content)."[/{$tag}{$m_four}]";
    } elseif( $tag == 'vc_tabs' || $tag == 'vc_accordion' || $tag == 'vc_tour') {
        $result .= "[{$m_one}{$tag} {$attr_string}]".preg_replace_callback( "/{$pattern}/s", 'vc_convert_tab_inner_shortcode', $content)."[/{$tag}{$m_four}]";
    } else {
        $result .= preg_replace('/(\"\d\/\d\")/', '"1/1"', $output);
    }

    // $content = preg_replace_callback( "/{$pattern}/s", 'vc_convert_inner_shortcode', $content );

    // End
    if($tag!='vc_column') $result .= '[/vc_column]';
    if(preg_match('/last/', $el_position) || empty($shortcode_attr['width']) || $shortcode_attr['width']==='1/1')  $result .= '[/vc_row]'."\n";

    return $result;
}

function vc_convert_tab_inner_shortcode($m) {
    list($output, $m_one, $tag, $attr_string, $m_four, $content) = $m;
    $result = $width = $el_position = '';
    extract(shortcode_atts(array(
        'width' => '1/1',
        'el_class' => '',
        'el_position' => ''
    ), shortcode_parse_atts( $attr_string )));
    $pattern = get_shortcode_regex();
    $result .= "[{$m_one}{$tag} {$attr_string}]".preg_replace_callback( "/{$pattern}/s", 'vc_convert_inner_shortcode', $content)."[/{$tag}{$m_four}]";
    return $result;
}

function vc_convert_inner_shortcode($m) {
    list($output, $m_one, $tag, $attr_string, $m_four, $content) = $m;
    $result = $width = $el_position = '';
    extract(shortcode_atts(array(
        'width' => '1/1',
        'el_class' => '',
        'el_position' => ''
    ), shortcode_parse_atts( $attr_string )));
    if($width != '1/1') {
        if(preg_match('/first/', $el_position))  $result .= '[vc_row_inner]';
        $result .= "\n".'[vc_column_inner width="'.$width.'" el_position="'.$el_position.'"]';
        $attr = '';
        foreach(shortcode_parse_atts( $attr_string ) as $key => $value) {
            if($key=='width') $value = '1/1';
            elseif($key=='el_position') $value='first last';
            $attr .= ' '.$key.'="'.$value.'"';
        }
        $result .= "[{$m_one}{$tag} {$attr}]".$content."[/{$tag}{$m_four}]";
        $result .= '[/vc_column_inner]';
        if(preg_match('/last/', $el_position))  $result .= '[/vc_row_inner]'."\n";
    } else {
        $result = $output;
    }
    return $result;
}

$vc_row_layouts = array(
    /*
     * How to count mask?
     * mask = column_count . sum of all numbers. Example layout 12_12 mask = (column count=2)(1+2+1+2=6)= 26
    */
    array('cells' => '11', 'mask' => '12', 'title' => '1/1', 'icon_class' => 'l_11'),
    array('cells' => '12_12', 'mask' => '26', 'title' => '1/2 + 1/2', 'icon_class' => 'l_12_12'),
    array('cells' => '23_13', 'mask' => '29', 'title' => '2/3 + 1/3', 'icon_class' => 'l_23_13'),
    array('cells' => '13_13_13', 'mask' => '312', 'title' => '1/3 + 1/3 + 1/3', 'icon_class' => 'l_13_13_13'),
    array('cells' => '14_14_14_14', 'mask' => '420', 'title' => '1/4 + 1/4 + 1/4 + 1/4', 'icon_class' => 'l_14_14_14_14'),
    array('cells' => '14_34', 'mask' => '212', 'title' => '1/4 + 3/4', 'icon_class' => 'l_14_34'),
    array('cells' => '14_12_14', 'mask' => '313', 'title' => '1/4 + 1/2 + 1/4', 'icon_class' => 'l_14_12_14'),
    array('cells' => '56_16', 'mask' => '218', 'title' => '5/6 + 1/6', 'icon_class' => 'l_56_16'),
    array('cells' => '16_16_16_16_16_16', 'mask' => '642', 'title' => '1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6', 'icon_class' => 'l_16_16_16_16_16_16')
);

function wpb_get_row_columns_control() {
    global $vc_row_layouts;
    $string = '';
    foreach($vc_row_layouts as $layout) {
        $string .= '<a class="set_columns '.$layout['icon_class'].'" data-cells="'.$layout['cells'].'" data-cells-mask="'.$layout['mask'].'" title="'.$layout['title'].'"></a> ';
    }
    return $string;
}

function wpb_vc_get_column_width_indent($width) {
    $identy = '11';
    if($width=='span6') {
        $identy = '12';
    } elseif($width=='span3') {
        $identy = '14';
    } elseif($width=='span4') {
        $identy = '13';
    } elseif($width=='span8') {
        $identy='23';
    } elseif($width=='span2') {
        $identy='16';
    } elseif($width=='span9') {
        $identy='34';
    }elseif($width=='span2') {
        $identy='16';
    } elseif($width=='span10') {
        $identy='56';
    }
    return $identy;
}

function get_row_css_class() {
    $custom = WPBakeryVisualComposerSettings::get('row_css_class');
    return !empty($custom ) ? $custom : 'vc_row-fluid';
}

function get_custom_column_class($class) {
    $custom_array = (array)WPBakeryVisualComposerSettings::get('column_css_classes');
    return !empty($custom_array[$class]) ? $custom_array[$class] : '';
}

