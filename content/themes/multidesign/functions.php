<?php
if ( function_exists('register_sidebar') )
register_sidebars(1,array('name' => 'Ajax Login Popup','before_widget' => '','after_widget' => '','before_title' => '<h2>','after_title' => '</h2>'));
register_sidebars(1,array('name' => 'Ajax Signup Popup','before_widget' => '','after_widget' => '','before_title' => '<h2>','after_title' => '</h2>'));
register_sidebars(1,array('name' => 'Ajax Search Popup','before_widget' => '','after_widget' => '','before_title' => '<h2>','after_title' => '</h2>'));
register_sidebars(1,array('name' => 'Blog - Categories Widget','before_widget' => '','after_widget' => '','before_title' => '<h1>','after_title' => '</h1>'));
register_sidebars(1,array('name' => 'Blog - Archives Widget','before_widget' => '','after_widget' => '','before_title' => '<h1>','after_title' => '</h1>'));
register_sidebars(1,array('name' => 'Sidebar - Right','before_widget' => '','after_widget' => '','before_title' => '<h1>','after_title' => '</h1>'));
register_sidebars(1,array('name' => 'Sidebar - Left','before_widget' => '','after_widget' => '','before_title' => '<h1>','after_title' => '</h1>'));

register_nav_menus(array(
'topmenu' => '',
'footer' => ''
));

//Başlıkları karakter bazlı kısaltma
function the_title_limit($length, $replacer = '...') {
 $string = the_title('','',FALSE);
 if(strlen($string) > $length)
 $string = (preg_match('/^(.*)\W.*$/', substr($string, 0, $length+1), $matches) ? $matches[1] : substr($string, 0, $length)) . $replacer;
 echo $string;
}

// Columb //
add_shortcode( '25', 'sc_25' );
function sc_25( $atts, $content ) {	 
		return '<div class="columbone">'.$content.'</div>';
	return '';
};

// Columb //
add_shortcode( '25and', 'sc_25and' );
function sc_25and( $atts, $content ) {
		return '<div class="columbone-last">'.$content.'</div>';
	return '';
};

// Columb //
add_shortcode( '33', 'sc_33' );
function sc_33( $atts, $content) {
		return '<div class="columbtwo">'.$content.'</div>';
	return '';
};

// Columb //
add_shortcode( '33and', 'sc_33and' );
function sc_33and( $atts, $content ) {
		return '<div class="columbtwo-last">'.$content.'</div>';
	return '';
};

// Columb //
add_shortcode( '50', 'sc_50' );
function sc_50( $atts, $content ) {
		return '<div class="columbthree">'.$content.'</div>';
	return '';
};

// Columb //
add_shortcode( '50and', 'sc_50and' );
function sc_50and( $atts, $content ) {
		return '<div class="columbthree-last">'.$content.'</div>';
	return '';
};

// Columb //
add_shortcode( 'b50', 'sc_b50' );
function sc_b50( $atts, $content) {
		return '<div class="bcolumbone">'.$content.'</div>';
	return '';
};

// Columb //
add_shortcode( 'b50last', 'sc_b50last' );
function sc_b50last( $atts, $content ) {
		return '<div class="bcolumbone-last">'.$content.'</div>';
	return '';
};


// Team //

add_shortcode( 'team', 'sc_team' );

function sc_team( $atts, $content ) {
		return '<div class="grid_5 about" style=" margin-top:6px; padding-bottom:30px;">'.$content.'</div>';
	return '';
};

// About Us //

add_shortcode( 'about', 'sc_about' );

function sc_about( $atts, $content ) {
		return '<div class="grid_11 post-blog-read" style="margin:2px 0px 0px 0px;">'.$content.'</div>';
	return '';
};

// Services //

add_shortcode( 'services', 'sc_services' );

function sc_services( $atts, $content) {
		return '<div class="grid_8 services">'.$content.'</div>';
	return '';
};

// Contact //

add_shortcode( 'iframe', 'sc_iframe' );

function sc_iframe( $atts, $content) {
		return '<div style="margin:8px 0px 0px 20px;" class="grid_5">'.$content.'</div>';
	return '';
};

add_shortcode( 'contact', 'sc_contact' );

