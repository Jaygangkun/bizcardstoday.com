<?php
	session_start();
// Original script developed by the Zach White Network.
// Modifications for flexible multi-uploads developed by
// Greg Johnson
// gjohnson@7south.com
// 7 South Communications, Inc.
// www.7south.com
// Mod Date: 11-07-02

	
	$line_1 = $HTTP_POST_VARS['line_1'];
	$line_2 = $HTTP_POST_VARS['line_2'];
	$line_3 = $HTTP_POST_VARS['line_3'];
	$line_4 = $HTTP_POST_VARS['line_4'];
	$line_5 = $HTTP_POST_VARS['line_5'];
	$line_6 = $HTTP_POST_VARS['line_6'];
	$line_7 = $HTTP_POST_VARS['line_7'];
	$line_8 = $HTTP_POST_VARS['line_8'];
	$line_9 = $HTTP_POST_VARS['line_9'];
	$line_10 = $HTTP_POST_VARS['line_10'];
	$line_11 = $HTTP_POST_VARS['line_11'];
	$line_12 = $HTTP_POST_VARS['line_12'];
	$Quantity = $HTTP_POST_VARS['Quantity'];
	$speed = $HTTP_POST_VARS['speed'];
	$ship = $HTTP_POST_VARS['ship'];
	$po = $HTTP_POST_VARS['po'];
	$Company = $HTTP_POST_VARS['company'];
	$Name=$HTTP_POST_VARS['name'];
	$Address1=$HTTP_POST_VARS['address1'];
	$Address2=$HTTP_POST_VARS['address2'];
	$City=$HTTP_POST_VARS['city'];
	$State=$HTTP_POST_VARS['state'];
	$Zip=$HTTP_POST_VARS['zip'];
	
	
	
//user defined variables
$abpath = "images/uploads"; //Absolute path to where images are uploaded. No trailing slash
$sizelim = "no"; //Do you want size limit, yes or no
$size = "2500000"; //What do you want size limited to be if there is one
$number_of_uploads = 1;  //Number of uploads to occur

if ($_REQUEST['submitted']){ // Begin processing portion of script

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

if ($photo_name != "") {
	//Checks if file is an image
	if (($photo_type == $cert2) || ($photo_type == $cert1)) {
		$dest_file="$abpath/" . $template . "_" . $card['ID'] . ".jpg";
		move_uploaded_file($photo, $dest_file) or $log .= "Couldn't copy image 1 to server<br>";
		if (file_exists($dest_file)) {
			$log .= "File uploaded to $dest_file";
			$ShortFile= $template . "_" . $card['ID'] . ".jpg";
			$CurCardID=$dest_file;
			chmod($dest_file, 0755);
		}
	} else {
		$log .= "File $i is not an valid image<br>";
	}
}
/*
print_r($template);
echo "<br>\n";
print_r($_REQUEST['line_1']);
echo "<br>\n";
print_r($photo_name);
echo "<br>\n";
print_r($photo_size);
echo "<br>\n";
print_r($photo_type);
echo "<br>\n";
print_r($photo);
echo "<br>\n";
print_r($dest_file);
echo "<br>\n";
*/
?>

<html>
<head>
<meta http-equiv="REFRESH" content="0;url=proof2.php">
<title>Image Report</title>
</head>
<body>

<body>
</html>
<? 
exit;
} // End processing portion of script
?>