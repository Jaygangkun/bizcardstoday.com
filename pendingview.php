<?php
	session_start();
	if($admin!="y")
		header("Location: index2.php");
		
	require("util.php");
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	$statement = "SELECT Template, Vertical FROM Templates where ID=" . $template;
	$sql->Queryrow($statement);
	$comp=$sql->data[0];
	if($sql->data[1]=='y')
	{
		$dimension="width=\"294\" height=\"498\"";
	}else{
		$dimension="height=\"294\" width=\"498\"";
	}
	
	if($HTTP_POST_VARS['card']!="")
	{
		$statement = "SELECT * FROM Finished_Cards WHERE Template=" . $template . " AND ID=" . $HTTP_POST_VARS['card'] . " AND Status<>'s' ORDER BY Line_1";
		$sql->Queryrow($statement);
		$quantity = $sql->data['Quantity'];
		$company = $sql->data['company'];
		$name = $sql->data['name'];
		$address1 = $sql->data['address1'];
		$address2 = $sql->data['address2'];
		$city = $sql->data['city'];
		$state = $sql->data['state'];
		$zip = $sql->data['zip'];
	}
	
	$statement = "SELECT * FROM Finished_Cards WHERE Status<>'s' and Template=" . $template;
	if($HTTP_POST_VARS['sort']=="")
		$statement .= " ORDER BY Line_1";
	else
		$statement .= " ORDER BY " . $HTTP_POST_VARS['sort'];
	
	$sql->Query($statement);
	if($HTTP_POST_VARS['view']!="")
	{
		$j=0;
		while($j<$sql->rows)
		{
			$sql->Fetch($j);
			if($sql->data['ID']==$HTTP_POST_VARS['view'])
			{
				$row=$sql->data;
				$j=$sql->rows;
			}
			$j++;
		}
	}else
	{
		$sql->Fetch(0);
		$row=$sql->data;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title><? echo $row['company'] ?> Finished Cards</title>
	</head>

	<body bgcolor="#ffffff">
		<p><a href="welcome.php">Return to Menu</a></p>
		<p><form action=pendingview.php method=post>
			<table border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td colspan=6>Sort By:</td>
				</tr><tr>
					<td><input type=radio name=sort value='Line_1' <? if($HTTP_POST_VARS['sort']=='Line_1' || $HTTP_POST_VARS['sort']=="") echo "checked"; ?> onclick="submit();"> Name</td>
					<td><input type=radio name=sort value='Date_Stamp' <? if($HTTP_POST_VARS['sort']=="Date_Stamp") echo "checked"; ?> onclick="submit();"> Order Date</td>
					<td><input type=radio name=sort value="po" <? if($HTTP_POST_VARS['sort']=="po") echo "checked"; ?> onclick="submit();"> Purchase Order</td>
					<td><input type=radio name=sort value="Status" <? if($HTTP_POST_VARS['sort']=='Status') echo "checked"; ?> onclick="submit();"> Status</td>
					<td><input type=radio name=sort value="Due_Date" <? if($HTTP_POST_VARS['sort']=='Due_Date') echo "checked"; ?> onclick="submit();"> Due Date</td>
				</tr>
			</table>
			<br><br>
			<table border=1 cellspacing=3 cellpadding=0>
				<tr>
					<th bgcolor=#CCCCCC>Name</th>
					<th bgcolor=#CCCCCC>Status</th>
					<th bgcolor=#CCCCCC>Order Date</th>
					<th bgcolor=#CCCCCC>Due Date</th>
					<? 
						if($full=='y')
						{
							echo "<th bgcolor=#CCCCCC>Print Date</th>\n";
							echo "<th bgcolor=#CCCCCC>Ship Date</th>\n";
						}
					?>
					<th bgcolor=#CCCCCC>Review Order #</th>
				</tr>
				<?
					$j=0;
					while($j<$sql->rows)
					{	
						$sql->Fetch($j);
						echo "\t\t\t\t<tr>\n";
						echo "\t\t\t\t\t<td";
						if($j%2!=0)
							echo " bgcolor=#CCCCCC";
						echo ">" . $sql->data['Line_1'] . "</td>\n";
						echo "\t\t\t\t\t<td";
						if($j%2!=0)
							echo " bgcolor=#CCCCCC";
						echo ">" . $sql->data['Status'] . "</td>\n";
						echo "\t\t\t\t\t<td";
						if($j%2!=0)
							echo " bgcolor=#CCCCCC";
						echo ">" . $sql->data['Date_Stamp'] . "</td>\n";
						echo "\t\t\t\t\t<td";
						if($j%2!=0)
							echo " bgcolor=#CCCCCC";
						echo ">" . $sql->data['due_date'] . "</td>\n";
						if($full=='y')
						{
							echo "\t\t\t\t\t<td";
							if($j%2!=0)
								echo " bgcolor=#CCCCCC";
							echo ">" . $sql->data['Print_Date'] . "</td>\n";
							echo "\t\t\t\t\t<td";
							if($j%2!=0)
								echo " bgcolor=#CCCCCC";
							echo ">" . $sql->data['ship_date'] . "</td>\n";
						}
						echo "\t\t\t\t\t<td";
						if($j%2!=0)
							echo " bgcolor=#CCCCCC";
						echo "><input type=submit value='" . $sql->data['ID'] . "' name=view></td>\n";
						$j++;
					}
				?>
			</table>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td><embed src='<? echo $row['Filename'] ?>'  <? echo $dimension; ?> type="image/svg+xml"></td>
				<td>
					<table border=0 cellspacing=0 cellspacing=0>
						<tr>
							<td bgcolor=#CCCCCC>Quantity:</td>
							<td><? echo $row['Quantity']; ?></td>
						</tr><tr>
							<td bgcolor=#CCCCCC>Speed Cards:</td>
							<td><? if($row['speed']=='y') echo "Yes"; else echo "No"; ?></td>
						</tr><tr>
							<td bgcolor=#CCCCCC>Shipping Information:</td>
						</tr><tr>
							<td bgcolor=#CCCCCC>Company</td>
							<td><? echo $row['company']; ?></td>
						</tr><tr>
							<td bgcolor=#CCCCCC>Name</td>
							<td><? echo $row['name']; ?></td>
						</tr><tr>
							<td bgcolor=#CCCCCC>Address 1</td>
							<td><? echo $row['address1']; ?></td>
						</tr><tr>
							<td bgcolor=#CCCCCC>Address 2</td>
							<td><? echo $row['address2']; ?></td>
						</tr><tr>
							<td colspan=2>
								<table border=0 cellspacing=4 cellpadding=4>
									<tr>
										<td bgcolor=#CCCCCC>City</td>
										<td><? echo $row['city'] ?></td>
										<td bgcolor=#CCCCCC>State</td>
										<td><? echo $row['state'] ?></td>
										<td bgcolor=#CCCCCC>Zip</td>
										<td><? echo $row['zip'] ?></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>

</html>