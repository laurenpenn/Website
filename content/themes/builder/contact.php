<?php
define("WEBMASTER_EMAIL", 'administrator@fxmail.ru'); // Enter your e-mail
 
error_reporting (E_ALL); 
 
if(!empty($_POST))
{
$_POST = array_map('trim', $_POST); 
$name = htmlspecialchars($_POST['name']);
$email = $_POST['email'];
$message = htmlspecialchars($_POST['message']);
 
$error = array();
 
 
if(empty($name))
{
$error[] = _e('Please enter your name','commander');
}
 
 
if(empty($email))
{
$error[] = _e('Please enter your e-mail','commander');
}elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
{ 
$error[] = _e('e-mail is incorrect','commander');
}
 
 
if(empty($message) || empty($message{15})) 
{
$error[] = _e('Please enter message more than 15 characters','commander');
}
 
if(empty($error))
{ 
$message = 'Name ' . $name . '
Email: ' . $email . '
Mssage: ' . $message;
$mail = mail(WEBMASTER_EMAIL, 'WebSite', $message,
     "From: ".$name." <".WEBMASTER_EMAIL."> \r\n"
    ."Reply-To: ".$email."\r\n"
    ."X-Mailer: PHP/" . phpversion());
 
if($mail)
{
echo 'OK';
}
 
}
else
{
echo '<div class="notification_error">'.implode('<br />', $error).'</div>';
}
}