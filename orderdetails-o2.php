<?php
//
//	Project: BizCardsToday.com															
//	Programmer: Jason Hosler															
//	Purpose: This page is the main page of the site.  This is where the user will fill out the card,
//		upload pictures, specify shipping options, etc.										
//
session_start(); //Start the session

include_once('inc/preConf.php');
include_once('firelogger/firelogger.php');

flog('sess', $_SESSION);
flog('post', $_POST);

// 	if($template==0 || !session_is_registered("template") || $template=="") 
//  boot to homepage if the card template is unspecified
if(!session_is_registered("template")) //Boot to homepage if card template is not set.
{
   header("Location: index2.php");
}else 
{
   $template = $_SESSION['template'];
   $user = $_SESSION['user'];
}

if(!session_is_registered("card")) //Register variables with session
{
   session_register("card");
   session_register("CurCardID");
   session_register("ShortFile");
}
require("util.php"); // db wrapper
$sql = new MySQL_class;
$sql->Create("bizcardstodaynew");


//Set $_POST to $card
foreach($_POST as $a=>$b)
{
   //if($b!="")
   $card[$a]=$b;
}

flog('card', $card);


if($card['Action']=='New' && $card['ID']=="")
{
   $statement = "SELECT max(ID) from Finished_Cards;";
   $sql->QueryItem($statement);
   if($card['ID']=="") // If an ID has not been assigned, grab the next one in line.
   {
	 $card['ID']=$sql->data[0]+1;
   }
}

if(($_POST['Line_1'] == "") && ($_POST['Card_Name'] == ""))
{
   $ben = mysql_query("SELECT * FROM Finished_Cards");
   $afrow = mysql_num_rows($ben);
   $_POST['Card_Name'] = "No Name $afrow";
}

//This section is commented out because it randomly throws false positives on the Dup check.
//$statement = "SELECT * FROM Finished_Cards WHERE Card_Name=\"" . $card['Card_Name'] . "\" AND Template=$template;";
//$sql->Query($statement);
//echo "<!--" . $sql->rows . " || " . $card['Action'] . "-->\n";
//if($sql->rows>0 && $card['Action']=="New")
//{
//	$alerter ="alert('Duplicate or Invalid Card Title.\\nPlease Enter a valid Card Title.');";
//}

//Verify file name reference and set Photo variables.
$card['Filename']="images/finished/" . $template ."_" . $card['ID'] . ".svg";
$CurCardID = "images/uploads/" . $template ."_" . $card['ID'] . ".jpg";
$ShortFile = $template . "_" . $card['ID'] . ".jpg";


//Load Template values
$statement = "SELECT * FROM Templates WHERE ID=" . $template;
$sql->QueryRow($statement);
$row=$sql->data;


$templatefile=$row['Template'];

$numlines=$row['lines'];
$twosided = $row['2_Sided'];
$per250 = $row['per250'];
$per500 = $row['per500'];
$per1000=$row['per1000'];
$per2000=$row['per2000'];
$per250_premium = $row['per250_premium'];
$per500_premium = $row['per500_premium'];
$per1000_premium=$row['per1000_premium'];
$per2000_premium=$row['per2000_premium'];
$Quickcard=$row['QuickCard_Price'];
$default_quantity=$row['default_value'];
$card_quality=$row['card_quality'];
if($row['Vertical']=='y') //Set display size variables
{
   $dimension="width=\"294\" height=\"498\"";
   //These variables are only used for the back side of a two sided card
   $width=294;
   $height=498;
}else
{
   $dimension="height=\"294\" width=\"498\"";
   //These variables are only used for the back side of a two sided card
   $width=498;
   $height=294;
}

$statement = "SELECT * FROM Templates WHERE ID=$template";
$sql->QueryRow($statement);
$row=$sql->data;

for($i=1; $i<=$numlines; $i++) //Setup skip array to Lockdown specified fields
{
   if($row['Line_' . $i . '_Lock']=='y')
	 $skip[$i]='y';
   else
	 $skip[$i]='n';
}

