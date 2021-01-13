<?php

// ini_set("register_globals", "1");

include_once('inc/preConf.php');
if(DEBUGSW) include_once('inc/debug.inc.php');

/**************************************************************************************************/
/*	Project: BizCardsToday.com															*/
/*	Programmer: Jason Hosler															*/
/*	Purpose: This page is the main page of the site.  This is where the user will fill out the card*/
/*		upload pictures, specify shipping options, etc.										*/
/**************************************************************************************************/
session_start(); //Start the session


if(DEBUGSW) 
{
	debug_msg("page is editpad.php-1");
	debug_var("POST",$_POST);
	debug_var("GET",$_GET);
	debug_var("REQUEST", $_REQUEST);
	debug_var("SESSION",$_SESSION);
}

if(isset($_SESSION['template']))
	$template = $_SESSION['template'];
else if(isset($_POST['template']))
	$template = $_POST['template'];
else if(isset($_GET['template']))
	$template = $_GET['template'];
else $template = '';

if(!isset($_POST['redraw'])) $_POST['redraw'] = '';


//  boot to homepage if the card template is unspecified
// if($template == 0 || !session_is_registered("template") || $template == "") 
if($template == 0 || !isset($_SESSION["template"]) || $template == "") 
	header("Location: index.php");

// if(!session_is_registered("card")) //Register variables with session
if(!isset($_SESSION["card"])) //Register variables with session
{
	// session_register("card");
	// session_register("CurCardID");
	// session_register("ShortFile");

	$_SESSION['card'] = '';
	$_SESSION['CurCardID'] = '';
	$_SESSION['ShortFile'] = '';
}

if(isset($_SESSION['card']))
	$card = $_SESSION['card'];
else if(isset($_POST['card']))
	$card = $_POST['card'];
else if(isset($_GET['card']))
	$card = $_GET['card'];
else $card = '';

if(isset($_SESSION['Action']))
	$action = $_SESSION['Action'];
else if(isset($_POST['Action']))
	$action = $_POST['Action'];
else if(isset($_GET['Action']))
	$action = $_GET['Action'];
else if(isset($_REQUEST['Action']))
	$action = $_REQUEST['Action'];
else $action = '';

if(isset($_SESSION['reorder']))
	$reorder = $_SESSION['reorder'];
else if(isset($_POST['reorder']))
	$reorder = $_POST['reorder'];
else if(isset($_GET['reorder']))
	$reorder = $_GET['reorder'];
else if(isset($_REQUEST['reorder']))
	$reorder = $_REQUEST['reorder'];
else $reorder = '';

if(DEBUGSW) 
{
	if(isset($card['Action']) AND $card['Action'] == 'New') 
	{
		$atest = 1;
		debug_var("card action", $card['Action']);
	
	}
	else $atest = 0;
	debug_var("atest", $atest);
	debug_var("Card-array", $card);
	
	if(isset($card['ID'])) debug_var("card-id", $card['ID']);
}

require("util.php"); // db wrapper
$sql = new MySQL_class;
$sql->Create("bizcardstodaynew");

if(DEBUGSW) 
{
	debug_msg("page is editpad.php-1");
	debug_var("POST",$_POST);
	debug_var("GET",$_GET);
	debug_var("SESSION",$_SESSION);
}

if(isset($card['LastAction']) AND $card['LastAction'] != "")
	$sql->Insert($card['LastAction']);
if(isset($card['LastAction']))
	$cardLastAction = $card['LastAction'];
else
	$cardLastAction = '';
echo "<!--LastAction = " . $cardLastAction . "-->\n";


