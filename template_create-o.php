<?php
/*  This page will allow the creation of a new Template record. */
	session_start();
	$template = $_SESSION['template'];

	if($_SESSION['admin'] != "y")  //Boot if access not allowed
		header("Location: index.html");

	if(!session_is_registered("per250"))  //Session variables to allow data retention on pic upload
	{
		session_register("Company");
		session_register("company_name");
		session_register("Template_Name");
		session_register("Address1");
		session_register("Address2");
		session_register("City");
		session_register("State");
		session_register("Zip");
		session_register("line_count");
		session_register("per250");
		session_register("per500");
		session_register("per1000");
		session_register("per2000");
		session_register("per250_premium");
		session_register("per500_premium");
		session_register("per1000_premium");
		session_register("per2000_premium");
		session_register("QuickCard_Price");
		session_register("Card_Quality");
		session_register("Pic_Upload");
		session_register("Two_Sided");
		session_register("Paper");
		session_register("Paper_premium");
		session_register("Ink");
		session_register("Vertical");
		session_register("Allow_PDF");
		session_register("Approval_Req");
		session_register("Approval_Email");
		session_register("Approval_Phone");
		session_register("Approval_Name");
		session_register("Printer_Email");
		session_register("Pic_Width");
		session_register("Pic_Height");
	}
	require("util.php"); //db wrapper
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");

	$cardtemplate=array();
	if($_POST['button']=="")
		$Company="";

	//This section will load all of the session values into the $cardtemplate array for display
	if($Approval_Req!="")
		$cardtemplate['Approval_Req']=$Approval_Req;
	if($Approval_Email!="")
		$cardtemplate['Approval_Email']=$Approval_Email;
	if($Approval_Phone!="")
		$cardtemplate['Approval_Phone']=$Approval_Phone;
	if($Approval_Name!="")
		$cardtemplate['Approval_Name']=$Approval_Name;
	if($per250!="")
		$cardtemplate['per250']=$per250;
	if($per500!="")
		$cardtemplate['per500']=$per500;
	if($per1000!="")
		$cardtemplate['per1000']=$per1000;
	if($per2000!="")
		$cardtemplate['per2000']=$per2000;
	if($per250_premium!="")
		$cardtemplate['per250_premium']=$per250_premium;
	if($per500!="")
		$cardtemplate['per500_premium']=$per500_premium;
	if($per1000!="")
		$cardtemplate['per1000_premium']=$per1000_premium;
	if($per2000!="")
		$cardtemplate['per2000_premium']=$per2000_premium;
	if($QuickCard_Price!="")
		$cardtemplate['QuickCard_Price']=$QuickCard_Price;
	if($Card_Quality!="")
		$cardtemplate['card_quality']=$Card_Quality;
	if($Pic_Upload!="")
		$cardtemplate['Pic_Upload']=$Pic_Upload;
	if($Pic_Width!="")
		$cardtemplate['Pic_Width']=$Pic_Width;
	if($Pic_Height!="")
		$cardtemplate['Pic_Height']=$Pic_Height;
	if($Two_Sided!="")
		$cardtemplate['2_Sided']=$Two_Sided;
	if($Paper!="")
		$cardtemplate['Paper']=$Paper;
	if($Paper_premium!="")
		$cardtemplate['Paper_premium']=$Paper_premium;
	if($Ink!="")
		$cardtemplate['Ink']=$Ink;
	if($Vertical!="")
		$cardtemplate['Vertical']=$Vertical;
	if($Allow_PDF!="")
		$cardtemplate['Allow_PDF']=$Allow_PDF;
	if($company_name!="")
	{
		$sql->QueryItem("SELECT Name, ID FROM Company WHERE Name=\"$company_name\"");
		if($sql->rows>0)
		{
			$cardtemplate['company_name']=$sql->data[0];
			$cardtemplate['company']=$sql->data[1];
		}else
			$cardtemplate['company_name']=$company_name;
	}
	if($Template_Name!="")
		$cardtemplate['template_name']=$Template_Name;
	if($Address1!="")
		$cardtemplate['address1']=$Address1;
	if($Address2!="")
		$cardtemplate['address2']=$Address2;
	if($City!="")
		$cardtemplate['city']=$City;
	if($State!="")
		$cardtemplate['state']=$State;
	if($Zip!="")
		$cardtemplate['zip']=$Zip;
	if($Printer_Email!="")
		$cardtemplate['Printer_Email']=$Printer_Email;

	if(isset($_POST['Template_Name'])) //This will load a submitted template (just created) for display.
	{
		if($_POST['Company_Name']=="" && $_POST['Old_Company']!="")
			$_POST['Company_Name']=$_POST['Old_Company'];

		$sql->QueryItem("SELECT Name, ID FROM Company WHERE Name=\"" . $_POST['Company_Name'] . "\"");
		$cardtemplate['company_name']=$sql->data[0];
		$cardtemplate['company']=$sql->data[1];

		$cardtemplate['template_name'] = $_POST['Template_Name'];
		$cardtemplate['name'] = $_POST['name'];
		$cardtemplate['address1']=$_POST['address1'];
		$cardtemplate['address2']=$_POST['address2'];
		$cardtemplate['city']=$_POST['city'];
		$cardtemplate['state']=$_POST['state'];
		$cardtemplate['zip']=$_POST['zip'];
		$cardtemplate['per250']=$_POST['per250'];
		$cardtemplate['per500']=$_POST['per500'];
		$cardtemplate['per1000']=$_POST['per1000'];
		$cardtemplate['per2000']=$_POST['per2000'];
		$cardtemplate['per250_premium']=$_POST['per250_premium'];
		$cardtemplate['per500_premium']=$_POST['per500_premium'];
		$cardtemplate['per1000_premium']=$_POST['per1000_premium'];
		$cardtemplate['per2000_premium']=$_POST['per2000_premium'];
		$cardtemplate['default_value']=$_POST['default_value'];
		$cardtemplate['card_quality']=$_POST['card_quality'];
		$cardtemplate['QuickCard_Price']=$_POST['QuickCard_Price'];
		$cardtemplate['Pic_Upload']=$_POST['Pic_Upload'];
		$cardtemplate['Pic_Width']=$_POST['Pic_Width'];
		$cardtemplate['Pic_Height']=$_POST['Pic_Height'];
		$cardtemplate['2_Sided']=$_POST['2_Sided'];
		$cardtemplate['Paper']=$_POST['Paper'];
		$cardtemplate['Paper_premium']=$_POST['Paper_premium'];
		$cardtemplate['Ink']=$_POST['Ink'];
		$cardtemplate['Vertical']=$_POST['Vertical'];
		$cardtemplate['Allow_PDF']=$_POST['Allow_PDF'];
		$cardtemplate['Printer_Email']=$_POST['Printer_Email'];
		$cardtemplate['Inactive']=$_POST['Inactive'];
		$cardtemplate['Rep']=$_POST['Rep'];
		$cardtemplate['Agent']=$_POST['Agent'];
		$cardtemplate['Approval_Email']=$_POST['Approval_Email'];
		$cardtemplate['Approval_Req']=$_POST['Approval_Req'];
		$cardtemplate['Line_1']=$_POST['Line_1'];
		$cardtemplate['Line_1_Lock']=$_POST['Line_1_Lock'];
		$cardtemplate['Line_2']=$_POST['Line_2'];
		$cardtemplate['Line_2_Lock']=$_POST['Line_2_Lock'];
		$cardtemplate['Line_3']=$_POST['Line_3'];
		$cardtemplate['Line_3_Lock']=$_POST['Line_3_Lock'];
		$cardtemplate['Line_4']=$_POST['Line_4'];
		$cardtemplate['Line_4_Lock']=$_POST['Line_4_Lock'];
		$cardtemplate['Line_5']=$_POST['Line_5'];
		$cardtemplate['Line_5_Lock']=$_POST['Line_5_Lock'];
		$cardtemplate['Line_6']=$_POST['Line_6'];
		$cardtemplate['Line_6_Lock']=$_POST['Line_6_Lock'];
		$cardtemplate['Line_7']=$_POST['Line_7'];
		$cardtemplate['Line_7_Lock']=$_POST['Line_7_Lock'];
		$cardtemplate['Line_8']=$_POST['Line_8'];
		$cardtemplate['Line_8_Lock']=$_POST['Line_8_Lock'];
		$cardtemplate['Line_9']=$_POST['Line_9'];
		$cardtemplate['Line_9_Lock']=$_POST['Line_9_Lock'];
		$cardtemplate['Line_10']=$_POST['Line_10'];
		$cardtemplate['Line_10_Lock']=$_POST['Line_10_Lock'];
		$cardtemplate['Line_11']=$_POST['Line_11'];
		$cardtemplate['Line_11_Lock']=$_POST['Line_11_Lock'];
		$cardtemplate['Line_12']=$_POST['Line_12'];
		$cardtemplate['Line_12_Lock']=$_POST['Line_12_Lock'];
		$cardtemplate['Line_13']=$_POST['Line_13'];
		$cardtemplate['Line_13_Lock']=$_POST['Line_13_Lock'];
		$cardtemplate['Line_13']=$_POST['Line_14'];
		$cardtemplate['Line_13_Lock']=$_POST['Line_14_Lock'];
		$cardtemplate['Line_13']=$_POST['Line_15'];
		$cardtemplate['Line_13_Lock']=$_POST['Line_15_Lock'];
		$cardtemplate['Line_13']=$_POST['Line_16'];
		$cardtemplate['Line_13_Lock']=$_POST['Line_16_Lock'];
		$cardtemplate['Line_13']=$_POST['Line_17'];
		$cardtemplate['Line_13_Lock']=$_POST['Line_17_Lock'];
		$cardtemplate['Line_13']=$_POST['Line_18'];
		$cardtemplate['Line_13_Lock']=$_POST['Line_18_Lock'];
		$cardtemplate['Line_13']=$_POST['Line_19'];
		$cardtemplate['Line_13_Lock']=$_POST['Line_19_Lock'];
		$cardtemplate['Line_13']=$_POST['Line_20'];
		$cardtemplate['Line_13_Lock']=$_POST['Line_20_Lock'];
	}

