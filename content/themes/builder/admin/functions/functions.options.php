<?php

add_action('init','of_options');

if (!function_exists('of_options'))
{
	function of_options()
	{
		//Access the WordPress Categories via an Array
		$of_categories = array();  
		$of_categories_obj = get_categories('hide_empty=0');
		foreach ($of_categories_obj as $of_cat) {
		    $of_categories[$of_cat->cat_ID] = $of_cat->cat_name;}
		$categories_tmp = array_unshift($of_categories, "Select a category:");    
	       
		//Access the WordPress Pages via an Array
		$of_pages = array();
		$of_pages_obj = get_pages('sort_column=post_parent,menu_order');    
		foreach ($of_pages_obj as $of_page) {
		    $of_pages[$of_page->ID] = $of_page->post_name; }
		$of_pages_tmp = array_unshift($of_pages, "Select a page:");       
	
		//Testing 
		$of_options_select = array("one","two","three","four","five"); 
		$of_options_radio = array("one" => "One","two" => "Two","three" => "Three","four" => "Four","five" => "Five");
		
		//Sample Homepage blocks for the layout manager (sorter)
		$of_options_homepage_blocks = array
		( 
			"disabled" => array (
				"placebo" 		=> "placebo", //REQUIRED!
				"block_one"		=> "Block One",
				"block_two"		=> "Block Two",
				"block_three"	=> "Block Three",
			), 
			"enabled" => array (
				"placebo" => "placebo", //REQUIRED!
				"block_four"	=> "Block Four",
			),
		);


		//Stylesheets Reader
		$alt_stylesheet_path = LAYOUT_PATH;
		$alt_stylesheets = array();
		
		if ( is_dir($alt_stylesheet_path) ) 
		{
		    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) 
		    { 
		        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) 
		        {
		            if(stristr($alt_stylesheet_file, ".css") !== false)
		            {
		                $alt_stylesheets[] = $alt_stylesheet_file;
		            }
		        }    
		    }
		}


		//Background Images Reader
		$of_theme_layout = array( "1" => "Boxed and 1170px container", "2" => "Boxed and 960px container", "3" => "Fullwidth and 1170px container", "4" => "Fullwidth and 960px container" );
		$of_tag_line_position = array("1" => "Before Slider", "2" => "After Slider");
		$of_portfolio_style = array("1" => "6 Columns Portfolio", "2" => "4 Columns Portfolio", "3" => "3 Columns Portfolio", "4" => "2 Columns Portfolio", "5" => "Portfolio with Sidebar");
		$of_portfolio_details_style = array("1" => "Landscape Style", "2" => "Portrait Style", "3" => "With Sidebar");
		$of_blog_style = array("1" => "Large Images", "2" => "Medium Images");
		$of_blog_sidebar = array("1" => "Right Sidebar", "2" => "Left Sidebar");
		$of_portfolio_sidebar = array("1" => "Right Sidebar", "2" => "Left Sidebar");
		$of_blog_image_hover_icons = array("1" => "Zoom icon + Link icon", "2" => "Zoom icon only", "3" => "Link icon only");
		$of_blog_date_format = array("1" => "American Style", "2" => "European Style");
		$bg_images_path = get_stylesheet_directory(). '/images/bg/'; // change this to where you store your bg images
		$bg_images_url = get_template_directory_uri().'/images/bg/'; // change this to where you store your bg images
		$bg_images = array();
		
		if ( is_dir($bg_images_path) ) {
		    if ($bg_images_dir = opendir($bg_images_path) ) { 
		        while ( ($bg_images_file = readdir($bg_images_dir)) !== false ) {
		            if(stristr($bg_images_file, ".png") !== false || stristr($bg_images_file, ".jpg") !== false) {
		                $bg_images[] = $bg_images_url . $bg_images_file;
		            }
		        }    
		    }
		}
		

		/*-----------------------------------------------------------------------------------*/
		/* TO DO: Add options/functions that use these */
		/*-----------------------------------------------------------------------------------*/
		
		//More Options
		$uploads_arr = wp_upload_dir();
		$all_uploads_path = $uploads_arr['path'];
		$all_uploads = get_option('of_uploads');
		$other_entries = array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");
		$body_repeat = array("no-repeat","repeat-x","repeat-y","repeat");
		$body_pos = array("top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right");
		
		// Image Alignment radio box
		$of_options_thumb_align = array("alignleft" => "Left","alignright" => "Right","aligncenter" => "Center"); 
		
		// Image Links to Options
		$of_options_image_link_to = array("image" => "The Image","post" => "The Post"); 


/*-----------------------------------------------------------------------------------*/
/* The Options Array */
/*-----------------------------------------------------------------------------------*/

// Set the Options Array
global $of_options;
$of_options = array();

//Layout
$of_options[] = array( "name" => __("General Settings","builder"),
					"type" => "heading");


$of_options[] = array( "name" => __("GENERAL SETTINGS","builder"),
					"desc" => __("Select your themes alternative color scheme","builder"),
					"id" => "alt_stylesheet",
					"std" => "empty.css",
					"type" => "select",
					"options" => $alt_stylesheets);


$of_options[] = array( "name" => "",
					"desc" => __("Choose WebSite Layout","builder"),
					"id" => "theme_layout",
					"std" => "1",
					"type" => "select",
					"options" => $of_theme_layout);

$of_options[] = array( "name" => "",
					"desc" => __("Upload your favicon.ico","builder"),
					"id" => "header_favicon",
					"std" => "http://www.orange-idea.com/assets/builder/favicon.ico",
					"type" => "media");

					  
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Container Background","builder"),
					"id" => "theme_conatiner_bg_color",
					"std" => "#fdfdfd",
					"type" => "color");

$of_options[] = array( "name" =>  __("BOXED LAYOUT SETTINGS","builder"),
					"desc" => __("Pick a color for the Background","builder"),
					"id" => "theme_boxed_bg_color",
					"std" => "#fdfdfd",
					"type" => "color");

$of_options[] = array( "name" => "",
					"desc" => __("Past the value for top and bottom margin for main container","builder"),
					"id" => "theme_boxed_margin",
					"std" => "0",
					"type" => "text");
					