//Save the Card to the DB.
if($alerter=="")
{
   $statement="SELECT ID, Filename FROM Finished_Cards WHERE ID=" . $card['ID'] . 
	   " and Template='" . $template . "'";
   $sql->QueryRow($statement);


   $a = $sql->rows;
   flog('statement', $statement);
   flog('rows', $a);
   if($sql->rows>0)
   {
	 $card['ID']=$sql->data['ID'];
	 $statement="UPDATE Finished_Cards SET Template='" . $template . "'";
   }else
   {
	 $statement="INSERT INTO Finished_Cards SET Template='" . $template . "'";
   }
   //Finished file name
   $file="images/finished/" . $template . "-" . $card['ID'] . ".svg";

   for($i=1; $i<=$numlines; $i++)
   {
	 if($_POST['Line_' . $i]!="")
	    $statement .= ", Line_" . $i . "=\"" . 
		 stripslashes(str_replace("&#39;", "'", str_replace("\"", "&quot;", $_POST['Line_' . $i]))) 
		 . "\""; //HTTP_POST_VARS
	 else
	 {
	 $statement .= ", Line_" . $i . "=\"" . 
		 stripslashes(str_replace("&#39;", "'", str_replace("\"", "&quot;", $card['Line_' . $i])))
		 . "\"";
	 $HTTP_POST_VARS['Line_' . $i]=$card['Line_' . $i];
	 }
	 //echo "<!--$i: " . stripslashes(str_replace("&#39;", "'", str_replace("\"", "&quot;", 
	 //$HTTP_POST_VARS['Line_' . $i]))) . "-->\n";
   }
   //echo "<!--" . $statement . "-->\n";
   if($card['Action']=="New")
   {
	 if($_POST['Card_Name']!="")
	 {
	 $statement .=", Card_Name=\"" . str_replace("\"", "&quot;", $_POST['Card_Name']) . "\"";
	 $card['Card_Name']=$_POST['Card_Name'];
	 }else
	 {
	 $statement .=", Card_Name=\"" . str_replace("\"", "&quot;", $_POST['Line_1']) . "\"";
	 $card['Card_Name']=$_POST['Line_1'];
	 }
   }else
	 $statement .= ", Card_Name=\"" . str_replace("\"", "&quot;", $card['Card_Name']) . "\"";
   //$statement .=", Quantity='" . $HTTP_POST_VARS['Quantity'] . "', Quality='" . 
   //$HTTP_POST_VARS['card_quality'] . "', Notes=\"" . addslashes($HTTP_POST_VARS['Notes']) . "\", 
   //Filename=\"" . $file . "\", po='" . $HTTP_POST_VARS['po'] . "', company='" . 
   //$HTTP_POST_VARS['company'] . "', name='" . $HTTP_POST_VARS['name'] . "', address1='" . 
   //$HTTP_POST_VARS['address1'] . "', address2='" . $HTTP_POST_VARS['address2'] . "', city='" . 
   //$HTTP_POST_VARS['city'] . "', state='" . $HTTP_POST_VARS['state'] . "', zip='" . 
   //$HTTP_POST_VARS['zip'] . "', speed='" . $HTTP_POST_VARS['speed'] . "', ship='" . 
   //$HTTP_POST_VARS['ship'] . "', paper='" . $Paper . "', Ink='" . $Ink . "'";
   if($_POST['hold']=='no')
   {
	 if($target!="")
	    $statement .= ", Date_Approved=now(), Due_Date=now()+interval 14 day";
	 else
	    $statement .= ", Date_Stamp=now(), Due_Date=now()+interval 14 day";
   }else $statement .= ", Date_Stamp=now()";
   if($client_status=='p')
   {
	 $statement .= ", Status='P'";
   }else
   {
	 if($_POST['hold']=='no')
	    $statement .= ", Status='O'";
	 else
	    $statement .= ", Status='H'";
   }
   if(isset($_POST['symbol']))
   {
	 foreach($_POST['symbol'] as $a=>$b)
	 {
	    $symbol_line.=",$b";
	 }
	    $statement .= ", Symbols=\"$symbol_line,\"";
   }

   if($sql->rows>0)
   {
	 $statement .= " WHERE ID=" . $card['ID'] . " and Template='" . $template . "'";
	 $sql->Update($statement);
   }else
   {
	 $statement .= ", ID=" . $card['ID'];
	 $sql->Insert($statement);
   }
}
if(DEBUGSW) 
{
   echo("-$statement-<br>");
}

//if no photo is specified or the specified photo is not found, load the placeholder
if(!@fopen($CurCardID, 'r'))
{
   $CurCardID = "images/uploads/placeholder.jpg";
   $ShortFile="placeholder.jpg";
}

