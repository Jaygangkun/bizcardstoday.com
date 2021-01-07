<?php
	require("util.php");
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	$statement = "SELECT ID, Login, Password, Name, Email, Company, Full_Admin, User_Admin FROM Users ORDER BY ID";
	//$statement = "SELECT * FROM Company ORDER BY ID";
	$sql->Query($statement);
	
	$j=0;
	while($j<$sql->rows)
	{
		$sql->Fetch($j);
		$commands[] = "INSERT INTO Users SET ID=" . $sql->data['ID'] . ", Login='" . $sql->data['Login'] . "', Password='" . $sql->data['Password'] . "', Name='" . $sql->data['Name'] . "', Email='" . $sql->data['Email'] . "', Company=\"" . $sql->data['Company'] . "\", Full_Admin='" . $sql->data['Full_Admin'] . "', User_Admin='" . $sql->data['User_Admin'] . "'";
		//$commands[] ="INSERT INTO Company SET ID=" . $sql->data['ID'] . ", Name=\"" . $sql->data['Name'] . "\", Address1=\"" . $sql->data['Address1'] . "\", Address2=\"" . $sql->data['Address2'] . "\", City=\"" . $sql->data['City'] . "\", State=\"" . $sql->data['State'] . "\", Zip=\"" . $sql->data['Zip'] . "\", Inactive=\"" . $sql->data['Inactive'] . "\", Approval=\"" . $sql->data['Approval'] . "\", Approval_Email=\"" . $sql->data['Approval_Email'] . "\", Approval_Phone=\"" . $sql->data['Approval_Phone'] . "\", Approval_Name=\"" . $sql->data['Approval_Name'] . "\", PO=\"" . $sql->data['PO'] . "\", Default_Ship=\"" . $sql->data['Default_Ship'] . "\", Rep=\"" . $sql->data['Rep'] . "\";";
		$j++;
	}
	
	$sql2 = new MySQL_class;
	$sql2->Create("bizforms");
	
	foreach($commands as $a)
	{
		echo $a . "<br>\n";
		$sql2->Insert($a);	
	}
?>