function sc_contact( $atts, $content) {
		return '<div class="grid_11 post-blog-read" style="margin:2px 0px 0px 0px;">'.$content.'</div>';
	return '';
};

// Chart //
function chart_shortcode( $atts ) {
	extract(shortcode_atts(array(
	    'data' => '',
	    'colors' => '',
	    'size' => '400x200',
	    'bg' => 'ffffff',
	    'title' => '',
	    'labels' => '',
	    'advanced' => '',
	    'type' => 'pie'
	), $atts));

	switch ($type) {
		case 'line' :
			$charttype = 'lc'; break;
		case 'xyline' :
			$charttype = 'lxy'; break;
		case 'sparkline' :
			$charttype = 'ls'; break;
		case 'meter' :
			$charttype = 'gom'; break;
		case 'scatter' :
			$charttype = 's'; break;
		case 'venn' :
			$charttype = 'v'; break;
		case 'pie' :
			$charttype = 'p3'; break;
		case 'pie2d' :
			$charttype = 'p'; break;
		default :
			$charttype = $type;
		break;
	}

	if ($title) $string .= '&chtt='.$title.'';
	if ($labels) $string .= '&chl='.$labels.'';
	if ($colors) $string .= '&chco='.$colors.'';
	$string .= '&chs='.$size.'';
	$string .= '&chd=t:'.$data.'';
	$string .= '&chf='.$bg.'';

	return '<img title="'.$title.'" src="http://chart.apis.google.com/chart?cht='.$charttype.''.$string.$advanced.'" alt="'.$title.'" style="margin-top:20px; margin-bottom:20px;" />';
}
add_shortcode('chart', 'chart_shortcode');

// Short Link //
function subzane_shorturl($atts) {
	extract(shortcode_atts(array(
		'url' => '',
		'name' => '',
), $atts));
$request = 'http://u.nu/unu-api-simple?url=' . urlencode($url);
$short_url = file_get_contents($request);
	if (substr($short_url, 0, 4) == 'http')    {
		$name = empty($name)?$short_url:$name;
		return '<a href="'.$short_url.'">'.$name.'</a>';
	} else {
		$name = empty($name)?$url:$name;
		return '<a href="'.$url.'">'.$name.'</a>';
	}
}
add_shortcode('shorturl', 'subzane_shorturl');

// Not //
add_shortcode( 'note', 'sc_note' );

function sc_note( $atts, $content = null ) {
	 if ( current_user_can( 'publish_posts' ) )
		return '<div class="note">'.$content.'</div>';
	return '';
}

// Buttons //

