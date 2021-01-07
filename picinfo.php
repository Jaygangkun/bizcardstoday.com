<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>BizCardsToday.com Photo Specifications</title>
	</head>

	<body bgcolor="#ffffff">
<?php
	if($_REQUEST['msg']==1)
		echo "<p>Pictures uploaded for use in your business cards must be " . round(($_GET['pic_width']/300), 2) . " inches (" . $_GET['pic_width'] . " pixels) wide by " . round(($_GET['pic_height']/300),2) . " inches (" . $_GET['pic_height'] . " pixels) high at 300 DPI resolution in JPEG format.  If you have further questions about your pictures please send an email to <a href=mailto:bizinfo&#64;bizcardstoday.com>bizinfo&#64;bizcardstoday.com</a></p>";
	elseif($_POST['msg']==2)
		echo "<p>Biz Cards offers two quality levels - <b>Regular</b> and <b>Premium</b>.</p><p>Most of our clients choose <b>Regular Biz Cards</b> because of the amazing turnaround and lower price. These cards are printed digitally on a 10 point coated stock. Because of bleeds, ink coverage, and complexity, some designs are not good candidates for our <b>Regular</b> Biz Cards and must be produced on our <b>Premium</b> press. </p><p>If you want the very best image possible, choose our <b>Premium Biz Cards</b>, printed at higher resolution on a heavy 14 point coated stock. These top end cards with a glossy UV coating cost a little more, but they look and feel spectacular. Premium quality does take a little longer, so you’ll have to wait about 10 days.</p>";
?>

	</body>

</html>