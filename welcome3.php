<?php
include_once('inc/preConf.php');
if(DEBUGSW) include_once('inc/debug.inc.php');

session_start();

// if(DEBUGSW) 
// {
// debug_msg("page is welcome.php-1");
// debug_var("POST",$_POST);
// debug_var("GET",$_GET);
// debug_var("SESSION",$_SESSION);
// }


if(isset($_SESSION['CurCardID']))
	$CurCardID = $_SESSION['CurCardID'];
else
	$CurCardID = '';

if(isset($_POST['step']))
	$step = $_POST['step'];
else if(isset($_SESSION['step']))
	$step = $_SESSION['step'];
else
	$step = '';

if(isset($_SESSION['ricksays']))
	$ricksays = $_SESSION['ricksays'];
else if(isset($_POST['ricksays']))
	$ricksays = $_POST['ricksays'];
else if(isset($_GET['ricksays']))
	$ricksays = $_GET['ricksays'];
else $ricksays = '';

//Boot to homepage if card template is not set.
// if($template == 0 || !session_is_registered("template") || $template == "") 
if($template == 0 || !isset($_SESSION["template"]) || $template == "") 
	header("Location: index2.php");

require("util.php");	//db wrapper
$sql = new MySQL_class;
$sql->Create("bizcardstodaynew"); //bus_card

// if(!session_is_registered("Company"))
if(!isset($_SESSION["Company"]))
	// session_register("Company");
	$_SESSION['Company'] = '';

$Approval_Req = "";
$Approval_Email = "";
$Approval_Phone = "";
$Approval_Name = "";
$per250 = "";
$per500 = "";
$per1000 = "";
$per2000 = "";
$per250_premium = "";
$per500_premium = "";
$per1000_premium = "";
$per2000_premium = "";
$QuickCard_Price = "";
$card_quality = "";
$Pic_Upload = "";
$Two_Sided = "";
$Paper = "";
$Ink = "";
$Vertical = "";
$Allow_PDF = "";
$Company = "";
$Template_Name = "";
$Address1 = "";
$Address2 = "";
$City = "";
$State = "";
$Zip = "";
$dest_file = "";
$card  =  "";
$form = "";

if(!isset($HTTP_POST_VARS['template']) )// $HTTP_POST_VARS['template'] == ""
	$HTTP_POST_VARS['template'] = $template;
else
	$template = $HTTP_POST_VARS['template'];

$statement = "SELECT Inactive, Company FROM Templates WHERE ID=" . $template;
$sql->QueryRow($statement);
$client_status = $sql->data[0];
$Company = $sql->data[1];

$statement = "SELECT * FROM Finished_Cards WHERE Template=" . $template;
echo "<!--$statement-->\n";
$sql->Query($statement);
echo "<!--" . $sql->rows . "-->\n";
if($sql->rows>0)
{
	$reorder_bool = 
	"<A HREF=editpad.php><IMG SRC=images/Reorder.jpg BORDER=0></A>
	<A HREF=batch.php><IMG SRC=images/batch.jpg BORDER=0></A>";
}else
	$reorder_bool = "";

// Pop Letterhead section.
$statement = "SELECT * FROM Forms WHERE Template=$template;"; 
$sql->Query($statement);
if($sql->rows>0)
	$lhsection = true;
else
	$lhsection = false;

$statement = 
"SELECT Users.Name, Company.Name as Company, Allow_Upload, Users.Rep_Code, 
Users.Special_Type FROM Users, Company WHERE Users.ID=$user AND 
(Users.Company=Company.ID OR Users.Company=Company.Name)";
$sql->QueryRow($statement);

//Delete most recently uploaded pdf if PDF order is canceled
if(strtolower(substr($CurCardID,-3)) == "pdf" && !$order_complete) 
{
	@unlink($CurCardID);
}
$CurCardID = ""; //Purge PDF/Photo link


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>Welcome to BizCardsToday.com</title>
	</head>


<body bgcolor=FFFFFF>
<CENTER>
<table width=800 cellpadding=0 cellspacing=0 border=0><TR><TD colspan=2 bgcolor=FFFFFF>
	<TABLE style="border: 1px solid #000000;">
	<TR>
		<TD><img src=images/businesscard_03.gif><font face=verdana size=1><BR><B>
		The fast, easy way to order and reorder business cards</td>
		<td width=100%><center><CENTER><h3>Welcome 
		<?php echo $sql->data['Name']; ?></h3><h2>with  <b>
		<?php echo $sql->data['Company'] ?></b></h2></CENTER></td></tr>
	</table>



</td>
</tr>
<TR><TD VALIGN=top>
<A HREF="<?php echo(HOME_URL); ?>" ><IMG SRC=images/home.gif BORDER="0"></A>
<BR>
<A HREF=mailto:bizinfo@bizcardstoday.com?Subject=Request%20For%20Information%20About%20BizCardsToday.com>
<IMG SRC=images/contact.gif BORDER="0"></A>
<BR><A HREF=welcome.php><IMG SRC=images/mm.gif BORDER="0"></A></TD>
<TD><CENTER><TABLE><TR><TD>

