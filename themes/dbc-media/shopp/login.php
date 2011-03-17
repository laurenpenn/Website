<?php if (shopp('customer','accounts','return=true') == "none"): ?>
	<?php shopp('customer','order-lookup'); ?>
<?php return; endif; ?>

<form action="<?php shopp('customer','url'); ?>" method="post" class="shopp" id="login">

<?php if (shopp('customer','process','return=true') == "recover"): ?>

	<ul>
		<li><h3>Recover your password</h3></li>
		<li><?php shopp('customer','login-errors'); ?></li>
		<li>
		<span><?php shopp('customer','account-login','size=20&title=Login'); ?><label for="login"><label for="login"><?php shopp('customer','login-label'); ?></label></label></span>
		<span><?php shopp('customer','recover-button'); ?></span>
		</li>
		<li></li>
	</ul>

<?php else: ?>

	<?php if (shopp('customer','notloggedin')): ?>
	<p><?php shopp('customer','login-errors'); ?></p>
				
	<div class="styled-form" class="yui-g">
		<div class="yui-u first">
			<fieldset>
				<legend>Account Login</legend>
				<ul>
					<li>
						<label for="login"><?php shopp('customer','login-label'); ?></label>
						<?php shopp('customer','account-login','size=20&title=Login'); ?>
					</li>
					<li>
						<label for="password">Password</label>
						<?php shopp('customer','password-login','size=20&title=Password'); ?>
					</li>
					<li>
						<?php shopp('customer','login-button'); ?>
					</li>
					<li><a href="<?php shopp('customer','recover-url'); ?>">Lost your password?</a></li>
					
				</ul>
			</fieldset>
		</div>
		<div class="yui-u">
			<fieldset>
				<legend>What account?</legend>
				<p>When you purchase anything from our store we'll automatically create an account for you. This allows you to easily access your purchase history and make future purchases faster.</p>
				<p>We'll keep your basic information, addresses, and email address on hand, but <em>we don't store any credit card information.</em></p>
			</fieldset>
		</div>
	</div>
		
	<?php endif; ?>

<?php endif; ?>

</form>