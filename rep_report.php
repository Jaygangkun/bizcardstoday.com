<?php
	session_start();
	require("util.php");
// 	if($template==0 || !session_is_registered("template") || $template=="")
	if(!session_is_registered("template")) //Boot to homepage if card template is not set.
		header("Location: index2.php");
		
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	$statement = "SELECT Name, Rep_Code FROM Users WHERE ID=$user";
	$sql->QueryRow($statement);
	$rep_code=$sql->data[1];
	$name = $sql->data[0];
	$comp_filter="";
	
	if($full=='y')
		$rep_code="%";
	if($_POST['viewrep']!="")
		$rep_code=$_POST['viewrep'];
	if($_POST['compfilter']!="")
		$comp_filter = " AND t.ID=" . $_POST['compfilter'];
	if($_POST['StartMonth']!="" || $_POST['DateRange']!="")
	{
		if($_POST['DateRange']!="")
		{
			switch($_POST['DateRange'])
			{
				case "1 week":
					$date_range = "SELECT t.Template_Name, f.Card_Name, o.Quantity, o.Date_Stamp FROM Order_History o, Templates t, Finished_Cards f WHERE o.Date_Stamp>date_sub(now(),interval 7 day) and t.Rep like '$rep_code' and f.Template=t.ID and o.Order_ID=f.ID and Action=\"Order\" $comp_filter ORDER BY Template_Name, Date_Stamp DESC, Card_Name;";
					$Title = "One Week Prior To " . date('F j, Y', strtotime("now"));
					break;
				case "2 weeks":
					$date_range = "SELECT t.Template_Name, f.Card_Name, o.Quantity, o.Date_Stamp FROM Order_History o, Templates t, Finished_Cards f WHERE o.Date_Stamp>date_sub(now(),interval 14 day) and t.Rep like '$rep_code' and f.Template=t.ID and o.Order_ID=f.ID and Action=\"Order\" $comp_filter ORDER BY Template_Name, Date_Stamp DESC, Card_Name;";
					$Title = "Two Weeks Prior To " . date('F j, Y', strtotime("now"));
					break;
				case "1 month":
					$date_range = "SELECT t.Template_Name, f.Card_Name, o.Quantity, o.Date_Stamp FROM Order_History o, Templates t, Finished_Cards f WHERE o.Date_Stamp>date_sub(now(),interval 1 month) and t.Rep like '$rep_code' and f.Template=t.ID and o.Order_ID=f.ID and Action=\"Order\" $comp_filter ORDER BY Template_Name, Date_Stamp DESC, Card_Name;";
					$Title = "One Month Prior To " . date('F j, Y', strtotime("now"));
					break;
				case "3 month":
					$date_range = "SELECT t.Template_Name, f.Card_Name, o.Quantity, o.Date_Stamp FROM Order_History o, Templates t, Finished_Cards f WHERE o.Date_Stamp>date_sub(now(),interval 3 month) and t.Rep like '$rep_code' and f.Template=t.ID and o.Order_ID=f.ID and Action=\"Order\" $comp_filter ORDER BY Template_Name, Date_Stamp DESC, Card_Name;";
					$Title = "Three Months Prior To " . date('F j, Y', strtotime("now"));
					break;
				case "6 month":
					$date_range = "SELECT t.Template_Name, f.Card_Name, o.Quantity, o.Date_Stamp FROM Order_History o, Templates t, Finished_Cards f WHERE o.Date_Stamp>date_sub(now(),interval 6 month) and t.Rep like '$rep_code' and f.Template=t.ID and o.Order_ID=f.ID and Action=\"Order\" $comp_filter ORDER BY Template_Name, Date_Stamp DESC, Card_Name;";
					$Title = "Six Months Prior To " . date('F j, Y', strtotime("now"));
					break;
				case "1 year":
					$date_range = "SELECT t.Template_Name, f.Card_Name, o.Quantity, o.Date_Stamp FROM Order_History o, Templates t, Finished_Cards f WHERE o.Date_Stamp>date_sub(now(),interval 1 year) and t.Rep like '$rep_code'  and f.Template=t.ID and o.Order_ID=f.ID and Action=\"Order\" $comp_filter ORDER BY Template_Name, Date_Stamp DESC, Card_Name;";
					$Title = "One Year Prior To " . date('F j, Y', strtotime("now"));
					break;
				case "all":
					$date_range = "SELECT t.Template_Name, f.Card_Name, o.Quantity, o.Date_Stamp FROM Order_History o, Templates t, Finished_Cards f WHERE t.Rep like '$rep_code' and f.Template=t.ID and o.Order_ID=f.ID and Action=\"Order\" $comp_filter ORDER BY Template_Name, Date_Stamp DESC, Card_Name;";
					$Title = "Complete Order History";
					break;
				default:
					$date_range = "SELECT t.Template_Name, f.Card_Name, o.Quantity, o.Date_Stamp FROM Order_History o, Templates t, Finished_Cards f WHERE o.Date_Stamp>date_sub(now(),interval 14 day) and t.Rep like '$rep_code' and f.Template=t.ID and o.Order_ID=f.ID and Action=\"Order\" $comp_filter ORDER BY Template_Name, Date_Stamp DESC, Card_Name;";
					$Title = "Two Weeks Prior To " . date('F j, Y', strtotime("now"));
			}
		}else
		{
			$date_range = "SELECT t.Template_Name, f.Card_Name, o.Quantity, o.Date_Stamp FROM Order_History o, Templates t, Finished_Cards f WHERE TO_DAYS(o.Date_Stamp)>TO_DAYS('" . $_POST['StartYear'] . "-" . $_POST['StartMonth'] . "-" . $_POST['StartDay'] . "') and TO_DAYS(o.Date_Stamp)<TO_DAYS('" . $_POST['EndYear'] . "-" . $_POST['EndMonth'] . "-" . $_POST['EndDay'] . "') and t.Rep like '$rep_code' and f.Template=t.ID and o.Order_ID=f.ID and Action=\"Order\" $comp_filter ORDER BY Template_Name, Date_Stamp DESC, Card_Name;";
			$Title = "Range From " . date("F j, Y", mktime(0,0,0, $_POST['StartMonth'], $_POST['StartDay'], $_POST['StartYear'])) . " To " . date('F j, Y', mktime(0,0,0, $_POST['EndMonth'], $_POST['EndDay'], $_POST['EndYear']));
		}		
	}else
	{
		$date_range = "SELECT t.Template_Name, f.Card_Name, o.Quantity, o.Date_Stamp FROM Order_History o, Templates t, Finished_Cards f WHERE o.Date_Stamp>date_sub(now(),interval 14 day) and t.Rep like '$rep_code' and f.Template=t.ID and o.Order_ID=f.ID and Action=\"Order\" $comp_filter ORDER BY Template_Name, Card_Name;";
		$Title = "Two Weeks Prior To " . date("F j, Y", strtotime("now"));
	}		
	echo "<!--$date_range-->\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>BizCardsToday.com Order Report - <? echo $Title ?></title>
	</head>

	<body bgcolor="#ffffff">
		<form action=rep_report.php method=post name=Form1>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td width=150 valign=top><? echo $name?></td>
				<td nowrap valign=bottom><font size=+2><b><? echo $Title; ?></b></font></td>
				<td width=150 align=right valign=top><? echo date("n/d/Y", strtotime("now")); ?></td>
			</tr><tr>
				<td colspan=3 align=center>
					<table border=0 cellspacing=3 cellpadding=0>
						<tr>
							<th align=center>Card Name</th>
							<th align=center>Order Date/Time</th>
							<th align=center>Card Name</th>
							<th align=center>Quantity</th>
						<?
							$sql->Query($date_range);
							$j=0;
							$cur_temp="";
							while($j<$sql->rows)
							{
								$sql->Fetch($j);
								
								if($j%2==0)
									$color="#FFFFFF";
								else
									$color="#CCCCCC";
									
								echo "</tr><tr>\n";
								if($sql->data['Template_Name']!=$cur_temp)
								{
									echo "\t<td bgcolor=$color>" . $sql->data['Template_Name'] . "</td>\n";
									$cur_temp=$sql->data['Template_Name'];
									$templates[]=$cur_temp;
								}else
									echo "\t<td bgcolor=$color>&nbsp;</td>\n";
								echo "\t<td bgcolor=$color>" . $sql->data['Date_Stamp'] . "</td>\n";
								echo "\t<td bgcolor=$color>" . $sql->data['Card_Name'] . "</td>\n";
								echo "\t<td bgcolor=$color>" . $sql->data['Quantity'] . "</td>\n";
								$j++;
							}
						?>
						</tr>
					</table>
				</td>
			</tr><tr>
				<td colspan=3 align=center>
					<table border=0 cellspacing=3 cellpadding=0>
						<tr>
							<th>Select Date Range from today:</th>
							<th>Select A Company to View:</th>
						</tr><tr>
							<td><select size=1 name=DateRange>
									<option value="">Select a Date Range</option>
									<option value="1 week" <? if($_POST['DateRange']=='1 week') echo "selected"; ?>>1 Week</option>
									<option value="2 weeks" <? if($_POST['DateRange']=='2 weeks') echo "selected"; ?>>2 Weeks</option>
									<option value="1 month" <? if($_POST['DateRange']=='1 month') echo "selected"; ?>>1 Month</option>
									<option value="3 month" <? if($_POST['DateRange']=='3 month') echo "selected"; ?>>3 Months</option>
									<option value="6 month" <? if($_POST['DateRange']=='6 month') echo "selected"; ?>>6 Months</option>
									<option value="1 year" <? if($_POST['DateRange']=='1 year') echo "selected"; ?>>1 Year</option>
									<option value="all" <? if($_POST['DateRange']=='all') echo "selected"; ?>>All Time</option>
								</select>
							</td>
							<td>
								<?
									echo "<!--$rep_code-->\n";
									$statement="SELECT t.ID, Template_Name FROM Templates t, Rep r WHERE t.Rep=r.Code and r.ID like '$rep_code' ORDER BY Template_Name";
									$sql->Query($statement);
									$j=0;
									echo "<select size=1 name=compfilter>";
									echo "\n\t\t\t\t\t\t\t<option value=\"\">All</option>";
									while($j<$sql->rows)
									{
										$sql->Fetch($j);
										echo "\n\t\t\t\t\t\t\t<option value=\"" . $sql->data[0] . "\"";
										if($sql->data[0]==$comp_filter)
											echo " selected";
										echo ">" . $sql->data[1] . "</option>";
										$j++;
									}
								?>
							</td>
						</tr><tr>
							<th colspan=2>Or Provide a Range:</th>
						</tr><tr>
							<td colspan=2>Orders placed between: <select size=1 name=StartMonth>
									<option value="1">Jan</option>
									<option value="2">Feb</option>
									<option value="3">Mar</option>
									<option value="4">Apr</option>
									<option value="5">May</option>
									<option value="6">Jun</option>
									<option value=7>Jul</option>
									<option value=8>Aug</option>
									<option value=9>Sep</option>
									<option value=10>Oct</option>
									<option value=11>Nov</option>
									<option value=12>Dec</option>
									</select>&nbsp;<select size=1 name=StartDay>
									<option value=1>1</option>
									<option value=2>2</option>
									<option value=3>3</option>
									<option value=4>4</option>
									<option value=5>5</option>
									<option value=6>6</option>
									<option value=7>7</option>
									<option value=8>8</option>
									<option value=9>9</option>
									<option value=10>10</option>
									<option value=11>11</option>
									<option value=12>12</option>
									<option value=13>13</option>
									<option value=14>14</option>
									<option value=15>15</option>
									<option value=16>16</option>
									<option value=17>17</option>
									<option value=18>18</option>
									<option value=19>19</option>
									<option value=20>20</option>
									<option value=21>21</option>
									<option value=22>22</option>
									<option value=23>23</option>
									<option value=24>24</option>
									<option value=25>25</option>
									<option value=26>26</option>
									<option value=27>27</option>
									<option value=28>28</option>
									<option value=29>29</option>
									<option value=30>30</option>
									<option value=31>31</option>
									</select>, <select size=1 name=StartYear>
									<option value=2006>2006</option>
									<option value=2007>2007</option>
									</select> and <select size=1 name=EndMonth>
									<option value="1">Jan</option>
									<option value="2">Feb</option>
									<option value="3">Mar</option>
									<option value="4">Apr</option>
									<option value="5">May</option>
									<option value="6">Jun</option>
									<option value=7>Jul</option>
									<option value=8>Aug</option>
									<option value=9>Sep</option>
									<option value=10>Oct</option>
									<option value=11>Nov</option>
									<option value=12>Dec</option>
									</select>&nbsp;<select size=1 name=EndDay>
									<option value=1>1</option>
									<option value=2>2</option>
									<option value=3>3</option>
									<option value=4>4</option>
									<option value=5>5</option>
									<option value=6>6</option>
									<option value=7>7</option>
									<option value=8>8</option>
									<option value=9>9</option>
									<option value=10>10</option>
									<option value=11>11</option>
									<option value=12>12</option>
									<option value=13>13</option>
									<option value=14>14</option>
									<option value=15>15</option>
									<option value=16>16</option>
									<option value=17>17</option>
									<option value=18>18</option>
									<option value=19>19</option>
									<option value=20>20</option>
									<option value=21>21</option>
									<option value=22>22</option>
									<option value=23>23</option>
									<option value=24>24</option>
									<option value=25>25</option>
									<option value=26>26</option>
									<option value=27>27</option>
									<option value=28>28</option>
									<option value=29>29</option>
									<option value=30>30</option>
									<option value=31>31</option>
									</select>, <select size=1 name=EndYear>
									<option value=2006>2006</option>
									<option value=2007>2007</option>
									</select>
							</td>
						</tr><tr>
							<td><input type=submit value="Rebuild Report">&nbsp;<input type=submit value="Return to Menu" onclick="document.Form1.action='welcome.php';"></td>
						</tr><?
							if($full=='y')
							{
								$statement="SELECT ID, Name, Code FROM Rep ORDER BY Name";
								$sql->Query($statement);
								$j=0;
								echo "</tr>\n\t\t\t\t\t\t<td><select name=viewrep size=1>\n";
								echo "\t\t\t\t\t\t\t<option value=\"\">Switch Rep</option>\n";
								echo "\t\t\t\t\t\t\t<option value=\"%\"";
								if($_POST['viewrep']=="%")
									echo " selected";
								echo ">All Reps</option>\n";
								while($j<$sql->rows)
								{
									$sql->Fetch($j);
									echo "\t\t\t\t\t\t\t<option value=\"" . $sql->data['ID'] . "\"";
									if($sql->data['ID']==$rep_code)
										echo " selected";
									echo ">" . $sql->data['Name'] . "{" . $sql->data['Code'] . "}</option>\n";
									$j++;
								}
								echo "\t\t\t\t\t\t</select></td>\n\t\t\t\t\t</tr>\n";
							}
						?>
					</table>
				</td>
			</tr>
		</table>
	</body>

</html>