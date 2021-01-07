<?php

include_once('inc/preConf.php');
include_once('firelogger/firelogger.php');
session_start();
//flog('session1', $_SESSION);
if(DEBUGSW)
{
echo('<pre>');
print_r($_SESSION);
// print_r($_GET);
print_r($_POST);
echo('</pre>');
}

if(isset($_GET['u'])) $user = $_GET['u'];

// if(!session_is_registered("template")) //Boot to homepage if card template is not set.
if(!isset($_SESSION["template"])) //Boot to homepage if card template is not set.
{
	header("Location: index2.php");
}else
{
	if(isset($_POST['template']))
	{
		$template = $_POST['template'];
		$_SESSION['template'] = $template;
	}else $template = $_SESSION['template'];

	$user = $_SESSION['user'];
	$admin = $_SESSION['admin'];
	$full = $_SESSION['full'];
	$master = $_SESSION['master'];
}

if(isset($_GET['s']) AND $_GET['s'] == 1)
{
   unset($_SESSION['company']);
   unset($_SESSION['company_name']);
   unset($_SESSION['template_name']);
   unset($_SESSION['address1']);
   unset($_SESSION['address2']);
   unset($_SESSION['city']);
   unset($_SESSION['state']);
   unset($_SESSION['zip']);
   unset($_SESSION['line_count']);
   unset($_SESSION['per250']);
   unset($_SESSION['per500']);
   unset($_SESSION['per1000']);
   unset($_SESSION['per2000']);
   unset($_SESSION['per250_premium']);
   unset($_SESSION['per500_premium']);
   unset($_SESSION['per1000_premium']);
   unset($_SESSION['per2000_premium']);
   unset($_SESSION['quickcard_price']);
   unset($_SESSION['card_quality']);
   unset($_SESSION['pic_upload']);
   unset($_SESSION['two_sided']);
   unset($_SESSION['paper']);
   unset($_SESSION['paper_premium']);
   unset($_SESSION['ink']);
   unset($_SESSION['vertical']);
   unset($_SESSION['allow_pdf']);
   unset($_SESSION['approval_req']);
   unset($_SESSION['approval_email']);
   unset($_SESSION['approval_phone']);
   unset($_SESSION['approval_name']);
   unset($_SESSION['printer_email']);
   unset($_SESSION['pic_width']);
   unset($_SESSION['pic_height']);

}
require("util.php");	//db wrapper
$sql = new MySQL_class;
$sql->Create("bizcardstodaynew");

// if(!session_is_registered("Company"))
// 	session_register("Company");
if(!isset($_SESSION["Company"]))
	$_SESSION["Company"] = '';

$Approval_Req="";
$Approval_Email="";
$Approval_Phone="";
$Approval_Name="";
$per250="";
$per500="";
$per1000="";
$per2000="";
$per250_premium="";
$per500_premium="";
$per1000_premium="";
$per2000_premium="";
$QuickCard_Price="";
$card_quality="";
$Pic_Upload="";
$Two_Sided="";
$Paper="";
$Ink="";
$Vertical="";
$Allow_PDF="";
$Company="";
$Template_Name="";
$Address1="";
$Address2="";
$City="";
$State="";
$Zip="";
$dest_file="";
$card = "";
$form="";
if($HTTP_POST_VARS['template']=="")
	$HTTP_POST_VARS['template']=$template;
else
	$template = $HTTP_POST_VARS['template'];

$statement="SELECT Inactive, Company FROM Templates WHERE ID='" . $template . "'";
$sql->QueryRow($statement);
$client_status=$sql->data[0];
$_SESSION['client_status'] = $client_status;
$Company=$sql->data[1];

$statement="SELECT * FROM Finished_Cards WHERE Template=" . $template;
echo "<!--$statement-->\n";
$sql->Query($statement);
echo "<!--" . $sql->rows . "-->\n";
if($sql->rows>0)
{
		$reorder_bool="<A HREF=editpad.php><IMG SRC=images/Reorder.jpg BORDER=0></A>"; // <A HREF=batch.php><IMG SRC=images/batch.jpg BORDER=0></A>
}else
	$reorder_bool="";

$statement="SELECT * FROM Forms WHERE Template=$template;"; // Pop Letterhead section.
$sql->Query($statement);
if($sql->rows>0)
	$lhsection=true;
else
	$lhsection=false;

$statement="SELECT Users.Name, Company.Name as Company, Allow_Upload, Users.Rep_Code, Users.Special_Type FROM Users, Company WHERE Users.ID=$user AND (Users.Company=Company.ID OR Users.Company=Company.Name)";
// exit("-$statement-");
$sql->QueryRow($statement);

