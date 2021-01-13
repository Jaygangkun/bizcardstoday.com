<?php
session_start();
require("util.php");

$sql=new MySQL_class;
$sql->Create("bizcardstodaynew");

if(isset($_SESSION['template'])) $template = $_SESSION['template'];

$statement="SELECT Template FROM Templates WHERE ID=" . $template;
$sql->QueryItem($statement);
$template_name=$sql->data[0];
	
if ($_POST['submitted'])
{ // Begin processing portion of script

	//all image types to upload
	
	$cert1 = "text/xml";
	
	if ($_FILES['img']['name'][0] != "") {
		$abpath="images/template";
		
		//Checks if file is an image
		move_uploaded_file($_FILES['img']['tmp_name'][0], "$abpath/$template_name.svg") or 
			$log .= "Couldn't copy SVG to server<br>";
		if (file_exists("$abpath/$template_name.svg")) 
		{
			$log .= "Front SVG file was uploaded<br>";
		} else {
			$log .= "Front File is not a SVG<br>";
		}
		
		
		//Process file
		$dest_file="$abpath/$template_name.svg";
		$lines=file($dest_file);

		$output = fopen($dest_file, "w");
		$i=1;
		$alignment="";
		$dataset=false;
		$tag=array();
		
		chmod($dest_file, 0664); //rw-rw-r-- 
		
		$watch=array();
		$watch[0]=false;
		$deleter=array();
		$deleter[0] = false;
		$z=array();
		$z[0] = "THIS IS A PLACE HOLDER AND SHOULD NOT BE NOTICED";
		/*
		$watch=false;
		$deleter=false;
		$z="THIS IS A PLACE HOLDER";
		*/
	

		foreach($lines as $a) //Parse each line of uploaded file
		{
			//Fix span lines with place holder
			$a = preg_replace("/<p><span> </span></p>/", "<p><span>~~$i~~</span></p>", $a);
			$a = preg_replace("/category=\"&ns_vars;\"/", "category=\"http://ns.adobe.com/Variables/1.0/\"", $a);
			//purge the bullet code
			$a = str_replace("·"," ",$a);
			$a = str_replace("•"," ",$a);
			$a = str_replace("®"," ",$a);
			//turn off borders
			$a = str_replace("true","false",$a);
			//Drop place holder into display lines
			if(preg_match("/\">(·| ) *</tspan>/", $a))
			{
				$a = preg_replace("/\"> +</tspan></text>/", "\">~~$i~~</tspan></text>", $a);  //The only problem with this is when Illustrator defines each space as a seperate tspan
				$i++;
			}
			
			//Check for the start of a dataset (variable area)
			if(preg_match("/<v:sampleDataSets/", $a))
			{
				$dataset=true;
			}
			if(preg_match("/</v:sampleDataSets/", $a)) //End of dataset, reset line counter
			{
				$dataset=false;
				$i=1;
			}
			
			//Do Variable replacement
			if($dataset===true && preg_match("/<p>.*</p>/", $a))
			{
				$a = preg_replace("/<p>.*</p>/", "<p>~~$i~~</p>", $a);
				$i++;
			}
			if(preg_match("/><span.*> *</span></p>/", $a))
			{
				$a = preg_replace("/span.*>.*</span></p>/", "span>~~$i~~</span></p>", $a);
			}
			
			//Do picture inserts
			if($dataset && preg_match("/<Photo>/",$a)) //This will fix the variable set reference
			{
				$a = "\t\t\t\t\t\t<Photo>~~insert shortref~~</Photo>\n";
			}
			if(preg_match("/".$z[0]."/", $a)) //This has to be before $z is set so that it doesn't trip on the same line that sets it.
			{	
				$watch[0]=true;
				// This variable watches for the ID tag so that we know we have gotten to the photo block
				//echo "Set Watch[0] to true.\n<br>";
			}
			if(preg_match("/varName=\"Photo\"/",$a)) //Grab the ID number of the Photo block.  This always comes substantially before the block itself
			{
				$tag=explode("_", $a, 3);
				$z[0]="XMLID_" . $tag[1] . "_";
				//echo "Photo Tag found.\n<br>";
				
			}
			if($watch[0] && preg_match("/i:linkRef=\".*\"/", $a))
			{
				$a = preg_replace("/i:linkRef=\".*\"/", "i:linkRef=\"~~insert shortref~~\"", $a);
				//echo "Shortref placeholder insert\n<br>";
			}
			if($deleter[0] && preg_match("/^.*\"/", $a)) //This must come before deleter is set, or the last half of the file will be skipped over.
			{
				$deleter[0]=false;
				$watch[0]=false;
				$a = preg_replace("/^.*\" i:/", " i:", $a);
				//echo "End of Delete range found. Deleter[0] and Watch[0] set to false.\n<br>";
			}
			if($watch[0] && preg_match("/xlink:href=\"data:;.*/",$a)) //Mark the first line of the photo block and set flags to skip to the end of the encoded info
			{
				$a = preg_replace("/xlink:href=\"data:;.*/",  "xlink:href=\"http://www.bizcardstoday.com/~~insert picture~~\"", $a);
				fwrite($output, $a);
				$deleter[0]=true;
				//echo "Begin Delete rand found. Deleter[0] set to true.\n<br>";
			}
			//Check for alignment headers
			if(preg_match("/text-align=\"right\"/", $a) || preg_match("/text-align:right/", $a)) //These must be placed in exactly the right spot or they will not be applied in the browser
				$alignment="text-anchor=\"end\"";
			if(preg_match("/text-align=\"center\"/", $a) || preg_match("/text-align:center/", $a))
				$alignment="text-anchor=\"middle\"";
			$pattern = "\">~~" . ($i-1) . "~~</tspan>";
			if($alignment!="" && preg_match("/<text /",$a) && preg_match("/".$pattern."/", $a))
			{
				//ereg("<text .* transform=\".*\"", $a, $temp);
				//$a = $temp[0] . " " . $a;
				$a=preg_replace("/<text /", "<text $alignment ", $a);
				$alignment="";
			}else if($alignment!="" && preg_match("/<text /",$a) )
			{
				$alignment="";
			}

			if(!$deleter[0])
				fwrite($output, $a);
		}
		
		$line_count=($i-1);
		$statement="UPDATE Templates SET Templates.Lines=$line_count WHERE ID=$template";
		$sql->Update($statement);
	}
	
	
	if ($_FILES['img']['name'][1] != "") {
		$abpath="images/finished";
		
		//Checks if file is an image
		move_uploaded_file($_FILES['img']['tmp_name'][1], "$abpath/$template.svg") or $log .= "Couldn't copy Back SVG to server\n";
		if (file_exists("$abpath/$filename.svg")) {
			$log .= "Back SVG file was uploaded\n";
		} else {
			$log .= "Back File is not a SVG\n";
		}
		$statement = "UPDATE Templates SET 2_Sided='y' WHERE ID=$template";
		$sql->Update($statement);
	}
	
	$statement="SELECT * FROM Finished_Cards WHERE Template=$template";
	$sql->Query($statement);
	$j=0;
	while($j<$sql->rows)
	{
		$sql->Fetch($j);
		$row=$sql->data;
		$lines=file("images/template/$template_name.svg");
		$b = file("images/finished/$template-" . $row['ID'] . ".svg");
		$output = fopen("images/finished/" . $template . "-" . $row['ID'] . ".svg", "w");
		
		$row['Line_1'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes( str_replace("&quot;", "\"", $row['Line_1'])))));
		$row['Line_2'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_2'])))));
		$row['Line_3'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_3'])))));
		$row['Line_4'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_4'])))));
		$row['Line_5'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_5'])))));
		$row['Line_6'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_6'])))));
		$row['Line_7'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_7'])))));
		$row['Line_8'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_8'])))));
		$row['Line_9'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_9'])))));
		$row['Line_10'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_10'])))));
		$row['Line_11'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_11'])))));
		$row['Line_12'] = str_replace('&amp;reg;', '®', str_replace('*', '·', htmlentities(stripslashes(str_replace("&quot;", "\"", $row['Line_12'])))));
		foreach($lines as $a=>$b)
		{
			//Swap out ~~X~~ for the Line X value. This includes swapping out * for Bullet	
			$b = str_replace('~~1~~', $row['Line_1'], str_replace('~~2~~', $row['Line_2'], 
				str_replace('~~3~~', $row['Line_3'], str_replace('~~4~~', $row['Line_4'], 
				str_replace('~~5~~', $row['Line_5'], str_replace('~~6~~', $row['Line_6'], 
				str_replace('~~7~~', $row['Line_7'], str_replace('~~8~~', $row['Line_8'], 
				str_replace('~~9~~', $row['Line_9'], str_replace('~~10~~', $row['Line_10'], 
				str_replace('~~11~~', $row['Line_11'], str_replace('~~12~~', $row['Line_12'], $b)))))))
				)))));
			
			//Swap out picture placeholders
			$b = str_replace('~~insert picture~~', "/uploads/$template-" . $row['ID'] . ".svg", $b);
			$b = str_replace('~~insert shortref~~', "$template-" . $row['ID'] . ".svg", $b);
			fwrite($output, $b);
		}
		$j++;
	}
}

echo "<!--$log-->\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>Template Updater &amp; Backside Loader</title>
	</head>

	<body bgcolor="#ffffff">
		<form action=template_update.php method=post enctype="multipart/form-data" name=Form1>
		<input type=hidden name=submitted value=true>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td>Current Front:<br><embed src="/images/template/<? echo $template_name ?>.svg"></td>
			</tr><?
				$str_file="images/finished/$template.svg";
				echo "<!--$str_file-->\n";
				if(file_exists($str_file))
					echo "<tr>\n<td>Current Back:<br><embed src='/images/finished/$template.svg'></td>\n</tr>";?><tr>
				<td>Front Side to update:<input type=file name="img[]"></td>
			</tr><tr>
				<td>Backside of Card:<input type=file name="img[]"></td>
			</tr><tr>
				<td><input type=submit value="Begin Upload">&nbsp;&nbsp;&nbsp;
				<input type=button value="Return to Menu" onclick="window.location='welcome.php'"></td>
			</tr>
		</table>
		</form>
	</body>

</html>