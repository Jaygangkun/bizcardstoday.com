<?php
/**********************************************************************************************************************/
/*	Project: BizCardsToday.com															*/
/*	Programmer: Jason Hosler															*/
/*	Purpose: This is the order placement/confirmation page.  It must accomplish three actions: 		*/
/*		1:	Insert the card information into the database									*/
/*		2:	Build the finished SVG card													*/
/*		3:	Send confirmation email														*/
/*		The page should display the built SVG and the shipping information						*/
/**********************************************************************************************************************/
	session_start();
// echo('<pre>');
// print_r($_SESSION);
// print_r($_POST);
// print_r($_GET);
// echo('</pre>');

	require("util.php");//db wrapper
	require("emailer.php"); //email wrapper
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");

	// if(!session_is_registered("template")) //Boot to homepage if card template is not set.
	if(!isset($_SESSION["template"])) //Boot to homepage if card template is not set.
	{
		header("Location: index.php");
	}else 
	{
		$user = $_SESSION['user'];
		$template = $_SESSION['template'];
		$user = $_SESSION['user'];
		$admin = $_SESSION['admin'];
		$full = $_SESSION['full'];
		$master = $_SESSION['master'];
		$card = $_SESSION['card'];
		$CurCardID = $_SESSION['CurCardID'];
		$ShortFile = $_SESSION['ShortFile'];
	}

	
	$statement="SELECT * FROM Templates WHERE ID='" . $template . "'";
	$sql->QueryRow($statement);
	$numlines = $sql->data['lines'];
	$templatefile = $sql->data['Template'];
	$c_name = $sql->data['Company'];
	$pshippingcst = $sql->data['pship'];

	if($_POST['card_quality']=='p')
	{
	$sshippingcst = $sql->data['pship'];
	}ELSE{
	$sshippingcst = $sql->data['sship'];
	}


	$Picupload = $sql->data['Pic_Upload'];
	if($_POST['card_quality']=='s' || $_POST['card_quality']=='')
		$Paper = $sql->data['Paper'];
	else
		$Paper = $sql->data['Paper_premium'];
	$Ink = $sql->data['Ink'];
	$Agent = $sql->data['Agent'];
	$Rep = $sql->data['Rep'];
	if($_POST['card_quality']=='p')
	{
		$per250 = $sql->data['per250_premium'];
		$per500 = $sql->data['per500_premium'];
		$per1000 = $sql->data['per1000_premium'];
		$per2000 = $sql->data['per2000_premium'];
	}else
	{
		$per250 = $sql->data['per250'];
		$per500 = $sql->data['per500'];
		$per1000 = $sql->data['per1000'];
		$per2000 = $sql->data['per2000'];
	}
	$Quickcard = $sql->data['QuickCard_Price'];

	if($sql->data['Vertical']=='y') //Set display size variables
	{
		$dimension="width=\"294\" height=\"498\"";
		//These variables are only used for the back side of a two sided card
		$width=294;
		$height=498;
	}else{
		$dimension="height=\"294\" width=\"498\"";
		//These variables are only used for the back side of a two sided card
		$width=498;
		$height=294;
	}

	foreach($_POST as $a=>$b) //Load card array
	{
		//if($b!="")
			$card[$a]=$b;
	}
// echo('<br>--<pre>');
// print_r($card);
// echo("<br>-----<br>");
// print_r($HTTP_POST_VARS);
// echo('</pre>');

	if($card['Action']=='New' && $card['ID']=="")
	{
		$statement = "SELECT max(ID) from Finished_Cards;";
		$sql->QueryItem($statement);
		if($card['ID']=="") // If an ID has not been assigned, grab the next one in line.
		{
			$card['ID']=$sql->data[0]+1;
		}
	}

	if($Agent!="None" && $Agent!="")
	{
		echo "<!--$Agent-->\n";
		$sql->QueryRow("SELECT Email, Name, Company, ID FROM Users WHERE Rep_Code='$Agent'");
		$Agent=$sql->data[0];
		$Agent_Name=$sql->data[1];
		if($user==$sql->data['ID']) //This is so if the Agent is placing the order it will still showing pricing information online
			$bypass_bypasser='y';
		else
			$bypass_bypasser='n';
		echo "<!--$Agent-->\n";
		$sql->QueryRow("SELECT Name FROM Company WHERE ID=" . $sql->data['Company']);
		$Agent_Company=$sql->data[0];
	}else
	{
		$sql->QueryItem("SELECT Name FROM Users WHERE Rep_Code='$Rep'");
		$Rep=$sql->data[0];
	}
	//Save the Card to the DB.
	if($card['Card_Name']=="")
	{
		$statement="SELECT * FROM Finished_Cards WHERE Card_Name=\"" . $card['Line_1'] . "\" AND Template='$template'";
		$sql->Query($statement);
		//if($sql->rows>0)
		//	$card['Card_Name'] = $card['Line_1'] . " Duplicate";
		//else
			$card['Card_Name'] = $card['Line_1'];
	}
	$statement="SELECT ID, Filename FROM Finished_Cards WHERE (Card_Name=\"" . $card['Card_Name'] . "\" OR ID=" . $card['ID'] . ") and Template='" . $template . "'";
