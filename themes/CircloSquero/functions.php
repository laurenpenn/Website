<?php
// include plugins
if (is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {

$theme_url = get_bloginfo('template_url');
$theme_url_split = explode("/", $theme_url);
$theme_url_split_length = count($theme_url_split);
$theme_name = $theme_url_split[$theme_url_split_length-1];

$plugin_source = "../wp-content/themes/CircloSquero/plugins/wp-nivo-slider";
$plugin_target = "../wp-content/plugins/wp-nivo-slider";

$plugin = 'wp-nivo-slider/wp-nivo-slider.php';

function full_copy( $source, $target, $plugin_dir ) {
    if ( is_dir( $source ) ) {
        @mkdir( $target );
        $d = dir( $source );
        while ( FALSE !== ( $entry = $d->read() ) ) {
            if ( $entry == '.' || $entry == '..' ) {
                continue;
            }
            $Entry = $source . '/' . $entry; 
            if ( is_dir( $Entry ) ) {
                full_copy( $Entry, $target . '/' . $entry, $plugin );
                continue;
            }
            copy( $Entry, $target . '/' . $entry );
        }

        $d->close();
        
        $active_plugins = get_option('active_plugins');
        if (!isset($active_plugins[$plugin_path])) run_activate_plugin( $plugin_dir );

    }
}

function run_activate_plugin( $plugin ) {
    $current = get_option( 'active_plugins' );
    $plugin = plugin_basename( trim( $plugin ) );

    if ( !in_array( $plugin, $current ) ) {
        $current[] = $plugin;
        sort( $current );
        do_action( 'activate_plugin', trim( $plugin ) );
        update_option( 'active_plugins', $current );
        do_action( 'activate_' . trim( $plugin ) );
        do_action( 'activated_plugin', trim( $plugin) );
    }

    return null;
}
//Include Nivo Slider
full_copy($plugin_source, $plugin_target, $plugin );

//Include Option Tree
$plugin_source = "../wp-content/themes/CircloSquero/plugins/option-tree";
$plugin_target = "../wp-content/plugins/option-tree";
$plugin = 'option-tree/index.php';
full_copy($plugin_source, $plugin_target, $plugin );

//Include Piecemaker Slider
$plugin_source1 = "../wp-content/themes/CircloSquero/plugins/the-piecemaker";
$plugin_target1 = "../wp-content/plugins/the-piecemaker";
$plugin1 = "the-piecemaker/the_piecemaker.php";
full_copy($plugin_source1, $plugin_target1, $plugin1 );

//Include Breadcrumb NavXT Plugin
$plugin_source1 = "../wp-content/themes/CircloSquero/plugins/breadcrumb-navxt";
$plugin_target1 = "../wp-content/plugins/breadcrumb-navxt";
$plugin1 = "breadcrumb-navxt/breadcrumb_navxt_admin.php";
full_copy($plugin_source1, $plugin_target1, $plugin1 );
}




/* include widgets*/
include("search_widget.php");
include("latest-posts-widget.php");
include("most-commented-posts-widget.php");
include("tag-cloud-widget.php");
include("featured-works-widget.php");
include("twitter_widget.php");

/* register and enqueue scripts */

function portfolioScripts() {
wp_register_script('cs_quicksand', (get_bloginfo('template_url')."/scripts/jquery.quicksand.min.js"), false);
wp_enqueue_script('cs_quicksand');
wp_register_script('cs_jquery_easing', (get_bloginfo('template_url')."/scripts/jquery.easing.js"), false);
wp_enqueue_script('cs_jquery_easing');
wp_register_script('cs_quicksand_script', (get_bloginfo('template_url')."/scripts/script.js"), false);
wp_enqueue_script('cs_quicksand_script'); 
};

wp_register_style('nivo-inline', (get_bloginfo('template_url')."/nivo-inline.css"), false);



// remove_filter('the_content', 'wpautop');

if (!is_admin())
  add_filter('widget_text', 'do_shortcode', 11);

 

function circlosquero_pagination($next = 'Next', $prev = 'Previous',  $pages = '', $range = 4)
{  
     $showitems = ($range * 2)+1;  

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   

     if(1 != $pages)
     {
         echo "<div class='pagination'>";

         if($paged > 1) {echo "<div class='prevdivpag'><a href='".get_pagenum_link($paged - 1)."'>".$prev."</a></div>";} else {echo "<div class='prevdivpag'><a href='#' class='inactiveBB' >".$prev."</a></div>";};

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 if ($paged == $i) {echo "<span class='current'>".$i."</span>"; }
                 elseif ($paged+1 == $i) {
                    echo "<a href='".get_pagenum_link($i)."' class='inactiveNext' >".$i."</a>";
                 }
                 elseif (1 == $i) {
                    echo "<a href='".get_pagenum_link($i)."' class='inactiveNextone' >".$i."</a>";
                 }
                 else {
                 echo "<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
                 }
             }
         }

         if ($paged < $pages) {echo "<div class='nextdivpag'><a href='".get_pagenum_link($paged + 1)."' class='bbbordri' >".$next."</a></div>";} else {echo "<div class='nextdivpag'><a href='#' class='inactiveBB bbbordri' >".$next."</a></div>";};  

         echo "</div>\n";
     }
}