$of_options[] = array( "name" => " ",
					"desc" => __("Pick a image for the Background ( Choose first image to show only background color )","builder"),
					"id" => "theme_boxed_bg",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);



$of_options[] = array("name" =>  __("HEADERS TYPOGRAPHY","builder"),
					"desc" => __("Specify the h1 header font properties","builder"),
					"id" => "headers_font_one",
					"std" => array('size' => '36px', 'face' => 'Open Sans','style' => 'normal','color' => '#555555'),
					"type" => "typography");  

$of_options[] = array("name" =>  "",
					"desc" => __("Specify the h2 header font properties","builder"),
					"id" => "headers_font_two",
					"std" => array('size' => '30px','face' => 'Open Sans','style' => 'normal','color' => '#555555'),
					"type" => "typography");  


$of_options[] = array("name" =>  "",
					"desc" => __("Specify the h3 header font properties","builder"),
					"id" => "headers_font_three",
					"std" => array('size' => '24px','face' => 'Open Sans','style' => 'normal','color' => '#555555'),
					"type" => "typography");  

$of_options[] = array("name" =>  "",
					"desc" => __("Specify the h4 header font properties","builder"),
					"id" => "headers_font_four",
					"std" => array('size' => '18px','face' => 'Open Sans','style' => 'normal','color' => '#555555'),
					"type" => "typography");  

$of_options[] = array("name" =>  "",
					"desc" => __("Specify the h5 header font properties","builder"),
					"id" => "headers_font_five",
					"std" => array('size' => '14px','face' => 'Open Sans','style' => 'normal','color' => '#555555'),
					"type" => "typography");  

$of_options[] = array("name" =>  "",
					"desc" => __("Specify the h6 header font properties","builder"),
					"id" => "headers_font_six",
					"std" => array('size' => '12px','face' => 'Open Sans','style' => 'normal','color' => '#555555'),
					"type" => "typography");


$of_options[] = array("name" =>  __("BODY TYPOGRAPHY","builder"),
					"desc" => __("Specify body font properties","builder"),
					"id" => "body_font",
					"std" => array('size' => '12px', 'face' => 'arial','style' => 'normal','color' => '#666666'),
					"type" => "typography");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the links","builder"),
					"id" => "main_conent_links",
					"std" => "#AEC71E",
					"type" => "color");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the links, when hover","builder"),
					"id" => "main_conent_links_hover",
					"std" => "#000000",
					"type" => "color");



$of_options[] = array( "name" => __("REVOLUTION SLIDER","builder"),
					"desc" => __("Show Revolution Slider on the HomePage?","builder"),
					"id" => "revolution_homepage",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" => "",
					"desc" => __("Show Revolution Slider on other pages?","builder"),
					"id" => "revolution_index",
					"std" => 0,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" => __("CUSTOM CSS","builder"),
					"desc" => __("Please put your custom css here","builder"),
					"id" => "custom_css",
					"std" => "",
					"type" => "textarea");



//Top Line
$of_options[] = array( "name" => __("Top Line","builder"),
					"type" => "heading");

$of_options[] = array( "name" => __("Show/Hide Top Line","builder"),
					"desc" => __("Show Top Line","builder"),
					"id" => "top_line_show",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" =>  __("TOP LINE BACKGROND","builder"),
					"desc" => __("Pick a color for the 'Top Line' area background","builder"),
					"id" => "theme_colors_top_line",
					"std" => "#AEC71E",
					"type" => "color");

$of_options[] = array( "name" => "",
					"desc" => __("Pick a image for the 'Top Line' area background ( Choose first image to show only background color )","builder"),
					"id" => "theme_colors_top_line_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);


$of_options[] = array( "name" => __("TOP LINE TEXT","builder"),
					"desc" => __("Past your text or HTML","builder"),
					"id" => "header_top_line",
					"std" => "",
					"type" => "text");					

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Top Line text","builder"),
					"id" => "theme_colors_top_line_text",
					"std" => "#FFFFFF",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Top Line links","builder"),
					"id" => "theme_colors_top_line_a",
					"std" => "#FFFFFF",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Top Line mouse over links","builder"),
					"id" => "theme_colors_top_line_a_hover",
					"std" => "#FFFFFF",
					"type" => "color");


					
$of_options[] = array( "name" => __("SOCIAL ICONS","builder"),
					"desc" => __("Twitter","builder"),
					"id" => "header_social_tw",
					"std" => "",
					"type" => "text");	

$of_options[] = array( "name" => "",
					"desc" => __("Facebook","builder"),
					"id" => "header_social_fb",
					"std" => "",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Google +","builder"),
					"id" => "header_social_g",
					"std" => "",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Dribbble","builder"),
					"id" => "header_social_dr",
					"std" => "",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Flickr","builder"),
					"id" => "header_social_fl",
					"std" => "",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("YouTube","builder"),
					"id" => "header_social_yt",
					"std" => "",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Linkedin","builder"),
					"id" => "header_social_in",
					"std" => "",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Pinterest","builder"),
					"id" => "header_social_pi",
					"std" => "",
					"type" => "text");



//Logo And Menu
$of_options[] = array( "name" => __("Logo And Menu","builder"),
					"type" => "heading");
          

$of_options[] = array( "name" => __("LOGO AND MENU AREA","builder"),
					"desc" => __("Top padding","builder"),
					"id" => "logo_and_menu_t_padding",
					"std" => "0",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Bottom padding","builder"),
					"id" => "logo_and_menu_b_padding",
					"std" => "0",
					"type" => "text");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the 'Logo and Menu' area background","builder"),
					"id" => "logo_and_menu_bg",
					"std" => "#F9F9F9",
					"type" => "color");

