<?php if (shopp('cart','hasitems')): ?>
<div id="cart" class="shopp">
<table>
	<tr>
		<th scope="col" class="item">Cart Items</th>
		<th scope="col">Quantity</th>
		<th scope="col" class="money">Item Price</th>
		<th scope="col" class="money">Item Total</th>
	</tr>

	<?php while(shopp('cart','items')): ?>
		<tr>
			<td><a href="<?php shopp('cartitem','url'); ?>"><?php shopp('cartitem','name'); ?></a><?php shopp('cartitem','options','show=selected&before= (&after=)'); ?></td>
			<td><?php shopp('cartitem','quantity'); ?></td>
			<td class="money"><?php shopp('cartitem','unitprice'); ?></td>
			<td class="money"><?php shopp('cartitem','total'); ?></td>
		</tr>
	<?php endwhile; ?>

	<tr class="totals">
		<td colspan="2" rowspan="5">
			<?php if ((shopp('cart','has-shipping-methods'))): ?>
			<small>Select a shipping method:</small>

			<ul id="shipping-methods">
			<?php while(shopp('shipping','methods')): ?>
				<li><label><?php shopp('shipping','method-selector'); ?>
				<?php shopp('shipping','method-name'); ?> &mdash;
				<strong><?php shopp('shipping','method-cost'); ?></strong><br />
				<small><?php shopp('shipping','method-delivery'); ?></small></label>
				</li>
			<?php endwhile; ?>
			</ul>
			
			<?php endif; ?>
		</td>
		<th scope="row">Subtotal</th>
		<td class="money"><?php shopp('cart','subtotal'); ?></td>
	</tr>
	<?php if (shopp('cart','hasdiscount')): ?>
	<tr class="totals">
		<th scope="row">Discount</th>
		<td class="money">-<?php shopp('cart','discount'); ?></td>
	</tr>
	<?php endif; ?>
	<tr class="totals">
		<th scope="row"><?php shopp('cart','shipping','label=Shipping'); ?></th>
		<td class="money"><?php shopp('cart','shipping'); ?></td>
	</tr>
	<!--<tr class="totals">
		<th scope="row"><?php shopp('cart','tax','label=Taxes'); ?></th>
		<td class="money"><?php shopp('cart','tax'); ?></td>
	</tr>-->
	<tr class="totals total">
		<th scope="row">Total</th>
		<td class="money"><?php shopp('cart','total'); ?></td>
	</tr>
</table>
</div>
<?php else: ?>
	<p class="warning">There are currently no items in your shopping cart.</p>
<?php endif; ?>
