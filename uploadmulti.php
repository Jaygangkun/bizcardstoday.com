<?
session_start();
// if($template==0 || !session_is_registered("template") || $template=="") //Boot to homepage if card template is not set.
	if(!session_is_registered("template")) //Boot to homepage if card template is not set.
	header("Location: index2.php");

require("util.php");
$sql = new MySQL_class;
$sql->Create("bizcardstodaynew");

$statement = "SELECT max(ID)+1 FROM Forms";
$sql->QueryItem($statement);
if($sql->data[0]=="")
	$sql->data[0]=1;



foreach($_POST as $a=>$b)
{
	if($a!="submitted" && $a!="img" && $a!="Filename")
		$form_local[$a]=$b;
}
$filename=$form_local["Template"] . "-" . $sql->data[0];
$form_local['ID']=$sql->data[0];

$form_local['Filename']=$filename;
//user defined variables
$JPGpath = "images/JPG"; //Absolute path to where images are uploaded. No trailing slash
$PDFpath = "images/PDF";
$sizelim = "no"; //Do you want size limit, yes or no
$size = "2500000"; //What do you want size limited to be if there is one

if ($_REQUEST['submitted']){ // Begin processing portion of script

//all image types to upload

$cert1 = "image/pjpeg";
$cert2 = "image/jpeg"; //Jpeg type 2
$cert18 = "application/pdf";

$log = "";
$log .=print_r($_FILES) . "<br>\n";



	if ($_FILES['img']['name'][2] != "") {
		$abpath="images/PDF";
	
		//checks if file exists
		if (file_exists("$abpath/$filename.pdf")) {
			$log .= "PDF already exists for this form<br>";
		} else {
		//Checks if file is an image
			copy($_FILES['img']['tmp_name'][2], "$abpath/$filename.pdf") or $log .= "Couldn't copy PDF to server<br>";
			if (file_exists("$abpath/$filename.pdf")) {
				$log .= "PDF file was uploaded<br>";
			}
		}
	}

	if ($_FILES['img']['name'][0] != "") {
			$abpath="images/JPG";
			
		//checks if file exists
		if (file_exists("$abpath/$filename.jpg")) {
			$log .= "JPG already exists for this form<br>";
		} else {
			//Checks if file is an image
			move_uploaded_file($_FILES['img']['tmp_name'][0], "$abpath/$filename.jpg") or $log .= "Couldn't copy JPG to server<br>";
			if (file_exists("$abpath/$filename.jpg")) {
				$log .= "JPG file was uploaded<br>";
				$pic = getimagesize("$abpath/$filename.jpg");
				$form_local['Width']=$pic[0];
				$form_local['Height']=$pic[1];
			} else {
				$log .= "File is not a JPG<br>";
			}
		}
	}

	if ($_FILES['img']['name'][1] != "") {
		$form_local['2sided']='y';
		$abpath="images/JPG";
			
		//checks if file exists
		if (file_exists("$abpath/$filename-back.jpg")) {
			$log .= "JPG already exists for this form<br>";
		} else {
			//Checks if file is an image
			move_uploaded_file($_FILES['img']['tmp_name'][1], "$abpath/$filename-back.jpg") or $log .= "Couldn't copy JPG Back to server<br>";
			if (file_exists("$abpath/$filename-back.jpg")) {
				$log .= "JPG Back file was uploaded<br>";
			} else {
				$log .= "File is not a JPG<br>";
			}
		}
	}
}
?>

<html>
<head>
<title>Form Upload report</title>
<meta http-equiv="REFRESH" content="0;url=<? echo $form_local['from']?>.php">
</head>
<body>
<p>Log:<br>
<?

echo "$log";

?>
</p>
<body>
</html>



