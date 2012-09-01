<?php if (get_option('disable_slider', 1) == '1') : ?>
<!-- begin banner container -->
<div class="banner_container">
	
	<div class="slider_mask_top"></div><!-- slider top corners effect -->
	
	<!-- begin banner holder -->
	<div class="banner_holder">
		
		<!-- begin anythingSlider -->
		<div class="anythingSlider">
			
			<!-- begin slider items -->
			<div class="wrapper">
				<ul>
			   <?php
				global $wpdb;
				$table_name = $wpdb->prefix . "glamour_slider"; // select the slider database!
					
				if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
					include (TEMPLATEPATH . '/includes/get_database.php');
				}
				
				$slider_datas = $wpdb->get_results("SELECT * FROM $table_name ORDER BY orderby");
					$check = 0;
				foreach ($slider_datas as $data) {
				?>
				<li><?php if($data->url) { ?><a href="<?php echo $data->url;?>" target="<?php if($data->target) { echo $data->target; } else { echo "_self"; } ;?>"><?php } ?><img src="<?php echo $data->src;?>" alt=""/><?php if($data->url) { ?></a><?php } ?></li>
				<?php $check++; } ?>
				</ul>        
			</div>
			<!-- begin slider items -->
			 
		</div> 
		<!-- end anythingSlider -->

		</div>
		<!-- end banner holder -->
	
		<div class="slider_mask_bottom"></div><!-- slider bottom corners effect -->
				
</div>
<!--end banner container -->

<div class="cleardiv"></div>

<?php endif; ?>