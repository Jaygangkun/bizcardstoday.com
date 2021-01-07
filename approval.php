<?php
	session_start();
	if($_GET['asdfo845asdf34']!='asdf3ee4r_q34w5jladf' || $_GET['234rrasd9f83t__233']!=zzsdfwcvoi23450adslfjaowerjoiasdf2348afjow_238)
	{
		if($_GET['asdfo845asdf34']!='asdf3ee4r_q34w5jladf' && $_GET['234rrasd9f83t__233']!=zzsdfwcvoi23450adslfjaowerjoiasdf2348afjow_238)
			header("Location: index2.html");
	}
	require("util.php");
	require("emailer.php"); //email wrapper
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");

	if($_REQUEST['button']=='Approve')
	{
		$sql->Update("UPDATE Finished_Cards Set Approved='y' WHERE ID=" . $HTTP_POST_VARS['target']);
	}else if($_REQUEST['button']=='Disapprove')
	{
		$sql->Update("UPDATE Finished_Cards SET Approved='d' WHERE ID=" . $_POST['target']);
	}

	$target=$_REQUEST['target'];
	if($HTTP_POST_VARS['po']!="")
		$sql->Update("UPDATE Finished_Cards SET po='" . $HTTP_POST_VARS['po'] . "' where ID=" . $target);

	$statement="SELECT * FROM Finished_Cards WHERE ID='" . $target . "'";
	$sql->QueryRow($statement);
	$row = $sql->data;

	$Notes = $sql->data['Notes'];
	$Card_Name = $sql->data['Card_Name'];
	$Quantity = $sql->data['Quantity'];
	$Company = $sql->data['company'];
	$order_by = $sql->data['Order_By'];
	$name = $sql->data['name'];
	$address1 = $sql->data['address1'];
	$address2 = $sql->data['address2'];
	$city = $sql->data['city'];
	$state = $sql->data['state'];
	$zip = $sql->data['zip'];
	$po = $sql->data['po'];
	if($HTTP_GET_VARS['po']!="")
		$po=$HTTP_GET_VARS['po'];
	$speed = $sql->data['speed'];
	$ship = $sql->data['ship'];
	$file=$sql->data['Filename'];
	$template = $sql->data['Template'];
	$approval = $sql->data['Approved'];
	$Paper=$sql->data['Paper'];
	$statement = "SELECT * FROM Templates where ID=" . $template;
	$sql->QueryRow($statement);
	if($row['Quality']=='p')
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


	if($row['Vertical']=='y') //Set display size variables
	{
		$dimension="width=\"294\" height=\"498\"";
	}else{
		$dimension="height=\"294\" width=\"498\"";
	}

	//Construct notification email.
	$msg = "An order for " . $Quantity . " Business Cards.<br>\n-----------------------------------------------------------------------------------------<br>\n";
	$msg .= "Shipping Information:<br>\n";
	$msg .= $Company . "<br>\n";
	$msg .= $name . "<br>\n";
	$msg .= $address1 . "<br>\n";
	if($address2!="")
		$msg .= $address2 . "<br>\n";
	$msg .= $city . ", " . $state . "  " . $zip . "<br>\n";
	$msg .="Ship By: ";
	if($ship=="2")
		$msg .= "2 Day Air<br>\n";
	elseif($ship=="o")
		$msg .= "Next Day<br>\n";
	else
		$msg .= "UPS Ground<br>\n";
	if($po!="")
		$msg .= "Purchase Order #: " . $po . "<br>\n";

	if($speed=='y')
		$msg .= "<br>\n<b>Rush Charges apply</b><br>\n";

	$statement="SELECT Ink, QuickCard_Price, Template_Name, Approval_Email from Templates where ID=" . $template;
	$sql->QueryRow($statement);
	$Ink=$sql->data['Ink'];
	$Quickcard = $sql->data['QuickCard_Price'];
	$email = $sql->data['Approval_Email'];
	$template_name=$sql->data['Template_Name'];
	if($Quantity==250)
	{
		$price=$per250;
		$msg .= "Pricing: 250 @ $per250<br>\n";
	}else if($Quantity==500)
	{
		$price=$per500;
		$msg .= "Pricing: 500 @ $per500<br>\n";
	}else if($Quantity==1000)
	{
		$price=$per1000;
		$msg .= "Pricing: 1000 @ $per1000<br>\n";
	}else if($Quantity==2000)
	{
		$price=$per2000;
		$msg .= "Pricing: 2000 @ $per2000<br>\n";
	}else
		$msg .="<i>You have ordered 0 regular business cards</i><br>\n";
	if($speed=='y')
	{
		$price+=$Quickcard;
		$msg .= "<b>Rush Charges Apply</b> (&#36;$Quickcard)<br>\n";
	}
	$msg .= "Total: $price + Shipping<br>\n";

	$msg .= "<br><br>This card will be printed on " . $Paper . " with " . $sql->data['Ink'] . "<br>\n";
	if($Notes!="")
		$msg .= "Extra Notes: " . $Notes . "<br><br>\n\n";

	$msg .="<br>\nCARD SKU: " . $template . "-" . $_REQUEST['target'] . "<br>\n";
	$msg .="<br>\nTemplate Name: $template_name<br>\n";
	$testemail = "orders@bizcardstoday.com, admin@buskirkgraphics.com"; //Swapable email for easy testing

	if($approval=='y')
	{
		$msg .="<br>\n<br>\nTHIS ORDER HAS BEEN APPROVED<br>\n";
		if($speed=='y')
			$sub_line=$quantity . "Business Card Order - Finalized(Approved) w/ Rush Charges";
		else
			$sub_line=$quantity . " Business Card order - Finalized(Approved)";
		$emailer=new email_html_wa($email, $sub_line, $email, $email);
		$emailer->clean();
		$emailer->setheaders();
		$emailer->addmessage($msg);
		$emailer->embed_image($file,  $template . "-" . $_REQUEST['target'] . ".svg");
		if(@fopen("http://www.bizcardstoday.com/images/finished/" . $template . ".svg", 'r'))
		{
			$emailer->embed_image("images/finished/" . $template . ".svg",  $template . "-Back.svg");
		}
		if(@fopen("http://www.bizcardstoday.com/images/uploads/" . $template . "_" . $_REQUEST['target'] . ".jpg", 'r'))
		{
			$emailer->embed_image("images/uploads/" . $template . "_" . $_REQUEST['target'] . ".jpg", $template . "_" . $_REQUEST['target'] . ".jpg");
		}
		$emailer->sendmail($testemail);
		$approval='y';
	}else if($approval=='d'){
		$statement= "SELECT Email, name FROM Users WHERE ID=$order_by";
		$sql->QueryRow($statement);

		$msg = "YOUR BIZCARDSTODAY.COM ORDER HAS BEEN DENIED<br>\n";
		$msg .= $sql->data['name'] . ", your card order ($Card_Name) has been denied by Purchasing.  Please contact them at $email.";
		$emailer=new email_html_wa($email, $quantity . " Business Card order - Finalized(Denied)", $email, $email);
		$emailer->clean();
		$emailer->setheaders();
		$emailer->addmessage($msg);
		/*
		$emailer->embed_image($file,  $template . "-" . $_REQUEST['target'] . ".svg");
		if(@fopen("http://www.bizcardstoday.com/images/finished/" . $template . ".svg", 'r'))
		{
			$emailer->embed_image("images/finished/" . $template . ".svg",  $template . "-Back.svg");
		}
		*/
		$emailer->sendmail($testemail);
		$approval='d';
	}
