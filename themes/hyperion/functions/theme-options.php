<?php

$themename = "Hyperion";
$shortname = "hp";

$categories = get_categories('hide_empty=0&orderby=name');
$wp_cats = array();
foreach ($categories as $category_list ) {
       $wp_cats[$category_list->cat_ID] = $category_list->cat_name;
}
array_unshift($wp_cats, "Choose a category"); 


$options = array (
 
array( "name" => $themename." Options",
	"type" => "title"),
 

array( "name" => "General",
	"type" => "section"),
array( "type" => "open"),
 
array( "name" => "Colour Scheme",
	"desc" => "Select the color scheme for the theme",
	"id" => $shortname."_color",
	"type" => "select",
	"options" => array("Light", "Dark", "Blue", "Green", "Modern Blue", "Modern Green", "Modern Taupe", "Bubblegum", "Modern Black" ),
	"std" => "Light"),
	
array( "name" => "Logo URL",
	"desc" => "Enter the link to your logo image",
	"id" => $shortname."_logo",
	"type" => "text",
	"std" => ""),

array( "name" => "Cufon Font Replacement",
	"desc" => "Check this if you want to use the font replacement script for titles. Special characters do not work with this.",
	"id" => $shortname."_cufon",
	"type" => "checkbox",
	"std" => ""),

array( "name" => "Blog Category",
	"desc" => "Choose a category from which blog posts are drawn.",
	"id" => $shortname."_blogcat",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Portfolio Category",
	"desc" => "Choose a category from which portfolio posts are drawn.",
	"id" => $shortname."_portfoliocat",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
	
array( "name" => "Custom CSS",
	"desc" => "Want to add any custom CSS code? Put in here, and the rest is taken care of. This overrides any other stylesheets. eg: a.button{color:green}",
	"id" => $shortname."_custom_css",
	"type" => "textarea",
	"std" => ""),		
	
array( "type" => "close"),

array( "name" => "Navigation Options",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "<b>Use pages to navigate</b>",
	   "id" => $shortname."_page_navigation",
	   "type" => "checkbox",
	   "desc" => "Check/uncheck - Do you want to use pages as your main navigation?"),
	   "std" => "false",

array( "name" => "<b>Use categories to navigate</b>",
	   "id" => $shortname."_cat_navigation",
	   "type" => "checkbox",
	   "desc" => "Check/uncheck - Do you want to use pages as your main navigation?"),
	   "std" => "false",
	   
array( "name" => "<b>Use pages and categories to navigate</b>",
	   "id" => $shortname."_pcn_navigation",
	   "type" => "checkbox",
	   "desc" => "Check/uncheck - Do you want to use pages and categories as your main navigation? Note: Pages will be listed first. Best used with some dropdowns."),
	   "std" => "false",	   

array( "name" => "Exclude Pages from Main Navigation",
	"desc" => "Exclude pages from the main menu. Input the page ID followed by a comma. EX: 3, 4,",
	"id" => $shortname."_navexclude",
	"type" => "text",
	"std" => ""),	

array( "name" => "Exclude Categories from Main Navigation",
	"desc" => "Exclude categories from the main menu. Input the category ID followed by a comma. EX: 3, 4,",
	"id" => $shortname."_catexclude",
	"type" => "text",
	"std" => ""),	
	
array( "type" => "close"),

array( "name" => "Slider Choices and Controls",
	   "type" => "section"),
array( "type" => "open"),

array( "name" => "<b>Use Full Width Slider</b>",
	   "id" => $shortname."_show_fullslider",
	   "type" => "checkbox",
	   "desc" => "Check/uncheck - Do you want to use the full width slider on your homepage?"),
	   "std" => "false",
	   
array( "name" => "<b>Use Compact Version of Full Slider</b>",
	   "id" => $shortname."_show_compactslider",
	   "type" => "checkbox",
	   "desc" => "Check/uncheck - Do you want to use the compact version of the slider on your homepage?"),
	   "std" => "false",
	   
array( "name" => "<b>Use Fading Slider</b>",
	   "id" => $shortname."_show_fadingslider",
	   "type" => "checkbox",
	   "desc" => "Check/uncheck - Do you want to use the fading slider on your homepage? This only uses the transition speed option below."),
	   "std" => "false",	   

array( "name" => "Use jQuery Easing",
	"desc" => "This will make transions have extra effects like bouncing and stuff! Easing does not work with the fading slider.",
	"id" => $shortname."_use_easing",
	"type" => "checkbox",
	"std" => "false"),

array( "name" => "Easing Type",
	"desc" => "What fancy style would you like the easing to use?",
	"id" => $shortname."_easing",
	"type" => "select",
	"options" => array("easeOutBounce", "easeOutElastic", "easeOutBack"),
	"std" => ""),

array( "name" => "Transition Speed",
	"desc" => "How fast do you want the sliding to be? Must be in milliseconds. EX: 1500",
	"id" => $shortname."_transition_speed",
	"type" => "text",
	"std" => ""),
	
array( "name" => "Display Time",
	"desc" => "How long do you want each slide to show? Must be in milliseconds. EX: 5000",
	"id" => $shortname."_display_time",
	"type" => "text",
	"std" => ""),

array( "type" => "close"),
	   
array( "name" => "Slider Content Options",
	   "type" => "section"),
array( "type" => "open"),

array( "name" => "Use a post category",
	   "id" => $shortname."_show_sliderposts",
	   "type" => "checkbox",
	   "desc" => "Check/uncheck - Do you want to pull items for the sliders from a post category?"),
	   "std" => "false",

array( "name" => "Featured Category",
	"desc" => "Choose a category from which featured posts are drawn.",
	"id" => $shortname."_featurecat",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Featured Posts Number",
	"desc" => "How many items do you want to show in the Featured Content area?",
	"id" => $shortname."_postnumber",
	"type" => "text",
	"std" => ""),

array( "name" => "Choose what items to show",
	   "id" => $shortname."_show_slider_choice",
	   "type" => "checkbox",
	   "desc" => "Check/uncheck - Do you want to choose what specific image an item the sliders link to? With this option you are limited to 6 items."),
	   "std" => "false",

// Slider item 1
array( "name" => "Item 1 title",
	"desc" => "Enter the title of the first image.",
	"id" => $shortname."_slider_title1",
	"type" => "text",
	"std" => ""),	   
	   
array( "name" => "Item 1 image",
	"desc" => "Enter the URL of the first image.",
	"id" => $shortname."_slider_image1",
	"type" => "text",
	"std" => ""),

array( "name" => "Item 1 URL",
	"desc" => "Enter the URL where this image takes you.",
	"id" => $shortname."_slider_link1",
	"type" => "text",
	"std" => ""),

// Slider item 2
array( "name" => "Item 2 title",
	"desc" => "Enter the title of the second image.",
	"id" => $shortname."_slider_title2",
	"type" => "text",
	"std" => ""),	   
	   
array( "name" => "Item 2 image",
	"desc" => "Enter the URL of the second image.",
	"id" => $shortname."_slider_image2",
	"type" => "text",
	"std" => ""),

array( "name" => "Item 2 URL",
	"desc" => "Enter the URL where this image takes you.",
	"id" => $shortname."_slider_link2",
	"type" => "text",
	"std" => ""),

// Slider item 3
array( "name" => "Item 3 title",
	"desc" => "Enter the title of the third image.",
	"id" => $shortname."_slider_title3",
	"type" => "text",
	"std" => ""),	   
	   
array( "name" => "Item 3 image",
	"desc" => "Enter the URL of the third image.",
	"id" => $shortname."_slider_image3",
	"type" => "text",
	"std" => ""),

array( "name" => "Item 3 URL",
	"desc" => "Enter the URL where this image takes you.",
	"id" => $shortname."_slider_link3",
	"type" => "text",
	"std" => ""),

// Slider item 4
array( "name" => "Item 4 title",
	"desc" => "Enter the title of the fourth image.",
	"id" => $shortname."_slider_title4",
	"type" => "text",
	"std" => ""),	   
	   
array( "name" => "Item 4 image",
	"desc" => "Enter the URL of the fourth image.",
	"id" => $shortname."_slider_image4",
	"type" => "text",
	"std" => ""),

array( "name" => "Item 4 URL",
	"desc" => "Enter the URL where this image takes you.",
	"id" => $shortname."_slider_link4",
	"type" => "text",
	"std" => ""),

// Slider item 5
array( "name" => "Item 5 title",
	"desc" => "Enter the title of the fifth image.",
	"id" => $shortname."_slider_title5",
	"type" => "text",
	"std" => ""),	   
	   
array( "name" => "Item 5 image",
	"desc" => "Enter the URL of the fifth image.",
	"id" => $shortname."_slider_image5",
	"type" => "text",
	"std" => ""),

array( "name" => "Item 5 URL",
	"desc" => "Enter the URL where this image takes you.",
	"id" => $shortname."_slider_link5",
	"type" => "text",
	"std" => ""),

// Slider item 6
array( "name" => "Item 6 title",
	"desc" => "Enter the title of the sixth image.",
	"id" => $shortname."_slider_title6",
	"type" => "text",
	"std" => ""),	   
	   
array( "name" => "Item 6 image",
	"desc" => "Enter the URL of the sixth image.",
	"id" => $shortname."_slider_image6",
	"type" => "text",
	"std" => ""),

array( "name" => "Item 6 URL",
	"desc" => "Enter the URL where this image takes you.",
	"id" => $shortname."_slider_link6",
	"type" => "text",
	"std" => ""),
	
array( "type" => "close"),

array( "name" => "Homepage Contact Us Area",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "<b>Show contact us area.</b>",
	   "id" => $shortname."_show_intouch",
	   "type" => "checkbox",
	   "desc" => "Check/Uncheck - Do you want to show the contact button and text on your homepage? This is directly under the slider you may be using."),
	   "std" => "false",

array( "name" => "Contact area teaser text",
	"desc" => "Enter what you would like to say that will make people want to get in touch with you.",
	"id" => $shortname."_intouch_teaser",
	"type" => "text",
	"std" => ""),

array( "name" => "Contact button url",
	"desc" => "Where would you like the contact button to take them?",
	"id" => $shortname."_intouch_formurl",
	"type" => "text",
	"std" => ""),

array( "type" => "close"),


array( "name" => "Homepage Information Boxes",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "<b>Show Information Boxes</b>",
	   "id" => $shortname."_show_infoboxes",
	   "type" => "checkbox",
	   "desc" => "Check/Uncheck - Do you want to use the 3 info boxes on your homepage?"),
	   "std" => "false",
	   
array( "name" => "<b>Use lightbox link on Image hover</b>",
	   "id" => $shortname."_show_homelightbox",
	   "type" => "checkbox",
	   "desc" => "Check/Uncheck - Do you want to have the image link open up a larger image or video in a lightbox?"),
	   "std" => "false",	   
	   
array( "name" => "Box Link Text",
	"desc" => "Enter the that you want to display on the mor info link. ( EX: More Information... )",
	"id" => $shortname."_box_moretext",
	"type" => "text",
	"std" => ""),	   

array( "name" => "Box 1 Title",
	"desc" => "Enter the title of the first box on the left.",
	"id" => $shortname."_box1_title",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 1 Sub Text",
	"desc" => "Enter the sub text for the first box on the left. This appears under the title",
	"id" => $shortname."_box1_subtext",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 1 Main Text",
	"desc" => "Enter the main text for the first box on the left.",
	"id" => $shortname."_box1_maintext",
	"type" => "textarea",
	"std" => ""),

array( "name" => "Box 1 Image",
	"desc" => "Enter the image url for the first box on the left.",
	"id" => $shortname."_box1_image",
	"type" => "text",
	"std" => ""),

array( "name" => "<b>This box image links to a video</b>",
	   "id" => $shortname."_home_lightbox_video",
	   "type" => "checkbox",
	   "desc" => "Check/Uncheck - Check this box if you are linking to a video in the lightbox. If you do not the play icon will not show on hover."),
	   "std" => "false",

array( "name" => "Box 1 Lightbox Content",
	"desc" => "Enter the URL for an Image or Video. For this content to show the Box that says, Use Lightbox on hover, must be checked.",
	"id" => $shortname."_box1_content",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 1 Link Url",
	"desc" => "Enter the link where the more text takes you when clicked..",
	"id" => $shortname."_box1_morelink",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 2 Title",
	"desc" => "Enter the title of the middle boxe.",
	"id" => $shortname."_box2_title",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 2 Sub Text",
	"desc" => "Enter the sub text for the middle box. This appears under the title",
	"id" => $shortname."_box2_subtext",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 2 Main Text",
	"desc" => "Enter the main text for the middle box.",
	"id" => $shortname."_box2_maintext",
	"type" => "textarea",
	"std" => ""),

array( "name" => "Box 2 Image",
	"desc" => "Enter the image url for the middle box.",
	"id" => $shortname."_box2_image",
	"type" => "text",
	"std" => ""),

array( "name" => "<b>This box image links to a video</b>",
	   "id" => $shortname."_home_lightbox_video2",
	   "type" => "checkbox",
	   "desc" => "Check/Uncheck - Check this box if you are linking to a video in the lightbox. If you do not the play icon will not show on hover."),
	   "std" => "false",

array( "name" => "Box 2 Lightbox Content",
	"desc" => "Enter the URL for an Image or Video. For this content to show the Box that says, Use Lightbox on hover, must be checked.",
	"id" => $shortname."_box2_content",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 2 Link Url",
	"desc" => "Enter the link where the more text takes you when clicked..",
	"id" => $shortname."_box2_morelink",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 3 Title",
	"desc" => "Enter the title of the last box.",
	"id" => $shortname."_box3_title",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 3 Sub Text",
	"desc" => "Enter the sub text for the last box. This appears under the title",
	"id" => $shortname."_box3_subtext",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 3 Main Text",
	"desc" => "Enter the main text for the last box.",
	"id" => $shortname."_box3_maintext",
	"type" => "textarea",
	"std" => ""),

array( "name" => "Box 3 Image",
	"desc" => "Enter the image url for the last box.",
	"id" => $shortname."_box3_image",
	"type" => "text",
	"std" => ""),

array( "name" => "<b>This box image links to a video</b>",
	   "id" => $shortname."_home_lightbox_video3",
	   "type" => "checkbox",
	   "desc" => "Check/Uncheck - Check this box if you are linking to a video in the lightbox. If you do not the play icon will not show on hover."),
	   "std" => "false",

array( "name" => "Box 3 Lightbox Content",
	"desc" => "Enter the URL for an Image or Video. For this content to show the Box that says, Use Lightbox on hover, must be checked.",
	"id" => $shortname."_box3_content",
	"type" => "text",
	"std" => ""),

array( "name" => "Box 3 Link Url",
	"desc" => "Enter the link where the more text takes you when clicked..",
	"id" => $shortname."_box3_morelink",
	"type" => "text",
	"std" => ""),

array( "type" => "close"),

array( "name" => "Homepage Blog and News Entries",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "<b>Show Latest blog and news post entries.</b>",
	   "id" => $shortname."_show_blognews",
	   "type" => "checkbox",
	   "desc" => "Check/Uncheck - Do you want to show your latest blog posts and news on your homepage?"),
	   "std" => "false",
	   
array( "name" => "Home page Blog Category",
	"desc" => "Choose a category from which blog posts on the home page are drawn.",
	"id" => $shortname."_home_blogcat",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Blog Posts Count",
	"desc" => "Enter the number of blog posts you would like to show.",
	"id" => $shortname."_blog_postnumber",
	"type" => "text",
	"std" => ""),

array( "name" => "Blog Posts Title",
	"desc" => "Enter what you want the title of your blog entry section to be.",
	"id" => $shortname."_homeblog_title",
	"type" => "text",
	"std" => ""),

array( "name" => "Blog Posts Sub Title",
	"desc" => "Enter what you want the subtitle title of your blog entry section to be.",
	"id" => $shortname."_homeblog_sub_title",
	"type" => "text",
	"std" => ""),

array( "name" => "Home page News Category",
	"desc" => "Choose a category from which news posts on the home page are drawn.",
	"id" => $shortname."_home_newscat",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "News Posts Count",
	"desc" => "Enter the number of news posts you would like to show.",
	"id" => $shortname."_news_postnumber",
	"type" => "text",
	"std" => ""),

array( "name" => "News Posts Title",
	"desc" => "Enter what you want the title of your news entry section to be.",
	"id" => $shortname."_homenews_title",
	"type" => "text",
	"std" => ""),

array( "name" => "News Posts Sub Title",
	"desc" => "Enter what you want the subtitle title of your news entry section to be.",
	"id" => $shortname."_homenews_sub_title",
	"type" => "text",
	"std" => ""),

array( "type" => "close"),

array( "name" => "Portfolio Options",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Portfolio Page Title",
	"desc" => "Enter a title for your portfolio page.",
	"id" => $shortname."_portfolio_title",
	"type" => "text",
	"std" => ""),

array( "name" => "Portfolio Page Description",
	"desc" => "Enter a short description for your portfolio. This show next to the title.",
	"id" => $shortname."_portfolio_description",
	"type" => "text",
	"std" => ""),

array( "type" => "close"),

array( "name" => "Blog Options",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Blog Page Title",
	"desc" => "Enter a title for your blog page.",
	"id" => $shortname."_blog_title",
	"type" => "text",
	"std" => ""),

array( "name" => "Blog Page Description",
	"desc" => "Enter a short description for your blog. This show next to the title.",
	"id" => $shortname."_blog_description",
	"type" => "text",
	"std" => ""),

array( "name" => "<b>Show Post Author Bio.</b>",
	   "id" => $shortname."_show_bio",
	   "type" => "checkbox",
	   "desc" => "Check/Uncheck - Do you want to show the author bio on a blog post?"),
	   "std" => "false",
	   
array( "name" => "<b>Show Related Posts</b>",
	   "id" => $shortname."_show_related",
	   "type" => "checkbox",
	   "desc" => "Check/Uncheck - Do you want to show related posts on you blog post?"),
	   "std" => "false",	   

array( "type" => "close"),

// Contact form
array( "name" => "Contact Form",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Email address",
	"desc" => "Enter the address you would like the emails sent to.",
	"id" => $shortname."_contact_email",
	"type" => "text",
	"std" => ""),

array( "name" => "Email copy subject line.",
	"desc" => "Enter what subject line the person submitting the form sees in their inbox when the copy box is checked. EX: You just emailed company name.",
	"id" => $shortname."_contact_copy_subject",
	"type" => "text",
	"std" => ""),

array( "name" => "Email copy from text.",
	"desc" => "Enter what is shown in the from section of the copied email. EX: From company Name email@company.com",
	"id" => $shortname."_contact_copy_from",
	"type" => "text",
	"std" => ""),

array( "name" => "Success message.",
	"desc" => "Enter what is shown when the email is successfully sent.",
	"id" => $shortname."_contact_success",
	"type" => "textarea",
	"std" => ""),
   

array( "type" => "close"),

// Footer Options and social media
array( "name" => "Footer",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Contact Form Button Big Text",
	"desc" => "Enter something like Contact us, or Get in Touch.",
	"id" => $shortname."_cf_button_big",
	"type" => "text",
	"std" => ""),

array( "name" => "Contact Form Button Small Text",
	"desc" => "Enter something like, How May we Help You?, or whatever you like.",
	"id" => $shortname."_cf_button_small",
	"type" => "text",
	"std" => ""),

array( "name" => "Contact Form Button URL",
	"desc" => "Enter the URL to where this Contact button will take the user. Should be your contact page.",
	"id" => $shortname."_footer_formurl",
	"type" => "text",
	"std" => ""),

array( "name" => "Social Links Title",
	"desc" => "Enter the title of the social links area. EX: Get updates",
	"id" => $shortname."_footer_social",
	"type" => "text",
	"std" => ""),

array( "name" => "Social Links Text",
	"desc" => "Enter what you woul;d like to say about you social links area. EX: Subscribe or follow, etc...",
	"id" => $shortname."_footer_social_p",
	"type" => "text",
	"std" => ""),
	
array( "name" => "Feedburner URL",
	"desc" => "Feedburner is a Google service that takes care of your RSS feed. Paste your Feedburner URL here to let readers see it in your website",
	"id" => $shortname."_feedburner",
	"type" => "text",
	"std" => get_bloginfo('rss2_url')),

array( "name" => "Twitter URL",
	"desc" => "If you are using Twitter enter the URL to your Twitter page here.",
	"id" => $shortname."_twitter",
	"type" => "text",
	"std" => ""),

array( "name" => "Facebook URL",
	"desc" => "If you are using Facebook enter the URL to your Facebook page here.",
	"id" => $shortname."_facebook",
	"type" => "text",
	"std" => ""),

array( "name" => "LinkedIn URL",
	"desc" => "If you are using LinkedIn enter the URL to your LinkedIn page here.",
	"id" => $shortname."_linkedin",
	"type" => "text",
	"std" => ""),

array( "name" => "Flickr URL",
	"desc" => "If you are using Flickr enter the URL to your Flickr page here.",
	"id" => $shortname."_flickr",
	"type" => "text",
	"std" => ""),

array( "name" => "Behance URL",
	"desc" => "If you are using Behance enter the URL to your Behance page here.",
	"id" => $shortname."_behance",
	"type" => "text",
	"std" => ""),

array( "name" => "Footer copyright text",
	"desc" => "Enter text used in the bottom left side of the footer. HTML links can be included.",
	"id" => $shortname."_footer_text",
	"type" => "textarea",
	"std" => ""),

array( "name" => "Google Analytics Code",
	"desc" => "You can paste your Google Analytics or other tracking code in this box. This will be automatically added to the footer.",
	"id" => $shortname."_ga_code",
	"type" => "textarea",
	"std" => ""),

 
array( "type" => "close")
 
);


function mytheme_add_admin() {
 
global $themename, $shortname, $options;
 
if ( $_GET['page'] == basename(__FILE__) ) {
 
	if ( 'save' == $_REQUEST['action'] ) {		
 
foreach ($options as $value) {
	if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
 
	header("Location: admin.php?page=theme-options.php&saved=true");
die;
 
} 
else if( 'reset' == $_REQUEST['action'] ) {
 
	foreach ($options as $value) {
		delete_option( $value['id'] ); }
 
	header("Location: admin.php?page=theme-options.php&reset=true");
die;
 
}
}
 
add_menu_page($themename, $themename, 'administrator', basename(__FILE__), 'mytheme_admin');
}

function mytheme_add_init() {

$file_dir=get_bloginfo('template_directory');
wp_enqueue_style("functions", $file_dir."/functions/functions.css", false, "1.0", "all");
wp_enqueue_script("rm_script", $file_dir."/functions/rm_script.js", false, "1.0");

}
function mytheme_admin() {
 
global $themename, $shortname, $options;
$i=0;
 
if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
 
?>
<div class="wrap rm_wrap">
<h2><?php echo $themename; ?> Settings</h2>
 
<div class="rm_opts">
<form method="post">
<?php foreach ($options as $value) {
switch ( $value['type'] ) {
 
case "open":
?>
 
<?php break;
 
case "close":
?>
 
</div>
</div>
<br />

 
<?php break;
 
case "title":
?>
<p>To easily use the <?php echo $themename;?> theme, you can use the menu below.</p>

 
<?php break;
 
case 'text':
?>

<div class="rm_input rm_text">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" />
 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 
 </div>
<?php
break;
 
case 'textarea':
?>

<div class="rm_input rm_textarea">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea>
 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 
 </div>
  
<?php
break;
 
case 'select':
?>

<div class="rm_input rm_select">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	
<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
		<option <?php if (get_settings( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?>
</select>

	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
</div>
<?php
break;
 
case "checkbox":
?>

<div class="rm_input rm_checkbox">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	
<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />


	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 </div>
<?php break; 
case "section":

$i++;

?>

<div class="rm_section">
<div class="rm_title"><h3><img src="<?php bloginfo('template_directory')?>/functions/images/trans.gif" class="inactive" alt="""><?php echo $value['name']; ?></h3><span class="submit"><input name="save<?php echo $i; ?>" type="submit" value="Save changes" />
</span><div class="clearfix"></div></div>
<div class="rm_options">

 
<?php break; } } ?>
 
<input type="hidden" name="action" value="save" />
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
</div> 
 

<?php } ?>
<?php
add_action('admin_init', 'mytheme_add_init');
add_action('admin_menu', 'mytheme_add_admin');
?>
