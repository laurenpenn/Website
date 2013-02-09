<?php
	define('PREMIUMCAST', true);
	define('PODPRESS_PREMIUM_METHOD', 'Digest');
	podPress_validateLogin();
	require('wp-rss2.php');
