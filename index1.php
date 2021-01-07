<?php
// if(!isset($_GET[c]))
// {
// echo("<img alt='maintenance' src='http://lab.webereng.com/maintenance.jpg' width='333' height='152'>");
// echo("<h2>bizcardstoday.com Undergoing Maintenance</h2> <br><h4>Please stop back in 6 or so hours. Sorry for the inconveniece!</h4>");
// exit();
// }

session_start();
session_destroy();
?>
<HTML>
<HEAD>
<TITLE>BizCardsToday.com - The fast, easy way to order and reorder business cards.</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
		<csscriptdict import>
			<script type="text/javascript" src="GeneratedItems/CSScriptLib.js"></script>
		</csscriptdict>
		<csactiondict>
			<script type="text/javascript"><!--
var preloadFlag = true;

// --></script>
		</csactiondict>
	</HEAD>
<BODY BGCOLOR=#000000 LEFTMARGIN=0 TOPMARGIN=0px MARGINWIDTH=0 MARGINHEIGHT=0 onload="document.all.email.focus()">
<!-- ImageReady Slices (new sidebar.psd) -->
<form action="login.php" method=post>
<input type=hidden name=target value='<? echo $HTTP_GET_VARS['target'] ?>'>
<table border=0 cellpadding=0 cellspacing=0 width=800>
<tr>
<td align=left valign=top>
						<TABLE WIDTH=132 BORDER=0 CELLPADDING=0 CELLSPACING=0 valign=top height="524">
							<TR height="130">
								<TD colspan="2" align="left" valign="top" width="160" height="130"><A HREF="(EmptyReference!)"><IMG NAME="businesscard_01" SRC="images/businesscard_01.gif" height="130" WIDTH=160 BORDER=0 ALT=""></A></TD>
							</TR>
							<tr valign="top" height="180">
								<td align="left" valign="top" width="130" height="180" background="images/table_bkgrnd.gif">
									<table width="74" border="0" cellspacing="0" cellpadding="0" background="images/table_bkgrnd.gif" height="180">
										<tr>
											<td align="right" valign="top" width="10"><img src="images/10x10.gif" alt="" height="10" width="10" border="0"></td>
											<td align="right" valign="top" width="110">
												<div align="center">
													<input type=text name=email size=14></div>
											</td>
										</tr>
										<tr>
											<td align="right" valign="top" width="10"></td>
											<td align="right" valign="top" width="110">
												<div align="center">
													<IMG SRC="images/email_06.gif" WIDTH=110 HEIGHT=22 ALT=""></div>
											</td>
										</tr>
										<tr>
											<td align="right" valign="top" width="10"></td>
											<td align="right" valign="top" width="110">
												<div align="center">
													<input type=password name=pword size=14></div>
											</td>
										</tr>
										<tr>
											<td align="right" valign="top" width="10"></td>
											<td align="right" valign="top" width="110">
												<div align="center">
													<IMG SRC="images/password_08.gif" WIDTH=110 HEIGHT=23 ALT=""></div>
											</td>
										</tr>
										<tr>
											<td align="right" valign="top" width="10"></td>
											<td align="right" valign="top" width="110">
												<div align="center">
													<input type=image src="images/login_button.gif" width=110 height=23></div>
											</td>
										</tr>
										<tr>
											<td align="right" valign="top" width="10"></td>
											<td align="right" valign="top" width="110">
												<div align="center">
													<font color=black>or</font></div>
											</td>
										</tr>
										<tr>
											<td align="right" valign="top" width="10"></td>
											<td align="right" valign="top" width="110">
												<div align="center">
													<A HREF="requestpassword.php"><IMG NAME="forgetpassword_09" SRC="images/forgetpassword_09.gif" WIDTH=110 HEIGHT=19 BORDER=0 ALT="" onmouseover="changeImages('forgetpassword_09','images/forgetpassword_09-over.gif');document.all['forgetpassword_09'].src='images/forgetpassword_09-over.gif';return true" onmouseout="changeImages('forgetpassword_09','images/forgetpassword_09.gif');document.all['forgetpassword_09'].src='images/forgetpassword_09.gif';return true" ></A></div>
											</td>
										</tr>
									</table>
								</td>
								<td width="30" height="180"></td>
							</tr>
							<tr height="30">
								<td align="center" valign="top" width="130" height="30"><A onmouseover="changeImages('home_11','images/home_11-over.gif');return true" onmouseout="changeImages('home_11','images/home_11.gif');return true" HREF="http://www.bizcardstoday.com"><IMG NAME="home_11" SRC="images/home_11.gif" height="31" WIDTH=130 BORDER=0 ALT="" onmouseover="changeImages('home_11','images/home_11-over.gif');document.all['home_11'].src='images/home_11-over.gif';return true" onmouseout="changeImages('home_11','images/home_11.gif');document.all['home_11'].src='images/home_11.gif';return true"></A></td>
								<td align="left" valign="top" width="30" height="30"></td>
							</tr>
							<tr height="30">
								<td align="center" valign="top" width="130" height="30"><A title="Bizcardstoday.com About Us" onmouseover="changeImages('aboutbizcards_12','images/aboutbizcards_12-over.gif');return true" onmouseout="changeImages('aboutbizcards_12','images/aboutbizcards_12.gif');return true" HREF="aboutus.html"><IMG NAME="aboutbizcards_12" SRC="images/aboutbizcards_12.gif" WIDTH=130 HEIGHT=31 BORDER=0 ALT="" onmouseover="changeImages('aboutbizcards_12','images/aboutbizcards_12-over.gif');document.all['aboutbizcards_12'].src='images/aboutbizcards_12-over.gif';return true" onmouseout="changeImages('aboutbizcards_12','images/aboutbizcards_12.gif');document.all['aboutbizcards_12'].src='images/aboutbizcards_12.gif';return true"></A></td>
								<td align="left" valign="top" width="30" height="30"></td>
							</tr>
							<tr height="30">
								<td align="center" valign="top" width="130" height="30"><a title="BizCardsToday.com New Users" onmouseover="changeImages('newuser_13','images/newuser_13-over.gif');return true" onmouseout="changeImages('newuser_13','images/newuser_13.gif');return true" href="newuser.html"><img id="newuser_13" src="images/newuser_13.gif" alt="" name="newuser_13" height="30" width="130" border="0"></a></td>
								<td align="left" valign="top" width="30" height="30"></td>
							</tr>
							<TR height="30">
								<TD COLSPAN=2 align="left" valign="top" width="160" height="30"><a title="BizCards Today Promotional Products" onmouseover="changeImages('premiumprods','images/premiumprods-over.gif');return true" onmouseout="changeImages('premiumprods','images/premiumprods.gif');return true" href="http://www.distributorcentral.com/websites/BizCardsToday/catalog.cfm?&CatalogGUID=18f2680e-6d0f-4d9f-be23-a5777cd3112d&StartLevel=2" target="_blank"><img id="premiumprods" src="images/premiumprods.gif" alt="" name="premiumprods" height="31" width="130" border="0"></a></TD>
							</TR>
							<tr height="30">
								<td align="left" valign="top" width="130" height="30"><A title="Contact Info for BizCardsToday.com" onmouseover="changeImages('contactus_14','images/contactus_14-over.gif');return true" onmouseout="changeImages('contactus_14','images/contactus_14.gif');return true" HREF="contactus.html"><IMG NAME="contactus_14" SRC="images/contactus_14.gif" WIDTH=130 BORDER=0 ALT="" onmouseover="document.all['contactus_14'].src='images/contactus_14-over.gif'" onmouseout="document.all['contactus_14'].src='images/contactus_14.gif'"></A></td>
								<td align="left" valign="top" width="30" height="30"></td>
							</tr>
							<tr height="30">
								<td align="left" valign="top" width="130" height="30"><img src="images/newsidebar_15.gif" alt="" height="29" width="130" border="0"></td>
								<td align="left" valign="top" width="30" height="30"></td>
							</tr>
							<tr height="30">
								<td colspan="2" align="left" valign="top" width="160" height="30"><a title="Bizcardstoday.com Promotional Items" href="http://www.distributorcentral.com/websites/BizCardsToday/categorylist.html?stip=2" target="_blank"><img src="images/speciality.gif" alt="" height="130" width="130" align="top" border="0"></a></td>
							</tr>
						</TABLE>
					</td>
