<?php
/*
	Template Name: Contact Page
*/
?>
<?php get_header(); ?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			
					<div class="simple_page_title">
				<?php the_title(); ?>
			</div>
			<?php include (TEMPLATEPATH . '/includes/get_shortcut.php'); ?>
			
			<div class="white_line"></div>
				
				<div class="page_contact">
					<!-- begin contact container -->
					<div class="page_container_contact">
						
						<!-- begin navigation content -->
						<div class="page_navigation_contact">

							<?php if (get_option('disable_contact_address', 1) == '1'): ?>
							<div class="page_navigation_title">
								<h3><?php echo get_option("our_address_title", "Meet Our Company"); ?></h3>
							</div>
							<div class="page_navigation_advert">
								<?php $address_text = '28. Freelancer City:Ankara / Country:TURKEY'; ?>
								<?php echo get_option('address_text', $address_text); ?>
							</div>
							<?php endif; ?>
							
							
							<?php if (get_option('disable_contact_call', 1) == '1'): ?>
							<div class="page_navigation_title">
								<h3><?php echo get_option("our_address_call", "Call us via Phone"); ?></h3>
							</div>
							<div class="page_navigation_advert">
								<?php $call_text = 'Tel. +90 (312) 441 23 4* <br />Fax. +90 (312) 441 23 4*'; ?>
								<?php echo get_option('call_text', $call_text); ?>
							</div>
							<?php endif; ?>
							
							
							<?php if (get_option('disable_contact_emails', 1) == '1'): ?>
							<div class="page_navigation_title">
								<h3><?php echo get_option("our_emails", "Emails"); ?></h3>
							</div>
							<div class="page_navigation_advert">
								<?php $email_text = 'General: info@codestarlive.com<br />Support: support@codestarlive.com<br />Shopping: shopping@codestarlive.com'; ?>
								<?php echo get_option('email_text', $email_text); ?>
							</div>
							<?php endif; ?>
							
							
							<?php if (get_option('disable_contact_text', 1) == '1'): ?>
							<div class="page_navigation_title">
								<h3><?php echo get_option("miscell", "Miscellaneous"); ?></h3>
							</div>
							<div class="page_navigation_advert">
								<?php $contact_text = 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. <br /><br />Excepteur sint occaecat cupidatat non proident, sunt in anim id est laborum! Allamco laboris nisi ut aliquip ex ea commodo consequat! Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.'; ?>
								<?php echo get_option('contact_text', $contact_text); ?>
							</div>
							<?php endif; ?>
						<div class="cleardiv"></div>
						</div>
						<!-- end navigation content -->
						
						<!-- begin map and contact form -->
						<div class="page_contact_container">
							
							<?php if (get_option('disable_google_map', 1) == '1'): ?>
							<!-- begin map -->
							<div class="map_container">
								<div class="portfolio_image_skin2">
									<div class="inside_border">
										<div class="portfolio_box_anime">
											<?php $google_iframe_key = '<iframe width="525" height="279" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=tr&amp;geocode=&amp;q=ankara&amp;ie=UTF8&amp;hq=&amp;hnear=Ankara,+T%C3%BCrkiye&amp;z=15&amp;ll=39.943873,32.856034&amp;output=embed"></iframe>'; ?>
											<?php echo get_option('google_iframe_key', $google_iframe_key); ?>
										</div>
									</div>
								</div>
								<div class="cleardiv"></div>
							</div>
							<!-- end map -->
							
							<!-- begin map text -->
							<div class="map_text">
								<div class="portfolio_title">
									<div class="custom_title"><?php echo get_option("google_title", "Google Map"); ?></div>
								</div>
								<?php $google_text = 'In addition to online resources like the forums and mailing lists a great way to get ailing lists a great way to get involved withIn addition to online resources like the forums and mailing lists a great way to get ailing listsIn addition to online resources like the forums and mailing lists a great way to get ailing lists'; ?>
								<?php echo get_option('google_text', $google_text); ?>
							</div>
							<!-- end map text -->
							<?php endif; ?>
						
							<?php if (get_option('disable_contact_form', 1) == '1'): ?>
							<!-- begin contact form -->
							<div class="contact_form">
								<form id="myform" action="" method="post">
								
								<div class="contact_title">
									<div class="custom_title"><?php echo get_option("form_title", "Contact Form"); ?></div>
								</div>
								<div id="result" style="display:none;"></div>
								<div id="success" class="success" style="display:none;"><?php echo get_option("form_success", "Success, mail has been sent."); ?></div>
								<div id="error" class="error" style="display:none;"><?php echo get_option("form_error", "Error! Plese check fields."); ?></div>
	
									<div class="contact_form_item">
										<div class="contact_form_title"><?php echo get_option("form_name", "Name"); ?></div>
										<div class="contact_form_input_bg">
											<div class="contact_form_bg"><input id="form_name" name="Name" type="text" class="is_required" /></div>
										</div>
										<div class="contact_form_required"><?php echo get_option("required", "(* required)"); ?></div>
									</div>
									
									<div class="contact_form_item">
										<div class="contact_form_title"><?php echo get_option("form_mail", "Email Address"); ?></div>
										<div class="contact_form_input_bg">
											<div class="contact_form_bg"><input id="form_email" name="Email" type="text" class="vemail is_required"/></div>
										</div>
										<div class="contact_form_required"><?php echo get_option("required", "(* required)"); ?></div>
									</div>
										
									<div class="contact_form_item">
										<div class="contact_form_title"><?php echo get_option("form_subject", "Subject"); ?></div>
										<div class="contact_form_input_bg">
											<div class="contact_form_bg"><input id="form_subject" name="Subject" type="text" class="is_required"/></div>
										</div>
										<div class="contact_form_required"><?php echo get_option("required", "(* required)"); ?></div>
									</div>
									
									<div class="contact_form_item" style="height:130px;">
										<div class="contact_form_title"><?php echo get_option("form_message", "Message"); ?></div>
										<div class="contact_form_textarea_input_bg">
											<div class="contact_form_message_bg"><textarea id="form_message" name="Message" class="is_required" rows="0"  cols="0"></textarea></div>
										</div>
										<div class="contact_form_required"><?php echo get_option("required", "(* required)"); ?></div>
									</div>
									
									<div>
										<div class="contact_form_submit" style="width:86px;">
										  <input type="submit" name="Submit" value="<?php echo get_option("form_submit", "SUBMIT"); ?>" id="mySend" style="width:86px; height:26px; margin:0; cursor:pointer; padding:0; left:0; color:#555;" />
										</div>
									</div>
		
								</form>
							</div>
							<!-- begin contact form -->
							<?php endif; ?>

						</div>
						<!-- end map and contact form -->
						
					</div>
					<!-- end contact container -->
					
				</div>
				<!-- end container text -->					
		
<?php endwhile; endif;  ?>

<?php $no_contact = true; include (TEMPLATEPATH . '/footer.php'); ?>