// exit("-$statement-");
	$sql->QueryRow($statement);
	//echo "<!--$statement-->\n";
	if($sql->rows>0)
	{
		$card['ID']=$sql->data['ID'];
		$statement="UPDATE Finished_Cards SET Template='" . $template . "'";
	}else{
		$statement="INSERT INTO Finished_Cards SET Template='" . $template . "'";
	}
	//Finished file name
	$file="images/finished/" . $template . "-" . $card['ID'] . ".svg";

	for($i=1; $i<=$numlines; $i++) //Run the lines for insert/update
	{
		if($_POST['Line_' . $i]!="")
			$statement .= ", Line_" . $i . "=\"" . stripslashes(str_replace("&#39;", "'", str_replace("\"", "&quot;", $_POST['Line_' . $i]))) . "\"";
		else{
			$statement .= ", Line_" . $i . "=\"" . stripslashes(str_replace("&#39;", "'", str_replace("\"", "&quot;", $card['Line_' . $i]))) . "\"";
			$_POST['Line_' . $i]=$card['Line_' . $i];
		}
		echo "<!--$i: " . stripslashes(str_replace("&#39;", "'", str_replace("\"", "&quot;", $_POST['Line_' . $i]))) . "-->\n";
	}
	//echo "<!--" . $statement . "-->\n";
	foreach($card as $a=>$b)
	{
		if($_POST[$a]=="")
			$_POST[$a]=$card[$a];
	}
	if($card['Action']=="New")
	{
		if($_POST['Card_Name']!="")
		{
			$statement .=", Card_Name=\"" . str_replace("\"", "&quot;", $_POST['Card_Name']) . "\"";
			$card['Card_Name']=$_POST['Card_Name'];
		}else
		{
			$statement .=", Card_Name=\"" . str_replace("\"", "&quot;", $_POST['Line_1']) . "\"";
			$card['Card_Name']=$_POST['Line_1'];
		}
	}elseif($card['Card_Name']!="")
		$statement .= ", Card_Name=\"" . str_replace("\"", "&quot;", $card['Card_Name']) . "\"";
	$statement .=", Quantity='" . $_POST['Quantity'] . "', Quality='" . $_POST['card_quality'] . "', Approved='n', Notes=\"" . addslashes($_POST['Notes']) . "\", Filename=\"" . $file . "\", po='" . $_POST['po'] . "', company='" . $_POST['company'] . "', name='" . $_POST['name'] . "', address1='" . $_POST['address1'] . "', address2='" . $_POST['address2'] . "', city='" . $_POST['city'] . "', state='" . $_POST['state'] . "', zip='" . $_POST['zip'] . "', speed='" . $_POST['speed'] . "', ship='" . $_POST['ship'] . "', Order_By=$user, paper='" . $Paper . "', Ink='" . $Ink . "'";
	if($_POST['hold']=='no')
	{
		if($target!="")
			$statement .= ", Date_Approved=now(), Due_Date=now()+interval 14 day";
		else
			$statement .= ", Date_Stamp=now(), Due_Date=now()+interval 14 day";
	}else
		$statement .= ", Date_Stamp=now()";
		
	if($client_status=='p')
	{
		$statement .= ", Status='P'";
	}else
	{
		if($_POST['hold']=='no')
			$statement .= ", Status='O'";
		else
			$statement .= ", Status='H'";
	}
	$rowcount=$sql->rows;

	if(isset($_POST['symbol']))
	{
		foreach($_POST['symbol'] as $a=>$b)
		{
			$symbol_line.=",$b";
			$statement2 = "SELECT Functional_Name FROM Card_Symbols WHERE ID=$b";
			$sql->QueryItem($statement2);
			$_POST['Notes'] .= "\n<br>Include Symbol: " . $sql->data[0];
		}
		$statement .= ", Symbols=\"$symbol_line,\"";
	}
	if($rowcount>0)
	{
		$statement .= " WHERE ID=" . $card['ID'] . " and Template='" . $template . "'";
		$sql->Update($statement);
	}else{
// exit("-$statement-");
		$statement .= ", ID=" . $card['ID'];
		$sql->Insert($statement);
	}

	if($_POST['Quantity']=="")
		$_POST['Quantity']=0;

	$statement = "INSERT INTO Order_History SET Order_id=" . $card['ID'] . ", Quantity=" . $_POST['Quantity'] . ", Date_Stamp=now(), Action=\"";
	if($client_status=='p')
	{
		$statement .= "Test\", ";
	}else
	{
		if($_POST['hold']=='no')
			$statement .= "Order\", ";
		else
			$statement .= "Hold\", ";
	}
	$statement .= "Done_By=\"";
	if($full=='y')
		$statement .="BizCards $user\"";
	elseif($partial=='y')
		$statement .= "Admin $user\"";
	else
		$statement .= $user . "\"";
