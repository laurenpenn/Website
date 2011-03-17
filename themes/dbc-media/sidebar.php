<div id="sidebar" class="yui-b">
	<div id="sidebar-container">
		<?php if ( is_home() ) { ?>
		
			<img src="<?php bloginfo('template_directory'); ?>/images/media-ministry-logo.png" alt="Media Ministry" id="media-ministry-logo" />
		
			<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Home Sidebar') ) : else : endif; ?>
				
		<?php }?>
					
		<?php if ( is_page() || is_category() || is_search()  ) { ?>
		
			<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Page Sidebar') ) : else : ?>
					
				<div class="box-container-demo ui-helper-reset <?php dd_corners(); ?>">
			
					<div class="section-header ui-widget-header ui-helper-reset <?php dd_corners(); ?>">Page Sidebar - <a href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?show=&sidebar=sidebar-4">Add</a></div>
					
					<p class="demo-widget-link"><a href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?show=&sidebar=sidebar-4">click here to change this</a></p>
				 
				 </div><!-- end .box-container --> 
				
			<?php endif; ?>
				
		<?php } ?>
		
		<?php if ( is_single()  ) { ?>
		
			<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Post Sidebar') ) : else : ?>
				
				<div class="box-container-demo ui-helper-reset <?php dd_corners(); ?>">
			
					<div class="section-header ui-widget-header ui-helper-reset <?php dd_corners(); ?>">Post Sidebar - <a href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?show=&sidebar=sidebar-5">Add</a></div>
					
					<p class="demo-widget-link"><a href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?show=&sidebar=sidebar-5">click here to change this</a></p>
				 
				 </div><!-- end .box-container -->
				 	 
			<?php endif; ?>
				
		<?php } ?>
	</div>
</div>