//Make the card
$file="images/finished/" . $template . "-" . $card['ID'] . ".svg";
$lines=file("images/template/" . $templatefile . ".svg");
$output = fopen($file, "w");

//Replace the asterisks with the bullet code for inclusion in the card
$j=1;
while($j<=$numlines)
{
   $card['Line_' . $j] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', 
	   htmlentities(str_replace("&quot;", "\"", $card['Line_' . $j]), ENT_NOQUOTES))));
   $j++;
}
/*
$card['Line_2'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_2']), ENT_NOQUOTES))));
$card['Line_3'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"",$card['Line_3']), ENT_NOQUOTES))));
$card['Line_4'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_4']), ENT_NOQUOTES))));
$card['Line_5'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_5']), ENT_NOQUOTES))));
$card['Line_6'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_6']), ENT_NOQUOTES))));
$card['Line_7'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_7']), ENT_NOQUOTES))));
$card['Line_8'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_8']), ENT_NOQUOTES))));
$card['Line_9'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_9']), ENT_NOQUOTES))));
$card['Line_10'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_10']), ENT_NOQUOTES))));
$card['Line_11'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_11']), ENT_NOQUOTES))));
$card['Line_12'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_12']), ENT_NOQUOTES))));
$card['Line_13'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_13']), ENT_NOQUOTES))));
$card['Line_14'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_14']), ENT_NOQUOTES))));
$card['Line_15'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_15']), ENT_NOQUOTES))));
$card['Line_16'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_16']), ENT_NOQUOTES))));
$card['Line_17'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_17']), ENT_NOQUOTES))));
$card['Line_18'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_18']), ENT_NOQUOTES))));
$card['Line_19'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_19']), ENT_NOQUOTES))));
$card['Line_20'] = str_replace('&reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_20']), ENT_NOQUOTES))));
*/
foreach($lines as $a=>$b)
{
   //Swap out ~~X~~ for the Line X value. This includes swapping out * for Bullet
   $j=1;
   while($j<=20)
   {
	 //echo "<!--" . $card['Line_' . $j] . "-->\n";
	 $b=str_replace("~~$j~~", $card['Line_' . $j], $b);
	 $j++;
   }


   //Swap out picture placeholders
   $b = str_replace('~~insert picture~~', $CurCardID, $b);
   $b = str_replace('~~insert shortref~~', $ShortFile, $b);
   fwrite($output, $b);
}
//Switch them back for display on the screen
$j=1;
while($j<=$numlines)
{
   $card['Line_' . $j] = str_replace('®', '&reg;', str_replace("&amp;", "&", 
	   stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_' . $j])))));
   $j++;
}
/*
$card['Line_1'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_1'])))));
$card['Line_2'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_2'])))));
$card['Line_3'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_3'])))));
$card['Line_4'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_4'])))));
$card['Line_5'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_5'])))));
$card['Line_6'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_6'])))));
$card['Line_7'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_7'])))));
$card['Line_8'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_8'])))));
$card['Line_9'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_9'])))));
$card['Line_10'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_10'])))));
$card['Line_11'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_11'])))));
$card['Line_12'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_12'])))));
$card['Line_13'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_13'])))));
$card['Line_14'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_14'])))));
$card['Line_15'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_15'])))));
$card['Line_16'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_16'])))));
$card['Line_17'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_17'])))));
$card['Line_18'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_18'])))));
$card['Line_19'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_19'])))));
$card['Line_20'] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_20'])))));
*/
?>
<html>
<head>
<title>BizCardsToday.com Design & Proofing page</title>
<link rel="stylesheet" href="bizcard.css" type="text/css">
<script language=javascript>


function OnButton9()
{	//Place Order
   document.Form1.action = "place_order.php";
   document.Form1.submit();
   return true;
}


function OnButton5()
{	//Cancel Order
   document.Form1.action = "welcome.php"
   document.Form1.submit();
   return true;
}


function disable()
{
   var obj = document.getElementById('uploadmes');
   var messege = "<BR><BR><BR><BR><BR><BR><center><p><font color='#000000' size='2'>\n\
	 Processing Order. Please be patient, this process can take around 2 minutes </font></p><BR>\n\
	 <img src=Untitled-6.gif></center><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>\n\
	 <BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>\n\
	 <BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>";
   obj.innerHTML = messege;
   var button = document.getElementById('btn');
   var dis = '<input disabled type="button" value="Place Order" onClick="disable();" tabindex="2">';
   button.innerHTML = dis;
}

function update()
{
   var obj = document.getElementById('uploadmes');
   var messege = "<p><font color='#00FF00' size='2'>Processing Order. Please wait.</font></p>";
   obj.innerHTML = messege;
}

</script>
</head>

<body bgcolor=#ffffff leftmargin=5px>
<div id="uploadmes"><img src=Untitled-6.gif width=1 height=1></div>
<table border=0 cellspacing=5 cellpadding=5>
<tr>
   <td>
   <table border=0 cellspacing=0 cellpadding=0><tr><td><b>Card Proof:</b><br>
   <embed src="<?php echo $file ?>"  <?php echo $dimension; ?> type="image/svg+xml" 
		pluginspage="http://www.adobe.com/svg/viewer/install/auto/"></td></tr></table>
   <?php	//Pop Back Side button if needed
   if($twosided=='y')
   {
	 echo "This is a two sided card. Please click the \"Back Side\" button to see what the back of 
	    your card looks like. 
	    <input type=button 
	    onclick=\"window.open ('backview.php?width=$width&height=$height', 'view', 
		  'width=$width,height=$height');\" value='Back Side'>";
   }
   $statement="SELECT Name, Company FROM Users WHERE ID=$user";
   $sql->QueryRow($statement);
   ?>
   </td>
<?php
if($card_quality=='e')
{
   echo "<td align=center valign=center>\n";
   echo "\t<font size=+3>BizCards now come in 2 flavors <br> <b>Standard &amp; Premium</font><br>
	 <input type=button value=\"More Info\" onclick=\"window.open ('picinfo.php?pic_width=" . 
	   $card['Pic_Width'] . "&pic_height=" . $card['Pic_Height'] . "&msg=2', 'view', 'width=" . 
	   $width . ",height=" . $height . "');\"></b>\n";
}
?>
</td>
</tr>
</table>
<input type=hidden value='<?php echo $template ?>' name='template>
<table border=0 cellspacing=5 cellpadding=5><!-- width=600-->
	
<form action="place_order.php" method='POST' name='Form1' ENCTYPE="multipart/form-data">
<input type='hidden' name='Card_Name' value='<?php echo($card['Card_Name']); ?>'>
<input type='hidden' name='Line_1' value='<?php echo($_POST['Line_1']); ?>'>
<input type=hidden name=Action value="<?php echo $_REQUEST['Action'] ?>">
<input type=hidden name=hold value='no'>
<input type=hidden name=redraw value='n'>

<tr>
	<td colspan=2 valign=top bgcolor=#D1D1D1>
		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td colspan=4 valign=top><b>How Many Cards</b> Would You Like To Order</td></tr>
		<tr>

<?php
if($card_quality=="s")
{
   echo "<input type=hidden value='s' name=card_quality>\n";

   if($per250>0) //If a price has been defined for 250, then allow them to order 250
   {
	 echo "<td><input type=radio value=250 name=Quantity ";
	 if($card['Quantity']=='250' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='250'))
		 echo "checked";
	 echo ">250</td>\n";
   }
   if($per500>0) //If a price has been defined for 500, then allow them to order 500
   {
	 echo "<td><input type=radio value=500 name=Quantity ";
	 if($card['Quantity']=='500' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='500'))
		 echo "checked";
	 echo ">500</td>\n";
   }
   if($per1000>0) //If a price has been defined for 1000, then allow them to order 1000
   {
	 echo "<td><input type=radio value=1000 name=Quantity ";
	 if($card['Quantity']=='1000' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='1000'))
		 echo "checked";
	 echo ">1000</td>\n";
   }
   if($per2000>0) //If a price has been defined for 2000, then allow them to order 2000
   {
	 echo "<td><input type=radio value=2000 name=Quantity ";
	 if($card['Quantity']=='2000' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='2000'))
		 echo "checked";
	 echo ">2000</td>\n";
   }
}else if($card_quality=="p")
{
   echo "<!--" . $card['Quantity'] . "-->\n";
   echo "<input type=hidden value='p' name=card_quality>\n";
   if($per250_premium>0) //If a price has been defined for 250, then allow them to order 250
   {
	 echo "<td><input type=radio value=250 name=Quantity ";
	 if($card['Quantity']=='250' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='250'))
		 echo "checked";
	 echo ">250</td>\n";
   }
   if($per500_premium>0) //If a price has been defined for 500, then allow them to order 500
   {
	 echo "<td><input type=radio value=500 name=Quantity ";
	 if($card['Quantity']=='500' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='500'))
		 echo "checked";
	 echo ">500</td>\n";
   }
   if($per1000_premium>0) //If a price has been defined for 1000, then allow them to order 1000
   {
	 echo "<td><input type=radio value=1000 name=Quantity ";
	 if($card['Quantity']=='1000' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='1000'))
		 echo "checked";
	 echo ">1000</td>\n";
   }
   if($per2000_premium>0) //If a price has been defined for 2000, then allow them to order 2000
   {
	 echo "<td><input type=radio value=2000 name=Quantity ";
	 if($card['Quantity']=='2000' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='2000'))
		 echo "checked";
	 echo ">2000</td>\n";
   }
}else
{//If a price has been defined for 250, then allow them to order 250
   if($per250_premium>0 || $per250>0) 
   {
	 echo "<td><input type=radio value=250 name=Quantity ";
	 if($card['Quantity']=='250' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='250'))
		 echo "checked";
	 if($per250_premium>0 && $per250>0)
		 echo ">250</td>\n";
	 else
	 {
		 if($per250_premium>0)
			 echo ">250 (Premium only)</td>\n";
		 else
			 echo ">250 (Standard only)</td>\n";
	 }
   }
   if($per500_premium>0) //If a price has been defined for 500, then allow them to order 500
   {
	 echo "<td><input type=radio value=500 name=Quantity ";
	 if($card['Quantity']=='500' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='500'))
		 echo "checked";
	 if($per500_premium>0 && $per500>0)
		 echo ">500</td>\n";
	 else
	 {
		 if($per500_premium>0)
			 echo ">500 (Premium only)</td>\n";
		 else
			 echo ">500 (Standard only)</td>\n";
	 }
   }
   if($per1000_premium>0) //If a price has been defined for 1000, then allow them to order 1000
   {
	 echo "<td><input type=radio value=1000 name=Quantity ";
	 if($card['Quantity']=='1000' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='1000'))
		 echo "checked";
	 if($per1000_premium>0 && $per1000>0)
		 echo ">1000</td>\n";
	 else
	 {
		 if($per1000_premium>0)
			 echo ">1000 (Premium only)</td>\n";
		 else
			 echo ">1000 (Standard only)</td>\n";
	 }
   }
   if($per2000_premium>0) //If a price has been defined for 2000, then allow them to order 2000
   {
	 echo "<td><input type=radio value=2000 name=Quantity ";
	 if($card['Quantity']=='2000' || (($card['Quantity']=="0" || $card['Quantity']=="") && 
		 $default_quantity=='2000'))
		 echo "checked";
	 if($per2000_premium>0 && $per2000>0)
		 echo ">2000</td>\n";
	 else
	 {
		 if($per2000_premium>0)
			 echo ">2000 (Premium only)</td>\n";
		 else
			 echo ">2000 (Standard only)</td>\n";
	 }
   }
}
if($card_quality=='e')
{
    echo "\t\t\t\t\t</tr><tr>\n\t\t\t\t\t\t<td colspan=4><span id=Blinker>Card Quality </span> " . 
		 "(<a href=# onclick=\"window.open ('picinfo.php?pic_width=" . $card['Pic_Width'] . 
		 "&pic_height=" . $card['Pic_Height'] . "&msg=2', 'view', 'width=$width,height=$height');\">" . 
		 "What's This?</a>)&nbsp;&nbsp;&nbsp;<input type=radio name=card_quality value='s' ";
    if($card['Quality']=='s' || $card['Quality']=="")
	    echo "checked";
    echo "> Regular <input type=radio value='p' name=card_quality ";
    if($card['Quality']=='p')
	    echo "checked";
    echo "> Premium</td>\n\t\t\t\t\t</tr>";
}
?>
<!--
<td><input type=radio value=500 name=Quantity <?php //if($card['Quantity']=='500') echo "checked"; ?>>500</td>
<td><input type=radio value=1000 name=Quantity <?php //if($card['Quantity']=='1000' || $card['Quantity']=="" ) echo "checked"; ?>>1000</td>
<td><input type=radio value=2000 name=Quantity <?php //if($card['Quantity']=='2000') echo "checked"; ?>>2000</td>
-->
		</tr>
		</table>
	</td>