//------------------------------------------------------------------//
// Big Button 1 //
function sc_downloadButton($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon-music.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big1', 'sc_downloadButton');
//------------------------------------------------------------------//
// Big Button 2 //
function sc_downloadButton2($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon-picture.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big2', 'sc_downloadButton2');
//------------------------------------------------------------------//
// Big Button 2 //
function sc_downloadButton3($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon-video.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big3', 'sc_downloadButton3');
//------------------------------------------------------------------//
// Big Button 4 //
function sc_downloadButton4($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon-okay.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big4', 'sc_downloadButton4');
//------------------------------------------------------------------//
// Big Button 4 //
function sc_downloadButton5($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon1.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big5', 'sc_downloadButton5');
//------------------------------------------------------------------//
// Big Button 6 //
function sc_downloadButton6($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon2.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big6', 'sc_downloadButton6');
//------------------------------------------------------------------//
// Big Button 7 //
function sc_downloadButton7($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon3.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big7', 'sc_downloadButton7');
//------------------------------------------------------------------//
// Big Button 8 //
function sc_downloadButton8($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon4.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big8', 'sc_downloadButton8');
//------------------------------------------------------------------//
// Big Button 9 //
function sc_downloadButton9($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon5.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big9', 'sc_downloadButton9');
//------------------------------------------------------------------//
// Big Button 10 //
function sc_downloadButton10($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon6.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big10', 'sc_downloadButton10');
//------------------------------------------------------------------//
// Big Button 11 //
function sc_downloadButton11($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon7.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big11', 'sc_downloadButton11');
//------------------------------------------------------------------//

// Big Button 12 //
function sc_downloadButton12($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon8.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big12', 'sc_downloadButton12');
//------------------------------------------------------------------//
// Big Button 13 //
function sc_downloadButton13($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon9.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big13', 'sc_downloadButton13');
//------------------------------------------------------------------//
// Big Button 14 //
function sc_downloadButton14($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' grey-button" style=" margin-left:5px; margin-right:10px"><span class="grey-right"></span><img src="wp-content/themes/multidesign/image/theme/icon10.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('big14', 'sc_downloadButton14');
//------------------------------------------------------------------//
// Color 1 //
function sc_downloadButton15($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' green-button" style=" margin-left:5px; margin-right:10px"><span class="green-right"></span><img src="wp-content/themes/multidesign/image/theme/login-icon.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('color1', 'sc_downloadButton15');
//------------------------------------------------------------------//
// Color 2 //
function sc_downloadButton16($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' red2-button" style=" margin-left:5px; margin-right:10px"><span class="red2-right"></span><img src="wp-content/themes/multidesign/image/theme/login-icon.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('color2', 'sc_downloadButton16');
//------------------------------------------------------------------//
// Color 3 //
function sc_downloadButton17($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' brown-button" style=" margin-left:5px; margin-right:10px"><span class="brown-right"></span><img src="wp-content/themes/multidesign/image/theme/login-icon.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('color3', 'sc_downloadButton17');
//------------------------------------------------------------------//
// Color 4 //
function sc_downloadButton18($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' gold-button" style=" margin-left:5px; margin-right:10px"><span class="gold-right"></span><img src="wp-content/themes/multidesign/image/theme/login-icon.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('color4', 'sc_downloadButton18');
//------------------------------------------------------------------//
// Color 5 //
function sc_downloadButton19($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="generalbutton">
<a href="' . $url . '"class="' . $desc . ' purple-button" style=" margin-left:5px; margin-right:10px"><span class="purple-right"></span><img src="wp-content/themes/multidesign/image/theme/login-icon.png" alt="" class="button-icon">' . $title . '
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('color5', 'sc_downloadButton19');
//------------------------------------------------------------------//
// Message 1 //
function sc_m1($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/001.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m1', 'sc_m1');
//------------------------------------------------------------------//
// Message 2 //
function sc_m2($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/002.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m2', 'sc_m2');
//------------------------------------------------------------------//
// Message 3 //
function sc_m3($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/003.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m3', 'sc_m3');
//------------------------------------------------------------------//
// Message 4 //
function sc_m4($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/004.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m4', 'sc_m4');
//------------------------------------------------------------------//
// Message 5 //
function sc_m5($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/005.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m5', 'sc_m5');
//------------------------------------------------------------------//
// Message 6 //
function sc_m6($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/006.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m6', 'sc_m6');
//------------------------------------------------------------------//
// Message 7 //
function sc_m7($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/007.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m7', 'sc_m7');
//------------------------------------------------------------------//
// Message 8 //
function sc_m8($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/008.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m8', 'sc_m8');
//------------------------------------------------------------------//
// Message 9 //
function sc_m9($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/009.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m9', 'sc_m9');
//------------------------------------------------------------------//
// Message 10 //
function sc_m10($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/010.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m10', 'sc_m10');
//------------------------------------------------------------------//
// Message 11 //
function sc_m11($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/011.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m11', 'sc_m11');
//------------------------------------------------------------------//
// Message 12 //
function sc_m12($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/012.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m12', 'sc_m12');
//------------------------------------------------------------------//
// Message 13 //
function sc_m13($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/013.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {
$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m13', 'sc_m13');
//------------------------------------------------------------------//
// Message 14 //
function sc_m14($atts) {
extract(shortcode_atts(array(
"url" => '',
"title" => '',
"desc" => '',
"align" => ''
), $atts));

if ($align == '') {
$align='center';
}

$var_sHTML = '';
$var_sHTML .= '<div class="m1">
<a href="' . $url . '"class="' . $desc . '">
<img src="wp-content/themes/multidesign/image/theme/014.png" alt="" ><p>' . $title . '</p>
</a>
</div>';

if ($align == 'right' || $align == 'left') {

$var_sHTML .= '<div class="dlbutton-floatreset"></div>';
}

return $var_sHTML;
}

add_shortcode('m14', 'sc_m14');
//------------------------------------------------------------------//


if ( ! isset( $content_width ) )
	$content_width = 640;


add_action( 'after_setup_theme', 'twentyten_setup' );

if ( ! function_exists( 'twentyten_setup' ) ):

function twentyten_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'twentyten', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'twentyten' ),
	) );

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	define( 'HEADER_TEXTCOLOR', '' );
	// No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	define( 'HEADER_IMAGE', '%s/images/headers/path.jpg' );

	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to twentyten_header_image_width and twentyten_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width', 940 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height', 198 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Don't support text inside the header image.
	define( 'NO_HEADER_TEXT', true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See twentyten_admin_header_style(), below.
	add_custom_image_header( '', 'twentyten_admin_header_style' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'berries' => array(
			'url' => '%s/images/headers/berries.jpg',
			'thumbnail_url' => '%s/images/headers/berries-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Berries', 'twentyten' )
		),
		'cherryblossom' => array(
			'url' => '%s/images/headers/cherryblossoms.jpg',
			'thumbnail_url' => '%s/images/headers/cherryblossoms-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Cherry Blossoms', 'twentyten' )
		),
		'concave' => array(
			'url' => '%s/images/headers/concave.jpg',
			'thumbnail_url' => '%s/images/headers/concave-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Concave', 'twentyten' )
		),
		'fern' => array(
			'url' => '%s/images/headers/fern.jpg',
			'thumbnail_url' => '%s/images/headers/fern-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Fern', 'twentyten' )
		),
		'forestfloor' => array(
			'url' => '%s/images/headers/forestfloor.jpg',
			'thumbnail_url' => '%s/images/headers/forestfloor-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Forest Floor', 'twentyten' )
		),
		'inkwell' => array(
			'url' => '%s/images/headers/inkwell.jpg',
			'thumbnail_url' => '%s/images/headers/inkwell-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Inkwell', 'twentyten' )
		),
		'path' => array(
			'url' => '%s/images/headers/path.jpg',
			'thumbnail_url' => '%s/images/headers/path-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Path', 'twentyten' )
		),
		'sunset' => array(
			'url' => '%s/images/headers/sunset.jpg',
			'thumbnail_url' => '%s/images/headers/sunset-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Sunset', 'twentyten' )
		)
	) );
}
endif;

if ( ! function_exists( 'twentyten_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in twentyten_setup().
 *
 * @since Twenty Ten 1.0
 */
function twentyten_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentyten_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Twenty Ten 1.0
 * @return int
 */
function twentyten_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'twentyten_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Twenty Ten 1.0
 * @return string "Continue Reading" link
 */
function twentyten_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyten_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string An ellipsis
 */
function twentyten_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyten_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyten_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function twentyten_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyten_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyten_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Twenty Ten's style.css.
 *
 * @since Twenty Ten 1.0
 * @return string The gallery style filter, with the styles themselves removed.
 */
function twentyten_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'twentyten_remove_gallery_css' );

if ( ! function_exists( 'twentyten_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyten_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?><!-- #1-->
      <div class="user-comments">
        
        <span id="user-mask-two"></span><?php echo get_avatar( $comment, 58 ); ?>
        
        <h6 style="margin-bottom:0px;">
        <?php if ( $comment->comment_approved == '0' ) : ?>
              <?php _e( 'Your comment is awaiting moderation.', 'twentyten' ); ?>
              <br />
        <?php endif; ?>
        
        <?php printf( __( '%s <span class="says">says:</span>', 'twentyten' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
        
		<span style="color:#cfcdcd;"><?php printf( __( '%1$s at %2$s', 'twentyten' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'twentyten' ), ' ' ); ?> | <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?></span>
        
        </h6>
        
        <?php comment_text(); ?>
      </div>
      <!-- #1-->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyten' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'twentyten'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override twentyten_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Twenty Ten 1.0
 * @uses register_sidebar
 */
function twentyten_widgets_init() {
	// Area 1, located at the top of the sidebar.

}
/** Register sidebars by running twentyten_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'twentyten_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'twentyten_remove_recent_comments_style' );

if ( ! function_exists( 'twentyten_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'twentyten' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'twentyten_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;
