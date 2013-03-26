<?php if ($data['theme_layout'] == "Fullwidth and 960px container") { ?>
	.wide_cont { max-width:100% !important;  margin:0px auto; background-color:#fdfdfd; }
<?php } ?>

<?php if ($data['theme_layout'] == "Boxed and 960px container") { ?>
	.wide_cont { max-width:1000px !important;  margin:<?php echo $data['theme_boxed_margin']; ?>px auto !important; box-shadow:0px 0px 3px #b4b4b4 !important;}
    body {
      background: <?php echo $data['theme_boxed_bg_color']; ?> <?php echo 'url("'.strip_tags($data['theme_boxed_bg']).'")'; ?> fixed !important;
      
    }
<?php } ?>

<?php if ($data['theme_layout'] == "Fullwidth and 1170px container") { ?>
	.wide_cont { max-width:100% !important;  margin:0px auto !important; background-color:#fdfdfd;}
<?php } ?>

<?php if ($data['theme_layout'] == "Boxed and 1170px container") { ?>
	.wide_cont { max-width:1240px !important;  margin:<?php echo $data['theme_boxed_margin']; ?>px auto; box-shadow:0px 0px 3px #b4b4b4 !important;}
	body {
      background: <?php echo $data['theme_boxed_bg_color']; ?> <?php echo 'url("'.strip_tags($data['theme_boxed_bg']).'")'; ?> fixed !important;
    }
<?php } ?>


.caption.commander_heading{	color:<?php echo $data['main_conent_links']; ?>;}
.caption.commander_small_heading{ color:<?php echo $data['main_conent_links']; ?>;}

a { color: <?php echo $data['main_conent_links']; ?>;}
a:hover {color:<?php echo $data['main_conent_links_hover']; ?>;}

.wide_cont {background-color:<?php echo $data['theme_conatiner_bg_color']; ?>;}

.colored {color: <?php echo $data['main_conent_links']; ?> !important;}
.top_line {background-color: <?php echo $data['theme_colors_top_line']; ?> !important; background-image: <?php echo 'url("'.strip_tags($data['theme_colors_top_line_bg_image']).'")'; ?> !important;}
.top_line p {color: <?php echo $data['theme_colors_top_line_text']; ?> !important;}
.top_line a {color: <?php echo $data['theme_colors_top_line_a']; ?> !important;}
.top_line a:hover {color: <?php echo $data['theme_colors_top_line_a_hover']; ?> !important;}

.page_head {padding-top: <?php echo $data['logo_and_menu_t_padding']; ?>px !important;
padding-bottom: <?php echo $data['logo_and_menu_b_padding']; ?>px !important;
background-image: <?php echo 'url("'.strip_tags($data['logo_and_menu_bg_image']).'")'; ?> !important;
background-color: <?php echo $data['logo_and_menu_bg']; ?> !important;
}
.logo {margin-top: <?php echo $data['logo_and_menu_logo_margin']; ?>px !important;}
.page_head .menu {margin-top: <?php echo $data['logo_and_menu_menu_margin']; ?>px !important; }
.page_head .menu li { margin-left:<?php echo $data['logo_and_menu_menu_li_margin']; ?>px !important;	background:<?php echo $data['logo_and_menu_menu_li_bg']; ?>}
<?php if($data['logo_and_menu_menu_li_shadow'] == true ) { ?>
.page_head .menu a { box-shadow:1px 1px <?php echo $data['logo_and_menu_menu_li_shadow_color']; ?> !important; }
<?php } ?>

.page_head .menu li, .page_head .menu li ul{
-moz-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px !important;
-o-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px !important;
-webkit-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px !important;
border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px !important;
}
.page_head .menu .current-menu-parent a {color: <?php echo $data['logo_and_menu_menu_li_currnet_a_color']; ?>}
.page_head .menu .current-menu-parent {background: <?php echo $data['logo_and_menu_menu_li_currnet_bg']; ?>}

.page_head .menu li a {
color: <?php echo $data['logo_and_menu_menu_li_a_color']; ?>;
font-size: <?php echo $data['logo_and_menu_menu_li_font']; ?>px;
padding-left: <?php echo $data['logo_and_menu_menu_li_h_padding']; ?>px;
padding-right: <?php echo $data['logo_and_menu_menu_li_h_padding']; ?>px;
padding-top: <?php echo $data['logo_and_menu_menu_li_v_padding']; ?>px;
padding-bottom: <?php echo $data['logo_and_menu_menu_li_v_padding']; ?>px;
}
.page_head .menu ul {top: <?php echo (($data['logo_and_menu_menu_li_v_padding']*2)+35); ?>px !important; }
.page_head .menu .current-menu-item a {color: <?php echo $data['logo_and_menu_menu_li_currnet_a_color']; ?> !important ;}

.page_head .menu .current-menu-item {background: <?php echo $data['logo_and_menu_menu_li_currnet_bg']; ?> !important ;}
.page_head .menu li:hover { background: <?php echo $data['logo_and_menu_menu_li_bg_hover']; ?> ;}
.page_head .menu li:hover a { color: <?php echo $data['logo_and_menu_menu_li_a_hover']; ?> ;}

.page_head .menu ul .current-menu-item a{ color: <?php echo $data['logo_and_menu_menu_li_a_hover']; ?> !important;}
.page_head .menu .current-menu-item ul a{ color: <?php echo $data['logo_and_menu_menu_sublevel_li_a']; ?> !important;}


.page_head .menu li ul { background-color: <?php echo $data['logo_and_menu_menu_sublevel_li_bg']; ?> ;}
.page_head .menu ul li:first-child > a:after { border-bottom-color:<?php echo $data['logo_and_menu_menu_sublevel_li_bg']; ?> ;}
.page_head .menu ul ul li:first-child > a:after { border-right-color: <?php echo $data['logo_and_menu_menu_sublevel_li_bg']; ?> ;}
.page_head .menu ul li a { color: <?php echo $data['logo_and_menu_menu_sublevel_li_a']; ?> !important;}

.page_head .menu ul li:hover a { background:<?php echo $data['logo_and_menu_menu_sublevel_li_hover_bg']; ?> }
.page_head .menu ul li:hover:first-child > a:after { border-bottom-color:<?php echo $data['logo_and_menu_menu_sublevel_li_hover_bg']; ?> ;}
.page_head .menu ul ul li:hover:first-child > a:after { border-right-color: <?php echo $data['logo_and_menu_menu_sublevel_li_hover_bg']; ?> ;}
.page_head .menu ul li:hover a { color: <?php echo $data['logo_and_menu_menu_sublevel_li_a_hover']; ?> !important}

.page_head .menu ul ul a { background-color: <?php echo $data['logo_and_menu_menu_sublevel_li_bg']; ?>  !important;}
.page_head .menu ul li:hover li a { color: <?php echo $data['logo_and_menu_menu_sublevel_li_a']; ?> !important;}
.page_head .menu ul li li:hover a { color:<?php echo $data['logo_and_menu_menu_sublevel_li_a_hover']; ?> !important; }
.page_head .menu ul ul a:hover { background:<?php echo $data['logo_and_menu_menu_sublevel_li_hover_bg']; ?> !important }


.page_head .menu li a {
-moz-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px;
-o-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px;
-webkit-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px;
border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px;
}

.page_head .menu li a:hover {
-moz-border-radius: 0px;
-o-border-radius: 0px;
-webkit-border-radius: 0px;
border-radius: 0px;
}

.page_head .menu ul li:first-child a:hover {
-moz-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px 0px 0px !important;
-o-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px  0px 0px !important;
-webkit-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px  0px 0px !important;
border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px  0px 0px !important;
}

.page_head .menu ul li:last-child a:hover {
-moz-border-radius: 0px 0px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px !important;
-o-border-radius: 0px 0px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px !important;
-webkit-border-radius: 0px 0px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px !important;
border-radius: 0px 0px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px !important;
}

.page_head .menu ul ul li:first-child a:hover {
-moz-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px 0px 0px !important;
-o-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px  0px 0px !important;
-webkit-border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px  0px 0px !important;
border-radius: <?php echo $data['logo_and_menu_menu_li_radius']; ?>px <?php echo $data['logo_and_menu_menu_li_radius']; ?>px  0px 0px !important;
}


.tag_line { background-image: <?php echo 'url("'.strip_tags($data['tag_line_custom_bg']).'")'; ?>; border-bottom: 1px solid <?php echo $data['tag_line_border_bottom']; ?>; border-top: 1px solid <?php echo $data['tag_line_border_top']; ?>; background-color: <?php echo $data['tag_line_bg']; ?> }
.welcome {padding-bottom: <?php echo $data['tag_line_padding_bottom']; ?>px; padding-top: <?php echo $data['tag_line_padding_top']; ?>px}

.footer {
	color: <?php echo $data['footer_text_color']; ?>;
	background-image: <?php echo 'url("'.strip_tags($data['footer_bg_image']).'")'; ?>;
    background-color: <?php echo $data['footer_bg_color']; ?>;
	margin-top:<?php echo $data['footer_margin_top']; ?>px; 
	padding-top:<?php echo $data['footer_padding_top']; ?>px; 
    padding-bottom: <?php echo $data['footer_padding_bottom']; ?>px;
    border-top: <?php echo $data['footer_border_value']; ?>px solid <?php echo $data['footer_border_color']; ?>;
}
.footer p { color: <?php echo $data['footer_text_color']; ?>;}
.footer strong {color: <?php echo $data['footer_text_strong_color']; ?>;}
#jstwitter .tweet {color: <?php echo $data['footer_text_color']; ?>;}
#jstwitter .tweet .time {color: <?php echo $data['footer_text_small_color']; ?>;}
#jstwitter .tweet a:hover {color: <?php echo $data['footer_text_a_hover_color']; ?>;}
.small-meta { color:<?php echo $data['footer_text_small_color']; ?>;}
.small-meta a { color: <?php echo $data['footer_text_small_color']; ?> !important;}
.footer a {color: <?php echo $data['footer_text_a_color']; ?>;}
.footer a:hover {color: <?php echo $data['footer_text_a_hover_color']; ?>;}
.small-meta a:hover { color: <?php echo $data['footer_text_a_hover_color']; ?> !important;}

.footer h5 { color:<?php echo $data['footer_text_header_color']; ?>; }
.footer hr{ border-top-color: <?php echo $data['footer_hr_color']; ?>;  margin-top:6px; margin-bottom:15px;}
.bottom_line { background-color: <?php echo $data['theme_colors_bottom_line']; ?>; background-image: <?php echo 'url("'.strip_tags($data['theme_colors_bottom_line_bg_image']).'")'; ?>; }
.bottom_line { color: <?php echo $data['theme_colors_bottom_line_text']; ?>;}
.bottom_line a {color: <?php echo $data['theme_colors_bottom_line_a']; ?>;}
.bottom_line a:hover {color: <?php echo $data['theme_colors_bottom_line_a_hover']; ?>;}
.main_content_area {margin-top: <?php echo $data['main_content_margin_top']; ?>px;}

.main_content_area .date { background: <?php echo $data['blog_date_bg']; ?>; border-radius:<?php echo $data['blog_date_border_radius']; ?>px;}
.main_content_area .date h6 { color:<?php echo $data['blog_date_color']; ?>;  text-shadow:0px 1px <?php echo $data['blog_date_text_shadow']; ?>;}

.blog_item .view-first .mask {background-color: rgba(<?php echo $data['blog_image_bg_1']; ?>,<?php echo $data['blog_image_bg_2']; ?>,<?php echo $data['blog_image_bg_3']; ?>, <?php echo $data['blog_image_bg_op']; ?>)}
.blog_item .view a.info {background-color:<?php echo $data['blog_image_icons_bg']; ?>; background-image: <?php echo 'url("'.strip_tags($data['blog_image_icons_zoom']).'")'; ?>}
.blog_item .view a.info:hover {background-color:<?php echo $data['blog_image_icons_bg_hover']; ?>;}
.blog_item .view a.link {background-color:<?php echo $data['blog_image_icons_bg']; ?>; background-image: <?php echo 'url("'.strip_tags($data['blog_image_icons_link']).'")'; ?>}
.blog_item .view a.link:hover {background-color:<?php echo $data['blog_image_icons_bg_hover']; ?>;}

.blog_item_description { background-color:<?php echo $data['blog_item_description_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['blog_item_description_bg_image']).'")'; ?>; padding:<?php echo $data['blog_item_description_padding']; ?>px; border:1px solid <?php echo $data['blog_item_description_border_color']; ?>; color:<?php echo $data['blog_item_description_text_color']; ?>}

.pride_pg a {padding: <?php echo $data['pagination_padding_v']; ?>px <?php echo $data['pagination_padding_h']; ?>px; border-radius:<?php echo $data['pagination_border_radius']; ?>px; background-color: <?php echo $data['pagination_bg_color']; ?>; color: <?php echo $data['pagination_text_color']; ?>; text-shadow: <?php echo $data['pagination_text_shadow']; ?> 0px 1px 0px; background-image: <?php echo 'url("'.strip_tags($data['pagination_bg_image']).'")'; ?>; }
.pride_pg .current {padding: <?php echo $data['pagination_padding_v']; ?>px <?php echo $data['pagination_padding_h']; ?>px; border-radius:<?php echo $data['pagination_border_radius']; ?>px; background-color: <?php echo $data['pagination_hover_bg_color']; ?>;  color:<?php echo $data['pagination_hover_text_color']; ?>;  background-image: <?php echo 'url("'.strip_tags($data['pagination_bg_image']).'")'; ?>;}
.pride_pg a:hover  {
	background-color: <?php echo $data['pagination_hover_bg_color']; ?>;
	color:<?php echo $data['pagination_hover_text_color']; ?>;
	text-shadow: none;
	background-image: <?php echo 'url("'.strip_tags($data['pagination_bg_image']).'")'; ?>;
}

.portfolio_post_item_description { background-color:<?php echo $data['portfolio_post_item_description_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['portfolio_post_item_description_bg_image']).'")'; ?>; padding:<?php echo $data['portfolio_post_item_description_padding']; ?>px; border:1px solid <?php echo $data['portfolio_post_item_description_border_color']; ?>; color:<?php echo $data['portfolio_post_item_description_text_color']; ?>}

.blog_post_item_description { background-color:<?php echo $data['blog_post_item_description_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['blog_post_item_description_bg_image']).'")'; ?>; padding:<?php echo $data['blog_post_item_description_padding']; ?>px; border:1px solid <?php echo $data['blog_post_item_description_border_color']; ?>; color:<?php echo $data['blog_post_item_description_text_color']; ?>}
.blog_author_item_description { background-color:<?php echo $data['blog_author_item_description_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['blog_author_item_description_bg_image']).'")'; ?>; padding:<?php echo $data['blog_author_item_description_padding']; ?>px; border:1px solid <?php echo $data['blog_author_item_description_border_color']; ?>; color:<?php echo $data['blog_author_item_description_text_color']; ?>}
.share {padding:<?php echo $data['blog_share_padding']; ?>px; background-color:<?php echo $data['blog_share_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['blog_share_bg_color']).'")'; ?>; color:<?php echo $data['blog_share_text_color']; ?>;}
.comments_div {border-left:1px solid <?php echo $data['blog_comments_border_color']; ?>; padding-left:<?php echo $data['blog_comments_padding']; ?>px;}
.blog_item_comments_description { background-color:<?php echo $data['blog_comments_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['blog_comments_bg_image']).'")'; ?>; padding:<?php echo $data['blog_comments_li_padding']; ?>px; border:1px solid <?php echo $data['blog_comments_border_color']; ?>; color:<?php echo $data['blog_comments_text_color']; ?>}


.blog_sidebar {background-color:<?php echo $data['blog_sidebar_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['blog_sidebar_bg_image']).'")'; ?>; border-radius:<?php echo $data['blog_sidebar_border_radius']; ?>px;}

.blog_sidebar .well hr { border-bottom-color:<?php echo $data['blog_sidebar_widget_hr']; ?>;}
.blog_sidebar .well {border:1px solid <?php echo $data['blog_sidebar_widget_border_color']; ?>; background-color:<?php echo $data['blog_sidebar_widget_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['blog_sidebar_widget_bg_image']).'")'; ?>; border-radius:<?php echo $data['blog_sidebar_widget_border_radius']; ?>px;}
.blog_sidebar h5 { color:<?php echo $data['blog_sidebar_widget_header_color']; ?>;}
.blog_sidebar a{ color:<?php echo $data['blog_sidebar_widget_links_color']; ?>;}
.blog_sidebar a:hover{ color:<?php echo $data['blog_sidebar_widget_links_color_hover']; ?>;}
.blog_sidebar { color:<?php echo $data['blog_sidebar_widget_text_color']; ?>;}
.blog_sidebar ul li { border-bottom:1px dashed <?php echo $data['blog_sidebar_widget_hr']; ?>}
.blog_sidebar .current-menu-item a {color:<?php echo $data['blog_sidebar_widget_links_color_hover']; ?>;}


.filter_button {  font-size:<?php echo $data['portfolio_filter_text_size']; ?>px; margin-right:<?php echo $data['portfolio_filter_margin']; ?>px; padding:<?php echo $data['portfolio_filter_padding_v']; ?>px <?php echo $data['portfolio_filter_padding_h']; ?>px; background-color:<?php echo $data['portfolio_filter_bg_color']; ?>; border:1px solid <?php echo $data['portfolio_filter_border_color']; ?>; border-radius:<?php echo $data['portfolio_filter_border_radius']; ?>px; color:<?php echo $data['portfolio_filter_text_color']; ?>; text-shadow:1px 1px <?php echo $data['portfolio_filter_text_shadow']; ?>;}
.filter_button:hover {background-color:<?php echo $data['portfolio_filter_bg_color_hover']; ?>; color:<?php echo $data['portfolio_filter_text_color_hover']; ?>; border-color:<?php echo $data['portfolio_filter_bg_color_hover']; ?> }
.filter_current { background-color:<?php echo $data['portfolio_filter_bg_color_hover']; ?>; border-color:<?php echo $data['portfolio_filter_bg_color_hover']; ?>; color:<?php echo $data['portfolio_filter_text_color_hover']; ?>;}

.portfolio_item .view-first .mask {background-color: rgba(<?php echo $data['portfolio_image_bg_1']; ?>,<?php echo $data['portfolio_image_bg_2']; ?>,<?php echo $data['portfolio_image_bg_3']; ?>, <?php echo $data['portfolio_image_bg_op']; ?>)}
.portfolio_item .view a.info {background-color:<?php echo $data['portfolio_image_icons_bg']; ?>; background-image: <?php echo 'url("'.strip_tags($data['portfolio_image_icons_zoom']).'")'; ?>}
.portfolio_item .view a.info:hover {background-color:<?php echo $data['portfolio_image_icons_bg_hover']; ?>;}
.portfolio_item .view a.link {background-color:<?php echo $data['portfolio_image_icons_bg']; ?>; background-image: <?php echo 'url("'.strip_tags($data['portfolio_image_icons_link']).'")'; ?>}
.portfolio_item .view a.link:hover {background-color:<?php echo $data['portfolio_image_icons_bg_hover']; ?>;}


.descr {background-color:<?php echo $data['portfolio_descr_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['portfolio_descr_bg_image']).'")'; ?>; border:1px solid <?php echo $data['portfolio_descr_border_color']; ?>;}
.descr a { color: <?php echo $data['portfolio_descr_links_color']; ?>;}
.descr a:hover { color: <?php echo $data['portfolio_descr_links_color_hover']; ?>;}
.clo { font-size:<?php echo $data['portfolio_descr_text_size']; ?>px; color:<?php echo $data['portfolio_descr_text_color']; ?> !important;}

.portfolio_sidebar .well hr { border-bottom-color:<?php echo $data['portfolio_sidebar_widget_hr']; ?>;}
.portfolio_sidebar .well {border:1px solid <?php echo $data['portfolio_sidebar_widget_border_color']; ?>; background-color:<?php echo $data['portfolio_sidebar_widget_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['portfolio_sidebar_widget_bg_image']).'")'; ?>; border-radius:<?php echo $data['portfolio_sidebar_widget_border_radius']; ?>px;}
.portfolio_sidebar h5 { color:<?php echo $data['portfolio_sidebar_widget_header_color']; ?>;}
.portfolio_sidebar a{ color:<?php echo $data['portfolio_sidebar_widget_links_color']; ?>;}
.portfolio_sidebar a:hover{ color:<?php echo $data['portfolio_sidebar_widget_links_color_hover']; ?>;}
.portfolio_sidebar { color:<?php echo $data['portfolio_sidebar_widget_text_color']; ?>;}
.portfolio_sidebar ul li { border-bottom:1px dashed <?php echo $data['portfolio_sidebar_widget_hr']; ?>}
.portfolio_sidebar .current-menu-item a {color:<?php echo $data['portfolio_sidebar_widget_links_color_hover']; ?>;}

.blog_item .meta a, .blog_item .meta span, .blog_item .meta span a:after{ color:<?php echo $data['blog_show_posts_meta_color']; ?>;}
.blog_item .meta a:hover { color:<?php echo $data['blog_show_posts_meta_color_hover']; ?>;}

.blog_post_item_description .meta a, .blog_post_item_description .meta a:after, .blog_post_item_description .meta span{ color:<?php echo $data['blog_post_show_posts_meta_color']; ?>;}
.blog_post_item_description .meta a:hover { color:<?php echo $data['blog_post_show_posts_meta_color_hover']; ?>;}


.portfolio_post_item_description .meta a, .portfolio_post_item_description .meta a:after, .portfolio_post_item_description .meta span{ color:<?php echo $data['portfolio_post_show_posts_meta_color']; ?>;}
.portfolio_post_item_description .meta a:hover { color:<?php echo $data['portfolio_post_show_posts_meta_color_hover']; ?>;}


#filters_sidebar a { border-bottom:1px dashed <?php echo $data['portfolio_sidebar_widget_hr']; ?>;}
.filter_sidebar_current { color:<?php echo $data['portfolio_sidebar_widget_links_color_hover']; ?>;}



.page_sidebar .well hr { border-bottom-color:<?php echo $data['page_sidebar_widget_hr']; ?>;}
.page_sidebar .well {border:1px solid <?php echo $data['page_sidebar_widget_border_color']; ?>; background-color:<?php echo $data['page_sidebar_widget_bg_color']; ?>; background-image: <?php echo 'url("'.strip_tags($data['page_sidebar_widget_bg_image']).'")'; ?>; border-radius:<?php echo $data['page_sidebar_widget_border_radius']; ?>px;}
.page_sidebar h5 { color:<?php echo $data['page_sidebar_widget_header_color']; ?>;}
.page_sidebar a{ color:<?php echo $data['page_sidebar_widget_links_color']; ?>;}
.page_sidebar a:hover{ color:<?php echo $data['page_sidebar_widget_links_color_hover']; ?>;}
.page_sidebar { color:<?php echo $data['page_sidebar_widget_text_color']; ?>;}
.page_sidebar ul li { border-bottom:1px dashed <?php echo $data['page_sidebar_widget_hr']; ?>}
.page_sidebar .main_content_area .menu li { border-bottom:1px dashed <?php echo $data['page_sidebar_widget_hr']; ?>; padding:0px !important;}
.page_sidebar .main_content_area .menu li a { color:<?php echo $data['page_sidebar_widget_text_color']; ?>;} 
.page_sidebar .main_content_area .menu li a:hover { color:<?php echo $data['page_sidebar_widget_links_color_hover']; ?>;}
.page_sidebar .current-menu-item a {color:<?php echo $data['page_sidebar_widget_links_color_hover']; ?>;}
<?php
$head_font_one = $data['headers_font_one'];
$head_font_two = $data['headers_font_two'];
$head_font_three = $data['headers_font_three'];
$head_font_four = $data['headers_font_four'];
$head_font_five = $data['headers_font_five'];
$head_font_six = $data['headers_font_six'];
$commander_body_font = $data['body_font'];
?>

body {
	font-family: <?php echo $commander_body_font['face']; ?> !important;
	color: <?php echo $commander_body_font['color']; ?> !important;
	font-style: <?php echo $commander_body_font['style']; ?> !important;
	font-size: <?php echo $commander_body_font['size']; ?> !important; 
}

h1 {
	font-family: <?php echo $head_font_one['face']; ?> !important;
	color: <?php echo $head_font_one['color']; ?> !important;
	font-style: <?php echo $head_font_one['style']; ?> !important;
	font-size: <?php echo $head_font_one['size']; ?> !important; 
	
}
h2{
	font-family: <?php echo $head_font_two['face']; ?>;
	color: <?php echo $head_font_two['color']; ?>;
	font-style: <?php echo $head_font_two['style']; ?>;
	font-size: <?php echo $head_font_two['size']; ?>; 
	
}
h3 {
	font-family: <?php echo $head_font_three['face']; ?>;
	color: <?php echo $head_font_three['color']; ?>;
	font-style: <?php echo $head_font_three['style']; ?>;
	font-size: <?php echo $head_font_three['size']; ?>; 
	
}
h4{
	font-family: <?php echo $head_font_four['face']; ?>;
	color: <?php echo $head_font_four['color']; ?>;
	font-style: <?php echo $head_font_four['style']; ?>;
	font-size: <?php echo $head_font_four['size']; ?>; 
	
}
h5 {
	font-family: <?php echo $head_font_five['face']; ?>;
	color: <?php echo $head_font_five['color']; ?>;
	font-style: <?php echo $head_font_five['style']; ?>;
	font-size: <?php echo $head_font_five['size']; ?>; 
	
}
h6 {
	font-family: <?php echo $head_font_six['face']; ?>;
	color: <?php echo $head_font_six['color']; ?>;
	font-style: <?php echo $head_font_six['style']; ?>;
	font-size: <?php echo $head_font_six['size']; ?>; 
	
}




<?php if($data['logo_and_menu_menu_li_triangles'] == false ) { ?>
	.page_head .menu ul li:first-child > a:after { border-bottom:0px !important;}
    .page_head .menu ul {top: <?php echo (($data['logo_and_menu_menu_li_v_padding']*2)+20); ?>px !important; }
<?php } ?>

.blog_head h3 a { color:<?php echo $data['blog_show_posts_meta_color']; ?>;}
.blog_head h3 a:hover { color:<?php echo $data['blog_show_posts_meta_color_hover']; ?>;}

<?php echo $data['custom_css']; ?>