$of_options[] = array( "name" => "",
					"desc" => __("Pick a image for the 'Logo and Menu' area background ( Choose first image to show only background color )","builder"),
					"id" => "logo_and_menu_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);



$of_options[] = array( "name" => __("LOGO OPTIONS","builder"),
					"desc" => __("Upload your logo","builder"),
					"id" => "header_logo",
					"std" => "http://www.orange-idea.com/assets/builder/logo.png",
					"type" => "media");

$of_options[] = array( "name" => "",
					"desc" => __("Top margin for logo"),
					"id" => "logo_and_menu_logo_margin",
					"std" => "20",
					"type" => "text");
					

$of_options[] = array( "name" => __("MENU OPTIONS","builder"),
					"desc" => __("Top margin for menu","builder"),
					"id" => "logo_and_menu_menu_margin",
					"std" => "0",
					"type" => "text");
					
$of_options[] = array( "name" => "",
					"desc" => __("Space between menu items","builder"),
					"id" => "logo_and_menu_menu_li_margin",
					"std" => "0",
					"type" => "text");	
									
$of_options[] = array( "name" => "",
					"desc" => __("Menu items Border Radius","builder"),
					"id" => "logo_and_menu_menu_li_radius",
					"std" => "0",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Menu items Font Size","builder"),
					"id" => "logo_and_menu_menu_li_font",
					"std" => "12",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Menu items Horizontal Padding","builder"),
					"id" => "logo_and_menu_menu_li_h_padding",
					"std" => "20",
					"type" => "text");
					
$of_options[] = array( "name" => "",
					"desc" => __("Menu items Vertical Padding","builder"),
					"id" => "logo_and_menu_menu_li_v_padding",
					"std" => "25",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Show Menu Items shadow","builder"),
					"id" => "logo_and_menu_menu_li_shadow",
					"std" => 0,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" => "",
					"desc" => __("Show Dropdown Menu Triangles","builder"),
					"id" => "logo_and_menu_menu_li_triangles",
					"std" => 0,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Menu items shadow","builder"),
					"id" => "logo_and_menu_menu_li_shadow_color",
					"std" => "#ededed",
					"type" => "color");
					
					
					
$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the Menu item background","builder"),
					"id" => "logo_and_menu_menu_li_bg",
					"std" => "#f9f9f9",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Menu item text","builder"),
					"id" => "logo_and_menu_menu_li_a_color",
					"std" => "#666666",
					"type" => "color");


$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the Menu item background when mouse over","builder"),
					"id" => "logo_and_menu_menu_li_bg_hover",
					"std" => "#444444",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Menu item text when mouse over","builder"),
					"id" => "logo_and_menu_menu_li_a_hover",
					"std" => "#ffffff",
					"type" => "color");



$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the SubLevel Menu background","builder"),
					"id" => "logo_and_menu_menu_sublevel_li_bg",
					"std" => "#444444",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the SubLevel Menu text","builder"),
					"id" => "logo_and_menu_menu_sublevel_li_a",
					"std" => "#bcbcbc",
					"type" => "color");					
					



$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the SubLevel Menu background when mouse over","builder"),
					"id" => "logo_and_menu_menu_sublevel_li_hover_bg",
					"std" => "#AEC71E",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the SubLevel Menu text when mouse over","builder"),
					"id" => "logo_and_menu_menu_sublevel_li_a_hover",
					"std" => "#ffffff",
					"type" => "color");	


					


$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the Current Menu item background","builder"),
					"id" => "logo_and_menu_menu_li_currnet_bg",
					"std" => "#AEC71E",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Current Menu item text","builder"),
					"id" => "logo_and_menu_menu_li_currnet_a_color",
					"std" => "#ffffff",
					"type" => "color");

















					
//Tag Line
$of_options[] = array( "name" => __("Tag Line","builder"),
					"type" => "heading");
					
$of_options[] = array( "name" => __("Show/Hide Tag Line","builder"),
					"desc" => __("Show Tag Line?","builder"),
					"id" => "tag_line_show",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" => "",
					"desc" => __("Show Breadcumbs?","builder"),
					"id" => "breadcumbs",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" => "",
					"desc" => __("Choose Tag Line Position on Home Page","builder"),
					"id" => "tag_line_position",
					"std" => "1",
					"type" => "select",
					"options" => $of_tag_line_position);

$of_options[] = array( "name" => __("TAG LINE SETTINGS","builder"),
					"desc" => __("Homepage Tag Line content","builder"),
					"id" => "header_tagline",
					"std" => "<strong class='colored'>BUILDER:</strong> Super powerful <span class='colored'>&amp;</span> responsive wordpress theme with hundreds options.",
					"type" => "text");	

$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the tag line background","builder"),
					"id" => "tag_line_bg",
					"std" => "#ffffff",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the tag line top border","builder"),
					"id" => "tag_line_border_top",
					"std" => "#ededed",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the tag line bottom border","builder"),
					"id" => "tag_line_border_bottom",
					"std" => "#ededed",
					"type" => "color");

$of_options[] = array( "name" => " ",
					"desc" => __("Tag Line top padding","builder"),
					"id" => "tag_line_padding_top",
					"std" => "20",
					"type" => "text");
					
$of_options[] = array( "name" => "",
					"desc" => __("Tag Line bottom padding","builder"),
					"id" => "tag_line_padding_bottom",
					"std" => "20",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Tag Line bottom margin","builder"),
					"id" => "main_content_margin_top",
					"std" => "30",
					"type" => "text");

$of_options[] = array( "name" => __("BACKGROUND PATTERNS","builder"),
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "tag_line_custom_bg",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);





/* Page Sidebar */
$of_options[] = array( "name" => __("Page Sidebar","builder"),
					"type" => "heading");

$of_options[] = array( "name" =>  __("PAGE SIDEBAR","builder"),
					"desc" => __("How many sidebars do you want?","builder"),
					"id" => "page_sidebar_generator",
					"std" => "8",
					"type" => "text");



$of_options[] = array( "name" =>  __("SIDEBAR AREA SETTINGS","builder"),
					"desc" => __("Pick a color for the widget background","builder"),
					"id" => "page_sidebar_bg_color",
					"std" => "",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Border radius value","builder"),
					"id" => "page_sidebar_border_radius",
					"std" => "0",
					"type" => "text");



$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "page_sidebar_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);


