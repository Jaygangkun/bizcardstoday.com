<?php

/*

Amended 17/08/2003 to attach data from php as a file

	Use the 1st argument of '$email->attach()' for the data
	and the 2nd argument for the filename

	code is backwards compatible with older versions.

Amended 20/02/2002 to include embeded images in html.

email_html_wa & email_multi_wa are now capable of using embeded images.

Include file holds 6 classes each extensions of email_txt

email_txt		- Text email no attachments.
email_wa		- Text email with attachments.
email_html		- Html email no attachments.
email_html_wa		- Html email with attachments.
email_multi		- Multi part email html and text.
email_multi_wa		- Multi part email html and text with attachments.

All have the same prototype
(
    from,		- Sender of email
    subject,		- Subject of email
    return address,	- Address for failed emails to return to.
    reply address,	- Address for any replies to go to.
)

All classes have these functions

sendmail(recipient)
  			- Sends the email to the supplied email address
			- the email address is checked for domain existance.
setheaders()
			- Creates the headers ready for transmission.
clean()			
			- Removes any message and headers already set.
			- Useful for sending different emails.
addmessage(message)
			- Add the message to the email.

classes email_multi and email_multi_wa use this version of addmessage instead
of the above.
addmessage(text, html)
			- text is the text part of the email and html holds
			- the html alternative.

Classes email_wa, email_html_wa and email_multi_wa also have

attach(filename)	
			- This will add an attachment to the email.
			- filename is the full pathname to the file.

embed_image(filename, imageid)		
			- Embeds an image in the email usefull for 
 			  sending html emails with images that can be
			  viewed offline.

examples of use:

text email

$email=new email_txt("from@me.com", "Test Email");
$email->clean();
$email->setheaders();
$email->addmessage("Hello");
$email->sendmail("you@your.email");

text email with attachment

$email=new email_wa("from@me.com", "Test Email");
$email->clean();
$email->setheaders();
$email->addmessage("Hello");
$email->attach("/path/to/file/filename");
$email->sendmail("you@your.email");

HTML email

$email=new email_html("from@me.com", "Test Email");
$email->clean();
$email->setheaders();
$email->addmessage("<B>Hello<B>");
$email->sendmail("you@your.email");

HTML email with attachment

$email=new email_html("from@me.com", "Test Email");
$email->clean();
$email->setheaders();
$email->addmessage("<B>Hello<B>");
$email->attach("/path/to/file/filename");
$email->sendmail("you@your.email");

Multipart email

$email=new email_multi("from@me.com", "Test Email");
$email->clean();
$email->setheaders();
$email->addmessage("hello", "<B>Hello<B>");
$email->sendmail("you@your.email");

Multipart email with attachment

$email=new email_multi_wa("from@me.com", "Test Email");
$email->clean();
$email->setheaders();
$email->addmessage("hello", "<B>Hello<B>");
$email->attach("/path/to/file/filename");
$email->sendmail("you@your.email");

HTML email with embeded image

$email=new email_html_wa("from@me.com", "Test Email");
$email->clean();
$email->setheaders();
$email->addmessage("<IMG src=\"cid:pic1.1a\"");
$email->embed_image("/path/to/file/filename", "pic1.1a");
$email->sendmail("you@your.email");

Multipart email with embeded image

$email=new email_multi_wa("from@me.com", "Test Email");
$email->clean();
$email->setheaders();
$email->addmessage("<IMG src=\"cid:pic1.1a\"");
$email->embed_image("/path/to/file/filename", "pic1.1a");
$email->sendmail("you@your.email");

*/

class email_txt
{
    var $subject;			// email subject
    var $bound;				// email boundary
    var $from;				// from address
    var $headers;			// message headers
    var $message;			// email message
    var $retadd;			// return address
    var $replyto;			// reply address

    function email_txt($from="nobody@localhost", $subj="Email for you", $retadd="nobody@localhost", $replyto="nobody@localhost")
    { 
	$bound="----=_NextPart_000_" . uniqid(rand()); 
	$this->message="";
	$this->bound=$bound;
	$this->subject=$subj;
	$this->retadd=$retadd;
	$this->replyto=$replyto;
	$this->from=$from;
    }

