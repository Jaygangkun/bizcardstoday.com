<?php
	session_start();
	session_destroy();
	session_start();
	if(!session_is_registered("template"))
	{
		session_register("template");
		session_register("admin");
		session_register("user");
		session_register("target");
		session_register("name");
		session_register("full");
		session_register("master");
		session_register("partial");
		session_register("client_status");
	}
	require("util.php");
	$sql = new MySQL_class;
	$sql->Create("bizcardstodaynew");
	
	$statement = "SELECT u.Template, Full_Admin, Master_Admin, u.ID, User_Admin, Approver, Name, t.Inactive, u.Special_Type, u.Rep_Code FROM Users u, Templates t WHERE u.Template=t.id and login='" . $_REQUEST['email'] . "' and password='" . $_REQUEST['pword'] . "';";

	$sql->QueryRow($statement);
	if($sql->rows>0)
	{
		$template=$sql->data[0];
		$user = $sql->data['ID'];
// echo('--<pre>');
// echo('data');
// print_r($sql->data);
// echo('httpVar');
// print_r($_REQUEST);
// echo('post');
// print_r($_POST);
// exit('</pre>');

		$_SESSION['template'] = $sql->data['Template']; // number 
		$_SESSION['admin'] = $sql->data['User_Admin']; // y or n 
		$_SESSION['user'] = $sql->data['ID']; // number 
		$_SESSION['target'] = $target;
		$_SESSION['name'] = $sql->data['Name'];
		$_SESSION['full'] = $sql->data['Full_Admin'];
		$_SESSION['master'] = $sql->data['Master_Admin'];//Full_Admin User_Admin
// 		$_SESSION[''] = ;
// 		$_SESSION[''] = ;

// echo('->' . $sql->data[0] . '-');
		if($HTTP_POST_VARS['target']!="" && $sql->data['Approver']=="y")
		{
			$client_status=$sql->data['Inactive'];
			$target=$HTTP_POST_VARS['target'];
		}
		if($sql->data['Inactive']=='i')
			$kick="index2.php";
		else
			$kick = "welcome.php?t=$template&u=$user";
	}else
	{
		$template=0;
		if($_REQUEST['from']!="15591827d5af328786cdc81ed0f456bf") 
			$kick = "index2.php";
		elseif($_REQUEST['from']=="15591827d5af328786cdc81ed0f456bf") //bizforms
			$kick = "http://www.bizcardstoday.com/welcome.php?auth=15591827d5af328687cdc81ed0f456bf&msg=1";
		
	}
	if($sql->data['Full_Admin']=='y' || $sql->data['User_Admin']=='y' || $sql->data['Master_Admin']!='n')
	{
		$admin = 'y';
	}else
		$admin='n';
		
	$full=$sql->data['Full_Admin']; //Full System Admin Access
	$master=$sql->data['Master_Admin']; //Multi-Template Admin access
	$partial=$sql->data['User_Admin']; //Single Template Admin access
	$user = $sql->data['ID'];
	$name = $sql->data['Name'];
	
// exit("-$kick-");
// exit("->$full-$master-$partial-$user-$name<-");
	/*
	if($sql->data['Special_Type']=="Agent")
	{
		$statement="SELECT ID FROM Templates WHERE Agent=\"" . $sql->data['Rep_Code'] . "\" ORDER BY Template_Name LIMIT 1";
		$sql->QueryItem($statement);
		if($sql->rows>0)
			$template=$sql->data[0];
	}
	*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<meta http-equiv="REFRESH" content="0;url=<?php echo $kick; ?>">
		<?php if($kick=="index2.php")
			echo "<script language=javascript>\nalert('Invalid Login.  Please try again.');\n</script>\n";
		?>
	</head>


</html>
