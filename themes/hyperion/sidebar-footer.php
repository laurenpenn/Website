<div class="footer-widgets"> 
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget Area') ) : ?>
		
        <div class="footer-widget">
		<h3>Widget Ready</h3>
        <p>This theme has 3 different widget areas for you to choose.</p>
        <p>This is the area for your footer widgets and everyone knows that widgets are super fun.</p>
        <p><img src="http://themes.digitonik.com/img/widget-ready.png" alt="Widget Ready Footer"/></p>
		</div>
        
        <div class="footer-widget">
        <h3>Widget Areas</h3>
        <p>Widget areas allow you to easily add or change content without editing any code.</p>
        <p>Drag widgets from the admin area to use them.</p>
        </div> 
        
        <div class="footer-widget">
		<h3>Meta</h3>
		<ul>
		<?php wp_register(); ?>
		<li><?php wp_loginout(); ?></li>
		<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Strict">Valid XHTML</a></li>
		<li><a href="http://wordpress.org/">WordPress</a></li>
		<?php wp_meta(); ?>
		</ul>
        </div>           
	<?php endif; ?>
 </div>			