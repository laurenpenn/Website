<?php
/*
	SHORTCODES
*/




add_shortcode('blog', 'theme_blog');

function theme_blog( $atts, $content = null)
{

	extract(shortcode_atts(
        array(
			'show_posts' => '1',
			'header' => 'My blog title',
    ), $atts));
	
	$output = '<div>'."\n";
	if($content) { $output .= '<p>'.theme_remove_autop(stripslashes($content)).'</p>'."\n"; }
	$output .= theme_blog_loop($show_posts, $header);
	$output .= '</div>'."\n";

	return $output;

}

function theme_blog_loop($show_posts, $header)
{
	$args = array( 
				'post_type' => 'post',
				'portfolio-types' => $category_slug_name,
				'posts_per_page' => $show_posts,
				'header' => $header,
				'post_status' => 'publish'
				); 
	$query =  new WP_Query(array('header' => $header, 'post_type' => 'post', 'showposts' => $show_posts, 'order' => 'DESC'));

	$loop_count = 0;
	
	$default_url= get_template_directory_uri();
	
			$output .= '<div id="slides">'."\n";
				$output .= '<div class="sep_bg">'."\n";
					$output .= '<h3 class="pull-left" style="margin-bottom:0px;">'.$header.'</h3>'."\n";
					$output .= '<div class="pull-right"><a href="#" class="prev"></a><a href="#" class="next"></a></div>'."\n";
					$output .= '<div class="clearfix"></div>'."\n";
				$output .= '</div>'."\n";
				$output .= '<div class="slides_container">'."\n";

	while ($query->have_posts()) { $query->the_post();

		$post_id = get_the_id();
  

		$attr = array(
			'class'	=> "bordered_img last",
		);
		global $more;
	    $more = 0;
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
		$title = get_the_title();
		$icon = get_post_meta($post_id, 'port-icon', 1);
		$descr = get_post_meta($post_id, 'port-descr', 1);
		$video = get_post_meta($post->ID, video, true);
		$more_link_text = '<br><a class="btn btn-small" style=" margin-top:15px;" href="'.get_permalink().'">Read More</a> '."\n";
		$content = get_the_content('<br><a class="btn btn-small btn-info" style=" margin-top:15px;" href="'. get_permalink($post->ID) . '">Read More</a>');
		$author = get_the_author_meta('nickname');

			$output .= '<div class="row">'."\n";
				$output .= '<div class="span6">'."\n";
					if (get_post_meta($post->ID, video, true));{ 
						$output .= ''.$video.''."\n";
					}
					if (( has_post_thumbnail())) {
						
						$output .= '<div class="row">'."\n";
							$output .= '<div class="span6 slider_area nolink" style="margin-bottom:0px;">'."\n";
								$output .= '<div class="view view-first">'."\n";
									$output .= '<img src="'.$large_image_url[0].'" alt="" />'."\n";
									$output .= '<div class="mask">'."\n";
										$output .= '<a href="'.$large_image_url[0].'" rel="prettyPhoto" class="info"></a>'."\n";
									$output .= '</div>'."\n";
								$output .= '</div>'."\n";
								$output .= '<div class="clearfix"></div>'."\n";
							$output .= '</div>'."\n";
						$output .= '</div>'."\n";
	
					}
					$output .= '<div class="row" style="margin-top:20px;">'."\n";
					$output .= '<div class="span2" style="margin-bottom:20px;">'."\n";
					$output .= '<h4 class="sep_bg">'.get_the_time('d M Y').'</h4>'."\n";
						$output .= '<div class="meta">'."\n";
							$output .= '<span><strong><i class="icon-user"></i> Author:</strong> '.$author.'</span>'."\n";
							$output .= '<span><strong><i class="icon-comment"></i> Comments:</strong> <a href="'.get_permalink().'#comments">'.get_comments_number('0','1','%').'</a></span>'."\n";
						$output .= '</div>'."\n";
						$output .= '<hr class="hidden-phone">'."\n";
					$output .= '</div>'."\n";
					
					
					$output .= '<div class="span4"><h4 class="sep_bg"><a href="'.get_permalink().'"> '.$title.'</a></h4>'.do_shortcode($content).'</div>'."\n";
					$output .= '</div>'."\n";
				$output .= '</div>'."\n";
			$output .= '</div>'."\n";



	}
	wp_reset_postdata();
		$output .= '</div>'."\n";
	$output .= '</div>'."\n";

	return $output;
}






add_shortcode('6col_portfolio', 'theme_portfolio_6col');

function theme_portfolio_6col( $atts, $content = null)
{

	extract(shortcode_atts(
        array(
			'show_posts' => '6',
			'pcat' => ''
    ), $atts));
	
	$output = '<div>'."\n";
	if($content) { $output .= '<p>'.theme_remove_autop(stripslashes($content)).'</p>'."\n"; }
	$output .= theme_portfolio_6col_loop($pcat, $show_posts);
	$output .= '</div>'."\n";

	return $output;

}

