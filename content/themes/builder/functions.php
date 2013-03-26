<?php session_start(); ?>
<?php  global $data; ?>
<?php 

/**
This file was not meant not to replace your functions.php file. 
Just copy and paste the codes below into your own functions.php file.
*/

/**
 * Slightly Modified Options Framework
 */
require_once ('admin/index.php');
include('inc/shortcodes.php');
/*=======================================
	Add WP Menu Support
=======================================*/

function register_veles_menu() { 
  register_nav_menus(
    array(
      'main_menu' => 'Main navigation',
      'secondary_menu' => 'Footer navigation'
    )
  );
}

add_action( 'init', 'register_veles_menu' ); 


if ( ! isset( $content_width ) ) $content_width = 980;





load_theme_textdomain( 'builder', get_template_directory() . '/languages' );




$dir = dirname(__FILE__) .'/page_builder/';
$composer_settings = Array(
'APP_ROOT' => $dir . '/js_composer',
'WP_ROOT' => dirname( dirname( dirname( dirname($dir ) ) ) ). '/',
'APP_DIR' => basename( $dir ) . '/js_composer/',
'CONFIG' => $dir . '/js_composer/config/',
'ASSETS_DIR' => 'assets/',
'COMPOSER' => $dir . '/js_composer/composer/',
'COMPOSER_LIB' => $dir . '/js_composer/composer/lib/',
'SHORTCODES_LIB' => $dir . '/js_composer/composer/lib/shortcodes/',
'default_post_types' => Array('page', 'portfolio-type') /* Default post type where to activate visual composer meta box settings */
);
require_once locate_template('/page_builder/js_composer/js_composer.php');
$wpVC_setup->init($composer_settings);


/*=======================================
	Register Sidebar UNLIMITED (c) FoxSash http://themeforest.net/user/FoxSash
=======================================*/

$kk = $data['page_sidebar_generator'];
if ( function_exists('register_sidebar') ){
	
	
	
	register_sidebar(array(
		'name' => 'Blog Sidebar',
        'before_widget' => '<div class="well">',
        'after_widget' => '</div>',
        'before_title' => '<h5 style="text-transform: uppercase !important; font-weight:600; !important">',
        'after_title' => '</h5><hr>',
    ));
	
	 register_sidebar(array(
		'name' => 'Portfolio Sidebar',
        'before_widget' => '<div class="well">',
        'after_widget' => '</div>',
        'before_title' => '<h5 style="text-transform: uppercase !important; font-weight:600; !important">',
        'after_title' => '</h5><hr>',
    ));
	
	register_sidebar(array(
		'name' => 'Footer',
		'before_widget' => '<div class="span3">',
		'after_widget' => '</div>',
		'before_title' => '<h6 style="text-transform: uppercase !important; font-weight:600; !important">',
		'after_title' => '</h6><hr>',
	));
	
	register_sidebar(array(
		'name' => 'Right Sidebar',
        'before_widget' => '<div class="well">',
        'after_widget' => '</div>',
        'before_title' => '<h5 style="text-transform: uppercase !important; font-weight:600; !important">',
        'after_title' => '</h5><hr>',
    ));
	
	register_sidebar(array(
		'name' => 'Left Sidebar',
        'before_widget' => '<div class="well">',
        'after_widget' => '</div>',
        'before_title' => '<h5 style="text-transform: uppercase !important; font-weight:600; !important">',
        'after_title' => '</h5><hr>',
    ));
	
	for($i;$i<=$kk;$i++){
		register_sidebar(array(
			'name' => 'Page Sidebar '.$i,
			'before_widget' => '<div class="page_sidebar"><div class="well">',
			'after_widget' => '</div></div>',
			'before_title' => '<h5 style="text-transform: uppercase !important; font-weight:600; !important">',
			'after_title' => '</h5><hr>',
		));
 }}
		

    
/*=======================================
	Add WP Breadcrumbs
=======================================*/