    function sendmail($rcpt)
    {
    	$receivers=explode(",", $rcpt);
    	foreach($receivers as $b)
    	{
    		echo "<!--$b-->\n";
		list($name, $domain)=explode("@", $b);
		$retval=checkdnsrr($domain, ANY);
		echo "<!--$retval-->\n";
		if ( (!$retval) || (empty($name)) || (empty($domain)) )
		{
		    echo "Invalid email address ".$b.".<BR>";
		    return(0);
		}
		$retval=mail($b, $this->subject, "", $this->headers.$this->message);
	}
	return($retval);
    }

    function setheaders()
    {
	unset($headers);
	$headers="Errors: <".$this->retadd.">\n";
	$headers.="From: <".$this->from.">\n";
	$headers.="MIME-Version: 1.0\n";
	$headers.="Return-Path: <".$this->retadd.">\n";
	$headers.="Reply-To: <".$this->replyto.">\n";
	$headers.="Content-Transfer-Encoding: 7bit\n";
	$headers.="Content-Type:text/plain; charset=us-ascii\n\n";

        unset($this->headers);
	$this->headers=$headers;
    }

    function clean()
    {
        $this->message="";
        $this->headers="";
    }

    function addmessage($messin)
    {
	if ( !empty($messin) )
	{
	    $this->message.=$messin."\n";
	}
    }
    
}

class email_html extends email_txt
{
    function email_html($from="nobody@localhost", $subj="Email for you", $retadd="nobody@localhost", $replyto="nobody@localhost")
    { 
	$bound="----=_NextPart_000_" . uniqid(rand()); 
	$this->message="";
	$this->bound=$bound;
	$this->subject=$subj;
	$this->retadd=$retadd;
	$this->replyto=$replyto;
	$this->from=$from;
    }

    function setheaders()
    {
	unset($headers);
	$headers.="Errors: <".$this->retadd.">\n";
	$headers.="From: <".$this->from.">\n";
	$headers.="X-Mailer: PHP\n";
	$headers.="X-Sender: <".$this->from.">\n";
	$headers.="X-Priority: 1\n";
	$headers.="Return-Path: <".$this->retadd.">\n";
	$headers.="Reply-To: <".$this->replyto.">\n";
	$headers.="Content-Type:text/html; charset=iso-8859-1\n\n";
	
	$this->message=$headers;
    }

}

class email_wa extends email_txt
{
    function email_wa($from="nobody@localhost", $subj="Email for you", $retadd="nobody@localhost", $replyto="nobody@localhost")
    { 
	$bound="----=_NextPart_000_" . uniqid(rand()); 
	$this->message="";
	$this->bound=$bound;
	$this->subject=$subj;
	$this->retadd=$retadd;
	$this->replyto=$replyto;
	$this->from=$from;
    }

    function sendmail($rcpt)
    {
//    	$receivers=explode(",", $rcpt);
//    	foreach($receivers as $b)
//    	{
		list($name, $domain)=explode("@", $rcpt);
		$retval=checkdnsrr($domain, ANY);
		if ( (!$retval) || (empty($name)) || (empty($domain)) )
		{
		    echo "Invalid email address ".$rcpt.".<BR>";
		    return(0);
		}
		$this->message.="\n\n--".$this->bound."--\n";

//echo('<pre>-' . $rcpt . '-' . $this->subject . '-' . $this->message . '-' . $this->headers.$this->message . '->' . $this->headers . '<-');

		$retval=mail($rcpt, $this->subject, $this->message);// , $this->headers.$this->message
//	}
	return($retval);
    }
    
    function setheaders()
    {
	unset($headers);
	$headers.="Errors: <".$this->retadd.">\n";
	$headers.="From: <".$this->from.">\n";
	$headers.="MIME-Version: 1.0\n";
	$headers.="Return-Path: <".$this->retadd.">\n";
	$headers.="Reply-To: <".$this->replyto.">\n";
	$headers.="Content-Type: multipart/mixed;boundary=\"".$this->bound."\"\n";
	$headers.="Content-Transfer-Encoding: 7bit\n";
	$headers.="\n\n--".$this->bound."\n";
	$headers.="Content-Type:text/plain; charset=us-ascii\n\n";
	
	$this->message=$headers;
    }