function theme_portfolio_6col_loop($pcat, $show_posts)
{
	$args = array( 
				'post_type' => 'portfolio-type',
				'portfolio-category' => $pcat,
				'posts_per_page' => $show_posts,
				'post_status' => 'publish'
				); 
	$query =  new WP_Query(array('post_type' => 'portfolio-type', 'portfolio-category' => $pcat, 'showposts' => $show_posts, 'order' => 'DESC'));

	$loop_count = 0;
	
	$default_url= get_template_directory_uri();
	
	$output = '<div class="row">'."\n";

	while ($query->have_posts()) { $query->the_post();

		$post_id = get_the_id();
  

		$attr = array(
			'class'	=> "bordered_img last",
		);
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
		$title = get_the_title();
		$icon = get_post_meta($post_id, 'port-icon', 1);
		$descr = get_post_meta($post_id, 'port-descr', 1);
		

			$output .= '<div class="span2 block" style="margin-bottom:20px !important;">'."\n";
				$output .= '<div class="view view-first">'."\n";
					$output .= '<a href="'.$large_image_url[0].'" rel="prettyPhoto"><img src="'.$large_image_url[0].'" alt="" /></a>'."\n";
					$output .= '<div class="mask">'."\n";
						$output .= '<a href="'.$large_image_url[0].'" rel="prettyPhoto" class="info"></a>'."\n";
						$output .= '<a href="'.get_permalink().'" class="link"></a>'."\n";
					$output .= '</div>'."\n";
				$output .= '</div>'."\n";
				$output .= '<div class="descr">'."\n";
					$output .= '<h6><i class="'.$icon.'"></i> <a href="'.get_permalink().'">'.$title.'</a></h6>'."\n";
					$output .= '<p class="clo">'.$descr.'</p>'."\n";
				$output .= '</div>'."\n";
			$output .= '</div>'."\n";


	}
	wp_reset_postdata();

	$output .= '</div>'."\n";

	return $output;
}












add_shortcode('4col_portfolio', 'theme_portfolio_4col');

function theme_portfolio_4col( $atts, $content = null)
{

	extract(shortcode_atts(
        array(
			'show_posts' => '4',
			'pcat' => ''
    ), $atts));
	
	$output = '<div>'."\n";
	if($content) { $output .= '<p>'.theme_remove_autop(stripslashes($content)).'</p>'."\n"; }
	$output .= theme_portfolio_4col_loop($pcat, $show_posts);
	$output .= '</div>'."\n";

	return $output;

}

function theme_portfolio_4col_loop($pcat, $show_posts)
{
	$args = array( 
				'post_type' => 'portfolio-type',
				'portfolio-category' => $pcat,
				'posts_per_page' => $show_posts,
				'post_status' => 'publish'
				); 
	$query =  new WP_Query(array('post_type' => 'portfolio-type', 'portfolio-category' => $pcat, 'showposts' => $show_posts, 'order' => 'DESC'));

	$loop_count = 0;
	
	$default_url= get_template_directory_uri();
	
	$output = '<div class="row">'."\n";

	while ($query->have_posts()) { $query->the_post();

		$post_id = get_the_id();
  

		$attr = array(
			'class'	=> "bordered_img last",
		);
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
		$title = get_the_title();
		$icon = get_post_meta($post_id, 'port-icon', 1);
		$descr = get_post_meta($post_id, 'port-descr', 1);
		

			$output .= '<div class="span3 block" style="margin-bottom:20px !important;">'."\n";
				$output .= '<div class="view view-first">'."\n";
					$output .= '<a href="'.$large_image_url[0].'" rel="prettyPhoto"><img src="'.$large_image_url[0].'" alt="" /></a>'."\n";
					$output .= '<div class="mask">'."\n";
						$output .= '<a href="'.$large_image_url[0].'" rel="prettyPhoto" class="info"></a>'."\n";
						$output .= '<a href="'.get_permalink().'" class="link"></a>'."\n";
					$output .= '</div>'."\n";
				$output .= '</div>'."\n";
				$output .= '<div class="descr">'."\n";
					$output .= '<h6><i class="'.$icon.'"></i> <a href="'.get_permalink().'">'.$title.'</a></h6>'."\n";
					$output .= '<p class="clo">'.$descr.'</p>'."\n";
				$output .= '</div>'."\n";
			$output .= '</div>'."\n";


	}
	wp_reset_postdata();

	$output .= '</div>'."\n";

	return $output;
}









add_shortcode('3col_portfolio', 'theme_portfolio_3col');

function theme_portfolio_3col( $atts, $content = null)
{

	extract(shortcode_atts(
        array(
			'show_posts' => '3',
			'pcat' => ''
    ), $atts));
	
	$output = '<div>'."\n";
	if($content) { $output .= '<p>'.theme_remove_autop(stripslashes($content)).'</p>'."\n"; }
	$output .= theme_portfolio_3col_loop($pcat, $show_posts);
	$output .= '</div>'."\n";

	return $output;

}

