<?php
	session_start();
	require("util.php");
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	if($HTTP_POST_VARS['target']!="")
	{
		@unlink("images/PDF/" . $HTTP_POST_VARS['target']);
		$HTTP_POST_VARS['target'] = "";
	}
?>
<html>
	<head>
		<title> BizCardsToday.com PDF Review</title>
	</head>
	
	<body>
		<a href=welcome.php>Return to Menu</a><br>
		<table border=0 cellspacing=4 cellpadding=4>
<?php

	if ($handle=opendir("images/PDF")) 
	{ 
		while(false !== ($file = readdir($handle))) 
		{
			$skip='n';
			if(strtolower(substr($file,-3))=="pdf")
			{	
				$statement="SELECT Company FROM Templates WHERE ID=" . $template;
				$sql->QueryRow($statement);
				$comp = $sql->data[0];
				
				$nameparts = explode("-", substr($file, 0, (strlen($file)-4)));
				$statement = "SELECT DateStamp FROM PDF_Uploads WHERE ID=" . $nameparts[1];
				$sql->QueryItem($statement);
				
				//echo "<!--" . $sql->data[0] . "-->\n";
				$funcdate = date('m/d/y h:i:s', strtotime($sql->data[0]));
				//echo "<!--$funcdate-->\n";
				echo "\t\t\t<tr>\n\t\t\t\t<td><a href='images/PDF/" . $file . "' target='_blank'>$file - $funcdate</a></td>\n\t\t\t\t<td><form action=pdfreview.php method=post><input type=hidden value='" . $file . "' name=target><input type=submit value=\"Delete\"></form></td>\n\t\t\t</tr>\n";
			}
		}
	}

?>
		</table><br>
		<a href=welcome.php>Return to Menu</a>
	</body>
</html>