<?php
	session_start();
// 	if($template==0 || !session_is_registered("template") || $template=="")
	// if(!session_is_registered("template")) //Boot to homepage if card template is not set.
	if(!isset($_SESSION["template"])) //Boot to homepage if card template is not set.
		header("Location: index2.php");

	// if(!session_is_registered("PDF"))
	if(!isset($_SESSION["PDF"]))
	{
		// session_register("PDF");
		$_SESSION['PDF'] = '';
	}
	require("util.php");
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");

	if($Company=="") //Default shipping address
	{
		$statement = "SELECT *, c.Name as Company_Name FROM Templates t, Company c where t.company=c.id and t.ID=" . $template;
		$sql->QueryRow($statement);
		$PDF['company'] = $sql->data['Company_Name'];
		$PDF['name']=$sql->data['Name'];
		$PDF['address1']=$sql->data['Address1'];
		$PDF['address2']=$sql->data['Address2'];
		$PDF['city']=$sql->data['City'];
		$PDF['state']=$sql->data['State'];
		$PDF['zip']=$sql->data['Zip'];
		$PDF['po']=$sql->data['po'];
	}
	$order_complete=false;;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>BizCardsToday PDF Uploader</title>
		<script language="Javascript">
		<!--
			function popOptions(a)
			{
				switch(a)
				{
					case "off":
						Options.innerHTML = "";
						break;
					case "laid":
						Options.innerHTML = "<table border=0 cellspacing=4 cellpadding=5>\n\t<tr>\n\t\t<td colspan=8><br>Please specify the color for your card.</td>\n\t</tr><tr>\n\t\t<th>Color</th>\n\t</tr><tr>\n\t\t<td><input type=radio value='gray' name=color> Gray</td>\n\t\t<td><input type=radio value='ice blue' name=color> Ice Blue</td>\n\t\t<td><input type=radio value='ivory' name=color> Ivory</td>\n\t\t<td><input type=radio value='natural' name=color> Natural</td>\n\t\t<td><input type=radio value='pale green' name=color> Pale Green</td>\n\t\t<td><input type=radio value='white' name=color checked> White</td>\n\t</tr>\n</table>";
						break;
					case "fiber":
						Options.innerHTML = "<table border=0 cellspacing=4 cellpadding=5>\n\t<tr>\n\t\t<td colspan=8><br>Please specify the color for your card.</td>\n\t</tr><tr>\n\t\t<th>Color</th>\n\t</tr><tr>\n\t\t<td><input type=radio value='fiesta white' name=color> Fiesta White</td>\n\t\t<td><input type=radio value='white' name=color checked> White</td>\n\t\t<td><input type=radio value='champagne ivory' name=color> Champagne Ivory</td>\n\t\t<td><input type=radio value='natural' name=color> Natural</td>\n\t\t<td><input type=radio value='birch' name=color> Birch</td>\n\t\t<td><input type=radio value='cottonwood' name=color> Cottonwood</td>\n\t</tr><tr>\n\t\t<td><input type=radio value='cream' name=color> Cream</td>\n\t\t<td><input type=radio value='balsa' name=color> Balsa</td>\n\t\t<td><input type=radio value='gray' name=color> Gray</td>\n\t\t<td><input type=radio value='rose' name=color> Rose</td>\n\t\t<td><input type=radio value='ice blue' name=color> Ice Blue</td>\n\t\t<td><input type=radio value='thyme' name=color> Thyme</td>\n\t</tr><tr>\n\t\t<td><input type=radio value='frosted glass' name=color> Frosted Glass</td>\n\t\t<td><input type=radio value='driftwood' name=color> Driftwood</td>\n\t\t<td><input type=radio value='sunflower' name=color> Sunflower</td>\n\t\t<td><input type=radio value='moss' name=color> Moss</td>\n\t\t<td><input type=radio value='periwinkle' name=color> Periwinkle</td>\n\t\t<td><input type=radio value='kraft' name=color> Kraft</td>\n\t</tr>\n</table>";
						break;
				}
			}
		-->
		</script>
		<csscriptdict import>
			<script type="text/javascript" src="file:///C:/Documents%20and%20Settings/Jason%20Hosler.JASON/Application%20Data/Adobe/Adobe%20GoLive/Settings/JScripts/GlobalScripts/CSScriptLib.js"></script>
		</csscriptdict>
		<csactiondict>