function theme_portfolio_3col_loop($pcat, $show_posts)
{
	$args = array( 
				'post_type' => 'portfolio-type',
				'portfolio-category' => $pcat,
				'posts_per_page' => $show_posts,
				'post_status' => 'publish'
				); 
	$query =  new WP_Query(array('post_type' => 'portfolio-type', 'portfolio-category' => $pcat, 'showposts' => $show_posts, 'order' => 'DESC'));

	$loop_count = 0;
	
	$default_url= get_template_directory_uri();
	
	$output = '<div class="row">'."\n";

	while ($query->have_posts()) { $query->the_post();

		$post_id = get_the_id();
  

		$attr = array(
			'class'	=> "bordered_img last",
		);
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
		$title = get_the_title();
		$icon = get_post_meta($post_id, 'port-icon', 1);
		$descr = get_post_meta($post_id, 'port-descr', 1);
		

			$output .= '<div class="span4 block" style="margin-bottom:20px !important;">'."\n";
				$output .= '<div class="view view-first">'."\n";
					$output .= '<a href="'.$large_image_url[0].'" rel="prettyPhoto"><img src="'.$large_image_url[0].'" alt="" /></a>'."\n";
					$output .= '<div class="mask">'."\n";
						$output .= '<a href="'.$large_image_url[0].'" rel="prettyPhoto" class="info"></a>'."\n";
						$output .= '<a href="'.get_permalink().'" class="link"></a>'."\n";
					$output .= '</div>'."\n";
				$output .= '</div>'."\n";
				$output .= '<div class="descr">'."\n";
					$output .= '<h6><i class="'.$icon.'"></i> <a href="'.get_permalink().'">'.$title.'</a></h6>'."\n";
					$output .= '<p class="clo">'.$descr.'</p>'."\n";
				$output .= '</div>'."\n";
			$output .= '</div>'."\n";


	}
	wp_reset_postdata();

	$output .= '</div>'."\n";

	return $output;
}













add_shortcode('2col_portfolio', 'theme_portfolio_2col');

function theme_portfolio_2col( $atts, $content = null)
{

	extract(shortcode_atts(
        array(
			'show_posts' => '2',
			'pcat' => ''
    ), $atts));
	
	$output = '<div>'."\n";
	if($content) { $output .= '<p>'.theme_remove_autop(stripslashes($content)).'</p>'."\n"; }
	$output .= theme_portfolio_2col_loop($pcat, $show_posts);
	$output .= '</div>'."\n";

	return $output;

}

function theme_portfolio_2col_loop($pcat, $show_posts)
{
	$args = array( 
				'post_type' => 'portfolio-type',
				'portfolio-category' => $pcat,
				'posts_per_page' => $show_posts,
				'post_status' => 'publish'
				); 
	$query =  new WP_Query(array('post_type' => 'portfolio-type', 'portfolio-category' => $pcat, 'showposts' => $show_posts, 'order' => 'DESC'));

	$loop_count = 0;
	
	$default_url= get_template_directory_uri();
	
	$output = '<div class="row">'."\n";

	while ($query->have_posts()) { $query->the_post();

		$post_id = get_the_id();
  

		$attr = array(
			'class'	=> "bordered_img last",
		);
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
		$title = get_the_title();
		$icon = get_post_meta($post_id, 'port-icon', 1);
		$descr = get_post_meta($post_id, 'port-descr', 1);
		

			$output .= '<div class="span6 block" style="margin-bottom:20px !important;">'."\n";
				$output .= '<div class="view view-first">'."\n";
					$output .= '<a href="'.$large_image_url[0].'" rel="prettyPhoto"><img src="'.$large_image_url[0].'" alt="" /></a>'."\n";
					$output .= '<div class="mask">'."\n";
						$output .= '<a href="'.$large_image_url[0].'" rel="prettyPhoto" class="info"><img src="'.$default_url.'/assets/img/plus.png" alt="" /></a>'."\n";
						$output .= '<a href="'.get_permalink().'" class="link"><img src="'.$default_url.'/assets/img/link.png" alt="Visit link" /></a>'."\n";
					$output .= '</div>'."\n";
				$output .= '</div>'."\n";
				$output .= '<div class="descr">'."\n";
					$output .= '<h6><i class="'.$icon.'"></i> <a href="'.get_permalink().'">'.$title.'</a></h6>'."\n";
					$output .= '<p class="clo">'.$descr.'</p>'."\n";
				$output .= '</div>'."\n";
			$output .= '</div>'."\n";


	}
	wp_reset_postdata();

	$output .= '</div>'."\n";

	return $output;
}



function well_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="well slider_area">
				<p style="margin-bottom:0px">'.$content.'</p>
			</div>
	';
	return $code;
}
add_shortcode('well', 'well_f');


function well2_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
		<div class="sidebar">
			<div class="well">
				<p style="margin-bottom:0px">'.$content.'</p>
			</div>
		</div>
	';
	return $code;
}
add_shortcode('well2', 'well2_f');





