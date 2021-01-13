<?php
/*	This page will allow the upload of a specified svg file, and parse it into the necessary 
 * formatting for use. */

	session_start();
	// if(!session_is_registered("dest_file"))
	if(!isset($_SESSION["dest_file"]))
		// session_register("dest_file");
		$_SESSION['dest_file'] = '';

//Load all the values of the form for passback
	$per250=$_POST['per250'];
	$per500=$_POST['per500'];
	$per1000=$_POST['per1000'];
	$per2000=$_POST['per2000'];
	$per250_premium=$_POST['per250_premium'];
	$per500_premium=$_POST['per500_premium'];
	$per1000_premium=$_POST['per1000_premium'];
	$per2000_premium=$_POST['per2000_premium'];
	$QuickCard_Price=$_POST['QuickCard_Price'];
	$card_quality=$_POST['card_quality'];
	$Pic_Upload=$_POST['Pic_Upload'];
	$Pic_Width = $_POST['Pic_Width'];
	$Pic_Height = $_POST['Pic_Height'];
	$Two_Sided=$_POST['2_Sided'];
	$Paper=$_POST['Paper'];
	$Paper_premium=$_POST['Paper_premium'];
	$Ink=$_POST['Ink'];
	$Vertical=$_POST['Vertical'];
	$Allow_PDF=$_POST['Allow_PDF'];
	$Approval_Req=$_POST['Approval_Req'];
	$Approval_Email=$_POST['Approval_Email'];
	$Approval_Phone=$_POST['Approval_Phone'];
	$Approval_Name=$_POST['Approval_Name'];
	$Inactive=$_POST['Inactive'];
	$Quantity = $HTTP_POST_VARS['Quantity'];
	$default_value = $HTTP_POST_VARS['default_value'];
	if($_POST['Company_Name']=="" && $_POST['Old_Company']!="")
		$_POST['Company_Name']=$_POST['Old_Company'];
	$company_name = str_replace("\"", "&quot;", $HTTP_POST_VARS['Company_Name']);
	$Template_Name = str_replace("\"", "&quot;", $HTTP_POST_VARS['Template_Name']);
	$Name=str_replace("\"", "&quot;", $HTTP_POST_VARS['name']);
	$Address1=str_replace("\"", "&quot;", $HTTP_POST_VARS['address1']);
	$Address2=str_replace("\"", "&quot;", $HTTP_POST_VARS['address2']);
	$City=str_replace("\"", "&quot;", $HTTP_POST_VARS['city']);
	$State=$HTTP_POST_VARS['state'];
	$Zip=$HTTP_POST_VARS['zip'];
	$Printer_Email=$HTTP_POST_VARS['Printer_Email'];
	
print_r($_POST);	
	
//user defined variables
$abpath = "images/template"; //Absolute path to where images are uploaded. No trailing slash
$sizelim = "no"; //Do you want size limit, yes or no
$size = "1000000"; //What do you want size limited to be if there is one
$number_of_uploads = 1;  //Number of uploads to occur

