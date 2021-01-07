<?php
include_once('inc/preConf.php');
include_once('firelogger/firelogger.php');
session_start();
	
if($_SESSION['admin'] !="y")
	header("Location: index.html");


flog('post', $_POST);
flog('session', $_SESSION);

if(isset($_SESSION['template'])) $template =  $_SESSION['template'];
require("util.php");
$sql = new MySQL_class;
$sql->Create("bizcardstodaynew");


echo "<!--" . $_POST['lines'] . "-->\n";
if($_POST['lines']!="") //If there are lines of the card
{
	$statement="SELECT Rep FROM Templates WHERE ID=$template";
	$sql->QueryItem($statement);
	$rep_code=$sql->data[0];
	if($_POST['Rep']!=$rep_code)
	{
		$statement="SELECT Master_Admin FROM Users WHERE Rep_Code=\"" . $rep_code . "\"";
		$sql->QueryItem($statement);
		$template_array = explode(",", $sql->data[0]);
		$i=1;
		foreach($template_array as $a)
		{
			if($a!=$template)
			{
				if($i<(count($template_array)-1))
					$codestring.=$a . ",";
				else
					$codestring.=$a;
			}
			$i++;
		}
		$codestring = str_replace("n,", "", $codestring);
		//$codestring=ereg_replace($template . ",?", "", $sql->data[0]);
		$statement = "UPDATE Users SET Master_Admin=\"$codestring\" WHERE Rep_Code=\"" . $rep_code . 
			"\"";
		echo "<!--$statement-->\n";
		$sql->Update($statement);
		$statement = "SELECT Master_Admin FROM Users WHERE Rep_Code=\"" . $_POST['Rep'] . "\"";
		$sql->QueryItem($statement);
		$codestring = $sql->data[0] . ",$template";
		$statement = "UPDATE Users SET Master_Admin=\"$codestring\" WHERE Rep_code=\"" . 
			$_POST['Rep'] . "\"";
		$sql->Update($statement);
	}

	$statement="UPDATE Templates SET ";
	foreach($_POST as $a=>$b)
	{
		echo "<!--$a=>$b-->\n";
		if($a!="lines" && $a!="symbol" && $a!="symbol2")
			$statement .= $a . "='" . $b . "', ";
	}
	for($i=1; $i <= $_POST['lines']; $i++)
	{
		echo "<!-- In Lock Check $i-->\n";
		if($_POST['Line_' . $i . '_Lock']=='y')
			$statement .= "Line_" . $i . "_Lock='y', ";
		else
			$statement .= "Line_" . $i . "_Lock='n', ";
	}
	if($_POST['Pic_Upload']=='y')
		$statement .= "Pic_Upload='y', ";
	else
		$statement .= "Pic_Upload='n', ";
	if($_POST['2_Sided']=='y')
		$statement .= "2_Sided='y', ";
	else
		$statement .= "2_Sided='n', ";
	if($_POST['Vertical']=='y')
		$statement .= "Vertical='y', ";
	else
		$statement .= "Vertical='n', ";
	if($_POST['Allow_PDF']=='y')
		$statement .= "Allow_PDF='y', ";
	else
		$statement .= "Allow_PDF='n', ";
	$statement .= "Agent='" . $_POST['Agent'] . "', Inactive='" . $_POST['Inactive'] . "', ";
	$client_status = $_POST['Inactive'];
	if($_POST['Approval_Req']=='y')
		$statement .= "Approval_Req='y', ";
	else
		$statement .= "Approval_Req='n', ";
	$statement .= " Templates.lines='" . $_POST['lines'] . "' WHERE ID=" . $template;
	echo "<!--$statement-->\n";
	$sql->Update($statement);
	if($statement!="")
		$kick="welcome.php";

	if(isset($_POST['symbol']))
	{
		//$statement = "DELETE FROM Card_Symbols WHERE Template_ID=$template";
		//$sql->Delete($statement);

		foreach($_POST['symbol'] as $a=>$b)
		{
			$statement = "SELECT * FROM Card_Symbols WHERE Functional_Name=\"" . $b . 
				"\" AND Template_ID=$template";
			$sql->Query($statement);
			if($sql->rows<1)
			{
				$statement = "INSERT INTO Card_Symbols SET Functional_Name=\"" . $b . 
					"\", Template_ID=$template";
				$sql->Insert($statement);
			}
		}
	}
	if(isset($_POST['symbol2']))
	{
		foreach($_POST['symbol2'] as $a=>$b)
		{
			$path="images/symbols/$b";
			if ($handle=opendir("$path"))
			{
				while(false !== ($file = readdir($handle)))
				{
					if ($file != "." && $file != "..")
					{
						$statement = "SELECT * FROM Card_Symbols WHERE Functional_Name=\"/$b/" . $file . 
							"\" AND Template_ID=$template";
						$sql->Query($statement);
						if($sql->rows<1)
						{
							$statement = "INSERT INTO Card_Symbols SET Functional_Name=\"/$b/" . $file . 
								"\", Template_ID=$template";
							$sql->Insert($statement);
						}
					}
				}
			}
		}
	}
	$kick = "welcome.php";
}

