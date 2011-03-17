<?php if(shopp('category','hasproducts','load=prices,images')): ?>
	<div class="category">
	
		<h2><?php shopp('category','name'); ?></h2>
		
		<?php shopp('catalog','breadcrumb'); ?>

		<div><?php shopp('catalog','orderby-list','dropdown=on'); ?></div>
		
		<div class="alignright"><?php shopp('category','pagination'); ?></div>
		
		<div class="clear"></div>

		<table>
			<thead>
				<tr>
					<th class="col1" scope="col">Title</th>
					<th class="col2">Speaker</th>
					<th class="col3">Date</th>
				</tr>
			</thead>
			
			<tbody>

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
					<td class="product-name"><?php if(shopp('product','in-category','id=3')) : echo "<strong>Series:</strong> "; endif; ?><a href="<?php shopp('product','url'); ?>"><?php shopp('product','name'); ?></a></td>
					<td class="product-speaker"><a href="<?php bloginfo('url'); ?>/?st=shopp&amp;s=<?php echo $detail_speaker; ?>"><?php echo $detail_speaker; ?></a></td>
					<td class="product-date"><?php echo $detail_date; ?></td>
				</tr>
				
				<?php endwhile; ?>
			
			</tbody>
		
		</table>
		
		<div class="clear"></div>
		<div class="alignright"><?php shopp('category','pagination'); ?></div>
		<div class="clear"></div>
	</div>
<?php else: ?>
	<?php if (!shopp('catalog','is-landing')): ?>
	<?php shopp('catalog','breadcrumb'); ?>
	<h3><?php shopp('category','name'); ?></h3>
	<p>No products were found.</p>
	<?php endif; ?>
<?php endif; ?>