$of_options[] = array( "name" =>  __("WIDGET SETTINGS","builder"),
					"desc" => __("Pick a color for the widget background","builder"),
					"id" => "page_sidebar_widget_bg_color",
					"std" => "#f9f9f9",
					"type" => "color");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget border","builder"),
					"id" => "page_sidebar_widget_border_color",
					"std" => "#f1f1f1",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Border radius value","builder"),
					"id" => "page_sidebar_widget_border_radius",
					"std" => "4",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "page_sidebar_widget_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);


$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the widget header","builder"),
					"id" => "page_sidebar_widget_header_color",
					"std" => "#333333",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for 'separator' after widget header","builder"),
					"id" => "page_sidebar_widget_hr",
					"std" => "#ededed",
					"type" => "color");					


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget text","builder"),
					"id" => "page_sidebar_widget_text_color",
					"std" => "#666666",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget links","builder"),
					"id" => "page_sidebar_widget_links_color",
					"std" => "#333333",
					"type" => "color");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget links on hover","builder"),
					"id" => "page_sidebar_widget_links_color_hover",
					"std" => "#AEC71E",
					"type" => "color");




/* Portfolio */
$of_options[] = array( "name" => __("Portfolio","builder"),
					"type" => "heading");



$of_options[] = array( "name" => __("CHOOSE PORTFOLIO STYLE","builder"),
					"desc" => "",
					"id" => "sl_portfolio_style",
					"std" => "1",
					"type" => "select",
					"options" => $of_portfolio_style);  
					
$of_options[] = array("name" =>  "",
					"desc" => __("Amount of projects on one page","builder"),
					"id" => "sl_portfolio_projects",
					"std" => "10",
					"type" => "text");


$of_options[] = array( "name" => __("FILTER BUTTONS SETTINGS","builder"),
					"desc" => __("Vertical padding amount","builder"),
					"id" => "portfolio_filter_padding_v",
					"std" => "4",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Horizontal padding amount","builder"),
					"id" => "portfolio_filter_padding_h",
					"std" => "8",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Right margin amount","builder"),
					"id" => "portfolio_filter_margin",
					"std" => "3",
					"type" => "text");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for buttons background","builder"),
					"id" => "portfolio_filter_bg_color",
					"std" => "#3a3a3a",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for buttons border","builder"),
					"id" => "portfolio_filter_border_color",
					"std" => "#3a3a3a",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for buttons text","builder"),
					"id" => "portfolio_filter_text_color",
					"std" => "#ffffff",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for text shadow","builder"),
					"id" => "portfolio_filter_text_shadow",
					"std" => "#111111",
					"type" => "color");


$of_options[] = array( "name" => "",
					"desc" => __("Font size","builder"),
					"id" => "portfolio_filter_text_size",
					"std" => "11",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Border radius amount","builder"),
					"id" => "portfolio_filter_border_radius",
					"std" => "0",
					"type" => "text");

$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for buttons background when hover","builder"),
					"id" => "portfolio_filter_bg_color_hover",
					"std" => "#AEC71E",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for buttons text when hover","builder"),
					"id" => "portfolio_filter_text_color_hover",
					"std" => "#ffffff",
					"type" => "color");



$of_options[] = array( "name" =>  __("IMAGE HOVER EFFECTS (RGBA)","builder"),
					"desc" => __("First rgba value (red color)","builder"),
					"id" => "portfolio_image_bg_1",
					"std" => "0",
					"type" => "text");

$of_options[] = array( "name" =>  "",
					"desc" => __("Second rgba value (green color)","builder"),
					"id" => "portfolio_image_bg_2",
					"std" => "0",
					"type" => "text");

$of_options[] = array( "name" =>  "",
					"desc" => __("Third rgba value (blue color)","builder"),
					"id" => "portfolio_image_bg_3",
					"std" => "0",
					"type" => "text");


$of_options[] = array( "name" =>  "",
					"desc" => __("Fourth rgba value (opacity)","builder"),
					"id" => "portfolio_image_bg_op",
					"std" => "0.15",
					"type" => "text");
					
					

$of_options[] = array( "name" => __("ZOOM AND LINK ICONS SETTINGS","builder"),
					"desc" => __("Choose Icons for image hover","builder"),
					"id" => "portfolio_image_hover_icons",
					"std" => "1",
					"type" => "select",
					"options" => $of_blog_image_hover_icons);

$of_options[] = array( "name" => "",
					"desc" => __("Upload Link icon","builder"),
					"id" => "portfolio_image_icons_link",
					"std" => "http://www.orange-idea.com/assets/builder/link.png",
					"type" => "media");

$of_options[] = array( "name" => "",
					"desc" => __("Upload Zoom icon","builder"),
					"id" => "portfolio_image_icons_zoom",
					"std" => "http://www.orange-idea.com/assets/builder/zoom.png",
					"type" => "media");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the icons background","builder"),
					"id" => "portfolio_image_icons_bg",
					"std" => "#000000",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the icons background when hover","builder"),
					"id" => "portfolio_image_icons_bg_hover",
					"std" => "#AEC71E",
					"type" => "color");


$of_options[] = array( "name" => __("SMALL DESCRIPTIOS SETTINGS","builder"),
					"desc" => "Show small description?",
					"id" => "portfolio_descr_show",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the background","builder"),
					"id" => "portfolio_descr_bg_color",
					"std" => "#f9f9f9",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the border","builder"),
					"id" => "portfolio_descr_border_color",
					"std" => "#ededed",
					"type" => "color");

$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "portfolio_descr_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);

$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the links","builder"),
					"id" => "portfolio_descr_links_color",
					"std" => "#AEC71E",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the links when hover","builder"),
					"id" => "portfolio_descr_links_color_hover",
					"std" => "#000000",
					"type" => "color");


$of_options[] = array( "name" => "",
					"desc" => __("Show small description?","builder"),
					"id" => "portfolio_descr_clo_text",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color small description","builder"),
					"id" => "portfolio_descr_text_color",
					"std" => "#747474",
					"type" => "color");


$of_options[] = array( "name" =>  "",
					"desc" => __("Small description font size","builder"),
					"id" => "portfolio_descr_text_size",
					"std" => "11",
					"type" => "text");



					
