<?php

include_once('inc/preConf.php');
include_once('firelogger/firelogger.php');

flog('start');
flog('post', $_POST);
flog('request', $_REQUEST);
if(DEBUGSW)
{
}

function makeCard()
{

	//Make the card
	$file="images/temp/" . $template . "-" . $card['ID'] . ".svg";
// exit("-$file-");
	$lines=file("images/template/" . $templatefile . ".svg");
	$output = fopen($file, "w");

flog('template', $templatefile);

	$statement = "SELECT * FROM Card_Symbols WHERE Template_ID=$template";
flog('statement', $statement);

	$sql->Query($statement);
	$j=0;
	while($j<$sql->rows)
	{
		$sql->Fetch($j);
		$usable_symbols[]=$sql->data['Functional_Name'];
		$j++;
	}

	//Replace the asterisks with the bullet code for inclusion in the card, also replace double-quotes with &quot and the registered mark with � code
	$j=1;
	while($j<=$numlines)
	{
		$card['Line_' . $j] = ereg_replace('&(amp;| *)reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_' . $j]), ENT_NOQUOTES))));
		$j++;
	}
//flog('lines', $lines);
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
		$card['Line_' . $j] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_' . $j])))));
		$j++;
	}

	$card['LastAction']="";
}


/**********************************************************************************************************************/
/*	Project: BizCardsToday.com															*/
/*	Programmer: Jason Hosler															*/
/*	Purpose: This page is the main page of the site.  This is where the user will fill out the card, 		*/
/*		upload pictures, specify shipping options, etc.										*/
/**********************************************************************************************************************/
	$renterNewFlag = 0;
	session_start(); //Start the session
	if(isset($_SESSION['card'])) 
	{
// 		$card = $_SESSION['card'];
		if($_SESSION['card']['Action'] == 'New') 
		{
			$renterNewFlag = 1;
		}
	}

	if(isset($_SESSION['user'])) $user = $_SESSION['user'];
 	if(!session_is_registered("template")) //$template==0 |boot to homepage  || $template==""if the card template is unspecified
	{	
		header("Location: index2.php");	
	}
	if(!isset($_SESSION['template'])) //  boot to homepage if the card template is unspecified
	{
		header("Location: index2.php");	
	}else $template = $_SESSION['template'];
	
	if(!session_is_registered("card")) //Register variables with session
	{
		session_register("card");
		session_register("CurCardID");
		session_register("ShortFile");
	}
	require("util.php"); // db wrapper
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");

	if($card['LastAction']!="")
		$sql->Insert($card['LastAction']);
	echo "<!--LastAction = " . $card['LastAction'] . "-->\n";
	
flog('session', $_SESSION);
	
	if(isset($_SESSION['card'])) $card = $_SESSION['card'];
