<?php
/**********************************************************************************************************************/
/*	Project: BizCardsToday.com															*/
/*	Programmer: Jason Hosler															*/
/*	Purpose: This page is the main page of the site.  This is where the user will fill out the card, 		*/
/*		upload pictures, specify shipping options, etc.										*/
/**********************************************************************************************************************/
	session_start(); //Start the session
// 	if($template==0 || !session_is_registered("template") || $template=="") //  boot to homepage if the card template is unspecified
	if(!session_is_registered("template")) //Boot to homepage if card template is not set.
		header("Location: index2.php");
		
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
		
	if($_REQUEST['Action']=='New' || $card['Action']=='New') //Brand new card.  Make new ID and fill default with last ordered card.
	{
		$statement = "SELECT max(ID) from Finished_Cards;";
		$sql->QueryItem($statement);
		$card['ID']=$sql->data[0]+1;
		$maxer = $card['ID'];
		$card['Action']="New";
		echo "<!--" . $card['ID'] . "||" . $_POST['redraw'] . "-->\n";
		if($_POST['redraw']=='y') // Redraw/Refresh.  Update $card array.
		{
			$_POST['redraw']='n';
			foreach($_POST as $a=>$b)
			{
				if($a!='Filename')
					$card[$a]=$b;
			}
			if($_POST['Card_Name']!="")//Verify Card Name is unique to Template
			{
				$statement = "SELECT ID FROM Finished_Cards WHERE Template=$template AND Card_Name=\"" . $_POST['Card_Name'] . "\";";
				$sql->Query($statement);
				if($sql->rows>0)
				{
					$alerter = "alert(\"Duplicate Card Title. Please select a new title.\");";
					$card['Card_Name']=$_POST['Card_Name'] . " Duplicate";
				}
			}else{
				$statement = "SELECT ID FROM Finished_Cards WHERE Template=$template AND Card_Name=\"" . $_POST['Line_1'] . "\";";
				$sql->Query($statement);
				if($sql->rows>0)
				{
					$alerter = "alert(\"Duplicate Card Title. Please select a new title.\");";
					$card['Card_Name']=$_POST['Card_Name'] . " Duplicate";
				}
			}	
			$card['ID']=$maxer;
		}elseif($card['upload']==true) //Just came from upload page.  No need to reload everything as it was just done on the upload.
		{
			$card['upload']=false;
		}elseif($_POST['redraw']!='y') // This is not a redraw/refresh action.  Load default values.
		{
			$statement = "SELECT ID, sum(if(line_1<>'',1,0) + if(line_2<>'',1,0) + if(line_3<>'',1,0) + if(line_4<>'',1,0) + if(line_5<>'',1,0) + if(line_6<>'',1,0) + if(line_7<>'',1,0) + if(line_8<>'',1,0) + if(line_9<>'',1,0) + if(line_10<>'',1,0) + if(line_11<>'',1,0) + if(line_12<>'',1,0)) as Num_Fields FROM Finished_Cards WHERE Template=$template GROUP BY ID ORDER BY Num_Fields DESC, Date_Stamp DESC";
			$sql->QueryRow($statement);
			if($sql->data[0]=="")
			{
				$statement = "SELECT Line_1, Line_2, Line_3, Line_4, Line_5, Line_6, Line_7, Line_8, Line_9, Line_10, Line_11, Line_12, Company, Address1, Address2, City, State, Zip FROM Templates WHERE ID=$template";
			}else
				$statement = "SELECT * FROM Finished_Cards WHERE Template=$template AND ID=" . $sql->data[0] . " ORDER BY Date_Stamp DESC";
			$sql->QueryRow($statement);
			foreach($sql->data as $a=>$b)
			{
				if($a!='Filename' && $a!='Card_Name' && $a!='ID' && $a!='po')
					$card[$a]=$b;
			}
			$card['ID']=$maxer;
		}		
	}else{ //Reorder an existing card
		$card['Action']="Reorder";
		echo "<!--" . $_REQUEST['reorder'] . " || " . $_POST['redraw'] . " || " . $card['upload'] . "-->\n";
		if($_POST['redraw']=="y") // Refresh/Redraw action.
		{
			$_POST['redraw']=false;
			foreach($_POST as $a=>$b)
			{
				$card[$a]=$b;
			}
		}elseif($_REQUEST['reorder']!="" && $_REQUEST['reorder']!="0") //Specific card requested through Reorder dropdown
		{
			$statement = "SELECT * FROM Finished_Cards WHERE ID=" . $_REQUEST['reorder']; 
			$sql->QueryRow($statement);
			foreach($sql->data as $a=>$b)
			{
				$card[$a]=$b;
			}
			$card['po']="";
			$card['ID']=$_REQUEST['reorder'];
		}elseif($card['upload']==true) //Just came from upload page.  No need to reload everything as it was just done on the upload.
		{
			$card['upload']=false;
		}else // Reorder initial entry load most recent card
		{
			echo "<!--test-->";
			$statement = "SELECT ID, sum(if(line_1<>'',1,0) + if(line_2<>'',1,0) + if(line_3<>'',1,0) + if(line_4<>'',1,0) + if(line_5<>'',1,0) + if(line_6<>'',1,0) + if(line_7<>'',1,0) + if(line_8<>'',1,0) + if(line_9<>'',1,0) + if(line_10<>'',1,0) + if(line_11<>'',1,0) + if(line_12<>'',1,0)) as Num_Fields FROM Finished_Cards WHERE Template=$template GROUP BY ID ORDER BY Num_Fields DESC, Date_Stamp DESC";
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
	$card['Filename']="images/finished/" . $template ."_" . $card['ID'] . ".svg";
	$CurCardID = "images/uploads/" . $template ."_" . $card['ID'] . ".jpg";
	$ShortFile = $template . "_" . $card['ID'] . ".jpg";
				
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
	
	//Replace the asterisks with the bullet code for inclusion in the card
	$card['Line_1'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_1']), ENT_NOQUOTES)));
	$card['Line_2'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_2']), ENT_NOQUOTES)));
	$card['Line_3'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"",$card['Line_3']), ENT_NOQUOTES)));
	$card['Line_4'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_4']), ENT_NOQUOTES)));
	$card['Line_5'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_5']), ENT_NOQUOTES)));
	$card['Line_6'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_6']), ENT_NOQUOTES)));
	$card['Line_7'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_7']), ENT_NOQUOTES)));
	$card['Line_8'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_8']), ENT_NOQUOTES)));
	$card['Line_9'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_9']), ENT_NOQUOTES)));
	$card['Line_10'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_10']), ENT_NOQUOTES)));
	$card['Line_11'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_11']), ENT_NOQUOTES)));
	$card['Line_12'] = stripslashes(str_replace('*', '·', htmlentities(str_replace("&quot;", "\"", $card['Line_12']), ENT_NOQUOTES)));
	foreach($lines as $a=>$b)
	{
		//Swap out ~~X~~ for the Line X value. This includes swapping out * for Bullet
				
		$b = str_replace('~~1~~', $card['Line_1'], str_replace('~~2~~', $card['Line_2'], str_replace('~~3~~', $card['Line_3'], str_replace('~~4~~', $card['Line_4'], str_replace('~~5~~', $card['Line_5'], str_replace('~~6~~', $card['Line_6'], str_replace('~~7~~', $card['Line_7'], str_replace('~~8~~', $card['Line_8'], str_replace('~~9~~', $card['Line_9'], str_replace('~~10~~', $card['Line_10'], str_replace('~~11~~', $card['Line_11'], str_replace('~~12~~', $card['Line_12'], $b))))))))))));
		
		//Swap out picture placeholders
		$b = str_replace('~~insert picture~~', $CurCardID, $b);
		$b = str_replace('~~insert shortref~~', $ShortFile, $b);
		fwrite($output, $b);
	}
	//Switch them back for display on the screen
	$card['Line_1'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_1']))));
	$card['Line_2'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_2']))));
	$card['Line_3'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_3']))));
	$card['Line_4'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_4']))));
	$card['Line_5'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_5']))));
	$card['Line_6'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_6']))));
	$card['Line_7'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_7']))));
	$card['Line_8'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_8']))));
	$card['Line_9'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_9']))));
	$card['Line_10'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_10']))));
	$card['Line_11'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_11']))));
	$card['Line_12'] = str_replace("&amp;", "&", stripslashes(str_replace('·', '*', str_replace("\"", "&quot;", $card['Line_12']))));

	$card['LastAction']="";
?>
<html>
	<head>
		<title>BizCardsToday.com Design & Proofing page</title>
		<link rel="stylesheet" href="bizcard.css" type="text/css">
		<script language=javascript>
		<!--
		<? if($alerter!="")
			{
			echo $alerter;
			$alerter="";
			}
		?>
		function OnButton1()
		{	//Redraw Card
			document.Form1.action = "editpad.php";
			document.Form1.elements['redraw'].value='y';
			<?
				if($card['Action']=='New')
				{
			?>
			if(document.Form1.elements['Card_Name'].value=="")
				document.Form1.elements['Card_Name'].value=document.Form1.elements['Line_1'].value;
			<?
				}
			?>
			document.Form1.submit();			
			return true;
		}
		
		function OnButton2()
		{	//Place Order
			document.Form1.action = "orderdetails.php";
			<?
				if($card['Action']=='New')
				{
			?>
			if(document.Form1.elements['Card_Name'].value=="")
				document.Form1.elements['Card_Name'].value=document.Form1.elements['Line_1'].value;
			<?
				}
			?>
			document.Form1.submit();			
			return true;
		}
		
		function OnButton3()
		{	//Upload photo
			document.Form1.action="upload.php"
			document.Form1.enctype="multipart/form-data"
			<?
				if($card['Action']=='New')
				{
			?>
			if(document.Form1.elements['Card_Name'].value=="")
				document.Form1.elements['Card_Name'].value=document.Form1.elements['Line_1'].value;
			<?
				}
			?>
			document.Form1.submit();			
			return true;
		}
		
		function OnButton4()
		{	//Hold Order
			document.Form1.action = "place_order.php"
			document.Form1.elements['hold'].value = 'yes';
			<?
				if($card['Action']=='New')
				{
			?>
			if(document.Form1.elements['Card_Name'].value=="")
				document.Form1.elements['Card_Name'].value=document.Form1.elements['Line_1'].value;
			<?
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
		function OnButton6()
		{	//Recall Card
			document.Form1.action = "editpad.php";
			<?
				if($card['Action']=='New')
				{
			?>
			if(document.Form1.elements['Card_Name'].value=="")
				document.Form1.elements['Card_Name'].value=document.Form1.elements['Line_1'].value;
			<?
				}
			?>
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
				<table border=0 cellspacing=0 cellpadding=0><tr><td><embed src="<? echo $file ?>"  <? echo $dimension; ?> type="image/svg+xml" pluginspage="http://www.adobe.com/svg/viewer/install/auto/"></td></tr></table>
				<?	//Pop Back Side button if needed
					if($twosided=='y')
					{
						echo "This is a two sided card. Please click the \"Back Side\" button to see what the back of your card looks like. <input type=button onclick=\"window.open ('backview.php?width=$width&height=$height', 'view', 'width=$width,height=$height');\" value='Back Side'>";
					}
					$statement="SELECT Name, Company FROM Users WHERE ID=$user";
					$sql->QueryRow($statement);
				?>
			</td>
			<td>Welcome <? echo $sql->data['Name']; ?><br><br>This is the design approved for your company business cards loaded with the information from the most complete card in our database. If the company design has changed, or if your department uses a variation not shown here, please contact us immediately.<br><br>Please check spelling, grammar, and punctuation carefully when reviewing your online proofs. We are not responsible for mistakes that you approve.<br><br>If your card does not appear, please <a href="CardViewer.exe" target="_blank">click here</a>.  You will be prompted to Open or Save the file, select Open and then after you've been told installation was successful, click the Redraw Card button.</td>
		</tr>
		</table>
		<input type=hidden value=<? echo $template ?> name=template>
		<table border=0 cellspacing=5 cellpadding=5><!-- width=600-->
			<form action="editpad.php" method=POST name=Form1 ENCTYPE="multipart/form-data" onsubmit="return disableForm(this);">
			<input type=hidden name=Action value="<? echo $_REQUEST['Action'] ?>">
			<input type=hidden name=hold value='no'>
			<input type=hidden name=redraw value='n'>
			<tr>
				<td colspan=2><b><? if($card['Action']=="New"){ echo "To Start A New Card"; }else{ echo "To Make Changes";} ?></b> simply replace the text in the boxes below and hit <b>Redraw Card</b>,<br> or select a name from the dropdown box to switch to another card. <br>Some lines may be locked to prevent changes to fixed information such as company address. <br>Use an asterisk(*) to insert a bullet(&#149;)</td>
			</tr>
			<tr>
				<td >
					<table border=0 cellspacing=0 cellpadding=0>
					<?
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
							echo "<tr>\n\t<td nowrap>Line 1:</td>\n\t<td>";
							echo stripslashes($card['Line_1']) . "</td>\n</tr>";
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
					<?
						if($card['Action']!='New')
						{
							//echo "Select a Card to use as Default Values for your new card.\n";
						
							//Pop Reorder select box if more than one card exists for this template
							$statement = "SELECT ID, Card_Name FROM Finished_Cards where Template=" . $template . " AND Card_Name!='' order by Card_Name";
							$sql->Query($statement);
							if($sql->rows!=0)
							{
								echo "<br><select name=reorder size=1 onchange='return OnButton6();'>\n<option value=0>Reorder Card</option>\n";
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
					<? 
						if($card['Action']=='New')
							echo "<br><b>Card Title:</b> (If different from Line 1)<br>This is how this card will be listed for Reorders.<br>\n<input type=text size=35 name=Card_Name value=\"" . stripslashes($card['Card_Name']) . "\">\n";
					?></td>
			</tr>
			<tr>
			<?
				//Pop photo upload if option is available
				$statement="SELECT Pic_Upload FROM Templates where ID=" . $template;
				$sql->QueryItem($statement);
				if($sql->data['Pic_Upload']=="y")
				{
					echo "<td colspan=2>Photo to upload: (JPG, only one picture per card. A new picture will replace one that is already on the card.)<br>\n<input type=file name=photo size=30><input type='hidden' name='submitted' value='true'>\n<input type='submit' name='button' onclick='return OnButton3();' value='Upload Picture' >\n<input type=button onclick=\"window.open ('picinfo.php?pic_width=" . $card['Pic_Width'] . "&pic_height=" . $card['Pic_Height'] . "', 'view', 'width=$width,height=$height');\" value='Photo Specifics'>\n";
				}
			?>
			</tr>
			<tr>
			<?
				$statement = "SELECT * FROM Card_Symbols WHERE Template_ID=$template ORDER BY Functional_Name";
				$sql->Query($statement);
				$j=0;
				if($sql->rows>0)
				{
					echo "\t\t\t\t<td colspan=2>\n\t\t\t\t\t<table border=1 cellspacing=3 cellpadding=3>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<th colspan=5 align=center>Optional Symbols</th>\n";
					$i=5;
					while($j<$sql->rows)
					{
						$sql->Fetch($j);
						if($i==5)
						{
							echo "\t\t\t\t\t\t</tr><tr>\n";
							$i=0;
						}
						echo "\t\t\t\t\t\t\t<td><input type=checkbox name=symbol[] value=\"" . $sql->data['ID'] . "\"";
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
