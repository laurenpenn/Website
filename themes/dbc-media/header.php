<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head profile="http://gmpg.org/xfn/11">
	
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :: '; } ?><?php bloginfo('name'); ?></title>	
	    
	    <meta http-equiv="content-type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	    
	    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	    
	    <link rel="shortcut icon" href="https://dentonbible.org/images/favicon.ico" />
		
	    <?php wp_head(); ?>
			
	</head>
	
	<body>
	
		<div id="background">
					
		    <div id="doc2" class="yui-t3">
	   
		        <div id="hd">
		        	<div class="container">
						<ul id="hd-links">
							<li><a href="<?php bloginfo('url'); ?>/support/contact-us/"><span class="anchor-text">Contact Us</span></a> |</li>
							<li><a href="<?php bloginfo('url'); ?>/support/"><span class="anchor-text">Need Help?</span></a></li>
						</ul>
					</div>
	
					<div id="top" class="yui-g">

						<?php if ( is_front_page() ) { ?>
							<div class="yui-u first">
								<h1 id="logo"><a href="<?php bloginfo('url'); ?>/"><img src="<?php bloginfo('template_directory'); ?>/images/denton-bible-church.png" title="<?php bloginfo('name'); ?>" alt="<?php bloginfo('name'); ?>" /></a></h1>
							</div>
							
							<?php } else { ?>
							
							<div class="yui-u first">
								<div id="title"><a href="<?php bloginfo('url'); ?>/"><img src="<?php bloginfo('template_directory'); ?>/images/denton-bible-church.png" title="<?php bloginfo('name'); ?>" alt="<?php bloginfo('name'); ?>" /></a></div>
							</div>
							
						<?php } ?>
											
						<div id="tagline" class="yui-u">
											
							<?php get_search_form(); ?>
							
						</div>
	
					</div><!-- end .yui-g -->
	
					<div id="static-nav" class="yui-g">
							
						<ul>
							<li class="static_page_item static-page-item-8 current_static_page_item"><a href="<?php bloginfo('url'); ?>">Media Home</a></li>
							<li class="static_page_item static-page-item-1"><a href="http://www.dentonbible.org/">DBC Home</a></li>
						</ul>
												
					</div><!-- end #nav -->
									
					<div id="nav" class="yui-g">

						<?php wp_page_menu('show_home=1&exclude_tree=58'); ?>
						
						<p class="floatright"><a href="<?php bloginfo('url'); ?>/shop/cart/" class="addtocart"><span>View Cart</span></a></p>
				
					</div><!-- end #nav -->
						        
		        </div><!--end #hd -->