$statement = "SELECT * FROM Card_Symbols WHERE Template_ID=$template";
$sql->Query($statement);
$j=0;
while($j<$sql->rows)
{
	$sql->Fetch($j);
	if(substr($sql->data['Functional_Name'],0,1)=="/")
	{
		$temp = explode("/", $sql->data['Functional_Name']);
		$usable_groups[]=$temp[1];
	}else
		$usable_symbols[]=$sql->data['Functional_Name'];
	$j++;
}

$statement="SELECT * FROM Templates where ID=" . $template;
$sql->QueryRow($statement);
echo "<!--$statement-->\n";
$row=$sql->data;

$statement="SELECT * FROM Finished_Cards WHERE Template=" . $template . " ORDER BY Filename DESC";
$sql->QueryRow($statement);
if($sql->data['Filename']=="")
	$sql->data['Filename']="images/template/" . $row['Template'] .".svg";
flog('file', $sql->data);

echo "<!--" . $sql->data["Filename"] . "-->\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<title>Welcome to <?php echo $row['company']; ?> Administration - Template Editor</title>
	<?php  if($kick!="")
		 echo "<script>\nwindow.location=\"$kick\";\n</script>";
	?>
</head>

<body bgcolor="#ffffff" leftmargin=5px topmargin=5px>
<p><embed src="<?php echo $sql->data["Filename"]; ?>"  height=400 width=400 type="image/svg+xml">
<?php if ($row['2_Sided']=="y")
		echo "<embed src='images/finished/" . $row['ID'] . 
			".svg' height=400 width=400 type='image/svg+xml'>\n";
?>
</p>
<form method=post action=template.php ENCTYPE="multipart/form-data">
<input type=hidden value="<?php echo $row['lines']; ?>" name=lines>
<table border=0 cellspacing=2 cellpadding=0>
<tr>
	<th align=center>Line</th>
	<th align=center>Default Value</th>
	<th align=center>Locked</th>
</tr>
<?php
	$j=1;
	while($j<=$row["lines"])
	{
		echo "<tr>\n\t\t\t\t<td>Line $j: (" . $sql->data["Line_$j"] . 
			")</td>\n\t\t\t\t<td align=center><input type=text value='" . $row["Line_" .$j] . 
			"' name=Line_$j></td>\n\t\t\t\t<td align=center><input type=checkbox value='y' name=Line_${j}_Lock ";
		if($row["Line_" . $j . "_Lock"]=='y')
			echo "checked";
		echo "></td>\n\t\t\t</tr>";
		$j++;
	}
