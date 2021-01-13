<?php
include 'ChromePhp.php';

require('firelogger/firelogger.php');
$debug = 1;
flog('test');
//ChromePhp::log('test');

/*  This page will allow the creation of a new Template record. */
session_start();
$template = $_SESSION['template'];

if($_SESSION['admin'] != "y")  //Boot if access not allowed
header("Location: index.html");
flog('session', $_SESSION);


// if(!session_is_registered("per250"))  //Session variables to allow data retention on pic upload
if(!isset($_SESSION["per250"]))  //Session variables to allow data retention on pic upload
{
//    session_register("company");
//    session_register("company_name");
//    session_register("template_name");
//    session_register("address1");
//    session_register("address2");
//    session_register("city");
//    session_register("state");
//    session_register("zip");
//    session_register("line_count");
//    session_register("per250");
//    session_register("per500");
//    session_register("per1000");
//    session_register("per2000");
//    session_register("per250_premium");
//    session_register("per500_premium");
//    session_register("per1000_premium");
//    session_register("per2000_premium");
//    session_register("quickcard_price");
//    session_register("card_quality");
//    session_register("pic_upload");
//    session_register("two_sided");
//    session_register("paper");
//    session_register("paper_premium");
//    session_register("ink");
//    session_register("vertical");
//    session_register("allow_pdf");
//    session_register("approval_req");
//    session_register("approval_email");
//    session_register("approval_phone");
//    session_register("approval_name");
//    session_register("printer_email");
//    session_register("pic_width");
//    session_register("pic_height");

   $_SESSION['company'] = '';
   $_SESSION['company_name'] = '';
   $_SESSION['template_name'] = '';
   $_SESSION['address1'] = '';
   $_SESSION['address2'] = '';
   $_SESSION['city'] = '';
   $_SESSION['state'] = '';
   $_SESSION['zip'] = '';
   $_SESSION['line_count'] = '';
   $_SESSION['per250'] = '';
   $_SESSION['per500'] = '';
   $_SESSION['per1000'] = '';
   $_SESSION['per2000'] = '';
   $_SESSION['per250_premium'] = '';
   $_SESSION['per500_premium'] = '';
   $_SESSION['per1000_premium'] = '';
   $_SESSION['per2000_premium'] = '';
   $_SESSION['quickcard_price'] = '';
   $_SESSION['card_quality'] = '';
   $_SESSION['pic_upload'] = '';
   $_SESSION['two_sided'] = '';
   $_SESSION['paper'] = '';
   $_SESSION['paper_premium'] = '';
   $_SESSION['ink'] = '';
   $_SESSION['vertical'] = '';
   $_SESSION['allow_pdf'] = '';
   $_SESSION['approval_req'] = '';
   $_SESSION['approval_email'] = '';
   $_SESSION['approval_phone'] = '';
   $_SESSION['approval_name'] = '';
   $_SESSION['printer_email'] = '';
   $_SESSION['pic_width'] = '';
   $_SESSION['pic_height'] = '';
}
require("util.php"); //db wrapper
$sql = new MySQL_class;
$sql->Create("bizcardstodaynew");

$cardtemplate=array();
if($_POST['button']=="") $Company="";

//This section will load all of the session values into the $cardtemplate array for display
if($_SESSION['approval_req'] != "")
   $cardtemplate['approval_req'] = $_SESSION['approval_req'];
if($_SESSION['Approval_Email']!="")
   $cardtemplate['approval_email']=$_SESSION['approval_email'];
if($_SESSION['approval_phone']!="")
   $cardtemplate['approval_phone']=$_SESSION['approval_phone'];
if($_SESSION['approval_name']!="")
   $cardtemplate['approval_name']=$_SESSION['approval_name'];
if($_SESSION['per250']!="")
   $cardtemplate['per250']=$_SESSION['per250'];
if($_SESSION['per500']!="")
   $cardtemplate['per500']=$_SESSION['per500'];
if($_SESSION['per1000']!="")
   $cardtemplate['per1000']=$_SESSION['per1000'];
if($_SESSION['per2000']!="")
   $cardtemplate['per2000']=$_SESSION['per2000'];
if($_SESSION['per250_premium']!="")
   $cardtemplate['per250_premium']=$_SESSION['per250_premium'];
if($_SESSION['per500']!="")
   $cardtemplate['per500_premium']=$_SESSION['per500_premium'];
if($_SESSION['per1000']!="")
   $cardtemplate['per1000_premium']=$_SESSION['per1000_premium'];
if($_SESSION['per2000']!="")
   $cardtemplate['per2000_premium']=$_SESSION['per2000_premium'];
if($_SESSION['quickcard_price']!="")
   $cardtemplate['quickcard_price']=$_SESSION['quickcard_price'];
