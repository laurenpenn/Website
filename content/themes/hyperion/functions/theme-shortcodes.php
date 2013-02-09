<?php

// [toplink]
function toplink_shortcode( $atts, $content = null ) {
   return '<a href="#headerwrap" class="anchorLink posttoplink">'. do_shortcode($content) .'</a>';
}
add_shortcode('toplink', 'toplink_shortcode');

// [toplink_full]
function toplink_full_shortcode( $atts, $content = null ) {
   return '<a href="#headerwrap" class="anchorLink posttoplink-full">'. do_shortcode($content) .'</a>';
}
add_shortcode('toplink_full', 'toplink_full_shortcode');

// [dropcap]
function dropcap_shortcode( $atts, $content = null ) {
   return '<span class="drop-cap">'. do_shortcode($content) .'</span>';
}
add_shortcode('dropcap', 'dropcap_shortcode');

// [download]
function download_shortcode( $atts, $content = null ) {
   return '<div class="download-box">'. do_shortcode($content) .'</div>';
}
add_shortcode('download', 'download_shortcode');

// [warning]
function warning_shortcode( $atts, $content = null ) {
   return '<div class="warning-box">'. do_shortcode($content) .'</div>';
}
add_shortcode('warning', 'warning_shortcode');

// [info]
function info_shortcode( $atts, $content = null ) {
   return '<div class="info-box">'. do_shortcode($content) .'</div>';
}
add_shortcode('info', 'info_shortcode');

// [note]
function note_shortcode( $atts, $content = null ) {
   return '<div class="note-box">'. do_shortcode($content) .'</div>';
}
add_shortcode('note', 'note_shortcode');

// [one_third]
function one_third_shortcode( $atts, $content = null ) {
   return '<div class="one-third"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('one_third', 'one_third_shortcode');

// [one_third_post]
function one_third_post_shortcode( $atts, $content = null ) {
   return '<div class="one-third-post"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('one_third_post', 'one_third_post_shortcode');

// [one_third_last]
function one_third_last_shortcode( $atts, $content = null ) {
   return '<div class="one-third last"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('one_third_last', 'one_third_last_shortcode');

// [one_third_post_last]
function one_third_post_last_shortcode( $atts, $content = null ) {
   return '<div class="one-third-post last"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('one_third_post_last', 'one_third_post_last_shortcode');

// [one_half]
function one_half_shortcode( $atts, $content = null ) {
   return '<div class="one-half"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('one_half', 'one_half_shortcode');

// [one_half_last]
function one_half_last_shortcode( $atts, $content = null ) {
   return '<div class="one-half last"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('one_half_last', 'one_half_last_shortcode');

// [one_half_post]
function one_half_post_shortcode( $atts, $content = null ) {
   return '<div class="one-half-post"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('one_half_post', 'one_half_post_shortcode');

// [one_half_post_last]
function one_half_post_last_shortcode( $atts, $content = null ) {
   return '<div class="one-half-post last"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('one_half_post_last', 'one_half_post_last_shortcode');

// [two_third]
function two_third_shortcode( $atts, $content = null ) {
   return '<div class="two-third"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('two_third', 'two_third_shortcode');

// [two_third_last]
function two_third_last_shortcode( $atts, $content = null ) {
   return '<div class="two-third last"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('two_third_last', 'two_third_last_shortcode');

// [one_fourth]
function one_fourth_shortcode( $atts, $content = null ) {
   return '<div class="one-fourth"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('one_fourth', 'one_fourth_shortcode');

// [one_fourth_last]
function one_fourth_last_shortcode( $atts, $content = null ) {
   return '<div class="one-fourth last"><p>'. do_shortcode($content) .'</p></div>';
}
add_shortcode('one_fourth_last', 'one_fourth_last_shortcode');

?>
