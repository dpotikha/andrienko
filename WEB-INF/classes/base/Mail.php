<?php

function sendMail($from, $to, $subj, $body, $charset = SITE_CHARSET) {
	$charset = "utf-8";
	$MAIL=new my_mime_mail();
	$MAIL->setCharset($charset);
	$MAIL->setFrom($from);
	$MAIL->setTo($to);
	$MAIL->setSubject($subj);
	$MAIL->setBody($body);
	$MAIL->send();
	return false;
	

	$mm = new Mail_mime();
	$mm->setFrom($from);
	$mm->setHTMLBody($body);
	$mm->setSubject($subj);

	//$msg = $mm->getMessage();
	$body = $mm->get(array(
						'head_encoding' => 'quoted-printable',//'base64',
						'html_encoding' => 'quoted-printable', //base64
						'html_charset'  => $charset,
						'head_charset'  => $charset));
    $txtHeaders = $mm->txtHeaders();
    $headers = $mm->headers();//array("Subject" => $subj)
    //_dump($txtHeaders);
	mail($to, $headers['Subject'], $body, $txtHeaders);
}

class my_mime_mail
{
  var $from;
  var $to;
  var $subject;
  var $headers;
  var $body;
  var $charset;
  var $att	=array();

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  function __construct()
  {
    global $boundary;
    $this->from =  "";
    $this->to =  "";
    $this->subject =  "";
    $this->headers =  "";
    $this->body =  "";
	$this->charset =  "";
    $boundary = md5(uniqid(time()));
  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  function encodeMailHeader($hdr_value, $charset) {
  //	return '=?' . $charset . '?B?' . base64_encode($hdr_value) . '?= <info@azur.com.ua>';
	preg_match_all('/(\w*[\x80-\xFF -]+\w*)/', $hdr_value, $matches);

	foreach ($matches[1] as $value) {
		$hdr_value = str_replace($value, '=?' . $charset . '?B?' . base64_encode($value) . '?=', $hdr_value);
	}

	return $hdr_value;
}
//'=?koi8-r?B?'.base64_encode(convert_cyr_string($subject, "w","k")).'?='; 
  function setBody($message)
  {
		$this->body=$message;
  }
  function setCharset($charset)
  {
	  $this->charset=$charset;
  }
  function setSubject($subject)
  {
//	  $this->subject=$this->encodeMailHeader($subject,$this->charset);
	  $this->subject=$this->encodeMailHeader(mb_convert_encoding($subject,"windows-1251","UTF-8"),"windows-1251");
  }
  function setFrom($from)
  {
	  $this->from=$this->encodeMailHeader(mb_convert_encoding($from,"windows-1251","UTF-8"),"windows-1251");
  }
  function setTo($to)
  {
	  $this->to=$to;
  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  function add_attachment($source, $filename, $ctype = "application/octet-stream")
  {
    global $boundary;
    $attach = '';
    $handle = fopen($source, "rb");
    while (!feof($handle))
    {
      $attach .= fread($handle, 1024);
    }
    fclose($handle);
    $message .= "\r\n";
    $message .= "------------".$boundary."\r\n";
    $message .= "Content-Type: ".$ctype."; name=\"".$filename."\"\r\n";
    $message .= "Content-transfer-encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n";
    $message .= "\r\n";
    $message .= chunk_split(base64_encode($attach))."\r\n";
    $this->att[] = $message;
  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  function send()
  {
  
  
  
  
  	
$mime_boundary = "----PinchukArtCentre----".md5(time());

# -=-=-=- MAIL HEADERS


$subject = "This text will display in the email's Subject Field";

$headers = "From: ".($this->from)."\n";
$headers .= "Reply-To: ".$this->from."\n";
$headers .= "MIME-Version: 1.0\n";
//$headers = "Content-Type: text/html; charset=windows-1251\n";
$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
//$headers .= "Content-Type: multipart/mixed; boundary=\"$mime_boundary\"\n";
# -=-=-=- TEXT EMAIL PART

//$message = "--$mime_boundary\n";
//$message = "Content-Type: text/plain; charset=windows-1251\n";
//$message .= "Content-Transfer-Encoding: 7bit\n\n";

//$message .= "$name:\n\n";
//$message .= "This email is to confirm that \"Our Company\" has received your order.\n";
//$message .= "Please send payment of $invoicetotal to our company address.\n\n";
//$message .= "Thank You.\n\n";

# -=-=-=- HTML EMAIL PART
 
$message = "--$mime_boundary\n";
$message .= "Content-Type: text/html; charset=windows-1251\n";
$message .= "Content-Transfer-Encoding: 8bit\n\n";
$message .=mb_convert_encoding($this->body,"windows-1251","UTF-8");

# -=-=-=- FINAL BOUNDARY

$message .= "\n\n--$mime_boundary--\n\n";

# -=-=-=- SEND MAIL
//	send_mail_1983($this->to, $this->body, $this->subject,$this->from,'');
	$mail_sent = @mail( $this->to, $this->subject,$message,$headers);
  
  }
}

?>