//	echo "<!--" . $cardtemplate['company_name'] . " || $company_name -->\n";
	//Check to see if this is an overwrite or a new template
	$statement="SELECT * FROM Templates WHERE Template_Name=\"" . str_replace("\"", "&quot;", $cardtemplate['template_name']) . "\"";
	$sql->QueryRow($statement);

	if($_POST['button']!="")
	{
		if($sql->rows>0 && $_POST['button']!="Overwrite")  //Double verify on the Overwrite
		{
			$msg = "A cardtemplate already exists for this company.  Click below to Overwrite.";
		}else
		{
			if($_POST['button']=="Overwrite") //Overwrite has been confirmed, update the existant template
			{
				$statement = "Update Templates SET  Templates.Lines='" . $line_count . "', Printer_Email='" . $cardtemplate['Printer_Email'] . "', Template_Name=\"" . str_replace("\"", "&quot;", $cardtemplate['template_name']) . "\", Company=\"" . $cardtemplate['company'] . "\", Vertical='" . $cardtemplate['Vertical'] . "', per250='" . $cardtemplate['per250'] . "', per500='" . $cardtemplate['per500'] . "', per1000='" . $cardtemplate['per1000'] . "', per2000='" . $cardtemplate['per2000'] . "', per250_premium='" . $cardtemplate['per250_premium'] . "', per500_premium='" . $cardtemplate['per500_premium'] . "', per1000_premium='" . $cardtemplate['per1000_premium'] . "', per2000_premium='" . $cardtemplate['per2000_premium'] . "', QuickCard_Price='" . $cardtemplate['QuickCard_Price'] . "', default_value='" . $cardtemplate['default_value'] . "', card_quality='" . $cardtemplate['card_quality'] . "', Pic_Upload='" . $cardtemplate['Pic_Upload'] . "', Pic_Width='" . $cardtemplate['Pic_Width'] . "', Pic_Height='" . $cardtemplate['Pic_Height'] . "', 2_Sided='" . $cardtemplate['2_Sided'] . "', Paper='" . $cardtemplate['Paper'] . "', Ink='" . $cardtemplate['Ink'] . "', Address1='" . $cardtemplate['address1'] . "', Address2='" . $cardtemplate['address2'] . "', City='" . $cardtemplate['city'] . "', State='" . $cardtemplate['state'] . "', Zip='" . $cardtemplate['zip'] . "', Inactive='" . $cardtemplate['Inactive'] . "', Rep='" . $cardtemplate['Rep'] . "', Agent='" . $cardtemplate['Agent'] . "', Approval_Email=\"" . $cardtemplate['Approval_Email'] . "\"";
				if($cardtemplate['Allow_PDF']=='y')
					$statement .= ", Allow_PDF='y' ";
				else
					$statement .= ", Allow_PDF='n' ";
				if($cardtemplate['Vertical']=='y')
					$statement .= ", Vertical='y'";
				else
					$statement .= ", Vertical='n'";
				if($cardtemplate['Approval_Req']=='y')
					$statement .= ", Approval_Req='y' ";
				else
					$statement .= ", Approval_Req='n' ";
				$statement .= ", Line_1='" . $cardtemplate['Line_1'] . "', Line_1_Lock='";
				if($cardtemplate['Line_1_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_2='" . $cardtemplate['Line_2'] . "', Line_2_Lock='";
				if($cardtemplate['Line_2_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_3='" . $cardtemplate['Line_3'] . "', Line_3_Lock='";
				if($cardtemplate['Line_3_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_4='" . $cardtemplate['Line_4'] . "', Line_4_Lock='";
				if($cardtemplate['Line_4_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_5='" . $cardtemplate['Line_5'] . "', Line_5_Lock='";
				if($cardtemplate['Line_5_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_6='" . $cardtemplate['Line_6'] . "', Line_6_Lock='";
				if($cardtemplate['Line_6_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_7='" . $cardtemplate['Line_7'] . "', Line_7_Lock='";
				if($cardtemplate['Line_7_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_8='" . $cardtemplate['Line_8'] . "', Line_8_Lock='";
				if($cardtemplate['Line_8_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_9='" . $cardtemplate['Line_9'] . "', Line_9_Lock='";
				if($cardtemplate['Line_9_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .="', Line_10='" . $cardtemplate['Line_10'] . "', Line_10_Lock='";
				if($cardtemplate['Line_10_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_11='" . $cardtemplate['Line_11'] . "', Line_11_Lock='";
				if($cardtemplate['Line_11_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_12='" . $cardtemplate['Line_12'] . "', Line_12_Lock='";
				if($cardtemplate['Line_12_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_13='" . $cardtemplate['Line_13'] . "', Line_13_Lock='";
				if($cardtemplate['Line_13_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_14='" . $cardtemplate['Line_14'] . "', Line_14_Lock='";
				if($cardtemplate['Line_14_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_15='" . $cardtemplate['Line_15'] . "', Line_15_Lock='";
				if($cardtemplate['Line_15_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_16='" . $cardtemplate['Line_16'] . "', Line_16_Lock='";
				if($cardtemplate['Line_16_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_17='" . $cardtemplate['Line_17'] . "', Line_17_Lock='";
				if($cardtemplate['Line_17_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_18='" . $cardtemplate['Line_18'] . "', Line_18_Lock='";
				if($cardtemplate['Line_18_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_19='" . $cardtemplate['Line_19'] . "', Line_19_Lock='";
				if($cardtemplate['Line_19_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_20='" . $cardtemplate['Line_20'] . "', Line_20_Lock='";
				if($cardtemplate['Line_20_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .="', ";
				$statement .= " Template='" . str_replace("\"", "", str_replace("'", "", str_replace(".", "", str_replace(" ", "", $cardtemplate['template_name'])))) . "' WHERE Template=\"" . str_replace("\"", "", str_replace("'", "", str_replace(".", "", str_replace(" ", "", $cardtemplate['template_name'])))) . "\"";
				$sql->Update($statement);

				if(isset($_POST['symbol']))
				{
					//$statement = "DELETE FROM Card_Symbols WHERE Template_ID=$template";
					//$sql->Delete($statement);

					foreach($_POST['symbol'] as $a=>$b)
					{
						$statement = "SELECT * FROM Card_Symbols WHERE Functional_Name=\"" . $b . "\" AND Template_ID=$template";
						$sql->Query($statement);
						if($sql->rows<1)
						{
							$statement = "INSERT INTO Card_Symbols SET Functional_Name=\"" . $b . "\", Template_ID=$template";
							$sql->Insert($statement);
						}
					}
				}
				if(isset($_POST['symbol2']))
				{
					foreach($_POST['symbol2'] as $a=>$b)
					{
						$path="images/symbols/$b";
						if ($handle=opendir("$path"))
						{
							while(false !== ($file = readdir($handle)))
							{
								if ($file != "." && $file != "..")
								{
									$statement = "SELECT * FROM Card_Symbols WHERE Functional_Name=\"/$b/" . $file . "\" AND Template_ID=$template";
									$sql->Query($statement);
									if($sql->rows<1)
									{
										$statement = "INSERT INTO Card_Symbols SET Functional_Name=\"/$b/" . $file . "\", Template_ID=$template";
										$sql->Insert($statement);
									}
								}
							}
						}
					}
				}
			}else{ //It's a new template, create a new record
				if($HTTP_POST_VARS['Company_Name']!="")
				{
					$statement="SELECT ID FROM Company WHERE Name=\"" . $_POST['Company_Name'] . "\"";
					$sql->QueryItem($statement);
					echo "<!--Existant ID: " . $sql->data[0] . "-->\n";
					if($sql->data[0]=="")
					{
						$statement="INSERT INTO Company SET Name=\"" . $_POST['Company_Name'] . "\", Address1=\"" . $_POST['address1'] . "\", Address2=\"" . $_POST['address2'] . "\", City=\"" . $_POST['city'] . "\", State=\"" . $_POST['state'] . "\", Zip=\"" . $_POST['zip'] . "\"";
						if($cardtemplate['Approval_Req']=='y')
							$statement .= ", Approval='y' ";
						else
							$statement .= ", Approval='n' ";
						$statement .= ", Approval_Email='" . $cardtemplate['Approval_Email'] . "',  Rep='" . $cardtemplate['Rep'] . "'";
						$sql->Insert($statement);
						$bizforms_company = $statement;
						$statement = "SELECT ID FROM Company WHERE Name=\"" . $_POST['Company_Name'] . "\"";
						$sql->QueryItem($statement);
						$cardtemplate['company']=$sql->data['ID'];
						$cardtemplate['company_name']=$_POST['Company_Name'];
					}else
					{
						$cardtemplate['company']=$sql->data['ID'];
						$cardtemplate['company_name']=$_POST['Company_Name'];
					}
				}
				echo "<!--$statement-->\n";
				$statement="INSERT INTO  Templates SET Templates.lines='" . $line_count . "', Printer_Email='" . $cardtemplate['Printer_Email'] . "', Template_Name=\"" . str_replace("\"", "&quot;", $cardtemplate['template_name']) . "\", Company=\"" . $cardtemplate['company'] . "\", per250='" . $cardtemplate['per250'] . "', per500='" . $cardtemplate['per500'] . "', per1000='" . $cardtemplate['per1000'] . "', per2000='" . $cardtemplate['per2000'] . "', per250_premium='" . $cardtemplate['per250_premium'] . "', per500_premium='" . $cardtemplate['per500_premium'] . "', per1000_premium='" . $cardtemplate['per1000_premium'] . "', per2000_premium='" . $cardtemplate['per2000_premium'] . "', QuickCard_Price='" . $cardtemplate['QuickCard_Price'] . "', default_value='" . $cardtemplate['default_value'] . "', card_quality='" . $cardtemplate['card_quality'] . "', Pic_Upload='" . $cardtemplate['Pic_Upload'] . "', Pic_Width='" . $cardtemplate['Pic_Width'] . "', Pic_Height='" . $cardtemplate['Pic_Height'] . "', 2_Sided='" . $cardtemplate['2_Sided'] . "', Paper='" . $cardtemplate['Paper'] . "', Ink='" . $cardtemplate['Ink'] . "', Address1='" . $cardtemplate['address1'] . "', Address2='" . $cardtemplate['address2'] . "', City='" . $cardtemplate['city'] . "', State='" . $cardtemplate['state'] . "', Zip='" . $cardtemplate['zip'] . "', Inactive='" . $cardtemplate['Inactive'] . "', Rep='" . $cardtemplate['Rep'] . "', Agent='" . $cardtemplate['Agent'] . "', Approval_Email=\"" . $cardtemplate['Approval_Email'] . "\"";
				if($cardtemplate['Allow_PDF']=='y')
					$statement .= ", Allow_PDF='y' ";
				else
					$statement .= ", Allow_PDF='n' ";
				if($cardtemplate['Vertical']=='y')
					$statement .= ", Vertical='y' ";
				else
					$statement .= ", Vertical='n' ";
				if($cardtemplate['Approval_Req']=='y')
					$statement .= ", Approval_Req='y' ";
				else
					$statement .= ", Approval_Req='n' ";
				$statement .= ", Line_1='" . $cardtemplate['Line_1'] . "', Line_1_Lock='";
				if($cardtemplate['Line_1_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_2='" . $cardtemplate['Line_2'] . "', Line_2_Lock='";
				if($cardtemplate['Line_2_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_3='" . $cardtemplate['Line_3'] . "', Line_3_Lock='";
				if($cardtemplate['Line_3_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_4='" . $cardtemplate['Line_4'] . "', Line_4_Lock='";
				if($cardtemplate['Line_4_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_5='" . $cardtemplate['Line_5'] . "', Line_5_Lock='";
				if($cardtemplate['Line_5_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_6='" . $cardtemplate['Line_6'] . "', Line_6_Lock='";
				if($cardtemplate['Line_6_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_7='" . $cardtemplate['Line_7'] . "', Line_7_Lock='";
				if($cardtemplate['Line_7_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_8='" . $cardtemplate['Line_8'] . "', Line_8_Lock='";
				if($cardtemplate['Line_8_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_9='" . $cardtemplate['Line_9'] . "', Line_9_Lock='";
				if($cardtemplate['Line_9_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .="', Line_10='" . $cardtemplate['Line_10'] . "', Line_10_Lock='";
				if($cardtemplate['Line_10_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_11='" . $cardtemplate['Line_11'] . "', Line_11_Lock='";
				if($cardtemplate['Line_11_Lock']=='y')
					$statement .= 'y';
				else
					$statement .= 'n';
				$statement .= "', Line_12='" . $cardtemplate['Line_12'] . "', Line_12_Lock='";
				if($cardtemplate['Line_12_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_13='" . $cardtemplate['Line_13'] . "', Line_13_Lock='";
				if($cardtemplate['Line_13_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_14='" . $cardtemplate['Line_14'] . "', Line_14_Lock='";
				if($cardtemplate['Line_14_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_15='" . $cardtemplate['Line_15'] . "', Line_15_Lock='";
				if($cardtemplate['Line_15_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_16='" . $cardtemplate['Line_16'] . "', Line_16_Lock='";
				if($cardtemplate['Line_16_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_17='" . $cardtemplate['Line_17'] . "', Line_17_Lock='";
				if($cardtemplate['Line_17_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_18='" . $cardtemplate['Line_18'] . "', Line_18_Lock='";
				if($cardtemplate['Line_18_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_19='" . $cardtemplate['Line_19'] . "', Line_19_Lock='";
				if($cardtemplate['Line_19_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .= "', Line_20='" . $cardtemplate['Line_20'] . "', Line_20_Lock='";
				if($cardtemplate['Line_20_Lock']=='y')
					$statement .='y';
				else
					$statement .= 'n';
				$statement .="', ";
				$statement .= " Template='" . str_replace("\"", "", str_replace("'", "", str_replace(".", "", str_replace(" ", "", $cardtemplate['template_name'])))) . "'";
				$sql->Insert($statement);
				echo "<!-- $statement -->\n";

				if(isset($_POST['symbol']))
				{
					//$statement = "DELETE FROM Card_Symbols WHERE Template_ID=$template";
					//$sql->Delete($statement);

					foreach($_POST['symbol'] as $a=>$b)
					{
						$statement = "SELECT * FROM Card_Symbols WHERE Functional_Name=\"" . $b . "\" AND Template_ID=$template";
						$sql->Query($statement);
						if($sql->rows<1)
						{
							$statement = "INSERT INTO Card_Symbols SET Functional_Name=\"" . $b . "\", Template_ID=$template";
							$sql->Insert($statement);
						}
					}
				}
				if(isset($_POST['symbol2']))
				{
					foreach($_POST['symbol2'] as $a=>$b)
					{
						$path="images/symbols/$b";
						if ($handle=opendir("$path"))
						{
							while(false !== ($file = readdir($handle)))
							{
								if ($file != "." && $file != "..")
								{
									$statement = "SELECT * FROM Card_Symbols WHERE Functional_Name=\"/$b/" . $file . "\" AND Template_ID=$template";
									$sql->Query($statement);
									if($sql->rows<1)
									{
										$statement = "INSERT INTO Card_Symbols SET Functional_Name=\"/$b/" . $file . "\", Template_ID=$template";
										$sql->Insert($statement);
									}
								}
							}
						}
					}
				}

				$sql->Create("bizcardstodaynew");
				if($bizforms_company!="")
					$sql->Insert($bizforms_company);

				$statement="SELECT ID FROM Templates WHERE Template='" . str_replace("\"", "", str_replace("'", "", str_replace(".", "", str_replace(" ", "", $cardtemplate['template_name'])))) . "'";
				$sql->Create('bizcardstoday');
				$sql->QueryItem($statement);
				$msg ="Template Created (# " . $sql->data[0] . ")";

				$statement = "SELECT * FROM Card_Symbols WHERE Template_ID=$template";
				$sql->Query($statement);
				$j=0;
				while($j<$sql->rows)
				{
					$sql->Fetch($j);
					if(substr($sql->data['Functional_Name'],0,1)=="/")
					{
						$temp = explode("/", $sql->data['Functional_Name']);
						$usable_groups[]=$temp[1];
					}else
						$usable_symbols[]=$sql->data['Functional_Name'];
					$j++;
				}
			}
		}
	}
	echo "<!--$statement-->\n";
?>
<html>
	<head>
		<title>BizCardsToday Template Creator</title>
		<link rel="stylesheet" href="bizcard.css" type="text/css">
		<script language=javascript>
		<!--
		//This batch of functions allows the whole form/page to be submitted to the appropriate handler dependant on which button is pressed.
		function OnButton1()
		{	//Redraw Card
			document.Form1.action = "template_create.php"
			document.Form1.submit();
			return true;
		}

		function OnButton2()
		{	//Upload svg
			document.Form1.action="template_loader.php"
			document.Form1.enctype="multipart/form-data"
			document.Form1.submit();
			return true;
		}

		function OnButton3()
		{	//Return to Menu
			document.Form1.action = "welcome.php"
			document.Form1.submit();
			return true;
		}
		-->
		</script>

	</head>

	<body>
	<?php echo "<p><font color=red size=+2>$msg</font></p>\n"; ?>
	<form action="template_create.php" method=post name=Form1 enctype="multipart/form-data">
	<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<th colspan=2>Default Shipping Information</th>
		</tr><tr>
			<td>Template Name:</td>
			<td><input type=text value="<?php echo stripslashes(str_replace("&quot;", "\"", $cardtemplate['template_name'])); ?>" name=Template_Name></td>
		</tr><tr>
			<td>Company:</td>
			<td><input type=text value="<?php echo stripslashes(str_replace("&quot;", "\"", $cardtemplate['company_name'])); ?>" name=Company_Name> or <select name=Old_Company size=1>
					<option value="">Already Defined Company</option>
				<?php
					$statement="SELECT Name From Company Order By Name";
					$sql->Query($statement);
					$j=0;
					while($j<$sql->rows)
					{
						$sql->Fetch($j);
						echo "<option value=\"" . $sql->data[0] . "\"";
						if($cardtemplate['company_name']==$sql->data[0])
							echo " selected";
						echo ">" . $sql->data[0] . "</option>\n";
						$j++;
					}
				?>
			</td>
		</tr><tr>
			<td>Address #1:</td>
			<td><input type=text value="<?php echo stripslashes(str_replace("&quot;", "\"", $cardtemplate['address1'])); ?>" name=address1></td>
		</tr><tr>
			<td>Address #2:</td>
			<td><input type=text value="<?php echo stripslashes(str_replace("&quot;", "\"", $cardtemplate['address2'])); ?>" name=address2></td>
		</tr><tr>
			<td colspan=2>
				<table border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td>City: <input type=text value="<?php echo stripslashes(str_replace("&quot;", "\"", $cardtemplate['city'])); ?>" name=city></td>
						<td>State: <input type=text size=3 maxlength=2 value='<?php echo $cardtemplate['state']; ?>' name=state></td>
						<td>Zip: <input type=text size=10 maxlength=10 value='<?php echo $cardtemplate['zip']; ?>' name=zip></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan=2><br>
				<table border=0 cellspacing=0 cellpadding=0>
					<tr>
						<th>Price Per</th>
						<th>Standard</th>
						<th>Premium</th>
						<th>Default Amount</th>
					</tr>
					<tr>
						<td>250</td>
						<td align=center><input type=text size=10 value='<?php echo $cardtemplate['per250']; ?>' name=per250></td>
						<td align=center><input type=text size=10 value='<?php echo $cardtemplate['per250_premium']; ?>' name=per250_premium></td>
						<td align=center><input type=radio name=default_value value=250 <?php if($cardtemplate['default_value']=="250") echo "checked"; ?>></td>
					</tr>
					<tr>
						<td>500</td>
						<td align=center><input type=text size=10 value='<?php if($cardtemplate['per500']!=0 && $cardtemplate['per500']!="") echo $cardtemplate['per500']; else echo "34.97";?>' name=per500></td>
						<td align=center><input type=text size=10 value='<?php if($cardtemplate['per500_premium']!=0 && $cardtemplate['per500_premium']!="") echo $cardtemplate['per500_premium']; else echo "49.95";?>' name=per500_premium></td>
						<td align=center><input type=radio name=default_value value=500 <?php if($cardtemplate['default_value']=="500") echo "checked"; ?>></td>
					</tr>
					<tr>
						<td>1000</td>
						<td align=center><input type=text size=10 value='<?php if($cardtemplate['per1000']!=0 && $cardtemplate['per1000']!="") echo $cardtemplate['per1000']; else echo "49.97"; ?>' name=per1000></td>
						<td align=center><input type=text size=10 value='<?php if($cardtemplate['per1000_premium']!=0 && $cardtemplate['per1000_premium']!="") echo $cardtemplate['per1000_premium']; else echo "69.95"; ?>' name=per1000_premium></td>
						<td align=center><input type=radio name=default_value value=1000 <?php if($cardtemplate['default_value']=="1000" || $row['default_value']=="") echo "checked"; ?>></td>
					</tr>
					<tr>
						<td>2000</td>
						<td align=center><input type=text size=10 value='<?php if($cardtemplate['per2000']!=0 && $cardtemplate['per2000']!="") echo $cardtemplate['per2000']; else echo "89.97"; ?>' name=per2000></td>
						<td align=center><input type=text size=10 value='<?php if($cardtemplate['per2000_premium']!=0 && $cardtemplate['per2000_premium']!="") echo $cardtemplate['per2000_premium']; else echo "99.95"; ?>' name=per2000_premium></td>
						<td align=center><input type=radio name=default_value value=2000 <?php if($cardtemplate['default_value']=="2000") echo "checked"; ?>></td>
					</tr>
					<TR>
						<td colspan=5 bgcolor=#B0C4DE>Standard Shipping Cost:$<input type=text name=sship size=4 maxlength=4 value=8>&nbsp;&nbsp;&nbsp;Premium Shipping Charge:$<input typ=text name=pship size=4 maxlength=4 value=10></td>
					</tr>
					<TR>
						<td colspan=5 bgcolor=#B0C4DE>Type of Shipping: <select name=typeship>
						<option>Pickup
						<option>Standard
						<option>UPS
						</select>
						</td>
					</tr>
					<tr>
						<td colspan=5>Rush Charges: <input type=text size=10 value='<?php if($cardtemplate['QuickCard_Price']!=0 && $cardtemplate['QuickCard_Price']!="") echo $cardtemplate['QuickCard_Price']; else echo "20"; ?>' name=QuickCard_Price></td>
					</tr>
					<tr>
						<td colspan=5>Default Card Quality: <input type=radio name=card_quality value='e' <?php if($cardtemplate['card_quality']=='e' || $cardtemplate['card_quality']=="") echo "checked"; ?>>Selection <input type=radio name=card_quality value='s' <?php if($cardtemplate['card_quality']=='s') echo "checked"; ?>>Standard Only  <input type=radio name=card_quality value='p' <?php if($cardtemplate['card_quality']=='p') echo "checked"; ?>>Premium Only</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan=2><br>
				<table border=0 cellspacing=0 cellpadding=0>
					<tr>
						<th align=center colspan=2>Card Setup</th>
					</tr><tr>
						<td>Photo Card</td>
						<td align=center><input type=checkbox name=Pic_Upload value='y' <?php if($cardtemplate['Pic_Upload']=='y') echo "checked"; ?>></td>
					</tr><tr>
						<td>Photo Width (in pixels)</td>
						<td><input type=text size=15 name=Pic_Width value='<?php echo $cardtemplate['Pic_Width']; ?>'></td>
					</tr><tr>
						<td>Photo Height (in pixels)</td>
						<td><input type=text size=15 name=Pic_Height value='<?php echo $cardtemplate['Pic_Height']; ?>'></td>
					</tr><tr>
						<td>2 sided Card</td>
						<td align=center><input type=checkbox name=2_Sided value='y' <?php if($cardtemplate['2_Sided']=='y') echo "checked"; ?>></td>
					</tr><tr>
						<td>Vertical Card</td>
						<td align=center><input type=checkbox name=Vertical value='y' <?php if($cardtemplate['Vertical']=='y') echo "checked"; ?>></td>
					</tr><tr>
						<td>Allow PDF Upload</td>
						<td align=center><input type=checkbox name=Allow_PDF value='y' <?php if($cardtemplate['Allow_PDF']=='y') echo "checked"; ?>></td>
					</tr><tr>
						<td>Paper</td>
						<td><input type=text size=15 name=Paper value='<?php if($cardtemplate['Paper']=="") echo "10 pt. coated"; else echo $cardtemplate['Paper']; ?>'></td>
					</tr><tr>
						<td>Premium Paper</td>
						<td><input type=text size=15 name=Paper_premium value='<?php if($cardtemplate['Paper_premium']=="") echo "14 pt. coated"; else echo $cardtemplate['Paper_premium']; ?>'></td>
					</tr><tr>
						<td>Ink</td>
						<td><input type=text size=15 name=Ink value='<?php if($cardtemplate['Ink']=="") echo "4/0"; else echo $cardtemplate['Ink']; ?>'></td>
					</tr><tr>
						<td>Printer Email</td>
						<td><input type=text size=15 name=Printer_Email value='<?php echo $row['Printer_Email']; ?>'></td>
					</tr><tr>
						<td>Card Status</td>
						<td><table border=0 cellspacing=2 cellpadding=0>
								<tr>
									<td><input type=radio name=Inactive value='i' <?php if($cardtemplate['Inactive']=='i') echo "checked"; ?>>Inactive</td>
									<td><input type=radio name=Inactive value='a' <?php if($cardtemplate['Inactive']=='a') echo "checked"; ?>>Active</td>
									<td><input type=radio name=Inactive value='p' <?php if($cardtemplate['Inactive']=='p' || $cardtemplate['Inactive']=="") echo "checked"; ?>>Prospective</td>
								</tr>
						</table></td>
					</tr>
				</table>
			</td>
		</tr><tr>
			<?php
				if($dest_file!="") //If a file has been uploaded it will be stored in $dest_file, and a linecount will have been generated.  This little block allows default values for the first card to be loaded.
				{
					//This section was added on 6/29/05.  Prevents optional symbol selection from showing until after the SVG has been uploaded.
					echo "<tr>
					<td colspan=3>
						<table border=1 cellspacing=3 cellpadding=3>
							<tr>
								<th colspan=5>Optional Symbols allowed on this card:</th>";

									$path="images/symbols";
									if ($handle=opendir("$path"))
									{
										//$i=5;
										while(false !== ($file = readdir($handle)))
										{
											if ($file != "." && $file != "..")
											{
												echo "<!--$file-->\n";
												if(is_file($path . "/" . $file))
												{
													$line = "\t\t\t\t\t\t\t\t<td><input type=checkbox name=symbol[]";
													if(count($usable_symbols)>0)
													{
														foreach($usable_symbols as $a=>$b)
														{
															if(str_replace(" ", "_", $file)==$b)
																$line .= " checked";
														}
													}
													$line .= " value=\"" . str_replace(" ", "_", $file) . "\"><img src=\"$path/$file\" border=0></td>\n";
													$symbol_output[]=$line;
												}else
													$directory[]=str_replace(" ", "_", $file);
											}

										}
										$i=5;
										$j=0;
										while ($j<count($directory))
										{
											if($i==5)
											{
												$i=0;
												echo "\t\t\t\t\t\t</tr><tr>\n";
											};
											echo "\t\t\t\t\t\t\t<td><input type=checkbox name=symbol2[]";
											if(count($usable_groups)>0)
											{
												foreach($usable_groups as $a=>$b)
												{
													//echo "<!--" . $directory[$j] . " || $b-->\n";
													if(str_replace(" ", "_", $directory[$j])==$b)
														echo " checked";
												}
											}
											echo " value=\"" . str_replace(" ", "_", $directory[$j]) . "\">&nbsp;" . $directory[$j] . "</td>\n";
											$j++;
											$i++;
										}
										$i=5;
										$j=0;
										while($j<count($symbol_output))
										{
											if($i==5)
											{
												$i=0;
												echo "\t\t\t\t\t\t</tr><tr>\n";
											};
											echo $symbol_output[$j];
											$j++;
											$i++;
										}
									}
							echo "</tr>
						</table>
					</td>
				</tr>
				<tr>
				<td colspan=3><br><br>
					<table border=1 cellspacing=3 cellpadding=3>
						<tr>
							<th>Purchase Approval Email</th>
							<th>Purchase Approval Required</th>
						</tr>
						<tr>
							<td align=center><input type=text name=Approval_Email value='" . $cardtemplate['Approval_Email'] . "'></td>
							<td align=center><input type=checkbox name=Approval_Req value='y'";
							if($cardtemplate['Approval_Req']=='y')
								echo " checked";
				echo "></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan=3>Rep: <select name=Rep size=1>\n";
						$statement="SELECT Rep_Code, Name FROM Users WHERE Rep_Code<>\"\" AND Special_Type='Rep' Order by Name";
						$sql->Query($statement);
						$j=0;
						if($cardtemplate['Rep']=="")
							$cardtemplate['Rep']="H";
						while($j<$sql->rows)
						{
							$sql->Fetch($j);
							echo "\t\t\t\t\t<option value='" . $sql->data["Rep_Code"] . "' ";
							if($cardtemplate['Rep']==$sql->data['Rep_Code'])
								echo "selected";
							echo ">" . $sql->data['Name'] . " {" . $sql->data['Rep_Code'] . "}</option>\n";
							$j++;
						}
						echo "</select>\n";

				echo "&nbsp;&nbsp;&nbsp;&nbsp;Agent: <select name=Agent size=1>
					<option value='None'>None</option>\n";
						$statement="SELECT Rep_Code, Name FROM Users WHERE Rep_Code<>\"\" AND Special_Type='Agent' Order by Name";
						$sql->Query($statement);
						$j=0;
						while($j<$sql->rows)
						{
							$sql->Fetch($j);
							echo "\t\t\t\t\t<option value='" . $sql->data["Rep_Code"] . "' ";
							if($cardtemplate['Agent']==$sql->data['Rep_Code'])
								echo "selected";
							echo ">" . $sql->data['Name'] . "</option>\n";
							$j++;
						}
						echo "</select>\n
				</td>
			<tr>\n";

					echo "\t\t<td align=center colspan=2><table border=0 cellspacing=0 cellpadding=0>\n";
					echo "\t\t<tr>\n";
					echo "\t\t\t<th align=center>Line</th>\n";
					echo "\t\t\t<th align=center>Default Value</th>\n";
					echo "\t\t\t<th align=center>Locked</th>\n";
					echo "\t\t</tr>\n";
					$j=1;
					while($j<=$line_count)
					{
						echo "\t\t<tr>\n\t\t\t<td>Line $j: </td>\n\t\t\t\t<td align=center><input type=text value='" . $cardtemplate["Line_" . $j] . "' name=Line_$j size=35></td>\n\t\t\t\t<td align=center><input type=checkbox value='y' name=Line_${j}_Lock ";
						if($cardtemplate["Line_" . $j . "_Lock"]=='y')
							echo "checked";
						echo "></td>\n\t\t</tr>\n";
						$j++;
					}
					if($cardtemplate['Vertical']=='y') //optimize page space
						$dimension = "height=498 width=294";
					else
						$dimension = "height=294 width=498";
					echo "\t\t<tr>\n\t\t\t<td colspan=3><embed src=$dest_file $dimension type=\"image/svg+xml\"></td>\n</tr><tr>\n";
				}
				echo "<td colspan=2>SVG to upload:<br><input type=file name=photo size=30><input type='hidden' name='submitted' value='true'><input type='submit' name='button' onclick='return OnButton2();' value='Upload SVG' ></td>\n";
			?>
		</tr><tr>
			<td colspan=2><input type='submit' name='button' onclick='return OnButton1();' value='<?php if($msg=="") echo "Create Template"; else echo "Overwrite"; ?>'>&nbsp;&nbsp;&nbsp;<a href='welcome.php'>Return to Menu</a></td>
		</tr>
		</table>
		</form>
	</body>
</html>
