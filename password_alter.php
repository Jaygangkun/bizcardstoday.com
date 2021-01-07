<?php
	session_start();
// 	if($template==0 || !session_is_registered("template") || $template=="")
	if(!session_is_registered("template")) //Boot to homepage if card template is not set.
	{
		header("Location: index2.php");
	}else 
	{
		$template = $_SESSION['template'];
		$user = $_SESSION['user'];
	}
	require("util.php");
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	$statement="SELECT Password, Email from Users WHERE ID=$user";
	$sql->QueryRow($statement);
	
	if($_REQUEST['new_email']!="")
	{
		$email_statement="UPDATE Users SET Email='" . $_REQUEST['new_email'] . "' WHERE ID=$user";
		$sql->Update($email_statement);
		$msg="Email Updated!<br>\n";
	}

	if($_REQUEST['new_pass2']=="" && $_REQUEST['new_pass']!="") 
		$err_msg .= "You must verify your new password<br>\n";
	if($_REQUEST['new_pass']!=$_REQUEST['new_pass2'] && $_REQUEST['new_pass']!="")
		$err_msg .= "You must successfully verify your new password.<br>\n";
	if($_REQUEST['new_pass']==$sql->data[0] && $_REQUEST['new_pass']!="")
		$err_msg .= "Your new password cannot be the same as your old password.<br>\n";
		
	if($err_msg=="" && $_REQUEST['new_pass']!="")
	{
		$password_statement="UPDATE Users SET Password='" . $_REQUEST['new_pass'] . "' WHERE ID=$user";
		$sql->Update($password_statement);
		$msg .="Password Updated!<br>\n";
	}
	
	if($email_statement!="")
	{
		$sql->Create("bizforms");
		$sql->Update($email_statement);
	}
	
	if($password_statement!="")
	{
		$sql->Create("bizforms");
		$sql->Update($password_statement);
	}
		
?>		
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>BizCardsToday.com Password Administration</title>
		<script language=javascript>
		<!--
		function OnButton1()
		{
			document.Form1.action = "welcome.php"
			document.Form1.submit();			// Submit the page
			return true;
		}
		-->
		</script>
	</head>

	<body bgcolor="#ffffff">
		<p>Enter what you want to change below, and hit "Submit Change".</p>
		<?
			if($err_msg!="" || $msg!="")
			{
				echo "<p><font color=red>" . $err_msg . "</font><font color=blue>" . $msg . "</font></p>\n";
			}
		?>
		<form action=password_alter.php method=post name=Form1>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td>Old Email: </td><td><? echo $sql->data['Email']; ?></td>
			</tr><tr>
				<td>New Email: </td><td><input type=text name=new_email></td>
			</tr><tr>
				<td>New Password: </td><td><input type=password name=new_pass></td>
			</tr><tr>
				<td>New Password Again: </td><td><input type=password name=new_pass2></td>
			</tr><tr>
				<td colspan=2 align=center><input type=submit value="Submit Change">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick="return OnButton1();"></td>
			</tr>
		</table>
		</form>
	</body>

</html>