flog('card', $_SESSION['card']);
		
	//Brand new card.  Make new ID and fill default with last ordered card.
	// +A
	if($_GET['Action']=='New' || ($card['Action']=='New' AND $_POST['redraw'] == 'y')) //  
	{
flog('New');
if(DEBUGSW) echo('-New-');
		$card = $_SESSION['card'];
		$statement = "SELECT max(ID) from Finished_Cards;";
		$sql->QueryItem($statement);
		if($card['ID']=="") // If an ID has not been assigned, grab the next one in line.
		{
			$card['ID']=$sql->data[0]+1;
			$maxer = $card['ID'];
		}
		
		$card['Action'] = "New";
		$_SESSION['card']['Action'] = "New";
		
		echo "<!--" . $card['ID'] . "||" . $_POST['redraw'] . "-->\n";
		if($_POST['redraw']=='y') // Redraw/Refresh.  Update $card array.,  +C
		{  
			if($_POST['redraw']=='y')
			{
				$cardNameOut = $_POST['Card_Name'];
				$card['Card_Name'] = $_POST['Card_Name'];
flog('A2', $card['Card_Name']);
			}else
			{
				$cardNameOut = '';
			}
			                              
			foreach($_POST as $a=>$b)
			{
				if($a!='Filename')
					$card[$a]=$b;
			}
			if($_POST['Card_Name']!="")//Verify Card Name is unique to Template
			{

				$statement = "SELECT ID FROM Finished_Cards WHERE Template=$template AND Card_Name=\"" 
					. $_POST['Card_Name'] . "\";";
				$sql->Query($statement);
				if($sql->rows>0)
				{
					$alerter = "alert(\"Duplicate Card Title. Please select a new title.\");";
					$card['Card_Name'] = $_POST['Card_Name'] . "$card[ID]";
				}
			}else
			{

			$_POST['Line_1'] = trim($_POST['Line_1']);

				if($_POST['Line_1']=="")
				{
				$alerter = "alert(\"If you want to leave the first line blank you have re title the 
					card to a new name\");";
				$card['Card_Name']="No Name $card[ID]";
				}else
				{

					$statement = 
						"SELECT ID FROM Finished_Cards WHERE Template=$template AND Card_Name=\"" . 
							$_POST['Line_1'] . "\";";
					$sql->Query($statement);
						if($sql->rows>0)
						{
							$alerter = "alert(\"Duplicate Card Title. Please select a new title.\");";
							$card['Card_Name']=$_POST['Line_1'] . "$card[ID]";
						}else{
						$card['Card_Name']=$_POST['Line_1'];
						}
				}
			}
			$card['ID']=$maxer;
		}elseif($card['upload']==true) //Just came from upload page.  No need to reload everything as it was just done on the upload.  +D
		{
			$card['upload']=false;
		}elseif($_POST['redraw']!='y') // This is not a redraw/refresh action.  Load default values.  +E
		{
			$statement = "SELECT ID, sum(";
			$j=1;
			while($j<20)
			{
				$statement .= "if(line_$j<>'',1,0) + ";
				$j++;
			}
			$statement .= "if(line_20<>'',1,0)";
			$statement .= ") as Num_Fields FROM Finished_Cards WHERE Template=$template GROUP BY ID 
				ORDER BY Num_Fields DESC, Date_Stamp DESC";
// exit("-$statement-");
			$sql->QueryRow($statement);
			if($sql->data[0]=="")
			{
				$statement = "SELECT * FROM Templates WHERE ID=$template";
			}else
				$statement = "SELECT * FROM Finished_Cards WHERE Template=$template AND ID=" . 
					$sql->data[0] . " ORDER BY Date_Stamp DESC";
			$sql->QueryRow($statement);
			foreach($sql->data as $a=>$b)
			{
				if($a!='Filename' && $a!='Card_Name' && $a!='ID' && $a!='po')
					$card[$a]=$b;
			}
			$card['ID']=$maxer;
		}
	}else
	{ //Reorder an existing card   B
flog('Reorder');
if(DEBUGSW) echo('-reorder-');
		$card['Action'] = "Reorder";
$card['ID'] = $_POST['ID'];
		$_SESSION['card']['Action'] = "Reorder";
		
		echo "<!--Reorder: " . $_POST['reorder'] . " || Redraw: " . $_POST['redraw'] . 
			" || Upload: " . $card['upload'] . "-->\n";
		if($_POST['redraw']=="y") // Refresh/Redraw action.
		{
flog('area A');
			$_POST['redraw']='n';
flog('card', $card);
flog('card[ID], post[reorder]', $card['ID'], $_POST['reorder']);
			if($card['ID'] == $_POST['reorder']) //This is just a redraw
			{
				echo "<!--" . $card['ID'] . "-->\n";
				foreach($_POST as $a=>$b) //Put potentially new values from form into card array
				{
					$card[$a]=$b;
				}
			}else //This is changing the current card to another card.
			{
				echo "<!--Reorder section-->\n";
				$statement = "SELECT * FROM Finished_Cards WHERE ID=" . $_POST['reorder'];
				$sql->QueryRow($statement);
				foreach($sql->data as $a=>$b)
				{
					$card[$a]=$b;
				}

				$card['po']=""; //Clear any existing Purchase Order number.
			}
		}elseif($_POST['reorder']!="" && $_POST['reorder']!="0") //Specific card requested through Reorder dropdown
		{ //This should be impossible to reach since autosubmitions of redraw form is off
flog('area B');
			echo "<!--Reorder section-->\n";
			$statement = "SELECT * FROM Finished_Cards WHERE ID=" . $_REQUEST['reorder'];
			$sql->QueryRow($statement);
			foreach($sql->data as $a=>$b)
			{
				$card[$a]=$b;
			}
			$card['po']="";
			$card['ID'] = $_REQUEST['reorder'];
		}elseif($card['upload']==true) //Just came from upload page.  No need to reload everything as it was just done on the upload.
		{
flog('area C');
			$card['upload']=false;
		}else // Reorder initial entry. Load most recent card with most number of lines
		{
flog('area D');

			// if image flag set, set the card and template to the last used (after uploading image)
			// else set the info for the card to the blank card or template information
			// template info - $_SESSION['template']
			// 
				
			$template = $_SESSION['template'];
			if(isset($_SESSION['imageIdFlag'])) // coming back from an image upload 
			{
				$cdId = $_SESSION['imageIdFlag'];
				$name = $_SESSION['imageNameFlag'];
			}else 
			{
			
// check for default card image, if none then create one and show else show card image			



				// get the last selected card id
				//$cdId = $sql->data[0];
				if(isset($_POST['ID'])) $cdId = $_POST['ID'];
				else
				{
					$tmplSql = "SELECT * FROM Templates WHERE ID = $template";
					$sql->QueryRow($tmplSql);
					$templateName = $sql->data[2];
					$company = $sql->data[3];
					$line1 = $sql->data[6];
					$title = $sql->data[8];
flog('tmplQuery', $sql->data);

				}
				
			}
				

// 			$statement = "SELECT ID, sum(";
// 			$j=1;
// 			while($j<20)
// 			{
// 				$statement .= "if(line_$j<>'',1,0) + ";
// 				$j++;
// 			}
// 			$statement .= "if(line_20<>'',1,0)";
// 			$statement .= ") as Num_Fields FROM Finished_Cards WHERE Template=$template GROUP BY ID 
// 				ORDER BY Num_Fields DESC, Date_Stamp DESC";
// 			$sql->QueryRow($statement);
			
			
			
// currently uses a finished card for the image, need to detect if a default card exists and if it // doesn't create one
// 			echo "<!--" . $statement . "-->\n";
// 			$statement = "SELECT * FROM Finished_Cards WHERE Template=$template AND ID=" . 
// 				$cdId . " ORDER BY Date_Stamp DESC";
// flog('sql1', $statement);
// 			$sql->QueryRow($statement);
// flog('sql2', $statement);
// 			echo "<!--" . $statement . "-->\n";
// 			
// 			foreach($sql->data as $a=>$b)
// 			{
// 				if($a!='po')
// 					$card[$a]=$b;
// 			}
// flog('card2', $card);
		
		
		
		}
	}
	
	
	if(isset($_SESSION['imageUploadFlag']))
	{
		$id = $_SESSION['imageUploadFlag'];
// 		exit("-123-$id");
	}else
		$id = $card['ID'];
	
	
	//Verify file name reference and set Photo variables.
	$card['Filename']="images/finished/" . $template ."_" . $id . ".svg";
	$CurCardID = "images/uploads/" . $template ."_" . $id . ".jpg";
	$ShortFile = $template . "_" . $card['ID'] . ".jpg";


	//$_SESSION['card'] = $card;
	$_SESSION['CurCardID'] = $CurCardID;
	$_SESSION['ShortFile'] = $ShortFile;
			
