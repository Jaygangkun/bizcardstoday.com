<?php

session_start();
require("util.php");//db wrapper
require("emailer.php"); //email wrapper
$sql=new MySQL_class;
$sql->Create("bizcardstodaynew");
$template = $_SESSION['template'];
$user = $_SESSION['user'];

// echo('<pre>');
// print_r($_SESSION);
// print_r($_POST);
// print_R($_GET);

$statement="SELECT * FROM Templates WHERE ID='" . $template . "'";
$sql->QueryRow($statement);
$numlines = $sql->data['lines'];
$templatefile = $sql->data['Template'];
// print_r($sql->data);
// echo('</pre>');

// echo("->$templatefile-$numlines-<br>");

$c_name = $sql->data['Company'];
$pshippingcst = $sql->data['pship'];
// echo("->$pshippingcst-<br>");

if($_POST['card_quality']=='p')
{
	$sshippingcst = $sql->data['pship'];
}ELSE
{
	$sshippingcst = $sql->data['sship'];
}


$Picupload = $sql->data['Pic_Upload'];
if($_POST['card_quality']=='s' || $_POST['card_quality']=='')
	$Paper = $sql->data['Paper'];
else
	$Paper = $sql->data['Paper_premium'];

$Ink = $sql->data['Ink'];
$Agent = $sql->data['Agent'];
$Rep = $sql->data['Rep'];

if($_POST['card_quality']=='p')
{
	$per250 = $sql->data['per250_premium'];
	$per500 = $sql->data['per500_premium'];
	$per1000 = $sql->data['per1000_premium'];
	$per2000 = $sql->data['per2000_premium'];
}else
{
	$per250 = $sql->data['per250'];
	$per500 = $sql->data['per500'];
	$per1000 = $sql->data['per1000'];
	$per2000 = $sql->data['per2000'];
}
$Quickcard = $sql->data['QuickCard_Price'];

if($sql->data['Vertical']=='y') //Set display size variables
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

foreach($_POST as $a=>$b) //Load card array
{
	//if($b!="")
		$card[$a]=$b;
}

if($card['Action']=='New' && $card['ID']=="")
{
	$statement = "SELECT max(ID) from Finished_Cards;";
	$sql->QueryItem($statement);
	if($card['ID']=="") // If an ID has not been assigned, grab the next one in line.
	{
		$card['ID']=$sql->data[0]+1;
	}
}

if($Agent!="None" && $Agent!="")
{
	echo "<!--$Agent-->\n";
	$sql->QueryRow("SELECT Email, Name, Company, ID FROM Users WHERE Rep_Code='$Agent'");
	$Agent=$sql->data[0];
	$Agent_Name=$sql->data[1];

	//This is so if the Agent is placing the order it will still showing pricing information online
	if($user==$sql->data['ID'])
		$bypass_bypasser='y';
	else
		$bypass_bypasser='n';
	echo "<!--$Agent-->\n";
	$sql->QueryRow("SELECT Name FROM Company WHERE ID=" . $sql->data['Company']);
	$Agent_Company=$sql->data[0];
}else
{
	$sql->QueryItem("SELECT Name FROM Users WHERE Rep_Code='$Rep'");
	$Rep=$sql->data[0];
}

//Save the Card to the DB.
if($card['Card_Name']=="")
{
	$statement = "SELECT * FROM Finished_Cards WHERE Card_Name=\"" . $card['Line_1'] .
		"\" AND Template='$template'";
	$sql->Query($statement);
	//if($sql->rows>0)
	//	$card['Card_Name'] = $card['Line_1'] . " Duplicate";
	//else
		$card['Card_Name'] = $card['Line_1'];
}
$statement = "SELECT ID, Filename FROM Finished_Cards WHERE (Card_Name=\"" . $card['Card_Name'] .
	"\" OR ID=" . $card['ID'] . ") and Template='" . $template . "'";

// echo("-$statement-" . $sql->rows . '-');
$sql->QueryRow($statement);
//echo "<!--$statement-->\n";
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

if(DEBUGSW)
{
// 	echo("-$file-<br>");
}

for($i=1; $i<=$numlines; $i++) //Run the lines for insert/update
{
	if($_POST['Line_' . $i]!="")
		$statement .= ", Line_" . $i . "=\"" .
			stripslashes(str_replace("&#39;", "'", str_replace("\"", "&quot;", $_POST['Line_' . $i])))
			. "\"";
	else{
		$statement .= ", Line_" . $i . "=\"" .
			stripslashes(str_replace("&#39;", "'", str_replace("\"", "&quot;", $card['Line_' . $i])))
			. "\"";
		$_POST['Line_' . $i]=$card['Line_' . $i];
	}
	echo "<!--$i: " .
		stripslashes(str_replace("&#39;", "'", str_replace("\"", "&quot;", $_POST['Line_' . $i]))) .
		"-->\n";
}
//echo "<!--" . $statement . "-->\n";
foreach($card as $a=>$b)
{
	if($_POST[$a]=="")
		$_POST[$a]=$card[$a];
}

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
}elseif($card['Card_Name']!="")
	$statement .= ", Card_Name=\"" . str_replace("\"", "&quot;", $card['Card_Name']) . "\"";

