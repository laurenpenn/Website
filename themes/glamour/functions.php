<?php
	
	add_action( 'admin_init', 'theme_options_init' );
	add_action( 'admin_menu', 'theme_options_add_page' );
	add_action('admin_print_styles', 'my_admin_styles');
	add_action('admin_print_scripts', 'my_admin_scripts');
	
	include (TEMPLATEPATH . '/options/glamour_metabox.php');

	$sidebars = array('Footer Column 1', 'Footer Column 2', 'Footer Column 3', 'Footer Column 4', 'Footer Column 5 (Contact)');	
	foreach($sidebars as $sidebar) {
		register_sidebar(array('name'=> $sidebar, 'description' => 'Custom Widgets', 'before_widget' => '', 'after_widget' => '', 'before_title' => '<div class="widgets_title">', 'after_title' => '</div>'));
	}
	
	$right_sidebars = array('Right Widget 1 (All of Pages)', 'Right Widget 2 (All of Pages)', 'Right Widget 3 (All of Pages)', 'Right Widget 4 (All of Pages)', 'Right Widget 5 (All of Pages)', 'Blog Widget 1');	
	foreach($right_sidebars as $rsidebar) {
		register_sidebar(array('name'=> $rsidebar, 'description' => 'Custom Widgets', 'before_widget' => '<div class="textwidget"><div class="page_navigation_advert">', 'after_widget' => '</div></div>', 'before_title' => '<div class="page_navigation_title"><h3>', 'after_title' => '</h3></div>'));
	}
	
	/**
	 * Init plugin options to white list our options
	 */
	function theme_options_init(){
		register_setting( 'sample_options', 'sample_theme_options', 'theme_options_validate' );
	}
	
	/**
	 * Load up the menu page
	 */
	function theme_options_add_page() {
		//add_theme_page( __( 'The Glamour Settings' ), __( 'The Glamour Settings' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
		add_theme_page( __( 'The Glamour Settings' ), __( 'Glamour Settings' ), 'edit_theme_options', 'tglamour', 'glamour_settings');
		add_theme_page( __( 'The Glamour Slider' ),  __( 'Glamour Slider' ), 'edit_theme_options', 'glamour_slider', 'glamour_slider');
		add_theme_page( __( 'The Glamour Home' ),  __( 'Glamour Home' ), 'edit_theme_options', 'glamour_home', 'glamour_home');
		add_theme_page( __( 'The Glamour Footer' ),  __( 'Glamour Footer' ), 'edit_theme_options', 'glamour_footer', 'glamour_footer');
		add_theme_page( __( 'The Glamour Contact' ),  __( 'Glamour Contact' ), 'edit_theme_options', 'glamour_contact', 'glamour_contact');
		add_theme_page( __( 'The Glamour Moreover' ),  __( 'Glamour Moreover' ), 'edit_theme_options', 'glamour_moreover', 'glamour_moreover');
	}

	function list_categories($name, $value)	{
		echo "<select name=$name>";
		echo "<option value=\"\">Select Category</option>";
	
		$categories = get_categories("title_li=&orderby=name");
		foreach ($categories as $category)
		{
			$selected = ($value == $category->term_id) ? "selected=\"selected\"" : "";
			echo "<option $selected value='". $category->term_id ."'>". $category->name ."</option>";
		}
		echo "</select>";
	}

	function list_pages($name, $value)	{
		echo "<select name=$name>";
		echo "<option value=\"\">Select Category</option>";
	
		$pages = get_pages(); 

		foreach ($pages as $page)
		{
			$selected = ($value == $page->ID) ? "selected=\"selected\"" : "";
			echo "<option $selected value='". $page->ID ."'>". $page->post_title ."</option>";
		}
		echo "</select>";
	}
	
	function my_admin_scripts(){
		wp_enqueue_script('media-upload');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('dashboard');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('mini', get_bloginfo('template_url').'/js/mini.js');
	}
	
	function my_admin_styles(){
		wp_enqueue_style('thickbox');
		wp_enqueue_style('dashboard');
		wp_enqueue_style('global');
		wp_enqueue_style('wp-admin');
	}
	
	function glamour_slider(){
		include_once( TEMPLATEPATH."/options/glamour_slider.php");
	}
	
	function glamour_home(){
		include_once( TEMPLATEPATH."/options/glamour_home.php");
	}

	function glamour_footer(){
		include_once( TEMPLATEPATH."/options/glamour_footer.php");
	}
	
	function glamour_moreover(){
		include_once( TEMPLATEPATH."/options/glamour_moreover.php");
	}
	
	function glamour_contact(){
		include_once( TEMPLATEPATH."/options/glamour_contact.php");
	}
	
	function glamour_settings(){
		include_once( TEMPLATEPATH."/options/glamour_settings.php");
	}
	
	function custom_comments($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
        $GLOBALS['comment_depth'] = $depth;
	?>
        <li>
			<div class="portfolio_box">
				<div class="portfolio_image">
					<div class="inside_border">
						<div class="portfolio_box_anime">
							<?php $avatar_email = get_comment_author_email(); echo get_avatar( $avatar_email, 80 ); ?>
						</div>
					</div>
				</div>
			
				<div class="comment_box">
					<div class="comment_border">
						<div class="comment_container">
							<strong style="font-size:15px;"><?php echo get_comment_author_link(); ?></strong>
							<span style="font-size:10px;">(<?php echo get_comment_date(); ?>)</span><br/>
							<?php comment_text() ?>
							<?php if ($comment->comment_approved == '0') _e("\t\t\t\t\t<span class='unapproved' style='font-size:11px; color:#adadad;'><i>".get_option('cf_awaiting', 'Your comment is awaiting moderation.')."</i></span>\n", 'your-theme') ?>
						</div>
					</div>
				</div>
			</div>
<?php } ?>