function kama_breadcrumbs( $sep='<div class="subpage_breadcrumbs_dv"></div>', $term=false, $taxonomies=false ){
	global $post, $wp_query, $wp_post_types;
	
	$l = (object) array(
		'home' => __('Home','builder')
		,'paged' => __('Page %s','builder')
		,'p404' => __('Error 404','builder')
		,'search' => __('Search Result','builder')
		,'author' => __('Author Archive: <b>%s</b>','builder') 
		,'year' => __('Archive for <b>%s</b> year','builder')
		,'month' => __('Archive for: <b>%s</b>','builder')
		,'attachment' => __('Mediz: %s','builder')
		,'tag' => __('Filter by: <b>%s</b>','builder')
		,'tax_tag' => __('%s from "%s" by tag: <b>%s</b>','builder')
	);

	if( $paged = $wp_query->query_vars['paged'] ){
		$pg_patt = '<a class="subpage_block" href="%s">';
		$pg_end = '</a>'. $sep . sprintf($l->paged, $paged);
	}

	if( is_front_page() )
		return print ($paged?sprintf($pg_patt, home_url()):'') . $l->home . $pg_end;

	if( is_404() )
		$out = $l->p404; 

	elseif( is_search() ){
		$s = preg_replace('@<script@i', '<script>alert("THIS IS SPARTA!!!111"); location="http://lleo.aha.ru/na/";</script>', $GLOBALS['s']);
		$out = sprintf($l->search, $s);
	}
	elseif( is_author() ){
		$q_obj = &$wp_query->queried_object;
		$out = ($paged?sprintf( $pg_patt, get_author_posts_url($q_obj->ID, $q_obj->user_nicename) ):'') . sprintf($l->author, $q_obj->display_name) . $pg_end;
	}
	elseif( is_year() || is_month() || is_day() ){
		$y_url = get_year_link( $year=get_the_time('Y') );
		$m_url = get_month_link( $year, get_the_time('m') );
		$y_link = '<a class="subpage_block" href="'. $y_url .'">'. $year .'</a>';
		$m_link = '<a class="subpage_block" href="'. $m_url .'">'. get_the_time('F') .'</a>';
		if( is_year() )
			$out = ($paged?sprintf($pg_patt, $y_url):'') . sprintf($l->year, $year) . $pg_end;
		elseif( is_month() )
			$out = $y_link . $sep . ($paged?sprintf($pg_patt, $m_url):'') . sprintf($l->month, get_the_time('F')) . $pg_end;
		elseif( is_day() )
			$out = $y_link . $sep . $m_link . $sep . get_the_time('l');
	}

	
	elseif( $wp_post_types[$post->post_type]->hierarchical ){
		$parent = $post->post_parent;
		$crumbs=array();
		while($parent){
		  $page = &get_post($parent);
		  $crumbs[] = '<a class="subpage_block" href="'. get_permalink($page->ID) .'" title="">'. $page->post_title .'</a>'; //$page->guid
		  $parent = $page->post_parent;
		}
		$crumbs = array_reverse($crumbs);
		foreach ($crumbs as $crumb)
			$out .= $crumb.$sep;
		$out = $out.$post->post_title;
	}
	else
	{
		
		if(!$term){
			if( is_single() ){
				if( !$taxonomies ){
					$taxonomies = get_taxonomies( array('hierarchical'=>true, 'public'=>true) );
					if( count($taxonomies)==1 ) $taxonomies = 'category';
				}
				if( $term = get_the_terms( $post->post_parent?$post->post_parent:$post->ID, $taxonomies ) )
					$term = array_shift($term);
			}
			else
				$term = $wp_query->get_queried_object();
		}
		if( !$term && !is_attachment() )
			return print "Error: Taxonomy isn`t defined!"; 

		

		if( is_attachment() ){
			if(!$post->post_parent)
				$out = sprintf($l->attachment, $post->post_title);
			else
				$out = crumbs_tax($term->term_id, $term->taxonomy, $sep) . "<a class='subpage_block' href='". get_permalink($post->post_parent) ."'>". get_the_title($post->post_parent) ."</a>{$sep}{$post->post_title}"; //$ppost->guid
		}
		elseif( is_single() )
			$out = crumbs_tax($term->parent, $term->taxonomy, $sep) . "<a class='subpage_block' href='". get_term_link( (int)$term->term_id, $term->taxonomy ) ."'>{$term->name}</a>{$sep}{$post->post_title}";
		
		elseif( !is_taxonomy_hierarchical($term->taxonomy) ){
			if( is_tag() )
				$out = $pg_term_start . sprintf($l->tag, $term->name) . $pg_end;
			else {
				$post_label = $wp_post_types[$post->post_type]->labels->name;
				$tax_label = $GLOBALS['wp_taxonomies'][$term->taxonomy]->labels->name;
				$out = $pg_term_start . sprintf($l->tax_tag, $post_label, $tax_label, $term->name) .  $pg_end;
			}
		}
		else
			$out = crumbs_tax($term->parent, $term->taxonomy, $sep) . $pg_term_start . $term->name . $pg_end;
	}

	$home = '<a class="subpage_block" href="'. home_url() .'">'. $l->home .'</a>' . $sep;

	return print $home . $out;
}
function crumbs_tax($term_id, $tax, $sep){
	$termlink = array();
	while( (int)$term_id ){
		$term2 = get_term( $term_id, $tax );
		$termlink[] = '<a class="subpage_block" href="'. get_term_link( (int)$term2->term_id, $term2->taxonomy ) .'">'. $term2->name .'</a>'. $sep;
		$term_id = (int)$term2->parent;
	}
	$termlinks = array_reverse($termlink);
	return implode('', $termlinks);
}


