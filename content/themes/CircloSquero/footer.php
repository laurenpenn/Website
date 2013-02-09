<?php if (get_option_tree( 'twitter_footer' ) == 'Yes') { ?>    
    <div id="twitter_widget">
        <div id="twitterSets1">
            <ul id="twitter_update_list"></ul>
        </div>
        <div id="twitterSets2">
            <div id="TWbutton"><a href="http://twitter.com/<?php echo get_option_tree( 'twitter_username' ); ?>" id="tw_button"><div><?php echo get_option_tree( 't_all_tweets' ); ?></div></a></div>
        </div>
    </div>
            <?php   };?>
    <div id="Footer">
          <div id="<?php if (get_option_tree( 'twitter_footer' ) == 'Yes') { echo 'FooterSeparator' ;} else { echo 'FooterSeparatorNT';}; ?>" ></div>     
        <div id="FooterWrap">
            <div id="FooterWidgetsLeft">
                <ul>
                <?php if ( !function_exists('dynamic_sidebar')  
                || !dynamic_sidebar( 'Footer Widgets Left' ) ) : ?>  
                <h2>About</h2>  
                <p>This is the 'Footer Widgets Left' widget section, add some widgets to change it.</p>  
                <?php endif; ?>
                </ul>
            </div>
            <div id="FooterWidgetsCenter">
                <ul>
                <?php if ( !function_exists('dynamic_sidebar')  
                || !dynamic_sidebar( 'Footer Widgets Center' ) ) : ?>  
                <h2>About</h2>  
                <p>This is the 'Footer Widgets Center' widget section, add some widgets to change it.</p>  
                <?php endif; ?>
                </ul>
            </div>
            <div id="FooterWidgetsRight">
                <ul>
                <?php if ( !function_exists('dynamic_sidebar')  
                || !dynamic_sidebar( 'Footer Widgets Right' ) ) : ?>  
                <h2>About</h2>  
                <p>This is the 'Footer Widgets Right' widget section, add some widgets to change it.</p>  
                <?php endif; ?>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
    <div id="footer2">
        <div id="footer2wrap">
            <div id="copyright">
            <?php echo get_option_tree( 'copyright' );  ?>    
            </div>
            <div id="Social_Networks">
                <?php if (get_option_tree( 'cs_stumble' )) { ?>
                    <a href="http://www.stumbleupon.com/stumbler/<?php echo get_option_tree( 'cs_stumble' ); ?>" target="_blank" ><img class="socialicons" src="<?php bloginfo('template_url'); ?>/images/social_networks/stumbleupon.jpg" alt="stumbleupon" /></a>
                <?php }; ?>  
                
                <?php if (get_option_tree( 'cs_digg' )) { ?>
                    <a href="http://www.digg.com/<?php echo get_option_tree( 'cs_digg' ); ?>" target="_blank" ><img class="socialicons" src="<?php bloginfo('template_url'); ?>/images/social_networks/digg.jpg" alt="digg" /></a>
                <?php }; ?>
                
                <?php if (get_option_tree( 'cs_youtube' )) { ?>
                    <a href="http://www.youtube.com/<?php echo get_option_tree( 'cs_youtube' ); ?>" target="_blank" ><img class="socialicons" src="<?php bloginfo('template_url'); ?>/images/social_networks/youtube.jpg" alt="youtube" /></a>
                <?php }; ?>                

                <?php if (get_option_tree( 'cs_vimeo' )) { ?>
                    <a href="http://www.vimeo.com/<?php echo get_option_tree( 'cs_vimeo' ); ?>" target="_blank" ><img class="socialicons" src="<?php bloginfo('template_url'); ?>/images/social_networks/vimeo.jpg" alt="vimeo" /></a>
                <?php }; ?>
                
                <?php if (get_option_tree( 'cs_facebook' )) { ?>
                    <a href="<?php echo get_option_tree( 'cs_facebook' ); ?>" target="_blank" ><img class="socialicons" src="<?php bloginfo('template_url'); ?>/images/social_networks/facebook.jpg" alt="facebook" /></a>
                <?php }; ?>
                
                <?php if (get_option_tree( 'cs_twitter' )) { ?>
                    <a href="http://www.twitter.com/<?php echo get_option_tree( 'cs_twitter' ); ?>" target="_blank" ><img class="socialicons" src="<?php bloginfo('template_url'); ?>/images/social_networks/twitter.jpg" alt="twitter" /></a>
                <?php }; ?>
            </div>
            <div class="clear"> </div>
             <script type="text/javascript">
		jQuery(document).ready(function(){
                    jQuery('.socialicons').fadeTo(0, 0.5);
                    jQuery('.socialicons').hover(function(){
                        jQuery(this).stop().fadeTo(300, 1);
                        }, function() {
                        jQuery(this).stop().fadeTo(500, 0.5);
                    });
                });
	</script> 
        </div>
    </div>

</div>

      <script type="text/javascript">
         jQuery(document).ready(function() {
            GetTwitterFeedIncRT('<?php echo get_option_tree( "cs_twitter" ); ?>', 1, 'twitter_update_list', 0);
	 });
      </script>


   <script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("a[rel^='prettyPhoto']").prettyPhoto({
                    theme: 'facebook'
                    });
                });
	</script> 
<?php wp_footer() ?>
</body>
</html>