/* Portfolio Post */
$of_options[] = array( "name" => __("Portfolio Post","builder"),
					"type" => "heading");


$of_options[] = array( "name" => __("CHOOSE DETAILS PAGE STYLE","builder"),
					"desc" => "",
					"id" => "sl_portfolio_details_style",
					"std" => "1",
					"type" => "select",
					"options" => $of_portfolio_details_style); ;

					
$of_options[] = array( "name" => __("DESCRIPTION SETTINGS","builder"),
					"desc" => __("Show 'Next post and Previous post' pagination?","builder"),
					"id" => "portfolio_details_pagination",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the 'Next post and Previous post","builder"),
					"id" => "portfolio_post_show_posts_meta_color",
					"std" => "#3a3a3a",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the 'Next post and Previous post color when hover","builder"),
					"id" => "portfolio_post_show_posts_meta_color_hover",
					"std" => "#AEC71E",
					"type" => "color");




$of_options[] = array( "name" =>  "",
					"desc" => __("Padding value","builder"),
					"id" => "portfolio_post_item_description_padding",
					"std" => "20",
					"type" => "text");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for background","builder"),
					"id" => "portfolio_post_item_description_bg_color",
					"std" => "#f9f9f9",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for text","builder"),
					"id" => "portfolio_post_item_description_text_color",
					"std" => "#747474",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for border","builder"),
					"id" => "portfolio_post_item_description_border_color",
					"std" => "#ededed",
					"type" => "color");

$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "portfolio_post_item_description_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);



/* Portfolio Sidebar */
$of_options[] = array( "name" => __("Portfolio Sidebar","builder"),
					"type" => "heading");
				

$of_options[] = array( "name" => __("CHOOSE SIDEBAR POSITION","builder"),
					"desc" => "",
					"id" => "portfolio_sidebar_position",
					"std" => "1",
					"type" => "select",
					"options" => $of_portfolio_sidebar);


$of_options[] = array( "name" =>  __("SIDEBAR AREA SETTINGS","builder"),
					"desc" => "Pick a color for the widget background",
					"id" => "portfolio_sidebar_bg_color",
					"std" => "",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Border radius value","builder"),
					"id" => "portfolio_sidebar_border_radius",
					"std" => "0",
					"type" => "text");



$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "portfolio_sidebar_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);




$of_options[] = array( "name" =>  __("WIDGET SETTINGS","builder"),
					"desc" => __("Pick a color for the widget background","builder"),
					"id" => "portfolio_sidebar_widget_bg_color",
					"std" => "#f9f9f9",
					"type" => "color");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget border","builder"),
					"id" => "portfolio_sidebar_widget_border_color",
					"std" => "#f1f1f1",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Border radius value","builder"),
					"id" => "portfolio_sidebar_widget_border_radius",
					"std" => "4",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "portfolio_sidebar_widget_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);


$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the widget header","builder"),
					"id" => "portfolio_sidebar_widget_header_color",
					"std" => "#333333",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for 'separator' after widget header","builder"),
					"id" => "portfolio_sidebar_widget_hr",
					"std" => "#ededed",
					"type" => "color");					


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget text","builder"),
					"id" => "portfolio_sidebar_widget_text_color",
					"std" => "#666666",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget links","builder"),
					"id" => "portfolio_sidebar_widget_links_color",
					"std" => "#333333",
					"type" => "color");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget links on hover","builder"),
					"id" => "portfolio_sidebar_widget_links_color_hover",
					"std" => "#AEC71E",
					"type" => "color");


/* Blog Settings */
$of_options[] = array( "name" => __("Blog","builder"),
					"type" => "heading");


$of_options[] = array( "name" => __("CHOOSE BLOG STYLE","builder"),
					"desc" => "",
					"id" => "sl_blog_style",
					"std" => "1",
					"type" => "select",
					"options" => $of_blog_style);
					
					
$of_options[] = array( "name" => __("DATE SETTINGS","builder"),
					"desc" => __("Show Posts Date?","builder"),
					"id" => "blog_show_posts_date",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" => "",
					"desc" => __("Choose Date Format","builder"),
					"id" => "blog_date_format",
					"std" => "1",
					"type" => "select",
					"options" => $of_blog_date_format);


$of_options[] = array( "name" => "",
					"desc" => __("Show Date Icon?","builder"),
					"id" => "blog_show_date_icon",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Blog date background","builder"),
					"id" => "blog_date_bg",
					"std" => "#3a3a3a",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Blog date text","builder"),
					"id" => "blog_date_color",
					"std" => "#ffffff",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Blog date text shadow","builder"),
					"id" => "blog_date_text_shadow",
					"std" => "#000000",
					"type" => "color");

$of_options[] = array("name" =>  "",
					"desc" => __("Past the value for border radius","builder"),
					"id" => "blog_date_border_radius",
					"std" => "0",
					"type" => "text");


$of_options[] = array( "name" => __("META TAGS SETTINGS","builder"),
					"desc" => __("Show Post Author?","builder"),
					"id" => "blog_show_posts_meta_author",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" => "",
					"desc" => __("Show Post Category?","builder"),
					"id" => "blog_show_posts_meta_category",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" => "",
					"desc" => __("Show Post Comments?","builder"),
					"id" => "blog_show_posts_meta_comments",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Blog meta color","builder"),
					"id" => "blog_show_posts_meta_color",
					"std" => "#3a3a3a",
					"type" => "color");
					

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Blog meta color when hover","builder"),
					"id" => "blog_show_posts_meta_color_hover",
					"std" => "#AEC71E",
					"type" => "color");



$of_options[] = array( "name" =>  __("IMAGE HOVER EFFECTS (RGBA)","builder"),
					"desc" => __("First rgba value (red color)","builder"),
					"id" => "blog_image_bg_1",
					"std" => "0",
					"type" => "text");

$of_options[] = array( "name" =>  "",
					"desc" => __("Second rgba value (green color)","builder"),
					"id" => "blog_image_bg_2",
					"std" => "0",
					"type" => "text");