// 	if($card['Action'] == 'New') $_SESSION['card']['Card_Name'] == '';

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
// exit("-$file-");
	$lines=file("images/template/" . $templatefile . ".svg");
	$output = fopen($file, "w");

flog('template', $templatefile);

	$statement = "SELECT * FROM Card_Symbols WHERE Template_ID=$template";
flog('statement', $statement);

	$sql->Query($statement);
	$j=0;
	while($j<$sql->rows)
	{
		$sql->Fetch($j);
		$usable_symbols[]=$sql->data['Functional_Name'];
		$j++;
	}

	//Replace the asterisks with the bullet code for inclusion in the card, also replace double-quotes with &quot and the registered mark with � code
	$j=1;
	while($j<=$numlines)
	{
		$card['Line_' . $j] = ereg_replace('&(amp;| *)reg;', '®', stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_' . $j]), ENT_NOQUOTES))));
		$j++;
	}
//flog('lines', $lines);
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
		$card['Line_' . $j] = str_replace('®', '&reg;', str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_' . $j])))));
		$j++;
	}

	$card['LastAction']="";
?>
<html>
<head>
	<title>BizCardsToday.com Design & Proofing page</title>
	<link rel="stylesheet" href="bizcard.css" type="text/css">
	<script language=javascript>
	<!--
	<?php  //This only pops if a Duplicate Card is found.
		if($alerter!="")
		{
		echo $alerter;
		$alerter="";
		}
	?>
	function OnButton1()
	{	//Redraw Card
// alert('1');
		document.Form1.action = "editpad.php";
		document.Form1.elements['redraw'].value='y';
		<?php
			if($card['Action']=='New')
			{
		?>
// alert('1');
//alert(document.Form1.elements['Line_1'].value.length);
// alert('-' + document.getElementById('Card_Name').value.length + '-');
			
				if(document.getElementById('Card_Name').value.length == 0)
				{
	// alert('here');
					document.getElementById('Card_Name').value = document.Form1.elements['Line_1'].value;
					alert('-' + document.Form1.elements['Line_1'].value + '-');
				}
		<?php
			}
		?>
		document.Form1.submit();
		return true;
	}

	function OnButton2()
	{	//Place Order
// alert('2');
		document.Form1.action = "orderdetails.php";
		<?php
			if($card['Action']=='New')
			{
		?>
		if(document.getElementById('Card_Name').value.length == 0)
		// if(document.Form1.elements['Card_Name'].value=="")
// 			document.Form1.elements['Card_Name'].value=document.Form1.elements['Line_1'].value;
			
		<?php
			}
		?>
		document.Form1.submit();
		return true;
	}

	function OnButton3()
	{	//Upload photo
// alert('3');
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
// alert('5');
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
	-->
	</script>
</head>

<body bgcolor=#ffffff leftmargin=5px>
	<table border=0 cellspacing=5 cellpadding=5>
	<tr>
		<td>
			<table border=0 cellspacing=0 cellpadding=0><tr><td><embed src="<?php echo $file ?>"  
			<?php echo $dimension; ?> type="image/svg+xml" 
				pluginspage="http://www.adobe.com/svg/viewer/install/auto/"></td></tr></table>
			<?php	//Pop Back Side button if needed
				if($twosided=='y')
				{
					echo "This is a two sided card. Please click the \"Back Side\" button to see what 
					the back of your card looks like. <input type=button 
					onclick=\"window.open ('backview.php?width=$width&height=$height', 'view', 
					'width=$width,height=$height');\" value='Back Side'>";
				}
				$statement="SELECT Name, Company FROM Users WHERE ID=$user";
				$sql->QueryRow($statement);
			?>
		</td>
		<td>Welcome <?php echo $sql->data['Name']; ?><br><br>This is the design approved for your 
		company business cards loaded with the information from the most complete card in our 
		database. If the company design has changed, or if your department uses a variation not 
		shown here, please contact us immediately.<br><br>Please check spelling, grammar, and 
		punctuation carefully when reviewing your online proofs. We are not responsible for 
		mistakes that you approve.<br><br>If your card does not appear, please 
		<a href="CardViewer.exe" target="_blank">click here</a>.  You will be prompted to Open or 
		Save the file, select Open and then after you've been told installation was successful, 
		click the Redraw Card button.</td>
	</tr>
	</table>
	<input type=hidden value=<?php echo $template ?> name=template>
	<table border=0 cellspacing=5 cellpadding=5><!-- width=600-->
		<form action="editpad.php" method=POST name='Form1' ENCTYPE="multipart/form-data" 
			onsubmit="return disableForm(this);">
		<input type=hidden name=Action value="<?php echo $_REQUEST['Action'] ?>"> <!--  -->
		<input type=hidden name='ID' value="<?php echo $card['ID'] ?>">
		<input type=hidden name=hold value='no'>
		<input type=hidden name=redraw value='n'>
		<tr>
			<td colspan=2><b>
			<?php if($card['Action']=="New")
			{ 
				echo "To Start A New Card"; 
			}else
			{ 
				echo "To Make Changes";} ?></b> simply replace the text in the boxes below and 
					hit <b>Redraw Card</b>,<br> or select a name from the dropdown box and hit 
					<b>Redraw Card</b> to switch to another card. <br>Some lines may be locked to 
					prevent changes to fixed information such as company address. 
					<br>Use an asterisk (*) to insert a bullet (&#149;)</td>
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
							echo "<tr>\n\t<td nowrap>Line $i:</td>\n\t<td><input type=text size=70 name='Line_$i' ";
							if($skip[$i]=='y') //This only works in IE browsers
								echo "readonly ";
							echo " value=\"" . stripslashes($card['Line_' . $i]) . "\"></td>\n</tr>";
						}
					}else
					{
flog('card2', $card);
						if($card['Line_1']!="") //Hide the normally uneditable Line 1 for totally blank/static cards.
						{
							echo "<tr>\n\t<td nowrap>Line 1:</td>\n\t<td>";
							echo stripslashes($card['Line_1']) . "</td>\n</tr>";
						}
						echo "<input type=hidden name=Line_1 value=\"" . 
							stripslashes($card['Line_1']) . "\">\n";
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
						$statement = "SELECT ID, Card_Name FROM Finished_Cards where Template=" . 
							$template . " AND Card_Name!='' order by Card_Name";
flog('re Sel', $statement);
						$sql->Query($statement);
						if($sql->rows!=0)
						{// <option value=0>Reorder Card-</option>
							echo "<br><select name=reorder size=1>\n\n";
							$j=0;
flog('cardID', $card['ID']);
							echo("<option value='0'>Reorder Card</option>");
							while($j<$sql->rows)
							{
								$sql->Fetch($j);

								if(isset($_SESSION['imageIdFlag']))
								{
									$name = $_SESSION['imageNameFlag'];
								}else 
								{
									
								
								}

								echo "<option value='" . $sql->data[0];
								if($sql->data[0]==$card['ID'])
								{
									echo "' selected>";
									$cardNameSelected = $sql->data['Card_Name'];
								}else
									echo "'>";
								echo $sql->data[1] . "</option>\n";
								$j++;
							}
							echo "</select>\n";
						}
					}
				?>
				<input type="hidden" name="Card_Name" value="<?php echo($cardNameSelected); ?>">
				<br><input type=submit value="Redraw Card" name=button onclick="return OnButton1();">
				<br>
