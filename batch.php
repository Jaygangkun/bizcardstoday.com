<?php
	session_start(); //Start the session
	// if(!session_is_registered("template")) //Boot to homepage if card template is not set.
	if(!isset($_SESSION["template"])) //Boot to homepage if card template is not set.
	{
		header("Location: index2.php");
	}else 
	{
		$template = $_SESSION['template'];
		$user = $_SESSION['user'];
	}
		
	require("util.php"); // db wrapper
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");

	$statement = "SELECT default_value, Vertical, per250, per250_premium, card_quality FROM Templates WHERE ID=" . $template;
	$sql->QueryItem($statement);
	$default_value=$sql->data[0];
	$per250 = $sql->data[2];
	$per250_premium = $sql->data['per250_premium'];
	$card_quality = $sql->data['card_quality'];
	if($sql->data[1]=='y')
	{
		$width=294;
		$height=498;
	}else{
		$height=294;
		$width=498;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>BizCardsToday.com Batch Ordering</title>
		<script language=javascript>
		<!--
		function OnButton1()
		{	//Redraw Card
			document.Form1.action = "batch.php#"+document.Form1.elements['jump'].value;
			document.Form1.submit();			
			return true;
		}
		function OnButton2()
		{	//Redraw Card
			document.Form2.action = "batchorder.php";
			document.Form2.submit();			
			return true;
		}
		function OnButton3()
		{	//Redraw Card
			document.Form1.action="welcome.php";
			document.Form1.submit();			
			return true;
		}
		-->
		</script>
	</head>

	<body bgcolor="#ffffff">
		<table border=0 cellspacing=2 cellpadding=4>
			<tr>
				<td colspan=3><form action="batch.php" method=post name=Form1>Jump to: <select size=1 name=jump onchange="OnButton1();">
					<option value="top">Select</option>
<?
						$statement="SELECT * FROM Finished_Cards WHERE Template=" . $template . " ORDER BY line_1";
						$sql->Query($statement);
						$j=0;
						while($j<$sql->rows)
						{
							$sql->Fetch($j);
							echo "\t\t\t\t<option value=\"" . $sql->data['ID'] . "\">" . $sql->data['Line_1'] . "</option>\n";
							$j++;
						}
?>				</select></form></td>
			</tr>
		</table><br>
		<form action="batchorder.php" method=post name=Form2>
		<table border=1 cellspacing=2 cellpadding=4>
			<tr>
				<th align=center>Quantity</th>
				<?
					if($card_quality=="e")
					{
						echo "\t\t\t\t<th align=center>Quality</th>\n";
					}
				?>
				<th align=center>Card</th>
				<th align=center>Click to View</th>
			</tr>
				<?
					$j=0;
					while($j<$sql->rows)
					{
						$sql->Fetch($j);
						echo "\t\t\t\t\t<tr>\n";
						echo "\t\t\t\t\t\t<td align=center><table border=0 cellspacing=0 cellpadding=0><tr>";
						if($per250!=0)
							echo "<td><font size=-1><input type=radio name=" . $sql->data['ID'] . " value='0' checked> 0</font></td><td><font size=-1><input type=radio name=" . $sql->data['ID'] . " value='250'> 250</font></td>";
						else
							echo "<td><font size=-1><input type=radio name=" . $sql->data['ID'] . " value='0' checked> 0</font></td>";
						echo "<td><font size=-1><input type=radio name=" . $sql->data['ID'] . " value='500'> 500</font></td><td><font size=-1><input type=radio name=" . $sql->data['ID'] . " value='1000'> 1000</font></td>";
						echo "<td><font size=-1><input type=radio name=" . $sql->data['ID'] . " value='2000'> 2000</font></td></tr></table></td>\n";
						if($card_quality=="e")
						{
							echo "\t\t\t\t\t\t<td align=center><input type=radio name=" . $sql->data['ID'] . "_quality value='s'";
							if($sql->data['Quality']=='s' || $sql->data['Quality']=='e')
								echo " checked";
							echo "> Regular <input type=radio name=" . $sql->data['ID'] . "_quality valued='p'";
							if($sql->data['Quality']=='p')
								echo " checked";
							echo "> Premium</td>\n";
						}else
							echo "\t\t\t\t\t\t<input type=hidden name=" . $sql->data['ID'] . "_quality value='" . $card_quality . "'>\n";
						echo "\t\t\t\t\t\t<td align=center><a name='" . $sql->data['ID'] . "'>" . $sql->data['Card_Name'] . "</a></td>\n";
						echo "\t\t\t\t\t\t<td align=center><input type=button onclick=\"window.open ('cardview.php?width=$width&height=$height&target=" . $sql->data['Filename'] . "', 'view', 'width=$width,height=$height');\" value='View Card'></td>\n";
						echo "\t\t\t\t\t</tr>\n";
						$j++;
					}
				?>
			<tr>
				<td colspan=3 align=center>Batch PO# <input type=text size=20 name=po></td>
			</tr>
			<tr>
				<td colspan=3 align=center><input type=button value="Submit Batch Order" onclick="OnButton2();">&nbsp;&nbsp;&nbsp;<input type=button value="Return to Menu" onclick="OnButton3();"></td>
			</tr>
		</table>
		</form>
	</body>

</html>