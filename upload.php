<?php
session_start();
// echo('<pre>');
// print_r($_POST);
// exit('</pre>');
include_once('firelogger/firelogger.php');

flog('post', $_POST);
flog('files', $_FILES);
flog('photo', $_FILES['photo']);
flog('session', $_SESSION);

if(isset($_FILES['photo']['name']))
	$photo_name = $_FILES['photo']['name'];
if(isset($_FILES['photo']['type']))
	$photo_type = $_FILES['photo']['type'];
if(isset($_FILES['photo']['type']))
	$photo = $_FILES['photo']['tmp_name'];
if(isset($_SESSION['template']))
	$template = $_SESSION['template'];
	
	
// Original script developed by the Zach White Network.
// Modifications for flexible multi-uploads developed by
// Greg Johnson
// gjohnson@7south.com
// 7 South Communications, Inc.
// www.7south.com
// Mod Date: 11-07-02


// 	$_POST['Card_ID']=$card['ID'];
	$card = $_SESSION['card'];
	$_POST['Card_ID'] = $_SESSION['card']['ID'];
	foreach($card as $a=>$b)
	{
		if(preg_match("/^([0-9])*/", $a))
			$card[$a]="";
	}
	
	if($_POST['Line_1']!="") $card['Line_1'] = $_POST['Line_1'];
	$card['Line_2'] = $_POST['Line_2'];
	$card['Line_3'] = $_POST['Line_3'];
	$card['Line_4'] = $_POST['Line_4'];
	$card['Line_5'] = $_POST['Line_5'];
	$card['Line_6'] = $_POST['Line_6'];
	$card['Line_7'] = $_POST['Line_7'];
	$card['Line_8'] = $_POST['Line_8'];
	$card['Line_9'] = $_POST['Line_9'];
	$card['Line_10'] = $_POST['Line_10'];
	$card['Line_11'] = $_POST['Line_11'];
	$card['Line_12'] = $_POST['Line_12'];
	if($_POST['Card_Name']!="") $card['Card_Name'] = $_POST['Card_Name'];
	$card['ID'] = $_POST['ID'];
	$card['Action']=$_POST['Action'];

	
/*
	$Quantity = $HTTP_POST_VARS['Quantity'];
	$speed = $HTTP_POST_VARS['speed'];
	$ship = $HTTP_POST_VARS['ship'];
	$po = $HTTP_POST_VARS['po'];
	//$Company = $HTTP_POST_VARS['company'];
	$company_name=$HTTP_POST_VARS['company_name'];
	$Name=$HTTP_POST_VARS['name'];
	$Address1=$HTTP_POST_VARS['address1'];
	$Address2=$HTTP_POST_VARS['address2'];
	$City=$HTTP_POST_VARS['city'];
	$State=$HTTP_POST_VARS['state'];
	$Zip=$HTTP_POST_VARS['zip'];
*/
/*
foreach($_POST as $a=>$b)
{
	$card[$a]=$b;
}	
*/
//print_r($card);
$card['upload']=true;
//user defined variables
$abpath = "images/uploads"; //Absolute path to where images are uploaded. No trailing slash
$sizelim = "no"; //Do you want size limit, yes or no
$size = "2500000"; //What do you want size limited to be if there is one
$number_of_uploads = 1;  //Number of uploads to occur

if ($_POST['submitted']){ // Begin processing portion of script

//all image types to upload
$cert1 = "image/pjpeg"; //Jpeg type 1
$cert2 = "image/jpeg"; //Jpeg type 2
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

$log = "";

//checks if file exists
if ($photo_name == "") {
	$log .= "No file selected for upload<br>";
}

if ($photo_name != "") 
{
	//Checks if file is an image
	if (($photo_type == $cert2) || ($photo_type == $cert1)) 
	{
		$dest_file="$abpath/" . $template . "_" . $card['ID'] . ".jpg";
flog('destFile', $dest_file);
		move_uploaded_file($photo, $dest_file) or $log .= "Couldn't copy image 1 to server<br>";
		if (file_exists($dest_file)) 
		{
			$log .= "File uploaded to $dest_file";
			$ShortFile= $template . "_" . $card['ID'] . ".jpg";
			$CurCardID=$dest_file;
			chmod($dest_file, 0775);
		}
	}else 
	{
		$log .= "File $i is not an valid image<br>";
	}
}

$_SESSION['imageIdFlag'] = $card['ID'];
$_SESSION['imageNameFlag'] = $card['Card_Name'];

//$card['LastAction']="INSERT INTO Order_History SET Order_id=" . $card['ID'] . ", Date_Stamp=now(), Action=\"Upload\", Done_By=\"";
//if($full=='y')
//	$card['LastAction'].="BizCards $user\"";
//elseif($partial=='y')
//	$card['LastAction'] .= "Admin $user\"";
//else
//	$card['LastAction'] .= $user . "\"";
/*
print_r($template);
echo "<br>\n";
print_r($_REQUEST['Line_1']);
echo "<br>\n";
print_r($photo_name);
echo "<br>\n";
print_r($photo_size);
echo "<br>\n";
print_r($photo_type);
echo "<br>\n";
echo "<br>\n";
print_r($photo);
echo "<br>\n";
print_r($dest_file);
echo "<br>\n";
*/
// exit('-here-');
?>

<html>
<head>
<meta http-equiv="REFRESH" content="0;url=editpad.php">
<title>Image Report</title>
</head>
<body>
<?php

echo $log; 

?>
<body>
</html>
<?php 
exit;
} // End processing portion of script
?>
