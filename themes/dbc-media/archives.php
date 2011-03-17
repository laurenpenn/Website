<?php
/*
Template Name: Archives Page
*/
?>


<?php get_header(); ?>
		       
        <div id="bd">
   
			<div id="yui-main">
				
				<div class="yui-b">
				
					<div id="page" class="yui-g">
	
						<?php $catid = $wpdb->get_var("SELECT term_ID FROM $wpdb->terms WHERE name='Asides'"); ?>
	
						<?php $catid2 = $wpdb->get_var("SELECT term_ID FROM $wpdb->terms WHERE name='Featured'"); ?>
	
	            		<ul class="archives">
							<?php wp_list_categories('title_li=&sort_column=name&show_count=1&show_last_updated=1&use_desc_for_title=1&exclude=' .$catid. ','  .$catid2. '') ?> 
						</ul>
						
						<h3 class="mast">By Month</h3>
	            		<ul class="archives">
							<?php wp_get_archives('type=monthly&show_post_count=1') ?>
						</ul>
						
						<h3 class="mast">By Tag</h3>
	            		<?php wp_tag_cloud('format=list&smallest=12&largest=12&unit=px'); ?>
							
					</div><!-- end #page -->
					
				</div><!-- end .yui-b -->
				
			</div><!-- end #yui-main -->
			
			<ul class="yui-b">
			
				<?php get_sidebar(); ?>

			</ul><!-- end .yui-b -->  	

   	 	</div><!-- end #bd -->        
        
<?php get_footer(); ?>     