function nivoimg_f($atts, $content = null) {
	extract( shortcode_atts( array(  "caption" => '', "url" => ''), $atts));
	
	$code = '
			<img src="'.$url.'"  title="'.$caption.'" alt="" />
	';
	return $code;
}
add_shortcode('nivoimg', 'nivoimg_f');


function nivo_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="slider_area standard">
				<div class="theme-default ">
					<div id="slider" class="nivoSlider">
						'.do_shortcode($content).'
					</div>
				</div>
			</div>
	';
	return $code;
}
add_shortcode('nivo', 'nivo_f');






function pricet_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE', "btn" => 'Register', "price" => '$25', "url" => 'http://www.google.com'), $atts));
	
	$code = '
			<div class="price">
				<div class="well">
					<h6 class="sep_bg"><span class="label label-inverse">'.$title.'</span></h6>
					<h1><p class="label label-inverse">'.$price.'</p></h1>
					'.$content.'
					<p style="margin-bottom:0px;"><a class="btn btn-small" href="'.$url.'">'.$btn.'</a></p>
				</div>
			</div>
	';
	return $code;
}
add_shortcode('pricet', 'pricet_f');



function active_pricet_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE', "btn" => 'Register', "price" => '$25', "url" => 'http://www.google.com'), $atts));
	
	$code = '
			<div class="price price-active">
				<div class="well">
					<h6 class="sep_bg"><span class="label label-inverse">'.$title.'</span></h6>
					<h1><p class="label label-inverse">'.$price.'</p></h1>
					'.$content.'
					<p style="margin-bottom:0px;"><a class="btn btn-small" href="'.$url.'">'.$btn.'</a></p>
				</div>
			</div>
	';
	return $code;
}
add_shortcode('active_pricet', 'active_pricet_f');





function visible_desktop_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="visible-desktop">'.do_shortcode($content).'</div>
	';
	return $code;
}
add_shortcode('visible_desktop', 'visible_desktop_f');


function hidden_tablet_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="hidden-tablet">'.do_shortcode($content).'</div>
	';
	return $code;
}
add_shortcode('hidden_tablet', 'hidden_tablet_f');






function toggle1_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle1">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec1">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle1', 'toggle1_f');




function toggle2_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle2">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec2">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle2', 'toggle2_f');







function toggle3_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle3">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec3">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle3', 'toggle3_f');









function toggle4_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle4">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec4">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle4', 'toggle4_f');








function toggle5_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle5">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec5">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle5', 'toggle5_f');








function toggle6_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle6">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec6">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle6', 'toggle6_f');









function toggle7_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle7">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec7">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle7', 'toggle7_f');








function toggle8_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle8">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec8">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle8', 'toggle8_f');








function toggle9_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle9">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec9">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle9', 'toggle9_f');







function toggle10_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle10">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec10">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle10', 'toggle10_f');









function toggle11_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle11">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec11">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle11', 'toggle11_f');








function toggle12_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle12">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec12">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle12', 'toggle12_f');







function toggle13_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle13">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec13">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle13', 'toggle13_f');









function toggle14_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle14">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec14">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle14', 'toggle14_f');








function toggle15_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle15">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec15">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle15', 'toggle15_f');








function toggle16_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle16">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec16">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle16', 'toggle16_f');







function toggle17_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle17">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec17">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle17', 'toggle17_f');








function toggle18_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle18">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec18">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle18', 'toggle18_f');








function toggle19_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle19">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec19">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle19', 'toggle19_f');








function toggle20_f($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'SOME TITLE'), $atts));
	
	$code = '
			<h5><a href="#" class="toggle-header" id="toggle20">'.$title.'</a></h5>
			<div class="toggle-content" style="display:none" id="togglec20">'.$content.'</div>
	';
	return $code;
}
add_shortcode('toggle20', 'toggle20_f');











function h1_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	$code = '
			<h1 class="sep_bg">'.do_shortcode($content).'</h1>
	';
	return $code;
}
add_shortcode('h1', 'h1_f');


function h2_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	$code = '
			<h2 class="sep_bg">'.do_shortcode($content).'</h2>
	';
	return $code;
}
add_shortcode('h2', 'h2_f');


function h3_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	$code = '
			<h3 class="sep_bg">'.do_shortcode($content).'</h3>
	';
	return $code;
}
add_shortcode('h3', 'h3_f');


function h4_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	$code = '
			<h4 class="sep_bg">'.do_shortcode($content).'</h4>
	';
	return $code;
}
add_shortcode('h4', 'h4_f');



function h5_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	$code = '
			<h5 class="sep_bg">'.do_shortcode($content).'</h5>
	';
	return $code;
}
add_shortcode('h5', 'h5_f');


function h6_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	$code = '
			<h6 class="sep_bg">'.do_shortcode($content).'</h6>
	';
	return $code;
}
add_shortcode('h6', 'h6_f');



