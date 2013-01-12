
			<div class="cleardiv"></div>
			
			
			<!-- begin footer widgets -->
			<div class="footer_widgets clearfix">
				
				<!-- begin footer widget 1 -->
				<div class="footer_widgets_box">
					<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Footer Column 1')) {} else { echo "Footer Column 1"; }  ?>
				</div>
				<!-- end footer widget 1 -->
				
				<!-- begin footer widget 2 -->
				<div class="footer_widgets_box">
					<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Footer Column 2')) {} else { echo "Footer Column 2"; }  ?>

				</div>
				<!-- end footer widget 2 -->
				
				<!-- begin footer widget 3 -->
				<div class="footer_widgets_box">
					<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Footer Column 3')) {} else { echo "Footer Column 3"; }  ?>

				</div>
				<!-- end footer widget 3 -->
				
				<!-- begin footer widget 3 -->
				<div class="footer_widgets_box">
					<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Footer Column 4')) {} else { echo "Footer Column 4"; }  ?>

				</div>
				<!-- end footer widget 4 -->
				
				<!-- begin footer widget 5 -->
				<div class="footer_widgets_box">
					
						<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Footer Column 5 (Contact)')) {} else { ?>
						
						<?php if (get_option('disable_footer_contact', 1) == '1') { ?>
						<?php if($no_contact != true) { ?>
						<div class="widgets_title"><?php echo get_option("footer_contact_title", "Contact us"); ?></div>
						<!-- begin contact form -->
						<div class="footer_form">
							<?php echo get_option("footer_contact_subtitle", "Get in touch with us"); ?>
							<form id="myform" action="" method="post">
								<input id="form_name" name="Name" type="text" value="<?php echo get_option("footer_contact_name", "name"); ?>" class="activefocus is_required vname input_footer" />
								<input id="form_email" name="Email" type="text" value="<?php echo get_option("footer_contact_email", "email address"); ?>" class="activefocus is_required vsemail vemail input_footer"/>
								<input id="form_subject" name="Subject" type="text" value="<?php echo get_option("footer_contact_subject", "subject"); ?>" class="activefocus is_required vsubject input_footer"/>
								<div class="textarea_form"><textarea id="form_message" name="Message" class="activefocus is_required vmessage textarea_footer" rows="0"  cols="0"><?php echo get_option("footer_contact_message", "message"); ?></textarea></div>
								<div id="result" style="display:none;"></div>
								<div id="success" class="mini_div"><?php echo get_option("footer_contact_success", "Success, mail sent."); ?></div>
								<div id="error" class="mini_div"><?php echo get_option("footer_contact_error", "Error! Check Fields"); ?></div>
								<input type="submit" name="Submit" value="<?php echo get_option("footer_contact_send", "send"); ?>" id="mySend" class="submit_bg" style="width:86px; height:26px; margin:0; cursor:pointer; padding:0; left:0; color:#555;"/>
							</form>
						</div>
						<!-- end contact form -->
						<?php } } else { echo "Footer Column 5 (Contact)"; } } ?>
					
				</div>
				<!-- end footer widget 5 -->
				
			</div>
			<!-- end footer widgets -->
			
			<div class="white_line_alternative"></div>

	</div>
	<!-- end site inside content -->
		
	</div>
	<!-- end site content -->
	
	<div class="cleardiv"></div>

	<!-- begin footer -->
	<div class="footer_container">
			<!-- begin footer text -->
			<div class="footer_text clearfix">
				
				<!-- begin footer menu -->
				<div class="footer_menu">
					<ul>
						<li><a href="<?php bloginfo('siteurl'); ?>"><?php echo get_option('home_page_name', 'Home'); ?></a></li>
					<?php
						$pages = get_pages('sort_column=post_date&sort_order=ASC&parent=0');
						foreach($pages as $page)
						{		
						?>
						<li><a href="<?php echo get_page_link($page->ID) ?>"><?php echo $page->post_title ?></a></li>
						<?php
						}	
					?>
					</ul>
				</div>
				<!-- end footer menu -->

				
				<div class="cleardiv"></div>
				<div class="dark_line"></div>
				
				<!-- begin copyright text -->
				<div class="footer_copyright">
					<?php $copyright_text = 'Copyright 2010 THE GLAMOUR - Wordpress Theme. All Rights Reserved.<br />Designed and developed by <a href="#">Codestar</a>'; ?>
					<?php echo get_option('copyright_text', $copyright_text); ?>
				</div>
				<!-- end copyright text -->
				
				<!-- begin footer social network icons -->
				<div class="footer_icons">
					<?php $social_text = '<ul>
	<li><a href="http://twetter.com/" target="_blank"><img src="'.get_bloginfo('template_url').'/images/icons/2.png" alt=""/></a></li>
	<li><a href="http://facebook.com/" target="_blank"><img src="'.get_bloginfo('template_url').'/images/icons/3.png" alt=""/></a></li>
  	<li><a href="'.get_bloginfo('siteurl').'/?feed=rss2" target="_blank"><img src="'.get_bloginfo('template_url').'/images/icons/4.png" alt=""/></a></li>
</ul>'; ?>
					<?php echo get_option('social_text', $social_text); ?>
				</div>
				<!-- end footer social network icons -->
				
			</div>
			<!-- end footer text -->
		
	</div>
	<!-- end footer -->
	
	<div class="footer_bg"></div>
	
</div>
<!-- end site container -->

<?php if (get_option('cufon_active', 1) == '1'): ?>
<script type="text/javascript">Cufon.now();</script>
<?php endif; ?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/custom.js"></script> 
<?php echo get_option('google_analytics'); ?>

<?php wp_footer(); ?>
</body>
</html>	
