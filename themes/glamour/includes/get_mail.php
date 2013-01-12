<?php

	//-----------------------------------------------------
	//-----------------------------------------------------
	$address=$_GET["email"]; // You must change it.... write to your mail.	
	//-----------------------------------------------------
	//-----------------------------------------------------

	$name = $_REQUEST["Name"];
	$email = $_REQUEST["Email"];
	$subject = $_REQUEST["Subject"];
	$message_content = $_REQUEST["Message"];
	
	$mime_boundary = md5(time());

	$headers = "From: $name <$email>\n";
	$headers .= "Reply-To: $subject <$email>\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";

	$message = "--$mime_boundary\n\n";
	
	$message .= "You have an email from your web site: \n\n\n";
	$message .= "Name: $name \n\n";
	$message .= "Email: $email \n\n";
	$message .= "Subject: $subject \n\n";
	$message .= "Message: $message_content \n\n";

	$message .= "--$mime_boundary--\n\n";

	$mail_sent = @mail( $address, $subject, $message, $headers );
	echo $mail_sent ? "Success, mail sent!" : "Mail failed";

?>