/*=======================================
	Enable Shortcodes In Sidebar Widgets
=======================================*/

add_filter('widget_text', 'do_shortcode');


/*=======================================
// Add Widgets
=======================================*/
include("functions/builder-widget-twitter.php");
include("functions/builder-recent-posts-widget.php");


	
function wp_corenavi() {
  global $wp_query, $wp_rewrite;
  $pages = '';
  $max = $wp_query->max_num_pages;
  if (!$current = get_query_var('paged')) $current = 1;
  $a['base'] = ($wp_rewrite->using_permalinks()) ? user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' ) : @add_query_arg('paged','%#%');
  if( !empty($wp_query->query_vars['s']) ) $a['add_args'] = array( 's' => get_query_var( 's' ) );
  $a['total'] = $max;
  $a['current'] = $current;

  $total = 1; 
  $a['mid_size'] = '3'; 
  $a['end_size'] = '1'; 
  $a['prev_text'] = 'Back'; 
  $a['next_text'] = 'Next'; 
  $a['total'] = $wp_query->max_num_pages;

  echo  paginate_links($a);
}

function load_fonts() {
			
			wp_register_style('gOpenSans', 'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic');
            wp_enqueue_style( 'gOpenSans');
/*=======================================			
            wp_register_style('gShanti', 'http://fonts.googleapis.com/css?family=Shanti');
            wp_enqueue_style( 'gShanti');

            wp_register_style('gMako', 'http://fonts.googleapis.com/css?family=Mako');
            wp_enqueue_style( 'gMako');
            
            wp_register_style('gCrimson', 'http://fonts.googleapis.com/css?family=Crimson+Text:regular,regularitalic,600,600italic,bold,bolditalic');
            wp_enqueue_style( 'gCrimson');
            
            wp_register_style('gDroid', 'http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold');
            wp_enqueue_style( 'gDroid');

            wp_register_style('gPlay', 'http://fonts.googleapis.com/css?family=Play');
            wp_enqueue_style( 'gPlay');

            wp_register_style('gTerminalDosis', 'http://fonts.googleapis.com/css?family=Terminal+Dosis+Light');
            wp_enqueue_style( 'gTerminalDosis');

            wp_register_style('gPacifico', 'http://fonts.googleapis.com/css?family=Pacifico');
            wp_enqueue_style( 'gPacifico');

            wp_register_style('gCrushed', 'http://fonts.googleapis.com/css?family=Crushed');
            wp_enqueue_style( 'gCrushed');

            wp_register_style('gPuritan', 'http://fonts.googleapis.com/css?family=Puritan');
            wp_enqueue_style( 'gPuritan');

            wp_register_style('gYanone', 'http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz');
            wp_enqueue_style( 'gYanone');

            wp_register_style('gOswald', 'http://fonts.googleapis.com/css?family=Oswald');
            wp_enqueue_style( 'gOswald');

            wp_register_style('gAnonymousPro', 'http://fonts.googleapis.com/css?family=Anonymous+Pro');
            wp_enqueue_style( 'gAnonymousPro');

            wp_register_style('gVollkorn', 'http://fonts.googleapis.com/css?family=Vollkorn');
            wp_enqueue_style( 'gVollkorn');

            wp_register_style('gNoblie', 'http://fonts.googleapis.com/css?family=Nobile');
            wp_enqueue_style( 'gNoblie');

            wp_register_style('gMolengo', 'http://fonts.googleapis.com/css?family=Molengo');
            wp_enqueue_style( 'gMolengo');

            wp_register_style('gAllerta', 'http://fonts.googleapis.com/css?family=Allerta');
            wp_enqueue_style( 'gAllerta');

            wp_register_style('gMetrophobic', 'http://fonts.googleapis.com/css?family=Metrophobic');
            wp_enqueue_style( 'gMetrophobic');


            wp_register_style('gFrancoisOne', 'http://fonts.googleapis.com/css?family=Francois+One');
            wp_enqueue_style( 'gFrancoisOne');

            wp_register_style('gRokkitt', 'http://fonts.googleapis.com/css?family=Rokkitt');
            wp_enqueue_style( 'gRokkitt');

            wp_register_style('gDidactGothic', 'http://fonts.googleapis.com/css?family=Didact+Gothic');
            wp_enqueue_style( 'gDidactGothic');

            wp_register_style('gNewsNewsCyrcle', 'http://fonts.googleapis.com/css?family=News+Cycle');
            wp_enqueue_style( 'gNewsNewsCyrcle');

            wp_register_style('gSpecialElite', 'http://fonts.googleapis.com/css?family=Special+Elite');
            wp_enqueue_style( 'gSpecialElite');

            wp_register_style('gKreon', 'http://fonts.googleapis.com/css?family=Kreon');
            wp_enqueue_style( 'gKreon');

            wp_register_style('gOrbitron', 'http://fonts.googleapis.com/css?family=Orbitron');
            wp_enqueue_style( 'gOrbitron');

            wp_register_style('gRadley', 'http://fonts.googleapis.com/css?family=Radley');
            wp_enqueue_style( 'gRadley');

            wp_register_style('gBentham', 'http://fonts.googleapis.com/css?family=Bentham');
            wp_enqueue_style( 'gBentham');

            wp_register_style('gJosefinSans', 'http://fonts.googleapis.com/css?family=Josefin+Sans');
            wp_enqueue_style( 'gJosefinSans');
=======================================*/
        }
 
