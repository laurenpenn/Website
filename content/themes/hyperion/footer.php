</div> <!--  END CONTENT  -->
</div> <!--  END CONTENT WRAPPER  -->


<div id="footer-wrap">
<div id="content-bottom"></div><!--  BOTTOM CONTENT SHADOW  -->
    <div id="footer"><!--  BEGIN FOOTER  -->
    
	<?php get_sidebar('footer'); ?>

<?php
$rss = stripslashes(get_option('hp_feedburner'));
$twitter = stripslashes(get_option('hp_twitter'));
$facebook = stripslashes(get_option('hp_facebook'));
$linkedin = stripslashes(get_option('hp_linkedin'));
$flickr = stripslashes(get_option('hp_flickr'));
$behance = stripslashes(get_option('hp_behance'));
$footertext =stripslashes(get_option('hp_footer_text'));
$ga = stripslashes(get_option('hp_ga_code'));
?>

    <div class="footer-info-area">
        
        <h3><?php echo stripslashes(get_option('hp_footer_social')); ?></h3>
        <p><?php echo stripslashes(get_option('hp_footer_social_p')); ?></p>
        
        <ul class="social-bookmarks">
            <?php if ($rss) : ?><li class="rss"><a href="<?php echo $rss ?>">Subscribe to RSS Feed</a></li><?php endif; ?>
            <?php if ($twitter) : ?><li class="twitter"><a href="<?php echo $twitter ?>">Twitter</a></li><?php endif; ?>
            <?php if ($facebook) : ?><li class="facebook"><a href="<?php echo $facebook ?>">Facebook</a></li><?php endif; ?>
            <?php if ($linkedin) : ?><li class="linkedin"><a href="<?php echo $linkedin ?>">LinkedIn</a></li><?php endif; ?>
            <?php if ($flickr) : ?><li class="flickr"><a href="<?php echo $flickr ?>">Flickr</a></li><?php endif; ?>
            <?php if ($behance) : ?><li class="behance"><a href="<?php echo $behance ?>">Behance</a></li><?php endif; ?>
        </ul>
        
        <a class="contact-button" href="<?php echo stripslashes(get_option('hp_footer_formurl',true)); ?>"><strong><?php echo stripslashes(get_option('hp_cf_button_big')); ?></strong><span><?php echo stripslashes(get_option('hp_cf_button_small')); ?></span></a>
                
    </div>   
            
    </div><!--  END FOOTER WIDGETS AND INFO  -->
<div class="content-topper"></div><!--  TOP CONTENT SHADOW  -->    
</div><!--  END FOOTER WRAP  -->

<div id="footer-bottom">
	<div class="bottom">
    	<div class="bottom-left">
        <?php if ($footertext) : ?>
		<p><?php echo $footertext; ?></p>
        <?php else : ?>
        <p>Copyright &copy; 2010 <a href="http://digitonik.com/">Digitonik</a>, All rights Reserved. Infused with valid <a href="http://validator.w3.org/check/referer">XHTML</a>. Yay!</p>
        <?php endif; ?>
        </div>
        
        <div class="bottom-right">
        <p><a href="#headerwrap" class="anchorLink toplink">Top</a></p>
        </div>
	</div>
</div>
<?php if ($ga) : ?><?php echo $ga ?><?php endif; ?>
<?php wp_footer(); ?>

</body>
</html>
