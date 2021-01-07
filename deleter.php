<?php
	session_start();
include_once('firelogger/firelogger.php');

flog('session 3', $_SESSION);

	if($_SESSION['admin'] !="y")
		header("Location: index2.php");
		
	require("util.php");
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");
flog('http var', $HTTP_POST_VARS['id']);
	
	if(isset($_POST['id']))
	{
		$statement="SELECT * FROM Finished_Cards WHERE ID=" . $_POST['id'];
flog('sql2', $statement);
		$sql->QueryRow($statement);
		
$filename = $sql->data['Filename'];
$oldfiles = $sql->data['Old_Files'];
flog('filename', $filename);
flog('oldfiles', $oldfiles);

		if($sql->data['Filename'] != "")
		{
			if(file_exists($sql->data['Filename']))
				unlink($sql->data['Filename']);
		}
		$files = explode(";", $sql->data['Old_Files']);
		if(count($files)>1)
		{
			foreach($files as $a)
			{
				@unlink($a);
			}
		}
		$statement="DELETE FROM Finished_Cards WHERE ID=" . $_POST['id'];
flog('sql3', $statement);
		$sql->Delete($statement);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>Delete Old Cards</title>
	</head>

	<body bgcolor="#ffffff">
		
		<table border=1 cellspacing=3 cellpadding=4>
			<tr>
				<th align=center>Card Title</th>
				<th align=center>Line 1</th>
				<th align=center>Line 2</th>
				<th align=center>Line 3</th>
				<th align=center>Action</th>
			</tr>
<?php 
			if(isset($_SESSION['template']))
			{
				$template = $_SESSION['template'];
				$statement="SELECT * FROM Finished_Cards WHERE Template=$template ORDER BY Card_Name;";
			}else exit('Error - template no not found');
			$sql->Query($statement);
			
			$j=0;
			while($j<$sql->rows)
			{
				$sql->Fetch($j);
				echo "\t\t\t<form action=deleter.php method=post>\n";
				echo "\t\t\t<tr>\n\t\t\t\t<input type=hidden value='" . $sql->data['ID'] . "' name=id><td align=center>" . $sql->data['Card_Name'] . "</td>\n\t\t\t\t<td align=center>" . $sql->data['Line_1'] . "</td>\n\t\t\t\t<td align=center>" . $sql->data['Line_2'] . "</td>\n\t\t\t\t<td align=center>" . $sql->data['Line_3'] . "</td>\n\t\t\t\t<td align=center><input type=submit value='Delete'></td>\n\t\t\t</tr>\n";
				echo "\t\t\t</form>\n";
				$j++;
			}
		?>
		</table>
		<p><a href="welcome.php">Return to Menu</a></p>
	</body>

</html>