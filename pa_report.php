<?php
	session_start();
	require("util.php");
// 	if($template==0 || !session_is_registered("template") || $template=="")
	if(!session_is_registered("template")) //Boot to homepage if card template is not set.
		header("Location: index2.php");


		function connectdb()
		{
			$db = mysql_connect("localhost", "artsy", "fartsy");
			mysql_select_db("bizcardstoday", $db);
		}

	connectdb();
?>
<STYLE>
td.{
	font-familly: verdana;
	font-size: 11pt;
}
</style>
<?php
echo "<body bgcolor=#808080>
<CENTER>
<TABLE width=1000 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG SRC='images/pa_report_01.jpg' WIDTH=25 HEIGHT=44 ALT=''></TD>
		<TD width=100% BACKGROUND='images/pa_report_02.jpg' align=center valign=center><BR><b><font color=#ffffff face=verdana size=1><CENTER>Purchasing Agent Report</TD>
		<TD><IMG SRC='images/pa_report_04.jpg' WIDTH=24 HEIGHT=44 ALT=''></TD>
	</TR>
	<TR>
		<TD BACKGROUND='images/pa_report_05.jpg'><IMG SRC=images/pa_report_05.jpg></TD>
		<TD width=100% bgcolor=EBE9ED>";

$dSQL = mysql_query("SELECT * FROM pa_report WHERE status='n'");
$nROW = mysql_num_rows($dSQL);
if($nROW >="1")
{
echo "<BR><CENTER><i>If you are approving the order please insert the P.O.# before clicking the Process button.</i><BR><BR><TABLE WIDTH=95% CELLPADDING=0 CELLSPACING=0 BORDER=1>
<TR><TD bgcolor=white><B>Type</TD><TD bgcolor=white><B>Company Name</TD><TD bgcolor=white><B>Name</TD><TD bgcolor=white><CENTER><B>Order Date</TD><TD bgcolor=white colspan=2><CENTER><B>Approve</TD><TD bgcolor=white><B><CENTER>Purchase Order #</TD><TD bgcolor=white><CENTER><B>View</TD><TD bgcolor=#FFFFFF></TD></TR>";
	while($gSQL = mysql_fetch_array($dSQL))
	{
	$hSQL = mysql_query("SELECT * FROM Company where ID='$gSQL[Company]'");
	$rSQL = mysql_fetch_array($hSQL);
	echo "<form action=? method=post><input type=hidden name=ID value='$gSQL[id]'><TR><TD>Card</TD><TD>$rSQL[Name]</TD><TD>Testing</TD><TD><CENTER>$gSQL[order_date]</TD><TD><CENTER>Y:<input type=radio name=lcol$gSQL[id]></TD><TD><CENTER>N:<input type=radio name=lcol$gSQL[id]></TD><TD>P.O.#<input type=text size=20 maxlength=20></TD><TD><CENTER><input type=button value='View Item'></TD><TD><input type=submit value='Process'></TD></TR></form>";
	}
echo "</TABLE><BR><BR>";
}else{
echo "<CENTER><BR><BR><BR>Sorry no orders are awaiting approval<BR><BR><BR>";
}
echo" 		<TD BACKGROUND='images/pa_report_08.jpg'<IMG SRC=images/pa_report_08.jpg></TD>
	</TR>
	<TR>
		<TD><IMG SRC='images/pa_report_13.jpg' WIDTH=25 HEIGHT=22 ALT=''></TD>
		<TD width=100% BACKGROUND='images/pa_report_14.jpg'><IMG SRC=images/pa_report_14.jpg></TD>
		<TD><IMG SRC='images/pa_report_16.jpg' WIDTH=24 HEIGHT=22 ALT=''></TD>
	</TR>
</TABLE>";





echo "<form action=welcome.php method=post><input type=submit value='Main Page'></form>";
?>