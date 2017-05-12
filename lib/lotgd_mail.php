<?php
/**
 * Function for send Mails to users
 * Has the same structure as the php "mail()" function, but this function checks if you want to send emails in html format or not.
 */
function lotgd_mail($to, $subject, $message, $additional_headers = '', $additional_parameters = '')
{
    global $lotgd_tpl, $copyright;

	require_once 'lib/nltoappon.php';
    require_once 'lib/settings_extended.php';

    $message = full_sanitize(str_replace('`n', '<br>', nltoappon($message)));
    $headers = [];

    //-- Add a "From" header if not added
    if (! strstr($additional_headers, "From")) $headers[] = "From: ".getsetting('servername', 'The Legend of the Green Dragon')." <".getsetting('gameadminemail','postmaster@localhost.com').">";

    //-- Send mail in HTML format
    if ($settings_extended->getsetting('sendhtmlmail', 0))
    {
        if (! strstr($additional_headers, "MIME-Version")) $headers[] = "MIME-Version: 1.0";
	    if (! strstr($additional_headers, "Content-type")) $headers[] = "Content-type: text/html; charset=UTF-8";

        $data = [
            'title' => $subject,
            'content' => $message,
            'copyright' => $copyright,
            'url' => getsetting('serverurl', '//'.$_SERVER['SERVER_NAME'])
        ];

        $message = $lotgd_tpl->renderThemeTemplate('mail.twig', $data);
        unset($data);
    }

    return mail($to, $subject, $message, $additional_headers.implode("\r\n", $headers), $additional_parameters);
}