$of_options[] = array( "name" =>  "",
					"desc" => __("Third rgba value (blue color)","builder"),
					"id" => "blog_image_bg_3",
					"std" => "0",
					"type" => "text");


$of_options[] = array( "name" =>  "",
					"desc" => __("Fourth rgba value (opacity)","builder"),
					"id" => "blog_image_bg_op",
					"std" => "0.15",
					"type" => "text");
					
					

$of_options[] = array( "name" => __("ZOOM AND LINK ICONS SETTINGS","builder"),
					"desc" => __("Choose Icons for image hover","builder"),
					"id" => "blog_image_hover_icons",
					"std" => "1",
					"type" => "select",
					"options" => $of_blog_image_hover_icons);

$of_options[] = array( "name" => "",
					"desc" => __("Upload Link icon","builder"),
					"id" => "blog_image_icons_link",
					"std" => "http://www.orange-idea.com/assets/builder/link.png",
					"type" => "media");

$of_options[] = array( "name" => "",
					"desc" => __("Upload Zoom icon","builder"),
					"id" => "blog_image_icons_zoom",
					"std" => "http://www.orange-idea.com/assets/builder/zoom.png",
					"type" => "media");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the icons background","builder"),
					"id" => "blog_image_icons_bg",
					"std" => "#000000",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the icons background when hover","builder"),
					"id" => "blog_image_icons_bg_hover",
					"std" => "#AEC71E",
					"type" => "color");


$of_options[] = array( "name" =>  __("POST PREVIEW SETTINGS","builder"),
					"desc" => __("Pick a color for text","builder"),
					"id" => "blog_item_description_text_color",
					"std" => "#747474",
					"type" => "color");



$of_options[] = array( "name" =>  "",
					"desc" => __("Padding value","builder"),
					"id" => "blog_item_description_padding",
					"std" => "20",
					"type" => "text");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for background","builder"),
					"id" => "blog_item_description_bg_color",
					"std" => "#f9f9f9",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for border","builder"),
					"id" => "blog_item_description_border_color",
					"std" => "#ededed",
					"type" => "color");

$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "blog_item_description_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);


$of_options[] = array( "name" => __("BLOG ARCHIVE","builder"),
					"desc" => __("Show Featured Images In Blog Archive?","builder"),
					"id" => "blog_archive_show_imges",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" =>  "",
					"desc" => __("'Blog Archive' page title","builder"),
					"id" => "blog_archive_title",
					"std" => "Blog Archive",
					"type" => "text");






					
/* Blog Post */
$of_options[] = array( "name" => __("Blog Post","builder"),
					"type" => "heading");



$of_options[] = array( "name" => __("FEATURED IMAGE SETTINGS","builder"),
					"desc" => __("Show Featured Image?","builder"),
					"id" => "blog_post_show_featured_image",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" => __("META TAGS SETTINGS","builder"),
					"desc" => __("Show Post Title?","builder"),
					"id" => "blog_post_show_posts_meta_title",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");



$of_options[] = array( "name" => "",
					"desc" => __("Show Post Author?","builder"),
					"id" => "blog_post_show_posts_meta_author",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");




$of_options[] = array( "name" => "",
					"desc" => __("Show Post Category?","builder"),
					"id" => "blog_post_show_posts_meta_category",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");

$of_options[] = array( "name" => "",
					"desc" => __("Show Post Comments?","builder"),
					"id" => "blog_post_show_posts_meta_comments",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");



$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Blog meta color","builder"),
					"id" => "blog_post_show_posts_meta_color",
					"std" => "#b7b7b7",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Blog meta color when hover","builder"),
					"id" => "blog_post_show_posts_meta_color_hover",
					"std" => "#AEC71E",
					"type" => "color");


$of_options[] = array( "name" =>  __("POST SETTINGS","builder"),
					"desc" => "Padding value",
					"id" => "blog_post_item_description_padding",
					"std" => "20",
					"type" => "text");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for background","builder"),
					"id" => "blog_post_item_description_bg_color",
					"std" => "#f9f9f9",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for text","builder"),
					"id" => "blog_post_item_description_text_color",
					"std" => "#747474",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for border","builder"),
					"id" => "blog_post_item_description_border_color",
					"std" => "#ededed",
					"type" => "color");

$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "blog_post_item_description_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);


$of_options[] = array( "name" => __("SHARE OPTIONS","builder"),
					"desc" => "Show 'Share This' button?",
					"id" => "blog_post_show_share_button",
					"std" => 0,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" =>  "",
					"desc" => __("'Share' text","builder"),
					"id" => "blog_post_show_share_button_text",
					"std" => "Share This Story:",
					"type" => "text");

$of_options[] = array( "name" =>  "",
					"desc" => __("Padding value","builder"),
					"id" => "blog_share_padding",
					"std" => "7",
					"type" => "text");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for Background","builder"),
					"id" => "blog_share_bg_color",
					"std" => "#ededed",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for text","builder"),
					"id" => "blog_share_text_color",
					"std" => "#3d3d3d",
					"type" => "color");

$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "blog_share_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);

$of_options[] = array( "name" => __("ABOUT AUTHOR OPTIONS","builder"),
					"desc" => __("Show 'About Author'?","builder"),
					"id" => "blog_post_show_author",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" => "",
					"desc" => __("Upload your avatar","builder"),
					"id" => "blog_post_show_author_avatar",
					"std" => "http://1.s3.envato.com/files/31496845/oi-80.jpg",
					"type" => "media");


$of_options[] = array( "name" =>  "",
					"desc" => __("Header text","builder"),
					"id" => "blog_post_show_author_header",
					"std" => "About The Author",
					"type" => "text");


$of_options[] = array( "name" =>  "",
					"desc" => __("Padding value","builder"),
					"id" => "blog_author_item_description_padding",
					"std" => "20",
					"type" => "text");



$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for text","builder"),
					"id" => "blog_author_item_description_text_color",
					"std" => "#747474",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for background","builder"),
					"id" => "blog_author_item_description_bg_color",
					"std" => "#f9f9f9",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for border","builder"),
					"id" => "blog_author_item_description_border_color",
					"std" => "#ededed",
					"type" => "color");