function the_breadcrumb() {
	if (!is_home()) {
		echo '<a href="';
		echo get_option('home');
		echo '">';
		bloginfo('name');
		echo "</a> > ";
			echo the_title();
	}
}

if ( !is_admin() ) {
wp_deregister_script('jquery');
wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"), false);
wp_enqueue_script('jquery');
}



function register_my_menus() {
  register_nav_menus(
    array('header-menu' => __( 'Header Menu' ) )
  );
}



add_action( 'init', 'register_my_menus' );

/* SIDEBARS*/
register_sidebar(array(
  'name' => 'Sidebar',
  'description' => 'Sidebar Widgets.',
  'before_title' => '<h2>',
  'after_title' => '</h2>',
  'before_widget' => '<li class="widgetSidebar">',
  'after_widget'  => '</li>'
));

register_sidebar(array(
  'name' => 'Footer Widgets Left',
  'description' => 'Left column in the footer for widgets.',
  'before_title' => '<h2>',
  'after_title' => '</h2>',
  'before_widget' => '<li class="widgetFooter">',
  'after_widget'  => '</li>'
));

register_sidebar(array(
  'name' => 'Footer Widgets Center',
  'description' => 'Middle column in the footer for widgets.',
  'before_title' => '<h2>',
  'after_title' => '</h2>',
  'before_widget' => '<li class="widgetFooter">',
  'after_widget'  => '</li>'
));

register_sidebar(array(
  'name' => 'Footer Widgets Right',
  'description' => 'Right column in the footer for widgets.',
  'before_title' => '<h2>',
  'after_title' => '</h2>',
  'before_widget' => '<li class="widgetFooter">',
  'after_widget'  => '</li>'
));

function show_post( $ID ) {
  $post = get_page( $ID, 'edit' );
  $content = apply_filters('the_content', $post->post_content);
  echo $content;
}

function cs_comments($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php if($depth > 1) {echo comment_class('comm_reply');}  else {echo comment_class();};  ?> id="li-comment-<?php comment_ID() ?>">
    <div id="comment-<?php comment_ID(); ?>" class="commentWrap">
      <div class="com_author_namer">

        <?php echo get_avatar($comment,$size='60'); ?>
        <div class="comm_reply_box">
          <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
        </div>
      </div>
      
      <div class="com_wrap">
        <div class="comment-meta commentmetadata">
          <span class="authorFont"><?php printf(__('%s'), get_comment_author_link()) ?></span> <?php if($depth > 1) {echo 'replied:';} else {echo 'said:'; };    ?><br/>
          <?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?><?php edit_comment_link(__('(Edit)'),'  ','') ?></div>
         <?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
      <?php endif; ?>
        <?php comment_text() ?>

      </div><div class="clear"> </div>
  </div>
    <div class="clear"> </div>
    <div class="commentSeparator"> </div>
    <div class="clear"> </div>
<?php
        }

/**
 * Attach CSS3PIE behavior to elements
 * This loads PIE into IE lol
 */
function my_render_css3_pie() {
?>   
<!--[if lte IE 8]>
<style type="text/css" media="screen">
   .roundbox, .commentWrap, #authorBoxx, .formm input, .comment-form-comment textarea, #rm_button, #tw_button, a.mediumbutton, .bigbutton,
   .smallbutton, #FooterWrap input, #FooterWrap textarea, .wpcf7-submit, .CStagcloud a, #twitter_update_listCS li {
      behavior: url('<?php echo get_bloginfo('template_url'); ?>/PIE.php');
      zoom: 1;
      position: relative;
      z-index: 99;
   }
