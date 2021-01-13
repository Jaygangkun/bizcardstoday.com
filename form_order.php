<?php
	//This page will add Letterhead and Envelope ordering functionality (duplicated from BizForms) to Bizcards.
	session_start();
// 	if($template==0 || !session_is_registered("template") || $template=="") //Boot to homepage if card template is not set.
	// if(!session_is_registered("template")) //Boot to homepage if card template is not set.
	if(!isset($_SESSION["template"])) //Boot to homepage if card template is not set.
		header("Location: index2.php");
	
	require("util.php"); // db wrapper
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	// if(!session_is_registered("proof_local"))
	if(!isset($_SESSION["proof_local"]))
	{
		// session_register("proof_local");
		// session_register("form");

		$_SESSION['proof_local'] = '';
		$_SESSION['form'] = '';
	}
	
	if($_POST['form_id']!="")
		$form=$_POST['form_id'];
	else
	{
		$statement = "SELECT * FROM Forms WHERE Template=$template ORDER BY Form_Name";
		$sql->QueryRow($statement);
		$form=$sql->data['ID'];
	}
	echo "<!--$form-->\n";
	$statement = "SELECT * FROM Forms WHERE ID=" . $form;
	$sql->QueryRow($statement);
	$proof_local['Low_Quantity']=$sql->data['Low_Quantity'];
	$proof_local['Default_Quantity']=$sql->data['Default_Quantity'];
	$proof_local['High_Quantity']=$sql->data['High_Quantity'];
	$proof_local['Filename']=$sql->data['Filename'];
	$proof_local['Form_Name']=$sql->data['Form_Name'];
	$temp=getimagesize("images/JPG/" . $proof_local['Filename'] . ".jpg");
	$proof_local['Width']=$temp[0];
	$proof_local['Height']=$temp[1];
	$proof_local['ID']=$sql->data['ID'];
	$proof_local['2Sided']=$sql->data['2Sided'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>BizCardsToday.com Letterhead &amp; Envelope Order page</title>
		<script language=javascript>
		<!--
		function OnButton1()
		{	//Redraw Card
			document.Form1.action = "form_order.php"
			document.Form1.submit();			
			return true;
		}
		
		function OnButton2()
		{	//Place Order
			document.Form1.action = "complete.php"
			document.Form1.elements['hold'].value = false
			document.Form1.submit();			
			return true;
		}
		
		function OnButton4()
		{	//Hold Order
			document.Form1.action = "complete.php"
			document.Form1.elements['hold'].value = true
			document.Form1.submit();
			return true;
		}
		
		function OnButton5()
		{	//Cancel Order
			document.Form1.action = "welcome.php"
			document.Form1.submit();
			return true;
		}
		-->
		</script>
	</head>

	<body bgcolor=#ffffff leftmargin=5px>
		<table border=0 cellspacing=5 cellpadding=5>
		<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=0><tr><td><input type=button value="View Form" onclick="window.open('formview.php?target=<? echo $proof_local['ID'] ?>', 'view', 'width=<? echo $proof_local['Width'] ?>,height=<? echo $proof_local['Height']; ?>')"></td></tr>
				<?
					if($proof_local['2Sided']=='y')
						echo "<tr><td><input type=button value=\"View Form Back\" onclick=\"window.open('formview.php?target=" . $proof_local['ID'] . "&back=1', 'view', 'width=" . $proof_local['Width'] . ", height=" . $proof_local['Height'] . "')\"></td></tr>";
				?></table>
			</td>
			<td>Welcome <? echo $name; ?><br><br>This is the design approved for this form (<b><? echo $proof_local['Form_Name'] ?></b>) prepared with the most recent information in our database. If the form design has changed, or if your department uses a variation not shown here, please contact us immediately.<br><br>Please check shipping information and order quantity carefully when reviewing your online proofs. We are not responsible for mistakes that you approve.<br><br></td>
		</tr>
		</table>
		<table border=0 cellspacing=5 cellpadding=5>
			<form action="form_order.php" method=POST name=Form1 ENCTYPE="multipart/form-data">
			<input type=hidden name=hold value=false>
			<input type=hidden name=form value=<? echo $form ?>>
			<!--<tr>
				<td colspan=2><b>To Start A New Card or to Make Changes</b> simply replace the text in the boxes below and hit <b>Redraw Form</b>. <br>Some lines may be locked to prevent changes to fixed information such as company address. <br>Use an asterisk(*) to insert a bullet(&#149;)</td>
			</tr>-->
			<tr>
				<td align=left valign=top>
					<?
						//Place Date/Time stamp on last order
						if($proof_local['Date_Stamp']!="")
						{
							$temp = explode(" ", $proof_local['Date_Stamp']);
							$temp2=explode("-", $temp[0]);
							$temp3 = explode(":", $temp[1]);
							$var = mktime($temp3[0]-5, $temp3[1], $temp3[2]);
							echo "Last order of this form placed on<br>" . $temp2[1] . "/" . $temp2[2] . "/" . $temp2[0] . " " . date('g:i a', $var) . "<input type=hidden value='" . $proof_local['Date_Stamp'] . "' name=Date_Stamp><br>\n";
						}
						//Pop Reorder select box if more than one form exists for this company
						$statement = "SELECT ID, Form_Name FROM Forms where Template=" . $template . " AND Form_Name!='' order by Form_Name";
						$sql->Query($statement);
						if($sql->rows!=0)
						{
							echo "<br><select name=form_id size=1 onchange='submit();'>\n<option value=0>Reorder Form</option>\n";
							$j=0;
							while($j<$sql->rows)
							{
								$sql->Fetch($j);
								if($sql->data[1]!="")
									echo "<option value='" . $sql->data[0] . "'>" . $sql->data[1] . "</option>\n";
								$j++;
							}
							echo "</select>\n<br><input type=submit value=\"Redraw Form\" name=button onclick=\"return OnButton1();\">";
						}
					?>	
				</td>
			</tr>
			<tr>
				<td colspan=2 valign=top bgcolor=#D1D1D1><table border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td colspan=4 valign=top><b>How Many Copies</b> Would You Like To Order</td>
					</tr>
					<tr>
						<?
							if($proof_local['Low_Quantity']!="0")
							{
								echo "<td><input type=radio value=" . $proof_local['Low_Quantity'] . " name=Quantity ";
								if($proof_local['Quantity']==$proof_local['Low_Quantity']) 
									echo "checked";
								echo "> " . $proof_local['Low_Quantity'] . "</td>\n";
							}
						?>
						<?
							if($proof_local['Default_Quantity']!="0")
							{
								echo "<td><input type=radio value=" . $proof_local['Default_Quantity'] . " name=Quantity ";
								if($proof_local['Quantity']==$proof_local['Default_Quantity'] || $proof_local['Quantity']=="") 
									echo "checked"; 
								echo "> " . $proof_local['Default_Quantity'] . "</td>\n";
							}
						?>
						
						<?
							if($proof_local['High_Quantity']!="0")
							{
								echo "<td><input type=radio value=" . $proof_local['High_Quantity'] . " name=Quantity ";
								if($proof_local['Quantity']==$proof_local['High_Quantity']) 
									echo "checked"; 
								echo "> " . $proof_local['High_Quantity'] . "</td>\n";
							}
						?>
						
					</tr>
				</table></td>
			</tr>
			<tr>
				<td colspan=2 bgcolor=#D1D1D1><table border=0 cellspacing=2 cellpadding=0>
					<tr>
						<td colspan=2><b>Purchase Order Number</b>:</td>
					</tr><tr>
						<td colspan=2><input type=text size=35 name=po value="<? echo $proof_local['po'] ?>"></td>
					</tr><tr>
						<td colspan=2><br>Shipping Information - Your order will be shipped to the address below.  <br>Make any changes needed and click on <b>Place Order</b></td>
					</tr><tr>
						<td colspan=2>Shipping Type: <input type=radio name="Ship_Type" value='s' <? if($proof_local['Ship_Type']=='s' || $proof_local['Ship_Type']=='') echo "checked"; ?>> UPS Ground <input type=radio name="Ship_Type" value='o' <? if($proof_local['Ship_Type']=='o') echo "checked"; ?>> Next Day <input type=radio name="Ship_Type" value='2' <? if($proof_local['Ship_Type']=='2') echo "checked"; ?>> 2 Day Air </td>
					</tr><tr>
						<td>Company:</td><td><input type=text name=company value="<? echo $proof_local['Company']; ?>" size=35></td>
					</tr><tr>
						<td>Name:</td><td><input type=text name=name value="<? echo $proof_local['Name']; ?>" size=35></td>
					</tr><tr>
						<td>Address 1:</td><td><input type=text name=address1 value="<? echo $proof_local['Address1']; ?>" size=35></td>
					</tr><tr>
						<td>Address 2:</td><td><input type=text name=address2 value="<? echo $proof_local['Address2']; ?>" size=35></td>
					</tr><tr>
						<td colspan=2>City: <input type=text name=city value="<? echo $proof_local['City']; ?>" size=20> State: <input type=text name=state value='<? echo $proof_local['State']; ?>' size=3 maxlength=2> Zip:<input type=text name=zip value='<? echo $proof_local['Zip']; ?>' size=10 maxlength=10></td>
					</tr></tr>
						<td colspan=2>Special Instructions:<br><textarea name=Notes rows=3 cols=75><? echo $proof_local['Notes']; ?></textarea></td>
					</tr>
				</table></td>
			</tr>
				<?
					echo "\t\t\t\t<td><table border=0 cellspacing=0 cellpadding=0><tr><td><input type=submit value='Place Order' name=button  onclick='return OnButton2();'></td><td><input type=submit value='Return to Menu' name=button onclick='return OnButton5();'></td></tr></table></td>\n";
				?>
			</tr>
			</form>
		</table>		
	</body>

</html>