if(strtolower(substr($CurCardID,-3))=="pdf" && !$order_complete) //Delete most recently uploaded pdf if PDF order is canceled
{
	@unlink($CurCardID);
}
$CurCardID=""; //Purge PDF/Photo link


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<title>Welcome to BizCardsToday.com</title>
</head>

<body bgcolor=FFFFFF>
<CENTER>
<table width=800 cellpadding=0 cellspacing=0 border=0>
<TR>
	<TD colspan=2 bgcolor=FFFFFF>
	<TABLE style="border: 1px solid #000000;">
	<TR>
		<TD><img src=images/businesscard_03.gif><font face=verdana size=1><BR>
		<B>The fast, easy way to order and reorder business cards</td>
		<td width=100%><center><CENTER><h3>Welcome
		<?php echo $sql->data['Name']; ?></h3><h2>with  <b>
		<?php echo $sql->data['Company'] ?></b></h2></CENTER></td></tr>
	</table>
	</td>
</tr>
<TR>
<TD VALIGN=top>
<A HREF=http://www.bizcardstoday.com><IMG SRC=images/home.gif BORDER="0"></A>
<BR><A HREF=mailto:bizinfo@bizcardstoday.com?Subject=Request%20For%20Information%20About%20BizCardsToday.com>
<IMG SRC=images/contact.gif BORDER="0"></A>
<BR><A HREF=welcome.php><IMG SRC=images/mm.gif BORDER="0"></A></TD>
<TD><CENTER><TABLE><TR><TD>

