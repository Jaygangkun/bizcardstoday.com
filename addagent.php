<?php
	session_start();
// 	if($template==0 || !session_is_registered("template") || $template=="")
	// if(!session_is_registered("template")) //Boot to homepage if card template is not set.
	if(!isset($_SESSION["template"])) //Boot to homepage if card template is not set.
		header("Location: index.php");
		
	require("util.php");
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	echo "<!--" . $_POST['button'] . "-->\n";
	if($HTTP_POST_VARS['button']=='Create')
	{
		if($_POST['form_name']=="Agent Create")
		{
			$statement="SELECT * FROM Users Where Rep_Code<>\"\" AND Rep_Code<>'None'";
			$sql->Query($statement);
			$j=0;
			while($j<$sql->rows)
			{
				$sql->Fetch($j);
				if($sql->data['Rep_Code']==$HTTP_POST_VARS['Code'])
					$err_msg .="Duplicate Code.  Each Code must be unique<br>\n";
				$j++;
			}
			if($err_msg=="")
			{
				$statement="INSERT INTO Users SET Name='" . $HTTP_POST_VARS['name'] . "', Email='" . $HTTP_POST_VARS['email'] . "', Rep_Code='" . $HTTP_POST_VARS['code'] . "', Special_Type='Agent', Login='" . $_POST['Login'] . "', Password='" . $_POST['Password'] . "', Company=" . $_POST['parent'] . ", Template=" . $_POST['base'];
				$sql->Insert($statement);
			}else
			{
				$bounce_name=$HTTP_POST_VARS['name'];
				$bounce_email=$HTTP_POST_VARS['email'];
				$bounce_code=$HTTP_POST_VARS['code'];
				$bounce_login=$_POST['Login'];
				$bounce_password=$_POST['Password'];
				$bounce_parent=$_POST['parent'];
				$bounce_base=$_POST['base'];
			}
		}else{
			$statement="SELECT * FROM Company WHERE Agency='y'";
			$sql->Query($statement);
			$j=0;
			while($j<$sql->rows)
			{
				$sql->Fetch($j);
				if($sql->data['Company_Name']==$_POST['Company_Name'])
						$err_msg .="Duplicate Company Name.  Each Company name must be unique<br>\n";
				$j++;
			}
			if($err_msg=="")
			{
				$statement="INSERT INTO Company SET Name='" . $HTTP_POST_VARS['Company_Name'] . "', Address1='" . $HTTP_POST_VARS['Company_Address'] . "', Address2='" . $HTTP_POST_VARS['Company_Address2'] . "', City='" . $_POST['Company_City'] . "', Zip='" . $_POST['Company_Zip'] . "', Agency='y'";
				$sql->Insert($statement);
			}else
			{
				$bounce_CN=$HTTP_POST_VARS['Company_Name'];
				$bounce_CA=$HTTP_POST_VARS['Company_Address'];
				$bounce_CA2=$HTTP_POST_VARS['Company_Address2'];
				$bounce_CC=$_POST['Company_City'];
				$bounce_CS=$_POST['Company_State'];
				$bounce_CZ=$_POST['Company_Zip'];
			}
			echo "<!--$statement-->\n";
		}
	}
	if($HTTP_POST_VARS['button']=="Edit")
	{
		$statement="SELECT * FROM Users WHERE ID=" . $_POST['id'];
		$sql->QueryRow($statement);
		if($sql->data['name']!="")
		{
			$statement="UPDATE Users SET Name=\"" . $_POST['Name'] . "\", Email=\"". $_POST['Email'] . "\", Rep_Code=\"" . $_POST['Code'] . "\", Login=\"" . $_POST['Login'] . "\", Password=\"" . $_POST['Password'] . "\", Template=" . $_POST['base'] . ", Company=" . $_POST['parent'] . ", Special_Type='Agent' WHERE ID=" . $_POST['id'];
			$sql->Update($statement);
		}
	}
	if($HTTP_POST_VARS['button']=='Delete')
	{
		$statement="SELECT Name, Rep_Code FROM Users WHERE ID=" . $HTTP_POST_VARS['id'];
		$sql->QueryRow($statement);
		$row=$sql->data;
		if($row['Name']!="")
		{
			$statement="UPDATE Templates SET Agent='None' WHERE Agent='" . $row['Rep_Code'] . "';";
			$sql->Update($statement);
			$statement="DELETE FROM Users WHERE ID=" . $HTTP_POST_VARS['id'];
			$sql->Delete($statement);
		}
	}	
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>BizCardsToday.com Agency/Agents Setup</title>
	</head>

	<body bgcolor="#ffffff" leftmargin=5px>
		<? if($err_msg!="")
			echo "<p>$err_msg</p>\n";
		?>
		<form action=addagent.php method=post>
		<input type=hidden name=form_name value="Company Create">
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<th align=center>Company Name</th>
				<th align=center>Address 1</th>
				<th align=center>Address 2</th>
				<th align=center>City</th>
				<th align=center>State</th>
				<th align=center>Zip</th>
			</tr>
			<tr>
				<td><input type=text name=Company_Name value='<? echo $bounce_CN; ?>'></td>
				<td><input type=text name=Company_Address value='<? echo $bounce_CA1; ?>'></td>
				<td><input type=text name=Company_Address2 value='<? echo $bounce_CA2; ?>'></td>
				<td><input type=text name=Company_City value='<? echo $bounce_CC; ?>'></td>
				<td><input type=text name=Company_State value='<? echo $bounce_CS; ?>'></td>
				<td><input type=text name=Company_Zip value='<? echo $bounce_CZ; ?>'></td>
			</tr>
			<tr>
				<td colspan=6 align=center><input type=submit value="Create" name=button></td>
			</tr>
		</table>
		</form>
		<form action=addagent.php method=post>
		<input type=hidden name=form_name value="Agent Create">
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<th align=center>Name</th>
				<th align=center>Email</th>
				<th align=center>Code</th>
				<td align=center rowspan=2><input type=submit value="Create" name=button></td>
			</tr>
			<tr>
				<td><input type=text name=name value='<? echo $bounce_name; ?>'></td>
				<td><input type=text name=email value='<? echo $bounce_email; ?>'></td>
				<td><input type=text name=code value='<? echo $bounce_code; ?>' maxlength=8 size=10></td>

			</tr>
			<tr>
				<th align=center>Login</th>
				<th align=center>Password</th>
				<th align=center>Parent Company</th>
				<th align=center>Base Template</th>
			</tr>
			<tr>
				<td><input type=text name=Login value='<? echo $bounce_login; ?>'></td>
				<td><input type=text name=Password value='<? echo $bounce_password; ?>'></td>
				<td><select name=parent size=1>
					<?
						$statement="SELECT ID, Name FROM Company WHERE Agency='y' ORDER BY Name";
						$sql->Query($statement);
						$j=0;
						while($j<$sql->rows)
						{
							$sql->Fetch($j);
							echo "\t\t\t\t\t<option value=" . $sql->data['ID'];
							if($bounce_parent==$sql->data['ID'])
								echo " selected";
							echo ">" . $sql->data['Name'] . "</option>\n";
							$j++;
						}
					?></select>
				</td>
				<td><select name=base size=1>
					<?
						$statement="SELECT ID, Template_Name FROM Templates ORDER BY Template_Name";
						$sql->Query($statement);
						$j=0;
						while($j<$sql->rows)
						{
							$sql->Fetch($j);
							echo "\t\t\t\t\t<option value=" . $sql->data['ID'];
							if($bounce_base==$sql->data['ID'])
								echo " selected";
							echo ">" . $sql->data['Template_Name'] . "</option>\n";
							$j++;
						}
					?></select>
				</td>
			</tr>
		</table></form>
		<br><br>
			<?
				$statement="SELECT * FROM Users WHERE Special_Type='Agent' order by Name";
				$sql->Query($statement);
				$j=0;
				while($j<$sql->rows)
				{
					$sql->Fetch($j);
					$row=$sql->data;
					if($j%2==0)
						$color="#CCCCCC";
					else
						$color="#FFFFFF";
					echo "<form action=addagent.php method=post>\n\t\t\t<input type=hidden name=form_name value=\"Record Edit\"><input type=hidden name=id value=" . $row['ID'] . ">\n\t\t\t<table border=1 cellspacing=0 cellpadding=5>\n\t\t\t\t";
					echo "<tr>\n\t\t\t\t\t<th align=center bgcolor=$color>Name</th>\n\t\t\t\t\t<th align=center bgcolor=$color>Email</th>\n\t\t\t\t\t<th align=center bgcolor=$color>Code</th>\n\t\t\t\t\t<td rowspan=2 bgcolor=$color align=center valign=center><input type=submit value='Edit' name=button><br><input type=submit value='Delete' name=button></td>\n\t\t\t\t</tr>";
					echo "\n\t\t\t\t<tr>\n\t\t\t\t\t<td bgcolor=$color><input type=text name=Name value=\"" . $row['name'] . "\"></td>\n\t\t\t\t\t<td bgcolor=$color><input type=text name=Email value=\"" . $row['Email'] . "\"></td>\n\t\t\t\t\t<td bgcolor=$color><input type=text name=Code value=\"" . $row['Rep_Code'] . "\"></td>\n\t\t\t\t";
					echo "<tr>\n\t\t\t\t\t<th align=center bgcolor=$color>Login</th>\n\t\t\t\t\t<th align=center bgcolor=$color>Password</th>\n\t\t\t\t\t<th align=center bgcolor=$color>Parent Company</th>\n\t\t\t\t\t<th align=center bgcolor=$color>Base Template</th>\n\t\t\t\t</tr>\n\t\t\t\t";
					echo "<tr>\n\t\t\t\t\t<td bgcolor=$color><input type=text name=Login value='" . $row['Login'] . "'></td>\n\t\t\t\t\t	<td bgcolor=$color><input type=text name=Password value='" . $row['Password'] . "'></td>\n\t\t\t\t\t<td bgcolor=$color><select name=parent size=1>\n";
					$statement="SELECT ID, Name FROM Company WHERE Agency='y' ORDER BY Name";
					$sql->Query($statement);
					$k=0;
					while($k<$sql->rows)
					{
						$sql->Fetch($k);
						echo "\t\t\t\t\t<option value=" . $sql->data['ID'];
						if($row['Company']==$sql->data['ID'])
							echo " selected";
						echo ">" . $sql->data['Name'] . "</option>\n";
						$k++;
					}
					echo "\t\t\t\t\t<select>\n\t\t\t\t</td><td bgcolor=$color><select name=base size=1>\n";
					$statement="SELECT ID, Template_Name FROM Templates ORDER BY Template_Name";
					$sql->Query($statement);
					$k=0;
					while($k<$sql->rows)
					{
						$sql->Fetch($k);
						echo "\t\t\t\t\t<option value=" . $sql->data['ID'];
						if($row['Template']==$sql->data['ID'])
							echo " selected";
						echo ">" . $sql->data['Template_Name'] . "</option>\n";
						$k++;
					}
					echo "\t\t\t\t\t<select>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t</table></form>";
					$statement="SELECT * FROM Users WHERE Special_Type='Agent' order by Name";
					$sql->Query($statement);
					$j++;
				}
			?>
			</tr>
		</table><br><br>
		<a href="welcome.php">Return to Main Menu</a>		
	</body>

</html>