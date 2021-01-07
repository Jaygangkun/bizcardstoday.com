<?php
include 'ChromePhp.php';

ChromePhp::log('Hello console!');
ChromePhp::log($_SERVER);
ChromePhp::warn('something went wrong!');
echo("Yes<br>");

$a = 1;

if($a <> 1)
	echo('yes');
else
	echo('no');




?>
