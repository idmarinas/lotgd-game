<?
/* Function send_mail
* $to is an array of type "emailaddress"=>"Name of the Emailholder"
* $from is an array of type "emailaddress"=>"Name of the Emailholder"
* $cc is an array of type "emailaddress"=>"Name of the Emailholder"
* $contenttype is the MIME type
*/
function send_email($to, $body, $subject, $from, $cc=false,$contenttype="text/plain") {
/**
* Simple example script using PHPMailer with exceptions enabled
* @package phpmailer
* @version $Id$
*/

require_once('lib/phpmailer/class.phpmailer.php');

try {
	$mail = new PHPMailer(true); //New instance, with exceptions enabled

	$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

	$mail->IsSendmail();  // tell the class to use Sendmail
	
	//only one
	foreach ($from as $add=>$name) {
		$mail->AddReplyTo($add,$name);

		$mail->From       = $add;
		$mail->FromName   = $name;
	}

	if ($cc!==false) {
		foreach ($cc as $add=>$name) {
			$mail->AddCC($add,$name);
		}
	}


	foreach ($to as $add=>$name) {
		$mail->AddAddress($add,$name);
	}

	$mail->Subject  = $subject;

	$mail->WordWrap   = 80; // set word wrap
	$mail->CharSet = 'utf-8';
	$mail->SetLanguage ("en");


	if ($contenttype != "text/plain") {
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->MsgHTML($body);
		$mail->IsHTML(true); // send as HTML
	} else {
		$mail->Body = $body;
	}
	$mail->Send();
	return true;
	#echo 'Message has been sent.';
} catch (phpmailerException $e) {
	output("An error has been encountered, please report this: %s", $e->errorMessage());
}
}
?>
