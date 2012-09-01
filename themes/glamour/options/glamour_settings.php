<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2>The Glamour General Settings</h2>
	
	<form action="options.php" method="post">
	<?php wp_nonce_field('update-options'); ?>
	
	<div class="tablenav">
		<div class="alignleft actions">
			<input type="submit" name="saving" value="Save All Changes" class="button-secondary" />
		</div>
	</div>
	
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id='normal-sortables' class='meta-box-sortables'>
				<div id="dashboard_right_now" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class='hndle'><span>General Settings</span></h3>
					<div class="inside">

						<div class="table">
							<table>
							
								<tr class="first">
									<td class="first b-posts">Background Color</td>
									<td class="t posts">: <input type="text" name="bg_color" size="10" value="<?php echo get_option('bg_color', 'd6dbdd'); ?>"/></td>
								</tr>
								
								<tr>
									<td class="first b-posts">Background Image :</td>
									<td style="width:300px; padding-top:10px;">
										<input type="text" name="bg_img" id="bg_img" class="upload_input" tabindex="1" size="25" value="<?php echo get_option('bg_img'); ?>" style="float:left;"><a href="media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true" id="bg_img" class="set_input thickbox button" title='Add an Image' onclick="return false;" style="float:left; margin-top:5px;">Upload</a> 
									</td>
								</tr>

							</table>
						</div>
							
							
							
							
							<div class="table">
								<table>
								
									<tr>
										<td class="first b-posts">General Font Family</td>
										<td class="t posts">: <input type="text" name="body_font" size="10" value="<?php echo get_option('body_font', 'Tahoma'); ?>"/></td>
									</tr>
									
									
									
									<tr class="">
										<td class="first b-posts">General Font Size</td>
										<td class="t posts">: <input type="text" name="body_font_size" size="5" value="<?php echo get_option('body_font_size', '13'); ?>"/><font size="1" color="#ccc"><i>.px</i></font></td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">General Font Color</td>
										<td class="t posts">: <input type="text" name="body_font_color" size="10" value="<?php echo get_option('body_font_color', '555555'); ?>"/></td>
									</tr>
									
									
								</table>
							</div>
							
					</div>
				</div>
			</div>							

			<div id='normal-sortables' class='meta-box-sortables'>
				<div id="dashboard_right_now" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class='hndle'><span>Site Logo Settings</span></h3>
					<div class="inside">

					
					
							<div class="table">
								<table>
									<tr class="first">
										<td colspan="2">
											<a href="media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true" id="sitelogo" class="set_input thickbox" title='Add an Image' onclick="return false;" style="background-color:#333; padding:0px; margin-top:10px; margin-bottom:10px; width:252px; height:79px; border:1px solid #ccc; display:table;">
												<span id="sitelogo_preview">
													<img src="<?php echo get_option('sitelogo', get_bloginfo('template_url').'/images/logo.png'); ?>">
												</span>
											</a>
										</td>
									</tr>
									<tr class="first">
										<td colspan="2">
										<a href="media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true" id="sitelogo" class="set_input thickbox button" title='Add an Image' onclick="return false;" style="float:left;">Upload Logo</a> </td>
									</tr>
									
									<tr class="first">
										<td colspan="2">
										<input type="text" name="sitelogo" id="sitelogo" class="upload_input" tabindex="1" size="95" value="<?php echo get_option('sitelogo', get_bloginfo('template_url').'/images/logo.png'); ?>" /> <font size="1" color="#ccc"><i>250x80 (px)</i></font></td>
									</tr>

								</table>
							</div>
					</div>
				</div>
			</div>	

			<div id='normal-sortables' class='meta-box-sortables'>
				<div id="dashboard_right_now" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class='hndle'><span>Cufon Font Settings</span></h3>
					<div class="inside">

							<div class="table">
								<table>
									<tr class="first">
										<td>
											<a href="media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true" id="cufon_font" class="set_input thickbox button" title='Add an Image' onclick="return false;" style="float:left; margin-top:10px;">Upload Font</a>
											
										</td>
									</tr>
									<tr class="first">
										<td>
											<input type="text" name="cufon_font" id="cufon_font" class="upload_input" tabindex="1" size="95" value="<?php echo get_option('cufon_font', get_bloginfo('template_url').'/js/Neo_Sans_Std_400-Neo_Sans_Std_400.font.js'); ?>" />
										</td>
									</tr>
							
									<tr class="first" style="height:50px;">
										<td>
										Enable Cufon Style ? <input name="cufon_active" type="radio" value="0" <?php if(get_option('cufon_active') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
										<input name="cufon_active" type="radio" value="1" <?php if(get_option('cufon_active', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please
										</td>
									</tr>										
								</table>
							</div>
							
							
					</div>
				</div>
			</div>				

			<div id='normal-sortables' class='meta-box-sortables'>
				<div id="dashboard_right_now" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class='hndle'><span>Slider Settings</span></h3>
					<div class="inside">

							<div class="table">
								<table>
									
									<tr class="first" style="height:50px;">
										<td colspan="2">Enable Slider ? <input name="disable_slider" type="radio" value="0" <?php if(get_option('disable_slider') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; <input name="disable_slider" type="radio" value="1" <?php if(get_option('disable_slider', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
									</tr>
									
								</table>
							</div>
		

					</div>
				</div>
			</div>					
			
			<div id='normal-sortables' class='meta-box-sortables'>
				<div id="dashboard_right_now" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class='hndle'><span>Search Settings</span></h3>
					<div class="inside">

							<div class="table">
								<table>
																			
									<tr class="" style="height:50px;">
										<td colspan="2">Enable Page Search ? <input name="disable_page_search" type="radio" value="0" <?php if(get_option('disable_page_search') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; <input name="disable_page_search" type="radio" value="1" <?php if(get_option('disable_page_search', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
									</tr>
									
								</table>
							</div>
		

					</div>
				</div>
			</div>

			<div id='normal-sortables' class='meta-box-sortables'>
				<div id="dashboard_right_now" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class='hndle'><span>Portfolio Styles Settings</span></h3>
					<div class="inside">

							<div class="table">
								<table>
									<tr class="first" style="height:50px;">
										<td colspan="2">
											<strong>Portfolios General Settings</strong>
										</td>
									</tr>
									
									<tr class="" style="height:50px;">
										<td colspan="2">Enable Details Button ? <input name="disable_portfolio_details" type="radio" value="0" <?php if(get_option('disable_portfolio_details') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; <input name="disable_portfolio_details" type="radio" value="1" <?php if(get_option('disable_portfolio_details', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Details Button Title</td>
										<td class="t posts">: <input type="text" name="portfolio_details_title" size="15" value="<?php echo get_option('portfolio_details_title', 'DETAILS'); ?>"/></td>
									</tr>
								
								</table>
							</div>
							

							<div class="table">
								<table>
									<tr class="first" style="height:50px;">
										<td colspan="2">
											<strong>Portfolio Style 1 Settings</strong>
										</td>
									</tr>
									
															
									<tr class="">
										<td class="first b-posts">Thumbnail Size</td>
										<td class="t posts">: width : <input name="portfolio1_thumb_width" type="text" size="5" id="portfolio1_thumb_width" value="<?php echo get_option('portfolio1_thumb_width', '270'); ?>" class="regular-text" />	<font size="1" color="#ccc"><i>.px</i></font> height : <input name="portfolio1_thumb_height" type="text" size="5" id="portfolio1_thumb_height" value="<?php echo get_option('portfolio1_thumb_height', '150'); ?>" class="regular-text" />	<font size="1" color="#ccc"><i>.px</i></font></td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Item Limit </td>
										<td class="t posts">: <input name="portfolio1_limit" type="text" id="portfolio1_limit" value="<?php echo get_option('portfolio1_limit', '9'); ?>" size="3"/></td>
									</tr>

									<tr class="">
										<td class="first b-posts">Columns</td>
										<td class="t posts">: <input name="portfolio1_columns" type="text" id="portfolio1_columns" value="<?php echo get_option('portfolio1_columns', '3'); ?>" size="3" /></td>
									</tr>
									
								</table>
							</div>
															
							
							<div class="table">
								<table>
									<tr class="first" style="height:50px;">
										<td colspan="2">
											<strong>Portfolio Style 2 Settings</strong>
										</td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Thumbnail Size</td>
										<td class="t posts">: width : <input name="portfolio2_thumb_width" type="text" size="5" id="portfolio2_thumb_width" value="<?php echo get_option('portfolio2_thumb_width', '500'); ?>" class="regular-text" />	<font size="1" color="#ccc"><i>.px</i></font> height : <input name="portfolio2_thumb_height" type="text" size="5" id="portfolio2_thumb_height" value="<?php echo get_option('portfolio2_thumb_height', '200'); ?>" class="regular-text" />	<font size="1" color="#ccc"><i>.px</i></font></td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Item Limit </td>
										<td class="t posts">: <input name="portfolio2_limit" type="text" id="portfolio2_limit" value="<?php echo get_option('portfolio2_limit', '10'); ?>" size="3"/></td>
									</tr>
									
								</table>
							</div>	


							<div class="table">
								<table>
									<tr class="first" style="height:50px;">
										<td colspan="2">
											<strong>Portfolio Style 3 Settings</strong>
										</td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Thumbnail Size</td>
										<td class="t posts">: width : <input name="portfolio3_thumb_width" type="text" size="5" id="portfolio3_thumb_width" value="<?php echo get_option('portfolio1_thumb_width', '270'); ?>" class="regular-text" />	<font size="1" color="#ccc"><i>.px</i></font> height : <input name="portfolio3_thumb_height" type="text" size="5" id="portfolio3_thumb_height" value="<?php echo get_option('portfolio3_thumb_height', '150'); ?>" class="regular-text" />	<font size="1" color="#ccc"><i>.px</i></font></td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Item Limit </td>
										<td class="t posts">: <input name="portfolio3_limit" type="text" id="portfolio3_limit" value="<?php echo get_option('portfolio3_limit', '10'); ?>" size="3"/></td>
									</tr>

									<tr class="">
										<td class="first b-posts">Columns</td>
										<td class="t posts">: <input name="portfolio3_columns" type="text" id="portfolio3_columns" value="<?php echo get_option('portfolio3_columns', '2'); ?>" size="3" /></td>
									</tr>
									
								</table>
							</div>	
			</div>
				</div>
			</div>	
			
			<div id='normal-sortables' class='meta-box-sortables'>
				<div id="dashboard_right_now" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class='hndle'><span>Gallery Page Settings</span></h3>
					<div class="inside">
					
					
					
							<div class="table">
								<table>
									<tr class="first" style="height:50px;">
										<td colspan="2">
											<strong>Gallery Settings</strong>
										</td>
									</tr>
									
															
									<tr class="">
										<td class="first b-posts">Thumbnail Size</td>
										<td class="t posts">: width : <input name="gallery_thumb_width" type="text" size="5" id="gallery_thumb_width" value="<?php echo get_option('gallery_thumb_width', '270'); ?>" class="regular-text" />	<font size="1" color="#ccc"><i>.px</i></font> height : <input name="gallery_thumb_height" type="text" size="5" id="gallery_thumb_height" value="<?php echo get_option('gallery_thumb_height', '150'); ?>" class="regular-text" />	<font size="1" color="#ccc"><i>.px</i></font></td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Item Limit </td>
										<td class="t posts">: <input name="gallery_limit" type="text" id="gallery_limit" value="<?php echo get_option('gallery_limit', '9'); ?>" size="3"/></td>
									</tr>

									<tr class="">
										<td class="first b-posts">Columns</td>
										<td class="t posts">: <input name="gallery_columns" type="text" id="gallery_columns" value="<?php echo get_option('gallery_columns', '3'); ?>" size="3" /></td>
									</tr>
									
									<tr class="" style="height:50px;">
										<td colspan="2">Enable Gallery Details Button ? <input name="disable_gallery_details" type="radio" value="0" <?php if(get_option('disable_gallery_details', 0) == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; <input name="disable_gallery_details" type="radio" value="1" <?php if(get_option('disable_gallery_details') == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Gallery Details Button Title</td>
										<td class="t posts">: <input type="text" name="gallery_details_title" size="15" value="<?php echo get_option('gallery_details_title', 'DETAILS'); ?>"/></td>
									</tr>
								
									
								</table>
							</div>								
		
					</div>
				</div>
			</div>	
			
			<div id='normal-sortables' class='meta-box-sortables'>
				<div id="dashboard_right_now" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class='hndle'><span>Blog Page Settings</span></h3>
					<div class="inside">

					
							<div class="table">
								<table>

									<tr class="first" style="height:50px;">
										<td colspan="2">Enable Blog Details Button ? <input name="disable_blog_details" type="radio" value="0" <?php if(get_option('disable_blog_details') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; <input name="disable_blog_details" type="radio" value="1" <?php if(get_option('disable_blog_details', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please</td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Blog Details Button Title</td>
										<td class="t posts">: <input type="text" name="blog_details_title" size="15" value="<?php echo get_option('blog_details_title', 'DETAILS'); ?>"/></td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Thumbnail Size</td>
										<td class="t posts">: width : <input name="blog_thumb_width" type="text" size="5" id="blog_thumb_width" value="<?php echo get_option('blog_thumb_width', '620'); ?>" class="regular-text" />	<font size="1" color="#ccc"><i>.px</i></font> height : <input name="blog_thumb_height" type="text" size="5" id="blog_thumb_height" value="<?php echo get_option('blog_thumb_height', '200'); ?>" class="regular-text" />	<font size="1" color="#ccc"><i>.px</i></font></td>
									</tr>
									
									<tr class="">
										<td class="first b-posts">Item Limit </td>
										<td class="t posts">: <input name="blog_limit" type="text" id="blog_limit" value="<?php echo get_option('blog_limit', '10'); ?>" size="3"/></td>
									</tr>

									<tr class="">
										<td class="first b-posts">Columns</td>
										<td class="t posts">: <input name="blog_columns" type="text" id="blog_columns" value="<?php echo get_option('blog_columns', '1'); ?>" size="3" /></td>
									</tr>
									
								</table>
							</div>
															
					</div>
				</div>
			</div>					
		</div>
		<div class="clear"></div>
	</div>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="gallery_details_title, disable_gallery_details, gallery_columns, gallery_limit, gallery_thumb_width, gallery_thumb_height, bg_color, bg_img, blog_limit, blog_columns, blog_thumb_height, blog_thumb_width, blog_details_title, disable_blog_details, portfolio1_limit, portfolio2_limit, portfolio3_limit, portfolio3_thumb_height, portfolio3_columns, portfolio3_thumb_width, portfolio1_columns, portfolio2_thumb_height, portfolio2_thumb_width, portfolio1_thumb_width, portfolio1_thumb_height, portfolio_details_title, disable_portfolio_details, page_search_subtitle, disable_page_search, ticker_limit, ticker_title, ticker_category_id, disable_blogticker, slider_height, fade_speed, delay_time, disable_slider, cufon_active, cufon_font, sitelogo, body_font, body_font_size, body_font_color, menu_font_size, submenu_font_size, subtitle_font_size, menu_a, menu_a_hover, submenu_a, submenu_a_hover" />
	
	<div class="tablenav">
		<div class="alignleft actions">
			<input type="submit" name="saving" value="Save All Changes" class="button-secondary" />
		</div>
	</div>
	
	</form>	
</div>