<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>Card Back Viewer</title>
	</head>

	<body bgcolor="#ffffff" topmargin=0 leftmargin=0>
		<table border=0 cellspacing=0 cellpadding=0 width='<? echo $HTTP_GET_VARS['width']; ?>' height='<? echo $HTTP_GET_VARS['height']; ?>'> 
			<tr>
				<td><embed src="images/finished/<? echo $template ?>.svg" width='<? echo $HTTP_GET_VARS['width']; ?>' height='<? echo $HTTP_GET_VARS['height']; ?>' type="image/svg+xml" pluginspage="http://www.adobe.com/svg/viewer/install/auto/"></td>
			</tr>
		</table>
	</body>

</html>