<?php if($step == "")
{
?>



<fieldset style="border: 2px ridge #000000; color: #ffffff; width: 585px;">
	<legend style="color: #260000; font-family: verdana; font-size: 11pt;">
	What would you like to do</legend>
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
		echo "<A HREF=pdf.php><IMG SRC=images/PDF.jpg BORDER=0></A>"
?>
</label>
</fieldset>


</td></tr>
<tr><td>

<?php

if($ricksays == "y")
{
	if($full=='y' || $lhsection===true)
	{
		echo "<fieldset style=\"border: 2px ridge #000000; color: #ffffff; width: 
		585px;\">
		<legend style=\"color: #260000; font-family: verdana; font-size: 11pt;\">
		Specialty Goods</legend>
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
<td>
";

if($admin=='y')
{
	echo "<fieldset style=\"border: 2px ridge #000000; color: #ffffff; 
	width: 585px;\">
	<legend style=\"color: #260000; font-family: verdana; font-size: 11pt;
	\">Administrative Functions</legend>
	<label>";
	if($full=='y')
	{
		echo "<A HREF=template.php><IMG SRC=images/edit_template.jpg  BORDER=0>
		</A>
		<A HREF=template_create.php><IMG SRC=images/Create_Template.jpg  BORDER=0>
		</A>
		<A HREF=template_update.php><IMG SRC=images/Replace_Template.jpg 
		BORDER=0></A>
		<A HREF=addagent.php><IMG SRC=images/Agent_ADD.jpg  BORDER=0></A>";
//							if ($handle=opendir("/images/PDF"))
//							{
//								while(false !== ($file = readdir($handle)))
//								{
//									if(strtolower(substr($file,-3))=="pdf")
//										$pdfreview=true;
//								}
//							}
		if($pdfreview)
			echo "\t\t\t\t<li><a href=\"pdfreview.php\">Review Waiting PDFs</a>
			</li>\n";
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

		echo "</label><label>";
	}


	if($sql->data['Rep_Code']!='')
	{
		if($sql->data['Special_Type']=='Rep')
		{

		}else if($sql->data['Special_Type']=='Agent')
		{
			echo "<A HREF=agent_report.php><IMG SRC=images/Order_Report.jpg BORDER=0></A>";
		}
		if($full=='y')
		{
			echo "<A HREF=agent_report.php><IMG SRC=images/Order_Report.jpg BORDER=0></A>";
		}
	}
	if($sql->data['Name']=="BizCards Admin")
	{
		echo "<A HREF=pa_report.php><IMG SRC=images/Untitled-1_07.gif border=0></A>";
	}

	echo "</label>
	</fieldset>

</td>
</tr>

<tr>
<td>";

// $t1 = count($master);
// exit("-$full+$t1+");

	if($full=='y' || $master!='n')
	{
		echo "<fieldset style=\"border: 2px ridge #000000; color: #ffffff; 
		width: 585px;\">
	<legend style=\"color: #260000; font-family: verdana; font-size: 11pt;\">
	Change Active Card</legend>
	<label><font face=verdana size=1 color=000000>
	<form action=welcome.php method=post>&nbsp;&nbsp;Current Card: 
	<select name=template size=1 onchange=\"submit();\">";
	
		$statement = "SELECT * FROM Templates WHERE Inactive<>'i'";
		if($master!='n' && $full!='y')
		{
			$statement .= " AND ID in ($master)";
		}
		$statement .= " ORDER BY Template_Name";
				
		$sql->Query($statement);
		$j = 0;
		while($j<$sql->rows)
		{
			$sql->Fetch($j);
			echo "<option value='" . $sql->data['ID'] . "'";
			if($template==$sql->data['ID'])
			{
				echo " selected";
				$templatefile = $sql->data['Template'];
			}
			echo ">" . $sql->data['Template_Name'];
			if($sql->data['Inactive']=='p')
				echo " {Prospective}";
			echo "</option>\n";
			$j++;
		}
		echo "</select></form></label><label>";
	}
		
	if($full=='y')
	{
		echo "<form action=welcome.php method=post>&nbsp;&nbsp;Inactive: 
		<select name=template size=1 onchange=\"submit();\">\n
		<option value=''>Select</option>\n";
		$statement = "SELECT * FROM Templates WHERE Inactive='i' ORDER BY Template_Name";
		$sql->Query($statement);
		$j = 0;
		while($j < $sql->rows)
		{
			$sql->Fetch($j);
			echo "<option value='" . $sql->data['ID'] . "'";
// debug_var("template1", "-$template-");
// debug_var("data ID", $sql->data['ID']);
// debug_var("template2", $sql->data['Template']);

			if($template == $sql->data['ID'])
			{
debug_var("template2", $sql->data['Template']);
				echo " selected";
				$templatefile = $sql->data['Template'];
			}
			echo ">" . $sql->data['Template_Name'] . "</option>\n";
			$j++;
		}
		echo "</select></form></p>\n";
	}
	
	echo "</label></fieldset>";
}
?>
</td>
</tr>
</table>

<?php
}


if($step != "")
{
	$bet = ucwords($step);
	echo "<fieldset style=\"border: 2px ridge #000000; color: #ffffff; 
		width: 585px;\">
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

if(DEBUGSW) 
{
debug_msg("page is welcome.php-2");
debug_var("POST",$_POST);
debug_var("GET",$_GET);
debug_var("SESSION",$_SESSION);
}

?>
</TD></TR></TABLE>
	</body>

</html>