$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "blog_author_item_description_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);



$of_options[] = array( "name" => __("COMMENTS OPTIONS","builder"),
					"desc" => __("Show 'comments'?","builder"),
					"id" => "blog_post_show_comments",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" =>  "",
					"desc" => __("Left padding value","builder"),
					"id" => "blog_comments_padding",
					"std" => "20",
					"type" => "text");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for MAIN LEFT border","builder"),
					"id" => "blog_comments_border_color",
					"std" => "#ededed",
					"type" => "color");
					
$of_options[] = array( "name" =>  " ",
					"desc" => __("Left padding value","builder"),
					"id" => "blog_comments_li_padding",
					"std" => "20",
					"type" => "text");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for comments border","builder"),
					"id" => "blog_comments_border_color",
					"std" => "#ededed",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for comments background","builder"),
					"id" => "blog_comments_bg_color",
					"std" => "#f9f9f9",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for text","builder"),
					"id" => "blog_comments_text_color",
					"std" => "#747474",
					"type" => "color");


$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "blog_comments_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);




















/* Blog Sidebar */
$of_options[] = array( "name" => __("Blog Sidebar","builder"),
					"type" => "heading");


$of_options[] = array( "name" => __("CHOOSE SIDEBAR POSITION","builder"),
					"desc" => "",
					"id" => "blog_sidebar_position",
					"std" => "1",
					"type" => "select",
					"options" => $of_blog_sidebar);


$of_options[] = array( "name" =>  __("SIDEBAR AREA SETTINGS","builder"),
					"desc" => __("Pick a color for the widget background","builder"),
					"id" => "blog_sidebar_bg_color",
					"std" => "",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Border radius value","builder"),
					"id" => "blog_sidebar_border_radius",
					"std" => "0",
					"type" => "text");



$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "blog_sidebar_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);




$of_options[] = array( "name" =>  __("WIDGET SETTINGS","builder"),
					"desc" => __("Pick a color for the widget background","builder"),
					"id" => "blog_sidebar_widget_bg_color",
					"std" => "#f6f6f6",
					"type" => "color");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget border","builder"),
					"id" => "blog_sidebar_widget_border_color",
					"std" => "#f1f1f1",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Border radius value","builder"),
					"id" => "blog_sidebar_widget_border_radius",
					"std" => "0",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "blog_sidebar_widget_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);


$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the widget header","builder"),
					"id" => "blog_sidebar_widget_header_color",
					"std" => "#333333",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for 'separator' after widget header","builder"),
					"id" => "blog_sidebar_widget_hr",
					"std" => "#ededed",
					"type" => "color");					


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget text","builder"),
					"id" => "blog_sidebar_widget_text_color",
					"std" => "#666666",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget links","builder"),
					"id" => "blog_sidebar_widget_links_color",
					"std" => "#333333",
					"type" => "color");


$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the widget links on hover","builder"),
					"id" => "blog_sidebar_widget_links_color_hover",
					"std" => "#AEC71E",
					"type" => "color");







/* Pagination */
$of_options[] = array( "name" => __("Pagination","builder"),
					"type" => "heading");

$of_options[] = array( "name" =>  __("PAGINATION SETTINGS","builder"),
					"desc" => __("Pick a color for the element background","builder"),
					"id" => "pagination_bg_color",
					"std" => "#3a3a3a",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the element text","builder"),
					"id" => "pagination_text_color",
					"std" => "#ffffff",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the element text shadow","builder"),
					"id" => "pagination_text_shadow",
					"std" => "#222222",
					"type" => "color");


$of_options[] = array( "name" =>  "",
					"desc" => __("Vertical padding value","builder"),
					"id" => "pagination_padding_v",
					"std" => "4",
					"type" => "text");

$of_options[] = array( "name" =>  "",
					"desc" => __("Horizontal padding value","builder"),
					"id" => "pagination_padding_h",
					"std" => "10",
					"type" => "text");

$of_options[] = array( "name" =>  "",
					"desc" => __("Horizontal padding value","builder"),
					"id" => "pagination_border_radius",
					"std" => "0",
					"type" => "text");


$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the current and hover elements background","builder"),
					"id" => "pagination_hover_bg_color",
					"std" => "#AEC71E",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the current and hover elements text","builder"),
					"id" => "pagination_hover_text_color",
					"std" => "#ffffff",
					"type" => "color");
					

$of_options[] = array( "name" => " ",
					"desc" => __("Select a background pattern ( Choose first image to show only background color )","builder"),
					"id" => "pagination_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);




//Footer
$of_options[] = array( "name" => __("Footer","builder"),
					"type" => "heading");
          

$of_options[] = array( "name" => __("TWITTER FEED SETTINGS","builder"),
					"desc" => __("Show Twitter Feed?","builder"),
					"id" => "show_twitter_feed",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" => "",
					"desc" => __("Text for Header","builder"),
					"id" => "footer_social_tw_header",
					"std" => "Twitter Feed",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Description","builder"),
					"id" => "footer_social_tw_descr",
					"std" => "Find out what's happening, right now, with the people and organizations you care about.",
					"type" => "textarea");

$of_options[] = array( "name" => "",
					"desc" => __("Twitter user name","builder"),
					"id" => "footer_social_tw_user",
					"std" => "Orange_Idea_RU",
					"type" => "text");


$of_options[] = array( "name" => __("FOOTER LOGO","builder"),
					"desc" => __("Upload your logo for footer","builder"),
					"id" => "footer_logo",
					"std" => "http://www.orange-idea.com/assets/builder/logo-footer.png",
					"type" => "media");
					

$of_options[] = array( "name" => __("SOCIAL ICONS","builder"),
					"desc" => __("Twitter","builder"),
					"id" => "footer_social_tw",
					"std" => "http://twitter.com/",
					"type" => "text");	

$of_options[] = array( "name" => "",
					"desc" => __("Facebook","builder"),
					"id" => "footer_social_fb",
					"std" => "https://facebook.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Google +","builder"),
					"id" => "footer_social_g",
					"std" => "http://plus.google.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Dribbble","builder"),
					"id" => "footer_social_dr",
					"std" => "http://dribbble.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Flickr","builder"),
					"id" => "footer_social_fl",
					"std" => "http://flickr.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("YouTube","builder"),
					"id" => "footer_social_yt",
					"std" => "http://youtube.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Linkedin","builder"),
					"id" => "footer_social_in",
					"std" => "http://linkedin.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Pinterest","builder"),
					"id" => "footer_social_pi",
					"std" => "http://pinterest.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Skype","builder"),
					"id" => "footer_social_skype",
					"std" => "http://www.skype.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("MySpace","builder"),
					"id" => "footer_social_myspace",
					"std" => "http://myspace.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("ICQ","builder"),
					"id" => "footer_social_icq",
					"std" => "http://www.icq.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Envato","builder"),
					"id" => "footer_social_envato",
					"std" => "http://envato.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Bing","builder"),
					"id" => "footer_social_bing",
					"std" => "http://www.bing.com/",
					"type" => "text");


