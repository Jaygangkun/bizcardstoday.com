<?php

include 'ChromePhp.php';
require_once('firelogger/firelogger.php');
session_start();
ChromePhp::log($_FILES);
ChromePhp::log($_POST);
/*	This page will allow the upload of a specified svg file, and parse it into the necessary
 * formatting for use. */

// The variable photo refers the to svg file.

session_start();


// if(!session_is_registered("dest_file")) session_register("dest_file");
if(!isset($_SESSION["dest_file"])) $_SESSION["dest_file"]='';

//Load all the values of the form for passback
$_SESSION['per250'] = $_POST['per250']; //$per250
$_SESSION['per500'] = $_POST['per500']; //$per500
$_SESSION['per1000'] = $_POST['per1000']; //$per1000
$_SESSION['per2000'] = $_POST['per2000']; //$per2000
$_SESSION['per250_premium'] = $_POST['per250_premium'];//$per250_premium
$_SESSION['per500_premium'] = $_POST['per500_premium'];//$per500_premium
$_SESSION['per1000_premium'] = $_POST['per1000_premium'];//$per1000_premium
$_SESSION['per2000_premium'] = $_POST['per2000_premium'];//$per2000_premium
$_SESSION['quickcard_price'] = $_POST['quickcard_price'];//$QuickCard_Price
$_SESSION['card_quality'] = $_POST['card_quality'];//$card_quality
$_SESSION['pic_upload'] = $_POST['pic_upload'];//$Pic_Upload
$_SESSION['pic_width'] = $_POST['pic_width'];//$Pic_Width
$_SESSION['pic_height'] = $_POST['pic_height'];//$Pic_Height
$_SESSION['2_sided'] = $_POST['2_sided'];//$Two_Sided
$_SESSION['paper'] = $_POST['paper'];//$Paper
$_SESSION['paper_premium'] = $_POST['paper_premium'];//$Paper_premium
$_SESSION['ink'] = $_POST['ink'];//$Ink
$_SESSION['vertical'] = $_POST['vertical'];//$Vertical
$_SESSION['allow_pdf'] = $_POST['allow_pdf'];//$Allow_PDF
$_SESSION['approval_req'] = $_POST['approval_req'];//$Approval_Req
$_SESSION['approval_email'] = $_POST['approval_email'];//$Approval_Email
$_SESSION['approval_phone'] = $_POST['approval_phone'];//$Approval_Phone
$_SESSION['approval_name'] = $_POST['approval_name'];//$Approval_Name
$_SESSION['inactive'] = $_POST['inactive'];//$Inactive
$_SESSION['quantity'] =  $_POST['quantity'];//$Quantity
$_SESSION['default_value'] = $_POST['default_value'];//$default_value
if($_POST['company_name'] == "" && $_POST['old_company'] != "")
    $_POST['company_name'] = $_POST['old_company'];
$_SESSION['company_name'] =  str_replace("\"", "&quot;", $_POST['company_name']);//$company_name
$_SESSION['template_name'] = str_replace("\"", "&quot;", $_POST['template_name']); //$Template_Name
$_SESSION['name'] = str_replace("\"", "&quot;", $_POST['name']); //$Name
$_SESSION['address1'] = str_replace("\"", "&quot;", $_POST['address1']); //$Address1
$_SESSION['address2'] = str_replace("\"", "&quot;", $_POST['address2']); //$Address2
$_SESSION['city'] = str_replace("\"", "&quot;", $_POST['city']); //$City
$_SESSION['state'] = $_POST['state']; //$State
$_SESSION['zip'] = $_POST['zip']; //$Zip
//$Printer_Email = $_POST['Printer_Email'];

//print_r($_POST);

//user defined variables
$abpath = "images/template"; //Absolute path to where images are uploaded. No trailing slash
$sizelim = "no"; //Do you want size limit, yes or no
$size = "1000000"; //What do you want size limited to be if there is one
$number_of_uploads = 1;  //Number of uploads to occur

