<form action="<?php shopp('checkout','url'); ?>" method="post" class="shopp" id="checkout" name="checkout" onsubmit="return validate_form ( );">

<?php shopp('checkout','cart-summary'); ?>

<?php if (shopp('cart','hasitems')): ?>
	<?php shopp('checkout','function'); ?>
		<div class="styled-form">
			<div class="yui-g first">				
				<?php if (shopp('customer','notloggedin')): ?>
				<fieldset>
					<legend>My Account</legend>
					<p><a href="<?php bloginfo('url'); ?>/shop/account/">Login</a></p>
					<p><em>If you already have an account, log in to save yourself some time and track all of your purchases!</em></p>
				</fieldset>
				<?php else:	?>
					
					<fieldset>
						<legend>My Account</legend>
						<p><a href="<?php bloginfo('url'); ?>/shop/account/">Manage My Account</a></p>
					</fieldset>
					
				<?php endif; ?>
				<fieldset>
					<legend>Contact Information</legend>
					<ul>
						<li><label for="firstname">First</label><?php shopp('checkout','firstname','required=true&minlength=2&size=50&title=First Name'); ?>
						<li><label for="lastname">Last</label><?php shopp('checkout','lastname','required=true&minlength=3&size=50&title=Last Name'); ?>
						<li><label for="company">Company/Organization</label><?php shopp('checkout','company','size=50&title=Company/Organization'); ?></li>

						<li><label for="phone">Phone</label><?php shopp('checkout','phone','required=true&format=phone&size=50&title=Phone'); ?></li>
						
						<li><hr /></li>
						
						<li><label for="email">Email</label><?php shopp('checkout','email','required=true&format=email&size=50&title=Email'); ?></li>
							
						<?php if (shopp('customer','notloggedin')): ?>
						<li><label for="email">Password</label><?php shopp('checkout','password','required=true&format=passwords&size=50&title=Password'); ?></li>
						<li><label for="email">Confirm Password</label><?php shopp('checkout','confirm-password','required=true&format=passwords&size=50&title=Password Confirmation'); ?></li>
						<?php endif; ?>
					</ul>
				</fieldset>
			</div>
			<div class="yui-g">
				<fieldset>
					<legend>Secure</legend>
					<p><small>This site takes appropriate precautions to make your purchase secure.</small></p>
					<p class="secure-images">
						<img src="<?php bloginfo('template_directory'); ?>/images/visa2.gif" />
						<img src="<?php bloginfo('template_directory'); ?>/images/mastercard1.gif" />
						<img src="<?php bloginfo('template_directory'); ?>/images/amex1.gif" />
						<img src="<?php bloginfo('template_directory'); ?>/images/discover.gif" />
						Shipping by: <img src="<?php bloginfo('template_directory'); ?>/images/ups.gif" />
					</p>
					<!-- (c) 2005, 2009. Authorize.Net is a registered trademark of CyberSource Corporation -->
					<div class="AuthorizeNetSeal">
						<script type="text/javascript" language="javascript">var ANS_customer_id="c05e0709-ff30-4ada-979a-9062ab466c28";</script>
						<script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script>
						<a href="http://www.authorize.net/" id="AuthorizeNetText" target="_blank">Payment Processing</a>
					</div> 
				</fieldset>
			</div>
		</div>
		
		<div class="styled-form">

			<fieldset>
				<legend>Address</legend>
				<ul>
					<?php if (shopp('checkout','shipping')): ?>
					<div class="half" id="billing-address-fields">
					<?php else: ?>
					<li>
					<?php endif; ?>

						<li><strong>Billing Address</strong></li>
						<li><label for="billing-address">Street Address</label><?php shopp('checkout','billing-address','required=true&title=Billing street address'); ?></li>
						<li><label for="billing-xaddress">Address Line 2</label><?php shopp('checkout','billing-xaddress','title=Billing address line 2'); ?></li>
						<li><label for="billing-city">City</label><?php shopp('checkout','billing-city','required=true&title=City billing address'); ?></li>
						<li><label for="billing-state">State / Province</label><?php shopp('checkout','billing-state','required=true&title=State/Provice/Region billing address'); ?></li>
						<li><label for="billing-postcode">Postal / Zip Code</label><?php shopp('checkout','billing-postcode','required=true&title=Postal/Zip Code billing address'); ?></li>
						<li><label for="billing-country">Country</label><?php shopp('checkout','billing-country','required=true&title=Country billing address'); ?></li>
					</div>
					<?php if (shopp('checkout','shipping')): ?>
					<div class="half right" id="shipping-address-fields">
						<li for="shipping-address"><strong>Shipping Address</strong></li>
						<li><label for="shipping-address">Street Address</label><?php shopp('checkout','shipping-address','required=true&title=Shipping street address'); ?></li>
						<li><label for="shipping-xaddress">Address Line 2</label><?php shopp('checkout','shipping-xaddress','title=Shipping address line 2'); ?></li>
						<li><label for="shipping-city">City</label><?php shopp('checkout','shipping-city','required=true&title=City shipping address'); ?></li>
						<li><label for="shipping-state">State / Province</label><?php shopp('checkout','shipping-state','required=true&title=State/Provice/Region shipping address'); ?></li>
						<li><label for="shipping-postcode">Postal / Zip Code</label><?php shopp('checkout','shipping-postcode','required=true&title=Postal/Zip Code shipping address'); ?></li>
						<li><label for="shipping-country">Country</label><?php shopp('checkout','shipping-country','required=true&title=Country shipping address'); ?></li>
					</div>
					<?php else: ?>
					</div>
					<?php endif; ?>
					<li class="inline"><?php shopp('checkout','same-shipping-address'); ?></li>
				</ul>

			</fieldset>

			<?php if (shopp('checkout','billing-required')): ?>
			
			<fieldset>
			
				<legend>Payment Information</legend>
				<ul>
					<li><label for="billing-cardtype">Card Type</label><?php shopp('checkout','billing-cardtype','required=true&title=Card Type'); ?></li>

					<li><label for="billing-card">Credit/Debit Card Number</label><?php shopp('checkout','billing-card','required=true&size=50&title=Credit/Debit Card Number'); ?></li>
					<li><label for="billing-cardholder">Name on Card</label><?php shopp('checkout','billing-cardholder','required=true&size=50&title=Card Holder\'s Name'); ?></li>

					<li class="billing-cardexpires">
						<span class="one"><label for="billing-cardexpires-mm">MM</label><?php shopp('checkout','billing-cardexpires-mm','size=4&required=true&minlength=2&maxlength=2&title=Card\'s 2-digit expiration month'); ?> /</span>
						<span class="two"><label for="billing-cardexpires-yy">YY</label><?php shopp('checkout','billing-cardexpires-yy','size=4&required=true&minlength=2&maxlength=2&title=Card\'s 2-digit expiration year'); ?></span>
						<span class="three"><label for="billing-cvv">Security ID</label><?php shopp('checkout','billing-cvv','size=7&minlength=3&maxlength=4&title=Card\'s security code (3-4 digits on the back of the card)'); ?></span>
					</li>
				</ul>
			</fieldset>
			<?php endif; ?>

			<p><?php shopp('checkout','order-data','type=checkbox&name=terms-and-conditions&required=1&title=Terms and Conditions'); ?> I agree to the <a href="<?php bloginfo('url'); ?>/support/terms-conditions/">Terms &amp; Conditions</a></p>

			<?php shopp('checkout','billing-xco'); ?>
			
		</div><!-- end .styled-form -->
	<br class="clear" />
	<p class="submit"><?php shopp('checkout','submit','value=Submit Order'); ?></p>

<?php endif; ?>
</form>