$of_options[] = array( "name" => "",
					"desc" => __("Forrst","builder"),
					"id" => "footer_social_forrst",
					"std" => "http://forrst.com/",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("DeviantArt","builder"),
					"id" => "footer_social_da",
					"std" => "http://deviantart.com/",
					"type" => "text");


$of_options[] = array( "name" =>  __("FOOTER SETTINGS","builder"),
					"desc" => __("Pick a color for the footer background","builder"),
					"id" => "footer_bg_color",
					"std" => "#303030",
					"type" => "color");


$of_options[] = array( "name" => " ",
					"desc" => __("Select a footer background pattern ( Choose first image to show only background color )","builder"),
					"id" => "footer_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);

$of_options[] = array( "name" => " ",
					"desc" => __("Footer top margin","builder"),
					"id" => "footer_margin_top",
					"std" => "0",
					"type" => "text");
					
$of_options[] = array( "name" => "",
					"desc" => __("Footer top padding","builder"),
					"id" => "footer_padding_top",
					"std" => "40",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Footer bottom padding","builder"),
					"id" => "footer_padding_bottom",
					"std" => "10",
					"type" => "text");

$of_options[] = array( "name" => "",
					"desc" => __("Footer top bodrer value","builder"),
					"id" => "footer_border_value",
					"std" => "1",
					"type" => "text");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the footer top border","builder"),
					"id" => "footer_border_color",
					"std" => "#444444",
					"type" => "color");

$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the footer headers","builder"),
					"id" => "footer_text_header_color",
					"std" => "#ffffff",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the lines after headers","builder"),
					"id" => "footer_hr_color",
					"std" => "#444444",
					"type" => "color");

$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the footer text","builder"),
					"id" => "footer_text_color",
					"std" => "#a8a8a8",
					"type" => "color");	
									
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the footer strong text elements","builder"),
					"id" => "footer_text_strong_color",
					"std" => "#ffffff",
					"type" => "color");						
					

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the footer small text elements","builder"),
					"id" => "footer_text_small_color",
					"std" => "#666666",
					"type" => "color");				
					

$of_options[] = array( "name" =>  " ",
					"desc" => __("Pick a color for the footer links","builder"),
					"id" => "footer_text_a_color",
					"std" => "#a8a8a8",
					"type" => "color");

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the footer hovered links","builder"),
					"id" => "footer_text_a_hover_color",
					"std" => "#ffffff",
					"type" => "color");						




// Bottom Line

$of_options[] = array( "name" => __("Bottom Line","builder"),
					"type" => "heading");

$of_options[] = array( "name" => __("Show/Hide Bottom Line","builder"),
					"desc" => __("Show Bottom Line?","builder"),
					"id" => "bottom_line_show",
					"std" => 1,
          			"folds" => 1,
					"type" => "checkbox");


$of_options[] = array( "name" =>  __("BOTTOM LINE BACKGROND","builder"),
					"desc" => __("Pick a color for the 'Bottom Line' area background","builder"),
					"id" => "theme_colors_bottom_line",
					"std" => "#3a3a3a",
					"type" => "color");

$of_options[] = array( "name" => "",
					"desc" => __("Pick a image for the 'Bottom Line' area background ( Choose first image to show only background color )","builder"),
					"id" => "theme_colors_bottom_line_bg_image",
					"std" => $bg_images_url."",
					"type" => "tiles",
					"options" => $bg_images,
					);


$of_options[] = array( "name" => __("BOTTOM LINE TEXT","builder"),
					"desc" =>  __("Past your text or HTML","builder"),
					"id" => "bottom_line_text",
					"std" => "Copyright 2012 Builder - Company. Design by <a href='http://themeforest.net/user/OrangeIdea?ref=OrangeIdea'>OrangeIdea</a>",
					"type" => "text");					

$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Bottom Line text","builder"),
					"id" => "theme_colors_bottom_line_text",
					"std" => "#FFFFFF",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Bottom Line links","builder"),
					"id" => "theme_colors_bottom_line_a",
					"std" => "#FFFFFF",
					"type" => "color");
					
$of_options[] = array( "name" =>  "",
					"desc" => __("Pick a color for the Top Line mouse over links","builder"),
					"id" => "theme_colors_bottom_line_a_hover",
					"std" => "#FFFFFF",
					"type" => "color");





					
// Backup Options
$of_options[] = array( "name" => __("Backup Options","builder"),
					"type" => "heading");
					
$of_options[] = array( "name" => __("Backup and Restore Options","builder"),
                    "id" => "of_backup",
                    "std" => "",
                    "type" => "backup",
					"desc" => __("You can use the two buttons below to backup your current options, and then restore it back at a later time. This is useful if you want to experiment on the options but would like to keep the old settings in case you need it back.","builder"),
					);
					
$of_options[] = array( "name" => __("Transfer Theme Options Data","builder"),
                    "id" => "theme_update",
                    "std" => "",
                    "type" => "transfer",
					"desc" => __("You can tranfer the saved options data between different installs by copying the text inside the text box. To import data from another install, replace the data in the text box with the one from another install and click 'Import Options'","builder"));



	}
}
?>
