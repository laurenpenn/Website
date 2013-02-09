<?php /* Multimedia Theme*/ ?>

<!-- Footer Back-->
<div id="footer-back"></div>
<div class="container_16">
  <div id="footer-register" class="grid_8">
    <p>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'footer_reg') ) : ?>
	<?php get_option_tree( 'footer_reg', '', 'true' ); ?>
	<?php else : ?>
    Copright © 2010 iamthemes.com. All rights reserved. W3C standart web site valid xhtml and css
	<?php endif; endif; ?>
    </p>
    <ul>
	<?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'footer_link') ) : ?>
	<?php get_option_tree( 'footer_link', '', 'true' ); ?>
	<?php else : ?>
    <li><a href="#">Home Page</a></li>
    <li><a href="#">Term Multimedia</a></li>
    <li><a href="#">Contact</a></li>
	<?php endif; endif; ?>
    </ul>
  </div>
  
  <div id="footer-social" class="grid_8">
    <ul>
    <?php if ( function_exists( 'get_option_tree') ) : if( get_option_tree( 'footer_social') ) : ?>
	<?php get_option_tree( 'footer_social', '', 'true' ); ?>
	<?php else : ?>
      <li><a href="#"><img src="<?php bloginfo('template_directory'); ?>/image/theme/twitter.png" alt="" /></a></li>
      <li><a href="#"><img src="<?php bloginfo('template_directory'); ?>/image/theme/facebook.png" alt="" /></a></li>
      <li><a href="#"><img src="<?php bloginfo('template_directory'); ?>/image/theme/rss.png" alt="" /></a></li>
      <?php endif; endif; ?>
    </ul>
  </div>
</div>

<script> 
$("#allpage-login-top").pageSlide({ width: "334px", direction: "left"});
$("#allpage-signup-top").pageSlide({ width: "334px", direction: "right" });
$("#allpage-search-top").pageSlide({ width: "350px", direction: "left", modal: true });
$("#homepage-login-button").pageSlide({ width: "350px", direction: "left" });
$("#homepage-signup-button").pageSlide({ width: "350px", direction: "right" });
</script>
<?php wp_footer(); ?>
</body> 
</html>