//Brand new card.  Make new ID and fill default with last ordered card.
if($action == 'New' || $card['Action'] == 'New') 
{
if(DEBUGSW) debug_msg("-new- path taken editpad.php");	
	
	$statement = "SELECT max(ID) from Finished_Cards";
	$sql->QueryItem($statement);
	
	// If an ID has not been assigned, grab the next one in line.

	if(!isset($card['ID']) OR $card['ID'] == "") 
	{
		$card['ID'] = $sql->data[0]+1;
		$maxer = $card['ID'];
	}else
		$maxer = $card['ID'];

if(DEBUGSW) debug_var("maxer",$maxer);
	
	$card['Action'] = "New";
	if(isset($card['ID']))
		echo "<!--" . $card['ID'] . "||" . $_POST['redraw'] . "-->\n";
		
	if($_POST['redraw']=='y') // Redraw/Refresh.  Update $card array.
	{
		$_POST['redraw']='n';
		if(DEBUGSW) debug_var("POST2",$_POST);
		foreach($_POST as $a=>$b)
		{
			if($a != 'Filename')
			{
				$card[$a]=$b;
			}
		}
		if($_POST['Card_Name'] != "")//Verify Card Name is unique to Template
		{
			$statement = "SELECT ID FROM Finished_Cards WHERE Template=$template AND Card_Name=\"" . $_POST['Card_Name'] . "\";";
			$sql->Query($statement);
			if($sql->rows > 0)
			{
				$alerter = "alert(\"Duplicate Card Title. Please select a new title.\");";
				$card['Card_Name'] = $_POST['Card_Name'] . "$card[ID]";
			}
		}else
		{
			$_POST['Line_1'] = trim($_POST['Line_1']);

			if($_POST['Line_1']=="")
			{
			$alerter = "alert(\"If you want to leave the first line blank you have re title the card to a new name\");";
			$card['Card_Name']="No Name $card[ID]";
			}else
			{

				$statement = "SELECT ID FROM Finished_Cards WHERE Template=$template AND Card_Name=\"" . $_POST['Line_1'] . "\";";
				$sql->Query($statement);
					if($sql->rows>0)
					{
						$alerter = "alert(\"Duplicate Card Title. Please select a new title.\");";
						$card['Card_Name']=$_POST['Line_1'] . "$card[ID]";
					}else
					{
					$card['Card_Name']=$_POST['Line_1'];
					}
			}
		}
		$card['ID'] = $maxer;
	}elseif(isset($card['upload']) AND $card['upload'] == true) 
	{
		//Just came from upload page.  No need to reload everything as it was just done on the upload.
		$card['upload']=false;
	}elseif($_POST['redraw'] != 'y') // This is not a redraw/refresh action.  Load default values.
	{
		$statement = "SELECT ID, sum(";
		$j=1;
		while($j<20)
		{
			$statement .= "if(line_$j<>'',1,0) + ";
			$j++;
		}
		$statement .= "if(line_20<>'',1,0)";
		$statement .= ") as Num_Fields FROM Finished_Cards WHERE Template=$template GROUP BY ID ORDER BY Num_Fields DESC, Date_Stamp DESC";
		
		if(DEBUGSW) debug_var("Statement", $statement);
		
		$sql->QueryRow($statement);
		if($sql->data[0] == "")
		{
			$statement = "SELECT * FROM Templates WHERE ID=$template";
		}else
			$statement = "SELECT * FROM Finished_Cards WHERE Template=$template AND ID=" . $sql->data[0] . " ORDER BY Date_Stamp DESC";
		$sql->QueryRow($statement);
		foreach($sql->data as $a=>$b)
		{
			if($a!='Filename' && $a!='Card_Name' && $a!='ID' && $a!='po')
				$card[$a] = $b;
		}
		$card['ID'] = $maxer;
		if(DEBUGSW) debug_var("CardArray2", $card);
	}
}else
{ 
	//Reorder an existing card
	$card['Action']="Reorder";
	if(isset($card['upload']))
		$cdUpload = $card['upload'];
	else
		$cdUpload = '';
		
	echo "<!--Reorder: " . $reorder . " || Redraw: " . $_POST['redraw'] . " || Upload: " . $cdUpload . "-->\n";
	if($_POST['redraw']=="y") // Refresh/Redraw action.
	{
		$_POST['redraw']='n';
		if($card['ID']==$_REQUEST['reorder']) //This is just a redraw
		{
			echo "<!--" . $card['ID'] . "-->\n";
			foreach($_POST as $a=>$b) //Put potentially new values from form into card array
			{
				$card[$a]=$b;
			}
		}else //This is changing the current card to another card.
		{
			echo "<!--Reorder section-->\n";
			$statement = "SELECT * FROM Finished_Cards WHERE ID=" . $_REQUEST['reorder'];
			$sql->QueryRow($statement);
			foreach($sql->data as $a=>$b)
			{
				$card[$a]=$b;
			}
			$card['po']=""; //Clear any existing Purchase Order number.
		}
	}elseif($reorder != "" && $reorder != "0") //Specific card requested through Reorder dropdown
	{ //This should be impossible to reach since autosubmitions of redraw form is off
		echo "<!--Reorder section-->\n";
		$statement = "SELECT * FROM Finished_Cards WHERE ID=" . $_REQUEST['reorder'];
		$sql->QueryRow($statement);
		foreach($sql->data as $a=>$b)
		{
			$card[$a]=$b;
		}
		$card['po']="";
		$card['ID']=$_REQUEST['reorder'];
	}elseif(isset($card['upload']) AND $card['upload']==true) //Just came from upload page.  No need to reload everything as it was just done on the upload.
	{
		$card['upload']=false;
	}else // Reorder initial entry. Load most recent card with most number of lines
	{
		$statement = "SELECT ID, sum(";
		$j=1;
		while($j<20)
		{
			$statement .= "if(line_$j<>'',1,0) + ";
			$j++;
		}
		$statement .= "if(line_20<>'',1,0)";
		$statement .= ") as Num_Fields FROM Finished_Cards WHERE Template=$template GROUP BY ID ORDER BY Num_Fields DESC, Date_Stamp DESC";
		$sql->QueryRow($statement);
		echo "<!--" . $statement . "-->\n";
		$statement = "SELECT * FROM Finished_Cards WHERE Template=$template AND ID=" . $sql->data[0] . " ORDER BY Date_Stamp DESC";
		$sql->QueryRow($statement);
		echo "<!--" . $statement . "-->\n";
		foreach($sql->data as $a=>$b)
		{
			if($a!='po')
				$card[$a]=$b;
		}
	}
}

