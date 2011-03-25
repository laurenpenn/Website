<?php
$protocol = $_SERVER["SERVER_PROTOCOL"];
if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
        $protocol = 'HTTP/1.0';
header( "$protocol 503 Service Unavailable", true, 503 );
header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Maintenance</title>
    
	<style type="text/css">
	body {
		background: #15100e;
		color: #5c5650;
		font: 14px/18px Tahoma, Arial, Helvetica, sans-serif;
	}
	
	#ohno {
		background: #ccc;
		border: 1px solid #fff;
		border-radius: 20px;
		-moz-border-radius: 20px;
		-webkit-border-radius: 20px;
		box-shadow: 0 0 10px #000;
		-moz-box-shadow: 0 0 10px #000;
		-webkit-box-shadow: 0 0 10px #000;
		margin: 0 auto;
		padding: 50px;
		width: 500px;
	}
	</style>
</head>
<body>
	
	<div id="ohno">
		<h1>Update in progress.</h1>
		<p>We're doing some pretty awesome things behind the scenes right now. You'd be impressed. Come back real soon.</p>
		<p><a href="http://dbcmedia.org">DBC</a> Media is still up and running and you can visit us on <a href="http://facebook.com/dentonbible">Facebook</a>.</p>
	</div>
</body>
</html>
<?php die(); ?>