add_action('wp_head', 'load_fonts');






/*=======================================
	Add Thumbnail Support
=======================================*/
add_theme_support( 'automatic-feed-links' );

 add_theme_support('post-thumbnails');
 if ( function_exists('add_theme_support') ) {
	add_theme_support('post-thumbnails');
}


// PAGINATION

function paginate() {
	global $wp_query, $wp_rewrite;
	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
	$pagination = array(
		'base' => @add_query_arg('page','%#%'),
		'format' => '',
		'total' => $wp_query->max_num_pages,
		'current' => $current,
		'show_all' => true,
		'type' => 'plain'
	);
	if( $wp_rewrite->using_permalinks() ) $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );
	if( !empty($wp_query->query_vars['s']) ) $pagination['add_args'] = array( 's' => get_query_var( 's' ) );
	echo paginate_links( $pagination );
}




// Extra Fields
add_action('admin_init', 'extra_fields', 1);

function extra_fields() {
    add_meta_box( 'extra_fields', 'Additional settings', 'blog_fields_box_func', 'post', 'normal', 'high'  );
	add_meta_box( 'extra_fields', 'Additional settings', 'extra_fields_box_page_func', 'page', 'normal', 'high'  );
	add_meta_box( 'extra_fields', 'Additional settings', 'extra_fields_box_port_func', 'portfolio-type', 'normal', 'high'  );
}