</tr>
<tr>
<td colspan=2 bgcolor=#D1D1D1>In a mad rush? 
	<input type=checkbox name=speed value='y'> Yes, I want my cards ready within 24 hours 
	(Rush Charges Apply)<br>How would you like your order shipped: 
	<input type=radio name="ship" value='s' 
		<?php if($card['ship']=='s' || $card['ship']=='') echo "checked"; ?>> UPS Ground 
	<input type=radio name="ship" value='o' 
		<?php if($card['ship']=='o') echo "checked"; ?>> Next Day 
	<input type=radio name="ship" value='2' 
		<?php if($card['ship']=='2') echo "checked"; ?>> 2 Day Air </td>
</tr>
<tr>
<?php

$gris = mysql_query("Select * from Company where ID='$row[Company]'");
$hapsd = mysql_fetch_array($gris);

if($card['company'] == ""){ $card['company'] = "$hapsd[Name]";}
if($card['address1'] == ""){$card['address1'] = "$hapsd[Address1]";}
if($card['address2'] == ""){$card['address2'] = "$hapsd[Address2]";}
if($card['city'] == ""){$card['city'] = "$hapsd[City]";}
if($card['state'] == ""){$card['state'] = "$hapsd[State]";}
if($card['zip'] == ""){$card['zip'] = "$hapsd[Zip]";}
?>