    function attach($attch, $filename)
    {

	if ($filename)
	{
		$thisfile = $filename;
	}
	else
	{
		$bits=explode("/", $attch);
		$thisfile=$bits[(count($bits)-1)];
	}
	
	unset($line);
	$line.="\n\n--".$this->bound."\n";
	$line.="Content-Type: application/octet-stream; name=\"$thisfile\"\n";
	$line.="Content-Transfer-Encoding: base64\n\n\n";

	if ($filename)
	{
		$line.= chunk_split(base64_encode($attch));
	}
	else
	{
		$filename=$attch;
		$linein=`uuencode -m $filename fred`;
	
		$lines=explode("\n", $linein);
		for ( $loop=1; $loop<count($lines); $loop++ )
		{
	    	$line.=$lines[$loop]."\n\r";
		}

	}
	
	$line.="\n\n";

	$this->message.=$line;
    }

}

class email_html_wa extends email_wa
{

    function email_html_wa($from="nobody@localhost", $subj="Email for you", $retadd="nobody@localhost", $replyto="nobody@localhost")
    { 
	$bound="----=_NextPart_000_" . uniqid(rand()); 
	$this->message="";
	$this->bound=$bound;
	$this->subject=$subj;
	$this->retadd=$retadd;
	$this->replyto=$replyto;
	$this->from=$from;
    }

    function setheaders()
    {
	unset($headers);
//	$headers.="Errors: <".$this->retadd.">\n";
	$headers.="From: <".$this->from.">\n";
	$headers.="MIME-Version: 1.0\n";
	$headers.="Return-Path: <".$this->retadd.">\n";
	$headers.="Reply-To: <".$this->replyto.">\n";
	$headers.="Content-Type: multipart/mixed;boundary=\"".$this->bound."\"\n";
	$headers.="Content-Transfer-Encoding: 7bit\n";
	$headers.="\n\n--".$this->bound."\n";
	$headers.="Content-Type:text/html; charset=iso-8859-1\n\n";
//exit("-$headers-");		
	$this->message=$headers;
    }

    function addmessage($messin)
    {
	if ( !empty($messin) )
	{
	    $this->message.=$messin."\n";
	}
    }
    
    function embed_image($img_path, $img_name)
    {
	unset($line);
	unset($bits);
	$bits=explode("/", $img_path);
	$filename=$bits[(count($bits)-1)];
	unset($bits);
	$bits=explode(".", $img_path);
	$line.="\n\n--".$this->bound."\n";
	$line.="Content-Type: image/".$bits[1]."\n";
	$line.="Content-ID: ".$img_name."\n";
	$line.="Content-Disposition: inline; filename=\"".$filename."\"\n";
	$line.="Content-Transfer-Encoding: base64\n\n\n";

	$linein=`uuencode -m $img_path fred`;

	$lines=explode("\n", $linein);
	for ( $loop=1; $loop<count($lines); $loop++ )
	{
	    $line.=$lines[$loop]."\n\r";
	}

	$line.="\n\n";

	$this->message.=$line;
    }

}

class email_multi extends email_txt
{

    function email_multi($from="nobody@localhost", $subj="Email for you", $retadd="nobody@localhost", $replyto="nobody@localhost")
    {
	$bound="----=_NextPart_000_" . uniqid(rand()); 
	$this->bound=$bound;
	$this->subject=$subj;
	$this->retadd=$retadd;
	$this->replyto=$replyto;
	$this->from=$from;
    }

    function addmessage($text, $html)
    {
	unset($message);
	// text 
	$message .= "--$this->bound\n"; 
	$message .= "Content-Type: text/plain; \n\tcharset=\"iso-8859-1\"\r\n"; 
	$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n"; 
	$message .= $text . "\n"; 

	// html 
	$message .= "--$this->bound\r\n"; 
	$message .= "Content-Type: text/html; \n\tcharset=\"iso-8859-1\"\r\n"; 
	$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n"; 
	$message .= $html; 
	
	$message .= "\r\n--$this->bound--"; 

	$this->message=$message;
    }