</style>
<![endif]-->
 <!--[if IE 8]>
<style type="text/css" media="screen">
   .slidingContentTitleCSCS, .slidingContentContentCSCS {
      behavior: url('<?php echo get_bloginfo('template_url'); ?>/PIE.php');
      zoom: 1;
      position: relative;
      z-index: 99;
   }
</style>
<![endif]-->
 
<?php
;}

add_action('wp_head', 'my_render_css3_pie', 8);


/* /////////// SHORTCODES  /////////////// */


/*COLUMNS*/
function oneHalf($atts, $content = null) {
     $content = do_shortcode($content);
	return '<div class="oneHalf">'.$content.'</div>';
}

function oneHalfLast($atts, $content = null) {
     $content = do_shortcode($content);
	return '<div class="oneHalfLast">'.$content.'</div><div class="clear"></div>';
}

function oneThird($atts, $content = null) {
     $content = do_shortcode($content);
	return '<div class="oneThird">'.$content.'</div>';
}


function oneThirdLast($atts, $content = null) {
        $content = do_shortcode($content);
	return '<div class="oneThirdLast">'.$content.'</div><div class="clear"></div>';
}

function twoThirds($atts, $content = null) {
     $content = do_shortcode($content);
	return '<div class="twoThirds">'.$content.'</div>';
}

function twoThirdsLast($atts, $content = null) {
     $content = do_shortcode($content);
	return '<div class="twoThirdsLast">'.$content.'</div><div class="clear"></div>';
}

function oneFourth($atts, $content = null) {
     $content = do_shortcode($content);
	return '<div class="oneFourth">'.$content.'</div>';
}

function oneFourthLast($atts, $content = null) {
     $content = do_shortcode($content);
	return '<div class="oneFourthLast">'.$content.'</div><div class="clear"></div>';
}

function threeFourths($atts, $content = null) {
     $content = do_shortcode($content);
	return '<div class="threeFourths">'.$content.'</div>';
}

function threeFourthsLast($atts, $content = null) {
     $content = do_shortcode($content);
	return '<div class="threeFourthsLast">'.$content.'</div><div class="clear"></div>';
}


function IconColumn($atts, $content = null) {
     	extract(shortcode_atts(array(
		"icon_url48x48" => 'http://',
                "title" => '',
                "subtitle" => ''
	), $atts));
        $content = do_shortcode($content);
	return '<div class="oneThird"><div class="icon48"><img src="'.$icon_url48x48.'" alt="'.$title.'" width="48" height="48" /></div><div class="titleSubtitle"><h2 class="h2Blue">'.$title.'</h2><h5 class="h5regular">'.$subtitle.'</h5></div><div class="clear"></div><p class="iconPar">'.$content.'</p></div>';
}

function IconColumnLast($atts, $content = null) {
     	extract(shortcode_atts(array(
		"icon_url48x48" => 'http://',
                "title" => '',
                "subtitle" => ''
	), $atts));
       $content = do_shortcode($content);
	return '<div class="oneThirdLast"><div class="icon48"><img src="'.$icon_url48x48.'" alt="'.$title.'" width="48" height="48" /></div><div class="titleSubtitle"><h2 class="h2Blue">'.$title.'</h2><h5 class="h5regular">'.$subtitle.'</h5></div><div class="clear"></div><p class="iconPar">'.$content.'</p></div><div class="clear"></div>';
}


/*TOGGLES*/
function slidingContent($atts, $content = null) {
     	extract(shortcode_atts(array(
                "title" => '',
	), $atts));
	return '<div class="slidingContentWrapCSCS"><div class="slidingContentTitleCSCS"><p>'.$title.'</p></div><div class="slidingContentContentCSCS">'.$content.'</div></div>';
}

function slidingContent2($atts, $content = null) {
     	extract(shortcode_atts(array(
                "title" => '',
	), $atts));
	return '<div class="slidingContentWrapCSCS2"><div class="slidingContentTitleCSCS2"><p>'.$title.'</p></div><div class="slidingContentContentCSCS2">'.$content.'</div></div>';
}

function slidingContent3($atts, $content = null) {
     	extract(shortcode_atts(array(
                "title" => '',
	), $atts));
	return '<div class="slidingContentWrapCSCS3"><div class="slidingContentTitleCSCS3"><p>'.$title.'</p></div><div class="slidingContentContentCSCS3">'.$content.'</div></div>';
}