/* separator */
function veles_separator($atts, $content = null) {
	extract(shortcode_atts(array('margin' => '0px'),$atts));

	return '<div class="separator clear" style="margin-top:'.$margin.'"></div>';
}
add_shortcode('separator', 'veles_separator');





function tslider_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="testimonialslider">
            <ul class="slides">'.do_shortcode($content).'</ul>
			</div>
	';
	return $code;
}

add_shortcode('tslider', 'tslider_f');







function row1_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="row">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('row1', 'row1_f');

function row2_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="row">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('row2', 'row2_f');




function row_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="row">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('row', 'row_f');


function span1_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span1">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span1', 'span1_f');


function span2_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span2">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span2', 'span2_f');


function span3_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span3">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span3', 'span3_f');



function span4_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span4">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span4', 'span4_f');



function span5_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span5">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span5', 'span5_f');


function span6_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span6">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span6', 'span6_f');


function span7_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span7">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span7', 'span7_f');


function span8_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span8">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span8', 'span8_f');


function span9_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span9">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span9', 'span9_f');


function span10_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span10">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span10', 'span10_f');


function span11_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span11">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span11', 'span11_f');


function span12_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="span12">'.do_shortcode($content).'</div>
	';
	return $code;
}

add_shortcode('span12', 'span12_f');




function section_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<section>'.do_shortcode($content).'</section>
	';
	return $code;
}

add_shortcode('section', 'section_f');



function mybtn_f($atts, $content = null) {
	extract( shortcode_atts( array(  "url" => '#'), $atts));
	
	$code = '
				<a href="'.do_shortcode($url).'" class="mybutton"><span>'.do_shortcode($content).'</span></a>
	';
	return $code;
}
add_shortcode('mybtn', 'mybtn_f');





function btn_f($atts, $content = null) {
	extract( shortcode_atts( array(  "url" => '#', "size" => '' , "style" => '' ), $atts));
	
	$code = '
				<a href="'.do_shortcode($url).'" class="btn '.do_shortcode($size).' '.do_shortcode($style).'">'.do_shortcode($content).'</a>
	';
	return $code;
}
add_shortcode('btn', 'btn_f');


function icon_f($atts, $content = null) {
	extract( shortcode_atts( array(  "image" => '#', "style" => '' ), $atts));
	
	$code = '
				<i class="'.do_shortcode($image).' '.do_shortcode($style).'"></i>
	';
	return $code;
}
add_shortcode('icon', 'icon_f');


function hr_f($atts, $content = null) {
	extract( shortcode_atts( array( "style" => '' ), $atts));
	
	$code = '
				<hr class="'.do_shortcode($style).'" />
	';
	return $code;
}
add_shortcode('hr', 'hr_f');







function badge_f($atts, $content = null) {
	extract( shortcode_atts( array( "style" => '' ), $atts));
	
	$code = '
				<span class="badge '.do_shortcode($style).'" >'.do_shortcode($content).'</span>
	';
	return $code;
}
add_shortcode('badge', 'badge_f');



function label_f($atts, $content = null) {
	extract( shortcode_atts( array( "style" => '' ), $atts));
	
	$code = '
				<span class="label '.do_shortcode($style).'" >'.do_shortcode($content).'</span>
	';
	return $code;
}
add_shortcode('label', 'label_f');






function alert_f($atts, $content = null) {
	extract( shortcode_atts( array( "style" => '' ), $atts));
	
	$code = '
				<div class="alert '.do_shortcode($style).'" ><a class="close" data-dismiss="alert">&times;</a> '.do_shortcode($content).'</div>
	';
	return $code;
}
add_shortcode('alert', 'alert_f');




function progress_bar_f($atts, $content = null) {
	extract( shortcode_atts( array( "style" => '',"width" => '20%' ), $atts));
	
	$code = '
				<div class="progress progress-striped active '.do_shortcode($style).'" >
					<div class="bar" style="width: '.do_shortcode($width).'" ></div>
				</div>
	';
	return $code;
}
add_shortcode('progress_bar', 'progress_bar_f');









/* width: 940px */
function one_column($atts, $content = null) {
	extract( shortcode_atts( array( "align" => 'left', "margin" => '1', "last" => '0' ), $atts));
	
	$code = '';
	
	if( ($margin == "1") && ($last == "0")) $code ='<div class="span-24">'.do_shortcode($content).'</div>';	
	if( ($margin == "0") && ($last == "1")) $code ='<div class="span-24 notopmargin last">'.do_shortcode($content).'</div>';		
	if( ($last == "1") && ($margin == "1")) $code ='<div class="span-24 last">'.$content.'</div>';	
	if( ($margin == "0")  && ($last == "0")) $code ='<div class="span-24 notopmargin">'.do_shortcode($content).'</div>';		
	
	
	return $code;
}

add_shortcode('one', 'one_column');


