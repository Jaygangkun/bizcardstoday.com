<?php
	session_start(); //Start the session
// 	if($template==0 || !session_is_registered("template") || $template=="") //  boot to homepage if the card template is unspecified
	if(!session_is_registered("template")) //Boot to homepage if card template is not set.
		header("Location: index2.php");

	require("util.php"); // db wrapper
	require("emailer.php"); //email wrapper
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");

	if($client_status=='a')
	{
		foreach($_POST as $a=>$b)
		{
			if($b!=0 && ereg('^[0-9]+$', $a)) //If the value is not 0 and the key is a number
			{
				$card_quality=$_POST[$a . "_quality"];
				//Update the card record with this new order.
				$statement="UPDATE Finished_Cards SET Date_Stamp=now(), Status='O', due_date=now()+interval 14 day, Quantity=$b, speed_card='n', Quality='" . $card_quality . "'";
				if($_POST['po']!="")
					$statement .= ", po='" . $_POST['po'] . "'";
				$statement .= " WHERE ID=$a";
				$sql->Update($statement);

				//Log this card in the Order History
				$statement = "INSERT INTO Order_History SET Order_id=" . $a . ", Quantity=" . $b . ", Date_Stamp=now(), Action=\"";
				if($client_status=='p')
				{
					$statement .= "Test\", ";
				}else
				{
					$statement .= "Order\", ";
				}
				$statement .= "Done_By=\"";
				if($full=='y')
					$statement .="BizCards $user\"";
				elseif($partial=='y')
					$statement .= "Admin $user\"";
				else
					$statement .= $user . "\"";
				$sql->Insert($statement);

				//Grab information for pricing and mailing the card order
				$statement="SELECT * FROM Users WHERE ID=" . $user;
				$sql->Queryrow($statement);
				$sender=$sql->data; //This array hold informtation about the current user
				$statement="SELECT per250, per250_premium, per500, per500_premium, per1000, per1000_premium, per2000, per2000_premium, Paper_premium, Template_Name FROM Templates WHERE ID=$template";
				$sql->QueryRow($statement);
				$cur_template=$sql->data; //This is pricing information
				$statement="SELECT * FROM Finished_Cards WHERE ID=$a";
				$sql->QueryRow($statement);
				$row=$sql->data; //This is the current card

				$msg = $sender['name'] . " has ordered " . $b . " Business Cards for " . stripslashes(str_replace("\"", "&quot;",$row['Card_Name'])) . "<br>\n-----------------------------------------------------------------------------------------<br>\n";
				$notice .= $sender['name'] . " has ordered " . $b . " Business Cards for " . stripslashes(str_replace("\"", "&quot;", $row['Card_Name'])) .", Shipped to " . $row['address1'] . " " . $row['address2'] . " " . $row['city'] . ", " . $row['state'] . " " . $row['zip'];
				$msg .= "Shipping Information:<br>\n";
				$msg .= stripslashes($row['company']) . "<br>\n";
				$msg .= $row['name'] . "<br>\n";
				$msg .= $row['address1'] . "<br>\n";
				if($row['address2']!="")
					$msg .= $row['address2'] . "<br>\n";
				$msg .= $row['city'] . ", " . $row['state'] . "  " . $row['zip'] . "<br>\n";
				if($row['po']!="")
					$msg .= "Purchase Order #: " . $row['po'] . "<br>\n";

				if($card_quality=="s")
					$msg .= "This card will be printed on " . $row['Paper'] . " with " . $row['Ink'] . "<br>\n";
				else
				{
					$msg .= "This card will be printed on " . $cur_template['Paper_premium'] . " with " . $row['Ink'] . "<br>\n";
					$msg .= "This is a <b>PREMIUM</b> card.<br>\n";
				}
				//This was commented out because we purge the Notes when a card is reordered normally, thusly it should be purged here.
				//if($row['Notes']!="")
				//	$msg .= "Extra Notes: " . $row['Notes'] . "<br>\n";

				if($row['Vertical']=='y') //Set display size variables
				{
					//These variables are only used for the back side of a two sided card
					$width=294;
					$height=498;
				}else{
					//These variables are only used for the back side of a two sided card
					$width=498;
					$height=294;
				}

				//Price calculation
				if($b==250)
				{
					if($card_quality=="s")
						$price=$cur_template['per250'];
					else
						$price=$cur_template['per250_premium'];
					$msg .= "Pricing: 250 @ " . $price . "<br>\n";
					$notice .="250";
				}else if($b==500)
				{
					if($card_quality=="s")
						$price=$cur_template['per500'];
					else
						$price=$cur_template['per500_premium'];
					$msg .= "Pricing: 500 @ " . $price . "<br>\n";
					$notice .="500";
				}else if($b==1000)
				{
					if($card_quality=="s")
						$price=$cur_template['per1000'];
					else
						$price=$cur_template['per1000_premium'];
					$msg .= "Pricing: 1000 @ " . $price . "<br>\n";
					$notice .="1000";
				}else
				{
					if($card_quality=="s")
						$price=$cur_template['per2000'];
					else
						$price=$cur_template['per2000_premium'];
					$msg .= "Pricing: 2000 @ " . $price . "<br>\n";
					$notice .="2000";
				}
				$notice .= " @ $price.<br>\n";
				$total += $price;
				$msg .="<br>\n<br>\nCARD SKU: " . $template . "-" . $a . "<br>\n";
				$msg .="<br>\nTemplate Name: " . $cur_template['Template_Name'] . "<br>\n";
				if($row['speed']=='y')
					$emailer=new email_html_wa($sender['Email'], $quantity . " Batch Card order - Complete w/ BizQuick", $sender['Email'], $sender['Email']);
				$msg .="<BR><a href=http://www.bizcardstoday.com/images/finished/$template" . "-" . $row['ID'] . ".svg>SVG TO DOWNLOAD</A><BR>";
				if(@fopen("http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $card['ID'] . ".jpg", 'r'))
				{
				$msg .="<a href=http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $row['ID'] . ".jpg>Picture to Download</a><BR>";
				}

				else
					$emailer=new email_html_wa($sender['Email'], $quantity . " Batch Card order - Complete", $sender['Email'], $sender['Email']);
				$emailer->clean();
				$emailer->setheaders();
				$emailer->addmessage($msg);
				$emailer->embed_image($row['Filename'],  $template . "-" . $row['ID'] . ".svg");
				$msg .="<BR><a href=http://www.bizcardstoday.com/images/finished/$template" . "-" . $row['ID'] . ".svg>SVG TO DOWNLOAD</A><BR>";
				if(@fopen("http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $card['ID'] . ".jpg", 'r'))
				{
				$msg .="<a href=http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $row['ID'] . ".jpg>Picture to Download</a><BR>";
				}

				if(@fopen("http://www.bizcardstoday.com/images/finished/" . $template . ".svg", 'r'))
				{
					$emailer->embed_image("images/finished/" . $template . ".svg",  $template . "-Back.svg");
				}
				//$testemail="orders@bizcardstoday.com, admin@buskirkgraphics.com";
				$testemail = array();
				$testemail[] = "orders@bizcardstoday.com";
				$testemail[] = "admin@buskirkgraphics.com";

				//$testemail[] = "jason@crystalcommunications.biz";
				//$testemail[] = "todd@crystalcommunications.biz";
				foreach($testemail as $a)
				{
					$emailer->sendmail($a);
					echo "<!--$a-->\n";
				}
				//$emailer->sendmail($testemail);
			}
		}
	}
	$notice .= "<br> Batch Order Total: \$$total<br>\n";
	if($client_status=='p')
	{
		$notice .="<font size=+1 color=red>THIS IS JUST A TEST ORDER.  NO REAL ORDER HAS BEEN PLACED.</font><br>\n";
	}
?>
<html>
	<head>
		<title>BizCardsToday.com Batch Order Confirmation</title>
	</head>

	<body>
		<p><? echo $notice; ?></p>
		<p><a href="welcome.php">Return to Menu</a></p>
	</body>
</html>