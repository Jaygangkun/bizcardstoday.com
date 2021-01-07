<?php
session_start();
// 	if($template==0 || !session_is_registered("template") || $template=="")
if(!session_is_registered("template")) //Boot to homepage if card template is not set.
header("Location: index2.php");

if(!session_is_registered("PDF"))
{
session_register("PDF");
}
require("util.php");
require("emailer.php");
$sql = new MySQL_class;
$sql->Create("bizcardstodaynew");


$statement = "SELECT *, c.Name as Company_Name FROM Templates t, Company c where t.company=c.id and t.ID=" . 
	$template;
$sql->QueryRow($statement);
$PDF['company'] = $sql->data['Company_Name'];
$PDF['name']=$sql->data['Name'];
$PDF['address1']=$sql->data['Address1'];
$PDF['address2']=$sql->data['Address2'];
$PDF['city']=$sql->data['City'];
$PDF['state']=$sql->data['State'];
$PDF['zip']=$sql->data['Zip'];
$PDF['po']=$sql->data['po'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<title>Welcome to BizCardsToday.com</title>
<script type="text/javascript" language="javascript">
// create a function to tell if the file is uploading or error, or finished
function disable(){
var obj = document.getElementById('uploadmes');
var messege = "<BR><BR><BR><BR><BR><BR><center><p><font color='#000000' size='2'>Preparing File. \n\Please be patient, this process can take around 2 minutes </font></p><BR><img src=Untitled-6.gif></center><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>";
obj.innerHTML = messege;
var button = document.getElementById('btn');
var dis = '<input disabled type="button" value="Upload Image" onClick="disable();" tabindex="2">';
button.innerHTML = dis;
}

function update(){
var obj = document.getElementById('uploadmes');
var messege = "<p><font color='#00FF00' size='2'>Uploading File. Please wait.</font></p>";
obj.innerHTML = messege;
}
</script>
</head>


<body bgcolor=FFFFFF>
<CENTER>
<table width=800 cellpadding=0 cellspacing=0 border=0><TR><TD colspan=2 bgcolor=FFFFFF>
	<TABLE style="border: 1px solid #000000;">
	<TR>
		<TD><img src=images/cardlogo.gif><font face=verdana size=1><BR>
			 <B>The fast, easy way to order and reorder business cards</B></td>
		<td width=100%><center><CENTER><h3>Welcome <?php echo $sql->data['Name']; ?></h3>
			    <h2>with  <b><?php echo $sql->data['Company'] ?></b></h2></CENTER></td></tr>
	</table>
</td>
</tr>
<TR><TD VALIGN=top>
<A HREF=http://www.bizcardstoday.com><IMG SRC=images/home.gif BORDER="0"></A>
<BR><A HREF=mailto:bizinfo@bizcardstoday.com?Subject=Request%20For%20Information%20About%20BizCardsToday.com>
   <IMG SRC=images/contact.gif BORDER="0"></A>
<BR><A HREF=welcome.php><IMG SRC=images/mm.gif BORDER="0"></A></TD>
<TD><CENTER><TABLE><TR><TD>

<?php
switch($_REQUEST['req'])
{
   default:

   $now_click = date('is');
?>
<CENTER> <h2>File Uploader</h2>
<div id="uploadmes"><img src=Untitled-6.gif width=1 height=1></div>
<fieldset style="border: 1px ridge #000000; color: #ffffff; width: 400px;">
	<legend style="color: #260000; font-family: verdana; font-size: 11pt;">Card Defintions</legend>
<table><TR><TD>


<form id="upload_file" action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="524288000" />
<input type="hidden" name="req" value="upload">
<input type="hidden" name="now_click" value="<?php echo $now_click; ?>">
<fieldset style="border: 1px ridge #000000; color: #ffffff; background: #A7A7A7; width: 400px;">
	<legend style="color: #260000; font-family: verdana; font-size: 11pt;">Quanity</legend>
	<label>	<table><tr>
	<td><input type=radio name=Quantity value=250>250</td>
	<td><input type=radio name=Quantity value=500>500</td>
	<td><input type=radio name=Quantity value=1000 checked=yes>1000</td>
	<td><input type=radio name=Quantity value=2000>2000</td></tr>
	</table>
	</lable>
</fieldset>
<BR>
<fieldset style="border: 1px ridge #000000; color: #ffffff; background: #A7A7A7; width: 400px;">
	<legend style="color: #260000; font-family: verdana; font-size: 11pt;">Card Type</legend>
	<label>	<table><tr><td><B>Premium Color</td>
	<td><input type=radio name=pc value='Premium 4/0' checked=yes>4/0</td>
	<td><input type=radio name=pc value='Premium 4B'>4/B</td>
	<td><input type=radio name=pc value='Premium 4/4'>4/4</td></tr>
	</table>
	</lable>
	<label>	<table><tr><td><B>Regular Color</td>
	<td><input type=radio name=pc value='Regular 4/0'>4/0</td>
	<td><input type=radio name=pc value='Regular 4/1'>4/1</td></tr>
	</table>
	</lable>


</fieldset>

<BR>
<fieldset style="border: 1px ridge #000000; color: #ffffff; background: #A7A7A7; width: 400px;">
	<legend style="color: #260000; font-family: verdana; font-size: 11pt;">Traditional Cards</legend>
	<label>	<table><tr>
	<td><input type=radio name=trd value=bw checked=yes>Bright-White</td>
	<td><input type=radio name=trd value=laid>Laid</td>
	<td><input type=radio name=trd value=lin>Linen</td>
	<td><input type=radio name=trd value=fib>Fiber</td></tr>
	</table>
	</lable>
	<label>	<table><tr><td><B>Sided</td>
	<td><input type=radio name=side value=1 checked=yes>1</td>
	<td><input type=radio name=side value=2>2</td></tr>
	</table>
	</lable>
</fieldset>
</td></tr></table>
</fieldset>
<BR>


<fieldset style="border: 1px ridge #000000; color: #000000; background: #A7A7A7; width: 550px;">
	<legend style="color: #260000; font-family: verdana; font-size: 11pt;"><B>Shipping Information
	   </b></legend>
	<label><table><tr><td>Purchase Order Number
	<BR>
	<BR>Preferred Shipping Method: <input type=radio name=ship value='UPS Ground' checked=yes>
	    UPS Ground | <input type=radio name=ship value='o'>Next Day | 
	    <input type=radio name=ship value='2'>2 Day Air</label>
	<BR>Company: <input type=text name=company value='<?php echo $PDF['company'] ?>'>
	<BR>Name: <input type=text name=name value='<?php echo $PDF['name'] ?>'>
	<BR>Address 1: <input type=text name=address1 value='<?php echo $PDF['address1'] ?>'>
	<BR>Address 2: <input type=text name=address2 value='<?php echo $PDF['address2'] ?>'>
	<BR>City:<input type=text name=city value='<?php echo $PDF['city'] ?>'> | 
	    State:<input type=text name=state value='<?php echo $PDF['state'] ?>'> | 
	    Zip:<input type=text name=zip value='<?php echo $PDF['zip'] ?>'>
	<BR>Special Instructions
	<BR><textarea rows=3 cols=70></textarea><BR>
	<BR>PDF/AI File of Card <input type="file" name="image" tabindex="1" size="35" />
	</td></tr></table>
	</label>
</fieldset>
<BR>
<span id="btn"><input type="button" value="Upload PDF" onClick="disable(); submit();" tabindex="2">
   </span>
</form>
</div>
</td>
</tr>
</table>

<?php
   break;

   case "upload":
   # set constant
?>
<script type="text/javascript" language="javascript">
function meswrite(mes){
var obj = document.getElementById('uploadmes');
var messege = "<p><font color='#00FF00' size='2'>"+mes+"</p>";
obj.innerHTML = messege;
}

window.onLoad = meswrite('Uploading File. Please wait.');
</script>
<?php

   $gawd =date("nGi");
   $name = $template . "-" . $gawd .".pdf";

   define ("FILES","./images/PDF/");
   //$name = $_FILES['image']['name'];
   $size = $_FILES['image']['size'];
   /*Make sure the file was posted */
   if(is_uploaded_file($_FILES['image']['tmp_name']))
   {
	 /* move uploaded image to final destination. */
	 $result = move_uploaded_file($_FILES['image']['tmp_name'], FILES."/$name");
	 if($result == 1) {
	 ?>
	 <script type="text/javascript" language="javascript">
	 meswrite('File uploaded to server. An email is on its way to you to confirm your order');
	 </script>
	 <?php

		 $msg = $HTTP_POST_VARS['name'] . " has ordered " . $HTTP_POST_VARS['Quantity'] . 
			 " Business Cards through the PDF Uploader<br>\n-------------------------------------" . 
			 "----------------------------------------------------<br>\n";
		 $msg .= "Shipping Information:<br>\n";
		 $msg .= stripslashes($HTTP_POST_VARS['company']) . "<br>\n";
		 $msg .= $HTTP_POST_VARS['name'] . "<br>\n";
		 $msg .= $HTTP_POST_VARS['address1'] . "<br>\n";
		 if($HTTP_POST_VARS['address2']!="")
			 $msg .= $HTTP_POST_VARS['address2'] . "<br>\n";
		 $msg .= $HTTP_POST_VARS['city'] . ", " . $HTTP_POST_VARS['state'] . "  " . 
			 $HTTP_POST_VARS['zip'] . "<br>\n";
		 $msg .="Ship By: ";
		 if($HTTP_POST_VARS['ship']=="2")
			 $msg .= "2 Day Air<br>\n";
		 elseif($HTTP_POST_VARS['ship']=="o")
			 $msg .= "Next Day<br>\n";
		 else
			 $msg .= "UPS Ground<br>\n";
		 if($HTTP_POST_VARS['po']!="")
			 $msg .= "Purchase Order #: " . $HTTP_POST_VARS['po'] . "<br>\n";

		 $msg .= "This card will be printed on " . $PDF['Quality'];
		 $msg .="<BR>\n";
		 $msg .="Using " .$_FILES['image']['name'] . "<BR>\n";
		 if($_POST['Notes']!="")
			 $msg .= "<br>\nExtra Notes: " . $_POST['Notes'] . "<br>\n";

	 $pew =  date('is');
	 $now_click = date('is') - $_POST['now_click'];
	 echo "<CENTER>$msg <BR>$now_click Upload time <BR>";


		 if($log=="")
		 {
		 $msg .="Listed on server as " .$name . "<BR>\n";
			 $emailer=new email_html_wa($email, $HTTP_POST_VARS['Quantity'] . 
				 " PDF Upload Order - Complete", $email, $email);
			 $emailer->clean();
			 $emailer->setheaders();
			 $emailer->addmessage($msg);
			 $emailer->embed_image("/images/PDF/" . $name,  $name);
			 if(@fopen("http://www.bizcardstoday.com/TestBed/PDF/uploads/" . $name, 'r'))
			 {
				 $emailer->embed_image("/imagesPDF/" . $name, $name);
			 }

			 $emailer->sendmail("orders@bizcardstoday.com");
			 //$emailer->sendmail("webmaster@bizcardstoday.com");
			 $emailer->sendmail("admin@buskirkgraphics.com");
		 }


	 }else 
	 {
	    echo "<p>The file didn't upload correctly. Please contact us.</p>";
	 }
   }

   break;
}
?>
</td></tr></table>