/*TESTIMONIALS*/
function testimonials1($atts, $content = null) {
     	extract(shortcode_atts(array(
                "by" => '',
	), $atts));
	return '<div class="testimonials1"><p>'.$content.'</p></div><div class="testimonialauthor"><p>'.$by.'</p></div>';
}
function testimonials2($atts, $content = null) {
     	extract(shortcode_atts(array(
                "by" => '',
	), $atts));
	return '<div class="testimonials2"><p>'.$content.'</p></div><div class="testimonial2author"><p>'.$by.'</p></div>';
}


/*HIGLIGHTS*/
function highlight1($atts, $content = null) {
	return '<span class="highlight1">'.$content.'</span>';
}
function highlight2($atts, $content = null) {
	return '<span class="highlight2">'.$content.'</span>';
}

/* BUTTONS */
function BigButton($atts, $content = null) {
     	extract(shortcode_atts(array(
		"link" => '#'
	), $atts));
        $content = do_shortcode($content);
	return '<a href="'. $link .'" class="bigbutton"><div>'.$content.'</div></a>';
}

function MediumButton($atts, $content = null) {
     	extract(shortcode_atts(array(
		"link" => '#'
	), $atts));
        $content = do_shortcode($content);
	return '<a href="'. $link .'" class="mediumbutton"><div>'.$content.'</div></a>';
}

function SmallButton($atts, $content = null) {
     	extract(shortcode_atts(array(
		"link" => '#'
	), $atts));
        $content = do_shortcode($content);
	return '<a href="'. $link .'" class="smallbutton"><div>'.$content.'</div></a>';
}

/* BLOCKQUOTES */
function bquoteleft($atts, $content = null) {
	return '<blockquote class="bqleft">'.$content.'</blockquote>';
}
function bquote($atts, $content = null) {
	return '<blockquote class="bqcenter">'.$content.'</blockquote>';
}
function bquoteright($atts, $content = null) {
	return '<blockquote class="bqright">'.$content.'</blockquote>';
}


/* RANDOM */
function h2blue($atts, $content = null) {
	return '<h2 class="h2Blue">'.$content.'</h2>';
}

function h5subtitle($atts, $content = null) {
	return '<h5 class="h5regular">'.$content.'</h5>';
}

function separatorDots($atts, $content = null) {
	return '<div class="separatorDots"></div>';
}

/* DROPCAPS */

function dropcap1($atts, $content = null) {
	return '<span class="dropcap1">'.$content.'</span>';
}
function dropcap2($atts, $content = null) {
	return '<span class="dropcap2">'.$content.'</span>';
}
function dropcap3($atts, $content = null) {
	return '<span class="dropcap3">'.$content.'</span>';
}

/* LISTS */
function ulcircles($atts, $content = null) {
     $content = do_shortcode($content);
	return '<ul class="circle_bullets">'.$content.'</ul>';
}
function ularrow1($atts, $content = null) {
     $content = do_shortcode($content);
	return '<ul class="arrow1_bullets">'.$content.'</ul>';
}
function ularrow2($atts, $content = null) {
     $content = do_shortcode($content);
	return '<ul class="arrow2_bullets">'.$content.'</ul>';
}

/* SLIDER */