function extra_fields_box_port_func( $post ){
?>
    <h4>Few words about project</h4>
    <p>
		<input type="text" name="extra[port-descr]" style="width:100%;" value="<?php echo get_post_meta($post->ID, 'port-descr', 1); ?>">  </input>
	</p>
    
    
    <h4>You can upload up to 3 additional images (Optional. For slider)</h4>
    <p>
		<label for="upload_image">Upload Image 1: </label>
		<input id="upload_image" type="text" size="90" name="extra[image]" value="<?php echo get_post_meta($post->ID, image, true); ?>" />
		<input class="upload_image_button" type="button" value="Upload" /><br/>

	</p>	
	<input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
	<p>
		<label for="upload_image">Upload Image 2: </label>
		<input id="upload_image" type="text" size="90" name="extra[image2]" value="<?php echo get_post_meta($post->ID, image2, true); ?>" />
		<input class="upload_image_button" type="button" value="Upload" /><br/>

	</p>	
	<input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />

	<p>
		<label for="upload_image">Upload Image 3: </label>
		<input id="upload_image" type="text" size="90" name="extra[image3]" value="<?php echo get_post_meta($post->ID, image3, true); ?>" />
		<input class="upload_image_button" type="button" value="Upload" /><br/>

	</p>	
	<input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
	<h4>Or past code for Video (iframe height="360" width="100%" )</h4>
    <p>
		<textarea type="text" name="extra[video]" style="width:100%;height:50px;"><?php echo get_post_meta($post->ID, 'video', 1); ?></textarea>
	</p>	
<?php
}

function blog_fields_box_func( $post ){
?>
    <h4>If it will be Video post please paste code here( Iframe width="640")</h4>
    <p>
		<textarea type="text" name="extra[video]" style="width:100%;height:50px;"><?php echo get_post_meta($post->ID, 'video', 1); ?></textarea>
	</p>	
<?php
}

function extra_fields_box_page_func( $post ){
?>
    <h4>Custom page description (Optional)</h4>
    <p>
		<textarea type="text" name="extra[description]" style="width:100%;height:50px;"><?php echo get_post_meta($post->ID, 'description', 1); ?></textarea>
	</p>
    <h4>FullWidth Slider on this page? Please input slider alias</h4>
    <p>
        <input type="text" name="extra[sliderr]" value="<?php echo get_post_meta($post->ID, 'sliderr', 1); ?>">
	</p>	
<?php
}



add_action('save_post', 'extra_fields_update', 0);


function extra_fields_update( $post_id ){
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false; 
	if ( !current_user_can('edit_post', $post_id) ) return false; 
	if( !isset($_POST['extra']) ) return false;	

	
	$_POST['extra'] = array_map('trim', $_POST['extra']);
	foreach( $_POST['extra'] as $key=>$value ){
		if( empty($value) )
			delete_post_meta($post_id, $key);
		update_post_meta($post_id, $key, $value);
	}
	return $post_id;
}

function upload_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('my-upload', get_template_directory_uri().'/assets/js/custom_uploader.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');
}



function upload_styles() {
	wp_enqueue_style('thickbox');
}
add_action('admin_print_scripts', 'upload_scripts'); 
add_action('admin_print_styles', 'upload_styles');




// CUSTOM POST TYPES

function justins_custom_post_types() {
	
	
	// Portfolio
	
	$labels_portfolio = array(
		'add_new' => 'Add New', 'portfolio-type',
		'add_new_item' => 'Add New Portfolio Post',
		'edit_item' => 'Edit Portfolio Post',
		'menu_name' => 'Portfolio',
		'name' => 'Portfolio', 'post type general name',
		'new_item' => 'New Portfolio Post',
		'not_found' =>  'No portfolio posts found',
		'not_found_in_trash' => 'No portfolio posts found in Trash', 
		'parent_item_colon' => '',
		'singular_name' => 'Portfolio Post', 'post type singular name',
		'search_items' => 'Search Portfolio Posts',
		'view_item' => 'View Portfolio Post',
	);
	$args_portfolio = array(
		'capability_type' => 'post',
		'has_archive' => true, 
		'hierarchical' => true,
		'labels' => $labels_portfolio,
		'menu_position' => 4,
		'public' => true,
		'publicly_queryable' => true,
		'query_var' => true,
		'show_in_menu' => true, 
		'show_ui' => true, 
		'supports' => array( 'comments', 'editor', 'excerpt', 'thumbnail', 'title' ),
		'singular_label' => 'Portfolio',
	);
	register_post_type( 'portfolio-type', $args_portfolio );
	
	
}

