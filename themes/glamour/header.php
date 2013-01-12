<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head>

<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); ?><?php bloginfo('name'); ?></title>

<meta name="description" content="<?php bloginfo('description'); ?>" />

<meta name="keywords" content="<?php echo get_option('sitekeywords', 'the, tags, web sites, clean, dark, useful, etc...'); ?>" />



<?php wp_head(); ?>

<?php wp_meta(); ?>



<!-- including css style -->

<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />





<style type="text/css">

<?php

	$bg = get_option('bg_img', '');

	echo "body{

	color:#".get_option('body_font_color', '555555').";

	font-family:".get_option('body_font', 'Tahoma').";

	font-size:".get_option('body_font_size', '13')."px;

	background-color:#".get_option('bg_color', 'd6dbdd').";

	"; 

	if($bg != ''){ echo "background:url(".get_option('bg_img', '').");";	}

	echo "

}

	

.portfolio_box_skin_3{

	width:";

	$pmin = get_option('portfolio3_thumb_width', '270') + 20;

	echo $pmin."px;	

}



.blog_box_skin_3{

	width:";

	$pmin = get_option('blog_thumb_width', '620') + 20;

	echo $pmin."px;	

}



.portfolio_box_skin_2{

	width:";

	$pmin = get_option('portfolio1_thumb_width', '270') + 20;

	echo $pmin."px;

}

";

?>

</style>



<!-- including javascripts (jquery, swfObject, prettyPhoto) -->

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery-1.4.2.min.js"></script>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.anythingslider.js"></script>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.easing.1.2.js"></script>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.tabs.min.js"></script>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.prettyPhoto.js"></script>



<?php if (get_option('cufon_active', 1) == '1'): ?>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/cufon.js"></script>

<script type="text/javascript" src="<?php echo get_option('cufon_font', get_bloginfo('template_url').'/js/Neo_Sans_Std_400-Neo_Sans_Std_400.font.js'); ?>"></script>

<?php endif; ?>



<!--[if IE 6]>

	<script src="<?php bloginfo('template_url'); ?>/js/DD_belatedPNG_0.0.8a-min.js"></script>

	<script>

		DD_belatedPNG.fix('img, div, .back, .forward, #thumbContainer, #thumbNav a, .portfolio_zoom');

	</script>

<![endif]-->



<!--[if IE 7]>

	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/ie7.css" />

<![endif]-->



<script type="text/javascript">

		<!--

		$(document).ready(function ()

		{

			$('#myform').FormValidate({

				phpFile:"<?php bloginfo('template_url'); ?>/includes/get_mail.php?email=<?php $email = get_option('siteadmin_email'); if ($email != "") { echo get_option('siteadmin_email'); } else { bloginfo('admin_email'); } ?>",

				ajax:true

			});

		});

		-->

</script>



</head>

<body>







<!-- begin site container -->

<div class="container">

	

	<div class="header_bg"></div><!-- this container header bg -->

	

	<!-- begin site content -->

	<div class="content">

		

		<!-- begin site inside content -->

		<div class="site_content clearfix">

			

			<!-- begin header container -->

			<div class="header_container">

				

				<!-- begin logo -->

				<div class="logo">

					<a href="<?php bloginfo('siteurl'); ?>"><img src="<?php echo get_option('sitelogo', get_bloginfo('template_url').'/images/logo.png'); ?>" alt="<?php bloginfo('description'); ?>"/></a>

				</div>

				<!-- end logo -->

				

				<!-- begin search -->

				<div class="page_search">

					<?php include (TEMPLATEPATH . '/includes/get_search.php'); ?>

				</div>

				<!-- end begin search -->

				

				<div class="cleardiv"></div>

				

				<!-- begin menu holder -->

				<div class="menu_holder">

					

					<!-- begin menu container -->

					<div class="menu_container">

					

					

						<ul class="site_menu">

							<?php if (get_option('disable_home_page', 1) == '1'): ?>

							<li<?php if (is_home()) { echo " class=\"current_page_item\""; } ?>><a href="<?php bloginfo('siteurl'); ?>"><span><?php echo get_option('home_page_name', 'Home'); ?></span></a></li>

							<?php endif; ?>

							<?php 

							add_filter('wp_list_pages','remove_title');

							function remove_title($content) {

								$content = preg_replace('/title=\"(.*?)\"/','',$content);

									return preg_replace('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a>@i', '<li$1><a$2><span>$3</span></a>',$content);

							}

							wp_list_pages('title_li=&sort_order=ASC&sort_column=menu_order');

							?>

							

							

						</ul>

						

					</div>

					<!-- end menu container -->

					

				</div>

				<!-- end menu holder -->

				

			</div>

			<!-- end header container -->

			

			<div class="cleardiv"></div>