if($_POST['submitted'])
{ // Begin processing portion of script
   flog('submitted');
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

flog('files', $_FILES);
//echo('<pre>');
//print_r($_FILES);
//echo('</pre>');

   if(isset($_FILES['photo']))
   {
	 $photo_name = $_FILES['photo']['name'];
	 $imageType = $_FILES['photo']['type'];
	 $photo = $_FILES['photo']['tmp_name'];
flog('photo_name', $photo_name);
ChromePhp::log($photo_name);
   }

   //checks if file exists

   if($photo_name == "")
   {
//	 echo "No file selected for upload<br>";
   }

//   print_r($_REQUEST['Company']);
//   echo "<br>\n";
//   print_r($photo_name);
//   echo "<br>\n";
//   print_r($_FILES['photo']['type']);
//   echo "<br>\n";
//   print_r($photo_size);
//   echo "<br>\n";
//   print_r($photo_type);
//   echo "<br>\n";
//   print_r($photo);
//   echo "<br>\n";
   foreach($_POST as $a=>$b)
   {
//	 print_r($a);
//	 echo "=>";
//	 print_r($b);
//	 echo "<br>\n";
   }

ChromePhp::log('test5');


   if ($photo_name != "")
   {
	 //Autoname the file
	 //Complete the upload by moving and renaming the file
	 $dest_file = "$abpath/" .
		str_replace(",", "", str_replace("\"", "", str_replace("'", "", str_replace(".", "",
		(str_replace(" ", "", stripslashes($_POST['template_name']))))))) . ".svg";

	 move_uploaded_file($photo, $dest_file) or print_r("Couldn't copy image 1 to server<br>");

	 if (file_exists($dest_file))
	 { //Success?
//	    echo "File uploaded to $dest_file<bR>\n";
	    chmod($dest_file, 0664); //rw-rw-r--

	    //Process file
	    $lines = file($dest_file);
	    $output = fopen($dest_file, "w");
	    $i = 1;
	    $alignment = "";
	    $dataset = false;
	    $tag = array();

	    $watch = array();
	    $watch[0] = false;
	    $watch[1] = false;
	    $watch[2] = false;
	    $watch[3] = false;
	    $watch[4] = false;
	    $deleter = array();
	    $deleter[0] = false;
	    $deleter[1] = false;
	    $deleter[2] = false;
	    $deleter[3] = false;
	    $deleter[4] = false;
	    $z = array();
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
		  $a = str_replace(""," ",$a);
		  $a = str_replace(""," ",$a);
		  //turn off borders
		  $a = str_replace("true","false",$a);
		  $a2 = "-$a-";
		  //Drop place holder into display lines
//echo($a2);
//		  if(ereg("\">(| ) *</tspan>", $a))
			$p1 = '/' . "\">(| ) *<\/tspan>" . '/';
//echo('<br>' . $p1);
		  if(preg_match($p1, $a))
		  {
		  //The only problem with this is when Illustrator defines each space as a seperate tspan
			$a = preg_replace("/\"> +</tspan></text>/", "\">~~$i~~</tspan></text>", $a);
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
		  //This will fix the variable set reference
		  if($dataset && preg_match("/<$photo_var>/",$a))
		  {
			$a = "\t\t\t\t\t\t<$photo_var>~~insert shortref~~</$photo_var>\n";
		  }
		  //This will fix the variable set reference
		  if($dataset && preg_match("/<Symbol1>/",$a))
		  {
			$a = "\t\t\t\t\t\t<Symbol1>~~insert symbol1~~</Symbol1>\n";
		  }
		  //This will fix the variable set reference
		  if($dataset && preg_match("/<Symbol2>/",$a))
		  {
			$a = "\t\t\t\t\t\t<Symbol2>~~insert symbol2~~</Symbol2>\n";
		  }
		  //This will fix the variable set reference
		  if($dataset && preg_match("/<Symbol3>/",$a))
		  {
			$a = "\t\t\t\t\t\t<Symbol3>~~insert symbol3~~</Symbol3>\n";
		  }
		  //This will fix the variable set reference
		  if($dataset && preg_match("/<Symbol4>/",$a))
		  {
			$a = "\t\t\t\t\t\t<Symbol4>~~insert symbol4~~</Symbol4>\n";
		  }
		  //This has to be before $z is set so that it doesn't trip on the same line that sets it.
		  if(preg_match("/".$z[0]."/", $a))
		  {
			$watch[0]=true;
			// This variable watches for the ID tag so that we know we have gotten to the photo block
			echo "Set Watch[0] to true.\n<br>";
		  }
		  //Grab the ID number of the Photo block.  This always comes substantially before the block itself
		  if(preg_match("/varName=\"Photo\"/",$a))
		  {
			$tag=explode("_", $a, 3);
			$z[0]="XMLID_" . $tag[1] . "_";
			echo "Photo Tag found.\n<br>";

		  }
		  if($watch[0] && preg_match("/i:linkRef=\".*\"/", $a))
		  {
			$a = preg_replace("/i:linkRef=\".*\"/", "i:linkRef=\"~~insert shortref~~\"", $a);
			echo "Shortref placeholder insert\n<br>";
		  }//This must come before deleter is set, or the last half of the file will be skipped over.
		  if($deleter[0] && preg_match("/^.*\"/", $a))
		  {
			$deleter[0]=false;
			$watch[0]=false;
			$a = preg_replace("/^.*\" i:/", " i:", $a);
			echo str_replace("<", "&lt;", str_replace(">", "&gt;", $a)) . "\n<br>";
			echo "End of Delete range found. Deleter[0] and Watch[0] set to false.\n<br>";
		  }
		  //Mark the first line of the photo block and set flags to skip to the end of the encoded info
		  if($watch[0] && preg_match("/xlink:href=\"data:;.*/",$a))
		  {
			$a = preg_replace("/xlink:href=\"data:;.*/",
				"xlink:href=\"http://www.bizcardstoday.com/~~insert picture~~\"", $a);
			fwrite($output, $a);
			$deleter[0]=true;
			echo str_replace("<", "&lt;", str_replace(">", "&gt;", $a)) . "\n<br>";
			echo "Begin Delete found. Deleter[0] set to true.\n<br>";
		  }

		  //Check for alignment headers
		  //These must be placed in exactly the right spot or they will not be applied in the browser
		  if(preg_match("/text-align=\"right\"/", $a) || preg_match("/text-align:right/", $a))
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
$line_count = ($i-1);

//flog('line count', $line_count);
//flog('Session', $_SESSION);
//flog('Post', $_POST);
$_SESSION['temp_lineCnt'] = $line_count;
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
//exit();
} // End processing portion of script
?>