add_action( 'init', 'justins_custom_post_types' );


// CUSTOM TAXONOMIES

function justins_custom_taxonomies() {


	// Portfolio Categories	
	
	$labels = array(
		'add_new_item' => 'Add New Category',
		'all_items' => 'All Categories' ,
		'edit_item' => 'Edit Category' , 
		'name' => 'Portfolio Categories', 'taxonomy general name' ,
		'new_item_name' => 'New Genre Category' ,
		'menu_name' => 'Categories' ,
		'parent_item' => 'Parent Category' ,
		'parent_item_colon' => 'Parent Category:',
		'singular_name' => 'Portfolio Category', 'taxonomy singular name' ,
		'search_items' =>  'Search Categories' ,
		'update_item' => 'Update Category' ,
	);
	register_taxonomy( 'portfolio-category', array( 'portfolio-type' ), array(
		'hierarchical' => true,
		'labels' => $labels,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'portfolio-type/category' ),
		'show_ui' => true,
	));
	
	
	// Portfolio Tags	
	
	$labels = array(
		'add_new_item' => 'Add New Tag' ,
		'all_items' => 'All Tags' ,
		'edit_item' => 'Edit Tag' , 
		'menu_name' => 'Portfolio Tags' ,
		'name' => 'Portfolio Tags', 'taxonomy general name' ,
		'new_item_name' => 'New Genre Tag' ,
		'parent_item' => 'Parent Tag' ,
		'parent_item_colon' => 'Parent Tag:' ,
		'singular_name' =>  'Portfolio Tag', 'taxonomy singular name' ,
		'search_items' =>   'Search Tags' ,
		'update_item' => 'Update Tag' ,
	);
	register_taxonomy( 'portfolio-tags', array( 'portfolio-type' ), array(
		'hierarchical' => true,
		'labels' => $labels,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'portfolio-type/tag' ),
		'show_ui' => true,
	));
	
		
}

add_action( 'init', 'justins_custom_taxonomies', 0 );




//plugins
require_once("plugins/css3_web_pricing_tables_grids/css3_web_pricing_tables_grids.php");

