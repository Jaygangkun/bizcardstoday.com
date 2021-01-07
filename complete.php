<?php
/**********************************************************************************************************************/
/*	Project: BizCardsToday.com															*/
/*	Programmer: Jason Hosler															*/
/*	Purpose: This is the order placement/confirmation page.  It must accomplish three actions: 		*/
/*		1)	Insert the order information into the database									*/
/*		2)	Build the email with PDF attachement											*/
/*		3)	Send confirmation email														*/
/*		The page should display the built SVG and the shipping information						*/
/**********************************************************************************************************************/
	session_start();
	require("util.php");//db wrapper
	require("emailer.php"); //email wrapper
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");

	if($form=="")
		$form=$_POST['form'];

	$statement="SELECT * FROM Forms WHERE ID='" . $form . "'";
	$sql->QueryRow($statement);
	$row=$sql->data;
	$form_local=$row;

	//Finished file name
	$file="images/JPG/" . $row['Filename'] . ".jpg";

	//Save the Order to the DB.
	$statement="INSERT INTO Form_Orders SET Form=" . $form . ", Template=" . $template . ", Quantity=" . $_POST['Quantity'] . ", Price=";
	if($_POST['Quantity']==$row['Low_Quantity'])
		$statement .= $row['Low_Price'];
	elseif($_POST['Quantity']==$row['Default_Quantity'])
		$statement .= $row['Default_Price'];
	elseif($_POST['Quantity']==$row['High_Quantity'])
		$statement .= $row['High_Price'];
	else
		$statement .= "9999.99";
	$statement .= ", Notes=\"" . $_POST['Notes'] . "\", Printer=\"" . $row['Printer_Email'] . "\", Address1=\"" . $_POST['address1'] . "\", Address2=\"" . $_POST['address2'] . "\", City=\"" . $_POST['City'] . "\", State=\"" . $_POST['State'] . "\", Zip=\"" . $_POST['Zip'] . "\", Ship_Type=\"" . $_POST['Ship_Type'] . "\", Date_Stamp=now(), PO=\"" . $_POST['po'] . "\", Name=\"" . $_POST['name'] . "\";";
	$sql->Insert($statement);

	//Construct notification email.
	$statement="SELECT * FROM Users WHERE ID=" . $user;
	$sql->Queryrow($statement);
	$row=$sql->data;

	$email = $row['Email'];
	$msg = $row['name'] . " has ordered " . $HTTP_POST_VARS['Quantity'] . " copies of the " . $form_local['Form_Name'] . "<br>\n-----------------------------------------------------------------------------------------<br>\n";
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

	//$statement="SELECT Paper, Ink, Notes from Finished_Cards where ID=$cur_card";
	//$sql->QueryRow($statement);
	//$msg .= "This card will be printed on " . $sql->data['Paper'] . " with " . $sql->data['Ink'] . "<br>\n";
	if($_POST['Notes']!="")
		$msg .= "Extra Notes: " . $_POST['Notes'] . "<br>\n";

	//Price calculation
	if($HTTP_POST_VARS['Quantity']==$form_local['Low_Quantity'])
	{
		$price = $form_local['Low_Price'];
		$msg .= "Pricing: " . $form_local['Low_Quantity'] . " @ " . $form_local['Low_Price'] . "<br>\n";
	}elseif($HTTP_POST_VARS['Quantity']==$form_local['Default_Quantity'])
	{
		$price = $form_local['Default_Price'];
		$msg .= "Pricing: " . $form_local['Default_Quantity'] . " @ " . $form_local['Default_Price'] . "<br>\n";
	}elseif($HTTP_POST_VARS['Quantity']==$form_local['High_Quantity'])
	{
		$price = $form_local['High_Price'];
		$msg .= "Pricing: " . $form_local['High_Quantity'] . " @ " . $form_local['High_Price'] . "<br>\n";
	}

	$testemail = "orders@bizformstoday.com, admin@buskirkgraphics.com"; //Swapable email for easy testing
	if($HTTP_POST_VARS['button']=="Place Order" && $client_status=='a')
	{
		$emailer=new email_html_wa($email, $form_local['Form_Name'] . " order - Complete", $email, $email);
		$emailer->clean();
		$emailer->setheaders();
		$emailer->addmessage($msg);
		$emailer->embed_image($file,  $template . "-" . $form . ".svg");
		$emailer->sendmail($testemail);
	}
?>
<head>
	<body>
		<table border=0 cellspacing=0 cellpadding=0><tr><td><img src="<? echo $file ?>"></td></tr>
			</table><br><br>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td><?
						echo $msg;
					?>
				</td>
			</tr>
			<?
				if($target!="")
				{
					echo "<tr>\n\t<td>This order has been approved</td>\n</tr>\n";
				}
				if($approval=='y')
				{
					echo "<tr>\n\t<td>This order has been forwarded to ";
					$statement="SELECT Approval_Name, Approval_Email FROM Templates WHERE ID=" . $template;
					$sql->QueryRow($statement);
					echo $sql->data[0] . " at " . $sql->data[1] . " for final approval.</td>\n</tr>";
				}
				if($client_status=='p')
					echo "<tr>\n\t<td align=center><font color=red size=+2>This is a test order.  No actual order has been placed.</font></td>\n\t</tr>\n";
			?>
			<tr>
				<td align=center><table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td width=33% align=center><a href="form_order.php?reorder=y"><? if($target=="") echo "Order Another"; else echo "Edit This Form"; ?></a></td>
							<td width=33% align=center><a href="index2.php">Logout</a></td>
							<td width=33% align=center><a href="welcome.php">Return to Menu</a></td>
						</tr>
				</table></td>
			</tr>
		</table>
	</body>
</head>