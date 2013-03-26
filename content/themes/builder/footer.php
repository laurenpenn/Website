        <!--FOOTER-->
        <?php  global $data;?>
        <?php if($data['show_twitter_feed'] == true ) { ?>
        <div class="twitter-block" style="background-color:#3a3a3a; margin-top:60px; padding-top:15px; padding-bottom:15px; border-top:1px solid #f4f4f4;">
            <div class="container">
                <div class="row">
                    <div class="span3">
                        <h6 style="color:#fff; font-weight:600; text-transform:uppercase !important"><?php echo stripslashes($data['footer_social_tw_header']) ?></h6>
                        <p style="margin-bottom:0px !important; font-size:12px; line-height:16px; color:#a8a8a8;"><?php echo stripslashes($data['footer_social_tw_descr']) ?></p>
                    </div>
                    <br class="visiblephone">
                    <div class="span9">
                        <div class="well" style=" box-shadow:none !important; margin-bottom:0px; background:#303030; border:0px; border-radius:0px !important">
                            <div class="tweet"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="footer">
            <div class="container">
                <div class="row">
                	<div class="span3 soc_icons">
                    <div style="">
                    	<img src="<?php echo stripslashes($data['footer_logo']) ?>" alt="<?php bloginfo('name'); ?>" /><br> 
                        <?php if($data['footer_social_fl']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_fl']) ?>" target="_blank"><div class="icon_flickr"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_g']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_g']) ?>" target="_blank"><div class="icon_google"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_fb']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_fb']) ?>" target="_blank"><div class="icon_facebook"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_pi']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_pi']) ?>" target="_blank"><div class="icon_pi"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_tw']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_tw']) ?>" target="_blank"><div class="icon_t"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_yt']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_yt']) ?>" target="_blank"><div class="icon_youtube"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_in']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_in']) ?>" target="_blank"><div class="icon_in"></div></a>
                        <?php } ?>
                        
                        
						<?php if($data['footer_social_da']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_da']) ?>" target="_blank"><div class="icon_da"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_skype']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_skype']) ?>" target="_blank"><div class="icon_skype"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_icq']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_icq']) ?>" target="_blank"><div class="icon_icq"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_envato']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_envato']) ?>" target="_blank"><div class="icon_envato"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_forrst']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_forrst']) ?>" target="_blank"><div class="icon_forrst"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_bing']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_bing']) ?>" target="_blank"><div class="icon_bing"></div></a>
                        <?php } ?>
                        <?php if($data['footer_social_myspace']) { ?>
                            <a href="<?php echo stripslashes($data['footer_social_myspace']) ?>" target="_blank"><div class="icon_myspace"></div></a>
                        <?php } ?>
                    </div>
                    <br class="visiblephone"><br class="visiblephone"><br class="visiblephone"><br class="visiblephone">
                    </div>
                    <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Footer") ) : ?>                
                    <?php endif; ?> 
                </div>
            </div>
        </div>
        <?php if($data['bottom_line_show'] == true ) { ?>
        <div class="bottom_line" style="border-top:1px solid #444444;">
            <div class="container">
            	<div class="row">
                    <div class="span6">
                        <span style="font-size:11px;"><?php echo stripslashes($data['bottom_line_text']) ?></span>
                    </div>
                    <div class="span6">
                        <span style="font-size:11px;" class="pull-right visible-desktop"><?php wp_nav_menu( array('theme_location' => 'secondary_menu', 'menu_class' => 'unstyled footer_menu')); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <!--/FOOTER-->
        </div>
    
    		<!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <?php wp_enqueue_script( 'jquery' ); ?>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.tweet.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/google-code-prettify/prettify.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap.min.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.easing.1.3.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/superfish-menu/superfish.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.nivo.slider.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.prettyPhoto.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jflickrfeed.min.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/testimonialrotator.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.waitforimages.js"></script>
    	<script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.isotope.min.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/custom.js"></script>
        
        <!-- FOR CONTACT PAGE -->
        <script type="text/javascript">
			jQuery.noConflict()(function($){
			$(document).ready(function ()
			{ // after loading the DOM
				$("#ajax-contact-form").submit(function ()
				{
					// this points to our form
					var str = $(this).serialize(); // Serialize the data for the POST-request
					$.ajax(
					{
						type: "POST",
						url: '<?php echo get_template_directory_uri(); ?>/contact.php',
						data: str,
						success: function (msg)
						{
							$("#note").ajaxComplete(function (event, request, settings)
							{
								if (msg == 'OK')
								{
									result = '<div class="notification_ok">Message was sent to website administrator, thank you!</div>';
									$("#fields").hide();
								}
								else
								{
									result = msg;
								}
								$(this).html(result);
							});
						}
					});
					return false;
				});
			});
			});
		</script>
        <script>
		/***************************************************
					TWITTER FEED
		***************************************************/
		
		jQuery.noConflict()(function($){
		$(document).ready(function() {  
		
			  $(".tweet").tweet({
					count: 1,
					username: '<?php echo stripslashes($data['footer_social_tw_user']) ?>',
					loading_text: "loading twitter..."      
				});
		});
		});
		</script>
        
	</body>
    <?php wp_footer(); ?>
</html>