// exit("-$statement-");
	$sql->Insert($statement);
	$card['LastAction']="";

	//This is self correction to prevent overcrowding on the server.  Purges any old cards still hanging around.
	@unlink("images/temp/$template-" . $card['ID'] . "*.svg");

	//Build modified SVG.
	$lines=file("images/template/" . $templatefile . ".svg");
	$output = fopen($file, "w");

	$j=1;
	while($j<=$numlines)
	{
		$_POST['Line_' . $j] = preg_replace('/&(amp;| *)reg;/', '®', str_replace('*', '·', htmlentities(stripslashes( str_replace("&quot;", "\"", $_POST['Line_' . $j])))));
		$j++;
	}
	foreach($lines as $a=>$b)
	{
		//Swap out ~~X~~ for the Line X value. This includes swapping out * for Bullet
		$j=1;
		while($j<=20)
		{
			$b=str_replace("~~$j~~", $_POST['Line_' . $j], $b);
			$j++;
		}

		//Swap out picture placeholders
		$b = str_replace('~~insert picture~~', $CurCardID, $b);
		$b = str_replace('~~insert shortref~~', $ShortFile, $b);
		fwrite($output, $b);
	}

	//Construct notification email.
	$statement="SELECT * FROM Users WHERE ID=" . $user;
	$sql->Queryrow($statement);
	$row=$sql->data;
	if($_POST['speed']=="y"){$randys = " + $ 20 Rush";}
	$email = $row['Email'];
	$msg = $row['name'] . " has ordered " . $_POST['Quantity'] . " Business Cards for " . stripslashes(str_replace("·", "*", str_replace("\"", "&quot;",$card['Card_Name']))) . "<br>\n-----------------------------------------------------------------------------------------<br>\n";
	$msg .= "Shipping Information:<br>\n";
	$msg .= stripslashes($_POST['company']) . "<br>\n";
	$msg .= $_POST['name'] . "<br>\n";
	$msg .= $_POST['address1'] . "<br>\n";
	if($_POST['address2']!="")
		$msg .= $_POST['address2'] . "<br>\n";
	$msg .= $_POST['city'] . ", " . $_POST['state'] . "  " . $_POST['zip'] . "<br>\n";
	$msg .="Ship By: ";
	if($_POST['ship']=="2")
		$msg .= "2 Day Air<br>\n Shipping Cost:$ $pshippingcst $randys<br>\n";
	elseif($_POST['ship']=="o")
		$msg .= "Next Day<br>\n Shipping Cost:$ $pshippingcst $randys<br>\n";
	else
		$msg .= "UPS Ground<br>\n Shipping Cost:$ $sshippingcst $randys<br>\n";
	if($_POST['po']!="")
		$msg .= "Purchase Order #: " . $_POST['po'] . "<br>\n";

	$msg .= "This card will be printed " . $Ink . " on " . $Paper . "<br>\n";
	if($_POST['Notes']!="")
		$msg .= "Extra Notes: " . $_POST['Notes'] . "<br>\n";