if ($_POST['submitted'])
{ // Begin processing portion of script

//all image types to upload
//$cert1 = "image/pjpeg"; //Jpeg type 1
//$cert2 = "image/jpeg"; //Jpeg type 2
//$cert3 = "image/gif"; //Gif type
//$cert4 = "image/ief"; //Ief type
//$cert5 = "image/png"; //Png type
//$cert6 = "image/tiff"; //Tiff type
//$cert7 = "image/bmp"; //Bmp Type
//$cert8 = "image/vnd.wap.wbmp"; //Wbmp type
//$cert9 = "image/x-cmu-raster"; //Ras type
//$cert10 = "image/x-x-portable-anymap"; //Pnm type
//$cert11 = "image/x-portable-bitmap"; //Pbm type
//$cert12 = "image/x-portable-graymap"; //Pgm type
//$cert13 = "image/x-portable-pixmap"; //Ppm type
//$cert14 = "image/x-rgb"; //Rgb type
//$cert15 = "image/x-xbitmap"; //Xbm type
//$cert16 = "image/x-xpixmap"; //Xpm type
//$cert17 = "image/x-xwindowdump"; //Xwd type
$cert18 = "text/xml";

$log = "";

//checks if file exists
if ($photo_name == "") {
	echo "No file selected for upload<br>";
}

print_r($_REQUEST['Company']);
echo "<br>\n";
print_r($photo_name);
echo "<br>\n";
print_r($_FILES['photo']['type']);
echo "<br>\n";
print_r($photo_size);
echo "<br>\n";
print_r($photo_type);
echo "<br>\n";
print_r($photo);
echo "<br>\n";
foreach($HTTP_POST_FILES as $a=>$b)
{
	print_r($a);
	echo "=>";
	print_r($b);
	echo "<br>\n";
}

if ($photo_name != "") {
	$dest_file="$abpath/" .  str_replace(",", "", str_replace("\"", "", str_replace("'", "", str_replace(".", "", (str_replace(" ", "", stripslashes($_REQUEST['Template_Name']))))))) . ".svg"; //Autoname the file
	move_uploaded_file($photo, $dest_file) or print_r("Couldn't copy image 1 to server<br>"); //Complete the upload by moving and renaming the file
	if (file_exists($dest_file)) 
	{ //Success?
		echo "File uploaded to $dest_file<bR>\n";
		chmod($dest_file, 0664); //rw-rw-r-- 
		
		//Process file
		$lines=file($dest_file);
		$output = fopen($dest_file, "w");
		$i=1;
		$alignment="";
		$dataset=false;
		$tag=array();
		
		$watch=array();
		$watch[0]=false;
		$watch[1]=false;
		$watch[2]=false;
		$watch[3]=false;
		$watch[4]=false;
		$deleter=array();
		$deleter[0] = false;
		$deleter[1] = false;
		$deleter[2] = false;
		$deleter[3] = false;
		$deleter[4] = false;
		$z=array();
		$z[0] = "THIS IS A PLACE HOLDER AND SHOULD NOT BE NOTICED";
		$z[1] = "THIS IS A PLACE HOLDER AND SHOULD NOT BE NOTICED";
		$z[2] = "THIS IS A PLACE HOLDER AND SHOULD NOT BE NOTICED";
		$z[3] = "THIS IS A PLACE HOLDER AND SHOULD NOT BE NOTICED";
		$z[4] = "THIS IS A PLACE HOLDER AND SHOULD NOT BE NOTICED";
		/*
		$watch=false;
		$deleter=false;
		$z="THIS IS A PLACE HOLDER";
		*/
		$photo_var="Photo";

		foreach($lines as $a) //Parse each line of uploaded file
		{
			//Fix span lines with place holder
			$a = preg_replace("/<p><span> </span></p>/", "<p><span>~~$i~~</span></p>", $a);
			$a = preg_replace("/category=\"&ns_vars;\"/", "category=\"http://ns.adobe.com/Variables/1.0/\"", $a);
			//purge the bullet code
			$a = str_replace("·"," ",$a);
			$a = str_replace("•"," ",$a);
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
			if($dataset===true)
			{
				if(preg_match("/trait=\"fileref\"/", $a))
				{
					preg_match("/(varName=\")(.+)([\" ]{2})/", $a, $temp);
					print_r($temp);
					$photo_var=$temp[1];
					print_r($temp);
				}
			}
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
			if($dataset && preg_match("/<$photo_var>/",$a)) //This will fix the variable set reference
			{
				$a = "\t\t\t\t\t\t<$photo_var>~~insert shortref~~</$photo_var>\n";
			}
			if($dataset && preg_match("/<Symbol1>/",$a)) //This will fix the variable set reference
			{
				$a = "\t\t\t\t\t\t<Symbol1>~~insert symbol1~~</Symbol1>\n";
			}
			if($dataset && preg_match("/<Symbol2>/",$a)) //This will fix the variable set reference
			{
				$a = "\t\t\t\t\t\t<Symbol2>~~insert symbol2~~</Symbol2>\n";
			}
			if($dataset && preg_match("/<Symbol3>/",$a)) //This will fix the variable set reference
			{
				$a = "\t\t\t\t\t\t<Symbol3>~~insert symbol3~~</Symbol3>\n";
			}
			if($dataset && preg_match("/<Symbol4>/",$a)) //This will fix the variable set reference
			{
				$a = "\t\t\t\t\t\t<Symbol4>~~insert symbol4~~</Symbol4>\n";
			}
			
			if(preg_match("/".$z[0]."/", $a)) //This has to be before $z is set so that it doesn't trip on the same line that sets it.
			{	
				$watch[0]=true;
				// This variable watches for the ID tag so that we know we have gotten to the photo block
				echo "Set Watch[0] to true.\n<br>";
			}
			if(preg_match("/varName=\"Photo\"/",$a)) //Grab the ID number of the Photo block.  This always comes substantially before the block itself
			{
				$tag=explode("_", $a, 3);
				$z[0]="XMLID_" . $tag[1] . "_";
				echo "Photo Tag found.\n<br>";
				
			}
			if($watch[0] && preg_match("/i:linkRef=\".*\"/", $a))
			{
				$a = preg_replace("/i:linkRef=\".*\"/", "i:linkRef=\"~~insert shortref~~\"", $a);
				echo "Shortref placeholder insert\n<br>";
			}
			if($deleter[0] && preg_match("/^.*\"/", $a)) //This must come before deleter is set, or the last half of the file will be skipped over.
			{
				$deleter[0]=false;
				$watch[0]=false;
				$a = preg_replace("/^.*\" i:/", " i:", $a);
				echo str_replace("<", "&lt;", str_replace(">", "&gt;", $a)) . "\n<br>";
				echo "End of Delete range found. Deleter[0] and Watch[0] set to false.\n<br>";
			}
			if($watch[0] && preg_match("/xlink:href=\"data:;.*/",$a)) //Mark the first line of the photo block and set flags to skip to the end of the encoded info
			{
				$a = preg_replace("/xlink:href=\"data:;.*/",  "xlink:href=\"http://www.bizcardstoday.com/~~insert picture~~\"", $a);
				fwrite($output, $a);
				$deleter[0]=true;
				echo str_replace("<", "&lt;", str_replace(">", "&gt;", $a)) . "\n<br>";
				echo "Begin Delete found. Deleter[0] set to true.\n<br>";
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
		echo "File Processed: $dest_file<br>\n";
	}else
		echo "File not uploaded<br>\n";
}
$line_count=($i-1);
?>

<html>
<head>
<meta http-equiv="REFRESH" content="0;url=template_create.php">
<title>Image Report</title>
</head>
<body>
 
<body>
</html>
<?php 
exit;
} // End processing portion of script
?>