<td valign=top>
<embed src="images/bizcardcircle2.swf" quality="high" bgcolor="#000000" width="569" height="571" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"></td>
</tr>
</table>
</form>
<!-- End ImageReady Slices -->
</BODY>
</HTML>
<!--<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,2,0" id="home" width="100%" height="75%">
<param name="src" value="/media/swf/0/public/1/home.swf">
<param name="movie" value="/media/swf/0/public/1/home.swf">
<param name="quality" value="high">
<param name="bgcolor" value="#336699">
<embed src="/media/swf/0/public/1/home.swf" quality="high" bgcolor="#336699" width="100%" height="75%" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
</object>
<OBJECT CLASSID="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" CODEBASE="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0" WIDTH="200" HEIGHT="155">
<PARAM NAME="MOVIE" VALUE="movie_name.swf">
<PARAM NAME="QUALITY" VALUE="HIGH">
<PARAM NAME="PLAY" VALUE="TRUE">
<PARAM NAME="LOOP" VALUE="TRUE">
<PARAM NAME="BGCOLOR" VALUE="#FFFFFF">
<EMBED SRC="movie_name.swf" QUALITY="HIGH" BGCOLOR="#FFFFFF" WIDTH="200" HEIGHT="155" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
</EMBED>
</OBJECT>-->