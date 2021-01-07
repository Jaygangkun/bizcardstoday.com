<?php
	require("util.php");
	$sql=new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	if(($HTTP_POST_VARS['pword']!="" || $HTTP_POST_VARS['login']!="") && $HTTP_POST_VARS['email']!="")
	{
		$statement = "SELECT Password, Login, Email FROM Users WHERE Email='" . $HTTP_POST_VARS['email'] . "' AND";
		if($HTTP_POST_VARS['pword']!="")
			$statement .= " Password='" . $HTTP_POST_VARS['pword'] . "'";
		else
			$statement .= " Login='" . $HTTP_POST_VARS['login'] . "'";
		$sql->QueryRow($statement);
		$pword = $sql->data['Password'];
		$login = $sql->data['Login'];
		$email = $sql->data['Email'];
	}else
	{		
		if(($HTTP_POST_VARS['pword']!="" || $HTTP_POST_VARS['login']!="") && $HTTP_POST_VARS['email']=="")
		{
			$msg = "This login does not have an email contact listed.  Please contact us at (859) 576-9602 for assistance.";
		}
		if($HTTP_POST_VARS['pword']=="" && $HTTP_POST_VARS['login']=="" && $HTTP_POST_VARS['submitted']==true)
		{
			$msg = "Please contact us at (859) 576-9602 for verification of your account.";
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<HTML>
<HEAD>
<TITLE>BizCardsToday.com - The fast, easy way to order and reorder business cards.</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

</HEAD>
<BODY BGCOLOR=#FFFFFF LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>
<!-- ImageReady Slices (new sidebar.psd) -->

<table border=0 cellpadding=0 cellspacing=4 width=800>
	<tr>
		<td align=left valign=top><form action="login.php" method=post>
			<TABLE WIDTH=150 BORDER=0 CELLPADDING=0 CELLSPACING=0 valign=top>
				<TR>
					<TD COLSPAN=4>
						<A HREF="index.html">
							<IMG NAME="businesscard_01" SRC="images/businesscard_02.gif" WIDTH=160 HEIGHT=130 BORDER=0 ALT=""></A></TD>
				</TR>
				<TR>
					<TD ROWSPAN=7 background="images/blankblueleft_02.gif">
						<IMG NAME="blankblueleft_02" SRC="images/blankblueleft_02.gif" WIDTH=10 HEIGHT=120 ALT=""></TD>
					<TD bgcolor=#13D0FF align=left width=110>
						<input type=text name=email size=14></TD>
					<TD ROWSPAN=7 background="images/blankblueright_04.gif" align=left>
						<IMG NAME="blankblueright_04" SRC="images/blankblueright_04.gif" WIDTH=10 HEIGHT=110 ALT=""></TD>
					<TD ROWSPAN=11 background="images/blankwhite_05.gif">
						<IMG SRC="images/blankwhite_05.gif" WIDTH=30 HEIGHT=262 ALT=""></TD>
							</TR>
				<TR>
					<TD width=110>
						<IMG SRC="images/email_06.gif" WIDTH=110 HEIGHT=22 ALT=""></TD>
							</TR>
				<TR>
					<TD bgcolor=#13D0FF width=110>
						<input type=password name=pword size=14></TD>
							</TR>
				<TR>
								<TD width=110>
						<IMG SRC="images/password_08.gif" WIDTH=110 HEIGHT=23 ALT=""></TD>
							</TR>
				<TR>
								<TD width=110>
						<input type=image src="images/login_button.gif" width=110 height=23></TD>
							</TR>
				<TR>
								<TD bgcolor=#13D0FF align=center width=110><font color=black>or</font><br>
						<A HREF="requestpassword.php">
							<IMG NAME="forgetpassword_09" SRC="images/forgetpassword_09.gif" WIDTH=110 HEIGHT=19 BORDER=0 ALT="" onmouseover="document.all['forgetpassword_09'].src='images/forgetpassword_09-over.gif'" onmouseout="document.all['forgetpassword_09'].src='images/forgetpassword_09.gif'" ></A></TD>
							</TR>
				<TR>
								<TD width=110>
						<IMG SRC="images/newsidebar_10.gif" WIDTH=110 HEIGHT=10 ALT=""></TD>
							</TR>
				<TR>
								<TD COLSPAN=3>
						<A HREF="index.html">
							<IMG NAME="home_11" SRC="images/home_11.gif" WIDTH=130 HEIGHT=31 BORDER=0 ALT="" onmouseover="document.all['home_11'].src='images/home_11-over.gif'" onmouseout="document.all['home_11'].src='images/home_11.gif'"></A></TD>
							</TR>
				<TR>
								<TD COLSPAN=3>
						<A HREF="aboutus.html">
							<IMG NAME="aboutbizcards_12" SRC="images/aboutbizcards_12.gif" WIDTH=130 HEIGHT=31 BORDER=0 ALT="" onmouseover="document.all['aboutbizcards_12'].src='images/aboutbizcards_12-over.gif'" onmouseout="document.all['aboutbizcards_12'].src='images/aboutbizcards_12.gif'"></A></TD>
							</TR>
				<TR>
								<TD COLSPAN=3>
						<A HREF="newuser.html">
							<IMG NAME="newuser_13" SRC="images/newuser_13.gif" WIDTH=130 HEIGHT=30 BORDER=0 ALT="" onmouseover="document.all['newuser_13'].src='images/newuser_13-over.gif'" onmouseout="document.all['newuser_13'].src='images/newuser_13.gif'"></A></TD>
							</TR>
				<TR>
								<TD COLSPAN=3>
						<A HREF="contactus.html">
							<IMG NAME="contactus_14" SRC="images/contactus_14.gif" WIDTH=130 HEIGHT=31 BORDER=0 ALT="" onmouseover="document.all['contactus_14'].src='images/contactus_14-over.gif'" onmouseout="document.all['contactus_14'].src='images/contactus_14.gif'"></A></TD>
							</TR>
				<TR>
								<TD COLSPAN=3>
						<IMG NAME="newsidebar_15" SRC="images/white_sidebar_15.gif" WIDTH=130 HEIGHT=29 ALT=""></TD>
							</TR>
			</TABLE></form></td>
			<td>
				<p>Please provide your email address and one other piece of information.</p>
				<? if($msg!="")
					echo "<p><font size=+1 color=red>$msg</font></p>";
				?>
				<form action=requestpassword.php method=post>
				<input type=hidden value=true name=submitted>
				<table border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td>Email Address:</td>
						<td><input type=text name=email value='<? echo $email; ?>'></td>
					</tr><tr>
						<td>Login: </td>
						<td><input type=text name=login value='<? echo $login; ?>'></td>
					</tr><tr>
						<td>Password: </td>
						<td><input type=<? if($pword!="") echo "text"; else echo "password"; ?> name=pword value='<? echo $pword; ?>'></td>
					</tr><tr>
						<td colspan=2><input type=submit value="Submit"></td>
					</tr>
				</table>
				</form>
			</td>
		</tr>
	</table>

<!-- End ImageReady Slices -->
</BODY>
</HTML>

	</body>
</html>