/* width: 460px */
function one_half_column($atts, $content = null) {
	extract( shortcode_atts( array( "align" => 'left', "margin" => '1', "last" => '0' ), $atts));
	
	$code = '';
	
	if( ($margin == "1") && ($last == "0")) $code ='<div class="span-12">'.do_shortcode($content).'</div>';	
	if( ($margin == "0") && ($last == "1")) $code ='<div class="span-12 notopmargin last">'.do_shortcode($content).'</div>';		
	if( ($last == "1") && ($margin == "1")) $code ='<div class="span-12 last">'.do_shortcode($content).'</div>';	
	if( ($margin == "0")  && ($last == "0"))$code ='<div class="span-12 notopmargin">'.do_shortcode($content).'</div>';		
	
	
	return $code;
}

add_shortcode('one_half', 'one_half_column');

/* width: 620px */
function two_third_column($atts, $content = null) {
	extract( shortcode_atts( array( "align" => 'left', "margin" => '1', "last" => '0' ), $atts));
	
	$code = '';
	
	if( ($margin == "1") && ($last == "0")) $code ='<div class="span-16">'.do_shortcode($content).'</div>';	
	if( ($margin == "0") && ($last == "1")) $code ='<div class="span-16 notopmargin last">'.do_shortcode($content).'</div>';		
	if( ($last == "1") && ($margin == "1")) $code ='<div class="span-16 last">'.do_shortcode($content).'</div>';	
	if( ($margin == "0")  && ($last == "0")) $code ='<div class="span-16 notopmargin">'.do_shortcode($content).'</div>';		
	
	
	return $code;
}

add_shortcode('two_third', 'two_third_column');


/* width: 300px */
function one_third_column($atts, $content = null) {
	extract( shortcode_atts( array( "align" => 'left', "margin" => '1', "last" => '0' ), $atts));
	
	$code = '';
	
	if( ($margin == "1") && ($last == "0")) $code ='<div class="span-8">'.do_shortcode($content).'</div>';	
	if( ($margin == "0") && ($last == "1")) $code ='<div class="span-8 notopmargin last">'.do_shortcode($content).'</div>';		
	if( ($last == "1") && ($margin == "1")) $code ='<div class="span-8 last">'.do_shortcode($content).'</div>';	
	if( ($margin == "0")  && ($last == "0")) $code ='<div class="span-8 notopmargin">'.do_shortcode($content).'</div>';		
	
	
	return $code;
}

add_shortcode('one_third', 'one_third_column');


/* width: 220px */
function one_fourth_column($atts, $content = null) {
	extract( shortcode_atts( array( "align" => 'left', "margin" => '1', "last" => '0' ), $atts));
	
	$code = '';
	
	if( ($margin == "1") && ($last == "0")) $code ='<div class="span-6">'.do_shortcode($content).'</div>';	
	if( ($margin == "0") && ($last == "1")) $code ='<div class="span-6 notopmargin last">'.do_shortcode($content).'</div>';		
	if( ($last == "1") && ($margin == "1")) $code ='<div class="span-6 last">'.do_shortcode($content).'</div>';	
	if( ($margin == "0")  && ($last == "0")) $code ='<div class="span-6 notopmargin">'.do_shortcode($content).'</div>';		
	
	
	return $code;
}

add_shortcode('one_fourth', 'one_fourth_column');


/* width: 700px */
function three_fourth_column($atts, $content = null) {
	extract( shortcode_atts( array( "align" => 'left', "margin" => '1', "last" => '0' ), $atts));
	
	$code = '';
	
	if( ($margin == "1") && ($last == "0")) $code ='<div class="span-18">'.do_shortcode($content).'</div>';	
	if( ($margin == "0") && ($last == "1")) $code ='<div class="span-18 notopmargin last">'.do_shortcode($content).'</div>';		
	if( ($last == "1") && ($margin == "1")) $code ='<div class="span-18 last">'.do_shortcode($content).'</div>';	
	if( ($margin == "0")  && ($last == "0")) $code ='<div class="span-18 notopmargin">'.do_shortcode($content).'</div>';		
	
	
	return $code;
}

add_shortcode('three_fourth', 'three_fourth_column');
/* END COLUMNS */


function welcome_f($atts, $content = null) {
	extract( shortcode_atts( array(  "header" => '' ), $atts));
	
	$code = '
				<h1 class="sep_bg welcome2">'.do_shortcode($content).'</h1>
	';
	return $code;
}
add_shortcode('welcome', 'welcome_f');



function welcome1_f($atts, $content = null) {
	extract( shortcode_atts( array(  "header" => '' ), $atts));
	
	$code = '
				<h1 class="sep_bg welcome1">'.do_shortcode($content).'</h1>
	';
	return $code;
}
add_shortcode('welcome1', 'welcome1_f');