function sliderCS($atts, $content = null) {
     
	return strip_tags('<script type="text/javascript">
jQuery(window).load(function() {
	jQuery(".slider-inline").nivoSlider({
		effect:"random",
		slices:5,
		animSpeed:500, //Slide transition speed
        pauseTime:6000,
        startSlide:0, //Set starting Slide (0 index)
        directionNav:false, //Next amd Prev
        directionNavHide:true, //Only show on hover
        controlNav:true, //1,2,3...
        controlNavThumbs:false, //Use thumbnails for Control Nav
        controlNavThumbsFromRel:false, //Use image rel for thumbs
        controlNavThumbsSearch: ".jpg", //Replace this with...
        controlNavThumbsReplace: "_thumb.jpg", //...this in thumb Image src
        keyboardNav:true, //Use left and right arrows
        pauseOnHover:true, //Stop animation while hovering
        manualAdvance:false, //Force manual transitions
        beforeChange: function(){},
        afterChange: function(){},
        slideshowEnd: function(){}, //Triggers after all slides have been shown
        lastSlide: function(){}, //Triggers when last slide is shown
        afterLoad: function(){} //Triggers when slider has loaded
	});
});
</script>
<div class="slider-inline"> '.$content.'</div>', '<a><img><script><div>');
}
	


/* INFO BOXES */
function InfoBox1($atts, $content = null) {
     	extract(shortcode_atts(array(
		"title" => ''
	), $atts));
	return '<div class="infobox1"><div class="titlebox1"><p>'.$title.'</p></div><div class="contentbox1"><br/><p>'.$content.'</p><br/></div></div>';
}

function InfoBox2($atts, $content = null) {
     	extract(shortcode_atts(array(
		"title" => ''
	), $atts));
	return '<div class="infobox2"><div class="titlebox2"><p>'.$title.'</p></div><div class="contentbox2"><br/><p>'.$content.'</p><br/></div></div>';
}


function InfoBox3($atts, $content = null) {
	return '<div class="infobox3"><div class="contentbox3"><br/><p>'.$content.'</p><br/></div></div>';
}

function InfoBox4($atts, $content = null) {
     	extract(shortcode_atts(array(
		"button_text" => 'More Info',
                "button_link"  => '#'
	), $atts));
	return '<div class="infobox4"><p>'.$content.'</p><br /><a href="'.$button_link.'" class="mediumbutton">'.$button_text.'</a></div>';
}

function InfoBox5($atts, $content = null) {
     	extract(shortcode_atts(array(
		"button_text" => 'More Info',
                "button_link"  => '#'
	), $atts));
	return '<div class="infobox5"><p>'.$content.'</p><br/><a href="'.$button_link.'" class="mediumbutton">'.$button_text.'</a></div>';
}

function InfoBox6($atts, $content = null) {
     	extract(shortcode_atts(array(
		"button_text" => 'More Info',
                "button_link"  => '#'
	), $atts));
	return '<div class="infobox6"><p>'.$content.'</p><br/><a href="'.$button_link.'" class="mediumbutton">'.$button_text.'</a></div>';
}


remove_shortcode('gallery', 'gallery_shortcode');

add_shortcode('gallery', 'cs_gallery_shortcode');

function cs_gallery_shortcode($attr) {
	global $post, $wp_locale;

	static $instance = 0;
	$instance++;

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr);
	if ( $output != '' )
		return $output;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
                'atttile'    => $post->post_title,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => ''
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';
	if ( apply_filters( 'use_default_gallery_style', true ) )
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
		</style>
		<!-- see gallery_shortcode() in wp-includes/media.php -->";
	$size_class = sanitize_html_class( $size );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		$link = "<a href='".wp_get_attachment_url( $id )."' rel='prettyPhoto[43]'><img src='".wp_get_attachment_thumb_url( $id )."' alt='".$attachment->post_title."' /></a>";
                
		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
			<{$icontag}  class='gallery-icon'>
				$link
			</{$icontag}>";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= '<br style="clear: both" />';
	}

	$output .= "
			<br style='clear: both;' />
		</div>\n";

	return $output;
}


add_shortcode("one_half", "oneHalf");
add_shortcode("one_half_last", "oneHalfLast");
add_shortcode("one_third", "oneThird");
add_shortcode("one_third_last", "oneThirdLast");
add_shortcode("two_thirds", "twoThirds");
add_shortcode("two_thirds_last", "twoThirdsLast");
add_shortcode("one_fourth", "oneFourth");
add_shortcode("one_fourth_last", "oneFourthLast");
add_shortcode("three_fourths", "threeFourths");
add_shortcode("three_fourths_last", "threeFourthsLast");

add_shortcode("slider", "sliderCS");

add_shortcode("info_box1", "InfoBox1");
add_shortcode("info_box2", "InfoBox2");
add_shortcode("info_box3", "InfoBox3");

add_shortcode("info_box4", "InfoBox4");
add_shortcode("info_box5", "InfoBox5");
add_shortcode("info_box6", "InfoBox6");

add_shortcode("testimonials1", "testimonials1");
add_shortcode("testimonials2", "testimonials2");

add_shortcode("big_button", "BigButton");
add_shortcode("medium_button", "MediumButton");
add_shortcode("small_button", "SmallButton");

add_shortcode("icon_column", "IconColumn");
add_shortcode("icon_column_last", "IconColumnLast");

add_shortcode("sliding_content1", "slidingContent");
add_shortcode("sliding_content2", "slidingContent2");
add_shortcode("sliding_content3", "slidingContent3");

add_shortcode("h2_title", "h2blue");
add_shortcode("h5_subtitle", "h5subtitle");

add_shortcode("highlight_1", "highlight1");
add_shortcode("highlight_2", "highlight2");