    function setheaders()
    {
	unset($headers);
	$headers="From: $from <$this->from>\r\n"; 
	$headers.="X-Sender: <$this->from>\r\n"; 
	$headers.="X-Mailer: TLGExtranet\r\n";
	$headers.="Return-Path: <$this->from>\r\n";
	$headers.="Mime-Version: 1.0\n"; 
	$headers.="Content-Type: multipart/alternative; boundary=\"$this->bound\"\r\n"; 
	$headers.="X-Priority: 1\r\n"; 
	$this->headers=$headers;
    }
}

class email_multi_wa extends email_txt
{

    function email_multi($from="nobody@localhost", $subj="Email for you", $retadd="nobody@localhost", $replyto="nobody@localhost")
    {
	$bound="----=_NextPart_000_" . uniqid(rand()); 
	$this->bound=$bound;
	$this->subject=$subj;
	$this->retadd=$retadd;
	$this->replyto=$replyto;
	$this->from=$from;
    }

    function addmessage($text, $html)
    {
	unset($message);
	// text 
	$message .= "--$this->bound\n"; 
	$message .= "Content-Type: text/plain; \n\tcharset=\"iso-8859-1\"\r\n"; 
	$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n"; 
	$message .= $text . "\n"; 

	// html 
	$message .= "--$this->bound\r\n"; 
	$message .= "Content-Type: text/html; \n\tcharset=\"iso-8859-1\"\r\n"; 
	$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n"; 
	$message .= $html; 
	
	$this->message=$message;
    }

    function setheaders()
    {
	unset($headers);
	$headers="From: $from <$this->from>\r\n"; 
	$headers.="X-Sender: <$this->from>\r\n"; 
	$headers.="X-Mailer: TLGExtranet\r\n";
	$headers.="Return-Path: <$this->from>\r\n";
	$headers.="Mime-Version: 1.0\n"; 
	$headers.="Content-Type: multipart/alternative; boundary=\"$this->bound\"\r\n"; 
	$headers.="X-Priority: 1\r\n"; 
	$this->headers=$headers;
    }

    function sendmail($rcpt)
    {
	list($name, $domain)=explode("@", $rcpt);
	$retval=checkdnsrr($domain, ANY);
	if ( (!$retval) || (empty($name)) || (empty($domain)) )
	{
	    echo "Invalid email address ".$rcpt.".<BR>";
	    return(0);
	}
	$this->message.="\r\n--$this->bound--"; 

	$retval=mail($rcpt, $this->subject, "", $this->headers.$this->message);
	return($retval);
    }

    function attach($attch)
    {
	$bits=explode("/", $attch);
	$thisfile=$bits[(count($bits)-1)];
	unset($line);
	$line.="\n\n--".$this->bound."\r\n";
	$line.="Content-Type: application/octet-stream; name=\"$thisfile\"\r\n";
	$line.="Content-Transfer-Encoding: base64\r\n\r\n";

	$filename=$attch;

	$linein=`uuencode -m $filename fred`;

	$lines=explode("\n", $linein);
	for ( $loop=1; $loop<count($lines); $loop++ )
	{
	    $line.=$lines[$loop]."\n\r";
	}

	$line.="\n\n";

	$this->message.=$line;
    }
    
    function embed_image($img_path, $img_name)
    {
	unset($line);
	unset($bits);
	$bits=explode("/", $img_path);
	$filename=$bits[(count($bits)-1)];
	unset($bits);
	$bits=explode(".", $img_path);
	$line.="\n\n--".$this->bound."\n";
	$line.="Content-Type: image/".$bits[1]."\n";
	$line.="Content-ID: ".$img_name."\n";
	$line.="Content-Disposition: inline; filename=\"".$filename."\"\n";
	$line.="Content-Transfer-Encoding: base64\n\n\n";

	$linein=`uuencode -m $img_path fred`;

	$lines=explode("\n", $linein);
	for ( $loop=1; $loop<count($lines); $loop++ )
	{
	    $line.=$lines[$loop]."\n\r";
	}

	$line.="\n\n";

	$this->message.=$line;
    }

    
}

?>