function action_block_f($atts, $content = null) {
	extract( shortcode_atts( array(  "header" => 'Header here', "url" => 'http://www.google.com',"btn" => 'Purchase!' ), $atts));
	
	$code = '
			<div class="sep_bg intro visible-desktop" style="margin-top:10px;">
				<h3 class="colored">'.do_shortcode($header).' <a class="btn btn-small btn-info pull-right" href="'.do_shortcode($url).'"> '.do_shortcode($btn).'</a></h3>
			</div>';
	return $code;
}
add_shortcode('action_block', 'action_block_f');




function m_button_f($atts, $content = null) {
	extract( shortcode_atts( array(  "url" => '#', "price" => '35$',"header1" => 'Purchase theme now &amp;', "header2" => 'Download right now' ), $atts));
	
	$code = '
			<div class="span-8 last notopmargin">
				<a href="'.do_shortcode($url).'" class="a-btn">
					<span class="a-btn-text"><small>'.do_shortcode($header1).'</small> '.do_shortcode($header2).'</span> 
				</a>
			</div>
	';
	return $code;
}
add_shortcode('m_button', 'm_button_f');



function style_image_f($atts, $content=null)
{	
	extract(shortcode_atts(array(
		'size' => 'one_fourth',
		'image' => '',
		'title' => 'Some title',
		'alt' => 'Image description or alternate text.',
		'zoom' => '1',
		'link' => '0',
		'url' => 'http://www.google.com',
		
	), $atts));
	
	$default_url= get_template_directory_uri();
	if($zoom == '0') 
	{
		$zoom1 = 'noinfo';
	}
	
	if($link == '0') 
	{
		$link1 = 'nolink';
	}
	
	if($size == 'one_six') 
	{
		$size1 = 'span2';
	}
	if($size == 'one_fourth') 
	{
		$size1 = 'span3';
	}
	if($size == 'one_third') 
	{
		$size1 = 'span4';
	}
	if($size == 'two_third') 
	{
		$size1 = 'span8';
	}
	if($size == 'one_half') 
	{
		$size1 = 'span6';
	}
	if($size == 'one') 
	{
		$size1 = 'span12';
	}
	
	$output .= '<div class="'.$size1.' block my_img">'."\n";
	$output .= '<div class="view view-first '.$link1.' '.$zoom1.'">'."\n";
	$output .=	'<a href="'.$image.'" rel="prettyPhoto"><img src="'.$image.'" alt="" /></a>'."\n";
		$output .=	'<div class="mask">'."\n";
			if(($link == '0') & ($zoom == '0')){ $output .=	''."\n";}
			if(($link == '1') & ($zoom == '0')){ $output .=	'<a href="'.$url.'" class="link"></a>'."\n";}
			
			if(($link == '1') & ($zoom == '1')){ $output .=	'<a href="'.$image.'" rel="prettyPhoto" class="info"></a>'."\n";
												  $output .=	'<a href="'.$url.'" class="link"></a>'."\n";}
			
			if(($link == '0') & ($zoom == '1')){ $output .=	'<a href="'.$image.'" rel="prettyPhoto" class="info" style="margin-left:-18px !important;"></a>'."\n";}
		$output .=	'</div>'."\n";
		$output .=	'</div>'."\n";
	$output .=	'</div>';

	return $output;
}

add_shortcode('style_image', 'style_image_f');



function spec_block1($atts, $content = null) {
	extract( shortcode_atts( array('icon' => 'size', 'url' => '#', 'icon_src' => '#', 'title' => 'Some Title', 'icon' => '0',), $atts));
	
	if ($icon =='1'){
	$code = '
		<img class="servise_icon" src="'.$icon_src.'" alt=" "/>
		<h4><a class="link" href="'.$url.'">'.$title.'</a></h4>
		<p class="small">'.do_shortcode($content).'</p>
	';} else {
		$code = '
		<h4><a class="link" href="'.$url.'">'.$title.'</a></h4>
		<p class="small">'.do_shortcode($content).'</p>
	';
	}
	return $code;
}

add_shortcode('spec_block', 'spec_block1');



function small_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<span class="small-italic">'.do_shortcode($content).'</span>
	';
	return $code;
}

add_shortcode('small', 'small_f');


function inner_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<section class="inner_section">'.do_shortcode($content).'</section>
	';
	return $code;
}

add_shortcode('inner_section', 'inner_f');




function testimonialrotator_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<div class="sidebar">
				<div class="testimonialrotator">
					'.do_shortcode($content).'
				</div>
			</div>
	';
	return $code;
}

add_shortcode('testimonialrotator', 'testimonialrotator_f');


function testimonial_f($atts, $content = null) {
	extract( shortcode_atts( array("author" => 'Jhon Doe', ), $atts));
	
	$code = '
			<div class="testimonial">
				<div class="main_testimonial"><div class="blockquote">'.do_shortcode($content).'</div></div>
				<div class="the-author">'.$author.'</div>
			</div>
	';
	return $code;
}

add_shortcode('testimonial', 'testimonial_f');