<td colspan=2 bgcolor=#D1D1D1>
	<table border=0 cellspacing=2 cellpadding=0>
	<tr>
		<td colspan=2><b>Purchase Order Number</b>:</td>
	</tr>
	<tr>
		<td colspan=2><input type=text size=35 name=po value="<?php echo $card['po'] ?>"></td>
	</tr>
	<tr>
		<td colspan=2><br>Shipping Information - Your order will be shipped to the address below.<br>
			Make any changes needed and click on <b>Place Order</b></td>
	</tr>
	<tr>
		<td>Company:</td>
		<td><input type=text name=company value="<?php echo $card['company']; ?>" size=35></td>
	</tr>
	<tr>
		<td>Name:</td>
		<td><input type=text name=name value="<?php echo $card['name']; ?>" size=35></td>
	</tr>
	<tr>
		<td>Address 1:</td>
		<td><input type=text name=address1 value="<?php echo $card['address1']; ?>" size=35></td>
	</tr>
	<tr>
		<td>Address 2:</td>
		<td><input type=text name=address2 value="<?php echo $card['address2']; ?>" size=35></td>
	</tr>
	<tr>
		<td colspan=2>City: <input type=text name=city value="<?php echo $card['city']; ?>" size=20> 
			State: <input type=text name=state value='<?php echo $card['state'] ?>' size=3 maxlength=2> 
			Zip:<input type=text name=zip value='<?php echo $card['zip'] ?>' size=10 maxlength=10></td>
	</tr>
	<tr>
		<td colspan=2>Special Instructions:<br><textarea name=Notes rows=3 cols=75></textarea></td>
	</tr>
	</table>
	</td>
</tr>
<?php
if($target!="") 	//If this is an Approval of an order
{
	echo "<td>
		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td><input type=submit value='Approve Order' name=button onclick='return OnButton2();'></td>
			<td><input type=submit value='Return to Menu' name=button onclick='return OnButton5();'></td>
		</tr>
		</table>
	</td>\n";
}else
{		//Regular Order placement
	echo "<td>
		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td colspan=3><b>Place Order</b> will save the card and submit an order for printing</td></tr>
		<tr>
			<td>";
?>
<form id="upload_file" action="place_order.php" METHOD="post">
<span id="btn"><input type="button" value="Place Order" onClick="disable(); submit();" tabindex="2"></span>
<input type="hidden" name="fileName" value="<?php echo($file); ?>">
</form>
<?php
echo "</td><td><input type=submit value='Return to Menu' name=button onclick='return OnButton5();'></td></tr></table></td>\n";
}
?>
</tr>
</form>
</table>


<?php 
$_SESSION['card'] = $card;
if(DEBUGSW) 
{
echo('<pre>');
print_r($_SESSION);
print_r($_POST);
// print_r($_GET);
echo('</pre>');
}
?>	
</body>
</html>
