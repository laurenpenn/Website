<?php
/*
Template Name: Shopp Page
*/
?>

<?php get_header(); ?>
	       
    <div id="bd" class="single">

		<div id="yui-main">
			
			<div class="yui-b">
			
				<div id="post" class="yui-g">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
													
					<div id="post-<?php the_ID(); ?>">
								
						<div class="entry">
																			
							<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
						
							<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
						
						</div><!-- end .entry -->

						<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
						
						<?php if (function_exists('sharethis_button')) { ?> <div class="share-this"><?php sharethis_button(); ?></div> <?php } ?>
																
					</div><!-- end .box-container -->
					
				</div><!-- end #post -->
				
			</div><!-- end .yui-b -->
			
		</div><!-- end #yui-main -->
		
		<div id="sidebar" class="yui-b">
			
			<div id="sidebar-container">
			
				<?php if(shopp('product','found')): ?>
				
				<?php if(!empty($Shopp->Product->specs)): ?>
				<h2>Details</h2>
				<ul class="postmetadata">

					<?php 
						if(!empty($Shopp->Product->specs)):
							$detail_speaker = ($Shopp->Product->specs[Speaker]->content);
							$detail_author = ($Shopp->Product->specs[Author]->content);
							$detail_date = ($Shopp->Product->specs[Date]->content);
							$detail_reference = ($Shopp->Product->specs[Reference]->content);
							$detail_product_id = ($Shopp->Product->specs[ID]->content);
						endif;
					?>
					<?php if(!empty($Shopp->Product->specs[Speaker]->content)) : ?><li><strong>Speaker:</strong> <a href="<?php bloginfo('url'); ?>/?st=shopp&amp;s=<?php echo $detail_speaker; ?>"><?php echo $detail_speaker; ?></a></li><?php endif; ?>
					<?php if(!empty($Shopp->Product->specs[Author]->content)) : ?><li><strong>Author:</strong> <a href="<?php bloginfo('url'); ?>/?st=shopp&amp;s=<?php echo $detail_author; ?>"><?php echo $detail_author; ?></a></li><?php endif; ?>
					<?php if(!empty($Shopp->Product->specs[Date]->content)) : ?><li><strong>Date:</strong> <?php echo $detail_date; ?></li><?php endif; ?>
					<?php if(!empty($Shopp->Product->specs[Reference]->content)) : ?><li><strong>Reference:</strong> <?php echo $detail_reference; ?></li><?php endif; ?>
					<?php if(!empty($Shopp->Product->specs[ID]->content)) : ?><li><strong>Product ID:</strong> <?php echo $detail_product_id; ?></li><?php endif; ?>
					
				</ul>

				<?php endif; ?>
									
				<h2>Purchase</h2>
				
				<?php if (shopp('product','onsale')): ?>
					<p class="original price"><?php shopp('product','price'); ?></p>
					<p class="sale price"><?php shopp('product','saleprice'); ?></p>
					<?php if (shopp('product','has-savings')): ?>
						<p class="savings">You save <?php shopp('product','savings'); ?> (<?php shopp('product','savings','show=%'); ?>)!</p>
					<?php endif; ?>
				<?php else: ?>
					<p class="price"><?php shopp('product','price'); ?></p>
				<?php endif; ?>
				
				<?php if (shopp('product','freeshipping')): ?>
				<p class="freeshipping">Free Shipping!</p>
				<?php endif; ?>

				<div>
					<form action="<?php shopp('cart','url'); ?>" method="post" class="shopp product">
						<?php if(shopp('product','has-variations')): ?>
						<ul class="variations">
							<?php shopp('product','variations','mode=multiple&label=true&defaults=Select an option&before_menu=<li>&after_menu=</li>'); ?>			
						</ul>
						<?php endif; ?>
						<div><label>Qty</label> <?php shopp('product','quantity','class=selectall&input=menu'); ?></div>
						<div><?php shopp('product','addtocart'); ?></div>
					
					</form>
				</div>				

				<?php endif; ?>
				
				<h2>Shopping Cart</h2>
				
				<?php shopp('cart','sidecart'); ?>

			</div>
			
		</div><!-- end .yui-b --> 
		
		<?php endwhile; endif; ?>	

	 </div><!-- end #bd -->        
    
<?php get_footer(); ?>     
