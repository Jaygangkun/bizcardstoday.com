<?php
	session_start();
	if($admin!="y")
		header("Location: index2.php");
		
	require("util.php");
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	if($HTTP_POST_VARS['id']!="")
	{
		$statement="SELECT * FROM Forms WHERE ID=" . $HTTP_POST_VARS['id'];
		$sql->QueryRow($statement);
		if($sql->data['Filename']!="")
			@unlink("images/JPG/" . $sql->data['Filename'] . ".jpg");
		$statement="DELETE FROM Forms WHERE ID=" . $HTTP_POST_VARS['id'];
		$sql->Delete($statement);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>Delete Old Forms</title>
	</head>

	<body bgcolor="#ffffff">
		
		<table border=1 cellspacing=3 cellpadding=4>
			<tr>
				<th align=center>Form Title</th>
				<th align=center>Action</th>
			</tr>
<?
			$statement="SELECT * FROM Forms WHERE Template=$template;";
			$sql->Query($statement);
			
			$j=0;
			while($j<$sql->rows)
			{
				$sql->Fetch($j);
				echo "\t\t\t<form action=form_deleter.php method=post>\n";
				echo "\t\t\t<tr>\n\t\t\t\t<input type=hidden value='" . $sql->data['ID'] . "' name=id><td align=center>" . $sql->data['Form_Name'] . "</td>\n\t\t\t\t<td align=center><input type=submit value='Delete'></td>\n\t\t\t</tr>\n";
				echo "\t\t\t</form>\n";
				$j++;
			}
		?>
		</table>
		<p><a href="welcome.php">Return to Menu</a></p>
	</body>

</html>