<?php if($step == "")
{
?>

<fieldset style="border: 2px ridge #000000; color: #ffffff; width: 585px;">
	<legend style="color: #260000; font-family: verdana; font-size: 11pt;">What would you like to do</legend>
	<label>
<?php
if($sql->data['Allow_Upload']!='y')
{
	echo "<A HREF=editpad.php?Action=New><IMG SRC=images/Start.jpg BORDER=0></A>";
	echo $reorder_bool;
}
?>
						<A HREF=password_alter.php><IMG SRC=images/email.jpg BORDER=0></A>
<?php
if($sql->data['Allow_Upload']=='y' || $full=='y')
	//echo "<A HREF=pdf.php><IMG SRC=images/PDF.jpg BORDER=0></A>"
?>
</label>
</fieldset>
</td>
</tr>
<tr>
<td>

<?php

if($ricksays == "y")
{
	if($full=='y' || $lhsection===true)
	{
		echo "<fieldset style=\"border: 2px ridge #000000; color: #ffffff; width: 585px;\">
		<legend style=\"color: #260000; font-family: verdana; font-size: 11pt;\">Specialty Goods</legend>
		<label>";
		if($full=='y')
		{
			echo "<A HREF=form_add.php><IMG SRC=images/LAE_07.gif WIDTH=119 HEIGHT=92 BORDER=0></A>";
			if($lhsection===true)
				echo "<A HREF=form_edit.php><IMG SRC=images/LAE_08.gif WIDTH=132 HEIGHT=92 BORDER=0></A>";
		}
		if($full=='y' || ($admin=='y' && $template!=1))
			echo "<A HREF=form_deleter.php><IMG SRC=images/LAE_09.gif WIDTH=146 HEIGHT=92 BORDER=0></A>";
		if($lhsection===true)
			echo "<A HREF=form_order.php><IMG SRC=images/LAE_10.gif WIDTH=137 HEIGHT=92 BORDER=0></A>";

	echo "</label></fieldset>";
	}

}

echo "</td>
</tr>
<tr>
<td>";


if($admin=='y')
{
	echo "<fieldset style=\"border: 2px ridge #000000; color: #ffffff; width: 585px;\">
	<legend style=\"color: #260000; font-family: verdana; font-size: 11pt;\">Administrative Functions
	</legend>
	<label>";
	if($full=='y')
	{
		echo "<A HREF=template.php><IMG SRC=images/edit_template.jpg  BORDER=0></A>
		<A HREF=template_create.php><IMG SRC=images/Create_Template.jpg  BORDER=0></A>
		<A HREF=template_update.php><IMG SRC=images/Replace_Template.jpg  BORDER=0></A>";
	//	<A HREF=addagent.php><IMG SRC=images/Agent_ADD.jpg  BORDER=0></A>
	//							if ($handle=opendir("/images/PDF"))
	//							{
	//								while(false !== ($file = readdir($handle)))
	//								{
	//									if(strtolower(substr($file,-3))=="pdf")
	//										$pdfreview=true;
	//								}
	//							}
		if($pdfreview)
			echo "\t\t\t\t<li><a href=\"pdfreview.php\">Review Waiting PDFs</a></li>\n";
	}

	//Prevent anyone from accessing Administrative functions on the BizCards template
	if($template!=1 || $full=='y')
	{
		echo "</label>
		<label>";
		//Reps should not have access to Deletion functionality in clients
		if($sql->data['Rep_Code']=="" || $full=='y')
			echo "<A HREF=users.php><IMG SRC=images/Users.jpg  BORDER=0></A>
				<A HREF=deleter.php><IMG SRC=images/Delete_old.jpg BORDER=0></A>";
	}else
	{

		echo "</label>
			<label>";
	}


	if($sql->data['Rep_Code']!='')
	{
		if($sql->data['Special_Type']=='Rep')
		{

		}else if($sql->data['Special_Type']=='Agent')
		{
			//echo "<A HREF=agent_report.php><IMG SRC=images/Order_Report.jpg BORDER=0></A>";
		}
		if($full=='y')
		{
			//echo "<A HREF=agent_report.php><IMG SRC=images/Order_Report.jpg BORDER=0></A>";
		}
	}
	if($sql->data['Name']=="BizCards Admin")
	{
		//echo "<A HREF=pa_report.php><IMG SRC=images/Untitled-1_07.gif border=0></A>";
	}

	echo "</label>
	</fieldset>

	</td>
	</tr>

	<tr>
	<td>";


	if($full=='y' || $master!='n')
	{
		echo "<fieldset style=\"border: 2px ridge #000000; color: #ffffff; width: 585px;\">
	<legend style=\"color: #260000; font-family: verdana; font-size: 11pt;\">Change Active Card</legend>
	<label><font face=verdana size=1 color=000000><form action=welcome.php method=post>&nbsp;&nbsp;
	Current Card: <select name=template size=1 onchange=\"submit();\">";
		$statement="SELECT * FROM Templates WHERE Inactive<>'i'";
		if($master!='n' && $full!='y')
		{
			$statement .= " AND ID in ($master)";
		}
		$statement .= " ORDER BY Template_Name";

		$sql->Query($statement);
		$j=0;

	// echo('<pre>');
	// print_r($sql->data);
	// echo("-$template-");
	// echo('</pre>');

		while($j<$sql->rows)
		{
			$sql->Fetch($j);
			echo "<option value='" . $sql->data['ID'] . "'";
			if($template==$sql->data['ID'])
			{
				echo " selected";
				$templatefile = $sql->data['template'];
			}
			echo ">" . $sql->data['Template_Name'];
			if($sql->data['Inactive']=='p')
				echo " {Prospective}";
			echo "</option>\n";
			$j++;
		}
		echo "</select></form></label>
		<label>";
	}

	if($full=='y')
	{
		echo "<form action=welcome.php method=post>&nbsp;&nbsp;Inactive: <select name=template size=1
			onchange=\"submit();\">\n<option value=''>Select</option>\n";
		$statement="SELECT * FROM Templates WHERE Inactive='i' ORDER BY Template_Name";
		$sql->Query($statement);
		$j = 0;
		while($j < $sql->rows)
		{
			$sql->Fetch($j);
			echo "<option value='" . $sql->data['ID'] . "'";
			if($template==$sql->data['ID'])
			{
				echo " selected";
				$templatefile = $sql->data['template'];
			}
			echo ">" . $sql->data['Template_Name'] . "</option>\n";
			$j++;
		}
		echo "</select></form></p>\n";
	}

	echo "</label></fieldset>";
}
?></td>
</tr>
</table>
<?php
} // end of step = ''


if($step != "")
{
$bet = ucwords($step);
echo "<fieldset style=\"border: 2px ridge #000000; color: #ffffff; width: 585px;\">
						<legend style=\"color: #260000; font-family: verdana; font-size: 11pt;\">$bet</legend>
						<label>";
}


if($step == "users")
{
echo "<Table bgcolor=FFFFFF><tr><td>";
include "users.php";
echo "</td></tr></table>";
}

if($step == "deleter")
{
echo "<Table bgcolor=FFFFFF><tr><td>";
include "deleter.php";
echo "</td></tr></table>";
}


if($step != "")
{
echo "</label></fieldset>";
}


?>
</TD></TR></TABLE>
<?php
// 	echo("<A HREF=editpad.php?Action=New>N</A>&nbsp;<A HREF=editpad.php>R</A>&nbsp;<A HREF=batch.php>B</A>");
flog('session2', $_SESSION);
?>

</body>

</html>