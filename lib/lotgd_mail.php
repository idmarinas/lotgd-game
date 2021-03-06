<?php
/**
 * Function for send Mails to users
 * Has the same structure as the php "mail()" function, but this function checks if you want to send emails in html format or not.
 *
 * @param mixed $to
 * @param mixed $subject
 * @param mixed $message
 * @param mixed $additional_headers
 * @param mixed $additional_parameters
 *
 * @deprecated 5.3.0 Removed in future versions.
 */
function lotgd_mail($to, $subject, $message, $additional_headers = '', $additional_parameters = '')
{
    $message = \LotgdSanitize::fullSanitize(\str_replace('`n', '<br>', nltoappon($message)));
    $headers = [];

    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 5.3.0; and delete in future version. Use Symfony mailer for send emails.',
        __METHOD__
    ), E_USER_DEPRECATED);

    //-- Add a "From" header if not added
    if ( ! \strstr($additional_headers, 'From'))
    {
        $headers[] = 'From: '.LotgdSetting::getSetting('servername', 'The Legend of the Green Dragon').' <'.LotgdSetting::getSetting('gameadminemail', 'postmaster@localhost.com').'>';
    }

    //-- Send mail in HTML format
    if (LotgdSetting::getSetting('sendhtmlmail', 0))
    {
        if ( ! \strstr($additional_headers, 'MIME-Version'))
        {
            $headers[] = 'MIME-Version: 1.0';
        }

        if ( ! \strstr($additional_headers, 'Content-type'))
        {
            $headers[] = 'Content-type: text/html; charset=UTF-8';
        }

        $data = [
            'title'     => $subject,
            'content'   => $message,
            'copyright' => \Lotgd\Core\Kernel::COPYRIGHT,
            'url'       => LotgdSetting::getSetting('serverurl', '//'.$_SERVER['SERVER_NAME']),
        ];

        try
        {
            $message = \LotgdTheme::render('mail.twig', $data);
        }
        catch(\Throwable $ex)
        {
            \Tracy\Debugger::log($ex);
            $message = \str_replace('<br>', "\r\n", $message);
            $headers = [];

            //-- Add a "From" header if not added
            if ( ! \strstr($additional_headers, 'From'))
            {
                $headers[] = 'From: '.LotgdSetting::getSetting('servername', 'The Legend of the Green Dragon').' <'.LotgdSetting::getSetting('gameadminemail', 'postmaster@localhost.com').'>';
            }
        }

        unset($data);
    }
    else
    {
        $message = \str_replace('<br>', "\r\n", $message);
    }

    return \mail($to, $subject, $message, $additional_headers.\implode("\r\n", $headers), $additional_parameters);
}