?>
<head>
	<!--<meta http-equiv="REFRESH" content="0;URL=proof.php">-->
	<body>
		<form action=approval.php method=post>
		<input type=hidden value='asdf3ee4r_q34w5jladf' name='asdfo845asdf34'>
		<input type=hidden value='zzsdfwcvoi23450adslfjaowerjoiasdf2348afjow_238' name='234rrasd9f83t__233'>
		<input type=hidden value="<? echo $_REQUEST['target']; ?>" name=target>
		<?
			if($approval=='y')
				echo "<font color='red'>THIS ORDER HAS BEEN APPROVED</font><br>\n";
			else if($approval=='n')
				echo "<font color='red'>THIS ORDER IS WAITING ON YOUR APPROVAL</font><br>\n";
			else if($approval=='d')
				echo "<font color='red'>THIS ORDER HAS BEEN DENIED</font><br>\n";
		?>
		<table border=0 cellspacing=4 cellpadding=3><tr><td><embed src="<? echo $file ?>" <? echo $dimension ?> type="image/svg"></td>
			<td><br>Please check spelling, grammar, and punctuation carefully when reviewing your online proofs. We are not responsible for mistakes that you approve.<br><br>If your card does not appear, please <a href="CardViewer.exe" target="_blank">click here</a>.  You will be prompted to Open or Save the file, select Open and then after you've been told installation was successful, Refresh the page.</td>
			</tr>
		</table><br><br>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td>An order has been placed with <b>BizCardsToday.com</b><br> for <? echo $Quantity  ?> Business Cards</td>
			</tr><tr>
				<td><hr></td>
			</tr><tr>
				<td>To be printed <? echo $Ink; ?> on <? echo $Paper; ?>.</td>
			</tr><tr>
				<?
					if($Notes!="")
						echo "<td>Extra Notes: $Notes</td>\n</tr><tr>\n";
				?>
				<td><u>Shipping Information</u></td>
			</tr><tr>
				<td><? echo $Company ?></td>
			</tr><tr>
				<td><? echo $name ?></td>
			</tr><tr>
				<td><?
						echo $address1;
						if($address2!="")
							echo $address2;
					?></td>
			</tr><tr>
				<td><? echo $city . ", " . $state . " " . $zip ?></td>
			</tr><tr>
				<td><br>Purchase Order #: <input type=text value='<? echo $po ?>' name=po></td>
			</tr><?
				echo "<tr>\n\t<td>";
				if($ship=='o')
				{
					echo " Ship By: Next Day";
				}else if($ship=='2')
				{
					echo " Ship By: 2-Day Air";
				}else
					echo " Ship By: UPS Ground";
				echo "</td>\n</tr>";
				if($speed=='y')
				{
					echo "<tr>\n\t<td><b>Rush Charges Apply</b> (&#36;$Quickcard)</td>\n</tr>";
				}
				if($Quantity==250)
				{
					echo "<tr>\n\t<td>Pricing: 250 @ &#36;$per250 + Shipping</td>\n</tr>";
				}else if($Quantity==500)
				{
					echo "<tr>\n\t<td>Pricing: 500 @ &#36;$per500 + Shipping</td>\n</tr>";
				}else if($Quantity==1000)
				{
					echo "<tr>\n\t<td>Pricing: 1000 @ &#36;$per1000 + Shipping</td>\n</tr>";
				}else if($Quantity==2000)
				{
					echo "<tr>\n\t<td>Pricing: 2000 @ &#36;$per2000 + Shipping</td>\n</tr>";
				}else
					echo "<tr>\n\t<td><i>You have ordered 0 regular business cards</i></td>\n</tr>";
				?>
			<tr>
				<td align=center><table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td width=50% align=center><input type=submit name=button value="Approve" <? if($approval=='d') echo "disabled"; ?>></td>
							<td width=50% align=center><input type=submit name=button value="Disapprove" <? if($approval=='y') echo "disabled"; ?>></td>
						</tr>
				</table></td>
			</tr>
		</table>
		</form>
	</body>
</head>