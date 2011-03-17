<?php get_header(); ?>
	       
    <div id="bd">

		<div id="yui-main">
			
			<div class="yui-b">

				<div id="featured-products" class="yui-g">
					<div id="slider1" class="sliderwrapper">
						<?php $my_query = new WP_Query('category_name=featured&showposts=10'); ?>
						<?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
						<div class="contentdiv">
						
							<?php if (image_by_attachment(true)) { ?>

								<?php if(function_exists('get_the_image')) { get_the_image('default_size=medium&image_scan=true'); } ?>
										
							<?php } else { ?>		
							
								<div class="featured-content-container">			
								
									<div class="featured-content">
										
										<h2><a href="<?php the_permalink(); ?>" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
		
										<?php the_content(); ?>
									
									</div>
									
								</div>
								
							<?php } ?>											
											
						</div>
						<?php endwhile; ?>
					</div>
				</div>
						
				<div id="paginate-slider1" class="pagination"></div>
				
				<script type="text/javascript">						
					featuredcontentslider.init({
						id: "slider1", //id of main slider DIV
						contentsource: ["inline", ""], //Valid values: ["inline", ""] or ["ajax", "path_to_file"]
						toc: "#increment", //Valid values: "#increment", "markup", ["label1", "label2", etc]
						nextprev: ["&lt;", "&gt;"], //labels for "prev" and "next" links. Set to "" to hide.
						enablefade: [true, 0.2], //[true/false, fadedegree]
						autorotate: [true, 3000], //[true/false, pausetime]
						onChange: function(previndex, curindex){ //event handler fired whenever script changes slide
						//previndex holds index of last slide viewed b4 current (1=1st slide, 2nd=2nd etc)
						//curindex holds index of currently shown slide (1=1st slide, 2nd=2nd etc)
						}
					})						
				</script>

				<?php shopp('catalog','category', 'order=newest&show=1&name=Sunday Messages&load=true'); ?>
				 
				<?php if (shopp('category','hasproducts','load=prices,images')) : ?>
				 
					<?php while(shopp('category','products')) : if(shopp('product','has-specs')) : ?>
						<?php 
							if(!empty($Shopp->Product->specs)):
								$detail_date = ($Shopp->Product->specs[Date]->content);
							endif;
						?>

							<div id="this-weeks-message" class="yui-g">
								
								<div id="this-weeks-message-container" class="highlight">

									<div class="yui-u first">

										<div class="highlight-title">Latest Sunday Message</div>								
										<div class="highlight-message"><a href="<?php shopp('product','url'); ?>"><?php shopp('product','name'); ?></a></div>
										<div class="highlight-date"><?php echo $detail_date; ?></div>

									</div>
		
									<div class="yui-u highlight-buttons">
										<div class="highlight-listen"><a href="<?php shopp('product','url'); ?>"><span class="anchor-text">Listen</span></a></div>
									</div>
									
									<div class="clear"></div>
		
								</div>
							
							</div>
							
						<?php endif; ?>
						
					<?php endwhile; ?>
							 
				<?php endif; ?>	
				
				<div id="recent-sermons" class="yui-g">
					
					<h1>Recent Sermons</h1>
						
					<table>
						<thead>
							<tr>
								<th class="col1" scope="col">Title</th>
								<th class="col2">Speaker</th>
								<th class="col3">Date</th>
							</tr>
						</thead>
						<tbody>

							<?php shopp('catalog','category', 'id=1&show=10&load=true&order=newest&pagination=alpha'); ?>
							 
							<?php if (shopp('category','hasproducts','load=prices,images')) : ?>
							 
								<?php while(shopp('category','products')) : ?>
									<?php 
										if(shopp('product','has-specs')) :
											if(!empty($Shopp->Product->specs)):
												$detail_speaker = ($Shopp->Product->specs[Speaker]->content);
												$detail_date = ($Shopp->Product->specs[Date]->content);
											endif;
										endif; 
									?>
									<tr>
										<td class="product-name"><a href="<?php shopp('product','url'); ?>"><?php shopp('product','name'); ?></a></td>
										<td class="product-speaker"><a href="<?php bloginfo('url'); ?>/?st=shopp&amp;s=<?php echo $detail_speaker; ?>"><?php echo $detail_speaker; ?></a></td>
										<td class="product-date"><?php echo $detail_date; ?></td>
									</tr>
								<?php endwhile; ?>
							 
							<?php endif; ?>				
										
						</tbody>
					
					</table>
					
					<p><a href="<?php bloginfo('url'); ?>/sermons/" class="input">More Sermons</a></p>
					
				</div>
							
			</div><!-- end .yui-b -->
								
		</div><!-- end #yui-main -->
		
		<?php get_sidebar(); ?>
		
		<div class="clear"></div>

	</div><!-- end #bd -->        
        
<?php get_footer(); ?>