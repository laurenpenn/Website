<style>
	.widefat td{
		vertical-align:middle;
		padding-top:20px;
		padding-bottom:20px;
	}
</style>

<div class="wrap nosubsub">
	<div id="icon-link-manager" class="icon32"><br /></div><h2>The Glamour Contact Page</h2><br  />
	
	<div class="clear"></div>

	<form action="options.php" method="post">
	<?php wp_nonce_field('update-options'); ?>

	<table class="widefat" cellspacing="0">
	
		<thead>
			<tr>
				<th scope="col" id="name" class="manage-column column-name" colspan="3">Table Of Contact</th>
			</tr>
			</thead>
			
			<tfoot>
			<tr>
				<th scope="col"  class="manage-column column-name" colspan="3"></th>
			</tr>
		</tfoot>
		<tbody>
	
		<tr class="alternate">
			<td class="column-name" colspan="3">
				<table>
				<tr >
					<td style="border:0px;">Address Enable ? <input name="disable_contact_address" type="radio" value="0" <?php if(get_option('disable_contact_address') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
					<input name="disable_contact_address" type="radio" value="1" <?php if(get_option('disable_contact_address', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
					<td style="border:0px;"></td>
				</tr>
				<tr >
					<td style="border:0px;"><img src="<?php echo get_bloginfo('template_url'); ?>/images/admin/page_contact.png"  alt=""/></td>
					<td style="border:0px; width:350px;"><input type="text" style="width:100%;" name="our_address_title" value="<?php echo get_option("our_address_title", "Meet Our Company"); ?>"/><br>
					
						<?php $address_text = '28. Freelancer City:Ankara / Country:TURKEY'; ?>
						<textarea rows='3' style="width:100%;" name='address_text'><?php echo get_option('address_text', $address_text); ?></textarea>
					</td> 
				</tr>
				</table>
			</td>
		</tr>
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>

		<tr class="alternate">
			<td class="column-name" colspan="3">
			
				<table>
				<tr >
					<td style="border:0px;">Call us Enable ? <input name="disable_contact_call" type="radio" value="0" <?php if(get_option('disable_contact_call') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
					<input name="disable_contact_call" type="radio" value="1" <?php if(get_option('disable_contact_call', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
					<td style="border:0px;"></td>
				</tr>
				<tr >
					<td style="border:0px;"><img src="<?php echo get_bloginfo('template_url'); ?>/images/admin/call_us.png"  alt=""/></td>
					<td style="border:0px; width:350px;"><input type="text" style="width:100%;" name="our_address_call" value="<?php echo get_option("our_address_call", "Call us via Phone"); ?>"/><br>
					
						<?php $call_text = 'Tel. +90 (312) 441 23 4* <br />
Fax. +90 (312) 441 23 4*'; ?>
						<textarea rows='3' style="width:100%;" name='call_text'><?php echo get_option('call_text', $call_text); ?></textarea>
					</td> 
				</tr>
				</table>
				
				
			</td>
		</tr>
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>

		<tr class="alternate">
			<td class="column-name" colspan="3">
			
				<table>
				<tr >
					<td style="border:0px;">Emails Enable ? <input name="disable_contact_emails" type="radio" value="0" <?php if(get_option('disable_contact_emails') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
					<input name="disable_contact_emails" type="radio" value="1" <?php if(get_option('disable_contact_emails', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
					<td style="border:0px;"></td>
				</tr>
				<tr >
					<td style="border:0px;"><img src="<?php echo get_bloginfo('template_url'); ?>/images/admin/emails.png"  alt=""/></td>
					<td style="border:0px; width:350px;"><input type="text" style="width:100%;" name="our_emails" value="<?php echo get_option("our_emails", "Emails"); ?>"/><br>
					
						<?php $email_text = 'General: info@codestarlive.com<br />
Support: support@codestarlive.com<br />
Shopping: shopping@codestarlive.com'; ?>
						<textarea rows='3' style="width:100%;" name='email_text'><?php echo get_option('email_text', $email_text); ?></textarea>
					</td> 
				</tr>
				</table>
				
			</td>
		</tr>
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>

		<tr class="alternate">
			<td class="column-name" colspan="3">
			
				<table>
				<tr >
					<td style="border:0px;">Contact Text Enable ? <input name="disable_contact_text" type="radio" value="0" <?php if(get_option('disable_contact_text') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
					<input name="disable_contact_text" type="radio" value="1" <?php if(get_option('disable_contact_text', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
					<td style="border:0px;"></td>
				</tr>
				<tr >
					<td style="border:0px;"><img src="<?php echo get_bloginfo('template_url'); ?>/images/admin/contact_text.png"  alt=""/></td>
					<td style="border:0px; width:350px;"><input type="text" style="width:100%;" name="miscell" value="<?php echo get_option("miscell", "Miscellaneous"); ?>"/><br>
					
						<?php $contact_text = 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. <br /><br />Excepteur sint occaecat cupidatat non proident, sunt in anim id est laborum! Allamco laboris nisi ut aliquip ex ea commodo consequat! Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.'; ?>
						<textarea rows='3' style="width:100%;" name='contact_text'><?php echo get_option('contact_text', $contact_text); ?></textarea>
					</td> 
				</tr>
				</table>
				
			</td>
		</tr>
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>

		<tr class="alternate">
			<td class="column-name" colspan="3">
			
				<table>
				<tr >
					<td style="border:0px;">Google Map Enable ? <input name="disable_google_map" type="radio" value="0" <?php if(get_option('disable_google_map') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
					<input name="disable_google_map" type="radio" value="1" <?php if(get_option('disable_google_map', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
					<td style="border:0px;"></td>
				</tr>
				<tr >
					<td style="border:0px;"><img src="<?php echo get_bloginfo('template_url'); ?>/images/admin/google.png"  alt=""/></td>
					<td style="border:0px; width:350px;">
						
						<?php $google_iframe_key = '<iframe width="525" height="279" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=tr&amp;geocode=&amp;q=ankara&amp;ie=UTF8&amp;hq=&amp;hnear=Ankara,+T%C3%BCrkiye&amp;z=15&amp;ll=39.943873,32.856034&amp;output=embed"></iframe>'; ?>
						<textarea rows='15' style="width:100%;" name='google_iframe_key'><?php echo get_option('google_iframe_key', $google_iframe_key); ?></textarea>
						<input type="text" style="width:100%;" name="google_title" value="<?php echo get_option("google_title", "Google Map"); ?>"/><br>
						<?php $google_text = 'In addition to online resources like the forums and mailing lists a great way to get ailing lists a great way to get involved withIn addition to online resources like the forums and mailing lists a great way to get ailing listsIn addition to online resources like the forums and mailing lists a great way to get ailing lists'; ?>
						<textarea rows='5' style="width:100%;" name='google_text'><?php echo get_option('google_text', $google_text); ?></textarea>
					</td> 
				</tr>
				</table>
				
				
			</td>
		</tr>
	
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>

		<tr class="alternate">
			<td class="column-name" colspan="3">
			
				<table>
				<tr >
					<td style="border:0px;">Contact Form Enable ? <input name="disable_contact_form" type="radio" value="0" <?php if(get_option('disable_contact_form') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
					<input name="disable_contact_form" type="radio" value="1" <?php if(get_option('disable_contact_form', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
					<td style="border:0px;"></td>
				</tr>
				<tr >
					<td style="border:0px;"><img src="<?php echo get_bloginfo('template_url'); ?>/images/admin/form.png"  alt=""/></td>
					<td style="border:0px; width:350px;">
						<input type="text" style="width:100%;" name="form_title" value="<?php echo get_option("form_title", "Contact Form"); ?>"/><br><br>
						<input type="text" style="width:100%;" name="form_name" value="<?php echo get_option("form_name", "Name"); ?>"/><br><br>
						<input type="text" style="width:100%;" name="form_mail" value="<?php echo get_option("form_mail", "Email Address"); ?>"/><br><br>
						<input type="text" style="width:100%;" name="form_subject" value="<?php echo get_option("form_subject", "Subject"); ?>"/><br><br>
						<input type="text" style="width:100%;" name="form_message" value="<?php echo get_option("form_message", "Message"); ?>"/><br><br>
						<input type="text" style="width:100%;" name="required" value="<?php echo get_option("required", "(* required)"); ?>"/><br><br>
						<input type="text" style="width:100%;" name="form_submit" value="<?php echo get_option("form_submit", "SUBMIT"); ?>"/><br><br>
						<input type="text" style="width:100%;" name="form_success" value="<?php echo get_option("form_success", "Success, mail has been sent."); ?>"/><br><br>
						<input type="text" style="width:100%;" name="form_error" value="<?php echo get_option("form_error", "Error! Plese check fields."); ?>"/><br>
					</td> 
				</tr>
				</table>
			
			</td>
		</tr>
	
		<tr class="alternate">
			<td class="column-name" colspan="3">
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="form_submit, form_message, disable_contact_form, form_title, form_name, form_mail, form_subject, required, form_success, form_error, disable_contact_address, our_address_title, address_text, disable_contact_call, our_address_call, call_text, disable_contact_emails, our_emails, email_text, disable_contact_text, miscell, contact_text, disable_google_map, google_iframe_key, google_title, google_text" />
				<input type="submit" name="publish" id="publish" accesskey="p" tabindex="5" class="button-primary" value="Save" />
				</form>
			</td>
		</tr>
		
		</tbody>
	</table>

</div>