?>
<tr>
	<td colspan=3><br>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<th>Price Per</th>
				<th>Standard</th>
				<th>Premium</th>
				<th>Default Amount</th>
			</tr>
			<tr>
				<td>250</td>
				<td align=center>
					<input type=text size=10 value='<?php echo $row['per250']; ?>' name=per250></td>
				<td align=center>
					<input type=text size=10 value='<?php echo $row['per250_premium']; ?>' 
						name=per250_premium></td>
				<td align=center>
					<input type=radio name=default_value value=250 
						<?php if($row['default_value']=="250") echo "checked"; ?>>
				</td>
			</tr>
			<tr>
				<td>500</td>
				<td align=center>
					<input type=text size=10 value='<?php echo $row['per500']; ?>' name=per500></td>
				<td align=center>
					<input type=text size=10 value='<?php echo $row['per500_premium']; ?>' 
						name=per500_premium></td>
				<td align=center>
					<input type=radio name=default_value value=500 
					<?php if($row['default_value']=="500") echo "checked"; ?>></td>
			</tr>
			<tr>
				<td>1000</td>
				<td align=center>
					<input type=text size=10 value='<?php echo $row['per1000']; ?>' name=per1000></td>
				<td align=center>
					<input type=text size=10 value='<?php echo $row['per1000_premium']; ?>' name=per1000_premium></td>
				<td align=center>
				<input type=radio name=default_value value=1000 
					<?php if($row['default_value']=="1000" || $row['default_value']=="") echo "checked"; ?>>
				</td>
			</tr>
			<tr>
				<td>2000</td>
				<td align=center>
					<input type=text size=10 value='<?php echo $row['per2000']; ?>' name=per2000></td>
				<td align=center>
					<input type=text size=10 value='<?php echo $row['per2000_premium']; ?>' name=per2000_premium></td>
				<td align=center>
					<input type=radio name=default_value value=2000 <?php if($row['default_value']=="2000") echo "checked"; ?>></td>
			</tr>
		<TR>
			<td colspan=5 bgcolor=#B0C4DE>Standard Shipping Cost:$
			<input type=text name=sship size=4 maxlength=4 value='<?php echo $row['sship']; ?>'>
				&nbsp;&nbsp;&nbsp;Premium Shipping Charge:$
			<input typ=text name=pship size=4 maxlength=4 value='<?php echo $row['pship']; ?>'></td>
		</tr>
		<TR>
			<td colspan=5 bgcolor=#B0C4DE>Type of Shipping: <select name=typeship>
			<option selected><?php echo $row['typeship']; ?>
			<option>Pickup
			<option>Standard
			<option>UPS
			</select>
			</td>
		</tr>
			<tr>
				<td colspan=5>Rush Charges: 
				<input type=text size=10 value='<?php echo $row['QuickCard_Price']; ?>' name=QuickCard_Price></td>
			</tr>
			<tr>
				<td colspan=5>Default Card Quality: 
				<input type=radio name=card_quality value='e' 
				<?php if($row['card_quality']=='e' || $row['card_quality']=="") echo "checked"; ?>>Selection 
				<input type=radio name=card_quality value='s' <?php if($row['card_quality']=='s') echo "checked"; ?>>
					Standard Only  
				<input type=radio name=card_quality value='p' <?php if($row['card_quality']=='p') echo "checked"; ?>>
					Premium Only</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan=3><br>
	<table border=0 cellspacing=0 cellpadding=0>
	<tr>
		<th align=center colspan=2>Card Setup</th>
	</tr><tr>
		<td>Template ID</td>
		<td><?php echo $template; ?></td>
	</tr><tr>
		<td>Template Name</td>
		<td><input type=text name=Template_Name size=20 value="<?php echo $row['Template_Name'] ?>"></td>
	</tr><tr>
		<td>Photo Card</td>
		<td><input type=checkbox name=Pic_Upload value='y' <?php if($row['Pic_Upload']=='y') echo "checked"; ?>>
		</td>
	</tr><tr>
		<td>Photo Width (in pixels)</td>
		<td><input type=text name=Pic_Width value='<?php echo $row['Pic_Width']; ?>'></td>
	</tr><tr>
		<td>Photo Height (in pixels)</td>
		<td><input type=text name=Pic_Height value='<?php echo $row['Pic_Height']; ?>'></td>
	</tr><tr>
		<td>2 sided Card</td>
		<td><input type=checkbox name=2_Sided value='y' <?php if($row['2_Sided']=='y') echo "checked"; ?>>
		</td>
	</tr><tr>
		<td>Vertical Card</td>
		<td><input type=checkbox name=Vertical value='y' <?php if($row['Vertical']=='y') echo "checked"; ?>>
		</td>
	</tr><tr>
		<td>Allow PDF Upload</td>
		<td><input type=checkbox name=Allow_PDF value='y' <?php if($row['Allow_PDF']=='y') echo "checked"; ?>>
		</td>
	</tr><tr>
		<td>Paper</td>
		<td><input type=text size=15 name=Paper value='<?php echo $row['Paper']; ?>'></td>
	</tr><tr>
		<td>Premium Paper</td>
		<td><input type=text size=15 name=Paper_premium value='<?php echo $row['Paper_premium']; ?>'>
		</td>
	</tr><tr>
		<td>Ink</td>
		<td><input type=text size=15 name=Ink value='<?php echo $row['Ink']; ?>'></td>
	</tr><tr>
		<td>Printer Email</td>
		<td><input type=text size=15 name=Printer_Email value='<?php echo $row['Printer_Email']; ?>'>
		</td>
	</tr><tr>
		<td>Card Status</td>
		<td>
		<table border=0 cellspacing=2 cellpadding=0>
		<tr>
			<td><input type=radio name=Inactive value='i' <?php if($row['Inactive']=='i') echo "checked"; ?>>
				Inactive</td>
			<td><input type=radio name=Inactive value='a' <?php if($row['Inactive']=='a') echo "checked"; ?>>
				Active</td>
			<td><input type=radio name=Inactive value='p' <?php if($row['Inactive']=='p') echo "checked"; ?>>
				Prospective</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td colspan=3>
		<table border=1 cellspacing=3 cellpadding=3>
			<tr>
				<th colspan=5>Optional Symbols allowed on this card:</th>