if($_SESSION['card_quality']!="")
   $cardtemplate['card_quality']=$_SESSION['card_quality'];
if($_SESSION['pic_upload']!="")
   $cardtemplate['pic_upload']=$_SESSION['pic_upload'];
if($_SESSION['pic_width']!="")
   $cardtemplate['pic_width']=$_SESSION['pic_width'];
if($_SESSION['pic_height']!="")
   $cardtemplate['pic_height']=$_SESSION['pic_height'];
if($_SESSION['two_sided']!="")
   $cardtemplate['2_sided']=$_SESSION['two_sided'];
if($_SESSION['paper']!="")
$cardtemplate['paper']=$_SESSION['paper'];
if($_SESSION['paper_premium']!="")
   $cardtemplate['paper_premium']=$_SESSION['paper_premium'];
if($_SESSION['ink']!="")
   $cardtemplate['ink']=$_SESSION['ink'];
if($_SESSION['vertical']!="")
   $cardtemplate['vertical']=$_SESSION['vertical'];
if($_SESSION['allow_pdf']!="")
   $cardtemplate['allow_pdf']=$_SESSION['allow_pdf'];
if($_SESSION['company_name']!="")
{
   $sql->QueryItem("SELECT Name, ID FROM Company WHERE Name=\"$company_name\"");
   if($sql->rows>0)
   {
	 $cardtemplate['company_name']=$sql->data[0];
	 $cardtemplate['company']=$sql->data[1];
   }else $cardtemplate['company_name'] = $_SESSION['company_name'];
}
if($_SESSION['template_name']!="")
   $cardtemplate['template_name']=$_SESSION['template_name'];
if($_SESSION['address1']!="")
   $cardtemplate['address1']=$_SESSION['address1'];
if($_SESSION['address2']!="")
   $cardtemplate['address2']=$_SESSION['address2'];
if($_SESSION['city']!="")
   $cardtemplate['city']=$_SESSION['city'];
if($_SESSION['state']!="")
   $cardtemplate['state']=$_SESSION['state'];
if($_SESSION['zip']!="")
   $cardtemplate['zip']=$_SESSION['zip'];
if($_SESSION['printer_email']!="")
   $cardtemplate['printer_email']=$_SESSION['printer_email'];

if(isset($_POST['template_name'])) //This will load a submitted template (just created) for display.
{

//ChromePhp::log('template_name set');
   if($_POST['company_name'] == "" && $_POST['old_company']!="")
	 $_POST['company_name'] = $_POST['old_company'];

   $sql->QueryItem("SELECT Name, ID FROM Company WHERE Name=\"" . $_POST['company_name'] . "\"");
   if($sql->rowsAffected() > 0)
   {
	 $cardtemplate['company_name'] = $sql->data[0];
	 $cardtemplate['company'] = $sql->data[1];
   }else
   {
	 $cardtemplate['company_name'] = $_POST['company_name'];
	 $cardtemplate['company'] = $_POST['company_name'];
   }

   $cardtemplate['template_name'] = $_POST['template_name'];
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
   $cardtemplate['quickcard_price']=$_POST['quickcard_price'];
   $cardtemplate['pic_upload']=$_POST['pic_upload'];
   $cardtemplate['pic_width']=$_POST['pic_width'];
   $cardtemplate['pic_height']=$_POST['pic_height'];
   $cardtemplate['2_sided']=$_POST['2_sided'];
   $cardtemplate['paper']=$_POST['paper'];
   $cardtemplate['paper_premium']=$_POST['paper_premium'];
   $cardtemplate['ink']=$_POST['ink'];
   $cardtemplate['vertical']=$_POST['vertical'];
   $cardtemplate['allow_pdf']=$_POST['allow_pdf'];
   $cardtemplate['printer_email']=$_POST['printer_email'];
   $cardtemplate['inactive']=$_POST['inactive'];
   $cardtemplate['rep']=$_POST['Rep'];
   $cardtemplate['agent']=$_POST['agent'];
   $cardtemplate['approval_email']=$_POST['approval_email'];
   $cardtemplate['approval_req']=$_POST['approval_req'];
   $cardtemplate['line_1']=$_POST['line_1'];
   $cardtemplate['line_1_lock']=$_POST['line_1_lock'];
   $cardtemplate['line_2']=$_POST['line_2'];
   $cardtemplate['line_2_lock']=$_POST['line_2_lock'];
   $cardtemplate['line_3']=$_POST['line_3'];
   $cardtemplate['line_3_lock']=$_POST['line_3_lock'];
   $cardtemplate['line_4']=$_POST['line_4'];
   $cardtemplate['line_4_lock']=$_POST['line_4_lock'];
   $cardtemplate['line_5']=$_POST['line_5'];
   $cardtemplate['line_5_lock']=$_POST['line_5_lock'];
   $cardtemplate['line_6']=$_POST['line_6'];
   $cardtemplate['line_6_lock']=$_POST['line_6_lock'];
   $cardtemplate['line_7']=$_POST['line_7'];
   $cardtemplate['line_7_lock']=$_POST['line_7_lock'];
   $cardtemplate['line_8']=$_POST['line_8'];
   $cardtemplate['line_8_lock']=$_POST['line_8_lock'];
   $cardtemplate['line_9']=$_POST['line_9'];
   $cardtemplate['line_9_lock']=$_POST['line_9_lock'];
   $cardtemplate['line_10']=$_POST['line_10'];
   $cardtemplate['line_10_lock']=$_POST['line_10_lock'];
   $cardtemplate['line_11']=$_POST['line_11'];
   $cardtemplate['line_11_lock']=$_POST['line_11_lock'];
   $cardtemplate['line_12']=$_POST['line_12'];
   $cardtemplate['line_12_lock']=$_POST['line_12_lock'];
   $cardtemplate['line_13']=$_POST['line_13'];
   $cardtemplate['line_13_lock']=$_POST['line_13_lock'];
   $cardtemplate['line_13']=$_POST['line_14'];
   $cardtemplate['line_13_lock']=$_POST['line_14_lock'];
   $cardtemplate['line_13']=$_POST['line_15'];
   $cardtemplate['line_13_lock']=$_POST['line_15_lock'];
   $cardtemplate['line_13']=$_POST['line_16'];
   $cardtemplate['line_13_lock']=$_POST['line_16_lock'];
   $cardtemplate['line_13']=$_POST['line_17'];
   $cardtemplate['line_13_lock']=$_POST['line_17_lock'];
   $cardtemplate['line_13']=$_POST['line_18'];
   $cardtemplate['line_13_lock']=$_POST['line_18_lock'];
   $cardtemplate['line_13']=$_POST['line_19'];
   $cardtemplate['line_13_lock']=$_POST['line_19_lock'];
   $cardtemplate['line_13']=$_POST['line_20'];
   $cardtemplate['line_13_lock']=$_POST['line_20_lock'];

flog('cardtemplate', $cardtemplate);

}

