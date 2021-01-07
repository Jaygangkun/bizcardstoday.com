<?php
	session_start();
// 	if($template==0 || !session_is_registered("template") || $template=="") //Boot to homepage if card template is not set.
		header("Location: index2.php");
	
	require("util.php");
	
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");
	if(!session_is_registered("form_local"))
	{
		session_register("form_local");
		session_register("form");
	}
		
	if($_POST['reloader']=='y')
	{
		$form=$_POST['form_id'];
		echo "<!--reload triggered: $form-->\n";
	}
	
	if($form=="")
	{
		$statement="SELECT ID FROM Forms WHERE Template=$template";
		$sql->QueryItem($statement);
		$form=$sql->data[0];
		echo "<!--\$form empty: $form-->\n";
	}

	if($_POST['Form_Name']!="" && $_POST['reloader']!='y')
	{
		$statement = "UPDATE Forms SET ";
		foreach($_POST as $a=>$b)
		{
			if(!is_numeric($a) && $a!="submitted" && $a!='reloader' && $a!='form_id')
				$statement .= $a . "=\"$b\", ";
		}
		$statement .= "Filename=\"";
		$file = $template . "-" . $form; //str_replace("'", "", str_replace(" ", "", $_POST['Form_Name']));
		$statement .= $file . "\" WHERE ID=$form;";
		echo "<!--$statement-->\n";
		$sql->Update($statement);
		$statement = "SELECT * FROM Forms WHERE ID=$form";
		$sql->QueryRow($statement);
		$form_local=$sql->data;
		echo "<!--POST Form_Name not empty: $form-->\n";
	}
		
	if($form_local['Form_Name']=="" || $_POST['reloader']=='y')
	{
		$statement = "SELECT * FROM Forms WHERE ID=$form";
		$sql->QueryRow($statement);
		$form_local=$sql->data;
		echo "<!--form_local Form_Name is empty: $form-->\n";
	}
$filename=$template . "-" . $form;	
$JPGpath = "images/JPG"; //Absolute path to where images are uploaded. No trailing slash
$PDFpath = "images/PDF";
$sizelim = "no"; //Do you want size limit, yes or no
$size = "2500000"; //What do you want size limited to be if there is one

if ($_REQUEST['submitted']){ // Begin processing portion of script

//all image types to upload

$cert1 = "image/pjpeg";
$cert2 = "image/jpeg"; //Jpeg type 2
$cert18 = "application/pdf";

$log = "";
//$log .=print_r($_FILES) . "<br>\n";



	if ($_FILES['img']['name'][2] != "") {
		$abpath="images/PDF";
	
		//checks if file exists
		/*
		if (file_exists("$abpath/$filename.pdf")) {
			$log .= "PDF already exists for this form<br>";
		} else {
		*/
		//Checks if file is an image
			copy($_FILES['img']['tmp_name'][2], "$abpath/$filename.pdf") or $log .= "Couldn't copy PDF to server<br>";
			if (file_exists("$abpath/$filename.pdf")) {
				$log .= "PDF file was uploaded<br>";
			}
		//}
	}

	if ($_FILES['img']['name'][0] != "") {
		$abpath="images/JPG";
			
		//checks if file exists
		/*
		if (file_exists("$abpath/$filename.jpg")) {
			$log .= "JPG already exists for this form<br>";
		} else {
		*/
			//Checks if file is an image
			move_uploaded_file($_FILES['img']['tmp_name'][0], "$abpath/$filename.jpg") or $log .= "Couldn't copy JPG to server<br>";
			if (file_exists("$abpath/$filename.jpg")) {
				$log .= "JPG file was uploaded<br>";
				$pic = getimagesize("$abpath/$filename.jpg");
				$form_local['Width']=$pic[0];
				$form_local['Height']=$pic[1];
			} else {
				$log .= "File is not a JPG<br>";
			}
		//}
	}

	if ($_FILES['img']['name'][1] != "") {
		$form_local['2sided']='y';
		$abpath="images/JPG";
			
		//checks if file exists
		/*
		if (file_exists("$abpath/$filename-back.jpg")) {
			$log .= "JPG already exists for this form<br>";
		} else {
		*/
			//Checks if file is an image
			move_uploaded_file($_FILES['img']['tmp_name'][1], "$abpath/$filename-back.jpg") or $log .= "Couldn't copy JPG Back to server<br>";
			if (file_exists("$abpath/$filename-back.jpg")) {
				$log .= "JPG Back file was uploaded<br>";
			} else {
				$log .= "File is not a JPG<br>";
			}
		//}
	}
}
	$form_local['Company']=$Company;