function break_f($atts, $content = null) {
	extract( shortcode_atts( array("top" => '0px', "bottom" => '0px', ), $atts));
	
	$code = '
			</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			<div style="margin-top: '.$top.'; margin-bottom: '.$bottom.'">
			'.do_shortcode($content).'
			</div>
			<div class="main_content_area">
			<div class="container">
			<div class="row">
			<div class="span12">
			<div>
			<div>
			<div>
	';
	return $code;
}

add_shortcode('break', 'break_f');










function intro_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<hr class="dash">
			<div class="intro">
				<p style="margin-bottom:10px;"><em>'.do_shortcode($content).'</em></p>
			</div>
			<hr class="dash" style="margin-bottom:20px !important;">
	';
	return $code;
}

add_shortcode('intro', 'intro_f');


function br_f($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<br>
	';
	return $code;
}

add_shortcode('br', 'br_f');










/* Dropcaps */

function dropcap11($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<span class="dropcap">'.do_shortcode($content).'</span>
	';
	return $code;
}

add_shortcode('dropcap1', 'dropcap11');

function dropcap22($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<span class="dropcap2">'.do_shortcode($content).'</span>
	';
	return $code;
}

add_shortcode('dropcap2', 'dropcap22');


function dropcap33($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<span class="dropcap3">'.do_shortcode($content).'</span>
	';
	return $code;
}

add_shortcode('dropcap3', 'dropcap33');

function dropcap44($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<span class="dropcap4">'.do_shortcode($content).'</span>
	';
	return $code;
}

add_shortcode('dropcap4', 'dropcap44');


function dropcap55($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<span class="dropcap5">'.do_shortcode($content).'</span>
	';
	return $code;
}

add_shortcode('dropcap5', 'dropcap55');

/* /Dropcaps */

/* Blockquotes */

function blockquote_f($atts, $content = null) {
	extract( shortcode_atts( array('author' => ''), $atts));
	
	$code = '
			<blockquote><p>'.do_shortcode($content).'</p><small>'.do_shortcode($author).'</small></blockquote>
	';
	return $code;
}

add_shortcode('blockquote', 'blockquote_f');


function blockquote11($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<p class="blockquote1">'.do_shortcode($content).'</p>
	';
	return $code;
}

add_shortcode('blockquote1', 'blockquote11');

function blockquote22($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<p class="blockquote2">'.do_shortcode($content).'</p>
	';
	return $code;
}

add_shortcode('blockquote2', 'blockquote22');


function blockquote33($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<p class="blockquote3">'.do_shortcode($content).'</p>
	';
	return $code;
}

add_shortcode('blockquote3', 'blockquote33');

function blockquote44($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<p class="blockquote4">'.do_shortcode($content).'</p>
	';
	return $code;
}

add_shortcode('blockquote4', 'blockquote44');


function blockquote55($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<p class="blockquote5">'.do_shortcode($content).'</p>
	';
	return $code;
}

add_shortcode('blockquote5', 'blockquote55');



function blockquote66($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<p class="blockquote6">'.do_shortcode($content).'</p>
	';
	return $code;
}

add_shortcode('blockquote6', 'blockquote66');


function blockquote77($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<p class="blockquote7">'.do_shortcode($content).'</p>
	';
	return $code;
}

add_shortcode('blockquote7', 'blockquote77');


function blockquote88($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<p class="blockquote8">'.do_shortcode($content).'</p>
	';
	return $code;
}

add_shortcode('blockquote8', 'blockquote88');

function blockquote99($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<p class="blockquote9">'.do_shortcode($content).'</p>
	';
	return $code;
}

add_shortcode('blockquote9', 'blockquote99');

/* /Blockquotes */


function awesome_block($atts, $content = null) {
	extract( shortcode_atts( array(  "title" => 'Some Title Here' ), $atts));
	
	$code = '
		<div class="awesome_block">
			<h5>'.do_shortcode($title).'</h5>
			<p class="nobottommargin" style="margin-top:10px;">'.do_shortcode($content).'</p>
		</div>
	';
	return $code;
}

add_shortcode('spec', 'awesome_block');








function coloredd($atts, $content = null) {
	extract( shortcode_atts( array(), $atts));
	
	$code = '
			<span class="colored">'.do_shortcode($content).'</span>
	';
	return $code;
}

add_shortcode('colored', 'coloredd');


function readmoree($atts, $content = null) {
	extract( shortcode_atts( array( "url" =>'#',"margin" =>'1',), $atts));
	
	if($margin == '1') 
	{
		$margin1 = '20px';
	}
	$code = '
			<div style=" margin-top:'.$margin1.'"><a class="button_readmore left"  href="'.do_shortcode($url).'"></a></div>
	';
	return $code;
}

add_shortcode('readmore', 'readmoree');



function skills($atts, $content = null) {
	extract( shortcode_atts( array( "percent" =>'20'), $atts));
	
	$code = '
			<h6>'.do_shortcode($content).'</h6>
			<div class="progress-bar blue stripes">
				<span style="width: '.do_shortcode($percent).'%"></span>
			</div>
	';
	return $code;
}

add_shortcode('skill', 'skills');