add_shortcode("bquote_left", "bquoteleft");
add_shortcode("bquote", "bquote");
add_shortcode("bquote_right", "bquoteright");

add_shortcode("dropcap1", "dropcap1");
add_shortcode("dropcap2", "dropcap2");
add_shortcode("dropcap3", "dropcap3");

add_shortcode("list_circle", "ulcircles");
add_shortcode("list_arrow1", "ularrow1");
add_shortcode("list_arrow2", "ularrow2");

add_shortcode("separator_dots", "separatorDots");










add_action( 'init', 'portfolio_custom_post' );
function portfolio_custom_post() {
	register_post_type( 'portfolio',
		array(
			'labels' => array(
				'name' => __( 'Portfolio' ),
				'singular_name' => __( 'Portfolio' ),
                              'add_new' => __( 'Add New' ),
                              'add_new_item' => __( 'Add New Portfolio Item' ),
                              'edit' => __( 'Edit' ),
                              'edit_item' => __( 'Edit Portfolio Item' ),
                              'new_item' => __( 'New Portfolio Item' ),
                              'view' => __( 'View Portfolio Item' ),
                              'view_item' => __( 'View Portfolio Item' ),
                              'search_items' => __( 'Search Portfolio Items' ),
                              'not_found' => __( 'No portfolio items found' ),
                              'not_found_in_trash' => __( 'No portfolio items found in Trash' )
			),
               'supports' => array( 'title', 'editor' ),
		'public' => true,
               'rewrite' => true
		)
	);
}

  $labels = array(
    'name' => _x( 'Project Types', 'project types' ),
    'singular_name' => _x( 'Project Type', 'project type' ),
    'search_items' =>  __( 'Search Project Types' ),
    'all_items' => __( 'All Project Types' ),
    'parent_item' => __( 'Parent Project Type' ),
    'parent_item_colon' => __( 'Parent Project Type:' ),
    'edit_item' => __( 'Edit Project Type' ), 
    'update_item' => __( 'Update Project Type' ),
    'add_new_item' => __( 'Add Project Type' ),
    'new_item_name' => __( 'New Project Type' ),
    'menu_name' => __( 'Project Type' ),
  ); 	



register_taxonomy("types", array("portfolio"), array("hierarchical" => true, 'labels' => $labels, "rewrite" => true));

add_action('add_meta_boxes', 'circlosquero_portfolio');

 
function circlosquero_portfolio(){
  add_meta_box("project_description-meta", "Project Description", "project_description", "portfolio", "normal");

}
 
function project_description(){
  global $post;
  $custom = get_post_custom($post->ID);
  $thumbnailurl = $custom["thumbnailurl"][0];
  $fullsizeurl = $custom["fullsizeurl"][0];
  $portfolioDescription = $custom["portfoliodesc"][0];
  ?>
  <label>Thumbnail URL:  </label> <br/>
  <input name="thumbnailurl" value="<?php echo $thumbnailurl; ?>" style="width: 300px;" />
  <p><em>Enter the location of the thumbnail for this portfolio item. This will be the thumbnail of the portfolio item when displaying it in portfolio items list.<br/>Dimensions:280x140px for grid view, or 590x265px for list view<br/>Example: www.yoursite.com/image.jpg</em></p>
  <br/> <br/>
  <label>Full size image OR Video URL:  </label> <br/>
  <input name="fullsizeurl" value="<?php echo $fullsizeurl; ?>" style="width: 300px;" />
  <p><em>Enter the location of the image/video for this portfolio item. This image/video will be shown in a lightbox when a user clicks on this item's thumbnail.<br/>Example: www.yoursite.com/image2.jpg OR http://www.youtube.com/embed/GLvWLn3hkBk</em></p><br/><br/>
  <label>Decription:</label><br/> 
  <textarea name="portfoliodesc" cols="50" rows="5" ><?php echo $portfolioDescription; ?></textarea>
  <p><em>Enter short decription of your project.</em></p>
  
  <?php
}
 

add_action('save_post', 'save_details');

function save_details(){
global $post;
$type = $post->post_type;
     if( $type == 'portfolio') { 
       update_post_meta($post->ID, "thumbnailurl", $_POST["thumbnailurl"]);
       update_post_meta($post->ID, "fullsizeurl", $_POST["fullsizeurl"]);
       update_post_meta($post->ID, "portfoliodesc", $_POST["portfoliodesc"]);
     }
}




?>