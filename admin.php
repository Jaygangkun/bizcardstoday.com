<?php
	session_start();
	session_register("full");
	session_register("partial");
	header("Location: welcome.php");
	if($admin!="y")
		header("Location: index.html");
		
	require("util.php");
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");
	$statement = "SELECT * from Users WHERE id=" . $user;
	$sql->Queryrow($statement);
	$row=$sql->data;
	$full = $row['Full_Admin'];
	$partial = $row['User_Admin'];
	
	$statement = "SELECT ID, Company, template, Vertical from Templates ORDER BY Company";
	$sql->Query($statement);
	$line_1 = "Line 1";
	$line_2 = "Line 2";
	$line_3 = "Line 3";
	$line_4 = "Line 4";
	$line_5 = "Line 5";
	$line_6 = "Line 6";
	$line_7 = "Line 7";
	$line_8 = "Line 8";
	$line_9 = "Line 9";
	$line_10 = "Line 10";
	$line_11 = "Line 11";
	$line_12 = "Line 12";
	if($sql->data['Vertical']=='y')
	{
		$dimension="width=\"294\" height=\"498\"";
		$width=294;
		$height=498;
	}else{
		$dimension="height=\"294\" width=\"498\"";
		$width=498;
		$height=294;
	}
	if($HTTP_POST_VARS['template']=="")
		$HTTP_POST_VARS['template']=$template;
	else
		$template = $HTTP_POST_VARS['template'];
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>Welcome to <? echo $row['company']; ?> Administration</title>
	</head>

	<body bgcolor="#ffffff">
		<center>
		<? if($full=='y')
			echo "<p><a href=\"template.php\">Edit Working Template/Current Company Info</a></p>";
		?>
		<p><a href="users.php">Create/Edit/Delete Users</a></p>
		<p><a href="pendingview.php">View/Edit Pending Orders</a></p>
		<p><a href="deleter.php">Delete Old Cards</a></p>
		<?
			if($partial=='y')
			{
				echo "<p><a href='cardview.php'>View Previous Orders</a></p>\n";
			}
		?>		
		<? if($full=='y')
			{
				echo '<p>Change Working Template:<form action=admin.php method=post><select name=template size=1 onchange="submit();">\n';
				
				$j=0;
				while($j<$sql->rows)
				{
					$sql->Fetch($j);
					echo "<option value='" . $sql->data['ID'] . "'";
					if($HTTP_POST_VARS['template']==$sql->data['ID'])
					{
						echo " selected";
						$templatefile = $sql->data['template'];
					}
					echo ">" . $sql->data['Company'] . "</option>\n";
					$j++;
				}
				echo "</select></form></p>\n";
			}
		?>
		<p><embed src="images/template/<? echo $templatefile ?>.php"  <? echo $dimension; ?> type="image/svg+xml"> </p></center>
	</body>

</html>