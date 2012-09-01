<?php
	// powerpress-feed-auth.php
	
	function powerpress_feed_auth($feed_slug)
	{
		$FeedSettings = get_option('powerpress_feed_'.$feed_slug);
		
		if( !isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) )
			powerpress_feed_auth_basic( $FeedSettings['title'] );
			
		$user = $_SERVER['PHP_AUTH_USER'];
		$password = $_SERVER['PHP_AUTH_PW'];
		
		$user = wp_authenticate($user, $password);
		
		if( !is_wp_error($user) )
		{
			// Check capability...
			if( $user->has_cap( $FeedSettings['premium'] ) )
				return; // Nice, let us continue...
			
			powerpress_feed_auth_basic( $FeedSettings['title'], __('Access Denied', 'powerpress') );
		}
		
		// user authenticated here
		powerpress_feed_auth_basic( $FeedSettings['title'], __('Authorization Failed', 'powerpress') );
	}
	
	function powerpress_feed_auth_basic($realm_name, $error = false )
	{
		if( !$error )
			$error = __('Unauthorized', 'powerpress');
		header('HTTP/1.0 401 Unauthorized');
		header('WWW-Authenticate: Basic realm="'. str_replace('"', '', $realm_name).'"');
		
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">';
		echo "\n";
?>
<html>
<head>
	<title><?php echo $error; ?></title>
	<meta name="robots" content="noindex" />
</head>
<body>
	<p><?php echo $error; ?></p>
</body>
</html>
<?php
		exit;
	}

?>