//	echo "<!--" . $cardtemplate['company_name'] . " || $company_name -->\n";
//Check to see if this is an overwrite or a new template
$statement="SELECT * FROM Templates WHERE template_name=\"" .
	str_replace("\"", "&quot;", $cardtemplate['template_name']) . "\"";
$sql->QueryRow($statement);

if($_POST['tmplAction'] != "")
{
	if(isset($_SESSION['temp_lineCnt']))
	{
		$line_count = $_SESSION['temp_lineCnt'];
	}
	
//ChromePhp::log('button not blank');
   if($sql->rows>0 && $_POST['tmplAction'] !="Overwrite")  //Double verify on the Overwrite$_POST['button']
   {
		$msg = "A cardtemplate already exists for this company.  Click below to Overwrite.";
   }else
   {
		if($_POST['tmplAction']=="overwrite") //Overwrite has been confirmed, update the existant template
		{
			$statement = "Update Templates SET  Templates.Lines='" . $line_count .
			 "', Printer_Email='" . $cardtemplate['printer_email'] . "', Template_Name=\"" .
			 str_replace("\"", "&quot;", $cardtemplate['template_name']) . "\", Company=\"" .
			 $cardtemplate['company'] . "\", Vertical='" . $cardtemplate['vertical'] .
			 "', per250='" . $cardtemplate['per250'] . "', per500='" . $cardtemplate['per500'] .
			 "', per1000='" . $cardtemplate['per1000'] . "', per2000='" .
			 $cardtemplate['per2000'] . "', per250_premium='" . $cardtemplate['per250_premium'] .
			 "', per500_premium='" . $cardtemplate['per500_premium'] . "', per1000_premium='" .
			 $cardtemplate['per1000_premium'] . "', per2000_premium='" .
			 $cardtemplate['per2000_premium'] . "', QuickCard_Price='" .
			 $cardtemplate['quickcard_price'] . "', default_value='" .
			 $cardtemplate['default_value'] . "', card_quality='" .
			 $cardtemplate['card_quality'] . "', Pic_Upload='" . $cardtemplate['pic_upload'] .
			 "', Pic_Width='" . $cardtemplate['pic_width'] . "', Pic_Height='" .
			 $cardtemplate['pic_height'] . "', 2_Sided='" . $cardtemplate['2_sided'] .
			 "', Paper='" . $cardtemplate['paper'] . "', Ink='" . $cardtemplate['ink'] .
			 "', Address1='" . $cardtemplate['address1'] . "', Address2='" .
			 $cardtemplate['address2'] . "', City='" . $cardtemplate['city'] . "', State='" .
			 $cardtemplate['state'] . "', Zip='" . $cardtemplate['zip'] . "', Inactive='" .
			 $cardtemplate['inactive'] . "', Rep='" . $cardtemplate['rep'] . "', Agent='" .
			 $cardtemplate['agent'] . "', Approval_Email=\"" . $cardtemplate['approval_email'] .
			 "\"";
//			unset($_SESSION['temp_lineCnt']);

			if($cardtemplate['allow_pdf']=='y')
				$statement .= ", Allow_PDF='y' ";
			else
				$statement .= ", Allow_PDF='n' ";
			if($cardtemplate['vertical']=='y')
				$statement .= ", Vertical='y'";
			else
				$statement .= ", Vertical='n'";
			if($cardtemplate['approval_req']=='y')
				$statement .= ", Approval_Req='y' ";
			else
				$statement .= ", Approval_Req='n' ";
			$statement .= ", Line_1='" . $cardtemplate['line_1'] . "', Line_1_Lock='";
			if($cardtemplate['line_1_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_2='" . $cardtemplate['line_2'] . "', Line_2_Lock='";
			if($cardtemplate['line_2_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_3='" . $cardtemplate['line_3'] . "', Line_3_Lock='";
			if($cardtemplate['line_3_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_4='" . $cardtemplate['line_4'] . "', Line_4_Lock='";
			if($cardtemplate['line_4_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_5='" . $cardtemplate['line_5'] . "', Line_5_Lock='";
			if($cardtemplate['line_5_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_6='" . $cardtemplate['line_6'] . "', Line_6_Lock='";
			if($cardtemplate['line_6_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_7='" . $cardtemplate['line_7'] . "', Line_7_Lock='";
			if($cardtemplate['line_7_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_8='" . $cardtemplate['line_8'] . "', Line_8_Lock='";
			if($cardtemplate['line_8_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_9='" . $cardtemplate['line_9'] . "', Line_9_Lock='";
			if($cardtemplate['line_9_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .="', Line_10='" . $cardtemplate['line_10'] . "', Line_10_Lock='";
			if($cardtemplate['line_10_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_11='" . $cardtemplate['line_11'] . "', Line_11_Lock='";
			if($cardtemplate['line_11_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_12='" . $cardtemplate['line_12'] . "', Line_12_Lock='";
			if($cardtemplate['line_12_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_13='" . $cardtemplate['line_13'] . "', Line_13_Lock='";
			if($cardtemplate['line_13_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_14='" . $cardtemplate['line_14'] . "', Line_14_Lock='";
			if($cardtemplate['line_14_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_15='" . $cardtemplate['line_15'] . "', Line_15_Lock='";
			if($cardtemplate['line_15_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_16='" . $cardtemplate['line_16'] . "', Line_16_Lock='";
			if($cardtemplate['line_16_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_17='" . $cardtemplate['line_17'] . "', Line_17_Lock='";
			if($cardtemplate['line_17_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_18='" . $cardtemplate['line_18'] . "', Line_18_Lock='";
			if($cardtemplate['line_18_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_19='" . $cardtemplate['line_19'] . "', Line_19_Lock='";
			if($cardtemplate['line_19_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_20='" . $cardtemplate['line_20'] . "', Line_20_Lock='";
			if($cardtemplate['line_20_lock']=='y')
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
					$statement = "SELECT * FROM Card_Symbols WHERE Functional_Name=\"" . $b .
						"\" AND Template_ID=$template";
					$sql->Query($statement);
					if($sql->rows<1)
					{
					  $statement = "INSERT INTO Card_Symbols SET Functional_Name=\"" . $b .
						  "\", Template_ID=$template";
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
								$statement = "SELECT * FROM Card_Symbols WHERE Functional_Name=\"/$b/" .
								 $file . "\" AND Template_ID=$template";
								$sql->Query($statement);
								if($sql->rows<1)
								{
									$statement = "INSERT INTO Card_Symbols SET Functional_Name=\"/$b/" .
									  $file . "\", Template_ID=$template";
									$sql->Insert($statement);
								}
							}
						}
					}
				}
			}
		}else
		{ //It's a new template, create a new record
			if($HTTP_POST_VARS['company_name']!="")
			{
				$statement="SELECT ID FROM Company WHERE Name=\"" . $_POST['company_name'] . "\"";
				$sql->QueryItem($statement);
				echo "<!--Existant ID: " . $sql->data[0] . "-->\n";
				if($sql->data[0]=="")
				{
					$statement="INSERT INTO Company SET Name=\"" . $_POST['company_name'] .
						"\", Address1=\"" . $_POST['address1'] . "\", Address2=\"" .
						$_POST['address2'] . "\", City=\"" . $_POST['city'] . "\", State=\"" .
						$_POST['state'] . "\", Zip=\"" . $_POST['zip'] . "\"";
					if($cardtemplate['approval_req']=='y')
					  $statement .= ", Approval='y' ";
					else
					  $statement .= ", Approval='n' ";
					$statement .= ", Approval_Email='" . $cardtemplate['approval_email'] . "',  Rep='" .
						$cardtemplate['rep'] . "'";
					$sql->Insert($statement);
					$bizforms_company = $statement;
					$statement = "SELECT ID FROM Company WHERE Name=\"" . $_POST['company_name'] . "\"";
					$sql->QueryItem($statement);
					$cardtemplate['company']=$sql->data['ID'];
					$cardtemplate['company_name']=$_POST['company_name'];
				}else
				{
					$cardtemplate['company']=$sql->data['ID'];
					$cardtemplate['company_name']=$_POST['company_name'];
				}
			}
			echo "<!--$statement-->\n";
			$statement="INSERT INTO  Templates SET Templates.lines='" . $line_count .
			 "', Printer_Email='" . $cardtemplate['printer_email'] . "', Template_Name=\"" .
			 str_replace("\"", "&quot;", $cardtemplate['template_name']) . "\", Company=\"" .
			 $cardtemplate['company'] . "\", per250='" . $cardtemplate['per250'] . "', per500='" .
			 $cardtemplate['per500'] . "', per1000='" . $cardtemplate['per1000'] . "', per2000='" .
			 $cardtemplate['per2000'] . "', per250_premium='" . $cardtemplate['per250_premium'] .
			 "', per500_premium='" . $cardtemplate['per500_premium'] . "', per1000_premium='" .
			 $cardtemplate['per1000_premium'] . "', per2000_premium='" .
			 $cardtemplate['per2000_premium'] . "', QuickCard_Price='" .
			 $cardtemplate['quickcard_price'] . "', default_value='" .
			 $cardtemplate['default_value'] . "', card_quality='" . $cardtemplate['card_quality'] .
			 "', Pic_Upload='" . $cardtemplate['pic_upload'] . "', Pic_Width='" .
			 $cardtemplate['pic_width'] . "', Pic_Height='" . $cardtemplate['pic_height'] .
			 "', 2_Sided='" . $cardtemplate['2_sided'] . "', Paper='" . $cardtemplate['paper'] .
			 "', Ink='" . $cardtemplate['ink'] . "', Address1='" . $cardtemplate['address1'] .
			 "', Address2='" . $cardtemplate['address2'] . "', City='" . $cardtemplate['city'] .
			 "', State='" . $cardtemplate['state'] . "', Zip='" . $cardtemplate['zip'] .
			 "', Inactive='" . $cardtemplate['inactive'] . "', Rep='" . $cardtemplate['rep'] .
			 "', Agent='" . $cardtemplate['agent'] . "', Approval_Email=\"" .
			 $cardtemplate['approval_email'] . "\"";
//			unset($_SESSION['temp_lineCnt']);
			if($cardtemplate['allow_pdf']=='y')
				$statement .= ", Allow_PDF='y' ";
			else
				$statement .= ", Allow_PDF='n' ";
			if($cardtemplate['vertical']=='y')
				$statement .= ", Vertical='y' ";
			else
				$statement .= ", Vertical='n' ";
			if($cardtemplate['approval_req']=='y')
				$statement .= ", Approval_Req='y' ";
			else
				$statement .= ", Approval_Req='n' ";
			$statement .= ", Line_1='" . $cardtemplate['line_1'] . "', Line_1_Lock='";
			if($cardtemplate['line_1_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_2='" . $cardtemplate['line_2'] . "', Line_2_Lock='";
			if($cardtemplate['line_2_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_3='" . $cardtemplate['line_3'] . "', Line_3_Lock='";
			if($cardtemplate['line_3_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_4='" . $cardtemplate['line_4'] . "', Line_4_Lock='";
			if($cardtemplate['line_4_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_5='" . $cardtemplate['line_5'] . "', Line_5_Lock='";
			if($cardtemplate['line_5_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_6='" . $cardtemplate['line_6'] . "', Line_6_Lock='";
			if($cardtemplate['line_6_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_7='" . $cardtemplate['line_7'] . "', Line_7_Lock='";
			if($cardtemplate['line_7_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_8='" . $cardtemplate['line_8'] . "', Line_8_Lock='";
			if($cardtemplate['line_8_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_9='" . $cardtemplate['line_9'] . "', Line_9_Lock='";
			if($cardtemplate['line_9_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .="', Line_10='" . $cardtemplate['line_10'] . "', Line_10_Lock='";
			if($cardtemplate['line_10_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_11='" . $cardtemplate['line_11'] . "', Line_11_Lock='";
			if($cardtemplate['line_11_lock']=='y')
				$statement .= 'y';
			else
				$statement .= 'n';
			$statement .= "', Line_12='" . $cardtemplate['line_12'] . "', Line_12_Lock='";
			if($cardtemplate['line_12_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_13='" . $cardtemplate['line_13'] . "', Line_13_Lock='";
			if($cardtemplate['line_13_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_14='" . $cardtemplate['line_14'] . "', Line_14_Lock='";
			if($cardtemplate['line_14_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_15='" . $cardtemplate['line_15'] . "', Line_15_Lock='";
			if($cardtemplate['line_15_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_16='" . $cardtemplate['line_16'] . "', Line_16_Lock='";
			if($cardtemplate['line_16_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_17='" . $cardtemplate['line_17'] . "', Line_17_Lock='";
			if($cardtemplate['line_17_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_18='" . $cardtemplate['line_18'] . "', Line_18_Lock='";
			if($cardtemplate['line_18_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_19='" . $cardtemplate['line_19'] . "', Line_19_Lock='";
			if($cardtemplate['line_19_lock']=='y')
				$statement .='y';
			else
				$statement .= 'n';
			$statement .= "', Line_20='" . $cardtemplate['line_20'] . "', Line_20_Lock='";
			if($cardtemplate['line_20_lock']=='y')
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
					$statement = "SELECT * FROM Card_Symbols WHERE Functional_Name=\"" . $b .
						"\" AND Template_ID=$template";
					$sql->Query($statement);
					if($sql->rows<1)
					{
						$statement = "INSERT INTO Card_Symbols SET Functional_Name=\"" . $b .
							"\", Template_ID=$template";
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
								$statement = "SELECT * FROM Card_Symbols WHERE Functional_Name=\"/$b/" .
								 $file . "\" AND Template_ID=$template";
								$sql->Query($statement);
								if($sql->rows<1)
								{
								$statement = "INSERT INTO Card_Symbols SET Functional_Name=\"/$b/" .
								  $file . "\", Template_ID=$template";
								$sql->Insert($statement);
								}
							}
						}
					}
				}
			}

			$sql->Create("bizcardstodaynew");
//			ChromePhp::log('bizforms_company');
//			ChromePhp::log($bizforms_company);
			if($bizforms_company!="")
			$sql->Insert($bizforms_company);

			$statement="SELECT ID FROM Templates WHERE Template='" .
			 str_replace("\"", "", str_replace("'", "", str_replace(".", "", str_replace(" ", "", $cardtemplate['template_name'])))) . "'";
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
if($debug)
{
   flog('session', $_SESSION);
   flog('post2', $_POST);
   flog('get', $_GET);
   flog('cardtemplate', $cardtemplate);
}
?>
<html>
<head>
   <title>BizCardsToday Template Creator</title>
   <link rel="stylesheet" href="bizcard.css" type="text/css">
   <script language=javascript>
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
   </script>

</head>

<body>
<?php echo "<p><font color=red size=+2>$msg</font></p>\n"; ?>
<form action="template_create.php" method=post name=Form1 enctype="multipart/form-data">
<table border=0 cellspacing=0 cellpadding=0>
<tr>
   <th colspan=2>Default Shipping Information</th>
</tr>
<tr>
   <td>Template Name:</td>
   <td>
	 <input type=text value="<?php echo stripslashes(str_replace("&quot;", "\"", $cardtemplate['template_name'])); ?>" name="template_name">
   </td>
</tr>
<tr>
   <td>Company:</td>
   <td>
	 <input type=text value="<?php echo stripslashes(str_replace("&quot;", "\"", $cardtemplate['company_name'])); ?>" name="company_name">
	 or <select name="Old_Company" size=1>
		  <option value="">Already Defined Company</option>
<?php
$statement="SELECT Name From Company Order By Name";
$sql->Query($statement);
$j=0;
while($j<$sql->rows)
{
   $sql->Fetch($j);
   echo "<option value=\"" . $sql->data[0] . "\"";
   if($cardtemplate['company_name']==$sql->data[0]) echo " selected";
   echo ">" . $sql->data[0] . "</option>\n";
   $j++;
}
?>
   </td>
</tr>
<tr>
   <td>Address #1:</td>
   <td><input type=text value="<?php echo stripslashes(str_replace("&quot;", "\"", $cardtemplate['address1'])); ?>" name=address1></td>
</tr>
<tr>
   <td>Address #2:</td>
   <td><input type=text value="<?php echo stripslashes(str_replace("&quot;", "\"", $cardtemplate['address2'])); ?>" name=address2></td>
</tr>
<tr>
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
	 <td align=center>
	    <input type=text size=10
		  value='<?php echo $cardtemplate['per250']; ?>' name=per250></td>
	 <td align=center>
	    <input type=text size=10
			 value='<?php echo $cardtemplate['per250_premium']; ?>' name=per250_premium></td>
	 <td align=center>
	    <input type=radio name=default_value value=250
		  <?php if($cardtemplate['default_value']=="250") echo "checked"; ?>></td>
   </tr>
   <tr>
	 <td>500</td>
	 <td align=center>
	    <input type=text size=10
			 value='<?php if($cardtemplate['per500']!=0 && $cardtemplate['per500']!="") echo $cardtemplate['per500']; else echo "34.97";?>' name=per500>
	 </td>
	 <td align=center>
	    <input type=text size=10
			 value='<?php if($cardtemplate['per500_premium']!=0 && $cardtemplate['per500_premium']!="") echo $cardtemplate['per500_premium']; else echo "49.95";?>' name=per500_premium>
	 </td>
	 <td align=center>
	    <input type=radio name=default_value value=500
		  <?php if($cardtemplate['default_value']=="500") echo "checked"; ?>></td>
   </tr>
   <tr>
	 <td>1000</td>
	 <td align=center>
	    <input type=text size=10
			 value='<?php if($cardtemplate['per1000']!=0 && $cardtemplate['per1000']!="") echo $cardtemplate['per1000']; else echo "49.97"; ?>' name=per1000>
	 </td>
	 <td align=center>
	    <input type=text size=10
			 value='<?php if($cardtemplate['per1000_premium']!=0 && $cardtemplate['per1000_premium']!="") echo $cardtemplate['per1000_premium']; else echo "69.95"; ?>' name=per1000_premium>
	 </td>
	 <td align=center>
	    <input type=radio name=default_value value=1000 <?php if($cardtemplate['default_value']=="1000" || $row['default_value']=="") echo "checked"; ?>></td>
   </tr>
   <tr>
	 <td>2000</td>
	 <td align=center>
	    <input type=text size=10
			 value='<?php if($cardtemplate['per2000']!=0 && $cardtemplate['per2000']!="") echo $cardtemplate['per2000']; else echo "89.97"; ?>' name=per2000></td>
	 <td align=center>
	    <input type=text size=10
			 value='<?php if($cardtemplate['per2000_premium']!=0 && $cardtemplate['per2000_premium']!="") echo $cardtemplate['per2000_premium']; else echo "99.95"; ?>' name=per2000_premium>
	 </td>
	 <td align=center>
	    <input type=radio name=default_value value=2000
		  <?php if($cardtemplate['default_value']=="2000") echo "checked"; ?>>
	 </td>
   </tr>
   <TR>
	 <td colspan=5 bgcolor=#B0C4DE>Standard Shipping Cost:$
	    <input type=text name=sship size=4 maxlength=4 value=8>&nbsp;&nbsp;&nbsp;
	    Premium Shipping Charge:$
	    <input typ=text name=pship size=4 maxlength=4 value=10>
	 </td>
   </tr>
   <TR>
	 <td colspan=5 bgcolor=#B0C4DE>Type of Shipping:
	    <select name=typeship>
		  <option>Pickup
		  <option>Standard
		  <option>UPS
	    </select>
	 </td>
   </tr>
   <tr>
	 <td colspan=5>Rush Charges:
	    <input type=text size=10
			 value='<?php if($cardtemplate['QuickCard_Price']!=0 && $cardtemplate['quickcard_price']!="") echo $cardtemplate['quickcard_price']; else echo "20"; ?>' name=QuickCard_Price>
	 </td>
   </tr>
   <tr>
	 <td colspan=5>Default Card Quality:
	    <input type=radio name=card_quality value='e'
		  <?php if($cardtemplate['card_quality']=='e' || $cardtemplate['card_quality']=="") echo "checked"; ?>>Selection
	    <input type=radio name=card_quality value='s'
		  <?php if($cardtemplate['card_quality']=='s') echo "checked"; ?>>Standard Only
	    <input type=radio name=card_quality value='p'
		  <?php if($cardtemplate['card_quality']=='p') echo "checked"; ?>>Premium Only
	 </td>
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
	 <td align=center>
	    <input type=checkbox name=Pic_Upload value='y'
		  <?php if($cardtemplate['pic_upload']=='y') echo "checked"; ?>></td>
   </tr><tr>
	 <td>Photo Width (in pixels)</td>
	 <td>
	    <input type=text size=15 name=Pic_Width
			 value='<?php echo $cardtemplate['pic_width']; ?>'></td>
   </tr><tr>
	 <td>Photo Height (in pixels)</td>
	 <td>
	    <input type=text size=15 name=Pic_Height
			 value='<?php echo $cardtemplate['pic_height']; ?>'></td>
   </tr><tr>
	 <td>2 sided Card</td>
	 <td align=center>
	    <input type=checkbox name=2_sided value='y'
		  <?php if($cardtemplate['2_sided']=='y') echo "checked"; ?>></td>
   </tr><tr>
	 <td>Vertical Card</td>
	 <td align=center>
	    <input type=checkbox name=vertical value='y'
		  <?php if($cardtemplate['vertical']=='y') echo "checked"; ?>></td>
   </tr><tr>
	 <td>Allow PDF Upload</td>
	 <td align=center>
	    <input type=checkbox name=allow_pdf value='y'
		  <?php if($cardtemplate['Allow_PDF']=='y') echo "checked"; ?>></td>
   </tr><tr>
	 <td>Paper</td>
	 <td>
	    <input type=text size=15 name=paper
		  value='<?php if($cardtemplate['paper']=="") echo "10 pt. coated"; else echo $cardtemplate['paper']; ?>'></td>
   </tr><tr>
	 <td>Premium Paper</td>
	 <td>
	    <input type=text size=15 name=Paper_premium
		  value='<?php if($cardtemplate['paper_premium']=="") echo "14 pt. coated"; else echo $cardtemplate['paper_premium']; ?>'></td>
   </tr><tr>
	 <td>Ink</td>
	 <td>
	    <input type=text size=15 name=ink
			 value='<?php if($cardtemplate['ink']=="") echo "4/0"; else echo $cardtemplate['ink']; ?>'></td>
   </tr><tr>
	 <td>Printer Email</td>
	 <td>
	    <input type=text size=15 name=printer_email
			 value='<?php echo $row['printer_email']; ?>'></td>
   </tr><tr>
	 <td>Card Status</td>
	 <td>
	 <table border=0 cellspacing=2 cellpadding=0>
	 <tr>
	    <td>
		  <input type=radio name=inactive value='i'
			<?php if($cardtemplate['inactive']=='i') echo "checked"; ?>>Inactive</td>
	    <td>
		  <input type=radio name=inactive value='a'
			<?php if($cardtemplate['inactive']=='a') echo "checked"; ?>>Active</td>
	    <td>
		  <input type=radio name=inactive value='p'
			<?php if($cardtemplate['inactive']=='p' || $cardtemplate['inactive']=="") echo "checked"; ?>>Prospective</td>
	 </tr>
	 </table>
	 </td>
   </tr>
   </table>
   </td>
</tr>
<tr>
<?php
//If a file has been uploaded it will be stored in $dest_file, and a linecount will have been
//generated.  This little block allows default values for the first card to be loaded.
if($dest_file!="")
{
   //This section was added on 6/29/05.  Prevents optional symbol selection from showing until
   //after the SVG has been uploaded.
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
				 if(str_replace(" ", "_", $file)==$b) $line .= " checked";
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
   <td align=center><input type=text name=approval_email value='" . $cardtemplate['approval_email'] . "'></td>
   <td align=center><input type=checkbox name=approval_req value='y'";

   if($cardtemplate['approval_req']=='y')
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
   if($cardtemplate['rep']=="") $cardtemplate['rep']="H";
   while($j<$sql->rows)
   {
	 $sql->Fetch($j);
	 echo "\t\t\t\t\t<option value='" . $sql->data["Rep_Code"] . "' ";
	 if($cardtemplate['rep']==$sql->data['Rep_Code']) echo "selected";
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
	 if($cardtemplate['Agent']==$sql->data['Rep_Code']) echo "selected";
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
	 echo "\t\t<tr>\n\t\t\t<td>Line $j: </td>\n\t\t\t\t<td align=center><input type=text value='" .
		 $cardtemplate["line_" . $j] .
		 "' name=Line_$j size=35></td>\n\t\t\t\t<td align=center><input type=checkbox value='y' name=Line_${j}_Lock ";
	 if($cardtemplate["line_" . $j . "_Lock"]=='y') echo "checked";
	 echo "></td>\n\t\t</tr>\n";
	 $j++;
   }
   if($cardtemplate['vertical']=='y') //optimize page space
	 $dimension = "height=498 width=294";
   else
	 $dimension = "height=294 width=498";
   echo "\t\t<tr>\n\t\t\t<td colspan=3><embed src=$dest_file $dimension type=\"image/svg+xml\"></td>\n</tr><tr>\n";
}
if($msg == "")
	$tmplAction = 'create';
else
	$tmplAction = 'overwrite';

echo "<td colspan=2>SVG to upload:<br><input type=file name=photo size=30>
   <input type='hidden' name='submitted' value='true'>
	<input type='hidden' name='tmplAction' value='true'>
   <input type='submit' name='button' onclick='return OnButton2();' value='Upload SVG' ></td>\n";
?>
</tr>
<tr>
<td colspan=2>
   <input type='submit' name='button' onclick='return OnButton1();'
		value='<?php if($msg=="") echo "Create Template"; else echo "Overwrite"; ?>'>
   &nbsp;&nbsp;&nbsp;<a href='welcome.php?s=1'>Return to Menu</a></td>
</tr>

</table>
</form>
</body>
</html>
