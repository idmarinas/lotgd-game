<?php

use Lotgd\Core\Kernel;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Tracy\Debugger;

/**
 * Function for send Mails to users
 * Has the same structure as the php "mail()" function, but this function checks if you want to send emails in html
 * format or not.
 *
 * @param mixed $to
 * @param mixed $subject
 * @param mixed $message
 * @param mixed $additional_headers
 * @param mixed $additional_parameters
 *
 * @deprecated 5.3.0 Removed in future versions.
 */
function lotgd_mail ($to, $subject, $message, $additional_headers = '', $additional_parameters = '')
{
	trigger_error(
	  sprintf(
		'Usage of %s is obsolete since 5.3.0; and delete in future version. Use Symfony mailer for send emails.',
		__METHOD__
	  ),
	  E_USER_DEPRECATED
	);

	$message = str_replace(["\r\n", "\r"], "\n", $message);
	$message = str_replace("\n", '`n', $message);
	$message = LotgdSanitize::fullSanitize(str_replace('`n', '<br>', $message));

	$mailer = LotgdKernel::get('lotgd.core.mailer');

	$emailFrom = LotgdSetting::getSetting('gameadminemail', 'postmaster@localhost.com');
	$nameFrom = LotgdSetting::getSetting('servername', 'The Legend of the Green Dragon');
	$from = new Address($emailFrom, $nameFrom);

	$email = (new Email())
	  ->from($from)
	  ->to($to)
	  ->subject($subject)
	;

	//-- Send mail in HTML format
	if (LotgdSetting::getSetting('sendhtmlmail', 0)) {
		$data = [
		  'title'     => $subject,
		  'content'   => $message,
		  'copyright' => Kernel::COPYRIGHT,
		  'url'       => LotgdSetting::getSetting('serverurl', '//' . $_SERVER['SERVER_NAME']),
		];

		try {
			$message = LotgdTheme::render('mail.twig', $data);

			$email->html($message);
		} catch (Throwable $ex) {
			Debugger::log($ex);
			$email->text(str_replace('<br>', "\r\n", $message));
		}

		unset($data);
	} else {
		$email->text(str_replace('<br>', "\r\n", $message));
	}

	try {
		$mailer->send($email);
	} catch (Throwable $ex) {
		Debugger::log($ex);
	}
}
