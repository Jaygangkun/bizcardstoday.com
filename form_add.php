<?php
	session_start();
// 	if($template==0 || !session_is_registered("template") || $template=="") //Boot to homepage if card template is not set.
	// if(!session_is_registered("template")) //Boot to homepage if card template is not set.
	if(!isset($_SESSION["template"])) //Boot to homepage if card template is not set.
		header("Location: index2.php");
	require("util.php");
	
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");
	// if(!session_is_registered("form_local"))
	if(!isset($_SESSION["form_local"]))
		// session_register("form_local");
		$_SESSION['form_local'] = '';
	
	if($form_local['Form_Name']!="")
	{
		$statement = "SELECT * FROM Forms WHERE ID=" . $form_local['ID'];
		$sql->Query($statement);
		if($sql->rows>0)
			$statement = "UPDATE Forms SET ";
		else
			$statement = "INSERT INTO Forms SET ";
		foreach($form_local as $a=>$b)
		{
			if($a!="Filename" && !preg_match("/[0-9]./", $a) && $a!="from")
				$statement .= $a . "=\"$b\", ";
		}
		$statement .= " Filename=\"" . $form_local['Filename'] . "\"";
		if($sql->rows>0)
			$sql->Update($statement);
		else
			$sql->Insert($statement);
	}
	
	$form_local['from']="form_add";
	$form_local['Template']=$template;
	
?>
<html>
	<head>
		<title>BizFormsToday.com Form Uploader</title>
	</head>
	
	<body>
		<form action=uploadmulti.php method=post enctype="multipart/form-data" name=Form1>
		<input type=hidden name=submitted value=true>
		<input type=hidden name=Template value="<? echo $form_local['Template']; ?>">
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td>Form Name:<input type=text name=Form_Name  value="<? echo $form_local['Form_Name']; ?>" width=150></td>
			</tr><tr>
				<td><b>Pricing:</b><br><table border=1 cellspacing=0 cellpadding=0>
					<tr>
						<th align=center>&nbsp;</th>
						<th align=center>Quantity</th>
						<th align=center>Price</th>
					</tr><tr>
						<td nowrap>Low: </td>
						<td><input type=text name=Low_Quantity value="<? echo $form_local['Low_Quantity']; ?>"></td>
						<td><input type=text name=Low_Price value="<? echo $form_local['Low_Price']; ?>"></td>
					</tr><tr>
						<td nowrap>Default:</td>
						<td><input type=text name=Default_Quantity value="<? echo $form_local['Default_Quantity']; ?>"></td>
						<td><input type=text name=Default_Price value="<? echo $form_local['Default_Price']; ?>"></td>
					</tr><tr>
						<td nowrap>High: </td>
						<td><input type=text name=High_Quantity value="<? echo $form_local['High_Quantity']; ?>"></td>
						<td><input type=text name=High_Price value="<? echo $form_local['High_Price']; ?>"></td>
					</tr>
					</table>
				</td>
			</tr><tr>
				<td>Printing Information:<br><textarea name=Specs rows=10 cols=100><? echo $form_local['Specs']; ?></textarea></td>
			</tr><tr>
				<td>Display Status: Active <input type=radio name=Status value='a' <? if($form_local['Status']=='a') echo "checked"; ?>> Inactive <input type=radio name=Status value='i' <? if($form_local['Status']=='i') echo "checked"; ?>> Pending <input type=radio name=Status value='p' <? if($form_local['Status']=='p' || $form_local['Status']=='') echo "checked"; ?>></td>
			</tr><tr>
				<td>Printer Email:<input type=text name=Printer_Email value="<? echo $form_local['Printer_Email']; ?>" width=30></td>
			</tr><tr>
				<td><br><table border=1 cellspacing=0 cellpadding=0>
					<tr>
						<th align=center bgcolor=#CCCCCC>Default Shipping:</th>
					</tr><tr>
						<td>Address1: <input type=text name=Address1 value="<? echo $form_local['Address1']; ?>"></td>
					</tr><tr>
						<td>Address2: <input type=text name=Address2 value="<? echo $form_local['Address2']; ?>"></td>
					</tr><tr>
						<td>City: <input type=text name=City value="<? echo $form_local['City']; ?>"></td>
					</tr><tr>
						<td>State: <input type=text name=State value="<? echo $form_local['State']; ?>"></td>
					</tr><tr>
						<td>Zip: <input type=text name=Zip value="<? echo $form_local['Zip']; ?>"></td>
					</tr>
				</table></td>
			</tr><tr>
				<td><br>JPG File:<input type=file name="img[]"></td>
			</tr><tr>
				<td>JPG Back File:<input type=file name="img[]"></td>
			</tr><tr>
				<td>PDF File:<input type=file name="img[]"></td>
			</tr><?
				if($form_local['Filename']!="")
				{
					$pic = getimagesize("images/JPG/" . $form_local['Filename'] . ".jpg");
					echo "<tr>\n\t\t\t\t<td><img src=\"images/JPG/" . $form_local['Filename'] . ".jpg\" " . $pic[3] . " border=1></td>\n\t\t\t</tr>";
				}
			?><tr>
				<td align=center>
					<table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td align=center><input type=submit value="Add Form"></td>
							<td align=center><input type=reset value="Reset Values"></td>
							<td align=center><input type=button value="Return to Main" onclick="window.location='welcome.php';"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>
	</body>
</html>
							