?>
<html>
	<head>
		<title>BizCardsToday.com Form Uploader</title>
	</head>
	
	<body>
		<table border=0 cellspacing=0 cellpadding=0><?
				if($form_local['Filename']!="")
				{
					$pic = getimagesize("images/JPG/" . $form_local['Filename'] . ".jpg");
					echo "<tr>\n\t\t\t\t<td><img src=\"images/JPG/" . $form_local['Filename'] . ".jpg\" " . $pic[3] . " border=1></td>\n\t\t\t</tr>";
				}
			?></table>
		<form action=form_edit.php method=post name=reloadform enctype="multipart/form-data" name=Form1>
		<input type=hidden name=reloader value='n'>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td>Select Form to Edit: <select size=1 name=form_id onchange="document.all['reloader'].value='y'; document.all['submitted'].value=false; submit();">
<?
						$statement = "SELECT * FROM Forms WHERE Template=$template order by Form_Name";
						$sql->Query($statement);
						$j=0;
						while($j<$sql->rows)
						{
							$sql->Fetch($j);
							echo "\t\t\t\t\t<option value=" . $sql->data['ID'];
							if($form_local['ID']==$sql->data['ID'])
								echo " selected";
							echo ">" . $sql->data['Form_Name'] . "</option>\n";
							$j++;
						}
					?></select>
				</td>
			</tr>
		</table>
		<input type=hidden name=Template value="<? echo $form_local['Template']; ?>">
		<input type=hidden name=submitted value=true>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td>Form Name:<input type=text name=Form_Name  value="<? echo $form_local['Form_Name']; ?>"></td>
			</tr><tr>
				<td><b>Pricing:</b><br><table border=1 cellspacing=0 cellpadding=0>
					<tr>
						<th align=center>&nbsp;</th>
						<th align=center>Quantity</th>
						<th align=center>Price</th>
					</tr><tr>
						<td nowrap>Low: </td>
						<td><input type=text name=Low_Quantity value="<? echo $form_local['Low_Quantity']; ?>"></td>
						<td><input type=text name=Low_Price value="<? echo $form_local['Low_Price']; ?>"></td>
					</tr><tr>
						<td nowrap>Default:</td>
						<td><input type=text name=Default_Quantity value="<? echo $form_local['Default_Quantity']; ?>"></td>
						<td><input type=text name=Default_Price value="<? echo $form_local['Default_Price']; ?>"></td>
					</tr><tr>
						<td nowrap>High: </td>
						<td><input type=text name=High_Quantity value="<? echo $form_local['High_Quantity']; ?>"></td>
						<td><input type=text name=High_Price value="<? echo $form_local['High_Price']; ?>"></td>
					</tr>
					</table>
				</td>
			</tr><tr>
				<td>Printing Information:<br><textarea name=Specs rows=10 cols=100><? echo $form_local['Specs']; ?></textarea></td>
			</tr><tr>
				<td>Display Status: Active <input type=radio name=Status value='a' <? if($form_local['Status']=='a') echo "checked"; ?>> Inactive <input type=radio name=Status value='i' <? if($form_local['Status']=='i') echo "checked"; ?>> Pending <input type=radio name=Status value='p' <? if($form_local['Status']=='p' || $form_local['Status']=='') echo "checked"; ?>></td>
			</tr><tr>
				<td>Printer Email:<input type=text name=Printer_Email value="<? echo $form_local['Printer_Email']; ?>" width=30></td>
			</tr><tr>
				<td>Default Shipping:<br><table border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td>Address1: <input type=text name=Address1 value="<? echo $form_local['Address1']; ?>"></td>
					</tr><tr>
						<td>Address2: <input type=text name=Address2 value="<? echo $form_local['Address2']; ?>"></td>
					</tr><tr>
						<td>City: <input type=text name=City value="<? echo $form_local['City']; ?>"></td>
					</tr><tr>
						<td>State: <input type=text name=State value="<? echo $form_local['State']; ?>"></td>
					</tr><tr>
						<td>Zip: <input type=text name=Zip value="<? echo $form_local['Zip']; ?>"></td>
					</tr>
				</table></td>
			</tr><tr>
				<td>JPG File:<input type=file name="img[]"></td>
			</tr><tr>
				<td>JPG Back File:<input type=file name="img[]"></td>
			</tr><tr>
				<td>PDF File:<input type=file name="img[]"></td>
			</tr><tr>
				<td align=center>
					<table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td align=center><input type=submit value="Save Form"></td>
							<td align=center><input type=reset value="Reset Values"></td>
							<td align=center><input type=button value="Return to Main" onclick="window.location='welcome.php';"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>
							