<?php 

$to = 'lpweber@mac.com';

$subject = 'Testing';

$headers = "From: " . 'lpweber@mac.com' . "\r\n";
$headers .= "Reply-To: ". 'lpweber@mac.com' . "\r\n";
$headers .= "CC: les.weber@gmail.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$message = '<html><body>';
$message .= '<h1>Hello, World!</h1>';
$message .= '</body></html>';
if(mail($to, $subject, $message, $headers))
	echo('sent');
else
	echo('not sent');

?>