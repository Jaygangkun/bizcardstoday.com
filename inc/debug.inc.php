<?php

/**
* print debug information to the current debug window
*
* @access public
* @param $name string variable name
* @param $data unknown variable
* @return null
* @global

To use these function, include() this library into your script. Then you may 
call both debug_msg() and debug_var() anywhere within your script. 

*/
function debug_var($name,$data)
{

	debug_open_window();

	$captured = explode("\n", debug_capture_print_r($data) );
	print "<script language='JavaScript'>\n";

	print "debugWindow.document.writeln('<span style=\'font-family: Arial, Helvetica, sans-serif; font-size: 10px; line-height: 11px;\'><b>$name</b></span>');\n";
	print "debugWindow.document.writeln('<pre style=\'line-height: 12px;\'>');";// \n

	foreach($captured as $line)
	{
		$line2 = debug_colorize_string($line);
		print "debugWindow.document.writeln('<font size=-3>" . 
				$line2 . "</font>');\n";
	}
	print "debugWindow.document.writeln('</pre>');\n";
	print "debugWindow.scrollBy(0,10000);";
	print "self.focus();\n";

	print "</script>\n";
}

/**
* print a message to the debug window
*
* @access public
* @param $mesg string message to display
* @return null
* @global
*/
function debug_msg($mesg)
{
	debug_open_window();
	print "<script language='JavaScript'>\n";
	print "debugWindow.document.writeln('<span style=\'font-family: Arial, Helvetica, sans-serif; font-size: 10px; line-height: 11px;\'>" . trim(nl2br($mesg)) . "</span><br>');\n";
	print "debugWindow.scrollBy(0,10000);";
	print "self.focus();\n";
	print "</script>\n";
}

/**
* open a debug window for display
*
* this function may be called multiple times
* it will only print the code to open the
* remote window the first time that it is called.
*

* @access private
* @return null
* @global
*/
function debug_open_window()
{
	static $window_opened = FALSE;
	if(!$window_opened)
	{

?>
		<script language="JavaScript">
		debugWindow = window.open("","debugWin","toolbar=no,scrollbars,width=600,height=400");
		debugWindow.document.writeln('<html>');
		debugWindow.document.writeln('<head>');
		debugWindow.document.writeln('<title>PHP Remote Debug Window</title>');
		debugWindow.document.writeln('</head>');

		debugWindow.document.writeln('<body><font face="verdana,arial">');
		debugWindow.document.writeln('<hr size=1 width="100%">');
		</script>
<?php
		$window_opened = TRUE;
	}
}


/**
* catch the contents of a print_r into a string
*
* @access private
* @param $data unknown variable
* @return string print_r results
* @global
*/
function debug_capture_print_r($data)
{
	ob_start();
	print_r($data);
	$result = ob_get_contents();

	ob_end_clean();
	return $result;
}


/**
* colorize a string for pretty display
*
* @access private
* @param $string string info to colorize
* @return string HTML colorized
* @global
*/
function debug_colorize_string($string)
{
	/* turn array indexes to red */

	$string = str_replace('[','[<font color="red">',$string);
	$string = str_replace(']','</font>]',$string);

	/* turn the word Array blue */
	$string = str_replace('Array','<font color="blue">Array</font>',$string);
	/* turn arrows graygreen */
	$string = str_replace('=>','<font color="#556F55">=></font>',$string);
	$string = str_replace("'", "\'", $string);
	$string = str_replace("/", "\/", $string);
	$string = str_replace('"', '\"', $string);
	return $string;
}
?>