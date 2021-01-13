<?php
	session_start();
// 	if($template==0 || !session_is_registered("template") || $template=="")
	// if(!session_is_registered("template")) //Boot to homepage if card template is not set.
	if(!isset($_SESSION["template"])) //Boot to homepage if card template is not set.
		header("Location: index2.php");
	
	require("util.php");
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");
		
	// if(!session_is_registered("PDF"))
	if(!isset($_SESSION["PDF"]))
	{
		// session_register("PDF");
		$_SESSION['PDF'] = '';
	}
	
	$PDF= array();
	
	$statement = "SELECT max(ID) FROM PDF_Uploads";
	$sql->QueryItem($statement);
	if($sql->data[0]=="")
	{
		$PDF['ID']=1;
	}else
		$PDF['ID']=($sql->data[0]+1);
	
	$PDF['Template']=$template;
	$PDF['Quantity']=$_POST['quantity'];
	$PDF['Quality']=$_POST['type'];
	if($PDF['Quality']=="Brite White" || $PDF['Quality']=="Laid" || $PDF['Quality']=="Linen" || $PDF['Quality']=="Fiber")
	{
		if($_POST['sides']=="Dual")
		{
			$PDF['Quality'] = ucwords($_POST['color']) . " " . $PDF['Quality'] . " 3/2";
			$PDF['2_Sided']='2';
		}else{
			$PDF['Quality'] = ucwords($_POST['color']) . " " . $PDF['Quality'] . " 3/0";
			$PDF['2_Sided']='0';
		}
	}else
	{
		if(substr($PDF['Quality'], -1)=="B" || substr($PDF['Quality'], -1)=="1" || substr($PDF['Quality'], -1)=="4")
			$PDF['2_Sided']=substr($PDF['Quality'],-1);
		else
			$PDF['2_Sided']='0';
	}
		
	/*
	if($_POST['sides']!="")
		$PDF['2-sided']=$_POST['back'];
	else
		$PDF['2-sided']='0';
	*/
	
	$statement="SELECT * FROM Users WHERE ID=" . $user;
	$sql->Queryrow($statement);
	$row=$sql->data;
	
	$statement="SELECT c.* FROM Company c, Templates t WHERE t.id=$template AND c.ID=t.company;";
	$sql->QueryRow($statement);
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>BizCardsToday.com PDF Upload Utility - Step 2</title>
	</head>

	<body bgcolor="#ffffff">
		<form action="pdffinal.php" method=post ENCTYPE="multipart/form-data">
		<p><?
				$confirmation = "You have specified that you wish to order a " . $PDF['Quantity'] . " count shipment of our " . $PDF['Quality'] . " cards.";			
				echo $confirmation;
			?>
		</p>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<th colspan=2>Shipping Information</th>
			</tr><tr>
				<td colspan=2><b>Purchase Order Number</b>:</td>
			</tr><tr>
				<td colspan=2><input type=text size=35 name=po></td>
			</tr><tr>
				<td colspan=2>Preferred Shipping Method: <input type=radio name="ship" value='s' checked> UPS Ground <input type=radio name="ship" value='o'> Next Day <input type=radio name="ship" value='2'> 2 Day Air </td>
			</tr><tr>
				<td>Company:</td><td><input type=text name=company size=35 value='<? echo $sql->data['Name']; ?>'></td>
			</tr><tr>
				<td>Name:</td><td><input type=text name=name size=35 value='<? echo $row['Name']; ?>'></td>
			</tr><tr>
				<td>Address 1:</td><td><input type=text name=address1 size=35 value='<? echo $sql->data['Address1']; ?>'></td>
			</tr><tr>
				<td>Address 2:</td><td><input type=text name=address2 size=35 value='<? echo $sql->data['Address2']; ?>'></td>
			</tr><tr>
				<td colspan=2>City: <input type=text name=city size=20 value='<? echo $sql->data['City']; ?>'> State: <input type=text name=state size=3 maxlength=2 value='<? echo $sql->data['State']; ?>'> Zip:<input type=text name=zip size=10 maxlength=10 value='<? echo $sql->data['Zip']; ?>'></td>
			</tr></tr>
				<td colspan=2>Special Instructions:<br><textarea name=Notes rows=3 cols=75></textarea></td>
			</tr><tr>
				<td colspan=2>PDF File of Card: <input type=file name=pdf_upload[]></td>
			</tr><?
				if($_POST['sides']=="Dual")
					echo "<tr>\n\t\t\t\t<td colspan=2>PDF File of Card Back: <input type=file name=pdf_upload[]></td>\n\t\t\t</tr>";
				?><tr>
				<td><input type=submit value="Upload PDFs and Complete Order"></td>
				<td><input type=button value="Return to Main Menu" onclick="window.location='welcome.php'"></td>
			</tr>
		</table>
		</form>
	</body>

</html>