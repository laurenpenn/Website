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
		background: #000;
		color: #5c5650;
		font: 14px/18px Tahoma, Arial, Helvetica, sans-serif;
		margin: 50px 0;
	}
	
	#logo { text-align: center; }
	
	#ohno {
		border: 1px solid #000;
		border-radius: 20px;
		-moz-border-radius: 20px;
		-webkit-border-radius: 20px;
		box-shadow: 0 0 30px #38241D;
		-moz-box-shadow: 0 0 30px #38241D;
		-webkit-box-shadow: 0 0 30px #38241D;
		margin: 50px auto 0;
		width: 550px;
	}
	#ohno-inside {
		background: #15100e url('wp-content/themes/dbc/library/images/bg.png') center top no-repeat;
		border-radius: 20px;
		-moz-border-radius: 20px;
		-webkit-border-radius: 20px;
		box-shadow: inset 0 0 10px #000;
		-moz-box-shadow: inset 0 0 10px #000;
		-webkit-box-shadow: inset 0 0 10px #000;
		color: #C8B6A2;
		padding: 30px 50px;
		text-shadow: -1px -1px 1px #15100e;
	}
	h1 {
		font: 30px/30px "Georgia", "Times New Roman", Times, Serif;
		text-align: center;
	}
	h2 {
		color: #6B4012;
		font: 20px/20px "Georgia", "Times New Roman", Times, Serif;
		text-align: center;
		text-shadow: 0 1px 0 #000;
	}
	p {
		font-size: 12px;
	}
	a { color: #fff; }
	</style>
</head>
<body>
	
	<div id="logo"><img src="http://dentonbible.org/wp-content/themes/dbc/library/images/denton-bible-church.png" alt="Denton Bible Church" /></div>
	
	<div id="ohno">
		<div id="ohno-inside">
			<h1>Someone turned off the lights!</h1>
			<h2>actually, we're just updating the site</h2>
			<p>We're doing some pretty awesome things behind the scenes right now. You'd be impressed. Come back real soon.</p>
			<p><a href="http://dbcmedia.org">DBC Media</a> is still up and running.</p>
			<p>And of course you can always find us on Facebook...</p>
			<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%253A%252F%252Fwww.facebook.com%252Fpages%252FDenton-TX%252FDenton-Bible-Church%252F206268862487&amp;width=450&amp;colorscheme=light&amp;connections=0&amp;stream=true&amp;header=0&amp;height=395" scrolling="no" frameborder="0" style="background: #fff; border:none; overflow:hidden; width:450px; height:395px;" allowTransparency="true"></iframe>
		</div>
	</div>
</body>
</html>
<?php die(); ?>