//Verify file name reference and set Photo variables.
$card['Filename'] = "images/finished/" . $template ."_" . $card['ID'] . ".svg";
$CurCardID = "images/uploads/" . $template ."_" . $card['ID'] . ".jpg";
$ShortFile = $template . "_" . $card['ID'] . ".jpg";
if(DEBUGSW) debug_var("CardArray3", $card);
// exit("-" . $card['Filename'] . "-" . $CurCardID . "-" . $ShortFile . "-");

//if no photo is specified or the specified photo is not found, load the placeholder
if(!@fopen($CurCardID, 'r'))
{
	$CurCardID = "images/uploads/placeholder.jpg";
	$ShortFile="placeholder.jpg";
}

//Load Template values
$statement = "SELECT * FROM Templates WHERE ID=" . $template;
$sql->QueryRow($statement);
$row=$sql->data;

$templatefile=$row['Template'];
$pic_width=$row['Pic_Width'];
$pic_height=$row['Pic_Height'];
$numlines=$row['lines'];
$twosided = $row['2_Sided'];
for($i=1; $i<=$numlines; $i++) //Setup skip array to Lockdown specified fields
{
	if($row['Line_' . $i . '_Lock']=='y')
		$skip[$i]='y';
	else
		$skip[$i]='n';
}

if($row['Vertical']=='y') //Set display size variables
{
	$dimension="width=\"294\" height=\"498\"";
	//These variables are only used for the back side of a two sided card
	$width=294;
	$height=498;
}else{
	$dimension="height=\"294\" width=\"498\"";
	//These variables are only used for the back side of a two sided card
	$width=498;
	$height=294;
}



//Make the card
$file="images/temp/" . $template . "-" . $card['ID'] . ".svg";
$lines=file("images/template/" . $templatefile . ".svg");
$output = fopen($file, "w");


$statement = "SELECT * FROM Card_Symbols WHERE Template_ID=$template";
$sql->Query($statement);
$j=0;
while($j<$sql->rows)
{
	$sql->Fetch($j);
	$usable_symbols[]=$sql->data['Functional_Name'];
	$j++;
}

