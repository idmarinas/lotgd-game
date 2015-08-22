<?php
// rename_function('mail', 'html_mail');
function html_mail($to, $subject, $message, $additional_headers='', $additional_parameters='') {
	require_once("lib/nltoappon.php");
	
	$f = file_get_contents('lib/email.htm');
	$headers = $additional_headers?array(''):array();
	
	$message = full_sanitize(str_replace("`n", "<br />", nltoappon($message)));
	$message = str_replace(array("{subject}", "{message}"), array($subject, $message), $f);
	
	if (!strstr($additional_headers, "MIME-Version"))
		$headers[] = "MIME-Version: 1.0";
	if (!strstr($additional_headers, "Content-type"))
		$headers[] = "Content-type: text/html; charset=iso-8859-1";
	if (!strstr($additional_headers, "From"))
		$headers[] = "From: Your Website <noreply@yourwebsite.com>";
	
	mail($to, $subject, $message, $additional_headers.implode("\r\n", $headers), $additional_parameters);
}
?>