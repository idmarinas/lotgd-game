<?php

// translator ready
// addnews ready
// mail ready
require_once 'lib/is_email.php';

function systemmail($to, $subject, $body, $from = 0, $noemail = false)
{
    global $session;

    $repository = \Doctrine::getRepository('LotgdCore:Mail');
    $acctRepository = \Doctrine::getRepository('LotgdCore:Accounts');

    $accountEntityTo = $acctRepository->find($to);
    $prefs = $accountEntityTo->getPrefs();

    $entity = $repository->hydrateEntity([
        'msgfrom' => (int) $from,
        'msgto' => (int) $to,
        'subject' => is_array($subject) ? serialize($subject) : str_replace(["\n", '`n'], '', $subject),
        'body' => is_array($body) ? serialize($body) : $body,
        'sent' => new \DateTime('now'),
        'originator' => $session['user']['acctid']
    ]);

    \Doctrine::persist($entity);
    \Doctrine::flush();

    $email = false;

    if (isset($prefs['emailonmail']) && $prefs['emailonmail'] && $from > 0)
    {
        $email = true;
    }
    elseif (isset($prefs['emailonmail']) && $prefs['emailonmail'] && 0 == $from && isset($prefs['systemmail']) && $prefs['systemmail'])
    {
        $email = true;
    }

    if (! is_email($accountEntityTo->getEmailaddress()))
    {
        $email = false;
    }

    if ($email && ! $noemail)
    {
        $fromName = $acctRepository->getCharacterNameFromAcctId($from);
        $toName = $acctRepository->getCharacterNameFromAcctId($to);

        $fromline = \LotgdSanitize::fullSanitize($fromName) ?: getsetting('servername');
        $toline = \LotgdSanitize::fullSanitize($toName);

        // We've inserted it into the database, so.. strip out any formatting
        // codes from the actual email we send out... they make things
        // unreadable
        $body = is_array($body) ? \LotgdTranslator::t($body[0], $body[1], $body[2], $prefs['language'] ?? null) : $body;
        $body = preg_replace('`n', "\n", $body);
        $body = \LotgdSanitize::fullSanitizeize($body);
        $subject = is_array($subject) ? \LotgdTranslator::t($subject[0], $subject[1], $subject[2], $prefs['language'] ?? null) : $subject;
        $subject = \LotgdSanitize::fullSanitize($subject);

        $mailSubject = \LotgdTranslator::t('notificationmail.subject', [
            'subject' => $subject
        ], 'app-mail');

        $mailMessage = \LotgdTranslator::t('notificationmail.body', [
            'subject' => $subject,
            'sendername' => $fromline,
            'receivername' => $toline,
            'body' => $body,
            'gameurl' => \LotgdHttp::getServer('REQUEST_SCHEME').'://'.\LotgdHttp::getServer('SERVER_NAME'),
        ], 'app-mail');


        lotgd_mail($accountEntityTo->getEmailaddress(), \LotgdSanitize::fullSanitize($mailSubject), appoencode($mailMessage, true));
    }

    LotgdCache::removeItem("mail-{$to}");
}
