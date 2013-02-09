			<?php if (get_option('disable_some_text', 1) == '1') : ?>
			
			
			<!-- begin intro content -->
			<div class="intro_content clearfix">

				<?php
					$some_text = '<div class="intro_text"><h3>WOULD YOU LIKE WORDPRESS ?</h3>WordPress started in 2003 with a single bit of code to enhance the typogra phy of everyday writing and with fewer users than you can count on  your fingers and toes.  Since then it has grown to be the largest self-hosted blogging tool in the world, used on millions of sites and seen by tens of millions of people every day.</div><div class="intro_image"><img src="'.get_bloginfo('template_url').'/images/readytoget.png" alt="" align="right"/></div>';
					echo get_option('some_text', $some_text);						
				?>
				
			</div>
			<!-- end intro content -->
			<?php endif; ?>
			
			
			
			<?php if (get_option('disable_widgets', 1) == '1') : ?>
		
			
			<!-- begin some widgets -->
			<div class="some_widgets">
	
				<!-- begin some widget 1 -->	
				<div class="some_widgets_item">
					
					<!-- begin some widget text -->
					<div class="some_widget_text">
					
						
						<?php $widgets1 = '<img src="'.get_bloginfo('template_url').'/images/icons/some_icon1.png" class="some_widget_image" alt=""/>
						<h5>WHERE WE USE IT</h5>
						<p>
						Lorem Ipsum is simply dummy text of the printing and typeses industry. 
						</p>'; ?>
						<?php echo get_option('widgets1', $widgets1); ?>
						
						
					</div>
					<!-- end some widget text -->
					
				</div>
				<!-- end some widget 1 -->	
				
				
				
				<!-- begin some widget 2 -->	
				<div class="some_widgets_item">
					
					<!-- begin some widget text -->
					<div class="some_widget_text">
					
						<?php $widgets2 = '<img src="'.get_bloginfo('template_url').'/images/icons/some_icon1.png" class="some_widget_image" alt=""/>
						<h5>WHAT IS LOREM IPSUM</h5>
						<p>
						Lorem Ipsum has been the industrys standard dummy text ever since the 1500s.
						</p>'; ?>
						<?php echo get_option('widgets2', $widgets2); ?>
						
					</div>
					<!-- end some widget text -->
					
				</div>
				<!-- end some widget 2 -->	
				
				
				
				<!-- begin some widget 3 -->	
				<div class="some_widgets_item_last">
					
					<!-- begin some widget text -->
					<div class="some_widget_text">
					
						<?php $widgets3 = '<img src="'.get_bloginfo('template_url').'/images/icons/some_icon1.png" class="some_widget_image" alt=""/>
						<h5>GRAPH OF MONTH</h5>
						<p>		
						It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum
						</p>'; ?>
						<?php echo get_option('widgets3', $widgets3); ?>
						
					</div>
					<!-- end some widget text -->
					
				</div>
				<!-- end some widget 3 -->	
				
			</div>
			<!-- end some widgets -->
			
			<div class="cleardiv"></div>
			<?php endif; ?>
