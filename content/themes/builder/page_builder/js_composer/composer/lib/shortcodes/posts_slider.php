<?php
/**
 */

class WPBakeryShortCode_VC_Posts_slider extends WPBakeryShortCode {

    protected function content( $atts, $content = null ) {
        $title = $type = $count = $interval = $slides_content = $link = '';
        $custom_links = $thumb_size = $posttypes = $posts_in = $categories = '';
        $orderby = $order = $el_class = $width = $el_position = $link_image_start = '';
        extract(shortcode_atts(array(
            'title' => '',
            'type' => 'flexslider_fade',
            'count' => 3,
            'interval' => 3,
            'slides_content' => '',
            'link' => 'link_post',
            'custom_links' => '',
            'thumb_size' => 'thumbnail',
            'posttypes' => '',
            'posts_in' => '',
            'categories' => '',
            'orderby' => NULL,
            'order' => 'DESC',
            'el_class' => '',
            'width' => '1/1',
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
        $width = wpb_translateColumnWidthToSpan($width);

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
        }
        $flex_fx = '';
        if ( $type == 'flexslider' || $type == 'flexslider_fade' || $type == 'fading' ) {
            $type = ' wpb_flexslider flexslider_fade flexslider';
            $flex_fx = ' data-flex_fx="fade"';
        } else if ( $type == 'flexslider_slide' ) {
            $type = ' wpb_flexslider flexslider_slide flexslider';
            $flex_fx = ' data-flex_fx="slide"';
        }

        if ( $link == 'link_image' ) {
            wp_enqueue_script( 'prettyphoto' );
            wp_enqueue_style( 'prettyphoto' );
        }

        $query_args = array();

        //exclude current post/page from query
        if ( $posts_in == '' ) {
            global $post;
            $query_args['post__not_in'] = array($post->ID);
        }
        else if ( $posts_in != '' ) {
            $query_args['post__in'] = explode(",", $posts_in);
        }

        // Post teasers count
        if ( $count != '' && !is_numeric($count) ) $count = -1;
        if ( $count != '' && is_numeric($count) ) $query_args['posts_per_page'] = $count;

        // Post types
        $pt = array();
        if ( $posttypes != '' ) {
            $posttypes = explode(",", $posttypes);
            foreach ( $posttypes as $post_type ) {
                array_push($pt, $post_type);
            }
            $query_args['post_type'] = $pt;
        }

        // Narrow by categories
        if ( $categories != '' ) {
            $categories = explode(",", $categories);
            $gc = array();
            foreach ( $categories as $grid_cat ) {
                array_push($gc, $grid_cat);
            }
            $gc = implode(",", $gc);
            ////http://snipplr.com/view/17434/wordpress-get-category-slug/
            $query_args['category_name'] = $gc;

            $taxonomies = get_taxonomies('', 'object');
            $query_args['tax_query'] = array('relation' => 'OR');
            foreach ( $taxonomies as $t ) {
                if ( in_array($t->object_type[0], $pt) ) {
                    $query_args['tax_query'][] = array(
                        'taxonomy' => $t->name,//$t->name,//'portfolio_category',
                        'terms' => $categories,
                        'field' => 'slug',
                    );
                }
            }
        }

        // Order posts
        if ( $orderby != NULL ) {
            $query_args['orderby'] = $orderby;
        }
        $query_args['order'] = $order;

        // Run query
        $my_query = new WP_Query($query_args);

        $pretty_rel_random = 'rel-'.rand();
        if ( $link == 'custom_link' ) { $custom_links = explode( ',', $custom_links); }
        $teasers = '';
        $i = -1;

        while ( $my_query->have_posts() ) {
            $i++;
            $my_query->the_post();
            $post_title = the_title("", "", false);
            $post_id = $my_query->post->ID;
            //$teaser_post_type = 'posts_slider_teaser_'.$my_query->post->post_type . ' ';
            if ( $slides_content == 'teaser' ) {
                $content = apply_filters('the_excerpt', get_the_excerpt());//get_the_excerpt();
            } else {
                $content = '';
            }
            $thumbnail = '';

            // Thumbnail logic
            $post_thumbnail = $p_img_large = '';

            $post_thumbnail = wpb_getImageBySize(array( 'post_id' => $post_id, 'thumb_size' => $thumb_size ));
            $thumbnail = $post_thumbnail['thumbnail'];
            $p_img_large = $post_thumbnail['p_img_large'];

            // if ( $thumbnail == '' ) $thumbnail = __("No Featured image set.", "js_composer");

            // Link logic
            if ( $link != 'link_no' ) {
                if ( $link == 'link_post' ) {
                    $link_image_start = '<a class="link_image" href="'.get_permalink($post_id).'" title="'.sprintf( esc_attr__( 'Permalink to %s', 'js_composer' ), the_title_attribute( 'echo=0' ) ).'">';
                }
                else if ( $link == 'link_image' ) {
                    $p_video = get_post_meta($post_id, "_p_video", true);
                    //
                    if ( $p_video != "" ) {
                        $p_link = $p_video;
                    } else {
                        $p_link = $p_img_large[0]; // TODO!!!
                    }
                    $link_image_start = '<a class="link_image prettyphoto" href="'.$p_link.'" title="'.the_title_attribute('echo=0').'" rel="prettyPhoto['.$pretty_rel_random.']">';
                }
                else if ( $link == 'custom_link' ) {
                    $link_image_start = '<a class="link_image" href="'.$custom_links[$i].'">';
                }

                $link_image_end = '</a>';
            } else {
                $link_image_start = '';
                $link_image_end = '';
            }

            $description = '';
            if ( $slides_content != '' && $content != '' && ( $type == ' wpb_flexslider flexslider_fade flexslider' || $type == ' wpb_flexslider flexslider_slide flexslider' ) ) {
                $description = '<div class="flex-caption blog_item"><div class="blog_head"><div class="date"><h6><i class="icon-calendar icon-white"></i> '.get_the_time('d').' '.get_the_time('M').' / '.get_the_time('Y') .'</h6></div><h3><a href="'.get_permalink().'">'.get_the_title().' </a></h3></div>
				<div class="meta">
					<span><strong>By</strong> '.get_the_author().'</span>
					<span>'.get_comments_number('0','1','%').' comments</span>
				</div>
				'.implode(array_slice(explode('<br>',wordwrap(get_the_content(),180,'<br>',false)),0,1)).' ...<h6 class="read_more" style="margin-top:10px !important"><a style="margin-top:15px;" href="'. get_permalink($post->ID) . '">'. __("Read More","commander") .'</a></h6></div>';
            }

            $teasers .= $el_start . $link_image_start . $thumbnail . $link_image_end . $description . $el_end;
        } // endwhile loop
        wp_reset_query();


        if ( $teasers ) { $teasers = $slides_wrap_start. $teasers . $slides_wrap_end; }
        else { $teasers = __("Nothing found." , "js_composer"); }


        $output .= "\n\t".'<div class="wpb_gallery wpb_posts_slider wpb_content_element '.$width.$el_class.'">';
        $output .= "\n\t\t".'<div class="wpb_wrapper">';
        $output .= ($title != '' ) ? "\n\t\t\t".'<h3 class="wpb_heading wpb_posts_slider_heading">'.$title.'</h3>' : '';
        $output .= '<div class="wpb_gallery_slides'.$type.'" data-interval="'.$interval.'"'.$flex_fx.'>'.$teasers.'</div>';
        $output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
        $output .= "\n\t".'</div> '.$this->endBlockComment($width);

        $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
        return $output;
    }
}