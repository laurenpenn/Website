<?php
/**
 * 404 Template
 *
 * The 404 template is used when a reader visits an invalid URL on your site. By default, the template will 
 * display a generic message.
 *
 * @package Prototype
 * @subpackage Template
 * @link http://codex.wordpress.org/Creating_an_Error_404_Page
 */

@header( 'HTTP/1.1 404 Not found', true, 404 ); ?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
	<meta property="fb:page_id" content="206268862487" />
	<title><?php hybrid_document_title(); ?></title>
	
	<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="all" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
	<?php wp_head(); // wp_head ?>
    
	<style type="text/css">
	html {
		margin: 0 !important;
		padding: 0 !important;
	}
	body {
		color: #5c5650;
		font: 14px/18px Tahoma, Arial, Helvetica, sans-serif;
		margin: 0 !important;
		padding: 0 !important;
	}
	
	#container {
		padding: 50px 0;
	}
	
	#logo { text-align: center; }
	
	#ohno {
		border: 1px solid #000;
		border-radius: 20px;
		-moz-border-radius: 20px;
		-webkit-border-radius: 20px;
		box-shadow: 0 0 30px #38241D;
		-moz-box-shadow: 0 0 30px #38241D;
		-webkit-box-shadow: 0 0 30px #38241D;
		margin: 50px auto 0;
		width: 550px;
	}
	#ohno-inside {
		background: #15100e url('<?php bloginfo( 'template_directory'); ?>/library/images/bg.png') center top no-repeat;
		border-radius: 20px;
		-moz-border-radius: 20px;
		-webkit-border-radius: 20px;
		box-shadow: inset 0 0 10px #000;
		-moz-box-shadow: inset 0 0 10px #000;
		-webkit-box-shadow: inset 0 0 10px #000;
		color: #C8B6A2;
		padding: 30px 50px;
		text-shadow: -1px -1px 1px #15100e;
	}
	h1 {
		font: 30px/30px "Georgia", "Times New Roman", Times, Serif;
		text-align: center;
	}
	h2 {
		color: #6B4012;
		font: 20px/20px "Georgia", "Times New Roman", Times, Serif;
		text-align: center;
		text-shadow: 0 1px 0 #000;
	}
	p {
		font-size: 12px;
	}
	a { color: #fff; }
	#search-form {
		margin: 50px auto;
	}
	</style>
</head>
<body>
	
	<div id="container">
	
		<div id="logo"><img src="<?php bloginfo( 'template_directory'); ?>/library/images/denton-bible-church.png" alt="Denton Bible Church" /></div>
		
		<div id="ohno">
			<div id="ohno-inside">
				<h1>That page doesn't exist</h1>
				<h2>there's a few reasons this page may not work:</h2>
				<ul>
					<li>An <strong>out-of-date bookmark/favorite</strong></li>
					<li>A search engine that has an <strong>out-of-date listing for us</strong></li>
					<li>A <strong>mis-typed address</strong></li>
				</ul>				
				<form action="http://dentonbible.org/" id="search-form" class="search-form" method="get">
					<div>
						<input type="text" onblur="if(this.value=='')this.value=this.defaultValue;" onfocus="if(this.value==this.defaultValue)this.value='';" value="Try searching..." id="search-text" name="s" class="search-text">
						<input type="submit" value="Search" id="search-submit" name="submit" class="search-submit button">
					</div>
				</form>
				<p style="text-align: center;">Or just <a href="http://dentonbible.org/">go to the home page</a>.</p>
			</div>
			
		</div>
		
	</div>
	
	<?php wp_footer(); // wp_footer ?>
	
</body>
</html>