<script type="text/javascript">
var preloadFlag = false;
function preloadImages()
{
	if (document.images)
	{
		over_businesscard_01 = newImage(/*URL*/'images/businesscard_01-over.gif');
		preloadFlag = true;
	}
}

</script>
		</csactiondict>
	</head>

	<body onload="preloadImages();" bgcolor="#ffffff"><h2><a onmouseover="changeImages( /*CMP*/'businesscard_01',/*URL*/'images/businesscard_01-over.gif');return true" onmouseout="changeImages( /*CMP*/'businesscard_01',/*URL*/'images/businesscard_01.gif');return true" href="#"><img src="images/businesscard_01.gif" alt="" name="businesscard_01" width="160" height="130" border="0"></a></h2><h3>File Uploader (PDF or AI files only)</h3>
		<form action=pdfstep2.php method=post>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td colspan=2>Quantity:<br><input type=radio value=250 name=quantity> 250 <input type=radio value=500 name=quantity> 500 <input type=radio value=1000 name=quantity checked> 1000 <input type=radio value=2000 name=quantity> 2000</td>
			</tr><tr>
					<td><br>Card Type:</td>
				</tr><tr>
				<td width=350>
					<table border=0 cellspacing=4 cellpadding=5>
						<tr>
							<th colspan=3>Premium Color</th>
						</tr><tr>
							<td align=center><input type=radio name=type value="Premium 4/0" checked onclick="popOptions('off'); sides[0].checked=true;"> 4/0</td>
							<td align=center><input type=radio name=type value="Premium 4/B" onclick="popOptions('off'); sides[1].checked=true;"> 4/B</td>
							<td align=center><input type=radio name=type value="Premium 4/4" onclick="popOptions('off'); sides[1].checked=true;"> 4/4</td>
						</tr><tr>
							<th colspan=3><br>Regular Color</th>
						</tr><tr>
							<td colspan=3>
								<table border=0 cellspacing=0 cellpadding=0>
									<tr>
										<td align=center><input type=radio name=type value="Regular 4/0" onclick="popOptions('off'); sides[0].checked=true;"> 4/0</td>
										<td align=center><input type=radio name=type value="Regular 4/1" onclick="popOptions('off'); sides[1].checked=true;"> 4/1</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
				<td valign=top>
					<table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<th colspan=4>Traditional Cards</th>
						</tr><tr>
							<td><input type=radio name=type value="Brite White" onclick="popOptions('off');"> Brite White</td>
							<td><input type=radio name=type value="Laid" onclick="popOptions('laid');"> Laid</td>
							<td><input type=radio name=type value="Linen" onclick="popOptions('laid');"> Linen</td>
							<td><input type=radio name=type value="Fiber" onclick="popOptions('fiber');"> Fiber</td>
						</tr><tr>
							<td colspan=4><table border=0 cellspacing=0 cellpadding=0>
								<tr>
									<td align=center><input type=radio name=sides value="Single" checked> 1 sided</td>
									<td align=center><input type=radio name=sides value="Dual"> 2 sided</td>
								</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr><tr>
				<td colspan=2><span id=Options></span></td>
			</tr><tr>
				<td colspan=2><input type=submit value="Proceed with Order">&nbsp;&nbsp;&nbsp;<input type=button value="Return to Main" onclick="window.location='welcome.php'"></td>
			</tr>
		</table>
		</form>
	</body>

</html>