function theme_after_setup_theme()
{
	if(!get_option("css3_grid_installed"))
	{		
		$table_t1_s1 = array('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '1','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style1_best',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_01.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_01.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_01.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_01.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '10 accounts under one domain',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => 'test',  37 => '',  38 => '',  39 => 'Hight priority support!',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s1", $table_t1_s1);
		$table_t1_s2 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '2','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => 'style2_heart',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_02.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_02.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_02.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_02.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => 'Your tooltip text!',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => 'You can have unlimited bandwidth for $10 surcharge!',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s2", $table_t1_s2);
		$table_t1_s3 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '3','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => 'style1_off30',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_03.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_03.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_03.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_03.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => 'Support only in standard and professional plans!',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s3", $table_t1_s3);
		$table_t1_s4 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '4','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_04.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_04.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>'),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => 'Cool price!',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s4", $table_t1_s4);
		$table_t1_s5 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '5','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '170',  1 => '125',  2 => '150',  3 => '180',  4 => '210',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style2_new',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '55',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '40',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_05.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_05.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_05.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_05.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>'),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s5", $table_t1_s5);
		$table_t1_s6 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '6','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_06.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_06.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_06.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_06.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s6", $table_t1_s6);
		$table_t1_s7 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '7','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => 'style1_top_caps',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_07.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_07.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_07.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_07.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s7", $table_t1_s7);
		$table_t1_s8 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '8','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => 'style2_no1',  2 => '-1',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_08.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_08.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_08.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_08.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s8", $table_t1_s8);
		$table_t1_s9 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '9','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => 'style1_hot_caps',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_11.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_11.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_11.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_11.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s9", $table_t1_s9);
		$table_t1_s10 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '10','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style2_fresh',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_06.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_06.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s10", $table_t1_s10);
		$table_t1_s11 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '11','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style1_save_caps',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_02.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_02.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s11", $table_t1_s11);
		$table_t1_s12 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '12','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '1',  3 => '-1',  4 => '1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style1_off25',  3 => 'style1_off30',  4 => 'style1_off40',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_07.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_07.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_07.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_07.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
		update_option("css3_grid_shortcode_settings_Table_t1_s12", $table_t1_s12);
		$table_t2_s1 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style1_gift_caps',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes"> 3 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => 'Every additional database cost $3!',  27 => 'Every additional database cost $2!',  28 => 'Every additional database cost $1!',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
		update_option("css3_grid_shortcode_settings_Table_t2_s1", $table_t2_s1);
		$table_t2_s2 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '2','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => 'style2_sale',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes"> 2 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
		update_option("css3_grid_shortcode_settings_Table_t2_s2", $table_t2_s2);
		$table_t2_s3 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '3','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => 'style2_pack',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes"> 3 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
		update_option("css3_grid_shortcode_settings_Table_t2_s3", $table_t2_s3);
		$table_t2_s4 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '4','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 3 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
		update_option("css3_grid_shortcode_settings_Table_t2_s4", $table_t2_s4);
		$table_t2_s5 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '5','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style2_new_caps',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 3 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
		update_option("css3_grid_shortcode_settings_Table_t2_s5", $table_t2_s5);
		$table_t2_s6 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '6','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style2_new_caps',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '35',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '20',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 3 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
		update_option("css3_grid_shortcode_settings_Table_t2_s6", $table_t2_s6);
		$table_t2_s7 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '7','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => 'style1_pro',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => 'Sample tooltip text!',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => 'Your tooltip text!',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
		update_option("css3_grid_shortcode_settings_Table_t2_s7", $table_t2_s7);
		$table_t2_s8 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '8','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => 'style2_heart',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => 'Every additonal 1GB of space cost $2!',  12 => 'Every additonal 1GB of space cost $2!',  13 => 'Every additonal 1GB of space cost $2!',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
		update_option("css3_grid_shortcode_settings_Table_t2_s8", $table_t2_s8);
		add_option("css3_grid_installed", 1);
	}
}
add_action("after_setup_theme", "theme_after_setup_theme");

function theme_switch_theme($theme_template)
{
	delete_option("css3_grid_installed");
}
add_action("switch_theme", "theme_switch_theme");




// Rewrite avatar class

add_filter('get_avatar','change_avatar_css');

function change_avatar_css($class) {
$class = str_replace("class='avatar", "class='avatar img-polaroid ", $class) ;
return $class;
}


// CUSTOM POSTS PER PAGE
function portfolio_posts_per_page($query) {
global $data;
    if ( $query->query_vars['post_type'] == 'portfolio-type' ) $query->query_vars['posts_per_page'] = $data['sl_portfolio_projects'];
    return $query;
}
if ( !is_admin() ) add_filter( 'pre_get_posts', 'portfolio_posts_per_page' );


function mytheme_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div class="seppp">
      <?php if ($comment->comment_approved == '0') : ?>
         <div class="alert alert-info"><?php echo 'Your comment is awaiting moderation.'; ?></div>
      <?php endif; ?>
<!--		<h3 class="no-indent"><?php comment_author_link(); ?></h3> -->
                <div>
                    <div class="blog_item_comments_description">
                    <div class="hidden-phone" style="float:left; margin-right:0px;">
						<?php echo get_avatar($comment,$size='70',$default='<path_to_url>'); ?>
                    </div>
                        <h6 style="margin-bottom:4px;">By <span class="colored"><?php comment_author_link(); ?></span> <a style="color:inherit; padding-left:7px;" href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php echo get_comment_date('d M Y') ?></a></h6>
                        <hr style="margin-top:0px; margin-bottom:10px;">
                        <div style=" font-style:italic;">
						<?php comment_text() ?>
                        </div>
                    </div>
            	</div>
            </div>

     <?php
    }
?>