$statement .=", Quantity='" . $_POST['Quantity'] . "', Quality='" .
	$_POST['card_quality'] . "', Approved='n', Notes=\"" .
	addslashes($_POST['Notes']) . "\", Filename=\"" . $file . "\", po='" .
	$_POST['po'] . "', company='" . $_POST['company'] . "', name='" .
	$_POST['name'] . "', address1='" . $_POST['address1'] . "', address2='" .
	$_POST['address2'] . "', city='" . $_POST['city'] . "', state='" .
	$_POST['state'] . "', zip='" . $_POST['zip'] . "', speed='" .
	$_POST['speed'] . "', ship='" . $_POST['ship'] .
	"', Order_By=$user, paper='" . $Paper . "', Ink='" . $Ink . "'";

if($_POST['hold']=='no')
{
	if($target!="")
		$statement .= ", Date_Approved=now(), Due_Date=now()+interval 14 day";
	else
		$statement .= ", Date_Stamp=now(), Due_Date=now()+interval 14 day";
}else
	$statement .= ", Date_Stamp=now()";

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

$rowcount = $sql->rows;

if(isset($_POST['symbol']))
{
	foreach($_POST['symbol'] as $a=>$b)
	{
		$symbol_line.=",$b";
		$statement2 = "SELECT Functional_Name FROM Card_Symbols WHERE ID=$b";
		$sql->QueryItem($statement2);
		$_POST['Notes'] .= "\n<br>Include Symbol: " . $sql->data[0];
	}
	$statement .= ", Symbols=\"$symbol_line,\"";
}
// echo("-$statement-" . $rowcount . '-');
// exit('-');
if($rowcount>0)
{
	$statement .= " WHERE ID=" . $card['ID'] . " and Template='" . $template . "'";
// exit("-$statement-");
	$sql->Update($statement);
}else
{
	$statement .= ", ID=" . $card['ID'];
// exit("-$statement-");
	$sql->Insert($statement);
}
if($_POST['Quantity']=="")
	$_POST['Quantity']=0;

$statement = "INSERT INTO Order_History SET Order_id=" . $card['ID'] . ", Quantity=" .
	$_POST['Quantity'] . ", Date_Stamp=now(), Action=\"";

if($client_status=='p')
{
	$statement .= "Test\", ";
}else
{
	if($_POST['hold']=='no')
		$statement .= "Order\", ";
	else
		$statement .= "Hold\", ";
}
$statement .= "Done_By=\"";
if($full=='y')
	$statement .="BizCards $user\"";
elseif($partial=='y')
	$statement .= "Admin $user\"";
else
	$statement .= $user . "\"";
$sql->Insert($statement);

// echo("-$statement-<br>");
$card['LastAction']="";

//echo("images/temp/$template-" . $card['ID'] . "*.svg");

//This is self correction to prevent overcrowding on the server.
//Purges any old cards still hanging around.
//@unlink("images/temp/$template-" . $card['ID'] . "*.svg");
// exit("-$file-<br>");

//Build modified SVG.
$lines=file("images/template/" . $templatefile . ".svg");
$output = fopen($file, "w");

$j=1;
while($j<=$numlines)
{
	//$_POST['Line_' . $j] = ereg_replace('&(amp;| *)reg;', '', str_replace('*', '', htmlentities(stripslashes( str_replace("&quot;", "\"", $_POST['Line_' . $j])))));
	$_POST['Line_' . $j] =
	preg_replace('/(amp;| *)reg;/', '', stripslashes(str_replace('*', '', htmlentities(str_replace("&quot;", "\"", $_POST['Line_' . $j]), ENT_NOQUOTES))));
	$j++;
}
foreach($lines as $a=>$b)
{
	//Swap out ~~X~~ for the Line X value. This includes swapping out * for Bullet
	$j = 1;
	while($j <= 20)
	{
		if(isset($_POST['Line_' . $j]))
		{
			$b=str_replace("~~$j~~", $_POST['Line_' . $j], $b);
		}
		$j++;
	}
	//Swap out picture placeholders
	$b = str_replace('~~insert picture~~', $CurCardID, $b);
	$b = str_replace('~~insert shortref~~', $ShortFile, $b);
	fwrite($output, $b);
}

?>
<head>
<meta charset="utf-8" />
</head>
<body>
<table border=0 cellspacing=0 cellpadding=0>
<tr>
	<td><embed src="<?php echo $file ?>" <?php echo $dimension; ?> type="image/svg+xml"></td>
	</tr>
	<?php
		if(file_exists("http://www.bizcardstoday.com/images/finished/" . $template . ".svg"))
		{
			echo "<tr><td><embed src='images/finished/" . $template . ".svg' " . $dimension .
				" type=\"image/svg+xml\"></td></tr>";
		}
	?>
</table><br><br>
<table border=0 cellspacing=0 cellpadding=0>
<tr>
	<td><? echo stripslashes($row['name']) . " has saved this card for future use."; ?>
	</td>
</tr>
<tr>
	<td align=center>
	<table border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width='33%' align=center>
			<input type=button value="Edit This Card" onclick="window.location='editpad.php'"></td>
		<td width='33%' align=center>
			<input type=button value="Logout" onclick="window.location='index2.php'"></td>
		<td width='33%' align=center>
			<input type=button value="Return to Menu" onclick="window.location='welcome.php'"></td>
	</tr>
	</table>
	</td>
</tr>
</table>

<?php
// echo('<pre>');
// print_r($_SESSION);
// print_r($_POST);
// print_r($card);
// print_R($_GET);
// echo('</pre>');
?>
</body>
</head>
