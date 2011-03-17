<?php if (shopp('product','found')): ?>

	<h3><?php shopp('product','name'); ?></h3>

	<?php shopp('product','thumbnail','class=alignleft'); ?>

	<p class="headline"><big><?php shopp('product','summary'); ?></big></p>
	<?php if (shopp('product','onsale')): ?>
		<h3 class="original price"><?php shopp('product','price'); ?></h3>
		<h3 class="sale price"><?php shopp('product','saleprice'); ?></h3>
	<?php else: ?>
		<h3 class="price"><?php shopp('product','price'); ?></h3>
	<?php endif; ?>

	<form action="<?php shopp('cart','url'); ?>" method="post" class="shopp product">
		<p><?php if(shopp('product','has-variations')) shopp('product','variations','mode=single&label=false&defaults=Select an option'); ?>
		<?php shopp('product','addtocart'); ?></p>
	
	</form>
	<br class="clear" />
<?php endif; ?>