<?php 

$path="images/symbols";
if ($handle=opendir("$path"))
{
//$i=5;
while(false !== ($file = readdir($handle)))
{
if ($file != "." && $file != "..")
{
	echo "<!--$file-->\n";
	if(is_file($path . "/" . $file))
	{
		$line = "\t\t\t\t\t\t\t\t<td><input type=checkbox name=symbol[]";
		if(count($usable_symbols)>0)
		{
			foreach($usable_symbols as $a=>$b)
			{
				if(str_replace(" ", "_", $file)==$b)
					$line .= " checked";
			}
		}
		$line .= " value=\"" . str_replace(" ", "_", $file) . 
			"\"><img src=\"$path/$file\" border=0></td>\n";
		$symbol_output[]=$line;
	}else
		$directory[]=str_replace(" ", "_", $file);
}

}
$i=5;
$j=0;
while ($j<count($directory))
{
if($i==5)
{
	$i=0;
	echo "\t\t\t\t\t\t</tr><tr>\n";
};
echo "\t\t\t\t\t\t\t<td><input type=checkbox name=symbol2[]";
if(count($usable_groups)>0)
{
	foreach($usable_groups as $a=>$b)
	{
		//echo "<!--" . $directory[$j] . " || $b-->\n";
		if(str_replace(" ", "_", $directory[$j])==$b)
			echo " checked";
	}
}
echo " value=\"" . str_replace(" ", "_", $directory[$j]) . "\">&nbsp;" . $directory[$j] . "</td>\n";
$j++;
$i++;
}
$i=5;
$j=0;
while($j<count($symbol_output))
{
if($i==5)
{
	$i=0;
	echo "\t\t\t\t\t\t</tr><tr>\n";
};
echo $symbol_output[$j];
$j++;
$i++;
}
}
?>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan=3>
		<table border=1 cellspacing=3 cellpadding=3>
			<tr>
				<th>Purchase Approval Email</th>
				<th>Purchase Approval Required</th>
			</tr>
			<tr>
				<td align=center>
					<input type=text name=Approval_Email value='<?php echo $row['Approval_Email'] ?>'></td>
				<td align=center>
				<input type=checkbox name=Approval_Req value='y' <?php if($row['Approval_Req']=='y') echo "checked"; ?>>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan=3>Rep: <select name=Rep size=1>
		<?php
			$statement=
				"SELECT Rep_Code, Name FROM Users WHERE Rep_Code<>\"\" AND Special_Type=\"Rep\" Order by Name";
			$sql->Query($statement);
			$j=0;
			if($row['Rep']=="")
				$row['Rep']="H";
			while($j<$sql->rows)
			{
				$sql->Fetch($j);
				echo "\t\t\t\t\t<option value='" . $sql->data["Rep_Code"] . "' ";
				if($row['Rep']==$sql->data['Rep_Code'])
					echo "selected";
				echo ">" . $sql->data['Name'] . " {" . $sql->data['Rep_Code'] . "}</option>\n";
				$j++;
			}
			echo "</select>\n";
		?>
	&nbsp;&nbsp;&nbsp;&nbsp;Agent: <select name=Agent size=1>
		<option value='None'>None</option>
		<?php
			$statement=
			"SELECT c.Name as Comp_Name, u.Name, u.Rep_Code FROM Users u, Company c WHERE c.ID=u.Company AND Rep_Code<>\"\" AND Special_Type=\"Agent\" Order by Comp_Name, Name";
			$sql->Query($statement);
			$j=0;
			while($j<$sql->rows)
			{
				$sql->Fetch($j);
				echo "\t\t\t\t\t<option value='" . $sql->data["Rep_Code"] . "' ";
				if(substr($row['Agent'],0,4)==substr($sql->data['Rep_Code'],0,4))
					echo "selected";
				echo ">" . $sql->data['Comp_Name'] . " - " . $sql->data['Name'] . " {" . $sql->data['Rep_Code'] . "}</option>\n";
				$j++;
			}
			echo "</select>\n";
		?>
	</td>
<tr>
	<td colspan=3>
		<table border=0 cellspacing=3 cellpadding=3>
		<tr>
			<td><input type=submit value="Save Changes"></td>
			<td><a href="welcome.php">Return to Menu</a></td>
		</tr>
		</table>
	</td>
</tr>
</table>
</form>
</body>
</html>