if(($_POST['ship'] == "2") || ($_POST['ship'] == "o"))
{
$rshipping = $pshippingcst;
}else{
$rshipping = $sshippingcst;
}

	//Price calculation
	//if($Agent=="None" || $Agent=="")
	//{
		if($_POST['Quantity']==250)
		{
			$price=$per250+$rshipping;
			$msg .= "Pricing: 250 @ $per250 + Shipping: $ $rshipping<br>\n";
		}else if($_POST['Quantity']==500)
		{
			$price=$per500+$rshipping;
			$msg .= "Pricing: 500 @ $per500 + Shipping: $ $rshipping<br>\n";
		}else if($_POST['Quantity']==1000)
		{
			$price=$per1000+$rshipping;
			$msg .= "Pricing: 1000 @ $per1000 + Shipping: $ $rshipping<br>\n";
		}else if($_POST['Quantity']==2000)
		{
			$price=$per2000+$rshipping;
			$msg .= "Pricing: 2000 @ $per2000 + Shipping: $ $rshipping<br>\n";
		}else
			$msg .="<i>You have ordered 0 regular business cards</i><br>\n";
		if($_POST['speed']=='y')
		{
			$price = $price + 20;
			$msg .= "<b>Rush Charges Apply</b> ($ 20.00)<br>\n";

		}

		$msg .= "Total: $ $price<br>\n";
	/*
	}else
	{
		if($_POST['Quantity']==250)
		{
			$msg .= "Quantity: 250 <br>\n";
		}else if($_POST['Quantity']==500)
		{
			$msg .= "Quantity: 500<br>\n";
		}else if($_POST['Quantity']==1000)
		{
			$msg .= "Quantity: 1000<br>\n";
		}else if($_POST['Quantity']==2000)
		{
			$msg .= "Quantity: 2000<br>\n";
		}else
			$msg .="<i>You have ordered 0 regular business cards</i><br>\n";
	}
	*/
	if($_POST['card_quality']=='p')
		$msg .="This is a <b>PREMIUM</b> card order.<br>\n<br>\n";
	$msg .="<br>\n<br>\nCARD SKU: " . $template . "-" . $card['ID'] . "<br>\n";
	if($Agent_Name!="")
		$msg .="AGENT: $Agent_Name - $Agent_Company<br>\n";
	else
		$msg .="REP: $Rep<br>\n";

	$statement="SELECT Approval_Email, Approval_Req, Template_Name FROM Templates WHERE id=" . $template;
	$sql->QueryRow($statement);
	$msg .="<br>TEMPLATE NAME: " . $sql->data['Template_Name'] . "<br>\n";

	$msg .="<BR><a href=http://www.bizcardstoday.com/images/finished/$template" . "-" . $card['ID'] . ".svg>SVG TO DOWNLOAD</A><BR>";

	echo "<!-- Approver: " . $sql->data[0] . "; Req: " . $sql->data[1] . "; Template Name: " . $sql->data[2] . "-->\n";
	//$testemail = "orders@bizcardstoday.com"; //Swapable email for easy testing
	$testemail = array();
	$testemail[] = "orders@bizcardstoday.com";

	if($Agent!="None" && $Agent!="")
	{
		$testemail[] = $Agent;
	}

	if($_POST['hold']=='no' && $client_status=='a') //$_POST['button']=="Place Order"
	{

		if($sql->data[1]=='y')
		{
			$msg = $row['name'] . " has ordered " . $_POST['Quantity'] . " Business Cards for " . stripslashes(str_replace("·", "*", str_replace("\"", "&quot;",$card['Card_Name']))) . "<br>\n-----------------------------------------------------------------------------------------<br>\n";
			$msg .="<br>\n<br>\nThis order is pending approval.  It can be reviewed by clicking the link below:\n<br><a href=\"http://www.bizcardstoday.com/approval.php?asdfo845asdf34=asdf3ee4r_q34w5jladf&target=" . $card['ID'] . "&234rrasd9f83t__233=zzsdfwcvoi23450adslfjaowerjoiasdf2348afjow_238\">Review and Approve this Order</a><br>\n";
			$emailer=new email_html_wa($email, $quantity . " Business Card order - Pending", $email, $email);
			$emailer->clean();
			$emailer->setheaders();
			$emailer->addmessage($msg);
			$receiver = "orders@bizcardstoday.com," . $sql->data[0];
			$receivertwo = "admin@buskirkgraphics.com," . $sql->data[0];
			echo "<!-- receiver string: $receiver -->\n";
			$emailer->sendmail($receiver);
			$emailer->sendmail($receivertwo);

$emailer->sendmail('lpweber@mac.com');
$emailer->sendmail('les.weber@gmail.com');

			$approval='y';
		}else
		{

			if(@fopen("http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $card['ID'] . ".jpg", 'r'))
			{
			$msg .="<a href=http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $card['ID'] . ".jpg>Picture to Download</a>";
			}


			if(!@fopen("http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $card['ID'] . ".jpg", 'r') && $Picupload=='y')
			{
				if(@filesize("images/uploads/" . $template . "_" . $card['ID'] . ".jpg")>1000000 && $Picupload=='y')
				{
					$msg .= "<br><b>Photo Unreadable or Over 1 MB in size.  Access via <a href=\"http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $card['ID'] . ".jpg\">http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $card['ID'] . ".jpg</a> or Server direct FTP.</b>";
					$skipattach='y';
				}else
					$skipattach='y';
			}else
				$skipattach='n';
			if($row['speed']=='y')
				$emailer=new email_html_wa($email, $quantity . " Business Card order - Complete (Rush Order)", $email, $email);
			else
				$emailer=new email_html_wa($email, $quantity . " Business Card order - Complete", $email, $email);
			$emailer->clean();
			$emailer->setheaders();
			$emailer->addmessage($msg);
			//$emailer->embed_image($file,  $template . "-" . $card['ID'] . ".svg");
			if(@fopen("http://www.bizcardstoday.com/images/finished/" . $template . ".svg", 'r'))
			{
			//	$emailer->embed_image("images/finished/" . $template . ".svg",  $template . "-Back.svg");
			}
			if(@fopen("http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $card['ID'] . ".jpg", 'r') && $skipattach!='y')
			{
			//	$emailer->embed_image("images/uploads/" . $template . "_" . $card['ID'] . ".jpg", $template . "_" . $card['ID'] . ".jpg");
			$msg .="<a href=http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $card['ID'] . ".jpg>Picture to Download</a>";
			}
			$msg .="<BR><BR>Shipping Information <BR>(Standard Rate: $row[sship])<BR>(Premium Rate: $row[pship])<BR> Shipping type for this Customer: (Standard Rate: $row[typeship])";
			$emailer->sendmail("orders@bizcardstoday.com");
			//$emailer->sendmail("webmaster@bizcardstoday.com");
			$emailer->sendmail("admin@buskirkgraphics.com");
			//$emailer->sendmail("todd@crystalcommunications.biz");
			//foreach($testemail as $a)
			//{
			//	$emailer->sendmail($a);
			//	echo "<!--$a-->\n";
			//}
			$mDay = date("Ymd");
			$statement = "INSERT INTO Order_History SET Order_id=" . $card['ID'] . ", Date_Stamp=now(), Action=\"Email\", Done_By=\"";
			$rickinsert = mysql_query("INSERT INTO cards_ordered (date_day, processed_out, process_sheet, c_name) VALUES ('$mDay', 'n', '$msg' ,'$c_name')");
			if($full=='y')
				$statement .="BizCards $user\"";
			elseif($partial=='y')
				$statement .= "Admin $user\"";
			else
				$statement .= $user . "\"";
			$sql->Insert($statement);

		}
	}
?>
<head>
	<body>
		<table border=0 cellspacing=0 cellpadding=0><tr><td><embed src="<? echo $file ?>" <? echo $dimension; ?> type="image/svg+xml"></td></tr>
			<?
				if(@fopen("http://www.bizcardstoday.com/images/finished/" . $template . ".svg", 'r'))
				{
					echo "<tr><td><embed src='images/finished/" . $template . ".svg' " . $dimension . " type=\"image/svg+xml\"></td></tr>";
				}
			?></table><br><br>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td><?
					if($_POST['hold']=="no")
						echo stripslashes($row['name']) . " has ordered " .  $_POST['Quantity'] . " Business Cards for " . stripslashes(str_replace("\"", "&quot;",str_replace("·", "*", $_POST['Line_1']))) . ".";
					else
						echo stripslashes($row['name']) . " has saved this card for future use.";
					?>
				</td>
			</tr><tr>
				<td><? if($_POST['hold']=="no") echo "<hr>"; ?></td>
			</tr><tr>
				<td><? if($_POST['hold']=="no") echo "To be printed " . $Ink . " on " . $Paper; ?></td>
			</tr><tr>
				<?
					if($Notes!="" && $_POST['hold']=="no")
						echo "<td>Notes: $Notes</td>\n</tr><tr>\n";
				?>
				<td><? if($_POST['hold']=="no") echo "<u>Shipping Information</u>"; ?></td>
			</tr><tr>
				<td><? if($_POST['hold']=="no") echo stripslashes($_POST['company']); ?></td>
			</tr><tr>
				<td><? if($_POST['hold']=="no") echo $_POST['name']; ?></td>
			</tr><tr>
				<td><? if($_POST['hold']=="no")
					{
						echo $_POST['address1'];
						if($_POST['address2']!="")
							echo $_POST['address2'];
					}
					?></td>
			</tr><tr>
				<td><? if($_POST['hold']=="no") echo $_POST['city'] . ", " . $_POST['state'] . " " . $_POST['zip']; ?></td>
			</tr><tr>
				<td><br><? if($_POST['hold']=="no" && $_POST['po']!="") echo "Purchase Order #: " . $_POST['po']; ?></td>
			</tr><?
				if($_POST['hold']=="no")
				{
					echo "<tr>\n\t<td>";
					if($_POST['ship']=='o')
					{
						echo " Ship By Next Day delivery";
					}else if($_POST['ship']=='2')
					{
						echo " Ship By 2-Day Air delivery";
					}
					echo "</td>\n</tr>";
				}
				if($_POST['speed']=='y' && $_POST['hold']=='no')
				{
					echo "<tr>\n\t<td><b>Rush Charges Apply</b> ($ 20.00)</td>\n</tr>";
				}
				?>
			<TR><td><HR><? $msg = str_replace("SVG TO DOWNLOAD", "","$msg"); echo $msg; ?><HR></td></tr>
			<tr>
				<td><? if(($_POST['hold']=="no" && ($Agent=="None" || $Agent=="")) || $bypass_bypasser=='y') echo "<hr><br><br>Your Total: " . $price . ""; ?></td>
			</tr><tr>
				<td><? if($_POST['po']!="")echo "Purchase Order #: " . $_POST['po'] . "<br>\n";?></td>
			</tr><tr>
				<td>CARD SKU: <? echo $template; ?>-<? echo $card['ID']; ?></td>
			</tr><tr>
			<?
				if($_POST['hold']=="no" && $approval=='y')
				{
					echo "<tr>\n\t<td>This order has been forwarded to ";
					$statement="SELECT Approval_Email FROM Templates WHERE ID=" . $template;
					$sql->QueryRow($statement);
					echo $sql->data[0] . " for final approval.</td>\n</tr>";
				}
				if($_POST['hold']=="no" && $client_status=='p')
					echo "<tr>\n\t<td align=center><font color=red size=+2>This is a test order.  No actual order has been placed.</font></td>\n\t</tr>\n";
			?>
			<tr>
				<td align=center><table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<? $card['Action']='reorder'; //Make sure a direct return to the card order pulls up reorder functionality instead of new card.
							     $card['Card_Name']=''; //Force the Card name to reset ?>
							<td width=33% align=center><? if($target=="") echo "<input type=button value=\"Reorder Another\" onclick=\"window.location='editpad.php'\">"; else echo "<input type=button value=\"Edit This Card\" onclick=\"window.location='editpad.php'\">"; ?></td>
							<td width=33% align=center><input type=button value="Logout" onclick="window.location='index2.php'"></td>
							<td width=33% align=center><input type=button value="Return to Menu" onclick="window.location='welcome.php'"></td>
						</tr>
				</table></td>
			</tr>
		</table>
	</body>
</head>
