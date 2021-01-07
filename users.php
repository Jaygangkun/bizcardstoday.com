<?php
// include_once('firelogger/firelogger.php');
	session_start();
// flog('here');
	$full = $_SESSION['full'];
	
// flog('session', $_SESSION);
	if(isset($_SESSION['admin'])) $admin = $_SESSION['admin'];
	if(isset($_SESSION['template'])) $template = $_SESSION['template'];
	if($admin!="y")
		header("Location: index2.php");

// flog('post', $_POST);		
	require("util.php");
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	$statement = "SELECT Company, Allow_PDF FROM Templates where ID=" . $template;

	$sql->QueryRow($statement);
	$company=$sql->data[0];
	$allow_pdf=$sql->data[1];
	
	if($allow_pdf!='y')
		$HTTP_POST_VARS['Allow_Upload']='n';
	
	if($_POST['button']=='Create')
	{
		$statement = "SELECT * FROM Users WHERE Login='" . $HTTP_POST_VARS['login'] . 
			"' AND Password='" . $HTTP_POST_VARS['password'] . "' AND Template='" . 
			$HTTP_POST_VARS['template'] . "';";
		$sql->Query($statement);
		if($sql->rows<1)
		{
			$statement = "INSERT INTO Users SET Login='" . $_POST['login'] . 
				"', Password='" . $_POST['password'] . "', Template='" . 
				$_POST['template'] . "', Name='" . $_POST['name'] . "', Email='" . 
				$_POST['email'] . "', Company=\"" . 
				$_POST['company'] . "\", Allow_Upload='" . $_POST['Allow_Upload'] . 
				"', User_Admin='" . $_POST['User_Admin'] . "'";
			if(count($_POST['Group_Templates'])!=0)
			{
				foreach($_POST['Group_Templates'] as $a=>$b)
				{
					if($b!=0)
						$accrue .= $b . ",";
				}
				if($accrue!="")
					$statement .= ", Master_Admin='$accrue$template';";
			}
			$sql->Insert($statement);
			
			$statement = "INSERT INTO Users SET Login='" . $_POST['login'] . 
				"', Password='" . $_POST['password'] . "', Name='" . 
				$_POST['name'] . "', Email='" . $_POST['email'] . 
				"', Company=\"" . $_POST['company'] . "\", User_Admin='" . 
				$_POST['User_Admin'] . "'";
			$sql2 = new MySQL_class;
			$sql2->Create("bizcardstoday"); //bizforms
			$sql2->Insert($statement);
			unset($sql2);
			$sql->Create("bizcardstodaynew");
		}else
		{
			$sql->Fetch(0);
			$statement = "UPDATE Users Set Login='" . $_POST['login'] . 
				"', Password='" . $_POST['password'] . "', Template='" . 
				$_POST['template'] . "', Name='" . $_POST['name'] . 
				"', Email='" . $_POST['email'] . "', Company='" . 
				$_POST['company'] . "'";
			if($_POST['User_Admin']=='y')
				$statement .= ", User_Admin='y'";
			else
				$statement .= ", User_Admin='n'";
			if($_POST['Allow_Upload']=='y')
				$statement .= ", Allow_Upload='y'";
			else
				$statement .= ", Allow_Upload='n'";
			if(isset($_POST['Group_Templates']))
			{
				foreach($_POST['Group_Templates'] as $a=>$b)
				{
					if($b!=0 && $b!=$template)
						$accrue .= $b . ",";
				}
			}
			if($accrue!="")
				$statement .= ", Master_Admin='$accrue$template'";
			$statement .= " WHERE ID=" . $sql->data['ID'] . ";";
			$sql->Update($statement);
			
			$statement = "UPDATE Users SET Login='" . $_POST['login'] . "', Password='" . 
				$_POST['password'] . "', Name='" . $_POST['name'] . "', Email='" . 
				$_POST['email'] . "', Company=\"" . 
				$_POST['company'] . "\", User_Admin='" . $_POST['User_Admin'] . "'";
			$statement .= " WHERE ID=" . $sql->data['ID'] . ";";
			$sql2 = new MySQL_class;
			$sql2->Create("bizcardstoday"); //bizforms
			$sql2->Update($statement);
			unset($sql2);
			$sql->Create("bizcardstodaynew");
		}	
	}else if($_POST['button']=='Edit')
	{
		$statement = "UPDATE Users Set Login='" . $_POST['login'] . "', Password='" . 
			$_POST['password'] . "', Template='" . $_POST['template'] . 
			"', Name='" . $_POST['name'] . "', Email='" . 
			$_POST['email'] . "', Company='" . $_POST['company'] . "'";
		if($_POST['User_Admin']=='y')
			$statement .= ", User_Admin='y'";
		else
			$statement .= ", User_Admin='n'";
		if($_POST['Allow_Upload']=='y')
			$statement .= ", Allow_Upload='y'";
		else
			$statement .= ", Allow_Upload='n'";
		if(isset($_POST['Group_Templates']))
		{
			foreach($_POST['Group_Templates'] as $a=>$b)
			{
				if($b!=0 && $b!=$template)
					$accrue .= $b . ",";
			}
		}
		if($accrue!="")
			$statement .= ", Master_Admin='$accrue$template'";
		$statement .= " WHERE ID=" . $_POST['id'] . ";";
		$sql->Update($statement);
		
		$statement = "UPDATE Users SET Login='" . $_POST['login'] . "', Password='" . 
			$_POST['password'] . "', Name='" . $_POST['name'] . "', Email='" . 
			$_POST['email'] . "', Company=\"" . 
			$_POST['company'] . "\", User_Admin='" . $_POST['User_Admin'] . "'";
		$statement .= " WHERE ID=" . $_POST['id'] . ";";
		$sql2 = new MySQL_class;
		$sql2->Create("bizcardstoday"); //bizforms
		$sql2->Update($statement);
		unset($sql2);
		$sql->Create("bizcardstodaynew");
	}else if($_POST['button']=='Delete')
	{
		$statement = "DELETE FROM Users WHERE ID=" . $_POST['id'];
		$sql->Delete($statement);
		$sql->Create("bizcardstodaynew"); //bizforms
		$sql->Delete($statement);
		$sql->Create("bizcardstodaynew");
	}
	
	$statement = "SELECT * FROM Users WHERE Template='" . $template . "'";
	$sql->Query($statement);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>Create/Edit/Delete User</title>
	</head>

	<body bgcolor="#ffffff">
		<p><a href="welcome.php">Return to Menu</a></p>
		<p>Create a New User:</p>
		<form action=users.php method=post>
		<table border=1 cellspacing=0 cellpadding=0 width=800> <!--new user-->
			<tr>
				<th align=center>Login</th>
				<th align=center>Password</th>
				<th align=center>Name</th>
			</tr><tr>
				<input type=hidden name=company value='<?php echo $company ?>'>
				<input type=hidden name=template value='<?php echo $template ?>'>
				<td align=center><input type=text name=login size=25></td>
				<td align=center><input type=text name=password size=25></td>
				<td align=center><input type=text name=name size=25></td>
			</tr><tr>
				<th align=center>E-mail</th>
				<th align=center>Admin</th>
				<?php
					if($full=='y')
						echo "<th align=center>Master Admin Access</th>\n";
					if($allow_pdf=='y')
						echo "<th align=center>Uploader</th>\n";
				?>
				<th align=center>Action</th>
			</tr><tr>
				<td align=center><input type=text name=email size=25></td>
				<td align=center><input type=checkbox name=User_Admin value='y'></td>
				<?php
					if($full=='y')
					{
						echo "<td align=center><select name=Group_Templates[] multiple size=5 width=50>\n";
						echo "\t<option value=\"0\">NA</option>\n";
						$sql->Query("SELECT ID, Template_Name FROM Templates ORDER BY Template_Name");
						$j=0;
						while($j<$sql->rows)
						{
							$sql->Fetch($j);
							echo "\t<option value=\"" . $sql->data['ID'] . "\">" . 
								$sql->data['Template_Name'] . "</option>\n";
							$j++;
						}
						echo "</select></td>\n";
					}
					if($allow_pdf=='y')
						echo "<td align=center><input type=checkbox name=Allow_Upload value='y'></td>\n";
				?>
				<!--<td align=center><input type=checkbox name=Approver value='y'></td>-->
				<td align=center rowspan=3>
					<input type=submit value="Create" name=button><br>
					<input type=reset value="Clear Info" name=button></td>
			</tr>
		</table></form><br>
		<p>Edit/Delete User: (<b>NOTE:</b> Deletion is NOT reversible.)</p>
		<table border=1 cellspacing=0 cellpadding=0 width=800> <!--Edit/Delete-->
			<?php
				$sql->Query("SELECT ID, Template_Name FROM Templates ORDER BY Template_Name");
				$j=0;
				while($j<$sql->rows)
				{	
					$sql->Fetch($j);
					$template_compare[]=$sql->data;
					$j++;
				}		
						
				$statement = "SELECT * FROM Users WHERE Template=" . $template;
				$sql->Query($statement);
				$j=0;
				while($j<$sql->rows)
				{
					$sql->Fetch($j);
					$row=$sql->data;
					if($j%2==0)
						$color = "#ffffff";
					else
						$color="#CCCCCC";
						
					echo "<form action=users.php method=post>\n";
					echo "<tr>\n";
					echo "<th align=center bgcolor=" . $color . ">Login</th>\n";
					echo "<th align=center bgcolor=" . $color . ">Password</th>\n";
					echo "<th align=center bgcolor=" . $color . ">Name</th>\n";
					echo "</tr><tr>\n";
					echo "<input type=hidden name=id value='" . $row['ID'] . "'>\n";
					echo "<input type=hidden name=company value='" . $company . "'>\n";
					echo "<input type=hidden name=template value='" . $template . "'>\n";
					echo "<td align=center bgcolor=" . $color . "><input type=text name=login value='" . 
						$row['Login'] . "' size=25></td>\n";
					echo "<td align=center bgcolor=" . $color . 
						"><input type=text name=password value='" . $row['Password'] . "' size=25></td>\n";
					echo "<td align=center bgcolor=" . $color . "><input type=text name=name value='" . 
						$row['name'] . "' size=25></td>\n";
					echo "</tr><tr>\n";
					echo "<th align=center bgcolor=" . $color . ">E-mail</th>\n";
					echo "<th align=center bgcolor=" . $color . ">Admin</th>\n";
					if($full=='y')
						echo "<th align=center bgcolor=" . $color .">Master Admin Access</th>\n";
					if($allow_pdf=='y')
						echo "<th align=center bgcolor=" . $color . ">Uploader</th>\n";
					echo "<th align=center bgcolor=" . $color . ">Action</th>\n";
					echo "</tr><tr>\n";
					echo "<td align=center bgcolor=" . $color . "><input type=text name=email value='" . 
						$row['Email'] . "' size=25></td>\n";
					echo "<td align=center bgcolor=" . $color . 
						"><input type=checkbox name=User_Admin value='y' ";
					if($row['User_Admin']=='y')
						echo "checked";
					echo "></td>\n";
					if($full=='y')
					{
						echo "<td align=center bgcolor=" . $color . 
							"><select size=4 multiple name=Group_Templates[]>\n";
						echo "\t<option value=\"0\">NA</option>\n";
						
						$temp = explode(",", $row['Master_Admin']);
						//if(count($temp)>1)
						//{
							foreach($template_compare as $x=>$y)
							{
								echo "\t<option value=\"" . $y['ID'] . "\"";
								foreach($temp as $a)
								{
									if($y['ID']==$a)
										echo " selected";
								}
								echo ">" . $y['Template_Name'] . "</option>\n";
							}
						//}
						echo "</select></td>\n";
					}
					if($allow_pdf=='y')
					{
						echo "<td align=center bgcolor=" . $color . 
							"><input type=checkbox name='Allow_Upload' value='y' ";
						if($row['Allow_Upload']=='y')
							echo "checked";
						echo "></td>\n";
					}
					echo "<td align=center bgcolor=" . $color . 
						"><input type=submit value='Edit' name=button><br><input type=submit " . 
						"value='Delete' name=button></td>\n";
					echo "</tr>\n";
					echo "</form>\n";
					$j++;
				}
			?>
		</table>
	</body>

</html>