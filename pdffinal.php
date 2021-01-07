<?php
	session_start();

	require("util.php");
	require("emailer.php");
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");

	if (($pdf_upload_type[0] == "application/pdf")) {
		move_uploaded_file($pdf_upload[0], "images/PDF/$template-" . $PDF['ID'] . ".pdf") or $log .= "Couldn't copy PDF 1 to server<br>";
		$extension = ".pdf";
	} elseif (($pdf_upload_type[0] == "application/illustrator")){
		move_uploaded_file($pdf_upload[0], "images/PDF/$template-" . $PDF['ID'] . ".ai") or $log .= "Couldn't copy AI 1 to server<br>";
		$extension = ".ai";
	} else {
		$log .= "File 1 is not a PDF<br>";
	}

	if($PDF['2_Sided']!="0")
	{
		if (($pdf_upload_type[1] == "application/pdf")) {
			@move_uploaded_file($pdf_upload[1], "images/PDF/$template-" . $PDF['ID'] . "-back.pdf") or $log .= "Couldn't copy PDF 2 to server<br>";
		} elseif (($pdf_upload_type[1] == "application/illustrator")){
			move_uploaded_file($pdf_upload[1], "images/PDF/$template-" . $PDF['ID'] . "-back.ai") or $log .= "Couldn't copy AI 2 to server<br>";
		} else {
			$log .= "File 2 is not a PDF<br>";
		}
	}
	foreach($_GET as $a=>$b)
	{
		$PDF[ucfirst($a)]=$b;
	}
	if($log=="")
	{
		$statement = "INSERT INTO PDF_Uploads SET";
		foreach($PDF as $a=>$b)
		{
			if(is_string($b))
				$statement .= " $a='" . $b . "',";
			else
				$statement .= " $a=$b,";
		}
		$statement .= " DateStamp=now();";
		$sql->Insert($statement);
	}
	$statement="SELECT * FROM Users WHERE ID=" . $user;
	$sql->Queryrow($statement);
	$row=$sql->data;

	$email = $row['Email'];
	$msg = $row['name'] . " has ordered " . $PDF['Quantity'] . " Business Cards through the PDF Uploader<br>\n-----------------------------------------------------------------------------------------<br>\n";
	$msg .= "Shipping Information:<br>\n";
	$msg .= stripslashes($HTTP_POST_VARS['company']) . "<br>\n";
	$msg .= $HTTP_POST_VARS['name'] . "<br>\n";
	$msg .= $HTTP_POST_VARS['address1'] . "<br>\n";
	if($HTTP_POST_VARS['address2']!="")
		$msg .= $HTTP_POST_VARS['address2'] . "<br>\n";
	$msg .= $HTTP_POST_VARS['city'] . ", " . $HTTP_POST_VARS['state'] . "  " . $HTTP_POST_VARS['zip'] . "<br>\n";
	$msg .="Ship By: ";
	if($HTTP_POST_VARS['ship']=="2")
		$msg .= "2 Day Air<br>\n";
	elseif($HTTP_POST_VARS['ship']=="o")
		$msg .= "Next Day<br>\n";
	else
		$msg .= "UPS Ground<br>\n";
	if($HTTP_POST_VARS['po']!="")
		$msg .= "Purchase Order #: " . $HTTP_POST_VARS['po'] . "<br>\n";

	$msg .= "This card will be printed on " . $PDF['Quality'];
	if($_POST['Notes']!="")
		$msg .= "<br>\nExtra Notes: " . $_POST['Notes'] . "<br>\n";

	if($log=="")
	{
		$emailer=new email_html_wa($email, $PDF['Quantity'] . " PDF Upload Order - Complete", $email, $email);
		$emailer->clean();
		$emailer->setheaders();
		$emailer->addmessage($msg);
		$emailer->embed_image("images/PDF/$template-" . $PDF['ID'] . $extension,  $template . "-" . $PDF['ID'] . $extension);
		if(@fopen("http://www.bizcardstoday.com/images/PDF/$template-" . $PDF['ID'] . "-back" . $extension, 'r'))
		{
			$emailer->embed_image("images/PDF/$template-" . $PDF['ID'] . "-back" . $extension, $template . $extension);
		}

		$emailer->sendmail("orders@bizcardstoday.com");
		$emailer->sendmail("admin@buskirkgraphics.com");
		//$emailer->sendmail("todd@crystalcommunications.biz");
	}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>BizCardsToday.com PDF Order Confirmation</title>
	</head>

	<body bgcolor="#ffffff">
		<?
			if($log!="")
			{
				echo "<p>Your order <b>HAS NOT</b> placed due to a failure to properly receive your PDF file(s).  Please verify that you can access the files you are trying to send to us, and that they are in PDF format.  Once this has been verified please <a href=pdfproof.php>click here</a> to place your order again.<br><br><input type=button value=\"Return to Main Menu\" onclick=\"window.location='welcome.php'\"></p>";
			}else
			{
				echo $msg;
				echo "<br><input type=button value=\"Return to Main Menu\" onclick=\"window.location='welcome.php'\">";
			}
		?>
	</body>

</html>