<?php
if($card['Action']=='New')
{
flog('cardNameOut', $cardNameOut);
	echo "<br><b>Card Title:</b> <br>This is how this card will be listed for 
		Reorders, and should be unique for each card.<br>\n
		<input type=text size=35 name='Card_Name' id='Card_Name' value=\"" . 
		stripslashes($cardNameOut) . "\">\n";

}
?>
				</td>
		</tr>
		<tr>
		<?php
			//Pop photo upload if option is available
			$statement="SELECT Pic_Upload FROM Templates where ID=" . $template;
			$sql->QueryItem($statement);
			if($sql->data['Pic_Upload']=="y")
			{
				echo "<td colspan=2>Photo to upload: (JPG, only one picture per card. A new picture 
				will replace one that is already on the card.)<br>\n<input type=file 
				name=photo size=30><input type='hidden' name='submitted' 
				value='true'>\n<input type='submit' name='button' onclick='return OnButton3();' 
				value='Upload Picture' >\n
				<input type=button onclick=\"window.open ('picinfo.php?pic_width=" . $pic_width . 
				"&pic_height=" . $pic_height . 
				"&msg=1', 'view', 'width=$width,height=$height');\" value='Photo Specifics'>\n";
			}
		?>
		</tr>
		<tr>
		<?php
			$statement = "SELECT * FROM Card_Symbols WHERE Template_ID=$template ORDER BY Functional_Name";
			$sql->Query($statement);
			$j=0;
			if($sql->rows>0)
			{
				echo "\t\t\t\t<td colspan=2>\n\t\t\t\t\t<table border=1 cellspacing=3 
					cellpadding=3>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<th colspan=5 
					align=center>Optional Symbols</th>\n";
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
					if(ereg("," . $sql->data['ID'] . ",", $card['Symbols']))
					{
						echo " checked";
					}
					echo "><img src=\"images/symbols/" . $sql->data['Functional_Name'] . "\" border=0></td>\n";
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
						<td colspan=3><b>Save and Order</b> will save the card and gather information 
						for printing an order, <br>while <b>Save and Exit</b> will merely save the 
						card to the Reorder list for later ordering.</td>
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

<?php 
// echo("--$test2-<br>");
// $_SESSION['card'] = $card;
// 	echo('<pre>');
// 	print_r($_SESSION);
// 	// print_r($_REQUEST);
// 	echo('card');
// 	print_r($card);
// 	// print_r($_GET);
// 	print_r($_POST);
// 	echo('</pre>');
// 	echo("r-$renterNewFlag-");
?>
</body>
</html>
