<?php 

if(!isset($_GET[c]))
{
echo("<img alt='maintenance' src='http://lab.webereng.com/maintenance.jpg' width='333' height='152'>");
echo("<h2>bizcardstoday.com Undergoing Maintenance</h2> <br><h4>Please stop back in 6 or so hours. Sorry for the inconveniece!</h4>");
exit();
}else 
{
$headerString = 'Location: http://www.bizcardstoday.com/bizcards/index.php';
header($headerString);

}

?>