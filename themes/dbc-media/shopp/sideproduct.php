<?php if (shopp('product','found')): ?>
	
	<a href="<?php shopp('product','url'); ?>"><?php shopp('product','thumbnail','class=alignleft'); ?></a>

	<h3><a href="<?php shopp('product','url'); ?>"><?php shopp('product','name'); ?></a></h3>

	<?php if (shopp('product','onsale')): ?>
		<p class="original price"><?php shopp('product','price'); ?></p>
		<p class="sale price"><big><?php shopp('product','saleprice'); ?></big></p>
	<?php else: ?>
		<p class="price"><big><?php shopp('product','price'); ?></big></p>
	<?php endif; ?>
	
	<br class="clear" />
<?php endif; ?>