//Replace the asterisks with the bullet code for inclusion in the card, also replace double-quotes with &quot and the registered mark with Æ code
$j=1;
while($j<=$numlines)
{
	$card['Line_' . $j] = preg_replace('/&(amp;| *)reg;/', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_' . $j]), ENT_NOQUOTES))));
	$j++;
}
/*
$card['Line_1'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_1']), ENT_NOQUOTES))));
$card['Line_2'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_2']), ENT_NOQUOTES))));
$card['Line_3'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"",$card['Line_3']), ENT_NOQUOTES))));
$card['Line_4'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_4']), ENT_NOQUOTES))));
$card['Line_5'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_5']), ENT_NOQUOTES))));
$card['Line_6'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_6']), ENT_NOQUOTES))));
$card['Line_7'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_7']), ENT_NOQUOTES))));
$card['Line_8'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_8']), ENT_NOQUOTES))));
$card['Line_9'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_9']), ENT_NOQUOTES))));
$card['Line_10'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_10']), ENT_NOQUOTES))));
$card['Line_11'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_11']), ENT_NOQUOTES))));
$card['Line_12'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_12']), ENT_NOQUOTES))));
$card['Line_13'] = ereg_replace('&(amp;| *)reg;', '¬Æ', stripslashes(str_replace('*', '¬∑', htmlentities(str_replace("&quot;", "\"", $card['Line_13']), ENT_NOQUOTES))));
*/
foreach($lines as $a=>$b)
{
	//Swap out ~~X~~ for the Line X value. This includes swapping out * for Bullet
	$j=1;
	while($j<=20)
	{
		$b=str_replace("~~$j~~", $card['Line_' . $j], $b);
		$j++;
	}
	//$b = str_replace('~~1~~', $card['Line_1'], str_replace('~~2~~', $card['Line_2'], str_replace('~~3~~', $card['Line_3'], str_replace('~~4~~', $card['Line_4'], str_replace('~~5~~', $card['Line_5'], str_replace('~~6~~', $card['Line_6'], str_replace('~~7~~', $card['Line_7'], str_replace('~~8~~', $card['Line_8'], str_replace('~~9~~', $card['Line_9'], str_replace('~~10~~', $card['Line_10'], str_replace('~~11~~', $card['Line_11'], str_replace('~~12~~', $card['Line_12'], str_replace('~~13~~', $card['Line_13'], $b)))))))))))));

	//Swap out picture placeholders
	$b = str_replace('~~insert picture~~', $CurCardID, $b);
	$b = str_replace('~~insert shortref~~', $ShortFile, $b);
	fwrite($output, $b);
}
//Switch them back for display on the screen, also includes switch for HTML form submit autoflippling the ampersand.
$j=1;
while($j<=$numlines)
{
	$card['Line_' . $j] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_' . $j])))));
	$j++;
}
/*
$card['Line_1'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_1'])))));
$card['Line_2'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_2'])))));
$card['Line_3'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_3'])))));
$card['Line_4'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_4'])))));
$card['Line_5'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_5'])))));
$card['Line_6'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_6'])))));
$card['Line_7'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_7'])))));
$card['Line_8'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_8'])))));
$card['Line_9'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_9'])))));
$card['Line_10'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_10'])))));
$card['Line_11'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_11'])))));
$card['Line_12'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_12'])))));
$card['Line_13'] = str_replace('¬Æ', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('¬∑', '*', str_replace("\"", "&quot;", $card['Line_13'])))));
*/

$card['LastAction']="";

if(DEBUGSW) 
{
debug_msg("page is editpad.php-2");
debug_var("POST",$_POST);
debug_var("GET",$_GET);
debug_var("SESSION",$_SESSION);
}

?>

<html>
<head>
<title>BizCardsToday.com Design & Proofing page</title>
<link rel="stylesheet" href="bizcard.css" type="text/css">
<script language=javascript>
<?php  //This only pops if a Duplicate Card is found.
	if(isset($alerter) AND $alerter != "")
	{
	echo $alerter;
	$alerter="";
	}
?>
function OnButton1()
{	//Redraw Card
	document.Form1.action = "editpad.php";
	document.Form1.elements['redraw'].value='y';
	<?php
		if($card['Action']=='New')
		{
	?>
	if(document.Form1.elements['Card_Name'].value=="")
		document.Form1.elements['Card_Name'].value=document.Form1.elements['Line_1'].value;
	<?php
		}
	?>
	document.Form1.submit();
	return true;
}

function OnButton2()
{	//Place Order
	document.Form1.action = "orderdetails.php";
	<?php
		if($card['Action']=='New')
		{
	?>
	if(document.Form1.elements['Card_Name'].value=="")
		document.Form1.elements['Card_Name'].value=document.Form1.elements['Line_1'].value;
	<?php
		}
	?>
	document.Form1.submit();
	return true;
}

function OnButton3()
{	//Upload photo
	document.Form1.action="upload.php"
	document.Form1.enctype="multipart/form-data"
	<?php
		if($card['Action']=='New')
		{
	?>
	if(document.Form1.elements['Card_Name'].value=="")
		document.Form1.elements['Card_Name'].value=document.Form1.elements['Line_1'].value;
	<?php
		}
	?>
	document.Form1.submit();
	return true;
}

function OnButton4()
{	//Hold Order
	document.Form1.action = "saveexit.php"
	document.Form1.elements['hold'].value = 'yes';
	<?php
		if($card['Action']=='New')
		{
	?>
	if(document.Form1.elements['Card_Name'].value=="")
		document.Form1.elements['Card_Name'].value=document.Form1.elements['Line_1'].value;
	<?php
		}
	?>
	document.Form1.submit();
	return true;
}

function OnButton5()
{	//Cancel Order
	document.Form1.action = "welcome.php"
	document.Form1.submit();
	return true;
}

function disableForm(theform)
{
	for (i = 0; i < theform.length; i++) {
		var tempobj = theform.elements[i];
		if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset")
		tempobj.disabled = true;
	}
}
</script>
</head>

<body bgcolor=#ffffff leftmargin=5px>
<table border=0 cellspacing=5 cellpadding=5>
<tr>
	<td>
		<table border=0 cellspacing=0 cellpadding=0><tr><td><embed src="<?php echo $file ?>"  <?php echo $dimension; ?> type="image/svg+xml" pluginspage="http://www.adobe.com/svg/viewer/install/auto/"></td></tr></table>
		<?php
			//Pop Back Side button if needed
			if($twosided=='y')
			{
				echo "This is a two sided card. Please click the \"Back Side\" button to see what the back of your card looks like. <input type=button onclick=\"window.open ('backview.php?width=$width&height=$height', 'view', 'width=$width,height=$height');\" value='Back Side'>";
			}
			$statement="SELECT Name, Company FROM Users WHERE ID=$user";
			$sql->QueryRow($statement);
		?>
	</td>
	<td>Welcome <?php echo $sql->data['Name']; ?><br><br>This is the design approved for your company business cards loaded with the information from the most complete card in our database. If the company design has changed, or if your department uses a variation not shown here, please contact us immediately.<br><br>Please check spelling, grammar, and punctuation carefully when reviewing your online proofs. We are not responsible for mistakes that you approve.<br><br>If your card does not appear, please <a href="CardViewer.exe" target="_blank">click here</a>.  You will be prompted to Open or Save the file, select Open and then after you&rsquo;ve been told installation was successful, click the Redraw Card button.</td>
</tr>
</table>
<input type=hidden value=<?php echo $template ?> name=template>
<table border=0 cellspacing=5 cellpadding=5><!-- width=600-->

	<form action="editpad.php" method=POST name=Form1 ENCTYPE="multipart/form-data" onsubmit="return disableForm(this);">
	<input type=hidden name=Action value="<?php echo $_REQUEST['Action'] ?>">
	<input type=hidden name=ID value="<?php echo $card['ID'] ?>">
	<input type=hidden name=hold value='no'>
	<input type=hidden name=redraw value='n'>
	<tr>
		<td colspan=2><b><?php if($card['Action']=="New"){ echo "To Start A New Card"; }else{ echo "To Make Changes";} ?></b> simply replace the text in the boxes below and hit <b>Redraw Card</b>,<br> or select a name from the dropdown box and hit <b>Redraw Card</b> to switch to another card. <br>Some lines may be locked to prevent changes to fixed information such as company address. <br>Use an asterisk (*) to insert a bullet (&#149;)</td>
	</tr>
	<tr>
		<td >
			<table border=0 cellspacing=0 cellpadding=0>
			<?php
				if($card['Action']=="New")
				{
					for($i=1;$i<=$numlines;$i++)
					{
						//One entry for each available line
						echo "<tr>\n\t<td nowrap>Line $i:</td>\n\t<td><input type=text size=70 name=Line_$i ";
						if($skip[$i]=='y') //This only works in IE browsers
							echo "readonly ";
						echo " value=\"" . stripslashes($card['Line_' . $i]) . "\"></td>\n</tr>";
					}
				}else
				{
					if($card['Line_1']!="") //Hide the normally uneditable Line 1 for totally blank/static cards.
					{
						echo "<tr>\n\t<td nowrap>Line 1:</td>\n\t<td>";
						echo stripslashes($card['Line_1']) . "</td>\n</tr>";
					}
					echo "<input type=hidden name=Line_1 value=\"" . stripslashes($card['Line_1']) . "\">\n";
					for($i=2;$i<=$numlines;$i++)
					{
						//One entry for each available line
						echo "<tr>\n\t<td nowrap>Line $i:</td>\n\t<td><input type=text size=70 name=Line_$i ";
						if($skip[$i]=='y') //This only works in IE browsers
							echo "readonly ";
						echo " value=\"" . stripslashes($card['Line_' . $i]) . "\"></td>\n</tr>";
					}
				}
			?>
			</table>
		</td>
		<td align=left valign=top>
			<?php
				if($card['Action']!='New')
				{
					//echo "Select a Card to use as Default Values for your new card.\n";

					//Pop Reorder select box if more than one card exists for this template
					$statement = "SELECT ID, Card_Name FROM Finished_Cards where Template=" . $template . " AND Card_Name!='' order by Card_Name";
					$sql->Query($statement);
					if($sql->rows!=0)
					{
						echo "<br><select name=reorder size=1>\n<option value=0>Reorder Card</option>\n";
						$j=0;
						while($j<$sql->rows)
						{
							$sql->Fetch($j);
							echo "<option value='" . $sql->data[0];
							if($sql->data[0]==$card['ID'])
								echo "' selected>";
							else
								echo "'>";
							echo $sql->data[1] . "</option>\n";
							$j++;
						}
						echo "</select>\n";
					}
				}
			?>
			<br><input type=submit value="Redraw Card" name=button onclick="return OnButton1();"><br>
			<?php
				if($card['Action']=='New')
				{
					if(isset($card['Card_Name']))
						$cardNameOut = $card['Card_Name'];
					else
						$cardNameOut = '';
					echo "<br><b>Card Title:</b> <br>This is how this card will be listed for 
					Reorders, and should be unique for each card.<br>\n
					<input type=text size=35 name=Card_Name value=\"" . 
					stripslashes($cardNameOut) . "\">\n";
				}
			?></td>
	</tr>
	<tr>
	<?php
		//Pop photo upload if option is available
		$statement="SELECT Pic_Upload FROM Templates where ID=" . $template;
		$sql->QueryItem($statement);
		if($sql->data['Pic_Upload']=="y")
		{
			echo "<td colspan=2>Photo to upload: (JPG, only one picture per card. A new picture 
			will replace one that is already on the card.)<br>\n<input type=file name=photo 
			size=30><input type='hidden' name='submitted' value='true'>\n<input type='submit' 
			name='button' onclick='return OnButton3();' value='Upload Picture' >\n
			<input type=button onclick=\"window.open ('picinfo.php?pic_width=" . $pic_width . 
			"&pic_height=" . $pic_height . 
			"&msg=1', 'view', 'width=$width,height=$height');\" value='Photo Specifics'>\n";
		}
	?>
	</tr>
	<tr>
	<?php
		$statement = "SELECT * FROM Card_Symbols WHERE Template_ID=$template ORDER BY 
			Functional_Name";
		$sql->Query($statement);
		$j=0;
		if($sql->rows>0)
		{
			echo "\t\t\t\t<td colspan=2>\n\t\t\t\t\t<table border=1 cellspacing=3 
			cellpadding=3>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<th colspan=5 align=center>
			Optional Symbols</th>\n";
			$i=5;
			while($j<$sql->rows)
			{
				$sql->Fetch($j);
				if($i==5)
				{
					echo "\t\t\t\t\t\t</tr><tr>\n";
					$i=0;
				}
				echo "\t\t\t\t\t\t\t<td><input type=checkbox name=symbol[] value=\"" . 
				$sql->data['ID'] . "\"";
				if(preg_match("/,/" . $sql->data['ID'] . ",", $card['Symbols']))
				{
					echo " checked";
				}
				echo "><img src=\"images/symbols/" . $sql->data['Functional_Name'] . "\" 
					border=0></td>\n";
				$i++;
				$j++;
			}
			echo "\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t</td>\n";
		}
	?>
	</tr>
	<tr>
		<td>
			<table border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td colspan=3><b>Save and Order</b> will save the card and gather information for printing an order, <br>while <b>Save and Exit</b> will merely save the card to the Reorder list for later ordering.</td>
				</tr><tr>
					<td><input type=submit value='Save and Order' name=button  onclick='return OnButton2();'></td>
					<td><input type=submit value='Save and Exit' name=button onclick='return OnButton4();'></td>
					<td><input type=submit value='Return to Menu' name=button onclick='return OnButton5();'></td>
				</tr>
			</table>
		</td>
	</tr>
	</form>
</table>
</body>
</html>
