<style>
td.{
	font-family: verdana;
	font-size: 9px;
}
</style>
<?php

function connectdb()
{
	$db = mysql_connect("localhost", "artsy", "fartsy");
	mysql_select_db("bizcardstoday", $db);
}
connectdb();

if($step == "")
{
echo "<CENTER>
<TABLE>
<TR>
	<TD>Username:</td>
	<TD><form action=? method=post>
	<input type=hidden name=step value='uGo'>
	<input type=text name='u_name'></td>
</tr>
<tr>
	<td>Password:</td>
	<td><input type=password name='p_word'></td>
</tr>
<tr>
	<td rowspan=2><input type=submit value='Login'></form></td>
<tr>
</table>";
}


if($_POST['step'] == "uGo")
{
$griw = 1;
	if(($_POST[u_name] == "bizcards") && ($_POST[p_word] == "puravida"))
	{
		echo "<body bgcolor=C0CFB7><CENTER>";
		$lert = mysql_query("SELECT * FROM cards_ordered WHERE processed_out='n' ORDER BY id,date_day");
		$arow = mysql_num_rows($lert);
		echo "$arow orders <BR>";
		while($feds = mysql_fetch_array($lert))
		{
		echo "#$griw <TABLE cellpadding=0 cellspacing=0 style=\"border: 1px solid #000000;\" bgcolor=white><tr><td valign=top width=150>";
			$swde = mysql_query("SELECT * FROM Company WHERE ID='$feds[c_name]'");
			$boze = mysql_fetch_array($swde);
				echo "<B>Order Placed: $feds[date_day]</b><BR>$boze[Name]<BR>$boze[Address1]<BR>";
			if($boze[Address2] == "")
			{}else{
			echo "$boze[Address2]<BR>";
			}
			echo "$boze[City]<BR>$boze[State]<BR>$boze[Zip]";
		$shoed = str_replace("Shipping Information:<br>", "Shipping Information:", $feds[process_sheet]);
		$shoed = str_replace("<br>
		<br>", "<BR>", $shoed);
		$shoed = str_replace(",   <br>", "", $shoed);
		$shoed = str_replace("<BR><a href", "<a href", $shoed);
		echo "<td><td>$shoed <BR></td></tr></table><HR>";
		$griw++;
		}
	}else{}
}
?>