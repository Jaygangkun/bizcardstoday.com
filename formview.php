<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>Form Viewer</title>
	</head>

	<body bgcolor="#ffffff" topmargin=0 leftmargin=0>
		<table border=0 cellspacing=0 cellpadding=0> 
			<tr>
				<td><img src='images/JPG/<? echo $template; ?>-<? if($_GET['back']==1) echo $_GET['target'] . "-back"; else echo $HTTP_GET_VARS['target']; ?>.jpg'></td>
			</tr>
		</table>
	</body>

</html>