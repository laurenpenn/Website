<?php

if ( function_exists('register_sidebar') )
register_sidebar(array('name'=>'Sidebar',
'before_widget' => '<div id="%1$s" class="widget %2$s">',
'after_widget' => '</div>',
'before_title' => '<h3 class="widgettitle">',
'after_title' => '</h3>',
));
if ( function_exists('register_sidebar') )
register_sidebar(array('name'=>'Home Sidebar',
'before_widget' => '<div id="%1$s" class="widget %2$s">',
'after_widget' => '</div>',
'before_title' => '<h3 class="widgettitle">',
'after_title' => '</h3>',
));
register_sidebar(array('name'=>'Footer Widget Area',
'before_widget' => '<div id="%1$s" class="footer-widget">',
'after_widget' => '</div>',
'before_title' => '<h3 class="widgettitle">',
'after_title' => '</h3>',
));

?>