<style>
	.widefat td{
		vertical-align:middle;
		padding-top:20px;
		padding-bottom:20px;
	}
</style>

<div class="wrap nosubsub">
	<div id="icon-link-manager" class="icon32"><br /></div><h2>The Glamour Footer Widgets</h2><br  />
	
	<div class="clear"></div>

	<form action="options.php" method="post">
	<?php wp_nonce_field('update-options'); ?>

	<table class="widefat" cellspacing="0">
	
		<thead>
			<tr>
				<th scope="col" id="name" class="manage-column column-name" colspan="3">Footer Table Of Content</th>
			</tr>
			</thead>
			
			<tfoot>
			<tr>
				<th scope="col"  class="manage-column column-name" colspan="3"></th>
			</tr>
		</tfoot>
		<tbody>
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>

		<tr class="alternate">
			<td class="column-name" colspan="3">
			
				<table width="100%;">
				<tr>
					<td style="border:0px; width:300px;">
						Copyright Enable ? <input name="disable_copyright_text" type="radio" value="0" <?php if(get_option('disable_copyright_text') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
						<input name="disable_copyright_text" type="radio" value="1" <?php if(get_option('disable_copyright_text', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please
					</td>
					</tr>
					
					<tr>
					<td style="border:0px;" colspan="2">
						<?php $copyright_text = 'Copyright 2010 THE GLAMOUR - Wordpress Theme. All Rights Reserved.<br />Designed and developed by Codestar'; ?>
						<textarea rows='3' style="width:100%;" name='copyright_text'><?php echo get_option('copyright_text', $copyright_text); ?></textarea>	
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
			
				<table width="100%;">
				<tr>
					<td style="border:0px; width:325px;">
						Social Network Enable ? <input name="disable_social" type="radio" value="0" <?php if(get_option('disable_social') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
						<input name="disable_social" type="radio" value="1" <?php if(get_option('disable_social', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please
					</td>
					</tr>
					
					<tr>
					<td style="border:0px;" colspan="2">
						<?php $social_text = '<ul>
	<li><a href="http://twetter.com/" target="_blank"><img src="'.get_bloginfo('template_url').'/images/icons/2.png" alt=""/></a></li>
	<li><a href="http://facebook.com/" target="_blank"><img src="'.get_bloginfo('template_url').'/images/icons/3.png" alt=""/></a></li>
  	<li><a href="'.get_bloginfo('siteurl').'/?feed=rss2" target="_self"><img src="'.get_bloginfo('template_url').'/images/icons/4.png" alt=""/></a></li>
</ul>'; ?>
						<textarea rows='10' style="width:100%;" name='social_text'><?php echo get_option('social_text', $social_text); ?></textarea>	
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
			
			<tr>
			
			<td style="border:0px;">
				<table>
				<tr>
					<td style="border:0px; width:325px;">
						Footer Contact Enable ? <input name="disable_footer_contact" type="radio" value="0" <?php if(get_option('disable_footer_contact') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
						<input name="disable_footer_contact" type="radio" value="1" <?php if(get_option('disable_footer_contact', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please
					</td>
					</tr>
				
				</table>
			
			</td>
			<td style="border:0px;">
							
				<table>
				
					<tr>
						<td style="border:0px;">Title:</td> 
						<td style="border:0px;"><input type="text" size="20" name="footer_contact_title" value="<?php echo get_option("footer_contact_title", "Contact us"); ?>"/></td> 
					</tr>
					<tr>
						<td style="border:0px;">Subtitle:</td> 
						<td style="border:0px;"><input type="text" size="20" name="footer_contact_subtitle" value="<?php echo get_option("footer_contact_subtitle", "Get in touch with us"); ?>"/></td> 
					</tr>
					<tr>
						<td style="border:0px;">Name:</td> 
						<td style="border:0px;"><input type="text" size="20" name="footer_contact_name" value="<?php echo get_option("footer_contact_name", "name"); ?>"/></td> 
					</tr>
					<tr>
						<td style="border:0px;">Email:</td> 
						<td style="border:0px;"><input type="text" size="20" name="footer_contact_email" value="<?php echo get_option("footer_contact_email", "email address"); ?>"/></td> 
					</tr>
					<tr>
						<td style="border:0px;">Subject:</td> 
						<td style="border:0px;"><input type="text" size="20" name="footer_contact_subject" value="<?php echo get_option("footer_contact_subject", "subject"); ?>"/></td> 
					</tr>
					<tr>
						<td style="border:0px;">Message:</td> 
						<td style="border:0px;"><input type="text" size="20" name="footer_contact_message" value="<?php echo get_option("footer_contact_message", "message"); ?>"/></td> 
					</tr>
					<tr>
						<td style="border:0px;">Send:</td> 
						<td style="border:0px;"><input type="text" size="20" name="footer_contact_send" value="<?php echo get_option("footer_contact_send", "send"); ?>"/></td> 
					</tr>
					
					<tr>
						<td style="border:0px;">Success Message:</td> 
						<td style="border:0px;"><input type="text" size="20" name="footer_contact_success" value="<?php echo get_option("footer_contact_success", "Success, mail sent."); ?>"/></td> 
					</tr>
					
					<tr>
						<td style="border:0px;">Error Message:</td> 
						<td style="border:0px;"><input type="text" size="20" name="footer_contact_error" value="<?php echo get_option("footer_contact_error", "Error! Check Fields"); ?>"/></td> 
					</tr>
					
				</table>
				
			</td>
			</tr>
			</table>
			
			</td>
		</tr>

		<tr class="alternate">
			<td class="column-name" colspan="3">
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="social_text, disable_social, copyright_text, disable_footer_contact, disable_copyright_text, footer_contact_email, footer_contact_success, footer_contact_error, disable_our_pages, footer_contact_send, footer_contact_message, footer_contact_subject, our_pages_title, footer_contact_title, footer_contact_subtitle, footer_contact_name" />
				<input type="submit" name="publish" id="publish" accesskey="p" tabindex="5" class="button-primary" value="Save" />
				</form>
			</td>
		</tr>

	
		</tbody>
	</table>

			
</div>
